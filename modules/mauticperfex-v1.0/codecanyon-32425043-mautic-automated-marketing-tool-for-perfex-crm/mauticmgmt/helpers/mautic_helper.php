<?php defined('BASEPATH') or exit('No direct script access allowed');

use Mautic\Auth\ApiAuth;
use Mautic\MauticApi;

function get_lead($array)
{
    $CI = &get_instance();
    $data['settings'] = $CI->db->query('Select * from ' . db_prefix() . 'mautic_settings where id = 1')->result_array();

    //ApiAuth->newAuth() will accept an array of Auth settings
    $settings = [
        'userName' => $data['settings'][0]['public_key'], // Create a new user
        'password' => $data['settings'][0]['secret_key'], // Make it a secure password
    ];
    $apiUrl = $data['settings'][0]['mautic_base_url'];

    // Initiate the auth object specifying to use BasicAuth
    $initAuth = new ApiAuth();
    $auth = $initAuth->newAuth($settings, 'BasicAuth');

    $api = new MauticApi();
    $contactApi = $api->newApi('contacts', $auth, $apiUrl);

    $name = $array['name'];
    $email = $array['email'];
    if ($array['is_public'] == 0) {
        $published = 0;
    } else {

        $published = 1;
    }
    $points = $array['lead_value'];

    $lead_data = array(
        'firstname' => $name,
        'lastname' => '',
        'email' => $email,
        'ipAddress' => $_SERVER['REMOTE_ADDR'],
        'overwriteWithBlank' => true,
        'isPublished' => $published,
    );

    $contact = $contactApi->create($lead_data);

}
