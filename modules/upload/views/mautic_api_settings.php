<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-5 left-column">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="no-margin"><?php echo _l('mautic_settings'); ?></h4>
            <hr class="hr-panel-heading">
            <?php echo form_open('mauticmgmt/settings/settings_submit', array('id' => 'meeting-submit-form')); ?>
            <div class="row">
              <div class="col-md-12">
                <?php
                  $mautic_base_url = $settings[0]['mautic_base_url'];
                  
                  echo render_input('mautic_base_url', 'mautic_base_url', $mautic_base_url, 'text', array('required' => 'true'));?>
              </div>
            </div>
            <?php
              $public_key = $settings[0]['public_key'];
              
              echo render_input('public_key', 'mautic_public_key', $public_key, 'text', array('required' => 'true'));?>
            <div class="row">
              <div class="col-md-12">
                <?php
                  $secret_key = $settings[0]['secret_key'];
                  
                  echo render_input('secret_key', 'mautic_secret_key', $secret_key, 'password', array('required' => 'true'));?>
              </div>
            </div>
            <h4 class="no-margin"><?php echo _l('mautic_lead_settings'); ?></h4>
            <hr class="hr-panel-heading">
            <div class="row">
              <div class="col-md-12">
                <?php $staff_selected = $settings[0]['mautic_lead_assigned'];?>
                <?php echo render_select('staffid', $staff, array('staffid', array('firstname', 'lastname')), _l('mautic_lead_assign'), $staff_selected, array('data-width' => '100%', 'data-none-selected-text' => _l('leads_dt_assigned')), array(), 'no-mbot'); ?>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <?php
                  $selected = array();
                  if ($this->input->get('status')) {
                      $selected[] = $this->input->get('status');
                  } else {
                      foreach ($statuses as $key => $status) {
                          if ($status['isdefault'] == 0) {
                              $selected[] = $status['id'];
                          } else {
                              $statuses[$key]['option_attributes'] = array('data-subtext' => _l('leads_converted_to_client'));
                          }
                      }
                  }
                  echo '<div id="leads-filter-status">';
                  echo render_select('view_status', $statuses, array('id', 'name'), _l('mautic_lead_status'), $selected, array('data-width' => '100%'), array(), 'no-mbot', '', false);
                  echo '</div>';
                  ?>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <?php $source_selected = $settings[0]['mautic_view_source'];?>
                <?php
                  echo render_select('view_source', $sources, array('id', 'name'), _l('mautic_lead_source'), $source_selected, array('data-width' => '100%', 'data-none-selected-text' => _l('leads_source')), array(), 'no-mbot');
                  ?>
              </div>
            </div>
            <div class="btn-bottom-toolbar text-right">
              <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>
</div>
<?php init_tail();?>
</body>
</html>