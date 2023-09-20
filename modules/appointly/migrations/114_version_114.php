<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_114 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        add_option(
            'appointly_default_feedbacks',
            '["0","1","2","3","4","5","6"]'
        );

        if (!$CI->db->field_exists('feedback', db_prefix() . "appointly_appointments")) {
            $CI->db->query("ALTER TABLE " . db_prefix() . "appointly_appointments 
            ADD `feedback` SMALLINT NULL DEFAULT NULL, 
            ADD `feedback_comment` TEXT NULL DEFAULT NULL;
            ");
        }

        create_email_template('Feedback request for Appointment', '<span 12pt=""><span 12pt="">Hello {appointment_client_name} <br /><br />A new feedback request has just been submitted, please leave your comments and thoughts about this past appointment, fast navigate to the appointment to add a feedback:</strong> <a href="{appointment_public_url}">{appointment_public_url}</a></span><br /><br /><br />{companyname}<br />{crm_url}<br /><span 12pt=""></span></span>', 'appointly', 'Request Appointment Feedback (Sent to Client)', 'appointly-appointment-request-feedback');

        create_email_template('New appointment feedback rating received', '<span 12pt=""><span 12pt="">Hello {staff_firstname} {staff_lastname} <br /><br />A new feedback rating has been received from client {appointment_client_name}. View the new feedback rating submitted at the following link:</strong> <a href="{appointment_admin_url}">{appointment_admin_url}</a></span><br /><br /><br />{companyname}<br />{crm_url}<br /><span 12pt=""></span></span>', 'appointly', 'New Feedback Received (Sent to Responsible Person)', 'appointly-appointment-feedback-received');

        create_email_template('Appointment feedback rating updated', '<span 12pt=""><span 12pt="">Hello {staff_firstname} {staff_lastname} <br /><br />An existing feedback was just updated from client {appointment_client_name}. View the new rating submitted at the following link:</strong> <a href="{appointment_admin_url}">{appointment_admin_url}</a></span><br /><br /><br />{companyname}<br />{crm_url}<br /><span 12pt=""></span></span>', 'appointly', 'Feedback Updated (Sent to Responsible Person)', 'appointly-appointment-feedback-updated');

        if (!$CI->db->field_exists('feedback', db_prefix() . "appointly_appointments")) {
            $CI->load->helper('appointly' . '/appointly_database');
            bugCheckCommentsField();
        }
    }

}
