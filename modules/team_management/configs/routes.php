<?php

defined('BASEPATH') or exit('No direct script access allowed');

$route['/team_management/get_shift_timings/(:num)/(:num)'] =  'Team_management::get_shift_timings/$1/$2';

$route['/team_management/mail_weekly/(:num)'] =  'Team_management::mail_weeky/$1';

$route['/team_management/export_shift_details_to_pdf/(:num)/(:num)'] =  'Team_management::export_shift_details_to_pdf/$1/$2';

$route['/team_management/export_all_shift_details_to_pdf/(:num)'] =  'Team_management::export_all_shift_details_to_pdf/$1';

$route['/team_management/activity_log/(:num)/(:num)'] =  'Team_management::activity_log/$1/$2';

$route['/team_management/staff_stats/(:num)/(:num)'] =  'Team_management::staff_stats/$1/$2';

$route['/team_management/daily_reports/(:num)/(:num)'] =  'Team_management::daily_reports/$1/$2';