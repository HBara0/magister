<?php
/*
 * Copyright © 2012 Orkila International Offshore, All Rights Reserved
 * 
 * Import Quarter Data
 * $id: balancesvalidations.php
 * Created:        @zaher.reda    March 14, 2013 | 6:38:41 PM
 * Last Update:    @zaher.reda    March 14, 2013 | 6:38:41 PM
 */
if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canCreateReports'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!$core->input['action']) {
	$quarter = currentquarter_info();
	$selected[$quarter['quarter']] = ' selected="selected"';

	$affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 0);
	$affid_field = parse_selectlist('affid', 1, $affiliates, '');
	
	eval("\$importqdata = \"".$template->get('reporting_importqdata')."\";");
	output_page($importqdata);
}
else {
	if($core->input['action'] == 'do_import') {
		
		if(is_empty($core->input['year'])) {
			output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>"); 
			exit;
		}
		$affid = intval($core->input['affid']);
		$affid = 19;
		$options = $core->input;
		$core->input['turnoverink'] = 0;
		$options['method'] = 'productbase';

		$options['fromDate'] = $core->settings['q'.$options['quarter'].'start'].'-'.$options['year'];
		$options['toDate'] = $core->settings['q'.$options['quarter'].'end'].'-'.$options['year'];

		$options['turnoverdivision'] = 1000;
		if($core->input['turnoverink'] == 1) {
			$options['turnoverdivision'] = 1;
		}

		$po_query = $db->query("SELECT imso.*
						FROM ".Tprefix."integration_mediation_purchaseorders imso 
						JOIN ".Tprefix."integration_mediation_entities ims ON (imso.spid=ims.foreignId)
						WHERE imso.foreignSystem={$options[foreignSystem]} AND imso.affid={$affid} AND (imso.date BETWEEN ".strtotime($options['fromDate'])." AND ".strtotime($options['toDate']).")");
		if($db->num_rows($po_query) > 0) {
			while($purchaseorder = $db->fetch_assoc($po_query)) {
				$pol_query = $db->query("SELECT imsol.*, imp.localId AS localpid, imsol.pid AS foreignpid, p.spid AS localspid
										FROM ".Tprefix."integration_mediation_purchaseorderlines imsol 
										JOIN integration_mediation_products imp ON (imsol.pid=imp.foreignId) 
										JOIN products p ON (p.pid=imp.localId)
										WHERE foreignOrderId='{$purchaseorder['impoid']}' AND imp.localId!=0");
										
				if($db->num_rows($pol_query) > 0) {
					while($purchaseorderline = $db->fetch_assoc($pol_query)) {
						if(is_empty($purchaseorderline['localspid'], $purchaseorderline['localpid'])) {
							continue;
						}
						$temporary_purchasetype = '';
						/* GET Quarter Information - START */
						$quarter_info = quarter_info($purchaseorder['date']);
						/* GET Quarter Information - END */

						if($quarter_info['quarter'] != $options['quarter']) {
							continue;
						}
						if(isset($reports_cache[$affid][$purchaseorderline['localspid']][$quarter_info['year']][$quarter_info['quarter']])) {
							$report = $reports_cache[$affid][$purchaseorderline['localspid']][$quarter_info['year']][$quarter_info['quarter']];
						}
						else {
							$report = $db->fetch_assoc($db->query("SELECT * FROM reports WHERE affid='{$affid}' AND quarter='{$quarter_info[quarter]}' AND year='{$quarter_info[year]}' AND spid='{$purchase[localspid]}' AND type='q'"));
							$reports_cache[$affid][$purchaseorderline['localspid']][$quarter_info['year']][$quarter_info['quarter']] = $report;
						}

						if(is_array($report)) {
							if(!isset($newpurchase[$report['rid']][$purchaseorderline['localpid']])) {
								$newpurchase[$report['rid']][$purchaseorderline['localpid']] = array(
										'pid' => $purchaseorderline['localpid'],
										'quantity' => $purchaseorderline['quantity'],
										'turnOver' => ($purchaseorderline['amount'] / $options['turnoverdivision']),
										'rid' => $report['rid'],
										'uid' => 0
								);
							}
							else {
								$newpurchase[$report['rid']][$purchaseorderline['localpid']]['quantity'] += $purchaseorderline['quantity'];
								$newpurchase[$report['rid']][$purchaseorderline['localpid']]['turnOver'] += ($purchaseorderline['amount'] / $options['turnoverdivision']);
							}

							if($purchaseorder['currency'] != 'USD') {
								$newpurchase[$report['rid']][$purchaseorderline['localpid']]['turnOver'] = (($purchaseorderline['amount'] / $purchaseorder['usdFxrate']) / $options['turnoverdivision']);
								$newpurchase[$report['rid']][$purchaseorderline['localpid']]['turnOverOc'] = ($purchaseorderline['amount'] / $options['turnoverdivision']);
								$newpurchase[$report['rid']][$purchaseorderline['localpid']]['originalCurrency'] = $purchaseorder['currency'];
							}

							if(in_array($purchaseorder['purchaseType'], array('SKI', 'ReI'))) {
								$temporary_purchasetype = 'distribution';
							}
							elseif(in_array($purchaseorder['purchaseType'], array('DIv'))) {
								$temporary_purchasetype = 'indent';
							}

							/* 	if($newpurchase[$report['rid']]['saleType'] != $temporary_purchasetype) {
							  $newpurchase[$report['rid']][$purchase['localpid']]['saleType'] = 'both';
							  }
							  else
							  { */
							$newpurchase[$report['rid']][$purchaseorderline['localpid']]['saleType'] = $temporary_purchasetype;
							//}
							/* Get sold quantity - START */
							$newpurchase[$report['rid']][$purchaseorderline['soldQty']] = $db->fetch_field($db->query("SELECT SUM(quantity) AS quantity
								FROM ".Tprefix."integration_mediation_salesorderlines
								WHERE pid='".$purchaseorderline['foreignpid']."' AND foreignOrderId IN (SELECT imsoid FROM ".Tprefix."integration_mediation_salesorders WHERE foreignSystem={$options[foreignSystem]} AND affid={$affid} AND (date BETWEEN ".strtotime($options['fromDate'])." AND ".strtotime($options['toDate'])."))"), 'quantity');
							/* Get sold quantity - END */
						}
						else {
							if(!isset($purchaseorder['foreignName']) || empty($purchaseorder['foreignName'])) {
								$purchaseorder['foreignName'] = $db->fetch_field($db->query("SELECT companyName FROM entities WHERE eid={$purchaseorderline[localspid]}"), 'companyName');
							}
							$errors['reportnotfound'][] = 'Q'.$quarter_info['quarter'].'/'.$quarter_info['year'].' '.$affid.'-'.$purchaseorder['foreignName'];
						}
					}
				}
			}
		}

		//if($options['method'] == 'normal') {
		//$query = $db->query("SELECT imso.*, ims.localId AS localspid, imp.localId AS localpid, ims.foreignName
		//					FROM integration_mediation_stockpurchases imso JOIN integration_mediation_products imp ON (imso.pid=imp.foreignId) JOIN integration_mediation_entities ims ON (imso.spid=ims.foreignId)
		//					WHERE ims.affid=imso.affid AND imso.affid={$affid} AND (imp.localId!=0 OR ims.localId=0) AND ims.entityType='s' AND (imso.date BETWEEN ".strtotime($options['fromDate'])." AND ".strtotime($options['toDate']).")");
		//}
		//elseif($options['method'] == 'productbase')
		//{
		//	$query = $db->query("SELECT imso.*, imp.localId as localpid, p.spid AS localspid
		//						FROM integration_mediation_stockpurchases imso JOIN integration_mediation_products imp ON (imso.pid=imp.foreignId) JOIN products p ON (p.pid=imp.localId) 
		//						WHERE imso.foreignSystem={$options[foreignSystem]} AND imso.affid={$affid} AND imp.localId!=0 AND (imso.date BETWEEN ".strtotime($options['fromDate'])." AND ".strtotime($options['toDate']).")");
		//}

		if(is_array($newpurchase)) {
			foreach($newpurchase as $rid => $products) {
				foreach($products as $pid => $activity) {
					echo $activity['rid'].' '.print_r($activity).'<hr />';
					//$db->insert_query('productsactivity', $activity);
					echo "Done<br />";
				}
			}
		
			if(is_array($errors)) {
				foreach($errors as $key => $val) {
					foreach($val as $error) {
						echo $error.'<br />';
					}
				}
			}
		}
		else {
			output_xml("<status>false</status><message>{$lang->na}</message>"); 
			exit;
		}
	}
}
?>