<?php

use app\services\utilities\Str;

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard_model');
        $this->load->model('newsfeed_model');
        $this->load->model('team_management_model');
        $this->load->model('staff_model');
        $this->load->library('webhook_library', null, 'webhook_lib');
    }

    /* This is admin dashboard view */
    public function index()
    {
        close_setup_menu();
        $this->load->model('departments_model');
        $this->load->model('todo_model');
        $data['departments'] = $this->departments_model->get();

        $data['todos'] = $this->todo_model->get_todo_items(0);
        // Only show last 5 finished todo items
        $this->todo_model->setTodosLimit(5);
        $data['todos_finished']            = $this->todo_model->get_todo_items(1);
        $data['upcoming_events_next_week'] = $this->dashboard_model->get_upcoming_events_next_week();
        $data['upcoming_events']           = $this->dashboard_model->get_upcoming_events();
        $data['title']                     = _l('dashboard_string');

        $this->load->model('contracts_model');
        $data['expiringContracts'] = $this->contracts_model->get_contracts_about_to_expire(get_staff_user_id());

        $this->load->model('currencies_model');
        $data['currencies']    = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['activity_log']  = $this->misc_model->get_activity_log();
        // Tickets charts
        $tickets_awaiting_reply_by_status     = $this->dashboard_model->tickets_awaiting_reply_by_status();
        $tickets_awaiting_reply_by_department = $this->dashboard_model->tickets_awaiting_reply_by_department();

        $data['tickets_reply_by_status']              = json_encode($tickets_awaiting_reply_by_status);
        $data['tickets_awaiting_reply_by_department'] = json_encode($tickets_awaiting_reply_by_department);

        $data['tickets_reply_by_status_no_json']              = $tickets_awaiting_reply_by_status;
        $data['tickets_awaiting_reply_by_department_no_json'] = $tickets_awaiting_reply_by_department;

        $data['projects_status_stats'] = json_encode($this->dashboard_model->projects_status_stats());
        $data['leads_status_stats']    = json_encode($this->dashboard_model->leads_status_stats());
        $data['google_ids_calendars']  = $this->misc_model->get_google_calendar_ids();
        $data['bodyclass']             = 'dashboard invoices-total-manual';
        $this->load->model('announcements_model');
        $data['staff_announcements']             = $this->announcements_model->get();
        $data['total_undismissed_announcements'] = $this->announcements_model->get_total_undismissed_announcements();

        $this->load->model('projects_model');
        $data['projects_activity'] = $this->projects_model->get_activity('', hooks()->apply_filters('projects_activity_dashboard_limit', 20));
        add_calendar_assets();
        $this->load->model('utilities_model');
        $this->load->model('estimates_model');
        $data['estimate_statuses'] = $this->estimates_model->get_statuses();

        $this->load->model('proposals_model');
        $data['proposal_statuses'] = $this->proposals_model->get_statuses();

        $wps_currency = 'undefined';
        if (is_using_multiple_currencies()) {
            $wps_currency = $data['base_currency']->id;
        }
        $data['weekly_payment_stats'] = json_encode($this->dashboard_model->get_weekly_payments_statistics($wps_currency));

        $data['dashboard'] = true;

        $data['user_dashboard_visibility'] = get_staff_meta(get_staff_user_id(), 'dashboard_widgets_visibility');
        $data['upcoming_birthdays'] = $this->staff_model->get_upcoming_birthdays();
        $data['posts'] = $this->newsfeed_model->load_newsfeed(0);

        if (!$data['user_dashboard_visibility']) {
            $data['user_dashboard_visibility'] = [];
        } else {
            $data['user_dashboard_visibility'] = unserialize($data['user_dashboard_visibility']);
        }
        $data['user_dashboard_visibility'] = json_encode($data['user_dashboard_visibility']);

        $data['tickets_report'] = [];
        if (is_admin()) {
            $data['tickets_report'] = (new \app\services\TicketsReportByStaff())->filterBy('this_month');
        }

        $data['posts'] = $this->newsfeed_model->load_newsfeed(0);

        $data['upcoming_birthdays'] = $this->staff_model->get_upcoming_birthdays();

        $staff_id = get_staff_user_id(); // Yeh function current logged in user ki ID return karta hai.

        $data = hooks()->apply_filters('before_dashboard_render', $data);

        $data['total_time'] = $this->team_management_model->get_today_live_timer($staff_id);

        $date = date("Y-m-d");

        $shift_timings = $this->team_management_model->get_shift_timings_of_date($date, $staff_id);
        
        
        $shift_secs = 0;
        if(isset($shift_timings['first_shift']['end']) && isset($shift_timings['first_shift']['start'])){
            $shift_secs += strtotime($shift_timings['first_shift']['end']) - strtotime($shift_timings['first_shift']['start']);
        }
        if(isset($shift_timings['second_shift']['end']) && isset($shift_timings['second_shift']['start'])){
            $shift_secs += strtotime($shift_timings['second_shift']['end']) - strtotime($shift_timings['second_shift']['start']);
        }

        
        $data['shift_seconds'] = $shift_secs;

        $data['shift_timings'] = $shift_timings;
        $data['afk_offline_entries'] = $this->team_management_model->get_afk_and_offline_entries($staff_id, $date);
        $data['clock_in_entries'] = $this->team_management_model->get_staff_time_entries($staff_id, $date);

        $this->load->library('kpi_system');

        $data['puctuality_rate'] = $this->kpi_system->kpi_punctuality_rate($staff_id, date("Y-m-01"), date("Y-m-d"))['on_time_percentage'];

        $this->load->view('admin/dashboard/dashboard', $data);
    }

    /* Chart weekly payments statistics on home page / ajax */
    public function weekly_payments_statistics($currency)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->dashboard_model->get_weekly_payments_statistics($currency));
            die();
        }
    }

    /* Chart monthly payments statistics on home page / ajax */
    public function monthly_payments_statistics($currency)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->dashboard_model->get_monthly_payments_statistics($currency));
            die();
        }
    }

    public function ticket_widget($type)
    {
        $data['tickets_report'] = (new \app\services\TicketsReportByStaff())->filterBy($type);
        $this->load->view('admin/dashboard/widgets/tickets_report_table', $data);
    }

    public function like_or_unlike() {
        $postId = $this->input->post('post_id');
        
        if ($this->newsfeed_model->user_liked_post($postId)) {
            $result = $this->newsfeed_model->unlike_post($postId);
            echo json_encode(['liked' => !$result]);
        } else {
            $result = $this->newsfeed_model->like_post($postId);
            echo json_encode(['liked' => $result]);
        }
    }

    public function markPostAsSeen() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $postId = $this->input->post('postId');
        $currentUserId = get_staff_user_id(); // Assuming you have a method to get logged-in user's ID.

        // Load your model (if not already loaded)
        $this->load->model('newsfeed_model');

        // Mark post as seen and get the result
        $result = $this->newsfeed_model->markAsSeen($postId, $currentUserId);

        if($result) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update database.']);
        }
    }



}