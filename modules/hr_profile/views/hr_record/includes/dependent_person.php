			<div class="row">
				<div class="col-md-12">
					<?php if($member->staffid == get_staff_user_id() || has_permission('hrm_hr_records', '', 'create') || has_permission('hrm_hr_records', '', 'edit')){ ?>
						<div class="_buttons">
							<a href="#" onclick="new_dependent_person(); return false;" class="btn btn-info pull-left display-block">
								<?php echo _l('hr_add_dependents'); ?>
							</a>
						</div>
					<?php } ?>
				</div>
			</div>

			<div class="clearfix"></div>
			<br>
			<div class="row">
				<div class="col-md-12">
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
					),'table_dependent_person'); ?>
				</div>
			</div>

			<div class="modal fade" id="dependent" tabindex="-1" role="dialog">
				<div class="modal-dialog">
					<?php echo form_open(admin_url('hr_profile/dependent_person')); ?>

					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">
								<span class="edit-title"><?php echo _l('hr_edit_dependents'); ?></span>
								<span class="add-title"><?php echo _l('hr_add_dependents'); ?></span>
							</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<div id="dependent_person_id"></div>   
									<div class="form"> 
										<div class="row">
											<div class="col-md-6">
												<?php 
												echo render_input('dependent_name','hr_dependent_name'); ?>
											</div>
											<div class="col-md-6">
												<?php 
												echo render_input('relationship','hr_hr_relationship'); ?>
											</div>
										</div>    
										<div class="row">
											<div class="col-md-6">
												<?php 
												echo render_date_input('dependent_bir','hr_dependent_bir'); ?>
											</div>
											<div class="col-md-6">
												<?php 
												echo render_input('dependent_iden','hr_citizen_identification','','number'); ?>
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
							<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
						</div>
					</div>
					<?php echo form_close(); ?>
				</div>
			</div>