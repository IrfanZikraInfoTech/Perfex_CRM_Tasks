<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
	'training_process_id',
	'training_name',
	'training_type',
	'description',
	'mint_point',
	'date_add',
];


$sIndexColumn = 'training_process_id';
$sTable       = db_prefix() . 'hr_jp_interview_training';

$join =[];
$where =[];

//load deparment by manager
 if(!is_admin() && !has_permission('staffmanage_training','','view')){
      //View own
 	$array_staff = $this->ci->hr_profile_model->get_staff_by_manager();

 	if (count($array_staff) == 0) {
 		$where[] = 'AND 1=2';
 	}
 }

$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['job_position_id', 'position_training_id', 'additional_training', 'staff_id', 'time_to_start', 'time_to_end']);


$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

	$row = [];
		$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['training_process_id'] . '"><label></label></div>';
		$row[] = $aRow['training_process_id'];

			if (has_permission('staffmanage_training', '', 'view') || has_permission('staffmanage_training', '', 'view_own')) {
				$subject = '<a href="' . admin_url('hr_profile/view_training_program/' . $aRow['training_process_id']) . '" >' .  $aRow['training_name']  . '</a>';

			}else{
				$subject = $aRow['training_name'];
			}

			$subject .= '<div class="row-options">';

			if (has_permission('staffmanage_training', '', 'view') || has_permission('staffmanage_training', '', 'view_own')) {

				$subject .= '<a href="' . admin_url('hr_profile/view_training_program/' . $aRow['training_process_id']) . '" >' . _l('view') . '</a>';
			}
			if (has_permission('staffmanage_training', '', 'edit')) {
				$subject .= ' | <a href="#" onclick="edit_training_process(this,' . $aRow['training_process_id'] . ', '.$aRow['training_process_id'].');return false;" data-id_training= "'.$aRow['training_process_id'].'" data-training_name= "'.$aRow['training_name'].'"  data-job_position_training_type= "'.$aRow['training_type'].'" data-job_position_mint_point= "'.$aRow['mint_point'].'"  data-job_position_training_id= "'.$aRow['position_training_id'].'" data-job_position_id= "'.$aRow['job_position_id'].'" data-additional_training= "'.$aRow['additional_training'].'" data-staff_id= "'.$aRow['staff_id'].'" data-time_to_start= "'._d($aRow['time_to_start']).'" data-time_to_end= "'._d($aRow['time_to_end']).'" >' . _l('hr_edit') . '</a>';
			}    

			if (has_permission('staffmanage_training', '', 'delete')) {
				$subject .= ' | <a href="' . admin_url('hr_profile/delete_job_position_training_process/' . $aRow['training_process_id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
			}
			$subject .= '</div>';

		$row[] = $subject;

		$row[] = get_type_of_training_by_id($aRow['training_type']);

		/*get frist 100 character */
		if(strlen($aRow['description']) > 300){
			$pos=strpos($aRow['description'], ' ', 300);
			$description_sub = substr($aRow['description'],0,$pos ); 
		}else{
			$description_sub = $aRow['description'];
		}

		$row[] = $description_sub;
		$row[] = $aRow['mint_point'];
		$row[] = _dt($aRow['date_add']);

		//view own
		if(strlen($aRow['job_position_id']) > 0){
			$training_program_staff = $this->ci->hr_profile_model->get_staff_by_job_position($aRow['job_position_id']);
		}else{
			$training_program_staff = explode(",", $aRow['staff_id']);
		}

		if(isset($array_staff)){

			if(count($training_program_staff) == 0){
				continue;//jump
			}else{
				$check_staff=false;
				foreach ($training_program_staff as $staff_id) {
					if(in_array($staff_id, $array_staff)){
						$check_staff = true;//jump
					}
				}

				if($check_staff == false){
					continue;//jump
				}
			}
		}

	$row['DT_RowClass'] = 'has-row-options';
	$output['aaData'][] = $row;
}
