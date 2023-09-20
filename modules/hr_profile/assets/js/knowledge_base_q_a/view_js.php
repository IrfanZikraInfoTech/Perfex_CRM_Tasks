<script>
	$(function(){
		'use strict';

			// Lightbox for knowledge base images
			$.each($('.kb-article').find('img'), function () {
				$(this).wrap('<a href="'+$(this).attr('src')+'" data-lightbox="kb-attachment"></a>');
			});

			$('.article_useful_buttons button').on('click', function(e) {
				e.preventDefault();
				var data = {};
				data.answer = $(this).data('answer');
				data.articleid = '<?php echo html_entity_decode($article->articleid); ?>';
				$.post(admin_url+'hr_profile/knowledge_base_q_a/add_kb_answer', data).done(function(response) {
					response = JSON.parse(response);
					if (response.success == true) {
						$(this).focusout();
					}
					$('.answer_response').html(response.message);
				});
			});

		});
	function send_mail_support(obj){
		'use strict';
		
		var id = $(obj).attr('id');
		$('#'+id+'_form').modal('show');
		appValidateForm($('#mail_form_knowledge_base'), {
			content: 'required', subject:'required',email:'required'});
	}

			//contract preview file
			function preview_file_q_a(invoker){
				'use strict';
				var id = $(invoker).attr('id');
				var rel_id = $(invoker).attr('rel_id');
				view_hr_profile_q_a(id, rel_id);
			}

	 //function view hr_profile_file
	 function view_hr_profile_q_a(id, rel_id) {  
	 	'use strict'; 
	 	$('#contract_file_data').empty();
	 	$("#contract_file_data").load(admin_url + 'hr_profile/preview_q_a_file/' + id + '/' + rel_id, function(response, status, xhr) {
	 		if (status == "error") {
	 			alert_float('danger', xhr.statusText);
	 		}
	 	});
	 }
	</script>