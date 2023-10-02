<?php defined('BASEPATH') or exit('No direct script access allowed');

class Appointments_history extends AdminController
{
     private $staff_no_view_permissions;

     public function __construct()
     {
          parent::__construct();

          $this->staff_no_view_permissions  = !staff_can('view', 'appointments') && !staff_can('view_own', 'appointments');

          $this->load->model('appointly_model', 'apm');
     }

     /**
      * Main view
      *
      * @return void
      */
     public function index()
     {
          if ($this->staff_no_view_permissions) {
               access_denied('Appointments');
          }

          $this->load->view('history_view');
     }

     /**
      * Render table view
      *
      * @return void
      */
     public function table()
     {
          if ($this->staff_no_view_permissions) {
               access_denied();
          }

          $this->app->get_table_data(module_views_path(APPOINTLY_MODULE_NAME, 'history/index'));
     }

     public function get_notes($appointment_id)
     {
          $this->db->select('notes, subject');
          $this->db->where('id', $appointment_id);
          echo json_encode($this->db->get(db_prefix() . 'appointly_appointments')->row());
     }

     public function update_note()
     {
          $appointment_id = $this->input->post('appointment_id');
          $notes = $this->input->post('notes');

          if ($appointment_id) {
               $this->db->where('id', $appointment_id);
               $this->db->update(db_prefix() . 'appointly_appointments', ['notes' => $notes]);
               echo json_encode(['result' => true]);
          } else {
               echo json_encode(['result' => false]);
          }
     }
}
