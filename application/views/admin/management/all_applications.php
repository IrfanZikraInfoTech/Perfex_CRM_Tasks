<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper" class="wrapper">
    <div class="content flex flex-col gap-8">

        <div class="w-full flex flex-col gap-4">
            <div class="w-full mb-4">
                
                <h2 class="text-3xl font-bold text-center">Applications of Staff Reporting me</h2>

                <div class="flex flex-row flex-wrap px-10 justify-center gap-4 mx-auto mt-6 mb-2">
                    <?php
                        foreach($staff_under as $staff){
                            ?>

                        <button onclick="staffDetails(<?= $staff['id'] ?>, '<?= htmlspecialchars($staff['name']) ?>')" title="<?= $staff['name'] ?>" data-toggle="tooltip" data-placement="top">
                            <?= staff_profile_image($staff['id'], ['border-2 border-solid object-cover w-12 h-12 staff-profile-image-thumb'], 'thumb'); ?>
                        </button>

                            <?php
                        }
                    ?>
                </div>

            </div>

            <div class="self-center flex flex-row gap-4">
                <button onclick="refreshDataTable('Pending')" class="btn-primary p-2 px-4 text-white">Pending</button>
                <button onclick="refreshDataTable('Approved')" class="btn-primary p-2 px-4 text-white">Approved</button>
                <button onclick="refreshDataTable('Disapproved')" class="btn-primary p-2 px-4 text-white">Disapproved</button>
            </div>


            <table id="all-applications" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID </th>
                        <th>User</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Period</th>
                        <th>Shift</th>
                        <th>Reason</th>   
                        <th>Action</th>                
                    </tr>
                </thead>
            </table>
        </div>

    </div>
</div>

<div class="modal fade" id="attachmentModal" tabindex="-1" aria-labelledby="attachmentModalLabel" aria-hidden="true">
    <div class="max-w-3xl mx-auto my-3 flex flex-col justify-center h-full">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-xl" id="attachmentModalLabel">Preview Attachment</h5>
            </div>
            <div class="modal-body p-4">

                <img id="img-render" class="h-full mx-auto" />
                <div id="pdf-render">PDF Downloading</div>

            </div>


            <div class="modal-footer flex justify-between">
                <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>        
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="leaveModal" tabindex="-1" aria-labelledby="leaveModalLabel" aria-hidden="true" data-backdrop="true">
    <div class="max-w-[90%] mx-auto my-3 flex flex-col justify-center h-full">
        <div class="modal-content">
            <div class="modal-header flex flex-row">
                <h5 class="modal-title text-xl" id="leaveModalLabel"></h5>
                <button type="button" class="ml-auto btn btn-secondary" data-dismiss="modal"><i class="fa fa-close"></i></button>
            </div>
            <div class="modal-body p-4 flex lg:flex-row flex-col gap-4 mb-4">
                
            </div>


            <div class="modal-footer flex justify-between">
                <div class="w-full">
                    <button type="button" class="btn btn-secondary w-full" data-dismiss="modal">Close</button>
                </div>        
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
var currentStatusViewing = 'Pending';
$(document).ready(function() {

    var allApplicationsTable = $('#all-applications').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: 'fetch_applications',
            type: 'GET',
            dataType: 'json',
            data: {
                staff_id: 0,
                status : 'Pending'
            },
            dataSrc: 'applications'
        },
        columns: [
            // Your column definitions here, e.g.:
            {
                "data": 'id',
            },
            {
                "data": null,
                "width" : "10%",
                "render": function(data, type, row) {
                    return '<div class="flex flex-row items-center gap-2"><div>'+row.user_pfp+'</div><button data-toggle="modal" data-target="#attachmentModal" onclick="attachmentModal('+row.id+')">'+row.user_name+'</button></div>';
                }
            },
            { data: 'created_at' },
            { data: 'application_type' },
            { data: 'status' },
            { 
                "data": null,
                "render": function ( data, type, row ) {
                    if(row.start_date != row.end_date){
                        return row.start_date + " to " + row.end_date;
                    }else{
                        return row.start_date;
                    }
                    
                }
            },
            {
                data: null,
                "render": function ( data, type, row ) {
                    if(row.shift == "all") shiftTxt = "Full Day";
                    else if(row.shift=="1") shiftTxt = "1st Shift";
                    else if(row.shift=="2") shiftTxt = "2nd Shift";
                    return shiftTxt;
                }
            },
            { 
                data: 'reason',
                "width": "30%"
            },
            { 
                data: null,
                "width": "10%",
                "render": function ( data, type, row ) {

                    if(data['is_editable'] == false){
                        return `

                        <button onclick="requestAdmin('`+row.id+`');" class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                            Request Admins
                        </button>

                    `;
                    }else{
                    return `
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                            Change status
                        </button>
                        <div class="dropdown-menu p-2" aria-labelledby="dropdownMenuButton">
                            <button onclick="changeStatus('Pending', '`+row.id+`');" class="dropdown-item w-full mb-1 btn-primary p-2 px-4 text-white">Pending</button>
                            <button onclick="changeStatus('Approved', '`+row.id+`');" class="dropdown-item w-full mb-1 btn-primary p-2 px-4 text-white">Approve</button>
                            <button onclick="changeStatus('Disapproved', '`+row.id+`');" class="dropdown-item w-full mb-1 btn-primary p-2 px-4 text-white">Disapprove</button>
                            <button onclick="deleteApplication('`+row.id+`');" class="dropdown-item w-full mb-1 btn-primary p-2 px-4 text-white">Delete</button>
                        </div>
                    </div>
                    `;
                    }
                }
            },
        ],
        initComplete: function() {
            $('#all-applications_wrapper').removeClass('table-loading');


            const urlParams = new URLSearchParams(window.location.search);
            const idParam = urlParams.get('id');
            // Set the value of the input element with the ID 'all-applications_filter' to the 'id' query parameter
            if(idParam){
                const inputElement = document.querySelector('#all-applications_filter input');
                if (inputElement) {
                    inputElement.value = idParam;

                    if (idParam) {
                        $('#all-applications').DataTable().search(idParam).draw();
                    }
                    
                }
            }
            
        },
        order: [[0, 'desc']],
        "createdRow": function (row, data, dataIndex) {

            if(data['is_editable'] == false){
                $(row).addClass("!bg-pink-100 hover:!bg-gray-200 transition-all");
            }
        }
    });

});

function refreshDataTable(status) {
    var allApplicationsTable = $('#all-applications').DataTable();

    if(status == null){
        status = currentStatusViewing;
    }

    allApplicationsTable.clear().draw();   

    $.ajax({
    url: 'fetch_applications',
    type: 'GET',
    data: {staff_id: 0, 'status': status},
    dataType: 'json',
    success: function(response) {

      var data = response.applications;

      console.log(data);

      var i = 1;
      data.forEach(function(row) {
        row.index = i;
        i++;
      });
      allApplicationsTable.rows.add(data).draw();

    },
    error: function(xhr) {
      console.log(xhr.responseText);
    }
  });

  currentStatusViewing = status;

}

function changeStatus(newStatus, applicationId) {

alert_float('info', 'Sending Mail...');
$.ajax({
  url: 'change_application_status', // Replace this with the appropriate URL of your endpoint
  type: 'POST',
  dataType: 'json',
  data: {
    [csrfData.token_name]: csrfData.hash,
    id: applicationId,
    status: newStatus
  },
  success: function (response) {
      if (response.success) {
        // Handle the successful status change here, e.g., update the UI, show a notification
        alert_float('success', 'Status changed successfully!');
        refreshDataTable(newStatus);
      } else {
        // Handle any errors that occurred during the status change
        console.error('Error changing status:', response.message);
      }
  },
  error: function (xhr, status, error) {
      alert_float('danger', 'There was an error changing the status');
  },
});
}

function deleteApplication(applicationId) {
  $.ajax({
    url: 'delete_application', // Replace this with the appropriate URL of your endpoint
    type: 'POST',
    dataType: 'json',
    data: {
      [csrfData.token_name]: csrfData.hash,
      id: applicationId
    },
    success: function (response) {

        if (response.success) {
          // Handle the successful status change here, e.g., update the UI, show a notification
          alert_float('success', 'Deleted Successfully!');
          refreshDataTable();
        } else {
          // Handle any errors that occurred during the status change
          console.error('Error deleting:');
        }
    },
    error: function (xhr, status, error) {
        alert_float('danger', 'There was an error changing the status');
    },
  });
}

function attachmentModal(id) {
    //../../

    const filename = 'application_'+id;

    fetch(`get_file_type?filename=${filename}`)
    .then(response => response.json())
    .then(data => {
        if (data.file_type === 'image') {

            //document.getElementById("")

            $("#img-render").attr("src", "../../uploads/applications/"+filename+"."+data.ext);
            $("#pdf-render").hide();
            $("#img-render").show();

        } else if (data.file_type === 'pdf') {

            $("#pdf-render").show();
            $("#img-render").hide();

            const url = "../../uploads/applications/"+filename+"."+data.ext;
            downloadPDF(url);

            $("#pdf-render").html("PDF Downloading!");

        } else {
            $("#pdf-render").show();
            $("#img-render").hide();
            $("#pdf-render").html("No attachment!");
        }
        console.log(data);
    })
    .catch(error => {
        console.error('Error:', error);
    });

}

function downloadPDF(url) {
  // Create an anchor element
  const link = document.createElement('a');

  // Set the URL and the download attribute
  link.href = url;
  link.download = 'Attachment.pdf';

  // Append the link to the document, trigger a click event, and remove the link
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

function requestAdmin(applicationId) {

alert_float('info', 'Sending Mail...');
$.ajax({
  url: 'application_request_admin', // Replace this with the appropriate URL of your endpoint
  type: 'POST',
  dataType: 'json',
  data: {
    [csrfData.token_name]: csrfData.hash,
    id: applicationId,
  },
  success: function (response) {
      if (response.success) {
        // Handle the successful status change here, e.g., update the UI, show a notification
        alert_float('success', 'Request Sent!!');
      } else {
        // Handle any errors that occurred during the status change
        alert_float('danger', 'Some error');
      }
  },
  error: function (xhr, status, error) {
      alert_float('danger', 'There was an error sending the mail');
  },
});
}

function staffDetails(staffId, name) {
    $.ajax({
        url: '<?= admin_url('team_management/get_staff_leave_details') ?>/' + staffId, // Update the path accordingly
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $("#leaveModalLabel").text("Leave details of "+name);

            let paidLeavesAllowed = <?= get_option('paid_leaves') ?>;
            let unpaidLeavesAllowed = <?= get_option('unpaid_leaves') ?>;
            let gazLeavesAllowed = <?= (int)get_option('gaz_leaves') ?>;

            let paidDifference = paidLeavesAllowed - data.app_paid_no;
            let unpaidDifference = unpaidLeavesAllowed - data.app_unpaid_no;
            let gazDifference = gazLeavesAllowed - data.app_gaz_no;

            let paidDisplayText = paidDifference >= 0 ? `${paidDifference} left` : `${Math.abs(paidDifference)} Exceeded`;
            let unpaidDisplayText = unpaidDifference >= 0 ? `${unpaidDifference} left` : `${Math.abs(unpaidDifference)} Exceeded`;
            let gazDisplayText = gazDifference >= 0 ? `${gazDifference} left` : `${Math.abs(gazDifference)} Exceeded`;

            let modalBody = `

                    <!-- Paid Leaves Details -->
                    <div class="bg-blue-100 rounded-lg p-4 shadow w-full">
                        <div class="flex flex-row justify-between">
                            <h3 class="text-xl font-bold mb-2">Paid Leaves:</h3>
                            <div><h2 class="px-4 flex items-center justify-center h-5 rounded-full bg-white text-black">${paidDisplayText}</h2></div>
                        </div>
                        <div class="text-lg font-semibold flex xl:flex-row flex-col justify-between">
                            <div>Pending: ${data.pen_paid_no}</div>
                            <div>Approved: ${data.app_paid_no}</div>
                            <div>Disapproved: ${data.dis_paid_no}</div>
                        </div>
                    </div>
                    <!-- Unpaid Leaves Details -->
                    <div class="bg-green-100 rounded-lg p-4 shadow w-full">
                        <div class="flex flex-row justify-between">
                            <h3 class="text-xl font-bold mb-2">Unpaid Leaves:</h3>
                            <div><h2 class="px-4 flex items-center justify-center h-5 rounded-full bg-white text-black">${unpaidDisplayText}</h2></div>
                        </div>
                        <div class="text-lg font-semibold flex xl:flex-row flex-col justify-between">
                            <div>Pending: ${data.pen_unpaid_no}</div>
                            <div>Approved: ${data.app_unpaid_no}</div>
                            <div>Disapproved: ${data.dis_unpaid_no}</div>
                        </div>
                    </div>
                    <!-- Gazetted Leaves Details -->
                    <div class="bg-yellow-100 rounded-lg p-4 shadow w-full">
                        <div class="flex flex-row justify-between">
                            <h3 class="text-xl font-bold mb-2">Gazetted Leaves:</h3>
                            <div><h2 class="px-4 flex items-center justify-center h-5 rounded-full bg-white text-black">${gazDisplayText}</h2></div>
                        </div>
                        <div class="text-lg font-semibold flex xl:flex-row flex-col justify-between">
                            <div>Pending: ${data.pen_gaz_no}</div>
                            <div>Approved: ${data.app_gaz_no}</div>
                            <div>Disapproved: ${data.dis_gaz_no}</div>
                        </div>
                    </div>
            `;



            $('.modal-body').html(modalBody);
            $("#leaveModal").modal('show');
        },
        error: function(error) {
            console.error("Error fetching staff leave details:", error);
        }
    });
}


</script>


</body>
</html>