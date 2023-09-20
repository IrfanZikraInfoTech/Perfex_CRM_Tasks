<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="training-add-edit-wrapper">
				<div class="row">
					<div class="col-md-12">
						<div class="panel_s">
							<div class="panel-body">

								<h4 class="modal-title pl-3">
									<span class="edit-title"><?php echo _l('hr_job_position_detail'); ?></span>
								</h4>


										<!-- general_info start -->
											<div class="row">
												<div class="row col-md-12">

													<div class="col-md-12 panel-padding">
														<table class="table border table-striped table-margintop">
															<tbody>
																<tr class="project-overview">
																	<td class="bold" width="30%"><?php echo _l('hr_position_code'); ?></td>
																	<td><?php echo html_entity_decode($job_position_general->position_code) ; ?></td>
																</tr>
																<tr class="project-overview">
																	<td class="bold"><?php echo _l('hr_position_name'); ?></td>
																	<td><?php echo html_entity_decode($job_position_general->position_name) ; ?></td>
																</tr>
																<tr class="project-overview">
																	<td class="bold"><?php echo _l('hr_job_p_id'); ?></td>
																	<td><?php echo html_entity_decode(get_job_name($job_position_general->job_p_id)) ; ?></td>
																</tr>
																<tr class="project-overview">
																	<td class="bold"><?php echo _l('hr_department'); ?></td>
																	<td><?php echo (get_department_from_strings($job_position_general->department_id, 3)) ; ?></td>
																</tr>

																<tr class="project-overview">
																	<td class="bold"><?php echo _l('tags'); ?></td>
																	<td>
																		<div class="form-group">
																			<div id="inputTagsWrapper">
																				<input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($job_position_general) ? prep_tags_input(get_tags_in($job_position_general->position_id,'job_position')) : ''); ?>" data-role="tagsinput">
																			</div>
																		</div>

																	</td>
																</tr>
															</tbody>
														</table>
													</div>

													<br>
												</div>
											</div>

											<div class=" row ">
												<div class="col-md-12">
													<h4 class="h4-color"><?php echo _l('hr_hr_description'); ?></h4>
													<hr class="hr-color">
													<h5><?php echo html_entity_decode($job_position_general->job_position_description) ; ?></h5>
												</div>
											</div>

											<!-- file attachment -->
											<div class="row">                           
												<div id="contract_attachments" class="mtop30 col-md-8 ">
													<?php if(isset($job_position_attachment)){ ?>
														<?php
														$data = '<div class="row" id="attachment_file">';
														foreach($job_position_attachment as $attachment) {
															$href_url = site_url('modules/hr_profile/uploads/job_position/'.$attachment['rel_id'].'/'.$attachment['file_name']).'" download';
															if(!empty($attachment['external'])){
																$href_url = $attachment['external_link'];
															}
															$data .= '<div class="display-block contract-attachment-wrapper">';
															$data .= '<div class="col-md-10">';
															$data .= '<div class="col-md-1 mr-5">';
															$data .= '<a name="preview-btn" onclick="preview_file_job_position(this); return false;" rel_id = "'.$attachment['rel_id'].'" id = "'.$attachment['id'].'" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="'._l("preview_file").'">';
															$data .= '<i class="fa fa-eye"></i>'; 
															$data .= '</a>';
															$data .= '</div>';
															$data .= '<div class=col-md-9>';
															$data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
															$data .= '<a href="'.$href_url.'>'.$attachment['file_name'].'</a>';
															$data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
															$data .= '</div>';
															$data .= '</div>';
															$data .= '<div class="col-md-2 text-right">';

															$data .= '</div>';
															$data .= '<div class="clearfix"></div><hr/>';
															$data .= '</div>';
														}
														$data .= '</div>';
														echo html_entity_decode($data);
														?>
													<?php } ?>                              
												</div>

											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="modal-footer">
														<a href="<?php echo admin_url('hr_profile/job_positions/'.$parent_id); ?>" class="btn btn-default  mright5"><?php echo _l('hr_close'); ?></a>
													</div>
												</div>
											</div>


										<!-- salary level start -->
										<div role="tabpanel" class="tab-pane hide " id="salary_allowance_insurance">
											<?php echo form_open_multipart(admin_url('hr_profile/job_position_salary_add_edit'),array('class'=>'job_position_salary_add_edit','autocomplete'=>'off')); ?>
											<input type="hidden" name="job_position_id" value="<?php echo html_entity_decode($job_position_id); ?>">

											<div class="row hide">
												<div class="col-md-12">
													<?php $premium_rates = isset($salary_insurance) ? $salary_insurance : '' ;
													$attr = array();
													$attr = ['data-type' => 'currency'];

													echo render_input('premium_rates','_insurance_salary', app_format_money((int)$premium_rates,''),'text', $attr); ?>
												</div>
											</div>

											<div class="row">
												<div class="col-md-6">
													<div class="row " >

														<!-- start salary-->
														<?php if(isset($salary_form_edit) && (count($salary_form_edit) != 0)){ ?>

															<div class="col-md-12 contract-expense-al">
																<!-- for each start -->
																<?php foreach ($salary_form_edit as $salary_key => $salary_value) { ?>

																	<div id ="contract-expense" class="row">
																		<div class="col-md-5 ">
																			<label for="salary_form[<?php echo html_entity_decode($salary_key); ?>]" class="control-label"><?php echo _l('hr_salary_form'); ?></label>
																			<select onchange="OnSelectionChange_salsaryform (this)" name="salary_form[<?php echo html_entity_decode($salary_key); ?>]" class="selectpicker" id="salary_form[<?php echo html_entity_decode($salary_key); ?>]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
																				<option value=""></option> 
																				<?php
																				foreach($salary_form as $s){                             
																					?>
																					<option value="<?php echo html_entity_decode($s['form_id']); ?>" <?php if(isset($salary_value) && $salary_value['rel_id'] == $s['form_id'] ){echo 'selected';} ?>><?php echo html_entity_decode($s['form_name']); ?></option>

																				<?php } ?>
																			</select>
																		</div>

																		<div class="col-md-5">
																			<?php
																			$input_att1 =[];
																			$input_att1['data-type']='currency';

																			?>

																			<?php $value_expense = (isset($salary_value['value']) ? $salary_value['value'] : ''); ?>

																			<?php  echo render_input('contract_expense['.$salary_key.']','amount_of_money', app_format_money($value_expense, ''), 'text', $input_att1, [],'','salary_currency'); ?> 

																		</div>
																		<?php if($salary_key == 0) { ?>
																			<div class="col-md-2 ptop" name="button_add">
																				<button name="add" class="btn new_contract_expense btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
																			</div>
																		<?php } else{ ?>
																			<div class="col-md-2 ptop" name="button_add">
																				<button name="add" class="btn remove_contract_expense btn-danger" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
																			</div>
																		<?php } ?>

																	</div>
																<?php } ?>
															</div>

														<?php }else{ ?>
															<div class="col-md-12 contract-expense-al">
																<div id ="contract-expense" class="row">
																	<div class="col-md-5 ">

																		<label for="salary_form[0]" class="control-label"><?php echo _l('hr_salary_form'); ?></label>

																		<select onchange="OnSelectionChange_salsaryform (this)" name="salary_form[0]" class="selectpicker" id="salary_form[0]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
																			<option value=""></option> 
																			<?php
																			foreach($salary_form as $s){                             
																				?>
																				<option value="<?php echo html_entity_decode($s['form_id']); ?>" <?php if(isset($contracts) && $contracts[0]['salary_form'] == $s['form_id'] ){echo 'selected';} ?>><?php echo html_entity_decode($s['form_name']); ?>
																			</option>
																		<?php } ?>
																	</select>
																</div>
																<div class="col-md-5">
																	<?php $value = (isset($expense) ? $expense->expense_name : ''); ?>
																	<div class="form-group" app-field-wrapper="contract_expense[0]">
																		<label for="contract_expense[0]" class="control-label get_id_row" value="1"><?php echo _l('hr_amount_of_money') ?></label>
																		<input type="text" id="contract_expense[0]" name="contract_expense[0]" class="form-control salary_currency" data-type="currency" >
																	</div>
																</div>
																<div class="col-md-2 ptop" name="button_add">
																	<button name="add" class="btn new_contract_expense btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
																</div>
															</div>
														</div>

													<?php } ?>


												</div>
											</div>
											<div class="col-md-6">

												<?php if(isset($salary_allowance) && (count($salary_allowance) != 0)){ ?>
													<div class="col-md-12 contract-allowance-type">
														<?php foreach ($salary_allowance as $allowance_key => $allowance_value) { ?>

															<div id ="contract-allowancetype" class="row">
																<div class="col-md-5">
																	<label for="allowance_type[<?php echo html_entity_decode($allowance_key) ?>]" class="control-label"><?php echo _l('hr_allowance_type'); ?></label>
																	<select onchange="OnSelectionChange_allowancetype (this)" name="allowance_type[<?php echo html_entity_decode($allowance_key) ?>]" class="selectpicker" id="allowance_type[<?php echo html_entity_decode($allowance_key) ?>]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
																		<option value=""></option> 
																		<?php
																		foreach($allowance_type as $s){                             
																			?>
																			<option value="<?php echo html_entity_decode($s['type_id']); ?>" <?php if(isset($allowance_value) && $allowance_value['rel_id'] == $s['type_id'] ){echo 'selected';} ?>><?php echo html_entity_decode($s['type_name']); ?></option>

																		<?php } ?>
																	</select>
																</div>
																<div class="col-md-5">
																	<div class="form-group" app-field-wrapper="allowance_expense[$allowance_key]">

																		<?php
																		$input_att2 =[];
																		$input_att2['data-type']='currency';
																		?>
																		<?php $value_allowance = (isset($allowance_value['value']) ? $allowance_value['value'] : ''); ?>

																		<?php  echo render_input('allowance_expense['.$allowance_key.']','amount_of_money', app_format_money($value_allowance, ''), 'text', $input_att2); ?> 

																	</div>

																</div>
																<?php if($allowance_key == 0) {?>
																	<div class="col-md-2 ptop" name="button_allowance_type">
																		<button name="add" class="btn new_contract_allowance_type btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
																	</div>
																<?php }else{ ?>
																	<div class="col-md-2 ptop" name="button_allowance_type">
																		<button name="add" class="btn remove_contract_allowance_type btn-danger" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
																	</div>
																<?php } ?>
															</div>
														<?php } ?>

													</div>
												<?php }else{ ?>
													<div class="col-md-12 contract-allowance-type">
														<div id ="contract-allowancetype" class="row">

															<div class="col-md-5">
																<label for="allowance_type[0]" class="control-label"><?php echo _l('hr_allowance_type'); ?></label>
																<select onchange="OnSelectionChange_allowancetype (this)" name="allowance_type[0]" class="selectpicker" id="allowance_type[0]" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
																	<option value=""></option> 
																	<?php
																	foreach($allowance_type as $s){                             
																		?>
																		<option value="<?php echo html_entity_decode($s['type_id']); ?>" <?php if(isset($contracts) && $contracts[0]['allowance_type'] == $s['type_id'] ){echo 'selected';} ?>><?php echo html_entity_decode($s['type_name']); ?></option>

																	<?php } ?>
																</select>
															</div>
															<div class="col-md-5">
																<?php $value = (isset($expense) ? $expense->expense_name : ''); ?>

																<div class="form-group" app-field-wrapper="allowance_expense[0]">
																	<label for="allowance_expense[0]" class="control-label get_id_row_allowance" value="1"><?php echo _l('hr_amount_of_money') ?></label>
																	<input type="text" id="allowance_expense[0]" name="allowance_expense[0]" class="form-control" value="" data-type="currency">
																</div>

															</div>
															<div class="col-md-2 ptop" name="button_allowance_type">
																<button name="add" class="btn new_contract_allowance_type btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
															</div>
														</div>
													</div>
												<?php } ?>

											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<div class="modal-footer">
													<a href="<?php echo admin_url('hr_profile/job_positions/'.$parent_id); ?>" class="btn btn-default  mright5"><?php echo _l('hr_close'); ?></a>
													<?php if (has_permission('staffmanage_job_position', '', 'create')) { ?>
														<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
													<?php } ?>
												</div>
											</div>
										</div>


										<?php echo form_close(); ?>
									</div>




						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>


</div>

</div>

</div>
<?php echo form_close(); ?>


</div>
<div id="contract_file_data"></div>
<?php init_tail(); ?>
</body>
</html>
