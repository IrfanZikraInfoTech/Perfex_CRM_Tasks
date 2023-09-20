<?php

defined('BASEPATH') or exit('No direct script access allowed');

$id = $this->ci->input->post('hr_profile_staff');
$staff_data = $this->ci->staff_model->get($id);
$aColumns = [
	'training_name',
	'training_process_id',
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'hr_training_allocation';
$join = [];
$where = [' where staffid = '.$id];
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['training_process_id','training_name']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {   
	$row = [];
	$row[] = $aRow['training_name'] ;
	$data_marks=$this->ci->get_mark_staff($id);
	$count_data_mark = count($data_marks);
	$count_complete = 0;
	foreach ($data_marks['array_resiult_filter'] as $key => $item_mark) {
		$mark_form = $this->ci->hr_profile_model->get_board_mark_form((int)$item_mark[0]);
		if($item_mark[1] >= $mark_form->mint_point){
			$count_complete += 1;
		}
	}
	$row[] =  $count_complete.'/'.$count_data_mark;
	$output['aaData'][] = $row;
}
