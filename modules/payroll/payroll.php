<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Module Name: Payroll
 * Description: A module for managing payroll calculations and records.
 * Version: 1.0.0
 * Requires at least: 2.3.*
*/
require(__DIR__ . '/vendor/autoload.php');

define('PAYROLL_MODULE_NAME', 'payroll');

hooks()->add_action('admin_init', 'payroll_permissions');

if (has_permission('payroll', '', 'admin')) {
hooks()->add_action('admin_init', 'payroll_init_menu_items');
}

// Register activation hook
register_activation_hook(PAYROLL_MODULE_NAME, 'payroll_module_activation_hook');


function payroll_init_menu_items()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('payroll', [
        'name' => 'Payroll', // The name of the item
        'position' => 5, // The menu position, see below for default positions
        'icon' => 'fa fa-money-bill-alt', // Font awesome icon
    ]);

    
    
    //Role Salary
    $CI->app_menu->add_sidebar_children_item('payroll', [
        'slug' => 'calculate', // Required ID/slug UNIQUE for the child menu
        'name' => 'Role Salary', // The name of the item
        'href' => admin_url('payroll/Role_salary'), // URL of the item
        'position' => 1, // The menu position
        'icon' => 'fa fa-calculator', // Font awesome icon
    ]);


    

    //Pay Slip
    $CI->app_menu->add_sidebar_children_item('payroll', [
            'slug' => 'settings', // Required ID/slug UNIQUE for the child menu
            'name' => 'Pay Slip', // The name of the item
            'href' => admin_url('payroll/pay_slip'), // URL of the item
            'position' => 3, // The menu position
            'icon' => 'fa fa-file-alt', // Font awesome icon
    ]);


        //Monthly Saction    
    $CI->app_menu->add_sidebar_children_item('payroll', [
        'slug' => 'settings', // Required ID/slug UNIQUE for the child menu
        'name' => 'Montly Section', // The name of the item
        'href' => admin_url('payroll/monthly_section'), // URL of the item
        'position' => 3, // The menu position
        'icon' => 'fa fa-file-invoice-dollar', // Font awesome icon
    ]);

    //Setting
    $CI->app_menu->add_sidebar_children_item('payroll', [
        'slug' => 'settings', // Required ID/slug UNIQUE for the child menu
        'name' => 'Payroll Settings', // The name of the item
        'href' => admin_url('payroll/Setting'), // URL of the item
        'position' => 3, // The menu position
        'icon' => 'fa fa-cog', // Font awesome icon
    ]);
    
        
}
function payroll_module_activation_hook() {
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

function payroll_permissions() {
	$capabilities = [];
	$capabilities['capabilities'] = [
		'admin' => 'Is Admin'
	];
	register_staff_capabilities('payroll', $capabilities, 'Payroll');
}