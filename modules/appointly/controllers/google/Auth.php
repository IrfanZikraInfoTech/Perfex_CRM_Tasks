<?php defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('googlecalendar');
    }

    public function login()
    {
        $data = array('loginUrl' => $this->googlecalendar->loginUrl());

        if (!appointlyGoogleAuth()) {
            redirect($data['loginUrl']);
        } else {
            redirect('admin/appointly/appointments');
        }
    }

    public function oauth()
    {
        $code = $this->input->get('code', true);

        if (!$code) {
            $this->login();
        }

        $this->googlecalendar->login($code);
        redirect(admin_url() . APPOINTLY_MODULE_NAME . '/appointments');
    }

    public function logout()
    {
        $this->googleplus->revokeToken();
        redirect('admin/appointly/appointments');
    }
}
