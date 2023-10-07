<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>


<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-7">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                    <?php echo $title; ?>
                </h4>
                <?php echo form_open_multipart($this->uri->uri_string(), ['id' => 'staff_profile_table', 'autocomplete' => 'off']); ?>

                <div class="panel_s">
                    <div class="panel-body"> 
                        <ul class="nav nav-tabs" id="staffEditTabs" role="tablist">
                            <li class="nav-item active">
                                <a class="nav-link " id="personal-tab" data-toggle="tab" href="#personal" role="tab" aria-controls="personal" aria-selected="true">Staff Information</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Other Information</a>
                            </li>
                        </ul>
                        <div class="tab-content mt-3" id="staffEditTabsContent">
                            <div class="tab-pane active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                                <?php if ($current_user->profile_image == null) { ?>
                                <div class="form-group">
                                    <label for="profile_image"
                                        class="profile-image"><?php echo _l('staff_edit_profile_image'); ?></label>
                                    <input type="file" name="profile_image" class="form-control" id="profile_image">
                                </div>
                                <?php } ?>
                                <?php if ($current_user->profile_image != null) { ?>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <?php echo staff_profile_image($current_user->staffid, ['img', 'img-responsive', 'staff-profile-image-thumb'], 'thumb'); ?>
                                        </div>
                                        <div class="col-md-3 text-right">
                                            <a href="<?php echo admin_url('staff/remove_staff_profile_image'); ?>"><i
                                                    class="fa fa-remove"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="firstname" class="control-label"><?php echo _l('staff_add_edit_firstname'); ?></label>
                                            <input type="text" class="form-control" name="firstname" value="<?php if (isset($member)) {
                                                echo $member->firstname;
                                            } ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lastname" class="control-label"><?php echo _l('staff_add_edit_lastname'); ?></label>
                                            <input type="text" class="form-control" name="lastname" value="<?php if (isset($member)) {
                                                echo $member->lastname;
                                            } ?>">
                                        </div>
                                    </div>
                                </div>   
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_of_birth" class="control-label">Date Of Birth</label>
                                            <input type="date" class="form-control" name="date_of_birth" value="<?php if (isset($member)) { echo $member->date_of_birth; } ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="Address" class="control-label">Address</label>
                                            <textarea class="form-control" name="Address" rows="1"><?php if (isset($member)) { echo $member->Address; } ?></textarea>
                                        </div>                              
                                    </div>
                                </div>              
                                <div class="form-group">
                                    <label for="email" class="control-label"><?php echo _l('staff_add_edit_email'); ?></label>
                                    <input type="email" <?php if (has_permission('staff', '', 'edit')) { ?> name="email"
                                        <?php } else { ?> disabled="true" <?php } ?> class="form-control"
                                        value="<?php echo $member->email; ?>" id="email">
                                </div>
                                <div class="form-group">
                                    <label for="staff_salary" class="control-label">Staff Salary:</label>
                                    <input type="number" class="form-control" name="staff_salary" value="<?php if (isset($member)) { echo $member->staff_salary; } ?>">
                                </div>  
                                <?php $value = (isset($member) ? $member->phonenumber : ''); ?>
                                <?php echo render_input('phonenumber', 'staff_add_edit_phonenumber', $value); ?>
                                <div class="form-group">
                                    <label for="staff_title" class="control-label">Title:</label>
                                    <input type="text" class="form-control" name="staff_title" value="<?php if (isset($member)) { echo $member->staff_title; } ?>">
                                </div>
                                <?php if (count($staff_departments) > 0) { ?>
                                    <div class="form-group">
                                        <label for="departments"><?php echo _l('staff_edit_profile_your_departments'); ?></label>
                                        <div class="clearfix"></div>
                                        <?php foreach ($departments as $department) { ?>
                                            <?php foreach ($staff_departments as $staff_department) {
                                                if ($staff_department['departmentid'] == $department['departmentid']) { ?>
                                                    <!-- Disabled input field for the department -->
                                                    <input type="text" class="form-control" value="<?php echo $staff_department['name']; ?>" disabled>
                                                <?php }
                                            } ?>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <div class="form-group select-placeholder">
                                    <label for="report_to">Staff Report to :</label>
                                    <select class="selectpicker"
                                        data-live-search="true"
                                        data-none-selected-text="<?php echo _l('system_default_string'); ?>" data-width="100%"
                                        name="report_to" id="report_to" disabled>
                                        <?php foreach($staff_members as $staff): ?>
                                            <option value="<?php echo $staff['staffid']; ?>" 
                                                <?php 
                                                if (isset($member) && $member->staffid == $staff['staffid']) {
                                                    echo 'disabled';
                                                }
                                                if (isset($member) && $member->report_to == $staff['staffid']) {
                                                    echo 'selected';
                                                }
                                                ?>>
                                                <?php echo $staff['firstname'] . ' ' . $staff['lastname']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php if (!is_language_disabled()) { ?>
                                <div class="form-group select-placeholder">
                                    <label for="default_language"
                                        class="control-label"><?php echo _l('localization_default_language'); ?></label>
                                    <select name="default_language" data-live-search="true" id="default_language"
                                        class="form-control selectpicker"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value=""><?php echo _l('system_default_string'); ?></option>
                                        <?php foreach ($this->app->get_available_languages() as $availableLanguage) {
                                            $selected = '';
                                            if (isset($member)) {
                                                if ($member->default_language == $availableLanguage) {
                                                    $selected = 'selected';
                                                }
                                            } ?>
                                        <option value="<?php echo $availableLanguage; ?>" <?php echo $selected; ?>>
                                            <?php echo ucfirst($availableLanguage); ?></option>
                                        <?php
                                            } ?>
                                    </select>
                                </div>
                                <?php } ?>
                                <i class="fa-regular fa-circle-question" data-toggle="tooltip"
                                    data-title="<?php echo _l('staff_email_signature_help'); ?>"></i>
                                <?php $value = (isset($member) ? $member->email_signature : ''); ?>
                                <?php echo render_textarea('email_signature', 'settings_email_signature', $value, ['data-entities-encode' => 'true']); ?>
                                <div class="form-group select-placeholder">
                                    <label for="direction"><?php echo _l('document_direction'); ?></label>
                                    <select class="selectpicker"
                                        data-none-selected-text="<?php echo _l('system_default_string'); ?>" data-width="100%"
                                        name="direction" id="direction">
                                        <option value="" <?php if (isset($member) && empty($member->direction)) {
                                                echo 'selected';
                                            } ?>></option>
                                        <option value="ltr" <?php if (isset($member) && $member->direction == 'ltr') {
                                                echo 'selected';
                                            } ?>>LTR</option>
                                        <option value="rtl" <?php if (isset($member) && $member->direction == 'rtl') {
                                                echo 'selected';
                                            } ?>>RTL</option>
                                    </select>
                                </div>
                            </div>
                       

                            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                <div class="form-group">
                                    <label for="personal_email_address" class="control-label">Personal Email Address</label>
                                    <input type="email" class="form-control" name="personal_email_address" value="<?php if (isset($member)) { echo $member->personal_email_address; } ?>">
                                </div>
                                <div class="form-group">
                                    <label for="CNIC_Number" class="control-label">CNIC Number</label>
                                    <input type="text" class="form-control" name="CNIC_Number" value="<?php if (isset($member)) { echo $member->CNIC_Number; } ?>">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="emergency_Contact_name" class="control-label">Emergency Contact Name:</label>
                                            <input type="text" class="form-control" name="emergency_Contact_name" value="<?php if (isset($member)) { echo $member->emergency_Contact_name; } ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">                         
                                        <div class="form-group">
                                            <label for="emergency_contact_number" class="control-label">Emergency Contact Number:</label>
                                            <input type="text" class="form-control" name="emergency_contact_number" value="<?php if (isset($member)) { echo $member->emergency_contact_number; } ?>">
                                        </div>
                                    </div>
                                </div>                                                           
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bank_name" class="control-label">Bank Name </label>
                                            <input type="text" class="form-control" name="bank_name" value="<?php if (isset($member)) { echo $member->bank_name; } ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bank_acc_no" class="control-label">Bank Account Number</label>
                                            <input type="text" class="form-control" name="bank_acc_no" value="<?php if (isset($member)) { echo $member->bank_acc_no; } ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Next_of_KIN" class="control-label">Next To Kin</label>
                                    <input type="text" <?php if (has_permission('staff', '', 'edit')) { ?> name="Next_of_KIN"
                                        <?php } else { ?> disabled="true" <?php } ?> class="form-control"
                                        value="<?php echo $member->Next_of_KIN; ?>" id="Next_of_KIN">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group select-placeholder">
                                            <label for="Marital_Status">Marital Status:</label>
                                            <select class="selectpicker"
                                                data-live-search="true"
                                                data-none-selected-text="<?php echo _l('system_default_string'); ?>" data-width="100%"
                                                name="Marital_Status" id="Marital_Status">
                                                <option value="Single" <?php if(isset($member) && $member->Marital_Status == 'Single') { echo 'selected'; } ?>>Single</option>
                                                <option value="Married" <?php if(isset($member) && $member->Marital_Status == 'Married') { echo 'selected'; } ?>>Married</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                    <div class="form-group select-placeholder">
                                    <label for="gender">Gender:</label>
                                    <select class="selectpicker"
                                        data-live-search="true"
                                        data-none-selected-text="<?php echo _l('system_default_string'); ?>" data-width="100%"
                                        name="gender" id="gender">
                                        <option value="Male" <?php if(isset($member) && $member->gender == 'Male') { echo 'selected'; } ?>>Male</option>
                                        <option value="Female" <?php if(isset($member) && $member->gender == 'Female') { echo 'selected'; } ?>>Female</option>
                                        <option value="Other" <?php if(isset($member) && $member->gender == 'Other') { echo 'selected'; } ?>>Other</option>
                                    </select>
                                </div>
                                    </div>
                                </div>                         
                                <div class="form-group">
                                    <label for="google_chat_id" class="control-label">Google Chat ID</label>
                                    <input type="text" <?php if (has_permission('staff', '', 'edit')) { ?> name="google_chat_id"
                                        <?php } else { ?> disabled="true" <?php } ?> class="form-control"
                                        value="<?php echo $member->google_chat_id; ?>" id="google_chat_id">
                                </div>                                
                                <div class="form-group">
                                    <label for="facebook" class="control-label"><i class="fa-brands fa-facebook-f"></i>
                                        <?php echo _l('staff_add_edit_facebook'); ?></label>
                                    <input type="text" class="form-control" name="facebook" value="<?php if (isset($member)) {
                                                echo $member->facebook;
                                            } ?>">
                                </div>
                                <div class="form-group">
                                    <label for="linkedin" class="control-label"><i class="fa-brands fa-linkedin-in"></i>
                                        <?php echo _l('staff_add_edit_linkedin'); ?></label>
                                    <input type="text" class="form-control" name="linkedin" value="<?php if (isset($member)) {
                                        echo $member->linkedin;
                                    } ?>">
                                </div>
                                <div class="form-group">
                                    <label for="skype" class="control-label"><i class="fa-brands fa-skype"></i>
                                        <?php echo _l('staff_add_edit_skype'); ?></label>
                                    <input type="text" class="form-control" name="skype" value="<?php if (isset($member)) {
                                        echo $member->skype;
                                    } ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer text-right">
                        <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                    </div>
                </div>
                <?php echo form_close(); ?>




            </div>


            <div class="col-md-5">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                    <?php echo _l('staff_edit_profile_change_your_password'); ?>
                </h4>
                <?php echo form_open('admin/staff/change_password_profile', ['id' => 'staff_password_change_form']); ?>

                <div class="panel_s">

                    <div class="panel-body">

                        <div class="form-group">
                            <label for="oldpassword"
                                class="control-label"><?php echo _l('staff_edit_profile_change_old_password'); ?></label>
                            <input type="password" class="form-control" name="oldpassword" id="oldpassword">
                        </div>
                        <div class="form-group">
                            <label for="newpassword"
                                class="control-label"><?php echo _l('staff_edit_profile_change_new_password'); ?></label>
                            <input type="password" class="form-control" id="newpassword" name="newpassword">
                        </div>
                        <div class="form-group">
                            <label for="newpasswordr"
                                class="control-label"><?php echo _l('staff_edit_profile_change_repeat_new_password'); ?></label>
                            <input type="password" class="form-control" id="newpasswordr" name="newpasswordr">
                        </div>
                    </div>

                    <div class="panel-footer">
                        <div class="tw-flex tw-justify-between">
                            <span>
                                <?php if ($member->last_password_change != null) { ?>
                                <?php echo _l('staff_add_edit_password_last_changed'); ?>:
                                <span class="text-has-action" data-toggle="tooltip"
                                    data-title="<?php echo _dt($member->last_password_change); ?>">
                                    <?php echo time_ago($member->last_password_change); ?>
                                </span>
                                <?php } ?>
                            </span>
                            <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
                <h4 class="tw-font-semibold tw-text-lg tw-text-neutral-700 mtop30">
                    <?php echo _l('staff_two_factor_authentication'); ?>
                </h4>
                <?php echo form_open('admin/staff/update_two_factor', ['id' => 'two_factor_auth_form']); ?>

                <div class="panel_s">
                    <div class="panel-body">
                        <div class="radio radio-primary">
                            <input type="radio" id="two_factor_auth_disabled" name="two_factor_auth" value="off"
                                class="custom-control-input"
                                <?php echo ($current_user->two_factor_auth_enabled == 0) ? 'checked' : '' ?>>
                            <label class="custom-control-label"
                                for="two_factor_auth_disabled"><?php echo _l('two_factor_authentication_disabed'); ?></label>
                        </div>
                        <?php if (is_email_template_active('two-factor-authentication')) { ?>
                        <div class="radio radio-primary">
                            <input type="radio" id="two_factor_auth_enabled" name="two_factor_auth" value="email"
                                class="custom-control-input"
                                <?php echo ($current_user->two_factor_auth_enabled == 1) ? 'checked' : '' ?>>
                            <label for="two_factor_auth_enabled">
                                <i class="fa-regular fa-circle-question" data-placement="right" data-toggle="tooltip"
                                    data-title="<?php echo _l('two_factor_authentication_info'); ?>"></i>
                                <?php echo _l('enable_two_factor_authentication'); ?>
                            </label>
                        </div>
                        <?php } ?>
                        <div class="radio radio-primary">
                            <input type="radio" id="google_two_factor_auth_enabled" name="two_factor_auth"
                                value="google" class="custom-control-input"
                                <?php echo ($current_user->two_factor_auth_enabled == 2) ? 'checked' : '' ?>>
                            <label class="custom-control-label"
                                for="google_two_factor_auth_enabled"><?php echo _l('enable_google_two_factor_authentication'); ?></label>
                        </div>
                        <div id="qr_image" class=" mtop30 card">
                        </div>


                    </div>
                    <div class="panel-footer text-right">
                        <button id="submit_2fa" type="submit" class="btn btn-primary">
                            <?php echo _l('submit'); ?>
                        </button>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <?php init_tail(); ?>
    <script>
document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.nav-link');
        const contents = document.querySelectorAll('.tab-pane');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function(event) {
                event.preventDefault();
                document.getElementById('personal-tab').click();


                // Remove active class from all tabs
                tabs.forEach(innerTab => innerTab.classList.remove('active'));
                // Hide all contents
                contents.forEach(content => content.classList.remove('show', 'active'));

                // Add active class to clicked tab
                tab.classList.add('active');

                // Display the content that corresponds to the clicked tab
                const contentId = tab.getAttribute('href');
                document.querySelector(contentId).classList.add('show', 'active');
            });
        });
    });



    $(function() {
        var qr_loaded = 0;
        var is_g2fa_enabled = "<?php echo $current_user->two_factor_auth_enabled ?>"
        $('input[type=radio][name="two_factor_auth"]').change(function() {
            if (this.value == 'google') {
                if (is_g2fa_enabled == 2) {
                    return;
                }

                if (qr_loaded == 0) {
                    $('#qr_image').load(admin_url + 'authentication/get_qr', {}, function(response,
                        status) {
                        qr_loaded = 1;
                        $('#qr_image').show();
                    });
                } else {
                    $('#qr_image').show();
                }
                $('#submit_2fa').prop("disabled", true);
            } else {
                $('#qr_image').hide();
                $('#submit_2fa').prop("disabled", false);
            }
        });
        appValidateForm($('#staff_profile_table'), {
            firstname: 'required',
            lastname: 'required',
            email: 'required'
        });
        appValidateForm($('#staff_password_change_form'), {
            oldpassword: 'required',
            newpassword: 'required',
            newpasswordr: {
                equalTo: "#newpassword"
            }
        });
        appValidateForm($('#two_factor_auth_form'), {
            two_factor_auth: 'required'
        });
    });


    </script>
    </body>

    </html>
