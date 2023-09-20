<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
	<div class="_buttons">
		<?php if(is_admin() || has_permission('hrm_setting','','create')) {?>
			<a href="#" onclick="new_workplace(); return false;" class="btn btn-info pull-left display-block">
				<?php echo _l('hr_hr_add'); ?>
			</a>
		<?php } ?>
	</div>
	<div class="clearfix"></div>
	<br>
	<table class="table dt-table">
		<thead>
			<th><?php echo _l('hr_hr_workplace'); ?></th>
			<th><?php echo _l('hr_workplace_address'); ?></th>
			<th><?php echo _l('hr_latitude_lable'); ?></th>
			<th><?php echo _l('hr_longitude_lable'); ?></th>
			<th><?php echo _l('options'); ?></th>
		</thead>
		<tbody>
			<?php foreach($workplace as $w){ ?>
				<tr>
					<td><?php echo html_entity_decode($w['name']); ?></td>
					<td><?php echo html_entity_decode($w['workplace_address']); ?></td>
					<td><?php echo html_entity_decode($w['latitude']); ?></td>
					<td><?php echo html_entity_decode($w['longitude']); ?></td>
					<td>
						<?php if(is_admin() || has_permission('hrm_setting','','edit')) {?>
							<a href="#" onclick="edit_workplace(this,<?php echo html_entity_decode($w['id']); ?>); return false" data-name="<?php echo html_entity_decode($w['name']); ?>" data-workplace_address="<?php echo html_entity_decode($w['workplace_address']); ?>" data-latitude="<?php echo html_entity_decode($w['latitude']); ?>" data-longitude="<?php echo html_entity_decode($w['longitude']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
						<?php } ?>

						<?php if(is_admin() || has_permission('hrm_setting','','delete')) {?>
							<a href="<?php echo admin_url('hr_profile/delete_workplace/'.$w['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>       
	<div class="modal" id="workplace" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<?php echo form_open(admin_url('hr_profile/workplace'), array('id' => 'add_workplace' )); ?>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<span class="edit-title"><?php echo _l('hr_edit_workplace'); ?></span>
						<span class="add-title"><?php echo _l('hr_new_workplace'); ?></span>
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div id="additional_workplace"></div>   
							<div class="form">     
								<?php 
								echo render_input('name','hr_hr_workplace'); ?>
							</div>
						</div>
						<div class="col-md-12">
							<?php echo render_textarea('workplace_address', 'hr_workplace_address') ?>
						</div>
						<div class="col-md-6">

							<?php echo render_input('latitude', 'hr_latitude_lable', '', 'number') ?>
						</div>
						<div class="col-md-6">
							<?php echo render_input('longitude', 'hr_longitude_lable', '', 'number') ?>
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
</div>

</body>
</html>
