var position_training_id = {};
(function(){
  'use strict';
  window.addEventListener('load',function(){
    appValidateForm($("body").find('.job_position_training_add_edit'), {
      'training_type': 'required',
      'job_position_id[]': 'required',
      'mint_point': 'required',
    });

  });  
})(jQuery);

function new_training_process(){
  'use strict';

  $('#job_position_training').modal('show');
  $('.add-title-training').addClass('hide');
  $('.edit-title-training').removeClass('hide');

  $('#additional_form_training').empty();

  $('#job_position_training input[name="training_name"]').val('');
  $('#job_position_training input[name="mint_point"]').val('');

  $('#job_position_training select[name="training_type"]').val('');
  $('#job_position_training select[name="training_type"]').change();
  
  position_training_id = ('').split(',');
  tinyMCE.activeEditor.setContent("");

  $("select[name='job_position_id[]']").val('');
  $("select[name='job_position_id[]']").change();
  $('.selectpicker').selectpicker({
    });
}

function edit_training_process(invoker,id, rec_evaluation_form_id){
  'use strict';

  $('#job_position_training').modal('show');

  $('.edit-title-training').addClass('hide');
  $('.add-title-training').removeClass('hide');
  $('#additional_form_training').empty();
  $('#additional_form_training').append(hidden_input('id_training',id));
  
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

  var job_position_training_id_str = $(invoker).data('job_position_training_id');

  if(typeof(job_position_training_id_str) == "string"){
    position_training_id = ($(invoker).data('job_position_training_id')).split(',');
  }else{
    position_training_id = ($(invoker).data('job_position_training_id'));

  }

  var job_position_id_str = $(invoker).data('job_position_id');
  if(typeof(job_position_id_str) == "string"){
    $('#job_position_training select[name="job_position_id[]"]').val( ($(invoker).data('job_position_id')).split(',')).change();
  }else{
   $('#job_position_training select[name="job_position_id[]"]').val($(invoker).data('job_position_id')).change();

 }

 $('.add-title').addClass('hide');
 $('.edit-title').removeClass('hide');
}

function training_type_change(invoker){
  'use strict';

  if(invoker.value){
    $.post(admin_url + 'hr_profile/get_training_type_child/'+invoker.value).done(function(response) {
      response = JSON.parse(response);
      $('select[name="position_training_id[]"]').html('');
      $('select[name="position_training_id[]"]').append(response.html);

      $('select[name="position_training_id[]"]').selectpicker('refresh');

      $('#job_position_training select[name="position_training_id[]"]').val(position_training_id).change();
    }); 
  }

}

/*get jobposition in department by staff in department*/

function department_change(invoker){
  'use strict';

  var data_select = {};
  data_select.department_id = $('select[name="department_id[]"]').val();
  data_select.status = 'true';
  if((data_select.department_id).length == 0){
    data_select.status = 'false';
  }
  $.post(admin_url + 'hr_profile/get_jobposition_fill_data',data_select).done(function(response){
   response = JSON.parse(response);
   $("select[name='job_position_id[]']").html('');
   $("select[name='job_position_id[]']").append(response.job_position);
   $("select[name='job_position_id[]']").selectpicker('refresh');
 });
}