<?php

defined('BASEPATH') or exit('No direct script access allowed');
set_time_limit(0);
use Mautic\Auth\ApiAuth;
use Mautic\MauticApi;

class Campaign extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('campaign_model');
        $this->load->model('Mauticsettings_model');

    }

    public function index()
    {

        $data['title'] = _l('mautic_new_campaign');

        $this->load->view('mautic_new_campaign', $data);

    }

    public function campaign_list()
    {

        $data['settings'] = $this->Mauticsettings_model->get_api_settings();

        // ApiAuth->newAuth() will accept an array of Auth settings
        $settings = [
            'userName' => $data['settings'][0]['public_key'], // Create a new user
            'password' => $data['settings'][0]['secret_key'], // Make it a secure password
        ];
        $apiUrl = $data['settings'][0]['mautic_base_url'];

        // Initiate the auth object specifying to use BasicAuth
        $initAuth = new ApiAuth();
        $auth = $initAuth->newAuth($settings, 'BasicAuth');

        $api = new MauticApi();
        $campaignApi = $api->newApi("campaigns", $auth, $apiUrl);
        $campaigns = $campaignApi->getList('', '', '', '', 'desc', '', '');

        $response_campaigns = $campaigns['campaigns'];

        foreach ($response_campaigns as $cmp) {

            $id = $cmp['id'];
            $exist = $this->campaign_model->check_campaign_exist($id);

            if (empty($exist)) {

                $data2 = array(
                    'id' => $cmp['id'],
                    'mautic_campaign_name' => $cmp['name'],
                    'mautic_campaign_desc' => $cmp['description'],
                    'mautic_campaign_published' => $cmp['isPublished'],
                    'mautic_campaign_category' => $cmp['category']['title'],

                );
                $this->campaign_model->insert_campaign($data2);

            }

        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('mauticmgmt', 'mautic_campaign_list'));
        }
        $data['title'] = _l('mautic_campaign_list');
        $this->load->view('mautic_campaign', $data);

    }

    public function mautic_contact_add_to_campaign()
    {

        $data['settings'] = $this->Mauticsettings_model->get_api_settings();

        // ApiAuth->newAuth() will accept an array of Auth settings
        $settings = [
            'userName' => $data['settings'][0]['public_key'], // Create a new user
            'password' => $data['settings'][0]['secret_key'], // Make it a secure password
        ];
        $apiUrl = $data['settings'][0]['mautic_base_url'];

        // Initiate the auth object specifying to use BasicAuth
        $initAuth = new ApiAuth();
        $auth = $initAuth->newAuth($settings, 'BasicAuth');

        $api = new MauticApi();
        $contactApi = $api->newApi("contacts", $auth, $apiUrl);

        $campaignApi = $api->newApi("campaigns", $auth, $apiUrl);
        $campaigns = $campaignApi->getList('', 0, '', '', '', '', '');

        $contacts = $contactApi->getList('', 0, '', '', '', '1', '');

        $data['campaigns'] = $campaigns['campaigns'];
        $data['contacts'] = $contacts['contacts'];
        $data['title'] = _l('mautic_contact_add_to_campaign');

        $this->load->view('mautic_contact_add_to_campaign', $data);

    }

    public function mautic_add_contact()
    {

        $data['settings'] = $this->Mauticsettings_model->get_api_settings();

        // ApiAuth->newAuth() will accept an array of Auth settings
        $settings = [
            'userName' => $data['settings'][0]['public_key'], // Create a new user
            'password' => $data['settings'][0]['secret_key'], // Make it a secure password
        ];
        $apiUrl = $data['settings'][0]['mautic_base_url'];

        // Initiate the auth object specifying to use BasicAuth
        $initAuth = new ApiAuth();
        $auth = $initAuth->newAuth($settings, 'BasicAuth');

        $api = new MauticApi();
        $campaignApi = $api->newApi("campaigns", $auth, $apiUrl);

        $select_campaign = $this->input->post('select_campaign', false);
        $select_user = $this->input->post('select_user', false);

        $response = $campaignApi->addContact($select_campaign, $select_user);

        if (isset($response['success'])) {

            set_alert('success', _l('mautic_contact_added', _l('mautic')));
            redirect(admin_url('mauticmgmt/campaign/campaign_list'));

        }

    }

}
