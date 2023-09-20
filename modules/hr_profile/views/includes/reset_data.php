<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php echo form_open_multipart(admin_url('hr_profile/reset_data'), array('id'=>'reset_data')); ?>
<div class="_buttons">
    <?php if (is_admin()) { ?>
        <div class="row">
            <div class="col-md-12">
                <button type="button" class="btn btn-info intext-btn" onclick="reset_data(this); return false;" ><?php echo _l('reset_data'); ?></button>
                <a href="#" class="input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('hr_reset_data_title'); ?>"></i></a>
            </div>
        </div>

    <?php } ?>
</div>
<?php echo form_close(); ?>


<?php require 'modules/hr_profile/assets/js/setting/reset_data_js.php';?>
</body>
</html>
