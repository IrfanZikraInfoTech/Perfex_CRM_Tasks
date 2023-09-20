<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head();
?>

<div id="wrapper">
     <div class="content">
          <?php if (get_option('appointly_responsible_person') == '') { ?>
               <div class="alert alert-warning alert-dismissible" role="alert">
                    <?= _l('appointments_resp_person_not_set'); ?> <a href="<?= admin_url('settings?group=appointly-settings'); ?>"><?= _l('appointly_settings_label_pointer'); ?></a>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">×</span>
                    </button>
               </div>
          <?php } ?>
          <?php if (get_option('callbacks_responsible_person') == '') { ?>
               <div class="alert alert-warning alert-dismissible" role="alert">
                    <?= _l('callbacks_resp_person_not_set'); ?> <a href="<?= admin_url('settings?group=appointly-settings'); ?>"><?= _l('appointly_settings_label_pointer'); ?></a>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">×</span>
                    </button>
               </div>
          <?php } ?>
          <div class="row main_row">
               <div class="col-md-12">
                    <div class="panel_s">
                         <div class="panel-body">
                              <div class="_buttons">
                                   <a href="#" class="btn btn-xs btn-default btn-with-tooltip toggle-small-view hidden-xs pull-right hidden" id="toggleTableBtn" onclick="toggle_appointment_table_view(); return false;" data-toggle="tooltip" title="" data-original-title="Toggle Table">
                                        <i class="fa fa-angle-double-right"></i>
                                   </a>
                                   <a href="<?= admin_url('appointly/appointments'); ?>" class="btn btn-xs btn-info pull-left display-block mleft10">
                                        <?= _l('appointments'); ?>
                                   </a>
                                   <a href="<?= admin_url('appointly/callbacks'); ?>" id="backToAppointments" class="btn btn-xs btn-info pull-left display-block mleft10">
                                        <?= _l('appointly_callbacks'); ?>
                                   </a>
                              </div>
                              <div class="clearfix"></div>
                              <hr class="hr-panel-heading" />
                              <?php render_datatable([
                                   [
                                        'th_attrs' => ['width' => '10px'],
                                        'name' => _l('id')
                                   ],
                                   [
                                        'th_attrs' => ['width' => '350px'],
                                        'name' => _l('appointment_subject')
                                   ],
                                   _l('appointment_meeting_date'),
                                   _l('appointment_initiated_by'),
                                   _l('appointment_description'),
                                   _l('appointment_source'),
                                   [
                                        'th_attrs' => ['width' => '120px'],
                                        'name' =>  _l('appointments_table_calendar')
                                   ]
                              ], 'appointments-history'); ?>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</div>
<div id="modal_wrapper"></div>
<?php init_tail(); ?>
<?php require('modules/appointly/assets/js/history_main_js.php'); ?>
</body>

</html>