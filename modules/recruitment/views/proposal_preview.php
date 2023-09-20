<div class="panel_s">
	<div class="panel-body">
		<?php if($proposals->status == 1){ ?>
	    	<div class="ribbon info"><span><?php echo _l('_proposal'); ?></span></div>
		<?php }elseif($proposals->status == 2){ ?>
			<div class="ribbon success"><span><?php echo _l('approved'); ?></span></div>
		<?php }elseif($proposals->status == 3){ ?>	
			<div class="ribbon warning"><span><?php echo _l('made_finish'); ?></span></div>
		<?php }elseif($proposals->status == 4){ ?>
			<div class="ribbon danger"><span><?php echo _l('reject'); ?></span></div>
		<?php } ?>
  		<div class="row col-md-12">
        <?php if(get_staff_user_id() == $proposals->approver && $proposals->status == 1){ ?>
          <div id="reject_div">
          <a href="<?php echo admin_url('recruitment/approve_reject_proposal/'.'reject'.'/'.$proposals->id); ?>" id="reject_btn" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-warning pull-right mleft10 display-block">
              <?php echo _l('reject'); ?>
          </a>
          </div>
          <div id="approved_div">
          <a href="<?php echo admin_url('recruitment/approve_reject_proposal/'.'approved'.'/'.$proposals->id); ?>" id="approved_btn" data-loading-text="<?php echo _l('wait_text'); ?>"  class="btn btn-success pull-right display-block">
             <?php echo _l('approve'); ?>
          </a>
          </div>
        <?php } ?>
    		<h4 class="general-infor-color"><?php echo _l('general_infor') ?></h4>
    		<hr class="general-infor-hr" />
    	</div>
    	<div class="col-md-6" class="padding-left-right-0">
    		<table class="table border table-striped margin-top-0">
            <tbody>
               <tr class="project-overview">
                  <td class="bold" width="30%"><?php echo _l('proposal_name'); ?></td>
                  <td><?php echo html_entity_decode($proposals->proposal_name); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('department'); ?></td>
                  <td><?php echo get_rec_dpm_name($proposals->department); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('form_of_work'); ?></td>
                  <td><?php echo _l($proposals->form_work); ?></td>
               </tr>
               
               <tr class="project-overview">
                <?php 
                  $get_base_currency = get_base_currency();
                  $current_id='';
                  if($get_base_currency){
                    $current_id= $get_base_currency->id;
                  }
                 ?>
                  <td class="bold"><?php echo _l('starting_salary'); ?></td>
                  <td><?php echo app_format_money($proposals->salary_from, $current_id).' - '.app_format_money($proposals->salary_to, $current_id); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('campaign'); ?></td>
                  <td></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('reason_recruitment'); ?></td>
                  <td><?php echo html_entity_decode($proposals->reason_recruitment); ?></td>
               </tr>

                </tbody>
        </table>
            
    	</div>
       
    	<div class="col-md-6 padding-left-right-0">
    		<table class="table table-striped margin-top-0">
            <tbody>
               <tr class="project-overview">
                  <td class="bold" width="40%"><?php echo _l('position'); ?></td>
                  <td><?php echo get_rec_position_name($proposals->position); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('amount_recruiment'); ?></td>
                  <td><?php echo html_entity_decode($proposals->amount_recruiment); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('workplace'); ?></td>
                  <td><?php echo html_entity_decode($proposals->workplace); ?></td>
               </tr>
               
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('recruiment_duration'); ?></td>
                  <td><?php echo _d($proposals->from_date) .' - '. _d($proposals->to_date) ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('status'); ?></td>
                  <td>
                  	<?php if($proposals->status == 1 ){
			                $_data = ' <span class="label label inline-block project-status-'.$proposals->status.' proposal-style"> '._l('_proposal').' </span>';
			            }elseif($proposals->status == 2 ){
			                $_data = ' <span class="label label inline-block project-status-'.$proposals->status.' approved-style" > '._l('approved').' </span>';
			            }elseif($proposals->status == 3 ){
			                $_data = ' <span class="label label inline-block project-status-'.$proposals->status.' made_finish-style"> '._l('made_finish').' </span>';
			            }elseif($proposals->status == 4 ){
			                $_data = ' <span class="label label inline-block project-status-'.$proposals->status.' reject-style"> '._l('reject').' </span>';
			            }

			            echo html_entity_decode($_data);
			             ?>
                  </td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('add_from'); ?></td>
                  <td><a href="<?php echo admin_url('staff/profile/'.$proposals->add_from); ?>"><?php echo get_staff_full_name($proposals->add_from); ?></a></td>
               </tr>
                </tbody>
             </table>
    	</div>
      <div class="col-md-12 padding-left-10">
        <p class="bold text-muted"><?php echo _l('job_description').': '.$proposals->job_description; ?></p>
          
        </div>
    	<div class="row col-md-12">
    		<h4 class="candidate_request-color"><?php echo _l('candidate_request') ?></h4>
    		<hr class="candidate_request-hr" />
    	</div>
    	<div class="col-md-6 padding-left-right-0">
    		<table class="table border table-striped margin-top-0">
            <tbody>
               <tr class="project-overview">
                  <td class="bold" width="30%"><?php echo _l('gender'); ?></td>
                  <td><?php echo _l($proposals->gender); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('height'); ?></td>
                  <td><?php echo html_entity_decode($proposals->height); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('literacy'); ?></td>
                  <td><?php echo _l($proposals->literacy); ?></td>
               </tr>
             </tbody>
             </table>
    	</div>
    	<div class="col-md-6 padding-left-right-0">
    		<table class="table table-striped margin-top-0">
            <tbody>
               <tr class="project-overview">
                  <td class="bold" width="30%"><?php echo _l('ages'); ?></td>
                  <td><?php echo html_entity_decode($proposals->ages_from.' - '.$proposals->ages_to); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('weight'); ?></td>
                  <td><?php echo html_entity_decode($proposals->weight); ?></td>
               </tr>
               <tr class="project-overview">
                  <td class="bold"><?php echo _l('experience'); ?></td>
                  <td><?php echo _l($proposals->experience); ?></td>
               </tr>
               </tbody>
            </table>
    	</div>
    	<div class="col-md-12" id="proposal_pv_file">
    	  	<?php
		        $file_html = '';
		        if(count($proposal_file) > 0){
		            $file_html .= '<hr />
		                    <p class="bold text-muted">'._l('proposal_files').'</p>';
		            foreach ($proposal_file as $f) {
		                $href_url = site_url(RECRUITMENT_PATH.'proposal/'.$f['rel_id'].'/'.$f['file_name']).'" download';
		                                if(!empty($f['external'])){
		                                  $href_url = $f['external_link'];
		                                }
		               $file_html .= '<div class="mbot15 row inline-block full-width" data-attachment-id="'. $f['id'].'">
		              <div class="col-md-8">
		                 <a name="preview-ase-btn" onclick="preview_proposal_btn(this); return false;" rel_id = "'. $f['rel_id']. '" id = "'.$f['id'].'" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left margin-right-5" data-toggle="tooltip" title data-original-title="'. _l('preview_file').'"><i class="fa fa-eye"></i></a>
		                 <div class="pull-left"><i class="'. get_mime_class($f['filetype']).'"></i></div>
		                 <a href=" '. $href_url.'" target="_blank" download>'.$f['file_name'].'</a>
		                 <br />
		                 <small class="text-muted">'.$f['filetype'].'</small>
		              </div>
		              <div class="col-md-4 text-right">';
		                if($f['staffid'] == get_staff_user_id() || is_admin()){
		                $file_html .= '<a href="#" class="text-danger" onclick="delete_proposal_attachment('. $f['id'].'); return false;"><i class="fa fa-times"></i></a>';
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
 <div id="proposal_file_data"></div>
 <script>
  function delete_proposal_attachment(id) {
  	if (confirm_delete()) {
  	    requestGet('recruitment/delete_proposal_attachment/' + id).done(function(success) {
  	        if (success == 1) {
  	            $("#proposal_pv_file").find('[data-attachment-id="' + id + '"]').remove();
  	        }
  	    }).fail(function(error) {
  	        alert_float('danger', error.responseText);
  	    });
  	}
  }
  $('#reject_btn').on('click',function(){
    $('#approved_div').addClass('hide');
  });
  $('#approved_btn').on('click',function(){
    $('#reject_div').addClass('hide');
  });
 </script>