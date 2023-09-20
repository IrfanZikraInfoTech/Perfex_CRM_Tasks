<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Mautic - World's Largest Open-Source Marketing Automation
Author: UHScripts
Author URI: http://www.uhscripts.com
Description: Cutting edge marketing automation empowered by open source technology
Version: 1.0.0
Requires at least: 2.3.*
 */
define('MAUTIC_MODULE_NAME', 'mauticmgmt');

require __DIR__ . '/vendor/autoload.php';

$CI = &get_instance();

hooks()->add_action('admin_init', 'mautic_module_init_menu_items');
hooks()->add_action('before_lead_added', 'add_lead_to_mautic', 10); //

/**
 * Register activation module hook
 */
register_activation_hook(MAUTIC_MODULE_NAME, 'mautic_module_activation_hook');

/**
 * Loads the module function helper
 */
$CI->load->helper(MAUTIC_MODULE_NAME . '/mautic');

function mautic_module_activation_hook()
{
    $CI = &get_instance();
    require_once __DIR__ . '/install.php';
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(MAUTIC_MODULE_NAME, [MAUTIC_MODULE_NAME]);

/**
 * Init backup module menu items in setup in admin_init hook
 * @return null
 */
function mautic_module_init_menu_items()
{
    /**
     * If the logged in user is administrator, add custom menu in Setup
     */

    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('mautic-menu', [
        'slug' => 'mautic',
        'name' => _l('mautic_menu'),
        'collapse' => true,
        'icon' => 'fa fa-key',
        'position' => 5,
    ]);

    $CI->app_menu->add_sidebar_children_item('mautic-menu', [

        'slug' => 'mautic-settings', // Required ID/slug UNIQUE for the child menu

        'name' => _l('mautic_settings'), // The name if the item

        'href' => admin_url('mauticmgmt/settings'),

    ]);

    $CI->app_menu->add_sidebar_children_item('mautic-menu', [

        'slug' => 'mautic-campaign-list', // Required ID/slug UNIQUE for the child menu

        'name' => _l('mautic_campaign_list'), // The name if the item

        'href' => admin_url('mauticmgmt/campaign/campaign_list'),

    ]);

    $CI->app_menu->add_sidebar_children_item('mautic-menu', [

        'slug' => 'mautic-add-campaign', // Required ID/slug UNIQUE for the child menu

        'name' => _l('mautic_contact_add_to_campaign'), // The name if the item

        'href' => admin_url('mauticmgmt/campaign/mautic_contact_add_to_campaign'),

    ]);

    function add_lead_to_mautic($data)
    {

        get_lead($data);

        return $data;
    }

}
