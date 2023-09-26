<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payroll_model extends App_Model
{
    public function __construct(){
        parent::__construct();
    }
    //Role_salary
    public function get_employee_details(){
        $this->db->select('tblstaff.firstname,tblstaff.lastname,tblstaff.staffid,tblstaff.email,tbl_payroll_salary.*',);
        $this->db->from('tblstaff');
        $this->db->join('tbl_payroll_salary', 'tbl_payroll_salary.employee_id = tblstaff.staffid', 'left');
        $query = $this->db->get();
        return $query->result_array();
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
        $this->db->select('tblstaff.firstname, tbl_payroll_records.salary, tbl_payroll_records.staff_id, tbl_payroll_records.bonus, tbl_payroll_records.deduction, tbl_payroll_records.id, tbl_payroll_records.changedby, approver.firstname AS approver_name, tbl_payroll_records.Refrence_number,tbl_payroll_records.payment_mode,tbl_payroll_records.month,tbl_payroll_records.status');
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
    public function saveReferenceNumber($id, $referenceNumber,$paymentMode) {
        $this->db->set('Refrence_number', $referenceNumber);
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

    
