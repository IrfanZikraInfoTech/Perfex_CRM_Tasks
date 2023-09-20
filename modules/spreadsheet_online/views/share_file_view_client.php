<?php echo form_open_multipart(site_url('spreadsheet_online/spreadsheet_online_client/file_view_share'),array('id'=>'spreadsheet-test-form'));?>
<div id="luckysheet"></div>
<?php echo form_hidden('parent_id', $parent_id);  ?>
<?php echo form_hidden('id', isset($id) ? $id : "");  ?>
<?php echo form_hidden('client_screen', "true");  ?>
<?php echo form_close(); ?>  

<?php echo form_hidden('type', 3);  ?>
<?php echo form_hidden('role', $role);  ?>

<div class="modal fade" id="SaveAsModal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title add-new"><?php echo _l('save_as') ?></h4>
			</div>

			<div class="modal-body">
				<?php echo render_select('folder',$folder,array('id','name'),'','',array()); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
			</div>
		</div>
	</div>
</div>
<?php hooks()->do_action('client_pt_footer_js'); ?>

<?php require 'modules/spreadsheet_online/assets/js/new_file_js.php'; ?>
