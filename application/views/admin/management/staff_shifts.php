<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper" class="wrapper">
    <div class="content flex flex-col">

        <div class="bg-white flex flex-col gap-4 rounded">
            <h2 class="text-xl text-center py-4">
                
            Set Shifts
                
            <a class="btn-primary p-2 rounded float-right mr-4" href="export_all_shift_details_to_pdf/<?php echo date('n') ?>" id="exportPDF" >PDF</a>
            
            <a class="btn-primary p-2 rounded float-right mr-4" target="_blank" href="<?php echo admin_url('team_management/process_staff_leaves_cron_access/OUIYUGBSCL')?>" >Update Leaves</a>
            
            <br>
        
        
        </h2>
            <div class="align-middle inline-block min-w-full overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Id</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Name</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($staff_members as $staff) { ?>
                            <tr class="hover:bg-gray-200/30 transition-all">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $staff['staffid']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $staff['firstname'] . ' ' . $staff['lastname']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <select class="p-2 form-select block w-full mt-1 text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" id="monthSelection<?php echo $staff['staffid']; ?>" onchange="document.getElementById('activity-btn-<?php echo $staff['staffid']; ?>').setAttribute('href', '<?php echo admin_url();?>team_management/activity_log/<?php echo $staff['staffid']; ?>/'+this.value)">
                                    <?php
                                    $currentMonth = date('m');
                                    for ($i = 1; $i <= 12; $i++):
                                        $selected = ($i == $currentMonth) ? 'selected' : '';
                                        $monthName = date('F', mktime(0, 0, 0, $i, 10)); // Get the month name
                                    ?>
                                        <option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $monthName; ?></option>
                                    <?php endfor; ?>
                                </select>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-toggle="modal" data-target="#shiftsModal" data-staff-id="<?php echo $staff['staffid']; ?>" data-staff-name="<?php echo $staff['firstname']; ?>" data-table-btn="idk">Set Shifts</button>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?php echo admin_url();?>team_management/activity_log/<?php echo $staff['staffid']; ?>/<?php echo date('n'); ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white hover:text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="activity-btn-<?php echo $staff['staffid']; ?>" data-staff-id="<?php echo $staff['staffid']; ?>">View</button>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button class="manage-leaves-btn inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-toggle="modal" data-target="#leavesModal" data-staff-id="<?php echo $staff['staffid']; ?>" data-staff-name="<?php echo $staff['firstname']; ?>">Leaves</button>
                                </td>

                            </tr>
                        <?php } ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="shiftsModal" tabindex="-1" aria-labelledby="shiftsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-xl" id="shiftsModalLabel">Set Shifts for Staff</h5>
            </div>
            <div class="modal-body">
    
            <form id="shiftsForm">

                <div class="flex flex-row p-2 mb-4 justify-center gap-2">

                    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseWeeks" aria-expanded="false" aria-controls="collapseWeeks">
                        Week Times
                    </button>

                    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseRoot" aria-expanded="false" aria-controls="collapseRoot">
                        Root Times
                    </button>

                    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseAll" aria-expanded="false" aria-controls="collapseAll">
                        All Times
                    </button>

                </div>
                
                <div class="collapse" id="collapseWeeks">
                    <div class="flex flex-col p-2 gap-2">
                    
                        <?php 
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        foreach($days as $day): 
                        ?>
                            <div class="flex flex-row gap-2">
                                <div class="w-1/2">
                                    <label for="<?= strtolower($day) ?>_shift_start1"><?= $day ?> Shift 1 Start</label>
                                    <input type="time" class="form-control" id="<?= strtolower($day) ?>_shift_start1" name="<?= strtolower($day) ?>_shift_start1">
                                </div>
                                <div class="w-1/2">
                                    <label for="<?= strtolower($day) ?>_shift_end1"><?= $day ?> Shift 1 End</label>
                                    <input type="time" class="form-control" id="<?= strtolower($day) ?>_shift_end1" name="<?= strtolower($day) ?>_shift_end1">
                                </div>
                            </div>

                            <div class="flex flex-row gap-2">
                                <div class="w-1/2">
                                    <label for="<?= strtolower($day) ?>_shift_start2"><?= $day ?> Shift 2 Start</label>
                                    <input type="time" class="form-control" id="<?= strtolower($day) ?>_shift_start2" name="<?= strtolower($day) ?>_shift_start2">
                                </div>
                                <div class="w-1/2">
                                    <label for="<?= strtolower($day) ?>_shift_end2"><?= $day ?> Shift 2 End</label>
                                    <input type="time" class="form-control" id="<?= strtolower($day) ?>_shift_end2" name="<?= strtolower($day) ?>_shift_end2">
                                </div>
                            </div>

                            <div class="flex flex-row gap-2">
                                <div class="w-1/2">
                                    <label for="<?= strtolower($day) ?>_shift_start3"><?= $day ?> Shift 3 Start</label>
                                    <input type="time" class="form-control" id="<?= strtolower($day) ?>_shift_start3" name="<?= strtolower($day) ?>_shift_start3">
                                </div>
                                <div class="w-1/2">
                                    <label for="<?= strtolower($day) ?>_shift_end3"><?= $day ?> Shift 3 End</label>
                                    <input type="time" class="form-control" id="<?= strtolower($day) ?>_shift_end3" name="<?= strtolower($day) ?>_shift_end3">
                                </div>
                            </div>

                            <?php if($day !== "Sunday"){?>
                            <div class="border-b my-4 border-black border-dashed"></div>
                            <?php }?>

                        <?php endforeach; ?>
                        <div class="flex justify-end gap-2">
                            <button type="button" class="btn btn-primary" onclick="changeTimesWeek();">Change All Times for Week</button>
                        </div>
                    </div>
                </div>

                <div class="collapse" id="collapseRoot">
                    <div class="flex flex-col p-2 gap-2">      
                        <div class="flex flex-row gap-2 ">

                            <div class="w-1/2">
                                <label for="all_shift_start1">Shift 1 Start</label>
                                <input type="time" class="form-control" id="all_shift_start1" name="all_shift_start1">
                            </div>
                            <div class="w-1/2">
                                <label for="all_shift_end1">Shift 1 End</label>
                                <input type="time" class="form-control" id="all_shift_end1" name="all_shift_end1">
                            </div>

                        </div>

                        <div class="flex flex-row gap-2">

                            <div class="w-1/2">
                                <label for="all_shift_start2">Shift 2 Start</label>
                                <input type="time" class="form-control" id="all_shift_start2" name="all_shift_start2">
                            </div>
                            <div class="w-1/2">
                                <label for="all_shift_end2">Shift 2 End</label>
                                <input type="time" class="form-control" id="all_shift_end2" name="all_shift_end2">
                            </div>

                        </div>

                        <div class="flex flex-row gap-2">

                            <div class="w-1/2">
                                <label for="all_shift_start3">Shift 3 Start</label>
                                <input type="time" class="form-control" id="all_shift_start3" name="all_shift_start3">
                            </div>
                            <div class="w-1/2">
                                <label for="all_shift_end3">Shift 3 End</label>
                                <input type="time" class="form-control" id="all_shift_end3" name="all_shift_end3">
                            </div>

                        </div>
                                    
                        <div class="flex justify-end gap-2">
                                <button type="button" class="btn btn-primary" onclick="changeTimes();">Change All Times</button>
                        </div>

                    </div>
                </div>

                <div class="collapse" id="collapseAll">
                    <div class="individual-shifts mt-4 max-h-[50vh] overflow-y-scroll">
                        <?php for ($i = 1; $i <= 31; $i++): ?>

                            <div class="flex flex-row p-2 gap-2 ">

                                <div class="w-1/2">
                                    <label for="start_shift1_day_<?php echo $i; ?>"><?php echo $i; ?>/<span class="monthSpan"></span>/<?= date('Y') ?> - Shift 1 Start</label>
                                    <input required type="time" class="form-control" id="start_shift1_day_<?php echo $i; ?>" name="start_shift1_day_<?php echo $i; ?>">
                                </div>
                                <div class="w-1/2">
                                    <label for="end_shift1_day_<?php echo $i; ?>"><?php echo $i; ?>/<span class="monthSpan"></span>/<?= date('Y') ?> - Shift 1 End</label>
                                    <input required type="time" class="form-control" id="end_shift1_day_<?php echo $i; ?>" name="end_shift1_day_<?php echo $i; ?>">
                                </div>

                            </div>

                            <div class="flex flex-row p-2 gap-2 ">

                                <div class="w-1/2">
                                    <label for="start_shift2_day_<?php echo $i; ?>"><?php echo $i; ?>/<span class="monthSpan"></span>/<?= date('Y') ?> - Shift 2 Start</label>
                                    <input required type="time" class="form-control" id="start_shift2_day_<?php echo $i; ?>" name="start_shift2_day_<?php echo $i; ?>">
                                </div>
                                <div class="w-1/2">
                                    <label for="end_shift2_day_<?php echo $i; ?>"><?php echo $i; ?>/<span class="monthSpan"></span>/<?= date('Y') ?> - Shift 2 End</label>
                                    <input required type="time" class="form-control" id="end_shift2_day_<?php echo $i; ?>" name="end_shift2_day_<?php echo $i; ?>">
                                </div>

                            </div>

                            <div class="flex flex-row p-2 gap-2 ">

                                <div class="w-1/2">
                                    <label for="start_shift3_day_<?php echo $i; ?>"><?php echo $i; ?>/<span class="monthSpan"></span>/<?= date('Y') ?> - Shift 3 Start</label>
                                    <input required type="time" class="form-control" id="start_shift3_day_<?php echo $i; ?>" name="start_shift3_day_<?php echo $i; ?>">
                                </div>
                                <div class="w-1/2">
                                    <label for="end_shift3_day_<?php echo $i; ?>"><?php echo $i; ?>/<span class="monthSpan"></span>/<?= date('Y') ?> - Shift 3 End</label>
                                    <input required type="time" class="form-control" id="end_shift3_day_<?php echo $i; ?>" name="end_shift3_day_<?php echo $i; ?>">
                                </div>

                            </div>

                            <div class="border-b my-4 border-black border-dashed"></div>

                        <?php endfor; ?>
                    </div>
                </div>
            </form>

            </div>

            <div class="modal-footer flex justify-between">

                <div class="btn-group mr-auto">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Export
                    </button>
                    <div class="dropdown-menu flex-col p-2 gap-2">
                    <a class="dropdown-item p-2" href="#" id="exportPDF" >PDF</a><br>
                    <button class="dropdown-item p-2" id="exportMail" onclick="exportMail();">Mail</button>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveShifts" data-staff-id="" onclick="setShifts();">Save Changes</button>
                </div>
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="leavesModal" tabindex="-1" aria-labelledby="leavesModalLabel" aria-hidden="true">
    <div class="max-w-3xl mx-auto my-3">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-xl" id="leavesModalLabel">Set Shifts for Staff</h5>
            </div>
            <div class="modal-body p-4">

                <!-- Add this inside the manage-leaves-modal div -->
                <form id="add-leave-form" class="mb-4">
                    <input type="hidden" name="staff_id" id="leave_staff_id" value="1" />
                    <div class="grid grid-cols-2 gap-4">      
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input required type="date" name="start_date" id="start_date" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input required type="date" name="end_date" id="end_date" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                        </div>
                        <div class="w-full col-span-2" id="shift-container" style="display:none;">
                            <label for="reason" class="block text-sm font-medium text-gray-700">Shift</label>
                            <select required name="shift" id="shift" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="1">1st Shift</option>
                                <option value="2">2nd Shift</option>
                                <option value="all" selected>All day</option>
                            </select>
                        </div>
                        <div class="w-full col-span-2">
                            <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                            <input required type="text" name="reason" id="reason" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                        </div>
                    </div>
                    <button type="submit" class="mt-4 bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-800">Add Leave</button>
                </form>

                <table id="leaves-table" class="leaves-table min-w-full divide-y divide-gray-200 table-auto mt-4">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Added</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delete</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Leaves will be added here dynamically -->
                    </tbody>
                </table>

            </div>


            <div class="modal-footer flex justify-between">
                <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>        
            </div>
        </div>
    </div>
</div>



<?php init_tail(); ?>

<script>



// Add a click event listener to the 'Set Shifts' buttons
document.querySelectorAll('button[data-table-btn]').forEach(function (button) {
    button.addEventListener('click', function () {
        const staffId = button.getAttribute('data-staff-id');
        const staffName = button.getAttribute('data-staff-name');
        document.getElementById("shiftsModalLabel").textContent = "Set shifts for : "+staffName;
        let month = document.getElementById("monthSelection"+staffId).value;     

        document.querySelectorAll('.monthSpan').forEach(function(span) {
            span.innerHTML = month;
        });
        showShiftsModal(staffId, month);
    });
});

document.querySelectorAll('.manage-leaves-btn').forEach(function (button) {
    button.addEventListener('click', function () {
        const staffId = button.getAttribute('data-staff-id');
        const staffName = button.getAttribute('data-staff-name');
        document.getElementById("leavesModalLabel").textContent = "Manage Leaves for : "+staffName;
        document.getElementById("leave_staff_id").value = staffId;
        fetchLeaves(staffId);
    });
});

function changeTimes() {

    if (confirm("Are you sure?") == true) {
        for (let i = 1; i <= 31; i++){
            if(document.getElementById('start_shift1_day_'+i) != null){

                document.getElementById('start_shift1_day_'+i).value = document.getElementById('all_shift_start1').value;
                document.getElementById('end_shift1_day_'+i).value = document.getElementById('all_shift_end1').value;

                document.getElementById('start_shift2_day_'+i).value = document.getElementById('all_shift_start2').value;
                document.getElementById('end_shift2_day_'+i).value = document.getElementById('all_shift_end2').value;

                document.getElementById('start_shift3_day_'+i).value = document.getElementById('all_shift_start3').value;
                document.getElementById('end_shift3_day_'+i).value = document.getElementById('all_shift_end3').value;
            
            }
        }
    } 
}

function changeTimesWeek() {
    if (confirm("Are you sure?") == true) {
        let days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        // We'll assume this is the current year and month
        let year = new Date().getFullYear();
        let month = new Date().getMonth(); // 0-indexed, so January is 0 and December is 11

        for (let i = 1; i <= 31; i++) {
            // Create a new date for the ith day of the current month
            let date = new Date(year, month, i);

            // This will automatically correct the date if i is larger than the number of days in the month
            // e.g., if the current month is February and i is 30, this will give us the date for March 2

            // Now we can get the day of the week for this date
            let currentDay = days[date.getDay()]; // getDay returns 0 (for Sunday) through 6 (for Saturday)

            // Then we can use this day of the week in the rest of your code
            if(document.getElementById('start_shift1_day_'+i) != null){
                document.getElementById('start_shift1_day_'+i).value = document.getElementById(currentDay+'_shift_start1').value;

            }
            if(document.getElementById('end_shift1_day_'+i) != null){
                document.getElementById('end_shift1_day_'+i).value = document.getElementById(currentDay+'_shift_end1').value;
            }
            if(document.getElementById('start_shift2_day_'+i) != null){
                document.getElementById('start_shift2_day_'+i).value = document.getElementById(currentDay+'_shift_start2').value;
            }  
            if(document.getElementById('end_shift2_day_'+i) != null){
                document.getElementById('end_shift2_day_'+i).value = document.getElementById(currentDay+'_shift_end2').value;
            }

            if(document.getElementById('start_shift3_day_'+i) != null){
                document.getElementById('start_shift3_day_'+i).value = document.getElementById(currentDay+'_shift_start3').value;
            }  
            if(document.getElementById('end_shift3_day_'+i) != null){
                document.getElementById('end_shift3_day_'+i).value = document.getElementById(currentDay+'_shift_end3').value;
            }
        }
    } 
}



function showShiftsModal(staffId, month) {
    // Fetch the existing shifts for the staff member and populate the input fields
    // You can make an AJAX request to your server to fetch the shifts data and then update the input fields
    $.ajax({
        url: '<?php echo base_url('team_management/get_shift_timings/'); ?>' + staffId + '/' + month,
        type: 'GET',
        dataType: 'json',
        success: function(shiftTimings) {
            for (var i = 0; i < shiftTimings.length; i++) {
                var day = shiftTimings[i].day;

                if(shiftTimings[i].shift_number == 1){
                    var shift1_start_time = shiftTimings[i].shift_start_time;
                    var shift1_end_time = shiftTimings[i].shift_end_time;

                    $('#start_shift1_day_' + day).val(shift1_start_time);
                    $('#end_shift1_day_' + day).val(shift1_end_time);  
                }else if(shiftTimings[i].shift_number == 2){
                    var shift2_start_time = shiftTimings[i].shift_start_time;
                    var shift2_end_time = shiftTimings[i].shift_end_time;

                    $('#start_shift2_day_' + day).val(shift2_start_time);
                    $('#end_shift2_day_' + day).val(shift2_end_time);  
                }else{
                    var shift3_start_time = shiftTimings[i].shift_start_time;
                    var shift3_end_time = shiftTimings[i].shift_end_time;

                    $('#start_shift3_day_' + day).val(shift3_start_time);
                    $('#end_shift3_day_' + day).val(shift3_end_time);  
                }

                
            }
            for (var i = 0; i <= 31; i++) {
                if(!shiftTimings[i]){
                    $('#start_shift1_day_' + i).val('');
                    $('#end_shift1_day_' + i).val('');

                    $('#start_shift2_day_' + i).val('');
                    $('#end_shift2_day_' + i).val('');

                    $('#start_shift3_day_' + i).val('');
                    $('#end_shift3_day_' + i).val('');
                }    
            }

            document.getElementById("saveShifts").setAttribute("data-staff-id", staffId);
            document.getElementById("saveShifts").setAttribute("data-month", month);
            document.getElementById("exportPDF").setAttribute("href", "export_shift_details_to_pdf/"+staffId+"/"+month);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('Error fetching shift timings:', textStatus, errorThrown);
        }
    });
}

function setShifts() {

    var _staffid = document.getElementById("saveShifts").getAttribute("data-staff-id");
    var _month = document.getElementById("saveShifts").getAttribute("data-month");

    const formData = new FormData(shiftsForm);
    formData.append('staff_id', _staffid);
    formData.append(csrfData.token_name, csrfData.hash);
    formData.append('month', _month);
    fetch('save_shift_timings', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Shift timings saved successfully.');
            } else {
                alert('An error occurred while saving the shift timings.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the shift timings.');
        });
}

function exportMail() {
    var _staffid = document.getElementById("saveShifts").getAttribute("data-staff-id");
    var _month = document.getElementById("saveShifts").getAttribute("data-month");

    const formData = new FormData();
    formData.append('staff_id', _staffid);
    formData.append(csrfData.token_name, csrfData.hash);
    formData.append('month', _month);

    fetch('mail_shift_timings', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Mail sent successfully.');
            } else {
                alert('An error occurred while mailing.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while mailing.');
        });
}

function updateSelect(element) {
    var staffId = element.getAttribute("data-staff-id");
    document.getElementById("activity-btn-"+staffId).setAttribute("href", "<?php echo admin_url();?>team_management/activity_log/"+staffId+"/"+element.value);
}

// Bind an event listener to the add-leave-form submit event
$("#add-leave-form").on("submit", function(event) {
  event.preventDefault();

  const staffId = $('[name="staff_id"]').val();
  const reason = $('[name="reason"]').val();
  const startDate = $('[name="start_date"]').val();
  const endDate = $('[name="end_date"]').val();
  const shift = $('#shift').val();

  addLeave(staffId, reason, startDate, endDate, shift);

});

function addLeave(staffId, reason, startDate, endDate, shift) {
  let leaveId = null;
  $.ajax({
    url: 'add_leave', // Your controller function URL for adding a leave
    type: 'POST',
    dataType: 'json',
    data: {
      staff_id: staffId,
      reason: reason,
      start_date: startDate,
      end_date: endDate,
      shift: shift,
      [csrfData.token_name]: csrfData.hash // CSRF token
    },
    success: function(response) {
      if (response.success) {
        alert_float('success', 'Leave added successfully!');
        const leaveId = response.id;
        let durationTxt = `${startDate} to ${endDate}`;
        let shiftTxt = (shift == "all") ? "Full Day" : ((shift == "1") ? "1st Shift" : "2nd Shift");
        $("#leaves-table > tbody").prepend(`

        <tr id="leave-${leaveId}" class="bg-white">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${reason}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">`+durationTxt+`</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Today</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">`+shiftTxt+`</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                onclick="deleteLeave(${leaveId})"
                class="text-indigo-600 hover:text-indigo-900"
                >
                Delete
                </button>
            </td>
        </tr>

    `);
        
      } else {
        alert_float('danger', 'There was an error adding leave.');
      }
    },
    error: function() {
        alert_float('danger', 'There was an error adding leave.');
    }
  });
}

function deleteLeave(leaveId) {
  $.ajax({
    url: 'delete_leave', // Your controller function URL for deleting a leave
    type: 'POST',
    dataType: 'json',
    data: {
      leave_id: leaveId,
      [csrfData.token_name]: csrfData.hash // CSRF token
    },
    success: function(response) {
      if (response.success) {
        alert_float('success', 'Leave deleted successfully!');
        $("#leave-" + leaveId).remove();
        // Update the frontend (e.g., remove the row from the table)
      } else {
        alert_float('danger', 'There was an deleting adding leave.');
      }
    },
    error: function() {
        alert_float('danger', 'There was an deleting adding leave.');
    }
  });
}

function fetchLeaves(staffId) {
  $.ajax({
    url: "view_leaves",
    type: "POST",
    dataType: "json",
    data: { staff_id: staffId, [csrfData.token_name]: csrfData.hash},
    success: function(response) {
        
        const leaveTableBody = $("#leaves-table tbody");
        leaveTableBody.empty(); // Clear the existing rows
        response.leaves.forEach(function(leave) {
            let durationStr = `${leave.start_date} to ${leave.end_date}`;
            let shiftTxt = (leave.shift == "all") ? "Full Day" : ((leave.shift == "1") ? "1st Shift" : "2nd Shift");
            const row = `
            
            <tr class="bg-white" id="leave-${leave.id}">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${leave.reason}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${durationStr}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${leave.created_at}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${shiftTxt}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button
                    onclick="deleteLeave(${leave.id})"
                    class="text-indigo-600 hover:text-indigo-900"
                    >
                    Delete
                    </button>
                </td>
            </tr>
            
            `;
            leaveTableBody.append(row);
        });
    },
    error: function() {
      console.error("Error fetching leave data.");
    },
  });
}

function checkDatesAndToggleShift() {

    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();

    if (startDate === endDate) {
        $('#shift-container').show();
    } else {
        $('#shift-container').hide();
    }

}
// Attach event listeners to the date inputs
$('#start_date').on('change', checkDatesAndToggleShift);
$('#end_date').on('change', checkDatesAndToggleShift);


</script>

</body>
</html>