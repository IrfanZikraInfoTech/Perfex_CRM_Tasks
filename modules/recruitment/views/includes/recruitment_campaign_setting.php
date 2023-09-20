<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="form-group">
  <div class="checkbox checkbox-primary">
    <input onchange="recruitment_campaign_setting(this); return false" type="checkbox" id="recruitment_create_campaign_with_plan" name="purchase_setting[recruitment_create_campaign_with_plan]" <?php if(get_recruitment_option('recruitment_create_campaign_with_plan') == 1 ){ echo 'checked';} ?> value="recruitment_create_campaign_with_plan">
    <label for="recruitment_create_campaign_with_plan"><?php echo _l('create_recruitment_campaign_not_create_plan'); ?>

    <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('recruitment_campaign_setting_tooltip'); ?>"></i></a>
    </label>
  </div>
</div>


<div class="clearfix"></div>


