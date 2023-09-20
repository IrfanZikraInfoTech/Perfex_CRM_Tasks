	(function(){
		'use strict';

		appValidateForm($('#dependent form'), {
			'dependent_name': 'required',
			'relationship': 'required',
			'dependent_bir': 'required',
		})

		var ContractsServerParams = {
			"memberid": "[name='memberid']",
			"member_view": "[name='member_view']",
		};

		var table_dependent_person = $('.table-table_dependent_person');
		initDataTable(table_dependent_person, admin_url+'hr_profile/table_dependent_person', [0], [0], ContractsServerParams, [0, 'desc']);

		//hide first column
		var hidden_columns = [0,1,3,9];
		$('.table-table_dependent_person').DataTable().columns(hidden_columns).visible(false, false);

	})(jQuery);

	function new_dependent_person(){
		'use strict';
		$('#dependent').modal('show');
		$('#dependent_person_id').html('');
		
		$('.edit-title').addClass('hide');
		$('.add-title').removeClass('hide');
	}

	function edit_dependent_person(invoker,id){
		'use strict';
		$('#dependent_person_id').append(hidden_input('id',id));
		$('#dependent input[name="dependent_name"]').val($(invoker).data('dependent_name'));
		$('#dependent input[name="relationship"]').val($(invoker).data('relationship'));
		$('#dependent input[name="reason"]').val($(invoker).data('reason'));
		$('#dependent input[name="dependent_iden"]').val($(invoker).data('dependent_iden'));
		$('#dependent input[name="dependent_bir"]').val($(invoker).data('dependent_bir')).change();
		$('#dependent').modal('show');
		$('.add-title').addClass('hide');
		$('.edit-title').removeClass('hide');
	}