<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="no-margin"><?= _l('appointment_your_settings'); ?></h4>
                            <hr class="hr-panel-heading">
                            <form action="<?= admin_url(APPOINTLY_MODULE_NAME . '/appointments/user_settings'); ?>" method="POST">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                <div class="form-group">
                                    <label for="appointly_show_summary" class="control-label clearfix">
                                        <?php echo _l('appointly_show_summary_in_appointments_dashboard'); ?>
                                    </label>
                                    <div class="radio radio-primary radio-inline">
                                        <input type="radio" id="y_opt_1_appointly_show_summary" name="appointly_show_summary" value="1" <?= ($appointly_show_summary == '1') ? ' checked' : '' ?>>
                                        <label for="y_opt_1_appointly_show_summary"><?php echo _l('settings_yes'); ?></label>
                                    </div>
                                    <div class="radio radio-primary radio-inline">
                                        <input type="radio" id="y_opt_2_appointly_show_summary" name="appointly_show_summary" value="0" <?= ($appointly_show_summary == '0') ? ' checked' : '' ?>>
                                        <label for="y_opt_2_appointly_show_summary">
                                            <?php echo _l('settings_no'); ?>
                                        </label>
                                    </div>
                                </div>
                                <hr>
                                <?php
                                echo render_select(
                                    'appointly_default_table_filter',
                                    $filters,
                                    ['id', ['status']],
                                    'appointly_default_table_filter_label',
                                    $appointly_default_table_filter
                                );
                                echo '<hr>';
                                ?>
                                <?php if (staff_can('create', 'appointments') || staff_appointments_responsible()) { ?>
                                    <button type="button" class="btn btn-xs btn-info btn-xs pull-right" data-toggle="modal" data-target="#typesModal"><?= _l('appointments_type_add'); ?></button>
                                    <label for="appointly_default_table_filter" class="control-label font-medium"><?= _l('appointments_type_heading'); ?></label>
                                    <hr>
                                    <div class="clearfix"></div>
                                    <div class="col-md-12 d-flex flex-wrap">
                                        <?php foreach (get_appointment_types() as $type) { ?>
                                            <div class="mright20" id="aptype_<?= $type['id']; ?>">
                                                <?php if (staff_can('delete', 'appointments') || staff_appointments_responsible()) : ?>
                                                    <a href="#" class="btn btn-xs btn-danger pull-right delete_apppointment_type" onclick="delete_appointment_type(<?= $type['id']; ?>); return false;"><i class="fa fa-times"></i></a>
                                                <?php endif; ?>
                                                <?php echo render_color_picker('type_id_' . $type['id'], $type['type'], $type['color']); ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-xs btn-info btn-xs"><?= _l('submit'); ?></button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div id="typesModal" class="modal fade" role="dialog">
    <form id="appointmentNewTypeForm">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?= _l('appointments_type_add'); ?></h4>
                </div>
                <div class="modal-body">
                    <label for="appointment_type" class="control-label"><?= _l('appointments_type_add_name_label'); ?></label>
                    <input type="text" class="form-control mbot10" name="appointment_type" required>
                    <?php echo render_color_picker('color', _l('appointments_type_add_calendar_label')); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-default" data-dismiss="modal"><?= _l('close') ?></button>
                    <button type="submit" class="btn btn-xs btn-primary"><?= _l('save'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>
</body>
<?php init_tail(); ?>
<?php require('modules/appointly/assets/js/user_settings_js.php'); ?>

</html>