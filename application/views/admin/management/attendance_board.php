<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); 
?>
<style>
    .collapsible-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s;
    }
</style>
<div id="wrapper" class="wrapper">
    <div class="content flex flex-col gap-8">

        <div class="w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-<?= get_option('management_theme_border')?> transition-all">
            <div class="flex lg:flex-row flex-col gap-4 items-stretch justify-between">
                <!-- User Information Section -->
                <div class="flex flex-col lg:w-1/3 w-full bg-<?= get_option('management_theme_background')?> p-4 py-3 shadow-inner rounded-[50px]  max-h-[300px] min-h-full">
                    <div class="h-full flex justify-center items-center">
                        <h2 class="text-3xl font-bold text-center text-gray-700">Attendance Board</h2>
                    </div>
                </div>
                <div class="flex flex-col lg:w-1/3 w-full bg-<?= get_option('management_theme_background')?> p-4 py-3 shadow-inner rounded-[50px]  max-h-[300px]">
                    <!-- Input boxes for FROM and TO -->
                    <div class="flex flex-col gap-2 my-2 mx-3 w-90">

                        <input type="date" id="from" class="w-full py-2 px-4 border !rounded-3xl text-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="From" value="<?= $from ?>">

                        <input type="date" id="to" class="w-full py-2 px-4 border !rounded-3xl text-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="To" value="<?= $to ?>">

                        <select data-width="100%" id="staff" data-live-search="true" class="selectpicker text-2xl font-bold text-uppercase" multiple>
                        <?php 
                            $current_staff_id = get_staff_user_id();

                            if (has_permission('team_management', '', 'admin')) {
                                $staff_members = $this->staff_model->get();
                            } else {
                                $subordinate_ids = get_staff_under($current_staff_id);
                                
                                if (!empty($subordinate_ids)) {
                                    array_push($subordinate_ids, $current_staff_id);

                                    $staff_members = [];
                                    foreach ($subordinate_ids as $id) {
                                        $staff_member = $this->staff_model->get($id);
                                        // Check if the result is an object, if so convert to array
                                        if (is_object($staff_member)) {
                                            $staff_member = (array) $staff_member;
                                        }
                                        $staff_members[] = $staff_member;
                                    }
                                } else {
                                    $staff_member = $this->staff_model->get($current_staff_id);
                                    if (is_object($staff_member)) {
                                        $staff_member = (array) $staff_member;
                                    }
                                    $staff_members = [$staff_member];
                                }
                            }

                            foreach($staff_members as $staff_member) {
                                $selected = '';
                                if (isset($exclude_ids) && in_array($staff_member['staffid'], $exclude_ids)) {
                                    $selected = 'selected';
                                }
                                echo '<option '.$selected.' value="'.$staff_member['staffid'].'">'.$staff_member['full_name'].'</option>';
                            }
                        ?>
                    </select>



                        <button class="px-4 py-2 bg-<?= get_option('management_theme_background')?> border border-blue-600 rounded-[50px] text-blue-600 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 hover:text-white focus:ring-opacity-50 transition-all duration-300 mt-2" onclick="redirectToAttendanceBoard()">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        </button>
                    </div>

                        
                </div>
            </div>
        </div>
            
        <div>
            <div class="flex flex-row justify-between items-center gap-4 px-4 py-2 cursor-pointer text-lg text-gray-600 transition-all bg-white rounded-[40px]" onclick="toggleCollapse(this, event, 'staff-cards')">
                <div class="opacity-0 transform transition-transform duration-300"></div>
                <div>Staff Cards</div>
                <div class="fas fa-angle-down rotate-[90deg] transform transition-transform duration-300"></div>
            </div>

            <div class="w-full bg-white rounded-[50px] shadow-lg hover:shadow-xl border border-solid border-white hover:border-<?= get_option('management_theme_border')?> transition-all flex flex-col collapsible-content" id="staff-cards">
                <div class="p-6">
                


                    <!-- Select box for sorting -->
                    <div class="w-full mb-6">
                        <label for="sortSelect" class="mr-2">Sort by:</label>
                        <select id="sortSelect" class="border rounded-md p-2 w-64">
                            <option value="name">Name</option>
                            <option value="ar">Attendance Rate</option>
                            <option value="pr">Punctuality Rate</option>
                            <option value="ct">Clockable Time</option>
                            <option value="cdt">Clocked Time</option>
                        </select>
                    </div>
                    
                    <?php foreach($departments as $department): ?>
                        <div class="department-block mb-8">
                            <h2 class="text-lg uppercase font-bold text-gray-800 text-center py-4 rounded-[40px] transition-all bg-gray-100 mb-2">
                                <?= $department->name; ?>
                            </h2>
                            <div class="grid grid-cols-3 gap-4 department-block-grid">
                                <?php $staff_members = $this->team_management_model->get_staff_by_department($department->departmentid); ?>
                                <?php foreach($staff_members as $staff_member): ?>
                                    <?php $staff = $staff_dates_data[$staff_member->staffid]; ?>

                                <div class="flex flex-col justify-center items-center gap-2 shadow-inner bg-<?= get_option('management_theme_foreground')?> rounded-[40px] min-h-[140px] shadow-inner hover:shadow-xl shadow-none transition-all staff-box p-4" data-name="<?= $staff['name'] ?>" data-ar="<?= $staff['ar'] ?>" data-pr="<?= $staff['pr'] ?>" data-ct="<?= $staff['ct'] ?>" data-cdt="<?= $staff['cdt'] ?>" data-departments="<?= htmlspecialchars($staff['department']) ?>">
                                    <h3 class="text-xl text-center font-bold"><?= $staff['name'] ?></h3>
                                    <hr class="bg-gray-700 text-gray-800 h-[1px] border-none w-full mb-1" />
                                    <div class="xl:text-xl lg:text-lg text-base grid lg:grid-cols-2 grid-cols-1 gap-4 place-items-centers">
                                        <h4 class="text-center font-bold border-gray-700">AR: <?= round($staff['ar'],2) ?>%</h4>
                                        <h4 class="text-center font-bold border-gray-700">PR: <?= round($staff['pr'],2) ?>%</h4>
                                        <h4 class="text-center font-bold border-gray-700 ">Clockable: <?= convertSecondsToRoundedTime($staff['ct']) ?></h4>
                                        <h4 class="text-center font-bold border-gray-700 ">Clocked: <?= convertSecondsToRoundedTime($staff['cdt']) ?></h4>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>    
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>


            </div>
        </div>

        <div class="w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-<?= get_option('management_theme_border')?> transition-all overflow-x-auto xl:cursor-auto cursor-move">

            <div class="flex flex-col xl:min-w-full min-w-[900px] ">

                <div class="w-full transition-all ease-in-out rounded-[40px] bg-<?= get_option('management_theme_foreground')?> font-bold flex flex-row xl:text-xl lg:text-md md:text-md overflow-hidden sticky top-4 z-10 mb-12">

                    <div class="w-[20%] hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 text-center flex justify-center items-center">
                        Staff
                    </div>

                    <div class="w-[30%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all  flex flex-col">
                        <div class="border-b border-solid border-gray-600 py-1">
                            Shift 1
                        </div>
                        <div class="transition-all text-center flex flex-row py-1">
                            <div class="w-1/2">Allocated</div>
                            <div class="w-1/2 border-l border-solid border-gray-600">Actual</div>
                        </div>
                    </div>

                    <div class="w-[30%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all  flex flex-col">
                        <div class="border-b border-solid border-gray-600 py-1">
                            Shift 2
                        </div>
                        <div class="transition-all text-center flex flex-row py-1">
                            <div class="w-1/2">Allocated</div>
                            <div class="w-1/2 border-l border-solid border-gray-600">Actual</div>
                        </div>
                    </div>

                    <div class="w-[10%] border-l border-solid border-gray-600 hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 text-center flex justify-center items-center">
                        Attendance
                    </div>

                    <div class="w-[10%] border-l border-solid border-gray-600 hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 text-center flex justify-center items-center">
                        Shift
                    </div>
                    
                </div>

                <?php 

                foreach ($dates as $date => $data): 
                                    
                    ?>

                    <div class="flex flex-row justify-between items-center gap-4 px-4 py-2 cursor-pointer text-lg text-gray-600 transition-all bg-gray-100 rounded-[40px]" data-date="<?= $date ?>" onclick="toggleCollapse(this, event, '<?= $date ?>')">
                        <div class="opacity-0 transform transition-transform duration-300"></div>
                        <div><?= date("jS F, l", strtotime($date)) ?></div>
                        <div class="fas fa-angle-down rotate-[90deg] transform transition-transform duration-300"></div>
                    </div>

                    <div class="collapsible-content w-full transition-all ease-in-out rounded-[40px] border border-solid border-white bg-<?= get_option('management_theme_background')?> shadow-inner overflow-hidden mb-5 xl:text-base text-sm" id="<?= $date ?>">
                    <?php
                            $totals = null;
                            $staffByDepartment = [];

                            // Organize the staff data by department
                            foreach ($data as $index => $staff) {
                                if ($index == 'totals') {
                                    $totals = $staff;
                                    continue;
                                }
                                $staffByDepartment[$staff['department_id']][] = $staff;
                            }

                            // Loop through the departments
                            foreach ($departments as $department) :
                                // Check if there are staff members in this department
                                $departmentStaff = $staffByDepartment[$department->departmentid] ?? [];
                                
                                // If there are no staff members, continue to the next department
                                if (empty($departmentStaff)) {
                                    continue;
                                }

                                // If there are staff members, then show the department name
                                ?>
                                <div class="department-block border-y border-solid border-gray-600">
                                    <h2 class="text-lg uppercase font-bold text-gray-800 text-center py-2 rounded-[40px] transition-all">
                                        <?= $department->name; ?>
                                    </h2>
                                </div>
                                <?php 
                                // Loop through and display the staff members in this department
                                foreach ($departmentStaff as $staff):
                                ?>

                                <div class="flex flex-row transition-all hover:bg-sky-200/75staff-row" >

                                            <div class="w-[20%] hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 text-center flex justify-center items-center">
                                                <?= $staff['name'] ?>
                                            </div>

                                            <div class="w-[15%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 flex justify-center items-center">
                                                <?= !empty($staff['data']['shifts'][0]['shift_start_time']) 
                                                ? 
                                                $staff['data']['shifts'][0]['shift_start_time'] . ' - '. $staff['data']['shifts'][0]['shift_end_time']
                                                : '-'; ?>
                                            </div>

                                            <div class="w-[15%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all flex justify-center items-center">
                                                <?php 

                                                if(
                                                    isset($staff['data']['shifts'][0]['is_on_leave'])
                                                    &&
                                                    ($staff['data']['shifts'][0]['is_on_leave'])
                                                ){
                                                    $title = "Leave";
                                                    $bg = "yellow-200";
                                                }

                                                else if(
                                                    isset($staff['data']['shifts'][0]['status'])
                                                    &&
                                                    ($staff['data']['shifts'][0]['status'] == 'absent')
                                                ){
                                                    $title = "Absent";
                                                    $bg = "red-200";
                                                }

                                                else{
                                                    
                                                    
                                                    
                                                    if(
                                                        isset($staff['data']['shifts'][0]['status'])
                                                        &&
                                                        ($staff['data']['shifts'][0]['status'] == 'late')
                                                    ){
                                                        $title = "Late by " . round($staff['data']['shifts'][0]['difference'] / 60).'m';
                                                        $bg = "orange-200";
                                                    }

                                                    else if(
                                                        isset($staff['data']['shifts'][0]['is_early_departure'])
                                                        &&
                                                        ($staff['data']['shifts'][0]['is_early_departure'])
                                                    ){
                                                        $title = "Early Departure";
                                                        $bg = "pink-200";
                                                    }
                                                    else if(
                                                        isset($staff['data']['shifts'][0]['status'])
                                                        &&
                                                        ($staff['data']['shifts'][0]['status'] == 'present')
                                                    ){
                                                        $title = "On Time";
                                                        $bg = "green-200";
                                                    }else{
                                                        $title = "No Shift";
                                                        $bg = "gray-200";
                                                    }

                                                }

                                                ?>

                                                <button class="bg-<?=$bg?> rounded flex justify-center items-center w-[70%] h-[70%] py-2" title="<?= $title ?>" data-toggle="tooltip" data-placement="top">

                                                <?= 
                                                !empty($staff['data']['shifts'][0]['clock_in']) ? 
                                                $staff['data']['shifts'][0]['clock_in'] . ' - '. $staff['data']['shifts'][0]['clock_out']
                                                : '-'; 
                                                ?>

                                                </button>

                                            </div>

                                            <div class="w-[15%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 flex justify-center items-center">
                                                <?= !empty($staff['data']['shifts'][1]['shift_start_time']) 
                                                ? 
                                                $staff['data']['shifts'][1]['shift_start_time'] . ' - '. $staff['data']['shifts'][1]['shift_end_time']
                                                : '-'; ?>
                                            </div>

                                            <div class="w-[15%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all flex justify-center items-center">
                                                <?php 

                                                if(
                                                    isset($staff['data']['shifts'][1]['is_on_leave'])
                                                    &&
                                                    ($staff['data']['shifts'][1]['is_on_leave'])
                                                ){
                                                    $title = "Leave";
                                                    $bg = "yellow-200";
                                                }

                                                else if(
                                                    isset($staff['data']['shifts'][1]['status'])
                                                    &&
                                                    ($staff['data']['shifts'][1]['status'] == 'absent')
                                                ){
                                                    
                                                    $title = "Absent";
                                                    $bg = "red-200";
                                                }

                                                else{
                                                    
                                                    
                                                    if(
                                                        isset($staff['data']['shifts'][1]['status'])
                                                        &&
                                                        ($staff['data']['shifts'][1]['status'] == 'late')
                                                    ){
                                                        $title = "Late by " . round($staff['data']['shifts'][1]['difference'] / 60).'m';                                
                                                        $bg = "orange-200";
                                                    }

                                                    else if(
                                                        isset($staff['data']['shifts'][1]['is_early_departure'])
                                                        &&
                                                        ($staff['data']['shifts'][1]['is_early_departure'])
                                                    ){
                                                        $title = "Early Departure";
                                                        $bg = "pink-200";
                                                    }
                                                    else if(
                                                        isset($staff['data']['shifts'][1]['status'])
                                                        &&
                                                        ($staff['data']['shifts'][1]['status'] == 'present')
                                                    ){
                                                        $title = "On Time";
                                                        $bg = "green-200";
                                                    }else{
                                                        $title = "No Shift";
                                                        $bg = "gray-200";
                                                    }

                                                }
                            
                                                ?>

                                                <button class="bg-<?=$bg?> rounded flex justify-center items-center w-[70%] h-[70%] py-2" title="<?= $title ?>" data-toggle="tooltip" data-placement="top">

                                                <?= !empty($staff['data']['shifts'][1]['clock_in']) ? 
                                                $staff['data']['shifts'][1]['clock_in'] . ' - '. $staff['data']['shifts'][1]['clock_out']
                                                : '-'; ?>

                                                </button>

                                            </div>

                                            <div class="w-[10%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 flex justify-center items-center font-bold">
                                                <?php

                                                    $attendance = $staff['data']['status'];

                                                    if($attendance == 'absent')
                                                    {
                                                        $attendance = 'Absent';
                                                        $class = 'text-red-500 p-2 rounded';
                                                    }
                                                    else if($attendance == 'present')
                                                    {
                                                        $attendance = 'On Time';
                                                        $class = 'text-green-500 p-2 rounded';
                                                    }
                                                    else if($attendance == 'late')
                                                    {
                                                        $attendance = 'Late';
                                                        $class = 'text-orange-500 p-2 rounded';
                                                    }
                                                    else if($attendance == 'leave')
                                                    {
                                                        $attendance = 'Leave';
                                                        $class = 'text-yellow-500 p-2 rounded';
                                                    }
                                                    else if($attendance == 'no-shifts')
                                                    {
                                                        $attendance = 'No Shifts';
                                                        $class = 'text-gray-500 p-2 rounded';
                                                    }
                                                    
                                                    echo '<span class="'.$class.'">'.$attendance.'</span>';
                                                
                                                ?>
                                            </div>
                                            
                                            <div class="w-[10%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 flex justify-center items-center font-bold">
                                                <?php

                                                    $status = $staff['data']['status'];
                                                    if($status != "absent" && $status != "leave" && $status != "no-shifts"){
                                                        if($staff['data']['day_status'] == 0){
                                                            $completion = 'Incompleted';
                                                            $class = 'text-red-500';
                                                        }else if($staff['data']['day_status'] == 1){
                                                            $completion = 'Completed';
                                                            $class = 'text-green-500';
                                                        }else if($staff['data']['day_status'] == 2){
                                                            $completion = 'Overtime';
                                                            $class = 'text-lime-700';
                                                        }
                                                    }else{
                                                        $completion = '-';
                                                        $class = '';
                                                    }

                                                    echo '<span class="font-bold '.$class.'">'.$completion.'</span>';
                                                ?>
                                            </div>
                                            
                                </div>


                            <?php
                            endforeach; 
                        endforeach; 
                        
                        ?>

                        <div class="flex flex-row text-base transition-all hover:bg-sky-200/75 staff-row border-t border-solid border-gray-700 mt-4" >

                            <div class="w-[20%] hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 text-center flex justify-center items-center font-bold">
                                Totals
                            </div>

                            <div class="w-[15%] hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 text-center flex justify-center items-center font-bold border-l border-gray-700 border-solid">
                                <?= convertSecondsToRoundedTime($totals['clockable_shift_1']); ?>
                            </div>

                            <div class="w-[15%] hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 text-center flex justify-center items-center font-bold border-l border-gray-700 border-solid">
                                <?= convertSecondsToRoundedTime($totals['clocked_shift_1']); ?>
                            </div>

                            <div class="w-[15%] hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 text-center flex justify-center items-center font-bold border-l border-gray-700 border-solid">
                                <?= convertSecondsToRoundedTime($totals['clockable_shift_2']); ?>
                            </div>

                            <div class="w-[15%] hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 text-center flex justify-center items-center font-bold border-l border-gray-700 border-solid">
                                <?= convertSecondsToRoundedTime($totals['clocked_shift_2']); ?>
                            </div>

                            <div class="w-[20%] border-l border-solid border-gray-600 text-center flex flex-col font-bold">
                                <div class="border-b border-solid border-gray-600 py-1">
                                    Rates
                                </div>
                                <div class="transition-all text-center flex flex-row">
                                    <button data-toggle="tooltip" data-title="Punctuality Rate" class="py-1 w-1/2 hover:bg-<?= get_option('management_theme_hover')?> transition-all"><?= round($totals['pr'],2) ?>%</button>
                                    <button data-toggle="tooltip" data-title="Attenandance Rate" class="py-1 w-1/2 border-l border-solid border-gray-600 hover:bg-<?= get_option('management_theme_hover')?> transition-all"><?= round($totals['ar'],2) ?>%</button>
                                </div>
                            </div>

                        </div>

                    </div>


                <?php endforeach; ?>
                

                <div class="flex flex-row text-base transition-all bg-<?= get_option('management_theme_foreground')?> ease-in-out rounded-[40px] xl:text-xl lg:text-md md:text-md overflow-hidden sticky bottom-4 mt-4 z-10 " >

                    <div class="w-[20%] hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 text-center flex justify-center items-center font-bold">
                        Cumulative
                    </div>

                    <div class="w-[15%] hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 text-center flex justify-center items-center font-bold border-l border-gray-700 border-solid">
                        <?= convertSecondsToRoundedTime($totals['clockable_shift_1']); ?>
                    </div>

                    <div class="w-[15%] hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 text-center flex justify-center items-center font-bold border-l border-gray-700 border-solid">
                        <?= convertSecondsToRoundedTime($totals['clocked_shift_1']); ?>
                    </div>

                    <div class="w-[15%] hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 text-center flex justify-center items-center font-bold border-l border-gray-700 border-solid">
                        <?= convertSecondsToRoundedTime($totals['clockable_shift_2']); ?>
                    </div>

                    <div class="w-[15%] hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 text-center flex justify-center items-center font-bold border-l border-gray-700 border-solid">
                        <?= convertSecondsToRoundedTime($totals['clocked_shift_2']); ?>
                    </div>

                    <div class="w-[20%] border-l border-solid border-gray-600 text-center flex flex-col font-bold">
                        <div class="border-b border-solid border-gray-600 py-1">
                            Rates
                        </div>
                        <div class="transition-all text-center flex flex-row">
                            <button data-toggle="tooltip" data-title="Punctuality Rate" class="py-1 w-1/2 hover:bg-<?= get_option('management_theme_hover')?> transition-all"><?= round($totals['pr'],2) ?>%</button>
                            <button data-toggle="tooltip" data-title="Attenandance Rate" class="py-1 w-1/2 border-l border-solid border-gray-600 hover:bg-<?= get_option('management_theme_hover')?> transition-all"><?= round($totals['ar'],2) ?>%</button>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>


<?php init_tail(); ?>

<script>
function redirectToAttendanceBoard() {
    // Get the selected values from the #staff select element
    var staffSelect = document.getElementById('staff');
    var selectedOptions = [];
    for (var i = 0; i < staffSelect.options.length; i++) {
        if (staffSelect.options[i].selected) {
            selectedOptions.push(staffSelect.options[i].value);
        }
    }

    // Convert the selected values to a comma-separated string
    var staffValues = selectedOptions.join('e');

    // Get the values of 'from' and 'to' elements
    var fromValue = document.getElementById('from').value;
    var toValue = document.getElementById('to').value;

    // Construct the URL with the selected staff values
    var url = admin_url + 'team_management/attendance_board/' + fromValue + '/' + toValue + '/' + staffValues;

    // Redirect to the constructed URL
    window.location.href = url;
}

function toggleCollapse(button, event, elementId) {
    if (event.target.tagName.toLowerCase() === 'button' || event.target.tagName.toLowerCase() === 'input') {
        return;
    }

    var content = document.getElementById(elementId);
    var arrow = button.querySelector('.fa-angle-down');

    var isExpanding = !content.classList.contains("expanded");
    content.classList.toggle("expanded");
    updateMaxHeight(content, isExpanding);
    arrow.classList.toggle('rotate-[90deg]');
    button.classList.toggle('py-3');
    button.classList.toggle('bg-amber-200/75');
    
}


function updateMaxHeight(collapsibleContent, isExpanding) {
    if (isExpanding) {
        // Set max-height to scrollHeight when expanding
        var maxHeight = (collapsibleContent.scrollHeight + 40) + 'px';
        collapsibleContent.style.maxHeight = maxHeight;
    } else {
        // Reset max-height to 0 when collapsing
        collapsibleContent.style.maxHeight = '0';
    }
}
$(document).ready(function() {
    $('#sortSelect').on('change', function() {
        var sortBy = $(this).val();

        // Loop over each department block
        $('.department-block-grid').each(function() {
            // var $departmentBlock = $(this); // Current department block
            var staffBoxes = $(this).find('.staff-box').sort(function(a, b) {
                if (sortBy === 'name') {
                    return $(a).data('name').localeCompare($(b).data('name'));
                } else if(sortBy === 'ar') {
                    return $(b).data('ar') - $(a).data('ar');
                } else if(sortBy === 'pr') {
                    return $(b).data('pr') - $(a).data('pr');
                } else if(sortBy === 'ct') {
                    return $(b).data('ct') - $(a).data('ct');
                } else if(sortBy === 'cdt') {
                    return $(b).data('cdt') - $(a).data('cdt');
                }
            });
            // Replace the existing staff boxes with the sorted ones
            $(this).html(staffBoxes);
            console.log(staffBoxes);
        });
    });
});


// function sortAndDisplayByDepartment() {
//     var departments = {};

//     // Group the staff boxes by department
//     $('.staff-box').each(function() {
//         var department = $(this).data('departments') || 'No Department';
//         if (!departments[department]) {
//             departments[department] = [];
//         }
//         departments[department].push(this);
//     });

//     $('#staffGrid').empty();

//     // Loop through each department and create the layout
//     $.each(departments, function(departmentName, staffBoxes) {
//         // Create the full-width department header
//         var departmentHeader = $('<div/>', {
//             'class': 'text-lg uppercase font-bold text-gray-800 text-center py-2 mb-4 bg-blue-100 rounded-lg shadow w-full', // full width
//             'text': departmentName
//         });

//         // Create a container for the staff boxes, this will be a flex container
//         var boxesContainer = $('<div/>', {
//             'class': 'flex flex-row flex-wrap justify-center gap-4' // Updated classes for flex row
//         });

//         // Append staff boxes to the container
//         $.each(staffBoxes, function(index, box) {
//             // Add flex classes for the boxes
//             $(box).addClass('bg-pink-100 p-4 rounded-lg shadow-md'); // Removed flex-4 as it's not a standard Tailwind class
//             boxesContainer.append(box);
//         });

//         // Append the department header and container to the grid
//         var departmentContainer = $('<div/>', {
//             'class': 'mb-8' // margin bottom for spacing between departments
//         });

//         departmentContainer.append(departmentHeader).append(boxesContainer);
//         $('#staffGrid').append(departmentContainer);
//     });
// }
// function sortAndDisplayByDepartment() {
//     var departments = {};

//     // Group the staff boxes by department
//     $('.staff-box').each(function() {
//         var department = $(this).data('departments') || 'No Department';
//         if (!departments[department]) {
//             departments[department] = [];
//         }
//         departments[department].push(this);
//     });

//     // Assume only one department can be displayed at a time
//     // If your logic requires showing multiple departments at once, you will need to adjust this.
//     var departmentNames = Object.keys(departments);
//     if (departmentNames.length > 0) {
//         // Display the first department name and hide the department row if no department is found
//         var departmentName = departmentNames[1];
//         $('#departmentNameRow').text(departmentName).removeClass('hidden');
//     } else {
//         $('#departmentNameRow').addClass('hidden');
//     }

//     // Empty the current staffGrid for staff cards
//     $('#staffGrid').empty();

//     // Get the staff boxes for the first department
//     var staffBoxes = departments[departmentName] || [];

//     // Append staff boxes to the staffGrid
//     $.each(staffBoxes, function(index, box) {
//         $(box).addClass('bg-pink-100 p-4 rounded-lg shadow-md'); // Classes for styling the staff boxes
//         $('#staffGrid').append(box); // Append each staff card to the staffGrid
//     });
// }
const container = document.querySelector('.overflow-x-auto');
                
let startY;
let startX;
let scrollLeft;
let scrollTop;
let isDown;

container.addEventListener('mousedown',e => mouseIsDown(e));  
container.addEventListener('mouseup',e => mouseUp(e))
container.addEventListener('mouseleave',e=>mouseLeave(e));
container.addEventListener('mousemove',e=>mouseMove(e));


function mouseIsDown(e){
  isDown = true;
  startY = e.pageY - container.offsetTop;
  startX = e.pageX - container.offsetLeft;
  scrollLeft = container.scrollLeft;
  scrollTop = container.scrollTop; 

}
function mouseUp(e){
  isDown = false;
}
function mouseLeave(e){
  isDown = false;
}
function mouseMove(e){
  if(isDown){
    
    

    e.preventDefault();



    //Move vertcally
    const y = e.pageY - container.offsetTop;
    const walkY = y - startY;
    container.scrollTop = scrollTop - walkY;

    //Move Horizontally
    const x = e.pageX - container.offsetLeft;
    const walkX = x - startX;
    container.scrollLeft = scrollLeft - walkX;

  }
}

</script>
</body>
</html>