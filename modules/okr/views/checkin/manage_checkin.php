<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
<div class="content">
<div class="panel_s">
<div class="panel-body">
	<br>
	<br>
	<div class="row">
		<?php 
			$circulation_cky_current = ''; 
			if(isset($cky_current)){
				$circulation_cky_current = $cky_current;
			}
		
			$type = [
				['id' => 1, 'name' => _l('personal')],
				['id' => 2, 'name' => _l('department')],
				['id' => 3, 'name' => _l('company')]
			];
		?>
		<div class="col-md-4">
			<label for="okrs" class="control-label"><?php echo _l('circulation'); ?></label>
			<select id="circulation" name="circulation" class="selectpicker" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98">
				<option value=""></option>
				<?php foreach ($circulation as $key => $value) { ?>
					<option value="<?php echo html_entity_decode($value['id']); ?>" <?php if($circulation_cky_current == $value['id']){ echo 'selected'; } ?> ><?php echo html_entity_decode($value['name_circulation']); ?> 
						<small>( <?php echo html_entity_decode($value['from_date']); ?> - <?php echo html_entity_decode($value['to_date']); ?>)</small>
					</option>
				<?php } ?>
			</select>
		</div>

		<div class="col-md-4">
			<?php echo render_select('okrs',$okrs,array('id',array('your_target')),'okr',''); ?>
		</div>
		<div class="col-md-4">
			<?php echo render_select('staff',$staffs,array('staffid',array('firstname','lastname')),'staff',''); ?>
		</div>
		<div class="col-md-4">
			<?php echo render_select('type',$type,array('id',array('name')),'type', ''); ?>
		</div>
		<div class="col-md-4">
			<?php echo render_select('category',$category,array('id',array('category')),'category', ''); ?>
		</div>
		<div class="col-md-4">
			<?php echo render_select('department',$department,array('departmentid',array('name')),'department', ''); ?>
		</div>
	</div>
	<table id="data" class="table tree-move-checkin">
        <thead>
            <tr>
                <th scope="col"><?php echo _l('object'); ?></th>
		        <th scope="col"><?php echo _l('key_results'); ?></th>
		        <th scope="col"><?php echo _l('progress'); ?></th>
		        <th scope="col"><?php echo _l('change'); ?></th>
		        <th scope="col"><?php echo _l('confidence_level'); ?></th>
		        <th scope="col"><?php echo _l('category'); ?></th>
		        <th scope="col"><?php echo _l('type'); ?></th>
		        <th scope="col"><?php echo _l('department'); ?></th>
		        <th scope="col"><?php echo _l('checkin'); ?></th>
		        <th scope="col"><?php echo _l('recently_checkin'); ?></th>
		        <th scope="col"><?php echo _l('upcoming_checkin'); ?></th>
		        <th scope="col"><?php echo _l('approval_status'); ?></th>
            </tr>
        </thead>
        <tbody>
    		<?php echo html_entity_decode($array_tree); ?>
        </tbody>
    </table>
</div>
</div>
</div>
</div>
<!-- HIDDEN / POP-UP DIV -->
<div id="pop-up">
   <p>
  </p>
  
</div>
<?php init_tail(); ?>
<?php require 'modules/okr/assets/js/file_js_checkin.php';?>
</body>
</html>