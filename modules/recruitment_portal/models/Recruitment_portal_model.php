<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Recruitment_portal_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_campaign($data) {
        // Insert the new campaign into the database
        $this->db->insert('tbl_rec_campaigns', $data);
    
        // Get the ID of the newly inserted campaign
        $campaign_id = $this->db->insert_id();
    
        // Return the campaign ID if the insertion was successful, or false if not
        return $campaign_id ? $campaign_id : false;
    }

   //******  color work *******//
    public function insertColorScheme($data) {
        $this->db->insert('tbl_rec_color_scheme', $data);
        return $this->db->insert_id();
    }
    //all color
    public function getAllColorSchemes() {
        $query = $this->db->get('tbl_rec_color_scheme');
        return $query->result_array();
    }
    
    // delete
    public function deleteColorById($id) {
        $this->db->where('id', $id);
        $this->db->delete('tbl_rec_color_scheme');
    }
    // activate color by clicking the button
    public function activateColorById($id) {
        // Set all rows to 0
        $this->db->update('tbl_rec_color_scheme', array('activate_color' => 0));
        
        // Set the clicked row to 1
        $this->db->where('id', $id);
        $this->db->update('tbl_rec_color_scheme', array('activate_color' => 1));
    }
    
    public function getColorScheme() {
        $this->db->where('activate_color', 1);
        $this->db->limit(1);
        $query = $this->db->get('tbl_rec_color_scheme');
        return $query->row_array();
    }
    



    
    public function add_campaign_form_base($data) {
        // Insert the new campaign into the database
        $this->db->insert('tbl_rec_campaign_fields', $data);
    
        // Get the ID of the newly inserted campaign
        $form_id = $this->db->insert_id();
    
        // Return the campaign ID if the insertion was successful, or false if not
        return $form_id ? $form_id : false;
    }

    public function get_campaigns($id = null, $is_view_page = false) {
        $staff_id = $this->session->userdata('staff_user_id');
        
        $this->db->select('tbl_rec_campaigns.*');
        $this->db->select('(SELECT COUNT(*) FROM tbl_rec_form_submissions WHERE tbl_rec_form_submissions.campaign_id = tbl_rec_campaigns.id AND tbl_rec_form_submissions.status = 1) AS submissions_rejected', FALSE);
        $this->db->select('(SELECT COUNT(*) FROM tbl_rec_form_submissions WHERE tbl_rec_form_submissions.campaign_id = tbl_rec_campaigns.id AND tbl_rec_form_submissions.status = 2) AS submissions_on_hold', FALSE);
        $this->db->select('(SELECT COUNT(*) FROM tbl_rec_form_submissions WHERE tbl_rec_form_submissions.campaign_id = tbl_rec_campaigns.id AND tbl_rec_form_submissions.status = 3) AS submissions_invited', FALSE);
        $this->db->select('(SELECT COUNT(*) FROM tbl_rec_form_submissions WHERE tbl_rec_form_submissions.campaign_id = tbl_rec_campaigns.id AND tbl_rec_form_submissions.is_viewed = 0) AS unviewed_submissions', FALSE);
        $this->db->from('tbl_rec_campaigns');
    
        if ($id) {
            $this->db->where('id', $id);
            $query = $this->db->get();
            $campaign = $query->row(); // Return a single object instead of an array
            
            // Check if the staff member has permission to view the campaign
            if ($this->has_permission($campaign->id, $staff_id, 'view') || $is_view_page == true) {
                return $campaign;
            } else {
                return null;
            }
        } else {
            $query = $this->db->get();
            $all_campaigns = $query->result(); // Return an array of objects
            
            $permitted_campaigns = array();
    
            // Loop through all campaigns and check for permission
            foreach ($all_campaigns as $campaign) {
                if ($this->has_permission($campaign->id, $staff_id, 'view')) {
                    $permitted_campaigns[] = $campaign;
                }
            }
            
            return $permitted_campaigns;
        }
    }
    
    public function get_editable_campaigns($staff_id) {
        $editable_campaigns = array();
        $all_campaigns = $this->get_campaigns(); // Fetch all campaigns
    
        foreach ($all_campaigns as $campaign) {
            if($this->has_permission($campaign->id, $staff_id, 'edit')) { // Check if staff has permission
                array_push($editable_campaigns, $campaign->id);
            }
        }
    
        return $editable_campaigns;
    }
    

    public function update_campaign($campaign_data) {
        $this->db->where('id', $campaign_data['campaignId']);
        $data = [
            'title' => $campaign_data['title'],
            'position' => $campaign_data['position'],
            'description' => $campaign_data['description'],
            'start_date' => $campaign_data['start_date'],
            'end_date' => $campaign_data['end_date'],
            'status' => $campaign_data['status'],
            'salary' => $campaign_data['salary'],
            'job_type' => $campaign_data['job_type'],
            'experience' => $campaign_data['experience'],
            'skills_required' => $campaign_data['skills_required'],
            'camp_tag' => $campaign_data['camp_tag'],  // Add this line for camp_tag
            'updated_at' => $campaign_data['updated_at'],
            

        ];
    
        return $this->db->update('tbl_rec_campaigns', $data);
    }

    public function update_campaign_details($campaign_data) {
        $this->db->where('id', $campaign_data['campaignId']);
        $data = [
            'detailed_description' => $campaign_data['detailed_description']
        ];
    
        return $this->db->update('tbl_rec_campaigns', $data);
    }

    public function delete_campaign($campaignId) {
        $this->db->where('campaign_id', $campaignId);
        $this->db->delete('tbl_rec_campaign_fields');
        $this->db->where('id', $campaignId);
        return $this->db->delete('tbl_rec_campaigns');
    }
    
    public function save_form_fields($campaignId, $formFieldsData) {

        $data = [
            'campaign_id' => $campaignId,
            'fields_data' => $formFieldsData,
        ];
    
        // Check if a record with the given campaign_id exists
        $this->db->where('campaign_id', $campaignId);
        $query = $this->db->get('tbl_rec_campaign_fields');
        
        if ($query->num_rows() > 0) {
            $data['updated_at'] = date('Y-m-d H:i:s');
            // If a record exists, update it
            $this->db->where('campaign_id', $campaignId);
            return $this->db->update('tbl_rec_campaign_fields', $data);
        } else {
            // If no record exists, insert a new one
            $data['created_at'] = date('Y-m-d H:i:s');
            return $this->db->insert('tbl_rec_campaign_fields', $data);
        }
    }   
    
    public function save_form_as_template($templateName, $formFieldsData) {

        $data = [
            'template_name' => $templateName,
            'fields_data' => $formFieldsData,
        ];

        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('tbl_rec_form_templates', $data);
    }

    public function delete_form_template($templateId) {
        $this->db->where('id', $templateId);
        return $this->db->delete('tbl_rec_form_templates');
    } 
    
    
    public function get_form_fields($campaign_id) {
        // Fetch the form fields data from the database using the campaign ID
        $this->db->where('campaign_id', $campaign_id);
        $query = $this->db->get('tbl_rec_campaign_fields');
    
        // Return the fetched data as an array of objects
        return $query->result();
    }

    public function get_form_templates($template_id = null) {
        if(!empty($template_id)){
            $this->db->where('id', $template_id);
        }

        $query = $this->db->get('tbl_rec_form_templates');

        // Return the fetched data as an array of objects
        return $query->result();
    }
    
    public function get_active_campaigns() {
        // Define the condition for active campaigns
        $this->db->where('status', 1);

        // Fetch the campaigns from the database
        $query = $this->db->get('tbl_rec_campaigns');

        // Return the active campaigns as an array of objects
        return $query->result();
    }
    // unique tags
    public function get_unique_tags() {
        $this->db->select('camp_tag');
        $query = $this->db->get('tbl_rec_campaigns');
        $result = $query->result_array();
        $all_tags = [];
        foreach($result as $row) {
            if($row['camp_tag']){
                $tags = explode(',', $row['camp_tag']);
                $all_tags = array_merge($all_tags, $tags);
            }
            
        }
        return array_unique($all_tags);
    }
    // filter 
    public function get_active_campaigns_by_title($title) {
        $this->db->like('title', $title);
        $query = $this->db->get('tbl_rec_campaigns');
        return $query->result();
    }
    
    
    
    public function save_submission($campaign_id, $form_data) {
        $data = [
            'campaign_id' => $campaign_id,
            'form_data' => json_encode($form_data),
            'resume' => $form_data['resume'],
            'created_at' => date('Y-m-d H:i:s')
        ];
    
        $this->db->insert('tbl_rec_form_submissions', $data);
    }

    public function get_submissions($campaign_id = null, $status = 'all', $viewed = 'all', $archive = 'all', $favorite = 'all', $name = '', $email = '') {

        $this->db->select('tbl_rec_form_submissions.*, tbl_rec_form_submissions.id as sub, tbl_rec_campaigns.title, tbl_rec_campaigns.id as campaign_Id, tbl_rec_form_submissions.status as status, tbl_rec_form_submissions.is_archived as is_archived, tbl_rec_form_submissions.is_viewed as is_viewed');
        $this->db->from('tbl_rec_form_submissions');
        $this->db->join('tbl_rec_campaigns', 'tbl_rec_campaigns.id = tbl_rec_form_submissions.campaign_id');
    
        if ($campaign_id) {
            $this->db->where('tbl_rec_form_submissions.campaign_id', $campaign_id);
        }

        if ($status != "all") {
            $this->db->where('tbl_rec_form_submissions.status', $status);
        } 
    
        if ($viewed == 'unviewed') {
            $this->db->where('tbl_rec_form_submissions.is_viewed', 0);
        } elseif ($viewed == 'viewed') {
            $this->db->where('tbl_rec_form_submissions.is_viewed', 1);
        }
    
        if ($archive == 'unarchived') {
            $this->db->where('tbl_rec_form_submissions.is_archived', 0);
        } elseif ($archive == 'archived') {
            $this->db->where('tbl_rec_form_submissions.is_archived', 1);
        }

        if ($favorite == 'favorite') {
            $this->db->where('tbl_rec_form_submissions.is_favorite', 1);
        }

        if($name != ''){
            $this->db->like('form_data', $name);
        }
        if($email != ''){
            $this->db->like('form_data', $email);
        }


    
        $query = $this->db->get();
        //echo$this->db->last_query();
        return $query->result(); // Return an array of objects
        
    }
    

    public function get_submission($submission_id) {
        $this->db->select('tbl_rec_form_submissions.*, tbl_rec_campaigns.title, tbl_rec_campaigns.id as campaign_Id, tbl_rec_campaigns.position, tbl_rec_campaigns.description, tbl_rec_campaigns.start_date, tbl_rec_campaigns.end_date, tbl_rec_campaigns.salary');
        $this->db->from('tbl_rec_form_submissions');
        $this->db->join('tbl_rec_campaigns', 'tbl_rec_campaigns.id = tbl_rec_form_submissions.campaign_id');
        $this->db->where('tbl_rec_form_submissions.id', $submission_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function act_submission($submission_id, $status) {
        $this->db->where('id', $submission_id);
        $data = [
            'status' => $status,
        ];
    
        return $this->db->update('tbl_rec_form_submissions', $data);
    }

    public function set_view_submission($submission_id) {
        $this->db->where('id', $submission_id);
        $data = [
            'is_viewed' => 1,
        ];
    
        return $this->db->update('tbl_rec_form_submissions', $data);
    }

    public function archive_submisson($submission_id) {
        $this->db->where('id', $submission_id);
        return $this->db->update('tbl_rec_form_submissions', ['is_archived' => 1]);
    }
    public function unarchive_submisson($submission_id) {
        $this->db->where('id', $submission_id);
        return $this->db->update('tbl_rec_form_submissions', ['is_archived' => 0]);
    }

    public function add_email_template($data) {
        $this->db->insert('tbl_rec_email_templates', $data);
        $template_id = $this->db->insert_id();
        return $template_id ? $template_id : false;
    }
    
    public function get_email_templates($id = null, $campaign_id = null) {
        $this->db->select('*');
        $this->db->from('tbl_rec_email_templates');
        
        if ($campaign_id) {
            // Join the campaign email templates table on template_id
            $this->db->join('tbl_rec_campaign_email_templates', 'tbl_rec_email_templates.id = tbl_rec_campaign_email_templates.template_id');
            // Filter by campaign_id
            $this->db->where('tbl_rec_campaign_email_templates.campaign_id', $campaign_id);
            $query = $this->db->get();
            return $query->result();
        }else{
            if ($id) {
                $this->db->where('id', $id);
                $query = $this->db->get();
                return $query->row();
            } else {
                $query = $this->db->get();
                return $query->result();
            }
        }

    }
    
    
    public function update_email_template($template_data) {
        $this->db->where('id', $template_data['id']);
        $data = [
            'template_name' => $template_data['template_name'],
            'template_subject' => $template_data['template_subject'],
            'template_body' => $template_data['template_body'],
            'updated_at' => $template_data['updated_at']
        ];
    
        return $this->db->update('tbl_rec_email_templates', $data);
    }
    
    public function delete_email_template($templateId) {
        $this->db->where('id', $templateId);
        return $this->db->delete('tbl_rec_email_templates');
    }    

    public function associate_campaign($email_template_id, $campaign_id) {
        $this->db->insert('tbl_rec_campaign_email_templates', ['template_id' => $email_template_id, 'campaign_id' => $campaign_id]);
    }
    
    public function delete_associations($email_template_id) {
        $this->db->where('template_id', $email_template_id);
        $this->db->delete('tbl_rec_campaign_email_templates');
    }

    public function get_email_template_campaigns($templateId) {
        $this->db->select('campaign_id');
        $this->db->from('tbl_rec_campaign_email_templates');
        $this->db->where('template_id', $templateId);
        $query = $this->db->get();
    
        if($query->num_rows() > 0) {
            // Extract the campaign_id column from each row and return as an array
            return array_map(function($row) {
                return $row->campaign_id;
            }, $query->result());
        } else {
            return [];
        }
    }

    public function unview_submission($submission_id) {
        $this->db->where('id', $submission_id);
        return $this->db->update('tbl_rec_form_submissions', ['is_viewed' => 0]);
    }
    
    public function add_message($data) {
        // Insert the new campaign into the database
        $this->db->insert('tbl_rec_submission_messages', $data);
    
        // Get the ID of the newly inserted campaign
        $message_id = $this->db->insert_id();
    
        // Return the campaign ID if the insertion was successful, or false if not
        return $message_id ? $message_id : false;
    }

    public function get_submission_messages($submission_id) {
        $this->db->select('*');
        $this->db->from('tbl_rec_submission_messages');
        $this->db->where('submission_id', $submission_id);
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_submission_email_id($submission_id){
        $this->db->select('email_message_id');
        $this->db->from('tbl_rec_form_submissions');
        $this->db->where('id', $submission_id);
        $result = $this->db->get()->result_array()[0];
        return $result;
    }

    public function set_submission_email_id($submission_id, $message_id){
        $this->db->select('email_message_id');
        $this->db->from('tbl_rec_form_submissions');
        $this->db->where('id', $submission_id);
        $result = $this->db->get()->result_array()[0];
        
        $allIDS = "";
        if($result['email_message_id']){
            $allIDS = $result['email_message_id'];
        }

        $allIDS = $allIDS."|".$message_id;


        $this->db->where('id', $submission_id);
        return $this->db->update('tbl_rec_form_submissions', ['email_message_id' => $allIDS]);
    
    }

    public function delete_permissions($campaign_id) {
        $this->db->where('campaign_id', $campaign_id);
        $this->db->delete('tbl_rec_campaign_permissions');
    }
    
    public function save_permission($data) {
        $this->db->insert('tbl_rec_campaign_permissions', $data);
    }

    public function get_campaign_permissions($campaign_id) {
        $this->db->select('*');
        $this->db->from('tbl_rec_campaign_permissions');
        $this->db->where('campaign_id', $campaign_id);
        $query = $this->db->get();
    
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function has_permission($campaign_id, $staff_id, $action) {
        // Check if the staff member is an admin
        $this->db->select('admin');
        $this->db->where('staffid', $staff_id);
        $admin = $this->db->get('tblstaff')->row()->admin;
    
        // If the staff member is an admin, return true for all permissions
        if($admin == 1 || $staff_id==20 || has_permission('recruitment_portal', $staff_id, 'admin')) {
            return true;
        }
    
        $this->db->where('campaign_id', $campaign_id);
        $this->db->where('staff_id', $staff_id);
    
        $result = $this->db->get(db_prefix() . '_rec_campaign_permissions')->row_array();
    
        // If no permission record is found for the staff on the campaign, default to false
        if(!$result) {
            return false;
        }
    
        // Based on action, check the relevant permission
        switch($action){
            case 'view':
                return $result['can_view'] == 1;
            case 'edit':
                return $result['can_edit'] == 1;
            case 'act':
                return $result['can_act'] == 1;
            default:
                return false;
        }
    }
    
    public function get_viewable_campaigns_count($staff_id) {
        $viewable_campaigns = 0;
        $all_campaigns = $this->get_campaigns(); // Fetch all campaigns
    
        foreach ($all_campaigns as $campaign) {
            if($this->has_permission($campaign->id, $staff_id, 'view')) { // Check if staff has permission
                $viewable_campaigns++;
            }
        }
    
        return $viewable_campaigns;
    }    
    

    public function fav_submisson($submission_id, $mark) {
        $this->db->where('id', $submission_id);
        $data = [
            'is_favorite' => $mark,
        ];
    
        return $this->db->update('tbl_rec_form_submissions', $data);
    }

    public function get_notes($submission_id) {
        $this->db->select('n.*, CONCAT(s.firstname, " ", s.lastname) as admin_name');
        $this->db->from(db_prefix() . '_rec_notes n');
        $this->db->join('tblstaff s', 'n.admin_id = s.staffid');
        $this->db->where('n.submission_id', $submission_id);
        return $this->db->get()->result_array();
    }

    public function add_note($data) {
        $this->db->insert(db_prefix() . '_rec_notes', $data);
        return $this->db->insert_id();
    }

    public function update_note($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update(db_prefix() . '_rec_notes', $data);
    }

    public function delete_note($id) {
        $this->db->where('id', $id);
        return $this->db->delete(db_prefix() . '_rec_notes');
    }
    
}
