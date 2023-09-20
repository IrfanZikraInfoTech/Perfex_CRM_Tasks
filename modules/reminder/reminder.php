<?php
defined('BASEPATH') or exit('No direct script access allowed');
/*
Module Name: Reminder
Description: create custom reminder and notify
Version: 1.0.4
Author: Zonvoir
Author URI: https://zonvoir.com/
Requires at least: 2.3.*
*/
if (!defined('MODULE_REMINDER')) {
    define('MODULE_REMINDER', basename(__DIR__));
}
if (!defined('APP_DISABLE_CRON_LOCK')) {
    define('APP_DISABLE_CRON_LOCK', true);
}
hooks()->add_action('admin_init', 'reminder_module_init_menu_items');
hooks()->add_action('admin_init', 'reminder_permissions');
hooks()->add_action('admin_init', 'reminder_module_activation_hook');
hooks()->add_filter('staff_reminder_merge_fields', 'reminder_send_with_subject', 10, 2);

hooks()->add_action('after_cron_run', 'contact_reminder_send');

hooks()->add_action('admin_init', 'reminder_init_menu_items');
hooks()->add_action('admin_init', 'add_total_amount_field');

function reminder_init_menu_items()
{
    /**
     * If the logged in user is administrator, add custom menu in Setup
     */
    if (is_admin()) {
        $CI = &get_instance();
        $CI->app_menu->add_setup_menu_item('reminder-options', [
            'collapse' => true,
            'name'     => 'Reminder SETTINGS',
            'position' => 66,
        ]);

        $CI->app_menu->add_setup_children_item('reminder-options', [
            'slug'     => 'reminder-service',
            'name'     => _l('reminder_services'),
            'href'     => admin_url('reminder/services'),
            'position' => 5,
        ]);
    }
}

function contact_reminder_send()
{
    $CI = &get_instance();
    $CI->load->library('reminder/mails/reminder_mail_template');
    $CI->db->select(db_prefix() . 'reminders.*, ' . db_prefix() . 'contacts.email, ' . db_prefix() . 'contacts.phonenumber, ' . db_prefix() . 'contacts.firstname, ' . db_prefix() . 'contacts.lastname');
    $CI->db->from(db_prefix() . 'reminders');
    $CI->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.id=' . db_prefix() . 'reminders.contact');
    $CI->db->where(db_prefix() . 'reminders.notify_by_email_client', '2');
    $CI->db->or_where(db_prefix() . 'reminders.notify_by_sms_client', '2');
    $reminders = $CI->db->get()->result_array();
    $notifiedUsers = [];
    if (isset($reminders)) {
        foreach ($reminders as $reminder) {
            if (strtotime(date('Y-m-d H:i:s')) >= strtotime($reminder['date'])) {
                if (!empty($reminder['recurring_type']) && !empty($reminder['last_recurring_date'])) {
                    if ($reminder['recurring_type'] == "day") {
                        $days = ($reminder['repeat_every'] * 1);
                        $end_date = date('Y-m-d H:i:s', strtotime($reminder['last_recurring_date'] . ' +' . $days . ' day'));
                    }
                    if ($reminder['recurring_type'] == "week") {
                        $days = ($reminder['repeat_every'] * 7);
                        $end_date = date('Y-m-d H:i:s', strtotime($reminder['last_recurring_date'] . ' +' . $days . ' day'));
                    }
                    if ($reminder['recurring_type'] == "month") {
                        $days = ($reminder['repeat_every'] * 28);
                        $end_date = date('Y-m-d H:i:s', strtotime($reminder['last_recurring_date'] . ' +' . $days . ' day'));
                    }
                    if ($reminder['recurring_type'] == "year") {
                        $days = ($reminder['repeat_every'] * 365);
                        $end_date = date('Y-m-d H:i:s', strtotime($reminder['last_recurring_date'] . ' +' . $days . ' day'));
                    }
                    if (strtotime(date('Y-m-d H:i:s')) >= strtotime($end_date)) {

                        if (!empty($reminder['cycles']) && $reminder['recurring'] < $reminder['cycles']) {
                           

                            if ($reminder['rel_type'] == "service") {
                                $data = service_relation_data($reminder['services'], $reminder);
                                $notified = $data['notified'];
                                $reminder = $data['reminder'];
                            } else {
                                 $rel_data   = get_relation_data($reminder['rel_type'], $reminder['rel_id']);
                                $rel_values = get_relation_values($rel_data, $reminder['rel_type']);
                                $notificationLink = str_replace(admin_url(), '', $rel_values['link']);
                                $notificationLink = ltrim($notificationLink, '/');
                                $notified = add_notification([
                                    'fromcompany'     => true,
                                    'touserid'        => $reminder['contact'],
                                    'description'     => 'not_new_reminder_for',
                                    'link'            => $notificationLink,
                                    'additional_data' => serialize([
                                        $rel_values['name'] . ' - ' . strip_tags(mb_substr($reminder['description'], 0, 50)) . '...',
                                    ]),
                                ]);
                            }

                            if ($notified) {
                                array_push($notifiedUsers, $reminder['contact']);
                            }
                            set_reminder_data($reminder);
                        } else {
                           
                            if ($reminder['rel_type'] == "service") {
                                $data = service_relation_data($reminder['services'], $reminder);
                                $notified = $data['notified'];
                                $reminder = $data['reminder'];
                            } else {
                                 $rel_data   = get_relation_data($reminder['rel_type'], $reminder['rel_id']);
                                $rel_values = get_relation_values($rel_data, $reminder['rel_type']);
                                $notificationLink = str_replace(admin_url(), '', $rel_values['link']);
                                $notificationLink = ltrim($notificationLink, '/');
                                $notified = add_notification([
                                    'fromcompany'     => true,
                                    'touserid'        => $reminder['contact'],
                                    'description'     => 'not_new_reminder_for',
                                    'link'            => $notificationLink,
                                    'additional_data' => serialize([
                                        $rel_values['name'] . ' - ' . strip_tags(mb_substr($reminder['description'], 0, 50)) . '...',
                                    ]),
                                ]);
                            }
                            if ($notified) {
                                array_push($notifiedUsers, $reminder['contact']);
                            }
                            set_reminder_data($reminder);
                        }
                    }
                }
                if (empty($reminder['last_recurring_date'])) {

                    if (!empty($reminder['cycles']) && $reminder['recurring'] < $reminder['cycles']) {
                        
                        if ($reminder['rel_type'] == "service") {
                            $data = service_relation_data($reminder['services'], $reminder);
                            $notified = $data['notified'];
                            $reminder = $data['reminder'];
                        } else {
                            $rel_data   = get_relation_data($reminder['rel_type'], $reminder['rel_id']);
                            $rel_values = get_relation_values($rel_data, $reminder['rel_type']);
                            $notificationLink = str_replace(admin_url(), '', $rel_values['link']);
                            $notificationLink = ltrim($notificationLink, '/');
                            $notified = add_notification([
                                'fromcompany'     => true,
                                'touserid'        => $reminder['contact'],
                                'description'     => 'not_new_reminder_for',
                                'link'            => $notificationLink,
                                'additional_data' => serialize([
                                    $rel_values['name'] . ' - ' . strip_tags(mb_substr($reminder['description'], 0, 50)) . '...',
                                ]),
                            ]);
                        }

                        if ($notified) {
                            array_push($notifiedUsers, $reminder['contact']);
                        }
                        set_reminder_data($reminder);
                    } else {
                        if ($reminder['rel_type'] == "service") {
                            $data = service_relation_data($reminder['services'],$reminder);
                            $notified = $data['notified'];
                            $reminder = $data['reminder'];
                        } else {
                            $rel_data   = get_relation_data($reminder['rel_type'], $reminder['rel_id']);
                            $rel_values = get_relation_values($rel_data, $reminder['rel_type']);
                            $notificationLink = str_replace(admin_url(), '', $rel_values['link']);
                            $notificationLink = ltrim($notificationLink, '/');
                            $notified = add_notification([
                                'fromcompany'     => true,
                                'touserid'        => $reminder['contact'],
                                'description'     => 'not_new_reminder_for',
                                'link'            => $notificationLink,
                                'additional_data' => serialize([
                                    $rel_values['name'] . ' - ' . strip_tags(mb_substr($reminder['description'], 0, 50)) . '...',
                                ]),
                            ]);
                        }
                        if ($notified) {
                            array_push($notifiedUsers, $reminder['contact']);
                        }
                        set_reminder_data($reminder);
                    }
                }
            }
        }
    }
    pusher_trigger_notification($notifiedUsers);
}
function set_reminder_data($reminder)
{

    if ($reminder['notify_by_email_client'] == '2') {
        if ($reminder['rel_type'] == 'service') {
            $template = reminder_mail_service_template('service_send_reminder', $reminder);
            $template->send();
        } else {
            $template = reminder_mail_template('contact_send_reminder', $reminder);
            $template->send();
        }
    }
    if ($reminder['notify_by_sms_client'] == '2') {
        $account_sid = get_option('sms_twilio_account_sid');
        if($account_sid !=''){
        $resposnse = send_sms_reminder($reminder['phonenumber'], $reminder['description']);
    }
    }
    update_reminder_data($reminder);
}
function service_relation_data($rel_data, $reminder)
{
    $service_data = [];
    $amount = 0;
    $rel_data=explode(',', $rel_data);
    foreach ($rel_data as $service_id) {
        $services = get_reminder_service_data($service_id);
        array_push($service_data, $services['service_name']);
        $amount = $amount + $services['service_amount'];
    }
    $reminder['total_amount'] = $amount;
    $reminder['total_services'] = implode(",", $service_data);
    $rel_values['name'] = $reminder['firstname'] . ' ' . $reminder['lastname'];
    $notified = add_notification([
        'fromcompany'     => true,
        'touserid'        => $reminder['contact'],
        'description'     => 'not_new_reminder_for',
        'link'            => "",
        'additional_data' => serialize([
            $rel_values['name'] . ' - ' . strip_tags(mb_substr($reminder['description'], 0, 50)) . '...',
        ]),
    ]);
    $data = array('reminder' => $reminder, 'notified' => $notified);
    return $data;
}

function reminder_send_with_subject($fields, $data)
{
    if (!empty($data['reminder'])) {
        $reminder = $data['reminder'];
        $rel_type = !empty($reminder->rel_type) ? $reminder->rel_type : '';
        if ($rel_type != '') {
            if ($rel_type == "custom_reminder") {
                $fields['{staff_reminder_relation_name}'] = 'Custom Reminder';
                $fields['{staff_reminder_relation_link}'] =  admin_url('reminder');
            }
            if($rel_type =='service'){
             $fields['{staff_reminder_relation_name}'] = 'Services';
             $fields['{staff_reminder_relation_link}'] =  admin_url('reminder')."#".$reminder->id;
             }
            return $fields;
        }
        
    }
}
function reminder_permissions()
{
    $capabilities = [];
    $capabilities['capabilities'] = [
        'view_own' => _l('permission_view_own'),
        'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    if (function_exists('register_staff_capabilities')) {
        register_staff_capabilities('reminder', $capabilities, _l('reminder'));
    }
}
/** 
 * Register activation module hook
 */
register_activation_hook(MODULE_REMINDER, 'reminder_module_activation_hook');
function reminder_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}
/**
 * Register uninstall module hook
 */
register_uninstall_hook(MODULE_REMINDER, 'reminder_module_uninstall_hook');
function reminder_module_uninstall_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/uninstall.php');
}
get_instance()->load->helper(MODULE_REMINDER . '/reminder');
register_language_files(MODULE_REMINDER, [MODULE_REMINDER]);
function reminder_module_init_menu_items()
{
    $CI = &get_instance();
    $CI->app_menu->add_sidebar_menu_item('reminder', [
        'slug'     => 'reminder',
        'name'     => _l('reminder'),
        'position' => 6,
        'icon'     => 'fa fa-calendar menu-icon',
        'href'     => admin_url('reminder')
    ]);
}
function add_total_amount_field()
{
    $CI = &get_instance();
    if (!$CI->db->field_exists('total_amount', db_prefix() . 'reminders')) {
    $CI->db->query('ALTER TABLE `tblreminders`
   ADD `total_amount` int(40) DEFAULT NULL;');
  }
}

hooks()->add_filter('available_merge_fields', function ($available) {
    $CI = &get_instance();
    $reminder_available = get_available_reminder_merge_fields();
    $rm_fields = [];
    $service_fields = [];
    foreach ($reminder_available as $rm_key => $rm_merge_fields) {
        if (array_key_exists('reminder', $rm_merge_fields)) {
            $rm_fields = $reminder_available[$rm_key];
        }
        if (array_key_exists('service', $rm_merge_fields)) {
            $service_fields = $reminder_available[$rm_key];
        }
    }
    $uri = $CI->uri->uri_to_assoc(1);
    if (isset($uri) && !empty($uri)) {
        if (isset($uri['admin']) && $uri['admin'] == 'emails' && is_numeric($uri['email_template'])) {
            $template = get_email_template_row($uri['email_template']);
            if (isset($template) && !empty($template) && $template->slug == 'reminder-send-to-contact') {
                $key = null;
                foreach ($available as $key => $merge_fields) {
                    if (array_key_exists('client', $merge_fields)) {
                        $available[$key]['client'] = $rm_fields['reminder'];
                    }
                }
            }
            if (isset($template) && !empty($template) && $template->slug == 'reminder-service-send-to-contact') {
                $key = null;
                foreach ($available as $key => $merge_fields) {
                    if (array_key_exists('client', $merge_fields)) {
                        $available[$key]['client'] = $service_fields['service'];
                    }
                }
            }
        }
    }
    return $available;
});
