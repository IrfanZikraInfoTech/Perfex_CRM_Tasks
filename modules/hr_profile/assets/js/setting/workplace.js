    
    function new_workplace(){
        'use strict';
        $('#additional_workplace').html('');

        $('#workplace input[name="name"]').val('');
        $('#workplace textarea[name="workplace_address"]').val('');
        $('#workplace input[name="latitude"]').val('');
        $('#workplace input[name="longitude"]').val('');

        $('#workplace').modal('show');
        $('.edit-title').addClass('hide');
        $('.add-title').removeClass('hide');
    }


    function edit_workplace(invoker,id){
        'use strict';

        $('#additional_workplace').html('');
        $('#additional_workplace').append(hidden_input('id',id));

        $('#workplace input[name="name"]').val($(invoker).data('name'));
        $('#workplace textarea[name="workplace_address"]').val($(invoker).data('workplace_address'));
        $('#workplace input[name="latitude"]').val($(invoker).data('latitude'));
        $('#workplace input[name="longitude"]').val($(invoker).data('longitude'));

        $('#workplace').modal('show');
        $('.add-title').addClass('hide');
        $('.edit-title').removeClass('hide');
    }