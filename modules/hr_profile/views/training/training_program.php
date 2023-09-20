<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
	<div role="tabpanel" class="tab-pane <?php if(isset($tab) && $tab='training_program'){echo 'active';} ?>" id="training_program" >
		<div class="_buttons">
			<?php  if(is_admin() || has_permission('staffmanage_training','','create')) { ?>
				<a href="#" onclick="new_training_process(); return false;" class="btn btn-info pull-left display-block">
					<?php echo _l('hr_hr_add'); ?>
				</a>
			<?php } ?>
		</div>

		<div class="clearfix"></div>
		<br>

		<div class="modal bulk_actions" id="table_training_program_bulk_actions" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title"><?php echo _l('hr_bulk_actions'); ?></h4>
					</div>
					<div class="modal-body">
						<?php if(has_permission('staffmanage_training','','delete') || is_admin()){ ?>
							<div class="checkbox checkbox-danger">
								<input type="checkbox" name="mass_delete" id="mass_delete">
								<label for="mass_delete"><?php echo _l('hr_mass_delete'); ?></label>
							</div>
						<?php } ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>

						<?php if(has_permission('staffmanage_training','','delete') || is_admin()){ ?>
							<a href="#" class="btn btn-info" onclick="training_program_delete_bulk_action(this); return false;"><?php echo _l('hr_confirm'); ?></a>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>

		<?php if (has_permission('staffmanage_training','','delete')) { ?>
			<a href="#"  onclick="training_program_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_training_program" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('hr_bulk_actions'); ?></a>
		<?php } ?>

		<?php 
		$table_data = array(
			'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_training_program"><label></label></div>',

			_l('id'),
			_l('name'),
			_l('hr_training_type'),
			_l('hr_hr_description'),
			_l('hr_mint_point'),
			_l('hr_datecreator'),
		);

		render_datatable($table_data,'table_training_program',
			array('customizable-table'),
			array(
				'id'=>'table-table_training_program',
				'data-last-order-identifier'=>'table_training_program',
				'data-default-order'=>get_table_last_order('table_training_program'),
			)); 

			?>

		</div>
		<!-- training_program end -->
	</div>

	<div class="modal fade" id="job_position_training" tabindex="-1" role="dialog">
		<div class="modal-dialog new_job_positions_dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<span class="add-title-training"><?php echo _l('hr_edit_training_process'); ?></span>
						<span class="edit-title-training"><?php echo _l('hr_new_training_process'); ?></span>
					</h4>
				</div>
				<?php echo form_open_multipart(admin_url('hr_profile/job_position_training_add_edit'),array('class'=>'job_position_training_add_edit','autocomplete'=>'off')); ?>
				<div class="modal-body">
					<div id="additional_form_training"></div>
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="interview_infor">

							<div class="row">
								<div class="col-md-6">
									<?php echo render_input('training_name', 'hr_training_name'); ?>
								</div>
								<div class="col-md-6">
									<label for="training_type" class="control-label"><?php echo _l('hr_training_type'); ?></label>
									<select onchange="training_type_change(this)" name="training_type" class="selectpicker" id="training_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
										<option value=""></option> 

										<?php foreach ($type_of_trainings as $key => $value) { ?>
											<option value="<?php echo $value['id'] ?>" <?php if(isset($position_training) && $position_training->training_type ==  $value['id'] ){echo 'selected';} ?> ><?php echo $value['name']  ?></option>

										<?php } ?>
										
									</select>

								</div>
							</div>

							<div class="row ">
								<div class="col-md-6">
									<label for="position_training_id" class="control-label get_id_row" value ="0" ><span class="text-danger">* </span><?php echo _l('hr_training_item'); ?></label>

									<select name="position_training_id[]" class="selectpicker mb-5" id="position_training_id[]" data-width="100%" data-live-search="true" multiple="true" data-actions-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-sl-id="e_criteria[0]" > 
									</select>
								</div>
								<div class="col-md-6">
									<?php $mint_point_f="1";
									$min_p =[];
									$min_p['min']='0';
									// $min_p['required']='true';

									?>
									<?php echo render_input('mint_point','hr_mint_point',$mint_point_f,'number', $min_p) ?>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<div class="checkbox checkbox-primary">
											<input  type="checkbox" id="additional_training" name="additional_training"  value="additional_training" >
											<label for="additional_training"><?php echo _l('hr_additional_training'); ?></label>
										</div>
									</div>
								</div>
							</div>

							<div class="row additional_training_hide hide">
								<div class="col-md-12">
									<div class="form-group">
										<label for="staff_id" class="control-label"><?php echo _l('hr_hr_staff_name'); ?></label>
										<select name="staff_id[]" data-live-search="true" class="selectpicker" id="staff_id" data-width="100%" multiple="true"data-actions-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" > 
											<?php foreach ($staffs as $staff){ ?>
												<option value="<?php echo html_entity_decode($staff['staffid']) ?>"><?php echo html_entity_decode($staff['firstname'].' '.$staff['lastname']); ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								
								<div class="col-md-6">
									<?php
									echo render_date_input('time_to_start','hr_time_to_start'); ?>
								</div>
								<div class="col-md-6">
									<?php
									echo render_date_input('time_to_end','hr_time_to_end'); ?>
								</div>
							</div>

							<div class="row mb-4 onboading_hide">
								<div class="col-md-6">

									<label for="department_id" class="control-label get_id_row" value ="0" ><?php echo _l('hr_department'); ?></label>
									<select onchange="department_change(this)" name="department_id[]" class="selectpicker" id="department_id" data-width="100%" data-live-search="true" multiple="true" data-actions-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
										<?php foreach($hr_profile_get_department_name as $dp){ ?> 
											<option value="<?php echo html_entity_decode($dp['departmentid']); ?>"><?php echo html_entity_decode($dp['name']); ?></option>
										<?php } ?>

									</select>

								</div>

								<div class="col-md-6">

									<label for="job_position_id" class="control-label get_id_row" value ="0" ><span class="text-danger">* </span><?php echo _l('hr__position_apply'); ?></label>

									<select name="job_position_id[]" class="selectpicker" id="job_position_id" data-width="100%" data-live-search="true" multiple="true" data-actions-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required> 
										<?php foreach($get_job_position as $p){ ?> 
											<option value="<?php echo html_entity_decode($p['position_id']); ?>" <?php if(isset($member) && $member->job_position == $p['position_id']){echo 'selected';} ?>><?php echo html_entity_decode($p['position_name']); ?></option>
										<?php } ?>
									</select>
									<div class="clearfix"></div>
									<br>
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
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
					<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
				</div>
				<?php echo form_close(); ?>                 
			</div>
		</div>
	</div>
</body>
</html>
