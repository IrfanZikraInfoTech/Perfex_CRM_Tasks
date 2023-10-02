<?php defined('BASEPATH') or exit('No direct script access allowed');
// Means module is disabled
if ( ! function_exists('get_appointment_types')) {
    access_denied();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php echo hooks()->apply_filters('appointments_form_title', _l('appointment_create_new_appointment')); ?></title>

    <?php app_external_form_header($form); ?>

    <link href="<?= module_dir_url('appointly', 'assets/css/appointments_external_form.css'); ?>" rel="stylesheet"
          type="text/css">
</head>

<body class="appointments-external-form" <?php if (is_rtl(true)) {
    echo ' dir="rtl"';
} ?>>
<?php
$clientUserData = $this->session->userdata();
applyAdditionalCssStyles($clientUserData);
?>
<div id="wrapper">
    <div id="content">
        <div class="container">

            <div id="response"></div>

            <?php echo form_open('appointly/appointments_public/create_external_appointment', ['id' => 'appointments-form']); ?>

            <input type="text" hidden name="rel_type" value="external">

            <div class="row">

                <div class="main_wrapper mbot20 <?= ($this->input->get('col')) ? $this->input->get('col') : 'col-md-12'; ?>">

                    <div class="appointment-header"><?php hooks()->do_action('appointly_form_header'); ?></div>

                    <div class="text-center">
                        <h4 class="text-center"><?= _l('appointment_create_new_appointment'); ?></h4>
                    </div>

                    <?php echo render_input('subject', 'appointment_subject'); ?>

                    <?php echo render_textarea('description', 'appointment_description', '', ['rows' => 5]); ?>

                    <?php $appointment_types = get_appointment_types();

                    if (count($appointment_types) > 0) { ?>
                        <div class="form-group appointment_type_holder">
                            <label for="appointment_select_type"
                                   class="control-label"><?= _l('appointments_type_heading'); ?></label>
                            <select class="form-control selectpicker" name="type_id" id="appointment_select_type">
                                <option value=""><?= _l('dropdown_non_selected_tex'); ?></option>
                                <?php foreach ($appointment_types as $app_type) { ?>
                                    <option class="form-control" data-color="<?= $app_type['color']; ?>"
                                            value="<?= $app_type['id']; ?>"><?= $app_type['type']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class=" clearfix mtop15"></div>
                    <?php } ?>
                    <br>
                    <div class="form-group">
                        <label for="name"><?= _l('appointment_full_name'); ?></label>
                        <input type="text" class="form-control"
                               value="<?= (isset($clientUserData['client_logged_in'])) ? get_contact_full_name($clientUserData['contact_user_id']) : ''; ?>"
                               name="name" id="name">
                    </div>
                    <div class="form-group">
                        <label for="email"><?= _l('appointment_your_email'); ?></label>
                        <input type="email" class="form-control"
                               value="<?= (isset($clientUserData['client_logged_in'])) ? get_contact_detail($clientUserData['contact_user_id'], 'email') : ''; ?>"
                               name="email" id="email">
                    </div>
                    <div class="form-group">
                        <label for="phone"><?= _l('appointment_phone'); ?>
                            (Ex: <?= _l('appointment_your_phone_example'); ?>)</label>
                        <input type="text" class="form-control"
                               value="<?= (isset($clientUserData['client_logged_in'])) ? get_contact_detail($clientUserData['contact_user_id'], 'phonenumber') : ''; ?>"
                               name="phone" id="phone">
                    </div>
                    <div class="hours_wrapper">
                        <span class="available_time_info hwp"><?= _l('appointment_available_hours'); ?></span>
                        <span class="busy_time_info hwp"><?= _l('appointment_busy_hours'); ?></span>
                    </div>
                    <?php echo render_datetime_input('date', 'appointment_date_and_time', '', ['readonly' => "readonly"], [], '', 'appointment-date'); ?>
                    <div class="form-group">
                        <label for="address"><?= _l('appointment_meeting_location') . ' ' . _l('appointment_optional'); ?></label>
                        <input type="text" class="form-control" value="" name="address" id="address">
                    </div>
                    <?php
                    $rel_cf_id = (isset($appointment) ? $appointment['apointment_id'] : false);
                    echo render_custom_fields('appointly', $rel_cf_id);
                    ?>
                    <?php if (
                        get_option('recaptcha_secret_key') != ''
                        && get_option('recaptcha_site_key') != ''
                        && get_option('appointly_appointments_recaptcha') == 1
                    ) { ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="g-recaptcha"
                                         data-sitekey="<?php echo get_option('recaptcha_site_key'); ?>"></div>
                                    <div id="recaptcha_response_field" class="text-danger"></div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="pull-right">
                        <button type="submit" id="form_submit"
                                class="btn btn-primary"><?php echo _l('appointment_submit'); ?></button>
                    </div>
                    <div class="clearfix mtop15"></div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php
app_external_form_footer($form);
?>

<?php if (isset($form)): ?>
    <script>
        app.locale = "<?= get_locale_key($form->language); ?>";
    </script>
<?php endif; ?>

<!-- Javascript functionality -->
<?php require('modules/appointly/assets/js/appointments_external_form.php'); ?>

<!-- If callbacks is enabled load on appointments external form -->
<?php if (get_option('callbacks_mode_enabled') == 1) require('modules/appointly/views/forms/callbacks_form.php'); ?>

</body>

</html>