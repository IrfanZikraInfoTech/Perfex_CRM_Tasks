<div class="modal fade" id="dependentPersonModal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<?php echo form_open(admin_url('hr_profile/dependent_person'), array('id' => 'dependent_person' )); ?>

		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">
					<span class="edit-title <?php if(!isset($dependent_person)){ echo ' hide' ;}; ?>"><?php echo _l('hr_edit_dependent_person'); ?></span>
					<span class="add-title <?php if(isset($dependent_person)){ echo ' hide' ;}; ?>"><?php echo _l('hr_new_dependent_person'); ?></span>
				</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div id="dependent_person_id"></div>   
						<div class="form"> 
							<div class="row">

								<?php
								if(isset($dependent_person)){
									echo form_hidden('id',$dependent_person->id);
								}
								echo form_hidden('manage',$manage);
								?>

								<div class="col-md-12 <?php if(isset($dependent_person)){ echo ' hide' ;}; ?>">
									<?php 
									$staff_selected = '';
									if(isset($dependent_person)){
										$staff_selected = $dependent_person->staffid;
									}

									?>
									<?php echo render_select('staffid',$staff_members,array('staffid',array('firstname', 'lastname')),'hr_hr_staff_name',$staff_selected); ?>
								</div>
								<div class="col-md-6">
									<?php 
									$dependent_name =  isset($dependent_person) ? $dependent_person->dependent_name : '';
									echo render_input('dependent_name','hr_dependent_name', $dependent_name); ?>
								</div>
								<div class="col-md-6">
									<?php 
									$relationship =  isset($dependent_person) ? $dependent_person->relationship : '';
									echo render_input('relationship','hr_hr_relationship', $relationship); ?>
								</div>
							</div>    
							<div class="row">
								<div class="col-md-6">
									<?php 
									$birthday =  isset($dependent_person) ? _d($dependent_person->dependent_bir) : '';
									echo render_date_input('dependent_bir','hr_dependent_bir', $birthday); ?>
								</div>
								<div class="col-md-6">
									<?php 
									$dependent_iden =  isset($dependent_person) ? $dependent_person->dependent_iden : '';

									echo render_input('dependent_iden','hr_citizen_identification', $dependent_iden,'number'); ?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<?php 
									$reason =  isset($dependent_person) ? $dependent_person->reason : '';
									echo render_input('reason','hr_reason_label', $reason); ?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<?php 
									$start_month =  isset($dependent_person) ? _d($dependent_person->start_month) : '';
									echo render_date_input('start_month','hr_start_month', $start_month); ?>
								</div>
								<div class="col-md-6">
									<?php 
									$end_month =  isset($dependent_person) ? _d($dependent_person->end_month) : '';
									echo render_date_input('end_month','hr_end_month', $end_month); ?>
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
<?php require('modules/hr_profile/assets/js/dependent_person/modal_js.php'); ?>

