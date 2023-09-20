<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Appointments_public extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('appointly_model', 'apm');
        $this->load->model('staff_model');
    }

    /**
     * Clients hash view.
     *
     * @return void
     */
    public function client_hash()
    {
        $hash = $this->input->get('hash');

        if (!$hash) show_404();

        $appointment = $this->apm->getByHash($hash);

        if (!$appointment) show_404();

        $appointment['url'] = site_url('appointly/appointments_public/cancel_appointment');

        $appointment['feedback_url'] = site_url('appointly/appointments_public/handleFeedbackPost');

        if ($appointment['feedback_comment'] !== null) $appointment['feedback_comment'] = true;

        $this->load->view('clients/clients_hash', ['appointment' => $appointment]);
    }

    /**
     * Fetches contact data if client who requested meeting is already in the system.
     *
     * @return void
     */
    public function external_fetch_contact_data()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id = $this->input->post('contact_id');

        header('Content-Type: application/json');
        echo json_encode($this->apm->apply_contact_data($id, false));
    }

    /**
     * Handles clients external public form.
     *
     * @return void
     */
    public function form()
    {
        $form = new stdClass();

        $form->language = get_option('active_language');

        $this->lang->load($form->language . '_lang', $form->language);

        if (file_exists(APPPATH . 'language/' . $form->language . '/custom_lang.php')) {
            $this->lang->load('custom_lang', $form->language);
        }

        if ($this->input->post() && $this->input->is_ajax_request()) {

            $post_data = $this->input->post();

            $required = ['subject', 'description', 'name', 'email'];

            foreach ($required as $field) {
                if (!isset($post_data[$field]) || isset($post_data[$field]) && empty($post_data[$field])) {
                    $this->output->set_status_header(422);
                    die;
                }
            }
            die;
        }

        $data['form'] = $form;
        $data['form']->recaptcha = 1;

        $this->load->view('forms/appointments_form', $data);
    }

    /**
     * Handles creation of an external appointment.
     *
     * @return void
     */
    public function create_external_appointment()
    {
        $data = $this->input->post();

        if (!$data) {
            show_404();
        }

        $data['source'] = $data['rel_type'];
        unset($data['rel_type']);

        if (isset($data['g-recaptcha-response'])) {
            if (get_option('recaptcha_secret_key') != '' && get_option('recaptcha_site_key') != '') {
                if (!do_recaptcha_validation($data['g-recaptcha-response'])) {
                    echo json_encode([
                        'success'   => false,
                        'recaptcha' => false,
                        'message'   => _l('recaptcha_error'),
                    ]);
                    die;
                }
            }
        }

        if (isset($data['g-recaptcha-response'])) unset($data['g-recaptcha-response']);

        if ($this->apm->insert_external_appointment($data)) {
            echo json_encode([
                'success' => true,
                'message' => _l('appointment_sent_successfully')
            ]);
        }
    }

    /**
     * Handles appointment cancelling.
     *
     * @return bool|void
     */
    public function cancel_appointment()
    {
        if ($this->input->get('hash')) {

            $hash = $this->input->get('hash');
            $notes = $this->input->get('notes');

            if ($notes == '') return false;

            if (!$hash) show_404();

            $appointment = $this->apm->getByHash($hash);

            if (!$appointment) {
                show_404();
            } else {
                $cancellation_in_progress = $this->apm->checkIfCancellationIsInProgress($hash);

                header('Content-Type: application/json');
                if ($cancellation_in_progress['cancel_notes'] === null) {
                    $responsible_person = get_option('appointly_responsible_person');
                    $touserid = '';

                    if ($responsible_person != '') {
                        $touserid = $responsible_person;
                    } else if ($responsible_person == '' && $appointment['created_by'] !== null) {
                        $touserid = $appointment['created_by'];
                    } else {
                        /** If none of above conditions are true
                         * Goes to default eg. first admin created with id of 1.
                         */
                        $touserid = 1;
                    }

                    add_notification([
                        'description' => 'appointment_cancel_notification',
                        'touserid'    => $touserid,
                        'fromcompany' => true,
                        'link'        => 'appointly/appointments/view?appointment_id=' . $appointment['id'],
                    ]);

                    pusher_trigger_notification([$touserid]);
                    echo json_encode($this->apm->applyForAppointmentCancellation($hash, $notes));
                } else {
                    echo json_encode(['response' => [
                        'message' => _l('appointments_already_applied_for_cancelling'),
                        'success' => false
                    ]]);
                }
            }
        } else {
            show_404();
        }
    }

    /**
     * Get busy appointment times.
     *
     * @return void
     */
    public function busyDates()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        return $this->apm->getBusyTimes();
    }

    /**
     * Handles external callback post.
     */
    public function request_callback_external()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $data = $this->input->post();

        if (!$data) show_404();

        /*
         * Init callbacks model
         */
        $this->load->model('callbacks_model', 'callbackm');

        echo json_encode(['success' => $this->callbackm->handle_callback_request_data($data)]);
    }

    public function handleFeedbackPost()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id = $this->input->get('id');

        if (!$id) show_404();

        $rating = $this->input->get('rating');

        $comment = ($this->input->get('feedback_comment')) ? $this->input->get('feedback_comment') : null;

        if ($this->apm->handle_feedback_post($id, $rating, $comment)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

}
