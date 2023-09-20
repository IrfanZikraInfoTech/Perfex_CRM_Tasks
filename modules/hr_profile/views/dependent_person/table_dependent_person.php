 <?php

 defined('BASEPATH') or exit('No direct script access allowed');

 $base_currency = get_base_currency();

 $aColumns = [
 	'id',
 	'staffid',
 	'dependent_name',
 	'dependent_bir',
 	'start_month',
 	'dependent_iden',
 	'reason',
 	'status',
 	'status_comment'
 ];

 $sIndexColumn = 'id';
 $sTable       = db_prefix() . 'hr_dependent_person';

 $join = [];
 $where  = [];
 $filter = [];

//load deparment by manager
if(!is_admin() && !has_permission('hrm_dependent_person','','view')){
	  //View own
	$staff_ids = $this->ci->hr_profile_model->get_staff_by_manager();
	if (count($staff_ids) > 0) {
		$where[] = 'AND '.db_prefix().'hr_dependent_person.staffid IN (' . implode(', ', $staff_ids) . ')';

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
 			$where_staff .= ' where staffid = "'.$staffs. '"';
 		}else{
 			$where_staff .= ' or staffid = "' .$staffs.'"';
 		}
 	}
 	if($where_staff != '')
 	{
 		array_push($where, $where_staff);
 	}
 }


 $staff_id = $this->ci->input->post('staff_id');
 if(isset($staff_id)){
 	$where_staff = '';
 	foreach ($staff_id as $staffid) {

 		if($staffid != '')
 		{
 			if($where_staff == ''){
 				$where_staff .= ' ('.db_prefix().'hr_dependent_person.staffid in ('.$staffid.')';
 			}else{
 				$where_staff .= ' or '.db_prefix().'hr_dependent_person.staffid in ('.$staffid.')';
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

$status_id = $this->ci->input->post('status_id');
 if(isset($status_id)){
 	$where_status = '';
 	foreach ($status_id as $statusid) {

 		if($statusid != '')
 		{
 			if($where_status == ''){
 				$where_status .= ' ('.db_prefix().'hr_dependent_person.status in ('.$statusid.')';
 			}else{
 				$where_status .= ' or '.db_prefix().'hr_dependent_person.status in ('.$statusid.')';
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


 $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['end_month', 'relationship']);

 $output  = $result['output'];
 $rResult = $result['rResult'];

 foreach ($rResult as $aRow) {
 	$row = [];
	$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
 	$row[] = $aRow['id'];

 	$subjectOutput ='';


 		$subjectOutput .= '<a href="#">'. $aRow['dependent_name'].' ('.$aRow['relationship'].')</a>';


 	$subjectOutput .= '<div class="row-options">';
	 	if(isset($member_view) && $member_view == 1){
	 		if ( (get_staff_user_id() == $aRow['staffid']) &&  ($aRow['status'] == 0)){
		 		$subjectOutput .='<a href="#" onclick="edit_dependent_person(this,'.$aRow['id'].'); return false"  data-toggle="sidebar-right" data-dependent_name="'.$aRow['dependent_name'].'" data-relationship="'.$aRow['relationship'].'"  data-dependent_iden="'.$aRow['dependent_iden'].'" data-reason="'.$aRow['reason'].'" data-dependent_bir="'._d($aRow['dependent_bir']).'"  >'._l('hr_edit').'</a> |';
		 	}

	 		if (has_permission('hrm_dependent_person', '', 'delete') || is_admin() || (get_staff_user_id() == $aRow['staffid'])){
		 		$subjectOutput .='<a href="'.admin_url('hr_profile/delete_dependent_person/'.$aRow['id']).'" class="text-danger" >'. _l('delete').'</a>';
		 	}
	 	}else{
		 	if ((has_permission('hrm_dependent_person', '', 'edit') || is_admin()) && ($aRow['status'] == 0)){
		 		$subjectOutput .='<a href="#" onclick="dependent_person_update('.$aRow['staffid'].','.$aRow['id'].', true); return false"  data-toggle="sidebar-right" >'._l('hr_edit').'</a> |';
		 	}

		 	if (has_permission('hrm_dependent_person', '', 'delete') || is_admin()){
		 		$subjectOutput .='<a href="'.admin_url('hr_profile/admin_delete_dependent_person/'.$aRow['id']).'" class="text-danger" >'. _l('delete').'</a>';
		 	}
		}


 	$subjectOutput .= '</div>';
 	$row[] = $subjectOutput;

 	$row[] = get_staff_full_name($aRow['staffid']);
 	$row[] = _d($aRow['dependent_bir']);
 	$row[] = $aRow['dependent_iden'];
 	$row[] = _d($aRow['start_month']) .' - '. _d($aRow['end_month']);
 	$row[] = $aRow['reason'];

 	$status_str = '';
 	if($aRow['status'] == 1){ 
 		$status_str .= '<span class="label label-success">'._l('hr_agree_label').'</span>';
 	} elseif($aRow['status'] == 2){
 		$status_str .= '<span class="label label-danger">'._l('hr_rejected_label').'</span>';
 	} else{
 		$status_str .= '<span class="label label-primary">'._l('hr_pending_label').'</span>';
 	}
 	$row[] = $status_str;

 	$options_str = '';

 	if(isset($member_view) && $member_view == 1){
 		$options_str = '';
 	}else{
	 	if($aRow['status'] == 0){

	 		if( is_admin() || has_permission('hrm_dependent_person', '', 'edit')){ 

	 			$options_str .= '<div id="accept_reject_'.$aRow['id'].'">';


	 			$options_str .= '<a class="btn btn-success btn-xs mleft5" data-toggle="tooltip" title=""  onclick="approval(this);" data-original-title="'._l('hr_agree_label').'" data-dependent_id="'.$aRow['id'].'" data-start_month="'._d($aRow['start_month']).'" data-end_month="'._d($aRow['end_month']).'"><i class="fa fa-check"></i></a>';

	 			$options_str .= '<a class="btn btn-danger btn-xs mleft5" data-toggle="tooltip" title=""  onclick="reject(this);" data-original-title="'._l('hr_rejected_label').'" data-dependent_id="'.$aRow['id'].'"  data-start_month="'._d($aRow['start_month']).'" data-end_month="'._d($aRow['end_month']).'"><i class="fa fa-remove"></i></a>';

	 		}

	 	}
	 }
 	$row[] = $options_str;


 	$row[] = $aRow['status_comment'];

 	$output['aaData'][] = $row;
 }
