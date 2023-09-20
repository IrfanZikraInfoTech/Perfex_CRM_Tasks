	<script>	
	
		function change_info(){
			'use strict';
			return false;
		}

		function change_mark(){
			'use strict';
			return false;
		}

		function active_asset(el){
			'use strict';
			var data={};
			data.allocation_id = $(el).data('id');
			if($(el).val()==1){
				$(el).val(0);
				data.status_allocation = 0;
			}
			else{
				$(el).val(1);
				data.status_allocation = 1;
			}
			$.post(admin_url+'hr_profile/change_status_allocation_asset', data).done(function(response){
				response = JSON.parse(response);
				if(response.success == true) { 
					change_progress();  
				}
			});
		}

		function convert_float(x){
			'use strict';
			var n = parseFloat(x);
			return Math.round(n * 100)/100;
		}

		function write_progress(el,len,len_total){
			'use strict';
			if(len>0){
				var len_progress = len * 100 / len_total;
				var percent = convert_float(len_progress);
				$(el).text(convert_float(len_progress)+'%').css({"width": percent +"%", "color":"#fff","margin":"unset"});
				if(percent >= 33.33 && percent <= 80){
					$(el).removeClass('bg-green').addClass('bg-secondary').removeClass('bg-danger');
				}
				if(percent > 81){
					$(el).addClass('bg-green').removeClass('bg-secondary').removeClass('bg-danger');
				}
			}else{
				$(el).text('0%').css({"width":"16%", "margin-left":"5px"}).removeClass('bg-green').removeClass('bg-secondary').addClass('bg-danger');		
			}
		}
		
		var count_id = '<?php echo html_entity_decode($count_id); ?>';
		function change_progress(){
			'use strict';
			var len_total = $('input[name="info_staff[]"]').length;
			var len = $('input[name="info_staff[]"]:checked').length;
			write_progress('#info_staff',len,len_total);
			len_total = $('input[name="asset_staff[]"]').length;
			len = $('input[name="asset_staff[]"]:checked').length;
			write_progress('#asset_staff',len,len_total);
			len_total = $('input[name="training_staffs[]"]').length;
			var len = $('input[name="training_staffs[]"]:checked').length;
			write_progress('#training_staff',len,len_total);
			for (var i = 1; i <= count_id; i++) {
				len_total = $('input[name="subitem['+i+']"]').length;
				var len = $('input[name="subitem['+i+']"]:checked').length;
				write_progress('div[name="progress['+i+']"]',len,len_total);
			}
			reload_table_reception_staff();
		}

		change_progress();
		function change_info_checklist(el){
			'use strict';
			var data={};
			data.checklist_id = $(el).data('id');
			if($(el).val()==1){
				$(el).val(0);
				data.status_checklist = 0;
			}
			else{
				$(el).val(1);
				data.status_checklist = 1;
			}
			$.post(admin_url+'hr_profile/change_status_checklist', data).done(function(response){
				response = JSON.parse(response);
				if(response.success == true) { 
					change_progress();
				}
			});
		}

		var length;
			length = $('.add_asset_staff').length;
		function gen_input_asset(){
    	'use strict';

			var html = '<div class="sub"><br/><div class="row"><div class="col-md-10 pt-0 pb-4 px-0"><input type="text" name="add_asset_staff['+length+']" class="form-control w-100 add_asset_staff" placeholder="<?php echo _l('hr_new_asset_name'); ?>" required></div><div class="col-md-2"><button class="btn text-danger border-0 shadow-none" type="button" onclick="remove_new_asset(this);" class="bg-dark"><i class="fa fa-minus" ></i></button></div></div></div>';
			$('#add_asset').append(html);
			$('.btn_save_add_asset').removeClass('d-none');
			length++;
		}

		function remove_new_asset(el){
			'use strict';
			$(el).closest('.sub').remove();
			var length = $('.add_asset_staff').length;
			if(length == 0){
				$('.btn_save_add_asset').addClass('d-none');
			}
		}

		function rtrim(str){
			'use strict';
			return str.replace(/\,+$/, '');
		}

		function save_add_asset(){
			'use strict';
			var data = {}
			var t = '';           
			var list = $('.add_asset_staff');
			for (var i = 0; i < list.length ; i++) {
				var val = list.eq(i).val();
				if(val.trim()){
					t += val+',';				
				}
			}
			data.name = rtrim(t);
			$.post(admin_url+'hr_profile/add_new_asset/<?php echo html_entity_decode($staff->staffid); ?>',data).done(function(response){
				response = JSON.parse(response);
				if(response.success == true) {
					$('#asset_list').html(response.data);
					$('#add_asset').html('');
					$('.btn_save_add_asset').addClass('d-none');
					change_progress();
				}
			});
		}

		function delete_asset(el){
			'use strict';
			var val = $(el).data('id');
			$.post(admin_url+'hr_profile/delete_asset/'+val+'/<?php echo html_entity_decode($staff->staffid); ?>').done(function(response){
				response = JSON.parse(response);
				if(response.success == true) {
					$('#asset_list').html(response.data);
					alert_float('success','<?php echo _l('hr_deleted'); ?>');
					change_progress();
				}
			});
		}	


		//reload table when close
		function reload_table_reception_staff(el){
    	'use strict';

               $('table.table-table_staff').DataTable().ajax.reload(null, false)
                .columns.adjust()
                .responsive.recalc();
		};
	</script>