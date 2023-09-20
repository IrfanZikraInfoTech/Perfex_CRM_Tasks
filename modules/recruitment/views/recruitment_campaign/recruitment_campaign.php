<?php init_head();?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12" id="small-table">
            <div class="panel_s">
               <div class="panel-body">
                <?php echo form_hidden('campaign_id', $campaign_id); ?>
                  <div class="row">
                     <div class="col-md-12">
                      <h4 class="no-margin font-bold"><i class="fa fa-sitemap" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                      <hr />
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-3  ">
                        <?php if (has_permission('recruitment', '', 'create') || is_admin()) {?>
                        <a href="#" onclick="new_campaign(); return false;" class="btn btn-info pull-left display-block">
                            <?php echo _l('new_campaign'); ?>
                        </a>
                        <?php }?>
                    </div>
                    <div class=" col-md-3">
                      <select name="department_filter[]" id="department_filter" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('filter_by_department'); ?>">

                          <?php foreach ($departments as $s) {?>
                            <option value="<?php echo html_entity_decode($s['departmentid']); ?>"><?php echo html_entity_decode($s['name']); ?></option>
                            <?php }?>
                        </select>
                    </div>
                    <div class=" col-md-3">
                      <select name="position_filter[]" id="position_filter" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('filter_by_position'); ?>">

                          <?php foreach ($positions as $s) {?>
                            <option value="<?php echo html_entity_decode($s['position_id']); ?>"><?php echo html_entity_decode($s['position_name']); ?></option>
                            <?php }?>
                        </select>
                    </div>
                    <div class=" col-md-2">
                      <div class="form">
                      <select name="status_filter[]" id="status_filter" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('filter_by_status'); ?>">

                          <option value="1"><?php echo _l('planning'); ?></option>
                          <option value="2"><?php echo _l('overdue'); ?></option>
                          <option value="3"><?php echo _l('in_progress'); ?></option>
                          <option value="4"><?php echo _l('finish'); ?></option>
                          <option value="4"><?php echo _l('cancel'); ?></option>
                      </select>
                      </div>
                    </div>
                    <div class="col-md-1 pull-right">
                        <a href="#" class="btn btn-default pull-right btn-with-tooltip toggle-small-view hidden-xs" onclick="toggle_small_view_campaign('.campaign_sm','#campaign_sm_view'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
                    </div>
                    </div>
                    <br><br>
                  <?php render_datatable(array(
	_l('campaign_name'),
	_l('position'),
	_l('form_of_work'),
	_l('department'),
	_l('amount_recruiment'),
	_l('status'),
), 'table_rec_campaign', ['campaign_sm' => 'campaign_sm']);?>
               </div>
            </div>
         </div>
         <div class="col-md-7 small-table-right-col">
            <div id="campaign_sm_view" class="hide">
            </div>
         </div>
      </div>
   </div>

</div>
<div class="modal fade" id="recruitment_campaign" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
        <?php echo form_open_multipart(admin_url('recruitment/campaign'), array('id' => 'recruitment-campaign-form')); ?>
        <div class="modal-content width-125">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_campaign'); ?></span>
                    <span class="add-title"><?php echo _l('new_campaign'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div id="additional_campaign"></div>
                <div class="horizontal-scrollable-tabs preview-tabs-top">
                  <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                  <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                  <div class="horizontal-tabs">
                  <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                   <li role="presentation" class="active">
                       <a href="#general_infor" aria-controls="general_infor" role="tab" data-toggle="tab" aria-controls="general_infor">
                       <span class="glyphicon glyphicon-align-justify"></span>&nbsp;<?php echo _l('general_infor'); ?>
                       </a>
                    </li>
                    <li role="presentation">
                       <a href="#candidate_request" aria-controls="candidate_request" role="tab" data-toggle="tab" aria-controls="candidate_request">
                       <i class="fa fa-group"></i>&nbsp;<?php echo _l('candidate_request'); ?>
                       </a>
                    </li>
                   </ul>
                 </div>
               </div>
               <div class="tab-content">
                  <div role="tabpanel" class="tab-pane active" id="general_infor">
                    <div class="col-md-4">
                      <?php echo render_input('campaign_code', 'campaign_code', ''); ?>
                    </div>
                    <div class="col-md-8">
                      <?php echo render_input('campaign_name', 'campaign_name', ''); ?>
                    </div>

                    <div class="col-md-12 <?php  if(get_recruitment_option('recruitment_create_campaign_with_plan') == 1 ){ echo 'hide';} ;?>">
                      <label for="cp_proposal"><?php echo _l('recruitment_proposal'); ?></label>
                        <select name="cp_proposal[]" id="proposal" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">

                          <?php foreach ($rec_proposal as $s) {?>
                            <option value="<?php echo html_entity_decode($s['id']); ?>"><?php echo html_entity_decode($s['proposal_name']); ?></option>
                            <?php }?>
                        </select>
                        <br><br>
                    </div>

                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="rec_channel_form_id"><?php echo _l('recruitment_channel_form'); ?></label>
                        <select name="rec_channel_form_id" id="rec_channel_form_id" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <?php foreach ($rec_channel_form as $rec_c_f) {?>
                            <option value="<?php echo html_entity_decode($rec_c_f['id']); ?>"><?php echo html_entity_decode($rec_c_f['r_form_name']); ?></option>
                            <?php }?>
                        </select>
                      </div>
                    </div>


                    <div class="col-md-6">
                      <label for="position"><small class="req text-danger">* </small> <?php echo _l('position'); ?></label>
                        <select name="cp_position" id="position" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" required>
                          <option value=""></option>
                          <?php foreach ($positions as $s) {?>
                            <option value="<?php echo html_entity_decode($s['position_id']); ?>"><?php echo html_entity_decode($s['position_name']); ?></option>
                            <?php }?>
                        </select>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                         <label  for="company_id"><?php echo _l('company'); ?></label>
                          <select  name="company_id" id="company_id" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                              <option value=''></option>
                              <?php foreach ($company_list as $company) {?>
                                <option value="<?php echo html_entity_decode($company['id']); ?>"><?php echo html_entity_decode($company['company_name']); ?></option>
                                <?php }?>
                          </select>
                      </div>
                    </div>


                    <div class="col-md-6">
                       <?php echo render_input('cp_amount_recruiment', 'amount_recruiment', '', 'number'); ?>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                      <label for="form_of_work"><?php echo _l('form_of_work'); ?></label>
                        <select name="cp_form_work" id="form_of_work" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <option value="intership"><?php echo _l('intership'); ?></option>
                          <option value="full_time"><?php echo _l('full_time'); ?></option>
                          <option value="part_time"><?php echo _l('part_time'); ?></option>
                          <option value="collaborators"><?php echo _l('collaborators'); ?></option>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                      <label for="department"><?php echo _l('department'); ?></label>
                        <select name="cp_department" id="department" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <?php foreach ($departments as $s) {?>
                            <option value="<?php echo html_entity_decode($s['departmentid']); ?>"><?php echo html_entity_decode($s['name']); ?></option>
                            <?php }?>
                        </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                      <?php echo render_input('cp_workplace', 'workplace', ''); ?>
                    </div>
                    <div class="col-md-6"> <?php
                      $attr = array();
                      $attr = ['data-type' => 'currency'];
                    ?>

                          <label><?php echo _l('starting_salary_from'); ?></label>
                          <div class="input-group">
                              <input type="text" class="form-control text-right" name="cp_salary_from" value="" data-type="currency">

                             <div class="input-group-addon">
                                <div class="dropdown">
                                   <span class="discount-type-selected">
                                    <?php echo html_entity_decode($base_currency->name) ;?>
                                   </span>
                                </div>
                             </div>
                          </div>

                    </div>

                    <div class="col-md-6"> 
                      
                      <label><?php echo _l('starting_salary_to'); ?></label>
                      <div class="input-group">
                        <input type="text" class="form-control text-right" name="cp_salary_to" value="" data-type="currency">

                        <div class="input-group-addon">
                          <div class="dropdown">
                            <span class="discount-type-selected">
                              <?php echo html_entity_decode($base_currency->name) ;?>
                            </span>
                          </div>
                        </div>
                      </div>

                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                          <div class="checkbox checkbox-primary">
                            <input  type="checkbox" id="display_salary" name="display_salary" value="display_salary">

                            <label for="display_salary"><?php echo _l('rec_display_salary'); ?><small ><?php echo _l('rec_display_salary_tooltip') ?> </small>
                            </label>
                          </div>
                        </div>
                    </div>  

                    <div class="col-md-6"> <?php echo render_date_input('cp_from_date', 'from_date', ''); ?></div>
                    <div class="col-md-6"> <?php echo render_date_input('cp_to_date', 'to_date', ''); ?></div>

                    <div class="col-md-12"> <?php echo render_textarea('cp_reason_recruitment', 'reason_recruitment', '') ?></div>
                    <div class="col-md-12"> <?php echo render_textarea('cp_job_description', 'job_description', '', array(), array(), '', 'tinymce') ?></div>
                    <div class="col-md-6">
                      <label for="cp_manager"><?php echo _l('manager'); ?></label>
                      <select name="cp_manager[]" id="manager" class="selectpicker" multiple="true" data-actions-box="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">

                        <?php foreach ($staffs as $s) {?>
                          <option value="<?php echo html_entity_decode($s['staffid']); ?>"><?php echo html_entity_decode($s['firstname'] . ' ' . $s['lastname']); ?></option>
                          <?php }?>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label for="cp_follower"><?php echo _l('follower'); ?></label>
                      <select name="cp_follower[]" id="follower" class="selectpicker" multiple="true" data-actions-box="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">

                        <?php foreach ($staffs as $s) {?>
                          <option value="<?php echo html_entity_decode($s['staffid']); ?>"><?php echo html_entity_decode($s['firstname'] . ' ' . $s['lastname']); ?></option>
                          <?php }?>
                      </select>
                    </div>

                  </div>

                  <div role="tabpanel" class="tab-pane" id="candidate_request">

                    <div class="col-md-6"> <?php echo render_input('cp_ages_from', 'ages_from', '', 'number'); ?></div>
                    <div class="col-md-6"> <?php echo render_input('cp_ages_to', 'ages_to', '', 'number'); ?></div>

                    <div class="col-md-4">
                      <label for="cp_gender"><?php echo _l('gender'); ?></label>
                      <select name="cp_gender" id="gender" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                        <option value=""></option>
                        <option value="male"><?php echo _l('male'); ?></option>
                        <option value="female"><?php echo _l('female'); ?></option>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label for="profit"><?php echo _l('height') ?></label>
                      <div class="input-group">
                        <span  class="input-group-addon">
                                 <?php
echo '>=';
?>
                         </span>
                         <input type="number" id="height" name="cp_height" class="form-control text-aligh-right" value="" min="0" max="3" step="0.1">

                      </div>
                    </div>
                    <div class="col-md-4">
                      <label for="profit"><?php echo _l('weight') ?></label>
                      <div class="input-group">
                        <span  class="input-group-addon">
                                 <?php
echo '>=';
?>
                         </span>
                         <input type="weight" id="weight" name="cp_weight" class="form-control text-aligh-right" value="">

                      </div>
                      <br>
                    </div>

                    <div class="col-md-6">
                      <label for="literacy"><?php echo _l('literacy'); ?></label>
                        <select name="cp_literacy" id="literacy" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('not_required'); ?>">
                          <option value=""></option>
                          <option value="primary_level"><?php echo _l('primary_level'); ?></option>
                          <option value="intermediate_level"><?php echo _l('intermediate_level'); ?></option>
                          <option value="college_level"><?php echo _l('college_level'); ?></option>
                          <option value="masters"><?php echo _l('masters'); ?></option>
                          <option value="doctor"><?php echo _l('doctor'); ?></option>
                          <option value="bachelor"><?php echo _l('bachelor'); ?></option>
                          <option value="engineer"><?php echo _l('engineer'); ?></option>
                          <option value="university"><?php echo _l('university'); ?></option>
                          <option value="intermediate_vocational"><?php echo _l('intermediate_vocational'); ?></option>
                          <option value="college_vocational"><?php echo _l('college_vocational'); ?></option>
                          <option value="in-service"><?php echo _l('in-service'); ?></option>
                          <option value="high_school"><?php echo _l('high_school'); ?></option>
                          <option value="intermediate_level_pro"><?php echo _l('intermediate_level_pro'); ?></option>
                        </select>
                    </div>
                    <div class="col-md-6">
                      <label for="experience"><?php echo _l('experience'); ?></label>
                        <select name="cp_experience" id="experience" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <option value="no_experience_yet"><?php echo _l('no_experience_yet'); ?></option>
                          <option value="less_than_1_year"><?php echo _l('less_than_1_year'); ?></option>
                          <option value="1_year"><?php echo _l('1_year'); ?></option>
                          <option value="2_years"><?php echo _l('2_years'); ?></option>
                          <option value="3_years"><?php echo _l('3_years'); ?></option>
                          <option value="4_years"><?php echo _l('4_years'); ?></option>
                          <option value="5_years"><?php echo _l('5_years'); ?></option>
                          <option value="over_5_years"><?php echo _l('over_5_years'); ?></option>
                        </select>
                        <br><br>
                    </div>
                    <div class="col-md-12">
                          <?php echo render_input('file', 'file_campaign', '', 'file') ?>

                    </div>
                  </div>
                </div>
                <div class="col-md-12"><hr/></div>

            </div>
            <div class="modal-footer border-top-0">
                <button type="" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button id="sm_btn" type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

<?php init_tail();?>
</body>
</html>