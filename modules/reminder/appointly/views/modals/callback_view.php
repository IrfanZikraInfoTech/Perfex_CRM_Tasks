<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Modal -->
<div id="callbackView" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?= _l('callbacks_m_name_title'); ?> [ <?= $callback['firstname'] . ' ' . $callback['lastname']; ?> ]
                </h4>
            </div>
            <div class="modal-body partial">
                <div class="row">
                    <div class="col-md-12">
                        <div class="top-lead-menu">
                            <div class="horizontal-scrollable-tabs preview-tabs-top">
                                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                                <div class="horizontal-tabs">

                                    <ul class="nav-tabs-horizontal nav nav-tabs role=" tablist">
                                        <li role="presentation" class="active">
                                            <a href="#tab_general_information" aria-controls="tab_general_information" role="tab" data-toggle="tab">
                                                <?= _l('callbacks_m_general'); ?>
                                            </a>
                                        </li>
                                        <li role="presentation">
                                            <a href="#callback_notes" aria-controls="callback_notes" role="tab" data-toggle="tab">
                                                <?= _l('callbacks_m_notes'); ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Tab panes -->
                        <div class="tab-content mtop20">
                            <div role="tabpanel" class="tab-pane" id="callback_notes">
                                <?php echo form_open(admin_url('appointly/callbacks/add_note/' . $callback['id']), array('id' => 'callback-notes')); ?>
                                <?php if (isset($callback)) {
                                    echo form_hidden('callbackid', $callback['id']);
                                } ?>
                                <div class="form-group">
                                    <textarea id="callback_notes_description" name="callback_notes_description" class="form-control" rows="4"></textarea>
                                </div>
                                <div class="callback-select-date-contacted hide">
                                    <?php echo render_datetime_input('custom_contact_date', 'callback_date_contacted', '', array('data-date-end-date' => date('Y-m-d'))); ?>
                                </div>
                                <div class="radio radio-primary">
                                    <input type="radio" name="callback_contacted_indicator" id="callback_contacted_indicator_yes" value="yes">
                                    <label for="callback_contacted_indicator_yes"><?= _l('callbacks_have_contacted'); ?></label>
                                </div>
                                <div class="radio radio-primary">
                                    <input type="radio" name="callback_contacted_indicator" id="callback_contacted_indicator_no" value="no" checked>
                                    <label for="callback_contacted_indicator_no"><?= _l('callbacks_have_not_contacted'); ?></label>
                                </div>
                                <button type="submit" class="btn btn-xs btn-info pull-right"><?php echo _l('lead_add_edit_add_note'); ?></button>
                                <?php echo form_close(); ?>
                                <div class="clearfix"></div>
                                <hr />
                                <div class="panel_s no-shadow">
                                    <?php
                                    $len = count($callback_notes);
                                    $i = 0;
                                    foreach ($callback_notes as $note) { ?>
                                        <div class="media callback-note">
                                            <a href="<?php echo admin_url('profile/' . $note["addedfrom"]); ?>" target="_blank">
                                                <?php echo staff_profile_image($note['addedfrom'], array('staff-profile-image-small', 'pull-left mright10')); ?>
                                            </a>
                                            <div class="media-body">
                                                <?php if (
                                                    $note['addedfrom'] == get_staff_user_id()
                                                    || is_admin()
                                                    || is_staff_callbacks_responsible()
                                                ) { ?>
                                                    <a href="#" class="pull-right text-danger" onclick="delete_callback_note(this,<?php echo $note['id']; ?>, <?php echo $note['id']; ?>);return false;"><i class="fa fa fa-times"></i></a>
                                                    <a href="#" class="pull-right mright5" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><i class="fa fa-pencil-square-o"></i></a>
                                                <?php } ?>
                                                <?php if (!empty($note['date_contacted'])) { ?>
                                                    <span data-toggle="tooltip" data-title="" <?php echo _l('callback_date_contacted') . ' ' . _dt($note['date_contacted']); ?>">
                                                        <i class="fa fa-phone-square text-success font-medium valign" aria-hidden="true"></i>
                                                    </span>
                                                <?php } ?>
                                                <small><?php echo _l('lead_note_date_added', _dt($note['dateadded'])); ?></small>
                                                <a href="<?php echo admin_url('profile/' . $note["addedfrom"]); ?>" target="_blank">
                                                    <h5 class="media-heading bold"><?php echo get_staff_full_name($note['addedfrom']); ?></h5>
                                                </a>
                                                <div data-note-description="<?php echo $note['id']; ?>" class="text-muted">
                                                    <?php echo check_for_links(app_happy_text($note['description'])); ?>
                                                </div>
                                                <div data-note-edit-textarea="<?php echo $note['id']; ?>" class="hide mtop15">
                                                    <?php echo render_textarea('note', '', $note['description']); ?>
                                                    <div class="text-right">
                                                        <button type="button" class="btn btn-xs btn-default" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
                                                        <button type="button" class="btn btn-xs btn-info" onclick="edit_note(<?php echo $note['id']; ?>);"><?php echo _l('update_note'); ?></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if ($i >= 0 && $i != $len - 1) {
                                                echo '<hr />';
                                            }
                                            ?>
                                        </div>
                                    <?php $i++;
                                    } ?>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane active" id="tab_general_information">
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
                                    <strong><i class="fa fa-envelope fa-fw fa-lg"></i> <?= _l('callbacks_m_email'); ?>:</strong>
                                    <span><?= $callback['email']; ?></span>
                                </p>
                                <hr>
                                <p><i class="fa fa-globe fa-fw fa-lg"></i>
                                    <strong><?= _l('callbacks_m_timezone'); ?>: </strong><?= $callback['timezone'] . ' GMT' . $date_time_callback_zone->format('P') ?>
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
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-xs btn-default close_btn" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
<?php require('modules/appointly/assets/js/modals/callback_view_js.php'); ?>