<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
	<div role="tabpanel" class="tab-pane" id="training_program" >

		<div class="row">

			<div  class="col-md-4 leads-filter-column">
				<label><?php echo _l('als_staff'); ?></label>
				<select name="staff[]" id="staff" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
					<?php foreach($list_staff as $s) { ?>
						<option value="<?php echo html_entity_decode($s['staffid']); ?>"><?php echo html_entity_decode($s['firstname'].' '. $s['lastname']); ?></option>
					<?php } ?>
				</select>
			</div>

			<div  class="col-md-4 leads-filter-column">
				<label><?php echo _l('hr_training_library'); ?></label>
				<select name="training_library[]" id="training_library" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
					<?php foreach($training_libraries as $training_library) { ?>
						<option value="<?php echo html_entity_decode($training_library['training_id']); ?>"><?php echo html_entity_decode($training_library['subject']); ?></option>
					<?php } ?>
				</select>
			</div>

			<div  class="col-md-4 leads-filter-column">
				<label><?php echo _l('hr_training_program'); ?></label>
				<select name="training_program[]" id="training_program" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
					<?php foreach($training_programs as $training_program) { ?>
						<option value="<?php echo html_entity_decode($training_program['training_process_id']); ?>"><?php echo html_entity_decode($training_program['training_name']); ?></option>
					<?php } ?>
				</select>
			</div>
			 

		</div>

		<div class="clearfix"></div>
		<br>

		<div class="modal bulk_actions" id="table_training_result_bulk_actions" tabindex="-1" role="dialog">
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
			<a href="#" class="hide"  onclick="training_program_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_training_result" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('hr_bulk_actions'); ?></a>
		<?php } ?>

		<?php 
		$table_data = array(
			'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_training_result"><label></label></div>',

			_l('id'),
			_l('hr_hr_staff_name'),
			_l('hr_training_library'),
			_l('hr_training_type'),
			_l('hr_datecreator'),
		);

		render_datatable($table_data,'table_training_result',
			array('customizable-table'),
			array(
				'id'=>'table-table_training_result',
				'data-last-order-identifier'=>'table_training_result',
				'data-default-order'=>get_table_last_order('table_training_result'),
			)); 

			?>

		</div>
		<!-- training_program end -->
	</div>

