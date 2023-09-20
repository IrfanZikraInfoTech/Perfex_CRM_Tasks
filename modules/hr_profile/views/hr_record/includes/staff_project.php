<div class="row">

     <div class="col-md-12<?php if($this->input->get('notifications')){echo ' hide';} ?>">
      
     <?php if(($staff_p->staffid == get_staff_user_id() || is_admin()) && !$this->input->get('notifications')) { ?>
         <h4 class="no-margin">
          <?php echo _l('projects'); ?>
        </h4>
        <hr class="hr-panel-heading" />
        <div class="_filters _hidden_inputs hidden staff_projects_filter">
          <?php echo form_hidden('staff_id',$staff_p->staffid); ?>
        </div>
        <?php render_datatable(array(
          _l('project_name'),
          _l('project_start_date'),
          _l('project_deadline'),
          _l('project_status'),
          ),'staff-projects',[], [
              'data-last-order-identifier' => 'my-projects',
              'data-default-order'  => get_table_last_order('my-projects'),
          ]); ?>
     <?php } ?>
   </div>
</div>