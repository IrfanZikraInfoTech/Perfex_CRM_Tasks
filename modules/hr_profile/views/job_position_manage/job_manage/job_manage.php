<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php if($this->session->flashdata('debug')){ ?>
				<div class="col-lg-12">
					<div class="alert alert-warning">
						<?php echo html_entity_decode($this->session->flashdata('debug')); ?>
					</div>
				</div>
			<?php } ?>

			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div>
							<div class="row">
								<div class="_buttons col-md-12">
									<?php if(is_admin() || has_permission('staffmanage_job_position','','create')) { ?>
										<a href="#" onclick="new_job_p(); return false;" class="btn mright5 btn-info pull-left display-block">
											<?php echo _l('hr_new_job_p'); ?>
										</a>
									<?php } ?>
										<a href="<?php echo admin_url('hr_profile/job_positions'); ?>" class=" btn mright5 btn-default pull-left display-block">
											<?php echo _l('hr__back'); ?>
										</a>
								</div>
							</div>
							<br/>

							<div class="row">
								<div class="col-md-3 pull-right">
									<div class="form-group ">
										<select name="job_position_id[]" class="selectpicker" id="job_position_id" data-width="100%" data-live-search="true" multiple="true" data-action-box="true" data-none-selected-text="<?php echo _l('hr_hr_job_position'); ?>"> 
											<?php foreach($get_job_position as $p){ ?> 
												<option value="<?php echo html_entity_decode($p['position_id']); ?>" <?php if(isset($member) && $member->job_position == $p['position_id']){echo 'selected';} ?>><?php echo html_entity_decode($p['position_name']); ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="col-md-3 pull-right">
									<div class="form-group">
										<select onchange="department_change(this)" name="department_id[]" class="selectpicker" id="department_id" data-width="100%" data-live-search="true" multiple="true" data-action-box="true"  data-none-selected-text="<?php echo _l('departments') ?>">

											<?php foreach($hr_profile_get_department_name as $dp){ ?> 
												<option value="<?php echo html_entity_decode($dp['departmentid']); ?>"><?php echo html_entity_decode($dp['name']); ?></option>
											<?php } ?>

										</select>
									</div>
								</div>
							</div>

							<div class="modal bulk_actions" id="table_contract_bulk_actions" tabindex="-1" role="dialog">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											<h4 class="modal-title"><?php echo _l('hr_bulk_actions'); ?></h4>
										</div>
										<div class="modal-body">
											<?php if(has_permission('staffmanage_job_position','','delete') || is_admin()){ ?>
												<div class="checkbox checkbox-danger">
													<input type="checkbox" name="mass_delete" id="mass_delete">
													<label for="mass_delete"><?php echo _l('hr_mass_delete'); ?></label>
												</div>
											<?php } ?>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>

											<?php if(has_permission('staffmanage_job_position','','delete') || is_admin()){ ?>
												<a href="#" class="btn btn-info" onclick="staff_delete_bulk_action(this); return false;"><?php echo _l('hr_confirm'); ?></a>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>

							<?php if (has_permission('staffmanage_job_position','','delete')) { ?>
								<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_job" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('hr_bulk_actions'); ?></a>
							<?php } ?>

							<!-- Render table job -->
							<div class="clearfix"></div>
							<?php render_datatable(array(
								'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_job"><label></label></div>',
								_l('hr_job_id'),
								_l('hr_job_p'),
								_l('hr_hr_description'),
								_l('departments'),
							),'table_job',
							array('customizable-table'),
							array(
								'id'=>'table-table_job',
								'data-last-order-identifier'=>'table_job',
								'data-default-order'=>get_table_last_order('table_job'),
							)); ?>


							<div class="modal fade" id="job_p" tabindex="-1" role="dialog">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											<h4 class="modal-title">
												<span class="edit-title"><?php echo _l('hr_edit_job_p'); ?></span>
												<span class="add-title"><?php echo _l('hr_new_job_p'); ?></span>
											</h4>
										</div>
										<?php echo form_open_multipart(admin_url('hr_profile/job_p'),array('class'=>'job_p','autocomplete'=>'off')); ?>
										<div class="modal-body">
											<div id="additional_job"></div>
											<div role="tabpanel" class="tab-pane active" id="general_infor">
												<div class="row">
													<div class="col-md-12">
														<div id="additional"></div>   
														<div class="form">     
															<?php 
															echo render_input('job_name','hr_job_p'); ?>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<p class="bold"><?php echo _l('hr_hr_description'); ?></p>
														<?php $contents = ''; if(isset($project)){$contents = $project->description;} ?>
														<?php echo render_textarea('description','',$contents,array(),array(),'','tinymce'); ?>
													</div>
												</div>

											</div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
											<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
										</div>
										<?php echo form_close(); ?>                 
									</div>
								</div>
							</div>


						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="btn-bottom-pusher"></div>
	</div>
</div>
<div id="new_version"></div>
<?php init_tail(); ?>
</body>
</html>
