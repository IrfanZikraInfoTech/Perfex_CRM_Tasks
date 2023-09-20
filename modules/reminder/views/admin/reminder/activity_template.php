<?php defined('BASEPATH') or exit('No direct script access allowed');
foreach($activity as $activity){
  $_custom_data = false;
  ?>
  <div class="feed-item newclass" data-sale-activity-id="<?php echo $activity['id']; ?>">
    <div class="date">
     <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($activity['date']); ?>">
       <?php echo time_ago($activity['date']); ?>
     </span>
   </div> 
   <div class="text">
     <?php if(!empty($activity['staffid']) && is_numeric($activity['staffid']) && $activity['staffid'] != 0){ ?>
       <a href="<?php echo admin_url('profile/'.$activity["staffid"]); ?>">
         <?php echo staff_profile_image($activity['staffid'],array('staff-profile-xs-image pull-left mright5'));
         ?>
       </a>
     <?php } ?>
     <?php
     $additional_data = '';
     if(!empty($activity['additional_data'])){
       $additional_data = unserialize($activity['additional_data']);
       $i = 0;
       foreach($additional_data as $data){
         if(strpos($data,'<original_status>') !== false){
           $original_status = get_string_between($data, '<original_status>', '</original_status>');
           $additional_data[$i] = format_estimate_status($original_status,'',false);
         } else if(strpos($data,'<new_status>') !== false){
           $new_status = get_string_between($data, '<new_status>', '</new_status>');
           $additional_data[$i] = format_estimate_status($new_status,'',false);
         } else if(strpos($data,'<status>') !== false){
           $status = get_string_between($data, '<status>', '</status>');
           $additional_data[$i] = format_estimate_status($status,'',false);
         } else if(strpos($data,'<custom_data>') !== false){
           $_custom_data = get_string_between($data, '<custom_data>', '</custom_data>');
           unset($additional_data[$i]);
         }
         $i++;
       }
     }
     $_formatted_activity = nl2br((isset($additional_data) AND $additional_data !='')?$additional_data[0]:'');
     if($_custom_data !== false){
      $_formatted_activity .= '  ' .$_custom_data;
    }
    if(!empty($activity['full_name'])){
      $_formatted_activity = $activity['full_name'] . ' ' . $_formatted_activity;
    }
    echo $_formatted_activity;
    if(is_admin()){
      echo '<a href="#" class="pull-right text-danger" onclick="delete_reminder_activity('.$activity['id'].'); return false;"><i class="fa fa-remove"></i></a>';
    }
    ?>
  </div>
</div>
<?php } ?>