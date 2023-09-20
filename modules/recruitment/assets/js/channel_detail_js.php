<script>
$(function(){
  "use strict";
   var formBuilder = $(buildWrap).formBuilder(fbOptions);
   var $create_task_on_duplicate = $('#create_task_on_duplicate');

   $('#allow_duplicate').on('change',function(){
     $('.duplicate-settings-wrapper').toggleClass('hide');
   });

   $('#notify_lead_imported').on('change',function(){
        $('.select-notification-settings').toggleClass('hide');
   });

  $('#specific_staff').on('change',function(){
        $('#role_notify').addClass('hide');
        $('#staff_notify').removeClass('hide');
   });

  $('#roles').on('change',function(){
        $('#staff_notify').addClass('hide');
        $('#role_notify').removeClass('hide');
   });

  $('#assigned').on('change',function(){
        $('#staff_notify').addClass('hide');
        $('#role_notify').addClass('hide');
   });

   setTimeout(function(){
       $( ".form-builder-save" ).wrap( "<div class='btn-bottom-toolbar text-right'></div>" );
       var $btnToolbar = $('body').find('#tab_form_build .btn-bottom-toolbar');
       $btnToolbar = $('#tab_form_build').append($btnToolbar);
       $btnToolbar.find('.btn').addClass('btn-info');
   },100);

   /*get data form to web*/
   $('body').on('click','#sm_btn2',function() {
      $('input[name="form_data"]').val(formBuilder.formData);
   });
 });
var buildWrap = document.getElementById('form-build-wrap');
var formData = '<?php echo html_entity_decode($formData); ?>';

/*if('<?php echo html_entity_decode($formData); ?>' != ''){
  formData = '<?php echo html_entity_decode($formData); ?>';
}*/
if(formData.length){
  formData = formData.replace(/=\\/gm, "=''");
}
appValidateForm($('.recruitment-channel-add-edit'),{
  r_form_name:'required',
  language:'required',
  submit_btn_name:'required',
  success_submit_msg:'required',
  lead_status:'required',
  responsible:'required',
  form_type:'required',
  rec_campaign_id:'required',

});
</script>