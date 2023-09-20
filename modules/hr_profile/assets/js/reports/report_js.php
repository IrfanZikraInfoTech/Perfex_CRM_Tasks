<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
	init_datepicker();
	var salesChart;
	var groupsChart;
	var paymentMethodsChart;
	var customersTable;
	var report_from = $('input[name="report-from"]');
	var report_to = $('input[name="report-to"]');

	var report_leave_statistics = $('#leave-statistics');

	var date_range = $('#date-range');
	var report_from_choose = $('#report-time');
	var fnServerParams = {
		"report_months": '[name="months-report"]',
		report_from: "input[name='report-from']",
		report_to: "input[name='report-to']",
		"position_filter": "[name='position[]']",
		"department_filter": "[name='department[]']",
		"staff_filter": "[name='staff[]']",
		"months_filter": "[name='months-report']",
		"year_requisition": "[name='year_requisition']",
	}
	$(function() {
    'use strict';
		
		$('').on('change', function() {
			gen_reports();
		});

		report_from.on('change', function() {
			var val = $(this).val();
			var report_to_val = report_to.val();
			if (val != '') {
				report_to.attr('disabled', false);
				if (report_to_val != '') {
					gen_reports();
				}
			} else {
				report_to.attr('disabled', true);
			}
		});

		report_to.on('change', function() {
			var val = $(this).val();
			if (val != '') {
				gen_reports();
			}
		});

		$('select[name="months-report"]').on('change', function() {
			var val = $(this).val();
			report_to.attr('disabled', true);
			report_to.val('');
			report_from.val('');
			if (val == 'custom') {
				date_range.addClass('fadeIn').removeClass('hide');
				return;
			} else {
				if (!date_range.hasClass('hide')) {
					date_range.removeClass('fadeIn').addClass('hide');
				}
			}
			gen_reports();
		});

		$('select[name="year_requisition"]').on('change', function() {
			var val = $(this).val();
			gen_reports();
		});



	});

	function init_report(e, type) {
    'use strict';

		var report_wrapper = $('#report');

		if (report_wrapper.hasClass('hide')) {
			report_wrapper.removeClass('hide');
		}

		$('head title').html($(e).text());
		$('.leave-statistics-gen').addClass('hide');


		report_leave_statistics.addClass('hide');


		report_from_choose.addClass('hide');

		$('select[name="months-report"]').selectpicker('val', 'this_month');
	 // Cleaasr custom date picker
	 report_to.val('');
	 report_from.val('');
	 $('#report-time').removeClass('hide');
	 $('.title_table').text('');
	 $('.sorting_table').addClass('hide');
	 $('select[name="position[]"]').closest('.col-md-4').removeClass('hide');
	 $('select[name="staff[]"]').closest('.col-md-4').removeClass('hide');

	 if(type == 'seniority'){
		$('.leave-statistics-gen').addClass('hide');
		$('#container_senior_staff').removeClass('hide');
		$('.working-hours-gen').addClass('hide');
		$('#hr_is_working').addClass('hide');
		$('#leave-reports').addClass('hide');
		$('#report_the_employee_quitting').addClass('hide');
		$('#list_of_employees_with_salary_change').addClass('hide');
		$('#qualifications_department').addClass('hide');
		$('.sorting_table').addClass('hide');
		$('#additional_timesheets').addClass('hide');


		$('#report-time').removeClass('hide');
		$('#year_requisition').addClass('hide');

	 } 
	 else if(type == 'month'){
		$('#hr_is_working').removeClass('hide');
		$('#container_senior_staff').addClass('hide');
		$('.leave-statistics-gen').addClass('hide');
		$('.working-hours-gen').addClass('hide');
		$('#leave-reports').addClass('hide');
		$('#report_the_employee_quitting').addClass('hide');
		$('#list_of_employees_with_salary_change').addClass('hide');
		$('#qualifications_department').addClass('hide');
		$('.sorting_table').addClass('hide');
		$('#additional_timesheets').addClass('hide');


		$('#report-time').addClass('hide');
		$('#year_requisition').addClass('hide');

	 }else if(type == 'report_the_employee_quitting'){
		$('.sorting_table').removeClass('hide');
		$('#hr_is_working').addClass('hide');
		$('#container_senior_staff').addClass('hide');
		$('.leave-statistics-gen').addClass('hide');
		$('.working-hours-gen').addClass('hide');
		$('#leave-reports').addClass('hide');
		$('#report_the_employee_quitting').removeClass('hide');
		$('#list_of_employees_with_salary_change').addClass('hide');
		$('#qualifications_department').addClass('hide');
		$('#additional_timesheets').addClass('hide');


		$('#report-time').removeClass('hide');
		$('#year_requisition').addClass('hide');

	 } 
	 else if(type == 'list_of_employees_with_salary_change'){
		$('.sorting_table').removeClass('hide');
		$('#hr_is_working').addClass('hide');
		$('#container_senior_staff').addClass('hide');
		$('.leave-statistics-gen').addClass('hide');
		$('.working-hours-gen').addClass('hide');
		$('#leave-reports').addClass('hide');
		$('#report_the_employee_quitting').addClass('hide');
		$('#list_of_employees_with_salary_change').removeClass('hide');
		$('#qualifications_department').addClass('hide');
		$('#additional_timesheets').addClass('hide');


		$('#report-time').removeClass('hide');
		$('#year_requisition').addClass('hide');

	 } 
	 else if(type == 'qualifications_department'){
		$('#hr_is_working').addClass('hide');
		$('#container_senior_staff').addClass('hide');
		$('.leave-statistics-gen').addClass('hide');
		$('.working-hours-gen').addClass('hide');
		$('#leave-reports').addClass('hide');
		$('#report_the_employee_quitting').addClass('hide');
		$('#list_of_employees_with_salary_change').addClass('hide');
		$('#qualifications_department').removeClass('hide');
		$('.sorting_table').removeClass('hide');
		$('#additional_timesheets').addClass('hide');


		$('#report-time').addClass('hide');
		$('#year_requisition').addClass('hide');

	 }

	 gen_reports();
	}



	 // Main generate report function
	 function gen_reports() {
    'use strict';



		if (!$('#container_senior_staff').hasClass('hide')) {
			senior_statff_gen();
		} 

		if (!$('#hr_is_working').hasClass('hide')) {
			hr_is_working();
		} 
		
		if(!$('#report_the_employee_quitting').hasClass('hide')){
			report_the_employee_quitting();
		}
		if(!$('#list_of_employees_with_salary_change').hasClass('hide')){
			list_of_employees_with_salary_change();
		}  
		if(!$('#qualifications_department').hasClass('hide')){
			qualifications_department();
		}


	 }
	 function senior_statff_gen(){
    'use strict';

		var data = {};
		var sort_from='0';

		var months_report = $('select[name="months-report"]').val(); 
		var report_from = $('input[name="report-from"]').val();
		var report_to = $('input[name="report-to"]').val();

		requestGetJSON('hr_profile/get_chart_senior_staff/' + sort_from+'/'+months_report+'/'+report_from+'/'+report_to).done(function (response) {

			 //get data for hightchart
			 Highcharts.setOptions({
				chart: {
					style: {
						fontFamily: 'inherit !important',
									fill: 'black'
								}
							},
							colors: [ '#119EFA','#ef370dc7','#15f34f','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B']
						});
			 Highcharts.chart('container_senior_staff', {
				chart: {
					zoomType: 'xy'        
				},
				title: {
					text: '<?php echo _l('hr_report_seniority_fluctuations'); ?>'
				},
				subtitle: {
					text: ''
				},
				credits: {
					enabled: false
				},
				xAxis: [{
					categories: ['<= 1 <?php echo _l('months'); ?>', '<= 3 <?php echo _l('months'); ?>', '<= 6 <?php echo _l('months'); ?>', '<= 9 <?php echo _l('months'); ?>', '<= 12 <?php echo _l('months'); ?>', '> 12 <?php echo _l('months'); ?>'],
					crosshair: true
				}],
		yAxis: [{ // Primary yAxis
			labels: {
				format: '{value} %',
				style: {
					color: Highcharts.getOptions().colors[3]
				}
			},
			title: {
				text: '<?php echo _l('ratio'); ?>',
				style: {
					color: Highcharts.getOptions().colors[4]
				}
			}
		}, { // Secondary yAxis
			title: {
				text: '<?php echo _l('hr_number_of_employees'); ?>',
				style: {
					color: Highcharts.getOptions().colors[0]
				}
			},
			labels: {
				format: '{value} ',
				style: {
					color: Highcharts.getOptions().colors[2]
				}
			},
			opposite: true
		}],
		tooltip: {
			shared: true
		},
		legend: {
			layout: 'vertical',
			align: 'left',
			x: 60,
			verticalAlign: 'top',
			y: -4,
			floating: true,
			backgroundColor:
						Highcharts.defaultOptions.legend.backgroundColor || // theme
						'rgba(255,0,0,0.2)'
					},
					series: [{
						name: '<?php echo _l('hr_number_of_employees'); ?>',
						type: 'column',
						yAxis: 1,
						data:response.data,
						tooltip: {
							valueSuffix: ' <?php echo _l('people'); ?>'
						}

					}, {
						name: '<?php echo _l('ratio'); ?>',
						type: 'spline',
						data:response.data_ratio,
						tooltip: {
							valueSuffix: ' %'
						}
					}]
				});
			 

			});




	 }

	 function hr_is_working(){
    'use strict';

		var data = {};
		data.sort_from='0';
		data.months_filter = $('select[name="months-report"]').val(); 
		data.report_from = report_from.val();
		data.report_to = report_to.val();
		$.post(admin_url + 'hr_profile/HR_is_working', data).done(function(response) {
			response = JSON.parse(response);


			Highcharts.setOptions({
				chart: {
					style: {
						fontFamily: 'inherit !important',
						fill: 'black'
					}
				},
				colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B']
			});
			Highcharts.chart('hr_is_working', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<?php echo _l('employee_chart_by_month'); ?>'
				},
				credits: {
					enabled: false
				},
				xAxis: {
					categories: response.categories,
					crosshair: true
				},
				yAxis: {
					min: 0,
					title: {
						text: ''
					}
				},
				tooltip: {
					headerFormat: '<span class="font-size-10">{point.key}</span><table>',
					pointFormat: '<tr><td class="padding-0" style="color:{series.color}">{series.name}: </td>' +
					'<td class="padding-0"><b>{point.y:.1f}</b></td></tr>',
					footerFormat: '</table>',
					shared: true,
					useHTML: true
				},
				plotOptions: {
					column: {
						pointPadding: 0.2,
						borderWidth: 0
					}
				},
				series: [{
					name: '<?php echo _l('hr_new_staff'); ?>',
					data: response.hr_new_staff 

				}, {
					name: '<?php echo _l('hr_staff_are_working'); ?>',
					data: response.hr_staff_are_working

				}, {
					name: '<?php echo _l('hr_staff_quit'); ?>',
					data: response.hr_staff_quit

				}]
			});


		});

	 }
	 function qualifications_department(){
    'use strict';

		$('.sorting_table').removeClass('hide');
		$('select[name="position[]"]').closest('.col-md-4').addClass('hide');
		$('select[name="staff[]"]').closest('.col-md-4').addClass('hide');
		var data = {};
		data.months_filter = $('select[name="months-report"]').val(); 
		data.department_filter = $('select[name="department[]"]').val(); 
		data.report_from = report_from.val();
		data.report_to = report_to.val();
		$.post(admin_url + 'hr_profile/qualification_department', data).done(function(response) {
			response = JSON.parse(response);
			if(!response.circle_mode){
				var data_json = [];
				$.each(response.data_result,function(key, e){
					var data = 
					{
						name: e.stack,
						data: e.data
					};
					data_json.push(data);                                    
				});

				var options = {
					chart: {
						height: 1000,
						renderTo: 'qualifications_department',
						type: 'bar'
					},
					title: {
						text: '<?php echo _l("hr_personnel_qualifications_department")?>' 
					},
					credits: {
						enabled: false
					},
					xAxis: {
						categories: response.department,
						crosshair: true
					},
					yAxis: {
						min: 0,
						title: {
							text: '<?php echo _l('hr_hr_quantity') ?>'
						}
					},
					tooltip: {
						formatter: function() {
							var currentPoint = this,
							currentSeries = currentPoint.series,
							chart = currentSeries.chart,
							stackName = this.series.userOptions.stack,
							stackValues = '';

							return '<b><?php echo _l('department_name') ?>: ' + this.x + '</b><br/>' +
							'<b style   =   "color:'+this.series.color+';padding:0">'+this.series.name+': </b> '+this.y+' người<br>';
						}
					},
					plotOptions: {
						bar: {
							dataLabels: {
								enabled: true
							}
						}
					},
					series: data_json
				};
				var chart1 = new Highcharts.Chart(options);
			}
			else{
				Highcharts.chart('qualifications_department', {
					chart: {
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false,
						type: 'pie'
					},
					title: {
						text: '<?php echo _l("personnel_qualifications_department")?> '+response.department[0]
					},
					tooltip: {
						pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
					},
					accessibility: {
						point: {
							valueSuffix: '%'
						}
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: false
							},
							showInLegend: true
						}
					},
					series: [{
						name: '<?php echo _l('ratio'); ?>',
						colorByPoint: true,
						data: response.data_result
					}]
				});
			}

		});
	 }
//table bao cao
$(function() {
    'use strict';

	$('select[name="position[]"],select[name="department[]"],select[name="staff[]"]').on('change', function() {
		gen_reports();
	});
});

function report_the_employee_quitting(){
    'use strict';

	$('.title_table').text('<?php echo _l('hr_report_the_employee_quitting'); ?>');
	if ($.fn.DataTable.isDataTable('.table-report_the_employee_quitting')) {
		$('.table-report_the_employee_quitting').DataTable().destroy();
	} 
	initDataTable('.table-report_the_employee_quitting', admin_url + 'hr_profile/report_the_employee_quitting', [0], [0], fnServerParams, [0, 'desc']);

	//hide first column
    var hidden_columns = [0];
        $('.table-report_the_employee_quitting').DataTable().columns(hidden_columns).visible(false, false);
}

function list_of_employees_with_salary_change(){ 
    'use strict';

	$('.title_table').text('<?php echo _l('hr_list_of_employees_with_salary_change'); ?>');
	if ($.fn.DataTable.isDataTable('.table-list_of_employees_with_salary_change')) {
		$('.table-list_of_employees_with_salary_change').DataTable().destroy();
	} 
	initDataTable('.table-list_of_employees_with_salary_change', admin_url + 'hr_profile/list_of_employees_with_salary_change', [0], [0], fnServerParams, [0, 'desc']);
	//hide first column
    var hidden_columns = [0];
        $('.table-list_of_employees_with_salary_change').DataTable().columns(hidden_columns).visible(false, false);
}



</script>
