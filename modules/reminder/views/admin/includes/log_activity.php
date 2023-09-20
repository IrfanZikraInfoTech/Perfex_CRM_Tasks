<h4>Job Log</h4>
<div class="panel_s no-shadow job_log_activity">
   <div class="activity-feed-job">
     <?php foreach($job_activity_log as $log){ ?>
      <div class="feed-item">
         <div class="date">
          <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($log['date']); ?>">
             <?php echo time_ago($log['date']); ?>
          </span>
       </div>
       <div class="text">
         <?php if($log['staffid'] != 0){ ?>
            <a href="<?php echo admin_url('profile/'.$log["staffid"]); ?>">
               <?php echo staff_profile_image($log['staffid'],array('staff-profile-xs-image pull-left mright5'));
               ?>
            </a>
            <?php
         }
         $additional_data = '';
         if(!empty($log['additional_data'])){
           $additional_data = unserialize($log['additional_data']);
           echo ($log['staffid'] == 0) ? _l($log['description'],$additional_data) : $log['full_name'] .' - '._l($log['description'],$additional_data);
        } else {
           echo $log['full_name'] . ' - ';
           echo _l($log['description'],'',false);
        }
        ?>
     </div>
  </div>
<?php } ?>
</div>
    <div class="clearfix"></div>
</div>