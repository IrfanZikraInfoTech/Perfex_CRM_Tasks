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
</style>
<div id="wrapper">

   <div class="content flex flex-col gap-10">


        <!--\\\\\\\ Individual Stats \\\\\\\\-->

        <div class="w-full">
            <div class="w-full mb-4">
                <h2 class="text-3xl font-bold text-center">Individual Stats</h2>

                <?php if(has_permission('team_management', '', 'admin')){ ?>
                <div class="flex justify-center mt-5 mb-10">
                    <button target="_blank" id="universalReminders" class="bg-blue-500 hover:bg-blue-700 text-white hover:text-white shadow-blue-200 shadow-[5px_5px_0_0] hover:shadow-blue-600 hover:shadow-[0px_0px_0_0] transition-all font-bold py-2 px-4 rounded">
                        Send Reminders!!!
                </button>
                </div>
                <?php } ?>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">
                <?php foreach ($staff_members as $staff): ?>

                <div class="flex flex-col bg-white rounded-lg overflow-hidden transform transition-all duration-500 ease-in-out hover:scale-[0.97] shadow-md hover:shadow-lg">
                    <div class="flex justify-center items-center p-4 bg-gray-200">
                        <?php echo staff_profile_image($staff->staffid, ['h-48', 'w-48', 'rounded-full', 'object-cover'], 'thumb'); ?>
                    </div>
                    <div class="p-4">
                        <h2 class="text-lg font-bold text-center"><?php echo $staff->firstname ?></h2>

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
                        <div class="mt-6 grid grid-cols-2 gap-2">
                            <a target="_blank" href="<?= admin_url();?>team_management/staff_stats/<?= $staff->staffid.'/'.date('n') ?>" class="text-center py-2 px-3 text-white rounded bg-blue-500 hover:bg-blue-600 hover:text-gray-100">View Stats</a>
                            <a target="_blank" href="<?= admin_url();?>team_management/control_room/<?= $staff->staffid ?>" class="text-center py-2 px-3 text-white rounded bg-green-500 hover:bg-green-600 hover:text-gray-100">Control Room</a>
                        </div>
                        <?php }?>
                    </div>
                </div>

                <?php endforeach; ?>
            </div>


        </div>

      
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