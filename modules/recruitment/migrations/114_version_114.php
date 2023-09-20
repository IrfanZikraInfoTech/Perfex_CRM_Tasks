<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_114 extends App_module_migration
{
     public function up()
     {
          $CI = &get_instance();


          if (!$CI->db->field_exists('company_id', 'rec_campaign')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'rec_campaign` 
              ADD COLUMN `company_id` int(15) null
              ;');            
          }
          
          
     }
}
