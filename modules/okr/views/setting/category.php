<div class="col-md-12">
	<?php if(has_permission('okr','','create') || is_admin()){ ?>
	<a href="#" onclick="add_setting_category(); return false;" class="btn btn-info pull-left">
    	<?php echo _l('add'); ?>
	</a>
	<?php } ?>
	
</div>
<div class="col-md-12 padding-with-table">
	<?php
	    $table_data = array(
	        _l('category'),
	        _l('option'),
	        );
	    render_datatable($table_data,'category');
	?>
</div>
<div class="modal fade" id="setting_category" tabindex="-1" role="dialog">
<?php echo form_open(admin_url('okr/setting_category'),array('id'=>'form_setting_category')); ?>             
	<div class="modal-dialog">
    	<div class="modal-content">
	        <div class="modal-header">
	            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	            <h4 class="modal-title">
	                <span class="add-title"><?php echo _l('add_setting_category'); ?></span>
	                <span class="update-title hide"><?php echo _l('update_setting_category'); ?></span>
	            </h4>
	        </div>
	        <div class="modal-body">
	          <?php echo form_hidden('id'); ?>
	          <?php echo render_input('category', 'category'); ?>
	        </div>
	        <div class="modal-footer">
	            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
	            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
	        </div>
      	</div>
    </div>
<?php echo form_close(); ?>                 
</div>