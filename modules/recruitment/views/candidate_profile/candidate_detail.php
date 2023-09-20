<?php init_head();?>
<div id="wrapper">
   <div class="content">
        <div class="panel_s">
           <div class="panel-body">
           	<div class="col-md-12">
           		<?php
$get_base_currency = get_base_currency();
$current_id = '';
if ($get_base_currency) {
	$current_id = $get_base_currency->id;
}

?>
           		<h4 class="bold col-md-5">
           		<?php echo '#' . $candidate->candidate_code . ' - ' . $candidate->candidate_name . ' ' . $candidate->last_name; ?>
           		</h4>
                 <a href="Javascript:void(0);" id="toggle_popup_approval" class="btn btn-success display-block pull-right"><i class="fa fa-user-md"></i><?php echo ' ' . _l('rec_care') . ' '; ?><i class="fa fa-caret-down"></i></a>
                 <ul id="popup_approval" class="dropdown-menu dropdown-menu-right min-width-440">
                  <li>
                    <br>
                    <div class="col-md-12">
                      	<a href="#" onclick="interview(); return false;" class="btn btn-info pull-right display-block mright5 interview-background"><i class="fa fa-microphone"></i><?php echo ' ' . _l('interview'); ?></a>
		              	<a href="#" onclick="test(); return false;" class="btn btn-info pull-right display-block mright5 test-background"><i class="fa fa-forward"></i><?php echo ' ' . _l('test'); ?></a>
		              	<a href="#" onclick="call(); return false;" class="btn btn-info pull-right display-block mright5 call-background"><i class="fa fa-phone"></i><?php echo ' ' . _l('call'); ?></a>
		              	<a href="#" onclick="sendmail(); return false;" class="btn btn-info pull-right display-block mright5 send_mail-background"><i class="fa fa-envelope"></i><?php echo ' ' . _l('send_mail'); ?></a>

                    </div>
                    <br>&nbsp;<br/>
                  </li>
                 </ul>
                  <a href="#" onclick="send_mail_candidate(); return false;" class="btn btn-info pull-right display-block mright5" ><i class="fa fa-envelope"></i><?php echo ' ' . _l('send_mail'); ?></a>

              	  <a href="#" class="btn btn-warning pull-right mright5" data-toggle="modal" data-target="#candidate_rating"><i class="fa fa-star"></i><?php echo ' ' . _l('rate_candidate'); ?></a>
              	  <div class="col-md-3 pull-right">
              	  <select name="change_status" id="change_status" onchange="change_status_candidate(this,<?php echo html_entity_decode($candidate->id); ?>); return false;" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('change_status_to'); ?>">
		            <option value=""></option>
		            <option value="1" class="<?php if ($candidate->status == 1) {echo 'hide';}?>"><?php echo _l('application'); ?></option>
		            <option value="2" class="<?php if ($candidate->status == 2) {echo 'hide';}?>"><?php echo _l('potential'); ?></option>
		            <option value="3" class="<?php if ($candidate->status == 3) {echo 'hide';}?>"><?php echo _l('interview'); ?></option>
		            <option value="4" class="<?php if ($candidate->status == 4) {echo 'hide';}?>"><?php echo _l('won_interview'); ?></option>
		            <option value="5" class="<?php if ($candidate->status == 5) {echo 'hide';}?>"><?php echo _l('send_offer'); ?></option>
		            <option value="6" class="<?php if ($candidate->status == 6) {echo 'hide';}?>"><?php echo _l('elect'); ?></option>
		            <option value="7" class="<?php if ($candidate->status == 7) {echo 'hide';}?>"><?php echo _l('non_elect'); ?></option>
		            <option value="8" class="<?php if ($candidate->status == 8) {echo 'hide';}?>"><?php echo _l('unanswer'); ?></option>
		            <option value="9" class="<?php if ($candidate->status == 9) {echo 'hide';}?>"><?php echo _l('transferred'); ?></option>
		            <option value="10" class="<?php if ($candidate->status == 10) {echo 'hide';}?>"><?php echo _l('freedom'); ?></option>
		          </select>
		          </div>
           	</div>
           	<div class="col-md-12">
		       	<div class="horizontal-scrollable-tabs preview-tabs-top">
		             <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
		             <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
		             <div class="horizontal-tabs">
		             <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
		               <li role="presentation" class="active">
		                   <a href="#detail" aria-controls="detail" role="tab" data-toggle="tab" aria-controls="detail">
		                   <span class="glyphicon glyphicon-align-justify"></span>&nbsp;<?php echo _l('detail'); ?>
		                   </a>
		                </li>
		                <li role="presentation">
		                   <a href="#history_recruitment" aria-controls="history_recruitment" role="tab" data-toggle="tab" aria-controls="history_recruitment">
		                   <i class="fa fa-calendar"></i>&nbsp;<?php echo _l('history_recruitment'); ?>
		                   </a>
		                </li>
		                <li role="presentation">
		                   <a href="#capacity_profile" aria-controls="capacity_profile" role="tab" data-toggle="tab" aria-controls="capacity_profile">
		                   <i class="fa fa-address-card-o"></i>&nbsp;<?php echo _l('capacity_profile'); ?>
		                   </a>
		                </li>
		                <li role="presentation">
		                   <a href="#attachment" aria-controls="attachment" role="tab" data-toggle="tab" aria-controls="attachment">
		                   <i class="fa fa-paperclip"></i>&nbsp;<?php echo _l('attachment'); ?>
		                   </a>
		                </li>
		              </ul>
		             </div>
		        </div>
		        <div class="tab-content">
                  <div role="tabpanel" class="tab-pane active" id="detail">

              		<p class="bold margin-top-15 general-infor-color"><?php echo _l('general_infor'); ?></p>
              		<hr class="margin-top-10 general-infor-hr"/>

                  	<div class="col-md-2 padding-left-right-0">
                  		<div class="container">
                            <div class="picture-container pull-left">
                                <div class="picture pull-left">
                                    <img class="width-height-160" src="<?php if (isset($candidate->avar)) {echo site_url(RECRUITMENT_PATH . 'candidate/avartar/' . $candidate->id . '/' . $candidate->avar->file_name);} else {echo site_url(RECRUITMENT_PATH . 'none_avatar.jpg');}?>" class="picture-src" id="wizardPicturePreview" title="">

                                </div>
                             </div>
                        </div>
                  	</div>
                  	<div class="col-md-5 padding-left-right-0">
                  		<table class="table border table-striped margin-top-0">
				            <tbody>
				               <tr class="project-overview">
				                  <td class="bold" width="30%"><?php echo _l('full_name'); ?></td>
				                  <td><?php echo html_entity_decode($candidate->candidate_name . ' ' . $candidate->last_name); ?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('gender'); ?></td>
				                  <td><?php echo _l($candidate->gender); ?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('nation'); ?></td>
				                  <td><?php echo html_entity_decode($candidate->nation); ?></td>
				               </tr>

				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('marital_status'); ?></td>
				                  <td><?php echo _l($candidate->marital_status); ?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('height'); ?></td>
				                  <td><?php echo html_entity_decode($candidate->height); ?></td>
				               </tr>

				            </tbody>
				        </table>
                  	</div>
                  	<div class="col-md-5 padding-left-right-0">
                  		<table class="table border table-striped margin-top-0">
				            <tbody>
				            	<tr class="project-overview">
				                  <td class="bold"><?php echo _l('candidate_code'); ?></td>
				                  <td><?php echo html_entity_decode($candidate->candidate_code); ?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold" width="30%"><?php echo _l('birthday'); ?></td>
				                  <td><?php echo _d($candidate->birthday); ?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('nationality'); ?></td>
				                  <td><?php echo html_entity_decode($candidate->nationality); ?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('religion'); ?></td>
				                  <td><?php echo html_entity_decode($candidate->religion); ?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('weight'); ?></td>
				                  <td><?php echo html_entity_decode($candidate->weight); ?></td>
				               </tr>

				            </tbody>
				        </table>
                  	</div>

                  	<p class="bold other_infor-style"><?php echo _l('other_infor'); ?></p>
              		<hr class="other_infor-hr" />

              		<div class="col-md-6 padding-left-right-0">
                  		<table class="table border table-striped margin-top-0">
				            <tbody>
				               <tr class="project-overview">
				                  <td class="bold" width="30%"><?php echo _l('campaign'); ?></td>
				                  <td><?php
$cp = get_rec_campaign_hp($candidate->rec_campaign);
$datas = '';
if (isset($cp)) {
	$datas = '<a href="' . admin_url('recruitment/recruitment_campaign/' . $cp->cp_id) . '">' . $cp->campaign_code . ' - ' . $cp->campaign_name . '</a>';
}
echo html_entity_decode($datas);
?>
            					  </td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('submission_date'); ?></td>
				                  <td><?php echo _d($candidate->date_add); ?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('identification'); ?></td>
				                  <td><?php echo html_entity_decode($candidate->identification); ?></td>
				               </tr>

				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('birthplace'); ?></td>
				                  <td><?php echo _l($candidate->birthplace); ?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('resident'); ?></td>
				                  <td><?php echo html_entity_decode($candidate->resident); ?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('phonenumber'); ?></td>
				                  <td><?php echo html_entity_decode($candidate->phonenumber); ?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('alternate_contact_number'); ?></td>
				                  <td><?php echo html_entity_decode($candidate->alternate_contact_number); ?></td>
				               </tr>

				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('skype'); ?></td>
				                  <td><a href="<?php echo html_entity_decode($candidate->skype); ?>"><?php echo html_entity_decode($candidate->skype); ?></a></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('skill_name'); ?></td>
				                  <td><?php echo html_entity_decode($skill_name); ?></td>
				               </tr>

				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('introduce_yourself'); ?></td>
				                  <td><?php echo html_entity_decode($candidate->introduce_yourself); ?></td>
				               </tr>
				            </tbody>
				        </table>
                  	</div>

                  	<div class="col-md-6 padding-left-right-0">
                  		<table class="table border table-striped margin-top-0">
				            <tbody>
				               <tr class="project-overview">
				                  <td class="bold" width="30%"><?php echo _l('status'); ?></td>
				                  <td><?php echo get_status_candidate($candidate->status); ?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('desired_salary'); ?></td>
				                  <td><?php echo app_format_money($candidate->desired_salary, $current_id); ?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('days_for_identity'); ?></td>
				                  <td><?php echo _d($candidate->days_for_identity); ?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('home_town'); ?></td>
				                  <td><?php echo html_entity_decode($candidate->home_town); ?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('current_accommodation'); ?></td>
				                  <td><?php echo html_entity_decode($candidate->current_accommodation); ?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('email'); ?></td>
				                  <td><a href="mailto:<?php echo html_entity_decode($candidate->email); ?>"><?php echo html_entity_decode($candidate->email); ?></a></td>

				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('facebook'); ?></td>
				                  <td><a href="<?php echo html_entity_decode($candidate->facebook); ?>"><?php echo html_entity_decode($candidate->facebook); ?></a></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('linkedin'); ?></td>
				                  <td><a href="<?php echo html_entity_decode($candidate->linkedin); ?>"><?php echo html_entity_decode($candidate->linkedin); ?></a></td>
				               </tr>

				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('interests'); ?></td>
				                  <td><?php echo html_entity_decode($candidate->interests); ?></td>
				               </tr>

				               <tr class="project-overview">
				                  <td>  &nbsp;</td>
				                  <td></td>
				               </tr>
				            </tbody>
				        </table>
                  	</div>
                  	<div class="row col-md-12">
              		<p class="bold other_infor-style"><?php echo _l('work_experience'); ?></p>
              		<hr class="other_infor-hr" />

              		<?php if (count($candidate->work_experience) > 0) {?>
              			<table class="table dt-table margin-top-0">
              				<thead>
              					<th><?php echo _l('from_date'); ?></th>
              					<th><?php echo _l('to_date'); ?></th>
              					<th><?php echo _l('company'); ?></th>
              					<th><?php echo _l('position'); ?></th>
              					<th><?php echo _l('contact_person'); ?></th>
              					<th><?php echo _l('salary'); ?></th>
              					<th><?php echo _l('reason_quitwork'); ?></th>
              					<th><?php echo _l('job_description'); ?></th>
              				</thead>
				            <tbody>
				            	$current_id
				            <?php foreach ($candidate->work_experience as $we) {?>
				               <tr class="project-overview">
				                  <td><?php echo _d($we['from_date']); ?></td>
				                  <td><?php echo _d($we['to_date']); ?></td>
				                  <td><?php echo html_entity_decode($we['company']); ?></td>
				                  <td><?php echo html_entity_decode($we['position']); ?></td>
				                  <td><?php echo html_entity_decode($we['contact_person']); ?></td>
				                  <td><?php echo html_entity_decode(app_format_money($we['salary'], $current_id)); ?></td>
				                  <td><?php echo html_entity_decode($we['reason_quitwork']); ?></td>
				                  <td><?php echo html_entity_decode($we['job_description']); ?></td>
				               </tr>
				           <?php }?>
				            </tbody>
				        </table>
				    <?php } else {?>
				    	<p><?php echo _l('no_result'); ?></p>
				   	<?php }?>



              		<p class="bold other_infor-style"><?php echo _l('literacy'); ?></p>
              		<hr class="other_infor-hr" />
              		<?php if (count($candidate->literacy) > 0) {?>
              			<table class="table dt-table margin-top-0">
              				<thead>
              					<th><?php echo _l('from_date'); ?></th>
              					<th><?php echo _l('to_date'); ?></th>
              					<th><?php echo _l('diploma'); ?></th>
              					<th><?php echo _l('training_places'); ?></th>
              					<th><?php echo _l('specialized'); ?></th>
              					<th><?php echo _l('training_form'); ?></th>

              				</thead>
				            <tbody>
				            <?php foreach ($candidate->literacy as $we) {?>
				               <tr class="project-overview">
				                  <td><?php echo _d($we['literacy_from_date']); ?></td>
				                  <td><?php echo _d($we['literacy_to_date']); ?></td>
				                  <td><?php echo html_entity_decode(_l($we['diploma'])); ?></td>
				                  <td><?php echo html_entity_decode($we['training_places']); ?></td>
				                  <td><?php echo html_entity_decode($we['specialized']); ?></td>
				                  <td><?php echo html_entity_decode($we['training_form']); ?></td>

				               </tr>
				           <?php }?>
				            </tbody>
				        </table>
				    <?php } else {?>
				    	<p><?php echo _l('no_result'); ?></p>
				   	<?php }?>
              		<p class="bold other_infor-style"><?php echo _l('family_infor'); ?></p>
              		<hr class="other_infor-hr" />
              		<?php if (count($candidate->family_infor) > 0) {?>
              			<table class="table dt-table margin-top-0">
              				<thead>
              					<th><?php echo _l('relationship'); ?></th>
              					<th><?php echo _l('name'); ?></th>
              					<th><?php echo _l('birthday'); ?></th>
              					<th><?php echo _l('job'); ?></th>
              					<th><?php echo _l('address'); ?></th>
              					<th><?php echo _l('phone'); ?></th>

              				</thead>
				            <tbody>
				            <?php foreach ($candidate->family_infor as $we) {?>
				               <tr class="project-overview">
				                  <td><?php echo html_entity_decode($we['relationship']); ?></td>
				                  <td><?php echo html_entity_decode($we['name']); ?></td>
				                  <td><?php echo _d($we['fi_birthday']); ?></td>
				                  <td><?php echo html_entity_decode($we['job']); ?></td>
				                  <td><?php echo html_entity_decode($we['address']); ?></td>
				                  <td><?php echo html_entity_decode($we['phone']); ?></td>

				               </tr>
				           <?php }?>
				            </tbody>
				        </table>
				    <?php } else {?>
				    	<p><?php echo _l('no_result'); ?></p>
				   	<?php }?>
				   	</div>

                  </div>

                  <div role="tabpanel" class="tab-pane" id="history_recruitment">
              		<p class="bold other_infor-style"><?php echo _l('campaign_has_joined'); ?></p>
              		<hr class="other_infor-hr" />
              		<?php if ($candidate->rec_campaign > 0) {
	?>
              			<table class="table dt-table margin-top-0">
              				<thead>
              					<th><?php echo _l('campaign'); ?></th>
              					<th><?php echo _l('status'); ?></th>
              					<th><?php echo _l('submission_date'); ?></th>
              					<th><?php echo _l('desired_salary'); ?></th>
              				</thead>
				            <tbody>

				               <tr class="project-overview">
				                  <td><?php
$cp = get_rec_campaign_hp($candidate->rec_campaign);
	$datas = '';
	if (isset($cp)) {
		$datas = '<a href="' . admin_url('recruitment/recruitment_campaign/' . $cp->cp_id) . '">' . $cp->campaign_code . ' - ' . $cp->campaign_name . '</a>';
	}
	echo html_entity_decode($datas);
	?></td>
				                  <td><?php echo get_status_candidate($candidate->status); ?></td>
				                  <td><?php echo _d($candidate->date_add); ?></td>
				                  <td><?php echo app_format_money($candidate->desired_salary, $current_id); ?></td>
				               </tr>
				            </tbody>
				        </table>
              		<?php } else {?>
              			<p><?php echo _l('no_result'); ?></p>
              		<?php }?>

              		<p class="bold other_infor-style"><?php echo _l('interview_schedule'); ?></p>
              		<hr class="other_infor-hr" />
              		<table class="table dt-table margin-top-0">
          				<thead>
          					<th><?php echo _l('add_from'); ?></th>
          					<th><?php echo _l('interview_schedules_name'); ?></th>
          					<th><?php echo _l('rec_time'); ?></th>
          					<th><?php echo _l('interview_day'); ?></th>
          					<th><?php echo _l('recruitment_campaign'); ?></th>
          					<th><?php echo _l('interviewer'); ?></th>
          					<th><?php echo _l('date_add'); ?></th>
          				</thead>
			            <tbody>
			            	<?php foreach ($list_interview as $li) {
	?>
			            		<tr>
			            			<td>
			            				<?php
$_data = '<a href="' . admin_url('staff/profile/' . $li['added_from']) . '">' . staff_profile_image($li['added_from'], [
		'staff-profile-image-small',
	]) . '</a>';
	$_data .= ' <a href="' . admin_url('staff/profile/' . $li['added_from']) . '">' . get_staff_full_name($li['added_from']) . '</a>';
	echo html_entity_decode($_data);
	?>
			            			</td>
			            			<td><?php echo html_entity_decode($li['is_name']) ?></td>
			            			<td><?php echo html_entity_decode($li['from_time'] . ' - ' . $li['to_time']); ?></td>
			            			<td><?php echo _d($li['interview_day']); ?></td>
			            			<td><?php
$cp = get_rec_campaign_hp($li['campaign']);
	if ($li['campaign'] != '' && $li['campaign'] != 0) {
		if (isset($cp)) {
			$_data = $cp->campaign_code . ' - ' . $cp->campaign_name;
		} else {
			$_data = '';
		}
	} else {
		$_data = '';

	}

	echo html_entity_decode($_data);
	?>

			            			</td>
			            			<td>
			            				<?php
$inv = explode(',', $li['interviewer']);
	$ata = '';
	foreach ($inv as $iv) {
		$ata .= '<a href="' . admin_url('staff/profile/' . $iv) . '">' . staff_profile_image($iv, [
			'staff-profile-image-small mright5',
		], 'small', [
			'data-toggle' => 'tooltip',
			'data-title' => get_staff_full_name($iv),
		]) . '</a>';
	}
	echo html_entity_decode($ata);
	?>
			            			</td>
			            			<td><?php echo _d($li['added_date']); ?></td>
			            		</tr>
			            	<?php }?>
			            </tbody>
				    </table>

              		<p class="bold other_infor-style"><?php echo _l('care_history'); ?></p>
              		<hr class="other_infor-hr" />
              		<?php if ($candidate->care > 0) {
	?>
              			<table class="table dt-table margin-top-0" >
              				<thead>
              					<th><?php echo _l('type'); ?></th>
              					<th><?php echo _l('caregiver'); ?></th>
              					<th><?php echo _l('rec_time'); ?></th>
              					<th><?php echo _l('result'); ?></th>
              					<th><?php echo _l('description'); ?></th>
              				</thead>
				            <tbody>

				           	<?php foreach ($candidate->care as $care) {
		?>
				               <tr class="project-overview">
				                  <td><?php echo _l($care['type']); ?></td>
				                  <td>
				                  	<?php
$_data = '<a href="' . admin_url('staff/profile/' . $care['add_from']) . '">' . staff_profile_image($care['add_from'], [
			'staff-profile-image-small',
		]) . '</a>';
		$_data .= ' <a href="' . admin_url('staff/profile/' . $care['add_from']) . '">' . get_staff_full_name($care['add_from']) . '</a>';
		echo html_entity_decode($_data);
		?>
				                  </td>
				                  <td><?php echo _d($care['care_time']); ?></td>
				                  <td><?php echo html_entity_decode($care['care_result']); ?></td>
				                  <td><?php echo html_entity_decode($care['description']); ?></td>
				               </tr>
				     		<?php }?>
				            </tbody>
				        </table>
              		<?php } else {?>
              			<p><?php echo _l('no_result'); ?></p>
              		<?php }?>

                  </div>

                  <div role="tabpanel" class="tab-pane" id="capacity_profile">

                  	<div class="row col-md-12">
              			<p class="bold other_infor-style"><?php echo _l('candidate_evaluation'); ?></p>
              			<hr class="other_infor-hr" />
              		</div>

              		<div class="col-md-6">
              			<?php if (count($cd_evaluation) > 0) {
	?>
              			<table class="table border table-striped margin-top-0">
				            <tbody>
				               <tr class="project-overview">
				                  <td class="bold" width="30%"><?php echo _l('assessor'); ?></td>
				                  <td><?php
$_data = '<a href="' . admin_url('staff/profile/' . $assessor) . '">' . staff_profile_image($assessor, [
		'staff-profile-image-small',
	]) . '</a>';
	$_data .= ' <a href="' . admin_url('staff/profile/' . $assessor) . '">' . get_staff_full_name($assessor) . '</a>';
	echo html_entity_decode($_data);
	?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('evaluation_date'); ?></td>
				                  <td><?php echo _d($evaluation_date); ?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('avg_score'); ?></td>
				                  <td><?php echo html_entity_decode($avg_score); ?></td>
				               </tr>
				               <tr class="project-overview">
				                  <td class="bold"><?php echo _l('feedback'); ?></td>
				                  <td><?php echo html_entity_decode($feedback); ?></td>
				               </tr>

				            </tbody>
				        </table>
				    <?php } else {?>
				    	<p class="bold text-danger" ><?php echo _l('none_evaluation_for_cd'); ?></p>
				    <?php }?>
              		</div>
              		<div class="col-md-6">

              		</div>
              		<div class="row col-md-12">
              			<p class="bold other_infor-style"><?php echo _l('result'); ?></p>
              			<hr class="other_infor-hr" />
              			<table class="table dt-table margin-top-0">
              				<thead>
              					<th><?php echo _l('criteria_name'); ?></th>
              					<th><?php echo _l('proportion'); ?></th>
              					<th><?php echo _l('rec_score'); ?></th>
              					<th><?php echo _l('result'); ?></th>

              				</thead>
				            <tbody>
				           		<?php if (count($data_group) > 0) {
	$count_gr = 1;
	foreach ($data_group as $key => $gr) {
		?>
				           			<tr>
				           				<td class="bold text-danger"><?php echo html_entity_decode($count_gr . '. ' . get_criteria_name($key)); ?></td>
				           				<td class="bold text-danger"><?php echo html_entity_decode($gr['toltal_percent'] . '%'); ?></td>
				           				<td class="bold text-danger"></td>
				           				<td class="bold text-danger"><?php echo html_entity_decode($gr['result']); ?></td>
				           			</tr>
				           			<?php $count_cr = 1;foreach ($cd_evaluation as $cd) {
			if ($cd['group_criteria'] == $key) {
				?>
				           				<tr>
					           				<td><?php echo html_entity_decode($count_gr . '.' . $count_cr . '. ' . get_criteria_name($cd['criteria'])); ?></td>
					           				<td><?php echo html_entity_decode($cd['percent'] . '%'); ?></td>
					           				<td>
					           					<?php
$sp1 = '';
				$sp2 = '';
				$sp3 = '';
				$sp4 = '';
				$sp5 = '';
				if ($cd['rate_score'] == 1) {
					$sp1 = ' checked';
					$sp2 = '-o';
					$sp3 = '-o';
					$sp4 = '-o';
					$sp5 = '-o';
				} elseif ($cd['rate_score'] == 2) {
					$sp1 = ' checked';
					$sp2 = ' checked';
					$sp3 = '-o';
					$sp4 = '-o';
					$sp5 = '-o';
				} elseif ($cd['rate_score'] == 3) {
					$sp1 = ' checked';
					$sp2 = ' checked';
					$sp3 = ' checked';
					$sp4 = '-o';
					$sp5 = '-o';
				} elseif ($cd['rate_score'] == 4) {
					$sp1 = ' checked';
					$sp2 = ' checked';
					$sp3 = ' checked';
					$sp4 = ' checked';
					$sp5 = '-o';
				} elseif ($cd['rate_score'] == 5) {
					$sp1 = ' checked';
					$sp2 = ' checked';
					$sp3 = ' checked';
					$sp4 = ' checked';
					$sp5 = ' checked';
				}
				?>
												<span class="fa fa-star<?php echo html_entity_decode($sp1); ?>"></span>
												<span class="fa fa-star<?php echo html_entity_decode($sp2); ?>"></span>
												<span class="fa fa-star<?php echo html_entity_decode($sp3); ?>"></span>
												<span class="fa fa-star<?php echo html_entity_decode($sp4); ?>"></span>
												<span class="fa fa-star<?php echo html_entity_decode($sp5); ?>"></span>
					           				</td>
					           				<td><?php echo html_entity_decode(($cd['rate_score'] * $cd['percent']) / 100); ?></td>
				           				</tr>
				           			<?php $count_cr++;}}?>
				           		<?php $count_gr++;}}?>
				            </tbody>
				        </table>

              		</div>



                  </div>

                  <div role="tabpanel" class="tab-pane" id="attachment">
                  	<div id="candidate_pv_file">
                  		<br>
			    	  	<?php
$file_html = '';
if (count($candidate->file) > 0) {

	foreach ($candidate->file as $f) {
		$href_url = site_url(RECRUITMENT_PATH . 'candidate/files/' . $f['rel_id'] . '/' . $f['file_name']) . '" download';
		if (!empty($f['external'])) {
			$href_url = $f['external_link'];
		}
		$file_html .= '<div class="mbot15 row inline-block full-width" data-attachment-id="' . $f['id'] . '">
					              <div class="col-md-8">
					                 <a name="preview-ase-btn" onclick="preview_candidate_btn(this); return false;" rel_id = "' . $f['rel_id'] . '" id = "' . $f['id'] . '" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left margin-right-5" data-toggle="tooltip" title data-original-title="' . _l('preview_file') . '"><i class="fa fa-eye"></i></a>
					                 <div class="pull-left"><i class="' . get_mime_class($f['filetype']) . '"></i></div>
					                 <a href=" ' . $href_url . '" target="_blank" download>' . $f['file_name'] . '</a>
					                 <br />
					                 <small class="text-muted">' . $f['filetype'] . '</small>
					              </div>
					              <div class="col-md-4 text-right">';
		if ($f['staffid'] == get_staff_user_id() || is_admin()) {
			$file_html .= '<a href="#" class="text-danger" onclick="delete_candidate_attachment(' . $f['id'] . '); return false;"><i class="fa fa-times"></i></a>';
		}
		$file_html .= '</div></div>';
	}
	$file_html .= '<hr />';
	echo html_entity_decode($file_html);
}
?>
			    	</div>
			    	<div id="candidate_file_data"></div>
                  </div>

              	</div>
         	</div>
           </div>
       </div>
   </div>
</div>
<div class="modal fade" id="care_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open_multipart(admin_url('recruitment/care_candidate'), array('id' => 'care_candidate-form')); ?>
        <div class="modal-content width-100">
            <div class="modal-header">
                <button d type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">

                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                	<div class="col-md-6">
                		<?php $attr = [];
$attr = ['disabled' => "true"];
echo render_input('candidate', 'candidate', $candidate->candidate_code . ' - ' . $candidate->candidate_name . ' ' . $candidate->last_name, 'text', $attr);

echo form_hidden('candidate', $candidate->id);
?>
                	</div>
                	<div class="col-md-6">
                		<?php echo render_datetime_input('care_time', 'care_time') ?>
                	</div>
                	<div class="col-md-12" id="care_rs">

                	</div>
                	<div class="col-md-12">
                		<?php echo render_textarea('description', 'description') ?>
                	</div>
                	<div id="type_care">

                	</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type=""class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button id="sm_btn" type="submit" onclick="submit_care_candidate(); return false;" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

 <div class="modal fade" id="mail_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open_multipart(admin_url('recruitment/send_mail_candidate'), array('id' => 'mail_candidate-form')); ?>
        <div class="modal-content width-100">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span><?php echo _l('send_mail'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                	<div class="col-md-12">
                		<?php $attr = [];
$attr = ['disabled' => "true"];
echo render_input('candidate', 'candidate', $candidate->candidate_code . ' - ' . $candidate->candidate_name . ' ' . $candidate->last_name, 'text', $attr);

echo form_hidden('candidate', $candidate->id);
?>
                	</div>
                	<div class="col-md-12">
                		<?php echo render_input('email', 'email', $candidate->email); ?>
                	</div>

                	<div class="col-md-12">
                		<?php echo render_input('subject', 'subject'); ?>
                	</div>

                	<div class="col-md-12">
                		<?php echo render_textarea('content', 'content', '', array(), array(), '', 'tinymce') ?>
                	</div>
                	<div id="type_care">

                	</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type=""class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button id="sm_btn" type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

<div class="modal fade" id="candidate_rating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content width-100">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span><?php echo _l('rate_candidate'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/recruitment/rating_candidate', array('id' => 'rating-modal')); ?>
              <?php echo form_hidden('candidate', $candidate->id); ?>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12">
                  <?php if ($evaluation != '') {
	$count_gr = 1;
	foreach ($evaluation['groups'] as $gr) {

		?>

                  			<h5 class="bold"><?php echo html_entity_decode($count_gr . '. ' . $gr['criteria_title']); ?></h5>
                  			<hr class="criteria_title-hr" />

                  		<?php
$count_cr = 1;

		foreach ($evaluation['criteria'] as $cr) {
			if ($cr['group_cr'] == $gr['id']) {

				?>

                  			<p><div class="star-rating">
                  				&nbsp;&nbsp;&nbsp;<?php echo html_entity_decode($count_gr . '.' . $count_cr . '. ' . $cr['criteria_title'] . ' (' . $cr['percent'] . '%)'); ?>
                  				<div class="pull-right font-size-125">
			                    <span class="fa fa-star-o margin-top-8" data-rating="1" data-id="<?php echo html_entity_decode($cr['evaluation_criteria']); ?>"></span>
			                    <span class="fa fa-star-o margin-top-8" data-rating="2" data-id="<?php echo html_entity_decode($cr['evaluation_criteria']); ?>"></span>
			                    <span class="fa fa-star-o margin-top-8" data-rating="3" data-id="<?php echo html_entity_decode($cr['evaluation_criteria']); ?>"></span>
			                    <span class="fa fa-star-o margin-top-8" data-rating="4" data-id="<?php echo html_entity_decode($cr['evaluation_criteria']); ?>"></span>
			                    <span class="fa fa-star-o margin-top-8" data-rating="5" data-id="<?php echo html_entity_decode($cr['evaluation_criteria']); ?>"></span>
			                    <input type="hidden" name="rating[<?php echo html_entity_decode($cr['evaluation_criteria']); ?>]" class="rating-value" value="">
			                    <input type="hidden" name="percent[<?php echo html_entity_decode($cr['evaluation_criteria']); ?>]" value="<?php echo html_entity_decode($cr['percent']); ?>">
			                    <input type="hidden" name="group[<?php echo html_entity_decode($cr['evaluation_criteria']); ?>]" value="<?php echo html_entity_decode($gr['id']); ?>">
			                	</div>

			                 </div> </p>

             	  <?php $count_cr++;}}
		$count_gr++;}?>
             	   <?php echo render_textarea('feedback', 'feedback'); ?>
                  <?php } else {echo '<p class="bold text-danger">' . _l('none_evaluetion_form') . '</p>';}?>


                </div>

              </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" onclick="submit_rating_candidate(); return false;" class="btn btn-info"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<?php init_tail();?>
<?php require 'modules/recruitment/assets/js/candidate_detail_js.php';?>
</body>
</html>