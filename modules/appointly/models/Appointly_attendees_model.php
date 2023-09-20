<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class helper model responsible for appointment attendeees
 */
class Appointly_attendees_model extends App_Model
{
    /**
     * Create new appointment
     *
     * @param string $appointment_id
     * @param array $attendees
     * @return void
     */
    public function create($appointment_id, $attendees)
    {
        foreach ($attendees as $attendee) {
            $this->db->insert('appointly_attendees', [
                'staff_id' => $attendee,
                'appointment_id' => $appointment_id,
            ]);
        }
    }

    /**
     * Get appointment
     *
     * @param string $appointment_id
     * @return array
     */
    public function get($appointment_id)
    {
        $this->db->where('appointment_id', $appointment_id);
        $this->db->join('staff', db_prefix() . 'staff.staffid=' . db_prefix() . 'appointly_attendees.staff_id');
        return $this->db->get('appointly_attendees')->result_array();
    }

    public function details($attendee_ids)
    {
        $data = [];

        $this->db->select('CONCAT(firstname, " ", lastname) as name, email');
        $this->db->where_in('staffid', $attendee_ids);

        $attendees = $this->db->get(db_prefix() . 'staff')->result();

        if ($attendees) {
            foreach ($attendees as $attendee) {
                $data[] = ['emailAddress' => [
                    'address' => $attendee->email,
                    'name' => $attendee->name
                ]];
            }
            return $data;
        }
    }

    /**
     * Get appointment active attendees
     *
     * @param string $appointment_id
     * @return array
     */
    public function attendees($appointment_id)
    {
        $this->db->select('staff_id');
        $this->db->where('appointment_id', $appointment_id);
        $appointment  = $this->db->get('appointly_attendees')->result_array();

        return $appointment['staff_id'] = array_map(function ($attendee) {
            return $attendee['staff_id'];
        }, $appointment);
    }


    /**
     * Get current contact email
     *
     * @param array $appointment
     * @return mixed
     */
    public function get_contact_email($appointment)
    {
        if ($appointment['source'] && $appointment['source'] == 'external') {

            $this->db->select('email');
            $this->db->where('id', $appointment['appointment_id']);
            $appointment  = $this->db->get('appointly_appointments')->row_array();

            if ($appointment) {
                return $appointment;
            }
        }
        return false;
    }


    /**
     * Send notifications to new attendees
     *
     * @param array $attendees
     * @param array $appointment
     * @return void
     */
    public function send_notifications_to_new_attenddees($attendees, $appointment)
    {
        foreach ($attendees as $staff) {

            if ($staff['staffid'] === get_staff_user_id()) {
                continue;
            }

            add_notification([
                'description'     => 'appointment_is_approved',
                'touserid'        => $staff['staffid'],
                'fromcompany'     => true,
                'link'            => 'appointly/appointments/view?appointment_id=' . $appointment['appointment_id'],
            ]);


            $notified_users[] = $staff['staffid'];
            send_mail_template('appointly_appointment_approved_to_staff_attendees', 'appointly', array_to_object($appointment), array_to_object($staff));


            pusher_trigger_notification(array_unique($notified_users));
        }
    }

    /** 
     * Send new notifications to new assigned contact
     * @param [array] $appointment
     * @return void
     */
    public function send_notifications_to_appointment_contact($appointment)
    {
        $template = mail_template('appointly_appointment_approved_to_contact', 'appointly', array_to_object($appointment));
        $merge_fields =  $template->get_merge_fields();

        if (!empty($appointment['phone'])) {
            $merge_fields =  $template->get_merge_fields();
            $this->app_sms->trigger(APPOINTLY_SMS_APPOINTMENT_APPROVED_TO_CLIENT, $appointment['phone'], $merge_fields);
        }

        $template->send();
    }

    /**
     * Get current assigned contact for appointment
     *
     * @param [array] $appointment
     * @return array
     */
    public function get_current_appointment_contact($appointment)
    {
        $this->db->where('id', $appointment['appointment_id']);
        $this->db->where('contact_id', $appointment['contact_id']);
        return $this->db->get(db_prefix() . 'appointly_appointments')->row_array();
    }

    /**
     * Update appointment attendees
     *
     * @param [string] $appointment_id
     * @param [array] $attendees
     * @return void
     */
    public function update($appointment_id, $attendees)
    {
        $this->deleteAll($appointment_id);

        $this->create($appointment_id, $attendees);
    }

    /**
     * Deletet all attendees
     *
     * @param [string] $appointment_id
     * @return void
     */
    public function deleteAll($appointment_id)
    {
        $this->db->where('appointment_id', $appointment_id);
        $this->db->delete('appointly_attendees');
    }
}
