<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_105 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();

					//V105: hr_profile_hide_menu
		add_option('hr_profile_hide_menu', 1, 1);
	}
}
