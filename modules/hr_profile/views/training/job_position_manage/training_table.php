<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
	'1',
	'training_id',
	'subject',
	'training_type',
	'(SELECT count(questionid) FROM ' . db_prefix() . 'hr_position_training_question_form WHERE ' . db_prefix() . 'hr_position_training_question_form.rel_id = ' . db_prefix() . 'hr_position_training.training_id AND rel_type="position_training")',
	'(SELECT count(resultsetid) FROM ' . db_prefix() . 'hr_p_t_surveyresultsets WHERE ' . db_prefix() . 'hr_p_t_surveyresultsets.trainingid = ' . db_prefix() . 'hr_position_training.training_id)',
	'datecreated',
];
$sIndexColumn = 'training_id';
$sTable       = db_prefix() . 'hr_position_training';
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['hash',db_prefix() . 'hr_position_training.training_id']);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {

	$row = [];
	for ($i = 0; $i < count($aColumns); $i++) {
		$_data = $aRow[$aColumns[$i]];
		if($aColumns[$i] == '1') {
			$_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['training_id'] . '"><label></label></div>';

		}elseif ($aColumns[$i] == 'subject') {
			$_data = '<a href="' . site_url('hr_profile/participate/index/' . $aRow['training_id'] . '/' . $aRow['hash']) . '" target="_blank">' . $_data . '</a>';

			$_data .= '<div class="row-options">';


			if (is_admin() || has_permission('staffmanage_training', '', 'edit')) {
				$_data .= ' <a href="' . admin_url('hr_profile/position_training/' . $aRow['training_id']) . '">' . _l('hr_edit') . '</a>';
			}    

			if (is_admin() || has_permission('staffmanage_training', '', 'delete')) {
				$_data .= ' | <a href="' . admin_url('hr_profile/delete_position_training/' . $aRow['training_id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
			}

			$_data .= '</div>';
		}elseif($aColumns[$i] == 'training_type'){
			$_data = get_type_of_training_by_id($_data);
		} elseif ($aColumns[$i] == 'datecreated') {
			$_data = _dt($_data);
		}


		$row[] = $_data;
	}
	$row['DT_RowClass'] = 'has-row-options';
	$output['aaData'][] = $row;
}
