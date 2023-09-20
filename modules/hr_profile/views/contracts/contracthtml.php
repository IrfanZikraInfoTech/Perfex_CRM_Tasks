<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">

			<div class="col-md-12" >
				<div class="panel_s">

					<div class="panel-body">

						<div class="mtop15 preview-top-wrapper">
							<div class="row">
								<div class="col-md-3">
									<div class="mbot30">
										<div class="contract-html-logo">
											<?php echo get_dark_company_logo(); ?>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
							</div>


							<div class="row">
								<div class="col-md-12">
									<h4 class="pull-left no-mtop contract-html-subject"><?php echo html_entity_decode($contract->contract_code); ?><br />
										<small><?php echo hr_get_contract_type($contract->name_contract); ?></small>
									</h4>
									<div class="visible-xs">
										<div class="clearfix"></div>
									</div>

									<?php if($contract->staff_signature == '' ) { ?>
										<?php if( get_staff_user_id() == $contract->staff){ ?>
											<button type="submit" id="staff_accept_action" class="btn btn-success pull-right action-button"><?php echo _l('staff_signature_sign'); ?></button>
										<?php } ?>

									<?php }?>

									<?php if($contract->signature == '' ) { ?>
										<?php if(is_admin() || get_staff_user_id() == $contract->staff_delegate){ ?>
											<button type="submit" id="accept_action" class="btn btn-success pull-right action-button"><?php echo _l('e_signature_sign'); ?></button>
										<?php } ?>

									<?php } else { ?>
										<span class="success-bg content-view-status contract-html-is-signed"><?php echo _l('is_signed'); ?></span>
									<?php } ?>
									<a href="<?php echo admin_url('hr_profile/contract_pdf/'.$contract->id_contract); ?>" class="btn btn-default pull-right action-button mright5 contract-html-pdf"><i class="fa fa-file-pdf-o"></i> <?php echo _l('download'); ?></a>

									</div>
								</div>

							</div>

							<div class="row">
								<div class="col-md-8 contract-left">
									<div class="panel_s mtop20">
										<div class="panel-body tc-content padding-30 contract-html-content">
											<?php echo html_entity_decode($contract->content); ?>
										</div>
									</div>
								</div>
								<div class="col-md-4 contract-right">
									<div class="inner mtop20 contract-html-tabs">
										<ul class="nav nav-tabs nav-tabs-flat mbot15" role="tablist">
											<li role="presentation" class="<?php if(!$this->input->get('tab') || $this->input->get('tab') === 'summary'){echo 'active';} ?>">
												<a href="#summary" aria-controls="summary" role="tab" data-toggle="tab">
													<i class="fa fa-file-text-o" aria-hidden="true"></i> <?php echo _l('summary'); ?></a>
												</li>

											</ul>
											<div class="tab-content">
												<div role="tabpanel" class="tab-pane<?php if(!$this->input->get('tab') || $this->input->get('tab') === 'summary'){echo ' active';} ?>" id="summary">
													<address class="contract-html-company-info">
														<?php echo format_organization_info(); ?>
													</address>
													<div class="row mtop20">

														<div class="col-md-5 text-muted contract-number">
															# <?php echo _l('contract_number'); ?>
														</div>
														<div class="col-md-7 contract-number">
															<?php echo html_entity_decode($contract->contract_code); ?>
														</div>
														<div class="col-md-5 text-muted contract-start-date">
															<?php echo _l('contract_start_date'); ?>
														</div>
														<div class="col-md-7 contract-start-date">
															<?php echo _d($contract->start_valid); ?>
														</div>
														<?php if(!empty($contract->end_valid)){ ?>
															<div class="col-md-5 text-muted contract-end-date">
																<?php echo _l('contract_end_date'); ?>
															</div>
															<div class="col-md-7 contract-end-date">
																<?php echo _d($contract->end_valid); ?>
															</div>
														<?php } ?>
														<?php if(!empty($contract->type_name)){ ?>
															<div class="col-md-5 text-muted contract-type">
																<?php echo _l('contract_type'); ?>
															</div>
															<div class="col-md-7 contract-type">
																<?php echo html_entity_decode($contract->name_contract); ?>
															</div>
														<?php } ?>
														<?php if($contract->signature != ''){ ?>
															<div class="col-md-5 text-muted contract-type">
																<?php echo _l('date_signed'); ?>
															</div>
															<div class="col-md-7 contract-type">
																<?php echo _d($contract->sign_day); ?>
															</div>
														<?php } ?>
													</div>


													<?php if($contract->staff_signature != ''){ ?>
														<div class="row mtop20">
															<div class="col-md-12 contract-value">
																<h4 class="bold mbot30">
																	<?php echo _l('staff_signature'); ?>
																</h4>
															</div>
															<div class="col-md-5 text-muted contract-signed-by">
																<?php echo _l('contract_signed_by'); ?>
															</div>
															
															 <?php 
															 if(is_numeric($contract->staff)){
															 	$contracts_staff_signer = get_staff_full_name($contract->staff);
															 }else {
															 	$contracts_staff_signer = ' ';
															 }

															 ?>

															<div class="col-md-7 contract-contract-signed-by">
																<?php echo html_entity_decode($contracts_staff_signer); ?>
															</div>

															<div class="col-md-5 text-muted contract-signed-by">
																<?php echo _l('contract_signed_date'); ?>
															</div>
															<div class="col-md-7 contract-contract-signed-by">
																<?php echo _d($contract->staff_sign_day); ?>
															</div>

														</div>
														<div class="row mtop20">
															<?php if ( strlen($contract->staff_signature) > 0) { ?>
															<img src="<?php echo site_url('download/preview_image?path='.protected_file_url_by_path(HR_PROFILE_CONTRACT_SIGN.$contract->id_contract.'/'.$contract->staff_signature)); ?>" class="img-responsive" alt="">
														<?php } ?>
															
														</div>
													<?php } ?>

													<?php if($contract->signature != ''){ ?>
														<div class="row mtop20">
															<div class="col-md-12 contract-value">
																<h4 class="bold mbot30">
																	<?php echo _l('company_signature'); ?>
																</h4>
															</div>
															<div class="col-md-5 text-muted contract-signed-by">
																<?php echo _l('contract_signed_by'); ?>
															</div>
															<?php 
															$staff_delegate = get_staff_full_name($contract->signer);
															 ?>
															 <?php 
															 if(is_numeric($contract->signer)){
															 	$contracts_signer = get_staff_full_name($contract->signer);
															 }else {
															 	$contracts_signer = ' ';
															 }

															 ?>

															<div class="col-md-7 contract-contract-signed-by">
																<?php echo html_entity_decode($contracts_signer); ?>
															</div>

															<div class="col-md-5 text-muted contract-signed-by">
																<?php echo _l('contract_signed_date'); ?>
															</div>
															<div class="col-md-7 contract-contract-signed-by">
																<?php echo _d($contract->sign_day); ?>
															</div>

														</div>
														<div class="row mtop20">
															<?php if ( strlen($contract->signature) > 0) { ?>
															<img src="<?php echo site_url('download/preview_image?path='.protected_file_url_by_path(HR_PROFILE_CONTRACT_SIGN.$contract->id_contract.'/'.$contract->signature)); ?>" class="img-responsive" alt="">
														<?php } ?>
															
														</div>
													<?php } ?>

												</div>

											</div>
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>

					<!-- signature_pad -->
					<div class="modal fade" tabindex="-1" role="dialog" id="identityConfirmationModal">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<?php echo form_open(( $this->uri->uri_string()), array('id'=>'identityConfirmationForm','class'=>'form-horizontal')); ?>
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title"><?php echo _l('signature'); ?> &amp; <?php echo _l('confirmation_of_identity'); ?></h4>
								</div>
								<div class="modal-body">

									<div id="identity_fields">
										<div class="form-group hide">
											<label for="acceptance_firstname" class="control-label col-sm-2">
												<span class="text-left inline-block full-width">
													<?php echo _l('client_firstname'); ?>
												</span>
											</label>
											<div class="col-sm-10">
												<input type="text" name="acceptance_firstname" id="acceptance_firstname" class="form-control"  value="<?php echo (isset($contract) ? get_staff_full_name($contract->staff_delegate) : '') ?>">
											</div>
										</div>
										<div class="form-group hide">
											<label for="acceptance_lastname" class="control-label col-sm-2">
												<span class="text-left inline-block full-width">
													<?php echo _l('client_lastname'); ?>
												</span>
											</label>
											<div class="col-sm-10">
												<input type="text" name="acceptance_lastname" id="acceptance_lastname" class="form-control"  value="<?php echo (isset($contact) ? $contact->lastname : '') ?>">
											</div>
										</div>
										<div class="form-group hide">
											<label for="acceptance_email" class="control-label col-sm-2">
												<span class="text-left inline-block full-width">
													<?php echo _l('client_email'); ?>
												</span>
											</label>
											<div class="col-sm-10">
												<input type="email" name="acceptance_email" id="acceptance_email" class="form-control"  value="<?php echo (isset($contact) ? $contact->email : '') ?>">
											</div>
										</div>
										<div class="sign_by">
											
										</div>

										<p class="bold" id="signatureLabel"><?php echo _l('signature'); ?></p>
										<div class="signature-pad--body">
											<canvas id="signature" height="130" width="550"></canvas>
										</div>
										<input type="text" style="width:1px; height:1px; border:0px;" tabindex="-1" name="signature" id="signatureInput">
										<div class="dispay-block">
											<button type="button" class="btn btn-default btn-xs clear" tabindex="-1" onclick="signature_clear();"><?php echo _l('clear'); ?></button>
										</div>
									</div>

								</div>
								<div class="modal-footer">
									<p class="text-left text-muted e-sign-legal-text">
										<?php echo _l(get_option('e_sign_legal_text'),'', false); ?>
									</p>
									<hr />
									<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
									<button type="submit" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" data-form="#identityConfirmationForm" class="btn btn-success"><?php echo _l('e_signature_sign'); ?></button>
								</div>
								<?php echo form_close(); ?>
							</div>
							<!-- /.modal-content -->
						</div>
						<!-- /.modal-dialog -->
					</div>
					<!-- /.modal -->

				</div>
			</div>
		</div>


		<?php init_tail(); ?>
<?php 
require('modules/hr_profile/assets/js/contracts/contracthtml_js.php');
?>



	