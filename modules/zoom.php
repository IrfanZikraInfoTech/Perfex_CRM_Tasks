<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: ZOOM Meeting
Description: Complete ZOOM based collaboration tool, which provides all the real time communication with in Perfex
Version: 1.0
Requires at least: 2.3.*
*/

define('ZOOM_MODULE_NAME', 'zoom');
$CI = &get_instance();



/**
* Register activation module hook
*/
register_activation_hook(ZOOM_MODULE_NAME, 'zoom_module_activation_hook');

function zoom_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

hooks()->add_action('admin_init', 'zoom_module_init_menu_items');




/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(ZOOM_MODULE_NAME, [ZOOM_MODULE_NAME]);


function zoom_module_init_menu_items()
{
	if(has_permission('zoom','','view')){
		$CI = &get_instance();

		$CI->app_menu->add_sidebar_menu_item('zoom', [
			'name'     => _l('zoom'), // The name if the item
			'collapse' => true, // Indicates that this item will have submitems
			'position' => 10, // The menu position
			'icon'     => 'fa fa-comment-o', // Font awesome icon
		]);
		
		$CI->app_menu->add_sidebar_children_item('zoom', [
			'slug'     => 'send-zoom', // Required ID/slug UNIQUE for the child menu
			'name'     => _l('zoom_meeting_list'), // The name if the item
			'href'     => admin_url('zoom'),
			'position' => 5,
		   
		   
		]);

		// The first paremeter is the parent menu ID/Slug
		$CI->app_menu->add_sidebar_children_item('zoom', [
			'slug'     => 'create-meeting', // Required ID/slug UNIQUE for the child menu
			'name'     => _l('zoom_create_meeting'), // The name if the item
			'href'     =>admin_url('zoom/create_meeting'),
			'position' => 5, // The menu position
		   
		]);


	
		$CI->app_menu->add_sidebar_children_item('zoom', [
			'slug'     => 'meeting-registrant', // Required ID/slug UNIQUE for the child menu
			'name'     => _l('zoom_add_registrant'), // The name if the item
			'href'     =>admin_url('zoom/add_registrant'),
			'position' => 5, // The menu position
		   
		]);

		$CI->app_menu->add_sidebar_children_item('zoom', [
			'slug'     => 'api-meeting', // Required ID/slug UNIQUE for the child menu
			'name'     => _l('zoom_api_settings'), // The name if the item
			'href'     =>admin_url('zoom/api_meeting'),
			'position' => 5, // The menu position
		   
		]);
		
		
	
    }
}
