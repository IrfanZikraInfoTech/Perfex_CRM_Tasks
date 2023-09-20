<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_103 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();
		$Table = db_prefix() . 'reminders';
		if ($CI->db->table_exists($Table)) {
			if(!$CI->db->field_exists('created_by_staff', $Table)){
				$CI->db->query("ALTER TABLE ".$Table." ADD `created_by_staff` INT(11) NULL DEFAULT NULL AFTER `other_relation_type`;");
			}
		}
		$emailtemplates = $CI->db->query('SELECT * FROM '.db_prefix() . 'emailtemplates where `slug` = "reminder-send-to-contact" AND `type`= "reminder";')->result_array();
		if(isset($emailtemplates) && !empty($emailtemplates)){
			foreach($emailtemplates as $template){
				$CI->db->where('emailtemplateid',$template['emailtemplateid']);
				$CI->db->update(db_prefix() . 'emailtemplates', ['type' => 'client']);
			}
		}

		foreach ($CI->app->get_available_languages() as $avLanguage) {
			$template_found = $CI->db->where(['slug' => 'reminder-send-to-contact', 'language' => $avLanguage])->get(db_prefix().'emailtemplates')->row();
			if(!isset($template_found) && empty($$template_found)){
				$CI->db->query("INSERT INTO `".db_prefix() ."emailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `fromemail`, `plaintext`, `active`, `order`) VALUES ('reminder', 'reminder-send-to-contact', '".$avLanguage."', 'Reminder [".$avLanguage."]', 'New Reminder','','', NULL, 0, 1, 0);");
			}
		}
	}
}