<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payroll extends AdminController
{
    public function __construct(){
        parent::__construct();
        $this->load->model('payroll_model');
    }

    // Role_salary
    public function Role_salary(){   
        if (!has_permission('payroll', '', 'admin')) {
            access_denied('Access Denied!');
        }
        $data['staffs'] = $this->payroll_model->get_employee_details();
        $this->load->view('role_salary', $data);
        // $this->load->view('payroll_manage');
    }
    public function add_payment() {
        $staff_id = $this->input->post('staffId');
        $bonus = $this->input->post('bonus');
        $deduction = $this->input->post('deduction');
        $salary = $this->input->post('salary');
        $status = $this->input->post('status', TRUE);
        $created_date = date('Y-m-d');
        $update_date = null;
        $month = $this->input->post('month');

        $data = array(
            'staff_id' => $staff_id,
            'bonus' => $bonus,
            'deduction' => $deduction,
            'salary' => $salary,
            'status' => $status,
            'created_date' => $created_date,
            'update_date' => $update_date,
            'month' => $month,
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
        if (!has_permission('payroll', '', 'admin')) {
            access_denied('Access Denied!');
        }
        $data['staffs'] = $this->payroll_model->get_employee_details();
        $this->load->view('payroll_setting', $data);    
    }
    //Finished Setting Controllers

    //monthly payroll
    public function monthly_section() {    

        if (!has_permission('payroll', '', 'admin')) {
            access_denied('Access Denied!');
        }

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
    //finished monthly 

    //payslip
    public function pay_slip(){

        if (!has_permission('payroll', '', 'admin')) {
            access_denied('Access Denied!');
        }

        // Get the month and year from the GET parameters, set a default if they're not set
        $month = $this->input->get('month', TRUE) ? $this->input->get('month', TRUE) : date('Y-m-d');
   
        // Retrieve the data from the Model
        $data['staffs'] = $this->payroll_model->get_monthly_payroll($month);
    
        // Load the view and pass the data
        $this->load->view('pay_slip', $data);
   }
    public function view_payslip($type, $id) {
        if (!has_permission('payroll', '', 'admin')) {
            access_denied('Access Denied!');
        }
        // Load the employee data
        $data = $this->payroll_model->getPaymentDetails($id);
        if (!$data) {
            show_404();
            return;
        }

        $mpdf = new \Mpdf\Mpdf();

        // Define some HTML content with style
        $salary = $data->salary;
        $bonus = $data->bonus;
        $deduction = $data->deduction;
        $total = $salary + $bonus - $deduction;

        $html = file_get_contents(base_url('modules/payroll/views/pay_salary_slip_model.html'));

        $html = str_replace('{firstname}', (!empty($data->firstname) ? $data->firstname : "Unspecified"), $html);
        $html = str_replace('{email}', (!empty($data->email) ? $data->email : "Unspecified"), $html);
        $html = str_replace('{phonenumber}', (!empty($data->phonenumber) ? $data->phonenumber : "Unspecified"), $html);
        $html = str_replace('{payment_mode}', (!empty($data->payment_mode) ? $data->payment_mode : "Unspecified"), $html);
        $html = str_replace('{salary}', (!empty($salary) ? $salary : "Unspecified"), $html);
        $html = str_replace('{bonus}', (!empty($bonus) ? $bonus : "Unspecified"), $html);
        $html = str_replace('{deduction}', (!empty($deduction) ? $deduction : "Unspecified"), $html);
        $html = str_replace('{total}', (!empty($total) ? $total : "Unspecified"), $html);
        $html = str_replace('{Refrence_number}', (!empty($data->Refrence_number) ? $data->Refrence_number : "Unspecified"), $html);
        $html = str_replace('{approver_name}', (!empty($data->approver_name) ? $data->approver_name : "Unspecified"), $html);


        // Output the HTML content
        $mpdf->WriteHTML($html);

        // Close and output PDF document
        if($type==1){
            $mpdf->Output('Salaryslip.pdf', 'I');
        }else{
            $mpdf->Output('Salaryslip.pdf' . '.pdf', 'D');
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
    $paymentMode = $this->input->post('payment_mode');

    echo $this->payroll_model->saveReferenceNumber($id, $referenceNumber,$paymentMode);
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