    (function(){
      'use strict';
      window.addEventListener('load',function(){
        appValidateForm($("body").find('#job_position'), {
          'position_name': 'required',
          'position_code': 'required',
        });
      });

      $("body").on('click', '.tagit-close', function() {

       'use strict';
       var tag_id = $(this).parents('li').val();
       /*delete tag id*/
       if(tag_id){
        $.post(admin_url + 'hr_profile/job_position_delete_tag_item/'+tag_id).done(function(response) {
          response = JSON.parse(response);

          if(response.status == 'true'){
            alert_float('success', response.message);
          }else{
            alert_float('warning', response.message);
          }

        });
      }
      $(this).parents('li').remove();
    });

    var addMoreAttachmentsInputKey = 1;

    $("body").on('click', '.add_more_attachments_file', function() {
      'use strict';

      if ($(this).hasClass('disabled')) {
        return false;
      }

      var total_attachments = $('.attachments input[name*="file"]').length;
      if ($(this).data('max') && total_attachments >= $(this).data('max')) {
        return false;
      }

      var newattachment = $('.attachments').find('.attachment').eq(0).clone().appendTo('.attachments');
      newattachment.find('input').removeAttr('aria-describedby aria-invalid');
      newattachment.find('input').attr('name', 'file[' + addMoreAttachmentsInputKey + ']').val('');
      newattachment.find($.fn.appFormValidator.internal_options.error_element + '[id*="error"]').remove();
      newattachment.find('.' + $.fn.appFormValidator.internal_options.field_wrapper_class).removeClass($.fn.appFormValidator.internal_options.field_wrapper_error_class);
      newattachment.find('i').removeClass('fa-plus').addClass('fa-minus');
      newattachment.find('button').removeClass('add_more_attachments_file').addClass('remove_attachment_file').removeClass('btn-success').addClass('btn-danger');
      addMoreAttachmentsInputKey++;
    });

    // Remove attachment
    $("body").on('click', '.remove_attachment_file', function() {
      'use strict';

      $(this).parents('.attachment').remove();
    });


    //init table
    var jobPositionServerParams = {
      "department_id": "[name='department_id[]']",
      "job_p_id"    : "[name='job_p_id[]']"
    };
    var table_job_position = $('.table-table_job_position');
    initDataTable(table_job_position, admin_url+'hr_profile/table_job_position', [0], [0], jobPositionServerParams, [0, 'desc']);

    //hide first column
    var hidden_columns = [1];
        $('.table-table_job_position').DataTable().columns(hidden_columns).visible(false, false);

    $('#department_id').on('change', function() {
      table_job_position.DataTable().ajax.reload().columns.adjust().responsive.recalc();
    });
    $('#job_p_id').on('change', function() {
      table_job_position.DataTable().ajax.reload().columns.adjust().responsive.recalc();
    });

    appValidateForm($("body").find('.job_p'), {
        'job_name': 'required'
      });


  })(jQuery);

  var Input_totall =0;
  function hrrecord_new_job_position(){  
   'use strict';

    $('#new_job_positions').modal('show');  
    $('#additional_proposal').closest('form').find("input[type=text], textarea").val("");
    tinyMCE.activeEditor.setContent("");
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#additional_proposal').empty();
    $('#tags_value').find('ul li.tagit-choice').remove();
    init_tags_inputs();
    $('#job_position select[name="job_p_id"]').val('').change().selectpicker('refresh');
    $('#job_position select[id="department_id"]').val('').change().selectpicker('refresh');

    init_selectpicker();

    var rel_type ='position_code';
    requestGetJSON('hr_profile/get_code/' + rel_type).done(function (response) {
        $("input[name='position_code']").val(response.code);
    });
  }

  function hrrecord_edit_job_position(invoker,id){
     'use strict';

     $('#additional_proposal').empty();
     $('#additional_proposal').append(hidden_input('id',id));
     $('#job_position input[name="position_name"]').val($(invoker).data('name'));
     $('#job_position input[name="position_code"]').val($(invoker).data('position_code'));




     var department_id_str = $(invoker).data('department_id');

      if(typeof(department_id_str) == "string"){
        $('#job_position select[name="department_id[]"]').val( ($(invoker).data('department_id')).split(',')).change();
      }else{
        $('#job_position select[name="department_id[]"]').val($(invoker).data('department_id')).change();
      }

      $.post(admin_url + 'hr_profile/get_list_job_position_tags_file/'+id).done(function(response) {
        response = JSON.parse(response);

        if(response.job_p != 0){
          $('#job_position select[name="job_p_id"]').val(response.job_p).change();

        }else{
          $('#job_position select[name="job_p_id"]').val('').change();

        }


        $('#tags_value').find('ul li.tagit-choice').remove();
        $('#tags_value').find('ul').prepend(response.htmltag);


        init_tags_inputs();
        $('#job_position input[id="tags"]').attr('value', response.item_value);

        $('#position_attachments').empty();
        $('#position_attachments').append(response.htmlfile);
        tinymce.get("job_position_description").setContent(response.description);
        $('.selectpicker').selectpicker({
        });

      });

    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
    $('#new_job_positions').modal('show');  

  }

  function edit_job_position(invoker,id){
     'use strict';

     $('#additional_proposal').empty();
     $('#additional_proposal').append(hidden_input('id',id));
     $('#job_position input[name="position_name"]').val($(invoker).data('name'));
     $('#job_position input[name="position_code"]').val($(invoker).data('position_code'));
     var department_id_str = $(invoker).data('department_id');

      if(typeof(department_id_str) == "string"){
        $('#job_position select[name="department_id[]"]').val( ($(invoker).data('department_id')).split(',')).change();
      }else{
        $('#job_position select[name="department_id[]"]').val($(invoker).data('department_id')).change();
      }

      $.post(admin_url + 'hr_profile/get_list_job_position_tags_file/'+id).done(function(response) {
        response = JSON.parse(response);

        if(response.job_p != 0){
          $('#job_position select[name="job_p_id"]').val(response.job_p).change();

        }else{
          $('#job_position select[name="job_p_id"]').val('').change();

        }


        $('#tags_value').find('ul li.tagit-choice').remove();
        $('#tags_value').find('ul').prepend(response.htmltag);

        $('#position_attachments').empty();
        $('#position_attachments').append(response.htmlfile);

        tinyMCE.activeEditor.setContent(response.description);
        $('.selectpicker').selectpicker({
        });

      });

    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
    $('#new_job_positions').modal('show');  
    init_tags_inputs();
  }

  function preview_file_job_position(invoker){
   'use strict';

    var id = $(invoker).attr('id');
    var rel_id = $(invoker).attr('rel_id');
    view_hr_profilestaff_file_job_position(id, rel_id);
  }

  function view_hr_profilestaff_file_job_position(id, rel_id) {   
   'use strict';

    $('#contract_file_data').empty();
      $("#contract_file_data").load(admin_url + 'hr_profile/preview_job_position_file/' + id + '/' + rel_id, function(response, status, xhr) {
        $('#new_job_positions').removeClass('in');
        if (status == "error") {
          alert_float('danger', xhr.statusText);
        }
    });
  }

  function delete_job_position_attachment(wrapper, id) {
    'use strict';

    if (confirm_delete()) {
      $.get(admin_url + 'hr_profile/delete_hr_profile_job_position_attachment_file/' + id, function (response) {
        if (response.success == true) {
          $(wrapper).parents('.contract-attachment-wrapper').remove();

            var totalAttachmentsIndicator = $('.attachments-indicator');
            var totalAttachments = totalAttachmentsIndicator.text().trim();
          if(totalAttachments == 1) {
            totalAttachmentsIndicator.remove();
          } else {
            totalAttachmentsIndicator.text(totalAttachments-1);
          }
        } else {
          alert_float('danger', response.message);
        }
      }, 'json');
    }
    return false;
  }

  function new_job_p(){
    'use strict';

    $('#additional_job').empty();
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#job_p input[name="job_name"]').val('');
    $('#job_p').modal('show');
    tinyMCE.activeEditor.setContent('');
    
    $('#_create_job_position_default').removeClass('hide');

  }

    function staff_bulk_actions(){
    'use strict';

    $('#table_contract_bulk_actions').modal('show');
  }

   // Leads bulk action
   function staff_delete_bulk_action(event) {
    'use strict';

    if (confirm_delete()) {
      var mass_delete = $('#mass_delete').prop('checked');

      if(mass_delete == true){
        var ids = [];
        var data = {};

        data.mass_delete = true;
        data.rel_type = 'hrm_job_position';

        var rows = $('#table-table_job_position').find('tbody tr');
        $.each(rows, function() {
          var checkbox = $($(this).find('td').eq(0)).find('input');
          if (checkbox.prop('checked') === true) {
            ids.push(checkbox.val());
          }
        });

        data.ids = ids;
        $(event).addClass('disabled');
        setTimeout(function() {
          $.post(admin_url + 'hr_profile/hrm_delete_bulk_action', data).done(function() {
            window.location.reload();
          }).fail(function(data) {
            $('#table_contract_bulk_actions').modal('hide');
            alert_float('danger', data.responseText);
          });
        }, 200);
      }else{
        window.location.reload();
      }

    }
   }