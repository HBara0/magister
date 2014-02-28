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

if($core->usergroup['sourcing_canViewKPI'] == 30) {
	error($lang->sectionnopermission);
}
$potential_supplier = new Sourcing();
$period['beginingcurrentmonth'] = strtotime(date('01-m-Y'));  /* first day of the current month */
$period['currentmonth'] = TIME_NOW;
$period['begininglastmonth'] = strtotime('first day of last month midnight'); /* first day of the last month at 12 */
$period['endlastmonth'] = strtotime(date('01-m-Y').'-1 second'); /* end day of the  last month */
$period['thisdaythismonth'] = strtotime('this day this month');
$period['thisdaylastmonth'] = strtotime('this day last month');

$kpidata = array('name' => 'Prequalification Performance', 'kpitarget' => 90);
$kpidatacrrent = $potential_supplier->get_periods(array('kpifor' => 'isProductApproved', 'fromDate' => $period['beginingcurrentmonth'], 'toDate' => $period['currentmonth']));
$kpidatacrrent_dateoutput= ' Up To '.date('M,d',$period['beginingcurrentmonth']);

$kpidataprev = $potential_supplier->get_periods(array('fromDate' => $period['beginingcurrentmonth'], 'toDate' => $period['currentmonth']));
$kpipercentage['current'] = round(($kpidatacrrent / $kpidataprev) * 100, 0).'%';

$kpidataprevmonth = $potential_supplier->get_periods(array('kpifor' => 'isProductApproved', 'fromDate' => $period['begininglastmonth'], 'toDate' => $period['endlastmonth']));

$kpidataprev = $potential_supplier->get_periods(array('fromDate' => $period['begininglastmonth'], 'toDate' => $period['endlastmonth']));
$kpipercentage['last'] = round(($kpidataprevmonth / $kpidataprev) * 100, 0).'%';

if($kpipercentage['last'] < $kpidata['kpitarget']) {
	$kpibelowtarget = " kpibelow";
}
if($kpipercentage['last'] > $kpidata['kpitarget']) {
	$kpiabovetarget = " kpiabove";
}
else {
	$kpiabovetarget = " kpiabove";
}
echo $kpidataprevmonth.' '.$kpidataprev;

// equiivalent to $period['currentmonth'] but  im previous month
$kpitodayequivapprove = $potential_supplier->get_periods(array('kpifor' => 'isProductApproved', 'fromDate' => $period['begininglastmonth'], 'toDate' => $period['thisdaylastmonth']));
$kpitodayequivapprove_dateoutput= ' Up To '.date('M,d',$period['begininglastmonth']);

$kpi['trend'] = $kpidatacrrent - $kpitodayequivapprove;
if($kpi['trend'] < $kpidata['kpitarget']) {
	$kpibelowtarget = " kpibelow";
	$trend_output = '<span class="arrow-down" style="width:0px;height:0px; border-left-width:27px; border-right-width:27px ; border-top-width:27px ;border-top-color:#ff0000 " title = " current: '.$kpidatacrrent.' '.$kpidatacrrent_dateoutput.' | prev: '.$kpitodayequivapprove.' '.$kpitodayequivapprove_dateoutput.'  " ;</span>';
}
elseif($kpi['trend'] == 0) {
	$trend_output = '<span title="current: '.$kpidatacrrent.' | prev: '.$kpitodayequivapprove.'" >=</span>';
}
else {
	$kpiabovetarget = " kpiabove";
	$trend_output = '<span title="current: '.$kpidatacrrent.' | prev: '.$kpitodayequivapprove.'" class="arrow-down">&#8593;</span>';
}

eval("\$sourcing_workspace = \"".$template->get('sourcing_workspace')."\";");
output($sourcing_workspace);
?>
