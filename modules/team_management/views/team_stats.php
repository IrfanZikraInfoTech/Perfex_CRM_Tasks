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
    .noscroll::-webkit-scrollbar {
        height: 5px;
    }

</style>
<div id="wrapper">

   <div class="content flex flex-col gap-10">


        <!--\\\\\\\ Team Stats \\\\\\\\-->

        <div class="w-full">
            <div class="w-full mb-4">
                <h2 class="text-3xl font-bold text-center">Team Stats</h2>
            </div>

            <div class="flex flex-col w-full p-4 rounded-2xl bg-white shadow-none hover:shadow-lg transition-all duration-400">

                <div class="grid lg:grid-cols-3 grid-cols-2 gap-2">

                <div class="w-full h-36 flex flex-col items-center justify-center bg-gradient-to-r from-blue-500 via-blue-600/90 to-blue-800
 rounded-lg rounded-xl text-white text-center shadow-none hover:shadow-lg transition-all duration-300 gap-2">
                    <div class="text-xl font-semibold">Total Time Today:</div>
                    <div class="text-3xl font-bold"><?php echo convertSecondsToRoundedTime($timers->todayTime); ?></div>
                </div>

                <div class="w-full h-36 flex flex-col items-center justify-center bg-gradient-to-b from-pink-400 via-pink-500 to-pink-600
 rounded-lg hover:rounded-xl text-white text-center shadow-none hover:shadow-lg transition-all duration-300 gap-2">
                    <div class="text-xl font-semibold">Time Yesterday:</div>
                    <div class="text-3xl font-bold"><?php echo convertSecondsToRoundedTime($timers->yesterdayTime); ?></div>
                </div>

                <div class="w-full h-36 flex flex-col items-center justify-center bg-gradient-to-br from-cyan-400 via-cyan-500 to-cyan-600
 rounded-lg hover:rounded-xl text-white text-center shadow-none hover:shadow-lg transition-all duration-300 gap-2">
                    <div class="text-xl font-semibold">Time This Week:</div>
                    <div class="text-3xl font-bold"><?php echo convertSecondsToRoundedTime($timers->weekTime); ?></div>
                </div>

                <div class="w-full h-36 flex flex-col items-center justify-center bg-gradient-to-tl from-lime-500/80 via-lime-500 to-lime-600
 rounded-lg hover:rounded-xl text-white text-center shadow-none hover:shadow-lg transition-all duration-300 gap-2">
                    <div class="text-xl font-semibold">Total Ongoing Tasks:</div>
                    <div class="text-3xl font-bold"><?php echo $timers->totalOngoingTasks; ?></div>
                </div>

                <div class="w-full h-36 flex flex-col items-center justify-center bg-gradient-to-tl from-violet-400 via-violet-500 to-violet-600
 rounded-lg hover:rounded-xl text-white text-center shadow-none hover:shadow-lg transition-all duration-300 gap-2">
                    <div class="text-xl font-semibold">Most efficient Member:</div>
                    <div class="text-2xl font-semibold flex flex-row items-center gap-2">
                        <?php echo $timers->maxTasksCompletedName; ?>
                        <div class="w-full">
                            <?php echo ($timers->maxTasksCompletedId) ? staff_profile_image($timers->maxTasksCompletedId, ['h-12', 'w-full', 'object-cover', 'md:h-full', 'md:w-14', 'staff-profile-image-thumb', 'mx-auto'], 'thumb') : ''; ?>
                        </div>
                    </div>
                </div>

                <div class="w-full h-36 flex flex-col items-center justify-center bg-gradient-to-tr from-rose-500 via-rose-600 to-rose-700
 rounded-lg hover:rounded-xl text-white text-center shadow-none hover:shadow-lg transition-all duration-300 gap-2">
                    <div class="text-xl font-semibold">Most Time Put in by:</div>
                    <div class="text-2xl font-semibold flex flex-row items-center gap-2">
                        <?php echo $timers->maxHoursPutInName; ?>
                        <div class="w-full">
                            <?php echo ($timers->maxHoursPutInId) ? staff_profile_image($timers->maxHoursPutInId, ['h-12', 'w-full', 'object-cover', 'md:h-full', 'md:w-14', 'staff-profile-image-thumb', 'mx-auto'], 'thumb') : ''; ?>
                        </div>
                    </div>
                </div>


                </div>

            </div>
        </div>

        <!--\\\\\\\ Activities \\\\\\\\-->

        <div class="w-full">

            <div class="w-full mb-4">
                <h2 class="text-3xl font-bold text-center">Activites</h2>
            </div>

            <div class="w-full flex 2xl:flex-row flex-col gap-4">

                <!--\\\\\\\ Table Online/AFK \\\\\\\\-->

                <div class="2xl:w-1/2 w-full">
                    <div class="">
                        <div class="align-middle inline-block min-w-full">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class=" table-fixed">
                                
                                <colgroup>
                                    <col class="w-[5%]" />
                                    <col class="w-[30%]" />
                                    <col class="w-[60%]" />
                                </colgroup>
                                <thead class="bg-gray-50">
                                    <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        S.no
                                    </th>
                                    <th scope="col" class=" px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Working Staff
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tasks Allocated
                                    </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php $i = 1; ?>
                                    <?php foreach ($staff_members as $staff): ?>
                                    <?php if ($staff->working): ?>

                                        <tr class="transition-all duration-300 hover:bg-<?php echo ($staff->status == 'Online') ? 'emerald' : 'cyan'; ?>-300/20">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo $i; ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo $staff->firstname.' '.$staff->lastname; ?> <a class="text-xs ml-4 w-4 h-4 inline-block text-center rounded-full text-white hover:text-white bg-<?php echo ($staff->status == 'Online') ? 'emerald' : 'cyan'; ?>-300" data-toggle="tooltip" title="<?php echo $staff->status;?>"><i class="fa fa-info"></i></a></div>
                                        </td>

                                        <td class="whitespace-nowrap px-3 py-4">
                                            <div class="flex flex-col text-sm text-gray-900 items-center gap-2">
                                                
                                                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" data-toggle="modal" data-target="#all-tasks-modal-<?php echo $staff->staffid; ?>">
                                                    Show All
                                                </button>

                                            </div>
                                            
                                        </td>

                                        </tr>
                                        
                                        <div class="modal fade" id="all-tasks-modal-<?php echo $staff->staffid; ?>" tabindex="-1" role="dialog" aria-labelledby="allTasksModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header flex flex-row">
                                                        <div class="modal-title text-2xl" id="allTasksModalLabel"><?php echo $staff->firstname ?>'s Tasks</div>
                                                    </div>
                                                    <div class="modal-body text-lg">
                                                        <ul class="list-decimal pl-10">
                                                            <?php
                                                                if(isset($staff->all_tasks)){

                                                                foreach ($staff->all_tasks as $task):
                                                                    if ($task->id == $staff->currentTaskId) {
                                                                    
                                                                    if(!empty($task->project_name)){
                                                                        echo '<li class="p-2 transition-all bg-emerald-200/20"> ' .$task->project_name .' / '. $task->name . '</li>';
                                                                    }else{
                                                                        echo '<li class="p-2 transition-all bg-emerald-200/20"> '.$task->name . '</li>';
                                                                    }
                                                                   

                                                                    }
                                                                    else{

                                                                        if(!empty($task->project_name)){
                                                                            echo '<li class="p-2 "> ' .$task->project_name .' / '. $task->name . '</li>';
                                                                        }else{
                                                                            echo '<li class="p-2"> '.$task->name . '</li>';
                                                                        }

                                                                    }
                                                                endforeach;
                                                                }else{
                                                                    echo '<li class="p-2"> No Tasks Assigned </li>';
                                                                }
                                                            ?>
                                                        </ul>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php $i++; ?>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                                </table>
                                
                            </div>
                        </div>
                    </div>
                </div>
                
                <!--\\\\\\\ Table Offline \\\\\\\\-->

                <div class="2xl:w-1/2 w-full">
                    <div class="">
                        <div class="align-middle inline-block min-w-full">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class=" table-fixed">
                                
                                <colgroup>
                                    <col class="w-[5%]" />
                                    <col class="w-[30%]" />
                                    <col class="w-[60%]" />
                                </colgroup>
                                <thead class="bg-gray-50">
                                    <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        S.no
                                    </th>
                                    <th scope="col" class=" px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Not working staff
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tasks Allocated
                                    </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php $i = 1; ?>
                                    <?php foreach ($staff_members as $staff): ?>
                                    <?php if (!$staff->working): ?>

                                        <tr class="transition-all duration-300 <?php echo ($staff->status == "Offline") ? "hover:bg-gray-300/20" : (($staff->status == "Online") ? "hover:bg-emerald-200/20" : "hover:bg-amber-300/20"); ?>">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo $i; ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            
                                            <div class="text-sm text-gray-900"><?php echo $staff->firstname.' '.$staff->lastname; ?>
                                            
                                            <a class="text-xs ml-4 w-4 h-4 inline-block text-center rounded-full text-white hover:text-white bg-<?php echo ($staff->status == "Offline") ? "gray" : (($staff->status == "Online") ? "emerald" : "amber"); ?>-300" data-toggle="tooltip" title="<?php echo $staff->status;?>"><i class="fa fa-info"></i></a>
                                        
                                        </div>
                                        </td>

                                        <td class="whitespace-nowrap px-3 py-4">
                                            <div class="flex flex-col text-sm text-gray-900 items-center gap-2">

                                                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" data-toggle="modal" data-target="#all-tasks-modal-<?php echo $staff->staffid; ?>">
                                                    Show All
                                                </button>

                                            </div>
                                            
                                        </td>


                                        </tr>

                                        <div class="modal fade" id="all-tasks-modal-<?php echo $staff->staffid; ?>" tabindex="-1" role="dialog" aria-labelledby="allTasksModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header flex flex-row">
                                                        <div class="modal-title text-2xl" id="allTasksModalLabel"><?php echo $staff->firstname ?>'s Tasks</div>
                                                    </div>
                                                    <div class="modal-body text-lg">
                                                        <ul class="list-decimal pl-10">
                                                            <?php
                                                                foreach ($staff->all_tasks as $task):
                                                                    if ($task->id == $staff->currentTaskId) {
                                                                    
                                                                    if(!empty($task->project_name)){
                                                                        echo '<li class="p-2 transition-all bg-emerald-200/20"> ' .$task->project_name .' / '. $task->name . '</li>';
                                                                    }else{
                                                                        echo '<li class="p-2 transition-all bg-emerald-200/20"> '.$task->name . '</li>';
                                                                    }
                                                                   

                                                                    }
                                                                    else{

                                                                        if(!empty($task->project_name)){
                                                                            echo '<li class="p-2 "> ' .$task->project_name .' / '. $task->name . '</li>';
                                                                        }else{
                                                                            echo '<li class="p-2"> '.$task->name . '</li>';
                                                                        }

                                                                    }
                                                                endforeach;
                                                            ?>
                                                        </ul>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php $i++; ?>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>



            </div>
        </div>
        


        <!--\\\\\\\ Tasks \\\\\\\\-->
                
        <div class="w-full">
            <div class="w-full mb-4">
                <h2 class="text-3xl font-bold text-center">All Tasks</h2>
            </div>

            <div class="p-4 bg-white">
                
                <h2 class="text-2xl font-bold text-center">Tasks past due date</h2>
                <table id="tasks-table-due" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Assigned To:</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Priority</th>
                        </tr>
                    </thead>
                </table>

                <h2 class="text-2xl font-bold text-center">Tasks due today</h2>
                <table id="tasks-table-due-today" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Assigned To:</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Priority</th>
                        </tr>
                    </thead>
                </table>

                <h2 class="text-2xl font-bold text-center">All Tasks</h2>
                <table id="tasks-table-all"  class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Assigned To:</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Priority</th>
                        </tr>
                    </thead>
                </table>

            </div>


                

            </div>
        </div>

      
   </div>

</div>

<?php init_tail(); ?>

<script>
    $('#tasks-table-due').DataTable({
        "ajax": {
            "url": "<?= admin_url('team_management/tasks_table_due') ?>",
            "dataSrc": "data"
        },
        "columns": [
            {
            "data": null,
            "render": function ( data, type, row ) {
                let projectNameSpan = '';
                if (row.project_name !== null) {
                    projectNameSpan = '<a target="_blank" href="<?php echo admin_url() ?>projects/view/'+row.rel_id+'" style="font-size:12px;">' + row.project_name + '</a>';
                }
                return '<div class="flex flex-col"><a target="_blank" href="<?php echo admin_url() ?>tasks/view/'+row.id+'">' + row.name + '</a>' + projectNameSpan + '</div>';
            }
            },
            { 
            "data": "assigned",
            "render": function(data, type, row) {
                return data.join(', ');
            }
            },
            { 
            "data": "status"
            },
            { 
            "data": "startdate" 
            },
            { 
            "data": "duedate" 
            },
            { 
            "data": "priority" 
            }
        ],
        ordering: false,
        initComplete: function() {
            $('#tasks-table-due_wrapper').removeClass('table-loading');
        },
    });

    $('#tasks-table-due-today').DataTable({
        "ajax": {
            "url": "<?= admin_url('team_management/tasks_table_due_today') ?>",
            "dataSrc": "data"
        },
        "columns": [
            {
            "data": null,
            "render": function ( data, type, row ) {
                let projectNameSpan = '';
                if (row.project_name !== null) {
                    projectNameSpan = '<a target="_blank" href="<?php echo admin_url() ?>projects/view/'+row.rel_id+'" style="font-size:12px;">' + row.project_name + '</a>';
                }
                return '<div class="flex flex-col"><a target="_blank" href="<?php echo admin_url() ?>tasks/view/'+row.id+'">' + row.name + '</a>' + projectNameSpan + '</div>';
            }
            },
            { 
            "data": "assigned",
            "render": function(data, type, row) {
                return data.join(', ');
            }
            },
            { 
            "data": "status"
            },
            { 
            "data": "startdate" 
            },
            { 
            "data": "duedate" 
            },
            { 
            "data": "priority" 
            }
        ],
        ordering: false,
        initComplete: function() {
            $('#tasks-table-due-today_wrapper').removeClass('table-loading');
        },
    });

    $('#tasks-table-all').DataTable({
        "ajax": {
            "url": "<?= admin_url('team_management/tasks_table_all') ?>",
            "dataSrc": "data"
        },
        "columns": [
            {
            "data": null,
            "render": function ( data, type, row ) {
                let projectNameSpan = '';
                if (row.project_name !== null) {
                    projectNameSpan = '<a target="_blank" href="<?php echo admin_url() ?>projects/view/'+row.rel_id+'" style="font-size:12px;">' + row.project_name + '</a>';
                }
                return '<div class="flex flex-col"><a target="_blank" href="<?php echo admin_url() ?>tasks/view/'+row.id+'">' + row.name + '</a>' + projectNameSpan + '</div>';
            }
            },
            { 
            "data": "assigned",
            "render": function(data, type, row) {
                return data.join(', ');
            }
            },
            { 
            "data": "status"
            },
            { 
            "data": "startdate" 
            },
            { 
            "data": "duedate" 
            },
            { 
            "data": "priority" 
            }
        ],
        ordering: false,
        initComplete: function() {
            $('#tasks-table-all_wrapper').removeClass('table-loading');
        },
    });


</script>
</body>
</html>