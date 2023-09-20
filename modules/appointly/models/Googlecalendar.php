<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Googlecalendar extends App_Model
{

    public $table;

    protected $googleReminders;


    public function __construct()
    {
        parent::__construct();

        $this->load->library('appointly/googleplus');

        $this->unauthorized_url_settings = '<h3>Seems that something is wrong, please check that your Client Secret is correctly inserted in <a href="' . admin_url('settings?group=appointly-settings') . '">Setup->Settings->Appointments</a></h3>';

        $this->table = db_prefix() . 'appointly_google';

        $this->googleReminders = [
            'useDefault' => false,
            'overrides'  => [
                ['method' => 'email', 'minutes' => 24 * 60],
                ['method' => 'popup', 'minutes' => 60],
            ],
        ];
        /**
         * Init Google Calendar Service instance
         */
        $this->calendar = new Google_Service_Calendar($this->googleplus->client());
    }


    /**
     * Google OAuth Login Url
     *
     * @return void
     */
    public function loginUrl()
    {
        return $this->googleplus
            ->loginUrl();
    }


    /**
     * Google OAuth Login validation
     *
     * @return bool
     */
    public function login($code)
    {
        try {
            $login = $this->googleplus
                ->client
                ->authenticate($code);
        } catch (GuzzleHttp\Exception\ClientException $e) {
            if ($e->getCode() === 401) {
                die($this->unauthorized_url_settings);
            }
        }
        if (isset($login['error_description']) && $login['error_description'] == 'Unauthorized') {
            die($this->unauthorized_url_settings);
        }

        if ($login) {

            $this->db->where('staff_id', get_staff_user_id());
            $result = $this->db->get($this->table)->row_array();

            if ($result) {
                $this->db->where('staff_id', get_staff_user_id());

                $this->db->update($this->table, [
                    'access_token'  => $login['access_token'],
                    'expires_in'    => time() + $login['expires_in'],
                    'refresh_token' => $login['refresh_token'],
                ]);
            } else {
                if (isset($login['refresh_token'])) {
                    $this->db->insert($this->table, [
                        'staff_id'      => get_staff_user_id(),
                        'access_token'  => $login['access_token'],
                        'refresh_token' => $login['refresh_token'],
                        'expires_in'    => time() + $login['expires_in'],
                    ]);
                } else {
                    $this->db->insert($this->table, [
                        'staff_id'     => get_staff_user_id(),
                        'access_token' => $login['access_token'],
                        'expires_in'   => time() + $login['expires_in'],
                    ]);
                }
            }

            $token = $this->googleplus
                ->client
                ->getAccessToken();

            $this->googleplus
                ->client
                ->setAccessToken($token);

            $this->saveNewTokenValues($token);

            return true;
        }
        return false;
    }


    /**
     * Google get email user info
     *
     * @return array
     */
    public function getUserInfo()
    {
        return $this->googleplus->getUser();
    }


    /**
     * Google Calendar delete event
     *
     * @param $eventid
     * @return bool
     */
    public function deleteEvent($eventid)
    {
        try {
            $this->googlecalendar->calendar->events->delete('primary', $eventid);
        } catch (Exception $e) {
            /**
             * This means that event is not found and return true so error will be not thrown
             * Just a percusion when relogging with gmail accounts sometimes can get messy
             */
            if ($e->getCode() === 404) {
                return true;
            }
        }
    }

    /**
     * Google Calendar get events
     *
     * @param string $calendarId
     * @param bool   $timeMin
     * @param bool   $timeMax
     * @param int    $maxResults
     * @return array
     */
    public function getEvents($calendarId = 'primary', $timeMin = false, $timeMax = false, $maxResults = 200)
    {

        if ( ! $timeMin) {

            $timeMin = date("c", strtotime("-6 months", strtotime(date('Y-m-d') . ' 00:00:00')));
        } else {

            $timeMin = date("c", strtotime($timeMin));
        }


        if ( ! $timeMax) {

            $timeMax = date("c", strtotime("+6 months", strtotime(date('Y-m-d') . ' 23:59:59')));
        } else {

            $timeMax = date("c", strtotime($timeMax));
        }

        $optParams = [
            'maxResults'   => $maxResults,
            'orderBy'      => 'startTime',
            'singleEvents' => true,
            'timeMin'      => $timeMin,
            'timeMax'      => $timeMax,
            'timeZone'     => get_option('default_timezone'),

        ];

        /**
         * Optional: Get all events
         */
        // $results = $this->googlecalendar->calendar->events->listEvents($calendarId);

        /**
         * Get all events from past 12 months START FROM CURRENT MONTH
         */
        $results = $this->googlecalendar->calendar->events->listEvents($calendarId, $optParams);

        $data = [];

        foreach ($results->getItems() as $item) {

            array_push(
                $data,
                [

                    'id'          => $item->getId(),
                    'summary'     => $item->getSummary(),
                    'description' => $item->getDescription(),
                    'creator'     => $item->getCreator(),
                    'start'       => $item->getStart()->dateTime,
                    'end'         => $item->getEnd()->dateTime,
                ]

            );
        }

        return $data;
    }


    /**
     * Google Calendar add new event
     *
     * @param string $calendarId
     * @param        $data
     * @return Google_Service_Calendar_Event
     */
    public function addEvent($data, $calendarId = 'primary')
    {
        $event = $this->fillGoogleCalendarEvent($data);

        return $this->calendar->events->insert($calendarId, $event, ['conferenceDataVersion' => 1]);
    }


    /**
     * Google Calendar update existing event
     *
     * @param $eventid
     * @param $data
     * @return Google_Service_Calendar_Event
     */
    public function updateEvent($eventid, $data)
    {
        $event = $this->fillGoogleCalendarEvent($data);

        return $this->calendar->events->update('primary', $eventid, $event);
    }

    /**
     * Manage data and fill google calendar event array
     *
     * @param $data
     * @return Google_Service_Calendar_Event of Google_Service_Calendar_Event
     */
    public function fillGoogleCalendarEvent($data)
    {
        return new Google_Service_Calendar_Event(
            [
                'summary'        => $data['summary'],
                'description'    => $data['description'],
                'location'       => ($data['location']) ? $data['location'] : '',
                'sendUpdates'    => 'all',
                'start'          => [
                    'dateTime' => $data['start'],
                    'timeZone' => get_option('default_timezone'),
                ],
                'end'            => [
                    'dateTime' => $data['start'],
                    'timeZone' => get_option('default_timezone'),
                ],
                'attendees'      => (array) $data['attendees'],
                'reminders'      => $this->googleReminders,
                'conferenceData' => [
                    "createRequest" => [
                        "conferenceSolutionKey" => ["type" => "hangoutsMeet"],
                        "requestId"             => generate_encryption_key() . time()
                    ]
                ]
            ]
        );
    }

    /**
     * Get logged in Google account details
     *
     * @return mixed
     */
    public function getAccountDetails()
    {
        $this->db->select();
        $this->db->where('staff_id', get_staff_user_id());
        $result = $this->db->get($this->table)->result();

        if ($result) {
            return $result;
        }
        return false;
    }


    /**
     * Google save / update new token values in database
     *
     * @param $data
     * @return void
     */
    public function saveNewTokenValues($data)
    {
        $this->db->where('staff_id', get_staff_user_id());
        $this->db->update(
            $this->table,
            [
                'refresh_token' => $data['refresh_token'],
                'access_token'  => $data['access_token'],
                'expires_in'    => time() + $data['expires_in']
            ]
        );
    }

}
