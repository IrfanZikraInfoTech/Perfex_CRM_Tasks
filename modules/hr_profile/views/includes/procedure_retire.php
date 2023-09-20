<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>

	<div class="_buttons pull-left">
		<?php if(is_admin() || has_permission('hrm_setting','','create')) {?>
			<a href="#" id="add_save" onclick="add_procedure_form_manage(''); return false;" class="btn btn-info pull-left display-block">
				<?php echo _l('hr_hr_add'); ?>
			</a>
		<?php } ?>
	</div>
	<div class="clearfix"></div>
	<br>

	<?php 
	$table_data = array(
		_l('hr_name_procedure_retire'),
		_l('hr_department'),
		_l('hr_datecreator'),
	);
	render_datatable($table_data,'table_procedure_retire');
	?> 


	<div class="modal" id="add_procedure_retire_manage" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg">
			<?php echo form_open(admin_url('hr_profile/add_procedure_form_manage'),  array('id'=>'add_procedure_form_manage')); ?>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title add"><?php echo _l('hr_add_procedure_retire'); ?></h4>
					<h4 class="modal-title edit"><?php echo _l('hr_edit_procedure_retire'); ?></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<input type="hidden" name="id" value="">
						<div class="col-md-6">
							<div class="form-group">
								<?php echo render_input('name_procedure_retire','hr_name_procedure_retire'); ?>
							</div>            
						</div>
						<div class="col-md-6">
							<div class="form-group select-placeholder department_add_edit">
								<label for="departmentid" class="control-label"><small class="req text-danger">* </small><?php echo _l('hr_department'); ?></label>
								<select name="departmentid[]" id="departmentid" multiple="true" class="form-control selectpicker" data-actions-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
									<?php foreach ($department as $d) { ?>
										<option value="<?php echo html_entity_decode($d['departmentid'])?>"><?php echo html_entity_decode($d['name']); ?></option>
									<?php } ?>
								</select>
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
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</div>

</body>
</html>
