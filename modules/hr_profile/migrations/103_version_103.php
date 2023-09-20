<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_103 extends App_module_migration
{
     public function up()
     {
          $CI = &get_instance();

          //V103 add option : additional_training, show result training
          if (!$CI->db->field_exists('additional_training' ,db_prefix() . 'hr_jp_interview_training')) { 
            $CI->db->query('ALTER TABLE `' . db_prefix() . "hr_jp_interview_training`
              ADD COLUMN `additional_training` VARCHAR(100) NULL DEFAULT '',
              ADD COLUMN `staff_id` TEXT NULL ,
              ADD COLUMN `time_to_start` DATE NULL ,
              ADD COLUMN `time_to_end` DATE NULL
              ;");
          }
         
     }
}
