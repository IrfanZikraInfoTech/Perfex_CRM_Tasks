<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h3 class="text-lg font-semibold mb-4">Control Room for Staff: <?= $staff_name ?></h3>
                        <div class="mb-4 flex flex-row gap-4 border-b border-gray-300 border-solid pb-6">
                            <button id="clock-in" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded shadow">Clock In</button>
                            <button id="clock-out" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded shadow">Clock Out</button>
 
                            <select class="px-4 text-lg border border-solid bg-transparent text-lime-500 appearance-none cursor-pointer " onchange="statusSelectColors(this);" id="status">
                                <option id="Online" value="Online" class="text-lime-500">Online</option>
                                <option id="AFK" value="AFK" class="text-blue-500">AFK</option>
                                <option id="Offline" value="Offline" class="text-pink-500">Offline</option>
                                <option id="Leave" value="Leave" class="text-amber-600">Leave</option>
                            </select>

                            <div>
                            <h2 class="text-xl font-semibold" id="live-timer">
                            </h2>
                            </div>
                        </div>
                        
                        <h4 class="text-lg font-semibold mb-4">Time Entries</h4>
                        <table class="table-auto w-full" id="staff-time-entries">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock In</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock Out</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>

                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="max-w-3xl mx-auto my-3">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-xl" id="editModalLabel">Edit Clock Times</h5>
            </div>
            <div class="modal-body p-4">
                
                <form id="edit-form" class="mb-4 flex flex-col gap-4">
                    <input type="hidden" id="edit-id" />
                    <div>
                    <h2 class="text-lg">Clock In</h2>  
                    <div class="grid grid-cols-2 gap-4">            
                        <div>
                            <input required type="date" name="in_date" id="in_date" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                        </div>
                        <div>
                            <input required type="time" name="in_time" id="in_time" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                        </div>
                    </div>
                    </div>

                    <div class="border-t border-solid border-gray-300 pt-4">
                    <h2 class="text-lg">Clock Out</h2>  
                    <div class="grid grid-cols-2 gap-4">            
                        <div>
                            <input type="date" name="out_date" id="out_date" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                        </div>
                        <div>
                            <input type="time" name="out_time" id="out_time" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                        </div>
                    </div>
                    </div>

                    <div class="flex flex-row gap-2">
                        <button type="button" class="border border-black mt-4 bg-gray-300 text-black py-2 px-4 rounded-md hover:bg-gray-400" data-dismiss="modal">Close</button>
                        <button type="submit" class="border border-black mt-4 bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-800">Edit</button>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>

const staffTimeEntriesTable = document.querySelector("#staff-time-entries");
const staffId = <?php echo $staff_id ?>;
const tbody = staffTimeEntriesTable.querySelector("tbody");

function fetchStaffTimeEntries() {
    fetch(`<?= admin_url("team_management/fetch_staff_time_entries") ?>/${staffId}`)
        .then(response => response.json())
        .then(data => {
            tbody.innerHTML = "";
            let prevDate = null;
            data.forEach(entry => {
                const currentDate = new Date(entry.clock_in).toDateString();
                const row = document.createElement("tr");
                row.classList.add("border-gray-200", "border-solid", "hover:bg-gray-100", "transition-all"); // Add classes to the row
                if (prevDate && prevDate !== currentDate) {
                    row.classList.add("border-t-2", "border-gray-400");
                }
                const idCell = document.createElement("td");
                idCell.textContent = entry.id;
                idCell.classList.add("p-4"); // Add classes to the cell
                row.appendChild(idCell);
                const clockInCell = document.createElement("td");
                clockInCell.textContent = entry.clock_in;
                clockInCell.classList.add("p-4"); // Add classes to the cell
                row.appendChild(clockInCell);
                const clockOutCell = document.createElement("td");
                clockOutCell.textContent = entry.clock_out;
                clockOutCell.classList.add("p-4"); // Add classes to the cell
                row.appendChild(clockOutCell);
                const statusCell = document.createElement("td");
                statusCell.innerHTML = `
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        CRUD
                    </button>
                    <div class="dropdown-menu p-2">
                    <button data-toggle="modal" data-target="#editModal" onclick="openEditModal('`+entry.id+`', '`+entry.clock_in+`', '`+entry.clock_out+`')" class="dropdown-item w-full mb-1 btn-primary p-2 px-4 text-white">Edit</button>
                    <button onclick="deleteStaffTimeEntry('`+entry.id+`')" class="dropdown-item w-full mb-1 btn-primary p-2 px-4 text-white">Delete</button>
                   </div>
                </div>
                `;
                statusCell.classList.add("p-4"); // Add classes to the cell
                row.appendChild(statusCell);
                // Add more cells as needed
                tbody.appendChild(row);
                prevDate = currentDate;
            });
        });
}

$("#edit-form").on("submit", function(event) {
    event.preventDefault();
    editStaffTime();
});

function openEditModal(id, in_clock, out_clock) {

    $("#edit-id").val(id);

    // Split the date and time parts
    const [datePartIn, timePartIn] = in_clock.split(" ");

    // Set the date to the input of type 'date'
    const dateInputIn = document.getElementById("in_date");
    dateInputIn.value = datePartIn;

    // Extract hours and minutes from the time part
    const [hoursIn, minutesIn] = timePartIn.split(":");

    // Set the time to the input of type 'time'
    const timeInputIn = document.getElementById("in_time");
    timeInputIn.value = `${hoursIn}:${minutesIn}`;


    // Clock out // 

    // Split the date and time parts
    const [datePartOut, timePartOut] = out_clock.split(" ");

    // Set the date to the input of type 'date'
    const dateInputOut = document.getElementById("out_date");
    dateInputOut.value = datePartOut;

    // Extract hours and minutes from the time part
    const [hoursOut, minutesOut] = timePartOut.split(":");

    // Set the time to the input of type 'time'
    const timeInputOut = document.getElementById("out_time");
    timeInputOut.value = `${hoursOut}:${minutesOut}`;

}

function editStaffTime() {
    var entryId = $("#edit-id").val(); // Add # prefix
    var dateIn = $("#in_date").val(); // Add # prefix
    var timeIn = $("#in_time").val(); // Add # prefix
    var dateOut = $("#out_date").val(); // Add # prefix
    var timeOut = $("#out_time").val(); // Add # prefix

    let combinedDateTimeIn = new Date(dateIn + " " + timeIn); // Use dateIn and timeIn
    // Format the combined date and time
    let formattedDateTimeIn =
    combinedDateTimeIn.getFullYear() +
    "-" +
    String(combinedDateTimeIn.getMonth() + 1).padStart(2, "0") +
    "-" +
    String(combinedDateTimeIn.getDate()).padStart(2, "0") +
    " " +
    String(combinedDateTimeIn.getHours()).padStart(2, "0") +
    ":" +
    String(combinedDateTimeIn.getMinutes()).padStart(2, "0") +
    ":" +
    String(combinedDateTimeIn.getSeconds()).padStart(2, "0");

    let combinedDateTimeOut = new Date(dateOut + " " + timeOut); // Use dateOut and timeOut
    // Format the combined date and time
    let formattedDateTimeOut =
    combinedDateTimeOut.getFullYear() +
    "-" +
    String(combinedDateTimeOut.getMonth() + 1).padStart(2, "0") +
    "-" +
    String(combinedDateTimeOut.getDate()).padStart(2, "0") +
    " " +
    String(combinedDateTimeOut.getHours()).padStart(2, "0") +
    ":" +
    String(combinedDateTimeOut.getMinutes()).padStart(2, "0") +
    ":" +
    String(combinedDateTimeOut.getSeconds()).padStart(2, "0");

    // Prepare the data to be sent
    const data = {
        entry_id: entryId,
        in_time: formattedDateTimeIn,
        out_time: formattedDateTimeOut
    };

    // Send the AJAX request
    $.ajax({
        url: `<?= admin_url("team_management/edit_staff_time_entry") ?>`, // Replace with the actual URL for your endpoint
        type: "POST",
        data: data,
        dataType: "json",
        success: function (response) {
        // Handle the response from the server, e.g., update the table or show a message
        console.log("Edit successful:", response);
        fetchStats();
        alert_float("success", "Time entry edited successfully");
        },
        error: function (xhr, status, error) {
        // Handle any errors that occurred during the request
        console.error("Edit failed:", error);
        },
    });
}   

function deleteStaffTimeEntry(id) {

    const data = {
        entry_id: id
    };

    $.ajax({
        url: `<?= admin_url("team_management/delete_staff_time_entry") ?>`, // Replace with the actual URL for your endpoint
        type: "POST",
        data: data,
        dataType: "json",
        success: function (response) {
            fetchStats();
            alert_float("success", "Time entry deleted successfully");
        },
        error: function (xhr, status, error) {
        // Handle any errors that occurred during the request
        console.error("Edit failed:", error);
        },
    });
}

var timerInterval;
var clockedIn = false;
var startTime;

const clockInBtn = document.getElementById('clock-in');
const clockOutBtn = document.getElementById('clock-out');
const statusSelect = document.getElementById('status');

const liveTimer = document.getElementById('live-timer');

function fetchStats() {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', admin_url + 'team_management/fetch_stats/', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
    xhr.onload = function() {
    if (this.status === 200) {

        var stats = JSON.parse(this.responseText);
        liveTimer.textContent = formatTime(stats.total_time);

        if(stats.status == "Online"){
            if (stats.clock_in_time) {
                clockInBtn.disabled = true;
                clockInBtn.style.opacity = 0.7;
                clockOutBtn.disabled = false;
                clockOutBtn.style.opacity = 1;
                clockedIn = true;
                console.log(stats.clock_in_time);
                startTime = new Date(stats.clock_in_time);
                timerInterval = setInterval(updateLiveTimer, 1000);
            }else{
                clockInBtn.disabled = false;
                clockInBtn.style.opacity = 1;
                clockOutBtn.disabled = true;
                clockOutBtn.style.opacity = 0.7;
                clearInterval(timerInterval);
                clockedIn = false;
            }
        }else{
            clockInBtn.disabled = true;
            clockInBtn.style.opacity = 0.7;
            clockOutBtn.disabled = true;
            clockOutBtn.style.opacity = 0.7;
            clearInterval(timerInterval);
            clockedIn = false;
            
        }
        if(stats.status == "Leave"){
            document.getElementById('Online').disabled = true;
            document.getElementById('AFK').disabled = true;
            document.getElementById('Offline').disabled = true;
            document.getElementById('Leave').disabled = false;
        }else{
            document.getElementById('Online').disabled = false;
            document.getElementById('AFK').disabled = false;
            document.getElementById('Offline').disabled = false;
            document.getElementById('Leave').disabled = true;
        }
        
        statusSelect.value = stats.status;
        statusSelectColors(statusSelect);
        fetchStaffTimeEntries();
    } 
    else {
        alert('Unable to fetch stats. Please try again.');
    }
    
    }
    xhr.send(csrf_token_name + '=' + encodeURIComponent(csrf_token) + "&staff_id=<?= $staff_id ?>");
}

clockInBtn.addEventListener('click', () => {

    var xhr = new XMLHttpRequest();
    xhr.open('POST', admin_url + 'team_management/clock_in/');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);
            if (response.success) {
                clockedIn = true;
                fetchStats();
                clockInBtn.disabled = true;
                timerInterval = setInterval(updateLiveTimer, 1000);
            } else {
                alert('Unable to clock in. Please try again.');
            }
        }
    };
    var requestData = csrf_token_name + '=' + encodeURIComponent(csrf_token) + "&staff_id=<?= $staff_id ?>";
    xhr.send(requestData);
});

clockOutBtn.addEventListener('click', () => {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', admin_url + 'team_management/clock_out/');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);
            if (response.success) {
                fetchStats();
            } else {
                alert('Unable to clock out. Please try again.');
            }
        }
    };

    // Include the CSRF token in the request data
    var requestData = csrf_token_name + '=' + encodeURIComponent(csrf_token) + "&staff_id=<?= $staff_id ?>&force=true";
    xhr.send(requestData);
});

let previousValue = statusSelect.value;
statusSelect.addEventListener('change', (event) => {
    
    var statusText = statusSelect.value;
    
    if (statusSelect != previousValue) {
        
        //Backend Timers
        var xhr = new XMLHttpRequest();
        xhr.open('POST', admin_url + 'team_management/update_status');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.responseText);
                if (!response.success) {
                    alert('Unable to update status. Please try again.');
                }
                fetchStats();
            }
        };

        // Include the CSRF token and status in the request data
        var requestData = csrf_token_name + '=' + encodeURIComponent(csrf_token) + '&statusValue=' + encodeURIComponent(statusText) + "&staff_id=<?= $staff_id ?>";
        xhr.send(requestData);
    }
});


fetchStats();


function convertDateTimeZone(getDateObject) {
    let timeZone = myZone;
    var options = { timeZone: timeZone, hour: 'numeric', minute: 'numeric', second: 'numeric' };
    var localTime = getDateObject.toLocaleString('en-US', options);
    var localTimeArray = localTime.split(/[:\s]/);
    var localDate = new Date(getDateObject.toLocaleDateString('en-US', { timeZone: timeZone }));

    //Convert the hours to 24-hour format if needed
    if (localTimeArray[3] === 'PM') {
        localTimeArray[0] = parseInt(localTimeArray[0], 10) + 12;
    }
    
    localDate.setHours(localTimeArray[0], localTimeArray[1], localTimeArray[2]);
    return localDate;
}

function getCurrentTimeInAsiaKolkata() {
    const now = new Date();
    const timeZone = 'Asia/Kolkata';
    const localTimeString = now.toLocaleString('en-US', { timeZone });
  
    return new Date(localTimeString);
}
function updateLiveTimer() {
    if (clockedIn) {
        var currentTime = getCurrentTimeInAsiaKolkata();
        var elapsedTime = Math.floor((currentTime - startTime) / 1000) + 5;
        document.getElementById('live-timer').textContent = formatTime(elapsedTime );
    }
}
function formatTime(seconds) {
    const hours = Math.floor(seconds / 3600);
    seconds %= 3600;
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;

    return hours.toString().padStart(2, '0') + ':' +
           minutes.toString().padStart(2, '0') + ':' +
           remainingSeconds.toString().padStart(2, '0');
}
function statusSelectColors(element){
    element.classList.remove('text-lime-500');
    element.classList.remove('text-blue-500');
    element.classList.remove('text-pink-500');
    element.classList.add(element.options.namedItem(element.value).classList.item(0));
}
</script>

</body>
</html>