function new_job_position(){
"use strict";
    $('#job_position').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');

    $('#job_position input[name="position_name"]').val('');
    $('#job_position select[name="industry_id"]').val('').change();
    $('#job_position textarea[name="position_description"]').val('').change();

    $('#additional').html('');
}
function edit_job_position(invoker,id){
    "use strict";
    $('#additional').append(hidden_input('id',id));
    $('#job_position input[name="position_name"]').val($(invoker).data('name'));

    if($(invoker).data('industry_id') != 0){
        $('#job_position select[name="industry_id"]').val($(invoker).data('industry_id')).change();

    }else{

        $('#job_position select[name="industry_id"]').val('').change();
    }


    var job_skill_str = $(invoker).data('job_skill');
    if(typeof(job_skill_str) == "string"){
        $('#job_position select[name="job_skill[]"]').val( ($(invoker).data('job_skill')).split(',')).change();
    }else{
       $('#job_position select[name="job_skill[]"]').val($(invoker).data('job_skill')).change();

    }


    $('#job_position textarea[name="position_description"]').val($(invoker).data('position_description'));
    $('#job_position').modal('show');
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
}
