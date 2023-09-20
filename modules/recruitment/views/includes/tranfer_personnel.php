<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div>
<div class="_buttons">
    <a href="#" onclick="new_setting_tranfer(); return false;" class="btn btn-info pull-left display-block">
        <?php echo _l('new'); ?>
    </a>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table">
 <thead>
    <th><?php echo _l('subject'); ?></th>
    <th><?php echo _l('add_from'); ?></th>
    <th><?php echo _l('date_add'); ?></th>
    <th><?php echo _l('options'); ?></th>
 </thead>
 <tbody>
    <?php foreach ($list_set_tran as $li) {?>
        <tr>
             <td><?php echo html_entity_decode($li['subject']); ?></td>
            <td><a href="<?php echo admin_url('staff/profile/' . $li['add_from']); ?>" ><?php echo staff_profile_image($li['add_from'], ['staff-profile-image-small mright5'], 'small') . get_staff_full_name($li['add_from']); ?></td>

            <td><?php echo _d($li['add_date']); ?></td>
            <td>
                <a href="#" onclick="edit_setting_tranfer(this,<?php echo html_entity_decode($li['set_id']); ?>); return false" data-subject="<?php echo html_entity_decode($li['subject']); ?>" data-send_to="<?php echo html_entity_decode($li['send_to']); ?>"  data-email_to="<?php echo html_entity_decode($li['email_to']); ?>" data-order="<?php echo html_entity_decode($li['order']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>

                <a href="<?php echo admin_url('recruitment/delete_setting_tranfer/' . $li['set_id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
            </td>
        </tr>
    <?php }?>
 </tbody>
</table>
<div class="modal fade" id="setting_tranfer" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open_multipart(admin_url('recruitment/setting_tranfer')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_setting_tranfer'); ?></span>
                    <span class="add-title"><?php echo _l('new_setting_tranfer'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                     <div id="additional_tranfer"></div>
                     <div class="form">
                        <?php $attr = [];
$attr['required'] = '';
echo render_input('order', '<span class="text-danger">* </span>' . _l('order') . ' ' . '<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="' . _l('tooltip_order') . '"></i>', '', 'number', $attr);?>

                        <label for="send_to"><span class="text-danger">* </span><?php echo _l('send_to'); ?></label>
                        <select name="send_to" id="send_to" onchange="change_send_to(this); return false;" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" required>
                            <option value=""></option>
                            <option value="candidate" ><?php echo _l('successful_candidates'); ?></option>
                            <option value="staff" ><?php echo _l('staff'); ?></option>
                            <option value="department" ><?php echo _l('department'); ?></option>
                        </select>
                        <br><br>
                        <div id="email_to_div" class="hide">
                            <label for="email_to"><span class="text-danger">* </span><?php echo _l('email_to'); ?></label>
                            <select name="email_to[]" id="email_to" class="selectpicker" multiple="true" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">

                            </select>
                            <br><br>
                        </div>



                        <?php
echo render_input('subject', '<span class="text-danger">* </span>' . _l('subject') . '', '', 'text', $attr); ?>
                        <?php echo render_textarea('content', 'content', '', array(), array(), '', 'tinymce') ?>
                        
                    <!-- view attachment file -->
                    <div class="row">
                      <div id="tranfer_personnel_attachments" class="mtop30 col-md-12 ">

                      </div>
                    </div>

                        <?php echo render_input('attachment', 'attachment', '', 'file'); ?>
                    </div>
                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
<div id="personnel_attachments_file_data"></div>
</body>
</html>
