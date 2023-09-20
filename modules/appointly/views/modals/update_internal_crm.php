<div class="modal fade" id="appointmentModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('appointment_edit_appointment'); ?></h4>
            </div>
            <?php echo form_open('appointly/appointments/update_internal_crm', ['id' => 'appointment-form']); ?>
            <div class="modal-body">
                <div class="row">
                    <input type="text" hidden value="<?= $history['appointment_id']; ?>" name="appointment_id">
                    <input type="text" hidden value="<?= $history['source']; ?>" name="source">

                    <?php if ($history['source'] == 'lead_related') : ?>
                        <input type="text" hidden value="<?= $history['email']; ?>" name="email">
                    <?php endif; ?>

                    <input type="text" hidden value="<?= $history['approved']; ?>" name="approved">
                    <input type="text" hidden value="<?= $history['google_added_by_id']; ?>" name="google_added_by_id">
                    <?php if (isset($history['selected_contact'])) { ?>
                        <input type="text" hidden value="<?= $history['selected_contact']; ?>" name="selected_contact">
                    <?php } ?>
                    <div class="col-md-12">
                        <?php if (appointlyGoogleAuth()) { ?>
                            <?php if ($history['google_event_id'] !== null && $history['google_added_by_id'] == get_staff_user_id()) { ?>
                                <input type="text" hidden value="<?= $history['google_event_id']; ?>" name="google_event_id">
                            <?php } ?>
                            <?php if ($history['google_event_id'] && $history['google_added_by_id'] == get_staff_user_id()) : ?>
                                <div class="checkbox pull-right mleft10 mtop1">
                                    <input disabled data-toggle="tooltip" title="<?= _l('appointments_added_to_google_calendar'); ?>" type="checkbox" id="google" checked />
                                    <label data-toggle="tooltip" title="<?= _l('appointments_added_to_google_calendar'); ?>" for="google">
                                        <i class="fa fa-google" aria-hidden="true"></i></label>
                                </div>
                            <?php endif; ?>
                        <?php } ?>

                        <div class="pull-right"><span class="label label-info">Internal Staff Appointment</span></div>
                        <div class="clearfix"></div>

                        <label for="subject"><?= _l('appointment_subject'); ?></label><br>
                        <input type="text" class="form-control" name="subject" id="subject" value="<?= $history['subject']; ?>">
                        <div class="form-group mtop20">
                            <label for="description"><?= _l('appointment_description'); ?></label>
                            <textarea name="description" class="form-control" id="description" rows="5"><?= $history['description']; ?></textarea>
                        </div>
                        <div class="form-group">
                            <?php echo render_select('attendees[]', $staff_members, ['staffid', ['firstname', 'lastname']], 'appointment_select_attendees', $history['selected_staff'], ['multiple' => true], [], '', '', false); ?>
                        </div>
                        <div class="pull-right available_times_labels_edit">
                            <span class="available_time_info">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            <?= _l('appointment_available_hours'); ?>
                            <span class="busy_time_info">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            <?= _l('appointment_busy_hours'); ?>
                            <?php if (appointlyGoogleAuth()) : ?>
                                <span class="busy_time_info_google">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                <?= _l('appointments_google_calendar'); ?>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 no-padding">
                            <?php echo render_datetime_input(
                                'date',
                                'appointment_date_and_time',
                                _dt($history['date'] . ' ' . $history['start_hour']),
                                ["readonly" => "readonly"],
                                [],
                                '',
                                'appointment-date'
                            ); ?>
                        </div>

                        <div class="clearfix"></div>

                        <div class="form-group">
                            <label for="address"><?= _l('appointment_meeting_location') . ' ' . _l('appointment_optional'); ?></label>
                            <input type="text" class="form-control" value="<?= isset($history['address']) ? $history['address'] : ''; ?>" name="address" id="address">
                        </div>
                        <?php
                        $appointment_types = get_appointment_types();
                        if (
                            count($appointment_types) > 0
                            && isset($history['type_id'])
                        ) {
                            $app_color = get_appointment_color_type($history['type_id']);
                            ?>
                            <div class="form-group appointment_type_holder">
                                <label for="appointment_select_type" class="control-label"><?= _l('appointments_type_heading'); ?></label>
                                <select class="form-control selectpicker" name="type_id" id="appointment_select_type">
                                    <option value=""><?= _l('dropdown_non_selected_tex'); ?></option>
                                    <?php foreach ($appointment_types as $app_type) { ?>
                                        <option <?= ($app_type['id'] == $history['type_id']) ? 'selected' : ''; ?> class="form-control" data-color="<?= $app_type['color']; ?>" value="<?= $app_type['id']; ?>"><?= $app_type['type']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <small id="appointment_color_type" class="pull-right appointment_color_type" style="background: <?= ($app_color) ? $app_color : '#e1e6ec'; ?>"></small>
                            </div>
                            <div class=" clearfix mtop15"></div>
                            <hr>
                        <?php } ?>
                        <?php
                        $data['appointment'] = isset($history) ? $history : [];
                        $this->load->view('view_includes/recurring_wrapper', $data);
                        ?>
                        <?php echo render_custom_fields('appointly', $history['appointment_id']); ?>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12 mbot5">
                                    <?= _l('appointment_modal_notification_info'); ?> </div>
                                <div class="col-md-6">
                                    <div class="checkbox">
                                        <input type="checkbox" name="by_email" id="by_email" <?= $history['by_email'] == 1 ? 'checked' : '' ?>>
                                        <label for="by_email"><?= _l('appointment_email_notification_text'); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group appointment-reminder<?php if ($history['by_sms'] == null && $history['by_email'] == null) {
                            echo ' hide';
                        } ?>">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="reminder_before"><?php echo _l('event_notification'); ?></label>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="reminder_before" value="<?php echo $history['reminder_before']; ?>" id="reminder_before">
                                        <span class="input-group-addon"><i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('reminder_notification_placeholder'); ?>"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <select name="reminder_before_type" id="reminder_before_type" class="selectpicker" data-width="100%">
                                        <option value="minutes" <?php if ($history['reminder_before_type'] == 'minutes') {
                                            echo ' selected';
                                        } ?>><?php echo _l('minutes'); ?></option>
                                        <option value="hours" <?php if ($history['reminder_before_type'] == 'hours') {
                                            echo ' selected';
                                        } ?>><?php echo _l('hours'); ?></option>
                                        <option value="days" <?php if ($history['reminder_before_type'] == 'days') {
                                            echo ' selected';
                                        } ?>><?php echo _l('days'); ?></option>
                                        <option value="weeks" <?php if ($history['reminder_before_type'] == 'weeks') {
                                            echo ' selected';
                                        } ?>><?php echo _l('weeks'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <span class="font-medium pleft5"><?= _l('appointment_client_notes'); ?></span>
                            </div>
                            <div class="col-md-12 mtop8">
                                <textarea name="notes" id="" cols="30" rows="10">
                                    <?= isset($history['notes']) ? htmlentities($history['notes']) : ''; ?>
                                </textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-xs btn-default close_btn" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-xs btn-info"><?php echo _l('submit'); ?></button>
                <?php
                if (appointlyGoogleAuth()) {
                    if (
                        $history['google_event_id'] === null
                        && $history['google_calendar_link'] === null
                        && $history['google_added_by_id'] === null
                        && $history['approved']
                    ) { ?>
                        <button type="button" data-toggle="tooltip" title="<?= _l('appointment_google_not_added_yet'); ?>" onclick="addEventToGoogleCalendar(this)" class="btn btn-xs btn-primary"><?= _l('appointment_add_to_calendar'); ?>&nbsp;<i class="fa fa-google" aria-hidden="true"></i>
                        </button>
                    <?php } ?>
                <?php } ?>

            </div>
            <?php echo form_close(); ?>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php require('modules/appointly/assets/js/modals/update_js.php'); ?>