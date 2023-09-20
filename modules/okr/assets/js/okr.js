(function(){
    "use strict";
    var fnServerParams = {
      "status" : 'select[name="status"]',
      "okrs" : 'select[name="okrs"]',
      "person_assigned" : 'select[name="person_assigned"]',
      "category" : 'select[name="category"]',
      "department" : 'select[name="department"]',
      "circulation" : 'select[name="circulation"]',
      "type" : 'select[name="type"]',
    }
    initDataTable('.table-dashboard', admin_url + 'okr/table_dashboard', false, false, fnServerParams, [0, 'desc']);
    var fnServerParamss = {
    "list_tasks": "[name='list_tasks']",
    "key_result": "[name='key_result']",
    "okrs_id": "[name='okrs_id']"
    };
    initDataTable('.table-task-list', admin_url + 'okr/task_list_table', false, false, fnServerParamss, [0, 'desc']);
    appValidateForm($('#okrs-new-main-form'), {
           'circulation': 'required',
           'your_target': 'required',
           'main_results[0]': 'required',
           'target[0]': 'required',
           'person_assigned': 'required',
           'display': 'required',
           'type': 'required',
    });
    var addMoreBoxInformationInputKey = $('.list textarea[name*="main_results"]').length;
    $("body").on('click', '.new_box', function() {
        if ($(this).hasClass('disabled')) { return false; }
        var newattachment = $('.list').find('#item').eq(0).clone().appendTo('.list');
        newattachment.find('button[role="combobox"]').remove();
        
        newattachment.find('textarea[id="main_results[0]"]').attr('id', 'main_results[' + addMoreBoxInformationInputKey + ']');
        newattachment.find('textarea[name="main_results[0]"]').attr('name', 'main_results[' + addMoreBoxInformationInputKey + ']');
        newattachment.find('label[for="main_results[0]"]').attr('for', 'main_results[' + addMoreBoxInformationInputKey + ']');

        newattachment.find('input[name="target[0]"]').attr('name', 'target[' + addMoreBoxInformationInputKey + ']').val('');
        newattachment.find('input[id="target[0]"]').attr('id', 'target[' + addMoreBoxInformationInputKey + ']').val('');
        newattachment.find('label[for="target[0]"]').attr('for', 'target[' + addMoreBoxInformationInputKey + ']');

        newattachment.find('textarea[name="plan[0]"]').attr('name', 'plan[' + addMoreBoxInformationInputKey + ']').val('');
        newattachment.find('textarea[id="plan[0]"]').attr('id', 'plan[' + addMoreBoxInformationInputKey + ']').val('');
        newattachment.find('label[for="plan[0]"]').attr('for', 'target[' + addMoreBoxInformationInputKey + ']');

        newattachment.find('textarea[name="results[0]"]').attr('name', 'results[' + addMoreBoxInformationInputKey + ']').val('');
        newattachment.find('textarea[id="results[0]"]').attr('id', 'results[' + addMoreBoxInformationInputKey + ']').val('');
        newattachment.find('label[for="results[0]"]').attr('for', 'target[' + addMoreBoxInformationInputKey + ']');


        newattachment.find('select[name="unit[0]"]').attr('name', 'unit[' + addMoreBoxInformationInputKey + ']').val('');
        newattachment.find('select[id="unit[0]"]').attr('id', 'unit[' + addMoreBoxInformationInputKey + ']').val('');
        newattachment.find('label[for="unit[0]"]').attr('for', 'unit[' + addMoreBoxInformationInputKey + ']');

        newattachment.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
        newattachment.find('button[name="add"]').removeClass('new_box').addClass('remove_box').removeClass('btn-success').addClass('btn-danger');

        $('textarea[name="main_results['+addMoreBoxInformationInputKey+']"]').val('');
        $('input[name="target['+addMoreBoxInformationInputKey+']"]').val('');
        $('textarea[name="plan['+addMoreBoxInformationInputKey+']"]').val('');
        $('textarea[name="results['+addMoreBoxInformationInputKey+']"]').val('');
        $('select[name="unit['+addMoreBoxInformationInputKey+']"]').val('');
        init_selectpicker();

      addMoreBoxInformationInputKey++;

    });

    $('.popovers').click(function(){
      var content = $(this).attr('data-content');
      var title = $(this).attr('data-original-title');
      $('#myModal_preview .modal-title').html(title);
      $('#myModal_preview .modal-body').html(content);
      $('#myModal_preview').modal('show');
    })

    $("body").on('click', '.remove_box', function() {
        $(this).parents('#item').remove();
    });
    
    $('select[name="type"]').on('change', function(){
       if($(this).val() == 1){
          $('.staff-current').removeClass("hide");
          $('.department-current').addClass('hide');
          $('select[name="department"]').val('').change();
          
          if($('.category-current').hasClass('col-md-6')){
            $('.category-current').removeClass('col-md-6');
            $('.category-current').addClass('col-md-4');
          }
          if($('.type-current').hasClass('col-md-6')){
            $('.type-current').removeClass('col-md-6');
            $('.type-current').addClass('col-md-4');
          }
       }else if($(this).val() == 2){
          $('.department-current').removeClass('hide');
          $('.staff-current').addClass('hide');
          if($('.category-current').hasClass('col-md-6')){
            $('.category-current').removeClass('col-md-6');
            $('.category-current').addClass('col-md-4');
          }
          if($('.type-current').hasClass('col-md-6')){
            $('.type-current').removeClass('col-md-6');
            $('.type-current').addClass('col-md-4');
          }
       }else if($(this).val() == 3){
          $('.staff-current').addClass('hide');
          $('.department-current').addClass('hide');
          $('.category-current').addClass('col-md-6');
          $('.category-current').removeClass('col-md-4');
          $('.type-current').addClass('col-md-6');
          $('.type-current').removeClass('col-md-4');
       }  
    })
    $('.circulation_new').on('change', function(){
      var id = $(this).val();
      requestGet('okr/set_okr_superior/' + id).done(function(success) {
        success = JSON.parse(success);
        $('select[name="okr_superior').html('');
        $('select[name="okr_superior').append('<option value=""></option>');
        $('select[name="okr_superior').append(success);
        $('select[name="okr_superior').selectpicker('refresh');
      }).fail(function(error) {
          alert_float('danger', 'Error');
      });
    })
    change_filter();
    $(document).on('click', '.task_list_view_rs', function(){
      var id = $(this).parents('tr').data('key-result');
      requestGet(admin_url+'okr/task_list_view_key_result/'+id).done(function(response) {
        response = JSON.parse(response);
        $('input[name="list_tasks"]').val(response);
        $('input[name="key_result"]').val(id);
        $('.table-task-list').DataTable().ajax.reload();
        $('#add-task-view-key-result').modal('show');

      })
      
    })

    

})(jQuery);


function formatCurrency(input, blur) {
"use strict";
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
function formatNumber(n) {
"use strict";
  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}

function preview_okrs_btn(invoker){
    "use strict";
      var id = $(invoker).attr('id');
      var rel_id = $(invoker).attr('rel_id');
      view_okrs_file(id, rel_id);
  }

function view_okrs_file(id, rel_id) {
    "use strict";
        $('#okrs_file_data').empty();
        $("#okrs_file_data").load(admin_url + 'okr/file_okrs/' + id + '/' + rel_id, function(response, status, xhr) {
            if (status == "error") {
                alert_float('danger', xhr.statusText);
            }
        });
  }

function delete_okrs_attachment(id) {
    "use strict";
      if (confirm_delete()) {
          requestGet('okr/delete_okrs_attachment/' + id).done(function(success) {
              if (success == 1) {
                  $("#okrs_pv_file").find('[data-attachment-id="' + id + '"]').remove();
              }
          }).fail(function(error) {
              alert_float('danger', error.responseText);
          });
      }
  }
function close_modal_preview(){
    "use strict";
 $('._project_file').modal('hide');
}

function change_filter(){
  "use strict";
  $('select[name="status"]').change(function(){
      $('.table-dashboard').DataTable().ajax.reload();
  })

  $('select[name="okrs"]').change(function(){
      $('.table-dashboard').DataTable().ajax.reload();
  })

  $('select[name="person_assigned"]').change(function(){
      $('.table-dashboard').DataTable().ajax.reload();
  })

  $('select[name="category"]').change(function(){
      $('.table-dashboard').DataTable().ajax.reload();
  })

  $('select[name="department"]').change(function(){
      $('.table-dashboard').DataTable().ajax.reload();
  })

  $('select[name="circulation"]').change(function(){
      $('.table-dashboard').DataTable().ajax.reload();
  })

  $('select[name="type"]').change(function(){
      $('.table-dashboard').DataTable().ajax.reload();
  })
}