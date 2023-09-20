<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
<div class="content">
<div class="panel_s">
<div class="panel-body">
	<div class="col-md-4">
		<?php echo render_select('okrs',$okrs,array('id',array('your_target')),'okr',''); ?>
	</div>
	<div class="col-md-4">
		<?php echo render_select('person_assigned',$person_assigned,array('staffid',array('firstname','lastname')),'person_assigned',''); ?>
	</div>
	
	<div class="col-md-4">
		<?php echo render_select('category',$category,array('id',array('category')),'category',''); ?>
	</div>
	<div class="col-md-4">
		<?php echo render_select('department',$department,array('departmentid',array('name')),'department',''); ?>
	</div>
	<div class="col-md-4">
		<?php  
			$status = [
				['id' => 1, 'name' => _l('finish')],
				['id' => 0, 'name' => _l('unfinished')],
			];	
			$type = [
				['id' => 1, 'name' => _l('personal')],
				['id' => 2, 'name' => _l('department')],
				['id' => 3, 'name' => _l('company')]
			];
		?>
		<?php echo render_select('status',$status,array('id',array('name')),'status',''); ?>
	</div>
	<div class="col-md-4">
		<?php echo render_select('type',$type,array('id',array('name')),'type', ''); ?>
	</div>
	<div class="col-md-4">
		<div class="form-group" app-field-wrapper="circulation">
			<label for="circulation" class="control-label"><?php echo _l('circulation'); ?></label>
			<select id="circulation" name="circulation" class="selectpicker" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98">
				<option value=""></option>
				<?php foreach ($circulation as $key => $value) { ?>
					<option value="<?php echo html_entity_decode($value['id']); ?>" ><?php echo html_entity_decode($value['name_circulation']); ?>
						<small>( <?php echo html_entity_decode($value['from_date']); ?> - <?php echo html_entity_decode($value['to_date']); ?>)</small>
					</option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="col-md-12 padding-with-table">
		<?php
		    $table_data = array(
		        _l('object'),
		        _l('circulation'),
		        _l('progress'),
		        _l('okr_superior'),
		        _l('person_assigned'),
		        _l('type'),
		        _l('category'),
		        _l('department'),
		        _l('recently_checkin'),
		        _l('upcoming_checkin'),
		        _l('status'),
		        );
		    render_datatable($table_data,'dashboard');
		?>
	</div>
</div>
</div>

</div>
</div>

<?php init_tail(); ?>
</body>
</html>
