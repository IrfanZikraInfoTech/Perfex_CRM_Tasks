<?php

defined('BASEPATH') or exit('No direct script access allowed');
set_time_limit(0);

class Zoom extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('zoom_model');
        $this->load->library('ZoomAPIWrapper');
    }

   
    public function index()
    {
        
        $settings=$this->zoom_model->get_api_settings();
        $apiKey=$settings[0]['api_key'];
        $apiSecret=$settings[0]['api_secret'];
        $email=$settings[0]['zoom_email'];

        $params = array('apiKey' => $apiKey, 'apiSecret' => $apiSecret);
        $zoom = new ZoomAPIWrapper($params);
        $pathParams=array('userId'=>$email);
        $response = $zoom->doRequest(GET, '/users/{userId}/meetings','' ,$pathParams , '');
        $data['data']=$response['meetings'];
        $data['title']= _l('zoom_list');
        $this->load->view('zoom', $data);	 
       
    }

    
	public function create_meeting()
    {

        $data['title']= _l('zoom_create_meeting');
        $this->load->view('zoom_create_meeting', $data);	 

    }

    function submit_meeting(){

        $settings=$this->zoom_model->get_api_settings();
        $apiKey=$settings[0]['api_key'];
        $apiSecret=$settings[0]['api_secret'];
        $email=$settings[0]['zoom_email'];

        $params = array('apiKey' => $apiKey, 'apiSecret' => $apiSecret);
        $zoom = new ZoomAPIWrapper($params);
        $pathParams=array('userId'=>$email);
        
        $subject   = html_purify($this->input->post('subject', false));
        $start_time   = html_purify($this->input->post('start_time', false));
        $timezone   = html_purify($this->input->post('timezone', false));
        $agenda   = html_purify($this->input->post('agenda', false));
        $duration   = html_purify($this->input->post('duration', false));

        $meeting_data = array(
            "topic"  => $subject,
            "start_time"   => $start_time,
            "timezone"=> $timezone,
            "duration"=>$duration,
            "agenda"=> $agenda ,
          );
        $response = $zoom->doRequest(POST, '/users/{userId}/meetings','' ,$pathParams , $meeting_data); 
        if ($response) {
					
            set_alert('success', _l('zoom_meeting_created', _l('zoom')));
            redirect(admin_url('zoom/zoom'));
        }

    }


    public function api_meeting()
    {

        $data['settings']=$this->zoom_model->get_api_settings();
        $data['title']= _l('zoom_api_settings');
        $this->load->view('zoom_api_settings', $data);	 

    }
	
     
    public function api_meeting_submit()
    {

        $data['zoom_email']   = html_purify($this->input->post('zoom_email', false));
        $data['api_key']      = html_purify($this->input->post('api_key', false));
        $data['api_secret']   = html_purify($this->input->post('api_secret', false));

        $id=$this->zoom_model->update_meeting_settings($data);
        $data['title']= _l('zoom_api_settings');
        

        if ($id) {
					
            set_alert('success', _l('zoom_api_updated', _l('zoom')));
            redirect(admin_url('zoom/api_meeting'));
        }

        
    }

    public function add_registrant(){

        $data['title']= _l('zoom_add_registrant');
        $this->load->view('zoom_add_registrant', $data);	
          
    }


    public function submit_registrant(){

        $settings=$this->zoom_model->get_api_settings();
        $apiKey=$settings[0]['api_key'];
        $apiSecret=$settings[0]['api_secret'];
        $email=$settings[0]['zoom_email'];
        $registrant_email   = html_purify($this->input->post('zoom_registrant_email', false));
        $registrant_fname   = html_purify($this->input->post('zoom_registrant_fname', false));
        $registrant_lname = html_purify($this->input->post('zoom_registrant_lname', false));
        $zoom_registrant_meetid = html_purify($this->input->post('zoom_registrant_meetid', false));

        $params = array('apiKey' => $apiKey, 'apiSecret' => $apiSecret);
        $zoom = new ZoomAPIWrapper($params);
        $pathParams=array('meetingId'=>$zoom_registrant_meetid);
        
        

        $registrant_data = array(
            "email"  => $registrant_email,
            "first_name"   => $registrant_fname,
            "last_name"=> $registrant_lname,
          );
        $response = $zoom->doRequest(POST, '/meetings/{meetingId}/registrants','' ,$pathParams , $registrant_data); 	
        
        if($response['code']==3001){
            set_alert('warning', _l('zoom_meeting_not_exists'));	
            redirect(admin_url('zoom/add_registrant'));
        }
        else{
					
            set_alert('success', _l('zoom_registrant_added', _l('zoom')));
            redirect(admin_url('zoom/add_registrant'));
        }
    }

	
}
