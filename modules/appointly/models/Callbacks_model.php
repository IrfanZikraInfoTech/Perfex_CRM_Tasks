<?php defined('BASEPATH') or exit('No direct script access allowed');

class Callbacks_model extends App_Model
{
    protected $_table;

    public function __construct()
    {
        parent::__construct();
        $this->_table = db_prefix() . 'appointly_callbacks';
    }


    /**
     * Get callback data
     *
     * @param string $id
     *
     * @return array
     */
    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->_table)->row_array();
    }


    /**
     * Handle new callback request
     *
     * @param $data
     *
     * @return bool
     */
    public function handle_callback_request_data($data)
    {
        if (!isset($data['call_types'])) {
            $data['call_types'] = ['phone'];
        }

        $update_data = [
            'call_type'    => json_encode($data['call_types']),
            'firstname'    => $data['client_firstname'],
            'lastname'     => $data['client_lastname'],
            'email'        => $data['client_email'],
            'status'       => 1,
            'phone_number' => $data['client_phone'],
            'timezone'     => $data['timezone'],
            'message'      => htmlentities($data['client_message']),
            'date_start'   => to_sql_date($data['date_from'], true),
            'date_end'     => to_sql_date($data['date_to'], true),
        ];

        if ($this->db->insert($this->_table, $update_data)) {

            $responsiblePerson = get_option('callbacks_responsible_person');

            if ($responsiblePerson != '') {

                $staff = appointly_get_staff($responsiblePerson);

                send_mail_template('appointly_callbacks_notification_newcallback_to_staff', 'appointly', array_to_object($staff));

                add_notification([
                    'description' => 'callbacks_new_callback_request',
                    'touserid'    => $responsiblePerson,
                    'fromcompany' => true,
                    'link'        => 'appointly/callbacks',
                ]);
            }
            /**
             * In case responsible person is not set notification will be sent to Admin with default id of 1
             * i.e person who installed CRM
             */
            if ($responsiblePerson == '') $responsiblePerson = 1;

            pusher_trigger_notification([$responsiblePerson]);

            return true;
        }
        return false;
    }

    /**
     * Update callback status
     *
     * @param array $data
     *
     * @return json|void
     */
    public function update_callback_status($data)
    {
        $this->db->where('id', $data['callback_id']);
        $this->db->update($this->_table, ['status' => $data['status']]);

        if ($this->db->affected_rows() !== 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => 'true']);
        }
    }

    /**
     * Assigning new staff member to a callback
     *
     * @param array $data
     *
     * @return boolean
     */
    public function add_callback_assignees($data)
    {
        $this->db->insert(
            db_prefix() . 'appointly_callbacks_assignees',
            [
                'callbackid' => $data['callbackid'],
                'user_id'    => $data['assignee']
            ]
        );
        if ($this->db->affected_rows() !== 0) {
            $this->_send_user_email_and_notifications($this->staff_model->get($data['assignee']));
            return true;
        }
        return false;
    }

    /**
     * Remove assignee from callback
     *
     * @param string $id
     * @param string $callbackid
     *
     * @return boolean
     */
    public function remove_assignee($id, $callbackid)
    {
        $this->db->where('(callbackid = "' . $callbackid . '" AND user_id = "' . $id . '")');
        $this->db->delete(db_prefix() . 'appointly_callbacks_assignees');
        if ($this->db->affected_rows() !== 0) {
            return true;
        }
        return false;
    }


    /**
     * Delete callback
     *
     * @param string $callbackid
     *
     * @return boolean
     */
    public function deleteCallback($callbackid)
    {
        $this->db->where('callbackid', $callbackid);
        $this->db->delete(db_prefix() . 'appointly_callbacks_assignees');

        $this->db->where('id', $callbackid);
        return $this->db->delete($this->_table);
    }

    /**
     * Get callback assigned staff user ids
     *
     * @param string $callbackid
     *
     * @return array
     */
    public function get_callback_assignees_ids($callbackid)
    {
        $sqlCallbacksSelect = '(SELECT GROUP_CONCAT(user_id SEPARATOR ",") FROM ' . db_prefix() . 'appointly_callbacks_assignees WHERE ' . db_prefix() . 'appointly_callbacks_assignees.callbackid = "' . $callbackid . '" ORDER by user_id ASC) as assignees_ids';

        $this->db->select($sqlCallbacksSelect);
        $assignees_ids = $this->db->get(db_prefix() . 'appointly_callbacks')->row_array();

        // Get all admins and send notifications also, admins should get notifications because they see all callbacks
        $admins = $this->staff_model->get('', ['active' => 1, 'admin' => 1]);

        $assignees_cleared_ids = array_map(function ($admin) {
            return $admin['staffid'];
        }, $admins);

        return array_merge(explode(',', $assignees_ids['assignees_ids']), $assignees_cleared_ids);
    }


    /**
     * Get callback assigned staff assignees
     *
     * @param string $callbackid
     *
     * @return array
     */
    public function get_callback_assignees($callbackid)
    {
        $this->db->select(db_prefix() . 'appointly_callbacks_assignees.user_id as assigneeid, firstname,lastname,CONCAT(firstname, " ", lastname) as full_name, callbackid');
        $this->db->from(db_prefix() . 'appointly_callbacks_assignees');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'appointly_callbacks_assignees.user_id');
        $this->db->where('callbackid', $callbackid);
        $this->db->order_by('firstname', 'asc');

        return $this->db->get()->result_array();
    }


    /**
     * Get details
     *
     * @param $callbackid
     *
     * @return mixed
     */
    public function get_callback_details($callbackid)
    {
        $this->db->select('firstname, lastname');
        $this->db->where('id', $callbackid);
        return $this->db->get($this->_table)->row_array();
    }


    /**
     * Send staff email and notifications
     *
     * @param $staff
     */
    private function _send_user_email_and_notifications($staff)
    {
        $users[] = $staff->staffid;

        add_notification([
            'description' => 'callbacks_assignee_notification',
            'touserid'    => $staff->staffid,
            'fromcompany' => true,
            'link'        => 'appointly/callbacks',
        ]);

        send_mail_template('appointly_callbacks_notification_assigned_to_staff', 'appointly', $staff);
        pusher_trigger_notification(array_unique($users));
    }

}
