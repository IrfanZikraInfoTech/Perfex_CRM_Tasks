<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
	<div class="_buttons">
		<?php if(has_permission('hrm_setting', '', 'create') || is_admin() ){ ?>
			<a href="#" onclick="new_type_of_training(); return false;" class="btn btn-info pull-left display-block">
				<?php echo _l('hr_hr_add'); ?>
			</a>
		<?php } ?>
	</div>
	<div class="clearfix"></div>
	<br>

	<table class="table dt-table">
		<thead>
			<th><?php echo _l('type_of_training_name'); ?></th>
			<th><?php echo _l('options'); ?></th>
		</thead>
		<tbody>
			<?php foreach($type_of_trainings as $type_of_training){ ?>
				<tr>
					<td><?php echo html_entity_decode($type_of_training['name']); ?></td>
					<td>

						<?php if(has_permission('hrm_setting', '', 'edit') || is_admin() ){ ?>
							<a href="#" onclick="edit_type_of_training(this,<?php echo html_entity_decode($type_of_training['id']); ?>); return false" data-name="<?php echo html_entity_decode($type_of_training['name']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
						<?php } ?>

						<?php if(has_permission('hrm_setting', '', 'delete') || is_admin() ){ ?>
							<a href="<?php echo admin_url('hr_profile/delete_type_of_training/'.$type_of_training['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>  


	<div class="modal" id="type_of_training" tabindex="-1" role="dialog">
		<div class="modal-dialog w-25">
			<?php echo form_open(admin_url('hr_profile/type_of_training'),  array('id'=>'add_type_of_training')); ?>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<span class="edit-title"><?php echo _l('hr_edit_type_of_training'); ?></span>
						<span class="add-title"><?php echo _l('hr_new_type_of_training'); ?></span>
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div id="additional_type_of_training"></div>   
							<div class="form">     
								<?php 
								echo render_input('name','type_of_training_name'); ?>

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
