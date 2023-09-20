<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="#" onclick="new_type(); return false;" class="btn btn-info pull-left display-block"><?php echo _l('reminder_new_services'); ?></a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <table class="table dt-table table-reminder-services" data-order-col="1" data-order-type="asc">
                            <thead>
                                <th><?php echo _l('reminder_id'); ?></th>
                                <th><?php echo _l('reminder_name_service'); ?></th>
                                <th><?php echo _l('reminder_amount'); ?></th>
                                <th><?php echo _l('reminder_options'); ?></th>
                            </thead>
                            <?php if (count($services) > 0) { ?>
                                <tbody>
                                    <?php foreach ($services as $source) { ?>
                                        <tr>
                                            <td><?php echo $source['id']; ?></td>
                                            <td>
                                                <span class="text-muted">
                                                    <?php echo $source['service_name']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted">
                                                    <?php echo $source['service_amount']; ?>
                                                </span>
                                                <span class="text-muted">
                                                    <?php echo $base_currency->symbol; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="#" onclick="edit_type(this)" data-id="<?php echo $source['id']; ?>" data-name="<?php echo $source['service_name']; ?>" data-amount="<?php echo $source['service_amount']; ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
                                                <a href="<?php echo admin_url('reminder/reminder_service_delete/' . $source['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/includes/modals/reminder_add_service'); ?>
<?php init_tail(); ?>
</body>

</html>