<script>

  $('input[id="question_answers"]').on('click', function(){
    if($(this).prop("checked") == true){
      $('.curator').removeClass('hide');
    }
    else if($(this).prop("checked") == false){
      $('.curator').addClass('hide');
    }
  });

  $(function(){
    init_editor('#description', {append_plugins: 'stickytoolbar'});
    appValidateForm($('#article-form'),{subject:'required',articlegroup:'required'});
  });

  function preview_file() 
  {
    'use strict';
    
    $('.files_preview').css('display', 'block'); 
    $('.files_preview').append("<span><i class='fa fa-file-text' > <?php echo _l('hr_download_the_document_successfully'); ?></i></span>");
  }

  //contract preview file
  function preview_file_q_a(invoker){
    'use strict';
    var id = $(invoker).attr('id');
    var rel_id = $(invoker).attr('rel_id');
    view_hr_profile_q_a(id, rel_id);
  }

   //function view hr_profile_file
   function view_hr_profile_q_a(id, rel_id) {  
     'use strict'; 
     $('#contract_file_data').empty();
     $("#contract_file_data").load(admin_url + 'hr_profile/preview_q_a_file/' + id + '/' + rel_id, function(response, status, xhr) {
      if (status == "error") {
        alert_float('danger', xhr.statusText);
      }
    });
   }

   function delete_q_a_attachment(wrapper, id) {
    'use strict';

    if (confirm_delete()) {
      $.get(admin_url + 'hr_profile/delete_hr_profile_q_a_attachment_file/' + id, function (response) {
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

</script>