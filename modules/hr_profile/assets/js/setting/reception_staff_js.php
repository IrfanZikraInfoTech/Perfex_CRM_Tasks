 <script>
    var counttitle ;
     counttitle = $('#manage_reception').children('.title').length;

   function add_title(el){
    'use strict';

     var html = '<div class="row title pt-5">';                           
     html+='<div class="col-md-11 pt-2">';
     html+='<div class="form-group">';
     html+='<input type="text" name="title_name['+counttitle+']" class="form-control" placeholder="<?php echo _l('hr_title'); ?>" value="">';
     html+='</div>';
     html+='</div>';
     html+='<div class="col-md-1 pl-0 pt-0" name="button_add">';
     html+='<button onclick="remove_title(this); return false;" class="btn btn-danger mt-1" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>';
     html+='</div>';

     html+='<div class="col-md-12 pl-0 sub_title">';
     html+='<div class="sub">';                           
     html+='<div class="col-md-10 pt-2">';
     html+='<div class="form-group">';
     html+='<input type="text" name="sub_title_name['+counttitle+'][0]" value="" data-count="'+counttitle+'" class="form-control" placeholder="<?php echo _l('hr_sub_title'); ?>">';
     html+='</div>';
     html+='</div>';
     html+='<div class="col-md-2 pl-0 pt-0" name="button_add">';
     html+='<button onclick="add_subtitle(this); return false;" class="btn btn-primary mt-1" data-ticket="true" type="button">';
     html+='<i class="fa fa-plus"></i></button>';
     html+='</div>';
     html+='</div></div>';
     html+='</div>';

     counttitle++;

     $('#manage_reception').append(html);
   }


    var step_increa;
        step_increa = <?php echo isset($max_checklist) ? $max_checklist : 1 ; ?>;
   function add_subtitle(el){
    'use strict';
    var step_default = $(el).parent().parent().find('input').data('count');

    var html='<div class="sub"><div class="col-md-10 pt-2">';
    html+='<div class="form-group">';
    html+='<input type="text" name="sub_title_name['+step_default+']['+step_increa+']" value="" data-count="'+step_default+'" class="form-control" placeholder="<?php echo _l('hr_sub_title'); ?>">';
    html+='</div>';
    html+='</div>';
    html+='<div class="col-md-2 pl-0 pt-0" name="button_add">';
    html+='<button onclick="remove_subtitle(this); return false;" class="btn btn-danger mt-1" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>';
    html+='</div></div>';

    step_increa++;

    $(el).closest('.title').find('.sub_title').append(html);
  }

  function remove_title(el){
    'use strict';
    $(el).closest('.title').remove();
  }
  function remove_subtitle(el){
    'use strict';
    $(el).closest('.sub').remove();
  }
  $("body").on('click', '.remove_marks_emp', function() {
    'use strict';
    $(this).parents('#marks_emp').remove();
  }); 
  var addMoreVendorsInputKey2 = $('.assets_wrap').children().length + 1;
  $("body").on('click', '.new_assets_emp', function() {
    'use strict';
    if ($(this).hasClass('disabled')) { return false; }
    var new_assets_emp = $(this).parents('.assets_wrap').find('#assets_emp').eq(0).clone().appendTo($(this).parents('.assets_wrap'));

    new_assets_emp.find('input[name="asset_name[]"]').val('');       

    new_assets_emp.find('div[name="button_add"]').removeAttr("style");

    new_assets_emp.find('button[name="add_asset"] i').removeClass('fa-plus').addClass('fa-minus');
    new_assets_emp.find('button[name="add_asset"]').removeClass('new_assets_emp').addClass('remove_assets_emp').removeClass('btn-primary').addClass('btn-danger');

    new_assets_emp.find('select').selectpicker('val', '');
    addMoreVendorsInputKey2++;            
  });
  $("body").on('click', '.remove_assets_emp', function() {
    'use strict';
    $(this).parents('#assets_emp').remove();
  }); 

</script>