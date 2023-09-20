<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [ 
    'id',
    'question',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'okr_setting_question';
$join         = [];
$where = [];
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

    $row = [];
    $row[] = $aRow['question'];
    $option = '';
    if(has_permission('okr','','edit') || is_admin()){ 
    $option .= '<a href="#" class="btn btn-default btn-icon" data-id="'.$aRow['id'].'" data-question="'.$aRow['question'].'" onclick="update_setting_question(this);" >';
    $option .= '<i class="fa fa-edit"></i>';
    $option .= '</a>';
    }
    if(has_permission('okr','','detete') || is_admin()){ 
    $option .= '<a href="' . admin_url('okr/delete_setting_question/'.$aRow['id']) . '" class="btn btn-danger btn-icon _delete">';
    $option .= '<i class="fa fa-remove"></i>';
    $option .= '</a>';
    }
    $row[] = $option; 
    
    $output['aaData'][] = $row;

}
