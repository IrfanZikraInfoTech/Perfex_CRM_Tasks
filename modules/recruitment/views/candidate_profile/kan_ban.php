<?php defined('BASEPATH') or exit('No direct script access allowed');

$statuses =  [
            [
                'id'             => 1,
                'color'          => '#989898',
                'name'           => _l('application'),
                'order'          => 1,
                'filter_default' => true,
                ],
             [
                'id'             => 2,
                'color'          => '#03A9F4',
                'name'           => _l('potential'),
                'order'          => 2,
                'filter_default' => true,
                ],
             [
                'id'             => 3,
                'color'          => '#2d2d2d',
                'name'           => _l('interview'),
                'order'          => 3,
                'filter_default' => true,
                ],
              [
                'id'             => 4,
                'color'          => '#adca65',
                'name'           => _l('won_interview'),
                'order'          => 4,
                'filter_default' => true,
                ],
              [
                'id'             => 5,
                'color'          => '#84c529',
                'name'           => _l('send_offer'),
                'order'          => 100,
                'filter_default' => false,
                ],
                [
                'id'             => 6,
                'color'          => '#84c529',
                'name'           => _l('elect'),
                'order'          => 100,
                'filter_default' => false,
                ],
                [
                'id'             => 7,
                'color'          => '#84c529',
                'name'           => _l('non_elect'),
                'order'          => 100,
                'filter_default' => false,
                ],
                [
                'id'             => 8,
                'color'          => '#84c529',
                'name'           => _l('unanswer'),
                'order'          => 100,
                'filter_default' => false,
                ],
                [
                'id'             => 9,
                'color'          => '#84c529',
                'name'           => _l('transferred'),
                'order'          => 100,
                'filter_default' => false,
                ],
                [
                'id'             => 10,
                'color'          => '#84c529',
                'name'           => _l('freedom'),
                'order'          => 100,
                'filter_default' => false,
                ],
                
            ];


foreach ($statuses as $status) { 

  $total_pages = ceil($this->recruitment_model->do_kanban_query($status['id'],$this->input->get('search'),1,true,[])/50);
  ?>
  <ul class="kan-ban-col tasks-kanban" data-col-status-id="<?php echo html_entity_decode($status['id']); ?>" data-total-pages="<?php echo html_entity_decode($total_pages); ?>">
    <li class="kan-ban-col-wrapper">
      <div class="border-right panel_s">
        <div class="panel-heading-bg <?php echo get_kan_ban_status_candidate_color($status['id']) ?>"  data-status-id="<?php echo html_entity_decode($status['id']); ?>">
          <div class="kan-ban-step-indicator<?php if($status['id'] == 10){ echo ' kan-ban-step-indicator-full'; } ?>"></div>

          <span class="heading span_heading_color"><?php echo get_kan_ban_status_candidate_color($status['id'],true); ?>
          </span>
          <a href="#" onclick="return false;" class="pull-right color-white">
          </a>
        </div>

        <div class="kan-ban-content-wrapper kan-ban-wrapper-min-height">
          <div class="kan-ban-content">
            <ul class="status tasks-status sortable relative" data-task-status-id="<?php echo html_entity_decode($status['id']); ?>">
              <?php
              $candidates = $this->recruitment_model->do_kanban_query($status['id'],$this->input->get('search'),1,false,[]);
              $total_candidates = count($candidates);
              foreach ($candidates as $candidate) {
                if ($candidate['status'] == $status['id']) {
                  $this->load->view('recruitment/candidate_profile/_kan_ban_card',array('candidate'=>$candidate,'status'=>$status['id']));
                } } ?>
                <?php if($total_candidates > 0 ){ ?>
                <li class="text-center not-sortable kanban-load-more" data-load-status="<?php echo html_entity_decode($status['id']); ?>">

                 <a href="#" class="btn btn-default btn-block<?php if($total_pages <= 1){echo ' disabled';} ?>" data-page="1" onclick="candidate_kanban_load_more(<?php echo html_entity_decode($status['id']); ?>,this,'recruitment/recruitment_kanban_load_more',265,360); return false;";><?php echo _l('load_more'); ?></a>
               </li>
               <?php } ?>
               <li class="text-center not-sortable mtop30 kanban-empty<?php if($total_candidates > 0){echo ' hide';} ?>">
                <h4>
                  <i class="fa fa-circle-o-notch" aria-hidden="true"></i><br /><br />
                  <?php echo _l('no_candidate_found'); ?></h4>
                </li>
              </ul>
            </div>
          </div>

        </li>
      </ul>
      <?php } ?>
