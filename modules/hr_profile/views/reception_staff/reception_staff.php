<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">                       
						<div class="row">

							<div class="col-md-3 pull-left">
								<?php if(is_admin() || has_permission('hrm_reception_staff','','create')){ ?>
									<button type="button" class="btn btn-info" onclick="new_reception();"><?php echo _l('hr_add_reception'); ?></button>
								<?php } ?>

							</div>
						</div>
						<div class="clearfix"></div>
						<br>

						<div class="modal bulk_actions" id="table_staff_bulk_actions" tabindex="-1" role="dialog">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title"><?php echo _l('hr_bulk_actions'); ?></h4>
									</div>
									<div class="modal-body">
										<?php if(has_permission('hrm_reception_staff','','delete') || is_admin()){ ?>
											<div class="checkbox checkbox-danger">
												<input type="checkbox" name="mass_delete" id="mass_delete">
												<label for="mass_delete"><?php echo _l('hr_mass_delete'); ?></label>
											</div>
										<?php } ?>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>

										<?php if(has_permission('hrm_reception_staff','','delete') || is_admin()){ ?>
											<a href="#" class="btn btn-info" onclick="staff_delete_bulk_action(this); return false;"><?php echo _l('hr_confirm'); ?></a>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>

						<?php if (has_permission('hrm_reception_staff','','delete')) { ?>
							<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_staff" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('hr_bulk_actions'); ?></a>
						<?php } ?>

						<?php
						$table_data = array(
							'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_staff"><label></label></div>',
							_l('staff_id'),
							_l('staff_dt_name'),
							_l('hr_hr_code'),
							_l('hr_hr_birthday'),
							_l('hr_hr_finish'));
						render_datatable($table_data,'table_staff',
							array('customizable-table'),
							array(
								'id'=>'table-table_staff',
								'data-last-order-identifier'=>'table_staff',
								'data-default-order'=>get_table_last_order('table_staff'),
							));
						?>
						<div class="clearfix"></div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="add_reception_staff" tabindex="-1" role="dialog">
	<div class="modal-dialog w-50">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">
					<span class="add-title"><?php echo _l('hr_add_reception'); ?></span>
					<span class="edit-title hide"><?php echo _l('hr_edit_reception'); ?></span>
				</h4>
			</div>
			<div class="modal-body p-0">

			<?php echo form_open(admin_url('hr_profile/add_new_reception'),  array('id'=>'add_new_reception')); ?>

				<div class="row">
					<div class="col-md-12">
						<label for="staff_id" class="control-label"><small class="req text-danger">* </small><?php echo _l('hr_select_employee'); ?></label>
						<select name="staff_id" data-live-search="true" class="selectpicker" id="staff_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required> 
							<option value=""></option> 
							<?php foreach ($list_staff_not_record as $e){ ?>
								<option value="<?php echo html_entity_decode($e['staffid']) ?>"><?php echo html_entity_decode($e['firstname'].' '.$e['lastname']); ?></option>
							<?php } ?>
						</select>
					</div>



					<?php if(count($group_checklist)>0){ ?>
						<div class="col-md-12">
							<br>
							<h4 class="text-primary"><i class="fa fa-info-circle"></i> <?php echo _l('hr_reception_information'); ?></h4>
							<hr>
							<div class="col-md-12">
								<div class="col-md-12" id="manage_reception">

									<?php 

									if (isset($group_checklist)) {
										foreach ($group_checklist as $key => $value) {?>                 
											<div class="row title">                           
												<div class="row">
													<div class="col-md-10">
														<div class="form-group">
															<input type="text" name="title_name[<?php echo html_entity_decode($key); ?>]" class="form-control" placeholder="<?php echo _l('hr_title'); ?>" value="<?php echo html_entity_decode($value['group_name']); ?>" required>
														</div>
													</div>
													<div class="col-md-2" name="button_add">
														<?php 
														if($key == 0){ ?>
															<button onclick="add_title(this); return false;" class="btn btn-primary mt-1 btn-title" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
														<?php }else{?>
															<button onclick="remove_title(this); return false;" class="btn btn-danger mt-1 btn-title" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
														<?php } ?>
													</div>
												</div>
												<?php 
												$checklist = $this->hr_profile_model->checklist_by_group($value['id']);

												foreach ($checklist as $ind => $sub_item) {?>
													<div class="sub"> 
														<div class="row">                          
															<div class="col-md-9">
																<div class="form-group">
																	<input type="text" name="sub_title_name[<?php echo html_entity_decode($key); ?>][<?php echo html_entity_decode($ind); ?>]" data-count="<?php echo html_entity_decode($key); ?>" class="form-control" value="<?php echo html_entity_decode($sub_item['name']); ?>" placeholder="<?php echo _l('hr_sub_title'); ?>" required>
																</div>
															</div>
															<div class="col-md-3" name="button_add">
																<?php 
																if($ind == 0){ ?>
																	<button onclick="add_subtitle(this); return false;" class="btn btn-primary btn-sub-title" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
																<?php }else{?>
																	<button onclick="remove_subtitle(this); return false;" class="btn btn-danger btn-sub-title" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
																<?php } ?>
															</div>
														</div>
													</div>
												<?php  } ?>                   
												<div class="col-md-12 pl-0 sub_title"></div>
											</div>
											<?php 
										}}else{?>            
											<div class="row title">                           
												<div class="col-md-11 pt-2">
													<div class="form-group">
														<input type="text" name="title_name[0]" class="form-control" placeholder="Tiêu đề mục" value="">
													</div>
												</div>
												<div class="col-md-1 pl-0 pt-0" name="button_add">
													<button onclick="add_title(this); return false;" class="btn btn-primary mt-1" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
												</div>

												<div class="col-md-12 pl-0">
													<div class="sub">                           
														<div class="col-md-10 pt-2">
															<div class="form-group">
																<input type="text" name="sub_title_name[0][0]" data-count="0" class="form-control" value="" placeholder="Mục con">
															</div>
														</div>
														<div class="col-md-2 pl-0 pt-0" name="button_add">
															<button onclick="add_subtitle(this); return false;" class="btn btn-primary mt-1" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
														</div>
													</div>
												</div>
												<div class="col-md-12 pl-0 sub_title"></div>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						<?php } ?>
						<?php if(count($list_reception_staff_asset)>0){ ?>
							<div class="col-md-12 mt-1">
								<br>
								<h4 class="text-primary"><i class="fa fa-star"></i> <?php echo _l('hr_property_allocation'); ?></h4>
								<hr>
								<div class="col-md-12">
									<!--  Add assets    -->
									<div class="col-md-12 assets_wrap">
										<?php if($list_reception_staff_asset){
											foreach ($list_reception_staff_asset as $p_key => $p_value) {              
												?>
												<div id ="assets_emp" class="row">                            
													<div class="col-md-11 pt-2">
														<div class="form-group">
															<?php
															$name=$p_value['name'];

															?>
															<input type="text" name="asset_name[]" class="form-control" value="<?php echo html_entity_decode($name); ?>" placeholder="<?php echo _l('hr_enter_property_name'); ?>" required>
														</div>
													</div>                            
													<div class="col-md-1 pl-0 pt-0" name="button_add">
														<button name="add_asset" class="btn mt-1 <?php if($p_key == 0){ echo 'new_assets_emp btn-primary' ;}else{echo 'remove_assets_emp btn-danger' ;} ?>" data-ticket="true" type="button"><i class="fa <?php if($p_key == 0){ echo 'fa-plus' ;}else{ echo 'fa-minus' ;} ?> "></i></button>
													</div>
												</div>
											<?php } ?>
										<?php }else{ ?>
											<div id ="assets_emp" class="row">                           
												<div class="col-md-11 pt-2">
													<div class="form-group">                
														<input type="text" name="asset_name[]" class="form-control" value="" placeholder="<?php echo _l('hr_enter_property_name'); ?>" required>
													</div>
												</div>
												<div class="col-md-1 pl-0 pt-0" name="button_add">
													<button name="add_asset" class="btn new_assets_emp btn-primary mt-1" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
												</div>
											</div>
										<?php } ?>
									</div>
									<!--  End add asset    -->
								</div>
							</div>
						<?php } ?>
						<?php if(isset($setting_training)>0){ ?>
							<div class="col-md-12">
								<br>
								<h4 class="text-primary "><i class="fa fa-graduation-cap"></i> <?php echo _l('hr_training'); ?></h4>
								<hr >
								<div class="col-md-12">      
									<div class="row">

										<div class="col-md-12">
										<div class="form-group">
											<label><?php echo _l('type_of_training'); ?></label>

											<select name="training_type" class="selectpicker" id="training_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
												<option value=""></option> 
												<?php foreach ($type_of_trainings as $key => $value) { ?>
													<option value="<?php echo $value['id'] ?>" <?php if(isset($setting_training) && $setting_training->training_type == $value['id']  ){echo 'selected';}; ?> ><?php echo $value['name']  ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									</div>
								</div>
							</div>
						<?php } ?>

						<div class="col-md-12">
							<div class="col-md-12">      
								<div class="row">
									<div class="col-md-12 ">
										<div class="form-group">
											<label><small class="req text-danger">* </small><?php echo _l('hr_training_program'); ?></label>
											<select name="training_program" class="selectpicker" id="training_program" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required> 
												
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>
					<div class="clearfix"></div>
					<hr>
					<div class="row d-flex justify-content-end m-0 p-0 pb-4">
						<div class="col-md-12 text-right">
							<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
							<button class="btn btn-info"><?php echo _l('submit'); ?></button>
						</div>
					</div>
					<?php echo form_close(); ?>
				</div>   
			</div>
		</div>
	</div>
	<div class="modal fade" id="reception_sidebar" tabindex="-1" role="dialog">
		<div class="modal-dialog new_job_positions_dialog">
			<div class="modal-content">

			</div>
		</div>
	</div>

	<?php init_tail(); ?>
	<?php require('modules/hr_profile/assets/js/reception_staff/reception_staff_manage_js.php');
	?>
</body>
</html>
