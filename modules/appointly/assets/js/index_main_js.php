<script>
    var appointly_please_wait = "<?= _l("appointment_please_wait"); ?>";
    var is_busy_times_enabled = "<?= get_option('appointly_busy_times_enabled'); ?>";
    var appointly_lang_finished = "<?= _l("appointment_marked_as_finished"); ?>";
    var appointly_lang_cancelled = "<?= _l("appointment_is_cancelled"); ?>";
    var appointly_mark_as_ongoing = "<?= _l("appointment_marked_as_ongoing"); ?>";
    var appointment_are_you_sure_mark_as_ongoing = "<?= _l("appointment_are_you_sure_to_mark_as_ongoing") ?>";
    var appointly_are_you_sure_mark_as_cancelled = "<?= _l("appointment_are_you_sure_to_cancel") ?>";
    var appointly_are_you_early_reminders = "<?= _l("appointly_are_you_early_reminders") ?>";
    var appointly_reminders_sent = "<?= _l("appointly_reminders_sent") ?>";
    var appointly_lang_approved = "<?= _l("appointment_marked_as_approved"); ?>";
    var edit_from_view = "<?= $edit_appointment_id; ?>";

    var filters = <?php echo json_encode($filters); ?>;

    $(function () {

        var apointmentsServerParams = {};

        for (var filter in filters) {
            apointmentsServerParams[filters[filter]] = "[name=\"" + filters[filter] + "\"]";
        }


        let appointmentsTable = initDataTable(".table-appointments", '<?php echo admin_url('appointly/appointments/table'); ?>', [7], [7], apointmentsServerParams, [1, "desc"]);


        appointmentsTable.on("draw", function () {
                if (edit_from_view > 0) {
                    console.log(edit_from_view);
                    $("body").find(".row-options a[data-id=\"" + edit_from_view + "\"]").trigger("click");
                    "<?php $this->session->unset_userdata('from_view_id')?> ";
                }
            }
        );

        $("body").on("click", ".approve_appointment", function () {
            $(this).attr("disabled", true);
            $(this).prev().next().addClass("approve_appointment_spacing");
            $(this).html("<i class=\"fa fa-refresh fa-spin fa-fw\"></i>");
        });

        $("#createNewAppointment").click(function () {
            $("#modal_wrapper").load("<?php echo admin_url('appointly/appointments/modal'); ?>", {
                slug: "create"
            }, function () {
                if ($(".modal-backdrop.fade").hasClass("in")) {
                    $(".modal-backdrop.fade").remove();
                }
                if ($("#newAppointmentModal").is(":hidden")) {
                    $("#newAppointmentModal").modal({
                        show: true
                    });
                }
            });
        });
    });

    function appointmentUpdateModal(el)
    {
        var id = $(el).data("id");
        var modal = $("#modal_wrapper").load("<?php echo admin_url('appointly/appointments/modal'); ?>", {
            slug: "update",
            appointment_id: id
        }, function () {
            if ($(".modal-backdrop.fade").hasClass("in")) {
                $(".modal-backdrop.fade").remove();
            }
            if ($("#appointmentModal").is(":hidden")) {
                $("#appointmentModal").modal({
                    show: true
                });
            }
            if (!isOutlookLoggedIn()) {
                $("#addToOutlookBtn").remove();
            }
        });
    }

    $(".modal").on("hidden.bs.modal", function (e) {
        $(this).removeData();
    });

    var allowedHours = <?= json_encode(json_decode(get_option('appointly_available_hours'))); ?>;
    var appMinTime = <?= get_option('appointments_show_past_times'); ?>;
    var appWeekends = <?= (get_option('appointments_disable_weekends')) ? "[0, 6]" : "[]"; ?>;

    var todaysDate = new Date();

    var currentDate = todaysDate.getFullYear() + "-" + (((todaysDate.getMonth() + 1) < 10) ? "0" : "") + (todaysDate.getMonth() + 1 + "-" + ((todaysDate.getDate() < 10) ? "0" : "") + todaysDate.getDate());

    function initAppointmentScheduledDates()
    {
        let busyDatesUrl = site_url + "/appointly/appointments_public/busyDates";

        $.post(busyDatesUrl).done(function (r) {
            r = JSON.parse(r);
            var dateFormat = app.options.date_format;
            var appointmentDatePickerOptions = {
                dayOfWeekStart: app.options.calendar_first_day,
                minDate: 0,
                format: dateFormat,
                defaultTime: "09:00",
                allowTimes: allowedHours,
                closeOnDateSelect: 0,
                closeOnTimeSelect: 1,
                validateOnBlur: false,
                minTime: appMinTime,
                disabledWeekDays: appWeekends,
                disabledDates: ["2021/04/08"],
                onGenerate: function (ct) {
                    if (is_busy_times_enabled == 1) {
                        var selectedGeneratedDate = ct.getFullYear() + "-" + (((ct.getMonth() + 1) < 10) ? "0" : "") + (ct.getMonth() + 1 + "-" + ((ct.getDate() < 10) ? "0" : "") + ct.getDate());

                        $(r).each(function (i, el) {

                            if (el.date == selectedGeneratedDate) {
                                var currentTime = $("body")
                                    .find(".xdsoft_time:contains(\"" + el.start_hour + "\")");
                                if (el.source == undefined) {
                                    currentTime.addClass("busy_google_time");
                                } else {
                                    currentTime.addClass("busy_time");
                                }
                            }
                        });
                    }
                },
                onSelectDate: function (ct) {

                    var selectedDate = ct.getFullYear() + "-" + (((ct.getMonth() + 1) < 10) ? "0" : "") + (ct.getMonth() + 1 + "-" + ((ct.getDate() < 10) ? "0" : "") + ct.getDate());

                    setTimeout(function () {
                        $("body").find(".xdsoft_time").removeClass("xdsoft_current xdsoft_today");

                        if (currentDate !== selectedDate) {
                            $("body").find(".xdsoft_time.xdsoft_disabled").removeClass("xdsoft_disabled");
                        }
                    }, 200);
                },
                onChangeDateTime: function () {
                    var currentTime = $("body").find(".xdsoft_time");
                    currentTime.removeClass("busy_time");
                }
            };

            if (app.options.time_format == 24) {
                dateFormat = dateFormat + " H:i";
            } else {
                dateFormat = dateFormat + " g:i A";
                appointmentDatePickerOptions.formatTime = "g:i A";
            }

            appointmentDatePickerOptions.format = dateFormat;

            $(".appointment-date").datetimepicker(appointmentDatePickerOptions);
        });
    }

    // Create new task directly from relation, related options selected after modal is shown
    function new_task_from_relation_appointment(appointment)
    {
        var contact_name = $(appointment).data("name");
        var contact_id = $(appointment).data("contact-id");
        var rel_id = $(appointment).data("customer-id");
        var rel_type = $(appointment).data("source");

        if (rel_id !== 0 && rel_type === "internal") {
            rel_type = "customer";
            rel_id = rel_id;
        } else {
            rel_type = "lead";
            rel_id = rel_id;
        }

        var url = admin_url + "tasks/task?rel_id=" + rel_id + "&rel_type=" + rel_type;
        new_task(url);

        $("#_task").on("show.bs.modal", function (e) {
            $("body").find("#_task #task_is_billable").attr("checked", false);
            $("body").find("#_task #name").val("<?= _l("appointments_contact_name_task"); ?> " + "[ " + contact_name + " ]");
        });
    }

    // Init lead convert to lead for appointment
    function init_appointment_lead(appointment)
    {
        var contact_name = $(appointment).data("name");
        var contact_email = $(appointment).data("email");
        var contact_phonenumber = $(appointment).data("phone");
        // In case header error
        if (init_lead_modal_data(undefined, undefined, false)) {
            $("#lead-modal").modal("show");
        }
        $("#lead-modal").on("show.bs.modal", function (e) {
            $("body").find("#lead-modal .modal-title").text("<?= _l("appointments_convert_to_lead"); ?>");
            $("body").find("#lead-modal #name").val(contact_name);
            $("body").find("#lead-modal #email").val(contact_email);
            $("body").find("#lead-modal #phonenumber").val(contact_phonenumber);
        });
    }

    // Request appointment feedback
    function request_appointment_feedback(appointment_id)
    {
        $("body").append("<div class=\"dt-loader\"></div>");

        var url = admin_url + "appointly/appointments/requestAppointmentFeedback/" + appointment_id;
        $.post(url).done(function (response) {
            if (response.success == true) {
                alert_float("info", "<?= _l("appointment_feedback_reuested_alert"); ?>");
                $("body").find(".dt-loader").remove();
            }
        }).fail(function (err) {
            $("body").find(".dt-loader").remove();
            console.log("An unknown error has been thrown" + err);
        });
    }

    function deleteAppointment(id, el)
    {
        if (confirm("<?= _l("appointment_are_you_sure"); ?>")) {
            var outlookId = $(".table-appointments").find("a#outlookLink_" + id).data("outlook-id");

            if (outlookId != undefined) {
                deleteOutlookEvent(outlookId);
            }

            $.post(site_url + "appointly/appointments/delete/" + id).done(function (res) {
                res = JSON.parse(res);
                if (res.success) {
                    alert_float("success", res.message);
                    $(".table-appointments").DataTable().ajax.reload();
                }
            });
        }
    }

    /**
     * Check if user is logged in to outlook
     *
     * @return boolean
     */
    function isOutlookLoggedIn()
    {
        if (typeof myMSALObj !== "undefined" && myMSALObj.getAccount()) {
            return true;
        }
        return false;
    }

    // Mark appointment as finished
    function markAppointmentAsFinished(id)
    {
        var url = window.location.search;
        $.post("appointments/finished", {
            id: id,
            beforeSend: function () {
                $(".table-appointments").append("<div class=\"dt-loader\"></div>");
            }
        }).done(function (r) {
            if (r.success == true) {
                alert_float("success", appointly_lang_finished);
                $(".table-appointments").DataTable().ajax.reload();
            }
            $(".table-appointments").find(".dt-loader").remove();
        });
    }

    function markAppointmentAsApproved(id)
    {
        var url = window.location.search;
        $.post("appointments/approve", {
            appointment_id: id,
            beforeSend: function () {
                $(".table-appointments").append("<div class=\"dt-loader\"></div>");
            }
        }).done(function (r) {
            r = JSON.parse(r);
            if (r.result == true) {
                alert_float("success", appointly_lang_approved);
                $(".table-appointments").DataTable().ajax.reload();
            }
            $(".table-appointments").find(".dt-loader").remove();
        });
    }

    // Cancel appointment
    function markAppointmentAsCancelled(id)
    {
        var url = window.location.search;
        if (confirm(appointly_are_you_sure_mark_as_cancelled)) {
            $.post("appointments/cancel_appointment", {
                id: id,
                beforeSend: function () {
                    $(".table-appointments").append("<div class=\"dt-loader\"></div>");
                },
            }).done(function (r) {
                if (r.success == true) {
                    alert_float("success", appointly_lang_cancelled);
                    $(".table-appointments").DataTable().ajax.reload();
                }
                $(".table-appointments").find(".dt-loader").remove();
            });
        }
    }

    // Mark appointment as ongoing if marked as cancelled
    function markAppointmentAsOngoing(id)
    {
        var url = window.location.search;
        if (confirm(appointment_are_you_sure_mark_as_ongoing)) {
            $.post("appointments/mark_as_ongoing_appointment", {
                id: id,
                beforeSend: function () {
                    $(".table-appointments").append("<div class=\"dt-loader\"></div>");
                    // think of some indicator
                },
            }).done(function (r) {
                if (r.success == true) {
                    alert_float("success", appointly_mark_as_ongoing);
                    $(".table-appointments").DataTable().ajax.reload();
                }
                $(".table-appointments").find(".dt-loader").remove();
            });
        }
    }
</script>