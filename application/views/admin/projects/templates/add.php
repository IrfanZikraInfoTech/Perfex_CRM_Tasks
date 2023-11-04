<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
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
                <h2 class="text-lg font-bold">New Template</h2>

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

let epicIdCounter = 1;

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

let storyIdCounter = 1;

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
        tinymce.get('descriptionInput').setContent("");
        //document.getElementById('descriptionInput').value = '';
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
    const description = tinymce.get('descriptionInput').getContent();

    
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
                    template_name: templateName,
                    template_data: templateData
                },
                success: function(response) {
                    Swal.close();
                    response = JSON.parse(response);
                    if (response.success) {

                        Swal.fire({
                            title: 'Success',
                            text: "Template created successfully!",
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            cancelButtonText: 'Go Back',
                            confirmButtonText: 'Edit More'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = admin_url + "projects/templates/edit/"+response.template_id;
                                
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
                    Swal.fire('Error!', 'ALL PROGRESS COULD BE LOST, Dont refresh the page and contact @Ahmed Usman.', 'error');
                }
            });
        }
    });
}



function generateTemplateJson() {


//     return JSON.stringify(
// [
//   {
//     "name": "Monthly Planning:",
//     "stories": [
//       {
//         "name": "Monthly Planning Document - for Project Owners",
//         "estimatedHours": "NaN",
//         "description": "<p><span id=\"docs-internal-guid-758396dc-7fff-f4df-559b-e9f05090d57a\"><span style=\"font-size: 11pt; font-family: Poppins, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;\">I want a comprehensive monthly marketing plan that outlines strategies for SEO, content, social media, email marketing, and paid marketing to achieve my business goals.</span></span></p>",
//         "checklistItems": [
//           "Create a document with the objective of the month.",
//           "Define the Last Meeting Minutes with the client.",
//           "Content Packet Ideas for this Month.",
//           "Email Content for the Campaigns.",
//           "Social Media Strategies."
//         ]
//       },
//       {
//         "name": "Outline SEO Strategies - for Imran Khalid (Stakeholder)",
//         "estimatedHours": "NaN",
//         "description": "<p><span id=\"docs-internal-guid-ec86f237-7fff-812b-141b-ba73f367b8ea\"><span style=\"font-size: 11pt; font-family: Poppins, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;\">As an SEO Specialist, I want to outline the SEO strategies for the month, summarizing the key focus areas and planned activities.</span></span></p>",
//         "checklistItems": [
//           "Keyword Strategy: Summarize the main keywords we plan to target for SEO efforts.",
//           "SEO Focus: Highlight the primary SEO objectives for the month, such as improving rankings, increasing organic traffic, or enhancing on-page optimization.",
//           "Responsibilities: Indicate the team members responsible for executing the outlined SEO strategies."
//         ]
//       }
//     ]
//   },
//   {
//     "name": "Content Packet Creation:",
//     "stories": [
//       {
//         "name": "Social Media Content Writing - for Project Owner",
//         "estimatedHours": "NaN",
//         "description": "<p><span id=\"docs-internal-guid-7351ffac-7fff-2454-524b-5d3453c95786\"><span style=\"font-size: 11pt; font-family: Poppins, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;\">As a project owner, I want to craft compelling written content for social media posts to engage the audience and amplify the client's brand effectively.</span></span></p>",
//         "checklistItems": [
//           "Understand the chosen keywords and content themes from the planning phase.",
//           "Develop content outlines for individual social media posts.",
//           "Write engaging and relevant captions, considering the platform-specific tone and style.",
//           "Select and incorporate suitable hashtags based on keyword research and trends.",
//           "Review and refine the content for clarity and brand alignment."
//         ]
//       },
//       {
//         "name": " Internal Review, Revision, Approval, and Client Feedback Integration - for Project Owners",
//         "estimatedHours": "NaN",
//         "description": "<p>&nbsp;</p>\n<p dir=\"ltr\" style=\"line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;\"><span style=\"font-size: 11pt; font-family: Poppins,sans-serif; color: #000000; background-color: transparent; font-weight: 500; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">As a Project Owner, I want to share the marketing plan with both the internal team and the client for review, incorporating feedback from both parties to ensure alignment, comprehensiveness, and approval.</span></p>",
//         "checklistItems": [
//           "Distribute the initial marketing plan draft to relevant team members.",
//           "Schedule and conduct an internal review session.",
//           "Collect and assess feedback from team members.",
//           "Make adjustments to the marketing plan based on team feedback.",
//           "resent the revised plan to key internal stakeholders for approval.",
//           "Document and communicate internal consensus on the plan.",
//           "Share the approved plan with the client, highlighting key strategies and themes.",
//           "Gather feedback and questions from the client.",
//           "Assess and incorporate client feedback into the plan.",
//           "Finalize the marketing plan, ensuring alignment with both team and client perspectives.",
//           "Communicate the finalized plan to both the team and the client."
//         ]
//       },
//       {
//         "name": "Content Calendar Creation - for Project Owners",
//         "estimatedHours": "NaN",
//         "description": "<p>&nbsp;</p>\n<p dir=\"ltr\" style=\"line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;\"><span style=\"font-size: 11pt; font-family: Poppins,sans-serif; color: #000000; background-color: transparent; font-weight: 500; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">As a Project Owner, I want to create a content calendar that outlines the publishing schedule for blog posts, social media content, and email campaigns throughout the month.</span></p>",
//         "checklistItems": [
//           "Based on the marketing plan, create a content calendar that specifies publication dates, content topics, and responsible team members.",
//           "Ensure that the content calendar is synchronized with SEO and social media posting schedules.",
//           "Share the content calendar with the team to ensure alignment and collaboration.",
//           "Include placeholders for content drafts and final approvals."
//         ]
//       }
//     ]
//   },
//   {
//     "name": "Social Media Management:",
//     "stories": [
//       {
//         "name": "Responsive Community Management - for Project Owners",
//         "estimatedHours": "NaN",
//         "description": "<p><span id=\"docs-internal-guid-94d9fe50-7fff-a1af-4e64-374b2875dd64\"><span style=\"font-size: 11pt; font-family: Poppins, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;\">As a Digital Marketer, I need to provide timely and empathetic community management to enhance brand reputation and build a loyal following.</span></span></p>",
//         "checklistItems": [
//           "Regularly monitor all social media inboxes and notifications for direct messages or mentions.",
//           "Respond to queries, comments, and complaints in a tone that aligns with the brand voice.",
//           "Recognize and escalate critical issues or crises to the client immediately.",
//           "Develop and maintain an FAQ document to respond to common queries efficiently.",
//           "Build relationships with key community members, including influencers or brand advocates."
//         ]
//       },
//       {
//         "name": "Cross-Platform Growth Analysis - for Project Owners",
//         "estimatedHours": "NaN",
//         "description": "<p><span id=\"docs-internal-guid-7266bacc-7fff-78cd-8891-f0c998c2aed1\"><span style=\"font-size: 11pt; font-family: Poppins, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;\">As a Digital Marketer, I need to consistently analyze performance metrics across all platforms to steer growth and report ROI to the client.</span></span></p>",
//         "checklistItems": [
//           "Set up and regularly review analytics tools for each platform.",
//           "Identify and report key performance indicators (KPIs) such as growth in followers, engagement rates, and conversion metrics.",
//           "Compile monthly performance reports detailing successes and areas for improvement.",
//           "Discuss analytic insights with the client and provide recommendations for strategic adjustments.",
//           "mplement agreed-upon changes and monitor their impact on growth."
//         ]
//       },
//       {
//         "name": "Responsive Community Management - for Project Owners",
//         "estimatedHours": "NaN",
//         "description": "<p><span id=\"docs-internal-guid-d33ee30a-7fff-28dd-fe73-92844b01f8e3\"><span style=\"font-size: 11pt; font-family: Poppins, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;\">As a Digital Marketer, I need to provide timely and empathetic community management to enhance brand reputation and build a loyal following.</span></span></p>",
//         "checklistItems": [
//           "Regularly monitor all social media inboxes and notifications for direct messages or mentions.",
//           "Respond to queries, comments, and complaints in a tone that aligns with the brand voice.",
//           "Recognize and escalate critical issues or crises to the client immediately.",
//           "Develop and maintain an FAQ document to respond to common queries efficiently.",
//           "Build relationships with key community members, including influencers or brand advocates."
//         ]
//       },
//       {
//         "name": "Platform-Specific Social Media Growth Tips - for Project Owners",
//         "estimatedHours": "NaN",
//         "description": "<p dir=\"ltr\" style=\"line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;\"><span style=\"font-size: 11pt; font-family: Poppins,sans-serif; color: #000000; background-color: transparent; font-weight: 500; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">These user stories cover a broad spectrum of responsibilities that a social media manager must handle across multiple platforms. Each platform has its unique quirks and best practices, so these stories are created to be adaptable based on the specific platform and the current social media landscape. The estimated times are approximations and can vary based on the actual volume of content, the number of platforms managed, and the specific needs of the client.</span></p>\n<p><span id=\"docs-internal-guid-5c693dcb-7fff-2403-2e57-b173045aa4cc\">&nbsp;</span></p>\n<p dir=\"ltr\" style=\"line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;\"><span style=\"font-size: 11pt; font-family: Poppins,sans-serif; color: #000000; background-color: transparent; font-weight: 500; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">Certainly, each social media platform has distinct user demographics and engagement patterns, so it's important to tailor your strategies to each. Here are platform-specific strategies:</span></p>",
//         "checklistItems": [
//           "Facebook",
//           "Instagram",
//           "LinkedIn",
//           "Twitter",
//           "YouTube",
//           "Pinterest"
//         ]
//       },
//       {
//         "name": "Platform-Specific Engagement Strategies - for Project Owners",
//         "estimatedHours": "NaN",
//         "description": "<p>&nbsp;</p>\n<p dir=\"ltr\" style=\"line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;\"><span style=\"font-size: 11pt; font-family: Poppins,sans-serif; color: #000000; background-color: transparent; font-weight: 500; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">As a Digital Marketer, I need to implement tailored engagement strategies for each platform to foster community growth and interaction.</span></p>",
//         "checklistItems": [
//           "Develop a unique engagement strategy for each platform (e.g., Twitter chats, Instagram Stories, LinkedIn articles, etc.).",
//           "Monitor notifications for each account and respond promptly to comments and messages.",
//           "Create and schedule regular engagement posts (e.g., polls, questions, live videos) to prompt audience interaction.",
//           "Participate in relevant conversations, tags, or challenges within each platform to increase visibility.",
//           "Analyze engagement data to understand what's working and to refine strategies.",
//           "More stuff can be added as per the strategy/plan."
//         ]
//       }
//     ]
//   },
//   {
//     "name": "Email Marketing Campaigns:",
//     "stories": [
//       {
//         "name": "Email Marketing Campaign Launch - for Project Owners",
//         "estimatedHours": "NaN",
//         "description": "<p><span id=\"docs-internal-guid-16fb74c1-7fff-4c2c-9e3b-9d5a02807995\"><span style=\"font-size: 11pt; font-family: Poppins, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;\">As a Digital Marketer, I need to launch email marketing campaigns that include updating the email lists, test emails, sending for internal approval, and sending the templates to the client for approval.</span></span></p>",
//         "checklistItems": [
//           "Update the email list according to the monthly inquiries.",
//           "Test emails to see if everything is working fine.",
//           "Sending the emails for Internal approval.",
//           "Implement the changes.",
//           "Send the email templates to the client.",
//           "Revise the templates.",
//           "Launch the campaigns."
//         ]
//       }
//     ]
//   },
//   {
//     "name": "SEO Improvements:",
//     "stories": [
//       {
//         "name": "Blog Posts Creation - for Imran Khalid (Stakeholder)",
//         "estimatedHours": "NaN",
//         "description": "<p><span id=\"docs-internal-guid-94701810-7fff-a322-b1a4-7f43adad7efd\"><span style=\"font-size: 11pt; font-family: Poppins, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;\">As an SEO Specialist, I want to create engaging and informative blog posts that align with the client's brand and objectives.</span></span></p>",
//         "checklistItems": [
//           "Research the assigned topic or industry trends.",
//           "Develop an outline and structure for the blog post.",
//           "Write and edit the blog content, ensuring it is well-researched and free of errors.",
//           "Incorporate relevant images, infographics, or multimedia elements.",
//           "Optimize the blog post for SEO by including target keywords."
//         ]
//       }
//     ]
//   },
//   {
//     "name": "Graphic Designing:",
//     "stories": [
//       {
//         "name": "Designing Social Media Content - for Muhammad Ameen (Stakeholder) ",
//         "estimatedHours": "NaN",
//         "description": "<p>&nbsp;</p>\n<p dir=\"ltr\" style=\"line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;\"><span style=\"font-size: 11pt; font-family: Poppins,sans-serif; color: #000000; background-color: transparent; font-weight: 500; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">As a graphic designer, I want to design striking social media posts that reflect the brand identity and complement the written content.</span></p>",
//         "checklistItems": [
//           "Analyze the written content for each social media post, carousels etc..",
//           "Design images or graphics that complement the content and enhance the message.",
//           "Adapt the design for various platforms (e.g., Instagram, Facebook, Twitter), considering different dimensions and best practices.",
//           "Apply brand guidelines consistently across all designs.",
//           "Prepare files for optimal resolution and size for social media.",
//           "Get the designs reviewed by HOD and implement the changes."
//         ]
//       },
//       {
//         "name": "Designing Graphics for Blog Posts - for Muhammad Ameen (Stakeholder) ",
//         "estimatedHours": "NaN",
//         "description": "<p><span id=\"docs-internal-guid-d333f77e-7fff-a581-7fda-88a19ecd99f2\"><span style=\"font-size: 11pt; font-family: Poppins, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;\">As a graphic designer, I want to create visually appealing, brand-aligned graphics for each blog post to enhance readability and engagement.</span></span></p>",
//         "checklistItems": [
//           "Review the blog post content to understand the context and tone.",
//           "Create custom images, charts, or diagrams that help explain the content of the blog post.",
//           "Select or design feature images that are attention-grabbing and relevant.",
//           "Ensure all graphics follow the brand's style guide in terms of color scheme, typography, and logo usage.",
//           "Format images correctly for optimal web display and performance.",
//           "Get the designs reviewed by HOD and implement the changes."
//         ]
//       },
//       {
//         "name": "Creating Email Marketing Visuals - for Muhammad Ameen (Stakeholder) ",
//         "estimatedHours": "NaN",
//         "description": "<p>&nbsp;</p>\n<p dir=\"ltr\" style=\"line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;\"><span style=\"font-size: 11pt; font-family: Poppins,sans-serif; color: #000000; background-color: transparent; font-weight: 500; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">As a graphic designer, I want to design captivating email templates and visuals that improve reader engagement and click-through rates.</span></p>",
//         "checklistItems": [
//           "Review the email marketing content and objectives.",
//           "Design or update email templates using brand colors, fonts, and styling.",
//           "Create relevant graphics, such as banners or inline images, to support the content.",
//           "Ensure that visuals are responsive for mobile devices.",
//           "Test email visuals in various email clients to ensure consistency.",
//           "Get the designs reviewed by HOD and implement the changes."
//         ]
//       },
//       {
//         "name": "Producing Visuals for Social Media Stories and Highlights - for Muhammad Ameen (Stakeholder) ",
//         "estimatedHours": "NaN",
//         "description": "<p>&nbsp;</p>\n<p dir=\"ltr\" style=\"line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;\"><span style=\"font-size: 11pt; font-family: Poppins,sans-serif; color: #000000; background-color: transparent; font-weight: 500; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">As a graphic designer, I want to create engaging visuals for social media stories and highlights that accurately reflect brand messaging.</span></p>",
//         "checklistItems": [
//           "Understand the content strategy for stories and highlights.",
//           "Create graphics, animations, or interactive elements that are optimized for stories.",
//           "Design cover images for highlights that are consistent with the brand's visual identity.",
//           "Check compatibility and visual appeal on mobile devices.",
//           "Adapt content for different social media platforms' story features, if necessary.",
//           "Get the designs reviewed by HOD and implement the changes."
//         ]
//       },
//       {
//         "name": "Crafting Engaging Reels/Social Media Videos - for Muhammad Ameen (Stakeholder)",
//         "estimatedHours": "NaN",
//         "description": "<p>&nbsp;</p>\n<p dir=\"ltr\" style=\"line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;\"><span style=\"font-size: 11pt; font-family: Poppins,sans-serif; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">As a graphic designer, I aim to craft engaging and visually appealing social media videos that highlight the key messages from our content creation team.</span></p>",
//         "checklistItems": [
//           "Review the provided content and distill the main message for the video narrative.",
//           "Create the video with a focus on strong visuals that complement the content.",
//           "Edit for clarity, engagement, and brand consistency.",
//           "Collaborate with the content team on any necessary revisions.",
//           "Finalize the video for social media publication.",
//           "Get the designs reviewed by HOD and implement the changes."
//         ]
//       },
//       {
//         "name": "Fulfilling Client-Specific Design Requests - for Muhammad Ameen (Stakeholder) ",
//         "estimatedHours": "NaN",
//         "description": "<p><span id=\"docs-internal-guid-1ddf093a-7fff-f56d-ff27-7f4cf7477d88\"><span style=\"font-size: 11pt; font-family: Poppins, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;\">As a graphic designer, I want to meet client-specific requests for custom graphics that fit their brand and objectives</span></span></p>",
//         "checklistItems": [
//           "Communicate with the Project Owner/ client to grasp their specific needs and preferences.",
//           "Sketch preliminary designs or concepts for client approval.",
//           "Produce the final designs, ensuring they meet the specifications and are consistent with the brand identity.",
//           "Review with the client and revise as necessary.",
//           "Prepare the final files in the appropriate formats for the client's use.",
//           "Get the designs reviewed by HOD and implement the changes."
//         ]
//       },
//       {
//         "name": "Designing Informative Infographics - for Muhammad Ameen (Stakeholder)",
//         "estimatedHours": "NaN",
//         "description": "<p>&nbsp;</p>\n<p dir=\"ltr\" style=\"line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;\"><span style=\"font-size: 11pt; font-family: Poppins,sans-serif; color: #000000; background-color: transparent; font-weight: 500; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">As a graphic designer, I want to transform data and information into visually appealing infographics that are easy to understand and share.</span></p>",
//         "checklistItems": [
//           "Analyze the data or information to be included in the infographic.",
//           "Create a visual hierarchy to ensure the most important information stands out.",
//           "Design custom graphics, charts, or icons to represent the data effectively.",
//           "Apply the brand's color scheme, typography, and styling for consistency.",
//           "Format the infographic for different uses, such as a full web page, social media post, or PDF download.",
//           "Get the designs reviewed by HOD and implement the changes."
//         ]
//       }
//     ]
//   },
//   {
//     "name": "Website Management:",
//     "stories": [
//       {
//         "name": "Theme and Extensions/Plugin Management - for Imran Khan (Stakeholders)",
//         "estimatedHours": "NaN",
//         "description": "<p dir=\"ltr\" style=\"line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;\"><span style=\"font-size: 11pt; font-family: Poppins,sans-serif; color: #000000; background-color: transparent; font-weight: 500; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">As a website manager, I want to keep the site's design and functionality updated.</span><span id=\"docs-internal-guid-b55b3551-7fff-5154-3280-b000b9a73ffa\"></span></p>",
//         "checklistItems": [
//           "Regularly check for updates related to the site's theme and extensions/plugins.",
//           "Back up the website before implementing any updates",
//           "Update and test the site after each major change.",
//           "Remove unused extensions/plugins to maintain performance.",
//           "Address any compatibility issues after updates. "
//         ]
//       },
//       {
//         "name": "Website Security Management - for Imran Khan (Stakeholders)",
//         "estimatedHours": "NaN",
//         "description": "<p><span id=\"docs-internal-guid-baa1a1f4-7fff-ca9c-e731-0309bd3a66c6\"><span style=\"font-size: 11pt; font-family: Poppins, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;\">As a website manager, I want to ensure the site is secure.</span></span></p>",
//         "checklistItems": [
//           "Regularly update the core system and any extensions/plugins.",
//           "Implement strong passwords and user access controls.",
//           "Maintain an active backup solution.",
//           "Scan the site for vulnerabilities and address them.",
//           "Monitor website traffic and activities for anomalies."
//         ]
//       },
//       {
//         "name": "Performance Optimization - for Imran Khan (Stakeholders)",
//         "estimatedHours": "NaN",
//         "description": "<p><span id=\"docs-internal-guid-41d681f2-7fff-41fb-4414-a7e869f6ffda\"><span style=\"font-size: 11pt; font-family: Poppins, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;\">As a website manager, I want to ensure the site is secure.</span></span></p>",
//         "checklistItems": [
//           "Regularly update the core system and any extensions/plugins.",
//           "Implement strong passwords and user access controls.",
//           "Maintain an active backup solution.",
//           "Scan the site for vulnerabilities and address them.",
//           "Monitor website traffic and activities for anomalies."
//         ]
//       },
//       {
//         "name": "Implementing Client-Requested Changes - for Imran Khan (Stakeholders)",
//         "estimatedHours": "NaN",
//         "description": "<p><span id=\"docs-internal-guid-106269e3-7fff-46e7-c24c-b63253979afc\"><span style=\"font-size: 11pt; font-family: Poppins, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;\">As a website manager, I want to accommodate spontaneous changes or additions as requested by the client.</span></span></p>",
//         "checklistItems": [
//           "Document client requests and confirm the scope.",
//           "Plan and schedule the updates.",
//           "Implement changes while ensuring the website remains responsive and user-friendly.",
//           "Test and review all new elements.",
//           "Obtain client feedback and iterate as required. "
//         ]
//       },
//       {
//         "name": "Monitoring Domain and Hosting - for Imran Khan (Stakeholders)",
//         "estimatedHours": "NaN",
//         "description": "<p><span id=\"docs-internal-guid-b7d3add2-7fff-8095-37ee-216f3720ab4e\"><span style=\"font-size: 11pt; font-family: Poppins, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;\">As a website manager, I want to monitor the domain and hosting expiry dates to prevent any site downtime.</span></span></p>",
//         "checklistItems": [
//           "Document the expiration dates of the domain and hosting services upon project initiation.",
//           "Set reminders for renewal deadlines well in advance.",
//           "Regularly verify the status of the domain and hosting services to catch any unexpected changes or notifications.",
//           "Coordinate with the client for renewals, ensuring they're aware of upcoming expiry dates.",
//           "Process renewals or assist the client in doing so, confirming successful transactions. "
//         ]
//       }
//     ]
//   },
//   {
//     "name": "Paid Marketing:",
//     "stories": [
//       {
//         "name": "Paid Marketing Strategies - for Farhan Khan (Stakeholder)",
//         "estimatedHours": "NaN",
//         "description": "<p><span id=\"docs-internal-guid-08565890-7fff-7ea8-7988-dd1bd1b55c90\"><span style=\"font-size: 11pt; font-family: Poppins, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;\">As a Paid marketing Specialist, I want to outline the Paid Marketing strategies for the month, summarizing the key focus areas and planned activities.</span></span></p>",
//         "checklistItems": [
//           "Paid Marketing Objectives: Provide an overview of the paid marketing campaign objectives, such as lead generation, website traffic, or sales.",
//           "Advertising Platforms: Specify the chosen advertising platforms for paid marketing campaigns.",
//           "Budget Allocation: Summarize how the client's budget will be distributed across different advertising platforms and campaigns.",
//           "Audience Targeting: Provide a brief description of the target audience demographics and interests.",
//           "Monitoring and Optimization: Highlight the ongoing monitoring and optimization processes for Paid Marketing.",
//           "Reporting Metrics: Identify the key performance indicators (KPIs) that will be tracked for  Paid Marketing.",
//           "Responsibilities: Indicate the team members responsible for executing the outlined Paid Marketing strategies."
//         ]
//       }
//     ]
//   },
//   {
//     "name": "Client Reporting and Communication:",
//     "stories": [
//       {
//         "name": "Paid Campaign Updates - for Farhan Khan (Stakeholders)",
//         "estimatedHours": "NaN",
//         "description": "<p><span id=\"docs-internal-guid-844694a1-7fff-ba6c-2664-07db1b54d83d\"><span style=\"font-size: 11pt; font-family: Poppins, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;\">As a marketing specialist, I want to keep the client updated on new inquiries and weekly performance of paid marketing campaigns to maintain transparency and adjust strategies as needed.</span></span></p>",
//         "checklistItems": [
//           "Compile a weekly summary of inquiries received with details on follow-ups or sales conversions.",
//           "Provide a weekly performance report of paid marketing campaigns, including spend, impressions, clicks, and conversions.",
//           "Highlight any trends or insights from the weekly data that may inform adjustments in strategy.",
//           "Send Looker Studio Report",
//           "Discuss with the client any immediate actions taken or proposed in response to the week's performance."
//         ]
//       },
//       {
//         "name": "Comprehensive Month-End Reporting - Project Owners",
//         "estimatedHours": "NaN",
//         "description": "<p><span id=\"docs-internal-guid-9d004346-7fff-5241-38a4-f524050b3616\"><span style=\"font-size: 11pt; font-family: Poppins, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;\">As a marketing specialist, I want to deliver a detailed month-end report that provides a summary and insights into all the activities performed, showcasing the results and work accomplished.</span></span></p>",
//         "checklistItems": [
//           "Follow the established month-end report template to ensure consistency and completeness.",
//           "Include an executive summary that provides a snapshot of performance across all epics.",
//           "Add the Looker Studio link.",
//           "Detail the results and key performance metrics for each marketing activity.",
//           "Add sections for insights and observations, highlighting successes and areas for improvement.",
//           "Conclude with actionable recommendations for the next month based on the report's findings.",
//           "Review the report internally for accuracy and clarity before sending it to the client.",
//           "Schedule a call with the client to walk through the report and discuss any questions or feedback."
//         ]
//       },
//       {
//         "name": "Month-End Team Meeting With Client - For all key stakeholders",
//         "estimatedHours": "NaN",
//         "description": "<p>&nbsp;</p>\n<p dir=\"ltr\" style=\"line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;\"><span style=\"font-size: 11pt; font-family: Poppins,sans-serif; color: #000000; background-color: transparent; font-weight: 500; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;\">We want to provide incremental updates to the client on the progress of marketing activities, ensuring they are informed and engaged throughout the execution of the project.</span></p>",
//         "checklistItems": [
//           "Inform the client as soon as a deliverable from any epic is ready and share the outcome, like Content Packet, Email Templates, Etc..."
//         ]
//       }
//     ]
//   },
//   {
//     "name": "Internal Project Meetings:",
//     "stories": [
//       {
//         "name": "Internal Meetings with teammates",
//         "estimatedHours": "NaN",
//         "description": "<p><span id=\"docs-internal-guid-2337984d-7fff-f8fb-d28e-59de83121465\"><span style=\"font-size: 11pt; font-family: Poppins, sans-serif; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre-wrap;\">Maintain good communication with your teammates regarding your projects.</span></span></p>",
//         "checklistItems": []
//       }
//     ]
//   }
// ],
// null,2
// )
//     ;

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

            if(!estimatedHours){
                estimatedHours = 0;
            }

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