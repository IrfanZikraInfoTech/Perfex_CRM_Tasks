<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
   <div class="panel_s">
    <div class="panel-body">
	    	<div class="clearfix"></div>
		    	
		    	<div class="col-md-12">
				 	<div class="horizontal-scrollable-tabs preview-tabs-top">
					  <div class="horizontal-tabs">
					  	<ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
					      <li role="presentation" class="tab_cart <?php if($tab == 'circulation'){ echo 'active'; } ?>">
					         <a href="<?php echo admin_url('okr/setting?tab=circulation'); ?>" aria-controls="tab_config" role="tab" aria-controls="tab_config">
					         		<?php echo _l('circulation'); ?>
					         </a>
					      </li>
					      <li role="presentation" class="tab_cart <?php if($tab == 'question'){ echo 'active'; } ?>">
					         <a href="<?php echo admin_url('okr/setting?tab=question'); ?>" aria-controls="tab1" role="tab" aria-controls="tab2">
					         		<?php echo _l('question'); ?>
					         </a>
					      </li>

					      <li role="presentation" class="tab_cart <?php if($tab == 'evaluation_criteria'){ echo 'active'; } ?>">
					         <a href="<?php echo admin_url('okr/setting?tab=evaluation_criteria'); ?>" aria-controls="tab1" role="tab" aria-controls="tab2">
					         		<?php echo _l('evaluation_criteria'); ?>
					         </a>
					      </li>
							
							<li role="presentation" class="tab_cart <?php if($tab == 'unit'){ echo 'active'; } ?>">
					         <a href="<?php echo admin_url('okr/setting?tab=unit'); ?>" aria-controls="tab1" role="tab" aria-controls="tab2">
					         		<?php echo _l('unit'); ?>
					         </a>
					      </li>
					      <li role="presentation" class="tab_cart <?php if($tab == 'category'){ echo 'active'; } ?>">
					         <a href="<?php echo admin_url('okr/setting?tab=category'); ?>" aria-controls="tab1" role="tab" aria-controls="tab2">
					         		<?php echo _l('category'); ?>
					         </a>
					      </li>	
					      <li role="presentation" class="tab_cart <?php if($tab == 'approval_process'){ echo 'active'; } ?>">
					         <a href="<?php echo admin_url('okr/setting?tab=approval_process'); ?>" aria-controls="tab1" role="tab" aria-controls="tab2">
					         		<?php echo _l('approval_process'); ?>
					         </a>
					      </li>	
					  	</ul>
					  </div>
					</div> 
					<?php $this->load->view('setting/'.$tab); ?>
				</div>
	  </div>
	</div>
  </div>
 </div>
</div>
<?php init_tail(); ?>
</body>
</html>
<script>
	
	appValidateForm($('form'),{group_name:'required', unit_name:'required'});
  $('#radio1').on('click', function(){
    $('input[name="value_choose"]').val(1);
  })

  $('#radio2').on('click', function(){
    $('input[name="value_choose"]').val(2);
  })

  $('#radio3').on('click', function(){
    $('input[name="value_choose"]').val(3);
  })
  appValidateForm($('#approval-setting-form'),{name:'required', related:'required'});


  $("body").on('click', '.new_vendor_requests', function() {
    var addMoreVendorsInputKey = $('.list_approvest select[name*="approver"]').length;

    if ($(this).hasClass('disabled')) { return false; }

    var newattachment = $('.list_approvest').find('#item_approve').eq(0).clone().appendTo('.list_approvest');
    newattachment.find('button[role="combobox"]').remove();
    newattachment.find('select').selectpicker('refresh');

    newattachment.find('button[data-id="approver[0]"]').attr('data-id', 'approver[' + addMoreVendorsInputKey + ']');
    newattachment.find('label[for="approver[0]"]').attr('for', 'approver[' + addMoreVendorsInputKey + ']');

    newattachment.find('select[name="approver[0]"]').attr('data-id', addMoreVendorsInputKey);
    newattachment.find('select[name="approver[0]"]').attr('name', 'approver[' + addMoreVendorsInputKey + ']');
    newattachment.find('select[id="approver[0]"]').attr('id', 'approver[' + addMoreVendorsInputKey + ']').selectpicker('refresh');

    newattachment.find('button[data-id="staff[0]"]').attr('data-id', 'staff[' + addMoreVendorsInputKey + ']');
    newattachment.find('label[for="staff[0]"]').attr('for', 'staff[' + addMoreVendorsInputKey + ']');
    newattachment.find('select[name="staff[0]"]').attr('name', 'staff[' + addMoreVendorsInputKey + ']');
    newattachment.find('select[id="staff[0]"]').attr('id', 'staff[' + addMoreVendorsInputKey + ']').selectpicker('refresh');

    newattachment.find('#is_staff_0').attr('id', 'is_staff_' + addMoreVendorsInputKey).addClass('hide');

    newattachment.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
    newattachment.find('button[name="add"]').removeClass('new_vendor_requests').addClass('remove_vendor_requests').removeClass('btn-success').addClass('btn-danger');

    $('select[name="approver[' + addMoreVendorsInputKey + ']"]').change(function(){
      if($(this).val() == 'specific_personnel'){
        $('#is_staff_' + $(this).attr('data-id')).removeClass('hide');
      }else{
        $('#is_staff_' + $(this).attr('data-id')).addClass('hide');
      }
    });
    addMoreVendorsInputKey++;
  });

  $("body").on('click', '.remove_vendor_requests', function() {
    $(this).parents('#item_approve').remove();
  });

  $('select[name="approver[0]"]').change(function(){
    if($(this).val() == 'specific_personnel'){
      $('#is_staff_0').removeClass('hide');
    }else{
      $('#is_staff_0').addClass('hide');
    }
  });

  $('#choose_when_approving').click(function(){
    if($(this).is(':checked')){
      $('.list_approvest').addClass('hide');
    } else {
      $('.list_approvest').removeClass('hide');

    }
  });


  var fnServerParams = {  

  }
   initDataTable('.table-approve', admin_url + 'okr/approval_table', false, false, fnServerParams, [0, 'desc']);

  
</script>
