<?php

use app\services\utilities\Str;

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard_model');
        $this->load->model('newsfeed_model');
        $this->load->model('team_management_model');
        $this->load->model('staff_model');
        $this->load->library('webhook_library', null, 'webhook_lib');
    }

    public function index(){
        $this->load->view('admin/management/kudos');
    }

}