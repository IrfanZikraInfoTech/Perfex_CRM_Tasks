<script>

	$(function(){
		'use strict';

		appValidateForm($('.save_update_education'), {
			training_programs_name: 'required',
			training_places: 'required',
			training_time_from: 'required',
			training_time_to: 'required',
		});

		var ContractsServerParams = {
			"hr_profile_staff"    : "[name='memberid']",
			"hr_profile_from_year"    : "select[name='from_year']",
			"hr_profile_from_month"    : "",
		};
		var table_education2 = $('table.table-table_education_position');
		initDataTable(table_education2,admin_url + 'hr_profile/table_education_position', [0], [0], ContractsServerParams);
		var table_education = $('table.table-table_education');
		var staff_id = $('input[name="memberid"]').val();
		initDataTable(table_education,admin_url + 'hr_profile/table_education/'+staff_id, [0], [0], '');


		$('form.save_update_education').on('submit', function (e) {
		'use strict';

			e.preventDefault();
			var data=$('form.save_update_education').serialize();
			var training_programs_name = $('input[name="training_programs_name"]').val();
			var training_places = $('input[name="training_places"]').val();
			var training_time_from = $('input[name="training_time_from"]').val();
			var training_time_to = $('input[name="training_time_to"]').val();

			if(training_programs_name != '' && training_places != '' && training_time_from != '' && training_time_to != ''){
				$('#education_sidebar').modal('hide');
				$.post(admin_url+'hr_profile/save_update_education',data).done(function(response){
					response = JSON.parse(response);
					if(response.success == true) {
						alert_float('success',response.message);
						table_education.DataTable().ajax.reload()
						.columns.adjust()
						.responsive.recalc();
					}
					else{
						alert_float('warning',response.message);
						table_education.DataTable().ajax.reload()
						.columns.adjust()
						.responsive.recalc();
					}
				});
			}
		});

		$('#training_time_from').datetimepicker();
		$('#training_time_to').datetimepicker();

		$(".save_update_education").submit(function(event){
			'use strict';
			tinymce.triggerSave();
		});

	});


	function create_trainings(){
		'use strict';
		$('#education_sidebar').modal('show');
		$('input[name="id"]').val('');
		$('input[name="training_programs_name"]').val('');
		$('input[name="training_places"]').val('');
		$('input[name="training_time_from"]').val('');
		$('input[name="training_time_to"]').val('');
		$('textarea[name="training_result"]').val('');
		$('input[name="degree"]').val('');
		$('textarea[name="notes"]').val('');
		$('.education_sidebar').addClass('sidebar-open');
		$('.edit-title-training').hide();
		$('.add-title-training').show();
	}


	function delete_education(el){
		'use strict';
		var id = $(el).data('id');
		var table_education = $('table.table-table_education');

		$.post(admin_url+'hr_profile/delete_education',{'id':id}).done(function(response){
			response = JSON.parse(response);
			if(response.success == true) {
				alert_float('success',response.message);
				table_education.DataTable().ajax.reload()
				.columns.adjust()
				.responsive.recalc();
			}
			else{
				alert_float('danger',response.message);
				table_education.DataTable().ajax.reload()
				.columns.adjust()
				.responsive.recalc();
			}
		});
	}

	function update_education(el){
		'use strict';
		$('#education_sidebar').modal('show');
		var id = $(el).data('id');
		$('input[name="id"]').val(id);
		$('input[name="training_programs_name"]').val($(el).data('name_programe'));
		$('input[name="training_places"]').val($(el).data('training_pl'));
		$('input[name="training_time_from"]').val($(el).data('time_from'));
		$('input[name="training_time_to"]').val($(el).data('time_to'));
		$('input[name="degree"]').val($(el).data('degree'));
		$('textarea[name="notes"]').val($(el).data('notes'));
		$('.education_sidebar').addClass('sidebar-open');
		$('.edit-title-training').show();
		$('.add-title-training').hide();
		tinyMCE.activeEditor.setContent($(el).data('result'));
	}

$('.trainingtable').dataTable( {
	 'destroy': true,
		"ordering": false
	} );
</script>