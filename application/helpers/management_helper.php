<?php 

defined('BASEPATH') or exit('No direct script access allowed');

hooks()->add_action('admin_init', 'team_management_permissions');

hooks()->add_action('task_assignee_added', 'notify_task_allocation');

hooks()->add_action('task_timer_started', 'notify_task_timer_started');

hooks()->add_action('before_task_timer_stopped', 'notify_task_timer_stopped');

hooks()->add_action('task_status_changed', 'notify_task_status_changed');

hooks()->add_action('task_comment_added', 'notify_task_comment_added');


$CI = &get_instance();

function notify_task_allocation($data) {
    
    $CI = &get_instance();

    $CI->load->model('staff_model');
    $CI->load->model('tasks_model');
    $CI->load->model('projects_model');
    $CI->load->model('team_management_model');

    $CI->load->library('webhook_library');
    

    
    
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

    $tag = id_to_name($staff_id, 'tblstaff', 'staffid', 'google_chat_id');

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

    $CI->load->library('webhook_library');
    

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

    $tag = id_to_name($staff_id, 'tblstaff', 'staffid', 'google_chat_id');
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

    $CI->load->library('webhook_library');

    
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

    $tag = id_to_name($staff_id, 'tblstaff', 'staffid', 'google_chat_id');
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
    $CI->load->library('webhook_library');

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

    $tag = id_to_name($staff_id, 'tblstaff', 'staffid', 'google_chat_id');
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
    $CI->load->library('webhook_library');

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

    $tag = id_to_name($staff_id, 'tblstaff', 'staffid', 'google_chat_id');
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

function id_to_name($id, $tableName, $idName, $nameName) {

    $CI = &get_instance();

    $CI->db->select($nameName);
    $CI->db->from($tableName);
    $CI->db->where($idName, $id);
    $query = $CI->db->get();
    if ($query->num_rows() > 0) {
        $row = $query->row();
        return $row->$nameName;
    } else {
        return 'Unknown';
    }
}

function convertSecondsToRoundedTime($seconds)
{
    $hours = floor($seconds / 3600);
    $minutes = round(($seconds % 3600) / 60);

    if ($hours > 0) {
        return "{$hours}h {$minutes}m";
    } else {
        return "{$minutes}m";
    }
}


function team_management_permissions() {
	$capabilities = [];
	$capabilities['capabilities'] = [
		'admin' => 'Is Admin'
	];
	register_staff_capabilities('team_management', $capabilities, 'Team Management');
}



?>