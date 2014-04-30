<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: workspace.php
 * Created:        @tony.assaad    Feb 27, 2014 | 2:09:34 PM
 * Last Update:    @tony.assaad    Feb 27, 2014 | 2:09:34 PM
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['sourcing_canViewKPIs'] == 0) {
    error($lang->sectionnopermission);
}
$potential_supplier = new Sourcing();
$periods['begcurmonth'] = strtotime(date('01-m-Y'));  /* first day of the current month */
$periods['currentmonth'] = TIME_NOW;
$periods['beglastmonth'] = strtotime('first day of last month midnight'); /* first day of the last month at 12 */
$periods['endlastmonth'] = strtotime(date('01-m-Y').'-1 second'); /* end day of the  last month */
$periods['thisdaylastmonth'] = strtotime('this day last month');

$kpi_config = array('name' => 'Prequalification Performance', 'target' => 90);

$kpis_data[1]['currentkpi'] = $potential_supplier->count_contacthist(array('kpifor' => 'isProductApproved', 'fromDate' => $periods['begcurmonth'], 'toDate' => $periods['currentmonth']));
$kpis_data[1]['currenttotal'] = $potential_supplier->count_contacthist(array('fromDate' => $periods['begcurmonth'], 'toDate' => $periods['currentmonth']));
$kpis[1]['current'] = 0;
if($kpis_data[1]['currenttotal'] > 0) {
    $kpis[1]['current'] = round(($kpis_data[1]['currentkpi'] / $kpis_data[1]['currenttotal']) * 100, 0);
}

$kpi_class['current'] = 'green_text';
if($kpis[1]['current'] < $kpi_config['target']) {
    $kpi_class['current'] = 'red_text';
}

$kpis_data[1]['prevkpi'] = $potential_supplier->count_contacthist(array('kpifor' => 'isProductApproved', 'fromDate' => $periods['beglastmonth'], 'toDate' => $periods['endlastmonth']));
$kpis_data[1]['prevtotal'] = $potential_supplier->count_contacthist(array('fromDate' => $periods['beglastmonth'], 'toDate' => $periods['endlastmonth']));
$kpis[1]['lastmonth'] = 0;
if($kpis_data[1]['prevtotal'] > 0) {
    $kpis[1]['lastmonth'] = round(($kpis_data[1]['prevkpi'] / $kpis_data[1]['prevtotal']) * 100, 0);
}

$kpi_class['lastmonth'] = 'green_text';
if($kpis[1]['lastmonth'] < $kpi_config['target']) {
    $kpi_class['lastmonth'] = 'red_text';
}


$kpis_data[1]['currentprevequiv'] = $potential_supplier->count_contacthist(array('kpifor' => 'isProductApproved', 'fromDate' => $periods['beglastmonth'], 'toDate' => $periods['thisdaylastmonth']));

$trend_title = 'Up to '.date('M, d', $periods['currentmonth']).': '.$kpis_data[1]['currentkpi'].'<br />';
$trend_title .= 'Up to '.date('M, d', $periods['thisdaylastmonth']).': '.$kpis_data[1]['currentprevequiv'];
if($kpis_data[1]['currentkpi'] < $kpis_data[1]['currentprevequiv']) {
    $trend_output = '<span class="arrow-down" style="border-left-width:15px; border-right-width:15px; border-top-width:15px; border-top-color:#CA0703;" title="'.$trend_title.'"></span>';
}
elseif($kpis_data[1]['currentkpi'] == $kpis_data[1]['currentprevequiv']) {
    $trend_output = '<span title="'.$trend_title.'" style="color:#CCC;">&bull;</span>';
}
else {
    $trend_output = '<span title="'.$trend_title.'" class="arrow-up" style="border-left-width:15px; border-right-width:15px; border-bottom-width:15px; border-bottom-color:#91b64f;"></span>';
}

eval("\$sourcing_workspace = \"".$template->get('sourcing_workspace')."\";");
output_page($sourcing_workspace);
?>
