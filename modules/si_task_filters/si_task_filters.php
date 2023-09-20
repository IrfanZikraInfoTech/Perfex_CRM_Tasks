<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: SI Task Filters
Description: Module will Generate Custom Task Filters and save filters as Templates for future use.
Author: Sejal Infotech
Version: 1.0.1
Requires at least: 1.0.*
*/

define('SI_TASK_FILTERS_MODULE_NAME', 'si_task_filters');

$CI = &get_instance();

hooks()->add_action('admin_init', 'si_task_filters_init_menu_items');
hooks()->add_action('admin_init', 'si_task_filters_permissions');

/**
* Load the module helper
*/
$CI->load->helper(SI_TASK_FILTERS_MODULE_NAME . '/si_task_filters');

/**
* Register activation module hook
*/
register_activation_hook(SI_TASK_FILTERS_MODULE_NAME, 'si_task_filters_activation_hook');

function si_task_filters_activation_hook()
{
    $CI = &get_instance();
	require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(SI_TASK_FILTERS_MODULE_NAME, [SI_TASK_FILTERS_MODULE_NAME]);

/**
 * Init menu setup module menu items in setup in admin_init hook
 * @return null
 */
function si_task_filters_init_menu_items()
{
	/**
	* If the logged in user is administrator, add custom Reports in Sidebar, if want to add menu in Setup then Write Setup instead of sidebar in menu ceation
	*/
	if (is_admin() || has_permission('si_task_filters', '', 'view')) {
		$CI = &get_instance();
		$CI->app_menu->add_sidebar_menu_item('custom-reports', [
			'collapse' => true,
			'icon'     => 'fa fa-filter',
			'name'     => _l('custom_reports'),
			'position' => 35,
		]);
		$CI->app_menu->add_sidebar_children_item('custom-reports', [
			'slug'     => 'tasks-report-options',
			'name'     => _l('tasks_filter'),
			'href'     => admin_url('si_task_filters/tasks_report'),
			'position' => 5,
		]);
		$CI->app_menu->add_sidebar_children_item('custom-reports', [
			'slug'     => 'clients-report-options',
			'name'     => _l('tasks_filter_templates'),
			'href'     => admin_url('si_task_filters/list_filters'),
			'position' => 10,
		]);
	}
}
function si_task_filters_permissions()
{
	$capabilities = [];
	$capabilities['capabilities'] = [
		'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
		'create' => _l('permission_create'),
	];
    register_staff_capabilities('si_task_filters', $capabilities, _l('custom_reports'));
}
