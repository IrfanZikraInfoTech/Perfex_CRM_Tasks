<script>
    $(function () {

        var appointly_please_wait = "<?= _l("appointment_please_wait"); ?>";
        var is_busy_times_enabled = "<?= get_option('appointly_busy_times_enabled'); ?>";
        var allowedLeadsHours = <?= json_encode(json_decode(get_option('appointly_available_hours'))); ?>;
        var appLeadsMinTime = <?= get_option('appointments_show_past_times'); ?>;
        var appLeadsWeekends = <?= (get_option('appointments_disable_weekends')) ? "[0, 6]" : "[]"; ?>;

        var todaysLeadsDate = new Date();
        var currentLeadDate = todaysLeadsDate.getFullYear() + "-" + (((todaysLeadsDate.getMonth() + 1) < 10) ? "0" : "") + (todaysLeadsDate.getMonth() + 1 + "-" + ((todaysLeadsDate.getDate() < 10) ? "0" : "") + todaysLeadsDate.getDate());

        init_selectpicker();
        initAppointmentScheduledLeadsContactsDates();

        init_editor("textarea[name=\"notes\"]", {
            menubar: false,
        });

        appValidateForm($("#appointment-leads-contacts-crm-form"), {
            subject: "required",
            description: "required",
            name: "required",
            email: "required",
            date: "required",
            rel_type: "required",
            "attendees[]": {
                required: true,
                minlength: 1
            }
        }, apply_appointments_form_data, {
            "attendees[]": "Please select at least 1 staff member"
        });

        function apply_appointments_form_data(form)
        {
            $("#appointment-leads-contacts-crm-form button[type=\"submit\"], button.close_btn").prop("disabled", true);
            $("#appointment-leads-contacts-crm-form button[type=\"submit\"]").html("<i class=\"fa fa-refresh fa-spin fa-fw\"></i>");
            $("#appointment-leads-contacts-crm-form .modal-body").addClass("filterBlur");
            $(".modal-title").html(appointly_please_wait);

            var formSerializedData = $(form).serializeArray();

            var data = $(form).serialize();
            var url = form.action;

            $.post(url, data).done(function (response) {
                if (response.result) {
                    alert_float("success", "<?= _l("appointment_created"); ?>");
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
            return false;
        }

        function initAppointmentScheduledLeadsContactsDates()
        {
            $.post(site_url + "appointly/appointments_public/busyDates").done(function (r) {
                r = JSON.parse(r);
                var dateFormat = app.options.date_format;
                var appointmentDatePickerOptions = {
                    dayOfWeekStart: app.options.calendar_first_day,
                    minDate: 0,
                    format: dateFormat,
                    defaultTime: "09:00",
                    allowTimes: allowedLeadsHours,
                    closeOnDateSelect: 0,
                    closeOnTimeSelect: 1,
                    validateOnBlur: false,
                    minTime: appLeadsMinTime,
                    disabledWeekDays: appLeadsWeekends,
                    onGenerate: function (ct) {
                        if (is_busy_times_enabled == 1) {
                            var selectedGeneratedDate = ct.getFullYear() + "-" + (((ct.getMonth() + 1) < 10) ? "0" : "") + (ct.getMonth() + 1 + "-" + ((ct.getDate() < 10) ? "0" : "") + ct.getDate());

                            $(r).each(function (i, el) {
                                if (el.date == selectedGeneratedDate) {
                                    var currentTime = $("body").find(".xdsoft_time:contains(\"" + el.start_hour + "\")");
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

                            if (currentLeadDate !== selectedDate) {
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
    });
</script>