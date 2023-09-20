<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
	<div role="tabpanel" class="tab-pane active" id="training_library">
		<div>
			<?php if(has_permission('staffmanage_training','','create') || has_permission('staffmanage_training','','view')){ ?>
				<div class="_buttons">
					<?php if(has_permission('staffmanage_training','','create')){ ?>
						<a href="<?php echo admin_url('hr_profile/position_training'); ?>" class="btn btn-info pull-left display-block" >
							<?php echo _l('hr_hr_add'); ?>
						</a>
					<?php } ?>
				</div>
			<?php } ?>

			<div class="clearfix"></div>
			<br>

			<div class="modal bulk_actions" id="table_training_table_bulk_actions" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title"><?php echo _l('hr_bulk_actions'); ?></h4>
						</div>
						<div class="modal-body">
							<?php if(has_permission('staffmanage_training','','delete') || is_admin()){ ?>
								<div class="checkbox checkbox-danger">
									<input type="checkbox" name="mass_delete" id="mass_delete">
									<label for="mass_delete"><?php echo _l('hr_mass_delete'); ?></label>
								</div>
							<?php } ?>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>

							<?php if(has_permission('staffmanage_training','','delete') || is_admin()){ ?>
								<a href="#" class="btn btn-info" onclick="training_library_delete_bulk_action(this); return false;"><?php echo _l('hr_confirm'); ?></a>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>

			<?php if (has_permission('staffmanage_training','','delete')) { ?>
				<a href="#"  onclick="training_library_bulk_actions(); return false;" data-toggle="modal" data-table=".table-training_table" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('hr_bulk_actions'); ?></a>
			<?php } ?>

			<?php $training_table = array(
				'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="training_table"><label></label></div>',
				_l('id'),
				_l('hr_survey_dt_name'),
				_l('hr_training_type'),
				_l('hr_survey_dt_total_questions'),
				_l('hr_survey_dt_total_participants'),
				_l('hr_survey_dt_date_created'),
			); 

		render_datatable($training_table,'training_table',
			array('customizable-table'),
			array(
				'id'=>'table-training_table',
				'data-last-order-identifier'=>'training_table',
				'data-default-order'=>get_table_last_order('training_table'),
			)); 

			?>

		</div>
	</div>

</div>
</body>
</html>
