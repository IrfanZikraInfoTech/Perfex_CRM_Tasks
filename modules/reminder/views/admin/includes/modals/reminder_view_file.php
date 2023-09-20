<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">
                <span class="view-title"><?php echo _l('rm_view_reminder_modal_heading').$reminder->id. _l('rm_view_reminder_modal_heading_notified_on'). _d($reminder->date); ?></span>
            </h4>
        </div>
        <div class="modal-body">
            <table class="table table-striped text-left">
                <tbody>
                    <tr>
                        <td><span class="bold">Customer</span></td>
                        <td class="total"> <a href="<?php echo admin_url()?>clients/client/<?php echo $reminder->customer  ?>" target="_blank"><?php echo $reminder->company; ?> </a> </td>
                        <td><span class="bold">Contact</span></td>
                        <td><a href="<?php echo admin_url()?>clients/client/<?php echo $reminder->customer?>?group=contacts&contactid=<?php echo $reminder->contact ?>" target="_blank"> <?php echo get_contact_full_name($reminder->contact); ?></td>
                        </tr>
                        <tr class="tax-area">
                            <td class="bold">Email</td><td><a href="mailto: <?php echo $reminder->email; ?>"><?php echo $reminder->email; ?></a></td>
                            <td class="bold">Telephone</td><td><a href="tel:<?php echo $reminder->phonenumber; ?>"><?php echo $reminder->phonenumber; ?></a></td>
                        </tr>
                        <tr class="tax-area">
                            <td class="bold"><?php echo _l('reminder_assigned')?></td>
                            <td><a href="<?php echo admin_url()?>staff/profile/<?php echo $reminder->assigned_to;?>" target="_blank"><?php echo get_staff_full_name($reminder->assigned_to) ; ?></a></td>
                            <td class="bold"><?php echo _l('reminder_related')?></td>
                            <td><?php echo _l($reminder->rel_type); ?></a></td>
                        </tr>
                        <tr class="tax-area">
                            <?php if($reminder->rel_type=='invoice'){ ?>
                                <td class="bold"><?php echo _l('rm_invoices')?></td>
                                <td>
                                    <a href="<?php echo site_url()?>invoice/<?php echo $reminder->rel_id ?>/<?php echo $related_doc[0]['hash'] ?>" target="_blank"> <?php echo format_invoice_number($reminder->rel_id); ?>
                                </a>
                            </td>
                        <?php } ?>
                        <?php if($reminder->rel_type=='quotes'){ ?>
                            <td class="bold"><?php echo _l('rm_proposals')?></td>
                            <td>
                                <a href="<?php echo site_url()?>proposal/<?php echo $reminder->rel_id ?>/<?php echo $related_doc[0]['hash']?>" target="_blank"> <?php echo format_proposal_number($reminder->rel_id); ?>
                            </a>
                        </td>
                    <?php } ?>
                    <?php if($reminder->rel_type=='estimate'){ ?>
                        <td class="bold"><?php echo _l('rm_estimates')?></td>
                        <td>
                            <a href="<?php echo site_url()?>estimate/<?php echo $reminder->rel_id ?>/<?php echo $related_doc[0]['hash'] ?>" target="_blank"> <?php echo format_estimate_number($reminder->rel_id); ?>
                        </a>
                    </td>
                <?php } ?>
                <?php if($reminder->rel_type=='credit_note'){ ?>
                    <td class="bold"><?php echo _l('credit_note')?></td>
                    <td>
                        <a href="#" target="_blank"> <?php echo format_credit_note_number($reminder->rel_id); ?>
                    </a>
                </td>
            <?php } ?>
            <?php if($reminder->rel_type=='ticket'){ ?>
                <td class="bold"><?php echo _l('rm_tickets')?></td>
                <td>
                    <a href="#" target="_blank"> <?php echo $reminder->rel_id; ?>
                </a>
            </td>
        <?php } ?>
        <td class="bold"><?php echo _l('reminder_status_th')?></td>
        <td><?php echo $reminder->isnotified == '0' ? '<span class="label label-warning">'._l('rm_not_notified_status').'</span>' : '<span class="label label-warning">'._l('rm_notified_status').'</span>' ; ?></td>
    </tr>
    <tr class="tax-area">
        <td class="bold"><?php echo _l('rm_view_reminder_modal_is_email_client')?></td>
        <td><?php echo $reminder->notify_by_email_client == '0' ? _l('no') : _l('yes') ; ?></td>
        <td class="bold"><?php echo _l('rm_view_reminder_modal_is_sms_client')?></td>
        <td><?php echo $reminder->notify_by_sms_client == '0' ? _l('no') : _l('yes') ; ?></td>
    </tr>
    <tr class="tax-area">
        <td class="bold"><?php echo _l('reminder_description')?></td>
        <td><?php echo $reminder->description; ?></td>
    </tr>
</tbody>
</table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
</div>
</div>
</div>
