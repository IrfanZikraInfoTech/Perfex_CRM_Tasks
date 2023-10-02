<script>
    $(function () {

        var callback_form = $("#perfex-callbacks-form");
        var call_types = [];
        var validation_phone_lang = "<?= _l('callback_phone_validate'); ?>";

        $(".dates").datetimepicker();

        $(document).on("click", ".callbacks_image", function () {

            var selectedType = $(this).data("type-name");

            $(this).toggleClass("selected_option");

            var itemExists = $.inArray(selectedType, call_types);

            if (itemExists >= 0) {
                call_types = call_types.filter(function (item) {
                    return item !== selectedType;
                });
            } else {
                call_types.push(selectedType);
            }
        });

        appValidateForm($("#perfex-callbacks-form"), {
            client_firstname: "required",
            client_lastname: "required",
            client_email: "required",
            date_from: "required",
            date_to: "required",
            client_phone: {
                required: true,
                number: true,
                minlength: 6,
                maxlength: 20
            },
        }, callbacksHandleFormSubmit, {
            client_phone: validation_phone_lang
        });

        // Widget active
        $(document).on("click", ".bar-deactive", function () {
            $(this).removeClass("bar-deactive").addClass("bar-active");
            $(".cb-form-wrapper").removeClass("pfx-cb-hide").addClass("pfx-cb-show");
            $("body.appointments-external-form .container-fluid").addClass("blurry");
            callback_form[0].reset();
        });

        // Widget hidden
        $(document).on("click", ".bar-active", function () {
            $(this).removeClass("bar-active").addClass("bar-deactive");
            ;
            $(".cb-form-wrapper").removeClass("pfx-cb-show").addClass("pfx-cb-hide");
            $(".selectpicker").selectpicker("refresh");
            $("body.appointments-external-form .container-fluid").removeClass("blurry");
            callback_form[0].reset();
            call_types = [];
        });

        function disableSubmitEvents()
        {
            $("#pfxcbsubmit").attr("disabled", true).html("<i class=\"fa fa-refresh fa-spin fa-fw\"></i>");
            ;
        }


        function callbacksHandleFormSubmit(form)
        {
            $(".selectpicker").selectpicker("render");

            disableSubmitEvents();

            $.post(form.action, (
                $(form).serialize() + "&" + $.param({"call_types": call_types}))).done(function (response) {

                response = JSON.parse(response);

                if (response.success) {
                    var orignal_form = $(".message_wrapper").html();
                    $("#request_callback_label").hide("fast");
                    $(".message_wrapper").html('<div class="alert alert-success" role="alert"><h4 class="alert-heading"><?= _l('callback_was_submited'); ?></h4><p><?= _l('callback_was_submited_thank_you'); ?></p></div>');

                    setTimeout(() => {
                        <?php if (is_client_logged_in()) : ?>
                        window.location.href = "<?= base_url(); ?>";
                        <?php endif; ?>
                    }, 2000);

                    setTimeout(() => {
                        $(".message_wrapper").html(orignal_form);
                        $(this).removeClass("bar-active").addClass("bar-deactive");
                        $(".cb-form-wrapper").removeClass("pfx-cb-show").addClass("pfx-cb-hide");
                        $(document).find(".callbacks_image").removeClass("selected_option");
                        $(".dates").datetimepicker();
                        // Remove previously used selectpicker
                        $("button[role=\"combobox\"]").remove();
                        $("#pfxcbsubmit").attr("disabled", false).html('<?= _l('callbacks_request_btn_label'); ?>');
                        $("#request_callback_label").show();
                        callback_form[0].reset();
                        call_types = [];
                    }, 3000);

                }
                $("body.appointments-external-form .container-fluid").removeClass("blurry");

            });
            return false;
        }
    });
</script>