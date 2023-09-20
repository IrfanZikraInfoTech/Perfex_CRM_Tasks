
var Input_totall;
var addnewkpi;
(function($) {
    "use strict";    



    window.addEventListener('load',function(){
        addnewkpi = $('.new-kpi-al').children().length;

       $("body").on('click', '.new_kpi', function() {
        console.log(addnewkpi);
          //get position row
          var idrow = $(this).parents('.new-kpi-al').find('.get_id_row').attr("value");
             if ($(this).hasClass('disabled')) { return false; }

            var newkpi = $(this).parents('.new-kpi-al').find('#new_kpi').eq(0).clone().appendTo($(this).parents('.new-kpi-al'));

            newkpi.find('button[data-toggle="dropdown"]').remove();

            newkpi.find('select[id="evaluation_criteria[' + idrow + '][0]"]').attr('name', 'evaluation_criteria[' + idrow + '][' + addnewkpi + ']').val('');
            newkpi.find('select[id="evaluation_criteria[' + idrow + '][0]"]').attr('id', 'evaluation_criteria[' + idrow + '][' + addnewkpi + ']').val('');
            newkpi.find('input[id="percent[' + idrow + '][0]"]').attr('name', 'percent[' + idrow + '][' + addnewkpi + ']').val('');
            newkpi.find('input[id="percent[' + idrow + '][0]"]').attr('id', 'percent[' + idrow + '][' + addnewkpi + ']').val('');
            
            newkpi.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
            newkpi.find('button[name="add"]').removeClass('new_kpi').addClass('remove_kpi').removeClass('btn-success').addClass('btn-danger');

            newkpi.find('select').selectpicker('val', '');
            addnewkpi++;

        });

        $("body").on('click', '.remove_kpi', function() {
            $(this).parents('#new_kpi').remove();
        });

        Input_totall = $('.new-kpi-group-al').children().length;

       $("body").on('click', '.new_kpi_group', function() {
             if ($(this).hasClass('disabled')) { return false; }

            var addMore = 0;

            var newkpigroup = $('.new-kpi-group-al').find('#new_kpi_group').eq(0).clone().appendTo('.new-kpi-group-al');

            for(var i = 0; i <= newkpigroup.find('#new_kpi').length ; i++){
                if(i > 0){
                  newkpigroup.find('#new_kpi').eq(i).remove();
                }
                newkpigroup.find('#new_kpi').eq(1).remove();
            }

            newkpigroup.find('button[data-toggle="dropdown"]').remove();
            newkpigroup.find('select').selectpicker('refresh');

            newkpigroup.find('select[id="group_criteria[0]"]').attr('name', 'group_criteria[' + Input_totall + ']').val('');
            newkpigroup.find('select[id="group_criteria[0]"]').attr('id', 'group_criteria[' + Input_totall + ']').val('');
            newkpigroup.find('button[data-id="group_criteria[0]"]').attr('data-id', 'group_criteria[' + Input_totall + ']');

            // start expense
            newkpigroup.find('select[id="evaluation_criteria[0][0]"]').attr('name', 'evaluation_criteria[' + Input_totall + '][' + addMore + ']').val('');
            newkpigroup.find('select[id="evaluation_criteria[0][0]"]').attr('id', 'evaluation_criteria[' + Input_totall + '][' + addMore + ']').val('');
            newkpigroup.find('select[data-sl-id="e_criteria[0]"]').attr('data-sl-id', 'e_criteria[' + Input_totall + ']');
            newkpigroup.find('label[for="evaluation_criteria[0][0]"]').attr('for', 'evaluation_criteria[' + Input_totall + '][' + addMore + ']');

            newkpigroup.find('input[id="percent[0][0]"]').attr('name', 'percent[' + Input_totall + '][' + addMore + ']').val('');
            newkpigroup.find('input[id="percent[0][0]"]').attr('id', 'percent[' + Input_totall + '][' + addMore + ']').val('');
            newkpigroup.find('label[for="percent[0][0]"]').attr('for', 'percent[' + Input_totall + '][' + addMore + ']');

            newkpigroup.find('label[for="evaluation_criteria[' + Input_totall + '][' + addMore + ']"]').attr('value',  Input_totall);

            newkpigroup.find('div[name="button_add_kpi_group"]').removeAttr("style");
            newkpigroup.find('button[name="add_kpi_group"] i').removeClass('fa-plus').addClass('fa-minus');
            newkpigroup.find('button[name="add_kpi_group"]').removeClass('new_kpi_group').addClass('remove_kpi_group').removeClass('btn-success').addClass('btn-danger');

            newkpigroup.find('select').selectpicker('val', '');

            init_datepicker();
            Input_totall++;

        });

        $("body").on('click', '.remove_kpi_group', function() {
            $(this).parents('#new_kpi_group').remove();

        });
    });
})(jQuery);

function new_evaluation_form(){
    "use strict"; 
    $('#evaluation_form').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#additional_criteria').html('');
}
function edit_evaluation_form(invoker,id){
    "use strict"; 
    $('#additional_criteria').html('');
    $('#additional_criteria').append(hidden_input('id',id));

    $('#evaluation_form input[name="form_name"]').val($(invoker).data('form_name'));
    $('#evaluation_form select[name="job_position"]').val($(invoker).data('position'));
    $('#evaluation_form select[name="job_position"]').change();

    $.post(admin_url + 'recruitment/get_list_criteria_edit/'+id).done(function(response) {
        response = JSON.parse(response);
        $('#list_criteria').html('');
        $('#list_criteria').append(response.html);
        Input_totall = response.group_criteria;
        addnewkpi = response.evaluation_criteria;

        $('.selectpicker').selectpicker({
        });

    });

    $('#evaluation_form').modal('show');
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

function group_criteria_change(invoker){
    "use strict"; 
    var result = invoker.name.match(/\d/g);
    $.post(admin_url + 'recruitment/get_criteria_by_group/'+invoker.value).done(function(response) {
        response = JSON.parse(response);
        $('select[data-sl-id="e_criteria['+result+']').html('');
        $('select[data-sl-id="e_criteria['+result+']').append(response.html);

        $('select[data-sl-id="e_criteria['+result+']').selectpicker('refresh');
    });
}