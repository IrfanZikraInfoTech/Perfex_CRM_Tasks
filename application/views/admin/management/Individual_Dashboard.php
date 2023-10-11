<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); 

function convertSecondsToRoundedTime($seconds)
{
    $hours = floor($seconds / 3600);
    $minutes = round(($seconds % 3600) / 60);

    if ($hours > 0) {
        return "{$hours}h {$minutes}m";
    } else {
        return "{$minutes}m";
    }
}

?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css" rel="stylesheet" type="text/css"/>

<style>
    .row-options{
        display: none;
    }
    /* timeline  */
.no-scroll::-webkit-scrollbar {
  display: none;
}
.clock-in-time {
    background: linear-gradient(90deg, rgba(54,162,235,0.2) 0%, rgba(74,182,255,0.2) 100%)!important;
    border-color: rgba(54, 162, 235, 1)!important;
}

.afk-time {
    background: linear-gradient(90deg, rgba(255,206,86,0.2) 0%, rgba(255,226,106,0.2) 100%)!important;
    border-color: rgba(255, 206, 86, 1)!important;
}
.shift-time {
    background: linear-gradient(90deg, rgba(255, 105, 180, 0.2) 0%, rgba(255, 182, 193, 0.2) 100%)!important;
    border-color: rgba(255, 105, 180, 0.2)!important;
}
.myscrollbar::-webkit-scrollbar {
    width: 0; 
}

.myscrollbar {
    scrollbar-width: none;
}

.myscrollbar {
    -ms-overflow-style: none;
}

</style>
<div id="wrapper" class="wrapper">
    <div class="content flex flex-col gap-8">
        <div class="w-full mb-4">
             <h2 class="text-3xl font-bold text-center text-gray-500">Individual KPI Dashboard</h2>
        </div>
        <div class="w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-gray-400 transition-all">
            <div class="flex items-center justify-between">
                <!-- User Information Section -->
                <div class="flex items-center w-2/3">
                    <div class="w-32 h-32 relative mr-5">
                        <?php echo staff_profile_image($GLOBALS['current_user']->staffid, ['w-full', 'h-full', 'rounded-full', 'object-cover'], 'thumb') ?>
                    </div>

                    <!-- Information Text -->
                    <div>
                        <h1 class="text-2xl font-bold mb-2 text-uppercase"><?php echo $GLOBALS['current_user']->firstname . ' ' . $GLOBALS['current_user']->lastname; ?></h1>
                        <p class="text-lg">
                            <span class="font-semibold">Position:</span> 
                            <span class="font-medium"> <?php echo $GLOBALS['current_user']->staff_title; ?> </span>
                        </p>
                        <p class="text-lg">
                            <span class="font-semibold">Department:</span> 
                            <span class="font-medium"></span>
                        </p>     
                        <?php
                        $report_to_id = $GLOBALS['current_user']->report_to;
                        // echo "Report To ID: " . $report_to_id . "<br/>";

                        $reporting_to_name = "";

                        // Check if current user staffid is 1
                        if ($GLOBALS['current_user']->staffid == 1) {
                            $reporting_to_name = "None";
                            // echo "Logged in as Staff ID: 1 - No reporting required.<br/>";
                        } else {
                            foreach ($staff_members as $staff) {
                                // echo "Checking Staff ID: " . $staff->staffid . "<br/>"; // See every staff id checked
                                if ($staff->staffid == $report_to_id) {
                                    // echo "Matched with Staff ID: " . $staff->staffid . "<br/>"; // Should show when a match is found
                                    $reporting_to_name = $staff->firstname . ' ' . $staff->lastname;
                                    break;
                                }
                            }

                            if (empty($reporting_to_name)) {
                                echo "No match found!";
                            }
                        }
                        ?>
                        <p class="text-lg">
                            <span class="font-semibold">REPORTING TO:</span> 
                            <span class="font-medium"><?php echo $reporting_to_name; ?></span>
                        </p>
                    </div>
                </div>
                <div class="flex flex-col w-1/3 bg-gray-100 p-4 py-3 shadow-inner rounded-[50px]  max-h-[300px]">
                    <!-- Input boxes for FROM and TO -->
                    <div class="flex flex-col space-y-2 mb-2 py-3 mx-3 w-90">
                        <input type="text" id="from" class="w-full p-2 border rounded text-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="From">
                        <input type="text" id="to" class="w-full p-2 border rounded text-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="To">
                    </div>
                    
                    <!-- Search Button -->
                    <div class="flex justify-end mr-2">
                        <button class="px-4 py-2 bg-gray-100 border border-blue-600 rounded-[50px] text-blue-600 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 hover:text-white focus:ring-opacity-50 transition-all duration-300">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>
        <!-- performance -->
        <div class=" flex lg:flex-row flex-col justify-between relative gap-5">
            <!-- Left side table -->
            <div class="w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-gray-400 transition-all overflow-hidden">
                <h2 class="text-xl font-bold mb-4 text-center">Key Performance Indicators:</h2>
                <div class="flex flex-col h-full bg-gray-100 p-6 rounded-[50px] shadow-inner overflow-y-scroll myscrollbar max-h-[300px]">
                    <div class="w-full bg-white shadow-lg hover:shadow-xl border border-solid border-gray-200 shadow-inner rounded-[50px] transition-all p-6">

                        <table class="w-full table text-lg">
                            <thead>
                            <tr>
                                <th class="font-bold text-between ">KPI</th>
                                <th class="font-bold text-right  ">SCORE</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="font-semibold">Punctuality Rate</td>
                                    <td class="text-right">100%</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="font-semibold">Task Completion Rate</td>
                                    <td class="text-right">50%</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="font-semibold">Task Efficiency Rate</td>
                                    <td class="text-right">100%</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="font-semibold">Task Time Adherence Rate</td>
                                    <td class="text-right">80%</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="font-semibold">Summary Adherence Rate</td>
                                    <td class="text-right">100%</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="font-semibold">Shift Productivity Rate</td>
                                    <td class="text-right">50%</td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
             <div class="xl:w-[40%] w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-gray-400 transition-all flex flex-col items-center">
                <h2 class="text-xl font-bold mb-4 text-center">Overall Performance Score:</h2>

                <div class="relative w-44 h-44 rounded-full overflow-hidden mb-4" id="scoreCircle">
                    <!-- Background color of the circle -->
                    <div class="absolute top-0 left-0 w-full h-full bg-red-200"></div>
                    <!-- Text inside the circle -->
                    <div class="absolute top-0 left-0 w-full h-full flex items-center justify-center text-2xl font-bold" id="scoreText">8/10</div>
                </div>
            </div>
        </div>
        <!--  timeline   -->
        <div class="bg-white shadow-lg hover:shadow-xl border border-solid border-white hover:border-gray-400 transition-all rounded-[50px] p-7 flex md:flex-row flex-col justify-between h-full w-full">
            <div id="visualization"  class="relative w-full rounded-[50px]" >
            </div>
        </div>

            <div class="w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-gray-400 transition-all overflow-hidden">
                <h2 class="text-xl font-bold mb-4 text-center text-uppercase ">Key Performance Indicators:</h2>
            <!-- Insert the charts or graphs as per your design and library of choice here -->
        
            <div class="p-6 mt-6 space-y-4 bg-gray-100 min-h-[300px] rounded-[50px]">

                <!-- Row 1 -->
                <div class="flex justify-between mt-6">
                    <div class="text-lg text-grey-600 font-semibold">
                        <p>
                        Task Compilition Rate And Task Efficiency Rate
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-gray-400">
                        <span class="text-sm font-medium">TASK ASSIGNED</span>
                        <span class="text-xl mt-2">100</span>
                    </div>
                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-gray-400">
                        <span class="text-sm font-medium">TASK COMPLETED</span>
                        <span class="text-xl mt-2">98</span>
                    </div>
                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-gray-400">
                        <span class="text-sm font-medium">TASK COMPLETED PAST DUE</span>
                        <span class="text-xl mt-2">15</span>
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="flex justify-between mt-6">
                    <div class="text-lg text-grey-600 font-semibold ">         
                       <p>
                       Task Time Adherence Rate
                       </p>
                    </div>
                </div>

               
                <div class="grid grid-cols-2 p-3 mb-4  gap-4">
                <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-gray-400">
                        <span class="text-sm font-medium">ESTIMATED TIME ON THE TASKS ASSIGNED</span>
                        <span class="text-xl mt-2">400 HRS</span>
                    </div>
                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-gray-400">
                        <span class="text-sm font-medium">ACTUAL TIME ON THE TASKS ASSIGNED</span>
                        <span class="text-xl mt-2">400 HRS</span>
                    </div>
                </div>
         
                <!-- Row 3-->
                <div class="flex justify-between mt-6">
                    <div class="text-lg text-grey-600 font-semibold ">    
                      <p>
                      Shift Productivity Rate
                      </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 p-3 mb-4   gap-4">
                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-gray-400">
                        <span class="text-sm font-medium">TOTAL LOGGED TIME</span>
                        <span class="text-xl mt-2">100</span>
                    </div>
                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-gray-400">
                        <span class="text-sm font-medium">TIME SPENT DOING TASKS</span>
                        <span class="text-xl mt-2">100</span>
                    </div>
                </div>

                <!-- Row 4-->
                <div class="flex justify-between mt-6">
                    <div class="text-lg text-grey-600 font-semibold"> 
                        <p>
                            Summary Adherence Rate
                        </p>
                    </div>
                </div>

                <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-gray-400">
                <span class="text-xl mt-2">SUMMARY 1... SUMMARY N</span>
                </div>
            </div>

        </div>
        <div class="w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-gray-400 transition-all overflow-hidden">
        <div class="p-6 mt-6 space-y-4 bg-gray-100 min-h-[300px] rounded-[50px]">       
            <!-- All Tasks Table -->
                <h2 class="text-xl font-bold mb-4 text-center text-uppercase">All Tasks</h2>
                    <div class="w-full bg-white shadow-lg hover:shadow-xl border border-solid border-gray-200 shadow-inner rounded-[50px] transition-all  p-6">

                        <table class="w-full table  text-base">
                            <thead>
                            <tr>
                                <th class="font-bold">ID</th>
                                <th class="font-bold">TITLE</th>
                                <th class="font-bold">ASSIGNED DATE</th>
                                <th class="font-bold">DUE DATE</th>
                                <th class="font-bold">COMPLETED DATE</th>
                                <th class="font-bold">TOTAL TIME TAKEN</th>
                                <th class="font-bold">NO. DAYS LATE</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="font-semibold">4287</td>
                                <td class="font-semibold">Triweekly Departmental Sync</td>
                                <td class="font-semibold">2023-09-21</td>
                                <td class="font-semibold">2023-09-21</td>
                                <td class="font-semibold">2023-09-21</td>
                                <td class="font-semibold">0h 0m</td>
                                <td class="font-semibold">0 days</td>
                            </tr>
                            <!-- ... Other rows ... -->
                            </tbody>
                        </table>
                    </div>
                <br>
    

        <!-- Task Timer Activity Table -->
                <h2 class="text-xl font-bold mb-4  text-center text-uppercase">Task Timer Activity</h2>
                    <div class="w-full bg-white shadow-lg hover:shadow-xl border border-solid border-gray-200 shadow-inner rounded-[50px] transition-all  p-6">

                        <table class="w-full table  text-base">
                            <thead>
                                <tr>
                                    <th class="font-bold">TASK</th>
                                    <th class="font-bold">START TIME</th>
                                    <th class="font-bold">END TIME</th>
                                    <th class="font-bold">DURATION</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="hover:bg-gray-50 transition">
                                <td class="font-semibold">NO DATA</td>
                                </tr>
                                <!-- ... Other rows ... -->
                                </tbody>
                        </table>
                    </div>
                <br>

        <!-- AFK Time Table -->
        <h2 class="text-xl font-bold mb-4  text-center text-uppercase">AFK Time</h2>
                    <div class="w-full bg-white shadow-lg hover:shadow-xl border border-solid border-gray-200 shadow-inner rounded-[50px] transition-all  p-6">

                        <table class="w-full table  text-base">
            <thead>
                <tr>
                <th class="font-bold"></th>
                </tr>
            </thead>
            <tbody>
            <tr class="hover:bg-gray-50 transition">
            <td class="font-semibold"></td>
                </tr>
                <!-- ... Other rows ... -->
            </tbody>
        </table>
                    </div>
         <br>           
         <h2 class="text-xl font-bold mb-4  text-center text-uppercase">Offline Time</h2>
                    <div class="w-full bg-white shadow-lg hover:shadow-xl border border-solid border-gray-200 shadow-inner rounded-[50px] transition-all  p-6">

                        <table class="w-full table  text-base">
                        <table class="w-full table  text-base">
            <thead>
                <tr>
                <th class="font-bold"></th>
                </tr>
            </thead>
            <tbody>
            <tr class="hover:bg-gray-50 transition">
            <td class="font-semibold"></td>
                </tr>
                <!-- ... Other rows ... -->
            </tbody>
        </table>
                    </div>
         <br>           


        <!-- Leave Table -->
        <h2 class="text-xl font-bold mb-4  text-center text-uppercase">Offline Time</h2>
                    <div class="w-full bg-white shadow-lg hover:shadow-xl border border-solid border-gray-200 shadow-inner rounded-[50px] transition-all">

                        <table class="w-full table  text-base">
                        <table class="w-full table  text-base">
            <thead>
                <tr>
                <th class="font-bold"></th>
                </tr>
            </thead>
            <tbody>
            <tr class="hover:bg-gray-50 transition">
            <td class="font-semibold"></td>
                </tr>
                <!-- ... Other rows ... -->
            </tbody>
        </table>
                    </div>
         <br>           

        </div>

    </div>
</div>
</div></div>



<?php init_tail(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>

<script>
   
   var dailyStats = <?php echo json_encode($daily_stats); ?>;

function fetchDailyInfos() {
    let data = dailyStats;

    const today = new Date();
    let startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 0, 0, 0); // Aaj ki date ka 12:00 AM
    let endDate = new Date(today.getTime() + 24*60*60*1000); // Default: next day
    console.log(data);
    // AFK entries filter
    const afk_entries = data.afk_and_offline.filter(entry => entry.status === 'AFK');

    var items = new vis.DataSet();
    var options = {
        zoomMin: 1000 * 60 * 60, // one hour in milliseconds
        zoomMax: 1000 * 60 * 60 * 24 * 31, // 31 days in milliseconds
        height: "180px"
    };

    // Clock-in aur Clock-out times ko timeline mein add karte hain
    if (data.clock_ins_outs) {
        data.clock_ins_outs.forEach(clock => {
            const inTime = new Date(clock.clock_in).toISOString();
            const outTime = new Date(clock.clock_out).toISOString();

            // Setting startDate and endDate based on clock-in and clock-out times
            if (new Date(inTime) < startDate) {
                startDate = new Date(inTime);
            }
            if (new Date(outTime) > endDate) {
                endDate = new Date(outTime);
            }

            items.add({
                content: 'Clock in',
                start: inTime,
                end: outTime,
                type: 'range',
                className: 'clock-in-time',
                group: 2
            });
        });
    }
    if (data.shift_timings && data.shift_timings.length > 0) {
        data.shift_timings.forEach(shift => {
            const shiftStart = new Date(`${shift.Year}-${shift.month}-${shift.day} ${shift.shift_start_time}`).toISOString();;
            const shiftEnd = new Date(`${shift.Year}-${shift.month}-${shift.day} ${shift.shift_end_time}`).toISOString();;

            console.log(shiftStart);
            
            items.add({
                content: 'Shift',
                start: shiftStart,
                end: shiftEnd,
                type: 'range',
                className: 'shift-time',
                group: 3  // Group 3 for shifts. You can adjust as needed.
            });

        });
    }

    // AFK timings ko timeline mein add karte hain
    if (afk_entries) {
      afk_entries.forEach(function (entry) {

        const start24HourTime = convertTo24Hour(entry.start_time);
        const end24HourTime = convertTo24Hour(entry.end_time);

        const startDateTime = new Date(`${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()} ${start24HourTime}`).toISOString();;
        const endDateTime = new Date(`${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()} ${end24HourTime}`).toISOString();;

        items.add({
          content: 'AFK',
          start: startDateTime,
          end: endDateTime,
          type: 'range',
          className: 'afk-time',
          group: 1
        });
      });
    } else {
      console.warn("afk_entries is not available");
    }

    var container = document.getElementById('visualization');
    if (container) {
      var timeline = new vis.Timeline(container, items, options);

    } else {
      console.error("Timeline container not found");
      return;
    }

    // Setting the timeline to focus on our startDate to endDate
    timeline.setWindow(startDate, endDate);
    timeline.setCurrentTime(getCurrentTimeInAsiaKolkata());
}

// Convert 12-hour time format to 24-hour time format
function convertTo24Hour(time) {
    const [hourMin, period] = time.split(' ');
    let [hour, minute] = hourMin.split(':');
    hour = +hour;
    if (period === "PM" && hour !== 12) hour += 12;
    if (period === "AM" && hour === 12) hour -= 12;
    return `${hour.toString().padStart(2, '0')}:${minute}`;
}

fetchDailyInfos();




function updateScoreCircle(value) {
    const percentage = value * 10;
    const scoreCircle = document.getElementById('scoreCircle');
    const scoreText = document.getElementById('scoreText');

    scoreCircle.style.background = `conic-gradient(orange 0% ${percentage}%, #f3f4f6 ${percentage}% 100%)`; // changed the transparent to a light gray for a subtle look
    scoreText.textContent = `${value}/10`;
}

updateScoreCircle(2);

</script>

</body>
</html>