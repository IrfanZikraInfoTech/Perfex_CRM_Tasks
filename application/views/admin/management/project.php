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
        
       
        <div class="rounded-[40px] bg-white shadow-lg hover:shadow-xl border border-solid border-white hover:border-<?= get_option('management_theme_border')?> transition-all p-6">


        <div class="flex flex-col justify-center gap-4 items-center mb-6 mr-4">
            <div class="flex items-center bg-white rounded-full shadow-lg border border-solid border-white hover:border-<?= get_option('management_theme_border')?> transition-all ">
                    <div class="p-2">
                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-6a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <!-- Dropdown for task filter -->
                    <select name="task_filter" class="border-none px-4 text-lg py-2 bg-transparent focus:outline-none focus:ring-0 focus:shadow-none mr-2 text-gray-500 rounded-r-none pl-2" style="border:0px;
                    outline:0px;
                    background-color:white;
                    background:none;
                    -webkit-appearance:none;
                    appearance:none;
                    ">
                        <option value="all_tasks">All Tasks</option>
                        <option value="my_tasks"><?= ($staff_id == get_staff_user_id()) ? 'My' : 'Staff'; ?> Tasks</option>
                    </select>

                </div>

                <?php
                if(has_permission('team_management', '', 'admin')){

                    $staff_members = $this->staff_model->get();

                ?>
                    
                    <select id="staff" data-live-search="true" class="selectpicker text-2xl font-bold mb-2 text-uppercase" onchange="window.location.href=admin_url+'team_management/projects/'+this.value">
                        <?php 
                        foreach($staff_members as $staff_member){
                            $selected = '';
                            if($staff_member['staffid'] == $staff_id){
                                $selected = 'selected';
                            }
                            echo '<option '.$selected.' value="'.$staff_member['staffid'].'">'.$staff_member['full_name'].'</option>';
                        }
                        ?>
                        
                    </select>

                <?php
                }
 ?>
 <?php
if (has_staff_under(get_staff_user_id())) {
    $subordinate_ids = get_staff_under(get_staff_user_id());
    $subordinates_details = [];
   
    // Fetch details of the current staff
    $current_staff = $this->staff_model->get(get_staff_user_id());

    // Adding the current staff details to the array
    if($current_staff) {
        $subordinates_details[] = $current_staff;
    }

    // Fetching details for each subordinate and adding to the array
    foreach ($subordinate_ids as $id) {
        $subordinates_details[] = $this->staff_model->get($id);
    }
?>
<select id="subordinateStaff" data-live-search="true" class="selectpicker text-2xl font-bold mb-2 text-uppercase" onchange="window.location.href=admin_url+'team_management/projects/'+this.value">
    <?php 
    foreach($subordinates_details as $subordinate) {
        $selected = ($subordinate->staffid == $staff_id) ? 'selected' : '';
        echo '<option ' . $selected . ' value="' . $subordinate->staffid . '">' . $subordinate->full_name . '</option>';
    }
    // print_r($selected);
    ?>
</select>


<?php
}
?>

            </div>

        
            <div class="p-5 rounded-[40px] bg-<?= get_option('management_theme_background')?> flex flex-row flex-wrap gap-10 justify-between"> 
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-5 w-full">

                <?php

                    if(count($staff_projects) < 1){
                        echo '<h2 class="text-xl">No Projects to show :(</h2>';
                    }


                    foreach($staff_projects as $project): ?>
                        <?php 
                                
                            ?>
                        <div class="flex flex-col transition-all rounded-[50px] overflow-hidden p-5 bg-white shadow-xl h-[350px] relative"> 
                            <div class="uppercase tracking-wide text-xl text-center text-gray-700 font-bold mb-5">
                                <?php echo $project['name']; ?>
                            </div>
                            
                            <div class="flex flex-col h-full bg-gray-100 p-6 rounded-[50px] shadow-inner overflow-y-scroll myscrollbar">
                            <?php 

                        // Check if the active_sprint stories are set and not empty
                        if(isset($project['active_sprint']->stories) && !empty($project['active_sprint']->stories)): 

                            
                            foreach($project['active_sprint']->stories as $story): 


                                $isAssigned = in_array($staff_id, $story->assignees_ids) ? 'true' : 'false';
                    ?>
                            <button class="task-block bg-white px-3 py-2 rounded-xl cursor-pointer border border-gray-200 border-solid transition-all hover:border-<?= get_option('management_theme_border')?> hover:shadow-lg mb-4" data-task-id="<?= $story->id ?>" data-assigned="<?= $isAssigned ?>" onclick="init_task_modal(<?= $story->id ?>)">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold"><?php echo $story->name; ?></span>
                                    <span><?php echo format_task_status($story->status); ?></span>
                                </div>
                            </button>
                    <?php 
                            endforeach; ?>
                       <?php else:
                    ?>
                            <p class="text-center text-bold text-xl text-gray-500">No sprint active</p>
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

$("select[name='task_filter']").on('change', function(e) {

    // Get the value from the dropdown
    let taskFilterValue = $(this).val();
console.log(taskFilterValue);
    // Get all the task blocks
    let allTasks = $('.task-block');

    allTasks.each(function() {
        let task = $(this); // Convert the DOM element to a jQuery object for easier manipulation
        if(taskFilterValue === "all_tasks") {
            // If 'All Tasks' is selected, show all tasks
            task.show('slow'); // Equivalent to setting display to 'block'
        } else if (taskFilterValue === "my_tasks" && task.attr('data-assigned') === "true") {
            // If 'My Tasks' is selected and the task is assigned to the current user
            task.show('slow'); // Equivalent to setting display to 'block'
        } else {
            task.hide('slow'); // Equivalent to setting display to 'none'
        }
    });
});




</script>

