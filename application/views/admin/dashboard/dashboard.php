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
    border-color: rgba(255, 105, 180, 0.2)!important;
}

.liked-bg-color {
    background-color: red !important;
}
</style>

<div id="wrapper">
    <div class="screen-options-area"></div>
        <div class="screen-options-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="tw-w-5 tw-h-5 tw-mr-1">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>

            <?php echo _l('dashboard_options'); ?>
        </div>
    <div class="content">
        <div class="row">
                <?php $this->load->view('admin/includes/alerts'); ?>

                <?php hooks()->do_action('before_start_render_dashboard_content'); ?>

                <div class="clearfix"></div> 
                
                <div class="col-md-12" data-container="middle-left-6">
                    <?php $this->load->view('admin/management/dashboard_widget' ); ?>
                    <?php render_dashboard_widgets('middle-left-6'); ?>
                </div>
               
                <div class="col-md-12" data-container="middle-left-6"> 
                    <div class="flex  gap-x-4">
                        <div class="bg-white shadow rounded-lg p-4 my-4 flex md:flex-row flex-col justify-between h-full w-3/4">
                            <div id="visualization"  class="relative w-full " >
                            </div>
                        </div>
                        <div class="myscrollbar  bg-white shadow rounded-lg p-6 my-4 w-1/4 h-[145px] mx-auto  overflow-y-auto">
                            <h5 class="attendance text-xl font-semibold mb-2 text-center text-gray-700 border-b pb-2">Attendance Overview</h1>
                            <div class="mt-4 flex flex-col space-y-3">

                                <!-- Attendance Status -->
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-md">
                                    <span class="text-gray-600">Attendance:</span>
                                    <span class="text-green-500 font-bold">Present</span>
                                    <!-- You can change the color to red for 'Absent' and yellow for 'Late' -->
                                </div>

                                <!-- Punctuality Rate -->
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-md">
                                    <span class="text-gray-600">Punctuality Rate:</span>
                                    <span class="font-semibold">95%</span>
                                </div>

                                <!-- Current Month -->
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-md">
                                    <span class="text-gray-600">Current Month:</span>
                                    <span class="font-semibold">October</span>
                                    <!-- Replace 'October' with the current month -->
                                </div>

                            </div>
                        </div>
                    </div>
                </div>



                <!--Tasks/ Announcement -->
                <div class="col-md-12" data-container="middle-left-6">
                    <div class="flex lg:flex-row flex-col justify-between w-full bg-white rounded-xl overflow-hidden">
                        <!-- Assigned Tasks -->
                        <div class="lg:w-1/2 w-full transition-all hover:shadow-sm rounded overflow-hidden p-8 xl:pr-10 md:pl-10">
                            <div class="uppercase tracking-wide text-xl text-center text-gray-700 font-bold">Assigned Task</div>
                            
                            <div class="flex flex-col h-full">
                                <div class="myscrollbar p-1 flex flex-col mt-4 gap-5 h-[520px] overflow-y-auto ">         
                                    <div class="space-y-4 ">
                                        <?php
                                        $tasks = $this->team_management_model->get_tasks_by_staff_member($GLOBALS['current_user']->staffid);
                                        $total_tasks = 0;
                                        $completed_tasks = 0;

                                        $current_date = date('Y-m-d'); 

                                        foreach ($tasks as $task) {
                                            $current_time = time();
                                            $start_time = strtotime($task->startdate);
                                            $due_time = strtotime($task->duedate ? $task->duedate : $task->startdate);

                                            if ($task->status == 5 && $due_time < $current_time) {
                                                continue; 
                                            }

                                            $total_tasks++;

                                            if ($task->status == 5) {
                                                $badgeColor = 'bg-green-500 text-white';
                                                $taskStatusText = 'Completed';
                                            } elseif ($start_time > $current_time) {
                                                $badgeColor = 'bg-blue-500 text-white';
                                                $taskStatusText = 'Not Started';
                                            } elseif ($start_time == strtotime($current_date) && $due_time == strtotime($current_date)) {
                                                $badgeColor = 'bg-yellow-400 text-black';
                                                $taskStatusText = 'In Progress';
                                            } elseif ($due_time < $current_time && $task->status != 5) {
                                                $badgeColor = 'bg-red-500 text-white';
                                                $taskStatusText = 'Not Completed';
                                            } elseif ($start_time <= $current_time && $due_time >= $current_time) {
                                                $badgeColor = 'bg-yellow-400 text-black';
                                                $taskStatusText = 'In Progress';
                                            }
                                            echo '
                                            <div class="flex justify-between items-center p-4 bg-gray-100 rounded-xl shadow hover:shadow-md transition-all duration-500">
                                                <a onclick="init_task_modal(' . $task->id . '); return false" href="#" class="font-semibold text-gray-800">' . $task->name . '</a>
                                                <span class="px-4 py-1 rounded-full text-sm font-medium ' . $badgeColor . ' whitespace-nowrap">' . $taskStatusText . '</span>
                                            </div>
                                            ';

                                            if ($task->status == 5) {
                                                $completed_tasks++;
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Newsfeed Panel Section -->
                        <div class="lg:w-1/2 w-full bg-lightgray-200 border-l border-gray-200 flex flex-col px-5 py-8">
                            <div class="panel-body p-0 m-0">
                                <div class="uppercase tracking-wide text-xl text-center text-gray-700 font-bold mb-5 ">Announcements</div>
                                <div class="bg-gray-100 rounded-lg p-4 py-3 shadow-inner">
                                        
                                    <?php $count = 0;
                                    foreach($posts as $post):
                                        if($count >= 3) break; 
                                        $isLiked = $this->newsfeed_model->user_liked_post($post["postid"]) ? "true" : "false";
                                        $totalLikes = count($this->newsfeed_model->get_post_likes($post["postid"]));
                                        $currentDateTime = new DateTime();
                                        $postDateTime = new DateTime($post["datecreated"]);
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
                                        <div data-postid="<?= $post["postid"] ?>" data-total-likes="<?= $totalLikes ?>"  data-liked-by-user="<?= $isLiked ?>"  class="dashboard-posts bg-white rounded-xl m-4 p-4 pb-2 cursor-pointer hover:shadow-md transition" data-creator="<?= $post["creator_name"] ?>" data-content="<?= htmlentities($post["content"]) ?>" onclick="openPostModal(this)">
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
                                    ?>
                                </div>    
                            </div>    
                        </div>    
                    </div>
                </div>


                <!-- Summary -->
                <div class="col-md-12"  data-container="middle-left-6">
                    <div class="flex lg:flex-row flex-col justify-between w-full bg-white shadow-md rounded-lg mt-5">
                        <div class="w-full transition-all hover:shadow-sm rounded overflow-hidden p-8 xl:pr-10 md:pl-10">
                            <!-- Row for Summary heading and Date Picker -->
                            <div class="flex justify-between items-center mb-4">
                                <div class="uppercase tracking-wide text-xl text-gray-700 font-bold text-center w-full">Summary</div>
                                <input type="date" id="summary_date" class="rounded p-2 mr-4" onchange="getOrSaveStaffSummary();">
                            </div>

                            <div class="flex mb-4">
                                <!-- Left Box with dummy summary -->
                                <div class="flex-grow p-4 h-[150px] border bg-gray-100/20">
                                    <!-- <h4><b>DUMMY SUMMARY </b></h4> -->
                                    <textarea class="w-full h-full shadow-inner p-3 bg-gray-100/20 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none overflow-y-hidden" readonly >DUMMY SUMMARY: Today I worked on the QCA Newsletter Design, followed up with the team for the October 2023 plan for recurring marketing projects, and connected with Ansar to discuss the new scrum workflow.</textarea>
                                </div>
                                
                                
                                <!-- Right Box for writing summary -->
                                <div class="flex-grow p-4 border">
                                    <textarea id="summary-textarea" class="w-full h-full p-3 bg-gray-100/20 border border-gray-300 shadow-inner rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none" placeholder="Write your summary here..."></textarea>
                                </div>
                            </div>

                            <div class="px-4 pb-4">
                                <button onclick="getOrSaveStaffSummary(document.getElementById('summary-textarea').value)" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Countries Clocks -->
                <div class="col-md-12" data-container="middle-left-6">
                    <div class="bg-white rounded-lg shadow-md p-4 my-4 flex md:flex-row flex-col justify-between">
                        <!-- Clocks Container -->
                        <div id="clocks" class="p-4 rounded-lg text-base md:text-lg grid grid-cols-1 md:grid-cols-2 gap-4 w-full"></div>
                    </div>
                </div> 

                <!-- Upcoming Birthdays -->
                <div class="col-md-12" data-container="bottom-right-4">
                    <?php if(isset($upcoming_birthdays) && !empty($upcoming_birthdays)): ?>
                        <div class="upcoming-birthdays bg-white p-6 rounded-lg ">
                            <h3 class="text-2xl mb-5 text-center font-bold text-gray-700">Upcoming Birthdays</h3>
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
                                        $daysRemaining = $interval->d;
                                    ?>
                                        <div class="staff-profile bg-gray-100 p-4 rounded-lg hover:shadow-xl transition-shadow duration-300 shadow-md flex justify-between items-center">
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

                <!-- Calendar/ Todo -->
            <div div class="col-md-12 bg-grey" data-container="middle-left-6">
                <div class="flex gap-x-4">
                    <div class="w-2/3 bg-white rounded shadow-lg p-4 my-4 flex md:flex-row flex-col justify-between" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('calendar'); ?>">
                        <div class="clearfix"></div>
                            <div class="panel_s">
                                <div class="p-2">
                                    <div class="dt-loader hide"></div>
                                    <?php $this->load->view('admin/utilities/calendar_filters'); ?>
                                    <div id="calendar"></div>
                                </div>
                            </div>
                        <div class="clearfix"></div>
                    </div>
                            
                    <div class="w-1/3 bg-white rounded shadow-lg p-4 my-4 flex md:flex-row flex-col justify-between h-[650px]" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('home_my_todo_items'); ?>">
                            <div class="panel_s todo-panel h-full p-3 w-full shadow-inner">
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
        </div>
    </div>
</div>

<!-- MODAL CODE -->
<div id="postModal" class="hidden fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-gray-800 bg-opacity-70 absolute inset-0"></div>

    <div class="relative bg-gray-900 p-8 max-w-2xl w-full mx-4 rounded-lg shadow-xl z-10 overflow-y-auto">  
        <div class="flex justify-between items-center mb-5">
            <div class="font-semibold text-2xl text-white" id="modalCreatorName"></div> 
            <div class="text-gray-400 text-sm italic" id="modalTime"></div>
        </div>
        
        <div class="text-gray-500 mb-5" id="modalDate"></div>
        
        <div class="text-md mb-3 text-gray-200" id="modalContent"></div>
        
        <button id="likeButton" class="text-lg btnlike flex items-center mb-2 p-2 rounded-full focus:outline-none" data-postid="<?= $post["postid"] ?>" onclick="likes(this)">
            <i class="heartIcon fas fa-heart text-gray-500 mr-2"></i> 
            <span id="likeCount" class="ml-2 text-gray-500"></span>
        </button>

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

    var totalLikes = postElement.getAttribute('data-total-likes');
    var likeCount = modal.querySelector('#likeCount');
    likeCount.innerText = `${totalLikes} ${totalLikes === "1" ? "like" : "likes"}`;

    document.getElementById('modalCreatorName').innerText = creatorName;
    document.getElementById('modalTime').innerText = timeString;
    document.getElementById('modalDate').innerText = dateCreated;
    document.getElementById('modalContent').innerText = content;

    document.getElementById('postModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('postModal').classList.add('hidden');
}

</script>
<!-- 
<script>
    var dailyStats = <?php echo json_encode($daily_stats); ?>;
    


    function fetchDailyInfos(staff_id) {
    let data = dailyStats;
   
    console.log(data);
    console.log('Total clocked-in time:', data.total_clocked_in_time);
    console.log('Total shift duration:', data.total_shift_duration);
    
    
    const afk_entries = data.afk_and_offline.filter(entry => entry.status === 'AFK');
    // const offline_entries = data.afk_and_offline.filter(entry => entry.status === 'Offline');

    
   
  var items = new vis.DataSet();
// var options = {
//   zoomMin: 1000 * 60 * 60,  // One hour in milliseconds
//   zoomMax: 1000 * 60 * 60 * 24  // One day in milliseconds
// };
var options = {
  zoomMin: 1000 * 60 * 60,  // One hour in milliseconds
  zoomMax: 1000 * 60 * 60 * 24,  // One day in milliseconds
  format: {
    minorLabels: function(date, scale, step) {
      return moment(date).format('hh:mm A'); // Time in AM/PM
    },
    majorLabels: function(date, scale, step) {
      return moment(date).format('MMM DD YYYY'); // Date in a good looking format
    }
  }
};
var container = document.getElementById('visualization');
if (container) {
  var timeline = new vis.Timeline(container, items, options);
} else {
  console.error("Timeline container not found");
  return;
}

// Debug
console.log("Data:", data);
console.log("AFK Entries:", afk_entries);
// console.log("Start Date:", startDate);
console.log("End Date:", endDate);
console.log("Items:", items);

if (data.clock_ins_outs) {
    const visJsData = [];
    
    data.clock_ins_outs.forEach(clock => {
        const inTime = new Date(clock.clock_in).toISOString();
        const outTime = new Date(clock.clock_out).toISOString();
        
        const item = {
            content:' Clock in',
            start: inTime,
            end: outTime,
            type: 'range',
            className: 'clock-in-time',
            group: 2 // Group 2 will contain all clock-ins
        };

        console.log("Item:", item);
        visJsData.push(item);
    });

    console.log("VisJs Data:", visJsData);
    
    // Add to timeline
    items.add(visJsData);
}



if (afk_entries) {
  afk_entries.forEach(function (entry) {
    // Convert '08:44 PM' format to ISO 8601
    const today = new Date();
    const startDateString = `${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()} ${entry.start_time}`;
    const endDateString = `${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()} ${entry.end_time}`;
    const startDate = new Date(startDateString).toISOString();
    const endDate = new Date(endDateString).toISOString();

    items.add({
      content: 'AFK',
      start: startDate,
      end: endDate,
      type: 'range',
      className: 'afk-time',
      group:1
    });
  });
} 
else {
  console.warn("afk_entries is not available");
}
// Setting the focus
if (startDate && endDate) {
  timeline.setWindow(startDate, endDate);
} else {
  console.warn("Start and end dates not properly set");
}

}
</script> -->



<script>
    var dailyStats = <?php echo json_encode($daily_stats); ?>;

function fetchDailyInfos(staff_id) {
    let data = dailyStats;

    const today = new Date();
    let startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 0, 0, 0); // Aaj ki date ka 12:00 AM
    let endDate = new Date(today.getTime() + 24*60*60*1000); // Default: next day
    console.log(data);
    // AFK entries filter
    const afk_entries = data.afk_and_offline.filter(entry => entry.status === 'AFK');

    var items = new vis.DataSet();
    var options = {
      zoomMin: 1000 * 60 * 60,
      zoomMax: 1000 * 60 * 60 * 24,

      format: {
        minorLabels: function(date, scale, step) {
          return moment(date).format('hh:mm A');
        },
        majorLabels: function(date, scale, step) {
          return moment(date).format('MMM DD YYYY');
        }
      }
    };
    var container = document.getElementById('visualization');
    if (container) {
      var timeline = new vis.Timeline(container, items, options);

    } else {
      console.error("Timeline container not found");
      return;
    }

    // Clock-in aur Clock-out times ko timeline mein add karte hain
    if (data.clock_ins_outs) {
        data.clock_ins_outs.forEach(clock => {
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
    }
    if (data.shift_timings && data.shift_timings.length > 0) {
    data.shift_timings.forEach(shift => {
        const shiftStart = new Date(`${shift.year}-${shift.month}-${shift.day} ${shift.shift_start}`);
        const shiftEnd = new Date(`${shift.year}-${shift.month}-${shift.day} ${shift.shift_end}`);

        items.add({
            content: 'Shift',
            start: shiftStart,
            end: shiftEnd,
            type: 'range',
            className: 'shift-time',
            group: 3  // Group 3 for shifts. You can adjust as needed.
        });

        // Setting startDate and endDate based on shift timings
        if (shiftStart < startDate) {
            startDate = shiftStart;
        }
        if (shiftEnd > endDate) {
            endDate = shiftEnd;
        }
    });
}

    // AFK timings ko timeline mein add karte hain
    if (afk_entries) {
      afk_entries.forEach(function (entry) {
        const startDateTime = moment(`${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()} ${entry.start_time}`, "YYYY-MM-DD hh:mm A").toDate();
        const endDateTime = moment(`${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()} ${entry.end_time}`, "YYYY-MM-DD hh:mm A").toDate();

        items.add({
          content: 'AFK',
          start: startDateTime,
          end: endDateTime,
          type: 'range',
          className: 'afk-time',
          group: 1
        });
      });
    } else {
      console.warn("afk_entries is not available");
    }

    // Setting the timeline to focus on our startDate to endDate
    timeline.setWindow(startDate, endDate);
}


</script>
<?php init_tail(); ?>
<?php $this->load->view('admin/utilities/calendar_template'); ?>
<?php $this->load->view('admin/dashboard/dashboard_js'); ?>
</body>
</html>
