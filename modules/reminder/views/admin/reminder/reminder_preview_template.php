<?php defined("BASEPATH") or exit("No direct script access allowed"); ?>
<?php echo form_hidden("_attachment_sale_id", $reminder->id); ?>
<?php echo form_hidden("_attachment_sale_type", "proposal"); ?>
<div class="panel_s">
   <div class="panel-body">
      <div class="horizontal-scrollable-tabs preview-tabs-top">
         <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
         <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
         <div class="horizontal-tabs">
            <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
               <li role="presentation" class="active">
                  <a href="#tab_proposal" aria-controls="tab_proposal" role="tab" data-toggle="tab">
                     <?php echo _l("reminder"); ?>
                  </a>
               </li>
               <?php if (isset($reminder))
{ ?>
                  <li role="presentation">
                     <a href="#tab_activity" aria-controls="tab_activity" role="tab" data-toggle="tab">
                        <?php echo _l("proposal_view_activity_tooltip"); ?>
                     </a>
                  </li>
                  <li role="presentation">
                     <a href="#notes_tab" aria-controls="notes_tab" role="tab" data-toggle="tab">
                        <?php echo _l("estimate_notes"); ?>
                     </a>
                  </li>
                  <li role="presentation" data-toggle="tooltip" data-title="<?php echo _l("toggle_full_view"); ?>" class="tab-separator toggle_view">
                     <a href="#" onclick="reminder_small_table_full_view(); return false;">
                        <i class="fa fa-expand"></i></a>
                  </li> 
               <?php
} ?>
            </ul>
         </div>
      </div>
      <div class="row">
         <div class="col-md-12">
            <div class="tab-content">
               <div role="tabpanel" class="tab-pane active" id="tab_proposal">
                  <div class="row ">
                     <div class="col-sm-12">
                        <table class="table text-left items">
                           <thead>
                              <tr>
                                 <th colspan="4"><?php echo _l("reminder_contact_info"); ?></th>
                              </tr>
                           </thead>
                           <tbody>
                              <tr>
                                 <td><span class="bold"><?php echo _l("reminder_customer"); ?></span></td>
                                 <td class="total"> <a href="<?php echo admin_url(); ?>clients/client/<?php echo $reminder->customer; ?>" target="_blank"><?php echo $reminder->company; ?> </a> </td>
                                 <td><span class="bold"><?php echo _l("reminder_contact"); ?></span></td>
                                 <td class="subtotal"><a href="<?php echo admin_url(); ?>clients/client/<?php echo $reminder->customer; ?>?group=contacts&contactid=<?php echo $reminder->contact; ?>" target="_blank"> <?php echo get_contact_full_name($reminder->contact); ?></td>
                              </tr>
                              <tr class="tax-area">
                                 <td class="bold"><?php echo _l("reminder_email"); ?></td>
                                 <td><a href="mailto: <?php echo $reminder->email; ?>"><?php echo $reminder->email; ?></a></td>
                                 <td class="bold"><?php echo _l("reminder_contact_num"); ?></td>
                                 <td><a href="tel:<?php echo $reminder->phonenumber; ?>"><?php echo $reminder->phonenumber; ?></a></td>
                              </tr>
                              <tr class="tax-area">
                                 <?php if ($reminder->rel_type == "invoice")
{ ?>
                                    <td class="bold"><?php echo $reminder->rel_type; ?></td>
                                    <td>
                                       <a href="<?php echo site_url(); ?>invoice/<?php echo $reminder->rel_id; ?>/<?php echo $related_doc[0]["hash"]; ?>" target="_blank"> <?php echo format_invoice_number($reminder->rel_id); ?>
                                       </a>
                                    </td>
                                 <?php
} ?>
                                 <?php if ($reminder->rel_type == "quotes")
{ ?>
                                    <td class="bold">Quote</td>
                                    <td>
                                       <a href="<?php echo site_url(); ?>proposal/<?php echo $reminder->rel_id; ?>/<?php echo $related_doc[0]["hash"]; ?>" target="_blank"> <?php echo format_proposal_number($reminder->rel_id); ?>
                                       </a>
                                    </td>
                                 <?php
} ?>
                                 <?php if ($reminder->rel_type == "estimate")
{ ?>
                                    <td class="bold">Order</td>
                                    <td>
                                       <a href="<?php echo site_url(); ?>estimate/<?php echo $reminder->rel_id; ?>/<?php echo $related_doc[0]["hash"]; ?>" target="_blank"> <?php echo format_estimate_number($reminder->rel_id); ?>
                                       </a>
                                    </td>
                                 <?php
} ?>
                                 <?php if ($reminder->rel_type == "credit_note")
{ ?>
                                    <td class="bold">Credit Notes</td>
                                    <td>
                                       <a href="#" target="_blank"> <?php echo format_credit_note_number($reminder->rel_id); ?>
                                       </a>
                                    </td>
                                 <?php
} ?>
                                 <?php if ($reminder->rel_type == "job")
{ ?>
                                    <td class="bold">Job</td>
                                    <td>
                                       <a href="<?php echo admin_url(); ?>jobs/job/<?php echo $reminder->rel_id; ?>" target="_blank"> <?php echo format_job_number($reminder->rel_id); ?>
                                       </a>
                                    </td>
                                 <?php
} ?>
                              </tr>
                           </tbody>
                        </table>
                        <hr class="hr-panel-heading" />
                     </div>
                  </div>
                  <div class="row">
                     <?php echo form_open_multipart(admin_url() . "reminder/reminder_new", ["id" => "reminder-form", "class" => "_transaction_form_reminder reminder-form new_items_table", "id" => "reminder-form", ]); ?>
                     <div class="col-md-12">
                        <div class="panel_s">
                           <div class="panel-body">
                              <div class="row">
                                 <div class="col-md-6">
                                    <?php
$value = isset($reminder) ? _d($reminder->date) : "";
if ($reminder->is_complete == "0")
{ ?>
                                    <?php echo render_datetime_input("date", "set_reminder_date", $value, ["data-date-min-date" => _d(date("Y-m-d")) , "disabled" => true, ]);
}
else
{
    echo render_datetime_input("date", "set_reminder_date", $value, ["data-date-min-date" => _d(date("Y-m-d")) , ]);
}
?>
                                 </div>
                                 <div class="col-md-6">
                                    <div class="row">
                                       <div class="col-md-12 side_cust">
                                          <?php
$i = 0;
$selected = "";
foreach ($customers as $member)
{
    if (isset($reminder))
    {
        if ($reminder->customer == $member["userid"])
        {
            $selected = $member["userid"];
        }
    }
    $i++;
}
if ($reminder->is_complete == "0")
{
    echo render_select("customer", $customers, ["userid", "company"], _l("reminder_customer") , $selected, ["disabled" => true]);
}
else
{
    echo render_select("customer", $customers, ["userid", "company"], _l("reminder_customer") , $selected);
}
?>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-md-6 side_view">
                                    <div class="proposal_to_wrap side_cn">
                                       <?php
$value = isset($reminder) ? $reminder->contact : "";
if (isset($reminder) && !empty($reminder->contact))
{
    echo render_select("contact", $contacts, ["id", ["firstname", "lastname"], ], "contact", $reminder->contact, [], [], "", "side_view_contact");
}
?>
                                    </div>
                                 </div>
                                 <div class="col-md-6">
                                    <div class="row">
                                       <div class="col-md-12">
                                          <?php
$i = 0;
$selected = "";
foreach ($staff as $member)
{
    if (isset($reminder))
    {
        if ($reminder->assigned_to == $member["staffid"])
        {
            $selected = $member["staffid"];
        }
    }
    $i++;
}
if ($reminder->is_complete == "0")
{
    echo render_select("assigned_to", $staff, ["staffid", ["firstname", "lastname"], ], "reminder_assigned", $selected, ["disabled" => true]);
}
else
{
    echo render_select("assigned_to", $staff, ["staffid", ["firstname", "lastname"], ], "reminder_assigned", $selected);
}
?>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-md-12">
                                    <div class="row">
                                    <div class="col-md-12">
                                  <div class="form-group select-placeholder">
                                                    <label for="rel_type" class="control-label"><?php echo _l("reminder_related"); ?></label>
                                                    <select name="rel_type" data-side_view="side_view" id="rel_type" class="selectpicker" data-width="100%">
                                                        <option value=""></option>
                                                        <option value="proposal" <?php echo ($reminder->rel_type == 'proposal')?'selected':''; ?>><?php echo _l("rm_proposals"); ?></option>
                                                        <option value="estimate"<?php echo ($reminder->rel_type == 'estimate')?'selected':''; ?>><?php echo _l("rm_estimates"); ?></option>
                                                        <option value="invoice" <?php echo ($reminder->rel_type == 'invoice')?'selected':''; ?>><?php echo _l("rm_invoices"); ?></option>
                                                        <option value="credit_note" <?php echo ($reminder->rel_type == 'credit_note')?'selected':''; ?>><?php echo _l("rm_credit_notes"); ?></option>
                                                        <option value="ticket" <?php echo ($reminder->rel_type == 'ticket')?'selected':''; ?>><?php echo _l("rm_tickets"); ?></option>
                                                        <option value="custom_reminder" <?php echo ($reminder->rel_type == 'custom_reminder')?'selected':''; ?>><?php echo _l("rm_custom_reminder"); ?></option>
                                                        <option value="service" <?php echo ($reminder->rel_type == 'service')?'selected':''; ?>><?php echo _l("rm_custom_service"); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                    </div>
                                 </div>  
                                 <div class="col-md-12 recurring_custom_service <?php echo $reminder->rel_type != 'service' ? 'hide' : '' ?>">
                                                <div class="row">
                                                   <?php $selected=($reminder)?$reminder->services:0;
                                                   $selected=explode(',', $selected)
                                                    ?>
                                                    <div class="col-md-6">
                                                        <?php echo render_select_with_input_group("services[]", $services, ["id", "service_name", ], "reminder_service_type", $selected, '<a href="#" data-toggle="modal" data-target="#reminder_add_service"><i class="fa fa-plus"></i></a>', ["multiple" => true, "data-actions-box" => true, ], [], "", "reminder_service", false); ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="lead_value"><?php echo _l("reminder_amount"); ?></label>
                                                        <div class="input-group" data-toggle="tooltip" title="<?php echo _l("reminder_amount"); ?>">
                                                            <input type="number" class="form-control" id="amountreminder" name="total_amount" value="<?php echo $reminder->total_amount; ?>">
                                                            <div class="input-group-addon">
                                                                <?php echo $base_currency->symbol; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                         <div class="col-md-12 ">
                                                <div class="relidwrap <?php echo $reminder->rel_type == 'service' ? 'hide' : '' ?> ">
                                                    <div class="form-group select-placeholder">
                                                        <label for="rel_id" class="control-label"><?php echo _l("reminder_related_document"); ?></label>
                                                        <select name="rel_id" id="rel_id" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l("dropdown_non_selected_tex"); ?>">
                                                            <?php $chkRelType = isset($reminder) ? $reminder->rel_id : 0; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div id="otheridwrap"></div>
                                            </div>
                                            <div class="col-md-12">
                                                <div id="other_attachment"></div>
                                            </div>
    <!-----------------------End Related service --------------------------->
<!----------------------Repeat Every start------------------------------>
                       <div class="col-md-12 <?php echo $reminder->recurring_type == '' ? 'hide' : '' ?> ">
  <?php
$related = [["value" => "1-week", "option" => _l("week") , ], ["value" => "2-week", "option" => "2 " . _l("weeks") , ], ["value" => "1-month", "option" => "1 " . _l("month") , ], ["value" => "2-month", "option" => "2 " . _l("months") , ], ["value" => "3-month", "option" => "3 " . _l("months") , ], ["value" => "6-month", "option" => "6 " . _l("months") , ], ["value" => "1-year", "option" => "1 " . _l("year") , ], ["value" => "custom", "option" => _l("recurring_custom") , ], ];
if($reminder->custom_recurring != 1){
$value = isset($reminder) && $reminder->repeat_every != '' ? $reminder->repeat_every .'-'.$reminder->recurring_type: '';
}else{
  $value = 'custom'; 
}
echo render_select("repeat_every", $related, ["value", "option"], "task_repeat_every", $value);
?>
                                            </div>
                                            <div class="col-md-12 recurring_custom <?php if ((isset($reminder) && $reminder->custom_recurring != 1) || !isset($reminder))
{
    echo "hide";
} ?>">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <?php $value = isset($reminder) && $reminder->custom_recurring == 1 ? $reminder->repeat_every: 1; ?>
                                                        <?php echo render_input("repeat_every_custom", "", $value, "number", ["min" => 1]); ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select name="repeat_type_custom" id="repeat_type_custom" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l("dropdown_non_selected_tex"); ?>">
                                                            <option value="day" <?php echo (isset($reminder->repeat_type_custom) && $reminder->repeat_type_custom == 'day')?'selected':''; ?>><?php echo _l("task_recurring_days"); ?></option>
                                                            <option value="week"  <?php echo (isset($reminder->repeat_type_custom) &&$reminder->repeat_type_custom == 'week')?'selected':''; ?>><?php echo _l("task_recurring_weeks"); ?></option>
                                                            <option value="month"  <?php echo ( isset($reminder->repeat_type_custom) &&$reminder->repeat_type_custom == 'month')?'selected':''; ?>><?php echo _l("task_recurring_months"); ?></option>
                                                            <option value="year"  <?php echo (isset($reminder->repeat_type_custom) &&$reminder->repeat_type_custom == 'year')?'selected':''; ?>><?php echo _l("task_recurring_years"); ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="cycles_wrapper" class="<?php if (!isset($reminder) || (isset($reminder) && $reminder->recurring == 0))
{
    echo " hide";
} ?> col-md-12">
                                                <?php $value = isset($reminder) ? $reminder->cycles : 0; ?>
                                                <div class="form-group recurring-cycles">
                                                    <label for="cycles"><?php echo _l("recurring_total_cycles"); ?>
                                                        <?php if (isset($reminder) && $reminder->total_cycles > 0)
{
    echo "<small>" . _l("cycles_passed", $reminder->total_cycles) . "</small>";
} ?>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" <?php if ($value == 0)
{
    echo " disabled";
} ?> name="cycles" id="cycles" value="<?php echo $value; ?>" <?php if (isset($reminder) && $reminder->total_cycles > 0)
{
    echo 'min="' . $reminder->total_cycles . '"';
} ?>>
                                                        <div class="input-group-addon">
                                                            <div class="checkbox">
                                                                <input type="checkbox" <?php if ($value == 0)
{
    echo " checked";
} ?> id="unlimited_cycles">
                                                                <label for="unlimited_cycles"><?php echo _l("cycles_infinity"); ?></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                          

 <!----------------------Repeat Every end------------------------------>
                              
                                 <div class="col-md-12">
                                    <?php
if (isset($reminder))
{
    $val = $reminder->description;
}
else
{
    $val = "";
}
if ($reminder->is_complete == "0")
{
    $btnSts = "disabled";
    echo render_textarea("description", "reminder_description", $val, ["disabled" => true]);
}
else
{
    $btnSts = "";
    echo render_textarea("description", "reminder_description", $val);
}
?>
                                 </div>
                                 <input type="hidden" name="id" value="<?php echo isset($reminder) ? $reminder->id : ""; ?>">
                                 <div class="col-sm-12 float-right ">
                                    <button class="btn btn-info  float-right mr-5" <?php echo $btnSts; ?> type="submit" style="float: right;margin-left: 20px">
                                       <?php echo _l("save_and_exit"); ?>
                                    </button>
                                    <?php if ($reminder->is_complete == "1")
{ ?>
                                       <a href="<?php echo admin_url(); ?>reminder/complete_reminder/<?php echo $reminder->id; ?>">
                                          <button class="btn btn-success  float-right" type="button" style="float: right;">
                                             <?php echo _l("complete"); ?>
                                          </button>
                                       </a>
                                    <?php
}
else
{ ?>
                                       <a href="<?php echo admin_url(); ?>reminder/reopen_reminder/<?php echo $reminder->id; ?>">
                                          <button class="btn btn-success  float-right" type="button" style="float: right;">
                                             <?php echo _l("reopen_reminder"); ?>
                                          </button>
                                       </a>
                                    <?php
} ?>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <?php echo form_close(); ?>
                  </div>
                  <hr class="hr-panel-heading" />
                  <div class="clearfix"></div>
               </div>
               <div role="tabpanel" class="tab-pane" id="tab_emails_tracking">
                  <?php $this
    ->load
    ->view("admin/includes/emails_tracking", ["tracked_emails" => get_tracked_emails($reminder->rel_id, $reminder->rel_type) , ]); ?>
               </div>
               <div role="tabpanel" class="tab-pane" id="tab_tasks">
                  <?php init_relation_tasks_table(["data-new-rel-id" => $reminder->id, "data-new-rel-type" => "proposal", ]); ?>
               </div>
               <div role="tabpanel" class="tab-pane" id="tab_reminders">
                  <a href="#" data-toggle="modal" class="btn btn-info" data-target=".reminder-modal-proposal-<?php echo $reminder->id; ?>"><i class="fa fa-bell-o"></i> <?php echo _l("proposal_set_reminder_title"); ?></a>
                  <hr />
                  <?php render_datatable([_l("reminder_description") , _l("reminder_date") , _l("reminder_staff") , _l("reminder_is_notified") , ], "reminders"); ?>
                  <?php $this
    ->load
    ->view("admin/includes/modals/reminder", ["id" => $reminder->id, "name" => "proposal", "members" => $members, "reminder_title" => _l("proposal_set_reminder_title") , ]); ?>
               </div>
               <div role="tabpanel" class="tab-pane ptop10" id="tab_views">
                  <?php
$views_activity = get_views_tracking("reminder", $reminder->id);
if (count($views_activity) === 0)
{
    echo '<h4 class="no-margin">' . _l("not_viewed_yet", _l("proposal_lowercase")) . "</h4>";
}
foreach ($views_activity as $activityy)
{ ?>
                     <p class="text-success no-margin">
                        <?php echo _l("view_date") . ": " . _dt($activityy["date"]); ?>
                     </p>
                     <p class="text-muted">
                        <?php echo _l("view_ip") . ": " . $activityy["view_ip"]; ?>
                     </p>
                     <hr />
                  <?php
}
?>
               </div>
               <div role="tabpanel" class="tab-pane" id="tab_activity">
                  <div class="row">
                     <div class="col-md-12">
                        <div class="row proposal-comments mtop15">
                           <div class="col-md-12">
                              <!-- <div id="proposal-comments"></div> -->
                              <div class="clearfix"></div>
                              <textarea name="content" id="comment" rows="4" class="form-control mtop15 reminder-comment"></textarea>
                              <button type="button" class="btn btn-info mtop10 pull-right" onclick="add_reminder_comment();"><?php echo _l("reminder_add_comment"); ?>
                              </button>
                           </div>
                        </div>
                        <div class="activity-feed">
                           <?php foreach ($activity as $activity)
{
    $_custom_data = false; ?>
                              <div class="feed-item" data-sale-activity-id="<?php echo $activity["id"]; ?>">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($activity["date"]); ?>">
                                       <?php echo time_ago($activity["date"]); ?>
                                    </span>
                                 </div>
                                 <div class="text">
                                    <?php if (!empty($activity["staffid"]) && is_numeric($activity["staffid"]) && $activity["staffid"] != 0)
    { ?>
                                       <a href="<?php echo admin_url("profile/" . $activity["staffid"]); ?>">
                                          <?php echo staff_profile_image($activity["staffid"], ["staff-profile-xs-image pull-left mright5", ]); ?>
                                       </a>
                                    <?php
    } ?>
                                    <?php
    $additional_data = "";
    if (!empty($activity["additional_data"]))
    {
        $additional_data = unserialize($activity["additional_data"]);
        $i = 0;
        foreach ($additional_data as $data)
        {
            if (strpos($data, "<original_status>") !== false)
            {
                $original_status = get_string_between($data, "<original_status>", "</original_status>");
                $additional_data[$i] = format_estimate_status($original_status, "", false);
            }
            elseif (strpos($data, "<new_status>") !== false)
            {
                $new_status = get_string_between($data, "<new_status>", "</new_status>");
                $additional_data[$i] = format_estimate_status($new_status, "", false);
            }
            elseif (strpos($data, "<status>") !== false)
            {
                $status = get_string_between($data, "<status>", "</status>");
                $additional_data[$i] = format_estimate_status($status, "", false);
            }
            elseif (strpos($data, "<custom_data>") !== false)
            {
                $_custom_data = get_string_between($data, "<custom_data>", "</custom_data>");
                unset($additional_data[$i]);
            }
            $i++;
        }
    }
    $_formatted_activity = _l($activity["description"], $additional_data);
    if ($_custom_data !== false)
    {
        $_formatted_activity .= "" . $_custom_data;
    }
    if (!empty($activity["full_name"]))
    {
        $_formatted_activity = $activity["full_name"] . " - " . $_formatted_activity;
    }
    echo $_formatted_activity;
    if (is_admin())
    {
        echo '<a href="#" class="pull-right text-danger" onclick="delete_reminder_activity(' . $activity["id"] . '); return false;"><i class="fa fa-remove"></i></a>';
    }
?>
                                 </div>
                              </div>
                           <?php
} ?>
                        </div>
                     </div>
                  </div>
               </div>
               <div role="tabpanel" class="tab-pane" id="notes_tab" >
              <?php echo form_open(admin_url() . "reminder/add_note/" . $reminder->id, ["id" => "reminder-notes", "class" => "reminder-notes"]); ?>
              <?php echo render_textarea("description", "", "", ["required" => true, ]); ?>
              <div class="text-right">
                <button type="submit" class="btn btn-info mtop15 mbot15"><?php echo _l("estimate_add_note"); ?></button>
              </div>
              <?php echo form_close(); ?>
              <hr />
              <div class="panel_s mtop20 no-shadow" id="reminders_notes_area"></div>
               
            </div>
               
               
            </div>
         </div>
      </div>
   </div>
</div>
<div id="modal-wrapper"></div>
<script>
   "use strict";
   init_datepicker();
   init_selectpicker();
   init_form_reminder();
   var reminder_id = '<?php echo $reminder->id; ?>';
   var rel_type = $("#rel_type").val();
   var customer_id = $("#customer").val();
$(function(){
   validate_reminder_form();
      $.get(admin_url + 'reminder/get_related_doc/' + rel_type + '/' + customer_id, function(response) {
         if (response) {
            $('#rel_id').html(response);
            $('#rel_id').selectpicker('refresh');
         }
      });
})
   function add_reminder_comment() {
      var comment = $('#comment').val();
      if (comment == '') {
         return;
      }
      var data = {};
      data.content = comment;
      data.reminder_id = reminder_id;
      $('body').append('<div class="dt-loader"></div>');
      $.post(admin_url + 'reminder/add_reminder_comment', data).done(function(response) {
         response = JSON.parse(response);
         $('body').find('.dt-loader').remove();
         if (response.success == true) {
            $('.reminder-comment').val('');
            get_reminder_activity(reminder_id);
         }
      });

   }

   function get_reminder_activity() {
      if (typeof(reminder_id) == 'undefined') {
         return;
      }
      requestGet('reminder/get_reminder_activity/' + reminder_id).done(function(response) {
         $('body').find('.activity-feed').html(response);
      });
   }

   function delete_reminder_activity(id) {
      if (confirm_delete()) {
         requestGet('reminder/delete_reminder_activity/' + id).done(function() {
            $("body").find('[data-sale-activity-id="' + id + '"]').remove();
         });
      }
   }
   function reminder_small_table_full_view() {
      $('#small-table').toggleClass('hide');
      $('.small-table-right-col').toggleClass('col-md-12 col-md-6');
      $(window).trigger('resize');
   }

   $(document).ready(function() {
      $('.reminder_service').on('change', function() {
         var service_name = $(this).attr('name');
         if (service_name == "services[]") {
            var service_data = $(this).val();
            if (service_data != "") {
               $('body').append('<div class="dt-loader"></div>');
               $.post(admin_url + 'reminder/getreminderaddService', {
                  data_service: service_data
               }).done(function(response) {
                  $('#amountreminder').val(response);
               });
            } else {
               $('#amountreminder').val("0");
            }
         }
      });
   });
    $(document).ready(function() {
          get_reminders_notes(reminder_id, 'reminder');
    $('#reminder-notes').on('submit', function() {
    var form = $(this);
    //alert("jgj");
    if (form.find('textarea[name="description"]').val() === '') { return; }

    $.post(form.attr('action'), $(form).serialize()).done(function(rel_id) {
        // Reset the note textarea value
        form.find('textarea[name="description"]').val('');
        // Reload the notes 
        get_reminders_notes(rel_id, 'reminder');
    });
    return false;
});
});
function get_reminders_notes(id, controller) {
  requestGet(controller + '/reminder/get_notes/' + id).done(function(response) {
      $('#reminders_notes_area').html(response);
      var totalNotesNow = $('#sales-notes-wrapper').attr('data-total');
      if (totalNotesNow > 0) {
          $('.notes-total').html('<span class="badge">' + totalNotesNow + '</span>').removeClass('hide');
      }
  });
}
</script>
