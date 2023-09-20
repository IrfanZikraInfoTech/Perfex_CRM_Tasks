<div class="panel_s">
	<div class="panel-body">
     <div class="tab-content">    
      <div class="row">
        <div class="col-md-12">          
        </div>
        <div class="col-md-12">
          <ul class="list-group">
            <h4 class="general-infor-color"><?php echo _l('general_infor') ?></h4>
            <div class="row">
                <div class="col-md-12"><hr class="general-infor-hr"/></div>              
            </div>
          <div class="col-md-12 padding-left-right-0">
            <table class="table border table-striped margin-top-0">
                <tbody>
                  <tr class="project-overview">
                      <td class="bold" width="40%"><?php echo _l('form_name'); ?></td>
                      <td><?php echo html_entity_decode($recruitment_channel->r_form_name); ?></td>
                   </tr>
                   <tr class="project-overview">
                      <td class="bold" width="40%"><?php echo _l('total_cv_reciver'); ?></td>
                      <td><?php echo html_entity_decode($total_cv_form); ?></td>
                   </tr>

                   <?php 
                    $rec_campaign = $this->recruitment_model->get_rec_campaign($recruitment_channel->rec_campaign_id);

                        if($rec_campaign){
                            $campaign_name =$rec_campaign->campaign_name;
                        }else{
                            $campaign_name = '';

                        }
                    ?>
                    <tr class="project-overview">
                      <td class="bold"><?php echo _l('responsible_person'); ?></td>
                      <td><?php echo staff_profile_image($recruitment_channel->responsible,[
                    'staff-profile-image-small mright5',
                    ], 'small') .' '. get_staff_full_name($recruitment_channel->responsible); ?></td>
                   </tr>

                   <?php 
                      $arr_status=[];
                      $arr_status['1']=_l('application');
                      $arr_status['2']=_l('potential');
                      $arr_status['3']=_l('interview');
                      $arr_status['4']=_l('won_interview');
                      $arr_status['5']=_l('send_offer');
                      $arr_status['6']=_l('elect');
                      $arr_status['7']=_l('non_elect');
                      $arr_status['8']=_l('unanswer');
                      $arr_status['9']=_l('transferred');
                      $arr_status['10']=_l('preliminary_selection');

                      $_data = ($arr_status[$recruitment_channel->lead_status]);

                    ?>
                   <tr class="project-overview">
                      <td class="bold"><?php echo _l('status_after_submit_form'); ?></td>
                      <td><?php echo get_status_candidate($recruitment_channel->lead_status);; ?></td>
                   </tr>            
                    
                    </tbody>
                 </table>
          </div>
          <div class="row">
            <div class="col-md-12">
              <h4 class="general-infor-color"><?php echo _l('intergate_form_website') ?></h4>
              
            </div>
          </div>
            <div class="row">
              <div class="col-md-12"><hr class="general-infor-hr"/></div>
              
            </div>
            <h4 class="bold">Form Info</h4>
            <p><b>Form url:</b>
                <span class="label label-default">
                    <a href="<?php echo site_url('recruitment/forms/wtl/0/'.$recruitment_channel->form_key); ?>" target="_blank"><?php echo site_url('recruitment/forms/wtl/0/'.$recruitment_channel->form_key); ?></a>
                </span>
            </p>
            <hr>
            <h4 class="bold">Embed form</h4>
            <p>Copy &amp; Paste the code anywhere in your site to show the form, additionally you can adjust the width and height px to fit for your website.</p>

            <textarea class="form-control width-height-738-66" rows="1">&lt;iframe width="600" height="850" src="<?php echo site_url('recruitment/forms/wtl/0/'.$recruitment_channel->form_key); ?>" frameborder="0" allowfullscreen&gt;&lt;/iframe&gt;</textarea>
            
          </ul>
        </div>
      </div> 
  </div>
  	</div>
 </div>