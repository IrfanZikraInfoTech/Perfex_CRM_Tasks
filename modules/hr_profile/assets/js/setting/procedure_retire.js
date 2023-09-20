    
    function add_procedure_form_manage(id){ 
    'use strict';

        $('#add_procedure_retire_manage').modal('show');
        $('.modal-title.add').removeClass('hide');
        $('.modal-title.edit').addClass('hide');
        $('input[name="id"]').val('');
        $('input[name="name_procedure_retire"]').val('');
        $('select[name="departmentid[]"]').val(0).change();
    }

    function edit_procedure_form_manage(el){
    'use strict';
      
        $('#add_procedure_retire_manage').modal('show');
        $('.modal-title.edit').removeClass('hide');
        $('.modal-title.add').addClass('hide');
        var id = $(el).data('id');
        var name_procedure_retire = $(el).data('name_procedure_retire');
        var department = $(el).data('department');
        var list_id = [];
        if(typeof department.length != 'undefined'){
            var array_dept = department.split(', ');
            jQuery.each(array_dept, function(key, value){
              list_id.push(value);
          }); 
        }
        else{
         list_id.push(department);
     }
     $('input[name="id"]').val(id);
     $('input[name="name_procedure_retire"]').val(name_procedure_retire);
     $('select[name="departmentid[]"]').val(list_id).change();
 }

