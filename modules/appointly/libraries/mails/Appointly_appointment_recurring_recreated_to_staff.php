<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Appointly_appointment_recurring_recreated_to_staff extends App_mail_template
{
    protected $for = 'staff';

    protected $appointment;

    protected $staff;

    public $slug = 'appointment-recurring-to-staff';

    public function __construct($appointment, $staff)
    {
        parent::__construct();

        $this->appointment = $appointment;
        $this->staff = $staff;

        $this->set_merge_fields('appointly_merge_fields', $this->appointment->id);
        $this->set_merge_fields('staff_merge_fields', $this->staff->staffid);
    }

    public function build()
    {
        $this->to($this->staff->email);
    }

}
