<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Module Name: Recruitment V.2 Custom
 * Description: A module for managing recruitment process including job postings, applications, interviews, and hiring.
 * Version: 1.0.0
 * Requires at least: 2.3.*
 */

$CI = &get_instance();

hooks()->add_action('admin_init', 'recruitment_portal_init_menu_items');

if (!$CI->db->table_exists(db_prefix() . '_rec_campaigns')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "_rec_campaigns` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `position` VARCHAR(255) NOT NULL,
        `description` TEXT,
        `start_date` DATE,
        `end_date` DATE,
        `status` TINYINT(1) DEFAULT 1,
        `salary` VARCHAR(10),
        `created_at` DATETIME,
        `updated_at` DATETIME
    )");
}

if (!$CI->db->table_exists(db_prefix() . '_rec_campaign_fields')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "_rec_campaign_fields` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `campaign_id` INT(11) NOT NULL,
        `fields_data` TEXT,
        `created_at` DATETIME,
        `updated_at` DATETIME
    )");
}

if (!$CI->db->table_exists(db_prefix() . '_rec_form_submissions')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "_rec_form_submissions` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `campaign_id` INT(11) NOT NULL,
        `form_data` TEXT NOT NULL,
        `resume` VARCHAR(255) NOT NULL,
        `created_at` DATETIME NOT NULL,
        status INT(1) DEFAULT 0,
        is_archived INT (1) DEFAULT 0
    )");
}

if (!$CI->db->table_exists(db_prefix() . '_rec_email_templates')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "_rec_email_templates` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `template_name` VARCHAR(255),
        `template_subject` VARCHAR(255),
        `template_body` TEXT,
        `created_at` DATETIME,
        `updated_at` DATETIME
    )");
}

if (!$CI->db->table_exists(db_prefix() . '_rec_campaign_email_templates')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "_rec_campaign_email_templates` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `campaign_id` INT(11) NOT NULL,
        `template_id` INT(11) NOT NULL,
        `created_at` DATETIME
    )");
}

if (!$CI->db->table_exists(db_prefix() . '_rec_form_templates')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "_rec_form_templates` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `template_name` VARCHAR(255),
        `fields_data` TEXT,
        `created_at` DATETIME,
        `updated_at` DATETIME
    )");
}


function recruitment_portal_init_menu_items()
{
    $CI = &get_instance();
    $CI->app_menu->add_sidebar_menu_item('recruitment_portal', [
        'name'     => 'Recruitment', // The name of the item
        'position' => 4, // The menu position, see below for default positions.
        'icon'     => 'fa fa-briefcase', // Font awesome icon
    ]);

    $CI->app_menu->add_sidebar_children_item('recruitment_portal', [
        'slug'     => 'campaigns', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Campaigns', // The name if the item
        'href'     => admin_url('recruitment_portal/campaigns'), // URL of the item
        'position' => 1, // The menu position
        'icon'     => 'fa fa-bullhorn', // Font awesome icon
    ]);

    $CI->app_menu->add_sidebar_children_item('recruitment_portal', [
        'slug'     => 'submissions', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Submissions', // The name if the item
        'href'     => admin_url('recruitment_portal/submissions'), // URL of the item
        'position' => 2, // The menu position
        'icon'     => 'fa fa-paper-plane', // Font awesome icon
    ]);

    $CI->app_menu->add_sidebar_children_item('recruitment_portal', [
        'slug'     => 'career', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Career', // The name if the item
        'href'     => base_url('career'), // URL of the item
        'position' => 3, // The menu position
        'icon'     => 'fa fa-graduation-cap', // Font awesome icon
    ]);

    $CI->app_menu->add_sidebar_children_item('recruitment_portal', [
        'slug'     => 'email-temps', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Templates', // The name if the item
        'href'     => admin_url('recruitment_portal/email_templates'), // URL of the item
        'position' => 3, // The menu position
        'icon'     => 'fa fa-envelope', // Font awesome icon
    ]);

}

?>
