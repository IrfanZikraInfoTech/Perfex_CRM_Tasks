<script>


	var TrainingProgramServerParams = { 
			"hr_staff"    : "select[name='staff[]']",
			"training_library"    : "select[name='training_library[]']",
			"training_program"    : "select[name='training_program[]']",
	};

	var table_training_result = $('.table-table_training_result');
	initDataTable(table_training_result, admin_url+'hr_profile/table_training_result', [0], [0], TrainingProgramServerParams, [0, 'desc']);

		 //hide first column
		 var hidden_columns = [0,1];
		 $('.table-table_training_result').DataTable().columns(hidden_columns).visible(false, false);

		 $('#staff').on('change', function() {
		 	table_training_result.DataTable().ajax.reload().columns.adjust().responsive.recalc();
		 });

		 $('#training_library').on('change', function() {
		 	table_training_result.DataTable().ajax.reload().columns.adjust().responsive.recalc();
		 });
		 $('#training_program').on('change', function() {
		 	table_training_result.DataTable().ajax.reload().columns.adjust().responsive.recalc();
		 });
		 


	function training_program_bulk_actions(){
		'use strict';

	 	$('#table_training_result_bulk_actions').modal('show');
	}

	 // Leads bulk action
	 function training_program_delete_bulk_action(event) {
	 	'use strict';

	 	if (confirm_delete()) {
	 		var mass_delete = $('#mass_delete').prop('checked');

	 		if(mass_delete == true){
	 			var ids = [];
	 			var data = {};

	 			data.mass_delete = true;
	 			data.rel_type = 'hrm_training_program';

	 			var rows = $('#table-table_training_result').find('tbody tr');
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
	 					$('#training_program_bulk_actions').modal('hide');
	 					alert_float('danger', data.responseText);
	 				});
	 			}, 200);
	 		}else{
	 			window.location.reload();
	 		}

	 	}
	 }


	</script>
