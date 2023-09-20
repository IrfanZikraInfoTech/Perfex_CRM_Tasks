<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head();
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a data-toggle="tooltip" title="<?= _l('appointly_back_to_appointments'); ?>" href="<?= admin_url('appointly/appointments'); ?>" id="backToAppointments" class="btn btn-xs btn-info pull-left display-block">
                                <?php echo _l('appointment_appointments'); ?>
                            </a>
                            <div class="_filters _hidden_inputs hidden">
                                <?php echo form_hidden(
                                    'custom_view'
                                ); ?>
                            </div>
                            <div class="btn-group pull-right btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                                <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-filter" aria-hidden="true"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left width300 height500">
                                    <li class="filter-group active">
                                        <a href="#" data-cview="all" onclick="dt_custom_view('','.table-callbacks',''); return false;">
                                            <?php echo _l('all'); ?>
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <?php
                                    foreach (getCallbacksTableStatuses() as $key => $status) {
                                        $status = strtolower($status);
                                        ?>
                                        <li class="filter-group" data-filter-group="<?= $status; ?>">
                                            <a href="#" data-cview="<?= $status; ?>" onclick="dt_custom_view('<?= $status; ?>','.table-callbacks'); return false;">
                                                <?= ucfirst(_l($status)); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <?php render_datatable([
                            _l('callbacks_table_full_name'),
                            _l('callbacks_table_phone'),
                            _l('callbacks_table_status'),
                            _l('callbacks_table_timezone'),
                            _l('callbacks_table_avail_from'),
                            _l('callbacks_table_avail_to'),
                            _l('callbacks_table_date_requested'),
                            _l('callbacks_table_assigned_to'),
                            _l('options'),
                        ], 'callbacks'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modal_wrapper"></div>
<?php init_tail(); ?>
<?php require('modules/appointly/assets/js/callbacks_js.php'); ?>
</body>

</html>