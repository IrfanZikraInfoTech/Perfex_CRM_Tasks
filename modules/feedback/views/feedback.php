<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
        
         <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'feedback-form','class'=>'')) ;?>
         <div class="col-md-6">
            <div class="panel_s">
               <div class="panel-body">
                  
                  <h4 class="no-margin"><?php _l('create_feedback_request'); ?></h4>
                  <hr class="hr-panel-heading" />
               <div class="form-group select-placeholder">
                     <label for="clientid" class="control-label"><?php echo _l('expense_add_edit_customer'); ?></label>
                     <select required id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                     <?php $selected = (isset($expense) ? $expense->clientid : '');
                        if($selected == ''){
                          $selected = (isset($customer_id) ? $customer_id: '');
                        }
                        if($selected != ''){
                         $rel_data = get_relation_data('customer',$selected);
                         $rel_val = get_relation_values($rel_data,'customer');
                         echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                        } ?>
                     </select>
                  </div>
                  <?php $hide_project_selector = ' hide'; ?>
                   
                  <div class="form-group projects-wrapper<?php echo $hide_project_selector; ?>">
                     <label for="project_id"><?php echo _l('project'); ?></label>
                     <div id="project_ajax_search_wrapper">
                        <select name="project_id" id="project_id" class="projects ajax-search" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <?php if(isset($expense) && $expense->project_id != 0){
                           echo '<option value="'.$expense->project_id.'" selected>'.get_project_name_by_id($expense->project_id).'</option>';
                           }
                           ?>
                        </select>
                     </div>
                  </div>
                  
                  <div class="btn-bottom-toolbar text-right">
                     <button type="submit" class="btn btn-info"><?php echo _l('request_for_feedback'); ?></button>
                  </div>
               </div>
            </div>
         </div>
        <?php echo form_close(); ?>
      </div>
      <div class="btn-bottom-pusher"></div>
   </div>
</div>

<?php init_tail(); ?>
<script>
   var customer_currency = '';
   Dropzone.options.expenseForm = false;
   var expenseDropzone;
   init_ajax_project_search_by_customer_id();
   var selectCurrency = $('select[name="currency"]');
   <?php if(isset($customer_currency)){ ?>
     var customer_currency = '<?php echo $customer_currency; ?>';
   <?php } ?>


   $('select[name="clientid"]').on('change',function(){
       customer_init();
       
     });

    function customer_init(){
        var customer_id = $('select[name="clientid"]').val();
        var projectAjax = $('select[name="project_id"]');
        var clonedProjectsAjaxSearchSelect = projectAjax.html('').clone();
        var projectsWrapper = $('.projects-wrapper');
        projectAjax.selectpicker('destroy').remove();
        projectAjax = clonedProjectsAjaxSearchSelect;
        $('#project_ajax_search_wrapper').append(clonedProjectsAjaxSearchSelect);
        init_ajax_project_search_by_customer_id();
        if(!customer_id){
           set_base_currency();
           projectsWrapper.addClass('hide');
         }
       $.get(admin_url + 'expenses/get_customer_change_data/'+customer_id,function(response){
         if(customer_id && response.customer_has_projects){
           projectsWrapper.removeClass('hide');
         } else {
           projectsWrapper.addClass('hide');
         }
         var client_currency = parseInt(response.client_currency);
       
       },'json');
     }


</script>
</body>
</html>
