
<script> 
function send_mail_candidate(argument, job_detail_id){
"use strict";
  $('#mail_modal').modal('show');

  var description = '';
  description += "<?php echo _l('Your_Name_'); ?>";
  description += "<?php echo _l('saw_this_job_opening_and_thought_you_might_find_it_interesting'); ?><br>";
   description += "<?php echo _l('We_would_like_to_invite_you_to_visit_our_recruitment_website_here_are_some_jobs_you_might_find_it_interesting'); ?><br>";
  description +='&#9755; <a href="'+site_url+'recruitment/recruitment_portal/job_detail/'+job_detail_id+'"><?php echo _l("click_here_to_go_to_page"); ?></a> ';

  $('#mail_candidate-form input[name="subject"]').val('');
  $('#mail_candidate-form textarea[name="content"]').val(description);

}
</script>