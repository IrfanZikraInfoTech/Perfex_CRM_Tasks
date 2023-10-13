<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('user_widget'); ?>">
    <div class="panel_s user-data">
        <div class="panel-body home-activity">
            <div class="widget-dragger"></div>
            <div class="text-2xl font-semibold text-center text-gray-800 mb-6">Assigned Tasks</div>
            <div class="space-y-4">

                <?php
                $tasks = $this->tasks_model->get_user_tasks_assigned($GLOBALS['current_user']->staffid);
                $total_tasks = 0;
                $completed_tasks = 0;

                foreach ($tasks as $task) {
                    $current_time = time();
                    $start_time = strtotime($task->startdate);
                    $due_time = strtotime($task->duedate ? $task->duedate : $task->startdate);

                    if ($task->status == 5 && $due_time < $current_time) {
                        continue; // Skip tasks that are completed and have a due date before the current date.
                    }

                    $total_tasks++;

                    if ($task->status == 5) {
                        $badgeColor = 'bg-green-500 text-white';
                        $taskStatusText = 'Completed';
                    } elseif ($start_time > $current_time) {
                        $badgeColor = 'bg-blue-500 text-white';
                        $taskStatusText = 'Not Started';
                    } elseif ($due_time < $current_time && $task->status != 5) {
                        $badgeColor = 'bg-red-500 text-white';
                        $taskStatusText = 'Not Completed';
                    } elseif ($start_time <= $current_time && $due_time >= $current_time) {
                        $badgeColor = 'bg-yellow-400 text-black';
                        $taskStatusText = 'In Progress';
                    }

                    echo '
                    <div class="mx-3 flex justify-between items-center p-4 bg-gray-100 rounded-xl shadow    hover:shadow-xl transition-all duration-500">
                        <a onclick="init_task_modal(' . $task->id . '); return false" href="#" class="ml-4 font-semibold text-gray-800">' . $task->name . '</a>
                        <span class="px-4 py-1 rounded-full text-sm font-medium '.$badgeColor.'">'.$taskStatusText.'</span>
                    </div>
                    ';

                    if ($task->status == 5) {
                        $completed_tasks++;
                    }
                }

                $percentage = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100) : 0;
                ?>

                <div class="mt-8 flex justify-between items-center mx-3">
                    <div class="text-xl font-bold text-gray-800">Rate:</div>
                    <div class="text-lg">
                        <span class="text-green-700 font-extrabold"><?= $percentage ?></span>%
                        (<span class="text-green-600 font-semibold"><?= $completed_tasks ?></span>/<span><?= $total_tasks ?></span>)
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
