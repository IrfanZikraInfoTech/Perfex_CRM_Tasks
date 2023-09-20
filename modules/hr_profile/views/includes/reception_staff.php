<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo  form_open(admin_url('hr_profile/save_setting_reception_staff'),['id' => 'reception_staff_form']); ?>
<div class="col-md-12">
	<h4><?php echo _l('hr_reception_information'); ?></h4>
	<hr>
	<div class="col-md-12">
		<div class="col-md-12" id="manage_reception">

			<?php 
			if (count($group_checklist)>0) {
				foreach ($group_checklist as $key => $value) {?>                 
					<div class="row title">                           
						<div class="col-md-11 pt-2">
							<div class="form-group">
								<input type="text" name="title_name[<?php echo html_entity_decode($key); ?>]" class="form-control" placeholder="<?php echo _l('hr_title'); ?>" value="<?php echo html_entity_decode($value['group_name']); ?>">
							</div>
						</div>
						<div class="col-md-1 pl-0 pt-0" name="button_add">
							<?php 
							if($key == 0){ ?>
								<button onclick="add_title(this); return false;" class="btn btn-primary mt-1" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
							<?php }else{?>
								<button onclick="remove_title(this); return false;" class="btn btn-danger mt-1" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
							<?php } ?>
						</div>
						<?php 

						$checklist = $this->hr_profile_model->checklist_by_group($value['id']);
						
						foreach ($checklist as $ind => $sub_item) {?>
							<div class="col-md-12 pl-0">
								<div class="sub">                           
									<div class="col-md-10 pt-2">
										<div class="form-group">
											<input type="text" name="sub_title_name[<?php echo html_entity_decode($key); ?>][<?php echo html_entity_decode($ind); ?>]" data-count="<?php echo html_entity_decode($key); ?>" class="form-control" value="<?php echo html_entity_decode($sub_item['name']); ?>" placeholder="<?php echo _l('hr_sub_title'); ?>">
										</div>
									</div>
									<div class="col-md-2 pl-0 pt-0" name="button_add">
										<?php 
										if($ind == 0){ ?>
											<button onclick="add_subtitle(this); return false;" class="btn btn-primary mt-1" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
										<?php }else{?>
											<button onclick="remove_subtitle(this); return false;" class="btn btn-danger mt-1" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
										<?php } ?>
									</div>
								</div>
							</div>
						<?php  } ?>                   
						<div class="col-md-12 pl-0 sub_title"></div>
					</div>
					<?php 
				}}else{?>            
					<div class="row title">                           
						<div class="col-md-11 pt-2">
							<div class="form-group">
								<input type="text" name="title_name[0]" class="form-control" placeholder="<?php echo _l('hr_title'); ?>" value="">
							</div>
						</div>
						<div class="col-md-1 pl-0 pt-0" name="button_add">
							<button onclick="add_title(this); return false;" class="btn btn-primary mt-1" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
						</div>

						<div class="col-md-12 pl-0">
							<div class="sub">                           
								<div class="col-md-10 pt-2">
									<div class="form-group">
										<input type="text" name="sub_title_name[0][0]" data-count="0" class="form-control" value="" placeholder="<?php echo _l('hr_sub_title'); ?>">
									</div>
								</div>
								<div class="col-md-2 pl-0 pt-0" name="button_add">
									<button onclick="add_subtitle(this); return false;" class="btn btn-primary mt-1" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
								</div>
							</div>
						</div>
						<div class="col-md-12 pl-0 sub_title"></div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="col-md-12">
		<br>
		<h4><?php echo _l('hr_property_allocation'); ?></h4>
		<hr>
		<div class="col-md-12">
			<!-- 	Add assets    -->
			<div class="col-md-12 assets_wrap">
				<?php if($list_reception_staff_asset){
					foreach ($list_reception_staff_asset as $p_key => $p_value) {              
						?>
						<div id ="assets_emp" class="row">                            
							<div class="col-md-11 pt-2">
								<div class="form-group">
									<?php
									$name=$p_value['name'];

									?>
									<input type="text" name="asset_name[]" class="form-control" value="<?php echo html_entity_decode($name); ?>" placeholder="<?php echo _l('hr_enter_property_name'); ?>" >
								</div>
							</div>                            
							<div class="col-md-1 pl-0 pt-0" name="button_add">
								<button name="add_asset" class="btn mt-1 <?php if($p_key == 0){ echo 'new_assets_emp btn-primary' ;}else{echo 'remove_assets_emp btn-danger' ;} ?>  " data-ticket="true" type="button"><i class="fa <?php if($p_key == 0){ echo 'fa-plus' ;}else{ echo 'fa-minus' ;} ?> "></i></button>
							</div>
						</div>
					<?php } ?>
				<?php }else{ ?>
					<div id ="assets_emp" class="row">                           
						<div class="col-md-11 pt-2">
							<div class="form-group">                
								<input type="text" name="asset_name[]" class="form-control" value="" placeholder="<?php echo _l('hr_enter_property_name'); ?>" >
							</div>
						</div>
						<div class="col-md-1 pl-0 pt-0" name="button_add">
							<button name="add_asset" class="btn new_assets_emp btn-primary mt-1" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
						</div>
					</div>
				<?php } ?>
			</div>
			<!-- 	End add assets    -->
		</div>
	</div>
	<div class="col-md-12">
		<br>
		<h4><?php echo _l('hr_training'); ?></h4>
		<hr>
		<div class="col-md-12" id="training_wrap">      
			<div class="row">
				<div class="check col-md-12">
					<div class="col-md-12">
						<div class="form-group mt-2">
							<select name="training_type" class="selectpicker" id="training_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
								<option value=""></option> 
								<?php foreach ($type_of_trainings as $key => $value) { ?>

									<option value="<?php echo $value['id']; ?>" <?php if(isset($setting_training) && $setting_training->training_type == $value['id'] ){echo 'selected';}; ?> ><?php echo $value['name'] ?></option>

								<?php } ?>
								
							</select>
						</div>
					</div>
					<div id="list_check"></div>
				</div>        
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
	<hr class="mb-4 pb-1">
	<?php if(has_permission('hrm_setting','','create') || has_permission('hrm_setting','','edit') ){ ?>
		<button class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
	<?php } ?>
	<?php echo form_close(); ?>

