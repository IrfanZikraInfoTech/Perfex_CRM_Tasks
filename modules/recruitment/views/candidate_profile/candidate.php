<?php init_head();?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <div class="col-md-12">
                      <h4 class="no-margin font-bold"> <?php echo _l($title); ?></h4>
                      <hr />
                    </div>
                  </div>
                  <?php if (isset($candidate)) {

	echo form_hidden('candidateid', $candidate->id);
	echo form_open_multipart(admin_url('recruitment/add_update_candidate/' . $candidate->id), array('id' => 'recruitment-candidate-form'));} else {
	echo form_open_multipart(admin_url('recruitment/add_update_candidate'), array('id' => 'recruitment-candidate-form'));}?>


                  <div class="col-md-7">
                    <div class="panel panel-primary">
                      <div class="panel-heading"><?php echo _l('general_infor') ?></div>
                      <div class="panel-body">

                        <div class="col-md-12">
                          <label for="rec_campaign"><?php echo _l('recruitment_campaign'); ?></label>
                          <select name="rec_campaign" id="rec_campaign" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                            <option value=""></option>
                            <?php foreach ($rec_campaigns as $s) {?>
                              <option value="<?php echo html_entity_decode($s['cp_id']); ?>" <?php if (isset($candidate) && $s['cp_id'] == $candidate->rec_campaign) {echo 'selected';}?>><?php echo html_entity_decode($s['campaign_code'] . ' - ' . $s['campaign_name']); ?></option>
                              <?php }?>
                          </select>
                          <br><br>
                        </div>

                        <div class="col-md-12">
                          <?php $candidate_code = (isset($candidate) ? $candidate->candidate_code : '');
echo render_input('candidate_code', 'candidate_code', $candidate_code)?>
                        </div>

                        <div class="col-md-6">
                          <?php $candidate_name = (isset($candidate) ? $candidate->candidate_name : '');
echo render_input('candidate_name', 'first_name', $candidate_name)?>
                        </div>

                        <div class="col-md-6">
                          <?php $last_name = (isset($candidate) ? $candidate->last_name : '');
echo render_input('last_name', 'last_name', $last_name)?>
                        </div>

                        <div class="col-md-4">
                          <?php $birthday = (isset($candidate) ? _d($candidate->birthday) : '');
echo render_date_input('birthday', 'birthday', $birthday)?>
                        </div>

                        <div class="col-md-4">
                          <label for="gender"><?php echo _l('gender'); ?></label>
                          <select name="gender" id="gender" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                            <option value=""></option>
                            <option value="male" <?php if (isset($candidate) && $candidate->gender == 'male') {echo 'selected';}?>><?php echo _l('male'); ?></option>
                            <option value="female" <?php if (isset($candidate) && $candidate->gender == 'female') {echo 'selected';}?>><?php echo _l('female'); ?></option>
                          </select>
                          <br><br>
                        </div>

                        <div class="col-md-4">
                          <?php $arrAtt = array();
                          $arrAtt['data-type'] = 'currency';
                          $desired_salary = (isset($candidate) ? app_format_money($candidate->desired_salary, '') : '');
                          ?>

                          <div class="form-group">
                            <label><?php echo _l('desired_salary'); ?></label>
                            <div class="input-group">
                              <input type="text" class="form-control text-right" name="desired_salary" value="<?php echo html_entity_decode($desired_salary) ?>" data-type="currency">

                              <div class="input-group-addon">
                                <div class="dropdown">
                                 <span class="discount-type-selected">
                                  <?php echo html_entity_decode($base_currency->name) ;?>
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>

                        </div>

                        <div class="col-md-6">
                          <?php $birthplace = (isset($candidate) ? $candidate->birthplace : '');
echo render_textarea('birthplace', 'birthplace', $birthplace)?>
                        </div>

                        <div class="col-md-6">
                          <?php $home_town = (isset($candidate) ? $candidate->home_town : '');
echo render_textarea('home_town', 'home_town', $home_town)?>
                        </div>

                        <div class="col-md-4">
                            <?php $identification = (isset($candidate) ? $candidate->identification : '');
echo render_input('identification', 'identification', $identification);?>
                        </div>
                        <div class="col-md-4">
                            <?php $days_for_identity = (isset($candidate) ? _d($candidate->days_for_identity) : '');
echo render_date_input('days_for_identity', 'days_for_identity', $days_for_identity);?>
                        </div>
                        <div class="col-md-4">
                            <?php $place_of_issue = (isset($candidate) ? $candidate->place_of_issue : '');
echo render_input('place_of_issue', 'place_of_issue', $place_of_issue);?>
                        </div>

                        <div class="col-md-4">
                             <label for="marital_status" class="control-label"><?php echo _l('marital_status'); ?></label>
                        <select name="marital_status" class="selectpicker" id="marital_status" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <option value=""></option>
                           <option value="<?php echo 'single'; ?>" <?php if (isset($candidate) && $candidate->marital_status == 'single') {echo 'selected';}?> ><?php echo _l('single'); ?></option>
                           <option value="<?php echo 'married'; ?>" <?php if (isset($candidate) && $candidate->marital_status == 'married') {echo 'selected';}?>  ><?php echo _l('married'); ?></option>
                        </select>
                        </div>
                        <div class="col-md-4">
                            <?php $nationality = (isset($candidate) ? $candidate->nationality : '');
echo render_input('nationality', 'nationality', $nationality);?>
                        </div>
                        <div class="col-md-4">
                            <?php $nation = (isset($candidate) ? $candidate->nation : '');
echo render_input('nation', 'nation', $nation);?>
                        </div>
                        <div class="col-md-4">
                            <?php $religion = (isset($candidate) ? $candidate->religion : '');
echo render_input('religion', 'religion', $religion);?>
                        </div>
                        <div class="col-md-4">
                            <?php $height = (isset($candidate) ? $candidate->height : '');
echo render_input('height', 'height', $height);?>
                        </div>
                        <div class="col-md-4">
                            <?php $weight = (isset($candidate) ? $candidate->weight : '');
echo render_input('weight', 'weight', $weight);?>
                        </div>

                        <div class="col-md-12">

                          <?php $introduce_yourself = (isset($candidate) ? $candidate->introduce_yourself : '');

                          $rows=[];
                          $rows['rows'] = 12;
echo render_textarea('introduce_yourself', 'introduce_yourself', $introduce_yourself, $rows)?>
                        </div>


                        <div class="col-md-12">
                           <div class="form-group">
                            <label for="skill[]" class="control-label"><?php echo _l('skill_name'); ?></label> 
                            <select name="skill[]" id="skill" data-live-search="true" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                          <?php if(isset($candidate->skill)){ $skill_id = explode(',', $candidate->skill);} ; ?>

                              <?php foreach($skills as $dpkey =>  $skill){ ?>
                              <option value="<?php echo html_entity_decode($skill['id']); ?>"  <?php if(isset($skill_id) && in_array($skill['id'], $skill_id) == true ){echo 'selected';} ?>> <?php echo html_entity_decode($skill['skill_name']); ?></option>                  
                              <?php }?>
                            </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                      

                            <?php $interests = (isset($candidate) ? $candidate->interests : '');
                            echo render_textarea('interests', 'interests', $interests)?>
                          
                        </div>

                      </div>
                    </div>
                  </div>

                  <div class="col-md-5">
                    <div class="panel panel-info">
                      <div class="panel-heading"><?php echo _l('contact_info') ?></div>
                      <div class="panel-body">

                        <div class="col-md-6">
                            <?php $phonenumber = (isset($candidate) ? $candidate->phonenumber : '');
echo render_input('phonenumber', 'phonenumber', $phonenumber);?>
                        </div>

                         <div class="col-md-6">
                            <?php $alternate_contact_number = (isset($candidate) ? $candidate->alternate_contact_number : '');
echo render_input('alternate_contact_number', 'alternate_contact_number', $alternate_contact_number);?>
                        </div>
                        
                        <div class="col-md-6">
                            <?php $email = (isset($candidate) ? $candidate->email : '');
echo render_input('email', 'email', $email);?>
                        </div>

                        <div class="col-md-6">
                            <?php $skype = (isset($candidate) ? $candidate->skype : '');
echo render_input('skype', 'skype', $skype);?>
                        </div>
                        <div class="col-md-6">
                            <?php $facebook = (isset($candidate) ? $candidate->facebook : '');
echo render_input('facebook', 'facebook', $facebook);?>
                        </div>

                        <div class="col-md-6">
                            <?php $linkedin = (isset($candidate) ? $candidate->linkedin : '');
echo render_input('linkedin', 'linkedin', $linkedin);?>
                        </div>
                        
                         <div class="col-md-12">
                            <?php $resident = (isset($candidate) ? $candidate->resident : '');
echo render_textarea('resident', 'resident', $resident)?>
                        </div>
                        <div class="col-md-12">
                            <?php $current_accommodation = (isset($candidate) ? $candidate->current_accommodation : '');
echo render_textarea('current_accommodation', 'current_accommodation', $current_accommodation)?>

                        </div>
                        <div class="col-md-12 pull-left">
                           <div class="container">
                              <div class="picture-container pull-left">
                                  <div class="picture pull-left">
                                      <img src="<?php if (isset($candidate->avar)) {echo site_url(RECRUITMENT_PATH . 'candidate/avartar/' . $candidate->id . '/' . $candidate->avar->file_name);} else {echo site_url(RECRUITMENT_PATH . 'none_avatar.jpg');}?>" class="picture-src" id="wizardPicturePreview" title="">
                                      <input name="cd_avar" type="file" id="wizard-picture" accept=".png, .jpg, .jpeg" class="">
                                  </div>

                                   <h5 class=""><?php echo _l('choose_picture'); ?></h5>

                              </div>
                          </div>
                        </div>
                         <div class="col-md-12">
                          <hr>
                            <?php echo render_input('file', 'file_campaign', '', 'file') ?>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="panel panel-success">
                      <div class="panel-heading"><?php echo _l('work_experience') ?></div>
                      <div class="panel-body">
                        <div class="work_experience">
                        <?php if (isset($candidate) && count($candidate->work_experience) > 0) {
	foreach ($candidate->work_experience as $key => $val) {
		?>
                          <div class="row col-md-12" id="work_experience-item">
                            <div class="col-md-3">
                            <?php $from_date = _d($val['from_date']);
		echo render_date_input('from_date[' . $key . ']', 'from_date', $from_date);?>
                            </div>

                            <div class="col-md-3">
                             <?php $to_date = _d($val['to_date']);
		echo render_date_input('to_date[' . $key . ']', 'to_date', $to_date);?>
                            </div>

                            <div class="col-md-3">
                             <?php $company = $val['company'];
		echo render_input('company[' . $key . ']', 'company', $company)?>
                            </div>

                            <div class="col-md-3">
                             <?php $position = $val['position'];
		echo render_input('position[' . $key . ']', 'position', $position)?>
                            </div>

                            <div class="col-md-3">
                             <?php $contact_person = $val['contact_person'];
		echo render_input('contact_person[' . $key . ']', 'contact_person', $contact_person)?>
                            </div>
                            <div class="col-md-3">
                             <?php $salary = $val['salary'];
		echo render_input('salary[' . $key . ']', 'salary', $salary)?>
                            </div>

                            <div class="col-md-6">
                             <?php $reason_quitwork = $val['reason_quitwork'];
		echo render_input('reason_quitwork[' . $key . ']', 'reason_quitwork', $reason_quitwork)?>
                            </div>

                            <div class="col-md-12">
                             <?php $job_description = $val['job_description'];
		echo render_textarea('job_description[' . $key . ']', 'job_description', $job_description)?>
                            </div>

                            <?php if ($key == 0) {?>
                              <div class="col-md-12 line-height-content">
                                    <span class="input-group-btn pull-bot">
                                        <button name="add" class="btn new_work_experience btn-success border-radius-4" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                                    </span>
                              </div>
                            <?php } else {?>
                              <div class="col-md-12 line-height-content">
                                    <span class="input-group-btn pull-bot">
                                        <button name="add" class="btn remove_work_experience btn-danger border-radius-4" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
                                    </span>
                              </div>
                            <?php }?>
                          </div>

                        <?php }} else {?>
                          <div class="row col-md-12" id="work_experience-item">
                            <div class="col-md-3">
                            <?php echo render_date_input('from_date[0]', 'from_date', ''); ?>
                            </div>

                            <div class="col-md-3">
                             <?php echo render_date_input('to_date[0]', 'to_date', ''); ?>
                            </div>

                            <div class="col-md-3">
                             <?php echo render_input('company[0]', 'company') ?>
                            </div>

                            <div class="col-md-3">
                             <?php echo render_input('position[0]', 'position') ?>
                            </div>

                            <div class="col-md-3">
                             <?php echo render_input('contact_person[0]', 'contact_person') ?>
                            </div>
                            <div class="col-md-3">
                             <?php echo render_input('salary[0]', 'salary') ?>
                            </div>

                            <div class="col-md-6">
                             <?php echo render_input('reason_quitwork[0]', 'reason_quitwork') ?>
                            </div>

                            <div class="col-md-12">

                             <p class="bold"><?php echo _l('job_description'); ?></p>
                              <?php echo render_textarea('job_description[0]','',''); ?>
                                  

                            </div>

                            <div class="col-md-12 line-height-content">
                                  <span class="input-group-btn pull-bot">
                                      <button name="add" class="btn new_work_experience btn-success border-radius-4" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                                  </span>
                            </div>

                          </div>
                        <?php }?>
                        </div>
                      </div>
                    </div>
                  </div>



                  <div class="col-md-12">
                    <div class="panel panel-default">
                      <div class="panel-heading"><?php echo _l('literacy') ?></div>
                      <div class="panel-body">
                        <div class="list_literacy">
                          <?php if (isset($candidate) && count($candidate->literacy) > 0) {
	foreach ($candidate->literacy as $key => $val) {
		?>
                          <div class="row col-md-12" id="literacy-item">
                            <div class="col-md-2">
                            <?php $literacy_from_date = _d($val['literacy_from_date']);
		echo render_date_input('literacy_from_date[' . $key . ']', 'from_date', $literacy_from_date);?>
                            </div>

                            <div class="col-md-2">
                             <?php $literacy_to_date = _d($val['literacy_to_date']);
		echo render_date_input('literacy_to_date[' . $key . ']', 'to_date', $literacy_to_date);?>
                            </div>

                            <div class="col-md-2">
                             <?php $diploma = $val['diploma'];
		echo render_input('diploma[' . $key . ']', 'diploma', $diploma)?>
                            </div>

                            <div class="col-md-2">
                             <?php $training_places = $val['training_places'];
		echo render_input('training_places[' . $key . ']', 'training_places', $training_places)?>
                            </div>

                            <div class="col-md-2">
                             <?php $specialized = $val['specialized'];
		echo render_input('specialized[' . $key . ']', 'specialized', $specialized)?>
                            </div>
                            <div class="col-md-2">
                             <?php $training_form = $val['training_form'];
		echo render_input('training_form[' . $key . ']', 'training_form', $training_form)?>
                            </div>
                            <?php if ($key == 0) {?>
                              <div class="col-md-12 line-height-content">
                                  <span class="input-group-btn pull-bot">
                                      <button name="add" class="btn new_literacy btn-success border-radius-4" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                                  </span>
                            </div>
                            <?php } else {?>
                              <div class="col-md-12 line-height-content">
                                    <span class="input-group-btn pull-bot">
                                        <button name="add" class="btn remove_literacy btn-danger border-radius-4" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
                                    </span>
                              </div>
                            <?php }?>


                          </div>
                        <?php }} else {?>
                          <div class="row col-md-12" id="literacy-item">
                            <div class="col-md-2">
                            <?php echo render_date_input('literacy_from_date[0]', 'from_date', ''); ?>
                            </div>

                            <div class="col-md-2">
                             <?php echo render_date_input('literacy_to_date[0]', 'to_date', ''); ?>
                            </div>

                            <div class="col-md-2">
                             <?php echo render_input('diploma[0]', 'diploma') ?>
                            </div>

                            <div class="col-md-2">
                             <?php echo render_input('training_places[0]', 'training_places') ?>
                            </div>

                            <div class="col-md-2">
                             <?php echo render_input('specialized[0]', 'specialized') ?>
                            </div>
                            <div class="col-md-2">
                             <?php echo render_input('training_form[0]', 'training_form') ?>
                            </div>

                            <div class="col-md-12 line-height-content">
                                  <span class="input-group-btn pull-bot">
                                      <button name="add" class="btn new_literacy btn-success border-radius-4" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                                  </span>
                            </div>

                          </div>
                        <?php }?>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="panel panel-warning">
                      <div class="panel-heading"><?php echo _l('family_infor') ?></div>
                      <div class="panel-body">
                        <div class="list_family_infor">
                          <?php if (isset($candidate) && count($candidate->family_infor) > 0) {
	foreach ($candidate->family_infor as $key => $val) {
		?>
                          <div class="row col-md-12" id="family_infor-item">
                            <div class="col-md-2">
                            <?php $relationship = $val['relationship'];
		echo render_input('relationship[' . $key . ']', 'relationship', $relationship);?>
                            </div>

                            <div class="col-md-2">
                             <?php $name = $val['name'];
		echo render_input('name[' . $key . ']', 'name', $name);?>
                            </div>

                            <div class="col-md-2">
                             <?php $fi_birthday = _d($val['fi_birthday']);
		echo render_date_input('fi_birthday[' . $key . ']', 'birthday', $fi_birthday)?>
                            </div>

                            <div class="col-md-2">
                             <?php $job = $val['job'];
		echo render_input('job[' . $key . ']', 'job', $job)?>
                            </div>

                            <div class="col-md-2">
                             <?php $address = $val['address'];
		echo render_input('address[' . $key . ']', 'address', $address)?>
                            </div>
                            <div class="col-md-2">
                             <?php $phone = $val['phone'];
		echo render_input('phone[' . $key . ']', 'phone', $phone)?>
                            </div>
                            <?php if ($key == 0) {?>
                              <div class="col-md-12 line-height-content">
                                  <span class="input-group-btn pull-bot">
                                      <button name="add" class="btn new_family_infor btn-success border-radius-4" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                                  </span>
                            </div>
                            <?php } else {?>
                              <div class="col-md-12 line-height-content">
                                    <span class="input-group-btn pull-bot">
                                        <button name="add" class="btn remove_family_infor btn-danger border-radius-4" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
                                    </span>
                              </div>
                            <?php }?>
                          </div>
                          <?php }} else {?>
                          <div class="row col-md-12" id="family_infor-item">
                            <div class="col-md-2">
                            <?php echo render_input('relationship[0]', 'relationship', ''); ?>
                            </div>

                            <div class="col-md-2">
                             <?php echo render_input('name[0]', 'name', ''); ?>
                            </div>

                            <div class="col-md-2">
                             <?php echo render_date_input('fi_birthday[0]', 'birthday') ?>
                            </div>

                            <div class="col-md-2">
                             <?php echo render_input('job[0]', 'job') ?>
                            </div>

                            <div class="col-md-2">
                             <?php echo render_input('address[0]', 'address') ?>
                            </div>
                            <div class="col-md-2">
                             <?php echo render_input('phone[0]', 'phone') ?>
                            </div>

                            <div class="col-md-12 line-height-content">
                                  <span class="input-group-btn pull-bot">
                                      <button name="add" class="btn new_family_infor btn-success border-radius-4" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                                  </span>
                            </div>

                          </div>
                          <?php }?>
                          </div>
                        </div>
                      </div>
                    </div>
                     <hr>
                     <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                   <?php echo form_close(); ?>
                  </div>

               </div>
            </div>
         </div>

      </div>
   </div>
<?php init_tail();?>
<script>
$(document).ready(function(){
    $("#wizard-picture").change(function(){
        readURL(this);
    });
});
</script>
</body>
</html>