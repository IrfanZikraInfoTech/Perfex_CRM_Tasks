<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
   $has_permission_edit = has_permission('hr_manage_q_a','','edit');
   $has_permission_create = has_permission('hr_manage_q_a','','create');
   ?>
<div id="wrapper" >
<div class="content">
<div class="row">
   <div class="col-md-12">
      <div class="panel_s mtop5">
         <div class="panel-body">
            <div class="_buttons">
               <?php if($has_permission_create){ ?>
               <a href="<?php echo admin_url('hr_profile/knowledge_base_q_a/article'); ?>" class="btn btn-info mright5"><?php echo _l('kb_article_new_article'); ?></a>
               <?php } ?>
               <?php if($has_permission_edit || $has_permission_create){ ?>
               <a href="<?php echo admin_url('hr_profile/knowledge_base_q_a/manage_groups'); ?>" class="btn btn-info mright5"><?php echo _l('als_kb_groups'); ?></a>
               <?php } ?>
               <a href="#" class="btn btn-default hidden-xs toggle-articles-list" onclick="initKnowledgeBaseTableArticles(); return false;">
               <i class="fa fa-th-list article_change_icon"></i>
               </a>
               <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data hide" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fa fa-filter" aria-hidden="true"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-left articles-width">
                     <li class="active">
                        <a href="#" data-cview="all" onclick="dt_custom_view('','.table-articles',''); return false;"><?php echo _l('view_articles_list_all'); ?></a>
                     </li>
                     <?php foreach($groups as $group){ ?>
                     <li><a href="#" data-cview="kb_group_<?php echo html_entity_decode($group['groupid']); ?>" onclick="dt_custom_view('kb_group_<?php echo html_entity_decode($group['groupid']); ?>','.table-articles','kb_group_<?php echo html_entity_decode($group['groupid']); ?>'); return false;"><?php echo html_entity_decode($group['name']); ?></a></li>
                     <?php } ?>
                  </ul>
               </div>
               <div class="_hidden_inputs _filters">
                  <?php foreach($groups as $group){
                     echo form_hidden('kb_group_'.$group['groupid']);
                     } ?>
               </div>
            </div>
            <hr class="hr-panel-heading" />
            <div class="row">
               <div class="col-md-12 tab-content">
                  <div role="tabpanel" class="tab-pane kb-kan-ban kan-ban-tab active" id="kan-ban">
                     <div class="container-fluid">
                        <?php
                           if(count($groups) == 0){
                            echo _l('kb_no_articles_found');
                           }
                           foreach($groups as $group){
                            $kanban_colors = '';
                            foreach(get_system_favourite_colors() as $color){
                              $color_selected_class = 'cpicker-small';
                              $kanban_colors .= "<div class='kanban-cpicker cpicker ".$color_selected_class."' data-color='".$color."' style   =  'background:".$color.";border:1px solid ".$color."'></div>";
                            }
                            ?>
                        <ul class="kan-ban-col<?php if(!$has_permission_edit){echo ' sortable-disabled'; } ?>" data-col-group-id="<?php echo html_entity_decode($group['groupid']); ?>">
                           <li class="kan-ban-col-wrapper">
                              <div class="border-right panel_s">
                                 <?php
                                    $group_color = 'style   =    "background:'.$group["color"].';border:1px solid '.$group['color'].'"';
                                    ?>
                                 <div class="panel-heading-bg primary-bg" <?php echo html_entity_decode($group_color); ?> data-group-id="<?php echo html_entity_decode($group['groupid']); ?>">
                                    <?php if($has_permission_edit){ ?>
                                    <i class="fa fa-reorder pointer"></i> <?php } ?>
                                    <a href="#" class="color-white" <?php if($has_permission_create || $has_permission_edit){ ?>onclick="edit_kb_group(this,<?php echo html_entity_decode($group['groupid']); ?>); return false;" data-name="<?php echo html_entity_decode($group['name']); ?>" data-slug="<?php echo html_entity_decode($group['group_slug']); ?>" data-color="<?php echo html_entity_decode($group['color']); ?>" data-description="<?php echo clear_textarea_breaks($group['description']); ?>" data-order="<?php echo html_entity_decode($group['group_order']); ?>" data-active="<?php echo html_entity_decode($group['active']); ?>" <?php } ?>><?php echo html_entity_decode($group['name']); ?></a>
                                    <small> - <?php echo total_rows(db_prefix().'hr_knowledge_base','articlegroup='.$group['groupid']); ?></small>
                                    <?php if($has_permission_edit){ ?>
                                    <a href="#" onclick="return false;" class="pull-right color-white kanban-color-picker" data-placement="bottom" data-toggle="popover" data-content="<div class='kan-ban-settings cpicker-wrapper'><?php echo html_entity_decode($kanban_colors); ?></div>" data-html="true" data-trigger="focus"><i class="fa fa-angle-down"></i>
                                    </a>
                                    <?php } ?>
                                 </div>
                                 <?php
                                    $this->db->select('*, (SELECT COUNT(*) FROM '.db_prefix().'hr_views_tracking WHERE rel_type="hr_profile_kb_article" AND rel_id='.db_prefix().'hr_knowledge_base.articleid) as total_views')->from(db_prefix().'hr_knowledge_base')->where('articlegroup',$group['groupid'])->order_by('article_order','asc');
                                    if(!$has_permission_create && !$has_permission_edit) {
                                      $this->db->where('active', 1);
                                      $this->db->where('question_answers', 1);
                                    }
                                    $articles = $this->db->get()->result_array();
                                    ?>
                                 <div class="kan-ban-content-wrapper">
                                    <div class="kan-ban-content">
                                       <ul class="sortable article-group groups<?php if(!$has_permission_edit){echo 'sortable-disabled'; } ?>" data-group-id="<?php echo html_entity_decode($group['groupid']); ?>">
                                          <?php foreach($articles as $article) { ?>
                                          <li class="<?php if($article['active'] == 0){echo 'line-throught';} ?>" data-article-id="<?php echo html_entity_decode($article['articleid']); ?>">
                                             <div class="panel-body">
                                                <?php if($article['staff_article'] == 1){ ?>
                                                <a href="<?php echo admin_url('hr_profile/knowledge_base_q_a/view/'.$article['slug']); ?>">
                                                <?php } else { ?>
                                                <a href="<?php echo site_url('knowledge-base/article/'.$article['slug']); ?>" target="_blank">
                                                <?php } ?><?php echo html_entity_decode($article['subject']); ?></a>
                                                <?php if($has_permission_edit){ ?>
                                                <a href="<?php echo admin_url('hr_profile/knowledge_base_q_a/article/'.$article['articleid']); ?>" target="_blank" class="pull-right"><span><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span></a>
                                                <?php } ?>
                                                <div class="clearfix"></div>
                                                <hr class="hr-10" />
                                                <?php if(isset($article['curator']) && $article['curator'] != '' && $article['curator'] != 0){ ?>

                                                  <?php echo staff_profile_image($article['curator'], ['staff-profile-image-small',], 'small', ['data-toggle' => 'tooltip', 'data-title' => _l('hr_curator_label').': '.get_staff_full_name($article['curator'])]); ?>

                                                  <a href="#" onclick="send_mail_support('<?php echo html_entity_decode($article['slug']); ?>', '<?php echo html_entity_decode($article['curator']); ?>', '<?php echo hr_get_staff_email_by_id($article['curator']); ?>');" id="support_<?php echo html_entity_decode($article['articleid']); ?>" class="btn btn-xs btn-success pull-right" ><?php echo _l('support') ?></a>

                                                <?php } ?>
                                             </div>
                                          </li>
                                          <?php } ?>
                                       </ul>
                                    </div>
                                 </div>
                           </li>
                        </ul>
                        <?php } ?>
                        </div>
                     </div>
                     <div role="tabpanel" class="tab-pane " id="list_tab">
                      <div class="row">
                        <div  class="col-md-3 leads-filter-column pull-right">
                          <select name="group[]" id="group" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('als_kb_groups'); ?>">
                            <?php foreach($groups as $group) { ?>
                              <option value="<?php echo html_entity_decode($group['groupid']); ?>"><?php echo html_entity_decode($group['name']); ?></option>
                            <?php } ?>
                          </select>
                        </div> 
                      </div>
                      <br>
                        <div class="col-md-12">

                          <div class="modal bulk_actions" id="table_contract_bulk_actions" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                  <h4 class="modal-title"><?php echo _l('hr_bulk_actions'); ?></h4>
                                </div>
                                <div class="modal-body">
                                  <?php if(has_permission('hr_manage_q_a','','delete') || is_admin()){ ?>
                                    <div class="checkbox checkbox-danger">
                                      <input type="checkbox" name="mass_delete" id="mass_delete">
                                      <label for="mass_delete"><?php echo _l('hr_mass_delete'); ?></label>
                                    </div>
                                  <?php } ?>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>

                                  <?php if(has_permission('hr_manage_q_a','','delete') || is_admin()){ ?>
                                    <a href="#" class="btn btn-info" onclick="staff_delete_bulk_action(this); return false;"><?php echo _l('hr_confirm'); ?></a>
                                  <?php } ?>
                                </div>
                              </div>
                            </div>
                          </div>

                          <?php if (has_permission('hr_manage_q_a','','delete')) { ?>
                            <a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-articles" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('hr_bulk_actions'); ?></a>
                          <?php } ?>

                           <?php render_datatable(
                              array(
                                '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="articles"><label></label></div>',
                                _l('kb_dt_article_name'),
                                _l('kb_dt_group_name'),
                                _l('date_published'),
                              ),'articles',[],[
                                'id'=>'table-kb-articles',
                                'data-last-order-identifier' => 'kb-articles',
                                'data-default-order'         => get_table_last_order('kb-articles'),
                              ]); ?>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php $this->load->view('knowledge_base_q_a/support_modal');  ?>

<?php include_once(APPPATH.'views/admin/knowledge_base/group.php'); ?>
<?php init_tail(); ?>
<?php  require('modules/hr_profile/assets/js/knowledge_base_q_a/articles_js.php'); ?>

</body>
</html>
