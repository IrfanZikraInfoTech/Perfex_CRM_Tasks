<?php defined('BASEPATH') or exit('No direct script access allowed');

require('modules/appointly/vendor/autoload.php');

class Googleplus
{

    /**
     * Googleplus constructor.
     */
    public function __construct()
    {
        $this->initGoogleClient();
        $this->thenVerifySSL();
    }

    /**
     * Client instance
     *
     * @return Google Client instance
     */
    public function client()
    {
        return $this->client;
    }


    /**
     * @return mixed
     */
    public function loginUrl()
    {
        return $this->client->createAuthUrl();
    }


    /**
     * @return mixed
     */
    public function getAuthenticate()
    {
        return $this->client->authenticate();
    }


    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->client->getAccessToken();
    }


    /**
     * @param $token
     *
     * @return mixed
     */
    public function setAccessToken($token)
    {
        return $this->client->setAccessToken($token);
    }

    /**
     * @param $token
     *
     * @return mixed
     */
    public function refreshToken($token)
    {
        return $this->client->refreshToken($token);
    }

    /**
     * @return mixed
     */
    public function isAccessTokenExpired()
    {
        return $this->client->isAccessTokenExpired();
    }

    /**
     * @return mixed
     */
    public function revokeToken()
    {
        $ci = &get_instance();
        $token = $this->client->setAccessToken($this->getTokenType('access_token'));
        $ci->db->where('staff_id', get_staff_user_id());
        $ci->db->delete(db_prefix() . 'appointly_google');
        return $this->client->revokeToken($token);
    }

    /**
     * @param $type
     *
     * @return mixed
     */
    public function getTokenType($type)
    {
        $ci = &get_instance();
        $ci->db->select($type);
        $ci->db->where('staff_id', get_staff_user_id());
        return $ci->db->get(db_prefix() . 'appointly_google')->row_array()[$type];
    }

    /**
     * Verify http or https
     */
    private function thenVerifySSL()
    {
        $httpClient = new GuzzleHttp\Client([
            'verify' => false,
        ]);

        $this->client->setHttpClient($httpClient);
    }

    /**
     * Init new Google_Client
     */
    private function initGoogleClient()
    {
        $this->client = new Google_Client();
        $this->client->setAccessType("offline");
        $this->client->setApprovalPrompt("force");
        $this->client->setApplicationName('Appointly Google Calendar API');
        $this->client->setClientId(get_option('google_client_id'));
        $this->client->setClientSecret(get_option('appointly_google_client_secret'));
        $this->client->setRedirectUri(base_url() . 'appointly/google/auth/oauth');
        $this->client->addScope(Google_Service_Calendar::CALENDAR);
    }
}
