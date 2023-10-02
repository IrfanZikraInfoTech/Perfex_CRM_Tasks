<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
     'phone_number as phone',
     'timezone',
     'CONCAT(' . db_prefix() . 'appointly_callbacks.firstname, \' \', ' . db_prefix() . 'appointly_callbacks.lastname) as fullname',
     'status',
     'date_start',
     'date_end',
     'date_added',
];

$callback_statuses = getCallbacksTableStatuses();

$sIndexColumn = 'firstname';
$sTable       = db_prefix() . 'appointly_callbacks';

$where  = [];

if ($this->ci->input->post('custom_view')) {
     foreach ($callback_statuses as $key => $status) {
          $status = strtolower($status);
          if ($this->ci->input->post('custom_view') == $status) {
               $where[] = 'AND status = "' . $key . '"';
          }
     }
}


$staff_not_has_permissions = !staff_can('view', 'appointments') || !staff_can('view_own', 'appointments');

$join = [];

if ($staff_not_has_permissions && !is_staff_callbacks_responsible()) {
     array_push(
          $where,
          'AND ' . db_prefix() . 'appointly_callbacks.id IN (SELECT callbackid 
     FROM ' . db_prefix() . 'appointly_callbacks_assignees 
     WHERE ' . db_prefix() . 'appointly_callbacks_assignees.callbackid = ' . db_prefix() . 'appointly_callbacks.id 
     AND ' . db_prefix() . 'appointly_callbacks_assignees.user_id = ' . db_prefix() . 'staff.staffid)'
     );

     $join = [
          'LEFT JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid IN (SELECT user_id 
          FROM ' . db_prefix() . 'appointly_callbacks_assignees 
          WHERE ' . db_prefix() . 'appointly_callbacks_assignees.user_id = ' . get_staff_user_id() . ')',
     ];
}



$additionalSelect = [
     db_prefix() . 'appointly_callbacks.id as id',
     'message',
     get_sql_select_callback_assignees_ids() . 'as assignees_ids',
     get_sql_select_callback_asignees_full_names() . 'as assignees',
];


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
     $row = [];

     $row[] = $aRow['fullname'];

     $row[] = '<a class="callbacks_phone" href="tel:' . $aRow['phone'] . '">' . $aRow['phone'] . '</a>';

     if (staff_can('edit', 'appointments') || staff_can('create', 'appointments') || is_staff_callbacks_responsible()) {
          $outputStatus = '<div class="dropdown inline-block mleft5">';
          $outputStatus .= '<a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableCallbackStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
          $outputStatus .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><span class="label label-callback-status-' . $aRow['status'] . '">' . fetchCallbackStatusName($aRow['status']) . '</span></span>';
          $outputStatus .= '</a>';

          $outputStatus .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableCallbackStatus-' . $aRow['id'] . '">';
          foreach ($callback_statuses as $key => $callbackChangeStatus) {
               if ($aRow['status'] != $key) {
                    $updateStatus = "'{$callbackChangeStatus}'";
                    $outputStatus .= '<li>
                    <a href="#" onclick="callback_mark_status_as(' . $aRow['id'] . ',' . $key . '); return false;">
                       ' . _l('task_mark_as', $callbackChangeStatus) . '
                    </a>
                 </li>';
               }
          }
          $outputStatus .= '</ul>';
          $outputStatus .= '</div>';
          $outputStatus .= '</span>';
     } else {
          $outputStatus = '<div>';
          $outputStatus .= '<a data-toggle="tooltip" title="' .  fetchCallbackStatusName($aRow['status']) . '" href="#" style="font-size:14px;vertical-align:middle;cursor:context-menu;" class="text-dark" id="tableCallbackStatus-' . $aRow['id'] . '">';
          $outputStatus .= '<span class="label label-callback-status-' . $aRow['status'] . '">' . fetchCallbackStatusName($aRow['status']) . '</span>';
          $outputStatus .= '</a>';
          $outputStatus .= '</div>';
     }


     $row[] = $outputStatus;

     $row[] = render_callbacks_timezone($aRow['timezone']);

     $row[] = _dt($aRow['date_start']);
     $row[] = _dt($aRow['date_end']);
     $row[] = _dt($aRow['date_added']);


     $row[] = format_members_by_ids_and_names($aRow['assignees_ids'], $aRow['assignees']);


     if (staff_can('view', 'appointments') || staff_can('view_own', 'appointments') || is_staff_callbacks_responsible()) {
          $options = '<button type="button" id="' . $aRow['id'] . '" class="btn btn-primary btn-xs mleft5 client_options" onclick="viewCallback(' . $aRow['id'] . ')" data-toggle="tooltip" title="' . _l('callbacks_view_label') . '" ><i class="fa fa-eye"></i></button>';

          $options .= '<a href="tel:' . $aRow['phone'] . '" class="btn btn-primary btn-xs mleft5 client_options" data-toggle="tooltip" title="' . _l('callbacks_call_now') . '" ><i class="fa fa-phone"></i></a>';
     } else {
          $options = '';
     }

     if (staff_can('delete', 'appointments') || is_staff_callbacks_responsible()) {
          $options .= '<button type="button" class="btn btn-danger btn-xs mleft5 client_options" onclick="deleteCallback(' . $aRow['id'] . ')" data-toggle="tooltip" title="' . _l('callbacks_delete_record') . '" ><i class="fa fa-trash"></i></button>';
     }

     $row[] = $options;

     $output['aaData'][] = $row;
}
