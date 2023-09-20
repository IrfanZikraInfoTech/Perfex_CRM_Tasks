<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_111 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        add_option('appointly_google_client_secret', '');
        add_option('appointly_also_delete_in_google_calendar', 1);
        add_option('appointments_show_past_times', 1);
        add_option('appointments_disable_weekends', 1);
        add_option('appointly_client_meeting_approved_default', 0);

        add_option(
            'appointly_available_hours',
            '["08:00","08:30","09:00","09:30","10:00","10:30","11:00","11:30","12:00","12:30","13:00","13:30","14:00","14:30","15:00","15:30","16:00","16:30","17:00"]'
        );

        $CI->db->query("ALTER TABLE " . db_prefix() . "appointly_appointments ADD `google_event_id` VARCHAR(191) NULL DEFAULT NULL AFTER `id`;");
        $CI->db->query("ALTER TABLE " . db_prefix() . "appointly_appointments ADD `google_calendar_link` VARCHAR(191) NULL DEFAULT NULL AFTER `google_event_id`;");
        $CI->db->query("ALTER TABLE " . db_prefix() . "appointly_appointments ADD `google_added_by_id` int(11) NULL DEFAULT NULL AFTER `google_calendar_link`;");

        $CI->db->query(
            "CREATE TABLE IF NOT EXISTS " . db_prefix() . "appointly_google (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `staff_id` int(11) NOT NULL,
                `access_token` varchar(191) NOT NULL,
                `refresh_token` varchar(191) NOT NULL,
                `expires_in` varchar(191) NOT NULL,
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        );
    }
}
