<div class="modal fade" id="staff_contract_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo html_entity_decode(isset($contract) ? $contract->contract_code : ''); ?></h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                      <table class="table border table-striped ">
                        <tbody>
                            <tr class="project-overview">
                              <td class="bold" ><?php echo _l('hr_contract_code'); ?></td>
                              <td><?php echo html_entity_decode($contract->contract_code); ?></td>
                            </tr>
                             <tr class="project-overview">
                              <td class="bold"><?php echo _l('hr_start_month'); ?></td>
                              <td><?php echo _d($contract->start_valid); ?></td>
                            </tr>
                              
                              <tr class="project-overview">
                                  <td class="bold"><?php echo _l('hr_end_month'); ?></td>
                                  <td><?php echo _d($contract->end_valid); ?></td>
                              </tr>
                              <tr class="project-overview">
                                  <td class="bold" ><?php echo _l('hr_sign_day'); ?></td>
                                  <td><?php echo _d($contract->sign_day); ?></td>
                              </tr>
                              <tr class="project-overview">
                                <td class="bold"><?php echo _l('hr_hourly_rate_month'); ?></td>
                                <td ><?php echo _l($contract->hourly_or_month); ?></td>
                              </tr>
                        </tbody>
                        </table>
                    </div>
                </div>

                <h5><?php echo _l('hr_hr_contract_rel_type') ?></h5>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table border table-striped ">
                           <thead>
                              <th class="th-color"><?php echo _l('hr_hr_contract_rel_type'); ?></th>
                              <th class="text-center th-color"><?php echo _l('hr_hr_contract_rel_value'); ?></th>
                              <th class="th-color"><?php echo _l('hr_start_month'); ?></th>
                              <th class="th-color"><?php echo _l('note'); ?></th>
                          </thead>
                          <tbody>
                              <?php foreach($contract_details as $contract_detail){ ?>
                                <?php 
                                    $type_name ='';
                                    if(preg_match('/^st_/', $contract_detail['rel_type'])){
                                        $rel_value = str_replace('st_', '', $contract_detail['rel_type']);
                                        $salary_type = $this->hr_profile_model->get_salary_form($rel_value);

                                        $type = 'salary';
                                        if($salary_type){
                                            $type_name = $salary_type->form_name;
                                        }

                                    }elseif(preg_match('/^al_/', $contract_detail['rel_type'])){
                                        $rel_value = str_replace('al_', '', $contract_detail['rel_type']);
                                        $allowance_type = $this->hr_profile_model->get_allowance_type($rel_value);

                                        $type = 'allowance';
                                        if($allowance_type){
                                            $type_name = $allowance_type->type_name;
                                        }
                                    }
                                 ?>
                                        <tr>
                                           <td><?php echo html_entity_decode($type_name); ?></td>
                                           <td class="text-right"><?php echo app_format_money((float)$contract_detail['rel_value'],''); ?></td>
                                           <td><?php echo _d($contract_detail['since_date']); ?></td>
                                           <td><?php echo html_entity_decode($contract_detail['contract_note']); ?></td>

                                        </tr>
                              <?php } ?>
                          </tbody>
                      </table>  
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default close_btn" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
            </div>
        </div>

    </div>
</div>
<?php 
  require('modules/hr_profile/assets/js/hr_record/add_update_staff_js.php');
 ?>
