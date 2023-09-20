<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php echo form_open_multipart(admin_url('hr_profile/prefix_number'),array('class'=>'prefix_number','autocomplete'=>'off')); ?>

<div class="row">
	<div class="col-md-12">
		<h5 class="no-margin font-bold h5-color"><?php echo _l('hr_position_code') ?></h5>
		<hr class="hr-color">
	</div>
</div>

<div class="form-group">
	<label><?php echo _l('hr_job_position_prefix'); ?></label>
	<div  class="form-group" app-field-wrapper="job_position_prefix">
		<input type="text" id="job_position_prefix" name="job_position_prefix" class="form-control" value="<?php echo get_hr_profile_option('job_position_prefix'); ?>"></div>
	</div>

	<div class="form-group">
		<label><?php echo _l('hr_job_position_number'); ?></label>
		<i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('hr_next_number_tooltip'); ?>"></i>
		<div  class="form-group" app-field-wrapper="job_position_number">
			<input type="number" min="0" id="job_position_number" name="job_position_number" class="form-control" value="<?php echo get_hr_profile_option('job_position_number'); ?>">
		</div>

	</div>

	<div class="row">
		<div class="col-md-12">
			<h5 class="no-margin font-bold h5-color"><?php echo _l('hr_staff_contract_code') ?></h5>
			<hr class="hr-color">
		</div>
	</div>

	<div class="form-group">
		<label><?php echo _l('hr_contract_code_prefix'); ?></label>
		<div  class="form-group" app-field-wrapper="contract_code_prefix">
			<input type="text" id="contract_code_prefix" name="contract_code_prefix" class="form-control" value="<?php echo get_hr_profile_option('contract_code_prefix'); ?>"></div>
		</div>

		<div class="form-group">
			<label><?php echo _l('hr_contract_code_number'); ?></label>
			<i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('hr_next_number_tooltip'); ?>"></i>
			<div  class="form-group" app-field-wrapper="contract_code_number">
				<input type="number" min="0" id="contract_code_number" name="contract_code_number" class="form-control" value="<?php echo get_hr_profile_option('contract_code_number'); ?>">
			</div>

		</div>

		<div class="row">
			<div class="col-md-12">
				<h5 class="no-margin font-bold h5-color"><?php echo _l('hr_staff_code') ?></h5>
				<hr class="hr-color">
			</div>
		</div>

		<div class="form-group">
			<label><?php echo _l('hr_staff_code_prefix'); ?></label>
			<div  class="form-group" app-field-wrapper="staff_code_prefix">
				<input type="text" id="staff_code_prefix" name="staff_code_prefix" class="form-control" value="<?php echo get_hr_profile_option('staff_code_prefix'); ?>">
			</div>
		</div>

		<div class="form-group">
			<label><?php echo _l('hr_staff_code_number'); ?></label>
			<i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('hr_next_number_tooltip'); ?>"></i>
			<div  class="form-group" app-field-wrapper="staff_code_number">
				<input type="number" min="0" id="staff_code_number" name="staff_code_number" class="form-control" value="<?php echo get_hr_profile_option('staff_code_number'); ?>">
			</div>

		</div>

		<div class="row">
			<div class="col-md-12">
				<h5 class="no-margin font-bold h5-color"><?php echo _l('hr_not_staff_member_setting') ?></h5>
				<hr class="hr-color">
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<div class="checkbox checkbox-primary">
						<input type="checkbox" id="hr_profile_hide_menu" name="hr_profile_hide_menu" <?php if(get_option('hr_profile_hide_menu') == 1 ){ echo 'checked';} ?> value="1">
						<label for="hr_profile_hide_menu"><?php echo _l('hr_not_staff_member_label'); ?></label>
						<i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('hr_not_staff_member_tooltip'); ?>"></i>
					</div>
				</div>
			</div>
		</div>



		<div class="clearfix"></div>

		<div class="modal-footer">
			<?php if(has_permission('hrm_setting', '', 'create') || has_permission('hrm_setting', '', 'edit') ){ ?>
			<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
		<?php } ?>
		</div>
		<?php echo form_close(); ?>


	</body>
	</html>


