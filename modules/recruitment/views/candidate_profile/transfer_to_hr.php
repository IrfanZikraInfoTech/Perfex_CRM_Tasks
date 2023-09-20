<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
 	<?php echo form_hidden('cd_id',$candidate->id); ?>
 	<?php echo form_open(admin_url('recruitment/transfer_hr/'.$candidate->id),array('class'=>'transfer-form','autocomplete'=>'off')); ?>
    <div class="col-md-8 col-md-offset-2" id="small-table">
   <div class="panel_s">
     <div class="panel-body">
       <ul class="nav nav-tabs" role="tablist">
       	<li role="presentation" class="active">
         <a href="#tab_staff_profile" aria-controls="tab_staff_profile" role="tab" data-toggle="tab">
         <span class="glyphicon glyphicon-user"></span>&nbsp;<?php echo _l('staff_profile_string'); ?>
         </a>
      	</li>
      	<?php if(is_admin()){ ?>
          <li role="presentation">
             <a href="#staff_permissions" aria-controls="staff_permissions" role="tab" data-toggle="tab">
             <span class="glyphicon glyphicon-lock"></span>&nbsp;<?php echo _l('staff_add_edit_permissions'); ?>
             </a>
          </li>
        <?php } ?>
       </ul>
       <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="tab_staff_profile">          	
          	<div class="row">
	          	<div class="col-md-12">
	          		<?php $attrs = (isset($candidate) ? array() : array('autofocus'=>true)); ?>
	          		<?php $staff_identifi = $staff_code ?>
	             	<?php echo render_input('staff_identifi','re_staff_code', $staff_identifi,'text',$attrs); ?>
	          	</div>
	            
            </div>
            <div class="row">
	            <div class="col-md-6">
	             <?php $value = (isset($candidate) ? $candidate->candidate_name : ''); ?>
	             <?php $attrs = (isset($candidate) ? array() : array('autofocus'=>true)); ?>
	             <?php echo render_input('firstname','first_name',$value,'text',$attrs); ?>  
	         	</div>

	         	<div class="col-md-6">
	             <?php $last_name = (isset($candidate) ? $candidate->last_name : ''); ?>
	             <?php $attrs = (isset($candidate) ? array() : array('autofocus'=>true)); ?>
	             <?php echo render_input('lastname','last_name',$last_name,'text',$attrs); ?>  
	         	</div>
            	
            </div>
            <div class="row">
	            <div class="col-md-6">
	            	<?php $email = isset($candidate) ? $candidate->email : ''; ?>
	             <?php echo render_input('email','staff_add_edit_email', $email,'email',array('autocomplete'=>'off')); ?>
	            </div>
	             
	            <div class="col-md-6">
	             <?php $value = (isset($candidate) ? $candidate->phonenumber : ''); ?>
	             <?php echo render_input('phonenumber','staff_add_edit_phonenumber',$value); ?>
	         	</div>
         	</div>
         	<div class="row">
		         <div class="form-group col-md-6">
		            <label for="facebook" class="control-label"><i class="fa fa-facebook"></i> <?php echo _l('staff_add_edit_facebook'); ?></label>
		            <input type="text" class="form-control" name="facebook" value="<?php echo html_entity_decode($candidate->facebook); ?>">
		         </div>
		         
		         <div class="form-group col-md-6">
		            <label for="skype" class="control-label"><i class="fa fa-skype"></i> <?php echo _l('staff_add_edit_skype'); ?></label>
		            <input type="text" class="form-control" name="skype" value="<?php echo html_entity_decode($candidate->skype); ?>">
		         </div>
         	</div>
         	<div class="row">
		         <div class="col-md-4">
		             <?php 
		             $birthday = (isset($candidate) ? $candidate->birthday : ''); 
		             echo render_date_input('birthday','birthday',_d($birthday)); ?>
		         </div>
		         <div class="col-md-4">
		            <?php
		             $birthplace = (isset($candidate) ? $candidate->birthplace : '');
		             echo render_input('birthplace','birthplace',$birthplace,'text'); ?> 
		         </div>
		         <div class="col-md-4">
		            <?php 
		            $home_town = (isset($candidate) ? $candidate->home_town : '');
		            echo render_input('home_town','home_town',$home_town,'text'); ?> 
		         </div>
         	</div>
         	<div class="row">
	            <div class="col-md-4">
	                 <label for="marital_status" class="control-label"><?php echo _l('marital_status'); ?></label>
	            <select name="marital_status" class="selectpicker" id="marital_status" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
	               <option value=""></option>                  
	               <option value="<?php echo 'single'; ?>" <?php if(isset($candidate) && $candidate->marital_status == 'single'){echo 'selected';} ?>><?php echo _l('single'); ?></option>
	               <option value="<?php echo 'married'; ?>" <?php if(isset($candidate) && $candidate->marital_status == 'married'){echo 'selected';} ?>><?php echo _l('married'); ?></option>
	            </select>
	            </div>
	            <div class="col-md-4">
	                <?php
	                 $nation = (isset($candidate) ? $candidate->nation : '');
	                 echo render_input('nation','nation',$nation,'text'); ?>
	            </div>
	            <div class="col-md-4">
	                <?php 
	                 $religion = (isset($candidate) ? $candidate->religion : '');
	                echo render_input('religion','religion',$religion,'text'); ?>
	            </div>
        	</div>
        	<div class="row">
	            <div class="col-md-4">
	                <?php 
	                $identification = (isset($candidate) ? $candidate->identification : '');
	                echo render_input('identification','identification',$identification,'text'); ?>
	            </div>
	            <div class="col-md-4">
	                <?php
	                $days_for_identity = (isset($candidate) ? $candidate->days_for_identity : '');
	                echo render_date_input('days_for_identity','days_for_identity',_d($days_for_identity)); ?>
	            </div>
	            <div class="col-md-4">
	                <?php
	                $place_of_issue = (isset($candidate) ? $candidate->place_of_issue : '');
	                echo render_input('place_of_issue','place_of_issue',$place_of_issue, 'text'); ?>
	            </div>
        	</div>
        	<div class="row">
	            <div class="col-md-4">
	                <?php 
	                $resident = (isset($candidate) ? $candidate->resident : '');
	                echo render_input('resident','resident',$resident,'text'); ?>
	            </div>
	            <div class="col-md-4">
	                <?php 
	                $current_accommodation = (isset($candidate) ? $candidate->current_accommodation : '');
	                echo render_input('current_address','current_address',$current_accommodation,'text'); ?>
	            </div>
	            <div class="col-md-4">
	                <?php
	                 echo render_input('literacy','literacy','','text'); ?>
	            </div>
        	</div>

             <input type="password" class="form-control password hide" name="password" value="123456a@" autocomplete="off">

             <?php if(get_option('disable_language') == 0){ ?>
             	<div class="row">
             		<div class="form-group select-placeholder col-md-12">
             			<label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?><i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('staff_email_signature_help'); ?>"></i></label>
             			<select name="default_language" data-live-search="true" id="default_language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
             				<option value=""><?php echo _l('system_default_string'); ?></option>
             				<?php foreach($this->app->get_available_languages() as $availableLanguage){
             					$selected = '';

             					?>
             					<option value="<?php echo html_entity_decode($availableLanguage); ?>" <?php echo html_entity_decode($selected); ?>><?php echo ucfirst($availableLanguage); ?></option>
             				<?php } ?>
             			</select>
             		</div>
             	</div>

             <?php } ?>
          </div>
          <div role="tabpanel" class="tab-pane" id="staff_permissions">
             <?php
                hooks()->do_action('staff_render_permissions');
                $selected = '';
                foreach($roles as $role){
                
                    $default_staff_role = get_option('default_staff_role');
                    if($default_staff_role == $role['roleid'] ){
                     $selected = $role['roleid'];
                    }
                }
                ?>
             <?php echo render_select('role',$roles,array('roleid','name'),'staff_add_edit_role',$selected); ?>
             <hr />
             <h4 class="font-medium mbot15 bold"><?php echo _l('staff_add_edit_permissions'); ?></h4>
             <?php
             $permissionsData = [ 'funcData' => ['staff_id'=> '' ] ];
             
             $this->load->view('admin/staff/permissions', $permissionsData);
             ?>
          
          </div>

        </div>
        <div class=" text-right btn-toolbar-container-out">
             <button type="submit" onclick="action_transfer_hr()"  class="btn btn-info"><?php echo _l('transfer'); ?></button>
        </div>
        <?php echo form_close(); ?>
     </div>
   </div>
   </div>
 </div>
</div>
<?php init_tail(); ?>