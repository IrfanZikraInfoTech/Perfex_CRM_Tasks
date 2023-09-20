<script>
    
document.addEventListener('DOMContentLoaded', function() {

    //Datatable init code
    var campaignsTable = $('#campaigns_table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '<?= admin_url("recruitment_portal/get_campaigns"); ?>',
            type: 'GET',
            dataType: 'json',
            dataSrc: 'campaigns'
        },
        columns: [
            { data: 'title' },
            { data: 'position' },
            { 
                data: null,
                "render": function ( data, type, row ) {
                    return row.start_date + " to " + (row.end_date ? 'Ongoing' : '');
                }
            },
            { 
                data: null,
                "render": function ( data, type, row ) {
                    return formatStatus(row.status);
                }
            },
            { 
                data: null,
                "width": "15%",
                "render": function ( data, type, row ) {
                    return `
                    <div class="flex justify-center w-full gap-4">
                        <button onclick="initEdit(` + row.id + `)" class="text-blue-600 hover:text-blue-900">Edit</button>
                        <a href="`+admin_url +`recruitment_portal/edit_form/`+ row.id + `" class="text-yellow-600 hover:text-yellow-900">Form</a>
                        <button onclick="deleteCampaign(` + row.id + `)" class="text-red-600 hover:text-red-900">Delete</button>
                    </div>
                    `;
                }
            },
        ],
        initComplete: function() {
            $('#campaigns_table_wrapper').removeClass('table-loading');
        },
        order: [[0, 'desc']]
    });


    // Attach submit event listener to the form
    $('#campaignAddForm').on('submit', function(e) {
        e.preventDefault();

        // Get form data
        const formData = new FormData(this);
        formData.append(csrfData.token_name, csrfData.hash);

        $.ajax({
            type: 'POST',
            url: '<?= admin_url("recruitment_portal/add_campaign"); ?>',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    // Display success message
                    alert_float("success", "Campaign Added");

                    // Close the modal
                    $('#campaignAddModal').modal('hide');

                    // Clear the form
                    $('#campaignAddForm')[0].reset();

                    //Refresh datatable
                    refreshCampaignsTable();

                } else {
                    // Display error message
                    alert(response.message);
                }
            },
            error: function() {
                alert('An error occurred while processing the request.');
            }
        });

    });

    // Attach submit event listener to the form
    $('#campaignEditForm').on('submit', function(e) {
        e.preventDefault();

        // Get form data
        const formData = new FormData(this);
        formData.append(csrfData.token_name, csrfData.hash);

        $.ajax({
            type: 'POST',
            url: '<?= admin_url("recruitment_portal/update_campaign"); ?>',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {

                if (response.success) {

                    alert_float('success', 'Campaign updated successfully!');

                    refreshCampaignsTable();

                    $('#campaignEditModal').modal('hide');

                }
                else {
                    alert('Error updating campaign: ' + response.error);
                }
            
            },
            error: function() {
                alert('An error occurred while processing the request.');
            }
        });

    });


});

function refreshCampaignsTable() {
    var campaignsTable = $('#campaigns_table').DataTable();

    campaignsTable.clear().draw();

    $.ajax({
        url: '<?= admin_url("recruitment_portal/get_campaigns"); ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            var data = response.campaigns;

            var i = 1;
            data.forEach(function(row) {
                row.index = i;
                i++;
            });
            campaignsTable.rows.add(data).draw();
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });
}

function initEdit(id) {
    $.ajax({
        url: '<?= admin_url("recruitment_portal/get_campaigns"); ?>', // Update this URL to match your controller method
        type: 'GET',
        data: {id: id},
        dataType: 'json',
        success: function(response) {
            var campaign = response.campaigns;
            // Populate the edit form with the campaign data
            
            $('#edit_id').val(id);
            $('#edit_title').val(campaign.title);
            $('#edit_position').val(campaign.position);
            $('#edit_description').val(campaign.description);
            $('#edit_start_date').val(campaign.start_date);
            $('#edit_end_date').val(campaign.end_date);
            $('#edit_status').val(campaign.status);
            $('#edit_salary').val(campaign.salary);

            // Show the edit modal
            $('#campaignEditModal').modal('show');
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });
}

function deleteCampaign(campaignId) {
  if (confirm("Are you sure you want to delete this campaign?")) {
    $.ajax({
      url: '<?= admin_url("recruitment_portal/delete_campaign"); ?>',
      type: 'POST',
      dataType: 'json',
      data: {
        campaignId: campaignId
      },
      success: function(response) {
        if (response.success) {
          // Refresh the campaigns table or remove the deleted row
          refreshCampaignsTable();
          alert_float('success', 'Campaign deleted successfully!');
        } else {
          alert(response.error);
        }
      },
      error: function(xhr) {
        console.log(xhr.responseText);
      }
    });
  }
}


function formatStatus(status) {
    if(status == 1) {
        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>';
    }else{
        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-green-800">Inactive</span>';
    }
}

</script>