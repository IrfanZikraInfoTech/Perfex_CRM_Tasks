
<script>
	$(function(){
		'use strict';
		var StaffServerParams = {
			"status_work": "[name='status_work[]']",
			"hr_profile_deparment": "[name='hr_profile_deparment']",
			"staff_role": "[name='staff_role[]']",
			"staff_teammanage": "input[name='staff_dep_tree']",
		};
		var table_staff = $('table.table-table_staff');
		initDataTable(table_staff,admin_url + 'hr_profile/table', [0],[0], StaffServerParams, [1, 'desc']);

		//hide first column
		 var hidden_columns = [];
		 $('.table-table_staff').DataTable().columns(hidden_columns).visible(false, false);

		$.each(StaffServerParams, function() {
			$('#hr_profile_deparment').on('change', function() {
				table_staff.DataTable().ajax.reload()
				.columns.adjust()
				.responsive.recalc();
			});
		});
				//staff role
				$.each(StaffServerParams, function() {
					$('#staff_role').on('change', function() {
						table_staff.DataTable().ajax.reload()
						.columns.adjust()
						.responsive.recalc();
					});
				});
				//combotree filter by team manage
				$('#staff_dep_tree').on('change', function() {
					$('#staff_tree').val(tree_dep.getSelectedItemsId());
					table_staff.DataTable().ajax.reload()
					.columns.adjust()
					.responsive.recalc();
				});
				$.each(StaffServerParams, function() {
					$('#status_work').on('change', function() {
						table_staff.DataTable().ajax.reload()
						.columns.adjust()
						.responsive.recalc();
					});
				});

			//combotree
			var tree_dep_derpartment = $('#hrm_derpartment_tree').comboTree({
				source : <?php echo html_entity_decode($dep_tree) ?>
			});

			//staff combotree
			var tree_dep = $('#staff_dep_tree').comboTree({
				source : <?php echo html_entity_decode($staff_dep_tree);?>
			});

		})
//staff role end  
function delete_staff_member(id){
	'use strict';
	$('#delete_staff').modal('show');
	$('#transfer_data_to').find('option').prop('disabled',false);
	$('#transfer_data_to').find('option[value="'+id+'"]').prop('disabled',true);
	$('#delete_staff .delete_id input').val(id);
	$('#transfer_data_to').selectpicker('refresh');
}

var nodeTemplate = function(data) { 
	'use strict';

	if(data.name){
		return `
		<div class="staff-chart-background-color">
		${data.image}${data.name}
		</div>
		<div class="content chart_company_name"><i class=${data.dp_user_icon} class="staff-chart-margin"></i>  ${data.job_position_name}</div>
		<div class="content"><i class=${data.dp_icon} class="staff-chart-margin"></i>  ${data.departmentname}</div>
		`;
	}else{
		return `
		<div class="staff-chart-background-color">
		${data.image}${data.name}
		</div>
		<div class="content chart_company_name"><i class=${data.dp_user_icon} class="staff-chart-margin"></i>${data.title}</div>
		<div class="content"><i class=${data.dp_icon} class="staff-chart-margin"></i>   ${data.departmentname}</div>
		`;
	}
};

//load staff chart
window.onload = function () {
	'use strict';

	var img_dir = site_url + 'uploads/company/favicon.png';
	var ds = {
		'image':'<img class="img_logo" src=" '+img_dir+' ">' ,
		'name': '',
		'title': '<p class="title_company"><?php echo get_option('invoice_company_name'); ?></p>',
		'departmentname': '',
		'children': <?php echo html_entity_decode($staff_members_chart); ?>
	};
	var oc = $('#staff_chart').orgchart({
		'data' :ds ,
		'nodeTemplate': nodeTemplate,
		'pan': true,
		'zoom': true,
		nodeContent: "title",
		verticalLevel: 4,
		visibleLevel: 4,
		'toggleSiblingsResp': true,
		'createNode': function(node, data) {
			node.on('click', function(event) {
				if (!$(event.target).is('.edge, .toggleBtn')) {
					var this_obj = $(this);
					var chart_obj = this_obj.closest('.orgchart');
					var newX = window.parseInt((chart_obj.outerWidth(true)/2) - (this_obj.offset().left - chart_obj.offset().left) - (this_obj.outerWidth(true)/2));
					var newY = window.parseInt((chart_obj.outerHeight(true)/2) - (this_obj.offset().top - chart_obj.offset().top) - (this_obj.outerHeight(true)/2));
					chart_obj.css('transform', 'matrix(1, 0, 0, 1, ' + newX + ', ' + newY + ')');
				}
			});
		}
	});
};

function staff_bulk_actions(){
	'use strict';
	$('#table_staff_bulk_actions').modal('show');
}

function staff_delete_bulk_action(event) {
	'use strict';
	if (confirm_delete()) {
		var mass_delete = $('#mass_delete').prop('checked');

		if(mass_delete == true){
			var ids = [];
			var data = {};
			data.mass_delete = true;
   			data.rel_type = 'hrm_staff';

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
   					$('#table_contract_bulk_actions').modal('hide');
   					alert_float('danger', data.responseText);
   				});
   			}, 200);

		}else{
			window.location.reload();
		}
	}
}

function hr_profile_add_staff(staff_id, role_id, add_new) {
	"use strict";

	$("#modal_wrapper").load("<?php echo admin_url('hr_profile/hr_profile/member_modal'); ?>", {
		slug: 'create',
		staff_id: staff_id,
		role_id: role_id,
		add_new: add_new
	}, function() {
		if ($('.modal-backdrop.fade').hasClass('in')) {
			$('.modal-backdrop.fade').remove();
		}
		if ($('#appointmentModal').is(':hidden')) {
			$('#appointmentModal').modal({
				show: true
			});
		}
	});

	init_selectpicker();
	$(".selectpicker").selectpicker('refresh');
}


function hr_profile_update_staff_manage_view(staff_id) {
	"use strict";

	$("#modal_wrapper").load("<?php echo admin_url('hr_profile/hr_profile/member_modal'); ?>", {
		slug: 'update',
		staff_id: staff_id,
		manage_staff: 'manage_staff'
	}, function() {
		if ($('.modal-backdrop.fade').hasClass('in')) {
			$('.modal-backdrop.fade').remove();
		}
		if ($('#appointmentModal').is(':hidden')) {
			$('#appointmentModal').modal({
				show: true
			});
		}
	});

	init_selectpicker();
	$(".selectpicker").selectpicker('refresh');
}

function view_staff_chart(){
	'use strict';
	$('#staff_chart_view').modal('show');
}


function staff_export_item(){
	"use strict";
	var ids = [];
	var data = {};

	data.mass_delete = true;
	data.rel_type = 'staff_list';

	var rows = $('#table-table_staff').find('tbody tr');
	$.each(rows, function() {
		var checkbox = $($(this).find('td').eq(0)).find('input');
		if (checkbox.prop('checked') === true) {
			ids.push(checkbox.val());
		}
	});
	data.ids = ids;

	$(event).addClass('disabled');

	if(data.ids.length > 0){
	setTimeout(function() {
		$.post(admin_url + 'hr_profile/create_staff_sample_file', data).done(function(response) {
			response = JSON.parse(response);
			if(response.success == true){
				alert_float('success', "<?php echo _l("create_export_file_success") ?>");

				$('#dowload_items').removeClass('hide');
				$('.hr_export_staff').addClass('hide');

				$('#dowload_items').attr({target: '_blank', 
					href  : site_url +response.filename});

			}else{
				alert_float('success', "<?php echo _l("create_export_file_fails") ?>");

			}

		}).fail(function(data) {


		});
	}, 200);
	}else{
		alert_float('warning', "<?php echo _l("please_select_the_employee_you_want_to_export_to_excel") ?>");

	}

}


</script>