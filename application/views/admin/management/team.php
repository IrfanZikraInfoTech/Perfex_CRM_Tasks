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
<div id="wrapper">

   <div class="content flex flex-col gap-10">


        <!--\\\\\\\ Individual Stats \\\\\\\\-->

       
        <div class="w-full">
    <div class="w-full mb-4">
        <h2 class="text-3xl font-bold text-center">Team</h2>
        <?php if(has_permission('team_management', '', 'admin')){ ?>
            <div class="flex justify-center mt-5 mb-10">
                <!-- Your existing code -->
            </div>
        <?php } ?>
    </div>

    <div class="flex flex-col gap-4">

        <?php  foreach ($departments as $department): ?>

            <div class="rounded-[40px] bg-white shadow-lg hover:shadow-xl border border-solid border-white hover:border-yellow-400 transition-all">

            <h2 class="text-xl pl-7 uppercase font-bold text-gray-800 text-center py-4"><?php echo $department->name; ?></h2>
            
            <div class="px-5 pb-5">
                <?php $staff_members = $this->team_management_model->get_staff_by_department($department->departmentid); 
                
                ?>
                    <div class="p-5 rounded-[40px] bg-gray-100 flex flex-row flex-wrap gap-10">
                    <?php  foreach ($staff_members as $staff): ?>
                        <!-- Your staff card code starts here -->
                        <div class="flex flex-col bg-white xl:w-1/4 2xl:w-1/5 lg:w-1/3 md:w-[40%] w-full rounded-[30px] overflow-hidden transition-all duration-500 ease-in-out hover:scale-[0.97] shadow-lg hover:shadow-xl border border-solid border-white hover:border-yellow-400">
                            <div class="flex justify-center items-center p-4 bg-gray-200">
                                <?php echo staff_profile_image($staff->staffid, ['h-48', 'w-48', 'rounded-full', 'object-cover'], 'thumb'); ?>
                            </div>
                            <div class="p-4">
                                <h2 class="text-lg font-bold text-center"><?php echo $staff->firstname. ' ' .$staff->lastname ?></h2>
                                <h2 class="text-lg font-bold text-center"><?php echo $staff->staff_title ?></h2>
                                <div class="mt-2">
                                    <div class="text-sm font-semibold text-gray-500">Current Status</div>
                                    <div class="flex flex-row gap-1 mt-1 text-gray-800"><div class="w-1 <?php echo 'bg-' . $staff->statusColor; ?> h-5 mr-2"></div><?php echo $staff->status; ?> \ <?php echo $staff->working ? 'Working' : 'Not working'; ?></div>
                                </div>
                                <div class="mt-4">
                                    <div class="text-sm font-semibold text-gray-500">Time spent today</div>
                                    <div class="mt-1 text-gray-800"><?php echo convertSecondsToRoundedTime($staff->live_time_today); ?></div>
                                </div>
                                <div class="mt-4">
                                    <div class="text-sm font-semibold text-gray-500">Current Task</div>
                                    <div class="mt-1 text-gray-800"><?php echo $staff->currentTaskName; ?></div>
                                </div>
                                <div class="mt-4">
                                    <div class="text-sm font-semibold text-gray-500">Current Project</div>
                                    <div class="mt-1 text-gray-800"><?php echo $staff->currentTaskProject ?? 'None'; ?></div>
                                </div>
                                <div class="mt-4">
                                    <div class="text-sm font-semibold text-gray-500">Task Timer</div>
                                    <div class="mt-1 text-gray-800"><?php echo convertSecondsToRoundedTime($staff->currentTaskTime); ?></div>
                                </div>
                                <?php if(has_permission('team_management', '', 'admin')){ ?>
                                <div class="mt-6 grid grid-cols-1 gap-2">
                                    <a target="_blank" href="<?= admin_url();?>team_management/control_room/<?= $staff->staffid ?>" class="text-center py-2 px-3 text-white rounded-3xl bg-sky-400 hover:bg-sky-500 hover:text-gray-100 transition-all">Control Room</a>
                                </div>
                                <?php }?>
                            </div>
                        </div>
                        <!-- Your staff card code ends here -->
                    <?php endforeach; ?>
                    </div>            
                </div>

            </div>
        <?php endforeach; ?>

      
   </div>

</div>


<?php init_tail(); ?>

<script>
    $('#universalReminders').on('click', function() {

        if(confirm("Are you sure?")){
            alert_float("info", "Please wait, Reminders take time");
        // Save the formFieldsData to the database
            $.ajax({
                url: '<?= admin_url();?>team_management/universalReminders', // Replace this with the actual URL to your controller function
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    if(response.success){
                        alert_float("success", "Reminders sent!");
                    }else{
                        alert_float("danger", "Reminders not sent!");
                    }
                    
                },
                error: function() {
                    // Show an error message or perform any other action on save failure
                    alert_float("danger", "Reminders not sent!");
                }
            });
        }
    });  
</script>

</body>
</html>