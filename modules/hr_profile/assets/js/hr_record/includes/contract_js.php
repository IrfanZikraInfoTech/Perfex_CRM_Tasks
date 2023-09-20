<script>

	$(function(){
		'use strict';
	     var ContractsServerParams = {
	      "memberid": "[name='memberid']",
	      "member_view": "[name='member_view']",
	     };
		var table_contract = $('table.table-table_contract');
		initDataTable(table_contract, admin_url+'hr_profile/table_contract', undefined, undefined, ContractsServerParams,[1, 'desc']);

		 //hide first column
	    var hidden_columns = [0,1,4,5];
	        $('.table-table_contract').DataTable().columns(hidden_columns).visible(false, false);
	});

    
    function member_view_contract(contract_id) {
      "use strict";

      $("#contract_modal_wrapper").load("<?php echo admin_url('hr_profile/hr_profile/view_contract_modal'); ?>", {
           slug: 'view',
           contract_id: contract_id
      }, function() {
           if ($('.modal-backdrop.fade').hasClass('in')) {
                $('.modal-backdrop.fade').remove();
           }
           if ($('#staff_contract_modal').is(':hidden')) {
                $('#staff_contract_modal').modal({
                     show: true
                });
           }
      });

      init_selectpicker();
      $(".selectpicker").selectpicker('refresh');
  }

</script>





