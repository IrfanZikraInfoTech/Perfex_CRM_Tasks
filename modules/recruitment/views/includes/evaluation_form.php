<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div>
<div class="_buttons">
    <a href="#" onclick="new_evaluation_form(); return false;" class="btn btn-info pull-left display-block">
        <?php echo _l('new_evaluation_form'); ?>
    </a>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table">
 <thead>
    <th><?php echo _l('add_from'); ?></th>
    <th><?php echo _l('form_name'); ?></th>
    <th><?php echo _l('position'); ?></th>
    <th><?php echo _l('number_of_criteria'); ?></th>
    <th><?php echo _l('date_add'); ?></th>
    <th><?php echo _l('options'); ?></th>
 </thead>
 <tbody>
    <?php foreach ($list_form as $lf) {?>
    	<tr>
    		<td><a href="<?php echo admin_url('staff/profile/' . $lf['add_from']); ?>" ><?php echo staff_profile_image($lf['add_from'], ['staff-profile-image-small mright5'], 'small') . get_staff_full_name($lf['add_from']); ?></td>
    		<td><?php echo html_entity_decode($lf['form_name']); ?></td>
    		<td><?php if (get_rec_position_name($lf['position']) != '') {echo get_rec_position_name($lf['position']);} else {echo _l('all');}?></td>
    		<td><?php echo count_criteria($lf['form_id']); ?></td>
    		<td><?php echo _d($lf['add_date']); ?></td>
    		<td>
    			<?php if (has_permission('recruitment', '', 'edit') || is_admin()) {?>
	            <a href="#" onclick="edit_evaluation_form(this,<?php echo html_entity_decode($lf['form_id']); ?>); return false;" data-form_name="<?php echo html_entity_decode($lf['form_name']); ?>" data-position="<?php echo html_entity_decode($lf['position']); ?>"  class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i>
	            </a>
	            <?php }?>

	            <?php if (has_permission('recruitment', '', 'delete') || is_admin()) {?>
	            <a href="<?php echo admin_url('recruitment/delete_evaluation_form/' . $lf['form_id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
	             <?php }?>
    		</td>
    	</tr>
    <?php }?>
 </tbody>
</table>
<div class="modal fade" id="evaluation_form" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('recruitment/evaluation_form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_evaluation_form'); ?></span>
                    <span class="add-title"><?php echo _l('new_evaluation_form'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                     <div id="additional_criteria"></div>
                     <div class="form">
                        <div class="col-md-12">
                        <p class="bold margin-top-15 general-infor-color"><?php echo _l('general_infor'); ?></p>
              			<hr class="margin-top-10 general-infor-hr"/>

                        <?php
$attr = [];
$attr['required'] = "";
echo render_input('form_name', '<span class="text-danger">* </span>' . _l('form_name'), '', 'text', $attr);?>

                        <div id="select_job_position">
                            <label for="job_position"><?php echo _l('job_position'); ?></label>
                            <select name="job_position" id="job_position" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('all'); ?>" >
                              <option value=""></option>
                              <?php foreach ($positions as $gr) {?>
                                <option value="<?php echo html_entity_decode($gr['position_id']); ?>"><?php echo html_entity_decode($gr['position_name']); ?></option>
                              <?php }?>
                            </select>
                            <br><br>
                        </div>

                        <p class="bold margin-top-15 general-infor-color"><?php echo _l('list_of_evaluation_criteria'); ?></p>
              			<hr class="margin-top-10 general-infor-hr"/>
              			<!-- list criteria -->
              			<div id="list_criteria">
	              			<div class="new-kpi-group-al">
	              			<div id="new_kpi_group" class="col-md-12">

	                          <div class="row margin-top-10">
	                            <div class="col-md-12">
	                                <label for="group_criteria[0]" class="control-label"><span class="text-danger">* </span><?php echo _l('group_criteria'); ?></label>
	                                  <select onchange="group_criteria_change(this)" name="group_criteria[0]" class="selectpicker" id="group_criteria[0]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required>
	                                    <option value=""></option>
	                                    <?php foreach ($list_group as $kpi_coll) {?>
	                                  <option value="<?php echo html_entity_decode($kpi_coll['criteria_id']); ?>"> <?php echo html_entity_decode($kpi_coll['criteria_title']); ?></option>
	                                  <?php }?>
	                                  </select>
	                            </div>

	                          </div>
								<br>
	                          <div class="row " >

	                            <div class="col-md-11 new-kpi-al pull-right padding-left-right-20-0">
	                              <div id ="new_kpi" class="row paddig-top-height-0-75">

	                                <div class="col-md-7 padding-right-0">
	                                  <label for="evaluation_criteria[0][0]" class="control-label get_id_row" value ="0" ><span class="text-danger">* </span><?php echo _l('evaluation_criteria'); ?></label>
	                                  <select name="evaluation_criteria[0][0]" class="selectpicker" id="evaluation_criteria[0][0]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-sl-id="e_criteria[0]" required>


	                                  </select>
	                                </div>

	                                <div class="col-md-3 padding-right-0">
	                                  <label for="percent[0][0]" class="control-label"><span class="text-danger">* </span><?php echo _l('proportion') ?></label>
	                              	  <input type="number" id="percent[0][0]" name="percent[0][0]" class="form-control" min="1" max="100" step="1" value="" aria-invalid="false" required>
	                                </div>
	                                <div class="col-md-1" name="button_add_kpi lightheitgh-84-nowrap">
	                                  <button name="add" class="btn new_kpi btn-success border-radius-20" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
	                                </div>


	                              </div>
	                            </div>

	                          </div>

	                          <div class="row">
	                            <div class="col-md-2 " name="button_add_kpi_group lightheitgh-84-nowrap">
	                                    <button name="add_kpi_group" class="btn new_kpi_group btn-success border-radius-20" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
	                            </div>
	                          </div>

	                        </div>
	                    	</div>
	                    </div>
	                    <!-- list criteria -->

                        </div>



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
