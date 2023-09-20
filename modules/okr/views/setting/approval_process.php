<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
 <div class="_buttons">
 	<a href="#" onclick="add(); return false;" class="btn btn-info pull-left" ><?php echo _l('add'); ?></a>
 </div>
 <div class="clearfix"></div>
 <hr class="hr-panel-heading" />
 <div class="clearfix"></div>

 <table class="table table-approve scroll-responsive">
 	<thead>
 		<th>ID#</th>
 		<th><?php echo _l('name'); ?></th>
 		<th><?php echo _l('department'); ?></th>
 		<th><?php echo _l('okrs'); ?></th>
 		<th><?php echo _l('options'); ?></th>      
 	</thead>
 	<tbody></tbody>
 	<tfoot>
 		<td></td>
 		<td></td>
 		<td></td>
 		<td></td>
 		<td></td>                        
 	</tfoot>
 </table>

 <div class="modal" id="approve_modal" tabindex="-1" role="dialog">
 	<div class="modal-dialog" role="document">
 		<div class="modal-content">
 			<div class="modal-header">
 				<h4 class="modal-title"><?php echo _l('add_approval'); ?></h4>
 				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
 			</div>

 			<?php $setting = []; ?>
 			<?php echo form_open(admin_url('okr/approver_setting'),array('id'=>'approval-setting-form')); ?>
 			<?php $value = (isset($approval_setting)) ? $approval_setting->id : ''; ?>
 			<?php echo form_hidden('approval_setting_id', $value); ?>
 			<div class="modal-body">
 				<div class="row">
 					<div class="col-md-12">
 						<?php $value = (isset($approval_setting)) ? $approval_setting->name : ''; ?>
 						<?php echo render_input('name','subject',$value,'text'); ?>
 						<?php $related = [ 
 							0 => ['id' => 'adjustment', 'name' => _l('adjustment')],
 							1 => ['id' => 'transfer', 'name' => _l('asset_tranfer')],
 							2 => ['id' => 'allocation', 'name' => _l('allocation')],
 							3 => ['id' => 'revoke', 'name' => _l('recalled')],
 							4 => ['id' => 'stocktaking', 'name' => _l('stocktaking')],                     
 							5 => ['id' => 'liquidation', 'name' => _l('assets_liquidation_slug')],
 							6 => ['id' => 'repair', 'name' => _l('repair')],
 							7 => ['id' => 'maintenance', 'name' => _l('maintenance')]
 						]; 
 						$value = (isset($approval_setting)) ? $approval_setting->related : '';
 						$value_okrs = (isset($approval_setting)) ? $approval_setting->value_okrs : '';
 						?>
 						<?php echo render_select('department[]',$department,array('departmentid','name'),'department',$value,array('multiple'=>'true')); ?>
 						<?php echo render_select('okrs[]',$okrs,array('id','your_target'),'okrs',$value_okrs,array('multiple'=>'true')); ?>
 						<?php $choose_when_approving = 0;
 						if(isset($approval_setting)){
 							$choose_when_approving = $approval_setting->choose_when_approving;
 						} 
 						?>
 						<!-- use for only Leave  start-->

 						<div id="notification_recipient" class="notification_recipient">
 							<div class="select-placeholder form-group">
 								<label for="notification_recipient[]"><?php echo _l('notification_recipient'); ?></label>
 								<select name="notification_recipient[]" id="notification_recipient[]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" multiple="true" data-action-box="true" data-hide-disabled="true" data-live-search="true">
 									<?php foreach($staffs as $val){
 										$selected = '';
 										?>
 										<option value="<?php echo $val['staffid']; ?>">
 											<?php echo get_staff_full_name($val['staffid']); ?>
 										</option>
 									<?php } ?>
 								</select>
 							</div> 
 						</div>

 						<?php echo render_input('number_day_approval','maximum_number_of_days_to_sign','','number'); ?>

 						<div >
 							<label style="font-weight: 500;"><?php echo _l('approval_process'); ?></label>
 							<div class="checkbox checkbox-inline checkbox-primary pull-right">
 								<input type="checkbox" name="choose_when_approving" id="choose_when_approving" value="1" <?php if($choose_when_approving == 1){echo 'checked';} ?>>
                <label for="choose_when_approving"><?php echo _l('choose_specific_senders_for_approval'); ?></label>
 							</div>
 						</div>
 						<div class="list_approvest">
 							<hr/>                        

 							<div id="item_approve" style="padding-left: 0">
 								<div class="col-md-11" style="padding-left: 0">
 									<div class="col-md-6" style="padding-left: 0;padding-right: 0">
 										<div class="select-placeholder form-group">
 											<label for="approver[0]"><?php echo _l('approver'); ?></label>
 											<select name="approver[0]" id="approver[0]" data-id="0" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true">
 												<option value="" class="hide"></option>
 												<option value="head_of_department" class="hide"><?php echo _l('head_of_department'); ?></option>
 												<option value="direct_manager" class="hide"><?php echo _l('direct_manager'); ?></option>
 												<option value="specific_personnel"><?php echo _l('specific_personnel'); ?></option>
 											</select>
 										</div> 
 									</div>
 									<div class="col-md-6" id="is_staff_0">
 										<div class="select-placeholder form-group">
 											<label for="staff[0]"><?php echo _l('staff'); ?></label>
 											<select name="staff[0]" id="staff[0]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true">
 												<option value=""></option>
 												<?php foreach($staffs as $val){ ?>
 													<option value="<?php echo $val['staffid']; ?>" <?php echo $selected; ?>>
 														<?php echo get_staff_full_name($val['staffid']); ?>
 													</option>
 												<?php } ?>
 											</select>
 										</div> 
 									</div>
 								</div>
 								<div class="col-md-1" style="display: contents;line-height: 84px;white-space: nowrap;">
 									<span class="pull-bot">
 										<button name="add" class="btn new_vendor_requests btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
 									</span>
 								</div>
 							</div>

 						</div>
 					</div>
 				</div>
 			</div>
 			<div class="modal-footer">
 				<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
 				<?php echo form_close(); ?>
 			</div>
 		</div>
 	</div>
 </div>

 <script>
 	function add(){
 		$('.modal-title').text('<?php echo _l('add_approval'); ?>');
    $('input[name="approval_setting_id"]').val('');
    $('input[name="name"]').val('');
    $('select[name="department[]"]').val('').change();
    $('select[name="okrs[]"]').val('').change();
    $('select[name="notification_recipient[]"]').val('').change();
 		$('input[name="number_day_approval"]').val('');
    $('.remove_vendor_requests').click();
    $('select[name="approver[0]"]').val('').change();
    $('select[name="staff[0]"]').val('').change();
    $('input[name="choose_when_approving"]').prop('checked', false);
 		$('#approve_modal').modal();
 	}

 	function update_approve(el){
 		var id = $(el).data('id');
 		$('input[name="approval_setting_id"]').val(id);
 		$('.modal-title').text('<?php echo _l('update_approval'); ?>');
 		$.post(admin_url+'okr/get_approve_setting/'+id).done(function(response){
      response = JSON.parse(response);

 			if(response.success == true) {

 				var item_approve = jQuery.parseJSON(response.data_setting.setting);
 				$('.remove_vendor_requests').click();

 				for (var i = 0;i < item_approve.length; i++) {
 					if(i>0){
 						$('.new_vendor_requests').click();
 					}
 					$('select[name="approver['+i+']"]').val(item_approve[i].approver).change();
 					if(item_approve[i].approver == 'specific_personnel'){
 						$('select[name="staff['+i+']"]').val(item_approve[i].staff).change();
 					}
               //console.log(item_approve[i].approver);
           }
           if(response.data_setting.choose_when_approving == 1){
           	$('#choose_when_approving').prop("checked", true);
           	$('.list_approvest').addClass('hide');
           }
           else{
           	$('#choose_when_approving').prop("checked", false);
           	$('.list_approvest').removeClass('hide');  
           }


           $('input[name="name"]').val(response.data_setting.name);

           $('select[name="department[]"]').val(response.data_setting.department).change();
           $('select[name="okrs[]"]').val(response.data_setting.okrs).change();


           $('select[name="notification_recipient[]"]').val(response.data_setting.notification_recipient).change();
           $('input[name="number_day_approval"]').val(response.data_setting.number_day_approval);


           $('#approve_modal').modal();
       }
       $('#savePredefinedReplyFromMessageModal').modal('hide');
   });
 	}
 </script>