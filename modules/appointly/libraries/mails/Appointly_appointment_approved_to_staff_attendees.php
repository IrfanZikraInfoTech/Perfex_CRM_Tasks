<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Appointly_appointment_approved_to_staff_attendees extends App_mail_template
{
    protected $for = 'staff';

    protected $appointment;

    protected $staff;

    public $slug = 'appointment-approved-to-staff';

    public function __construct($appointment, $staff)
    {
        parent::__construct();

        $this->appointment = $appointment;
        $this->staff = $staff;

        // For SMS and merge fields for email
        $this->set_merge_fields('appointly_merge_fields', $this->appointment->id);
        $this->set_merge_fields('staff_merge_fields', $this->staff->staffid);
    }
    public function build()
    {
        $this->to($this->staff->email);
    }
}
