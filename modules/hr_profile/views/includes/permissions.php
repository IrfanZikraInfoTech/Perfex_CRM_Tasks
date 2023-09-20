<div class="modal fade" id="appointmentModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<?php 
				$title = '';
				$staffid = '';
				if(isset($member)){
					$title .= _l('hr_update_permissions');
					$staffid    = $member->staffid;
				}else{
					$title .= _l('hr_add_permissions');
				}
				?>
				<h4 class="modal-title"><?php echo html_entity_decode($title); ?></h4>
			</div>
			<?php echo form_open(admin_url('hr_profile/hr_profile_update_permissions/'.$staffid), array('id' => 'update_permissions')); ?>
			<div class="modal-body">


				<div class="table-responsive">
					<div id="additional_staff_permissions"></div>

					<div class="hide">
						<?php
						$isadmin = '';
						if(isset($member) && ($member->staffid == get_staff_user_id() || is_admin($member->staffid))) {
							$isadmin = ' checked';
						}
						?>
						<input type="checkbox" name="administrator" id="administrator" <?php echo html_entity_decode($isadmin); ?>>
					</div>

					<?php 
					$selected = '';
					foreach($roles_value as $role_value){
						if(isset($member)){
							if($member->role == $role_value['roleid']){
								$selected = $role_value['roleid'];
							}
						} 
					}
					?>
					<div class="class">
						<?php echo render_select('role',$roles_value,array('roleid','name'),'staff_add_edit_role',$selected); ?>
					</div>

					<?php if(isset($member)){ 
						$staff_attr=[];
						$staff_attr['disabled'] = true;
						?>
						<div class="lable-display-name">
							<?php echo render_input('staff_name', 'hr_hr_staff_name', get_staff_full_name($member->staffid), '', $staff_attr); ?>

						</div>
					<?php } ?>

					<?php 
					$staff_selected = '';
					if(isset($member)){
						$staff_selected = $member->staffid;
					}
					?>

					<div class="class <?php  echo html_entity_decode($display_staff); ?>">
						<?php echo render_select('staff_id',$staffs,array('staffid',array('firstname', 'lastname')),'hr_hr_staff_name',$staff_selected); ?>
					</div>

					<table class="table table-bordered roles no-margin">
						<thead>
							<tr>
								<th>Feature</th>
								<th>Capabilities</th>
							</tr>
						</thead>
						<tbody>
							<?php
							if(isset($member)){
								$is_admin = is_admin($member->staffid);
							}

							$hr_profile_permissions = list_hr_profile_permisstion();

							foreach(get_available_staff_permissions($funcData) as $feature => $permission) { ?>
								<tr data-name="<?php echo html_entity_decode($feature); ?>" class="<?php if(!in_array($feature, $hr_profile_permissions)){echo "hide";} ?>">
									<td>
										<b><?php echo html_entity_decode($permission['name']); ?></b>
									</td>
									<td>
										<?php
										if(isset($permission['before'])){
											echo html_entity_decode($permission['before']);
										}
										?>
										<?php foreach ($permission['capabilities'] as $capability => $name) {
											$checked = '';
											$disabled = '';
											if((isset($is_admin) && $is_admin) ||
												(is_array($name) && isset($name['not_applicable']) && $name['not_applicable']) ||
												(
													($capability == 'view_own' || $capability == 'view'
														&& array_key_exists('view_own', $permission['capabilities']) && array_key_exists('view', $permission['capabilities']))
													&&
													((isset($member)
														&& staff_can(($capability == 'view' ? 'view_own' : 'view'), $feature, $member->staffid))
													||
													(isset($role)
														&& has_role_permission($role->roleid, ($capability == 'view' ? 'view_own' : 'view'), $feature))
												)
												)
											){
												$disabled = ' disabled ';
										} else if((isset($member) && staff_can($capability, $feature, $member->staffid))
											|| isset($role) && has_role_permission($role->roleid, $capability, $feature)){
											$checked = ' checked ';
										}
										?>
										<div class="checkbox">
											<input
											<?php if($capability == 'view') { ?> data-can-view <?php } ?>
											<?php if($capability == 'view_own') { ?> data-can-view-own <?php } ?>
											<?php if(is_array($name) && isset($name['not_applicable']) && $name['not_applicable']){ ?> data-not-applicable="true" <?php } ?>
											type="checkbox"
											<?php echo html_entity_decode($checked);?>
											class="capability"
											id="<?php echo html_entity_decode($feature .'_'.$capability); ?>"
											name="permissions[<?php echo html_entity_decode($feature); ?>][]"
											value="<?php echo html_entity_decode($capability); ?>"
											<?php echo html_entity_decode($disabled); ?>>
											<label for="<?php echo html_entity_decode($feature .'_'.$capability); ?>">
												<?php echo !is_array($name) ? $name : $name['name']; ?>
											</label>
											<?php
											if(isset($permission['help']) && array_key_exists($capability, $permission['help'])) {
												echo '<i class="fa fa-question-circle" data-toggle="tooltip" data-title="'.$permission['help'][$capability].'"></i>';
											}
											?>
										</div>
									<?php } ?>
									<?php
									if(isset($permission['after'])){
										echo html_entity_decode($permission['after']);
									}
									?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>

		</div>

		<div class="modal-footer">
			<button type="button" class="btn btn-default close_btn" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
			<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>
</div>
<?php require('modules/hr_profile/assets/js/setting/permissions_js.php'); ?>