function new_skill(){
    "use strict";
    $('#skill').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#skill input[name="skill_name"]').val('');
    $('#additional').html('');
}
function edit_skill(invoker,id){
    "use strict";
    $('#additional').append(hidden_input('id',id));
    $('#skill input[name="skill_name"]').val($(invoker).data('name'));

    $('#skill').modal('show');
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
}
