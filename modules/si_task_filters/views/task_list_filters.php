<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<h4 class="pull-left"><?php echo _l('custom_reports')." - "._l('filter_templates'); ?></h4>
						<a href="tasks_report" class=" pull-right btn btn-info mleft4"><?php echo _l('add_task_filter'); ?></a>
						<div class="clearfix"></div>
						<hr />
						<table class="table dt-table scroll-responsive">
							<thead>
								<tr>
									<th width="5%">#</th>
									<th><?php echo _l('filter_name'); ?></th>
								</tr>
							</thead>
							<tbody>
							<?php
							if(!empty($filter_templates)){
								$i=1;
								foreach($filter_templates as $row){?>
								<tr class="has-row-options">
									<td><?php echo $i++;?></td>
									<td data-order="<?php echo $row['filter_name']; ?>">
										<a href="tasks_report/?filter_id=<?php echo $row['id'];?>"><?php echo $row['filter_name']; ?></a>
										<div class="row-options">
										<a href="tasks_report/?filter_id=<?php echo $row['id'];?>"><?php echo _l('edit');?></a> | <a href="del_task_filter/<?php echo $row['id'];?>" class="confirm text-danger"><?php echo _l('delete');?></a>
										</div>
									</td>
								</tr>
								<?php } }?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>
<script>
(function($) {
"use strict";
	$('.confirm').on('click',function(){
		return confirm("<?php echo _l('delete_confirm');?>");
	});
})(jQuery);	 	
</script>

