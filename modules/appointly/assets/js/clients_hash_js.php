<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
        // Star rating functionality
        $(document).on('mouseover', 'span.feedback_star', function() {
            var onStar = parseInt($(this).data('count'));
            $(this).parent().children('span.feedback_star').each(function(e) {
                if (e <= onStar) {
                    $(this).addClass('hover_star');
                } else {
                    $(this).removeClass('hover_star');
                }
            });

        }).on('mouseout', 'span.feedback_star', function() {
            $(this).parent().children('span.feedback_star').each(function(e) {
                $(this).removeClass('hover_star');
            });
        });

    });

    var url = "<?= $appointment['url']; ?>";
    var feedback_url = "<?= $appointment['feedback_url']; ?>";
    var hash = "<?= $this->input->get('hash'); ?>";
    var review_feedback_data = {};

    $('#cancelAppointmentForm').on('click', function(e) {
        e.preventDefault();

        var notes = $('#notes').val();

        if ($.trim(notes) == '') {
            $('#alert').addClass('alert-warning').html("<?= _l('appointment_describe_reason_for_cancel'); ?>");
            return;
        }
        $('#alert').hide();

        $.get(url, {
            notes: notes,
            hash: hash
        }).done(function(r) {
            if (r !== '') {

                if (r.response.success == true) {
                    $('#alert').addClass('alert-success').removeClass('alert-warning').text(r.response.message).show();
                    $('#cancelAppointmentForm').attr('disabled', true);
                } else {
                    $('#alert').addClass('alert-warning').removeClass('alert-success').text(r.response.message).show();
                }

                if (r.response.success == true) {
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                }
            }
        })
    });
    // If staff is logged in dont allow to click on reviews
    <?php if (!is_staff_logged_in()) { ?>

        var _reviewHasComment = false;
        var _postClickEnabled = false;

        function handle_appointment_feedback(el) {
            // If there is a active current post dont allow clicking multiple times
            if (_postClickEnabled === false) {
                var id = "<?= $appointment['id']; ?>";
                var rating = $(el).data('rating');
                _postClickEnabled = true;
                $('.feedback_star').css('pointer-events', 'none');
                _reviewHasComment = "<?= json_encode(($appointment['feedback_comment'])); ?>";

                review_feedback_data.id = id;
                review_feedback_data.rating = rating;

                appendLoadingAnimation('removestar');

                var onStar = parseInt($(el).data('count') + 1);
                var stars = $(el).parent().children('.feedback_star');

                for (i = 0; i < stars.length; i++) {
                    $(stars[i]).removeClass('star_rated');
                }

                for (i = 0; i < onStar; i++) {
                    $(stars[i]).addClass('star_rated');
                }

                if (!_reviewHasComment == 'false' || _reviewHasComment == 'null') {
                    $("body").on('click', '.feedback_star', function() {
                        var comment = confirm("<?= _l('appointment_leave_a_comment'); ?>");
                        if (comment) {

                            if ($('.modal-backdrop.fade').hasClass('in')) {
                                $('.modal-backdrop.fade').remove();
                            }
                            if ($('#reviewModal').is(':hidden')) {
                                $('#reviewModal').modal('show');
                                $('#review-alert').addClass('alert-info').html("<?= _l('appointment_feedback_comment_textarea_info'); ?>");
                                appendLoadingAnimation('addstar');
                            }
                        } else {
                            handleReviewPost(feedback_url, review_feedback_data);
                        }
                    });
                }
                if (_reviewHasComment == 'true') {
                    handleReviewPost(feedback_url, review_feedback_data);
                }
            }
        }

        function handleReviewPost(feedback_url, review_feedback_data) {
            $.getJSON(feedback_url, review_feedback_data).done(function(response) {

                if (response.success == true) {
                    setTimeout(function() {
                        $('#appointment_feedbacks span:first').html("<?= _l('appointment_feedback_label'); ?>");
                        $('#appointment_feedbacks span:first').removeClass('label-success').addClass('label-primary');
                    }, 2000);
                    $('[data-toggle="tooltip"]').tooltip();
                    location.reload(3000);
                } else {
                    appendLoadingAnimation('addstar');
                }
                _reviewHasComment = false;
                _postClickEnabled = false;

            }).fail(function(err) {
                console.log('An error has occured: ');
                console.log(err);
            });
        }

        $('body').on('click', '#reviewModalSubmitBtn', function(e) {
            e.preventDefault();

            var feedback_comment = $('#feedback_comment').val();
            if ($.trim(feedback_comment) == '') {
                appendLoadingAnimation('addstar');
                return;

            } else {
                appendLoadingAnimation('removestar');

                review_feedback_data.feedback_comment = feedback_comment;
                $('#review-alert').hide();
                $('#reviewModal').modal('hide');

                $.getJSON(feedback_url, review_feedback_data).done(function(response) {

                    if (response.success == true) {
                        $('#appointment_feedbacks').html(response._html);
                        $('[data-toggle="tooltip"]').tooltip();
                        location.reload(2000);
                    }
                }).fail(function(err) {
                    console.log('An error has occured: ');
                    console.log(err);
                });
            }
        });

    <?php } ?>

    function appendLoadingAnimation(param) {
        if (param == 'removestar') {
            $('body').find('i').each(function(index, el) {
                $(el).removeClass('fa-star').addClass('pulse');
            });
        } else {
            $('body').find('i').each(function(index, el) {
                $(el).removeClass('pulse').addClass('fa-star');
            });
        }
    }
</script>