<?php
defined('BASEPATH') or exit('No direct script access allowed');
define('REMINDERS_OTHER_ATTACHMENTS_FOLDER', FCPATH . 'uploads/reminders/others' . '/');
function get_complete_reminder($id)
{
	$CI = &get_instance();
	if ($id) {
		$CI->db->where('id', $id);
		$data = $CI->db->select('is_complete')->from(db_prefix() . 'reminders')->get()->row();
	}
	if ($data) {
		return $data->is_complete;
	}
	return '';
}

function update_reminder_data($reminder)
{
	if ($reminder) {
		$CI = &get_instance();
		$data = array(
			'recurring' => $reminder['recurring'] + 1,
			'last_recurring_date' => date('Y-m-d')
		);
		$CI->db->where('id', $reminder['id']);
		$CI->db->update(db_prefix() . 'reminders', $data);
		return true;
	}
}

function insert_service($data)
{
	if ($data) {
		$CI = &get_instance();
		$CI->db->insert(db_prefix() . 'reminder_services', $data);
		$id=$CI->db->insert_id();
		return $id;
	}
}
function update_service($data, $id)
{
	if ($id) {
		$CI = &get_instance();
		$CI->db->where('id', $id);
		$CI->db->update(db_prefix() . 'reminder_services', $data);
		return true;
	}
}

function delete_service_data($id, $table)
{
	if ($table) {
		$CI = &get_instance();
		$CI->db->where('id', $id);
		$CI->db->delete(db_prefix() . $table);
		return true;
	}
}

function get_service_reminder_data(){
	$CI = &get_instance();
	return $CI->db->get(db_prefix() . 'reminder_services')->result_array();
}
function get_reminder_service_data($id)
{
	$CI = &get_instance();
	if($CI->db->table_exists(db_prefix() . 'reminder_services')){
		return $CI->db->where('id', $id)->get(db_prefix() . 'reminder_services')->row_array();
	}
}

function get_reminderservice_amount($id)
{
	if ($id) {
		$CI = &get_instance();
		$CI->db->where('id', $id);
		$CI->db->select('service_amount');
		return $CI->db->get(db_prefix() . 'reminder_services')->row_array();
	}
}

function insert_reminder_services($id, $data)
{
	if ($id) {
		$CI = &get_instance();
		foreach ($data as $res) {
			$insert_data = array(
				'rem_id' => $id,
				'service_id' => $res
			);
			$CI->db->insert(db_prefix() . 'reminder_service_value', $insert_data);
		}
		return true;
	}
}

function format_reminder_status($status, $classes = '', $label = true)
{
	$id = $status;
	if ($status == 1) {
		$status      = _l('reminder_run');
		$label_class = 'success';
	} elseif ($status == 0) {
		$status      = _l('complete');
		$label_class = 'danger';
	}
	if ($label == true) {
		return '<span class="label label-' . $label_class . ' ' . $classes . ' s-status proposal-status-' . $id . '">' . $status . '</span>';
	}
	return $status;
}

function reminder_mail_service_template($class)
{
	$CI = &get_instance();
	$params = func_get_args();
	unset($params[0]);
	$params = array_values($params);
	$path = get_reminder_mail_template_path($class, $params);
	if (!file_exists($path)) {
		if (!defined('CRON')) {
			show_error('Mail Class Does Not Exists [' . $path . ']');
		} else {
			return false;
		}
	}
	if (!class_exists($class, false)) {
		include_once($path);
	}
	$instance = new $class(...$params);
	return $instance;
}

function reminder_send_mail_template()
{
	$params = func_get_args();
	return reminder_mail_template(...$params)->send();
}
function reminder_mail_template($class)
{
	$CI = &get_instance();
	$params = func_get_args();
	unset($params[0]);
	$params = array_values($params);
	$path = get_reminder_mail_template_path($class, $params);
	if (!file_exists($path)) {
		if (!defined('CRON')) {
			show_error('Mail Class Does Not Exists [' . $path . ']');
		} else {
			return false;
		}
	}
	if (!class_exists($class, false)) {
		include_once($path);
	}
	$instance = new $class(...$params);
	return $instance;
}
function get_reminder_mail_template_path($class, &$params)
{
	$CI  = &get_instance();
	$dir = APP_MODULES_PATH . 'reminder/libraries/mails/';
	if (isset($params[0]) && is_string($params[0]) && is_dir(module_dir_path($params[0]))) {
		$module = $CI->app_modules->get($params[0]);
		if ($module['activated'] === 1) {
			$dir = module_libs_path($params[0]) . 'mails/';
		}
		unset($params[0]);
		$params = array_values($params);
	}
	return $dir . ucfirst($class) . '.php';
}
function send_sms_reminder($to, $message)
{
	$account_sid = get_option('sms_twilio_account_sid');
	$auth_token = get_option('sms_twilio_auth_token');
	$twilio_number = get_option('sms_twilio_phone_number');
	$client = new Twilio\Rest\Client($account_sid, $auth_token);
	$sms_ids = [];
	$errors = [];
	try {
		$message = $client->messages->create($to, array('from' => $twilio_number, 'body' => $message));
		$sms_ids[] = $message->sid;
	} catch (\Throwable $e) {
		$errors[] = [
			'phone_number' => $to,
			'message' => $e->getMessage()
		];
	}
	return json_encode([
		'success' => true,
		'sent' => $sms_ids,
		'errors'  => $errors
	]);
}
function is_email_template_active_reminder($slug)
{
	return total_rows(db_prefix() . 'emailtemplates', ['slug' => $slug, 'active' => 1]) > 0;
}
function get_staff_phone_number($staff_id)
{
	$CI  = &get_instance();
	$data = null;
	if ($staff_id) {
		$CI->db->where('staffid', $staff_id);
		$staff = $CI->db->select('phonenumber')->from(db_prefix() . 'staff')->get()->row();
		if (isset($staff) && isset($staff->phonenumber)) {
			$data = $staff->phonenumber;
		}
	}
	return $data;
}


function handle_reminder_other_attachments_upload($id)
{
	$path          = REMINDERS_OTHER_ATTACHMENTS_FOLDER . $id . '/';
	$CI            = &get_instance();
	$totalUploaded = 0;
	if (
		isset($_FILES['file']['name'])
		&& ($_FILES['file']['name'] != '' || is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)
	) {

		if (!is_array($_FILES['file']['name'])) {
			$_FILES['file']['name']     = [$_FILES['file']['name']];
			$_FILES['file']['type']     = [$_FILES['file']['type']];
			$_FILES['file']['tmp_name'] = [$_FILES['file']['tmp_name']];
			$_FILES['file']['error']    = [$_FILES['file']['error']];
			$_FILES['file']['size']     = [$_FILES['file']['size']];
		}

		_file_attachments_index_fix('file');
		for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
			hooks()->do_action('before_upload_reminder_attachment', $id);
			// Get the temp file path
			$tmpFilePath = $_FILES['file']['tmp_name'][$i];

			// Make sure we have a filepath
			if (!empty($tmpFilePath) && $tmpFilePath != '') {
				if (_perfex_upload_error($_FILES['file']['error'][$i]) || !_upload_extension_allowed($_FILES['file']['name'][$i])) {
					continue;
				}
				if (!file_exists($path)) {
					mkdir($path, 0755, true);
					fopen(rtrim($path, '/') . '/' . 'index.html', 'w');
				}

				$filename    = unique_filename($path, $_FILES['file']['name'][$i]);
				$newFilePath = $path . $filename;
				// Upload the file into the temp dir
				if (move_uploaded_file($tmpFilePath, $newFilePath)) {
					$attachment   = [];
					$attachment[] = [
						'file_name' => $filename,
						'filetype'  => $_FILES['file']['type'][$i],
					];
					if (is_image($newFilePath)) {
						create_img_thumb($newFilePath, $filename);
					}
					$CI->misc_model->add_attachment_to_database($id, 'reminder', $attachment);
					$totalUploaded++;
				}
			}
		}
	}

	return (bool) $totalUploaded;
}

function getRimenderOtherAttachement($id, $field = array())
{
	$CI  = &get_instance();
	return $CI->reminder_model->getAttachments($id, $field);
}

function get_email_template_row($id)
{
	$CI  = &get_instance();
	if (!class_exists('emails_model', false)) {
		$CI->load->model('emails_model');
	}
	$template = $CI->emails_model->get_email_template_by_id($id);
	return $template;
}
function get_available_reminder_merge_fields()
{
	$CI  = &get_instance();
	if (!class_exists('app_reminder_merge_fields', false)) {
		$CI->load->library('reminder/merge_fields/app_reminder_merge_fields');
	}
	return $CI->app_reminder_merge_fields->all();
}
