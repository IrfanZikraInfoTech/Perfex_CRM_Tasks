<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
    'id',
    'mautic_campaign_name',
    'mautic_campaign_desc',
    'mautic_campaign_published',
    'mautic_campaign_category',
];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'mautic_campaign';
$filter = [];
$where = [];
$statusIds = [];
$join = [];
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

    if ($aRow['mautic_campaign_published'] == 0) {

        $published = '';
    } else {

        $published = 'Published';
    }
    $row = [];
    $row[] = $aRow['mautic_campaign_name'];
    $row[] = $aRow['mautic_campaign_desc'];
    $row[] = $published;
    $row[] = $aRow['mautic_campaign_category'];

    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
