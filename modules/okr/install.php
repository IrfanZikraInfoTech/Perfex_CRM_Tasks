<?php

defined('BASEPATH') or exit('No direct script access allowed');
add_option('maximum_allowed_okrs_attachments', 20);
if (!$CI->db->table_exists(db_prefix() . 'okr_setting_circulation')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "okr_setting_circulation` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name_circulation` varchar(150) NOT NULL,
      `from_date` date NOT NULL,
      `to_date` date NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'okr_setting_question')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "okr_setting_question` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `question` text NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'okr_setting_evaluation_criteria')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "okr_setting_evaluation_criteria` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `group_criteria` int(11) NOT NULL,
      `name` varchar(250) NOT NULL,
      `scores` int(250) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'okrs')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "okrs` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `circulation` int(11) NOT NULL,
      `okr_superior` text NULL,
      `your_target` varchar(250) NOT NULL,
      `okr_cross` text NULL,
      `display` int(11) NULL,
      `creator` int(11) NOT NULL,
      `datecreator` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'okrs_key_result')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "okrs_key_result` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `okrs_id` int(11) NOT NULL,
      `main_results` text NOT NULL,
      `target` text NOT NULL,
      `departments` int(11) NULL,
      `plan` text NOT NULL,
      `results` text NOT NULL,
      `unit` text NOT NULL,
      `datecreator` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if ($CI->db->field_exists('departments' ,db_prefix() . 'okrs_key_result')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_key_result`
      DROP `departments`;
  ');
}

if (!$CI->db->field_exists('change' ,db_prefix() . 'okrs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs`
      ADD COLUMN `change` int(11) not null default 0
  ');
}

if (!$CI->db->field_exists('person_assigned' ,db_prefix() . 'okrs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs`
      ADD COLUMN `person_assigned` int(11) not null
  ');
}

if (!$CI->db->table_exists(db_prefix() . 'okrs_log')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "okrs_log` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `circulation` int(11) NOT NULL,
      `okr_superior` text NULL,
      `your_target` varchar(250) NOT NULL,
      `okr_cross` text NULL,
      `display` int(11) NULL,
      `editor` int(11) NOT NULL,
      `date_edit` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'okrs_key_result_log')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "okrs_key_result_log` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `okrs_id` int(11) NOT NULL,
      `main_results` text NOT NULL,
      `target` text NOT NULL,
      `plan` text NOT NULL,
      `results` text NOT NULL,
      `unit` text NOT NULL,
      `editor` int(11) NOT NULL,
      `date_edit` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('status' ,db_prefix() . 'okrs_key_result_log')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_key_result_log`
      ADD COLUMN `status` varchar(20) not null
  ');
}

if (!$CI->db->field_exists('change' ,db_prefix() . 'okrs_log')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_log`
      ADD COLUMN `change` int(11) not null default 0
  ');
}

if (!$CI->db->field_exists('person_assigned' ,db_prefix() . 'okrs_log')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_log`
      ADD COLUMN `person_assigned` int(11) not null
  ');
}

if (!$CI->db->field_exists('status' ,db_prefix() . 'okrs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs`
      ADD COLUMN `status` int(11) not null
  ');
}

if (!$CI->db->field_exists('progress' ,db_prefix() . 'okrs_key_result')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_key_result`
      ADD COLUMN `progress` float(2,2) not null default 0.00
  ');
}

if (!$CI->db->field_exists('progress' ,db_prefix() . 'okrs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs`
      ADD COLUMN `progress` float(2,2) not null default 0.00
  ');
}

if (!$CI->db->field_exists('achieved' ,db_prefix() . 'okrs_key_result')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_key_result`
      ADD COLUMN `achieved` text null
  ');
}

if (!$CI->db->field_exists('recently_checkin' ,db_prefix() . 'okrs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs`
      ADD COLUMN `recently_checkin` date null,
      ADD COLUMN `upcoming_checkin` date null 
  ');
}

if (!$CI->db->table_exists(db_prefix() . 'okrs_checkin')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "okrs_checkin` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `okrs_id` int(11) NOT NULL,
      `main_results` text NOT NULL,
      `target` text NOT NULL,
      `achieved` text NOT NULL,
      `progress` float(2,2) not null default 0.00,
      `confidence_level` int(11) not null default 1,
      `unit` text NOT NULL,
      `answer` text NOT NULL,
      `evaluation_criteria` int(11) NULL,
      `comment` text NULL,
      `type` int(11) NULL,
      `recently_checkin` date NULL,
      `upcoming_checkin` date NULL,
      `editor` int(11) NOT NULL,
      `created_date` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('key_results_id' ,db_prefix() . 'okrs_checkin')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_checkin`
      ADD COLUMN `key_results_id` int(11) not null
  ');
}

if (!$CI->db->table_exists(db_prefix() . 'okrs_checkin_log')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "okrs_checkin_log` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `okrs_id` int(11) NOT NULL,
      `main_results` text NOT NULL,
      `key_results_id` int(11) NOT NULL,
      `target` text NOT NULL,
      `achieved` text NOT NULL,
      `progress` float(2,2) not null default 0.00,
      `confidence_level` int(11) not null default 1,
      `unit` text NOT NULL,
      `answer` text NOT NULL,
      `evaluation_criteria` int(11) NULL,
      `comment` text NULL,
      `type` int(11) NULL,
      `recently_checkin` date NULL,
      `upcoming_checkin` date NULL,
      `editor` int(11) NOT NULL,
      `created_date` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if ($CI->db->field_exists('progress' ,db_prefix() . 'okrs_checkin')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_checkin`
      MODIFY progress DECIMAL(5,2)
  ');
}

if ($CI->db->field_exists('progress' ,db_prefix() . 'okrs_checkin_log')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_checkin_log`
      MODIFY progress DECIMAL(5,2)
  ');
}

if ($CI->db->field_exists('progress' ,db_prefix() . 'okrs_key_result')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_key_result`
      MODIFY progress DECIMAL(5,2)
  ');
}

if ($CI->db->field_exists('progress' ,db_prefix() . 'okrs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs`
      MODIFY progress DECIMAL(5,2)
  ');
}
if (!$CI->db->field_exists('progress_total' ,db_prefix() . 'okrs_checkin_log')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_checkin_log`
      ADD COLUMN `progress_total` DECIMAL(5,2) null
  ');
}

if (!$CI->db->field_exists('confidence_level' ,db_prefix() . 'okrs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs`
      ADD COLUMN `confidence_level` int(11) not null default 1
  ');
}

if (!$CI->db->field_exists('confidence_level' ,db_prefix() . 'okrs_key_result')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_key_result`
      ADD COLUMN `confidence_level` int(11) not null default 1
  ');
}


if (!$CI->db->field_exists('progress' ,db_prefix() . 'okrs_key_result_log')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_key_result_log`
      ADD COLUMN `progress` DECIMAL(5,2) null
  ');
}
if (!$CI->db->field_exists('achieved' ,db_prefix() . 'okrs_key_result_log')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_key_result_log`
      ADD COLUMN `achieved` DECIMAL(5,2) null
  ');
}

if (!$CI->db->field_exists('confidence_level' ,db_prefix() . 'okrs_key_result_log')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_key_result_log`
      ADD COLUMN `confidence_level` DECIMAL(5,2) null
  ');
}

if (!$CI->db->table_exists(db_prefix() . 'okr_setting_unit')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "okr_setting_unit` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `unit` text NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if ($CI->db->field_exists('status' ,db_prefix() . 'okrs')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs`
      MODIFY status int(11) not null default 0
  ');
}

if (!$CI->db->field_exists('complete_okrs' ,db_prefix() . 'okrs_checkin')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_checkin`
      ADD COLUMN `complete_okrs` int(11) not null default 0

  ');
}

if (!$CI->db->field_exists('complete_okrs' ,db_prefix() . 'okrs_checkin_log')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'okrs_checkin_log`
      ADD COLUMN `complete_okrs` int(11) not null default 0
  ');
}

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