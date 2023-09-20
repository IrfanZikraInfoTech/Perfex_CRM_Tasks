	
var hrmAttachmentDropzone;
	(function(){
		'use strict';
		if (typeof (hrmAttachmentDropzone) != 'undefined') {
		  hrmAttachmentDropzone.destroy();
		  hrmAttachmentDropzone = null;
		}

	   Dropzone.autoDiscover = false;
		if($('#hr_profile_attachment').length){
		   hrmAttachmentDropzone = new Dropzone("#hr_profile_attachment", appCreateDropzoneOptions({
			  uploadMultiple: true,
			  parallelUploads: 20,
			  maxFiles: 20,
			  paramName: 'file',
			  sending: function (file, xhr, formData) {
				 formData.append("staffid", $('input[name="memberid"]').val());
			  },
			  success: function (files, response) {
				 response = JSON.parse(response);
				 alert_float('success', response.message);
				 var html ='';
				 var data = response.data;
				 if(data){
				  $("#attachment_file").empty();
				  $("#attachment_file").append(data);

				 }
				 if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
				 }
			  }
		   }));
		}

	})(jQuery);

	//preview file
	function preview_file_staff(invoker){
		'use strict';
		var id = $(invoker).attr('id');
		var rel_id = $(invoker).attr('rel_id');
		view_hr_profilestaff_file(id, rel_id);
	}

	function view_hr_profilestaff_file(id, rel_id) {   
		'use strict';
		$('#contract_file_data').empty();
		$("#contract_file_data").load(admin_url + 'hr_profile/hr_profile_file/' + id + '/' + rel_id, function(response, status, xhr) {
			if (status == "error") {
				alert_float('danger', xhr.statusText);
			}
		});
	}
	 
	function delete_hr_att_file_attachment(wrapper, id) {
		'use strict'; 	
		if (confirm_delete()) {
			$.get(admin_url + 'hr_profile/delete_hr_profile_staff_attachment/' + id, function (response) {
				if (response.success == true || response.success == 'true') {
					$(wrapper).parents('.contract-attachment-wrapper').remove();

					var totalAttachmentsIndicator = $('.attachments-indicator');
					var totalAttachments = totalAttachmentsIndicator.text().trim();
					if(totalAttachments == 1) {
						totalAttachmentsIndicator.remove();
					} else {
						totalAttachmentsIndicator.text(totalAttachments-1);
					}
					alert_float('success', response.message);
				} else {
					alert_float('danger', response.message);
				}
			}, 'json');
		}
		return false;
	}