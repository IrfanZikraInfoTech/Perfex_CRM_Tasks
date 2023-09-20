<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">

				<div class="panel_s">

					<div class="panel-body">
						<h4 class="no-margin">
							<?php echo html_entity_decode($training_program->training_name); ?>
						</h4>
						<hr class="hr-panel-heading" />

						<div class="row">
							<div class="col-md-12 panel-padding">
								<table class="table border table-striped table-margintop">
									<tbody>

										<tr class="project-overview">
											<td class="bold" width="20%"><?php echo _l('hr_training_type'); ?></td>
											<td><?php echo html_entity_decode(get_type_of_training_by_id($training_program->training_type)) ; ?></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo _l('hr_training_item'); ?></td>
											<td><?php echo get_training_library_name($training_program->position_training_id) ; ?></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo _l('hr_mint_point'); ?></td>
											<td><?php echo html_entity_decode($training_program->mint_point) ; ?></td>
										</tr>

										<?php if($training_program->additional_training == 'additional_training'){ ?>
											<tr class="project-overview">
												<td class="bold"><?php echo _l('hr_additional_training'); ?></td>
												<td><?php echo hr_get_list_staff_name($training_program->staff_id) ; ?></td>
											</tr>


											<tr class="project-overview">
												<td class="bold"><?php echo _l('hr_training_time'); ?></td>
												<td><?php echo _l('hr_time_to_start').': '. _d($training_program->time_to_start).' -  '._l('hr_time_to_end').': '. _d($training_program->time_to_end) ; ?></td>
											</tr>

										<?php }else{ ?>
											<tr class="project-overview">
												<td class="bold"><?php echo _l('hr__position_apply'); ?></td>
												<td><?php echo hr_get_list_job_position_name($training_program->job_position_id) ; ?></td>
											</tr>
										<?php } ?>


									</tbody>
								</table>
							</div>
							<div class="col-md-12">
								<h4 class="h4-color"><?php echo _l('hr_hr_description'); ?></h4>
								<hr class="hr-color">
								<h5><?php echo html_entity_decode($training_program->description) ; ?></h5>

							</div>

						</div>

						<hr />

						<table class="table dt-table" >
							<thead>
								<th class="sorting_disabled hide"><?php echo _l('ID'); ?></th>
								<th class="sorting_disabled"><?php echo _l('name'); ?></th>
								<th class="sorting_disabled"><?php echo _l('hr_training_result'); ?></th>
								<th class="sorting_disabled"><?php echo _l('date'); ?></th>
								<th class="sorting_disabled"><?php echo _l('hr_status_label'); ?></th>
							</thead>
							<tbody>
								<?php $index=1; ?>

								<?php if(count($training_results) > 0){ ?>
									<?php foreach ($training_results as $key => $value) { ?>

										<tr>
											<td class="hide"><b><?php echo html_entity_decode($index); ?></b></td>
											<td><b><?php echo get_staff_full_name($value['staff_id']); ?></b></td>

											<td>
												<?php

												echo _l('total_point').' / '._l('hr_mint_point') .': '.html_entity_decode(isset($value['training_program_point']) ? $value['training_program_point'] : '') .'/'.html_entity_decode($training_program->mint_point) ;
												?>
											</td>
											<td></td>

											<td>
												<?php 
												if((float)$value['training_program_point'] >= (float)$training_program->mint_point){
													echo ' <span class="label label-success "> '._l('hr_complete').' </span>';
												}else{
													echo ' <span class="label label-primary"> '._l('hr_not_yet_complete').' </span>';
												}
												?>
											</td>
										</tr>
										<?php $index++; ?>
										<?php if(isset($value['staff_training_result'])){ ?>
											<?php  foreach ($value['staff_training_result'] as $r_key => $r_value) { ?>
												<tr>
													<td class="hide"><b><?php echo html_entity_decode($index); ?></b></td>

													<td>
														<a href="<?php echo admin_url('hr_profile/participate/view_staff_training_result/'.$r_value['staff_id'].'/'.$r_value['resultsetid'].'/'.$r_value['training_id'].'/'.$r_value['hash']); ?>"><?php echo '&nbsp;&nbsp;&nbsp;+'. html_entity_decode($r_value['training_name']); ?></a>


													</td>
													<td>
														<?php echo _l('hr_point').': '. html_entity_decode($r_value['total_point']).'/'. html_entity_decode($r_value['total_question_point']); ?>

													</td>
													<td><?php echo html_entity_decode(_dt($r_value['date'])) ?></td>
													<td></td>

												</tr>
												<?php $index++; ?>


											<?php }}} ?>

										<?php } ?>

									</tbody>
								</table>

							<div class="modal-footer">
								<a href="<?php echo admin_url('hr_profile/training?group=training_program'); ?>"  class="btn btn-default mr-2 "><?php echo _l('close'); ?></a>
							</div>
							</div>
						</div>
					</div>



				</div>
			</div>
		</div>

		<?php init_tail(); ?>

	</body>
	</html>
