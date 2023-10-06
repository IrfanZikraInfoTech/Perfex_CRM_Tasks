<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <?php echo form_hidden('project_id', $project->id) ?>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="_buttons">
                    <div class="row">
                        <div class="col-md-7 project-heading">
                            <div class="tw-flex tw-flex-wrap tw-items-center">
                                <h3 class="hide project-name"><?php echo $project->name; ?></h3>
                                <div id="project_view_name" class="tw-max-w-sm tw-mr-3">
                                    <div class="tw-w-full">
                                        <select class="selectpicker" id="project_top" data-width="100%"
                                            <?php if (count($other_projects) > 6) { ?> data-live-search="true"
                                            <?php } ?>>
                                            <option value="<?php echo $project->id; ?>" selected
                                                data-content="<?php echo $project->name; ?> - <small><?php echo $project->client_data->company; ?></small>">
                                                <?php echo $project->client_data->company; ?>
                                                <?php echo $project->name; ?>
                                            </option>
                                            <?php foreach ($other_projects as $op) { ?>
                                            <option value="<?php echo $op['id']; ?>"
                                                data-subtext="<?php echo $op['company']; ?>">#<?php echo $op['id']; ?> -
                                                <?php echo $op['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="visible-xs">
                                    <div class="clearfix"></div>
                                </div>

                                <div class="tw-items-center ltr:tw-space-x-2 tw-inline-flex">
                                    <div class="tw-flex -tw-space-x-1">
                                        <?php foreach ($members as $member) { ?>
                                        <span class="tw-group tw-relative"
                                            data-title="<?php echo get_staff_full_name($member['staff_id']) . (has_permission('projects', '', 'create') || $member['staff_id'] == get_staff_user_id() ? ' - ' . _l('total_logged_hours_by_staff') . ': ' . seconds_to_time_format($member['total_logged_time']) : ''); ?>"
                                            data-toggle="tooltip">
                                            <?php if (has_permission('projects', '', 'edit')) { ?>
                                            <a href="<?php echo admin_url('projects/remove_team_member/' . $project->id . '/' . $member['staff_id']); ?>"
                                                class="_delete group-hover:tw-inline-flex tw-hidden tw-rounded-full tw-absolute tw-items-center tw-justify-center tw-bg-neutral-300/50 tw-h-7 tw-w-7 tw-cursor-pointer">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="tw-w-4 tw-h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </a>
                                            <?php } ?>
                                            <?php echo staff_profile_image($member['staff_id'], ['tw-inline-block tw-h-7 tw-w-7 tw-rounded-full tw-ring-2 tw-ring-white', '']); ?>
                                        </span>
                                        <?php } ?>
                                    </div>
                                    <a href="#" data-target="#add-edit-members" data-toggle="modal"
                                        class="tw-mt-1.5 rtl:tw-mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="tw-w-5 tw-h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </a>
                                </div>
                                <?php
                                echo '<span class="tw-ml-1 project_status tw-inline-block label project-status-' . $project->status . '" style="color:' . $project_status['color'] . ';border:1px solid ' . adjust_hex_brightness($project_status['color'], 0.4) . ';background: ' . adjust_hex_brightness($project_status['color'], 0.04) . ';">' . $project_status['name'] . '</span>';
                            ?>
                            </div>
                        </div>
                        <div class="col-md-5 text-right">
                            
                            <button class="btn btn-primary" onclick="importScrum();">Import SCRUM</button>

                            <div class="btn-group">
                            <button type="button" class="px-4 py-2 rounded-lg shaodw bg-green-500 hover:bg-green-600 transition-all text-white dropdown-toggle border-2 border-gray-200 border-solid" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Reports
                            </button>
                                <div class="dropdown-menu">
                                    <button class="w-full text-left px-4 py-2 shaodw hover:bg-gray-200 transition-all" href="#" onclick="initReport();">Initiation Report</button>
                                    <button class="w-full text-left px-4 py-2 shaodw hover:bg-gray-200 transition-all" href="#" onclick="finalReport();">Completion Report</button>
                                </div>
                            </div>

                            
                            <!-- <?php if (has_permission('tasks', '', 'create')) { ?>
                            <a href="#"
                                onclick="new_task_from_relation(undefined,'project',<?php echo $project->id; ?>); return false;"
                                class="btn btn-primary">
                                <i class="fa-regular fa-plus tw-mr-1"></i>
                                <?php echo _l('new_task'); ?>
                            </a>
                            <?php } ?> -->
                            <?php
                           $invoice_func = 'pre_invoice_project';
                           ?>
                            <?php if (has_permission('invoices', '', 'create')) { ?>
                            <a href="#"
                                onclick="<?php echo $invoice_func; ?>(<?php echo $project->id; ?>); return false;"
                                class="invoice-project btn btn-primary<?php if ($project->client_data->active == 0) {
                               echo ' disabled';
                           } ?>">
                                <i class="fa-solid fa-file-invoice tw-mr-1"></i>
                                <?php echo _l('invoice_project'); ?>
                            </a>
                            <?php } ?>
                            <?php
                           $project_pin_tooltip = _l('pin_project');
                           if (total_rows(db_prefix() . 'pinned_projects', ['staff_id' => get_staff_user_id(), 'project_id' => $project->id]) > 0) {
                               $project_pin_tooltip = _l('unpin_project');
                           }
                           ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <?php echo _l('more'); ?> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right width200 project-actions">
                                    <li>
                                        <a href="<?php echo admin_url('projects/pin_action/' . $project->id); ?>">
                                            <?php echo $project_pin_tooltip; ?>
                                        </a>
                                    </li>
                                    <?php if (has_permission('projects', '', 'edit')) { ?>
                                    <li>
                                        <a href="<?php echo admin_url('projects/project/' . $project->id); ?>">
                                            <?php echo _l('edit_project'); ?>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (has_permission('projects', '', 'create')) { ?>
                                    <li>
                                        <a href="#" onclick="copy_project(); return false;">
                                            <?php echo _l('copy_project'); ?>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (has_permission('projects', '', 'create') || has_permission('projects', '', 'edit')) { ?>
                                    <li class="divider"></li>
                                    <?php foreach ($statuses as $status) {
                               if ($status['id'] == $project->status) {
                                   continue;
                               } ?>
                                    <li>
                                        <a href="#" data-name="<?php echo _l('project_status_' . $status['id']); ?>"
                                            onclick="project_mark_as_modal(<?php echo $status['id']; ?>,<?php echo $project->id; ?>, this); return false;"><?php echo _l('project_mark_as', $status['name']); ?></a>
                                    </li>
                                    <?php
                           } ?>
                                    <?php } ?>
                                    <li class="divider"></li>
                                    <?php if (has_permission('projects', '', 'create')) { ?>
                                    <li>
                                        <a href="<?php echo admin_url('projects/export_project_data/' . $project->id); ?>"
                                            target="_blank"><?php echo _l('export_project_data'); ?></a>
                                    </li>
                                    <?php } ?>
                                    <?php if (is_admin()) { ?>
                                    <li>
                                        <a href="<?php echo admin_url('projects/view_project_as_client/' . $project->id . '/' . $project->clientid); ?>"
                                            target="_blank"><?php echo _l('project_view_as_client'); ?></a>
                                    </li>
                                    <?php } ?>
                                    <?php if (has_permission('projects', '', 'delete')) { ?>
                                    <li>
                                        <a href="<?php echo admin_url('projects/delete/' . $project->id); ?>"
                                            class="_delete">
                                            <span class="text-danger"><?php echo _l('delete_project'); ?></span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="project-menu-panel tw-my-5">
                    <?php hooks()->do_action('before_render_project_view', $project->id); ?>
                    <?php $this->load->view('admin/projects/project_tabs'); ?>
                </div>
                <?php
               if ((has_permission('projects', '', 'create') || has_permission('projects', '', 'edit'))
                 && $project->status == 1
                 && $this->projects_model->timers_started_for_project($project->id)
                 && $tab['slug'] != 'project_milestones') {
                   ?>
                <div class="alert alert-warning project-no-started-timers-found mbot15">
                    <?php echo _l('project_not_started_status_tasks_timers_found'); ?>
                </div>
                <?php
               } ?>
                <?php
               if ($project->deadline && date('Y-m-d') > $project->deadline
                && $project->status == 2
                && $tab['slug'] != 'project_milestones') {
                   ?>
                <div class="alert alert-warning bold project-due-notice mbot15">
                    <?php echo _l('project_due_notice', floor((abs(time() - strtotime($project->deadline))) / (60 * 60 * 24))); ?>
                </div>
                <?php
               } ?>
                <?php
               if (!has_contact_permission('projects', get_primary_contact_user_id($project->clientid))
                 && total_rows(db_prefix() . 'contacts', ['userid' => $project->clientid]) > 0
                 && $tab['slug'] != 'project_milestones') {
                   ?>
                <div class="alert alert-warning project-permissions-warning mbot15">
                    <?php echo _l('project_customer_permission_warning'); ?>
                </div>
                <?php
               } ?>

                <?php
                $this->load->view(($tab ? $tab['view'] : 'admin/projects/project_overview')); ?>

            </div>
        </div>
    </div>
</div>
</div>
</div>

<div class="modal fade" id="add-edit-members" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('projects/add_edit_members/' . $project->id)); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('project_members'); ?></h4>
            </div>
            <div class="modal-body">
                <?php
            $selected = [];
            foreach ($members as $member) {
                array_push($selected, $member['staff_id']);
            }
           echo render_select('project_members[]', $staff, ['staffid', ['firstname', 'lastname']], 'project_members', $selected, ['multiple' => true, 'data-actions-box' => true], [], '', '', false);
           ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary" autocomplete="off"
                    data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php if (isset($discussion)) {
               echo form_hidden('discussion_id', $discussion->id);
               echo form_hidden('discussion_user_profile_image_url', $discussion_user_profile_image_url);
               echo form_hidden('current_user_is_admin', $current_user_is_admin);
           }
   echo form_hidden('project_percent', $percent);
   ?>
<div id="invoice_project"></div>
<div id="pre_invoice_project"></div>
<?php $this->load->view('admin/projects/milestone'); ?>
<?php $this->load->view('admin/projects/copy_settings'); ?>
<?php $this->load->view('admin/projects/_mark_tasks_finished'); ?>
<?php init_tail(); ?>
<!-- For invoices table -->
<script>
taskid = '<?php echo $this->input->get('taskid'); ?>';


</script>
<script>
var gantt_data = {};
<?php if (isset($gantt_data)) { ?>
gantt_data = <?php echo json_encode($gantt_data); ?>;
<?php } ?>
var discussion_id = $('input[name="discussion_id"]').val();
var discussion_user_profile_image_url = $('input[name="discussion_user_profile_image_url"]').val();
var current_user_is_admin = $('input[name="current_user_is_admin"]').val();
var project_id = $('input[name="project_id"]').val();
if (typeof(discussion_id) != 'undefined') {
    discussion_comments('#discussion-comments', discussion_id, 'regular');
}

<?php if($tab['slug'] == "project_board" && isset($active_sprint)){ ?>
$(function() {
    
    init_kanban("tasks/kanban", tasks_kanban_update, ".tasks-status", 240, 360);

});
<?php } ?>

$(function() {
    var project_progress_color =
        '<?php echo hooks()->apply_filters('admin_project_progress_color', '#84c529'); ?>';
    var circle = $('.project-progress').circleProgress({
        fill: {
            gradient: [project_progress_color, project_progress_color]
        }
    }).on('circle-animation-progress', function(event, progress, stepValue) {
        $(this).find('strong.project-percent').html(parseInt(100 * stepValue) + '<i>%</i>');
    });
});

function discussion_comments(selector, discussion_id, discussion_type) {
    var defaults = _get_jquery_comments_default_config(
        <?php echo json_encode(get_project_discussions_language_array()); ?>);
    var options = {
        // https://github.com/Viima/jquery-comments/pull/169
        wysiwyg_editor: {
            opts: {
                enable: true,
                is_html: true,
                container_id: 'editor-container',
                comment_index: 0,
            },
            init: function(textarea, content) {
                var comment_index = textarea.data('comment_index');
                var editorConfig = _simple_editor_config();
                editorConfig.setup = function(ed) {
                    textarea.data('wysiwyg_editor', ed);

                    ed.on('change', function() {
                        var value = ed.getContent();
                        if (value !== ed._lastChange) {
                            ed._lastChange = value;
                            textarea.trigger('change');
                        }
                    });

                    ed.on('keyup', function() {
                        var value = ed.getContent();
                        if (value !== ed._lastChange) {
                            ed._lastChange = value;
                            textarea.trigger('change');
                        }
                    });

                    ed.on('Focus', function(e) {
                        setTimeout(function() {
                            textarea.trigger('click');
                        }, 500)
                    });

                    ed.on('init', function() {
                        if (content) ed.setContent(content);

                        if ($('#mention-autocomplete-css').length === 0) {
                            $('<link>').appendTo('head').attr({
                                id: 'mention-autocomplete-css',
                                type: 'text/css',
                                rel: 'stylesheet',
                                href: site_url +
                                    'assets/plugins/tinymce/plugins/mention/autocomplete.css'
                            });
                        }

                        if ($('#mention-css').length === 0) {
                            $('<link>').appendTo('head').attr({
                                type: 'text/css',
                                id: 'mention-css',
                                rel: 'stylesheet',
                                href: site_url +
                                    'assets/plugins/tinymce/plugins/mention/rte-content.css'
                            });
                        }
                    })
                }

                editorConfig.toolbar = editorConfig.toolbar.replace('alignright', 'alignright strikethrough')
                editorConfig.plugins[0] += ' mention';
                editorConfig.content_style = 'span.mention {\
                     background-color: #eeeeee;\
                     padding: 3px;\
                  }';
                var projectUserMentions = [];
                editorConfig.mentions = {
                    source: function(query, process, delimiter) {
                        if (projectUserMentions.length < 1) {
                            $.getJSON(admin_url + 'projects/get_staff_names_for_mentions/' + project_id,
                                function(data) {
                                    projectUserMentions = data;
                                    process(data)
                                });
                        } else {
                            process(projectUserMentions)
                        }
                    },
                    insert: function(item) {
                        return '<span class="mention" contenteditable="false" data-mention-id="' + item
                            .id + '">@' +
                            item.name + '</span>&nbsp;';
                    }
                };

                var containerId = this.get_container_id(comment_index);
                tinyMCE.remove('#' + containerId);

                setTimeout(function() {
                    init_editor('#' + containerId, editorConfig)
                }, 100)
            },
            get_container: function(textarea) {
                if (!textarea.data('comment_index')) {
                    textarea.data('comment_index', ++this.opts.comment_index);
                }

                return $('<div/>', {
                    'id': this.get_container_id(this.opts.comment_index)
                });
            },
            get_contents: function(editor) {
                return editor.getContent();
            },
            on_post_comment: function(editor, evt) {
                editor.setContent('');
            },
            get_container_id: function(comment_index) {
                var container_id = this.opts.container_id;
                if (comment_index) container_id = container_id + "-" + comment_index;
                return container_id;
            }
        },
        currentUserIsAdmin: current_user_is_admin,
        getComments: function(success, error) {
            $.get(admin_url + 'projects/get_discussion_comments/' + discussion_id + '/' + discussion_type,
                function(response) {
                    success(response);
                }, 'json');
        },
        postComment: function(commentJSON, success, error) {
            $.ajax({
                type: 'post',
                url: admin_url + 'projects/add_discussion_comment/' + discussion_id + '/' +
                    discussion_type,
                data: commentJSON,
                success: function(comment) {
                    comment = JSON.parse(comment);
                    success(comment)
                },
                error: error
            });
        },
        putComment: function(commentJSON, success, error) {
            $.ajax({
                type: 'post',
                url: admin_url + 'projects/update_discussion_comment',
                data: commentJSON,
                success: function(comment) {
                    comment = JSON.parse(comment);
                    success(comment)
                },
                error: error
            });
        },
        deleteComment: function(commentJSON, success, error) {
            $.ajax({
                type: 'post',
                url: admin_url + 'projects/delete_discussion_comment/' + commentJSON.id,
                success: success,
                error: error
            });
        },
        uploadAttachments: function(commentArray, success, error) {
            var responses = 0;
            var successfulUploads = [];
            var serverResponded = function() {
                responses++;
                // Check if all requests have finished
                if (responses == commentArray.length) {
                    // Case: all failed
                    if (successfulUploads.length == 0) {
                        error();
                        // Case: some succeeded
                    } else {
                        successfulUploads = JSON.parse(successfulUploads);
                        success(successfulUploads)
                    }
                }
            }
            $(commentArray).each(function(index, commentJSON) {
                // Create form data
                var formData = new FormData();
                if (commentJSON.file.size && commentJSON.file.size > app
                    .max_php_ini_upload_size_bytes) {
                    alert_float('danger', "<?php echo _l('file_exceeds_max_filesize'); ?>");
                    serverResponded();
                } else {
                    $(Object.keys(commentJSON)).each(function(index, key) {
                        var value = commentJSON[key];
                        if (value) formData.append(key, value);
                    });

                    if (typeof(csrfData) !== 'undefined') {
                        formData.append(csrfData['token_name'], csrfData['hash']);
                    }
                    $.ajax({
                        url: admin_url + 'projects/add_discussion_comment/' + discussion_id +
                            '/' + discussion_type,
                        type: 'POST',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(commentJSON) {
                            successfulUploads.push(commentJSON);
                            serverResponded();
                        },
                        error: function(data) {
                            var error = JSON.parse(data.responseText);
                            alert_float('danger', error.message);
                            serverResponded();
                        },
                    });
                }
            });
        }
    }
    var settings = $.extend({}, defaults, options);
    $(selector).comments(settings);
}
</script>
<script>

$(document).ready(function() {
    $('#submitFolderForm').click(function(e) {
        e.preventDefault();
        var folderName = $('#folderName').val();
        var csrfName = csrfData.token_name;
        var csrfHash = csrfData.hash;
        var data = {folder_name: folderName};
        data[csrfName] = csrfHash;
        $.ajax({
            type: "POST",
            url: "<?php echo admin_url('projects/addFolder'); ?>",
            data: data,
            success: function(response) {
                console.log(response);
                location.reload(); // Reload the page after successful insertion.
            },
            error: function(response) {
                console.log(response);
            }
        });
    });
});

$(document).ready(function () {
    $(".folder-option").click(function(event){
        event.preventDefault();
        var folderId = $(this).attr("data-folder-id");
        $("#folder-id-input").val(folderId);
        $("#file-upload-form").show();
    });
});

$(".folder-name").click(function() {
    // Get the folder id
    var folder_id = $(this).data("folder-id");

    // Clear any previous data
    $("#filesContainer").empty();

    // Make the AJAX call
    $.ajax({
        url: "<?php echo admin_url('Projects/get_files_by_folder/'); ?>" + folder_id,
        type: 'GET',
        success: function(data) {
            // Append the table rows to the tbody
            $("#filesContainer").append(data);
        }
    });

    // Open the modal
    $("#folderModal").modal('show');
});

function importScrum(){
    $.ajax({
        url: "<?php echo admin_url('projects/get_templates/'); ?>",
        type: 'GET',
        success: function(data) {
            // Parse the response data
            var templates = JSON.parse(data);
            
            // Build the HTML for the buttons
            var buttonsHtml = '';
            templates.forEach(function(template, index){
                buttonsHtml += '<button class="template-button px-4 py-2 bg-blue-500 hover:bg-blue-600 shadow rounded text-white" data-template-id="' + template.id + '" data-template-json=\'' + template.epics_and_stories + '\'>Template ' + (index + 1) + ': ' + template.name + '</button>';
            });
            
            // Display the buttons in a SweetAlert
            Swal.fire({
                title: 'Select a template',
                html: '<div class="w-full flex flex-col gap-2">' + buttonsHtml + '</div>',
                allowOutsideClick: false,
                showConfirmButton: false,
                showCancelButton: true,
                didOpen: function () {
                    // Attach an event listener to each button
                    $('.template-button').on('click', function() {

                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You are about to import an entire template.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, import!'
                        }).then((result) => {
                            if (result.isConfirmed) {

                                Swal.fire({
                                title: 'Processing',
                                html: 'Importing Template...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }});

                                var templateId = $(this).data('template-id');
                                
                                // Trigger the POST AJAX request when a button is clicked
                                $.ajax({
                                    url: "<?php echo admin_url('projects/import_template'); ?>",
                                    type: 'POST',
                                    data: {
                                        project_id: <?= $project->id ?>,
                                        template_id: templateId
                                    },
                                    success: function(response) {
                                        response = JSON.parse(response);
                                        if(response.success){
                                            window.location.href= admin_url + "projects/view/<?= $project->id ?>?group=project_backlog";
                                        }
                                        //console.log('Template imported successfully:', response);
                                    },
                                    error: function(xhr, status, error) {
                                        // Handle the error case
                                        console.error('Error importing template:', error);
                                    }
                                });

                            }
                        });

                    });
                }
            });
        }
    });
}

function initReport(){

<?php
    if(!empty($project->init_report)){
        ?>

            Swal.fire({
                title: 'Report Found',
                text: 'Report already created, Want a new one??',
                icon: 'info',
                confirmButtonText: 'New',
                showCancelButton: true,
                cancelButtonText: 'Open Existing'
            }).then((result) => {
                if(result.isConfirmed){
                    createReport('init_report');
                }else if(result.isDismissed && result.dismiss == 'cancel'){
                    window.open("https://docs.google.com/document/d/<?= $project->init_report ?>", '_blank').focus();
                }
                console.log(result);
            });

        <?php
    }else{    
?>

createReport('init_report');

<?php } ?>

}

function finalReport(){

<?php
    if(!empty($project->final_report)){
        ?>

            Swal.fire({
                title: 'Report Found',
                text: 'Report already created, Want a new one??',
                icon: 'info',
                confirmButtonText: 'New',
                showCancelButton: true,
                cancelButtonText: 'Open Existing'
            }).then((result) => {
                if(result.isConfirmed){
                    createReport('final_report');
                }else if(result.isDismissed && result.dismiss == 'cancel'){
                    window.open("https://docs.google.com/document/d/<?= $project->final_report ?>", '_blank').focus();
                }
                console.log(result);
            });

        <?php
    }else{    
?>

createReport('final_report');

<?php } ?>

}

function createReport(name){
    Swal.fire({
    title: 'Processing',
    html: 'Creating Report...',
    allowOutsideClick: false,
    didOpen: () => {
        Swal.showLoading();
    }});
    
    // Trigger the POST AJAX request when a button is clicked
    $.ajax({
        url: "<?php echo admin_url('projects/'); ?>"+name,
        type: 'POST',
        data: {
            project_id: <?= $project->id ?>
        },
        success: function(response) {
            response = JSON.parse(response);
            if(response.success){
                Swal.close();
                Swal.fire({
                    title: 'Success',
                    html: 'Report is added in your drive!',
                    allowOutsideClick: false,
                    cancelButtonText: "Close",
                    confirmButtonText: "Open",
                    showCancelButton: true,
                    showConfirmButton: true
                }).then((result) => {
                    if(result.isConfirmed){
                        window.open(response.url, '_blank').focus();
                    }
                    location.reload();
                });
            }else{
                Swal.close();
                Swal.fire("Error!", response.message, "error");
            }
        },
        error: function(xhr, status, error) {
            // Handle the error case
            Swal.close();
            console.error('Error importing template:', error);
        }
    });
}
</script>


</body>

</html>