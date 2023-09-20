<script>
    $(function() {

        $('#callbackView').on('hidden.bs.modal', function() {
            $(this).removeData();
            $(this).find('form')[0].reset();
        });

        var callbacksServerParams = {
            'custom_view': '[name="custom_view"]'
        }

        initDataTable('.table-callbacks', '<?php echo admin_url('appointly/callbacks/table'); ?>', [8], [8], callbacksServerParams, [6, 'desc']);

    });


    // View single callback
    function viewCallback(callbackid) {
        var callback = function() {
            var $modalBackdrop = $('.modal-backdrop.fade');
            var $callbackView = $('#callbackView');

            if ($modalBackdrop.hasClass('in')) {
                $modalBackdrop.remove();
            }

            if ($callbackView.is(':hidden')) {
                $callbackView.modal({
                    show: true
                });
            }
        };

        $("#modal_wrapper").load("<?php echo admin_url('appointly/Callbacks/callbackView'); ?>", {
            id: callbackid
        }, callback);
    }

    function deleteCallback(callbackid) {
        if (confirm_delete()) {
            $("body").append('<div class="dt-loader"></div>');
            requestGetJSON('appointly/Callbacks/delete/' + callbackid).done(function(response) {
                $("body").find('.dt-loader').remove();
                if (response.success === true || response.success == 'true') {
                    $('.table-callbacks').DataTable().ajax.reload();
                    alert_float('success', response.message);
                }
            }).fail(function(data) {
                alert_float('danger', data.responseText);
            });
        }
    }


    // Mark callback status
    function callback_mark_status_as(callback_id, status) {
        var url = "<?= admin_url('appointly/Callbacks/callback_mark_as'); ?>";

        $("body").append('<div class="dt-loader"></div>');
        $.post(url, {
            callback_id,
            status: status
        }).done(function(r) {
            $("body").find('.dt-loader').remove();
            if (r.success === 'true') {
                $('.table-callbacks').DataTable().ajax.reload();
                alert_float('success', "<?= _l('callback_status_changed'); ?>");
            }
        });
    }

    // Delete callback note
    function delete_callback_note(wrapper, id, callback_id) {
        if (confirm_delete()) {
            requestGetJSON('appointly/Callbacks/delete_note/' + id + '/' + callback_id).done(function(response) {
                if (response.success === true || response.success === 'true') {
                    $(wrapper).parents('.callback-note').remove();
                }
            }).fail(function(data) {
                alert_float('danger', data.responseText);
            });
        }
    }

    // Add callback data returned from server to the callback modal
    function _callback_init_data(data) {

        var $callbackViewModal = $('#callbackView');

        $callbackViewModal.find('.partial').html(data.callbackView);

        $callbackViewModal.modal({
            show: true,
            backdrop: 'static'
        });
    }


    // Submit notes on callback modal do ajax not the regular request
    $("body").on('submit', '#callbackView #callback-notes', function() {
        var form = $(this);
        var data = $(form).serialize();
        $.post(form.attr('action'), data).done(function(response) {
            response = JSON.parse(response);
            _callback_init_data(response, response.id);
        }).fail(function(data) {
            alert_float('danger', data.responseText);
        });
        return false;
    });

    // Assign task to staff member
    $("body").on('change', 'select[name="select-callback-assignees"]', function() {
        $("body").append('<div class="dt-loader"></div>');

        var data = {};
        data.assignee = $('select[name="select-callback-assignees"]').val();
        if (data.assignee !== '') {
            data.callbackid = $(this).attr('data-callback-id');
            $.post(admin_url + 'appointly/Callbacks/add_callback_assignees', data).done(function(response) {
                $("body").find('.dt-loader').remove();
                response = JSON.parse(response);
                if (response.success === true || response.success == 'true') {
                    _initCallbackHtmlReturnData(response.callbackHtml);
                    alert_float('success', "<?= _l('callback_assignee_added_success'); ?>");
                }
            });
        }
    });

    // render (fetch new data) assignees user data
    function _initCallbackHtmlReturnData(data) {
        var $callbackModsl = $('#callbackView');

        $callbackModsl.find('.assigned_users_data').html(data);
        init_selectpicker();
        $('.table-callbacks').DataTable().ajax.reload();
    }

    // Remove task assignee
    function remove_callback_assignee(id, callbackid) {
        if (confirm_delete()) {
            requestGetJSON('appointly/Callbacks/remove_callback_assignee/' + id + '/' + callbackid).done(function(response) {
                if (response.success === true || response.success == 'true') {
                    alert_float('success', response.message);
                    _initCallbackHtmlReturnData(response.callbackHtml);
                }
            });
        }
    }
</script>
