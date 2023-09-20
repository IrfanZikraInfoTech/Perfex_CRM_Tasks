(function($) {
"use strict";
var _rel_id = $('#rel_id'),_rel_type = $('#rel_type'),_rel_id_wrapper = $('#rel_id_wrapper'),data = {};
$('.rel_id_label').html(_rel_type.find('option:selected').text());
_rel_type.on('change', function() {
	 var clonedSelect = _rel_id.html('').clone();
	 _rel_id.selectpicker('destroy').remove();
	 _rel_id = clonedSelect;
	 $('#rel_id_select').append(clonedSelect);
	 $('.rel_id_label').html(_rel_type.find('option:selected').text());
	 task_rel_select();
	 if($(this).val() != ''){
	  _rel_id_wrapper.removeClass('hide');
	} else {
	  _rel_id_wrapper.addClass('hide');
	}
});
task_rel_select();
function task_rel_select(){
	var serverData = {};
	serverData.rel_id = _rel_id.val();
	data.type = _rel_type.val();
	if(_rel_type.val()=='customer')
	{
		$('#group_id_wrapper').removeClass('hide');
	} else {
		$('#group_id_wrapper').addClass('hide');
	}
	init_ajax_search(_rel_type.val(),_rel_id,serverData);
}
$('#report_months').on('change', function() {
     var val = $(this).val();
	 var report_from = $('#report_from');
	 var report_to = $('#report_to');
	 var date_range = $('#date-range');
	 
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
	 if(val!='')
	 	$("#date_by_wrapper").removeClass('hide');
	 else
	 	$("#date_by_wrapper").addClass('hide');	
});
$('#si_save_filter').on('click',function(){
	var checked = this.checked;
	$('#si_filter_name').attr('disabled',!checked);
});
$('#si_form_task_filter').on('submit',function(){
	if($('#si_save_filter').is(":checked") && $('#si_filter_name').val()=='')
	{
		$('#si_filter_name').focus();
		return false;
	}
});
$(document).ready(function() {
	var table = $('.dt-table').DataTable();
	var hide_view = [];
	$('.dt-table thead tr th').each(function(i,a) { 
		if( $(this).hasClass('not-export'))
			hide_view.push($(this).index());	
	});
	table.button().add( 1, 'colvis' );
	table.columns( hide_view ).visible( false );
});
$(".buttons-colvis").text("Columns");
})(jQuery);	