<?php

defined('BASEPATH') or exit('No direct script access allowed');


	// Get project name

	function project_name_by_id($projectid)
	{

	   $CI = get_instance();
	   $CI->load->model('feedback/feedback_model');
	   $prj_array = $CI->feedback_model->get_project_info($projectid);
	   if(!empty($prj_array)){
	   $prj_name=$prj_array[0]['name'];
	   return $prj_name;
	   }else{
		 $prj_name='';
         return $prj_name;   		 
	   } 	   
	}
	
	function count_client_projects($client_id)
	{

	   $CI = get_instance();
	   $CI->load->model('feedback/feedback_model');
	   $array = $CI->feedback_model->get_projects($client_id);
	   $count=count($array);
	   return $count;
	   
	}