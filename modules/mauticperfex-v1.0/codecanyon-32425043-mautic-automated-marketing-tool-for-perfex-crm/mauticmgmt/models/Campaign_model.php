<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Campaign_model extends App_Model
{

    public function __construct()
    {

        parent::__construct();

    }

    public function insert_campaign($data)
    {

        $this->db->insert(db_prefix() . 'mautic_campaign', $data);

        return true;

    }

    public function check_campaign_exist($id)
    {

        $this->db->where('id =', $id);

        $array = $this->db->get(db_prefix() . 'mautic_campaign')->result_array();

        return $array;
    }

}
