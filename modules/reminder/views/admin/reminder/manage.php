<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="_filters _hidden_inputs">
                <?php
                if (isset($statuses) && !empty($statuses)) {
                    foreach ($statuses as $_status) {
                        $val = '';
                        if ($_status == $this->input->get('status')) {
                            $val = $_status;
                        }
                        echo form_hidden('reminder_' . $_status, $val);
                    }
                }
                echo form_hidden('this_month', '');
                echo form_hidden('this_week', '');
                echo form_hidden('recurring_reminders', '');
                 echo form_hidden('all','');
                foreach ($years as $year) {
                    echo form_hidden('year_' . $year['year'], $year['year']);
                }
                foreach ($reminder_sale_agents as $agent) {
                    echo form_hidden('sale_agent_' . $agent['sale_agent']);
                }
                foreach ($clients as $cust) {
                    echo form_hidden('customer_' . $cust['userid']);
                }
                echo form_hidden('leads_related');
                echo form_hidden('customers_related');
                echo form_hidden('expired');
                echo form_hidden('isnotified', 0);
                $reminder_filter_number_val = !empty($this->session->userdata['reminder_filter_number']) ? $this->session->userdata['reminder_filter_number'] : '';
                echo form_hidden('reminder_filter_number', $reminder_filter_number_val);
                $reminder_filter_date_f_val = !empty($this->session->userdata['reminder_filter_date_f']) ? $this->session->userdata['reminder_filter_date_f'] : '';
                echo form_hidden('reminder_filter_date_f', $reminder_filter_date_f_val);
                $reminder_filter_date_t_val = !empty($this->session->userdata['reminder_filter_date_t']) ? $this->session->userdata['reminder_filter_date_t'] : '';
                echo form_hidden('reminder_filter_date_t', $reminder_filter_date_t_val);
                $reminder_filter_company_val = !empty($this->session->userdata['reminder_filter_company']) ? $this->session->userdata['reminder_filter_company'] : '';
                echo form_hidden('reminder_filter_company', $reminder_filter_company_val);
                $reminder_filter_contact_val = !empty($this->session->userdata['reminder_filter_contact']) ? $this->session->userdata['reminder_filter_contact'] : '';
                echo form_hidden('reminder_filter_contact', $reminder_filter_contact_val);
                $reminder_filter_description_val = !empty($this->session->userdata['reminder_filter_description']) ? $this->session->userdata['reminder_filter_description'] : '';
                echo form_hidden('reminder_filter_description', $reminder_filter_description_val);
                $reminder_filter_assigned_val = !empty($this->session->userdata['reminder_filter_assigned']) ? $this->session->userdata['reminder_filter_assigned'] : '';
                echo form_hidden('reminder_filter_assigned', $reminder_filter_assigned_val);

                echo form_hidden('rel_type_quotes');
                echo form_hidden('rel_type_estimate');
                echo form_hidden('rel_type_invoice');
                echo form_hidden('rel_type_credit_note');
                echo form_hidden('rel_type_tickets');
                foreach ($created_ids as $id) {
                    echo form_hidden('created_by_' . $id['by_staff']);
                }
                ?>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel_s mbot10">
                        <div class="panel-body _buttons">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <?php if (has_permission('reminder', '', 'create')) { ?>
                                            <a data-toggle="modal" data-target="#reminderAddModal" class="btn btn-info pull-left display-block">
                                                <?php echo _l('reminder_new'); ?>
                                            </a>
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo render_date_input('date_f', '', '', ['placeholder' => _l('rm_from_date')]); ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo render_date_input('date_t', '', '', ['placeholder' => _l('rm_to_date')]); ?>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="display-block text-right">
                                            <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-filter" aria-hidden="true"></i>
                                                </button>
                                                <ul class="dropdown-menu width300">
                                                    <li>
                                                        <a href="#" data-cview="all" onclick="dt_custom_view('1','.table-reminder','isnotified','clear'); return false;">
                                                            <?php echo _l('proposals_list_all'); ?>
                                                        </a>
                                                    </li>
                                                      <li class="divider"></li>
                                                     <li>
                                                        <a href="#" data-cview="this_month" onclick="dt_custom_view('this_month','.table-reminder','this_month'); return false;">
                                                            <?php echo _l('this_month'); ?>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" data-cview="this_week" onclick="dt_custom_view('this_week','.table-reminder','this_week'); return false;">
                                                            <?php echo _l('this_week'); ?>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" data-cview="recurring_reminders" onclick="dt_custom_view('recurring_reminders','.table-reminder','recurring_reminders'); return false;">
                                                            <?php echo _l('recurring_reminders'); ?>
                                                        </a>
                                                    </li>
                                                  
                                                    <?php if (count($years) > 0) { ?>
                                                        <?php foreach ($years as $year) { ?>
                                                            <li class="active">
                                                                <a href="#" data-cview="year_<?php echo $year['year']; ?>" onclick="dt_custom_view(<?php echo $year['year']; ?>,'.table-reminder','year_<?php echo $year['year']; ?>'); return false;"><?php echo $year['year']; ?>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <li class="divider"></li>
                                                    <?php } ?>
                                                    <li>
                                                        <a href="#" data-cview="isnotified" onclick="dt_custom_view('1','.table-reminder','isnotified'); return false;">
                                                            <?php echo _l('show_notified_reminder'); ?>
                                                        </a>
                                                    </li>
                                                    <?php if (count($reminder_sale_agents) > 0) { ?>
                                                        <div class="clearfix"></div>
                                                        <li class="divider"></li>
                                                        <li class="dropdown-submenu pull-left">
                                                            <a href="#" tabindex="-1"><?php echo _l('sale_agent_string'); ?></a>
                                                            <ul class="dropdown-menu dropdown-menu-left">
                                                                <?php foreach ($reminder_sale_agents as $agent) { ?>
                                                                    <li>
                                                                        <a href="#" data-cview="sale_agent_<?php echo $agent['sale_agent']; ?>" onclick="dt_custom_view('sale_agent_<?php echo $agent['sale_agent']; ?>','.table-reminder','sale_agent_<?php echo $agent['sale_agent']; ?>'); return false;"><?php echo get_staff_full_name($agent['sale_agent']); ?>
                                                                        </a>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        </li>
                                                    <?php } ?>
                                                    <?php if (count($clients) > 0) { ?>
                                                        <div class="clearfix"></div>
                                                        <li class="divider"></li>
                                                        <li class="dropdown-submenu pull-left">
                                                            <a href="#" tabindex="-1"><?php echo _l('customers'); ?></a>
                                                            <ul class="dropdown-menu dropdown-menu-left">
                                                                <?php foreach ($clients as $cust) { ?>
                                                                    <li>
                                                                        <a href="#" data-cview="customer_<?php echo $cust['userid']; ?>" onclick="dt_custom_view('customer_<?php echo $cust['userid']; ?>','.table-reminder','customer_<?php echo $cust['userid']; ?>'); return false;"><?php echo $cust['company']; ?>
                                                                        </a>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        </li>
                                                    <?php } ?>
                                                    <div class="clearfix"></div>
                                                    <li class="divider"></li>
                                                    <li class="dropdown-submenu pull-left">
                                                        <a href="#" tabindex="-1"><?php echo _l('reminder_rel_type'); ?></a>
                                                        <ul class="dropdown-menu dropdown-menu-left">
                                                            <li>
                                                                <a href="#" data-cview="rel_type_quotes" onclick="dt_custom_view('rel_type_quotes','.table-reminder','rel_type_quotes'); return false;"><?php echo _l('rm_proposals'); ?>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#" data-cview="rel_type_estimate" onclick="dt_custom_view('rel_type_estimate','.table-reminder','rel_type_estimate'); return false;"><?php echo _l('rm_estimates'); ?>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#" data-cview="rel_type_invoice" onclick="dt_custom_view('rel_type_invoice','.table-reminder','rel_type_invoice'); return false;"><?php echo _l('rm_invoices'); ?>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#" data-cview="rel_type_credit_note" onclick="dt_custom_view('rel_type_credit_note','.table-reminder','rel_type_credit_note'); return false;"><?php echo _l('rm_credit_notes'); ?>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#" data-cview="rel_type_tickets" onclick="dt_custom_view('rel_type_tickets','.table-reminder','rel_type_tickets'); return false;"><?php echo _l('rm_tickets'); ?>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <?php if (count($created_ids) > 0 && is_admin()) { ?>
                                                        <div class="clearfix"></div>
                                                        <li class="divider"></li>
                                                        <li class="dropdown-submenu pull-left">
                                                            <a href="#" tabindex="-1"><?php echo _l('reminder_created_by_th'); ?></a>
                                                            <ul class="dropdown-menu dropdown-menu-left">
                                                                <?php foreach ($created_ids as $id) { ?>
                                                                    <li>
                                                                        <a href="#" data-cview="created_by_<?php echo $id['by_staff']; ?>" onclick="dt_custom_view('created_by_<?php echo $id['by_staff']; ?>','.table-reminder','created_by_<?php echo $id['by_staff']; ?>'); return false;"><?php echo $id['full_name']; ?>
                                                                        </a>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                            <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs" onclick="reminder_toggle_small_view('.table-reminder','#reminder'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="small-table">
                            <div class="panel_s">
                                <div class="panel-body">
                                    <?php echo form_hidden('reminderid', $reminderid);
                                    $table_data = array(
                                        _l('reminder_date'),
                                        _l('reminder_assigned'),
                                        _l('reminder_customer'),
                                        _l('reminder_contact'),
                                        _l('reminder_description'),
                                        _l('reminder_amount'),
                                        _l('reminder_rel_type'),
                                        _l('reminder_status_th'),
                                        _l('reminder_created_by_th'),
                                        _l('is_recurring'),
                                    );
                                    $custom_fields = get_custom_fields('reminder', array('show_on_table' => 1));
                                    foreach ($custom_fields as $field) {
                                        array_push($table_data, $field['name']);
                                    }
                                    $table_data = hooks()->apply_filters('reminder_table_columns', $table_data);
                                    render_datatable($table_data, 'reminder', [], [
                                        'data-last-order-identifier' => 'reminder',
                                        'data-default-order'         => get_table_last_order('reminder'),
                                    ]);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 small-table-right-col">
                            <div id="reminder" class="hide">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="reminderModalData">
        </div>
        <?php $this->load->view('admin/includes/modals/reminder_add_file'); ?>
        <?php $this->load->view('admin/includes/modals/reminder_add_service');
        ?>
        <div id="reminderViewData" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
        <script>
            var hidden_columns = [3, 4, 5];
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
                csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        </script>
        <?php init_tail(); ?>
        <script type="text/javascript" src="<?php echo module_dir_url('reminder', 'assets/manage.js') ?>"></script>
        <script>
            $('#date_t').on('change', function() {
                if ($('#date_f').val()) {
                    $('input[name="reminder_filter_date_f"]').val($('#date_f').val());
                    $('input[name="reminder_filter_date_t"]').val($('#date_t').val());
                    $('.table-reminder').DataTable().ajax.reload();
                }
            });
            $('#date_f').on('change', function() {
                if ($('#date_t').val()) {
                    $('input[name="reminder_filter_date_f"]').val($('#date_f').val());
                    $('input[name="reminder_filter_date_t"]').val($('#date_t').val());
                    $('.table-reminder').DataTable().ajax.reload();
                }
            });

            function getViewModal(id = '') {
                $('body').append('<div class="dt-loader"></div>');
                $.post(admin_url + 'reminder/getreminderViewModal', {
                    id: id
                }).done(function(response) {
                    $('body').find('.dt-loader').remove();
                    $("#reminderViewData").html(response);
                    $("#reminderViewData").modal('show');
                });
            }
        </script>
        <script>
            $(document).ready(function() {
                $('.service_reminder').on('change', function() {
                    var service_name = $(this).attr('name');
                    if (service_name == "services[]") {
                        var service_data = $(this).val();
                        if (service_data != "") {
                            $('body').append('<div class="dt-loader"></div>');
                            $.post(admin_url + 'reminder/getreminderaddService', {
                                data_service: service_data
                            }).done(function(response) {
                                $('#reminderamount').val(response);
                                 $('body').find('.dt-loader').remove();
                            });
                        } else {
                            $('#reminderamount').val("0");
                             $('body').find('.dt-loader').remove();
                        }
                    }
                });
            });
            // For expenses and recurring tasks
            $(document).on('change', '[name="rel_type"]', function() {
                var val = $(this).val();
                val == 'service' ? $('.recurring_custom_service').removeClass('hide') : $('.recurring_custom_service').addClass('hide');
                if (val != "service") {
                    var data = "";
                    $("#amountreminder").val(data);
                    $("#reminderamount").val(data);
                }
            });
     
        </script>
        </body>

        </html>