<script>

let editableCampaigns = <?php echo json_encode($editable_campaigns); ?>;

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
                    var permissionsButton = '';
                    var detailsButton = '';
                    var editButton = '';
                    var formButton = '';
                    var deleteButton = '';

                    if(editableCampaigns.includes(row.id)) {
                        permissionsButton = '<button onclick="permissionsEdit(`' + row.id + '`)" class="text-sky-600 hover:text-blue-900">Permissions</button>';
                        detailsButton = '<button onclick="detailEdit(`' + row.id + '`)" class="text-orange-600 hover:text-blue-900">Details</button>';
                        editButton = '<button onclick="initEdit(`' + row.id + '`)" class="text-blue-600 hover:text-blue-900">Edit</button>';
                        formButton = '<a href="' + admin_url + 'recruitment_portal/edit_form/' + row.id + '" class="text-yellow-600 hover:text-yellow-900">Form</a>';
                        deleteButton = '<button onclick="deleteCampaign(`' + row.id + '`)" class="text-red-600 hover:text-red-900">Delete</button>';
                    }

                    return `
                    <div class="flex justify-center w-full gap-4">
                        `+ permissionsButton +`
                        `+ detailsButton +`
                        `+ editButton +`
                        `+ formButton +`
                        `+ deleteButton +`
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

    // Attach submit event listener to the form
    $('#campaignDetailForm').on('submit', function(e) {
        e.preventDefault();

        // Get form data
        const formData = new FormData(this);
        formData.append(csrfData.token_name, csrfData.hash);
        formData.append('details', tinymce.get('detail_edit_content').getContent());

        $.ajax({
            type: 'POST',
            url: '<?= admin_url("recruitment_portal/update_campaign_details"); ?>',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {

                if (response.success) {

                    alert_float('success', 'Campaign updated successfully!');

                    $('#campaignDetailModal').modal('hide');

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
        url: '<?= admin_url("recruitment_portal/get_campaigns"); ?>',
        type: 'GET',
        data: {id: id},
        dataType: 'json',
        success: function(response) {
            var campaign = response.campaigns;
            $('#edit_id').val(id);
            $('#edit_title').val(campaign.title);
            $('#edit_position').val(campaign.position);
            $('#edit_description').val(campaign.description);
            $('#edit_start_date').val(campaign.start_date);
            $('#edit_end_date').val(campaign.end_date);
            $('#edit_status').val(campaign.status);
            $('#edit_salary').val(campaign.salary);
            $('#edit_job_type').val(campaign.job_type);
            $('#edit_experience').val(campaign.experience);
            $('#edit_skills_required').val(campaign.skills_required);
            $('#edit_camp_tag').val(campaign.camp_tag);
            // Show the edit modal
            $('#campaignEditModal').modal('show');
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });
}


function detailEdit(id) {
    $.ajax({
        url: '<?= admin_url("recruitment_portal/get_campaigns"); ?>', // Update this URL to match your controller method
        type: 'GET',
        data: {id: id},
        dataType: 'json',
        success: function(response) {

            var campaign = response.campaigns;
            // Populate the edit form with the campaign data
            
            $('#detail_edit_id').val(id);

            if(campaign.detailed_description){
                tinymce.get('detail_edit_content').setContent(campaign.detailed_description);
            }else{
                tinymce.get('detail_edit_content').setContent("");
            }


            $('#campaignDetailLabel').html("Editing details of " + campaign.title);
            // Show the edit modal
            $('#campaignDetailModal').modal('show');
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });
}

function permissionsEdit(id) {
    $("#permission_campaign").val(id);

    // Uncheck all checkboxes before loading the permissions
    $('.permission_checkbox').prop('checked', false);
    
    $.ajax({
        url: '<?= admin_url("recruitment_portal/get_campaign_permissions"); ?>',  // replace with your controller function that returns permissions
        type: 'POST',
        data: { 
            campaign_id: id,
            [csrfData.token_name]: csrfData.hash,
        },
        dataType: 'json',
        success: function(response) {
            
            if (response.success) {
                // Reset all checkboxes
                $('.permissionCheckbox').prop('checked', false);

                // Check the checkboxes according to the returned permissions
                for (let i = 0; i < response.data.length; i++) {
                    let permission = response.data[i];
                    $('#' + permission.staff_id + '_view').prop('checked', permission.can_view === "1");
                    $('#' + permission.staff_id + '_edit').prop('checked', permission.can_edit === "1");
                    $('#' + permission.staff_id + '_act').prop('checked', permission.can_act === "1");
                }

            }
            else {
                console.error('Error fetching campaign permissions: ' + response.error);
            }
        },
        error: function() {
            console.error('An error occurred while processing the request.');
        }
    });

    $('#permissionsModal').modal('show');
}


function save_permissions() {
    var id = $("#permission_campaign").val();

    // Initialize the permissions object
    var permissions = {};

    // Loop over each staff member row in the table
    $("#staffTable tbody tr").each(function() {
        var staffId = this.id.split('_')[1];  // Get staff ID from row ID

        // Create a new object for this staff member's permissions
        permissions[staffId] = {
            id: staffId,
            view: $('#' + staffId + '_view').is(':checked'),  // Check if the checkbox is checked
            edit: $('#' + staffId + '_edit').is(':checked'),
            act: $('#' + staffId + '_act').is(':checked')
        };
    });

    $.ajax({
        type: 'POST',
        url: '<?= admin_url("recruitment_portal/save_campaign_permissions"); ?>',
        data: {
            [csrfData.token_name]: csrfData.hash,
            campaign_id: id,
            permissions: JSON.stringify(permissions) 
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', 'Permissions saved successfully!');
                $('#permissionsModal').modal('hide');
            }
            else {
                alert('Error updating campaign: ' + response.error);
            }
        },
        error: function() {
            alert('An error occurred while processing the request.');
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

tinymce.init({
  selector: '#detail_edit_content',
  height: '500px'
});


</script>