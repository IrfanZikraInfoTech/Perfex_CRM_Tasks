<?php

defined('BASEPATH') or exit('No direct script access allowed');
set_time_limit(0);

class Settings extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mauticsettings_model');
        $this->load->model('leads_model');

    }

    public function index()
    {

        $data['settings'] = $this->Mauticsettings_model->get_api_settings();
        $data['title'] = _l('mautic_settings');
        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        $data['statuses'] = $this->leads_model->get_status();
        $data['sources'] = $this->leads_model->get_source();

        $this->load->view('mautic_api_settings', $data);

    }

    public function settings_submit()
    {

        $data['public_key'] = $this->input->post('public_key', false);
        $data['secret_key'] = $this->input->post('secret_key', false);
        $data['mautic_base_url'] = $this->input->post('mautic_base_url', false);

        $data['mautic_lead_status'] = $this->input->post('view_status', false);
        $data['mautic_view_source'] = $this->input->post('view_source', false);
        $data['mautic_lead_assigned'] = $this->input->post('staffid', false);

        $id = $this->Mauticsettings_model->update_mautic_settings($data);
        $data['title'] = _l('mautic_settings');

        if ($id) {

            set_alert('success', _l('update_mautic_settings', _l('mautic')));
            redirect(admin_url('mauticmgmt/settings'));
        }

    }

}
