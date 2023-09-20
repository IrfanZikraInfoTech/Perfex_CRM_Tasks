<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: AI Text
Description: Description of your module
Version: 1.0.0
Requires at least: 2.3.*
*/

define('AI_MODULE_NAME', 'ai_text');

$CI = &get_instance();

/**
 * Init module functionality
 *
 * This function is triggered in admin area
 */

$CI->load->model('ai_text/ai_text_form_model');

/**
 * Init admin menu items
 */

hooks()->add_action('admin_init','ai_text_init_menu_items_');

// Register activation hook
register_activation_hook(AI_MODULE_NAME, 'ai_module_activation_hook');

function ai_text_init_menu_items_()
{
    // Menu items code...
    $CI = &get_instance();
    $CI->app_menu->add_sidebar_menu_item('ai_text', [
        'name'     => _l('AI Text'), // The name of the menu item
        'position' =>  4, // The position in the menu
        'icon'     => 'fa-regular fa-file-lines', // The FontAwesome icon for the menu item
    ]);

    
    $CI->app_menu->add_sidebar_children_item('ai_text', [
        'slug'     => 'generatetext', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Generate Text', // The name of the item
        'href'     =>  admin_url('ai_text/display_data'), // URL of the item
        'position' =>  1, // The menu position
        'icon'     => 'fa-regular fa-pen-to-square', // Font awesome icon
    ]);

    $CI->app_menu->add_sidebar_children_item('ai_text', [
        'slug'     => 'image', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Generate Image', // The name of the item
        'href'     =>  admin_url('ai_text/ai_image'), // URL of the item
        'position' =>   2, // The menu position
        'icon'     => 'fa-regular fa-images', // Font awesome icon
    ]);

    $CI->app_menu->add_sidebar_children_item('ai_text', [
        'slug'     => 'manage', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Generated Data', // The name of the item
        'href'     =>  admin_url('ai_text/display_gen_data'), // URL of the item
        'position' =>  3, // The menu position
        'icon'     => 'fa-solid fa-box-archive', // Font awesome icon
    ]);

    $CI->app_menu->add_sidebar_children_item('ai_text', [
        'slug'     => 'manage', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Generated Images', // The name of the item
        'href'     =>  admin_url('ai_text/display_images'), // URL of the item
        'position' =>  4, // The menu position
        'icon'     => 'fa-regular fa-image', // Font awesome icon
    ]);
    
    $CI->app_menu->add_sidebar_children_item('ai_text', [
        'slug'     => 'manage', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Manage Templates', // The name of the item
        'href'     =>  admin_url('ai_text/manage_template'), // URL of the item
        'position' =>   5, // The menu position
        'icon'     => 'fa fa-cog', // Font awesome icon
    ]);

}

function ai_module_activation_hook() {
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}