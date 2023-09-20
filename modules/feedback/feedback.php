<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Feedback
Description: Get Customer Feedback for Completed Projects
Version: 1.0
Requires at least: 2.3.*
*/

define('FEEDBACK_MODULE_NAME', 'feedback');
$CI = &get_instance();


/**
* Load the module helper
*/
$CI->load->helper(FEEDBACK_MODULE_NAME.'/feedback');

/**
* Register activation module hook
*/
register_activation_hook(FEEDBACK_MODULE_NAME, 'feedback_module_activation_hook');

function feedback_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

hooks()->add_action('admin_init', 'feedback_module_init_menu_items');
hooks()->add_action('clients_init', 'feedback_client_module_init_menu_items');

/**
 * Init feedback module menu items in setup in admin_init hook
 * @return null
 */
 
function feedback_client_module_init_menu_items()
{
	$count='';
	$CI = &get_instance();
	$client_id=$CI->session->userdata('client_user_id');
	$var = count_client_projects($client_id);
	if($var > 0){
	$count=' ('.$var.')';
	}	
 // Item for all clients
     if(is_client_logged_in()) { 
		add_theme_menu_item('client-feedback', [
				'name'     => 'Feedback'.$count,
				'href'     => site_url('feedback/client/client_feedback'),
				'position' => 4,
			]);
     }
}	

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(FEEDBACK_MODULE_NAME, [FEEDBACK_MODULE_NAME]);


function feedback_module_init_menu_items()
{
	if(has_permission('feedback','','view')){
		$CI = &get_instance();

		$CI->app_menu->add_sidebar_menu_item('feedback', [
			'name'     => _l('feedback'), // The name if the item
			'collapse' => true, // Indicates that this item will have submitems
			'position' => 10, // The menu position
			'icon'     => 'fa fa-question-circle', // Font awesome icon
		]);

		// The first paremeter is the parent menu ID/Slug
		$CI->app_menu->add_sidebar_children_item('feedback', [
			'slug'     => 'feedback-received', // Required ID/slug UNIQUE for the child menu
			'name'     => _l('feedback_received'), // The name if the item
			'href'     =>admin_url('feedback/feedback_received'),
			'position' => 10, // The menu position
		   
		]);
		
		$CI->app_menu->add_sidebar_children_item('feedback', [
			'slug'     => 'send-feedback', // Required ID/slug UNIQUE for the child menu
			'name'     => _l('send_request'), // The name if the item
			'href'     => admin_url('feedback'),
			'position' => 5,
		   
		   
		]);
	
    }
}
