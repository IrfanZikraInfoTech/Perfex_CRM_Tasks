<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<li data-task-id="<?php echo html_entity_decode($candidate['id']); ?>">
  <div class="panel-body">
    <div class="row">
      <div class="col-md-12 task-name">

        <a href="<?php echo admin_url('recruitment/candidate/' . $candidate['id']); ?>" >
          <span class="inline-block full-width mtop10 mbot10"><?php echo html_entity_decode($candidate['candidate_code'] .'_'.$candidate['candidate_name'].' '.$candidate['last_name']); ?></span>
        </a>
      </div>
      <div class="col-md-2 text-muted">
        <a href="<?php echo admin_url('recruitment/candidate/' . $candidate['id']); ?>">
          <?php echo candidate_profile_image($candidate['id'],[
                    'staff-profile-image-small mright5',
                    ], 'small') ?>
        </a>

     </div>
     <div class="col-md-10 text-left text-muted">
        <span class="mright5 inline-block text-muted" data-toggle="tooltip" >
          <?php echo html_entity_decode($candidate['phonenumber']) ?>
        </span>
        <span class="mright5 inline-block text-muted" data-toggle="tooltip" >
         <?php echo html_entity_decode($candidate['email']) ?>
        </span>
        <span class="mright5 inline-block text-muted" data-toggle="tooltip" >
          <?php
          $cp = get_rec_campaign_hp($candidate['rec_campaign']);
          $datas = '';
          if (isset($cp)) {
            $datas = '<a href="' . admin_url('recruitment/recruitment_campaign/' . $cp->cp_id) . '">' . $cp->campaign_code . ' - ' . $cp->campaign_name . '</a>';
          }
          echo html_entity_decode($datas);
          ?>
        </span>
        
     </div>
    <?php if($candidate['status'] == 6){ ?>
     <?php if (has_permission('recruitment', '', 'edit') || is_admin()) { ?>
       <div class="col-md-12 mtop2">
        <span class="mright5 inline-block text-muted" data-toggle="tooltip" >
         <a href="<?php echo admin_url('recruitment/transfer_to_hr/' . $candidate['id'] ); ?>" class="btn btn-success btn-xs"><?php echo _l('tranfer_personnels') ?></a>
        </span>
       </div>
     <?php } ?>

   <?php }?>
      
   
</div>
</div>
</li>
