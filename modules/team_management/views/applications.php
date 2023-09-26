<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper" class="wrapper">
    <div class="content flex flex-col gap-8">

        <!--\\\\\\\ Application Form \\\\\\\\-->
        <div class="w-full flex flex-col gap-4">

            <div class="w-full mb-4">
                <h2 class="text-3xl font-bold text-center">Application Form</h2>
            </div>

            <div class="flex flex-row gap-4">
                <div>Paid Leaves: <?php echo 7 - $paid_no; ?></div>
                <div>Unpaid Leaves: <?php echo 10 - $unpaid_no; ?></div>
                <div>Gazetted Leaves: <?php echo 5 - $gaz_no; ?></div>
            </div>

            <form id="application-form" class="space-y-4 bg-white p-10">
                <div class="flex flex-col">
                    <label for="application_type" class="text-lg font-semibold">Application Type:</label>
                    <select name="application_type" id="application_type" class="w-full p-2 bg-white border border-gray-300 rounded-md">
                    <option value="Paid Leave" class="p-2">Paid Leave</option>
                    <option value="Unpaid Leave" class="p-2">Unpaid Leave</option>
                    <option value="Gazetted Leave" class="p-2">Gazetted Leave</option>
                    <option value="Change Shift Timings" class="p-2">Change Shift Timings</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="start_date" class="text-lg font-semibold">Start Date:</label>
                    <input type="date" name="start_date" id="start_date" required class="w-full p-2 bg-white border border-gray-300 rounded-md">
                </div>
                <div class="flex flex-col">
                    <label for="end_date" class="text-lg font-semibold">End Date:</label>
                    <input type="date" name="end_date" id="end_date" required class="w-full p-2 bg-white border border-gray-300 rounded-md">
                </div>

                <div id="shift-container" style="display:none;">
                    <label for="shift" class="text-lg font-semibold">Shift:</label>
                    <select name="shift" id="shift" class="w-full p-2 bg-white border border-gray-300 rounded-md">
                        <option value="1">1st Shift</option>
                        <option value="2">2nd Shift</option>
                        <option value="all" selected>All day</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="reason" class="text-lg font-semibold">Reason:</label>
                    <textarea name="reason" id="reason" rows="4" cols="50" required class="w-full p-2 bg-white border border-gray-300 rounded-md"></textarea>
                </div>
                <div class="flex flex-col">
                    <label for="attachment" class="text-lg font-semibold">Attachment:</label>
                    <input type="file" name="attachment" id="attachment" class="w-full p-2 bg-white border border-gray-300 rounded-md" />
                </div>
                <input type="submit" value="Submit Application" class="px-4 p-2 text-white bg-blue-600 rounded-md hover:bg-blue-800">
            </form>

        </div>

        <div class="w-full flex flex-col gap-4">

            <div class="w-full mb-4">
                <h2 class="text-3xl font-bold text-center">All Applications</h2>
            </div>

            <div class="align-middle inline-block p-2">
                <table id="all-applications" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted At </th> 
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">

                    </tbody>

                </table>
            </div>

        </div>
        
    </div>
</div>

<?php init_tail(); ?>

<script>

function refreshDataTable() {
    var allApplicationsTable = $('#all-applications').DataTable();

    allApplicationsTable.clear().draw();

    $.ajax({
    url: 'fetch_applications',
    type: 'GET',
    data: { 'staff_id': <?php echo $this->session->userdata('staff_user_id') ?>},
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

}


$(document).ready(function() {

    var allApplicationsTable = $('#all-applications').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: 'fetch_applications',
            type: 'GET',
            dataType: 'json',
            data: {
                staff_id: <?php echo $this->session->userdata('staff_user_id') ?>
            },
            dataSrc: 'applications'
        },
        columns: [
            // Your column definitions here, e.g.:
            {
                "data": "id"
            },
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
                "width": "40%"
            },
            { data: 'created_at' }
        ],
        initComplete: function() {
            $('#all-applications_wrapper').removeClass('table-loading');
        },
        order: [[0, 'desc']]
    });


    $("#application-form").submit(function(event) {
        alert_float('info', 'Sending...');
        event.preventDefault(); // Prevent the default form submission behavior

        var formData = new FormData(this);
        formData.append(csrfData.token_name, csrfData.hash);

        $.ajax({
            url: "submit_application",
            type: "POST",
            data: formData,
            dataType: "json", // Expect a JSON response from the server
            processData: false, // Tell jQuery not to process the data
            contentType: false,
            success: function(response) {
                // Check if the controller returned success: true or false
                if (response.success) {
                // Handle the successful submission here
                alert_float('success', 'Application submitted successfully!');
                
                $("#start_date").val("");
                $("#end_date").val("");
                $("#reason").val("");
                $("#attachment").val("");

                refreshDataTable();
                } else {
                // Handle any errors that occurred during submission
                alert_float('danger', 'There was an error submitting the application: ');
                }
            },
            error: function(xhr, status, error) {
                // Handle any errors that occurred during the AJAX request itself
                console.error(status, error);
                alert_float('danger', 'There was an error submitting the application.');
            },
        });


    });

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

});


</script>



</body>
</html>