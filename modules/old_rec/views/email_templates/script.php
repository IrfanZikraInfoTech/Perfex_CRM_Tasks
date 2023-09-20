<script>
document.addEventListener('DOMContentLoaded', function() {
    var templatesTable = $('#templates_table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '<?= admin_url("recruitment_portal/get_email_templates"); ?>',
            type: 'GET',
            dataType: 'json',
            dataSrc: 'email_templates'
        },
        columns: [
            { data: 'template_name' },
            { data: 'template_subject' },
            {
                data: 'template_body',
                "width": "40%",
                render: function(data, type, row) {
                    if (type === 'display') {
                        if (data.length > 100) { // Or however long you want the snippet to be
                            return '<span title="' + data + '">' + data.substr(0, 100) + '...</span>'; 
                        }
                    }
                    return data;
                }
            },

            { 
                data: null,
                "width": "15%",
                "render": function ( data, type, row ) {
                    return `
                    <div class="flex justify-center w-full gap-4">
                        <button onclick="initEdit(` + row.id + `)" class="text-blue-600 hover:text-blue-900">Edit</button>
                        <button onclick="deleteTemplate(` + row.id + `)" class="text-red-600 hover:text-red-900">Delete</button>
                    </div>
                    `;
                }
            },
        ],
        initComplete: function() {
            $('#templates_table_wrapper').removeClass('table-loading');
        },
        order: [[0, 'desc']]
    });

    $('#templateAddForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append(csrfData.token_name, csrfData.hash);
        let bodyContent = tinymce.get('body').getContent();
        formData.append('body', bodyContent);

        if(!bodyContent){
            alert('Body is required!');
            return;
        }

        $.ajax({
            type: 'POST',
            url: '<?= admin_url("recruitment_portal/add_email_template"); ?>',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    alert_float("success", "Template Added");
                    $('#templateAddModal').modal('hide');
                    $('#templateAddForm')[0].reset();
                    refreshTemplatesTable();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('An error occurred while processing the request.');
            }
        });
    });

    $('#templateEditForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append(csrfData.token_name, csrfData.hash);
        let editBodyContent = tinymce.get('edit_body').getContent();
        formData.append('body', editBodyContent);

        $.ajax({
            type: 'POST',
            url: '<?= admin_url("recruitment_portal/update_email_template"); ?>',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    alert_float('success', 'Template updated successfully!');
                    refreshTemplatesTable();
                    $('#templateEditModal').modal('hide');
                }
                else {
                    alert('Error updating template: ' + response.error);
                }
            },
            error: function() {
                alert('An error occurred while processing the request.');
            }
        });
    });
});

function refreshTemplatesTable() {
    var templatesTable = $('#templates_table').DataTable();
    templatesTable.clear().draw();
    $.ajax({
        url: '<?= admin_url("recruitment_portal/get_email_templates"); ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            var data = response.email_templates;
            var i = 1;
            data.forEach(function(row) {
                row.index = i;
                i++;
            });
            templatesTable.rows.add(data).draw();
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });
}

function initEdit(id) {
    $.ajax({
        url: '<?= admin_url("recruitment_portal/get_email_templates"); ?>', // Update this URL to match your controller method
        type: 'GET',
        data: {id: id},
        dataType: 'json',
        success: function(response) {
            var template = response.email_templates;
            // Populate the edit form with the template data
            $('#edit_id').val(id);
            $('#edit_name').val(template.template_name);
            $('#edit_subject').val(template.template_subject);
            tinymce.get('edit_body').setContent(template.template_body);

            $.ajax({
                url: '<?= admin_url("recruitment_portal/get_email_template_campaigns"); ?>',
                type: 'GET',
                data: {id: id},
                dataType: 'json',
                success: function(response) {
                    var campaignIds = response.campaignIds;
                    
                    // Loop over the select options and set the selected attribute for matching options
                    $("#edit_campaign_ids option").each(function() {
                        if ($.inArray($(this).val(), campaignIds) !== -1) {                            
                            $(this).prop("selected", true);
                        }
                    });
                    
                    // Trigger the select update (if using a plugin like select2)
                    $('#edit_campaign_ids').trigger('change');
                }
            });

            // Show the edit modal
            $('#templateEditModal').modal('show');
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });
}

function deleteTemplate(templateId) {
  if (confirm("Are you sure you want to delete this template?")) {
    $.ajax({
      url: '<?= admin_url("recruitment_portal/delete_email_template"); ?>',
      type: 'POST',
      dataType: 'json',
      data: {
        templateId: templateId
      },
      success: function(response) {
        if (response.success) {
          // Refresh the templates table or remove the deleted row
          refreshTemplatesTable();
          alert_float('success', 'Template deleted successfully!');
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

tinymce.init({
  selector: '#body',
});

tinymce.init({
  selector: '#edit_body',
});

</script>
