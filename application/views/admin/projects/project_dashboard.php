<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php


$activeSprint = null; 
$stories = [];


foreach($sprints as $sprint){
    if($sprint->status == "1"){
        $activeSprint = $sprint;
    }
}


foreach($epics as $epic){
    $stories = array_merge($stories, $epic->stories);
}


$completed_stories = [];
$progress_stories = [];
$pending_stories = [];

foreach($stories as $story){

    $duedate = $story->duedate ?? $story->startdate;

    if(strtotime($duedate) >= strtotime(date("Y-m-d"))){
        if($story->status == 5){
            $completed_stories[] = $story;
        }else{
            $progress_stories[] = $story;
        }
    }
    else{
        $pending_stories[] = $story;
    }
}
    


foreach ($members as &$member) {
    $member['Total Stories'] = 0;
    $member['Completed Stories'] = 0;
    $member['Delayed Stories'] = 0;
    $current_date = date('Y-m-d');

    foreach ($stories as $story) {
        if (in_array($member['staff_id'], $story->assignees_ids)) {
            $member['Total Stories']++;
            
            if ($story->status == 5) {
                $member['Completed Stories']++;
            }
            
            if ($story->duedate && $story->duedate < $current_date) {
                $member['Delayed Stories']++;
            }
        }
    }
}


?>

<div class="p-6 bg-gray-100 h-screen">
    
    <!-- Overview Panel -->
    <div class="bg-white rounded-xl p-4 shadow-md mb-4">
        <h2 class="text-xl font-bold mb-2">Overview</h2>
        <div class="flex justify-between">
            <div>
                <h3 class="text-gray-600">Current Sprint:</h3>
                <p class="font-medium"><?= ($activeSprint) ? $activeSprint->name : 'No sprint active!'; ?></p>
            </div>
            <div>
            <?php
                if (!$activeSprint) {
                    $headerText = "Sprint Status:";
                    $message = 'No sprint active!';
                } elseif (!$activeSprint->date_started) {
                    // The sprint hasn't started yet
                    $daysUntilStart = (strtotime($activeSprint->start_date) - strtotime(date("Y-m-d"))) / (60*60*24);
                    $headerText = "Days until sprint starts:";
                    $message = $daysUntilStart . ' day(s)';
                } elseif (!$activeSprint->date_ended) {
                    if (strtotime(date("Y-m-d")) <= strtotime($activeSprint->end_date)) {
                        // The sprint has started, but not ended yet and is not overdue
                        $daysLeft = (strtotime($activeSprint->end_date) - strtotime(date("Y-m-d"))) / (60*60*24);
                        $headerText = "Days left in current sprint:";
                        $message = $daysLeft . ' day(s)';
                    } else {
                        // The sprint is overdue
                        $daysOverdue = (strtotime(date("Y-m-d")) - strtotime($activeSprint->end_date)) / (60*60*24);
                        $headerText = "Sprint overdue by:";
                        $message = $daysOverdue . ' day(s)';
                    }
                } else {
                    // The sprint has ended
                    $headerText = "Sprint Status:";
                    $message = 'Sprint has ended!';
                }
            ?>

            <h3 class="text-gray-600"><?= $headerText; ?></h3>
            <p class="font-medium"><?= $message; ?></p>

            </div>
            <div class="flex items-center">
                <h3 class="text-gray-600 mr-2">Progress:</h3>
                <div class="w-32 bg-gray-300 rounded-full">
                    <div class="bg-blue-500 text-xs text-white py-1 rounded-full text-center" style="width: <?= $percent; ?>%;"><span class="text-black rounded px-2 py-[2px]"><?= $percent; ?>%</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current burndown chart -->
    <div class="bg-white border-solid border-gray-200 rounded-xl p-4 shadow-md mb-4">
        <div class="flex flex-row justify-between mb-4">
            <h2 class="text-xl font-bold"><?= ($activeSprint) ? $activeSprint->name . ' Burndown' : 'No sprint active!'; ?></h2>
        </div>
        <div>
            <!-- Bar graph (you can integrate any graph library like Chart.js and adjust here) -->
            <canvas id="burndownChart" style="width:100%;" height="400"></canvas>
        </div>
        
    </div>

    <!-- Time Metrics (Per Sprint) -->
    <div class="bg-white border-solid border-gray-200 rounded-xl p-4 shadow-md mb-4">
        <div class="flex flex-row justify-between mb-4">
            <h2 class="text-xl font-bold">Time Metrics </h2>
            
            <div class="flex flex-row gap-2">

                <select id="typeSelect" class="border rounded-md px-2">
                    <option value="sprint">Sprints</option>
                    <option <?php if(count($sprints) < 1 && count($epics) > 0){ echo 'selected';} ?> value="epic">Epics</option>
                </select>


                <!-- Sprint Select Box -->
                <div id="sprintSelectDiv" class="<?php if(count($sprints) < 1 && count($epics) > 0){ echo 'hidden';} ?>">

                    <select id="sprintSelect" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <?php foreach ($sprints as $sprint): ?>
                            <option value="<?php echo $sprint->id; ?>"><?php echo $sprint->name; ?></option>
                        <?php endforeach; ?>
                        <?php 
                            if(count($sprints) < 1){
                                echo '<option value="0">No sprint found</option>';
                            }
                        ?>
                    </select>
                </div>

                <!-- Epic Select Box -->
                <div id="epicSelectDiv" class="<?php if( count($sprints) > 0 || count($epics) < 1 ){ echo 'hidden';} ?>">
                    <select id="epicSelect" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <?php foreach ($epics as $epic): ?>
                            <option value="<?php echo $epic->id; ?>"><?php echo $epic->name; ?></option>
                        <?php endforeach; ?>
                        <?php 
                            if(count($epics) < 1){
                                echo '<option value="0">No epic found</option>';
                            }
                        ?>
                    </select>
                </div>


                
            </div>
        </div>
        <div>
            <!-- Bar graph (you can integrate any graph library like Chart.js and adjust here) -->
            <canvas id="barChart" style="width:100%;" height="400"></canvas>
        </div>
        
    </div>

    <!-- Task Metrics -->

    <div class="flex justify-between items-center my-5 gap-4">

      

        <div data-category="completed" class="bg-white border-solid border-gray-200 hover:border-green-500 border-2 p-6 rounded-lg w-full shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all ease-in-out duration-300 cursor-pointer ">
            <div class="flex justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold">Completed</h2>
                        <p class="text-4xl font-bold"><?= count($completed_stories) ?></p>
                    </div>
                    <i class="fas fa-check text-green-500 hover:text-green-600"></i>
            </div>
        </div>

        <div data-category="in-progress" class="bg-white border-solid border-gray-200 hover:border-blue-500 border-2 p-6 rounded-lg w-full shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all ease-in-out duration-300 cursor-pointer">
            <div class="flex justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold">In Progress</h2>
                        <p class="text-4xl font-bold"><?= count($progress_stories) ?></p>
                    </div>
                    <i class="fas fa-plane-departure text-blue-500 hover:text-blue-600"></i>
            </div>
        </div>

        <div data-category="pending" class="bg-white border-solid border-gray-200 hover:border-red-500 border-2 p-6 rounded-lg w-full shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all ease-in-out duration-300 cursor-pointer">
            <div class="flex justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold">Pending</h2>
                        <p class="text-4xl font-bold"><?= count($pending_stories) ?></p>
                    </div>
                    <i class="fas fa-bed text-red-500 hover:text-red-600"></i>
            </div>
        </div>

        
    </div>

    <!-- Completion and Progress Rate -->
    <div class="flex flex-row justify-around items-stretch my-5 gap-4">

        <div class="bg-white border-2 border-solid border-gray-200 rounded p-4 w-2/5">
            <h2 class="text-xl text-center mb-8 font-semibold">Progress Chart</h2>
            <div class="m-auto h-full max-w-[400px]">
                <canvas id="pieChart" height="200"></canvas>
            </div>

        </div>

        <div class="bg-white border-2 border-solid border-gray-200 rounded p-4 w-3/5">
        
            <h2 class="text-xl text-center mb-8 font-semibold">Progress Rate</h2>
            <div class="m-auto h-full max-w-[600px]">
                <canvas id="lineChart" height="200"></canvas>
            </div>

        </div>

    </div>

    <!-- Team Performance Metrics (Optional) -->
    <div class="bg-white rounded-xl p-4 shadow-md">
        <h2 class="text-xl font-bold mb-2">Team Performance Metrics</h2>
        <!-- Individual KPIs -->

        <div class="w-full">
            <canvas id="teamPerformanceChart" height="100"></canvas>
        </div>
    </div>
    <div class="mt-4"><hr></div>

</div>

<div class="modal fade" id="storyModal" tabindex="-1" role="dialog" aria-labelledby="storyModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content bg-white rounded-lg shadow-xl">
      <div class="modal-header bg-gray-200 p-4 flex justify-between items-center">
        <h5  class="modal-title text-2xl font-semibold text-gray-700 mx-auto" id="storyModalLabel">Stories</h5>
        <button type="button" class="close text-gray-600 hover:text-gray-800 text-2xl" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="hover:text-red-500">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="storyModalBody">

      </div>
    </div>
  </div>
</div>

<script>

    <?php


    $thisSprints = $sprints;
    $thisEpics = $epics;

    foreach ($thisSprints as &$sprint) {

        foreach($sprint->stories as &$story) {
            $story = (object) [
                'id' => $story->id,
                'name' => $story->name,
                'estimated_hours' => $story->estimated_hours,
                'total_time_spent' => $story->total_time_spent,
                'startdate' => $story->startdate,
                'duedate' => $story->duedate,
                'datefinished' => $story->datefinished,
                'status' => $story->status
            ];
        }

        $sprint = (object) [
            'id' => $sprint->id,
            'name' => $sprint->name,
            'stories' => $sprint->stories,
        ];
    }

    foreach ($thisEpics as &$epic) {

        foreach($epic->stories as &$story) {
            $story = (object) [
                'id' => $story->id,
                'name' => $story->name,
                'estimated_hours' => $story->estimated_hours,
                'total_time_spent' => $story->total_time_spent,
                'startdate' => $story->startdate,
                'duedate' => $story->duedate,
                'datefinished' => $story->datefinished,
                'status' => $story->status
            ];
        }

        $epic = (object) [
            'id' => $epic->id,
            'name' => $epic->name,
            'stories' => $epic->stories,
        ];
    }

    ?>

    var sprintsData = <?php echo json_encode($thisSprints); ?>;
    var epicsData = <?php echo json_encode($thisEpics); ?>;

    let completed_stories = <?php echo json_encode(array_map(function($story) {
        return [
            'id' => $story->id,
            'name' => $story->name,
            'estimated_hours' => $story->estimated_hours,
            'total_time_spent' => $story->total_time_spent,
        ];
    }, $completed_stories)); ?>;

    let progress_stories = <?php echo json_encode(array_map(function($story) {
        return [
            'id' => $story->id,
            'name' => $story->name,
            'estimated_hours' => $story->estimated_hours,
            'total_time_spent' => $story->total_time_spent,
        ];
    }, $progress_stories)); ?>;

    let pending_stories = <?php echo json_encode(array_map(function($story) {
        return [
            'id' => $story->id,
            'name' => $story->name,
            'estimated_hours' => $story->estimated_hours,
            'total_time_spent' => $story->total_time_spent,
        ];
    }, $pending_stories)); ?>;


    <?php

    $processedStories = array_map(function($story) {
        return [
            'status' => $story->status,
            'startdate' => $story->startdate,
            'duedate' => $story->duedate,
            'datefinished' => ($story->datefinished) ? date("Y-m-d", strtotime($story->datefinished)) : ''
        ];
    }, $stories);

    $days = [];
    $totalStories = count($processedStories);
    

    foreach ($processedStories as $story) {
        $completedCount = 0;
        
        if (!empty($story['datefinished']) && $story['status'] == 5) {

            $completedDay = date('Y-m-d', strtotime($story['datefinished']));
            
            // Increment global completed count
            $completedCount++;
            
            // Ensure the completed day is initialized
            if (!isset($days[$completedDay])) {
                $days[$completedDay] = ['completed' => 0];
            }
            
            $days[$completedDay]['completed'] = $completedCount;
        }
    }

    // Handle days where no stories were completed (carry over progress from previous day)

    $previousCompleted = 0;
    ksort($days);  // Ensure days are sorted in ascending order
    foreach ($days as &$day) {

        $day['completed'] += $previousCompleted;
        $previousCompleted = $day['completed'];

        $day['progress'] = ($totalStories > 0) ? round(($day['completed'] / $totalStories) * 100) : '0';
    }

    ?>

</script>


<script>

    

    // Data for Estimated vs. Actual Time Bar Chart
    var ctxBar = document.getElementById('barChart').getContext('2d');
    var barChart = new Chart(ctxBar, {
        type: 'bar',
    });

    function updateChart(type, id) {
        var data;

        if(id == 0){
            Swal.fire("Warning", "No "+type+" found", "warning");
            return;
        }

        if (type === 'sprint') {
            data = sprintsData.find(sprint => sprint.id == id);
        } else {
            data = epicsData.find(epic => epic.id == id);
        }

        // Assuming you're using Chart.js or a similar library:
        barChart.data = processDataForChart(data); // Convert your data to a format the chart can use
        barChart.update();
    }
    <?php

    if(count($sprints) > 0){
        echo "updateChart('sprint', sprintsData[0].id);";
    }else if(count($epics) > 0){
        echo "updateChart('epic', epicsData[0].id);";
    }
    
    
    ?>

    function processDataForChart(data) {
        const labels = [];
        const estimatedHours = [];
        const timeSpentHours = [];

        data.stories.forEach(story => {
            labels.push(story.name);
            estimatedHours.push(story.estimated_hours);
            timeSpentHours.push(story.total_time_spent / 3600);  // Convert seconds to hours
        });

        return {
            labels: labels,
            datasets: [
                {
                    label: 'Estimated Hours',
                    data: estimatedHours,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Time Spent (hours)',
                    data: timeSpentHours,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        };
    }

    

    document.getElementById('typeSelect').addEventListener('change', function(e) {
        const type = e.target.value; // either "sprint" or "epic"
        const idSelect = type === 'sprint' ? 'sprintSelect' : 'epicSelect';
        const id = document.getElementById(idSelect).value;

        if (type === 'sprint') {
        sprintSelectDiv.classList.remove('hidden');
        epicSelectDiv.classList.add('hidden');
        // Update chart for Sprint (You can call an update function here)
        } else {
            sprintSelectDiv.classList.add('hidden');
            epicSelectDiv.classList.remove('hidden');
            // Update chart for Epic (You can call an update function here)
        }


        updateChart(type, id);
    });

    document.getElementById('sprintSelect').addEventListener('change', function(e) {
        updateChart('sprint', e.target.value);
    });

    document.getElementById('epicSelect').addEventListener('change', function(e) {
        updateChart('epic', e.target.value);
    });


    // Data for Completion Rate Pie Chart
    var ctxPie = document.getElementById('pieChart').getContext('2d');
    var pieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: ['Completed', 'Pending'],
            datasets: [{
                data: [<?= $percent ?>, <?= 100 - $percent ?>],
                backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                borderWidth: 1
            }]
        }
    });

    // Data for Progress Rate Line Chart

    var ctxLine = document.getElementById('lineChart').getContext('2d');
    var lineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_keys($days)); ?>,
            datasets: [{
                label: 'Progress Rate',
                data: <?php echo json_encode(array_column($days, 'progress')); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                fill: false
            }]
        },
        options: {
        scales: {
            y: {
                suggestedMin: 0,
                suggestedMax: 100
            }
        }
    }
    });



    <?php

    $labels = [];
    $totalTasks = [];
    $completedTasks = [];
    $delayedTasks = [];
    $totalHoursLogged = [];
    
    foreach($members as $thismem){
        $labels[] = $thismem['firstname'];
        $totalTasks[] = $thismem['Total Stories'];
        $completedTasks[] = $thismem['Completed Stories'];
        $delayedTasks[] = $thismem['Delayed Stories'];
        $totalHoursLogged[] = round($thismem['total_logged_time'] / 3600, 2);
    }
    

    

    ?>

    var labels = <?php echo json_encode($labels); ?>;
    var totalTasks = <?php echo json_encode($totalTasks); ?>;
    var completedTasks = <?php echo json_encode($completedTasks); ?>;
    var delayedTasks = <?php echo json_encode($delayedTasks); ?>;
    var totalHoursLogged = <?php echo json_encode($totalHoursLogged); ?>;

    
    labels.unshift(""); 
    labels.push("");
    totalTasks.unshift(0);
    totalTasks.push(0);
    completedTasks.unshift(0);
    completedTasks.push(0);
    delayedTasks.unshift(0);
    delayedTasks.push(0);
    totalHoursLogged.unshift(0);
    totalHoursLogged.push(0);

    var ctxTeamPerformance = document.getElementById('teamPerformanceChart').getContext('2d');
    var teamPerformanceChart = new Chart(ctxTeamPerformance, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Tasks',
                data: totalTasks,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: false
            }, {
                label: 'Completed Tasks',
                data: completedTasks,
                borderColor: 'rgba(255, 159, 64, 1)',
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                fill: false
            }, {
                label: 'Delayed Tasks',
                data: delayedTasks,
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                fill: false
            }, {
                label: 'Total Hours Logged',
                data: totalHoursLogged, 
                borderColor: 'rgba(153, 102, 255, 1)',
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                fill: false
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

</script>

<script>
    let divs = document.querySelectorAll('[data-category]');
    divs.forEach(div => {
        div.addEventListener('click', function() {
            let category = this.getAttribute('data-category');
            let storiesList = '<div class="flex flex-col gap-4">';
            
            let stories = [];
            switch(category) {
                case 'completed':
                    stories = completed_stories;
                    break;
                case 'in-progress':
                    stories = progress_stories;
                    break;
                case 'pending':
                    stories = pending_stories;
                    break;
            }
            stories.forEach(story => {
                storiesList += `
                <div class="story" data-story-id="${story.id}">
                    <div class="border-2 border-solid border-gray-300 hover:border-gray-400 rounded-lg transition-all px-4 py-2 flex justify-between items-center">
                        <a data-dismiss="modal" aria-label="Close" href="#" onclick="init_task_modal('${story.id}');" class="text-gray-800 font-bold">${story.name}</a>
                        <div class="flex items-center">
                            <div class="ml-4 text-gray-800">${story.estimated_hours} hours estimated</div>
                        </div>
                    </div>
                </div>`;
            });

            if(storiesList == '<div class="flex flex-col gap-4">'){
                storiesList += `<h3 class="text-center text-lg font-bold my-4">No story found!</h3>`;
            }

            storiesList += '</div>';
            // Insert the stories into the modal body
            document.getElementById('storyModalBody').innerHTML = storiesList;
            
            // Show the modal
            $('#storyModal').modal('show');
        });
    });
</script>
<?php
if ($activeSprint) {
    $sprintStart = new DateTime($activeSprint->start_date);
    $sprintEnd = new DateTime($activeSprint->end_date);
    $totalDays = $sprintStart->diff($sprintEnd)->days + 1;  // +1 to include both start and end days

    $totalEstimatedHours = $activeSprint->estimated_time;
    $idealBurn = [];
    $actualBurn = [];
    $actualHoursLeft = $totalEstimatedHours;

    // Sort stories by date finished
    usort($activeSprint->stories, function($a, $b) {
        if ($a->datefinished == $b->datefinished) return 0;
        return ($a->datefinished < $b->datefinished) ? -1 : 1;
    });
    
    // Initialize ideal burn with even distribution
    $hoursBurnedPerDay = $totalEstimatedHours / $totalDays;
    for ($i = 0; $i < $totalDays; $i++) {
        $idealBurn[] = $totalEstimatedHours - ($hoursBurnedPerDay * ($i + 1));
    }
    
    // Adjust ideal burndown based on story start and due dates
    foreach ($activeSprint->stories as $story) {
        if (isset($story->startdate)) {
            $start = new DateTime($story->startdate);
            $due = (isset($story->duedate) && !empty($story->duedate)) ? new DateTime($story->duedate) : $start;

            // Calculate weighted burn for this story
            $storyDays = $start->diff($due)->days + 1;
            $storyBurnRate = $story->estimated_hours / $storyDays;

            for ($i = 0; $i < $storyDays; $i++) {
                $currentDayIndex = $start->diff($sprintStart)->days + $i;
                if (isset($idealBurn[$currentDayIndex])) {
                    $idealBurn[$currentDayIndex] -= $storyBurnRate;
                }
            }
        }
    }

    // Ensure ideal values don't go negative
    $previousValue = $totalEstimatedHours;
    foreach ($idealBurn as &$value) {
        $value = max(0, min($value, $previousValue));
        $previousValue = $value;
    }

    // Construct actual burndown
    foreach ($activeSprint->stories as $story) {
        if (isset($story->datefinished) && $story->status == 5) { // Assuming status 5 is "completed"
            $actualHoursLeft -= $story->estimated_hours;
            $date = date('Y-m-d', strtotime($story->datefinished));
            $actualBurn[$date] = $actualHoursLeft;
        }
    }

    // Fill in missing days for actual burndown
    $previousDayValue = $totalEstimatedHours;
    $allDays = [];
    for ($i = 0; $i <= $totalDays; $i++) {
        $dayString = date('Y-m-d', strtotime($activeSprint->start_date) + ($i * 60*60*24));
        $allDays[] = $dayString;
        if (!isset($actualBurn[$dayString])) {
            $actualBurn[$dayString] = $previousDayValue;
        } else {
            $previousDayValue = $actualBurn[$dayString];
        }
    }
    ksort($actualBurn);

} else {
    // Handle when $activeSprint is null
    $allDays = [];
    $idealBurn = [];
    $actualBurn = [];
}

?>

<script>
    var ctxBurn = document.getElementById('burndownChart').getContext('2d');
    var burndownChart = new Chart(ctxBurn, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($allDays); ?>,
            datasets: [{
                label: 'Ideal Burndown',
                data: <?php echo json_encode($idealBurn); ?>,
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 2,
                fill: false,
                borderDash: [5, 5]
            }, {
                label: 'Actual Burndown',
                data: <?php echo json_encode(array_values($actualBurn)); ?>,
                backgroundColor: 'rgba(220, 53, 69, 0.2)',
                borderColor: 'rgba(220, 53, 69, 1)',
                borderWidth: 2,
                fill: false
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
