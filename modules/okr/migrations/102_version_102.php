<?php



defined('BASEPATH') or exit('No direct script access allowed');



class Migration_Version_102 extends App_module_migration

{
     public function up()
     {
        $CI = &get_instance();
        //Version 1.0.2
        if (!$CI->db->table_exists(db_prefix() . 'okr_approval_setting')) {
          $CI->db->query('CREATE TABLE `' . db_prefix() .'okr_approval_setting` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `department` VARCHAR(255) NOT NULL,
            `okrs` VARCHAR(255) NOT NULL,
            `setting` LONGTEXT NOT NULL,
            `choose_when_approving` INT NOT NULL DEFAULT 0,
            `notification_recipient` LONGTEXT  NULL,
            `number_day_approval` INT(11) NULL,
            PRIMARY KEY (`id`));');
        }

        if (!$CI->db->table_exists(db_prefix() . 'okr_approval_details')) {
          $CI->db->query('CREATE TABLE `' . db_prefix() .'okr_approval_details` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `rel_id` INT(11) NOT NULL,
            `rel_type` VARCHAR(45) NOT NULL,
            `staffid` VARCHAR(45) NULL,
            `approve` VARCHAR(45) NULL,
            `note` TEXT NULL,
            `date` DATETIME NULL,
            `approve_action` VARCHAR(255) NULL,
            `reject_action` VARCHAR(255) NULL,
            `approve_value` VARCHAR(255) NULL,
            `reject_value` VARCHAR(255) NULL,
            `staff_approve` INT(11) NULL,
            `action` VARCHAR(45) NULL,
            `sender` INT(11) NULL,
            `date_send` DATETIME NULL,
            `notification_recipient` LONGTEXT NULL,
            `approval_deadline` DATE NULL,
            PRIMARY KEY (`id`));');
        }
        
        if (!$CI->db->field_exists('approval_status' ,db_prefix() . 'okrs')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs`
              ADD COLUMN `approval_status` int(1) NOT NULL default 0 COMMENT "0:draft 1:approval 2:reject" 
          ');
        }

        if (!$CI->db->field_exists('department' ,db_prefix() . 'okrs_log')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_log`
              ADD COLUMN `approval_status` int(1) NOT NULL default 0 COMMENT "0:draft 1:approval 2:reject 3:waiting for approval"
          ');
        }

        if (!$CI->db->field_exists('tasks' ,db_prefix() . 'okrs_key_result')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_key_result`
              ADD COLUMN `tasks` varchar(100) NULL
          ');
        }
     }

}

