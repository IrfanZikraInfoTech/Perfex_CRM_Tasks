<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Zoom_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

   
    public function update_meeting_settings($data){

        
        $this->db->where('id', 1);
        $this->db->update(db_prefix() . 'zoom', $data);
        return true;

    }

    public function get_api_settings(){

        $this->db->where('id =', 1);
		$array = $this->db->get(db_prefix() . 'zoom')->result_array();
		return $array ;
    }
	
}
