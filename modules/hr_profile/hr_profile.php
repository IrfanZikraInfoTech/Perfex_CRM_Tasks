<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: HR Records
Description: The primary function of HR Records is to provide a central database containing records for all employees past and presen
Version: 1.0.5
Requires at least: 2.3.*
Author: GreenTech Solutions
Author URI: https://codecanyon.net/user/greentech_solutions
*/

define('HR_PROFILE_MODULE_NAME', 'hr_profile');
define('HR_PROFILE_MODULE_UPLOAD_FOLDER', module_dir_path(HR_PROFILE_MODULE_NAME, 'uploads'));
define('HR_PROFILE_CONTRACT_ATTACHMENTS_UPLOAD_FOLDER', module_dir_path(HR_PROFILE_MODULE_NAME, 'uploads/contracts/'));
define('HR_PROFILE_JOB_POSIITON_ATTACHMENTS_UPLOAD_FOLDER', module_dir_path(HR_PROFILE_MODULE_NAME, 'uploads/job_position/'));
define('HR_PROFILE_Q_A_ATTACHMENTS_UPLOAD_FOLDER', module_dir_path(HR_PROFILE_MODULE_NAME, 'uploads/q_a/'));
define('HR_PROFILE_FILE_ATTACHMENTS_UPLOAD_FOLDER', module_dir_path(HR_PROFILE_MODULE_NAME, 'uploads/att_file/'));
define('HR_PROFILE_IMAGE_UPLOAD_FOLDER', 'uploads/staff_profile_images/');
define('HR_PROFILE_PATH', 'modules/hr_profile/uploads/');
define('HR_PROFILE_ERROR', 'modules/hr_profile/uploads/file_error_response/');
define('HR_PROFILE_CREATE_EMPLOYEES_SAMPLE', 'modules/hr_profile/uploads/employees_sample_file/');
define('HR_PROFILE_CONTRACT_SIGN', 'modules/hr_profile/uploads/contract_sign/');


register_merge_fields('hr_profile/merge_fields/hr_contract_merge_fields');
hooks()->add_filter('other_merge_fields_available_for', 'hr_contract_register_other_merge_fields');

hooks()->add_action('admin_init', 'hr_profile_permissions');
hooks()->add_action('app_admin_head', 'hr_profile_add_head_components');
hooks()->add_action('app_admin_footer', 'hr_profile_load_js');
hooks()->add_action('app_search', 'hr_profile_load_search');
hooks()->add_action('admin_init', 'hr_profile_module_init_menu_items');
//add hook render profile icon on header menu
hooks()->add_action('admin_navbar_start', 'render_my_profile_icon');


define('VERSION_HR_PROFILE', 105);

/**
* Register activation module hook
*/
register_activation_hook(HR_PROFILE_MODULE_NAME, 'hr_profile_module_activation_hook');

function hr_profile_module_activation_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/install.php');
}


/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(HR_PROFILE_MODULE_NAME, [HR_PROFILE_MODULE_NAME]);


$CI = & get_instance();
$CI->load->helper(HR_PROFILE_MODULE_NAME . '/hr_profile');

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function hr_profile_module_init_menu_items()
{   
	 $CI = &get_instance();

	 if(has_permission('hrm_dashboard','','view') || has_permission('staffmanage_orgchart','','view') || has_permission('hrm_reception_staff','','view') || has_permission('hrm_hr_records','','view') || has_permission('staffmanage_job_position','','view') || has_permission('staffmanage_training','','view') || has_permission('hr_manage_q_a','','view') || has_permission('hrm_contract','','view') || has_permission('hrm_dependent_person','','view') || has_permission('hrm_procedures_for_quitting_work','','view') || has_permission('hrm_report','','view') || has_permission('hrm_setting','','view')|| has_permission('staffmanage_orgchart','','view_own') || has_permission('staffmanage_job_position','','view_own') || has_permission('hrm_reception_staff','','view_own') || has_permission('hrm_hr_records','','view_own') || has_permission('staffmanage_training','','view_own')  || has_permission('hrm_contract','','view_own') || has_permission('hrm_dependent_person','','view_own') || has_permission('hrm_procedures_for_quitting_work','','view_own')){
	 	
	 	$CI->app_menu->add_sidebar_menu_item('hr_profile', [
	 		'name'     => _l('hr_hr_profile'),
	 		'icon'     => 'fa fa-users', 
	 		'position' => 5,
	 	]);
	 }

	 if(has_permission('hrm_dashboard','','view')){
		 $CI->app_menu->add_sidebar_children_item('hr_profile', [
			'slug'     => 'hr_profile_dashboard',
			'name'     => _l('hr_dashboard'),
			'icon'     => 'fa fa-dashboard',
			'href'     => admin_url('hr_profile/dashboard'),
			'position' => 1,
		]);
	 }


	 if(has_permission('staffmanage_orgchart','','view') || has_permission('staffmanage_orgchart','','view_own')){
		 $CI->app_menu->add_sidebar_children_item('hr_profile', [
			'slug'     => 'hr_profile_organizational_chart',
			'name'     => _l('hr_organizational_chart'),
			'icon'     => 'fa fa-th-list',
			'href'     => admin_url('hr_profile/organizational_chart'),
			'position' => 3,
		]);
	 }

	 if(has_permission('hrm_reception_staff','','view') || has_permission('hrm_reception_staff','','view_own')){
		 $CI->app_menu->add_sidebar_children_item('hr_profile', [
			'slug'     => 'hr_profile_reception_of_staff',
			'name'     => _l('hr_receiving_staff_lable'),
			'icon'     => 'fa fa-edit',
			'href'     => admin_url('hr_profile/reception_staff'),
			'position' => 3,
		]);
	 }

	 if(has_permission('hrm_hr_records','','view') || has_permission('hrm_hr_records','','view_own')){
		 $CI->app_menu->add_sidebar_children_item('hr_profile', [
			'slug'     => 'hr_profile_hr_records',
			'name'     => _l('hr_hr_records'),
			'icon'     => 'fa fa-user',
			'href'     => admin_url('hr_profile/staff_infor'),
			'position' => 4,
		]);
	 }

	 if(has_permission('staffmanage_job_position','','view') || has_permission('staffmanage_job_position','','view_own')){
		 $CI->app_menu->add_sidebar_children_item('hr_profile', [
			'slug'     => 'hr_profile_job_position_manage',
			'name'     => _l('hr_job_descriptions'),
			'icon'     => 'fa fa-map-pin',
			'href'     => admin_url('hr_profile/job_positions'),
			'position' => 2,
		]);
	 }

	 if(has_permission('staffmanage_training','','view') || has_permission('staffmanage_training','','view_own')){
		 $CI->app_menu->add_sidebar_children_item('hr_profile', [
			'slug'     => 'hr_profile_training',
			'name'     => _l('hr_training'),
			'icon'     => 'fa fa-graduation-cap',
			'href'     => admin_url('hr_profile/training?group=training_program'),
			'position' => 5,
		]);
	 }

	 if(has_permission('hr_manage_q_a','','view')){
		$CI->app_menu->add_sidebar_children_item('hr_profile', [
			'slug'     => 'hr_profile_q_a',
			'name'     => _l('hr_q_a'),
			'icon'     => 'fa fa-question-circle',
			'href'     => admin_url('hr_profile/knowledge_base_q_a'),
			'position' => 9,
		]);
	}

	if(has_permission('hrm_contract','','view') || has_permission('hrm_contract','','view_own')){
		$CI->app_menu->add_sidebar_children_item('hr_profile', [
			'slug'     => 'hr_profile_contract',
			'name'     => _l('hr_hr_contracts'),
			'icon'     => 'fa fa-wpforms',
			'href'     => admin_url('hr_profile/contracts'),
			'position' => 6,
		]);
	}

	if(has_permission('hrm_dependent_person','','view') || has_permission('hrm_dependent_person','','view_own')){
		$CI->app_menu->add_sidebar_children_item('hr_profile', [
			'slug'     => 'hr_profile_dependent_person',
			'name'     => _l('hr_dependent_persons'),
			'icon'     => 'fa fa-address-card-o',
			'href'     => admin_url('hr_profile/dependent_persons'),
			'position' => 7,
		]);
	}

	if(has_permission('hrm_procedures_for_quitting_work','','view') || has_permission('hrm_procedures_for_quitting_work','','view_own')){
		$CI->app_menu->add_sidebar_children_item('hr_profile', [
			'slug'     => 'hr_profile_quitting_works',
			'name'     => _l('hr_resignation_procedures'),
			'icon'     => 'fa fa-user-times',
			'href'     => admin_url('hr_profile/resignation_procedures'),
			'position' => 8,
		]);
	}

	if(has_permission('hrm_report','','view')){
		$CI->app_menu->add_sidebar_children_item('hr_profile', [
			'slug'     => 'hr_profile_reports',
			'name'     => _l('hr_reports'),
			'icon'     => 'fa fa-list-alt',
			'href'     => admin_url('hr_profile/reports'),
			'position' => 10,
		]);
	}

	if(has_permission('hrm_setting','','view')){
		 $CI->app_menu->add_sidebar_children_item('hr_profile', [
			'slug'     => 'hr_profile_setting',
			'name'     => _l('hr_settings'),
			'icon'     => 'fa fa-cogs',
			'href'     => admin_url('hr_profile/setting?group=contract_type'),
			'position' => 14,
		]);
	 }
}
/**
 * hr profile load js
 */
function hr_profile_load_js(){    
	$CI = &get_instance();    
	$viewuri = $_SERVER['REQUEST_URI'];
	if(!(strpos($viewuri,'admin/hr_profile/dashboard') === false)){

		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/highcharts/variable-pie.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/highcharts/export-data.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/highcharts/accessibility.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/highcharts/exporting.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
	}

	if(!(strpos($viewuri,'admin/hr_profile/reports') === false)){

		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/highcharts/exporting.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/highcharts/series-label.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
	}


	//settings
	if(!(strpos($viewuri,'admin/hr_profile/setting?group=contract_type') === false)){
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/setting/contract_type.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
	}

	if(!(strpos($viewuri,'admin/hr_profile/setting?group=allowance_type') === false)){

		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/setting/allowance_type.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
	}

	if(!(strpos($viewuri,'admin/hr_profile/setting?group=payroll') === false)){
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/setting/payroll.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
	}

	if(!(strpos($viewuri,'admin/hr_profile/setting?group=type_of_training') === false)){
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/setting/type_of_training.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
	}

	if(!(strpos($viewuri,'admin/hr_profile/setting?group=income_tax_individual') === false)){
		echo '<script src="https://cdn.jsdelivr.net/npm/handsontable@7.2.2/dist/handsontable.full.min.js"></script>';
		echo '<link type="text/css" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@7.2.2/dist/handsontable.full.min.css">';
	}

	if(!(strpos($viewuri,'admin/hr_profile/setting?group=procedure_retire') === false)){
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/setting/procedure_retire.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
	}

	if(!(strpos($viewuri,'admin/hr_profile/setting?group=salary_type') === false)){
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/setting/salary_type.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
	}

	if(!(strpos($viewuri,'admin/hr_profile/setting?group=workplace') === false)){
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/setting/workplace.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
	}


	if(!(strpos($viewuri,'admin/hr_profile/training') === false)){
		if(!(strpos($viewuri,'training_library') === false)){
			echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/training/training_library.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		}
	}

	if(!(strpos($viewuri,'admin/hr_profile/job_position_manage') === false)){
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/job_position/job/job.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
	}
	if(!(strpos($viewuri,'admin/hr_profile/job_positions') === false)){
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/job_position/position/position_manage.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
	}
	if(!(strpos($viewuri,'admin/hr_profile/job_position_view_edit') === false)){
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/job_position/job_position_view_edit.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
	}
	if(!(strpos($viewuri,'admin/hr_profile/importxlsx') === false)){
		echo '<script src="'.base_url('assets/plugins/jquery-validation/additional-methods.min.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
	}

	if(!(strpos($viewuri,'admin/hr_profile/member') === false)){
		if(!(strpos($viewuri,'insurrance') === false)){
			echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/hr_record/includes/insurrance.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		}
		if(!(strpos($viewuri,'income_tax') === false)){
			echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/hr_record/includes/income_tax.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		}
		if(!(strpos($viewuri,'profile') === false)){
			echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/hr_record/includes/profile.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		}
	   
		if(!(strpos($viewuri,'dependent_person') === false)){
			echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/hr_record/includes/dependent_person.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		}
		if(!(strpos($viewuri,'bonus_discipline') === false)){
			echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/hr_record/includes/bonus_discipline.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		}
		if(!(strpos($viewuri,'application_submitted') === false)){
			echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/hr_record/includes/application_submitted.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		}
		if(!(strpos($viewuri,'attach') === false)){
			echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/hr_record/includes/attach.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		}
		if(!(strpos($viewuri,'permission') === false)){
			echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/js/hr_record/includes/permission.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		}
	}

	if(!(strpos($viewuri,'admin/hr_profile/contracts') === false) || !(strpos($viewuri,'admin/hr_profile/staff_infor') === false)|| !(strpos($viewuri,'admin/hr_profile/organizational_chart') === false)){
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/ComboTree/comboTreePlugin.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/ComboTree/icontains.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/OrgChart-master/jquery.orgchart.js').'?v=' . VERSION_HR_PROFILE.'"></script>';

	}

	if(!(strpos($viewuri,'admin/hr_profile/contracts') === false) || !(strpos($viewuri,'admin/hr_profile/staff_infor') === false) || !(strpos($viewuri,'admin/hr_profile/organizational_chart') === false)){
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/ComboTree/comboTreePlugin.js').'?v=' . VERSION_HR_PROFILE.'"></script>';
		echo '<script src="'.module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/ComboTree/comboTreePlugin.js').'?v=' . VERSION_HR_PROFILE.'"></script>';


	}

	if (!(strpos($viewuri, '/admin/hr_profile/contract') === false)) {
	   echo '<script src="' . module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
	   echo '<script src="' . module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
	}

	if (!(strpos($viewuri, '/admin/hr_profile/contract_sign') === false)) {   
         echo '<script src="' . module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/signature_pad.min.js') . '"></script>';
     }



}


	/**
	 * hr profile add head components
	 */
	function hr_profile_add_head_components(){    
		$CI = &get_instance();
		$viewuri = $_SERVER['REQUEST_URI'];

		if(hr_profile_check_hide_menu()){
			if(!(strpos($viewuri,'admin') === false)){
				echo '<link href="' . module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/css/hide_sidebar_menu.css') . '?v=' . VERSION_HR_PROFILE. '"  rel="stylesheet" type="text/css" />';
			}
		}
		if(!(strpos($viewuri,'admin/hr_profile') === false)){
			echo '<link href="' . module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/css/style.css') . '?v=' . VERSION_HR_PROFILE. '"  rel="stylesheet" type="text/css" />';
		}

		if(!(strpos($viewuri,'admin/hr_profile/organizational_chart') === false) || !(strpos($viewuri,'admin/hr_profile/staff_infor') === false)){
			echo '<link href="' . module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/ComboTree/style.css') . '?v=' . VERSION_HR_PROFILE. '"  rel="stylesheet" type="text/css" />';
			echo '<link href="' . module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/css/style.css') . '?v=' . VERSION_HR_PROFILE. '"  rel="stylesheet" type="text/css" />';
			echo '<link href="' . module_dir_url(HR_PROFILE_MODULE_NAME, '/assets/plugins/OrgChart-master/jquery.orgchart.css') . '?v=' . VERSION_HR_PROFILE. '"  rel="stylesheet" type="text/css" />';

		}
		

		if(!(strpos($viewuri,'admin/hr_profile/organizational_chart') === false)){
		   echo '<link href="' . module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/css/organizational/organizational.css') . '?v=' . VERSION_HR_PROFILE. '"  rel="stylesheet" type="text/css" />';
		   echo '<link href="https://fonts.googleapis.com/css?family=Gochi+Hand" rel="stylesheet">';
		}

	   if(!(strpos($viewuri,'admin/hr_profile/training') === false)){
			if(!(strpos($viewuri,'insurrance') === false)){
				echo '<link href="' . module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/css/setting/insurrance.css') . '?v=' . VERSION_HR_PROFILE. '"  rel="stylesheet" type="text/css" />'; 
			}
		}

		if(!(strpos($viewuri,'admin/hr_profile/job_position_view_edit') === false) || !(strpos($viewuri,'admin/hr_profile/job_positions') === false)|| !(strpos($viewuri,'admin/hr_profile/reception_staff') === false)|| !(strpos($viewuri,'admin/hr_profile/training') === false)){
			echo '<link href="' . module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/css/job/job_position_view_edit.css') . '?v=' . VERSION_HR_PROFILE. '"  rel="stylesheet" type="text/css" />';
		}

		if(!(strpos($viewuri,'admin/hr_profile/member') === false) || !(strpos($viewuri,'admin/hr_profile/new_member') === false)|| !(strpos($viewuri,'admin/hr_profile/staff_infor') === false)){
			if(!(strpos($viewuri,'profile') === false)){
				echo '<link href="' . module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/css/hr_record/includes/profile.css') . '?v=' . VERSION_HR_PROFILE. '"  rel="stylesheet" type="text/css" />';
			}
		}

		if(!(strpos($viewuri,'admin/hr_profile/import_job_p') === false) || !(strpos($viewuri,'admin/hr_profile/import_xlsx_dependent_person') === false) || !(strpos($viewuri,'admin/hr_profile/importxlsx') === false)){
			echo '<link href="' . module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/css/box_loading/box_loading.css') . '?v=' . VERSION_HR_PROFILE. '"  rel="stylesheet" type="text/css" />';
		}

		if(!(strpos($viewuri,'admin/hr_profile/contracts') === false) || !(strpos($viewuri,'admin/hr_profile/staff_infor') === false)){
			echo '<link href="' . module_dir_url(HR_PROFILE_MODULE_NAME,'assets/plugins/ComboTree/style.css') .'?v=' . VERSION_HR_PROFILE. '"  rel="stylesheet" type="text/css" />';
			echo '<link href="' . module_dir_url(HR_PROFILE_MODULE_NAME,'assets/css/ribbons.css') .'?v=' . VERSION_HR_PROFILE. '"  rel="stylesheet" type="text/css" />';
		}

		if( !(strpos($viewuri,'admin/hr_profile/staff_infor') === false)){
			echo '<link href="' . module_dir_url(HR_PROFILE_MODULE_NAME,'assets/css/hr_record/hr_record.css') .'?v=' . VERSION_HR_PROFILE. '"  rel="stylesheet" type="text/css" />';
		}

		if (!(strpos($viewuri, '/admin/hr_profile/contract') === false)) {  
			echo '<link href="' . module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
			echo '<link href="' . module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/handsontable/chosen.css') . '"  rel="stylesheet" type="text/css" />';
			echo '<script src="' . module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
		}

		if (!(strpos($viewuri, '/admin/hr_profile/dashboard') === false)) { 
			echo '<link href="' . module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/css/dashboard/dashboard.css') . '?v=' . VERSION_HR_PROFILE. '"  rel="stylesheet" type="text/css" />';
		}


		if (!(strpos($viewuri, '/admin/hr_profile/setting') === false)) {
			echo '<link href="' . module_dir_url(HR_PROFILE_MODULE_NAME, 'assets/css/setting/contract_template.css') . '?v=' . VERSION_HR_PROFILE. '"  rel="stylesheet" type="text/css" />';

		}

	}



	/**
	 * hr profile permissions
	 */
	function hr_profile_permissions()
	{

		$capabilities = [];
		$capabilities_2 = [];
		$capabilities_3 = [];
		$dashboard = [];

		$capabilities['capabilities'] = [
				'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
				'create' => _l('permission_create'),
				'edit'   => _l('permission_edit'),
				'delete' => _l('permission_delete'),
		];

		$capabilities_2['capabilities'] = [
				// 'view_own'   => _l('permission_view'),
				'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
				'create' => _l('permission_create'),
				'edit'   => _l('permission_edit'),
				'delete' => _l('permission_delete'),
		];

		$capabilities_3['capabilities'] = [
				'view_own'   => _l('permission_view_own'),
				'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
				'create' => _l('permission_create'),
				'edit'   => _l('permission_edit'),
				'delete' => _l('permission_delete'),
		];

		$dashboard['capabilities'] = [
				'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
				
		];

		//Dashboard
		register_staff_capabilities('hrm_dashboard', $dashboard, _l('HR_dashboard'));
		//Orgranization
		register_staff_capabilities('staffmanage_orgchart', $capabilities_3, _l('HR_organizational_chart'));
		//Onboarding Process
		register_staff_capabilities('hrm_reception_staff', $capabilities_3, _l('HR_reception_staff'));
		//Hr Profile
		register_staff_capabilities('hrm_hr_records', $capabilities_3, _l('hr_hr_records'));
		//Job Description
		register_staff_capabilities('staffmanage_job_position', $capabilities_3, _l('HR_job_escription'));
		//Training
		register_staff_capabilities('staffmanage_training', $capabilities_3, _l('HR_training'));
		//Q&A
		register_staff_capabilities('hr_manage_q_a', $capabilities_2, _l('HR_q&a'));
		//Contracts
		register_staff_capabilities('hrm_contract', $capabilities_3, _l('HR_contract'));
		//Dependent Persons
		register_staff_capabilities('hrm_dependent_person', $capabilities_3, _l('HR_dependent_persons'));
		//Resignation procedures
		register_staff_capabilities('hrm_procedures_for_quitting_work', $capabilities_3, _l('HR_resignation_procedures'));
		//Reports
		register_staff_capabilities('hrm_report', $dashboard, _l('HR_report'));
		//Settings
		register_staff_capabilities('hrm_setting', $capabilities, _l('HR_setting'));

	}


	/**
	 * render my profile icon
	 * @return [type] 
	 */
	function render_my_profile_icon(){
		$CI = &get_instance();
		if(!hr_profile_check_hide_menu()){
			echo '<li class="dropdown">
			<a href="' . admin_url('hr_profile/member/' . get_staff_user_id()) . '" class="check_in_out_timesheet" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="'._l('hr_my_profile').'"><i class="fa fa-address-card"></i>
			</a>' ;
			echo '</li>';
		}
	}

	/**
	 * hr contract register other merge fields
	 * @param  [type] $for 
	 * @return [type]      
	 */
	function hr_contract_register_other_merge_fields($for)
	{
		$for[] = 'hr_contract';

		return $for;
	}


