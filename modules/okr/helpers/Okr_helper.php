<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * get role name staff 
 * @param  integer $staffid
 * @return $role_name         
 */
function get_role_name_staff($staffid){
	$CI = & get_instance(); 
	$CI->load->model('staff_model');
	$CI->load->model('roles_model');
	$staff = $CI->staff_model->get($staffid);  
	$role_name = ''; 

	if(!is_null($staff) || $staff){
		if ($staff->role != '' && !is_null($staff->role) && $staff->role != 0) {
			return $role_name = isset($CI->roles_model->get($staff->role)->name) ? $CI->roles_model->get($staff->role)->name : '' ;    
		}
	}
	return  $role_name;    
}

/**
 * [render_js_variables description]
 * @return [type] [description]
 */
function render_js_variables()
{
    $lang = [
        'switch_to_chart_okr'                      => _l('switch_to_chart_okr'),
        'switch_to_tree_grid'                      => _l('switch_to_tree_grid'),
        'upcoming_checkin_alert'                      => _l('upcoming_checkin_alert'),
     ];
    $script = ''; 
    $script .= '<script>';


    $script .= 'var apps = {};';
    $script .= 'var apps = {};';

    $script .= 'apps.lang = {};';

    foreach ($lang as $key => $val) {
        $script .= 'apps.lang. ' . $key . ' = "' . $val . '";';
    }



    $script .= 'var appsLang = {};';
    foreach ($lang as $key => $val) {
        $script .= 'appsLang["' . $key . '"] = "' . $val . '";';
    }

    $script .= '</script>';
    echo html_entity_decode($script);
}

function unit_name($id){
	$CI = & get_instance(); 
	$CI->load->model('okr_model');
	if(!$id){
		return '';
	}
	return $CI->okr_model->get_unit($id)->unit;    

}

/**
 * Check for ticket attachment after inserting ticket to database
 * @param  mixed $okr_id
 * @return mixed           false if no attachment || array uploaded attachments
 */
function handle_okrs_attachments($okr_id, $index_name = 'attachments')
{

    $path = OKR_MODULE_UPLOAD_FOLDER .'/okrs_attachments/'. $okr_id . '/';
    $uploaded_files = [];
    if($_FILES[$index_name]['name'][0] != ""){
        if (isset($_FILES[$index_name])) {
            for ($i = 0; $i < count($_FILES[$index_name]); $i++) {
                if ($i <= get_option('maximum_allowed_okrs_attachments')) {
                    // Get the temp file path
                    $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

                    // Make sure we have a filepath
                    if (!empty($tmpFilePath) && $tmpFilePath != '') {
                        // Getting file extension
                        $extension = strtolower(pathinfo($_FILES[$index_name]['name'][$i], PATHINFO_EXTENSION));
                        $allowed_extensions = explode(',', get_ticket_form_accepted_mimes());
                        $allowed_extensions = array_map('trim', $allowed_extensions);

                        // Check for all cases if this extension is allowed
                        if (!in_array('.' . $extension, $allowed_extensions)) {
                            continue;
                        }
                        _maybe_create_upload_path($path);
                        $filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
                        $newFilePath = $path . $filename;
                        // Upload the file into the temp dir
                        if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                            $CI           = & get_instance();
                            $attachment = [];
                            $attachment[] = [
                                'file_name' => $filename,
                                'filetype'  => $_FILES[$index_name]['type'][$i],
                                ];
                            $CI->misc_model->add_attachment_to_database($okr_id,$index_name, $attachment);
                        }
                    }
                }
            }

        }
        if (count($attachment) > 0) {
            return $attachment;
        }
    }

    return false;
}

/**
 * Gets the okrs file.
 *
 * @param    $okrs  The id
 *
 * @return   The okrs file.
 */
function get_okrs_attachment($id){
    $CI = & get_instance();
    $CI->db->where('rel_id',$id);
    $CI->db->where('rel_type','attachments');
    return $CI->db->get(db_prefix().'files')->result_array();
}

/**
 * get department name of okrs
 * @param  $departmentid
 * @return name             
 */
function get_department_name_of_okrs($departmentid){
    $CI = & get_instance();
    $CI->db->select('name');
    $CI->db->where('departmentid',$departmentid);
    return $CI->db->get(db_prefix().'departments')->row();
}
/**
 * [circulation_date description]
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function circulation_date($id){
    $CI = & get_instance();
    $circulation = $CI->db->get(db_prefix().'okr_setting_circulation')->row();
    $circulation = $circulation->name_circulation .'('. $circulation->from_date .' - '. $circulation->to_date .')';
    return $circulation;
}
/**
 * [progress_template description]
 * @param  [type] $progress [description]
 * @return [type]           [description]
 */
function progress_template($progress){
    if(is_null($progress)){
        $progress = 0;
    }
    $rattings = '
        <div class="progress no-margin progress-bar-mini cus_tran">
                  <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar" aria-valuenow="'.$progress.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$progress.'%;" data-percent="'.$progress.'">
                  </div>
               </div>
               '.$progress.'%
       </div>
        ';  
    return $rattings;
}
/**
 * [okr_name description]
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function okr_name($id){
    $CI = & get_instance();
    $CI->db->where('id',$id);
    $okr = $CI->db->get(db_prefix().'okrs')->row();
    $name = isset($okr->your_target) ? $okr->your_target : '';
    return $name;
}
/**
 * [category_view description]
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function category_view($id){
    $CI = & get_instance();
    $CI->db->where('id',$id);
    $category = $CI->db->get(db_prefix().'okr_setting_category')->row();
    return $category = isset($category->category) ? $category->category : '';
}

/**
 * OKRs get all staff by department
 * @param  string $departmentid Optional
 * @return array
 */
function okrs_get_all_staff_by_department($departmentid)
{
    $CI = & get_instance();
    if ($departmentid) {
        $CI->db->where('departmentid', $departmentid);
        $staffids = $CI->db->select('staffid')->from(db_prefix() . 'staff_departments')->get()->result_array();
    }else{
        $staffids = [];
    }
    return $staffids;
}