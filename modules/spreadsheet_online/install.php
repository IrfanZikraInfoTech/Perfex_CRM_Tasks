<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'spreadsheet_online_my_folder')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "spreadsheet_online_my_folder` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `parent_id` TEXT NOT NULL,
      `name` TEXT NOT NULL,
      `type` VARCHAR(20) NULL,
      `size` text NULL,
      `staffid` int(11) NOT NULL,
      `category` varchar(20) NOT NULL DEFAULT 'my_folder',
      `inserted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
	$CI->db->query('INSERT INTO `tblspreadsheet_online_my_folder` (`parent_id`, `name`, `type`, `size`, `staffid`) VALUES ("0", "Spreadsheet Online Root", "folder", "--", "'.get_staff_user_id().'");
    ');
}

if (!$CI->db->field_exists('data_form' ,db_prefix() . 'spreadsheet_online_my_folder')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'spreadsheet_online_my_folder`
      ADD COLUMN `data_form` LONGTEXT NULL
  ');
}


if (!$CI->db->field_exists('staffs_share' ,db_prefix() . 'spreadsheet_online_my_folder')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'spreadsheet_online_my_folder`
      ADD COLUMN `staffs_share` TEXT NULL,
      ADD COLUMN `departments_share` TEXT NULL,
      ADD COLUMN `clients_share` TEXT NULL,
      ADD COLUMN `client_groups_share` TEXT NULL
  ');
}


if (!$CI->db->table_exists(db_prefix() . 'spreadsheet_online_hash_share')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "spreadsheet_online_hash_share` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `rel_type` varchar(20) NOT NULL,
      `rel_id` int(11) NOT NULL,
      `id_share` int(11) NOT NULL,
      `hash` TEXT NULL,
      `inserted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
  
}

if (!$CI->db->field_exists('role' ,db_prefix() . 'spreadsheet_online_hash_share')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'spreadsheet_online_hash_share`
      ADD COLUMN `role` int(1) NOT NULL DEFAULT 1
  ');
}

if (!$CI->db->field_exists('rel_type' ,db_prefix() . 'spreadsheet_online_my_folder')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'spreadsheet_online_my_folder`
      ADD COLUMN `rel_type` varchar(100) NULL,
      ADD COLUMN `rel_id` varchar(11) NULL
  ');
}

if (!$CI->db->field_exists('group_share_staff' ,db_prefix() . 'spreadsheet_online_my_folder')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'spreadsheet_online_my_folder`
      ADD COLUMN `group_share_staff` varchar(1) NULL,
      ADD COLUMN `group_share_client` varchar(1) NULL
  ');
}

if (!$CI->db->table_exists(db_prefix() . 'spreadsheet_online_related')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "spreadsheet_online_related` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `parent_id` int(11) NOT NULL,
      `rel_type` varchar(20) NOT NULL,
      `rel_id` int(11) NOT NULL,
      `inserted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('role' ,db_prefix() . 'spreadsheet_online_related')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'spreadsheet_online_related`
      ADD COLUMN `hash` varchar(250) NOT NULL DEFAULT "",
      ADD COLUMN `role` int(1) NOT NULL DEFAULT 1
  ');

  $CI = & get_instance();
  $CI->db->where('hash = ""');
  $related = $CI->db->get(db_prefix() . 'spreadsheet_online_related')->result_array();
  if(count($related) > 0){
    foreach ($related as $key => $value) {
      $data['hash'] = app_generate_hash();
      $CI->db->where('id', $value['id']);
      $CI->db->update(db_prefix() . 'spreadsheet_online_related', $data);
    }
  }

  $CI->db->where('hash = "" or hash IS NULL');
    $share = $CI->db->get(db_prefix() . 'spreadsheet_online_hash_share')->result_array();

    if(count($share) > 0){
      foreach ($share as $key => $value) {
        $data['hash'] = app_generate_hash();
        $CI->db->where('id', $value['id']);
        $CI->db->update(db_prefix() . 'spreadsheet_online_hash_share', $data);
      }
    }
}

create_email_template('Spreadsheet Online - Share {type_spreadsheet} For You', '<span style=\"font-size: 12pt;\"> Hello {receiver}. </span><br /><br /><span style=\"font-size: 12pt;\">You have been shared by {sender} in the {name_spreadsheet} {type_spreadsheet}</span>
  Please click on the link to view information: {share_link_spreadsheet}
  </span><br /><br />', 'spreadsheet_online', 'Spreadsheet share assigned', 'spreadsheet-share-assigned');

create_email_template('Spreadsheet Online - Share {type_spreadsheet} For You', '<span style=\"font-size: 12pt;\"> Hello {receiver}. </span><br /><br /><span style=\"font-size: 12pt;\">You have been shared by {sender} in the {name_spreadsheet} {type_spreadsheet}</span>
  Please click on the link to view information: {share_link_client_spreadsheet}
  </span><br /><br />', 'spreadsheet_online', 'Spreadsheet share assigned client', 'spreadsheet-share-assigned-client');

add_option('spreadsheet_staff_notification', 0);
add_option('spreadsheet_email_templates_staff', 0);
add_option('spreadsheet_client_notification', 0);
add_option('spreadsheet_email_templates_client', 0);