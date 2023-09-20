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
        $this->load->view('campaigns/manage');
    }

    public function add_campaign() {
        if ($this->input->is_ajax_request()) {
            // Load the required model
            $this->load->model('recruitment_portal_model');
    
            // Get the form data from the AJAX request
            $data = array(
                'title' => $this->input->post('title'),
                'position' => $this->input->post('position'),
                'description' => $this->input->post('description'),
                'start_date' => $this->input->post('start_date'),
                'end_date' => $this->input->post('end_date'),
                'status' => $this->input->post('status'),
                'salary' => $this->input->post('salary'),
                'created_at' => date('Y-m-d H:i:s')
            );
    
            // Add the new campaign to the database using the model
            $campaign_id = $this->recruitment_portal_model->add_campaign($data);

            $defaultFormData = array(
                array("name" => "Name", "type" => "text", "order" => "1", "maxLength"=>45),
                array("name" => "Email", "type" => "email", "order" => "2"),
                array("name" => "Age", "type" => "numbers", "order" => "3", "min"=>15, "max"=>50),
                array("name" => "Gender", "type" => "select", "order" => "4", "options" => json_encode(array("Male", "Female", "Rather not say")))
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
            'salary' => $this->input->post('salary')
        ];
    
        $result = $this->recruitment_portal_model->update_campaign($campaign_data);
    
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
        $formFieldsData = $this->input->post('form_fields_data');
        $campaignId = $this->input->post('campaign_id');// Get the campaign ID
    
        // Save the form fields data to the database
        $this->recruitment_portal_model->save_form_fields($campaignId, $formFieldsData);
    }

    public function career(){
        $data['activeCampaigns'] = $this->recruitment_portal_model->get_active_campaigns();
        $this->load->view('career/index', $data);
    }

    public function apply($id){
        $data['campaign'] = $this->recruitment_portal_model->get_campaigns($id);
        $data['form_fields'] = $this->recruitment_portal_model->get_form_fields($id);
        $data['csrf_name'] = $this->security->get_csrf_token_name();
        $data['csrf_token'] = $this->security->get_csrf_hash();
        $this->load->view('career/apply', $data);
    }

    public function submissions($campaign_id = null, $status = null){
        if(!$campaign_id){
            $data['campaigns'] = $this->recruitment_portal_model->get_campaigns($campaign_id);
            $this->load->view('submissions/index', $data);
        }else{
            $data['id'] = $campaign_id;
            $data['status'] = ($status != null) ? $status : "-1";
            $data['campaign_name'] = $this->recruitment_portal_model->get_campaigns($campaign_id)->title;
            $data['templates'] = $this->recruitment_portal_model->get_email_templates(null, $campaign_id);
            $this->load->view('submissions/submissions', $data);
        }
        
    }
    
    public function handle_submission() {
        // Retrieve submitted data
        $campaign_id = $this->input->post('campaign_id');
        $form_data = $this->input->post();
    
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
            echo json_encode(['success' => true]);
        }
    }
    
    public function get_submissions($campaign_id = null, $status = null) {

        
        $submissions = ($campaign_id != null && $status != null) ? $this->recruitment_portal_model->get_submissions($campaign_id, $status) : $this->recruitment_portal_model->get_submissions();

        

        $dataSubmissions = [];
        foreach ($submissions as $submission) {
            
            $fields_data = json_decode($submission->form_data, true);
            $name = '';

            if(array_key_exists('Name', $fields_data)){
                $name = $fields_data['Name'];
            }

            $date = DateTime::createFromFormat('Y-m-d H:i:s', $submission->created_at);
            // Format the date as needed
            $readable_date = $date->format('F j, Y, g:i a');

        
            $dataSubmissions[] = [
                'id' => $submission->campaign_id,
                'sub' => $submission->id,
                'campaign' => $submission->title,
                'name' => $name,
                'submission_date' => $readable_date,
                'resume'  => $submission->resume,
                'status' => $submission->status,
                'is_archived' => $submission->is_archived
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
        $this->load->view('submissions/view_resume', ['file_name' => $file_name]);
    }

    public function view_submission($submission_id) {
        $data['submission'] = $this->recruitment_portal_model->get_submission($submission_id);
        $data['templates'] = $this->recruitment_portal_model->get_email_templates(null, $data['submission']->campaign_id);
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

        if(!empty($email)){

            $this->recruitment_portal_model->act_submission($submission_id, $status);

            $this->email->initialize();
            $this->email->set_newline(PHP_EOL);
            $this->email->from(get_option('smtp_email'), get_option('companyname'));
            $this->email->to($email);

            $this->email->subject($subject);
            $this->email->message(get_option('email_header') . $body . get_option('email_footer'));

            $email_sent = $this->email->send();

            echo json_encode(["success" => true, "message" => "Email sent successfully."]);

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
                foreach ($campaign_ids as $campaign_id) {
                    $campaign_ids = $this->input->post('campaign_ids');
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
    
    
    

    
}
