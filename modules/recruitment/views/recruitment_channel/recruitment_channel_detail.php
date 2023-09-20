<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<?php init_head();?>
<div id="wrapper">
<div class="content">
   <div class="row">
      <div class="col-md-12" id="training-add-edit-wrapper">
         <div class="row">
            <div class="col-md-12">
               <div class="panel_s">
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-12 margin-left-12">
                      <h4 class="modal-title pl-3">
                          <span class="edit-title"><?php echo _l('add_recuitment_channel'); ?></span>

                      </h4>
                    </div>
                  </div>
                <div class="modal-body">
                    <div class="horizontal-scrollable-tabs preview-tabs-top mb-2">
                      <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                      <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                      <div class="horizontal-tabs">
                      <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                       <li role="presentation" class="active">
                           <a href="#form_infomation" aria-controls="form_infomation" role="tab" data-toggle="tab" aria-controls="form_infomation">
                           <span class="glyphicon glyphicon-align-justify"></span>&nbsp;<?php echo _l('form_infomation'); ?>
                           </a>
                        </li>
                           <li role="presentation" class="<?php if (isset($tab) && $tab = 'interview_process') {echo 'active';}?>">
                             <a href="#form_builder" aria-controls="form_builder" role="tab" data-toggle="tab" aria-controls="form_builder">
                             <i class="fa fa-calendar"></i>&nbsp;<?php echo _l('form_builder'); ?>
                             </a>
                          </li>
                       </ul>
                     </div>
                   </div>

                  <?php echo form_open_multipart(admin_url('recruitment/add_edit_recruitment_channel'), array('class' => 'recruitment-channel-add-edit', 'autocomplete' => 'off')); ?>

                   <div class="tab-content">
                    <?php if (isset($recruitment_channel_id)) {?>
                      <?php echo form_hidden('recruitment_channel_id', $recruitment_channel_id); ?>
                    <?php }?>
                    <!-- form_infomation start -->
                      <div role="tabpanel" class="tab-pane active" id="form_infomation">
                        <div class="row mt-5">
                           <div class="col-md-6">

                              <?php $r_form_name = (isset($form->r_form_name) ? $form->r_form_name : '');?>

                              <?php echo render_input('r_form_name', 'form_name', $r_form_name); ?>

                              <!-- form type -->
                              <?php $related = [
	0 => ['id' => '1', 'name' => _l('form')],
];?>
                              <?php $form_type_value = (isset($form->form_type) ? $form->form_type : '');?>
                              <?php echo render_select('form_type', $related, array('id', 'name'), 'form_type', $form_type_value); ?>

                              

                              <div class="form-group select-placeholder">
                                 <label for="language" class="control-label"><i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('form_lang_validation_help'); ?>"></i> <?php echo _l('form_lang_validation'); ?></label>
                                 <select name="language" id="language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value=""></option>
                                    <?php foreach ($languages as $availableLanguage) {
	?>
                                    <option value="<?php echo html_entity_decode($availableLanguage); ?>"<?php if ((isset($form->language) && $form->language == $availableLanguage) || (!isset($form) && get_option('active_language') == $availableLanguage)) {
		echo ' selected';
	}?>><?php echo html_entity_decode(ucfirst($availableLanguage)); ?></option>
                                    <?php }?>
                                 </select>
                              </div>

                              <?php $value = (isset($form->submit_btn_name) ? $form->submit_btn_name : 'Submit');?>
                              <?php echo render_input('submit_btn_name', 'form_btn_submit_text', $value); ?>

                              <?php $value = (isset($form->success_submit_msg) ? $form->success_submit_msg : '');?>
                              <?php echo render_textarea('success_submit_msg', 'form_success_submit_msg', $value); ?>

                           </div>
                           <div class="col-md-6">
                            <?php $lead_status = (isset($form->lead_status) ? $form->lead_status : '');?>
                              <?php

$status = ['1' => ['id' => '1', 'name' => _l('application')],
	'2' => ['id' => '2', 'name' => _l('potential')],
	'3' => ['id' => '3', 'name' => _l('interview')],
	'4' => ['id' => '4', 'name' => _l('won_interview')],
	'5' => ['id' => '5', 'name' => _l('send_offer')],
	'6' => ['id' => '6', 'name' => _l('elect')],
	'7' => ['id' => '7', 'name' => _l('non_elect')],
	'8' => ['id' => '8', 'name' => _l('unanswer')],
	'9' => ['id' => '9', 'name' => _l('transferred')],
	'11' => ['id' => '10', 'name' => _l('preliminary_selection')],
];

echo render_select('lead_status', $status, array('id', 'name'), 'status', $lead_status);

$selected = '';
foreach ($members as $staff) {
	if (isset($form->responsible) && $form->responsible == $staff['staffid']) {
		$selected = $staff['staffid'];
	}
}
?>
                              <?php echo render_select('responsible', $members, array('staffid', array('firstname', 'lastname')), 'notify_assigned_user', $selected); ?>

                              <hr />
                              <label for="" class="control-label"><?php echo _l('notification_settings'); ?></label>
                              <div class="clearfix"></div>
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" name="notify_lead_imported" id="notify_lead_imported" <?php
if (isset($form->notify_lead_imported) && $form->notify_lead_imported == 1 || !isset($form->notify_lead_imported)) {
	echo 'checked';
}?>>
                                 <label for="notify_lead_imported"><?php echo _l('notify_when_new_candidates'); ?></label>
                              </div>
                              <div class="select-notification-settings<?php if (isset($form) && $form->notify_lead_imported == '0') {
	echo ' hide';
}?>">
                              <hr />
                              <div class="radio radio-primary radio-inline">
                                 <input type="radio" name="notify_type" value="specific_staff" id="specific_staff" <?php if (isset($form->notify_type) && $form->notify_type == 'specific_staff' || !isset($form->notify_type)) {
	echo 'checked';
}?>>
                                 <label for="specific_staff"><?php echo _l('specific_staff_members'); ?></label>
                              </div>

                              <div class="radio radio-primary radio-inline">
                                 <input type="radio" name="notify_type" id="roles" value="roles" <?php if (isset($form->notify_type) && $form->notify_type == 'roles') {
	echo 'checked';
}?>>
                                 <label for="roles"><?php echo _l('staff_with_roles'); ?></label>
                              </div>

                              <div class="radio radio-primary radio-inline">
                                 <input type="radio" name="notify_type" id="assigned" value="assigned" <?php if (isset($form->notify_type) && $form->notify_type == 'assigned') {
	echo 'checked';
}?>>
                                 <label for="assigned"><?php echo _l('notify_assigned_user'); ?></label>
                              </div>

                              <div class="clearfix mtop15"></div>
                              <div id="staff_notify" class="<?php if (isset($form->notify_type) && ($form->notify_type != 'specific_staff')) {echo 'd-none';}?>">
                                <label><?php echo _l('person_in_charge'); ?></label>
                                <select name="notify_ids_staff[]" id="notify_ids_staff" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('not_required'); ?>" multiple data-live-search="true">
                                  <?php
$arrayselect = array();
if (isset($form->notify_ids_staff)) {
	$arrayselect = explode(',', $form->notify_ids_staff);
}
foreach ($members as $m) {
	$selected = '';
	if (in_array($m['staffid'], $arrayselect)) {
		$selected = 'selected';
	}
	$label = $m['firstname'];
	$value = (int) $m['staffid'];?>
                                    <option value="<?php echo html_entity_decode($value); ?>" <?php echo html_entity_decode($selected); ?>><?php echo html_entity_decode($label); ?></option>

                                <?php }?>
                                </select>
                              </div>
                              <div id="role_notify" class="<?php if (isset($form->notify_type) && ($form->notify_type != 'roles')) {echo 'd-none';} else {echo 'hide';}?>">
                                        <label><?php echo _l('leads_email_integration_notify_roles'); ?></label>
                                      <select name="notify_ids_roles[]" id="notify_ids_roles" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('not_required'); ?>" multiple data-live-search="true">

                                      <?php
$arrayselect = array();
if (isset($form->notify_ids_roles)) {
	$arrayselect = explode(',', $form->notify_ids_roles);
}
foreach ($roles as $m) {
	$selected = '';
	if (in_array($m['roleid'], $arrayselect)) {
		$selected = 'selected';
	}
	$label = $m['name'];
	$value = (int) $m['roleid'];?>
                                        <option value="<?php echo html_entity_decode($m['roleid']); ?>" <?php echo html_entity_decode($selected); ?>><?php echo html_entity_decode($m['name']); ?></option>

                                    <?php }?>
                                    </select>
                              </div>
                              </div>
                           </div>
                        </div>

                      </div>
                    <!-- form_infomation end -->

                    <!-- form_builder start -->
                     <div role="tabpanel" class="tab-pane <?php if (isset($tab) && $tab = 'form_builder') {echo 'active';}?> " id="form_builder">
                        <div id="form-build-wrap"></div>
                        <div id='my_formBuilder'></div>
                     </div>
                    <!-- form_builder end -->
                    <input type="hidden" name="form_data">


                    <?php if (has_permission('recruitment', '', 'create')) {?>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="modal-footer">
                              <button id="sm_btn2" type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                          </div>
                        </div>
                      </div>
                    <?php }?>

                  </div>
                  <?php echo form_close(); ?>
                </div>
              </div>
            </div><!-- /.modal-content -->

               </div>
            </div>
         </div>
      </div>
   </div>


</div>
<?php init_tail();?>
<script src="<?php echo base_url('assets/plugins/form-builder/form-builder.min.js'); ?>"></script>
<?php require 'modules/recruitment/assets/js/channel_detail_js.php';?>
<?php $this->load->view('admin/includes/_form_js_formatter');?>
</body>
</html>
