<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_104 extends App_module_migration
{
	public function up() 
	{
		$CI = &get_instance();
		$Table = db_prefix() . 'reminders';
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
	}
}