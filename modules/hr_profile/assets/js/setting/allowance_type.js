  function new_allowance_type(){
    'use strict';

    $('#additional_allowance_type').empty();

    $('#allowance_type').modal('show');
    $('#allowance_type input[name="type_name"]').val('');
    $('#allowance_type input[name="allowance_val"]').val('');

    $('#allowance_type select[name="taxable"]').val('0').change();

    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');

    
    $("input[data-type='currency']").on({
        keyup: function() {        
          formatCurrency($(this));
        },
      blur: function() { 
          formatCurrency($(this), "blur");
      }
    });
}

function edit_allowance_type(invoker,id){
    'use strict';

    $('#additional_allowance_type').empty();

    $('#additional_allowance_type').append(hidden_input('id',id));
    $('#allowance_type input[name="type_name"]').val($(invoker).data('name'));

    $('#allowance_type input[name="allowance_val"]').val($(invoker).data('amount'));

    $('#allowance_type select[name="taxable"]').val($(invoker).data('taxable'));
    $('#allowance_type select[name="taxable"]').change();

    $('#allowance_type').modal('show');
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