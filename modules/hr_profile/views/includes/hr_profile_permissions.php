<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(has_permission('hrm_setting', '', 'create')){ ?>
<a href="#" onclick="hr_profile_permissions_update(0,0,' hide'); return false;" class="btn btn-info mbot10"><?php echo _l('hr_hr_add'); ?></a>
<?php } ?>
<table class="table table-hr-profile-permission">
  <thead>
    <th><?php echo _l('hr_hr_staff_name'); ?></th>
    <th><?php echo _l('role'); ?></th>
    <th><?php echo _l('staff_dt_email'); ?></th>
    <th><?php echo _l('phone'); ?></th>
    <th><?php echo _l('options'); ?></th>
  </thead>
  <tbody>
  </tbody>
</table>
<div id="modal_wrapper"></div>

