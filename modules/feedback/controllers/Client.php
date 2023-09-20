<?php

defined('BASEPATH') or exit('No direct script access allowed');
set_time_limit(0);

class Client extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('feedback_model');
		$this->load->helper('feedback_helper');
		hooks()->do_action('after_clients_area_init', $this);
    }

	public function client_feedback()
    {
         $client_id=$this->session->userdata('client_user_id');
		 $data['feedbacks'] = $this->feedback_model->get_projects($client_id);
         $data['title']= _l('customer_feedback');
         $this->data($data);
		 $this->view('client_feedback', $data);
		 $this->layout();
    }
	
	public function project()
    {
        $data['projectid']=$this->uri->segment(4);
		$data['title']= _l('submit_feedback');
        $this->data($data);
		$this->view('submit_feedback', $data);
		$this->layout();
    
    }
	
	public function submit_project()
    {
		if ($this->input->post()) {
            
			$data= $this->input->post();
			$data['coding']   = html_purify($this->input->post('coding', false));
			$data['projectid']   = html_purify($this->input->post('projectid', false));
			$data['communication']   = html_purify($this->input->post('communication', false));
			$data['services']   = html_purify($this->input->post('services', false));
			$data['recommendation']   = html_purify($this->input->post('recommendation', false));
			$data['message']   = html_purify($this->input->post('message', false));
			$data['feedback_submitted']   = '1';
			
			$id=$this->feedback_model->feedback_add($data);
			
                if ($id) {
                    set_alert('success', _l('feedback_added_successfully', _l('feedback')));
                    redirect(site_url('feedback/client/client_feedback'));
                }
		}	
    
    }
	
}
