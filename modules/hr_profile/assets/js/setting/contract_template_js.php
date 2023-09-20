<script>
	
	$(function() {
		'use strict';
		
		appValidateForm($('.contract-template-form'), {
			name: 'required',
			'job_position[]': 'required',
			
		});
	});

	 var contract_id = '<?php echo $template_id; ?>';
			
			var _templates = [];

			var editor_settings = {
				selector: 'div.editable',
				inline: true,
				theme: 'inlite',
				relative_urls: false,
				remove_script_host: false,
				inline_styles: true,
				verify_html: false,
				cleanup: false,
				apply_source_formatting: false,
				valid_elements: '+*[*]',
				valid_children: "+body[style], +style[type]",
				file_browser_callback: elFinderBrowser,
				table_default_styles: {
					width: '100%'
				},
				fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt',
				pagebreak_separator: '<p pagebreak="true"></p>',
				plugins: [
				'advlist pagebreak autolink autoresize lists link image charmap hr',
				'searchreplace visualblocks visualchars code',
				'media nonbreaking table contextmenu',
				'paste textcolor colorpicker'
				],
				autoresize_bottom_margin: 50,
				insert_toolbar: 'image media quicktable | bullist numlist | h2 h3 | hr',
				selection_toolbar: 'save_button bold italic underline superscript | forecolor backcolor link | alignleft aligncenter alignright alignjustify | fontselect fontsizeselect h2 h3',
				contextmenu: "image media inserttable | cell row column deletetable | paste pastetext searchreplace | visualblocks pagebreak charmap | code",
				setup: function (editor) {

					editor.addCommand('mceSave', function () {
						// save_contract_content(true);
					});

					editor.addShortcut('Meta+S', '', 'mceSave');

					editor.on('MouseLeave blur', function () {
						if (tinymce.activeEditor.isDirty()) {
							// save_contract_content();
						}
					});

					editor.on('MouseDown ContextMenu', function () {
						if (!is_mobile() && !$('.left-column').hasClass('hide')) {
							contract_full_view();
						}
					});

					editor.on('blur', function () {
						$.Shortcuts.start();
					});

					editor.on('focus', function () {
						$.Shortcuts.stop();
					});

				}
			}

			if (_templates.length > 0) {
				editor_settings.templates = _templates;
				editor_settings.plugins[3] = 'template ' + editor_settings.plugins[3];
				editor_settings.contextmenu = editor_settings.contextmenu.replace('inserttable', 'inserttable template');
			}

			if(is_mobile()) {

				editor_settings.theme = 'modern';
				editor_settings.mobile    = {};
				editor_settings.mobile.theme = 'mobile';
				editor_settings.mobile.toolbar = _tinymce_mobile_toolbar();

				editor_settings.inline = false;
				window.addEventListener("beforeunload", function (event) {
					if (tinymce.activeEditor.isDirty()) {
						save_contract_content();
					}
				});
			}

			tinymce.init(editor_settings);


			function insert_merge_field(field) {
				var key = $(field).text();
				tinymce.activeEditor.execCommand('mceInsertContent', false, key);
			}

			function contract_full_view() {
				$('.left-column').toggleClass('hide');
				$('.right-column').toggleClass('col-md-7');
				$('.right-column').toggleClass('col-md-12');
				$(window).trigger('resize');
			}


			function save_contract_content(manual) {
				var editor = tinyMCE.activeEditor;
				var data = {};
				data.contract_id = contract_id;
				data.content = editor.getContent();
				$.post(admin_url + 'hr_profile/save_hr_contract_data', data).done(function (response) {
					response = JSON.parse(response);
					if (typeof (manual) != 'undefined') {

				// Show some message to the user if saved via CTRL + S
				alert_float('success', response.message);

				}
				// Invokes to set dirty to false
				editor.save();
				}).fail(function (error) {
					var response = JSON.parse(error.responseText);
					alert_float('danger', response.message);
				});
			}

	</script>