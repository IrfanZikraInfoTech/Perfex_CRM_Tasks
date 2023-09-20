<script>
(function(){


	'use strict';
	var addMoreVendorsInputKey = $('.marks-emp-wrap').children().length + 1;
	$("body").on('click', '.new_marks_emp', function() {
		'use strict';
		if ($(this).hasClass('disabled')) { return false; }
		var new_marks_emp = $(this).parents('.marks-emp-wrap').find('#marks_emp').eq(0).clone().appendTo($(this).parents('.marks-emp-wrap'));

		new_marks_emp.find('button[role="button"]').remove();        

		new_marks_emp.find('select[id="info_name[1]"]').attr('name', 'name[' + addMoreVendorsInputKey + ']').val('');
		new_marks_emp.find('select[id="info_name[1]"]').attr('id', 'name[' + addMoreVendorsInputKey + ']').val('');

		new_marks_emp.find('div[name="button_add"]').removeAttr("style");

		new_marks_emp.find('button[name="add_marks"] i').removeClass('fa-plus').addClass('fa-minus');
		new_marks_emp.find('button[name="add_marks"]').removeClass('new_marks_emp').addClass('remove_marks_emp').removeClass('btn-primary').addClass('btn-danger');

		new_marks_emp.find('select').selectpicker('val', '');
		addMoreVendorsInputKey++;            
	});


	$("body").on('click', '.remove_marks_emp', function() {
		'use strict';
		$(this).parents('#marks_emp').remove();
	}); 


	var addMoreVendorsInputKey2 = $('.assets_wrap').children().length + 1;
	$("body").on('click', '.new_assets_emp', function() {
		'use strict';
		console.log('addMoreVendorsInputKey2', addMoreVendorsInputKey2);
		if ($(this).hasClass('disabled')) { return false; }
		var new_assets_emp = $(this).parents('.assets_wrap').find('#assets_emp').eq(0).clone().appendTo($(this).parents('.assets_wrap'));

		new_assets_emp.find('input[name="asset_name[]"]').val('');       

		new_assets_emp.find('div[name="button_add"]').removeAttr("style");

		new_assets_emp.find('button[name="add_asset"] i').removeClass('fa-plus').addClass('fa-minus');
		new_assets_emp.find('button[name="add_asset"]').removeClass('new_assets_emp').addClass('remove_assets_emp').removeClass('btn-primary').addClass('btn-danger');

		new_assets_emp.find('select').selectpicker('val', '');
		addMoreVendorsInputKey2++;            
	});


	$("body").on('click', '.remove_assets_emp', function() {
		'use strict';
		$(this).parents('#assets_emp').remove();
	}); 


	var table_staff = $('table.table-table_staff');
	$(function(){
		'use strict';
		var StaffServerParams = {
			"hrm_deparment": "input[name='hrm_deparment']",
			"staff_role": "[name='staff_role[]']",
		};


		initDataTable(table_staff,admin_url + 'hr_profile/table_reception_staff', [0], [0], StaffServerParams, [0, 'desc']);

		//hide first column
		var hidden_columns = [1];
				$('.table-table_staff').DataTable().columns(hidden_columns).visible(false, false);

				$('.table-table_staff').DataTable().on('draw', function() {
					var rows = $('.table-table_staff').find('tr');
					$.each(rows, function() {
						var td = $(this).find('td').eq(4);
						var percent = $(td).find('input[name="percent"]').val();

						$(td).find('.goal-progress').circleProgress({
							value: percent,
							size: 45,
							animation: false,
							fill: {
								gradient: ["#28b8da", "#059DC1"]
							}
						})
					})
				})
				
	})



})(jQuery); 


function delete_staff_member(id){
	'use strict';
	$('#delete_staff').modal('show');
	$('#transfer_data_to').find('option').prop('disabled',false);
	$('#transfer_data_to').find('option[value="'+id+'"]').prop('disabled',true);
	$('#delete_staff .delete_id input').val(id);
	$('#transfer_data_to').selectpicker('refresh');
}

function new_reception(){
	'use strict';
	$('#add_reception_staff').modal('show');
	$('.add-title').removeClass('hide');
	$('.edit-title').addClass('hide');

} 

$("select[name='staff_id']").on('change', function() {
	get_training_program_by_type();
});

$("select[name='training_type']").on('change', function() {
	var $staff_id = $('select[name="staff_id"]').val();
	if($staff_id != ''){
		get_training_program_by_type();
	}else{
		alert_float('warning', '<?php echo _l('please_select_staff_name_before_select_training_type') ?>');
	}
});



function get_training_program_by_type() {
	'use strict';

	var data={};
	data.staff_id = $('select[name="staff_id"]').val();
	data.training_type = $('select[name="training_type"]').val();
	
	$.post(admin_url+'hr_profile/get_training_program_by_type', data).done(function(response){
		response = JSON.parse(response);

		$('#training_program').html('');
		$('#training_program').append(response.training_program_html);

		init_selectpicker();
	  $(".selectpicker").selectpicker('refresh');
	});
}


var counttitle = $('#manage_reception').children('.title').length-1;
function add_title(el){
	'use strict';
	counttitle++;
	var row_title = $('#manage_reception .title').eq(0).clone().appendTo('#manage_reception');
	row_title.find('.btn-title').attr('onclick', 'remove_title(this); return false;').addClass('btn-danger').removeClass('btn-primary');
	row_title.find('.btn-title i').attr('class','fa fa-minus');
	row_title.find('input[name="title_name[0]"]').attr('name','title_name['+counttitle+']');
	row_title.find('input[name="sub_title_name[0][0]"]').attr('name','sub_title_name['+counttitle+'][0]');
	row_title.find('.sub .row').not(':first').remove();
	row_title.find('input').val('');
}


function add_subtitle(el){
	'use strict';
	var step_increa = $(el).closest('.title').find('.sub .row').length;
	var row_title = $('#manage_reception .title .sub .row').eq(0).clone().appendTo($(el).parents('.sub'));
	row_title.find('.btn-sub-title').attr('onclick', 'remove_subtitle(this); return false;').addClass('btn-danger').removeClass('btn-primary');
	row_title.find('.btn-sub-title i').attr('class','fa fa-minus');
	row_title.find('input').attr('name','sub_title_name['+counttitle+']['+step_increa+']');
	row_title.find('input').val('');
}


function remove_title(el){
	'use strict';
	$(el).closest('.title').remove();
}


function remove_subtitle(el){
	'use strict';
	$(el).closest('.row').remove();
}


function delete_reception(id){
	'use strict';
	$.post(admin_url+'recruitment/delete_reception/'+id).done(function(response){
		response = JSON.parse(response);
		if(response.success == true) {
			alert_float('success',response.message);
			table_staff.DataTable().ajax.reload()
			.columns.adjust()
			.responsive.recalc();
		}
	});
}


function show_info_reception(id){
	'use strict';
	var requestURL = (typeof(url) != 'undefined' ? url : 'hr_profile/get_reception/') + (typeof(id) != 'undefined' ? id : '');
	requestGetJSON(requestURL).done(function(response) {
		$('#reception_sidebar').modal('show').find('.modal-content').html(response.data);  
	}).fail(function(data) {
		alert_float('danger', data.responseText);
	});
}

function staff_bulk_actions(){
	'use strict';

	$('#table_staff_bulk_actions').modal('show');
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
				data.rel_type = 'hrm_reception_staff';

				var rows = $('#table-table_staff').find('tbody tr');
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
						$('#table_staff_bulk_actions').modal('hide');
						alert_float('danger', data.responseText);
					});
				}, 200);
			}else{
				window.location.reload();
			}

		}
	}

</script>