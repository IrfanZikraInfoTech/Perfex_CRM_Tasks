<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head();

$statusBG = "";
switch($submission->status){
    case '0':
        $statusBG = "bg-gray-100";
        break;
    case '1':
        $statusBG = "bg-red-100";
        break;
    case '2':
        $statusBG = "bg-amber-100";
        break;
    case '3':
        $statusBG = "bg-blue-100";
        break;
    case '4':
        $statusBG = "bg-green-100";
        break;
}

$status = $submission->status;
?>

<div id="wrapper">
    <div class="content">
        <div class="container mx-auto px-4 py-12">
            <h1 class="text-3xl font-semibold mb-6">Submission Details </h1>
            
            <div class="<?= $statusBG ?> shadow-lg rounded-xl p-8" id="submission-details-container">
                <div class="mb-4 text-xl">
                    <p class="font-semibold text-gray-700">Campaign: <span class="text-gray-600"><?= $submission->title ?></span></p>
                </div>
                <table class="min-w-full table-auto text-base bg-white rounded-2xl">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-700 font-semibold">Field</th>
                            <th class="px-4 py-2 text-left text-gray-700 font-semibold">Value</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600">
                        <?php
                        $formData = json_decode($submission->form_data, true);
                        foreach ($formData as $key => $value) {
                            if ($key != 'campaign_id') {
                                echo '<tr class="hover:bg-gray-100 transition-all ease-in-out hover:scale-[1.02]">';
                                echo '<td class="px-4 py-4 capitalize">' . $key . '</td>';

                                if (is_array($value)) {
                                    echo '<td class="px-4 py-4">';
                                    echo '<ul class="list-disc pl-5">';
                                    foreach ($value as $item) {
                                        echo '<li>' . $item . '</li>';
                                    }
                                    echo '</ul>';
                                    echo '</td>';
                                }else if($key == 'resume'){
                                    echo '<td class="px-4 py-2 border"><a target="_blank" href="'.admin_url('recruitment_portal/view_resume/'.$value).'">Resume</a></td>';
                                } 
                                else {
                                    echo '<td class="px-4 py-2 border">' . $value . '</td>';
                                }

                                echo '</tr>';
                                echo '<tr class="border-b border-solid border-gray-300"></tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-center" id="buttons-container">
            <?php if($status == 0){ ?>
                <button onclick="openActionsModal(3)" data-toggle="modal" data-target="#templateActionModal" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg mx-2">Invite for screening call</button>
                <button onclick="openActionsModal(1)" data-toggle="modal" data-target="#templateActionModal" class="bg-red-600 text-white font-semibold px-6 py-2 rounded-lg mx-2">Reject</button>
                <button onclick="openActionsModal(2)" data-toggle="modal" data-target="#templateActionModal" class="bg-yellow-600 text-white font-semibold px-6 py-2 rounded-lg mx-2">On-hold</button>
            <?php } elseif($status == 1){ ?>
                <button onclick="archiveRecord();" class="bg-red-600 text-white font-semibold px-6 py-2 rounded-lg mx-2">Archive</button>
                <button onclick="openActionsModal(2)" data-toggle="modal" data-target="#templateActionModal" class="bg-yellow-600 text-white font-semibold px-6 py-2 rounded-lg mx-2">On-hold</button>
                <button onclick="openActionsModal(3)" data-toggle="modal" data-target="#templateActionModal" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg mx-2">Invite for screening call</button>
            <?php } elseif($status == 2){ ?>
                <button onclick="openActionsModal(1)" data-toggle="modal" data-target="#templateActionModal" class="bg-red-600 text-white font-semibold px-6 py-2 rounded-lg mx-2">Reject</button>
                <button onclick="openActionsModal(3)" data-toggle="modal" data-target="#templateActionModal" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg mx-2">Invite for screening call</button>
                <button onclick="openActionsModal(4)" data-toggle="modal" data-target="#templateActionModal" class="bg-lime-600 text-white font-semibold px-6 py-2 rounded-lg mx-2">Hire</button>
            <?php } elseif($status == 3){ ?>
                <button onclick="openActionsModal(1)" data-toggle="modal" data-target="#templateActionModal" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg mx-2">Reject</button>
                <button onclick="openActionsModal(4)" data-toggle="modal" data-target="#templateActionModal" class="bg-lime-600 text-white font-semibold px-6 py-2 rounded-lg mx-2">Hire</button>
                <button onclick="openActionsModal(2)" data-toggle="modal" data-target="#templateActionModal" class="bg-yellow-600 text-white font-semibold px-6 py-2 rounded-lg mx-2">On-hold</button>
            <?php } elseif($status == 4){ ?>
                <button onclick="archiveRecord()" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg mx-2">Archive</button>
            <?php } ?>
            
        </div>

        </div>
    </div>
</div>

<?php include 'actions_modal.php';?>
<?php init_tail(); ?>

<script>
let globalStatus = 0;
function openActionsModal(status){
    globalStatus = status;
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
    document.getElementById('templateActionLabel').innerHTML = action + " submission";
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
                data: {id: <?= $submission->id ?>},
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
            id: <?= $submission->id ?>,
            status : globalStatus,
            subject : document.getElementById('subject').value,
            body : tinymce.get('body').getContent()
        },
        success : function(response) {
            if(response.success){
                alert_float("success", "Success!");
                $('#templateActionModal').modal('hide');

                let container = document.getElementById('submission-details-container');

                container.classList.remove('bg-red-100');
                container.classList.remove('bg-amber-100');
                container.classList.remove('bg-blue-100');
                container.classList.remove('bg-green-100');


                if(globalStatus === 1){
                    container.classList.add('bg-red-100');
                }else if(globalStatus === 2){
                    container.classList.add('bg-amber-100');
                }else if(globalStatus === 3){
                    container.classList.add('bg-blue-100');
                }
                else if(globalStatus === 4){
                    container.classList.add('bg-green-100');
                }

            }else{
                alert_float("danger", "Unsuccessful!");
            }
        },
        error : function (xhr) {
            console.log(xhr.responseText);
        }
    });
}

function archiveRecord() {
  if (confirm("Are you sure you want to Archive?")) {
    $.ajax({
      url: '<?= admin_url("recruitment_portal/archive_submisson"); ?>',
      type: 'POST',
      dataType: 'json',
      data: {
        submissionId: <?= $submission->id ?>
      },
      success: function(response) {
        if (response.success) {
            $("#buttons-container").html ("<button onclick=\"unarchiveRecord()\" class=\"bg-emerald-600 text-white font-semibold px-6 py-2 rounded-lg mx-2\">Unarchive</button>");
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

function unarchiveRecord() {
  if (confirm("Are you sure you want to Unarchive?")) {
    $.ajax({
      url: '<?= admin_url("recruitment_portal/unarchive_submisson"); ?>',
      type: 'POST',
      dataType: 'json',
      data: {
        submissionId: <?= $submission->id ?>
      },
      success: function(response) {
        if (response.success) {
            location.reload();
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