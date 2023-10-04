<?php 
function adf($seconds) {
    $hours = floor($seconds / 3600);
    $seconds %= 3600;
    $minutes = floor($seconds / 60);
    $remainingSeconds = $seconds % 60;

    return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' .
           str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':' .
           str_pad($remainingSeconds, 2, '0', STR_PAD_LEFT);
}

?>

<style>
.dot-container {
  transition: all 0.2s ease;
}

.dot {
  transition: all 0.2s ease;
}

.dot:hover + .info {
  display: block;
}

.info {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  white-space: nowrap;
}


</style>

<div class="bg-white shadow rounded-lg p-4 my-4 flex md:flex-row flex-col justify-between relative overflow-hidden">

    <div id="snooze-indicator" class="absolute w-full min-h-full top-0 left-0 bg-[#000]/70 z-10" style="display:none;">
        <div class="flex w-full h-full justify-center items-center my-auto absolute">
            <h3 class="text-xl text-white" style="font-family:monospace;">Tab is snoozed, Pls refresh ;</h3>
        </div>
    </div>

    <div class="md:w-3/5 w-full">

        <div class="flex items-center md:flex-row flex-col">
            <?php echo staff_profile_image($GLOBALS['current_user']->staffid, ['h-full', 'w-32' , 'object-cover', 'md:mr-4' , 'md:ml-0 mx-auto self-start' , 'staff-profile-image-thumb'], 'thumb') ?>
            <div class="flex flex-col gap-1 md:items-start items-center">

                <div class="text-xl font-semibold flex flex-row justify-between">

                    <div class="flex items-center">Hi, <?php echo $GLOBALS['current_user']->firstname; ?>! 👋</div>                    

                </div>
                <p class="text-lg">Welcome to your dashboard.</p>

                <div class="w-fit flex flex-row justify-between border border-slate-300 border-double pl-2 text-lg rounded shadow-md transition-all hover:shadow-none">

                    <div class="pr-2">Status: </div>

                    <select class="px-2 bg-transparent text-lime-500 appearance-none cursor-pointer " onchange="statusSelectColors(this);" id="status">
                        <option id="Online" value="Online" class="text-lime-500">Online</option>
                        <option id="AFK" value="AFK" class="text-blue-500">AFK</option>
                        <option id="Offline" value="Offline" class="text-pink-500">Offline</option>
                        <option id="Leave" value="Leave" class="text-amber-600">Leave</option>
                    </select>


                </div>

                <div class="my-2" id="shiftInfo">Upcoming Shift: </div>

                <div class="flex flex-row gap-2">
                    <button class="px-2 py-1 text-base bg-blue-600 rounded text-white transition-all shadow-lg hover:shadow-none" id="clock-in">Clock in</button>
                    <button class="px-2 py-1 text-base bg-blue-600 rounded text-white transition-all shadow-lg hover:shadow-none" id="clock-out">Clock Out</button>
                </div>
            </div>
        </div>
    </div>

    <div class="md:w-2/5 w-full flex flex-col md:text-right text-center md:mt-0 mt-5 justify-between">

        <div>
            <h2 class="text-xl font-semibold" id="live-timer">
                    <?php //echo formatTime($stats->total_time); ?>
            </h2>
        </div>

        <div class="flex flex-col gap-2">
            <h3 class="text-md">Today: <span id="today-timer"></span></h3>
            <h3 class="text-md">Yesterday: <span id="yesterday-timer"></span></h3>
            <h3 class="text-md">This Week: <span id="current-week-timer"></span></h3>
            <h3 class="text-md">Last Week: <span id="last-week-timer"></span></h3>
        </div>
    </div>
</div>

    <!-- <div class="bg-white shadow rounded-lg p-4 my-4 flex md:flex-row flex-col justify-between">
        <div id="visualization" class="relative w-full">
        </div>
    </div> -->


<script>

function fetchDailyInfo(staff_id) {

const currentDate = new Date();
const month = <?= $thisMonth; ?>;
const day = <?= $thisDay; ?>;
const year = currentDate.getFullYear();

const monthStr = month < 10 ? `0${month}` : `${month}`;
const dayStr = day < 10 ? `0${day}` : `${day}`;

const startDate = new Date(`${year}-${monthStr}-${dayStr}T00:00:00`);
const endDate = new Date(`${year}-${monthStr}-${dayStr}T23:59:59`);


$.ajax({
    url: admin_url + 'team_management/fetch_daily_info/',
    type: 'POST',
    data: {
        staff_id: staff_id,
        day: day,
        month: month,
        year: year,
        [csrfData.token_name]: csrfData.hash,
    },
    dataType: 'json',
    success: function (data) {

        Swal.close();
        
        console.log(data);
        console.log('Total clocked-in time:', data.total_clocked_in_time);
        console.log('Total shift duration:', data.total_shift_duration);
      
        $("#stats_daily_title").html(" :: " + day + "/" + month + "/" + <?= date('Y') ?>);

        $('#total_clock_in_time_day').html(data.total_clocked_in_time);
        $('#total_shift_duration').html(data.total_shift_duration);
        $('#total_task_time').html(data.total_task_time);

        $('#total_no_tasks_day').html(data.total_no_tasks + " tasks");
        $('#total_completed_tasks_day').html(data.total_completed_tasks + " tasks");
        $('#tasks_rate_day').html(data.tasks_rate + "%");


        $('#on_leave').html(data.on_leave ? 'Yes' : 'No');

        const afk_entries = data.afk_and_offline.filter(entry => entry.status === 'AFK');
        const offline_entries = data.afk_and_offline.filter(entry => entry.status === 'Offline');

        const monthDigit = month.toLocaleString('en-US', {
            minimumIntegerDigits: 2,
            useGrouping: false
        });

        const afk_rows = generateStatusRows(afk_entries);
        const offline_rows = generateStatusRows(offline_entries);
        const tasks_rows = generateTasksRows(data.task_timers);
        const all_tasks_rows = generateAllTasksRows(data.all_tasks, year+"-"+monthDigit+"-"+day);

        (afk_rows != "") ? $('#afk_time_table').html(afk_rows) : $('#afk_time_table').html("<tr><td colspan='3' class='px-4 py-2'>No Data</td></tr>");
        
        (offline_rows != "") ? $('#offline_time_table').html(offline_rows) : $('#offline_time_table').html("<tr><td colspan='3' class='px-4 py-2'>No Data</td></tr>");

        (tasks_rows != "") ? $('#tbl_tasks_activity').html(tasks_rows) : $('#tbl_tasks_activity').html("<tr><td colspan='4' class='px-4 py-2'>No Data</td></tr>");

        (all_tasks_rows != "") ? $('#tbl_all_tasks').html(all_tasks_rows) : $('#tbl_all_tasks').html("<tr><td colspan='4' class='px-4 py-2'>No Data</td></tr>");


        var items = new vis.DataSet();
// var options = {
//   zoomMin: 1000 * 60 * 60,  // One hour in milliseconds
//   zoomMax: 1000 * 60 * 60 * 24  // One day in milliseconds
// };
var options = {
  zoomMin: 1000 * 60 * 60,  // One hour in milliseconds
  zoomMax: 1000 * 60 * 60 * 24,  // One day in milliseconds
  format: {
    minorLabels: function(date, scale, step) {
      return moment(date).format('hh:mm A'); // Time in AM/PM
    },
    majorLabels: function(date, scale, step) {
      return moment(date).format('MMM DD YYYY'); // Date in a good looking format
    }
  }
};
var container = document.getElementById('visualization');
if (container) {
  var timeline = new vis.Timeline(container, items, options);
} else {
  console.error("Timeline container not found");
  return;
}

// Debug
console.log("Data:", data);
console.log("AFK Entries:", afk_entries);
console.log("Start Date:", startDate);
console.log("End Date:", endDate);
console.log("Items:", items);

if (data.clock_ins_outs) {
    const visJsData = [];
    
    data.clock_ins_outs.forEach(clock => {
        const inTime = new Date(clock.clock_in).toISOString();
        const outTime = new Date(clock.clock_out).toISOString();
        
        const item = {
            content:' Clock in',
            start: inTime,
            end: outTime,
            type: 'range',
            className: 'clock-in-time',
            group: 2 // Group 2 will contain all clock-ins
        };

        console.log("Item:", item);
        visJsData.push(item);
    });

    console.log("VisJs Data:", visJsData);
    
    // Add to timeline
    items.add(visJsData);
}



if (afk_entries) {
  afk_entries.forEach(function (entry) {
    // Convert '08:44 PM' format to ISO 8601
    const today = new Date();
    const startDateString = `${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()} ${entry.start_time}`;
    const endDateString = `${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()} ${entry.end_time}`;
    const startDate = new Date(startDateString).toISOString();
    const endDate = new Date(endDateString).toISOString();

    items.add({
      content: 'AFK',
      start: startDate,
      end: endDate,
      type: 'range',
      className: 'afk-time',
      group:1
    });
  });
} else {
  console.warn("afk_entries is not available");
}

// Setting the focus
if (startDate && endDate) {
  timeline.setWindow(startDate, endDate);
} else {
  console.warn("Start and end dates not properly set");
}

    $('#statsModal').modal('show');  
    },
    error: function (jqXHR, textStatus, errorThrown) {
        console.error('Error fetching daily stats:', textStatus, errorThrown);
    }
});
}


function generateStatusRows(entries) {
    let rows = '';
    entries.forEach(entry => {
        rows += `
        <tr>
            <td class="px-4 py-2">${entry.start_time}</td>
            <td class="px-4 py-2">${entry.end_time}</td>
            <td class="px-4 py-2">${(entry.duration)}</td>
        </tr>`;
    });
    return rows;
}

function generateTasksRows(entries) {
    let rows = '';
    entries.forEach(entry => {
 

        if(entry.project_id != null){
            rows += `
            <tr>
                <td class="px-4 py-2 flex flex-col">
                    <a target="_blank" onclick="init_task_modal(${entry.task_id}); return false" href="#" class="text-sm">${entry.task_name}</a>
                    <a target="_blank" href="<?= admin_url(); ?>projects/view/${entry.project_id}" class="text-xs">${entry.project_name}</a>
                </td>
                <td class="px-4 py-2">${entry.start_time}</td>
                <td class="px-4 py-2">${entry.end_time}</td>
                <td class="px-4 py-2">${(entry.duration)}</td>
            </tr>
            `;
        }else{
            rows += `
            <tr>
                <td class="px-4 py-2 flex flex-col">
                    <a target="_blank" href="#" onclick="init_task_modal(${entry.task_id}); return false" class="text-sm">${entry.task_name}</a>
                </td>
                <td class="px-4 py-2">${entry.start_time}</td>
                <td class="px-4 py-2">${entry.end_time}</td>
                <td class="px-4 py-2">${(entry.duration)}</td>
            </tr>
            `;
        }
        
    });
    return rows;
}

function generateAllTasksRows(entries, date) {
    let rows = '';

    let today = new Date();
    today = new Date(today.getFullYear(), today.getMonth(), today.getDate());


    entries.forEach(entry => {

        let taskBG = "";

        let assignedDate = new Date(entry.Assigned_Date);
        assignedDate = new Date(assignedDate.getFullYear(), assignedDate.getMonth(), assignedDate.getDate());

        let dueDate = new Date(entry.duedate);
        //dueDate = new Date(dueDate.getFullYear(), dueDate.getMonth(), dueDate.getDate());

        if(entry.Completed_Date){
            let completedDate = new Date(entry.Completed_Date);
            completedDate = new Date(completedDate.getFullYear(), completedDate.getMonth(), completedDate.getDate());
            console.log("dueDate",dueDate.getTime());
            console.log("completedDate",completedDate.getTime());
            if(dueDate.getTime() >= completedDate.getTime()){
                taskBG = "bg-emerald-100/70";
            } else {
                taskBG = "bg-red-100/70";
            }
        } else {
            if(dueDate.getTime() >= today.getTime()){
                taskBG = "bg-gray-100/70";
            } else {
                taskBG = "bg-red-100/70";
            }
        }
        

        if(entry.project_id != null){
            rows += `
            <tr class="transition-all hover:`+taskBG+`">
                <td class="px-4 py-2">${(entry.task_id)}</td>
                <td class="px-4 py-2 flex flex-col">
                    <a target="_blank" href="#" onclick="init_task_modal(${entry.task_id}); return false" class="text-sm">${entry.task_name}</a>
                    <a target="_blank" href="<?= admin_url(); ?>projects/view/${entry.project_id}" class="text-xs">${entry.project_name}</a>
                </td>
                <td class="px-4 py-2">${(entry.Assigned_Date)}</td>
                <td class="px-4 py-2">${(entry.duedate)}</td>
                <td class="px-4 py-2">${(entry.Completed_Date)}</td>
                <td class="px-4 py-2">${(entry.Total_Time_Taken)}</td>
                <td class="px-4 py-2">${(entry.Days_Offset)}</td>
            </tr>
            `;
        }else{
            rows += `
            <tr class="transition-all hover:`+taskBG+`">
                <td class="px-4 py-2">${(entry.task_id)}</td>
                <td class="px-4 py-2 flex flex-col">
                    <a target="_blank" href="#" onclick="init_task_modal(${entry.task_id}); return false" class="text-sm">${entry.task_name}</a>
                </td>
                <td class="px-4 py-2">${(entry.Assigned_Date)}</td>
                <td class="px-4 py-2">${(entry.duedate)}</td>
                <td class="px-4 py-2">${(entry.Completed_Date)}</td>
                <td class="px-4 py-2">${(entry.Total_Time_Taken)}</td>
                <td class="px-4 py-2">${(entry.Days_Offset)}</td>
            </tr>
            `;
        }
        
    });
    return rows;
}



</script>