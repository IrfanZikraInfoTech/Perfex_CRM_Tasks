<?php echo form_hidden('display', 'history'); ?>
<?php echo form_hidden('id', $id); ?>
 
<div class="col-md-12 padding-with-table history">
	<?php
	    $table_data = array(
	        _l('recently_checkin'),
	        _l('upcoming_checkin'),
	        _l('status'),
	        _l('details'),
	        );
	    render_datatable($table_data,'history');
	?>
</div>