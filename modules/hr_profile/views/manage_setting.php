<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
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
			<div class="col-md-3">
				<ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked">
					<?php
					$i = 0;
					foreach($tab as $group_item){
						?>
						<li<?php if($group_item == $group){echo " class='active'"; } ?>>
						<a href="<?php echo admin_url('hr_profile/setting?group='.$group_item); ?>" data-group="<?php echo html_entity_decode($group_item); ?>">

							<?php
							if($group_item == 'workplace'){
							 echo _l('hr_hr_workplace');
							}elseif($group_item == 'salary_type'){
							 echo _l('hr_salary_type');
							}elseif($group_item == 'procedure_retire'){
							 echo _l('hr_procedure_retire');
							}elseif($group_item == 'type_of_training'){
							 echo _l('hr_type_of_training');
							}elseif($group_item == 'reception_staff'){
							 echo _l('hr_reception_staff');
							}elseif($group_item == 'hr_profile_permissions'){
							 echo _l('hr_hr_profile_permissions');
							}elseif($group_item == 'prefix_number'){
							 echo _l('hr_prefix_number');
							}elseif($group_item == 'allowance_type'){
							 echo _l('hr_allowance_type');
							}else{
							 echo _l($group_item);
							}
							  ?>
						</a>
					</li>
				<?php } ?>
			</ul>
		</div>
		<div class="col-md-9">
			<div class="panel_s">
				<div class="panel-body">

					<?php $this->load->view($tabs['view']); ?>

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
<?php
$viewuri = $_SERVER['REQUEST_URI'];

require('modules/hr_profile/assets/js/setting/manage_setting_js.php');
if($group == 'reception_staff'){
	require('modules/hr_profile/assets/js/setting/reception_staff_js.php');
}elseif(!(strpos($viewuri,'admin/hr_profile/setting?group=hr_profile_permissions') === false)){
	require('modules/hr_profile/assets/js/setting/hr_profile_permissions_js.php');
}
hooks()->do_action('settings_tab_footer', $tab); ?>
</body>
</html>
