<!--mail_modal-->
<div class="modal" id="support_<?php echo isset($article) ? $article->articleid : '' ; ?>_form" tabindex="-1" role="dialog">
  <div class="modal-dialog">
      <?php echo form_open_multipart(admin_url('hr_profile/knowledge_base_q_a/send_mail_support'),array('id'=>'mail_form_knowledge_base')); ?>
      <div class="modal-content w-100">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">
                  <span><?php echo _l('send_mail'); ?></span>
              </h4>
          </div>
          <div class="modal-body">
              <div class="row">
                <div class="col-md-12">

                  <label for="candidate"><?php echo _l('send_to'); ?></label>
                  <?php 
                    $article_slug = isset($article) ? $article->slug : '';
                    $article_curator = isset($article) ? $article->curator : '';
                   ?>
                  <?php echo form_hidden('slug',$article_slug); ?>
                  <?php echo form_hidden('curator',$article_curator); ?>
                </div> 
                  <div class="col-md-12">
                    <?php 
                    $staff_email = isset($article) ? hr_get_staff_email_by_id($article->curator) : '';
                   ?>
                    <div class="form-group">
                      <input class="form-control" name="show_staff_email" value="<?php echo html_entity_decode($staff_email) ?>" type="text" placeholder="Disabled input here..." disabled>
                    </div>
                  </div>
                 
                
                <div class="col-md-12">
                  <?php echo render_input('subject','subject'); ?>
                </div>

                <div class="col-md-12">
                  <?php echo render_textarea('content','content','',array(),array(),'','tinymce') ?>
                </div>     
                      
              </div>
          </div>
          <div class="modal-footer">
              <button type=""class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
              <button id="sm_btn" type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
          </div>
      </div><!-- /.modal-content -->
          <?php echo form_close(); ?>
      </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
