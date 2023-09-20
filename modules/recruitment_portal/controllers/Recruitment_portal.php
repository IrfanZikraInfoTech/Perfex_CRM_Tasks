<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Recruitment_portal extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('recruitment_portal_model');
    }

    public function campaigns()
    {
        $this->load->model('staff_model');
        $data['staff_members'] = $this->staff_model->get(['is_active' => 1]);
        $data['editable_campaigns'] = $this->recruitment_portal_model->get_editable_campaigns($this->session->userdata('staff_user_id'));
        $this->load->view('campaigns/manage', $data);
    }
    // public function color() {
    //     $colorScheme = $this->recruitment_portal_model->getColorScheme();
        
    //     if (!empty($colorScheme)) {
    //         $data['colorScheme'] = $colorScheme;
    //         $this->load->view('career/color', $data);
    //     } else {
    //         $this->load->view('career/color');
    //     }
    // }

    public function color() {
        // Pehla function ka kaam: Sab color schemes fetch karain
        $data['allColors'] = $this->recruitment_portal_model->getAllColorSchemes();
        
        // Dosra function ka kaam: Selected color scheme fetch karain
        $colorScheme = $this->recruitment_portal_model->getColorScheme();
    
        if (!empty($colorScheme)) {
            $data['colorScheme'] = $colorScheme;
        }
    
        // Dono cases ke liye ek hi view use karein
        $this->load->view('career/color', $data);
    }
    
    public function saveColor() {
        $data = array(
            'background_color' => $this->input->post('background_color'),
            'button_color' => $this->input->post('button_color'),
        );
    
        $this->load->model('recruitment_portal_model');
        $this->recruitment_portal_model->insertColorScheme($data);
    
        //  $this->load->view('career/color');
         $this->color();
    }
    // public function colorSetting(){
    //     $data['allColors'] = $this->recruitment_portal_model->getAllColorSchemes();
    //     $this->load->view('career/colorsetting',$data);
    // }
    public function deleteColor($id) {
        $this->load->model('recruitment_portal_model');
        $this->recruitment_portal_model->deleteColorById($id);
        // $this->colorSetting();  // This will reload the view with data.
        $this->color();
    }
    public function activateColor($id) {
        $this->load->model('recruitment_portal_model');
        $this->recruitment_portal_model->activateColorById($id);
        $this->color();  // This will reload the view with data.
    }
    
    
   
    public function career() {
        $filter_title = $this->input->get('filter_title');
        
        if(!empty($filter_title)) {
            $data['activeCampaigns'] = $this->recruitment_portal_model->get_active_campaigns_by_title($filter_title);
        } else {
            $data['activeCampaigns'] = $this->recruitment_portal_model->get_active_campaigns();
        }
        
        $data['uniqueTags'] = $this->recruitment_portal_model->get_unique_tags();
        $data['colorScheme'] = $this->recruitment_portal_model->getColorScheme();

        $data['filter_title'] = $filter_title;

        $this->load->view('career/index', $data);
    }
    public function add_campaign() {
        if ($this->input->is_ajax_request()) {
            // Get the form data from the AJAX request
            $data = array(
                'title' => $this->input->post('title'),
                'position' => $this->input->post('position'),
                'description' => $this->input->post('description'),
                'start_date' => $this->input->post('start_date'),
                'end_date' => $this->input->post('end_date'),
                'status' => $this->input->post('status'),
                'salary' => $this->input->post('salary'),
                'created_at' => date('Y-m-d H:i:s'),
                'job_type' => $this->input->post('job_type'),
                'experience' => $this->input->post('experience'),
                'skills_required' => $this->input->post('skills_required'),
                'camp_tag' => $this->input->post('camp_tag'),  // Assuming the input name is camp_tag
                //  'tags' => json_encode(explode(',', $this->input->post('camp_tag')))
            );
    
            // Add the new campaign to the database using the model
            $campaign_id = $this->recruitment_portal_model->add_campaign($data);

            $defaultFormData = array(
                array("name" => "Name", "type" => "text", "order" => "1", "maxLength"=>45),
                array("name" => "Email", "type" => "email", "order" => "2"),
                array("name" => "Age", "type" => "numbers", "order" => "3", "min"=>15, "max"=>50),
                array("name" => "Phone Number", "type" => "text", "order" => "4", "maxLength"=>13),
                array("name" => "Gender", "type" => "select", "order" => "5", "options" => json_encode(array("Male", "Female", "Rather not say")))
            );

            $data = array(
                'campaign_id' => $campaign_id,
                'fields_data' => json_encode($defaultFormData),
                'created_at' => date('Y-m-d H:i:s')
            );

            $form_id = $this->recruitment_portal_model->add_campaign_form_base($data);
    
            // Check if the campaign was added successfully
            if ($campaign_id && $form_id) {
                echo json_encode(array('success' => true, 'message' => 'Campaign added successfully.'));
            } else {
                echo json_encode(array('success' => false, 'message' => 'Failed to add the campaign.'));
            }
        } else {

            show_error('No direct script access allowed.');
        }
    }

    public function get_campaigns() {
        $id = $this->input->get("id");
        $campaigns = $this->recruitment_portal_model->get_campaigns($id);
    
        $data = [
            "campaigns" => $campaigns
        ];

        echo json_encode($data);
        

    }

    public function update_campaign() {
        $campaign_data = [
            'campaignId' => $this->input->post('campaignId'),
            'title' => $this->input->post('title'),
            'position' => $this->input->post('position'),
            'description' => $this->input->post('description'),
            'start_date' => $this->input->post('start_date'),
            'end_date' => $this->input->post('end_date'),
            'status' => $this->input->post('status'),
            'salary' => $this->input->post('salary'),
            'updated_at' => date("Y-m-d H:i:s"),
            'job_type' => $this->input->post('job_type'),
            'experience' => $this->input->post('experience'),
            'skills_required' => $this->input->post('skills_required'),  
            'camp_tag' => $this->input->post('camp_tag'),  // Add this line for camp_tag    
        ];
    
        $result = $this->recruitment_portal_model->update_campaign($campaign_data);
    
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update campaign.']);
        }
    }

    public function update_campaign_details() {
        $campaign_data = [
            'campaignId' => $this->input->post('campaignId'),
            'detailed_description' => $this->input->post('details')
        ];
    
        $result = $this->recruitment_portal_model->update_campaign_details($campaign_data);
    
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update campaign.']);
        }
    }

    

    public function delete_campaign() {
        $campaignId = $this->input->post('campaignId');
        $result = $this->recruitment_portal_model->delete_campaign($campaignId);
      
        if ($result) {
          echo json_encode(['success' => true]);
        } else {
          echo json_encode(['success' => false, 'error' => 'Failed to delete campaign.']);
        }
    }

    public function edit_form($campaign_id, $form_template_id = null)
    {
        if (!$campaign_id || !$this->recruitment_portal_model->get_campaigns($campaign_id)) {
            show_404();
        }

        

        $data['campaign'] = $this->recruitment_portal_model->get_campaigns($campaign_id);
        $data['form_fields'] = $this->recruitment_portal_model->get_form_fields($campaign_id);
        $data['form_templates'] = $this->recruitment_portal_model->get_form_templates();

        if(empty($form_template_id)){
            $data['form_fields_code'] = $data['form_fields'][0]->fields_data;
            $data['is_template'] = false;
        }else{
            $template = $this->recruitment_portal_model->get_form_templates($form_template_id);
           
            if(array_key_exists(0, $template)){
                $data['form_fields_code'] = $template[0]->fields_data;
                $data['is_template'] = true;
                $data['template_name'] = $template[0]->template_name;
                $data['template_id'] = $template[0]->id;
            }else{
                $data['form_fields_code'] = $data['form_fields'][0]->fields_data;
                $data['is_template'] = false;
            }
            
        }

        $this->load->view('forms/edit', $data);
    }

    public function save_form_as_template() {
        $formFieldsData = $this->input->post('form_fields_data');
        $templateName = $this->input->post('templateName');// Get the campaign ID
    
        // Save the form fields data to the database
        if($this->recruitment_portal_model->save_form_as_template($templateName, $formFieldsData)){
            echo json_encode(array('success' => true, 'message' => 'Template saved successfully.'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Failed to save template.'));
        }
    }

    public function delete_form_template() {
        $templateId = $this->input->post('templateId');
        $result = $this->recruitment_portal_model->delete_form_template($templateId);
      
        if ($result) {
          echo json_encode(['success' => true, 'message' => 'Template Deleted Successfully']);
        } else {
          echo json_encode(['success' => false, 'message' => 'Failed to delete form template.']);
        }
    }

    public function save_form_fields() {
        $formFieldsData = $_POST['form_fields_data'];
        $campaignId = $this->input->post('campaign_id');// Get the campaign ID

        // Save the form fields data to the database
        $result = $this->recruitment_portal_model->save_form_fields($campaignId, $formFieldsData);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Form saved Successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save form.']);
        }
    }

    public function view($id){
        $data['campaign'] = $this->recruitment_portal_model->get_campaigns($id, true);
        $data['colorScheme'] = $this->recruitment_portal_model->getColorScheme();
        $this->load->view('career/view', $data);
    }

    public function apply($id){
        $data['campaign'] = $this->recruitment_portal_model->get_campaigns($id, true);
        $data['form_fields'] = $this->recruitment_portal_model->get_form_fields($id);
        $data['csrf_name'] = $this->security->get_csrf_token_name();
        $data['csrf_token'] = $this->security->get_csrf_hash();
        $data['colorScheme'] = $this->recruitment_portal_model->getColorScheme();
        $this->load->view('career/apply', $data);
    }

    public function submissions($campaign_id = null) {
        if($campaign_id == null){
            $data['campaigns'] = $this->recruitment_portal_model->get_campaigns($campaign_id);
            $this->load->view('submissions/index', $data);
        } else {

            if($campaign_id == "0"){
            
                $data['campaign_name'] = "Universal Search";
                $data['templates'] = $this->recruitment_portal_model->get_email_templates(null, $campaign_id);

                $data['submission_name'] = isset($_GET['name']) ? $_GET['name'] : '';
                $data['submission_email'] = isset($_GET['email']) ? $_GET['email'] : '';

            }else{
                $data['campaign_name'] = $this->recruitment_portal_model->get_campaigns($campaign_id)->title;
                $data['templates'] = $this->recruitment_portal_model->get_email_templates(null, $campaign_id);
               
            }

            $data['id'] = $campaign_id;
            $data['status'] = isset($_GET['status']) ? $_GET['status'] : 'all';
            $data['viewed'] = isset($_GET['viewed']) ? $_GET['viewed'] : 'all';
            $data['archive'] = isset($_GET['archive']) ? $_GET['archive'] : 'all';
            $data['favorite'] = isset($_GET['favorite']) ? $_GET['favorite'] : 'all';

            
            
            $data['can_act'] = $this->recruitment_portal_model->has_permission($campaign_id, $this->session->userdata('staff_user_id'), 'act');
            
            $this->load->view('submissions/submissions', $data);
        }   
    }
    
    
    public function handle_submission_skip_auth() {
        // Retrieve submitted data
        $campaign_id = $this->input->post('campaign_id');
        $form_data = $_POST;
        unset($form_data[0]);

        if(array_key_exists('Email', $form_data)){

            $email = $form_data['Email'];

            // Check if the email exists in the 'form_data' column
            $this->db->select('form_data');
            $this->db->from('tbl_rec_form_submissions');
            $this->db->where('campaign_id', $campaign_id);
            $this->db->where("JSON_UNQUOTE(JSON_EXTRACT(form_data, '$.Email')) =", $email);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                echo json_encode(['success' => false, 'message' => 'Can\'t apply twice!']);
                return;
            }

        }
    
        // Handle file upload
        $config['upload_path'] = './uploads/resumes/';
        $config['allowed_types'] = 'pdf|doc|docx';
        $config['max_size'] = 2048; // 2MB
        $config['encrypt_name'] = TRUE;

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }
        
        $this->load->library('upload', $config);

        header('Content-Type: application/json');
    
        if (!$this->upload->do_upload('resume')) {
            // Handle file upload error
            $error = $this->upload->display_errors();
            // Show an error message or redirect to an error page
            echo json_encode(['success' => false, 'message' => $error]);
        } else {
            $upload_data = $this->upload->data();
            $form_data['resume'] = $upload_data['file_name'];
    
            // Save the submitted data
            $this->recruitment_portal_model->save_submission($campaign_id, $form_data);
    
            // Return success status
            echo json_encode(['success' => true, 'form_data'=>$form_data]);
        }
    }
    
    public function get_submissions($campaign_id = null, $status = 'all', $viewed = 'all', $archive = 'all', $favorite = 'all', $name = '', $email = '') {

        $submissions = $this->recruitment_portal_model->get_submissions($campaign_id, $status, $viewed, $archive, $favorite, $name, $email);
        

        $dataSubmissions = [];
        foreach ($submissions as $submission) {
            
            $fields_data = json_decode($submission->form_data, true);
            $name = '';
            $email = '';

            if(array_key_exists('Name', $fields_data)){
                $name = $fields_data['Name'];
            }
            if(array_key_exists('Email', $fields_data)){
                $email = $fields_data['Email'];
            }

            $date = DateTime::createFromFormat('Y-m-d H:i:s', $submission->created_at);
            // Format the date as needed
            $readable_date = $date->format('F j, Y, g:i a');

        
            $dataSubmissions[] = [
                'id' => $submission->campaign_id,
                'sub' => $submission->id,
                'campaign' => $submission->title,
                'name' => $name,
                'email' => $email,
                'submission_date' => $readable_date,
                'resume'  => $submission->resume,
                'status' => $submission->status,
                'is_archived' => $submission->is_archived,
                'is_viewed' => $submission->is_viewed,
                'is_favorite' => $submission->is_favorite
                // Add other columns as needed
            ];
        }
    
        $data = [
            "submissions" => $dataSubmissions
        ];
    
        echo json_encode($data);
    }

    public function get_submission_data($id = null) {
        if (!$id) {
            $id = $this->input->get('id');
        }

        $submission = $this->recruitment_portal_model->get_submission($id);

        $fields_data = json_decode($submission->form_data, true);
        
        $name = (array_key_exists('Name', $fields_data)) ? $fields_data['Name'] : '';

        $email = (array_key_exists('Email', $fields_data)) ?  $fields_data['Email'] : '';


        $date = DateTime::createFromFormat('Y-m-d H:i:s', $submission->created_at);
        // Format the date as needed
        $readable_date = $date->format('F j, Y, g:i a');
    
        $dataSubmissions[] = [
            'candidate_name' => $name,
            'campaign_name' => $submission->title,
            'candidate_email' => $email,
            'position_name' => $submission->position,
            'submission_date' => $readable_date,
            // Add other columns as needed
        ];
        
    
        echo json_encode($dataSubmissions);
    }

    public function view_resume($file_name)
    {
        $this->load->view('submissions/view_resume', ['file_name' => $file_name, 'base_uri' => base_url()]);
    }

    public function view_submission($submission_id) {
        $this->recruitment_portal_model->set_view_submission($submission_id);

        $data['submission'] = $this->recruitment_portal_model->get_submission($submission_id);

        $fields_data = json_decode($data['submission']->form_data, true);
        $data['email'] = (array_key_exists('Email', $fields_data)) ?  $fields_data['Email'] : '';

        $data['can_act'] = $this->recruitment_portal_model->has_permission($data['submission']->campaign_id, $this->session->userdata('staff_user_id'), 'act');

        $data['templates'] = $this->recruitment_portal_model->get_email_templates(null, $data['submission']->campaign_id);
        $data['messages'] = $this->recruitment_portal_model->get_submission_messages($submission_id);
        
        $data['notes'] = $this->recruitment_portal_model->get_notes($submission_id);
       
        $this->load->view('submissions/view', $data);
    }

    public function act_submission() {
        $submission_id = $this->input->post('id');
        $status = $this->input->post('status');
        $subject = $this->input->post('subject');
        $body = $this->input->post('body');

        $submission = $this->recruitment_portal_model->get_submission($submission_id);
        $fields_data = json_decode($submission->form_data, true);

        $email = (array_key_exists('Email', $fields_data)) ?  $fields_data['Email'] : '';

        $name = (array_key_exists('Name', $fields_data)) ?  $fields_data['Name'] : '';
        $title = $submission->title;

        if(!empty($email)){

            $this->recruitment_portal_model->act_submission($submission_id, $status);

            $this->load->library('email');

            $this->email->initialize();
            $this->email->reply_to('recruitment+s_'.$submission_id.'@crm.zikrainfotech.com', "Candidate Reply");
            $this->email->from("recruitment@crm.zikrainfotech.com", "Zikra Infotech LLC, HR");
            $this->email->to($email);

            $this->email->subject("Hey ".$name.", Update regarding your ".$title." application at Zikrainfotech LLC.");
            $this->email->message(get_option('email_header') . $body . get_option('email_footer'));

            $email_message_id = $this->recruitment_portal_model->get_submission_email_id($submission_id)['email_message_id'];

            if($email_message_id){
                $email_message_id = substr($email_message_id, 1);
                $email_message_ids = explode('|', $email_message_id);

                if($email_message_ids){
                    // Preparing the References string with all the email IDs separated by a space
                    $references = implode(' ', $email_message_ids);
                    
                    // Setting the headers
                    $this->email->set_header('References', $references);
                    $this->email->set_header('In-Reply-To', end($email_message_ids));
                }
            }
            

            $this->email->set_header('X-SES-CONFIGURATION-SET', 'Config_1');
            $this->email->set_header('submission_id', $submission_id);

            $email_sent = $this->email->send(false);

            // Check if the campaign was added successfully
            if ($email_sent) {
                

                // Get the form data from the AJAX request
                $data = array(
                    'submission_id' => $submission_id,
                    'subject' => $subject,
                    'message' => $body,
                    'sent_by' => 'admin',
                    'created_at' => date('Y-m-d H:i:s')
                );
        
                // Add the new campaign to the database using the model
                $message_id = $this->recruitment_portal_model->add_message($data);

                if($message_id){
                    echo json_encode(["success" => true, "message" => "Email sent successfully."]);
                }else{
                    echo json_encode(["success" => true, "message" => "Email failed!."]);
                }
            }

        }else{
            echo json_encode(["success" => false, "message" => "Email not in form."]);
        }
        
        
    }

    public function archive_submisson() {
        $submission_id = $this->input->post('submissionId');
        $result = $this->recruitment_portal_model->archive_submisson($submission_id);
      
        if ($result) {
          echo json_encode(['success' => true]);
        } else {
          echo json_encode(['success' => false, 'error' => 'Failed to delete email template.']);
        }
    }

    public function unarchive_submisson() {
        $submission_id = $this->input->post('submissionId');
        $result = $this->recruitment_portal_model->unarchive_submisson($submission_id);
      
        if ($result) {
          echo json_encode(['success' => true]);
        } else {
          echo json_encode(['success' => false, 'error' => 'Failed to delete email template.']);
        }
    }

    public function email_templates()
    {
        $data['campaigns'] = $this->recruitment_portal_model->get_campaigns();
        $this->load->view('email_templates/manage', $data);
    }
    
    public function add_email_template() {
        if ($this->input->is_ajax_request()) {
    
            $data = array(
                'template_name' => $this->input->post('name'),
                'template_subject' => $this->input->post('subject'),
                'template_body' => $this->input->post('body'),
                'created_at' => date('Y-m-d H:i:s')
            );

            $template_id = $this->recruitment_portal_model->add_email_template($data);
    
            if ($template_id) {
                $campaign_ids = $this->input->post('campaign_ids');
                foreach ($campaign_ids as $campaign_id) {     
                    $this->recruitment_portal_model->associate_campaign($template_id, $campaign_id);
                }
                echo json_encode(array('success' => true, 'message' => 'Email template added successfully.'));
            } else {
                echo json_encode(array('success' => false, 'message' => 'Failed to add the email template.'));
            }
        } else {
            show_error('No direct script access allowed.');
        }
    }
    
    public function get_email_templates() {
        $id = $this->input->get("id");
        $templates = $this->recruitment_portal_model->get_email_templates($id, null);
        $data = [
            "email_templates" => $templates
        ];
    
        echo json_encode($data);
    }
    
    public function update_email_template() {
        $template_id = $this->input->post('templateId');
        $template_data = [
            'id' => $template_id,
            'template_name' => $this->input->post('name'),
            'template_subject' => $this->input->post('subject'),
            'template_body' => $this->input->post('body'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $campaign_ids = ($this->input->post('campaign_ids')) ? $this->input->post('campaign_ids') : [];

        $result = $this->recruitment_portal_model->update_email_template($template_data);
        
        if ($result) {
            $this->recruitment_portal_model->delete_associations($template_id);
            foreach ($campaign_ids as $campaign_id) {
                $this->recruitment_portal_model->associate_campaign($template_id, $campaign_id);
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update email template.']);
        }
    }
    
    public function delete_email_template() {
        $templateId = $this->input->post('templateId');
        $result = $this->recruitment_portal_model->delete_email_template($templateId);
      
        if ($result) {
          echo json_encode(['success' => true]);
        } else {
          echo json_encode(['success' => false, 'error' => 'Failed to delete email template.']);
        }
    }

    public function get_email_template_campaigns() {
        $templateId = $this->input->get('id');
        $this->load->model('recruitment_portal_model');
        $campaignIds = $this->recruitment_portal_model->get_email_template_campaigns($templateId);
      
        echo json_encode(['campaignIds' => $campaignIds]);
    }
    
    public function add_message() {

        if ($this->input->is_ajax_request()) {

            $this->load->library('email');

            $email = $this->input->post('email');
            $message = $this->input->post('message');
            $subject = $this->input->post('subject');
            $submission_id = $this->input->post('submission_id');

            $submission = $this->recruitment_portal_model->get_submission($submission_id);
            $fields_data = json_decode($submission->form_data, true);
            $name = (array_key_exists('Name', $fields_data)) ?  $fields_data['Name'] : '';
            $title = $submission->title;

            $this->email->initialize();
            $this->email->reply_to('recruitment+s_'.$submission_id.'@crm.zikrainfotech.com', 'Candidate Reply');
            $this->email->from("recruitment@crm.zikrainfotech.com", "Zikra Infotech LLC, HR");
            $this->email->to($email);

            $this->email->subject("Hey ".$name.", Update regarding your ".$title." application at Zikrainfotech LLC.");

            $this->email->message(get_option('email_header') . $message . get_option('email_footer'));

            $email_message_id = $this->recruitment_portal_model->get_submission_email_id($submission_id)['email_message_id'];

            if($email_message_id){
                $email_message_id = substr($email_message_id, 1);
                $email_message_ids = explode('|', $email_message_id);

                if($email_message_ids){
                    // Preparing the References string with all the email IDs separated by a space
                    $references = implode(' ', $email_message_ids);
                    
                    // Setting the headers
                    $this->email->set_header('References', $references);
                    $this->email->set_header('In-Reply-To', end($email_message_ids));
                }
            }

            $this->email->set_header('X-SES-CONFIGURATION-SET', 'Config_1');
            $this->email->set_header('submission_id', $submission_id);

            $email_sent = $this->email->send(false);


            // Check if the campaign was added successfully
            if ($email_sent) {

                // Get the form data from the AJAX request
                $data = array(
                    'submission_id' => $submission_id,
                    'subject' => $subject,
                    'message' => $message,
                    'sent_by' => 'admin',
                    'created_at' => date('Y-m-d H:i:s')
                );
        
                // Add the new campaign to the database using the model
                $message_id = $this->recruitment_portal_model->add_message($data);

                if($message_id){
                    echo json_encode(array('success' => true, 'message' => 'Message added successfully.'));
                }else{
                    echo json_encode(array('success' => false, 'message' => 'Failed to add the Message.'));

                }

            } else {
                echo json_encode(array('success' => false, 'message' => 'Failed to mail!'));
            }
        } else {
            show_error('No direct script access allowed.');
        }
    }

    function truncateReply($original) {
        $markers = array(
            'On Mon,', 'On Tue,', 'On Wed,', 'On Thu,', 'On Fri,', 'On Sat,', 'On Sun,',
            'On Monday,', 'On Tuesday,', 'On Wednesday,', 'On Thursday,', 'On Friday,', 'On Saturday,', 'On Sunday,',
            '-----Original Message-----'
            // Add more markers as needed
        );
    
        foreach ($markers as $marker) {
            $pos = strpos($original, $marker);
            if ($pos !== false) {
                return trim(substr($original, 0, $pos));
            }
        }
    
        // If no marker was found, return the whole message
        return $original;
    }
    
    public function submission_message_skip_auth(){

        $data = json_decode(file_get_contents('php://input'), true);

        $submission_id = $data['submission_id'];
        $message = $data['body'];
        $subject = $data['subject'];
        $message_id = $data['message_id'];

        $message = str_replace("\\n", "\n", $message);
        $message = str_replace("\\r", "\r", $message);
        $message = nl2br($message);

        $message = $this->truncateReply($message);


        $this->email->initialize();
        $this->email->from("recruitment@crm.zikrainfotech.com", "Recruitment Module");
        $this->email->to("hello@zikrainfotech.com");

        $this->email->subject("Candidate Response Recieved: ".$subject);
        $this->email->message(get_option('email_header') . '<b>Following is the response</b> <br>' . $message . get_option('email_footer'));

        $email_sent = $this->email->send();
        
        $this->recruitment_portal_model->set_submission_email_id($submission_id, $message_id);
        
        $data = array(
            'submission_id' => $submission_id,
            'subject' => $subject,
            'message' => $message,
            'sent_by' => 'candidate',
            'created_at' => date('Y-m-d H:i:s')
        );
        

        if($this->recruitment_portal_model->add_message($data)){
            if($this->recruitment_portal_model->unview_submission($submission_id)){
                echo json_encode(array('success' => true, 'message' => 'Message added successfully.'));
            }else{
                echo json_encode(array('success' => false, 'message' => 'Failed to add the Message.'));
            }
        }else{
            echo json_encode(array('success' => false, 'message' => 'Failed to add the Message.'));
        }
    }

    public function capture_message_id_cron_access(){
        // Get the raw POST data
        $data = file_get_contents('php://input');

        // Decode the data
        $message = json_decode($data, true);

        // The message's contents will be under the 'Message' key
        $messageContents = json_decode($message['Message'], true);

        // Extract the Message-ID and store it in your database
        $messageId = $messageContents['mail']['messageId'];

        // Extract the Submissions_Id
        $submissionId = 0;
        foreach ($messageContents['mail']['headers'] as $header) {
            if ($header['name'] == 'submission_id') {
                $submissionId = $header['value'];
                break;
            }
        }

        if($submissionId){

            $messageId = '<'.$messageId.'@email.amazonses.com>';

            $this->recruitment_portal_model->set_submission_email_id($submissionId, $messageId);
        }

    }

    public function save_campaign_permissions() {
        $campaign_id = $this->input->post('campaign_id');
        $permissions = json_decode($this->input->post('permissions'));
    
        if ($campaign_id && $permissions) {
            // Delete existing permissions for this campaign
            $this->recruitment_portal_model->delete_permissions($campaign_id);
    
            foreach ($permissions as $staff_id => $permission) {
                $data = array(
                    'campaign_id' => $campaign_id,
                    'staff_id' => $permission->id,
                    'can_view' => $permission->view,
                    'can_edit' => $permission->edit,
                    'can_act' => $permission->act
                );
    
                // Save new permission
                $this->recruitment_portal_model->save_permission($data);
            }
    
            $response = array('success' => true);
        } else {
            $response = array('success' => false, 'error' => 'Invalid request data.');
        }
    
        // Return the response as JSON
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function get_campaign_permissions() {
        $campaign_id = $this->input->post('campaign_id');
        
        if (!isset($campaign_id)) {
            echo json_encode(['success' => false, 'error' => 'Invalid campaign ID']);
            return;
        }

        $permissions = $this->recruitment_portal_model->get_campaign_permissions($campaign_id);
    
        if ($permissions === false) {
            echo json_encode(['success' => false, 'message' => 'Permissions dont exist']);
            return;
        }
    
        echo json_encode(['success' => true, 'data' => $permissions]);
    }
    
    public function fav_submission() {
        $submission_id = $this->input->post('id');
        $mark = $this->input->post('mark');

        $result = $this->recruitment_portal_model->fav_submisson($submission_id, $mark);
      
        if ($result) {
          echo json_encode(['success' => true]);
        } else {
          echo json_encode(['success' => false, 'error' => 'Failed to update mark.']);
        }
    }

    public function add_note() {
        $data = [
            'submission_id' => $this->input->post('submission_id'),
            'admin_id' => $this->session->userdata('staff_user_id'),
            'title' => $this->input->post('title'),
            'body' => $this->input->post('body'),
            'created_at' => date("Y-m-d H:i:s")
        ];
        $id = $this->recruitment_portal_model->add_note($data);
        echo json_encode(['id' => $id]);
    }

    public function update_note() {
        $id = $this->input->post('id');
        $data = [
            'title' => $this->input->post('title'),
            'body' => $this->input->post('body')
        ];
        $this->recruitment_portal_model->update_note($id, $data);
        echo json_encode(['success' => true]);
    }

    public function delete_note() {
        $id = $this->input->post('id');
        $this->recruitment_portal_model->delete_note($id);
        echo json_encode(['success' => true]);
    }

}
