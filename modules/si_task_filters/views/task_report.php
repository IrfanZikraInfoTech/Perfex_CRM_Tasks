<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$report_heading = '';
?>
<link href="<?php echo module_dir_url('si_task_filters','assets/css/si_task_filters_style.css'); ?>" rel="stylesheet" />
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<?php echo form_open($this->uri->uri_string() . ($this->input->get('filter_id') ? '?filter_id='.$this->input->get('filter_id') : ''),"id=si_form_task_filter"); ?>
						<h4 class="pull-left"><?php echo _l('custom_reports')." - "._l('tasks_filter'); ?> <small class="text-success"><?php echo $saved_filter_name;?></small></h4>
						<div class="btn-group pull-right mleft4 btn-with-tooltip-group" data-toggle="tooltip" data-title="<?php echo _l('filter_templates'); ?>" data-original-title="" title="">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-list"></i>
							</button>
							<ul class="row dropdown-menu width400">
							<?php
							if(!empty($filter_templates))
							{
								foreach($filter_templates as $row)
								{
									echo "<li><a href='tasks_report?filter_id=$row[id]'>$row[filter_name]</a></li>";
								}
							}
							else
								echo '<li><a >'._l('no_filter_template').'</a></li>';
							?>
							</ul>
						</div>
						<button type="submit" data-toggle="tooltip" data-title="<?php echo _l('si_apply_filter'); ?>" class=" pull-right btn btn-info mleft4"><?php echo _l('filter'); ?></button>
						<a href="tasks_report" class=" pull-right btn btn-info mleft4"><?php echo _l('new'); ?></a>
						<div class="clearfix"></div>
						<hr />
						<div class="row">
							<?php if(has_permission('tasks','','view')){ ?>
							<div class="col-md-2 border-right">
								<label for="rel_type" class="control-label"><?php echo _l('staff_members'); ?></label>
								<?php echo render_select('member',$members,array('staffid',array('firstname','lastname')),'',$staff_id,array('data-none-selected-text'=>_l('all_staff_members')),array(),'no-margin'); ?>
							</div>
							<?php } ?>
							<div class="col-md-2 text-center1 border-right">
								<label for="rel_type" class="control-label"><?php echo _l('task_status'); ?></label>		
								<div class="form-group no-margin select-placeholder">
									<select name="status[]" id="status" class="selectpicker no-margin" data-width="100%" data-title="<?php echo _l('task_status'); ?>" multiple>
										<option value="" <?php if(in_array('',$statuses)){echo 'selected'; } ?>><?php echo _l('task_list_all'); ?></option>
										<?php foreach($task_statuses as $status){ ?>
										<option value="<?php echo $status['id']; ?>" <?php if(in_array($status['id'],$statuses)){echo 'selected'; } ?>>
										<?php echo $status['name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<!--start billable select -->
							<div class="col-md-2 border-right form-group">
								<label for="billable" class="control-label"><span class="control-label"><?php echo _l('task_billable'); ?></span></label>
								<select name="billable" id="billable" class="selectpicker no-margin" data-width="100%" >
									<option value=""><?php echo _l('task_list_all'); ?></option>
									<option value="1" <?php echo ($billable!='' && $billable=="1"?'selected':'')?>><?php echo _l('Yes'); ?></option>
									<option value="0" <?php echo ($billable!='' && $billable=="0"?'selected':'')?>><?php echo _l('No'); ?></option>
								</select>
							</div>
							<!--end billable select-->
							<!--start rel type-->
							<div class="col-md-2 border-right">
								<label for="rel_type" class="control-label"><?php echo _l('task_related_to'); ?></label>
								<select name="rel_type" class="selectpicker" id="rel_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
									<option value=""></option>
									<option value="project" <?php if(isset($rel_type)){if($rel_type == 'project'){echo 'selected';}} ?>><?php echo _l('project'); ?></option>
									<option value="invoice" <?php if(isset($rel_type)){if($rel_type == 'invoice'){echo 'selected';}} ?>><?php echo _l('invoice'); ?></option>
									<option value="customer" <?php if(isset($rel_type)){if($rel_type == 'customer'){echo 'selected';}} ?>><?php echo _l('client'); ?></option>
									<option value="estimate" <?php if(isset($rel_type)){if($rel_type == 'estimate'){echo 'selected';}} ?>><?php echo _l('estimate'); ?></option>
									<option value="contract" <?php if(isset($rel_type)){if($rel_type == 'contract'){echo 'selected';}} ?>><?php echo _l('contract'); ?></option>
									<option value="ticket" <?php if(isset($rel_type)){if($rel_type == 'ticket'){echo 'selected';}} ?>><?php echo _l('ticket'); ?></option>
									<option value="expense" <?php if(isset($rel_type)){if($rel_type == 'expense'){echo 'selected';}} ?>><?php echo _l('expense'); ?></option>
									<option value="lead" <?php if(isset($rel_type)){if($rel_type == 'lead'){echo 'selected';}} ?>><?php echo _l('lead'); ?></option>
									<option value="proposal" <?php if(isset($rel_type)){if($rel_type == 'proposal'){echo 'selected';}} ?>><?php echo _l('proposal'); ?></option>
								</select>
							</div>
							<!--end of list of rel type-->
							<!--start rel_id select from rel_type-->
							<div class="col-md-2 border-right form-group<?php if($rel_id == '' && $rel_type==''){echo ' hide';} ?>" id="rel_id_wrapper">
								<label for="rel_id" class="control-label"><span class="rel_id_label"></span></label>
								<div id="rel_id_select">
									<select name="rel_id" id="rel_id" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
									<?php if($rel_id != '' && $rel_type != ''){
									$rel_data = get_relation_data($rel_type,$rel_id);
									$rel_val = get_relation_values($rel_data,$rel_type);
									echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
									if($group_by=='')
									$report_heading.=" - ".$rel_val['name'];
									} ?>
									</select>
								</div>
							</div>
							<!--end rel_id select-->
							<!--start group_id select from rel_id if rel_type is customer-->
							<div class="col-md-2 border-right form-group<?php if($rel_type !== 'customer'){echo ' hide';} ?>" id="group_id_wrapper">
								<label for="group_id" class="control-label"><span class="control-label"><?php echo _l('customer_groups'); ?></span></label>
								<div id="group_id_select">
									<select name="group_id" id="group_id" class="selectpicker no-margin" data-width="100%" >
										<option value="" selected><?php echo _l('dropdown_non_selected_tex'); ?></option>
										<?php if(!empty($groups)){
											foreach($groups as $group)
											{
												echo '<option value="'.$group['id'].'" '.($group_id!='' && $group_id==$group['id']?'selected':'').'>'.$group['name'].'</option>';
												if($group_id==$group['id'])
													$report_heading.=" (Group:".$group['name'].")";
											}
											} 
										?>
									</select>
								</div>
							</div>
							<!--end group_id select-->
						</div>
						<div class="row">
							<!--start group_by select -->
							<div class="col-md-2 border-right form-group">
								<label for="group_id" class="control-label"><span class="control-label"><?php echo _l('group_by_task'); ?></span></label>
								<select name="group_by" id="group_by" class="selectpicker no-margin" data-width="100%">
									<option value="" selected><?php echo _l('dropdown_non_selected_tex'); ?></option>
									<option value="rel_name" <?php echo ($group_by!='' && $group_by=='rel_name'?'selected':'')?>><?php echo _l('task_related_to'); ?></option>
									<option value="rel_name_and_name" <?php echo ($group_by!='' && $group_by=='rel_name_and_name'?'selected':'')?>><?php echo _l('task_related_to_and_name'); ?></option>
									<option value="name_and_rel_name" <?php echo ($group_by!='' && $group_by=='name_and_rel_name'?'selected':'')?>><?php echo _l('task_name_and_related_to'); ?></option>
									<option value="task_name" <?php echo ($group_by!='' && $group_by=='task_name'?'selected':'')?>><?php echo _l('filter_task_name'); ?></option>
									<option value="status" <?php echo ($group_by!='' && $group_by=='status'?'selected':'')?>><?php echo _l('task_status'); ?></option>
								</select>
							</div>
							<!--end group_by select-->
							<!--start hide_export_columns select -->
							<div class="col-md-2 border-right form-group">
								<label for="hide_columns" class="control-label"><span class="control-label"><?php echo _l('hide_export_columns'); ?></span></label>
								<select name="hide_columns[]" id="hide_columns" class="selectpicker no-margin" data-width="100%" multiple>
									<option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
									<option value="name" <?php echo (in_array('name',$hide_columns)?'selected':'')?>><?php echo _l('tasks_dt_name'); ?></option>
									<?php
									$custom_fields = get_custom_fields('tasks', ['show_on_table' => 1,]);
									foreach($custom_fields as $field)
										echo "<option value='$field[slug]' ".(in_array($field['slug'],$hide_columns)?'selected':'').">$field[name]</option>";
									?>
									<option value="status" <?php echo (in_array('status',$hide_columns)?'selected':'')?>><?php echo _l('task_status'); ?></option>
									<option value="start_date" <?php echo (in_array('start_date',$hide_columns)?'selected':'')?>><?php echo _l('tasks_dt_datestart'); ?></option>
									<option value="due_date" <?php echo (in_array('due_date',$hide_columns)?'selected':'')?>><?php echo _l('task_duedate'); ?></option>
									<option value="completed_date" <?php echo (in_array('completed_date',$hide_columns)?'selected':'')?>><?php echo _l('task_completed_date'); ?></option>
									<option value="billable" <?php echo (in_array('billable',$hide_columns)?'selected':'')?>><?php echo _l('task_billable'); ?></option>
									<option value="attachments" <?php echo (in_array('attachments',$hide_columns)?'selected':'')?>><?php echo _l('tasks_total_added_attachments'); ?></option>
									<option value="comments" <?php echo (in_array('comments',$hide_columns)?'selected':'')?>><?php echo _l('tasks_total_comments'); ?></option>
									<option value="checklist" <?php echo (in_array('checklist',$hide_columns)?'selected':'')?>><?php echo _l('task_checklist_items'); ?></option>
									<option value="logged_time" <?php echo (in_array('logged_time',$hide_columns)?'selected':'')?>><?php echo _l('staff_stats_total_logged_time'); ?></option>
									<option value="on_time" <?php echo (in_array('on_time',$hide_columns)?'selected':'')?>><?php echo _l('task_finished_on_time'); ?></option>
									<option value="assigned" <?php echo (in_array('assigned',$hide_columns)?'selected':'')?>><?php echo _l('task_assigned'); ?></option>
									<option value="tags" <?php echo (in_array('tags',$hide_columns)?'selected':'')?>><?php echo _l('tags'); ?></option>
								</select>
							</div>
							<!--end hide_export_columns select-->
							<div class="col-md-2 form-group border-right" id="report-time">
								<label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
								<select class="selectpicker" name="report_months" id="report_months" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
									<option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
									<option value="this_month"><?php echo _l('this_month'); ?></option>
									<option value="1"><?php echo _l('last_month'); ?></option>
									<option value="this_year"><?php echo _l('this_year'); ?></option>
									<option value="last_year"><?php echo _l('last_year'); ?></option>
									<option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
									<option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
									<option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
									<option value="custom"><?php echo _l('period_datepicker'); ?></option>
								</select>
								<?php
									if($report_months !== '')
									{
										$report_heading.=' for '._l('period_datepicker')." ";
										switch($report_months)
										{
											case 'this_month':$report_heading.=date('01-m-Y')." To ".date('t-m-Y');break;
											case '1'         :$report_heading.=date('01-m-Y',strtotime('-1 month'))." To ".date('t-m-Y',strtotime('-1 month'));break;
											case 'this_year' :$report_heading.=date('01-01-Y')." To ".date('31-12-Y');break;
											case 'last_year' :$report_heading.=date('01-01-Y',strtotime('-1 year'))." To ".date('31-12-Y',strtotime('-1 year'));break;
											case '3'         :$report_heading.=date('01-m-Y',strtotime('-2 month'))." To ".date('t-m-Y');break;
											case '6'         :$report_heading.=date('01-m-Y',strtotime('-5 month'))." To ".date('t-m-Y');break;
											case '12'        :$report_heading.=date('01-m-Y',strtotime('-11 month'))." To ".date('t-m-Y');break;
											case 'custom'    :$report_heading.=$report_from." To ".$report_to;break;
											default          :$report_heading.='All Time';
										}
									}
								?>
							</div>
							<!--start filter_by select -->
							<div class="col-md-2 border-right form-group<?php if($date_by == ''){echo ' hide';} ?>" id="date_by_wrapper">
								<label for="date_by" class="control-label"><span class="control-label"><?php echo _l('task_filter_by_date'); ?></span></label>
								<select name="date_by" id="date_by" class="selectpicker no-margin" data-width="100%" >
									<option value="startdate"><?php echo _l('tasks_dt_datestart'); ?></option>
									<option value="datefinished" <?php echo ($date_by!='' && $date_by=='datefinished'?'selected':'')?>><?php echo _l('task_completed_date'); ?></option>
								</select>
							</div>
							<!--end filter_by select-->
							<div id="date-range" class="col-md-4 hide mbot15">
								<div class="row">
									<div class="col-md-6">
										<label for="report_from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
										<div class="input-group date">
											<input type="text" class="form-control datepicker" id="report_from" name="report_from" value="<?php echo $report_from;?>" autocomplete="off">
											<div class="input-group-addon">
												<i class="fa fa-calendar calendar-icon"></i>
											</div>
										</div>
									</div>
									<div class="col-md-6 border-right">
										<label for="report_to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
										<div class="input-group date">
											<input type="text" class="form-control datepicker" id="report_to" name="report_to" autocomplete="off">
											<div class="input-group-addon">
												<i class="fa fa-calendar calendar-icon"></i>
											</div>
										</div>
									</div>
								</div>
							</div><!--end date time div-->
							<div class="col-md-12">
								<div class="checklist relative">
									<div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="<?php echo _l('save_filter_template'); ?>">
										<input type="checkbox" id="si_save_filter" name="save_filter" value="1" title="<?php echo _l('save_filter_template'); ?>" <?php echo ($this->input->get('filter_id')?'checked':'')?>>
										<label for=""><span class="hide"><?php echo _l('save_filter_template'); ?></span></label>
										<textarea id="si_filter_name" name="filter_name" rows="1" placeholder="<?php echo _l('filter_template_name'); ?>" <?php echo ($this->input->get('filter_id')?'':'disabled="disabled"')?> maxlength='100'><?php echo ($this->input->get('filter_id')?$saved_filter_name:'');?></textarea>
									</div>
								</div>
							</div>
						</div>
						<?php echo form_close(); ?>
					</div>
				</div>
				<div class="panel_s">
					<div class="panel-body">
					<?php
					foreach($overview as $month =>$data){ if(count($data) == 0){continue;} ?>
						<h4 class="bold text-success"><?php echo $month; ?>
						<?php if($this->input->get('project_id')){ echo ' - ' . get_project_name_by_id($this->input->get('project_id'));} ?>
						<?php if(is_numeric($staff_id) && has_permission('tasks','','view')) { echo ' ('.get_staff_full_name($staff_id).')';} ?>
						</h4>
						<table class="table tasks-overview dt-table scroll-responsive">
							<caption class="si_caption"><?php echo $month.$report_heading;?></caption>
							<thead>
								<tr>
								<?php if (($group_by!=='rel_name_and_name' && $group_by!=='name_and_rel_name') || $month==''){?>
									<th class="<?php echo (in_array('name',$hide_columns)?'not-export':'')?>"><?php echo _l('tasks_dt_name'); ?></th>
								<?php }?>
								<?php
									$custom_fields = get_custom_fields('tasks', ['show_on_table' => 1,]);
									foreach($custom_fields as $field)
									{
										echo '<th class="'.(in_array($field['slug'],$hide_columns)?'not-export':'').'">'.$field['name'].'</th>';	
									}
								?>
									<th class="<?php echo (in_array('status',$hide_columns)?'not-export':'')?>"><?php echo _l('task_status'); ?></th>
									<th class="<?php echo (in_array('start_date',$hide_columns)?'not-export':'')?>"><?php echo _l('tasks_dt_datestart'); ?></th>
									<th class="<?php echo (in_array('due_date',$hide_columns)?'not-export':'')?>"><?php echo _l('task_duedate'); ?></th>
									<th class="<?php echo (in_array('completed_date',$hide_columns)?'not-export':'')?>"><?php echo _l('task_completed_date'); ?></th>
									<th class="<?php echo (in_array('billable',$hide_columns)?'not-export':'')?>"><?php echo _l('task_billable'); ?></th>
									<th class="<?php echo (in_array('attachments',$hide_columns)?'not-export':'')?>"><?php echo _l('tasks_total_added_attachments'); ?></th>
									<th class="<?php echo (in_array('comments',$hide_columns)?'not-export':'')?>"><?php echo _l('tasks_total_comments'); ?></th>
									<th class="<?php echo (in_array('checklist',$hide_columns)?'not-export':'')?>"><?php echo _l('task_checklist_items'); ?></th>
									<th class="<?php echo (in_array('logged_time',$hide_columns)?'not-export':'')?>"><?php echo _l('staff_stats_total_logged_time'); ?></th>
									<th class="<?php echo (in_array('on_time',$hide_columns)?'not-export':'')?>"><?php echo _l('task_finished_on_time'); ?></th>
									<th class="<?php echo (in_array('assigned',$hide_columns)?'not-export':'')?>"><?php echo _l('task_assigned'); ?></th>
									<th class="<?php echo (in_array('tags',$hide_columns)?'not-export':'')?>"><?php echo _l('tags'); ?></th>
								</tr>
							</thead>
						<tbody>
							<?php
								foreach($data as $task){ ?>
								<tr>
								<?php if (($group_by!=='rel_name_and_name' && $group_by!=='name_and_rel_name') || $month==''){?>
									<td data-order="<?php echo htmlentities($task['name']); ?>"><a href="<?php echo admin_url('tasks/view/'.$task['id']); ?>" onclick="init_task_modal(<?php echo $task['id']; ?>); return false;"><?php echo $task['name']; ?></a>
									<?php
										if (!empty($task['rel_id']) && $group_by!='rel_name')
											echo '<br />'. _l('task_related_to').': <a class="text-muted" href="' . task_rel_link($task['rel_id'],$task['rel_type']) . '">' . task_rel_name($task['rel_name'],$task['rel_id'],$task['rel_type']) . '</a>';
									?>
									</td>
								<?php }?>
								<?php
									foreach($custom_fields as $field)
									{
										$current_value = get_custom_field_value($task['id'], $field['id'], 'tasks', false);
										echo '<td>'.(($field['type']=='date_picker' || $field['type']=='date_picker_time') && $current_value!='' ? date('d-m-Y',strtotime($current_value)):$current_value).'</td>';
									}
								?>
									<td><?php echo format_task_status($task['status']); ?></td>
									<td data-order="<?php echo $task['startdate']; ?>"><?php echo _d($task['startdate']); ?></td>
									<td data-order="<?php echo $task['duedate']; ?>"><?php echo _d($task['duedate']); ?></td>
									<td data-order="<?php echo $task['datefinished']; ?>"><?php echo _d($task['datefinished']); ?></td>
									<td data-order="<?php echo $task['billable']; ?>"><?php echo ($task['billable']?'Yes':'No'); ?></td>
									<td data-order="<?php echo $task['total_files']; ?>">
										<span class="label label-default-light" data-toggle="tooltip" data-title="<?php echo _l('tasks_total_added_attachments'); ?>">
											<a <?php if($task['total_files']>0) echo 'href="'.admin_url('tasks/download_files/'.$task['id']).'"';?> class="bold" disabled>
												<i class="fa fa-paperclip"></i>
												<?php
												if(!is_numeric($staff_id)) {
													echo $task['total_files'];
												}else{
													echo $task['total_files_staff'] . '/' . $task['total_files'];
												}
												?>
											</a>
										</span>
									</td>
									<td data-order="<?php echo $task['total_comments']; ?>">
										<span class="label label-default-light" data-toggle="tooltip" data-title="<?php echo _l('tasks_total_comments'); ?>">
											<i class="fa fa-comments"></i>
											<?php
											 if(!is_numeric($staff_id)) {
												echo $task['total_comments'];
											 } else {
												echo $task['total_comments_staff'] . '/' . $task['total_comments'];
											 }
											?>
										</span>
									</td>
									<td>
										<span class="label <?php if($task['total_checklist_items'] == '0'){ echo 'label-default-light'; } else if(($task['total_finished_checklist_items'] != $task['total_checklist_items'])){ echo 'label-danger';}
										else if($task['total_checklist_items'] == $task['total_finished_checklist_items']){echo 'label-success';} ?> pull-left mright5" data-toggle="tooltip" data-title="<?php echo _l('tasks_total_checklists_finished'); ?>">
											<i class="fa fa-th-list"></i>
											<?php echo $task['total_finished_checklist_items']; ?>/<?php echo $task['total_checklist_items']; ?>
										</span>
									</td>
									<td data-order="<?php echo $task['total_logged_time']; ?>">
										<span class="label label-default-light pull-left mright5" data-toggle="tooltip" data-title="<?php echo _l('staff_stats_total_logged_time'); ?>">
											<i class="fa fa-clock-o"></i> <?php echo seconds_to_time_format($task['total_logged_time']); ?>
										</span>
									</td>
									<?php
									$finished_on_time_class = '';
									$finishedOrder = 0;
									if(date('Y-m-d',strtotime($task['datefinished'])) > $task['duedate'] && $task['status'] == Tasks_model::STATUS_COMPLETE && is_date($task['duedate'])){
										$finished_on_time_class = 'text-danger';
										$finished_showcase = _l('task_not_finished_on_time_indicator');
									} else if(date('Y-m-d',strtotime($task['datefinished'])) <= $task['duedate'] && $task['status'] == Tasks_model::STATUS_COMPLETE && is_date($task['duedate'])){
										$finishedOrder = 1;
										$finished_showcase = _l('task_finished_on_time_indicator');
									} else {
										$finished_on_time_class = '';
										$finished_showcase = '';
									}
									?>
									<td data-order="<?php echo $finishedOrder; ?>">
										<span class="<?php echo $finished_on_time_class; ?>">
										<?php echo $finished_showcase; ?>
										</span>
									</td>
									<td>
										<?php echo format_members_by_ids_and_names($task['assignees_ids'],$task['assignees'], false);?>
									</td>
									<td><?php echo  render_tags(prep_tags_input(get_tags_in($task['id'],'task'))); ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						<hr />
					<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>
<script src="<?php echo module_dir_url('si_task_filters','assets/js/si_task_filters_task_report.js'); ?>"></script>
<script>
(function($) {
"use strict";
<?php  if($report_months !== ''){ ?>
	$('#report_months').val("<?php echo $report_months;?>");
	$('#report_months').change();		
<?php }
	if($report_from !== ''){ 
?>
	$('#report_from').val("<?php echo $report_from;?>");
<?php
	}
	if($report_to !== ''){ 
?>
	$('#report_to').val("<?php echo $report_to;?>");
<?php
	}
?>
})(jQuery);				  
</script>

