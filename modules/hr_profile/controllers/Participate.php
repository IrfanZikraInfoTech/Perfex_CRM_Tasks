<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Participate extends ClientsController
{
    /**
     * @param  integer $id   
     * @param  string $hash 
     * @return integer       
     */
    public function index($id, $hash)
    {
        $this->load->model('hr_profile_model');
        $training = $this->hr_profile_model->get_position_training($id);
        if (!$training
            || ($training->hash != $hash)
            || (!$hash || !$id)
            || ($training->active == 0 && !has_permission('job_position', '', 'view'))
            || ($training->onlyforloggedin == 1 && !is_logged_in())
        ) {
            show_404();
        }
        if ($this->input->post()) {
            $success = $this->hr_profile_model->add_training_result($id, $this->input->post());
                $link_redirect = site_url('admin/hr_profile/member/'.get_staff_user_id());
            if ($success) {
                $training = $this->hr_profile_model->get_position_training($id);
                set_alert('success', _l('hr_thank_you_for_participating_in_this_training'));
                if ($link_redirect !== '') {
                    redirect($link_redirect);
                }
                // Message is by default in English because there is no easy way to know the customer language
                set_alert('success', hooks()->apply_filters('survey_success_message', 'Thank you for participating in this survey. Your answers are very important to us.'));
                redirect(hooks()->apply_filters('survey_default_redirect', site_url('hr_profile/' . $id . '/' . $hash . '?participated=yes')));
            }
        }
        $this->app_css->theme('surveys-css', module_dir_url('hr_profile', 'assets/css/training/training_post.css'));
        $this->disableNavigation()
        ->disableSubMenu();

        $this->data(['training'=>$training]);
        $this->title($training->subject);
        no_index_customers_area();
        $this->view('participate');
        $this->layout();
    }

    public function view_staff_training_result($staff_id, $resultsetid, $id, $hash)
    {
        if (!has_permission('staffmanage_training', '', 'view') && !has_permission('staffmanage_training', '', 'view_own') && !is_admin() ) {
            access_denied('job_position');
        }

        $this->load->model('hr_profile_model');
        $training = $this->hr_profile_model->get_position_training($id);

        $training_result = $this->hr_profile_model->get_mark_staff_from_resultsetid($resultsetid, $id, $staff_id);

        if (!$training
            || ($training->hash != $hash)
            || (!$hash || !$id)
            || ($training->active == 0 && !has_permission('job_position', '', 'view'))
            || ($training->onlyforloggedin == 1 && !is_logged_in())
        ) {
            show_404();
        }
       
        $this->app_css->theme('surveys-css', module_dir_url('hr_profile', 'assets/css/training/training_post.css'));
        $this->disableNavigation()
        ->disableSubMenu();

        $this->data(['training'=>$training, 'training_result' => $training_result]);
        $this->title($training->subject);
        no_index_customers_area();
        $this->view('participate_result');
        $this->layout();
    }
}
