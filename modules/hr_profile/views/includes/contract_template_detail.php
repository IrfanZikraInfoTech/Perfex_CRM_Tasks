<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php 
				$template_id = '';

			 ?>
			<?php if(isset($contract_template)){
				$template_id = $contract_template->id;
			 ?>
				<div class="member">
					<?php echo form_hidden('isedit'); ?>
					<?php echo form_hidden('contractid',$contract_template->id); ?>
				</div>
			<?php } ?>
			
			<?php echo form_open_multipart(admin_url('hr_profile/contract_template/'.$template_id),array('class'=>'contract-template-form','autocomplete'=>'off')); ?>


			<div class="col-md-12" >
				<div class="panel_s">
					
					<div class="panel-body">

						<div class="row mb-5">
							<div class="col-md-12">
								<h4 class="no-margin"><?php echo html_entity_decode($title) ?></h4>
							</div>
						</div>

						<!-- start tab -->
						<div class="modal-body">

							<div class="tab-content">
								<!-- start general infor -->
								<div class="row">
								<h5 class="h5-color"><?php echo _l('general_info'); ?></h5>
								<hr class="hr-color">

									<?php 

									$name = (isset($contract_template) ? $contract_template->name : ''); 
									$value = (isset($contract_template) ? $contract_template->job_position : ''); 

									$arr_job_position = isset($contract_template) ? explode(",", $contract_template->job_position): [];

									?>

									<?php $attrs = (isset($contract_template) ? array() : array('autofocus'=>true)); ?>

									<div class="row">
									<div class="col-md-6">
										<?php echo render_input('name','contract_template',$name,'text',$attrs); ?>   
									</div>

									<div  class="col-md-6">
										<div class="form-group">
											<label><small class="req text-danger">* </small><?php echo _l('hr_hr_job_position'); ?></label>
											<select name="job_position[]" id="job_position" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
												<?php foreach($job_positions as $job_position) { 
													$selected = '';
													if(in_array($job_position['position_id'], $arr_job_position)){
														$selected = 'selected';
													}

													?>
													<option value="<?php echo html_entity_decode($job_position['position_id']); ?>" <?php echo html_entity_decode($selected); ?>><?php echo html_entity_decode($job_position['position_name']); ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									</div>

								</div>

								<div class="row">

									<h5 class="h5-color"><?php echo _l('contract_template'); ?></h5>
									<hr class="hr-color">

									<div class="row">
											<div class="col-md-12">
												<?php if(isset($contract_merge_fields)){ ?>
													<hr class="hr-panel-heading" />
													<p class="bold mtop10 text-right"><a href="#" onclick="slideToggle('.avilable_merge_fields'); return false;"><?php echo _l('available_merge_fields'); ?></a></p>
													<div class=" avilable_merge_fields mtop15 hide">
														<ul class="list-group">
															<?php
															foreach($contract_merge_fields as $field){
																foreach($field as $f){
																	echo '<li class="list-group-item"><b>'.$f['name'].'</b>  <a href="javascript:void(0)" class="pull-right" onclick="insert_merge_field(this); return false">'.$f['key'].'</a></li>';
																}
															}
															?>
														</ul>
													</div>
												<?php } ?>
											</div>
										</div>

										<hr class="hr-panel-heading" />
										<?php if(!has_permission('hrm_setting','','edit')) { ?>
											<div class="alert alert-warning contract-edit-permissions">
												<?php echo _l('contract_content_permission_edit_warning'); ?>
											</div>
										<?php } ?>
										<div class="tc-content<?php if(has_permission('hrm_setting','','edit')){echo ' editable';} ?>"
											style="border:1px solid #d2d2d2;min-height:70px; border-radius:4px;">
											<?php
											if((!isset($contract_template) || empty($contract_template->content) ) && has_permission('hrm_setting','','edit')){
												echo hooks()->apply_filters('new_contract_default_content', '<span class="text-danger text-uppercase mtop15 editor-add-content-notice"> ' . _l('click_to_add_content') . '</span>');
											} else {
												echo $contract_template->content;
											}
											?>
										</div>


								</div>

							</div>
						</div>

						<div class="modal-footer">
							<a href="<?php echo admin_url('hr_profile/setting?group=contract_template'); ?>"  class="btn btn-default mr-2 "><?php echo _l('hr_close'); ?></a>
							<?php if(has_permission('hrm_setting', '', 'create') || has_permission('hrm_setting', '', 'edit')){ ?>
								<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>

							<?php } ?>
						</div>

					</div>
				</div>
			</div>

			<?php echo form_close(); ?>
		</div>

	</div>
	<?php init_tail(); ?>
	<?php 
	require('modules/hr_profile/assets/js/setting/contract_template_js.php');
	?>

</body>
</html>
