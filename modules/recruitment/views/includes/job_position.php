<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div>
<div class="_buttons">
    <a href="#" onclick="new_job_position(); return false;" class="btn btn-info pull-left display-block">
        <?php echo _l('new_job_position'); ?>
    </a>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table">
 <thead>
    <th><?php echo _l('id'); ?></th>
    <th><?php echo _l('job_position'); ?></th>
    <th><?php echo _l('industry'); ?></th>
    <th><?php echo _l('options'); ?></th>
 </thead>
 <tbody>
    <?php foreach ($positions as $job) {?>
    <tr>
       <td><?php echo html_entity_decode($job['position_id']); ?></td>
       <td><?php echo html_entity_decode($job['position_name']); ?></td>
       <td><?php echo html_entity_decode(get_rec_industry_name($job['industry_id'])); ?></td>
       <td>
        <a href="#" onclick="edit_job_position(this,<?php echo html_entity_decode($job['position_id'] ?? ''); ?>); return false" data-name="<?php echo html_entity_decode($job['position_name'] ?? ''); ?>" data-position_description="<?php echo html_entity_decode($job['position_description'] ?? ''); ?>" data-industry_id="<?php echo html_entity_decode($job['industry_id'] ?? ''); ?>"  data-job_skill="<?php echo html_entity_decode($job['job_skill'] ?? ''); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil"></i></a>

          <a href="<?php echo admin_url('recruitment/delete_job_position/' . $job['position_id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
       </td>
    </tr>
    <?php }?>
 </tbody>
</table>
<div class="modal fade" id="job_position" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('recruitment/job_position')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_job_position'); ?></span>
                    <span class="add-title"><?php echo _l('new_job_position'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                     <div id="additional"></div>
                     <div class="form">
                        <?php echo render_input('position_name', 'job_position'); ?>

                       <div class="form-group">
                        <label for="job_skill[]" class="control-label"><?php echo _l('skill_name'); ?></label> 
                        <select name="job_skill[]" id="job_skill" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

                          <?php foreach($skills as $dpkey =>  $skill){ ?>
                          <option value="<?php echo html_entity_decode($skill['id']); ?>" > <?php echo html_entity_decode($skill['skill_name']); ?></option>                  
                          <?php }?>
                        </select>
                        </div>


                    <div class="form-group">
                       <label  for="industry_id"><?php echo _l('industry'); ?></label>
                        <select  name="industry_id" id="industry_id" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                            <option value=''></option>
                            <?php foreach ($industry_list as $industry) {?>
                              <option value="<?php echo html_entity_decode($industry['id']); ?>"><?php echo html_entity_decode($industry['industry_name']); ?></option>
                              <?php }?>
                        </select>
                    </div>


                        <?php echo render_textarea('position_description', 'description', '') ?>
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
