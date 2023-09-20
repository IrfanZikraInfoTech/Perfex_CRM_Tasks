"use strict"; 
var _customer_id = $("#customer").val();
$(document).ready(function() {
    $('tr td:nth-child(1)').hide();
});
var reminderid;
$(function(){
   var reminder_ServerParams = {};
   $.each($('._hidden_inputs._filters input'),function(){
     reminder_ServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
 });
   var tAPI=  initDataTable('.table-reminder', admin_url+'reminder/table', ['undefined'], ['undefined'], reminder_ServerParams, [0, 'desc']);
   init_reminder();
   $('#small-table').removeClass('hide');
   $('.complete_data').on('change',function(){
    var $checked = $('input[name="show_complete"]').prop('checked');
    if($checked) {
      $('input[name="complete_reminder"]').val('0');
      $('.table-reminder').DataTable().ajax.reload().columns.adjust().responsive.recalc();;
  }else{
      $('input[name="complete_reminder"]').val('1');
      $('.table-reminder').DataTable().ajax.reload().columns.adjust().responsive.recalc();;
  }
});
//Open modal
function getQueryStringValue (key) {  
    return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));  
}  
if(getQueryStringValue("add")!= 'undefined' && getQueryStringValue("add") == "true"){
    $('#reminderAddModal').modal('show');
}
});
// Init single proposal
function init_reminder(id) {
    reminder_load_small_table_item(id, '#reminder', 'reminderid', 'reminder/get_reminder_data_ajax', '.table-reminder');
   
}
$( "#reminderModal" ).remove();
function getmodal(id='')
{
    $( "#reminderModal" ).remove();
    $("#reminderModal").modal('show');
    $('body').append('<div class="dt-loader"></div>');
    $.post(admin_url + 'reminder/getreminderEditModal', {
        id: id
    }).done(function (response) {
        $('body').find('.dt-loader').remove();
        $("#reminderModalData").html(response);
        $(".reminder-modal").modal('show');
    });
}
$('body').on('change','#rel_type', function() {
  $('#rel_type').eq(1).prop('selected', true);
  $('#rel_id').eq(1).prop('selected', true);
  var check_view= $(this).attr('data-side_view');
if(check_view =='side_view'){
  _customer_id=$(".side_cust #customer").val();
}
  var rel_type = $(this).val() ? $(this).val() : 0;
  $.get(admin_url + 'reminder/get_related_doc/' + rel_type+'/'+_customer_id, function(response) {
      if(response){
         $('.relidwrap').removeClass('hide');
        $('#rel_id').html(response);
        $('#rel_id').selectpicker('refresh');
    }
});
});
function validate_reminder_form(selector) {
    selector = typeof (selector) == 'undefined' ? '#reminder-form' : selector;
    appValidateForm($(selector), {
        date: 'required',
        customer: 'required',
        description: 'required',
        contact: 'required',
        rel_type: 'required',
    });
} 
$(function(){
    validate_reminder_form();
});
var _rel_id = $('#rel_id'),_rel_type = $('#rel_type'),_rel_id_wrapper = $('#rel_id_wrapper'),data = {};
$('body').on('change','#customer', function() {
  var check_view=$(this).attr("data-full_view");
    _customer_id = $(this).val() ? $(this).val() : 0;
    $('select[name=rel_type]').val(1);
    $('.selectpicker').selectpicker('refresh')
    if(_customer_id != ''){
      $.get(admin_url + 'reminder/get_contact_data_values/' + _customer_id + '/customer', function(response) {
          if(check_view == 'full_view'){ 
           $('.full_cn').html(response.field_to);
          $('.full_view, #contact').selectpicker('refresh');
          $('.full_view, .proposal_to_wrap').removeClass('hide');
        }else{
         $('.side_cn').html(response.field_to);
          $('.side_view, #contact').selectpicker('refresh');
          $('.side_view, .proposal_to_wrap').removeClass('hide');
        }
      }, 'json');
    }else{
        var rel_type = $("#rel_type").val();
        $.get(admin_url + 'reminder/get_related_doc/' + rel_type+'/'+_customer_id, function(response) {
        if(response){
            $('#rel_id').html(response);
            $('#rel_id').selectpicker('refresh');
        }
    });
     if(check_view == 'full_view'){   
    
     $('.full_view, .proposal_to_wrap').addClass('hide');
    }else{
    $('.side_view, .proposal_to_wrap').addClass('hide');
    }
    $('#rel_type').eq(1).prop('selected', true);
    $('#assigned_to').eq(1).prop('selected', true);
    $('#rel_id').eq(1).prop('selected', true);
    }
});
function reminder_load_small_table_item(id, selector, input_name, url, table) {
    var _tmpID = $('input[name="' + input_name + '"]').val();
    if (_tmpID !== '' && !window.location.hash) {
        id = _tmpID;
        $('input[name="' + input_name + '"]').val('');
    } else {
        if (window.location.hash && !id) {
            id = window.location.hash.substring(1);
        }
    }
    if (typeof (id) == 'undefined' || id === '') {
        return;
    }
    destroy_dynamic_scripts_in_element($(selector))
    if (!$("body").hasClass('small-table')) {
        reminder_toggle_small_view(table, selector);
    }
    $('input[name="' + input_name + '"]').val(id);
    do_hash_helper(id);
    $(selector).load(admin_url + url + '/' + id);
    if (is_mobile()) {
        $('html, body').animate({
            scrollTop: $(selector).offset().top + 150
        }, 600);
    }
}
function reminder_toggle_small_view(table, main_data) {
    console.log(main_data);
    $("body").toggleClass('small-table');
    var tablewrap = $('#small-table');
    if (tablewrap.length === 0) {
        return;
    }
    var _visible = false;
    if (tablewrap.hasClass('col-md-6')) {
        tablewrap.removeClass('col-md-6').addClass('col-md-12');
        _visible = true;
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-right').addClass('fa fa-angle-double-left');
    } else {
        tablewrap.addClass('col-md-6').removeClass('col-md-12');
        $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-left').addClass('fa fa-angle-double-right');
    }
    var _table = $(table).DataTable();
    // Show hide hidden columns
    _table.columns(hidden_columns).visible(_visible, false);
    _table.columns.adjust();
    $(main_data).toggleClass('hide');
    $(window).trigger('resize');
}
$(document).on('submit', '.form_submit', function (event) {
    event.preventDefault();
    
    formdata = new FormData($(this)[0]);
    $.ajax({
        url: $(this).attr('action'),
        data: formdata,
        contentType: false,
        processData: false,
        type: 'POST',
        dataType: "json",
        success: function (response) { 
            if (response.success == true) {
                window.location.href = "";
            } else if (response.success == "warning") {
                $('.err' + 'franchise_number').css('color', 'red').text(response.msg);
            }
            else {
                $.each(response.errors, function (key, value) {
                    console.log(value);
                    $('.err' + key).css('color', 'red').text(value);
                });
            }
        }
    });
});