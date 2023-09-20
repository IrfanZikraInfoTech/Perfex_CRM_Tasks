<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_113 extends App_module_migration
{
     public function up()
     {
          $CI = &get_instance();

          if (!$CI->db->field_exists('linkedin', 'rec_candidate')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'rec_candidate` 
              ADD COLUMN `linkedin` text null
              ;');            
          } 

          if (!$CI->db->field_exists('alternate_contact_number', 'rec_candidate')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'rec_candidate` 
              ADD COLUMN `alternate_contact_number` varchar(15) null
              ;');            
          }

          if (!$CI->db->field_exists('position', 'rec_interview')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'rec_interview` 
              ADD COLUMN `position` int(15) null
              ;');            
          }

          if (recruitment_row_options_exist('"recruitment_create_campaign_with_plan "') == 0){
            $CI->db->query('INSERT INTO `tbloptions` (`name`,`value`, `autoload`) VALUES ("recruitment_create_campaign_with_plan", "1", "1");
          ');
          }

          if (!$CI->db->field_exists('rec_channel_form_id', 'rec_campaign')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'rec_campaign` 
              ADD COLUMN `rec_channel_form_id` int(15) null
              ;');            
          }

          //update recruitment portal
          if (!$CI->db->table_exists(db_prefix() . 'rec_company')) {
              $CI->db->query('CREATE TABLE `' . db_prefix() . "rec_company` (

                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `company_name` varchar(200) NOT NULL,
                `company_description` text NULL,
                `company_address` varchar(200) NULL,
                `company_industry` text NULL,

                PRIMARY KEY (`id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
          }

          if (!$CI->db->field_exists('display_salary', 'rec_campaign')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'rec_campaign` 
              ADD COLUMN `display_salary` int(15) null
              ;');            
          }

          if (!$CI->db->table_exists(db_prefix() . 'job_industry')) {
              $CI->db->query('CREATE TABLE `' . db_prefix() . "job_industry` (

                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `industry_name` varchar(200) NOT NULL,
                `industry_description` text NULL,

                PRIMARY KEY (`id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
          }

          if (!$CI->db->field_exists('industry_id', 'rec_job_position')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'rec_job_position` 
              ADD COLUMN `industry_id` int(15) null
              ;');            
          }

          if (!$CI->db->field_exists('company_id', 'rec_job_position')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'rec_job_position` 
              ADD COLUMN `company_id` int(15) null
              ;');            
          }
          
          if (!$CI->db->field_exists('job_skill', 'rec_job_position')) {
              $CI->db->query('ALTER TABLE `'.db_prefix() . 'rec_job_position` 
              ADD COLUMN `job_skill` text
              ;');            
          }
            

          
     }
}
