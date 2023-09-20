<?php init_head();?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12" id="small-table">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <div class="col-md-12">
                      <h4 class="no-margin font-bold"><i class="fa fa-user-o" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                      <hr />
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                  <a href="<?php echo admin_url('recruitment/candidates'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_candidate'); ?></a>
                  <a href="#" onclick="send_mail_candidate(); return false;" class="btn btn-success pull-left display-block mleft5" ><i class="fa fa-envelope"></i><?php echo ' ' . _l('send_mail'); ?></a>

                  <a href="<?php if(!$this->input->get('project_id')){ echo admin_url('recruitment/switch_kanban/'.$switch_kanban); } else { echo admin_url('projects/view/'.$this->input->get('project_id').'?group=project_tasks'); }; ?>" class="btn btn-default mleft10 pull-left hidden-xs">
                           <?php if($switch_kanban == 1){ echo _l('switch_to_list_view');}else{echo _l('leads_switch_to_kanban');}; ?>
                  </a>
                  </div>
                  </div>
                  <br>
                  

                   <?php
                  if($this->session->has_userdata('candidate_profile_kanban_view') && $this->session->userdata('candidate_profile_kanban_view') == 'true') { ?>

                  <hr class="hr-panel-heading hr-10" />
                  <div class="clearfix"></div>
                  <div class="kan-ban-tab kan-ban-overflow" id="kan-ban-tab" >
                     <div class="row">
                        <div id="kanban-params">
                           <?php echo form_hidden('project_id',$this->input->get('project_id')); ?>
                        </div>
                        <div class="container-fluid">
                           <div id="kan-ban"></div>
                        </div>
                     </div>
                  </div>
                  <?php }else{ ?>

                  <div class="row">
                    
                    <div class="col-lg-3 pull-right">
                        <select name="rec_campaign[]" id="rec_campaign" class="selectpicker" data-live-search="true" multiple="true"  data-width="100%" data-none-selected-text="<?php echo _l('recruitment_campaign'); ?>">

                          <?php foreach ($rec_campaigns as $s) {?>
                            <option value="<?php echo html_entity_decode($s['cp_id']); ?>" <?php if (isset($candidate) && $s['cp_id'] == $candidate->rec_campaign) {echo 'selected';}?>><?php echo html_entity_decode($s['campaign_code'] . ' - ' . $s['campaign_name']); ?></option>
                            <?php }?>
                        </select>
                    </div>

                    <div class="col-lg-3 pull-right">
                      <select name="change_status[]" id="change_status" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('change_status_to'); ?>">
                        <option value="1" ><?php echo _l('application'); ?></option>
                        <option value="2" ><?php echo _l('potential'); ?></option>
                        <option value="3" ><?php echo _l('interview'); ?></option>
                        <option value="4" ><?php echo _l('won_interview'); ?></option>
                        <option value="5" ><?php echo _l('send_offer'); ?></option>
                        <option value="6" ><?php echo _l('elect'); ?></option>
                        <option value="7" ><?php echo _l('non_elect'); ?></option>
                        <option value="8" ><?php echo _l('unanswer'); ?></option>
                        <option value="9" ><?php echo _l('transferred'); ?></option>
                        <option value="10" ><?php echo _l('freedom'); ?></option>
                      </select>
                    </div>  

                  </div>

                      <br>
                      <!-- print barcode -->      
                <?php echo form_open_multipart(admin_url('recruitment/item_print_candidate'), array('id'=>'item_print_candidate')); ?>      
                <div class="modal bulk_actions" id="table_commodity_list_print_candidate" tabindex="-1" role="dialog">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php echo _l('print_candidate'); ?></h4>
                      </div>
                      <div class="modal-body">
                        <?php if(has_permission('recruitment','','create') || is_admin()){ ?>

                          <div class="row">
                            <div class=" col-md-12">
                              <div class="form-group">
                                <select name="item_select_print_candidate[]" id="item_select_print_candidate" class="selectpicker" data-live-search="true" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('select_candidate'); ?>">

                                  <?php foreach($candidates as $candidate) { ?>
                                    <option value="<?php echo html_entity_decode($candidate['id']); ?>"><?php echo html_entity_decode($candidate['candidate_code'].'-'.$candidate['candidate_name'].' '.$candidate['last_name']); ?></option>
                                  <?php } ?>
                                </select>
                              </div>
                            </div>
                          </div>

                        <?php } ?>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

                        <?php if(has_permission('recruitment','','create') || is_admin()){ ?>

                          <button type="submit" class="btn btn-info" ><?php echo _l('confirm'); ?></button>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                </div>
                <?php echo form_close(); ?>

                <a href="#"  onclick="print_candidate_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_rec_candidate" data-target="#print_candidate_item" class=" hide print_candidate-bulk-actions-btn table-btn"><?php echo _l('print_candidate'); ?></a>

                    <?php render_datatable(array(
                  	_l('id'),
                    _l('candidate_code'),
                  	_l('candidate_name'),
                  	_l('tranfer_personnel'),
                  	_l('status'),
                  	_l('email'),
                  	_l('phonenumber'),
                  	_l('birthday'),
                  	_l('campaign'),
                  ), 'table_rec_candidate');?>
              <?php } ?>

               </div>
            </div>
         </div>

      </div>
   </div>
</div>
<div class="modal fade" id="mail_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
      <?php echo form_open_multipart(admin_url('recruitment/send_mail_list_candidate'), array('id' => 'mail_candidate-form')); ?>
      <div class="modal-content width-100">
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
                    <select name="candidate[]" id="candidate" class="selectpicker" multiple="true"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >

                        <?php foreach ($candidates as $s) {?>
                        <option value="<?php echo html_entity_decode($s['id']); ?>"><?php echo html_entity_decode($s['candidate_code'] . ' ' . $s['candidate_name'].' '.$s['last_name']); ?></option>
                          <?php }?>
                    </select>
                    <br><br>
                </div>
                <div class="col-md-12">

                </div>

                <div class="col-md-12">
                  <?php echo render_input('subject', 'subject'); ?>
                </div>

                <div class="col-md-12">
                  <?php echo render_textarea('content', 'content', '', array(), array(), '', 'tinymce') ?>
                </div>
                <div id="type_care">

                </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type=""class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
              <button id="sm_btn" type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
          </div>
      </div>
          <?php echo form_close(); ?>
      </div>
  </div>

<?php init_tail();?>

</body>
</html>