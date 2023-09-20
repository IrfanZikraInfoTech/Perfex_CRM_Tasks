<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">

						<div class="row">
							<div class="col-md-12">
								<?php if(is_admin() || has_permission('hrm_procedures_for_quitting_work','','create')){?>
		                    		<button class="btn btn-info" id="btn_new_staff"><?php echo _l('hr_new_resignation_procedures')?></button>
		                    	<?php } ?>
							</div>
						</div>

						<br>

						<div class="modal bulk_actions" id="table_resignation_procedures_bulk_actions" tabindex="-1" role="dialog">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title"><?php echo _l('hr_bulk_actions'); ?></h4>
									</div>
									<div class="modal-body">
										<?php if(has_permission('hrm_procedures_for_quitting_work','','delete') || is_admin()){ ?>
											<div class="checkbox checkbox-danger">
												<input type="checkbox" name="mass_delete" id="mass_delete">
												<label for="mass_delete"><?php echo _l('hr_mass_delete'); ?></label>
											</div>
										<?php } ?>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>

										<?php if(has_permission('hrm_procedures_for_quitting_work','','delete') || is_admin()){ ?>
											<a href="#" class="btn btn-info" onclick="staff_delete_bulk_action(this); return false;"><?php echo _l('hr_confirm'); ?></a>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>

						<?php if (has_permission('hrm_procedures_for_quitting_work','','delete')) { ?>
							<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_resignation_procedures" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('hr_bulk_actions'); ?></a>
						<?php } ?>

			      		<?php
                        $table_data = array(
                        	'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_resignation_procedures"><label></label></div>',
                            _l('staff_id'),
                            _l('hr_hr_staff_name'),
                            _l('departments'),
                            _l('hr_hr_job_position'),
                            _l('email'),
                            _l('hr_day_off'),
                            _l('hr_progress_label'),
                            _l('hr_status_label'),
                            _l('options'),
                            );
                        render_datatable($table_data,'table_resignation_procedures',
                        	array('customizable-table'),
                        	array(
                        		'id'=>'table-table_resignation_procedures',
                        		'data-last-order-identifier'=>'table_resignation_procedures',
                        		'data-default-order'=>get_table_last_order('table_resignation_procedures'),
                        	));

                        ?>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="new_staff" tabindex="-1" role="dialog"  aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content ">
			<?php echo form_open(admin_url('hr_profile/add_resignation_procedure'), array('id'=>'staff_quitting_work_form')); ?>
				<div class="modal-header pd-x-20">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <span class="approval-title"><?php echo _l('hr_new_resignation_procedures'); ?></span>
                    </h4>

				</div>
				<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<?php echo render_select('staffid',$staffs, array('staffid', array('firstname', 'lastname')), 'staff','') ?>
								<?php 
									$input_attr=[];
									$input_attr['readonly'] = true;
								 ?>
								<?php echo render_input('email', 'Email', '' , 'text', $input_attr) ?>
								<?php echo render_input('department_name', 'department','' , 'text', $input_attr) ?>
								<?php echo render_input('role_name', 'hr_hr_job_position','' , 'text', $input_attr) ?>
								<?php echo render_datetime_input('dateoff', 'hr_day_off') ?>
							</div>
						</div>
				</div><!-- modal-body -->
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _l('hr_close') ?></button>
					<button type="submit" class="btn btn-info"><?php echo _l('submit') ?></button>
				</div>
			<?php echo form_close(); ?>
		</div>
	</div><!-- modal-dialog -->
</div>
<div id="detail_checklist_staff" class="modal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content ">
			<?php echo form_open(admin_url('hr_profile/update_status_option_name')); ?>

			<div class="modal-header pd-x-20">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="approval-title"><?php echo _l('hr_resignation_procedures'); ?></span>
                </h4>

			</div>
			<div class="modal-body pd-20">
				<div class="content-modal-details">
					
				</div>
			</div><!-- modal-body -->
			<?php echo form_hidden('finish', 0); ?>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _l('hr_close') ?></button>
				<button type="submit" class="btn btn-success" id="finish_btn"><?php echo _l('hr_hr_finish') ?></button>
				<button type="submit" class="btn btn-info"><?php echo _l('submit') ?></button>

			</div>
			<?php echo form_close(); ?>
		</div>
	</div><!-- modal-dialog -->
</div>	
<?php init_tail(); ?>	
<?php  require('modules/hr_profile/assets/js/resignation_procedures/resignation_procedures_manage_js.php'); ?>

</body>
</html>