		<div class="hr_profile_view_profile<?php if($hr_profile_member_add == true ){echo ' hide';} ?> col-md-12">
			<div class="row">
				<?php if($member->staffid == get_staff_user_id() || is_admin() || has_permission('hrm_hr_records', '', 'edit')){ ?>
					<a href="#" onclick="hr_profile_update_staff(<?php echo html_entity_decode($member->staffid); ?>); return false;" class="ml-5 mb-3 font-medium-xs pull-left">
						<?php echo _l('hr_edit'); ?>
						<i class="fa fa-pencil-square-o"></i>
					</a>
				<?php } ?>

			</div>
			<?php if($hr_profile_member_add != true){ $this->load->view('hr_profile/hr_record/includes/staff_profile', $member); } ?>
		</div>

		<div id="modal_wrapper"></div>
