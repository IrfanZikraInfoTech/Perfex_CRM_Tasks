<div class="panel_s">
	<div class="panel-body">
  		<div class="col-md-12">
        <h4 class="isp-general-infor"><?php echo _l('general_infor') ?></h4>
         <hr class="isp-general-infor-hr" />
      </div>
      <div class="col-md-12">
        <table class="table border table-striped margin-top-0">
            <tbody>
              <tr class="project-overview">
                  <td class="bold" width="30%"><?php echo _l('interview_schedules_name'); ?></td>
                  <td><?php echo html_entity_decode($intv_sch->is_name); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold" width="30%"><?php echo _l('rec_time'); ?></td>
                  <?php
$from_hours_format = '';
$to_hours_format = '';

$from_hours = _dt($intv_sch->from_hours);
$from_hours = explode(" ", $from_hours);
foreach ($from_hours as $key => $value) {
	if ($key != 0) {
		$from_hours_format .= $value;
	}
}

$to_hours = _dt($intv_sch->to_hours);
$to_hours = explode(" ", $to_hours);
foreach ($to_hours as $key => $value) {
	if ($key != 0) {
		$to_hours_format .= $value;
	}
}

?>
                  <td><?php echo html_entity_decode($from_hours_format . ' - ' . $to_hours_format); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('interview_day'); ?></td>
                  <td><?php echo _d($intv_sch->interview_day); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('recruitment_campaign'); ?></td>
                  <td><?php $cp = get_rec_campaign_hp($intv_sch->campaign);
if (isset($cp)) {
	$_data = $cp->campaign_code . ' - ' . $cp->campaign_name;
} else {
	$_data = '';
}
echo html_entity_decode($_data);?></td>
               </tr>

               <tr class="project-overview">
                  <td class="bold"><?php echo _l('date_add'); ?></td>
                  <td>
                    <?php echo _d($intv_sch->added_date); ?>
                  </td>
               </tr>

               <tr class="project-overview">
                  <td class="bold"><?php echo _l('interviewer'); ?></td>
                  <td><?php
$inv = explode(',', $intv_sch->interviewer);
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
?></td>
               </tr>
               </tbody>
             </table>
          </div>
          <div class="col-md-12">
            <h4 class="isp-general-infor"><?php echo _l('list_of_candidates_participating') ?></h4>
            <hr class="isp-general-infor-hr" />
          </div>
          <?php foreach ($intv_sch->list_candidate as $cd) {?>
          <div class="col-md-6">
            <div class="col-md-12">
              <div class="row">
                  <div class="thumbnail">
                    <div class="caption" onclick="location.href='<?php echo admin_url('recruitment/candidate/' . $cd['candidate']) ?>'">

                      <h4 id="thumbnail-label"><?php echo candidate_profile_image($cd['candidate'], ['staff-profile-image-small mright5'], 'small') . ' #' . $cd['candidate_code'] . ' - ' . $cd['candidate_name'] . ' ' . $cd['last_name']; ?></h4>

                      <p><?php echo _l('email') . ': ' . $cd['email']; ?></p>

                      <div class="thumbnail-description smaller"><?php echo _l('phonenumber') . ': ' . $cd['phonenumber']; ?></div>

                    </div>
                  </div>
              </div>
            </div>
          </div>
        <?php }?>
  </div>