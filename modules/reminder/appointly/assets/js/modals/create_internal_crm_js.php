<script>
    $(function() {
        init_selectpicker();
        initAppointmentScheduledDatesStaff();
        init_editor('textarea[name="notes"]', {
            menubar: false,
        });

        $('.modal').on('hidden.bs.modal', function(e) {
            $('.xdsoft_datetimepicker').remove();
            $(this).removeData();
        });

        appValidateForm($("#appointment-internal-crm-form"), {
            subject: "required",
            description: "required",
            date: "required",
            rel_type: "required",
            'attendees[]': {
                required: true,
                minlength: 2
            }
        }, apply_appointments_form_data, {
            'attendees[]': "Please select at least 1 staff member"
        });

        function apply_appointments_form_data(form) {
            $('button[type="submit"], button.close_btn').prop('disabled', true);
            $('button[type="submit"]').html('<i class="fa fa-refresh fa-spin fa-fw"></i>');
            $('#appointment-internal-crm-form .modal-body').addClass('filterBlur');
            $('.modal-title').html(
                "<?= _l('appointment_please_wait'); ?>"
            );

            var formSerializedData = $(form).serializeArray();


            var data = $(form).serialize();
            var url = form.action;

            $.post(url, data).done(function(response) {
                if (response.result) {
                    alert_float('success', "<?= _l("appointment_created"); ?>");
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
            return false;
        }
    });
</script>