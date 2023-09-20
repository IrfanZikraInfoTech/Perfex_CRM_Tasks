<script>
(function($) {
"use strict";

    $('#from_time').datetimepicker({
        datepicker: false,
        format: 'H:i'
      });
      $('#to_time').datetimepicker({
        datepicker: false,
        format: 'H:i'
      });

    if(get_url_param('eventid')) {
    	view_event(get_url_param('eventid'));
    }

    _validate_form($('#add_edit_booking-form'),{purpose:'required',resource_group:'required',resource:'required',start_time:'required',end_time:'required'});
    $('#resource_group').on('change', function(){
      $.post(admin_url+'resource_booking/get_resource_by_group/'+this.value).done(function(response){
         response = JSON.parse(response);
         $("#resource").html('');
         $html = '<option value=""></option>';
         $.each(response.cont,function(){
            $html += '<option value="'+ this.id +'">'+ this.resource_name +'</option>';
         });
         $("#resource").html($html);
         $("#resource").selectpicker('refresh');
      });
    });

    var calendar_selector = $('#calendars');
    if (calendar_selector.length > 0) {
        validate_calendar_form();
        var calendar_settings = {
            //themeSystem: 'bootstrap3',
            customButtons: {},
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay,viewFullCalendar,calendarFilter'
            },
            editable: false,
            dayMaxEventRows: parseInt(app.options.calendar_events_limit) + 1,

            views: {
                day: {
                    eventLimit: false
                }
            },
            direction: (isRTL == 'true' ? 'rtl' : 'ltr'),
            eventStartEditable: false,
            firstDay: parseInt(app.options.calendar_first_day),
            initialView: app.options.default_view_calendar,
            timeZone: app.options.timezone,
            loading: function (isLoading, view) {
                !isLoading ? $('.dt-loader').addClass('hide') : $('.dt-loader').removeClass('hide');
            },
            eventSources: [function(info, successCallback, failureCallback){
                var params = {};
                  $('#calendar_filters').find('input:checkbox:checked').map(function () {
                        params[$(this).attr('name')] = true;
                    }).get();

                    if (!jQuery.isEmptyObject(params)) {
                        params['calendar_filters'] = true;
                    }

                return $.getJSON(admin_url + 'recruitment/get_calendar_interview_schedule_data', $.extend({}, params, {
                    start: info.startStr,
                    end: info.endStr,
                })).then(function(data){
                    successCallback(data.map(function(e){
                        return $.extend( {}, e, {
                            start: e.start || e.date,
                            end: e.end || e.date
                        });
                    }));
                });
            }],
            moreLinkClick: function (info) {
                calendar.gotoDate( info.date )
                calendar.changeView('dayGridDay');

                setTimeout(function(){
                    $('.fc-popover-close').click();
                }, 250)
            },

           eventDidMount: function (data) {
                var $el = $(data.el);
                $el.attr('title', data.event.extendedProps._tooltip);
                $el.attr('onclick', data.event.extendedProps.onclick);
                $el.attr('data-toggle', 'tooltip');
                if (!data.event.extendedProps.url) {
                    $el.on('click', function(){
                        view_event(data.event.extendedProps.eventid);
                    });
                }
            },
            dateClick: function (info) {
                if (info.dateStr.length <= 10) { // has not time
                    info.dateStr += ' 00:00';
                }

                var fmt = new DateFormatter();
                var vformat = (app.options.time_format == 24 ? app.options.date_format + ' H:i' : app.options.date_format + ' g:i A');
                var d1 = fmt.formatDate(new Date(info.dateStr), vformat);
                $("input[name='interview_day'].datetimepicker").val(d1);
                $('#interview_schedules_modal').modal('show');
                $('.add-title').removeClass('hide');
                $('.edit-title').addClass('hide');
                $('#from_time').datetimepicker({
                    datepicker: false,
                    format: 'H:i'
                  });
                  $('#to_time').datetimepicker({
                    datepicker: false,
                    format: 'H:i'
                  });
                return false;
            }
        };


        if (app.user_is_staff_member == 1) {

            if (app.options.google_api !== '') {
                calendar_settings.googleCalendarApiKey = app.options.google_api;
            }

            if (app.calendarIDs !== '') {
                app.calendarIDs = JSON.parse(app.calendarIDs);
                if (app.calendarIDs.length != 0) {
                    if (app.options.google_api !== '') {
                        for (var i = 0; i < app.calendarIDs.length; i++) {
                            var _gcal = {};
                            _gcal.googleCalendarId = app.calendarIDs[i];
                            calendar_settings.eventSources.push(_gcal);
                        }
                    } else {
                        console.error('You have setup Google Calendar IDs but you dont have specified Google API key. To setup Google API key navigate to Setup->Settings->Google');
                    }
                }
            }
        }
        // Init calendar
        var calendar = new FullCalendar.Calendar(calendar_selector[0], calendar_settings)
        calendar.render();

        var new_event = get_url_param('new_event');
        if (new_event) {
            $("input[name='interview_day'].datetimepicker").val(get_url_param('date'));
            $('#interview_schedules_modal').modal('show');
        }

    }


})(jQuery);

function check_resource_booking(){
    "use strict";

    var resource = $('#resource').val();
    var start_time = $('#start_time').val();
    var end_time = $('#end_time').val();
      $.post(admin_url+'resource_booking/check_resource_booking/'+resource+'/'+start_time+'/'+end_time).done(function(response){
         response = JSON.parse(response);
         if(response.check == true){
            $("#add_edit_booking-form").submit();
            $('#interview_schedules_modal').modal('hide');
            location.reload();
         }else{
            $('.notification').html('');
            $('.notification').append('<label class="danger"><?php echo _l('notification_check_resource_booking'); ?></label');
         }
      });
}


function validate_calendar_form() {
     "use strict";

    appValidateForm($("body").find('._event form'), {
        title: 'required',
        start: 'required',
        reminder_before: 'required'
    }, calendar_form_handler);

    appValidateForm($("body").find('#viewEvent form'), {
        title: 'required',
        start: 'required',
        reminder_before: 'required'
    }, calendar_form_handler);
}

function calendar_form_handler(form) {
    "use strict";

    $.post(form.action, $(form).serialize()).done(function(response) {
        response = JSON.parse(response);
        if (response.success === true || response.success == 'true') {
            alert_float('success', response.message);
            setTimeout(function() {
                var location = window.location.href;
                location = location.split('?');
                window.location.href = location[0];
            }, 500);
        }
    });

    return false;
}

</script>