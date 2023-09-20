<script>
    // Language variables
    var appointly_lang_finished = "<?= _l('appointment_marked_as_finished'); ?>";
    var appointly_lang_cancelled = "<?= _l('appointment_is_cancelled'); ?>";
    var appointly_mark_as_ongoing = "<?= _l('appointment_marked_as_ongoing'); ?>";
    var appointment_are_you_sure_mark_as_ongoing = "<?= _l('appointment_are_you_sure_to_mark_as_ongoing') ?>";
    var appointly_are_you_sure_mark_as_cancelled = "<?= _l('appointment_are_you_sure_to_cancel') ?>";
    var appointly_are_you_early_reminders = "<?= _l('appointly_are_you_early_reminders') ?>";
    var appointly_reminders_sent = "<?= _l('appointly_reminders_sent') ?>";
    var appointly_please_wait = "<?= _l('appointment_please_wait'); ?>";

    // Add body class
    $(function () {
        $("body").addClass("single_view_board");
    });

    function editAppointmentFromView()
    {
        let params = (new URL(window.location.href)).searchParams;

        let appointment_id = params.get("appointment_id");

        $.getJSON("edit_from_view", {
            from_view_id: appointment_id,
        }).done(function (r) {
            if (r.success) {
                window.location = admin_url + "appointly/appointments/";
            }
        });

    }


    $(".modal").on("hidden.bs.modal", function (e) {
        tinymce.remove("textarea[name=\"google_meet_notify_message\"]");
        $(this).removeData();
    });

    // Mark appointment as finished
    function markAppointmentAsFinished()
    {
        var url = window.location.search;
        var id = url.split("=")[1];
        $.post("finished", {
            id: id,
            beforeSend: function () {
                disableButtonsAfterPost($("#markAsFinished"));
            }
        }).done(function (r) {
            if (r.success == true) {
                alert_float("success", appointly_lang_finished);
                reloadlocation(800);
            }
        });
    }

    // Cancel appointment
    function cancelAppointment()
    {
        var url = window.location.search;
        var id = url.split("=")[1];
        if (confirm(appointly_are_you_sure_mark_as_cancelled)) {
            $.post("cancel_appointment", {
                id: id,
                beforeSend: function () {
                    disableButtonsAfterPost($("#cancelAppointment"));
                },
            }).done(function (r) {
                if (r.success == true) {
                    alert_float("success", appointly_lang_cancelled);
                    reloadlocation(800);
                }
            });
        }
    }

    // Mark appointment as ongoing if marked as cancelled
    function markAppointmentAsOngoing()
    {
        var url = window.location.search;
        var id = url.split("=")[1];

        if (confirm(appointment_are_you_sure_mark_as_ongoing)) {
            $.post("mark_as_ongoing_appointment", {
                id: id,
                beforeSend: function () {
                    disableButtonsAfterPost($("#markAppointmentAsOngoing"));
                },
            }).done(function (r) {
                if (r.success == true) {
                    alert_float("success", appointly_mark_as_ongoing);
                    reloadlocation(800);
                }
            });
        }
    }

    //  Trigger appointment reminders
    function sendAppointmentReminders()
    {
        var url = window.location.search;
        var id = url.split("=")[1];

        if (confirm(appointly_are_you_early_reminders)) {
            $.post("send_appointment_early_reminders", {
                id: id,
                beforeSend: function () {
                    disableButtonsAfterPost($("#sendAppointmentReminders"));
                },
            }).done(function (r) {
                r = JSON.parse(r);
                if (r.success == true) {
                    alert_float("success", appointly_reminders_sent);
                    reloadlocation(2000);
                }
            });
        }
    }

    /**
     * Send google meet notification email to attendees and client
     */
    function sendGoogleMeetRequestEmail()
    {
        if ($(".modal-backdrop.fade").hasClass("in")) {
            $(".modal-backdrop.fade").remove();
        }
        if ($("#customEmailModal").is(":hidden")) {
            $("#customEmailModal").modal({
                show: true
            });
        }

        init_editor("textarea[name=\"google_meet_notify_message\"]");
    }

    function sendAppointmentRemindersEmail()
    {

        var message = tinyMCE.activeEditor.getContent();

        if ($.trim(message) == "") {
            alert("Please enter a message");
            return false;
        }

        $("#customEmailModal button#submit_google_meet_email_btn").html("<i class=\"fa fa-refresh fa-spin fa-fw google_meet_spinner\"></i>").attr("disabled", true);

        var attendees = '<?= json_encode($google_meet_attendees[0]); ?>';
        var emailData = {
            message: message,
            client_external_url: $(".appointment_public_url").attr("href"),
            google_meet_link: $(".google_meet_main a").attr("href"),
            to: $(".appointly_single_container #g_client_email").text(), // client email
            attendees: attendees // attendees
        };

        $.post("sendCustomEmail", emailData).done(function (r) {
            if (r === true) {
                alert_float("success", "<?= _l('appointment_meeting_request_sent'); ?>");
                reloadlocation(1500);
            } else {
                alert_float("warning", "<?= 'Failed to send email. Please check if your email settings are set correctly'; ?>");
                reloadlocation(2000);
            }
        });
    }

    // Disable buttons
    function disableButtonsAfterPost(button)
    {
        $("#markAsFinished").attr("disabled", true);
        $("#confirmDelete").attr("disabled", true);
        $("#cancelAppointment").attr("disabled", true);
        $("#markAppointmentAsOngoing").attr("disabled", true);
        $(".btn-primary-google").attr("disabled", true);
        button.html("" + appointly_please_wait + "<i class=\"fa fa-refresh fa-spin fa-fw\"></i>");
    }

    // Disable delete button used in view as function
    function disableButtonsAfterDelete()
    {
        $("button").attr("disabled", true);
        $("a").addClass("disabled");
        return false;
    }

    // Simple reload
    function reloadlocation(timer)
    {
        setTimeout(function () {
            location.reload();
        }, timer);
    }
</script>

<style>
    .mce-widget.mce-btn.mce-menubtn.mce-flow-layout-item.mce-last.mce-btn-has-text,
    .mce-widget.mce-btn.mce-splitbtn.mce-colorbutton.mce-last,
    .mce-widget.mce-btn.mce-splitbtn.mce-colorbutton.mce-first {
        display: none;
    }
</style>