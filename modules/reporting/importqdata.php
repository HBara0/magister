<?php
require_once './inc/init.php';

$affid = 1;
$core->input['turnoverink'] = 0;
$options['foreignSystem'] = 1;
$options['method'] = 'productbase';
$options['quarter'] = 2;

$options['fromDate'] = '01-04-2012';
$options['toDate'] = '30-06-2012';

if($options['method'] == 'normal') {
$query = $db->query("SELECT imso.*, ims.localId AS localspid, imp.localId AS localpid, ims.foreignName
					FROM integration_mediation_stockpurchases imso JOIN integration_mediation_products imp ON (imso.pid=imp.foreignId) JOIN integration_mediation_entities ims ON (imso.spid=ims.foreignId)
					WHERE ims.affid=imso.affid AND imso.affid={$affid} AND (imp.localId!=0 OR ims.localId=0) AND ims.entityType='s' AND (imso.date BETWEEN ".strtotime($options['fromDate'])." AND ".strtotime($options['toDate']).")");
}
elseif($options['method'] == 'productbase')
{
	$query = $db->query("SELECT imso.*, imp.localId as localpid, p.spid AS localspid
						FROM integration_mediation_stockpurchases imso JOIN integration_mediation_products imp ON (imso.pid=imp.foreignId) JOIN products p ON (p.pid=imp.localId) 
						WHERE imso.foreignSystem={$options[foreignSystem]} AND imso.affid={$affid} AND imp.localId!=0 AND (imso.date BETWEEN ".strtotime($options['fromDate'])." AND ".strtotime($options['toDate']).")");
}

if($db->num_rows($query) > 0) {
	$options['turnoverdivision'] = 1000;
	if($core->input['turnoverink'] == 1) {
		$options['turnoverdivision'] == 1;	
	}
	while($purchase = $db->fetch_assoc($query)) {
		if(is_empty($purchase['localspid'], $purchase['localpid'])) {
			continue;
		}
		$temporary_saletype = '';
		/* GET Quarter Information - START */
		$quarter_info = quarter_info($purchase['date']);

		/* GET Quarter Information - END */
		
		if($quarter_info['quarter'] != $options['quarter']) {
		 	continue;
		}	
		if(isset($reports_cache[$affid][$purchase['localspid']][$quarter_info['year']][$quarter_info['quarter']])) {
			$report = $reports_cache[$affid][$purchase['localspid']][$quarter_info['year']][$quarter_info['quarter']];
		}
		else
		{
			$report = $db->fetch_assoc($db->query("SELECT * FROM reports WHERE affid='{$affid}' AND quarter='{$quarter_info[quarter]}' AND year='{$quarter_info[year]}' AND spid='{$purchase[localspid]}' AND type='q'"));
			$reports_cache[$affid][$purchase['localspid']][$quarter_info['year']][$quarter_info['quarter']] = $report;
		}

		if(is_array($report)) {
			if(!isset($newpurchase[$report['rid']][$purchase['localpid']])) {
				$newpurchase[$report['rid']][$purchase['localpid']] = array(
					'pid' => $purchase['localpid'],
					'quantity' => $purchase['quantity'],
					'turnOver' => ($purchase['amount']/$options['turnoverdivision']),
					'rid'	=> $report['rid'],
					'uid' 	=> 0
				);
			}
			else
			{
				$newpurchase[$report['rid']][$purchase['localpid']]['quantity'] += $purchase['quantity'];
				$newpurchase[$report['rid']][$purchase['localpid']]['turnOver'] += ($purchase['amount']/$options['turnoverdivision']);
			}
			
			if($purchase['currency'] != 'USD') {
				$newpurchase[$report['rid']][$purchase['localpid']]['turnOver'] = (($purchase['amount']/$purchase['usdFxrate'])/$options['turnoverdivision']);
				$newpurchase[$report['rid']][$purchase['localpid']]['turnOverOc'] = ($purchase['amount']/$options['turnoverdivision']);
				$newpurchase[$report['rid']][$purchase['localpid']]['originalCurrency'] = $purchase['currency'];
			}
			
			if(in_array($purchase['saleType'], array('SKI/ReI', 'SKI', 'ReI'))) {
				$temporary_saletype = 'distribution';
			}
			elseif(in_array($purchase['saleType'], array('DIv'))) {
				$temporary_saletype = 'indent';
			}
			
		/*	if($newpurchase[$report['rid']]['saleType'] != $temporary_saletype) {
				$newpurchase[$report['rid']][$purchase['localpid']]['saleType'] = 'both';
			}
			else
			{*/
				$newpurchase[$report['rid']][$purchase['localpid']]['saleType'] = $temporary_saletype;
			//}
		}
		else
		{
			if(!isset($purchase['foreignName']) || empty($purchase['foreignName'])) {
				$purchase['foreignName'] = $db->fetch_field($db->query("SELECT companyName FROM entities WHERE eid={$purchase[localspid]}"),'companyName');
			}
			$errors['reportnotfound'][] = 'Q'.$quarter_info['quarter'].'/'.$quarter_info['year'].' '.$affid.'-'.$purchase['foreignName'];
		}
	}
}

if(is_array($newpurchase)) {
	foreach($newpurchase as $rid => $products) {
		foreach($products as $pid => $activity) {
			//echo $activity['rid'].' '.print_r($activity).'<hr />';
			$db->insert_query('productsactivity', $activity);
			//echo "Done<br />";
		}
	}
}
if(is_array($errors)) {
	foreach($errors as $key => $val) {
		foreach($val as $error) {
			echo $error.'<br />';
		}
	}
}
function quarter_info($time) {
	global $core;

	$current_year = date('Y', $time);
	
	for($i=1;$i<=4;$i++) {
		$start = explode('/', $core->settings['q'.$i.'start']);
		$end = explode('/', $core->settings['q'.$i.'end']);
		
		$quarter_start = mktime(0,0,0, $start[1], $start[0], $current_year);
		$quarter_end = mktime(24,59,0, $end[1], $end[0], $current_year);
		
		if($time >= $quarter_start && $time <= $quarter_end) {
			$current_quarter = $i;
			if($real === false) {
				$current_quarter = $i-1;
				if($current_quarter == 0) {
					$current_quarter = 4;
					$current_year -= 1;
				}
			}
			return array('quarter' => $current_quarter, 'year' => $current_year);
		}
	}
	return false;
}
?>