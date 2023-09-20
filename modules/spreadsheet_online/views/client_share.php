<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php  hooks()->do_action('spreadsheet_online_client_head'); ?>
<div id="wrapper">
	<div class="content">
		<div class="panel_s">
			<div class="panel-body">
				<h4 class="no-margin font-bold"><span class="glyphicon glyphicon-align-justify"></span> <?php echo html_entity_decode($title); ?></h4>
				<div class="clearfix"></div>
				<br>
				<table id="spreadsheet-advanced">
					<thead>
						<tr>
							<th><?php echo _l('name') ?></th>
							<th><?php echo _l('kind') ?></th>
							<th><?php echo _l('size') ?></th>
						</tr>
					</thead>
					<?php echo html_entity_decode($folder_my_share_tree); ?>
				</table>
				<!--Creates the popup body-->
				<div class="popup-overlay">
					<!--Creates the popup content-->
					<div class="popup-content">
						<header role="banner">
							<nav class="nav-class" role="navigation">
								<ul class="nav__list button-group__mono-colors" data-share="true">
									<li class="select-option-choose" data-option="edit">
										<input id="group-1" type="checkbox" hidden />
										<label for="group-1"><span class="fa fa-angle-right"></span><i class="fa fa-crosshairs"></i> <?php echo _l('edit') ?></label>
									</li>
									<li class="select-option-choose" data-option="view">
										<input id="group-2" type="checkbox" hidden />
										<label for="group-2"><span class="fa fa-angle-right"></span><i class="fa fa-eye" aria-hidden="true"></i> <?php echo _l('view') ?></label>
									</li>
								</ul>
							</nav>
						</header>
					</div>
				</div>

				<!-- / The Context Menu -->
				<nav id="context-menu" class="context-menu" data-share="true">
					<ul class="context-menu__items">
						<li class="context-menu__item">
							<a href="#" class="context-menu__link" data-action="edit"><i class="fa fa-empire"></i> <?php echo _l('edit') ?></a>
						</li>
						<li class="context-menu__item">
							<a href="#" class="context-menu__link" data-action="view"><i class="fa fa-envira"></i> <?php echo _l('view') ?></a>
						</li>
					</ul>
				</nav>

				<div class="modal fade" id="AddFolderModal" role="dialog">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title add-new"><?php echo _l('add_new_folder') ?></h4>
								<h4 class="modal-title update-new hide"><?php echo _l('update_folder') ?></h4>
							</div>
							<?php echo form_open_multipart(site_url('spreadsheet_online/spreadsheet_online_client/add_edit_folder_client'),array('id'=>'add-edit-folder-form'));?>
							<?php echo form_hidden('id'); ?>
							<div class="modal-body">
								<div class="row">
									<div class="col-md-12 col-sm-12">
										<?php echo render_input('name', 'name_folder');?>
									</div>
								</div>
								<?php echo form_hidden('parent_id'); ?>				
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
								<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
							</div>
							<?php echo form_close(); ?>   
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php hooks()->do_action('client_pt_footer_js'); ?>
</body>
</html>
