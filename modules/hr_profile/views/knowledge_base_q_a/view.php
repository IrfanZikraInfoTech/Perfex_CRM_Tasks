<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" >
    <div class="content">
        <div class="row">
            <div class="col-md-7">
                <div class="panel_s">
                    <div class="panel-body tc-content">
                       <h4 class="bold no-margin"><?php echo html_entity_decode($article->subject); ?></h4>
                       <hr class="hr-panel-heading" />
                       <div class="clearfix"></div>
                       <div class="kb-article">
                         <?php echo html_entity_decode($article->description); ?>
                       </div>
                       
                       <!-- file attachment -->
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
                          $data .= '<div class="col-md-12">';
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

                  <h4 class="mtop20"><?php echo _l('clients_knowledge_base_find_useful'); ?></h4>
                    <div class="answer_response"></div>
                      

                  <div class="btn-group mtop15 article_useful_buttons" role="group">
                    <input type="hidden" name="articleid" value="<?php echo html_entity_decode($article->articleid); ?>">
                    <button type="button" data-answer="1" class="btn btn-success"><?php echo _l('clients_knowledge_base_find_useful_yes'); ?></button>
                    <button type="button" data-answer="0" class="btn btn-danger"><?php echo _l('clients_knowledge_base_find_useful_no'); ?></button>
                  </div>

                  <div class="btn-group mtop15 article_useful_buttons pull-right" role="group">
                    <?php if(isset($article->curator) && $article->curator != '' && $article->curator != 0){ ?>
                      <a href="#" onclick="send_mail_support(this);" id="support_<?php echo html_entity_decode($article->articleid); ?>" class="btn btn-success " ><?php echo _l('support') ?></a>
                    <?php } ?>

                    <a href="<?php echo admin_url('hr_profile/knowledge_base_q_a'); ?>"  class="btn btn-default  mright10">
                      <?php echo _l('go_back'); ?>
                    </a>
                  </div>

                </div>
            </div>
        </div>
        <?php if(count($related_articles) > 0){ ?>
        <div class="col-md-5">
          <div class="panel_s">
              <div class="panel-body">
                <h4 class="bold no-margin"><?php echo _l('related_knowledgebase_articles'); ?></h4>
                 <hr class="hr-panel-heading" />
                <ul class="mtop10 articles_list">
                <?php foreach($related_articles as $rel_article_article) { ?>
                    <li>
                        <i class="fa fa-file-text-o"></i>
                        <a href="<?php echo admin_url('hr_profile/knowledge_base_q_a/view/'.$rel_article_article['slug']); ?>" class="article-heading"><?php echo html_entity_decode($rel_article_article['subject']); ?></a>
                        <div class="text-muted mtop10"><?php echo strip_tags(mb_substr($rel_article_article['description'],0,100)); ?>...</div>
                    </li>
                    <hr />
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
</div>
</div>

<?php $this->load->view('knowledge_base_q_a/support_modal', $article);  ?>
<div id="contract_file_data"></div>

<?php init_tail(); ?>
<?php  require('modules/hr_profile/assets/js/knowledge_base_q_a/view_js.php'); ?>

</body>
</html>
