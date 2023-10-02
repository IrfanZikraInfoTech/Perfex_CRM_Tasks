<div class="row">
    <div class="col-md-12">
        <?php if (isset($callback)) {
            echo form_hidden('callbackid', $callback['id']);
        } ?>
        <div class="top-lead-menu">
            <div class="horizontal-scrollable-tabs preview-tabs-top">
                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                <div class="horizontal-tabs">

                    <ul class="nav-tabs-horizontal nav nav-tabs role=" tablist">
                        <li role="presentation">
                            <a href="#tab_general_information" aria-controls="tab_general_information" role="tab" data-toggle="tab">
                                <?= _l('callbacks_m_general'); ?>
                            </a>
                        </li>
                        <li role="presentation" class="active">
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
            <div role="tabpanel" class="tab-pane active" id="callback_notes">
                <?php echo form_open(admin_url('appointly/Callbacks/add_note/' . $callback['id']), array('id' => 'callback-notes')); ?>
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
                    <label for="callback_contacted_indicator_yes"><?php echo _l('callbacks_have_contacted'); ?></label>
                </div>
                <div class="radio radio-primary">
                    <input type="radio" name="callback_contacted_indicator" id="callback_contacted_indicator_no" value="no" checked>
                    <label for="callback_contacted_indicator_no"><?php echo _l('callbacks_have_not_contacted'); ?></label>
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
                                <?php if ($note['addedfrom'] == get_staff_user_id() || is_admin()) { ?>
                                    <a href="#" class="pull-right text-danger" onclick="delete_callback_note(this,<?php echo $note['id']; ?>, <?php echo $note['id']; ?>);return false;"><i class="fa fa fa-times"></i></a>
                                    <a href="#" class="pull-right mright5" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><i class="fa fa-pencil-square-o"></i></a>
                                <?php } ?>
                                <?php if (!empty($note['date_contacted'])) { ?>
                                    <span data-toggle="tooltip" data-title="<?php echo _l('callback_date_contacted') . ' ' . _dt($note['date_contacted']); ?>">
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
            <?php
            $data = [];
            $data['callback_notes'] = $callback_notes;
            $data['callback'] = $callback;
            $data['callback_assignees'] = $callback_assignees;

            $this->load->view('view_includes/callback_tab_general_information', $data); ?>
        </div>
    </div>
</div>
<?php require('modules/appointly/assets/js/modals/callback_view_js.php'); ?>