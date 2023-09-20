<?php
defined('BASEPATH') or exit('No direct script access allowed');


/**
 * Check whether column exists in a table
 * Custom function because Codeigniter is caching the tables and this is causing issues in migrations
 * @param  string $column column name to check
 * @param  string $table table name to check
 * @return boolean
 */
function handle_rec_proposal_file($id)
{

    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {

        hooks()->do_action('before_upload_contract_attachment', $id);
        $path = RECRUITMENT_MODULE_UPLOAD_FOLDER .'/proposal/'. $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['file']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI           = & get_instance();
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file']['type'],
                    ];
                $CI->misc_model->add_attachment_to_database($id, 'rec_proposal', $attachment);

                return true;
            }
        }
    }

    return false;
}

/**
 * reformat currency rec
 * @param  string $value
 * @return string
 */
function reformat_currency_rec($value)
{
    return str_replace(',','', $value);
}

/**
 * get rec dpm name
 * @param  int $id
 * @return string
 */
function get_rec_dpm_name($id){
    $CI           = & get_instance();
    if($id != 0){
        $CI->db->where('departmentid',$id);
        $dpm = $CI->db->get(db_prefix().'departments')->row();
        if($dpm->name){
            return $dpm->name;
        }else{
            return '';
        }
        
    }else{
        return '';
    }
}

/**
 * get rec position name
 * @param  int $id
 * @return string
 */
function get_rec_position_name($id){
    $CI           = & get_instance();
    if($id != 0){
        $CI->db->where('position_id',$id);
        $dpm = $CI->db->get(db_prefix().'rec_job_position')->row();
        if($dpm->position_name){
            return $dpm->position_name;
        }else{
            return '';
        }
        
    }else{
        return '';
    }
}

/**
 * handle rec campaign file
 * @param  int $id 
 * @return bool
 */
function handle_rec_campaign_file($id){
     if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {

        hooks()->do_action('before_upload_contract_attachment', $id);
        $path = RECRUITMENT_MODULE_UPLOAD_FOLDER .'/campaign/'. $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['file']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI           = & get_instance();
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file']['type'],
                    ];
                $CI->misc_model->add_attachment_to_database($id, 'rec_campaign', $attachment);

                return true;
            }
        }
    }

    return false;
}

/**
 * handle rec candidate file
 * @param  int $id
 * @return bool
 */
function handle_rec_candidate_file($id){
     if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {

        hooks()->do_action('before_upload_contract_attachment', $id);
        $path = RECRUITMENT_MODULE_UPLOAD_FOLDER .'/candidate/files/'. $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['file']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI           = & get_instance();
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file']['type'],
                    ];
                $CI->misc_model->add_attachment_to_database($id, 'rec_cadidate_file', $attachment);

                return true;
            }
        }
    }
    return false;
}

/**
 * handle rec candidate avar file
 * @param  int $id
 * @return bool   
 */
function handle_rec_candidate_avar_file($id){

    if (isset($_FILES['cd_avar']['name']) && $_FILES['cd_avar']['name'] != '') {
        
        hooks()->do_action('before_upload_contract_attachment', $id);
        $path = RECRUITMENT_MODULE_UPLOAD_FOLDER .'/candidate/avartar/'. $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['cd_avar']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['cd_avar']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI           = & get_instance();
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['cd_avar']['type'],
                    ];
                $CI->misc_model->add_attachment_to_database($id, 'rec_cadidate_avar', $attachment);

                return true;
            }
        }
    }

    return false;
}

/**
 * get rec campaign hp
 * @param  string $id
 * @return string
 */
function get_rec_campaign_hp($id = ''){
    $CI           = & get_instance();
    if($id != ''){
        $CI->db->where('cp_id', $id);
        return $CI->db->get(db_prefix().'rec_campaign')->row();
    }elseif ($id == '') {
        return $CI->db->get(db_prefix().'rec_campaign')->result_array();
    }
}

/**
 * get status candidate
 * @param  int $status
 * @return string
 */
function get_status_candidate($status){
    $result = '';
    if($status == 1){
        $result = '<span class="label label inline-block project-status-'.$status.' application-style"> '._l('application').' </span>';
    }elseif($status == 2){
        $result = '<span class="label label inline-block project-status-'.$status.' potential-style"> '._l('potential').' </span>';
    }elseif($status == 3){
        $result = '<span class="label label inline-block project-status-'.$status.' interview-style"> '._l('interview').' </span>';
    }elseif($status == 4){
        $result = '<span class="label label inline-block project-status-'.$status.' won_interview-style"> '._l('won_interview').' </span>';
    }elseif($status == 5){
        $result = '<span class="label label inline-block project-status-'.$status.' send_offer-style"> '._l('send_offer').' </span>';
    }elseif($status == 6){
        $result = '<span class="label label inline-block project-status-'.$status.' elect-style"> '._l('elect').' </span>';
    }elseif($status == 7){
        $result = '<span class="label label inline-block project-status-'.$status.' non_elect-style"> '._l('non_elect').' </span>';
    }elseif($status == 8){
        $result = '<span class="label label inline-block project-status-'.$status.' unanswer-style"> '._l('unanswer').' </span>';
    }elseif($status == 9){
        $result = '<span class="label label inline-block project-status-'.$status.' transferred-style"> '._l('transferred').' </span>';
    }elseif($status == 10){
        $result = '<span class="label label inline-block project-status-'.$status.' freedom-style"> '._l('freedom').' </span>';
    }

    return $result;
}

/**
 * candidate profile image
 * @param  int $id     
 * @param  array  $classes
 * @param  string $type   
 * @param  array  $img_attrs
 * @return string
 */
function candidate_profile_image($id, $classes = ['staff-profile-image'], $type = 'small', $img_attrs = [])
{
    $CI           = & get_instance();
    $url = base_url('assets/images/user-placeholder.jpg');
    $_attributes = '';
    foreach ($img_attrs as $key => $val) {
        $_attributes .= $key . '=' . '"' . html_escape($val) . '" ';
    }

    $blankImageFormatted = '<img src="' . $url . '" ' . $_attributes . ' class="' . implode(' ', $classes) . '" />';

    $CI->db->where('rel_id',$id);
    $CI->db->where('rel_type','rec_cadidate_avar');
    $result = $CI->db->get(db_prefix().'files')->row();  

    if (!$result) {
        return $blankImageFormatted;
    }

    if ($result && $result->file_name !== null) {
        $profileImagePath = RECRUITMENT_PATH.'candidate/avartar/'.$id.'/'.$result->file_name;
        if (file_exists($profileImagePath)) {
            $profile_image = '<img ' . $_attributes . ' src="' . site_url($profileImagePath) . '" class="' . implode(' ', $classes) . '" />';
        } else {
            return $blankImageFormatted;
        }
    } else {
        $profile_image = '<img src="' . $url . '" ' . $_attributes . ' class="' . implode(' ', $classes) . '" />';
    }

    return $profile_image;
}

/**
 * get candidate name
 * @param  int $id
 * @return string
 */
function get_candidate_name($id){
    $CI           = & get_instance();
    $CI->db->where('id',$id);
    $candidate = $CI->db->get(db_prefix().'rec_candidate')->row();
    if($candidate && $candidate->candidate_name != ''){
        return $candidate->candidate_name.' '.$candidate->last_name;
    }else{
        return '';
    }
}

/**
 * get candidate interview
 * @param  int $id
 * @return 
 */
function get_candidate_interview($id){
    $CI           = & get_instance();
    $CI->db->where('interview',$id);
    $data_rs = array();
    $cdinterview = $CI->db->get(db_prefix().'cd_interview')->result_array();
    
    foreach($cdinterview as $cd){
        $data_rs[] = $cd['candidate'];
    }

    return $data_rs;
}

/**
 * count criteria
 * @param  int $id
 * @return int
 */
function count_criteria($id){
    $CI           = & get_instance();
    $CI->db->where('evaluation_form',$id);
    $list = $CI->db->get(db_prefix().'rec_list_criteria')->result_array();

    return count($list);
}

/**
 * get criteria name
 * @param  int $id
 * @return string
 */
function get_criteria_name($id){
    $CI           = & get_instance();
    $CI->db->where('criteria_id',$id);
    $CI->db->select('criteria_title');
    $list = $CI->db->get(db_prefix().'rec_criteria')->row();
    if($list->criteria_title){
        return $list->criteria_title;
    }else{
        return '';
    }
    
}

/**
 * handle rec set transfer record
 * @param  int $id
 * @return bool
 */
function handle_rec_set_transfer_record($id){

    if (isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'] != '') {
        hooks()->do_action('before_upload_contract_attachment', $id);
        $path = RECRUITMENT_MODULE_UPLOAD_FOLDER .'/set_transfer/'. $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['attachment']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['attachment']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI           = & get_instance();
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['attachment']['type'],
                    ];
                $CI->misc_model->add_attachment_to_database($id, 'rec_set_transfer', $attachment);

                return true;
            }
        }
    }

    return false;
}

/**
 * Gets the staff email by identifier.
 *
 * @param      int   $id     The identifier
 *
 * @return     String  The staff email by identifier.
 */
function get_staff_email_by_id_rec($id)
{
    $CI           = & get_instance();
    $CI->db->where('staffid', $id);
    $staff = $CI->db->select('email')->from(db_prefix() . 'staff')->get()->row();

    return ($staff ? $staff->email : '');
}


/**
 * [handle rec candidate file form description]
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function handle_rec_candidate_file_form($id){
     if (isset($_FILES['file-input']['name']) && $_FILES['file-input']['name'] != '') {

        hooks()->do_action('before_upload_contract_attachment', $id);
        $path = RECRUITMENT_MODULE_UPLOAD_FOLDER .'/candidate/files/'. $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file-input']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['file-input']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI           = & get_instance();
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file-input']['type'],
                    ];
                $CI->misc_model->add_attachment_to_database($id, 'rec_cadidate_file', $attachment);

                return true;
            }
        }
    }
    return false;
}

/**
 * Format html task assignees
 * This function is used to save up on query
 * @param  string $ids   string coma separated assignee staff id
 * @param  string $names compa separated in the same order like assignee ids
 * @return string
 */
function format_members_by_ids_and_names_recruitment($ids, $names, $hidden_export_table = true, $image_class = 'staff-profile-image-small')
{
    $outputAssignees = '';
    $exportAssignees = '';

    $assignees   = explode(',', $names);
    $assigneeIds = explode(',', $ids);
    foreach ($assignees as $key => $assigned) {
        $assignee_id = $assigneeIds[$key];
        $assignee_id = trim($assignee_id);
        if ($assigned != '') {
            $outputAssignees .= '<a href="' . admin_url('profile/' . $assignee_id) . '">' .
                staff_profile_image($assignee_id, [
                  $image_class . ' mright5',
                ], 'small', [
                  'data-toggle' => 'tooltip',
                  'data-title'  => $assigned,
                ]) . '</a>';
            $exportAssignees .= $assigned . ', ';
        }
    }

    if ($exportAssignees != '') {
        $outputAssignees .= '<span class="hide">' . mb_substr($exportAssignees, 0, -2) . '</span>';
    }

    return $outputAssignees;
}

/**
 * get_kan ban status candidate color
 * @param  integer  $status 
 * @param  boolean $name   
 * @return string         
 */
function get_kan_ban_status_candidate_color($status, $name = false){
    $result = '';
    if($name == false){
        if($status == 1){
            $result = 'application-style';
        }elseif($status == 2){
            $result = 'potential-style';
        }elseif($status == 3){
            $result = 'interview-style';
        }elseif($status == 4){
            $result = 'won_interview-style';
        }elseif($status == 5){
            $result = 'send_offer-style';
        }elseif($status == 6){
            $result = 'elect-style';
        }elseif($status == 7){
            $result = 'non_elect-style';
        }elseif($status == 8){
            $result = 'unanswer-style';
        }elseif($status == 9){
            $result = 'transferred-style';
        }elseif($status == 10){
            $result = 'freedom-style';
        }
    }else{
        if($status == 1){
            $result = _l('application');
        }elseif($status == 2){
            $result = _l('potential');
        }elseif($status == 3){
            $result = _l('interview');
        }elseif($status == 4){
            $result = _l('won_interview');
        }elseif($status == 5){
            $result = _l('send_offer');
        }elseif($status == 6){
            $result = _l('elect');
        }elseif($status == 7){
            $result = _l('non_elect');
        }elseif($status == 8){
            $result = _l('unanswer');
        }elseif($status == 9){
            $result = _l('transferred');
        }elseif($status == 10){
            $result = _l('freedom');
        }
    }

    return $result;
}

/**
 * Used for customer forms eq. leads form, builded from the form builder plugin
 * @param  object $field field from database
 * @return mixed
 */
function render_form_builder_field_recruitment($field)
{

    $type         = $field->type;
    $classNameCol = 'col-md-12';
    if (isset($field->className)) {
        if (strpos($field->className, 'form-col') !== false) {
            $classNames = explode(' ', $field->className);
            if (is_array($classNames)) {
                $classNameColArray = array_filter($classNames, function ($class) {
                    return startsWith($class, 'form-col');
                });

                $classNameCol = implode(' ', $classNameColArray);
                $classNameCol = trim($classNameCol);

                $classNameCol = str_replace('form-col-xs', 'col-xs', $classNameCol);
                $classNameCol = str_replace('form-col-sm', 'col-sm', $classNameCol);
                $classNameCol = str_replace('form-col-md', 'col-md', $classNameCol);
                $classNameCol = str_replace('form-col-lg', 'col-lg', $classNameCol);

                // Default col-md-X
                $classNameCol = str_replace('form-col', 'col-md', $classNameCol);
            }
        }
    }

    echo '<div class="' . $classNameCol . '">';
    if ($type == 'header' || $type == 'paragraph') {
        echo '<' . $field->subtype . ' class="' . (isset($field->className) ? $field->className : '') . '">' . check_for_links(nl2br($field->label)) . '</' . $field->subtype . '>';
    } else {
        echo '<div class="form-group" data-type="' . $type . '" data-name="' . $field->name . '" data-required="' . (isset($field->required) ? true : 'false') . '">';
        echo '<label class="control-label" for="' . $field->name . '">' . (isset($field->required) ? ' <span class="text-danger">* </span> ': '') . $field->label . '' . (isset($field->description) ? ' <i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . $field->description . '" data-placement="' . (is_rtl(true) ? 'left' : 'right') . '"></i>' : '') . '</label>';
        if (isset($field->subtype) && $field->subtype == 'color') {
            echo '<div class="input-group colorpicker-input">
     <input' . (isset($field->required) ? ' required="true"': '') . ' placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '" type="text"' . (isset($field->value) ? ' value="' . $field->value . '"' : '') . ' name="' . $field->name . '" id="' . $field->name . '" class="' . (isset($field->className) ? $field->className : '') . '" />
         <span class="input-group-addon"><i></i></span>
     </div>';
        } elseif (($type == 'file' || $type == 'text' || $type == 'number') && ($field->name != 'skill')) {
            $ftype = isset($field->subtype) ? $field->subtype : $type;
            echo '<input' . (isset($field->required) ? ' required="true"': '') . (isset($field->placeholder) ? ' placeholder="' . $field->placeholder . '"' : '') . ' type="' . $ftype . '" name="' . $field->name . '" id="' . $field->name . '" class="' . (isset($field->className) ? $field->className : '') . '" value="' . (isset($field->value) ? $field->value : '') . '"' . ($field->type == 'file' ? ' accept="' . get_form_accepted_mimes() . '" filesize="' . file_upload_max_size() . '"' : '') . '>';
        } elseif ($type == 'textarea') {
            echo '<textarea' . (isset($field->required) ? ' required="true"': '') . ' id="' . $field->name . '" name="' . $field->name . '" rows="' . (isset($field->rows) ? $field->rows : '4') . '" class="' . (isset($field->className) ? $field->className : '') . '" placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '">' . (isset($field->value) ? $field->value : '') . '</textarea>';
        } elseif ($type == 'date') {
            echo '<input' . (isset($field->required) ? ' required="true"': '') . ' placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '" type="text" class="' . (isset($field->className) ? $field->className : '') . ' datepicker" name="' . $field->name . '" id="' . $field->name . '" value="' . (isset($field->value) ? _d($field->value) : '') . '">';
        } elseif ($type == 'datetime-local') {
            echo '<input' . (isset($field->required) ? ' required="true"': '') . ' placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '" type="text" class="' . (isset($field->className) ? $field->className : '') . ' datetimepicker" name="' . $field->name . '" id="' . $field->name . '" value="' . (isset($field->value) ? _dt($field->value) : '') . '">';
        } elseif ($type == 'select') {
            echo '<select' . (isset($field->required) ? ' required="true"': '') . '' . (isset($field->multiple) ? ' multiple="true"' : '') . ' class="' . (isset($field->className) ? $field->className : '') . '" name="' . $field->name . (isset($field->multiple) ? '[]' : '') . '" id="' . $field->name . '"' . (isset($field->values) && count($field->values) > 10 ? 'data-live-search="true"': '') . 'data-none-selected-text="' . (isset($field->placeholder) ? $field->placeholder : '') . '">';
            $values = [];
            if (isset($field->values) && count($field->values) > 0) {
                foreach ($field->values as $option) {
                    echo '<option value="' . $option->value . '" ' . (isset($option->selected) ? ' selected' : '') . '>' . $option->label . '</option>';
                }
            }
            echo '</select>';
        } elseif ($type == 'checkbox-group') {
            $values = [];
            if (isset($field->values) && count($field->values) > 0) {
                $i = 0;
                echo '<div class="chk">';
                foreach ($field->values as $checkbox) {
                    echo '<div class="checkbox' . ((isset($field->inline) && $field->inline == 'true') || (isset($field->className) && strpos($field->className, 'form-inline-checkbox') !== false) ? ' checkbox-inline' : '') . '">';
                    echo '<input' . (isset($field->required) ? ' required="true"': '') . ' class="' . (isset($field->className) ? $field->className : '') . '" type="checkbox" id="chk_' . $field->name . '_' . $i . '" value="' . $checkbox->value . '" name="' . $field->name . '[]"' . (isset($checkbox->selected) ? ' checked' : '') . '>';
                    echo '<label for="chk_' . $field->name . '_' . $i . '">';
                    echo html_entity_decode($checkbox->label);
                    echo '</label>';
                    echo '</div>';
                    $i++;
                }
                echo '</div>';
            }
        }
        echo '</div>';
    }
    echo '</div>';
}


/**
 * row options exist
 * @param  string $name 
 *        
 */
function recruitment_row_options_exist($name){
    $CI = & get_instance();
    $i = count($CI->db->query('Select * from '.db_prefix().'options where name = '.$name)->result_array());
    if($i == 0){
        return 0;
    }
    if($i > 0){
        return 1;
    }
}

/**
 * Gets the recruitment option.
 *
 * @param      <type>        $name   The name
 *
 * @return     array|string  The recruitment option.
 */
function get_recruitment_option($name)
{
    $CI = & get_instance();
    $options = [];
    $val  = '';
    $name = trim($name);
    

    if (!isset($options[$name])) {
        // is not auto loaded
        $CI->db->select('value');
        $CI->db->where('name', $name);
        $row = $CI->db->get(db_prefix() . 'options')->row();
        if ($row) {
            $val = $row->value;
        }
    } else {
        $val = $options[$name];
    }

    return $val;
}

/**
 * handle company attchment
 * @param  integer $id
 * @return array or row
 */
function handle_company_attachments($id)
{

    if (isset($_FILES['file']) && _perfex_upload_error($_FILES['file']['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo _perfex_upload_error($_FILES['file']['error']);
        die;
    }
    $path = RECRUITMENT_COMPANY_UPLOAD . $id . '/';
    $CI   = & get_instance();

    if (isset($_FILES['file']['name'])) {

        // 
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {

            _maybe_create_upload_path($path);
            $filename    = $_FILES['file']['name'];
            $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {

                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file']['type'],
                    ];

                $CI->misc_model->add_attachment_to_database($id, 'rec_company', $attachment);
            }
        }
    }

}

/**
 * get industry name
 * @param  integer $id 
 * @return string     
 */
function get_rec_industry_name($id){

    
    $CI = & get_instance();
    $CI->db->where('id',$id);
    $job_industry = $CI->db->get(db_prefix().'job_industry')->row();

    if($job_industry){
        return $job_industry->industry_name;
    }else{
        return '';
    }

}

/**
 * get company name
 * @param  integer $id 
 * @return string    
 */
function get_rec_company_name($id)
{
    $CI = & get_instance();
    $CI->db->where('id',$id);
    $rec_company = $CI->db->get(db_prefix().'rec_company')->row();

    if($rec_company){
        return $rec_company->company_name;
    }else{
        return '';
    }

}

/*separation portal v1.1.2*/

/**
 * app rec portal head
 * @param  [type] $language 
 * @return [type]           
 */
function app_rec_portal_head($language = null)
{
    // $language param is deprecated
    if (is_null($language)) {
        $language = $GLOBALS['language'];
    }

    if (file_exists(FCPATH . 'assets/css/custom.css')) {
        echo '<link href="' . base_url('assets/css/custom.css') . '" rel="stylesheet" type="text/css" id="custom-css">' . PHP_EOL;
    }

    hooks()->do_action('app_rec_portal_head');
}

/**
 * get template part rec portal
* @param      string   $name    The name
 * @param      array    $data    The data
 * @param      boolean  $return  The return
 *
 * @return     string   The template part.
 */
function get_template_part_rec_portal($name, $data = [], $return = false)
{
    if ($name === '') {
        return '';
    }

    $CI   = & get_instance();
    $path = 'recruitment_portal/template_parts/';

    if ($return == true) {
        return $CI->load->view($path . $name, $data, true);
    }

    $CI->load->view($path . $name, $data);
}

/**
 * init rec_portal area assets.
 */
function init_rec_portal_area_assets()
{
    // Used by themes to add assets
    hooks()->do_action('app_rec_portal_assets');

    hooks()->do_action('app_client_assets_added');
}

/**
 * { register theme rec_portal assets hook }
 *
 * @param      <type>   $function  The function
 *
 * @return     boolean  
 */
function register_theme_rec_portal_assets_hook($function)
{
    if (hooks()->has_action('app_rec_portal_assets', $function)) {
        return false;
    }

    return hooks()->add_action('app_rec_portal_assets', $function, 1);
}

/**
 * { app theme head hook }
 */
function app_theme_rec_portal_head_hook()
{
    $CI = &get_instance();
    ob_start();
    echo get_custom_fields_hyperlink_js_function();

    if (get_option('use_recaptcha_customers_area') == 1
        && get_option('recaptcha_secret_key') != ''
        && get_option('recaptcha_site_key') != '') {
        echo "<script src='https://www.google.com/recaptcha/api.js'></script>";
    }

    $isRTL = (is_rtl_rec(true) ? 'true' : 'false');

    $locale = get_locale_key($GLOBALS['language']);

    $maxUploadSize = file_upload_max_size();

    $date_format = get_option('dateformat');
    $date_format = explode('|', $date_format);
    $date_format = $date_format[0]; ?>
    <script>
        <?php if (is_staff_logged_in()) {
        ?>
        var admin_url = '<?php echo admin_url(); ?>';
        <?php
    } ?>

        var site_url = '<?php echo site_url(''); ?>',
        app = {},
        cfh_popover_templates  = {};

        app.isRTL = '<?php echo html_entity_decode($isRTL); ?>';
        app.is_mobile = '<?php echo is_mobile(); ?>';
        app.months_json = '<?php echo json_encode([_l('January'), _l('February'), _l('March'), _l('April'), _l('May'), _l('June'), _l('July'), _l('August'), _l('September'), _l('October'), _l('November'), _l('December')]); ?>';

        app.browser = "<?php echo strtolower($CI->agent->browser()); ?>";
        app.max_php_ini_upload_size_bytes = "<?php echo html_entity_decode($maxUploadSize); ?>";
        app.locale = "<?php echo html_entity_decode($locale); ?>";

        app.options = {
            calendar_events_limit: "<?php echo get_option('calendar_events_limit'); ?>",
            calendar_first_day: "<?php echo get_option('calendar_first_day'); ?>",
            tables_pagination_limit: "<?php echo get_option('tables_pagination_limit'); ?>",
            enable_google_picker: "<?php echo get_option('enable_google_picker'); ?>",
            google_client_id: "<?php echo get_option('google_client_id'); ?>",
            google_api: "<?php echo get_option('google_api_key'); ?>",
            default_view_calendar: "<?php echo get_option('default_view_calendar'); ?>",
            timezone: "<?php echo get_option('default_timezone'); ?>",
            allowed_files: "<?php echo get_option('allowed_files'); ?>",
            date_format: "<?php echo html_entity_decode($date_format); ?>",
            time_format: "<?php echo get_option('time_format'); ?>",
        };

        app.lang = {
            file_exceeds_maxfile_size_in_form: "<?php echo _l('file_exceeds_maxfile_size_in_form'); ?>" + ' (<?php echo bytesToSize('', $maxUploadSize); ?>)',
            file_exceeds_max_filesize: "<?php echo _l('file_exceeds_max_filesize'); ?>" + ' (<?php echo bytesToSize('', $maxUploadSize); ?>)',
            validation_extension_not_allowed: "<?php echo _l('validation_extension_not_allowed'); ?>",
            sign_document_validation: "<?php echo _l('sign_document_validation'); ?>",
            dt_length_menu_all: "<?php echo _l('dt_length_menu_all'); ?>",
            drop_files_here_to_upload: "<?php echo _l('drop_files_here_to_upload'); ?>",
            browser_not_support_drag_and_drop: "<?php echo _l('browser_not_support_drag_and_drop'); ?>",
            confirm_action_prompt: "<?php echo _l('confirm_action_prompt'); ?>",
            datatables: <?php echo json_encode(get_datatables_language_array()); ?>,
            discussions_lang: <?php echo json_encode(get_project_discussions_language_array()); ?>,
        };
        window.addEventListener('load',function(){
            custom_fields_hyperlink();
        });
    </script>
    <?php

    _do_clients_area_deprecated_js_vars($date_format, $locale, $maxUploadSize, $isRTL);

    $contents = ob_get_contents();
    ob_end_clean();
    echo html_entity_decode($contents);
}

/**
 * get company name
 * @param  integer $id 
 * @return string    
 */
function get_rec_channel_form_key($id)
{
    $CI = & get_instance();
    $CI->db->where('id',$id);
    $rec_campaign_form_web = $CI->db->get(db_prefix().'rec_campaign_form_web')->row();

    if($rec_campaign_form_web){
        return $rec_campaign_form_web->form_key;
    }else{
        return '';
    }

}

/**
 * is_rtl_rec
 * @param  boolean $client_area 
 * @return boolean              
 */
function is_rtl_rec($client_area = false)
{
    $CI = & get_instance();
    
    if ($client_area == true) {
        // Client not logged in and checked from clients area
        if (get_option('rtl_support_client') == 1) {
            return true;
        }
    } elseif (is_staff_logged_in()) {
        if (isset($GLOBALS['current_user'])) {
            $direction = $GLOBALS['current_user']->direction;
        } else {
            $CI->db->select('direction')->from(db_prefix() . 'staff')->where('staffid', get_staff_user_id());
            $direction = $CI->db->get()->row()->direction;
        }

        if ($direction == 'rtl') {
            return true;
        } elseif ($direction == 'ltr') {
            return false;
        } elseif (empty($direction)) {
            if (get_option('rtl_support_admin') == 1) {
                return true;
            }
        }

        return false;
    } elseif ($client_area == false) {
        if (get_option('rtl_support_admin') == 1) {
            return true;
        }
    }

    return false;
}

/**
 * re pdf logo url
 * @return [type] 
 */
function re_pdf_logo_url()
{
    $custom_pdf_logo_image_url = get_option('custom_pdf_logo_image_url');
    $width                     = get_option('pdf_logo_width');
    $logoUrl                   = '';

    if ($width == '') {
        $width = 120;
    }
    if ($custom_pdf_logo_image_url != '') {
        $logoUrl = $custom_pdf_logo_image_url;
    } else {
        if (get_option('company_logo_dark') != '' && file_exists(get_upload_path_by_type('company') . get_option('company_logo_dark'))) {
            $logoUrl = get_upload_path_by_type('company') . get_option('company_logo_dark');
        } elseif (get_option('company_logo') != '' && file_exists(get_upload_path_by_type('company') . get_option('company_logo'))) {
            $logoUrl = get_upload_path_by_type('company') . get_option('company_logo');
        }
    }

    $logoImage = '';
    if ($logoUrl != '') {
        $logoImage = '<img class="logo_width" src="' . $logoUrl . '">';
    }

    return hooks()->apply_filters('pdf_logo_url', $logoImage);
}

/**
 * rec get status modules
 * @param  [type] $module_name 
 * @return [type]              
 */
function rec_get_status_modules($module_name){
    $CI             = &get_instance();

    $sql = 'select * from '.db_prefix().'modules where module_name = "'.$module_name.'" AND active =1 ';
    $module = $CI->db->query($sql)->row();
    if($module){
        return true;
    }else{
        return false;
    }
}