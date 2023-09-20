<script type="text/javascript">
	
   $(function(){
    'use strict';

      appValidateForm($('#import_form'),{file_csv:{required:true,extension: "xlsx"},source:'required',status:'required'});
  	if('<?php echo html_entity_decode($active_language) ?>' == 'vietnamese'){
       $( "#dowload_file_sample" ).append( '<a href="'+ site_url+'modules/hr_profile/uploads/sample_file/Sample_import_job_position_file_vi.xlsx" class="btn btn-primary" ><?php echo _l('hr_download_sample') ?></a><hr>' );

     }else{
       $( "#dowload_file_sample" ).append( '<a href="'+ site_url+'modules/hr_profile/uploads/sample_file/Sample_import_job_position_file_en.xlsx" class="btn btn-primary" ><?php echo _l('hr_download_sample') ?></a><hr>' );
     }

   });


	function get_laguage() {
    'use strict';
    
	  	return $(".header-languages").find("li.active>").html();
	}


  function uploadfilecsv(){
    'use strict';

  	if(($("#file_csv").val() != '') && ($("#file_csv").val().split('.').pop() == 'xlsx')){
  		var formData = new FormData();
  		formData.append("file_csv", $('#file_csv')[0].files[0]);
  		formData.append("csrf_token_name", $('input[name="csrf_token_name"]').val());
  		formData.append("leads_import", $('input[name="leads_import"]').val());

  		//show box loading
	    var html = '';
	        html += '<div class="Box">';
	        html += '<span>';
	        html += '<span></span>';
	        html += '</span>';
	        html += '</div>';
	    $('#box-loading').html(html);
	    $(event).attr( "disabled", "disabled" );


  		$.ajax({ 
  			url: admin_url + 'hr_profile/import_job_position_excel', 
  			method: 'post', 
  			data: formData, 
  			contentType: false, 
  			processData: false

  		}).done(function(response) {
  			response = JSON.parse(response);

  			//hide boxloading
  			$('#box-loading').html('');
      		$(event).removeAttr('disabled')

  			$("#file_csv").val(null);
  			$("#file_csv").change();
  			$(".panel-body").find("#file_upload_response").html();

  			if($(".panel-body").find("#file_upload_response").html() != ''){
  				$(".panel-body").find("#file_upload_response").empty();
  			};

  			if(response.total_rows){
  				$( "#file_upload_response" ).append( "<h4><?php echo _l("_Result") ?></h4><h5><?php echo _l('import_line_number') ?> :"+response.total_rows+" </h5>" );
  			}
  			if(response.total_row_success){
  				$( "#file_upload_response" ).append( "<h5><?php echo _l('import_line_number_success') ?> :"+response.total_row_success+" </h5>" );
  			}
  			if(response.total_row_false){
  				$( "#file_upload_response" ).append( "<h5><?php echo _l('import_line_number_failed') ?> :"+response.total_row_false+" </h5>" );
  			}
  			if(response.total_row_false > 0)
  			{
  				$( "#file_upload_response" ).append( '<a href="'+response.site_url+response.filename+'" class="btn btn-warning"  ><?php echo _l('hr_download_file_error') ?></a>' );
  			}
  			if(response.total_rows < 1){
  				alert_float('warning', response.message);
  			}
  		}).fail(function(error) {
  			//hide boxloading
  			$('#box-loading').html('');
      		$(event).removeAttr('disabled')

	        alert_float('danger', JSON.parse(error.responseText));
	    });
  		return false;
  	}else if($("#file_csv").val() != ''){
  		alert_float('warning', "<?php echo _l('_please_select_a_file') ?>");
  	}

  }

</script>