<script>
(function($) {
"use strict";	
	$('#reject_btn').on('click',function(){
	  $('#approved_div').addClass('hide');
	});
	$('#approved_btn').on('click',function(){
	  $('#reject_div').addClass('hide');
	});


	  init_items_sortable(true);
   init_btn_with_tooltips();
   init_datepicker();
   init_selectpicker();
   init_form_reminder();
   init_tabs_scrollable();
   $("input[data-type='currency']").on({
    keyup: function() {        
      formatCurrency($(this));
    },
    blur: function() { 
      formatCurrency($(this), "blur");
    }
});

})(jQuery);



function delete_campaign_attachment(id) {
	"use strict";	
	if (confirm_delete()) {
	    requestGet('recruitment/delete_campaign_attachment/' + id).done(function(success) {
	        if (success == 1) {
	            $("#campaign_pv_file").find('[data-attachment-id="' + id + '"]').remove();
	        }
	    }).fail(function(error) {
	        alert_float('danger', error.responseText);
	    });
	}
}
function change_status_campaign(invoker,id_cp){
	"use strict";	
	  $.post(admin_url+'recruitment/change_status_campaign/'+invoker.value+'/'+id_cp).done(function(reponse){
	    reponse = JSON.parse(reponse);
	    window.location.href = admin_url + 'recruitment/recruitment_campaign/'+id_cp;
	    alert_float('success',reponse.result);
	  });
}
</script>