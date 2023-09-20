<script>
	$(function() {
    'use strict';

		 appValidateForm($('#dependent_person'), {
		    dependent_name: 'required',
		    relationship: 'required',
		    staffid: 'required',
			dependent_bir: 'required',

		});

		init_selectpicker();
		$(".selectpicker").selectpicker('refresh');
		init_datepicker();
	});

</script>


