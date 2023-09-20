<?php defined('BASEPATH') or exit('No direct script access allowed');

class Callbacks extends AdminController
{
     private $staff_has_no_view_permissions;
     private $staff_has_no_edit_create_permissions;
     private $staff_has_no_delete_permissions;

     public function __construct()
     {
          parent::__construct();

          $this->staff_has_no_view_permissions = !staff_can('view', 'appointments') && !staff_can('view_own', 'appointments');
          $this->staff_has_no_edit_create_permissions = !staff_can('edit', 'appointments') && !staff_can('create', 'appointments');
          $this->staff_has_no_delete_permissions = !staff_can('delete', 'appointments') && !staff_can('edit', 'appointments');

          /**
           * Init callbacks model
           */
          $this->load->model('callbacks_model', 'callbackm');
     }

     /**
      * Callbacks main view
      *
      * @return void
      */
     public function index()
     {
          if (!is_staff_callbacks_responsible()) {
               if ($this->staff_has_no_view_permissions) {
                    ajax_access_denied('Callbacks');
               }
          }

          $this->load->view('callbacks/index');
     }

     /**
      * Render callbacks table view
      *
      * @return void
      */
     public function table()
     {
          if (!is_staff_callbacks_responsible()) {
               if ($this->staff_has_no_view_permissions) {
                    ajax_access_denied('Callbacks');
               }
          }

          $this->app->get_table_data(module_views_path(APPOINTLY_MODULE_NAME, 'callbacks/callbacks'));
     }


     /**
      * Render callback view
      *
      * @return void
      */
     public function callbackView()
     {
          if (!is_staff_callbacks_responsible()) {
               if ($this->staff_has_no_view_permissions) {
                    ajax_access_denied('Callbacks');
               }
          }

          $callbackid = $this->input->post('id');

          $data = [
               'staff' => $this->staff_model->get('', ['active' => 1]),
               'callback' => $this->callbackm->get($callbackid),
               'callback_notes' => $this->misc_model->get_notes($callbackid, 'callback'),
               'callback_assignees' => $this->callbackm->get_callback_assignees($callbackid),
               'assignees_ids' => $this->callbackm->get_callback_assignees_ids($callbackid)
          ];

          $this->load->view('modals/callback_view', $data);
     }


     /* Add callback assignees / ajax */
     public function add_callback_assignees()
     {
          if (!is_staff_callbacks_responsible()) {
               if ($this->staff_has_no_edit_create_permissions) {
                    ajax_access_denied('Callbacks');
               }
          }

          echo json_encode([
               'success'  => $this->callbackm->add_callback_assignees($this->input->post()),
               'callbackHtml' => $this->_get_callback_assignees_html($this->input->post('callbackid'))
          ]);
     }


     /**  
      * Remove callback assignee / ajax
      * @param $id
      * @param $callbackid
      */

     public function remove_callback_assignee($id, $callbackid)
     {
          if (!is_staff_callbacks_responsible()) {
               if ($this->staff_has_no_edit_create_permissions) {
                    ajax_access_denied('Callbacks');
               }
          }

          $success = $this->callbackm->remove_assignee($id, $callbackid);
          $message = '';

          if ($success) {
               $message = _l('callbacks_assignee_removed');
          }
          echo json_encode([
               'success'  => $success,
               'message'  => $message,
               'callbackHtml' => $this->_get_callback_assignees_html($callbackid)
          ]);
     }
     /**  
      * Mark callback as [mixed]
      * @return json
      */
     public function callback_mark_as()
     {
          if (!is_staff_callbacks_responsible()) {
               if ($this->staff_has_no_edit_create_permissions) {
                    ajax_access_denied('Callbacks');
               }
          }

          return $this->callbackm->update_callback_status($this->input->post());
     }

     /**  
      * Add new callback note
      * @param string $rel_id
      * @return json
      */
     public function add_note($rel_id)
     {
          if (!is_staff_callbacks_responsible()) {
               if ($this->staff_has_no_view_permissions) {
                    ajax_access_denied('Callbacks');
               }
          }

          $callbackid = '';

          if ($this->input->post()) {
               $data = $this->input->post();

               $callbackid = $data['callbackid'];
               unset($data['callbackid']);

               if ($data['callback_contacted_indicator'] == 'yes') {
                    $contacted_date         = to_sql_date($data['custom_contact_date'], true);
                    $data['date_contacted'] = $contacted_date;
               }

               unset($data['callback_contacted_indicator']);
               unset($data['custom_contact_date']);

               // Causing issues with duplicate ID or if my prefixed file for lead.php is used
               $data['description'] = isset($data['callback_notes_description']) ? $data['callback_notes_description'] : $data['description'];

               if (isset($data['callback_notes_description'])) {
                    unset($data['callback_notes_description']);
               }

               $note_id = $this->misc_model->add_note($data, 'callback', $rel_id);

               if ($note_id) {

                    $assignee_ids = $this->callbackm->get_callback_assignees_ids($callbackid);
                    $callback_details = $this->callbackm->get_callback_details($callbackid);

                    foreach ($assignee_ids as $key => $assigneeid) {

                         $assignee_ids[$key] = trim($assigneeid);

                         if ($assigneeid == get_staff_user_id()) {
                              continue;
                         }

                         add_notification([
                              'description'     => get_staff_full_name(get_staff_user_id()) . ' ' . _l('callbacks_new_note') . ' ' . $callback_details['firstname'] . ' ' . $callback_details['lastname'],
                              'touserid'        =>  $assigneeid,
                              'fromcompany'     => true,
                              'link'            => 'appointly/callbacks',
                         ]);
                    }

                    pusher_trigger_notification(array_unique($assignee_ids));

                    echo json_encode(['callbackView' => $this->_get_callback_notes_data($rel_id), 'id' => $rel_id]);
               }
          }
     }


     /**
      * Get new callback data for ntoes modal
      *
      * @param string $callbackid
      * @return void
      */
     private function _get_callback_notes_data($callbackid = '')
     {
          if (!$this->callbackm->get($callbackid)) {
               header('HTTP/1.0 404 Not Found');
               echo 'Callback was not found';
               die;
          }

          $data = [
               'callback' => $this->callbackm->get($callbackid),
               'staff' => $this->staff_model->get('', ['active' => 1]),
               'callback_assignees' => $this->callbackm->get_callback_assignees($callbackid),
               'assignees_ids' => $this->callbackm->get_callback_assignees_ids($callbackid),
               'callback_notes' => $this->misc_model->get_notes($callbackid, 'callback')
          ];

          return $this->load->view('modals/callback_body_partial', $data, true);
     }

     /**  
      * Delete callback note
      * @param string $id
      * @return json
      */
     public function delete_note($id)
     {
          if ($this->staff_has_no_delete_permissions) {
               ajax_access_denied('Callbacks');
          }

          echo json_encode([
               'success' => $this->misc_model->delete_note($id),
          ]);
     }

     /**  
      * Delete callback 
      * @param string $callbackid
      * @return json
      */
     public function delete($callbackid)
     {
          if ($this->staff_has_no_delete_permissions) {
               ajax_access_denied('Callbacks');
          }

          echo json_encode([
               'success' => $this->callbackm->deleteCallback($callbackid),
               'message' => _l('callbacks_deleted_success')
          ]);
     }

     /**
      * Render callback assignees html
      *
      * @param string $callbackid
      * @return void
      */
     private function _get_callback_assignees_html($callbackid)
     {
          $data = [
               'staff' => $this->staff_model->get('', ['active' => 1]),
               'callback_assignees' => $this->callbackm->get_callback_assignees($callbackid),
               'assignees_ids' =>  $this->callbackm->get_callback_assignees_ids($callbackid),
               'cid' =>  $callbackid
          ];

          return $this->load->view('modals/callbacks_assignees_wrapper', $data, true);
     }
}
