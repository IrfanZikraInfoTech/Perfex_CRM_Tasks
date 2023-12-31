<?php defined('BASEPATH') or exit('No direct script access allowed');

use Orhanerday\OpenAi\OpenAi;
class Team_management extends AdminController {

    

    public function __construct() {
        parent::__construct();
        
        $this->load->model('team_management_model');
        $this->load->model('staff_model');
        $this->load->model('tasks_model');
        $this->load->model('projects_model');
        $this->load->library('webhook_library', null, 'webhook_lib');
        $this->load->library('kpi_system');

        //hooks()->add_action('task_assignee_added', 'notify_task_allocation');
    }

    
    public function index() {
        $this->team();
    }
  
    public function team()
    {
        $data['show_ceo_data'] = get_option('show_ceo_data');
        $data['departments'] = $this->team_management_model->get_all_departments();
        $data['hierarchy'] = $this->team_management_model->get_staff_hierarchy();
        // var_dump($data['show_ceo_data']);
        $this->load->view('admin/management/team', $data);
    }

    public function projects($staff_id = null) {

        // if($staff_id && !has_permission('team_management', '', 'admin' )){
        //     $staff_id = get_staff_user_id();
        // }
        $current_user_id = get_staff_user_id();

        $subordinates = get_staff_under($current_user_id);
        if (!has_permission('team_management', '', 'admin') && !in_array($staff_id, $subordinates)) {
            $staff_id = get_staff_user_id();
        }
        $staff_id = $staff_id ?? get_staff_user_id();

        $data['staff_id'] = $staff_id;
        
        $projects = $this->team_management_model->getProjectsByStaffId($staff_id);

        foreach ($projects as &$project) {
            $project['active_sprint'] = $this->team_management_model->getActiveSprintDetailsByProjectId($project['id']);
        }

        $data['staff_projects'] = $projects;
        $this->load->view('admin/management/project', $data);
    }
    

    public function individual_dashboard($from = null, $to = null, $staff_id = null){

        // if($staff_id && !has_permission('team_management', '', 'admin')){
        //     $staff_id = get_staff_user_id();
        // }
        $current_staff_id = get_staff_user_id();

        // If the logged-in user does not have admin permission for team_management
        if (!has_permission('team_management', '', 'admin')) {
            $subordinates = get_staff_under($current_staff_id);
        
            if (!is_array($subordinates)) {
                throw new Exception('Expected subordinates to be an array');
            }
        
            // Now, since $subordinates is just an array of staff IDs, we don't need to map it anymore
            $subordinate_ids = $subordinates;
        
            // If staff_id is provided and it's not in the subordinates list of the current user
            // OR if staff_id is not provided, then default to the current user.
            if (($staff_id && !in_array($staff_id, $subordinate_ids)) || !$staff_id) {
                $staff_id = $current_staff_id;
            }
        }
        // var_dump($subordinates);

        $from = $from ?? date("Y-m-d");
        $to = $to ?? date("Y-m-d");


        $staff_id = $staff_id ?? get_staff_user_id();
        $staff = $this->staff_model->get($staff_id);

        $data['staff'] = $staff;

        $data['from'] = $from;
        $data['to'] = $to;

        $data['punctuality_rate'] = $this->kpi_system->kpi_punctuality_rate($staff_id, $from, $to);
        $data['task_rates'] = $this->kpi_system->kpi_task_rates($staff_id, $from, $to);

        $data['summary_adherence_rate'] = $this->kpi_system->kpi_summary_adherence_rate($staff_id, $from, $to);
        
        $data['afk_adherence_rate'] = $this->kpi_system->kpi_afk_adherence_rate($staff_id, $from, $to);
        $data['shift_productivity_rate'] = $this->kpi_system->kpi_shift_productivity_rate($staff_id, $from, $to);
        
        if($from == $to){

            $data['timer_activities'] = $this->team_management_model->get_task_timers_entires($staff_id, $from);

            $data['afk_offline_entries'] = $this->team_management_model->get_afk_and_offline_entries($staff_id, $from);

            $data['shift_timings'] = $this->team_management_model->get_shift_timings_of_date($from, $staff_id);

            $data['clock_in_entries'] = $this->team_management_model->get_staff_time_entries($staff_id, $from);
        }

        $data['stories'] = get_tasks_in_date_range($staff_id, $from, $to);

        foreach($data['stories'] as &$story){
            $story->total_time_spent = $this->tasks_model->calc_task_total_time($story->id);
            
            if($story->rel_type=='project'){
                $story->project_name = id_to_name($story->rel_id, 'tblprojects', 'id', 'name');
            }

            $dueDate = ($story->duedate) ? new DateTime($story->duedate) : new DateTime($story->startdate);
            $completedDate = $story->datefinished ? new DateTime($story->datefinished) : new DateTime();
            $interval = $dueDate->diff($completedDate);
            $story->late = ($completedDate > $dueDate) ? $interval->days . " days" : "On Time";
            
        }
        
        $data['ops'] = $this->kpi_system->calculate_ops(
            $data['task_rates']['completion_rate'],
            $data['shift_productivity_rate']['percentage'],
            $data['summary_adherence_rate']['percentage'],
            $data['punctuality_rate']['on_time_percentage'],
            $data['task_rates']['timer_adherence_rate'],
            $data['afk_adherence_rate']['percentage'],
            $data['task_rates']['efficiency_rate']
        );

        $this->load->view('admin/management/individual_dashboard', $data);
    }

    

    public function kpi_board($from = null, $to = null, $exclude_ids = ''){
        $data['departments'] = $this->team_management_model->get_all_departments();


        $from = $from ?? date("Y-m-d");
        $to = $to ?? date("Y-m-d");

        

        $data['from'] = $from;
        $data['to'] = $to;

        $start_date = new DateTime($from);
        $end_date = new DateTime($to);
        $end_date->add(new DateInterval('P1D'));
        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod($start_date, $interval, $end_date);
        
        $data['kpi_data'] = [];
        foreach ($date_range as $date) {
            $formatted_date = $date->format('Y-m-d');
            $data['kpi_data'][] = $formatted_date;
        }

        if($exclude_ids){
            $exclude_ids = explode('e', $exclude_ids);
        }

        $current_staff_id = get_staff_user_id();
        $this->db->where('active', 1);
        // Agar user admin hai:
        if (has_permission('team_management', '', 'admin')) {

            
            if (!empty($exclude_ids) && is_array($exclude_ids)) {
                $this->db->select('staffid, firstname, lastname');
                

                $this->db->where_not_in('staffid', $exclude_ids);
                $this->db->order_by('firstname');
                
                $staffs = $this->db->get('tblstaff')->result();
                
                $data['selected_ids'] = $exclude_ids; 
            } else {
               
                $staffs = $this->db->select('staffid, firstname, lastname')->order_by('firstname')->get('tblstaff')->result();
            }
        
                // Agar user admin nahi hai:
        } else {
            $subordinate_ids = get_staff_under($current_staff_id);
            
            if (!empty($subordinate_ids)) {
                array_push($subordinate_ids, $current_staff_id); // Add current user ID to the list
                
                // If $exclude_ids is set, exclude the selected subordinate
                if(!empty($exclude_ids) && is_array($exclude_ids)){
                    $this->db->where('active', 1);
                    $this->db->where_not_in('staffid', $exclude_ids);
                    $this->db->where_in('staffid', $subordinate_ids);
                } else {
                    $this->db->where_in('staffid', $subordinate_ids);
                }
            } else {
                $this->db->where('staffid', $current_staff_id); // Only the current user's data
            }
            $staffs = $this->db->select('staffid, firstname, lastname')->order_by('firstname')->get('tblstaff')->result();
        }
        
      
        

        $staff_kpi_data = [];

        $ar = 0;
        $pr  = 0;
        $tcr = 0;
        $ter = 0;
        $ttr = 0;
        $sar = 0;
        $adr = 0;
        $spr = 0;
        $total_ops = 0;

        $total_staff_members = 0;



        foreach ($staffs as $staff) {
            $total_staff_members++;
            $staff_id = $staff->staffid;

         

            // Fetch KPIs for the staff member for the given date range
            $punctuality_rate = $this->kpi_system->kpi_punctuality_rate($staff_id, $from, $to);
            $task_rates = $this->kpi_system->kpi_task_rates($staff_id, $from, $to);
            $summary_adherence_rate = $this->kpi_system->kpi_summary_adherence_rate($staff_id, $from, $to);
            $afk_adherence_rate = $this->kpi_system->kpi_afk_adherence_rate($staff_id, $from, $to);
            $shift_productivity_rate = $this->kpi_system->kpi_shift_productivity_rate($staff_id, $from, $to);

            

            //Cumulative KPI
            $ar += $punctuality_rate['present_percentage'];
            $pr += $punctuality_rate['on_time_percentage'];
            $tcr += $task_rates['completion_rate'];
            $ter += $task_rates['efficiency_rate'];
            $ttr += $task_rates['timer_adherence_rate'];
            $sar += $summary_adherence_rate['percentage'];
            $adr += $afk_adherence_rate['percentage'];
            $spr += $shift_productivity_rate['percentage'];

            

            // Calculate OPS for the staff member
            $ops = $this->kpi_system->calculate_ops(
                $task_rates['completion_rate'],
                $shift_productivity_rate['percentage'],
                $summary_adherence_rate['percentage'],
                $punctuality_rate['on_time_percentage'],
                $task_rates['timer_adherence_rate'],
                $afk_adherence_rate['percentage'],
                $task_rates['efficiency_rate']
            );

            $total_ops += $ops;
            

            // Store the OPS in the array
            $staff_kpi_data[$staff_id] = [
                'name' => $staff->firstname . ' ' . $staff->lastname,
                'ops' => $ops,
                'ar' => $punctuality_rate['present_percentage'],
                'pr' => $punctuality_rate['on_time_percentage'],
                'tcr' => $task_rates['completion_rate'],
                'ter' => $task_rates['efficiency_rate'],
                'ttr' => $task_rates['timer_adherence_rate'],
                'sar' => $summary_adherence_rate['percentage'],
                'adr' => $afk_adherence_rate['percentage'],
                'spr' => $shift_productivity_rate['percentage']
            ];

            
        }

        
        // return;
        
        $average_ar = $ar / $total_staff_members;
        $average_pr = $pr / $total_staff_members;
        $average_tcr = $tcr / $total_staff_members;
        $average_ter = $ter / $total_staff_members;
        $average_ttr = $ttr / $total_staff_members;
        $average_sar = $sar / $total_staff_members;
        $average_adr = $adr / $total_staff_members;
        $average_spr = $spr / $total_staff_members;
        $average_total_ops = $total_ops / $total_staff_members;

        // Pass the OPS data to the view
        $data['staff_kpi_data'] = $staff_kpi_data;

        $data['cumulative_kpis'] = [
            'ar' => $average_ar,
            'pr' => $average_pr,
            'tcr' => $average_tcr,
            'ter' => $average_ter,
            'ttr' => $average_ttr,
            'sar' => $average_sar,
            'adr' => $average_adr,
            'spr' => $average_spr,
            'total_ops' => $average_total_ops
        ];        


        $this->load->view('admin/management/kpi_board', $data);
    }

    public function fetch_kpi_for_date() {

        $this->db->where('active', 1);
        $staffs = $this->db->select('staffid, firstname, lastname')->get('tblstaff')->result();

        $date = $this->input->post('date');

        $data = [];
        foreach ($staffs as $staff) {
            $staff_id = $staff->staffid;
            
            $punctuality_rate = $this->kpi_system->kpi_punctuality_rate($staff_id, $date);
            $task_rates = $this->kpi_system->kpi_task_rates($staff_id, $date);
            $summary_adherence_rate = $this->kpi_system->kpi_summary_adherence_rate($staff_id, $date);
            $afk_adherence_rate = $this->kpi_system->kpi_afk_adherence_rate($staff_id, $date);
            $shift_productivity_rate = $this->kpi_system->kpi_shift_productivity_rate($staff_id, $date);
    
            $ops = $this->kpi_system->calculate_ops(
                $task_rates['completion_rate'],
                $shift_productivity_rate['percentage'],
                $summary_adherence_rate['percentage'],
                $punctuality_rate['on_time_percentage'],
                $task_rates['timer_adherence_rate'],
                $afk_adherence_rate['percentage'],
                $task_rates['efficiency_rate']
            );
    
            $data[$staff_id] = [
                'name' => $staff->firstname . ' ' . $staff->lastname,
                'punctuality_rate' => $punctuality_rate,
                'task_rates' => $task_rates,
                'summary_adherence_rate' => $summary_adherence_rate,
                'afk_adherence_rate' => $afk_adherence_rate,
                'shift_productivity_rate' => $shift_productivity_rate,
                'ops' => $ops
            ];
        }
    
        echo json_encode($data);
    }


    public function attendance_board($from = null, $to = null, $exclude_ids = ''){
        $data['departments'] = $this->team_management_model->get_all_departments();

        $from = $from ?? date("Y-m-d");
        $to = $to ?? date("Y-m-d");

        $current_staff_id = get_staff_user_id();
        $this->db->where('active', 1);
        if($exclude_ids){
            $exclude_ids = explode('e', $exclude_ids);
            $data['exclude_ids'] = $exclude_ids;
        }
        if (has_permission('team_management', '', 'admin')) 
        {
            if (!empty($exclude_ids) && is_array($exclude_ids)) {
                $this->db->select('staffid, firstname, lastname');
                
                $this->db->where_not_in('staffid', $exclude_ids);

                $this->db->order_by('firstname');
                $staffs = $this->db->get('tblstaff')->result();
                
                $data['selected_ids'] = $exclude_ids; 
            } else {
                $staffs = $this->db->select('staffid, firstname, lastname')->order_by('firstname')->get('tblstaff')->result();

            }

        } else {
            $subordinate_ids = get_staff_under($current_staff_id);
    
            if (!empty($subordinate_ids)) {
                array_push($subordinate_ids, $current_staff_id); // Add current user ID to the list
                
                // If $exclude_ids is set, exclude the selected subordinate
                if(!empty($exclude_ids) && is_array($exclude_ids)){
                    $this->db->where('active', 1);

                    $this->db->where_not_in('staffid', $exclude_ids);
                    $this->db->where_in('staffid', $subordinate_ids);
                } else {
                    $this->db->where_in('staffid', $subordinate_ids);
                }
            } else {
                $this->db->where('staffid', $current_staff_id); // Only the current user's data
            }
    
            $staffs = $this->db->select('staffid, firstname, lastname')->order_by('firstname')->get('tblstaff')->result();
        }
        
        

        $data['from'] = $from;
        $data['to'] = $to;

        $start_date = new DateTime($from);
        $end_date = new DateTime($to);
        $end_date->add(new DateInterval('P1D'));
        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod($start_date, $interval, $end_date);
        

        $data['dates'] = [];
        $data['totals'] = [
            'allocation_shift_1' => 0,
            'actual_shift_1' => 0,
            'allocation_shift_2' => 0,
            'actual_shift_2' => 0,
            'pr' => 0,
            'ar' => 0,
        ];

        foreach ($date_range as $date) {
            $formatted_date = $date->format('Y-m-d');

            $date_data = $this->fetch_attendance_for_date($formatted_date, $staffs);

            $data['dates'][$formatted_date] = $date_data;

            $dataTotals = $date_data['totals'];

            $data['totals']['allocation_shift_1'] += $dateTotals['clockable_shift_1'];
            $data['totals']['actual_shift_1'] += $dateTotals['clocked_shift_1'];
            $data['totals']['allocation_shift_2'] += $dateTotals['clockable_shift_2'];
            $data['totals']['actual_shift_2'] += $dateTotals['clocked_shift_2'];
            $data['totals']['pr'] += $dateTotals['pr'];
            $data['totals']['ar'] += $dateTotals['ar'];
        }

        $data['totals']['pr'] = ($data['totals']['pr'] / count($data['dates'])) * 100;
        $data['totals']['ar'] = ($data['totals']['ar'] / count($data['dates'])) * 100;

        

        $staff_dates_data = [];

        foreach ($staffs as $staff) {
            $staff_id = $staff->staffid;

            // Fetch KPIs for the staff member for the given date range
            $punctuality_rate = $this->kpi_system->kpi_punctuality_rate($staff_id, $from, $to);
            $attendance_data = $this->kpi_system->attendance_data($staff_id, $from, $to);

            // Store the OPS in the array
            $staff_dates_data[$staff_id] = [
                'name' => $staff->firstname . ' ' . $staff->lastname,
                'ar' => $punctuality_rate['present_percentage'],
                'pr' => $punctuality_rate['on_time_percentage'],
                'ct' => $attendance_data['total_clockable'],
                'cdt' => $attendance_data['total_clocked'],
            ];
        }

        // Pass the OPS data to the view
        $data['staff_dates_data'] = $staff_dates_data;


        $this->load->view('admin/management/attendance_board', $data);
    }

    public function fetch_attendance_for_date($date, $staffs) {


        $data = [];

        $clockableShift1 = 0;
        $clockedShift1 = 0;
        $clockableShift2 = 0;
        $clockedShift2 = 0;

        $totalStaffCount = 0;
        $totalStaffOnTime = 0;
        $totalStaffPresent = 0;

        foreach ($staffs as $staff) {
            $staff_id = $staff->staffid;

            $shifts_data = $this->team_management_model->staff_attendance_data($staff_id, $date);


            $data[$staff_id] = [
                'name' => $staff->firstname . ' ' . $staff->lastname,
                'department_id' => id_to_name($staff->staffid, 'tblstaff_departments', 'staffid', 'departmentid'), // Include department_id here
                'data' => $shifts_data
            ];


            if(isset($shifts_data['shifts'])){

                if(isset($shifts_data['shifts'][0]['clockable_seconds'])){
                    $clockableShift1 += $shifts_data['shifts'][0]['clockable_seconds'];
                }
                if(isset($shifts_data['shifts'][0]['clocked_seconds'])){
                    $clockedShift1 += $shifts_data['shifts'][0]['clocked_seconds'];
                }

                if(isset($shifts_data['shifts'][1]['clockable_seconds'])){
                    $clockableShift2 += $shifts_data['shifts'][1]['clockable_seconds'];
                }
                if(isset($shifts_data['shifts'][1]['clocked_seconds'])){
                    $clockedShift2 += $shifts_data['shifts'][1]['clocked_seconds'];
                }

                if($shifts_data['status'] != "leave"){
                    $totalStaffCount++;

                    if($shifts_data['status'] == 'present'){
                        $totalStaffOnTime ++;
                        $totalStaffPresent ++;
                    }else if($shifts_data['status'] == 'late'){
                        $totalStaffPresent ++;
                    }
                }
            }
        }

        $totalPunctualityScore = ($totalStaffCount != 0) ? ($totalStaffOnTime / $totalStaffCount) * 100 : 0;
        $totalAttendanceScore = ($totalStaffCount != 0) ? ($totalStaffPresent / $totalStaffCount) * 100 : 0;

        $data['totals'] = [
            'clockable_shift_1' => $clockableShift1,
            'clocked_shift_1' => $clockedShift1,
            'clockable_shift_2' => $clockableShift2,
            'clocked_shift_2' => $clockedShift2,
            'pr' => $totalPunctualityScore,
            'ar' => $totalAttendanceScore,
        ];
    
        return $data;
    }

    
    
    public function staff_data($staff_id, $date){
        echo '<pre>';
        print_r($this->team_management_model->staff_attendance_data($staff_id, $date));
        echo '</pre>';
    }

    public function applications()
    {
        $staffId = $this->session->userdata('staff_user_id');


        $data['pen_paid_no'] = $this->team_management_model->get_leaves_count($staffId, 'Paid Leave', 'Pending')[0]['total_leaves'];
        $data['pen_unpaid_no'] = $this->team_management_model->get_leaves_count($staffId, 'Unpaid Leave', 'Pending')[0]['total_leaves'];
        $data['pen_gaz_no'] = $this->team_management_model->get_leaves_count($staffId, 'Gazetted Leave', 'Pending')[0]['total_leaves'];

        $data['app_paid_no'] = $this->team_management_model->get_leaves_count($staffId, 'Paid Leave', 'Approved')[0]['total_leaves'];
        $data['app_unpaid_no'] = $this->team_management_model->get_leaves_count($staffId, 'Unpaid Leave', 'Approved')[0]['total_leaves'];
        $data['app_gaz_no'] = $this->team_management_model->get_leaves_count($staffId, 'Gazetted Leave', 'Approved')[0]['total_leaves'];

        $data['dis_paid_no'] = $this->team_management_model->get_leaves_count($staffId, 'Paid Leave', 'Disapproved')[0]['total_leaves'];
        $data['dis_unpaid_no'] = $this->team_management_model->get_leaves_count($staffId, 'Unpaid Leave', 'Disapproved')[0]['total_leaves'];
        $data['dis_gaz_no'] = $this->team_management_model->get_leaves_count($staffId, 'Gazetted Leave', 'Disapproved')[0]['total_leaves'];




        $this->load->view('admin/management/applications', $data);
    }

    public function all_applications()
    {
        if (has_permission('team_management', '', 'admin')) {
            // access_denied('You do not have permission to access this page.');
        }

        $current_staff_id = get_staff_user_id();

        $staff_under = get_staff_under($current_staff_id);

        foreach($staff_under as &$staff){
            $staff_id = $staff;
            $staff = [];
            $staff['id'] = $staff_id;
            $staff['name'] = id_to_name($staff['id'], 'tblstaff', 'staffid','firstname') . ' ' . id_to_name($staff['id'], 'tblstaff', 'staffid','lastname');
        }

        $data['staff_under'] = $staff_under;

        $this->load->view('admin/management/all_applications', $data);
    }

    public function staff_shifts() {
        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);

        $this->load->view('admin/management/staff_shifts', $data);
    }

    public function add_global_leave() {

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $reason = $this->input->post('reason');
        
        $data = array(
            'start_date' => $start_date,
            'end_date' => $end_date,
            'reason' => $reason,
            'created_at' => date("Y-m-d H:i:s")
        );
    
        // Save the global leave data
        $result = $this->team_management_model->addGlobalLeave($data);
        
        if($result) {
            // Auto approve global leaves
            $this->team_management_model->auto_approve_global_leaves();
    
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
    
    public function fetch_global_leaves() {
        $this->load->model('team_management_model');
        $leaves = $this->team_management_model->getGlobalLeaves();
        echo json_encode($leaves);
    }
    public function delete_global_leave() {
        $leaveId = $this->input->post('id');
        if ($leaveId) {
            $this->load->model('team_management_model');
            $result = $this->team_management_model->deleteGlobalLeave($leaveId);
            echo json_encode(['success' => $result]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function control_room($staff_id) {

        if (!has_permission('team_management', '', 'admin')) {
            //access_denied('You do not have permission to access this page.');
        }

        $data['staff_id'] = $staff_id;
        $data['staff_name'] = id_to_name($staff_id, 'tblstaff', 'staffid', 'firstname');
        //$data['title'] = _l('control_room');
        $this->load->view('admin/management/control_room', $data);
    }



    public function fetch_staff_day_summaries()
    {
        $day = $this->input->post("day");
        $month = $this->input->post("month");
        $year = $this->input->post("year");
        $staff_id = $this->input->post("staff_id");

        $month = sprintf("%02d", $month);
        $day = sprintf("%02d", $day);

        $data = $year . '-' . $month . '-' . $day;

        $summary = $this->team_management_model->get_day_summary_staff($data, $staff_id)->summary;

        if($summary){
            echo json_encode(['success'=>true, 'summary' => $summary]);
        }else{
            echo json_encode(['success'=>false]);
        }

    }

    
    //Methods


    public function save_shifts() {

        $originalDate = $this->input->post('date');
        $dateObject = DateTime::createFromFormat('m/d/Y', $originalDate); 
        if ($dateObject === false) {
            echo json_encode(array('status' => 'error', 'message' => 'Invalid date format'));
            return;
        }
        // Format the date into Y-m-d format
        $date = $dateObject->format('Y-m-d');


        $repeat = $this->input->post('repeat');
        $shift_1_start = $this->input->post('s1s');
        $shift_1_end = $this->input->post('s1e');
        $shift_2_start = $this->input->post('s2s');
        $shift_2_end = $this->input->post('s2e');
        $staff_id = $this->input->post('staff_id');

        // Convert $date to a DateTime object to handle it easily
        $dateObj = new DateTime($date);

        $result = $this->team_management_model->save_staff_shifts($staff_id, $dateObj, $repeat, $shift_1_start, $shift_1_end, $shift_2_start, $shift_2_end);

        // Return the result as JSON
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($result));
    }

    public function copy_shifts() {
        $staff_id = $this->input->post('staff_id');
        $month = $this->input->post('month');
        
        // Validate the inputs
        if (empty($staff_id) || empty($month) || !is_numeric($month) || $month < 1 || $month > 12) {
            echo json_encode(array('status' => 'error', 'message' => 'Invalid input'));
            return;
        }
    
        // Call the model function
        $result = $this->team_management_model->copy_shifts($staff_id, $month);
    
        // Return the result
        echo json_encode($result);
    }
    

    public function get_shift_timings($staff_id, $month) {

        $shifts = $this->team_management_model->get_shift_timings($staff_id, $month);
        
        echo json_encode($shifts);
    }
    
    public function get_shift_status() {
        $staff_id = $this->session->userdata('staff_user_id');

        $shift_info = $this->team_management_model->get_shifts_info($staff_id);

        if ($shift_info) {

            $current_timezone = new DateTimeZone(get_option('default_timezone'));
            $current_time_out = new DateTime('now', $current_timezone);
            $current_time_str = $current_time_out->format('Y-m-d H:i:s');
            $current_time = strtotime($current_time_str);

            $current_month = $current_time_out->format('m');
            $current_day = $current_time_out->format('d');
            $current_year = $current_time_out->format('Y');

            $shift_start_time = new DateTime($current_year . '-' .$current_month . '-' . $current_day . ' ' . $shift_info->shift_start_time);
            $shift_end_time = new DateTime($current_year . '-' .$current_month . '-' . $current_day . ' ' . $shift_info->shift_end_time);

            //$shift_start_time = strtotime($shift_info->shift_start_time);
            //$shift_end_time = strtotime($shift_info->shift_end_time);

            $shift_start_time = $shift_start_time->getTimestamp();
            $shift_end_time = $shift_end_time->getTimestamp();


            $shift_info->shift_start_time = $shift_start_time;
            $shift_info->shift_end_time = $shift_end_time;

            $shift_info->current_time = $current_time;

            if ($current_time >= $shift_start_time && $current_time <= $shift_end_time) {
                $shift_info->status = 0;
                $shift_info->statusText = 'Shift Time Ongoing:';
                $shift_info->time_left = convertSecondsToRoundedTime($shift_end_time - $current_time);
            } else if ($current_time < $shift_start_time) {
                $shift_info->status = 1;
                $shift_info->statusText = 'Upcoming shift in:';
                $shift_info->time_left = convertSecondsToRoundedTime($shift_start_time - $current_time);
            } else {
                $shift_info->status = 2;
                $shift_info->statusText = 'none';
                $shift_info->time_left = 0;
            }
        }

        echo json_encode($shift_info);
    }


    function create_thread($webhookUrl, $threadKey, $message) {
        
        $ch = curl_init();
    
        $data = array(
            'text' => $message,
        );
    
        $threadWebhookUrl = "{$webhookUrl}&threadKey={$threadKey}&messageReplyOption=REPLY_MESSAGE_FALLBACK_TO_NEW_THREAD";
    
        curl_setopt($ch, CURLOPT_URL, $threadWebhookUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    
        $response = curl_exec($ch);
        curl_close($ch);
    
        return $response;
    }

    public function base_threads_cron_access($api_key)
    {
        if($api_key != $this->cronAPI()){
            return;
        }

        $today = date("dmY");
        $workingHoursThreadKey = "workingHours-{$today}";
        $shiftsThreadKey = "shifts-{$today}";
        $afkThreadKey = "afk-{$today}";
        $tasksAllThreadKey = "tasks-allocation-{$today}";
        $tasksActThreadKey = "tasks-activity-{$today}";
        $eosThreadKey = "eos-{$today}";

        $today = date("d/m/Y");

        $hourAlerts = "https://chat.googleapis.com/v1/spaces/AAAAsIq3P_g/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=5OC02nE2oxlTecgPi4jV1TGQLhOnhap4KlpQKTx5rzI";

        $taskAlerts = "https://chat.googleapis.com/v1/spaces/AAAA6jknWu4/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=onQidKXA1QDI0IBDMkqU0d_31zwWFZsFE-QPb-jJa5c";

        $scheduleAlerts = "https://chat.googleapis.com/v1/spaces/AAAAsGG8iYM/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=Mifshsjgb3HLutqyd8ScfXtpPfkDcykf2d_POhGWN3c";




        echo $this->create_thread($hourAlerts, $shiftsThreadKey, "--- 📆 `DATE: {$today}` 🔄 *SHIFTS-LOG THREAD* 🔄 ---");
            
        echo $this->create_thread($hourAlerts, $afkThreadKey, "--- 📆 `DATE: {$today}` 🚶‍♂️ *AFK THREAD* 🚶‍♀️ ---");
            
        echo $this->create_thread($taskAlerts, $tasksAllThreadKey, "--- 📆 `DATE: {$today}` 📝 *TASKS ALLOCATION THREAD* 📋 ---");
            
        echo $this->create_thread($taskAlerts, $tasksActThreadKey, "--- 📆 `DATE: {$today}` 🏃‍♂️ *TASKS ACTIVITY THREAD* 🏃‍♀️ ---");

        echo $this->create_thread($scheduleAlerts, $workingHoursThreadKey, "--- 📆 `DATE: {$today}` 🕰️ *WORK SCHEDULE THREAD* 🏢 ---");
            
        echo $this->create_thread($scheduleAlerts, $eosThreadKey, "--- 📆 `DATE: {$today}` 📚 *EOS SUMMARIES THREAD* 📖 ---"); 
        
    }

    public function log_shift_timings_cron_access($shift_no, $api_key) {

        if($api_key != $this->cronAPI()){
            return;
        }

        $shifts = $this->team_management_model->get_today_shift_timings();

        // Loop through each shift
        foreach ($shifts as $shift) {

            if($shift['shift_number'] == $shift_no ){

                $staff_id = $shift['staff_id'];

                // Check if the staff member is on leave
                if ($this->team_management_model->is_on_leave($staff_id, date('Y-m-d H:i:s'))) {
                    // Log message that the user is on leave

                    $tag = id_to_name($staff_id, 'tblstaff', 'staffid', 'google_chat_id');

                    $message = sprintf("😴 *<users/%s>* is on leave today. 🌴", $tag);

                    $this->webhook_lib->send_chat_webhook($message, 'workingHours');
                } else {
                    // Log shift timings
                    $tag = id_to_name($staff_id, 'tblstaff', 'staffid', 'google_chat_id');

                    $shift_number = $shift['shift_number'];
                    $start_time = $shift['shift_start_time'];
                    $end_time = $shift['shift_end_time'];

                    $message = sprintf("👩‍💻 *<users/%s>*'s 🕒 *Shift %d Timings Today* \n\n 📅 : `%s - %s`", $tag, $shift_number, date('g:i A', strtotime($start_time)), date('g:i A', strtotime($end_time)));
                    
                    // Send message to 'shifts' thread
                    $this->webhook_lib->send_chat_webhook($message, 'workingHours');
                }

            }
        }
    }

    
    public function clock_in()
    {
        $staff_id = ($this->input->post('staff_id') == null) ? $this->session->userdata('staff_user_id') : $this->input->post('staff_id');
        $clock_in_result = $this->team_management_model->clock_in($staff_id);

        if ($clock_in_result) {
            // format the date for readability
            $formatted_date = date('g:i A');

            $tag = id_to_name($staff_id, 'tblstaff', 'staffid', 'google_chat_id');

            $message = sprintf("👋 <users/%s> is `Clocking In` 🕒 *at*: %s", $tag, $formatted_date);
         
            $this->webhook_lib->send_chat_webhook($message, "shifts");
        }

        echo json_encode(['success' => $clock_in_result]);
    }

    public function clock_out()
    {
        $staff_id = ($this->input->post('staff_id') == null) ? $this->session->userdata('staff_user_id') : $this->input->post('staff_id');

        $is_force = ($this->input->post('force') == null) ? false : $this->input->post('force');
    
        
        $clock_out_result = $this->team_management_model->clock_out($staff_id, $is_force);

        if ($clock_out_result['success']) {


            // format the date for readability
            $formatted_date = date('g:i A');

            $tag = id_to_name($staff_id, 'tblstaff', 'staffid', 'google_chat_id');

            $message = sprintf("🏃‍♂️ <users/%s> is `Clocking Out` 🕒 *at*: %s", $tag, $formatted_date);
         
            $this->webhook_lib->send_chat_webhook($message, "shifts");
            
        }

        echo json_encode($clock_out_result);
    }

    public function update_status()
    {
        $staff_id = ($this->input->post('staff_id') == null) ? $this->session->userdata('staff_user_id') : $this->input->post('staff_id');

        $status = $this->input->post('statusValue');
        $current_time = date('Y-m-d H:i:s');
    
        // End the previous status
        $this->team_management_model->update_status($staff_id, $status);

        $this->team_management_model->end_previous_status($staff_id, $current_time);
    
        if ($status === 'Online') {
            // Do not insert a new status entry for the 'online' status
        } else {
            // Insert a new status entry for 'afk' or 'offline'
            $this->team_management_model->insert_status_entry($staff_id, $status, $current_time);
        }


        // format the date for readability
        $formatted_date = date('g:i A');

        $tag = id_to_name($staff_id, 'tblstaff', 'staffid', 'google_chat_id');

        // choose the right phrase depending on the status
        if ($status === 'AFK') {
            $message = sprintf("🚀 <users/%s> went `AFK` 🕒 *at*: %s", $tag, $formatted_date);
        } elseif ($status === 'Online') {

            $duration = $this->team_management_model->get_last_afk_or_offline_duration($staff_id);

            $message = sprintf("*🎉 Yay!* <users/%s> is back `Online` 🕒 *at*: %s after *%s* ⏱️", $tag, $formatted_date, $duration);
        }

        if($status != "Leave" && $status != "Offline"){
            $this->webhook_lib->send_chat_webhook($message, "afk");
        }
        
        echo json_encode(['success' => true]);
    }

    
    public function fetch_stats()
    {
        $staff_id = ($this->input->post('staff_id') == null) ? $this->session->userdata('staff_user_id') : $this->input->post('staff_id');
        $stats = $this->team_management_model->get_stats($staff_id);

        echo json_encode($stats);
    }

    public function get_attendance_status()
    {
        $staff_id = ($this->input->get('staff_id') == null) ? $this->session->userdata('staff_user_id') : $this->input->get('staff_id');

        $status = $this->team_management_model->staff_attendance_data($staff_id, date("Y-m-d"))['status'];

        echo json_encode(['status' => $status]);
    }


    public function fetch_staff_time_entries($staff_id) {
        if (!has_permission('team_management', '', 'admin')) {
            //access_denied('Access Denied!');
        }
    
        $staff_time_entries = $this->team_management_model->get_staff_time($staff_id);
        echo json_encode($staff_time_entries);
    }

    public function edit_staff_time_entry() {
        $entry_id = $this->input->post('entry_id');
        $in_time = $this->input->post('in_time');
        
        $out_time = $this->input->post('out_time');

        if($out_time == "NaN-NaN-NaN NaN:NaN:NaN"){
            $out_time = null;
        }

        $result = $this->team_management_model->update_staff_time_entry($entry_id, $in_time, $out_time);
        
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function delete_staff_time_entry() {
        $entry_id = $this->input->post('entry_id');

        $result = $this->team_management_model->delete_staff_time_entry($entry_id);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function process_staff_leaves_cron_access($api_key) {

        if($api_key != $this->cronAPI()){
            return;
        }
        $this->load->model('staff_model');

        $staff_members = $this->staff_model->get();
        
    
        foreach ($staff_members as $staff) {

            if ($this->team_management_model->is_on_leave($staff['staffid'], date('Y-m-d H:i:s'))) {
                $this->team_management_model->clock_out_and_set_leave_status($staff['staffid']); 
            }else{
                if($this->team_management_model->get_stats($staff['staffid'])->status == "Leave"){
                    $this->team_management_model->update_status($staff['staffid'], "Offline");
                }
            }
        }
    }

    public function get_file_type()
    {
        $filename = $this->input->get('filename');
        $directory = './uploads/applications/';

        $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
        $pdf_extension = 'pdf';
        $found_file = false;
        $file_type = '';
        $ext = '';

        $files = scandir($directory);
        $files = array_diff($files, array('.', '..'));

        foreach ($image_extensions as $extension) {
            
            if (in_array($filename . '.' . $extension, $files)) {
                $file_type = "image";
                $ext = $extension;
                $found_file = true;
                break;
            }
        }

        if (!$found_file && file_exists($directory . $filename . '.' . $pdf_extension)) {
            $file_type = "pdf";
            $ext = $pdf_extension;
        } elseif (!$found_file) {
            $file_type = "not_found";
        
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['file_type' => $file_type, 'ext' => $ext]));
    }

    public function staff_summary() {
        $staff_id = $this->session->userdata('staff_user_id');
        $date = $this->input->post('date');
        $summary = $this->input->post('summary');
    
        $selectedDate = new DateTime($date);
        $today = new DateTime();
        $diff = $today->diff($selectedDate)->days;
    
        if ($summary) {
            // If it's not yesterday or day before yesterday
            if ($diff > 2) {
                echo "You cannot edit this summary";
                return;
            }
    
            // Save the summary
            $this->team_management_model->save_staff_summary($staff_id, $summary, $date);
    
            $formatted_date = date('g:i A');
            $tag = id_to_name($staff_id, 'tblstaff', 'staffid', 'google_chat_id');
            $message = sprintf("📝✨ <users/%s> just shared their daily summary 🕑 *at*: %s\n\n📋*Summary*:\n%s", $tag, $formatted_date, $summary);
            $this->webhook_lib->send_chat_webhook($message, "eos");
        
        } else {
            // Get the summary
            $summary_record = $this->team_management_model->get_staff_summary($staff_id, $date);

            if ($summary_record) {
                echo $summary_record->summary;
            } else {
                echo '';
            }
        }
    }

    public function reminderAPI_cron_access($api_key, $staffChatId) {
        if($api_key != $this->cronAPI()){
            return;
        }

        $staffId = id_to_name($staffChatId, 'tblstaff', 'google_chat_id', 'staffid');
    
        $today = date('Y-m-d');

        $tasks = $this->tasks_model->get_user_tasks_assigned($staffId);
        $total_tasks = 0;
    
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
            }
        }
    
        $output = [];

        $all_tasks_timers = $this->team_management_model->get_staff_task_timers($staffId, $today);
        $last_task = end($all_tasks_timers);

        //print_r($all_tasks_timers);
    
        //echo ($last_task['end_time'] != null || count($all_tasks_timers) == 0);

        $output['timers_reminder'] = (($last_task['end_time'] != null || count($all_tasks_timers) == 0) && $total_tasks > 0) ? true : false;
        $output['is_working'] = ($this->team_management_model->get_status($staffId) == "Online") ? true : false;
        $output['summarys_reminder'] = ($this->team_management_model->get_staff_summary($staffId, $today)) ? false : true;
        $output['afk_reminder'] = ($this->team_management_model->get_current_afk_offline_duration($staffId) > 1800) ? true : false;

    
        header('Content-Type: application/json');
    
        echo json_encode($output);
    }

    public function json(){
        echo json_encode(array(
            "text" => " 🙄"
        ));
    }

    public function submit_application() {

        $this->load->library('email');
        $this->load->model('staff_model');

        // Get the submitted form data.
        $application_type = $this->input->post('application_type');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $reason = $this->input->post('reason');
        $shift = $this->input->post('shift');
        $staff_id = $this->session->userdata('staff_user_id');
      
        // Save the application to the database using your model.
        $application_data = array(
            'staff_id' => $staff_id,
            'application_type' => $application_type,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'reason' => $reason,
            'shift' => $shift,
            'status' => 'pending', // Set the initial status to 'pending'
            'created_at' => date('Y-m-d H:i:s'), // Set the created_at column to the current timestamp
        );

        $f_name = id_to_name($staff_id, 'tblstaff', 'staffid', 'firstname');

        $template = "
            <h2>New Application!!</h2>
            <div>Staff Name: ".$f_name."</div><br>
            <div>Date: ".date('Y-m-d')."</div><br>
            <div>Reason: ".$reason."</div><br>
            <a href='https://crm.zikrainfotech.com/admin/team_management/all_applications'> Applications Page! </a>
        ";

        $this->email->initialize();
        $this->email->set_newline(PHP_EOL);
        $this->email->from(get_option('smtp_email'), get_option('companyname'));

        $staff_above = get_staff_above($staff_id);

        foreach($staff_above as &$staff){
            $staff = id_to_name($staff, 'tblstaff', 'staffid', 'email');
        }

        $this->email->to($staff_above);

        $this->email->subject("New Application!");
        $this->email->message(get_option('email_header') . $template . get_option('email_footer'));

        $email_sent = $this->email->send();

        $insert_result = $this->team_management_model->save_application($application_data);

        if (isset($_FILES['attachment']) && $_FILES['attachment']['size'] > 0) {
            // Upload the file to the server.
            $config['upload_path'] = './uploads/applications/'; // Set the upload path.
            $config['allowed_types'] = 'gif|jpg|png|pdf|docx|webp'; // Set the allowed file types.
            $config['file_name'] = 'application_' . $insert_result; // Set the file name to the application id.
            $config['overwrite'] = true; // Overwrite the file if it exists.
            
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);
    
            if (!$this->upload->do_upload('attachment')) {    
                echo json_encode(['success' => false, 'error' => $this->upload->display_errors()]);
                return;
            }else {
                // Get the uploaded data
                $uploaded_data = $this->upload->data();
            
                // Set the permissions for the uploaded file
                chmod($uploaded_data['full_path'], 0644);
            }
        }

      
        // Redirect the user to a success page or back to the form with a success message.
        echo json_encode(['success' => $insert_result]);
    }

    public function application_request_admin(){
        $this->load->library('email');
        $this->load->model('staff_model');
        $application_id = $this->input->post('id');

        $application_data = $this->team_management_model->get_application($application_id);

        $status = $application_data["status"];
        $id = $application_data["id"];

        $staff_id = $application_data["staff_id"];
        $staff_name = id_to_name($staff_id, 'tblstaff', 'staffid', 'firstname') .' '. id_to_name($staff_id, 'tblstaff', 'staffid', 'lastname');

        $date = $application_data["created_at"];
        $duration = $application_data["start_date"] . ' - ' . $application_data["end_date"];
        $reason = $application_data["reason"];

    
        if($application_data['application_type'] == "Paid Leave"){

            $total = $this->team_management_model->get_leaves_count($staff_id, 'Paid Leave', 'Approved')[0]['total_leaves'];
            $left = $total - (int)(get_option('paid_leaves'));

        }
        else if($application_data['application_type'] == "Unpaid Leave"){
            $total = $this->team_management_model->get_leaves_count($staff_id, 'Unpaid Leave', 'Approved')[0]['total_leaves'];
            $left = $total - (int)(get_option('unpaid_leaves'));
        }
        else if($application_data['application_type'] == "Gazetted Leave"){
            $total = $this->team_management_model->get_leaves_count($staff_id, 'Gazetted Leave', 'Approved')[0]['total_leaves'];
            $left = $total - (int)(get_option('gaz_leaves'));
        }

        $total ++;

        if($total == 1){
            $total = '1st';
        }else if($total == 2){
            $total = '2nd';
        }else if($total == 3){
            $total = '3rd';
        }else{
            $total .= 'th';
        }

        $template = "
            <h2>Application Approval Request</h2>
            <p>Hey there beautiful Admin, Hiring Manager/Team Lead could not approve application of ".$staff_name." because of quota overdue, This is their <b>".$total."</b> leave and their current quota is exceeded by: <b>".$left."</b></p>
            <div>Application Id: ".$id."</div><br>
            <div>Sent At: ".$date."</div><br>
            <div>Duration: ".$duration."</div><br>
            <div>Reason: ".$reason."</div><br>
            <a href='https://crm.zikrainfotech.com/admin/team_management/all_applications?id=".$id."'> Approval Page! </a>
        ";


        $this->db->select('email');
        $this->db->where('admin', 1);
        $admins = $this->db->get('tblstaff')->result_array();

        foreach($admins as &$admin){
            $admin = $admin['email'];
        }

        $this->email->initialize();
        $this->email->set_newline(PHP_EOL);
        $this->email->from(get_option('smtp_email'), get_option('companyname'));

        $this->email->to($admins);

        $this->email->subject("Application Approval Request!!");
        $this->email->message(get_option('email_header') . $template . get_option('email_footer'));

        $email_sent = $this->email->send();

    }

    public function change_application_status() {
        $application_id = $this->input->post('id');
        $new_status = $this->input->post('status');
    
        if ($this->team_management_model->update_application_status($application_id, $new_status)) {
            
            $application_data = $this->team_management_model->get_application($application_id);

            $status = $application_data["status"];
            $id = $application_data["id"];
            $staff_id = $application_data["staff_id"];
            $date = $application_data["created_at"];
            $duration = $application_data["start_date"] . ' - ' . $application_data["end_date"];
            $reason = $application_data["reason"];

            $template = "
                <h2>Application Status Changed to: ".$status."</h2>
                <div>Application Id: ".$id."</div><br>
                <div>Sent At: ".$date."</div><br>
                <div>Duration: ".$duration."</div><br>
                <div>Reason: ".$reason."</div><br>
                <a href='https://crm.zikrainfotech.com/admin/team_management/applications'> Applications Page! </a>
            ";

            $this->email->initialize();
            $this->email->set_newline(PHP_EOL);
            $this->email->from(get_option('smtp_email'), get_option('companyname'));
            $this->email->to(get_option('smtp_email'));
            $this->email->to(id_to_name($staff_id, 'tblstaff', 'staffid', 'email'));
            $this->email->subject("Application Reviewed!!");
            $this->email->message(get_option('email_header') . $template . get_option('email_footer'));

            $email_sent = $this->email->send();

            
        $response = [
              'success' => true,
              'message' => 'Status changed successfully',
            ];
        } else {
          $response = [
            'success' => false,
            'message' => 'Error changing status',
          ];
        }
    
        echo json_encode($response);
    }

    public function delete_application() {
        $application_id  = $this->input->post('id');
        $result = $this->team_management_model->delete_application($application_id);
    
        // Check if the delete operation was successful and redirect or display an error message accordingly.
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
    

    public function view_leaves() {
        $staff_id = $this->input->post('staff_id');
        $leaves = $this->team_management_model->get_leaves($staff_id);
        echo json_encode(['leaves' => $leaves]);
    }

    // Function for adding leaves
    public function add_leave() {
        $staff_id = $this->input->post('staff_id');
        $reason = $this->input->post('reason');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $shift = $this->input->post('shift');

        $result = $this->team_management_model->insert_leave($staff_id, $reason, $start_date, $end_date, $shift);

        if ($result['success']) {
            echo json_encode(['success' => true, 'id' => $result['id']]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    // Function for deleting leaves
    public function delete_leave() {
        $leave_id = $this->input->post('leave_id');
        $result = $this->team_management_model->remove_leave($leave_id);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
    
    public function get_staff_leave_details($staffId) {
        $data = array();
    
        $data['pen_paid_no'] = $this->team_management_model->get_leaves_count($staffId, 'Paid Leave', 'Pending')[0]['total_leaves'];
        $data['pen_unpaid_no'] = $this->team_management_model->get_leaves_count($staffId, 'Unpaid Leave', 'Pending')[0]['total_leaves'];
        $data['pen_gaz_no'] = $this->team_management_model->get_leaves_count($staffId, 'Gazetted Leave', 'Pending')[0]['total_leaves'];

        $data['app_paid_no'] = $this->team_management_model->get_leaves_count($staffId, 'Paid Leave', 'Approved')[0]['total_leaves'];
        $data['app_unpaid_no'] = $this->team_management_model->get_leaves_count($staffId, 'Unpaid Leave', 'Approved')[0]['total_leaves'];
        $data['app_gaz_no'] = $this->team_management_model->get_leaves_count($staffId, 'Gazetted Leave', 'Approved')[0]['total_leaves'];

        $data['dis_paid_no'] = $this->team_management_model->get_leaves_count($staffId, 'Paid Leave', 'Disapproved')[0]['total_leaves'];
        $data['dis_unpaid_no'] = $this->team_management_model->get_leaves_count($staffId, 'Unpaid Leave', 'Disapproved')[0]['total_leaves'];
        $data['dis_gaz_no'] = $this->team_management_model->get_leaves_count($staffId, 'Gazetted Leave', 'Disapproved')[0]['total_leaves'];
        
        // Return the data as JSON
        echo json_encode($data);
    }
    
    public function fetch_applications() {

        $staff_id = $this->input->get('staff_id');
        $status = $this->input->get('status');

        $current_staff_id = $this->session->userdata('staff_user_id');

        $staff_under = get_staff_under($current_staff_id);

        if($staff_id != 0){
            $applications = $this->team_management_model->get_applications_by_staff_id($staff_id);
        }else{
            $applications = $this->team_management_model->get_all_applications($status, $staff_under);
        }

        foreach ($applications as $application) {
            $application->user_name = id_to_name($application->staff_id, 'tblstaff', 'staffid', 'firstname');;
            $application->user_pfp = staff_profile_image($application->staff_id, ['object-cover', 'md:h-full' , 'md:w-10 inline' , 'staff-profile-image-thumb'], 'thumb');

            $paid = (int)$this->team_management_model->get_leaves_count($application->staff_id, 'Paid Leave', 'Approved')[0]['total_leaves'];
            $unpaid = (int)$this->team_management_model->get_leaves_count($application->staff_id, 'Unpaid Leave', 'Approved')[0]['total_leaves'];
            $gaz = (int)$this->team_management_model->get_leaves_count($application->staff_id, 'Gazetted Leave', 'Approved')[0]['total_leaves'];

            $application->is_editable = false;

            if($application->application_type == "Paid Leave" && (((int)get_option('paid_leaves') - $paid) > 0) || is_admin()){
                $application->is_editable = true;
            }

            if($application->application_type == "Unpaid Leave" && (((int)get_option('unpaid_leaves') - $unpaid) > 0) || is_admin()){
                $application->is_editable = true;
            }


            if($application->application_type == "Gazetted Leave" && (((int)(get_option('gaz_leaves')) - $gaz) > 0) || is_admin()){
                $application->is_editable = true;
            }

            if($application->application_type == "Change Shift Timings"){
                $application->is_editable = true;
            }
            

        }

               
        $response = array(
            'applications' => $applications
        );
        //header('Content-Type: application/json');
        echo json_encode($response);
    }


    

    private function cronAPI()
    {
        return "OUIYUGBSCL";
    }
      

    public function kudos(){

        $staff_id = get_staff_user_id(); 
        


        $kudos_sent_this_month = $this->team_management_model->kudos_count($staff_id);
        $remaining_kudos = 5 - $kudos_sent_this_month;

        $data['staff_members'] = $this->staff_model->get();
        $data['kudos_data'] = $this->team_management_model->fetch_kudos();
        $data['remaining_kudos'] = $remaining_kudos;
        $data['top_givers'] = $this->team_management_model->get_top_kudos_givers();
        $data['top_receivers'] = $this->team_management_model->get_top_kudos_receivers();

        $staff_id_name= [];
        foreach($data['staff_members'] as $staff) {
            $staff_id_name[$staff['staffid']] = $staff['firstname'] . ' ' . $staff['lastname'];
        }
        $data['staff_id_name'] = $staff_id_name;

        $all_kudos = $this->db->get('tblkudos')->result();
        foreach($all_kudos as $kudos){
            $kudos_id = $kudos->id;

            $seen_users = ($kudos->seen_by) ? explode(',', $kudos->seen_by) : [];

            if (!in_array($staff_id, $seen_users)) { // If not marked as seen already
                $seen_users[] = $staff_id;
                $updated_seen_users = implode(',', $seen_users);
                $this->db->where('id', $kudos_id);
                $this->db->update('tblkudos', ['seen_by' => $updated_seen_users]);

            }
        }
        

        $this->load->view('admin/management/kudos', $data);
    }

    private function convert_html_to_google_chat_format($text) {
        // Replace bold tags with * for Google Chat
        $text = preg_replace('/<strong>(.*?)<\/strong>/', '*$1*', $text);
    
        // Replace italics tags with _ for Google Chat
        $text = preg_replace('/<em>(.*?)<\/em>/', '_$1_', $text);
    
         $text = strip_tags($text);

        // Decode any HTML entities
        $text = html_entity_decode($text);
    
        return $text;
    }
    
    

    public function save_kudos_data() {

        $principles = $this->input->post('principles');
        $principles_str = implode(', ', $principles);
    
        $staff_id = $this->session->userdata('staff_user_id');
        $to_id = $this->input->post('to_');

        // Convert staff_id and to_id to their respective tags
        $from_tag = id_to_name($staff_id, 'tblstaff', 'staffid', 'google_chat_id');
        $to_tag = id_to_name($to_id, 'tblstaff', 'staffid', 'google_chat_id');

        // Construct the kudos message
        $kudos_type = $this->input->post('type');
        $remarks = $_POST['remarks'];
        

        $data = [
            'kudosType' => $kudos_type,
            'to' => $to_id,
            'principles' => $principles_str,
            'remarks' => $remarks,
            'staff_id' => $staff_id

        ];
    
        $response = $this->team_management_model->kudosdata($data);

        $remarks = $this->convert_html_to_google_chat_format($remarks);

        $message = sprintf(
            "🌟 *#%s* from *<users/%s>* to *<users/%s>* 🌟\n\n *Following Principles:* %s\n\n*With Remarks:* %s ",
            ucfirst($kudos_type),
            $from_tag,
            $to_tag,
            $principles_str,
            $remarks
        );

        $this->webhook_lib->send_chat_webhook($message, 'kudos');

        echo json_encode([
            'success' => $response,
        ]);
    }

    public function like_kudos() {
        $kudos_id = $this->input->post('kudos_id');
        $staff_id = $this->session->userdata('staff_user_id'); 
    
        $this->db->where('id', $kudos_id);
        $kudos = $this->db->get('tblkudos')->row();
    
        if ($kudos) {
            $likes = explode(',', $kudos->kudos_like);
            if (!in_array($staff_id, $likes)) { // If not liked already
                $likes[] = $staff_id;
                $updated_likes = implode(',', $likes);
                $this->db->where('id', $kudos_id);
                $success = $this->db->update('tblkudos', ['kudos_like' => $updated_likes]);
                $image_url = staff_profile_image($staff_id, ['w-6 h-6 rounded-full'], 'thumb', []); 
                preg_match('/src="([^"]+)"/', $image_url, $match);
                $actual_image_url = $match[1];
                echo json_encode(['success' => $success, 'action' => 'liked', 'image_url' => $actual_image_url]);
            } else if (($key = array_search($staff_id, $likes)) !== false) {
                unset($likes[$key]);
                $updated_likes = implode(',', $likes);
                $this->db->where('id', $kudos_id);
                $success = $this->db->update('tblkudos', ['kudos_like' => $updated_likes]);
                $image_url = staff_profile_image($staff_id, ['w-6 h-6 rounded-full'], 'thumb', []);
                preg_match('/src="([^"]+)"/', $image_url, $match);
                $actual_image_url = $match[1];
                echo json_encode(['success' => $success, 'action' => 'unliked', 'image_url' => $actual_image_url]);
                return;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Kudos not found']);
        }
    }

    
    public function set_shifts($id = '', $month = ''){

        if (!has_permission('team_management', '', 'admin')) {
            $id = get_staff_user_id();
            $month = date('m');
        }
        
        $data['staff'] = $this->staff_model->get($id);

        $data['shifts'] = $this->team_management_model->get_shift_timings($id, $month);

        $data['month'] = $month;

        $data['is_admin'] = has_permission('team_management', '', 'admin');
        $data['is_editable'] = ($data['is_admin']) ? 1 : ((date('d') < 3) ? 1 : 0);

        $days_in_month = date('t', mktime(0, 0, 0, $month, 1, date('Y')));
        $leave_dates = [];
        for($day = 1; $day <= $days_in_month; $day++) {

            $date = date('Y') . '-' . $month . '-' . sprintf('%02d', $day) .' '. date("H:i:s");

            if($this->team_management_model->is_on_leave($id, $date)) {
                $date = explode(' ', $date)[0];
                $leave_dates[] = $date;
            }
        }


        $data['leave_dates'] = $leave_dates;

        $this->load->view('admin/management/set_shifts',$data);
    }

    public function exit_view()
    {
        $data['exit_data'] = $this->team_management_model->get_exit_data();
        $this->load->view('admin/management/exit_form',$data);
        
    }

    public function save_exit() {
        $data = array(
            'staff_id' => $this->input->post('staff_id'),
            'department_id' => $this->input->post('department_id'),
            'seperation_date' => $this->input->post('seperation_date'),
            'reason' => $this->input->post('reason'),
            'working_again' => $this->input->post('working_again'),
            'likes_about_org' => $this->input->post('likes_about_org'),
            'improvement_suggestions' => $this->input->post('improvement_suggestions'),
            'additional_comments' => $this->input->post('additional_comments'),
        );
        $this->team_management_model->insert_formdata($data);
        $this->exit_view();
    }
    
    public function all_exit_view(){
        $data['staff_members'] = $this->staff_model->get(['is_active' => 1]);
    
        $data['all_forms'] = $this->team_management_model->all_exit_form_data();
    
        $this->load->view('admin/management/all_exit_form', $data);
    }

    public function update_exit_form_status(){
        if($this->input->is_ajax_request()) {
            $form_id = $this->input->post('form_id');
            $status = $this->input->post('status');
    
            if(!$form_id || !$status) {
                echo json_encode(['status' => false, 'message' => 'Invalid Request']);
                return;
            }
    
            $update_status = $this->team_management_model->update_exit_form_data($form_id, $status);
    
            if ($update_status) {
                // Respond with a success status
                echo json_encode(['status' => true, 'message' => 'Status updated successfully']);
            } else {
                // Respond with an error status
                echo json_encode(['status' => false, 'message' => 'Failed to update status']);
            }
        } else {
            show_error('No direct script access allowed');
        }
    }


}


?>