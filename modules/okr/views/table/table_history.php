<?php

defined('BASEPATH') or exit('No direct script access allowed');
$temp = '';
$aColumns = [ 
    'id',
    'recently_checkin',
    'upcoming_checkin',
    'type'
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'okrs_checkin_log';
$join         = [];
$where = [];
if($this->ci->input->post('id_s')){
    $id = $this->ci->input->post('id_s');
    array_push($where, ' where okrs_id = '.$id);
}
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['okrs_id', 'created_date']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    if($temp == ''){
       $temp = $aRow['created_date']; 
    }else{
        if($temp == $aRow['created_date']){
            continue;
        }else{
            $temp = $aRow['created_date']; 
        }
    }
    $row = [];
    $row[] = _d($aRow['recently_checkin']);
    $row[] = _d($aRow['upcoming_checkin']);
    if($aRow['type'] == 2){
        $row[] = _l('draft');
    }else{
        $row[] = _l('checkin');
    }

    $row[] = '<a href="'.admin_url('okr/view_details/'.$aRow['id']).'">'._l('view_details').'</a>'; 
    
    $output['aaData'][] = $row;

}
