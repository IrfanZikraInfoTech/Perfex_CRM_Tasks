<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
	<div class="_buttons">
		<?php if(is_admin() || has_permission('hrm_setting','','create')){ ?>
			<a href="#" onclick="new_contract_type(); return false;" class="btn btn-info pull-left display-block">
				<?php echo _l('hr_hr_add'); ?>
			</a>
		<?php } ?>
	</div>
	<div class="clearfix"></div>
	<br>
	<table class="table dt-table">
		<thead>
			<th width="30%"><?php echo _l('hr_contract_name'); ?></th>
			<th><?php echo _l('hr_hr_description'); ?></th>
			<th><?php echo _l('options'); ?></th>
		</thead>
		<tbody>
			<?php foreach($contract as $c){ ?>
				<tr>
					<?php 
					/*get frist 400 character */
					if(strlen($c['description']) > 400){
						$pos=strpos($c['description'], ' ', 400);
						$description_sub = substr($c['description'],0,$pos ); 
					}else{
						$description_sub = $c['description'];
					}
					?>

					<td><?php echo html_entity_decode($c['name_contracttype']); ?></td>
					<td><?php echo html_entity_decode($description_sub); ?></td>
					<td>
						<?php if(is_admin() || has_permission('hrm_setting','','edit')){ ?>
							<a href="#" onclick="edit_contract_type(this,<?php echo html_entity_decode($c['id_contracttype']); ?>); return false"  class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
						<?php } ?>

						<?php if(is_admin() || has_permission('hrm_setting','','delete')){ ?>
							<a href="<?php echo admin_url('hr_profile/delete_contract_type/'.$c['id_contracttype']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
						<?php } ?>

					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>       
	<div class="modal" id="contract_type" tabindex="-1" role="dialog">
		<div class="modal-dialog w-25">
			<?php echo form_open(admin_url('hr_profile/contract_type'),  array('id'=>'add_contract_type')); ?>
			<div class="modal-content ">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<span class="edit-title"><?php echo _l('hr_edit_contract_type'); ?></span>
						<span class="add-title"><?php echo _l('hr_new_contract_type'); ?></span>
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div id="additional_contract_type"></div>   
							<div class="form">
								<div class="col-md-12">
									<?php 
									echo render_input('name_contracttype','name'); ?>
								</div>
								
								<div class="col-md-12">
									<p class="bold"><?php echo _l('hr_hr_description'); ?></p>
									<?php echo render_textarea('description','','',array(),array(),'','tinymce'); ?>
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
