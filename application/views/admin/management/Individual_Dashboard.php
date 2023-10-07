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
</style>
<div id="wrapper">
    <div class="bg-gray-100 p-5 border-b border-gray-300 flex justify-between items-center">
        <!-- Left side with icon and text -->
        <div class="flex items-center">
            <!-- Profile Image -->
            <div class="h-24 w-24 bg-gray-300 rounded-full flex items-center justify-center text-2xl mr-4">
                <span>?</span>
            </div>
            <!-- Information Text -->
            <div>
                <h1 class="text-xl font-bold mb-2">Individual KPI Dashboard</h1>
                <p class="text-sm mb-1">MOHAMMAD ANSAR ULLAH ANAS</p>
                <p class="text-sm mb-1">DIRECTOR</p>
                <p class="text-sm mb-1">MANAGEMENT DEPARTMENT</p>
                <p class="text-sm">REPORTING TO: GOD ALMIGHTY</p>
            </div>
        </div>

        <!-- Right side with input boxes and button -->
        <div class="flex items-center">
            <!-- Input boxes for FROM and TO -->
            <div class="flex flex-col mr-4">
                <label class="text-sm mb-1">FROM</label>
                <input type="text" class="border p-2 rounded text-sm mb-2" placeholder="FROM">
                <label class="text-sm mb-1">TO</label>
                <input type="text" class="border p-2 rounded text-sm" placeholder="TO">
            </div>
            <!-- Export Button -->
            <button class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-600">EXPORT</button>
        </div>
    </div>

    <div class="p-5 grid grid-cols-2 gap-4">
        <!-- Left side table -->
        <div class="p-4 bg-white rounded shadow">
            <h2 class="text-lg font-bold mb-2">Key Performance Indicators:</h2>
            <table class="w-full">
                <tr>
                    <th class="text-left">KPI</th>
                    <th class="text-right">SCORE</th>
                </tr>
                <tr>
                    <td>Punctuality Rate</td>
                    <td class="text-right">100%</td>
                </tr>
                <tr>
                    <td>Task Completion Rate</td>
                    <td class="text-right">50%</td>
                </tr>
                <tr>
                    <td>Task Efficiency Rate</td>
                    <td class="text-right">100%</td>
                </tr>
                <tr>
                    <td>Task Time Adheence Rate</td>
                    <td class="text-right">80%</td>
                </tr>
                <tr>
                    <td>Summary Adheence Rate</td>
                    <td class="text-right">100%</td>
                </tr>
                <tr>
                    <td>Shift Productivity Rate</td>
                    <td class="text-right">50%</td>
                </tr>
            </table>
        </div>

        <!-- Right side circular progress -->
        <div class="p-4 bg-white rounded shadow flex flex-col items-center">
            <h2 class="text-lg font-bold mb-2">Overall Performance Score:</h2>
            
            <!-- Circular progress indicator (let's say score is 8/10) -->
            <div class="relative w-24 h-24 rounded-full overflow-hidden" id="scoreCircle">
                <div class="absolute top-0 left-0 w-full h-full bg-red-200"></div>
                <div class="absolute top-0 left-0 w-full h-full"></div>
                <div class="absolute top-0 left-0 w-full h-full flex items-center justify-center text-2xl font-bold" id="scoreText">8/10</div>
            </div>
        </div>
    </div>


    <div class="p-5 bg-white rounded shadow mt-4">
        <h2 class="text-lg font-bold mb-2 border-b pb-2">Task Completion Rate and Adherence Rate</h2>
        <!-- Insert the charts or graphs as per your design and library of choice here -->
    
        <div class="p-6 space-y-4">
            <!-- Row 1 -->
            <div class="flex justify-between">
                <div class="bg-300  p-4 rounded">
                    <p>TASK COMPLETION RATE AND TASK EFFICIENCY RATE:</p>
                </div>
            </div>

            <!-- Row 2 -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-yellow-300 p-4 rounded">
                    <strong>TASK ASSIGNED</strong>
                    <p>100</p>
                </div>
                <div class="bg-yellow-300 p-4 rounded">
                    <strong>TASK COMPLETED</strong>
                    <p>98</p>
                </div>
                <div class="bg-yellow-300 p-4 rounded">
                    <strong>TASK COMPLETED PAST DUE</strong>
                    <p>15</p>
                </div>
            </div>

            <!-- Row 3 -->
            <div class="bg-300 p-4 rounded">
                <p>TASK TIME ADHERENCE RATE</p>
            </div>

            <!-- Row 4 -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-yellow-300 p-4 rounded">
                    <strong>ESTIMATED TIME ON THE TASKS ASSIGNED</strong>
                    <p>400 HRS</p>
                </div>
                <div class="bg-yellow-300 p-4 rounded">
                    <strong>ACTUAL TIME ON THE TASKS ASSIGNED</strong>
                    <p>400 HRS</p>
                </div>
            </div>

            <!-- Row 5 -->
            <div class="bg-300 p-4 rounded">
                <p>SHIFT PRODUCTIVITY RATE</p>
            </div>

            <!-- Row 6 -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-yellow-300 p-4 rounded">
                    <strong>TOTAL LOGGED TIME</strong>
                    <p>100</p>
                </div>
                <div class="bg-yellow-300 p-4 rounded">
                    <strong>TIME SPENT DOING TASKS</strong>
                    <p>100</p>
                </div>
            </div>

            <!-- Row 7 -->
            <div class="bg-300 p-4 rounded">
                <p>SUMMARY ADHERENCE RATE:</p>
            </div>

            <!-- Row 8 -->
            <div class="bg-yellow-300 p-4 rounded">
                <p>SUMMARY 1... SUMMARY N</p>
            </div>
        </div>

    </div>
    

    
    <div class="p-5 mt-4">
        <!-- All Tasks Table -->
        <h2 class="mb-4 font-medium text-lg">All Tasks</h2>
        <table class="min-w-full bg-white rounded shadow mb-4">
            <thead>
            <tr>
                <th class="px-4 py-2 border-b">ID</th>
                <th class="px-4 py-2 border-b">TITLE</th>
                <th class="px-4 py-2 border-b">ASSIGNED DATE</th>
                <th class="px-4 py-2 border-b">DUE DATE</th>
                <th class="px-4 py-2 border-b">COMPLETED DATE</th>
                <th class="px-4 py-2 border-b">TOTAL TIME TAKEN</th>
                <th class="px-4 py-2 border-b">NO. DAYS LATE</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="px-4 py-2 border-b">4287</td>
                <td class="px-4 py-2 border-b">Triweekly Departmental Sync</td>
                <td class="px-4 py-2 border-b">2023-09-21</td>
                <td class="px-4 py-2 border-b">2023-09-21</td>
                <td class="px-4 py-2 border-b">2023-09-21</td>
                <td class="px-4 py-2 border-b">0h 0m</td>
                <td class="px-4 py-2 border-b">0 days</td>
            </tr>
            <!-- ... Other rows ... -->
            </tbody>
    </table>

    <!-- Task Timer Activity Table -->
    <h2 class="mb-4 font-medium text-lg">Task Timer Activity</h2>
    <table class="min-w-full bg-white rounded shadow mb-4">
        <!-- ... Similar structure as above, modify as required ... -->
        <thead>
            <tr>
                <th class="px-4 py-2 border-b border-gray-300 text-left">TASK</th>
                <th class="px-4 py-2 border-b border-gray-300 text-left">START TIME</th>
                <th class="px-4 py-2 border-b border-gray-300 text-left">END TIME</th>
                <th class="px-4 py-2 border-b border-gray-300 text-left">DURATION</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="px-4 py-2 border-b">NO DATA</td>
            </tr>
            <!-- ... Other rows ... -->
            </tbody>
    </table>
    
    <!-- AFK Time Table -->
    <h2 class="mb-4 font-medium text-lg">AFK Time</h2>
    <table class="min-w-full bg-white rounded shadow mb-4">
        <thead>
            <tr>
                <th class="px-4 py-2 border-b"></th>
                <th class="px-4 py-2 border-b"></th>
                <th class="px-4 py-2 border-b"></th>
                <th class="px-4 py-2 border-b"></th>
                <th class="px-4 py-2 border-b"></th>
                <th class="px-4 py-2 border-b"></th>
                <th class="px-4 py-2 border-b"></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="px-4 py-2 border-b"></td>
                <td class="px-4 py-2 border-b"></td>
                <td class="px-4 py-2 border-b"></td>
                <td class="px-4 py-2 border-b"></td>
                <td class="px-4 py-2 border-b"></td>
                <td class="px-4 py-2 border-b"></td>
                <td class="px-4 py-2 border-b"></td>
            </tr>
            <!-- ... Other rows ... -->
        </tbody>
    </table>

    <!-- Offline Time Table -->
    <h2 class="mb-4 font-medium text-lg">Offline Time</h2>
    <table class="min-w-full bg-white rounded shadow mb-4">
        <thead>
            <tr>
                <th class="px-4 py-2 border-b"></th>
                <th class="px-4 py-2 border-b"></th>
                <th class="px-4 py-2 border-b"></th>
                <th class="px-4 py-2 border-b"></th>
                <th class="px-4 py-2 border-b"></th>
                <th class="px-4 py-2 border-b"></th>
                <th class="px-4 py-2 border-b"></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="px-4 py-2 border-b"></td>
                <td class="px-4 py-2 border-b"></td>
                <td class="px-4 py-2 border-b"></td>
                <td class="px-4 py-2 border-b"></td>
                <td class="px-4 py-2 border-b"></td>
                <td class="px-4 py-2 border-b"></td>
                <td class="px-4 py-2 border-b"></td>
            </tr>
            <!-- ... Other rows ... -->
        </tbody>
    </table>


    <!-- Leave Table -->
    <h2 class="mb-4 font-medium text-lg">Leave Status</h2>
    <table class="min-w-full bg-white rounded shadow mb-4">
        <!-- ... Similar structure as above, modify as required ... -->
        <thead>
            <tr>
                <th class="px-4 py-2 border-b">NO</th>
            </tr>
            </thead>
            <tbody>
            <!-- ... Other rows ... -->
            </tbody>
    </table>

    </div>
</div>


<?php init_tail(); ?>

<script>
    function updateScoreCircle(value) {
        const score = value / 10;
        const percentage = score * 100;
        const scoreCircle = document.getElementById('scoreCircle');
        const scoreText = document.getElementById('scoreText');
        
        scoreCircle.children[1].style.background = `conic-gradient(orange 0% ${percentage}%, transparent ${percentage}% 100%)`;
        scoreText.textContent = `${value}/10`;
    }

    // Update the circle with the desired score. 
    // Replace the number 0 with any other value between 0 and 10 to see the change.
    updateScoreCircle(2);
</script>

</body>
</html>