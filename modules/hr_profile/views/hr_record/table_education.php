<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	'training_programs_name',
	'training_places',
	'training_time_from',
	'training_time_to',
	'training_result',
	'degree',
	'notes',
];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'hr_education';
$join = [];
$where = [];
if ($staff_id != '') {
	array_push($where, 'AND staff_id=' . $staff_id);
}
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id', 'training_programs_name', 'training_places', 'training_time_from', 'training_time_to', 'training_result', 'degree', 'notes']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	$name = '<a href="#" >' . $aRow['training_programs_name'] . '</a>';
	$name .= '<div class="row-options">';
	$name .= '<a href="#" data-id="' . $aRow['id'] . '" onclick="update_education(this)" data-time_from="' . _dt($aRow['training_time_from']) . '" data-time_to="' . _dt($aRow['training_time_to']) . '" data-result="' . $aRow['training_result'] . '" data-degree="' . $aRow['degree'] . '" data-notes="' . $aRow['notes'] . '" data-name_programe="' . $aRow['training_programs_name'] . '" data-training_pl="' . $aRow['training_places'] . '">' . _l('hr_edit') . '</a>';
	$name .= ' | <a href="#" data-id="' . $aRow['id'] . '" onclick="delete_education(this)" class="text-danger _delete">' . _l('delete') . '</a>';
	$name .= '</div>';

	$row[] = $name;
	$row[] = $aRow['training_places'];
	$row[] = _dt($aRow['training_time_from']);
	$row[] = _dt($aRow['training_time_to']);
	$row[] = $aRow['training_result'];
	$row[] = $aRow['degree'];
	$row[] = $aRow['notes'];
	$output['aaData'][] = $row;
}
