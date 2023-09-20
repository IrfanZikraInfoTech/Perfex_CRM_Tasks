<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php  init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="_buttons">
							<?php if (is_admin() || has_permission('hrm_hr_records','','create') || has_permission('hrm_hr_records','','edit')) { ?>

								<a href="<?php echo admin_url('hr_profile/new_member'); ?>" class="btn mright5 btn-info pull-left display-block "><?php echo _l('new_staff'); ?></a>
								<a href="<?php echo admin_url('hr_profile/importxlsx'); ?>" class="btn mright5 btn-info pull-left  ">
									<?php echo _l('hr_import_xlsx_hr_profile'); ?>
								</a>


							<?php } ?>

							<?php if (is_admin() || has_permission('hrm_hr_records','','create') || has_permission('hrm_hr_records','','edit') ) { ?>
								<a href="#" onclick="staff_export_item(); return false;"  class="mright5 btn btn-warning pull-left   hr_export_staff">
									<?php echo _l('hr_export_staff'); ?>
								</a>

								 <a href="#" id="dowload_items"  class="btn btn-success pull-left  mr-4 button-margin-r-b hide"><?php echo _l('dowload_staffs'); ?></a>

							<?php } ?>
							
							<a href="#" onclick="view_staff_chart(); return false;"  class="mright5 btn btn-default pull-left display-block">
								<?php echo _l('hr_view_staff_chart'); ?>
							</a>
							
						</div>
						<br>
						<div class="row"></div>
						<br>
						<div class="row">

							<!-- fillter by teammanage -->
							<div class="col-md-3 pull-right hide">
								<input type="text" id="staff_dep_tree" name="staff_dep_tree" class="selectpicker" placeholder="<?php echo _l('hr_team_manage'); ?>" autocomplete="off">
								<input type="hidden" name="staff_tree" id="staff_tree"/>
							</div>

							<div class="col-md-3 pull-right">
								<select name="status_work[]" class="selectpicker" multiple="true" id="status_work" data-width="100%" data-none-selected-text="<?php echo _l('hr_status_label'); ?>"> 
									<option value="<?php echo 'working' ?>"><?php echo _l('hr_working'); ?></option>
									<option value="<?php echo 'maternity_leave'; ?>"><?php echo _l('hr_maternity_leave'); ?></option>
									<option value="<?php echo 'inactivity'; ?>"><?php echo _l('hr_inactivity'); ?></option>
								</select>
							</div>
							<div class="col-md-3 pull-right">
								<select name="staff_role[]" class="selectpicker" multiple="true" id="staff_role" data-width="100%" data-actions-box="true" data-live-search="true" data-none-selected-text="<?php echo _l('hr_hr_job_position'); ?>"> 
									<?php 
									foreach ($staff_role as $value) { ?>
										<option value="<?php echo html_entity_decode($value['position_id']); ?>"><?php echo html_entity_decode($value['position_name']) ?></option>
									<?php }
									?>              
								</select>
							</div>
							<div class="col-md-3 leads-filter-column pull-right">
								<select name="hr_profile_deparment" class="selectpicker" id="hr_profile_deparment" data-width="100%"  data-live-search="true" data-none-selected-text="<?php echo _l('departments'); ?>"> 
									<option value=""></option>
									<?php 
									foreach ($departments as $value) { ?>
										<option value="<?php echo html_entity_decode($value['departmentid']); ?>"><?php echo html_entity_decode($value['name']) ?></option>
									<?php }
									?>              
								</select>
							</div>



						</div>
						<br>

						<div class="row">
							<div class="col-md-12">
								<div class="modal bulk_actions" id="table_staff_bulk_actions" tabindex="-1" role="dialog">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
												<h4 class="modal-title"><?php echo _l('hr_bulk_actions'); ?></h4>
											</div>
											<div class="modal-body">
												<?php if(has_permission('crm_mana_leads','','delete')){ ?>
													<div class="checkbox checkbox-danger">
														<input type="checkbox" name="mass_delete" id="mass_delete">
														<label for="mass_delete"><?php echo _l('hr_mass_delete'); ?></label>
													</div>
												<?php } ?>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
												<a href="#" class="btn btn-info" onclick="staff_delete_bulk_action(this); return false;"><?php echo _l('hr_confirm'); ?></a>
											</div>
										</div>
									</div>
								</div>
								<?php if (has_permission('hrm_hr_records','','delete')) { ?>
									<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_staff" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('hr_bulk_actions'); ?></a>
								<?php } ?>

								<?php
								$table_data = array(
									'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_staff"><label></label></div>',
									_l('staff_dt_name'),
									_l('hr_staff_code'),
									_l('staff_dt_email'),
									_l('departments'),       
									_l('hr_sex'),
									_l('hr_hr_job_position'),
									_l('role'),
									_l('hr_active'),
									_l('hr_status_work'),                            
								);
								$custom_fields = get_custom_fields('staff',array('show_on_table'=>1));
								foreach($custom_fields as $field){
									array_push($table_data,$field['name']);
								}

								render_datatable($table_data,'table_staff',
									array('customizable-table'),
									array(
										'id'=>'table-table_staff',
										'data-last-order-identifier'=>'table_staff',
										'data-default-order'=>get_table_last_order('table_staff'),
									)); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="modal" id="delete_staff" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<?php echo form_open(admin_url('hr_profile/delete_staff',array('delete_staff_form'))); ?>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?php echo _l('delete_staff'); ?></h4>
				</div>
				<div class="modal-body">
					<div class="delete_id">
						<?php echo form_hidden('id'); ?>
					</div>
					<p><?php echo _l('delete_staff_info'); ?></p>
					<?php
					echo render_select('transfer_data_to',$staff_members,array('staffid',array('firstname','lastname')),'staff_member',get_staff_user_id(),array(),array(),'','',false);
					?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
					<button type="submit" class="btn btn-danger _delete"><?php echo _l('hr_confirm'); ?></button>
				</div>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>

	<div class="modal fade" id="staff_chart_view" tabindex="-1" role="dialog">
		<div class="modal-dialog w-100 h-100">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<span class="edit-title"><?php echo _l('hr_staff_chart'); ?></span>
					</h4>
				</div>
				<div class="modal-body">
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12" id="st_chart">
								<div id="staff_chart"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="modal_wrapper"></div>
	<?php init_tail(); ?>
	<?php 
	require('modules/hr_profile/assets/js/hr_record/hr_record_js.php');
	?>
</body>
</html>
