<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
   <div class="panel_s">
    <div class="panel-body">
	    	<div class="clearfix"></div>
		    	<div class="col-md-12">
				 	<div class="horizontal-scrollable-tabs preview-tabs-top">
					  <div class="horizontal-tabs">
					  	<ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
					      <li role="presentation" class="tab_cart <?php if($tab == 'checkin'){ echo 'active'; } ?>">
					         <a href="<?php echo admin_url('okr/checkin_detailt/'.$id.'?tab=checkin'); ?>" aria-controls="tab_config" role="tab" aria-controls="tab_config">
					         		<?php echo _l('checkin'); ?>
					         </a>
					      </li>

					      <li role="presentation">
					      	<a href="#tab_tasks" onclick="init_rel_tasks_table(<?php echo html_entity_decode($id); ?>,'okrs'); return false;" aria-controls="tab_tasks" role="tab" data-toggle="tab" data-id="<?php echo html_entity_decode($id); ?>" class="task-func"><?php echo _l('tasks'); ?></a>
					      </li>

					      <li role="presentation" class="tab_cart <?php if($tab == 'history'){ echo 'active'; } ?>">
					         <a href="<?php echo admin_url('okr/checkin_detailt/'.$id.'?tab=history'); ?>" aria-controls="tab1" role="tab" aria-controls="tab2">
					         		<?php echo _l('history'); ?>
					         </a>
					      </li>

					  	</ul>
					  </div>
					</div> 
					<?php $this->load->view('checkin/'.$tab); ?>
				    <div class="tab-content content-tabpanel">
				    	<div role="tabpanel" class="tab-pane" id="tab_tasks">
				 			<?php init_relation_tasks_table(array('data-new-rel-id'=>$id,'data-new-rel-type'=>'okrs')); ?>
				        </div>
				    </div>
				</div>
	  </div>
	</div>
  </div>
 </div>
</div>
<?php init_tail(); ?>
<?php require 'modules/okr/assets/js/file_js_checkin_detailt.php';?>
</body>
</html>
