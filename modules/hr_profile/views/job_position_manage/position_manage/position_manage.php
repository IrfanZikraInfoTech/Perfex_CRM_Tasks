<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<?php init_head();?>
<div id="wrapper">
	<div class="content">
		<div class="row">

			<?php if ($this->session->flashdata('debug')) {?>
				<div class="col-lg-12">
					<div class="alert alert-warning">
						<?php echo html_entity_decode($this->session->flashdata('debug')); ?>
					</div>
				</div>
			<?php }?>


			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">

						<div role="tabpanel" class="tab-pane" id="job_position_tab">
							<div class="_buttons">
								<?php if (is_admin() || has_permission('staffmanage_job_position', '', 'create')) {?>
									<a href="#" onclick="hrrecord_new_job_position(); return false;" class="btn btn-info pull-left ">
										<?php echo _l('hr_new_job_position'); ?>
									</a>
								<?php }?>

									<div class="btn-group mleft5">
										<a href="#" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('hr_position_groups') . ' '; ?><span class="caret"></span></a>
										<ul class="dropdown-menu dropdown-menu-right">
											<?php if (is_admin() || has_permission('staffmanage_job_position', '', 'create')) {?>
											<li class="hidden-xs"><a href="#" onclick="new_job_p(); return false;" >
												<?php echo _l('hr_new_position_groups'); ?>
											</a>
										</li>
										<?php }?>

										<li class="hidden-xs"><a href="<?php echo admin_url('hr_profile/job_position_manage'); ?>">
											<?php echo _l('hr_manage_position_groups'); ?></a>
										</li>
									</ul>
								</div>

							<?php if (is_admin() || has_permission('staffmanage_job_position', '', 'create')) {?>

								<a href="<?php echo admin_url('hr_profile/import_job_position'); ?>" class=" btn mright5 btn-default ">
									<?php echo _l('hr_job_p_import_excel'); ?>
								</a>
							<?php }?>
						</div>
						<div class="clearfix"></div>
						<br>

						<div class="row">
							<div class="col-md-3 pull-right">
								<div class="form-group ">
									<select name="job_p_id[]" class="selectpicker" id="job_p_id" data-width="100%" data-live-search="true" multiple="true" data-actions-box="true" data-none-selected-text="<?php echo _l('hr_job_p'); ?>">
										<?php foreach ($job_p_id as $p) {?>
											<option value="<?php echo html_entity_decode($p['job_id']); ?>"><?php echo html_entity_decode($p['job_name']); ?></option>
										<?php }?>
									</select>
								</div>
							</div>
							<div class="col-md-3 pull-right">
								<div class="form-group">
									<select name="department_id[]" class="selectpicker" id="department_id" data-width="100%" data-live-search="true" multiple="true" data-actions-box="true"  data-none-selected-text="<?php echo _l('departments') ?>">

										<?php foreach ($hr_profile_get_department_name as $dp) {?>
											<option value="<?php echo html_entity_decode($dp['departmentid']); ?>"><?php echo html_entity_decode($dp['name']); ?></option>
										<?php }?>

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
										<?php if (has_permission('staffmanage_job_position', '', 'delete') || is_admin()) {?>
											<div class="checkbox checkbox-danger">
												<input type="checkbox" name="mass_delete" id="mass_delete">
												<label for="mass_delete"><?php echo _l('hr_mass_delete'); ?></label>
											</div>
										<?php }?>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>

										<?php if (has_permission('staffmanage_job_position', '', 'delete') || is_admin()) {?>
											<a href="#" class="btn btn-info" onclick="staff_delete_bulk_action(this); return false;"><?php echo _l('hr_confirm'); ?></a>
										<?php }?>
									</div>
								</div>
							</div>
						</div>

						<?php if (has_permission('staffmanage_job_position', '', 'delete')) {?>
							<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_job_position" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('hr_bulk_actions'); ?></a>
						<?php }?>

						<?php render_datatable(array(
	'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_job_position"><label></label></div>',
	_l('position_id'),
	_l('hr_position_code'),
	_l('hr_position_name'),
	_l('hr_job_descriptions'),
	_l('department_name'),
	_l('hr_job_p_id'),
), 'table_job_position',
	array('customizable-table'),
	array(
		'id' => 'table-table_job_position',
		'data-last-order-identifier' => 'table_job_position',
		'data-default-order' => get_table_last_order('table_job_position'),
	));?>

					</div>


					<div class="clearfix"></div>
				</div>
			</div>
		</div>


		<div class="btn-bottom-pusher"></div>
		<div id="contract_file_data"></div>
		<div id="new_version"></div>


		<div class="modal fade" id="new_job_positions" tabindex="-1" role="dialog">
			<div class="modal-dialog new_job_positions_dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">
							<span class="edit-title"><?php echo _l('hr_edit_job_rank'); ?></span>
							<span class="add-title"><?php echo _l('hr_new_job_rank'); ?></span>
						</h4>
					</div>
					<?php echo form_open_multipart(admin_url('hr_profile/job_position'), array('id' => 'job_position', 'autocomplete' => 'off')); ?>
					<div class="modal-body">
						<div id="additional_proposal"></div>

						<!-- general_info start -->
						<div role="tabpanel" class="tab-pane active" id="general_infor">
							<div class="row">
								<div class="col-md-6">
									<div id="additional"></div>
									<div class="form">
										<?php echo render_input('position_code', 'hr_position_code'); ?>
									</div>
								</div>
								<div class="col-md-6">
									<?php
echo render_input('position_name', 'hr_position_name'); ?>
								</div>

								<div class="col-md-6">
									<?php echo render_select('job_p_id', $job_p_id, array('job_id', 'job_name'), 'hr_job_p_id'); ?>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label for="department_id" class="control-label get_id_row" value ="0" ><?php echo _l('departments'); ?></label>

										<select name="department_id[]" class="selectpicker" id="department_id" data-width="100%" data-live-search="true" multiple="true" data-action-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
											<?php foreach ($hr_profile_get_department_name as $dp) {?>
												<option value="<?php echo html_entity_decode($dp['departmentid']); ?>"><?php echo html_entity_decode($dp['name']); ?></option>
											<?php }?>
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group" id="tags_value">
										<label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
										<input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($project) ? prep_tags_input(get_tags_in($project->id, 'project')) : ''); ?>" data-role="tagsinput">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<p class="bold"><?php echo _l('hr_job_descriptions'); ?></p>
									<?php $contents = '';if (isset($project)) {$contents = $project->description;}?>
									<?php echo render_textarea('job_position_description', '', $contents, array(), array(), '', 'tinymce'); ?>
								</div>
							</div>
							<!-- view attachment file -->
							<div class="row">
								<div id="position_attachments" class="mtop30 col-md-12 "></div>
							</div>
							<!-- file attachment -->
							<div class="row">
								<div class="col-md-12">
									<div class=" attachments">
										<div class="attachment">
											<div class="col-md-6 pl-0">
												<div class="form-group">
													<label for="attachment" class="control-label"><?php echo _l('add_task_attachments'); ?></label>
													<div class="input-group">
														<input type="file" extension="<?php echo str_replace('.', '', get_option('allowed_files')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="file[0]">
														<span class="input-group-btn">
															<button class="btn btn-success add_more_attachments_file p8" type="button"><i class="fa fa-plus"></i></button>
														</span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
						<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
					</div>
					<?php echo form_close(); ?>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->

		<!-- New position group -->
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
					<?php echo form_open_multipart(admin_url('hr_profile/job_p'), array('class' => 'job_p', 'autocomplete' => 'off')); ?>
					<div class="modal-body">
						<div id="additional_job"></div>
						<div role="tabpanel" class="tab-pane active" id="general_infor">
							<div class="row">
								<div class="col-md-12">
									<div id="additional"></div>
									<div class="form">
										<?php
echo render_input('job_name', 'hr_job_p'); ?>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">

									<p class="bold"><?php echo _l('hr_hr_description'); ?></p>
									<?php $contents = '';if (isset($project)) {$contents = $project->description;}?>
									<?php echo render_textarea('description', '', $contents, array(), array(), '', 'tinymce'); ?>
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

<?php init_tail();?>
</body>
</html>
