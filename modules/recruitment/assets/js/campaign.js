(function($) {
"use strict"; 
  var CampaignServerParams = {
          "dpm": "[name='department_filter[]']",
          "posiotion_ft": "[name='position_filter[]']",
          "status": "[name='status_filter[]']",
      };
  var table_rec_campaign = $('.table-table_rec_campaign');
  var _table_api = initDataTable(table_rec_campaign, admin_url+'recruitment/table_campaign', '', '', CampaignServerParams);
  $.each(CampaignServerParams, function(i, obj) {
      $('select' + obj).on('change', function() {
          table_rec_campaign.DataTable().ajax.reload()
              .columns.adjust()
              .responsive.recalc();
      });
  });

  appValidateForm($('#recruitment-campaign-form'),{campaign_name:'required',cp_to_date:'required',cp_position:'required',
    campaign_code: {
       required: true,
       remote: {
        url: site_url + "admin/recruitment/campaign_code_exists",
        type: 'post',
        data: {
            campaign_code: function() {
                return $('input[name="campaign_code"]').val();
            },
            cp_id: function() {
                return $('input[name="cp_id"]').val();
            }
        }
    }
   }
  });

  init_recruitment_campaign();
     init_datepicker();
   init_selectpicker();
    
    $("input[data-type='currency']").on({
      keyup: function() {
        formatCurrency($(this));
      },
      blur: function() {
        formatCurrency($(this), "blur");
      }
  });  
})(jQuery);

function new_campaign(){
  "use strict"; 
  $('#recruitment_campaign').modal('show');
  $('.edit-title').addClass('hide');
  $('.add-title').removeClass('hide');
  $('#additional_campaign').html('');
  $('#recruitment_campaign input[name="campaign_code"]').val('');
  $('#recruitment_campaign input[name="campaign_name"]').val('');
  $('#recruitment_campaign input[name="cp_amount_recruiment"]').val('');
  $('#recruitment_campaign input[name="cp_workplace"]').val('');
  $('#recruitment_campaign input[name="cp_from_date"]').val('');
  $('#recruitment_campaign input[name="cp_to_date"]').val('');
  $('#recruitment_campaign input[name="cp_salary_from"]').val('');
  $('#recruitment_campaign input[name="cp_salary_to"]').val('');
  $('#recruitment_campaign input[name="cp_ages_from"]').val('');
  $('#recruitment_campaign input[name="cp_ages_to"]').val('');
  $('#recruitment_campaign input[name="cp_height"]').val('');
  $('#recruitment_campaign input[name="cp_weight"]').val('');
  $('#recruitment_campaign textarea[name="cp_reason_recruitment"]').val('');
  $('#recruitment_campaign select[id="proposal"]').val('').change();
  $('#recruitment_campaign select[name="cp_follower[]"]').val();
  $('#recruitment_campaign select[id="manager"]').val('');
  $('#recruitment_campaign select[name="cp_position"]').val('');
  $('#recruitment_campaign select[name="cp_position"]').change();
  $('#recruitment_campaign select[name="cp_department"]').val('');
  $('#recruitment_campaign select[name="cp_department"]').change();
  $('#recruitment_campaign select[name="cp_form_work"]').val('');
  $('#recruitment_campaign select[name="cp_form_work"]').change();

  $('#recruitment_campaign select[name="cp_gender"]').val('');
  $('#recruitment_campaign select[name="cp_gender"]').change();
  $('#recruitment_campaign select[name="cp_literacy"]').val('');
  $('#recruitment_campaign select[name="cp_literacy"]').change();
  $('#recruitment_campaign select[name="cp_experience"]').val('');
  $('#recruitment_campaign select[name="cp_experience"]').change();
  $('#recruitment_campaign select[name="rec_channel_form_id"]').val('').change();

  $('#recruitment_campaign select[name="company_id"]').val('').change();

  $('#recruitment_campaign input[id="display_salary"]').prop("checked", true);

  $('.selectpicker').selectpicker('refresh');
}

function edit_campaign(invoker,id){
  "use strict"; 
  $('#additional_campaign').html('');
  $('#additional_campaign').append(hidden_input('cp_id',id));
  $('.edit-title').removeClass('hide');
  $('.add-title').addClass('hide');
  $('#recruitment_campaign').modal('show');
  $('#recruitment_campaign input[name="campaign_code"]').val($(invoker).data('campaign_code'));
  $('#recruitment_campaign input[name="campaign_name"]').val($(invoker).data('campaign_name'));
  $('#recruitment_campaign input[name="cp_amount_recruiment"]').val($(invoker).data('amount_recruiment'));
  $('#recruitment_campaign input[name="cp_workplace"]').val($(invoker).data('workplace'));
  $('#recruitment_campaign input[name="cp_from_date"]').val($(invoker).data('from_date'));
  $('#recruitment_campaign input[name="cp_to_date"]').val($(invoker).data('to_date'));
  $('#recruitment_campaign input[name="cp_salary_from"]').val($(invoker).data('salary_from'));
  $('#recruitment_campaign input[name="cp_salary_to"]').val($(invoker).data('salary_to'));
  $('#recruitment_campaign input[name="cp_ages_from"]').val($(invoker).data('ages_from'));
  $('#recruitment_campaign input[name="cp_ages_to"]').val($(invoker).data('ages_to'));
  $('#recruitment_campaign input[name="cp_height"]').val($(invoker).data('height'));
  $('#recruitment_campaign input[name="cp_weight"]').val($(invoker).data('weight'));
  $('#recruitment_campaign textarea[name="cp_reason_recruitment"]').val($(invoker).data('reason_recruitment'));

  if($(invoker).data('company_id') != 0){
      $('#recruitment_campaign select[name="company_id"]').val($(invoker).data('company_id')).change();

  }else{
      $('#recruitment_campaign select[name="company_id"]').val('').change();

  }

  if($(invoker).data('rec_channel_form_id') != 0 ){
    $('#recruitment_campaign select[name="rec_channel_form_id"]').val($(invoker).data('rec_channel_form_id')).change();
  }else{

   $('#recruitment_campaign select[name="rec_channel_form_id"]').val('').change();
  }


  /*get job_description*/
    $.post(admin_url + 'recruitment/get_recruitment_campaign_edit/'+id).done(function(response) {
        response = JSON.parse(response);

      tinyMCE.activeEditor.setContent(response.description);

        $('.selectpicker').selectpicker({
        });

    });

  $('#recruitment_campaign select[id="proposal"]').val(($(invoker).data('proposal'))).change();

    var _recruitment_campaign_manager = $(invoker).data('manager');
    if(typeof(_recruitment_campaign_manager) == "string"){
          $('#recruitment_campaign select[id="manager').val( ($(invoker).data('manager')).split(',')).change();
    }else{
         $('#recruitment_campaign select[id="manager').val($(invoker).data('manager')).change();

    }

  var _recruitment_campaign_follower = $(invoker).data('follower');

      if(typeof(_recruitment_campaign_follower) == "string"){
          $('#recruitment_campaign select[name="cp_follower[]"]').val( ($(invoker).data('follower')).split(',')).change();
      }else{
         $('#recruitment_campaign select[name="cp_follower[]"]').val($(invoker).data('follower')).change();

      }

  $('#recruitment_campaign select[name="cp_position"]').val($(invoker).data('position'));
  $('#recruitment_campaign select[name="cp_position"]').change();

  if($(invoker).data('department') != 0 ){
    $('#recruitment_campaign select[name="cp_department"]').val($(invoker).data('department')).change();
  }else{

   $('#recruitment_campaign select[name="cp_department"]').val('').change();
  }


  if($(invoker).data('form_work') != 0 ){
    $('#recruitment_campaign select[name="cp_form_work"]').val($(invoker).data('form_work')).change();
  }else{

   $('#recruitment_campaign select[name="cp_form_work"]').val('').change();
  }


  $('#recruitment_campaign select[name="cp_gender"]').val($(invoker).data('gender'));
  $('#recruitment_campaign select[name="cp_gender"]').change();
  $('#recruitment_campaign select[name="cp_literacy"]').val($(invoker).data('literacy'));
  $('#recruitment_campaign select[name="cp_literacy"]').change();
  $('#recruitment_campaign select[name="cp_experience"]').val($(invoker).data('experience'));
  $('#recruitment_campaign select[name="cp_experience"]').change();

  /*render checkec*/

    if($(invoker).data('display_salary') == '1'){
      $('#recruitment_campaign input[id="display_salary"]').attr('checked', true);
    }else{
      $('#recruitment_campaign input[id="display_salary"]').removeAttr("checked");
    }

  $('.selectpicker').selectpicker('refresh');
}
function init_recruitment_campaign(id) {

  load_small_table_item_campaign(id, '#campaign_sm_view', 'campaign_id', 'recruitment/get_campaign_data_ajax', '.campaign_sm');
}
function load_small_table_item_campaign(pr_id, selector, input_name, url, table) {
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
  if (!$("body").hasClass('small-table')) { toggle_small_view_campaign(table, selector); }
  $('input[name="' + input_name + '"]').val(pr_id);
  do_hash_helper(pr_id);
  $(selector).load(admin_url + url + '/' + pr_id);
  if (is_mobile()) {
      $('html, body').animate({
          scrollTop: $(selector).offset().top + 150
      }, 600);
  }
   init_selectpicker();
  
  $('.selectpicker').selectpicker('refresh');


}
function toggle_small_view_campaign(table, main_data) {
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

function preview_campaign_btn(invoker){
  "use strict"; 
    var id = $(invoker).attr('id');
    var rel_id = $(invoker).attr('rel_id');
    view_campaign_file(id, rel_id);
}

function view_campaign_file(id, rel_id) {
  "use strict"; 
  $('#campaign_file_data').empty();
  $("#campaign_file_data").load(admin_url + 'recruitment/campaign_file/' + id + '/' + rel_id, function(response, status, xhr) {
      if (status == "error") {
          alert_float('danger', xhr.statusText);
      }
  });
}
function close_modal_preview(){
 $('._project_file').modal('hide');
}