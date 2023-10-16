<?php defined('BASEPATH') or exit('No direct script access allowed');

class Team_management_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tasks_model');
    }

    /**
     * @param  integer (optional)
     * @return object
     * Get single goal
     */


    public function get_all_departments(){
        $this->db->select('departmentid,name');
        $this->db->order_by('name');
        $query = $this->db->get('tbldepartments');
        return $query->result();
    }

    public function get_staff_by_department($department_id){

        $this->db->select(''.db_prefix().'staff.*, '.db_prefix().'departments.name as department, '.db_prefix().'_staff_status.*'); // Note the addition here
        $this->db->from(''.db_prefix().'staff');
        $this->db->join(''.db_prefix().'_staff_status', ''.db_prefix().'staff.staffid = '.db_prefix().'_staff_status.staff_id');
        $this->db->join(''.db_prefix().'staff_departments', ''.db_prefix().'staff.staffid = '.db_prefix().'staff_departments.staffid');
        $this->db->join(''.db_prefix().'departments', ''.db_prefix().'staff_departments.departmentid = '.db_prefix().'departments.departmentid');
        $this->db->where('is_not_staff', 0);
        $this->db->where(''.db_prefix().'staff.staffid !=', 1);
        $this->db->where('active', 1);
        $this->db->where(''.db_prefix().'staff_departments.departmentid', $department_id);
        $this->db->order_by('tblstaff.firstname');

        $query = $this->db->get();
        $result = $query->result();
        // Loop through each row in the result
        foreach ($result as $staff) {

            //Today's Timer Counter
            $staff->live_time_today = $this->get_today_live_timer($staff->staff_id);
            

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
        return $query->result();
    }
    
    public function getProjectsByStaffId($staff_id) {
        $query = $this->db->query('SELECT p.* FROM ' . db_prefix() . 'projects p JOIN ' . db_prefix() . 'project_members pm ON p.id = pm.project_id WHERE pm.staff_id = ?', array($staff_id));
        // var_dump(result_array);
        return $query->result_array();
    }

    public function getActiveSprintDetailsByProjectId($project_id) {
        $this->load->model('projects_model');
        $this->load->model('tasks_model');
    
        $sprint = $this->projects_model->is_active_sprint_exists($project_id)['sprint'];
    
        if ($sprint) {
            $sprint->stories = $this->projects_model->get_stories('sprint', $sprint->id);
            foreach ($sprint->stories as &$story) {
                $story = $this->tasks_model->get($story->id);
                $story->epic = $this->projects_model->get_epic($story->epic_id);
            }
        }
        return $sprint;
    }


    public function get_today_live_timer($staff_id)
    {
        $totalTime = 0;

        $totalTime = $this->get_total_logged_time_of_date($staff_id, date('Y-m-d'));
        
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

    public function clock_out($staff_id, $is_force = false)
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
            if (!$this->team_management_model->get_staff_summary($staff_id, $clock_in_date) && !$is_force) {
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

        
        $stats->todays_total_time = $this->get_today_live_timer($staff_id);

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

    public function get_total_afk_time($staff_id, $date)
    {   
        $nowDateTime = new DateTime('now');
        $nowDate = $nowDateTime->format('Y-m-d H:i:s');

        $this->db->select_sum('TIMESTAMPDIFF(SECOND, start_time, IFNULL(end_time, "'.$nowDate.'"))', 'total_time')
        ->where('staff_id', $staff_id)
        ->where('DATE(start_time)', $date)
        ->where_in('status', ['AFK', 'Offline']);
        $result = $this->db->get(''.db_prefix().'_staff_status_entries')->row();

        return $result->total_time;
        //return $this->db->last_query();
    }

    public function test_query($query) {
        $result = $this->db->query($query);
        return $result;
    }


    public function get_total_logged_time_of_date($staff_id, $date){

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



    public function save_staff_shifts($staff_id, $dateObj, $repeat, $shift_1_start, $shift_1_end, $shift_2_start, $shift_2_end) {
        // Depending on $repeat, calculate the array of dates for which the shifts need to be set
        $dates = $this->calculate_dates($dateObj, $repeat);

        // Prepare data for insertion
        $shiftsData = [];
        foreach ($dates as $date) {
            // If records for this date already exist, delete them
            $this->db->delete('tbl_staff_shifts', ['staff_id' => $staff_id, 'Year' => $date->format('Y'), 'month' => $date->format('m'), 'day' => $date->format('d')]);

            // Prepare the new records
            if (!empty($shift_1_start) && !empty($shift_1_end)) {
                $shiftsData[] = [
                    'staff_id' => $staff_id,
                    'Year' => $date->format('Y'),
                    'month' => $date->format('m'),
                    'day' => $date->format('d'),
                    'shift_number' => 1,
                    'shift_start_time' => $shift_1_start,
                    'shift_end_time' => $shift_1_end,
                ];
            }
            if (!empty($shift_2_start) && !empty($shift_2_end)) {
                $shiftsData[] = [
                    'staff_id' => $staff_id,
                    'Year' => $date->format('Y'),
                    'month' => $date->format('m'),
                    'day' => $date->format('d'),
                    'shift_number' => 2,
                    'shift_start_time' => $shift_2_start,
                    'shift_end_time' => $shift_2_end,
                ];
            }
        }

        // Insert the new shift records
        if (!empty($shiftsData)) {
            $this->db->insert_batch('tbl_staff_shifts', $shiftsData);
        }
        return ['status' => 'success', 'message' => 'Shifts saved successfully.'];
    }

    private function calculate_dates($dateObj, $repeat) {
        $dates = [];

        switch ($repeat) {
            case 'allFollowing':
                // Example: All following dates of the month
                $currentMonth = $dateObj->format('m');
                while ($currentMonth === $dateObj->format('m')) {
                    $dates[] = clone $dateObj;
                    $dateObj->modify('+1 day');
                }
                break;
            case 'allSameWeekday':
                // Example: All the same weekdays of the month
                $currentMonth = $dateObj->format('m');
                $weekday = $dateObj->format('l');
                while ($currentMonth === $dateObj->format('m')) {
                    if ($dateObj->format('l') === $weekday) {
                        $dates[] = clone $dateObj;
                    }
                    $dateObj->modify('+1 day');
                }
                break;
            case 'allDaysMonth':
                $start = clone $dateObj;
                $start->modify('first day of this month');
                $end = clone $dateObj;
                $end->modify('last day of this month');

                while ($start <= $end) {
                    $dates[] = clone $start;
                    $start->modify('+1 day');
                }
                break;
            default:
                // No repeat, just the single date
                $dates[] = $dateObj;
                break;
        }

        return $dates;
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

    public function get_all_applications($status, $staff_under) {
        $this->db->select('*');
        $this->db->from('tbl_applications');
        $this->db->where('status', $status);
        
        // Add this line to filter by staff_id
        $this->db->where_in('staff_id', $staff_under);
        
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


    public function get_day_shift_total($staff_id, $date){

        $query = "SELECT * FROM tbl_staff_shifts WHERE staff_id = ? AND Year = YEAR(?) AND month = MONTH(?) AND day = DAY(?) ORDER BY shift_number ASC";
        $shifts = $this->db->query($query, [$staff_id, $date, $date, $date])->result();

        $total_time = 0;

        foreach($shifts as $shift){
            if(isset($shift->shift_start_time) && isset($shift->shift_end_time)){
                $total_time += strtotime($shift->shift_end_time) - strtotime($shift->shift_start_time);
            }
        }

        return $total_time;
    }

    public function get_afk_and_offline_entries($staff_id, $date){
        $this->db->select('*');
        $this->db->from(db_prefix() . '_staff_status_entries');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('DATE(start_time)', $date);
        $query = $this->db->get();
        $afk_and_offline = $query->result_array(); 

        foreach ($afk_and_offline as &$entry) {

            $entry['is_going'] = ($entry['end_time']) ? false : true;

            $entry['start_time'] = date('H:i:s', strtotime($entry['start_time']));
            $entry['end_time'] = date('H:i:s', ($entry['end_time']) ? strtotime($entry['end_time']) : time());

           

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

        return $afk_and_offline;
    }

    public function get_task_timers_entires($staff_id, $date) {
    
   
        $this->db->select('t.id as task_id, t.name as task_name, p.id as project_id, p.name as project_name, tt.start_time, tt.end_time');
        $this->db->from(db_prefix() . 'taskstimers tt');
        $this->db->join(db_prefix() . 'tasks t', 'tt.task_id = t.id', 'left');
        $this->db->join(db_prefix() . 'projects p', 't.rel_id = p.id AND t.rel_type = "project"', 'left');
        $this->db->where('tt.staff_id', $staff_id);
        $this->db->where('DATE(FROM_UNIXTIME(tt.start_time))', $date);
        $query = $this->db->get();
        $task_timers = $query->result_array();

      
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

        return $task_timers;
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


    public function assign_staff_to_task($task_id, $staff_id, $assigned_from) {
        $data = [
            'taskid' => $task_id,
            'staffid' => $staff_id,
            'assigned_from' => $assigned_from
        ];
    
        $this->db->insert(db_prefix() . 'task_assigned', $data);
        return $this->db->affected_rows() > 0;
    }


    public function get_staff_summary($staff_id, $date) {
        $this->db->where('staff_id', $staff_id);
        if(!$date){
            $date=date("Y-m-d");
        }
        $this->db->where('date', $date);
        $query = $this->db->get('tbl_staff_summaries');
      

        return $query->row();
    }

    public function get_staff_summaries_in_range($staff_id, $from, $to){
        $this->db->where('staff_id', $staff_id);
        $this->db->where('date >=', $from);
        $this->db->where('date <=', $to);
        $query = $this->db->get('tbl_staff_summaries');
    
        return $query->result();
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


    public function staff_attendance_data($staff_id, $date){


        // First, fetch the shifts for the given staff and date
        $query = "SELECT * FROM tbl_staff_shifts WHERE staff_id = ? AND Year = YEAR(?) AND month = MONTH(?) AND day = DAY(?) ORDER BY shift_number ASC";
        $shifts = $this->db->query($query, [$staff_id, $date, $date, $date])->result();

        
        // Fetch clock_in times for the given staff and date
        $query = "SELECT * FROM tbl_staff_time_entries WHERE staff_id = ?  AND DATE(clock_in) = ? ORDER BY clock_in ASC";
        $entries = $this->db->query($query, [$staff_id, $date])->result();
        
        $all_shifts = [];
        $overall_late = false;
        $overall_leaves = 0;

        $total_clockable_seconds = 0;
        $total_clocked_seconds = 0;

        foreach ($shifts as $index => $shift) {
            $shift_start = new DateTime($date . ' ' . $shift->shift_start_time);
            $shift_end = new DateTime($date . ' ' . $shift->shift_end_time);

            $shift_start_time_plus_5_seconds = date('H:i:s', strtotime($shift->shift_start_time) + 5);
            $shiftDateTime = date('Y-m-d H:i:s', strtotime($date. ' ' . $shift_start_time_plus_5_seconds));

            $is_on_leave = 0;
            if($this->is_on_leave($staff_id, $shiftDateTime)){
                $is_on_leave = 1;
                $overall_leaves += 1;
            }
            
            $shift_start_def = clone $shift_start;
            
            $grace_period = new DateInterval('PT10M');
            $shift_start->add($grace_period);

           
    
            if (isset($entries[$index])) {
                $clock_in = new DateTime($entries[$index]->clock_in);
                $clock_out = isset($entries[$index]->clock_out) ? new DateTime($entries[$index]->clock_out) : null;
                $clock_status = 'mono';
            } else {

                //Clock Entry was not found for shift
                
                if(count($entries) > 0){

                    // In case of consecutive shifts without clock_out, use the last clock_in and clock_out
                    $clock_in = new DateTime(end($entries)->clock_in);
                    $clock_out = isset(end($entries)->clock_out) ? new DateTime(end($entries)->clock_out) : null;
                    $clock_status = 'last';
                    
                }else{
                    //No clock in found for the day
                    $clock_status = 'null';
                }

                
            }

            $difference = 0;

            if($clock_status == 'mono'){
                $difference = $clock_in->getTimestamp() - $shift_start_def->getTimestamp();
            }

            if($clock_status != 'null'){    

                // Case where Last shift is added but shifts are not consecutive and current shift is not clocked in
                // print_r($shift_start);
                if (($clock_status == 'last') && $clock_out && ($clock_out < $shift_start_def)) {
                    $shift_status = 'absent';

                    $clock_in = '';
                    $clock_out = '';  
                    $total_clocked = 0;
                    $sum_afk_offline_times = 0;
                    $clock_status = 'null';
                }else{

                    //Case where this is a separate shift
                    if($clock_status == 'mono'){

                        //Setting Late Status
                        $late = $clock_in > $shift_start;
                        if ($late) {
                            $overall_late = true;
                            $shift_status = 'late';
                        } else {
                            $shift_status = 'present';
                        }
                        
                        $clock_in_time = $clock_in->getTimestamp();
                        $clock_out_time = ($clock_out) ? $clock_out->getTimestamp() : time();

                        if ($clock_out_time < $clock_in_time) {
                            $clock_out_time += 86400; // Add 24 hours (86400 seconds) to the end_unix.
                        }

                        $total_clocked = $clock_out_time - $clock_in_time;
                        
                        $sum_afk_offline_times = $this->team_management_model->get_sum_afk_and_offline_times($staff_id, date("Y-m-d H:i:s", $clock_in_time), date("Y-m-d H:i:s", $clock_out_time));

                        $total_clocked -= $sum_afk_offline_times;
                        
                        $clock_in = date("h:i A", $clock_in_time);
                        $clock_out = ($clock_out) ? date("h:i A", $clock_out_time) : 'Going';

                    }else{
                        //Case where this shift is carried from last clock_in
                        $shift_status = 'present';

                        $clock_in_time = $clock_in->getTimestamp(); // Set shift 2 clockin time to start of this shift because user is already clocked in
                        $clock_out_time = ($clock_out) ? $clock_out->getTimestamp() : time();

                        if ($clock_out_time < $clock_in_time) {
                            $clock_out_time += 86400; // Add 24 hours (86400 seconds) to the end_unix.
                        }

                        $total_clocked = $clock_out_time - $clock_in_time;
                        
                        
                        $sum_afk_offline_times = $this->team_management_model->get_sum_afk_and_offline_times($staff_id, date("Y-m-d H:i:s", $clock_in_time), date("Y-m-d H:i:s", $clock_out_time));
                        
                        $total_clocked -= $sum_afk_offline_times;

                        

                        $clock_in = 'Continued';
                        $clock_out = ($clock_out) ? date("h:i A", $clock_out_time) : 'Going';
                    }

                }

            }else{
                $shift_status = 'absent';
                $total_clocked = 0;
                $sum_afk_offline_times = 0;
                $clock_in = '';
                $clock_out = '';   
            }

            $sum_afk_offline_times = $sum_afk_offline_times ?? 0;
            
            $start_unix = strtotime($shift->shift_start_time);
            $end_unix = strtotime($shift->shift_end_time);
            
            if ($end_unix < $start_unix) {
                $end_unix += 86400; // Add 24 hours (86400 seconds) to the end_unix.
            }
            
            $start_time = date("h:i A", $start_unix);
            $end_time = date("h:i A", $end_unix);

            $clockable_seconds = $end_unix - $start_unix;

            $total_clockable_seconds += $clockable_seconds;

            $is_early_departure = 0;
            if($clock_out != 'Continued'){
                if((strtotime($clock_out) + 300) < strtotime($end_time)){
                    $is_early_departure = 1;
                }
            }
            
            
            $all_shifts[] = ['status' => $shift_status, 'shift_number' => $shift->shift_number,  'clock_status'=> $clock_status, 'shift_start_time'=> $start_time, 'shift_end_time'=>$end_time, 'clock_in' => $clock_in, 'clock_out'=>$clock_out, 'difference' => $difference, 'clockable_seconds' => $clockable_seconds, 'clocked_seconds'=>$total_clocked, 'afk_time' => $sum_afk_offline_times, 'is_on_leave'=>$is_on_leave,'is_early_departure'=>$is_early_departure];
            
        }

        if(count($shifts) > 0){
            if(count($entries) < 1){
                $status = 'absent';
            }else{
                $status = ($overall_leaves == (count($shifts) + 1)) ? 'leave' : ($overall_late ? 'late' : 'present');
            }
        }else{
            $status = 'no-shifts';
        }
        

        
                
        $is_next_continued_shift = false;
        foreach($all_shifts as $index => $shift){

            if($shift['clock_status'] == 'last'){
                $all_shifts[$index - 1]['clock_out'] = 'Continued';
              
                $all_shifts[$index - 1]['clocked_seconds'] = $all_shifts[$index - 1]['clockable_seconds'] - $all_shifts[$index - 1]['difference'];

                $all_shifts[$index - 1]['clocked_seconds'] -= ($all_shifts[$index - 1]['afk_time'] - $all_shifts[$index]['afk_time']);

                
            }

        }
        $total_clocked_seconds =  0;
        foreach($all_shifts as $shift){
            $total_clocked_seconds += $shift['clocked_seconds'];
        }

        $day_status = 1;
        if(($total_clocked_seconds + 600) < $total_clockable_seconds){
            $day_status = 0;
        }else if (($total_clocked_seconds - 1800) > $total_clockable_seconds){
            $day_status = 2;
        }
        
        
        return ['status' => $status, 'clockable_seconds' => $total_clockable_seconds, 'clocked_seconds' => $total_clocked_seconds, 'day_status'=>$day_status, 'shifts' => $all_shifts, ''];
    }

    public function get_total_clockable($staff_id, $date){


        // First, fetch the shifts for the given staff and date
        $query = "SELECT * FROM tbl_staff_shifts WHERE staff_id = ? AND Year = YEAR(?) AND month = MONTH(?) AND day = DAY(?) ORDER BY shift_number ASC";
        $shifts = $this->db->query($query, [$staff_id, $date, $date, $date])->result();

        $total_clockable_seconds = 0;

        foreach ($shifts as $index => $shift) {

            $shift_start_time_plus_5_seconds = date('H:i:s', strtotime($shift->shift_start_time) + 5);

            $shiftDateTime = date('Y-m-d H:i:s', strtotime($date. ' ' . $shift_start_time_plus_5_seconds));

            if(!$this->is_on_leave($staff_id, $shiftDateTime)){
                $start_unix = strtotime($shift->shift_start_time);
                $end_unix = strtotime($shift->shift_end_time);

                if ($end_unix < $start_unix) {
                    $end_unix += 86400; // Add 24 hours (86400 seconds) to the end_unix.
                }
                
                $start_time = date("h:i A", $start_unix);
                $end_time = date("h:i A", $end_unix);
    
                $clockable_seconds = $end_unix - $start_unix;
            }else{
                $clockable_seconds = 0;
            }
            
            $total_clockable_seconds += $clockable_seconds;

        }

        
        return $total_clockable_seconds;
    }

    public function get_total_clocked($staff_id, $date){


        // First, fetch the shifts for the given staff and date
        $query = "SELECT * FROM tbl_staff_time_entries WHERE staff_id = ?  AND DATE(clock_in) = ? ORDER BY clock_in ASC";
        $entries = $this->db->query($query, [$staff_id, $date])->result();
        
        $total_clocked_seconds = 0;

        foreach($entries as $entry){

            $clock_in = new DateTime($entry->clock_in);
            $clock_out = isset($entry->clock_out) ? new DateTime($entry->clock_out) : null;

            $clock_in_time = $clock_in->getTimestamp(); 
            $clock_out_time = ($clock_out) ? $clock_out->getTimestamp() : time();

            if ($clock_out_time < $clock_in_time) {
                $clock_out_time += 86400; // Add 24 hours (86400 seconds) to the end_unix.
            }

            $total_clocked = $clock_out_time - $clock_in_time;
            
            
            $sum_afk_offline_times =$this->team_management_model->get_sum_afk_and_offline_times($staff_id, date("Y-m-d H:i:s", $clock_in_time), date("Y-m-d H:i:s", $clock_out_time));
            
            $total_clocked -= $sum_afk_offline_times;


            $total_clocked_seconds += $total_clocked;
        }

        
        return $total_clocked_seconds;
    }
    

    public function get_monthly_afks($staffId, $month, $year = null) {

        if($year == null){
            $year = date('Y');
        }

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
    


    public function kudosdata($data) {
        return $this->db->insert('tblkudos', [
            'type' => $data['kudosType'],
            'to_' => $data['to'],
            'principles' => $data['principles'],
            'remarks' => $data['remarks'],
            'created_at' => date('Y-m-d H:i:s'),
            'staff_id' => $data['staff_id']  // Insert the staff_id into the database
        ]);
    }

    public function fetch_kudos() {
        $this->db->select('tblkudos.*, staff.firstname, staff.lastname'); // Add the columns you need from the staff table
        $this->db->from('tblkudos');
        $this->db->join('staff', 'tblkudos.staff_id = staff.staffid', 'left'); // Joining the staff table
    
        // Order by 'created_at' column in descending order
        $this->db->order_by('tblkudos.created_at', 'DESC');
    
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_new_kudos($latestTimestamp) {
        $this->db->where('created_at >', $latestTimestamp);
        $query = $this->db->get('tblkudos');
        return $query->result_array();
    }

    public function likeKudos($kudos_id, $staff_id) {
        $this->db->where('id', $kudos_id);
        $this->db->update('tblkudos', ['kudos_like' => $staff_id]);
        
        return $this->db->affected_rows() > 0;
    }

    public function kudos_count($staff_id) {
        $first_day_of_month = date('Y-m-01 00:00:00');
        $last_day_of_month = date('Y-m-t 23:59:59');
    
        $this->db->from('tblkudos');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('created_at >=', $first_day_of_month);
        $this->db->where('created_at <=', $last_day_of_month);
    
        return $this->db->count_all_results();
    }

    public function get_top_kudos_givers() {
        $this->db->select('staff_id, COUNT(staff_id) as total_kudos');
        $this->db->from('tblkudos');
        $this->db->group_by('staff_id');
        $this->db->order_by('total_kudos', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_top_kudos_receivers() {
        $this->db->select('to_, COUNT(to_) as total_received');
        $this->db->from('tblkudos');
        $this->db->group_by('to_');
        $this->db->order_by('total_received', 'DESC');
        return $this->db->get()->result_array();
    }

    public function seenkudos($kudos_id, $staff_id) {
        $this->db->where('id', $kudos_id);
        $this->db->update('tblkudos', ['seen_by' => $staff_id]);
        
        return $this->db->affected_rows() > 0;
    }

    public function addGlobalLeave($data) {
        return $this->db->insert('tbl_global_leaves', $data);  
    }

    public function auto_approve_global_leaves() {
        // Fetch all global leaves which are not yet approved.
        $global_leaves = $this->db->get_where('tbl_global_leaves', ['status' => 'pending'])->result_array();
    
        foreach ($global_leaves as $leave) {
            // Get all staff members.
            $staff_members = $this->db->get('tblstaff')->result_array();
    
            foreach ($staff_members as $staff) {
                // Insert leave for each staff member.
                $leave_data = array(
                    'global_leave_id' => $leave['id'],
                    'staff_id' => $staff['staffid'],
                    'start_date' => $leave['start_date'],
                    'end_date' => $leave['end_date'],
                    'shift' => 'all',  
                    'reason' => $leave['reason'],
                    'created_at' => $leave['created_at'],                   
                    
                );
                $this->db->insert('tbl_staff_leaves', $leave_data);
            }
    
            // Update the global leave status to 'Approved'.
            $this->db->where('id', $leave['id']);
            $this->db->update('tbl_global_leaves', ['status' => 'Approved']);
        }
    }

    public function getGlobalLeaves() {
        return $this->db->get('tbl_global_leaves')->result_array();
    }

    public function deleteGlobalLeave($leaveId) {
        // Delete all records from tbl_staff_leaves where global_leave_id matches the given id
        $this->db->where('global_leave_id', $leaveId);
        $this->db->delete('tbl_staff_leaves');
    
        // Then, delete the singular record from tbl_global_leaves
        $this->db->where('id', $leaveId);
        return $this->db->delete('tbl_global_leaves');  // Returns true on success, false on failure
    }

    
}