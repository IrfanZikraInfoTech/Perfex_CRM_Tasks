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

function formatShift($shiftNumer)
{
    if($shiftNumer == "1")
        return "1st Shift";
    else if ($shiftNumer == "2")
        return "2nd Shift";
    else
        return "All Monthly";
}


?>

<style>

.no-scroll::-webkit-scrollbar {
  display: none;
}

</style>

<div id="wrapper" class="wrapper">

<div class="container mx-auto px-4 py-6">
    <div class="flex flex-row justify-between mb-6">
        <h1 class="text-3xl font-semibold">Monthly Reports</h1>
        <div class="max-w-sm flex flex-row gap-2">
        <select id="month-select" class="block appearance-none w-full bg-white  border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
            <option value="01" <?= $month == "01" ? "selected" : "" ?>>January</option>
            <option value="02" <?= $month == "02" ? "selected" : "" ?>>February</option>
            <option value="03" <?= $month == "03" ? "selected" : "" ?>>March</option>
            <option value="04" <?= $month == "04" ? "selected" : "" ?>>April</option>
            <option value="05" <?= $month == "05" ? "selected" : "" ?>>May</option>
            <option value="06" <?= $month == "06" ? "selected" : "" ?>>June</option>
            <option value="07" <?= $month == "07" ? "selected" : "" ?>>July</option>
            <option value="08" <?= $month == "08" ? "selected" : "" ?>>August</option>
            <option value="09" <?= $month == "09" ? "selected" : "" ?>>September</option>
            <option value="10" <?= $month == "10" ? "selected" : "" ?>>October</option>
            <option value="11" <?= $month == "11" ? "selected" : "" ?>>November</option>
            <option value="12" <?= $month == "12" ? "selected" : "" ?>>December</option>
        </select>

            <button onclick="changeReport();" class="bg-white hover:bg-gray-100 text-gray-800 font-semibold py-2 px-4 border border-gray-400 rounded shadow">Search</button>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6">
        <!-- Total Loggable Hours -->
        <div class="bg-gradient-to-br from-red-600 to-orange-600 shadow rounded p-4 text-white">
            <h2 class="text-xl font-semibold mb-2">Total Loggable Hours</h2>
            <p class="text-2xl"><?= convertSecondsToRoundedTime($report_data['total_loggable_hours']) ?></p>
        </div>

        <!-- Actual Total Logged in Time -->
        <div class="bg-gradient-to-br from-pink-600 to-purple-600 shadow rounded p-4 text-white">
            <h2 class="text-xl font-semibold mb-2">Actual Logged in Time</h2>
            <p class="text-2xl"><?= convertSecondsToRoundedTime($report_data['actual_total_logged_in_time']) ?> </p>
        </div>
        
        <!-- Task Completion Rate -->
        <div class="bg-gradient-to-br from-teal-600 to-green-500 shadow rounded p-4 text-white">

            <h2 class="text-xl font-semibold mb-2">Task Rates</h2>
            <div class="text-2xl">

            <?= $report_data['total_completed_tasks'] ?> / <?= $report_data['total_all_tasks'] ?> (<?= $report_data['total_tasks_rate'] ?>%)


            </div>
        </div>

        

        <!-- Most Clocked In Staff Member -->
        <div class="bg-gradient-to-br from-amber-500 to-yellow-500 shadow rounded p-4 text-white">

            <h2 class="text-xl font-semibold mb-2">Most Clocked In Staff Member</h2>

            <?php if (!empty($report_data['most_clocked_in_staff_member'])): ?>

                <div class="text-2xl flex align-center justify-between">
                    <div class="my-auto"><?= $report_data['most_clocked_in_staff_member']['firstname'] ?></div>


                    <div title="<?= $report_data['most_clocked_in_staff_member']['firstname'] ?>" data-toggle="tooltip" data-placement="top">
                        <?= staff_profile_image($report_data['most_clocked_in_staff_member']['staff_id'], ['border-2 border-solid object-cover w-12 h-full staff-profile-image-thumb'], 'thumb'); ?>
                    </div>
                </div>

            <?php else: ?>
                <p>No staff member found</p>
            <?php endif; ?>

        </div>

        <!-- Most Efficient Staff Member -->
        <div class="bg-gradient-to-br from-pink-500 to-indigo-500 shadow rounded p-4 text-white">
            <h2 class="text-xl font-semibold mb-2">Most Efficient Staff Member</h2>
            <?php if (!empty($report_data['most_eff_staff_member'])): ?>

            <div class="text-2xl flex align-center justify-between">
                <div class="my-auto">
                    <?= isset($report_data['most_eff_staff_member']->firstname) ? $report_data['most_eff_staff_member']->firstname : 'Default Value' ?>
                </div>
                <div title="<?= isset($report_data['most_eff_staff_member']->firstname) ? $report_data['most_eff_staff_member']->firstname : 'Default Value' ?>" data-toggle="tooltip" data-placement="top">
                    <?= isset($report_data['most_eff_staff_member']->staffid) ? staff_profile_image($report_data['most_eff_staff_member']->staffid, ['border-2 border-solid object-cover w-12 h-full staff-profile-image-thumb'], 'thumb') : '' ?>
                </div>
            </div>

            <?php else: ?>
            <p>No staff member found</p>
            <?php endif; ?>
        </div>

        <div class="w-full p-5 my-5 bg-white shadow rounded p-4 col-span-2">
            <div class="flex justify-between">
                <h2 class="card-title ms-1 text-uppercase text-center mb-4 w-full" style="font-weight: bold; color: #343a40; letter-spacing: 1.5px;padding-left:80px;">Organizational Report</h2>
                <h2 class="card-title ms-1 text-uppercase text-center mb-4 w-24" style="font-weight: bold; color: #343a40; letter-spacing: 1.5px;">
                <?php
                    echo convertSecondsToRoundedTime($report_data['all_daily_reports'][1]['total_loggable_hours']);
                ?>
                </h2>
            </div>
            <div class="d-flex justify-content-center">
                <canvas id="monthlyTimingsChart" style="width: 100%; height: 400px;"></canvas>
            </div>
        </div>

        <!-- All Staff Members -->
        <div class="bg-white shadow rounded p-4 col-span-2">
            <h2 class="text-xl font-semibold mb-4">Staff Members</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-sm font-medium text-gray-700">
                            <th class="px-4 py-2 border-b-2 border-gray-200">Name</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200">Shift</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200">Work</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200">Task Rate</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200">Summary</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200 text-sm text-gray-600">
                    
                    <?php foreach ($report_data['all_staff'] as $staff): ?>
                        <tr class="border-solid border-b border-gray-200">
                            <td class="border px-4 py-2 flex flex-row gap-2 items-center">
                                <?= $staff['firstname'] . ' ' . $staff['lastname'] ?>
                                <?= staff_profile_image($staff['staffid'], ['h-8', 'w-8', 'rounded-full'], 'thumb') ?>
                            </td>
                            <td class="border px-4 py-2">
                                <?= convertSecondsToRoundedTime($staff['total_shift_timings']) ?>
                            </td>
                            <td class="border px-4 py-2">
                                <?= convertSecondsToRoundedTime($staff['total_logged_in_time']) ?>
                            </td>
                            <td class="border px-4 py-2">
                                <?=  $staff['task_rate'] ?>
                            </td>
                            <td class="border px-4 py-2">
                                <a href="#" class="text-blue-500" data-staffname="<?= $staff['firstname'] ?>" data-staffid="<?= $staff['staffid'] ?>" data-toggle="modal" data-target="#monthlySummaryModal">Summary</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>
 

        <!-- All Tasks Worked On -->
        <div class="bg-white shadow rounded p-4 col-span-2">
            <h2 class="text-xl font-semibold mb-4">All Tasks Worked On</h2>
            <table class="w-full text-left border-collapse" id="allTasks">
                    <thead>
                        <tr class="text-sm font-medium text-gray-700">
                            <th class="px-4 py-2 border-b-2 border-gray-200">Name</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200">Worked by:</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200">Assigned</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200">Completed</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200">Status</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200">Total worked time:</th>
                            <th class="px-4 py-2 border-b-2 border-gray-200">Priority</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-sm text-gray-600">
                        <?php

                        foreach ($report_data['all_tasks_worked_on'] as $task): 
    
                        ?>
                            <tr class="border-solid border-b border-gray-200">

                            <td class="px-4 py-2 align-top" style="max-width:400px;">
                                <?php
                                $projectNameSpan = '';
                                if ($task['project_name'] !== null) {
                                    $projectNameSpan = '<a target="_blank" href="' . admin_url() . 'projects/view/' . $task['rel_id'] . '" style="font-size:12px;">' . $task['project_name'] . '</a>';
                                }
                                echo '<div class="flex flex-col"><a target="_blank" href="' . admin_url() . 'tasks/view/' . $task['id'] . '">' . $task['task_name'] . '</a>' . $projectNameSpan . '</div>';
                                ?>
                            </td>

                                <td class="px-4 py-2 flex flex-row gap-2">
                                <?php foreach ($task['staff'] as $staff): ?>
                                    
                                    <div title="<?= $this->team_management_model->id_to_name($staff['staff_id'], 'tblstaff', 'staffid', 'firstname') ?>" data-toggle="tooltip" data-placement="top">
                                    <?= staff_profile_image($staff['staff_id'], ['w-10 h-full staff-profile-image-thumb'], 'thumb') ?>
                                    </div>
                                <?php endforeach; ?>

                                </td>
                                <td class="px-4 py-2 align-top"><?= date("Y-m-d", strtotime($task['dateadded'])) ?></td>
                                <td class="px-4 py-2 align-top"><?= ($task['datefinished']) ? (date("Y-m-d", strtotime($task['datefinished']))) : '' ?></td>
                                <td class="px-4 py-2 align-top"><?= format_task_status($task['task_status']) ?></td>
                                <td class="px-4 py-2 align-top"><?= convertSecondsToRoundedTime($task['total_worked_time']) ?></td>
                                <td class="px-4 py-2 align-top"><?= task_priority($task['priority']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
            </table>
        </div>


        <!-- Monthly Summary -->
        <div class="bg-white shadow rounded p-4 col-span-2">
            <h2 class="text-xl font-semibold mb-2">Monthly Summary</h2>
            <div id="todaySummary"><?=htmlspecialchars_decode($monthly_summary)?></div>
        </div>
        
        <?php if (has_permission('team_management', '', 'admin')) : ?>
        <div class="bg-white shadow rounded p-4 col-span-2">
            
            <!-- Monthly summary editor (hidden by default) -->
            <div id="day-summary-editor">
                <textarea id="summary-editor" name="summary"><?=htmlspecialchars($monthly_summary)?></textarea>
                <div class="flex flex-row gap-2">
                    <button id="generate-summary" class="font-semibold my-2 px-4 py-2 bg-gray-200 rounded" type="button">Generate</button>
                    <button id="save-summary-btn" class="font-semibold my-2 px-4 py-2 bg-gray-200 rounded" type="button">Save</button>
                </div>
            </div>

        </div>
        <?php  endif; ?>
    </div>
</div>


</div>



<?php init_tail(); ?>


<script>
// First, get PHP data into JavaScript variables and convert seconds to hours
let actualTotalLoggedInTimeArray = [
    <?php 
        foreach($report_data['all_daily_reports'] as $day => $daily_report) {
            $hours = round($daily_report['actual_total_logged_in_time'] / 3600); // Convert seconds to hours
            echo $hours . ',';
        } 
    ?>
];

let ctx = document.getElementById('monthlyTimingsChart').getContext('2d');

// Create the bar chart
let monthlyTimingsChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: Array.from({ length: actualTotalLoggedInTimeArray.length }, (_, i) => i + 1),
        datasets: [{
            label: 'Actual Logged-in Time',
            data: actualTotalLoggedInTimeArray,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                max:2,  // Set the max value of y-axis to 8
                beginAtZero: true,
                ticks: {
                    callback: function(value, index, values) {
                        return value + ' hours';
                    }
                }
            }
        }
    }
});


$(document).ready(function() {
    $('#allTasks').DataTable({
        initComplete: function() {
            $('#allTasks_wrapper').removeClass('table-loading');
        },
    });
    // Initialize Summernote
    tinymce.init({
        selector: '#summary-editor',
        // Add any additional TinyMCE options you may need
    });
    // Save day summary on button click
    $('#save-summary-btn').click(function() {
        var summary = tinyMCE.activeEditor.getContent();
        alert_float("info", "Mailing summary...");
        $.post("<?=admin_url('team_management/save_monthly_summary')?>", {month: "<?=$month?>", year: "<?=$year?>", summary: summary}, function() {
            $('#todaySummary').html(summary);
            alert_float("success", "Success!");
        });
    });
});
function changeReport() {
    var month = document.getElementById("month-select").value;

    window.location.href = "<?=admin_url('team_management/monthly_reports')?>/" + <?= date('Y') ?> + "/" + month;
}
</script>
</body>
</html> 