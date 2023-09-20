<script>
	'use strict';
	staff_chart_by_age('staff_chart_by_age',<?php echo html_entity_decode($staff_chart_by_age); ?>, <?php echo json_encode(_l('hr_staff_chart_by_age')); ?>);
	staff_chart_by_age('staff_departments_chart',<?php echo html_entity_decode($staff_departments_chart); ?>, <?php echo json_encode(_l('hr_chart_by_department')); ?>);
	staff_chart_by_age('staff_chart_by_job_positions',<?php echo html_entity_decode($staff_chart_by_job_positions); ?>, <?php echo json_encode(_l('hr_chart_by_job_positions')); ?>);
	staff_chart_by_fluctuate_according_to_seniority('staff_chart_by_fluctuate_according_to_seniority', '', '');
	report_by_staffs('report_by_staffs', '', '');
	
	

	function staff_chart_by_age(id, value, title_c){
		Highcharts.setOptions({
			chart: {
				style: {
					fontFamily: 'inherit !important',
					fontWeight:'normal',
					fill: 'black'
				}
			},
			colors: [ '#119EFA','#ef370dc7','#15f34f','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B']
		});

		Highcharts.chart(id, {
			chart: {
				backgroundcolor: '#fcfcfc8a',
				type: 'column'
			},
			accessibility: {
				description: null
			},
			title: {
				text: title_c
			},
			credits: {
				enabled: false
			},
			tooltip: {
				pointFormat: '<span style="color:{series.color}">'+<?php echo json_encode(_l('invoice_table_quantity_heading')); ?>+'</span>: <b>{point.y}</b> <br/>',
				shared: true
			},
			legend: {
				enabled: false
			},
			xAxis: {
				type: 'category'
			},
			yAxis: {
				title: {
					text: ''
				}

			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					depth: 35,
					dataLabels: {
						enabled: true,
						format: '{point.name}'
					}        
				}
			},
			series: [{
				name: "",
				colorByPoint: true,
				data: value,

			}]
		});
	}

	function staff_chart_by_fluctuate_according_to_seniority(id, value, title_c){
    'use strict';

		var data = {};
		var sort_from='0';
		var months_report = ''; 

		requestGetJSON('hr_profile/get_chart_senior_staff/' + sort_from+'/'+months_report).done(function (response) {

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
			 Highcharts.chart(id, {
			 	chart: {
			 		zoomType: 'xy'        
			 	},
			 	title: {
			 		text: '<?php echo _l('hr_chart_seniority_fluctuations'); ?>'
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


function report_by_staffs(id, value, title_c){
    'use strict';
	
	requestGetJSON('hr_profile/report_by_staffs').done(function (response) {

       //get data for hightchart
       
       Highcharts.setOptions({
       	chart: {
       		style: {
       			fontFamily: 'inherit !important',
       			fill: 'black'
       		}
       	},
       	colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B']
       });
       Highcharts.chart(id, {
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


var table_staff = $('table.table-table_staff');
$(function(){

	var StaffServerParams = {
		"hr_profile_deparment": "input[name='hr_profile_deparment']",
		"staff_role": "[name='staff_role[]']",
	};
	initDataTable(table_staff,admin_url + 'hr_profile/table_reception', '','', '');

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

});



</script> 