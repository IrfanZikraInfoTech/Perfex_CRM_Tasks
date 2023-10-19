<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php

if(isset($active_sprint)){

?>

<!-- Existing Sprint -->
<div style="border: 2px solid #e2e8f0; border-radius: 0.5rem; margin-bottom: 1.25rem; transition: all 0.3s ease-in-out; background: white; box-shadow: 0 0 0 rgba(0,0,0,0);" onmouseover="this.style.boxShadow='0 0 15px rgba(0,0,0,0.3)'" onmouseout="this.style.boxShadow='0 0 0 rgba(0,0,0,0)'">
    
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 1rem; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;">
        <div style="display: flex; align-items: center; color: black; font-weight: bold; font-size: 1rem; gap: 0.5rem; width: 33%;">

            <input type="text" style="color: black; font-weight: bold; font-size: 1rem; background: transparent; width: 100%; padding: 0.25rem 0.5rem; border: none;" value="<?= htmlspecialchars($active_sprint->name); ?>" placeholder="Sprint Name" readonly/>
        </div>
        <div style="display: flex; height: 100%; align-items: center; gap: 1.5rem;">

            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <input type="date" style="background: transparent; border-radius: 0.5rem; padding: 0.5rem; border: none; transition: all 0.3s ease-in-out;" value="<?= htmlspecialchars($active_sprint->start_date); ?>" placeholder="Start Date" readonly onmouseover="this.style.background='white'; this.style.border='1px solid #4299e1'" onmouseout="this.style.background='transparent'; this.style.border='none'">
                <div>To</div>
                <input type="date" style="background: transparent; border-radius: 0.5rem; padding: 0.5rem; border: none; transition: all 0.3s ease-in-out;" value="<?= htmlspecialchars($active_sprint->end_date); ?>" placeholder="End Date" readonly onmouseover="this.style.background='white'; this.style.border='1px solid #4299e1'" onmouseout="this.style.background='transparent'; this.style.border='none'">
            </div>

            <div style="display: flex; flex-direction: row; gap: 0.25rem;">
                <div style="width: 1.25rem; height: 1.25rem; font-size: 0.75rem; background: #edf2f7; color: black; border-radius: 9999px; display: flex; justify-content: center; align-items: center;" data-toggle="tooltip" data-placement="top" title="Not Started: <?= $active_sprint->not_started_count ?>"><?= $active_sprint->not_started_count ?></div>
                <div style="width: 1.25rem; height: 1.25rem; font-size: 0.75rem; background: #c3ddfd; color: black; border-radius: 9999px; display: flex; justify-content: center; align-items: center;" data-toggle="tooltip" data-placement="top" title="In Progress: <?= $active_sprint->in_progress_count ?>"><?= $active_sprint->in_progress_count ?></div>
                <div style="width: 1.25rem; height: 1.25rem; font-size: 0.75rem; background: #c6f6d5; color: black; border-radius: 9999px; display: flex; justify-content: center; align-items: center;" data-toggle="tooltip" data-placement="top" title="Done: <?= $active_sprint->completed_count ?>"><?= $active_sprint->completed_count ?></div>
            </div>
            
            <button style="padding: 0.25rem 1rem; border-radius: 0.375rem; transition: all 0.3s ease-in-out; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); transform: scale(1); background: <?= $active_sprint->status == 2 ? '#48bb78' : 'white' ?>; color: <?= $active_sprint->status == 2 ? 'white' : '#4a5568' ?>; border: <?= $active_sprint->status == 2 ? 'none' : '1px solid #e2e8f0' ?>" onmouseover="this.style.boxShadow='0 0 15px rgba(0,0,0,0.3)'; this.style.transform='scale(1.05)'" onmouseout="this.style.boxShadow='0 1px 2px 0 rgba(0,0,0,0.05)'; this.style.transform='scale(1)'">
                <?php 
                    if($active_sprint->status == 1){
                        echo "In Progress";
                    }else {
                        echo "Completed";
                    }
                ?>
            </button>
        </div>
    </div>          
</div>

<div class="tasks-phases" style="overflow-x:scroll;">

    <div class="kan-ban-row" style="width:max-content;">
        <?php foreach ($task_statuses as $status) {
          $tasks   = $this->projects_model->get_tasks($project->id, ['status' => $status['id'], 'sprint_id'=>$active_sprint->id]);

          $status_color = '';
          if (!empty($status['color']) && !is_null($status['color'])) {
              $status_color = ' style="background:' . $status['color'] . ';border:1px solid ' . $status['color'] . '"';
          } 
          ?>
        <div class="kan-ban-col<?php if ($status['id'] == 0 && count($tasks) == 0) {
              echo ' hide';
          } ?>">
            <div class="panel-heading <?php if ($status_color != '') {
              echo 'color-not-auto-adjusted color-white ';
          } ?><?php if ($status['id'] != 0) {
              echo 'task-phase';
          } else {
              echo 'info-bg';
          } ?>" <?php echo $status_color; ?>>
                <?php if ($status['id'] != 0 && $status['description_visible_to_customer'] == 1) { ?>
                <i class="fa fa-file-text pointer" aria-hidden="true" data-toggle="popover"
                    data-title="<?php echo _l('status_description'); ?>" data-html="true"
                    data-content="<?php echo htmlspecialchars($status['description']); ?>"></i>&nbsp;
                <?php } ?>
                <span class="bold tw-text-sm"><?php echo $status['name']; ?></span>

            </div>
            <div class="panel-body">
                <?php
               if (count($tasks) == 0) {
                   echo 'No Tasks found';
               }
          foreach ($tasks as $task) { ?>
                <div class="media _task_wrapper<?php if ((!empty($task['duedate']) && $task['duedate'] < date('Y-m-d')) && $task['status'] != Tasks_model::STATUS_COMPLETE) {
              echo ' overdue-task';
          } ?>">
                    <div class="media-body">
                        <a href="<?php echo site_url('clients/project/' . $project->id . '?group=project_tasks&taskid=' . $task['id']); ?>"
                            class="task_status tw-mb-1 pull-left<?php if ($task['status'] == Tasks_model::STATUS_COMPLETE) {
              echo ' line-throught text-muted';
          } ?>">
                            <?php echo $task['name']; ?>

                        </a>

                        <?php if (
                     $project->settings->edit_tasks == 1 &&
                     $task['is_added_from_contact'] == 1 &&
                     $task['addedfrom'] == get_contact_user_id() &&
                     $task['billed'] == 0
                     ) { ?>
                        <a href="<?php echo site_url('clients/project/' . $project->id . '?group=edit_task&taskid=' . $task['id']); ?>"
                            class="pull-right">
                            <small><i class="fa-regular fa-pen-to-square"></i></small>
                        </a>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <p class="text-xs tw-mb-0">
                            <?php echo format_task_status($task['status'], true); ?>
                        </p>
                        <p class="tw-mb-0 tw-text-xs tw-text-neutral-500"><?php echo _l('tasks_dt_datestart'); ?>:
                            <b><?php echo _d($task['startdate']); ?></b>
                        </p>
                        <?php if (is_date($task['duedate'])) { ?>
                        <p class="tw-mb-0 tw-text-xs tw-text-neutral-500">
                            <?php echo _l('task_duedate'); ?>: <b><?php echo _d($task['duedate']); ?></b>
                        </p>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php
      } ?>
    </div>
</div>


<?php
}else{
?>

<div class="w-full px-4 py-2 bg-white border-gray-200 text-base">No Active Sprint!</div>

<?php } ?>