<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s" >
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<div class="_buttons">
									<?php if(is_admin() || has_permission('hrm_dependent_person','','create')) { ?>

										<a href="" onclick="dependent_person_add('', '', true); return false;" class=" btn mright5 btn-primary pull-left display-block">
											<?php echo _l('hr_new_dependent_person'); ?>
										</a>

										<a href="<?php echo admin_url('hr_profile/import_xlsx_dependent_person'); ?>" class=" btn mright5 btn-default pull-left display-block">
											<?php echo _l('hr_job_p_import_excel'); ?>
										</a>
										
									<?php } ?>
								</div>
							</div>
						</div>
						<br>
						<div class="row">
							<div  class="col-md-3 leads-filter-column pull-right">
								<?php 
								//1 approved, 2 rejected, 0 pending
								$array_status=[];
								$array_status['1'] = _l('hr_agree_label');
								$array_status['2'] = _l('hr_rejected_label');
								$array_status['0'] = _l('hr_pending_label');
								 ?>
								<select name="status[]" id="status" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('hr_status_label'); ?>">
									<?php foreach($array_status as $key => $status) { ?>
										<option value="<?php echo html_entity_decode($key); ?>"><?php echo html_entity_decode($status); ?></option>
									<?php } ?>
								</select>
							</div>

							<div  class="col-md-3 leads-filter-column pull-right">
								<select name="staff[]" id="staff" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('hr_hr_staff_name'); ?>">
									<?php foreach($staff as $s) { ?>
										<option value="<?php echo html_entity_decode($s['staffid']); ?>"><?php echo html_entity_decode($s['firstname'].' '. $s['lastname']); ?></option>
									<?php } ?>
								</select>
							</div> 
							
						</div>
						<br>

						<div class="row">

							<!-- Render table  -->
							<div class="clearfix"></div>
							<div class="col-md-12">

								<div class="modal bulk_actions" id="table_contract_bulk_actions" tabindex="-1" role="dialog">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
												<h4 class="modal-title"><?php echo _l('hr_bulk_actions'); ?></h4>
											</div>
											<div class="modal-body">
												<?php if(has_permission('hrm_dependent_person','','delete') || is_admin()){ ?>
													<div class="checkbox checkbox-danger">
														<input type="checkbox" name="mass_delete" id="mass_delete">
														<label for="mass_delete"><?php echo _l('hr_mass_delete'); ?></label>
													</div>
												<?php } ?>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>

												<?php if(has_permission('hrm_dependent_person','','delete') || is_admin()){ ?>
													<a href="#" class="btn btn-info" onclick="staff_delete_bulk_action(this); return false;"><?php echo _l('hr_confirm'); ?></a>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>

								<?php if (has_permission('hrm_dependent_person','','delete')) { ?>
									<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_dependent_person" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('hr_bulk_actions'); ?></a>
								<?php } ?>
									
								<?php render_datatable(array(
									'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_dependent_person"><label></label></div>',
									_l('id'),
									_l('hr_dependent_name'),
									_l('hr_hr_staff_name'),
									_l('hr_dependent_bir'),
									_l('hr_dependent_iden'),
									_l('hr_start_month'),
									_l('hr_reason_label'),
									_l('hr_status_label'),
									_l('options'),
									_l('hr_status_comment'),
								),'table_dependent_person',
								array('customizable-table'),
								array(
									'id'=>'table-table_dependent_person',
									'data-last-order-identifier'=>'table_dependent_person',
									'data-default-order'=>get_table_last_order('table_dependent_person'),
								)); ?>
							</div>
							
							<div class="modal" id="approvaldependent" tabindex="-1" role="dialog">
								<div class="modal-dialog">
									<?php echo form_open(admin_url('hr_profile/approval_status')); ?>
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											<h4 class="modal-title">
												<span class="approval-title"><?php echo _l('hr_agree_label'); ?></span>
												<span class="reject-title"><?php echo _l('hr_rejected_label'); ?></span>
											</h4>
										</div>
										<div class="modal-body">
											<div id="dependent_status">
												
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form"> 
														<div class="row">
															<div class="col-md-6 start_month_hide">
																<?php 
																echo render_date_input('start_month','hr_start_month'); ?>
															</div>
															<div class="col-md-6 end_month_hide">
																<?php 
																echo render_date_input('end_month','hr_end_month'); ?>
															</div>
														</div>    
														<div class="row">
															<div class="col-md-12">
																<?php 
																echo render_input('reason','hr_reason_label'); ?>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
											<button type="button" class="btn btn-info" onclick="update_status(this); return false;" ><?php echo _l('submit'); ?></button>
										</div>
									</div>
									<?php echo form_close(); ?>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="modal_wrapper"></div>
	<?php init_tail(); ?>
	<?php  require('modules/hr_profile/assets/js/dependent_person/manage_js.php'); ?>
</body>
</html>
