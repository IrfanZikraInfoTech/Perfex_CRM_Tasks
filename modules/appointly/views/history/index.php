<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'subject',
    'CAST(CONCAT(date, \' \', start_hour) AS DATETIME) as date',
    'firstname as creator_firstname',
    'description',
    'source'
];


$sIndexColumn = 'id';
$sTable = db_prefix() . 'appointly_appointments';

$where = [];

if (!is_admin() && !staff_appointments_responsible()) {
    array_push($where, 'AND (' . db_prefix() . 'appointly_appointments.created_by=' . get_staff_user_id() . ')
    OR ' . db_prefix() . 'appointly_appointments.id
    IN (SELECT appointment_id FROM ' . db_prefix() . 'appointly_attendees WHERE staff_id=' . get_staff_user_id() . ')');
}


$join = [
    'LEFT JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'appointly_appointments.created_by',
];

$additionalSelect = [
    'approved',
    'created_by',
    'finished',
    'lastname as creator_lastname',
    'name',
    db_prefix() . 'appointly_appointments.email as contact_email',
    db_prefix() . 'appointly_appointments.phone',
    'cancelled',
    'contact_id',
    'google_calendar_link',
    'google_added_by_id',
    'outlook_calendar_link',
    'outlook_added_by_id',
    'outlook_event_id',
    'feedback'
];
$where[] = 'AND finished = 1';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);

$output = $result['output'];
$rResult = $result['rResult'];


foreach ($rResult as $aRow) {

    $label_class = 'primary';
    $tooltip = '';

    if (date('Y-m-d H:i', strtotime($aRow['date'])) < date('Y-m-d H:i')) {
        $label_class = 'danger';
        $tooltip = 'data-toggle="tooltip" title="' . _l('appointment_missed') . '"';
    }

    $row = [];

    $hrefAttr = 'data-toggle="tooltip" title="' . _l('appointment_view_meeting') . '" href="' . admin_url('appointly/appointments/view?appointment_id=' . $aRow['id']) . '"';
    $row[] = $aRow['id'];

    $nameRow = '<a href="' . admin_url('appointly/appointments/view?appointment_id=' . $aRow['id']) . '">' . $aRow['subject'] . '</a>';

    if ($aRow['approved'] && $aRow['cancelled'] == 0 && $aRow['finished'] == 1) {
        $nameRow .= '<p class="text-success no-mbot">' . _l('appointment_finished') . '</p>';
    }

    $nameRow .= '<div class="row-options no-mtop">';

    $nameRow .= '<a ' . $hrefAttr . '>' . _l('view') . '</a>';

    if (staff_can('edit', 'appointments') || staff_appointments_responsible()) {
        $nameRow .= ' | <a data-toggle="tooltip" title="' . _l('appointment_edit_history_notes') . '" data-id="' . $aRow['id'] . '" onclick="editAppointmentNotes(this)" style="cursor:pointer;">' . _l('appointment_edit_history_notes') . '</a>';
    }

    // If there is no feedback from client and if appintment is marked as finished
    if ($aRow['feedback'] !== null && $aRow['finished'] !== 1) {
        $nameRow .= ' | <a data-toggle="tooltip" title="' . _l('appointment_view_feedback') . '" href="' . admin_url('appointly/appointments/view?appointment_id=' . $aRow['id']) . '#feedback_wrapper">' . _l('appointment_view_feedback') . '</a></li>';
    } else if ($aRow['finished'] == 1) {
        $nameRow .= ' | <a onclick="request_appointment_feedback(\'' . $aRow['id'] . '\')" data-toggle="tooltip" title="' . _l('appointments_request_feedback_from_client') . '" href="#">' . _l('appointments_request_feedback') . '</a>';
    }

    $nameRow .= '</div>';


    $row[] = $nameRow;

    $row[] = '<span  ' . $tooltip . ' class="label label-' . $label_class . '">' . _dt($aRow['date']) . '</span>';

    if ($aRow['creator_firstname']) {
        $staff_fullname = $aRow['creator_firstname'] . ' ' . $aRow['creator_lastname'];

        $row[] = '<a class="initiated_by" target="_blank" href="' . admin_url() . "profile/" . $aRow["created_by"] . '"><img src="' . staff_profile_image_url($aRow["created_by"], "small") . '" data-toggle="tooltip" data-title="' . $staff_fullname . '" class="staff-profile-image-small mright5" data-original-title="" title="' . $staff_fullname . '">' . $staff_fullname . '</a>';
    } else {
        $row[] = $aRow['name'];
    }

    $row[] = $aRow['description'];

    if ($aRow['source'] == 'external') {
        $row[] = _l('appointments_source_external_label');
    }
    if ($aRow['source'] == 'internal') {
        $row[] = _l('appointments_source_internal_label');
    }
    if ($aRow['source'] == 'lead_related') {
        $row[] = _l('lead');
    }
    if ($aRow['source'] == 'internal_staff_crm') {
        $row[] = _l('appointment_ism_label');
    }

    $options = '';
    $_google_calendar_link = $aRow['google_calendar_link'] !== null && $aRow['google_added_by_id'] == get_staff_user_id();
    $_outlook_calendar_link = $aRow['outlook_calendar_link'] !== null && $aRow['outlook_added_by_id'] == get_staff_user_id();

    $options .= '<div class="text-center">';

    if ($_google_calendar_link) {
        $options .= '<a data-toggle="tooltip" title="' . _l('appointment_open_google_calendar') . '" href="' . $aRow['google_calendar_link'] . '" target="_blank" class="mleft10 calendar_list"><i class="fa fa-google" aria-hidden="true"></i></a>';
    }

    if ($_outlook_calendar_link) {
        $options .= '<a data-outlook-id="' . $aRow['outlook_event_id'] . '" id="outlookLink_' . $aRow['id'] . '" data-toggle="tooltip" title="' . _l('appointment_open_outlook_calendar') . '" href="' . $aRow['outlook_calendar_link'] . '" target="_blank" class="mleft5 calendar_list float-right"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
    }
    if (!$_google_calendar_link && !$_outlook_calendar_link) {
        $options .= '<p class="text-muted">' . _l('appointment_not_added_to_calendars_yet') . '</p>'; #lang
    }

    $options .= '</div>';


    $row['DT_RowId'] = 'appointment_id' . $aRow['id'];

    if (isset($row['DT_RowClass'])) {
        $row['DT_RowClass'] .= ' has-row-options';
    } else {
        $row['DT_RowClass'] = 'has-row-options';
    }

    $row[] = $options;

    $output['aaData'][] = $row;
}
