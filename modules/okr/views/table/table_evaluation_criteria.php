<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [ 
    'id',
    'group_criteria',
    'name',
    'scores'
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'okr_setting_evaluation_criteria';
$join         = [];
$where = [];
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

    $row = [];
    $name = '';
    switch ($aRow['group_criteria']) {
        case 1:
            $name = _l('checkin');
            break;
        case 2:
            $name = _l('acknowledge');
            break;
        case 3:
            $name = _l('other_feedback');
            break;
        default:
            break;
    }
    $row[] = $name;
    $row[] = $aRow['name'];
    $row[] = $aRow['scores'];
    $option = '';
    if(has_permission('okr','','eidt') || is_admin()){ 

    $option .= '<a href="#" class="btn btn-default btn-icon" data-id="'.$aRow['id'].'" data-criteria="'.$aRow['group_criteria'].'" data-name="'.$aRow['name'].'" data-scores="'.$aRow['scores'].'" onclick="update_setting_evaluation_criteria(this);" >';
    $option .= '<i class="fa fa-edit"></i>';
    $option .= '</a>';
    } 
    if(has_permission('okr','','delete') || is_admin()){ 

    $option .= '<a href="' . admin_url('okr/delete_setting_evaluation_criteria/'.$aRow['id']) . '" class="btn btn-danger btn-icon _delete">';
    $option .= '<i class="fa fa-remove"></i>';
    $option .= '</a>';
    }
    $row[] = $option; 
    
    $output['aaData'][] = $row;

}
