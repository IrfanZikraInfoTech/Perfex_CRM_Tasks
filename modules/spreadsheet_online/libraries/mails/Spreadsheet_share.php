<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Spreadsheet_share extends App_mail_template
{

    protected $for = 'staff';

    protected $spreadsheet;

    public $slug = 'spreadsheet-share-assigned';
    public function __construct($spreadsheet)
    {
        parent::__construct();

        $this->spreadsheet = $spreadsheet;
        // For SMS and merge fields for email
        $this->set_merge_fields('spreadsheet_share_merge_fields', $this->spreadsheet);
    }
    public function build()
    {
        $this->to($this->spreadsheet->receiver);
    }
}
