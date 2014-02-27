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
$potential_supplier = new Sourcing();
$period['beginingcurrentmonth'] = strtotime(date('01-m-Y'));  /* first day of the current month */
$period['currentmonth'] = TIME_NOW;
$period['begininglastmonth'] = strtotime('first day of last month midnight'); /* first day of the last month at 12 */
$period['endlastmonth'] = strtotime(date('01-m-Y').'-1 second'); /* end day of the  last month */
$period['thisdaythismonth'] = strtotime('this day this month');
$period['thisdaylastmonth'] = strtotime('this day last month');

$kpidatacrrent = $potential_supplier->get_periods(array('kpifor' => 'isProductApproved', 'fromDate' => $period['beginingcurrentmonth'], 'toDate' => $period['currentmonth']));

$kpidataprev = $potential_supplier->get_periods(array('fromDate' => $period['beginingcurrentmonth'], 'toDate' => $period['currentmonth']));
$kpipercentage['current'] = round(($kpidatacrrent / $kpidataprev) * 100, 0).'%';


$kpidataprev = $potential_supplier->get_periods(array('fromDate' => $period['begininglastmonth'], 'toDate' => $period['endlastmonth']));

$kpidataprevmonth = $potential_supplier->get_periods(array('kpifor' => 'isProductApproved', 'fromDate' => $period['begininglastmonth'], 'toDate' => $period['endlastmonth']));

$kpipercentage['last'] = round(($kpidataprevmonth / $kpidataprev) * 100, 0).'%';
echo $kpidataprevmonth.'  '.$kpidataprev;

// equiivalent to $period['currentmonth'] but  im previous month
$kpitodayequivapprove = $potential_supplier->get_periods(array('kpifor' => 'isProductApproved', 'fromDate' => $period['begininglastmonth'], 'toDate' => $period['thisdaylastmonth']));
$kpi['trend'] = $kpidatacrrent - $kpitodayequivapprove;
if($kpi['trend'] < 0) {
	$trend_output = '<div title="current: '.$kpidatacrrent.' | prev: '.$kpitodayequivapprove.'">&#8595;</span>';
}
elseif($kpi['trend'] == 0) {
	$trend_output = '<span title="current: '.$kpidatacrrent.' | prev: '.$kpitodayequivapprove.'">=</span>';
}
else {

	$trend_output = '<span title="current: '.$kpidatacrrent.' | prev: '.$kpitodayequivapprove.'">&#8593;</span>';
}


eval("\$sourcing_workspace = \"".$template->get('sourcing_workspace')."\";");
output($sourcing_workspace);
?>
