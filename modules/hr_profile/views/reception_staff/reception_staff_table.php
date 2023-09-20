<?php

defined('BASEPATH') or exit('No direct script access allowed');


$aColumns = [
	db_prefix().'hr_rec_transfer_records.staffid',
	db_prefix().'hr_rec_transfer_records.staff_identifi',
	db_prefix().'hr_rec_transfer_records.firstname',  
	db_prefix().'hr_rec_transfer_records.birthday',
	db_prefix().'hr_rec_transfer_records.staffid',
];
$sIndexColumn = 'staffid';
$sTable       = db_prefix().'hr_rec_transfer_records';

$join         = [
	'LEFT JOIN '.db_prefix().'staff on '.db_prefix().'staff.staffid = '.db_prefix().'hr_rec_transfer_records.staffid',
];

$where = [];

//load deparment by manager
if(!is_admin() && !has_permission('hrm_reception_staff','','view')){
	  //View own
	$staff_ids = $this->ci->hr_profile_model->get_staff_by_manager();
	if (count($staff_ids) > 0) {
		$where[] = 'AND '.db_prefix().'hr_rec_transfer_records.staffid IN (' . implode(', ', $staff_ids) . ')';

	}else{
		$where[] = 'AND 1=2';
	}

}


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
	'profile_image',
	db_prefix().'hr_rec_transfer_records.id',
	db_prefix().'staff.lastname',
	db_prefix().'staff.firstname',
	db_prefix().'hr_rec_transfer_records.staffid',
	db_prefix().'staff.staff_identifi',
	db_prefix().'staff.birthday',
	'id'
]);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow[db_prefix().'hr_rec_transfer_records.staffid'] . '"><label></label></div>';
	$row[] = $aRow['staffid']; 

	$_data ='';

	$_data .= '<a href="' . admin_url('hr_profile/member/' . $aRow[db_prefix().'hr_rec_transfer_records.staffid']) . '">' . staff_profile_image($aRow[db_prefix().'hr_rec_transfer_records.staffid'], [
		'staff-profile-image-small',
	]) . '</a>';

	$_data .= ' <a href="' . admin_url('hr_profile/member/' . $aRow[db_prefix().'hr_rec_transfer_records.staffid']) . '">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a><br/>';

	$_data  .= '<div class="row-options">';
	if(is_admin() || has_permission('hrm_reception_staff','','edit')){
		$_data.='<span class="reception"><a  href="#" onclick="show_info_reception('.$aRow[db_prefix().'hr_rec_transfer_records.staffid'].');" >'. _l('hr_view') .'</a> |';
	}

	if(is_admin() || has_permission('hrm_reception_staff','','delete')){
		$_data.=' <a  href="' . admin_url('hr_profile/delete_reception/' . $aRow[db_prefix().'hr_rec_transfer_records.staffid']) . '" class="text-danger" >'. _l('delete') .'</a></span>';
	}
	$_data .= '</div>';

	$row[] = $_data;  
	$row[] = $aRow['staff_identifi'];  
	$row[] = _d($aRow['birthday']);


	$percent = round((float)$this->ci->get_percent_complete($aRow['staffid']), 2);

	ob_start();

	$progress_bar_percent = $percent / 100; ?>
	<input type="hidden" value="<?php
	echo html_entity_decode($progress_bar_percent); ?>" name="percent">
	<div class="goal-progress" data-reverse="true">
		<strong class="goal-percent"><?php
		echo html_entity_decode($percent); ?>%</strong>
	</div>
	<?php
	$progress = ob_get_contents();
	ob_end_clean();

	$row[]              = $progress;


	$output['aaData'][] = $row;
}
