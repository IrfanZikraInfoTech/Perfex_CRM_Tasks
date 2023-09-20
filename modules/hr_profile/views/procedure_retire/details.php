<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">

            <div>
              <?php echo form_open(admin_url('hr_profile/procedure_form')); ?>


              <div class="row">
               <div class="col-md-12">
                <h4 class="no-margin font-bold text-danger"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo _l('hr_configure_procedure_retire') ?></h4>
                <hr>
              </div>
            </div>  

            <div class="_buttons pull-left">
              <?php if(is_admin() || has_permission('hrm_setting','','create')) {?>
                <a href="#" id="add_save" onclick="add_procedure_retire(); return false;" class="btn btn-info pull-left display-block ">
                  <?php echo _l('hr_hr_add'); ?>
                </a>

              <?php } ?>

              <a href="<?php echo admin_url('hr_profile/setting?group=procedure_retire'); ?>"  class="btn btn-default pull-left display-block  mleft10">
                <?php echo _l('hr_go_back_setting_menu'); ?>
              </a>

            </div>
            <br><br>
            <br>
            <br>
            <div class="clearfix"></div>
              
            <div class="load_add_box"></div>
            <?php echo form_close(); ?>
            <div class="total_box">
              <?php foreach ($procedure_retire as $key => $value) {?>

                <?php if($value['people_handle_id'] == get_staff_user_id() || is_admin() || has_permission('hrm_setting','','create') || has_permission('hrm_setting','','edit')){?>
                  <div class="row">
                    <div class="col-md-11">
                      <h5 class="no-margin font-bold"> <?php echo _l('hr_step'); ?> <?php echo html_entity_decode($key+1); ?>:  <?php echo html_entity_decode($value['rel_name']); ?>
                      <span >( <?php echo get_staff_full_name($value['people_handle_id']); ?> )</span>
                    </h5>

                  </div>
                  <div class="col-md-1">
                    <?php if(is_admin() || has_permission('hrm_setting','','delete')) {?>

                      <a href="<?php echo admin_url('hr_profile/delete_procedure_retire/'.$value['id'].'/'.$id) ?>"  data-id="<?php echo html_entity_decode($value['id']); ?>" data-parent_id="<?php echo html_entity_decode($id); ?>" class=" pull-right btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>

                    <?php } ?>

                    <div class="pull-right">&nbsp;&nbsp;</div>
                    <?php if(is_admin() || has_permission('hrm_setting','','edit')) {?>
                      <a href="#" onclick="edit_procedure_retire(this); " data-id="<?php echo html_entity_decode($value['id']); ?>" class=" pull-right btn btn-warning btn-icon"><i class="fa fa-pencil-square-o"></i></a>
                    <?php } ?>

                  </div>
                  <br>
                  <br>

                  <div class="col-md-12">
                    <div class="">
                      <?php $option_select = json_decode($value['option_name']); ?>
                      <?php foreach ($option_select as $key => $option) {?>
                        <div class="box_area">
                          <div class="row">
                            <div class="col-md-1">
                            </div>
                            <?php if($option) { ?>
                              <div class="col-md-11">
                                <input type="text" placeholder="<?php echo _l('hr_add_options'); ?>..." data-box-descriptionid="" value="<?php echo html_entity_decode($option); ?>" class="my-3 form-control" disabled>
                              </div>
                            <?php } ?>  
                          </div>
                          <br/><div class="clearfix"></div>
                        </div>
                      <?php }?>
                    </div>
                  </div> 

                </div>
              <?php } ?>
            <?php } ?>
          </div>

          <div class="form-group hide">
            <div class="checkbox checkbox-primary">
              <input type="checkbox" id="mange_asset" value="total_salary" checked >
              <label for="mange_asset"><?php echo _l('hr_hand_over_assets_received_when_working'); ?></label>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
</div>


<div class="modal" id="myModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <?php echo form_open(admin_url('hr_profile/edit_procedure_form')); ?>
      <?php echo form_hidden('id'); ?>
      <?php echo form_hidden('procedure_retire_id'); ?>
      <!-- Modal body -->
      <div class="modal-body">
       <span class="no-margin font-bold"><?php echo _l('hr_group_name'); ?></span>
       <input type="text" placeholder="<?php echo _l('hr_item_name_to_add'); ?>..." name="rel_name[1]" id="rel_name[1]" data-box-descriptionid="" class="my-3 form-control check_edit_cus" value="">
       <br>
       <?php echo render_select('people_handle_id',$staffs, array('staffid', array('firstname','lastname')), 'hr_people_handle_id','',array('data-live-search' => 'true') ); ?>

       <div class="content_edit">

       </div>
     </div>
     <!-- Modal footer -->
     <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
      <button type="submit" class="btn btn-info pull-right display-block"><?php echo _l('submit'); ?></button>
    </div>
    <?php echo form_close(); ?>
  </div>
</div>
</div>
<?php init_tail(); ?>
<?php 
  require('modules/hr_profile/assets/js/setting/procedure_procedure_retire_details_js.php');
 ?>
</body>
</html>
<div>
