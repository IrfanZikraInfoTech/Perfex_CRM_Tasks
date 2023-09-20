<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="reminder_add_service" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span id="service_type" class="edit-title"><?php echo (isset($reminderData)) ? _l('reminder_edit_service') : _l('reminder_service_type'); ?></span>
                </h4>
            </div>
            <?php echo form_open_multipart(admin_url() . 'reminder/reminder_new_service/', array('class' => '_transaction_form_reminder reminder-form new_items_table form_submit reminder_form_submit', 'id' => 'reminder-form_service')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning affect-warning hide">
                            <?php echo _l('changing_items_affect_warning'); ?>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="hidden" id="service_id" name="service_id" />
                                                <div class="form-group" app-field-wrapper="service_name"><label for="service_name" class="control-label"> <small class="req text-danger">* </small><?php echo _l('reminder_name_service'); ?></label><input type="text" id="service_name" name="service_name" class="form-control" value="">
                                                    <span class="errservice_name"></span>
                                                </div>

                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group" app-field-wrapper="service_amount">
                                                    <label for="service_amount" class="control-label"> <small class="req text-danger">* </small>
                                                        <?php echo _l('reminder_amount'); ?></label>
                                                    <div class="input-group" data-toggle="tooltip" title="<?php echo _l('reminder_amount'); ?>">
                                                        <input type="number" id="service_amount" name="service_amount" class="form-control" value="">
                                                        <div class="input-group-addon">
                                                            <?php echo $base_currency->symbol; ?>
                                                        </div>
                                                    </div>
                                                    <span class="errservice_amount"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="button" id="submit_button" class="btn btn-info reminder_form_service"><?php echo _l('submit'); ?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    function new_type() {
        $("#service_id").val("");
        $("#service_name").val("");
        $("#submit_button").attr("data-check_view","full_view");

        $("#service_amount").val("");
        $("#submit_button").html('Save');
        $("#service_type").html('Add new Service');
        $('#reminder_add_service').modal('show');
    }
    function edit_type(elem) {
        var dataId = $(elem).data("id");
        var datname = $(elem).data("name");
        var dataamount = $(elem).data("amount");
        $("#service_id").val(dataId);
        $("#service_name").val(datname);
        $("#service_amount").val(dataamount);
        $("#submit_button").html('Update');
        $("#service_type").html('Edit Service');
        $('#reminder_add_service').modal('show');
    }
</script>