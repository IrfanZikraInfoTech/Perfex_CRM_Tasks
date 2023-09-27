<?php defined('BASEPATH') or exit('No direct script access allowed');

class Team_management_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  integer (optional)
     * @return object
     * Get single goal
     */

    public function get_all_staff()
    {
        $CI =& get_instance();

        $this->db->select('*');
        $this->db->from(''.db_prefix().'staff');
        $this->db->join(''.db_prefix().'_staff_status', ''.db_prefix().'staff.staffid = '.db_prefix().'_staff_status.staff_id');
        $this->db->where('is_not_staff', 0);
        $this->db->where('staffid !=', 1);
        $this->db->where('active', 1);

        $query = $this->db->get();
        $result = $query->result();
     
        // Loop through each row in the result
        foreach ($result as $staff) {

            //Today's Timer Counter
            $staff->live_time_today = $this->get_today_live_timer($staff->staff_id);

            //Task Assigned
            $allTasks = $this->get_tasks_of_staff($staff->staff_id);
            if($allTasks){
                $staff->all_tasks = $allTasks;
            }
            

            //Get current task
            $taskId = $this->get_current_task_by_staff_id($staff->staff_id);
            if($taskId){
                $task = $this->get_task_by_taskid($taskId);
                $staff->currentTaskName = $task->name;
                if($task->rel_type == "project"){
                    $CI->load->model('projects_model');
                    $task_project = $CI->projects_model->get($task->rel_id);
                    $staff->currentTaskProject = $task_project->name;
                }
                
                $currentTaskTime = $this->get_timers($taskId, $staff->staff_id);
                
                if($currentTaskTime){

                    $timestamp = $currentTaskTime->start_time;

                    $given_date = new DateTime();
                    $given_date->setTimestamp($timestamp);

                    $now = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));

                    $interval = $now->diff($given_date);
                    $seconds_passed = $interval->s + ($interval->i * 60) + ($interval->h * 3600) + ($interval->days * 86400);

                    $staff->currentTaskTime = $seconds_passed;

                }else{
                    $staff->currentTaskTime = "0";
                }

                $staff->currentTaskId = $task->id;

            }else{
                $staff->currentTaskId = 0;
                $staff->currentTaskName = "None";
                $staff->currentTaskTime = "0";
            }
            
            //Check if Shift is Active or Not
            $current_entry = $this->db->where('staff_id', $staff->staff_id)
                            ->where('clock_out IS NULL', null, false)
                            ->get(''.db_prefix().'_staff_time_entries')
                            ->row();
            if ($current_entry) {
                $staff->working = true;
            }else{
                $staff->working = false;
            }

            //Set Status Color Class
            if($staff->status == "Online"){
                $staff->statusColor = "emerald-200";
            }else if ($staff->status == "AFK"){
                $staff->statusColor = "sky-200";
            }
            else if ($staff->status == "Leave"){
                $staff->statusColor = "amber-200";
            }
            else{
                $staff->statusColor = "gray-200";
            }

         }
     
         return $result;
    }

    public function get_all_timers(){
        $timers = new stdClass();
        $yesterdayTime = 0;
        $weekTime = 0;
        $todayTime = 0;

        $this->db->select('staffid');
        $this->db->from(''.db_prefix().'staff');
        $this->db->where('is_not_staff', 0);
        $this->db->where('staffid !=', 1);  // This line excludes staff with ID=1
        $this->db->where('active', 1);
        $query = $this->db->get();

        $staff_members = $query->result();

        foreach ($staff_members as $staff) {
            $yesterdayTime += $this->get_yesterdays_total_time($staff->staffid);
            $weekTime += $this->get_this_weeks_total_time($staff->staffid);
            $todayTime += $this->get_today_live_timer($staff->staffid);
        }

        $timers->todayTime = $todayTime;
        $timers->yesterdayTime = $yesterdayTime;
        $timers->weekTime = $weekTime;

        $timers->totalOngoingTasks = $this->get_ongoing_tasks();
        $maxTasksCompleted = $this->get_staff_with_most_tasks_completed_today();

        if(isset($maxTasksCompleted)){
            if(isset($maxTasksCompleted->lastname)) {
                $timers->maxTasksCompletedName = $maxTasksCompleted->lastname;
            } else {
                $timers->maxTasksCompletedName = "Unknown";
            }
            
            if(isset($maxTasksCompleted->staffid)) {
                $timers->maxTasksCompletedId = $maxTasksCompleted->staffid;
            } else {
                $timers->maxTasksCompletedId = null;
            }
        } else {
            $timers->maxTasksCompletedName = "None :(";
            $timers->maxTasksCompletedId = null;
        }
        

        $maxHours = $this->get_staff_with_highest_today_live_timer();
        if($maxHours){
            $timers->maxHoursPutInName = $maxHours->lastname;
            $timers->maxHoursPutInId = $maxHours->staffid;
        }else{
            $timers->maxHoursPutInName = "Nan";
            $timers->maxHoursPutInId = null;
        }
        

        return $timers;
    }

    public function get_ongoing_tasks()
    {
        $this->db->select(''.db_prefix().'tasks.*');
        $this->db->from(''.db_prefix().'tasks');
        $this->db->join(''.db_prefix().'taskstimers', ''.db_prefix().'taskstimers.task_id = '.db_prefix().'tasks.id');
        $this->db->where(''.db_prefix().'taskstimers.end_time IS NULL', NULL, FALSE);
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function get_tasks_records($type) {

        $current_date = date('Y-m-d');

        $this->db->select(''.db_prefix().'tasks.*, '.db_prefix().'projects.name as project_name');
        $this->db->from(''.db_prefix().'tasks');

        
        if($type == 1){
            $this->db->where('duedate <', $current_date);
            $this->db->where(''.db_prefix().'tasks.status !=', 5);
        }else if ($type == 2){
            $this->db->where('duedate', $current_date);
        }else{
            $this->db->where(''.db_prefix().'tasks.status !=', 5);
            $this->db->group_start();
            $this->db->where('duedate >', $current_date);
            $this->db->or_where('duedate IS NULL', null, false);     
            $this->db->order_by('id DESC');
            $this->db->group_end();
        }

        $this->db->join(''.db_prefix().'projects', ''.db_prefix().'tasks.rel_id = '.db_prefix().'projects.id AND '.db_prefix().'tasks.rel_type = "project"', 'left');

        $query = $this->db->get();
        $allTasks = $query->result();

        foreach ($allTasks as $task) {

            $task->assigned = array();

            $this->db->select('staffid');
            $this->db->from(''.db_prefix().'task_assigned');
            $this->db->where('taskid', $task->id);
            $query = $this->db->get();
            $allStaff = $query->result();
            foreach ($allStaff as $staff) {
                array_push($task->assigned, staff_profile_image($staff->staffid, ['object-cover', 'md:h-full' , 'md:w-10 inline' , 'staff-profile-image-thumb'], 'thumb'));
            }
            if($task->duedate == null){
                $task->duedate = "None";
            }

            $task->priority = $this->id_to_name($task->priority, ''.db_prefix().'tickets_priorities', 'priorityid', 'name');

            if($task->status == 1){
                $task->status = "Not Started";
            }
            if($task->status == 2){
                $task->status = "Awaiting Feedback";
            }
            if($task->status == 3){
                $task->status = "Testing";
            }
            if($task->status == 4){
                $task->status = "In Progress";
            }
            if($task->status == 5){
                $task->status = "Completed";
            }
        }

        return $allTasks;
    }

    public function id_to_name($id, $tableName, $idName, $nameName) {
        $this->db->select($nameName);
        $this->db->from($tableName);
        $this->db->where($idName, $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->$nameName;
        } else {
            return 'Unknown';
        }
    }

    public function get_tasks_of_staff($staff_id)
    {
        $this->db->select(''.db_prefix().'tasks.*, '.db_prefix().'projects.name as project_name');
        $this->db->from(''.db_prefix().'tasks');
        $this->db->join(''.db_prefix().'task_assigned', ''.db_prefix().'task_assigned.taskid = '.db_prefix().'tasks.id');
        $this->db->join(''.db_prefix().'projects', ''.db_prefix().'tasks.rel_id = '.db_prefix().'projects.id AND '.db_prefix().'tasks.rel_type = "project"', 'left');
        $this->db->where(''.db_prefix().'task_assigned.staffid', $staff_id);
        $this->db->where(''.db_prefix().'tasks.status !=', 5);
        $query = $this->db->get();

        return $query->result();

    }
    
    public function get_incomplete_or_today_tasks($staff_id)
    {
        $today = date('Y-m-d');
        
        $this->db->select(''.db_prefix().'tasks.*, '.db_prefix().'projects.name as project_name');
        $this->db->from(''.db_prefix().'tasks');
        $this->db->join(''.db_prefix().'task_assigned', ''.db_prefix().'task_assigned.taskid = '.db_prefix().'tasks.id');
        $this->db->join(''.db_prefix().'projects', ''.db_prefix().'tasks.rel_id = '.db_prefix().'projects.id AND '.db_prefix().'tasks.rel_type = "project"', 'left');
        $this->db->where(''.db_prefix().'task_assigned.staffid', $staff_id);
        $this->db->group_start(); // Start group
        $this->db->where(''.db_prefix().'tasks.status !=', 5); // Incomplete tasks
        $this->db->or_where("DATE(".db_prefix()."tasks.startdate)", $today); // Tasks created today
        $this->db->group_end(); // End group
        $query = $this->db->get();

        return $query->result();
    }


    public function get_staff_with_highest_today_live_timer() {
        $all_staff = $this->get_all_staff();

        $highest_timer_staff = null;
        $highest_timer = 0;

        foreach ($all_staff as $staff) {
            $timer = $this->get_today_live_timer($staff->staffid);

            if ($timer > $highest_timer) {
                $highest_timer = $timer;
                $highest_timer_staff = $staff;
            }
        }

        return $highest_timer_staff;
    }

    public function get_staff_with_most_tasks_completed_today($date_start = null, $date_end = null)
    {
        $today_start = ($date_start == null) ? date('Y-m-d') : $date_start;
        $today_end = ($date_end == null) ? date('Y-m-d', strtotime(date("Y-m-d") . ' +1 day')) : $date_end;
        
        $most_eff_staff_member = null;

        $this->db->select('*');
        $query = $this->db->get(db_prefix() . 'staff');
        $all_staff_global = $query->result_array();

        $max_completed_tasks = 0;

        foreach ($all_staff_global as &$staff) {
            $staff_id = $staff['staffid'];

            $tasks = $this->team_management_model->get_tasks_by_staff_member($staff_id);
            $completed_tasks = 0;
            foreach ($tasks as $task) {

                $dueConsideration = ($task->duedate) ? $task->duedate : date("Y-m-d", strtotime($task->dateadded));
                $startConsideration = ($task->startdate) ? $task->startdate : date("Y-m-d", strtotime($task->dateadded));

                if (
                    (strtotime($startConsideration) <= strtotime($today_start) && strtotime($dueConsideration) >= strtotime($today_end)) 
                    || 
                    ($task->status != 5)
                )
                {
                    if ($task->status == 5) {
                        $completed_tasks++;
                    }
                }
            }

            if($max_completed_tasks < $completed_tasks){
                $max_completed_tasks = $completed_tasks;
                $most_eff_staff_member = $staff;
            }
        }

        

        $most_eff_staff_member = (object) $most_eff_staff_member;

        

        return $most_eff_staff_member;
    }

    public function get_today_live_timer($staff_id)
    {
        $totalTime = 0;

        $totalTime = $this->get_todays_total_time($staff_id);
        
        $current_entry = $this->db->where('staff_id', $staff_id)
                            ->where('clock_out IS NULL', null, false)
                            ->get(''.db_prefix().'_staff_time_entries')
                            ->row();
        if ($current_entry) {
            //$adjusted_clock_in_string = $current_entry->clock_in;
            //$adjusted_clock_in = DateTime::createFromFormat('Y-m-d H:i:s', $adjusted_clock_in_string);
            $current_shift_start = strtotime($current_entry->clock_in);

            //$adjusted_date_string = date('Y-m-d H:i:s');
            //$adjusted_date = DateTime::createFromFormat('Y-m-d H:i:s', $adjusted_date_string);
            $current_unix_timestamp = strtotime(date('Y-m-d H:i:s'));

            $elapsed_time = $current_unix_timestamp - $current_shift_start;
            $afk_and_offline_time = $this->get_total_afk_and_offline_time($staff_id, $current_entry->clock_in);
            $totalTime += $elapsed_time - $afk_and_offline_time;
        }

        return $totalTime;
    }

    public function get_timers($taskId, $staff_id) {
        $this->db->select('*');
        $this->db->from(''.db_prefix().'taskstimers');
        $this->db->where('task_id', $taskId);
        $this->db->where('staff_id', $staff_id);
        $this->db->where('end_time IS NULL', null, false);
        $query = $this->db->get();
        return $query->row();
    }
    
    
    public function get_current_task_by_staff_id($staff_id) {
        $this->db->select('task_id');
        $this->db->from(''.db_prefix().'taskstimers');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('end_time IS NULL');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->row();
    
        if ($result) {
            return $result->task_id;
        } else {
            return null;
        }
    }

    public function get_task_by_taskid($taskid) {
        $this->db->select('*');
        $this->db->from(''.db_prefix().'tasks');
        $this->db->where('id', $taskid);
        $query = $this->db->get();
        return $query->row();
    }

    public function clock_in($staff_id)
    {
        // Check if there's an existing open session for the staff member
        $this->db->where('staff_id', $staff_id);
        $this->db->where('clock_out IS NULL', null, false);
        $query = $this->db->get(db_prefix().'_staff_time_entries');
        
        if ($query->num_rows() > 0) {
            // If there's an open session, return false
            return false;
        }

        // Clock in the staff member
        $data = [
            'staff_id' => $staff_id,
            'clock_in' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert(db_prefix().'_staff_time_entries', $data);

        return $this->db->affected_rows() > 0;
    }

    public function clock_out($staff_id)
    {
        $now = date('Y-m-d H:i:s');
        $now_timestamp = time(); // Get the current Unix timestamp

        // Find the clock_in date for the latest open session
        $this->db->select('clock_in');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('clock_out IS NULL', null, false);
        $query = $this->db->get(db_prefix().'_staff_time_entries');
        $row = $query->row();

        if (isset($row)) {
            $clock_in_date = date("Y-m-d", strtotime($row->clock_in));
            if (!$this->team_management_model->get_staff_summary($staff_id, $clock_in_date)) {
                return ['success' => false, 'message' => "Please add your summary first."];
            }
        }

        // Stop the task timer for the staff member if there's any active timer
        $this->db->set('end_time', $now_timestamp);
        $this->db->where('staff_id', $staff_id);
        $this->db->where('end_time IS NULL', null, false);
        $this->db->update(db_prefix().'taskstimers');

        // Clock out the staff member by updating the latest open session
        $this->db->set('clock_out', $now);
        $this->db->where('staff_id', $staff_id);
        $this->db->where('clock_out IS NULL', null, false);
        $this->db->update(db_prefix().'_staff_time_entries');

        return ['success' => $this->db->affected_rows() > 0, 'message' => "Successfully clocked out"];
    }



    public function update_status($staff_id, $status)
    {
        // Check if the staff_id already exists in the table
        $query = $this->db->select('*')
                          ->from(''.db_prefix().'_staff_status')
                          ->where('staff_id', $staff_id)
                          ->get();

        // If staff_id exists, update the status
        if ($query->num_rows() > 0) {

            if($status == "AFK" || $status == "Offline"){
                $this->db->select("task_id");
                $this->db->from("tbltaskstimers");
                $this->db->where("staff_id", $staff_id);
                $this->db->where("end_time IS NULL", null, false);
                $this->db->order_by("id", "DESC");
                $this->db->limit(1);
                $query = $this->db->get();

                if($query->num_rows() > 0){
                    $this->db->set('last_working_task', $query->row()->task_id);
                }
            }else{

                $this->db->select("last_working_task");
                $this->db->from("tbl_staff_status");
                $this->db->where("staff_id", $staff_id);
                $query = $this->db->get();
                if($query->num_rows() > 0){
                    $last_working_task = $query->row()->last_working_task;
                    $data = array(
                        'task_id' => $last_working_task,
                        'start_time' => time(),
                        'staff_id' => $staff_id
                    );
                    if($last_working_task){
                        $this->db->insert('tbltaskstimers', $data);
                    }
                    
                }

                

                $this->db->set('last_working_task', null);
            }

            $this->db->set('status', $status);
            $this->db->where('staff_id', $staff_id);
            $this->db->update(''.db_prefix().'_staff_status');
        } 
        // Otherwise, insert a new row with staff_id and status
        else {
            $data = array(
                'staff_id' => $staff_id,
                'status' => $status
            );
            $this->db->insert(''.db_prefix().'_staff_status', $data);
        }

        $now_timestamp = time();

        if($status != "Online"){
            // Stop the task timer for the staff member if there's any active timer
            $this->db->set('end_time', $now_timestamp);
            $this->db->where('staff_id', $staff_id);
            $this->db->where('end_time IS NULL', null, false);
            $this->db->update(db_prefix().'taskstimers');
        }

        return $this->db->affected_rows() > 0;
    }

    public function get_stats($staff_id)
    {
        $stats = new stdClass();

        $current_entry = $this->db->where('staff_id', $staff_id)
                                ->where('clock_out IS NULL', null, false)
                                ->get(''.db_prefix().'_staff_time_entries')
                                ->row();

        if ($current_entry) {

            // Adjust clock_in time to the user's timezone
            //$adjusted_clock_in_string = $current_entry->clock_in;
            //$adjusted_clock_in = DateTime::createFromFormat('Y-m-d H:i:s', $adjusted_clock_in_string);
            $current_shift_start = strtotime($current_entry->clock_in);

            $total_afk_and_offline_time = $this->get_total_afk_and_offline_time($staff_id, $current_entry->clock_in);

            // Convert total_afk_and_offline_time to seconds and add to the current_shift_start
            $new_clock_in_time = $current_shift_start + $total_afk_and_offline_time;

            $stats->clock_in_time = date('Y-m-d H:i:s', $new_clock_in_time);

            //$adjusted_date_string = date('Y-m-d H:i:s');
            //$adjusted_date = DateTime::createFromFormat('Y-m-d H:i:s', $adjusted_date_string);
            $current_unix_timestamp = strtotime(date('Y-m-d H:i:s'));

            $elapsed_time = $current_unix_timestamp - $current_shift_start;


            $stats->total_afk_time = $total_afk_and_offline_time;
            $stats->total_time = $elapsed_time - $total_afk_and_offline_time;

        } else {
            $stats->clock_in_time = null;
            $stats->total_time = 0;
        }

        $current_entry = $this->db->where('staff_id', $staff_id)
                                ->get(''.db_prefix().'_staff_status')
                                ->row();
        if($current_entry){
            $stats->status = $current_entry->status;
        }else{
            $stats->status = "Status record not found!";
        }

        
        $stats->todays_total_time = $this->get_todays_total_time($staff_id);

        $stats->yesterdays_total_time = $this->get_yesterdays_total_time($staff_id);
        $stats->this_weeks_total_time = $this->get_this_weeks_total_time($staff_id);
        $stats->last_weeks_total_time = $this->get_last_weeks_total_time($staff_id);

        return $stats;
    }

    public function get_status($staff_id)
    {
        $current_entry = $this->db->where('staff_id', $staff_id)
                                ->get(''.db_prefix().'_staff_status')
                                ->row();
        if($current_entry){
            $status = $current_entry->status;
        }else{
            $status = "Status record not found!";
        }
        return $status;
    }


    public function end_previous_status($staff_id, $end_time)
    {
        $this->db->set('end_time', $end_time)
                ->where('staff_id', $staff_id)
                ->where('end_time IS NULL', null, false)
                ->update(''.db_prefix().'_staff_status_entries');
    }

    public function insert_status_entry($staff_id, $status, $start_time)
    {
        $data = [
            'staff_id' => $staff_id,
            'status' => $status,
            'start_time' => $start_time,
        ];

        $this->db->insert(''.db_prefix().'_staff_status_entries', $data);
    }

    public function get_total_afk_and_offline_time($staff_id, $current_shift_start)
    {   
        $nowDateTime = new DateTime('now');
        $nowDate = $nowDateTime->format('Y-m-d H:i:s');

        $this->db->select_sum('TIMESTAMPDIFF(SECOND, start_time, IFNULL(end_time, "'.$nowDate.'"))', 'total_time')
        ->where('staff_id', $staff_id)
        ->where('start_time >=', $current_shift_start)
        ->where_in('status', ['AFK', 'Offline']);
        $result = $this->db->get(''.db_prefix().'_staff_status_entries')->row();

        return $result->total_time;
        //return $this->db->last_query();
    }

    public function test_query($query) {
        $result = $this->db->query($query);
        return $result;
    }

    public function get_todays_total_time($staff_id)
    {
        $today_date = date('Y-m-d');
        return $this->get_total_time_of_date($staff_id, $today_date);
    }

    public function get_yesterdays_total_time($staff_id)
    {
        $yesterday_date = date('Y-m-d', strtotime('-1 day'));
        return $this->get_total_time_of_date($staff_id, $yesterday_date);
    }

    public function get_this_weeks_total_time($staff_id)
    {
        $week_start = date('Y-m-d 00:00:00', strtotime('monday this week'));
        $week_end = date('Y-m-d 23:59:59', strtotime('sunday this week'));
        return $this->get_total_time_within_range($staff_id, $week_start, $week_end);
    }

    public function get_last_weeks_total_time($staff_id)
    {
        $last_week_start = date('Y-m-d 00:00:00', strtotime('monday last week'));
        $last_week_end = date('Y-m-d 23:59:59', strtotime('sunday last week'));
        return $this->get_total_time_within_range($staff_id, $last_week_start, $last_week_end);
    }

    public function get_total_time_within_range($staff_id, $start_date, $end_date) {
        // Create a new DateTime object and modify it to the next day.
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $end = $end->modify('+1 day');
    
        // Create an interval of 1 day and an instance of DatePeriod.
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end);
    
        $total_time = 0;
    
        // Iterate over the period of days
        foreach ($period as $day) {
            $total_time += $this->get_total_time_of_date($staff_id, $day);
        }
    
        return $total_time;
    }
    

    public function get_total_time_of_date($staff_id, $date){

        if (is_string($date)) {
            $date = new DateTime($date);
        }

        $day = $date->format('d');
        $month = $date->format('m');
        $year = $date->format('Y');


        $this->db->select('*');
        $this->db->from(db_prefix() . '_staff_time_entries');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('DAY(clock_in)', $day);
        $this->db->where('MONTH(clock_in)', $month);
        $this->db->where('YEAR(clock_in)', $year);
        $query = $this->db->get();
        $clock_ins_outs = $query->result_array();

        $this->db->select('*');
        $this->db->from(db_prefix() . '_staff_status_entries');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('DAY(start_time)', $day);
        $this->db->where('MONTH(start_time)', $month);
        $this->db->where('YEAR(start_time)', $year);
        $query = $this->db->get();
        $afk_and_offline = $query->result_array();


        // Calculate the total clocked in time
        $total_clocked_in_time = 0;
    
        // Calculate total_clocked_in_time and total_shift_duration
        foreach ($clock_ins_outs as $clock_in_out) {

            $clock_in_time = strtotime($clock_in_out['clock_in']);
            if(!empty($clock_in_out['clock_out'])){
                $clock_out_time = strtotime($clock_in_out['clock_out']);
 
                $total_time = $clock_out_time - $clock_in_time;

                $sum_afk_offline_times = $this->team_management_model->get_sum_afk_and_offline_times($staff_id, $clock_in_out['clock_in'], $clock_in_out['clock_out']);
                
                $total_clocked_in_time += $total_time;
                $total_clocked_in_time -= $sum_afk_offline_times;
            }
        }


        return $total_clocked_in_time;
    }

    public function get_total_time_of_month($staff_id, $start_date, $end_date) {

        if (is_string($start_date)) {
            $start_date = new DateTime($start_date);
        }
        if (is_string($end_date)) {
            $end_date = new DateTime($end_date);
        }
    
        $this->db->select('*');
        $this->db->from(db_prefix() . '_staff_time_entries');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('clock_in >=', $start_date->format('Y-m-d'));
        $this->db->where('clock_in <=', $end_date->format('Y-m-d'));
        $query = $this->db->get();
        $clock_ins_outs = $query->result_array();
    
        $this->db->select('*');
        $this->db->from(db_prefix() . '_staff_status_entries');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('start_time >=', $start_date->format('Y-m-d'));
        $this->db->where('start_time <=', $end_date->format('Y-m-d'));
        $query = $this->db->get();
        $afk_and_offline = $query->result_array();
    
        // Calculate the total clocked in time
        $total_clocked_in_time = 0;
    
        // Calculate total_clocked_in_time and total_shift_duration
        foreach ($clock_ins_outs as $clock_in_out) {
    
            $clock_in_time = strtotime($clock_in_out['clock_in']);
            if(!empty($clock_in_out['clock_out'])){
                $clock_out_time = strtotime($clock_in_out['clock_out']);
    
                $total_time = $clock_out_time - $clock_in_time;
    
                $sum_afk_offline_times = $this->team_management_model->get_sum_afk_and_offline_times($staff_id, $clock_in_out['clock_in'], $clock_in_out['clock_out']);
                
                $total_clocked_in_time += $total_time;
                $total_clocked_in_time -= $sum_afk_offline_times;
            }
        }
    
        return $total_clocked_in_time;
    }
    

    public function save_shift_timings($staff_id, $month, $shift_timings) {
        // Delete existing shift timings for the staff member and month
        $this->db->where('staff_id', $staff_id)->where('month', $month)->delete(''.db_prefix().'_staff_shifts');
    
        // Insert new shift timings
        foreach ($shift_timings as $day => $shifts) {
            foreach ($shifts as $shift_number => $shift_time) {

                if((($shift_time['start']) && ($shift_time['end'])) || $shift_number != 3){

                    $this->db->insert(''.db_prefix().'_staff_shifts', [
                        'staff_id' => $staff_id,
                        'Year' => date("Y"),
                        'month' => $month,
                        'day' => $day,
                        'shift_number' => $shift_number,
                        'shift_start_time' => ($shift_time['start']) ? $shift_time['start'] : null,
                        'shift_end_time' => ($shift_time['end']) ? $shift_time['end'] : null,
                    ]);

                }
                
            }
        }
        
        return $this->db->affected_rows() > 0;
    }
    
    public function get_shift_timings($staff_id, $month) {
        $query = $this->db->where('staff_id', $staff_id)->where('month', $month)->get(''.db_prefix().'_staff_shifts');
        return $query->result_array();
    }

    public function get_staff_shift_details($staff_id, $month) {
        $this->db->select('day, shift_number, shift_start_time, shift_end_time');
        $this->db->from('tbl_staff_shifts');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('month', $month);
        $this->db->order_by('day', 'ASC');
        $this->db->order_by('shift_number', 'ASC');
        $query = $this->db->get();
    
        $shift_data = array();
        foreach ($query->result() as $row) {
            $shift_data[$row->day][$row->shift_number] = array(
                'start_time' => $row->shift_start_time,
                'end_time' => $row->shift_end_time,
            );
        }
    
        return $shift_data;
    }
    

    public function get_shifts_info($staff_id)
    {
        $dateTime = new DateTime("now", new DateTimeZone(get_option('default_timezone')));
        
        $current_month = $dateTime->format('m');
        $current_day = $dateTime->format('d');
        
        $current_time = $dateTime->format('H:i:s');

        $this->db->select('*');
        $this->db->from(''.db_prefix().'_staff_shifts');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('month', $current_month);
        $this->db->where('day', $current_day);
        $this->db->where('shift_end_time >=', $current_time);
        $this->db->order_by('shift_start_time', 'ASC');
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function get_staff_with_shifts() {

        $dateTime = new DateTime("now", new DateTimeZone(get_option('default_timezone')));
        $current_month = $dateTime->format('m');
        $current_day = $dateTime->format('d');

        $this->db->select(''.db_prefix().'staff.staffid, '.db_prefix().'staff.email, '.db_prefix().'_staff_shifts.shift_number, '.db_prefix().'_staff_shifts.shift_start_time, '.db_prefix().'_staff_shifts.shift_end_time');
        $this->db->from(''.db_prefix().'staff');
        $this->db->join(''.db_prefix().'_staff_shifts', ''.db_prefix().'_staff_shifts.staff_id = '.db_prefix().'staff.staffid');
        $this->db->where(''.db_prefix().'_staff_shifts.month', $current_month);
        $this->db->where(''.db_prefix().'_staff_shifts.day', $current_day);
        $query = $this->db->get();
    
        $staff_members = array();
        foreach ($query->result() as $row) {
            if (!isset($staff_members[$row->staffid])) {
                $staff_members[$row->staffid] = new stdClass();
                $staff_members[$row->staffid]->email = $row->email;
                $staff_members[$row->staffid]->shifts = array();
            }
            $shift = new stdClass();
            $shift->shift_number = $row->shift_number;
            $shift->shift_start_time = $row->shift_start_time;
            $shift->shift_end_time = $row->shift_end_time;
            $staff_members[$row->staffid]->shifts[] = $shift;
        }
    
        return $staff_members;
    }

    public function get_staff_shifts_for_month($staff_id, $month) {
        $this->db->select('tbl_staff_shifts.*, tblstaff.*');
        $this->db->from('tbl_staff_shifts');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_staff_shifts.staff_id');
        $this->db->where('tbl_staff_shifts.staff_id', $staff_id);
        $this->db->where('tbl_staff_shifts.month', $month);
        $this->db->order_by('tbl_staff_shifts.day', 'ASC');
        $this->db->order_by('tbl_staff_shifts.shift_number', 'ASC');
        $query = $this->db->get();

        return $query->num_rows() > 0 ? $query->result() : false;
    }
    
    
    public function get_user_activities($staffId, $month) {
        // Fetch shift start times from tbl_staff_time_entries
        $this->db->select("clock_in as time, 'Started Shift' as activity_type");
        $this->db->from('tbl_staff_time_entries');
        $this->db->where('staff_id', $staffId);
        $this->db->where('MONTH(clock_in)', $month);
        $this->db->order_by('clock_in', 'ASC');
        $query1 = $this->db->get_compiled_select();

        // Fetch AFK start and end times from tbl_staff_status_entries
        $this->db->select("start_time as time, CONCAT('Set ', status) as activity_type");
        $this->db->from('tbl_staff_status_entries');
        $this->db->where('staff_id', $staffId);
        $this->db->where('MONTH(start_time)', $month);
        $this->db->order_by('start_time', 'ASC');
        $query2 = $this->db->get_compiled_select();

        $this->db->select("end_time as time, 'Back to Online' as activity_type");
        $this->db->from('tbl_staff_status_entries');
        $this->db->where('staff_id', $staffId);
        $this->db->where('MONTH(end_time)', $month);
        $this->db->order_by('end_time', 'ASC');
        $query3 = $this->db->get_compiled_select();

        // Fetch shift end times from tbl_staff_time_entries
        $this->db->select("clock_out as time, 'Ended Shift' as activity_type");
        $this->db->from('tbl_staff_time_entries');
        $this->db->where('staff_id', $staffId);
        $this->db->where('MONTH(clock_out)', $month);
        $this->db->order_by('clock_out', 'ASC');
        $query4 = $this->db->get_compiled_select();

        // Combine queries using UNION
        $query = $this->db->query("($query1) UNION ($query2) UNION ($query3) UNION ($query4) ORDER BY time ASC");

        return $query->result_array();

    }

    public function get_staff_time_entries($staff_id, $date) {
        $this->db->where('staff_id', $staff_id);
        $this->db->where('DATE(clock_in)', $date);
        $query = $this->db->get('tbl_staff_time_entries');
        return $query->result_array();
    }

    public function save_application($application_data) {
        $this->db->insert('tbl_applications', $application_data);
        return $this->db->insert_id();
    }

    public function get_applications_by_staff_id($staff_id, $month = null) {
        $this->db->select('*');
        $this->db->from('tbl_applications');
        $this->db->where('staff_id', $staff_id);
        if($month){
            $this->db->where('MONTH(created_at)', $month);
        }
        $this->db->order_by('id DESC');
        $query = $this->db->get();

        return $query->result();
    }

    public function get_all_applications($status) {
        $this->db->select('*');
        $this->db->from('tbl_applications');
        $this->db->where('status', $status);
        $this->db->order_by('id DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_application($application_id) {
        $this->db->from('tbl_applications');
        $this->db->where('id', $application_id);
        $query = $this->db->get();
        $result = $query->row_array();
    
        return $result;
    } 

    public function update_application_status($application_id, $status) {
        // Get the application data.
        $application_data = $this->get_application($application_id);
    
        if (strpos($application_data['application_type'], 'Leave') !== false) {
            if ($status == 'Approved') {
                // Add a new leave row with the application data.
                $leave_data = array(
                    'application_id' => $application_id,
                    'staff_id' => $application_data['staff_id'],
                    'start_date' => $application_data['start_date'],
                    'end_date' => $application_data['end_date'],
                    'shift' => $application_data['shift'],
                    'reason' => $application_data['reason'],
                    'created_at' => $application_data['created_at'],
                );
                $this->db->insert('tbl_staff_leaves', $leave_data);
            } else {
                // Delete the leave row with the given application_id.
                $this->db->where('application_id', $application_id);
                $this->db->delete('tbl_staff_leaves');
            }
        }
    
        // Update the status.
        $this->db->where('id', $application_id);
        return $this->db->update('tbl_applications', ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
    }
    
    public function delete_application($application_id) {

        $application_data = $this->get_application($application_id);
    
        if (strpos($application_data['application_type'], 'Leave') !== false) {
            // Delete the leave row with the given application_id.
            $this->db->where('application_id', $application_id);
            $this->db->delete('tbl_staff_leaves');
        }

        $this->db->where('id', $application_id);
        return $this->db->delete('tbl_applications');
    }

    public function get_leaves_count($staff_id, $type, $status, $month = null)
    {
        $this->db->select('COUNT(*) as total_leaves');
        $this->db->from(db_prefix() . '_applications');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('application_type', $type);
        $this->db->where('status', $status);
        $this->db->where('YEAR(created_at)', 'YEAR(CURDATE())', false);
        if($month){
            $this->db->where('MONTH(created_at)', $month, false);
        }
        $query = $this->db->get();
        return $query->result_array();
    }
      
    
    public function get_leaves($staff_id) {
        $this->db->where('staff_id', $staff_id);
        $this->db->order_by('id DESC');
        $query = $this->db->get(db_prefix() . '_staff_leaves');
        return $query->result_array();
    }

    public function insert_leave($staff_id, $reason, $start_date, $end_date, $shift) {
        $data = [
            'staff_id' => $staff_id,
            'reason' => $reason,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'created_at' => date('Y-m-d H:i:s'),
            'shift' => $shift
        ];
        $success = $this->db->insert(db_prefix() . '_staff_leaves', $data);
        $leave_id = $this->db->insert_id();
        return array('success' => $success, 'id' => $leave_id);
    }

    public function remove_leave($leave_id) {
        $this->db->where('id', $leave_id);
        return $this->db->delete(db_prefix() . '_staff_leaves');
    }

    public function get_shift_timings_of_date($date, $staff_id) {
        $this->db->select('*');
        $this->db->from(db_prefix() . '_staff_shifts');
        $this->db->where('month', date('m', strtotime($date)));
        $this->db->where('day', date('d', strtotime($date)));
        $this->db->where('staff_id', $staff_id);
        $query = $this->db->get();
        $result = $query->result_array();
    
        $shift_timings = [
            'first_shift' => ['start' => null, 'end' => null],
            'second_shift' => ['start' => null, 'end' => null]
        ];
    
        foreach ($result as $row) {
            if ($row['shift_number'] == 1) {
                $shift_timings['first_shift']['start'] = $row['shift_start_time'];
                $shift_timings['first_shift']['end'] = $row['shift_end_time'];
            } elseif ($row['shift_number'] == 2) {
                $shift_timings['second_shift']['start'] = $row['shift_start_time'];
                $shift_timings['second_shift']['end'] = $row['shift_end_time'];
            }
        }
    
        return $shift_timings;
    }

    public function get_shift_timings_of_month($month, $year, $staff_id) {
        $total_shift_seconds = 0;
        
        // Get the number of days in the month
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        
        // Loop through each day of the month
        for($day = 1; $day <= $days_in_month; $day++) {
            $date = $year . '-' . $month . '-' . $day;
            
            $shift_timings = $this->get_shift_timings_of_date($date, $staff_id);
    
            $first_shift_start = ($shift_timings['first_shift']['start']) ? strtotime($shift_timings['first_shift']['start']) : 0;
            $first_shift_end = ($shift_timings['first_shift']['end']) ? strtotime($shift_timings['first_shift']['end']) : 0;
            $sec_shift_start = ($shift_timings['second_shift']['start']) ? strtotime($shift_timings['second_shift']['start']) : 0;
            $sec_shift_end = ($shift_timings['second_shift']['end']) ? strtotime($shift_timings['second_shift']['end']) : 0;

            $first_shift_seconds = $first_shift_end - $first_shift_start;
            $second_shift_seconds = $sec_shift_end - $sec_shift_start;

            
            $total_shift_seconds += $first_shift_seconds + $second_shift_seconds;
        }
    
        return $total_shift_seconds;
    }
    
    
    

    public function is_on_leave($staff_id, $date) {
        //return true;
        $shift_timings = $this->get_shift_timings_of_date($date, $staff_id);

        if (empty($shift_timings) || ($shift_timings['first_shift']['start'] == "00:00:00" && $shift_timings['second_shift']['start'] == "00:00:00")) {
            return true;
        }else{
    
        $this->db->select('*');
        $this->db->from(db_prefix() . '_staff_leaves');
        $this->db->where('staff_id', $staff_id);
        $this->db->where("DATE(start_date) <= DATE('{$date}')");
        $this->db->where("DATE(end_date) >= DATE('{$date}')");
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $leave = $query->row_array();

            // Get the current time on the given date.
            $current_time = date('H:i:s', strtotime($date));
    
            // Check if the staff member is on leave during the 1st or 2nd shift.
            if ($leave['shift'] == '1') {
                if ($current_time >= $shift_timings['first_shift']['start'] && $current_time <= $shift_timings['first_shift']['end']) {
                    return true;
                }
            } elseif ($leave['shift'] == '2') {
                if ($current_time >= $shift_timings['second_shift']['start'] && $current_time <= $shift_timings['second_shift']['end']) {
                    return true;
                }
            } elseif ($leave['shift'] == 'all') {
                return true;
            }
        }
        
        return false;
        }
    }
    
    public function is_clocked_in($staff_id) {
        $current_entry = $this->db->where('staff_id', $staff_id)
                        ->where('clock_out IS NULL', null, false)
                        ->get(''.db_prefix().'_staff_time_entries')
                        ->row();
        if ($current_entry) {
            $clock_out = true;
        }else{
            $clock_out = false;
        }

        return $clock_out;
    }
    
    public function clock_out_and_set_leave_status($staff_id) {
        if($this->is_clocked_in($staff_id)){
            $this->clock_out($staff_id);
        }
        
        $this->update_status($staff_id, "Leave");
    }

    public function get_sum_afk_and_offline_times($staff_id, $clock_in_time, $clock_out_time) {

        if(empty($clock_out_time)){
            $clock_out_time = strtotime(date("Y-m-d H:i:s"));
        }

        $this->db->select_sum('TIMESTAMPDIFF(SECOND, start_time, end_time)', 'total_duration');
        $this->db->from(db_prefix() . '_staff_status_entries');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('start_time >=', $clock_in_time);
        $this->db->where('end_time <=', $clock_out_time);
        $this->db->where_in('status', ['afk', 'offline']);
        $query = $this->db->get();
        $result = $query->row();
    
        return $result->total_duration;
    }
    
// work stats
    public function get_monthly_stats($staff_id, $month) {
        $current_month = $month;
        $current_year = date('Y');

        $monthly_total_clocked_time = 0;
        $monthly_shift_duration = 0;
        $punctuality_rate = 0;
    
        // Get shift timings
        $this->db->select('day, shift_start_time, shift_end_time');
        $this->db->from(db_prefix() . '_staff_shifts');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('month', $current_month);
        $shifts = $this->db->get()->result_array();
    
        // Group shifts by day
        $grouped_shifts = [];
        foreach ($shifts as $shift) {
            $day = $shift['day'];
            if (!isset($grouped_shifts[$day])) {
                $grouped_shifts[$day] = [];
            }

            $shift_start_time = date('h:i A', strtotime($shift['shift_start_time']));
            $shift_end_time = date('h:i A', strtotime($shift['shift_end_time']));


            $shift_start_time_plus_5_seconds = date('H:i:s', strtotime($shift['shift_start_time']) + 5);
            $shiftDateTime = date('Y-m-d H:i:s', strtotime($current_year.'-'.$current_month . '-' . $day . ' ' . $shift_start_time_plus_5_seconds));

            $shift_timings = [
                'start' => $shift_start_time,
                'end' => $shift_end_time,
                'is_shift_leave' => $this->is_on_leave($staff_id, $shiftDateTime)
            ];

            $grouped_shifts[$day][] =  $shift_timings;
        }
    
        // Get clock-in times
        $this->db->select('DATE(clock_in) as date, clock_in, clock_out');
        $this->db->from(db_prefix() . '_staff_time_entries');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('MONTH(clock_in)', $current_month);
        $this->db->where('YEAR(clock_in)', $current_year);
        $clock_ins = $this->db->get()->result_array();
    
        // Group clock-in times by day
        $grouped_clock = [];
        foreach ($clock_ins as $clock_in) {
            $day = date('j', strtotime($clock_in['date']));
            if (!isset($grouped_clock[$day])) {
                $grouped_clock[$day] = [];
            }
            $grouped_clock[$day]['in'][] = date('h:i A', strtotime($clock_in['clock_in']));
            if (isset($clock_in['clock_out'])) {
                $grouped_clock[$day]['out'][] = date('h:i A', strtotime($clock_in['clock_out']));
            }
            
        }
    
        // Combine shift timings and clock-in times for each day
        $data = [];
        for ($day = 1; $day <= date('t'); $day++) {
            $data[] = [
                'day' => $day,
                'shifts' => isset($grouped_shifts[$day]) ? $grouped_shifts[$day] : [],
                'clock' => isset($grouped_clock[$day]) ? $grouped_clock[$day] : [],
            ];
        }

        $total_shifts = 0;
        $sum_difference = 0;
        
        foreach ($data as &$stat) {
            $day = $stat['day'];
            $total_clock_in_time = 0;
            $total_shift_duration = 0;
            $total_task_time = 0;
            
    
            // Calculate total_clock_in_time
            $total_clock_in_time = $this->get_day_clocked_in_time($staff_id, $day, $current_month, $current_year);


            $clock_ins = $stat['clock']['in'] ?? [];
            $clock_outs = $stat['clock']['out'] ?? [];
            for ($i = 0; $i < count($clock_ins); $i++) {

                $date_time = DateTime::createFromFormat('Y-m-d h:i A', $current_year . '-' . $current_month . '-' . $day . ' ' . $clock_ins[$i]);
                $clock_in_formatted = $date_time->format('Y-m-d H:i:s');

                if (isset($clock_outs[$i])) {

                    if(strtotime($clock_outs[$i]) < strtotime($clock_ins[$i])){
                        $dayMod = $day + 1;
                        $date_time = DateTime::createFromFormat('Y-m-d h:i A', $current_year . '-' . $current_month . '-' . $dayMod . ' ' . $clock_outs[$i]);
                    }else{
                        $date_time = DateTime::createFromFormat('Y-m-d h:i A', $current_year . '-' . $current_month . '-' . $day . ' ' . $clock_outs[$i]);
                    }

                    
                    $clock_out_formatted = $date_time->format('Y-m-d H:i:s');

                } else {
                    $clock_out_formatted = date('Y-m-d H:i:s');
                }

                $sum_afk_offline_times = $this->team_management_model->get_sum_afk_and_offline_times($staff_id, $clock_in_formatted, $clock_out_formatted);

                $clock_in_time = strtotime($clock_in_formatted);
                $clock_out_time = strtotime($clock_out_formatted);

                if ($clock_out_time < $clock_in_time) {
                    $clock_out_time += 86400; // Add 24 hours (86400 seconds) to the clock_out_time.
                }

                $total_time = $clock_out_time - $clock_in_time;


                // Subtract the sum of AFK and Offline times from $total_time.
                $total_time -= $sum_afk_offline_times;

                //$total_clock_in_time += $total_time;

                //$total_clock_in_time = $clock_out_formatted;
            }
    
            // Calculate total_shift_duration
            foreach ($stat['shifts'] as $shift) {
                $shift_start = strtotime($shift['start']);
                $shift_end = strtotime($shift['end']);

                if ($shift_end < $shift_start) {
                    $shift_end += 86400; // Add 24 hours (86400 seconds) to the clock_out_time.
                }

                $duration = $shift_end - $shift_start;
                $total_shift_duration += $duration;
            }
    
            // Calculate total_task_time
            // Assuming you have a function like `get_total_task_time_for_day` in your model
            $total_task_time = $this->get_total_task_time_for_day($staff_id, $day, $current_month, $current_year);
    
            $stat['total_clock_in_time'] = gmdate('H\h i\m', $total_clock_in_time);
            $stat['total_shift_duration'] = gmdate('H\h i\m', $total_shift_duration);
            $stat['total_task_time'] = gmdate('H\h i\m', $total_task_time);

    
            // Concatenate shift timings and clock-in times
            $shift_timings_string = '';

            if (isset($stat['shifts'])) {

                foreach ($stat['shifts'] as $shift) {
                    if($shift['is_shift_leave'] == true){
                        $shiftStr = '<span class="bg-amber-200/50 px-2">Shift Leave</span><br>';
                    }else{
                        $shiftStr = '<span>'.  $shift['start'] . ' - ' . $shift['end'] .'</span><br>';
                    }

                    $shift_timings_string .= $shiftStr;
                }
                
            }

            $stat['shift_timings'] = $shift_timings_string;


            $clock_times = [];
            for ($i = 0; $i < count($clock_ins); $i++) {
                if (isset($clock_ins[$i]) && isset($clock_outs[$i])) {
                    $clock_times[] = $clock_ins[$i] . ' - ' . $clock_outs[$i];
                }
            }
            
            $stat['clock_times'] = implode('<br> ', $clock_times);

            $monthly_total_clocked_time += $total_clock_in_time;
            $monthly_shift_duration += $total_shift_duration;

            $shift_timings = $stat['shifts'];
            $clock_in_times = isset($stat['clock']['in']) ? $stat['clock']['in'] : [];

            $total_shifts += count($clock_in_times);
            for ($i = 0; $i < $total_shifts; $i++) {
                if (isset($clock_in_times[$i]) && isset($shift_timings[$i]['start'])) {
                    $shift_start = strtotime($shift_timings[$i]['start']);
                    $clock_in_time = strtotime($clock_in_times[$i]);
                    $difference = abs($shift_start - $clock_in_time);
                    $sum_difference += $difference;
                }
                
            }

            $timestampDay = mktime(0, 0, 0, $current_month, $day, $current_year);
            $date = date('Y-m-d', $timestampDay);

            $stat['day_date'] = $date;
            $stat['status'] = $this->check_staff_late($staff_id,$date);
            $tasks = $this->team_management_model->get_tasks_by_staff_member($staff_id);
            $total_tasks = 0;
            $total_completed_tasks = 0;

            $total_all_tasks = 0;
            $completed_tasks = 0;
            $today = $date;
        
            foreach ($tasks as $task) {

                $dueConsideration = ($task->duedate) ? $task->duedate : date("Y-m-d", strtotime($task->dateadded));
                $startConsideration = ($task->startdate) ? $task->startdate : date("Y-m-d", strtotime($task->dateadded));

                if (
                    (strtotime($startConsideration) <= strtotime($today) && strtotime($dueConsideration) >= strtotime($today)) 
                    || 
                    ($task->status != 5)
                )
                {
                    $total_tasks++;
                    if ($task->status == 5) {
                        $completed_tasks++;
                    }
                }
            }
        
            $task_rate_percentage = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100) : 0;
            $task_rate = $completed_tasks . '/' . $total_tasks . ' (' . $task_rate_percentage . '%)';
            $stat['task_rate'] = $task_rate;

            $total_all_tasks += $total_tasks;
            $total_completed_tasks += $completed_tasks;

            
        }

        $max_acceptable_difference = 10 * 60 * 60;

        $average_difference = ($total_shifts != 0) ? $sum_difference / $total_shifts : 0;
        $punctuality = 100 * (1 - $average_difference / $max_acceptable_difference);
        $punctuality = max(0, $punctuality); // Ensure punctuality does not go below 0% 
        
        return [
            'data' => $data,
            'monthly_total_clocked_time' => $monthly_total_clocked_time,
            'monthly_shift_duration' => $monthly_shift_duration,
            'punctuality_rate' => sprintf("%.2f%%", $punctuality)
        ];
    }
    
    public function get_total_task_time_for_day($staff_id, $day, $month, $year) {
        $this->db->select('start_time, end_time');
        $this->db->from(db_prefix() . 'taskstimers');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('DAY(FROM_UNIXTIME(start_time))', $day);
        $this->db->where('MONTH(FROM_UNIXTIME(start_time))', $month);
        $this->db->where('YEAR(FROM_UNIXTIME(start_time))', $year);
        $query = $this->db->get();
    
        $task_timers = $query->result_array();
        $total_task_time = 0;

        $dateTime = new DateTime("now", new DateTimeZone(get_option('default_timezone')));
        $currentUnixTimestamp = $dateTime->getTimestamp();
    
        foreach ($task_timers as $timer) {
            $start_time = $timer['start_time'];
            $end_time = isset($timer['end_time']) ? $timer['end_time'] : $currentUnixTimestamp;
            $duration = $end_time - $start_time;
            $total_task_time += $duration;
        }
    
        return $total_task_time;
    }

    public function get_day_clocked_in_time($staff_id, $day, $month, $year){
        $this->db->select('*');
        $this->db->from(db_prefix() . '_staff_time_entries');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('DAY(clock_in)', $day);
        $this->db->where('MONTH(clock_in)', $month);
        $this->db->where('YEAR(clock_in)', $year);
        $query = $this->db->get();
        $clock_ins_outs = $query->result_array();

        $total_clocked_in_time = 0;


         // Calculate total_clocked_in_time and total_shift_duration
         foreach ($clock_ins_outs as $clock_in_out) {

            $clock_in_time = strtotime($clock_in_out['clock_in']);

            if(empty($clock_in_out['clock_out'])){
                $clock_out_time = time();
            }else{
                $clock_out_time = strtotime($clock_in_out['clock_out']);
            }
            

            $total_time = $clock_out_time - $clock_in_time;

            $sum_afk_offline_times = $this->team_management_model->get_sum_afk_and_offline_times($staff_id, $clock_in_out['clock_in'], $clock_in_out['clock_out']);

            $total_clocked_in_time -= $sum_afk_offline_times;

            $total_clocked_in_time += $total_time;
        }

        return $total_clocked_in_time;
    }
    
    public function get_daily_stats($staff_id, $day, $month, $year) {
        $date = $year.'-'.$month.'-'.$day;

        $this->db->select('*');
        $this->db->from(db_prefix() . '_staff_time_entries');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('DAY(clock_in)', $day);
        $this->db->where('MONTH(clock_in)', $month);
        $this->db->where('YEAR(clock_in)', $year);
        $query = $this->db->get();
        $clock_ins_outs = $query->result_array();
    
        $this->db->select('*');
        $this->db->from(db_prefix() . '_staff_status_entries');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('DAY(start_time)', $day);
        $this->db->where('MONTH(start_time)', $month);
        $this->db->where('YEAR(start_time)', $year);
        $query = $this->db->get();
        $afk_and_offline = $query->result_array();
    
        $this->db->select('*');
        $this->db->from(db_prefix() . '_staff_shifts');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('day', $day);
        $this->db->where('month', $month);
        $query = $this->db->get();
        $shift_timings = $query->result_array();
    
        $this->db->select('t.id as task_id, t.name as task_name, p.id as project_id, p.name as project_name, tt.start_time, tt.end_time');
        $this->db->from(db_prefix() . 'taskstimers tt');
        $this->db->join(db_prefix() . 'tasks t', 'tt.task_id = t.id', 'left');
        $this->db->join(db_prefix() . 'projects p', 't.rel_id = p.id AND t.rel_type = "project"', 'left');
        $this->db->where('tt.staff_id', $staff_id);
        $this->db->where('DAY(FROM_UNIXTIME(tt.start_time))', $day);
        $this->db->where('MONTH(FROM_UNIXTIME(tt.start_time))', $month);
        $this->db->where('YEAR(FROM_UNIXTIME(tt.start_time))', $year);
        $query = $this->db->get();
        $task_timers = $query->result_array();

        // Fetch all tasks
        $this->db->select('tbltasks.id as task_id, tbltasks.name as task_name, tblprojects.name as project_name, tblprojects.id as project_id,tbltasks.startdate as Assigned_Date, tbltasks.duedate, tbltasks.startdate, tbltasks.datefinished as Completed_Date, GROUP_CONCAT(tbltask_assigned.staffid) as assignees');
        $this->db->from('tbltasks');
        $this->db->join('tblprojects', 'tbltasks.rel_id = tblprojects.id AND tbltasks.rel_type =\'project\'', 'left');
        $this->db->join('tbltask_assigned', 'tbltasks.id = tbltask_assigned.taskid');
        $this->db->where('tbltask_assigned.staffid', $staff_id);
        
        $this->db->where('(
            (DATE(tbltasks.startdate) <= "'.$date.'" AND DATE(tbltasks.duedate) >= "'.$date.'")
        )', NULL, FALSE);
        
        
        $this->db->group_by('tbltasks.id');
        $query = $this->db->get();
        $all_tasks = $query->result_array();
     
        // Calculate total time for each task
        foreach ($all_tasks as $index => $task) {
 
            $this->db->select('tbltaskstimers.start_time, tbltaskstimers.end_time');
            $this->db->from(db_prefix() . 'taskstimers');
            $this->db->join(db_prefix() . 'tasks', 'tbltaskstimers.task_id = tbltasks.id', 'left');
 
            $this->db->where('tbltaskstimers.staff_id', $staff_id);
            $this->db->where('tbltasks.id', $task['task_id']);
            $query = $this->db->get();
            $task_timers_local = $query->result_array();
 
            $total_task_time = 0;
 
            foreach ($task_timers_local as &$timer) {
                if(!$timer['end_time']) $timer['end_time'] = time();
                $duration_seconds = $timer['end_time'] - $timer['start_time'];
                $total_task_time += $duration_seconds;
              }
 
            $hours = floor($total_task_time / 3600);
            $minutes = floor(($total_task_time % 3600) / 60);
 
            $duration_formatted = $hours . 'h ' . $minutes . 'm';
     
            // Add total time to the task array
            $all_tasks[$index]['Total_Time_Taken'] = $duration_formatted;
            
            $all_tasks[$index]['Assigned_Date'] = date("Y-m-d", strtotime($all_tasks[$index]['Assigned_Date']));

            if($all_tasks[$index]['Completed_Date']){
                
                $all_tasks[$index]['Completed_Date'] = date("Y-m-d", strtotime($all_tasks[$index]['Completed_Date']));
                
                $all_tasks[$index]['Days_Offset'] = (date("d", strtotime($all_tasks[$index]['Completed_Date']) - strtotime($all_tasks[$index]['duedate'])) - 1) . ' days';
            }else{
                $all_tasks[$index]['Completed_Date'] = '';
                $all_tasks[$index]['Days_Offset'] = '';
            }

        }
     

    
        // Calculate the total clocked in time, shift duration, and total task time
        $total_clocked_in_time = 0;
        $total_shift_duration = 0;
        $total_task_time = 0;

        $total_no_tasks = 0;
        $total_completed_tasks = 0;
        $tasks_rate = 0;

        foreach ($all_tasks as $index => $task) {
            $task['Assigned_Date'] = date("Y-m-d", strtotime($task['Assigned_Date']));
            $task['Completed_Date'] = date("Y-m-d", strtotime($task['Completed_Date']));
            $total_no_tasks += 1;
            if($task['Assigned_Date'] == $task['Completed_Date']){
                $total_completed_tasks += 1;
            }
        }

        if($total_no_tasks != 0){
            $tasks_rate = round(($total_completed_tasks / $total_no_tasks)*100, 2);
        }else{
            $tasks_rate = 0;
        }
        

    
        // Calculate total_clocked_in_time and total_shift_duration
        foreach ($clock_ins_outs as $clock_in_out) {

            $clock_in_time = strtotime($clock_in_out['clock_in']);

            if(empty($clock_in_out['clock_out'])){
                $clock_out_time = time();
            }else{
                $clock_out_time = strtotime($clock_in_out['clock_out']);
            }
            

            $total_time = $clock_out_time - $clock_in_time;

            $sum_afk_offline_times = $this->team_management_model->get_sum_afk_and_offline_times($staff_id, $clock_in_out['clock_in'], $clock_in_out['clock_out']);

            $total_clocked_in_time -= $sum_afk_offline_times;

            $total_clocked_in_time += $total_time;
        }

        // Calculate total_shift_duration
        foreach ($shift_timings as $shift_timing) {
            $shift_start_time = strtotime($shift_timing['shift_start_time']);
            $shift_end_time = strtotime($shift_timing['shift_end_time']);
            $shift_duration = $shift_end_time - $shift_start_time;
            $total_shift_duration += $shift_duration;
        }

        foreach ($afk_and_offline as &$entry) {
            $entry['start_time'] = date('h:i A', strtotime($entry['start_time']));
            $entry['end_time'] = date('h:i A', strtotime($entry['end_time']));
        
            $start_unix = strtotime($entry['start_time']);
            $end_unix = strtotime($entry['end_time']);
        
            if ($end_unix < $start_unix) {
                $end_unix += 86400; // Add 24 hours (86400 seconds) to the end_unix.
            }
        
            $duration_seconds = $end_unix - $start_unix;
        
            $hours = floor($duration_seconds / 3600);
            $minutes = floor(($duration_seconds % 3600) / 60);
        
            $duration_formatted = $hours . 'h ' . $minutes . 'm';
        
            $entry['duration'] = $duration_formatted;
        }        

        foreach ($task_timers as &$timer) {

            if($timer['end_time']){
                $duration_seconds = $timer['end_time'] - $timer['start_time'];
            }else{
                $duration_seconds = time() - $timer['start_time'];
            }

            $hours = floor($duration_seconds / 3600);
            $minutes = floor(($duration_seconds % 3600) / 60);

            $duration_formatted = $hours . 'h ' . $minutes . 'm';

            

            $timer['duration'] = $duration_formatted;
            $timer['start_time'] = date('g:i A', $timer['start_time']);
            $timer['end_time'] = ($timer['end_time']) ? date('g:i A', $timer['end_time']) : 'Going On';
          }
          
    
        $total_task_time = $this->get_total_task_time_for_day($staff_id, $day, $month, $year);
    
        $daily_stats = [
            'clock_ins_outs' => $clock_ins_outs,
            'afk_and_offline' => $afk_and_offline,
            'shift_timings' => $shift_timings,
            'task_timers' => $task_timers,
            'all_tasks' => $all_tasks,
            'total_clocked_in_time' => gmdate('H\h i\m', $total_clocked_in_time),
            'total_shift_duration' => gmdate('H\h i\m',$total_shift_duration),
            'total_task_time' => gmdate('H\h i\m',$total_task_time),
            'total_no_tasks' => $total_no_tasks,
            'total_completed_tasks' => $total_completed_tasks,
            'tasks_rate' => $tasks_rate
        ];

        return $daily_stats;
    }
// daily report daata 
    public function get_daily_report_data($month, $day)
    {
        $date = date('Y') . '-' . $month . '-' . $day;

        $report_data = [];

        // Total Loggable Hours
        $this->db->select_sum(
            'CASE WHEN shift_end_time < shift_start_time 
            THEN TIMESTAMPDIFF(SECOND, shift_start_time, ADDTIME(shift_end_time, "24:00:00")) 
            ELSE TIMESTAMPDIFF(SECOND, shift_start_time, shift_end_time) 
            END', 
            'total_loggable_hours');        
        $this->db->from(db_prefix() . '_staff_shifts');
        $this->db->where('month', date('m', strtotime($date)));
        $this->db->where('day', date('d', strtotime($date)));
        $this->db->where(db_prefix() . '_staff_shifts.staff_id !=', 1);  // This line excludes staff with ID=1
        $this->db->where("NOT EXISTS (SELECT " . db_prefix() . "_staff_leaves.staff_id FROM " . db_prefix() . "_staff_leaves WHERE " . db_prefix() . "_staff_leaves.staff_id = " . db_prefix() . "_staff_shifts.staff_id AND " . db_prefix() . "_staff_leaves.start_date <= '" . $date . "' AND " . db_prefix() . "_staff_leaves.end_date >= '" . $date . "')");
        $query = $this->db->get();
        $report_data['total_loggable_hours'] = $query->row()->total_loggable_hours;

        // Fetching day-wise shifts
         $this->db->select('*');
        $this->db->from(db_prefix() . '_staff_shifts');
        $this->db->where('month', date('m', strtotime($date)));
        $this->db->where('day', date('d', strtotime($date)));
        $this->db->where(db_prefix() . '_staff_shifts.staff_id !=', 1);  // This line excludes staff with ID=1
        $query = $this->db->get();
        $daywise_shifts = $query->result_array();
        

        $daywise_shift_data = [];
        foreach ($daywise_shifts as $shift) {
            $staff_id = $shift['staff_id'];
            $daywise_shift_data[$staff_id][] = [
                'start_time' => $shift['shift_start_time'],
                'end_time' => $shift['shift_end_time']
            ];
        }
        $report_data['shift_timings_daywise'] = $daywise_shift_data;

        // End of processing day-wise shifts


        // All Tasks Worked On
        $this->db->select('task_id, staff_id, SUM(TIMESTAMPDIFF(SECOND, FROM_UNIXTIME(start_time), FROM_UNIXTIME(end_time))) as total_worked_time');
        $this->db->from(db_prefix() . 'taskstimers');
        $this->db->where('DATE(FROM_UNIXTIME(start_time))', $date);
        $this->db->where(db_prefix() . 'taskstimers.staff_id !=', 1);  // This line excludes staff with ID=1
        $this->db->group_by(['task_id', 'staff_id']);
        $query = $this->db->get();
        $all_tasks_worked_on = $query->result_array();

        // Initialize an array to hold total task time for each staff
        $total_task_time_by_staff = [];

        // Sum up total time worked on tasks for each staff
        foreach ($all_tasks_worked_on as $task) {
            $staff_id = $task['staff_id'];
            $total_worked_time = $task['total_worked_time'];
            
            if (!isset($total_task_time_by_staff[$staff_id])) {
                $total_task_time_by_staff[$staff_id] = 0;
            }
            
            $total_task_time_by_staff[$staff_id] += $total_worked_time;
        }

        $report_data['total_task_time'] = $total_task_time_by_staff;


        // times clock in
        $this->db->select('staff_id, DATE(clock_in) as date, clock_in, clock_out');
        $this->db->from(db_prefix() . '_staff_time_entries');
        $this->db->where('DATE(clock_in)', $date); // Fetch records for specific date
        $this->db->where(db_prefix() . '_staff_time_entries.staff_id !=', 1);  // Exclude staff with ID=1
        $clock_ins = $this->db->get()->result_array();
        
        $clock_times = [];
        foreach ($clock_ins as $clock_in) {
            $staff_id = $clock_in['staff_id'];
            if (!isset($clock_times[$staff_id])) {
                $clock_times[$staff_id] = [];
            }
            
            if (isset($clock_in['clock_in'])) {
                if(isset($clock_in['clock_out'])){
                    $clock_times[$staff_id][] = date('h:i A', strtotime($clock_in['clock_in'])) . ' - ' . date('h:i A', strtotime($clock_in['clock_out']));
                }else{
                    $clock_times[$staff_id][] = date('h:i A', strtotime($clock_in['clock_in'])) . ' - ' . date('h:i A');
                }
                
            }
        }
        
        foreach ($clock_times as $staff_id => $times) {
            $clock_times[$staff_id] = implode('<br>', $times);
        }
        
        $report_data['clock_times'] = $clock_times;
        

        // Actual Total Logged in Time
        $this->db->select('*');
        $this->db->where(db_prefix() . 'staff.staffid !=', 1);  // This line excludes staff with ID=1
        $this->db->where('tblstaff.active', 1);
        $query = $this->db->get(db_prefix() . 'staff');
        $all_staff_global = $query->result_array();
        
        $actual_total_logged_in_time = 0;

        $total_all_tasks = 0;
        $total_completed_tasks = 0;
        $total_tasks_rate = 0;

        $most_clocked_time = 0;
        $most_clocked_in_staff_member = null;
        
        foreach ($all_staff_global as &$staff) {
            $staff_id = $staff['staffid'];
        
            // Get total time within range
            $total_time = $this->get_total_time_of_date($staff_id, $date);
            $staff['total_logged_in_time'] = $total_time;

            if($total_time > $most_clocked_time){
                $most_clocked_in_staff_member = $staff;
                $most_clocked_time = $total_time;
            }
            
            $actual_total_logged_in_time += $total_time;

            $total_tasks = $this->get_task_stats_by_staff_date($staff_id, $date)['total_tasks'];
            $completed_tasks = $this->get_task_stats_by_staff_date($staff_id, $date)['completed_tasks'];


        
            $task_rate_percentage = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100) : 0;
            $task_rate = $completed_tasks . '/' . $total_tasks . ' (' . $task_rate_percentage . '%)';
            $staff['task_rate'] = $task_rate;

            $total_all_tasks += $total_tasks;
            $total_completed_tasks += $completed_tasks;
            

            $shift_timings = $this->get_shift_timings_of_date($date, $staff_id);

            $first_shift_start = isset($shift_timings['first_shift']['start']) ? $shift_timings['first_shift']['start'] : '00:00:00';
            $first_shift_end = isset($shift_timings['first_shift']['end']) ? $shift_timings['first_shift']['end'] : '00:00:00';
            $sec_shift_start = isset($shift_timings['second_shift']['start']) ? $shift_timings['second_shift']['start'] : '00:00:00';
            $sec_shift_end = isset($shift_timings['second_shift']['end']) ? $shift_timings['second_shift']['end'] : '00:00:00';

            $first_shift_start_time = new DateTime($first_shift_start);
            $first_shift_end_time = new DateTime($first_shift_end);
            $sec_shift_start_time = new DateTime($sec_shift_start);
            $sec_shift_end_time = new DateTime($sec_shift_end);

            if ($sec_shift_end_time < $sec_shift_start_time) {
                $sec_shift_end_time->modify('+1 day');
            }
            

            $first_shift_seconds = $first_shift_end_time->getTimestamp() - $first_shift_start_time->getTimestamp();
            $second_shift_seconds = $sec_shift_end_time->getTimestamp() - $sec_shift_start_time->getTimestamp();

            $total_shift_seconds = $first_shift_seconds + $second_shift_seconds;


            // Add total_shift_timings to the staff array
            $staff['total_shift_timings'] = $total_shift_seconds;
        }

        $total_tasks_rate = $total_all_tasks > 0 ? round(($total_completed_tasks / $total_all_tasks) * 100) : 0;

        $report_data['total_all_tasks'] = $total_all_tasks;
        $report_data['total_completed_tasks'] = $total_completed_tasks;
        $report_data['total_tasks_rate'] = $total_tasks_rate;
        
        $report_data['actual_total_logged_in_time'] = $actual_total_logged_in_time;


        // Total Present Staff
        $this->db->select('COUNT(DISTINCT staff_id) as total_present_staff');
        $this->db->select('staff_id, firstname');
        $this->db->where('tblstaff.staffid !=', 1);  // This line excludes staff with ID=1
        $this->db->where('tblstaff.active', 1);
        $this->db->from(db_prefix() . '_staff_time_entries');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . '_staff_time_entries.staff_id');
        $this->db->where('DATE(clock_in)', $date);
$this->db->where(db_prefix() . '_staff_time_entries.staff_id !=', 1);  // This line excludes staff with ID=1

        $this->db->group_by('staff_id');
        $query = $this->db->get();
        $report_data['total_present_staff'] = $query->num_rows(); // Get the total number of present staff
        $report_data['present_staff_list'] = $query->result_array(); // Get the list of present staff with their id and firstname

        
        $this->db->select('staffid, firstname');
        $this->db->from(db_prefix() . 'staff');
        $this->db->where('staffid !=', 1);  // This line excludes staff with ID=1
        $this->db->where('active', 1);
        $all_staff = $this->db->get()->result_array();


        $present_staff = $report_data['present_staff_list'];
        $staff_on_leave = $this->get_staff_on_leave($date);
        $present_staff_ids = array_column($present_staff, 'staff_id');
        $staff_on_leave_ids = array_column($staff_on_leave, 'staff_id');

        $absent_staff = array_filter($all_staff, function ($staff) use ($present_staff_ids, $staff_on_leave_ids) {
            return !in_array($staff['staffid'], $present_staff_ids) && !in_array($staff['staffid'], $staff_on_leave_ids);
        });

        $report_data['absentees'] = $absent_staff;

        // Fetch all tasks worked on, grouped by projects
        $this->db->select('*, SUM(TIMESTAMPDIFF(SECOND, FROM_UNIXTIME(start_time), FROM_UNIXTIME(end_time))) as total_worked_time');
        $this->db->select('IF('.db_prefix().'tasks.rel_type="project", '.db_prefix().'projects.name, NULL) as project_name', false);
        $this->db->select(db_prefix().'tasks.rel_id as project_task_id');
        $this->db->select(db_prefix().'tasks.name as task_name');
        $this->db->select(db_prefix().'tasks.status as task_status');
        $this->db->from(db_prefix() . 'taskstimers');
        $this->db->where('DATE(FROM_UNIXTIME(start_time))', $date);
        $this->db->join(db_prefix() . 'tasks', db_prefix() . 'tasks.id = ' . db_prefix() . 'taskstimers.task_id');
        $this->db->join(db_prefix() . 'projects', db_prefix() . 'projects.id = ' . db_prefix() . 'tasks.rel_id AND '.db_prefix().'tasks.rel_type = "project"', 'left');
        $this->db->group_by(db_prefix() . 'projects.id, '.db_prefix().'taskstimers.task_id');
        $query = $this->db->get();
        $tasks = $query->result_array();

        // Group tasks by their project IDs
        $groupedTasks = [];
        foreach($tasks as $task) {
            $projectId = $task['project_task_id'];

            $task['staff'] = $this->get_staff_members_for_task($task['task_id'], $date);
            $groupedTasks[$projectId]['project_name'] = $task['project_name'];
            $groupedTasks[$projectId]['tasks'][] = $task;
            $groupedTasks[$projectId]['project_id'] = $projectId;
        }
        $report_data['all_tasks_worked_on'] = $groupedTasks;




        $report_data['late_joiners'] = $this->get_on_time_and_late_staff($date)['late_joiners'];
        $report_data['on_timers'] = $this->get_on_time_and_late_staff($date)['on_timers'];

        $report_data['staff_on_leave'] = $this->get_staff_on_leave($date);
        $report_data['most_clocked_in_staff_member'] = $most_clocked_in_staff_member;
       

        $report_data['all_staff'] = $all_staff_global;



        $maxTasksCompleted = $this->get_staff_with_most_tasks_completed_today($date,$date);

        if($maxTasksCompleted){
            $maxTasksCompleted = $maxTasksCompleted;
        }else{
            $maxTasksCompleted = null;
        }

        $report_data['most_eff_staff_member'] = $maxTasksCompleted;


        
        return $report_data;
    }


    public function get_monthly_report_data($month, $year)
    {
        $start_date = $year . '-' . $month . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));

        $start_date_obj = new DateTime($start_date);
        $end_date_obj = new DateTime($end_date);

        $report_data = [];

        // Total Loggable Hours for the month
        $this->db->select_sum('TIMESTAMPDIFF(SECOND, shift_start_time, shift_end_time)', 'total_loggable_hours');
        $this->db->from(db_prefix() . '_staff_shifts');
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        $this->db->where(db_prefix() . '_staff_shifts.staff_id !=', 1);  // This line excludes staff_id 1 from the query

        // Exclude staff on leave in the given month
        $this->db->where("NOT EXISTS (SELECT " . db_prefix() . "_staff_leaves.staff_id FROM " . db_prefix() . "_staff_leaves WHERE " . db_prefix() . "_staff_leaves.staff_id = " . db_prefix() . "_staff_shifts.staff_id AND (" . db_prefix() . "_staff_leaves.start_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' OR " . db_prefix() . "_staff_leaves.end_date BETWEEN '" . $start_date . "' AND '" . $end_date . "'))");
        $query = $this->db->get();
        $report_data['total_loggable_hours'] = $query->row()->total_loggable_hours;


        // Actual Total Logged in Time for the month
        $this->db->select('*');
$this->db->where('staffid !=', 1); // Excluding staff with ID 1

        $query = $this->db->get(db_prefix() . 'staff');

        $all_staff_global = $query->result_array();

        $actual_total_logged_in_time = 0;
        $total_all_tasks = 0;
        $total_completed_tasks = 0;

        $max_completed_tasks = 0;

        foreach ($all_staff_global as &$staff) {
            $staff_id = $staff['staffid'];

            // Get total time within range
            $total_time = $this->get_total_time_of_month($staff_id, $start_date, $end_date);
            $staff['total_logged_in_time'] = $total_time;

            $actual_total_logged_in_time += $total_time;

            $tasks = $this->team_management_model->get_tasks_by_staff_member($staff_id);
            $total_tasks = 0;
            $completed_tasks = 0;


            foreach ($tasks as $task) {

                $task_start_date = ($task->startdate) ? date('Y-m-d', strtotime($task->startdate)) : date('Y-m-d', strtotime($task->dateadded));


                $task_end_date = isset($task->datefinished) ? date('Y-m-d', strtotime($task->datefinished)) : $task_start_date;

                $duedate = ($task->duedate) ? strtotime($task->duedate) : strtotime($task->dateadded);
                

                if (($task_start_date >= $start_date && $task_start_date <= $end_date) || ($task_end_date >= $start_date && $task_end_date <= $end_date)) {
                    $total_tasks++;
                    if($task->datefinished){
                        if ($task->status == 5 && $duedate >= strtotime(date("Y-m-d", strtotime($task->datefinished)))) {
                            $completed_tasks++;
                        }
                    }
                    
                }
            }

            $task_rate_percentage = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100) : 0;
            $task_rate = $completed_tasks . '/' . $total_tasks . ' (' . $task_rate_percentage . '%)';
            $staff['task_rate'] = $task_rate;

            $total_all_tasks += $total_tasks;
            $total_completed_tasks += $completed_tasks;

            $staff['total_shift_timings'] = $this->get_shift_timings_of_month($month, $year, $staff_id);

            if($max_completed_tasks < $completed_tasks){
                $max_completed_tasks = $completed_tasks;
                $most_eff_staff_member = $staff;
            }
        }

        $total_tasks_rate = $total_all_tasks > 0 ? round(($total_completed_tasks / $total_all_tasks) * 100) : 0;

        $report_data['total_all_tasks'] = $total_all_tasks;
        $report_data['total_completed_tasks'] = $total_completed_tasks;
        $report_data['total_tasks_rate'] = $total_tasks_rate;

        $report_data['actual_total_logged_in_time'] = $actual_total_logged_in_time;


        $this->db->select('*, SUM(TIMESTAMPDIFF(SECOND, FROM_UNIXTIME(start_time), FROM_UNIXTIME(end_time))) as total_worked_time');
        $this->db->select('IF('.db_prefix().'tasks.rel_type="project", '.db_prefix().'projects.name, NULL) as project_name', false);
        $this->db->select('IF('.db_prefix().'tasks.rel_type="project", '.db_prefix().'projects.id, NULL) as project_id', false);
        $this->db->select(db_prefix().'tasks.name as task_name');
        $this->db->select(db_prefix().'tasks.status as task_status');
        $this->db->distinct();
        $this->db->from(db_prefix() . 'taskstimers');
        $this->db->where('MONTH(FROM_UNIXTIME(start_time)) >=', $start_date_obj->format('m'));
        $this->db->where('MONTH(FROM_UNIXTIME(start_time)) <=', $end_date_obj->format('m'));
        $this->db->where('YEAR(FROM_UNIXTIME(start_time))', $start_date_obj->format('Y'));
$this->db->where(db_prefix() . 'taskstimers.staff_id !=', 1); // This line excludes staff_id 1 from the query

        $this->db->join(db_prefix() . 'tasks', db_prefix() . 'tasks.id = ' . db_prefix() . 'taskstimers.task_id');
        $this->db->join(db_prefix() . 'projects', db_prefix() . 'projects.id = ' . db_prefix() . 'tasks.rel_id AND '.db_prefix().'tasks.rel_type = "project"', 'left');
        $this->db->group_by(db_prefix() . 'taskstimers.task_id');
        $query = $this->db->get();
        $report_data['all_tasks_worked_on'] = $query->result_array();

        foreach ($report_data['all_tasks_worked_on'] as &$task) {
            $task['staff'] = $this->get_staff_members_for_task_month($task['task_id'], $month, $year);
        }

        // Get the number of days in the month
        $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Initialize an array to hold all daily reports
        $report_data['all_daily_reports'] = [];

        // Loop through all days in the month
        for ($day = 1; $day <= $num_days; $day++) {
            $daily_report = $this->get_daily_report_data($month, $day);
            $report_data['all_daily_reports'][$day] = $daily_report;
        }
        
        $report_data['most_clocked_in_staff_member'] = $this->get_most_clocked_in_staff_member_monthly($start_date, $end_date);

        $report_data['all_staff'] = $all_staff_global;
        

        // Initialize the variable before using it
        $most_eff_staff_member = array(); // or some default value suitable for your logic


        // $most_eff_staff_member = (object) $most_eff_staff_member;
        // if(!$most_eff_staff_member) {
        //     $most_eff_staff_member = null;
        // }

        // $report_data['most_eff_staff_member'] = $most_eff_staff_member;


        return $report_data;
    }


    public function get_staff_members_for_task($task_id, $date) {
        $this->db->select(db_prefix() . 'taskstimers.staff_id');
        $this->db->distinct();
        $this->db->from(db_prefix() . 'taskstimers');
        $this->db->where('task_id', $task_id);
        $this->db->where('DATE(FROM_UNIXTIME(start_time))', $date);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_staff_members_for_task_month($task_id, $month, $year) {
        $this->db->select(db_prefix() . 'taskstimers.staff_id');
        $this->db->distinct();
        $this->db->from(db_prefix() . 'taskstimers');
        $this->db->where('task_id', $task_id);
        $this->db->where('MONTH(FROM_UNIXTIME(start_time))', $month);
        $this->db->where('YEAR(FROM_UNIXTIME(start_time))', $year);
$this->db->where('staff_id !=', 1);  // Exclude staff with staff_id = 1
        $query = $this->db->get();
        return $query->result_array();
    }
    
    
    
    public function get_on_time_and_late_staff($date)
    {   
        // Get all staff data
        $this->db->select('*');
        $this->db->from('tblstaff');
        $this->db->where('staffid !=', 1);
        $this->db->where('active', 1);  // Exclude staff with staff_id = 1

        $staffs = $this->db->get()->result();

        $on_timers = array();
        $late_joiners = array();

        // Iterate each staff
        foreach($staffs as $staff) {
            
            $late_status = $this->check_staff_late($staff->staffid, $date);

            if($late_status['status'] === 'absent'){
                // Handle absent case if needed
                continue;
            }

            $staff->late_status = $late_status;  // Attach the entire $late_status data to $staff object
            // Check if staff is late in first shift only
            if($late_status['status'] === 'late'){
                $late_joiners[] = $staff;
            } else {
                $on_timers[] = $staff;
            }
        }

        return ['on_timers' => $on_timers, 'late_joiners' => $late_joiners];
    }

    
    public function get_staff_on_leave($date) {
        $this->db->select('tbl_staff_leaves.staff_id, tbl_staff_leaves.shift,tblstaff.firstname');
        $this->db->distinct();
        $this->db->from(db_prefix() . '_staff_leaves');
        $this->db->join(db_prefix() . 'staff', 'tblstaff.staffid = tbl_staff_leaves.staff_id');
$this->db->where('tblstaff.staffid !=', 1);
$this->db->where('tblstaff.active', 1); // Exclude staff with staff_id = 1
        $this->db->where('start_date <=', $date);
        $this->db->where('end_date >=', $date);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_most_clocked_in_staff_member_monthly($start_date, $end_date) {
        $this->db->select('staff_id, tblstaff.firstname, SUM(TIMESTAMPDIFF(SECOND, clock_in, clock_out)) as total_time');
        $this->db->from(db_prefix() . '_staff_time_entries');
        $this->db->join(db_prefix() . 'staff', 'tblstaff.staffid = tbl_staff_time_entries.staff_id');
$this->db->where('tblstaff.staffid !=', 1); // Exclude staff with staff_id = 1
$this->db->where('tblstaff.active', 1);
        $this->db->where('DATE(clock_in) >=', $start_date);
        $this->db->where('DATE(clock_out) <=', $end_date);
        $this->db->group_by('staff_id');
        $this->db->order_by('total_time', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }
    

    public function get_day_summary($date) {
        $this->db->where('date', $date);
        $query = $this->db->get(db_prefix() . '_day_summaries');
        return $query->row();
    }

    public function get_day_summary_staff($date, $staff_id) {
        $this->db->where('date', $date);
        $this->db->where('staff_id', $staff_id);
$this->db->where('staff_id !=', 1); // Excluding staff with ID 1
        $query = $this->db->get(db_prefix() . '_staff_summaries');
        return $query->row();
    }

    public function save_day_summary($date, $summary) {
        $day_summary = $this->get_day_summary($date);
        
        if ($day_summary) {
            $this->db->where('date', $date);
            return $this->db->update(db_prefix() . '_day_summaries', ['summary' => $summary, 'updated_at' => date('Y-m-d')]);
        } else {
            return $this->db->insert(db_prefix() . '_day_summaries', ['date' => $date, 'summary' => $summary, 'created_at' => date('Y-m-d')]);
        }
    }

    public function get_monthly_summary($month, $year) {
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        $query = $this->db->get(db_prefix() . '_monthly_summaries');
        return $query->row();
    }

    public function save_monthly_summary($month, $year, $summary) {
        $monthly_summary = $this->get_monthly_summary($month, $year);
        
        if ($monthly_summary) {
            $this->db->where('month', $month);
            $this->db->where('year', $year);
            return $this->db->update(db_prefix() . '_monthly_summaries', ['summary' => $summary, 'updated_at' => date('Y-m-d')]);
        } else {
            return $this->db->insert(db_prefix() . '_monthly_summaries', ['month' => $month, 'year' => $year, 'summary' => $summary, 'created_at' => date('Y-m-d')]);
        }
    }

    public function get_staff_time ($staff_id) {
        $this->db->where('staff_id', $staff_id);
        $this->db->from(db_prefix() . '_staff_time_entries');
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function update_staff_time_entry($entry_id, $in_time, $out_time) {
        $data = [
            'clock_in' => $in_time,
            'clock_out' => $out_time
        ];

        $this->db->where('id', $entry_id);
        return $this->db->update('tbl_staff_time_entries', $data);
    }

    public function delete_staff_time_entry($entry_id) {
        $this->db->where('id', $entry_id);
        return $this->db->delete('tbl_staff_time_entries');
    }

    public function get_projects() {
        $this->db->select('tblprojects.*, COUNT(tbltasks.id) AS total_tasks, SUM(CASE WHEN tbltasks.status = 5 THEN 1 ELSE 0 END) AS completed_tasks');
        $this->db->from(db_prefix() . 'projects');
        $this->db->join(db_prefix() . 'tasks', 'tblprojects.id = tbltasks.rel_id AND tbltasks.rel_type = "project"', 'left');
        $this->db->where("tblprojects.status", "2");
        $this->db->group_by('tblprojects.id');
        $this->db->order_by("tblprojects.id", "DESC");
        $query = $this->db->get();
    
        return $query->result_array();
    }
    

    public function get_project_tasks($project_id) {
        $this->db->select('*');
        $this->db->from(db_prefix() . 'tasks');
        $this->db->where('rel_id', $project_id);
        $this->db->where('rel_type', 'project');
        $this->db->order_by('datefinished', 'ASC');
        $this->db->order_by('priority', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_unassigned_staff($task_id) {
        $this->db->select('tblstaff.staffid, tblstaff.firstname, tblstaff.lastname');
        $this->db->from('tblstaff');
        $this->db->join('tbltask_assigned', 'tblstaff.staffid = tbltask_assigned.staffid AND tbltask_assigned.taskid = ' . $this->db->escape($task_id), 'left');
        $this->db->where('tbltask_assigned.taskid IS NULL', null, false);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function assign_staff_to_task($task_id, $staff_id, $assigned_from) {
        $data = [
            'taskid' => $task_id,
            'staffid' => $staff_id,
            'assigned_from' => $assigned_from
        ];
    
        $this->db->insert(db_prefix() . 'task_assigned', $data);
        return $this->db->affected_rows() > 0;
    }

    public function get_projects_with_task_counts() {
        $this->db->select('tblprojects.*, COUNT(tbltasks.id) AS total_tasks, SUM(CASE WHEN tbltasks.status = 5 THEN 1 ELSE 0 END) AS completed_tasks');
        $this->db->from(db_prefix() . 'tblprojects');
        $this->db->join(db_prefix() . 'tbltasks', 'tblprojects.id = tbltasks.rel_id AND tbltasks.rel_type = "project"', 'left');
        $this->db->group_by('tblprojects.id');
        $query = $this->db->get();
    
        return $query->result_array();
    } 

    public function add_dummy_task($project_id, $task_name)
    {
        $data = [
            'project_id' => $project_id,
            'name' => $task_name,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert(db_prefix() . '_dummy_tasks', $data);

        return $this->db->insert_id();
    }

    public function get_dummy_tasks_by_project($project_id)
    {
        $this->db->where('project_id', $project_id);
        $query = $this->db->get(db_prefix() . '_dummy_tasks');
        return $query->result_array();
    }


    public function get_tasks_by_staff_member($staff_id)
    {
        $this->db->select('tbltasks.*, GROUP_CONCAT(tbltask_assigned.staffid) as assignees');
        $this->db->from('tbltasks');
        $this->db->join('tbltask_assigned', 'tbltasks.id = tbltask_assigned.taskid');
        $this->db->where('tbltask_assigned.staffid', $staff_id);
        $this->db->group_by('tbltasks.id');
        $query = $this->db->get();

        return $query->result();
    }


    public function assign_task_to_dummy_task($taskId, $dummyTaskId)
    {
        $this->db->set('task_id', $taskId);
        $this->db->where('id', $dummyTaskId);
        return $this->db->update(db_prefix() . '_dummy_tasks');
    }

    public function fetch_task_details($taskId) {
        $this->db->select('tbltasks.name as task_name, tblstaff.staffid as assigned_user, tbltasks.id as task_id, tbltasks.status as status')
            ->from('tbltasks')
            ->join('tbltask_assigned', 'tbltask_assigned.taskid = tbltasks.id', 'left')
            ->join('tblstaff', 'tblstaff.staffid = tbltask_assigned.staffid', 'left')
            ->where('tbltasks.id', $taskId)
            ->limit(1);
    
        $query = $this->db->get();
        return $query->row_array();
    }    

    public function delete_dummy_task($dummy_task_id)
    {
        $this->db->where('id', $dummy_task_id);
        return $this->db->delete('tbl_dummy_tasks');
    }

    public function get_staff_summary($staff_id, $date) {
        $this->db->where('staff_id', $staff_id);
        $this->db->where('date', $date);
        $query = $this->db->get('tbl_staff_summaries');
      
        return $query->row();
    }
      
    public function save_staff_summary($staff_id, $summary, $date) {


        $this->db->where('staff_id', $staff_id);
        $this->db->where('date', $date);

        $query = $this->db->get('tbl_staff_summaries');
      
        if ($query->num_rows() > 0) {
          // Update the summary
          $data = array('summary' => $summary);
          $this->db->where('staff_id', $staff_id);
          $this->db->where('date', $date);
          $this->db->update('tbl_staff_summaries', $data);
        } else {
          // Insert the summary
          $data = array(
            'staff_id' => $staff_id,
            'summary' => $summary,
            'date' => $date
          );
          $this->db->insert('tbl_staff_summaries', $data);
        }
      }
      

    public function get_staff_summaries($date) {
        // Fetch summaries from the database
        $this->db->select('tbl_staff_summaries.staff_id, tbl_staff_summaries.summary, tblstaff.firstname, tblstaff.lastname');
        $this->db->from('tbl_staff_summaries');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_staff_summaries.staff_id', 'left');
        $this->db->where('tbl_staff_summaries.date', $date);
        $query = $this->db->get();
    
        // Convert the result set to an associative array
        $summaries = array();
        foreach ($query->result() as $row) {
            $summaries[$row->staff_id] = array(
                'staffid' => $row->staff_id,
                'summary' => $row->summary,
                'staff_name' => $row->firstname . ' ' . $row->lastname
            );
        }
    
        return $summaries;
    }

    public function get_all_staff_google_chat() {
        $this->db->select('tbl_staff_google_chat.staff_id, tbl_staff_google_chat.google_chat_user_id, tblstaff.firstname, tblstaff.lastname, tblstaff.staffid');
        $this->db->from('tblstaff');
        $this->db->join('tbl_staff_google_chat', 'tblstaff.staffid = tbl_staff_google_chat.staff_id', 'left');
        $this->db->where('tblstaff.active', 1);
        $staff = $this->db->get()->result_array();
        return $staff;
    }
    
    public function update_or_insert_google_chat_id($staff_id, $google_chat_user_id) {
        $data = array(
           'staff_id' => $staff_id,
           'google_chat_user_id' => $google_chat_user_id
        );
    
        $this->db->where('staff_id', $staff_id);
        $query = $this->db->get('tbl_staff_google_chat');
    
        if ($query->num_rows() > 0) {
            // A record does exist, so update it.
            $this->db->where('staff_id', $staff_id);
            $this->db->update('tbl_staff_google_chat', $data);
        } else {
            // No record exists, so insert a new one.
            $this->db->insert('tbl_staff_google_chat', $data);
        }
    }

    public function get_today_shift_timings() {
        $this->db->where('day', date('j')); // today's day
        $this->db->where('month', date('n')); // current month
        $shifts = $this->db->get('tbl_staff_shifts')->result_array();
        return $shifts;
    }
    
    
    
    public function get_staff_task_timers($staffId, $date) {
        $this->db->query("SET time_zone='+05:30'");
        $this->db->select('*');
        $this->db->from('tbltaskstimers');
        $this->db->where('staff_id', $staffId);
        $this->db->where("DATE(FROM_UNIXTIME(start_time)) = DATE('{$date}')");
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return [];
        }
    }
    

    public function get_monthly_tasks($staff_id, $month) {
        // Fetch all tasks
        $this->db->select('tbltasks.id as task_id, tbltasks.name as Title, tbltasks.dateadded as Assigned_Date, tbltasks.startdate, tbltasks.duedate, tbltasks.datefinished as Completed_Date, tblprojects.name as Project_Name, tblprojects.id as Project_Id, GROUP_CONCAT(tbltask_assigned.staffid) as assignees');
        $this->db->from('tbltasks');
        $this->db->join('tblprojects', 'tbltasks.rel_id = tblprojects.id AND tbltasks.rel_type = \'project\'', 'left');
        $this->db->join('tbltask_assigned', 'tbltasks.id = tbltask_assigned.taskid');
        $this->db->where('tbltask_assigned.staffid', $staff_id);
        $this->db->where('MONTH(tbltasks.dateadded)', $month);
        $this->db->where('YEAR(tbltasks.dateadded)', date('Y'));
        $this->db->group_by('tbltasks.id');
        $query = $this->db->get();
        $tasks = $query->result_array();
    
        // Calculate total time for each task
        foreach ($tasks as $index => $task) {

            $this->db->select('tbltaskstimers.start_time, tbltaskstimers.end_time');
            $this->db->from(db_prefix() . 'taskstimers');
            $this->db->join(db_prefix() . 'tasks', 'tbltaskstimers.task_id = tbltasks.id', 'left');

            $this->db->where('tbltaskstimers.staff_id', $staff_id);
            $this->db->where('tbltasks.id', $task['task_id']);
            $query = $this->db->get();
            $task_timers = $query->result_array();

            $total_task_time = 0;

            foreach ($task_timers as &$timer) {
                if(!$timer['end_time']) $timer['end_time'] = time();
                $duration_seconds = $timer['end_time'] - $timer['start_time'];
                $total_task_time += $duration_seconds;
              }

            $hours = floor($total_task_time / 3600);
            $minutes = floor(($total_task_time % 3600) / 60);

            $duration_formatted = $hours . 'h ' . $minutes . 'm';
    
            // Add total time to the task array
            $tasks[$index]['Total_Time_Taken'] = $duration_formatted;
            
            $tasks[$index]['Assigned_Date'] = date("Y-m-d", strtotime($tasks[$index]['Assigned_Date']));
            
            if($tasks[$index]['Completed_Date']){
                
                $tasks[$index]['Completed_Date'] = date("Y-m-d", strtotime($tasks[$index]['Completed_Date']));
                
                if (isset($tasks[$index]['Completed_Date'], $tasks[$index]['duedate']) && ($completedDate = strtotime($tasks[$index]['Completed_Date'])) !== false && ($dueDate = strtotime($tasks[$index]['duedate'])) !== false) {
                    $tasks[$index]['Days_Offset'] = (date("d", $completedDate - $dueDate) - 1) . ' days';
                }
                }else{
                $tasks[$index]['Days_Offset'] = '';
            }

            
        }
    
        return $tasks;
    }

    public function get_monthly_tasks_data($staff_id, $month) {

        $tasks = $this->get_monthly_tasks($staff_id, $month);
        $data['total_no_tasks'] = 0;
        $data['total_completed_tasks'] = 0;
        foreach ($tasks as $index => $task) {
            $data['total_no_tasks'] += 1;
            if($task['Completed_Date'] && $task['duedate']){
                if(strtotime($task['Completed_Date']) <= strtotime($task['duedate']) && $task['Completed_Date'] != null){
                    $data['total_completed_tasks'] += 1;
                }
            }
                
        }

        if($data['total_no_tasks'] != 0){
            $data['tasks_rate'] = ($data['total_completed_tasks'] / $data['total_no_tasks'])*100;
        }else{
            $data['tasks_rate'] = 0;
        }
        

        return $data;
    }

    public function get_last_afk_or_offline_duration($staff_id) {
        $this->db->select('*');
        $this->db->from('tbl_staff_status_entries');
        $this->db->where('staff_id', $staff_id);
        $this->db->order_by('end_time', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
    
        // Check if result exists
        if($query->num_rows() > 0) {
            $row = $query->row();
            $start_time = new DateTime($row->start_time);
            $end_time = new DateTime($row->end_time);
            $interval = $start_time->diff($end_time);
            $hours = $interval->h;
            $minutes = $interval->i;
            $duration = '';

            if ($hours > 0) {
                $duration .= $hours . 'h ';
            }
            if ($minutes > 0) {
                $duration .= $minutes . 'm';
            }
            return trim($duration); // returns duration in 'Xh Ym' format
        } else {
            return null;
        }
    }

    public function get_current_afk_offline_duration($staff_id) {
        $this->db->select('*');
        $this->db->from('tbl_staff_status_entries');
        $this->db->where('staff_id', $staff_id);
        $this->db->order_by('start_time', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
    
        // Check if result exists
        if($query->num_rows() > 0) {
            $row = $query->row();
            if(!$row->end_time){
                return time() - strtotime($row->start_time);
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
    
    public function get_days_data($staffId, $month) {
        $days_data = array();
        
        $currentYear = date('Y');
        $yearMonthString = $currentYear . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);

        // Getting total days
        $start_date = date('Y-m-01', strtotime($yearMonthString));
        $end_date = date('Y-m-t', strtotime($yearMonthString));
        $total_days = intval(date('d', strtotime($end_date)));

        $weekly_leaves = 0;
        $begin = new DateTime($start_date);
        $end = new DateTime($end_date);
        $end = $end->modify( '+1 day' ); 
        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval ,$end);

        foreach($daterange as $date){
            $date = $date->format("Y-m-d");
            $shift_timings = $this->get_shift_timings_of_date($date, $staffId);

            if (empty($shift_timings) || ($shift_timings['first_shift']['start'] == "00:00:00" &&   $shift_timings['second_shift']['start'] == "00:00:00")) {
                $weekly_leaves++;
            }
        }
        $total_days -= $weekly_leaves; // Subtracting weekly leaves
    
        $days_data['total_days'] = $total_days;

        // Getting worked days
        $query = $this->db->query("SELECT COUNT(DISTINCT DATE(clock_in)) as worked_days FROM tbl_staff_time_entries WHERE staff_id = $staffId AND DATE(clock_in) BETWEEN '$start_date' AND '$end_date'");
        $days_data['worked_days'] = $query->result()[0]->worked_days;
    
        // Getting paid leaves
        $query = $this->db->query("SELECT COUNT(*) as paid_leaves FROM tbl_applications WHERE staff_id = $staffId AND application_type = 'Paid Leave' AND status = 'Approved' AND ((start_date BETWEEN '$start_date' AND '$end_date') OR (end_date BETWEEN '$start_date' AND '$end_date'))");
        $days_data['paid_leaves'] = $query->result()[0]->paid_leaves;
    
        // Getting unpaid leaves
        $query = $this->db->query("SELECT COUNT(*) as unpaid_leaves FROM tbl_applications WHERE staff_id = $staffId AND application_type = 'Unpaid Leave' AND status = 'Approved' AND ((start_date BETWEEN '$start_date' AND '$end_date') OR (end_date BETWEEN '$start_date' AND '$end_date'))");
        $days_data['unpaid_leaves'] = $query->result()[0]->unpaid_leaves;
    
        // Getting ghosted days
        $days_data['ghosted'] = $total_days - $days_data['worked_days'] - $days_data['paid_leaves'] - $days_data['unpaid_leaves'];

        return $days_data;
    }
    

    // Save a pair to the history
    public function save_pair_history($staff_tag_1, $staff_tag_2) {
        $this->db->insert(db_prefix() . '_staff_pair_history', [
            'staff_tag_1' => $staff_tag_1,
            'staff_tag_2' => $staff_tag_2,
            'paired_at' => date('Y-m-d H:i:s')
        ]);
    }

    // Get all pairs from the history
    public function get_pair_history() {
        return $this->db->get(db_prefix() . '_staff_pair_history')->result_array();
    }

    // Clear the pair history
    public function clear_pair_history() {
        $this->db->empty_table(db_prefix() . '_staff_pair_history');
    }

    // Get all pairs from the history
    public function get_staff_chat_ids() {

        $this->db->select('*');
        $this->db->from('tbl_staff_google_chat');
        $this->db->join('tblstaff', 'tbl_staff_google_chat.staff_id = tblstaff.staffid', 'inner');
        
        return $this->db->get()->result_array();
    }

    public function get_clients() {

        $this->db->select('*');
        $this->db->from('tblclients');
        
        return $this->db->get()->result_array();
    }

    public function get_clients_contacts($userid) {

        $this->db->select('*');
        $this->db->from('tblcontacts');
        $this->db->where("userid", $userid);
        
        return $this->db->get()->result_array();
    }
    

    public function check_staff_late($staff_id, $date){


        // First, fetch the shifts for the given staff and date
        $query = "SELECT * FROM tbl_staff_shifts WHERE staff_id = ? AND staff_id != 1 AND Year = YEAR(?) AND month = MONTH(?) AND day = DAY(?) ORDER BY shift_number ASC";
        $shifts = $this->db->query($query, [$staff_id, $date, $date, $date])->result();

        
        // Fetch clock_in times for the given staff and date
        $query = "SELECT * FROM tbl_staff_time_entries WHERE staff_id = ?  AND staff_id != 1 AND DATE(clock_in) = ? ORDER BY clock_in ASC";
        $entries = $this->db->query($query, [$staff_id, $date])->result();
        
        $late_shifts = [];
        $overall_late = false;

        if(count($entries) <= 0){
            return ['status' => 'absent'];
        }
    
        foreach ($shifts as $index => $shift) {
            $shift_start = new DateTime($date . ' ' . $shift->shift_start_time);
            $shift_end = new DateTime($date . ' ' . $shift->shift_end_time);
            
            $shift_start_def = clone $shift_start;
            
            $grace_period = new DateInterval('PT30M');
            $shift_start->add($grace_period);

           
    
            if (isset($entries[$index])) {
                $clock_in = new DateTime($entries[$index]->clock_in);
                $clock_out = isset($entries[$index]->clock_out) ? new DateTime($entries[$index]->clock_out) : null;
                $is_direct = true;
            } else {
                // In case of consecutive shifts without clock_out, use the last clock_in and clock_out
               $clock_in = new DateTime(end($entries)->clock_in);
               $clock_out = isset(end($entries)->clock_out) ? new DateTime(end($entries)->clock_out) :new DateTime();
               $is_direct = false;
    
            }
            
            $late = $clock_in > $shift_start;

            if($is_direct){
                $difference = $clock_in->getTimestamp() - $shift_start_def->getTimestamp();
            }else{
                $difference = 'Consecutive Shift';
            }
            
    
            // Check the special case where a staff member was already clocked in during the start of the next consecutive shift

            if (!$is_direct && $clock_out && ($clock_out < $shift_start)) {
                // $late = $late && !($clock_in <= $shift_start && $clock_out >= $shift_start);
                $late_shifts[] = ['status' => 'absent', 'shift' => $shift->shift_number];
                
            }else{

                if ($late) {
                    $overall_late = true;

                    $late_shifts[] = ['status' => 'late', 'shift' => $shift->shift_number, 'difference' => $difference];
                } else {
                    $late_shifts[] = ['status' => 'present', 'shift' => $shift->shift_number, 'difference' => $difference];
                }
                
            }
            
        }

        $status = $overall_late ? 'late' : 'present';
        return ['status' => $status, 'shifts' => $late_shifts];
    }
    

    public function get_monthly_afks($staffId, $month, $year = '2023') {
        // The table name
        $table_name = 'tbl_staff_status_entries';
        
        // Format the month to two digits
        $formatted_month = str_pad($month, 2, '0', STR_PAD_LEFT);

        // Start and end dates for the given month
        $start_date = "{$year}-{$formatted_month}-01 00:00:00";
        $end_date = date('Y-m-t 23:59:59', strtotime("{$year}-{$formatted_month}-01"));

        $this->db->select('*');
        $this->db->from($table_name);
        $this->db->where('staff_id', $staffId);
$this->db->where('staff_id !=', 1);  // This line excludes staff with ID 1

        $this->db->where('status', 'AFK');  // For AFK statuses. If you want Offline as well, you can modify this.
        $this->db->where("start_time >=", $start_date);
        $this->db->where("end_time <=", $end_date);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return [];
        }
    }
    

    public function flash_stats($date) {
        // Initialize counters and staff name arrays for each status category
        $counters = [
            'leave' => 0,
            'absent' => 0,
            'present' => 0,
            'late' => 0
        ];
    
        $staffNames = [
            'leave' => [],
            'absent' => [],
            'present' => [],
            'late' => []
        ];
        
        // Step 1: Select all active staff members with their names
        $this->db->select('staffid, firstname');
        $this->db->from('tblstaff');
$this->db->where('staffid !=', 1);

        $this->db->where('active', 1);
        $query = $this->db->get();
        $active_staff = $query->result_array();
        
        // Step 2: Loop through active staff members to check their statuses
        foreach ($active_staff as $staff) {
            $staff_id = $staff['staffid'];
            $staff_name = $staff['firstname'];
    
            // Check if staff is on leave
            if ($this->is_on_leave($staff_id, $date)) {
                $counters['leave']++;
                $staffNames['leave'][] = ['id' => $staff_id, 'name' => $staff_name];
                continue;
            }
        
            // Use the check_staff_late function to get the status
            $result = $this->check_staff_late($staff_id, $date);
            if (isset($result['status'])) {
                $status = $result['status'];
                $counters[$status]++;
                // $staffNames[$status][] = $staff_name;
                $staffNames[$status][] = ['id' => $staff_id, 'name' => $staff_name];

            }
        }
        // return $counters;
        return ['counters' => $counters, 'staffNames' => $staffNames];
    }

    
    public function get_summary_ratio($date) {
        // Get total number of active staff
        $this->db->select('staffid as staff_id, firstname'); // Added staff_id
        $this->db->from('tblstaff');
$this->db->where('staffid !=', 1);  // This line excludes staff with ID=1
        $this->db->where('active', 1);
        $query_all_staff = $this->db->get();
        
        $all_staff_names = $query_all_staff->result_array();
        $total_staff = count($all_staff_names);
    
        // Get the number of staff who have added summaries for the specific date
        $this->db->distinct();
        $this->db->select('tbl_staff_summaries.staff_id, tblstaff.firstname');
        $this->db->from('tbl_staff_summaries');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_staff_summaries.staff_id', 'inner');
        $this->db->where('date', $date);
        $query_submitted = $this->db->get();
        
        $staff_with_summaries = $query_submitted->num_rows();
        $staff_names_and_ids = $query_submitted->result_array();
    
        if ($total_staff == 0) {
            return 0; // Prevent division by zero
        }
        
        // Fetch images here and include them in the return value if necessary
        
        return [
            'staff_with_summaries' => $staff_with_summaries, 
            'total_staff' => $total_staff,
            'staff_names_and_ids' => $staff_names_and_ids,
            'all_staff_names' => $all_staff_names,
            // Include images here if necessary
        ];
    }

    public function get_summary_ratio_and_names($date) {
        // Get total number of active staff
        $this->db->select('staffid as staff_id, firstname'); // Added staff_id
        $this->db->from('tblstaff');
        $this->db->where('active', 1);
        $query_all_staff = $this->db->get();
        
        $all_staff_names = $query_all_staff->result_array();
        $total_staff = count($all_staff_names);
    
        // Get the number of staff who have added summaries for the specific date
        $this->db->distinct();
        $this->db->select('tbl_staff_summaries.staff_id, tblstaff.firstname');
        $this->db->from('tbl_staff_summaries');
        $this->db->join('tblstaff', 'tblstaff.staffid = tbl_staff_summaries.staff_id', 'inner');
        $this->db->where('date', $date);
        $query_submitted = $this->db->get();
        
        $staff_with_summaries = $query_submitted->num_rows();
        $staff_names_and_ids = $query_submitted->result_array();
    
        if ($total_staff == 0) {
            return 0; // Prevent division by zero
        }
        
        // Fetch images here and include them in the return value if necessary
        
        return [
            'staff_with_summaries' => $staff_with_summaries, 
            'total_staff' => $total_staff,
            'staff_names_and_ids' => $staff_names_and_ids,
            'all_staff_names' => $all_staff_names,
            // Include images here if necessary
        ];
    }


    
        // return ['staff_with_summaries' => $staff_with_summaries, 'total_staff' => $total_staff]; // Round to two decimal places
    

    // public function get_summary_ratio($date) {
    //     // Get total number of active staff
    //     $this->db->where('active', 1);
    //     $total_staff = $this->db->count_all_results('tblstaff');
        
    //     // Get the IDs of staff who have added summaries for the specific date
    //     $this->db->distinct();
    //     $this->db->select('staff_id');
    //     $this->db->where('date', $date);
    //     $this->db->from('tbl_staff_summaries');
    //     $query = $this->db->get();
    //     $staff_ids_with_summaries = $query->result_array();  // This should give an array of staff_ids
      
    //     // Check if any summaries were submitted
    //     if (empty($staff_ids_with_summaries)) {
    //       return ['staff_with_summaries' => 0, 'staff_names_with_summaries' => [], 'total_staff' => $total_staff]; 
    //     }
      
    //     // Fetch staff names based on those IDs
    //     $this->db->where_in('id', array_column($staff_ids_with_summaries, 'staff_id'));
    //     $this->db->from('tblstaff');
    //     $query = $this->db->get();
    //     $staff_names_with_summaries = $query->result_array();  // This should give an array of staff names
      
    //     if ($total_staff == 0) {
    //         return 0; // Prevent division by zero
    //     }
        
    //     return ['staff_with_summaries' => count($staff_ids_with_summaries), 'staff_names_with_summaries' => $staff_names_with_summaries, 'total_staff' => $total_staff]; 
    //   }
      

    public function get_monthly_attendance_stats($month, $year) {
        $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $attendance_stats = [
            'present' => array_fill(1, $num_days, 0),
            'absent' => array_fill(1, $num_days, 0),
            'late' => array_fill(1, $num_days, 0),
            'leave' => array_fill(1, $num_days, 0)
        ];

        $today = date("Y-m-d");
    
        for ($day = 1; $day <= $num_days; $day++) {
            $date = "$year-$month-$day";
            $active_staff = $this->db->select('staffid')->from('tblstaff')->where('active', 1)->where('staffid !=', 1)->get()->result_array();

            if(strtotime($date) > strtotime($today)){
                continue;
            }
            
            foreach ($active_staff as $staff) {
                $staff_id = $staff['staffid'];
                if ($this->is_on_leave($staff_id, $date)) {
                    $attendance_stats['leave'][$day]++;
                } else {
                    $status = $this->check_staff_late($staff_id, $date)['status'];
                    $attendance_stats[$status][$day]++;
                }
            }
        }
        return $attendance_stats;
    }
    
    public function get_task_stats_by_staff_date($staff_id, $date)
    {
        $tasks = $this->get_tasks_by_staff_member($staff_id);

        $total_tasks = 0;
        $completed_tasks = 0;

        foreach ($tasks as $task) {
            $taskDueConsideration = ($task->duedate) ? $task->duedate : $task->startdate;

            if (
                (strtotime($task->startdate) <= strtotime($date) && strtotime($taskDueConsideration) >= strtotime($date))
                ||
                (strtotime($task->startdate) <= strtotime($date) && $task->status != 5 && strtotime($taskDueConsideration) < strtotime($date))
            )
            {
                $total_tasks++;
                if ($task->status == 5) {
                    $completed_tasks++;
                }
            }
        }

        return array('total_tasks' => $total_tasks, 'completed_tasks' => $completed_tasks);
    }

}