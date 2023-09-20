<?php

defined('BASEPATH') or exit('No direct script access allowed');
set_time_limit(0);

class Feedback extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('feedback_model');
    }

   
    public function index()
    {
        $data['title']= _l('create_feedback_request');
        $this->load->view('feedback', $data);
		
		 
        if ($this->input->post()) {
            
			$data= $this->input->post();
			$data['customerid']   = html_purify($this->input->post('clientid', false));
			$data['projectid']   = html_purify($this->input->post('project_id', false));
			$id=$this->feedback_model->add($data);
			
                if ($id) {
                    set_alert('success', _l('feedback_added_successfully', _l('feedback')));
                    redirect(admin_url('feedback'));
                }else{
					
				    set_alert('warning', _l('feedback_already_exists'));	
					redirect(admin_url('feedback'));
				}	
		}	
    }
	
	
	
	    public function feedback_received()
    {
		$data['feedback_array']=$this->feedback_model->get_feedback();
        $data['title']= _l('feedback_received');
        $this->load->view('feedback_received', $data);
    }
	
	
	
	
	
}
