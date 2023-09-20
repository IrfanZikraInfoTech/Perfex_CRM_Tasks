<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Service_send_reminder extends Reminder_mail_template
{
    protected $for = 'contact';
    protected $reminder_id;
    protected $reminder_data;
    protected $user_email;
    public $slug = 'reminder-service-send-to-contact';
    public $rel_type = 'client';
    public function __construct($reminder)
    {
        parent::__construct();
        $this->reminder_id     = $reminder['id'];
        $this->user_email = $reminder['email'];
        $this->reminder_data = $reminder;
        // For SMS
        $this->set_reminder_merge_fields('service_merge_fields', $this->reminder_id, $this->reminder_data);
    }
    public function build()
    {
        $this->to($this->user_email)
            ->set_rel_id($this->reminder_id);
    }
}
