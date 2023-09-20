<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-5 left-column">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="no-margin"><?php echo _l('mautic_contact_add_to_campaign'); ?></h4>
            <hr class="hr-panel-heading">
            <?php echo form_open('mauticmgmt/campaign/mautic_add_contact', array('id' => 'meeting-submit-form')); ?>
            <div class="row">
              <div class="col-md-12">
                <label class="control-label"> <?php echo _l('mautic_select_user'); ?> </label>
                <select id="select_user" name="select_user" class="selectpicker" data-width="100%" >
                  <?php
                    foreach ($contacts as $ct) {?>
                  <option value="<?php echo $ct['fields']['all']['id']; ?>"><?php echo $ct['fields']['all']['firstname']; ?></option>
                  <?php }?>
                </select>
              </div>
            </div>
            <br>
            <div class="row">
              <div class="col-md-12">
                <label class="control-label"> <?php echo _l('mautic_select_campaign'); ?> </label>
                <select id="select_campaign" name="select_campaign" class="selectpicker" data-width="100%" >
                  <?php
                    foreach ($campaigns as $cp) {?>
                  <option value="<?php echo $cp['id'] ?>"><?php echo $cp['name'] ?></option>
                  <?php }?>
                </select>
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