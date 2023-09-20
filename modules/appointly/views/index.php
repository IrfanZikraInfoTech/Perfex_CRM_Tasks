<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head();
$appointly_default_table_filter = get_meta('staff', get_staff_user_id(), 'appointly_default_table_filter');

if ( ! $appointly_default_table_filter) {
    $appointly_default_table_filter = 'all';
}

$filters = ['approved', 'not_approved', 'cancelled', 'finished', 'upcoming', 'missed', 'internal', 'external', 'recurring', 'lead_related', 'internal_staff'];

$appointly_show_summary = get_meta('staff', get_staff_user_id(), 'appointly_show_summary');
$appointly_outlook_client_id = get_option('appointly_outlook_client_id');
$edit_appointment_id = ($this->session->userdata('from_view_id')) ? $this->session->userdata('from_view_id') : 0;
?>

<div id="wrapper">
    <div class="content">
        <?php if (get_option('appointly_responsible_person') == '') { ?>
            <div class="alert alert-warning alert-dismissible" role="alert">
                <?= _l('appointments_resp_person_not_set'); ?>
                <a href="<?= admin_url('settings?group=appointly-settings'); ?>"><?= _l('appointly_settings_label_pointer'); ?></a>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        <?php } ?>
        <?php if (get_option('callbacks_responsible_person') == '') { ?>
            <div class="alert alert-warning alert-dismissible" role="alert">
                <?= _l('callbacks_resp_person_not_set'); ?>
                <a href="<?= admin_url('settings?group=appointly-settings'); ?>"><?= _l('appointly_settings_label_pointer'); ?></a>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        <?php } ?>
        <?php if (isset($td_appointments) && ! empty($td_appointments)) : ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <span class="label label-info label-big pull-right mtop5"><?= _d(date('Y-m-d')); ?></span>
                            <h4><?= _l('appointment_todays_appointments'); ?>
                            </h4>
                            <hr class="mbot0">
                            <?php foreach ($td_appointments as $appointment) : ?>
                                <div class="todays_appointment col-2 mleft20 appointly-secondary pull-left mtop10">
                                    <h3 class="text-muted mtop1">
                                        <a href="<?= admin_url('appointly/appointments/view?appointment_id=' . $appointment['id']); ?>"><?= $appointment['subject']; ?></a>
                                    </h3>
                                    <span class="text-muted span_limited">
                                        <?= _l('appointment_description'); ?>
                                        <?= $appointment['description']; ?>
                                    </span>
                                    <h5 class="no-margin">
                                        <span class="text-warning"><?= _l('appointment_scheduled_at'); ?>
                                        </span>
                                        <?= date("H:i A", strtotime($appointment['start_hour'])); ?>
                                    </h5>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <span class="label label-info label-big pull-right mtop5"><?= _d(date('Y-m-d')); ?></span>
                            <h4><?= _l('appointment_no_appointments'); ?>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if ($appointly_show_summary == 1) : ?>
                            <?php include_appointment_view('tables', 'summary'); ?>
                        <?php endif; ?>

                        <div class="_buttons">
                            <?php if (staff_can('create', 'appointments') || staff_appointments_responsible()) { ?>
                                <div class="dropdown pull-left">
                                    <button class="btn btn-info btn-xs display-block dropdown-toggle" type="button"
                                            data-toggle="dropdown"><?= _l("appointment_create_label") ?></button>
                                    <ul class="dropdown-menu" style="width:max-content">
                                        <li>
                                            <a href="#" onClick="return false;"
                                               id="createNewAppointment"><?= _l("appointment_create_cle") ?></a>
                                        </li>
                                        <li>
                                            <a href="#" onClick="return false;"
                                               id="createInternal"><?= _l("appointment_staff_meeting") ?></a>
                                        </li>
                                    </ul>
                                </div>
                            <?php } else { ?>
                                <div class="dropdown pull-left">
                                    <button class="btn btn-info btn-xs display-block dropdown-toggle disabled"
                                            type="button" data-toggle="dropdown"><?= _l("create") ?></button>
                                </div>
                            <?php } ?>
                            <a href="<?= admin_url('appointly/callbacks'); ?>" id="backToAppointments"
                               class="btn btn-info btn-xs pull-left display-block mleft10">
                                <?= _l('appointly_callbacks'); ?>
                            </a>

                            <div class="_filters _hidden_inputs hidden">
                                <?php
                                foreach ($filters as $filter) {
                                    echo form_hidden($filter, $filter === $appointly_default_table_filter ? $appointly_default_table_filter : null);
                                }
                                ?>
                            </div>
                            <div class="btn-group pull-right btn-with-tooltip-group _filter_data" data-toggle="tooltip"
                                 data-title="<?php echo _l('filter_by'); ?>">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-filter" aria-hidden="true"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left width300 height500">
                                    <li class="filter-group <?= ($appointly_default_table_filter == 'all') ? 'active' : ''; ?>">
                                        <a href="#" data-cview="all"
                                           onclick="dt_custom_view('','.table-appointments',''); return false;">
                                            <?php echo _l('all'); ?>
                                        </a>
                                    </li>
                                    <?php foreach ($filters as $filter) { ?>
                                        <li class="filter-group <?= ($appointly_default_table_filter == $filter) ? 'active' : ''; ?>">
                                            <a href="#" data-cview="<?php echo $filter; ?>"
                                               onclick="dt_custom_view('<?php echo $filter; ?>','.table-appointments', '<?php echo $filter; ?>',true); return false;">
                                                <?= _l('appointment_' . $filter . ($filter == 'missed' ? '_label' : '')); ?>
                                            </a>
                                        </li>
                                        <?php
                                        if ($filter === 'finished') { ?>
                                            <li class="divider"></li>
                                        <?php } ?>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div class="btn-group pull-right btn-with-tooltip-group mright5" data-toggle="tooltip"
                                 data-title="<?php echo _l('appointly_integrations'); ?>">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-rocket" aria-hidden="true"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left w-max-content">
                                    <?php if ($appointly_outlook_client_id !== '' && strlen($appointly_outlook_client_id) === 36) : ?>
                                        <li>
                                        <a href="#" data-toggle="tooltip" id="sign_in_outlook"
                                           onclick="signInToOutlook(); return false">
                                            <i class="fa fa-envelope"
                                               aria-hidden="true"></i> <?= _l('appointment_login_to_outlook'); ?>
                                        </a>
                                    <?php else : ?>
                                        <a href="#" data-toggle="tooltip"
                                           title="<?= _l('appointments_outlook_revoke'); ?>" id="sign_in_outlook"
                                           onclick="signInToOutlook(); return false">
                                            <i class="fa fa-envelope"
                                               aria-hidden="true"></i> <?= _l('appointment_login_to_outlook'); ?>
                                        </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (get_option('google_client_id') !== '' && get_option('appointly_google_client_secret') !== '') : ?>
                                        <li>
                                            <?php if ( ! appointlyGoogleAuth()) : ?>
                                                <a href="<?= site_url('appointly/google/auth/login'); ?>"
                                                   class="a_google">
                                                    <i class="fa fa-google" aria-hidden="true"></i>
                                                    &nbsp;&nbsp;<?= _l('appointments_sign_in_google'); ?>
                                                </a>
                                            <?php else : ?>
                                                <a data-toggle="tooltip" title="<?= _l('appointments_google_revoke') ?>"
                                                   href="<?= site_url('appointly/google/auth/logout'); ?>">
                                                    <i class="fa fa-google" aria-hidden="true"></i>
                                                    &nbsp;&nbsp;<?= _l('appointments_sign_out_google'); ?></a>
                                            <?php endif; ?>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>

                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading"/>
                        <?php render_datatable([
                            _l('id'),
                            [
                                'th_attrs' => ['width' => '300px'],
                                'name'     => _l('appointment_subject')
                            ],
                            _l('appointment_meeting_date'),
                            _l('appointment_initiated_by'),
                            _l('appointment_description'),
                            _l('appointment_status'),
                            _l('appointment_source'),
                            [
                                'th_attrs' => ['width' => '120px'],
                                'name'     => _l('appointments_table_calendar')
                            ]
                        ], 'appointments'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modal_wrapper"></div>
<?php init_tail(); ?>
<?php require 'modules/appointly/assets/js/index_main_js.php'; ?>

<?php if ($appointly_outlook_client_id !== '' && strlen($appointly_outlook_client_id) === 36) : ?>
    <?php require 'modules/appointly/assets/js/outlook_js.php'; ?>
<?php endif; ?>
</body>

</html>
