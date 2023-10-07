<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
var weekly_payments_statistics;
var monthly_payments_statistics;
var user_dashboard_visibility = <?php echo $user_dashboard_visibility; ?>;

$(function() {
    $("[data-container]").sortable({
        connectWith: "[data-container]",
        helper: 'clone',
        handle: '.widget-dragger',
        tolerance: 'pointer',
        forcePlaceholderSize: true,
        placeholder: 'placeholder-dashboard-widgets',
        start: function(event, ui) {
            $("body,#wrapper").addClass('noscroll');
            $('body').find('[data-container]').css('min-height', '20px');
        },
        stop: function(event, ui) {
            $("body,#wrapper").removeClass('noscroll');
            $('body').find('[data-container]').removeAttr('style');
        },
        update: function(event, ui) {
            if (this === ui.item.parent()[0]) {
                var data = {};
                $.each($("[data-container]"), function() {
                    var cId = $(this).attr('data-container');
                    data[cId] = $(this).sortable('toArray');
                    if (data[cId].length == 0) {
                        data[cId] = 'empty';
                    }
                });
                $.post(admin_url + 'staff/save_dashboard_widgets_order', data, "json");
            }
        }
    });

    // Read more for dashboard todo items
    $('.read-more').readmore({
        collapsedHeight: 150,
        moreLink: "<a href=\"#\"><?php echo _l('read_more'); ?></a>",
        lessLink: "<a href=\"#\"><?php echo _l('show_less'); ?></a>",
    });

    $('body').on('click', '#viewWidgetableArea', function(e) {
        e.preventDefault();

        if (!$(this).hasClass('preview')) {
            $(this).html("<?php echo _l('hide_widgetable_area'); ?>");
            $('[data-container]').append(
                '<div class="placeholder-dashboard-widgets pl-preview"></div>');
        } else {
            $(this).html("<?php echo _l('view_widgetable_area'); ?>");
            $('[data-container]').find('.pl-preview').remove();
        }

        $('[data-container]').toggleClass('preview-widgets');
        $(this).toggleClass('preview');
    });

    var $widgets = $('.widget');
    var widgetsOptionsHTML = '';
    widgetsOptionsHTML += '<div id="dashboard-options">';
    widgetsOptionsHTML +=
        "<div class=\"tw-flex tw-space-x-4 tw-items-center\"><h4 class='tw-font-medium tw-text-neutral-600 tw-text-lg'><i class='fa-regular fa-circle-question' data-toggle='tooltip' data-placement=\"bottom\" data-title=\"<?php echo _l('widgets_visibility_help_text'); ?>\"></i> <?php echo _l('widgets'); ?></h4><a href=\"<?php echo admin_url('staff/reset_dashboard'); ?>\" class=\"tw-text-sm\"><?php echo _l('reset_dashboard'); ?></a>";

    widgetsOptionsHTML +=
        ' <a href=\"#\" id="viewWidgetableArea" class=\"tw-text-sm\"><?php echo _l('view_widgetable_area'); ?></a></div>';

    $.each($widgets, function() {
        var widget = $(this);
        var widgetOptionsHTML = '';
        if (widget.data('name') && widget.html().trim().length > 0) {
            widgetOptionsHTML += '<div class="checkbox">';
            var wID = widget.attr('id');
            wID = wID.split('widget-');
            wID = wID[wID.length - 1];
            var checked = ' ';
            var db_result = $.grep(user_dashboard_visibility, function(e) {
                return e.id == wID;
            });
            if (db_result.length >= 0) {
                // no options saved or really visible
                if (typeof(db_result[0]) == 'undefined' || db_result[0]['visible'] == 1) {
                    checked = ' checked ';
                }
            }
            widgetOptionsHTML += '<input type="checkbox" class="widget-visibility" value="' + wID +
                '"' + checked + 'id="widget_option_' + wID + '" name="dashboard_widgets[' + wID + ']">';
            widgetOptionsHTML += '<label for="widget_option_' + wID + '">' + widget.data('name') +
                '</label>';
            widgetOptionsHTML += '</div>';
        }
        widgetsOptionsHTML += widgetOptionsHTML;
    });

    $('.screen-options-area').append(widgetsOptionsHTML);
    $('body').find('#dashboard-options input.widget-visibility').on('change', function() {
        if ($(this).prop('checked') == false) {
            $('#widget-' + $(this).val()).addClass('hide');
        } else {
            $('#widget-' + $(this).val()).removeClass('hide');
        }

        var data = {};
        var options = $('#dashboard-options input[type="checkbox"]').map(function() {
            return {
                id: this.value,
                visible: this.checked ? 1 : 0
            };
        }).get();

        data.widgets = options;
        /*
                if (typeof(csrfData) !== 'undefined') {
                    data[csrfData['token_name']] = csrfData['hash'];
                }
        */
        $.post(admin_url + 'staff/save_dashboard_widgets_visibility', data).fail(function(data) {
            // Demo usage, prevent multiple alerts
            if ($('body').find('.float-alert').length == 0) {
                alert_float('danger', data.responseText);
            }
        });
    });

    var tickets_chart_departments = $('#tickets-awaiting-reply-by-department');
    var tickets_chart_status = $('#tickets-awaiting-reply-by-status');
    var leads_chart = $('#leads_status_stats');
    var projects_chart = $('#projects_status_stats');

    if (tickets_chart_departments.length > 0) {
        // Tickets awaiting reply by department chart
        var tickets_dep_chart = new Chart(tickets_chart_departments, {
            type: 'doughnut',
            data: <?php echo $tickets_awaiting_reply_by_department; ?>,
        });
    }
    if (tickets_chart_status.length > 0) {
        // Tickets awaiting reply by department chart
        new Chart(tickets_chart_status, {
            type: 'doughnut',
            data: <?php echo $tickets_reply_by_status; ?>,
            options: {
                onClick: function(evt) {
                    onChartClickRedirect(evt, this);
                }
            },
        });
    }
    if (leads_chart.length > 0) {
        // Leads overview status
        new Chart(leads_chart, {
            type: 'doughnut',
            data: <?php echo $leads_status_stats; ?>,
            options: {
                maintainAspectRatio: false,
                onClick: function(evt) {
                    onChartClickRedirect(evt, this);
                }
            }
        });
    }
    if (projects_chart.length > 0) {
        // Projects statuses
        new Chart(projects_chart, {
            type: 'doughnut',
            data: <?php echo $projects_status_stats; ?>,
            options: {
                maintainAspectRatio: false,
                onClick: function(evt) {
                    onChartClickRedirect(evt, this);
                }
            }
        });
    }

    if ($(window).width() < 500) {
        // Fix for small devices weekly payment statistics
        $('#payment-statistics').attr('height', '250');
    }

    fix_user_data_widget_tabs();
    $(window).on('resize', function() {
        $('.horizontal-scrollable-tabs ul.nav-tabs-horizontal').removeAttr('style');
        fix_user_data_widget_tabs();
    });
    // Payments statistics
    init_weekly_payment_statistics(<?php echo $weekly_payment_stats; ?>);

    $('select[name="currency"]').on('change', function() {
        let $activeChart = $('#Payment-chart-name').data('active-chart');

        if (typeof(weekly_payments_statistics) !== 'undefined') {
            weekly_payments_statistics.destroy();
        }

        if (typeof(monthly_payments_statistics) !== 'undefined') {
            monthly_payments_statistics.destroy();
        }

        if ($activeChart == 'weekly') {
            init_weekly_payment_statistics();
        } else if ($activeChart == 'monthly') {
            init_monthly_payment_statistics();
        }

    });
});

function fix_user_data_widget_tabs() {
    if ((app.browser != 'firefox' &&
            isRTL == 'false' && is_mobile()) || (app.browser == 'firefox' &&
            isRTL == 'false' && is_mobile())) {
        $('.horizontal-scrollable-tabs ul.nav-tabs-horizontal').css('margin-bottom', '26px');
    }
}

function init_weekly_payment_statistics(data) {
    if ($('#payment-statistics').length > 0) {

        if (typeof(weekly_payments_statistics) !== 'undefined') {
            weekly_payments_statistics.destroy();
        }
        if (typeof(data) == 'undefined') {
            var currency = $('select[name="currency"]').val();
            $.get(admin_url + 'dashboard/weekly_payments_statistics/' + currency, function(response) {
                weekly_payments_statistics = new Chart($('#payment-statistics'), {
                    type: 'bar',
                    data: response,
                    options: {
                        responsive: true,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                }
                            }]
                        },
                    },
                });
            }, 'json');
        } else {
            weekly_payments_statistics = new Chart($('#payment-statistics'), {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                            }
                        }]
                    },
                },
            });
        }

    }
}

function init_monthly_payment_statistics() {
    if ($('#payment-statistics').length > 0) {

        if (typeof(monthly_payments_statistics) !== 'undefined') {
            monthly_payments_statistics.destroy();
        }

        var currency = $('select[name="currency"]').val();
        $.get(admin_url + 'dashboard/monthly_payments_statistics/' + currency, function(response) {
            monthly_payments_statistics = new Chart($('#payment-statistics'), {
                type: 'bar',
                data: response,
                options: {
                    responsive: true,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                            }
                        }]
                    },
                },
            });
        }, 'json');
    }
}

function update_payment_statistics(el) {
    let type = $(el).data('type');
    let $chartNameWrapper = $('#Payment-chart-name');
    $chartNameWrapper.data('active-chart', type);
    $chartNameWrapper.text($(el).text());

    if (typeof(weekly_payments_statistics) !== 'undefined') {
        weekly_payments_statistics.destroy();
    }

    if (typeof(monthly_payments_statistics) !== 'undefined') {
        monthly_payments_statistics.destroy();
    }

    console.log(type);

    if (type == 'weekly') {
        init_weekly_payment_statistics();
    } else if (type == 'monthly') {
        init_monthly_payment_statistics();
    }

}

function update_tickets_report_table(el) {
    var $el = $(el);
    var type = $el.data('type')
    $('#tickets-report-mode-name').text($el.text())

    $('#tickets-report-table-wrapper').load(admin_url + 'dashboard/ticket_widget/' + type, function(data) {
        $('.table-ticket-reports').dataTable().fnDestroy()
        initDataTableInline('.table-ticket-reports')
    });
    return false
}

document.addEventListener('DOMContentLoaded', function () {
    
    //setTimeout(function(){
    
    if (document.body.classList.contains('dashboard') && !document.body.classList.contains('team_management')&& !document.body.classList.contains('hr_profile')) {

          
  isVisible(function(){
    if(!isVisible()) {
      // Tab is not active, show the indicator
      document.getElementById('snooze-indicator').style.display = 'block';
    } else {
      // Tab is active, hide the indicator
      document.getElementById('snooze-indicator').style.display = 'none';
    }
  });

                //Start of the view Scripting
                var timerInterval;
                var clockedIn = false;
                var startTime;
                var csrf_token_name = csrfData.token_name;
                var csrf_token = csrfData.hash;

                

                const clockInBtn = document.getElementById('clock-in');
                const clockOutBtn = document.getElementById('clock-out');
                const statusSelect = document.getElementById('status');

                const liveTimer = document.getElementById('live-timer');
                const todayTimer = document.getElementById('today-timer');
                const yesterdayTimer = document.getElementById('yesterday-timer');
                const currentWeekTimer = document.getElementById('current-week-timer');
                const lastWeekTimer = document.getElementById('last-week-timer');

                function convertDateTimeZone(getDateObject) {

                    let timeZone = 'Asia/Kolkata';

                    var options = { timeZone: timeZone, hour: 'numeric', minute: 'numeric', second: 'numeric' };
                    var localTime = getDateObject.toLocaleString('en-US', options);
                    var localTimeArray = localTime.split(/[:\s]/);
                    var localDate = new Date(getDateObject.toLocaleDateString('en-US', { timeZone: timeZone }));
                    
                    //Convert the hours to 24-hour format if needed
                    if (localTimeArray[3] === 'PM') {
                        localTimeArray[0] = parseInt(localTimeArray[0], 10) + 12;
                    }
                    
                    localDate.setHours(localTimeArray[0], localTimeArray[1], localTimeArray[2]);
                    return localDate;
                }

                function getCurrentTimeInAsiaKolkata() {
                    const now = new Date();
                    const timeZone = 'Asia/Kolkata';
                    const localTimeString = now.toLocaleString('en-US', { timeZone });
                  
                    return new Date(localTimeString);
                }

                function updateLiveTimer() {
                    if (clockedIn) {

                        var currentTime = getCurrentTimeInAsiaKolkata();

                        var elapsedTime = Math.floor((currentTime - startTime) / 1000) + 5;

                        //console.log(currentTime);
                        //console.log(startTime);

                        document.getElementById('live-timer').textContent = formatTime(elapsedTime );
                    }
                }
                function fetchStats() {

                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', admin_url + 'team_management/fetch_stats', true);

                    xhr.onload = function() {

                    if (this.status === 200) {

                        var stats = JSON.parse(this.responseText);

                        liveTimer.textContent = formatTime(stats.total_time);
                        todayTimer.textContent = formatTime(stats.todays_total_time);
                        yesterdayTimer.textContent = formatTime(stats.yesterdays_total_time);
                        currentWeekTimer.textContent = formatTime(stats.this_weeks_total_time);
                        lastWeekTimer.textContent = formatTime(stats.last_weeks_total_time);

                        if(stats.status == "Online"){
                            if (stats.clock_in_time) {
                                clockInBtn.disabled = true;
                                clockInBtn.style.opacity = 0.7;
                                clockOutBtn.disabled = false;
                                clockOutBtn.style.opacity = 1;
                                clockedIn = true;
                                console.log(stats.clock_in_time);
                                startTime = new Date(stats.clock_in_time);
                                timerInterval = setInterval(updateLiveTimer, 1000);
                            }else{
                                clockInBtn.disabled = false;
                                clockInBtn.style.opacity = 1;
                                clockOutBtn.disabled = true;
                                clockOutBtn.style.opacity = 0.7;
                                clearInterval(timerInterval);
                                clockedIn = false;
                            }

                        }else{
                            clockInBtn.disabled = true;
                            clockInBtn.style.opacity = 0.7;
                            clockOutBtn.disabled = true;
                            clockOutBtn.style.opacity = 0.7;
                            clearInterval(timerInterval);
                            clockedIn = false;
                           
                        }

                        if(stats.status == "Leave"){
                            document.getElementById('Online').disabled = true;
                            document.getElementById('AFK').disabled = true;
                            document.getElementById('Offline').disabled = true;
                            document.getElementById('Leave').disabled = false;
                        }else{
                            document.getElementById('Online').disabled = false;
                            document.getElementById('AFK').disabled = false;
                            document.getElementById('Offline').disabled = false;
                            document.getElementById('Leave').disabled = true;
                        }
                       

                        statusSelect.value = stats.status;
                        statusSelectColors(statusSelect);
                    } else {
                        alert('Unable to fetch stats. Please try again.');
                    }
                };
           
                xhr.send();
            }


                    clockInBtn.addEventListener('click', () => {
                    
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Clocking in, please wait.',
                            timerProgressBar: true,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
            
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', admin_url + 'team_management/clock_in');
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                        xhr.onreadystatechange = function() {
                            if (this.readyState == 4 && this.status == 200) {
                                var response = JSON.parse(this.responseText);
                                if (response.success) {

                                    Swal.close();
                                    Swal.fire('Success!', 'Successfully clocked in.', 'success');

                                    clockedIn = true;
                                    fetchStats();
                                    clockInBtn.disabled = true;
                                    timerInterval = setInterval(updateLiveTimer, 1000);
                                } else {
                                    Swal.fire('Error!', 'Unable to clock in. Please try again.', 'error');
                                }
                            }
                        };
                        var requestData = csrf_token_name + '=' + encodeURIComponent(csrf_token);
                        xhr.send(requestData);

                    });

                clockOutBtn.addEventListener('click', () => {

                    Swal.fire({
                        title: 'Processing...',
                        text: 'Clocking out, please wait.',
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', admin_url + 'team_management/clock_out');
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                    xhr.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            var response = JSON.parse(this.responseText);
                            Swal.close();
                            if (response.success) {
                                Swal.fire('Success!', 'Successfully clocked out.', 'success')
                                    .then((result) => {
                                        // After user acknowledges success, ask them if they want to change status to offline
                                        Swal.fire({
                                            title: 'Change Status?',
                                            text: 'Do you want to change your status to offline too?',
                                            icon: 'question',
                                            showCancelButton: true,
                                            confirmButtonText: 'Yes, change it!',
                                            cancelButtonText: 'No'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                // If user confirms, change status to offline
                                                changeStatusToOffline();
                                            }
                                        });
                                    });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        }
                    };
                
                    // Include the CSRF token in the request data
                    var requestData = csrf_token_name + '=' + encodeURIComponent(csrf_token);
                    xhr.send(requestData);
                });

                function changeStatusToOffline() {
                    var statusText = 'Offline';

                    Swal.fire({
                        title: 'Processing...',
                        text: 'Changing status, please wait.',
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                
                    
                    //Backend Timers
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', admin_url + 'team_management/update_status');
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                    xhr.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            var response = JSON.parse(this.responseText);
                            Swal.close();
                            if (!response.success) {
                                Swal.fire('Failed!', `Could not change status`, 'error');
                            } else {
                                Swal.fire('Success!', `Status set to ${statusText}.`, 'success');
                            }
                            fetchStats();
                        }
                    };
                
                    // Include the CSRF token and status in the request data
                    var requestData = csrf_token_name + '=' + encodeURIComponent(csrf_token) + '&statusValue=' + encodeURIComponent(statusText);
                    xhr.send(requestData);
                }

                function formatTime(seconds) {
                    const hours = Math.floor(seconds / 3600);
                    seconds %= 3600;
                    const minutes = Math.floor(seconds / 60);
                    const remainingSeconds = seconds % 60;
                
                    return hours.toString().padStart(2, '0') + ':' +
                           minutes.toString().padStart(2, '0') + ':' +
                           remainingSeconds.toString().padStart(2, '0');
                }

                
                
                let previousValue = statusSelect.value;
                statusSelect.addEventListener('change', (event) => {

                    Swal.fire({
                        title: 'Processing...',
                        text: 'Changing status, please wait.',
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    var statusText = statusSelect.value;

                    
                    
                    if (statusSelect != previousValue) {

                        
                        //Backend Timers
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', admin_url + 'team_management/update_status');
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
                        xhr.onreadystatechange = function () {
                            if (this.readyState == 4 && this.status == 200) {
                                var response = JSON.parse(this.responseText);
                                Swal.close();
                                if (!response.success) {
                                    alert('Unable to update status. Please try again.');
                                    Swal.fire(
                                        'Failed!',
                                        `Could not change status`,
                                        'error'
                                    );
                                }else{
                                    Swal.fire(
                                        'Success!',
                                        `Status set to ${statusText}.`,
                                        'success'
                                    );
                                }
                                fetchStats();
                            }
                        };
                
                        // Include the CSRF token and status in the request data
                        var requestData = csrf_token_name + '=' + encodeURIComponent(csrf_token) + '&statusValue=' + encodeURIComponent(statusText);
                        xhr.send(requestData);
                    }
                });
                

                fetchStats();

                //Setting current shift status
                
                var xhrShift = new XMLHttpRequest();
                xhrShift.open("GET", admin_url + "team_management/get_shift_status", true);
                xhrShift.responseType = "json";
                xhrShift.onload = function () {
                    if (xhrShift.status === 200) {
                        
                        var response = xhrShift.response;
                        var shiftInfo = "";
                        if (response.status == 0) {
                            shiftInfo = "It's shift time. Time remaining: " + response.time_left;
                        } else if (response.status == 1) {
                            shiftInfo = "Upcoming shift in " + response.time_left;
                        } else {
                            shiftInfo = "No shift currently.";
                        }
                        document.getElementById("shiftInfo").textContent = shiftInfo;
                    } else {
                        console.error("Error retrieving shift information:", xhrShift.statusText);
                    }
                };
                xhrShift.onerror = function (error) {
                    console.error("Error retrieving shift information:", error);
                };
                xhrShift.send();
 


                const timezones = [
                    { name: 'India', timeZone: 'Asia/Kolkata' },
                    { name: 'USA(Central)', timeZone: 'America/Chicago' },
                    { name: 'Pakistan', timeZone: 'Asia/Karachi' },
                    { name: 'Bangladesh', timeZone: 'Asia/Dhaka' },
                ];

                function updateClocks() {
                    const clocksElement = document.getElementById('clocks');
                    clocksElement.innerHTML = '';
                                
                    for (const timezone of timezones) {
                        const date = new Date();
                        const formatter = new Intl.DateTimeFormat('en-US', {
                            timeZone: timezone.timeZone,
                            dateStyle: 'full',
                            timeStyle: 'medium',
                        });
                                
                        const formattedDate = formatter.format(date);
                        const clockElement = document.createElement('div');
                        clockElement.className = 'bg-gray-100 p-4 shadow rounded text-gray-700 font-semibold flex flex-col justify-between gap-2';
                        clockElement.innerHTML = `<div class="text-gray-500">${timezone.name}:</div> <div class="font-mono">${formattedDate}</div>`;
                        clocksElement.appendChild(clockElement);
                    }
                }
                
                
                updateClocks();
                setInterval(updateClocks, 1000);
                getOrSaveStaffSummary();

            }
        });



function statusSelectColors(element){
    element.classList.remove('text-lime-500');
    element.classList.remove('text-blue-500');
    element.classList.remove('text-pink-500');
    element.classList.add(element.options.namedItem(element.value).classList.item(0));
}


function updateTaskValues(staffId, newTask, isCompleted) {
    const totalTasksElem = document.querySelector('#user-' + staffId + '-rate-a');
    const completedTasksElem = document.querySelector('#user-' + staffId + '-rate-c');
    const percentageElem = document.querySelector('#user-' + staffId + '-rate-p');

    const totalTasks = parseInt(totalTasksElem.textContent) + (newTask ? 1 : 0);
    const completedTasks = parseInt(completedTasksElem.textContent) + (isCompleted ? 1 : 0);
    const percentage = totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0;

    totalTasksElem.textContent = totalTasks;
    completedTasksElem.textContent = completedTasks;
    percentageElem.textContent = percentage;
}

function getOrSaveStaffSummary(summary = null) {
    var today = new Date();
    var selectedDate = new Date(document.getElementById("summary_date").value);
    var diffInDays = Math.round((today - selectedDate) / (1000 * 60 * 60 * 24));

    // If selected date is not yesterday or day before yesterday
    if (diffInDays > 2) {
        document.getElementById("summary-textarea").readOnly = true;
    } else {
        document.getElementById("summary-textarea").readOnly = false;
    }

    var csrf_token_name = csrfData.token_name;
    var csrf_token = csrfData.hash;
    var date = document.getElementById("summary_date").value;

    $.ajax({
        url: 'team_management/staff_summary',
        type: 'POST',
        data: { summary: summary, date, csrf_token_name: csrf_token },
        success: function(response) {
            if (!summary) {
                document.querySelector('#summary-textarea').value = response;
            } else {
                alert_float("success", "Summary Saved!");
            }
        },
        error: function() {
            alert('Error fetching or saving the summary.');
        }
    });
}

function processShiftData(staffShifts) {
    //const timeLabels = new Set();
    const dotData = {};
  
    staffShifts.forEach((shift) => {
      const startTime = new Date(shift.shift_start_time);
      const endTime = new Date(shift.shift_end_time);
  
      const startHour = startTime.getHours();
      const endHour = endTime.getHours();
  
      
  
      if (!dotData[startHour]) {
        dotData[startHour] = [];
      }
      dotData[startHour].push({ name: shift.staff_name, type: 'Start' });
  
      if (!dotData[endHour]) {
        dotData[endHour] = [];
      }
      dotData[endHour].push({ name: shift.staff_name, type: 'End' });
    });

    const timeLabels = Array.from({ length: 13 }, (_, i) => i * 2);
  
    return {
      timeLabels: Array.from(timeLabels).sort((a, b) => a - b),
      dotData,
    };
}
  
function createTimeLabelElement(hour) {
    const div = document.createElement('div');
    div.classList.add('text-sm');
    div.textContent = hour >= 12 ? `${hour - 12 || 12} PM` : `${hour} AM`;
    return div;
}
  
function createDotElement(hour, data, timeLabels) {
    const dotContainer = document.createElement('div');
    dotContainer.classList.add(
      'dot-container',
      'absolute',
      'top-0',
      'transform',
      '-translate-x-1/2',
      '-translate-y-[6px]'
    );
    
    dotContainer.style.left = `${((hour - timeLabels[0]) / (timeLabels[timeLabels.length - 1] - timeLabels[0])) * 100}%`;
  
    const dot = document.createElement('div');
    dot.classList.add('dot', 'w-4', 'h-4', 'bg-blue-500', 'rounded-full', 'hover:bg-blue-700', 'cursor-pointer');
    dotContainer.appendChild(dot);
  
    const info = document.createElement('div');
    info.classList.add('info', 'hidden', 'mt-4', 'bg-white', 'text-sm', 'text-gray-700', 'p-4', 'rounded-lg', 'shadow-lg', 'border', 'border-gray-200', 'transition', 'duration-200', 'ease-in-out');
    data.forEach((text) => {
        const p = document.createElement('p');
        p.classList.add('mb-2', 'flex', 'items-center', 'space-x-1');
    
        // Create an icon element (using Font Awesome icons)
        const icon = document.createElement('i');
        icon.classList.add('text-blue-500', text.type === 'Start' ? 'fas' : 'far', 'fa-clock');
        p.appendChild(icon);
    
        const name = document.createElement('span');
        name.textContent = text.name;
        name.classList.add('font-semibold');
        p.appendChild(name);
    
        const type = document.createElement('span');
        type.textContent = text.type;
        type.classList.add('font-light', 'text-gray-600');
        p.appendChild(type);
    
        info.appendChild(p);
    });
    dotContainer.appendChild(info);

    const arrow = document.createElement('div');
    arrow.classList.add('absolute', 'w-3', 'h-3', 'bg-white', 'border', 'border-gray-200', 'shadow', 'transform', 'rotate-45', 'bottom-full', 'left-1/2', 'translate-x-[-50%]');
    info.appendChild(arrow);
  
    dotContainer.addEventListener('mouseenter', () => {
      info.classList.remove('hidden');
    });
    dotContainer.addEventListener('mouseleave', () => {
      info.classList.add('hidden');
    });
  
    return dotContainer;
}

var isVisible = (function() {
    var stateKey, eventKey, keys = {
      hidden: "visibilitychange",
      webkitHidden: "webkitvisibilitychange",
      mozHidden: "mozvisibilitychange",
      msHidden: "msvisibilitychange"
    };
    for (stateKey in keys) {
      if (stateKey in document) {
        eventKey = keys[stateKey];
        break;
      }
    }
    return function(c) {
      if (c) document.addEventListener(eventKey, c);
      return !document[stateKey];
    }
  })();

  $(document).ready(function() {
        fetchDailyInfos();
    });

</script>
