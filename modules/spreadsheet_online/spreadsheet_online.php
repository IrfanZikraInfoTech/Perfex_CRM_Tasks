<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Spreadsheet Online
Description: A powerful spreadsheet editor that lets you do pretty much everything you can do with contemporary spreadsheet software like Excel.
Version: 1.0.5
Requires at least: 2.3.*
Author: GreenTech Solutions
Author URI: https://codecanyon.net/user/greentech_solutions
*/

define('SPREAD_ONLINE_MODULE_NAME', 'spreadsheet_online');
define('SPREAD_ONLINE_MODULE_UPLOAD_FOLDER', module_dir_path(SPREAD_ONLINE_MODULE_NAME, 'uploads'));
hooks()->add_action('app_admin_head', 'spreadsheet_online_add_head_component');
hooks()->add_action('app_admin_footer', 'spreadsheet_online_load_js');
hooks()->add_action('admin_init', 'spreadsheet_online_module_init_menu_items');
hooks()->add_action('customers_navigation_end', 'spreadsheet_online_module_init_client_menu_items');
hooks()->add_action('client_pt_footer_js','spreadsheet_online_client_foot_js');

// Hook related item
// Project
hooks()->add_action('after_project_member_list', 'init_project_item_relate_so');

//contract
hooks()->add_action('after_contract_content', 'init_contract_item_relate_so');
hooks()->add_action('header_contracthtml', 'init_contracthtml_css');
hooks()->add_action('footer_contracthtml_js', 'init_contracthtml_js');

// Proposal
hooks()->add_action('after_li_proposal_view', 'init_tab_proposal');
hooks()->add_action('after_tab_proposal_content', 'init_tab_proposal_content');

//Estimate
hooks()->add_action('after_li_estimate_view', 'init_tab_estimate');
hooks()->add_action('after_tab_estimate_content', 'init_tab_estimate_content');

// Invoice
hooks()->add_action('after_li_invoice_view', 'init_tab_invoice');
hooks()->add_action('after_tab_invoice_content', 'init_tab_invoice_content');


//Expense
hooks()->add_action('after_li_expense_view', 'init_tab_expense');
hooks()->add_action('after_tab_expense_content', 'init_tab_expense_content');

// Lead
hooks()->add_action('after_li_lead_view', 'init_tab_lead');
hooks()->add_action('after_tab_lead_content', 'init_tab_lead_content');

define('VERSION_SREADSHEET', 105);
define('folder', 'folder');
//email theme
register_merge_fields('spreadsheet_online/merge_fields/spreadsheet_share_merge_fields');

/**
* Register activation module hook
*/

register_activation_hook(SPREAD_ONLINE_MODULE_NAME, 'spreadsheet_online_module_activation_hook');
$CI = & get_instance();

$CI->load->helper(SPREAD_ONLINE_MODULE_NAME . '/Spreadsheet_online');

/**
 * spreadsheet online module activation hook
 */
function spreadsheet_online_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function spreadsheet_online_module_init_menu_items()
{
    $CI = &get_instance();
    
    $CI->app_menu->add_sidebar_menu_item('SPREADSHEET_ONLINE', [
        'name'     => _l('SPREADSHEET_ONLINE'),
        'icon'     => 'fa fa-file-text',  
        'href'     => admin_url('spreadsheet_online/manage'),
        'position' => 1,
    ]);

}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(SPREAD_ONLINE_MODULE_NAME, [SPREAD_ONLINE_MODULE_NAME]);

/**
* init add head component
*/
function spreadsheet_online_add_head_component(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];

    if(!(strpos($viewuri,'admin/spreadsheet_online/manage') === false) || !(strpos($viewuri,'admin/projects/view') === false)  ){
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/css/jquery.treetable.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/css/jquery.treetable.theme.default.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/css/screen.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/css/manage_style.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri,'admin/proposals') === false) || !(strpos($viewuri,'admin/estimates') === false) || !(strpos($viewuri,'admin/invoices') === false) || !(strpos($viewuri,'admin/expenses') === false) || !(strpos($viewuri,'admin/leads') === false)){
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/css/jquery.treetable.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/css/jquery.treetable.theme.default.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/css/screen.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/css/custom.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri,'admin/spreadsheet_online/new_file_view') === false) || !(strpos($viewuri,'admin/spreadsheet_online/file_view_share') === false)) {
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ComboTree/style.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';

        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/css/manage.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/iconfont.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/luckysheet.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/plugins.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/pluginsCss.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';

        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/iconCustom.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/luckysheet-cellFormat.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/luckysheet-core.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/luckysheet-print.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/luckysheet-protection.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/luckysheet-zoom.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/chartmix.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/spectrum.min.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/chartmix.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    }
}

/**
 * init add footer component
 */
function spreadsheet_online_load_js(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    

    if (!(strpos($viewuri,'admin/spreadsheet_online/manage') === false) ) {
        echo '<script>';
        echo 'var download_file = "' . _l('download') . '";';
        echo 'var create_file = "' . _l('create_file') . '";';
        echo 'var create_folder = "' . _l('create_folder') . '";';
        echo 'var edit = "' . _l('edit') . '";';
        echo '</script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/jquery-ui.min.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/jquery.treetable.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/js/manage.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/js/context_menu.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/luckysheet.umd.js').'?v=' . VERSION_SREADSHEET.'"></script>';

        echo '<script type="module" src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/excel.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/FileSaver.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script  src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/js/exports.js').'?v=' . VERSION_SREADSHEET.'"></script>';
    }
    if (!(strpos($viewuri,'admin/projects/view') === false)  || !(strpos($viewuri,'admin/estimates') === false) || !(strpos($viewuri,'admin/proposals') === false) || !(strpos($viewuri,'admin/invoices') === false) || !(strpos($viewuri,'admin/expenses') === false) || !(strpos($viewuri,'admin/leads') === false)) {

        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/js/manage.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/jquery-ui.min.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/jquery.treetable.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/js/relate_to.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/luckysheet.umd.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script type="module" src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/excel.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/FileSaver.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script  src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/js/exports.js').'?v=' . VERSION_SREADSHEET.'"></script>';
    }

    if (!(strpos($viewuri,'admin/spreadsheet_online/new_file_view') === false) || !(strpos($viewuri,'admin/spreadsheet_online/file_view_share') === false)) {

        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ComboTree/comboTreePlugin.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ComboTree/icontains.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/spectrum.min.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/plugin.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/luckysheet.umd.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/js/manage.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/vue.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/vuex.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/vuexx.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/index.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/echarts.min.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/chartmix.umd.min.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/FileSaver.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script  src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/excel.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script  src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/js/exports.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/js/upload_file.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/luckyexcel.js').'?v=' . VERSION_SREADSHEET.'"></script>';
    }

}
/**
 *  add menu item and js file to client
*/
function spreadsheet_online_module_init_client_menu_items()
{
    $CI = &get_instance();

    $viewuri = $_SERVER['REQUEST_URI'];

    if (!(strpos($viewuri,'spreadsheet_online/spreadsheet_online_client/file_view_share_related') === false)) {
        echo "<style>
                #luckysheet {
        height: 80% !important;
        left: 2px !important;
        top: 100px !important;
    }
    </style>";
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/css/custom.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/css/jquery.treetable.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/css/jquery.treetable.theme.default.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/css/screen.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/css/manage.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/iconfont.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/luckysheet.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/plugins.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/pluginsCss.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';

    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/iconCustom.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/luckysheet-cellFormat.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/luckysheet-print.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/luckysheet-protection.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/luckysheet-zoom.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/chartmix.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/spectrum.min.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/chartmix.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
}

if(is_client_logged_in()){
    if (!(strpos($viewuri,'spreadsheet_online/spreadsheet_online_client/file_view_share') === false) ) {
        echo "<style>
            #luckysheet {
        height: 80% !important;
        left: 2px !important;
        top: 170px !important;
    }
    </style>";
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/css/custom.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
}

if (!(strpos($viewuri,'spreadsheet_online/spreadsheet_online_client') === false)) {
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/css/jquery.treetable.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/css/jquery.treetable.theme.default.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/css/screen.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/css/manage.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/iconfont.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/luckysheet.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/plugins.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/pluginsCss.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';

    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/iconCustom.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/luckysheet-cellFormat.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/luckysheet-print.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/luckysheet-protection.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/luckysheet-zoom.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/chartmix.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/spectrum.min.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/css/chartmix.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/css/custom.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';

}

$client_id = get_client_user_id();
$CI->load->model('spreadsheet_online/spreadsheet_online_model');
$check_share = $CI->spreadsheet_online_model->get_my_folder_by_client_share_folder_view($client_id);
if($check_share){
    echo '
    <li class="customers-nav-item-Insurances-plan">
    <a href="'.site_url('spreadsheet_online/spreadsheet_online_client').'">'._l('my_share_folder').'</a>
    </li>
    ';
}
} 
}

/**
 * add element for footer portal 
 */
function spreadsheet_online_client_foot_js(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];

    if (!(strpos($viewuri,'spreadsheet_online/spreadsheet_online_client') === false)) {
       echo '<script>';
       echo 'var site_url = "' . site_url() . '";';
       echo 'var admin_url = "' . admin_url() . '";';
       echo 'var create_file = "' . _l('create_file') . '";';
       echo 'var create_folder = "' . _l('create_folder') . '";';
       echo 'var download_file = "' . _l('download') . '";';
       echo 'var edit = "' . _l('edit') . '";';

       echo '</script>';

       echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/jquery-ui.min.js').'?v=' . VERSION_SREADSHEET.'"></script>';
       echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/jquery.treetable.js').'?v=' . VERSION_SREADSHEET.'"></script>';
       echo '<script  src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/js/client_sheet.js').'?v=' . VERSION_SREADSHEET.'"></script>';
       echo '<script  src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/js/context_menu_client.js').'?v=' . VERSION_SREADSHEET.'"></script>';
       echo '<script type="module" src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/excel.js').'?v=' . VERSION_SREADSHEET.'"></script>';
       echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/FileSaver.js').'?v=' . VERSION_SREADSHEET.'"></script>';
       echo '<script  src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/js/exports.js').'?v=' . VERSION_SREADSHEET.'"></script>';
   }

   if(!(strpos($viewuri,'spreadsheet_online/spreadsheet_online_client/file_view_share') === false)){
    echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ComboTree/comboTreePlugin.js').'?v=' . VERSION_SREADSHEET.'"></script>';
    echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ComboTree/icontains.js').'?v=' . VERSION_SREADSHEET.'"></script>';
    echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/spectrum.min.js').'?v=' . VERSION_SREADSHEET.'"></script>';
    echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/plugin.js').'?v=' . VERSION_SREADSHEET.'"></script>';
    echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/luckysheet.umd.js').'?v=' . VERSION_SREADSHEET.'"></script>';
    echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/vue.js').'?v=' . VERSION_SREADSHEET.'"></script>';
    echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/vuex.js').'?v=' . VERSION_SREADSHEET.'"></script>';
    echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/vuexx.js').'?v=' . VERSION_SREADSHEET.'"></script>';
    echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/index.js').'?v=' . VERSION_SREADSHEET.'"></script>';
    echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/echarts.min.js').'?v=' . VERSION_SREADSHEET.'"></script>';
    echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/chartmix.umd.min.js').'?v=' . VERSION_SREADSHEET.'"></script>';
    echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/luckyexcel.js').'?v=' . VERSION_SREADSHEET.'"></script>';
    echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/FileSaver.js').'?v=' . VERSION_SREADSHEET.'"></script>';
    echo '<script  src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/excel.js').'?v=' . VERSION_SREADSHEET.'"></script>';
    echo '<script  src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/js/exports.js').'?v=' . VERSION_SREADSHEET.'"></script>';
}
}


function init_contracthtml_css(){

  if(is_staff_logged_in()){
    $viewuri = $_SERVER['REQUEST_URI'];
    if(!(strpos($viewuri,'/contract/') === false)){
      echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/css/jquery.treetable.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
      echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/css/jquery.treetable.theme.default.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
      echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/css/screen.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
      echo '<link href="' . module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/css/manage.css') . '?v=' . VERSION_SREADSHEET. '"  rel="stylesheet" type="text/css" />';
  }
}
}
/**
 * init_contracthtml_js
 * @return [type] [description]
 */
function init_contracthtml_js(){
  $viewuri = $_SERVER['REQUEST_URI'];
  if(is_staff_logged_in()){
    if(!(strpos($viewuri,'/contract') === false)){
        echo '<script>';
        echo 'var site_url = "' . site_url() . '";';
        echo 'var admin_url = "' . admin_url() . '";';
        echo '</script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/js/contract_related.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/jquery-ui.min.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/ludo-jquery-treetable/jquery.treetable.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/luckysheet/js/luckysheet.umd.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script type="module" src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/excel.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/plugins/FileSaver.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script  src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/js/exports.js').'?v=' . VERSION_SREADSHEET.'"></script>';
        echo '<script src="'.module_dir_url(SPREAD_ONLINE_MODULE_NAME, 'assets/js/relate_to.js').'?v=' . VERSION_SREADSHEET.'"></script>';
    }
}
}
/**
 * Initializes the project item relate.
 *
 * @param        $project  The project
 */
function init_project_item_relate_so($project){
  $CI = &get_instance();
  if( is_admin() || is_staff_logged_in()){
    $CI->load->model('spreadsheet_online/spreadsheet_online_model');
    $folder_my_tree = $CI->spreadsheet_online_model->tree_my_folder_related('project',$project->id);
    require "modules/spreadsheet_online/views/view_related_general.php";
}
}

/**
 * Initializes the tab contracthtml.
 */
function init_tab_proposal() {
  echo '<div class="modal fade" id="AddFolderModal" role="dialog">
  <div class="modal-dialog">
  <div class="modal-content">
  <div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title add-new">'. _l('view') .'</h4>
  <h4 class="modal-title update-new hide">'. _l('update_folder') .'</h4>
  </div>
  '. form_open_multipart(admin_url('spreadsheet_online/add_edit_folder'),array('id'=>'add-edit-folder-form')).'
  '. form_hidden('id') .'
  <div class="modal-body">
  <div class="row">
  <div class="col-md-12 col-sm-12">
  '. render_input('name', 'name_folder').'
  </div>
  </div>
  '. form_hidden('parent_id') .'             
  </div>
  <div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">'. _l('close') .'</button>
  </div>
  '. form_close() .'   
  </div>
  </div>
  </div>';
  if(is_staff_logged_in()){
    echo '<li role="presentation" class="tab-separator">
    <a href="#spreadsheet_online" aria-controls="spreadsheet_online" role="tab" data-toggle="tab">
    ' . _l('spreadsheet_online') . '
    </a>
    </li>';
}
}

/**
 * Initializes the tab contracthtml.
 */
function init_tab_proposal_content($proposal) {
  $CI = &get_instance();
  if(is_staff_logged_in()){
    $CI->load->model('spreadsheet_online/spreadsheet_online_model');
    echo '<div role="tabpanel" class="tab-pane" id="spreadsheet_online">';

    $folder_my_tree = $CI->spreadsheet_online_model->tree_my_folder_related('proposal', $proposal->id);
    require "modules/spreadsheet_online/views/view_related_general.php";
    echo '</div>';
}
}


/**
 * Initializes the tab contracthtml.
 */
function init_tab_estimate() {
  echo '<div class="modal fade" id="AddFolderModal" role="dialog">
  <div class="modal-dialog">
  <div class="modal-content">
  <div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title add-new">'. _l('view') .'</h4>
  <h4 class="modal-title update-new hide">'. _l('update_folder') .'</h4>
  </div>
  '. form_open_multipart(admin_url('spreadsheet_online/add_edit_folder'),array('id'=>'add-edit-folder-form')).'
  '. form_hidden('id') .'
  <div class="modal-body">
  <div class="row">
  <div class="col-md-12 col-sm-12">
  '. render_input('name', 'name_folder').'
  </div>
  </div>
  '. form_hidden('parent_id') .'             
  </div>
  <div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">'. _l('close') .'</button>
  </div>
  '. form_close() .'   
  </div>
  </div>
  </div>';
  if(is_staff_logged_in()){
    echo '<li role="presentation" class="tab-separator">
    <a href="#spreadsheet_online" aria-controls="spreadsheet_online" role="tab" data-toggle="tab">
    ' . _l('spreadsheet_online') . '
    </a>
    </li>';
}
}

/**
 * Initializes the tab contracthtml.
 */
function init_tab_estimate_content($estimate) {
  $CI = &get_instance();
  if(is_staff_logged_in()){
    $CI->load->model('spreadsheet_online/spreadsheet_online_model');
    echo '<div role="tabpanel" class="tab-pane" id="spreadsheet_online">';

    $folder_my_tree = $CI->spreadsheet_online_model->tree_my_folder_related('estimate', $estimate->id);
    require "modules/spreadsheet_online/views/view_related_general.php";
    echo '</div>';
}
}

/**
 * Initializes the contract item relate so.
 *
 * @param      <type>  $contract  The contract
 */
function init_contract_item_relate_so($contract){

  $CI = &get_instance();
  if(is_staff_logged_in()){
    $CI->load->model('spreadsheet_online/spreadsheet_online_model');
    echo '<div class="panel_s mtop20">
    <div class="panel-body tc-content padding-30 contract-html-csnontent">';

    $folder_my_tree = $CI->spreadsheet_online_model->tree_my_folder_related('contract', $contract->id);
    require "modules/spreadsheet_online/views/view_related_general.php";
    echo '</div></div>';
}
echo '<div class="modal fade" id="AddFolderModal" role="dialog">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title add-new">'. _l('view') .'</h4>
<h4 class="modal-title update-new hide">'. _l('update_folder') .'</h4>
</div>
'. form_open_multipart(admin_url('spreadsheet_online/add_edit_folder'),array('id'=>'add-edit-folder-form')).'
'. form_hidden('id') .'
<div class="modal-body">
<div class="row">
<div class="col-md-12 col-sm-12">
'. render_input('name', 'name_folder').'
</div>
</div>
'. form_hidden('parent_id') .'             
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">'. _l('close') .'</button>
</div>
'. form_close() .'   
</div>
</div>
</div>';
}


/**
 * Initializes the tab contracthtml.
 */
function init_tab_invoice() {
  echo '<div class="modal fade" id="AddFolderModal" role="dialog">
  <div class="modal-dialog">
  <div class="modal-content">
  <div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title add-new">'. _l('view') .'</h4>
  <h4 class="modal-title update-new hide">'. _l('update_folder') .'</h4>
  </div>
  '. form_open_multipart(admin_url('spreadsheet_online/add_edit_folder'),array('id'=>'add-edit-folder-form')).'
  '. form_hidden('id') .'
  <div class="modal-body">
  <div class="row">
  <div class="col-md-12 col-sm-12">
  '. render_input('name', 'name_folder').'
  </div>
  </div>
  '. form_hidden('parent_id') .'             
  </div>
  <div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">'. _l('close') .'</button>
  </div>
  '. form_close() .'   
  </div>
  </div>
  </div>';
  if(is_staff_logged_in()){
    echo '<li role="presentation" class="tab-separator">
    <a href="#spreadsheet_online" aria-controls="spreadsheet_online" role="tab" data-toggle="tab">
    ' . _l('spreadsheet_online') . '
    </a>
    </li>';
}
}

/**
 * Initializes the tab contracthtml.
 */
function init_tab_invoice_content($invoice) {
  $CI = &get_instance();
  if(is_staff_logged_in()){
    $CI->load->model('spreadsheet_online/spreadsheet_online_model');
    echo '<div role="tabpanel" class="tab-pane" id="spreadsheet_online">';

    $folder_my_tree = $CI->spreadsheet_online_model->tree_my_folder_related('invoice', $invoice->id);
    require "modules/spreadsheet_online/views/view_related_general.php";
    echo '</div>';
}
}


/**
 * Initializes the tab contracthtml.
 */
function init_tab_expense() {
  echo '<div class="modal fade" id="AddFolderModal" role="dialog">
  <div class="modal-dialog">
  <div class="modal-content">
  <div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title add-new">'. _l('view') .'</h4>
  <h4 class="modal-title update-new hide">'. _l('update_folder') .'</h4>
  </div>
  '. form_open_multipart(admin_url('spreadsheet_online/add_edit_folder'),array('id'=>'add-edit-folder-form')).'
  '. form_hidden('id') .'
  <div class="modal-body">
  <div class="row">
  <div class="col-md-12 col-sm-12">
  '. render_input('name', 'name_folder').'
  </div>
  </div>
  '. form_hidden('parent_id') .'             
  </div>
  <div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">'. _l('close') .'</button>
  </div>
  '. form_close() .'   
  </div>
  </div>
  </div>';
  if(is_staff_logged_in()){
    echo '<li role="presentation" class="tab-separator">
    <a href="#spreadsheet_online" aria-controls="spreadsheet_online" role="tab" data-toggle="tab">
    ' . _l('spreadsheet_online') . '  </a>
    </li>';
}
}

/**
 * Initializes the tab contracthtml.
 */
function init_tab_expense_content($expense) {
  $CI = &get_instance();
  if(is_staff_logged_in()){
    $CI->load->model('spreadsheet_online/spreadsheet_online_model');
    echo '<div role="tabpanel" class="tab-pane" id="spreadsheet_online">';

    $folder_my_tree = $CI->spreadsheet_online_model->tree_my_folder_related('expense', $expense->id);
    require "modules/spreadsheet_online/views/view_related_general.php";
    echo '</div>';
}
}


/**
 * Initializes the tab contracthtml.
 */
function init_tab_lead() {
  echo '<div class="modal fade" id="AddFolderModal" role="dialog">
  <div class="modal-dialog">
  <div class="modal-content">
  <div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title add-new">'. _l('view') .'</h4>
  <h4 class="modal-title update-new hide">'. _l('update_folder') .'</h4>
  </div>
  '. form_open_multipart(admin_url('spreadsheet_online/add_edit_folder'),array('id'=>'add-edit-folder-form')).'
  '. form_hidden('id') .'
  <div class="modal-body">
  <div class="row">
  <div class="col-md-12 col-sm-12">
  '. render_input('name', 'name_folder').'
  </div>
  </div>
  '. form_hidden('parent_id') .'             
  </div>
  <div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">'. _l('close') .'</button>
  </div>
  '. form_close() .'   
  </div>
  </div>
  </div>';
  if(is_staff_logged_in()){
    echo '<li role="presentation" class="tab-separator">
    <a href="#spreadsheet_online" aria-controls="spreadsheet_online" role="tab" data-toggle="tab">
    ' . _l('spreadsheet_online') . '
    </a>
    </li>';
}
}

/**
 * Initializes the tab contracthtml.
 */
function init_tab_lead_content($lead) {
  $CI = &get_instance();
  if(is_staff_logged_in()){
    $CI->load->model('spreadsheet_online/spreadsheet_online_model');
    echo '<div role="tabpanel" class="tab-pane" id="spreadsheet_online">';

    $folder_my_tree = $CI->spreadsheet_online_model->tree_my_folder_related('lead', $lead->id);
    require "modules/spreadsheet_online/views/view_related_general.php";
    echo '</div>';
}
}