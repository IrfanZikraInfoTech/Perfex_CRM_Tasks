	(function(){
		'use strict';
		$(document).ready(function(){
			$("#profile_image").change(function(){
				readURL(this);
			});
		});
		$("body").on('click', '[hr_profile-staff-edit]', function(e) {
			e.preventDefault();
			'use strict';
			$('body .hr_profile_profile_edit').toggleClass('hide');
			$('body .hr_profile_view_profile').toggleClass('hide');
		});
	})(jQuery);

	function readURL(input) {
		'use strict';
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$('#wizardPicturePreview').attr('src', e.target.result).fadeIn('slow');
			}
			reader.readAsDataURL(input.files[0]);
		}
	}