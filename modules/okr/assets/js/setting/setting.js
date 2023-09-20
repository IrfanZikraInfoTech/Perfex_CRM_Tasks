(function(){
  "use strict";
  var fnServerParams = {
  }
  initDataTable('.table-circulation', admin_url + 'okr/table_circulation', false, false, fnServerParams, [0, 'desc']);
  initDataTable('.table-question', admin_url + 'okr/table_question', false, false, fnServerParams, [0, 'desc']);
  initDataTable('.table-evaluation-criteria', admin_url + 'okr/table_evaluation_criteria', false, false, fnServerParams, [0, 'desc']);
  initDataTable('.table-unit', admin_url + 'okr/table_unit', false, false, fnServerParams, [0, 'desc']);
  initDataTable('.table-category', admin_url + 'okr/table_category', false, false, fnServerParams, [0, 'desc']);

  appValidateForm($('#form_setting_circulation'), {
           'name_circulation': 'required',
           'from_date': 'required',
           'to_date': 'required'
  });

  appValidateForm($('#form_setting_question'), {
           'question': 'required',
  });

  appValidateForm($('#form_evaluation_criteria'), {
           'group_criteria': 'required',
           'name': 'required',
           'scores': 'required',
  });
  appValidateForm($('#form_setting_category'), {
           'category': 'required',
  });
  appValidateForm($('#form_setting_unit'), {
           'unit': 'required',
  });
})(jQuery);


function add_setting_circulation(){
  	"use strict";
	$('.update-title').addClass('hide');
  	$('.add-title').removeClass('hide');
    $('input[name="id"]').val('');
 	$('input[name="name_circulation"]').val('');
 	$('input[name="from_date"]').val('');
 	$('input[name="to_date"]').val('');
  	$('#setting_circulation').modal();
}

function update_setting_circulation(el){
     "use strict";
     $('input[name="id"]').val($(el).data('id'));
     $('input[name="name_circulation"]').val($(el).data('name'));
     $('input[name="from_date"]').val($(el).data('fromdate'));
     $('input[name="to_date"]').val($(el).data('todate'));
     $('.update-title').removeClass('hide');
     $('.add-title').addClass('hide');
     $('#setting_circulation').modal();
}

function add_setting_question(){
  	"use strict";
	$('.update-title').addClass('hide');
  	$('.add-title').removeClass('hide');
    $('input[name="id"]').val('');
 	$('input[name="question"]').val('');
  	$('#setting_question').modal();
}

function update_setting_question(el){
     "use strict";
     $('input[name="id"]').val($(el).data('id'));
     $('textarea[name="question"]').val($(el).data('question'));
     $('.update-title').removeClass('hide');
     $('.add-title').addClass('hide');
     $('#setting_question').modal();
}

function add_setting_unit(){
    "use strict";
    $('.update-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('input[name="id"]').val('');
    $('input[name="unit"]').val('');
    $('#setting_unit').modal();
}

function update_setting_unit(el){
     "use strict";
     $('input[name="id"]').val($(el).data('id'));
     $('input[name="unit"]').val($(el).data('unit'));
     $('.update-title').removeClass('hide');
     $('.add-title').addClass('hide');
     $('#setting_unit').modal();
}

function add_setting_evaluation_criteria(){
  	"use strict";
	$('.update-title').addClass('hide');
  	$('.add-title').removeClass('hide');
    $('input[name="id"]').val('');
  	$('#evaluation_criteria').modal();
}

function update_setting_evaluation_criteria(el){
     "use strict";
     $('input[name="id"]').val($(el).data('id'));
     $('select[name="group_criteria"]').val($(el).data('criteria')).change();
     $('input[name="name"]').val($(el).data('name'));
     $('input[name="scores"]').val($(el).data('scores'));
     $('.update-title').removeClass('hide');
     $('.add-title').addClass('hide');
     $('#evaluation_criteria').modal();
}


function add_setting_category(){
    "use strict";
    $('.update-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('input[name="id"]').val('');
    $('input[name="category"]').val('');
    $('#setting_category').modal();
}

function update_setting_category(el){
     "use strict";
     $('input[name="id"]').val($(el).data('id'));
     $('input[name="category"]').val($(el).data('category'));
     $('.update-title').removeClass('hide');
     $('.add-title').addClass('hide');
     $('#setting_category').modal();
}