<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" >
	<div class="content">
		<div class="row">

			<div class="col-md-8 col-md-offset-2">
				<div class="panel_s">
					<div class="panel-body">
						
						<?php echo form_open_multipart(admin_url('hr_profile/add_edit_member'), array('id' => 'add_edit_member')); ?>
						<div class="modal-body">
							<ul class="nav nav-tabs" role="tablist">
								<li role="presentation" class="active">
									<a href="#tab_staff_profile" aria-controls="tab_staff_profile" role="tab" data-toggle="tab">
										<?php echo _l('staff_profile_string'); ?>
									</a>
								</li>
								<li role="presentation">
									<a href="#tab_staff_contact" aria-controls="tab_staff_contact" role="tab" data-toggle="tab">
										<?php echo _l('hr_staff_profile_related_info'); ?>
									</a>
								</li>
								
							</ul>

							<div class="tab-content">
								<div role="tabpanel" class="tab-pane active" id="tab_staff_profile">

									<?php if(total_rows(db_prefix().'emailtemplates',array('slug'=>'two-factor-authentication','active'=>0)) == 0){ ?>
										<div class="checkbox checkbox-primary">
											<input type="checkbox" value="1" name="two_factor_auth_enabled" id="two_factor_auth_enabled"<?php if(isset($member) && $member->two_factor_auth_enabled == 1){echo ' checked';} ?>>
											<label for="two_factor_auth_enabled"><i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('two_factor_authentication_info'); ?>"></i>
												<?php echo _l('enable_two_factor_authentication'); ?></label>
											</div>
										<?php } ?>
										
									<?php
									if(isset($staff_cover_image) && $staff_cover_image != false){
										$link_cover_image = 'uploads/staff_profile_images/' . $member->staffid . '/thumb_'.$member->profile_image;
										$image_exist = file_exists(FCPATH .$link_cover_image); 
									}else{
										$image_exist = false;
									}
									?>
									<div class="col-md-12">  
										<div class="picture-container pull-left">
											<div class="picture pull-left">
												<img src="<?php if(isset($staff_cover_image) && isset($image_exist) && isset($link_cover_image)){ echo  base_url($link_cover_image); }else{  echo site_url(HR_PROFILE_PATH.'none_avatar.jpg'); } ?>" class="picture-src" id="wizardPicturePreview" title="">
												<input type="file" name="profile_image" class="form-control" id="profile_image" accept=".png, .jpg, .jpeg">
											</div>
										</div>
									</div>

									<div class="clearfix"></div>
									<br>
									<div class="clearfix"></div>

									<div class="row">
										<?php $value = (isset($member) ? $member->firstname : ''); ?>
										<?php $attrs = (isset($member) ? array() : array('autofocus'=>true)); ?>

										<div class="col-md-12">
											<?php  $hr_codes = (isset($member) ? $member->staff_identifi : $staff_code); ?>
											<div class="form-group" app-field-wrapper="staff_identifi">
												<label for="staff_identifi" class="control-label"><?php echo _l('hr_staff_code'); ?></label>
												<input type="text" id="staff_identifi" name="staff_identifi" class="form-control" value="<?php echo html_entity_decode($hr_codes) ?>" aria-invalid="false" <?php if(!is_admin() && !has_permission('hrm_hr_records','', 'edit') && !has_permission('hrm_hr_records','', 'create')){ echo 'disabled' ; }  ?>>
											</div>
										</div> 
										
									</div>

									<div class="row">
										<div class="col-md-6">
											<?php echo render_input('firstname','hr_firstname',$value,'text',$attrs); ?>
										</div>
										<div class="col-md-6">
											<?php echo render_input('lastname','hr_lastname',$value,'text',$attrs); ?>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="sex" class="control-label"><?php echo _l('hr_sex'); ?></label>
												<select name="sex" class="selectpicker" id="sex" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
													<option value=""></option>                  
													<option value="<?php echo 'male'; ?>" <?php if(isset($member) && $member->sex == 'male'){echo 'selected';} ?>><?php echo _l('male'); ?></option>
													<option value="<?php echo 'female'; ?>" <?php if(isset($member) && $member->sex == 'female'){echo 'selected';} ?>><?php echo _l('female'); ?></option>
												</select>
											</div>
										</div>

										<div class="col-md-6">
											<?php 
											$birthday = (isset($member) ? $member->birthday : ''); 
											echo render_date_input('birthday','hr_hr_birthday',_d($birthday)); ?>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<?php $value = (isset($member) ? $member->email : ''); ?>
											<div class="form-group" app-field-wrapper="email">
												<label for="email" class="control-label">Email</label>
												<input type="email" id="email" name="email" class="form-control" autocomplete="off" value="<?php echo html_entity_decode($value) ?>" <?php if(!is_admin() && !has_permission('hrm_hr_records','', 'edit') && !has_permission('hrm_hr_records','', 'create')){ echo 'disabled' ;}  ?>>
											</div>
										</div>

										<div class="col-md-6">
											<?php $value = (isset($member) ? $member->phonenumber : ''); ?>
											<?php echo render_input('phonenumber','staff_add_edit_phonenumber',$value); ?>
										</div>


									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="workplace" class="control-label"><?php echo _l('hr_hr_workplace'); ?></label>
												<select name="workplace" class="selectpicker" id="workplace" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
													<option value=""></option>                  
													<?php foreach($workplace as $w){ ?>

														<option value="<?php echo html_entity_decode($w['id']); ?>" <?php if(isset($member) && $member->workplace == $w['id']){echo 'selected';} ?>><?php echo html_entity_decode($w['name']); ?></option>

													<?php } ?>
												</select>
											</div>
										</div>

										<div class="col-md-6">
											<div class="form-group">
												<label for="status_work" class="control-label"><?php echo _l('hr_status_work'); ?></label>
												<select name="status_work" class="selectpicker" id="status_work" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
													<option value="<?php echo 'working'; ?>" <?php if(isset($member) && $member->status_work == 'working'){echo 'selected';} ?>><?php echo _l('hr_working'); ?></option>
													<option value="<?php echo 'maternity_leave'; ?>" <?php if(isset($member) && $member->status_work == 'maternity_leave'){echo 'selected';} ?>><?php echo _l('hr_maternity_leave'); ?></option>
													<option value="<?php echo 'inactivity'; ?>" <?php if(isset($member) && $member->status_work == 'inactivity'){echo 'selected';} ?>><?php echo _l('hr_inactivity'); ?></option>
												</select>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="job_position" class="control-label"><?php echo _l('hr_hr_job_position'); ?></label>
												<select name="job_position" class="selectpicker" id="job_position" data-width="100%"  data-action-box="true" data-hide-disabled="true" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
													<option value=""></option> 
													<?php foreach($positions as $p){ ?> 
														<option value="<?php echo html_entity_decode($p['position_id']); ?>" <?php if(isset($member) && $member->job_position == $p['position_id']){echo 'selected';} ?>><?php echo html_entity_decode($p['position_name']); ?></option>
													<?php } ?>
												</select>
											</div>
										</div>

										<div class="col-md-6">
											<!--teamanage -->
											<?php if(has_permission('hrm_hr_records','', 'edit') || has_permission('hrm_hr_records','', 'create')){ ?>
												<?php $value = (isset($member) ? $member->team_manage : ''); ?>
												<?php echo render_select('team_manage',$list_staff,array('staffid','full_name'),'hr_team_manage',$value); ?>
											<?php } ?>
											<!--teamanage -->
										</div>
									</div>  

									<?php if(is_admin() || has_permission('hrm_hr_records','', 'edit')){ ?>
										
										<?php
										hooks()->do_action('staff_render_permissions');
										$selected = '';
										foreach($roles_value as $role_value){
											if(isset($member)){
												if($member->role == $role_value['roleid']){
													$selected = $role_value['roleid'];
												}
											} else {
												$default_staff_role = get_option('default_staff_role');
												if($default_staff_role == $role_value['roleid'] ){
													$selected = $role_value['roleid'];
												}
											}
										}
										?>

										<div class="row">
											<div class="col-md-12">
												<?php echo render_select('role_v',$roles_value,array('roleid','name'),'staff_add_edit_role',$selected); ?>
											</div>
										</div>
									<?php } ?>

									<div class="row">
										<div class="col-md-6">
											<?php $literacy = (isset($member) ? $member->literacy : ''); ?> 
											<div class="form-group">
												<label for="literacy" class="control-label"><?php echo _l('hr_hr_literacy'); ?></label>
												<select name="literacy" id="literacy" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('hr_not_required'); ?>">
													<option value=""></option>
													<option value="primary_level" <?php if($literacy == 'primary_level'){ echo 'selected'; } ?> ><?php echo _l('hr_primary_level'); ?></option>
													<option value="intermediate_level" <?php if($literacy == 'intermediate_level'){ echo 'selected'; } ?> ><?php echo _l('hr_intermediate_level'); ?></option>
													<option value="college_level" <?php if($literacy == 'college_level'){ echo 'selected'; } ?> ><?php echo _l('hr_college_level'); ?></option>
													<option value="masters" <?php if($literacy == 'masters'){ echo 'selected'; } ?> ><?php echo _l('hr_masters'); ?></option>
													<option value="doctor" <?php if($literacy == 'doctor'){ echo 'selected'; } ?> ><?php echo _l('hr_Doctor'); ?></option>
													<option value="bachelor" <?php if($literacy == 'bachelor'){ echo 'selected'; } ?> ><?php echo _l('hr_bachelor'); ?></option>
													<option value="engineer" <?php if($literacy == 'engineer'){ echo 'selected'; } ?> ><?php echo _l('hr_Engineer'); ?></option>
													<option value="university" <?php if($literacy == 'university'){ echo 'selected'; } ?> ><?php echo _l('hr_university'); ?></option>
													<option value="intermediate_vocational" <?php if($literacy == 'intermediate_vocational'){ echo 'selected'; } ?> ><?php echo _l('hr_intermediate_vocational'); ?></option>
													<option value="college_vocational" <?php if($literacy == 'college_vocational'){ echo 'selected'; } ?> ><?php echo _l('hr_college_vocational'); ?></option>
													<option value="in-service" <?php if($literacy == 'in-service'){ echo 'selected'; } ?> ><?php echo _l('hr_in-service'); ?></option>
													<option value="high_school" <?php if($literacy == 'high_school'){ echo 'selected'; } ?> ><?php echo _l('hr_high_school'); ?></option>
													<option value="intermediate_level_pro" <?php if($literacy == 'intermediate_level_pro'){ echo 'selected'; } ?> ><?php echo _l('hr_intermediate_level_pro'); ?></option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="hourly_rate"><?php echo _l('staff_hourly_rate'); ?></label>
												<div class="input-group">
													<input type="number" name="hourly_rate" value="<?php if(isset($member)){echo html_entity_decode($member->hourly_rate);} else {echo 0;} ?>" id="hourly_rate" class="form-control">
													<span class="input-group-addon">
														<?php echo html_entity_decode($base_currency->symbol); ?>
													</span>
												</div>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6">
											<?php if(get_option('disable_language') == 0){ ?>
												<div class="form-group">
													<label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?></label>
													<select name="default_language" data-live-search="true" id="default_language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
														<option value=""><?php echo _l('system_default_string'); ?></option>
														<?php foreach($this->app->get_available_languages() as $availableLanguage){
															$selected = '';
															if(isset($member)){
																if($member->default_language == $availableLanguage){
																	$selected = 'selected';
																}
															}
															?>
															<option value="<?php echo html_entity_decode($availableLanguage); ?>" <?php echo html_entity_decode($selected); ?>><?php echo ucfirst($availableLanguage); ?></option>
														<?php } ?>
													</select>
												</div>
											<?php } ?>
										</div>

										<div class="col-md-6">
											<div class="form-group">
												<label for="direction"><?php echo _l('document_direction'); ?></label>
												<select class="selectpicker" data-none-selected-text="<?php echo _l('system_default_string'); ?>" data-width="100%" name="direction" id="direction">
													<option value="" <?php if(isset($member) && empty($member->direction)){echo 'selected';} ?>></option>
													<option value="ltr" <?php if(isset($member) && $member->direction == 'ltr'){echo 'selected';} ?>>LTR</option>
													<option value="rtl" <?php if(isset($member) && $member->direction == 'rtl'){echo 'selected';} ?>>RTL</option>
												</select>
											</div>
										</div>
									</div>


									<?php if(is_admin() || has_permission('hrm_hr_records','', 'edit')){ ?>
										<div class="form-group">
											<div class="row">
												<div class="col-md-6">
													<i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('staff_email_signature_help'); ?>"></i>
													<?php $value = (isset($member) ? $member->email_signature : ''); ?>
													<?php echo render_textarea('email_signature','settings_email_signature',$value, ['data-entities-encode'=>'true']); ?>
												</div>
												<div class="col-md-6">
													<?php
													$orther_infor = (isset($member) ? $member->orther_infor : '');
													echo render_textarea('orther_infor','hr_orther_infor',$orther_infor); ?>
												</div>
											</div>

											<br>
											<?php if(count($departments) > 0){ ?>
												<label for="departments"><?php echo _l('staff_add_edit_departments'); ?></label>
											<?php } ?>

											<?php foreach($departments as $department){ ?>
												<div class="checkbox checkbox-primary">
													<?php
													$checked = '';
													if(isset($member)){
														foreach ($staff_departments as $staff_department) {
															if($staff_department['departmentid'] == $department['departmentid']){
																$checked = ' checked';
															}
														}
													}
													?>
													<input type="checkbox" id="dep_<?php echo html_entity_decode($department['departmentid']); ?>" name="departments[]" value="<?php echo html_entity_decode($department['departmentid']); ?>"<?php echo html_entity_decode($checked); ?>>
													<label for="dep_<?php echo html_entity_decode($department['departmentid']); ?>"><?php echo html_entity_decode($department['name']); ?></label>
												</div>
											<?php } ?>
										</div>
									<?php } ?>

									<?php $rel_id = (isset($member) ? $member->staffid : false); ?>
									<?php echo render_custom_fields('staff',$rel_id); ?>

									<div class="row">
										<div class="col-md-12">
											<hr class="hr-10" />
											

											<?php if(!isset($member) && total_rows(db_prefix().'emailtemplates',array('slug'=>'new-staff-created','active'=>0)) === 0){ ?>
												<div class="checkbox checkbox-primary">
													<input type="checkbox" name="send_welcome_email" id="send_welcome_email" checked>
													<label for="send_welcome_email"><?php echo _l('staff_send_welcome_email'); ?></label>
												</div>
											<?php } ?>
										</div>
									</div>

									<?php if(!isset($member) || is_admin() || !is_admin() && $member->admin == 0) { ?>
										<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
										<input  type="text" class="fake-autofill-field" name="fakeusernameremembered" value='' tabindex="-1"/>
										<input  type="password" class="fake-autofill-field" name="fakepasswordremembered" value='' tabindex="-1"/>
										<div class="clearfix form-group"></div>
										<label for="password" class="control-label"><?php echo _l('staff_add_edit_password'); ?></label>
										<div class="input-group">
											<input type="password" class="form-control password" name="password" autocomplete="off">
											<span class="input-group-addon">
												<a href="#password" class="show_password" onclick="showPassword('password'); return false;"><i class="fa fa-eye"></i></a>
											</span>
											<span class="input-group-addon">
												<a href="#" class="generate_password" onclick="generatePassword(this);return false;"><i class="fa fa-refresh"></i></a>
											</span>
										</div>
										<?php if(isset($member)){ ?>
											<p class="text-muted"><?php echo _l('staff_add_edit_password_note'); ?></p>
											<?php if($member->last_password_change != NULL){ ?>
												<?php echo _l('staff_add_edit_password_last_changed'); ?>:
												<span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($member->last_password_change); ?>">
													<?php echo time_ago($member->last_password_change); ?>
												</span>
											<?php } } ?>
										<?php } ?>

									</div>

									<div role="tabpanel" class="tab-pane " id="tab_staff_contact">

										<div class="row">
											<div class="col-md-6">
												<?php 
												$home_town = (isset($member) ? $member->home_town : '');
												echo render_input('home_town','hr_hr_home_town',$home_town,'text'); ?> 
											</div>

											<div class="col-md-6">
												<div class="form-group">
													<label for="marital_status" class="control-label"><?php echo _l('hr_hr_marital_status'); ?></label>
													<select name="marital_status" class="selectpicker" id="marital_status" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
														<option value=""></option>                  
														<option value="<?php echo 'single'; ?>" <?php if(isset($member) && $member->marital_status == 'single'){echo 'selected';} ?>><?php echo _l('hr_single'); ?></option>
														<option value="<?php echo 'married'; ?>" <?php if(isset($member) && $member->marital_status == 'married'){echo 'selected';} ?>><?php echo _l('married'); ?></option>
													</select>
												</div>
											</div>

											
										</div>

										<div class="row">
											<div class="col-md-6">
												<?php 
												$current_address = (isset($member) ? $member->current_address : '');
												echo render_input('current_address','hr_current_address',$current_address,'text'); ?>
											</div>
											<div class="col-md-6">
												<?php
												$nation = (isset($member) ? $member->nation : '');
												echo render_input('nation','hr_hr_nation',$nation,'text'); ?>
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
												<?php
												$birthplace = (isset($member) ? $member->birthplace : '');
												echo render_input('birthplace','hr_hr_birthplace',$birthplace,'text'); ?> 
											</div>
											<div class="col-md-6">
												<?php 
												$religion = (isset($member) ? $member->religion : '');
												echo render_input('religion','hr_hr_religion',$religion,'text'); ?>
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
												<?php 
												$identification = (isset($member) ? $member->identification : '');
												echo render_input('identification','hr_citizen_identification',$identification,'text'); ?>
											</div>
											<div class="col-md-6">
												<?php
												$days_for_identity = (isset($member) ? $member->days_for_identity : '');
												echo render_date_input('days_for_identity','hr_license_date',_d($days_for_identity)); ?>
											</div>
										</div> 

										<div class="row">
											<div class="col-md-6">
												<?php
												$place_of_issue = (isset($member) ? $member->place_of_issue : '');
												echo render_input('place_of_issue','hr_hr_place_of_issue',$place_of_issue, 'text'); ?>
											</div>
											<div class="col-md-6">
												<?php 
												$resident = (isset($member) ? $member->resident : '');
												echo render_input('resident','hr_hr_resident',$resident,'text'); ?>
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
												<?php
												$account_number = (isset($member) ? $member->account_number : '');
												echo render_input('account_number','hr_bank_account_number',$account_number, 'text'); ?>
											</div>
											<div class="col-md-6">
												<?php
												$name_account = (isset($member) ? $member->name_account : '');
												echo render_input('name_account','hr_bank_account_name',$name_account, 'text'); ?>
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
												<?php
												$issue_bank = (isset($member) ? $member->issue_bank : '');
												echo render_input('issue_bank','hr_bank_name',$issue_bank, 'text'); ?>
											</div>
											<div class="col-md-6">
												<?php
												$Personal_tax_code = (isset($member) ? $member->Personal_tax_code : '');
												echo render_input('Personal_tax_code','hr_Personal_tax_code',$Personal_tax_code, 'text'); ?>
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="facebook" class="control-label"><i class="fa fa-facebook"></i> <?php echo _l('staff_add_edit_facebook'); ?></label>
													<input type="text" class="form-control" name="facebook" value="<?php if(isset($member)){echo html_entity_decode($member->facebook);} ?>">
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="linkedin" class="control-label"><i class="fa fa-linkedin"></i> <?php echo _l('staff_add_edit_linkedin'); ?></label>
													<input type="text" class="form-control" name="linkedin" value="<?php if(isset($member)){echo html_entity_decode($member->linkedin);} ?>">
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="skype" class="control-label"><i class="fa fa-skype"></i> <?php echo _l('staff_add_edit_skype'); ?></label>
													<input type="text" class="form-control" name="skype" value="<?php if(isset($member)){echo html_entity_decode($member->skype);} ?>">
												</div>
											</div>

										</div>

									</div>

									<div role="tabpanel" class="tab-pane hide" id="staff_permissions">
										<div class="table-responsive">

											

											<table class="table table-bordered roles no-margin">
												<thead>
													<tr>
														<th>Feature</th>
														<th>Capabilities</th>
													</tr>
												</thead>
												<tbody>
													<?php
													if(isset($member)){
														$is_admin = is_admin($member->staffid);
													}

													foreach(get_available_staff_permissions($funcData) as $feature => $permission) { ?>
														<tr data-name="<?php echo html_entity_decode($feature); ?>" >
															<td>
																<b><?php echo html_entity_decode($permission['name']); ?></b>
															</td>
															<td>
																<?php
																if(isset($permission['before'])){
																	echo html_entity_decode($permission['before']);
																}
																?>
																<?php foreach ($permission['capabilities'] as $capability => $name) {
																	$checked = '';
																	$disabled = '';
																	if((isset($is_admin) && $is_admin) ||
																		(is_array($name) && isset($name['not_applicable']) && $name['not_applicable']) ||
																		(
																			($capability == 'view_own' || $capability == 'view'
																				&& array_key_exists('view_own', $permission['capabilities']) && array_key_exists('view', $permission['capabilities']))
																			&&
																			((isset($member)
																				&& staff_can(($capability == 'view' ? 'view_own' : 'view'), $feature, $member->staffid))
																			||
																			(isset($role)
																				&& has_role_permission($role->roleid, ($capability == 'view' ? 'view_own' : 'view'), $feature))
																		)
																		)
																	){
																		$disabled = ' disabled ';
																} else if((isset($member) && staff_can($capability, $feature, $member->staffid))
																	|| isset($role) && has_role_permission($role->roleid, $capability, $feature)){
																	$checked = ' checked ';
																}
																?>
																<div class="checkbox">
																	<input
																	<?php if($capability == 'view') { ?> data-can-view <?php } ?>
																	<?php if($capability == 'view_own') { ?> data-can-view-own <?php } ?>
																	<?php if(is_array($name) && isset($name['not_applicable']) && $name['not_applicable']){ ?> data-not-applicable="true" <?php } ?>
																	type="checkbox"
																	<?php echo html_entity_decode($checked);?>
																	class="capability"
																	id="<?php echo html_entity_decode($feature .'_'.$capability); ?>"
																	name="permissions[<?php echo html_entity_decode($feature); ?>][]"
																	value="<?php echo html_entity_decode($capability); ?>"
																	<?php echo html_entity_decode($disabled); ?>>
																	<label for="<?php echo html_entity_decode($feature .'_'.$capability); ?>">
																		<?php echo !is_array($name) ? $name : $name['name']; ?>
																	</label>
																	<?php
																	if(isset($permission['help']) && array_key_exists($capability, $permission['help'])) {
																		echo '<i class="fa fa-question-circle" data-toggle="tooltip" data-title="'.$permission['help'][$capability].'"></i>';
																	}
																	?>
																</div>
															<?php } ?>
															<?php
															if(isset($permission['after'])){
																echo html_entity_decode($permission['after']);
															}
															?>
														</td>
													</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>



							</div>

							<div class="modal-footer">
								<a href="<?php echo admin_url('hr_profile/staff_infor'); ?>"  class="btn btn-default mr-2 "><?php echo _l('hr_close'); ?></a>
								<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
							</div>
							<?php echo form_close(); ?>
						</div>
					</div>
				</div>
			</div>


		</div>
		<div class="btn-bottom-pusher"></div>
	</div>
	<?php init_tail(); ?>
	<?php 
	require('modules/hr_profile/assets/js/hr_record/add_update_staff_js.php');
	require('modules/hr_profile/assets/js/hr_record/add_staff_js.php');
	?>
</body>
</html>