<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Forms Controller
 */
class Forms extends ClientsController
{
    public function index()
    {
        show_404();
    }

    /**
     * Web to lead form
     * User no need to see anything like LEAD in the url, this is the reason the method is named wtl
     * @param  string $key web to lead form key identifier
     * @return mixed
     */
    public function wtl($rec_campaignid="",$key)
    {
        $this->load->model('recruitment_model');
        $form = $this->recruitment_model->get_form([
            'form_key' => $key

            ]);

        if (!$form) {
            show_404();
        }

        // Change the locale so the validation loader function can load
        // the proper localization file
        $GLOBALS['locale'] = get_locale_key($form->language);

        $data['form_fields'] = json_decode($form->form_data);
        if (!$data['form_fields']) {
            $data['form_fields'] = [];
        }
        if ($this->input->post('key')) {
            $data1 = $this->input->post();
            if ($this->input->post('key') == $key) {
                if(isset($data1['csrf_token_name'])){
                    unset($data1['csrf_token_name']);
                }
                $ids = $this->recruitment_model->add_candidate_forms($data1, $key);
                if ($ids) {
                    handle_rec_candidate_file_form($ids);
                    handle_rec_candidate_avar_file($ids);
                    $success = true;
                    $message = _l('added_successfully').' '. _l('candidate_profile');

                    $data['form'] = $form;
                    $data['message'] =$form->success_submit_msg;


                    echo json_encode([
                        'success' => $success,
                        'message' => $form->success_submit_msg,
                    ]);
                    die;
                    
                }
            }
        }
        $data['form'] = $form;
        $data['rec_campaignid'] = $rec_campaignid;
        $this->load->view('forms/recruitment_channel_form', $data);
    }

    /**
     * Web to lead form
     * User no need to see anything like LEAD in the url, this is the reason the method is named eq lead
     * @param  string $hash lead unique identifier
     * @return mixed
     */
    public function l($hash)
    {
        if (get_option('gdpr_enable_lead_public_form') == '0') {
            show_404();
        }
        $this->load->model('leads_model');
        $this->load->model('gdpr_model');
        $lead = $this->leads_model->get('', ['hash' => $hash]);

        if (!$lead || count($lead) > 1) {
            show_404();
        }

        $lead = array_to_object($lead[0]);
        load_lead_language($lead->id);

        if ($this->input->post('update')) {
            $data = $this->input->post();
            unset($data['update']);
            $this->leads_model->update($data, $lead->id);
            redirect($_SERVER['HTTP_REFERER']);
        } elseif ($this->input->post('export') && get_option('gdpr_data_portability_leads') == '1') {
            $this->load->library('gdpr/gdpr_lead');
            $this->gdpr_lead->export($lead->id);
        } elseif ($this->input->post('removal_request')) {
            $success = $this->gdpr_model->add_removal_request([
                'description'  => nl2br($this->input->post('removal_description')),
                'request_from' => $lead->name,
                'lead_id'      => $lead->id,
            ]);
            if ($success) {
                send_gdpr_email_template('gdpr_removal_request_by_lead', $lead->id);
                set_alert('success', _l('data_removal_request_sent'));
            }
            redirect($_SERVER['HTTP_REFERER']);
        }

        $lead->attachments = $this->leads_model->get_lead_attachments($lead->id);
        $this->disableNavigation();
        $this->disableSubMenu();
        $data['title'] = $lead->name;
        $data['lead']  = $lead;
        $this->view('forms/lead');
        $this->data($data);
        $this->layout(true);
    }

    /**
     * ticket
     * @return view
     */
    public function ticket()
    {
        $form            = new stdClass();
        $form->language  = get_option('active_language');
        $form->recaptcha = 1;

        $this->lang->load($form->language . '_lang', $form->language);
        if (file_exists(APPPATH . 'language/' . $form->language . '/custom_lang.php')) {
            $this->lang->load('custom_lang', $form->language);
        }

        $form->success_submit_msg = _l('success_submit_msg');

        $form = hooks()->apply_filters('ticket_form_settings', $form);

        if ($this->input->post() && $this->input->is_ajax_request()) {
            $post_data = $this->input->post();

            $required = ['subject', 'department', 'email', 'name', 'message', 'priority'];

            if (is_gdpr() && get_option('gdpr_enable_terms_and_conditions_ticket_form') == 1) {
                $required[] = 'accept_terms_and_conditions';
            }

            foreach ($required as $field) {
                if (!isset($post_data[$field]) || isset($post_data[$field]) && empty($post_data[$field])) {
                    $this->output->set_status_header(422);
                    die;
                }
            }

            if (get_option('recaptcha_secret_key') != '' && get_option('recaptcha_site_key') != '' && $form->recaptcha == 1) {
                if (!do_recaptcha_validation($post_data['g-recaptcha-response'])) {
                    echo json_encode([
                            'success' => false,
                            'message' => _l('recaptcha_error'),
                            ]);
                    die;
                }
            }

            $post_data = [
                    'email'      => $post_data['email'],
                    'name'       => $post_data['name'],
                    'subject'    => $post_data['subject'],
                    'department' => $post_data['department'],
                    'priority'   => $post_data['priority'],
                    'service'    => isset($post_data['service']) && is_numeric($post_data['service'])
                    ? $post_data['service']
                    : null,
                    'custom_fields' => isset($post_data['custom_fields']) && is_array($post_data['custom_fields'])
                    ? $post_data['custom_fields']
                    : [],
                    'message' => $post_data['message'],
            ];

            $success = false;

            $this->db->where('email', $post_data['email']);
            $result = $this->db->get(db_prefix() . 'contacts')->row();

            if ($result) {
                $post_data['userid']    = $result->userid;
                $post_data['contactid'] = $result->id;
                unset($post_data['email']);
                unset($post_data['name']);
            }

            $this->load->model('tickets_model');

            $post_data = hooks()->apply_filters('ticket_external_form_insert_data', $post_data);
            $ticket_id = $this->tickets_model->add($post_data);

            if ($ticket_id) {
                $success = true;
            }

            if ($success == true) {
                hooks()->do_action('ticket_form_submitted', [
                        'ticket_id' => $ticket_id,
                     ]);
            }

            echo json_encode([
                    'success' => $success,
                    'message' => $form->success_submit_msg,
                    ]);

            die;
        }

        $this->load->model('tickets_model');
        $this->load->model('departments_model');
        $data['departments'] = $this->departments_model->get();
        $data['priorities']  = $this->tickets_model->get_priority();

        $data['priorities']['callback_translate'] = 'ticket_priority_translate';
        $data['services']                         = $this->tickets_model->get_service();

        $data['form'] = $form;
        $this->load->view('forms/ticket', $data);
    }

        /**
     * add candidate form recruitment channel
     * @param redirect
     */
    public function add_candidate_form_recruitment_channel($form_key) {
        $data = $this->input->post();
        if ($data) {
            $ids = $this->recruitment_model->add_candidate_forms($data, $form_key);
            if ($ids) {
                handle_rec_candidate_file_form($ids);
                handle_rec_candidate_avar_file($ids);
                $success = true;
                $message = _l('added_successfully', _l('candidate_profile'));
                set_alert('success', $message);
                redirect(site_url('recruitment/forms/wtl/' . $form_key));
            }
        }
    }


}
