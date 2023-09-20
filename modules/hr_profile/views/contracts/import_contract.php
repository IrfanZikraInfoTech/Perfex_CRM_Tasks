<?php defined('BASEPATH') or exit('No direct script access allowed'); 
?>
<?php
/*get salary form dynamic */ 
$salary_form =  $this->hrm_model->get_salary_form();
/*get allowance dynamic */ 
$allowance_type =  $this->hrm_model->get_allowance_type();

$file_header = array();
$file_header[] = _l('hr_contract_code');
$file_header[] = _l('hr_hr_code');
$file_header[] = _l('_id_contract_name');
$file_header[] = _l('hr_contract_form');
$file_header[] = _l('start_valid');
$file_header[] = _l('end_valid');
$file_header[] = _l('contract_status');

$file_header[] = _l('_effective_date_salary_allowance');

$file_header[] = _l('hr_sign_day');
$file_header[] = _l('hr_staff_delegate');
foreach ($salary_form as $salary_value) {
	$file_header[] = $salary_value['form_name'];
}

foreach ($allowance_type as $allowance_value) {
	$file_header[] = $allowance_value['type_name'];
}


?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div id ="dowload_file_sample">


						</div>
						<a href="<?= site_url('hr_profile/write_xlsx_contract_sample') ?>" class="btn btn-secondary-color2" ><?php echo _l('hr_download_sample') ?></a>
						<hr>

						<?php if(!isset($simulate)) { ?>
							<ul>
								<li>1. <?php echo _l('hr_import_excel_1'); ?></li>
								<li class="text-danger">2. <?php echo _l('file_xlsx_hrm_contract'); ?></li>
							</ul>
							<div class="table-responsive no-dt">
								<table class="table table-hover table-bordered">
									<thead>
										<tr>
											<?php
											$total_fields = 0;
											
											for($i=0;$i<count($file_header);$i++){
												if($i == 0 || $i == 1 ||$i == 2){
													?>
													<th class="bold"><span class="text-danger">*</span> <?php echo html_entity_decode($file_header[$i]) ?> </th>
													<?php 
												} else {
													?>
													<th class="bold"><?php echo html_entity_decode($file_header[$i]) ?> </th>
													
													<?php

												} 
												$total_fields++;
											}

											?>

										</tr>
									</thead>
									<tbody>
										<?php for($i = 0; $i<1;$i++){
											echo '<tr>';
											for($x = 0; $x<count($file_header);$x++){
												echo '<td>- </td>';
											}
											echo '</tr>';
										}
										?>
									</tbody>
								</table>
							</div>
							<hr>

						<?php } ?>
						
						<div class="row">
							<div class="col-md-4">
								<?php echo form_open_multipart(admin_url('hr_profile/import_job_p_excel'),array('id'=>'import_form')) ;?>
								<?php echo form_hidden('leads_import','true'); ?>
								<?php echo render_input('file_csv','choose_excel_file','','file'); ?> 

								<div class="form-group">
									<button id="uploadfile" type="button" class="btn btn-info import" onclick="return uploadfilecsv();" ><?php echo _l('import'); ?></button>
								</div>
								<?php echo form_close(); ?>
							</div>
							<div class="col-md-8">
								<div class="form-group ml-5" id="file_upload_response">
									
								</div>
								
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script src="<?php echo base_url('assets/plugins/jquery-validation/additional-methods.min.js'); ?>"></script>
<?php 
require('modules/hr_profile/assets/js/contracts/import_xlsx_js.php');
?>
</body>
</html>
