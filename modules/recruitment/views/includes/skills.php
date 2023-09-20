<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div>
<div class="_buttons">
    <a href="#" onclick="new_skill(); return false;" class="btn btn-info pull-left display-block">
        <?php echo _l('new_skill'); ?>
    </a>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table">
 <thead>
    <th><?php echo _l('id'); ?></th>
    <th><?php echo _l('skill_name'); ?></th>
    <th><?php echo _l('options'); ?></th>
 </thead>
 <tbody>
    <?php foreach ($skills as $skill) {?>
    <tr>
       <td><?php echo html_entity_decode($skill['id']); ?></td>
       <td><?php echo html_entity_decode($skill['skill_name']); ?></td>
       <td>
         <a href="#" onclick="edit_skill(this,<?php echo html_entity_decode($skill['id']); ?>); return false" data-name="<?php echo html_entity_decode($skill['skill_name']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
          <a href="<?php echo admin_url('recruitment/delete_skill/' . $skill['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
       </td>
    </tr>
    <?php }?>
 </tbody>
</table>

<div class="modal fade" id="skill" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('recruitment/skill')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_skill'); ?></span>
                    <span class="add-title"><?php echo _l('new_skill'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                     <div id="additional"></div>
                     <div class="form">
                        <?php echo render_input('skill_name', 'skill_name'); ?>
                    </div>
                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
</body>
</html>
