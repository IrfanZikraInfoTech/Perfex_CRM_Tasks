    appValidateForm($('#add_contract_type'), {
        name_contracttype: 'required',
        contracttype: 'required',
    });

function new_contract_type(){
    'use strict';
    $('#contract_type').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#contract_type input[name="name_contracttype"]').val('');
    $('#additional_contract_type').html('');
    tinyMCE.activeEditor.setContent("");

}
function edit_contract_type(invoker,id){
    'use strict';

    $('#additional_contract_type').html('');
    $('#additional_contract_type').append(hidden_input('id',id));
    tinyMCE.activeEditor.setContent("");

    requestGetJSON('hr_profile/get_contract_type/' + id).done(function (response) {
        $('#contract_type input[name="name_contracttype"]').val(response.contract_type.name_contracttype);
        tinyMCE.activeEditor.setContent(response.contract_type.description);
    });


    $('#contract_type').modal('show');
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
} 
