 <?php echo form_open_multipart('admin/hr_profile/upload_file',array('id'=>'hr_profile_attachment','class'=>'dropzone')); ?>
 <input type="hidden" name="staffid" value="<?php echo html_entity_decode($staffid); ?>">
 <?php echo form_close(); ?>   

 <div>
 	<div id="contract_attachments" class="mtop30 col-md-8 col-md-offset-2">
 		<?php
 		$data = '<div class="row" id="attachment_file">';
 		foreach($hr_profile_staff as $attachment) {
 			$href_url = site_url('modules/hr_profile/uploads/att_file/'.$attachment['rel_id'].'/'.$attachment['file_name']).'" download';
 			if(!empty($attachment['external'])){
 				$href_url = $attachment['external_link'];
 			}
 			$data .= '<div class="display-block contract-attachment-wrapper">';
 			$data .= '<div class="col-md-10">';
 			$data .= '<div class="col-md-2 mr-5">';
 			$data .= '<a name="preview-btn" onclick="preview_file_staff(this); return false;" rel_id = "'.$attachment['rel_id'].'" id = "'.$attachment['id'].'" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="'._l("preview_file").'">';
 			$data .= '<i class="fa fa-eye"></i>'; 
 			$data .= '</a>';
 			$data .= '</div>';
 			$data .= '<div class=col-md-8>';
 			$data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
 			$data .= '<a href="'.$href_url.'>'.$attachment['file_name'].'</a>';
 			$data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
 			$data .= '</div>';
 			$data .= '</div>';
 			$data .= '<div class="col-md-2 text-right">';
 			if($attachment['staffid'] == get_staff_user_id() || is_admin() || has_permission('hrm_hr_records', '', 'edit')){
 				$data .= '<a href="#" class="text-danger" onclick="delete_hr_att_file_attachment(this,'.$attachment['id'].'); return false;"><i class="fa fa fa-times"></i></a>';
 			}
 			$data .= '</div>';
 			$data .= '<div class="clearfix"></div><hr/>';
 			$data .= '</div>';
 		}
 		$data .= '</div>';
 		echo html_entity_decode($data);
 		?>

 	</div>
 </div>
 <div id="contract_file_data"></div>