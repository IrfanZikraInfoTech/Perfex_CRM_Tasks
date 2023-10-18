<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper" class="wrapper">
    <div class="content flex flex-col">

        <div class="bg-white flex flex-col gap-4 rounded">
            <h2 class="text-xl text-center py-4">
                
            Set Shifts

            <a class="btn-primary p-2 rounded float-right mr-4" target="_blank" href="<?php echo admin_url('team_management/process_staff_leaves_cron_access/OUIYUGBSCL')?>" >Update Leaves</a>
            
            <button class="btn-primary p-2 rounded float-right mr-4 manage-globalleaves-btn " data-toggle="modal" data-target="#globalleavesModal">Global Holidays</button>
           
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
                                    <button onclick="window.location.href='<?= admin_url('team_management/set_shifts/'.$staff['staffid']) ?>/'+document.getElementById('monthSelection<?php echo $staff['staffid']; ?>').value;" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">Set Shifts</button>
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


<!-- global -->
<div class="modal fade" id="globalleavesModal" tabindex="-1" aria-labelledby="globalleavesModalLabel" aria-hidden="true">
    <div class="max-w-3xl mx-auto my-3">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-xl" id="globalleavesModalLabel">Global holidays for Organization</h5>
            </div>
            <div class="modal-body p-4">

                <!-- Add this inside the manage-leaves-modal div -->
                <form id="add-globalleave-form" class="mb-4">
                    <!-- <input type="hidden" name="staff_id" id="leave_staff_id" value="1" /> -->
                    <div class="grid grid-cols-2 gap-4">      
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input required type="date" name="start_date" id="start_date" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input required type="date" name="end_date" id="end_date" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                        </div>
                       
                        <div class="w-full col-span-2">
                            <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                            <input required type="text" name="reason" id="reason" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                        </div>
                    </div>
                    <button type="submit" class="mt-4 bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-800">Add Leave</button>
                </form>

                <table id="globalleaves-table" class="leaves-table min-w-full divide-y divide-gray-200 table-auto mt-4">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Added</th>
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

document.querySelectorAll('.manage-leaves-btn').forEach(function (button) {
    button.addEventListener('click', function () {
        const staffId = button.getAttribute('data-staff-id');
        const staffName = button.getAttribute('data-staff-name');
        document.getElementById("leavesModalLabel").textContent = "Manage Leaves for : "+staffName;
        document.getElementById("leave_staff_id").value = staffId;
        fetchLeaves(staffId);
    });
});



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

$('#add-globalleave-form').on('submit', function(event) {
    event.preventDefault();
    // Data collection from form
    var formData = $(this).serialize();
    console.log(formData);
    // AJAX request to server
    $.ajax({
        url: '<?= admin_url('team_management/add_global_leave')?>',  // Add your controller's URL here
        type: 'POST',
        dataType: 'json',
        // data: {
        //     formData,
        // [csrfData.token_name]: csrfData.hash
        // } ,
        data: formData + "&" + csrfData.token_name + "=" + csrfData.hash,
        
        success: function(response) {
            // Check if the response contains a 'success' property
            if (typeof response.success !== 'undefined') {
                if(response.success) {
                    alert_float('success','Leave added successfully!');
                    location.reload(); // To refresh the page and show the added leave
                } else {
                    alert_float('danger','Error adding leave!');
                }
            } else {
                // If the 'success' property is missing, treat it as an unexpected server response
                alert_float('danger','Unexpected server response.');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // Enhanced error handling
            alert('Server error: ' + textStatus + ": " + errorThrown);
        }
    });
});


$('#globalleavesModal').on('show.bs.modal', function (e) {
    $.ajax({
        url: '<?=admin_url('team_management/fetch_global_leaves')?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            // clear the table body first
            $('#globalleaves-table tbody').empty();

            // loop through the data and append each row to the table
            data.forEach(function(leave, index) {
                var row = '<tr class="' + (index % 2 === 0 ? 'bg-gray-100' : '') + '">'; // alternating colors for rows
                row += '<td class="px-8 py-4">' + leave.reason + '</td>';
                row += '<td class="px-8 py-4">' + leave.start_date + ' to ' + leave.end_date + '</td>';
                row += '<td class="px-8 py-4">' + leave.created_at + '</td>';
                row += '<td class="px-8 py-4"><button class="delete-leave bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" data-id="' + leave.id + '">Delete</button></td>';
                row += '</tr>';
                $('#globalleaves-table tbody').append(row);
            });
        }
    });
});
$(document).on('click', '.delete-leave', function() {
    var leaveId = $(this).data('id');
    var that = $(this); // Cache the context of the clicked delete button

    $.ajax({
        url: '<?=admin_url('team_management/delete_global_leave')?>',
        type: 'POST',
        dataType: 'json', 

        data: { id: leaveId },
        success: function(response) {
            console.log(response);
            // Using the cached context for the delete button
            if(response.success) {
                // Refresh your modal data or remove the deleted row
                that.closest('tr').remove();
                alert_float('success','Leave deleted successfully.');

            } 
            else {
                // If unsuccessful, alert the user
                alert_float('danger','Failed to delete the leave.');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // Handle any AJAX errors
            console.error("AJAX Error:", textStatus, errorThrown);
            alert('An error occurred while trying to delete the leave.');
        }
    });
});
</script>

</body>
</html>