<?php

defined('BASEPATH') or exit('No direct script access allowed');


$aColumns = [
	'id',
	'staff_name',
	'department_name',
	'role_name',
	'email',
	'dateoff',
	'(SELECT count(*) FROM '.db_prefix().'hr_procedure_retire_of_staff'.' where '.db_prefix().'hr_procedure_retire_of_staff.staffid= '.db_prefix().'hr_list_staff_quitting_work.staffid) as total',
	'(select count(*) FROM '.db_prefix().'hr_procedure_retire_of_staff'.'  where '.db_prefix().'hr_procedure_retire_of_staff.staffid= '.db_prefix().'hr_list_staff_quitting_work.staffid and status = 1 ) as total_check',
	'(select active FROM '.db_prefix().'staff'.'  where '.db_prefix().'staff.staffid= '.db_prefix().'hr_list_staff_quitting_work.staffid) as staff_active'

];

$sIndexColumn = 'id';
$sTable       = db_prefix().'hr_list_staff_quitting_work';
$join = [''];
$where = [];

//load deparment by manager
if(!is_admin() && !has_permission('hrm_procedures_for_quitting_work','','view')){
	  //View own
	$staff_ids = $this->ci->hr_profile_model->get_staff_by_manager();
	if (count($staff_ids) > 0) {
		$where[] = 'AND '.db_prefix().'hr_list_staff_quitting_work.staffid IN (' . implode(', ', $staff_ids) . ')';

	}else{
		$where[] = 'AND 1=2';
	}

}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['staffid','staff_name','department_name','role_name','email','dateoff', 'approval']);

$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {

	if($aRow['total'] == 0 && $aRow['total_check'] == 0){
		$ces = 100;
	}else{
		$ces = round($aRow['total_check'] * 100 / $aRow['total'], 2);
	}
	$row = [];
	$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['staffid'] . '"><label></label></div>';
	
	$row[] = $aRow['id'];

	$staff_n = '';
	if(is_admin() || has_permission('hrm_procedures_for_quitting_work','','edit')){
		$staff_n .=  '<a href="#" data-id="'.$aRow['staffid'].'" onclick="detail_checklist_staff(this);">' . staff_profile_image($aRow['staffid'], [
			'staff-profile-image-small',
		]) . '</a><a href="#" onclick="detail_checklist_staff(this)" data-id="'.$aRow['staffid'].'">' . $aRow['staff_name']. '</a>';
	}else{
		$staff_n .= staff_profile_image($aRow['staffid'], ['staff-profile-image-small',]).' '. $aRow['staff_name'];
	}

	$row[] = $staff_n;

	$row[] = $aRow['department_name'];
	$row[] = $aRow['role_name'];
	$row[] = $aRow['email'];
	$row[] = _dt($aRow['dateoff']); 

	ob_start();
	$progress_bar_percent = $ces / 100; ?>
	<input type="hidden" value="<?php
	echo html_entity_decode($progress_bar_percent); ?>" name="percent">
	<div class="goal-progress" data-reverse="true">
		<strong class="goal-percent"><?php
		echo html_entity_decode($ces); ?>%</strong>
	</div>
	<?php
	$progress = ob_get_contents();
	ob_end_clean();

	$row[]              = $progress;



	if($aRow['approval'] == 'approved' ){
		$row[] = '<span class="label label-success">'._l('hr_agree_label').'</span>';
	}else{
		$row[] ='<span class="label label-primary">'._l('hr_pending_label').'</span>';
	}
	$options ='';
	if(is_admin() || has_permission('hrm_procedures_for_quitting_work','','edit')){	
		$options .= '<a href="#" onclick="detail_checklist_staff(this); return false" data-toggle="tooltip" data-original-title="'._l('hr_view').'" data-id="'.$aRow['staffid'].'" class="btn btn-default btn-icon" data-toggle="sidebar-right" data-target=".additional-timesheets-sidebar"><i class="fa fa-eye"></i></a>';
	}

	if(($ces == 100) && (is_admin() || has_permission('hrm_procedures_for_quitting_work','','edit'))){
		if($aRow['approval'] == null ){

			$options .= '<a class="btn btn-success btn-xs mleft5" id="'.$aRow['staffid'].'" resignation_id="'.$aRow['id'].'" data-toggle="tooltip" title=""  onclick="update_status_quit_work(this);" data-original-title="'._l('hr_agree_label').'"><i class="fa fa-check"></i></a>';
		}
	}

	if((is_admin() || (has_permission('hrm_procedures_for_quitting_work','','delete')) && $aRow['approval'] == null) ){

		$options .= '<a class="btn btn-danger btn-xs mleft5" id="confirmDelete" data-toggle="tooltip" title="" href="'. admin_url('hr_profile/delete_procedures_for_quitting_work/'.$aRow['staffid']).'"  data-original-title="'._l('hr_delete_resignation_procedures').'"><i class="fa fa-remove"></i></a>';
	}

	$row[]   = $options;
	$output['aaData'][] = $row;
}
