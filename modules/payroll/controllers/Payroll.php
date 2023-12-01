<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payroll extends AdminController
{
    public function __construct(){
        parent::__construct();
        $this->load->model('payroll_model');
        $this->load->library('kpi_system');

    }

// dashboard work:

public function dashboard(){
    $data = [];
    $allMonthsSalaries = $this->payroll_model->get_total_salaries_by_month();
    $data['all_months_salaries'] = $allMonthsSalaries;
    //  var_dump($data['all_months_salaries']);
    if ($this->input->post('selectedMonth')) {
        $selectedMonth = $this->input->post('selectedMonth');
        $currentYear = date('Y'); // Assuming you want the current year.

        // Fetch saved exchange rates for the selected month and year.
        $savedRates = $this->payroll_model->get_exchange_rates($selectedMonth, $currentYear);
        $data['saved_rates'] = $savedRates;
    
        // Pakistan Salaries
        $pakSalariesData = $this->payroll_model->get_total_pak_salaries($selectedMonth);
        $data['total_salaries_pak'] = $pakSalariesData['total_salary'];
        $data['currency_pak'] = $pakSalariesData['currency'];

        // India Salaries
        $indSalariesData = $this->payroll_model->get_total_ind_salaries($selectedMonth);
        $data['total_salaries_ind'] = $indSalariesData['total_salary'];
        $data['currency_ind'] = $indSalariesData['currency'];

        // Bangladesh Salaries
        $bangSalariesData = $this->payroll_model->get_total_bang_salaries($selectedMonth);
        $data['total_salaries_bang'] = $bangSalariesData['total_salary'];
        $data['currency_bang'] = $bangSalariesData['currency'];

        $totalStaff = $this->payroll_model->get_total_staff_for_month($selectedMonth);
        $departmentSalaries = $this->payroll_model->get_department_wise_salaries($selectedMonth);
        $data['department_salaries'] = $departmentSalaries;
      


    } else {
        $selectedMonth = null;
        // Default values agar koi month select nahi kiya gaya
        $data['total_salaries_pak'] = 0;
        $data['currency_pak'] = '';
        $data['total_salaries_ind'] = 0;
        $data['currency_ind'] = '';
        $data['total_salaries_bang'] = 0;
        $data['currency_bang'] = '';
       

    }
    $data['selectedMonth'] = $selectedMonth;
    $data['total_staff'] = $totalStaff ?? 0;
// var_dump($data['total_salaries_ind_usd']);
    $this->load->view('dashboard', $data);
}


public function save_exchange_rate() {
    // Validate the request
    if (!$this->input->is_ajax_request()) {
       exit('No direct script access allowed');
    }

    $month = $this->input->post('month');
    $year = $this->input->post('year');
    $rate = $this->input->post('rate');
    $currency = $this->input->post('currency');

    $data = [
        'month' => $month,
        'year' => $year,
        'rate_' . strtolower($currency) => $rate
    ];

    $this->payroll_model->save_exchange_rate($data);

    echo json_encode(['status' => 'success']);
}


    public function Role_salary(){   
        if (!has_permission('payroll', '', 'admin')) {
            access_denied('Access Denied!');
        }
        $data['staffs'] = $this->payroll_model->get_employee_details();
        $this->load->view('role_salary', $data);
    }

    public function getAttendanceData() {
        $staffId = $this->input->post('staffId');
        $fromDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');
    
        $this->load->model('team_management_model');
        $attendanceData = $this->kpi_system->kpi_punctuality_rate($staffId, $fromDate, $toDate);
    
        echo json_encode($attendanceData);
    }

    public function getUnpaidLeavesData() {
        $staffId = $this->input->post('staffId');
        $fromDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');
        
        // Call the method with the correct parameters
        $unpaidLeavesCount = $this->team_management_model->get_approved_unpaid_leave_days($staffId, $fromDate, $toDate);
        
        echo json_encode(['unpaidLeaves' => $unpaidLeavesCount]);
    }

    public function getallLeaves() {
        $staffId = $this->input->post('staffId');
        $fromDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');
        
        // Call the method with the correct parameters
        $leaves = $this->team_management_model->get_applications_by_staff_id_range($staffId, $fromDate, $toDate);
        
        echo json_encode(['leaves' => $leaves]);
    }

    public function add_payment() {
        $staff_id = $this->input->post('staffId');
        $bonus = $this->input->post('bonus');
        $deduction = $this->input->post('deduction');
        $salary = $this->input->post('salary');
        $status = $this->input->post('status', TRUE);
        $created_date = date('Y-m-d');
        $update_date = null;
        $currency = $this->input->post('currency');  
        $allowances = $this->input->post('allowances');  
        $unpaid_leave_deduction = $this->input->post('unpaid_leave_deduction');  
        $remarks = $this->input->post('remarks');  
        $fromDate = $this->input->post('fromDate');  
        $toDate = $this->input->post('toDate');  
        $totalDays = $this->input->post('totalDays');  
        $daysPresent = $this->input->post('daysPresent');  
        $leaves = $this->input->post('leaves');  
        $unpaidleaves = $this->input->post('unpaidleaves');  



        $data = array(
            'staff_id' => $staff_id,
            'allowances' => $allowances,
            'unpaid_leave_deduction' => $unpaid_leave_deduction,
            'remarks' => $remarks,
            'bonus' => $bonus,
            'deduction' => $deduction,
            'salary' => $salary,
            'status' => $status,
            'created_date' => $created_date,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'totalDays' => $totalDays,
            'daysPresent' => $daysPresent,
            'leaves' => $leaves,
            'unpaidleaves' => $unpaidleaves,
            'currency' => $currency 
        );
    
        $result = $this->payroll_model->add_payment($data);
    
        if ($result) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo json_encode(array('status' => 'fail'));
        }
    }
    
    // Finish Role_salary

    //Setting controllers
    public function add_salary() {
        $employee_id = $this->input->post('employee_id');
        $salary = $this->input->post('salary');

        echo $employee_id;
        echo $this->payroll_model->add_salary_details($employee_id, $salary);
        // redirect or load view here
    } 
    public function Setting(){
        $data['staffs'] = $this->payroll_model->get_employee_details();
        $this->load->view('payroll_setting', $data);    
    }
    //Finished Setting Controllers

    //monthly payroll
    public function monthly_section() {    
        // Get the current month and year if they're not set in the GET parameters
        $month = $this->input->get('month', TRUE) ? $this->input->get('month', TRUE) : date('m');
        $year = $this->input->get('year', TRUE) ? $this->input->get('year', TRUE) : date('Y');
        $monthYear = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
    
        // Retrieve the data from the Model
        $data['staffs'] = $this->payroll_model->get_monthly_payroll($monthYear);
    
        // Load the view and pass the data
        $this->load->view('monthly_section', $data);
    }
    public function update_approval_status() {
    
        // Fetch the POST data
        $status = $this->input->post('status');
        $id = $this->input->post('id');
        $changedby = $this->input->post('changedby');
        
        // Use the model to update the status in the database
        if($this->payroll_model->update_approval_status($id, $status,$changedby)){
            echo ("seccessfull");
        }else{
            echo("unsuccessfull");
        }
    }
    public function delete_record() {
        $id = $this->input->post('id_to_delete');
        
        $this->payroll_model->delete_staff($id);
        
        redirect('payroll/monthly_section');
    }
    
    //finished monthly 

    //payslip
    public function pay_slip(){
        
        $month = $this->input->get('month', TRUE) ? $this->input->get('month', TRUE) : date('m');
        $year = $this->input->get('year', TRUE) ? $this->input->get('year', TRUE) : date('Y');
        $monthYear = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
    
        // Retrieve the data from the Model
        $data['staffs'] = $this->payroll_model->get_monthly_payroll($month);
    
        // Load the view and pass the data
        $this->load->view('pay_slip', $data);
    }
    
    public function view_payslip($id) {
        $data['payslip'] = $this->payroll_model->getPaymentDetails($id);
        if (!$data['payslip']) {
            show_404();
            return;
        }
        // var_dump($data);
        $this->load->view('dynamic_pdf/payslip', $data);
    }

   public function save_payment_mode() {
    $id = $this->input->post('id');
    $paymentMode = $this->input->post('payment_mode');

    // Call the model function to save the payment mode
    $result = $this->payroll_model->savePaymentMode($id, $paymentMode);

    if ($result) {
        echo 'Success'; // You can return a success message or any other response here
    } else {
        echo 'Error'; // You can return an error message or handle the error case here
    }
    }
    public function save_reference_number() {
    $id = $this->input->post('id');
    $referenceNumber = $this->input->post('reference_number');
    $remark = $this->input->post('remark');
    $paymentMode = $this->input->post('payment_mode');

    echo $this->payroll_model->saveReferenceNumber($id, $referenceNumber,$paymentMode,$remark);
    }
}
    // public function make_payment() {
    //     $employee_id = $this->input->post('employee_id');
    //     $employee_payment = $this->input->post('employee_payment');
    //     // Use the loaded Model to update payment
    //     $this->Payroll_model->update_payment($employee_id, $employee_payment);
    //     // return a response to the AJAX call if necessary
    //     echo 'Payment status updated';
    // }
    // public function update_bonus(){   
    //     $data['staffs'] = $this->payroll_model->get_records_details();
    //     $employee_id = $this->input->post('employee_id');
    //     $bonus = $this->input->post('bonus');
    //     $this->load->view('payroll_record', $data);
    //     echo $this->payroll_model->add_salary_bonus($employee_id,$bonus);
    // }
    // public function get_payment_details($id) {
    //     $paymentDetails = $this->payroll_model->getPaymentDetails($id);
    
    //     echo json_encode($paymentDetails);
    // }