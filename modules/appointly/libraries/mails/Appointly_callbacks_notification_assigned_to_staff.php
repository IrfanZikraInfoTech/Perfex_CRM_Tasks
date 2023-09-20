<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Appointly_callbacks_notification_assigned_to_staff extends App_mail_template
{
    protected $for = 'staff';

    public $slug = 'callback-assigned-to-staff';

    protected $staff;

    public function __construct($staff)
    {
        parent::__construct();

        $this->staff = $staff;

        // For SMS and merge fields for email
        $this->set_merge_fields('staff_merge_fields', $this->staff->staffid);
    }
    public function build()
    {
        $this->to($this->staff->email);
    }
}
