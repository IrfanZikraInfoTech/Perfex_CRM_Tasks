<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payroll extends AdminController
{
    public function __construct(){
        parent::__construct();
        $this->load->model('payroll_model');
        $this->load->library('kpi_system');

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
            'currency' => $currency  // Add currency to the data array
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
        // Get the month and year from the GET parameters, set a default if they're not set
        $month = $this->input->get('month', TRUE) ? $this->input->get('month', TRUE) : date('Y-m-d');
    
        // Retrieve the data from the Model
        $data['staffs'] = $this->payroll_model->get_monthly_payroll($month);
    
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
        // Get the month and year from the GET parameters, set a default if they're not set
        $month = $this->input->get('month', TRUE) ? $this->input->get('month', TRUE) : date('Y-m-d');
   
        // Retrieve the data from the Model
        $data['staffs'] = $this->payroll_model->get_monthly_payroll($month);
    
        // Load the view and pass the data
        $this->load->view('pay_slip', $data);
   }
   public function view_payslip($type, $id) {
    // Load the employee data
    $data = $this->payroll_model->getPaymentDetails($id);
    if (!$data) {
        show_404();
        return;
    }

    // Create a new mPDF object.
    $mpdf = new \Mpdf\Mpdf();

    // Add a page
    $mpdf->AddPage();

    // Set the image that will be used as a background.
    $img_file = "https://i.ibb.co/qFYCS3K/Letter-haed-empty-01.jpg";
    $mpdf->Image($img_file, 1, 9.5, 285, 360, '', '', '', false, 300, '', false, false, 0);

    // Define some HTML content with style
    $salary = $data->salary;
    $bonus = $data->bonus;
    $deduction = $data->deduction;
    $total = $salary + $bonus - $deduction;

    $html = file_get_contents(base_url('modules/payroll/views/pay_salary_slip_model.html'));

    $html = str_replace(
        ['{firstname}','{lastname}','{email}', '{phonenumber}', '{payment_mode}', '{salary}', '{bonus}', '{deduction}', '{total}','{Refrence_number}','{approver_name}','{remark}'],
        [$data->firstname, $data->lastname, $data->email, $data->phonenumber, $data->payment_mode, $salary, $bonus, $deduction, $total, $data->Refrence_number, $data->approver_name,],
        $html
    );

    // Output the HTML content
    $mpdf->WriteHTML($html);

    // Close and output PDF document
    if($type==1){
        $mpdf->Output('Salaryslip.pdf', 'I');
    }else{
        $mpdf->Output('Salaryslip.pdf', 'D');
    }
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