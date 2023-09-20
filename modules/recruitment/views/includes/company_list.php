<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div>
<div class="_buttons">
    <a href="#" onclick="new_company(); return false;" class="btn btn-info pull-left display-block">
        <?php echo _l('new_company'); ?>
    </a>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table">
 <thead>
    <th><?php echo _l('company_name'); ?></th>
    <th><?php echo _l('company_address'); ?></th>
    <th><?php echo _l('company_industry'); ?></th>
    <th><?php echo _l('options'); ?></th>
 </thead>
 <tbody>
    <?php foreach ($company_list as $company) {?>
    <tr>
       <td><?php echo html_entity_decode($company['company_name']); ?></td>
       <td><?php echo html_entity_decode($company['company_address']); ?></td>
       <td><?php echo html_entity_decode($company['company_industry']); ?></td>
       <td>
         <a href="#" onclick="edit_company(this,<?php echo html_entity_decode($company['id']); ?>); return false" data-name="<?php echo html_entity_decode($company['company_name']); ?>" data-address="<?php echo html_entity_decode($company['company_address']); ?>" data-industry="<?php echo html_entity_decode($company['company_industry']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
          <a href="<?php echo admin_url('recruitment/delete_company/' . $company['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
       </td>
    </tr>
    <?php }?>
 </tbody>
</table>

<div class="modal fade" id="company" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open_multipart(admin_url('recruitment/company_add_edit'), array('id' => 'company_form')); ?>

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_company'); ?></span>
                    <span class="add-title"><?php echo _l('new_company'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                         <div id="additional_company"></div>
                            <?php echo render_input('company_name', 'company_name'); ?>
                        </div>

                        <div class="col-md-12">
                            <?php echo render_textarea('company_address', 'company_address') ?>
                        </div>

                        <div class="col-md-12">
                            <?php echo render_textarea('company_industry', 'company_industry') ?>
                        </div>


                        
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label><?php echo _l('company_images') ?></label>
                          <div id="dropzoneDragArea" class="dz-default dz-message">
                             <span><?php echo _l('drag_drop_file_here'); ?></span>
                          </div>
                          <div class="dropzone-previews"></div>
                        </div>
                        <div id="images_old_preview"></div>

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
