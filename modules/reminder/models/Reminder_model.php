<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reminder_model extends App_Model
{
	private $statuses;

	public function __construct()
	{
		parent::__construct();
	}
	public function get_statuses()
	{
		return $this->statuses;
	}
	public function get_sale_agents()
	{
		return $this->db->query('SELECT DISTINCT(assigned_to) as sale_agent FROM ' . db_prefix() . 'reminders WHERE assigned_to != 0')->result_array();
	}
	public function get_reminder_years()
	{
		return $this->db->query('SELECT DISTINCT(YEAR(date)) as year FROM ' . db_prefix() . 'reminders')->result_array();
	}
	public function getCustomersData()
	{
		return $this->db->query('SELECT DISTINCT(customer) as userid, ' . db_prefix() . 'clients.company  FROM ' . db_prefix() . 'reminders JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid=' . db_prefix() . 'reminders.customer')->result_array();
	}
	public function get_related_doc($rel_type, $customer_id)
	{
		if ($rel_type == "quotes") {
			$this->db->where(['rel_id' => $customer_id, 'rel_type' => 'customer']);
			return $this->db->get(db_prefix() . 'proposals')->result_array();
		} else if ($rel_type == "invoice") {
			$this->db->where(['clientid' => $customer_id]);
			return $this->db->get(db_prefix() . 'invoices')->result_array();
		} else if ($rel_type == "estimate") {
			$this->db->where(['clientid' => $customer_id]);
			return $this->db->get(db_prefix() . 'estimates')->result_array();
		} else if ($rel_type == "credit_note") {
			$this->db->where(['clientid' => $customer_id]);
			return $this->db->get(db_prefix() . 'creditnotes')->result_array();
		} elseif ($rel_type == "tickets") {
			$this->db->where(['userid' => $customer_id]);
			return $this->db->get(db_prefix() . 'tickets')->result_array();
		}
	}
	public function get_contact_data_values($rel_id, $rel_type)
	{
		$data = new StdClass();
		if ($rel_type == 'customer') {
			$this->db->where('userid', $rel_id);
			$_data = $this->db->get(db_prefix() . 'clients')->row();
			$primary_contact_id = get_primary_contact_user_id($rel_id);
			if ($primary_contact_id) {
				$contact     = $this->clients_model->get_contact($primary_contact_id);
				$data->email = $contact->email;
			}

			$data->phone            = isset($_data) ? $_data->phonenumber : '';

			$data->is_using_company = false;
			if (isset($contact)) {
				$data->to = $contact->firstname . ' ' . $contact->lastname;
			} else {
				if (isset($_data) && !empty($_data->company)) {
					$data->to               = $_data->company;
					$data->is_using_company = true;
				}
			}
			$data->company = isset($_data) ? $_data->company : '';
			$data->address = isset($_data) ? clear_textarea_breaks($_data->address) : '';
			$data->zip     = isset($_data) ? $_data->zip : '';
			$data->country = isset($_data) ? $_data->country : '';
			$data->state   = isset($_data) ? $_data->state : '';
			$data->city    = isset($_data) ? $_data->city : '';
			$default_currency = $this->clients_model->get_customer_default_currency($rel_id);
			if ($default_currency != 0) {
				$data->currency = $default_currency;
			}
			//Field
			$contacts     = $this->clients_model->get_contacts($rel_id);
			if (isset($contacts)) {
				foreach ($contacts as $key => $contact) {
					$contacts[$key]['id'] = $contact['id'];
					$contacts[$key]['name'] = $contact['firstname'] . ' ' . $contact['lastname'];
				}
			}

			$data->field_to = render_select('contact', $contacts, ['id', 'name'], 'contact');
		} elseif ($rel_type = 'lead') {
			$this->db->where('id', $rel_id);
			$_data       = $this->db->get(db_prefix() . 'leads')->row();
			$data->phone = $_data->phonenumber;
			$data->is_using_company = false;
			if (empty($_data->company)) {
				$data->to = $_data->name;
			} else {
				$data->to               = $_data->company;
				$data->is_using_company = true;
			}
			$data->field_to = render_input('contact', 'contact', $data->to);
		}
		return $data;
	}
	public function add($data)
	{
		$save_and_send = isset($data['save_and_send']);
		$data['notify_by_email'] = isset($data['notify_by_email']) ? 1 : 1;
		$data['notify_by_email_client'] = isset($data['notify_by_email_client']) ? '2' : '0';
		$data['notify_by_sms_client'] = isset($data['notify_by_sms_client']) ? '2' : '0';
		$data['customer'] = $data['customer'];
		$data['description'] = $data['description'];
		$data['contact'] = $data['contact'];
		$data['date'] = to_sql_date($data['date'], true);
		$data['rel_type'] = (isset($data['rel_type'])) ? $data['rel_type'] : '';
		$data['rel_id'] = (isset($data['rel_id'])) ? $data['rel_id'] : 0;
		$data['staff'] = $data['assigned_to'];
		$data['created_by_staff'] = get_staff_user_id();
		if (isset($data['rel_type']) && $data['rel_type'] == 'service') {
			$data['services'] = implode(',', $data['services']);
		} else {
			$data['services'] = "";
		}
		if (isset($data['repeat_every']) && $data['repeat_every'] != '') {
			$data['recurring'] = 1;
			if ($data['repeat_every'] == 'custom') {
				$data['repeat_every']     = $data['repeat_every_custom'];
				$data['recurring_type']   = $data['repeat_type_custom'];
				$data['custom_recurring'] = 1;
			} else {
				$_temp                    = explode('-', $data['repeat_every']);
				$data['recurring_type']   = $_temp[1];
				$data['repeat_every']     = $_temp[0];
				$data['custom_recurring'] = 0;
			}
		} else {
			$data['recurring']    = 0;
			$data['repeat_every'] = 0;
		}
		if (isset($data['repeat_type_custom']) && isset($data['repeat_every_custom'])) {
			unset($data['repeat_type_custom']);
			unset($data['repeat_every_custom']);
		}

		$items = [];
		$hook = hooks()->apply_filters('before_create_reminder', [
			'data'  => $data,
			'items' => $items,
		]);
		$data  = $hook['data'];
		$items = $hook['items'];

		$this->db->insert(db_prefix() . 'reminders', $data);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			if (isset($custom_fields)) {
				handle_custom_fields_post($insert_id, $custom_fields);
			}
			foreach ($items as $key => $item) {
				if ($itemid = add_new_sales_item_post($item, $insert_id, 'job')) {
					_maybe_insert_post_item_tax($itemid, $item, $insert_id, 'job');
				}
			}
			$this->log_reminder_activity($insert_id, 'reminder_activity_created', false, serialize([$data['description']]));
			log_activity('New reminder Created [ID: ' . $insert_id . ']');
			hooks()->do_action('reminder_created', $insert_id);
			return $insert_id;
		}
		return false;
	}
	public function log_reminder_activity($id, $description = '', $client = false, $additional_data = '')
	{
		$staffid   = get_staff_user_id();
		$full_name = get_staff_full_name(get_staff_user_id());
		if (DEFINED('CRON')) {
			$staffid   = '[CRON]';
			$full_name = '[CRON]';
		} elseif ($client == true) {
			$staffid   = null;
			$full_name = '';
		}
		$this->db->insert(db_prefix() . 'reminder_activity', [
			'description'     => $description,
			'date'            => date('Y-m-d H:i:s'),
			'reminder_id'          => $id,
			'staffid'         => $staffid,
			'full_name'       => $full_name,
			'additional_data' => $additional_data,
		]);
	}
	public function update($data, $id)
	{
		$affectedRows = 0;
		$save_and_send = isset($data['save_and_send']);
		$data['notify_by_email'] = isset($data['notify_by_email']);
		$data['date'] = to_sql_date($data['date'], true);
		$data['staff'] = get_staff_user_id();
		$original_task = $this->get($id);
		// Recurring task set to NO, Cancelled
		if (isset($data['rel_type']) && $data['rel_type'] == 'service') {
			$data['services'] = implode(',', $data['services']);
		} else {
			$data['services'] = "";
		}
		if (isset($data['repeat_every'])) {
			if ($original_task->repeat_every != '' && $data['repeat_every'] == '') {
				$data['cycles']              = 0;
				$data['total_cycles']        = 0;
				$data['last_recurring_date'] = null;
			}
			if ($data['repeat_every'] != '') {
				$data['recurring'] = 1;
				if ($data['repeat_every'] == 'custom') {
					$data['repeat_every']     = $data['repeat_every_custom'];
					$data['recurring_type']   = $data['repeat_type_custom'];
					$data['custom_recurring'] = 1;
				} else {
					$_temp                    = explode('-', $data['repeat_every']);
					$data['recurring_type']   = $_temp[1];
					$data['repeat_every']     = $_temp[0];
					$data['custom_recurring'] = 0;
				}
			} else {
				$data['recurring'] = 0;
			}
			if (isset($data['repeat_type_custom']) && isset($data['repeat_every_custom'])) {
				unset($data['repeat_type_custom']);
				unset($data['repeat_every_custom']);
			}
		}
		$items = [];
		$hook = hooks()->apply_filters('before_reminder_updated', [
			'data'          => $data,
			'items'         => $items,
		], $id);
		$data          = $hook['data'];

		$this->db->where('id', $id);
		$query = $this->db->update(db_prefix() . 'reminders', $data);
		$this->log_reminder_activity($id, 'reminder_activity_updated', false, serialize([$data['description']]));
		if ($query) {
			hooks()->do_action('after_reminder_updated', $id);
			return true;
		}
		return false;
	}

	public function update_reminder_service_value($data, $id)
	{
		$this->db->where('rem_id', $id)->delete(db_prefix() . 'reminder_service_value');
		foreach ($data as $res) {
			$inser_data = array(
				'rem_id' => $id,
				'service_id' => $res
			);
			$this->db->insert(db_prefix() . 'reminder_service_value', $inser_data);
		}
		return true; 
	}
	public function getReminderData($id)
	{
		$this->db->select(db_prefix() . 'reminders.id,'.db_prefix() . 'reminders.services,'.db_prefix() . 'reminders.total_amount,' . db_prefix() . 'reminders.description,' . db_prefix() . 'reminders.date,' . db_prefix() . 'reminders.rel_type,' . db_prefix() . 'reminders.rel_id,' . db_prefix() . 'reminders.contact,' . db_prefix() . 'reminders.customer,' . db_prefix() . 'contacts.email,' . db_prefix() . 'contacts.phonenumber,' . db_prefix() . 'reminders.custom_recurring,' . db_prefix() . 'reminders.recurring,' . db_prefix() . 'reminders.cycles,' . db_prefix() . 'reminders.total_cycles,' . db_prefix() . 'reminders.assigned_to,' . db_prefix() . 'reminders.notify_by_email,' . db_prefix() . 'reminders.repeat_every,' . db_prefix() . 'reminders.recurring_type,' . db_prefix() . 'reminders.is_complete,' . db_prefix() . 'clients.company,' . db_prefix() . 'reminders.customer,' . db_prefix() . 'reminders.notify_by_email_client,' . db_prefix() . 'reminders.notify_by_sms_client,' . db_prefix() . 'reminders.isnotified,' .  db_prefix() . 'reminders.total_amount')->from(db_prefix() . 'reminders');
		$this->db->join(db_prefix() . 'contacts', db_prefix() . 'reminders.contact=' . db_prefix() . 'contacts.id', 'left');
		$this->db->join(db_prefix() . 'clients', db_prefix() . 'reminders.customer=' . db_prefix() . 'clients.userid', 'left');
		$this->db->where([db_prefix() . 'reminders.id' => $id]);
		return $this->db->get()->row();
	}
	/**
	 * Get reminder
	 * @param  mixed $id reminder id OPTIONAL
	 * @return mixed
	 */
	public function get($id = '', $where = [], $for_editor = false)
	{
		$this->db->where($where);
		if (is_client_logged_in()) {
			$this->db->where('status !=', 0);
		}
		if (is_numeric($id) || isset($where['estimate_id'])) {
			$this->db->where(db_prefix() . 'reminders.id', $id);
			$job = $this->db->get(db_prefix() . 'reminders')->row();
			return $job;
		}
		return $this->db->get()->result_array();
	}
	public function get_reminder_activity($id)
	{
		$this->db->where('reminder_id', $id);
		$this->db->order_by('date', 'desc');
		return $this->db->get(db_prefix() . 'reminder_activity')->result_array();
	}

	public function delete($id)
	{
		$r_type = $this->db->where('id', $id)->get(db_prefix() . 'reminders')->row('rel_type');
		if ($r_type == 'custom_reminder') {
			$this->delete_attachment($id);
		}
		$this->db->where('id', $id);
		$this->db->delete(db_prefix() . 'reminders');
		if ($this->db->affected_rows() > 0) {
			$this->log_reminder_activity($id, 'reminder_activity_deleted');
			log_activity('reminder  Data Deleted [ID: ' . $id . ']');
			hooks()->do_action('reminder_deleted', $id);
			return true;
		}
		return false;
	}
	public function complete_reminder($id)
	{
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'reminders', ['is_complete' => '0']);
		if ($this->db->affected_rows() > 0) {
			$this->log_reminder_activity($id, 'reminder_completed');
			log_activity('reminder  Data Completed [ID: ' . $id . ']');
			hooks()->do_action('reminder_completed', $id);
			return 1;
		} else {
			return 2;
		}
		return 0;
	}
	public function reopen_reminder($id)
	{
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'reminders', ['is_complete' => '1']);
		if ($this->db->affected_rows() > 0) {
			$this->log_reminder_activity($id, 'reminder_activity_reopened');
			log_activity('reminder  Data Reopen [ID: ' . $id . ']');
			hooks()->do_action('reminder_completed', $id);
			return 1;
		} else {
			return 2;
		}
		return 0;
	}
	public function add_comment($data, $client = false)
	{
		$this->log_reminder_activity($data['reminder_id'], 'reminder_comment_added', false, serialize([$data['content']]));
		return true;
	}


	public function delete_attachment($rel_id)
	{
		$attachments = $this->getAttachments($rel_id);
		$path = REMINDERS_OTHER_ATTACHMENTS_FOLDER . $rel_id . '/';
		if ($attachments && file_exists($path)) {
			foreach ($attachments as $attachment) {
				unlink($path . $attachment['file_name']);
			}
			rmdir($path);
		}
		$this->db->where('rel_id', $rel_id);
		$this->db->where('rel_type', 'reminder');
		$this->db->delete(db_prefix() . 'files');
		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	public function getAttachments($rel_id, $field = array())
	{
		if (count($field) > 0) {
			$this->db->select('' . implode(',', $field) . '');
		}
		$res =  $this->db->where('rel_id', $rel_id)->where('rel_type', 'reminder')->get(db_prefix() . 'files')->result_array();
		return $res;
	}
	public function get_email_template($where = [], $result_type = 'result_array')
	{
		$this->db->where($where);

		return $this->db->get(db_prefix() . 'emailtemplates')->{$result_type}();
	}
	public function get_created_by_ids()
	{
		return $this->db->query('SELECT DISTINCT(created_by_staff) as by_staff, CONCAT(' . db_prefix() . 'staff.firstname,' . db_prefix() . 'staff.lastname) as full_name FROM ' . db_prefix() . 'reminders JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid=' . db_prefix() . 'reminders.created_by_staff')->result_array();
	}
	public function get_all_services($id = '')
	{
		if ($id) {
			$result = $this->db->where('id', $id)->get(db_prefix() . 'reminders')->row_array();
			return $result['services'];
		} else {
			return $this->db->get(db_prefix() . 'reminder_services')->result_array();
		}
	}
	  public function get_notes($rel_id, $rel_type)
    {
        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        $this->db->order_by('dateadded', 'desc');
        $notes = $this->db->get(db_prefix() . 'notes')->result_array();
        return hooks()->apply_filters('get_notes', $notes, ['rel_id' => $rel_id, 'rel_type' => $rel_type]);
    }
}
