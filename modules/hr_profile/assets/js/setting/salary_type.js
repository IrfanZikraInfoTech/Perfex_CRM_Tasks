
appValidateForm($('#salary_form_manage'), {
    warehouse_code: 'required',
    warehouse_name: 'required',
    
});

function new_salary_form(){
    'use strict';

    $('#salary_form').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#additional_salary_form').html('');

    $('#salary_form input[name="form_name"]').val('');
    $('#salary_form input[name="salary_val"]').val('');

    $("input[data-type='currency']").on({
        keyup: function() {        
          formatCurrency($(this));
        },
      blur: function() { 
          formatCurrency($(this), "blur");
      }
    });
}
function edit_salary_form(invoker,id){
    'use strict';
    $('#additional_salary_form').html('');
    
    $('#additional_salary_form').append(hidden_input('id',id));
    $('#salary_form input[name="form_name"]').val($(invoker).data('name'));
    $('#salary_form input[name="salary_val"]').val($(invoker).data('amount'));
    $('#salary_form select[name="tax"]').val($(invoker).data('taxable'));
    $('#salary_form select[name="tax"]').change();
    $('#salary_form').modal('show');
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');

    $("input[data-type='currency']").on({
        keyup: function() {        
          formatCurrency($(this));
        },
      blur: function() { 
          formatCurrency($(this), "blur");
      }
    });
}

function formatNumber(n) {
    'use strict';

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