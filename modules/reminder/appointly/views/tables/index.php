<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'subject',
    'CAST(CONCAT(date, \' \', start_hour) AS DATETIME) as date',
    'firstname as creator_firstname',
    'description',
    'finished',
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
$filters = [];
if ($this->ci->input->post('approved')) {
    $filters[] = 'AND approved = 1';
}
if ($this->ci->input->post('cancelled')) {
    $filters[] = 'AND cancelled = 1';
}
if ($this->ci->input->post('finished')) {
    $filters[] = 'AND finished = 1';
}
if ($this->ci->input->post('internal')) {
    $filters[] = 'AND (source= "internal")';
}
if ($this->ci->input->post('external')) {
    $filters[] = 'AND (source= "external")';
}
if ($this->ci->input->post('lead_related')) {
    $filters[] = 'AND (source= "lead_related")';
}
if ($this->ci->input->post('internal_staff')) {
    $filters[] = 'AND (source= "internal_staff_crm")';
}
if ($this->ci->input->post('finished')) {
    $filters[] = 'AND finished = 1';
}
if ($this->ci->input->post('not_approved')) {
    $filters[] = 'AND approved != 1';
}
if ($this->ci->input->post('upcoming')) {
    $filters[] = 'AND date > CURDATE()';
}
if ($this->ci->input->post('missed')) {
    $filters[] = 'AND date < CURDATE()';
}
if ($this->ci->input->post('recurring')) {
    $filters[] = 'AND recurring = 1';
}

if (count($filters) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filters) . ')');
}

$join = [
    'LEFT JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'appointly_appointments.created_by',
];

$additionalSelect = [
    'approved',
    'created_by',
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

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $label_class = 'primary';
    $tooltip = '';

    // Check with Perfex CRM default timezone configured in Setup->Settings->Localization
    if (date('Y-m-d H:i', strtotime($aRow['date'])) < date('Y-m-d H:i')) {
        $label_class = 'danger';
        $tooltip = 'data-toggle="tooltip" title="' . _l('appointment_missed') . '"';
    }

    $row = [];

    $hrefAttr = 'data-toggle="tooltip" title="' . _l('appointment_view_meeting') . '" href="' . admin_url('appointly/appointments/view?appointment_id=' . $aRow['id']) . '"';
    $row[] = $aRow['id'];

    $nameRow = '<a href="' . admin_url('appointly/appointments/view?appointment_id=' . $aRow['id']) . '">' . $aRow['subject'] . '</a>';

    if ($aRow['approved'] && $aRow['cancelled'] == 0) {
        $nameRow .= '<p class="text-success no-mbot">' . _l('appointment_approved') . '</p>';
    }

    $nameRow .= '<div class="row-options no-mtop">';
    $nameRow .= '<a ' . $hrefAttr . '>' . _l('view') . '</a>';
    if (
        $aRow['approved'] == 0
        && is_admin() && $aRow['cancelled'] == 0
        || $aRow['approved'] == 0
        && staff_can('view', 'appointments')
        && $aRow['cancelled'] == 0
    ) {
        $nameRow .= ' | <a class="approve_appointment" href="' . admin_url('appointly/appointments/approve?appointment_id=' . $aRow['id']) . '">' . _l('appointment_approve') . '</a>';
    }
    if (staff_can('edit', 'appointments') || staff_appointments_responsible()) {
        if ($aRow['source'] != 'internal_staff_crm') {
            $nameRow .= ' | <a href="" data-toggle="tooltip" title="' . _l('appointment_edit_meeting') . '" data-id="' . $aRow['id'] . '" onclick="appointmentUpdateModal(this); return false;">' . _l('edit') . '</a>';
        } else {
            $nameRow .= ' | <a href="" data-toggle="tooltip" title="' . _l('appointment_edit_meeting') . '" data-id="' . $aRow['id'] . '" onclick="appointmentGlobalStaffModal(this); return false;">' . _l('edit') . '</a>';
        }
    }
    // If contact id is not 0 then it means that contact is internal as for that dont show convert to lead
    $isContact = ($aRow['contact_id']) ? 0 : 1;

    // convert to task
    $nameRow .= (staff_can('create', 'tasks') && $aRow['approved'] == 1 && $aRow['source'] != 'internal_staff_crm') ?
        ' | <a data-toggle="tooltip" title="' . _l('appointments_create_task_tooltip') . '" href="#" data-customer-id="' . appointly_get_contact_customer_id($aRow['contact_id']) . '" data-source="' . $aRow['source'] . '" data-contact-id="' . $aRow['contact_id'] . '" data-name="' . $aRow['name'] . '" onclick="new_task_from_relation_appointment(this); return false;">' . _l('new_task') . '</a>'
        : '';

    // convert to lead
    $nameRow .= ($isContact && $aRow['approved'] == 1 && $aRow['source'] != 'internal_staff_crm') ?
        ' | <a data-toggle="tooltip" title="' . _l('appointments_convert_to_lead_tooltip') . '" href="#" data-name="' . $aRow['name'] . '" data-email="' . $aRow['contact_email'] . '" data-phone="' . $aRow['phone'] . '" onclick="init_appointment_lead(this);return false;">' . _l("appointments_convert_to_lead_label") . '</a>'
        : '';

    // If there is no feedback from client and if appintment is marked as finished
    if ($aRow['feedback'] !== null && $aRow['finished'] !== 1) {
        $nameRow .= ' | <a data-toggle="tooltip" title="' . _l('appointment_view_feedback') . '" href="' . admin_url('appointly/appointments/view?appointment_id=' . $aRow['id']) . '#feedback_wrapper">' . _l('appointment_view_feedback') . '</a></li>';
    } else if ($aRow['finished'] == 1) {
        $nameRow .= ' | <a onclick="request_appointment_feedback(\'' . $aRow['id'] . '\'); return false" data-toggle="tooltip" title="' . _l('appointments_request_feedback_from_client') . '" href="">' . _l('appointments_request_feedback') . '</a>';
    }

    if (staff_can('delete', 'appointments') && $aRow['created_by'] == get_staff_user_id() || staff_appointments_responsible()) {
        $nameRow .= ' | <a id="confirmDelete" data-toggle="tooltip" class="text-danger" title="' . _l('appointment_dismiss_meeting') . '" href="" onclick="deleteAppointment(' . $aRow['id'] . ',this); return false;">' . _l('delete') . '</a>';
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

    if (staff_can('edit', 'appointments') || staff_can('create', 'appointments') || staff_appointments_responsible()) {
        $currentStatus = checkAppointlyStatus($aRow);

        $outputStatus = '<div class="dropdown inline-block mleft5">';
        $outputStatus .= '<a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="appointmentStatusesDropdown' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';

        $outputStatus .= checkAppointlyStatus($aRow);

        $outputStatus .= '</a>';

        if ($aRow['finished'] != 1) {
            $outputStatus .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="appointmentStatusesDropdown' . $aRow['id'] . '">';
        }
        $needs_approval = $aRow['approved'] == 0 && $aRow['cancelled'] == 0 && is_admin() || $aRow['approved'] == 0 && $aRow['cancelled'] == 0 && staff_can('view', 'appointments');

        if ($needs_approval) {
            $outputStatus .= '<li><a href="" onclick="markAppointmentAsApproved(' . $aRow['id'] . '); return false" href="">' . _l('task_mark_as', 'Approved') . '</a></li>';
        }

        if ($aRow['cancelled'] == 0 && $aRow['finished'] == 0) {
            if ($aRow['created_by'] == get_staff_user_id() || staff_appointments_responsible()) {
                $outputStatus .= '<li><a href="" onclick="markAppointmentAsCancelled(' . $aRow['id'] . '); return false" id-"cancelAppointment">' . _l('task_mark_as', 'Cancelled') . '</a></li>';
            }
        }

        if ($aRow['finished'] == 0 && $aRow['cancelled'] == 0 && $aRow['approved'] != 0) {
            if ($aRow['created_by'] == get_staff_user_id() || staff_appointments_responsible()) {
                $outputStatus .= '<li><a href="" onclick="markAppointmentAsFinished(' . $aRow['id'] . '); return false" id="markAsFinished">' . _l('task_mark_as', 'Finished') . '</a></li>';
            }
        }

        if ($aRow['cancelled'] == 1 && $aRow['finished'] == 0) {
            if ($aRow['created_by'] == get_staff_user_id() || staff_appointments_responsible()) {
                $outputStatus .= '<li><a href="" onclick="markAppointmentAsOngoing(' . $aRow['id'] . '); return false" id-"markAppointmentAsOngoing">' . _l('task_mark_as', 'Ongoing') . '</a></li>';
            }
        }
        $outputStatus .= '</ul>';
        $outputStatus .= '</div>';
        $outputStatus .= '</span>';
    } else {
        $outputStatus = '<div>';
        $outputStatus .= '<a data-toggle="tooltip" title="' . $currentStatus . '" href="#" style="font-size:14px;vertical-align:middle;cursor:context-menu;" class="text-dark">';
        $outputStatus .= '<span class="label label-callback-status-' . $currentStatus . '">' . $currentStatus . '</span>';
        $outputStatus .= '</a>';
        $outputStatus .= '</div>';
    }

    $row[] = $outputStatus;

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
        $row[] = _l('appointment_internal_staff');
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
        $options .= '<p class="text-muted">Not added to any calendar yet.</p>'; //lang
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
