<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="w-full mx-auto">
            <div class="bg-white shadow-md rounded-md p-6">
                <div class="mb-10 w-full justify-between flex justify-between">

                    <h4 class="text-2xl font-semibold float-left w-2/5"><?= $campaign_name ?></h4>

                    <select id="status_select" class="w-1/5 rounded transition-all border-blue-500 focus:ring-blue-00 hover:bg-white border border-solid w-44 px-2 py-2">
                        <option value="-1" <?php if($status == -1) echo 'selected'; ?>>All</option>
                        <option value="0" <?php if($status == 0) echo 'selected'; ?>>Unviewed</option>
                        <option value="1" <?php if($status == 1) echo 'selected'; ?>>Rejected</option>
                        <option value="2" <?php if($status == 2) echo 'selected'; ?>>On-Hold</option>
                        <option value="3" <?php if($status == 3) echo 'selected'; ?>>Invited</option>
                        <option value="4" <?php if($status == 4) echo 'selected'; ?>>Hired</option>
                        <option value="5" <?php if($status == 5) echo 'selected'; ?>>Archived</option>
                    </select>


                    <div class="w-2/5 flex justify-end ">
                        <a href="<?= admin_url() ?>recruitment_portal/submissions" class="rounded transition-all bg-blue-600 text-white hover:bg-white hover:text-blue-500 hover:border-blue-500 border border-solid px-4 py-2">Go Back</a>
                    </div>
                </div>
                <div>
                    <table id="submissions_table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>
                                    Name
                                </th>
                                <th>
                                    Campaign
                                </th>
                                <th>
                                    Status
                                </th>
                                <th>
                                    Date
                                </th>
                                <th>
                                    View
                                </th>
                                <th>
                                    Quick
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'actions_modal.php';?>
<?php init_tail(); ?>

<?php include('script.php'); ?>

<script>
let globalStatus = 0;
let globalName = "";
let globalId = 0;
function openActionsModal(id, name, status){
    globalStatus = status;
    globalId = id;
    globalName = name;
    let action = "";
    if(status === 1){
        action = "Reject";
    }else if(status === 2){ 
        action = "On-hold";
    }else if(status === 3){
        action = "Invite";
    }else if(status === 4){
        action = "Hire";
    }
    document.getElementById('templateActionLabel').innerHTML = action + " "+name;
    document.getElementById('templateActionSubmit').innerHTML = action;
}

function fillEmailDetails(id) {
    $.ajax({
        url: '<?= admin_url("recruitment_portal/get_email_templates"); ?>',
        type: 'GET',
        data: {id: id},
        dataType: 'json',
        success: function(response) {
            var subject = "";
            var body = "";
            if(response.email_templates){
                subject = response.email_templates.template_subject;
                body = response.email_templates.template_body;
            }

            // Nested AJAX call
            $.ajax({
                url: '<?= admin_url("recruitment_portal/get_submission_data"); ?>',
                type: 'GET',
                data: {id: globalId},
                dataType: 'json',
                success: function(response) {
                    let data = response[0];
                    subject = subject.replace("{candidate_name}", data.candidate_name).replace("{campaign_name}", data.campaign_name).replace("{position_name}",data.position_name).replace("{submission_date}",data.submission_date);
                    body = body.replace("{candidate_name}", data.candidate_name).replace("{campaign_name}", data.campaign_name).replace("{position_name}",data.position_name).replace("{submission_date}",data.submission_date);
                    document.getElementById('subject').value = subject;
                    tinymce.get('body').setContent(body);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });
}

function changeStatus(){
    alert_float("info", "Sending...");
    $.ajax({
        url: '<?= admin_url("recruitment_portal/act_submission"); ?>',
        type: 'POST',
        dataType: 'json',
        data: {
            id: globalId,
            status : globalStatus,
            subject : document.getElementById('subject').value,
            body : tinymce.get('body').getContent()
        },
        success : function(response) {
            if(response.success){
                alert_float("success", "Success!");
                $('#templateActionModal').modal('hide');

                $("#status_"+globalId+"_span").replaceWith(formatStatus(globalStatus, globalId));
                $("#quick_buttons_"+globalId).replaceWith(formatQuickButtons(globalStatus, globalName, globalId));

            }else{
                alert_float("danger", "Unsuccessful!");
            }
        },
        error : function (xhr) {
            console.log(xhr.responseText);
        }
    });
}

function archiveRecord(submissionId) {
  if (confirm("Are you sure you want to Archive?")) {
    $.ajax({
      url: '<?= admin_url("recruitment_portal/archive_submisson"); ?>',
      type: 'POST',
      dataType: 'json',
      data: {
        submissionId: submissionId
      },
      success: function(response) {
        if (response.success) {
            $('#submissions_table').DataTable().row("#row_"+submissionId).remove().draw();
            alert_float('success', 'Archived Successfully!');
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

function unarchiveRecord(submissionId) {
  if (confirm("Are you sure you want to Unarchive?")) {
    $.ajax({
      url: '<?= admin_url("recruitment_portal/unarchive_submisson"); ?>',
      type: 'POST',
      dataType: 'json',
      data: {
        submissionId: submissionId
      },
      success: function(response) {
        if (response.success) {
            $('#submissions_table').DataTable().row("#row_"+submissionId).remove().draw();
            alert_float('success', 'Unarchived Successfully!');
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
  height : "300"
});
</script>

</body>
</html>