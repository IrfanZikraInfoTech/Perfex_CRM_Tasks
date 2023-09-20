
<script>
 var form_id = '#<?php echo $form->form_key; ?>';
 $(function() {
   $(form_id).appFormValidator({

    onSubmit: function(form) {

     $("input[type=file]").each(function() {
          if($(this).val() === "") {
              $(this).prop('disabled', true);
          }
      });

     var formURL = $(form).attr("action");
     var formData = new FormData($(form)[0]);

     $.ajax({
       type: $(form).attr('method'),
       data: formData,
       mimeType: $(form).attr('enctype'),
       contentType: false,
       cache: false,
       processData: false,
       url: formURL
     }).always(function(){
      $('#form_submit').prop('disabled', false);
     }).done(function(response){
      response = JSON.parse(response);
                 // In case action hook is used to redirect
                 if (response.redirect_url) {
                     window.top.location.href = response.redirect_url;
                     return;
                 }
                 if (response.success == false) {
                     $('#recaptcha_response_field').html(response.message); // error message
                   } else if (response.success == true) {
                     $(form_id).remove();
                     $('#response').html('<div class="alert alert-success">'+response.message+'</div>');
                     $('html,body').animate({
                       scrollTop: $("#online_payment_form").offset().top
                     },'slow');
                   } else {
                     $('#response').html('Something went wrong...');
                   }
                   if (typeof(grecaptcha) != 'undefined') {
                     grecaptcha.reset();
                   }
                 }).fail(function(data){
                 if (typeof(grecaptcha) != 'undefined') {
                   grecaptcha.reset();
                 }
                 if(data.status == 422) {
                    $('#response').html('<div class="alert alert-danger">Some fields that are required are not filled properly.</div>');
                 } else {
                    $('#response').html(data.responseText);
                 }
               });
                 return false;
               }
             });
 });
</script>