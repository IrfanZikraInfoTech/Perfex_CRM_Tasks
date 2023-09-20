<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'third_party/google-api-php-client-main/vendor/autoload.php';

class Custom_Google_client extends Google_Client
{
    public function __construct()
    {
        parent::__construct();
    }
}
