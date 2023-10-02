<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Appointly_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Appointment Subject',
                'key'       => '{appointment_subject}',
                'available' => [
                    'appointly',
                ],
            ],
            [
                'name'      => 'Appointment Description',
                'key'       => '{appointment_description}',
                'available' => [
                    'appointly',
                ],
            ],
            [
                'name'      => 'Appointment Client Name',
                'key'       => '{appointment_client_name}',
                'available' => ['appointly'],
            ],
            [
                'name'      => 'Appointment Client Email',
                'key'       => '{appointment_client_email}',
                'available' => ['appointly'],
            ],
            [
                'name'      => 'Appointment Client Name',
                'key'       => '{appointment_client_phone}',
                'available' => ['appointly'],
            ],
            [
                'name'      => 'Appointment Date',
                'key'       => '{appointment_date}',
                'available' => ['appointly'],
            ],
            [
                'name'      => 'Appointment Location',
                'key'       => '{appointment_location}',
                'available' => ['appointly'],
            ],
            [
                'name'      => 'Appointment Public Url',
                'key'       => '{appointment_public_url}',
                'available' => ['appointly'],
            ],
            [
                'name'      => 'Appointment Admin Url',
                'key'       => '{appointment_admin_url}',
                'available' => [],
                'templates' => [
                    'appointment-approved-to-staff',
                    'appointment-cron-reminder-to-staff',
                    'appointment-cancelled-to-staff',
                ]
            ]
        ];
    }

    /**
     * Merge field for appointments
     *
     * @param mixed $appointment_id
     *
     * @return array
     */
    public function format($appointment_id)
    {
        $fields = [];

        $this->ci->db->where('id', $appointment_id);

        $appointment = $this->ci->db->get(db_prefix() . 'appointly_appointments')->row();

        if (!$appointment) return $fields;

        $fields['{appointment_subject}'] = $appointment->subject;
        $fields['{appointment_description}'] = $appointment->description;
        $fields['{appointment_client_name}'] = $appointment->name;
        $fields['{appointment_client_email}'] = $appointment->email;
        $fields['{appointment_client_phone}'] = $appointment->phone;
        $fields['{appointment_date}'] = _dt($appointment->date . '' . $appointment->start_hour);
        $fields['{appointment_location}'] = $appointment->address;
        $fields['{appointment_admin_url}'] = admin_url('appointly/appointments/view?appointment_id=' . $appointment->id);
        $fields['{appointment_public_url}'] = site_url('appointly/appointments_public/client_hash?hash=' . $appointment->hash);

        return $fields;
    }

}
