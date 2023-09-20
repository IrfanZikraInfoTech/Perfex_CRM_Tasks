<?php
defined('BASEPATH') or exit('No direct script access allowed');
hooks()->add_action('after_email_templates', 'add_spreadsheet_share_email_templates');

/**
 * Get all staff by department
 * @param  string $departmentid Optional
 * @return array
 */
function get_all_staff_by_department($departmentid)
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

/**
 * Get all client by group
 * @param  string $groupid Optional
 * @return array
 */
function get_all_client_by_group($groupid)
{
    $CI = & get_instance();

    if ($groupid) {
        $CI->db->where('groupid', $groupid);
        $clientids = $CI->db->select('customer_id')->from(db_prefix() . 'customer_groups')->get()->result_array();
    }else{
    	$clientids = [];
    }

    return $clientids;
}


if (!function_exists('add_spreadsheet_share_email_templates')) {
    /**
     * Init appointly email templates and assign languages
     * @return void
     */
    function add_spreadsheet_share_email_templates() {
        $CI = &get_instance();

        $data['spreadsheet_share'] = $CI->emails_model->get(['type' => 'spreadsheet_online', 'language' => 'english']);

        $CI->load->view('spreadsheet_online/email_templates', $data);
    }
}


/**
 * { email staff }
 *
 * @param        $staffid  The staffid
 *
 * @return      ( description_of_the_return_value )
 */
function spreadsheet_email_staff($staffid){
    $CI = & get_instance();
    $CI->db->where('staffid', $staffid);
    return $CI->db->get(db_prefix().'staff')->row()->email;
}
