<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
<div class="content">
<div class="panel_s">
<div class="panel-body">
    <div id="tab-okr-detail" class="row">
        <div class="">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#detail_content" aria-controls="detail_content" role="tab" data-toggle="tab"><?php echo _l('okrs'); ?></a></li>
                <li role="presentation"><a href="#tab_tasks" onclick="init_rel_tasks_table(<?php echo html_entity_decode($okrs_id); ?>,'okrs'); return false;" aria-controls="tab_tasks" role="tab" data-toggle="tab"><?php echo _l('tasks'); ?></a></li>
            </ul>
            <!-- Tab panes -->
    	</div>
    </div>
    <div class="tab-content content-tabpanel">
        <div role="tabpanel" class="tab-pane active" id="detail_content">
            <div class="row">
			  	<div class="col-md-4">
			    	<div class="col-md-12">
			    		<div class="head_view">
			    			<?php echo _l('your_target'); ?>
			    		<div class="pull-right">:</div>
			    		</div>
			    	</div>
			    	<div class="col-md-12">
			    		<div class="head_view">
			    			<?php echo _l('person_assigned'); ?>
			    		<div class="pull-right">:</div>
			    		</div>
			    	</div>
			    	<div class="col-md-12">
			    		<div class="head_view">
			    			<?php echo _l('status'); ?>
			    		<div class="pull-right">:</div>
			    		</div>
			    	</div>
			    	<div class="col-md-12">
			    		<div class="head_view">
			    			<?php echo _l('circulation'); ?>
			    		<div class="pull-right">:</div>
			    		</div>
			    	</div>
			    	<div class="col-md-12">
			    		<div class="head_view">
			    			<?php echo _l('display'); ?>
			    		<div class="pull-right">:</div>
			    		</div>
			    	</div>
			    	<div class="col-md-12">
			    		<div class="head_view">
			    			<?php echo _l('progress'); ?>
			    		<div class="pull-right">:</div>
			    		</div>
			    	</div>
			  	</div>
				<div class="col-md-8">
				    <div class="col-md-12">
			    		<div class="head_view">
			    			<?php echo html_entity_decode($your_target); ?>
			    		</div>
			    	</div>
			    	<div class="col-md-12">
			    		<div class="head_view">
			    			<?php echo html_entity_decode($person_assigned); ?>
			    		</div>
			    	</div>
			    	<div class="col-md-12">
			    		<div class="head_view">
			    			<?php echo html_entity_decode($status); ?>
			    		</div>
			    	</div>
			    	<div class="col-md-12">
			    		<div class="head_view">
			    			<?php echo html_entity_decode($circulation); ?>
			    		</div>
			    	</div>
			    	<div class="col-md-12">
			    		<div class="head_view">
			    			<?php echo html_entity_decode($display); ?>
			    		</div>
			    	</div>
			    	<div class="col-md-12">
			    		<div class="head_view">
			    			<?php echo html_entity_decode($progress); ?>
			    		</div>
			    	</div>
			 	</div>
		  	</div>
			<div class="col-md-12">
				<?php echo html_entity_decode($html); ?>
			</div>
          </div>
        <div role="tabpanel" class="tab-pane" id="tab_tasks">
 			<?php init_relation_tasks_table(array('data-new-rel-id'=>$okrs_id,'data-new-rel-type'=>'okrs')); ?>
        </div>
        
    </div>
</div>
</div>
</div>
</div>

<?php init_tail(); ?>
</body>
</html>