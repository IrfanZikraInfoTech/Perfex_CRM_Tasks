<script>
    var appointly_please_wait = "<?= _l('appointment_please_wait'); ?>";
    var is_busy_times_enabled = "<?= get_option('appointly_busy_times_enabled'); ?>";
    var lang_save = "<?= _l('save'); ?>";

    var lang_view_notes = "<?= _l('appointment_viewing_notes'); ?>";

    $(function () {
        $(".sub-menu-item-appointly-user-history a").toggleClass("active");
        initDataTable(".table-appointments-history", '<?php echo admin_url('appointly/appointments_history/table'); ?>', [5], [5], [], [1, "desc"]);
    });

    $(".modal").on("hidden.bs.modal", function (e) {
        $(this).removeData();
    });

    var app_edit_id = "";

    function editAppointmentNotes(el)
    {
        var appointment_id = $(el).data("id");
        var content12 = $(".content .col-md-12");
        var content_row = $(".content .row.main_row");
        var skeleton_loader = `
                              <div class="ph-item">
                                   <div class="ph-col-12">
                                        <div class="ph-picture"></div>
                                             <div class="ph-row">
                                                  <div class="ph-col-6 big"></div>
                                                  <div class="ph-col-4 empty big"></div>
                                                  <div class="ph-col-2 big"></div>
                                                  <div class="ph-col-4"></div>
                                                  <div class="ph-col-8 empty"></div>
                                                  <div class="ph-col-6"></div>
                                                  <div class="ph-col-6 empty"></div>
                                                  <div class="ph-col-12"></div>
                                             </div>
                                        </div>
                              </div>`;

        $(".content .col-md-12").removeClass("col-md-12").addClass("col-md-6");
        $("td div.text-center a:first").css("margin", "-9px");
        $("#toggleTableBtn").removeClass("hidden");

        if (!content_row.find(".edit_appointment_history").length) {
            var div_loader = "<div class=\"col-md-6 edit_appointment_history old\"><div class=\"panel_s\"><div class=\"panel-body\">" + skeleton_loader + "</div></div><div>";
            content_row.append(div_loader);
        } else {
            content_row.find(".edit_appointment_history").append(div_loader);
            content_row.find(".edit_appointment_history.old").remove();
        }

        var appointment_notes = $.ajax({
            url: "/appointly/appointments_history/get_notes/" + appointment_id,
            beforeSend: function (xhr) {
                $(".edit_appointment_history .panel-body").html(skeleton_loader);
                app_edit_id = appointment_id;
            }
        })
            .done(function (data) {
                data = JSON.parse(data);
                tinymce.remove("textarea[name=\"notes\"]");
                data.notes = (null == data.notes) ? "" : data.notes;

                setTimeout(() => {
                    content_row.find(".edit_appointment_history").remove();
                    content_row.append(`
                                        <div class="col-md-6 edit_appointment_history">
                                             <div class="panel_s">
                                                  <div class="panel-body">
                                                       <div class="panel-heading"> 
                                                       <span class="font-medium">${lang_view_notes}: <strong>${data.subject}</strong></span>
                                                       </div>
                                                  <textarea name="notes" class="ays-ignore">${data.notes}</textarea>
                                                  <div class="from-group">
                                                       <button class="btn btn-primary mtop10 pull-right" onclick="updateAppointmentFormData()">${lang_save}</button>
                                                  </div>
                                             </div>
                                        </div>
                                        <div>`);
                    init_editor("textarea[name=\"notes\"]");
                }, 1000);
            });
    }

    function updateAppointmentFormData()
    {
        var notes = tinyMCE.activeEditor.getContent();
        var $button = $(".edit_appointment_history .from-group button");

        $.post("appointments_history/update_note", {
            appointment_id: app_edit_id,
            notes: notes,
            beforeUpdate()
            {
                $button.html("<i class=\"fa fa-refresh fa-spin fa-fw\"></i>");
            },
        }).done(function (response) {
            response = JSON.parse(response);
            if (response.result) {
                alert_float("success", '<?= _l('appointment_notes_updated'); ?>');
            } else {
                alert_float("success", "Something went wrong... try again later!");
            }
            $button.html(lang_save);
        });
    }

    // Request appointment feedback
    function request_appointment_feedback(appointment_id)
    {
        $("body").append("<div class=\"dt-loader\"></div>");

        var url = admin_url + "appointly/appointments/requestAppointmentFeedback/" + appointment_id;
        $.post(url).done(function (response) {
            if (response.success == true) {
                alert_float("info", "<?= _l('appointment_feedback_reuested_alert'); ?>");
                $("body").find(".dt-loader").remove();
            }
        }).fail(function (err) {
            $("body").find(".dt-loader").remove();
            console.log("An unknown error has been thrown" + err);
        });
    }

    function deleteAppointment(id, el)
    {
        if (confirm("<?= _l('appointment_are_you_sure'); ?>")) {

            $.post(site_url + "appointly/appointments/delete/" + id).done(function (res) {
                res = JSON.parse(res);
                if (res.success) {
                    alert_float("success", res.message);
                    $(".table-appointments").DataTable().ajax.reload();
                }
            });
        }
    }

    // Show/hide full table
    function toggle_appointment_table_view()
    {
        $("#toggleTableBtn").addClass("hidden");
        var col6 = $(".content .row.main_row .col-md-6").removeClass("col-md-6").addClass("col-md-12");
        $("td div.text-center a:first").css("margin", "auto");

        $(".edit_appointment_history").remove();
        setTimeout(() => {
            $(".edit_appointment_history").remove();
        }, 1000);
        $(window).trigger("resize");
    }
</script>
<style>
    .edit_appointment_history .panel-heading {
        background: #e3e8ee;
    }

    .mce-tinymce.mce-container.mce-panel {
        margin-top: 10px;

    }

    .ph-item {
        direction: ltr;
        position: relative;
        display: flex;
        flex-wrap: wrap;
        overflow: hidden;
        margin-bottom: 30px;
        background-color: #fff;
        border-radius: 2px;
    }

    .ph-item,
    .ph-item *,
    .ph-item ::after,
    .ph-item ::before {
        box-sizing: border-box;
    }

    .ph-item::before {
        content: " ";
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 50%;
        z-index: 1;
        width: 500%;
        margin-left: -250%;
        -webkit-animation: phAnimation 0.8s linear infinite;
        animation: phAnimation 0.8s linear infinite;
        background: linear-gradient(to right, rgba(255, 255, 255, 0) 46%, rgba(255, 255, 255, 0.35) 50%, rgba(255, 255, 255, 0) 54%) 50% 50%;
    }

    .ph-item > * {
        flex: 1 1 auto;
        display: flex;
        flex-flow: column;
        padding-right: 15px;
        padding-left: 15px;
    }

    .ph-row {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 7.5px;
    }

    .ph-row div {
        height: 10px;
        margin-bottom: 7.5px;
        background-color: #ced4da;
    }

    .ph-row .big,
    .ph-row.big div {
        height: 20px;
        margin-bottom: 15px;
    }

    .ph-row .empty {
        background-color: rgba(255, 255, 255, 0);
    }

    .ph-col-2 {
        flex: 0 0 16.66667%;
    }

    .ph-col-4 {
        flex: 0 0 33.33333%;
    }

    .ph-col-6 {
        flex: 0 0 50%;
    }

    .ph-col-8 {
        flex: 0 0 66.66667%;
    }

    .ph-col-10 {
        flex: 0 0 83.33333%;
    }

    .ph-col-12 {
        flex: 0 0 100%;
    }

    .ph-avatar {
        position: relative;
        width: 100%;
        min-width: 60px;
        background-color: #ced4da;
        margin-bottom: 15px;
        border-radius: 50%;
        overflow: hidden;
    }

    .ph-avatar::before {
        content: " ";
        display: block;
        padding-top: 100%;
    }

    .ph-picture {
        width: 100%;
        height: 120px;
        background-color: #ced4da;
        margin-bottom: 15px;
    }

    @-webkit-keyframes phAnimation {
        0% {
            transform: translate3d(-30%, 0, 0);
        }

        100% {
            transform: translate3d(30%, 0, 0);
        }
    }

    @keyframes phAnimation {
        0% {
            transform: translate3d(-30%, 0, 0);
        }

        100% {
            transform: translate3d(30%, 0, 0);
        }
    }
</style>