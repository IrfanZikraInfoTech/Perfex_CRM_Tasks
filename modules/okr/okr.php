<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: OKR
Description: OKR (Objectives and Key Results) is a goal system. It is a simple tool to create alignment and engagement around measurable goals.
Version: 1.0.2
Requires at least: 2.3.*
Author: GreenTech Solutions
Author URI: https://codecanyon.net/user/greentech_solutions
*/

define('OKR_MODULE_NAME', 'okr');
define('OKR_MODULE_UPLOAD_FOLDER', module_dir_path(OKR_MODULE_NAME, 'uploads'));
define('OKR_MODULE_IMAGE_OKR', module_dir_path(OKR_MODULE_NAME, 'assets/images'));
hooks()->add_action('admin_init', 'okr_permissions');
hooks()->add_action('app_admin_head', 'okr_add_head_component');
hooks()->add_action('app_admin_footer', 'okr_load_js');
hooks()->add_action('admin_init', 'okr_module_init_menu_items');
define('OKR_PATH', 'modules/okr/uploads/');
define('OKR_IMAGE_PATH', 'modules/okr/assets/image');
define('OKR_APP_PATH', 'modules/okr/');
//Okrs detail task
hooks()->add_action('task_related_to_select', 'okrs_related_to_select');
hooks()->add_filter('before_return_relation_values', 'okrs_relation_values', 10, 2);
hooks()->add_filter('before_return_relation_data', 'okrs_relation_data', 10, 4);
hooks()->add_filter('tasks_table_row_data', 'okrs_add_table_row', 10, 3);
define('VERSION_OKR', 102);

/**
* Register activation module hook
*/

register_activation_hook(OKR_MODULE_NAME, 'okr_module_activation_hook');
$CI = & get_instance();

$CI->load->helper(OKR_MODULE_NAME . '/Okr');
/**
 * team password module activation hook
 */
function okr_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(OKR_MODULE_NAME, [OKR_MODULE_NAME]);

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function okr_module_init_menu_items()
{
    $CI = &get_instance();
    
    if(has_permission('okr','','view') || is_admin() || has_permission('okr','','view_own')){
        $CI->app_menu->add_sidebar_menu_item('OKR', [
                'name'     => _l('okr'),
                'icon'     => 'fa fa-crosshairs',                
                'position' => 30,
        ]);

        $CI->app_menu->add_sidebar_children_item('OKR', [
                'slug'     => 'okr_dashboard',
                'name'     => _l('okr_dashboard'),
                'icon'     => 'fa fa-pie-chart',
                'href'     => admin_url('okr/dashboard'),
                'position' => 1,
        ]);

        $CI->app_menu->add_sidebar_children_item('OKR', [
                'slug'     => 'okr_report',
                'name'     => _l('okr_report'),
                'icon'     => 'fa fa-signal',
                'href'     => admin_url('okr/report'),
                'position' => 2,
        ]);

        $CI->app_menu->add_sidebar_children_item('OKR', [
                'slug'     => 'okrs',
                'name'     => _l('okrs'),
                'icon'     => 'fa fa-crosshairs',
                'href'     => admin_url('okr/okrs'),
                'position' => 3,
        ]);

        $CI->app_menu->add_sidebar_children_item('OKR', [
                'slug'     => 'okr_checkin',
                'name'     => _l('okr_checkin'),
                'icon'     => 'fa fa-calendar-check-o',
                'href'     => admin_url('okr/checkin'),
                'position' => 4,
        ]);

        
    }
    if(is_admin()){
        $CI->app_menu->add_sidebar_children_item('OKR', [
                'slug'     => 'okr_setting',
                'name'     => _l('okr_setting'),
                'icon'     => 'fa fa-cog',
                'href'     => admin_url('okr/setting'),
                'position' => 5,
        ]);
    }
            
}

/**
* init add head component
*/
function okr_add_head_component(){
    render_js_variables();
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if(!(strpos($viewuri,'admin/okr') === false)){
        echo '<link href="' . module_dir_url(OKR_MODULE_NAME, 'assets/css/okr.css') . '?v=' . VERSION_OKR. '"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri,'admin/okr/okrs') === false) || !(strpos($viewuri,'admin/okr/checkin') === false) || !(strpos($viewuri,'admin/okr/checkin_detailt') === false) || !(strpos($viewuri,'/admin/okr/show_detail_node' ) === false)){

        echo '<link href="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/dist/themes/default/style.min.css') . '?v=' . VERSION_OKR. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/dist/css/jquery.treegrid.css') . '?v=' . VERSION_OKR. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(OKR_MODULE_NAME, 'assets/css/okrs_display.css') . '?v=' . VERSION_OKR. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/Chart/CSS/jHTree.css') . '?v=' . VERSION_OKR. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/Chart/Themes/lightness/jquery-ui-1.10.4.custom.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/rate.min.css') . '?v=' . VERSION_OKR. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/OrgChart-master/jquery.orgchart.css') . '?v=' . VERSION_OKR. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/jquery.lineProgressbar.css') . '?v=' . VERSION_OKR. '"  rel="stylesheet" type="text/css" />';

    }
    if (!(strpos($viewuri,'/admin/okr/checkin_detailt') === false) || !(strpos($viewuri,'/admin/okr/view_details') === false)  || !(strpos($viewuri,'/admin/okr/show_detail_node' ) === false)) {
        echo '<link href="' . module_dir_url(OKR_MODULE_NAME, 'assets/css/highcharts.css') . '?v=' . VERSION_OKR. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(OKR_MODULE_NAME, 'assets/css/check_in_detailt.css') . '?v=' . VERSION_OKR. '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/jquery.lineProgressbar.css') . '?v=' . VERSION_OKR. '"  rel="stylesheet" type="text/css" />';

    }
    if (!(strpos($viewuri,'/admin/okr/dashboard') === false)) {
        echo '<link href="' . module_dir_url(OKR_MODULE_NAME, 'assets/css/highcharts.css') . '?v=' . VERSION_OKR. '"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri,'/admin/okr/checkin_detailt') === false)) {
        echo '<link href="' . module_dir_url(OKR_MODULE_NAME, 'assets/css/overide.css') . '?v=' . VERSION_OKR. '"  rel="stylesheet" type="text/css" />';
    }
}
/**
 * init add footer component
 */
function okr_load_js(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if (!(strpos($viewuri,'/admin/okr') === false)) {
        echo '<script src="'.module_dir_url(OKR_MODULE_NAME, 'assets/js/okr.js').'?v=' . VERSION_OKR.'"></script>';
        echo '<script src="'.module_dir_url(OKR_MODULE_NAME, 'assets/plugin/jquery-circle-progress/circle-progress.min.js').'?v=' . VERSION_OKR.'"></script>';
    }
    if (!(strpos($viewuri,'/admin/okr/setting') === false)) {
        echo '<script src="'.module_dir_url(OKR_MODULE_NAME, 'assets/js/setting/setting.js').'?v=' . VERSION_OKR.'"></script>';
    }
    if (!(strpos($viewuri,'/admin/okr/okrs') === false) || !(strpos($viewuri,'admin/okr/checkin') === false) || !(strpos($viewuri,'/admin/okr/checkin_detailt') === false) || !(strpos($viewuri,'/admin/okr/show_detail_node' ) === false) ) {
        echo '<script src="'.module_dir_url(OKR_MODULE_NAME, 'assets/plugin/dist/jstree.min.js').'"></script>';
        echo '<script src="'.module_dir_url(OKR_MODULE_NAME, 'assets/plugin/dist/js/jquery.treegrid.min.js').'"></script>';
        echo '<script src="'.module_dir_url(OKR_MODULE_NAME, 'assets/plugin/paging.js').'"></script>';
        echo '<script src="'.module_dir_url(OKR_MODULE_NAME, 'assets/plugin/Chart/js/jQuery.jHTree.js').'"></script>';
        echo '<script src="'.module_dir_url(OKR_MODULE_NAME, 'assets/plugin/demo.js').'"></script>';
        echo '<script src="'.module_dir_url(OKR_MODULE_NAME, 'assets/plugin/jquery.rate.min.js').'"></script>';
        echo '<script src="'.module_dir_url(OKR_MODULE_NAME, 'assets/plugin/OrgChart-master/jquery.orgchart.js').'"></script>';
    }
    if (!(strpos($viewuri,'/admin/okr/checkin_detailt') === false) || !(strpos($viewuri,'/admin/okr/view_details' ) === false) || !(strpos($viewuri,'/admin/okr/show_detail_node' ) === false)) {
        echo '<script src="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/highcharts/highcharts-3d.js') . '"></script>';
        echo '<script src="'.module_dir_url(OKR_MODULE_NAME, 'assets/plugin/jquery.lineProgressbar.js').'"></script>';
    }

    if (!(strpos($viewuri,'/admin/okr/dashboard') === false)) {
        echo '<script src="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(OKR_MODULE_NAME, 'assets/plugin/highcharts/highcharts-3d.js') . '"></script>';
    }
}

/**
 * okr permissions
 */
function okr_permissions() {
    $capabilities = [];
    $capabilities['capabilities'] = [
        'view_own'   => _l('permission_view'),
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('okr', $capabilities, _l('okr'));
}

/**
 * task related to select
 * @param  string $value 
 * @return string        
 */
function okrs_related_to_select($value)
{

    $selected = '';
    if($value == 'okrs'){
        $selected = 'selected';
    }
    echo "<option value='okrs' ".$selected.">".
                               _l('okrs')."
                           </option>";

}

/**
 * okrs detail relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function okrs_relation_values($values, $relation)
{

    if ($values['type'] == 'okrs') {
        if (is_array($relation)) {
            $values['id']   = $relation['id'];
            $values['name'] = $values['your_target'];
        } else {
            $values['id']   = $relation->id;
            $values['name'] = $relation->your_target;
        }
        $values['link'] = admin_url('okr/show_detail_node/' . $values['id']);
    }

    return $values;
}

/**
 * okrs detail relation data
 * @param  array $data   
 * @param  string $type   
 * @param  id $rel_id 
 * @param  array $q      
 * @return array         
 */
function okrs_relation_data($data, $type, $rel_id, $q)
{

    $CI = &get_instance();
    $CI->load->model('okr/okr_model');

    if ($type == 'okrs') {
        if ($rel_id != '') {
            $data = $CI->okr_model->get_okrs($rel_id);
        } else {
            $data   = [];
        }
    }
    return $data;
}


/**
 * okrs add table row
 * @param  string $row  
 * @param  string $aRow 
 * @return [type]       
 */
function okrs_add_table_row($row ,$aRow)
{

    $CI = &get_instance();
    $CI->load->model('okr/okr_model');

    if($aRow['rel_type'] == 'okrs'){
        $okrs = $CI->okr_model->get_okrs($aRow['rel_id']);
           if ($okrs) {

                $str = '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . admin_url('okr/show_detail_node/' . $okrs->id) . '">' . $okrs->your_target . '</a><br />';

                $row[2] =  $row[2].$str;
            }

    }

    return $row;
}