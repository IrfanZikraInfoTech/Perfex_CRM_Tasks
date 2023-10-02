<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="col-md-12">
    <h4 class="bold well email-template-heading">
        <?php echo _l('appointment_appointments'); ?>
        <?php if ($hasPermissionEdit) { ?>
            <a href="<?php echo admin_url('emails/disable_by_type/appointly'); ?>" class="pull-right mleft5 mright25"><small><?php echo _l('disable_all'); ?></small></a>
            <a href="<?php echo admin_url('emails/enable_by_type/appointly'); ?>" class="pull-right"><small><?php echo _l('enable_all'); ?></small></a>
        <?php } ?>

    </h4>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><?php echo _l('email_templates_table_heading_name'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointly_templates as $template) { ?>
                    <tr>
                        <td class="<?php if ($template['active'] == 0) {
                                            echo 'text-throught';
                                        } ?>">
                            <a href="<?php echo admin_url('emails/email_template/' . $template['emailtemplateid']); ?>"><?php echo $template['name']; ?></a>
                            <?php if (ENVIRONMENT !== 'production') { ?>
                                <br /><small><?php echo $template['slug']; ?></small>
                            <?php } ?>
                            <?php if ($hasPermissionEdit && $template['slug'] != 'two-factor-authentication') { ?>
                                <a href="<?php echo admin_url('emails/' . ($template['active'] == '1' ? 'disable/' : 'enable/') . $template['emailtemplateid']); ?>" class="pull-right"><small><?php echo _l($template['active'] == 1 ? 'disable' : 'enable'); ?></small></a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>