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
							<a href="<?php echo admin_url('hr_profile/training?group='.$group_item); ?>" data-group="<?php echo html_entity_decode($group_item); ?>">

								<?php
								if($group_item == 'training_library'){
									echo _l('hr__training_library');
								}elseif($group_item == 'training_program'){
									echo _l('hr__training_program');
								}elseif($group_item == 'training_result'){
									echo _l('hr_training_result');
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
</div>
</div>
<?php init_tail(); ?>

<?php hooks()->do_action('settings_tab_footer', $tab); ?>

<?php 
$viewuri = $_SERVER['REQUEST_URI'];
if(!(strpos($viewuri,'admin/hr_profile/training?group=training_program') === false) ){
	require('modules/hr_profile/assets/js/training/training_program_js.php');
}elseif(!(strpos($viewuri,'admin/hr_profile/training?group=training_result') === false)){
	require('modules/hr_profile/assets/js/training/training_result_js.php');

}
	require('modules/hr_profile/assets/js/training/training_program_js.php');

?>
</body>
</html>
