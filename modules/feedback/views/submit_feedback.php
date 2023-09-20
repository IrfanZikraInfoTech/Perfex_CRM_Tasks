<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php echo form_open('feedback/client/submit_project',array('id'=>'project-submit-form')); ?>

<input type="hidden" value="<?php echo $projectid;?>" id="projectid" name="projectid">

<div class="row">

   <div class="col-md-12">



     



      <div class="panel_s">

         <div class="panel-heading text-uppercase open-ticket-subject">

           <?php echo _l('feedback'); ?>

         </div>

         <div class="panel-body">

            <div class="row">

			

               <div class="col-md-12">

			    <div class="row">

					 <div class="col-md-6">

						  <div class="form-group open-ticket-subject-group">

							 <label for="department"><?php echo _l('rate_coding'); ?></label>

								   <select required  name="coding" id="coding" class="form-control selectpicker">

									  <option value="" ><?php echo _l('select'); ?></option>

									  <option value="5" ><?php echo _l('excellent'); ?></option>

									  <option value="4" ><?php echo _l('very_good'); ?></option>

									  <option value="3" ><?php echo _l('good'); ?></option>

									  <option value="2" ><?php echo _l('fair'); ?></option>

									  <option value="1" ><?php echo _l('bad'); ?></option>

								   </select>

							 <?php echo form_error('subject'); ?>

						  </div>

					 </div> 

					 <div class="col-md-6">

					 

					  <div class="form-group open-ticket-subject-group">

							 <label for="department"><?php echo _l('communication'); ?></label>

								   <select  name="communication" id="communication" class="form-control selectpicker">

									  <option value="" ><?php echo _l('select'); ?></option>

									  <option value="5" ><?php echo _l('excellent'); ?></option>

									  <option value="4" ><?php echo _l('very_good'); ?></option>

									  <option value="3" ><?php echo _l('good'); ?></option>

									  <option value="2" ><?php echo _l('fair'); ?></option>

									  <option value="1" ><?php echo _l('bad'); ?></option>

								   </select>

							 <?php echo form_error('subject'); ?>

						  </div>

					 

					 </div> 

				  </div>

                  <div class="row">

                     <div class="col-md-6">

                        <div class="form-group open-ticket-subject-group">

							 <label for="department"><?php echo _l('services'); ?></label>

								   <select  name="services" id="services" class="form-control selectpicker">

									  <option value="" ><?php echo _l('select'); ?></option>

									  <option value="5" ><?php echo _l('excellent'); ?></option>

									  <option value="4" ><?php echo _l('very_good'); ?></option>

									  <option value="3" ><?php echo _l('good'); ?></option>

									  <option value="2" ><?php echo _l('fair'); ?></option>

									  <option value="1" ><?php echo _l('bad'); ?></option>

								   </select>

							 <?php echo form_error('subject'); ?>

						  </div>

                     </div>

                     <div class="col-md-6">

                        <div class="form-group open-ticket-subject-group">

							 <label for="department"><?php echo _l('recommendation'); ?></label>

								   <select  name="recommendation" id="recommendation" class="form-control selectpicker">

									<option value="" ><?php echo _l('select'); ?></option>

									  <option value="5" ><?php echo _l('excellent'); ?></option>

									  <option value="4" ><?php echo _l('very_good'); ?></option>

									  <option value="3" ><?php echo _l('good'); ?></option>

									  <option value="2" ><?php echo _l('fair'); ?></option>

									  <option value="1" ><?php echo _l('bad'); ?></option>

								   </select>

							 <?php echo form_error('subject'); ?>

						  </div>

                     </div>

					<div class="form-group open-ticket-message-group">

						   <label for=""><?php echo _l('communication_comments'); ?>:</label>

						   <textarea name="message" id="message" class="form-control" rows="15"></textarea>

					</div>

                  </div>

                

               </div>

            </div>

         </div>

      </div>

   </div>

  

   <div class="col-md-12 text-center mtop20">

      <button type="submit" class="btn btn-info" data-form="#open-new-ticket-form" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('submit_feedback'); ?></button>

   </div>

</div>

<?php echo form_close(); ?>

