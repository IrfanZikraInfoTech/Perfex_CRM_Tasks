<script>
	

	$(function(){
		'use strict';
		
		var tree_dep = $('#hrm_derpartment_tree').comboTree({
			source : <?php echo html_entity_decode($dep_tree) ?>
		});

		var ContractsServerParams = {
			"hrm_deparment": "input[name='hrm_deparment']",
			"hrm_staff"    : "select[name='staff[]']",
			"validity_start_date": "input[name='validity_start_date']",
			"validity_end_date": "input[name='validity_end_date']",
		};
		$.each($('._hidden_inputs._filters input'),function(){
			ContractsServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
		});

		var table_contract = $('.table-table_contract');
		initDataTable(table_contract, admin_url+'hr_profile/table_contract', [0], [0], ContractsServerParams, [1, 'desc']);

		 //hide first column
		 var hidden_columns = [1];
		 $('.table-table_contract').DataTable().columns(hidden_columns).visible(false, false);

		//combotree department
		$('#hrm_derpartment_tree').on('change', function() {
			$('#hrm_deparment').val(tree_dep.getSelectedItemsId());
			table_contract.DataTable().ajax.reload()
			.columns.adjust()
			.responsive.recalc();
		});
		$('#staff').on('change', function() {
			table_contract.DataTable().ajax.reload().columns.adjust().responsive.recalc();
		});
		$('#validity_start_date').on('change', function() {
			table_contract.DataTable().ajax.reload().columns.adjust().responsive.recalc();
		});
		$('#validity_end_date').on('change', function() {
			table_contract.DataTable().ajax.reload().columns.adjust().responsive.recalc();
		});


		init_hrm_contract();

		<?php if(isset($to_expire)){ ?>
			dt_custom_view('hr_contract_is_about_to_expire','.table-table_contract','hr_contract_is_about_to_expire');
		<?php } ?>
		
		<?php if(isset($overdue_contract)){ ?>
			dt_custom_view('hr_overdue_contract','.table-table_contract','hr_overdue_contract');
		<?php } ?>
		
	});

	//init table contract view
	function init_hrm_contract(id) {
		'use strict';

		load_small_table_item(id, '#hrm_contract', 'hrmcontractid', 'hr_profile/get_hrm_contract_data_ajax', '.table-table_contract');
	}

	function load_small_table_item(pr_id, selector, input_name, url, table) {
		'use strict';

		var _tmpID = $('input[name="' + input_name + '"]').val();
		// Check if id passed from url, hash is prioritized becuase is last
		if (_tmpID !== '' && !window.location.hash) {
			pr_id = _tmpID;
			// Clear the current id value in case user click on the left sidebar credit_note_ids
			$('input[name="' + input_name + '"]').val('');
		} else {
			// check first if hash exists and not id is passed, becuase id is prioritized
			if (window.location.hash && !pr_id) {
				pr_id = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
			}
		}
		if (typeof(pr_id) == 'undefined' || pr_id === '') { return; }
		if (!$("body").hasClass('small-table')) { toggle_contract_view(table, selector); }
		$('input[name="' + input_name + '"]').val(pr_id);
		do_hash_helper(pr_id);
		$(selector).load(admin_url + url + '/' + pr_id);
		if (is_mobile()) {
			$('html, body').animate({
				scrollTop: $(selector).offset().top + 150
			}, 600);
		}
	}

	function toggle_contract_view(table, main_data) {
		'use strict';
		var hidden_columns = [4,5,6,8];

		$("body").toggleClass('small-table');
		var tablewrap = $('#small-table');
		if (tablewrap.length === 0) { return; }
		var _visible = false;
		if (tablewrap.hasClass('col-md-5')) {
			tablewrap.removeClass('col-md-5').addClass('col-md-12');
			_visible = true;
			$('.toggle-small-view').find('i').removeClass('fa fa-angle-double-right').addClass('fa fa-angle-double-left');
		} else {
			tablewrap.addClass('col-md-5').removeClass('col-md-12');
			$('.toggle-small-view').find('i').removeClass('fa fa-angle-double-left').addClass('fa fa-angle-double-right');
		}
		var _table = $(table).DataTable();
		// Show hide hidden columns
		_table.columns(hidden_columns).visible(_visible, false);
		_table.columns.adjust();
		$(main_data).toggleClass('hide');
		$(window).trigger('resize');
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
   			data.rel_type = 'hrm_contract';

   			var rows = $('#table-table_contract').find('tbody tr');
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
   					$('#table_contract_bulk_actions').modal('hide');
   					alert_float('danger', data.responseText);
   				});
   			}, 200);
   		}else{
   			window.location.reload();
   		}

   	}
   }

	 //contract preview file
	 function preview_file_staff(invoker){
	 	'use strict';

	 	var id = $(invoker).attr('id');
	 	var rel_id = $(invoker).attr('rel_id');
	 	view_hrmstaff_file(id, rel_id);
	 }

	   //function view hrm_file
	   function view_hrmstaff_file(id, rel_id) {   
	   	'use strict';

	   	$('#contract_file_data').empty();
	   	$("#contract_file_data").load(admin_url + 'hr_profile/hrm_file_contract/' + id + '/' + rel_id, function(response, status, xhr) {
	   		if (status == "error") {
	   			alert_float('danger', xhr.statusText);
	   		}
	   	});
	   }


	</script>