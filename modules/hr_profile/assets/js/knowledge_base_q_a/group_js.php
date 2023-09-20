<script>
    window.addEventListener('load',function(){

    // Validating the knowledge group form
    appValidateForm($('#kb_group_form'), {
        name: 'required'
    }, manage_kb_groups);

    // On hidden modal reset the values
    $('#kb_group_modal').on("hidden.bs.modal", function(event) {
        $('#kb_group_slug').addClass('hide');
        $('#kb_group_slug input').rules('remove', 'required');
        $('#additional').html('');
        $('#kb_group_modal input').not('[type="hidden"]').val('');
        $('#kb_group_modal textarea').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
        $('#kb_group_modal input[name="group_order"]').val($('table tbody tr').length + 1);
    });
});
// Form handler function for knowledgebase group
function manage_kb_groups(form) {
    'use strict';

    var data = $(form).serialize();
    var url = form.action;
    var articleAddEdit = $('body').hasClass('kb-article');
    if(articleAddEdit) {
        data+='&article_add_edit=true';
    }
    $.post(url, data).done(function(response) {
        if(!articleAddEdit) {
           window.location.reload();
        } else {
            response = JSON.parse(response);
            if(response.success == true){
                if(typeof(response.id) != 'undefined') {
                    var group = $('#articlegroup');
                    group.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                    group.selectpicker('val',response.id);
                    group.selectpicker('refresh');
                }
            }
            $('#kb_group_modal').modal('hide');
        }
    });
    return false;
}

// New knowledgebase group, opens modal
function new_kb_group() {
    'use strict';

    $('#kb_group_modal').modal('show');
    $('.edit-title').addClass('hide');
}

// Edit KB group, 2 places groups view or articles view directly click on kanban
function edit_kb_group(invoker, id) {
    'use strict';
    
    $('#additional').append(hidden_input('id', id));
    $('#kb_group_slug').removeClass('hide');
    $('#kb_group_slug input').rules('add', {required:true});
    $('#kb_group_slug input').val($(invoker).data('slug'));
    $('#kb_group_modal input[name="name"]').val($(invoker).data('name'));
    $('#kb_group_modal textarea[name="description"]').val($(invoker).data('description'));
    $('#kb_group_modal .colorpicker-input').colorpicker('setValue', $(invoker).data('color'));
    $('#kb_group_modal input[name="group_order"]').val($(invoker).data('order'));
    $('input[name="disabled"]').prop('checked', ($(invoker).data('active') == 0 ? true : false));
    $('#kb_group_modal').modal('show');
    $('.add-title').addClass('hide');
}

</script>