<script>
	$(function(){
		'use strict';
		init_roles_permissions_v1();


		function init_roles_permissions_v1(roleid, user_changed) {
		"use strict";
			
			roleid = typeof(roleid) == 'undefined' ? $('select[name="role_v"]').val() : roleid;
			var isedit = $('.member > input[name="isedit"]');

    // Check if user is edit view and user has changed the dropdown permission if not only return
    if (isedit.length > 0 && typeof(roleid) !== 'undefined' && typeof(user_changed) == 'undefined') {
    	return;
    }

    // Administrators does not have permissions
    if ($('input[name="administrator"]').prop('checked') === true) {
    	return;
    }

    // Last if the roleid is blank return
    if (roleid === '') {
    	return;
    }

    // Get all permissions
    var permissions = $('table.roles').find('tr');
    requestGetJSON('hr_profile/hr_role_changed/' + roleid).done(function(response) {

    	permissions.find('.capability').not('[data-not-applicable="true"]').prop('checked', false).trigger('change');

    	$.each(permissions, function() {
    		var row = $(this);
    		$.each(response, function(feature, obj) {
    			if (row.data('name') == feature) {
    				$.each(obj, function(i, capability) {
    					row.find('input[id="' + feature + '_' + capability + '"]').prop('checked', true);
    					if (capability == 'view') {
    						row.find('[data-can-view]').change();
    					} else if (capability == 'view_own') {
    						row.find('[data-can-view-own]').change();
    					}
    				});
    			}
    		});
    	});
    });
}


	});
	
</script>