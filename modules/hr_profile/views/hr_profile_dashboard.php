<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$this->load->model('hr_profile/hr_profile_model');
$data_dash = $this->hr_profile_model->get_hr_profile_dashboard_data();

$staff_chart_by_age = json_encode($this->hr_profile_model->staff_chart_by_age());
$contract_type_chart = json_encode($this->hr_profile_model->contract_type_chart());
$staff_departments_chart = json_encode($this->hr_profile_model->staff_chart_by_departments());
$staff_chart_by_job_positions = json_encode($this->hr_profile_model->staff_chart_by_job_positions());
?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="clearfix"></div>
		<div class="row">
			<div class="col-md-12 p-0">
				<div class="panel_s">
					<div class="panel-body">
						<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('hr_hr_profile'); ?>">
							<div class="row">
								<div class="col-md-12">
									<div class="col-md-6">
										<p class="text-dark text-uppercase bold"><?php echo _l('hr_hr_profile_dashboard');?></p>
									</div>
									<div class="col-md-3 pull-right">

									</div>
									<br>
									<hr class="mtop15" />




								</div>
								<div class="col-md-6">
									<div id="staff_departments_chart">
									</div>
								</div>
								<div class="col-md-6">
									<div id="staff_chart_by_job_positions">
									</div>
								</div>

								<div class="col-md-6">
									<div id="staff_chart_by_age">
									</div>
								</div>
								<div class="col-md-6">
									<div id="staff_chart_by_fluctuate_according_to_seniority">
									</div>
								</div>
								<div class="col-md-12">
									<div id="report_by_staffs">
									</div>
								</div>

								<hr class="hr-panel-heading-dashboard">

								<div class="quick-stats-invoices col-md-6"  >
									<div class="top_stats_wrapper min-height-85">
										<a class="text-warning  mbot15">
											<p class="text-uppercase mtop5 min-height-35"><i class="hidden-sm glyphicon glyphicon-remove"></i> <?php echo _l('hr_contract_is_about_to_expire'); ?>
												<a href="<?php echo admin_url('hr_profile/contracts?to_expire') ?>" >
													<i class="pull-right hidden-sm fa fa-eye" data-toggle="tooltip" data-original-title="<?php echo _l('view') ?>"></i>
												</a>
											</p>
											<span class="pull-right bold no-mtop font-size-24"><?php echo html_entity_decode($data_dash['expire_contract']); ?></span>
										</a>
										<div class="clearfix"></div>
										<div class="progress no-margin progress-bar-mini">
											<div class="progress-bar progress-bar-default no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo html_entity_decode($data_dash['expire_contract']); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($data_dash['total_staff']); ?>" style     =     "width:  <?php echo ($data_dash['expire_contract']/$data_dash['total_staff'])*100; ?>%" data-percent=" <?php echo ($data_dash['expire_contract']/$data_dash['total_staff'])*100; ?>%">
											</div>
										</div>
									</div>
								</div>

								<div class="quick-stats-invoices col-md-6">
									<div class="top_stats_wrapper min-height-85">
										<a class="text-danger mbot15">
											<p class="text-uppercase mtop5 min-height-35"><i class="hidden-sm glyphicon glyphicon-remove"></i> <?php echo _l('hr_overdue_contract'); ?>
												<a href="<?php echo admin_url('hr_profile/contracts?overdue_contract') ?>">
													<i class="pull-right hidden-sm fa fa-eye" data-toggle="tooltip" data-original-title="<?php echo _l('view') ?>"></i>
												</a>
											</p>
											<span class="pull-right bold no-mtop font-size-24"><?php echo html_entity_decode($data_dash['overdue_contract']); ?></span>
										</a>
										<div class="clearfix"></div>
										<div class="progress no-margin progress-bar-mini">
											<div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo html_entity_decode($data_dash['overdue_contract']); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($data_dash['total_staff']); ?>" style    =    "width:  <?php echo ($data_dash['overdue_contract']/$data_dash['total_staff'])*100; ?>%" data-percent=" <?php echo ($data_dash['overdue_contract']/$data_dash['total_staff'])*100; ?>%">
											</div>
										</div>
									</div>
								</div>


						<div class="col-md-12">
							
							<h4><p class="padding-5 bold"><?php echo _l('hr_birthday_in_month'); ?></p></h4>
							<hr class="hr-panel-heading-dashboard">
							<table class="table dt-table scroll-responsive">
								<thead>
									<th><?php echo _l('hr_hr_staff_name'); ?></th>
									<th><?php echo _l('staff_dt_email'); ?></th>
									<th><?php echo _l('staff_add_edit_phonenumber'); ?></th>
									<th><?php echo _l('hr_hr_birthday'); ?></th>
									<th><?php echo _l('hr_sex'); ?></th>
									<th><?php echo _l('departments'); ?></th>
								</thead>
								<tbody>

									<?php 
									$list_member_id = [];
									foreach($data_dash['staff_birthday'] as $staff){
										?>

										<tr>
											<td><a href="<?php echo admin_url('hr_profile/member/' . $staff['staffid']); ?>"><?php echo staff_profile_image($staff['staffid'], ['staff-profile-image-small',]); ?></a>
												<a href="<?php echo admin_url('hr_profile/member/' . $staff['staffid']); ?>"><?php echo html_entity_decode($staff['firstname']) . ' ' . $staff['lastname'].' - '.$staff['staff_identifi']; ?></a>
											</td>
											<td><?php echo html_entity_decode($staff['email']); ?></td>
											<td><?php echo html_entity_decode($staff['phonenumber']); ?></td>
											<td><?php echo _d($staff['birthday']); ?></td>
											<td><?php echo _l($staff['sex']); ?></td>
											<td> 
												<?php
												$departments = $this->departments_model->get_staff_departments($staff['staffid']);
												if(isset($departments[0])){
													$team = $this->hr_profile_model->hr_profile_get_department_name($departments[0]['departmentid']);
													$str = '';
													$j = 0;
													foreach ($team as $value) {
														$j++;
														$str .= '<span class="label label-tag tag-id-1"><span class="tag">'.$value.'</span><span class="hide">, </span></span>&nbsp';
														if($j%2 == 0){
															$str .= '<br><br/>';
														}
													}
													echo html_entity_decode($str);
												}
												else{
													echo '';
												} ?>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>


							<h4><p class="padding-5 bold"><?php echo _l('hr_unfinished_staff_received'); ?></p></h4>
							<hr class="hr-panel-heading-dashboard">
							<?php
							$table_data = array(
								_l('staff_id'),
								_l('hr_hr_staff_name'),
								_l('hr_hr_job_position'),
								_l('departments'),
								_l('hr_hr_finish'));

							
							render_datatable($table_data,'table_staff');
							?>

						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>
<div class="clearfix"></div>
<?php init_tail();
require('modules/hr_profile/assets/js/hr_profile_dashboard_js.php');
?>



