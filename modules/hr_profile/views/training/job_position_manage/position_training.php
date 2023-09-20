<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-5" id="training-add-edit-wrapper">
				<div class="row">
					<div class="col-md-12">
						<div class="panel_s">
							<?php echo form_open($this->uri->uri_string(), array('id'=>'training_form')); ?>
							<div class="panel-body">
								<h4 class="no-margin">
									<?php echo html_entity_decode($title); ?>
								</h4>
								<hr class="hr-panel-heading" />
								

								<label for="training_type" class="control-label"><?php echo _l('hr_training_type'); ?></label>
								<select name="training_type" class="selectpicker" id="training_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
									<option value=""></option> 
									
									<?php foreach ($type_of_trainings as $key => $value) { ?>
										<option value="<?php echo $value['id'] ?>" <?php if(isset($position_training) && $position_training->training_type == $value['id'] ){echo 'selected';}; ?> ><?php echo $value['name'] ?></option>
									<?php } ?>
								</select>

								<div class="clearfix"></div>
								<br>
								<div class="clearfix"></div>
								<?php $value = (isset($position_training) ? $position_training->subject : ''); ?>
								<?php $attrs = (isset($position_training) ? array() : array('autofocus'=>true)); ?>
								<?php echo render_input('subject','name',$value,'text',$attrs); ?>
								
								<p class="bold"><?php echo _l('hr_hr_description'); ?></p>

								<?php $value = (isset($position_training) ? $position_training->viewdescription : ''); ?>
								<?php echo render_textarea('viewdescription','',$value,array(),array(),'','tinymce-view-description'); ?>                     
								<hr />
								<button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
								<a href="<?php echo admin_url('hr_profile/training?group=training_library'); ?>"  class="btn btn-default pull-right mright5 "><?php echo _l('hr_close'); ?></a>
							</div>
							<?php echo form_close(); ?>
						</div>
					</div>

				</div>
			</div>
			<div class="col-md-7" id="training_questions_wrapper">
				<div class="panel_s">
					<div class="panel-body">
						<?php if(isset($position_training)){ ?>
							<ul class="nav nav-tabs tabs-in-body-no-margin" role="tablist">
								<li role="presentation" class="active">
									<a href="#survey_questions_tab" aria-controls="survey_questions_tab" role="tab" data-toggle="tab">
										<?php echo _l('hr_training_question_string'); ?>
									</a>
								</li>
								<li class="toggle_view">
									<a href="#" onclick="training_toggle_full_view(); return false;" data-toggle="tooltip" data-title="<?php echo _l('toggle_full_view'); ?>">
										<i class="fa fa-expand"></i></a>
									</li>
								</ul>
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane active" id="survey_questions_tab">
										<div class="row mt-3">
											<div class="_buttons">
												<a href="<?php echo site_url('hr_profile/participate/index/'.$position_training->training_id . '/' . $position_training->hash); ?>" target="_blank" class="btn btn-success pull-right mleft10 btn-with-tooltip" data-toggle="tooltip" data-placement="bottom" data-title="<?php echo _l('hr_survey_list_view_tooltip'); ?>"><i class="fa fa-eye"></i></a>
												<?php if(has_permission('staffmanage_training','','edit') || has_permission('staffmanage_training','','create')){ ?>
													<div class="btn-group pull-right">
														<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
															<?php echo _l('hr_survey_insert_field'); ?> <span class="caret"></span>
														</button>
														<ul class="dropdown-menu">
															<li>
																<a href="#" onclick="add_training_question('checkbox',<?php echo html_entity_decode($position_training->training_id); ?>);return false;">
																	<?php echo _l('hr_survey_field_checkbox'); ?></a>
																</li>
															</ul>
														</div>
													</div>
												</div>
											<?php } ?>
											<div class="clearfix"></div>
											<hr />
											<?php
											$question_area = '<ul class="list-unstyled survey_question_callback" id="survey_questions">';
											if(count($position_training->questions) > 0){
												foreach($position_training->questions as $question){
													$question_area .= '<li>';
													$question_area .= '<div class="form-group question">';
													$question_area .= '<div class="row pl-4">';
													$question_area .= '<div class="checkbox checkbox-primary required col-md-2">';
													if($question['required'] == 1){
														$_required = ' checked';
													} else {
														$_required = '';
													}
													$question_area .= '<input type="checkbox" id="req_'.$question['questionid'].'" onchange="update_question(this,\''.$question['boxtype'].'\','.$question['questionid'].');" data-question_required="'.$question['questionid'].'" name="required[]" '.$_required.'>';
													$question_area .= '<label for="req_'.$question['questionid'].'">'._l('hr_survey_question_required').'</label>';
													$question_area .= '</div>';
														 //start input
													$question_area .= '<div class="col-md-4">';
													$question_area .= '<input type="number" onblur="update_question(this,\''.$question['boxtype'].'\','.$question['questionid'].');" data-question-point="'.$question['questionid'].'" class="form-control questionid" value="'.$question['point'].'" title="'._l('hr_score').'..." placeholder="'._l('hr_score').'..."> ';
													$question_area .= '</div>';

													$question_area .= '</div>';

													$question_area .= '<input type="hidden" value="" name="order[]">';
														 // used only to identify input key no saved in database
													$question_area .='<label for="'.$question['questionid'].'" class="control-label display-block">'._l('hr_question_string').'
													<a href="#" onclick="update_question(this,\''.$question['boxtype'].'\','.$question['questionid'].'); return false;" class="pull-right update-question-button"><i class="fa fa-refresh text-success question_update"></i></a>
													<a href="#" onclick="remove_question_from_database(this,'.$question['questionid'].'); return false;" class="pull-right"><i class="fa fa-remove text-danger"></i></a>
													</label>';
													$question_area .= '<input type="text" onblur="update_question(this,\''.$question['boxtype'].'\','.$question['questionid'].');" data-questionid="'.$question['questionid'].'" class="form-control questionid" value="'.$question['question'].'">';
													if($question['boxtype'] == 'textarea'){
														$question_area .= '<textarea class="form-control mtop20" disabled="disabled" rows="6">'._l('hr_survey_question_only_for_preview').'</textarea>';
													} else if($question['boxtype'] == 'checkbox' || $question['boxtype'] == 'radio'){
														$question_area .= '<div class="row">';
														$x = 0;
														foreach($question['box_descriptions'] as $box_description){

															if($box_description['correct'] == 0){
																$correct_checked = ' checked';
															} else {
																$correct_checked = '';
															}

															$box_description_icon_class = 'fa-minus text-danger';
															$box_description_function = 'remove_box_description_from_database(this,'.$box_description['questionboxdescriptionid'].'); return false;';
															if($x == 0){
																$box_description_icon_class = 'fa-plus';
																$box_description_function = 'add_box_description_to_database(this,'.$question['questionid'].','.$question['boxid'].'); return false;';
															}
															$question_area .= '<div class="box_area">';

															$question_area .= '<div class="col-md-12">';
															$question_area .= '<a href="#" class="add_remove_action survey_add_more_box" onclick="'.$box_description_function.'"><i class="fa '.$box_description_icon_class.'"></i></a>';
															$question_area .= '<div class="'.$question['boxtype'].' '.$question['boxtype'].'-primary">';
															$question_area .= '<input type="'.$question['boxtype'].'" onchange="update_answer_question(this,\''.$question['boxtype'].'\','.$question['questionid'].','.$box_description['questionboxdescriptionid'].');"  data-checked-descriptionid="'.$box_description['questionboxdescriptionid'].'" class="data_checked_descriptionid" '.$correct_checked.' />';
															$question_area .= '
															<label>
															<input type="text" onblur="update_question(this,\''.$question['boxtype'].'\','.$question['questionid'].');" data-box-descriptionid="'.$box_description['questionboxdescriptionid'].'" value="'.$box_description['description'].'" class="survey_input_box_description">
															</label>';
															$question_area .= '</div>';
															$question_area .= '</div>';
															$question_area .= '</div>';
															$x++;
														}
												// end box row
														$question_area .= '</div>';
													} else {
														$question_area .= '<input type="text" class="form-control mtop20" disabled="disabled" value="'._l('hr_survey_question_only_for_preview').'">';
													}
													$question_area .= '</div>';
													$question_area .= '</li>';
												}
											}
											$question_area .= '</ul>';
											echo html_entity_decode($question_area);
											?>
										</div>

									<?php } else { ?>
										<p class="no-margin"><?php echo _l('hr_survey_create_first'); ?></p>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php init_tail(); ?>
			<?php 
			require('modules/hr_profile/assets/js/training/position_training_js.php');
			?>
		</body>
	</body>
	</html>
