<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_119 extends App_module_migration
{
    public function up()
    {
        create_email_template('Recurring appointment was re-created!', '<span style=\"font-size: 12pt;\"> Hello {staff_firstname} {staff_lastname} </span><br /><br /><span style=\"font-size: 12pt;\"> Your recurring appointment was recreated with date {appointment_date} and location {appointment_location}</span><br /><br /><span style=\"font-size: 12pt;\"><strong> Additional info for your appointment:</strong></span><br /><span style=\"font-size: 12pt;\"><strong>Appointment Subject:</strong> {appointment_subject}</span><br /><span style=\"font-size: 12pt;\"><strong>Appointment Description:</strong> {appointment_description}</span><br /><span style=\"font-size: 12pt;\"><strong>Appointment scheduled date to start:</strong> {appointment_date}</span><br /><span style=\"font-size: 12pt;\"><strong>You can view this appointment at the following link:</strong> <a href="{appointment_admin_url}">Your appointment URL</a></span><br /><span style=\"font-size: 12pt;\"><br />Kind Regards</span><br /><br /><span style=\"font-size: 12pt;\">{email_signature}</span>', 'appointly', 'Appointment recurring (Sent to Staff and Attendees)', 'appointment-recurring-to-staff');

        create_email_template('Recurring appointment was re-created!', '<span style=\"font-size: 12pt;\"> Hello {appointment_client_name}. </span><br /><br /><span style=\"font-size: 12pt;\"> Your recurring appointment was recreated with date {appointment_date} and location {appointment_location}.</span><br /><br /><span style=\"font-size: 12pt;\"><strong>Additional info for your appointment</strong></span><br /><span style=\"font-size: 12pt;\"><strong>Appointment Subject:</strong> {appointment_subject}</span><br /><span style=\"font-size: 12pt;\"><strong>Appointment Description:</strong> {appointment_description}</span><br /><span style=\"font-size: 12pt;\"><strong>Appointment scheduled date to start:</strong> {appointment_date}</span><br /><span style=\"font-size: 12pt;\"><strong>You can view this appointment at the following link:</strong> <a href="{appointment_public_url}">Your appointment URL</a></span><br /><span style=\"font-size: 12pt;\"><br />Kind Regards</span><br /><br /><span style=\"font-size: 12pt;\">{email_signature}</span>', 'appointly', 'Appointment recurring (Sent to Contact)', 'appointment-recurring-to-contacts');

        $CI = &get_instance();

        $table = db_prefix() . "appointly_appointments";

        if ($CI->db->table_exists($table)) {
            $CI->db->query(
                "ALTER TABLE {$table} 
                 ADD `recurring` INT NOT NULL DEFAULT '0',
                 ADD `recurring_type` VARCHAR(10) NULL DEFAULT NULL,
                 ADD `repeat_every` INT NULL DEFAULT NULL,
                 ADD `custom_recurring` TINYINT NOT NULL, 
                 ADD `cycles` INT NOT NULL DEFAULT '0', 
                 ADD `total_cycles` INT NOT NULL DEFAULT '0',
                 ADD `last_recurring_date` DATE NULL DEFAULT NULL;
          ");
        }
    }

}
