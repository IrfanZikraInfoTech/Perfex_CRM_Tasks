<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'hr_profile_option')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_profile_option` (
    `option_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `option_name` varchar(200) NOT NULL,
    `option_val` longtext NULL,
    `auto` tinyint(1) NULL,
    PRIMARY KEY (`option_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if (!$CI->db->field_exists('manager_id' ,db_prefix() . 'departments')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "departments`
    ADD COLUMN `manager_id` INT(11) NULL DEFAULT 0;");
}
if (!$CI->db->field_exists('parent_id' ,db_prefix() . 'departments')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "departments`
    ADD COLUMN `parent_id` INT(11) NULL DEFAULT 0;");
}
if (!$CI->db->table_exists(db_prefix() . 'rec_transfer_records')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "rec_transfer_records` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `staffid` int(11) NOT NULL,
    `firstname` varchar(100) NULL,
    `lastname` varchar(100) NULL,
    `birthday` date NULL,
    `gender` varchar(11) NULL,
    `staff_identifi` varchar(20) NULL,
    `creator` int(11) NULL,
    `datecreator` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if (!$CI->db->table_exists(db_prefix() . 'setting_transfer_records')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "setting_transfer_records` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`varchar(150),  
    `meta` varchar(50),  
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if (!$CI->db->table_exists(db_prefix() . 'rec_set_transfer_record')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "rec_set_transfer_record` (
    `set_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `send_to` varchar(45) NOT NULL,
    `email_to` text NULL,
    `add_from` int(11) NOT NULL,
    `add_date` date NOT NULL,
    `subject` text NOT NULL,
    `content` text NULL,
    `order` int(11) NOT NULL,
    PRIMARY KEY (`set_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if (!$CI->db->table_exists(db_prefix() . 'setting_asset_allocation')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "setting_asset_allocation` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(150),     
    `meta` varchar(50),
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if (!$CI->db->table_exists(db_prefix() . 'records_meta')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "records_meta` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(150),  
    `meta` varchar(100),  
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

  $data_array = array( 
    array("staff_identifi", "staff_identifi"), 
    array("firstname", "firstname"), 
    array("email", "email"), 
    array("phonenumber", "phonenumber"), 
    array("facebook", "facebook"), 
    array("skype", "skype"), 
    array("birthday", "birthday"), 
    array("birthplace", "birthplace"), 
    array("home_town", "home_town"), 
    array("marital_status", "marital_status"), 
    array("nation", "nation"), 
    array("religion", "religion"), 
    array("identification", "identification"), 
    array("days_for_identity", "days_for_identity"), 
    array("place_of_issue", "place_of_issue"), 
    array("resident", "resident"), 
    array("current_address", "current_address"), 
    array("literacy", "literacy"), 
  ); 
  foreach ($data_array as $key => $value) {
    $data['name']=$value[0];
    $data['meta']=$value[1];
    $CI->db->insert(db_prefix() . 'records_meta', $data);
  }    
}
if (!$CI->db->table_exists(db_prefix() . 'group_checklist')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'group_checklist` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `group_name` VARCHAR(100) NOT NULL,
    `meta` VARCHAR(100) NULL,
    PRIMARY KEY (`id`));');
}
if (!$CI->db->table_exists(db_prefix() . 'setting_training')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'setting_training` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `training_type` INT(11) NOT NULL,
    `position_training` VARCHAR(100) NULL,
    PRIMARY KEY (`id`));');
}
if (!$CI->db->table_exists(db_prefix() . 'rec_criteria')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "rec_criteria` (
    `criteria_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `criteria_type` varchar(45) NOT NULL,
    `criteria_title` varchar(200) NOT NULL,
    `group_criteria` int(11)  NULL,
    `description` text NULL,
    `add_from` int(11) NOT NULL,
    `add_date` date NULL,
    `score_des1` text NULL,
    `score_des2` text NULL,
    `score_des3` text NULL,
    `score_des4` text NULL,
    `score_des5` text NULL,
    PRIMARY KEY (`criteria_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if (!$CI->db->table_exists(db_prefix() . 'position_training_question_form')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "position_training_question_form` (
    `questionid` int(11) NOT NULL AUTO_INCREMENT,
    `rel_id` int(11) NOT NULL,
    `rel_type` varchar(20) DEFAULT NULL,
    `question` mediumtext NOT NULL,
    `required` tinyint(1) NOT NULL DEFAULT '0',
    `question_order` int(11) NOT NULL,
    `point`int(11) NOT NULL,

    PRIMARY KEY (`questionid`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'p_t_form_question_box_description')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "p_t_form_question_box_description` (
   `questionboxdescriptionid` int(11) NOT NULL AUTO_INCREMENT,
   `description` mediumtext NOT NULL,
   `boxid` mediumtext NOT NULL,
   `questionid` int(11) NOT NULL,
   `correct` int(11) NULL DEFAULT '1' COMMENT'0: correct 1: incorrect',

   PRIMARY KEY (`questionboxdescriptionid`)
 ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

}
if (!$CI->db->table_exists(db_prefix() . 'checklist')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'checklist` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `group_id` int(11) NULL,
    PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'group_checklist_allocation')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'group_checklist_allocation` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `group_name` VARCHAR(100) NOT NULL,
    `meta` VARCHAR(100) NULL,
    `staffid` INT(11) NULL,
    PRIMARY KEY (`id`));');
}
if (!$CI->db->table_exists(db_prefix() . 'checklist_allocation')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'checklist_allocation` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `group_id` INT(11) NULL,
    `staffid` INT(11) NULL,
    `status` INT(11) NULL DEFAULT 0,
    PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'training_allocation')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'training_allocation` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `training_process_id` VARCHAR(100) NOT NULL,
    `staffid` INT(11) NULL,
    `training_type` int(11) NULL,
    `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `training_name` varchar(150) NULL,
    PRIMARY KEY (`id`));');
}
if (!$CI->db->table_exists(db_prefix() . 'transfer_records_reception')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "transfer_records_reception` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`varchar(150),  
    `meta` varchar(50), 
    `staffid` int(11) NULL, 
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('question_answers' ,db_prefix() . 'knowledge_base')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "knowledge_base`
    ADD COLUMN `question_answers` INT(11) NULL DEFAULT 0,
    ADD COLUMN `file_name` VARCHAR(255) NULL DEFAULT '',
    ADD COLUMN `curator` VARCHAR(11) NULL DEFAULT '',
    ADD COLUMN `benchmark` INT(11) NULL DEFAULT 0, 
    ADD COLUMN `score` INT(11) NULL DEFAULT 0
    ;");
}
if (!$CI->db->table_exists(db_prefix() . 'rec_job_position')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "rec_job_position` (
    `position_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `position_name` varchar(200) NOT NULL,
    `position_description` text NULL,
    PRIMARY KEY (`position_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'bonus_discipline')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "bonus_discipline` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NULL,
    `id_criteria`  VARCHAR(200)  NULL,
    `type` int(3)  NOT NULL,
    `apply_for` varchar(50) NULL, 
    `from_time` DATETIME NULL ,
    `lever_bonus` int(11)  NULL,
    `approver` int(11)  NULL,
    `url_file` longtext NULL ,
    `create_time` DATETIME NULL,
    `id_admin` int(3) NULL,
    `status` int(3) NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if (!$CI->db->table_exists(db_prefix() . 'bonus_discipline_detail')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "bonus_discipline_detail` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_bonus_discipline` int(11) NOT NULL,
    `from_time` DATETIME NULL ,
    `staff_id` int(11)  NULL,
    `department_id` longtext NULL ,
    `lever_bonus` int(11)  NULL,
    `formality` varchar(50) NULL,
    `formality_value` varchar(100) NULL,
    `description` longtext NULL ,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_workplace')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_workplace` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` varchar(200) NOT NULL,
      `workplace_address` varchar(400) NULL,
      `latitude` double,
      `longitude` double,
      `default` bit NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

//table setting staff contract type
if (!$CI->db->table_exists(db_prefix() . 'hr_staff_contract_type')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_staff_contract_type` (
      `id_contracttype` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name_contracttype` varchar(200) NOT NULL,
      `description` longtext NULL ,
      `duration` int(11) NULL,
      `unit` varchar(20) NULL,
      `insurance` boolean NULL,
      PRIMARY KEY (`id_contracttype`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_salary_form')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_salary_form` (
      `form_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `form_name` varchar(200) NOT NULL,
      `salary_val` decimal(15,2) NOT NULL,
      `tax` boolean NOT NULL,
      PRIMARY KEY (`form_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_allowance_type')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_allowance_type` (
      `type_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `type_name` varchar(200) NOT NULL,
      `allowance_val` decimal(15,2) NOT NULL,
      `taxable` boolean NOT NULL,
      PRIMARY KEY (`type_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_procedure_retire')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_procedure_retire` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `rel_name` TEXT DEFAULT NULL,
    `option_name` TEXT DEFAULT NULL,
    `status` int(11) NULL DEFAULT 1,
    `people_handle_id` int(11) NOT NULL,
    `procedure_retire_id` int(11) NOT NULL,

    PRIMARY KEY (`id`)

  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_procedure_retire_manage')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_procedure_retire_manage` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name_procedure_retire` TEXT NOT NULL,
      `department` varchar(250) NOT NULL,
      `datecreator` datetime NOT NULL ,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


//job position table
if (!$CI->db->table_exists(db_prefix() . 'hr_job_p')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_job_p` (
      `job_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `job_name` VARCHAR(100) NULL,
      `description` TEXT NULL,
      PRIMARY KEY (`job_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if (!$CI->db->table_exists(db_prefix() . 'hr_job_position')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_job_position` (
      `position_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `position_name` varchar(200) NOT NULL,
      `job_position_description` TEXT NULL,
      `job_p_id` int(11) UNSIGNED NOT NULL,
      `position_code` VARCHAR(50) NULL,
      `department_id` TEXT NULL,

      PRIMARY KEY (`position_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_jp_salary_scale')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_jp_salary_scale` (
      `salary_scale_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `job_position_id` int(11) UNSIGNED NOT NULL ,
      `rel_type` VARCHAR(100) NULL COMMENT 'salary:allowance:insurance',
      `rel_id` int(11) NULL,
      `value` TEXT NULL,

      PRIMARY KEY (`salary_scale_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_jp_interview_training')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_jp_interview_training` (
      `training_process_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `job_position_id` LONGTEXT NULL,
      `training_name` VARCHAR(100) NULL,
      `training_type` int(11) NULL,
      `description` TEXT NULL,
      `date_add` datetime NULL,
      `position_training_id` TEXT NULL,
      `mint_point` INT(11) NULL,

      PRIMARY KEY (`training_process_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_allocation_asset')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_allocation_asset` (
      `allocation_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `staff_id` int(11) UNSIGNED NOT NULL ,
      `asset_name` VARCHAR(100) NULL,
      `assets_amount` int(11) UNSIGNED NOT NULL ,
      `status_allocation` int(11) UNSIGNED  NULL DEFAULT 0 COMMENT '1: Allocated 0: Unallocated',

      PRIMARY KEY (`allocation_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_group_checklist_allocation')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_group_checklist_allocation` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `group_name` VARCHAR(100) NOT NULL,
      `meta` VARCHAR(100) NULL,
      `staffid` INT(11) NULL,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_checklist_allocation')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_checklist_allocation` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` VARCHAR(100) NOT NULL,
      `group_id` INT(11) NULL,
      `status` INT(11) NULL DEFAULT 0,

       PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_training_allocation')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_training_allocation` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `training_process_id` VARCHAR(100) NOT NULL,
    `staffid` INT(11) NULL,
    `training_type` int(11) NULL,
    `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `training_name` varchar(150) NULL,

     PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('jp_interview_training_id' ,db_prefix() . 'hr_training_allocation')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "hr_training_allocation`
    ADD COLUMN `jp_interview_training_id` INT(11) NULL ;");
}

if (!$CI->db->table_exists(db_prefix() . 'hr_p_t_surveyresultsets')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_p_t_surveyresultsets` (
   `resultsetid` int(11) NOT NULL AUTO_INCREMENT,
    `trainingid` int(11) NOT NULL,
    `ip` varchar(40) NOT NULL,
    `useragent` varchar(150) NOT NULL,
    `date` datetime NOT NULL,
    `staff_id` int(11) UNSIGNED NOT NULL,

     PRIMARY KEY (`resultsetid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_p_t_form_results')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_p_t_form_results` (
      
    `resultid` int(11) NOT NULL AUTO_INCREMENT,
    `boxid` int(11) NOT NULL,
    `boxdescriptionid` int(11) DEFAULT NULL,
    `rel_id` int(11) NOT NULL,
    `rel_type` varchar(20) DEFAULT NULL,
    `questionid` int(11) NOT NULL,
    `answer` text,
    `resultsetid` int(11) NOT NULL,

    PRIMARY KEY (`resultid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

}

if (!$CI->db->table_exists(db_prefix() . 'hr_rec_transfer_records')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_rec_transfer_records` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `staffid` int(11) NOT NULL,
      `firstname` varchar(100) NULL,
      `lastname` varchar(100) NULL,
      `birthday` date NULL,
      `gender` varchar(11) NULL,
      `staff_identifi` varchar(20) NULL,
      `creator` int(11) NOT NULL,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_position_training')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_position_training` (
    `training_id` int(11) NOT NULL AUTO_INCREMENT,
    `subject` mediumtext NOT NULL,
    `training_type` int(11) UNSIGNED NOT NULL,
    `slug` mediumtext NOT NULL,
    `description` text  NULL,
    `viewdescription` text,
    `datecreated` datetime NOT NULL,
    `redirect_url` varchar(100) DEFAULT NULL,
    `send` tinyint(1) NOT NULL DEFAULT '0',
    `onlyforloggedin` int(11) DEFAULT '0',
    `fromname` varchar(100) DEFAULT NULL,
    `iprestrict` tinyint(1) NOT NULL,
    `active` tinyint(1) NOT NULL DEFAULT '1',
    `hash` varchar(32) NOT NULL,
    `mint_point` VARCHAR(20) NULL,

    PRIMARY KEY (`training_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_position_training_question_form')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_position_training_question_form` (
    `questionid` int(11) NOT NULL AUTO_INCREMENT,
    `rel_id` int(11) NOT NULL,
    `rel_type` varchar(20) DEFAULT NULL,
    `question` mediumtext NOT NULL,
    `required` tinyint(1) NOT NULL DEFAULT '0',
    `question_order` int(11) NOT NULL,
    `point`int(11) NOT NULL,

    PRIMARY KEY (`questionid`)

  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_p_t_form_question_box')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_p_t_form_question_box` (
   `boxid` int(11) NOT NULL AUTO_INCREMENT,
    `boxtype` varchar(10) NOT NULL,
    `questionid` int(11) NOT NULL,

  PRIMARY KEY (`boxid`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_p_t_form_question_box_description')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_p_t_form_question_box_description` (
    `questionboxdescriptionid` int(11) NOT NULL AUTO_INCREMENT,
    `description` mediumtext NOT NULL,
    `boxid` mediumtext NOT NULL,
    `questionid` int(11) NOT NULL,
    `correct` int(11) NULL DEFAULT '1' COMMENT'0: correct 1: incorrect',

  PRIMARY KEY (`questionboxdescriptionid`)

  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_staff_contract')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_staff_contract` (
      `id_contract` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `contract_code` varchar(200) NOT NULL,
      `name_contract` int(11) NOT NULL,
      `staff` int(11) NOT NULL,
      `start_valid` date NULL,
      `end_valid` date NULL,
      `contract_status` varchar(100) NULL,
      `sign_day` date NULL,
      `staff_delegate` int(11) NULL,

      PRIMARY KEY (`id_contract`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('hourly_or_month' ,db_prefix() . 'hr_staff_contract')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'hr_staff_contract`
    ADD COLUMN `hourly_or_month` LONGTEXT NULL ');
}


if (!$CI->db->table_exists(db_prefix() . 'hr_staff_contract_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_staff_contract_detail` (
      `contract_detail_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `staff_contract_id` int(11) UNSIGNED NOT NULL,
      `type` text NULL,
      `rel_type` text NULL,
      `rel_value` decimal(15,2) DEFAULT '0.00',
      `since_date` date NULL,
      `contract_note` text NULL,

      PRIMARY KEY (`contract_detail_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


//add column for tbl staff
//

if (!$CI->db->field_exists('team_manage' ,db_prefix() . 'staff')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff`
    ADD COLUMN `team_manage` int(11) DEFAULT "0" ');
}
if (!$CI->db->field_exists('staff_identifi' ,db_prefix() . 'staff')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff`
    ADD COLUMN `staff_identifi` VARCHAR(200) NULL ');
}


if (!$CI->db->field_exists('birthday' ,db_prefix() . 'staff')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "staff`
  ADD COLUMN `birthday` date NULL AFTER `email_signature`,
  ADD COLUMN `birthplace` VARCHAR(200) NULL AFTER `birthday`,
  ADD COLUMN `sex` varchar(15) NULL AFTER `birthplace`,
  ADD COLUMN `marital_status` varchar(25) NULL AFTER `sex`,
  ADD COLUMN `nation` varchar(25) NULL AFTER `marital_status`,
  ADD COLUMN `religion` varchar(50) NULL AFTER `nation`,
  ADD COLUMN `identification` varchar(100) NULL AFTER `religion`,
  ADD COLUMN `days_for_identity` date NULL AFTER `identification`,
  ADD COLUMN `home_town` varchar(200) NULL AFTER `days_for_identity`,
  ADD COLUMN `resident` varchar(200) NULL AFTER `home_town`,
  ADD COLUMN `current_address` varchar(200) NULL AFTER `resident`,
  ADD COLUMN `literacy` varchar(50) NULL AFTER `current_address`,
  ADD COLUMN `orther_infor` text NULL AFTER `literacy`

;");
}


if (!$CI->db->field_exists('place_of_issue' ,db_prefix() . 'staff')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "staff`

    ADD COLUMN `place_of_issue` varchar(50) NULL AFTER `orther_infor`,
    ADD COLUMN `account_number` varchar(50) NULL AFTER `place_of_issue`,
    ADD COLUMN `name_account` varchar(50) NULL AFTER `account_number`,
    ADD COLUMN `issue_bank` varchar(200) NULL AFTER `name_account`,
    ADD COLUMN `Personal_tax_code` varchar(50) NULL AFTER `issue_bank`

;");
}

if (!$CI->db->field_exists('records_received' ,db_prefix() . 'staff')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff`
    ADD COLUMN `records_received` LONGTEXT NULL AFTER `issue_bank`');
}

if (!$CI->db->field_exists('status_work' ,db_prefix() . 'staff')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff`
    ADD COLUMN `status_work` VARCHAR(100) NULL');
}

if (!$CI->db->field_exists('date_update' ,db_prefix() . 'staff')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "staff`
    ADD COLUMN `date_update` DATE NULL AFTER `status_work`
  ;");
}

if (!$CI->db->field_exists('job_position' ,db_prefix() . 'staff')) {
   $CI->db->query('ALTER TABLE `' . db_prefix() . 'staff`
  ADD COLUMN `job_position` int(11) NULL AFTER `orther_infor`,
  ADD COLUMN `workplace` int(11) NULL AFTER `job_position`');
}


//general settings
  if (hr_profile_row_options_exists('"job_position_prefix"') == 0){
      $CI->db->query('INSERT INTO `'.db_prefix().'hr_profile_option` (`option_name`,`option_val`, `auto`) VALUES ("job_position_prefix", "#JOB", "1");
    ');
  }

  if (hr_profile_row_options_exists('"job_position_number"') == 0){
      $CI->db->query('INSERT INTO `'.db_prefix().'hr_profile_option` (`option_name`,`option_val`, `auto`) VALUES ("job_position_number", "1", "1");
    ');
  }

  if (hr_profile_row_options_exists('"contract_code_prefix"') == 0){
      $CI->db->query('INSERT INTO `'.db_prefix().'hr_profile_option` (`option_name`,`option_val`, `auto`) VALUES ("contract_code_prefix", "#CONTRACT", "1");
    ');
  }

  if (hr_profile_row_options_exists('"contract_code_number"') == 0){
      $CI->db->query('INSERT INTO `'.db_prefix().'hr_profile_option` (`option_name`,`option_val`, `auto`) VALUES ("contract_code_number", "1", "1");
    ');
  }

  

if (!$CI->db->table_exists(db_prefix() . 'hr_dependent_person')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_dependent_person` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `staffid` int(11) UNSIGNED  NULL,
      `dependent_name` varchar(100) NULL ,
      `relationship` varchar(100) NULL ,
      `dependent_bir` date NULL ,
      `start_month` date NULL ,
      `end_month` date NULL ,
      `dependent_iden` varchar(20) NOT NULL ,
      `reason` longtext NULL ,
      `status` int(11) UNSIGNED  NULL DEFAULT 0 ,
      `status_comment` longtext NULL,

      
      PRIMARY KEY (`id`,`dependent_iden`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_list_staff_quitting_work')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_list_staff_quitting_work` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `staffid` int(11) DEFAULT NULL,
    `staff_name` TEXT NULL,
    `department_name` TEXT NULL,
    `role_name` TEXT NULL,
    `email` TEXT NULL,
    `dateoff` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `approval` varchar(100) NULL DEFAULT NULL,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if (!$CI->db->table_exists(db_prefix() . 'hr_procedure_retire_of_staff')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_procedure_retire_of_staff` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `rel_id` int(11) DEFAULT NULL,
    `option_name` TEXT DEFAULT NULL,
    `status` int(11) NULL DEFAULT 0,
    `staffid` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_procedure_retire_of_staff_by_id')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_procedure_retire_of_staff_by_id` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `rel_name` TEXT DEFAULT NULL,
    `people_handle_id` int(11),
     PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


/*knowledge_base for Q&A*/
if (!$CI->db->table_exists(db_prefix() . 'hr_knowledge_base')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_knowledge_base` (
    `articleid` int(11) NOT NULL AUTO_INCREMENT,
    `articlegroup` int(11) NOT NULL,
    `subject` mediumtext NOT NULL,
    `description` text NOT NULL,
    `slug` mediumtext NOT NULL,
    `active` tinyint(4) NOT NULL,
    `datecreated` datetime NOT NULL,
    `article_order` int(11) NOT NULL DEFAULT '0',
    `staff_article` int(11) NOT NULL DEFAULT '0',
    `question_answers` int(11) DEFAULT '0',
    `file_name` varchar(255) DEFAULT '',
    `curator` varchar(11) DEFAULT '',
    `benchmark` int(11) DEFAULT '0',
    `score` int(11) DEFAULT '0',

    PRIMARY KEY (`articleid`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_knowledge_base_groups')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_knowledge_base_groups` (
    `groupid` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(191) NOT NULL,
    `group_slug` text,
    `description` mediumtext,
    `active` tinyint(4) NOT NULL,
    `color` varchar(10) DEFAULT '#28B8DA',
    `group_order` int(11) DEFAULT '0',

    PRIMARY KEY (`groupid`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_knowedge_base_article_feedback')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_knowedge_base_article_feedback` (
    `articleanswerid` int(11) NOT NULL AUTO_INCREMENT,
    `articleid` int(11) NOT NULL,
    `answer` int(11) NOT NULL,
    `ip` varchar(40) NOT NULL,
    `date` datetime NOT NULL,

    PRIMARY KEY (`articleanswerid`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_views_tracking')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_views_tracking` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `rel_id` int(11) NOT NULL,
    `rel_type` varchar(40) NOT NULL,
    `date` datetime NOT NULL,
    `view_ip` varchar(40) NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'hr_education')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "hr_education` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `staff_id` INT(11) NOT NULL,
      `admin_id` INT(11) NOT NULL,
      `programe_id` INT(11) NULL,
      `training_programs_name` text NOT NULL,
      `training_places` text NULL,
      `training_time_from` DATETIME  NULL,
      `training_time_to` DATETIME  NULL,
      `date_create` DATETIME NULL,
      `training_result` VARCHAR(150) NULL,
      `degree` VARCHAR(150) NULL,
      `notes` text NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


/*knowledge_base for Q&A*/

if (hr_profile_row_options_exists('"staff_code_prefix"') == 0){
      $CI->db->query('INSERT INTO `'.db_prefix().'hr_profile_option` (`option_name`,`option_val`, `auto`) VALUES ("staff_code_prefix", "EC", "1");
    ');
  }

  if (hr_profile_row_options_exists('"staff_code_number"') == 0){
      $CI->db->query('INSERT INTO `'.db_prefix().'hr_profile_option` (`option_name`,`option_val`, `auto`) VALUES ("staff_code_number", "1", "1");
    ');
  }

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


//V103 add option : additional_training, show result training
if (!$CI->db->field_exists('additional_training' ,db_prefix() . 'hr_jp_interview_training')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "hr_jp_interview_training`
    ADD COLUMN `additional_training` VARCHAR(100) NULL DEFAULT '',
    ADD COLUMN `staff_id` TEXT NULL ,
    ADD COLUMN `time_to_start` DATE NULL ,
    ADD COLUMN `time_to_end` DATE NULL
  ;");
}

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

  add_option('hr_profile_hide_menu', 1, 1);
