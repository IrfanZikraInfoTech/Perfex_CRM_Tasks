<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
	<div class="_buttons">
		<?php if(has_permission('hrm_setting', '', 'create') || is_admin() ){ ?>
			<a href="#" onclick="new_allowance_type(); return false;" class="btn btn-info pull-left display-block">
				<?php echo _l('hr_hr_add'); ?>
			</a>
		<?php } ?>
	</div>
	<div class="clearfix"></div>
	<br>
	<table class="table dt-table">
		<thead>
			<th><?php echo _l('hr_allowance_type_name'); ?></th>
			<th><?php echo _l('amount'); ?></th>
			<th><?php echo _l('hr_taxable'); ?></th>
			<th><?php echo _l('options'); ?></th>
		</thead>
		<tbody>
			<?php foreach($allowance_type as $c){ ?>
				<tr>
					<td><?php echo html_entity_decode($c['type_name']); ?></td>
					<td><?php echo app_format_money($c['allowance_val'],''); ?></td>
					<td><?php if($c['taxable'] == 0){echo _l('hr_hr_no');}else{echo _l('hr_yes');}?></td>
					<td>
						<?php if(has_permission('hrm_setting', '', 'edit') || is_admin()){ ?>
							<a href="#" onclick="edit_allowance_type(this,<?php echo html_entity_decode($c['type_id']); ?>); return false" data-taxable="<?php echo html_entity_decode($c['taxable']); ?>" data-name="<?php echo html_entity_decode($c['type_name']); ?>" data-amount="<?php echo app_format_money($c['allowance_val'], ''); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
						<?php } ?>

						<?php if(has_permission('hrm_setting', '', 'delete') || is_admin()){ ?>
							<a href="<?php echo admin_url('hr_profile/delete_allowance_type/'.$c['type_id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>      
	
	<div class="modal" id="allowance_type" tabindex="-1" role="dialog"  aria-hidden="true">
		<div class="modal-dialog w-25">
			<?php echo form_open(admin_url('hr_profile/allowance_type')); ?>
			<div class="modal-content ">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<span class="edit-title"><?php echo _l('hr_edit_allowance_type'); ?></span>
						<span class="add-title"><?php echo _l('hr_new_allowance_type'); ?></span>
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div id="additional_allowance_type"></div>   
							<div class="form">     
								<?php 
								echo render_input('type_name','hr_allowance_type_name'); ?>
								<?php 
								$arrAtt = array();
								$arrAtt['data-type']='currency';
								echo render_input('allowance_val','hr_amount_of_money','','text',$arrAtt); ?> 

								<div class="form-group">
									<label for="taxable" class="control-label"><?php echo _l('hr_taxable'); ?></label>
									<select name="taxable" class="selectpicker" id="taxable" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
										<option value="0"><?php echo _l('hr_hr_no'); ?></option>
										<option value="1"><?php echo _l('hr_yes'); ?></option>
									</select>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
					<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
				</div>
			</div><!-- /.modal-content -->
			<?php echo form_close(); ?>
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</div>
</body>
</html>
