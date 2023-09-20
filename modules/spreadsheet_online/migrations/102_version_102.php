<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_102 extends App_module_migration
{
	public function up()
	{
		$CI = & get_instance();        
		if (!$CI->db->field_exists('role' ,db_prefix() . 'spreadsheet_online_related')) {
			$CI->db->query('ALTER TABLE `' . db_prefix() . 'spreadsheet_online_related`
				ADD COLUMN `hash` varchar(250) NOT NULL DEFAULT "",
				ADD COLUMN `role` int(1) NOT NULL DEFAULT 1
				');			
			$CI->db->where('hash = ""');
			$related = $CI->db->get(db_prefix() . 'spreadsheet_online_related')->result_array();
			if(count($related) > 0){
				foreach ($related as $key => $value) {
					$data['hash'] = app_generate_hash();
					$CI->db->where('id', $value['id']);
					$CI->db->update(db_prefix() . 'spreadsheet_online_related', $data);
				}
			}
		}
	}
}
