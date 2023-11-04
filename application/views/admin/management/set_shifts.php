<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); 
$dateObj   = DateTime::createFromFormat('!m', $month);
$monthName = $dateObj->format('F');
?>

<style>
.leave-day {
    background-color: #ff9f89; /* Or any color you want */
    /* You might need additional styles depending on your layout */
}

</style>

<div id="wrapper" class="wrapper">
    <div class="content flex flex-col">
        <div class="w-full rounded-[40px] bg-<?= get_option('management_theme_background')?> text-gray-700 p-6 flex flex-row justify-between border border-solid border-sky-100 hover:border-<?= get_option('management_theme_border')?> transition-all items-center text-xl">
            Settings shifts of <?= $staff->full_name ?> for the month of <?= $monthName ?>
            
            <div class="flex flex-row gap-4">
                <button class="px-4 py-2 bg-<?= get_option('management_theme_foreground')?> rounded-xl text-black hover:bg-<?= get_option('management_theme_hover')?> transition-all text-lg" onclick="copyMonthTimings()">Copy Previous month timings</button>

                <a href="<?= admin_url("team_management/staff_shifts") ?>" class="px-4 py-2 bg-<?= get_option('management_theme_foreground')?> rounded-xl hover:bg-<?= get_option('management_theme_hover')?> transition-all text-lg">Back</a>
            </div>
        </div>

        <div class="col-md-12 mt-7">
            <!-- Calendar Section -->
            <div class="w-3/3 rounded-[50px] bg-white p-4 shadow-lg hover:shadow-xl border border-solid border-white hover:border-<?= get_option('management_theme_border')?> transition-all">
                <div class="p-4 cal_container">
                    <div id="shifts_calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Calendar DATE MODAL -->

<div class="modal fade" id="shiftsModal" tabindex="-1" aria-labelledby="shiftsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-xl" id="shiftsModalLabel">Setting Shifts of <div id="dateLabel"></div></h5>
            </div>
            <div class="modal-body">
    
            <form id="shiftsForm"> 

                    <input type="hidden" class="form-control" id="dateInput" >

                    <div class="flex flex-col p-2 gap-2">      
                        <div class="flex flex-row gap-2 ">

                            <div class="w-1/2">
                                <label for="s1s">Shift 1 Start</label>
                                <input  <?= !$is_editable ? 'readonly' : '' ?> type="time" class="form-control" id="s1s">
                            </div>
                            <div class="w-1/2">
                                <label for="s1e">Shift 1 End</label>
                                <input <?= !$is_editable ? 'readonly' : '' ?> type="time" class="form-control" id="s1e" >
                            </div>

                        </div>

                        <div class="flex flex-row gap-2">

                            <div class="w-1/2">
                                <label for="s2s">Shift 2 Start</label>
                                <input <?= !$is_admin ? 'readonly' : '' ?> type="time" class="form-control"  id="s2s">
                            </div>
                            <div class="w-1/2">
                                <label for="s2e">Shift 2 End</label>
                                <input <?= !$is_admin ? 'readonly' : '' ?> type="time" class="form-control" id="s2e" >
                            </div>

                        </div>

                        <div class="flex flex-col mt-4 w-full">

                                <label for="repeatSelectBox">Repeat:</label>
                                <select class="form-control w-full" id="repeatSelectBox" name="repeat">
                                    <option value="">None</option>
                                    <option value="allFollowing">All following dates of month</option>
                                    <option id="weekdayOption" value="allSameWeekday">All</option>
                                    <!-- More options here -->
                                    <option value="allDaysMonth">All days in the month</option>
                                </select>
                        </div>

                    </div>

            </form>

            </div>

            <div class="modal-footer flex justify-between">

                <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveShifts" data-staff-id="" onclick="setShifts();" <?= !$is_editable ? 'disabled' : '' ?>>Save Changes</button>
                </div>
                
            </div>
        </div>
    </div>
</div>





<?php init_tail(); ?>

<!-- FullCalendar JS and dependencies -->

<script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>

<script>

function openShiftsModal(date, shiftsData) {

    const day = date.toLocaleString('en-us', { weekday: 'long' });

    // Clear previous values
    document.querySelector('#s1s').value = '';
    document.querySelector('#s1e').value = '';
    document.querySelector('#s2s').value = '18:00'; // Default values for shift 2 start
    document.querySelector('#s2e').value = '22:00'; // Default values for shift 2 end

    // Iterate through shiftsData to fill the respective inputs based on shift number
    shiftsData.forEach(shiftData => {
        if (shiftData.shiftNumber === "1") {
            document.querySelector('#s1s').value = shiftData.shiftStart.toISOString().substring(11,16);
            document.querySelector('#s1e').value = shiftData.shiftEnd.toISOString().substring(11,16);
        } else if (shiftData.shiftNumber === "2") {
            document.querySelector('#s2s').value = shiftData.shiftStart.toISOString().substring(11,16);
            document.querySelector('#s2e').value = shiftData.shiftEnd.toISOString().substring(11,16);
            // console.log(shiftData.shiftEnd.toISOString());
        }
    });

    // Show the modal
    $('#shiftsModal').modal('show');

    var localDateString = FullCalendar.formatDate(date, {
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit', 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit', 
        timeZoneName: 'short'
    });


    document.getElementById('weekdayOption').innerHTML = 'All '+day+'s';
    document.getElementById('dateInput').value = localDateString.split(',')[0];
    document.getElementById('dateLabel').innerHTML = localDateString.split(',')[0];
}



var calendarEl = document.getElementById('shifts_calendar');
var shifts = <?php echo json_encode($shifts); ?>;

var events = shifts
    .filter(function(shift) {
        // Check that both start and end times are not null or undefined
        return shift.shift_start_time && shift.shift_end_time;
    })
    .map(function(shift) {
        return {
            title: 'Shift ' + shift.shift_number + ': ' + shift.shift_start_time + ' - ' + shift.shift_end_time,
            start: new Date(shift.Year, shift.month - 1, shift.day, shift.shift_start_time.split(':')[0], shift.shift_start_time.split(':')[1]),
            end: new Date(shift.Year, shift.month - 1, shift.day, shift.shift_end_time.split(':')[0], shift.shift_end_time.split(':')[1]),
            allDay: false, // will make the time show
            shiftNumber: shift.shift_number,
            shiftStart: new Date(Date.UTC(shift.Year, shift.month - 1, shift.day, shift.shift_start_time.split(':')[0], shift.shift_start_time.split(':')[1])),
            shiftEnd: new Date(Date.UTC(shift.Year, shift.month - 1, shift.day, shift.shift_end_time.split(':')[0], shift.shift_end_time.split(':')[1])),
        };
    });


var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    events: events,
    height: "70vh",
    dateClick: function(info) {
        handleClick(info.date); 
    },
    eventClick: function(info) {
       handleClick(info.event.start); 
    },
    initialDate: <?= strtotime(date('Y') . '-' . $month . '-' . '03') * 1000 ?>,
});


calendar.render();

function handleClick(info){
    const clickedDate = info;

        var events = calendar.getEvents().filter(function(event) {
            // FullCalendar stores dates in a specific format, so you need to format the clicked date
            // and the event dates to compare them accurately
            var eventStart = event.start;
            return eventStart && FullCalendar.formatDate(eventStart, {year: 'numeric', month: '2-digit', day: '2-digit'}) ===
                                FullCalendar.formatDate(clickedDate, {year: 'numeric', month: '2-digit', day: '2-digit'});
        });

        var shiftsData = events.map(function(event) {
            return {
                shiftStart: event.extendedProps.shiftStart,
                shiftEnd: event.extendedProps.shiftEnd,
                shiftNumber: event.extendedProps.shiftNumber
            };
        });

        console.log(shiftsData);
        
        openShiftsModal(clickedDate, shiftsData);
}

var staff_id = <?= $staff->staffid ?>;
function setShifts(){
    var shift_1_start = $("#s1s").val();
    var shift_1_end = $("#s1e").val();

    var shift_2_start = $("#s2s").val();
    var shift_2_end = $("#s2e").val();

    var date = $("#dateInput").val();

    var repeat = $("#repeatSelectBox").val();

    

    $.ajax({
        url: '<?= admin_url("team_management/save_shifts") ?>',
        type: 'POST',
        data: { 
            date: date,
            repeat: repeat,
            s1s : shift_1_start,
            s1e: shift_1_end,
            s2s : shift_2_start,
            s2e: shift_2_end,
            staff_id : staff_id
        },
        dataType: 'json',
        success: function(staffStats) {
            location.reload();
        }
    });
}

function copyMonthTimings(){
    Swal.fire({
        title: 'Copy Timings?',
        text: 'This will replace all your current month\'s shifts with previous one?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Import!',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= admin_url("team_management/copy_shifts") ?>',
                type: 'POST',
                data: { 
                    month: <?= $month ?>,
                    staff_id : staff_id
                },
                dataType: 'json',
                success: function(staffStats) {
                    location.reload();
                }
            });
        }
    });
}
<?php
foreach($leave_dates as $date){
    echo '$(`.fc-day[data-date="2023-11-11"]`).css(`background`, `#fff28f`);
    ';
}
?>
</script>
