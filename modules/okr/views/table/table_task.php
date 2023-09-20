<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [ 
  'id',
  'name',
  'status',
  'startdate',
  'duedate',
  'addedfrom'
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'tasks';
$join         = [];
$where = [];

if($this->ci->input->post('list_tasks')){
  $list_tasks = $this->ci->input->post('list_tasks');
}else{
  $list_tasks = '';
}

if($this->ci->input->post('key_result')){
  $key_result = $this->ci->input->post('key_result');
}else{
  $key_result = '';
}

if($this->ci->input->post('okrs_id')){
  $okrs_id = $this->ci->input->post('okrs_id');
}else{
  $okrs_id = '';
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['priority']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

  if($list_tasks != ""){

    $list_tasks_s = explode(',', $list_tasks);
    if(in_array((int)$aRow['id'], $list_tasks_s)){
      $row = [];
      $row[] = '<a href="'.admin_url('tasks/view/'.$aRow['id']).'" onclick="init_task_modal('.$aRow['id'].'); return false;">'.$aRow['id'].'</a>';

      $option = '';
      $option = '<a href="'.admin_url('tasks/view/'.$aRow['id']).'" class="display-block main-tasks-table-href-name" onclick="init_task_modal('.$aRow['id'].'); return false;">'.$aRow['name'].'</a>';
      if($key_result != '' && $okrs_id != ''){
      $option .= '
      <div class="row-options"><span>
      <a href="'.admin_url('okr/remove_task_key_result/'.$key_result.'/'.$aRow['id'].'/'.$okrs_id).'" id="okr_delete" class="text-danger _delete task-delete">Delete </a></div>
      ';
      }
      $row[] = $option;

      $option_status = '';
      $option_status .= '
      <span class="inline-block label" style="color:#03A9F4;border:1px solid #03A9F4" task-status-table="'.$aRow['status'].'">';
      $option_status .= $aRow['status'] == 1 ?  'Mark as Not Started' : ($aRow['status'] == 2 ?  "Mark as Awaiting Feedback" : ($aRow['status'] == 3 ?  "Mark as Testing" : ($aRow['status'] == 4 ?  "In Progress" : "Mark as Complete")));
      $option_status .= '<div class="dropdown inline-block mleft5 table-export-exclude"><a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableTaskStatus-'.$aRow['id'].'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span data-toggle="tooltip" title="" data-original-title="Change Status"><i class="fa fa-caret-down" aria-hidden="true"></i></span></a>
      <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableTaskStatus-'.$aRow['id'].'" style="bottom: auto;">
      <li >
      <a href="#" onclick="task_mark_as(1,'.$aRow['id'].'); return false;">
      Mark as Not Started
      </a>
      </li>
      <li>
      <a href="#" onclick="task_mark_as(3,'.$aRow['id'].'); return false;">
      Mark as Testing
      </a>
      </li>
      <li>
      <a href="#" onclick="task_mark_as(4,'.$aRow['id'].'); return false;">
      In Progress
      </a>
      </li>
      <li>
      <a href="#" onclick="task_mark_as(2,'.$aRow['id'].'); return false;">
      Mark as Awaiting Feedback
      </a>
      </li>
      <li>
      <a href="#" onclick="task_mark_as(5,'.$aRow['id'].'); return false;">
      Mark as Complete
      </a>
      </li>
      </ul>   
      </div>
      </span>';
      $row[] = $option_status;
      $row[] = _d($aRow['startdate']);
      $row[] = $aRow['duedate'] == null ? "" : _d($aRow['duedate']); 

      $option_assigned = '';
      $option_assigned .= '
      <a href="'.admin_url('profile/'.$aRow['addedfrom']).'">';
      $option_assigned = '<a href="' . admin_url('staff/profile/' . $aRow['addedfrom']) . '" data-toggle="tooltip" data-title="'.get_staff_full_name($aRow['addedfrom']).'">' . staff_profile_image($aRow['addedfrom'], [
        'staff-profile-image-small mright5',
      ]) . '</a>';
      $row[] = $option_assigned ;

      $options = '';

      $options .= '
      <span style="color:#03a9f4;" class="inline-block">';
      $options .= $aRow['priority'] == 1 ?  'Low' : ($aRow['priority'] == 2 ?  "Medium" : ($aRow['status'] == 3 ?  "High" : "Urgent"));

      $options .= '<div class="dropdown inline-block mleft5 table-export-exclude"><a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableTaskPriority-'.$aRow['id'].'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span data-toggle="tooltip" title="Priority"><i class="fa fa-caret-down" aria-hidden="true"></i></span></a>
      <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableTaskPriority-'.$aRow['id'].'" style="bottom: auto;">
      <li>
      <a href="#" onclick="task_change_priority(1,'.$aRow['id'].'); return false;">
      Low
      </a>
      </li>
      <li>
      <a href="#" onclick="task_change_priority(2,'.$aRow['id'].'); return false;">
      Medium
      </a>
      </li>
      <li>
      <a href="#" onclick="task_change_priority(3,'.$aRow['id'].'); return false;">
      High
      </a>
      </li>
      <li>
      <a href="#" onclick="task_change_priority(4,'.$aRow['id'].'); return false;">
      Urgent
      </a>
      </li>
      </ul>
      </div>
      </span>';
      $row[] = $options;

      $output['aaData'][] = $row;

    }
  }

}
