<script type="text/javascript">
  (function(){
    "use strict";
    $('#luckysheet').parents('.row').css({'position': 'fixed', 'left':'13px', 'right':'0', 'bottom' : '2px', 'top' : '92px'});
    $('#luckysheet').parents('.container').css({'width': 'unset', 'padding':'0'});
    if((<?php echo $tree_save ?>).length > 0){
      var tree = $('input[name="folder"]').comboTree({
        source : <?php echo $tree_save ?>
      });   
    }

    $('input[name="folder"]').on('change', function(){
      var id = tree.getSelectedItemsId();
      $("input[name='parent_id']").val(id.replace( /^\D+/g, ''));
    })
    if((<?php echo isset($data_form) ? "true" : "false"?>)){
      var data = <?php echo isset($data_form) ? ($data_form != "" ? $data_form : '""') : '""' ?>;
      var dataSheet = data;
      var title = "<?php echo isset($file_excel) ? $file_excel->name : "" ?>";
    }else{
      var dataSheet = [{
        name: "Sheet1",
        status: "1",
        order: "0",
        data: [],
        config: {},
        index: 0
      }, {
        name: "Sheet2",
        status: "0",
        order: "1",
        data: [],
        config: {},
        index: 1
      }, {
        name: "Sheet3",
        status: "0",
        order: "2",
        data: [],
        config: {},
        index: 2
      }];

      var title = "Spreadsheet Online New";
    }
    var options = {
      container: 'luckysheet',
      lang: 'en',
      allowEdit:true,
      forceCalculation:true,
      plugins: ['chart'],
      data: dataSheet,
      title: title

    }
    luckysheet.create(options);

    var type_screen = $("input[name='type']").val();
    var role = $("input[name='role']").val();

    if(type_screen == 3){
      $('.luckysheet_info_detail_save_as').remove();
    }
    if(role == 1){
      $('.luckysheet_info_detail_save_as').remove();
      $('.luckysheet_info_detail_save').remove();
    }
      
  })(jQuery);



</script>

