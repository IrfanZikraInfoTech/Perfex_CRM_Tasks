<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Spreadsheet_share_client extends App_mail_template
{

    protected $for = 'client';

    protected $spreadsheet_client;

    public $slug = 'spreadsheet-share-assigned-client';
    public function __construct($spreadsheet_client)
    {
        parent::__construct();

        $this->spreadsheet_client = $spreadsheet_client;
        // For SMS and merge fields for email
        $this->set_merge_fields('spreadsheet_share_merge_fields', $this->spreadsheet_client);
    }
    public function build()
    {
        $this->to($this->spreadsheet_client->receiver);
    }
}
