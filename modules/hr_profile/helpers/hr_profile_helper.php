<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * get hr profile option
 * @param  string $name 
 */
function get_hr_profile_option($name)
{
	$CI = & get_instance();
	$options = [];
	$val  = '';
	$name = trim($name);
	if (!isset($options[$name])) {
		$CI->db->select('option_val');
		$CI->db->where('option_name', $name);
		$row = $CI->db->get(db_prefix() . 'hr_profile_option')->row();
		if ($row) {
			$val = $row->option_val;
		}
	} else {
		$val = $options[$name];
	}
	return hooks()->apply_filters('get_hr_profile_option', $val, $name);
}
/**
 * row options exist
 * @param  string $name 
 * @return integer       
 */
function hr_profile_row_options_exists($name){
	$CI = & get_instance();
	$i = count($CI->db->query('Select * from '.db_prefix().'hr_profile_option where option_name = '.$name)->result_array());
	if($i == 0){
		return 0;
	}
	if($i > 0){
		return 1;
	}
}
/**
 * job position by staff
 * @param  integer $staffid 
 * @return string          
 */
function hr_profile_job_position_by_staff($staffid){
	$CI             = &get_instance();
	$CI->load->model('hr_profile/hr_profile_model');
	$staff = $CI->hr_profile_model->get_staff($staffid);
	if($staff){
		$job_name = hr_profile_job_name_by_id($staff->job_position);
	}else{
		$job_name = '';
	}

	return $job_name;
}


	/**
	 * job name by id
	 * @param  integer $job_position 
	 * @return string               
	 */
	function hr_profile_job_name_by_id($job_position){
		$CI             = &get_instance();
		$CI->db->where('position_id', $job_position);
		$CI->db->select('position_name');
		$dpm = $CI->db->get(db_prefix().'hr_job_position')->row();
		if($dpm){
			return $dpm->position_name; 
		}else{
			return ''; 
		} 
	}


	/**
	 * hr profile reformat currency asset
	 * @param  string $value 
	 * @return string        
	 */
	function hr_profile_reformat_currency($value)
	{
		$f_dot = str_replace(',','', $value);
		return ((float)$f_dot + 0);
	}


	/**
	 * get department name
	 * @param  integer $departmentid 
	 * @return object               
	 */
	function hr_profile_get_department_name($departmentid){
		$CI = &get_instance();
		return $CI->db->query('select '.db_prefix().'departments.name from tbldepartments where departmentid = '.$departmentid)->row();
	}


	/**
	 * handle hr profile job position attachments array
	 * @param  [type] $jobposition_tid 
	 * @param  string $index_name      
	 * @return [type]                  
	 */
	function handle_hr_profile_job_position_attachments_array($jobposition_tid, $index_name = 'attachments')
	{
		$uploaded_files = [];
		$path           = get_hr_profile_upload_path_by_type('job_position').$jobposition_tid .'/';
		$CI             = &get_instance();
		if (isset($_FILES[$index_name]['name'])
			&& ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)) {
			if (!is_array($_FILES[$index_name]['name'])) {
				$_FILES[$index_name]['name']     = [$_FILES[$index_name]['name']];
				$_FILES[$index_name]['type']     = [$_FILES[$index_name]['type']];
				$_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
				$_FILES[$index_name]['error']    = [$_FILES[$index_name]['error']];
				$_FILES[$index_name]['size']     = [$_FILES[$index_name]['size']];
			}

				_file_attachments_index_fix($index_name);
				for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
					$tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];
					if (!empty($tmpFilePath) && $tmpFilePath != '') {
						if (_perfex_upload_error($_FILES[$index_name]['error'][$i])
							|| !_upload_extension_allowed($_FILES[$index_name]['name'][$i])) {
							continue;
					}

					_maybe_create_upload_path($path);
					$filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
					$newFilePath = $path . $filename;
					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						array_push($uploaded_files, [
							'file_name' => $filename,
							'filetype'  => $_FILES[$index_name]['type'][$i],
						]);
						if (is_image($newFilePath)) {
							create_img_thumb($path, $filename);
						}
					}
				}
			}
		}
		if (count($uploaded_files) > 0) {
			return $uploaded_files;
		}
		return false;
	}


/**
 * get hr profile upload path by type
 * @param  string $type 
 */
function get_hr_profile_upload_path_by_type($type)
{
	$path = '';
	switch ($type) {
		case 'staff_contract':
		$path = HR_PROFILE_CONTRACT_ATTACHMENTS_UPLOAD_FOLDER;

		break;

		case 'job_position':
		$path = HR_PROFILE_JOB_POSIITON_ATTACHMENTS_UPLOAD_FOLDER;

		break;

		case 'kb_article_files':
		$path = HR_PROFILE_Q_A_ATTACHMENTS_UPLOAD_FOLDER;
		break;
		
		case 'att_files':
		$path = HR_PROFILE_FILE_ATTACHMENTS_UPLOAD_FOLDER;

		break;
		
		
	}

	return hooks()->apply_filters('get_hr_profile_upload_path_by_type', $path, $type);
}


	/**
	 * get status modules
	 * @param  string $module_name 
	 * @return boolean              
	 */
	function hr_profile_get_status_modules($module_name){
		$CI             = &get_instance();
		$CI->db->where('module_name',$module_name);
		$module = $CI->db->get(db_prefix().'modules')->row();
		if($module&&$module->active==1)
		{
			return true;
		}
		return false;
	}


		/**
		 * handle hr profile attachments array
		 * @param  [type] $staffid    
		 * @param  string $index_name 
		 * @return [type]             
		 */
		function handle_hr_profile_attachments_array($staffid, $index_name = 'attachments')
		{
			$uploaded_files = [];
			$path           = get_hr_profile_upload_path_by_type('att_files').$staffid .'/';

			$CI             = &get_instance();
			if (isset($_FILES[$index_name]['name'])
				&& ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)) {
				if (!is_array($_FILES[$index_name]['name'])) {
					$_FILES[$index_name]['name']     = [$_FILES[$index_name]['name']];
					$_FILES[$index_name]['type']     = [$_FILES[$index_name]['type']];
					$_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
					$_FILES[$index_name]['error']    = [$_FILES[$index_name]['error']];
					$_FILES[$index_name]['size']     = [$_FILES[$index_name]['size']];
				}

					_file_attachments_index_fix($index_name);
					for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
						// Get the temp file path
						$tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

						// Make sure we have a filepath
						if (!empty($tmpFilePath) && $tmpFilePath != '') {
							if (_perfex_upload_error($_FILES[$index_name]['error'][$i])
								|| !_upload_extension_allowed($_FILES[$index_name]['name'][$i])) {
								continue;
						}

						_maybe_create_upload_path($path);
						$filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
						$newFilePath = $path . $filename;

							// Upload the file into the temp dir
						if (move_uploaded_file($tmpFilePath, $newFilePath)) {
							array_push($uploaded_files, [
								'file_name' => $filename,
								'filetype'  => $_FILES[$index_name]['type'][$i],
							]);
							if (is_image($newFilePath)) {
								create_img_thumb($path, $filename);
							}
						}
					}
				}
			}

			if (count($uploaded_files) > 0) {
				return $uploaded_files;
			}

			return false;
		}


	/**
	 * hr profile staff profile image upload for staffmodel
	 * @param  integer $staff id 
	 * @return boolean           
	 */
	function hr_profile_handle_staff_profile_image_upload($staff_id = '')
	{
		if (!is_numeric($staff_id)) {
			$staff_id = get_staff_user_id();
		}
		if (isset($_FILES['profile_image']['name']) && $_FILES['profile_image']['name'] != '') {

			hooks()->do_action('before_upload_staff_profile_image');
			$path = get_upload_path_by_type('staff') . $staff_id . '/';
			// Get the temp file path
			$tmpFilePath = $_FILES['profile_image']['tmp_name'];
			// Make sure we have a filepath
			if (!empty($tmpFilePath) && $tmpFilePath != '') {
				// Getting file extension
				$extension          = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
				$allowed_extensions = [
					'jpg',
					'jpeg',
					'png',
				];

				$allowed_extensions = hooks()->apply_filters('staff_profile_image_upload_allowed_extensions', $allowed_extensions);

				if (!in_array($extension, $allowed_extensions)) {
					set_alert('warning', _l('file_php_extension_blocked'));

					return false;
				}
				_maybe_create_upload_path($path);
				$filename    = unique_filename($path, $_FILES['profile_image']['name']);
				$newFilePath = $path . '/' . $filename;
				// Upload the file into the company uploads dir
				if (move_uploaded_file($tmpFilePath, $newFilePath)) {
					$CI                       = & get_instance();
					$config                   = [];
					$config['image_library']  = 'gd2';
					$config['source_image']   = $newFilePath;
					$config['new_image']      = 'thumb_' . $filename;
					$config['maintain_ratio'] = true;
					$config['width']          = hooks()->apply_filters('staff_profile_image_thumb_width', 320);
					$config['height']         = hooks()->apply_filters('staff_profile_image_thumb_height', 320);
					$CI->image_lib->initialize($config);
					$CI->image_lib->resize();
					$CI->image_lib->clear();
					$config['image_library']  = 'gd2';
					$config['source_image']   = $newFilePath;
					$config['new_image']      = 'small_' . $filename;
					$config['maintain_ratio'] = true;
					$config['width']          = hooks()->apply_filters('staff_profile_image_small_width', 32);
					$config['height']         = hooks()->apply_filters('staff_profile_image_small_height', 32);
					$CI->image_lib->initialize($config);
					$CI->image_lib->resize();
					$CI->db->where('staffid', $staff_id);
					$CI->db->update(db_prefix().'staff', [
						'profile_image' => $filename,
					]);
					// Remove original image
					unlink($newFilePath);

					return true;
				}
			}
		}

		return false;
	}


	/**
	 * list hr profile permisstion
	 * @return [type] 
	 */
	function list_hr_profile_permisstion()
	{
		$hr_profile_permissions=[];
		$hr_profile_permissions[]='hrm_dashboard';
		$hr_profile_permissions[]='staffmanage_orgchart';
		$hr_profile_permissions[]='hrm_reception_staff';
		$hr_profile_permissions[]='hrm_hr_records';
		$hr_profile_permissions[]='staffmanage_job_position';
		$hr_profile_permissions[]='staffmanage_training';
		$hr_profile_permissions[]='hr_manage_q_a';
		$hr_profile_permissions[]='hrm_contract';
		$hr_profile_permissions[]='hrm_dependent_person';
		$hr_profile_permissions[]='hrm_procedures_for_quitting_work';
		$hr_profile_permissions[]='hrm_report';
		$hr_profile_permissions[]='hrm_setting';

		return $hr_profile_permissions;
	}

	/**
	 * hr profile get staff id hr permissions
	 * @return [type] 
	 */
	function hr_profile_get_staff_id_hr_permissions()
	{
		$CI = & get_instance();
		$array_staff_id = [];
		$index=0;

		$str_permissions ='';
		foreach (list_hr_profile_permisstion() as $per_key =>  $per_value) {
			if(strlen($str_permissions) > 0){
				$str_permissions .= ",'".$per_value."'";
			}else{
				$str_permissions .= "'".$per_value."'";
			}

		}


		$sql_where = "SELECT distinct staff_id FROM ".db_prefix()."staff_permissions
		where feature IN (".$str_permissions.")
		";
		
		$staffs = $CI->db->query($sql_where)->result_array();

		if(count($staffs)>0){
			foreach ($staffs as $key => $value) {
				$array_staff_id[$index] = $value['staff_id'];
				$index++;
			}
		}
		return $array_staff_id;
	}


	/**
	 * hr profile get staff id dont permissions
	 * @return [type] 
	 */
	function hr_profile_get_staff_id_dont_permissions()
	{
		$CI = & get_instance();

		$CI->db->where('admin != ', 1);

		if(count(hr_profile_get_staff_id_hr_permissions()) > 0){
			$CI->db->where_not_in('staffid', hr_profile_get_staff_id_hr_permissions());
		}
		return $CI->db->get(db_prefix().'staff')->result_array();
		
	}


	/**
	 * hr profile handle contract attachments array
	 * @param  [type] $contractid 
	 * @param  string $index_name 
	 * @return [type]             
	 */
	function hr_profile_handle_contract_attachments_array($contractid, $index_name = 'attachments')
	{
		$uploaded_files = [];
		$path           = get_hr_profile_upload_path_by_type('staff_contract').$contractid .'/';
		$CI             = &get_instance();
		
		if (isset($_FILES[$index_name]['name'])
			&& ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)) {
			if (!is_array($_FILES[$index_name]['name'])) {
				$_FILES[$index_name]['name']     = [$_FILES[$index_name]['name']];
				$_FILES[$index_name]['type']     = [$_FILES[$index_name]['type']];
				$_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
				$_FILES[$index_name]['error']    = [$_FILES[$index_name]['error']];
				$_FILES[$index_name]['size']     = [$_FILES[$index_name]['size']];
			}

				_file_attachments_index_fix($index_name);
				for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
					// Get the temp file path
					$tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

					// Make sure we have a filepath
					if (!empty($tmpFilePath) && $tmpFilePath != '') {
						if (_perfex_upload_error($_FILES[$index_name]['error'][$i])
							|| !_upload_extension_allowed($_FILES[$index_name]['name'][$i])) {
							continue;
					}

					_maybe_create_upload_path($path);
					$filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
					$newFilePath = $path . $filename;

						// Upload the file into the temp dir
					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						array_push($uploaded_files, [
							'file_name' => $filename,
							'filetype'  => $_FILES[$index_name]['type'][$i],
						]);
						if (is_image($newFilePath)) {
							create_img_thumb($path, $filename);
						}
					}
				}
			}
		}

		if (count($uploaded_files) > 0) {
			return $uploaded_files;
		}

		return false;
	}


	/**
	 * get job name
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function get_job_name($id)
	{   
		$job_name ='';

		$CI             = &get_instance();

		if($id != 0 && $id != ''){
			$CI->db->where('job_id',$id);
			$hr_job_p = $CI->db->get(db_prefix().'hr_job_p')->row();

			if($hr_job_p){
				$job_name .= $hr_job_p->job_name;
			}
		}
		return $job_name;
	}


	/**
	 * get department from strings
	 * @param  [type] $string_ids 
	 * @return [type]             
	 */
	function get_department_from_strings($string_ids, $department_on_line)
	{   
		$CI             = &get_instance();
		$list_department_name ='';

		// get department
		if($string_ids != null && $string_ids != ''){
			$department_ids       = explode(',', $string_ids);
			$str = '';
			$j = 0;
			foreach ($department_ids as $key => $department_id) {
				$department_name ='';
				$member   = hr_profile_get_department_name($department_id);

				if($member){
					$department_name .= $member->name;
				}

				$j++;
				$str .= '<span class="label label-tag tag-id-1"><span class="tag">'.$department_name.'</span><span class="hide">, </span></span>&nbsp';

				if($j%$department_on_line == 0){
					$str .= '<br><br/>';
				}

			}
			$list_department_name = $str;
		}else{
			$list_department_name = '';
		}

		return $list_department_name;

	}


	/**
	 * hr profile get kb groups
	 * @return [type] 
	 */
	function hr_profile_get_kb_groups()
	{
		$CI = & get_instance();

		return $CI->db->get(db_prefix() . 'hr_knowledge_base_groups')->result_array();
	}


	/**
	 * hr profile get all knowledge base articles grouped
	 * @param  boolean $only_customers 
	 * @param  array   $where          
	 * @return [type]                  
	 */
	function hr_profile_get_all_knowledge_base_articles_grouped($only_customers = true, $where = [])
	{
		$CI = & get_instance();
		$CI->load->model('knowledge_base_q_a_model');
		$groups = $CI->knowledge_base_q_a_model->get_kbg('', 1);
		$i      = 0;
		foreach ($groups as $group) {
			$CI->db->select('slug,subject,description,' . db_prefix() . 'hr_knowledge_base.active as active_article,articlegroup,articleid,staff_article');
			$CI->db->from(db_prefix() . 'hr_knowledge_base');
			$CI->db->where('articlegroup', $group['groupid']);
			$CI->db->where('active', 1);
			if ($only_customers == true) {
				$CI->db->where('staff_article', 0);
			}
			$CI->db->where($where);
			$CI->db->order_by('article_order', 'asc');
			$articles = $CI->db->get()->result_array();
			if (count($articles) == 0) {
				unset($groups[$i]);
				$i++;

				continue;
			}
			$groups[$i]['articles'] = $articles;
			$i++;
		}

		return array_values($groups);
	}

	/**
	 * hr profile handle kb article files upload
	 * @param  string $articleid  
	 * @param  string $index_name 
	 * @return [type]             
	 */
	function hr_profile_handle_kb_article_files_upload($articleid = '', $index_name = 'kb_article_files')
	{
		$path           = get_hr_profile_upload_path_by_type('kb_article_files') . $articleid . '/';
		$uploaded_files = [];
		if (isset($_FILES[$index_name])) {
			_file_attachments_index_fix($index_name);
			// Get the temp file path
			$tmpFilePath = $_FILES[$index_name]['tmp_name'];
			// Make sure we have a filepath
			if (!empty($tmpFilePath) && $tmpFilePath != '') {
				// Getting file extension
				$extension = strtolower(pathinfo($_FILES[$index_name]['name'], PATHINFO_EXTENSION));

				$allowed_extensions = explode(',', get_option('ticket_attachments_file_extensions'));
				$allowed_extensions = array_map('trim', $allowed_extensions);
				// Check for all cases if this extension is allowed
				
				_maybe_create_upload_path($path);
				$filename    = unique_filename($path, $_FILES[$index_name]['name']);
				$newFilePath = $path . $filename;
				
				// Upload the file into the temp dir
				if (move_uploaded_file($tmpFilePath, $newFilePath)) {
					$CI                       = & get_instance();

					$CI->db->insert(db_prefix().'files', [
						'rel_id' => $articleid,
						'rel_type' => 'hr_profile_kb_article',
						'file_name' => $_FILES['kb_article_files']['name'],
						'filetype' => $_FILES['kb_article_files']['type'],
						'staffid' => get_staff_user_id()
					]);
					return true;
				}
			}
		}

		return false;
	}


	/**
	 * hr profile get workplace name
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function hr_profile_get_workplace_name($id){
		$CI             = &get_instance();
		$CI->db->where('id', $id);
		$CI->db->select('name');
		$workplace = $CI->db->get(db_prefix().'hr_workplace')->row();

		if($workplace){
			return $workplace->name; 
		}else{
			return ''; 
		} 
	}


	/**
	 * hr profile get job position name
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function hr_profile_get_job_position_name($id){
		$CI             = &get_instance();
		$CI->db->where('position_id', $id);
		$CI->db->select('position_name');
		$job_position = $CI->db->get(db_prefix().'hr_job_position')->row();

		if($job_position){
			return $job_position->position_name; 
		}else{
			return ''; 
		} 
	}


	/**
	 * hr profile get hr_code
	 * @param  [type] $staff_id 
	 * @return [type]           
	 */
	function hr_profile_get_hr_code($staff_id){
		$CI             = &get_instance();
		$CI->db->where('staffid', $staff_id);
		$CI->db->select('staff_identifi');
		$staff = $CI->db->get(db_prefix().'staff')->row();

		if($staff){
			return $staff->staff_identifi; 
		}else{
			return ''; 
		} 
	}


	/**
	 * hr get staff email by id
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function hr_get_staff_email_by_id($id)
	{
		$CI = & get_instance();

		$staff = $CI->app_object_cache->get('staff-email-by-id-' . $id);

		if (!$staff) {
			$CI->db->where('staffid', $id);
			$staff = $CI->db->select('email')->from(db_prefix() . 'staff')->get()->row();
			$CI->app_object_cache->add('staff-email-by-id-' . $id, $staff);
		}

		return ($staff ? $staff->email : '');
	}


	/**
	 * hr get training hash
	 * @param  [type] $training_id 
	 * @return [type]              
	 */
	function hr_get_training_hash($training_id)
	{
		$hash = '';
		$CI = & get_instance();
		$CI->db->where('training_id', $training_id);
		$training = $CI->db->get(db_prefix().'hr_position_training')->row();

		if($training){
			$hash .= $training->hash;
		}
		return $hash;
	}

	/**
	 * hr profile type of training exists
	 * @param  [type] $name 
	 * @return [type]       
	 */
	function hr_profile_type_of_training_exists($name){
		$CI = & get_instance();
		$i = count($CI->db->query('Select * from '.db_prefix().'hr_type_of_trainings where name = '.$name)->result_array());
		if($i == 0){
			return 0;
		}
		if($i > 0){
			return 1;
		}
	}


	/**
	 * get type of training by id
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function get_type_of_training_by_id($id)
	{
		$type_of_training_name ='';

		if(is_numeric($id)){

			$CI = & get_instance();
			$CI->db->where('id', $id);
			$type_of_training = $CI->db->get(db_prefix().'hr_type_of_trainings')->row();
			if($type_of_training){
				$type_of_training_name .= $type_of_training->name;
			}
		}

		return $type_of_training_name;
	}

	/**
	 * get training library name
	 * @param  [type] $ids 
	 * @return [type]      
	 */
	function get_training_library_name($ids)
	{
		$training_name='';

		$CI = & get_instance();
		$CI->db->where('training_id IN ('. $ids.') ');
		$hr_position_training = $CI->db->get(db_prefix().'hr_position_training')->result_array();
		foreach ($hr_position_training  as $value) {
			if(strlen($training_name) > 0){
				$training_name .=', '.$value['subject'];
			}else{
				$training_name .=$value['subject'];
			}
		}
		return $training_name;

	}

	/**
	 * hr get list staff name
	 * @param  [type] $ids 
	 * @return [type]      
	 */
	function hr_get_list_staff_name($ids)
	{
	    $staff_name='';

		$CI = & get_instance();

		if(strlen($ids) > 0){

			$CI->db->where('staffid IN ('. $ids.') ');
			$staffs = $CI->db->get(db_prefix().'staff')->result_array();
			foreach ($staffs  as $value) {
				if(strlen($staff_name) > 0){
					$staff_name .=', '.$value['firstname'].' '.$value['lastname'];
				}else{
					$staff_name .= $value['firstname'].' '.$value['lastname'];
				}
			}
		}

		return $staff_name;

	}

	/**
	 * hr get list job position name
	 * @param  [type] $ids 
	 * @return [type]      
	 */
	function hr_get_list_job_position_name($ids)
	{
		$position_names='';
		$CI = & get_instance();

		if(strlen($ids) > 0){

			$CI->db->where('position_id IN ('. $ids.') ');
			$CI->db->select('position_name');
			$job_position = $CI->db->get(db_prefix().'hr_job_position')->result_array();

			foreach ($job_position  as $value) {
				if(strlen($position_names) > 0){
					$position_names .=', '.$value['position_name'];
				}else{
					$position_names .= $value['position_name'];
				}
			}
		} 
		return $position_names;
	}

	/**
	 * hr contract pdf
	 * @param  [type] $contract 
	 * @return [type]           
	 */
	function hr_contract_pdf($contract)
	{
		return app_pdf('contract',  module_dir_path(HR_PROFILE_MODULE_NAME, 'libraries/pdf/Hr_contract_pdf'), $contract);
	}

	/**
	 * hr get contract type
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function hr_get_contract_type($id)
	{
		$name='';

		$CI = & get_instance();
		$CI->db->where('id_contracttype', $id);
		$contract_type = $CI->db->get(db_prefix().'hr_staff_contract_type')->row();

		if($contract_type){
			$name .= $contract_type->name_contracttype;
		}

		return $name;
	}

	/**
	 * hr get role name
	 * @param  [type] $ids 
	 * @return [type]      
	 */
	function hr_get_role_name($id)
	{
		$roles_names='';
		$CI = & get_instance();


		$CI->db->where('roleid', $id);
		$CI->db->select('name');
		$roles = $CI->db->get(db_prefix().'roles')->row();

		if($roles){
			$roles_names .= $roles->name;
		}
		return $roles_names;
	}

	/**
	 * get staff department names
	 * @param  [type] $staffid 
	 * @return [type]          
	 */
	function get_staff_department_names($staffid)
	{
		$list_department='';
		$CI = & get_instance();

		$arr_department = $CI->hr_profile_model->get_staff_departments($staffid, true);

		if(count($arr_department) > 0){

			foreach ($arr_department as $key => $department) {
				$department_value   = $CI->departments_model->get($department);

				if($department_value){
					if(strlen($list_department) != 0){
						$list_department .= ';'.$department_value->name;
					}else{
						$list_department .= $department_value->name;
					}
				}
			}
		}

		return $list_department;

	}

	/**
	 * hr render salary table
	 * @param  [type] $contract_id 
	 * @return [type]              
	 */
	function hr_render_salary_table($contract_id)
	{
		$table='';

		$CI = & get_instance();
		$contract_details = $CI->hr_profile_model->get_contract_detail($contract_id);

		$table  .= '<table border="1" style="width:100%;height:55px;">';
		$table  .= '<tbody>';
		$table  .= '<tr style="height:27px;">';
		$table  .= '<td style="width:25%;height:27px;text-align:left;"><strong>'._l('hr_hr_contract_rel_type').'</strong></td>';
		$table  .= '<td style="width:25%;height:27px;"><strong>'._l('hr_hr_contract_rel_value').'</strong></td>';
		$table  .= '<td style="width:25%;height:27px;"><strong>'._l('hr_start_month').'</strong></td>';
		$table  .= '<td style="width:25%;height:27px;"><strong>'._l('note').'</strong></td>';
		$table  .= '</tr>';

		foreach($contract_details as $contract_detail){


			$type_name ='';
			if(preg_match('/^st_/', $contract_detail['rel_type'])){
				$rel_value = str_replace('st_', '', $contract_detail['rel_type']);
				$salary_type = $CI->hr_profile_model->get_salary_form($rel_value);

				$type = 'salary';
				if($salary_type){
					$type_name = $salary_type->form_name;
				}

			}elseif(preg_match('/^al_/', $contract_detail['rel_type'])){
				$rel_value = str_replace('al_', '', $contract_detail['rel_type']);
				$allowance_type = $CI->hr_profile_model->get_allowance_type($rel_value);

				$type = 'allowance';
				if($allowance_type){
					$type_name = $allowance_type->type_name;
				}
			}



			$table .= '<tr style="height:28px;">';
			$table .= '<td style="width:25%;height:28px;"><span>'.$type_name.'</span></td>';
			$table .= '<td style="width:25%;height:28px;"><span>'.app_format_money((float)$contract_detail['rel_value'],'').'</span></td>';
			$table .= '<td style="width:25%;height:28px;"><span>'._d($contract_detail['since_date']).'</span></td>';
			$table .= '<td style="width:25%;height:28px;">'.$contract_detail['contract_note'].'</td>';
			$table .= '</tr>';

		}

		$table .= '</tbody>';
		$table .= '</table>';

		return $table;
	}

	/**
	 * hr process digital signature image
	 * @param  [type] $partBase64 
	 * @param  [type] $path       
	 * @return [type]             
	 */
	function hr_process_digital_signature_image($partBase64, $path)
	{
		if (empty($partBase64)) {
			return false;
		}

		_maybe_create_upload_path($path);
		$filename = unique_filename($path, 'staff_signature.png');

		$decoded_image = base64_decode($partBase64);

		$retval = false;

		$path = rtrim($path, '/') . '/' . $filename;

		$fp = fopen($path, 'w+');

		if (fwrite($fp, $decoded_image)) {
			$retval                                 = true;
			$GLOBALS['processed_digital_signature'] = $filename;
		}

		fclose($fp);

		return $retval;
	}

	/**
	 * hr profile check hide menu
	 * @return [type] 
	 */
	function hr_profile_check_hide_menu()
	{
		$hide_menu = false;
		if(get_option('hr_profile_hide_menu')){
			$CI             = &get_instance();
			$CI->db->where('staffid', get_staff_user_id());
			$staff = $CI->db->get(db_prefix().'staff')->row();
			if($staff){
				if($staff->is_not_staff == 1){
					$hide_menu = true;
				}
			}
		}

		return $hide_menu;
	}