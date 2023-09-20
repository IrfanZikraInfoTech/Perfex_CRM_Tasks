<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" >
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if($email_exist_as_staff){ ?>
                    <div class="alert alert-danger">
                     Some of the departments email is used as staff member email, according to the docs, the support department email must be unique email in the system, you must change the staff email or the support department email in order all the features to work properly.
                 </div>
             <?php } ?>
             <div class="panel_s">
                <div class="panel-body">
                    <div class="row">
                        <div class="_buttons col-md-8">
                            <?php if(is_admin() || has_permission('staffmanage_orgchart','','create')){ ?>
                                <a href="#" onclick="new_department(); return false;" class=" mright5 btn btn-info pull-left display-block">
                                    <?php echo _l('hr_new_unit'); ?>
                                </a>
                            <?php } ?>
                            <a href="#" onclick="view_department_chart(); return false;" class="mright5 btn btn-default pull-left display-block">
                                <?php echo _l('hr_view_department_chart'); ?>
                            </a>
                        </div>
                        <div class="col-md-4 leads-filter-column">
                            <input type="text" id="dep_tree" name="dep_tree" class="selectpicker" placeholder="<?php echo _l('filter_by'); ?>" autocomplete="off">
                            <input type="hidden" name="dept" id="dept"/>
                        </div>
                    </div>


                    <div class="clearfix"></div>
                    <hr class="hr-panel-heading" />
                    <div class="clearfix"></div>
                    <?php render_datatable(array(
                        _l('hr_hr_id'),
                        _l('department_list_name'),
                        _l('hr_parent_unit'),
                        _l('hr_manager_unit'),
                        _l('hr_unit_email'),
                        _l('department_calendar_id'),                        
                        _l('options')
                    ),'departments'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<div class="modal fade" id="department" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('hr_edit_unit'); ?></span>
                    <span class="add-title"><?php echo _l('hr_new_unit'); ?></span>
                </h4>
            </div>
            <?php echo form_open(admin_url('hr_profile/department')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
                        <input  type="text" class="fake-autofill-field" name="fakeusernameremembered" value='' tabindex="-1"/>
                        <input  type="password" class="fake-autofill-field" name="fakepasswordremembered" value='' tabindex="-1"/>
                        <?php echo render_input('name','unit_name'); ?>
                        <?php if(get_option('google_api_key') != ''){ ?>
                            <?php echo render_input('calendar_id','department_calendar_id'); ?>
                        <?php } ?>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="hidefromclient" id="hidefromclient">
                            <label for="hidefromclient"><?php echo _l('department_hide_from_client'); ?></label>
                        </div>
                        <hr />
                        <?php echo render_select('manager_id', $list_staff,array('staffid','full_name'),'hr_manager_unit'); ?>
                        <?php echo render_select('parent_id',$list_department ,array('departmentid','name'),'hr_parent_unit'); ?>
                        <?php echo render_input('email','hr_unit_email','','email'); ?>
                        <i class="fa fa-question-circle" data-toggle="tooltip"  data-title="<?php echo _l('department_username_help'); ?>"></i>
                        <?php echo render_input('imap_username','department_username'); ?>
                        <?php echo render_input('host','dept_imap_host'); ?>
                        <?php echo render_input('password','dept_email_password','','password'); ?>
                        <div class="form-group">
                            <label for="encryption"><?php echo _l('dept_encryption'); ?></label><br />
                            <div class="radio radio-primary radio-inline">
                                <input type="radio" name="encryption" value="tls" id="tls">
                                <label for="tls">TLS</label>
                            </div>
                            <div class="radio radio-primary radio-inline">
                                <input type="radio" name="encryption" value="ssl" id="ssl">
                                <label for="ssl">SSL</label>
                            </div>
                            <div class="radio radio-primary radio-inline">
                                <input type="radio" name="encryption" value="" id="no_enc" checked>
                                <label for="no_enc"><?php echo _l('dept_email_no_encryption'); ?></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" name="delete_after_import" id="delete_after_import">
                                <label for="delete_after_import"><?php echo _l('delete_mail_after_import'); ?>
                            </div>
                            <hr />
                            <button onclick="test_dep_imap_connection(); return false;" class="btn btn-default"><?php echo _l('leads_email_integration_test_connection'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>

            </div>
            <?php echo form_close(); ?>                 
        </div>
    </div>
</div>

<!-- view chart in sidebar start -->
<div class="modal fade" id="department_chart_view" tabindex="-1" role="dialog">
    <div class="modal-dialog organizational_chart_dialog w-100 h-100">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('hr_organizational_chart'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="dp_chart">
                        <div id="department_chart"></div>
                    </div>
                </div>
            </div>                 
        </div>
    </div>
</div>
<!-- view chart in sidebar end -->
<?php init_tail(); ?>
<?php require('modules/hr_profile/assets/js/organizational/organizational_js.php'); ?>
</body>
</html>

