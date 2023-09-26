<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    button[disabled]{
        background: #999;
    }
</style>

<div class="wrapper" id="wrapper" style="padding:30px;" >

    <div class="container mx-auto px-4 py-5">
        <h1 class="text-3xl font-bold mb-5">Project Board</h1>
        <div class="grid-project-board grid gap-5 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <?php foreach ($projects as $project) : ?>
                <div class="bg-white shadow-md rounded">
                    
                    <div class="project-heading rounded-t-lg p-4 bg-gray-200 border-b border-gray-300 flex justify-between items-center">
                    
                        <div class="flex flex-col">
                            <h2 class="text-xl font-semibold"><?= $project['name'] ?></h2>
                            <h3 class="text-sm font-semibold text-gray-500 hidden"><?= $project['completed_tasks'] ?>/<?= $project['total_tasks'] ?></h3>
                        </div>
                
                        <button data-project-id="<?= $project['id'] ?>" class="bg-blue-500 text-white rounded-full p-1 px-2">
                            <i class="fas fa-plus"></i>
                        </button>

                    </div>
                    <div class="p-2 space-y-2">

                        <!-- Existing project box code -->
                        <?php foreach ($project['dummy_tasks'] as $task) : if($task['task_id'] == null){  ?>
                            <div class="rounded-lg hover:bg-gray-200/50 transition-all p-2 flex justify-between items-center" id="dummy-<?= $task['id'] ?>">
                                <a href="#"><?= $task['name'] ?></a>
                                <div class="flex flex-row gap-1">
                                <button onclick="new_task(`<?= admin_url('tasks/task?rel_id=' . $project['id'] . '&rel_type=project')?>`);setupTaskCreate(`<?= $task['name'] ?>`, `<?= $task['id'] ?>`)" class="assign-staff-btn bg-green-500 text-white rounded-full py-1 px-[6px]">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                                <button onclick="delete_dummy(`<?= $task['id'] ?>`);" class="assign-staff-btn bg-rose-500 text-white rounded-full py-1 px-[10px]">
                                    <i class="fas fa-multiply"></i>
                                </button>
                                
                                </div>
                            </div>
                        <?php } endforeach; ?>

                        


                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="container mx-auto px-4 py-5">
        <h1 class="text-3xl font-bold mb-5">Tasks Board</h1>
        <div class="grid gap-4 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

        <?php foreach ($staff_members as $staff): ?>

        <div class="w-full transition-all bg-white hover:shadow-md rounded-lg shadow-md overflow-hidden p-4">
            <div class="flex flex-col h-full">

                <div class="flex-shrink-0 flex justify-center max-w-fit mx-auto mb-4">
                    <?php echo staff_profile_image($staff->staffid, ['h-32 rounded-full border-4 border-solid border-gray-300', 'w-full', 'object-cover', 'md:h-full' , 'md:w-32' , 'staff-profile-image-thumb'], 'thumb'); ?>
                </div>
                <div class="flex flex-col mt-4 gap-5 h-full">
                    
                    <div class="uppercase tracking-wide text-xl text-center text-gray-700 font-bold"><?php echo $staff->firstname ?></div>
                
                    <div class="text-gray-500 text-center flex flex-col gap-5" id="user-<?= $staff->staffid?>-task-list">
                        <?php
                            $today = date('Y-m-d');
                            $tasks = $this->team_management_model->get_tasks_by_staff_member($staff->staffid);
                            $total_tasks = 0;
                            $pending_tasks = 0;
                            $due_tasks = 0;
                            $completed_tasks = 0;

                            // Split tasks into sections
                            $completed_tasks_section = '';
                            $pending_tasks_section = '';
                            $due_tasks_section = '';

                            foreach ($tasks as $task) {
                                
                                $taskDueConsideration = ($task->duedate) ? $task->duedate : $task->startdate;

                                if (
                                    (strtotime($task->startdate) <= strtotime($today) && strtotime($taskDueConsideration) >= strtotime($today) && $task->status != 5)
                                    ||
                                    (strtotime($task->startdate) <= strtotime($today) && $task->status != 5 && strtotime($taskDueConsideration) < strtotime($today))
                                ) {
                                    
                                    $total_tasks++;
                                    if ($task->status == 5) {
                                        $completed_tasks++;
                                        $taskBG = 'bg-green-100';
                                        $compDisabled = 'disabled';
                                        $section = 'completed_tasks_section';
                                    } else {
                                        if(strtotime($taskDueConsideration) < strtotime($today) && $task->status != 5){
                                            $taskBG = 'bg-rose-100';
                                            $section = 'due_tasks_section';
                                            $due_tasks ++;
                                        } else{
                                            $taskBG= 'bg-gray-100';
                                            $section = 'pending_tasks_section';
                                            $pending_tasks ++;
                                        }
                                        $compDisabled = '';
                                    }

                                    $assignees = explode(',', $task->assignees);

                                    if($section == 'due_tasks_section'){
                                        $pendingReference = ($task->duedate) ? $task->duedate : $task->startdate;
                                        $pendingSeconds = time() - strtotime($pendingReference);
                                        $pendingDays = floor($pendingSeconds / (60 * 60 * 24)). ' d';

                                    }else {$pendingDays = '';}

                                    ${$section} .= '
                        <div class="flex justify-between gap-4 items-center px-4 py-2 rounded '.$taskBG.'">
                            <div class="text-left w-[10%] text-sm">
                                <a onclick="init_task_modal(' . $task->id . '); return false" href="#" class="text-left">' . $task->id . '</a>
                            </div>
                            <div class="w-[50%] text-sm text-left">
                                <a onclick="init_task_modal(' . $task->id . '); return false" href="#" class="text-left">' . $task->name . '</a>
                            </div>
                            <div class="w-[20%] text-sm text-left">
                                <a onclick="init_task_modal(' . $task->id . '); return false" href="#" class="text-left">' . $pendingDays . '</a>
                            </div>';
                                        
                        if (in_array($this->session->userdata('staff_user_id'), $assignees)) {
                            ${$section} .= '
                            <div class="w-[20%]">
                                <button '.$compDisabled.' onclick="mark_complete(' . $task->id . '); this.parentElement.parentElement.classList.remove(`bg-rose-100`);this.parentElement.parentElement.classList.add(`bg-green-100`); this.disabled = true;updateTaskValues(`' . $staff->staffid . '`,false,true); return false;" class="bg-green-500 text-white rounded-full px-2 py-1"><i class="fas fa-check"></i></button>
                            </div>';
                        }
                        ${$section} .= '</div>';

                                }
                            }

                            $completed_tasks_section = ($completed_tasks_section == '') ? '<h2>////////</h2>' : $completed_tasks_section;

                            $pending_tasks_section = ($pending_tasks_section == '') ? '<h2>////////</h2>' : $pending_tasks_section;

                            $due_tasks_section = ($due_tasks_section == '') ? '<h2>////////</h2>' : $due_tasks_section;

                            
                            // Display task sections
                            echo '<div class="bg-white rounded-lg overflow-hidden shadow-md w-full">
                            <button type="button" data-toggle="collapse" data-target="#collapseCompleted'.$staff->staffid.'" aria-controls="collapseCompleted'.$staff->staffid.'" class="w-full  rounded-none bg-gray-200 text-lg font-bold p-2">Completed Tasks # '.$completed_tasks.'</button><div aria-expanded="false" class="collapse out" id="collapseCompleted'.$staff->staffid.'"><div class="p-2 flex flex-col gap-2">' . $completed_tasks_section . '</div></div>
                            </div>';

                            echo '<div class="bg-white rounded-lg overflow-hidden shadow-md w-full">
                            <button type="button" data-toggle="collapse" data-target="#collapsePending'.$staff->staffid.'" aria-controls="collapsePending'.$staff->staffid.'" class="w-full  rounded-none bg-gray-200 text-lg font-bold p-2">Pending Tasks # <span id="user-'.$staff->staffid.'-pending-counter">'.$pending_tasks.'</span></button><div aria-expanded="false" class="collapse out" id="collapsePending'.$staff->staffid.'"><div class="p-2 flex flex-col gap-2">' . $pending_tasks_section . '</div></div>
                            </div>';

                            echo '<div class="bg-white rounded-lg overflow-hidden shadow-md w-full">
                            <button type="button" data-toggle="collapse" data-target="#collapseDue'.$staff->staffid.'" aria-controls="collapseDue'.$staff->staffid.'" class="w-full  rounded-none bg-gray-200 text-lg font-bold p-2">Due Tasks # '.$due_tasks.'</button><div aria-expanded="false" class="collapse out" id="collapseDue'.$staff->staffid.'"><div class="p-2 flex flex-col gap-2">' . $due_tasks_section . '</div></div>
                            </div>';

                            $percentage = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100) : 0;
                        ?>
                    </div>


                    <div class="mt-auto flex justify-between items-center text-base">
                    <div class="text-gray-700 font-semibold">Rate:</div>
                    <div class="text-gray-700 font-semibold">
                        <span id="user-<?= $staff->staffid?>-rate-p"><?= $percentage ?></span>%
                        (<span id="user-<?= $staff->staffid?>-rate-c"><?= $completed_tasks ?></span>/<span id="user-<?= $staff->staffid?>-rate-a"><?= $total_tasks ?></span>)
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php endforeach; ?>

    </div>


</div>

<div class="modal fade" id="addDummyTaskModal" tabindex="-1" role="dialog" aria-labelledby="addDummyTaskModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="p-5 border-b border-gray-300 border-solid flex flex-row justify-between">
        <h5 class="modal-title" id="addDummyTaskModalLabel">Add Dummy Task</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addDummyTaskForm">
          <input type="hidden" id="projectId" name="project_id">
          <div class="form-group">
            <label for="dummyTaskName">Dummy Task Name</label>
            <input type="text" class="form-control" id="dummyTaskName" name="dummy_task_name" placeholder="Enter dummy task name">
          </div>
          <button type="submit" id="addDummyTask" class="btn btn-primary">Add</button>
        </form>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="assignTaskModal" tabindex="-1" role="dialog" aria-labelledby="assignTaskModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="assignTaskModalLabel">Assign Task</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="assignTaskModalBody">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<?php init_tail(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script>

window.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelectorAll('.grid-project-board > div');
    let rowIndex = 0;
    let maxHeight = 0;

    rows.forEach((row, index) => {
        const heading = row.querySelector('.project-heading');
        maxHeight = Math.max(maxHeight, heading.offsetHeight);

        if ((index + 1) % 5 === 0 || index === rows.length - 1) {
            for (let i = rowIndex; i <= index; i++) {
                rows[i].querySelector('.project-heading').style.height = `${maxHeight}px`;
            }
            rowIndex = index + 1;
            maxHeight = 0;
        }
    });
});

//Open Dummy Task Modal
document.querySelectorAll('.project-heading button').forEach((button, index) => {
    button.addEventListener('click', () => {
        const projectId = button.getAttribute('data-project-id'); 
        document.getElementById('projectId').value = projectId;
        $('#addDummyTaskModal').modal('show');
    });
});

//Add dummy task
document.getElementById('addDummyTask').addEventListener('click', function (e) {
    e.preventDefault();

    const projectId = document.getElementById('projectId').value;
    const taskName = document.getElementById('dummyTaskName').value;
    const taskContainer = document.querySelector(`[data-project-id="${projectId}"]`).parentElement.nextElementSibling;

    var csrfName = csrfData.token_name;
    var csrfHash = csrfData.hash;
    $.ajax({
        type: 'POST',
        url: 'save_dummy_task', // Replace with the actual URL for adding a dummy task
        dataType: 'json',
        data: {
            project_id: projectId,
            name: taskName,
            csrfName , csrfHash
        },
        success: function (response) {
            const taskId = response.task_id; // Get the new task ID from the response
            const taskHtml = `
            <div class="rounded-lg p-2 flex justify-between items-center" id="dummy-${taskId}">
                <a href="#">${taskName}</a>
                <div class="flex flex-row gap-1">
                    <button onclick="new_task('<?= admin_url('tasks/task?rel_id=${projectId}&rel_type=project')?>');setupTaskCreate('${taskName}', '${taskId}');" class="assign-staff-btn bg-green-500 text-white rounded-full py-1 px-[6px]">
                        <i class="fas fa-user-plus"></i>
                    </button>
                    <button onclick="delete_dummy('${taskId}');" class="assign-staff-btn bg-rose-500 text-white rounded-full py-1 px-[10px]">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;


            taskContainer.insertAdjacentHTML('beforeend', taskHtml);

            const element = document.getElementById(`dummy-${taskId}`);
            // Assuming 'element' is the new div you're adding
            anime({
                targets: element,
                translateY: [-100, 0], // Change translateY values according to your requirement
                opacity: [0, 1],
                duration: 1000,
                easing: 'easeOutExpo'
            });


            //$('#addDummyTaskModal').modal('hide');
            alert_float("success", "Dummy task added successfully");
        },
        error: function () {
            // Handle any errors that occur during the AJAX request
            alert('An error occurred while adding the dummy task. Please try again.');
        }
    });
});

var dummyTaskId = null;


function setupTaskCreate(name, dummy_task_id){
    const today = new Date();
    const formattedToday = today.toISOString().slice(0, 10);

    setTimeout(() => {

        $("#name").val(name);
        $('#task_visible_to_client').prop('checked', false);
        $('#duedate').val(formattedToday);
        $('#assignees').val(null);
        $('#assignees').selectpicker('refresh');

        dummyTaskId = dummy_task_id;

    }, 1500);
    
}

$(document).ajaxSuccess(function(event, xhr, settings) {
  // This function will be called for all successful AJAX requests
  // Check if the request is the one you're interested in by comparing the URL or other settings properties
  if (settings.url === admin_url + 'tasks/task') {
    // The response data can be accessed from xhr.responseJSON or xhr.responseText
    var responseData = xhr.responseJSON || JSON.parse(xhr.responseText);
    
    if(dummyTaskId != null){
        console.log(responseData.id + " :: " + dummyTaskId);
        assignTaskToDummyTask(responseData.id, dummyTaskId);     
    }
    
  }
});


function assignTaskToDummyTask(taskId, dummyTaskId) {

    var csrfName = csrfData.token_name;
    var csrfHash = csrfData.hash;

    $.ajax({
        url: 'assign_task_to_dummy_task', // Replace with the actual URL for the API endpoint
        method: 'POST',
        dataType: 'json',
        data: {
        task_id: taskId,
        dummy_task_id: dummyTaskId,
        csrfName , csrfHash
        },
        success: function(response) {
        // Check if the assignment was successful (based on response)
        if (response.success) {

            // Remove the dummy task div using its ID
            const dummyTaskDiv = document.querySelector(`#dummy-${dummyTaskId}`);
            if (dummyTaskDiv) {
            dummyTaskDiv.remove();
            }

            if(response.task_details){

                console.log(response.task_details);
                const taskDiv = createTaskDiv(response.task_details);

                const pendingTasksSection = document.querySelector(`#user-${response.task_details.assigned_user}-task-list #collapsePending${response.task_details.assigned_user} .p-2`);
                if (pendingTasksSection) {
                    pendingTasksSection.appendChild(taskDiv);
                }

                let currentPendingTasks = parseInt($("#user-"+response.task_details.assigned_user+"-pending-counter").html());

                $("#user-"+response.task_details.assigned_user+"-pending-counter").html(parseInt(currentPendingTasks + 1));
                

                updateTaskValues(response.task_details.assigned_user, true, false);

            }
            

        } else {
            // Handle the error
            console.error('Failed to assign task ID to dummy task ID:', response.message);
        }
        },
        error: function(xhr, status, error) {
        console.error('AJAX request failed:', error);
        }
    });
}

const current_staff_id = <?php echo $this->session->userdata('staff_user_id'); ?>;

function createTaskDiv(task) {
    const taskDiv = document.createElement('div');
    const taskBG = task.status == 5 ? 'bg-green-100' : 'bg-gray-100';
    taskDiv.className = `flex justify-between gap-4 items-center px-4 py-2 ${taskBG}`;

    // Task ID div
    const taskIdDiv = document.createElement('div');
    taskIdDiv.className = 'text-left w-[10%] text-sm';
    const taskIdLink = document.createElement('a');
    taskIdLink.href = '#';
    taskIdLink.textContent = task.task_id;
    taskIdLink.onclick = function () {
        init_task_modal(task.task_id);
        return false;
    };
    taskIdDiv.appendChild(taskIdLink);

    // Task Name div
    const taskNameDiv = document.createElement('div');
    taskNameDiv.className = 'w-[50%] text-sm text-left';
    const taskNameLink = document.createElement('a');
    taskNameLink.href = '#';
    taskNameLink.textContent = task.task_name;
    taskNameLink.onclick = function () {
        init_task_modal(task.task_id);
        return false;
    };
    taskNameDiv.appendChild(taskNameLink);

    // Append task ID and task Name div to main task div
    taskDiv.appendChild(taskIdDiv);
    taskDiv.appendChild(taskNameDiv);

    // Check if the current user is an assignee
    const assignees = task.assigned_user.split(','); // Assuming assigned_user is a comma-separated string

        const buttonDiv = document.createElement('div');
        buttonDiv.className = 'w-[20%]';
        const button = document.createElement('button');
        button.className = 'bg-green-500 text-white rounded-full px-2 py-1';
        button.disabled = task.status == 5;

        button.onclick = function () {
            mark_complete(task.task_id);
            this.parentElement.parentElement.classList.remove('bg-gray-100');
            this.parentElement.parentElement.classList.add('bg-green-100');
            this.disabled = true;
            updateTaskValues(task.assigned_user, false, true);
            return false;
        };

        if (assignees.includes(String(current_staff_id))) {
        const icon = document.createElement('i');
        icon.className = 'fas fa-check';
        button.appendChild(icon);
        
        buttonDiv.appendChild(button);
        }
        taskDiv.appendChild(buttonDiv);
   


    return taskDiv;
}


function updateTaskValues(staffId, newTask, isCompleted) {
    const totalTasksElem = document.querySelector('#user-' + staffId + '-rate-a');
    const completedTasksElem = document.querySelector('#user-' + staffId + '-rate-c');
    const percentageElem = document.querySelector('#user-' + staffId + '-rate-p');

    const totalTasks = parseInt(totalTasksElem.textContent) + (newTask ? 1 : 0);
    const completedTasks = parseInt(completedTasksElem.textContent) + (isCompleted ? 1 : 0);
    const percentage = totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0;

    totalTasksElem.textContent = totalTasks;
    completedTasksElem.textContent = completedTasks;
    percentageElem.textContent = percentage;
}

function delete_dummy(dummyTaskId) {
  // Get the task element and the task container
  const element = document.getElementById(`dummy-${dummyTaskId}`);
  const taskContainer = element.parentNode;

  // Apply an exit animation to the task
  anime({
    targets: element,
    translateY: [0, -100],
    opacity: [1, 0],
    duration: 1000,
    easing: 'easeOutExpo',
    // Once the animation completes, make the AJAX call to delete the task
    complete: function() {
      $.ajax({
        url: 'delete_dummy_task',
        method: 'POST',
        dataType: 'json',
        data: {
            dummy_task_id: dummyTaskId
        },
        success: function (response) {
          if (response.success) {
            alert_float("success","Dummy task deleted!");
            
            // After confirming the deletion from the server, remove the element from the DOM
            element.remove();

          } else {
            console.error('Failed to delete dummy task:', response.message);
          }
        },
        error: function (error) {
          console.error('Error while deleting dummy task:', error);
        },
      });
    }
  });
}



</script>

</body>
</html>