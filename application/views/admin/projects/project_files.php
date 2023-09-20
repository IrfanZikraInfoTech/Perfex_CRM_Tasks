<style>
.myfoldercss{
    text-decoration: none;
    color: #777;
    font-size: medium;
    text-align: justify;
}


</style>


<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row my-3">
    <div class="col-md-12 text-center  ">
<!-- new folder -->
<div class="tw-mt-4 tw-inline-block tw-text-sm">
    <button href="#" data-toggle="modal" data-target="#newFolderModal" class="btn btn-primary">
    <i class="fa-regular fa-plus tw-mr-1"></i>
    <?php echo ('New Folder'); ?>
    </button>
</div>
<!-- new folder  -->
 <!-- folder fetch work work -->

 <div class="showfolderbtn tw-mt-4 tw-inline-block tw-text-sm">
    <div class="dropdown">
        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Select Folders
        </button>
        <div class="dropdown-menu px-3" aria-labelledby="dropdownMenuButton">
            <?php foreach($folders as $folder): ?>
                <a class="dropdown-item text-center mb-4 myfoldercss folder-option " href="#" data-folder-id="<?php echo $folder->folder_id; ?>">
                    <i class="fas fa-folder-open mr-2"></i> <?php echo $folder->folder_name; ?>
                </a>
                <div class="dropdown-divider"></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

</div>
      
<!-- folder fetch work end -->
    </div>

   <div id="file-upload-form" style="display:none">
    <?php echo form_open_multipart(admin_url('projects/upload_file/' . $project->id), ['class' => 'dropzone', 'id' => 'project-files-upload']); ?>
    <input type="hidden" name="folder_id" id="folder-id-input" value="" />
    <input type="file" name="file" multiple />
    <?php echo form_close(); ?>
</div>




<span class="tw-mt-4 tw-inline-block tw-text-sm"><?php echo _l('project_file_visible_to_customer'); ?></span><br />
<div class="onoffswitch">
    <input type="checkbox" name="visible_to_customer" id="pf_visible_to_customer" class="onoffswitch-checkbox">
    <label class="onoffswitch-label" for="pf_visible_to_customer"></label>
</div>
<div class="tw-flex tw-justify-end tw-items-center tw-space-x-2">
    <button class="gpicker" data-on-pick="projectFileGoogleDriveSave">
        <i class="fa-brands fa-google" aria-hidden="true"></i>
        <?php echo _l('choose_from_google_drive'); ?>
    </button>
    <div id="dropbox-chooser"></div>
</div>
<!-- gdrive work ends -->

<div class="clearfix"></div>
<div class="mtop20"></div>
<div class="modal fade bulk_actions" id="project_files_bulk_actions" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
            </div>
            <div class="modal-body">
                <?php if (is_admin()) { ?>
                <div class="checkbox checkbox-danger">
                    <input type="checkbox" name="mass_delete" id="mass_delete">
                    <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                </div>
                <hr class="mass_delete_separator" />
                <?php } ?>
                <div id="bulk_change">
                    <div class="form-group">
                        <label class="mtop5"><?php echo _l('project_file_visible_to_customer'); ?></label>
                        <div class="onoffswitch">
                            <input type="checkbox" name="bulk_visible_to_customer" id="bulk_pf_visible_to_customer"
                                class="onoffswitch-checkbox">
                            <label class="onoffswitch-label" for="bulk_pf_visible_to_customer"></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <a href="#" class="btn btn-primary"
                    onclick="project_files_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<a href="#" data-toggle="modal" data-target="#project_files_bulk_actions" class="bulk-actions-btn table-btn hide"
    data-table=".table-project-files">
    <?php echo _l('bulk_actions'); ?>
</a>
<a href="#"
    onclick="window.location.href = '<?php echo admin_url('projects/download_all_files/' . $project->id); ?>'; return false;"
    class="table-btn hide" data-table=".table-project-files"><?php echo _l('download_all'); ?></a>
<div class="clearfix"></div>






<!-- tables  -->
<div class="panel_s panel-table-full">
    <div class="panel-body">
        <table class="table dt-table table-project-files" data-order-col="7" data-order-type="desc">
            <thead>
                <tr>
                    <th data-orderable="false"><span class="hide"> - </span>
                        <div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all"
                                data-to-table="project-files"><label></label></div>
                    </th>
                    <th><?php echo('Folder Name'); ?></th>
                    <!-- <th><?php echo _l('project_file__filetype'); ?></th> -->
                    <th><?php echo _l('project_discussion_last_activity'); ?></th>
                    <th><?php echo _l('project_discussion_total_comments'); ?></th>
                    <th><?php echo _l('project_file_visible_to_customer'); ?></th>
                    <th><?php echo _l('project_file_uploaded_by'); ?></th>
                    <th><?php echo _l('project_file_dateadded'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file) {
    $path = get_upload_path_by_type('project') . $project->id . '/' . $file['file_name']; ?>
                <tr>
                    <td>
                        <div class="checkbox"><input type="checkbox" value="<?php echo $file['id']; ?>"><label></label>
                        </div>
                    </td>
                    <!-- <td data-order="<?php echo $file['file_name']; ?>">
                        <a href="#"
                            onclick="view_project_file(<?php echo $file['id']; ?>,<?php echo $file['project_id']; ?>); return false;">
                            <?php if (is_image(PROJECT_ATTACHMENTS_FOLDER . $project->id . '/' . $file['file_name']) || (!empty($file['external']) && !empty($file['thumbnail_link']))) {
        echo '<div class="text-left"><i class="fa fa-spinner fa-spin mtop30"></i></div>';
        echo '<img class="project-file-image img-table-loading" src="#" data-orig="' . project_file_url($file, true) . '" width="100">';
        echo '</div>';
    }
    echo $file['subject']; ?></a>
                    </td> -->



                    <td style="cursor:pointer;" class="hover:text-blue-700 folder-name" data-folder-id="<?= $file['project_folder_id']; ?>"><?= $file['folder_name']; ?></td>

                    <!-- <td data-order="<?php echo $file['filetype']; ?>"><?php echo $file['filetype']; ?></td> -->
                   
                    <td data-order="<?php echo $file['last_activity']; ?>">
                        <?php
            if (!is_null($file['last_activity'])) { ?>
                        <span class="text-has-action" data-toggle="tooltip"
                            data-title="<?php echo _dt($file['last_activity']); ?>">
                            <?php echo time_ago($file['last_activity']); ?>
                        </span>
                        <?php } else {
                echo _l('project_discussion_no_activity');
            } ?>
                    </td>
                    <?php $total_file_comments = total_rows(db_prefix() . 'projectdiscussioncomments', ['discussion_id' => $file['id'], 'discussion_type' => 'file']); ?>
                    <td data-order="<?php echo $total_file_comments; ?>">
                        <?php echo $total_file_comments; ?>
                    </td>
                    <td data-order="<?php echo $file['visible_to_customer']; ?>">
                        <?php
            $checked = '';
    if ($file['visible_to_customer'] == 1) {
        $checked = 'checked';
    } ?>
                        <div class="onoffswitch">
                            <input type="checkbox"
                                data-switch-url="<?php echo admin_url(); ?>projects/change_file_visibility"
                                id="<?php echo $file['id']; ?>" data-id="<?php echo $file['id']; ?>"
                                class="onoffswitch-checkbox" value="<?php echo $file['id']; ?>" <?php echo $checked; ?>>
                            <label class="onoffswitch-label" for="<?php echo $file['id']; ?>"></label>
                        </div>

                    </td>
                    <td>
                        <?php if ($file['staffid'] != 0) {
        $_data = '<a href="' . admin_url('staff/profile/' . $file['staffid']) . '">' . staff_profile_image($file['staffid'], [
                'staff-profile-image-small',
              ]) . '</a>';
        $_data .= ' <a href="' . admin_url('staff/member/' . $file['staffid']) . '">' . get_staff_full_name($file['staffid']) . '</a>';
        echo $_data;
    } else {
        echo ' <img src="' . contact_profile_image_url($file['contact_id'], 'thumb') . '" class="client-profile-image-small mrigh5">
             <a href="' . admin_url('clients/client/' . get_user_id_by_contact_id($file['contact_id']) . '?contactid=' . $file['contact_id']) . '">' . get_contact_full_name($file['contact_id']) . '</a>';
    } ?>
                    </td>
                    <td data-order="<?php echo $file['dateadded']; ?>"><?php echo _dt($file['dateadded']); ?></td>

                </tr>
                <?php
} ?>
            </tbody>
        </table>
   
   <!-- table end -->
    </div>
</div>
<div id="project_file_data"></div>


<!-- Modal -->
<div class="modal fade animate__animated animate__backInDown" id="folderModal" tabindex="-1" role="dialog" aria-labelledby="folderModalLabel" aria-hidden="true">

<div class="modal-dialog" role="document">
    <div class="modal-content rounded-lg shadow-sm">
      <div class="modal-header bg-light border-0">
        <h5 class="modal-title text-center w-100 text-uppercase" id="folderModalLabel">Folder Details</h5>
        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <table class="table table-responsive table-hover">
          <thead>
            <tr>
              <th class="text-secondary"><?php echo _l('project_file_filename'); ?></th>
              <th class="text-secondary"><?php echo _l('project_file__filetype'); ?></th>
              <th class="text-secondary"><?php echo _l('options'); ?></th>
            </tr>
          </thead>
          <tbody id="filesContainer" class="text-dark">
            <!-- The files will be added here -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="newFolderModal" tabindex="-1" role="dialog" aria-labelledby="newFolderModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newFolderModalLabel"><?php echo ('Create New Flder'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="newFolderForm">
          <div class="form-group">
            <label for="folderName" class="col-form-label" ><?php echo ('Folder Name:'); ?>:</label>
            <input type="text" class="form-control" name="project_name" id="folderName">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="button" class="btn btn-primary" id="submitFolderForm"><?php echo _l('save'); ?></button>
      </div>
    </div>
  </div>
</div>
        


<?php include_once(APPPATH . 'views/admin/clients/modals/send_file_modal.php'); ?>






