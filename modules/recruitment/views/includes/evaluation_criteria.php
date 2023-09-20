<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div>
<div class="_buttons">
    <a href="#" onclick="new_evaluation_criteria(); return false;" class="btn btn-info pull-left display-block">
        <?php echo _l('new_evaluation_criteria'); ?>
    </a>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table">
 <thead>
    <th><?php echo _l('add_from'); ?></th>
    <th><?php echo _l('criteria_title'); ?></th>
    <th><?php echo _l('type'); ?></th>
    <th><?php echo _l('date_add'); ?></th>
    <th><?php echo _l('options'); ?></th>
 </thead>
 <tbody>
    <?php foreach ($group_criterias as $gr) {
	?>

    <tr>
        <td><a href="<?php echo admin_url('staff/profile/' . $gr['add_from']); ?>" ><?php echo staff_profile_image($gr['add_from'], ['staff-profile-image-small mright5'], 'small') . get_staff_full_name($gr['add_from']); ?></a></td>
        <td>
            <?php if ($gr['criteria_type'] == 'group_criteria') {
		echo '<p class="bold"><i class="fa fa-folder-open"></i>' . ' ' . $gr['criteria_title'] . '</p>';
	} else {
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $gr['criteria_title'];
	}
	?>
        </td>
        <td><?php echo _l($gr['criteria_type']); ?></td>
        <td><?php echo _d($gr['add_date']); ?></td>
        <td>
            <?php if (has_permission('recruitment', '', 'edit') || is_admin()) {?>
            <a href="#" onclick="edit_evaluation_criteria(this,<?php echo html_entity_decode($gr['criteria_id']); ?>); return false;" data-criteria_title="<?php echo html_entity_decode($gr['criteria_title']); ?>" data-criteria_type="<?php echo html_entity_decode($gr['criteria_type']); ?>" data-description="<?php echo html_entity_decode($gr['description']); ?>" data-group_criteria="<?php echo html_entity_decode($gr['group_criteria']); ?>" data-score_des1="<?php echo html_entity_decode($gr['score_des1']); ?>" data-score_des2="<?php echo html_entity_decode($gr['score_des2']); ?>" data-score_des3="<?php echo html_entity_decode($gr['score_des3']); ?>" data-score_des4="<?php echo html_entity_decode($gr['score_des4']); ?>" data-score_des5="<?php echo html_entity_decode($gr['score_des5']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i>
            </a>
            <?php }?>

            <?php if (has_permission('recruitment', '', 'delete') || is_admin()) {?>
            <a href="<?php echo admin_url('recruitment/delete_evaluation_criteria/' . $gr['criteria_id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
             <?php }?>
        </td>
    </tr>
    <?php }?>
 </tbody>
</table>
<div class="modal fade" id="evaluation_criteria" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('recruitment/evaluation_criteria')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_evaluation_criteria'); ?></span>
                    <span class="add-title"><?php echo _l('new_evaluation_criteria'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                     <div id="additional_criteria"></div>
                     <div class="form">
                        <div class="col-md-12">
                        <label for="criteria_type"><span class="text-danger">* </span><?php echo _l('criteria_type'); ?></label>
                        <select name="criteria_type" id="criteria_type" onchange="criteria_type_change(this); return false;" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" required>
                          <option value=""></option>
                          <option value="criteria"><?php echo _l('criteria'); ?></option>
                          <option value="group_criteria"><?php echo _l('group_criteria'); ?></option>
                        </select>
                        <br><br>

                        <?php
$attr = [];
$attr['required'] = "";
echo render_input('criteria_title', '<span class="text-danger">* </span>' . _l('criteria_title'), '', 'text', $attr);?>

                        <div id="select_group_criteria">
                            <label for="group_criteria"><span id="group_span" class="text-danger">* </span><?php echo _l('group_criteria'); ?></label>
                            <select name="group_criteria" id="group_criteria" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" required>
                              <option value=""></option>
                              <?php foreach ($list_group as $gr) {?>
                                <option value="<?php echo html_entity_decode($gr['criteria_id']); ?>"><?php echo html_entity_decode($gr['criteria_title']); ?></option>
                              <?php }?>
                            </select>
                            <br><br>
                        </div>

                        <?php echo render_textarea('description', 'description', '') ?>
                        </div>

                        <div class="col-md-3 mbot5"><label for="score1"><?php echo _l('scores'); ?></label><input type="text" class="form-control" disabled="true" name="score1" value="<?php echo _l('score1'); ?>" placeholder="<?php echo _l('description_for_score'); ?>" /></div>
                        <div class="col-md-9 mbot5 padding-left-0"><label for="score1"><?php echo _l('scores_des'); ?></label><input type="text" class="form-control" name="score_des1" value=""  placeholder="<?php echo _l('description_for_score'); ?>" /></div>

                        <div class="col-md-3 mbot5"><input type="text" class="form-control" disabled="true" name="score2" value="<?php echo _l('score2'); ?>" /></div>
                        <div class="col-md-9 mbot5 padding-left-0"><input type="text" class="form-control" name="score_des2" value="" placeholder="<?php echo _l('description_for_score'); ?>" /></div>

                        <div class="col-md-3 mbot5"><input type="text" class="form-control" disabled="true" name="score1" value="<?php echo _l('score3'); ?>" /></div>
                        <div class="col-md-9 mbot5 padding-left-0"><input type="text" class="form-control" name="score_des3" value="" placeholder="<?php echo _l('description_for_score'); ?>" /></div>

                        <div class="col-md-3 mbot5"><input type="text" class="form-control" disabled="true" name="score1" value="<?php echo _l('score4'); ?>" /></div>
                        <div class="col-md-9 mbot5 padding-left-0"><input type="text" class="form-control" name="score_des4" value="" placeholder="<?php echo _l('description_for_score'); ?>" /></div>

                        <div class="col-md-3"><input type="text" class="form-control" disabled="true" name="score1" value="<?php echo _l('score5'); ?>" /></div>
                        <div class="col-md-9 padding-left-0"><input type="text" class="form-control" name="score_des5" value="" placeholder="<?php echo _l('description_for_score'); ?>" /></div>

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
</body>
</html>
