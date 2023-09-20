<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [ 
    'id',
    'your_target',
    'circulation',
    'okr_superior',
    'progress',
    'person_assigned',
    'status',
    'recently_checkin',
    'upcoming_checkin',
    'type',
    'category',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'okrs';
$join         = [];
$where = [];
// method post it get data
$status_ = $this->ci->input->post('status');
$okrs_ = $this->ci->input->post('okrs');
$person_assigned_ = $this->ci->input->post('person_assigned');
$category_ = $this->ci->input->post('category');
$department_ = $this->ci->input->post('department');
$circulation_ = $this->ci->input->post('circulation');
$type_ = $this->ci->input->post('type');

if(isset($status_) && $status_ != ''){
    array_push($where, 'AND status = '.$status_);
}

if(isset($okrs_) && $okrs_ != ''){
    array_push($where, 'AND id = '.$okrs_);
}

if(isset($person_assigned_) && $person_assigned_ != ''){
    array_push($where, 'AND person_assigned = '.$person_assigned_);
}

if(isset($category_) && $category_ != ''){
    array_push($where, 'AND category = '.$category_);
}

if(isset($department_) && $department_ != ''){
    array_push($where, 'AND department = '.$department_);
}

if(isset($circulation_) && $circulation_ != ''){
    array_push($where, 'AND circulation = '.$circulation_);
}

if(isset($type_) && $type_ != ''){
    array_push($where, 'AND type = '.$type_);
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['department']);

$output  = $result['output'];
$rResult = $result['rResult'];
$rResult = array_reverse($rResult);
foreach ($rResult as $aRow) {
    $row = [];
    $status = $aRow['status'] == 0 ? '<span class="label label-warning s-status ">'._l('unfinished').'</span>' : '<span class="label label-success s-status ">'._l('finish').'</span>';
    $type = $aRow['type'] != '' ? ($aRow['type'] == 1 ? _l('personal') : ($aRow['type'] == 2 ? _l('department') : _l('company'))) : '';
    $department = $aRow['department'] != '' && $aRow['department'] != 0 ? get_department_name_of_okrs($aRow['department'])->name : '';
    
    $row[] = $aRow['your_target'];
    $row[] = circulation_date($aRow['circulation']);
    $row[] = progress_template($aRow['progress']);
    $row[] = okr_name($aRow['okr_superior']);
    $row[] = get_staff_full_name($aRow['person_assigned']);
    $row[] = $type;
    $row[] = category_view($aRow['category']);
    $row[] = $department;
    $row[] = _d($aRow['recently_checkin']);
    $row[] = _d($aRow['upcoming_checkin']);
    $row[] = $status;
   
    
    $output['aaData'][] = $row;

}
