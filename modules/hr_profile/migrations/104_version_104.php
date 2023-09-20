<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_104 extends App_module_migration
{
     public function up()
     {
          $CI = &get_instance();

          //V104: import, export staff, contract pdf
          if (!$CI->db->field_exists('hash' ,db_prefix() . 'hr_staff_contract')) { 
            $CI->db->query('ALTER TABLE `' . db_prefix() . "hr_staff_contract`
              ADD COLUMN `content` LONGTEXT NULL,
              ADD COLUMN `hash` VARCHAR(32) NULL,
              ADD COLUMN `signature` VARCHAR(40) NULL,
              ADD COLUMN `signer` INT(11) NULL

              ;");
          }

          if (!$CI->db->table_exists(db_prefix() . 'hr_contract_template')) {
            $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_contract_template` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` TEXT NULL,
              `job_position` LONGTEXT NULL,
              `content` LONGTEXT NULL,

              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
          }

          if (!$CI->db->field_exists('staff_signature' ,db_prefix() . 'hr_staff_contract')) { 
            $CI->db->query('ALTER TABLE `' . db_prefix() . "hr_staff_contract`
              ADD COLUMN `staff_signature` VARCHAR(40) NULL,
              ADD COLUMN `staff_sign_day` DATE NULL

              ;");
          }
         
     }
}
