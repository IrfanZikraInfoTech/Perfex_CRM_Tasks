  (function(){
  'use strict';
  window.addEventListener('load',function(){
    $(function(){

    	var TrainingProgramServerParams ={};
    	var training_table = $('.table-training_table');
    	initDataTable(training_table, admin_url+'hr_profile/training_libraries_table', [0], [0], TrainingProgramServerParams, [1, 'desc']);

     //hide first column
     var hidden_columns = [1];
     $('.table-training_table').DataTable().columns(hidden_columns).visible(false, false);


    });
  });  
})(jQuery);

	function training_library_bulk_actions(){
		'use strict';

		$('#table_training_table_bulk_actions').modal('show');
	}

   // Leads bulk action
   function training_library_delete_bulk_action(event) {
   	'use strict';

   	if (confirm_delete()) {
   		var mass_delete = $('#mass_delete').prop('checked');

   		if(mass_delete == true){
   			var ids = [];
   			var data = {};

   			data.mass_delete = true;
   			data.rel_type = 'hrm_training_library';

   			var rows = $('#table-training_table').find('tbody tr');
   			$.each(rows, function() {
   				var checkbox = $($(this).find('td').eq(0)).find('input');
   				if (checkbox.prop('checked') === true) {
   					ids.push(checkbox.val());
   				}
   			});

   			data.ids = ids;
   			$(event).addClass('disabled');
   			setTimeout(function() {
   				$.post(admin_url + 'hr_profile/hrm_delete_bulk_action', data).done(function() {
   					window.location.reload();
   				}).fail(function(data) {
   					$('#table_training_table_bulk_actions').modal('hide');
   					alert_float('danger', data.responseText);
   				});
   			}, 200);
   		}else{
   			window.location.reload();
   		}

   	}
   }