<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
     
    'is_name',
    'from_time',
    'interview_day',
    'campaign', 
    'id',
    'interviewer',
    'added_date',
    'added_from', 
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'rec_interview';
$join         = [];
$where = [];


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['to_time','position', 'from_hours','to_hours']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

   for ($i = 0; $i < count($aColumns); $i++) {

        $_data = $aRow[$aColumns[$i]];
        if($aColumns[$i] == 'added_from'){
            $_data = '<a href="' . admin_url('staff/profile/' . $aRow['added_from']) . '">' . staff_profile_image($aRow['added_from'], [
                'staff-profile-image-small',
                ]) . '</a>';
            $_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['added_from']) . '">' . get_staff_full_name($aRow['added_from']) . '</a>';

        }elseif($aColumns[$i] == 'is_name'){
           

            $name = '<a href="' . admin_url('recruitment/interview_schedule/' . $aRow['id'] ).'" onclick="init_recruitment_interview_schedules('.$aRow['id'].'); return false;">' . $aRow['is_name'] . '</a>';

            $name .= '<div class="row-options">';

            $name .= '<a href="' . admin_url('recruitment/interview_schedule/' . $aRow['id'] ).'" onclick="init_recruitment_interview_schedules('.$aRow['id'].'); return false;">' . _l('view') . '</a>';

            if (has_permission('recruitment', '', 'edit') || is_admin()) {
                $name .= ' | <a href="#" onclick='.'"'.'edit_interview_schedule(this,' . $aRow['id'] . '); return false;'.'"'.' data-is_name="'.$aRow['is_name'].'" data-campaign="'.$aRow['campaign'].'" data-interview_day="'._d($aRow['interview_day']).'" data-from_time="'.$aRow['from_time'].'" data-to_time="'.$aRow['to_time'].'" data-interviewer="'.$aRow['interviewer'].'" data-position="'. $aRow['position'].'" >' ._l('edit') . '</a>';
            }

            if (has_permission('recruitment', '', 'delete') || is_admin()) {
                $name .= ' | <a href="' . admin_url('recruitment/delete_interview_schedule/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }

            $name .= '</div>';

            $_data = $name;

        }elseif($aColumns[$i] == 'from_time'){
            $from_hours_format='';
            $to_hours_format='';

            $from_hours = _dt($aRow['from_hours']);
            $from_hours = explode(" ", $from_hours);

            foreach ($from_hours as $key => $value) {
              if($key != 0){
                $from_hours_format .= $value;
                }
            }

            $to_hours = _dt($aRow['to_hours']);
            $to_hours = explode(" ", $to_hours);
            foreach ($to_hours as $key => $value) {
                  if($key != 0){
                    $to_hours_format .= $value;
                }
            }

            $_data = $from_hours_format.' - '.$to_hours_format;
        }elseif ($aColumns[$i] == 'interview_day') {
            $_data = _d($aRow['interview_day']);
        }elseif ($aColumns[$i] == 'campaign') {
            $cp = get_rec_campaign_hp($aRow['campaign']);
            if(isset($cp)){
                $_data = $cp->campaign_code.' - '.$cp->campaign_name;
            }else{
                $_data = '';
            }
            
        }elseif($aColumns[$i] == 'id'){
            $can = get_candidate_interview($aRow['id']);
            $ata = '';
            foreach($can as $cad){
                $ata .= '<a href="' . admin_url('recruitment/candidate/' . $cad) . '">'.candidate_profile_image($cad,[
                    'staff-profile-image-small mright5',
                    ], 'small', [
                    'data-toggle' => 'tooltip',
                    'data-title'  =>  get_candidate_name($cad),
                    ]).'</a>';
            }
            $_data = $ata;
            //$_data = count($can);
        }elseif($aColumns[$i] == 'interviewer'){
            $inv = explode(',', $aRow['interviewer']);
            $ata = '';
            foreach($inv as $iv){
                $ata .= '<a href="' . admin_url('staff/profile/' . $iv) . '">'.staff_profile_image($iv,[
                    'staff-profile-image-small mright5',
                    ], 'small', [
                    'data-toggle' => 'tooltip',
                    'data-title'  =>  get_staff_full_name($iv),
                    ]).'</a>';
            }
            $_data = $ata;
        }elseif($aColumns[$i] == 'added_date'){
            $_data = _d($aRow['added_date']);
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;

}
