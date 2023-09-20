<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Hr_contract_merge_fields extends App_merge_fields
{
	public function build()
	{
		return [
			//staff infor
			[
				'name'      => 'Staff Code',
				'key'       => '{staff_code}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Staff First Name',
				'key'       => '{staff_firstname}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Staff Last Name',
				'key'       => '{staff_lastname}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Staff Gender',
				'key'       => '{staff_gender}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Staff Birthday',
				'key'       => '{staff_birthday}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Staff Email',
				'key'       => '{staff_email}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Staff Phone',
				'key'       => '{staff_phone}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Staff workplace',
				'key'       => '{staff_workplace}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Staff Job Position',
				'key'       => '{staff_jobposition}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Staff Role',
				'key'       => '{staff_role}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Department Name',
				'key'       => '{staff_deparment_name}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Staff Domicile',
				'key'       => '{staff_Domicile}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Staff Current Address',
				'key'       => '{staff_current_address}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Staff Nation',
				'key'       => '{staff_nation}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Staff Place of Birth',
				'key'       => '{staff_place_of_birth}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Staff Religion',
				'key'       => '{staff_religion}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Staff Citizen Identification',
				'key'       => '{staff_citizen_identification}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Staff Date of Issue',
				'key'       => '{staff_date_of_issue}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Staff Resident',
				'key'       => '{staff_resident}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Staff Personal Tax Code',
				'key'       => '{staff_personal_tax_code}',
				'available' => [
					'hr_contract',
				],
			],


				//contract
			[
				'name'      => 'Contract Code',
				'key'       => '{contract_code}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Contract Type',
				'key'       => '{contract_type}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Contract Status',
				'key'       => '{contract_status}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Contract Effective Date',
				'key'       => '{contract_effective_date}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Contract Expiration date',
				'key'       => '{contract_expiration_date}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Salary and allowance by hourly rate or month',
				'key'       => '{hourly_or_month}',
				'available' => [
					'hr_contract',
				],
			],
			[
				'name'      => 'Salary and Allowance',
				'key'       => '{salary_and_allowance}',
				'available' => [
					'hr_contract',
				],
			],



		];
	}

	/**
	 * Merge field for contracts
	 * @param  mixed $contract_id contract id
	 * @return array
	 */
	public function format($contract_id)
	{
		$fields = [];

		$this->ci->db->select('*');
        $this->ci->db->where('id_contract', $contract_id);
        $this->ci->db->join(db_prefix() . 'hr_staff_contract_type', '' . db_prefix() . 'hr_staff_contract_type.id_contracttype = ' . db_prefix() . 'hr_staff_contract.name_contract', 'left');
        $this->ci->db->join(db_prefix() . 'staff', '' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'hr_staff_contract.staff');
		$contract = $this->ci->db->get(db_prefix() . 'hr_staff_contract')->row();



		if (!$contract) {
			return $fields;
		}

		$currency = get_base_currency();


		$fields['{staff_code}'] 				=  $contract->staff_identifi;
		$fields['{staff_firstname}'] 			=  $contract->firstname;
		$fields['{staff_lastname}'] 		=  $contract->lastname;
		$fields['{staff_gender}'] 				=  $contract->sex;
		$fields['{staff_birthday}'] 				=  $contract->birthday;
		$fields['{staff_email}'] 				=  $contract->email;
		$fields['{staff_phone}'] 				=  $contract->phonenumber;
		$fields['{staff_workplace}'] 			=  hr_profile_get_workplace_name($contract->workplace);
		$fields['{staff_jobposition}'] 			=  hr_profile_get_job_position_name($contract->job_position);
		$fields['{staff_role}'] 				=  hr_get_role_name($contract->role);
		$fields['{staff_deparment_name}'] 		=  get_staff_department_names($contract->staffid);
		$fields['{staff_Domicile}'] 			=  $contract->home_town;
		$fields['{staff_current_address}'] 		=  $contract->current_address;
		$fields['{staff_nation}'] 				=  $contract->nation;
		$fields['{staff_place_of_birth}'] 		=  $contract->birthplace;
		$fields['{staff_religion}'] 			=  $contract->religion;
		$fields['{staff_citizen_identification}'] =  $contract->identification;
		$fields['{staff_date_of_issue}'] 		=  _d($contract->days_for_identity);
		$fields['{staff_resident}'] 			=  $contract->resident;
		$fields['{staff_personal_tax_code}'] 	=  $contract->Personal_tax_code;
		$fields['{contract_code}'] 				=  $contract->contract_code;
		$fields['{contract_type}'] 				=  hr_get_contract_type($contract->name_contract);
		$fields['{contract_status}'] 			=  $contract->contract_status;
		$fields['{contract_effective_date}'] 	=  _d($contract->start_valid);
		$fields['{contract_expiration_date}'] 	=  _d($contract->end_valid);
		$fields['{hourly_or_month}'] 			=  $contract->hourly_or_month;
		$fields['{salary_and_allowance}'] 		=  hr_render_salary_table($contract_id);

		
		return hooks()->apply_filters('hr_contract_merge_fields', $fields, [
			'id'       => $contract_id,
			'contract' => $contract,
		]);
	}
}
