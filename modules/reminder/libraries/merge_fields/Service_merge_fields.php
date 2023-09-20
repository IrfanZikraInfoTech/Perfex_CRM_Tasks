<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Service_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Contact First Name',
                'key'       => '{firstname}',
                'available' => ['client'],
            ],
            [
                'name'      => 'Contact Last Name',
                'key'       => '{lastname}',
                'available' => ['client'],
            ],
            [
                'name'      => 'Reminder Link',
                'key'       => '{reminder_link}',
                'available' => ['client'],
            ],
            [
                'name'      => 'Relation Type',
                'key'       => '{rel_type}',
                'available' => ['client'],
            ],
            [
                'name'      => 'Message',
                'key'       => '{item_description}',
                'available' => ['client'],
            ],
            [
                'name'      => 'Total Amount',
                'key'       => '{total_amount}',
                'available' => ['client'],
            ],
            [
                'name'      => 'Total Services',
                'key'       => '{total_services}',
                'available' => ['client'],
            ],
        ];
    }
    public function format($reminder_id, $data)
    {
        $fields = [];
        $fields['{item_description}']          = $data['description'];
        $fields['{firstname}']                 = $data['firstname'];
        $fields['{lastname}']                  = $data['lastname'];
        $fields['{reminder_link}']             = admin_url('reminder#' . $reminder_id);
        $fields['{rel_type}']                  = $data['rel_type'];
        $fields['{total_amount}']              = $data['total_amount']." " .get_base_currency()->symbol;
        $fields['{total_services}']            = $data['total_services'];
        return hooks()->apply_filters('service_merge_fields', $fields, [
            'data'      => $data,
        ]);
    }
    public function name()
    {
        if (is_null($this->for)) {
            $this->for = strtolower(strbefore(get_class($this), '_merge_fields'));
        }
        return $this->for;
    }
}
