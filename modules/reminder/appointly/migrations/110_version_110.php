<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_110 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        if (!$CI->db->table_exists(db_prefix() . "appointly_appointments")) {
            $CI->load->helper('appointly' . '/appointly_database');

            init_appointly_install_sequence();
        }

        add_option('appointly_show_clients_schedule_button', 0);
        add_option('appointly_tab_on_clients_page', 0);

        $CI->db->query(
            "CREATE TABLE IF NOT EXISTS " . db_prefix() . "appointly_appointment_types (
               `id` int(11) NOT NULL AUTO_INCREMENT,
               `type` varchar(191) NOT NULL,
               `color` varchar(191) NOT NULL,
               PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        );

        $CI->db->query("ALTER TABLE " . db_prefix() . "appointly_appointments ADD `notes` LONGTEXT NULL;");
        $CI->db->query("ALTER TABLE " . db_prefix() . "appointly_appointments ADD `type_id` INT(11) NOT NULL DEFAULT '0';");
    }

}
