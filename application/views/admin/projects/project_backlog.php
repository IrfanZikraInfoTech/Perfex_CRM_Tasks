<?php defined('BASEPATH') or exit('No direct script access allowed');



?>

<style>
    .collapsible-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s;
    }
    .expanded {
        max-height: 50em; /* large enough to accommodate content */
    }
</style>

<div class="panel_s">
    <div class="panel-body">

        <!-- Sprints Section -->
        <div class="mt-4">

        <?php foreach ($sprints as $sprint):  ?>

            <?php 
                $color = $sprint->status == 0 ? 'bg-slate-100 border-slate-100' : ($sprint->status == 1 ? 'bg-sky-100 border-sky-100' : 'bg-teal-100 border-teal-100');
            ?>
                   <div class="flex justify-end mb-2">
                        <div class="relative inline-flex">
                            <span class="mr-2 mt-2">Sort by:</span>

                            <select id="storySortOrder" onchange="sortStories(this.value)" class="rounded-lg border border-gray-300 bg-white py-2 pl-2 pr-8 text-md leading-5">
                                <option value="alphabetical" <?php echo ($_GET['sort'] === 'alphabetical' ? 'selected' : ''); ?>>Alphabetical Order</option>
                                <option value="startDate" <?php echo ($_GET['sort'] === 'startDate' ? 'selected' : ''); ?>>Story Start Date</option>
                                <option value="endDate" <?php echo ($_GET['sort'] === 'endDate' ? 'selected' : ''); ?>>Story End Date</option>
                                <option value="estimatedHours" <?php echo ($_GET['sort'] === 'estimatedHours' ? 'selected' : ''); ?>>Estimated Hours</option>
                                <option value="totalLoggedTime" <?php echo ($_GET['sort'] === 'totalLoggedTime' ? 'selected' : ''); ?>>Total Logged Time in Story</option>
                            </select>
                        </div>
                    </div>

            <!-- Existing Sprint -->
            <div class="border-2 border-solid <?= $color ?> rounded-lg mb-4 transition-all hover:shadow-lg ease-in-out duration-300" style="background:white;">
                
                <div class="flex justify-between items-center py-2 px-4 <?= $color ?> rounded-t-lg cursor-pointer" onclick="toggleCollapse(event, 'sprint-<?= $sprint->id ?>-content')">
                    <div class="flex items-center text-black font-bold text-base gap-2 w-1/3" >
                        <div class="fas fa-angle-down transform transition-transform duration-300"></div>
                        <input class="text-black font-bold text-base bg-transparent w-full px-2 py-1" value="<?= htmlspecialchars($sprint->name); ?>" onchange="updateSprintName(this.value, <?= $sprint->id ?>)" placeholder="Sprint Name" />
                    </div>
                    <div class="flex h-full items-center gap-4">


                        <div class="flex items-center gap-4">
                            <input type="date" placeholder="Start Date" class="bg-transparent hover:bg-white rounded-lg p-2 border-none transition-all hover:border-blue-500 ease-in-out duration-300" value="<?= htmlspecialchars($sprint->start_date); ?>" onchange="updateSprintDate('start_date',this.value, <?= $sprint->id ?>)">
                            <div>To</div>
                            <input type="date" placeholder="End Date" class="bg-transparent hover:bg-white rounded-lg p-2 border-none transition-all hover:border-blue-500 duration-300" value="<?= htmlspecialchars($sprint->end_date); ?>" onchange="updateSprintDate('end_date',this.value, <?= $sprint->id ?>)">
                        </div>
                      

                        <div class="flex flex-row gap-1">
                            <div class="w-5 h-5 text-xs bg-gray-200 text-black rounded-full flex justify-center items-center" data-toggle="tooltip" data-placement="top" title="Not Started: <?= $sprint->not_started_count ?>"><?= $sprint->not_started_count ?></div>
                            <div class="w-5 h-5 text-xs bg-blue-200 text-black rounded-full flex justify-center items-center" data-toggle="tooltip" data-placement="top" title="In Progress: <?= $sprint->in_progress_count ?>"><?= $sprint->in_progress_count ?></div>
                            <div class="w-5 h-5 text-xs bg-green-200 text-black rounded-full flex justify-center items-center" data-toggle="tooltip" data-placement="top" title="Done: <?= $sprint->completed_count ?>"><?= $sprint->completed_count ?></div>
                        </div>
                        
                        <button class="px-4 py-1 rounded <?= $sprint->status == 2 ? 'bg-green-500 text-white' : 'bg-white border border-solid border-gray-200 text-gray-800' ?> transition-all hover:shadow-lg shadow-md hover:scale-105"   onclick="updateSprintStatus(this);" data-status="<?= $sprint->status ?>" data-sprint-id="<?= $sprint->id ?>">
                            <?php 
                                if($sprint->status == 0){
                                    echo "Start";
                                }else if($sprint->status == 1){
                                    echo "Complete";
                                }else {
                                    echo "Edit Summary";
                                }
                            ?>
                        </button>



                        <button class="px-4 py-1 rounded bg-white border border-solid border-gray-200 text-gray-800 transition-all hover:shadow-lg shadow-md hover:scale-105" onclick="deleteSprint(<?= $sprint->id ?>)">Delete</button>
                    </div>
                </div>          

                <div class="collapsible-content expanded" id="sprint-<?= $sprint->id ?>-content">
                    <div class="p-4 flex flex-col gap-4 sprint-list" data-issue-id="<?= $sprint->id ?>" data-issue-type="sprint" id="sprint-<?= $sprint->id ?>-list">

                        <?php foreach ($sprint->stories as $story):
                            echo story($story, true);
                            endforeach; 
                        ?>
                       
                    </div>
                </div>
            </div>

        <?php endforeach; ?>

            <!-- New Sprint -->
            <div class="bg-white border-2 border-solid border-blue-100/40 rounded-lg transition-all hover:shadow-lg ease-in-out duration-300">
                
                <div class="flex justify-between items-center py-1 px-4 bg-blue-100/40 rounded-t-lg">

                    <input id="new_sprint_name" class="w-1/3 text-black font-bold text-base bg-transparent px-2 py-1" placeholder="New Sprint" />

                    <div class="flex items-center gap-4">
                        <input type="date" placeholder="Start Date" class="rounded-lg p-2 border border-solid border-gray-300 transition-all hover:border-blue-500 ease-in-out duration-300" id="sprint_start_date">
                        <div>To</div>
                        <input type="date" placeholder="End Date" class="rounded-lg p-2 border border-solid border-gray-300 transition-all hover:border-blue-500 ease-in-out duration-300" id="sprint_end_date">
                        <button class="bg-blue-500 text-white rounded-lg px-4 py-2 transition-all hover:bg-blue-600 ease-in-out duration-300" onclick="newSprint();">Create Sprint</button>
                    </div>
                </div>


                <div class="p-2 flex flex-col gap-4" id="new-sprint-list">
                    
                </div>
  
            </div>

        </div>

        <!-- Backlog Container -->
        <div class="bg-white border-x-4 border-b-4 border-gray-100 rounded-lg my-4 border-solid">

            <div class="bg-gray-100 p-3 flex flex-row justify-left items-center gap-2 cursor-pointer" onclick="toggleCollapse(event, 'backlog')">
                <div class="fas fa-angle-down transform transition-transform duration-300"></div>
                <div class="text-gray-800 font-bold text-lg">Backlog</div>
            </div>

            <div class="collapsible-content expanded" id="backlog">

                <div class="p-4">

                <div class="flex justify-between items-center">
                    <input type="text" placeholder="New Epic Name" class="flex-grow rounded-lg p-2 border border-solid border-gray-300 mr-2 transition-all hover:border-purple-500 ease-in-out duration-300" id="epicNameInput">
                    <button class="bg-purple-500 disabled:bg-purple-700 text-white rounded-lg px-4 py-2 transition-all hover:bg-purple-600 ease-in-out duration-300" id="createEpicButton" onclick="createEpic()">Create Epic</button>
                </div>

                <div id="epicContainer" class="flex flex-col gap-4 pt-4">
                <?php foreach ($epics as $epic): ?>

                    <div class="border-2 border-solid border-purple-100 rounded-lg transition-all hover:shadow-lg ease-in-out duration-300">
            
                        <div class="flex justify-between items-center py-2 px-4 bg-purple-100/40 rounded-t-lg border-solid border-purple-200 cursor-pointer" onclick="toggleCollapse(event, 'epic-<?= $epic->id ?>-content')">

                            <div class="flex items-center text-black font-bold text-base gap-2 w-1/3">
                                <div class="fas fa-angle-down transform transition-transform duration-300"></div>
                                <input class="w-full text-black font-bold text-base bg-transparent px-2 py-1" value="<?= htmlspecialchars($epic->name); ?>" onchange="updateEpicName(this.value, <?= $epic->id ?>)" placeholder="Epic Name" />
                            </div>

                            <div class="flex flex-row gap-2">
                                <button class="bg-green-500 text-white rounded-lg px-4 py-2 transition-all hover:bg-green-600 ease-in-out duration-300" onclick="newStory(<?= $epic->id ?>)">New Story</button>

                                <button class="bg-rose-500 text-white rounded-lg px-4 py-2 transition-all hover:bg-rose-600 ease-in-out duration-300" onclick="deleteEpic(<?= $epic->id ?>)">Delete</button>
                            </div>
                        </div>

                        <div class="collapsible-content expanded" id="epic-<?= $epic->id ?>-content">
                            <div class="p-4 flex flex-col gap-4 epic-list" data-issue-type="epic" data-issue-id="<?= $epic->id ?>" id="epic-<?= $epic->id ?>-list">
                            <?php foreach ($epic->stories as $story):
                                echo story($story);
                                 endforeach; ?>
                            </div>
                        </div>
                    </div>


                <?php endforeach; ?>
                </div>

                </div>

            </div>
        </div>

    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

<script>

    function toggleCollapse(event, elementId) {
        if (event.target.tagName.toLowerCase() === 'button' || event.target.tagName.toLowerCase() === 'input') {
            return;
        }
        var content = document.getElementById(elementId);

        var isExpanding = !content.classList.contains("expanded");

        content.classList.toggle("expanded");
        updateMaxHeight(content, isExpanding);
        var arrow = event.currentTarget.querySelector('.fa-angle-down');
        arrow.classList.toggle('rotate-[-90deg]');

    }

    function updateMaxHeight(collapsibleContent, isExpanding) {
        if (isExpanding) {
            // Set max-height to scrollHeight when expanding
            var maxHeight = collapsibleContent.scrollHeight + 'px';
            collapsibleContent.style.maxHeight = maxHeight;
        } else {
            // Reset max-height to 0 when collapsing
            collapsibleContent.style.maxHeight = '0';
        }
    }

    document.querySelectorAll('.collapsible-content').forEach(function(element) {
        updateMaxHeight(element, true);
    });


    var storyGroup = {
        name: 'shared',
        pull: true,
        put: true,
    };
    
    function handleStoryMove(evt) {
    var story_id = evt.item.getAttribute('data-story-id');
    var estimated_hours = evt.item.getAttribute('data-estimated-hours'); // Ensure this attribute is set correctly in the HTML
    var origin_issue_type = evt.from.getAttribute('data-issue-type'); // Check the attribute is set in HTML
    var target_issue_type = evt.to.getAttribute('data-issue-type'); // Check the attribute is set in HTML

    // Function to move the story back to the epic
    function moveStoryBack() {
        evt.from.appendChild(evt.item); // Move back to the original list
    }

    // Check if we're moving from 'epic' to 'sprint' and if estimated hours are not set or zero
    if (origin_issue_type === 'epic' && target_issue_type === 'sprint' && (!estimated_hours || parseFloat(estimated_hours) === 0)) {
        // Show Swal with input for estimated hours
        Swal.fire({
            title: 'Estimated Hours Required',
            input: 'number',
            inputAttributes: {
                min: '0.5',
                step: '0.5'
            },
            inputValue: '0.5', // Default if estimated hours are not set
            showCancelButton: true,
            confirmButtonText: 'Update Hours',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value || parseFloat(value) <= 0) {
                    return 'You must enter estimated hours greater than 0!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                // User entered a valid number, now update the story with the estimated hours
                updateStoryEstimatedHours(story_id, result.value);
                // Continue with the story move operation if needed
            } else {
                // No valid hours entered; move the story back to the epic
                moveStoryBack();
            }
        });

        // Cancel the current move operation
        return false;
    }

    // Other conditions: move the story normally
    // ...
}

function updateStoryEstimatedHours(story_id, hours) {
    // Update the estimated hours for the story on the server
    $.ajax({
        url: 'your-endpoint-to-update-hours', // Replace with your actual endpoint
        type: 'POST',
        data: {
            story_id: story_id,
            estimated_hours: hours
        },
        success: function(response) {
            // Handle the success, update the UI accordingly
        },
        error: function() {
            // Handle the error, you may want to move the story back in this case as well
        }
    });
}

    function initializeSortable(epicList) {
        new Sortable(epicList, {
            group: storyGroup,
            animation: 150,
            easing: "cubic-bezier(1, 0, 0, 1)",
            ghostClass: 'sortable-ghost',
            dragClass: "sortable-drag",
            onAdd: handleStoryMove
        });
    }

    document.querySelectorAll('.epic-list').forEach(initializeSortable);

    var sprintLists = document.querySelectorAll('.sprint-list');
    sprintLists.forEach(function(sprintList) {
        new Sortable(sprintList, {
            group: storyGroup,
            animation: 150,
            easing: "cubic-bezier(1, 0, 0, 1)",
            ghostClass: 'sortable-ghost',
            dragClass: "sortable-drag",
            onAdd: handleStoryMove
        });
    });

    var sortableNewSprint = new Sortable(document.getElementById('new-sprint-list'), {
        group: storyGroup,
        animation: 150,
        easing: "cubic-bezier(1, 0, 0, 1)",
        ghostClass: 'sortable-ghost',
        dragClass: "sortable-drag",
    });

</script>

<script>


var epics = <?php echo json_encode($epics); ?>;

function createEpic() {
    var epicName = $('#epicNameInput').val();
    var projectId = '<?= $project->id ?>';  // Assuming you have an input with id 'projectIdInput'
    var createButton = $('#createEpicButton');
    

    if(epicName == ""){
        Swal.fire({
            title: 'Empty Name',
            icon: 'error',
            text: 'Write some epic name... (pun intended)'
        });
        return;
    }

    // Disable the button and show loading gif
    createButton.prop('disabled', true);
    
    Swal.fire({
        title: 'Processing...',
        onBeforeOpen: () => {
            Swal.showLoading()
        },
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
    });
    
    $.ajax({
        url: admin_url + 'projects/create_epic',
        type: 'POST',
        data: {
            project_id: projectId,
            name: epicName
        },
        success: function(response) {
            var data = JSON.parse(response);
            
            // Enable the button
            createButton.prop('disabled', false);
            
            // Close SweetAlert2 loading popup
            Swal.close();
            
            // Add the new epic to the UI
            addEpicToUI(data.epic_id, epicName);

            epics.push({
                id: data.epic_id,
                name: epicName
            });

        },
        error: function(jqXHR, textStatus, errorThrown) {
            // Handle any errors
            console.error(textStatus, errorThrown);
            
            // Enable the button
            createButton.prop('disabled', false);
            
            // Close SweetAlert2 loading popup and show error message
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong! Please try again.',
            });
        }
    });
}

function addEpicToUI(epicId, epicName) {
    var newEpicHTML = `
    <div class="border-2 border-solid border-purple-100 rounded-lg transition-all hover:shadow-lg ease-in-out duration-300">
            
            <div class="flex justify-between items-center py-2 px-4 bg-purple-100/40 rounded-t-lg border-solid border-purple-200 cursor-pointer" onclick="toggleCollapse(event, 'epic-${epicId}-content')">

                <div class="flex items-center text-black font-bold text-base gap-2 w-1/3">
                    <div class="fas fa-angle-down transform transition-transform duration-300"></div>
                    <input class="w-full text-black font-bold text-base bg-transparent px-2 py-1" value="${epicName}" onchange="updateEpicName(this.value, ${epicId})" placeholder="Epic Name" />
                </div>

                <div class="flex flex-row gap-2">
                <button class="bg-green-500 text-white rounded-lg px-4 py-2 transition-all hover:bg-green-600 ease-in-out duration-300" onclick="newStory(${epicId})">New Story</button>
                <button class="bg-rose-500 text-white rounded-lg px-4 py-2 transition-all hover:bg-rose-600 ease-in-out duration-300" onclick="deleteEpic(${epicId})">Delete</button>
                </div>
            </div>

            <div class="collapsible-content expanded" id="epic-${epicId}-content">
                <div class="p-4 flex flex-col gap-4 epic-list" data-issue-type="epic" data-issue-id="${epicId}" id="epic-${epicId}-list">
                </div>
            </div>
        </div>
    `;
    $('#epicContainer').append(newEpicHTML);
    updateMaxHeight(document.getElementById("backlog"), true);
    var newEpicList = document.querySelector('#epic-'+epicId+'-list');
    initializeSortable(newEpicList);
}

function newStory(epic_id) {
    var url = admin_url + "tasks/task?rel_id=<?= $project->id ?>&rel_type=project&epic_id="+epic_id;
    new_task(url);
}

function newSprint() {

    var story_ids = [];
    $('#new-sprint-list .story').each(function() {
        var story_id = $(this).attr('data-story-id');
        story_ids.push(story_id);
    });

    var sprint_name = $('#new_sprint_name').val();
    var start_date = $('#sprint_start_date').val();
    var end_date = $('#sprint_end_date').val();
    var project_id = '<?= $project->id ?>';




    if (story_ids.length === 0) {
        Swal.fire('Error!', 'No stories to add to the new sprint.', 'error');
        return;
    }else if(sprint_name === ""){
        Swal.fire('Error!', 'Enter Sprint Name', 'error');
        return;
    }else if(!isValidDate(start_date)){
        Swal.fire('Error!', 'Enter correct start date!', 'error');
        return;
    }else if(!isValidDate(end_date)){
        Swal.fire('Error!', 'Enter correct end date!', 'error');
        return;
    }
    

    Swal.fire({
        title: 'Processing',
        html: 'Creating new sprint...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();

            $.ajax({
                url: admin_url + 'projects/create_new_sprint',
                type: 'POST',
                dataType: 'json',
                data: {
                    project_id: project_id,
                    name : sprint_name,
                    start_date : start_date,
                    end_date : end_date,
                    story_ids: story_ids
                },
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        location.reload();  // reload the page
                    } else {
                        Swal.fire('Error!', 'Failed to update stories.', 'error');
                    }       
                },
                error: function() {
                    Swal.close();
                    Swal.fire('Error!', 'Failed to create new sprint.', 'error');
                }
            });
        }
    });
}

function updateSprintName(new_name, sprint_id) {

        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to change the sprint's name.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // User confirmed, send AJAX request to update the sprint name
                $.ajax({
                    url: admin_url + 'projects/update_sprint_name',  // Assuming you have a route set up for this
                    type: 'POST',
                    data: {
                        sprint_id: sprint_id,
                        new_name: new_name
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.success) {
                            Swal.fire(
                                'Updated!',
                                "The sprint's name has been updated.",
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Error!',
                                "Failed to update the sprint's name.",
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            "Failed to update the sprint's name.",
                            'error'
                        );
                    }
                });
            }
        });
}

function updateSprintDate(col_name, new_date, sprint_id) {

    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to change the sprint's date.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // User confirmed, send AJAX request to update the sprint name
            $.ajax({
                url: admin_url + 'projects/update_sprint_date',  // Assuming you have a route set up for this
                type: 'POST',
                data: {
                    col_name : col_name,
                    sprint_id: sprint_id,
                    new_date: new_date
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.success) {
                        Swal.fire(
                            'Updated!',
                            "The sprint's date has been updated.",
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Error!',
                            "Failed to update the sprint's date.",
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error!',
                        "Failed to update the sprint's date.",
                        'error'
                    );
                }
            });
        }
    });
}

function updateEpicName(new_name, epic_id) {

        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to change the epic's epic name (pun intended).",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // User confirmed, send AJAX request to update the Epic name
                $.ajax({
                    url: admin_url + 'projects/update_epic_name',  // Assuming you have a route set up for this
                    type: 'POST',
                    data: {
                        epic_id: epic_id,
                        new_name: new_name
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.success) {
                            $(".epic_"+epic_id+"_name").text(new_name);
                            Swal.fire(
                                'Updated!',
                                "The epic's epic name has been updated. (pun intended)",
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Error!',
                                "Failed to update the epic's epic name. (pun intended)",
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            "Failed to update the epic's epic name. (pun intended)",
                            'error'
                        );
                    }
                });
            }
        });
}

function updateSprintStatus(button) {
    var sprintStatus = button.getAttribute('data-status');
    var newStatus;

    var sprint_id = button.getAttribute('data-sprint-id')

    var projectId = '<?= $project->id ?>';
    
    // Determine the new status based on the current status
    if (sprintStatus == '0') {
        newStatus = '1';  // Start the sprint
    } else if (sprintStatus == '1') {
        newStatus = '2';  // Complete the sprint
    } else {

        $.ajax({
            url: admin_url + 'projects/get_sprint',
            type: 'POST',
            data: {
                sprint_id: sprint_id
            },
            success: function(response) {
                response = JSON.parse(response);

                if (response.id) {
                    Swal.fire({
                    title: 'Sprint closing summary',
                    html: '<div class="w-full"><textarea class="border-2 border-solid border-gray-200 h-64 w-full p-2" id="sprint_closing_summary">'+response.closing_summary+'</textarea></div>',
                    allowOutsideClick: false,
                    showConfirmButton: true,
                    showCancelButton: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var summary = $("#sprint_closing_summary").val();
                            if(summary == ""){
                                Swal.fire(
                                    'Error!',
                                    "Please write something!",
                                    'error'
                                );
                            }else{
                                Swal.fire({
                                title: 'Processing',
                                html: 'Updating spring summary...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                    }
                                });

                                $.ajax({
                                    url: admin_url + 'projects/set_sprint_closing_summary',
                                    type: 'POST',
                                    data: {
                                        sprint_id: sprint_id,
                                        summary: summary
                                    },
                                    success: function(response) {
                                        Swal.close();
                                        response = JSON.parse(response);
                                        if (response.success) {
                                            Swal.fire(
                                                'Success!',
                                                "Sprint closing summary is updated!",
                                                'success'
                                            );
                                        } else {
                                            // Handle error, e.g., another sprint is already active
                                            Swal.fire('Error!', response.error, 'error');
                                        }
                                    },
                                    error: function() {
                                        Swal.close();
                                        Swal.fire('Error!', 'Failed to update sprint status.', 'error');
                                    }
                                });
                                
                            }
                        }
                    });
                }
                 
            },
            error: function() {
                Swal.close();
                Swal.fire('Error!', 'Failed to get sprint summary.', 'error');
            }
        });
        return;
    }
    
    if(newStatus == '2'){
        Swal.fire({
        title: 'Sprint closing summary',
        html: '<div class="w-full"><textarea class="border-2 border-solid border-gray-200 h-64 w-full p-2" id="sprint_closing_summary"></textarea></div>',
        allowOutsideClick: false,
        showConfirmButton: true,
        showCancelButton: true,
        }).then((result) => {
            if (result.isConfirmed) {
                var summary = $("#sprint_closing_summary").val();
                if(summary == ""){
                    Swal.fire(
                        'Error!',
                        "Please write something!",
                        'error'
                    );
                }else{
                    Swal.fire({
                                title: 'Processing',
                                html: 'Adding spring summary...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                    }
                                });
                    $.ajax({
                        url: admin_url + 'projects/set_sprint_closing_summary',
                        type: 'POST',
                        data: {
                            sprint_id: sprint_id,
                            summary: summary
                        },
                        success: function(response) {
                            Swal.close();
                            response = JSON.parse(response);
                            if (response.success) {
                                modifySprintStatus(projectId, newStatus, sprint_id);
                            } else {
                                // Handle error, e.g., another sprint is already active
                                Swal.fire('Error!', response.error, 'error');
                            }
                        },
                        error: function() {
                            Swal.close();
                            Swal.fire('Error!', 'Failed to update sprint status.', 'error');
                        }
                    });
                    
                }
            }
        });

        
    }else{
        modifySprintStatus(projectId, newStatus, button.getAttribute('data-sprint-id'));
    }

    
}

function modifySprintStatus(project_id, sprint_status, sprint_id){
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
            project_id: project_id,
            sprint_status: sprint_status,
            sprint_id: sprint_id
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
        }
    });
}

function deleteSprint(sprint_id) {
    
    Swal.fire({
            title: 'Are you sure?',
            text: "You are about to delete sprint.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {

                Swal.fire({
                title: 'Processing',
                html: 'Deleting sprint...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }});

                $.ajax({
                    url: admin_url + 'projects/delete_sprint',
                    type: 'POST',
                    data: {
                        sprint_id: sprint_id
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
                        Swal.fire('Error!', 'Failed to delete sprint.', 'error');
                    },
                    complete: function() {
                        // Hide loading icon
                        button.disabled = false;
                    }
                });
            }

        });

    
}

async function deleteEpic(epicId) {
    const { value: confirmDeletion } = await Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to delete this epic? The stories will be moved to another epic.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    });

    if (confirmDeletion) {

        const response = await fetch(admin_url + 'projects/get_epic_list/<?= $project->id; ?>');
        const epicList = await response.json();
        const filteredEpicList = epicList.filter(epic => epic.id != epicId);
        const epicButtons = filteredEpicList.map(epic => `
            <button data-epic-id="${epic.id}" class="flex items-center justify-center p-2 mt-2 w-full bg-blue-500 hover:bg-blue-600 text-white rounded-md" onclick="transferStories(${epicId},${epic.id})">
                ${epic.name}
            </button>
        `).join('');

        

        Swal.fire({
            title: 'Choose an epic to transfer current stories to',
            html: epicButtons,
            showCancelButton: true,
            showConfirmButton: false,
            confirmButtonColor: '#29b952',
        });
    }
}

function transferStories(fromEpicId, toEpicId) {
    Swal.fire({
                title: 'Processing',
                html: 'Deleting epic...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }});
    $.ajax({
        url: admin_url + 'projects/delete_epic',
        type: 'POST',
        data: {
            from_epic_id: fromEpicId,
            to_epic_id : toEpicId
        },
        success: function(response) {
            Swal.close();
            response = JSON.parse(response);
            if (response.success) {
                Swal.close();
                location.reload();
            } else {
                // Handle error, e.g., another sprint is already active
                Swal.fire('Error!', "Failed to delete epic", 'error');
            }
        },
        error: function() {
            Swal.close();
            Swal.fire('Error!', 'Failed to delete epic.', 'error');
        }
    });
}



function waitForElement(selector, callback) {
    var interval = setInterval(function() {
        var element = $(selector);
        if (element.length) {
            clearInterval(interval);
            callback(element);
        }
    }, 100);  // check every 100ms
}
function isValidDate(dateString) {
    var date = new Date(dateString);
    return date instanceof Date && !isNaN(date);
}
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}
function sortStories(value) {
    var currentURL = window.location.href;
    var newURL;

    // Check if URL already has parameters
    if (currentURL.includes('?')) {
        // If 'sort' parameter already exists in URL, replace its value. Otherwise, add the 'sort' parameter.
        if (currentURL.includes('sort=')) {
            newURL = currentURL.replace(/(sort=)[^\&]+/, '$1' + value);
        } else {
            newURL = currentURL + '&sort=' + value;
        }
    } else {
        newURL = currentURL + '?sort=' + value;
    }
// console.log(value)
    location.href = newURL;
}


</script>