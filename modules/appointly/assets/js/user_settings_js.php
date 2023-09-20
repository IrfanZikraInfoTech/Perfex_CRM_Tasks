<script>
     function delete_appointment_type(id) {
          var url = "<?= admin_url('appointly/appointments/delete_appointment_type'); ?>";
          if (confirm('<?= _l("confirm_action_prompt"); ?>')) {
               $.post(url, {
                    id: id
               }).done(function(r) {
                    if (r.success == true) {
                         alert_float('success', "<?= _l('appointments_type_deleted_successfully'); ?>");
                         $('.mright20#aptype_' + id).fadeOut();
                    }
               })
          }
     }

     appValidateForm($("#appointmentNewTypeForm"), {
          appointment_type: "required",
          color: "required",
     });

     $('body').on('submit', '#appointmentNewTypeForm', function() {
          var app_type = $('input[name="appointment_type"]').val();
          var color_type = $('input[name="color"]').val();

          var url = "<?= admin_url('appointly/appointments/new_appointment_type'); ?>";

          $.post(url, {
               type: app_type,
               color: color_type,
               beforeSend() {
                    $('button[type="submit"], button.close_btn').prop('disabled', true);
                    $('button[type="submit"]').html('<i class="fa fa-refresh fa-spin fa-fw"></i>');
               }
          }).done(function(r) {
               if (r.success == true) {
                    setTimeout(function() {
                         location.reload()
                    }, 1500);
                    alert_float('success', "<?= _l('appointments_type_added_successfully'); ?>");
               }
          });
          return false;
     });
</script>