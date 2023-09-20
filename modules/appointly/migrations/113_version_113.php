<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_113 extends App_module_migration
{
     public function up()
     {
          $CI = &get_instance();

          add_option('appointly_busy_times_enabled', '1');
          add_option('callbacks_responsible_person', '');
          add_option('callbacks_mode_enabled', '1');
          add_option('appointly_appointments_recaptcha', '0');

          $CI->db->query(
               "CREATE TABLE IF NOT EXISTS " . db_prefix() . "appointly_callbacks (
               `id` int(11) NOT NULL AUTO_INCREMENT,
               `call_type` varchar(191) NOT NULL,
               `phone_number` varchar(191) NOT NULL,
               `timezone` varchar(191) NOT NULL,
               `firstname` varchar(191) NOT NULL,
               `lastname` varchar(191) NOT NULL,
               `status` varchar(191) NOT NULL DEFAULT '1',
               `message` text NOT NULL,
               `email`  varchar(191) NOT NULL ,
               `date_start` datetime NOT NULL,
               `date_end` datetime NOT NULL,
               `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
               PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
          );

          $CI->db->query(
               "CREATE TABLE IF NOT EXISTS " . db_prefix() . "appointly_callbacks_assignees (
               `id` int(11) NOT NULL AUTO_INCREMENT,
               `callbackid` int(11) NOT NULL,
               `user_id` int(11) NOT NULL,
               PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
          );

          create_email_template('New appointment request via external form', '<span 12pt=""><span 12pt="">Hello {staff_firstname} {staff_lastname}<br /><br />New appointment request submitted via external form</span>.<br /><br /><span 12pt=""><strong>Appointment Subject:</strong> {appointment_subject}</span><br /><br /><span 12pt=""><strong>Appointment Description:</strong> {appointment_description}</span><br /><br /><span 12pt=""><strong>Appointment requested scheduled start date:</strong> {appointment_date}</span><br /><br /><span 12pt=""><strong>You can view this appointment request at the following link:</strong> <a href="{appointment_admin_url}">{appointment_admin_url}</a></span><br /><br /><br />{companyname}<br />{crm_url}<br /><span 12pt=""></span></span>', 'appointly', 'New appointment request (Sent to Responsible Person)', 'appointment-submitted-to-staff');

          create_email_template('You have been assigned to handle a new callback', '<span 12pt=""><span 12pt="">Hello {staff_firstname} {staff_lastname}<br /><br />An admin assigned a callback to you, you can view this callback request at the following link:</strong> <a href="{admin_url}/appointly/callbacks">{admin_url}/appointly/callbacks</a></span><br /><br /><br />{companyname}<br />{crm_url}<br /><span 12pt=""></span></span>', 'appointly', 'Assigned to callback (Sent to Staff)', 'callback-assigned-to-staff');

          create_email_template('You have a new callback request', '<span 12pt=""><span 12pt="">Hello {staff_firstname} {staff_lastname}<br /><br />A new callback request has just been submitted, fast navigate to callbacks to view latest callback submitted:</strong> <a href="{admin_url}/appointly/callbacks">{admin_url}/appointly/callbacks</a></span><br /><br /><br />{companyname}<br />{crm_url}<br /><span 12pt=""></span></span>', 'appointly', 'New callback request (Sent to Callbacks Responsible Person)', 'newcallback-requested-to-staff');
     }
}
