(function(){
	'use strict';
	var fnServerParams = {

	}
	var id=$('input[name="memberid"]').val();
	initDataTable('.table-general_bonus', admin_url + 'hr_profile/general_bonus/'+id, false, false, fnServerParams, [0, 'desc']);
	initDataTable('.table-general_discipline', admin_url + 'hr_profile/general_discipline/'+id, false, false, fnServerParams, [0, 'desc']);
})(jQuery);



