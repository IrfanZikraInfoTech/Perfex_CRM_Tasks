<?php
defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
    'date',
    'assigned_to',
    'customer',
    'contact',
    'description',
    'total_amount',
    'rel_type',
    'isnotified',
    'created_by_staff',
    'recurring_type',
];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'reminders';
$filter = [];
$where = [];
$staff_id =  get_staff_user_id();
if(has_permission('reminder','','view_own') && !is_admin()){
    array_push($where, 'AND created_by_staff='.$staff_id);
}
 
$isnotified = $this->ci->input->post('isnotified');
if($isnotified){
    array_push($filter, "AND (" . db_prefix() . "reminders.isnotified=". $this->ci->input->post('isnotified')." OR ". db_prefix() . "reminders.isnotified = 0)");
}else{
    array_push($filter, "AND " . db_prefix() . "reminders.isnotified= 0");
}

if ($this->ci->input->post('reminder_filter_number')) {
    array_push($filter, "AND " . db_prefix() . "reminders.id LIKE '%" . removeQ($this->ci->input->post('reminder_filter_number')) . "%'");
}
//Check date filter
if ($this->ci->input->post('reminder_filter_date_f')) {
    array_push($filter, "AND date >= '" .  to_sql_date($this->ci->input->post('reminder_filter_date_f')) . "'");
}
if ($this->ci->input->post('reminder_filter_date_t')) {
    array_push($filter, "AND date <= '" .  to_sql_date($this->ci->input->post('reminder_filter_date_t')) . "'+ INTERVAL 1 DAY");
}
//Check status filter
if ($this->ci->input->post('reminder_filter_related')) {
    array_push($filter, "AND " . db_prefix() . "reminders.rel_type = '" . $this->ci->input->post('reminder_filter_related')."'");
}
//Check company filter
if ($this->ci->input->post('reminder_filter_company')) {
    array_push($filter, "AND customer = " . get_client_id_by_company($this->ci->input->post('reminder_filter_company')));
}
//Check name filter
if ($this->ci->input->post('reminder_filter_contact_val')) {
    array_push($filter, "AND contact = " . get_contact_id_by_full_name($this->ci->input->post('reminder_filter_contact_val')));
}
//Check status filter
if ($this->ci->input->post('reminder_filter_assigned')) {
    array_push($filter, "AND " . db_prefix() . "reminders.assigned_to = " . $this->ci->input->post('reminder_filter_assigned'));
}
//Check email filter
if ($this->ci->input->post('reminder_filter_description')) {
    array_push($filter, "AND description LIKE '%" . $this->ci->input->post('reminder_filter_description') . "%'");
}
//Check follow up date filter
if ($this->ci->input->post('filter_date_follow_f') && $this->ci->input->post('filter_date_follow_t')) {
    array_push($filter, "AND follow_up_date BETWEEN '" . strtotimemod($this->ci->input->post('filter_date_follow_f')) . "' AND '" . strtotimemod($this->ci->input->post('filter_date_follow_t')) . "'");
}
$agents    = $this->ci->reminder_model->get_sale_agents();
$agentsIds = [];
foreach ($agents as $agent) {
    if ($this->ci->input->post('sale_agent_' . $agent['sale_agent'])) {
        array_push($agentsIds, $agent['sale_agent']);
    }
}
if (count($agentsIds) > 0) {
    array_push($filter, 'AND assigned_to IN (' . implode(', ', $agentsIds) . ')');
}
$customer    = $this->ci->reminder_model->getCustomersData();
$customerIds = [];
foreach ($customer as $agent) {
    if ($this->ci->input->post('customer_' . $agent['userid'])) {
        array_push($customerIds, $agent['userid']);
    }
}
$rel_types = ['quotes','estimate','invoice','credit_note','tickets'] ;
$relationTypesIds = [];
foreach ($rel_types as $type) {
    if ($this->ci->input->post('rel_type_' . $type)) {
        array_push($relationTypesIds, $type);
    }
}
$created_by    = $this->ci->reminder_model->get_created_by_ids();
$created_by_ids = [];
foreach ($created_by as $id) {
    if ($this->ci->input->post('created_by_' . $id['by_staff'])) {
        array_push($created_by_ids, $id['by_staff']);
    }
}
if (count($created_by_ids) > 0) {
    array_push($filter, 'AND created_by_staff IN (' . implode(', ', $created_by_ids) . ')');
}
if (count($customerIds) > 0) {
    array_push($filter, 'AND customer IN (' . implode(', ', $customerIds) . ')');
}
if (count($relationTypesIds) > 0) {
    array_push($filter, 'AND rel_type IN ("' . implode('", "', $relationTypesIds) . '")');
}
$years      = $this->ci->reminder_model->get_reminder_years();
$yearsArray = [];
foreach ($years as $year) {
    if ($this->ci->input->post('year_' . $year['year'])) {
        array_push($yearsArray, $year['year']);
    }
}
if (count($yearsArray) > 0) {
    array_push($filter, 'AND YEAR(date) IN (' . implode(', ', $yearsArray) . ')');
}
if ($this->ci->input->post('this_month')) {
    $this_month=date('m');
    array_push($filter, 'AND MONTH(date) ='.$this_month.'');
}
if ($this->ci->input->post('this_week')) {
    $day=date('w');
    $week_start=date('Y-m-d',strtotime('-'.$day.'days'));
    $week_end=date('Y-m-d',strtotime('+'.(6-$day).'days'));
     array_push($filter, "AND DATE(date) BETWEEN '" . $week_start . "' AND '" . $week_end . "'");
}
if ($this->ci->input->post('recurring_reminders')) {
    array_push($filter, 'AND recurring_type !=""');
}
if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}
$join          = [];
$custom_fields = get_table_custom_fields('reminder');
$aColumns = hooks()->apply_filters('reminder_table_sql_columns', $aColumns);
// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'reminders.id as id']);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];
    $numberOutput = '<a href="javascript:void(0);" onclick="init_reminder(' . $aRow['id'] . '); return false;">' ._dt($aRow['date']) . '</a>';
    $numberOutput .= '<div class="row-options">';
    $numberOutput .= '<a href="javascript:void(0);" onclick="getViewModal(' . $aRow['id'] . ')">' . _l('view') . '</a>';
    if (has_permission('reminder', '', 'delete')) {
        $numberOutput .= ' | <a href="' . admin_url('reminder/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }
    $numberOutput .= '</div>';
    $row[] = $numberOutput;
    $row[] = !empty(get_staff($aRow['assigned_to'])) ? get_staff($aRow['assigned_to'])->firstname.' '.get_staff($aRow['assigned_to'])->lastname : '';
    $row[] = !empty(get_client($aRow['customer'])) ? get_client($aRow['customer'])->company : '';
    $row[] =  get_contact_full_name($aRow['contact']);
    $row[] = $aRow['description'];
    $row[] = $aRow['total_amount'];
    if($this->ci->input->post('reminder_filter_related')=='custom_reminder'){
        $row[] = ucfirst(_l($aRow['other_relation_type']));
    }else{
        $row[] = ucfirst(_l($aRow['rel_type']));
    }
    $row[] = $aRow['isnotified'] ? '<span class="label label-success">'._l('rm_notified_status').'</span>' : '<span class="label label-warning">'._l('rm_not_notified_status').'</span>';
    $staff = '';
    if(!empty($aRow['created_by_staff'])){
        $oStaff = $this->ci->staff_model->get($aRow['created_by_staff']);
        $staff = '<a data-toggle="tooltip" data-title="' . $oStaff->full_name . '" href="' . admin_url('profile/' . $aRow['created_by_staff']) . '">' . staff_profile_image($aRow['created_by_staff'], [
            'staff-profile-image-small',
        ]) . '</a>';
        $staff .= '<span class="hide">' . $oStaff->full_name . '</span>';
    }
    $row[] = $staff;
    
    $row[] = ($aRow['recurring_type']=='') ? "NO":"YES";
    
    $row['DT_RowClass'] = 'has-row-options'.' complete-'.get_complete_reminder($aRow['id']);
    $row['DT_RowLink'] = $aRow['id'];
    $row = hooks()->apply_filters('reminder_table_row_data', $row, $aRow);
    $output['aaData'][] = $row;
}
