<div class="panel_s">
	<div class="panel-body">
		<?php if ($campaigns->cp_status == 1) {?>
      <div class="ribbon warnings"><span><?php echo _l('planning'); ?></span></div>
		<?php } elseif ($campaigns->cp_status == 3) {?>
			<div class="ribbon info"><span><?php echo _l('in_progress'); ?></span></div>
		<?php } elseif ($campaigns->cp_status == 4) {?>
			<div class="ribbon success"><span><?php echo _l('finish'); ?></span></div>
    <?php } elseif ($campaigns->cp_status == 5) {?>
      <div class="ribbon danger"><span><?php echo _l('cancel'); ?></span></div>
    <?php }?>
    <?php if (($campaigns->cp_status == 1 || $campaigns->cp_status == 2) && date('Y-m-d') > $campaigns->cp_to_date) {?>
      <div class="ribbon warning"><span><?php echo _l('overdue'); ?></span></div>
    <?php }?>
  		<div class="row col-md-12">
       <div class="col-md-9">
    		<h4 class="general-infor-color"><?php echo _l('general_infor') ?></h4>
        </div>
        <?php $manager = explode(',', $campaigns->cp_manager ?? ''); ?>



    	</div>

        <?php
        $get_base_currency = get_base_currency();
        $current_id='';
        if($get_base_currency){
          $current_id= $get_base_currency->id;
        }

         $manager = explode(',', $campaigns->cp_manager ?? ''); 
        $curent_user = get_staff_user_id();
        if((in_array($curent_user, $manager) || $curent_user == $campaigns->cp_add_from || is_admin()) ){
        ?>

        <div class=" col-md-3 pull-right">
          <select name="change_status" id="change_status" class="selectpicker dropdown bootstrap-select show-tick bs3" onchange="change_status_campaign(this,<?php echo html_entity_decode($campaigns->cp_id); ?>); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('change_status_to'); ?>">

            <option value="1" <?php if ($campaigns->cp_status == 1) {echo 'selected';}?>><?php echo _l('planning'); ?></option>
            <option value="3" <?php if ($campaigns->cp_status == 3) {echo 'selected';}?>><?php echo _l('in_progress'); ?></option>
            <option value="4" <?php if ($campaigns->cp_status == 4) {echo 'selected';}?>><?php echo _l('finish'); ?></option>
            <option value="5" <?php if ($campaigns->cp_status == 5) {echo 'selected';}?>><?php echo _l('cancel'); ?></option>

          </select>
        </div>
      <?php } ?>

      <div class="col-md-12"><hr class="general-infor-hr" /></div>
      <div class="row">


    	<div class="col-md-6 padding-left-right-0">
    		<table class="table border table-striped margin-top-0">
            <tbody>
              <tr class="project-overview">
                  <td class="bold" width="30%"><?php echo _l('campaign_code'); ?></td>
                  <td><?php echo html_entity_decode($campaigns->campaign_code); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold" width="30%"><?php echo _l('campaign_name'); ?></td>
                  <td><?php echo html_entity_decode($campaigns->campaign_name); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('department'); ?></td>
                  <td><?php echo get_rec_dpm_name($campaigns->cp_department); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('form_of_work'); ?></td>
                  <td><?php echo _l($campaigns->cp_form_work); ?></td>
               </tr>

               <tr class="project-overview">
                  <td class="bold"><?php echo _l('starting_salary'); ?></td>
                  <td><?php echo app_format_money($campaigns->cp_salary_from, $current_id) . ' - ' . app_format_money($campaigns->cp_salary_to, $current_id); ?></td>
               </tr>
                <tr class="project-overview">
                  <td class="bold"><?php echo _l('recruitment_channel_form'); ?></td>
                  <?php 
                  $r_form_name='';
                    if(!is_array($rec_channel_form) ==1){
                        if($rec_channel_form){
                        $r_form_name = $rec_channel_form->r_form_name;
                      }
                    }
                   ?>
                  <td><?php echo html_entity_decode($r_form_name); ?></td>
               </tr>

               <tr class="project-overview">
                  <td class="bold"><?php echo _l('reason_recruitment'); ?></td>
                  <td><?php echo html_entity_decode($campaigns->cp_reason_recruitment); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('company_name'); ?></td>
                  <td><?php echo html_entity_decode(get_rec_company_name($campaigns->company_id)); ?></td>
               </tr>

               <?php 
                $rec_channel = get_rec_channel_form_key($campaigns->rec_channel_form_id);
                ?>

                <?php if(isset($rec_channel) && $rec_channel !='') {?>
                <tr class="project-overview">
                  <td class="bold"><?php echo 'Form url'; ?></td>
                  <td> 
                    <span class="label label-default">
                    <a href="<?php echo site_url('recruitment/forms/wtl/'.$campaigns->cp_id.'/'.$rec_channel); ?>" target="_blank"><?php echo site_url('recruitment/forms/wtl/'.$campaigns->cp_id.'/'.$rec_channel); ?></a>
                    </span>
                  </td>
               </tr>
             <?php } ?>
                
                </tbody>
             </table>

    	</div>

    	<div class="col-md-6 padding-left-right-0">
    		<table class="table table-striped margin-top-0">
            <tbody>
               <tr class="project-overview">
                  <td class="bold" width="40%"><?php echo _l('position'); ?></td>
                  <td><?php echo get_rec_position_name($campaigns->cp_position); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('amount_recruiment'); ?></td>
                  <td><?php echo html_entity_decode($campaigns->cp_amount_recruiment); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('workplace'); ?></td>
                  <td><?php echo html_entity_decode($campaigns->cp_workplace); ?></td>
               </tr>

               <tr class="project-overview">
                  <td class="bold"><?php echo _l('recruiment_duration'); ?></td>
                  <td><?php echo _d($campaigns->cp_from_date) . ' - ' . _d($campaigns->cp_to_date) ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('status'); ?></td>
                  <td class="padding-5">
                  	<?php if ($campaigns->cp_status == 1) {
	$_data = ' <span class="label label inline-block project-status-' . $campaigns->cp_status . ' campaign-planning-style"> ' . _l('planning') . ' </span>';
} elseif ($campaigns->cp_status == 3) {
	$_data = ' <span class="label label inline-block project-status-' . $campaigns->cp_status . ' campaign-progress-style"> ' . _l('in_progress') . ' </span>';
} elseif ($campaigns->cp_status == 4) {
	$_data = ' <span class="label label inline-block project-status-' . $campaigns->cp_status . ' campaign-finish-style""> ' . _l('finish') . ' </span>';
} elseif ($campaigns->cp_status == 5) {
	$_data = ' <span class="label label inline-block project-status-' . $campaigns->cp_status . ' campaign-cancel-style""> ' . _l('cancel') . ' </span>';
}

if (($campaigns->cp_status == 1 || $campaigns->cp_status == 2) && date('Y-m-d') > $campaigns->cp_to_date) {
	$_data = '<span class="label label inline-block project-status-' . $campaigns->cp_status . ' campaign-overdue-style"> ' . _l('overdue') . ' </span>';
}
echo html_entity_decode($_data);
?>
                  </td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('add_from'); ?></td>
                  <td><a href="<?php echo admin_url('staff/profile/' . $campaigns->cp_add_from); ?>"><?php echo get_staff_full_name($campaigns->cp_add_from); ?></a></td>
               </tr>
                 <tr class="project-overview">
                  <td class="bold"><?php echo _l('follower'); ?></td>
                  <td>
                    <?php $follows = explode(',', $campaigns->cp_follower ?? '');
foreach ($follows as $f) {
	?>
                    <a href="<?php echo admin_url('profile/' . $f); ?>">
                        <?php echo staff_profile_image($f, [
		'staff-profile-image-small mright5',
	], 'small', [
		'data-toggle' => 'tooltip',
		'data-title' => get_staff_full_name($f),
	]); ?>
                    </a>&nbsp;
                    <?php }?>
                  </td>
               </tr>

               <tr class="project-overview">
                  <td class="bold"><?php echo _l('manager'); ?></td>
                  <td>
                    <?php
foreach ($manager as $f) {
  ?>
                    <a href="<?php echo admin_url('profile/' . $f); ?>">
                        <?php echo staff_profile_image($f, [
    'staff-profile-image-small mright5',
  ], 'small', [
    'data-toggle' => 'tooltip',
    'data-title' => get_staff_full_name($f),
  ]); ?>
                    </a>&nbsp;
                    <?php }?>
                  </td>
               </tr>

                </tbody>
             </table>
    	</div>
      <div class="col-md-12 padding-left-10">
        <p class="bold text-muted"><?php echo _l('job_description') . ': ' . $campaigns->cp_job_description; ?></p>

        </div>
    	<div class="row col-md-12">
    		<h4 class="candidate_request-color"><?php echo _l('candidate_request') ?></h4>
    		<hr class="candidate_request-hr"/>
    	</div>
    	<div class="col-md-6 padding-left-right-0">
    		<table class="table border table-striped margin-top-0">
            <tbody>
               <tr class="project-overview">
                  <td class="bold" width="30%"><?php echo _l('gender'); ?></td>
                  <td><?php echo _l($campaigns->cp_gender); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('height'); ?></td>
                  <td><?php echo html_entity_decode($campaigns->cp_height); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('literacy'); ?></td>
                  <td><?php echo _l($campaigns->cp_literacy); ?></td>
               </tr>
             </tbody>
             </table>
    	</div>
    	<div class="col-md-6 padding-left-right-0">
    		<table class="table table-striped margin-top-0">
            <tbody>
               <tr class="project-overview">
                  <td class="bold" width="30%"><?php echo _l('ages'); ?></td>
                  <td><?php echo html_entity_decode($campaigns->cp_ages_from . ' - ' . $campaigns->cp_ages_to); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('weight'); ?></td>
                  <td><?php echo html_entity_decode($campaigns->cp_weight); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('experience'); ?></td>
                  <td><?php echo _l($campaigns->cp_experience); ?></td>
               </tr>
               </tbody>
            </table>
    	</div>
    	<div class="col-md-12" id="campaign_pv_file">
    	  	<?php
$file_html = '';
if (count($campaign_file) > 0) {
	$file_html .= '<hr />
		                    <p class="bold text-muted">' . _l('proposal_files') . '</p>';
	foreach ($campaign_file as $f) {
		$href_url = site_url(RECRUITMENT_PATH . 'campaign/' . $f['rel_id'] . '/' . $f['file_name']) . '" download';
		if (!empty($f['external'])) {
			$href_url = $f['external_link'];
		}
		$file_html .= '<div class="mbot15 row inline-block full-width" data-attachment-id="' . $f['id'] . '">
		              <div class="col-md-8">
		                 <a name="preview-ase-btn" onclick="preview_campaign_btn(this); return false;"  rel_id = "' . $f['rel_id'] . '" id = "' . $f['id'] . '" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left margin-right-5" data-toggle="tooltip" title data-original-title="' . _l('preview_file') . '"><i class="fa fa-eye"></i></a>
		                 <div class="pull-left"><i class="' . get_mime_class($f['filetype']) . '"></i></div>
		                 <a href=" ' . $href_url . '" target="_blank" download>' . $f['file_name'] . '</a>
		                 <br />
		                 <small class="text-muted">' . $f['filetype'] . '</small>
		              </div>
		              <div class="col-md-4 text-right">';
		if ($f['staffid'] == get_staff_user_id() || is_admin()) {
			$file_html .= '<a href="#" class="text-danger" onclick="delete_campaign_attachment(' . $f['id'] . '); return false;"><i class="fa fa-times"></i></a>';
		}
		$file_html .= '</div></div>';
	}
	$file_html .= '<hr />';
	echo html_entity_decode($file_html);
}
?>
    	</div>


  	</div>
 </div>
 <div id="campaign_file_data"></div>
   <?php require 'modules/recruitment/assets/js/campaign_preview_js.php';?>
