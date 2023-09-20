<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    '1',
    'subject',
    'articlegroup',
    'datecreated',
    ];
$sIndexColumn     = 'articleid';
$sTable           = db_prefix() . 'hr_knowledge_base';
$additionalSelect = [
    'name',
    'groupid',
    'articleid',
    'slug',
    'staff_article',
     db_prefix() . 'hr_knowledge_base.description',
    ];
$join = [
    'LEFT JOIN ' . db_prefix() . 'hr_knowledge_base_groups ON ' . db_prefix() . 'hr_knowledge_base_groups.groupid = ' . db_prefix() . 'hr_knowledge_base.articlegroup',
    ];

$where   = [];
$filter  = [];
$groups  = $this->ci->knowledge_base_q_a_model->get_kbg();
$_groups = [];
foreach ($groups as $group) {
    if ($this->ci->input->post('kb_group_' . $group['groupid'])) {
        array_push($_groups, $group['groupid']);
    }
}
if (count($_groups) > 0) {
    array_push($filter, 'AND articlegroup IN (' . implode(', ', $_groups) . ')');
}
if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

if (!has_permission('hr_manage_q_a', '', 'create') && !has_permission('hr_manage_q_a', '', 'edit')) {
    array_push($where, ' AND ' . db_prefix() . 'hr_knowledge_base.active=1');
    array_push($where, ' AND ' . db_prefix() . 'hr_knowledge_base.question_answers=1');
}

$group_ids = $this->ci->input->post('group_id');
if(isset($group_ids)){
    $where_group_id = '';
    foreach ($group_ids as $group_id) {

        if($group_id != '')
        {
            if($where_group_id == ''){
                $where_group_id .= ' ('.db_prefix().'hr_knowledge_base.articlegroup in ('.$group_id.')';
            }else{
                $where_group_id .= ' or '.db_prefix().'hr_knowledge_base.articlegroup in ('.$group_id.')';
            }
        }
    }
    if($where_group_id != '')
    {
        $where_group_id .= ')';
        if($where != ''){
            array_push($where, 'AND'. $where_group_id);
        }else{
            array_push($where, $where_group_id);
        }
        
    }
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if($aColumns[$i] == '1'){
            $_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['articleid'] . '"><label></label></div>';
        }elseif ($aColumns[$i] == 'articlegroup') {
            $_data = $aRow['name'];
        } elseif ($aColumns[$i] == 'subject') {
            $link = admin_url('hr_profile/knowledge_base_q_a/view/' . $aRow['slug']);
            if ($aRow['staff_article'] == 0) {
                $link = site_url('knowledge-base/article/' . $aRow['slug']);
            }

            $_data = '<b>' . $_data . '</b>';
            if (has_permission('hr_manage_q_a', '', 'edit')) {
                $_data = '<a href="' . admin_url('hr_profile/knowledge_base_q_a/article/' . $aRow['articleid']) . '" class="font-size-14">' . $_data . '</a>';
            } else {
                $_data = '<a href="' . $link . '" target="_blank" class="font-size-14">' . $_data . '</a>';
            }

            if ($aRow['staff_article'] == 1) {
                $_data .= '<span class="label label-default pull-right">' . _l('internal_article') . '</span>';
            }

            $_data .= '<div class="row-options">';

            $_data .= '<a href="' . $link . '" target="_blank">' . _l('hr_view') . '</a>';

            if (has_permission('hr_manage_q_a', '', 'edit')) {
                $_data .= ' | <a href="' . admin_url('hr_profile/knowledge_base_q_a/article/' . $aRow['articleid']) . '">' . _l('hr_edit') . '</a>';
            }

            if (has_permission('hr_manage_q_a', '', 'delete')) {
                $_data .= ' | <a href="' . admin_url('hr_profile/knowledge_base_q_a/delete_article/' . $aRow['articleid']) . '" class="_delete text-danger">' . _l('delete') . '</a>';
            }

            $_data .= '</div>';
        } elseif ($aColumns[$i] == 'datecreated') {
            $_data = _dt($_data);
        }

        $row[]              = $_data;
        $row['DT_RowClass'] = 'has-row-options';
    }

    $output['aaData'][] = $row;
}
