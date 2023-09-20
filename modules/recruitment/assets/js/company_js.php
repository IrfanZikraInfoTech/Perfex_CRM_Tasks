<script> 

(function($) {
"use strict"; 
	
	if($('#dropzoneDragArea').length > 0){
        expenseDropzone = new Dropzone("#company_form", appCreateDropzoneOptions({
          autoProcessQueue: false,
          clickable: '#dropzoneDragArea',
          previewsContainer: '.dropzone-previews',
          addRemoveLinks: true,
          maxFiles: 10,
         
            success:function(file,response){
             response = JSON.parse(response);
             if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {

                  location.reload();

             }else{

                expenseDropzone.processQueue();

             }

           },

       }));
     }

    appValidateForm($("body").find('#company_form'), {
        'company_name' : 'required',
        'company_address' : 'required',
    },expenseSubmitHandler);


})(jQuery);

 Dropzone.options.expenseForm = false;
 var expenseDropzone;

function new_company(){
    "use strict";
    $('#company').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#company input[name="company_name"]').val('');
    $('#company textarea[name="company_address"]').val('');
    $('#company textarea[name="company_industry"]').val('');

    $('#additional_company').html('');
}

function edit_company(invoker,id){
    "use strict";
    $('#additional_company').html('');
    $('#additional_company').append(hidden_input('id',id));

    $('#company input[name="company_name"]').val($(invoker).data('name'));
    $('#company textarea[name="company_address"]').val($(invoker).data('address'));
    $('#company textarea[name="company_industry"]').val($(invoker).data('industry'));

        $.post(admin_url + 'recruitment/get_company_file_url/'+id).done(function(response) {
            response = JSON.parse(response);

            $('#images_old_preview').empty();

            if(response !=''){
              $('#images_old_preview').prepend(response.arr_images);

            }


        });


    $('#company').modal('show');
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
}


 function expenseSubmitHandler(form){
  "use strict";

    var data ={};

        data.company_name = $('input[name="company_name"]').val();
        data.company_address = $('textarea[name="company_address"]').val();
        data.company_industry = $('textarea[name="company_industry"]').val();

        /*update*/
       var check_id = $('#additional_company').html();
       if(check_id){
        data.id = $('input[name="id"]').val();
       }

    $.post(form.action, data).done(function(response) {

     var response = JSON.parse(response);

      if (response.companyid) {
       if(typeof(expenseDropzone) !== 'undefined'){
        if (expenseDropzone.getQueuedFiles().length > 0) {
          expenseDropzone.options.url = admin_url + 'recruitment/add_company_attachment/' + response.companyid;
          expenseDropzone.processQueue();
        } else {
              location.reload();

        }
      } else {
        window.location.assign(response.url);
      }
    } else {
      window.location.assign(response.url);
    }
  });
    return false;
}


function delete_company_attachment(wrapper, id) {
  "use strict";
  
  if (confirm_delete()) {
     $.get(admin_url + 'recruitment/delete_company_file/' + id, function (response) {
        if (response.success == true) {
           $(wrapper).parents('.dz-preview').remove();

           var totalAttachmentsIndicator = $('.dz-preview'+id);
           var totalAttachments = totalAttachmentsIndicator.text().trim();

           if(totalAttachments == 1) {
             totalAttachmentsIndicator.remove();
           } else {
             totalAttachmentsIndicator.text(totalAttachments-1);
           }
           alert_float('success', "<?php echo _l('delete_company_file_success') ?>");

        } else {
           alert_float('danger', "<?php echo _l('delete_company_file_false') ?>");
        }
     }, 'json');
  }
  return false;
}

</script>
