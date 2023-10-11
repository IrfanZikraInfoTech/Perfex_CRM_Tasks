<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
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
        <div class="w-full mb-2">
            <h2 class="text-3xl font-bold text-center">All Projects Worked On</h2>
        </div>
        <div class="flex justify-center mb-10 mr-4">
            <form method="GET" class="flex items-center bg-white rounded-full shadow-lg">
                <div class="p-2">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-6a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <!-- Dropdown for task filter -->
                <select name="task_filter" class="border-none bg-transparent focus:outline-none focus:ring-0 focus:shadow-none mr-2 text-gray-500 rounded-r-none pl-2">
                    <option value="all_tasks">All Tasks</option>
                    <option value="my_tasks">My Tasks</option>
                </select>

                <button type="submit" class="text-white py-2 px-4 rounded-full bg-blue-400 hover:bg-blue-500 transition-colors duration-200">
                    Filter
                </button>
            </form>
        </div>
       
        <div class="rounded-[40px] bg-white shadow-lg hover:shadow-xl border border-solid border-white hover:border-gray-400 transition-all p-6">
            <div class="p-5 rounded-[40px] bg-gray-100 flex flex-row flex-wrap gap-10 justify-between"> 
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6 p-5 w-full">
                    <?php foreach($staff_projects as $project): ?>
                        <?php 
                                $assignedTaskIds = array_map(function($task) { 
                                    if (is_object($task) && isset($task->id)) {
                                        return $task->id; 
                                    } elseif (is_array($task) && isset($task['id'])) {
                                        return $task['id'];
                                    }
                                    return null;
                                }, $project['assigned_tasks']); 
                            ?>
                        <div class="flex flex-col transition-all rounded-[50px] overflow-hidden p-5 bg-white shadow-xl h-[350px] relative"> 
                            <div class="uppercase tracking-wide text-xl text-center text-gray-700 font-bold mb-5">
                                <?php echo $project['name']; ?>
                            </div>
                            
                            <div class="flex flex-col h-full bg-gray-100 p-4 rounded-[50px] shadow-inner overflow-y-scroll myscrollbar">
                            <?php 
                        // Check if the active_sprint stories are set and not empty
                        if(isset($project['active_sprint']->stories) && !empty($project['active_sprint']->stories)): 
                            foreach($project['active_sprint']->stories as $story): 
                                $isAssigned = in_array($story->id, $assignedTaskIds) ? 'true' : 'false';
                    ?>
                            <button class="task-block bg-white px-3 py-2 rounded-xl cursor-pointer border border-gray-200 border-solid transition-all hover:border-gray-400 hover:shadow-lg mb-4" data-task-id="<?= $story->id ?>" data-assigned="<?= $isAssigned ?>" onclick="init_task_modal(<?= $story->id ?>)">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold"><?php echo $story->name; ?></span>
                                    <span><?php echo format_task_status($story->status); ?></span>
                                </div>
                            </button>
                    <?php 
                            endforeach; ?>
                       <?php else:
                    ?>
                            <p class="text-center text-bold text-xl text-gray-500">No stories active</p>
                    <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
            </div>
        </div>

    </div>
</div>



<?php init_tail(); ?>
<script>
document.querySelector("button[type='submit']").addEventListener('click', function(e) {
    e.preventDefault(); // To prevent the form from submitting

    // Get the value from the dropdown
    let taskFilterValue = document.querySelector("select[name='task_filter']").value;

    // Get all the task blocks
    let allTasks = document.querySelectorAll('.task-block');

    allTasks.forEach(task => {
        if(taskFilterValue === "all_tasks") {
            // If 'All Tasks' is selected, show all tasks
            task.style.display = 'block';
        } else if (taskFilterValue === "my_tasks" && task.getAttribute('data-assigned') === "true") {
            // If 'My Tasks' is selected and the task is assigned to the current user
            task.style.display = 'block';
        } else {
            task.style.display = 'none';
        }
    });
});



</script>

