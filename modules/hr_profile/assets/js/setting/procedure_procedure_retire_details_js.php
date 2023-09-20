<script>       


	function add_insurrance() {
		'use strict';

		var Input_totall_insurrance = $('.insurrance .box_area_insurrance').children().length;
		Input_totall_insurrance = Input_totall_insurrance + 1;

		var html = '';
		html += '<div class="box_area_insurrance">';
		html += '<div class="row">';
		html += '     <div class="col-md-1">';
		html += '       <a href="#" class="add_remove_action" class="text-danger" onclick="remove_question(this);"><i class="fa fa-remove" class="text-danger"></i></a>';
		html += '</div>';

		html += '<div class="col-md-11">';
		html += '<input type="text" placeholder="<?php echo _l('hr_add_options'); ?>..." name="list_insurrance['+Input_totall_insurrance+']" id="list_insurrance['+Input_totall_insurrance+']" data-box-descriptionid="" class="  my-3 form-control">';
		html += '</div>';
		html += '</div><br/>';
		html += ' </div>';
		$('.insurrance').append(html);            
	}

	function add_asset() {
		'use strict';
		var Input_totall_asset = $('.asset .box_area_asset').children().length;
		Input_totall_asset = Input_totall_asset + 1;
		var html = '';
		html += '<div class="box_area_asset">';
		html += '   <div class="row">';
		html += '     <div class="col-md-1">';
		html += '       <a href="#" class="add_remove_action text-danger" onclick="remove_question(this);"><i class="fa fa-remove"></i></a>';
		html += '</div>';

		html += '<div class="col-md-11">';
		html += '<input type="text" placeholder="<?php echo _l('hr_add_options'); ?>..." name="list_asset['+Input_totall_asset+']" id="list_asset['+Input_totall_asset+']" data-box-descriptionid="" class="  my-3 form-control">';
		html += '</div>';
		html += '</div><div class="clearfix"></div><br/>';
		html += '</div>';
		$('.asset').append(html);

	}

	function add_contract() {
		'use strict';
		var Input_totall_contract = $('.contract .box_area_contract').children().length;
		Input_totall_contract = Input_totall_contract + 1;
		var html = '';
		html += '<div class="box_area_contract">';
		html += '   <div class="row">';
		html += '     <div class="col-md-1">';
		html += '       <a href="#" class="add_remove_action" class="text-danger" onclick="remove_question(this);"><i class="fa fa-remove" class="text-danger"></i></a>';
		html += '</div>';

		html += '<div class="col-md-11">';
		html += '<input type="text" placeholder="<?php echo _l('hr_add_options'); ?>..." name="list_contract['+Input_totall_contract+']" id="list_contract['+Input_totall_contract+']" data-box-descriptionid="" class="  my-3 form-control">';
		html += '</div>';
		html += '</div>';
		html += ' </div>';
		$('.contract').append(html);
	}


	function remove_question(el){
		'use strict';
		$(el).parent().parent().parent().remove();
	}



	function add_procedure_retire(){
		'use strict';
		var html = '';
		html += '<div class="row">';
		html += '<input type="hidden" name="procedure_retire_id" value="<?php echo html_entity_decode($id);?>">';
		html += '<div class="col-md-8">';
		html += '<input type="text" placeholder="<?php echo _l('hr_item_name_to_add'); ?>..." name="rel_name[1]" id="rel_name[1]" data-box-descriptionid="" class="my-3 form-control">';
		html += '</div>';

		html += '<div class="col-md-4">';
		html += '<div class="form-group select-placeholder department_add_edit">';
		html += '<select name="people_handle_id" id="people_handle_id" class="form-control selectpicker" data-none-selected-text="<?php echo _l('hr_people_handle_id'); ?>" data-live-search="true">';
		html += '<option value=""></option>';
		html += '<?php foreach ($staffs as $s) { ?><option value="<?php echo html_entity_decode($s['staffid']); ?>" ><?php echo get_staff_full_name($s['staffid']); ?></option><?php } ?>';
		html += '</select></div>';
		html += '</div>';

		html += '<div class="col-md-12">';
		html += '<div class="content_box">';
		html += '<div class="box_area">';
		html += '<div class="row">';
		html += '<div class="col-md-1">';
		html += '<a href="#" class="add_remove_action add_more_box btn" onclick="add_option();"><i class="fa fa-plus"></i></a>';
		html += '</div>';

		html += '<div class="col-md-11">';
		html += '<input type="text" placeholder="<?php echo _l('hr_add_options'); ?>..." name="option_name[1][1]" id="option_name[1][1]" data-box-descriptionid="" class="my-3 form-control">';
		html += '</div>';
		html += '</div><br/><div class="clearfix">';
		html += '</div>';
		html += '</div>';
		html += '</div>';
		html += '</div>';

		$('.load_add_box').append(html);
		$('#add_save').remove();
		$('._buttons').append('<button type="submit" class="btn btn-info pull-left mleft10"><?php echo _l('submit'); ?></button>');
		init_selectpicker();
	}

	var total;
	total = $('.load_add_box .box_area').children().length+1;

	function add_option() {
		'use strict';

		total = total + 1;
		var html = '';
		html += '<div class="box_area">';
		html += '<div class="row">';
		html += '<div class="col-md-1">';
		html += '<a href="#" class="add_remove_action add_more_box text-danger btn" onclick="remove_question(this);"><i class="fa fa-remove"></i></a>';
		html += '</div>';

		html += '<div class="col-md-11">';
		html += '<input type="text" placeholder="<?php echo _l('hr_add_options'); ?>..." name="option_name[1]['+total+']" id="option_name[1]['+total+']" data-box-descriptionid="" class="my-3 form-control">';
		html += '</div>';
		html += '</div><br/><div class="clearfix"></div>';
		html += '</div>';
		$('.content_box').append(html);
	}


	function edit_procedure_retire(el) {
		'use strict';
		var id = $(el).data('id');
		$('input[name="id"]').val(id);
		$.post(admin_url + 'hr_profile/edit_procedure_retire/'+id).done(function(response) {
			response = JSON.parse(response);

			detail_index = response.count_option_value;

			$('.modal-title').html('<?php echo _l('hr_edit'); ?> '+response.rel_name);
			$('input[name="procedure_retire_id"]').val(response.procedure_retire_id);
			var html = '';

			var edit_index = 1;
			$.each(response.option_name, function(e, v){
				html += '<div class="box_area_s">';
				html += '<div class="row">';
				html += '<div class="col-md-1">';
				if(e == 1){
					html += '<a href="#" class="add_remove_action add_more_box btn" onclick="add_option_modal(this);"><i class="fa fa-plus"></i></a>';
				}else{
					html += '<a href="#" class="add_remove_action add_more_box btn text-danger" onclick="remove_question(this);"><i class="fa fa-remove"></i></a>';
				}
				html += '</div>';

				html += '<div class="col-md-11">';

				html += '<input type="text" placeholder="<?php echo _l('hr_add_options'); ?>..." name="option_name[1]['+edit_index+']" id="option_name[1]['+edit_index+']" data-box-descriptionid="" class="my-3 form-control" value="'+v+'">';
				html += '</div>';
				html += '</div><br/><div class="clearfix"></div>';
				html += '</div>';

				edit_index++;
			})
			html += '<div class="content_box_modal"></div>'

			$('.content_edit').html(html);
			$('.check_edit_cus').val(response.rel_name);
			$('select[name="people_handle_id"] option[value="'+response.people_handle_id+'"]').attr('selected','selected');
			$('select[name="people_handle_id"]').selectpicker('refresh');
		});      
		$('#myModal').modal('show');
		add_option_modal();
	}

	var detail_index;
	detail_index = $('.modal-body .box_area_s').children().length;

	function add_option_modal() {
		'use strict';
		detail_index = detail_index + 1;
		var html = '';
		html += '<div class="box_area_s">';
		html += '<div class="row">';
		html += '<div class="col-md-1">';
		html += '<a href="#" class="add_remove_action add_more_box text-danger btn" onclick="remove_question(this);"><i class="fa fa-remove"></i></a>';
		html += '</div>';

		html += '<div class="col-md-11">';
		html += '<input type="text" placeholder="<?php echo _l('hr_add_options'); ?>..." name="option_name[1]['+detail_index+']" id="option_name[1]['+detail_index+']" data-box-descriptionid="" class="my-3 form-control">';
		html += '</div>';
		html += '</div><br/><div class="clearfix"></div>';
		html += '</div>';
		$('.content_box_modal').append(html);
	}

</script>