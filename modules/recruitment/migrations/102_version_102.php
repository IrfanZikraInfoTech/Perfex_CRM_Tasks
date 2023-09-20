<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_102 extends App_module_migration
{
     public function up()
     {
          $CI = &get_instance();

          if (!$CI->db->table_exists(db_prefix() . 'rec_skill')) {
            $CI->db->query('CREATE TABLE `' . db_prefix() . "rec_skill` (
              `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
              `skill_name` text  NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
          }

          if (!$CI->db->field_exists('skill', 'rec_candidate')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'rec_candidate` 
              ADD COLUMN `skill` text
              ;');            
          }

          if (!$CI->db->field_exists('interests', 'rec_candidate')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'rec_candidate` 
              ADD COLUMN `interests` text
              ;');            
          }
         
          
     }
}
