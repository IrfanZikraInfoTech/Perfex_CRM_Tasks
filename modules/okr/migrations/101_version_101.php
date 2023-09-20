<?php



defined('BASEPATH') or exit('No direct script access allowed');



class Migration_Version_101 extends App_module_migration

{

     public function up()

     {

        $CI = &get_instance();

        

        

        //Version 1.0.1


        if (!$CI->db->field_exists('type' ,db_prefix() . 'okrs')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs`
              ADD COLUMN `type` int(11) not null default 1
          ');
        }
        if (!$CI->db->table_exists(db_prefix() . 'okr_setting_category')) {
            $CI->db->query('CREATE TABLE `' . db_prefix() . "okr_setting_category` (
              `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `category` text NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
        }

        if (!$CI->db->field_exists('type' ,db_prefix() . 'okrs')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs`
              ADD COLUMN `type` int(11) not null default 1
          ');
        }

        if (!$CI->db->field_exists('category' ,db_prefix() . 'okrs')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs`
              ADD COLUMN `category` int(11) not null
          ');
        }

        if (!$CI->db->field_exists('type' ,db_prefix() . 'okrs_log')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_log`
              ADD COLUMN `type` int(11) not null default 1,
              ADD COLUMN `category` int(11) not null
          ');
        }

        if (!$CI->db->field_exists('department' ,db_prefix() . 'okrs')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs`
              ADD COLUMN `department` int(11) not null
          ');
        }

        if (!$CI->db->field_exists('department' ,db_prefix() . 'okrs_log')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_log`
              ADD COLUMN `department` int(11) not null
          ');
        }

     }

}

