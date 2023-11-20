<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payroll_model extends App_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('team_management_model');


    }
    //Role_salary
    public function get_employee_details() {
        $this->db->select('tblstaff.*, tbl_payroll_salary.*, tbl_payroll_records.currency, tbldepartments.name as department_name');
        $this->db->from('tblstaff');
        $this->db->where('tblstaff.active', 1); // Only select active staff
        $this->db->where('tblstaff.report_to IS NOT NULL'); // Exclude staff with 'report_to' as NULL

        $this->db->join('tbl_payroll_salary', 'tbl_payroll_salary.employee_id = tblstaff.staffid', 'left');
        
        // Joining with tbl_payroll_records
        $this->db->join('tbl_payroll_records', 'tbl_payroll_records.staff_id = tblstaff.staffid', 'left');
    
        // Check if 'currency' column exists in tbl_payroll_records
        $column_exists = $this->db->field_exists('currency', 'tbl_payroll_records');
        if (!$column_exists) {
            // If 'currency' column does not exist, create it
            $this->db->query("ALTER TABLE tbl_payroll_records ADD currency VARCHAR(50) DEFAULT 'USD'");
        }
    
        // Joining with tblstaff_departments and tbldepartments to fetch department name
        $this->db->join('tblstaff_departments', 'tblstaff_departments.staffid = tblstaff.staffid', 'left');
        $this->db->join('tbldepartments', 'tbldepartments.departmentid = tblstaff_departments.departmentid', 'left');
    
        $query = $this->db->get();
        return $query->result_array();
    }
    

    public function kpi_monthly_punctuality_rate($staff_id, $year) {
        $monthlyData = [];
    
        for($month = 1; $month <= 12; $month++){
            // Set the start and end dates for the month
            $startDate = new DateTime("$year-$month-01");
            $endDate = clone $startDate;
            $endDate->modify('last day of this month');
    
            // Initialize counters for the month
            $daysOnTime = 0;
            $daysPresent = 0;
            $totalDays = 0;
    
            // Iterate through each date in the month
            while($startDate <= $endDate){
                $currentDate = $startDate->format("Y-m-d");
    
                $onLeave = $this->team_management_model->is_on_leave($staff_id, $currentDate);
                    
                if(!$onLeave){
                    $totalDays++;
    
                    $statusData = $this->team_management_model->staff_attendance_data($staff_id, $currentDate);
                    
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
    
            // Calculate punctuality rate for the month
            $punctualityRate = $totalDays > 0 ? ($daysOnTime / $totalDays) * 100 : 0;
            $attendanceRate = $totalDays > 0 ? ($daysPresent / $totalDays) * 100 : 0;
    
            // Store the data for the month
            $monthlyData[$month] = [
                'total_days' => $totalDays, 
                'days_on_time' => $daysOnTime, 
                'days_present' => $daysPresent, 
                'on_time_percentage' => $punctualityRate, 
                'present_percentage' => $attendanceRate
            ];
        }
    
        return $monthlyData;
    }
    
    
    

    //Setting
    public function add_salary_details($employee_id, $salary) {
        $existing_record = $this->db->get_where('tbl_payroll_salary', array('employee_id' => $employee_id))->row();

        if ($existing_record) {
            $data = array(
                'employee_salary' => $salary,
                // add other fields here as necessary
            );

            $this->db->where('employee_id', $employee_id);
            return $this->db->update('tbl_payroll_salary', $data);
        } else {
            $data = array(
                'employee_id' => $employee_id,
                'employee_salary' => $salary,
                // add other fields here as necessary
            );

            return $this->db->insert('tbl_payroll_salary', $data);
        }
    }   

    //monthly payroll
    public function get_monthly_payroll($month) {
        $this->db->select('tblstaff.firstname, tbl_payroll_records.*,approver.firstname AS approver_name,');
        $this->db->from('tbl_payroll_records');
        $this->db->join('tblstaff', 'tbl_payroll_records.staff_id = tblstaff.staffid', 'left');
        $this->db->join('tblstaff AS approver', 'tbl_payroll_records.changedby = approver.staffid', 'left');
        // $this->db->where('month', $month);
        $query = $this->db->get();
    
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    } 
    public function update_approval_status($id, $status, $changedby) {
        $this->db->set('status', $status);
        $this->db->set('changedby', $changedby);
        $this->db->where('id', $id);
        return $this->db->update('tbl_payroll_records');
    }

    public function delete_staff($id) {
        $this->db->where('id', $id);
        $this->db->delete('tbl_payroll_records');
    }

    
    //payslip
    public function getPaymentDetails($id) {
        $this->db->select('tblstaff.*, tbl_payroll_records.salary, tbl_payroll_records.staff_id, tbl_payroll_records.bonus, approver.firstname AS approver_name, tbl_payroll_records.deduction, tbl_payroll_records.id, tbl_payroll_records.changedby, tbl_payroll_records.Refrence_number,tbl_payroll_records.payment_mode,tbl_payroll_records.month,tbl_payroll_records.status');
        $this->db->from('tbl_payroll_records');
        $this->db->join('tblstaff', 'tbl_payroll_records.staff_id = tblstaff.staffid', 'left');
        $this->db->join('tblstaff AS approver', 'tbl_payroll_records.changedby = approver.staffid', 'left');
        $this->db->where('id', $id);
        $query = $this->db->get();
        
        return $query->row();
    } 
    public function savePaymentMode($id, $paymentMode, ){
        $this->db->set('payment_mode', $paymentMode);
        $this->db->where('id', $id);
        return $this->db->update('tbl_payroll_records');
    }
    public function saveReferenceNumber($id, $referenceNumber,$paymentMode,$remark) {
        $this->db->set('Refrence_number', $referenceNumber);
        $this->db->set('remark', $remark);
        $this->db->set('payment_mode', $paymentMode);
        $this->db->where('id', $id);
        return $this->db->update('tbl_payroll_records');
    }
    public function add_payment($data) {
     
        return $this->db->insert('tbl_payroll_records', $data);
    }
}
    // public function get_payroll_records(){
    //     $this->db->select('tblstaff.firstname,tblstaff.staffid,tbl_payroll_records.*',);
    //     $this->db->from('tblstaff');
    //     $this->db->join('tbl_payroll_records', 'tbl_payroll_records.staff_id = tblstaff.staffid', 'left');
    //     $query = $this->db->get();
    //     return $query->result_array();
    // }
    // public function add_salary_bonus($employee_id,$bonus){
    //     $existing_record = $this->db->get_where('tbl_payroll_records', array('staff_id' => $employee_id))->row();

    //     if ($existing_record) {
    //         $data = array(
    //             'bonus' => $bonus,
    //             // add other fields here as necessary
    //         );
    //         $this->db->where('staff_id', $employee_id);
    //         return $this->db->update('tbl_payroll_records', $data);
    //     } else {
    //         $data = array(
    //             'staff_id' => $employee_id,
    //             // add other fields here as necessary
    //         );

    //         return $this->db->insert('tbl_payroll_records', $data);
    //     }
    // }

    
