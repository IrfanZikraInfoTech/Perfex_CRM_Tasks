
appValidateForm($('#add_type_of_training'), {
    'name': 'required',
    
});

function new_type_of_training(){
    'use strict';

    $('#type_of_training').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#additional_type_of_training').html('');

    $('#type_of_training input[name="name"]').val('');

    
}
function edit_type_of_training(invoker,id){
    'use strict';
    $('#additional_type_of_training').html('');
    
    $('#additional_type_of_training').append(hidden_input('id',id));
    $('#type_of_training input[name="name"]').val($(invoker).data('name'));
    $('#type_of_training').modal('show');
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');

    
}
