<script>
 'use strict';
 var table_procedure_retire = $('table.table-table_procedure_retire');
 initDataTable(table_procedure_retire,admin_url + 'hr_profile/table_procedure_retire');

 appValidateForm($('form'),{position_name:'required',type_name:'required',allowance_val:'required',taxable:'required',  insurance:'required',from_month:'required'});

 appValidateForm($('#shifts-sc-form'),{shift_symbol:'required',time_start_work:'required',time_end_work:'required',start_lunch_break_time:'required',end_lunch_break_time:'required',late_latency_allowed:'required'});
 $(function() {
   'use strict';
   $('#time_start_work').datetimepicker({
    datepicker: false,
    format: 'H:i'
  });
   $('#time_end_work').datetimepicker({
    datepicker: false,
    format: 'H:i'
  });
   $('#start_lunch_break_time').datetimepicker({
    datepicker: false,
    format: 'H:i'
  });
   $('#end_lunch_break_time').datetimepicker({
    datepicker: false,
    format: 'H:i'
  });
   $('#late_latency_allowed').datetimepicker({
    datepicker: false,
    format: 'H:i'
  });

   $('.date-picker').datepicker( {
    changeMonth: true,
    changeYear: true,
    showButtonPanel: true,
    dateFormat: 'mm/yy',
    onClose: function(dateText, inst) { 
      $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
    }
  });
 });
 function get_laguage() {
   'use strict';
   return $(".header-languages").find("li.active>").html();
 }
 if($(".header-languages").find("li.active>").html() == 'Vietnamese')
 {
  $( "#dowload_file_sample" ).append('<a href="<?php echo base_url('modules/hr_profile/uploads/Sample_import_province_vi.xlsx'); ?>" class="btn btn-success" ><?php echo _l('hr_download_sample') ?></a><hr>' );

}else{
  $( "#dowload_file_sample" ).append( '<a href="<?php echo base_url('modules/hr_profile/uploads/Sample_import_province_en.xlsx'); ?>" class="btn btn-success" ><?php echo _l('hr_download_sample') ?></a><hr>' );
}


var addMoreVendorsInputKey = $('.list_approve select[name*="approver"]').length;
$("body").on('click', '.new_vendor_requests', function() {
 'use strict';
 if ($(this).hasClass('disabled')) { return false; }        
 var newattachment = $('.list_approve').find('#item_approve').eq(0).clone().appendTo('.list_approve');
 newattachment.find('button[role="button"]').remove();
 newattachment.find('select').selectpicker('refresh');

 newattachment.find('button[data-id="approver[0]"]').attr('data-id', 'approver[' + addMoreVendorsInputKey + ']');
 newattachment.find('label[for="approver[0]"]').attr('for', 'approver[' + addMoreVendorsInputKey + ']');
 newattachment.find('select[name="approver[0]"]').attr('name', 'approver[' + addMoreVendorsInputKey + ']');
 newattachment.find('select[id="approver[0]"]').attr('id', 'approver[' + addMoreVendorsInputKey + ']').selectpicker('refresh');

 newattachment.find('button[data-id="staff[0]"]').attr('data-id', 'staff[' + addMoreVendorsInputKey + ']');
 newattachment.find('label[for="staff[0]"]').attr('for', 'staff[' + addMoreVendorsInputKey + ']');
 newattachment.find('select[name="staff[0]"]').attr('name', 'staff[' + addMoreVendorsInputKey + ']');
 newattachment.find('select[id="staff[0]"]').attr('id', 'staff[' + addMoreVendorsInputKey + ']').selectpicker('refresh');

 newattachment.find('button[data-id="action[0]"]').attr('data-id', 'action[' + addMoreVendorsInputKey + ']');
 newattachment.find('label[for="action[0]"]').attr('for', 'action[' + addMoreVendorsInputKey + ']');
 newattachment.find('select[name="action[0]"]').attr('name', 'action[' + addMoreVendorsInputKey + ']');
 newattachment.find('select[id="action[0]"]').attr('id', 'action[' + addMoreVendorsInputKey + ']').selectpicker('refresh');

 newattachment.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
 newattachment.find('button[name="add"]').removeClass('new_vendor_requests').addClass('remove_vendor_requests').removeClass('btn-success').addClass('btn-danger');
 addMoreVendorsInputKey++;

});
$("body").on('click', '.remove_vendor_requests', function() {
 'use strict';
 $(this).parents('#item_approve').remove();
});

$('.account-template-form-submiter').on('click', function() {
 'use strict';
 $('input[name="account_template"]').val(account_template.getData());
});
  
  //contract type validation
  appValidateForm($('#add_contract_type'), {
      name_contracttype: 'required',
  });

  //salary type validation
  appValidateForm($('#add_salary_form'), {
      form_name: 'required',
      salary_val: 'required',
  });

  //procedure retire validation
  appValidateForm($('#add_procedure_form_manage'), {
      name_procedure_retire: 'required',
      'departmentid[]': 'required',
  });

  //procedure retire validation
  appValidateForm($('#add_workplace'), {
      name: 'required',
      
  });

  

</script>