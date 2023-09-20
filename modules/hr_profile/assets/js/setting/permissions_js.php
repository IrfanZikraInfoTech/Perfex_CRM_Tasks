
<script>
	$(function() {
		 //procedure update_permissions
		 appValidateForm($('#update_permissions'), {
		    role: 'required',
		});

		init_selectpicker();
		$(".selectpicker").selectpicker('refresh');

		$('select[name="role"]').on('change', function() {
           var roleid = $(this).val();
           hr_profile_init_roles_permissions(roleid, true);
       	});

       	// Called when editing member profile
		function hr_profile_init_roles_permissions(roleid, user_changed) {
    	'use strict';

		    roleid = typeof(roleid) == 'undefined' ? $('select[name="role"]').val() : roleid;
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
		    requestGetJSON('staff/role_changed/' + roleid).done(function(response) {

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

		$('select[name="staff_id"]').on('change', function() {
    	'use strict';

           	var staff_id = $(this).val();
           	if(staff_id){
           		$('#additional_staff_permissions').html('');
    			$('#additional_staff_permissions').append(hidden_input('id',staff_id));

           		requestGetJSON('hr_profile/staff_id_changed/' + staff_id).done(function(response) {
           			if((response.status == 'true' || response.status == true) && (response.role_id != 0 || response.role_id != '0')){
           				$('select[name="role"]').val(response.role_id).change();

           				if(response.permission == true || response.permission == 'true'){

           					setTimeout(function(){
           						// Get all permissions
           						var permissions = $('table.roles').find('tr');

           							permissions.find('.capability').not('[data-not-applicable="true"]').prop('checked', false).trigger('change');

           							$.each(permissions, function() {
           								var row = $(this);
           								$.each(response.r_permission, function(feature, obj) {
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

           					}, 3000);

           				}
           			}else{
           				$('select[name="role"]').val('').change();
           			}

           			$('.role_hide').removeClass('hide');
           			init_selectpicker();
					$(".selectpicker").selectpicker('refresh');

           		});

           	}

       	});

       	// Permissions change, apply necessary action to disable OWN or VIEW OWN
       	$('[data-can-view-own], [data-can-view]').on('change', function() {
       		var is_own_attr = $(this).attr('data-can-view-own');
       		view_chk_selector = $(this).parents('tr').find('td input[' + (typeof is_own_attr !== typeof undefined && is_own_attr !== false ? 'data-can-view' : 'data-can-view-own') + ']');

       		if (view_chk_selector.data('not-applicable') == true) {
       			return;
       		}

       		view_chk_selector.prop('checked', false);
       		view_chk_selector.prop('disabled', $(this).prop('checked') === true);
       	});
       	

	});
</script>