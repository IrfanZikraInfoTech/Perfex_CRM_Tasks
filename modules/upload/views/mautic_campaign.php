<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="no-margin"><?php echo _l('mautic_campaign_list'); ?></h4>
            <hr class="hr-panel-heading">
            <?php
              $table_data = [
                  _l('mautic_campaign_name'),
                  _l('mautic_campaign_description'),
                  _l('mautic_campaign_published'),
                  _l('mautic_campaign_category'),
              
              ];
              render_datatable($table_data, ($class ?? 'mautic_campaign_list'));?>
          </div>
        </div>
      </div>
      <?php echo form_close(); ?>
    </div>
    <div class="btn-bottom-pusher"></div>
  </div>
</div>
<?php init_tail();?>
<script type="text/javascript">
  $(function(){
    initDataTable('.table-mautic_campaign_list', window.location.href,'undefined','undefined','');
  });
</script>
</body>
</html>