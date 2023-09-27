<?php defined('BASEPATH') or exit('No direct script access allowed');

function story($story, $show_epic = false) {

    // Prepare the assignee avatars and names for tooltip
    $assignee_avatars = '';
    foreach ($story->assignees as $assignee) {
        $assignee_avatars .= '<div class="w-8 h-8" data-toggle="tooltip" title="'.$assignee['full_name'].'">'.
                                staff_profile_image($assignee['assigneeid'], ['rounded-full'], 'thumb').
                             '</div>';
    }

    // Compute the time indicator text
    $current_date = new DateTime();  // get the current date
    $start_date = $story->startdate ? new DateTime($story->startdate) : null;
    $due_date = $story->duedate ? new DateTime($story->duedate) : null;

    if ($start_date && $current_date < $start_date) {
        $interval = $current_date->diff($start_date);
        $time_indicator_text = $interval->days . ' days remaining to start';
    } elseif ($due_date && $current_date > $due_date) {
        $interval = $current_date->diff($due_date);
        $time_indicator_text = $interval->days . ' days overdue';
    } elseif ($start_date && $due_date && $current_date >= $start_date && $current_date <= $due_date) {
        if ($current_date == $start_date) {
            $interval_remaining = $current_date->diff($due_date);
            $time_indicator_text = 'Started today, ' . $interval_remaining->days . ' days left';
        } else {
            $interval_started = $start_date->diff($current_date);
            $interval_remaining = $current_date->diff($due_date);
            $time_indicator_text = $interval_started->days . ' days since started, ' . $interval_remaining->days . ' days left';
        }
    } else {
        $time_indicator_text = 'Date information not available';
    }

    $color_classes = [
        1 => 'border-gray-100 hover:border-gray-200', // Not Started
        4 => 'border-cyan-100 hover:border-cyan-200', // In Progress
        3 => 'border-blue-100 hover:border-blue-200', // Testing
        2 => 'border-emerald-100 hover:border-emerald-200', // Awaiting Feedback
        5 => 'border-lime-100 hover:border-lime-200' // Completed
    ];

    // Get the color class for the current story
    $color_class = isset($color_classes[$story->status]) ? $color_classes[$story->status] : 'border-gray-300 hover:border-gray-400';

    $epic_html = "";

    if($show_epic){
        $epic_html = '<div class="text-fuchsia-900/70 text-xs epic_'.$story->epic->id.'_name">'.htmlspecialchars($story->epic->name). '</div>';
    }


    // Construct and return the story HTML
    $story_html = '
    <div class="story" data-story-id="'.$story->id.'">
        <div class="border-2 border-solid '.$color_class.' rounded-lg transition-all px-4 py-2 flex justify-between items-center">
            <a onclick="init_task_modal('.$story->id.');" href="#" class="text-gray-800 font-bold flex flex-col "> <div>'.htmlspecialchars($story->name). '</div>'.$epic_html.'</a>
            <div class="flex items-center">
                <div class="flex space-x-2">
                    '.$assignee_avatars.'
                </div>
                <div class="ml-4 text-gray-800">'.$time_indicator_text.'</div>
            </div>
        </div>
    </div>';

    return $story_html;
}

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
                        
                        <button class="px-4 py-1 rounded <?= $sprint->status == 2 ? 'bg-green-500 text-white' : 'bg-white border border-solid border-gray-200 text-gray-800' ?> transition-all hover:shadow-lg shadow-md hover:scale-105 disabled:hidden" <?= ($sprint->status == 2) ? 'disabled' : '' ?>  onclick="updateSprintStatus(this);" data-status="<?= $sprint->status ?>" data-sprint-id="<?= $sprint->id ?>">
                            <?php 
                                if($sprint->status == 0){
                                    echo "Start";
                                }else if($sprint->status == 1){
                                    echo "Complete";
                                }else {
                                    echo "Completed";
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
    
    function handleStoryMove(evt){

        var story_id = evt.item.getAttribute('data-story-id');
        var new_issue_id = evt.to.getAttribute('data-issue-id');
        var new_issue_type = evt.to.getAttribute('data-issue-type');
        

        updateMaxHeight(evt.to.parentElement, true);

        Swal.fire({
            title: 'Processing',
            html: 'Moving story...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
                
                $.ajax({
                    url: admin_url + 'projects/move_story',
                    type: 'POST',
                    data: {
                        story_id: story_id,
                        new_issue_id: new_issue_id,
                        new_issue_type: new_issue_type,
                    },
                    success: function(response) {
                        Swal.close();
                        response = JSON.parse(response);
                        if (response.success) {
                            Swal.fire('Success!', 'Story moved successfully.', 'success');
                        } else {
                            Swal.fire('Error!', 'Failed to move story.', 'error');
                        }
                    },
                    error: function() {
                        Swal.close();
                        Swal.fire('Error!', 'Failed to move story.', 'error');
                    }
                });
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

                <div class="flex items-center text-black font-bold text-base gap-2">
                    <div class="fas fa-angle-down transform transition-transform duration-300"></div>
                    <span class="">${epicName}</span>
                </div>

                <button class="bg-green-500 text-white rounded-lg px-4 py-2 transition-all hover:bg-green-600 ease-in-out duration-300" onclick="newStory(${epicId})">New Story</button>
            </div>

            <div class="collapsible-content expanded"  data-issue-type="epic" data-issue-id="${epicId}" id="epic-${epicId}-content">
                <div class="p-4 flex flex-col gap-4 epic-list" id="epic-${epicId}-list">
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
    new_task_from_relation(undefined, 'project', <?= $project->id ?>);

    // Wait for the modal HTML to be loaded, then add the select and hidden fields
    waitForElement('#task-form', function() {

        var selectField = `
            <div class="form-group mt-4" app-field-wrapper="epic_id">
                <label for="epic_id" class="control-label">Epic</label>
                <select name="epic_id" id="epic_id" class="form-control">
                    ${epics.map(epic => `<option value="${epic.id}" ${epic.id == epic_id ? 'selected' : ''}>${epic.name}</option>`).join('')}
                </select>
            </div>
        `;


        // Assuming the Subject field is wrapped in a div with class 'form-group'
        var subjectFieldGroup = $('.form-group #name').closest('.form-group');
        subjectFieldGroup.before(selectField);  // Insert the select field before the Subject field
        $('.project-details').hide();
    });  
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

</script>