<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	db_prefix().'hr_rec_transfer_records.staffid',
	db_prefix().'hr_rec_transfer_records.firstname',  
	db_prefix().'hr_rec_transfer_records.staff_identifi',
	db_prefix().'hr_rec_transfer_records.birthday',
	db_prefix().'hr_rec_transfer_records.staffid',
];
$sIndexColumn = 'lastname';
$sTable       = db_prefix().'hr_rec_transfer_records';
$join         = [];
$i            = 0;

$join         = [
	'LEFT JOIN '.db_prefix().'staff on '.db_prefix().'staff.staffid = '.db_prefix().'hr_rec_transfer_records.staffid',
];



$where = array();
$where = [];
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
	'profile_image',
	db_prefix().'hr_rec_transfer_records.id',
	db_prefix().'staff.lastname',
	db_prefix().'staff.firstname',
	db_prefix().'hr_rec_transfer_records.staffid',
	db_prefix().'staff.staff_identifi',
	db_prefix().'staff.birthday',
	db_prefix().'staff.job_position',
]);
$output  = $result['output'];
$rResult = $result['rResult'];


foreach ($rResult as $aRow) {
	$row = [];
	$row[] = $aRow['staff_identifi'];  
	$_data = '<a href="' . admin_url('hr_profile/member/' . $aRow[db_prefix().'hr_rec_transfer_records.staffid']) . '">' . staff_profile_image($aRow[db_prefix().'hr_rec_transfer_records.staffid'], [
		'staff-profile-image-small',
	]) . '</a>';
	$_data .= ' <a href="' . admin_url('hr_profile/member/' . $aRow[db_prefix().'hr_rec_transfer_records.staffid']) . '">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>';
	$row[] = $_data; 


	$name_position = '';
	if($aRow['job_position']){
		if($aRow['job_position'] != ''){
			$position = $this->ci->hr_profile_model->get_job_position($aRow['job_position']); 
			if(isset($position)){
				if(isset($position->position_name)){
					$name_position = $position->position_name;
				} 
			} 
		}
	}
	$row[] = $name_position;  

	$name_department = '';
	if($aRow[db_prefix().'hr_rec_transfer_records.staffid']){
		if($aRow[db_prefix().'hr_rec_transfer_records.staffid'] != ''){
			$department = $this->ci->hr_profile_model->get_department_by_staffid($aRow[db_prefix().'hr_rec_transfer_records.staffid']);    
			if(isset($department)){
				$name_department = $department->name;
			}        
		}
	}
	$row[] = $name_department;


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


	if($percent<100){
		$output['aaData'][] = $row;
	} 
}
