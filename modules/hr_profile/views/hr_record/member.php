<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $current_year = date('Y'); ?>
<?php echo form_hidden('isedit'); ?>
<?php echo form_hidden('memberid',$staffid); ?>
<?php echo form_hidden('memberid[]',$staffid); ?>
<?php echo form_hidden('curren_year',$current_year); ?>
<?php echo form_hidden('member_id',$staffid); ?>
<?php echo form_hidden('member_view',1); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php if($this->session->flashdata('debug')){ ?>
				<div class="col-lg-12">
					<div class="alert alert-warning">
						<?php echo html_entity_decode($this->session->flashdata('debug')); ?>
					</div>
				</div>
			<?php } ?>
			<?php if($hr_profile_member_add == false){ ?>
				<div class="col-md-3">
					<div class="panel_s mbot5">
						<div class="panel-body padding-10">
							<h4 class="text-center text-uppercase">
								<?php echo html_entity_decode($member->firstname.' '.$member->lastname.' '.(($member->staff_identifi != '') ? ' - '.$member->staff_identifi : '')); ?>
							</h4>
						</div>
					</div>


					<ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked">
						<?php
						$i = 0;
						foreach($tab as $group_item){
							?>
							<li<?php if($group_item == $group){echo " class='active'"; } ?>>
							<a href="<?php echo admin_url('hr_profile/member/'.$staffid.'/'.$group_item); ?>" data-group="<?php echo html_entity_decode($group_item); ?>">
								<?php
								$icon['profile'] = '<span class="glyphicon glyphicon-user"></span>';
								$icon['contract'] = '<span class="glyphicon glyphicon-file"></span>';
								$icon['dependent_person'] = '<span class="fa fa-group menu-icon"></span>';
								$icon['training'] = '<span class="fa fa-graduation-cap fa-fw fa-lg"></span>';
								$icon['bonus_discipline'] = '<span class="fa fa-bell-o fa-fw fa-lg"></span>';
								$icon['attach'] = '<span class="fa fa-link"></span>';
								$icon['staff_project'] = '<span class="fa fa-bars menu-icon"></span>';
								$icon['other'] = '<span class="fa fa-tasks menu-icon"></span>';

								if($group_item == 'profile'){
									echo html_entity_decode($icon[$group_item] .' '. _l('hr_staff_profile')); 
								}elseif($group_item == 'training'){
									echo html_entity_decode($icon[$group_item] .' '. _l('hr_training')); 
								}elseif($group_item == 'attach'){
									echo html_entity_decode($icon[$group_item] .' '. _l('hr_attach')); 
								}elseif($group_item == 'dependent_person'){
									echo html_entity_decode($icon[$group_item] .' '. _l('hr_dependent_person')); 

								}elseif($group_item == 'contract'){
									echo html_entity_decode($icon[$group_item] .' '. _l($group_item)); 

								}elseif($group_item == 'staff_project'){
									echo html_entity_decode($icon[$group_item] .' '. _l($group_item)); 
								}else{
									$temp_icon = $icon['other'];
									$temp_icon = hooks()->apply_filters('hr_profile_load_icon', $temp_icon, $group_item);
									echo html_entity_decode($temp_icon .' '. _l($group_item)); 
								}
								?>
							</a>
						</li>
					<?php } ?>
				</ul>
			</div>
		<?php } ?>
		<div class="<?php if($hr_profile_member_add == false){ echo 'col-md-9'; }else{ echo 'col-md-12'; } ?>">
			<div class="row">
			<div class="panel_s">
				<div class="panel-body">
					<?php $this->load->view($tabs['view']); ?>
				</div>
			</div>
			</div>
		</div>

		<div class="clearfix"></div>
	</div>
	<?php echo form_close(); ?>
	<div class="btn-bottom-pusher"></div>
</div>
</div>
<div id="new_version"></div>
<?php init_tail(); ?>
<?php hooks()->do_action('hr_profile_load_js_file', $group); ?>

<?php 
if($group == 'contract'){
	require('modules/hr_profile/assets/js/hr_record/includes/contract_js.php');
}
if($group == 'training'){
	require('modules/hr_profile/assets/js/hr_record/includes/training_js.php');
}
if($group == 'notification'){
	require('modules/hr_profile/assets/js/hr_record/includes/notifications_js.php');
}
?>
<?php 
require('modules/hr_profile/assets/js/hr_record/add_update_staff_js.php');

?>
<script type="text/javascript">
	
	

</script>
</body>
</html>
