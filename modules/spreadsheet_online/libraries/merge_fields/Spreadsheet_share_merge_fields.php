<?php

defined('BASEPATH') or exit('No direct script access allowed');

class spreadsheet_share_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Name spreadsheet',
                'key'       => '{name_spreadsheet}',
                'available' => [
                    'spreadsheet_online',
                ],
            ],
            [
                'name'      => 'Type spreadsheet',
                'key'       => '{type_spreadsheet}',
                'available' => [
                    'spreadsheet_online',
                ],
            ],
            [
                'name'      => 'Share link',
                'key'       => '{share_link_spreadsheet}',
                'available' => [
                    'spreadsheet_online',
                ],
            ],
            [
                'name'      => 'Receiver',
                'key'       => '{receiver}',
                'available' => [
                    'spreadsheet_online',
                ],
            ],
            [
                'name'      => 'sender',
                'key'       => '{sender}',
                'available' => [
                    'spreadsheet_online',
                ],
            ],
            [
                'name'      => 'share link client spreadsheet',
                'key'       => '{share_link_client_spreadsheet}',
                'available' => [
                    'spreadsheet_online',
                ],
            ]
          
        ];
    }

    /**
     * Merge field for appointments
     * @param  mixed $hira 
     * @return array
     */
    public function format($share_obj)
    {

        $this->ci->load->model('spreadsheet_online/spreadsheet_online_model');
        $fields['{name_spreadsheet}']   = $share_obj->name;
        $fields['{type_spreadsheet}'] = $share_obj->type;

        if($share_obj->type_template == "staff_template"){
            $response = $this->ci->spreadsheet_online_model->get_hash('staff', $share_obj->staff_share_id, $share_obj->id);

            $fields['{share_link_spreadsheet}']   = admin_url('spreadsheet_online/file_view_share/'.$response->hash);
            $fields['{receiver}']            = get_staff_full_name($share_obj->staff_share_id);        

        }elseif ($share_obj->type_template == "client_template") {
            $response = $this->ci->spreadsheet_online_model->get_hash('client', $share_obj->client_share_id, $share_obj->id);
            $fields['{share_link_client_spreadsheet}'] = site_url('spreadsheet_online/spreadsheet_online_client/file_view_share/'.$response->hash);
            $fields['{receiver}']            = get_company_name($share_obj->client_share_id);        
        }

        $fields['{sender}']            = get_staff_full_name(get_staff_user_id()); 

        return $fields;
    }
}
