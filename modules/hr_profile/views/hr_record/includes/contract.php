 <?php
 $table_data = array(
 	'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_contract"><label></label></div>',

 	_l('id'),
 	_l('hr_contract_code'),
 	_l('hr_name_contract'),
 	_l('staff'),
 	_l('departments'),
 	_l('hr_start_month'),
 	_l('hr_end_month'),
 	_l('hr_status_label'),
 	_l('hr_sign_day'), 
 );
 render_datatable($table_data,'table_contract');
 ?>
 <div id="contract_modal_wrapper"></div>
