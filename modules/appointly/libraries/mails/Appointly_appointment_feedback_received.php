<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Appointly_appointment_feedback_received extends App_mail_template
{
     protected $for = 'staff';

     public $slug = 'appointly-appointment-feedback-received';

     protected $staff;

     protected $appointment;


     public function __construct($staff, $appointment)
     {
          parent::__construct();

          $this->staff = $staff;
          $this->appointment = $appointment;

          // For SMS and merge fields for email
          $this->set_merge_fields('staff_merge_fields', $this->staff->staffid);
          $this->set_merge_fields('appointly_merge_fields', $this->appointment->id);
     }
     public function build()
     {
          $this->to($this->staff->email);
     }
}
