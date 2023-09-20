<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Reminder_merge_fields extends App_merge_fields
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
        return hooks()->apply_filters('reminder_merge_fields', $fields, [
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
