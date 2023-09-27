<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php

if(isset($active_sprint)){

?>

<!-- Existing Sprint -->
<div class="border-2 border-solid border-gray-200 rounded-lg mb-4 transition-all hover:shadow-lg ease-in-out duration-300" style="background:white;">
    
    <div class="flex justify-between items-center py-2 px-4 rounded-t-lg" >
                    <div class="flex items-center text-black font-bold text-base gap-2 w-1/3" >

                        <input class="text-black font-bold text-base bg-transparent w-full px-2 py-1" value="<?= htmlspecialchars($active_sprint->name); ?>" placeholder="Sprint Name" readonly/>
                    </div>
                    <div class="flex h-full items-center gap-4">

                        <div class="flex items-center gap-4">
                            <input type="date" placeholder="Start Date" class="bg-transparent hover:bg-white rounded-lg p-2 border-none transition-all hover:border-blue-500 ease-in-out duration-300" value="<?= htmlspecialchars($active_sprint->start_date); ?>" readonly>
                            <div>To</div>
                            <input type="date" placeholder="End Date" class="bg-transparent hover:bg-white rounded-lg p-2 border-none transition-all hover:border-blue-500 duration-300" value="<?= htmlspecialchars($active_sprint->end_date); ?>" readonly>
                        </div>

                        <div class="flex flex-row gap-1">
                            <div class="w-5 h-5 text-xs bg-gray-200 text-black rounded-full flex justify-center items-center" data-toggle="tooltip" data-placement="top" title="Not Started: <?= $active_sprint->not_started_count ?>"><?= $active_sprint->not_started_count ?></div>
                            <div class="w-5 h-5 text-xs bg-blue-200 text-black rounded-full flex justify-center items-center" data-toggle="tooltip" data-placement="top" title="In Progress: <?= $active_sprint->in_progress_count ?>"><?= $active_sprint->in_progress_count ?></div>
                            <div class="w-5 h-5 text-xs bg-green-200 text-black rounded-full flex justify-center items-center" data-toggle="tooltip" data-placement="top" title="Done: <?= $active_sprint->completed_count ?>"><?= $active_sprint->completed_count ?></div>
                        </div>
                        
                        <button class="px-4 py-1 rounded <?= $active_sprint->status == 2 ? 'bg-green-500 text-white' : 'bg-white border border-solid border-gray-200 text-gray-800' ?> transition-all hover:shadow-lg shadow-md hover:scale-105 disabled:hidden" <?= ($active_sprint->status == 2) ? 'disabled' : '' ?>  onclick="updateSprintStatus(this);" data-status="<?= $active_sprint->status ?>" data-sprint-id="<?= $active_sprint->id ?>">
                            <?php 
                                if($active_sprint->status == 0){
                                    echo "Start";
                                }else if($active_sprint->status == 1){
                                    echo "Complete";
                                }else {
                                    echo "Completed";
                                }
                            ?>
                        </button>
                    </div>
    </div>          
</div>

<div id="kanban-params">
<input type="hidden" name="sprint_id" value="<?= $active_sprint->id ?>">
</div>
<div class="tw-mt-5">
    <div class="kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
        <div class="row">
            <div class="container-fluid">
                <div id="kan-ban"></div>
            </div>
        </div>
    </div>
</div>


<script>
function updateSprintStatus(button) {
    var sprintStatus = button.getAttribute('data-status');
    var newStatus;

    var projectId = '<?= $project->id ?>';
    
    // Determine the new status based on the current status
    if (sprintStatus == '0') {
        newStatus = '1';  // Start the sprint
    } else if (sprintStatus == '1') {
        newStatus = '2';  // Complete the sprint
    } else {
        // Sprint is already completed, nothing to do
        return;
    }

    // Show a loading icon or similar while the request is being processed
    Swal.fire({
        title: 'Processing',
        html: 'Changing sprint status...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }});

    $.ajax({
        url: admin_url + 'projects/update_sprint_status',
        type: 'POST',
        data: {
            project_id: projectId,
            sprint_status: newStatus,
            sprint_id: button.getAttribute('data-sprint-id')
        },
        success: function(response) {
            Swal.close();
            response = JSON.parse(response);
            if (response.success) {
                location.reload();
            } else {
                // Handle error, e.g., another sprint is already active
                Swal.fire('Error!', response.error, 'error');
            }
        },
        error: function() {
            Swal.close();
            Swal.fire('Error!', 'Failed to update sprint status.', 'error');
        },
        complete: function() {
            // Hide loading icon
            button.disabled = false;
        }
    });
}

</script>
<?php
}else{
?>

<div class="w-full px-4 py-2 bg-white border-gray-200 text-base">No Active Sprint!</div>

<?php } ?>