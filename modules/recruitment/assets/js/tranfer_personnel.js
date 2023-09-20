function new_setting_tranfer(){
    "use strict";
    $('#setting_tranfer').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#additional_tranfer').html('');
    $('#email_to_div').addClass('hide');

    /*reset form*/
    $('#setting_tranfer input[name="subject"]').val('');
    $('#setting_tranfer input[name="order"]').val('');
    tinyMCE.activeEditor.setContent('');

    $('select[name="email_to[]"]').removeAttr('required');
}
function edit_setting_tranfer(invoker,id){
    "use strict";

    $('#additional_tranfer').html('');
    $('#additional_tranfer').append(hidden_input('id',id));
    $('#setting_tranfer input[name="subject"]').val($(invoker).data('subject'));
    $('#setting_tranfer input[name="order"]').val($(invoker).data('order'));

      $.post(admin_url + 'recruitment/get_tranfer_personnel_edit/'+id).done(function(response) {
          response = JSON.parse(response);

        tinyMCE.activeEditor.setContent(response.description);

        $('#tranfer_personnel_attachments').empty();
        $('#tranfer_personnel_attachments').append(response.htmlfile);

      });
    var _send_to = $(invoker).data('manager');
        if(typeof(_send_to) == "string"){
            $('#setting_tranfer select[name="send_to"]').val( ($(invoker).data('send_to')).split(',')).change();
        }else{
           $('#setting_tranfer select[name="send_to"]').val($(invoker).data('send_to')).change();

    }

    if($(invoker).data('send_to') != 'candidate'){
        $.post(admin_url + 'recruitment/change_send_to/'+$(invoker).data('send_to')).done(function(response) {
            response = JSON.parse(response);
            $('#email_to_div').removeClass('hide');
            if(response.type == 'staff'){
                $('select[name="email_to[]"]').attr('required','true');
                $('select[name="email_to[]"]').html('');
                $html = '';                        
                $.each(response.list, function() {
                    $html += '<option value="'+ this.email +'">'+ this.firstname +'</option>';
                 });

                $('select[name="email_to[]"]').append($html);
                $('select[name="email_to[]"]').selectpicker('refresh');

                var _send_to = $(invoker).data('manager');
                    if(typeof(_send_to) == "string"){
                        $('#setting_tranfer select[name="email_to[]"]').val( ($(invoker).data('email_to')).split(',')).change();
                    }else{
                       $('#setting_tranfer select[name="email_to[]"]').val($(invoker).data('email_to')).change();

                }
                
            }else{
                $('select[name="email_to[]"]').attr('required','true');
                $('select[name="email_to[]"]').html('');
                $html = '';                        
                $.each(response.list, function() {
                    $html += '<option value="'+ this.departmentid +'">'+ this.name +'</option>';
                 });
                
                $('select[name="email_to[]"]').append($html);
                $('select[name="email_to[]"]').selectpicker('refresh');

                var _send_to = $(invoker).data('manager');
                    if(typeof(_send_to) == "string"){
                        $('#setting_tranfer select[name="email_to[]"]').val( ($(invoker).data('email_to')).split(',')).change();
                    }else{
                       $('#setting_tranfer select[name="email_to[]"]').val($(invoker).data('email_to')).change();

                }                
            }
            
        }); 
    }else{
        $('#email_to_div').addClass('hide');
        $('select[name="email_to[]"]').removeAttr('required');
    }

    $('#setting_tranfer').modal('show');
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
    $('#email_to_div').addClass('hide');
    $('select[name="email_to[]"]').removeAttr('required');
}
var $html;
function change_send_to(invoker){
    "use strict";
    if(invoker.value != 'candidate'){
        $.post(admin_url + 'recruitment/change_send_to/'+invoker.value).done(function(response) {
            response = JSON.parse(response);
            $('#email_to_div').removeClass('hide');
            if(response.type == 'staff'){
                $('select[name="email_to[]"]').attr('required','true');
                $('select[name="email_to[]"]').html('');
                $html = '';                        
                $.each(response.list, function() {
                    $html += '<option value="'+ this.email +'">'+ this.firstname +'</option>';
                 });
                $('select[name="email_to[]"]').append($html);
                $('select[name="email_to[]"]').selectpicker('refresh');
                $('select[name="email_to[]"]').change();
            }else{
                $('select[name="email_to[]"]').attr('required','true');
                $('select[name="email_to[]"]').html('');
                $html = '';                        
                $.each(response.list, function() {
                    $html += '<option value="'+ this.departmentid +'">'+ this.name +'</option>';
                 });
                $('select[name="email_to[]"]').append($html);
                $('select[name="email_to[]"]').selectpicker('refresh');
                $('select[name="email_to[]"]').change();
            }

        }); 
    }else{
        $('#email_to_div').addClass('hide');
        $('select[name="email_to[]"]').removeAttr('required');
    }        
}

  //contract preview file
  function preview_file_tranfer_personnel(invoker){
    "use strict";

    var id = $(invoker).attr('id');
    var rel_id = $(invoker).attr('rel_id');
    view_file_tranfer_personnel(id, rel_id);
  }

   //function view file
  function view_file_tranfer_personnel(id, rel_id) {   
      "use strict";

      $('#personnel_attachments_file_data').empty();
      $("#personnel_attachments_file_data").load(admin_url + 'recruitment/re_preview_transfer_personnal_file/' + id + '/' + rel_id, function(response, status, xhr) {
          if (status == "error") {
              alert_float('danger', xhr.statusText);
          }
      });
  }

    //function delete tranfer_personnel file 
  function delete_tranfer_personnel_attachment(wrapper, id) {
    "use strict";

    if (confirm_delete()) {
       $.get(admin_url + 'recruitment/delete_transfer_personnal_attachment_file/' + id, function (response) {
          if (response.success == true) {
             $(wrapper).parents('.contract-attachment-wrapper').remove();

             var totalAttachmentsIndicator = $('.attachments-indicator');
             var totalAttachments = totalAttachmentsIndicator.text().trim();
             if(totalAttachments == 1) {
               totalAttachmentsIndicator.remove();
             } else {
               totalAttachmentsIndicator.text(totalAttachments-1);
             }

             alert_float('success', response.message);
          } else {
             alert_float('danger', response.message);
          }
       }, 'json');
    }
    return false;
   }

