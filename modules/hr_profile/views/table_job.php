 <?php

 defined('BASEPATH') or exit('No direct script access allowed');

 $base_currency = get_base_currency();

 $aColumns = [
 	'job_id',
 	'job_name',
 	'description',
 	'1',
 	
 ];

 $sIndexColumn = 'job_id';
 $sTable       = db_prefix() . 'hr_job_p';

 $join = [];

 $where  = [];
 $filter = [];

//load deparment by manager
 if(!is_admin() && !has_permission('staffmanage_job_position','','view')){
      //View own
 	$array_department = $this->ci->hr_profile_model->get_department_by_manager();

 	if (count($array_department) == 0) {
 		$where[] = 'AND 1=2';
 	}
 }
      
 $department_id = $this->ci->input->post('department_id');
 $job_position_id = $this->ci->input->post('job_position_id');

 if(isset($department_id)){
 	if(isset($job_position_id)){
 		$job_p_id = $this->ci->hr_profile_model->get_department_from_position_department($job_position_id, true);

 		if(strlen($job_p_id) != 0){
 			$where[] = 'AND '.db_prefix().'hr_job_p.job_id IN ('.$job_p_id.')';

 		}else{
 			$where[] = 'AND '.db_prefix().'hr_job_p.job_id IN ("")';

 		}

 	}else{
 		$job_p_id = $this->ci->hr_profile_model->get_department_from_position_department($department_id, false);

 		if(strlen($job_p_id) != 0){
 			$where[] = 'AND '.db_prefix().'hr_job_p.job_id IN ('.$job_p_id.')';

 		}else{
 			$where[] = 'AND '.db_prefix().'hr_job_p.job_id IN ("")';


 		}
 	}

 }elseif(isset($job_position_id)){
 	$job_p_id = $this->ci->hr_profile_model->get_department_from_position_department($job_position_id, true);

 	if(strlen($job_p_id) != 0){
 		$where[] = 'AND '.db_prefix().'hr_job_p.job_id IN ('.$job_p_id.')';

 	}else{
 		$where[] = 'AND '.db_prefix().'hr_job_p.job_id IN ("")';


 	}

 }



 $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['job_id']);

 $output  = $result['output'];
 $rResult = $result['rResult'];

 foreach ($rResult as $aRow) {
 	$row = [];

 	$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['job_id'] . '"><label></label></div>';
 	$row[] = $aRow['job_id'];
 	$subjectOutput ='';

 	if (has_permission('staffmanage_job_position', '', 'view') || has_permission('staffmanage_job_position', '', 'view_own' ) || is_admin()){ 

 		$subjectOutput .= '<a href="'.admin_url('hr_profile/job_positions/'.$aRow['job_id']).'">'. $aRow['job_name'].'</a>';
 	}


 	$subjectOutput .= '<div class="row-options">';

 	if (has_permission('staffmanage_job_position', '', 'edit') || is_admin()){
 		$subjectOutput .='<a href="#" onclick="edit_job_p(this,'.$aRow['job_id'].'); return false" data-name="'. $aRow['job_name'].'"  data-toggle="sidebar-right" data-target=".job_p-add-edit-modal">'._l('hr_edit').'</a> |';
 	}

 	if (has_permission('staffmanage_job_position', '', 'delete') || is_admin()){
 		$subjectOutput .='<a href="'.admin_url('hr_profile/delete_job_p/'.$aRow['job_id']).'" class="text-danger _delete" >'. _l('delete').'</a>';

 	}

 	$subjectOutput .= '</div>';
 	$row[] = $subjectOutput;

 	/*get frist 100 character */
 	if(strlen($aRow['description']) > 200){
 		$pos=strpos($aRow['description'], ' ', 200);
 		$description_sub = substr($aRow['description'],0,$pos ); 
 	}else{
 		$description_sub = $aRow['description'];
 	}


 	$row[] = $description_sub;

	// get department
 	$arr_department = $this->ci->hr_profile_model->get_department_from_job_p($aRow['job_id']);

 	if(count($arr_department) > 0){

 		$str = '';
 		$j = 0;
 		foreach ($arr_department as $key => $member_id) {
 			$member   = hr_profile_get_department_name($member_id);

 			$j++;
 			$str .= '<span class="label label-tag tag-id-1"><span class="tag">'.$member->name.'</span><span class="hide">, </span></span>&nbsp';
 			
 			if($j%2 == 0){
 				$str .= '<br><br/>';
 			}
 			
 		}
 		$_data = $str;
 	}
 	else{
 		$_data = '';
 	}

 	//view own
	if(isset($array_department)){

		if(count($arr_department) == 0){
			continue;//jump
		}else{
			$check_dp=false;
			foreach ($arr_department as $dp_id) {
			    if(in_array($dp_id, $array_department)){
					$check_dp = true;//jump
				}
			}

			if($check_dp == false){
				continue;//jump
			}
		}
		
	}

 	$row[] = $_data;

 	$output['aaData'][] = $row;
 }
