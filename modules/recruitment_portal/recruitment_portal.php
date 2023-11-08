<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Module Name: Recruitment V.2 Custom
 * Description: A module for managing recruitment process including job postings, applications, interviews, and hiring.
 * Version: 1.0.0
 * Requires at least: 2.3.*
 */

$CI = &get_instance();

$CI->load->model('recruitment_portal/recruitment_portal_model');

hooks()->add_action('admin_init', 'recruitment_portal_permissions');

if(has_permission('recruitment_portal', '', 'admin') ||  has_staff_under() || (array_key_exists('staff_user_id', $_SESSION) && $_SESSION['staff_user_id'] == "20") || (array_key_exists('staff_user_id', $_SESSION) && ($CI->recruitment_portal_model->get_viewable_campaigns_count($_SESSION['staff_user_id']) > 0))){
    hooks()->add_action('admin_init', 'recruitment_portal_init_menu_items');
}


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
        `updated_at` DATETIME,
        `detailed_description` text null,
        `job_type` text null,
        `experience` text null,
        `skills_required` text null,
        `camp_tag` text null
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
        `status` INT(1) DEFAULT 0,
        `is_archived` INT (1) DEFAULT 0,
        `is_viewed` INT (1) DEFAULT 0,
        `is_favorite` INT (1) DEFAULT 0,
        `email_message_id` text null
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

if (!$CI->db->table_exists(db_prefix() . '_rec_submission_messages')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "_rec_submission_messages` (
        `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `submission_id` INT(11) NOT NULL,
        `subject` TEXT NOT NULL,
        `message` TEXT NOT NULL,
        `sent_by` ENUM('admin', 'candidate') NOT NULL,
        `created_at` TIMESTAMP NOT NULL
    )");
}

if (!$CI->db->table_exists(db_prefix() . '_rec_campaign_permissions')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "_rec_campaign_permissions` (
        `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `staff_id` INT(11) NOT NULL,
        `campaign_id` INT(11) NOT NULL,
        `can_view` TINYINT(1) NOT NULL DEFAULT 0,
        `can_edit` TINYINT(1) NOT NULL DEFAULT 0,
        `can_act` TINYINT(1) NOT NULL DEFAULT 0
    )");
}

if (!$CI->db->table_exists(db_prefix() . '_rec_notes')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "_rec_notes` (
        `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `submission_id` INT(11) NOT NULL,
        `admin_id` INT(11) NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `body` TEXT NOT NULL,
        `created_at` TIMESTAMP NOT NULL
    )");
}
if (!$CI->db->table_exists(db_prefix() . '_rec_color_scheme')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "_rec_color_scheme` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `background_color` VARCHAR(100) NOT NULL,
        `button_color` VARCHAR(100) NOT NULL,
        `activate_color` INT NOT NULL DEFAULT '0'
    )");
}
if (!$CI->db->table_exists(db_prefix() . 'requisition_form')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "requisition_form` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `staff_id` INT(11) UNSIGNED NOT NULL,
        `department_id` INT(11) UNSIGNED NOT NULL,
        `job_title` VARCHAR(255) NOT NULL,
        `position_type` VARCHAR(100) NOT NULL,
        `employment_type` VARCHAR(100) NOT NULL,
        `expected_start_date` DATE NOT NULL,
        `experience` TEXT NOT NULL,
        `reason_for_requisition` TEXT NOT NULL,
        `duties_and_responsibilities` TEXT NOT NULL,
        `qualifications` TEXT NOT NULL,
        `work_schedule` TEXT NOT NULL,
        `salary` VARCHAR(100) NOT NULL,
        `additional_info`TEXT NOT NULL,
        `status` VARCHAR(100) NOT NULL DEFAULT 'Pending' 

    ) ");
}




function addCustomRoutes() {
    $routeFilePath = FCPATH . 'application/config/routes.php'; 

    if (is_writable($routeFilePath)) {

        $customRoutes = [
            "\$route['career'] = 'admin/recruitment_portal/career';",
            "\$route['career/apply/(:num)'] = 'admin/recruitment_portal/apply/\$1';",
            "\$route['career/view/(:num)'] = 'admin/recruitment_portal/view/\$1';",
            // "\$route['default_controller'] = 'clients';",
            // "\$route['404_override'] = '';",
            // "\$route['translate_uri_dashes'] = false;"
        ];

        $content = file_get_contents($routeFilePath);
        $content .= "\n// Custom Routes for MyModule\n" . implode("\n", $customRoutes);

        if (file_put_contents($routeFilePath, $content) !== false) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}




function recruitment_portal_init_menu_items()
{
    $CI = &get_instance();
    $CI->app_menu->add_sidebar_menu_item('recruitment_portal', [
        'name'     => 'Recruitment', // The name of the item
        'position' => 3, // The menu position, see below for default positions.
        'icon'     => 'fa fa-briefcase', // Font awesome icon
    ]);
    if(has_permission('recruitment_portal', '', 'admin')){
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
}
if (has_permission('recruitment_portal', '', 'admin') || has_staff_under()) {
    $CI->app_menu->add_sidebar_children_item('recruitment_portal', [
        'slug'     => 'Requisition_Form', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Requisition Form', // The name if the item
        'href'     => admin_url('recruitment_portal/requisition_form'), // URL of the item
        'position' => 2, // The menu position
        'icon'     => 'fa fa-address-book', // Font awesome icon
    ]);
}
if (has_permission('recruitment_portal', '', 'admin')) {
    $CI->app_menu->add_sidebar_children_item('recruitment_portal', [
        'slug'     => 'All_Requisition_Form', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Req Submissions', // The name if the item
        'href'     => admin_url('recruitment_portal/all_requisition_form'), // URL of the item
        'position' => 2, // The menu position
        'icon'     => 'fa fa-address-book', // Font awesome icon
    ]);
}
    if(has_permission('recruitment_portal', '', 'admin')){
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
        $CI->app_menu->add_sidebar_children_item('recruitment_portal', [
            'slug'     => 'color', // Unique slug for the child menu
            'name'     => 'Theme Color', // The name of the item
            'href'     => admin_url('recruitment_portal/color'), // URL of the item
            'position' => 4, // Menu position
            'icon'     => 'fa fa-paint-brush', // Font awesome icon
        ]);
    }
    else if(array_key_exists('staff_user_id', $_SESSION) && $_SESSION['staff_user_id'] == "20"){
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
        $CI->app_menu->add_sidebar_children_item('recruitment_portal', [
            'slug'     => 'color', // Unique slug for the child menu
            'name'     => 'Theme Color', // The name of the item
            'href'     => admin_url('recruitment_portal/color'), // URL of the item
            'position' => 4, // Menu position
            'icon'     => 'fa fa-paint-brush', // Font awesome icon
        ]);
    }

    

        // Dynamic COlor work
    if(has_permission('recruitment_portal', '', 'admin')){
        
    }
    // if(has_permission('recruitment_portal', '', 'admin')){
    //     $CI->app_menu->add_sidebar_children_item('recruitment_portal', [
    //         'slug'     => 'colorSetting', // Unique slug for the child menu
    //         'name'     => 'Color Setting', // The name of the item
    //         'href'     => admin_url('recruitment_portal/colorSetting'), // URL of the item
    //         'position' => 4, // Menu position
    //         'icon'     => 'fa fa-cog', // Font awesome icon
    //     ]);
    // }
}

function recruitment_portal_permissions() {
	$capabilities = [];
	$capabilities['capabilities'] = [
		'admin' => 'Is Admin'
	];
	register_staff_capabilities('recruitment_portal', $capabilities, 'Recruitment Portal');
}


?>
