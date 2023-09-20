   (function(){
    'use strict';
    var position_training_id = {};
    var Input_totall= 0;
    var addnewkpi = 0;
    var InputKeyallowance = 0;
    var addMoreVendorsInputKey=0;
    window.addEventListener('load',function(){
      'use strict';
      appValidateForm($("body").find('.job_position_interview_add_edit'), {
        'order': 'required',
        'head_unit': 'required',
        'position_name': 'required',
        'job_p_id': 'required',
      });
    });

    window.addEventListener('load',function(){
      'use strict';
      appValidateForm($("body").find('.job_position_training_add_edit'), {
        'training_type': 'required',
      });
    });

    addnewkpi = $('.new-kpi-al').children().length;
    $("body").on('click', '.new_kpi', function() {
      'use strict';

      var idrow = $(this).parents('.new-kpi-al').find('.get_id_row').attr("value");
      if ($(this).hasClass('disabled')) { return false; }

      var newkpi = $(this).parents('.new-kpi-al').find('#new_kpi').eq(0).clone().appendTo($(this).parents('.new-kpi-al'));

      newkpi.find('button[data-toggle="dropdown"]').remove();

      newkpi.find('select[id="evaluation_criteria[' + idrow + '][0]"]').attr('name', 'evaluation_criteria[' + idrow + '][' + addnewkpi + ']').val('');
      newkpi.find('select[id="evaluation_criteria[' + idrow + '][0]"]').attr('id', 'evaluation_criteria[' + idrow + '][' + addnewkpi + ']').val('');        

      newkpi.find('input[id="percent[' + idrow + '][0]"]').attr('name', 'percent[' + idrow + '][' + addnewkpi + ']').val('');
      newkpi.find('input[id="percent[' + idrow + '][0]"]').attr('id', 'percent[' + idrow + '][' + addnewkpi + ']').val('');

      newkpi.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
      newkpi.find('button[name="add"]').removeClass('new_kpi').addClass('remove_kpi').removeClass('btn-success').addClass('btn-danger');

      newkpi.find('select').selectpicker('val', '');
      addnewkpi++;

    });

    $("body").on('click', '.remove_kpi', function() {
      'use strict';
      $(this).parents('#new_kpi').remove();
    });    

    Input_totall = $('.new-kpi-group-al').children().length;
    $("body").on('click', '.new_kpi_group', function() {
      'use strict';

      if ($(this).hasClass('disabled')) { return false; }
      var addMore = 0;

      var newkpigroup = $('.new-kpi-group-al').find('#new_kpi_group').eq(0).clone().appendTo('.new-kpi-group-al');

      for(var i = 0; i <= newkpigroup.find('#new_kpi').length ; i++){
        if(i > 0){
          newkpigroup.find('#new_kpi').eq(i).remove();
        }
        newkpigroup.find('#new_kpi').eq(1).remove();
      }

      newkpigroup.find('button[data-toggle="dropdown"]').remove();
      newkpigroup.find('select').selectpicker('refresh');

      newkpigroup.find('select[id="group_criteria[0]"]').attr('name', 'group_criteria[' + Input_totall + ']').val('');
      newkpigroup.find('select[id="group_criteria[0]"]').attr('id', 'group_criteria[' + Input_totall + ']').val('');
      newkpigroup.find('button[data-id="group_criteria[0]"]').attr('data-id', 'group_criteria[' + Input_totall + ']');

        // start expense
        newkpigroup.find('select[id="evaluation_criteria[0][0]"]').attr('name', 'evaluation_criteria[' + Input_totall + '][' + addMore + ']').val('');
        newkpigroup.find('select[id="evaluation_criteria[0][0]"]').attr('id', 'evaluation_criteria[' + Input_totall + '][' + addMore + ']').val('');
        newkpigroup.find('select[data-sl-id="e_criteria[0]"]').attr('data-sl-id', 'e_criteria[' + Input_totall + ']');
        newkpigroup.find('label[for="evaluation_criteria[0][0]"]').attr('for', 'evaluation_criteria[' + Input_totall + '][' + addMore + ']');

        


        newkpigroup.find('input[id="percent[0][0]"]').attr('name', 'percent[' + Input_totall + '][' + addMore + ']').val('');
        newkpigroup.find('input[id="percent[0][0]"]').attr('id', 'percent[' + Input_totall + '][' + addMore + ']').val('');
        newkpigroup.find('label[for="percent[0][0]"]').attr('for', 'percent[' + Input_totall + '][' + addMore + ']');

        

        newkpigroup.find('label[for="evaluation_criteria[' + Input_totall + '][' + addMore + ']"]').attr('value',  Input_totall);

        newkpigroup.find('div[name="button_add_kpi_group"]').removeAttr("style");
        newkpigroup.find('button[name="add_kpi_group"] i').removeClass('fa-plus').addClass('fa-minus');
        newkpigroup.find('button[name="add_kpi_group"]').removeClass('new_kpi_group').addClass('remove_kpi_group').removeClass('btn-success').addClass('btn-danger');

        newkpigroup.find('select').selectpicker('val', '');

        init_datepicker();
        Input_totall++;

      });

    $("body").on('click', '.remove_kpi_group', function() {
      'use strict';
      $(this).parents('#new_kpi_group').remove();
    });

    addMoreVendorsInputKey = $('.salary_currency').length;
    $("body").on('click', '.new_contract_expense', function() {
     'use strict';
     var idrow = $(this).parents('.contract-expense-al').find('.get_id_row').attr("value");
     if ($(this).hasClass('disabled')) { return false; }
     var newattachment = $(this).parents('.contract-expense-al').find('#contract-expense').eq(0).clone().appendTo($(this).parents('.contract-expense-al'));
     newattachment.find('button[role="combobox"]').remove();
     newattachment.find('select').selectpicker('refresh');
     newattachment.find('input[id="contract_expense[0]"]').attr('name', 'contract_expense[' + addMoreVendorsInputKey + ']').val('');
     newattachment.find('input[id="contract_expense[0]"]').attr('id', 'contract_expense['+ addMoreVendorsInputKey + ']').val('');
     newattachment.find('label[for="salary_form[0]"]').attr('for', 'salary_form[' + addMoreVendorsInputKey + ']');
     newattachment.find('select[name="salary_form[0]"]').attr('name', 'salary_form[' + addMoreVendorsInputKey + ']');
     newattachment.find('select[id="salary_form[0]"]').attr('id', 'salary_form['+ addMoreVendorsInputKey + ']').selectpicker('refresh');
     newattachment.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
     newattachment.find('button[name="add"]').removeClass('new_contract_expense').addClass('remove_contract_expense').removeClass('btn-success').addClass('btn-danger');
     newattachment.find('select').selectpicker('val', '');
     addMoreVendorsInputKey++;
     $("input[data-type='currency']").on({
      keyup: function() {        
        formatCurrency($(this));
      },
      blur: function() { 
        formatCurrency($(this), "blur");
      }
    });
   });

    $("body").on('click', '.remove_contract_expense', function() {
      'use strict';
      $(this).parents('#contract-expense').remove();
    });





    InputKeyallowance = $('.contract-allowance-type').children().length;
    $("body").on('click', '.new_contract_allowance_type', function() {
      'use strict';
      if ($(this).hasClass('disabled')) { return false; }
      var idrow_allowance = $(this).parents('.contract-allowance-type').find('.get_id_row_allowance').attr("value");
      var newattachment = $(this).parents('.contract-allowance-type').find('#contract-allowancetype').eq(0).clone().appendTo($(this).parents('.contract-allowance-type'));
      newattachment.find('button[role="combobox"]').remove();
      newattachment.find('select').selectpicker('refresh');
      newattachment.find('input[id="allowance_expense[0]"]').attr('name', 'allowance_expense['+ InputKeyallowance + ']').val('');
      newattachment.find('input[id="allowance_expense[0]"]').attr('id', 'allowance_expense[' + InputKeyallowance + ']').val('');
      newattachment.find('select[name="allowance_type[0]"]').attr('name', 'allowance_type[' + InputKeyallowance + ']');
      newattachment.find('select[id="allowance_type[0]"]').attr('id', 'allowance_type[' + InputKeyallowance + ']').selectpicker('refresh');
      newattachment.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
      newattachment.find('button[name="add"]').removeClass('new_contract_allowance_type').addClass('remove_contract_allowance_type').removeClass('btn-success').addClass('btn-danger');
      newattachment.find('select').selectpicker('val', '');
      InputKeyallowance++;
      $("input[data-type='currency']").on({
        keyup: function() {        
          formatCurrency($(this));
        },
        blur: function() { 
          formatCurrency($(this), "blur");
        }
      });
    });

    $("body").on('click', '.remove_contract_allowance_type', function() {
      'use strict';
      $(this).parents('#contract-allowancetype').remove();
    });

    $("input[data-type='currency']").on({
      keyup: function() {        
        formatCurrency($(this));
      },
      blur: function() { 
        formatCurrency($(this), "blur");
      }
    });
  })(jQuery);

  function formatNumber(n) {
    'use strict';
    return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
  }
  
  function formatCurrency(input, blur) {
    'use strict';
    var input_val = input.val();
    if (input_val === "") { return; }
    var original_len = input_val.length;
    var caret_pos = input.prop("selectionStart");
    if (input_val.indexOf(".") >= 0) {
      var decimal_pos = input_val.indexOf(".");
      var left_side = input_val.substring(0, decimal_pos);
      var right_side = input_val.substring(decimal_pos);
      left_side = formatNumber(left_side);

      right_side = formatNumber(right_side);
      right_side = right_side.substring(0, 2);
      input_val = left_side + "." + right_side;

    } else {
      input_val = formatNumber(input_val);
      input_val = input_val;
    }
    input.val(input_val);
    var updated_len = input_val.length;
    caret_pos = updated_len - original_len + caret_pos;
    input[0].setSelectionRange(caret_pos, caret_pos);
  }
  function new_interview_process(){
    'use strict';
    $('.add-title-interview').addClass('hide');
    $('.edit-title-interview').removeClass('hide');
    $('#additional_proposal').empty();
    $('#additional_form_name').empty();
  }

  function new_training_process(){
    'use strict';

    $('.add-title-training').addClass('hide');
    $('.edit-title-training').removeClass('hide');

    $('#additional_proposal').empty();
    $('#additional_form_training').empty();

    $('#job_position_training input[name="training_name"]').val('');
    $('#job_position_training input[name="mint_point"]').val('');

    $('#job_position_training select[name="training_type"]').val('');
    $('#job_position_training select[name="training_type"]').change();
    position_training_id = ('').split(',');
  }

  function edit_training_process(invoker,id, rec_evaluation_form_id){
    'use strict';
    $('.edit-title-training').addClass('hide');
    $('.add-title-training').removeClass('hide');
    $('#additional_proposal').append(hidden_input('id_interview',id));
    $('#additional_form_name').append(hidden_input('additional_form_name',rec_evaluation_form_id));
    $('#job_position_training input[name="training_name"]').val($(invoker).data('training_name'));
    $('#job_position_training input[name="mint_point"]').val($(invoker).data('job_position_mint_point'));
    $('#job_position_training select[name="training_type"]').val($(invoker).data('job_position_training_type'));
    $('#job_position_training select[name="training_type"]').change();
    $.post(admin_url + 'hr_profile/get_list_job_position_training/'+id).done(function(response) {
      response = JSON.parse(response);
      tinyMCE.activeEditor.setContent(response.description);
      $('.selectpicker').selectpicker({
      });
    });
    position_training_id = ($(invoker).data('job_position_training_id')).split(',');
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
  }


  function edit_interview_process(invoker,id, rec_evaluation_form_id){
    'use strict';
    $('.edit-title-interview').addClass('hide');
    $('.add-title-interview').removeClass('hide');
    $('#additional_proposal').append(hidden_input('id_interview',id));
    $('#additional_form_name').append(hidden_input('additional_form_name',rec_evaluation_form_id));
    $('#job_position_interview input[name="interview_name"]').val($(invoker).data('name'));
    $('#job_position_interview input[name="form_name"]').val($(invoker).data('form_name'));
    $('#job_position_interview input[name="order"]').val($(invoker).data('job_position_interview_order'));
    $('#job_position_interview select[name="head_unit"]').val($(invoker).data('job_position_interview_head_unit'));
    $('#job_position_interview select[name="head_unit"]').change();
    $('#job_position_interview select[name="specific_people"]').val($(invoker).data('job_position_interview_specific_people'));
    $('#job_position_interview select[name="specific_people"]').change();
    var job_position_id_str = $(invoker).data('job_position_id');
    if(typeof(job_position_id_str) == "string"){
      $('#job_position_interview select[name="job_position_id[]"]').val( ($(invoker).data('job_position_id')).split(',')).change();
    }else{
      $('#job_position_interview select[name="job_position_id[]"]').val($(invoker).data('job_position_id')).change();
    }
    $.post(admin_url + 'hr_profile/get_list_job_position_interview_edit/'+rec_evaluation_form_id+'/'+id).done(function(response) {
      response = JSON.parse(response);
      $('#list_criteria').html('');
      $('#list_criteria').append(response.html);
      Input_totall = response.count_group;
      addnewkpi = response.max_criteria;
      $('#job_position_interview input[name="form_name"]').val(response.form_name);
      tinyMCE.activeEditor.setContent(response.description);
      $('.selectpicker').selectpicker({
      });
    });
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
  }


  function group_criteria_change(invoker){
    'use strict';
    var result = invoker.name.match(/\d/g);
    $.post(admin_url + 'recruitment/get_criteria_by_group/'+invoker.value).done(function(response) {
      response = JSON.parse(response);
      $('select[data-sl-id="e_criteria['+result+']').html('');
      $('select[data-sl-id="e_criteria['+result+']').append(response.html);
      $('select[data-sl-id="e_criteria['+result+']').selectpicker('refresh');
    }); 
  }



  function training_type_change(invoker){
    'use strict';
    if(invoker.value){
      $.post(admin_url + 'hr_profile/get_training_type_child/'+invoker.value).done(function(response) {
        response = JSON.parse(response);
        $('select[name="position_training_id[]').html('');
        $('select[name="position_training_id[]').append(response.html);

        $('select[name="position_training_id[]').selectpicker('refresh');

        $('#job_position_training select[name="position_training_id[]"]').val(position_training_id).change();
      }); 
    }
  }


  function OnSelectionChange_salsaryform (select) {
    'use strict';
    var selectedOption = select.options[select.selectedIndex];
    var ex = select.name.substring(11);

    if(selectedOption.value != ''){

      var formData = new FormData();
      formData.append("csrf_token_name", $('input[name="csrf_token_name"]').val());
      formData.append("id", selectedOption.value);
      $.ajax({ 
        url: admin_url + 'hr_profile/get_staff_salary_form', 
        method: 'post', 
        data: formData, 
        contentType: false, 
        processData: false
      }).done(function(response) {
        response = JSON.parse(response);
        if(response.salary_val != null){
         document.getElementById("contract_expense"+ex).value = (response.salary_val);
       }
     });
      return false;
    }else{
      document.getElementById("contract_expense"+ex).value = '';
    }

  }

  function OnSelectionChange_allowancetype (allowancetype_value) {
    'use strict';
    var selectedOption = allowancetype_value.options[allowancetype_value.selectedIndex];
    var ex = allowancetype_value.name.substring(14);
    if(selectedOption.value != ''){
      var formData = new FormData();
      formData.append("csrf_token_name", $('input[name="csrf_token_name"]').val());
      formData.append("id", selectedOption.value);
      $.ajax({ 
        url: admin_url + 'hr_profile/get_staff_allowance_type', 
        method: 'post', 
        data: formData, 
        contentType: false, 
        processData: false
      }).done(function(response) {
        response = JSON.parse(response);
        if(response.allowance_val != null){
         document.getElementById("allowance_expense"+ex).value = (response.allowance_val);
       }
     });
      return false;
    }else{
      document.getElementById("allowance_expense"+ex).value = '';
    }
  }

    //contract preview file
    function preview_file_job_position(invoker){
      'use strict';
      var id = $(invoker).attr('id');
      var rel_id = $(invoker).attr('rel_id');
      view_hr_profilestaff_file_job_position(id, rel_id);
    }

   //function view hr_profile_file
   function view_hr_profilestaff_file_job_position(id, rel_id) {  
     'use strict'; 
     $('#contract_file_data').empty();
     $("#contract_file_data").load(admin_url + 'hr_profile/preview_job_position_file/' + id + '/' + rel_id, function(response, status, xhr) {
      if (status == "error") {
        alert_float('danger', xhr.statusText);
      }
    });
   }