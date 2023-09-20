<?php

defined('BASEPATH') or exit('No direct script access allowed');


$aColumns = [
   'name_procedure_retire',
    'department'
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'hr_procedure_retire_manage';
$join = [''];
$where = [];



$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id','datecreator']);

$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $CI = & get_instance();
    $CI->load->model('departments_model');
    $row = [];
    $list_department = '';

    $department = '';
    $aRow['department'] = json_decode($aRow['department']);
    foreach ($aRow['department'] as $key => $value) {
        $list_department.= $value.', ';
        if($key == 0){
            $department .= hr_profile_get_department_name($value)->name;
        }else{
            $department .= ', '.hr_profile_get_department_name($value)->name;
        }
    }
    $list_department = rtrim($list_department, ', ');

    $_data = '<a href="' . admin_url('hr_profile/procedure_procedure_retire_details/' . $aRow['id']) . '">'.$aRow['name_procedure_retire'].'</a>';
    $_data .= '<div class="row-options">';
    $_data .= '<a href="' . admin_url('hr_profile/procedure_procedure_retire_details/' . $aRow['id']) . '">' . _l('hr_view') . '</a>';

    if(has_permission('hrm_setting', '', 'edit') || is_admin()){ 
        $_data .= ' | <a href="#" onclick="edit_procedure_form_manage(this); return false;" data-id="'.$aRow['id'].'"  data-name_procedure_retire="'.$aRow['name_procedure_retire'].'"  data-department="'.$list_department.'" >' . _l('hr_edit') . '</a>';
    }

    if(has_permission('hrm_setting', '', 'delete') || is_admin()){ 
        $_data .= ' | <a href="' . admin_url('hr_profile/delete_procedure_form_manage/' . $aRow['id']) . '"  class="text-danger _delete">' . _l('delete') . '</a>';
    }
    $_data .= '</div>';

    $row[] = $_data;
    $row[] = $department;
    $row[] = _dt($aRow['datecreator']);
             
    $output['aaData'][] = $row;
}
