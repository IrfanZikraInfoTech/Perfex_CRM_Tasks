<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" >
 <div class="content">
  <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'article-form')); ?>
  <div class="row">
   <div class="col-md-8 col-md-offset-2">
    <div class="panel_s">
     <div class="panel-body">
      <h4 class="no-margin">
       <?php echo html_entity_decode($title); ?>
       <?php if(isset($article)){ ?>
       <br />
       <small>
        <?php if($article->staff_article == 1){ ?>
        <a href="<?php echo admin_url('hr_profile/knowledge_base_q_a/view/'.$article->slug); ?>" target="_blank"><?php echo admin_url('hr_profile/knowledge_base_q_a/view/'.$article->slug); ?></a>
        <?php } else { ?>

        <a href="<?php echo site_url('knowledge-base/article/'.$article->slug); ?>" target="_blank"><?php echo site_url('knowledge-base/article/'.$article->slug); ?></a>
        <?php } ?>
      </small>
      <?php } ?>
    </h4>
    <?php if(isset($article)){ ?>
    <p>
   
     <?php if(has_permission('hr_manage_q_a','','create')){ ?>
     <a href="<?php echo admin_url('hr_profile/knowledge_base_q_a/article'); ?>" class="btn btn-success pull-right"><?php echo _l('kb_article_new_article'); ?></a>
     <?php } ?>
     <?php if(has_permission('hr_manage_q_a','','delete')){ ?>
     <a href="<?php echo admin_url('hr_profile/knowledge_base_q_a/delete_article/'.$article->articleid); ?>" class="btn btn-danger _delete pull-right mright5"><?php echo _l('delete'); ?></a>
     <?php } ?>
     <div class="clearfix"></div>
   </p>
   <?php } ?>
   <hr class="hr-panel-heading" />

   <div class="clearfix"></div>
   <?php $value = (isset($article) ? $article->subject : ''); ?>
   <?php $attrs = (isset($article) ? array() : array('autofocus'=>true)); ?>
   <?php echo render_input('subject','kb_article_add_edit_subject',$value,'text',$attrs); ?>
   <?php if(isset($article)){
     echo render_input('slug','kb_article_slug',$article->slug,'text');
   } ?>
   <?php $value = (isset($article) ? $article->articlegroup : ''); ?>
   <?php if(has_permission('hr_manage_q_a','','create')){
     echo render_select_with_input_group('articlegroup',hr_profile_get_kb_groups(),array('groupid','name'),'kb_article_add_edit_group',$value,'<a href="#" onclick="new_kb_group();return false;"><i class="fa fa-plus"></i></a>');
   } else {
    echo render_select('articlegroup',hr_profile_get_kb_groups(),array('groupid','name'),'kb_article_add_edit_group',$value);
  }
  ?>
  <div class="checkbox checkbox-primary hide">
   <input type="checkbox" id="staff_article" name="staff_article" <?php if(isset($article) && $article->staff_article == 1){echo 'checked';} ?>>
   <label for="staff_article"><?php echo _l('internal_article'); ?></label>
 </div>
 <div class="checkbox checkbox-primary">
   <input type="checkbox" id="disabled" name="disabled" <?php if(isset($article) && $article->active_article == 0){echo 'checked';} ?>>
   <label for="disabled" ><?php echo _l('kb_article_disabled'); ?><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('kb_article_disabled_tooltip'); ?>"></i></label>
 </div>
<div class="checkbox checkbox-primary">
   <input type="checkbox" id="question_answers" name="question_answers" <?php if(isset($article) && $article->question_answers == 1){echo 'checked';} ?>>
   <label for="question_answers"><?php echo _l('hr_kb_article_QA'); ?></label>
 </div>

<div class="curator <?php if((isset($article) && $article->curator != '') || (isset($article) && $article->question_answers == '1')){echo '';}else{ echo ' hide';} ?> ">
  <?php $curator = (isset($article) ? $article->curator : ''); ?>
  <?php echo render_select('curator',$staffs,array('staffid',array('firstname','lastname')), _l('hr_curator_label') ,$curator,false); ?>
</div>

 <p class="bold"><?php echo _l('hr_kb_article_files'); ?></p>
      
       <!-- file attachment -->

       <?php if(isset($attachments) ){ ?>
      <div class="row">                           
       <div id="contract_attachments" class="mtop30 col-md-8 ">
        <?php if(count($attachments) > 0){ ?>
         <?php
         $data = '<div class="row" id="attachment_file">';
         foreach($attachments as $attachment) {
          $href_url = site_url('modules/hr_profile/uploads/q_a/'.$attachment['rel_id'].'/'.$attachment['file_name']).'" download';
          if(!empty($attachment['external'])){
            $href_url = $attachment['external_link'];
          }
          $data .= '<div class="display-block contract-attachment-wrapper">';
          $data .= '<div class="col-md-10">';
          $data .= '<div class="col-md-1 mr-5">';
          $data .= '<a name="preview-btn" onclick="preview_file_q_a(this); return false;" rel_id = "'.$attachment['rel_id'].'" id = "'.$attachment['id'].'" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="'._l("preview_file").'">';
          $data .= '<i class="fa fa-eye"></i>'; 
          $data .= '</a>';
          $data .= '</div>';
          $data .= '<div class=col-md-9>';
          $data .= '<div class="pull-left"><small><i class="'.get_mime_class($attachment['filetype']).'"></i></small></div>';
          $data .= '<a href="'.$href_url.'>'.$attachment['file_name'].'</a>';
          $data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
          $data .= '</div>';
          $data .= '</div>';
          $data .= '<div class="col-md-2 text-right">';
           if(has_permission('staffmanage_job_position', '', 'delete')){
                   $data .= '<a href="#" class="text-danger" onclick="delete_q_a_attachment(this,'.$attachment['id'].'); return false;"><i class="fa fa fa-times"></i></a>';
                }
          $data .= '</div>';
          $data .= '<div class="clearfix"></div><hr/>';
          $data .= '</div>';
        }
        $data .= '</div>';
        echo html_entity_decode($data);
        ?>
      <?php } ?>                              
    </div>
  </div>
    <?php } ?>


<div class="files_preview"></div>
 <div class="publisher-tools-tab attach js_publisher-tab x-uploader" data-tab="photos"> 
      <input name="kb_article_files" id="kb_article_files" type="file" onchange="preview_file();">
      <i class="fa fa-folder js_x-uploader"></i> <?php echo _l('hr_kb_article_files'); ?>
  </div>

 
 <p class="bold"><?php echo _l('kb_article_description'); ?></p>
 <?php $contents = ''; if(isset($article)){$contents = $article->description;} ?>
 <?php echo render_textarea('description','',$contents,array(),array(),'','tinymce tinymce-manual'); ?>


<?php if((has_permission('hr_manage_q_a','','create') && !isset($article)) || has_permission('hr_manage_q_a','','edit') && isset($article)){ ?>
<div class="text-right">
  <a href="<?php echo admin_url('hr_profile/knowledge_base_q_a'); ?>"  class="btn btn-default  mright10">
                <?php echo _l('go_back'); ?>
              </a>
  <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
</div>
<?php } ?>
</div>
</div>
</div>
</div>
<?php echo form_close(); ?>
</div>

<!-- /.modal -->
<?php $this->load->view('knowledge_base_q_a/group'); ?>

<div id="contract_file_data"></div>
<?php init_tail(); ?>
<?php  require('modules/hr_profile/assets/js/knowledge_base_q_a/article_js.php'); ?>

</body>
</html>
