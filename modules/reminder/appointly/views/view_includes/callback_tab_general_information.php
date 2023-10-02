<div role="tabpanel" class="tab-pane" id="tab_general_information">
    <?php
    $target_callback_timezone = new DateTimeZone($callback['timezone']);
    $date_time_callback_zone = new DateTime('now', $target_callback_timezone);
    ?>
    <p>
        <i class="fa fa-user-o fa-fw fa-lg" aria-hidden="true"></i><strong> <?= _l('callbacks_m_name'); ?></strong><?= $callback['firstname'] . ' ' . $callback['lastname']; ?>
    </p>
    <hr>
    <p data-toggle="tooltip" title="<?= _l('callbacks_call_now'); ?>" data-placement=" left">
        <strong><i class="fa fa-phone fa-fw fa-lg"></i> <?= _l('callbacks_form_your_phone'); ?>: </strong>
        <a href="tel:<?= $callback['phone_number']; ?>">
            <?= $callback['phone_number']; ?>
        </a>
    </p>
    <hr>
    <p data-toggle="tooltip" title="<?= _l('callbacks_m_email'); ?>" data-placement="left">
        <strong><i class="fa fa-envelope fa-fw fa-lg"></i> <?= _l('callbacks_m_email'); ?>: </strong>
        <span><?= $callback['email']; ?></span>
    </p>
    <hr>
    <p><i class="fa fa-globe fa-fw fa-lg"></i>
        <strong><?= _l('callbacks_m_timezone'); ?>:</strong><?= $callback['timezone'] . ' GMT' . $date_time_callback_zone->format('P') ?>
    </p>
    <hr>
    <p>
        <i class="fa fa-star-o fa-fw fa-lg"></i>
        <strong><?= _l('callbacks_m_status'); ?>:</strong><?= ucfirst(fetchCallbackStatusName($callback['status'])); ?>
    </p>
    <hr>
    <label for="callbacks-textarea-message">
        <i class="fa fa-comments fa-fw fa-lg"></i>
        <?= _l('callbacks_m_client_message'); ?>:</label>
    <textarea class="callbacks-textarea-message" disabled id="callbacks-textarea-message"><?= $callback['message']; ?></textarea>
    <hr>
    <p class="callbacks_phone"><i class="fa fa-info fa-fw fa-lg"></i>
        <strong><?= _l('callback_m_perfered_client_call'); ?>:</strong>
        <?= callbacks_handle_call_type(json_decode($callback['call_type'])) ?>
        <hr>
    </p>
    <hr>
    <p>
        <i class="fa task-info-icon fa-fw fa-lg fa-calendar-plus-o pull-left fa-margin"></i>
        <?= _l('callbacks_m_best_time_to_call'); ?>
        <span class="label label-info"><?= _dt($callback['date_start']); ?> <strong class="text-dark"><?= strtoupper(_l('callback_plus_and')); ?> </strong><?= _dt($callback['date_end']); ?></span>
    </p>
    <hr>
    <div class="callbacks_assignees_wrapper assigned_users_data">
        <h4 class="task-info-heading font-normal font-medium-xs"><i class="fa fa-user-o" aria-hidden="true"></i> <?php echo _l('callbacks_table_assigned_to'); ?></h4>
        <?php if (is_admin() || is_staff_callbacks_responsible()) { ?>
            <div class="simple-bootstrap-select callbacks-select-staff">
                <div class="dropdown bootstrap-select text-muted callback-action-select bs3" style="width: 100%;">
                    <select data-width="100%" data-live-search-placeholder="<?php echo 'Search members'; ?>" data-callback-id="<?php echo $callback['id']; ?>" id="add_callback_assignees" class="text-muted callback-users-action-select selectpicker" name="select-callback-assignees" data-live-search="true" title='<?= _l('callbacks_select_assignees'); ?>' data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
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
                if (
                    is_admin()
                    || is_staff_callbacks_responsible()
                ) {
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
</div>