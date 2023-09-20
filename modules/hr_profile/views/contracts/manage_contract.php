<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="_filters _hidden_inputs hidden">
						<?php
						
						echo form_hidden('draft');
						echo form_hidden('valid');
						echo form_hidden('invalid');
						echo form_hidden('hr_contract_is_about_to_expire');
						echo form_hidden('hr_overdue_contract');
						
						foreach($staff as $s) { 
							echo form_hidden('contracts_by_staff_'.$s['staffid']);
						}
						foreach($contract_type as $type){
							echo form_hidden('contracts_by_type_'.$type['id_contracttype']);
						}
						foreach($duration as $d){
							echo form_hidden('contracts_by_duration_'.$d['duration'].'_'.$d['unit']);
						}
						?>
					</div>
					<div class="panel-body">
						<?php if(has_permission('hrm_contract','','create') || is_admin()){ { ?>
							<div class="_buttons">
								<a href="<?php echo admin_url('hr_profile/contract'); ?>" class="btn btn-info pull-left display-block mright5"><?php echo _l('new_contract'); ?></a>

								<a href="<?php echo admin_url('hr_profile/import_xlsx_contract'); ?>" class=" btn mright5 btn-default pull-left hide">
									<?php echo _l('hr_job_p_import_excel'); ?>
								</a>
							</div>
						<?php } ?>
						<div class="btn-group pull-right btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fa fa-filter" aria-hidden="true"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-left width300 height500">
								
								<li>
									<a href="#" data-cview="all" onclick="dt_custom_view('','.table-table_contract',''); return false;">
										<?php echo _l('contracts_view_all'); ?>
									</a>
								</li>
								<li class="filter-group" data-filter-group="status">
									<a href="#" data-cview="draft"  onclick="dt_custom_view('draft','.table-table_contract','draft'); return false;">
										<?php echo _l('hr_hr_draft'); ?>
									</a>
								</li>
								<li class="filter-group" data-filter-group="status">
									<a href="#" data-cview="valid"  onclick="dt_custom_view('valid','.table-table_contract','valid'); return false;">
										<?php echo _l('hr_hr_valid'); ?>
									</a>
								</li>
								<li class="filter-group" data-filter-group="status">
									<a href="#" data-cview="invalid"  onclick="dt_custom_view('invalid','.table-table_contract','invalid'); return false;">
										<?php echo _l('hr_hr_expired'); ?>
									</a>
								</li>
								<li class="filter-group" data-filter-group="status">
									<a href="#" data-cview="hr_contract_is_about_to_expire"  onclick="dt_custom_view('hr_contract_is_about_to_expire','.table-table_contract','hr_contract_is_about_to_expire'); return false;">
										<?php echo _l('hr_contract_is_about_to_expire'); ?>
									</a>
								</li>
								<li class="filter-group" data-filter-group="status">
									<a href="#" data-cview="hr_overdue_contract"  onclick="dt_custom_view('hr_overdue_contract','.table-table_contract','hr_overdue_contract'); return false;">
										<?php echo _l('hr_overdue_contract'); ?>
									</a>
								</li>
								
								<div class="clearfix"></div>
								<li class="divider"></li>

								<li class="dropdown-submenu pull-left">
									<a href="#" tabindex="-1"><?php echo _l('staff'); ?></a>
									<ul class="dropdown-menu dropdown-menu-left">
										<?php  foreach($staff as $s){ ?>
											<li><a href="#" data-cview="contracts_by_staff_<?php echo html_entity_decode($s['staffid']); ?>" onclick="dt_custom_view('contracts_by_staff_<?php echo html_entity_decode($s['staffid']); ?>','.table-table_contract','contracts_by_staff_<?php echo html_entity_decode($s['staffid']); ?>'); return false;">
												<?php echo html_entity_decode($s['firstname'].' '.$s['lastname']); ?>
											</a></li>
										<?php } ?>
									</ul>
								</li>
								<div class="clearfix"></div>
								<?php if(count($contract_type) > 0){ ?>
									<li class="divider"></li>
									<?php foreach($contract_type as $type){ ?>
										<li>
											<a href="#" data-cview="contracts_by_type_<?php echo html_entity_decode($type['id_contracttype']); ?>" onclick="dt_custom_view('contracts_by_type_<?php echo html_entity_decode($type['id_contracttype']); ?>','.table-table_contract','contracts_by_type_<?php echo html_entity_decode($type['id_contracttype']); ?>'); return false;">
												<?php echo html_entity_decode($type['name_contracttype']); ?>
											</a>
										</li>
									<?php } ?>
								<?php } ?>
								<div class="clearfix"></div>
								<?php if(count($duration) > 0){ ?>
									<li class="divider"></li>
									<?php foreach($duration as $type){ ?>
										<li class="filter-group" data-filter-group="duration">
											<a href="#" data-cview="contracts_by_duration_<?php echo html_entity_decode($type['duration']).'_'.$type['unit']; ?>" onclick="dt_custom_view('contracts_by_duration_<?php echo html_entity_decode($type['duration']).'_'.$type['unit']; ?>','.table-table_contract','contracts_by_duration_<?php echo html_entity_decode($type['duration']).'_'.$type['unit']; ?>'); return false;">
												<?php echo html_entity_decode($type['duration']).' '.$type['unit']; ?>
											</a>
											<li>
											<?php } ?>
										<?php } ?>
									</ul>
								</div>
								<div class="_buttons">
									<a href="#" class="btn btn-default pull-right btn-with-tooltip toggle-small-view hidden-xs" onclick="toggle_contract_view('.table-table_contract','#hrm_contract'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
								</div>

								<div class="clearfix"></div>
								<br>
							<?php } ?>

							<div class="row">

								<div class="col-md-3 leads-filter-column">
									<input type="text" id="hrm_derpartment_tree" name="hrm_derpartment_tree" class="selectpicker" placeholder="<?php echo _l('hr_hr_filter_by_department'); ?>" autocomplete="off">
									<input type="hidden" name="hrm_deparment" id="hrm_deparment"/>
								</div>

								<div  class="col-md-3 leads-filter-column">
									
									<select name="staff[]" id="staff" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('als_staff'); ?>">
										<?php foreach($staff as $s) { ?>
											<option value="<?php echo html_entity_decode($s['staffid']); ?>"><?php echo html_entity_decode($s['firstname'].' '. $s['lastname']); ?></option>
										<?php } ?>
									</select>
								</div> 

								<div  class="col-md-3 leads-filter-column ">
									<?php 
									$input_attr_e = [];
									$input_attr_e['placeholder'] = _l('hr_start_month');

									echo render_date_input('validity_start_date','','',$input_attr_e ); ?>
								</div> 
								<div  class="col-md-3 leads-filter-column ">
									<?php 
									$input_attr = [];
									$input_attr['placeholder'] = _l('hr_end_month');

									echo render_date_input('validity_end_date','','',$input_attr ); ?>
								</div> 
								
							</div>
							<br>
							<div class="row">
								<div class="col-md-12 left-column" id="small-table">
									<div class="clearfix"></div>

									<?php echo form_hidden('hrmcontractid',$hrmcontractid); ?>

									<div class="modal bulk_actions" id="table_contract_bulk_actions" tabindex="-1" role="dialog">
										<div class="modal-dialog" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
													<h4 class="modal-title"><?php echo _l('hr_bulk_actions'); ?></h4>
												</div>
												<div class="modal-body">
													<?php if(has_permission('hrm_contract','','delete') || is_admin()){ ?>
														<div class="checkbox checkbox-danger">
															<input type="checkbox" name="mass_delete" id="mass_delete">
															<label for="mass_delete"><?php echo _l('hr_mass_delete'); ?></label>
														</div>
													<?php } ?>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>

													<?php if(has_permission('hrm_contract','','delete') || is_admin()){ ?>
														<a href="#" class="btn btn-info" onclick="staff_delete_bulk_action(this); return false;"><?php echo _l('hr_confirm'); ?></a>
													<?php } ?>
												</div>
											</div>
										</div>
									</div>

									<?php if (has_permission('hrm_contract','','delete')) { ?>
										<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_contract" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('hr_bulk_actions'); ?></a>
									<?php } ?>

									<?php
									$table_data = array(
										'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_contract"><label></label></div>',

										_l('id'),
										_l('hr_contract_code'),
										_l('hr_name_contract'),
										_l('staff'),
										_l('departments'),
										_l('hr_start_month'),
										_l('hr_end_month'),
										_l('hr_status_label'),
										_l('hr_sign_day'),                            
									);
									
									render_datatable($table_data,'table_contract',
										array('customizable-table'),
										array(
											'id'=>'table-table_contract',
											'data-last-order-identifier'=>'table_contract',
											'data-default-order'=>get_table_last_order('table_contract'),
										)); ?>
										
									</div>
									<div class="col-md-7 right-column small-table-right-col">
										<div id="hrm_contract" class="hide">
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php init_tail(); ?>
		<?php 
		require('modules/hr_profile/assets/js/contracts/manage_contract_js.php');
		?>
	</body>
	</html>
