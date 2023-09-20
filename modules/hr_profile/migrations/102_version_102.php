<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_102 extends App_module_migration
{
     public function up()
     {
          $CI = &get_instance();

          //Update v102: add Type of training menu
          if (!$CI->db->table_exists(db_prefix() . 'hr_type_of_trainings')) {
            $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_type_of_trainings` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` TEXT NULL,

              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
          }

          //Insert default data: Basic training for old customer
          if (hr_profile_type_of_training_exists('"Basic training"') == 0){
            $CI->db->query('INSERT INTO `'.db_prefix().'hr_type_of_trainings` (`name`) VALUES ("Basic training");
              ');
          }
          
     }
}
