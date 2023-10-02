<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_115 extends App_module_migration
{
     public function up()
     {
          add_option('appointly_outlook_client_id', "");

          $CI = &get_instance();

          if (get_instance()->db->table_exists(db_prefix() . "appointly_appointments")) {

               if (!$CI->db->field_exists('google_meet_link', db_prefix() . "appointly_appointments")) {
                    get_instance()->db->query("ALTER TABLE " . db_prefix() . "appointly_appointments 
                       ADD `google_meet_link` VARCHAR(191) NULL DEFAULT;");
               }

               if (!$CI->db->field_exists('outlook_event_id', db_prefix() . "appointly_appointments")) {
                    get_instance()->db->query("ALTER TABLE " . db_prefix() . "appointly_appointments                       
                       ADD `outlook_event_id` VARCHAR(191) NULL DEFAULT NULL, 
                       ADD `outlook_calendar_link` VARCHAR(255) NULL DEFAULT NULL,
                       ADD `outlook_added_by_id` INT(11) NULL DEFAULT NULL;");
               }

               // Feedback fields fix
               if (!$CI->db->field_exists('feedback', db_prefix() . "appointly_appointments")) {
                    $CI->db->query("ALTER TABLE " . db_prefix() . "appointly_appointments ADD `feedback` SMALLINT NULL DEFAULT NULL;");
               }
               if (!$CI->db->field_exists('feedback_comment', db_prefix() . "appointly_appointments")) {
                    $CI->db->query("ALTER TABLE " . db_prefix() . "appointly_appointments ADD `feedback_comment` TEXT NULL DEFAULT NULL;");
               }
          } else {
               $CI = &get_instance();

               if (!$CI->db->table_exists(db_prefix() . "appointly_appointments")) {
                    $CI->load->helper('appointly' . '/appointly_database');

                    init_appointly_install_sequence();
               }
          }
     }
}
