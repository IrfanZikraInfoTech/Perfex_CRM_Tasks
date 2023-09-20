<div class="modal fade" id="leadAppointmentModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= _l('close'); ?>">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= _l('appointment_create_label') . ' ' . $user->type . ' ' . _l('appointment_label') ?></h4>
            </div>
            <?php echo form_open('appointly/appointments/create', ['id' => 'appointment-leads-contacts-crm-form']); ?>
            <?php
            $rel_type = (strtolower($user->type) == 'lead') ? 'lead_related' : 'internal';
            ?>
            <input type="text" hidden name="rel_type" value="<?= $rel_type ?>">
            <input type="text" hidden name="<?= strtolower($user->type) == 'contact' ? 'contact_id' : 'rel_id' ?>" value="<?= (isset($user->id)) ? $user->id : $user->userid ?>">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php if (appointlyGoogleAuth() && get_option('appointly_google_client_secret')) : ?>
                            <div class="checkbox pull-right mtop1">
                                <input type="checkbox" name="google" id="google">
                                <label data-toggle="tooltip" title="<?= _l('appointment_add_to_google_calendar'); ?>" for="google">
                                    <?= _l('appointment_add_to_google_calendar'); ?>
                                </label>
                            </div>
                        <?php endif; ?>
                        <?php echo render_input('subject', 'appointment_subject'); ?>
                        <?php echo render_textarea('description', 'appointment_description', '', ['rows' => 5]); ?>
                        <?php if (isset($staff)) : ?>
                            <div class="form-group">
                                <?php echo render_select('attendees[]', $staff, ['staffid', ['firstname', 'lastname']], 'appointment_select_attendees', [get_staff_user_id()], ['multiple' => true], [], '', '', false); ?>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-12 no-padding">
                            <div class="col-md-6 no-padding">
                                <?php echo render_datetime_input('date', 'appointment_date_and_time', '', ['readonly' => "readonly"], [], '', 'appointment-date'); ?>
                            </div>
                            <div class="col-md-6" style="padding-right: 0;padding-left:10px;">
                                <div class="form-group">
                                    <label for="address"><?= _l('appointment_meeting_location') . ' ' . _l('appointment_optional'); ?></label>
                                    <input type="text" class="form-control" name="address" id="address">
                                </div>
                            </div>
                            <div class="form-group" id="div_name">
                                <label for="name"><?= _l('appointment_name'); ?></label>
                                <input type="text" value="<?= (isset($user->name)) ? $user->name : $user->firstname . ' ' . $user->lastname ?>" class="form-control" name="name" id="name">
                            </div>
                            <div class="form-group" id="div_email">
                                <label for="email"><?= _l('appointment_email'); ?></label>
                                <input type="email" value="<?= $user->email ?>" class="form-control" name="email" id="email">
                            </div>
                            <div class="form-group" id="div_phone">
                                <label for="phone"><?= _l('appointment_phone'); ?> (Ex: <?= _l('appointment_your_phone_example'); ?>) </label>
                                <input type="text" value="<?= $user->phonenumber ?>" class="form-control" name="phone" id="phone">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <?php $appointment_types = get_appointment_types();
                        if (count($appointment_types) > 0) { ?>
                            <div class="form-group appointment_type_holder">
                                <label for="appointment_select_type" class="control-label"><?= _l('appointments_type_heading'); ?></label>
                                <select class="form-control selectpicker" name="type_id" id="appointment_select_type">
                                    <option value=""><?= _l('dropdown_non_selected_tex'); ?></option>
                                    <?php foreach ($appointment_types as $app_type) { ?>
                                        <option class="form-control" data-color="<?= $app_type['color']; ?>" value="<?= $app_type['id']; ?>"><?= $app_type['type']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <small id="appointment_color_type" class="pull-right appointment_color_type" style="background:#e1e6ec"></small>
                            </div>
                            <div class=" clearfix mtop15"></div>
                            <hr>
                        <?php } ?>
                        <?php
                        $this->load->view('view_includes/recurring_wrapper');
                        ?>
                        <?php
                        $rel_cf_id = (isset($appointment) ? $appointment['appointment_id'] : false);
                        echo render_custom_fields('appointly', $rel_cf_id);
                        ?>
                        <div class="form-group mtop10">
                            <div class="row">
                                <div class="col-md-12 mbot5">
                                    <?= _l('appointment_modal_notification_info'); ?>
                                </div>
                                <div class="col-md-6">
                                    <div class="checkbox">
                                        <input type="checkbox" name="by_sms" id="by_sms">
                                        <label for="by_sms"><?= _l('appoontment_sms_notification'); ?></label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" name="by_email" id="by_email">
                                        <label for="by_email"><?= _l('appoontment_email_notification'); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group appointment-reminder hide">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="reminder_before"><?= _l('appointments_reminder_time_value'); ?></label><br>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="reminder_before" value="" id="reminder_before">
                                        <span class="input-group-addon"><i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('reminder_notification_placeholder'); ?>"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <select name="reminder_before_type" id="reminder_before_type" class="selectpicker" data-width="100%">
                                        <option value="minutes"><?php echo _l('minutes'); ?></option>
                                        <option value="hours"><?php echo _l('hours'); ?></option>
                                        <option value="days"><?php echo _l('days'); ?></option>
                                        <option value="weeks"><?php echo _l('weeks'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <span class="font-medium pleft5"><?= _l('appointment_client_notes'); ?></span>
                            </div>
                            <div class="col-md-12 mtop8">
                                <textarea name="notes" id="" cols="30" rows="10"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-xs btn-default close_btn" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-xs btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php require('modules/appointly/assets/js/modals/create_leads_contacts_js.php'); ?>
<script>

</script>