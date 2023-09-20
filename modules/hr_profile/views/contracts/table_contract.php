<?php

defined('BASEPATH') or exit('No direct script access allowed');

$base_currency = get_base_currency();

$aColumns = [
	'staff_delegate',
	db_prefix() . 'hr_staff_contract.id_contract as id',
	'contract_code',
	'name_contract',
	'staff',
	'(select group_concat('.db_prefix().'departments.departmentid separator "," ) from '.db_prefix().'staff_departments join '.db_prefix().'departments on '.db_prefix().'staff_departments.departmentid = '.db_prefix().'departments.departmentid where '.db_prefix().'staff_departments.staffid = '.db_prefix().'staff.staffid order by '.db_prefix().'staff.staffid ) as departments',
	'start_valid',
	'end_valid',
	'contract_status',
	'sign_day',
];

$sIndexColumn = 'id_contract';
$sTable       = db_prefix() . 'hr_staff_contract';

$join = [
	'LEFT JOIN ' . db_prefix() . 'hr_staff_contract_type ON ' . db_prefix() . 'hr_staff_contract_type.id_contracttype = ' . db_prefix() . 'hr_staff_contract.name_contract',
	'LEFT JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'hr_staff_contract.staff = ' . db_prefix() . 'staff.staffid',
	'LEFT JOIN ' .db_prefix().'roles ON '.db_prefix().'roles.roleid = '.db_prefix().'staff.role' , 
];

$where  = [];
$filter = [];

//load deparment by manager
if(!is_admin() && !has_permission('hrm_contract','','view')){
	  //View own
	$staff_ids = $this->ci->hr_profile_model->get_staff_by_manager();
	if (count($staff_ids) > 0) {
		$where[] = 'AND '.db_prefix().'hr_staff_contract.staff IN (' . implode(', ', $staff_ids) . ')';

	}else{
		$where[] = 'AND 1=2';
	}

}

$member_view = $this->ci->input->post('member_view');

if($this->ci->input->post('memberid')){
	$where_staff = '';
	$staffs = $this->ci->input->post('memberid');
	if($staffs != '')
	{
		if($where_staff == ''){
			$where_staff .= 'AND staff = "'.$staffs. '"';
		}else{
			$where_staff .= 'or staff = "' .$staffs.'"';
		}
	}
	if($where_staff != '')
	{
		array_push($where, $where_staff);
	}
}

if ($this->ci->input->post('draft')) {
	array_push($filter, 'AND contract_status = "draft"');
}

if ($this->ci->input->post('valid')) {
	array_push($filter, 'AND contract_status = "valid"');
}

if ($this->ci->input->post('invalid')) {
	array_push($filter, 'AND contract_status = "invalid"');
}

if ($this->ci->input->post('hr_contract_is_about_to_expire')) {
	array_push($filter, 'AND end_valid <= "'.date('Y-m-d',strtotime('+7 day',strtotime(date('Y-m-d')))).'" AND end_valid >= "'.date('Y-m-d').'" AND contract_status = "valid"');
}

if ($this->ci->input->post('hr_overdue_contract')) {
	array_push($filter, 'AND end_valid < "'.date('Y-m-d').'" AND contract_status = "valid"');
}


$staff    = $this->ci->staff_model->get();
$staffIds = [];

foreach ($staff as $s) {
	if ($this->ci->input->post('contracts_by_staff_' . $s['staffid'])) {
		array_push($staffIds, $s['staffid']);
	}
}
if (count($staffIds) > 0) {

	array_push($filter, 'AND staff IN (' . implode(', ', $staffIds) . ')');
}

$types    = $this->ci->hr_profile_model->get_contracttype();
$typesIds = [];
foreach ($types as $type) {
	if ($this->ci->input->post('contracts_by_type_' . $type['id_contracttype'])) {
		array_push($typesIds, $type['id_contracttype']);
	}
}

if (count($typesIds) > 0) {

	array_push($filter, 'AND name_contract IN (' . implode(', ', $typesIds) . ')');
}

$duration    = $this->ci->hr_profile_model->get_duration();
$duIds = [];
foreach ($duration as $d) {
	if ($this->ci->input->post('contracts_by_duration_' . $d['duration'].'_'.$d['unit'])) {
		array_push($duIds, $d['duration'].'_'.$d['unit']);
	}
}

if (count($duIds) > 0) {

	array_push($filter, 'AND CONCAT(duration, "_", unit) IN ("' . implode(', ', $duIds) . '")');
}

if (count($filter) > 0) {
	array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

$department_id = $this->ci->input->post('hrm_deparment');

if(isset($department_id) && strlen($department_id) > 0){

	//filter by department ( parent, children)
	$departmentgroup = $this->ci->hr_profile_model->get_staff_in_deparment($department_id);
	if (count($departmentgroup) > 0) {
		$where[] = 'AND '.db_prefix().'hr_staff_contract.staff IN (SELECT staffid FROM '.db_prefix().'staff_departments WHERE departmentid IN (' . implode(', ', $departmentgroup) . '))';
	}

}

$staff_id = $this->ci->input->post('hrm_staff');
if(isset($staff_id)){
	$where_staff = '';
	foreach ($staff_id as $staffid) {

		if($staffid != '')
		{
			if($where_staff == ''){
				$where_staff .= ' ('.db_prefix().'staff.staffid in ('.$staffid.')';
			}else{
				$where_staff .= ' or '.db_prefix().'staff.staffid in ('.$staffid.')';
			}
		}
	}
	if($where_staff != '')
	{
		$where_staff .= ')';
		if($where != ''){
			array_push($where, 'AND'. $where_staff);
		}else{
			array_push($where, $where_staff);
		}
		
	}
}

if($this->ci->input->post('validity_start_date')){
	$start_date = to_sql_date($this->ci->input->post('validity_start_date'));

	array_push($where, 'AND date_format(start_valid, "%Y-%m-%d") >= "'.$start_date.'"');
}

if($this->ci->input->post('validity_end_date')){
	$end_date = to_sql_date($this->ci->input->post('validity_end_date'));

	array_push($where, 'AND date_format(end_valid, "%Y-%m-%d") <= "'.$end_date.'"');

}


$aColumns = hooks()->apply_filters('contracts_table_sql_columns', $aColumns);

// Fix for big queries. Some hosting have max_join_limit

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'hr_staff_contract.id_contract', 'name_contracttype', 'firstname', 'duration', 'unit', db_prefix() . 'hr_staff_contract.id_contract as id', 'lastname', 'signature']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
	$row[] = $aRow['id'];

	if (has_permission('hrm_contract', '', 'view') || has_permission('hrm_contract', '', 'view_own') || is_admin()) {
		$subjectOutput = '<a href="' . admin_url('hr_profile/contracts/' . $aRow['id'] ).'" onclick="init_hrm_contract(' . $aRow['id'] . ');return false;">' . $aRow['contract_code'] . '</a>';
	}else{
		$subjectOutput = $aRow['contract_code'];
	}

	$subjectOutput .= '<div class="row-options">';

	if(isset($member_view) && $member_view == '1'){
		$subjectOutput .= '<a href="#" onclick="member_view_contract(' . $aRow['id'] . ');return false;">' . _l('hr_view') .' </a>';
		if($aRow['signature'] != ''){
			$subjectOutput .= ' | <a href="' . admin_url('hr_profile/contract_sign/'.$aRow['id']).'" >' . _l('hr_view_detail') .' </a>';
		}

	}else{

		if (has_permission('hrm_contract', '', 'view') || has_permission('hrm_contract', '', 'view_own') || is_admin()) {
			$subjectOutput .= '<a href="' . admin_url('hr_profile/contracts/' . $aRow['id'] ).'" onclick="init_hrm_contract(' . $aRow['id'] . ');return false;">' . _l('hr_view') .' </a>';
		}
		if (has_permission('hrm_contract', '', 'edit') || is_admin()) {
			$subjectOutput .= ' | <a href="' . admin_url('hr_profile/contract/' . $aRow['id']) . '">' . _l('hr_edit') . '</a>';
		}

		if (has_permission('hrm_contract', '', 'delete') || is_admin()) {
			$subjectOutput .= ' | <a href="' . admin_url('hr_profile/delete_contract/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
		}
	}

	$subjectOutput .= '</div>';
	$row[] = $subjectOutput;

	$row[] = $aRow['name_contracttype'];

	$row[] = ' <a href="' . admin_url('hr_profile/member/' . $aRow['staff']) . '">' . $aRow['firstname'] .' '.  $aRow['lastname'] .'</a>';

	$departmentsRow = '';
	if($aRow['departments'] != ''){
		$departments = explode(',', $aRow['departments']);
		

		foreach ($departments as $department) {
			$deparmentname = $this->ci->hr_profile_model->hr_profile_get_department_name($department);
			if($deparmentname){
				$name = $deparmentname->name;
				$departmentsRow .= '<span class="label label-default mleft5 inline-block customer-group-list pointer">' . $name . '</span>';
			}

		}
		$row[] = $departmentsRow;
	}
	else{
		$row[] = '';
	}


	$row[] = _d($aRow['start_valid']);

	$row[] = _d($aRow['end_valid']);

	if($aRow['contract_status'] == 'draft' ){
		$row[] = ' <span class="label label-warning" > '._l('hr_hr_draft').' </span>';
	}elseif($aRow['contract_status'] == 'valid'){
		$row[] = ' <span class="label label-success "> '._l('hr_hr_valid').' </span>';
	}elseif($aRow['contract_status'] == 'invalid'){
		$row[] = ' <span class="label label-danger "> '._l('hr_hr_expired').' </span>';
	}elseif($aRow['contract_status'] == 'finish'){
		$row[] = ' <span class="label label-primary"> '._l('hr_hr_finish').' </span>';
	}else{
		$row[] = '';
	}
	
	$row[] = _d($aRow['sign_day']);

	$row = hooks()->apply_filters('hr_contracts_table_row_data', $row, $aRow);

	$row['DT_RowClass'] = 'has-row-options';
	
	$output['aaData'][] = $row;
}
