<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
<div class="content">
<div class="panel_s">
<div class="panel-body">
<?php 
  $achieved = [];
  $progress_m = [];
  $confidence_level = [];
  $answer = [];
  $evaluation_criterias = [];
  $upcoming_checkin = '';
  $comment = [];
  if(count($key_result_checkin) > 0){
    foreach ($key_result_checkin as $key => $value) {
      $achieved[] = $value['achieved'];
      $progress_m[] = $value['progress'];
      $confidence_level[] = $value['confidence_level'];
      $answer[] = json_decode($value['answer']);
      $evaluation_criterias[] = $value['evaluation_criteria'];
      $comment[] = $value['comment'];
      $date_checkin = $value['recently_checkin'];
      $upcoming_checkin = $value['upcoming_checkin'];
    }
  }else{
    foreach ($get_key_result as $key => $value) {
      $achieved[] = '';
      $progress_m[] = '';
      $confidence_level[] = '';
      $answer[] = json_decode('');
      $evaluation_criterias[] = '';
      $comment[] = '';
      $upcoming_checkin = '';
    }
  }
?>

<div class="row">
  <div class="column">
  	<div class="col-md-12">
    	<h4><?php echo _l('objective'); ?> : <?php echo html_entity_decode($name); ?></h4>
    </div>
    <div class="col-md-12">
    	<h4><?php echo _l('checkin_date'); ?> : <?php echo html_entity_decode($date_checkin); ?></h4>
    </div>
    <div class="col-md-12">
    	<h4 class="display-in-flex"><?php echo _l('change'); ?> : <?php echo html_entity_decode($change); ?>
    	</h4>
    </div>
    <div class="col-md-12 position-relative">
    	<h4><?php echo _l('progress'); ?> </h4>
		<div id="jq" 
        line-progressbar 
        data-percentage="<?php echo html_entity_decode($progress); ?>" 
        data-progress-color="#1abc9c">
        </div>
    </div>
    
  </div>
  <div class="column" class="border_gray">
    <figure class="highcharts-figure">
	    <div id="container"></div>
	</figure>
  </div>
  </div>
  <?php echo form_hidden('display', 'checkin'); ?>
  <?php echo form_open(admin_url('okr/add_check_in'),array('id'=>'form_add_check_in')); ?>             
  <?php echo form_hidden('type', 1); ?>
  <?php echo form_hidden('id', $id); ?>
  <?php echo form_hidden('okrs_id', $id); ?>
  <?php echo form_hidden('recently_checkin', $date_checkin); ?>
<div class="row">
  <div id="table-scroll" class="table-scroll">
    <div class="table-wrap">
      <table class="table table-custom main-table">
        <tr>
          <th class="fixed-side"><?php echo _l('key_results');?></th>
          <th><?php echo _l('target');?></th>
          <th><?php echo _l('achieved');?></th>
          <th ><?php echo _l('progress');?></th>
          <th ><?php echo _l('confidence_level');?></th>
          <?php if($count_question > 0) {
            foreach ($question as $key => $value) { ?>                  
              <th class="not-fixed-side"><?php echo _l('question').' '.($key+1);?>  </th>
          <?php  } } ?>
          <th><?php echo _l('feedback_management');?></th>
        </tr>
          <?php if(count($get_key_result) > 0){
            foreach ($get_key_result as $key => $value) { ?>
            <?php echo form_hidden('rs_id['.$key.']', $value['id']); ?>

        <tr data-key-result="<?php echo html_entity_decode($value['id']) ?>">
            <td class="main_results fixed-side">
                <div class="main_results">

                  <div class="col-md-12 main_results_title ks_style">
                    <?php echo html_entity_decode($value['main_results']);?>
                    <?php echo form_hidden('main_results['.$key.']',$value['main_results']);?>
                  </div>

                  <div class="col-md-6 plan-detailt position-relative">
                    <div><span class="popovers" data-placement="top" data-content="<?php echo html_entity_decode($value['plan']) ?>" data-original-title="<?php echo _l('plan') ?>" ><?php echo _l('plan'); ?><span></div>
                  </div>
                  <div class="col-md-6 result-detailt position-relative">
                    <div><span class="popovers" data-placement="top" data-content="<?php echo html_entity_decode($value['results']) ?>" data-original-title="<?php echo _l('results') ?>"><?php echo _l('results_lable'); ?><span></div>
                  </div>
                </div>

                <h5 class="task-hanld">
                  <span class="label label-default task_list_view_rs"><i class="fa fa-eye" aria-hidden="true"></i></span>
                </h5>
            </td>

            <td  class="fixed-side">
                <div class="target text-center">

                <?php echo form_hidden('target['.$key.']', $value['target']); ?>
                <span><?php echo app_format_money($value['target'], ""); ?></span>
                <span ><?php echo html_entity_decode(unit_name($value['unit'])); ?></span>
                <?php echo form_hidden('unit['.$key.']',$value['unit']);?>
              </div>
            </td>
            

            <td  class="not-fixed-side">
                <div class="achieved">
                  <?php $achieved_ = (isset($achieved[$key]) ? $achieved[$key] : 0);?>
                  <div class="form-group" app-field-wrapper="achieved[<?php echo html_entity_decode($key); ?>]">
                    <input type="text" onkeyup="formatCurrency($(this));" onblur="formatCurrency($(this), 'blur');" id="achieved" name="achieved[<?php echo html_entity_decode($key); ?>]" class="form-control"  data-key="<?php echo html_entity_decode($key); ?>" data-min="0" data-max="<?php echo html_entity_decode($value['target']); ?>" value="<?php echo html_entity_decode($achieved_); ?>" required>
                  </div>
                  <span class="symbol"><?php echo html_entity_decode(unit_name($value['unit'])); ?></span>
                </div>
            </td>
            <?php $progress_m_ = (isset($progress_m[$key]) ? $progress_m[$key] : 0);?>
            <?php echo form_hidden('progress['.$key.']',$progress_m_);?>
            <td id="progress_m_<?php echo html_entity_decode($key); ?>" class="not-fixed-side">
               <div name="progress[<?php echo html_entity_decode($key); ?>]" class="checkin-progress relative" data-value="<?php echo html_entity_decode($progress_m_/100); ?>" data-size="55" data-thickness="5">
                  <strong class="okr-percent"></strong>
               </div>
            </td>  
            <?php $confidence_level_ = (isset($confidence_level[$key]) ? $confidence_level[$key] : '');?>

            <td class="not-fixed-side">
              <div class="default">
                <div class="changed_1">
                    <label>
                      <input type="radio" name="confidence_level[<?php echo html_entity_decode($key); ?>]" value="1" <?php if($confidence_level_ == 1){ echo 'checked';} ?> ><span> <?php echo _l('is_fine') ?></span>
                    </label>
                </div>
                <label>
                  <input type="radio"  name="confidence_level[<?php echo html_entity_decode($key); ?>]" value="2" <?php if($confidence_level_ == 2){ echo 'checked';}?> ><span class="default_ct"> <?php echo _l('not_so_good') ?></span>
                </label>
                <div class="changed_2">
                    <label>
                      <input type="radio" name="confidence_level[<?php echo html_entity_decode($key); ?>]" value="3" <?php if($confidence_level_ == 3){ echo 'checked';}?> > <span> <?php echo _l('very_good') ?></span>
                    </label>
                </div>
              </div>
            </td>

            <?php if($count_question > 0) {
              foreach ($question as $k => $v) { ?>
                <td class="not-fixed-side"><?php echo html_entity_decode($v['question']); ?>?

                  <?php $value_ = (isset($answer[$key][$k]) ? $answer[$key][$k] : '');

                  echo render_textarea('answer['.$key.']['.$k.']','', $value_,array('class' => 'question')); ?>
                </td>
            <?php  } } ?>


            <td class="not-fixed-side"><?php echo _l('score_estimate');?>
            <?php $evaluation_criterias_ = (isset($evaluation_criterias[$key]) ? $evaluation_criterias[$key] : '');?>
            <?php $comment_ = (isset($comment[$key]) ? $comment[$key] : '');?>

              <?php echo render_select('evaluation_criteria['.$key.']',$evaluation_criteria,array('id','name', 'scores'),'',$evaluation_criterias_); ?>
              <?php echo render_textarea('comment['.$key.']', 'comment', $comment_, ['rows' => 4, 'cols' => 100, 'class' => 'question']); ?>
            </td>
              
          </tr>
          <?php }
          } else{ echo '<tr><td colspan="'.($count_question + 6).'"></td></tr>'; } ?>
      </table>
    </div>
  </div>
  <br>
  <div class="col-md-4">
    <?php echo render_date_input('upcoming_checkin', 'upcoming_checkin', $upcoming_checkin); ?>
  </div>
  <div class="clearfix"></div>
  <div class="modal-footer">
    <a class="btn btn-danger" href="<?php echo admin_url('okr/checkin_detailt/'.$id.'?tab=history');  ?>" role="button"><?php echo _l('close'); ?></a>
  </div>
<?php echo form_close(); ?>                 
  
</div>
</div>
</div>
</div>
</div>

<div id="add-task-view-key-result" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo _l('view') ?></h4>
      </div>
      <div class="modal-body">
        <?php echo form_hidden('list_tasks'); ?>
        <?php echo form_hidden('key_result'); ?>
        <?php echo form_hidden('key_result'); ?>
        <table class="table table-task-list scroll-responsive">
          <thead>
            <th>#</th>
            <th><?php echo _l('tasks_dt_name'); ?></th>
            <th><?php echo _l('task_status'); ?></th>
            <th><?php echo _l('tasks_dt_datestart'); ?></th>
            <th><?php echo _l('task_duedate'); ?></th>
            <th><?php echo _l('task_assigned'); ?></th>
            <th><?php echo _l('tasks_list_priority'); ?></th>
            </thead>
            <tbody></tbody>
            <tfoot>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>      
            </tfoot>
         </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      </div>
    </div>

  </div>
</div>

<!-- Modal -->
<div id="myModal_preview" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      </div>
    </div>

  </div>
</div>
<?php init_tail(); ?>
<?php require 'modules/okr/assets/js/file_js_view_details.php';?>

</body>
</html>