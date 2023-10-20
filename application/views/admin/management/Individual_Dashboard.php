<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); 

function hoursToHoursMinutes($hours) {
    $intHours = floor($hours);
    $minutes = round(($hours - $intHours) * 60);
    return $intHours . "h " . $minutes . "m";
}
function interpolateColor($from, $to, $percent) {
    $f = sscanf($from, "#%02x%02x%02x");
    $t = sscanf($to, "#%02x%02x%02x");

    $deltaR = $t[0] - $f[0];
    $deltaG = $t[1] - $f[1];
    $deltaB = $t[2] - $f[2];

    $color = [
        round($f[0] + ($deltaR * $percent)),
        round($f[1] + ($deltaG * $percent)),
        round($f[2] + ($deltaB * $percent))
    ];

    return sprintf("#%02x%02x%02x", $color[0], $color[1], $color[2]);
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
        border-color: rgba(255, 105, 180, 1)!important;
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

    #visualization .vis-timeline{
        border-radius:30px;
    }

</style>
<div id="wrapper" class="wrapper">
    <div class="content flex flex-col gap-8">
        <div class="w-full mb-4">
             <h2 class="text-3xl font-bold text-center text-gray-500">Individual KPI Dashboard</h2>
        </div>
        <div class="w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-yellow-400 transition-all">
            <div class="flex lg:flex-row flex-col gap-4 items-center justify-between">
                <!-- User Information Section -->
                <div class="flex sm:flex-row flex-col  items-center lg:w-2/3 w-full">
                    <div class="max-w-[10rem] h-full relative mr-5">
                        <?php echo staff_profile_image($staff->staffid, ['w-full', 'h-full', 'rounded-full', 'object-cover'], 'thumb') ?>
                    </div>

                    <!-- Information Text -->
                    <div class="sm:text-left text-center">

                        <h1 class="text-2xl font-bold mb-2 text-uppercase"><?= $staff->full_name?></h1>

                        <p class="text-lg">
                            <span class="font-semibold">Position:</span> 
                            <span class="font-medium"> <?= $staff->staff_title; ?> </span>
                        </p>
                        <p class="text-lg">
                            <span class="font-semibold">Department:</span> 
                            <span class="font-medium"><?= $staff->department_name ?></span>
                        </p>     
                        <?php
                        $report_to_id = $staff->report_to;

                        $reporting_to_name = id_to_name($report_to_id, 'tblstaff', 'staffid', 'firstname') . ' ' .id_to_name($report_to_id, 'tblstaff', 'staffid', 'lastname');
                        ?>
                        <p class="text-lg">
                            <span class="font-semibold">Reporting to:</span> 
                            <span class="font-medium"><?= $reporting_to_name; ?></span>
                        </p>
                    </div>
                </div>
                <div class="flex flex-col lg:w-1/3 w-full bg-sky-100 p-4 py-3 shadow-inner rounded-[50px]  max-h-[300px]">
                    <!-- Input boxes for FROM and TO -->
                    <div class="flex flex-col gap-2 mb-2 py-3 mx-3 w-90">

                        <input type="date" id="from" class="w-full py-2 px-4 border !rounded-3xl text-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="From" value="<?= $from ?>">

                        <input type="date" id="to" class="w-full py-2 px-4 border !rounded-3xl text-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="To" value="<?= $to ?>">

                        <?php
                        if(has_permission('team_management', '', 'admin')){

                            $staff_members = $this->staff_model->get();

                        ?>
                            
                            <select data-width="100%" id="staff" data-live-search="true" class="selectpicker text-2xl font-bold mb-2 text-uppercase">
                                <?php 
                                foreach($staff_members as $staff_member){
                                    $selected = '';
                                    if($staff_member['staffid'] == $staff->staffid){
                                        $selected = 'selected';
                                    }
                                    echo '<option '.$selected.' value="'.$staff_member['staffid'].'">'.$staff_member['full_name'].'</option>';
                                }
                                ?>
                                
                            </select>

                        <?php
                        }
                        ?>

                        <button class="px-4 py-2 bg-sky-100 border border-blue-600 rounded-[50px] text-blue-600 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 hover:text-white focus:ring-opacity-50 transition-all duration-300 mt-2" onclick="window.location.href=admin_url+'team_management/individual_dashboard/'+document.getElementById('from').value + '/' + document.getElementById('to').value <?= (has_permission('team_management', '', 'admin')) ? (" +'/' + document.getElementById('staff').value") : '' ?>">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        </button>
                    </div>

                        
                </div>

            </div>
        </div>



        <!-- performance -->
        <div class=" flex xl:flex-row flex-col justify-between relative gap-5">
            
            <!-- Left side table -->

            <div class="xl:w-[60%] w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-yellow-400 transition-all">

                <h2 class="text-xl font-bold mb-4 text-center">Key Performance Indicators:</h2>
                
                <div class="flex flex-col bg-sky-100 p-6 rounded-[50px] shadow-inner overflow-y-scroll myscrollbar">

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
                                    <td class="font-semibold">Attendance Rate</td>
                                    <td class="text-right"><?= round($punctuality_rate['present_percentage'],2) ?>%</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="font-semibold">Punctuality Rate</td>
                                    <td class="text-right"><?= round($punctuality_rate['on_time_percentage'],2) ?>%</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="font-semibold">Task Completion Rate</td>
                                    <td class="text-right"><?= round($task_rates['completion_rate'],2) ?>%</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="font-semibold">Task Efficiency Rate</td>
                                    <td class="text-right"><?= round($task_rates['efficiency_rate'],2) ?>%</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="font-semibold">Task Time Adherence Rate</td>
                                    <td class="text-right"><?= round($task_rates['timer_adherence_rate'],2) ?>%</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="font-semibold">Summary Adherence Rate</td>
                                    <td class="text-right"><?= round($summary_adherence_rate['percentage'],2) ?>%</td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="font-semibold">AFK Adherence Rate</td>
                                    <td class="text-right"><?= round($afk_adherence_rate['percentage'],2) ?>%</td>
                                </tr>
                                
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="font-semibold">Shift Productivity Rate</td>
                                    <td class="text-right"><?= round($shift_productivity_rate['percentage'], 2) ?>%</td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>

             <div class="xl:w-[40%] w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-yellow-400 transition-all flex flex-col items-center">
                <h2 class="text-xl font-bold mb-4 text-center">Overall Performance Score:</h2>
                <div class="relative w-full h-full min-h-[400px] flex justify-center items-center">

                    <div class="h-[90%] inset-0 my-auto absolute opacity-40">
                        <canvas id="progressChart"></canvas>
                    </div>

                    <div class="text-3xl font-black z-10">
                        <h2><?= round(($ops / 10),2) ?> / 10</h2>
                    </div>

                </div>
            </div>
        </div>
<?php if($from == $to){ ?>
        <div class="w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-yellow-400 transition-all">
            <div id="visualization"></div>
        </div>
        <?php }?>

        <div class="w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-yellow-400 transition-all overflow-hidden">
                <h2 class="text-xl font-bold mb-4 text-center text-uppercase ">METRICS</h2>
            <!-- Insert the charts or graphs as per your design and library of choice here -->
        
            <div class="p-6 mt-6 bg-sky-100 min-h-[300px] rounded-[50px]">

                <!-- Row 0 -->
                <div class="flex justify-between">
                    <div class="text-lg text-grey-600 pl-2 font-semibold">
                        <p>
                        Punctuality Metrics
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-6">

                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-yellow-400">
                        <span class="text-sm font-medium">Loggable Days</span>
                        <span class="text-xl mt-2"><?= $punctuality_rate['total_days'] ?></span>
                    </div>
                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-yellow-400">
                        <span class="text-sm font-medium">Days Present</span>
                        <span class="text-xl mt-2"><?= $punctuality_rate['days_present'] ?></span>
                    </div>
                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-yellow-400">
                        <span class="text-sm font-medium">Days on Time</span>
                        <span class="text-xl mt-2"><?= $punctuality_rate['days_on_time'] ?></span>
                    </div>
                    
                </div>

                <!-- Row 1 -->
                <div class="flex justify-between">
                    <div class="text-lg text-grey-600 pl-2 font-semibold">
                        <p>
                        Story Metrics
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-yellow-400">
                        <span class="text-sm font-medium">Assigned Stories</span>
                        <span class="text-xl mt-2"><?= $task_rates['total_tasks'] ?></span>
                    </div>
                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-yellow-400">
                        <span class="text-sm font-medium">Stories Completed</span>
                        <span class="text-xl mt-2"><?= $task_rates['completed_tasks_within_due'] ?></span>
                    </div>
                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-yellow-400">
                        <span class="text-sm font-medium">Stories Delayed Completed</span>
                        <span class="text-xl mt-2"><?= $task_rates['completed_tasks_past_due'] ?></span>
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="flex justify-between">
                    <div class="text-lg text-grey-600 font-semibold ">         
                       <p>
                       Task Time Adherence Rate
                       </p>
                    </div>
                </div>

               
                <div class="grid grid-cols-2 gap-4 mb-6">

                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-yellow-400">
                        <span class="text-sm font-medium">Total Stories Time Estimation</span>
                        <span class="text-xl mt-2"><?= hoursToHoursMinutes($task_rates['esimate_hours']) ?></span>
                    </div>
                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-yellow-400">
                        <span class="text-sm font-medium">Total Time spent on Stories</span>
                        <span class="text-xl mt-2"><?= hoursToHoursMinutes($task_rates['spent_hours']) ?></span>
                    </div>
                </div>
         
                <!-- Row 3-->
                <div class="flex justify-between">
                    <div class="text-lg text-grey-600 font-semibold ">    
                      <p>
                      Shift Productivity Rate
                      </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-yellow-400">
                        <span class="text-sm font-medium">Total Logged Time</span>
                        <span class="text-xl mt-2"><?= hoursToHoursMinutes($shift_productivity_rate['total_logged_time']) ?></span>
                    </div>
                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-yellow-400">
                        <span class="text-sm font-medium">Total Hours on Tasks</span>
                        <span class="text-xl mt-2"><?= hoursToHoursMinutes($shift_productivity_rate['total_task_time']) ?></span>
                    </div>
                </div>

                <!-- Row 4-->
                <div class="flex justify-between">
                    <div class="text-lg text-grey-600 font-semibold ">    
                      <p>
                      AFK Adherence Rate
                      </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-yellow-400">
                        <span class="text-sm font-medium">Total Allowed AFK</span>
                        <span class="text-xl mt-2"><?= hoursToHoursMinutes($afk_adherence_rate['allowed_afk_time']) ?></span>
                    </div>
                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-yellow-400">
                        <span class="text-sm font-medium">Total AFK Time Taken</span>
                        <span class="text-xl mt-2"><?= hoursToHoursMinutes($afk_adherence_rate['actual_afk_time']) ?></span>
                    </div>
                </div>

                <!-- Row 5-->
                <div class="flex justify-between">
                    <div class="text-lg text-grey-600 font-semibold"> 
                        <p>
                            Summary Adherence Rate
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-yellow-400">
                        <span class="text-sm font-medium">Working Days</span>
                        <span class="text-xl mt-2"><?= $summary_adherence_rate['working_days'] ?></span>
                    </div>
                    <div class="flex flex-col items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition border border-gray-200 border-solid hover:border-yellow-400 cursor-pointer" onclick="$('#summaries').toggle('slow');">
                        <span class="text-sm font-medium">Summaries Written</span>
                        <span class="text-xl mt-2"><?= $summary_adherence_rate['days_with_summaries'] ?></span>
                    </div>
                </div>

                <div id="summaries" class="grid grid-cols-2 gap-4 items-center bg-white p-5 shadow-sm hover:shadow-lg rounded-[40px] transition  border border-gray-200 border-solid hover:border-yellow-400 overflow-y-scroll myscrollbar max-h-[400px]" style="display:none;">
                                        
                                        <?php 
                                        foreach($summary_adherence_rate['summaries'] as $summary):                   
                                        ?>
                                            <div class="w-full h-full bg-white rounded-[40px] cursor-pointer hover:shadow-md border border-gray-200 border-solid transition-all hover:border-yellow-400 p-6"
                                            >
            
                                                <div class="font-bold text-xl"><?= $summary->date ?></div>

                                                <div class="clamp-lines text-md"><?= htmlspecialchars($summary->summary); ?></div>

                                            </div> 

                                        <?php
                                        endforeach;  
        
                                    if(count($summary_adherence_rate['summaries']) < 1){
                                        echo '<h2>No Summary Found!</h2>';
                                    }
    
                                    ?>
                                         
                </div>


            </div>

        </div>
        <div class="w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-yellow-400 transition-all overflow-hidden">

        <div class="p-6 mt-6 bg-sky-100 rounded-[50px]"> 

            <h2 class="text-xl font-bold mb-4 text-center text-uppercase">All Tasks</h2>
            <div class="w-full bg-white shadow-lg hover:shadow-xl border border-solid border-gray-200 hover:border-yellow-400 shadow-inner rounded-[50px] transition-all  p-6">
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
                        <?php foreach($stories as $story): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="font-semibold"><?= $story->id ?></td>
                                <td class="font-semibold flex flex-col">
                                    <a target="_blank" href="#" onclick="init_task_modal(<?= $story->id ?>); return false" class="text-sm"><?= $story->name ?></a>
                                    <?php if($story->rel_id && $story->rel_type =='project'): ?>
                                        <a target="_blank" href="<?= admin_url(); ?>projects/view/<?= $story->rel_id ?>" class="text-xs"><?= $story->project_name ?></a>
                                    <?php endif; ?>
                                </td>
                                <td class="font-semibold"><?= date("Y-m-d", strtotime( $story->dateadded)) ?></td>
                                <td class="font-semibold"><?= $story->duedate ?></td>
                                <td class="font-semibold"><?= $story->datefinished ? $story->datefinished : "Not Completed" ?></td>
                                <td class="font-semibold">
                                    <?php
                                    $hours = floor($story->total_time_spent / 3600);
                                    $minutes = floor(($story->total_time_spent / 60) % 60);
                                    echo $hours . "h " . $minutes . "m";
                                    ?>
                                </td>
                                <td class="font-semibold">
                                    <?= $story->late ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

            <?php 
            if($from == $to){

                //print_r($timer_activities);
            ?>

            <h2 class="text-xl font-bold mb-4 mt-8 text-center text-uppercase">Task Timer Activity</h2>
            <div class="w-full bg-white shadow-lg hover:shadow-xl border border-solid border-gray-200 hover:border-yellow-400 shadow-inner rounded-[50px] transition-all p-6">

                <table class="w-full table  text-base">
                    <thead>
                        <tr>
                            <th class="font-bold">#</th>
                            <th class="font-bold">TASK</th>
                            <th class="font-bold">START TIME</th>
                            <th class="font-bold">END TIME</th>
                            <th class="font-bold">DURATION</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($timer_activities as $count => $activity){
                            ?>

                            <tr class="hover:bg-gray-50 transition">

                                <td><?= $count +1 ?></td>
                                <td class="font-semibold flex flex-col">
                                    
                                    <a target="_blank" href="#" onclick="init_task_modal(<?= $activity['task_id'] ?>); return false" class="text-sm"><?= $activity["task_name"] ?></a>
                                        
                                    <?php if($activity['project_id']){ ?><a target="_blank" href="<?= admin_url(); ?>projects/view/<?= $activity['project_id'] ?>" class="text-xs"><?= $activity["project_name"] ?></a><?php } ?>
                                    
                                </td>

                                <td class="font-semibold"><?= $activity['start_time'] ?></td>
                                <td class="font-semibold"><?= $activity['end_time'] ?></td>
                                <td class="font-semibold"><?= $activity['duration'] ?></td>

                            </tr>

                            <?php }?>
                        </tbody>
                </table>
            </div>

            <div class="flex flex-row gap-4 mt-4">
                <div class="w-1/2">
                    <h2 class="text-xl font-bold my-4 text-center text-uppercase">AFK Entries</h2>
                    <div class="w-full bg-white shadow-lg hover:shadow-xl border border-solid border-gray-200 hover:border-yellow-400 shadow-inner rounded-[50px] transition-all  p-6">

                        <table class="w-full table  text-base">
                            <thead>
                                <tr>
                                    <th class="font-bold">START TIME</th>
                                    <th class="font-bold">END TIME</th>
                                    <th class="font-bold">DURATION</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                    foreach($afk_offline_entries as $entry){

                                        if($entry['status'] == "AFK"){
                                    ?>

                                    <tr class="hover:bg-gray-50 transition">


                                        <td class="font-semibold"><?= date("h:i A", strtotime($entry['start_time']))  ?></td>
                                        <td class="font-semibold"><?= !($entry['is_going']) ? date("h:i A", strtotime($entry['end_time'])) : 'Going On'  ?></td>
                                        <td class="font-semibold"><?= $entry['duration'] ?></td>

                                    </tr>

                                    <?php }}?>
                                </tbody>
                        </table>
                    </div>
                </div>
                <div class="w-1/2">
                    <h2 class="text-xl font-bold my-4  text-center text-uppercase">Offline Entries</h2>
                    <div class="w-full bg-white shadow-lg hover:shadow-xl border border-solid border-gray-200 hover:border-yellow-400 shadow-inner rounded-[50px] transition-all  p-6">

                        <table class="w-full table  text-base">
                            <thead>
                                <tr>
                                    <th class="font-bold">START TIME</th>
                                    <th class="font-bold">END TIME</th>
                                    <th class="font-bold">DURATION</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach($afk_offline_entries as $entry){

                                        if($entry['status'] == "Offline"){
                                    ?>

                                    <tr class="hover:bg-gray-50 transition">


                                        <td class="font-semibold"><?= date("h:i A", strtotime($entry['start_time'])) ?></td>
                                        <td class="font-semibold"><?= !($entry['is_going']) ? date("h:i A", strtotime($entry['end_time'])) : 'Going On'  ?></td>
                                        <td class="font-semibold"><?= $entry['duration'] ?></td>

                                    </tr>

                                    <?php }}?>
                                </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <?php } ?>

        </div>

    </div>
</div>
</div></div>


<?php init_tail(); ?>
<script src="https://visjs.github.io/vis-timeline/standalone/umd/vis-timeline-graph2d.min.js"></script>

<script>

<?php
$opsColor = $ops / 100;
$dynamicColor = interpolateColor("#ff0000", "#C2E812", $opsColor);
?>

var ctx = document.getElementById('progressChart').getContext('2d');

var chart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ["%","%"],
        datasets: [
            {
                data: [<?= round($ops, 2) ?>, <?= 100 - round($ops, 2) ?>],
                backgroundColor: ['<?= $dynamicColor ?>', '#e5e5e5'],
                borderWidth: 5,
                borderAlign: 'inner',
                label: "%",
            },
    ]
    },
    options: {
        cutoutPercentage: 50,
        maintainAspectRatio: false,
        legend: {
            display: false
         },
    }
});

</script>
<?php 
if($from == $to){
?>
<script>

    
function getCurrentTimeInAsiaKolkata() {
    const now = new Date();
    const timeZone = 'Asia/Kolkata';
    const localTimeString = now.toLocaleString('en-US', { timeZone });
  
    return new Date(localTimeString);
}
    
var shift_timings = <?php echo json_encode($shift_timings); ?>;
var afk_offline_entries = <?php echo json_encode($afk_offline_entries); ?>;
var clock_in_entries = <?php echo json_encode($clock_in_entries); ?>;


const today = new Date("<?php echo $from; ?>");
let startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 0, 0, 0); 
let endDate = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 23, 59, 59); 
console.log(endDate);

var items = new vis.DataSet();
var options = {
    zoomMin: 1000 * 60 * 60, // one hour in milliseconds
    zoomMax: 1000 * 60 * 60 * 24 * 31, // 31 days in milliseconds
    height: "180px",
    min: startDate,
    max: endDate,
    moveable: false,
    start: startDate,
    end: endDate
};

clock_in_entries.forEach(clock => {
    const inTime = new Date(clock.clock_in).toISOString();
    const outTime = new Date(clock.clock_out).toISOString();

    items.add({
        content: 'Clock in',
        start: inTime,
        end: outTime,
        type: 'range',
        className: 'clock-in-time',
        group: 2
    });
});

for(let shiftKey in shift_timings) {
    let shift = shift_timings[shiftKey];
    if(!shift.start || !shift.end){
        continue;
    }
    let shiftStart = new Date(`${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()} ${shift.start}`).toISOString();
    let shiftEnd = new Date(`${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()} ${shift.end}`).toISOString();

    // If shift ends before it starts, add one day to the end date
    if(shift.end < shift.start) {
        let endDateTime = new Date(shiftEnd);
        endDateTime.setDate(endDateTime.getDate() + 1);
        shiftEnd = endDateTime.toISOString();
    }

    items.add({
        content: 'Shift',
        start: shiftStart,
        end: shiftEnd,
        type: 'range',
        className: 'shift-time',
        group: 3  // Group 3 for shifts. You can adjust as needed.
    });
}

afk_offline_entries.forEach(function (entry) {
  const start24HourTime = entry.start_time;
  const end24HourTime = entry.end_time;
  const startDateTime = new Date(`${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()} ${start24HourTime}`).toISOString();;
  const endDateTime = new Date(`${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()} ${end24HourTime}`).toISOString();;
  items.add({
    content: entry.status,
    start: startDateTime,
    end: endDateTime,
    type: 'range',
    className: 'afk-time',
    group: 1
  });
});

console.log("Shift Timings:", shift_timings);
console.log("AFK/Offline Entries:", afk_offline_entries);
console.log("Clock In Entries:", clock_in_entries);
var container = document.getElementById('visualization');
var timeline = new vis.Timeline(container, items, options);

// Setting the timeline to focus on our startDate to endDate
timeline.setWindow(startDate, endDate);
// timeline.setCurrentTime(getCurrentTimeInAsiaKolkata());

</script>

<?php }?>
</body>
</html>