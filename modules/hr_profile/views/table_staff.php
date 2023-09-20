<?php

defined('BASEPATH') or exit('No direct script access allowed');

$has_permission_delete = has_permission('hrm_hr_records', '', 'delete');
$has_permission_edit   = has_permission('hrm_hr_records', '', 'edit');
$has_permission_create = has_permission('hrm_hr_records', '', 'create');

$custom_fields = get_custom_fields('staff', [
	'show_on_table' => 1,
]);
$aColumns = [
	'nation',
	'firstname',
	'staff_identifi',
	'email',
	'team_manage',
	'sex',
	db_prefix().'hr_job_position.position_name',
    db_prefix().'roles.name',
	'active',
	'status_work',
];
$sIndexColumn = 'staffid';
$sTable       = db_prefix().'staff';
$join         = [
	'LEFT JOIN '.db_prefix().'roles ON '.db_prefix().'roles.roleid = '.db_prefix().'staff.role',
	'LEFT JOIN '.db_prefix().'hr_job_position ON '.db_prefix().'hr_job_position.position_id = '.db_prefix().'staff.job_position',
];
$i            = 0;
foreach ($custom_fields as $field) {
	$select_as = 'cvalue_' . $i;
	if ($field['type'] == 'date_picker' || $field['type'] == 'date_picker_time') {
		$select_as = 'date_picker_cvalue_' . $i;
	}
	array_push($aColumns, 'ctable_' . $i . '.value as ' . $select_as);
	array_push($join, 'LEFT JOIN '.db_prefix().'customfieldsvalues as ctable_' . $i . ' ON '.db_prefix().'staff.staffid = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $i . '.fieldid=' . $field['id']);
	$i++;
}
if (count($custom_fields) > 4) {
	@$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$where = hooks()->apply_filters('staff_table_sql_where', []);
$where = array();

$department_id = $this->ci->input->post('hr_profile_deparment');
if(isset($department_id) && strlen($department_id) > 0){

	$departmentgroup = $this->ci->hr_profile_model->get_staff_in_deparment($department_id);
	if (count($departmentgroup) > 0) {

		$where[] = 'AND '.db_prefix().'staff.staffid IN (SELECT staffid FROM '.db_prefix().'staff_departments WHERE departmentid IN (' . implode(', ', $departmentgroup) . '))';
	}

}

if($this->ci->input->post('status_work')){
	$where_status = '';
	$status = $this->ci->input->post('status_work');
	foreach ($status as $statues) {
		if($status != '')
		{
			if($where_status == ''){
				$where_status .= ' ('.db_prefix().'staff.status_work in ("'.$statues.'")';
			}else{
				$where_status .= ' or '.db_prefix().'staff.status_work in ("'.$statues.'")';
			}
		}
	}
	if($where_status != '')
	{
		$where_status .= ')';
		if($where != ''){
			array_push($where, 'AND'. $where_status);
		}else{
			array_push($where, $where_status);
		}
		
	}
}          



if($this->ci->input->post('staff_role')){
	$where_role = '';
	$staff_role      = $this->ci->input->post('staff_role');
	foreach ($staff_role as $staff_id) {
		if($staff_id != '')
		{
			if($where_role == ''){
				$where_role .= '( '.db_prefix().'staff.job_position in ('.$staff_id.')';
			}else{
				$where_role .= ' or '.db_prefix().'staff.job_position in ('.$staff_id.')';
			}
		}
	}

	if($where_role != '')
	{
		$where_role .= ' )';
		if($where_role != ''){
			array_push($where, 'AND '. $where_role);
		}else{
			array_push($where, $where_role);
		}

	}
	
}


$manages = $this->ci->input->post('staff_teammanage');
if(isset($manages) && strlen($manages) > 0){

	$where[] = '  AND staffid IN (select 
	staffid 
	from    (select * from '.db_prefix().'staff as s
	order by s.team_manage, s.staffid) departments_sorted,
	(select @pv := '.$manages.') initialisation
	where   find_in_set(team_manage, @pv)
	and     length(@pv := concat(@pv, ",", staffid)) OR staffid ='.$manages.')';
}

//load deparment by manager
if(!is_admin() && !has_permission('hrm_hr_records','','view')){
	  //View own
	$staff_ids = $this->ci->hr_profile_model->get_staff_by_manager();
	if (count($staff_ids) > 0) {
		$where[] = 'AND '.db_prefix().'staff.staffid IN (' . implode(', ', $staff_ids) . ')';

	}else{
		$where[] = 'AND 1=2';
	}

}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
	'firstname',
	'email',
	'staff_identifi',
	'profile_image',
	'lastname',
	db_prefix().'staff.staffid',
]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	for ($i = 0; $i < count($aColumns); $i++) {
		if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
			$_data = $aRow[strafter($aColumns[$i], 'as ')];
		} else {
			$_data = $aRow[$aColumns[$i]];
		}
		if($aColumns[$i] == 'staff_identifi'){
			$_data = $aRow['staff_identifi'];
		}elseif($aColumns[$i] == 'birthday'){
			$_data = _d($aRow['birthday']);
		}elseif($aColumns[$i] == 'last_login'){
			$_data = _d($aRow['last_login']);
		}
		elseif($aColumns[$i] == 'sex'){
			$_data = _l($aRow['sex']);
        
        }elseif($aColumns[$i] == 'status_work'){
			$_data = _l($aRow['status_work']);
		}         
		elseif ($aColumns[$i] == 'active') {
			$checked = '';
			if ($aRow['active'] == 1) {
				$checked = 'checked';
			}
			$_data = '<div class="onoffswitch">
			<input type="checkbox" ' . (($aRow['staffid'] == get_staff_user_id() || (is_admin($aRow['staffid']) || !has_permission('hrm_hr_records', '', 'edit')) && !is_admin()) ? 'disabled' : '') . ' data-switch-url="' . admin_url() . 'hr_profile/change_staff_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['staffid'] . '" data-id="' . $aRow['staffid'] . '" ' . $checked . '>
			<label class="onoffswitch-label" for="c_' . $aRow['staffid'] . '"></label>
			</div>';

			$_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
		} elseif ($aColumns[$i] == 'firstname') {
			$_data = '<a href="' . admin_url('hr_profile/member/' . $aRow['staffid']) . '">' . staff_profile_image($aRow['staffid'], [
				'staff-profile-image-small',
			]) . '</a>';
			$_data .= ' <a href="' . admin_url('hr_profile/member/' . $aRow['staffid']) . '">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>';
			
			$_data .= '<div class="row-options">';

			if (has_permission('hrm_hr_records', '', 'view') || has_permission('hrm_hr_records', '', 'view_own') || ($aRow['staffid'] == get_staff_user_id()) ) {
				$_data .= '<a href="' . admin_url('hr_profile/member/' . $aRow['staffid']) . '">' . _l('hr_view') . '</a>';
			}

			if (has_permission('hrm_hr_records', '', 'edit') || ($aRow['staffid'] == get_staff_user_id()) || is_admin()) {
				$_data .= ' | <a href="#" onclick="hr_profile_update_staff_manage_view(' . $aRow['staffid'] . ');return false;" >' . _l('hr_edit') . '</a>';
			}

			if (has_permission('hrm_hr_records', '', 'delete') || is_admin()) {
				if ($has_permission_delete && $output['iTotalRecords'] > 1 && $aRow['staffid'] != get_staff_user_id()) {
					$_data .= ' | <a href="#" onclick="delete_staff_member(' . $aRow['staffid'] . '); return false;" class="text-danger">' . _l('delete') . '</a>';
				}
			}

			$_data .= '</div>';
		} elseif ($aColumns[$i] == 'email') {
			$_data = '<a href="mailto:' . $_data . '">' . $_data . '</a>';
		} elseif ($aColumns[$i] == 'team_manage') {
			if($aRow['staffid'] != ''){
				$team = $this->ci->hr_profile_model->get_staff_departments($aRow['staffid']);
				$str = '';
				$j = 0;
				foreach ($team as $value) {
					$j++;
					$str .= '<span class="label label-tag tag-id-1"><span class="tag">'.$value['name'].'</span><span class="hide">, </span></span>&nbsp';
					if($j%2 == 0){
						$str .= '<br><br/>';
					}
					
				}
				$_data = $str;
			}
			else{
				$_data = '';
			}
		}elseif($aColumns[$i] == 'nation'){
			$_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['staffid'] . '"><label></label></div>';
		}
		else {
			if (strpos($aColumns[$i], 'date_picker_') !== false) {
				$_data = (strpos($_data, ' ') !== false ? _dt($_data) : _d($_data));
			}
		}
		$row[] = $_data;
	}

	$row['DT_RowClass'] = 'has-row-options';
	$output['aaData'][] = $row;
}
