<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$CI = &get_instance();
$CI->load->model('appointly/appointly_model', 'apm');
$appointments = $CI->apm->fetch_todays_appointments();
?>
<div class="widget" id="widget-<?php echo basename(__FILE__, ".php"); ?>" data-name="<?= _l('appointment_todays_appointments'); ?>">
    <div class="panel_s todo-panel">
        <div class="panel-body padding-10">
            <div class="widget-dragger"></div>
            <span class="label label-info label-big pull-right mtop5"><?= _d(date('Y-m-d')); ?></span>
            <?php if (!empty($appointments)) { ?>
                <h4 class="mleft10"><?= _l('appointment_todays_appointments'); ?></h4>
                <hr class="mbot0">
            <?php } else { ?>
                <h4 class="mleft10"><?= _l('appointment_no_appointments'); ?></h4>
            <?php } ?>
            <?php if (!empty($appointments)) { ?>
                <?php foreach ($appointments as $appointment) { ?>
                    <div class="todays_appointment col-2 mleft20 appointly-secondary pull-left mtop10">
                        <h3 class="text-muted mtop1"><a href="<?= admin_url('appointly/appointments/view?appointment_id=' . $appointment['id']); ?>"><?= $appointment['subject']; ?></a></h3>
                        <span class="text-muted span_limited">
                            <?= _l('appointment_description'); ?> <?= $appointment['description']; ?>
                        </span>
                        <h5 class="no-margin">
                            <span class="text-warning"><?= _l('appointment_scheduled_at'); ?> </span>
                            <?= date("H:i A", strtotime($appointment['start_hour'])); ?>
                        </h5>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</div>