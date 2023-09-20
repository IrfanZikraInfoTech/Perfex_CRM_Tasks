<?php
defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'proposal_name',  
    'position',
    'form_work',
    'department',
    'amount_recruiment', 
    'status',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'rec_proposal';
$join         = [
    'LEFT JOIN '.db_prefix().'rec_job_position on '.db_prefix().'rec_job_position.position_id = '.db_prefix().'rec_proposal.position',
    'LEFT JOIN '.db_prefix().'departments on '.db_prefix().'departments.departmentid = '.db_prefix().'rec_proposal.department',
];
$where = [];

if($this->ci->input->post('posiotion_ft')){
    $posiotion_ft = $this->ci->input->post('posiotion_ft');
    $where_posiotion_ft = '';
    foreach ($posiotion_ft as $y) {
        if($y != '')
        {
            if($where_posiotion_ft == ''){
                $where_posiotion_ft .= ' AND (tblrec_proposal.position = "'.$y.'"';
            }else{
                $where_posiotion_ft .= ' or tblrec_proposal.position = "'.$y.'"';
            }
        }
    }
    if($where_posiotion_ft != '')
    {
        $where_posiotion_ft .= ')';
        array_push($where, $where_posiotion_ft);
    }
}
if($this->ci->input->post('dpm')){
    $dpm = $this->ci->input->post('dpm');
    $where_dpm = '';
    foreach ($dpm as $y) {
        if($y != '')
        {
            if($where_dpm == ''){
                $where_dpm .= ' AND (tblrec_proposal.department = "'.$y.'"';
            }else{
                $where_dpm .= ' or tblrec_proposal.department = "'.$y.'"';
            }
        }
    }
    if($where_dpm != '')
    {
        $where_dpm .= ')';
        array_push($where, $where_dpm);
    }
}
if($this->ci->input->post('status')){
    $status = $this->ci->input->post('status');
    $where_status = '';
    foreach ($status as $y) {
        if($y != '')
        {
            if($where_status == ''){
                $where_status .= ' AND (tblrec_proposal.status = "'.$y.'"';
            }else{
                $where_status .= ' or tblrec_proposal.status = "'.$y.'"';
            }
        }
    }
    if($where_status != '')
    {
        $where_status .= ')';
        array_push($where, $where_status);
    }
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id','position_name', db_prefix().'departments.name as dpm_name','workplace','salary_from','salary_to','from_date','to_date','ages_to','ages_from','height','weight','job_description','reason_recruitment','approver','gender','experience','literacy']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'proposal_name') {
            
            $name = '<a href="' . admin_url('recruitment/recruitment_proposal/' . $aRow['id'] ).'" onclick="init_recruitment_proposal('.$aRow['id'].'); return false;">' . $aRow['proposal_name'] . '</a>';

            $name .= '<div class="row-options">';

            $name .= '<a href="' . admin_url('recruitment/recruitment_proposal/' . $aRow['id'] ).'" onclick="init_recruitment_proposal('.$aRow['id'].'); return false;">' . _l('view') . '</a>';

            if($aRow['status'] == 1 ){
                if (has_permission('recruitment', '', 'edit') || is_admin()) {
                    $name .= ' | <a href="#" onclick='.'"'.'edit_proposal(this,' . $aRow['id'] . '); return false;'.'"'.' data-proposal_name="'.$aRow['proposal_name'].'" data-position="'.$aRow['position'].'" data-form_work="'.$aRow['form_work'].'" data-department="'.$aRow['department'].'" data-amount_recruiment="'.$aRow['amount_recruiment'].'" data-workplace="'.$aRow['workplace'].'" data-salary_from="'.app_format_money($aRow['salary_from'],'').'" data-salary_to="'.app_format_money($aRow['salary_to'],'').'" data-from_date="'._d($aRow['from_date']).'" data-to_date="'._d($aRow['to_date']).'" data-ages_to="'.$aRow['ages_to'].'" data-ages_from="'.$aRow['ages_from'].'" data-height="'.$aRow['height'].'" data-weight="'.$aRow['weight'].'" data-reason_recruitment="'.$aRow['reason_recruitment'].'" data-approver="'.$aRow['approver'].'" data-gender="'.$aRow['gender'].'" data-literacy="'.$aRow['literacy'].'" data-experience="'.$aRow['experience'].'"  >' ._l('edit') . '</a>';
                }
            }

            if (has_permission('recruitment', '', 'delete') || is_admin()) {
                $name .= ' | <a href="' . admin_url('recruitment/delete_recruitment_proposal/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }

            $name .= '</div>';

            $_data = $name;
        }elseif ($aColumns[$i] == 'form_work') {
            $_data = _l($aRow['form_work']);
        }elseif ($aColumns[$i] == 'position') {
            $_data = $aRow['position_name'];
        }elseif($aColumns[$i] == 'department'){
            $_data = $aRow['dpm_name'];
        }elseif($aColumns[$i] == 'status'){
            if($aRow['status'] == 1 ){
                $_data = ' <span class="label label inline-block project-status-'.$aRow['status'].' proposal-style"> '._l('_proposal').' </span>';
            }elseif($aRow['status'] == 2 ){
                $_data = ' <span class="label label inline-block project-status-'.$aRow['status'].' approved-style"> '._l('approved').' </span>';
            }elseif($aRow['status'] == 3 ){
                $_data = ' <span class="label label inline-block project-status-'.$aRow['status'].' made_finish-style"> '._l('made_finish').' </span>';
            }elseif($aRow['status'] == 4 ){
                $_data = ' <span class="label label inline-block project-status-'.$aRow['status'].' reject-style"> '._l('reject').' </span>';
            }
        }
        $row[] = $_data;        
    }
    $output['aaData'][] = $row;
}
