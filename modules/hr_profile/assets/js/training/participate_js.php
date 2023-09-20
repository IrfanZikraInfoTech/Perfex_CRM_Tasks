<script>
   $(function(){
      'use strict';
     $('#survey_form').appFormValidator();
     var survey_fields_required = $('#survey_form').find('[data-required="1"]');
     $.each(survey_fields_required, function() {
       $(this).rules("add", {
         required: true
       });
       var name = $(this).data('for');
       var label = $(this).parents('.form-group').find('[for="' + name + '"]');
       if (label.length > 0) {
         if (label.find('.req').length == 0) {
           label.prepend(' <small class="req text-danger">* </small>');
         }
       }
     });
   });
   <?php if($this->input->get('participated')) { ?>
     $('body').find('input, textarea, #submit').prop('disabled',true);
   <?php } ?>
</script>