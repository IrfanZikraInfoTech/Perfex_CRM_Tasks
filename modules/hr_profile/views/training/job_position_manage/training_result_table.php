<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
	'resultsetid',
	'staff_id',
	'trainingid',
	'date',
];


$sIndexColumn = 'resultsetid';
$sTable       = db_prefix() . 'hr_p_t_surveyresultsets';

$join = [
	'LEFT JOIN ' . db_prefix() . 'hr_position_training ON ' . db_prefix() . 'hr_p_t_surveyresultsets.trainingid = ' . db_prefix() . 'hr_position_training.training_id',
];

$where =[];

//load deparment by manager
if(!is_admin() && !has_permission('staffmanage_training','','view')){
	  //View own
	$staff_ids = $this->ci->hr_profile_model->get_staff_by_manager();
	if (count($staff_ids) > 0) {
		$where[] = 'AND '.db_prefix().'hr_p_t_surveyresultsets.staff_id IN (' . implode(', ', $staff_ids) . ')';

	}else{
		$where[] = 'AND 1=2';
	}

}


$training_program = $this->ci->input->post('training_program');
if(isset($training_program)){
//get staff from training program
	$str_staff = $this->ci->hr_profile_model->get_staff_from_training_program($training_program);
	if(strlen($str_staff) > 0){
		$where[] = 'AND ('  .db_prefix().'hr_p_t_surveyresultsets.staff_id IN ('.$str_staff.') )';
	}else{
		$where[] = 'AND (1=3 )';
	}

}

$staff_id = $this->ci->input->post('hr_staff');
if(isset($staff_id)){
	$where_staff = '';
	foreach ($staff_id as $staffid) {

		if($staffid != '')
		{
			if($where_staff == ''){
				$where_staff .= ' ('.db_prefix().'hr_p_t_surveyresultsets.staff_id in ('.$staffid.')';
			}else{
				$where_staff .= ' or '.db_prefix().'hr_p_t_surveyresultsets.staff_id in ('.$staffid.')';
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

$training_libraries = $this->ci->input->post('training_library');
if(isset($training_libraries)){
	$where_staff = '';
	foreach ($training_libraries as $training_library) {

		if($training_library != '')
		{
			if($where_staff == ''){
				$where_staff .= ' ('.db_prefix().'hr_p_t_surveyresultsets.trainingid in ('.$training_library.')';
			}else{
				$where_staff .= ' or '.db_prefix().'hr_p_t_surveyresultsets.trainingid in ('.$training_library.')';
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


$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'hr_position_training.subject',db_prefix() . 'hr_position_training.training_type', db_prefix() . 'hr_position_training.hash' , db_prefix() . 'hr_position_training.training_id' ]);


$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

	$position_training = $this->ci->hr_profile_model->get_board_mark_form($aRow['resultsetid']);

	$row = [];
	$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['resultsetid'] . '"><label></label></div>';
	$row[] = $aRow['resultsetid'];

	$subject = get_staff_full_name($aRow['staff_id']);
	$subject .= '<div class="row-options">';

	if (has_permission('staffmanage_training', '', 'view') || has_permission('staffmanage_training', '', 'view_own')) {
		
		$subject .= '<a href="' . site_url('hr_profile/participate/view_staff_training_result/'.$aRow['staff_id'].'/'.$aRow['resultsetid'].'/' . $aRow['training_id'] . '/' . $aRow['hash']) . '" target="_blank">' ._l('view'). '</a>';

	}

	if (has_permission('staffmanage_training', '', 'delete')) {
		$subject .= ' | <a href="' . admin_url('hr_profile/delete_job_position_training_process/' . $aRow['resultsetid']) . '" class="text-danger _delete hide">' . _l('delete') . '</a>';
	}
	
	$subject .= '</div>';

	$row[] = $subject;

	$row[] =$aRow['subject'];
	$row[] = get_type_of_training_by_id($aRow['training_type']);
	$row[] = _dt($aRow['date']);

	$row['DT_RowClass'] = 'has-row-options';
	$output['aaData'][] = $row;
}
