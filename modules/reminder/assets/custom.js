$(document).on('click', '.reminder_form_service', function (event) {
    event.preventDefault();
    formdata = new FormData($("#reminder-form_service")[0]);
    var check_view=$(this).attr('data-check_view');
    $.ajax({
        url: $("#reminder-form_service").attr('action'),
        data: formdata,
        contentType: false,
        processData: false,
        type: 'POST',
        dataType: "json",
        success: function (response) { 
            if (response.success == true) {
                if(check_view == "full_view"){
                window.location.href="";
                }else{
                $("#reminder_add_service").modal('hide');
                  $('select[name="services[]"]').append('<option value="'+response.id+'" selected>'+response.name+'</option>');
                $('select[name="services[]"]').selectpicker('refresh');
                $('select[name="services[]"]').trigger('change');
                alert_float('success', "services added successfully");
            }
            } else if (response.success == "warning") {
                $('.err' + 'franchise_number').css('color', 'red').text(response.msg);
            }
            else {
                $.each(response.errors, function (key, value) {
                    $('.err' + key).css('color', 'red').text(value);
                });
            }
        }
    });
});
