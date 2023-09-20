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
        <div class="w-full px-2">
            <div class="flex flex-row">
                <h1 class="text-2xl font-semibold mb-6">Submission Details # <?= $submission->id  ?></h1>
                
                <div class="ml-auto">

                    <div class="flex flex-row gap-4">
                    <button class="rounded transition-all bg-yellow-500 text-white hover:bg-white hover:text-yellow-500 hover:border-yellow-500 border border-solid px-4 py-2 items-center" onclick="toggleFavorite(this);">
                    <i class="fa fa-star"></i> 
                        <span id="fav-label"><?= ($submission->is_favorite) ? 'Unmark as favorite' : 'Mark as favorite'; ?></span>
                    </button>


                        <a href="<?= admin_url("recruitment_portal/submissions/".$submission->campaign_id) ?>" class="rounded transition-all bg-blue-600 text-white hover:bg-white hover:text-blue-500 hover:border-blue-500 border border-solid px-4 py-2">Go Back</a>
                    </div>
                </div>
            </div>


            <div class="flex xl:flex-row flex-col gap-4">

                <div class="flex flex-col <?php if($can_act){ echo 'xl:w-2/5 w-full';} else{echo 'w-full';}?> ">
                <div class="<?= $statusBG ?> shadow-lg rounded-xl py-4 px-8  <?php if($can_act){ echo 'sticky top-4';}?>" id="submission-details-container">
                    <div class="mb-4 text-xl">
                        <p class="font-semibold text-gray-700">Campaign: <span class="text-gray-600"><?= $submission->title ?></span></p>
                    </div>
                    <div class="mt-10 text-base bg-white rounded-xl p-4">
                        <?php
                        $formData = json_decode($submission->form_data, true);
                        foreach ($formData as $key => $value) {
                            if ($key != 'campaign_id') {
                                echo '<div class="border-b border-solid border-gray-300 py-4">';

                                echo '<div class="text-left text-gray-700 font-semibold mb-2">' . ucfirst($key) . '</div>';

                                if (is_array($value)) {
                                    echo '<div class="text-gray-600 pl-5 list-disc">';
                                    foreach ($value as $item) {
                                        echo '<li>' . $item . '</li>';
                                    }
                                    echo '</div>';
                                }else if($key == 'resume'){
                                    echo '<div class="text-gray-600"><a class="text-blue-500 hover:text-blue-600" target="_blank" href="'.admin_url('recruitment_portal/view_resume/'.$value).'">Resume</a></div>';
                                }
                                else {
                                    if(filter_var($value, FILTER_VALIDATE_URL)) {
                                        echo '<div class="text-blue-600"><a href="'. $value .'" target="_blank">'. $value .'</a></div>';
                                    } else {
                                        echo '<div class="text-gray-600">'. $value .'</div>';
                                    }
                                }

                                echo '</div>';
                            }
                        }
                        ?>
                    </div>

                    <?php if($can_act){ ?>

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

                    <?php } ?>

                </div>
                </div>

                <?php if($can_act){ ?>

                <!-- Messaging System Section -->
                <div class="xl:w-3/5 w-full shadow-lg rounded-xl p-4 h-full relative" id="messaging-container">
                    
                    <ul class="nav nav-tabs">

                        <li class="active w-1/2 text-center">
                            <a data-toggle="tab" href="#messaging" class="">Messaging</a>
                        </li>
                        <li class="w-1/2 text-center">
                            <a data-toggle="tab" href="#notes" class="">Notes</a>
                        </li>
                    </ul>


                    <div class="tab-content">

                        <div id="messaging" class="tab-pane fade in active">

                            <div class="top-0 left-0 w-full">

                                <div class="mb-6">

                                    <select id="inline_action_campaign_ids" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500" onchange="fillEmailDetails(this.value, 'Inline')">
                                    <option disabled selected>Select Template</option>
                                    <?php foreach($templates as $template): ?>
                                        <option value="<?php echo $template->template_id; ?>"><?php echo $template->template_name; ?></option>
                                    <?php endforeach; ?>
                                    </select>
                                </div>

                                <form id="sendMessageForm" class="flex flex-col gap-4 items-center">
                                    <input id="subjectInline" name="subject" placeholder="Subject..." type="text" required class="w-full rounded border border-gray-200 p-2" />
                                    <textarea id="bodyInline" class="w-full rounded border border-gray-200 p-2 mr-2 resize-none" rows="3" placeholder="Write a message..."></textarea>
                                    <button type="submit" class="w-full bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg">Send</button>
                                </form>
                            </div>

                            <div class="rounded border border-gray-200 mt-10 text-lg" id="messages-container">
                                <!-- Dummy messages -->
                                <?php
                                    foreach($messages as $message){

                                        $colors = ($message["sent_by"] == "admin") ? "from-blue-500 to-blue-300" : "from-green-500 to-green-300";

                                        echo '
                                        <div class="my-4 flex flex-col border shadow-lg rounded-lg overflow-hidden">
                                            <div class="px-4 py-2 bg-gradient-to-r '.$colors.' text-white font-semibold">'.$message["subject"].'</div>
                                            <div class="px-4 py-2 bg-white text-black w-full">'.$message["message"].'</div>
                                            <div class="px-4 py-2 bg-gray-200 text-right text-xs text-gray-600">'.ucfirst($message["sent_by"]).', '.date("F jS, Y", strtotime($message["created_at"])).'</div>
                                        </div>
                                        ';
                                    }
                                    ?>


                                
                                <!-- End of Dummy messages -->
                            </div>
                        </div>

                        <div id="notes" class="tab-pane fade">
                            <div class="top-0 left-0 w-full">

                                <form id="addNoteForm" class="flex flex-col gap-4 items-center mb-6">
                                    <input id="noteTitle" name="noteTitle" placeholder="Note Title..." type="text" required class="w-full rounded border border-gray-200 p-2" />
                                    <textarea id="noteBody" class="w-full rounded border border-gray-200 p-2 mr-2 resize-none" rows="3" placeholder="Write a note..."></textarea>
                                    <button type="submit" class="w-full bg-green-600 text-white font-semibold px-4 py-2 rounded-lg">Add Note</button>
                                </form>

                                <div class="rounded border border-gray-200 mt-10 text-lg" id="notes-container">
                                    <!-- Dummy notes -->
                                    <?php
                                        foreach($notes as $note){
                                            echo '
                                            <div class="my-4 border border-solid border-gray-200 shadow-lg rounded-lg overflow-hidden">
                                                <div class="px-4 py-2 bg-gray-100 border-b">
                                                    <span class="text-black font-semibold">'.$note["title"].'</span>
                                                    <span class="px-2 py-1 bg-gray-200 text-xs text-gray-600 rounded ml-2">'.date("h i A, F jS, Y", strtotime($note["created_at"])).'</span>
                                                </div>
                                                <div class="px-4 py-2 bg-white text-black w-full">
                                                    <p class="text-sm">'.$note["body"].'</p>
                                                </div>
                                                <div class="px-4 py-2 bg-gray-50 text-right text-xs text-gray-600">'.$note["admin_name"].'</div>
                                            </div>
                                            ';
                                        }
                                    ?>

                                    <!-- End of Dummy notes -->
                                </div>
                            </div>
                        </div>


                        

                    </div>
                </div>

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

function fillEmailDetails(id, type) {
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

                    document.getElementById('subject'+type).value = subject;
                    tinymce.get('body'+type).setContent(body);

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
    subject = document.getElementById('subjectModal').value;
    bodyContent = tinymce.get('bodyModal').getContent();
    $.ajax({
        url: '<?= admin_url("recruitment_portal/act_submission"); ?>',
        type: 'POST',
        dataType: 'json',
        data: {
            id: <?= $submission->id ?>,
            status : globalStatus,
            subject : subject,
            body : bodyContent
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

                var date = new Date();
                var day = date.getDate();
                var year = date.getFullYear();
                var month = date.toLocaleString('default', { month: 'long' });
                date = month + ' ' + day + nth(day) + ', ' + year;

                $("#messages-container").prepend(`

                <div class="my-4 flex flex-col border shadow-lg rounded-lg overflow-hidden">
                    <div class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-300 text-white font-semibold">`+subject+`</div>
                    <div class="px-4 py-2 bg-white text-black w-full">`+bodyContent+`</div>
                    <div class="px-4 py-2 bg-gray-200 text-right text-xs text-gray-600">Admin, `+date+`</div>
                </div>
                `);

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

$('#sendMessageForm').on('submit', function(e) {
    alert_float("info", "Sending...");
    e.preventDefault();
    const formData = new FormData(this);
    formData.append(csrfData.token_name, csrfData.hash);
    let bodyContent = tinymce.get('bodyInline').getContent();
    formData.append('message', bodyContent);
    formData.append('submission_id', <?= $submission->id ?>);
    formData.append('email', '<?= $email; ?>');
    if(!bodyContent){
        alert('Body is required!');
        return;
    }

    var date = new Date();
    var day = date.getDate();
    var year = date.getFullYear();
    var month = date.toLocaleString('default', { month: 'long' });
    date = month + ' ' + day + nth(day) + ', ' + year;

    $.ajax({
        type: 'POST',
        url: '<?= admin_url("recruitment_portal/add_message"); ?>',
        data: formData,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.success) {
                alert_float("success", "Message Sent!");
                $("#messages-container").prepend(`

                <div class="my-4 flex flex-col border shadow-lg rounded-lg overflow-hidden">
                    <div class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-300 text-white font-semibold">`+$("input[name=subject]").val()+`</div>
                    <div class="px-4 py-2 bg-white text-black w-full">`+bodyContent+`</div>
                    <div class="px-4 py-2 bg-gray-200 text-right text-xs text-gray-600">Admin, `+date+`</div>
                </div>
                `);
                $("#subject").val("");
                tinymce.get('message-input').setContent("");
                
            } else {
                alert(response.message);
            }
        },
        error: function() {
            alert('An error occurred while processing the request.');
        }
    });
});


tinymce.init({
  selector: '#bodyInline',
  height : "300",
  plugins: 'link',
  toolbar: 'link'
});
tinymce.init({
        selector: '#bodyModal',
        height : "300",
        plugins: 'link',
    toolbar: 'link'
    });

function nth(d) {
  if(d>3 && d<21) return 'th'; 
  switch (d % 10) {
        case 1:  return "st";
        case 2:  return "nd";
        case 3:  return "rd";
        default: return "th";
    }
}

var currentFavorite = <?= $submission->is_favorite ?>;

function toggleFavorite(element){

    alert_float("info", "Marking...");
    element.disabled = true;

    let toChange = 0;

    if(currentFavorite == 0){
        toChange = 1;
    }

    $.ajax({
        url: '<?= admin_url("recruitment_portal/fav_submission"); ?>',
        type: 'POST',
        dataType: 'json',
        data: {
            id: <?= $submission->id ?>,
            mark : toChange,
        },
        success : function(response) {
            if(response.success){
                alert_float("success", "Success!");
                
                if(toChange == 1){
                    document.getElementById("fav-label").innerHTML = 'Unmark as favorite';
                }else{
                    document.getElementById("fav-label").innerHTML = 'Mark as favorite';
                }

                currentFavorite = toChange;

                element.disabled = false;

            }else{
                alert_float("danger", "Unsuccessful!");
            }
        },
        error : function (xhr) {
            console.log(xhr.responseText);
        }
    });
}

$("#addNoteForm").submit(function(e) {
    e.preventDefault();

    alert_float("info", "Creating...");

    var submission_id = <?= $submission->id; ?>; // Replace with your actual submission ID
    var title = $("#noteTitle").val();
    var body = $("#noteBody").val();
    $.ajax({
        url: '<?= admin_url("recruitment_portal/add_note"); ?>', // Replace with your actual path
        method: 'POST',
        dataType: 'json',
        data: {
            submission_id: submission_id,
            title: title,
            body: body
        },
        success: function(response) {
            if (response.id) {
                // Note successfully added
                alert_float("success", "Added!");

                var now = new Date();
                var hours = now.getHours() > 12 ? now.getHours() - 12 : now.getHours();
                var minutes = now.getMinutes();
                var ampm = now.getHours() >= 12 ? 'PM' : 'AM';
                var day = now.getDate();
                var suffix = ['th', 'st', 'nd', 'rd'];
                var i = day % 10;
                var daySuffix = i <= 3 && parseInt(day / 10) !== 1 ? suffix[i] : suffix[0];
                var month = now.toLocaleString('en-US', { month: 'long' });
                var year = now.getFullYear();
                var formattedDate = (hours < 10 ? '0' + hours : hours) + ' ' + 
                                    (minutes < 10 ? '0' + minutes : minutes) + ' ' + 
                                    ampm + ', ' + month + ' ' + day + daySuffix + ', ' + year;


                var noteHtml = '<div class="my-4 border border-solid border-gray-200 shadow-lg rounded-lg overflow-hidden">' +
                '<div class="px-4 py-2 bg-gray-100 border-b">' +
                '<span class="text-black font-semibold">' + title + '</span>' +
                '<span class="px-2 py-1 bg-gray-200 text-xs text-gray-600 rounded ml-2">' + formattedDate + '</span>' +
                '</div>' +
                '<div class="px-4 py-2 bg-white text-black w-full">' +
                '<p class="text-sm">' + body + '</p>' +
                '</div>' +
                '<div class="px-4 py-2 bg-gray-50 text-right text-xs text-gray-600"><?= htmlspecialchars(get_staff_full_name()); ?></div>' +
                '</div>';
                
                $('#notes-container').prepend(noteHtml);
                
                $("#noteTitle").val("");
                $("#noteBody").val("");
            } else {
                alert_float("error", "Error!");
            }
        },
        error: function() {
            alert_float("error", "Error!");
        }
    });
});



</script>

</body>
</html>