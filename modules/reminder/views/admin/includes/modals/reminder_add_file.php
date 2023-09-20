<?php defined("BASEPATH") or exit("No direct script access allowed"); ?>
<div class="modal fade" id="reminderAddModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo isset($reminderData)
                        ? _l("reminder_edit")
                        : _l("reminder_new"); ?></span>
                </h4>
            </div>
            <?php echo form_open_multipart(
                admin_url() . "reminder/reminder_new/",
                [
                    "id" => "reminder-form",
                    "class" =>
                        "_transaction_form_reminder reminder-form new_items_table",
                    "id" => "reminder-form",
                ]
            ); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning affect-warning hide">
                            <?php echo _l("changing_items_affect_warning"); ?>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php $value = isset(
                                                    $reminderData
                                                )
                                                    ? _d($reminderData->date)
                                                    : ""; ?>
                                                <?php echo render_datetime_input(
                                                    "date",
                                                    "set_reminder_date",
                                                    $value,
                                                    [
                                                        "data-date-min-date" => _d(
                                                            date("Y-m-d")
                                                        ), 
                                                    ]
                                                ); ?>
                                                <div class="col-md-12">
                                                </div>
                                            </div>
                                            <div class="col-md-6"> 
                                                <div class="row">
                                                    <div class="form-group select-placeholder">
                                                        <label for="customer" class="control-label"><?php echo _l(
                                                            "reminder_customer"
                                                        ); ?></label>
                                                        <div class="flexer">
                                                            <select name="customer" data-full_view="full_view" id="customer" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l(
                                                                "dropdown_non_selected_tex"
                                                            ); ?>">
                                                                <option value="" <?php if (
                                                                    (isset(
                                                                        $proposal
                                                                    ) &&
                                                                        $proposal->rel_type ==
                                                                            "customer") ||
                                                                    $this->input->get(
                                                                        "rel_type"
                                                                    ) ||
                                                                    !empty(
                                                                        $rel_type
                                                                    )
                                                                ) {
                                                                    if (
                                                                        $rel_type ==
                                                                        "customer"
                                                                    ) {
                                                                        echo "selected";
                                                                    }
                                                                } ?>>
                                                                </option>
                                                                <?php if (
                                                                    $customers
                                                                ) {
                                                                    foreach (
                                                                        $customers
                                                                        as $key =>
                                                                            $v
                                                                    ) {
                                                                        $chkCust = isset(
                                                                            $reminderData
                                                                        )
                                                                            ? $reminderData->customer
                                                                            : 0;
                                                                        $val =
                                                                            $chkCust ==
                                                                            $v[
                                                                                "userid"
                                                                            ]
                                                                                ? "selected"
                                                                                : "";
                                                                        echo "<option value='" .
                                                                            $v[
                                                                                "userid"
                                                                            ] .
                                                                            "'" .
                                                                            $val .
                                                                            ">" .
                                                                            $v[
                                                                                "company"
                                                                            ] .
                                                                            "</option>";
                                                                    }
                                                                } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 full_view">
                                                <div class="proposal_to_wrap full_cn">
                                                    <?php
                                                    $value = isset(
                                                        $reminderData
                                                    )
                                                        ? $reminderData->contact
                                                        : "";
                                                    if (
                                                        isset($reminderData) &&
                                                        !empty(
                                                            $reminderData->contact
                                                        )
                                                    ) {
                                                        echo render_select(
                                                            "contact",
                                                            $contacts,
                                                            ["id", "name"],
                                                            "contact",
                                                            $reminderData->contact,
                                                            [],
                                                            [],
                                                            "",
                                                            "full_view_contact"
                                                        );
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <?php
                                                $i = 0;
                                                $selected = "";
                                                foreach ($staff as $member) {
                                                    if (isset($reminderData)) {
                                                        if (
                                                            $reminderData->assigned_to ==
                                                            $member["staffid"]
                                                        ) {
                                                            $selected =
                                                                $member[
                                                                    "staffid"
                                                                ];
                                                        }
                                                    }
                                                    $i++;
                                                }
                                                echo render_select(
                                                    "assigned_to",
                                                    $staff,
                                                    [
                                                        "staffid",
                                                        [
                                                            "firstname",
                                                            "lastname",
                                                        ],
                                                    ],
                                                    "reminder_assigned",
                                                    $selected
                                                );
                                                ?>
                                            </div>

                                            <!----------------------Related Service------------------------------>
                                            <div class="col-md-12">
                                                <div class="form-group select-placeholder">
                                                    <label for="rel_type" class="control-label"><?php echo _l(
                                                        "reminder_related"
                                                    ); ?></label>
                                                    <select name="rel_type" id="rel_type" class="selectpicker" data-width="100%">
                                                        <option value=""></option>
                                                        <option value="proposal"><?php echo _l(
                                                            "rm_proposals"
                                                        ); ?></option>
                                                        <option value="estimate"><?php echo _l(
                                                            "rm_estimates"
                                                        ); ?></option>
                                                        <option value="invoice"><?php echo _l(
                                                            "rm_invoices"
                                                        ); ?></option>
                                                        <option value="credit_note"><?php echo _l(
                                                            "rm_credit_notes"
                                                        ); ?></option>
                                                        <option value="ticket"><?php echo _l(
                                                            "rm_tickets"
                                                        ); ?></option>
                                                        <option value="custom_reminder"><?php echo _l(
                                                            "rm_custom_reminder"
                                                        ); ?></option>
                                                        <option value="service"><?php echo _l(
                                                            "rm_custom_service"
                                                        ); ?></option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12 recurring_custom_service hide">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <?php echo render_select_with_input_group(
                                                            "services[]",
                                                            $services,
                                                            [
                                                                "id",
                                                                "service_name",
                                                            ],
                                                            "reminder_service_type",
                                                            "",
                                                            '<a href="#" data-toggle="modal" data-target="#reminder_add_service"><i class="fa fa-plus"></i></a>',
                                                            [
                                                                "multiple" => true,
                                                                "data-actions-box" => true,
                                                            ],
                                                            [],
                                                            "",
                                                            "service_reminder",
                                                            false
                                                        ); ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="lead_value"><?php echo _l(
                                                            "reminder_amount"
                                                        ); ?></label>
                                                        <div class="input-group" data-toggle="tooltip" title="<?php echo _l(
                                                            "reminder_amount"
                                                        ); ?>">
                                                            <input type="number" class="form-control" id="reminderamount" name="total_amount" value="">
                                                            <div class="input-group-addon">
                                                                <?php echo $base_currency->symbol; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-----------------------End Related service --------------------------->
                                            <div class="col-md-12">
                                                <div class="relidwrap">
                                                    <div class="form-group select-placeholder">
                                                        <label for="rel_id" class="control-label"><?php echo _l(
                                                            "reminder_related_document"
                                                        ); ?></label>
                                                        <select name="rel_id" id="rel_id" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l(
                                                            "dropdown_non_selected_tex"
                                                        ); ?>">
                                                            <option></option>
                                                            <?php $chkRelType = isset(
                                                                $reminderData
                                                            )
                                                                ? $reminderData->rel_id
                                                                : 0; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div id="otheridwrap"></div>
                                            </div>
                                            <div class="col-md-12">
                                                <div id="other_attachment"></div>
                                            </div>

                                            <!----------------------Repeat Every start------------------------------>

                                            <div class="col-md-12">
                                                <?php
                                                $related = [
                                                    [
                                                        "value" => "1-week",
                                                        "option" => _l("week"),
                                                    ],
                                                    [
                                                        "value" => "2-week",
                                                        "option" =>
                                                            "2 " . _l("weeks"),
                                                    ],
                                                    [
                                                        "value" => "1-month",
                                                        "option" =>
                                                            "1 " . _l("month"),
                                                    ],
                                                    [
                                                        "value" => "2-month",
                                                        "option" =>
                                                            "2 " . _l("months"),
                                                    ],
                                                    [
                                                        "value" => "3-month",
                                                        "option" =>
                                                            "3 " . _l("months"),
                                                    ],
                                                    [
                                                        "value" => "6-month",
                                                        "option" =>
                                                            "6 " . _l("months"),
                                                    ],
                                                    [
                                                        "value" => "1-year",
                                                        "option" =>
                                                            "1 " . _l("year"),
                                                    ],
                                                    [
                                                        "value" => "custom",
                                                        "option" => _l(
                                                            "recurring_custom"
                                                        ),
                                                    ],
                                                ];
                                                echo render_select(
                                                    "repeat_every",
                                                    $related,
                                                    ["value", "option"],
                                                    "task_repeat_every",
                                                    ""
                                                );
                                                ?>
                                            </div>

                                            <div class="col-md-12 recurring_custom <?php if (
                                                (isset($reminder) &&
                                                    $reminder->custom_recurring !=
                                                        1) ||
                                                !isset($reminder)
                                            ) {
                                                echo "hide";
                                            } ?>">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <?php $value =
                                                            isset($reminder) &&
                                                            $reminder->custom_recurring ==
                                                                1
                                                                ? $reminder->repeat_every
                                                                : 1; ?>
                                                        <?php echo render_input(
                                                            "repeat_every_custom",
                                                            "",
                                                            $value,
                                                            "number",
                                                            ["min" => 1]
                                                        ); ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select name="repeat_type_custom" id="repeat_type_custom" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l(
                                                            "dropdown_non_selected_tex"
                                                        ); ?>">
                                                            <option value="day"><?php echo _l(
                                                                "task_recurring_days"
                                                            ); ?></option>
                                                            <option value="week"><?php echo _l(
                                                                "task_recurring_weeks"
                                                            ); ?></option>
                                                            <option value="month"><?php echo _l(
                                                                "task_recurring_months"
                                                            ); ?></option>
                                                            <option value="year"><?php echo _l(
                                                                "task_recurring_years"
                                                            ); ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="cycles_wrapper" class="<?php if (
                                                !isset($reminder) ||
                                                (isset($reminder) &&
                                                    $reminder->recurring == 0)
                                            ) {
                                                echo " hide";
                                            } ?> col-md-12">
                                                <?php $value = isset($reminder)
                                                    ? $reminder->cycles
                                                    : 0; ?>
                                                <div class="form-group recurring-cycles">
                                                    <label for="cycles"><?php echo _l(
                                                        "recurring_total_cycles"
                                                    ); ?>
                                                        <?php if (
                                                            isset($reminder) &&
                                                            $reminder->total_cycles >
                                                                0
                                                        ) {
                                                            echo "<small>" .
                                                                _l(
                                                                    "cycles_passed",
                                                                    $reminder->total_cycles
                                                                ) .
                                                                "</small>";
                                                        } ?>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" <?php if (
                                                            $value == 0
                                                        ) {
                                                            echo " disabled";
                                                        } ?> name="cycles" id="cycles" value="<?php echo $value; ?>" <?php if (
    isset($reminder) &&
    $reminder->total_cycles > 0
) {
    echo 'min="' . $reminder->total_cycles . '"';
} ?>>
                                                        <div class="input-group-addon">
                                                            <div class="checkbox">
                                                                <input type="checkbox" <?php if (
                                                                    $value == 0
                                                                ) {
                                                                    echo " checked";
                                                                } ?> id="unlimited_cycles">
                                                                <label for="unlimited_cycles"><?php echo _l(
                                                                    "cycles_infinity"
                                                                ); ?></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-----------------------Repeat Every End --------------------------->

                                            <div class="col-md-12">
                                                <?php
                                                $value = isset($reminderData)
                                                    ? _d(
                                                        $reminderData->description
                                                    )
                                                    : "";
                                                echo render_textarea(
                                                    "description",
                                                    "reminder_description",
                                                    $value
                                                );
                                                ?>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="checkbox" name="notify_by_sms_client" id="notify_by_sms_client">
                                                            <label for="notify_by_sms_client" value="2"><?php echo _l(
                                                                "rm_reminder_notify_me_by_sms_client"
                                                            ); ?></label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="checkbox" name="notify_by_email_client" id="notify_by_email_client">
                                                            <label for="notify_by_email_client" value="2"><?php echo _l(
                                                                "rm_reminder_notify_me_by_email_client"
                                                            ); ?></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="id" value="<?php echo isset(
                                                $reminderData
                                            )
                                                ? $reminderData->id
                                                : ""; ?>">
                                            <div class="btn-bottom-toolbar bottom-transaction text-right">
                                                <button class="btn btn-info mleft5 proposal-form-submit transaction-submit-proposal" type="submit">
                                                    <?php echo _l(
                                                        "save_and_exit"
                                                    ); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l(
                    "close"
                ); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l(
                    "submit"
                ); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>