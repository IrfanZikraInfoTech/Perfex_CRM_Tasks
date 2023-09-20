<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Feedback_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add($data)
    {
		$projectid= $data['projectid'];
        $this->db->where('projectid', $projectid);
        $feedback = $this->db->get(db_prefix().'feedback')->row();
		if(!$feedback){
			$datecreated = date('Y-m-d H:i:s');
			$this->db->insert(db_prefix().'feedback', [
			   
				'customerid'     => $data['customerid'], 
				'projectid'     => $data['projectid'], 
				'date'     => $datecreated,
			   
			]);
			$feedbackid = $this->db->insert_id();
			$subject='Feedback';

			log_activity('New Feedback Added [ID: ' . $feedbackid . ', Subject: ' . $subject . ']');

			return $feedbackid;
		}else{
            return false;
        }			
    }
	
	public function feedback_add($data)
    {
		
		$projectid=$data['projectid'];
        $this->db->where('projectid', $projectid);
        $this->db->update(db_prefix() . 'feedback', $data);
			

		 return true;
				
    }
	
	public function get_projects($client_id){
		
		$this->db->where('customerid', $client_id);
		$this->db->where('feedback_submitted =', NULL);
		$feedback_array = $this->db->get(db_prefix() . 'feedback')->result_array();
		return $feedback_array ;
	}

    public function get_project_info($projectid){	
	
	    $this->db->where('id', $projectid);
		$prj_array = $this->db->get(db_prefix() . 'projects')->result_array();
		return $prj_array ;
	
	}
	
	public function get_feedback(){
		
		$this->db->where('feedback_submitted =', 1);
		$feedback_array = $this->db->get(db_prefix() . 'feedback')->result_array();
		return $feedback_array;
	}
	
}
