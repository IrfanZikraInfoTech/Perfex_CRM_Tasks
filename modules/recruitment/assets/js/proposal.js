(function($) {
"use strict";
var ProposalServerParams = {
        "dpm": "[name='department_filter[]']",
        "posiotion_ft": "[name='position_filter[]']",
        "status": "[name='status_filter[]']",
    };
var table_rec_proposal = $('.table-table_rec_proposal');
var _table_api = initDataTable(table_rec_proposal, admin_url+'recruitment/table_proposal', '', '', ProposalServerParams);

$.each(ProposalServerParams, function(i, obj) {
    $('select' + obj).on('change', function() {  
        table_rec_proposal.DataTable().ajax.reload()
            .columns.adjust()
            .responsive.recalc();
    });
});

_validate_form($('#recruitment-proposal-form'),{proposal_name:'required',to_date:'required',approver:'required',position:'required'}); 
init_recruitment_proposal();
$("input[data-type='currency']").on({
    keyup: function() {        
      formatCurrency($(this));
    },
    blur: function() { 
      formatCurrency($(this), "blur");
    }
});
})(jQuery);

function new_proposal(){
  "use strict";
  $('#recruitment_proposal').modal('show');
  $('.edit-title').addClass('hide');
  $('.add-title').removeClass('hide');
  $('#additional_proposal').html('');
  $('#recruitment_proposal input[name="proposal_name"]').val('');
  $('#recruitment_proposal input[name="amount_recruiment"]').val('');
  $('#recruitment_proposal input[name="workplace"]').val('');
  $('#recruitment_proposal input[name="from_date"]').val('');
  $('#recruitment_proposal input[name="to_date"]').val('');
  $('#recruitment_proposal input[name="salary_from"]').val('');
  $('#recruitment_proposal input[name="salary_to"]').val('');
  $('#recruitment_proposal input[name="ages_from"]').val('');
  $('#recruitment_proposal input[name="ages_to"]').val('');
  $('#recruitment_proposal input[name="height"]').val('');
  $('#recruitment_proposal input[name="weight"]').val('');
  $('#recruitment_proposal textarea[name="reason_recruitment"]').val('');
  $('#recruitment_proposal select[name="position"]').val('');
  $('#recruitment_proposal select[name="position"]').change();
  $('#recruitment_proposal select[name="department"]').val('');
  $('#recruitment_proposal select[name="department"]').change();
  $('#recruitment_proposal select[name="form_work"]').val('');
  $('#recruitment_proposal select[name="form_work"]').change();
  $('#recruitment_proposal select[name="approver"]').val('');
  $('#recruitment_proposal select[name="approver"]').change();
  $('#recruitment_proposal select[name="gender"]').val('');
  $('#recruitment_proposal select[name="gender"]').change();
  $('#recruitment_proposal select[name="literacy"]').val('');
  $('#recruitment_proposal select[name="literacy"]').change();
  $('#recruitment_proposal select[name="experience"]').val('');
  $('#recruitment_proposal select[name="experience"]').change();
}
function edit_proposal(invoker,id){
  "use strict";
  $('#additional_proposal').html('');
  $('#additional_proposal').append(hidden_input('id',id));
  $('.edit-title').removeClass('hide');
  $('.add-title').addClass('hide');
  $('#recruitment_proposal').modal('show');
  $('#recruitment_proposal input[name="proposal_name"]').val($(invoker).data('proposal_name'));
  $('#recruitment_proposal input[name="amount_recruiment"]').val($(invoker).data('amount_recruiment'));
  $('#recruitment_proposal input[name="workplace"]').val($(invoker).data('workplace'));
  $('#recruitment_proposal input[name="from_date"]').val($(invoker).data('from_date'));
  $('#recruitment_proposal input[name="to_date"]').val($(invoker).data('to_date'));
  $('#recruitment_proposal input[name="salary_from"]').val($(invoker).data('salary_from'));
  $('#recruitment_proposal input[name="salary_to"]').val($(invoker).data('salary_to'));
  $('#recruitment_proposal input[name="ages_from"]').val($(invoker).data('ages_from'));
  $('#recruitment_proposal input[name="ages_to"]').val($(invoker).data('ages_to'));
  $('#recruitment_proposal input[name="height"]').val($(invoker).data('height'));
  $('#recruitment_proposal input[name="weight"]').val($(invoker).data('weight'));
  $('#recruitment_proposal textarea[name="reason_recruitment"]').val($(invoker).data('reason_recruitment'));

  /*get job_description*/
    $.post(admin_url + 'recruitment/get_recruitment_proposal_edit/'+id).done(function(response) {
        response = JSON.parse(response);

      tinyMCE.activeEditor.setContent(response.description);

        $('.selectpicker').selectpicker({
        });

    });  

  $('#recruitment_proposal select[name="position"]').val($(invoker).data('position'));
  $('#recruitment_proposal select[name="position"]').change();
  $('#recruitment_proposal select[name="department"]').val($(invoker).data('department'));
  $('#recruitment_proposal select[name="department"]').change();
  $('#recruitment_proposal select[name="form_work"]').val($(invoker).data('form_work'));
  $('#recruitment_proposal select[name="form_work"]').change();
  $('#recruitment_proposal select[name="approver"]').val($(invoker).data('approver'));
  $('#recruitment_proposal select[name="approver"]').change();
  $('#recruitment_proposal select[name="gender"]').val($(invoker).data('gender'));
  $('#recruitment_proposal select[name="gender"]').change();
  $('#recruitment_proposal select[name="literacy"]').val($(invoker).data('literacy'));
  $('#recruitment_proposal select[name="literacy"]').change();
  $('#recruitment_proposal select[name="experience"]').val($(invoker).data('experience'));
  $('#recruitment_proposal select[name="experience"]').change();
}
function init_recruitment_proposal(id) {
  load_small_table_item_proposal(id, '#proposal_sm_view', 'proposal_id', 'recruitment/get_proposal_data_ajax', '.proposal_sm');
}
function load_small_table_item_proposal(pr_id, selector, input_name, url, table) {
  "use strict";
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
  if (!$("body").hasClass('small-table')) { toggle_small_view_proposal(table, selector); }
  $('input[name="' + input_name + '"]').val(pr_id);
  do_hash_helper(pr_id);
  $(selector).load(admin_url + url + '/' + pr_id);
  if (is_mobile()) {
      $('html, body').animate({
          scrollTop: $(selector).offset().top + 150
      }, 600);
  }
}
function toggle_small_view_proposal(table, main_data) {
    "use strict";
    var hidden_columns = [3];
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
function formatNumber(n) {
  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}
function formatCurrency(input, blur) {
  "use strict";
  var input_val = input.val();
  if (input_val === "") { return; }
  var original_len = input_val.length; 
  var caret_pos = input.prop("selectionStart");
  if (input_val.indexOf(".") >= 0) {
    var decimal_pos = input_val.indexOf(".");
    var left_side = input_val.substring(0, decimal_pos);
    var right_side = input_val.substring(decimal_pos);
    left_side = formatNumber(left_side);
    right_side = formatNumber(right_side);
    right_side = right_side.substring(0, 2);
    input_val = left_side + "." + right_side;
  } else {

    input_val = formatNumber(input_val);
    input_val = input_val;
  }
  input.val(input_val);
  var updated_len = input_val.length;
  caret_pos = updated_len - original_len + caret_pos;
  input[0].setSelectionRange(caret_pos, caret_pos);
}

function preview_proposal_btn(invoker){
    "use strict";
    var id = $(invoker).attr('id');
    var rel_id = $(invoker).attr('rel_id');
    view_proposal_file(id, rel_id);
}

function view_proposal_file(id, rel_id) {
  "use strict";
  $('#proposal_file_data').empty();
  $("#proposal_file_data").load(admin_url + 'recruitment/file/' + id + '/' + rel_id, function(response, status, xhr) {
      if (status == "error") {
          alert_float('danger', xhr.statusText);
      }
  });
}
function close_modal_preview(){
 $('._project_file').modal('hide');
}