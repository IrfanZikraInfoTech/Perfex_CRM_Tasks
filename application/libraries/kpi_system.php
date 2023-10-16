<?php defined('BASEPATH') or exit('No direct script access allowed');

class kpi_system
{

    public function __construct() {
        $this->ci = & get_instance();

        $this->ci->load->model('team_management_model');
        $this->ci->load->model('tasks_model');
    }
    
    public function kpi_punctuality_rate($staff_id, $from, $to = null){
        if(!$to){
            $to = $from;
        }
    
        // Initialize counters
        $daysOnTime = 0;
        $daysPresent = 0;
        $totalDays = 0;
    
        // Convert dates to DateTime objects for iteration
        $startDate = new DateTime($from);
        $endDate = new DateTime($to);
    
        // Iterate through each date in the range
        while($startDate <= $endDate){

            $currentDate = $startDate->format("Y-m-d");

            $onLeave = $this->ci->team_management_model->is_on_leave($staff_id, $currentDate);

            if(!$onLeave){
                $totalDays++;

                $statusData = $this->ci->team_management_model->staff_attendance_data($staff_id, $currentDate);
        
                // Check if status is 'present' and increment counters accordingly
                if(isset($statusData['status']) && $statusData['status'] == 'present'){
                    $daysOnTime++;
                }
                
                if (isset($statusData['status']) && ($statusData['status'] == 'present' || $statusData['status'] == 'late')){
                    $daysPresent++;
                }

            }


            $startDate->modify('+1 day');
        }
    
        // Calculate punctuality rate
        if($totalDays > 0) {
            $punctualityRate = ($daysOnTime / $totalDays) * 100;
            $attendanceRate = ($daysPresent / $totalDays) * 100;
        } else {
            $punctualityRate = 0; // To handle edge case where totalDays is 0
            $attendanceRate = 0;
        }

        return ['total_days'=>$totalDays, 'days_on_time' => $daysOnTime, 'days_present' =>  $daysPresent, 'on_time_percentage'=> $punctualityRate, 'present_percentage'=> $attendanceRate];
    }

    
    public function kpi_task_rates($staff_id, $from, $to = null){
        if(!$to){
            $to = $from;
        }
    
        // Initialize counters
        $completedTasksWithinDue = 0;
        $completedTasksPastDue = 0;

        // Initialize counters for total estimated hours and total spent hours
        $totalEstimatedHours = 0;
        $totalSpentHours = 0;
    
        // Fetch tasks in date range using helper function
        $tasks = get_tasks_in_date_range($staff_id, $from, $to);
    
        // Iterate through each task
        foreach($tasks as $task){

            // Completion KPIS
            if(isset($task->datefinished) && $task->status == 5){

                if(strtotime(date("Y-m-d", strtotime($task->datefinished))) <= strtotime($task->duedate) ){
                    $completedTasksWithinDue++;
                }else{
                    $completedTasksPastDue++;
                }

            }

            //Timer KPIS
            $estimatedHours = $task->estimated_hours;
            
            // Get the total time spent on the task in seconds
            $spentSeconds = $this->ci->tasks_model->calc_task_total_time($task->id);
            // Convert seconds to hours
            $spentHours = $spentSeconds / 3600;
    
            $totalEstimatedHours += $estimatedHours;
            $totalSpentHours += $spentHours;

        }
    
        // Calculate task completion rate
        $taskCount = count($tasks);
        if($taskCount > 0) {
            $taskCompletionRate = ($completedTasksWithinDue / $taskCount) * 100;
            $taskEfficiencyRate = (1 - ($completedTasksPastDue / $taskCount)) * 100;
        } else {
            $taskCompletionRate = 0; // To handle edge case where taskCount is 0
            $taskEfficiencyRate = 0;
        }

        // Calculate Task Time Adherence Rate
        if($totalSpentHours > 0) {
            $taskTimeAdherenceRate = ($totalEstimatedHours / $totalSpentHours) * 100;

            if($taskTimeAdherenceRate > 100) $taskTimeAdherenceRate = 100;

        } else {
            $taskTimeAdherenceRate = 0; // To handle edge case where totalSpentHours is 0
        }
    
        return ['completed_tasks_past_due'=>$completedTasksPastDue, 'completed_tasks_within_due' => $completedTasksWithinDue, 'total_tasks'=>$taskCount, 'completion_rate'=> $taskCompletionRate, 'efficiency_rate'=>$taskEfficiencyRate,'esimate_hours'=>$totalEstimatedHours , 'spent_hours'=>$totalSpentHours,'timer_adherence_rate'=>$taskTimeAdherenceRate];
    }

    
    public function kpi_summary_adherence_rate($staff_id, $from, $to = null){
        if(!$to){
            $to = $from;
        }
    
        // Get summaries for the staff in the date range
        $summaries = $this->ci->team_management_model->get_staff_summaries_in_range($staff_id, $from, $to);
    
        $daysWithSummaries = 0;
        foreach($summaries as &$summary){
            if(!$this->ci->team_management_model->is_on_leave($staff_id, $summary->date)){
                $daysWithSummaries++;
            }else{
                unset($summary);
            }
        }

        // Convert dates to DateTime objects
        $startDate = new DateTime($from);
        $endDate = new DateTime($to);
    
        // Calculate total days in the range
        $totalDays = $startDate->diff($endDate)->days + 1;
    
        $daysOnLeave = 0;
        for ($date = clone $startDate; $date <= $endDate; $date->modify('+1 day')) {
            if ($this->ci->team_management_model->is_on_leave($staff_id, $date->format("Y-m-d"))) {
                $daysOnLeave++;
            }
        }
    
        // Calculate Summary Adherence Rate
        $workingDays = $totalDays - $daysOnLeave;
        $SAR = ($workingDays > 0) ? ($daysWithSummaries / $workingDays) * 100 : 0;
    
        return [
            'days_with_summaries' => $daysWithSummaries,
            'working_days' => $workingDays,
            'percentage' => $SAR,
            'summaries' => $summaries
        ];
    }
    

    public function kpi_afk_adherence_rate($staff_id, $from, $to = null){
        if(!$to){
            $to = $from;
        }
    
        // Initialize counters for total allowed and actual AFK time
        $totalAllowedAfkTime = 0;
        $totalActualAfkTime = 0;
    
        // Convert dates to DateTime objects for iteration
        $startDate = new DateTime($from);
        $endDate = new DateTime($to);
    
        // Iterate through each date in the range
        while($startDate <= $endDate){
            $currentDate = $startDate->format("Y-m-d");
    
            // Fetch shift timings for the day
            $shiftTiming = $this->ci->team_management_model->get_day_shift_total($staff_id, $currentDate);
    
            // Determine allowed AFK time based on shift timing and sum it up
            if($shiftTiming > (4 * 3600)) { // 4 hours in seconds
                $totalAllowedAfkTime += (30 * 60); // 30 minutes in seconds
            } else {
                $totalAllowedAfkTime += (15 * 60); // 15 minutes in seconds
            }
    
            // Fetch actual AFK time for the day and sum it up
            $totalActualAfkTime += $this->ci->team_management_model->get_total_afk_time($staff_id, $currentDate);
    
            $startDate->modify('+1 day');
        }
    
        // Calculate AFK Adherence Rate
        if($totalActualAfkTime > 0) {
            $afkAdherenceRate = ($totalAllowedAfkTime / $totalActualAfkTime) * 100;
            if($afkAdherenceRate > 100) $afkAdherenceRate = 100;
        } else {
            $afkAdherenceRate = 100; // If no actual AFK time, adherence is considered optimal
        }

        $totalAllowedAfkTime /= 3600;
        $totalActualAfkTime /= 3600;
    
        return [
            'allowed_afk_time' => $totalAllowedAfkTime,
            'actual_afk_time' => $totalActualAfkTime,
            'percentage' => $afkAdherenceRate
        ];
    }
    
    public function kpi_shift_productivity_rate($staff_id, $from, $to = null){
        if(!$to){
            $to = $from;
        }
    
        // Initialize counters for total task time and logged time
        $totalTaskTime = 0;
        $totalLoggedTime = 0;
    
        // Grace period is 30 minutes in seconds for each day
        $gracePeriodPerDay = 30 * 60;
    
        // Convert dates to DateTime objects for iteration
        $startDate = new DateTime($from);
        $endDate = new DateTime($to);
    
        // Iterate through each date in the range
        while($startDate <= $endDate){
            $currentDate = $startDate->format("Y-m-d");
            
            // Extract day, month, and year from the current date for the task time method
            $day = $startDate->format("d");
            $month = $startDate->format("m");
            $year = $startDate->format("Y");
    
            // Fetch total task time for the day and sum it up
            $totalTaskTime += $this->ci->team_management_model->get_total_task_time_for_day($staff_id, $day, $month, $year);
    
            // Fetch total logged time for the day and sum it up
            $totalLoggedTime += $this->ci->team_management_model->get_total_logged_time_of_date($staff_id, $currentDate);
    
            $startDate->modify('+1 day');
        }
    
        // Subtract grace period from total logged time
        $adjustedLoggedTime = $totalLoggedTime - ($gracePeriodPerDay * ($endDate->diff($startDate)->days + 1)); // Including both start and end date
    
        // Calculate Shift Productivity Rate
        if($adjustedLoggedTime > 0) {
            $shiftProductivityRate = ($totalTaskTime / $adjustedLoggedTime) * 100;

            $shiftProductivityRate = ($shiftProductivityRate > 100) ? 100 : $shiftProductivityRate;

        } else {
            $shiftProductivityRate = 0; // To handle edge case where adjustedLoggedTime is 0
        }

        $totalTaskTime /= 3600;
        $totalLoggedTime /= 3600;
    
        return [
            'total_task_time' => $totalTaskTime,
            'total_logged_time' => $totalLoggedTime,
            'percentage' => $shiftProductivityRate
        ];
    }
    
    public function calculate_ops($task_completion_rate, $shift_productivity_rate, $summary_adherence_rate, $punctuality_rate, $timer_adherence_rate, $afk_adherence_rate, $efficiency_rate) {

        $ops = (0.2 * $task_completion_rate) + 
               (0.1 * $shift_productivity_rate) + 
               (0.15 * $summary_adherence_rate) + 
               (0.2 * $punctuality_rate) + 
               (0.15 * $timer_adherence_rate) + 
               (0.1 * $afk_adherence_rate) + 
               (0.1 * $efficiency_rate);
    
        return $ops;
    }

    public function attendance_data($staff_id, $from, $to = null){
        if(!$to){
            $to = $from;
        }
    
        // Initialize counters
        $totalClockable = 0;
        $totalClocked = 0;
    
        // Convert dates to DateTime objects for iteration
        $startDate = new DateTime($from);
        $endDate = new DateTime($to);
    
        // Iterate through each date in the range
        while($startDate <= $endDate){

            $currentDate = $startDate->format("Y-m-d");

            $onLeave = $this->ci->team_management_model->is_on_leave($staff_id, $currentDate);

            if(!$onLeave){
                $totalClockable += $this->ci->team_management_model->get_total_clockable($staff_id, $currentDate);
                $totalClocked += $this->ci->team_management_model->get_total_clocked($staff_id, $currentDate);
            }


            $startDate->modify('+1 day');
        }

        return ['total_clockable'=>$totalClockable, 'total_clocked' => $totalClocked];
    }
    
}