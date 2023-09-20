<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title">
		<span class="edit-title pl-4"><?php echo html_entity_decode($staff->firstname.' '.$staff->lastname); ?></span>
	</h4>
</div>
<div class="modal-body p-3">
	<div class="row">
		<div class="col-md-12">
			
			<table class="table border table-striped ">
				<tbody>
					<tr class="project-overview">
						<td class="bold" width="30%"><?php echo _l('hr_employee_code'); ?></td>
						<td><?php echo (isset($staff->staff_identifi) || $staff->staff_identifi != '' ? $staff->staff_identifi : '...'); ?></td>
					</tr>
					<tr class="project-overview">
						<td class="bold"><?php echo _l('hr_sex'); ?></td>
						<td><?php echo _l($staff->sex);; ?></td>
					</tr>
					<tr class="project-overview">
						<td class="bold">Email</td>
						<td><?php echo html_entity_decode($staff->email); ?></td>
					</tr>
					<tr class="project-overview">
						<td class="bold"><?php echo _l('staff_add_edit_phonenumber'); ?></td>
						<td><?php echo html_entity_decode($staff->phonenumber); ?></td>
					</tr>
					<tr class="project-overview">
						<td class="bold"><?php echo _l('hr_hr_job_position'); ?></td>
						<td><?php echo (isset($position->position_name) ? $position->position_name : '...'); ?></td>
					</tr>

					<tr class="project-overview">
						<td class="bold"><?php echo _l('hr_department'); ?></td>
						<td><?php echo (isset($department->name) ? $department->name : '...'); ?></td>
					</tr>

				</tbody>
			</table>

		</div>
	</div>
	<?php 
	$count_id = 0;
	foreach ($group_checklist as $value) { 
		$count_id += 1;
		?>
		<div class="row">
			<div class="col-md-6">
				<h4 class="text-primary mt-0"><i class="fa fa-info-circle"></i> <?php echo html_entity_decode($value['group_name']); ?></h4>
			</div>
			<div class="col-md-6">
				<div class="progress-bar bg-green task-progress-bar-ins-427 pull-right" name="progress[<?php echo html_entity_decode($count_id); ?>]">0%</div>
			</div>
		</div>	
		<hr class="mt-0 mb-0">				 
		<?php 
		$ckecklist = $this->hr_profile_model->get_checklist_allocation_by_group_id($value['id']);
		foreach ($ckecklist as $key => $sub) { ?>
			<div class="row">
				<div class="col-md-12">	
					<div class="checkbox">
						<input data-can-view="" type="checkbox" onclick="change_info_checklist(this);" class="capability" id="<?php echo html_entity_decode($sub['name']) ?>" name="subitem[<?php echo html_entity_decode($count_id); ?>]" data-id="<?php echo html_entity_decode($sub['id']); ?>" data-count="<?php echo html_entity_decode($count_id); ?>" value="<?php echo html_entity_decode($sub['status']); ?>" <?php if((int)$sub['status'] == 1){
							echo 'checked';
						} ?>>
						<label for="<?php echo html_entity_decode($sub['name']) ?>">
							<?php echo html_entity_decode($sub['name']); ?>							
						</label>
					</div>
				</div>
			</div>
		<?php } ?>

		<?php
	} ?>


	<?php 
	if(isset($list_staff_asset)){
		if($list_staff_asset){ ?>
			<br>

			<div class="row">
				<div class="col-md-6">
					<h4 class="text-primary mt-0"><i class="fa fa-info-circle"></i> <?php echo _l('hr_property_allocation'); ?></h4>
				</div>
				<div class="col-md-6">
					<div class="progress-bar bg-green bg-danger task-progress-bar-ins-427 pull-right" id="asset_staff">0%</div>
				</div>
			</div>	
			<hr class="mt-0 mb-0">	

			<div id="asset_list">	    
				<?php 
				foreach ($list_staff_asset as $key => $value) { ?>
					<div class="row item_hover">
						<div class="col-md-7">	
							<div class="checkbox">
								<input data-can-view="" type="checkbox" class="capability" name="asset_staff[]" id="<?php echo html_entity_decode($value['asset_name']); ?>	" data-id="<?php echo html_entity_decode($value['allocation_id']); ?>" value="<?php echo html_entity_decode($value['status_allocation']); ?>" <?php if($value['status_allocation'] == 1){ echo 'checked'; } ?> onclick="active_asset(this);">
								<label for="<?php echo html_entity_decode($value['asset_name']); ?>	">
									<?php echo html_entity_decode($value['asset_name']); ?>					
								</label>
							</div>
						</div>
						<div class="col-md-3 pt-10">
							<a href="#" class="text-danger" onclick="delete_asset(this);"  data-id="<?php echo html_entity_decode($value['allocation_id']); ?>" ><?php echo _l('delete'); ?></a>
						</div>
					</div>
				<?php } ?>
			</div>
			<div id="add_asset"></div>
			<div class="row">	
				<br>	
				<div class="col-md-12">	
					<button class="btn text-info shadow-none" onclick="gen_input_asset();"><i class="fa fa-plus"></i></button>		
					<button class="btn btn-info shadow-none float-right d-none btn_save_add_asset" onclick="save_add_asset();"><?php echo _l('save'); ?></button>
				</div>				
			</div>				
		<?php }	} ?>

		<?php if(isset($list_training)){
			if($list_training){
				?>
				<br>
				<div class="row">
					<div class="col-md-6">
						<h4 class="text-primary mt-0"><i class="fa fa-info-circle"></i> <?php echo _l('hr_training'); ?></h4>
					</div>
					<div class="col-md-6">
						<?php if(isset($complete)){ ?>
						<?php if($complete == 1){ ?>
						<div class="progress-bar bg-green bg-danger task-progress-bar-ins-427 pull-right" id="training_staff">0%</div>
					<?php }else{ ?>
						<div class="progress-bar task-progress-bar-ins-427 pull-right bg-green" id="asset_staff" style="width: 100%; color: rgb(255, 255, 255); margin: unset;">100%</div>
					<?php } }else{?>
						<div class="progress-bar bg-green bg-danger task-progress-bar-ins-427 pull-right" id="training_staff">0%</div>

					<?php } ?>
					</div>
				</div>	
				<hr class="mt-0 mb-0">	
				<div class="row mt-4 p-3 panel">
					<div class="col-md-12">
						<?php 
						if(isset($list_training_allocation)){
							echo html_entity_decode($list_training_allocation->training_name) .' ( ';
						} 
						 ?>

						<?php
						echo get_type_of_training_by_id($list_training_allocation->training_type);
						

						echo ': '.html_entity_decode($training_program_point) .'/'.html_entity_decode($training_allocation_min_point) .' )';
						?>	
					</div>			
				</div>
				<?php 
				$check = '';
				$mark = 0;

				if(isset($staff_training_result)){

				foreach ($staff_training_result as $key => $value) {

					?>
					<div class="row border p-0 mt-4 mb-4">
						<div class="col-md-11 pt-3">
							<div class="w-100">
								<div class="col-md-10"><?php echo html_entity_decode($value['training_name']); ?></div>					  	
								<div class="col-md-2 border float-right">
									<?php echo _l('hr_point') ?> : <?php echo html_entity_decode($value['total_point']); ?>
								</div>
							</div>				
						</div>
						<div class="col-md-1 pt-1">					 
							<div class="checkbox float-right">
								
							</div>
						</div>
					</div>

				<?php }} ?>

			<?php }} ?>

		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default reception_staff_close_model" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
		</div>
		<?php 
		require('modules/hr_profile/assets/js/reception_staff/reception_staff_js.php');
		?>