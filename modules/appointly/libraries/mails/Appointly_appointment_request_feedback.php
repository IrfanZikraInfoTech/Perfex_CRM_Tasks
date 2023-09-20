<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Appointly_appointment_request_feedback extends App_mail_template
{
    protected $for = 'contact';

    public $slug = 'appointly-appointment-request-feedback';

    protected $appointment;

    public function __construct($appointment)
    {
        parent::__construct();

        $this->appointment = $appointment;

        // For SMS and merge fields for email
        $this->set_merge_fields('appointly_merge_fields', $this->appointment->id);
    }
    public function build()
    {
        $this->to($this->appointment->email);
    }
}
