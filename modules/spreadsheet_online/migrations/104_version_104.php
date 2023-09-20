<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_104 extends App_module_migration
{
	public function up()
	{        
		$CI = & get_instance();
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
	}
}
