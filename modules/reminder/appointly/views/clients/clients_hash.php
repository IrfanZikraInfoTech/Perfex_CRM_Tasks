<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= _l('appointment_overview'); ?>
    </title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?= site_url('assets\plugins\bootstrap\css\bootstrap.min.css'); ?>">
    <!-- Bootstrap Theme -->
    <link rel="stylesheet" href="<?= site_url('assets\plugins\bootstrap\css\bootstrap-theme.min.css'); ?>">
    <link rel="stylesheet" href="<?= site_url('assets\plugins\font-awesome/css/font-awesome.css'); ?>">
    <!-- Client side styles -->
    <link rel="stylesheet" href="<?= site_url('modules/' . APPOINTLY_MODULE_NAME . '/assets/css/client_styles.css?v=' . time()); ?>">
    <link rel="stylesheet" type="text/css" id="roboto-css" href="<?php echo site_url('assets/plugins/roboto/roboto.css'); ?>">

</head>

<body>
    <div id="wrapper">
        <div class="content" style="overflow-x:hidden;">
            <div class="row">
                <div class="col-md-12 appointment">
                    <div class="appointment_logo"><?= get_company_logo(); ?>
                    </div>
                    <h3 class="appointly_status_placeholder">
                        <?php
                        if ($appointment['cancelled'] && $appointment['finished'] == 0) { ?>
                            <span class="label label-danger"><?= strtoupper(_l('appointment_status') . ': ' . _l('appointment_cancelled')); ?></span>
                        <?php } elseif ($appointment['approved'] && !$appointment['cancelled'] && !$appointment['finished']) { ?>
                            <span class="label label-success"><?= strtoupper(_l('appointment_status') . ': ' . _l('appointment_approved')); ?></span>
                        <?php } elseif (!$appointment['approved'] && !$appointment['cancelled'] && !$appointment['finished']) { ?>
                            <span class="label label-warning"><?= strtoupper(_l('appointment_status') . ': ' . _l('appointment_pending_approval')); ?></span>
                        <?php } else { ?>
                            <span class="label label-primary"><?= strtoupper(_l('appointment_status') . ': ' . _l('appointment_finished')); ?></span>
                        <?php } ?>
                    </h3>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-xs-12 col-lg-offset-3 fixpadding_clients">
                    <div class="panel_s <?= (!is_staff_logged_in()) ? 'nomargin' : '' ?>">
                        <div class="panel-body">

                            <div class="panel-heading info-header no-padding">
                                <h3> <?= _l('appointment_overview'); ?>
                                    <?php if (isset($appointment['google_meet_link'])) : ?>
                                        <div class="google_meet_client_main">
                                            <a href="<?= $appointment['google_meet_link']; ?>" target="_blank" data-toggle="tooltip" title="<?= _l('appointment_google_client_meet_info'); ?>">
                                                <img width="30" src="<?= base_url('/modules/appointly/assets/images/google_meet.png') ?>" alt="">
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </h3>

                            </div>
                            <div class="text-center" id="appointment_feedbacks">
                                <?php
                                if ($appointment['finished'] == 1) {
                                    echo renderAppointmentFeedbacks($appointment);
                                }
                                ?>
                            </div>
                            <?php if ($appointment['cancelled'] == 1) : ?>
                                <h3 class="text-danger text-center mtop5"><?= _l('appointment_cancelled_text'); ?>
                                </h3>
                            <?php endif; ?>
                            <div class="col-lg-12 col-xs-12">
                                <h4 class="appointly-default reorder-content"><?= _l('appointment_general_info'); ?>
                                </h4>
                                <div class="appointly_single_container">
                                    <span class="spmodified">
                                        <boldit><?= _l('appointment_initiated_by'); ?>
                                        </boldit><?= ($appointment['created_by']) ? get_staff_full_name($appointment['created_by']) : $appointment['name']; ?>
                                    </span><br>
                                    <span class="spmodified">
                                        <boldit><?= _l('appointment_description'); ?>
                                        </boldit> <?= $appointment['description']; ?>
                                    </span><br>
                                    <?php if (isset($appointment['details'])) : ?>
                                        <span class="spmodified">
                                            <boldit><?= _l('appointment_name'); ?>
                                            </boldit><?= isset($appointment['name']) ? $appointment['name'] : $appointment['details']['full_name']; ?>
                                        </span><br>
                                    <?php endif; ?>
                                    <?php if (isset($appointment['details'])) : ?>
                                        <span class="spmodified">
                                            <?php $mail_to = isset($appointment['email']) ? $appointment['email'] : $appointment['details']['email']; ?>
                                            <boldit><?= _l('appointment_email'); ?>
                                            </boldit><a href="mailto:<?= $mail_to; ?>"><?= $mail_to; ?></a>
                                        </span><br>
                                        <span class="spmodified">
                                            <boldit><?= _l('appointment_phone'); ?>
                                            </boldit>
                                            <?php
                                            if (isset($appointment['details']['phone'])) {
                                                $phoneToCall =  $appointment['details']['phone'];
                                            } elseif ($appointment['phone']) {
                                                $phoneToCall = $appointment['phone'];
                                            } else {
                                                $phoneToCall = '';
                                            }
                                            ?>
                                            <?php if ($phoneToCall !== '') : ?>
                                                <div class="client_numbers">
                                                    <a data-toggle="tooltip" class="label label-success" title="<?= _l('appointment_send_an_sms'); ?>" href="sms:<?= $phoneToCall; ?>&body=Hello">SMS:
                                                        <?= $phoneToCall; ?></a>
                                                    <a data-toggle="tooltip" class="label label-success mleft5" title="<?= _l('appointment_call_number') ?>" href="tel:<?= $phoneToCall; ?>">Call:
                                                        <?= $phoneToCall; ?></a>
                                                </div>
                                            <?php endif; ?>
                                        </span><br>
                                    <?php endif; ?>
                                    <span class="spmodified">
                                        <boldit><?= _l('appointment_location_address'); ?>
                                        </boldit>
                                        <?php $appAddress = $appointment['address'] ? $appointment['address'] : ''; ?>

                                        <a data-toggle="tooltip" title="Open in Google Maps" target="_blank" href="https://maps.google.com/?q=<?= $appAddress; ?>"><?= $appAddress; ?></a>
                                    </span><br>
                                    <span class="spmodified">
                                        <boldit><?= _l('appointment_meeting_time'); ?>
                                        </boldit> <?= _d($appointment['date']); ?>
                                    </span><br>
                                    <span class="spmodified">
                                        <boldit><?= _l('appointment_squeduled_at_text'); ?>
                                        </boldit> <?= date("H:i A", strtotime($appointment['start_hour'])); ?>
                                    </span><br>
                                    <?php if ($appointment['type_id'] != 0) { ?>
                                        <span class="spmodified">
                                            <boldit><?= _l('appointments_type_heading'); ?>
                                            </boldit>
                                            <?= get_appointment_type($appointment['type_id']); ?>
                                        </span>
                                        <br>
                                    <?php } ?>
                                    <?php
                                    $custom_fields = get_custom_fields('appointly');
                                    if ($custom_fields) {
                                        foreach ($custom_fields as $field) {
                                            $value = get_custom_field_value($appointment['id'], $field['id'], 'appointly');
                                            if ($value != '') { ?>
                                                <span class="spmodified">
                                                    <boldit><?= $field['name'] ?>
                                                    </boldit>
                                                    <span>
                                                        <?= ($value != '' ? $value : '-'); ?>
                                                    </span>
                                                </span>
                                                <br>
                                    <?php
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-lg-12 col-xs-12">
                                <h4 class="appointly-default reorder-content"><?= _l('appointment_staff_attendees'); ?>
                                </h4>
                                <div class="appointly_single_container">
                                    <span class="spmodified d-block">
                                        <?php
                                        if (!empty($appointment['attendees'])) {
                                            $role = '';
                                            foreach ($appointment['attendees'] as $staff) {
                                                $role = get_appointly_staff_userrole($staff['role']);
                                                if (!$role) {
                                                    if ($staff['admin']) {
                                                        $role = ' ' . _l('appointments_admin_label');
                                                    } else {
                                                        $role = ' ' . _l('appointments_staff_label');
                                                    }
                                                } elseif ($role && $staff['admin']) {
                                                    $role = ' ' . _l('appointments_admin_label') . ' / ' . $role;
                                                } else {
                                                    $role = " " . $role;
                                                }
                                                echo $staff['firstname'] . ' ' . $staff['lastname'] . ' - <strong>' . $role . '</strong><br>';
                                            } ?>
                                        <?php
                                        } else { ?>
                                            <strong> - &nbsp; <?= _l('appointment_no_assigned_staff_found'); ?></strong>
                                        <?php } ?>
                                    </span><br>
                                </div>
                            </div>
                            <div class="text-center padding-30">
                                <?php if ($appointment['finished'] == 0) : ?>
                                    <?php if ($appointment['cancelled'] == 0) : ?>
                                        <button <?= ($appointment['cancel_notes']) ? 'disabled' : ''; ?> class="btn btn-<?= ($appointment['cancel_notes']) ? 'mywarning' : 'mydanger'; ?>" data-toggle="modal" data-target="<?= ($appointment['cancel_notes']) ? 'return false' : '#cancellationModal'; ?>">
                                            <?= ($appointment['cancel_notes']) ? _l('appointment_pending_cancellation') : _l('appointment_cancel'); ?>
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="modal fade" role="dialog">
        <form>
            <div class="modal-dialog">
                <!-- Modal content-->
                <input type="text" name="hash" value="<?= $appointment['hash']; ?>" hidden>
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            &times;
                        </button>
                        <h4 class="modal-title"><?= _l('appointment_feedback_title'); ?>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <span><?= _l('appointmnet_feedback_comment'); ?></span>
                        <textarea required class="form-control" minlength="5" name="feedback_comment" id="feedback_comment" cols="30" rows="5" style="margin-top:10px;"></textarea>
                    </div>
                    <div id="messages">
                        <div class="alert text-center" id="review-alert"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="reviewModalSubmitBtn" class="btn btn-primary"><?= _l('appointment_submit'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!--Cancellation Modal -->
    <div id="cancellationModal" class="modal fade" role="dialog">
        <form>
            <div class="modal-dialog">
                <!-- Modal content-->
                <input type="text" name="hash" value="<?= $appointment['hash']; ?>" hidden>
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            &times;
                        </button>
                        <h4 class="modal-title"><?= _l('appointment_cancel'); ?>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <span><?= _l('appointment_description_to_cancel'); ?></span>
                        <textarea required class="form-control" minlength="5" name="notes" id="notes" cols="30" rows="5"></textarea>
                    </div>
                    <div id="messages">
                        <div class="alert text-center" id="alert"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="cancelAppointmentForm" class="btn btn-primary"><?= _l('appointment_request_to_cancel'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="<?= module_dir_url('appointly', 'assets\third-party\jquery\jquery-3.4.1.min.js'); ?>">
    </script>
    <script src="<?= site_url('assets\plugins\bootstrap\js\bootstrap.min.js'); ?>">
    </script>
    <?php
    if (is_staff_logged_in()) : ?>
        <style>
            .feedback_star.star_rated.hover_star i:before {
                color: #f37502;
            }

            .feedback_star.hover_star i:before {
                color: #333333;
            }
        </style>
    <?php endif; ?>
</body>
<?php require 'modules/appointly/assets/js/clients_hash_js.php'; ?>

</html>