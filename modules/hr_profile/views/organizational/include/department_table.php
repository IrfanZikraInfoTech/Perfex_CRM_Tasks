<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
	'departmentid',
	'name',
	'parent_id',
	'manager_id',
	'email',
	'calendar_id',
	];
$sIndexColumn = 'departmentid';
$sTable       = db_prefix().'departments';
$where = array();
$dept = $this->ci->input->post('dept');
if(isset($dept) && strlen($dept) > 0 && $dept != 0){

	$where[] = ' AND  (departmentid IN (select 
		departmentid 
		from    (select * from tbldepartments
		order by tbldepartments.parent_id, tbldepartments.departmentid) departments_sorted,
		(select @pv := '.$dept.') initialisation
		where   find_in_set(parent_id, @pv)
		and     length(@pv := concat(@pv, ",", departmentid))) OR departmentid = '.$dept.')';
}

//load deparment by manager
if(!is_admin() && !has_permission('staffmanage_orgchart','','view')){
	  //View own
	$array_department = $this->ci->hr_profile_model->get_department_by_manager();
	if (count($array_department) > 0) {
		$where[] = 'AND '.db_prefix().'departments.departmentid IN (' . implode(', ', $array_department) . ')';

	}else{
		$where[] = 'AND 1=2';
	}

}



$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [],$where, ['departmentid','email', 'hidefromclient', 'host', 'encryption', 'password', 'delete_after_import', 'imap_username', 'parent_id', 'manager_id']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	for ($i = 0; $i < count($aColumns); $i++) {
		$_data = $aRow[$aColumns[$i]];
		$ps    = '';
		if (!empty($aRow['password'])) {
			$ps = $this->ci->encryption->decrypt($aRow['password']);
		}

		if ($aColumns[$i] == 'departmentid') {
			$_data = $aRow['departmentid'];
		}elseif ($aColumns[$i] == 'name') {
			$_data = $aRow['name'];
			
		}elseif($aColumns[$i] == 'parent_id'){
			$dpm = $this->ci->hr_profile_model->hr_profile_get_department_name($aRow['parent_id']);
			if($dpm){
				$_data = $dpm->name;
			}else{
				$_data = '';
			}
		}elseif($aColumns[$i] == 'manager_id'){
			if($aRow['manager_id'] != 0){
				$_data = '<a href="' . admin_url('staff/profile/' . $aRow['manager_id']) . '">' . staff_profile_image($aRow['manager_id'], [
					'staff-profile-image-small',
					]) . '</a>';
				$_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['manager_id']) . '">' . get_staff_full_name( $aRow['manager_id']) . '</a>';
			}else{
				$_data = '';
			}
		}
		$row[] = $_data;
	}

	$options = '';

	if(is_admin() || has_permission('staffmanage_orgchart','','edit')){
		$options .= icon_btn('departments/department/' . $aRow['departmentid'], 'pencil-square-o', 'btn-default', [
			'onclick' => 'edit_department(this,' . $aRow['departmentid'] . '); return false', 'data-name' => $aRow['name'], 'data-calendar-id' => $aRow['calendar_id'], 'data-email' => $aRow['email'], 'data-hide-from-client' => $aRow['hidefromclient'], 'data-host' => $aRow['host'], 'data-password' => $ps, 'data-encryption' => $aRow['encryption'], 'data-imap_username' => $aRow['imap_username'], 'data-delete-after-import' => $aRow['delete_after_import'], 'data-parent_id' => $aRow['parent_id'], 'data-manager_id' => $aRow['manager_id'], 'data-toggle' => 'sidebar-right', 'data-target' => '.department-add-edit-modal'
			]);
	}

	if(is_admin() || has_permission('staffmanage_orgchart','','delete')){
		$options .= icon_btn('hr_profile/delete/' . $aRow['departmentid'], 'remove', 'btn-danger _delete');
	}

	$row[] = $options;

	$output['aaData'][] = $row;
}
