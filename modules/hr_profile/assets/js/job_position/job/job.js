  (function(){
    'use strict';

    window.addEventListener('load',function(){
      appValidateForm($("body").find('.job_p'), {
        'job_name': 'required'
      });
    });

    var ContractsServerParams = {
      "department_id": "[name='department_id[]']",
      "job_position_id"    : "[name='job_position_id[]']"
    };

    var table_job = $('.table-table_job');
    initDataTable(table_job, admin_url+'hr_profile/table_job', [0], [0], ContractsServerParams, [0, 'desc']);

    //hide first column
    var hidden_columns = [];
        $('.table-table_job').DataTable().columns(hidden_columns).visible(false, false);

    $('#department_id').on('change', function() {
      table_job.DataTable().ajax.reload().columns.adjust().responsive.recalc();
    });
    $('#job_position_id').on('change', function() {
      table_job.DataTable().ajax.reload().columns.adjust().responsive.recalc();
    });

  })(jQuery);

  function new_job_p(){
    'use strict';

    $('#additional_job').empty();
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#job_p input[name="job_name"]').val('');
    $('#job_p').modal('show');
    tinyMCE.activeEditor.setContent('');
    
    $('#_create_job_position_default').removeClass('hide');
    $('input[name="create_job_position"]').prop('checked',true); 
  }

  function edit_job_p(invoker,id){
    'use strict';

    $('input[name="create_job_position"]').prop('checked',false); 


    $('#_create_job_position_default').addClass('hide');
    $('#additional_job').append(hidden_input('id',id));
    $('#job_p input[name="job_name"]').val($(invoker).data('name'));

    $.post(admin_url + 'hr_profile/get_job_p_edit/'+id).done(function(response) {
      response = JSON.parse(response);
      tinyMCE.activeEditor.setContent(response.description);
      $('.selectpicker').selectpicker({});
    });
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
    $('#job_p').modal('show');
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
    $.post(admin_url + 'hr_profile/get_position_by_department',data_select).done(function(response){
     response = JSON.parse(response);
     $("select[name='job_position_id[]']").html('');

     $("select[name='job_position_id[]']").append(response.job_position);
     $("select[name='job_position_id[]']").selectpicker('refresh');

   });

  }

  function staff_bulk_actions(){
    'use strict';

    $('#table_contract_bulk_actions').modal('show');
  }

   // Leads bulk action
   function staff_delete_bulk_action(event) {
    'use strict';

    if (confirm_delete()) {
      var mass_delete = $('#mass_delete').prop('checked');

      if(mass_delete == true){
        var ids = [];
        var data = {};

        data.mass_delete = true;
        data.rel_type = 'hrm_job';

        var rows = $('#table-table_job').find('tbody tr');
        $.each(rows, function() {
          var checkbox = $($(this).find('td').eq(0)).find('input');
          if (checkbox.prop('checked') === true) {
            ids.push(checkbox.val());
          }
        });

        data.ids = ids;
        $(event).addClass('disabled');
        setTimeout(function() {
          $.post(admin_url + 'hr_profile/hrm_delete_bulk_action_v2', data).done(function() {
            window.location.reload();
          }).fail(function(data) {
            $('#table_contract_bulk_actions').modal('hide');
            alert_float('danger', data.responseText);
          });
        }, 200);
      }else{
        window.location.reload();
      }

    }
   }
