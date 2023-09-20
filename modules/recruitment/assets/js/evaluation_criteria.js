function new_evaluation_criteria(){
    "use strict";
    $('#evaluation_criteria').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#additional_criteria').html('');
}
function edit_evaluation_criteria(invoker,id){
    "use strict";
    $('#additional_criteria').html('');
    $('#additional_criteria').append(hidden_input('id',id));

    $('#evaluation_criteria input[name="criteria_title"]').val($(invoker).data('criteria_title'));
    $('#evaluation_criteria textarea[name="description"]').val($(invoker).data('description'));
    $('#evaluation_criteria input[name="score_des1"]').val($(invoker).data('score_des1'));
    $('#evaluation_criteria input[name="score_des2"]').val($(invoker).data('score_des2'));
    $('#evaluation_criteria input[name="score_des3"]').val($(invoker).data('score_des3'));
    $('#evaluation_criteria input[name="score_des4"]').val($(invoker).data('score_des4'));
    $('#evaluation_criteria input[name="score_des5"]').val($(invoker).data('score_des5'));
    $('#evaluation_criteria select[name="criteria_type"]').val($(invoker).data('criteria_type'));
    $('#evaluation_criteria select[name="criteria_type"]').change();
    $('#evaluation_criteria select[name="group_criteria"]').val($(invoker).data('group_criteria'));
    $('#evaluation_criteria select[name="group_criteria"]').change();


    $('#evaluation_criteria').modal('show');
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
}

function criteria_type_change(invoker){
    "use strict";
    if(invoker.value == 'criteria'){
        $('select[name="group_criteria"]').attr('required','');
        $('#select_group_criteria').removeClass('hide');
    }else{
        $('select[name="group_criteria"]').removeAttr('required');
        $('#select_group_criteria').addClass('hide');
    }
}
