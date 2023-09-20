<div class="callbacks_assignees_wrapper assigned_users_data">
    <h4 class="callback-info-heading font-normal font-medium-xs"><i class="fa fa-user-o" aria-hidden="true"></i> <?php echo _l('task_single_assignees'); ?></h4>
    <?php if (is_admin() || is_staff_callbacks_responsible()) { ?>
        <div class="simple-bootstrap-select callbacks-select-staff">
            <div class="dropdown bootstrap-select text-muted callback-action-select bs3" style="width: 100%;">
                <select data-width="100%" data-live-search-placeholder="<?php echo 'Search members'; ?>" data-callback-id="<?= $cid; ?>" id="add_callback_assignees" class="text-muted callback-users-action-select selectpicker" name="select-callback-assignees" data-live-search="true" title='<?= _l('callbacks_select_assignees'); ?>' data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                    <?php
                    $options = '';
                    foreach ($staff as $assignee) {

                        if (!in_array($assignee['staffid'], $assignees_ids)) {

                            $options .= '<option value="' . $assignee['staffid'] . '">' . $assignee['full_name'] . '</option>';
                        }
                    }
                    echo $options;
                    ?>
                </select>
            </div>
        </div>
    <?php } ?>
    <div class="callback_users_wrapper">
        <?php
        $_assignees = '';
        foreach ($callback_assignees as $assignee) {

            $_remove_assigne = '';
            if (is_admin() || is_staff_callbacks_responsible()) {
                $_remove_assigne = ' <a href="#" class="remove-callback-user text-danger" onclick="remove_callback_assignee(' . $assignee['assigneeid'] . ',' . $assignee['callbackid'] . '); return false;"><i class="fa fa-remove"></i></a>';
            }
            $_assignees .= '<div class="callback-user" data-cid="' . $assignee['assigneeid'] . '"';
            $_assignees .= 'data-toggle="tooltip" data-title="' . html_escape($assignee['full_name']) . '">
                                                                <a href="' . admin_url('profile/' . $assignee['assigneeid']) . '" target="_blank">' . staff_profile_image($assignee['assigneeid'], array(
                'staff-profile-image-small'
            )) . '</a> ' . $_remove_assigne . '</span>
                                                                </div>';
        }
        if ($_assignees == '') {
            $_assignees = '<div class="text-danger display-block">' . _l('callback_no_assignees') . '</div>';
        }
        echo $_assignees;
        ?>
    </div>
</div>