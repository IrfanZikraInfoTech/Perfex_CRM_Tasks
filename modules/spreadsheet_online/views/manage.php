<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="panel_s">
			<div class="panel-body">
				<h4 class="no-margin font-bold"><span class="glyphicon glyphicon-align-justify"></span> <?php echo html_entity_decode($title); ?>
				<?php if(is_admin()){ ?>
					<span class="label label-default cursor setting-sent-notifications"><i class="fa fa-cog" aria-hidden="true"></i></span>
				<?php } ?>
				</h4>

				<div class="clearfix"></div>
				<br>

				<div class="row">
					<div class="col-md-12">
						<div class="horizontal-scrollable-tabs preview-tabs-top">
							<div class="horizontal-tabs">
								<ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
									<li role="presentation" class="tab_cart <?php if($tab == 'my_folder'){ echo 'active'; } ?>">
										<a href="<?php echo admin_url('spreadsheet_online/manage?tab=my_folder'); ?>" aria-controls="tab_config" role="tab" aria-controls="tab_config">
											<?php echo _l('my_folder'); ?>
											
										</a>
									</li>
									
									<li role="presentation" class="tab_cart <?php if($tab == 'my_share_folder'){ echo 'active'; } ?>">
										<a href="<?php echo admin_url('spreadsheet_online/manage?tab=my_share_folder'); ?>" aria-controls="tab_config" role="tab" aria-controls="tab_config">
											<?php echo _l('my_share_folder'); ?>
										</a>
									</li>
								</ul>
							</div>
						</div> 
						<?php $this->load->view($tab); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="setting-sent-notifications" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?php echo _l('setting_notification') ?></h4>
			</div>
			<div class="modal-body">
				<?php echo form_open_multipart(admin_url('spreadsheet_online/spreadsheet_online_setting'),array('id'=>'spreadsheet-online-setting-form')) ?>
				<h4><?php echo _l('staff'); ?></h4>
				<div class="wrapper">
					<input id="spreadsheet_staff_notification" type="checkbox" name="spreadsheet_staff_notification" <?php if(get_option('spreadsheet_staff_notification') == 1){ echo 'checked'; }?> />
					<label for="spreadsheet_staff_notification"><?php echo _l('notifications') ?></label>
					<input id="spreadsheet_email_templates_staff" type="checkbox" name="spreadsheet_email_templates_staff" <?php if(get_option('spreadsheet_email_templates_staff') == 1){ echo 'checked'; }?> />
					<label for="spreadsheet_email_templates_staff"><?php echo _l('email_templates') ?></label>
					
					<div data-tooltip="<?php echo _l('ss_guide_email_template') ?>" data-tooltip-location="up"><i class="fa fa-question-circle"></i></div>

				</div>

				<h4><?php echo _l('client'); ?></h4>
				<div class="wrapper">
					<input id="spreadsheet_client_notification" type="checkbox" name="spreadsheet_client_notification" <?php if(get_option('spreadsheet_client_notification') == 1){ echo 'checked'; }?> />
					<label for="spreadsheet_client_notification"><?php echo _l('notifications') ?></label>
					<input id="spreadsheet_email_templates_client" type="checkbox" name="spreadsheet_email_templates_client" <?php if(get_option('spreadsheet_email_templates_client') == 1){ echo 'checked'; }?>/>
					<label for="spreadsheet_email_templates_client"><?php echo _l('email_templates') ?></label>
					<div data-tooltip="<?php echo _l('ss_guide_email_template') ?>" data-tooltip-location="up"><i class="fa fa-question-circle"></i></div>
					
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default test_class" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
			</div>
		</div>
		<?php echo form_close(); ?>   
	</div>
</div>
<?php init_tail(); ?>
<?php require 'modules/spreadsheet_online/assets/js/manage_js.php'; ?>
</body>
</html>