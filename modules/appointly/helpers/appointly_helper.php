<?php

defined('BASEPATH') or exit('No direct script access allowed');

hooks()->add_action('app_admin_head', 'appointly_head_components');
hooks()->add_action('app_admin_footer', 'appointly_footer_components');
hooks()->add_action('after_email_templates', 'add_appointly_email_templates');
hooks()->add_action('clients_init', 'appointly_clients_area_schedule_appointment');

/**
 * DOMDocument must be loaded and Perfex CRM version 2.8.0 and UP
 */
if (in_array('dom', get_loaded_extensions()) && get_instance()->app->get_current_db_version() >= 280) {

    hooks()->add_filter('staff_table_row', 'staff_table_row_func', 10, 2);
    hooks()->add_filter('leads_table_row_data', 'leads_table_row_data_func', 10, 2);
    hooks()->add_filter('all_contacts_table_row', 'all_contacts_table_row_func', 10, 2);


    /**
     * @param $row
     * @param $db
     *
     * @return mixed
     */
    if ( ! function_exists('all_contacts_table_row_func')) {

        function all_contacts_table_row_func($row, $db)
        {
            $row[0] = domCreateNewElement($row[0], [
                'onClick'   => 'appointmentGlobalLeadsContactsModalNew(this,true);return false;',
                'data-type' => 'contact',
                'href'      => '#',
                'id'        => $db['id'],
            ]);

            return $row;
        }
    }

    /**
     * @param $row
     * @param $db
     *
     * @return mixed
     */
    if ( ! function_exists('staff_table_row_func')) {

        function staff_table_row_func($row, $db)
        {
            $row[0] = domCreateNewElement($row[0], [
                'onClick' => 'appointmentGlobalStaffEditModal(this,true);return false;',
                'href'    => '#',
                'id'      => $db['staffid'],
            ]);

            return $row;
        }
    }

    /**
     * @param $row
     * @param $db
     *
     * @return mixed
     */
    if ( ! function_exists('leads_table_row_data_func')) {

        function leads_table_row_data_func($row, $db)
        {
            $row[2] = domCreateNewElement($row[2], [
                'onClick'   => 'appointmentGlobalLeadsContactsModalNew(this,true);return false;',
                'data-type' => 'lead',
                'href'      => '#',
                'id'        => $db['id'],
            ]);

            return $row;
        }
    }

    /**
     * Create new element in has-row-options for tables.
     *
     * @param        $row
     * @param        $data
     * @param string $title
     * @param string $text
     *
     * @return string
     */
    if ( ! function_exists('domCreateNewElement')) {

        function domCreateNewElement($row, $data, $title = '', $text = '')
        {
            $title = _l("appointment_label");
            $text = _l("appointment_create_href");

            if ( ! isset($data['title'])) {
                $data['title'] = $title;
            }

            if ( ! isset($data['text'])) {
                $data['text'] = $text;
            }

            libxml_use_internal_errors(true);
            $doc = new DOMDocument();

            $doc->loadHTML(mb_convert_encoding($row, 'HTML-ENTITIES', 'UTF-8'));

            $rowOptions = $doc->getElementsByTagName('div')['row-options'];
            $appended = $doc->createElement('a', $text);

            foreach ($data as $attr => $value) {
                $appended->setAttribute($attr, $value);
            }

            $rowOptions->appendChild($appended);

            return $doc->saveHTML();
        }
    }
}

/**
 * Schedule appointment menu items in client area
 */
if ( ! function_exists('appointly_clients_area_schedule_appointment')) {

    function appointly_clients_area_schedule_appointment()
    {
        // Item is available for all clients if enabled in Setup->Settings->Appointment
        if (get_option('appointly_show_clients_schedule_button') == 1 && ! is_client_logged_in()) {
            add_theme_menu_item('schedule-appointment-id', [
                'name'     => _l('appointly_schedule_new_appointment'),
                'href'     => site_url('appointly/appointments_public/form?col=col-md-8+col-md-offset-2'),
                'position' => 10,
            ]);
        }

        // Item is available for logged in clients if enabled in Setup->Settings->Appointment
        if (is_client_logged_in()) {
            if (get_option('appointly_tab_on_clients_page') == 1) {
                add_theme_menu_item('schedule-appointment-logged-in-id', [
                    'name'     => _l('appointly_schedule_new_appointment'),
                    'href'     => site_url('appointly/appointments_public/form?col=col-md-8+col-md-offset-2'),
                    'position' => 1,
                ]);
            }
        }
    }
}

/**
 * Init appointly email templates and assign languages.
 */
if ( ! function_exists('add_appointly_email_templates')) {

    function add_appointly_email_templates()
    {
        $CI = &get_instance();

        $data['appointly_templates'] = $CI->emails_model->get(['type' => 'appointly', 'language' => 'english']);

        $CI->load->view('appointly/email_templates', $data);
    }
}

hooks()->add_filter('available_tracking_templates', 'add_appointment_approved_email_tracking');

/**
 * Email tracking
 *
 * @param $slugs
 *
 * @return mixed
 */
if ( ! function_exists('add_appointment_approved_email_tracking')) {

    function add_appointment_approved_email_tracking($slugs)
    {
        if ( ! in_array('appointment-approved-to-contact', $slugs)) {
            array_push($slugs, 'appointment-approved-to-contact');
        }

        return $slugs;
    }
}

/**
 * Injects theme CSS.
 */
if ( ! function_exists('appointly_head_components')) {

    function appointly_head_components()
    {
        echo '<link href="' . module_dir_url(APPOINTLY_MODULE_NAME, 'assets/css/styles.css?v=' . time()) . '"  rel="stylesheet" type="text/css" >';
    }
}

/**
 * Injects theme JS for global modal.
 */
if ( ! function_exists('appointly_footer_components')) {

    function appointly_footer_components()
    {
        echo '<script src="' . module_dir_url(APPOINTLY_MODULE_NAME, 'assets/js/global.js?v=' . time()) . '"  type="text/javascript"></script>';
    }
}

/**
 * Fetches from database all staff assigned customers
 * If admin fetches all customers.
 *
 * @return array
 */
if ( ! function_exists('appointly_get_staff_customers')) {

    function appointly_get_staff_customers()
    {
        $CI = &get_instance();

        $staffCanViewAllClients = staff_can('view', 'customers');

        $CI->db->select('firstname, lastname, ' . db_prefix() . 'contacts.id as contact_id, ' . get_sql_select_client_company());
        $CI->db->where(db_prefix() . 'clients.active', '1');
        $CI->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid=' . db_prefix() . 'contacts.userid', 'left');
        $CI->db->select(db_prefix() . 'clients.userid as client_id');

        if ( ! $staffCanViewAllClients) {
            $CI->db->where('(' . db_prefix() . 'clients.userid IN (SELECT customer_id FROM ' . db_prefix() . 'customer_admins WHERE staff_id=' . get_staff_user_id() . '))');
        }

        $result = $CI->db->get(db_prefix() . 'contacts')->result_array();

        foreach ($result as &$contact) {
            if ($contact['company'] == $contact['firstname'] . ' ' . $contact['lastname']) {
                $contact['company'] = _l('appointments_individual_contact');
            } else {
                $contact['company'] = "" . _l('appointments_company_for_select') . "(" . $contact['company'] . ")";
            }
        }

        if ($CI->db->affected_rows() !== 0) {
            return $result;
        } else {
            return [];
        }
    }
}


/**
 * Fetch current appointment data.
 *
 * @param [string] $appointment_id
 *
 * @return array
 */
if ( ! function_exists('fetch_appointment_data')) {

    function fetch_appointment_data($appointment_id)
    {
        $CI = &get_instance();

        $appointment = $CI->apm->get_appointment_data($appointment_id);

        if ( ! empty($appointment)) {
            $CI->load->model('staff_model');

            $appointment['selected_staff'] = array_map(function ($staff)
            {
                return $staff['staffid'];
            }, $appointment['attendees']);

            if ($appointment['source'] !== 'lead_related') {
                $appointment['selected_contact'] = $appointment['contact_id'];

                if ( ! empty($appointment['selected_contact'])) {
                    $appointment['details'] = get_appointment_contact_details($appointment['selected_contact']);
                }
            } else {
                if (isset($appointment['address'])) {
                    unset($appointment['address']);
                }
            }
            $appointment['appointment_id'] = $appointment_id;

            return $appointment;
        }

        return [];
    }
}

/**
 * Convert dates for database insertion
 *
 * @param string $date
 *
 * @return array
 */
if ( ! function_exists('convertDateForDatabase')) {

    function convertDateForDatabase($date)
    {
        $date = to_sql_date($date, true);

        return [
            'date'       => date('Y-m-d', strtotime($date)),
            'start_hour' => date('H:i', strtotime($date)),
        ];
    }
}


/**
 * Convert dates for database insertion
 *
 * @param string $date
 *
 * @param        $time
 *
 * @return array
 */
if ( ! function_exists('convertDateForValidation')) {

    function convertDateForValidation($date, $time)
    {
        $date = to_sql_date($date, true);

        $dt = 'H:i';

        if ($time == '12') $dt = 'g:i A';

        $toTime = strtotime($date);

        return [
            'date'       => date('Y-m-d', $toTime),
            'start_hour' => date($dt, $toTime),
        ];
    }
}


/**
 * Recurring update appointment data validation
 *
 * @param array $original
 * @param array $data
 *
 * @return array
 */
if ( ! function_exists('validateRecurringData')) {

    function validateRecurringData(array $original, array $data)
    {
        // Recurring appointment set to NO, Cancelled
        if ($original['repeat_every'] != '' && $data['repeat_every'] == '') {
            $data['cycles'] = 0;
            $data['total_cycles'] = 0;
            $data['last_recurring_date'] = null;
        }

        if ($data['repeat_every'] != '') {
            $data['recurring'] = 1;
            if ($data['repeat_every'] == 'custom') {
                $data['repeat_every'] = $data['repeat_every_custom'];
                $data['recurring_type'] = $data['repeat_type_custom'];
                $data['custom_recurring'] = 1;
            } else {
                $_temp = explode('-', $data['repeat_every']);
                $data['recurring_type'] = $_temp[1];
                $data['repeat_every'] = $_temp[0];
                $data['custom_recurring'] = 0;
            }
        } else {
            $data['recurring'] = 0;
        }

        $data['cycles'] = ! isset($data['cycles']) || $data['recurring'] == 0 ? 0 : $data['cycles'];

        unset($data['repeat_type_custom']);
        unset($data['repeat_every_custom']);
        return $data;
    }
}

/**
 * Recurring appointment insert data validation
 *
 * @param array $data
 *
 * @return array
 */
if ( ! function_exists('validateInsertRecurring')) {

    function validateInsertRecurring(array $data)
    {
        if (isset($data['repeat_every']) && ! empty($data['repeat_every'])) {
            $data['recurring'] = 1;
            if ($data['repeat_every'] == 'custom') {
                $data['repeat_every'] = $data['repeat_every_custom'];
                $data['recurring_type'] = $data['repeat_type_custom'];
                $data['custom_recurring'] = 1;
            } else {
                $_temp = explode('-', $data['repeat_every']);
                $data['recurring_type'] = $_temp[1];
                $data['repeat_every'] = $_temp[0];
                $data['custom_recurring'] = 0;
            }
        } else {
            $data['recurring'] = 0;
        }
        unset($data['repeat_type_custom']);
        unset($data['repeat_every_custom']);
        return $data;
    }
}


/**
 * Send email and push notifications for newly created recurring appointment
 *
 * @param string $appointment_id
 *
 * @return void
 */
if ( ! function_exists('newRecurringAppointmentNotifications')) {

    function newRecurringAppointmentNotifications($appointment_id)
    {
        $CI = &get_instance();
        $CI->db->where('id', $appointment_id);

        // get appointment
        $appointment = $CI->db->get(db_prefix() . 'appointly_appointments')->row_array();

        if ($CI->db->affected_rows() > 0) {
            // get attendees
            $CI->db->where('appointment_id', $appointment_id);
            $CI->db->join('staff', db_prefix() . 'staff.staffid=' . db_prefix() . 'appointly_attendees.staff_id');
            $appointment['attendees'] = $CI->db->get('appointly_attendees')->result_array();
        }

        $notified_users = [];

        $attendees = $appointment['attendees'];

        foreach ($attendees as $staff) {

            if ($staff['staffid'] === get_staff_user_id()) {
                continue;
            }

            add_notification([
                'description' => 'appointment_recurring_re_created',
                'touserid'    => $staff['staffid'],
                'fromcompany' => true,
                'link'        => 'appointly/appointments/view?appointment_id=' . $appointment_id,
            ]);

            $notified_users[] = $staff['staffid'];

            send_mail_template('appointly_appointment_recurring_recreated_to_staff',
                'appointly',
                array_to_object($appointment),
                array_to_object($staff)
            );
        }

        pusher_trigger_notification(array_unique($notified_users));

        $template = mail_template('appointly_appointment_recurring_recreated_to_contacts', 'appointly', array_to_object($appointment));

        $template->send();
    }
}


/**
 * Helper function to handle reminder fields
 *
 * @param array $data
 *
 * @return array
 */
if ( ! function_exists('handleDataReminderFields')) {

    function handleDataReminderFields($data)
    {

        (isset($data['by_email']) && $data['by_email'] == 'on')
            ? $data['by_email'] = '1'
            : $data['by_email'] = null;

        (isset($data['by_sms']) && $data['by_sms'] == 'on')
            ? $data['by_sms'] = '1'
            : $data['by_sms'] = null;

        if ($data['by_email'] === null && $data['by_sms'] === null) {
            $data['reminder_before'] = null;
            $data['reminder_before_type'] = null;
        }

        if (isset($data['by_email']) || isset($data['by_sms'])) {
            if ($data['reminder_before'] == '') {
                $data['reminder_before'] = '30';
            }
        }
        return $data;
    }
}

/**
 * Helper redirect function with alert message.
 *
 * @param [string] $type    'success' | 'danger'
 * @param [string] $message
 */
if ( ! function_exists('redirect_after_event')) {

    function appointly_redirect_after_event($type, $message, $path = null)
    {
        $CI = &get_instance();

        $CI->session->set_flashdata('message-' . $type . '', $message);

        if ($path) {
            redirect('admin/appointly/' . $path);
        } else {
            redirect('admin/appointly/appointments');
        }
    }
}

/**
 * Helper function to get contact specific data.
 *
 * @param [string] $contact_id
 *
 * @return array
 */
if ( ! function_exists('get_appointment_contact_details')) {

    function get_appointment_contact_details($contact_id)
    {
        $CI = &get_instance();
        $CI->db->select('email, userid, phonenumber as phone, CONCAT(firstname, " " , lastname) AS full_name ');
        $CI->db->where('id', $contact_id);
        $contact = $CI->db->get(db_prefix() . 'contacts')->row_array();
        $contact['company_name'] = get_company_name($contact['userid']);

        return $contact;
    }
}

/**
 * Get staff.
 *
 * @param [string] $staffid
 *
 * @return array
 */
if ( ! function_exists('appointly_get_staff')) {

    function appointly_get_staff($staffid)
    {
        $CI = &get_instance();
        $CI->db->where('staffid', $staffid);

        return $CI->db->get(db_prefix() . 'staff')->row_array();
    }
}

/**
 * Include appointment view
 *
 * @param $path
 * @param $name
 *
 * @return mixed
 */
if ( ! function_exists('include_appointment_view')) {

    function include_appointment_view($path, $name)
    {
        return require 'modules/appointly/views/' . $path . '/' . $name . '.php';
    }
}


/**
 * Get projects summary
 *
 * @return array
 */
if ( ! function_exists('get_appointments_summary')) {

    function get_appointments_summary()
    {
        $CI = &get_instance();

        if ( ! is_admin() && ! staff_appointments_responsible()) {
            $CI->db->where('(' . db_prefix() . 'appointly_appointments.created_by=' . get_staff_user_id() . ')
        OR ' . db_prefix() . 'appointly_appointments.id
        IN (SELECT appointment_id FROM ' . db_prefix() . 'appointly_attendees WHERE staff_id=' . get_staff_user_id() . ')');
        }

        $appointments = $CI->db->get(db_prefix() . 'appointly_appointments')->result_array();

        $data = [
            'total_appointments' => 0,
            'upcoming'           => [
                'total' => 0,
                'name'  => _l('appointment_upcoming'),
                'color' => 'rgb(86, 111, 236)'
            ],
            'not_approved'       => [
                'total' => 0,
                'name'  => _l('appointment_pending_approval'),
                'color' => 'rgb(236, 169, 86)'
            ],
            'cancelled'          => [
                'total' => 0,
                'name'  => _l('appointment_cancelled'),
                'color' => 'rgba(244, 3, 47, 0.59)'
            ],
            'missed'             => [
                'total' => 0,
                'name'  => _l('appointment_missed_label'),
                'color' => 'rgba(244, 3, 47, 0.59)'
            ],
            'finished'           => [
                'total' => 0,
                'name'  => _l('appointment_finished'),
                'color' => 'rgb(132, 197, 41)'
            ]
        ];

        if ($CI->db->count_all_results() > 0) {
            $data['total_appointments'] = count($appointments);

            foreach ($appointments as $appointment) {
                if ($appointment['cancelled']) {
                    $data['cancelled']['total'] = $data['cancelled']['total'] + 1;
                } else if (
                    ! $appointment['approved']
                    && ! $appointment['cancelled']
                    && date('Y-m-d H:i', strtotime($appointment['date'] . ' ' . $appointment['start_hour'])) > date('Y-m-d H:i')
                ) {
                    $data['not_approved']['total'] = $data['not_approved']['total'] + 1;
                } else if (
                    ! $appointment['finished'] && ! $appointment['cancelled']
                    && date('Y-m-d H:i', strtotime($appointment['date'] . ' ' . $appointment['start_hour'])) < date('Y-m-d H:i')
                ) {
                    $data['missed']['total'] = $data['missed']['total'] + 1;
                } else if ( ! $appointment['finished'] && ! $appointment['cancelled']) {
                    $data['upcoming']['total'] = $data['upcoming']['total'] + 1;
                } else {
                    $data['finished']['total'] = $data['finished']['total'] + 1;
                }
            }
        }

        return $data;
    }
}


/**
 * Get staff current role.
 *
 * @param $role_id
 *
 * @return mixed
 */
if ( ! function_exists('get_appointly_staff_userrole')) {

    function get_appointly_staff_userrole($role_id)
    {
        $CI = &get_instance();
        $CI->db->select('name');
        $CI->db->where('roleid', $role_id);

        $result = $CI->db->get(db_prefix() . 'roles')->row_array();

        if ($result !== null) {
            return $result['name'];
        }
    }
}


/**
 *
 * Get contact user id from contacts table
 * Used for when creating new task in appointments.
 *
 * @param $contact_id
 *
 * @return mixed
 */
if ( ! function_exists('appointly_get_contact_customer_id')) {

    function appointly_get_contact_customer_id($contact_id)
    {
        $CI = &get_instance();
        $CI->db->select('userid');
        $CI->db->where('id', $contact_id);
        $result = $CI->db->get(db_prefix() . 'contacts')->row_array();
        if ($result !== null) {
            return $result['userid'];
        }
    }
}

/**
 * Get all appointment types.
 *
 * @return array
 */
if ( ! function_exists('get_appointment_types')) {

    function get_appointment_types()
    {
        $CI = &get_instance();

        return $CI->db->get(db_prefix() . 'appointly_appointment_types')->result_array();
    }
}

/**
 * Get single appointment type.
 *
 * @param $type_id
 *
 * @return mixed
 */
if ( ! function_exists('get_appointment_type')) {

    function get_appointment_type($type_id)
    {
        $CI = &get_instance();
        $CI->db->select('type');
        $CI->db->where('id', $type_id);
        $result = $CI->db->get(db_prefix() . 'appointly_appointment_types')->row_array();
        if ($result !== null) {
            return $result['type'];
        }
    }
}

/**
 * Get appointment assigned color type.
 *
 * @param $type_id
 *
 * @return mixed
 */
if ( ! function_exists('get_appointment_color_type')) {

    function get_appointment_color_type($type_id)
    {
        $CI = &get_instance();
        $CI->db->where('id', $type_id);
        $result = $CI->db->get(db_prefix() . 'appointly_appointment_types')->row_array();
        if ($result !== null) {
            return $result['color'];
        }
        return '';
    }
}

/**
 * Get table filters
 *
 * @return array
 */
if ( ! function_exists('get_appointments_table_filters')) {

    function get_appointments_table_filters()
    {
        return [
            [
                'id'     => 'all',
                'status' => 'All'
            ],
            [
                'id'     => 'approved',
                'status' => _l('appointment_approved')
            ],
            [
                'id'     => 'not_approved',
                'status' => _l('appointment_not_approved')
            ],
            [
                'id'     => 'cancelled',
                'status' => _l('appointment_cancelled')
            ],
            [
                'id'     => 'finished',
                'status' => _l('appointment_finished')
            ],
            [
                'id'     => 'upcoming',
                'status' => _l('appointment_upcoming')
            ],
            [
                'id'     => 'missed',
                'status' => _l('appointment_missed_label')
            ],
            [
                'id'     => 'internal',
                'status' => _l('appointment_internal')
            ]
            ,
            [
                'id'     => 'external',
                'status' => _l('appointment_external')
            ]
            ,
            [
                'id'     => 'recurring',
                'status' => _l('appointment_recurring')
            ]
            ,
            [
                'id'     => 'lead_related',
                'status' => _l('appointment_lead_related')
            ],
            [
                'id'     => 'internal_staff',
                'status' => _l('appointment_internal_staff')
            ]
        ];
    }
}

/**
 * Get staff or contact email.
 *
 * @param        $id
 * @param string $type
 *
 * @return mixed
 */
if ( ! function_exists('appointly_get_user_email')) {

    function appointly_get_user_email($id, $type = 'staff')
    {
        $CI = &get_instance();
        $CI->db->select('email');
        $table = 'staff';
        $selector = 'staffid';

        if ($type == 'contact') {
            $table = 'contacts';
            $selector = 'id';
        }

        $CI->db->where($selector, $id);
        $result = $CI->db->get(db_prefix() . $table)->row_array();
        if ($result !== null) {
            return $result['email'];
        }
    }
}

/**
 * Insert new appointment to google calendar.
 *
 * @param $data
 * @param $attendees
 *
 * @return array
 *
 * @throws Exception
 */
if ( ! function_exists('insertAppointmentToGoogleCalendar')) {

    function insertAppointmentToGoogleCalendar($data, $attendees)
    {
        if (appointlyGoogleAuth()) {
            $dateForGoogleCalendar = new DateTime(to_sql_date($data['date'], true));

            $data['date'] = date_format($dateForGoogleCalendar, 'Y-m-d\TH:i:00');

            $gmail_guests = [];
            $insertDate = $data['date'];
            $gmail_attendees = $attendees;

            foreach ($gmail_attendees as $attendee) {
                $gmail_guests[] = ['email' => appointly_get_user_email($attendee)];
            }

            if ( ! empty($data['contact_id']) && $data['source'] != 'lead_related') {
                $gmail_guests[] = ['email' => appointly_get_user_email($data['contact_id'], 'contact')];
            } else {
                if (isset($data['email'])) {
                    $gmail_guests[] = ['email' => $data['email']];
                }
            }


            $response = get_instance()->googlecalendar->addEvent([
                'summary'     => $data['subject'],
                'location'    => $data['address'],
                'description' => $data['description'],
                'start'       => $insertDate,
                'end'         => $insertDate,
                'attendees'   => $gmail_guests
            ], 'primary');

            $return_data = [];
            if ($response) {

                $return_data['google_event_id'] = $response['id'];
                $return_data['htmlLink'] = $response['htmlLink'];
                $return_data['hangoutLink'] = $response['hangoutLink'];
                $return_data['google_added_by_id'] = get_staff_user_id();

                return $return_data;
            }
        }
        return [];
    }
}

/**
 * @param $data
 *
 * @return array
 * @throws \Exception
 */
if ( ! function_exists('updateAppointmentToGoogleCalendar')) {

    function updateAppointmentToGoogleCalendar($data)
    {
        if (appointlyGoogleAuth()) {
            $dateForGoogleCalendar = new DateTime(to_sql_date($data['date'], true));

            $data['date'] = date_format($dateForGoogleCalendar, 'Y-m-d\TH:i:00');

            $insertDate = $data['date'];

            $gmail_guests = [];
            $gmail_attendees = $data['attendees'];

            foreach ($gmail_attendees as $attendee) {
                $gmail_guests[] = ['email' => appointly_get_user_email($attendee)];
            }

            if ( ! empty($data['contact_id']) && $data['source'] != 'lead_related') {
                $gmail_guests[] = ['email' => appointly_get_user_email($data['contact_id'], 'contact')];
            } else if (isset($data['selected_contact']) && $data['source'] != 'lead_related') {
                $gmail_guests[] = ['email' => appointly_get_user_email($data['selected_contact'], 'contact')];
            } else if ($data['source'] != 'lead_related') {
                if (isset($data['email'])) {
                    $gmail_guests[] = ['email' => $data['email']];
                }
            }

            $response = get_instance()->googlecalendar->updateEvent($data['google_event_id'], [
                'summary'     => $data['subject'],
                'location'    => $data['address'],
                'description' => $data['description'],
                'start'       => $insertDate,
                'end'         => $insertDate,
                'attendees'   => $gmail_guests
            ]);

            if ($response) {
                $return_data = [];
                if (isset($response['hangoutLink'])) {
                    $return_data['google_meet_link'] = $response['hangoutLink'];
                }
                $return_data['google_event_id'] = $response['id'];
                $return_data['htmlLink'] = $response['htmlLink'];

                return $return_data;
            }
        }
        return [];
    }
}

/**
 * Check if user is authenticated with google calendar
 * Refresh access token.
 *
 * @return bool
 */
if ( ! function_exists('appointlyGoogleAuth')) {

    function appointlyGoogleAuth()
    {
        $CI = &get_instance();
        $CI->load->model('appointly/googlecalendar');

        $account = $CI->googlecalendar->getAccountDetails();

        if ( ! $account) return false;

        $newToken = '';

        if ($account) {
            $account = $account[0];

            $currentToken = [
                'access_token' => $account->access_token,
                'expires_in'   => $account->expires_in
            ];

            $CI->googleplus
                ->client
                ->setAccessToken($currentToken);

            $refreshToken = $account->refresh_token;
            // renew 5 minutes before token expire
            if ($account->expires_in <= time() + 300) {
                if ($CI->googleplus->isAccessTokenExpired()) {
                    $CI->googleplus
                        ->client
                        ->setAccessToken($currentToken);
                }

                if ($refreshToken) {
                    // { "error": "invalid_grant", "error_description": "Token has been expired or revoked." }
                    try {
                        $newToken = $CI->googleplus->client->refreshToken($refreshToken);
                    } catch (Exception $e) {
                        if ($e->getCode() === 400) {
                            return false;
                        } else if ($e->getCode() === 401) {
                            return false;
                        }
                    }

                    $CI->googleplus
                        ->client
                        ->setAccessToken($newToken);

                    if ($newToken) {
                        $CI->googlecalendar->saveNewTokenValues($newToken);
                    }
                }
            } else {
                try {
                    $newToken = $CI->googleplus->client->refreshToken($refreshToken);
                } catch (Exception $e) {
                    if ($e->getCode() === 400) {
                        return false;
                    } else if ($e->getCode() === 401) {
                        return false;
                    }
                }
            }

            $CI->googleplus
                ->client
                ->setAccessToken(($newToken !== '') ? $newToken : $account->access_token);
        }

        if ($CI->googleplus->client->getAccessToken()) {
            return $CI->googleplus->client->getAccessToken();
        } else {
            return false;
        }
    }
}

/**
 * @return array
 */
if ( ! function_exists('getAppointlyUserMeta')) {

    function getAppointlyUserMeta($data = [])
    {
        $data['appointly_show_summary'] = get_meta('staff', get_staff_user_id(), 'appointly_show_summary');

        $data['appointly_default_table_filter'] = get_meta('staff', get_staff_user_id(), 'appointly_default_table_filter');

        return $data;
    }
}

/**
 * Handle appointly user meta
 *
 * @param $meta
 *
 * @return void
 */
if ( ! function_exists('handleAppointlyUserMeta')) {

    function handleAppointlyUserMeta($meta)
    {
        foreach ($meta as $key => $value) {
            update_meta('staff', get_staff_user_id(), $key, $value);
        }
    }
}


/**
 * Get appointment hours
 *
 * @return \string[][]
 */
if ( ! function_exists('getAppointmentHours')) {

    function getAppointmentHours()
    {
        return [
            ['value' => '00:00', 'name' => '00:00 AM'],
            ['value' => '00:30', 'name' => '00:30 AM'],
            ['value' => '01:00', 'name' => '01:00 AM'],
            ['value' => '01:30', 'name' => '01:30 AM'],
            ['value' => '02:00', 'name' => '02:00 AM'],
            ['value' => '02:30', 'name' => '02:30 AM'],
            ['value' => '03:00', 'name' => '03:00 AM'],
            ['value' => '03:30', 'name' => '03:30 AM'],
            ['value' => '04:00', 'name' => '04:00 AM'],
            ['value' => '04:30', 'name' => '04:30 AM'],
            ['value' => '05:00', 'name' => '05:00 AM'],
            ['value' => '05:30', 'name' => '05:30 AM'],
            ['value' => '06:00', 'name' => '06:00 AM'],
            ['value' => '06:30', 'name' => '06:30 AM'],
            ['value' => '07:00', 'name' => '07:00 AM'],
            ['value' => '07:30', 'name' => '07:30 AM'],
            ['value' => '08:00', 'name' => '08:00 AM'],
            ['value' => '08:30', 'name' => '08:30 AM'],
            ['value' => '09:00', 'name' => '09:00 AM'],
            ['value' => '09:30', 'name' => '09:30 AM'],
            ['value' => '10:00', 'name' => '10:00 AM'],
            ['value' => '10:30', 'name' => '10:30 AM'],
            ['value' => '11:00', 'name' => '11:00 AM'],
            ['value' => '11:30', 'name' => '11:30 AM'],
            ['value' => '12:00', 'name' => '12:00 PM'],
            ['value' => '12:30', 'name' => '12:30 PM'],
            ['value' => '13:00', 'name' => '13:00 PM'],
            ['value' => '13:30', 'name' => '13:30 PM'],
            ['value' => '14:00', 'name' => '14:00 PM'],
            ['value' => '14:30', 'name' => '14:30 PM'],
            ['value' => '15:00', 'name' => '15:00 PM'],
            ['value' => '15:30', 'name' => '15:30 PM'],
            ['value' => '16:00', 'name' => '16:00 PM'],
            ['value' => '16:30', 'name' => '16:30 PM'],
            ['value' => '17:00', 'name' => '17:00 PM'],
            ['value' => '17:30', 'name' => '17:30 PM'],
            ['value' => '18:00', 'name' => '18:00 PM'],
            ['value' => '18:30', 'name' => '18:30 PM'],
            ['value' => '19:00', 'name' => '19:00 PM'],
            ['value' => '19:30', 'name' => '19:30 PM'],
            ['value' => '20:00', 'name' => '20:00 PM'],
            ['value' => '20:30', 'name' => '20:30 PM'],
            ['value' => '21:00', 'name' => '21:00 PM'],
            ['value' => '21:30', 'name' => '21:30 PM'],
            ['value' => '22:00', 'name' => '22:00 PM'],
            ['value' => '22:30', 'name' => '22:30 PM'],
            ['value' => '23:00', 'name' => '23:00 PM'],
            ['value' => '23:30', 'name' => '23:30 PM']
        ];
    }
}

/**
 * Get appointment default feedbacks.
 *
 * @return array
 */
if ( ! function_exists('getAppointmentsFeedbacks')) {

    function getAppointmentsFeedbacks()
    {
        return [
            ['value' => '0', 'name' => _l('ap_feedback_not_sure')],
            ['value' => '1', 'name' => _l('ap_feedback_the_worst')],
            ['value' => '2', 'name' => _l('ap_feedback_bad')],
            ['value' => '3', 'name' => _l('ap_feedback_not_bad')],
            ['value' => '4', 'name' => _l('ap_feedback_good')],
            ['value' => '5', 'name' => _l('ap_feedback_very_good')],
            ['value' => '6', 'name' => _l('ap_feedback_extremely_good')],
        ];
    }
}

/**
 * Renders appointment feedbacks html
 *
 * @param      $appointment
 * @param bool $fallback
 *
 * @return string
 */
if ( ! function_exists('renderAppointmentFeedbacks')) {

    function renderAppointmentFeedbacks($appointment, $fallback = false)
    {
        $appointmentFeedbacks = getAppointmentsFeedbacks();

        if ($fallback && is_string($appointment)) {
            $CI = &get_instance();
            $appointment = $CI->apm->get_appointment_data($appointment);
        }
        $html = '<div class="col-lg-12 col-xs-12 mtop20 text-center" id="feedback_wrapper">';
        $html .= '<span class="label label-default" style="line-height: 30px;">' . _l('appointment_feedback_label') . '</span><br>';

        if ($appointment['feedback'] !== null && ! is_staff_logged_in()) {
            $html = '<span class="label label-primary" style="line-height: 30px;">' . _l('appointment_feedback_label_current') . '</span><br>';
        }

        if ($fallback) {
            $html = '<span class="label label-success" style="line-height: 30px;">' . _l('appointment_feedback_label_added') . '</span><br>';
        }

        $savedFeedbacks = json_decode(get_option('appointly_default_feedbacks'));
        $count = 0;

        foreach ($appointmentFeedbacks as $feedback) {

            if ($savedFeedbacks !== null) {
                if ( ! in_array($feedback['value'], $savedFeedbacks)) {
                    continue;
                }
            }

            $rating_class = '';

            if ($appointment['feedback'] >= $feedback['value']) {
                $rating_class = 'star_rated';
            }

            $onClick = '';
            if ( ! is_staff_logged_in()) {
                $onClick = 'onclick="handle_appointment_feedback(this)"';
            }

            $html .= '<span ' . $onClick . ' data-count="' . $count++ . '" data-rating="' . $feedback['value'] . '" data-toggle="tooltip" title="' . $feedback['name'] . '" class="feedback_star text-center ' . $rating_class . '"><i class="fa fa-star" aria-hidden="true"></i></span>';
        }

        if ( ! is_bool($appointment['feedback_comment'])) {
            if ($appointment['feedback_comment'] !== null) {
                $html .= '<div class="col-md-12 text-center mtop5" id="feedback_comment_area">';
                $html .= '<h6>' . $appointment['feedback_comment'] . '</h6>';
                $html .= '</div>';
                $html .= '<div class="clearfix"></div>';
            }
        }

        if ( ! is_staff_logged_in() && $appointment['feedback'] !== null) {
            echo '<div>';
        }

        $html .= '</div>';

        return $html;
    }
}

/**
 * Render callbacks timezone.
 *
 * @param $datetime
 *
 * @return string
 * @throws \Exception
 */
if ( ! function_exists('render_callbacks_timezone')) {

    function render_callbacks_timezone($datetime)
    {
        $target_time_zone = new DateTimeZone($datetime);
        $dt = new DateTime('now', $target_time_zone);

        return '<i data-toggle="tooltip" title="' . $datetime . ' GMT ' . $dt->format('P') . '" class="fa fa-globe timezone" aria-hidden="true"></i>';
    }
}

/**
 * Handle callbacks types.
 *
 * @param array $types
 *
 * @return string
 */
if ( ! function_exists('callbacks_handle_call_type')) {

    function callbacks_handle_call_type(array $types)
    {
        $url = '';
        $link = site_url('modules/appointly/assets/images/callbacks/');
        $class = 'class="callbacks_image"';

        $sources = array_diff(@scandir(APP_MODULES_PATH . 'appointly/assets/images/callbacks'), ['.', '..']);

        // In case file cannot be read
        if (is_array($sources) && ! empty($sources)) {
            foreach ($sources as &$source) {
                $source = str_replace('.png', '', $source);
                $source = str_replace('.', '', $source);
            }
        } else {
            $sources = ['phone', 'skype', 'viber', 'messenger', 'whatsapp', 'wechat', 'instagram', 'linkedin', 'telegram', 'vk'];
        }

        foreach ($types as $type) {
            if (in_array($type, $sources)) {
                $url .= '<img data-toggle="tooltip" title="' . ucfirst($type) . '" ' . $class . ' src="' . $link . '' . $type . '.png' . '">';
            } else {
                $url .= '<img data-toggle="tooltip" title="' . ucfirst($type) . '" ' . $class . ' src="' . $link . 'other.png' . '">';
            }
        }

        return $url;
    }
}

/**
 * Render callbacks types.
 *
 * @return string
 */
if ( ! function_exists('render_callbacks_handle_call_type')) {

    function render_callbacks_handle_call_type()
    {
        $url = '';
        $link = site_url('modules/appointly/assets/images/callbacks/');
        $class = 'class="callbacks_image"';

        $sources = array_diff(@scandir(APP_MODULES_PATH . 'appointly/assets/images/callbacks'), ['.', '..']);

        // In case file cannot be read
        if (is_array($sources) && ! empty($sources)) {
            foreach ($sources as &$source) {
                $source = str_replace('.png', '', $source);
                $source = str_replace('.', '', $source);
            }
        } else {
            $sources = ['phone', 'skype', 'viber', 'messenger', 'whatsapp', 'wechat', 'instagram', 'linkedin', 'telegram', 'vk'];
        }

        foreach ($sources as $type) {
            if ($type == 'other') {
                continue;
            }
            $url .= '<img data-type-name="' . $type . '" data-toggle="tooltip" title="' . ucfirst($type) . '" ' . $class . ' src="' . $link . '' . $type . '.png' . '">';
        }

        return $url;
    }
}

/**
 * Check if staff is set as responsible person for callbacks.
 *
 * @return bool
 */
if ( ! function_exists('is_staff_callbacks_responsible')) {

    function is_staff_callbacks_responsible()
    {
        return get_option('callbacks_responsible_person') == get_staff_user_id();
    }
}

/**
 * Check if staff is set as responsible person for appointments.
 *
 * @return bool
 */
if ( ! function_exists('staff_appointments_responsible')) {

    function staff_appointments_responsible()
    {
        if (is_admin()) return true;

        return get_option('appointly_responsible_person') == get_staff_user_id();
    }
}

/**
 * Returns all callback statuses.
 *
 * @return array
 */
if ( ! function_exists('getCallbacksTableStatuses')) {

    function getCallbacksTableStatuses()
    {
        return [
            '1' => _l('callback_status_upcoming'),
            '2' => _l('callback_status_postponed'),
            '3' => _l('callback_status_cancelled'),
            '4' => _l('callback_status_complete'),
        ];
    }
}

/**
 * Returns all Appointments Table statuses.
 *
 * @return array
 */
if ( ! function_exists('getAppointlyTableStatuses')) {

    function getAppointlyTableStatuses()
    {
        return [
            'approved'  => _l('appointment_mark_as_approved'),
            'ongoing'   => _l('appointment_mark_as_ongoing'),
            'cancelled' => _l('appointment_mark_as_cancelled'),
            'finished'  => _l('appointment_mark_as_finished'),
        ];
    }
}

/**
 * Check and render appointment status with HTML.
 *
 * @param array $aRow
 *
 * @return string
 */
if ( ! function_exists('checkAppointlyStatus')) {

    function checkAppointlyStatus($aRow)
    {
        $outputStatus = '';
        $icon = '';
        /* We dont want caret down if status is finished */
        if ($aRow['finished'] != 1) {
            $icon = ' <i class="fa fa-caret-down" aria-hidden="true"></i>';
        }

        if ($aRow['cancelled'] && $aRow['finished'] == 0) {
            $outputStatus .= '<span class="label label-danger">' . strtoupper(_l('appointment_cancelled')) . ' ' . $icon . ' </span>';
        } else if ( ! $aRow['finished'] && ! $aRow['cancelled'] && date('Y-m-d H:i', strtotime($aRow['date'])) < date('Y-m-d H:i') && $aRow['approved'] == 1) {
            $outputStatus .= '<span class="label label-danger">' . strtoupper(_l('appointment_missed_label')) . ' ' . $icon . ' </span>';
        } else if ( ! $aRow['finished'] && ! $aRow['cancelled'] && $aRow['approved'] == 1) {
            $outputStatus .= '<span class="label label-info">' . strtoupper(_l('appointment_upcoming')) . ' ' . $icon . ' </span>';
        } else if ( ! $aRow['finished'] && ! $aRow['cancelled'] && $aRow['approved'] == 0) {
            $outputStatus .= '<span class="label label-warning">' . strtoupper(_l('appointment_pending_approval')) . ' ' . $icon . ' </span>';
        } else {
            $outputStatus .= '<span class="label label-success">' . strtoupper(_l('appointment_finished')) . ' ' . $icon . ' </span>';
        }

        return $outputStatus;
    }
}

/**
 * Validate status name.
 *
 * @param $status
 *
 * @return string
 */
if ( ! function_exists('fetchCallbackStatusName')) {

    function fetchCallbackStatusName($status)
    {
        switch ($status) {
            case '2':
                $status = _l('callback_status_postponed');
                break;
            case '3':
                $status = _l('callback_status_cancelled');
                break;
            case '4':
                $status = _l('callback_status_complete');
                break;
            default:
                $status = _l('callback_status_upcoming');
                break;
        }

        return $status;
    }
}

/**
 * Sql helper to get all assigned ids for callbacks and save space on query.
 *
 * @return string
 */
if ( ! function_exists('get_sql_select_callback_assignees_ids')) {

    function get_sql_select_callback_assignees_ids()
    {
        return '(SELECT GROUP_CONCAT(user_id SEPARATOR ",") FROM ' . db_prefix() . 'appointly_callbacks_assignees WHERE ' . db_prefix() . 'appointly_callbacks_assignees.callbackid = ' . db_prefix() . 'appointly_callbacks.id ORDER by user_id ASC) ';
    }
}

/**
 * Sql helper to get all assigned staff names for callbacks and save space on query.
 *
 * @return string
 */
if ( ! function_exists('get_sql_select_callback_asignees_full_names')) {

    function get_sql_select_callback_asignees_full_names()
    {
        return '(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'appointly_callbacks_assignees JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'appointly_callbacks_assignees.user_id WHERE ' . db_prefix() . 'appointly_callbacks_assignees.callbackid=' . db_prefix() . 'appointly_callbacks.id ORDER BY ' . db_prefix() . 'appointly_callbacks_assignees.user_id ASC) ';
    }
}

/**
 * Function helper to get client details.
 *
 * @param string $contact_id
 * @param string $detail
 *
 * @return mixed
 */
if ( ! function_exists('get_contact_detail')) {

    function get_contact_detail($contact_id, $detail)
    {
        $allowedFields = ['firstname', 'lastname', 'email', 'phonenumber'];

        if ( ! in_array($detail, $allowedFields)) {
            return '';
        }

        return get_instance()->db->get_where(db_prefix() . 'contacts', ['id' => $contact_id])->row($detail);
    }
}

/**
 * Get module version.
 *
 * @return string
 */
if ( ! function_exists('get_appointly_version')) {

    function get_appointly_version()
    {
        get_instance()->db->where('module_name', 'appointly');
        $version = get_instance()->db->get(db_prefix() . 'modules');

        if ($version->num_rows() > 0) {
            return _l('appointly_current_version') . $version->row('installed_version');
        }
        return 'Unknown';
    }
}

/**
 * Add additional css on client side external form.
 *
 * @param string $client
 */
if ( ! function_exists('applyAdditionalCssStyles')) {

    function applyAdditionalCssStyles($client)
    {
        if (isset($client['client_logged_in'])) {
            echo compile_theme_css();
            get_template_part('navigation');
            echo '<style>
            body .cb-form-wrapper { top: calc(100% - 88.2%); }
            @media only screen and (max-width: 768px) {
              body .cb-form-wrapper {
                top: calc(100% - 86.2%);
                }
            }
        </style>';
        } else {
            echo '<style>
        @media only screen and (max-width: 768px) {
            body .callback-icon {
                right: 32px;
            }
         }
        </style>';
        }
    }
}
