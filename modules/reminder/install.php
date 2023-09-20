<?php
defined('BASEPATH') or exit('No direct script access allowed');
if (get_option('reminder_queries_run') != 1) {
  update_option('reminder_queries_run', 1);
  if (!$CI->db->field_exists('customer', db_prefix() . 'reminders')) {
    $CI->db->query('ALTER TABLE `tblreminders`
   ADD `customer` varchar(100) NOT NULL;');
  } 
  if (!$CI->db->field_exists('contact', db_prefix() . 'reminders')) {
    $CI->db->query('ALTER TABLE `tblreminders`
   ADD `contact` varchar(100) NOT NULL;');
  }
  if (!$CI->db->field_exists('assigned_to', db_prefix() . 'reminders')) {
    $CI->db->query('ALTER TABLE `tblreminders`
   ADD `assigned_to` varchar(40) NOT NULL;');
  }
  if (!$CI->db->field_exists('repeat_every', db_prefix() . 'reminders')) {
    $CI->db->query('ALTER TABLE `tblreminders`
   ADD `repeat_every` varchar(40) NOT NULL;');
  }
  if (!$CI->db->field_exists('cycles', db_prefix() . 'reminders')) {
    $CI->db->query('ALTER TABLE `tblreminders`
   ADD `cycles` varchar(11) NOT NULL;');
  }
  
  if (!$CI->db->field_exists('recurring_type', db_prefix() . 'reminders')) {
    $CI->db->query('ALTER TABLE `tblreminders`
   ADD `recurring_type` varchar(20) NOT NULL;');
  }
  if (!$CI->db->field_exists('recurring', db_prefix() . 'reminders')) {
    $CI->db->query('ALTER TABLE `tblreminders`
   ADD `recurring` int(11) NOT NULL;');
  }
  if (!$CI->db->field_exists('is_recurring_from', db_prefix() . 'reminders')) {
    $CI->db->query('ALTER TABLE `tblreminders`
   ADD `is_recurring_from` int(11) NOT NULL;');
  }
  if (!$CI->db->field_exists('custom_recurring', db_prefix() . 'reminders')) {
    $CI->db->query('ALTER TABLE `tblreminders`
   ADD `custom_recurring` int(11) NOT NULL;');
  }
  if (!$CI->db->field_exists('total_cycles', db_prefix() . 'reminders')) {
    $CI->db->query('ALTER TABLE `tblreminders`
   ADD `total_cycles` int(11) NOT NULL;');
  }
  if (!$CI->db->field_exists('last_recurring_date', db_prefix() . 'reminders')) {
    $CI->db->query('ALTER TABLE `tblreminders`
   ADD `last_recurring_date` DATE NULL DEFAULT NULL;');
  }

  if (!$CI->db->field_exists('startdate', db_prefix() . 'reminders')) {
    $CI->db->query('ALTER TABLE `tblreminders`
   ADD `startdate` DATE NULL DEFAULT NULL;');
  }
  if (!$CI->db->field_exists('total_amount', db_prefix() . 'reminders')) {
    $CI->db->query('ALTER TABLE `tblreminders`
   ADD `total_amount` int(40) DEFAULT NULL;');
  }

  if (!$CI->db->field_exists('services', db_prefix() . 'reminders')) {
    $CI->db->query('ALTER TABLE `tblreminders`
   ADD `services` varchar(100) DEFAULT NULL;');
  }

  if (!$CI->db->field_exists('duedate', db_prefix() . 'reminders')) {
    $CI->db->query('ALTER TABLE `tblreminders`
   ADD `duedate` DATE NULL DEFAULT NULL;');
  }
  if (!$CI->db->field_exists('is_complete', db_prefix() . 'reminders')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "reminders` ADD is_complete ENUM('1','0') NOT NULL;");
  }
  if (!$CI->db->field_exists('notify_by_email_client', db_prefix() . 'reminders')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "reminders` ADD notify_by_email_client ENUM('2','1','0') DEFAULT '0' NOT NULL;");
  }
  if (!$CI->db->table_exists(db_prefix() . 'reminder_activity')) {
    $CI->db->query('CREATE TABLE `tblreminder_activity` (
    `id` int(11) NOT NULL,
    `reminder_id` int(11) NOT NULL,
    `description` mediumtext NOT NULL,
    `additional_data` text DEFAULT NULL,
    `date` datetime NOT NULL,
    `staffid` int(11) NOT NULL,
    `full_name` varchar(100) DEFAULT NULL
  )ENGINE=InnoDB DEFAULT CHARSET=utf8;');
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'reminder_activity`
    ADD PRIMARY KEY (`id`)');
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'reminder_activity`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
  }
  $row_exists = $CI->db->query('SELECT * FROM ' . db_prefix() . 'emailtemplates WHERE type = "reminder" and slug = "reminder-send-to-contact" and language = "english";')->row();
  if (!$row_exists) {
    $message = '<p>Hi {contact_name}<br /><br /><strong>Description:</strong> {item_description}<br /></p>';
    $CI->db->query("INSERT INTO `" . db_prefix() . "emailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `fromemail`, `plaintext`, `active`, `order`) VALUES ('reminder', 'reminder-send-to-contact', 'english', 'Reminder', 'New Reminder','" . $message . "','', NULL, 0, 1, 0);");
    foreach ($CI->app->get_available_languages() as $avLanguage) {
      if ($avLanguage != 'english') {
        $CI->db->query("INSERT INTO `" . db_prefix() . "emailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `fromemail`, `plaintext`, `active`, `order`) VALUES ('reminder', 'reminder-send-to-contact', '" . $avLanguage . "', 'Reminder [" . $avLanguage . "]', 'New Reminder','" . $message . "','', NULL, 0, 1, 0);");
      }
    }
  }
  $row_exists = $CI->db->query('SELECT * FROM ' . db_prefix() . 'emailtemplates WHERE type = "client" and slug = "reminder-service-send-to-contact" and language = "english";')->row();
  if (!$row_exists) {
    $message = '<p>Hi {contact_name}<br /><br /><strong>Description:</strong> {item_description}<br /></p>';
    $CI->db->query("INSERT INTO `" . db_prefix() . "emailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `fromemail`, `plaintext`, `active`, `order`) VALUES ('client', 'reminder-service-send-to-contact', 'english', 'Services', 'New Service','" . $message . "','', NULL, 0, 1, 0);");
    foreach ($CI->app->get_available_languages() as $avLanguage) {
      if ($avLanguage != 'english') {
        $CI->db->query("INSERT INTO `" . db_prefix() . "emailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `fromemail`, `plaintext`, `active`, `order`) VALUES ('client', 'reminder-service-send-to-contact', '" . $avLanguage . "', 'Services [" . $avLanguage . "]', 'New Service','" . $message . "','', NULL, 0, 1, 0);");
      }
    }
  }
  if (!$CI->db->field_exists('notify_by_sms_client', db_prefix() . 'reminders')) {
    $CI->db->query("ALTER TABLE tblreminders ADD notify_by_sms_client ENUM('2','1','0') DEFAULT '0' NOT NULL;");
  }
  if (!$CI->db->field_exists('created_by_staff', db_prefix() . 'reminders')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "reminders ADD `created_by_staff` INT(11) NULL DEFAULT NULL;");
  }
  $CI->db->query('UPDATE ' . db_prefix() . 'emailtemplates SET type = "client" WHERE type = "reminder" AND slug = "reminder-send-to-contact"');
}

if (!$CI->db->table_exists(db_prefix() . 'reminder_services')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'reminder_services` (
    `id` int(11) NOT NULL,
    `service_name` varchar(200) DEFAULT NULL,
    `service_amount` int(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'reminder_services`
  ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'reminder_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}
if (!$CI->db->table_exists(db_prefix() . 'reminder_service_value')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'reminder_service_value` (
    `id` int(11) NOT NULL,
    `rem_id` int(40) DEFAULT NULL,
    `service_id` int(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'reminder_service_value`
  ADD PRIMARY KEY (`id`);');

  $CI->db->query('ALTER TABLE `' . db_prefix() . 'reminder_service_value`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}
