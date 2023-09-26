<?php 

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Module Name: Team Management
 * Description: A module for displaying a dashboard with all staff members.
 * Version: 2.3.0
 *Requires at least: 2.3.*
*/

require(__DIR__ . '/vendor/autoload.php');


$CI = &get_instance();

hooks()->add_action('admin_init', 'team_management_permissions');

hooks()->add_action('admin_init', 'team_management_init_menu_items');

hooks()->add_action('app_admin_head', 'widget');

hooks()->add_action('task_assignee_added', 'notify_task_allocation');

hooks()->add_action('task_timer_started', 'notify_task_timer_started');

hooks()->add_action('before_task_timer_stopped', 'notify_task_timer_stopped');

hooks()->add_action('task_status_changed', 'notify_task_status_changed');

hooks()->add_action('task_comment_added', 'notify_task_comment_added');


function notify_task_allocation($data) {
    
    $CI = &get_instance();

    $CI->load->model('staff_model');
    $CI->load->model('tasks_model');
    $CI->load->model('projects_model');
    $CI->load->model('team_management/team_management_model');

    $CI->load->library('team_management/webhook_library');
    

    
    
    // Get task details
    $task_id = $data['task_id'];
    $task = $CI->tasks_model->get($task_id);

    if($task->rel_type == "project"){
        $project_id = $task->rel_id;
        $project_name = $CI->projects_model->get($project_id)->name;
    }else{
        $project_name = "";
    }
    

    $assignees = $CI->tasks_model->get_task_assignees($task_id);
    $last_assignee = end($assignees);
    $staff_id = $last_assignee['assigneeid'];

    $tag = $CI->team_management_model->id_to_name($staff_id, 'tbl_staff_google_chat', 'staff_id', 'google_chat_user_id');


    // Prepare message content
    $message_content = "🎯 *Task Assigned*\n";
    $message_content .= "----------------\n";
    $message_content .= "*Assigned To:* <users/" . $tag . "> \n";
    $message_content .= "*Task Name:* " . $task->name . "\n";
    $message_content .= "*Project Name:* " . $project_name . "\n";
    $message_content .= "*Description:* " . $task->description . "\n";
    $message_content .= "*Due Date:* " . $task->duedate . "\n";
    $message_content .= "*Task Link:* " . admin_url() . 'tasks/view/' . $task_id . "\n";

    
    $CI->webhook_library->send_chat_webhook($message_content, "tasks-allocation");
}

function notify_task_timer_started($data) {
    
    $CI = &get_instance();

    $CI->load->model('staff_model');
    $CI->load->model('tasks_model');
    $CI->load->model('projects_model');
    $CI->load->model('team_management/team_management_model');

    $CI->load->library('team_management/webhook_library');
    

    // Get task details
    $task_id = $data['task_id'];
    $task = $CI->tasks_model->get($task_id);

    if($task->rel_type == "project"){
        $project_id = $task->rel_id;
        $project_name = $CI->projects_model->get($project_id)->name;
    }else{
        $project_name = "";
    }
    
    // Get timer details
    $timer_id = $data['timer_id'];
    $timer = $CI->tasks_model->get_task_timer(['id' => $timer_id]);
    $staff_id = $timer->staff_id;
    $start_time = date("g:i A", $timer->start_time);

    // Get staff details
    $staff = $CI->staff_model->get($staff_id);

    $tag = $CI->team_management_model->id_to_name($staff_id, 'tbl_staff_google_chat', 'staff_id', 'google_chat_user_id');

    // Prepare message content
    $message_content = "⏱️ *Task Timer Started*\n";
    $message_content .= "------------------------\n";
    $message_content .= "*Started By:* <users/" . $tag . "> \n";
    $message_content .= "*Task Name:* " . $task->name . "\n";
    $message_content .= "*Task Started At:* " . $start_time . "\n";  // Add start time to message content
    $message_content .= "*Project Name:* " . $project_name . "\n";
    $message_content .= "*Task Link:* " . admin_url() . 'tasks/view/' . $task_id . "\n";

    
    $CI->webhook_library->send_chat_webhook($message_content, "tasks-activity");
}

function notify_task_timer_stopped($data) {
    
    $CI = &get_instance();

    $CI->load->model('tasks_model');
    $CI->load->model('projects_model');
    $CI->load->model('team_management/team_management_model');

    $CI->load->library('team_management/webhook_library');

    
    // Get timer details
    $timer = $data['timer'];
    $staff_id = $timer->staff_id;
    $end_time = date("g:i A", $timer->end_time);

    $note = $data['note'];
    
    // Get task details
    $task_id = $data['task_id'];
    $task = $CI->tasks_model->get($task_id);

    if($task->rel_type == "project"){
        $project_id = $task->rel_id;
        $project_name = $CI->projects_model->get($project_id)->name;
    }else{
        $project_name = "";
    }

    $tag = $CI->team_management_model->id_to_name($staff_id, 'tbl_staff_google_chat', 'staff_id', 'google_chat_user_id');

    // Prepare message content
    $message_content = "⏱️ *Task Timer Stopped*\n";
    $message_content .= "------------------------\n";
    $message_content .= "*Stopped By:* <users/" . $tag . "> \n";
    $message_content .= "*Task Name:* " . $task->name . "\n";
    $message_content .= "*Task Ended At:* " . $end_time . "\n";  // Add end time to message content
    $message_content .= "*Note:* " . $note . "\n";  // Add end time to message content
    $message_content .= "*Project Name:* " . $project_name . "\n";
    $message_content .= "*Task Link:* " . admin_url() . 'tasks/view/' . $task_id . "\n";

    
    $CI->webhook_library->send_chat_webhook($message_content, "tasks-activity");
}

function notify_task_status_changed($data) {
    $CI = &get_instance();

    // Load necessary models
    $CI->load->model('tasks_model');
    $CI->load->model('projects_model');
    $CI->load->model('team_management/team_management_model');

    // Load necessary library
    $CI->load->library('team_management/webhook_library');

    // Get task details
    $task_id = $data['task_id'];
    $task = $CI->tasks_model->get($task_id);

    // Get all assignees and get the last assignee
    $assignees = $CI->tasks_model->get_task_assignees($task_id);
    $last_assignee = end($assignees);
    $staff_id = $last_assignee['assigneeid'];
    
    // Get status details
    $status = $data['status'];  // 'status' comes from the array passed when triggering the hook

    if($status != 5){
        return;
    }

    $timers = $CI->tasks_model->get_timers($task_id);
    $total_task_time = 0;
    foreach($timers as $timer) {
        $total_task_time += $timer['end_time'] - $timer['start_time'];
    }

    $tag = $CI->team_management_model->id_to_name($staff_id, 'tbl_staff_google_chat', 'staff_id', 'google_chat_user_id');

    // Prepare message content
    $message_content = "📝 *Task Completed*\n";
    $message_content .= "-----------------------\n";
    $message_content .= "*By:* <users/" . $tag . "> \n";
    $message_content .= "*Task Name:* " . $task->name . "\n";
    $message_content .= "*Time Taken:* " . secondsToReadableTime($total_task_time) . "\n";
    $message_content .= "*Task Link:* " . admin_url() . 'tasks/view/' . $task_id . "\n";

    $CI->webhook_library->send_chat_webhook($message_content, "tasks-activity");
}

function notify_task_comment_added($data) {
    $CI = &get_instance();

    // Load required models
    $CI->load->model('staff_model');
    $CI->load->model('tasks_model');
    $CI->load->model('team_management/team_management_model');

    // Load webhook library
    $CI->load->library('team_management/webhook_library');

    // Get task details
    $task_id = $data['task_id'];
    $task = $CI->tasks_model->get($task_id);

    // Get comment details
    $CI->db->where('id', $data['comment_id']);
    $comment = $CI->db->get(db_prefix() . 'task_comments')->row();

    // Format dateadded
    $comment_date = date('g:i A', strtotime($comment->dateadded));

    // Get staff details
    $staff_id = $comment->staffid;

    $tag = $CI->team_management_model->id_to_name($staff_id, 'tbl_staff_google_chat', 'staff_id', 'google_chat_user_id');

    // Prepare message content
    $message_content = "💬 *Task Comment Added*\n";
    $message_content .= "----------------\n";
    $message_content .= "*Task:* " . $task->name . "\n";
    $message_content .= "*Comment By:* <users/" . $tag . "> \n";
    $message_content .= "*Comment Time:* " . $comment_date . "\n";
    $message_content .= "*Comment:* " . strip_tags($comment->content) . "\n";
    $message_content .= "*Task Link:* " . admin_url() . 'tasks/view/' . $task_id . "\n";

    // Send message
    $CI->webhook_library->send_chat_webhook($message_content, "tasks-activity");
}



function secondsToReadableTime($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);

    return "{$hours}h {$minutes}m";
}



function widget()
{   
    $CI = &get_instance();

    $query = "SELECT staff_id, CONCAT(firstname, ' ', lastname) AS staff_name, shift_start_time, shift_end_time, `month`, `day` FROM tbl_staff_shifts JOIN tblstaff ON tbl_staff_shifts.staff_id = tblstaff.staffid WHERE `month` = '4' AND `day` = '26'";
    $result = $CI->db->query($query);

    $staffShifts = [];

    // Fetch the rows and add them to the array
    foreach ($result->result_array() as $row) {
        $staffShifts[] = [
            'staff_id' => $row['staff_id'],
            'staff_name' => $row['staff_name'],
            'shift_start_time' => date('Y').'-'.$row["month"].'-'.$row["day"].' '. $row['shift_start_time'],
            'shift_end_time' => date('Y').'-'.$row["month"].'-'.$row["day"].' '. $row['shift_end_time']
        ];
    }

    echo '<script>';
    echo 'var base_url = "' . site_url() . '";';
    echo 'var myZone = "' . get_option('default_timezone') . '";';
    echo 'var csrf_token_name = "'.$CI->security->get_csrf_token_name().'";';
    echo 'var csrf_token = "'.$CI->security->get_csrf_hash().'";';
    echo 'var admin_url = "' . admin_url() . '";var staffShifts = ';
    echo json_encode($staffShifts);
    echo ';';
    
    echo '</script>';
    echo '<script src="' . base_url('modules/team_management/assets/js/widget.js') . '?v=3.347"></script>';

}

if (!$CI->db->table_exists(db_prefix() . '_staff_time_entries')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "_staff_time_entries` (
      `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `staff_id` INT(11) UNSIGNED,
      `clock_in` DATETIME,
      `clock_out` DATETIME NULL
      )
    ");
}
if (!$CI->db->table_exists(db_prefix() . '_staff_status_entries')) {

    $CI->db->query('CREATE TABLE `' . db_prefix() . "_staff_status_entries` (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        staff_id INT(11) UNSIGNED,
        status VARCHAR(100),
        start_time DATETIME,
        end_time DATETIME NULL
        );
    ");
}
if (!$CI->db->table_exists(db_prefix() . '_staff_status')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "_staff_status` (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        staff_id INT(11) UNSIGNED,
        status VARCHAR(100),
        last_working_task int(11) null
        );
  ");
}

if (!$CI->db->table_exists(db_prefix() . '_staff_shifts')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "_staff_shifts` (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        staff_id INT(11) UNSIGNED,
        month INT(2) UNSIGNED,
        day INT(2) UNSIGNED,
        shift_number INT(2) UNSIGNED,
        shift_start_time TIME,
        shift_end_time TIME
        );
  ");
}

if (!$CI->db->table_exists(db_prefix() . '_applications')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "_applications` (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        staff_id INT(11) UNSIGNED,
        application_type VARCHAR(255) NOT NULL,
        status ENUM('Pending', 'Approved', 'Disapproved'),
        start_date DATE,
        end_date DATE,
        shift ENUM('1', '2', 'all') DEFAULT 'all'
        reason TEXT NOT NULL,
        created_at DATE,
        updated_at DATE
        );
    ");
}

if (!$CI->db->table_exists(db_prefix() . '_day_summaries')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "_day_summaries` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `date` DATE NOT NULL,
        `summary` TEXT NOT NULL,
        `created_at` DATETIME,
        `updated_at` DATETIME
    );");
}

if (!$CI->db->table_exists(db_prefix() . '_monthly_summaries')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "_monthly_summaries` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `month` int NOT NULL,
        `year` int NOT NULL,
        `summary` TEXT NOT NULL,
        `created_at` DATETIME,
        `updated_at` DATETIME
    );");
}

if (!$CI->db->table_exists(db_prefix() . '_dummy_tasks')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "_dummy_tasks` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `project_id` INT(11) UNSIGNED NOT NULL,
        `task_id` INT(11),
        `name` VARCHAR(255) NOT NULL,
        `created_at` DATETIME,
        `updated_at` DATETIME
    );");
}

if (!$CI->db->table_exists(db_prefix() . '_staff_summaries')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . '_staff_summaries` (
      `id` int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `staff_id` int(11) NOT NULL,
      `summary` text NOT NULL,
      `date` date NOT NULL
    )');
}

if (!$CI->db->table_exists(db_prefix() . '_staff_google_chat')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . '_staff_google_chat` (
      `id` int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `staff_id` int(11) NOT NULL,
      `google_chat_user_id` varchar(255) NOT NULL,
      UNIQUE (`staff_id`)
    )');
}

if (!$CI->db->table_exists(db_prefix() . '_staff_pair_history')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . '_staff_pair_history` (
      `id` int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `staff_tag_1` varchar(255) NOT NULL,
      `staff_tag_2` varchar(255) NOT NULL,
      `paired_at` datetime NOT NULL,
      UNIQUE (`staff_tag_1`, `staff_tag_2`)
    )');
}





$staff_id = $CI->session->userdata('staff_user_id');

$staff_id_exists = $CI->db->select('*')
                          ->from(''.db_prefix().'_staff_status')
                          ->where('staff_id', $staff_id)
                          ->get()
                          ->num_rows();

if (!$staff_id_exists) {
    $data = array(
        'staff_id' => $staff_id,
        'status' => 'Online'
    );
    $CI->db->insert(''.db_prefix().'_staff_status', $data);
}

function team_management_init_menu_items(){
    $CI = &get_instance();
    $CI->app_menu->add_sidebar_menu_item('team_management', [
        'name'     => 'Management', // The name if the item
        'position' => 2, // The menu position, see below for default positions.
        'icon'     => 'fa fa-users', // Font awesome icon
    ]);

    $CI->app_menu->add_sidebar_children_item('team_management', [
        'slug'     => 'team_dashboard', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Dashboard', // The name if the item
        'href'     => admin_url('team_management/dashboard'), // URL of the item
        'position' => 1, // The menu position
        'icon'     => 'fa fa-user-friends', // Font awesome icon
    ]);

    $CI->app_menu->add_sidebar_children_item('team_management', [
        'slug'     => 'individual_stats', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Individual Stats', // The name if the item
        'href'     => admin_url('team_management/individual_stats'), // URL of the item
        'position' => 2, // The menu position
        'icon'     => 'fa fa-user-cog', // Font awesome icon
    ]);

    

    
    

    if (has_permission('team_management', '', 'admin')) {
        $CI->app_menu->add_sidebar_children_item('team_management', [
            'slug'     => 'applications_admin', // Required ID/slug UNIQUE for the child menu
            'name'     => 'All Applications', // The name if the item
            'href'     => admin_url('team_management/all_applications'), // URL of the item
            'position' => 2, // The menu position
            'icon'     => 'fa fa-address-book', // Font awesome icon
        ]);

        
        $CI->app_menu->add_sidebar_children_item('team_management', [
            'slug'     => 'staff_shifts', // Required ID/slug UNIQUE for the child menu
            'name'     => 'Staff Shifts', // The name if the item
            'href'     => admin_url('team_management/staff_shifts'), // URL of the item
            'position' => 2, // The menu position
            'icon'     => 'fa fa-user-clock', // Font awesome icon
        ]);

        $CI->app_menu->add_sidebar_children_item('team_management', [
            'slug'     => 'chat_ids', // Required ID/slug UNIQUE for the child menu
            'name'     => 'Chat U_IDS', // The name if the item
            'href'     => admin_url('team_management/staff_google_chat'), // URL of the item
            'position' => 2, // The menu position
            'icon'     => 'fa fa-user-lock', // Font awesome icon
        ]);

           


    }else{
        $CI->app_menu->add_sidebar_children_item('team_management', [
            'slug'     => 'applications', // Required ID/slug UNIQUE for the child menu
            'name'     => 'Applications', // The name if the item
            'href'     => admin_url('team_management/applications'), // URL of the item
            'position' => 2, // The menu position
            'icon'     => 'fa fa-address-book', // Font awesome icon
        ]);
    }

    $CI->app_menu->add_sidebar_children_item('team_management', [
        'slug'     => 'project_management', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Projects', // The name if the item
        'href'     => admin_url('team_management/project_management'), // URL of the item
        'position' => 2, // The menu position
        'icon'     => 'fa fa-user-clock', // Font awesome icon
    ]);

    $CI->app_menu->add_sidebar_children_item('team_management', [
        'slug'     => 'daily_report', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Daily Report', // The name if the item
        'href'     => admin_url('team_management/daily_reports/'.date('m').'/'.date('d')), // URL of the item
        'position' => 2, // The menu position
        'icon'     => 'fa fa-hourglass-end', // Font awesome icon
    ]);  

    $CI->app_menu->add_sidebar_children_item('team_management', [
        'slug'     => 'monthly_report', // Required ID/slug UNIQUE for the child menu
        'name'     => 'Monthly Report', // The name if the item
        'href'     => admin_url('team_management/monthly_reports/'.date('Y').'/'.date('m')), // URL of the item
        'position' => 2, // The menu position
        'icon'     => 'fa fa-calendar', // Font awesome icon
    ]);  

}

function team_management_permissions() {
	$capabilities = [];
	$capabilities['capabilities'] = [
		'admin' => 'Is Admin'
	];
	register_staff_capabilities('team_management', $capabilities, 'Team Management');
}






?>