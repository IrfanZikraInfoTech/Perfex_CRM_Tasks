<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css" rel="stylesheet" type="text/css"/>

<style>
.clamp-lines {
    display: -webkit-box;
    -webkit-line-clamp: 3; /* Number of lines you want to display */
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.myscrollbar::-webkit-scrollbar {
    width: 0; 
}

.myscrollbar {
    scrollbar-width: none;
}

.myscrollbar {
    -ms-overflow-style: none;
}


/* timeline  */
.no-scroll::-webkit-scrollbar {
  display: none;
}
.clock-in-time {
    background: linear-gradient(90deg, rgba(54,162,235,0.2) 0%, rgba(74,182,255,0.2) 100%)!important;
    border-color: rgba(54, 162, 235, 1)!important;
}

.afk-time {
    background: linear-gradient(90deg, rgba(255,206,86,0.2) 0%, rgba(255,226,106,0.2) 100%)!important;
    border-color: rgba(255, 206, 86, 1)!important;
}
.shift-time {
    background: linear-gradient(90deg, rgba(255, 105, 180, 0.2) 0%, rgba(255, 182, 193, 0.2) 100%)!important;
    border-color: rgba(255, 105, 180, 1)!important;
}

.liked-bg-color {
    background-color: red !important;
}

#visualization .vis-timeline{
    border-radius:30px;
}
.not-seen {
    background-color: #fffcca;
}
</style>

<div id="wrapper">

    <div class="content">
        <div class="row">
                <?php //$this->load->view('admin/includes/alerts'); ?>

                <?php //hooks()->do_action('before_start_render_dashboard_content'); ?>

                <div class="clearfix"></div> 
                
                <div class="col-md-12 my-4" data-container="middle-left-6">
                    <?php $this->load->view('admin/management/dashboard_widget' ); ?>
                    <?php render_dashboard_widgets('middle-left-6'); ?>
                </div>
               
                <div class="col-md-12 my-4" data-container="middle-left-6"> 

                    <div class="flex lg:flex-row flex-col gap-4">
                        
                        <div class="myscrollbar bg-white shadow-lg hover:shadow-xl border border-solid border-white hover:border-yellow-400 transition-all rounded-[50px] p-6 lg:w-1/4 w-full mx-auto  overflow-y-auto">
                            <h5 class="attendance text-xl font-semibold mb-2 text-center text-gray-700 border-b pb-2 capitalize">ATTENDENCE</h1>
                            <div class="mt-4 flex flex-col space-y-3">

                                <!-- Attendance Status -->
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-md">
                                    <span class="text-gray-600">Status:</span>
                                    <span id="attendanceStatus" class="text-gray-500 font-bold">-</span>

                                </div>

                                <!-- Punctuality Rate -->
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-md">
                                    <span class="text-gray-600">Rate:</span>
                                    <span class="font-semibold"><?= round($puctuality_rate,2); ?>%</span>
                                </div>

                                <!-- Current Month -->
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-md">
                                    <span class="text-gray-600">Current Month:</span>
                                    <span class="font-semibold"><?= date("F") ?></span>
                                    <!-- Replace 'October' with the current month -->
                                </div>

                            </div>
                        </div>

                        <div class="bg-white shadow-lg hover:shadow-xl border border-solid border-white hover:border-yellow-400 transition-all rounded-[50px] p-7 flex md:flex-row flex-col justify-between h-full lg:w-3/4 w-full">
                            <div id="visualization"  class="relative w-full rounded-[50px]" >
                            </div>
                        </div>


                    </div>
                </div>




            <div class="col-md-12 my-4 flex lg:flex-row flex-col gap-10">
                    <!-- Assigned Tasks -->
                    <div class="lg:w-1/2 w-full transition-all rounded-[50px] overflow-hidden p-5 bg-white shadow-xl">

                        <div class="uppercase tracking-wide text-xl text-center text-gray-700 font-bold mb-5">Assigned Task</div>
                        
                        <div class="flex flex-col bg-sky-100 px-4 py-2 rounded-[50px] shadow-inner overflow-y-scroll myscrollbar max-h-[300px]">
                                <?php
                                    $tasks = $this->tasks_model->get_user_tasks_assigned($GLOBALS['current_user']->staffid);
                                    $total_tasks = 0;
                                    $completed_tasks = 0;

                                    $current_date = date('Y-m-d'); 

                                    if(count($tasks) > 0){
                                ?>
                        
                                <div class="p-4 flex flex-col mt-4 gap-2">         
                                    
                                    <?php
                                        foreach ($tasks as $task) {
                                            $current_time = time();
                                            $start_time = strtotime($task->startdate);
                                            $due_time = strtotime($task->duedate ? $task->duedate : $task->startdate);

                                            if ($task->status == 5 && $due_time < $current_time) {
                                                continue; 
                                            }

                                        ?>
                                        <button class="task-block bg-white px-4 py-2 rounded-xl cursor-pointer border border-gray-200 border-solid transition-all hover:border-yellow-400 hover:shadow-lg" data-task-id="<?= $task->id ?>" onclick="init_task_modal(<?= $task->id ?>)">
                                            <div class="flex items-center justify-between">
                                                <span class="font-semibold"><?= $task->name ?></span>
                                                <span><?= format_task_status($task->status);  ?></span>
                                            </div>
                                        </button>

                                        <?php

                                    }
                                    echo '</div>';
                                }else{
                                    echo 'No Task!';
                                }
                                   
                                    ?>
                            
                        </div>
                    </div>

                        <!-- Newsfeed Panel Section -->
                        <div class="lg:w-1/2 w-full  border-l border-gray-200 flex flex-col p-5 bg-white rounded-[50px] shadow-lg">
                            <div class="panel-body p-0 m-0">
                                <div class="uppercase tracking-wide text-xl text-center text-gray-700 font-bold mb-5 ">Announcements</div>
                                <div class="bg-sky-100 p-4 py-3 shadow-inner rounded-[50px] overflow-y-scroll myscrollbar max-h-[300px]">
                                        
                                    <?php $count = 0;
                                    $currentUserId = get_staff_user_id(); // Get the current user ID
                                    foreach($posts as $post):
                                        $isLiked = $this->newsfeed_model->user_liked_post($post["postid"]) ? "true" : "false";
                                        $totalLikes = count($this->newsfeed_model->get_post_likes($post["postid"]));
                                        $currentDateTime = new DateTime();
                                        $postDateTime = new DateTime($post["datecreated"]);
                                        $hasUserSeenPost = in_array($currentUserId, explode(',', $post["seen_by"])); // Check if user ID exists in seen_by column
                                        $postClass = $hasUserSeenPost ? "" : "not-seen"; // Assign the class based on if the user has seen the post or not
                                        $interval = $currentDateTime->diff($postDateTime);
                                        
                                        $timeString = '';
                                        if ($interval->y > 0) {
                                            $timeString = $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
                                        } elseif ($interval->m > 0) {
                                            $timeString = $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
                                        } elseif ($interval->d > 0) {
                                            $timeString = $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
                                        } elseif ($interval->h > 0) {
                                            $timeString = $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
                                        } elseif ($interval->i > 0) {
                                            $timeString = $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
                                        } else {
                                            $timeString = 'just now';
                                        }
                                    ?>
                                        <div data-postid="<?= $post["postid"] ?>" data-total-likes="<?= $totalLikes ?>"  data-liked-by-user="<?= $isLiked ?>"  class="dashboard-posts bg-white rounded-[40px] m-4 p-6 pb-2 cursor-pointer hover:shadow-md border border-gray-200 border-solid transition-all hover:border-yellow-400  <?= $postClass ?>" data-creator="<?= $post["creator_name"] ?>" data-content="<?= htmlentities($post["content"]) ?>" onclick="openPostModal(this)">
                                            <div class="flex justify-between items-center">
                                                <div class="font-bold text-xl"><?= $post["creator_name"] ?></div>
                                                <div class="text-gray-500 text-sm italic"><?= $timeString ?></div>
                                            </div>
                                            <div class="text-gray-500 mb-3">Published: <?= $post["datecreated"] ?></div>
                                            <div class="clamp-lines text-md mb-4"><?= $post["content"] ?></div>
                                            
                                            <div class="likes-count text-gray-500 mb-1">
                                                <?= $totalLikes ?> <?= $totalLikes === 1 ? "like" : "likes" ?>
                                            </div>
                                            <!-- Like action -->
                                            <!-- <div class="flex justify-start items-center">
                                                <button class="p-2 rounded-full hover:bg-gray-200 focus:outline-none" onclick="load_likes_modal(<?= $post["postid"] ?>)">
                                                    <i class="fas fa-heart text-gray-500"></i>
                                                </button>
                                            </div> -->
                                        </div> 
                                        <?php $count++;
                                        endforeach;  
    
                                        if($count < 1){
                                            echo '<h2>No announcements!</h2>';
                                        }

                                        ?>
                                    </div>    
                            </div>    
                        </div>    
                    </div>
    


            
                <!-- Summary-->
            <div class="col-md-12 my-4">
                
                <div class="p-4 w-full bg-white shadow-xl rounded-[40px]">


                        <!-- Row for Summary heading and Date Picker -->
                        <div class="flex justify-between items-center mb-4">
                            <div class="uppercase tracking-wide text-xl text-gray-700 font-bold text-center w-full">Summary</div>
                            <input type="date" value="<?=date("Y-m-d")?>" id="summary_date" class="rounded p-2 mr-4" onchange="getOrSaveStaffSummary();">
                        </div>

                        <div class="flex flex-row p-4 bg-sky-100 min-h-[300px] rounded-[50px]">
                            <!-- Left Box with dummy summary -->
                            
                            <div class="w-1/2 p-4">
                                    <!-- <h4><b>DUMMY SUMMARY </b></h4> -->
                                    <textarea class="w-full h-full transition-all shadow-sm hover:shadow-xl shadow-inner p-5 bg-white rounded-[40px] focus:outline-none focus:ring-2 resize-none focus:ring-blue-400 overflow-y-hidden text-lg border border-gray-200 border-solid hover:border-yellow-400" readonly ><?= get_option('dummy_summary'); ?></textarea>
                            </div>
                            
                            
                            <!-- Right Box for writing summary -->
                            <div class="w-1/2 p-4 flex flex-col gap-3">
                                <textarea id="summary-textarea" class="w-full flex-grow transition-all shadow-sm hover:shadow-xl shadow-inner p-5 bg-white rounded-[40px] focus:outline-none focus:ring-2 resize-none focus:ring-blue-400 overflow-y-hidden text-lg border border-gray-200 border-solid hover:border-yellow-400" placeholder="Write your summary here..."></textarea>

                                <div class="flex flex-row w-full justify-end">
                                    <button onclick="getOrSaveStaffSummary(document.getElementById('summary-textarea').value)" class="w-full bg-blue-500/90 text-white font-semibold py-2 px-4 rounded-3xl shadow-sm hover:shadow-xl  transition-all border border-blue-200 border-solid hover:border-blue-700">Submit</button>
                                </div>


                            </div>

                            

                        </div>

                </div>
            </div>


            <!-- Countries Clocks -->
            <div class="col-md-12 my-4"  data-container="middle-left-6">
                <div class="bg-white shadow-lg rounded-[50px] p-4 my-4 flex md:flex-row flex-col justify-between">
                    <div id="clocks" class="p-4 rounded-lg text-lg grid grid-cols-2 gap-4 w-full"></div>
                </div>
            </div>    

            <!-- Upcoming Birthdays -->
            <div class="col-md-12">
                    <?php

                    if(isset($upcoming_birthdays) && !empty($upcoming_birthdays)): ?>
                        <div class="upcoming-birthdays bg-white p-6 rounded-[50px] shadow-lg">
                            <h3 class="uppercase tracking-wide text-xl text-center text-gray-700 font-bold mb-5">Upcoming Birthdays</h3>
                            <div class="grid grid-cols-2 gap-6">
                                <?php foreach($upcoming_birthdays as $staff): ?>
                                    <?php
                                        // Extract month and day from date_of_birth
                                        $dob = DateTime::createFromFormat('Y-m-d', $staff['date_of_birth']);
                                        $formattedDob = $dob->format('F j');

                                        $currentDate = new DateTime(date('Y-m-d'));
                                        $birthdayThisYear = new DateTime(date('Y') . '-' . date('m', strtotime($staff['date_of_birth'])) . '-' . date('d', strtotime($staff['date_of_birth'])));

                                        if ($currentDate > $birthdayThisYear) {
                                            $birthdayThisYear->modify('+1 year');
                                        }

                                        $interval = $currentDate->diff($birthdayThisYear);


                                        $daysRemaining = $interval->days;
                                    ?>
                                        <div class="staff-profile bg-sky-100 p-4 rounded-[40px] shadow-lg hover:shadow-xl border border-solid border-white hover:border-yellow-400 transition-all flex justify-between items-center">

                                        <?= staff_profile_image($staff['staffid'], ['border-4 border-gradient-to-r from-teal-400 to-blue-500 object-cover w-20 h-20 rounded-full staff-profile-image-thumb mr-4'], 'thumb'); ?>
                                        <div class="staff-details flex-grow flex flex-col">
                                            <span class="staff-name text-xl font-semibold text-gray-800 my-2"><?= $staff['full_name'] ?></span>
                                            <span class="days-left text-gray-700 font-semibold">Remaining Days: <span class="text-blue-600"><?= $daysRemaining ?> days</span></span>
                                        </div>
                                        <span class="staff-dob text-gray-600 font-light"><?= $formattedDob ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
            </div>

            <!-- Calendar/Todo -->
            <div class="col-md-12 mt-7">

                <div class="flex flex-row w-full gap-10 rounded-lg">

                    <!-- Calendar Section -->
                    <div class="w-2/3 rounded-[50px] bg-white p-4 shadow-lg hover:shadow-xl border border-solid border-white hover:border-yellow-400 transition-all">

                        <div class="p-4 ">

                                <div class="dt-loader hide"></div>
                                <?php $this->load->view('admin/utilities/calendar_filters'); ?>
                                <div id="calendar"></div>

                        </div>
                    </div>

                    <!-- To do Section -->
                    <div class="w-1/3 flex md:flex-row flex-col rounded-[50px] bg-white p-4 shadow-lg hover:shadow-xl border border-solid border-white hover:border-yellow-400 transition-all">

                            <div class="panel_s todo-panel h-full p-5 w-full shadow-inner rounded-[50px]">
                                <div class="tw-flex tw-justify-between tw-items-center">
                                    <p class="tw-font-medium tw-flex tw-items-center tw-mb-0 tw-space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="tw-w-6 tw-h-6 tw-text-neutral-500">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                        </svg>
                                        <span class="tw-text-neutral-700">
                                            <?php echo _l('home_my_todo_items'); ?>
                                        </span>
                                    </p>
                                    <div class="tw-divide-x tw-divide-solid tw-divide-neutral-300 tw-space-x-2">
                                        <a href="<?php echo admin_url('todo'); ?>" class="tw-text-sm tw-mb-0">
                                            <?php echo _l('home_widget_view_all'); ?>
                                        </a>
                                        <a href="#__todo" data-toggle="modal" class="tw-text-sm tw-mb-0 tw-pl-2">
                                            <?php echo _l('new_todo'); ?>
                                        </a>
                                    </div>
                                </div>

                                <hr class="tw-mt-2 tw-mb-4">

                                <?php $total_todos = count($todos); ?>
                                <h4 class="todo-title text-warning tw-text-lg -tw-mt-2">
                                    <i class="fa fa-warning"></i>
                                    <?php echo _l('home_latest_todos'); ?>
                                </h4>
                                <ul class="list-unstyled todo unfinished-todos todos-sortable sortable">
                                    <?php foreach ($todos as $todo) { ?>
                                    <li>
                                        <?php echo form_hidden('todo_order', $todo['item_order']); ?>
                                        <?php echo form_hidden('finished', 0); ?>
                                        <div class="media tw-mt-2">
                                            <div class="media-left no-padding-right">
                                                <div class="dragger todo-dragger"></div>
                                                <div class="checkbox checkbox-default todo-checkbox">
                                                    <input type="checkbox" name="todo_id" value="<?php echo $todo['todoid']; ?>">
                                                    <label></label>
                                                </div>
                                            </div>
                                            <div class="media-body">
                                                <p class="todo-description read-more no-padding-left"
                                                    data-todo-description="<?php echo $todo['todoid']; ?>">
                                                    <?php echo $todo['description']; ?>
                                                </p>
                                                <a href="#" onclick="delete_todo_item(this,<?php echo $todo['todoid']; ?>); return false;"
                                                    class="pull-right text-muted">
                                                    <i class="fa fa-remove"></i>
                                                </a>
                                                <a href="#" onclick="edit_todo_item(<?php echo $todo['todoid']; ?>); return false;"
                                                    class="pull-right text-muted mright5">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <span class="todo-date tw-text-sm tw-text-neutral-500">
                                                    <?php echo $todo['dateadded']; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                    <?php } ?>
                                    <li class="padding no-todos ui-state-disabled <?php if ($total_todos > 0) {echo 'hide';} ?>"><?php echo _l('home_no_latest_todos'); ?></li>
                                </ul>
                                <?php $total_finished_todos = count($todos_finished); ?>
                                <h4 class="todo-title text-success tw-mt-4 tw-text-lg tw-mb-2">
                                    <i class="fa fa-check"></i>
                                    <?php echo _l('home_latest_finished_todos'); ?>
                                </h4>
                                <ul class="list-unstyled todo finished-todos todos-sortable sortable">
                                    <?php foreach ($todos_finished as $todo_finished) { ?>
                                    <li>
                                        <?php echo form_hidden('todo_order', $todo_finished['item_order']); ?>
                                        <?php echo form_hidden('finished', 1); ?>
                                        <div class="media tw-mt-2">
                                            <div class="media-left no-padding-right">
                                                <div class="dragger todo-dragger"></div>
                                                <div class="checkbox checkbox-default todo-checkbox">
                                                    <input type="checkbox" value="<?php echo $todo_finished['todoid']; ?>" name="todo_id"
                                                        checked>
                                                    <label></label>
                                                </div>
                                            </div>
                                            <div class="media-body">
                                                <p class="todo-description read-more line-throught no-padding-left">
                                                    <?php echo $todo_finished['description']; ?>
                                                </p>
                                                <a href="#"
                                                    onclick="delete_todo_item(this,<?php echo $todo_finished['todoid']; ?>); return false;"
                                                    class="pull-right text-muted"><i class="fa fa-remove"></i></a>
                                                <a href="#" onclick="edit_todo_item(<?php echo $todo_finished['todoid']; ?>); return false;"
                                                    class="pull-right text-muted mright5">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <span class="todo-date todo-date-finished tw-text-sm tw-text-neutral-500">
                                                    <?php echo $todo_finished['datefinished']; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                    <?php } ?>
                                    <li class="padding no-todos ui-state-disabled <?php if ($total_finished_todos > 0) {
                                        echo 'hide';
                                    } ?>"><?php echo _l('home_no_finished_todos_found'); ?></li>
                                </ul>
                                </div>
                            </div>
                            <?php $this->load->view('admin/todos/_todo.php'); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>



<!-- MODAL CODE -->
<div id="postModal" class="hidden fixed inset-0 flex items-center justify-center z-[1000]">
    <div class="bg-gray-800 bg-opacity-70 absolute inset-0"></div>

    <div class="relative bg-gray-900 p-8 max-w-2xl w-full mx-4 rounded-lg shadow-xl z-10 overflow-y-auto">  
        <div class="flex justify-between items-center mb-5">
            <div class="font-semibold text-2xl text-white" id="modalCreatorName"></div> 
            <div class="text-gray-400 text-sm italic" id="modalTime"></div>
        </div>
        
        <div class="text-gray-500 mb-5" id="modalDate"></div>
        
        <div class="text-md mb-3 text-gray-200 overflow-y-scroll max-h-[65vh] myscrollbar" id="modalContent"></div>
        
        <div class="flex justify-between items-center">
            <button id="likeButton" class="text-lg btnlike flex items-center mb-2 p-2 rounded-full focus:outline-none" data-postid="<?= $post["postid"] ?>" onclick="likes(this)">
                <i class="heartIcon fas fa-heart text-gray-500 mr-2"></i> 
                <span id="likeCount" class="ml-2 text-gray-500 text-md"></span>
            </button>
            
            
        </div>

        <button onclick="closeModal()" class="bg-red-500 hover:bg-red-700 text-white float-right font-semibold py-2 px-4 border border-red-600 hover:border-red-700 rounded transition ease-in-out duration-300">Close</button>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>


<script>
    // COMPLETE LIKE FUNCTION
function likes(buttonElement) {
    var postId = buttonElement.getAttribute('data-postid');
    var postElement = document.querySelector(`[data-postid="${postId}"]`);
    var heartIcon = buttonElement.querySelector('.heartIcon');
    var likeCountModal = document.querySelector('#likeCount');
    var likeCountPost = postElement.querySelector('.likes-count');

    $.ajax({
        type: "POST",
        url: "Dashboard/like_or_unlike",
        dataType: "json",
        data: { post_id: postId },
        success: function(response) {
            var totalLikes = parseInt(postElement.getAttribute('data-total-likes'));
            if (response.liked) {
                heartIcon.classList.remove('text-gray-500');
                heartIcon.classList.add('text-red-500');
                postElement.setAttribute('data-liked-by-user', 'true');
                totalLikes += 1;
            } else {
                heartIcon.classList.add('text-gray-500');
                heartIcon.classList.remove('text-red-500');
                postElement.setAttribute('data-liked-by-user', 'false');
                totalLikes -= 1;
            }
            postElement.setAttribute('data-total-likes', totalLikes);
            likeCountModal.innerText = `${totalLikes} ${totalLikes === 1 ? "like" : "likes"}`;
            likeCountPost.innerText = `${totalLikes} ${totalLikes === 1 ? "like" : "likes"}`;
        }
    });
}

// Modal Opening
function openPostModal(postElement) {
    var modal = document.getElementById('postModal');
    var postId = postElement.getAttribute("data-postid"); // <-- Fetch post ID from clicked element
    var likedByUser = postElement.getAttribute('data-liked-by-user') === "true";

    var creatorName = postElement.getAttribute('data-creator');
    var content = postElement.getAttribute('data-content');
    var timeString = postElement.querySelector('.text-sm.italic').innerText;
    var dateCreated = postElement.querySelector('.text-gray-500.mb-3').innerText;
    
    var likeButton = modal.querySelector('.btnlike');
    var heartIcon = likeButton.querySelector('.heartIcon');
    
    if (likedByUser) {
        heartIcon.classList.add('text-red-500');
        heartIcon.classList.remove('text-gray-500');
    } else {
        heartIcon.classList.remove('text-red-500');
        heartIcon.classList.add('text-gray-500');
    }
    likeButton.setAttribute('data-postid', postId);
    markPostAsSeen(postId);

    var totalLikes = postElement.getAttribute('data-total-likes');
    var likeCount = modal.querySelector('#likeCount');
    likeCount.innerText = `${totalLikes} ${totalLikes === "1" ? "like" : "likes"}`;

    document.getElementById('modalCreatorName').innerText = creatorName;
    document.getElementById('modalTime').innerText = timeString;
    document.getElementById('modalDate').innerText = dateCreated;
    document.getElementById('modalContent').innerHTML = content;

    document.getElementById('postModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('postModal').classList.add('hidden');
}

function markPostAsSeen(postId) {
    $.ajax({
        type: "POST",
        url: "dashboard/markPostAsSeen",
        dataType: 'json',
        data: { 
            postId: postId,
            <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        success: function(response) {
            if (response.status === 'success') {
                // Remove the not-seen class from the post when marked as seen
                $('[data-postid="' + postId + '"]').removeClass('not-seen');
            } else {
                console.error('Failed to mark post as seen.');
            }
        }
    });
}


</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>


<script>

    
function getCurrentTimeInAsiaKolkata() {
    const now = new Date();
    const timeZone = 'Asia/Kolkata';
    const localTimeString = now.toLocaleString('en-US', { timeZone });
  
    return new Date(localTimeString);
}
    
var shift_timings = <?php echo json_encode($shift_timings); ?>;
var afk_offline_entries = <?php echo json_encode($afk_offline_entries); ?>;
var clock_in_entries = <?php echo json_encode($clock_in_entries); ?>;


const today = new Date();
let startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 0, 0, 0); // Aaj ki date ka 12:00 AM
let endDate = new Date(today.getTime() + 24*60*60*1000); // Default: next day

var items = new vis.DataSet();
var options = {
    zoomMin: 1000 * 60 * 60, // one hour in milliseconds
    zoomMax: 1000 * 60 * 60 * 24 * 31, // 31 days in milliseconds
    height: "180px"
};

clock_in_entries.forEach(clock => {
    const inTime = new Date(clock.clock_in).toISOString();
    const outTime = new Date(clock.clock_out).toISOString();
    // Setting startDate and endDate based on clock-in and clock-out times
    if (new Date(inTime) < startDate) {
        startDate = new Date(inTime);
    }
    if (new Date(outTime) > endDate) {
        endDate = new Date(outTime);
    }
    items.add({
        content: 'Clock in',
        start: inTime,
        end: outTime,
        type: 'range',
        className: 'clock-in-time',
        group: 2
    });
});

for(let shiftKey in shift_timings) {
    let shift = shift_timings[shiftKey];

    if(!shift.start || !shift.end){
        continue;
    }
    
    let shiftStart = new Date(`${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()} ${shift.start}`).toISOString();
    let shiftEnd = new Date(`${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()} ${shift.end}`).toISOString();

    // If shift ends before it starts, add one day to the end date
    if(shift.end < shift.start) {
        let endDateTime = new Date(shiftEnd);
        endDateTime.setDate(endDateTime.getDate() + 1);
        shiftEnd = endDateTime.toISOString();
    }

    items.add({
        content: 'Shift',
        start: shiftStart,
        end: shiftEnd,
        type: 'range',
        className: 'shift-time',
        group: 3  // Group 3 for shifts. You can adjust as needed.
    });
}

afk_offline_entries.forEach(function (entry) {
  const start24HourTime = entry.start_time;
  const end24HourTime = entry.end_time;
  const startDateTime = new Date(`${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()} ${start24HourTime}`).toISOString();;
  const endDateTime = new Date(`${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()} ${end24HourTime}`).toISOString();;
  items.add({
    content: entry.status,
    start: startDateTime,
    end: endDateTime,
    type: 'range',
    className: 'afk-time',
    group: 1
  });
});

var container = document.getElementById('visualization');
var timeline = new vis.Timeline(container, items, options);

// Setting the timeline to focus on our startDate to endDate
timeline.setWindow(startDate, endDate);
timeline.setCurrentTime(getCurrentTimeInAsiaKolkata());
</script>

<?php init_tail(); ?>
<?php $this->load->view('admin/utilities/calendar_template'); ?>
<?php $this->load->view('admin/dashboard/dashboard_js'); ?>
</body>
</html>
