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
<style>
img,video{max-width:100%;height:auto}[hidden]{display:none}.relative{position:relative}.my-4{margin-top:1rem;margin-bottom:1rem}.mb-4{margin-bottom:1rem}.ml-4{margin-left:1rem}.mr-2{margin-right:0.5rem}.mt-4{margin-top:1rem}.flex{display:flex}.hidden{display:none}.h-5{height:1.25rem}.h-8{height:2rem}.h-full{height:100%}.w-1\/3{width:33.333333%}.w-5{width:1.25rem}.w-8{width:2rem}.w-full{width:100%}.flex-grow{flex-grow:1}.transform{transform:translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}.cursor-pointer{cursor:pointer}.flex-row{flex-direction:row}.flex-col{flex-direction:column}.items-center{align-items:center}.justify-center{justify-content:center}.justify-between{justify-content:space-between}.gap-1{gap:0.25rem}.gap-2{gap:0.5rem}.gap-4{gap:1rem}.space-x-2 > :not([hidden]) ~ :not([hidden]){--tw-space-x-reverse:0;margin-right:calc(0.5rem * var(--tw-space-x-reverse));margin-left:calc(0.5rem * calc(1 - var(--tw-space-x-reverse)))}.rounded{border-radius:0.25rem}.rounded-full{border-radius:9999px}.rounded-lg{border-radius:0.5rem}.rounded-t-lg{border-top-left-radius:0.5rem;border-top-right-radius:0.5rem}.border{border-width:1px}.border-2{border-width:2px}.border-x-4{border-left-width:4px;border-right-width:4px}.border-b-4{border-bottom-width:4px}.border-solid{border-style:solid}.border-none{border-style:none}.border-blue-100\/40{border-color:rgb(219 234 254 / 0.4)}.border-cyan-100{--tw-border-opacity:1;border-color:rgb(207 250 254 / var(--tw-border-opacity))}.border-gray-100{--tw-border-opacity:1;border-color:rgb(243 244 246 / var(--tw-border-opacity))}.border-gray-200{--tw-border-opacity:1;border-color:rgb(229 231 235 / var(--tw-border-opacity))}.border-gray-300{--tw-border-opacity:1;border-color:rgb(209 213 219 / var(--tw-border-opacity))}.border-purple-100{--tw-border-opacity:1;border-color:rgb(243 232 255 / var(--tw-border-opacity))}.border-purple-200{--tw-border-opacity:1;border-color:rgb(233 213 255 / var(--tw-border-opacity))}.border-sky-100{--tw-border-opacity:1;border-color:rgb(224 242 254 / var(--tw-border-opacity))}.bg-blue-100\/40{background-color:rgb(219 234 254 / 0.4)}.bg-blue-200{--tw-bg-opacity:1;background-color:rgb(191 219 254 / var(--tw-bg-opacity))}.bg-blue-500{--tw-bg-opacity:1;background-color:rgb(59 130 246 / var(--tw-bg-opacity))}.bg-gray-100{--tw-bg-opacity:1;background-color:rgb(243 244 246 / var(--tw-bg-opacity))}.bg-gray-200{--tw-bg-opacity:1;background-color:rgb(229 231 235 / var(--tw-bg-opacity))}.bg-green-200{--tw-bg-opacity:1;background-color:rgb(187 247 208 / var(--tw-bg-opacity))}.bg-green-500{--tw-bg-opacity:1;background-color:rgb(34 197 94 / var(--tw-bg-opacity))}.bg-purple-100\/40{background-color:rgb(243 232 255 / 0.4)}.bg-purple-500{--tw-bg-opacity:1;background-color:rgb(168 85 247 / var(--tw-bg-opacity))}.bg-rose-500{--tw-bg-opacity:1;background-color:rgb(244 63 94 / var(--tw-bg-opacity))}.bg-sky-100{--tw-bg-opacity:1;background-color:rgb(224 242 254 / var(--tw-bg-opacity))}.bg-transparent{background-color:transparent}.bg-white{--tw-bg-opacity:1;background-color:rgb(255 255 255 / var(--tw-bg-opacity))}.p-2{padding:0.5rem}.p-3{padding:0.75rem}.p-4{padding:1rem}.px-1{padding-left:0.25rem;padding-right:0.25rem}.px-2{padding-left:0.5rem;padding-right:0.5rem}.px-4{padding-left:1rem;padding-right:1rem}.py-1{padding-top:0.25rem;padding-bottom:0.25rem}.py-2{padding-top:0.5rem;padding-bottom:0.5rem}.\!pr-2{padding-right:0.5rem !important}.pt-4{padding-top:1rem}.text-left{text-align:left}.text-center{text-align:center}.text-right{text-align:right}.text-base{font-size:1rem;line-height:1.5rem}.text-lg{font-size:1.125rem;line-height:1.75rem}.text-xs{font-size:0.75rem;line-height:1rem}.font-bold{font-weight:700}.font-medium{font-weight:500}.text-black{--tw-text-opacity:1;color:rgb(0 0 0 / var(--tw-text-opacity))}.text-fuchsia-900\/70{color:rgb(112 26 117 / 0.7)}.text-gray-800{--tw-text-opacity:1;color:rgb(31 41 55 / var(--tw-text-opacity))}.text-white{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity))}.shadow-md{--tw-shadow:0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);--tw-shadow-colored:0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)}.transition-all{transition-property:all;transition-timing-function:cubic-bezier(0.4, 0, 0.2, 1);transition-duration:150ms}.transition-transform{transition-property:transform;transition-timing-function:cubic-bezier(0.4, 0, 0.2, 1);transition-duration:150ms}.duration-300{transition-duration:300ms}.ease-in-out{transition-timing-function:cubic-bezier(0.4, 0, 0.2, 1)}.hover\:scale-105:hover{--tw-scale-x:1.05;--tw-scale-y:1.05;transform:translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}.hover\:border-blue-500:hover{--tw-border-opacity:1;border-color:rgb(59 130 246 / var(--tw-border-opacity))}.hover\:border-cyan-200:hover{--tw-border-opacity:1;border-color:rgb(165 243 252 / var(--tw-border-opacity))}.hover\:border-purple-500:hover{--tw-border-opacity:1;border-color:rgb(168 85 247 / var(--tw-border-opacity))}.hover\:bg-blue-600:hover{--tw-bg-opacity:1;background-color:rgb(37 99 235 / var(--tw-bg-opacity))}.hover\:bg-gray-200:hover{--tw-bg-opacity:1;background-color:rgb(229 231 235 / var(--tw-bg-opacity))}.hover\:bg-green-600:hover{--tw-bg-opacity:1;background-color:rgb(22 163 74 / var(--tw-bg-opacity))}.hover\:bg-purple-600:hover{--tw-bg-opacity:1;background-color:rgb(147 51 234 / var(--tw-bg-opacity))}.hover\:bg-rose-600:hover{--tw-bg-opacity:1;background-color:rgb(225 29 72 / var(--tw-bg-opacity))}.hover\:bg-white:hover{--tw-bg-opacity:1;background-color:rgb(255 255 255 / var(--tw-bg-opacity))}.hover\:shadow-lg:hover{--tw-shadow:0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);--tw-shadow-colored:0 10px 15px -3px var(--tw-shadow-color), 0 4px 6px -4px var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)}.disabled\:bg-purple-700:disabled{--tw-bg-opacity:1;background-color:rgb(126 34 206 / var(--tw-bg-opacity))}.group.active .group-\[\.active\]\:w-\[80\%\]{width:80%}

input{
    border: 0 !important;
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
                        
                        <button class="px-4 py-1 rounded <?= $sprint->status == 2 ? 'bg-green-500 text-white' : 'bg-white border border-solid border-gray-200 text-gray-800' ?> transition-all hover:shadow-lg shadow-md hover:scale-105"   onclick="updateSprintStatus(this);" data-status="<?= $sprint->status ?>" data-sprint-id="<?= $sprint->id ?>">
                            <?php 
                                if($sprint->status == 0){
                                    echo "No started";
                                }else if($sprint->status == 1){
                                    echo "In Progress";
                                }else {
                                    echo "Completed";
                                }
                            ?>
                        </button>

                    </div>
                </div>          

                <div class="collapsible-content expanded" id="sprint-<?= $sprint->id ?>-content">
                    <div class="p-4 flex flex-col gap-4 sprint-list" data-issue-id="<?= $sprint->id ?>" data-issue-type="sprint" id="sprint-<?= $sprint->id ?>-list">

                        <?php foreach ($sprint->stories as $story):
                            echo story($story, true, site_url('clients/project/' . $project->id . '?group=project_tasks&taskid=' . $story->id));
                            endforeach; 
                        ?>
                       
                    </div>
                </div>
            </div>

        <?php endforeach; ?>


        </div>

        <!-- Backlog Container -->
        <div class="bg-white border-x-4 border-b-4 border-gray-100 rounded-lg my-4 border-solid">

            <div class="bg-gray-100 p-3 flex flex-row justify-left items-center gap-2 cursor-pointer" onclick="toggleCollapse(event, 'backlog')">
                <div class="fas fa-angle-down transform transition-transform duration-300"></div>
                <div class="text-gray-800 font-bold text-lg">Backlog</div>
            </div>

            <div class="collapsible-content expanded" id="backlog">

                <div class="p-4">


                <div id="epicContainer" class="flex flex-col gap-4 pt-4">
                <?php foreach ($epics as $epic): ?>

                    <div class="border-2 border-solid border-purple-100 rounded-lg transition-all hover:shadow-lg ease-in-out duration-300">
            
                        <div class="flex justify-between items-center py-2 px-4 bg-purple-100/40 rounded-t-lg border-solid border-purple-200 cursor-pointer" onclick="toggleCollapse(event, 'epic-<?= $epic->id ?>-content')">

                            <div class="flex items-center text-black font-bold text-base gap-2 w-1/3">
                                <div class="fas fa-angle-down transform transition-transform duration-300"></div>
                                <input class="w-full text-black font-bold text-base bg-transparent px-2 py-1" value="<?= htmlspecialchars($epic->name); ?>" onchange="updateEpicName(this.value, <?= $epic->id ?>)" placeholder="Epic Name" />
                            </div>

                            <div class="flex flex-row gap-2">

                            <?php if ($project->settings->create_tasks == 1) { ?>
                                <a href="<?php echo site_url('clients/project/' . $project->id . '?group=new_task&epicid='.$epic->id); ?>"
                                class="bg-green-500 text-white rounded-lg px-4 py-2 transition-all hover:bg-green-600 ease-in-out duration-300">
                                    <?php echo _l('New Story'); ?>
                                </a>
                            <?php } ?>
                            </div>
                        </div>

                        <div class="collapsible-content expanded" id="epic-<?= $epic->id ?>-content">
                            <div class="p-4 flex flex-col gap-4 epic-list" data-issue-type="epic" data-issue-id="<?= $epic->id ?>" id="epic-<?= $epic->id ?>-list">
                            <?php foreach ($epic->stories as $story):
                                echo story($story, false, site_url('clients/project/' . $project->id . '?group=project_tasks&taskid=' . $story->id));
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