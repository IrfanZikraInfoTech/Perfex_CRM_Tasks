

<div class="row">
	<?php if (($staff_p->staffid == get_staff_user_id() || is_admin()) && !$this->input->get('notifications')) {?>

      <div class="col-md-12">

          <div class="panel-body no-padding-bottom">

           <?php $this->load->view('hr_record/includes/stats');?>

         </div>

     </div>

     <?php }?>



	<br>

	<br>

	<?php

if ($member->active == 0) {?>

		<div class="alert alert-danger text-center"><?php echo _l('staff_profile_inactive_account'); ?></div>

		<hr />

	<?php }?>

	<div class="col-md-12 pl-0" >

		<div class="col-md-5">

			<div class="row">

				<div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">

					<div class="card box-shadow-0 overflow-hidden">

					<?php if ($member->status_work == 'working') { ?>
							<div class="ribbon ribbon-top-right text-info"><span class="bg_working"><?php echo _l('hr_working'); ?></span></div>
								<?php }
						 elseif ($member->status_work == 'maternity_leave') { ?>
									<div class="ribbon ribbon-top-right text-info"><span class="bg_maternity_leave"><?php echo _l('hr_maternity_leave'); ?></span></div>
								<?php }
						 elseif ($member->status_work == 'probation') { ?>
									<div class="ribbon ribbon-top-right text-info"><span class="bg_probation"><?php echo _l('Probation'); ?></span></div>
								<?php } 
						elseif ($member->status_work == 'permanent') { ?>
									<div class="ribbon ribbon-top-right text-info"><span class="bg_permanent"><?php echo _l('Permanent'); ?></span></div>
								<?php } 
						elseif ($member->status_work == 'contract') { ?>
									<div class="ribbon ribbon-top-right text-info"><span class="bg_contract"><?php echo _l('Contract'); ?></span></div>
								<?php } 
						elseif ($member->status_work == 'temporary') { ?>
									<div class="ribbon ribbon-top-right text-info"><span class="bg-pink"><?php echo _l('Temporary'); ?></span></div>
								<?php } 
						elseif ($member->status_work == 'notice_period') { ?>
									<div class="ribbon ribbon-top-right text-info"><span class="bg_notice_period"><?php echo _l('Notice period'); ?></span></div>
								<?php }
						elseif ($member->status_work == 'terminated') { ?>
									<div class="ribbon ribbon-top-right text-info"><span class="bg_terminated"><?php echo _l('Terminated'); ?></span></div>
								<?php } 
						elseif ($member->status_work == 'resigned') { ?>
									<div class="ribbon ribbon-top-right text-info"><span class="bg_resigned"><?php echo _l('Resigned'); ?></span></div>
								<?php }?>
						<div class="card-body">

							<div class="text-center">

								<div class="userprofile">

									<div class="userpic  brround mb-3 ">

										<?php echo staff_profile_image($member->staffid, array('staff-profile-image-thumb mx-auto'), 'thumb'); ?>

									</div>

									<h3 class="username mb-2"><?php echo html_entity_decode($member->firstname . ' ' . $member->lastname); ?></h3>

									<div class="socials text-center mt-3">

									<a href="https://www.facebook.com/<?php echo html_escape($member->facebook); ?>" class="btn btn-circle" target="_blank">
										<i class="fa-brands fa-facebook" aria-hidden="true"></i>
									</a>
									
									<a href="<?php echo html_escape($member->linkedin); ?>" class="btn btn-circle" target="_blank">
										<i class="fa-brands fa-linkedin"></i>
									</a>

										<a href="skype:<?php echo html_escape($member->skype); ?>?chat" class="btn btn-circle">
											<i class="fa-brands fa-skype"></i>
										</a>

										<a href="mailto:{email} " class="btn btn-circle">

											<i class="fa fa-envelope"></i>

										</a>

									</div>

								</div>

							</div>

						</div>

						<br>

					</div>

				</div>

			</div>

			<div class="card panel-theme">

				<div class="card-body no-padding">

					<ul class="list-group no-margin">

						<li class="list-group-item"><i class="fa fa-envelope mr-4"></i> <?php echo html_entity_decode($member->email) ?></li>

						<li class="list-group-item"><i class="fa fa-phone mr-4"></i> <?php echo html_entity_decode($member->phonenumber) ?></li>

						<li class="list-group-item"><i class="fa fa-graduation-cap mr-4"></i> <?php echo html_entity_decode($member->literacy) ?></li>

						<li class="list-group-item"><i class="fa fa-intersex mr-4"></i> <?php echo html_entity_decode(_l($member->sex)) ?></li>

					</ul>

				</div>

				<div class="card  ">
					<div class="card-body ml-3">
						<?php if (count($staff_departments) > 0) { ?>
							<div class="form-group mt-3">
								<?php foreach ($departments as $department) {
									foreach ($staff_departments as $staff_department) {
										if ($staff_department['departmentid'] == $department['departmentid']) { ?>
											<div class="d-flex align-items-center mb-2">
												<strong class="mr-2">Department:</strong>
												<span><?php echo html_entity_decode($staff_department['name']); ?></span>
											</div>
										<?php }
									}
								} ?>
							</div>
						<?php } else { ?>
							<p>No departments assigned.</p>
						<?php } ?>
					</div>
				</div>
				<div class="card-header d-flex ml-3 ">
					<h4 class="card-title ">
						<?php echo _l('hr_team_manage') . ':  ' . staff_profile_image($member->team_manage, ['staff-profile-image-small ml-2']) . '  ' . get_staff_full_name($member->team_manage); ?>
					</h4>
				</div>

			</div>
		</div>

		<div class="col-md-7">



			<div class="col-md-12">

				<h4 class="bold"><?php echo _l('hr_general_infor'); ?></h4>



				<table class="table border table-striped ">

					<tbody>

						<tr class="project-overview">

							<td class="bold" width="40%"><?php echo _l('hr_hr_code'); ?></td>

							<td>
								<?php
								// Fetch the custom prefix from the settings
								$custom_prefix = get_option('custom_prefix'); 

								// Assuming $member is your staff member object and it contains staffid
								$hr_codes = (isset($member->staffid)) ? $custom_prefix . $member->staffid : '';

								echo html_entity_decode($hr_codes);
								?>
							</td>

						</tr>

						<tr class="project-overview">

							<td class="bold" width="30%"><?php echo _l('hr_hr_staff_name'); ?></td>

							<td><?php echo html_entity_decode($member->firstname . ' ' . $member->lastname); ?></td>

						</tr>

						<tr class="project-overview">

							<td class="bold"><?php echo _l('hr_sex'); ?></td>

							<td><?php echo _l($member->sex); ?></td>

						</tr>
						<tr class="project-overview">
							<td class="bold"><?php echo _l('Work Experience In Zikra InfoTech'); ?>:</td>
							<td>
								<?php
								if (isset($member->datecreated)) {
									$date_created = new DateTime($member->datecreated);
									$current_date = new DateTime();
									$interval = $date_created->diff($current_date);

									// Calculate total months of experience
									$years = $interval->y;
									$months = $interval->m + ($years * 12);
									$days = $interval->d;

									// Displaying the result
									echo sprintf(_l('%d months and %d days'), $months, $days);
								} else {
									echo _l('No Date Provided');
								}
								?>
							</td>
						</tr>


						<tr class="project-overview">

							<td class="bold" ><?php echo _l('hr_hr_birthday'); ?></td>

							<td><?php echo _d($member->birthday); ?></td>

						</tr>

						<tr class="project-overview">

							<td class="bold"><?php echo _l('staff_add_edit_phonenumber'); ?></td>

							<td><?php echo html_entity_decode($member->phonenumber); ?></td>

						</tr>

						<tr class="project-overview">

							<td class="bold"><?php echo _l('country'); ?></td>

							<td><?php echo html_entity_decode($member->country); ?></td>

						</tr>

						<tr class="project-overview">

							<td class="bold" width="40%"><?php echo _l('hr_status_label'); ?></td>

							<td>

								<?php echo html_entity_decode(_l($member->status_work)) ?>

							</td>

						</tr>

						<tr class="project-overview">

							<td class="bold" width="40%"><?php echo _l('hr_hr_job_position'); ?></td>

							<td>

								<?php

									if ($member->job_position > 0) {

										$job_position_name = html_entity_decode(hr_profile_get_job_position_name($member->job_position))

										?>

										<a href="<?php echo admin_url() . 'hr_profile/job_position_view_edit/' . $member->job_position; ?>"><?php echo $job_position_name; ?></a>

										<?php

										}



										?>

							</td>

						</tr>

						<tr class="project-overview">

							<td class="bold" width="40%"><?php echo _l('hr_hr_literacy'); ?></td>

							<td>

								<?php echo html_entity_decode(_l($member->literacy)) ?>

							</td>

						</tr>

						<tr class="project-overview">

							<td class="bold"><?php echo _l('hr_hr_religion'); ?></td>

							<td><?php echo html_entity_decode($member->religion); ?></td>

						</tr>

						<tr class="project-overview">

							<td class="bold"><?php echo _l('Nationality'); ?></td>

							<td><?php echo html_entity_decode($member->nation); ?></td>

						</tr>
						<tr class="project-overview">

							<td class="bold"><?php echo _l('hr_hr_marital_status'); ?></td>

							<td><?php echo _l($member->marital_status); ?></td>

						</tr>
					</tbody>

				</table>

			</div>





			<div class="col-md-12">

				<h4><?php echo _l('hr_staff_profile_related_info'); ?></h4>

				<table class="table border table-striped ">

					<tbody>



						<tr class="project-overview">

							<td class="bold"><?php echo _l('hr_citizen_identification'); ?></td>

							<td><?php echo html_entity_decode($member->identification); ?></td>

						</tr>

						<tr class="project-overview">

							<td class="bold" width="40%"><?php echo _l('hr_license_date'); ?></td>

							<td><?php echo _d($member->days_for_identity); ?></td>

						</tr>

						<tr class="project-overview">

							<td class="bold"><?php echo _l('hr_hr_birthplace'); ?></td>

							<td><?php echo _l($member->birthplace); ?></td>

						</tr>

						<tr class="project-overview">

							<td class="bold"><?php echo _l('hr_current_address'); ?></td>

							<td><?php echo html_entity_decode($member->current_address); ?></td>

						</tr>

						<tr class="project-overview">

							<td class="bold"><?php echo _l('hr_hr_resident'); ?></td>

							<td><?php echo html_entity_decode($member->resident); ?></td>

						</tr>

						<tr class="project-overview">

							<td class="bold"><?php echo _l('hr_hr_home_town'); ?></td>

							<td><?php echo html_entity_decode($member->home_town); ?></td>

						</tr>





						<tr class="project-overview">

							<td class="bold"><?php echo _l('hr_bank_account_number'); ?></td>

							<td><?php echo html_entity_decode($member->account_number); ?></td>

						</tr>

						<tr class="project-overview">

							<td class="bold"><?php echo _l('hr_bank_account_name'); ?></td>

							<td><?php echo html_entity_decode($member->name_account); ?></td>

						</tr>

						<tr class="project-overview">

							<td class="bold"><?php echo _l('hr_bank_name'); ?></td>

							<td><?php echo html_entity_decode($member->issue_bank); ?></td>

						</tr>

						<tr class="project-overview">

							<td class="bold"><?php echo _l('hr_Personal_tax_code'); ?></td>

							<td><?php echo html_entity_decode($member->Personal_tax_code); ?></td>

						</tr>

					</tbody>

				</table>

			</div>

			<div class="col-md-12">

				<br>

				<h4><?php echo _l('hr_orther_infor'); ?></h4>

				<h5 class="text-justify"><?php echo html_entity_decode($member->orther_infor); ?></h5>

			</div>





		</div>

	</div>

</div>

