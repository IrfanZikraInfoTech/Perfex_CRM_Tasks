<div class="cb-form-wrapper pfxcbcontent">
    <link href="<?= module_dir_url('appointly', 'assets/css/callbacks_external_form.css'); ?>" rel="stylesheet" type="text/css">
    <?php echo form_open('appointly/appointments_public/request_callback_external', ['id' => 'perfex-callbacks-form']); ?>

    <div class="vertical bar-deactive cb-form-color" data-toggle="tooltip" data-title="<?= _l('callbacks_request_a_callback'); ?>" data-placement="left">
        <!-- <span>Request a Callback</span> -->
        <span class="callback-icon"><img src="data:image/png;base64,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"></span>
        </span>
    </div>
    <?php $clientUserData = $this->session->userdata(); ?>
    <div class="text-center">
        <?php hooks()->do_action('callbacks_form_header'); ?>
        <h4 class="text-dark" id="request_callback_label"><?= _l('callbacks_request_a_callback'); ?></h4>
    </div>
    <div class="col-6 message_wrapper">
        <div class="form-group">
            <label for="client_firstname"><?= _l('callbacks_form_firstname'); ?></label>
            <input type="text" class="form-control" value="<?= (isset($clientUserData['client_logged_in'])) ? get_contact_detail($clientUserData['contact_user_id'], 'firstname') : ''; ?>" name="client_firstname" id="client_firstname">
        </div>
        <div class="form-group">
            <label for="client_lastname"><?= _l('callbacks_form_lastname'); ?></label>
            <input type="text" class="form-control" value="<?= (isset($clientUserData['client_logged_in'])) ? get_contact_detail($clientUserData['contact_user_id'], 'lastname') : ''; ?>" name="client_lastname" id="client_lastname">
        </div>

        <div class="form-group">
            <label for="client_email"><?= _l('callbacks_form_your_email'); ?></label>
            <input type="email" class="form-control" value="<?= (isset($clientUserData['client_logged_in'])) ? get_contact_detail($clientUserData['contact_user_id'], 'email') : ''; ?>" name="client_email" id="client_email">
        </div>

        <div class="form-group">
            <label for="client_message"><?= _l('callbacks_form_your_message'); ?></label>
            <textarea type="textarea" class="form-control" rows="5" name="client_message" id="client_message"></textarea>
        </div>

        <div class="form-group">
            <label for="client_phone"><?= _l('callbacks_form_your_phone'); ?></label>
            <input type="tel" class="form-control" value="<?= (isset($clientUserData['client_logged_in'])) ? get_contact_detail($clientUserData['contact_user_id'], 'phonenumber') : ''; ?>" name="client_phone" id="client_phone">
        </div>

        <label for="client_phone"><?= _l('callbacks_form_available_from_to'); ?></label>

        <div class="form-group">
            <div class="col-3 callback_date_from">
                <input type="text" class="form-control dates" value="" name="date_from" id="date_from">
            </div>
            <span class="text-center" style="padding-left:14px">--></span>
            <div class="col-3 callback_date_to">
                <input type="text" class="form-control dates" value="" name="date_to" id="date_to">
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group">
            <label for="timezones" class="control-label"><?php echo _l('settings_localization_default_timezone'); ?></label>
            <select name="timezone" id="timezones" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                <?php foreach (get_timezones_list() as $key => $timezones) { ?>
                    <optgroup label="<?php echo $key; ?>">
                        <?php foreach ($timezones as $timezone) { ?>
                            <option value="<?php echo $timezone; ?>" <?php if (get_option('default_timezone') == $timezone) {
                                echo 'selected';
                            } ?>><?php echo $timezone; ?></option>
                        <?php } ?>
                    </optgroup>
                <?php } ?>
            </select>
        </div>

        <label for="types"><?= _l('callbacks_prefer_to_be_contacted_via'); ?></label>
        <div class="form-group">
            <?php echo render_callbacks_handle_call_type(); ?>
        </div>
        <div class="submit">
            <button type="submit" value="submit" class="btn btn-info pfxcbsubmit" id="pfxcbsubmit"><?= _l('callbacks_request_btn_label'); ?></button>
        </div>
    </div>
    <?= form_close(); ?>
</div>
<script>
    app.locale = "<?= get_locale_key($form->language); ?>";
</script>
<?php require('modules/appointly/assets/js/callbacks_external_form_js.php'); ?>