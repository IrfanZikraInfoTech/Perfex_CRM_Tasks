<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php
    $templateData = json_decode($template->epics_and_stories, true);
    $epic_count = count($templateData);
    $story_count = 0;
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
<div id="wrapper">
    <div class="content">

        <div class="bg-white p-4 flex flex-col">
            <div class="flex flex-row pb-4 justify-between">
                <h2 class="text-lg font-bold"><?= $template->name ?> Template</h2>

                <div class="flex flex-row gap-2">
                    
                    <button onclick="window.location.href='<?= admin_url('projects/templates') ?>'" class="bg-gray-500 text-white rounded-lg px-4 py-2 transition-all hover:bg-gray-600 ease-in-out duration-300">Back</button>

                    <button class="bg-green-500 text-white rounded-lg px-4 py-2 transition-all hover:bg-green-600 ease-in-out duration-300" onclick="saveProject();">Save</button>
                </div>
            </div>
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
                        <?php foreach ($templateData as $epicIndex => $epic): 
                            $epicIndex++;
                            ?>
                            <div class="border-2 border-solid border-purple-100 rounded-lg transition-all hover:shadow-lg ease-in-out duration-300 epicDiv" data-epic-id="<?= htmlspecialchars($epicIndex) ?>" id="epic-<?= htmlspecialchars($epicIndex) ?>">
                                <div class="flex justify-between items-center py-2 px-4 bg-purple-100/40 rounded-t-lg border-solid border-purple-200 cursor-pointer" onclick="toggleCollapse(event, 'epic-<?= htmlspecialchars($epicIndex) ?>-content')">
                                    <div class="flex items-center text-black font-bold text-base gap-2 w-1/3">
                                        <div class="fas fa-angle-down transform transition-transform duration-300"></div>
                                        <input class="w-full text-black font-bold text-base bg-transparent px-2 py-1" value="<?= htmlspecialchars($epic['name']) ?>" placeholder="Epic Name" />
                                    </div>
                                    <div class="flex flex-row gap-2">
                                        <button class="bg-green-500 text-white rounded-lg px-4 py-2 transition-all hover:bg-green-600 ease-in-out duration-300" onclick="showStoryModal(<?= htmlspecialchars($epicIndex) ?>, null)">New Story</button>
                                        <button class="bg-rose-500 text-white rounded-lg px-4 py-2 transition-all hover:bg-rose-600 ease-in-out duration-300" onclick="deleteComponent('Epic', 'epic-<?= htmlspecialchars($epicIndex) ?>');">Delete</button>
                                    </div>
                                </div>
                                <div class="collapsible-content expanded" id="epic-<?= htmlspecialchars($epicIndex) ?>-content">
                                    <div class="p-4 flex flex-col gap-4 epic-list" data-issue-type="epic" data-issue-id="<?= htmlspecialchars($epicIndex) ?>" id="epic-<?= htmlspecialchars($epicIndex) ?>-list">
                                    
                                    <?php foreach ($epic['stories'] as $story): 
                                        
                                        $story_count ++;
                                        $storyIndex = $story_count;
                                        
                                        ?>
                                        <div class="storyDiv" data-story-name="<?= htmlspecialchars($story['name']) ?>" data-story-estimatedHours="<?= htmlspecialchars($story['estimatedHours']) ?>" data-story-description="<?= htmlspecialchars($story['description']) ?>" data-story-checklistItems="<?= htmlspecialchars(json_encode($story['checklistItems'])) ?>" data-story-id="<?= htmlspecialchars($storyIndex) ?>" id="story-<?= htmlspecialchars($storyIndex) ?>">
                                            <div class="border-2 border-solid border-green-200 rounded-lg hover:border-green-500 transition-all px-4 py-2 flex justify-between items-center">
                                                <a id="story-<?= htmlspecialchars($storyIndex) ?>-button" data-epic-id="<?= htmlspecialchars($epicIndex) ?>" href="#" class="text-gray-800 font-bold" onclick="showStoryModal(null, <?= htmlspecialchars($storyIndex) ?>)">
                                                    <?= htmlspecialchars($story['name']) ?>
                                                </a>
                                                <div class="flex items-center">
                                                    <button class="bg-rose-500 text-white rounded-lg px-4 py-2 transition-all hover:bg-rose-600 ease-in-out duration-300" onclick="deleteComponent('Story', 'story-<?= htmlspecialchars($storyIndex) ?>')">Delete</button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

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
</div>

<div class="modal fade" id="storyModal" tabindex="-1" role="dialog" aria-labelledby="storyModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content bg-white rounded-lg shadow-xl">
      <div class="modal-header bg-gray-200 p-4 flex justify-between items-center">
        <div></div> <!-- Empty div for flex justification -->
        <h5 class="modal-title text-2xl font-semibold text-gray-700 mx-auto" id="storyModalLabel">New Story</h5>
        <button type="button" class="close text-gray-600 hover:text-gray-800 text-2xl" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="hover:text-red-500">&times;</span>
        </button>
      </div>
      <div class="modal-body p-6 text-lg">
        <!-- Story details input fields -->
        <input type="hidden" id="storyEpicId" value="">
        <input type="hidden" id="storyId" value="">
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="storyNameInput">
            Story Name
          </label>
          <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="storyNameInput" type="text" placeholder="Enter story name">
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="estimatedHoursInput">
            Estimated Hours
          </label>
          <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="estimatedHoursInput" type="number" placeholder="Enter estimated hours">
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2" for="descriptionInput">
            Description
          </label>
          <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="descriptionInput" placeholder="Enter description"></textarea>
        </div>
        <div class="mb-4">
        
            <div class="flex flex-row h-10 items-center">
                <label class="w-full block text-gray-700 text-sm font-bold" for="checklistItemsInput">
                    Checklist Item (comma separated)
                </label>
                <button class="px-4 py-2 flex justify-center items-center bg-green-500 text-white rounded-lg transition-all hover:bg-green-600 ease-in-out duration-300 font-xl" onclick="addChecklistItem()">+</button>
            </div>
          
            <div class="mt-5 flex flex-col gap-2" id="checklist-items">


            </div>
            
        </div>
      </div>
      <div class="modal-footer p-4">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="saveStory()">Save</button>
      </div>
    </div>
  </div>
</div>


<?php init_tail(); ?>

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


function refreshSortable(){
    const epicLists = document.querySelectorAll('.epic-list');
    epicLists.forEach(epicList => {
        new Sortable(epicList, {
            group: 'shared', // Set a group name for the sortable lists to allow dragging between them
            animation: 150,
            onEnd(evt) {
                refreshMaxHeights();
            }
        });
    });
}
refreshSortable();
let epicIdCounter = <?= $epic_count + 1 ?>;

function createEpic() {
    const epicName = document.getElementById('epicNameInput').value;
    if (epicName === '') {
        alert('Please enter an epic name.');
        return;
    }

    const epic = {
        id: epicIdCounter,
        name: epicName
    };

    renderEpic(epic);

    epicIdCounter++;
    document.getElementById('epicNameInput').value = '';
    updateMaxHeight(document.getElementById("backlog"), true);

    refreshSortable();
}

function renderEpic(epic) {
    const epicContainer = document.getElementById('epicContainer');
    const epicHtml = `
        <div class="border-2 border-solid border-purple-100 rounded-lg transition-all hover:shadow-lg ease-in-out duration-300 epicDiv" data-epic-id="${epic.id}" id="epic-${epic.id}">
            <div class="flex justify-between items-center py-2 px-4 bg-purple-100/40 rounded-t-lg border-solid border-purple-200 cursor-pointer" onclick="toggleCollapse(event, 'epic-${epic.id}-content')">
                <div class="flex items-center text-black font-bold text-base gap-2 w-1/3">
                    <div class="fas fa-angle-down transform transition-transform duration-300"></div>
                    <input class="w-full text-black font-bold text-base bg-transparent px-2 py-1" value="${epic.name}" placeholder="Epic Name" />
                </div>
                <div class="flex flex-row gap-2">
                    <button class="bg-green-500 text-white rounded-lg px-4 py-2 transition-all hover:bg-green-600 ease-in-out duration-300" onclick="showStoryModal(${epic.id}, null)">New Story</button>
                    <button class="bg-rose-500 text-white rounded-lg px-4 py-2 transition-all hover:bg-rose-600 ease-in-out duration-300" onclick="deleteComponent('Epic', 'epic-${epic.id}');">Delete</button>
                </div>
            </div>
            <div class="collapsible-content expanded" id="epic-${epic.id}-content">
                <div class="p-4 flex flex-col gap-4 epic-list" data-issue-type="epic" data-issue-id="${epic.id}" id="epic-${epic.id}-list">
                </div>
            </div>
        </div>`;
    epicContainer.insertAdjacentHTML('beforeend', epicHtml);
}

let storyIdCounter = <?= $story_count + 1 ?>;

function showStoryModal(epicId = null, storyId = null) {

    if (storyId) {

        var storyElement = document.getElementById("story-"+storyId);
        let id = storyElement.getAttribute("data-story-id");
        let name = storyElement.getAttribute("data-story-name");
        let estimatedHours = storyElement.getAttribute("data-story-estimatedHours");
        let description = storyElement.getAttribute("data-story-description");

        let checklistItems = storyElement.getAttribute("data-story-checklistItems");
        let retrievedChecklistArray = JSON.parse(checklistItems);

        retrievedChecklistArray.forEach(element => {
            addChecklistItem(element);
        });


        document.getElementById('storyId').value = id;
        document.getElementById('storyNameInput').value = name;
        document.getElementById('estimatedHoursInput').value = estimatedHours;

        //document.getElementById('descriptionInput').value = description;
        tinymce.get('descriptionInput').setContent(description);

        //document.getElementById('checklist-items').innerHTML = checklistItems;
        document.getElementById('storyModalLabel').textContent = 'Edit Story';
    } else {
        // This is a new story, clear the input fields
        document.getElementById('storyId').value = '';
        
        document.getElementById('storyEpicId').value = epicId;
        document.getElementById('storyNameInput').value = '';
        document.getElementById('estimatedHoursInput').value = '';
        //document.getElementById('descriptionInput').value = '';
        tinymce.get('descriptionInput').setContent("");
        document.getElementById('checklist-items').innerHTML = '';
        document.getElementById('storyModalLabel').textContent = 'New Story';
    }

    

    $('#storyModal').modal('show');
}

function saveStory() {

    const storyId = parseInt(document.getElementById('storyId').value);

    const epicId = parseInt(document.getElementById('storyEpicId').value);

    const storyName = document.getElementById('storyNameInput').value;
    const estimatedHours = parseFloat(document.getElementById('estimatedHoursInput').value);
    const description =  tinymce.get('descriptionInput').getContent();

    
    let checklistArray = [];
    document.querySelectorAll('.checklist-item').forEach(item => {
        // Query select the input field within the item
        let input = item.querySelector('input');
        
        // Add the input value (i.e., the checklist item name) to the array
        if (input && input.value) {
            checklistArray.push(input.value);
        }
    });



    const checklistItems = JSON.stringify(checklistArray);

    if (storyId) {
        // This is an edit operation, update the existing story
        const storyElement = document.getElementById("story-"+storyId);
        const story={
            id: storyElement.getAttribute("data-story-id"),
            name: storyName,
            estimatedHours: estimatedHours,
            description: description,
            checklistItems: checklistItems
        };
        renderStory(story);
    } else {
        // This is a new story, add it to the epic
        const newStory = {
            id: storyIdCounter,
            name: storyName,
            estimatedHours: estimatedHours,
            description: description,
            checklistItems: checklistItems
        };
        storyIdCounter ++;
        renderStory(newStory, epicId);
    }

    
    $('#storyModal').modal('hide');
    refreshMaxHeights();
}

function renderStory(story, epicId = null) {

    const storyHtml = `
        <div class="story" data-story-id="${story.id}">
            <div class="border-2 border-solid border-green-200 rounded-lg hover:border-green-500 transition-all px-4 py-2 flex justify-between items-center">
                <a id="story-${story.id}-button" data-epic-id="${epicId}" href="#" class="text-gray-800 font-bold" onclick="showStoryModal(null, ${story.id})">
                    ${story.name}
                </a>
                <div class="flex items-center">
                    <button class="bg-rose-500 text-white rounded-lg px-4 py-2 transition-all hover:bg-rose-600 ease-in-out duration-300" onclick="deleteComponent('Story', 'story-${story.id}')">Delete</button>
                </div>
            </div>
        </div>
    `;

    
    
    if (!epicId) {
        let storyDiv = document.getElementById(`story-${story.id}`);

        storyDiv.setAttribute("data-story-name", story.name);
        storyDiv.setAttribute("data-story-estimatedHours", story.estimatedHours);
        storyDiv.setAttribute("data-story-description", story.description);
        storyDiv.setAttribute("data-story-checklistItems", story.checklistItems);

        storyDiv.innerHTML = storyHtml;

    } else {
        const epicList = document.querySelector(`#epic-${epicId}-list`);

        // If the story div does not exist, create a new div and append it to the epic list
        storyDiv = document.createElement('div');
        storyDiv.setAttribute("id", "story-"+story.id);
        storyDiv.setAttribute("class", "storyDiv");
        storyDiv.setAttribute("data-story-id", story.id);
        storyDiv.setAttribute("data-story-name", story.name);
        storyDiv.setAttribute("data-story-estimatedHours", story.estimatedHours);
        storyDiv.setAttribute("data-story-description", story.description);
        storyDiv.setAttribute("data-story-checklistItems", story.checklistItems);
        storyDiv.innerHTML = storyHtml;
        epicList.appendChild(storyDiv);
    }
    
}

function deleteComponent(type, id){
    Swal.fire({
            title: 'Are you sure?',
            text: "You are about to delete "+type,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(id).remove();
                Swal.fire('Success!', 'Deleted Successfully.', 'success');
            }
        });
}

function addChecklistItem(item = '') {
    const checklistContainer = document.getElementById('checklist-items');

    // Create the div element
    const divElement = document.createElement('div');
    divElement.className = 'flex flex-row gap-2 checklist-item';

    // Create the input element
    const inputElement = document.createElement('input');
    inputElement.className = 'checklist-item shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline';
    inputElement.id = 'checklistItemsInput';
    inputElement.type = 'text';
    inputElement.placeholder = 'Enter checklist items';
    inputElement.value = item;  // Set the value property instead of embedding in HTML

    // Create the button element
    const buttonElement = document.createElement('button');
    buttonElement.className = 'px-4 flex justify-center items-center bg-rose-500 text-white rounded-lg transition-all hover:bg-rose-600 ease-in-out duration-300';
    buttonElement.innerHTML = '<span>&times;</span>';
    buttonElement.onclick = function() {
        this.parentNode.remove();
    };

    // Append the input and button elements to the div element
    divElement.appendChild(inputElement);
    divElement.appendChild(buttonElement);

    // Append the div element to the checklist container
    checklistContainer.appendChild(divElement);
}



function saveProject() {
    Swal.fire({
        title: 'Save Template',
        input: 'text',
        inputPlaceholder: 'Template Name',
        inputValue: '<?= htmlspecialchars($template->name); ?>',
        showCancelButton: true,
        confirmButtonText: 'Save',
        preConfirm: (templateName) => {
            if (!templateName) {
                Swal.showValidationMessage('Template name is required');
                return false;
            }
            const templateData = generateTemplateJson();

            $.ajax({
                url: admin_url + 'projects/save_template',
                type: 'POST',
                data: {
                    template_id: <?= $template->id ?>,
                    template_name: templateName,
                    template_data: templateData
                },
                success: function(response) {
                    Swal.close();
                    response = JSON.parse(response);
                    if (response.success) {

                        Swal.fire({
                            title: 'Success',
                            text: "Template edited successfully!",
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            cancelButtonText: 'Go Back',
                            confirmButtonText: 'Edit More'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                
                            }else{
                                window.location.href = admin_url + "projects/templates";
                            }
                        });
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
    });
}



function generateTemplateJson() {
    // Initialize an empty array to hold the epic data
    const epics = [];

    // Find all epic divs
    const epicDivs = document.querySelectorAll('.epicDiv');

    // Loop through each epic div
    epicDivs.forEach(epicDiv => {
        // Get the epic name from the nearest input element
        const epicName = epicDiv.querySelector('input').value;
        const epicId = epicDiv.getAttribute('data-epic-id');

        // Initialize an empty array to hold the stories for this epic
        const stories = [];

        // Find all story divs within the current epic div's .collapsible-content
        const storyDivs = epicDiv.querySelector('.collapsible-content').querySelectorAll('.storyDiv');

        // Loop through each story div
        storyDivs.forEach(storyDiv => {
            // Get the story data from data attributes
            const storyName = storyDiv.getAttribute('data-story-name');
            const estimatedHours = storyDiv.getAttribute('data-story-estimatedHours');
            const description = storyDiv.getAttribute('data-story-description');

            const checklistItems = JSON.parse(storyDiv.getAttribute('data-story-checklistItems'));

            // Create a story object and add it to the stories array
            stories.push({
                name: storyName,
                estimatedHours: estimatedHours,
                description: description,
                checklistItems: checklistItems
            });
        });

        // Create an epic object and add it to the epics array
        epics.push({
            name: epicName,
            stories: stories
        });
    });


    // Convert the template object to a JSON string
    const templateJson = JSON.stringify(epics, null, 2);

    // Output the JSON string to the console (or you could return it from the function)
    return templateJson;
}

function refreshMaxHeights(){
    document.querySelectorAll('.collapsible-content').forEach(function(element) {
        updateMaxHeight(element, true);
    });

    setTimeout(() => {
        updateMaxHeight(document.querySelector('#backlog'), true);
    }, 500);
}


$('#storyModal').on('hidden.bs.modal', function () {
  $('#checklist-items').html("");
})

tinymce.init({
  selector: '#descriptionInput',
  height: '300px'
});

</script>

</body>
</html>