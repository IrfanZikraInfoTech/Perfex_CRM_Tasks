<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Class Hr profile
 */
class Hr_profile extends AdminController {
	/**
	 * __construct
	 */
	public function __construct() {
		parent::__construct();
		$this->load->model('hr_profile_model');
		$this->load->model('departments_model');
		$this->load->model('staff_model');
	}

	/* List all announcements */
	public function dashboard() {
		if (!has_permission('hrm_dashboard', '', 'view')) {
			access_denied('hr_profile');
		}
		$this->app_scripts->add('circle-progress-js', 'assets/plugins/jquery-circle-progress/circle-progress.min.js');
		$data['google_ids_calendars'] = $this->misc_model->get_google_calendar_ids();
		$data['title'] = 'HR Profile';
		$this->load->view('hr_profile_dashboard', $data);
	}

	/**
	 * Organizational chart
	 * @return view
	 */
	public function organizational_chart() {
		if (!has_permission('staffmanage_orgchart', '', 'view') && !has_permission('staffmanage_orgchart', '', 'view_own')) {
			access_denied('hr_profile');
		}
		$this->load->model('staff_model');

		$data['list_department'] = $this->departments_model->get();

		//load deparment by manager
		if (!is_admin() && !has_permission('staffmanage_orgchart', '', 'view')) {
			//View own
			$data['deparment_chart'] = json_encode($this->hr_profile_model->get_data_departmentchart_v2());
		} else {
			//admin or view global
			$data['deparment_chart'] = json_encode($this->hr_profile_model->get_data_departmentchart());
		}

		$data['staff_members_chart'] = json_encode($this->hr_profile_model->get_data_chart());
		$data['list_staff'] = $this->staff_model->get();
		$data['email_exist_as_staff'] = $this->email_exist_as_staff();
		$data['title'] = _l('hr_organizational_chart');
		$data['dep_tree'] = json_encode($this->hr_profile_model->get_department_tree());
		$this->load->view('organizational/organizational_chart', $data);
	}

	/**
	 * email exist as staff
	 * @return integer
	 */
	private function email_exist_as_staff() {
		return total_rows(db_prefix() . 'departments', 'email IN (SELECT email FROM ' . db_prefix() . 'staff)') > 0;
	}

	/**
	 * get data department
	 * @return json
	 */
	public function get_data_department() {
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data(module_views_path('hr_profile', 'organizational/include/department_table'));
		}
	}

	/**
	 * Delete department from database
	 * @param  integer $id
	 */
	public function delete($id) {
		if (!$id) {
			redirect(admin_url('hr_profile/organizational_chart'));
		}
		$response = $this->departments_model->delete($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('hr_is_referenced', _l('department_lowercase')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('hr_department')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('department_lowercase')));
		}
		redirect(admin_url('hr_profile/organizational_chart'));
	}

	/* Edit or add new department */
	public function department($id = '') {
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
			$data = $this->input->post();
			$data['password'] = $this->input->post('password', false);

			if (isset($data['fakeusernameremembered']) || isset($data['fakepasswordremembered'])) {
				unset($data['fakeusernameremembered']);
				unset($data['fakepasswordremembered']);
			}
			if (!$this->input->post('id')) {
				$id = $this->departments_model->add($data);
				if ($id) {
					$success = true;
					$message = _l('added_successfully', _l('department'));
				}
				echo json_encode([
					'success' => $success,
					'message' => $message,
					'email_exist_as_staff' => $this->email_exist_as_staff(),
				]);
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->departments_model->update($data, $id);
				if ($success) {
					$message = _l('updated_successfully', _l('department'));
				}
				echo json_encode([
					'success' => $success,
					'message' => $message,
					'email_exist_as_staff' => $this->email_exist_as_staff(),
				]);
			}
			die;
		}
	}

	/**
	 * email exists
	 * @return [type]
	 */
	public function email_exists() {
		// First we need to check if the email is the same
		$departmentid = $this->input->post('departmentid');
		if ($departmentid) {
			$this->db->where('departmentid', $departmentid);
			$_current_email = $this->db->get(db_prefix() . 'departments')->row();
			if ($_current_email->email == $this->input->post('email')) {
				echo json_encode(true);
				die();
			}
		}
		$exists = total_rows(db_prefix() . 'departments', [
			'email' => $this->input->post('email'),
		]);
		if ($exists > 0) {
			echo 'false';
		} else {
			echo 'true';
		}
	}

	/**
	 * test imap connection
	 * @return [type]
	 */
	public function test_imap_connection() {
		app_check_imap_open_function();

		$email = $this->input->post('email');
		$password = $this->input->post('password', false);
		$host = $this->input->post('host');
		$imap_username = $this->input->post('username');
		if ($this->input->post('encryption')) {
			$encryption = $this->input->post('encryption');
		} else {
			$encryption = '';
		}

		require_once APPPATH . 'third_party/php-imap/Imap.php';

		$mailbox = $host;

		if ($imap_username != '') {
			$username = $imap_username;
		} else {
			$username = $email;
		}

		$password = $password;
		$encryption = $encryption;
		// open connection
		$imap = new Imap($mailbox, $username, $password, $encryption);
		if ($imap->isConnected() === true) {
			echo json_encode([
				'alert_type' => 'success',
				'message' => _l('lead_email_connection_ok'),
			]);
		} else {
			echo json_encode([
				'alert_type' => 'warning',
				'message' => $imap->getError(),
			]);
		}
	}

	/**
	 * reception_staff
	 * @return view
	 */
	public function reception_staff() {
		$this->app_scripts->add('circle-progress-js', 'assets/plugins/jquery-circle-progress/circle-progress.min.js');

		if (!is_admin() && !has_permission('hrm_reception_staff', '', 'view') && !has_permission('hrm_reception_staff', '', 'view_own')) {
			access_denied('reception_staff');
		}
		$this->load->model('hr_profile/hr_profile_model');
		$this->load->model('roles_model');
		$this->load->model('staff_model');
		$data['staff_members'] = $this->hr_profile_model->get_staff('', ['active' => 1]);
		$data['title'] = _l('staff_infor');
		$data['list_staff_not_record'] = $this->hr_profile_model->get_all_staff_not_in_record();
		$data['list_reception_staff_transfer'] = $this->hr_profile_model->get_setting_transfer_records();
		$data['staff_dep_tree'] = json_encode($this->hr_profile_model->get_staff_tree());
		$data['staff_members_chart'] = json_encode($this->hr_profile_model->get_data_chart());
		$data['list_training'] = $this->hr_profile_model->get_all_jp_interview_training();
		$data['list_reception_staff_asset'] = $this->hr_profile_model->get_setting_asset_allocation();
		$data['list_record_meta'] = $this->hr_profile_model->get_list_record_meta();
		$data['group_checklist'] = $this->hr_profile_model->group_checklist();
		$data['setting_training'] = $this->hr_profile_model->get_setting_training();
		$data['type_of_trainings'] = $this->hr_profile_model->get_type_of_training();

		$data['title'] = _l('hr_reception_staff');
		$this->load->view('reception_staff/reception_staff', $data);
	}

	/**
	 * table reception staff
	 */
	public function table_reception_staff() {
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data(module_views_path('hr_profile', 'reception_staff/reception_staff_table'));
		}
	}

	/**
	 * setting
	 * @return view
	 */
	public function setting() {

		if (!has_permission('hrm_setting', '', 'view')) {
			access_denied('hr_profile');
		}

		$this->load->model('staff_model');
		$data['group'] = $this->input->get('group');
		$data['title'] = _l('setting');
		$data['tab'][] = 'contract_type';
		$data['tab'][] = 'salary_type';
		$data['tab'][] = 'allowance_type';
		$data['tab'][] = 'procedure_retire';
		$data['tab'][] = 'type_of_training';
		$data['tab'][] = 'reception_staff';
		$data['tab'][] = 'workplace';
		$data['tab'][] = 'contract_template';
		if (is_admin()) {
			$data['tab'][] = 'hr_profile_permissions';
		}
		$data['tab'][] = 'prefix_number';
		//reset data
		if (is_admin()) {
			$data['tab'][] = 'reset_data';
		}

		if ($data['group'] == '') {
			$data['group'] = 'contract_type';
			$data['contract'] = $this->hr_profile_model->get_contracttype();
		} elseif ($data['group'] == 'contract_type') {
			$data['contract'] = $this->hr_profile_model->get_contracttype();

		} elseif ($data['group'] == 'salary_type') {
			$data['salary_form'] = $this->hr_profile_model->get_salary_form();

		} elseif ($data['group'] == 'allowance_type') {
			$data['allowance_type'] = $this->hr_profile_model->get_allowance_type();

		} elseif ($data['group'] == 'procedure_retire') {
			$data['allowance_type'] = $this->hr_profile_model->get_allowance_type();

		} elseif ($data['group'] == 'type_of_training') {
			$data['type_of_trainings'] = $this->hr_profile_model->get_type_of_training();

		} elseif ($data['group'] == 'reception_staff') {
			$data['type_of_trainings'] = $this->hr_profile_model->get_type_of_training();
			$data['list_reception_staff_transfer'] = $this->hr_profile_model->get_setting_transfer_records();
			$data['list_reception_staff_asset'] = $this->hr_profile_model->get_setting_asset_allocation();
			$data['setting_training'] = $this->hr_profile_model->get_setting_training();

			$data['group_checklist'] = $this->hr_profile_model->group_checklist();
			$data['max_checklist'] = $this->hr_profile_model->count_max_checklist();

		} elseif ($data['group'] == 'workplace') {
			$data['workplace'] = $this->hr_profile_model->get_workplace();
		} elseif ($data['group'] == 'contract_template') {
			$data['contract_templates'] = $this->hr_profile_model->get_contract_template();
		}

		$data['job_position'] = $this->hr_profile_model->get_job_position();
		$data['contract_type'] = $this->hr_profile_model->get_contracttype();
		$data['positions'] = $this->hr_profile_model->get_job_position();

		$data['staff'] = $this->staff_model->get();
		$data['department'] = $this->departments_model->get();
		$data['procedure_retire'] = $this->hr_profile_model->get_procedure_retire();
		$data['str_allowance_type'] = $this->hr_profile_model->get_allowance_type_tax();

		$this->load->model('currencies_model');
		$data['base_currency'] = $this->currencies_model->get_base_currency();
		$data['title'] = _l('hr_settings');
		$data['tabs']['view'] = 'includes/' . $data['group'];
		$this->load->view('manage_setting', $data);
	}

	/**
	 * contract_type
	 * @param  integer $id
	 */
	public function contract_type($id = '') {

		if ($this->input->post()) {
			$message = '';

			$data = $this->input->post();
			$data['description'] = $this->input->post('description', false);

			if (!$this->input->post('id')) {

				$id = $this->hr_profile_model->add_contract_type($data);
				if ($id) {
					$success = true;
					$message = _l('added_successfully', _l('contract_type'));
					set_alert('success', $message);
				}

				redirect(admin_url('hr_profile/setting?group=contract_type'));
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->hr_profile_model->update_contract_type($data, $id);
				if ($success) {
					$message = _l('updated_successfully', _l('contract_type'));
					set_alert('success', $message);
				} else {
					$message = _l('hr_updated_failed', _l('contract_type'));
					set_alert('warning', $message);
				}

				redirect(admin_url('hr_profile/setting?group=contract_type'));
			}
			die;
		}
	}
	/**
	 * delete contract type
	 * @param  integer $id
	 */
	public function delete_contract_type($id) {
		if (!$id) {
			redirect(admin_url('hr_profile/setting?group=contract_type'));
		}
		$response = $this->hr_profile_model->delete_contract_type($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('hr_is_referenced', _l('contract_type')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('contract_type')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('contract_type')));
		}
		redirect(admin_url('hr_profile/setting?group=contract_type'));
	}
	/**
	 * allowancetype
	 * @param  integer $id
	 */
	public function allowance_type($id = '') {
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
			if (!$this->input->post('id')) {
				$id = $this->hr_profile_model->add_allowance_type($data);
				if ($id) {
					$success = true;
					$message = _l('added_successfully', _l('hr_allowance_type'));
				}
				echo json_encode([
					'success' => $success,
					'message' => $message,
				]);
				redirect(admin_url('hr_profile/setting?group=allowance_type'));
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->hr_profile_model->update_allowance_type($data, $id);
				if ($success) {
					$message = _l('updated_successfully', _l('hr_allowance_type'));
					set_alert('success', $message);
				} else {
					$message = _l('hr_updated_failed', _l('hr_allowance_type'));
					set_alert('warning', $message);
				}
				redirect(admin_url('hr_profile/setting?group=allowance_type'));
			}
			die;
		}
	}
	/**
	 * delete_allowance_type
	 * @param  integer $id
	 */
	public function delete_allowance_type($id) {
		if (!$id) {
			redirect(admin_url('hr_profile/setting?group=allowance_type'));
		}
		$response = $this->hr_profile_model->delete_allowance_type($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('hr_is_referenced', _l('hr_allowance_type')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('hr_allowance_type')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('hr_allowance_type')));
		}
		redirect(admin_url('hr_profile/setting?group=allowance_type'));
	}
	/**
	 * insurance type
	 */
	public function insurance_type() {
		if ($this->input->post()) {
			$data = $this->input->post();
			if (!$this->input->post('id')) {
				$add = $this->hr_profile_model->add_insurance_type($data);
				if ($add) {
					$message = _l('added_successfully', _l('insurance_type'));
					set_alert('success', $message);
				}
				redirect(admin_url('hr_profile/setting?group=insurrance'));
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->hr_profile_model->update_insurance_type($data, $id);
				if ($success == true) {
					$message = _l('updated_successfully', _l('insurance_type'));
					set_alert('success', $message);
				}
				redirect(admin_url('hr_profile/setting?group=insurrance'));
			}

		}
	}
	/**
	 * delete insurance type
	 * @param  integer $id
	 */
	public function delete_insurance_type($id) {
		if (!$id) {
			redirect(admin_url('hr_profile/setting?group=insurrance'));
		}
		$response = $this->hr_profile_model->delete_insurance_type($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('hr_is_referenced', _l('insurance_type')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('insurance_type')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('insurance_type')));
		}
		redirect(admin_url('hr_profile/setting?group=insurrance'));
	}
	/**
	 * insurance conditions setting
	 */
	public function insurance_conditions_setting() {
		if ($this->input->post()) {
			$data = $this->input->post();
			$success = $this->hr_profile_model->update_insurance_conditions($data);
			if ($success > 0) {
				set_alert('success', _l('setting_updated_successfullyfully'));
			}
			redirect(admin_url('hr_profile/setting?group=insurrance'));
		}
	}
	/**
	 * salary form
	 * @param  integer $id
	 */
	public function salary_form($id = '') {
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();

			if (!$this->input->post('id')) {
				$id = $this->hr_profile_model->add_salary_form($data);
				if ($id) {
					$success = true;
					$message = _l('added_successfully', _l('hr_salary_form'));
				}
				echo json_encode([
					'success' => $success,
					'message' => $message,
				]);
				redirect(admin_url('hr_profile/setting?group=salary_type'));
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->hr_profile_model->update_salary_form($data, $id);
				if ($success) {
					$message = _l('updated_successfully', _l('hr_salary_form'));
					set_alert('success', $message);
				} else {
					$message = _l('hr_updated_failed', _l('hr_allowance_type'));
					set_alert('warning', $message);
				}
				redirect(admin_url('hr_profile/setting?group=salary_type'));
			}
			die;
		}
	}

	/**
	 * delete salary form
	 * @param  integer $id
	 */
	public function delete_salary_form($id) {
		if (!$id) {
			redirect(admin_url('hr_profile/setting?group=salary_type'));
		}

		if (!has_permission('hrm_setting', '', 'delete') && !is_admin()) {
			access_denied('hr_profile');
		}

		$response = $this->hr_profile_model->delete_salary_form($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('hr_is_referenced', _l('hr_salary_form')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('hr_salary_form')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('hr_salary_form')));
		}
		redirect(admin_url('hr_profile/setting?group=salary_type'));
	}

	/**
	 * table procedure retire
	 */
	public function table_procedure_retire() {
		$this->app->get_table_data(module_views_path('hr_profile', 'procedure_retire/table_procedure_retire'));
	}

	/**
	 * add procedure form manage
	 */
	public function add_procedure_form_manage() {
		$data = $this->input->post();

		if ($data['id'] == '') {
			$response = $this->hr_profile_model->add_procedure_form_manage($data);

			if ($response) {
				set_alert('success', _l('added_successfully'));
				redirect(admin_url('hr_profile/procedure_procedure_retire_details/' . $response));
			} else {
				set_alert('warning', _l('hr_added_failed'));
			}

		} else {
			$id = $data['id'];
			unset($data['id']);

			$response = $this->hr_profile_model->update_procedure_form_manage($data, $id);
			if ($response) {
				set_alert('success', _l('hr_updated_successfully'));
			} else {
				set_alert('warning', _l('hr_update_failed'));
			}
		}

		redirect(admin_url('hr_profile/setting?group=procedure_retire'));
	}

	/**
	 * delete procedure form manage
	 * @param  integer $id
	 */
	public function delete_procedure_form_manage($id) {
		if (!has_permission('hrm_setting', '', 'delete') && !is_admin()) {
			access_denied('hr_profile');
		}

		$success = $this->hr_profile_model->delete_procedure_form_manage($id);
		if ($success == true) {
			$message = _l('hr_deleted');
			echo json_encode([
				'success' => true,
				'message' => $message,
			]);
			set_alert('success', $message);

		} else {
			$message = _l('problem_deleting');
			echo json_encode([
				'success' => true,
				'message' => $message,
			]);
			set_alert('warning', $message);

		}
		redirect(admin_url('hr_profile/setting?group=procedure_retire'));
	}

	/**
	 * procedure procedure retire details
	 * @param  integer $id
	 * @return view
	 */
	public function procedure_procedure_retire_details($id = '') {
		if (!$id) {
			blank_page(_l('hr_procedure_retire'), 'danger');
		}

		$data['title'] = _l('hr_procedure_retire');
		$data['id'] = $id;
		$data['procedure_retire'] = $this->hr_profile_model->get_procedure_retire($id);
		$data['staffs'] = $this->staff_model->get();
		$this->load->view('hr_profile/procedure_retire/details', $data);
	}

	/**
	 * procedure form
	 */
	public function procedure_form() {
		$data = $this->input->post();
		$result = $this->hr_profile_model->add_procedure_retire($data);

		if ($result) {
			set_alert('success', _l('hr_added_successfully'));
		} else {
			set_alert('warning', _l('hr_added_failed'));
		}
		redirect(admin_url('hr_profile/procedure_procedure_retire_details/' . $data['procedure_retire_id']));
	}

	/**
	 * delete procedure retire
	 * @param  integer $id
	 * @return integer
	 */
	public function delete_procedure_retire($id_detail, $id) {
		$result = $this->hr_profile_model->delete_procedure_retire($id_detail);

		if ($result) {
			set_alert('success', _l('hr_deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('hr_profile/procedure_procedure_retire_details/' . $id));

	}

	/**
	 * edit procedure retire
	 * @param  integer $id
	 */
	public function edit_procedure_retire($id) {
		$data = $this->hr_profile_model->get_edit_procedure_retire($id);
		$id = $data->id;
		$procedure_retire_id = $data->procedure_retire_id;
		$people_handle_id = $data->people_handle_id;
		$option_name = json_decode($data->option_name);

		$count_option_value = count(get_object_vars(json_decode($data->option_name))) + 1;

		$rel_name = $data->rel_name;

		echo json_encode([
			'id' => $id,
			'option_name' => $option_name,
			'rel_name' => $rel_name,
			'procedure_retire_id' => $procedure_retire_id,
			'people_handle_id' => $people_handle_id,
			'count_option_value' => $count_option_value,
		]);

	}

	/**
	 * edit procedure form
	 */
	public function edit_procedure_form() {
		$data = $this->input->post();
		if (isset($data['id'])) {
			$id = $data['id'];
			unset($data['id']);
		}
		$success = $this->hr_profile_model->edit_procedure_retire($data, $id);
		if ($success) {
			set_alert('success', _l('hr_updated_successfully'));
		} else {
			set_alert('warning', _l('hr_update_false'));
		}
		redirect(admin_url('hr_profile/procedure_procedure_retire_details/' . $data['procedure_retire_id']));

	}

	/**
	 * training
	 * @return view
	 */
	public function training() {
		if (!has_permission('staffmanage_training', '', 'view') && !has_permission('staffmanage_training', '', 'view_own') && !is_admin()) {
			access_denied('job_position');
		}
		$data['group'] = $this->input->get('group');
		$data['title'] = _l('hr_training');
		$data['tab'][] = 'training_program';
		$data['tab'][] = 'training_library';
		$data['tab'][] = 'training_result';

		if ($data['group'] == '') {
			$data['group'] = 'training_program';
		}
		$data['tabs']['view'] = 'training/' . $data['group'];

		$data['training_table'] = $this->hr_profile_model->get_job_position_training_process();
		$data['get_job_position'] = $this->hr_profile_model->get_job_position();
		$data['hr_profile_get_department_name'] = $this->departments_model->get();
		$data['type_of_trainings'] = $this->hr_profile_model->get_type_of_training();
		$data['staffs'] = $this->hr_profile_model->get_staff_active();

		$data['list_staff'] = $this->staff_model->get();
		$data['training_libraries'] = $this->hr_profile_model->get_training_library();
		$data['training_programs'] = $this->hr_profile_model->get_job_position_training_process();

		$this->load->view('training/manage_training', $data);
	}

	/**
	 * Add new position training or update existing
	 * @param integer id
	 */
	public function position_training($id = '') {
		if (!has_permission('staffmanage_training', '', 'view')) {
			access_denied('job_position');
		}
		if ($this->input->post()) {
			$data = $this->input->post();
			$data['description'] = $this->input->post('description', false);
			$data['viewdescription'] = $this->input->post('viewdescription', false);

			if ($id == '') {
				if (!has_permission('staffmanage_training', '', 'create')) {
					access_denied('job_position');
				}
				$id = $this->hr_profile_model->add_position_training($data);
				if ($id) {
					set_alert('success', _l('added_successfully', _l('hr_training')));
					redirect(admin_url('hr_profile/position_training/' . $id));
				}
			} else {
				if (!has_permission('staffmanage_training', '', 'edit')) {
					access_denied('job_position');
				}
				$success = $this->hr_profile_model->update_position_training($data, $id);
				if ($success) {
					set_alert('success', _l('updated_successfully', _l('hr_training')));
				}
				redirect(admin_url('hr_profile/position_training/' . $id));
			}
		}
		if ($id == '') {
			$title = _l('add_new', _l('hr_training'));
		} else {
			$position_training = $this->hr_profile_model->get_position_training($id);
			$data['position_training'] = $position_training;
			$title = $position_training->subject;
		}
		if (is_gdpr() && (get_option('gdpr_enable_consent_for_contacts') == '1' || get_option('gdpr_enable_consent_for_leads') == '1')) {
			$this->load->model('gdpr_model');
			$data['purposes'] = $this->gdpr_model->get_consent_purposes();
		}
		$data['title'] = $title;
		$data['type_of_trainings'] = $this->hr_profile_model->get_type_of_training();
		$this->app_scripts->add('surveys-js', module_dir_url('surveys', 'assets/js/surveys.js'), 'admin', ['app-js']);
		$this->app_css->add('surveys-css', module_dir_url('hr_profile', 'assets/css/training/training_post.css'), 'admin', ['app-css']);

		$this->load->view('hr_profile/training/job_position_manage/position_training', $data);
	}

	/* New survey question */
	public function add_training_question() {
		if (!has_permission('staffmanage_training', '', 'edit') && !has_permission('staffmanage_training', '', 'create')) {
			echo json_encode([
				'success' => false,
				'message' => _l('access_denied'),
			]);
			die();
		}
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				echo json_encode([
					'data' => $this->hr_profile_model->add_training_question($this->input->post()),
					'survey_question_only_for_preview' => _l('hr_survey_question_only_for_preview'),
					'survey_question_required' => _l('hr_survey_question_required'),
					'survey_question_string' => _l('hr_question_string'),
				]);
				die();
			}
		}
	}

	/* Update question */
	public function update_training_question() {
		if (!has_permission('staffmanage_training', '', 'edit') && !has_permission('staffmanage_training', '', 'create')) {
			echo json_encode([
				'success' => false,
				'message' => _l('access_denied'),
			]);
			die();
		}
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$this->hr_profile_model->update_question($this->input->post());
			}
		}
	}

	/* Reorder surveys */
	public function update_training_questions_orders() {
		if (has_permission('staffmanage_training', '', 'edit') || has_permission('staffmanage_training', '', 'create')) {
			if ($this->input->is_ajax_request()) {
				if ($this->input->post()) {
					$this->hr_profile_model->update_survey_questions_orders($this->input->post());
				}
			}
		}
	}
	/* Remove survey question */
	public function remove_question($questionid) {
		if (!has_permission('staffmanage_training', '', 'edit') && !has_permission('staffmanage_training', '', 'create')) {
			echo json_encode([
				'success' => false,
				'message' => _l('access_denied'),
			]);
			die();
		}
		if ($this->input->is_ajax_request()) {
			echo json_encode([
				'success' => $this->hr_profile_model->remove_question($questionid),
			]);
		}
	}

	/* Removes survey checkbox/radio description*/
	public function remove_box_description($questionboxdescriptionid) {
		if (!has_permission('staffmanage_training', '', 'edit') && !has_permission('staffmanage_training', '', 'create')) {
			echo json_encode([
				'success' => false,
				'message' => _l('access_denied'),
			]);
			die();
		}
		if ($this->input->is_ajax_request()) {
			echo json_encode([
				'success' => $this->hr_profile_model->remove_box_description($questionboxdescriptionid),
			]);
		}
	}

	/* Add box description */
	public function add_box_description($questionid, $boxid) {
		if (!has_permission('staffmanage_training', '', 'edit') && !has_permission('staffmanage_training', '', 'create')) {
			echo json_encode([
				'success' => false,
				'message' => _l('access_denied'),
			]);
			die();
		}
		if ($this->input->is_ajax_request()) {
			$boxdescriptionid = $this->hr_profile_model->add_box_description($questionid, $boxid);
			echo json_encode([
				'boxdescriptionid' => $boxdescriptionid,
			]);
		}
	}
	/* Update question */
	public function update_training_question_answer() {
		if (!has_permission('staffmanage_training', '', 'edit') && !has_permission('staffmanage_training', '', 'create')) {
			echo json_encode([
				'success' => false,
				'message' => _l('access_denied'),
			]);
			die();
		}
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {

				$this->hr_profile_model->update_answer_question($this->input->post());
			}
		}
	}
	/**
	 * get training type child
	 * @param  integer $id
	 * @return json
	 */
	public function get_training_type_child($id) {
		$list = $this->hr_profile_model->get_child_training_type($id);
		$html = '';
		foreach ($list as $li) {
			$html .= '<option value="' . $li['training_id'] . '">' . $li['subject'] . '</option>';
		}
		echo json_encode([
			'html' => $html,
		]);
	}

	/**
	 * job position training add edit
	 */
	public function job_position_training_add_edit() {
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();

			if (!$this->input->post('id_training')) {
				$job_position_id = $this->input->post('job_position_id');
				$id = $this->hr_profile_model->add_job_position_training_process($data);
				if ($id) {
					$success = true;
					$message = _l('added_successfully');
					set_alert('success', $message);
				}
				redirect(admin_url('hr_profile/training/?group=training_program'));
			} else {
				$job_position_id = $data['job_position_id'];
				$id = $data['id_training'];
				unset($data['id_training']);
				$success = $this->hr_profile_model->update_job_position_training_process($data, $id);

				if ($success) {
					$message = _l('hr_updated_successfully');
					set_alert('success', $message);
				} else {
					$message = _l('hr_updated_failed');
					set_alert('warning', $message);
				}
				redirect(admin_url('hr_profile/training/?group=training_program'));
			}
			die;
		}
	}

	/**
	 * get jobposition fill data
	 * @return json
	 */
	public function get_jobposition_fill_data() {
		$data = $this->input->post();
		if ($data['status'] == 'true') {
			$job_position = $this->hr_profile_model->get_position_by_department($data['department_id'], true);

		} else {
			$job_position = $this->hr_profile_model->get_position_by_department(1, false);

		}
		echo json_encode([
			'job_position' => $job_position,
		]);
	}

	/**
	 * job position manage
	 * @return view
	 */
	public function job_position_manage() {
		if (!has_permission('staffmanage_job_position', '', 'view') && !is_admin() && !has_permission('staffmanage_job_position', '', 'view_own')) {
			access_denied('job_position');
		}
		$this->load->model('staff_model');

		$data['job_p'] = $this->hr_profile_model->get_job_p();
		$data['get_job_position'] = $this->hr_profile_model->get_job_position();
		$data['hr_profile_get_department_name'] = $this->departments_model->get();
		$this->load->view('hr_profile/job_position_manage/job_manage/job_manage', $data);
	}

	/**
	 * table job
	 */
	public function table_job() {
		$this->app->get_table_data(module_views_path('hr_profile', 'table_job'));
	}

	/**
	 * add job position
	 * @param  integer $id
	 */
	public function job_p($id = '') {

		if ($this->input->post()) {

			$message = '';
			$data = $this->input->post();

			if (!$this->input->post('id')) {
				$data['description'] = $this->input->post('description', false);
				$id = $this->hr_profile_model->add_job_p($data);

				if ($id) {
					$message = _l('added_successfully', _l('job'));
					set_alert('success', $message);
				}
				redirect(admin_url('hr_profile/job_position_manage'));

			} else {
				$id = $data['id'];
				unset($data['id']);
				$data['description'] = $this->input->post('description', false);
				$success = $this->hr_profile_model->update_job_p($data, $id);

				if ($success) {
					set_alert('success', _l('updated_successfully', _l('job')));
				} else {
					set_alert('warning', _l('updated_false', _l('job')));
				}

				redirect(admin_url('hr_profile/job_position_manage'));
			}
			die;
		}
	}

	/**
	 * get job position edit
	 * @param  integer $id
	 * @return json
	 */
	public function get_job_p_edit($id) {

		$list = $this->hr_profile_model->get_job_p($id);

		if (isset($list)) {
			$description = $list->description;
		} else {
			$description = '';
		}

		echo json_encode([
			'description' => $description,
		]);

	}

	/**
	 * delete job position
	 * @param  integer $id
	 */
	public function delete_job_p($id) {
		if (!$id) {
			redirect(admin_url('hr_profile/job_position_manage'));
		}

		$response = $this->hr_profile_model->delete_job_p($id);
		if ($response) {
			set_alert('success', _l('deleted', _l('job')));
			redirect(admin_url('hr_profile/job_position_manage'));
		} else {
			set_alert('warning', _l('problem_deleting', _l('job')));
			redirect(admin_url('hr_profile/job_position_manage'));
		}

	}

	/**
	 * import job p, Import Job
	 * @return [type]
	 */
	public function import_job_p() {
		$data['departments'] = $this->departments_model->get();
		$data['job_positions'] = $this->hr_profile_model->get_job_position();

		$data_staff = $this->hr_profile_model->get_staff(get_staff_user_id());

		/*get language active*/
		if ($data_staff) {
			if ($data_staff->default_language != '') {
				$data['active_language'] = $data_staff->default_language;

			} else {

				$data['active_language'] = get_option('active_language');
			}

		} else {
			$data['active_language'] = get_option('active_language');
		}

		$this->load->view('hr_profile/job_position_manage/job_manage/import_job', $data);
	}

	/**
	 * import job p excel
	 * @return [type]
	 */
	public function import_job_p_excel() {
		if (!is_admin() && !has_permission('staffmanage_job_position', '', 'create')) {
			access_denied('Leads Import');
		}
		$total_row_false = 0;
		$total_rows = 0;
		$dataerror = 0;
		$total_row_success = 0;
		$filename = '';

		if ($this->input->post()) {
			// $simulate = $this->input->post('simulate');
			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
				//do_action('before_import_leads');

				// Get the temp file path
				$tmpFilePath = $_FILES['file_csv']['tmp_name'];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

					if (!file_exists(TEMP_FOLDER)) {
						mkdir(TEMP_FOLDER, 0755);
					}

					if (!file_exists($tmpDir)) {
						mkdir($tmpDir, 0755);
					}

					// Setup our new file path
					$newFilePath = $tmpDir . $_FILES['file_csv']['name'];

					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						$import_result = true;
						$rows = [];

						$objReader = new PHPExcel_Reader_Excel2007();
						$objReader->setReadDataOnly(true);
						$objPHPExcel = $objReader->load($newFilePath);
						$rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();
						$sheet = $objPHPExcel->getActiveSheet();

						//init file error start
						$dataError = new PHPExcel();
						$dataError->setActiveSheetIndex(0);
						//create title
						$dataError->getActiveSheet()->setTitle('error');
						$dataError->getActiveSheet()->getColumnDimension('A')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('B')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('C')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('D')->setWidth(20);
						//Set bold for header
						$dataError->getActiveSheet()->getStyle('A1:AA1')->getFont()->setBold(true);

						$dataError->getActiveSheet()->setCellValue('A1', _l('job_name'));
						$dataError->getActiveSheet()->setCellValue('B1', _l('description'));
						$dataError->getActiveSheet()->setCellValue('C1', _l('hr_create_job_position_default'));
						$dataError->getActiveSheet()->setCellValue('D1', _l('error'));
						//init file error end

						// start Write data error from line 2
						$styleArray = array(
							'font' => array(
								'bold' => true,
								'color' => array('rgb' => 'ff0000'),
							));

						$numRow = 2;
						$total_rows = 0;
						//get data for compare

						foreach ($rowIterator as $row) {
							$rowIndex = $row->getRowIndex();
							if ($rowIndex > 1) {

								$rd = array();
								$flag = 0;

								$string_error = '';

								$value_job_name = $sheet->getCell('A' . $rowIndex)->getValue();
								$value_description = $sheet->getCell('B' . $rowIndex)->getValue();
								$value_create_default = $sheet->getCell('C' . $rowIndex)->getValue();

								if (is_null($value_job_name) == true) {
									$string_error .= _l('job_name') . _l('not_yet_entered');
									$flag = 1;
								}

								if (is_null($value_description) == true) {
									$string_error .= _l('description') . _l('not_yet_entered');
									$flag = 1;
								}

								if (($flag == 1)) {
									$dataError->getActiveSheet()->setCellValue('A' . $numRow, $sheet->getCell('A' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('B' . $numRow, $sheet->getCell('B' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('C' . $numRow, $sheet->getCell('C' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('D' . $numRow, $string_error)->getStyle('D' . $numRow)->applyFromArray($styleArray);

									$numRow++;
								}

								if (($flag == 0)) {

									if (is_numeric($value_create_default) && $value_create_default == '0') {
										$rd['create_job_position'] = 'on';
									}
									$rd['job_name'] = $sheet->getCell('A' . $rowIndex)->getValue();
									$rd['description'] = $sheet->getCell('B' . $rowIndex)->getValue();

								}

								if (get_staff_user_id() != '' && $flag == 0) {
									$rows[] = $rd;
									$this->hr_profile_model->add_job_p($rd);
								}
								$total_rows++;
							}
						}

						$total_rows = $total_rows;
						$data['total_rows_post'] = count($rows);
						$total_row_success = count($rows);
						$total_row_false = $total_rows - (int) count($rows);
						$dataerror = $dataError;
						$message = 'Not enought rows for importing';

						if ($total_row_false != 0) {

							$objWriter = new PHPExcel_Writer_Excel2007($dataError);

							$filename = 'Import_job_error_' . get_staff_user_id() . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
							$objWriter->save(str_replace($filename, HR_PROFILE_ERROR . $filename, $filename));

						}
						$import_result = true;
						@delete_dir($tmpDir);

					}
				} else {
					set_alert('warning', _l('import_upload_failed'));
				}
			}

		}
		echo json_encode([
			'message' => $message,
			'total_row_success' => $total_row_success,
			'total_row_false' => $total_row_false,
			'total_rows' => $total_rows,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id(),
			'filename' => HR_PROFILE_ERROR . $filename,
		]);

	}

	/**
	 * job positions
	 * @param  integer $id
	 * @return view
	 */
	public function job_positions($id = '') {
		if (!has_permission('staffmanage_job_position', '', 'view') && !has_permission('staffmanage_job_position', '', 'view_own')) {
			access_denied('job_position');
		}
		$get_department_by_manager = $this->hr_profile_model->get_department_by_manager();

		$data['job_p_id'] = $this->hr_profile_model->get_job_p();
		$data['hr_profile_get_department_name'] = $this->departments_model->get();
		$data['get_job_position'] = $this->hr_profile_model->get_job_position();
		$data['title'] = _l('hr_job_descriptions');

		$this->load->view('hr_profile/job_position_manage/position_manage/position_manage', $data);
	}

	/**
	 * add or update job position
	 * @param  integer $id
	 */
	public function job_position($id = '') {

		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
			if (!$this->input->post('id')) {
				$data['job_position_description'] = $this->input->post('job_position_description', false);
				$id = $this->hr_profile_model->add_job_position($data);
				if ($id) {
					$_id = $id;
					$uploadedFiles = handle_hr_profile_job_position_attachments_array($id, 'file');

					if ($uploadedFiles && is_array($uploadedFiles)) {
						foreach ($uploadedFiles as $file) {
							$insert_file_id = $this->hr_profile_model->add_attachment_to_database($id, 'job_position', [$file]);
						}
					}
				}

				if ($id) {
					$message = _l('added_successfully', _l('job_position'));
					set_alert('success', $message);
				} else {
					$message = _l('added_failed', _l('job_position'));
					set_alert('warning', $message);
				}

				redirect(admin_url('hr_profile/job_position_view_edit/' . $id));
			} else {
				$job_p_id = $this->input->post('job_p_id');

				$id = $data['id'];
				unset($data['id']);
				$data['job_position_description'] = $this->input->post('job_position_description', false);
				$success = $this->hr_profile_model->update_job_position($data, $id);

				//upload file
				if ($id) {
					$_id = $id;
					$message = _l('added_successfully', _l('job_position'));
					$uploadedFiles = handle_hr_profile_job_position_attachments_array($id, 'file');
					if ($uploadedFiles && is_array($uploadedFiles)) {
						$len = count($uploadedFiles);

						foreach ($uploadedFiles as $file) {
							$insert_file_id = $this->hr_profile_model->add_attachment_to_database($id, 'job_position', [$file]);
							if ($insert_file_id > 0) {
								$count_file++;
							}
						}
						if ($count_file == $len) {
							$response = true;
						}
					}
				}

				if ($success) {
					$message = _l('updated_successfully', _l('job_position'));
					set_alert('success', $message);
				} else {
					$message = _l('updated_failed', _l('job_position'));
					set_alert('warning', $message);
				}
				redirect(admin_url('hr_profile/job_positions'));
			}
			die;
		}
	}

	/**
	 * table job position
	 * @return [type]
	 */
	public function table_job_position() {
		$this->app->get_table_data(module_views_path('hr_profile', 'job_position_manage/position_manage/table_job_position'));
	}

	/**
	 * job position delete tag item
	 * @param  String $tag_id
	 * @return json
	 */
	public function job_position_delete_tag_item($tag_id) {

		$result = $this->hr_profile_model->delete_tag_item($tag_id);
		if ($result == 'true') {
			$message = _l('hr_deleted');
			$status = 'true';
		} else {
			$message = _l('problem_deleting');
			$status = 'fasle';
		}

		echo json_encode([
			'message' => $message,
			'status' => $status,
		]);
	}

	/**
	 * hrm preview jobposition file
	 * @param  [type] $id
	 * @param  [type] $rel_id
	 * @return [type]
	 */
	public function preview_job_position_file($id, $rel_id) {
		$data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
		$data['current_user_is_admin'] = is_admin();
		$data['file'] = $this->hr_profile_model->get_file($id, $rel_id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		$this->load->view('hr_profile/job_position_manage/position_manage/preview_position_file', $data);
	}

	public function delete_hr_profile_job_position_attachment_file($attachment_id) {
		if (!has_permission('staffmanage_job_position', '', 'delete') && !is_admin()) {
			access_denied('job_position');
		}

		$file = $this->misc_model->get_file($attachment_id);
		echo json_encode([
			'success' => $this->hr_profile_model->delete_hr_job_position_attachment_file($attachment_id),
		]);
	}

	/**
	 * job position view edit
	 * @param  string $id
	 * @return view
	 */
	public function job_position_view_edit($id = '', $parent_id = '') {

		if (!has_permission('staffmanage_job_position', '', 'view') && !has_permission('staffmanage_job_position', '', 'view_own')) {
			access_denied('job_position');
		}
		if ($id == '') {
			$title = _l('add_new', _l('hr_training'));
		} else {

			$data['job_position_general'] = $this->hr_profile_model->get_job_position($id);
			$data['job_position_tag'] = $this->hr_profile_model->get_job_position_tag($id);
			$data['job_position_id'] = $id;

			$data_salary_scale = $this->hr_profile_model->get_job_position_salary_scale($id);
			$data['salary_insurance'] = $data_salary_scale['insurance'];
			$data['salary_form_edit'] = $data_salary_scale['salary'];
			$data['salary_allowance'] = $data_salary_scale['allowance'];

			$data['count_salary_form'] = count($data_salary_scale['salary']);
			$data['count_salary_allowance'] = count($data_salary_scale['allowance']);

			$data['job_position_attachment'] = $this->hr_profile_model->get_hr_profile_attachments_file($id, 'job_position');

		}

		$data['list_job_p'] = $this->hr_profile_model->get_job_p();
		$data['list_staff'] = $this->staff_model->get();

		$data['allowance_type'] = $this->hr_profile_model->get_allowance_type();
		$data['salary_form'] = $this->hr_profile_model->get_salary_form();
		$data['parent_id'] = $parent_id;
		$data['hr_profile_get_department_name'] = $this->departments_model->get();

		$this->load->view('hr_profile/job_position_manage/view_edit_jobposition', $data);
	}

	/**
	 * get list job position tags file
	 * @param  [type] $id
	 * @return [type]
	 */
	public function get_list_job_position_tags_file($id) {
		$list = $this->hr_profile_model->get_list_job_position_tags_file($id);

		$job_position_de = $this->hr_profile_model->get_job_position($id);
		if (isset($job_position_de)) {
			$description = $job_position_de->job_position_description;

			$job_p = $this->hr_profile_model->get_job_p($job_position_de->job_p_id);
			$job_p = isset($job_p) ? $job_p->job_id : 0;
		} else {
			$description = '';
			$job_p = 0;

		}

		if((get_tags_in($id,'job_position') != null)){
			$item_value = implode(',', get_tags_in($id,'job_position')) ;
		}else{

			$item_value = '';
		}

		echo json_encode([
			'description' => $description,
			'htmltag' => $list['htmltag'],
			'htmlfile' => $list['htmlfile'],
			'job_position_html' => render_tags(get_tags_in($id, 'job_position')),
			'job_p' => $job_p,
    		'item_value' => $item_value,
			
		]);
	}

	/**
	 * get position by department
	 * @return json
	 */
	public function get_position_by_department() {
		$data = $this->input->post();
		if ($data['status'] == 'true') {
			$job_position = $this->hr_profile_model->get_position_by_department($data['department_id'], true);
		} else {
			$job_position = $this->hr_profile_model->get_position_by_department(1, false);
		}
		echo json_encode([
			'job_position' => $job_position,
		]);

	}

	/**
	 * delete job position
	 * @param  integer $id
	 * @param  integer $job_p_id
	 */
	public function delete_job_position($id) {
		if (!$id) {
			redirect(admin_url('hr_profile/job_positions'));
		}
		$response = $this->hr_profile_model->delete_job_position($id);

		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('hr_is_referenced', _l('hr_hr_job_position')));
		} elseif ($response == true) {
			set_alert('success', _l('hr_deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('hr_profile/job_positions'));
	}

	/**
	 * get staff salary form
	 * @return json
	 */
	public function get_staff_salary_form() {
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$id = $this->input->post('id');
				$name_object = $this->hr_profile_model->get_salary_form($id);
			}
		}
		if ($name_object) {
			echo json_encode([
				'salary_val' => (String) hr_profile_reformat_currency($name_object->salary_val),
			]);
		}

	}

	/**
	 * get staff allowance type
	 * @return json
	 */
	public function get_staff_allowance_type() {
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$id = $this->input->post('id');
				$name_object = $this->hr_profile_model->get_allowance_type($id);
			}
		}

		if ($name_object) {
			echo json_encode([
				'allowance_val' => (String) hr_profile_reformat_currency($name_object->allowance_val),
			]);
		}
	}

	/**
	 * job position salary add edit
	 */
	public function job_position_salary_add_edit() {
		if (!has_permission('staffmanage_job_position', '', 'create')) {
			access_denied('job_position');
		}

		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();
			if ($this->input->post()) {
				$job_position_id = $data['job_position_id'];
				$id = $this->hr_profile_model->job_position_add_update_salary_scale($data);
				if ($id) {
					$success = true;
					$message = _l('added_successfully');
					set_alert('success', $message);
				}
				redirect(admin_url('hr_profile/job_position_view_edit/' . $job_position_id . '?tab=salary_scale'));
			}
			die;
		}
	}

	/**
	 * save setting reception staff
	 */
	public function save_setting_reception_staff() {
		if ($this->input->post()) {
			$data = $this->input->post();
			$data_asset['name'] = $data['asset_name'];
			$data_training['training_type'] = $data['training_type'];
			$this->hr_profile_model->add_manage_info_reception($data);
			$this->hr_profile_model->add_setting_training($data_training);
			$this->hr_profile_model->add_setting_transfer_records($data_transfer);
			$this->hr_profile_model->add_setting_asset_allocation($data_asset);
			$message = _l('hr_updated_successfully');
			set_alert('success', $message);
			redirect(admin_url('hr_profile/setting?group=reception_staff'));
		}
	}

	/**
	 * add new reception
	 */
	public function add_new_reception() {
		if ($this->input->post()) {

			$data = $this->input->post();
			$list_staff = $this->hr_profile_model->get_staff_info_id($data['staff_id']);
			$data_rec_tranfer['staffid'] = $list_staff->staffid;
			$data_rec_tranfer['firstname'] = isset($list_staff->firstname) ? $list_staff->firstname : '';
			$data_rec_tranfer['birthday'] = isset($list_staff->birthday) ? $list_staff->birthday : '';
			$data_rec_tranfer['staffidentifi'] = isset($list_staff->staffidentifi) ? $list_staff->staffidentifi : '';

			// Create records for management reception
			$this->hr_profile_model->add_rec_transfer_records($data_rec_tranfer);

			//1 Reception information board
			$this->hr_profile_model->add_manage_info_reception_for_staff($list_staff->staffid, $data);

			//2 Create a property allocation record
			if (isset($data['asset_name'])) {
				$list_asset = [];
				foreach ($data['asset_name'] as $key => $value) {
					array_push($list_asset, ['name' => $value]);
				}
				if ($list_asset) {
					$this->hr_profile_model->add_asset_staff($list_staff->staffid, $list_asset);
				}
			}

			//3 Create a training record

			if ($list_staff->job_position != '') {

				$jp_interview_training = $this->hr_profile_model->get_job_position_training_de($data['training_program']);
				//TO DO
				// $list_training = $this->hr_profile_model->get_jp_interview_training($list_staff->job_position,$data['training_type']);
				if ($jp_interview_training) {
					$this->hr_profile_model->add_training_staff($jp_interview_training, $list_staff->staffid);
					if (isset($list_staff->email)) {
						if ($list_staff->email != '') {
							$this->send_training_staff($list_staff->email, $list_staff->job_position, $data['training_type'], $jp_interview_training->position_training_id, $list_staff->staffid);
						}
					}
				}
			}

			//4 Create a record with additional profile information
			if (isset($data['info_name'])) {
				if ($data['info_name']) {
					$this->hr_profile_model->add_transfer_records_reception($data['info_name'], $data['staff_id']);
				}
			}

			$message = _l('added_successfully');
			set_alert('success', $message);
			redirect(admin_url('hr_profile/reception_staff'));
		}

	}

	/**
	 * send training staff
	 * @param  [type] $email
	 * @param  [type] $position_id
	 * @param  string $training_type
	 * @return [type]
	 */
	public function send_training_staff($email, $position_id, $training_type = '', $position_training_id = '', $staffid = '') {
		if ($position_training_id != '') {
			$data_training = $this->hr_profile_model->get_list_position_training_by_id_training($position_training_id);

			$data['description'] = '
			<div >
			<div> ' . _l('hr_please_complete_the_tests_below_to_complete_the_training_program') . '</div><br>
			<div> ' . _l('hr_please_log_in_training') . '</div>
			<div></div>';
			foreach ($data_training as $key => $value) {
				$data['description'] .= '<div>';
				$data['description'] .= '&#9755; <a href="' . site_url() . 'hr_profile/participate/index/' . $value['training_id'] . '/' . $value['hash'] . '">' . site_url() . '' . $value['slug'] . '</a>';
				$data['description'] .= '</div><br>';
			}

			$data['description'] .= '</div>';

			//send notification
			$notified = add_notification([
				'description' => $data['description'],
				'touserid' => $staffid,
				'additional_data' => serialize([
					$data['description'],
				]),
			]);
			if ($notified) {
				pusher_trigger_notification([$staffid]);
			}

			//send mail
			$this->hr_profile_model->send_mail_training($email, get_option('companyname'), '' . get_option('companyname') . ': ' . _l('hr_new_training_for_you'), $data['description']);
		}
	}

	/**
	 * get percent complete
	 * @param  string $id
	 * @return [type]
	 */
	public function get_percent_complete($id = '') {
		if ($id != '') {
			$this->load->model('hr_profile/hr_profile_model');
			$this->load->model('departments_model');
			$this->load->model('staff_model');

			$data['staff'] = $this->staff_model->get($id);
			$data['list_reception_staff_transfer'] = $this->hr_profile_model->get_setting_transfer_records();
			$staff_array = json_decode(json_encode($data['staff']), true);
			$count_info = 0;
			$count_info_total = 0;
			$count_effect_total = 0;
			$count_total = 0;
			//check list
			$checklist_effect = 0;
			$listchecklist = $this->hr_profile_model->get_group_checklist_allocation_by_staff_id($id);
			$count_total = count($listchecklist);

			foreach ($listchecklist as $value) {
				$checklist = $this->hr_profile_model->get_checklist_allocation_by_group_id($value['id']);
				$total = count($checklist);
				$effect_checklist = 0;
				foreach ($checklist as $item) {
					if ((int) $item['status'] == 1) {
						$effect_checklist += 1;
					}
				}
				if ($effect_checklist == $total) {
					$count_effect_total += 1;
				}
			}

			//recpetion
			foreach ($data['list_reception_staff_transfer'] as $value) {
				$count_info_total += 1;
				if ($staff_array[$value['meta']] != '') {
					$count_info += 1;
				}
			}

			$percent_info_total = $this->hr_profile_model->getPercent($count_info_total, $count_info);
			if ($percent_info_total >= 100) {
				$count_effect_total += 1;
			}
			if ($count_info_total > 0) {
				$count_total += 1;
			}

			$data['list_staff_asset'] = $this->hr_profile_model->get_allocation_asset($id);
			$count_asset = 0;
			$count_asset_total = 0;
			foreach ($data['list_staff_asset'] as $value) {
				$count_asset_total += 1;
				if ($value['status_allocation'] == 1) {
					$count_asset += 1;
				}
			}

			$percent_asset_total = $this->hr_profile_model->getPercent($count_asset_total, $count_asset);
			if ($percent_asset_total >= 100) {
				$count_effect_total += 1;
			}
			if ($count_asset_total > 0) {
				$count_total += 1;
			}

			//Get the latest employee's training result.
			$list_training_allocation = $this->hr_profile_model->get_training_allocation_staff($id);
			if ($list_training_allocation) {

				$data_marks = $this->get_mark_staff($id, $list_training_allocation->training_process_id);

				if (count($data_marks['staff_training_result']) > 0) {
					$count_total += 1;

					$training_allocation_min_point = 0;

					if (isset($list_training_allocation)) {

						$job_position_training = $this->hr_profile_model->get_job_position_training_de($list_training_allocation->jp_interview_training_id);

						if ($job_position_training) {
							$training_allocation_min_point = $job_position_training->mint_point;
						}
					}

					if ((float) $data_marks['training_program_point'] >= (float) $training_allocation_min_point) {
						$count_effect_total += 1;
					}

				}
			}

			return $this->hr_profile_model->getPercent($count_total, $count_effect_total);
		}
	}

	/**
	 * get mark staff
	 * @param  integer $id_staff
	 * @return array
	 */
	public function get_mark_staff($id_staff, $training_process_id) {
		$array_training_point = [];
		$training_program_point = 0;

		//Get the latest employee's training result.
		$trainig_resultset = $this->hr_profile_model->get_resultset_training($id_staff, $training_process_id);

		$array_training_resultset = [];
		$array_resultsetid = [];
		$list_resultset_id = '';

		foreach ($trainig_resultset as $item) {
			if (count($array_training_resultset) == 0) {
				array_push($array_training_resultset, $item['trainingid']);
				array_push($array_resultsetid, $item['resultsetid']);

				$list_resultset_id .= '' . $item['resultsetid'] . ',';
			}
			if (!in_array($item['trainingid'], $array_training_resultset)) {
				array_push($array_training_resultset, $item['trainingid']);
				array_push($array_resultsetid, $item['resultsetid']);

				$list_resultset_id .= '' . $item['resultsetid'] . ',';
			}
		}

		$list_resultset_id = rtrim($list_resultset_id, ",");
		$count_out = 0;
		if ($list_resultset_id == "") {
			$list_resultset_id = '0';
		} else {
			$count_out = count($array_training_resultset);
		}

		$array_result = [];
		foreach ($array_training_resultset as $key => $training_id) {
			$total_question = 0;
			$total_question_point = 0;

			$total_point = 0;
			$training_library_name = '';
			$training_question_forms = $this->hr_profile_model->hr_get_training_question_form_by_relid($training_id);
			$hr_position_training = $this->hr_profile_model->get_board_mark_form($training_id);
			$total_question = count($training_question_forms);
			if ($hr_position_training) {
				$training_library_name .= $hr_position_training->subject;
			}

			foreach ($training_question_forms as $question) {
				$flag_check_correct = true;

				$get_id_correct = $this->hr_profile_model->get_id_result_correct($question['questionid']);
				$form_results = $this->hr_profile_model->hr_get_form_results_by_resultsetid($array_resultsetid[$key], $question['questionid']);

				if (count($get_id_correct) == count($form_results)) {
					foreach ($get_id_correct as $correct_key => $correct_value) {
						if (!in_array($correct_value, $form_results)) {
							$flag_check_correct = false;
						}
					}
				} else {
					$flag_check_correct = false;
				}

				$result_point = $this->hr_profile_model->get_point_training_question_form($question['questionid']);
				$total_question_point += $result_point->point;

				if ($flag_check_correct == true) {
					$total_point += $result_point->point;
					$training_program_point += $result_point->point;
				}

			}

			array_push($array_training_point, [
				'training_name' => $training_library_name,
				'total_point' => $total_point,
				'training_id' => $training_id,
				'total_question' => $total_question,
				'total_question_point' => $total_question_point,
			]);
		}

		$response = [];
		$response['training_program_point'] = $training_program_point;
		$response['staff_training_result'] = $array_training_point;

		return $response;
	}

	/**
	 * delete reception
	 * @param  integer $id
	 */
	public function delete_reception($id) {
		$this->hr_profile_model->delete_manage_info_reception($id);
		$this->hr_profile_model->delete_setting_training($id);
		$this->hr_profile_model->delete_setting_asset_allocation($id);
		// $this->hr_profile_model->delete_tranining_result_by_staffid($id);

		$success = $this->hr_profile_model->delete_reception($id);
		if ($success == true) {
			$message = _l('hr_deleted');
			set_alert('success', $message);
		}
		redirect(admin_url('hr_profile/reception_staff'));
	}

	/**
	 * get reception
	 * @param  integer $id
	 * @return json
	 */
	public function get_reception($id = '') {
		$this->load->model('departments_model');
		$this->load->model('staff_model');
		$data['staff'] = $this->staff_model->get($id);

		if (isset($data['staff'])) {
			$data['position'] = $this->hr_profile_model->get_job_position($data['staff']->job_position);
			$data['department'] = $this->hr_profile_model->get_department_by_staffid($data['staff']->staffid);
			$data['group_checklist'] = $this->hr_profile_model->get_group_checklist_allocation_by_staff_id($data['staff']->staffid);
			$data['list_staff_asset'] = $this->hr_profile_model->get_allocation_asset($data['staff']->staffid);

			if (($data['staff']->job_position) && (is_numeric($data['staff']->job_position))) {
				$has_training = 1;
				$data['training_allocation_min_point'] = 0;
				$data['list_training_allocation'] = $this->hr_profile_model->get_training_allocation_staff($data['staff']->staffid);

				if (isset($data['list_training_allocation'])) {

					$job_position_training = $this->hr_profile_model->get_job_position_training_de($data['list_training_allocation']->jp_interview_training_id);

					if ($job_position_training) {
						$data['training_allocation_min_point'] = $job_position_training->mint_point;
					}

					if ($data['list_training_allocation']) {
						$training_process_id = $data['list_training_allocation']->training_process_id;

						$data['list_training'] = $this->hr_profile_model->get_list_position_training_by_id_training($data['list_training_allocation']->training_process_id);

						//Get the latest employee's training result.
						$training_results = $this->get_mark_staff($data['staff']->staffid, $training_process_id);

						$data['training_program_point'] = $training_results['training_program_point'];

						//have not done the test data
						$staff_training_result = [];
						foreach ($data['list_training'] as $key => $value) {
							$staff_training_result[$value['training_id']] = [
								'training_name' => $value['subject'],
								'total_point' => 0,
								'training_id' => $value['training_id'],
								'total_question' => 0,
								'total_question_point' => 0,
							];
						}

						//did the test
						if (count($training_results['staff_training_result']) > 0) {

							foreach ($training_results['staff_training_result'] as $result_key => $result_value) {
								if (isset($staff_training_result[$result_value['training_id']])) {
									unset($staff_training_result[$result_value['training_id']]);
								}
							}

							$data['staff_training_result'] = array_merge($training_results['staff_training_result'], $staff_training_result);

						} else {
							$data['staff_training_result'] = $staff_training_result;
						}

						if ((float) $training_results['training_program_point'] >= (float) $data['training_allocation_min_point']) {
							$data['complete'] = 0;
						} else {
							$data['complete'] = 1;
						}

					}
				}
			}

			echo json_encode([
				'data' => $this->load->view('reception_staff/reception_staff_sidebar', $data, true),
				'success' => true,
			]);
		}
	}

/**
 * change status checklist
 * @return json
 */
	public function change_status_checklist() {
		if ($this->input->post()) {
			$data = $this->input->post();
			$success = $this->hr_profile_model->update_checklist($data);
			if ($success == true) {
				echo json_encode([
					'success' => true,
				]);
			}
		}
	}
/**
 * add new asset
 * @param integer $id
 */
	public function add_new_asset($id) {
		if ($this->input->post()) {
			$data = $this->input->post();
			$list_tt = explode(',', $data['name']);
			$this->hr_profile_model->add_new_asset_staff($id, $list_tt);
			$list_asset = $this->hr_profile_model->get_allocation_asset($id);

			$html = '';
			foreach ($list_asset as $value) {
				$checked = '';
				if ($value['status_allocation'] == 1) {
					$checked = 'checked';
				}
				$html .= '<div class="row item_hover">
	  <div class="col-md-7">
	  <div class="checkbox">
	  <input data-can-view="" type="checkbox" class="capability" id="' . $value['asset_name'] . '" name="asset_staff[]" data-id="' . $value['allocation_id'] . '" value="' . $value['status_allocation'] . '" ' . $checked . ' onclick="active_asset(this);">
	  <label for="' . $value['asset_name'] . '">
	  ' . $value['asset_name'] . '
	  </label>
	  </div>
	  </div>
	  <div class="col-md-3 pt-10">
	  <a href="#" class="text-danger" onclick="delete_asset(this);"  data-id="' . $value['allocation_id'] . '" >' . _l('delete') . '</a>
	  </div>
	  </div>';
			}
			echo json_encode([
				'data' => $html,
				'success' => true,
			]);
		}
	}
/**
 * change status allocation asset
 * @return json
 */
	public function change_status_allocation_asset() {
		if ($this->input->post()) {
			$data = $this->input->post();
			$success = $this->hr_profile_model->update_asset_staff($data);
			if ($success == true) {
				echo json_encode([
					'success' => true,
				]);
			}
		}
	}
/**
 * delete asset
 * @param  integer $id
 * @param  integer $id2
 * @return json
 */
	public function delete_asset($id, $id2) {
		$success = $this->hr_profile_model->delete_allocation_asset($id);
		if ($success == true) {

			$list_asset = $this->hr_profile_model->get_allocation_asset($id2);

			$html = '';
			foreach ($list_asset as $value) {
				$checked = '';
				if ($value['status_allocation'] == 1) {
					$checked = 'checked';
				}
				$html .= '<div class="row item_hover">
	<div class="col-md-7">
	<div class="checkbox">
	<input data-can-view="" type="checkbox" class="capability" name="asset_staff[]" data-id="' . $value['allocation_id'] . '" value="' . $value['status_allocation'] . '" ' . $checked . ' onclick="active_asset(this);">
	<label>
	' . $value['asset_name'] . '
	</label>
	</div>
	</div>
	<div class="col-md-3 pt-10">
	<a href="#" class="text-danger" onclick="delete_asset(this);"  data-id="' . $value['allocation_id'] . '" >' . _l('delete') . '</a>
	</div>
	</div>';
			}
			echo json_encode([
				'data' => $html,
				'success' => true,
			]);
		} else {
			echo json_encode([
				'success' => false,
			]);
		}
	}

	/**
	 * staff infor
	 * @return view
	 */
	public function staff_infor() {
		if (!has_permission('hrm_hr_records', '', 'view') && !has_permission('hrm_hr_records', '', 'view_own')) {
			access_denied('staff');
		}
		$this->load->model('roles_model');
		$this->load->model('staff_model');
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data(module_views_path('hr_profile', 'table_staff'));
		}
		$data['departments'] = $this->departments_model->get();
		$data['staff_members'] = $this->hr_profile_model->get_staff('', ['active' => 1]);
		$data['title'] = _l('hr_hr_profile');
		$data['dep_tree'] = json_encode($this->hr_profile_model->get_department_tree());
		$data['staff_dep_tree'] = json_encode($this->hr_profile_model->get_staff_tree());

		//load deparment by manager
		if (!is_admin() && !has_permission('hrm_hr_records', '', 'view')) {
			//View own
			$data['staff_members_chart'] = json_encode($this->hr_profile_model->get_data_chart_v2());

		} else {
			//admin or view global
			$data['staff_members_chart'] = json_encode($this->hr_profile_model->get_data_chart());
		}

		$data['staff_role'] = $this->hr_profile_model->get_job_position();
		$this->load->view('hr_record/manage_staff', $data);
	}

	/**
	 * table
	 */
	public function table() {
		$this->app->get_table_data(module_views_path('hr_profile', 'table_staff'));
	}

	/**
	 * importxlsx
	 * @return view
	 */
	public function importxlsx() {
		$data['departments'] = $this->departments_model->get();
		$data['job_positions'] = $this->hr_profile_model->get_job_position();
		$data['workplaces'] = $this->hr_profile_model->get_workplace();
		/*get language active*/
		$data_staff = $this->hr_profile_model->get_staff(get_staff_user_id());
		if ($data_staff) {
			if ($data_staff->default_language != '') {
				$data['active_language'] = $data_staff->default_language;
			} else {
				$data['active_language'] = get_option('active_language');
			}
		} else {
			$data['active_language'] = get_option('active_language');
		}
		$this->load->view('hr_profile/import_xlsx', $data);
	}

	/**
	 * import employees excel
	 * @return [type]
	 */
	public function import_employees_excel() {
		if (!has_permission('hrm_hr_records', '', 'create') && !has_permission('hrm_hr_records', '', 'edit') && !is_admin()) {
			access_denied('hrm_hr_records');
		}

		if (!class_exists('XLSXReader_fin')) {
			require_once module_dir_path(HR_PROFILE_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
		}
		require_once module_dir_path(HR_PROFILE_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

		$filename = '';
		if ($this->input->post()) {
			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

				$this->delete_error_file_day_before(1);

				// Get the temp file path
				$tmpFilePath = $_FILES['file_csv']['tmp_name'];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$rows = [];
					$arr_insert = [];
					$arr_update = [];

					$tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

					if (!file_exists(TEMP_FOLDER)) {
						mkdir(TEMP_FOLDER, 0755);
					}

					if (!file_exists($tmpDir)) {
						mkdir($tmpDir, 0755);
					}

					// Setup our new file path
					$newFilePath = $tmpDir . $_FILES['file_csv']['name'];

					if (move_uploaded_file($tmpFilePath, $newFilePath)) {

						//Writer file
						$writer_header = array(

							_l('id') => 'string',
							_l('hr_staff_code') => 'string',
							_l('hr_firstname') => 'string',
							_l('hr_lastname') => 'string',
							_l('hr_sex') => 'string',
							_l('hr_hr_birthday') => 'string',
							_l('Email') => 'string',
							_l('staff_add_edit_phonenumber') => 'string',
							_l('hr_hr_workplace') => 'string',
							_l('hr_status_work') => 'string',
							_l('hr_hr_job_position') => 'string',
							_l('hr_team_manage') => 'string',
							_l('staff_add_edit_role') => 'string',
							_l('hr_hr_literacy') => 'string',
							_l('staff_hourly_rate') => 'string',
							_l('staff_add_edit_departments') => 'string',
							_l('staff_add_edit_password') => 'string',
							_l('hr_hr_home_town') => 'string',
							_l('hr_hr_marital_status') => 'string',
							_l('hr_current_address') => 'string',
							_l('hr_hr_nation') => 'string',
							_l('hr_hr_birthplace') => 'string',
							_l('hr_hr_religion') => 'string',
							_l('hr_citizen_identification') => 'string',
							_l('hr_license_date') => 'string',
							_l('hr_hr_place_of_issue') => 'string',
							_l('hr_hr_resident') => 'string',
							_l('hr_bank_account_number') => 'string',
							_l('hr_bank_account_name') => 'string',
							_l('hr_bank_name') => 'string',
							_l('hr_Personal_tax_code') => 'string',
							_l('staff_add_edit_facebook') => 'string',
							_l('staff_add_edit_linkedin') => 'string',
							_l('staff_add_edit_skype') => 'string',

							_l('error') => 'string',
						);

						$writer = new XLSXWriter();
						// $writer->writeSheetHeader('Sheet1', $writer_header,  $col_options = ['widths'=>[40,40,40,50,40,40,40,40,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50,50]]);

						$widths = [40, 40, 40, 50, 40, 40, 40, 40, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50];
						//orange: do not update
						$col_style1 = [0, 1];
						$style1 = ['widths' => $widths, 'fill' => '#fc2d42', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

						//red: required
						$col_style2 = [2, 3, 6, 9, 10];
						$style2 = ['widths' => $widths, 'fill' => '#ff9800', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

						//otherwise blue: can be update

						$writer->writeSheetHeader_v2('Sheet1', $writer_header, $col_options = ['widths' => $widths, 'fill' => '#03a9f46b', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13],
							$col_style1, $style1, $col_style2, $style2);

						$row_style1 = array('fill' => '#F8CBAD', 'height' => 25, 'border' => 'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');
						$row_style2 = array('fill' => '#FCE4D6', 'height' => 25, 'border' => 'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');

						//Reader file
						$xlsx = new XLSXReader_fin($newFilePath);
						$sheetNames = $xlsx->getSheetNames();
						$data = $xlsx->getSheetData($sheetNames[1]);
						$arr_header = [];

						$arr_header['staffid'] = 0;
						$arr_header['staff_identifi'] = 1;
						$arr_header['firstname'] = 2;
						$arr_header['lastname'] = 3;
						$arr_header['sex'] = 4;
						$arr_header['birthday'] = 5;
						$arr_header['email'] = 6;
						$arr_header['phonenumber'] = 7;
						$arr_header['workplace'] = 8;
						$arr_header['status_work'] = 9;
						$arr_header['job_position'] = 10;
						$arr_header['team_manage'] = 11;
						$arr_header['role'] = 12;
						$arr_header['literacy'] = 13;
						$arr_header['hourly_rate'] = 14;
						$arr_header['department'] = 15;
						$arr_header['password'] = 16;
						$arr_header['home_town'] = 17;
						$arr_header['marital_status'] = 18;
						$arr_header['current_address'] = 19;
						$arr_header['nation'] = 20;
						$arr_header['birthplace'] = 21;
						$arr_header['religion'] = 22;
						$arr_header['identification'] = 23;
						$arr_header['days_for_identity'] = 24;
						$arr_header['place_of_issue'] = 25;
						$arr_header['resident'] = 26;
						$arr_header['account_number'] = 27;
						$arr_header['name_account'] = 28;
						$arr_header['issue_bank'] = 29;
						$arr_header['Personal_tax_code'] = 30;
						$arr_header['facebook'] = 31;
						$arr_header['linkedin'] = 32;
						$arr_header['skype'] = 33;

						$pattern = '#^[a-z][a-z0-9\._]{2,31}@[a-z0-9\-]{3,}(\.[a-z]{2,4}){1,2}$#';
						$reg_day = '#^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$#';

						$staff_str_result = '';
						$staff_prefix_str = '';
						$staff_prefix_str .= get_hr_profile_option('staff_code_prefix');
						$staff_next_number = (int) get_hr_profile_option('staff_code_number');
						$staff_str_result .= $staff_prefix_str . str_pad($staff_next_number, 5, '0', STR_PAD_LEFT);

						//job position data
						$job_position_data = [];
						$job_positions = $this->hr_profile_model->get_job_position();

						foreach ($job_positions as $key => $job_position) {
							$job_position_data[$job_position['position_code']] = $job_position;
						}

						//direct manager
						$staff_data = [];
						$list_staffs = $this->hr_profile_model->get_staff();
						foreach ($list_staffs as $key => $list_staff) {
							$staff_data[$list_staff['staff_identifi']] = $list_staff;
						}

						//get role data
						$roles_data = [];
						$this->load->model('role/roles_model');
						$list_roles = $this->roles_model->get();
						foreach ($list_roles as $list_role) {
							$roles_data[$list_role['roleid']] = !empty($list_role['permissions']) ? unserialize($list_role['permissions']) : [];
						}

						//get workplace data
						$list_workplaces = $this->hr_profile_model->get_workplace();

						//get list department
						$this->load->model('department/departments_model');
						$list_departments = $this->departments_model->get();

						$total_rows = 0;
						$total_row_false = 0;
						$total_row_success = 0;

						$column_key = $data[1];

						//write the next row (row2)
						$writer->writeSheetRow('Sheet1', array_keys($arr_header));

						for ($row = 2; $row < count($data); $row++) {

							$total_rows++;

							$rd = array();
							$flag = 0;
							$flag2 = 0;
							$flag_mail = 0;

							$string_error = '';

							$flag_value_job_position = 0;
							$flag_value_team_manage = 0;
							$flag_value_workplace = 0;
							$flag_value_role = 0;
							$flag_value_department = [];
							$permissions = [];

							$value_staffid = isset($data[$row][$arr_header['staffid']]) ? $data[$row][$arr_header['staffid']] : '';
							$value_staff_identifi = isset($data[$row][$arr_header['staff_identifi']]) ? $data[$row][$arr_header['staff_identifi']] : '';
							$value_firstname = isset($data[$row][$arr_header['firstname']]) ? $data[$row][$arr_header['firstname']] : '';
							$value_lastname = isset($data[$row][$arr_header['lastname']]) ? $data[$row][$arr_header['lastname']] : '';
							$value_sex = isset($data[$row][$arr_header['sex']]) ? $data[$row][$arr_header['sex']] : '';

							$value_birthday = isset($data[$row][$arr_header['birthday']]) ? $data[$row][$arr_header['birthday']] : '';
							$value_email = isset($data[$row][$arr_header['email']]) ? $data[$row][$arr_header['email']] : '';
							$value_phonenumber = isset($data[$row][$arr_header['phonenumber']]) ? $data[$row][$arr_header['phonenumber']] : '';
							$value_workplace = isset($data[$row][$arr_header['workplace']]) ? $data[$row][$arr_header['workplace']] : '';
							$value_status_work = isset($data[$row][$arr_header['status_work']]) ? $data[$row][$arr_header['status_work']] : '';
							$value_job_position = isset($data[$row][$arr_header['job_position']]) ? $data[$row][$arr_header['job_position']] : '';
							$value_team_manage = isset($data[$row][$arr_header['team_manage']]) ? $data[$row][$arr_header['team_manage']] : '';
							$value_role = isset($data[$row][$arr_header['role']]) ? $data[$row][$arr_header['role']] : '';
							$value_literacy = isset($data[$row][$arr_header['literacy']]) ? $data[$row][$arr_header['literacy']] : '';
							$value_hourly_rate = isset($data[$row][$arr_header['hourly_rate']]) ? $data[$row][$arr_header['hourly_rate']] : '';
							$value_department = isset($data[$row][$arr_header['department']]) ? $data[$row][$arr_header['department']] : '';
							$value_password = isset($data[$row][$arr_header['password']]) ? $data[$row][$arr_header['password']] : '';
							$value_home_town = isset($data[$row][$arr_header['home_town']]) ? $data[$row][$arr_header['home_town']] : '';
							$value_marital_status = isset($data[$row][$arr_header['marital_status']]) ? $data[$row][$arr_header['marital_status']] : '';
							$value_current_address = isset($data[$row][$arr_header['current_address']]) ? $data[$row][$arr_header['current_address']] : '';
							$value_nation = isset($data[$row][$arr_header['nation']]) ? $data[$row][$arr_header['nation']] : '';
							$value_birthplace = isset($data[$row][$arr_header['birthplace']]) ? $data[$row][$arr_header['birthplace']] : '';
							$value_religion = isset($data[$row][$arr_header['religion']]) ? $data[$row][$arr_header['religion']] : '';
							$value_identification = isset($data[$row][$arr_header['identification']]) ? $data[$row][$arr_header['identification']] : '';
							$value_days_for_identity = isset($data[$row][$arr_header['days_for_identity']]) ? $data[$row][$arr_header['days_for_identity']] : '';
							$value_place_of_issue = isset($data[$row][$arr_header['place_of_issue']]) ? $data[$row][$arr_header['place_of_issue']] : '';
							$value_resident = isset($data[$row][$arr_header['resident']]) ? $data[$row][$arr_header['resident']] : '';
							$value_account_number = isset($data[$row][$arr_header['account_number']]) ? $data[$row][$arr_header['account_number']] : '';
							$value_name_account = isset($data[$row][$arr_header['name_account']]) ? $data[$row][$arr_header['name_account']] : '';
							$value_issue_bank = isset($data[$row][$arr_header['issue_bank']]) ? $data[$row][$arr_header['issue_bank']] : '';
							$value_Personal_tax_code = isset($data[$row][$arr_header['Personal_tax_code']]) ? $data[$row][$arr_header['Personal_tax_code']] : '';
							$value_facebook = isset($data[$row][$arr_header['facebook']]) ? $data[$row][$arr_header['facebook']] : '';
							$value_linkedin = isset($data[$row][$arr_header['linkedin']]) ? $data[$row][$arr_header['linkedin']] : '';
							$value_skype = isset($data[$row][$arr_header['skype']]) ? $data[$row][$arr_header['skype']] : '';

							/*check null*/
							if (is_null($value_firstname) == true || $value_firstname == '') {
								$string_error .= _l('hr_firstname') . ' ' . _l('not_yet_entered') . '; ';
								$flag = 1;
							}

							/*check null*/
							if (is_null($value_lastname) == true || $value_lastname == '') {
								$string_error .= _l('hr_lastname') . ' ' . _l('not_yet_entered') . '; ';
								$flag = 1;
							}

							if (is_null($value_status_work) == true || $value_status_work == '') {
								$string_error .= _l('hr_status_work') . ' ' . _l('not_yet_entered') . '; ';
								$flag = 1;
							}

							if (is_null($value_job_position) == true || $value_job_position == '') {
								$string_error .= _l('hr_hr_job_position') . ' ' . _l('not_yet_entered') . '; ';
								$flag = 1;
							}

							if (is_null($value_sex) != true && $value_sex != '') {

								if ($value_sex != 'male' && $value_sex != 'female') {
									$string_error .= _l('hr_sex') . ' ' . _l('does_not_exist') . '; ';
									$flag2 = 1;
								}
							}

							if (is_null($value_email) == true || $value_email == '') {
								$string_error .= _l('email') . ' ' . _l('not_yet_entered') . '; ';
								$flag = 1;
							} else {
								if (preg_match($pattern, $value_email, $match) != 1) {
									$string_error .= _l('email') . ' ' . _l('invalid') . '; ';
									$flag = 1;
								} else {
									$flag_mail = 1;
								}
							}

							//check mail exist
							if ($flag_mail == 1) {

								if ($value_staffid == '' || is_null($value_staffid) == true) {

									$this->db->where('email', $value_email);
									$total_rows_email = $this->db->count_all_results(db_prefix() . 'staff');
									if ($total_rows_email > 0) {
										$string_error .= _l('email') . ' ' . _l('exist') . '; ';
										$flag2 = 1;
									}
								}

							}

							//check start_time
							if (is_null($value_birthday) != true && $value_birthday != '') {

								if (is_null($value_birthday) != true) {

									if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", trim($value_birthday, " "))) {
										$test = true;

									} else {
										$flag2 = 1;
										$string_error .= _l('hr_hr_birthday') . ' ' . _l('invalid') . '; ';
									}
								}
							}

							//check start_time
							if (is_null($value_days_for_identity) != true && $value_days_for_identity != '') {

								if (is_null($value_days_for_identity) != true) {

									if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", trim($value_days_for_identity, " "))) {
										$test = true;

									} else {
										$flag2 = 1;
										$string_error .= _l('days_for_identity') . ' ' . _l('invalid') . '; ';
									}
								}
							}

							//check position is int
							if (is_null($value_job_position) != true && strlen($value_job_position) > 0) {

								if (!isset($job_position_data[$value_job_position])) {
									$string_error .= _l('hr_hr_job_position') . ' ' . _l('does_not_exist') . '; ';
									$flag2 = 1;
								} else {
									$flag_value_job_position = $job_position_data[$value_job_position]['position_id'];
								}

							}

							//value_team_manage
							if (is_null($value_team_manage) != true && strlen($value_team_manage) > 0) {

								if (!isset($staff_data[$value_team_manage])) {
									$string_error .= _l('hr_team_manage') . ' ' . _l('does_not_exist') . '; ';
									$flag2 = 1;
								} else {

									$flag_value_team_manage = $staff_data[$value_team_manage]['staffid'];
								}
							}

							//check workplace is int
							if (is_null($value_workplace) != true && strlen($value_workplace) > 0) {

								$workplaces_flag = false;
								foreach ($list_workplaces as $list_workplace) {
									if ($list_workplace['name'] == $value_workplace) {
										$workplaces_flag = true;

										$flag_value_workplace = $list_workplace['id'];
									}
								}

								if ($workplaces_flag == false) {
									$string_error .= _l('hr_hr_workplace') . ' ' . _l('does_not_exist') . '; ';
									$flag2 = 1;

								} else {
								}

							}

							//check role
							if (is_null($value_role) != true && strlen($value_role) > 0) {

								$roles_flag = false;
								foreach ($list_roles as $list_role) {
									if ($list_role['name'] == $value_role) {
										$roles_flag = true;

										$flag_value_role = $list_role['roleid'];

										if (isset($roles_data[$list_role['roleid']])) {
											$permissions = $roles_data[$list_role['roleid']];

										}

									}
								}

								if ($roles_flag == false) {
									$string_error .= _l('staff_add_edit_role') . ' ' . _l('does_not_exist') . '; ';
									$flag2 = 1;

								}

							}

							//check department
							if (is_null($value_department) != true && strlen($value_department) > 0) {
								$arr_department_value = explode(';', $value_department);

								$deparments_flag = true;
								$str_deparments_not_exist = '';
								$temp_str_deparments_not_exist = explode(';', $value_department);

								foreach ($list_departments as $list_department) {

									if (in_array($list_department['name'], $arr_department_value)) {

										$flag_value_department[] = $list_department['departmentid'];

										foreach ($temp_str_deparments_not_exist as $key => $str_deparments_not_exist) {
											if ($str_deparments_not_exist == $list_department['name']) {

												unset($temp_str_deparments_not_exist[$key]);

											}
										}
									}

								}

								if (count($temp_str_deparments_not_exist) > 0) {
									$string_error .= _l('staff_add_edit_departments') . ': ' . implode(';', $temp_str_deparments_not_exist) . ' ' . _l('does_not_exist');
									$flag2 = 1;

								}

							}

							if (($flag == 1) || $flag2 == 1) {
								//write error file
								$writer->writeSheetRow('Sheet1', [
									$value_staffid,
									$value_staff_identifi,
									$value_firstname,
									$value_lastname,
									$value_sex,
									$value_birthday,
									$value_email,
									$value_phonenumber,
									$value_workplace,
									$value_status_work,
									$value_job_position,
									$value_team_manage,
									$value_role,
									$value_literacy,
									$value_hourly_rate,
									$value_department,
									$value_password,
									$value_home_town,
									$value_marital_status,
									$value_current_address,
									$value_nation,
									$value_birthplace,
									$value_religion,
									$value_identification,
									$value_days_for_identity,
									$value_place_of_issue,
									$value_resident,
									$value_account_number,
									$value_name_account,
									$value_issue_bank,
									$value_Personal_tax_code,
									$value_facebook,
									$value_linkedin,
									$value_skype,
									$string_error,
								]);

								// $numRow++;
								$total_row_false++;
							}

							if ($flag == 0 && $flag2 == 0) {

								$rd['staffid'] = $value_staffid;
								$rd['staff_identifi'] = $staff_prefix_str . str_pad($staff_next_number, 5, '0', STR_PAD_LEFT);
								$rd['firstname'] = $value_firstname;
								$rd['lastname'] = $value_lastname;
								$rd['sex'] = $value_sex;
								$rd['birthday'] = $value_birthday;
								$rd['email'] = $value_email;
								$rd['phonenumber'] = $value_phonenumber;
								$rd['workplace'] = $flag_value_workplace;
								$rd['status_work'] = $value_status_work;
								$rd['job_position'] = $flag_value_job_position;
								$rd['team_manage'] = $flag_value_team_manage;
								$rd['role'] = $flag_value_role;
								$rd['literacy'] = $value_literacy;
								$rd['hourly_rate'] = $value_hourly_rate;
								$rd['departments'] = $flag_value_department;

								if (strlen($value_password) > 0) {
									$rd['password'] = $value_password;
								} else {
									$rd['password'] = '123456';
								}

								$rd['home_town'] = $value_home_town;
								$rd['marital_status'] = $value_marital_status;
								$rd['current_address'] = $value_current_address;
								$rd['nation'] = $value_nation;
								$rd['birthplace'] = $value_birthplace;
								$rd['religion'] = $value_religion;
								$rd['identification'] = $value_identification;
								$rd['days_for_identity'] = $value_days_for_identity;
								$rd['place_of_issue'] = $value_place_of_issue;
								$rd['resident'] = $value_resident;
								$rd['account_number'] = $value_account_number;
								$rd['name_account'] = $value_name_account;
								$rd['issue_bank'] = $value_issue_bank;
								$rd['Personal_tax_code'] = $value_Personal_tax_code;
								$rd['facebook'] = $value_facebook;
								$rd['linkedin'] = $value_linkedin;
								$rd['skype'] = $value_skype;

								$rd['permissions'] = $permissions;

								$rows[] = $rd;
								array_push($arr_insert, $rd);

								$staff_next_number++;

							}

							if ($flag == 0 && $flag2 == 0) {

								// $rd = array_combine($column_key, $data[$row]);

								if ($rd['staffid'] == '' || $rd['staffid'] == 0) {

									$rd['email_signature'] = '';
									//insert staff
									$response = $this->hr_profile_model->add_staff($rd);
									if ($response) {
										$total_row_success++;
									}
								} else {
									//update staff
									unset($data['staff_identifi']);
									unset($data['password']);

									$rd['email_signature'] = '';
									$response = $this->hr_profile_model->update_staff($rd, $rd['staffid']);
									if ($response) {
										$total_row_success++;
									}
								}

							}

						}

						$total_rows = $total_rows;
						$total_row_success = $total_row_success;
						$dataerror = '';
						$message = 'Not enought rows for importing';

						if ($total_row_false != 0) {
							$filename = 'Import_employee_error_' . get_staff_user_id() . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
							$writer->writeToFile(str_replace($filename, HR_PROFILE_ERROR . $filename, $filename));
						}

					}
				}
			}
		}

		if (file_exists($newFilePath)) {
			@unlink($newFilePath);
		}

		echo json_encode([
			'message' => $message,
			'total_row_success' => $total_row_success,
			'total_row_false' => $total_row_false,
			'total_rows' => $total_rows,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id(),
			'filename' => HR_PROFILE_ERROR . $filename,
		]);
	}

	/**
	 * importxlsx2
	 * @return  json
	 */
	public function importxlsx2() {
		if (!is_admin() && get_option('allow_non_admin_members_to_import_leads') != '1') {
			access_denied('Leads Import');
		}
		$total_row_false = 0;
		$total_rows = 0;
		$dataerror = 0;
		$total_row_success = 0;
		if ($this->input->post()) {
			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
				// Get the temp file path
				$tmpFilePath = $_FILES['file_csv']['tmp_name'];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';
					if (!file_exists(TEMP_FOLDER)) {
						mkdir(TEMP_FOLDER, 0755);
					}
					if (!file_exists($tmpDir)) {
						mkdir($tmpDir, 0755);
					}
					// Setup our new file path
					$newFilePath = $tmpDir . $_FILES['file_csv']['name'];
					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						$import_result = true;
						$rows = [];

						$objReader = new PHPExcel_Reader_Excel2007();
						$objReader->setReadDataOnly(true);
						$objPHPExcel = $objReader->load($newFilePath);
						$rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();
						$sheet = $objPHPExcel->getActiveSheet();

						$dataError = new PHPExcel();
						$dataError->setActiveSheetIndex(0);
						$dataError->getActiveSheet()->setTitle('Data is not allowed');
						$dataError->getActiveSheet()->getColumnDimension('A')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('B')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('C')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('D')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('E')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('F')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('G')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('H')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('I')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('J')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('K')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('L')->setWidth(30);
						$dataError->getActiveSheet()->getColumnDimension('M')->setWidth(30);
						$dataError->getActiveSheet()->getColumnDimension('N')->setWidth(30);
						$dataError->getActiveSheet()->getColumnDimension('O')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('P')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('R')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('S')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('T')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('U')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('V')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('W')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('X')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('AA')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('AB')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('AC')->setWidth(20);
						$dataError->getActiveSheet()->getStyle('A1:AA1')->getFont()->setBold(true);

						$dataError->getActiveSheet()->setCellValue('A1', _l('hr_staff_code'));
						$dataError->getActiveSheet()->setCellValue('B1', _l('hr_firstname'));
						$dataError->getActiveSheet()->setCellValue('C1', _l('hr_lastname'));
						$dataError->getActiveSheet()->setCellValue('D1', _l('email'));
						$dataError->getActiveSheet()->setCellValue('E1', _l('hr_gender'));
						$dataError->getActiveSheet()->setCellValue('F1', _l('birthday'));
						$dataError->getActiveSheet()->setCellValue('G1', _l('phonenumber'));
						$dataError->getActiveSheet()->setCellValue('H1', _l('nation'));
						$dataError->getActiveSheet()->setCellValue('I1', _l('religion'));
						$dataError->getActiveSheet()->setCellValue('J1', _l('birthplace'));
						$dataError->getActiveSheet()->setCellValue('K1', _l('home_town'));
						$dataError->getActiveSheet()->setCellValue('L1', _l('resident'));
						$dataError->getActiveSheet()->setCellValue('M1', _l('hr_current_address'));
						$dataError->getActiveSheet()->setCellValue('N1', _l('marital_status'));
						$dataError->getActiveSheet()->setCellValue('O1', _l('identification'));
						$dataError->getActiveSheet()->setCellValue('P1', _l('days_for_identity'));
						$dataError->getActiveSheet()->setCellValue('Q1', _l('place_of_issue'));
						$dataError->getActiveSheet()->setCellValue('R1', _l('literacy'));
						$dataError->getActiveSheet()->setCellValue('S1', _l('job_position'));
						$dataError->getActiveSheet()->setCellValue('T1', _l('hr_job_rank'));
						$dataError->getActiveSheet()->setCellValue('U1', _l('workplace'));
						$dataError->getActiveSheet()->setCellValue('V1', _l('departments'));
						$dataError->getActiveSheet()->setCellValue('W1', _l('account_number'));
						$dataError->getActiveSheet()->setCellValue('X1', _l('hr_name_account'));
						$dataError->getActiveSheet()->setCellValue('Y1', _l('hr_issue_bank'));
						$dataError->getActiveSheet()->setCellValue('Z1', _l('hr_Personal_tax_code'));
						$dataError->getActiveSheet()->setCellValue('AA1', _l('hr_status_work'));
						$dataError->getActiveSheet()->setCellValue('AB1', _l('password'));
						$dataError->getActiveSheet()->setCellValue('AC1', _l('error'));

						$styleArray = array(
							'font' => array(
								'bold' => true,
								'color' => array('rgb' => 'ff0000'),

							));
						$numRow = 2;
						$total_rows = 0;

						//get data for compare
						foreach ($rowIterator as $row) {

							$rowIndex = $row->getRowIndex();

							if ($rowIndex > 1) {
								$rd = array();
								$flag = 0;
								$flag2 = 0;
								$flag_mail = 0;
								$string_error = '';
								$value_cell_hrcode = $sheet->getCell('A' . $rowIndex)->getValue();
								$value_cell_first_name = $sheet->getCell('B' . $rowIndex)->getValue();
								$value_cell_last_name = $sheet->getCell('C' . $rowIndex)->getValue();
								$value_cell_email = $sheet->getCell('D' . $rowIndex)->getValue();
								$value_cell_sex = $sheet->getCell('E' . $rowIndex)->getValue();
								$value_cell_birthday = $sheet->getCell('F' . $rowIndex)->getValue();
								$value_cell_maries_status = $sheet->getCell('N' . $rowIndex)->getValue();

								$value_cell_status = $sheet->getCell('AA' . $rowIndex)->getValue();
								$value_cell_day_identity = $sheet->getCell('P' . $rowIndex)->getValue();
								$value_cell_position = $sheet->getCell('S' . $rowIndex)->getValue();
								$value_cell_workplace = $sheet->getCell('U' . $rowIndex)->getValue();
								$value_cell_password = $sheet->getCell('AB' . $rowIndex)->getValue();
								$pattern = '#^[a-z][a-z0-9\._]{2,31}@[a-z0-9\-]{3,}(\.[a-z]{2,4}){1,2}$#';
								$reg_day = '#^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$#';
								$position_array = $this->hr_profile_model->get_job_position_arrayid();
								$workplace_array = $this->hr_profile_model->get_workplace_array_id();
								$sex_array = ['0', '1'];
								$status_array = ['0', '1', '2'];

								if (is_null($value_cell_hrcode) == true) {
									$string_error .= _l('hr_hr_code') . _l('not_yet_entered');
									$flag = 1;
								}

								if (is_null($value_cell_first_name) == true) {
									$string_error .= _l('hr_firstname') . _l('not_yet_entered');
									$flag = 1;
								}

								if (is_null($value_cell_last_name) == true) {
									$string_error .= _l('hr_lastname') . _l('not_yet_entered');
									$flag = 1;
								}

								if (is_null($value_cell_email) == true) {
									$string_error .= _l('email') . _l('not_yet_entered');
									$flag = 1;
								} else {
									if (preg_match($pattern, $value_cell_email, $match) != 1) {
										$string_error .= _l('email') . _l('invalid');
										$flag = 1;
									} else {
										$flag_mail = 1;
									}
								}

								//check hr_code exist
								if (is_null($value_cell_hrcode) != true) {
									$this->db->where('staff_identifi', $value_cell_hrcode);
									$hrcode = $this->db->count_all_results('tblstaff');
									if ($hrcode > 0) {
										$string_error .= _l('hr_hr_code') . _l('exist');
										$flag2 = 1;
									}

								}
								//check mail exist
								if ($flag_mail == 1) {
									$this->db->where('email', $value_cell_email);
									$total_rows_email = $this->db->count_all_results(db_prefix() . 'staff');
									if ($total_rows_email > 0) {
										$string_error .= _l('email') . _l('exist');
										$flag2 = 1;
									}
								}

								//check sex is int
								if (is_null($value_cell_sex) != true) {
									if (is_string($value_cell_sex)) {
										$string_error .= _l('hr_sex') . _l('invalid');
										$flag2 = 1;

									} elseif (in_array($value_cell_sex, $sex_array) != true) {
										$string_error .= _l('hr_sex') . _l('does_not_exist');
										$flag2 = 1;
									}
								}

								//check position is int
								if (is_null($value_cell_position) != true) {
									if (is_string($value_cell_position)) {
										$string_error .= _l('job_position') . _l('invalid');
										$flag2 = 1;

									} elseif (in_array($value_cell_position, $position_array) != true) {
										$string_error .= _l('job_position') . _l('does_not_exist');
										$flag2 = 1;
									}

								}
								//check status is int
								if (is_null($value_cell_status) != true) {
									if (is_string($value_cell_status)) {
										$string_error .= _l('hr_status_work') . _l('invalid');
										$flag2 = 1;

									} elseif (in_array($value_cell_status, $status_array) != true) {
										$string_error .= _l('hr_status_work') . _l('does_not_exist');
										$flag2 = 1;
									}
								}
								//check workplace is int
								if (is_null($value_cell_workplace) != true) {
									if (!is_numeric($value_cell_workplace)) {
										$string_error .= _l('workplace') . _l('invalid');
										$flag2 = 1;
									} elseif (in_array($value_cell_workplace, $workplace_array) != true) {
										$string_error .= _l('workplace') . _l('does_not_exist');
										$flag2 = 1;
									}
								}

								//check birday input
								if (is_null($value_cell_birthday) != true) {
									if (preg_match($reg_day, $value_cell_birthday, $match) != 1) {
										$string_error .= _l('birthday') . _l('invalid');
										$flag = 1;
									}
								}
								//check day identity
								if (is_null($value_cell_day_identity) != true) {
									if (preg_match($reg_day, $value_cell_day_identity, $match) != 1) {
										$string_error .= _l('days_for_identity') . _l('invalid');
										$flag = 1;
									}

								}

								if (($flag == 1) || ($flag2 == 1)) {
									$dataError->getActiveSheet()->setCellValue('A' . $numRow, $sheet->getCell('A' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('B' . $numRow, $sheet->getCell('B' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('C' . $numRow, $sheet->getCell('C' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('D' . $numRow, $sheet->getCell('D' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('E' . $numRow, $sheet->getCell('E' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('F' . $numRow, $sheet->getCell('F' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('G' . $numRow, $sheet->getCell('G' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('H' . $numRow, $sheet->getCell('H' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('I' . $numRow, $sheet->getCell('I' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('J' . $numRow, $sheet->getCell('J' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('K' . $numRow, $sheet->getCell('K' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('L' . $numRow, $sheet->getCell('L' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('M' . $numRow, $sheet->getCell('M' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('N' . $numRow, $sheet->getCell('N' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('O' . $numRow, $sheet->getCell('O' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('P' . $numRow, $sheet->getCell('P' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('Q' . $numRow, $sheet->getCell('Q' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('R' . $numRow, $sheet->getCell('R' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('S' . $numRow, $sheet->getCell('S' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('T' . $numRow, $sheet->getCell('T' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('U' . $numRow, $sheet->getCell('U' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('V' . $numRow, $sheet->getCell('V' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('W' . $numRow, $sheet->getCell('W' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('X' . $numRow, $sheet->getCell('X' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('Y' . $numRow, $sheet->getCell('Y' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('Z' . $numRow, $sheet->getCell('Z' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('AA' . $numRow, $sheet->getCell('AA' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('AB' . $numRow, $sheet->getCell('AB' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('AC' . $numRow, $string_error)->getStyle('AC' . $numRow)->applyFromArray($styleArray);

									$numRow++;
								}

								if (($flag == 0) && ($flag2 == 0)) {

									if (is_null($value_cell_sex)) {
										$rd['sex'] = '';
									} else {
										if ($value_cell_sex == 0) {
											$rd['sex'] = 'male';
										} else {
											$rd['sex'] = 'female';
										}
									}

									if (is_null($value_cell_status)) {
										$rd['status_work'] = '';
									} else {
										if ($value_cell_status == 0) {
											$rd['status_work'] = 'Working';
										} elseif ($value_cell_status == 1) {
											$rd['status_work'] = 'Maternity leave';
										} else {
											$rd['status_work'] = 'Inactivity';

										}
									}

									if (is_null($value_cell_maries_status)) {
										$rd['marital_status'] = '';
									} else {
										if ($value_cell_sex == 0) {
											$rd['marital_status'] = 'single';
										} else {
											$rd['marital_status'] = 'married';
										}
									}

									if (is_null($value_cell_birthday) == true) {
										$rd['birthday'] = '';
									} else {
										$rd['birthday'] = $value_cell_birthday;
									}

									if (is_null($value_cell_day_identity) == true) {
										$rd['days_for_identity'] = '';
									} else {
										$rd['days_for_identity'] = $value_cell_birthday;
									}

									if (is_null($value_cell_email) == true) {
										$rd['email'] = '';
									} else {
										$rd['email'] = $value_cell_email;
									}

									if (is_null($value_cell_position) == true) {
										$rd['job_position'] = '';
									} else {
										$rd['job_position'] = $value_cell_position;
									}

									if (is_null($value_cell_workplace) == true) {
										$rd['workplace'] = '';
									} else {
										$rd['workplace'] = $value_cell_workplace;
									}

									if (is_null($value_cell_password) == true) {
										$rd['password'] = '123456a@';
									} else {
										$rd['password'] = $value_cell_password;
									}
									$rd['staff_identifi'] = $sheet->getCell('A' . $rowIndex)->getValue();
									$rd['firstname'] = $sheet->getCell('B' . $rowIndex)->getValue();
									$rd['lastname'] = $sheet->getCell('C' . $rowIndex)->getValue();
									$rd['email'] = $sheet->getCell('D' . $rowIndex)->getValue();
									$rd['sex'] = $sheet->getCell('E' . $rowIndex)->getValue();
									$rd['birthday'] = $sheet->getCell('F' . $rowIndex)->getValue();
									$rd['phonenumber'] = $sheet->getCell('G' . $rowIndex)->getValue();
									$rd['nation'] = $sheet->getCell('H' . $rowIndex)->getValue();
									$rd['religion'] = $sheet->getCell('I' . $rowIndex)->getValue();
									$rd['birthplace'] = $sheet->getCell('J' . $rowIndex)->getValue();
									$rd['home_town'] = $sheet->getCell('K' . $rowIndex)->getValue();
									$rd['resident'] = $sheet->getCell('L' . $rowIndex)->getValue();
									$rd['current_address'] = $sheet->getCell('M' . $rowIndex)->getValue();
									$rd['marital_status'] = $sheet->getCell('N' . $rowIndex)->getValue();
									$rd['identification'] = $sheet->getCell('O' . $rowIndex)->getValue();
									$rd['days_for_identity'] = $sheet->getCell('P' . $rowIndex)->getValue();
									$rd['place_of_issue'] = $sheet->getCell('Q' . $rowIndex)->getValue();
									$rd['literacy'] = $sheet->getCell('R' . $rowIndex)->getValue();
									$rd['job_position'] = $sheet->getCell('S' . $rowIndex)->getValue();
									$rd['workplace'] = $sheet->getCell('U' . $rowIndex)->getValue();
									$rd['departments'] = explode(",", $sheet->getCell('V' . $rowIndex)->getValue());
									$rd['account_number'] = $sheet->getCell('W' . $rowIndex)->getValue();
									$rd['name_account'] = $sheet->getCell('X' . $rowIndex)->getValue();
									$rd['issue_bank'] = $sheet->getCell('Y' . $rowIndex)->getValue();
									$rd['Personal_tax_code'] = $sheet->getCell('Z' . $rowIndex)->getValue();
									$rd['status_work'] = $sheet->getCell('AA' . $rowIndex)->getValue();
									$rd['password'] = $sheet->getCell('AB' . $rowIndex)->getValue();
								}

								if (get_staff_user_id() != '' && $flag == 0 && $flag2 == 0) {
									$rows[] = $rd;
									$this->hr_profile_model->add_staff($rd);
								}
								$total_rows++;
							}
						}

						$total_rows = $total_rows;
						$data['total_rows_post'] = count($rows);
						$total_row_success = count($rows);
						$total_row_false = $total_rows - (int) count($rows);
						$dataerror = $dataError;
						$message = 'Not enought rows for importing';

						if ($total_row_false != 0) {

							$objWriter = new PHPExcel_Writer_Excel2007($dataError);
							$filename = 'file_error_hr_profile' . get_staff_user_id() . '.xlsx';
							$objWriter->save($filename);

						}
						$import_result = true;
						@delete_dir($tmpDir);

					}
				} else {
					set_alert('warning', _l('import_upload_failed'));
				}
			}

		}
		echo json_encode([
			'message' => $message,
			'total_row_success' => $total_row_success,
			'total_row_false' => $total_row_false,
			'total_rows' => $total_rows,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id(),
		]);

	}
/**
 * delete staff
 */
	public function delete_staff() {
		if (!is_admin() && is_admin($this->input->post('id'))) {
			die('Busted, you can\'t delete administrators');
		}
		if (has_permission('hrm_hr_records', '', 'delete')) {
			$success = $this->hr_profile_model->delete_staff($this->input->post('id'), $this->input->post('transfer_data_to'));
			if ($success) {
				set_alert('success', _l('deleted', _l('staff_member')));
			}
		}
		redirect(admin_url('hr_profile/staff_infor'));
	}

	/**
	 * member
	 * @param  integer $id
	 * @param  integer $group
	 * @return view
	 */
	public function member($id = '', $group = '') {

		$data['staffid'] = $id;
		$data['group'] = $group;

		$data['tab'][] = 'profile';
		$data['tab'][] = 'contract';
		$data['tab'][] = 'dependent_person';
		$data['tab'][] = 'training';
		$data['tab'][] = 'staff_project';
		$data['tab'][] = 'attach';
		$data['tab'] = hooks()->apply_filters('hr_profile_tab_name', $data['tab']);

		if ($data['group'] == '') {
			$data['group'] = 'profile';
		}
		$data['hr_profile_member_add'] = false;
		if ($id == '') {
			if (!is_admin() && !has_permission('hrm_hr_records', '', 'create') && !has_permission('hrm_hr_records', '', 'edit')) {
				access_denied('staff');
			}
			$data['hr_profile_member_add'] = true;
			$title = _l('add_new', _l('staff_member_lowercase'));
		} else {
			//View own
			$staff_ids = $this->hr_profile_model->get_staff_by_manager();

			if (!in_array($id, $staff_ids) && get_staff_user_id() != $id && !is_admin() && !has_permission('hrm_hr_records', '', 'edit') && !has_permission('hrm_hr_records', '', 'view') && !has_permission('hrm_hr_records', '', 'create')) {
				access_denied('staff');
			}

			$member = $this->hr_profile_model->get_staff($id);
			if (!$member) {
				blank_page('Staff Member Not Found', 'danger');
			}
			$data['member'] = $member;
			$title = $member->firstname . ' ' . $member->lastname;

			if ($data['group'] == 'profile') {
				$data['staff_departments'] = $this->departments_model->get_staff_departments($id);
				$data['list_staff'] = $this->staff_model->get();

				$recordsreceived = $this->hr_profile_model->get_records_received($id);
				$data['records_received'] = json_decode($recordsreceived->records_received, true);
				$data['checkbox'] = [];
				if (isset($data['records_received'])) {
					foreach ($data['records_received'] as $value) {
						$data['checkbox'][$value['datakey']] = $value['value'];
					}
				}
				$data['staff_departments'] = $this->departments_model->get_staff_departments($member->staffid);
				$data['staff_avatar'] = $this->hr_profile_model->get_hr_profile_profile_file($id);
				$data['staff_cover_image'] = $this->hr_profile_model->get_hr_profile_profile_file($id);

				$data['logged_time'] = $this->staff_model->get_logged_time_data($id);
				$data['staff_p'] = $this->staff_model->get($id);
				$data['staff_departments'] = $this->departments_model->get_staff_departments($data['staff_p']->staffid);
				// notifications
				$total_notifications = total_rows(db_prefix() . 'notifications', [
					'touserid' => get_staff_user_id(),
				]);
				$data['total_pages'] = ceil($total_notifications / $this->misc_model->get_notifications_limit());

			}
			if ($data['group'] == 'dependent_person') {
				$data['dependent_person'] = $this->hr_profile_model->get_dependent_person_bytstaff($id);
			}
			if ($data['group'] == 'attach') {
				$data['hr_profile_staff'] = $this->hr_profile_model->get_hr_profile_attachments($id);
			}
			if ($data['group'] == 'staff_project') {
				$data['logged_time'] = $this->staff_model->get_logged_time_data($id);
				$data['staff_p'] = $this->staff_model->get($id);
				$data['staff_departments'] = $this->departments_model->get_staff_departments($data['staff_p']->staffid);
				// notifications
				$total_notifications = total_rows(db_prefix() . 'notifications', [
					'touserid' => get_staff_user_id(),
				]);
				$data['total_pages'] = ceil($total_notifications / $this->misc_model->get_notifications_limit());

			}

			if ($data['group'] == 'training') {

				$training_data = [];
				//Onboarding training
				$training_allocation_staff = $this->hr_profile_model->get_training_allocation_staff($id);

				if ($training_allocation_staff != null) {

					$training_data['list_training_allocation'] = get_object_vars($training_allocation_staff);
				}

				if (isset($training_allocation_staff) && $training_allocation_staff != null) {
					$training_data['training_allocation_min_point'] = 0;

					$job_position_training = $this->hr_profile_model->get_job_position_training_de($training_allocation_staff->jp_interview_training_id);

					if ($job_position_training) {
						$training_data['training_allocation_min_point'] = $job_position_training->mint_point;
					}

					if ($training_allocation_staff) {
						$training_process_id = $training_allocation_staff->training_process_id;

						$training_data['list_training'] = $this->hr_profile_model->get_list_position_training_by_id_training($training_process_id);

						//Get the latest employee's training result.
						$training_results = $this->get_mark_staff($id, $training_process_id);

						$training_data['training_program_point'] = $training_results['training_program_point'];
						$training_data['staff_training_result'] = $training_results['staff_training_result'];

						//have not done the test data
						$staff_training_result = [];
						foreach ($training_data['list_training'] as $key => $value) {
							$staff_training_result[$value['training_id']] = [
								'training_name' => $value['subject'],
								'total_point' => 0,
								'training_id' => $value['training_id'],
								'total_question' => 0,
								'total_question_point' => 0,
							];
						}

						//did the test
						if (count($training_results['staff_training_result']) > 0) {

							foreach ($training_results['staff_training_result'] as $result_key => $result_value) {
								if (isset($staff_training_result[$result_value['training_id']])) {
									unset($staff_training_result[$result_value['training_id']]);
								}
							}

							$training_data['staff_training_result'] = array_merge($training_results['staff_training_result'], $staff_training_result);

						} else {
							$training_data['staff_training_result'] = $staff_training_result;
						}

						if ((float) $training_results['training_program_point'] >= (float) $training_data['training_allocation_min_point']) {
							$training_data['complete'] = 0;
						} else {
							$training_data['complete'] = 1;
						}

					}
				}

				if (count($training_data) > 0) {
					$data['training_data'][] = $training_data;
				}

				//Additional training
				$additional_trainings = $this->hr_profile_model->get_additional_training($id);

				foreach ($additional_trainings as $key => $value) {
					$training_temp = [];

					$training_temp['training_allocation_min_point'] = $value['mint_point'];
					$training_temp['list_training_allocation'] = $value;
					$training_temp['list_training'] = $this->hr_profile_model->get_list_position_training_by_id_training($value['position_training_id']);

					//Get the latest employee's training result.
					$training_results = $this->get_mark_staff($id, $value['position_training_id']);

					$training_temp['training_program_point'] = $training_results['training_program_point'];
					$training_temp['staff_training_result'] = $training_results['staff_training_result'];

					//have not done the test data
					$staff_training_result = [];
					foreach ($training_temp['list_training'] as $key => $value) {
						$staff_training_result[$value['training_id']] = [
							'training_name' => $value['subject'],
							'total_point' => 0,
							'training_id' => $value['training_id'],
							'total_question' => 0,
							'total_question_point' => 0,
						];
					}

					//did the test
					if (count($training_results['staff_training_result']) > 0) {

						foreach ($training_results['staff_training_result'] as $result_key => $result_value) {
							if (isset($staff_training_result[$result_value['training_id']])) {
								unset($staff_training_result[$result_value['training_id']]);
							}
						}

						$training_temp['staff_training_result'] = array_merge($training_results['staff_training_result'], $staff_training_result);

					} else {
						$training_temp['staff_training_result'] = $staff_training_result;
					}

					if ((float) $training_results['training_program_point'] >= (float) $training_temp['training_allocation_min_point']) {
						$training_temp['complete'] = 0;
					} else {
						$training_temp['complete'] = 1;
					}

					if (count($training_temp) > 0) {
						$data['training_data'][] = $training_temp;
					}

				}

			}
		}
		$this->load->model('currencies_model');
		$data['positions'] = $this->hr_profile_model->get_job_position();
		$data['workplace'] = $this->hr_profile_model->get_workplace();
		$data['base_currency'] = $this->currencies_model->get_base_currency();

		$data['roles'] = $this->roles_model->get();
		$data['user_notes'] = $this->misc_model->get_notes($id, 'staff');
		$data['departments'] = $this->departments_model->get();
		$data['title'] = $title;

		$data['contract_type'] = $this->hr_profile_model->get_contracttype();
		$data['staff'] = $this->staff_model->get();
		$data['allowance_type'] = $this->hr_profile_model->get_allowance_type();
		$data['salary_form'] = $this->hr_profile_model->get_salary_form();
		$data['list_staff'] = $this->staff_model->get();

		$data['tabs']['view'] = 'hr_record/includes/' . $data['group'];

		$data['tabs']['view'] = hooks()->apply_filters('hr_profile_tab_content', $data['tabs']['view']);

		$this->load->view('hr_record/member', $data);
	}

	/**
	 * table education position
	 */
	public function table_education_position() {
		$this->app->get_table_data(module_views_path('hr_profile', 'hr_record/table_education_by_position'));
	}

	/**
	 * table education
	 */
	public function table_education($staff_id = '') {
		$this->app->get_table_data(module_views_path('hr_profile', 'hr_record/table_education'), ['staff_id' => $staff_id]);
	}

	/**
	 * save update education
	 * @return json
	 */
	public function save_update_education() {
		if ($this->input->post()) {
			$data = $this->input->post();
			$data['training_time_from'] = to_sql_date($data['training_time_from'], true);
			$data['training_time_to'] = to_sql_date($data['training_time_to'], true);
			$data['admin_id'] = get_staff_user_id();
			$data['programe_id'] = '';
			$data['date_create'] = date('Y-m-d');
			if ($data['id'] == '') {
				$success = $this->hr_profile_model->add_education($data);
				$message = _l('added_successfully', _l('hr_education'));
				$message_f = _l('hr_added_failed', _l('hr_education'));
				if ($success) {
					echo json_encode([
						'success' => true,
						'message' => $message,
					]);
				} else {
					echo json_encode([
						'success' => false,
						'message' => $message_f,
					]);
				}
			} else {
				$success = $this->hr_profile_model->update_education($data);
				$message = _l('updated_successfully', _l('hr_education'));
				$message_f = _l('hr_update_failed', _l('hr_education'));
				if ($success) {
					echo json_encode([
						'success' => true,
						'message' => $message,
					]);
				} else {
					echo json_encode([
						'success' => false,
						'message' => $message_f,
					]);
				}
			}
		}

		die;
	}

/**
 * delete education
 * @return json
 */
	public function delete_education() {
		if ($this->input->post()) {
			$data = $this->input->post();
			$success = $this->hr_profile_model->delete_education($data['id']);
			if ($success == true) {
				$message = _l('hr_deleted');
				echo json_encode([
					'success' => true,
					'message' => $message,
				]);
			} else {
				$message = _l('problem_deleting');
				echo json_encode([
					'success' => true,
					'message' => $message,
				]);
			}
		}
	}
/**
 * table reception
 */
	public function table_reception() {
		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data(module_views_path('hr_profile', 'includes/reception_table'));
		}
	}
/**
 * general bonus
 * @param  integer $id
 * @return json
 */
	public function general_bonus($id) {
		$select = [
			db_prefix() . 'bonus_discipline_detail.id',
			db_prefix() . 'bonus_discipline_detail.id',
			db_prefix() . 'bonus_discipline_detail.id',
			db_prefix() . 'bonus_discipline_detail.id',
			db_prefix() . 'bonus_discipline_detail.id',
		];
		$where = [' where staff_id = ' . $id . ' and type = 1 and status = 2'];
		$aColumns = $select;
		$sIndexColumn = 'id';
		$sTable = db_prefix() . 'bonus_discipline_detail';
		$join = [' LEFT JOIN ' . db_prefix() . 'bonus_discipline ON ' . db_prefix() . 'bonus_discipline.id = ' . db_prefix() . 'bonus_discipline_detail.id_bonus_discipline'];

		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
			db_prefix() . 'bonus_discipline_detail.id',
			db_prefix() . 'bonus_discipline_detail.from_time',
			'staff_id',
			'apply_for',
			db_prefix() . 'bonus_discipline_detail.lever_bonus',
			db_prefix() . 'bonus_discipline.name',
			db_prefix() . 'bonus_discipline.type',
			db_prefix() . 'bonus_discipline.id_criteria',
			db_prefix() . 'bonus_discipline_detail.formality',
			db_prefix() . 'bonus_discipline_detail.formality_value',
			db_prefix() . 'bonus_discipline_detail.description',
		]);

		$output = $result['output'];
		$rResult = $result['rResult'];
		foreach ($rResult as $aRow) {
			$row = [];
			$row[] = $aRow['name'];
			$criterial = '';
			$list_criteria = json_decode($aRow['id_criteria']);
			if ($list_criteria) {
				foreach ($list_criteria as $key => $criteria) {
					$criterial = '<span class="badge inline-block project-status" class="bg-white text-dark"> ' . $this->hr_profile_model->get_criteria($criteria)->kpi_name . ' </span>  ';
				}
			}

			$row[] = $criterial;
			$row[] = _l($aRow['from_time']);
			$formality = '';
			$value_formality = '';
			if (isset($aRow['formality'])) {
				if ($aRow['formality'] == 'bonus_money') {
					$formality = _l('bonus_money');
					$value_formality = app_format_money($aRow['formality_value'], '');
				}
				if ($aRow['formality'] == 'indemnify') {
					$formality = _l('indemnify');
					$t = explode(',', $aRow['formality_value']);
					$value_formality = _l('amount_of_damage') . ': ' . app_format_money((int) $t[0], '') . '<br>' . _l('indemnify') . ': ' . app_format_money((int) $t[1], '');
				}
				if ($aRow['formality'] == 'commend') {
					$formality = _l('commend');
				}
				if ($aRow['formality'] == 'remind') {
					$formality = _l('remind');
				}
			}
			$row[] = $formality;
			$row[] = $value_formality;

			$output['aaData'][] = $row;
		}
		echo json_encode($output);
		die();
	}
/**
 * general discipline
 * @param  integer $id
 * @return json
 */
	public function general_discipline($id) {
		$select = [
			db_prefix() . 'bonus_discipline_detail.id',
			db_prefix() . 'bonus_discipline_detail.id',
			db_prefix() . 'bonus_discipline_detail.id',

			db_prefix() . 'bonus_discipline_detail.id',
			db_prefix() . 'bonus_discipline_detail.id',
		];
		$where = [' where staff_id = ' . $id . ' and type = 2 and status = 2'];
		$aColumns = $select;
		$sIndexColumn = 'id';
		$sTable = db_prefix() . 'bonus_discipline_detail';
		$join = [' LEFT JOIN ' . db_prefix() . 'bonus_discipline ON ' . db_prefix() . 'bonus_discipline.id = ' . db_prefix() . 'bonus_discipline_detail.id_bonus_discipline'];

		$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
			db_prefix() . 'bonus_discipline_detail.id',
			db_prefix() . 'bonus_discipline_detail.from_time',
			'staff_id',
			'apply_for',
			db_prefix() . 'bonus_discipline_detail.lever_bonus',
			db_prefix() . 'bonus_discipline.name',
			db_prefix() . 'bonus_discipline.type',
			db_prefix() . 'bonus_discipline.id_criteria',
			db_prefix() . 'bonus_discipline_detail.formality',
			db_prefix() . 'bonus_discipline_detail.formality_value',
			db_prefix() . 'bonus_discipline_detail.description',
		]);

		$output = $result['output'];
		$rResult = $result['rResult'];
		foreach ($rResult as $aRow) {
			$row = [];
			$row[] = $aRow['name'];
			$criterial = '';
			$list_criteria = json_decode($aRow['id_criteria']);
			if ($list_criteria) {
				foreach ($list_criteria as $key => $criteria) {
					$criterial = '<span class="badge inline-block project-status" class="bg-white text-dark"> ' . $this->hr_profile_model->get_criteria($criteria)->kpi_name . ' </span>  ';
				}
			}
			$row[] = $criterial;
			$row[] = _l($aRow['from_time']);
			$formality = '';
			$value_formality = '';
			if (isset($aRow['formality'])) {
				if ($aRow['formality'] == 'bonus_money') {
					$formality = _l('bonus_money');
					$value_formality = app_format_money($aRow['formality_value'], '') . 'đ';
				}
				if ($aRow['formality'] == 'indemnify') {
					$formality = _l('indemnify');
					$t = explode(',', $aRow['formality_value']);
					$value_formality = _l('amount_of_damage') . ': ' . app_format_money((int) $t[0], '') . 'đ<br>' . _l('indemnify') . ': ' . app_format_money((int) $t[1], '');
				}
				if ($aRow['formality'] == 'commend') {
					$formality = _l('commend');
				}
				if ($aRow['formality'] == 'remind') {
					$formality = _l('remind');
				}
			}
			$row[] = $formality;
			$row[] = $value_formality;

			$output['aaData'][] = $row;
		}
		echo json_encode($output);
		die();
	}
/**
 * records received
 * @return json
 */
	public function records_received() {
		if ($this->input->is_ajax_request()) {
			if ($this->input->post() != null) {
				$data = $this->input->post();
				$data1 = $data['dt_record'];
				$this->db->set('records_received', $data1);
				$this->db->where('staffid', $data['staffid']);
				$this->db->update(db_prefix() . 'staff');
				$affected_rows = $this->db->affected_rows();
				if ($affected_rows > 0) {
					$message = 'Add records received success';
				} else {
					$message = 'Add records received false';
				}
				echo json_encode([
					'message' => $message,
				]);
			}
		}
	}

	/**
	 * upload file
	 * @return json
	 */
	public function upload_file() {
		$staffid = $this->input->post('staffid');
		$files = handle_hr_profile_attachments_array($staffid, 'file');
		$success = false;
		$count_id = 0;
		$message = '';

		if ($files) {
			$i = 0;
			$len = count($files);
			foreach ($files as $file) {
				$insert_id = $this->hr_profile_model->add_attachment_to_database($staffid, 'hr_staff_file', [$file], false);
				if ($insert_id > 0) {
					$count_id++;
				}
				$i++;
			}
			if ($insert_id == $i) {
				$message = 'Upload file success';
			}
		}

		$hr_profile_staff = $this->hr_profile_model->get_hr_profile_attachments($staffid);
		$data = '';
		foreach ($hr_profile_staff as $key => $attachment) {
			$href_url = site_url('modules/hr_profile/uploads/att_file/' . $attachment['rel_id'] . '/' . $attachment['file_name']) . '" download';
			if (!empty($attachment['external'])) {
				$href_url = $attachment['external_link'];
			}
			$data .= '<div class="display-block contract-attachment-wrapper">';
			$data .= '<div class="col-md-10">';
			$data .= '<div class="col-md-1 mr-5">';
			$data .= '<a name="preview-btn" onclick="preview_file_staff(this); return false;" rel_id = "' . $attachment['rel_id'] . '" id = "' . $attachment['id'] . '" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="' . _l("preview_file") . '">';
			$data .= '<i class="fa fa-eye"></i>';
			$data .= '</a>';
			$data .= '</div>';
			$data .= '<div class=col-md-9>';
			$data .= '<div class="pull-left"><i class="' . get_mime_class($attachment['filetype']) . '"></i></div>';
			$data .= '<a href="' . $href_url . '>' . $attachment['file_name'] . '</a>';
			$data .= '<p class="text-muted">' . $attachment["filetype"] . '</p>';
			$data .= '</div>';
			$data .= '</div>';
			$data .= '<div class="col-md-2 text-right">';
			if ($attachment['staffid'] == get_staff_user_id() || is_admin() || has_permission('hrm_hr_records', '', 'edit')) {
				$data .= '<a href="#" class="text-danger" onclick="delete_hr_att_file_attachment(this,' . $attachment['id'] . '); return false;"><i class="fa fa fa-times"></i></a>';
			}
			$data .= '</div>';
			$data .= '<div class="clearfix"></div><hr/>';
			$data .= '</div>';
		}

		echo json_encode([
			'message' => _l('hr_attach_file_successfully'),
			'data' => $data,
		]);
	}

	/**
	 * hr profile file
	 * @param  integer $id
	 * @param  string $rel_id
	 */
	public function hr_profile_file($id, $rel_id) {
		$data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
		$data['current_user_is_admin'] = is_admin();
		$data['file'] = $this->hr_profile_model->get_file($id, $rel_id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		$this->load->view('hr_profile/includes/_file', $data);
	}

	/**
	 * delete hr profile staff attachment
	 * @param  integer $attachment_id
	 * @return json
	 */
	public function delete_hr_profile_staff_attachment($attachment_id) {
		$file = $this->misc_model->get_file($attachment_id);
		if ($file->staffid == get_staff_user_id() || is_admin() || has_permission('hrm_hr_records', '', 'edit')) {
			$result = $this->hr_profile_model->delete_hr_profile_staff_attachment($attachment_id);

			if ($result) {
				$status = true;
				$message = _l('hr_deleted');
			} else {
				$message = _l('problem_deleting');
				$status = false;

			}
			echo json_encode([
				'success' => $status,
				'message' => $message,
			]);
		} else {
			access_denied('hr_profile');
		}
	}

/**
 * update staff permission
 */
	public function update_staff_permission() {
		$data = $this->input->post();
		if ($data['id'] != '') {
			if (!$data['id'] == get_staff_user_id() && !is_admin() && !hr_profile_permissions('hr_profile', '', 'edit')) {
				access_denied('hr_profile');
			}
			$response = $this->hr_profile_model->update_staff_permissions($data);
			if ($response == true) {
				set_alert('success', _l('updated_successfully', _l('staff_member')));
			} else {
				set_alert('danger', _l('updated_failed', _l('staff_member')));
			}
		}
		redirect(admin_url('hr_profile/member/' . $data['id'] . '/permission'));
	}
/**
 * update staff profile
 */
	public function update_staff_profile() {
		$data = $this->input->post();
		if ($data['id'] == '') {
			unset($data['id']);
			if (!has_permission('hrm_hr_records', '', 'create') && !has_permission('hrm_hr_records', '', 'edit') && !is_admin()) {
				access_denied('member');
			}
			$id = $this->hr_profile_model->add_staff($data);
			if ($id) {
				hr_profile_handle_staff_profile_image_upload($id);
				set_alert('success', _l('added_successfully', _l('staff_member')));
				redirect(admin_url('hr_profile/member/' . $id . '/profile'));
			}
		} else {
			if (!$data['id'] == get_staff_user_id() && !is_admin() && !hr_profile_permissions('hr_profile', '', 'edit')) {
				access_denied('hr_profile');
			}
			$response = $this->hr_profile_model->update_staff_profile($data);
			if ($response == true) {
				hr_profile_handle_staff_profile_image_upload($data['id']);
			}
			if (is_array($response)) {
				if (isset($response['cant_remove_main_admin'])) {
					set_alert('warning', _l('staff_cant_remove_main_admin'));
				} elseif (isset($response['cant_remove_yourself_from_admin'])) {
					set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
				}
			} elseif ($response == true) {
				set_alert('success', _l('updated_successfully', _l('staff_member')));
			}
			redirect(admin_url('hr_profile/member/' . $data['id'] . '/profile'));
		}
	}

	/**
	 * add update staff bonus discipline
	 */
	public function add_update_staff_bonus_discipline() {
		if ($this->input->post()) {
			$data = $this->input->post();
			$this->hr_profile_model->update_bonus_discipline($data['id_detail'], $data);
			$message = _l('hr_updated_successfully');
			set_alert('success', $message);
			redirect(admin_url('hr_profile/view_bonus_discipline/' . $data['id']));
		}
	}
	/**
	 * file view bonus discipline
	 * @param  integer $id
	 * @return view
	 */
	public function file_view_bonus_discipline($id) {
		$data['rel_id'] = $id;
		$data['file'] = $this->hr_profile_model->get_file_info($id, 'bonus_discipline');
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		$this->load->view('_file_bonus_discipline', $data);
	}

	/**
	 * workplace
	 * @param  string $id
	 * @return [type]
	 */
	public function workplace($id = '') {

		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();

			if (!$this->input->post('id')) {
				$id = $this->hr_profile_model->add_workplace($data);

				if ($id) {
					$message = _l('added_successfully', _l('workplace'));
					set_alert('success', $message);
				} else {
					$message = _l('added_failed', _l('workplace'));
					set_alert('warning', $message);
				}

				redirect(admin_url('hr_profile/setting?group=workplace'));
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->hr_profile_model->update_workplace($data, $id);

				if ($success) {
					$message = _l('updated_successfully', _l('workplace'));
					set_alert('success', $message);

				} else {
					$message = _l('update_failed', _l('workplace'));
					set_alert('warning', $message);
				}

				redirect(admin_url('hr_profile/setting?group=workplace'));
			}

		}
	}

	/**
	 * delete workplace
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_workplace($id) {
		if (!$id) {
			redirect(admin_url('hr_profile/setting?group=workplace'));
		}
		$response = $this->hr_profile_model->delete_workplace($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('hr_is_referenced', _l('workplace')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('workplace')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('workplace')));
		}
		redirect(admin_url('hr_profile/setting?group=workplace'));
	}

	public function hr_profile_permission_table() {
		if ($this->input->is_ajax_request()) {

			$select = [
				'staffid',
				'CONCAT(firstname," ",lastname) as full_name',
				'firstname', //for role name
				'email',
				'phonenumber',
			];
			$where = [];
			$where[] = 'AND ' . db_prefix() . 'staff.admin != 1';

			$arr_staff_id = hr_profile_get_staff_id_hr_permissions();

			if (count($arr_staff_id) > 0) {
				$where[] = 'AND ' . db_prefix() . 'staff.staffid IN (' . implode(', ', $arr_staff_id) . ')';
			} else {
				$where[] = 'AND ' . db_prefix() . 'staff.staffid IN ("")';
			}

			$aColumns = $select;
			$sIndexColumn = 'staffid';
			$sTable = db_prefix() . 'staff';
			$join = ['LEFT JOIN ' . db_prefix() . 'roles ON ' . db_prefix() . 'roles.roleid = ' . db_prefix() . 'staff.role'];

			$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'roles.name as role_name', db_prefix() . 'staff.role']);

			$output = $result['output'];
			$rResult = $result['rResult'];

			$not_hide = '';

			foreach ($rResult as $aRow) {
				$row = [];

				$row[] = '<a href="' . admin_url('staff/member/' . $aRow['staffid']) . '">' . $aRow['full_name'] . '</a>';

				$row[] = $aRow['role_name'];
				$row[] = $aRow['email'];
				$row[] = $aRow['phonenumber'];

				$options = '';

				if (has_permission('hrm_setting', '', 'edit')) {
					$options = icon_btn('#', 'edit', 'btn-default', [
						'title' => _l('hr_edit'),
						'onclick' => 'hr_profile_permissions_update(' . $aRow['staffid'] . ', ' . $aRow['role'] . ', ' . $not_hide . '); return false;',
					]);
				}

				if (has_permission('hrm_setting', '', 'delete')) {
					$options .= icon_btn('hr_profile/delete_hr_profile_permission/' . $aRow['staffid'], 'remove', 'btn-danger _delete', ['title' => _l('delete')]);
				}

				$row[] = $options;

				$output['aaData'][] = $row;
			}

			echo json_encode($output);
			die();
		}
	}

	/**
	 * permission modal
	 * @return [type]
	 */
	public function permission_modal() {
		if (!$this->input->is_ajax_request()) {
			show_404();
		}
		$this->load->model('staff_model');

		if ($this->input->post('slug') === 'update') {
			$staff_id = $this->input->post('staff_id');
			$role_id = $this->input->post('role_id');

			$data = ['funcData' => ['staff_id' => isset($staff_id) ? $staff_id : null]];

			if (isset($staff_id)) {
				$data['member'] = $this->staff_model->get($staff_id);
			}

			$data['roles_value'] = $this->roles_model->get();
			$data['staffs'] = hr_profile_get_staff_id_dont_permissions();
			$add_new = $this->input->post('add_new');

			if ($add_new == ' hide') {
				$data['add_new'] = ' hide';
				$data['display_staff'] = '';
			} else {
				$data['add_new'] = '';
				$data['display_staff'] = ' hide';
			}

			$this->load->view('includes/permissions', $data);
		}
	}

	/**
	 * hr profile update permissions
	 * @param  string $id
	 * @return [type]
	 */
	public function hr_profile_update_permissions($id = '') {
		if (!is_admin()) {
			access_denied('hr_profile');
		}
		$data = $this->input->post();

		if (!isset($id) || $id == '') {
			$id = $data['staff_id'];
		}

		if (isset($id) && $id != '') {

			$data = hooks()->apply_filters('before_update_staff_member', $data, $id);

			if (is_admin()) {
				if (isset($data['administrator'])) {
					$data['admin'] = 1;
					unset($data['administrator']);
				} else {
					if ($id != get_staff_user_id()) {
						if ($id == 1) {
							return [
								'cant_remove_main_admin' => true,
							];
						}
					} else {
						return [
							'cant_remove_yourself_from_admin' => true,
						];
					}
					$data['admin'] = 0;
				}
			}

			$this->db->where('staffid', $id);
			$this->db->update(db_prefix() . 'staff', [
				'role' => $data['role'],
			]);

			$response = $this->staff_model->update_permissions((isset($data['admin']) && $data['admin'] == 1 ? [] : $data['permissions']), $id);
		} else {
			$this->load->model('roles_model');

			$role_id = $data['role'];
			unset($data['role']);
			unset($data['staff_id']);

			$data['update_staff_permissions'] = true;

			$response = $this->roles_model->update($data, $role_id);
		}

		if (is_array($response)) {
			if (isset($response['cant_remove_main_admin'])) {
				set_alert('warning', _l('staff_cant_remove_main_admin'));
			} elseif (isset($response['cant_remove_yourself_from_admin'])) {
				set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
			}
		} elseif ($response == true) {
			set_alert('success', _l('updated_successfully', _l('staff_member')));
		}
		redirect(admin_url('hr_profile/setting?group=hr_profile_permissions'));

	}

	/**
	 * staff id changed
	 * @param  [type] $staff_id
	 * @return [type]
	 */
	public function staff_id_changed($staff_id) {
		$role_id = '';
		$status = 'false';
		$r_permission = [];

		$staff = $this->staff_model->get($staff_id);

		if ($staff) {
			if (count($staff->permissions) > 0) {
				foreach ($staff->permissions as $permission) {
					$r_permission[$permission['feature']][] = $permission['capability'];
				}
			}

			$role_id = $staff->role;
			$status = 'true';

		}

		if (count($r_permission) > 0) {
			$data = ['role_id' => $role_id, 'status' => $status, 'permission' => 'true', 'r_permission' => $r_permission];
		} else {
			$data = ['role_id' => $role_id, 'status' => $status, 'permission' => 'false', 'r_permission' => $r_permission];
		}

		echo json_encode($data);
		die;
	}

	/**
	 * delete hr profile permission
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_hr_profile_permission($id) {
		if (!is_admin()) {
			access_denied('hr_profile');
		}

		$response = $this->hr_profile_model->delete_hr_profile_permission($id);

		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('hr_is_referenced', _l('department_lowercase')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('hr_department')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('department_lowercase')));
		}
		redirect(admin_url('hr_profile/setting?group=hr_profile_permissions'));

	}

	/**
	 * zen unit chart
	 * @param  [type] $department
	 * @return [type]
	 */
	public function zen_unit_chart($department) {
		$this->load->model('staff_model');
		$dpm = $this->departments_model->get($department);
		$dpm_data = $this->hr_profile_model->get_data_dpm_chart($department);
		$reality_now = $this->hr_profile_model->count_reality_now($department);

		$list_job = $this->hr_profile_model->list_job_department($department);

		$html = '<table class="table table-striped table-bordered text-nowrap dataTable no-footer dtr-inline collapsed"  ><tbody>';
		$html .= '<tr class="text-white">
						<th>' . _l('position') . '</th>
						<th>' . _l('hr_now') . '</th>
						<th>' . _l('hrplanning') . '</th>
					</tr>';

		$li_jobid = [];

		if (count($list_job) > 0) {
			foreach ($list_job as $lj) {
				if ($lj != '') {
					if (!in_array($lj, $li_jobid)) {
						$html .= '<tr class="text-white">
								<td class="text-left">' . job_name_by_id($lj) . '</td>
								<td>' . count_staff_job_unnit($department, $lj) . '</td>
								<td>' . count_staff_job_unnit($department, $lj) . '</td>
							</tr>';
					}
				}

			}
		}

		$html .= '</tbody></table>';

		echo json_encode([
			'dpm_name' => $dpm->name,
			'data' => $dpm_data,
			'reality_now' => $reality_now,
			'html' => $html,
		]);

	}

	/**
	 * get list job position training
	 * @param  [type] $id
	 * @return [type]
	 */
	public function get_list_job_position_training($id) {
		$list = $this->hr_profile_model->get_job_position_training_de($id);
		if (isset($list)) {
			$description = $list->description;
		} else {
			$description = '';

		}
		echo json_encode([
			'description' => $description,

		]);
	}

	/**
	 * delete job position training process
	 * @param  [type] $training_id
	 * @return [type]
	 */
	public function delete_job_position_training_process($training_id) {
		if (!has_permission('staffmanage_job_position', '', 'delete')) {
			access_denied('job_position');
		}

		if (!$training_id) {
			redirect(admin_url('hr_profile/training/?group=training_program'));
		}
		$success = $this->hr_profile_model->delete_job_position_training_process($training_id);
		if ($success) {
			set_alert('success', _l('hr_deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('hr_profile/training/?group=training_program'));
	}

	/**
	 * delete position training
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_position_training($id) {
		if (!has_permission('staffmanage_job_position', '', 'delete')) {
			access_denied('job_position');
		}
		if (!$id) {
			redirect(admin_url('hr_profile/training'));
		}
		$success = $this->hr_profile_model->delete_position_training($id);
		if ($success) {
			set_alert('success', _l('hr_deleted'));
		} else {
			set_alert('warning', _l('problem_deleting'));
		}
		redirect(admin_url('hr_profile/training'));
	}

	/**
	 * table contract
	 * @return [type]
	 */
	public function table_contract() {
		$this->app->get_table_data(module_views_path('hr_profile', 'contracts/table_contract'));
	}

	/**
	 * contracts
	 * @param  string $id
	 * @return [type]
	 */
	public function contracts($id = '') {
		$this->load->model('staff_model');

		if (!has_permission('hrm_contract', '', 'view') && !has_permission('hrm_contract', '', 'view_own') && !is_admin()) {
			access_denied('staff_contract');
		}

		//filter from dasboard
		$data_get = $this->input->get();
		if (isset($data_get['to_expire'])) {
			$data['to_expire'] = true;
		}

		if (isset($data_get['overdue_contract'])) {
			$data['overdue_contract'] = true;
		}

		$data['hrmcontractid'] = $id;
		$data['positions'] = $this->hr_profile_model->get_job_position();
		$data['workplace'] = $this->hr_profile_model->get_workplace();
		$data['contract_type'] = $this->hr_profile_model->get_contracttype();
		$data['staff'] = $this->staff_model->get();
		$data['allowance_type'] = $this->hr_profile_model->get_allowance_type();
		$data['salary_form'] = $this->hr_profile_model->get_salary_form();
		$data['duration'] = $this->hr_profile_model->get_duration();
		$data['contract_attachment'] = $this->hr_profile_model->get_hrm_attachments_file($id, 'hr_contract');
		$data['dep_tree'] = json_encode($this->hr_profile_model->get_department_tree());

		$data['title'] = _l('hr_hr_contracts');
		$this->load->view('contracts/manage_contract', $data);
	}

	/**
	 * contract
	 * @param  string $id
	 * @return [type]
	 */
	public function contract($id = '') {
		if (!has_permission('hrm_contract', '', 'view') && !has_permission('hrm_contract', '', 'view_own') && !is_admin()) {
			access_denied('staff_contract');
		}

		if ($this->input->post()) {
			$data = $this->input->post();
			$count_file = 0;
			if ($id == '') {
				if (!has_permission('hrm_contract', '', 'create') && !is_admin()) {
					access_denied('staff_contract');
				}
				$id = $this->hr_profile_model->add_contract($data);

				//upload file
				if ($id) {
					$success = true;
					$_id = $id;
					$message = _l('added_successfully', _l('contract_attachment'));
					$uploadedFiles = hr_profile_handle_contract_attachments_array($id, 'file');

					if ($uploadedFiles && is_array($uploadedFiles)) {
						foreach ($uploadedFiles as $file) {

							$insert_file_id = $this->hr_profile_model->add_attachment_to_database($id, 'hr_contract', [$file]);
						}
					}
				}

				if ($id) {
					set_alert('success', _l('added_successfully', _l('contract')));
					redirect(admin_url('hr_profile/contracts/' . $id));
				}

			} else {
				if (!has_permission('hrm_contract', '', 'edit') && !is_admin()) {
					access_denied('staff_contract');
				}

				$response = $this->hr_profile_model->update_contract($data, $id);
				//upload file
				if ($id) {
					$success = true;
					$_id = $id;
					$message = _l('added_successfully', _l('contract_attachment'));
					$uploadedFiles = hr_profile_handle_contract_attachments_array($id, 'file');
					if ($uploadedFiles && is_array($uploadedFiles)) {
						$len = count($uploadedFiles);

						foreach ($uploadedFiles as $file) {
							$insert_file_id = $this->hr_profile_model->add_attachment_to_database($id, 'hr_contract', [$file]);
							if ($insert_file_id > 0) {
								$count_file++;
							}
						}
						if ($count_file == $len) {
							$response = true;
						}
					}
				}

				if (is_array($response)) {
					if (isset($response['cant_remove_main_admin'])) {
						set_alert('warning', _l('staff_cant_remove_main_admin'));
					} elseif (isset($response['cant_remove_yourself_from_admin'])) {
						set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
					}
				} elseif ($response == true) {
					set_alert('success', _l('updated_successfully', _l('contract')));
				}
				redirect(admin_url('hr_profile/contracts/' . $id));
			}
		}

		if ($id == '') {
			$title = _l('add_new', _l('contract'));
			$data['title'] = $title;
			$data['staff_contract_code'] = $this->hr_profile_model->create_code('staff_contract_code');
		} else {

			$contract = $this->hr_profile_model->get_contract($id);

			//load deparment by manager
			if (!is_admin() && !has_permission('hrm_contract', '', 'view')) {
				//View own
				if ($contract) {
					$staff_ids = $this->hr_profile_model->get_staff_by_manager();
					if (count($staff_ids) > 0) {
						if (!in_array($contract->staff, $staff_ids)) {
							access_denied('staff_contract');
						}
					} else {
						access_denied('staff_contract');
					}
				}

			}

			$contract_detail = $this->hr_profile_model->get_contract_detail($id);
			$data['contract_attachment'] = $this->hr_profile_model->get_hrm_attachments_file($id, 'hr_contract');
			if (!$contract) {
				blank_page('Contract Not Found', 'danger');
			}

			$data['contracts'] = $contract;
			if ($contract) {
				$data['staff_delegate_role'] = $this->hr_profile_model->get_staff_role($contract->staff_delegate);
			}

			$data['contract_details'] = json_encode($contract_detail);
			if ($contract) {
				$title = $this->hr_profile_model->get_contracttype_by_id($contract->name_contract);
				if (isset($title[0]['name_contracttype'])) {
					$data['title'] = $title[0]['name_contracttype'];
				}
			}

		}

		$data['positions'] = $this->hr_profile_model->get_job_position();
		$data['workplace'] = $this->hr_profile_model->get_workplace();
		$data['contract_type'] = $this->hr_profile_model->get_contracttype();
		$data['staff'] = $this->hr_profile_model->get_staff_active();
		$data['allowance_type'] = $this->hr_profile_model->get_allowance_type();
		$data['salary_allowance_type'] = $this->hr_profile_model->get_salary_allowance_handsontable();
		$types = [];
		$types[] = [
			'id' => 'salary',
			'label' => _l('salary'),
		];
		$types[] = [
			'id' => 'allowance',
			'label' => _l('allowance'),
		];

		$data['types'] = $types;

		$this->load->view('hr_profile/contracts/contract', $data);
	}

	/**
	 * delete contract
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_contract($id) {
		if (!has_permission('hrm_contract', '', 'delete') && !is_admin()) {
			access_denied('staff_contract');
		}

		if (!$id) {
			redirect(admin_url('hr_profile/contracts'));
		}

		$response = $this->hr_profile_model->delete_contract($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('hr_is_referenced', _l('contract')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('contract')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('contract')));
		}
		redirect(admin_url('hr_profile/contracts'));

	}

	/**
	 * contract code exists
	 * @return [type]
	 */
	public function contract_code_exists() {
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				// First we need to check if the email is the same
				$contractid = $this->input->post('contractid');

				if ($contractid != '') {

					$staff_contract = $this->hr_profile_model->get_contract($contractid);
					if ($staff_contract->contract_code == $this->input->post('contract_code')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('contract_code', $this->input->post('contract_code'));
				$total_rows = $this->db->count_all_results(db_prefix() . 'hr_staff_contract');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

	/**
	 * get hrm contract data ajax
	 * @param  [type] $id
	 * @return [type]
	 */
	public function get_hrm_contract_data_ajax($id) {
		$contract = $this->hr_profile_model->get_contract($id);
		$contract_detail = $this->hr_profile_model->get_contract_detail($id);
		if (!$contract) {
			blank_page('Contract Not Found', 'danger');
		}

		$data['contracts'] = $contract;
		if ($contract) {
			$data['staff_delegate_role'] = $this->hr_profile_model->get_staff_role($contract->staff_delegate);
			$title = $this->hr_profile_model->get_contracttype_by_id($contract->name_contract);
			if ($title) {
				$data['title'] = $title[0]['name_contracttype'];
			} else {
				$data['title'] = '';
			}

			//check update content from contract template (in case old data)
			if (strlen($contract->content) == 0) {

				$this->hr_profile_model->update_hr_staff_contract_content($id, $contract->staff);
			}

		}

		$data['contract_details'] = $contract_detail;
		$data['positions'] = $this->hr_profile_model->get_job_position();
		$data['workplace'] = $this->hr_profile_model->get_workplace();
		$data['contract_type'] = $this->hr_profile_model->get_contracttype();
		$data['staff'] = $this->staff_model->get();
		$data['allowance_type'] = $this->hr_profile_model->get_allowance_type();
		$data['salary_form'] = $this->hr_profile_model->get_salary_form();
		$data['contract_attachment'] = $this->hr_profile_model->get_hrm_attachments_file($id, 'hr_contract');

		$data['contract_merge_fields'] = $this->app_merge_fields->get_flat('hr_contract', ['other'], '{email_signature}');

		$this->load->view('hr_profile/contracts/contract_preview_template', $data);
	}

	/**
	 * get staff role
	 * @return [type]
	 */
	public function get_staff_role() {
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {

				$id = $this->input->post('id');
				$name_object = $this->db->query('select r.name from ' . db_prefix() . 'staff as s join ' . db_prefix() . 'roles as r on s.role = r.roleid where s.staffid = ' . $id)->row();
			}
		}
		if ($name_object) {
			echo json_encode([
				'name' => $name_object->name,
			]);
		}

	}

	/**
	 * get contract type
	 * @param  string $id
	 * @return [type]
	 */
	public function get_contract_type($id = '') {
		$contract_type = $this->hr_profile_model->get_contracttype($id);

		echo json_encode([
			'contract_type' => $contract_type,
		]);
		die;

	}

	/**
	 * inventory setting
	 * @return [type]
	 */
	public function prefix_number() {
		$data = $this->input->post();

		if ($data) {

			$success = $this->hr_profile_model->update_prefix_number($data);

			if ($success == true) {

				$message = _l('hr_updated_successfully');
				set_alert('success', $message);
			}

			redirect(admin_url('hr_profile/setting?group=prefix_number'));

		}
	}

	/**
	 * get code
	 * @param  String $rel_type
	 * @return String
	 */
	public function get_code($rel_type) {
		//get data
		$code = $this->hr_profile_model->create_code($rel_type);

		echo json_encode([
			'code' => $code,
		]);
		die;

	}

	/**
	 * import job position
	 * @return [type]
	 */
	public function import_job_position() {
		$data['departments'] = $this->departments_model->get();
		$data['job_positions'] = $this->hr_profile_model->get_job_position();

		$data_staff = $this->hr_profile_model->get_staff(get_staff_user_id());

		/*get language active*/
		if ($data_staff) {
			if ($data_staff->default_language != '') {
				$data['active_language'] = $data_staff->default_language;

			} else {

				$data['active_language'] = get_option('active_language');
			}

		} else {
			$data['active_language'] = get_option('active_language');
		}

		$this->load->view('hr_profile/job_position_manage/position_manage/import_position', $data);
	}

	/**
	 * dependent person
	 * @param  string $id
	 * @return [type]
	 */
	public function dependent_person($id = '') {
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();

			if ($this->input->post('id') == null) {
				$manage = $this->input->post('manage');
				unset($data['manage']);

				$id = $this->hr_profile_model->add_dependent_person($data);
				if ($id) {
					$success = true;
					$message = _l('added_successfully', _l('hr_dependent_persons'));
					set_alert('success', $message);
				} else {
					$message = _l('added_failed', _l('hr_dependent_persons'));
					set_alert('warning', $message);
				}

				if ($manage) {
					redirect(admin_url('hr_profile/dependent_persons'));
				} else {
					redirect(admin_url('hr_profile/member/' . get_staff_user_id() . '/dependent_person'));
				}
			} else {
				$manage = $this->input->post('manage');
				$id = $data['id'];
				unset($data['id']);
				unset($data['manage']);
				$success = $this->hr_profile_model->update_dependent_person($data, $id);

				if ($success) {
					$message = _l('updated_successfully', _l('hr_dependent_persons'));
					set_alert('success', $message);
				} else {
					$message = _l('updated_failed', _l('hr_dependent_persons'));
					set_alert('warning', $message);
				}

				if ($manage) {
					redirect(admin_url('hr_profile/dependent_persons'));
				} else {
					redirect(admin_url('hr_profile/member/' . get_staff_user_id() . '/dependent_person'));
				}
			}
		}
	}

	/**
	 * delete dependent person
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_dependent_person($id) {
		if (!$id) {
			redirect(admin_url('hr_profile/member' . get_staff_user_id()));
		}
		$response = $this->hr_profile_model->delete_dependent_person($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('hr_is_referenced', _l('hr_dependent_persons')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('hr_dependent_persons')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('hr_dependent_persons')));
		}
		redirect(admin_url('hr_profile/member/' . get_staff_user_id() . '/dependent_person'));
	}

	/**
	 * approval dependents
	 * @return [type]
	 */
	public function dependent_persons() {

		if (!is_admin() && !has_permission('hrm_dependent_person', '', 'view') && !has_permission('hrm_dependent_person', '', 'view_own')) {
			access_denied('You_do_not_have_permission_to_approve');
		}

		$data['approval'] = $this->hr_profile_model->get_dependent_person();
		$data['staff'] = $this->staff_model->get();

		$this->load->view('hr_profile/dependent_person/manage_dependent_person', $data);
	}

	/**
	 * approval status
	 * @return [type]
	 */
	public function approval_status() {
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$data = $this->input->post();

				$success = $this->hr_profile_model->update_approval_status($data);
				if ($success) {
					$message = _l('hr_updated_successfully');
					echo json_encode([
						'success' => true,
						'message' => $message,
					]);
				} else {
					$message = _l('hr_updated_failed');
					echo json_encode([
						'success' => false,
						'message' => $message,
					]);
				}
			}
		}
	}

	/**
	 * table dependent person
	 * @return [type]
	 */
	public function table_dependent_person() {
		$this->app->get_table_data(module_views_path('hr_profile', 'dependent_person/table_dependent_person'));
	}

	/**
	 * import xlsx dependent person
	 * @return [type]
	 */
	public function import_xlsx_dependent_person() {
		if (!is_admin() && !has_permission('hrm_dependent_person', '', 'create')) {
			access_denied('you_do_not_have_permission_create_dependent_person');
		}

		$data_staff = $this->hr_profile_model->get_staff(get_staff_user_id());

		/*get language active*/
		if ($data_staff) {
			if ($data_staff->default_language != '') {
				$data['active_language'] = $data_staff->default_language;

			} else {

				$data['active_language'] = get_option('active_language');
			}

		} else {
			$data['active_language'] = get_option('active_language');
		}

		$this->load->view('hr_profile/dependent_person/import_dependent_person', $data);
	}

	/**
	 * import file xlsx dependent person
	 * @return [type]
	 */
	public function import_file_xlsx_dependent_person() {
		if (!is_admin() && !has_permission('hrm_dependent_person', '', 'create')) {
			access_denied(_l('you_do_not_have_permission_create_dependent_person'));
		}

		$total_row_false = 0;
		$total_rows = 0;
		$dataerror = 0;
		$total_row_success = 0;
		if ($this->input->post()) {

			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

				$this->delete_error_file_day_before();

				// Get the temp file path
				$tmpFilePath = $_FILES['file_csv']['tmp_name'];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

					if (!file_exists(TEMP_FOLDER)) {
						mkdir(TEMP_FOLDER, 0755);
					}

					if (!file_exists($tmpDir)) {
						mkdir($tmpDir, 0755);
					}

					// Setup our new file path
					$newFilePath = $tmpDir . $_FILES['file_csv']['name'];

					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						$import_result = true;
						$rows = [];

						$objReader = new PHPExcel_Reader_Excel2007();
						$objReader->setReadDataOnly(true);
						$objPHPExcel = $objReader->load($newFilePath);
						$rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();
						$sheet = $objPHPExcel->getActiveSheet();

						$dataError = new PHPExcel();
						$dataError->setActiveSheetIndex(0);

						$dataError->getActiveSheet()->setTitle(_l('hr_error_data'));
						$dataError->getActiveSheet()->getColumnDimension('A')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('B')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('C')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('D')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('E')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('F')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('G')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('H')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('I')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('J')->setWidth(20);

						$dataError->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
						$dataError->getActiveSheet()->setCellValue('A1', _l('hr_hr_code'));
						$dataError->getActiveSheet()->setCellValue('B1', _l('hr_dependent_name'));
						$dataError->getActiveSheet()->setCellValue('C1', _l('relationship'));
						$dataError->getActiveSheet()->setCellValue('D1', _l('birth_date'));
						$dataError->getActiveSheet()->setCellValue('E1', _l('identification'));
						$dataError->getActiveSheet()->setCellValue('F1', _l('reason_'));
						$dataError->getActiveSheet()->setCellValue('G1', _l('hr_start_month'));
						$dataError->getActiveSheet()->setCellValue('H1', _l('hr_end_month'));
						$dataError->getActiveSheet()->setCellValue('I1', _l('status'));
						$dataError->getActiveSheet()->setCellValue('J1', _l('hr_error_data_description'));

						$styleArray = array(
							'font' => array(
								'bold' => true,
								'color' => array('rgb' => 'ff0000'),

							));

						//start write on line 2
						$numRow = 2;
						$total_rows = 0;
						$arr_insert = [];
						//get data for compare

						foreach ($rowIterator as $row) {
							$rowIndex = $row->getRowIndex();
							if ($rowIndex > 1) {
								$total_rows++;

								$rd = array();
								$flag = 0;
								$flag2 = 0;
								$flag_mail = 0;
								$string_error = '';

								$value_cell_hrcode = $sheet->getCell('A' . $rowIndex)->getValue();
								$value_cell_dependent_name = $sheet->getCell('B' . $rowIndex)->getValue();
								$value_cell_bir_of_day_dependent = $sheet->getCell('D' . $rowIndex)->getValue();
								$value_cell_dependent_identification = $sheet->getCell('E' . $rowIndex)->getValue();
								$value_cell_start_time = $sheet->getCell('G' . $rowIndex)->getValue();
								$value_cell_end_time = $sheet->getCell('H' . $rowIndex)->getValue();
								$value_cell_status = $sheet->getCell('I' . $rowIndex)->getValue();

								$pattern = '#^[a-z][a-z0-9\._]{2,31}@[a-z0-9\-]{3,}(\.[a-z]{2,4}){1,2}$#';
								$reg_day = '#^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$#';

								/*check null*/
								if (is_null($value_cell_hrcode) == true) {
									$string_error .= _l('hr_hr_code') . _l('not_yet_entered');
									$flag = 1;
								}

								if (is_null($value_cell_dependent_name) == true) {
									$string_error .= _l('hr_dependent_name') . _l('not_yet_entered');
									$flag = 1;
								}

								//check hr_code exist
								if (is_null($value_cell_hrcode) != true) {
									$this->db->where('staff_identifi', $value_cell_hrcode);
									$hrcode = $this->db->count_all_results('tblstaff');
									if ($hrcode == 0) {
										$string_error .= _l('hr_hr_code') . _l('does_not_exist');
										$flag2 = 1;
									}

								}

								//check bir of day dependent person input
								if (is_null($value_cell_bir_of_day_dependent) != true) {
									if (preg_match($reg_day, $value_cell_bir_of_day_dependent, $match) != 1) {
										$string_error .= _l('days_for_identity') . _l('_check_invalid');
										$flag = 1;
									}

								}

								//check start_time
								if (is_null($value_cell_start_time) != true) {
									if (preg_match($reg_day, $value_cell_start_time, $match) != 1) {
										$string_error .= _l('hr_start_month') . _l('_check_invalid');
										$flag = 1;
									}

								}

								//check end_time
								if (is_null($value_cell_end_time) != true) {
									if (preg_match($reg_day, $value_cell_end_time, $match) != 1) {
										$string_error .= _l('hr_end_month') . _l('_check_invalid');
										$flag = 1;
									}

								}

								if (($flag == 1) || ($flag2 == 1)) {
									$dataError->getActiveSheet()->setCellValue('A' . $numRow, $sheet->getCell('A' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('B' . $numRow, $sheet->getCell('B' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('C' . $numRow, $sheet->getCell('C' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('D' . $numRow, $sheet->getCell('D' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('E' . $numRow, $sheet->getCell('E' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('F' . $numRow, $sheet->getCell('F' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('G' . $numRow, $sheet->getCell('G' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('H' . $numRow, $sheet->getCell('H' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('I' . $numRow, $sheet->getCell('I' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('J' . $numRow, $string_error)->getStyle('J' . $numRow)->applyFromArray($styleArray);

									$numRow++;
									$total_row_false++;
								}

								if (($flag == 0) && ($flag2 == 0)) {

									if (is_numeric($value_cell_status) && ($value_cell_status == '2')) {
										/*reject*/
										$rd['status'] = 2;
									} else {
										/*approval*/
										$rd['status'] = 1;
									}

									/*staff id is HR_code, input is HR_CODE, insert => staffid*/
									$rd['staffid'] = $sheet->getCell('A' . $rowIndex)->getValue();
									$rd['dependent_name'] = $sheet->getCell('B' . $rowIndex)->getValue();
									$rd['relationship'] = $sheet->getCell('C' . $rowIndex)->getValue();
									$rd['dependent_bir'] = date('Y-m-d', strtotime(str_replace('/', '-', $sheet->getCell('D' . $rowIndex)->getValue())));
									$rd['dependent_iden'] = $sheet->getCell('E' . $rowIndex)->getValue() != null ? $sheet->getCell('E' . $rowIndex)->getValue() : '';
									$rd['reason'] = $sheet->getCell('F' . $rowIndex)->getValue();
									$rd['start_month'] = date('Y-m-d', strtotime(str_replace('/', '-', $sheet->getCell('G' . $rowIndex)->getValue())));
									$rd['end_month'] = date('Y-m-d', strtotime(str_replace('/', '-', $sheet->getCell('H' . $rowIndex)->getValue())));

									array_push($arr_insert, $rd);
								}

							}

						}
						$total_rows = $total_rows;
						$total_row_success = count($arr_insert);
						$dataerror = $dataError;
						$message = 'Not enought rows for importing';

						if ($total_row_false != 0) {

							$objWriter = new PHPExcel_Writer_Excel2007($dataError);
							$filename = 'Import_dependent_person_error_' . get_staff_user_id() . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
							$objWriter->save(str_replace($filename, HR_PROFILE_ERROR . $filename, $filename));

						} else {
							$this->db->insert_batch(db_prefix() . 'hr_dependent_person', $arr_insert);
						}
						$import_result = true;
						@delete_dir($tmpDir);

					}
				} else {
					set_alert('warning', _l('import_upload_failed'));
				}
			}

		}
		echo json_encode([
			'message' => $message,
			'total_row_success' => $total_row_success,
			'total_row_false' => $total_row_false,
			'total_rows' => $total_rows,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id(),
		]);

	}

	/**
	 * admin delete dependent person
	 * @param  [type] $id
	 * @return [type]
	 */
	public function admin_delete_dependent_person($id) {
		if (!$id) {
			redirect(admin_url('hr_profile/member' . get_staff_user_id()));
		}
		$response = $this->hr_profile_model->delete_dependent_person($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('hr_is_referenced', _l('hr_dependent_persons')));
		} elseif ($response == true) {
			set_alert('success', _l('hr_deleted'));
		} else {
			set_alert('warning', _l('problem_deleting', _l('hr_dependent_persons')));
		}
		redirect(admin_url('hr_profile/dependent_persons'));
	}

	/**
	 * delete_error file day before
	 * @return [type]
	 */
	public function delete_error_file_day_before($before_day = '', $folder_name = '') {
		if ($before_day != '') {
			$day = $before_day;
		} else {
			$day = '7';
		}

		if ($folder_name != '') {
			$folder = $folder_name;
		} else {
			$folder = HR_PROFILE_ERROR;
		}

		//Delete old file before 7 day
		$date = date_create(date('Y-m-d H:i:s'));
		date_sub($date, date_interval_create_from_date_string($day . " days"));
		$before_7_day = strtotime(date_format($date, "Y-m-d H:i:s"));

		foreach (glob($folder . '*') as $file) {

			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (file_exists($file)) {
				//don't delete index.html file
				if ($filename != 'index.html') {
					$file_name_arr = explode("_", $filename);
					$date_create_file = array_pop($file_name_arr);
					$date_create_file = str_replace('.xlsx', '', $date_create_file);

					if ((float) $date_create_file <= (float) $before_7_day) {
						unlink($folder . $filename);
					}
				}
			}
		}
		return true;
	}

	/**
	 * dependent person modal
	 * @return [type]
	 */
	public function dependent_person_modal() {
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		$this->load->model('staff_model');

		$data['staff_members'] = $this->staff_model->get('', ['active' => 1]);

		if ($this->input->post('slug') === 'create') {
			$data['manage'] = $this->input->post('manage');
			$this->load->view('hr_profile/dependent_person/dependent_person_modal', $data);
		} else if ($this->input->post('slug') === 'update') {

			$data['manage'] = $this->input->post('manage');
			$data['dependent_person_id'] = $this->input->post('dependent_person_id');
			$data['dependent_person'] = $this->hr_profile_model->get_dependent_person($data['dependent_person_id']);

			if (isset($data['notes'])) {
				$data['notes'] = htmlentities($data['notes']);
			}

			$this->load->view('hr_profile/dependent_person/dependent_person_modal', $data);
		}
	}

	/**
	 * resignation procedures
	 * @return [type]
	 */
	public function resignation_procedures() {
		$this->app_scripts->add('circle-progress-js', 'assets/plugins/jquery-circle-progress/circle-progress.min.js');
		if (!has_permission('hrm_procedures_for_quitting_work', '', 'view') && !has_permission('hrm_procedures_for_quitting_work', '', 'view_own') && !is_admin()) {
			access_denied('hrm_procedures_for_quitting_work');
		}

		$data['staffs'] = $this->staff_model->get('', ['active' => 1]);
		$data['detail'] = $this->input->get('detail');
		$this->load->view('resignation_procedures/manage_resignation_procedures', $data);
	}

	/**
	 * add staff quitting work
	 */
	public function add_resignation_procedure() {
		if (!has_permission('hrm_procedures_for_quitting_work', '', 'edit') && !has_permission('hrm_procedures_for_quitting_work', '', 'add') && !is_admin()) {
			access_denied('hrm_procedures_for_quitting_work');
		}

		$data = $this->input->post();
		$response = $this->hr_profile_model->add_resignation_procedure($data);
		if ($response == true) {
			set_alert('success', _l('added_successfully', _l('staff_member')));
		} else if ($response == false) {
			set_alert('warning', _l('This_person_has_been_on_the_list_of_quit_work'));
		}
		redirect(admin_url('hr_profile/resignation_procedures'));
	}

	/**
	 * delete resignation procedure
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_resignation_procedure($id) {

		if (!has_permission('hrm_procedures_for_quitting_work', '', 'edit') && !is_admin()) {
			access_denied('hrm_procedures_for_quitting_work');
		}

		$success = $this->hr_profile_model->delete_procedures_for_quitting_work($id);
		if ($success) {
			set_alert('success', _l('deleted', _l('hr_procedures_for_quitting_work')));
		}

		redirect(admin_url('hr_profile/resignation_procedures'));
	}

	/**
	 * table resignation procedures
	 * @return [type]
	 */
	public function table_resignation_procedures() {
		$this->app->get_table_data(module_views_path('hr_profile', 'resignation_procedures/table_resignation_procedures'));
	}

	/**
	 * get staff info of resignation procedures
	 * @param  [type] $staff_id
	 * @return [type]
	 */
	public function get_staff_info_of_resignation_procedures($staff_id) {
		$staff_email = '';
		$staff_department_name = '';
		$staff_job_position = '';
		$status = true;
		$message = '';

		//check resignation procedures exist
		$resignation_procedure = $this->hr_profile_model->get_resignation_procedure_by_staff($staff_id);

		if (!$resignation_procedure) {
			$staff = $this->staff_model->get($staff_id);
			if ($staff) {
				$staff_email = $staff->email;
				$staff_job_position = hr_profile_job_name_by_id($staff->job_position);
				$departments = $this->departments_model->get_staff_departments($staff_id);

				if (count($departments) > 0) {
					foreach ($departments as $value) {
						if (strlen($staff_department_name) > 0) {
							$staff_department_name .= ',' . $value['name'];
						} else {
							$staff_department_name .= $value['name'];
						}
					}
				}

			}
		} else {
			$status = false;
			$message = _l('hr_resignation_procedure_already_exists');
		}

		echo json_encode([
			'staff_email' => $staff_email,
			'staff_department_name' => $staff_department_name,
			'staff_job_position' => $staff_job_position,
			'status' => $status,
			'message' => $message,
		]);
		die;

	}

	/**
	 * delete procedures for quitting work
	 * @param  [type] $staffid
	 * @return [type]
	 */
	public function delete_procedures_for_quitting_work($staffid) {
		if (!has_permission('hrm_procedures_for_quitting_work', '', 'edit') && !is_admin()) {
			access_denied('hrm_procedures_for_quitting_work');
		}

		$success = $this->hr_profile_model->delete_procedures_for_quitting_work($staffid);
		if ($success) {
			set_alert('success', _l('deleted', _l('hr_procedures_for_quitting_work')));
		}

		redirect(admin_url('hr_profile/resignation_procedures'));
	}

	/**
	 * set data detail staff checklist quit work
	 * @param [type] $staffid
	 */
	public function set_data_detail_staff_checklist_quit_work($staffid) {
		if ($this->input->is_ajax_request()) {
			$results = $this->hr_profile_model->get_data_procedure_retire_of_staff($staffid);

			$html = '<input type="hidden" name="staffid" value="' . $staffid . '">';
			$rel_id = '';
			foreach ($results as $key => $value) {
				if ($value['people_handle_id'] == 0) {
					$value['people_handle_id'] = get_staff_user_id();
				}
				if ($rel_id != $value['rel_id']) {
					$rel_id = $value['rel_id'];
					$html .= '<br><h5 class="no-margin font-bold text-danger"><i class="fa fa-plus "></i>  ' . $value['rel_name'] . ' (' . get_staff_full_name($value['people_handle_id']) . ')<span ></span></h5><br>';

					$html .= ' <a href="#" class="list-group-item list-group-item-action">
					<div class="row">
					<div class="col-md-10 resignation-procedures-modal"><label for="' . $value['id'] . '">' . $value['option_name'] . ' </label></div>
					<div class="col-md-2 text-right">
					<div class="row">
					<div class="col-md-6 pt-1 pr-2">
					<div class="checkbox float-right">';
					if ($value['status'] == 1) {
						$html .= '<input type="checkbox" class="option_name" name="option_name[]" id="' . $value['id'] . '" data-id="' . $value['id'] . '" value="' . $value['id'] . '" checked disabled>
						<label></label>';
					} else {
						$html .= '<input type="checkbox" class="option_name" name="option_name[]" id="' . $value['id'] . '" data-id="' . $value['id'] . '" value="' . $value['id'] . '">
						<label></label>';
					}
					$html .= '</div>
					</div>
					</div>
					</div>
					</div>
					</a>';
				} else {
					$html .= ' <a href="#" class="list-group-item list-group-item-action" >
					<div class="row">
					<div class="col-md-10 resignation-procedures-modal"><label for="' . $value['id'] . '">' . $value['option_name'] . ' </label></div>
					<div class="col-md-2 text-right">
					<div class="row">
					<div class="col-md-6 pt-1 pr-2">
					<div class="checkbox float-right">';
					if ($value['status'] == 1) {
						$html .= '<input type="checkbox" class="option_name" name="option_name[]" id="' . $value['id'] . '" data-id="' . $value['id'] . '" value="' . $value['id'] . '" checked disabled>
						<label></label>';
					} else {
						$html .= '<input type="checkbox" class="option_name" name="option_name[]" id="' . $value['id'] . '" data-id="' . $value['id'] . '" value="' . $value['id'] . '">
						<label></label>';
					}
					$html .= '</div>
					</div>
					</div>
					</div>
					</div>

					</a>';
				}
			}
		}
		echo json_encode([
			'result' => $html,
			'staff_name' => get_staff_full_name($staffid),
		]);

	}

	/**
	 * update status quit work
	 * @param  [type] $staffid
	 * @return [type]
	 */
	public function update_status_quit_work() {
		$data = $this->input->post();
		$staffid = $data['staffid'];
		$id = $data['id'];
		$result = $this->hr_profile_model->update_status_quit_work($staffid, $id);

		if ($result == 0) {
			$message = _l('hr_updated_successfully');
		} else {
			$message = _l('hr_update_failed');
		}

		echo json_encode([
			'status' => $result,
			'message' => $message,
		]);

	}

	/**
	 * update status option name
	 * @return [type]
	 */
	public function update_status_option_name() {
		$data = $this->input->post();
		if ($data['finish'] == 0) {
			foreach ($data['option_name'] as $id_option) {
				$result = $this->hr_profile_model->update_status_procedure_retire_of_staff(['id' => $id_option]);
			}
		} else {
			$result = $this->hr_profile_model->update_status_procedure_retire_of_staff(['staffid' => $data['staffid']]);
		}

		if ($result) {
			set_alert('success', _l('hr_updated_successfully'));
		} else if ($response == false) {
			set_alert('warning', _l('hr_update_failed'));
		}
		redirect(admin_url('hr_profile/resignation_procedures'));
	}

	/**
	 * preview q a file
	 * @param  [type] $id
	 * @param  [type] $rel_id
	 * @return [type]
	 */
	public function preview_q_a_file($id, $rel_id) {
		$data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
		$data['current_user_is_admin'] = is_admin();
		$data['file'] = $this->hr_profile_model->get_file($id, $rel_id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		$this->load->view('hr_profile/knowledge_base_q_a/preview_q_a_file', $data);
	}

	/**
	 * delete hr profile q a attachment file
	 * @param  [type] $attachment_id
	 * @return [type]
	 */
	public function delete_hr_profile_q_a_attachment_file($attachment_id) {
		if (!has_permission('hr_manage_q_a', '', 'delete')) {
			access_denied('hr_manage_q_a');
		}

		$file = $this->misc_model->get_file($attachment_id);
		echo json_encode([
			'success' => $this->hr_profile_model->delete_hr_q_a_attachment_file($attachment_id),
		]);
	}

	/**
	 * get salary allowance value
	 * @param  [type] $rel_type
	 * @return [type]
	 */
	public function get_salary_allowance_value($rel_type) {

		if (preg_match('/^st_/', $rel_type)) {
			$rel_value = str_replace('st_', '', $rel_type);
			$salary_type = $this->hr_profile_model->get_salary_form($rel_value);

			$type = 'salary';
			if ($salary_type) {
				$value = $salary_type->salary_val;
			} else {
				$value = 0;
			}

		} elseif (preg_match('/^al_/', $rel_type)) {
			$rel_value = str_replace('al_', '', $rel_type);
			$allowance_type = $this->hr_profile_model->get_allowance_type($rel_value);

			$type = 'allowance';
			if ($allowance_type) {
				$value = $allowance_type->allowance_val;
			} else {
				$value = 0;
			}

		} else {

		}

		$effective_date = date('Y-m-d');

		echo json_encode([
			'type' => $type,
			'rel_value' => (float) $value,
			'effective_date' => $effective_date,
		]);
		die;
	}

	/**
	 * hrm file contract
	 * @param  [type] $id
	 * @param  [type] $rel_id
	 * @return [type]
	 */
	public function hrm_file_contract($id, $rel_id) {
		$data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
		$data['current_user_is_admin'] = is_admin();
		$data['file'] = $this->hr_profile_model->get_file($id, $rel_id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		$this->load->view('hr_profile/contracts/preview_contract_file', $data);
	}

	/**
	 * delete hrm contract attachment file
	 * @param  [type] $attachment_id
	 * @return [type]
	 */
	public function delete_hrm_contract_attachment_file($attachment_id) {
		if (!has_permission('hrm_contract', '', 'delete') && !is_admin()) {
			access_denied('hrm');
		}

		$file = $this->misc_model->get_file($attachment_id);
		echo json_encode([
			'success' => $this->hr_profile_model->delete_hr_contract_attachment_file($attachment_id),
		]);
	}

	/**
	 * member modal
	 * @return [type]
	 */
	public function member_modal() {
		if (!$this->input->is_ajax_request()) {
			show_404();
		}
		$this->load->model('staff_model');

		if ($this->input->post('slug') === 'create') {

			$this->load->view('hr_record/mew_member', $data);

		} else if ($this->input->post('slug') === 'update') {
			$staff_id = $this->input->post('staff_id');
			$role_id = $this->input->post('role_id');

			$data = ['funcData' => ['staff_id' => isset($staff_id) ? $staff_id : null]];

			if (isset($staff_id)) {
				$data['member'] = $this->staff_model->get($staff_id);
			}

			$data['roles_value'] = $this->roles_model->get();
			$data['staffs'] = hr_profile_get_staff_id_dont_permissions();
			$add_new = $this->input->post('add_new');

			if ($add_new == ' hide') {
				$data['add_new'] = ' hide';
				$data['display_staff'] = '';
			} else {
				$data['add_new'] = '';
				$data['display_staff'] = ' hide';
			}
			$this->load->model('currencies_model');

			$data['list_staff'] = $this->staff_model->get();
			$data['base_currency'] = $this->currencies_model->get_base_currency();
			$data['departments'] = $this->departments_model->get();
			$data['staff_departments'] = $this->departments_model->get_staff_departments($staff_id);
			$data['positions'] = $this->hr_profile_model->get_job_position();
			$data['workplace'] = $this->hr_profile_model->get_workplace();
			$data['staff_cover_image'] = $this->hr_profile_model->get_hr_profile_file($staff_id, 'staff_profile_images');
			$data['manage_staff'] = $this->input->post('manage_staff');
			$this->load->view('hr_record/update_member', $data);
		}
	}

	/**
	 * new member
	 * @return [type]
	 */
	public function new_member() {

		if (!has_permission('hrm_hr_records', '', 'create')) {
			access_denied('staff');
		}

		$data['hr_profile_member_add'] = true;
		$title = _l('add_new', _l('staff_member_lowercase'));

		$this->load->model('currencies_model');
		$data['positions'] = $this->hr_profile_model->get_job_position();
		$data['workplace'] = $this->hr_profile_model->get_workplace();
		$data['base_currency'] = $this->currencies_model->get_base_currency();

		$data['roles_value'] = $this->roles_model->get();
		$data['departments'] = $this->departments_model->get();
		$data['title'] = $title;
		$data['contract_type'] = $this->hr_profile_model->get_contracttype();
		$data['staff'] = $this->staff_model->get();
		$data['list_staff'] = $this->staff_model->get();
		$data['funcData'] = ['staff_id' => isset($staff_id) ? $staff_id : null];
		$data['staff_code'] = $this->hr_profile_model->create_code('staff_code');

		$this->load->view('hr_record/new_member', $data);
	}

	/**
	 * add edit member
	 * @param string $id
	 */
	public function add_edit_member($id = '') {
		if (!has_permission('hrm_hr_records', '', 'view') && !has_permission('hrm_hr_records', '', 'view_own') && get_staff_user_id() != $id) {
			access_denied('staff');
		}
		hooks()->do_action('staff_member_edit_view_profile', $id);

		$this->load->model('departments_model');
		if ($this->input->post()) {
			$data = $this->input->post();
			// Don't do XSS clean here.
			$data['email_signature'] = $this->input->post('email_signature', false);
			$data['email_signature'] = html_entity_decode($data['email_signature']);

			if ($data['email_signature'] == strip_tags($data['email_signature'])) {
				// not contains HTML, add break lines
				$data['email_signature'] = nl2br_save_html($data['email_signature']);
			}

			$data['password'] = $this->input->post('password', false);

			if ($id == '') {
				if (!has_permission('hrm_hr_records', '', 'create')) {
					access_denied('staff');
				}
				$id = $this->hr_profile_model->add_staff($data);

				if ($id) {
					hr_profile_handle_staff_profile_image_upload($id);
					set_alert('success', _l('added_successfully', _l('staff_member')));
					redirect(admin_url('hr_profile/member/' . $id));
				}
			} else {
				if (!has_permission('hrm_hr_records', '', 'edit') && get_staff_user_id() != $id) {
					access_denied('staff');
				}

				$manage_staff = false;
				if (isset($data['manage_staff'])) {
					$manage_staff = true;
					unset($data['manage_staff']);
				}
				hr_profile_handle_staff_profile_image_upload($id);
				$response = $this->hr_profile_model->update_staff($data, $id);
				if (is_array($response)) {
					if (isset($response['cant_remove_main_admin'])) {
						set_alert('warning', _l('staff_cant_remove_main_admin'));
					} elseif (isset($response['cant_remove_yourself_from_admin'])) {
						set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
					}
				} elseif ($response == true) {
					set_alert('success', _l('updated_successfully', _l('staff_member')));
				}

				if ($manage_staff) {
					redirect(admin_url('hr_profile/staff_infor'));
				} else {
					redirect(admin_url('hr_profile/member/' . $id));
				}
			}
		}

		$title = _l('add_new', _l('staff_member_lowercase'));
		$this->load->model('currencies_model');
		$data['positions'] = $this->hr_profile_model->get_job_position();
		$data['workplace'] = $this->hr_profile_model->get_workplace();
		$data['base_currency'] = $this->currencies_model->get_base_currency();

		$data['roles_value'] = $this->roles_model->get();
		$data['departments'] = $this->departments_model->get();
		$data['title'] = $title;
		$data['contract_type'] = $this->hr_profile_model->get_contracttype();
		$data['staff'] = $this->staff_model->get();
		$data['list_staff'] = $this->staff_model->get();
		$data['funcData'] = ['staff_id' => isset($staff_id) ? $staff_id : null];
		$data['staff_code'] = $this->hr_profile_model->create_code('staff_code');

		$this->load->view('hr_record/new_member', $data);
	}

	/**
	 * change staff status: Change status to staff active or inactive
	 * @param  [type] $id
	 * @param  [type] $status
	 * @return [type]
	 */
	public function change_staff_status($id, $status) {
		if (has_permission('hrm_hr_records', '', 'edit')) {
			if ($this->input->is_ajax_request()) {
				$this->staff_model->change_staff_status($id, $status);
			}
		}
	}

	/**
	 * hr code exists
	 * @return [type]
	 */
	public function hr_code_exists() {
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				// First we need to check if the email is the same
				$memberid = $this->input->post('memberid');
				if ($memberid != '') {
					$this->db->where('staffid', $memberid);
					$staff = $this->db->get('tblstaff')->row();
					if ($staff->staff_identifi == $this->input->post('staff_identifi')) {
						echo json_encode(true);
						die();
					}
				}

				$this->db->where('staff_identifi', $this->input->post('staff_identifi'));
				$total_rows = $this->db->count_all_results('tblstaff');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

	/**
	 * view contract modal
	 * @return [type]
	 */
	public function view_contract_modal() {
		if (!$this->input->is_ajax_request()) {
			show_404();
		}
		$this->load->model('staff_model');

		if ($this->input->post('slug') === 'view') {
			$contract_id = $this->input->post('contract_id');

			$data['contract'] = $this->hr_profile_model->get_contract($contract_id);
			$data['contract_details'] = $this->hr_profile_model->get_contract_detail($contract_id);

			$this->load->view('hr_record/contract_modal_view', $data);
		}
	}

	/**
	 * reports
	 * @return [type]
	 */
	public function reports() {
		if (!has_permission('hrm_report', '', 'view') && !is_admin()) {
			access_denied('reports');
		}

		$data['mysqlVersion'] = $this->db->query('SELECT VERSION() as version')->row();
		$data['sqlMode'] = $this->db->query('SELECT @@sql_mode as mode')->row();
		$data['position'] = $this->hr_profile_model->get_job_position();
		$data['staff'] = $this->staff_model->get();
		$data['department'] = $this->departments_model->get();
		$data['title'] = _l('hr_reports');

		$this->load->view('reports/manage_reports', $data);
	}

	/**
	 * report by leave statistics
	 * @return [type]
	 */
	public function report_by_leave_statistics() {
		echo json_encode($this->hr_profile_model->report_by_leave_statistics());
	}

	/**
	 * report by working hours
	 * @return [type]
	 */
	public function report_by_working_hours() {
		echo json_encode($this->hr_profile_model->report_by_working_hours());
	}

	/**
	 * report the employee quitting
	 * @return [type]
	 */
	public function report_the_employee_quitting() {
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {

				// $months_report = 'all_time';
				$months_report = $this->input->post('months_filter');
				$position_filter = $this->input->post('position_filter');
				$department_filter = $this->input->post('department_filter');
				$staff_filter = $this->input->post('staff_filter');

				if ($months_report == 'this_month') {
					$from_date = date('Y-m-01') . ' 00:00:00';
					$to_date = date('Y-m-t') . ' 23:59:59';
				}
				if ($months_report == '1') {
					$from_date = date('Y-m-01', strtotime('first day of last month')) . ' 00:00:00';
					$to_date = date('Y-m-t', strtotime('last day of last month')) . ' 23:59:59';
				}
				if ($months_report == 'this_year') {
					$from_date = date('Y-m-d', strtotime(date('Y-01-01'))) . ' 00:00:00';
					$to_date = date('Y-m-d', strtotime(date('Y-12-31'))) . ' 23:59:59';
				}
				if ($months_report == 'last_year') {
					$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) . ' 00:00:00';
					$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . ' 23:59:59';
				}

				if ($months_report == '3') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH")) . ' 00:00:00';
					$to_date = date('Y-m-t') . ' 23:59:59';
				}
				if ($months_report == '6') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH")) . ' 00:00:00';
					$to_date = date('Y-m-t') . ' 23:59:59';

				}
				if ($months_report == '12') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH")) . ' 00:00:00';
					$to_date = date('Y-m-t') . ' 23:59:59';

				}
				if ($months_report == 'custom') {
					$from_date = to_sql_date($this->input->post('report_from')) . ' 00:00:00';
					$to_date = to_sql_date($this->input->post('report_to')) . ' 23:59:59';
				}

				$select = [
					'staffid',
					'staff_identifi',
					'firstname',
					'job_position',
					'staffid',
					'staffid',
					'staffid',
				];
				$query = '';

				if (isset($from_date) && isset($to_date)) {
					$query = ' staffid in (SELECT staffid FROM ' . db_prefix() . 'hr_list_staff_quitting_work where dateoff >= \'' . $from_date . '\' and dateoff <= \'' . $to_date . '\' AND ' . db_prefix() . 'hr_list_staff_quitting_work.approval = "approved") and';
				} else {
					$query = ' staffid in (SELECT staffid FROM ' . db_prefix() . 'hr_list_staff_quitting_work where  ' . db_prefix() . 'hr_list_staff_quitting_work.approval = "approved") and';
				}

				if (isset($position_filter)) {
					$position_list = implode(',', $position_filter);
					$query .= ' job_position in (' . $position_list . ') and';
				}
				if (isset($staff_filter)) {
					$staffid_list = implode(',', $staff_filter);
					$query .= ' staffid in (' . $staffid_list . ') and';
				}
				if (isset($department_filter)) {
					$department_list = implode(',', $department_filter);
					$query .= ' staffid in (SELECT staffid FROM ' . db_prefix() . 'staff_departments where departmentid in (' . $department_list . ')) and';
				}

				$total_query = '';
				if (($query) && ($query != '')) {
					$total_query = rtrim($query, ' and');
					$total_query = ' where ' . $total_query;
				}

				$where = [$total_query];

				$aColumns = $select;
				$sIndexColumn = 'staffid';
				$sTable = db_prefix() . 'staff';
				$join = [];

				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
					'staffid',
					'firstname',
					'lastname',
					'staff_identifi',
					'job_position',
					'datecreated',
					'email',
				]);

				$output = $result['output'];
				$rResult = $result['rResult'];
				foreach ($rResult as $aRow) {
					$row = [];
					$row[] = $aRow['staffid'];
					$row[] = $aRow['staff_identifi'];
					$row[] = $aRow['firstname'] . ' ' . $aRow['lastname'];

					$position = $this->hr_profile_model->get_job_position($aRow['job_position']);
					$name_position = '';
					if (isset($position) && !is_array($position)) {
						$name_position = $position->position_name;
					}
					$row[] = $name_position;

					$department = $this->hr_profile_model->get_department_by_staffid($aRow['staffid']);
					$name_department = '';
					if (isset($department)) {
						$name_department = $department->name;
					}
					$row[] = $name_department;

					$row[] = date('d/m/Y', strtotime($aRow['datecreated']));

					$data_quiting = $this->hr_profile_model->get_list_quiting_work($aRow['staffid']);
					$date_off = '';
					if (isset($data_quiting)) {
						$date_off = date('d/m/Y', strtotime($data_quiting->dateoff));
					}
					$row[] = $date_off;

					$output['aaData'][] = $row;
				}

				echo json_encode($output);
				die();
			}
		}
	}

	/**
	 * list of employees with salary change
	 * @return [type]
	 */
	public function list_of_employees_with_salary_change() {
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$months_report = $this->input->post('months_filter');
				$position_filter = $this->input->post('position_filter');
				$department_filter = $this->input->post('department_filter');
				$staff_filter = $this->input->post('staff_filter');

				$from_date = date('Y-m-d', strtotime('1997-01-01'));
				$to_date = date('Y-m-d', strtotime(date('Y-m-d')));

				if ($months_report == 'this_month') {

					$from_date = date('Y-m-01');
					$to_date = date('Y-m-t');
				}
				if ($months_report == '1') {
					$from_date = date('Y-m-01', strtotime('first day of last month'));
					$to_date = date('Y-m-t', strtotime('last day of last month'));
				}
				if ($months_report == 'this_year') {
					$from_date = date('Y-m-d', strtotime(date('Y-01-01')));
					$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
				}
				if ($months_report == 'last_year') {
					$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
					$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
				}

				if ($months_report == '3') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');

				}
				if ($months_report == '6') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');

				}
				if ($months_report == '12') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');

				}
				if ($months_report == 'custom') {
					$from_date = to_sql_date($this->input->post('report_from'));
					$to_date = to_sql_date($this->input->post('report_to'));
				}
				$select = [
					'staffid',
					'firstname',
					'staff_identifi',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
					'staffid',
				];
				$query = '';
				if (isset($position_filter)) {
					$position_list = implode(',', $position_filter);
					$query .= ' job_position in (' . $position_list . ') and';
				}
				if (isset($staff_filter)) {
					$staffid_list = implode(',', $staff_filter);
					$query .= ' staffid in (' . $staffid_list . ') and';
				}
				if (isset($department_filter)) {
					$department_list = implode(',', $department_filter);
					$query .= ' staffid in (SELECT staffid FROM ' . db_prefix() . 'staff_departments where departmentid in (' . $department_list . ')) and';
				}

				$total_query = '';
				if (($query) && ($query != '')) {
					$total_query = rtrim($query, ' and');
					$total_query = ' where ' . $total_query;
				}
				$where = [$total_query];

				$aColumns = $select;
				$sIndexColumn = 'staffid';
				$sTable = db_prefix() . 'staff';
				$join = [];

				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
					'staffid',
					'firstname',
					'lastname',
					'staff_identifi',
					'job_position',
					'email',
				]);

				$output = $result['output'];
				$rResult = $result['rResult'];
				foreach ($rResult as $aRow) {
					$row = [];
					$row[] = $aRow['staffid'];
					$row[] = $aRow['staff_identifi'];
					$row[] = $aRow['firstname'] . ' ' . $aRow['lastname'];
					$position = $this->hr_profile_model->get_job_position($aRow['job_position']);
					$name_position = '';
					if (isset($position) && !is_array($position)) {
						$name_position = $position->position_name;
					}
					$row[] = $name_position;

					$department = $this->hr_profile_model->get_department_by_staffid($aRow['staffid']);
					$name_department = '';
					if (isset($department)) {
						$name_department = $department->name;
					}
					$row[] = $name_department;

					$has_change = 0;
					$old_salary = 0;
					$new_salary = 0;
					$date_effect = '1970-01-01 00:00:00';
					$list_contract_staff = $this->hr_profile_model->get_list_contract_detail_staff($aRow['staffid']);

					if ($list_contract_staff) {
						$has_change = 1;
						$old_salary = $list_contract_staff['old_salary'];
						$new_salary = $list_contract_staff['new_salary'];
						$date_effect = $list_contract_staff['date_effect'];

					}

					$strtotime_from_date = strtotime($from_date);
					$strtotime_to_date = strtotime($to_date);
					$strtotime_date_effect = strtotime($date_effect);

					if (($strtotime_date_effect >= $strtotime_from_date) && ($strtotime_date_effect <= $strtotime_to_date)) {

						$row[] = _d($date_effect);
						$row[] = app_format_money($old_salary, $this->get_base_currency_name());
						$row[] = app_format_money($new_salary, $this->get_base_currency_name());
						if ($has_change == 1) {
							$output['aaData'][] = $row;
						}
					}

				}
				echo json_encode($output);
				die();
			}
		}
	}

	/**
	 * get get base currency name
	 * @return [type]
	 */
	public function get_base_currency_name() {
		$currency = '';

		$this->load->model('currencies_model');
		$base_currency = $this->currencies_model->get_base_currency();

		if ($base_currency) {
			$currency .= $base_currency->name;
		}
		return $currency;
	}

	/**
	 * get chart senior staff
	 * @return [type]
	 */
	public function get_chart_senior_staff($sort_from, $months_report = '', $report_from = '', $report_to = '') {
		if ($this->input->is_ajax_request()) {

			$months_report = $months_report;
			if ($months_report == '' || !isset($months_report)) {
				$staff_list = $this->staff_model->get();
			}
			if ($months_report == 'this_month') {

				$beginMonth = date('Y-m-01');
				$endMonth = date('Y-m-t');
				$staff_list = $this->hr_profile_model->get_staff_by_month($beginMonth, $endMonth);
			}
			if ($months_report == '1') {
				$beginMonth = date('Y-m-01', strtotime('first day of last month'));
				$endMonth = date('Y-m-t', strtotime('last day of last month'));
				$staff_list = $this->hr_profile_model->get_staff_by_month($beginMonth, $endMonth);
			}
			if ($months_report == 'this_year') {
				$from_year = date('Y-m-d', strtotime(date('Y-01-01')));
				$to_year = date('Y-m-d', strtotime(date('Y-12-31')));
				$staff_list = $this->hr_profile_model->get_staff_by_month($from_year, $to_year);
			}
			if ($months_report == 'last_year') {

				$from_year = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
				$to_year = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));

				$staff_list = $this->hr_profile_model->get_staff_by_month($from_year, $to_year);

			}

			if ($months_report == '3') {
				$months_report = 3;
				$months_report--;
				$beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
				$endMonth = date('Y-m-t');
				$staff_list = $this->hr_profile_model->get_staff_by_month($beginMonth, $endMonth);

			}
			if ($months_report == '6') {
				$months_report = 6;
				$months_report--;
				$beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
				$endMonth = date('Y-m-t');
				$staff_list = $this->hr_profile_model->get_staff_by_month($beginMonth, $endMonth);
			}
			if ($months_report == '12') {
				$months_report = 12;
				$months_report--;
				$beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
				$endMonth = date('Y-m-t');
				$staff_list = $this->hr_profile_model->get_staff_by_month($beginMonth, $endMonth);
			}
			if ($months_report == 'custom') {
				$from_date = to_sql_date($report_from);
				$to_date = to_sql_date($report_to);
				$staff_list = $this->hr_profile_model->get_staff_by_month($from_date, $to_date);

			}

			$list_count_month = array();
			$m1 = 0;
			$m3 = 0;
			$m6 = 0;
			$m9 = 0;
			$m12 = 0;
			$mp12 = 0;

			$p1 = 0;
			$p3 = 0;
			$p6 = 0;
			$p9 = 0;
			$p12 = 0;
			$pp12 = 0;
			$count_total_staff = count($staff_list);

			$current_date = new DateTime(date('Y-m-d'));

			foreach ($staff_list as $key => $value) {
				if ($value['datecreated'] != '') {

					$datecreated = new DateTime($value['datecreated']);

					$total_month = $datecreated->diff($current_date)->m + ($datecreated->diff($current_date)->y * 12) + $datecreated->diff($current_date)->d / 30;

					if ($total_month <= 1) {
						$m1 += 1;
					}
					if (($total_month > 1) && ($total_month <= 3)) {
						$m3 += 1;
					}
					if (($total_month > 3) && ($total_month <= 6)) {
						$m6 += 1;
					}
					if (($total_month > 6) && ($total_month <= 9)) {
						$m9 += 1;
					}
					if (($total_month > 9) && ($total_month <= 12)) {
						$m12 += 1;
					}
					if ($total_month > 12) {
						$mp12 += 1;
					}
				}
			}

			$list_chart = array($m1, $m3, $m6, $m9, $m12, $mp12);
			if ($count_total_staff > 0) {
				foreach ($list_chart as $key => $value) {
					if ($key == 0) {
						$p1 = round(($value * 100) / $count_total_staff, 2);
					}
					if ($key == 1) {
						$p3 = round(($value * 100) / $count_total_staff, 2);
					}
					if ($key == 2) {
						$p6 = round(($value * 100) / $count_total_staff, 2);
					}
					if ($key == 3) {
						$p9 = round(($value * 100) / $count_total_staff, 2);
					}
					if ($key == 4) {
						$p12 = round(($value * 100) / $count_total_staff, 2);
					}
					if ($key == 5) {
						$pp12 = round(($value * 100) / $count_total_staff, 2);
					}
				}
			}

			$list_ratio = array($p1, $p3, $p6, $p9, $p12, $pp12);

			echo json_encode([
				'data' => $list_chart,
				'data_ratio' => $list_ratio,
			]);
		}
	}

	/**
	 * HR is working
	 */
	public function HR_is_working() {
		if ($this->input->is_ajax_request()) {

			$months_report = $this->input->post('months_filter');

			$from_date = date('Y-m-d', strtotime('1997-01-01'));
			$to_date = date('Y-m-d', strtotime(date('Y-12-31')));

			if ($months_report == 'this_month') {

				$from_date = date('Y-m-01');
				$to_date = date('Y-m-t');
			}
			if ($months_report == '1') {
				$from_date = date('Y-m-01', strtotime('first day of last month'));
				$to_date = date('Y-m-t', strtotime('last day of last month'));

			}
			if ($months_report == 'this_year') {
				$from_date = date('Y-m-d', strtotime(date('Y-01-01')));
				$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
			}
			if ($months_report == 'last_year') {
				$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
				$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
			}

			if ($months_report == '3') {
				$months_report--;
				$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
				$to_date = date('Y-m-t');

			}
			if ($months_report == '6') {
				$months_report--;
				$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
				$to_date = date('Y-m-t');

			}
			if ($months_report == '12') {
				$months_report--;
				$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
				$to_date = date('Y-m-t');

			}
			if ($months_report == 'custom') {
				$from_date = to_sql_date($this->input->post('report_from'));
				$to_date = to_sql_date($this->input->post('report_to'));
			}

			// change to report_by_staffs_month($from_date, $to_date));
			echo json_encode($this->hr_profile_model->report_by_staffs());

		}
	}

	/**
	 * qualification department
	 * @return [type]
	 */
	public function qualification_department() {
		if ($this->input->is_ajax_request()) {
			if ($this->input->post()) {
				$months_report = $this->input->post('months_filter');
				$department_filter = $this->input->post('department_filter');

				$from_date = date('Y-m-d', strtotime('1997-01-01'));
				$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
				if ($months_report == 'this_month') {

					$from_date = date('Y-m-01');
					$to_date = date('Y-m-t');
				}
				if ($months_report == '1') {
					$from_date = date('Y-m-01', strtotime('first day of last month'));
					$to_date = date('Y-m-t', strtotime('last day of last month'));

				}
				if ($months_report == 'this_year') {
					$from_date = date('Y-m-d', strtotime(date('Y-01-01')));
					$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
				}
				if ($months_report == 'last_year') {
					$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
					$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
				}

				if ($months_report == '3') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');

				}
				if ($months_report == '6') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');

				}
				if ($months_report == '12') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');

				}
				if ($months_report == 'custom') {
					$from_date = to_sql_date($this->input->post('report_from'));
					$to_date = to_sql_date($this->input->post('report_to'));
				}

				$id_department = '';
				if (isset($department_filter)) {
					$id_department = implode(',', $department_filter);
				}
				$circle_mode = false;
				$list_diploma = array(
					"primary_level",
					"intermediate_level",
					"college_level",
					"masters",
					"doctor",
					"bachelor",
					"engineer",
					"university",
					"intermediate_vocational",
					"college_vocational",
					"in-service",
					"high_school",
					"intermediate_level_pro",
				);
				$list_result = array();
				$list_data_department = [];

				$departement_by_literacy = $this->hr_profile_model->count_staff_by_department_literacy();

				if ($id_department == '') {
					$list_department = $this->hr_profile_model->get_department_by_list_id();

					foreach ($list_diploma as $diploma) {
						$list_data_count = [];
						foreach ($list_department as $department) {

							$count = 0;
							if (isset($departement_by_literacy[$department['departmentid']][$diploma])) {
								$count = (int) $departement_by_literacy[$department['departmentid']][$diploma];
							}
							$list_data_count[] = $count;
						}
						array_push($list_result, array('stack' => _l($diploma), 'data' => $list_data_count));
					}
				} else {
					if (count($department_filter) == 1) {
//one department
						$circle_mode = true;
						$list_department = $this->hr_profile_model->get_department_by_list_id($id_department);
						$list_temp = [];
						$count_total = 0;
						foreach ($list_department as $department) {
							foreach ($list_diploma as $diploma) {
								$count = 0;
								if (isset($departement_by_literacy[$department['departmentid']][$diploma])) {
									$count = (int) $departement_by_literacy[$department['departmentid']][$diploma];
								}

								$count_total += $count;
								$list_temp[] = array('name' => _l($diploma), 'y' => $count);
							}
						}
						foreach ($list_temp as $key => $value) {
							if ($count_total <= 0) {
								$ca_percent = 0;
							} else {
								$ca_percent = ($value['y'] * 100) / $count_total;
							}
							array_push($list_result, array('name' => $value['name'], 'y' => $ca_percent));
						}
					} else {
// multiple deparment
						$list_department = $this->hr_profile_model->get_department_by_list_id($id_department);
						foreach ($list_diploma as $diploma) {
							$list_data_count = [];
							foreach ($list_department as $department) {
								$count = 0;
								if (isset($departement_by_literacy[$department['departmentid']][$diploma])) {
									$count = (int) $departement_by_literacy[$department['departmentid']][$diploma];
								}
								$list_data_count[] = $count;
							}
							array_push($list_result, array('stack' => _l($diploma), 'data' => $list_data_count));
						}

					}
				}
				if (isset($list_department)) {
					foreach ($list_department as $key => $value) {
						$list_data_department[] = $value['name'];
					}
				}
				echo json_encode([
					'department' => $list_data_department,
					'data_result' => $list_result,
					'circle_mode' => $circle_mode,
				]);
				die;
			}
		}
	}

	/**
	 * report by staffs
	 * @return [type]
	 */
	public function report_by_staffs() {
		echo json_encode($this->hr_profile_model->report_by_staffs());
	}

	/**
	 * import job position excel
	 * @return [type]
	 */
	public function import_job_position_excel() {
		if (!class_exists('XLSXReader_fin')) {
			require_once module_dir_path(HR_PROFILE_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
		}
		require_once module_dir_path(HR_PROFILE_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

		$filename = '';
		if ($this->input->post()) {
			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

				$this->delete_error_file_day_before();

				// Get the temp file path
				$tmpFilePath = $_FILES['file_csv']['tmp_name'];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

					if (!file_exists(TEMP_FOLDER)) {
						mkdir(TEMP_FOLDER, 0755);
					}

					if (!file_exists($tmpDir)) {
						mkdir($tmpDir, 0755);
					}

					// Setup our new file path
					$newFilePath = $tmpDir . $_FILES['file_csv']['name'];

					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						//Writer file
						$writer_header = array(
							_l('hr_position_code') => 'string',
							_l('hr_position_name') => 'string',
							_l('hr_job_p_id') => 'string',
							_l('hr_job_descriptions') => 'string',
							_l('department_id') => 'string',
							_l('tags') => 'string',
							_l('error') => 'string',
						);
						$rowstyle[] = array('widths' => [10, 20, 30, 40]);

						$writer = new XLSXWriter();
						$writer->writeSheetHeader('Sheet1', $writer_header, $col_options = ['widths' => [40, 40, 40, 50, 40, 40, 50]]);

						//Reader file
						$xlsx = new XLSXReader_fin($newFilePath);
						$sheetNames = $xlsx->getSheetNames();
						$data = $xlsx->getSheetData($sheetNames[1]);

						$arr_header = [];

						$arr_header['position_code'] = 0;
						$arr_header['position_name'] = 1;
						$arr_header['job_p_id'] = 2;
						$arr_header['job_position_description'] = 3;
						$arr_header['department_id'] = 4;
						$arr_header['tags'] = 5;

						$total_rows = 0;
						$total_row_false = 0;

						for ($row = 1; $row < count($data); $row++) {

							$total_rows++;

							$rd = array();
							$flag = 0;
							$flag2 = 0;

							$string_error = '';
							$flag_position_group;
							$flag_department = null;

							$value_position_code = isset($data[$row][$arr_header['position_code']]) ? $data[$row][$arr_header['position_code']] : '';
							$value_position_name = isset($data[$row][$arr_header['position_name']]) ? $data[$row][$arr_header['position_name']] : '';
							$value_position_group = isset($data[$row][$arr_header['job_p_id']]) ? $data[$row][$arr_header['job_p_id']] : 0;
							$value_description = isset($data[$row][$arr_header['job_position_description']]) ? $data[$row][$arr_header['job_position_description']] : '';
							$value_department = isset($data[$row][$arr_header['department_id']]) ? $data[$row][$arr_header['department_id']] : '';
							$value_tags = isset($data[$row][$arr_header['tags']]) ? $data[$row][$arr_header['tags']] : '';

							if (is_null($value_position_name) == true || $value_position_name == '') {
								$string_error .= _l('hr_position_name') . _l('not_yet_entered');
								$flag = 1;
							}

							//check position group exist  (input: id or name)
							$flag_position_group = 0;
							if (is_null($value_position_group) != true && ($value_position_group != '0')) {
								/*case input id*/
								if (is_numeric($value_position_group)) {

									$this->db->where('job_id', $value_position_group);
									$position_group_id_value = $this->db->count_all_results(db_prefix() . 'hr_job_p');

									if ($position_group_id_value == 0) {
										$string_error .= _l('hr_job_p_id') . _l('does_not_exist');
										$flag2 = 1;
									} else {
										/*get id job_id*/
										$flag_position_group = $value_position_group;
									}

								} else {
									/*case input name*/
									$this->db->like(db_prefix() . 'hr_job_p.job_name', $value_position_group);

									$position_group_id_value = $this->db->get(db_prefix() . 'hr_job_p')->result_array();
									if (count($position_group_id_value) == 0) {
										$string_error .= _l('hr_job_p_id') . _l('does_not_exist');
										$flag2 = 1;
									} else {
										/*get job_id*/
										$flag_position_group = $position_group_id_value[0]['job_id'];
									}
								}

							}
							//check department
							if ($value_department != null && $value_department != '') {
								$department_result = $this->hr_profile_model->check_department_format($value_department);

								if ($department_result['status']) {
									$flag_department = $department_result['result'];
								} else {
									$string_error .= $department_result['result'] . _l('department_name') . _l('does_not_exist');
									$flag2 = 1;
								}

							}

							if (($flag == 1) || $flag2 == 1) {
								//write error file
								$writer->writeSheetRow('Sheet1', [
									$value_position_code,
									$value_position_name,
									$value_position_group,
									$value_description,
									$value_department,
									$value_tags,
									$string_error,
								]);
								$total_row_false++;
							}

							if ($flag == 0 && $flag2 == 0) {

								if ($value_position_code == '') {
									$rd['position_code'] = $this->hr_profile_model->create_code('position_code');
								} else {
									$rd['position_code'] = $value_position_code;
								}

								$rd['position_name'] = $value_position_name;
								$rd['job_p_id'] = $flag_position_group;
								$rd['job_position_description'] = $value_description;
								$rd['department_id'] = $flag_department;
								$rd['tags'] = $value_tags;

								$rows[] = $rd;
								$response = $this->hr_profile_model->add_job_position($rd);
							}

						}

						$total_rows = $total_rows;
						$total_row_success = isset($rows) ? count($rows) : 0;
						$dataerror = '';
						$message = 'Not enought rows for importing';

						if ($total_row_false != 0) {
							$filename = 'Import_job_position_error_' . get_staff_user_id() . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
							$writer->writeToFile(str_replace($filename, HR_PROFILE_ERROR . $filename, $filename));
						}

					}
				}
			}
		}

		if (file_exists($newFilePath)) {
			@unlink($newFilePath);
		}

		echo json_encode([
			'message' => $message,
			'total_row_success' => $total_row_success,
			'total_row_false' => $total_row_false,
			'total_rows' => $total_rows,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id(),
			'filename' => HR_PROFILE_ERROR . $filename,
		]);
	}

	/**
	 * hrm delete bulk action
	 * @return [type]
	 */
	public function hrm_delete_bulk_action() {
		if (!is_staff_member()) {
			ajax_access_denied();
		}

		$total_deleted = 0;

		if ($this->input->post()) {

			$ids = $this->input->post('ids');
			$rel_type = $this->input->post('rel_type');

			/*check permission*/
			switch ($rel_type) {
			case 'hrm_contract':
				if (!has_permission('hrm_contract', '', 'delete') && !is_admin()) {
					access_denied('hr_hr_profile');
				}
				break;

			case 'hrm_staff':
				if (!has_permission('hrm_hr_records', '', 'delete') && !is_admin()) {
					access_denied('hr_hr_profile');
				}
				break;

			case 'hrm_training_library':
				if (!has_permission('staffmanage_training', '', 'delete') && !is_admin()) {
					access_denied('hr_hr_profile');
				}
				break;

			case 'hrm_job_position':
				if (!has_permission('staffmanage_job_position', '', 'delete') && !is_admin()) {
					access_denied('hr_hr_profile');
				}
				break;

			case 'hrm_kb-articles':
				if (!has_permission('hr_manage_q_a', '', 'delete') && !is_admin()) {
					access_denied('hr_hr_profile');
				}
				break;

			case 'hrm_reception_staff':
				if (!has_permission('hrm_reception_staff', '', 'delete') && !is_admin()) {
					access_denied('hr_hr_profile');
				}
				break;

			case 'hrm_resignation_procedures':
				if (!has_permission('hrm_procedures_for_quitting_work', '', 'delete') && !is_admin()) {
					access_denied('hr_hr_profile');
				}
				break;

			default:
				# code...
				break;
			}

			/*delete data*/
			$transfer_data_to = get_staff_user_id();
			if ($this->input->post('mass_delete')) {
				if (is_array($ids)) {
					foreach ($ids as $id) {

						switch ($rel_type) {
						case 'hrm_contract':
							if ($this->hr_profile_model->delete_contract($id)) {
								$total_deleted++;
								break;
							} else {
								break;
							}

						case 'hrm_staff':
							if ($this->hr_profile_model->delete_staff($id, $transfer_data_to)) {
								$total_deleted++;
								break;
							} else {
								break;
							}

						case 'hrm_training_library':
							if ($this->hr_profile_model->delete_position_training($id)) {
								$total_deleted++;
								break;
							} else {
								break;
							}

							break;

						case 'hrm_job_position':
							if ($this->hr_profile_model->delete_job_position($id)) {
								$total_deleted++;
								break;
							} else {
								break;
							}

							break;

						case 'hrm_kb-articles':
							$this->load->model('knowledge_base_q_a_model');

							if ($this->knowledge_base_q_a_model->delete_article($id)) {
								$total_deleted++;
								break;
							} else {
								break;
							}

							break;

						case 'hrm_reception_staff':

							$this->hr_profile_model->delete_manage_info_reception($id);
							$this->hr_profile_model->delete_setting_training($id);
							$this->hr_profile_model->delete_setting_asset_allocation($id);
							$success = $this->hr_profile_model->delete_reception($id);
							if ($success) {
								$total_deleted++;
							} else {
								break;
							}

							break;

						case 'hrm_resignation_procedures':
							$success = $this->hr_profile_model->delete_procedures_for_quitting_work($id);
							if ($success) {
								$total_deleted++;
							} else {
								break;
							}

							break;

						default:
							# code...
							break;
						}

					}
				}

				/*return result*/
				switch ($rel_type) {
				case 'hrm_contract':
					set_alert('success', _l('total_contract_deleted') . ": " . $total_deleted);
					break;

				case 'hrm_staff':
					set_alert('success', _l('total_staff_deleted') . ": " . $total_deleted);
					break;

				case 'hrm_training_library':
					set_alert('success', _l('total_training_libraries_deleted') . ": " . $total_deleted);
					break;

				case 'hrm_job_position':
					set_alert('success', _l('total_job_position_deleted') . ": " . $total_deleted);
					break;

				case 'hrm_kb-articles':
					set_alert('success', _l('total_kb_articles_deleted') . ": " . $total_deleted);
					break;

				case 'hrm_reception_staff':
					set_alert('success', _l('total_reception_staff_deleted') . ": " . $total_deleted);
					break;

				case 'hrm_resignation_procedures':
					set_alert('success', _l('total_layoff_checklist_deleted') . ": " . $total_deleted);
					break;

				default:
					# code...
					break;

				}

			}

		}

	}

	/**
	 * hrm delete bulk action v2
	 * @return [type]
	 * Delete data from ids array, don't use foreach
	 */
	public function hrm_delete_bulk_action_v2() {
		if (!is_staff_member()) {
			ajax_access_denied();
		}

		$total_deleted = 0;

		if ($this->input->post()) {

			$ids = $this->input->post('ids');
			$rel_type = $this->input->post('rel_type');

			/*check permission*/
			switch ($rel_type) {

			case 'hrm_training_program':
				if (!has_permission('staffmanage_training', '', 'delete') && !is_admin()) {
					access_denied('hr_hr_profile');
				}
				break;

			case 'hrm_job':
				if (!has_permission('staffmanage_job_position', '', 'delete') && !is_admin()) {
					access_denied('hr_hr_profile');
				}
				break;

			case 'hrm_dependent_person':
				if (!has_permission('hrm_dependent_person', '', 'delete') && !is_admin()) {
					access_denied('hr_hr_profile');
				}
				break;

			default:
				# code...
				break;
			}

			/*delete data*/
			$transfer_data_to = get_staff_user_id();
			if ($this->input->post('mass_delete')) {
				if (is_array($ids)) {

					switch ($rel_type) {

					case 'hrm_training_program':
						$sql_where = " training_process_id  IN ( '" . implode("', '", $ids) . "' ) ";
						$this->db->where($sql_where);
						$this->db->delete(db_prefix() . 'hr_jp_interview_training');
						$total_deleted = count($ids);
						break;

					case 'hrm_job':
						$sql_where = " job_id  IN ( '" . implode("', '", $ids) . "' ) ";
						$this->db->where($sql_where);
						$this->db->delete(db_prefix() . 'hr_job_p');
						$total_deleted = count($ids);
						break;

					case 'hrm_dependent_person':
						$sql_where = " id  IN ( '" . implode("', '", $ids) . "' ) ";
						$this->db->where($sql_where);
						$this->db->delete(db_prefix() . 'hr_dependent_person');
						$total_deleted = count($ids);
						break;

					default:
						# code...
						break;
					}

				}

				/*return result*/
				switch ($rel_type) {

				case 'hrm_training_program':
					set_alert('success', _l('total_training_program_deleted') . ": " . $total_deleted);
					break;

				case 'hrm_job':
					set_alert('success', _l('total_job_deleted') . ": " . $total_deleted);
					break;

				case 'hrm_dependent_person':
					set_alert('success', _l('total_dependent_person_deleted') . ": " . $total_deleted);
					break;

				default:
					# code...
					break;

				}

			}

		}

	}

	/**
	 * import dependent person excel
	 * @return [type]
	 */
	public function import_dependent_person_excel() {
		if (!class_exists('XLSXReader_fin')) {
			require_once module_dir_path(HR_PROFILE_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
		}
		require_once module_dir_path(HR_PROFILE_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

		$filename = '';
		if ($this->input->post()) {
			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

				$this->delete_error_file_day_before();

				// Get the temp file path
				$tmpFilePath = $_FILES['file_csv']['tmp_name'];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$rows = [];
					$arr_insert = [];

					$tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

					if (!file_exists(TEMP_FOLDER)) {
						mkdir(TEMP_FOLDER, 0755);
					}

					if (!file_exists($tmpDir)) {
						mkdir($tmpDir, 0755);
					}

					// Setup our new file path
					$newFilePath = $tmpDir . $_FILES['file_csv']['name'];

					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						//Writer file
						$writer_header = array(
							_l('hr_hr_code') => 'string',
							_l('hr_dependent_name') => 'string',
							_l('hr_hr_relationship') => 'string',
							_l('hr_dependent_bir') => 'string',
							_l('hr_dependent_iden') => 'string',
							_l('hr_reason_label') => 'string',
							_l('hr_start_month') => 'string',
							_l('hr_end_month') => 'string',
							_l('hr_status_label') => 'string',
							_l('error') => 'string',
						);
						$rowstyle[] = array('widths' => [10, 20, 30, 40]);

						$writer = new XLSXWriter();
						$writer->writeSheetHeader('Sheet1', $writer_header, $col_options = ['widths' => [40, 40, 40, 50, 40, 40, 40, 40, 50, 50]]);

						//Reader file
						$xlsx = new XLSXReader_fin($newFilePath);
						$sheetNames = $xlsx->getSheetNames();
						$data = $xlsx->getSheetData($sheetNames[1]);

						$arr_header = [];

						$arr_header['staffid'] = 0;
						$arr_header['dependent_name'] = 1;
						$arr_header['relationship'] = 2;
						$arr_header['dependent_bir'] = 3;
						$arr_header['dependent_iden'] = 4;
						$arr_header['reason'] = 5;
						$arr_header['start_month'] = 6;
						$arr_header['end_month'] = 7;
						$arr_header['status'] = 8;

						$total_rows = 0;
						$total_row_false = 0;

						for ($row = 1; $row < count($data); $row++) {

							$total_rows++;

							$rd = array();
							$flag = 0;
							$flag2 = 0;

							$string_error = '';
							$flag_position_group;
							$flag_department = null;

							$value_staffid = isset($data[$row][$arr_header['staffid']]) ? $data[$row][$arr_header['staffid']] : '';
							$value_dependent_name = isset($data[$row][$arr_header['dependent_name']]) ? $data[$row][$arr_header['dependent_name']] : '';
							$value_relationship = isset($data[$row][$arr_header['relationship']]) ? $data[$row][$arr_header['relationship']] : '';
							$value_dependent_bir = isset($data[$row][$arr_header['dependent_bir']]) ? $data[$row][$arr_header['dependent_bir']] : '';
							$value_dependent_iden = isset($data[$row][$arr_header['dependent_iden']]) ? $data[$row][$arr_header['dependent_iden']] : '';
							$value_reason = isset($data[$row][$arr_header['reason']]) ? $data[$row][$arr_header['reason']] : '';
							$value_start_month = isset($data[$row][$arr_header['start_month']]) ? $data[$row][$arr_header['start_month']] : '';
							$value_end_month = isset($data[$row][$arr_header['end_month']]) ? $data[$row][$arr_header['end_month']] : '';
							$value_status = isset($data[$row][$arr_header['status']]) ? $data[$row][$arr_header['status']] : '';

							/*check null*/
							if (is_null($value_staffid) == true) {
								$string_error .= _l('hr_hr_code') . _l('not_yet_entered');
								$flag = 1;
							}

							$flag_staff_id = 0;
							//check hr_code exist
							if (is_null($value_staffid) != true) {
								$this->db->where('staff_identifi', $value_staffid);
								$hrcode = $this->db->get(db_prefix() . 'staff')->row();
								if ($hrcode) {
									$flag_staff_id = $hrcode->staffid;
								} else {
									$string_error .= _l('hr_hr_code') . _l('does_not_exist');
									$flag2 = 1;
								}

							}

							//check start_time
							if (is_null($value_dependent_bir) != true && $value_dependent_bir != '') {

								if (is_null($value_dependent_bir) != true) {

									if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", trim($value_dependent_bir, " "))) {
										$test = true;

									} else {
										$flag2 = 1;
										$string_error .= _l('hr_dependent_bir') . _l('invalid');
									}
								}
							}

							//check start_time
							if (is_null($value_start_month) != true && $value_start_month != '') {

								if (is_null($value_start_month) != true) {

									if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", trim($value_start_month, " "))) {
										$test = true;

									} else {
										$flag2 = 1;
										$string_error .= _l('hr_start_month') . _l('invalid');
									}
								}
							}

							if (is_null($value_end_month) != true && $value_end_month != '') {

								if (is_null($value_end_month) != true) {

									if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", trim($value_end_month, " "))) {
										$test = true;

									} else {
										$flag2 = 1;
										$string_error .= _l('hr_end_month') . _l('invalid');
									}
								}
							}

							if (($flag == 1) || $flag2 == 1) {
								//write error file
								$writer->writeSheetRow('Sheet1', [
									$value_staffid,
									$value_dependent_name,
									$value_relationship,
									$value_dependent_bir,
									$value_dependent_iden,
									$value_reason,
									$value_start_month,
									$value_end_month,
									$value_status,
									$string_error,
								]);

								// $numRow++;
								$total_row_false++;
							}

							if ($flag == 0 && $flag2 == 0) {

								if (is_numeric($value_status) && ($value_status == '2')) {
									/*reject*/
									$rd['status'] = 2;
								} else {
									/*approval*/
									$rd['status'] = 1;
								}

								$rd['staffid'] = $flag_staff_id;
								$rd['dependent_name'] = $value_dependent_name;
								$rd['relationship'] = $value_relationship;
								$rd['dependent_bir'] = $value_dependent_bir;
								$rd['dependent_iden'] = $value_dependent_iden;
								$rd['reason'] = $value_reason;
								$rd['start_month'] = $value_start_month;
								$rd['end_month'] = $value_end_month;

								$rows[] = $rd;
								array_push($arr_insert, $rd);

							}

						}

						//insert batch
						if (count($arr_insert) > 0) {

							$this->db->insert_batch(db_prefix() . 'hr_dependent_person', $arr_insert);
						}

						$total_rows = $total_rows;
						$total_row_success = isset($rows) ? count($rows) : 0;
						$dataerror = '';
						$message = 'Not enought rows for importing';

						if ($total_row_false != 0) {
							$filename = 'Import_dependent_person_error_' . get_staff_user_id() . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
							$writer->writeToFile(str_replace($filename, HR_PROFILE_ERROR . $filename, $filename));
						}

					}
				}
			}
		}

		if (file_exists($newFilePath)) {
			@unlink($newFilePath);
		}

		echo json_encode([
			'message' => $message,
			'total_row_success' => $total_row_success,
			'total_row_false' => $total_row_false,
			'total_rows' => $total_rows,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id(),
			'filename' => HR_PROFILE_ERROR . $filename,
		]);
	}

	/**
	 * reset data
	 * @return [type]
	 */
	public function reset_data() {

		if (!is_admin()) {
			access_denied('hr_profile');
		}
		//delete Onboarding process
		$this->db->truncate(db_prefix() . 'hr_rec_transfer_records');
		$this->db->truncate(db_prefix() . 'hr_group_checklist_allocation');
		$this->db->truncate(db_prefix() . 'hr_allocation_asset');
		$this->db->truncate(db_prefix() . 'hr_training_allocation');

		//delete Training
		$this->db->truncate(db_prefix() . 'hr_jp_interview_training');
		$this->db->truncate(db_prefix() . 'hr_position_training');
		$this->db->truncate(db_prefix() . 'hr_position_training_question_form');
		$this->db->truncate(db_prefix() . 'hr_p_t_form_question_box');
		$this->db->truncate(db_prefix() . 'hr_p_t_form_question_box_description');
		$this->db->truncate(db_prefix() . 'hr_p_t_form_results');
		$this->db->truncate(db_prefix() . 'hr_p_t_surveyresultsets');

		//delete contracs, file type "hr_contract"
		$this->db->truncate(db_prefix() . 'hr_staff_contract_detail');
		$this->db->truncate(db_prefix() . 'hr_staff_contract');

		//delete dependent persons
		$this->db->truncate(db_prefix() . 'hr_dependent_person');

		//delete Resignation procedures
		$this->db->truncate(db_prefix() . 'hr_list_staff_quitting_work');
		$this->db->truncate(db_prefix() . 'hr_procedure_retire_of_staff');

		//delete Q&A
		$this->db->truncate(db_prefix() . 'hr_knowledge_base');
		$this->db->truncate(db_prefix() . 'hr_knowledge_base_groups');
		$this->db->truncate(db_prefix() . 'hr_knowedge_base_article_feedback');
		$this->db->truncate(db_prefix() . 'hr_views_tracking');

		//delete sub folder contract
		foreach (glob(HR_PROFILE_CONTRACT_ATTACHMENTS_UPLOAD_FOLDER . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (is_dir($file)) {
				delete_dir(HR_PROFILE_CONTRACT_ATTACHMENTS_UPLOAD_FOLDER . $filename);
			}
		}

		//delete sub folder Q_A_ATTACHMENTS
		foreach (glob(HR_PROFILE_Q_A_ATTACHMENTS_UPLOAD_FOLDER . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (is_dir($file)) {
				delete_dir(HR_PROFILE_Q_A_ATTACHMENTS_UPLOAD_FOLDER . $filename);
			}
		}

		//delete file error response
		foreach (glob('modules/hr_profile/uploads/file_error_response/' . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (is_dir($file)) {
				delete_dir('modules/hr_profile/uploads/file_error_response/' . $filename);
			}
		}

		//delete file
		$this->db->where('rel_type', 'staff_contract');
		$this->db->or_where('rel_type', 'kb_article_files');
		$this->db->delete(db_prefix() . 'files');

		set_alert('success', _l('reset_data_successful'));
		redirect(admin_url('hr_profile/setting?group=reset_data'));

	}

	/**
	 * table training program
	 * @return [type]
	 */
	public function table_training_program() {
		$this->app->get_table_data(module_views_path('hr_profile', 'training/job_position_manage/training_programs_table'));
	}

	/**
	 * table training result
	 * @return [type]
	 */
	public function table_training_result() {
		$this->app->get_table_data(module_views_path('hr_profile', 'training/job_position_manage/training_result_table'));
	}

	/**
	 * training table
	 * @return [type]
	 */
	public function training_libraries_table() {
		$this->app->get_table_data(module_views_path('hr_profile', 'training/job_position_manage/training_table'));
	}

	/**
	 * type of training
	 * @param  string $id
	 * @return [type]
	 */
	public function type_of_training($id = '') {
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();

			if (!$this->input->post('id')) {
				$id = $this->hr_profile_model->add_type_of_training($data);
				if ($id) {
					$success = true;
					$message = _l('added_successfully', _l('hr_type_of_training'));
				}
				echo json_encode([
					'success' => $success,
					'message' => $message,
				]);
				redirect(admin_url('hr_profile/setting?group=type_of_training'));
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->hr_profile_model->update_type_of_training($data, $id);
				if ($success) {
					$message = _l('updated_successfully', _l('hr_type_of_training'));
					set_alert('success', $message);
				} else {
					$message = _l('hr_updated_failed', _l('hr_allowance_type'));
					set_alert('warning', $message);
				}
				redirect(admin_url('hr_profile/setting?group=type_of_training'));
			}
			die;
		}
	}

	/**
	 * delete type of training
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_type_of_training($id) {
		if (!$id) {
			redirect(admin_url('hr_profile/setting?group=type_of_training'));
		}

		if (!has_permission('hrm_setting', '', 'delete') && !is_admin()) {
			access_denied('hr_profile');
		}

		$response = $this->hr_profile_model->delete_type_of_training($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('hr_is_referenced', _l('hr_type_of_training')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('hr_type_of_training')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('hr_type_of_training')));
		}
		redirect(admin_url('hr_profile/setting?group=type_of_training'));
	}

	/**
	 * get training program by type
	 * @return [type]
	 */
	public function get_training_program_by_type() {
		if ($this->input->is_ajax_request()) {
			$data = $this->input->post();
			if ($data['training_type'] == '') {
				$training_type = 0;
			} else {
				$training_type = $data['training_type'];
			}

			$training_program_option = '';
			$list_staff = $this->hr_profile_model->get_staff_info_id($data['staff_id']);
			if ($list_staff) {
				$training_program_option = $this->hr_profile_model->get_list_training_program($list_staff->job_position, $training_type);
			}

			echo json_encode([
				'training_program_html' => $training_program_option,
			]);
		}
	}

	/**
	 * view training program
	 * @param  string $id
	 * @return [type]
	 */
	public function view_training_program($id = '') {
		if (!has_permission('staffmanage_training', '', 'view') && !has_permission('staffmanage_training', '', 'view_own')) {
			access_denied('view_training_program');
		}

		//load deparment by manager
		if (!is_admin() && !has_permission('staffmanage_training', '', 'view')) {
			//View own
			$array_staff = $this->hr_profile_model->get_staff_by_manager();
		}

		$data['title'] = _l('view_training_program');
		$data['training_program'] = $this->hr_profile_model->get_job_position_training_de($id);

		if (!$data['training_program']) {
			blank_page('Training program Not Found', 'danger');
		}

		$data['training_results'] = $this->hr_profile_model->get_training_result_by_training_program($id);
		if (isset($array_staff)) {
			foreach ($data['training_results'] as $key => $value) {
				if (!in_array($value['staff_id'], $array_staff)) {
					unset($data['training_results'][$key]);
				}
			}
		}

		$this->load->view('hr_profile/training/view_training_program', $data);
	}

	/* Get role permission for specific role id */
	public function hr_role_changed($id) {
		echo json_encode($this->roles_model->get($id)->permissions);
	}

	/**
	 * create staff excel file
	 * @return [type]
	 */
	public function create_staff_sample_file() {
		$this->load->model('departments_model');

		$data = $this->input->post();

		if (!class_exists('XLSXReader_fin')) {
			require_once module_dir_path(HR_PROFILE_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
		}
		require_once module_dir_path(HR_PROFILE_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

		$this->delete_error_file_day_before('1', HR_PROFILE_CREATE_EMPLOYEES_SAMPLE);

		if (isset($data['sample_file'])) {

			$staffs = [];
		} else {

			//get list staff by id
			$this->db->where('staffid  IN (' . implode(",", $data['ids']) . ') ');
			$staffs = $this->db->get(db_prefix() . 'staff')->result_array();
		}

		$header_key = [
			'staffid',
			'staff_identifi', //*
			'firstname', //*
			'lastname', //*
			'sex',
			'birthday',
			'email', //*
			'phonenumber',
			'workplace',
			'status_work', //*
			'job_position', //*
			'team_manage',
			'role',
			'literacy',
			'hourly_rate',
			'department',
			'password',
			'home_town', //text
			'marital_status',
			'current_address',
			'nation',
			'birthplace',
			'religion',
			'identification',
			'days_for_identity',
			'place_of_issue',
			'resident',
			'account_number',
			'name_account',
			'issue_bank',
			'Personal_tax_code',
			'facebook',
			'linkedin',
			'skype',
		];

		$header_label = [
			'id',
			'hr_staff_code', //*
			'hr_firstname', //*
			'hr_lastname', //*
			'hr_sex',
			'hr_hr_birthday',
			'Email', //*
			'staff_add_edit_phonenumber',
			'hr_hr_workplace',
			'hr_status_work', //*
			'hr_hr_job_position', //*
			'hr_team_manage',
			'staff_add_edit_role',
			'hr_hr_literacy',
			'staff_hourly_rate',
			'staff_add_edit_departments',
			'staff_add_edit_password',
			'hr_hr_home_town', //text
			'hr_hr_marital_status',
			'hr_current_address',
			'hr_hr_nation',
			'hr_hr_birthplace',
			'hr_hr_religion',
			'hr_citizen_identification',
			'hr_license_date',
			'hr_hr_place_of_issue',
			'hr_hr_resident',
			'hr_bank_account_number',
			'hr_bank_account_name',
			'hr_bank_name',
			'hr_Personal_tax_code',
			'staff_add_edit_facebook',
			'staff_add_edit_linkedin',
			'staff_add_edit_skype',
		];

		//Writer file
		//create header value
		$writer_header = [];
		$widths = [];

		$widths[] = 30;

		foreach ($header_label as $header_value) {
			$writer_header[_l($header_value)] = 'string';
			$widths[] = 30;
		}

		$writer = new XLSXWriter();

		//orange: do not update
		$col_style1 = [0, 1];
		$style1 = ['widths' => $widths, 'fill' => '#fc2d42', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

		//red: required
		$col_style2 = [2, 3, 6, 9, 10];
		$style2 = ['widths' => $widths, 'fill' => '#ff9800', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

		//otherwise blue: can be update

		$writer->writeSheetHeader_v2('Sheet1', $writer_header, $col_options = ['widths' => $widths, 'fill' => '#03a9f46b', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13],
			$col_style1, $style1, $col_style2, $style2);

		$row_style1 = array('fill' => '#F8CBAD', 'height' => 25, 'border' => 'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');
		$row_style2 = array('fill' => '#FCE4D6', 'height' => 25, 'border' => 'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');

		//job position data
		$job_position_data = [];
		$job_positions = $this->hr_profile_model->get_job_position();

		foreach ($job_positions as $key => $job_position) {
			$job_position_data[$job_position['position_id']] = $job_position;
		}

		//direct manager
		$staff_data = [];
		$list_staffs = $this->hr_profile_model->get_staff();
		foreach ($list_staffs as $key => $list_staff) {
			$staff_data[$list_staff['staffid']] = $list_staff;
		}

		//get role data
		$role_data = [];
		$this->load->model('role/roles_model');
		$list_roles = $this->roles_model->get();

		foreach ($list_roles as $key => $list_role) {
			$role_data[$list_role['roleid']] = $list_role;
		}

		//get workplace data
		$workplace_data = [];
		$list_workplaces = $this->hr_profile_model->get_workplace();

		foreach ($list_workplaces as $key => $list_workplace) {
			$workplace_data[$list_workplace['id']] = $list_workplace;
		}

		//write the next row (row2)
		$writer->writeSheetRow('Sheet1', $header_key);

		foreach ($staffs as $staff_key => $staff_value) {

			$arr_department = $this->hr_profile_model->get_staff_departments($staff_value['staffid'], true);

			$list_department = '';
			if (count($arr_department) > 0) {

				foreach ($arr_department as $key => $department) {
					$department_value = $this->departments_model->get($department);

					if ($department_value) {
						if (strlen($list_department) != 0) {
							$list_department .= ';' . $department_value->name;
						} else {
							$list_department .= $department_value->name;
						}
					}
				}
			}

			$temp = [];

			foreach ($header_key as $_key) {
				if ($_key == 'password') {
					$temp[] = '';
				} elseif ($_key == 'department') {
					$temp[] = $list_department;

				} elseif ($_key == 'job_position') {
					$temp[] = isset($job_position_data[$staff_value['job_position']]) ? $job_position_data[$staff_value['job_position']]['position_code'] : '';

				} elseif ($_key == 'team_manage') {
					$temp[] = isset($staff_data[$staff_value['team_manage']]) ? $staff_data[$staff_value['team_manage']]['staff_identifi'] : '';

				} elseif ($_key == 'role') {
					$temp[] = isset($role_data[$staff_value['role']]) ? $role_data[$staff_value['role']]['name'] : '';

				} elseif ($_key == 'workplace') {
					$temp[] = isset($workplace_data[$staff_value['workplace']]) ? $workplace_data[$staff_value['workplace']]['name'] : '';

				} else {
					$temp[] = isset($staff_value[$_key]) ? $staff_value[$_key] : '';
				}
			}

			if (($staff_key % 2) == 0) {
				$writer->writeSheetRow('Sheet1', $temp, $row_style1);
			} else {
				$writer->writeSheetRow('Sheet1', $temp, $row_style2);
			}

		}

		$filename = 'employees_sample_file' . get_staff_user_id() . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
		$writer->writeToFile(str_replace($filename, HR_PROFILE_CREATE_EMPLOYEES_SAMPLE . $filename, $filename));

		echo json_encode([
			'success' => true,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id(),
			'filename' => HR_PROFILE_CREATE_EMPLOYEES_SAMPLE . $filename,
		]);

	}

	//test view PDF file: TODO
	public function view_pdf() {
		$data = [];
		$this->load->view('hr_profile/contracts/view_contract_pdf', $data);
	}

	/**
	 * save contract data
	 * @return [type]
	 */
	public function save_hr_contract_data() {
		if (!has_permission('hrm_contract', '', 'edit')) {
			header('HTTP/1.0 400 Bad error');
			echo json_encode([
				'success' => false,
				'message' => _l('access_denied'),
			]);
			die;
		}

		$success = false;
		$message = '';

		$this->db->where('id_contract', $this->input->post('contract_id'));
		$this->db->update(db_prefix() . 'hr_staff_contract', [
			'content' => html_purify($this->input->post('content', false)),
		]);

		$success = $this->db->affected_rows() > 0;
		$message = _l('updated_successfully', _l('contract'));

		echo json_encode([
			'success' => $success,
			'message' => $message,
		]);
	}

	/**
	 * hr clear signature
	 * @param  [type] $id
	 * @return [type]
	 */
	public function hr_clear_signature($id) {
		if (has_permission('hrm_contract', '', 'delete')) {
			$this->hr_profile_model->contract_clear_signature($id);
		}

		redirect(admin_url('hr_profile/contracts#' . $id));
	}

	/**
	 * contract pdf
	 * @param  [type] $id
	 * @return [type]
	 */
	public function contract_pdf($id) {
		if (!has_permission('hrm_contract', '', 'view') && !has_permission('hrm_contract', '', 'view_own')) {
			access_denied('contracts');
		}

		if (!$id) {
			redirect(admin_url('hr_profile/hrm_contract'));
		}

		$contract = $this->hr_profile_model->hr_get_staff_contract_pdf_only_for_pdf($id);

		try {
			$pdf = hr_contract_pdf($contract);
		} catch (Exception $e) {
			echo $e->getMessage();
			die;
		}

		$type = 'D';
		if ($this->input->get('output_type')) {
			$type = $this->input->get('output_type');
		}

		if ($this->input->get('print')) {
			$type = 'I';
		}

		$pdf->Output(slug_it($contract->contract_code) . '.pdf', $type);
	}

	/**
	 * contract sign
	 * @param  [type] $id
	 * @return [type]
	 */
	public function contract_sign($id) {
		$contract = $this->hr_profile_model->hr_get_staff_contract_pdf($id);

		if (!$contract) {
			show_404();
		}

		if ($this->input->post()) {

			if ($this->input->post('sign_by') == 'company') {
				process_digital_signature_image($this->input->post('signature', false), HR_PROFILE_CONTRACT_SIGN . $id);
				$get_acceptance_info_array = get_acceptance_info_array();

				$this->db->where('id_contract', $id);
				$this->db->update(db_prefix() . 'hr_staff_contract', ['signature' => $get_acceptance_info_array['signature'], 'signer' => get_staff_user_id(), 'sign_day' => date('Y-m-d')]);
			} else {

				hr_process_digital_signature_image($this->input->post('signature', false), HR_PROFILE_CONTRACT_SIGN . $id);
				$get_acceptance_info_array = get_acceptance_info_array();

				$this->db->where('id_contract', $id);
				$this->db->update(db_prefix() . 'hr_staff_contract', ['staff_signature' => $get_acceptance_info_array['signature'], 'staff_sign_day' => date('Y-m-d')]);

			}

			// Notify contract creator that customer signed the contract
			// send_contract_signed_notification_to_staff($id);

			set_alert('success', _l('document_signed_successfully'));
			redirect($_SERVER['HTTP_REFERER']);

		}

		$data['title'] = $contract->contract_code;

		$data['contract'] = $contract;
		$data['bodyclass'] = 'contract contract-view';

		$data['identity_confirmation_enabled'] = true;
		$data['bodyclass'] .= ' identity-confirmation';

		$this->load->view('hr_profile/contracts/contracthtml', $data);
	}

	/**
	 * contract template
	 * @param  string $id
	 * @return [type]
	 */
	public function contract_template($id = '') {

		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();

			$data['content'] = $this->input->post('mce_0', false);

			if (isset($data['mce_0'])) {
				unset($data['mce_0']);
			}

			if ($id == '') {
				$id = $this->hr_profile_model->add_contract_template($data);

				if ($id) {
					$message = _l('added_successfully', _l('contract_template'));
					set_alert('success', $message);
				} else {
					$message = _l('added_failed', _l('contract_template'));
					set_alert('warning', $message);
				}

				redirect(admin_url('hr_profile/setting?group=contract_template'));
			} else {

				$success = $this->hr_profile_model->update_contract_template($data, $id);

				if ($success) {
					$message = _l('updated_successfully', _l('contract_template'));
					set_alert('success', $message);

				} else {
					$message = _l('update_failed', _l('contract_template'));
					set_alert('warning', $message);
				}

				redirect(admin_url('hr_profile/setting?group=contract_template'));
			}

		}
		$data = [];

		if ($id == '') {
			//add
			$title = _l('add_contract_template');
			$data['title'] = $title;

		} else {
			//update
			$title = _l('update_contract_template');
			$data['title'] = $title;
			$data['contract_template'] = $this->hr_profile_model->get_contract_template($id);
		}

		$data['job_positions'] = $this->hr_profile_model->get_job_position();
		$data['contract_merge_fields'] = $this->app_merge_fields->get_flat('hr_contract', ['other'], '{email_signature}');

		$this->load->view('hr_profile/includes/contract_template_detail', $data);

	}

	/**
	 * delete contract template
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_contract_template_($id) {
		if (!$id) {
			redirect(admin_url('hr_profile/setting?group=contract_template'));
		}
		$response = $this->hr_profile_model->delete_contract_template($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('hr_is_referenced', _l('contract_template')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('contract_template')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('contract_template')));
		}
		redirect(admin_url('hr_profile/setting?group=contract_template'));
	}

//end file
}
