<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php if(isset($contracts)){ ?>
				<div class="member">
					<?php echo form_hidden('isedit'); ?>
					<?php echo form_hidden('contractid',$contracts->id_contract); ?>
				</div>
			<?php } ?>
			<?php echo form_open_multipart($this->uri->uri_string(),array('class'=>'staff-contract-form','autocomplete'=>'off')); ?>

			<div class="col-md-12" >
				<div class="panel_s">
					
					<div class="panel-body">
						<div class="row mb-5">
							<div class="col-md-12">
								<?php if(isset($contracts)){ ?>
									<h4 class="no-margin"><?php echo _l('hr_edit_contract') ?> 
								</h4>
							<?php }else{?>
								<h4 class="no-margin"><?php echo _l('new_contract') ?> 
							</h4>
						<?php } ?>
					</div>
				</div>

				<!-- start tab -->
				<div class="modal-body">

					<div class="tab-content">
						<!-- start general infor -->
						<h5 class="h5-color"><?php echo _l('general_info'); ?></h5>
						<hr class="hr-color">

						<div class="row">
							<?php $value = (isset($contracts) ? $contracts->name_contract : ''); ?>
							<?php $attrs = (isset($contracts) ? array() : array('autofocus'=>true)); ?>
							<div class="col-md-6">

								<?php 
								$contract_code = (isset($contracts) ? $contracts->contract_code : $staff_contract_code);
								echo render_input('contract_code','hr_contract_code',$contract_code,'text',$attrs); ?>   
							</div>
							<div class="col-md-6">
								<label for="staff" class="control-label"><?php echo _l('hr_hr_staff_name'); ?></label>
								<select name="staff" class="selectpicker" id="staff" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true"> 
									<option value=""></option>                  
									<?php foreach($staff as $s){ ?>
										<option value="<?php echo html_entity_decode($s['staffid']); ?>"  <?php if(isset($contracts) && $contracts->staff == $s['staffid'] ){echo 'selected';} ?>> <?php echo html_entity_decode($s['firstname'].''.$s['lastname']); ?></option>                  
									<?php }?>
								</select>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="name_contract" class="control-label"><?php echo _l('hr_name_contract'); ?></label>
									<select name="name_contract" class="selectpicker" id="name_contract" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
										<option value=""></option>                  
										<?php foreach($contract_type as $c){ ?>
											<option value="<?php echo html_entity_decode($c['id_contracttype']); ?>" <?php if(isset($contracts) && $contracts->name_contract == $c['id_contracttype'] ){echo 'selected';} ?>><?php echo html_entity_decode($c['name_contracttype']); ?> </option>
										<?php }?>
									</select>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label for="contract_status" class="control-label"><?php echo _l('hr_status_label'); ?></label>
									<select name="contract_status" class="selectpicker" id="contract_status" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
										<option value="draft" <?php if(isset($contracts) && $contracts->contract_status == 'draft' ){echo 'selected';} ?> ><?php echo _l('hr_hr_draft') ?></option>
										<option value="valid" <?php if(isset($contracts) && $contracts->contract_status == 'valid' ){echo 'selected';} ?>><?php echo _l('hr_hr_valid') ?></option>
										<option value="invalid" <?php if(isset($contracts) && $contracts->contract_status == 'invalid' ){echo 'selected';} ?>><?php echo _l('hr_hr_expired') ?></option>
										<option value="finish" <?php if(isset($contracts) && $contracts->contract_status == 'finish' ){echo 'selected';} ?>><?php echo _l('hr_hr_finish') ?></option>
									</select>
								</div>
							</div>

						</div>

						<div class="row">
							<div class="col-md-6">
								<?php
								$start_valid = (isset($contracts) ? $contracts->start_valid : date('Y-m-d'));
								echo render_date_input('start_valid','hr_start_month',_d($start_valid)); ?>
							</div>
							<div class="col-md-6">
								<?php
								$end_valid = (isset($contracts) ? $contracts->end_valid : '');
								echo render_date_input('end_valid','hr_end_month',_d($end_valid)); ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6"></div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="hourly_or_month" class="control-label"><?php echo _l('hr_hourly_rate_month'); ?></label>
									<select name="hourly_or_month" class="selectpicker" id="hourly_or_month" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
										<option value="month" <?php if(isset($contracts) && $contracts->hourly_or_month == 'month' ){echo 'selected';} ?>><?php echo _l('hr_month') ?></option>
										<option value="hourly_rate" <?php if(isset($contracts) && $contracts->hourly_or_month == 'hourly_rate' ){echo 'selected';} ?> ><?php echo _l('hourly_rate') ?></option>
										
									</select>
							</div>
						</div>

						<h5 class="h5-color"><?php echo _l('hr_wages_allowances'); ?></h5>
						<hr class="hr-color">

						<!-- end genral infor -->


						<div class="form"> 
							<div id="staff_contract_hs" class="hot handsontable htColumnHeaders">
							</div>
							<?php echo form_hidden('staff_contract_hs'); ?>
						</div>

						<br>
						<h5 class="h5-color"><?php echo _l('hr_signed_information'); ?></h5>
						<hr class="hr-color">

						<div class="row">
							<div class="col-md-6">
								<?php
								$sign_day = (isset($contracts) ? $contracts->sign_day : date('Y-m-d'));
								echo render_date_input('sign_day','hr_sign_day',_d($sign_day)); ?>

							</div>
							<div class="col-md-6">
								<label for="staff_delegate" class="control-label"><?php echo _l('hr_staff_delegate'); ?></label>
								<select name="staff_delegate" class="selectpicker" data-live-search="true" id="staff_delegate" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
									<option value=""></option>                  
									<?php foreach($staff as $s){ ?>
										<option value="<?php echo html_entity_decode($s['staffid']); ?>"  <?php if(isset($contracts) && $contracts->staff_delegate == $s['staffid'] ){echo 'selected';} ?>> <?php echo html_entity_decode($s['firstname'].''.$s['lastname']); ?></option>                  
									<?php }?>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class=" attachments">
									<div class="attachment">
										<div class="form-group">
											<label for="attachment" class="control-label"><?php echo _l('add_task_attachments'); ?></label>
											<div class="input-group">
												<input type="file" extension="<?php echo str_replace('.','',get_option('allowed_files')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="file[0]">
												<span class="input-group-btn">
													<button class="btn btn-success add_more_attachments_file p8" type="button"><i class="fa fa-plus"></i></button>
												</span>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
						<div class="row">

							<div class="col-md-6">
								<div class="row hide">
									<div class="col-md-12">
										<?php 
										$staff_role = (isset($staff_delegate_role) ? $staff_delegate_role->name : '');
										echo render_input('job_position','hr_hr_job_position',$staff_role,'text',$attrs); ?> 
									</div>
								</div>
								<div class="row">
									<div id="contract_attachments" class="mtop30 ">
										<?php if(isset($contract_attachment)){ ?>

											<?php
											$data = '<div class="row" id="attachment_file">';
											foreach($contract_attachment as $attachment) {
												$href_url = site_url('modules/hr_profile/uploads/contracts/'.$attachment['rel_id'].'/'.$attachment['file_name']).'" download';
												if(!empty($attachment['external'])){
													$href_url = $attachment['external_link'];
												}
												$data .= '<div class="display-block contract-attachment-wrapper">';
												$data .= '<div class="col-md-10">';
												$data .= '<div class="col-md-1 mr-5">';
												$data .= '<a name="preview-btn" onclick="preview_file_staff(this); return false;" rel_id = "'.$attachment['rel_id'].'" id = "'.$attachment['id'].'" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="'._l("preview_file").'">';
												$data .= '<i class="fa fa-eye"></i>'; 
												$data .= '</a>';
												$data .= '</div>';
												$data .= '<div class=col-md-9>';
												$data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
												$data .= '<a href="'.$href_url.'>'.$attachment['file_name'].'</a>';
												$data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
												$data .= '</div>';
												$data .= '</div>';
												$data .= '<div class="col-md-2 text-right">';
												if(is_admin() || has_permission('hrm_contract', '', 'delete')){
													$data .= '<a href="#" class="text-danger" onclick="delete_contract_attachment(this,'.$attachment['id'].'); return false;"><i class="fa fa fa-times"></i></a>';
												}
												$data .= '</div>';
												$data .= '<div class="clearfix"></div><hr/>';
												$data .= '</div>';
											}
											$data .= '</div>';
											echo html_entity_decode($data);
											?>
										<?php } ?>
										<!-- check if edit contract => display attachment file end-->

									</div>

									<div id="contract_file_data"></div>
								</div>

							</div>
							

						</div>

					</div>

				</div>

				<div class="modal-footer">
					<a href="<?php echo admin_url('hr_profile/contracts'); ?>"  class="btn btn-default mr-2 "><?php echo _l('hr_close'); ?></a>
					<?php if(has_permission('hrm_contract', '', 'create') || has_permission('hrm_contract', '', 'edit')){ ?>
						<a href="#"class="btn btn-info pull-right mright10 display-block add_goods_receipt" ><?php echo _l('submit'); ?></a>
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
require('modules/hr_profile/assets/js/contracts/contract_js.php');
?>
</body>
</html>
