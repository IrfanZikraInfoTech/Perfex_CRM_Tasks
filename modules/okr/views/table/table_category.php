<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [ 
    'id',
    'category',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'okr_setting_category';
$join         = [];
$where = [];
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

    $row = [];
    $row[] = $aRow['category'];
    $option = '';
    
    if(has_permission('okr','','edit') || is_admin()){ 
    $option .= '<a href="#" class="btn btn-default btn-icon" data-id="'.$aRow['id'].'" data-category="'.$aRow['category'].'" onclick="update_setting_category(this);" >';
    $option .= '<i class="fa fa-edit"></i>';
    $option .= '</a>';
    }
    if(has_permission('okr','','delete') || is_admin()){ 
    $option .= '<a href="' . admin_url('okr/delete_setting_category/'.$aRow['id']) . '" class="btn btn-danger btn-icon _delete">';
    $option .= '<i class="fa fa-remove"></i>';
    $option .= '</a>';
    }
    $row[] = $option; 
    
    $output['aaData'][] = $row;

}
