<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_103 extends App_module_migration
{
	public function up()
	{        
		$CI = & get_instance();
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
}
