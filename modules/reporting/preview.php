<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Preview and export quarter reports
 * $module: reporting
 * $id: preview.php
 * Last Update: @zaher.reda 	March 07, 2012 | 04:33 PM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canFillReports'] == 0 && $core->usergroup['canGenerateReports'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

$session->start_phpsession();

if(!$core->input['action']) {
	if($core->input['referrer'] == 'generate' || $core->input['referrer'] == 'list') {
		if(!isset($core->input['year'], $core->input['quarter'], $core->input['spid'], $core->input['affid'])) {
			redirect('index.php?module=reporting/generatereport');
		}

		/* foreach($core->input as $key => $val) {
		  create_cookie($key, $val, (time() + (60*$core->settings['idletime']*2)));
		  } */

		if($core->input['generateType'] == 1) {
			$foreach = $core->input['affid'];
		}
		else {
			if($core->input['referrer'] == 'list') {
				$core->input['incMarketReport'] = $core->input['incKeyCustomers'] = $core->input['incKeyProducts'] = $core->input['genByProduct'] = 1;
				$core->input['spid'] = array($core->input['spid']);
			}
			$foreach = $core->input['spid'];
		}
	}
	elseif($core->input['referrer'] == 'direct') {
		if(isset($core->input['identifier'])) {
			$identifier = unserialize(base64_decode($core->input['identifier']));
			foreach($identifier as $key => $val) {
				$core->input[$key] = $val;
			}
			$core->input['incMarketReport'] = $core->input['incKeyCustomers'] = $core->input['incKeyProducts'] = $core->input['genByProduct'] = 1;
			$core->input['generateType'] = 1;
			$foreach = $core->input['affid'];
		}
		else {
			redirect('index.php?module=reporting/generatereport');
		}
	}
	else {
		$foreach = array(''); //Dummy array
	}

	$no_send_icon = true;
	$session_identifier = md5(uniqid(microtime()));

	/* Check if all reports are included - Start */
	$incomplete_report = false;
	if($core->input['generateType'] == 1) {
		$report_affiliates_query = $db->query("SELECT a.name, r.affid FROM ".Tprefix."reports r JOIN affiliates a ON (a.affid=r.affid) WHERE quarter='".$db->escape_string($core->input['quarter'])."' AND year='".$db->escape_string($core->input['year'])."' AND spid='".$db->escape_string($core->input['spid'])."'");
		while($report_affiliate = $db->fetch_assoc($report_affiliates_query)) {
			if(!in_array($report_affiliate['affid'], $core->input['affid'])) {
				$incomplete_report = true;
				$missing_affiliates[] = $report_affiliate['name'];
				//break;
			}
		}
	}
	$incomplete_report_notification = '';
	if($incomplete_report === true) {
		$missing_affiliates_list = implode(', ', $missing_affiliates);
		//$incomplete_report_popup = '<div id="popup_missingaffiliates" title="'.$lang->missingaffiliates.'">'.$missing_affiliates_list.'</div>';
		//$incomplete_report_notification = '<tr><td align="center"><span style="color:#993300; font:weight:100; font-size: 20px;"> <a href="#" id="showpopup_missingaffiliates" class="showpopup"><img src="images/notemark.gif" border="0"/></a> '.$lang->incompletereport.'</span>'.$incomplete_report_popup.'</td></tr>';
		$incomplete_report_notification = '<tr><td align="center"><span style="color:#993300; font:weight:100; font-size: 20px;"><img src="images/notemark.gif" border="0"/> '.$lang->incompletereport.'</span><br /><span class="smalltext">Missing: '.$missing_affiliates_list.'</span></td></tr>';
	}

	/* Check if all reports are included - End */
	foreach($foreach as $index => $entity) {
		$salesforperiod = $quantitiesforperiod = '';
		$productsdata = array();
		$productsdata_perquarter = array();
		if($core->input['referrer'] == 'generate' || $core->input['referrer'] == 'list' || $core->input['referrer'] == 'direct') {
			$report['quarter'] = $db->escape_string($core->input['quarter']);
			$report['year'] = $db->escape_string($core->input['year']);

			if($core->input['generateType'] == 1) {
				$report['affid'] = $entity;
				$report['spid'] = $db->escape_string($core->input['spid']);

				if($core->usergroup['canViewAllAff'] == 0) {
					if(!@in_array($report['affid'], $core->user['auditedaffiliates'][$report['spid']]) && !@in_array($report['affid'], $core->user['suppliers']['affid'][$report['spid']])) {
						if(count($foreach) == 1) {
							redirect($_SERVER['HTTP_REFERER']);
						}
						unset($foreach[$index]);
						continue;
					}
				}

				if($core->usergroup['canViewAllSupp'] == 0) {
					if(!in_array($report['spid'], $core->user['suppliers']['eid'])) {
						redirect($_SERVER['HTTP_REFERER']);
					}
				}
			}
			else {
				$report['affid'] = $db->escape_string($core->input['affid']);
				$report['spid'] = $entity;

				if($core->usergroup['canViewAllAff'] == 0) {
					if(!@in_array($report['affid'], $core->user['auditedaffiliates'][$report['spid']]) && !@in_array($report['affid'], $core->user['suppliers']['affid'][$report['spid']])) {
						redirect($_SERVER['HTTP_REFERER']);
					}
				}
				else {
					if(!value_exists('reports', 'affid', $report['affid'], "spid='".$report['spid']."' AND status='1'")) {
						redirect('index.php?module=reporting/generatereport');
					}
				}

				if($core->usergroup['canViewAllSupp'] == 0) {
					if(!in_array($report['spid'], $core->user['suppliers']['eid'])) {
						if(count($foreach) == 1) {
							redirect($_SERVER['HTTP_REFERER']);
						}
						continue;
					}
				}
				else {
					if(!value_exists('reports', 'spid', $report['spid'], "affid='".$report['affid']."' AND status='1'")) {
						redirect('index.php?module=reporting/generatereport');
					}
				}
			}

			$where_clause = "quarter='{$report[quarter]}' AND year='{$report[year]}' AND affid='{$report[affid]}' AND spid='{$report[spid]}'";

			list($report['rid']) = get_specificdata('reports', array('rid'), '0', 'rid', '', 0, $where_clause);
			$reports_meta_data['rid'][] = $report['rid'];
			$reports_meta_data['spid'][] = $report['spid'];
			$reports_meta_data['affid'][] = $report['affid'];
<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Preview and export quarter reports
 * $module: reporting
 * $id: preview.php
 * Last Update: @zaher.reda 	March 07, 2012 | 04:33 PM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canFillReports'] == 0 && $core->usergroup['canGenerateReports'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

$session->start_phpsession();

if(!$core->input['action']) {
	if($core->input['referrer'] == 'generate' || $core->input['referrer'] == 'list') {
		if(!isset($core->input['year'], $core->input['quarter'], $core->input['spid'], $core->input['affid'])) {
			redirect('index.php?module=reporting/generatereport');
		}

		/* foreach($core->input as $key => $val) {
		  create_cookie($key, $val, (time() + (60*$core->settings['idletime']*2)));
		  } */

		if($core->input['generateType'] == 1) {
			$foreach = $core->input['affid'];
		}
		else {
			if($core->input['referrer'] == 'list') {
				$core->input['incMarketReport'] = $core->input['incKeyCustomers'] = $core->input['incKeyProducts'] = $core->input['genByProduct'] = 1;
				$core->input['spid'] = array($core->input['spid']);
			}
			$foreach = $core->input['spid'];
		}
	}
	elseif($core->input['referrer'] == 'direct') {
		if(isset($core->input['identifier'])) {
			$identifier = unserialize(base64_decode($core->input['identifier']));
			foreach($identifier as $key => $val) {
				$core->input[$key] = $val;
			}
			$core->input['incMarketReport'] = $core->input['incKeyCustomers'] = $core->input['incKeyProducts'] = $core->input['genByProduct'] = 1;
			$core->input['generateType'] = 1;
			$foreach = $core->input['affid'];
		}
		else {
			redirect('index.php?module=reporting/generatereport');
		}
	}
	else {
		$foreach = array(''); //Dummy array
	}

	$no_send_icon = true;
	$session_identifier = md5(uniqid(microtime()));

	/* Check if all reports are included - Start */
	$incomplete_report = false;
	if($core->input['generateType'] == 1) {
		$report_affiliates_query = $db->query("SELECT a.name, r.affid FROM ".Tprefix."reports r JOIN affiliates a ON (a.affid=r.affid) WHERE quarter='".$db->escape_string($core->input['quarter'])."' AND year='".$db->escape_string($core->input['year'])."' AND spid='".$db->escape_string($core->input['spid'])."'");
		while($report_affiliate = $db->fetch_assoc($report_affiliates_query)) {
			if(!in_array($report_affiliate['affid'], $core->input['affid'])) {
				$incomplete_report = true;
				$missing_affiliates[] = $report_affiliate['name'];
				//break;
			}
		}
	}
	$incomplete_report_notification = '';
	if($incomplete_report === true) {
		$missing_affiliates_list = implode(', ', $missing_affiliates);
		//$incomplete_report_popup = '<div id="popup_missingaffiliates" title="'.$lang->missingaffiliates.'">'.$missing_affiliates_list.'</div>';
		//$incomplete_report_notification = '<tr><td align="center"><span style="color:#993300; font:weight:100; font-size: 20px;"> <a href="#" id="showpopup_missingaffiliates" class="showpopup"><img src="images/notemark.gif" border="0"/></a> '.$lang->incompletereport.'</span>'.$incomplete_report_popup.'</td></tr>';
		$incomplete_report_notification = '<tr><td align="center"><span style="color:#993300; font:weight:100; font-size: 20px;"><img src="images/notemark.gif" border="0"/> '.$lang->incompletereport.'</span><br /><span class="smalltext">Missing: '.$missing_affiliates_list.'</span></td></tr>';
	}

	/* Check if all reports are included - End */
	foreach($foreach as $index => $entity) {
		$salesforperiod = $quantitiesforperiod = '';
		$productsdata = array();
		$productsdata_perquarter = array();
		if($core->input['referrer'] == 'generate' || $core->input['referrer'] == 'list' || $core->input['referrer'] == 'direct') {
			$report['quarter'] = $db->escape_string($core->input['quarter']);
			$report['year'] = $db->escape_string($core->input['year']);

			if($core->input['generateType'] == 1) {
				$report['affid'] = $entity;
				$report['spid'] = $db->escape_string($core->input['spid']);

				if($core->usergroup['canViewAllAff'] == 0) {
					if(!@in_array($report['affid'], $core->user['auditedaffiliates'][$report['spid']]) && !@in_array($report['affid'], $core->user['suppliers']['affid'][$report['spid']])) {
						if(count($foreach) == 1) {
							redirect($_SERVER['HTTP_REFERER']);
						}
						unset($foreach[$index]);
						continue;
					}
				}

				if($core->usergroup['canViewAllSupp'] == 0) {
					if(!in_array($report['spid'], $core->user['suppliers']['eid'])) {
						redirect($_SERVER['HTTP_REFERER']);
					}
				}
			}
			else {
				$report['affid'] = $db->escape_string($core->input['affid']);
				$report['spid'] = $entity;

				if($core->usergroup['canViewAllAff'] == 0) {
					if(!@in_array($report['affid'], $core->user['auditedaffiliates'][$report['spid']]) && !@in_array($report['affid'], $core->user['suppliers']['affid'][$report['spid']])) {
						redirect($_SERVER['HTTP_REFERER']);
					}
				}
				else {
					if(!value_exists('reports', 'affid', $report['affid'], "spid='".$report['spid']."' AND status='1'")) {
						redirect('index.php?module=reporting/generatereport');
					}
				}

				if($core->usergroup['canViewAllSupp'] == 0) {
					if(!in_array($report['spid'], $core->user['suppliers']['eid'])) {
						if(count($foreach) == 1) {
							redirect($_SERVER['HTTP_REFERER']);
						}
						continue;
					}
				}
				else {
					if(!value_exists('reports', 'spid', $report['spid'], "affid='".$report['affid']."' AND status='1'")) {
						redirect('index.php?module=reporting/generatereport');
					}
				}
			}

			$where_clause = "quarter='{$report[quarter]}' AND year='{$report[year]}' AND affid='{$report[affid]}' AND spid='{$report[spid]}'";

			list($report['rid']) = get_specificdata('reports', array('rid'), '0', 'rid', '', 0, $where_clause);
			$reports_meta_data['rid'][] = $report['rid'];
			$reports_meta_data['spid'][] = $report['spid'];
			$reports_meta_data['affid'][] = $report['affid'];

			list($report['isApproved'], $report['isSent']) = $db->fetch_array($db->query("SELECT isApproved, isSent FROM ".Tprefix."reports WHERE rid='{$report[rid]}'"), MYSQL_NUM); //get_specificdata('reports', array('isApproved'), '0', 'isApproved', '', 0, "rid='{$report[rid]}'");

			$reports_id = base64_encode(serialize($report['rid']));

			if($report['isSent'] == 0) {
				$no_send_icon = false;
			}

			if($core->input['genByProduct'] == 1) {
				$query = $db->query("SELECT pa.*, p.name AS productname
									FROM ".Tprefix."productsactivity pa LEFT JOIN ".Tprefix."products p ON (p.pid=pa.pid)
									WHERE pa.rid='{$report[rid]}'
									GROUP BY pa.pid
									ORDER BY pa.turnOver DESC");
			}
			else {
				$query = $db->query("SELECT SUM(pa.turnOver) AS turnOver, SUM(pa.quantity) AS quantity, UM(pa.soldqty) AS soldqty, pa.salesForecast, pa.quantityForecast, ps.title AS productname, ps.psid AS pid
									FROM ".Tprefix."productsactivity pa JOIN ".Tprefix."products p ON(pa.pid=p.pid) JOIN ".Tprefix."genericproducts gp ON (p.gpid=gp.gpid) JOIN ".Tprefix."productsegments ps ON (gp.psid=ps.psid)
									WHERE pa.rid='{$report[rid]}'
									GROUP BY ps.title");
			}
			$i = 1;
			while($productsactivitydata = $db->fetch_array($query)) {
				$productsdata['pid'][$i] = $productsactivitydata['pid'];
				$productsdata['name'][$i] = $productsactivitydata['productname'];
				$productsdata['turnOver'][$i] = $productsactivitydata['turnOver'];
				$productsdata['soldQty'][$i] = $productsactivitydata['soldQty'];
				$productsdata['quantity'][$i] = $productsactivitydata['quantity'];
				$productsdata['saleType'][$i] = ucfirst($productsactivitydata['saleType']);
				$productsdata['salesForecast'][$i] = $productsactivitydata['salesForecast'];
				$productsdata['quantityForecast'][$i] = $productsactivitydata['quantityForecast'];

				if(!empty($productsactivitydata['originalCurrency'])) {
					$currencies[] = $productsactivitydata['originalCurrency'];
				}
				$i++;
			}

			$products_numrows = $db->num_rows($query);

			if($core->input['incKeyCustomers'] == 1) {
				$query2 = $db->query("SELECT kc.*, e.companyName FROM ".Tprefix."keycustomers kc LEFT JOIN ".Tprefix."entities e ON (e.eid=kc.cid) WHERE kc.rid='{$report[rid]}' ORDER BY kc.rank ASC");
				$keycustomers = '';
				while($keycustomer = $db->fetch_array($query2)) {
					$rank = $keycustomer['rank'];
					$customername = $keycustomer['companyName'];
					eval("\$keycustomers .= \"".$template->get("reporting_report_keycustomersbox_customerrow")."\";");
				}
			}
			if($core->input['incMarketReport'] == 1) {
				//	$marketreport_data = $db->fetch_array($db->query("SELECT * FROM ".Tprefix."marketreport WHERE rid='{$report[rid]}'"));
				$marketreport_data = array();
				$query = $db->query("SELECT mr.*, ps.title AS segmenttitle FROM ".Tprefix."marketreport mr LEFT JOIN ".Tprefix."productsegments ps ON (ps.psid=mr.psid) WHERE mr.rid='{$report[rid]}'");
				while($marketreports_rawdata = $db->fetch_assoc($query)) {
					$marketreport_data[$marketreports_rawdata['psid']] = $marketreports_rawdata;
					$query2 = $db->query("SELECT u.uid, displayName AS fullname FROM ".Tprefix."marketreport_authors ma JOIN ".Tprefix."users u ON (u.uid=ma.uid) WHERE mrid='{$marketreports_rawdata[mrid]}'");
					if($db->num_rows($query2) > 0) {
						while($author = $db->fetch_assoc($query2)) {
							$marketreport_data[$marketreports_rawdata['psid']]['authors'][$author['uid']] = $author['fullname'];
						}
					}
				}
			}
		}
		else {
			$identifier = $db->escape_string($core->input['identifier']);
			$session_identifier = $identifier;
			$productsactivitydata_session = $session->get_phpsession('productsactivitydata_'.$identifier);
			$keycustomersdata_session = $session->get_phpsession('keycustomersdata_'.$identifier);

			if(!isset($productsactivitydata_session, $keycustomersdata_session)) {
				redirect($_SERVER['HTTP_REFERER'].'&identifier='.$identifier);
			}

			$reportmeta = unserialize($session->get_phpsession('reportmeta_'.$identifier));
			$productsactivitydata = unserialize($productsactivitydata_session);
			$keycustomersdata = unserialize($keycustomersdata_session);
			$used_currencies = unserialize($session->get_phpsession('reportcurrencies_'.$identifier));

			$report['rid'] = $db->escape_string($reportmeta['rid']);
			$report['quarter'] = $db->escape_string($reportmeta['quarter']);
			$report['year'] = $db->escape_string($reportmeta['year']);
			$report['affid'] = $db->escape_string($reportmeta['affid']);
			$report['spid'] = $db->escape_string($reportmeta['spid']);

			$products_numrows = 0;
			if(empty($productsactivitydata['excludeProductsActivity'])) {
				for($q = 1, $i = 1; $q <= $productsactivitydata['numrows']; $q++) {
					if(empty($productsactivitydata['pid_'.$q])) {
						continue;
					}

					if(isset($productsactivitydata['paid_'.$q]) && !empty($productsactivitydata['paid_'.$q])) {
						$productsdata['paid'][$i] = $db->escape_string($productsactivitydata['paid_'.$q]);
					}
					$productsdata['pid'][$i] = $db->escape_string($productsactivitydata['pid_'.$q]);
					$productsdata['name'][$i] = $db->escape_string($productsactivitydata['product_'.$q.'_QSearch']);
					$productsdata['turnOver'][$i] = $db->escape_string($productsactivitydata['turnOver_'.$q]);
					if($productsactivitydata['fxrate_'.$q] != 1) {
						$productsdata['turnOverOc'][$i] = $productsactivitydata['turnOver_'.$q];
						$productsdata['turnOver'][$i] = $productsactivitydata['turnOver_'.$q] * $productsactivitydata['fxrate_'.$q];
						$productsdata['originalCurrency'][$i] = $used_currencies[$productsactivitydata['fxrate_'.$q]];
					}

					$productsdata['quantity'][$i] = $db->escape_string($productsactivitydata['quantity_'.$q]);
					$productsdata['soldQty'][$i] = $db->escape_string($productsactivitydata['soldQty_'.$q]);
					$productsdata['saleType'][$i] = $db->escape_string($productsactivitydata['saleType_'.$q]);
					$productsdata['salesForecast'][$i] = $db->escape_string($productsactivitydata['salesForecast_'.$q]);
					$productsdata['quantityForecast'][$i] = $db->escape_string($productsactivitydata['quantityForecast_'.$q]);
					$i++;
					$products_numrows++;
				}
			}

			foreach($core->input['marketreport'] as $key => $val) {
				if(isset($val['exclude']) && $val['exclude'] == 1) {
					continue;
				}
				$marketreport_data[$key] = $val;
				$marketreport_data[$key]['psid'] = $key;
				$marketreport_data[$key]['rid'] = $report['rid'];
			}
			if(empty($marketreport_data)) {
				redirect($_SERVER['HTTP_REFERER'].'&identifier='.$core->input['identifier']);
			}
			/* $marketreport['markTrendCompetition'] = $core->input['markTrendCompetition'];
			  $marketreport['quarterlyHighlights'] = $core->input['quarterlyHighlights'];
			  $marketreport['devProjectsNewOp'] = $core->input['devProjectsNewOp'];
			  $marketreport['issues'] = $core->input['issues'];
			  $marketreport['actionPlan'] = $core->input['actionPlan'];
			  $marketreport['remarks'] = $core->input['remarks'];
			 */
			$session->set_phpsession(array('marketreport_'.$identifier => serialize($marketreport_data)));

			$reportdata['excludeProductsActivity'] = $productsactivitydata['excludeProductsActivity'];

			if(empty($reportdata['excludeProductsActivity'])) {
				if(empty($productsdata)) {
					redirect($_SERVER['HTTP_REFERER'].'&identifier='.$core->input['identifier']);
				}
				arsort($productsdata['turnOver']);
				$reportdata['productsdata'] = &$productsdata;
			}

			if(empty($reportdata['excludeKeyCustomers'])) {
				for($q = 1, $i = 1; $q <= $keycustomersdata['numrows']; $q++) {
					if(empty($keycustomersdata['eid_'.$q])) {
						continue;
					}
					$rank = $i;
					$eid = $db->escape_string($keycustomersdata['eid_'.$q]);
					list($customername) = get_specificdata('entities', array('companyName'), '0', 'companyName', '', 0, "eid='{$eid}'");

					$keycustomersrawdata[] = array('rank' => $rank, 'cid' => $eid);
					eval("\$keycustomers .= \"".$template->get('reporting_report_keycustomersbox_customerrow')."\";");
					$i++;
				}
				$reportdata['keycustomersdata'] = &$keycustomersrawdata;
			}

			$reportdata['excludeKeyCustomers'] = $keycustomersdata['excludeKeyCustomers'];

			$reportdata['marketreportdata'] = &$marketreport_data;
			$reportdata['rid'] = $report['rid'];

			$session->set_phpsession(array('reportrawdata_'.$session_identifier => serialize($reportdata)));

			$core->input['incMarketReport'] = $core->input['incKeyProducts'] = $core->input['incKeyCustomers'] = $core->input['genByProduct'] = 1;
			if(!empty($reportdata['excludeKeyCustomers'])) {
				$core->input['incKeyCustomers'] = 0;
			}

			if(!empty($reportdata['excludeProductsActivity'])) {
				$core->input['incKeyProducts'] = 0;
			}
		}

		$report['quartername'] = 'Q'.$report[quarter].' '.$report[year];
		list($report['affiliate']) = get_specificdata('affiliates', array('name'), '0', 'name', '', 0, "affid='{$report[affid]}'");
		$cache['affiliates'][$report['affid']] = $report['affiliate'];
		list($report['supplier']) = get_specificdata('entities', array('companyName'), '0', 'companyName', '', 0, "eid='{$report[spid]}'");
		eval("\$highlightbox = \"".$template->get('reporting_report_highlightbox')."\";");

		$report['title'] = 'Q'.$report['quarter'].' '.$report['year'].' - '.$report['supplier'].' / '.$report['affiliate'];

		$current_year = $report['year'];
		$previous_year = $current_year - 1;

		$current_quarter = $report['quarter'];
		$salesbox = $quantitiesbox = '';

		if(!empty($productsdata)) {
			for($k = 1; $k <= $products_numrows; $k++) {
				if($core->input['genByProduct'] == 1) {
					$query = $db->query("SELECT SUM(pa.quantity) AS sumquantity, SUM(pa.soldqty) AS sumsoldqty, SUM(pa.turnOver) AS sumturnOver, SUM(pa.salesForecast) AS salesForecast, SUM(pa.quantityForecast) AS quantityForecast, r.year, r.quarter
										FROM ".Tprefix."productsactivity pa LEFT JOIN ".Tprefix."reports r ON (r.rid=pa.rid)
										WHERE pa.pid='".$productsdata['pid'][$k]."' AND r.affid='{$report[affid]}' AND r.spid='{$report[spid]}' AND (r.year = '{$current_year}' OR r.year = '{$previous_year}')
										GROUP BY r.year, r.quarter, pa.pid"); // AND r.quarter<='{$current_quarter}'
				}
				else {
					$query = $db->query("SELECT SUM(pa.turnOver) AS sumturnOver, SUM(pa.quantity) AS sumquantity, SUM(pa.soldqty) AS sumsoldqty, SUM(pa.salesForecast) AS salesForecast, SUM(pa.quantityForecast) AS quantityForecast, r.year, r.quarter
									FROM ".Tprefix."productsactivity pa JOIN ".Tprefix."products p ON (pa.pid=p.pid) JOIN ".Tprefix."genericproducts gp ON (p.gpid=gp.gpid) JOIN ".Tprefix."productsegments ps ON (gp.psid=ps.psid) JOIN ".Tprefix."reports r ON (r.rid=pa.rid)
									WHERE r.rid=pa.rid AND ps.psid='".$productsdata['pid'][$k]."' AND r.affid='{$report[affid]}' AND r.spid='{$report[spid]}' AND (r.year = '{$current_year}' OR r.year = '{$previous_year}') AND r.quarter<='{$current_quarter}'
									GROUP BY r.year, r.quarter");
				}
				$productsdata['salesupprevyearquarter'][$k] = 0;
				$productsdata['quantitiesupprevyearquarter'][$k] = 0;
				while($activity = $db->fetch_assoc($query)) {
					$productsdata_perquarter[$activity['quarter']][$activity['year']]['turnover'] += @round($activity['sumturnOver'], 2);
					$productsdata_perquarter[$activity['quarter']][$activity['year']]['quantity'] += @round($activity['sumquantity'], 2);
					$comparebox_totals[$activity['year']]['turnover'] = @round($activity['sumturnOver'], 2);
					$comparebox_totals[$activity['year']]['quantity'] = @round($activity['sumquantity'], 2);
					$comparebox_totals[$activity['year']]['soldqty'] = @round($activity['sumsoldqty'], 2);

					if($activity['quarter'] > $current_quarter) {
						continue;
					}

					if($activity['year'] == $current_year) {
						if($activity['quarter'] == $current_quarter) {
							$quarter_found = true;
						}

						$productsdata['salesuptoquarter'][$k] += @round($activity['sumturnOver'], 2);
						$productsdata['quantitiesuptoquarter'][$k] += @round($activity['sumquantity'], 2);
						$productsdata['soldqtyuptoquarter'][$k] += @round($activity['sumsoldqty'], 2);
						if($activity['quarter'] == $current_quarter) {
							$productsdata['salesforecastyear'][$k] = @round($activity['salesForecast'], 2);
							$productsdata['quantitiesforecastyear'][$k] = @round($activity['quantityForecast'], 2);
						}
					}
					else {
						$productsdata['salesupprevyearquarter'][$k] += @round($activity['sumturnOver'], 2);
						$productsdata['quantitiesupprevyearquarter'][$k] += @round($activity['sumquantity'], 2);
					}
				}

				if($quarter_found !== true && $core->input['referrer'] != 'generate') {
					$productsdata['salesuptoquarter'][$k] += $productsdata['turnOver'][$k];
					$productsdata['quantitiesuptoquarter'][$k] += $productsdata['quantity'][$k];
					$productsdata['soldqtyuptoquarter'][$k] += $productsdata['soldqty'][$k];
					$productsdata['salesforecastyear'][$k] = $productsdata['salesForecast'][$k];
					$productsdata['quantitiesforecastyear'][$k] = $productsdata['quantityForecast'][$k];
				}
				$quarter_found = false;
				if($core->input['genByProduct'] == 1) {
					$dataprevyear = $db->fetch_array($db->query("SELECT SUM(pa.quantity) AS sumquantity, SUM(pa.turnOver) AS sumturnOver
															FROM ".Tprefix."productsactivity pa LEFT JOIN ".Tprefix."reports r ON (r.rid=pa.rid)
															WHERE pa.pid='".$productsdata['pid'][$k]."' AND r.affid='$report[affid]' AND r.spid='{$report[spid]}' AND r.year='{$previous_year}'
															GROUP BY pa.pid"));
				}
				else {
					$dataprevyear = $db->fetch_array($db->query("SELECT SUM(pa.quantity) AS sumquantity, SUM(pa.turnOver) AS sumturnOver
															FROM ".Tprefix."productsactivity pa JOIN ".Tprefix."reports r ON (r.rid=pa.rid) JOIN ".Tprefix."products p ON (pa.pid=p.pid) JOIN ".Tprefix."genericproducts gp ON (p.gpid=gp.gpid) JOIN ".Tprefix."productsegments ps ON (gp.psid=ps.psid)
															WHERE ps.psid='".$productsdata['pid'][$k]."' AND r.affid='$report[affid]' AND r.spid='{$report[spid]}' AND r.year='{$previous_year}'")); //REVUSE
				}

				$productsdata['salesprevyear'][$k] = 0;
				$productsdata['quantitiesprevyear'][$k] = 0;
				$productsdata['salesachievedpercentage'][$k] = 100;
				$productsdata['quantitiesachievedpercentage'][$k] = 100;

				if(!empty($dataprevyear['sumturnOver'])) {
					$productsdata['salesprevyear'][$k] = @round($dataprevyear['sumturnOver'], 2);
					$productsdata['quantitiesprevyear'][$k] = @round($dataprevyear['sumquantity'], 2);
				}

				if(!empty($productsdata['salesforecastyear'][$k])) {
					$productsdata['salesachievedpercentage'][$k] = @round(($productsdata['salesuptoquarter'][$k] / ($productsdata['salesforecastyear'][$k] / 100)), 0);
				}
				else {
					$productsdata['quantitiesforecastyear'][$k] = 0;
				}
				if(!empty($productsdata['quantitiesforecastyear'][$k])) {
					$productsdata['quantitiesachievedpercentage'][$k] = @round(($productsdata['quantitiesuptoquarter'][$k] / ($productsdata['quantitiesforecastyear'][$k] / 100)), 0);
				}
				else {
					$productsdata['quantitiesforecastyear'][$k] = 0;
				}
				$productsdata_output = $productsdata;
				array_walk_recursive($productsdata_output, 'format_numbers', array('decimals' => 0, 'decimals_ignorezero' => true));

				eval("\$salesforperiod .= \"".$template->get('reporting_report_salesdatarow')."\";");
				eval("\$quantitiesforperiod .= \"".$template->get('reporting_report_quantitiesdatarow')."\";");
			}
		}

		$addproducts = array();
		$comparebox_totals = array();
		//if($current_quarter > 1) {
		if(empty($productsdata)) {
			$product_query_string = '';
		}
		else {
			$product_query_string = "pa.pid NOT IN (".implode(',', $productsdata['pid']).") AND ";
		}
		if($core->input['genByProduct'] == 1) {
			$additionproducts_query = $db->query("SELECT SUM(pa.quantity) AS sumquantity, SUM(pa.soldQty) AS sumsoldqty, SUM(pa.turnOver) AS sumturnOver, SUM(pa.salesForecast) AS salesForecast, SUM(pa.quantityForecast) AS quantityForecast, r.year, r.quarter, pa.pid, p.name
											FROM ".Tprefix."productsactivity pa LEFT JOIN ".Tprefix."reports r ON (r.rid=pa.rid) LEFT JOIN ".Tprefix."products p ON (p.pid=pa.pid)
											WHERE {$product_query_string}r.affid='{$report[affid]}' AND r.spid='{$report[spid]}' AND (r.year = '{$current_year}' OR r.year = '{$previous_year}')
											GROUP BY r.year, r.quarter, pa.pid
											ORDER by r.quarter ASC"); // AND r.quarter<='{$current_quarter}'
		}
		else {
			if(!empty($product_query_string)) {
				$product_query_string .= "ps.psid='".$productsdata['pid'][$k]."' AND ";
			}
			$additionproducts_query = $db->query("SELECT SUM(pa.turnOver) AS sumturnOver, SUM(pa.quantity) AS sumquantity, SUM(pa.soldQty) AS sumsoldqty, SUM(pa.salesForecast) AS salesForecast, SUM(pa.quantityForecast) AS quantityForecast, r.year, r.quarter, ps.title AS name, ps.psid AS pid
										FROM productsactivity pa JOIN products p ON (pa.pid=p.pid) JOIN genericproducts gp ON (p.gpid=gp.gpid) JOIN productsegments ps On (gp.psid=ps.psid) JOIN reports r ON (r.rid=pa.rid)
										WHERE {$product_query_string}r.affid='{$report[affid]}' AND r.spid='{$report[spid]}' AND (r.year = '{$current_year}' OR r.year = '{$previous_year}')
										GROUP BY r.year, r.quarter, ps.psid
										ORDER by r.quarter ASC"); //AND r.quarter<='{$current_quarter}'
		}
		while($addproduct = $db->fetch_assoc($additionproducts_query)) {

			$addproducts[$addproduct['pid']]['name'] = $addproduct['name'];
			$productsdata_perquarter[$addproduct['quarter']][$addproduct['year']]['turnover'] += @round($addproduct['sumturnOver'], 2);
			$productsdata_perquarter[$addproduct['quarter']][$addproduct['year']]['quantity'] += @round($addproduct['sumquantity'], 2);
			$comparebox_totals[$addproduct['year']]['turnover'] = @round($addproduct['sumturnOver'], 2);
			$comparebox_totals[$addproduct['year']]['quantity'] = @round($addproduct['sumquantity'], 2);

			if($addproduct['quarter'] <= $current_quarter) {

				if($addproduct['year'] == $current_year) {
					$addproducts[$addproduct['pid']]['salesuptoquarter'] += @round($addproduct['sumturnOver'], 2);
					$addproducts[$addproduct['pid']]['quantitiesuptoquarter'] += @round($addproduct['sumquantity'], 2);
					$addproducts[$addproduct['pid']]['soldqtyuptoquarter'] += @round($addproduct['sumsoldqty'], 2);

					$addproducts[$addproduct['pid']]['salesforecastyear'] = @round($addproduct['salesForecast'], 2);
					$addproducts[$addproduct['pid']]['quantitiesforecastyear'] = @round($addproduct['quantityForecast'], 2);
				}
				else {
					$addproducts[$addproduct['pid']]['salesprevyear'] = $addproducts[$addproduct['pid']]['salesupprevyearquarter'] += @round($addproduct['sumturnOver'], 2);
					$addproducts[$addproduct['pid']]['quantitiesprevyear'] = $addproducts[$addproduct['pid']]['quantitiesupprevyearquarter'] += @round($addproduct['sumquantity'], 2);
				}
			}
			else {
				if($addproduct['year'] != $current_year) {
					$addproducts[$addproduct['pid']]['salesprevyear'] += @round($addproduct['sumturnOver'], 2);
					$addproducts[$addproduct['pid']]['quantitiesprevyear'] += @round($addproduct['sumquantity'], 2);
					if(!isset($addproducts[$addproduct['pid']]['salesuptoquarter'])) {
						$addproducts[$addproduct['pid']]['salesuptoquarter'] = 0;
						$addproducts[$addproduct['pid']]['quantitiesuptoquarter'] = 0;
						$addproducts[$addproduct['pid']]['soldqtyuptoquarter'] = 0;
						$addproducts[$addproduct['pid']]['salesforecastyear'] = 0;
						$addproducts[$addproduct['pid']]['quantitiesforecastyear'] = 0;
					}
				}
			}
		}

		$k = $products_numrows;

		if(is_array($addproducts)) {
			eval("\$salesforperiod .= \"".$template->get('reporting_report_spacerrow')."\";");
			eval("\$quantitiesforperiod .= \"".$template->get('reporting_report_spacerrow')."\";");

			foreach($addproducts as $key => $val) {
				++$k;
				/* The below is required to show previous year purchases which were not purchased during this year */
				if(empty($val['salesuptoquarter']) && empty($val['salesforecastyear'])) {
					$val['salesforecastyear'] = $val['salesuptoquarter'] = 0;
					$val['quantitiesforecastyear'] = $val['quantitiesuptoquarter'] = 0;
					//continue;
				}
				$productsdata['name'][$k] = $val['name'];
				$productsdata['salesprevyear'][$k] = $productsdata['quantitiesprevyear'][$k] = $productsdata['salesupprevyearquarter'][$k] = $productsdata['quantitiesupprevyearquarter'][$k] = 0;
				$productsdata['salesachievedpercentage'][$k] = $productsdata['quantitiesachievedpercentage'][$k] = 0;

				$productsdata['salesuptoquarter'][$k] = $val['salesuptoquarter'];
				$productsdata['quantitiesuptoquarter'][$k] = $val['quantitiesuptoquarter'];
				$productsdata['soldqtyuptoquarter'][$k] = $val['soldqtyuptoquarter'];

				if(!empty($val['salesprevyear'])) {
					$productsdata['salesprevyear'][$k] = $val['salesprevyear'];
					$productsdata['salesupprevyearquarter'][$k] = $val['salesupprevyearquarter'];
				}
				if(!empty($val['quantitiesprevyear'])) {
					$productsdata['quantitiesprevyear'][$k] = $val['quantitiesprevyear'];
					$productsdata['quantitiesupprevyearquarter'][$k] = $val['quantitiesupprevyearquarter'];
				}

				$productsdata['salesforecastyear'][$k] = $val['salesforecastyear'];
				$productsdata['quantitiesforecastyear'][$k] = $val['quantitiesforecastyear'];

				$productsdata['salesachievedpercentage'][$k] = @round(($val['salesuptoquarter'] / ($val['salesforecastyear'] / 100)), 0);
				$productsdata['quantitiesachievedpercentage'][$k] = @round(($val['quantitiesuptoquarter'] / ($val['quantitiesforecastyear'] / 100)), 0);

				$productsdata_output = $productsdata;

				array_walk_recursive($productsdata_output, 'format_numbers', array('decimals' => 0, 'decimals_ignorezero' => true));
				eval("\$salesforperiod .= \"".$template->get('reporting_report_salesdatarow')."\";");
				eval("\$quantitiesforperiod .= \"".$template->get('reporting_report_quantitiesdatarow')."\";");
			}
		}
		//}	

		if(!empty($productsdata)) {
			$overviewtotals['affiliatename'][$report['affid']] = $overview2totals['affiliatename'][$report['affid']] = $report['affiliate'];

			$overviewtotals['uptoprevquarteryear'][$report['affid']] = $totals['uptoprevquartersales'] = @round(@array_sum($productsdata['salesupprevyearquarter']), 2);
			$overviewtotals['uptoquarter'][$report['affid']] = $totals['uptoquartersales'] = array_sum($productsdata['salesuptoquarter']); //@round(array_sum($productsdata['salesuptoquarter']), 2);
			$overviewtotals['prevyear'][$report['affid']] = $totals['prevyearsales'] = @round(array_sum($productsdata['salesprevyear']), 2);
			$overviewtotals['yearforecast'][$report['affid']] = $totals['salesforecast'] = @round(array_sum($productsdata['salesforecastyear']), 2);

			$overview2totals['uptoprevquarteryear'][$report['affid']] = $totals['uptoprevquarterquantities'] = @array_sum($productsdata['quantitiesupprevyearquarter']);
			$overview2totals['uptoquarter'][$report['affid']] = $totals['uptoquarterquantities'] = array_sum($productsdata['quantitiesuptoquarter']);
			$overview2totals['uptoquarter'][$report['affid']] = $totals['uptoquartersoldqty'] = array_sum($productsdata['soldqtyuptoquarter']);
			$overview2totals['prevyear'][$report['affid']] = $totals['prevyearquantities'] = array_sum($productsdata['quantitiesprevyear']);
			$overview2totals['yearforecast'][$report['affid']] = $totals['quantitiesforecast'] = array_sum($productsdata['quantitiesforecastyear']);

			if(!empty($totals['salesforecast'])) {
				$totals['salesachievedpercentage'] = @round(($totals['uptoquartersales'] / ($totals['salesforecast'] / 100)), 0);
			}
			else {
				$totals['salesachievedpercentage'] = 100;
			}
			if(empty($totals['quantitiesforecast'])) {
				$totals['quantitiesachievedpercentage'] = @round(($totals['uptoquarterquantities'] / ($totals['quantitiesforecast'] / 100)), 0);
			}
			else {
				$totals['quantitiesachievedpercentage'] = 100;
			}
			$totals_output = $totals;
			array_walk_recursive($totals_output, 'format_numbers', array('decimal' => 2, 'decimals_ignorezero' => true));

			/* Start Q compare boxes */
			$quarters_compare_firsttimein = true;
			$quarters_salescompare_rows = $quarters_quantitiescompare_rows = '';

			//Add missing quarter - Start
			if(count($productsdata_perquarter) < 4) {
				$available_quarters = array_keys($productsdata_perquarter);
				$missing_quarters = array_diff(array(1, 2, 3, 4), $available_quarters);
				foreach($missing_quarters as $val) {
					$productsdata_perquarter[$val] = array($previous_year => array('turnover' => 0, 'quantity' => 0),
							$current_year => array('turnover' => 0, 'quantity' => 0)
					);
				}
			}
			//Add missing quarter - End

			ksort($productsdata_perquarter);
			foreach($productsdata_perquarter as $key => $val) {
				$overviewqcompare[$key][$previous_year]['turnover'] += $val[$previous_year]['turnover'];
				if(!isset($val[$current_year]['turnover'])) {
					$val[$current_year]['turnover'] = 0;
				}
				$overviewqcompare[$key][$current_year]['turnover'] += $val[$current_year]['turnover'];

				$overviewqcompare[$key][$previous_year]['quantity'] += $val[$previous_year]['quantity'];
				if(!isset($val[$current_year]['quantity'])) {
					$val[$current_year]['quantity'] = 0;
				}
				$overviewqcompare[$key][$current_year]['quantity'] += $val[$current_year]['quantity'];

				$yearforecast_sales_cell = $yearforecast_quantities_cell = '';
				$overviewtotals_output = $overviewtotals;
				$overview2totals_output = $overview2totals;
				array_walk_recursive($overviewtotals_output, 'format_numbers', array('decimals' => 2, 'decimals_ignorezero' => true));
				array_walk_recursive($overview2totals_output, 'format_numbers', array('decimals' => 2, 'decimals_ignorezero' => true));

				if($quarters_compare_firsttimein == true) {
					$yearforecast_sales_cell = "<td class='lightdatacell_freewidth' rowspan='".(count($productsdata_perquarter) + 1)."'>".$overviewtotals_output['yearforecast'][$report['affid']].'</td>';
					$yearforecast_quantities_cell = "<td class='lightdatacell_freewidth' rowspan='".(count($productsdata_perquarter) + 1)."'>".$overview2totals_output['yearforecast'][$report['affid']].'</td>';

					$quarters_compare_firsttimein = false;
				}

				if(empty($val[$previous_year]['turnover'])) {
					$val['salesachivementprevyear'] = $val['quantityachivementprevyear'] = 100;
				}
				else {
					$val['salesachivementprevyear'] = @round(((($val[$current_year]['turnover'] * 100) / ($val[$previous_year]['turnover']))), 2); //@round(((($val[$current_year]['turnover'])/($val[$previous_year]['turnover'])*100)), 2);
					$val['quantityachivementprevyear'] = @round(((($val[$current_year]['quantity'] * 100) / ($val[$previous_year]['quantity']))), 2);
				}

				if(empty($val[$previous_year]['turnover'])) {
					$val[$previous_year]['turnover'] = $val[$previous_year]['turnover'] = 0;
				}
				if(empty($val[$previous_year]['quantity'])) {
					$val[$previous_year]['quantity'] = $val[$previous_year]['quantity'] = 0;
				}

				$val_output = $val;
				array_walk_recursive($val_output, 'format_numbers', array('decimals' => 0, 'decimals_ignorezero' => true));

				$quarter_totals[$previous_year]['turnover'][$report['affid']] += $val[$previous_year]['turnover'];
				$quarter_totals[$report['year']]['turnover'][$report['affid']] += $val[$report['year']]['turnover'];
				/* 	if(empty($overviewtotals['yearforecast'][$report['affid']])) {
				  $overviewtotals['yearforecast'][$report['affid']] = 2;
				  } */
				$quarters_salescompare_rows .= "<tr><td style='width: 10%;'>Q{$key}</td><td class='lightdatacell_freewidth'>{$val_output[$previous_year][turnover]}</td><td class='datacell_freewidth'>{$val_output[$report[year]][turnover]}</td><td class='lightdatacell_freewidth'>{$val_output[salesachivementprevyear]}%</td>{$yearforecast_sales_cell}<td class='lightdatacell_freewidth'>".round(@(($val[$report['year']]['turnover'] * 100) / $overviewtotals['yearforecast'][$report['affid']]), 0)."%</td></tr>";

				$quarter_totals[$previous_year]['quantity'][$report['affid']] += $val[$previous_year]['quantity'];
				$quarter_totals[$report['year']]['quantity'][$report['affid']] += $val[$report['year']]['quantity'];
				$quarters_quantitiescompare_rows .= "<tr><td style='width: 10%;'>Q{$key}</td><td class='lightdatacell_freewidth'>{$val_output[$previous_year][quantity]}</td><td class='datacell_freewidth'>{$val_output[$report[year]][quantity]}</td><td class='lightdatacell_freewidth'>{$val_output[quantityachivementprevyear]}%</td>{$yearforecast_quantities_cell}<td class='lightdatacell_freewidth'>".round(@(($val[$report['year']]['quantity'] * 100) / $overview2totals['yearforecast'][$report['affid']]), 0)."%</td></tr>";

				$quarter_totals_output = $quarter_totals;
				array_walk_recursive($quarter_totals_output, 'format_numbers', array('decimals' => 2, 'decimals_ignorezero' => true));
			}

			if(empty($overviewtotals['uptoprevquarteryear'][$report['affid']])) {
				$quarters_salescompare_achivementprevyear = $quarters_quantitiescompare_achivementprevyear = 100;
			}
			else {
				$quarters_salescompare_achivementprevyear = round(@(($quarter_totals[$report['year']]['turnover'][$report['affid']] * 100) / $quarter_totals[$previous_year]['turnover'][$report['affid']]), 2);
				$quarters_quantitiescompare_achivementprevyear = round(@(($quarter_totals[$report['year']]['quantity'][$report['affid']] * 100) / $quarter_totals[$previous_year]['quantity'][$report['affid']]), 2);
			}

			$quarters_salescompare_achivementforecast = round(@(($quarter_totals[$report['year']]['turnover'][$report['affid']] * 100) / $overviewtotals['yearforecast'][$report['affid']]), 2);
			$quarters_quantitiescompare_achivementforecast = round(@(($quarter_totals[$report['year']]['quantity'][$report['affid']] * 100) / $overview2totals['yearforecast'][$report['affid']]), 2);

			$productsdata_perquarter = array();
			/* End Q compare boxes */

			eval("\$salesbox = \"".$template->get('reporting_report_salesbox')."\";");
			eval("\$quantitiesbox = \"".$template->get('reporting_report_quantitiesbox')."\";");
		}

		if(empty($productsdata)) {
			$overviewtotals['affiliatename'][$report['affid']] = $overview2totals['affiliatename'][$report['affid']] = $report['affiliate'];
			$overviewtotals['uptoprevquarteryear'][$report['affid']] = $overview2totals['uptoprevquarteryear'][$report['affid']] = 0;
			$overviewtotals['uptoquarter'][$report['affid']] = $overview2totals['uptoquarter'][$report['affid']] = 0;
			$overviewtotals['prevyear'][$report['affid']] = $overview2totals['prevyear'][$report['affid']] = 0;
			$overviewtotals['yearforecast'][$report['affid']] = $overview2totals['yearforecast'][$report['affid']] = 0;
		}

		if($core->input['incKeyCustomers'] == 1) {
			$keycustomersbox = '';
			if(!empty($keycustomers)) {
				eval("\$keycustomersbox = \"".$template->get('reporting_report_keycustomersbox')."\";");
			}
		}

		$keyproducts = '';
		unset($keyproduct);
		if($core->input['incKeyProducts'] == 1) {
			if($core->input['genByProduct'] == 0) {
				$query = $db->query("SELECT pa.*, p.name
									FROM ".Tprefix."productsactivity pa LEFT JOIN ".Tprefix."products p ON (p.pid=pa.pid)
									WHERE pa.rid='{$report[rid]}'
									ORDER BY pa.turnOver ASC
									LIMIT 0,5");
				while($keyproduct = $db->fetch_array($query)) {
					if($keyproduct['turnOver'] == '0') {
						continue;
					}
					eval("\$keyproducts .= \"".$template->get('reporting_report_keyproductsbox_productrow')."\";");
				}
			}
			else {
				if(!empty($productsdata)) {
					if(is_array($productsdata['turnOver'])) {
						foreach($productsdata['turnOver'] as $k => $value) {
							if($value == '0') {
								continue;
							}
							foreach($productsdata as $key => $val) {
								$keyproduct[$key] = $productsdata[$key][$k];
							}
							eval("\$keyproducts .= \"".$template->get('reporting_report_keyproductsbox_productrow')."\";");
							if($k == 5) { //make the 5 as a setting
								break;
							}
						}
					}
				}
			}
			$keyproductsbox = '';
			if(!empty($keyproducts)) {
				eval("\$keyproductsbox = \"".$template->get('reporting_report_keyproductsbox')."\";");
			}
		}
		if($core->input['incMarketReport'] == 1) {
			$marketreportbox = '';
			foreach($marketreport_data as $key => $marketreport) {
				array_walk($marketreport, 'chtmlspecialchars');
				array_walk($marketreport, 'fix_newline');
				array_walk($marketreport, 'parse_ocode');

				if(is_array($marketreport['authors'])) {
					$contributors_overview[$report['affid']][$key] = $marketreport['authors'];
					$marketreport['authors'] = $lang->authors.': '.implode(', ', $marketreport['authors']);
					$cache['productsegments'][$key] = $marketreport['segmenttitle'];
				}

				eval("\$marketreportbox .= \"".$template->get('reporting_report_marketreportbox')."\";");
			}
		}

		$query = $db->query("SELECT rc.*, u.uid, u.firstName, u.lastName, u.email
							FROM ".Tprefix."reportcontributors rc,".Tprefix."users u, ".Tprefix."reports r
							WHERE r.rid=rc.rid AND rc.uid=u.uid AND r.rid='{$report[rid]}'
							ORDER BY u.firstName ASC");

		$lang->reportpreparedby_text = $lang->reportpreparedby;
		$lang->email_text = $lang->email;
		if($db->num_rows($query) > 0) {
			$contributors = '';
			while($employee = $db->fetch_array($query)) {
				$cache['emails'][$employee['uid']] = $employee['email'];
				eval("\$contributors .= \"".$template->get('reporting_preview_contributorrow')."\";");
				$lang->reportpreparedby_text = $lang->email_text = '';
			}
		}
		else {
			$employee['email'] = $core->user['email'];
			$employee['firstName'] = $core->user['firstName'];
			$employee['lastName'] = $core->user['lastName'];
			eval("\$contributors = \"".$template->get('reporting_preview_contributorrow')."\";");
		}

		eval("\$reports .= \"".$template->get('reporting_report')."\";");
	}

	/*
	 * End of generating reports
	 * Start gathering them up
	 */

	if(is_array($overviewtotals) && is_array($overview2totals)) {
		if($core->input['generateType'] == 1) {
			//array_walk_recursive($overviewtotals, 'ceil_by_reference');
			//array_walk_recursive($overview2totals, 'ceil_by_reference');

			$totalvalues['totaluptoprevquarteryear'] = array_sum($overviewtotals['uptoprevquarteryear']);
			$totalvalues['totaluptocurrentquarter'] = array_sum($overviewtotals['uptoquarter']);
			$totalvalues['totalprevyear'] = array_sum($overviewtotals['prevyear']);
			$totalvalues['totalyearforecast'] = array_sum($overviewtotals['yearforecast']);

			$totalvalues['totalquantitiesuptoprevquarteryear'] = array_sum($overview2totals['uptoprevquarteryear']);
			$totalvalues['totalquantitiesuptocurrentquarter'] = array_sum($overview2totals['uptoquarter']);
			$totalvalues['totalquantitiesprevyear'] = array_sum($overview2totals['prevyear']);
			$totalvalues['totalquantitiesyearforecast'] = array_sum($overview2totals['yearforecast']);

			if(count($overviewtotals['affiliatename']) > 1) {
				$uptoquarter_sum = array_sum($overviewtotals['uptoquarter']);
				if($uptoquarter_sum > 0) {

					$pie = new Charts(array('titles' => $overviewtotals['affiliatename'], 'values' => $overviewtotals['uptoquarter']), 'pie');
					$piechart = "<img src='{$pie->get_chart()}' />";
					$piechart_description = $lang->graphdistsales;

					arsort($overviewtotals['uptoquarter']);
					$affiliate_rank = 1;
					foreach($overviewtotals['uptoquarter'] as $key => $val) {
						$piechart_section_tablerows .= '<tr><td class="lightdatacell_freewidth" style="width: 1%;">'.$affiliate_rank.'</td><td>'.$overviewtotals['affiliatename'][$key].'</td><td class="lightdatacell_freewidth" style="width: 25%;">'.number_format($overviewtotals['uptoquarter'][$key], 0, '.', ' ').'</td><td class="datacell_freewidth">'.round((($overviewtotals['uptoquarter'][$key] * 100) / $uptoquarter_sum), 0).'%</td></tr>';
						$affiliate_rank++;
					}
					eval("\$piechart_section = \"".$template->get('reporting_report_overviewbox_piechart')."\";");
				}
			}

			if(count($overview2totals['affiliatename']) > 1) {
				$uptoquarter_sum = array_sum($overview2totals['uptoquarter']);
				if($uptoquarter_sum > 0) {
					$piechart_section_tablerows = '';
					$pie2 = new Charts(array('titles' => $overview2totals['affiliatename'], 'values' => $overview2totals['uptoquarter']), 'pie');

					$piechart = "<img src='{$pie2->get_chart()}' />";
					$piechart_description = $lang->graphdistquantities;

					arsort($overview2totals['uptoquarter']);
					$affiliate_rank = 1;
					foreach($overview2totals['uptoquarter'] as $key => $val) {
						$piechart_section_tablerows .= '<tr><td class="lightdatacell_freewidth" style="width: 1%;">'.$affiliate_rank.'</td><td>'.$overview2totals['affiliatename'][$key].'</td><td class="lightdatacell_freewidth" style="width: 25%;">'.number_format($overview2totals['uptoquarter'][$key], 0, '.', ' ').'</td><td class="datacell_freewidth">'.round((($overview2totals['uptoquarter'][$key] * 100) / $uptoquarter_sum), 0).'%</td></tr>';
						$affiliate_rank++;
					}
					eval("\$piechart2_section = \"".$template->get('reporting_report_overviewbox_piechart')."\";");
				}
			}

			foreach($foreach as $index => $affid) {
				$diffcurrency['actual'][$affid] = $overviewtotals['uptoquarter'][$affid] - $overviewtotals['uptoprevquarteryear'][$affid];
				$diffcurrency['forecast'][$affid] = $overviewtotals['yearforecast'][$affid] - $overviewtotals['prevyear'][$affid];

				$diffquantities['actual'][$affid] = $overview2totals['uptoquarter'][$affid] - $overview2totals['uptoprevquarteryear'][$affid];
				$diffquantities['forecast'][$affid] = $overview2totals['yearforecast'][$affid] - $overview2totals['prevyear'][$affid];

				$actual_divideby['sales'] = $overviewtotals['uptoprevquarteryear'][$affid];
				$actual_divideby['quantities'] = $overview2totals['uptoprevquarteryear'][$affid];
				/* foreach($actual_divideby as $key => $val) {
				  if($val == 0) {
				  $actual_divideby[$key] = 1;
				  }
				  } */

				$forecast_divideby['sales'] = $overviewtotals['prevyear'][$affid];
				$forecast_divideby['quantities'] = $overview2totals['prevyear'][$affid];
				/* foreach($forecast_divideby as $key => $val) {
				  if($val == 0) {
				  $forecast_divideby[$key] = 1;
				  }
				  } */

				//$diffpercentage['actual'][$affid] = round($diffcurrency['actual'][$affid]/$actual_divideby['sales'], 2);
				//$diffpercentage['forecast'][$affid] = round($diffcurrency['forecast'][$affid]/$forecast_divideby['sales'], 2);

				if($actual_divideby['sales'] == 0) {
					if($diffcurrency['actual'][$affid] > 0) {
						$diffpercentage['actual'][$affid] = 100;
					}
					else {
						$diffpercentage['actual'][$affid] = 0;
					}
				}
				else {
					$diffpercentage['actual'][$affid] = round(($diffcurrency['actual'][$affid] * 100) / $actual_divideby['sales'], 0);
				}
				if($forecast_divideby['sales'] == 0) {
					$diffpercentage['forecast'][$affid] = 100;
				}
				else {
					$diffpercentage['forecast'][$affid] = round(($diffcurrency['forecast'][$affid] * 100) / $forecast_divideby['sales'], 0);
				}
				//$diffquantitiespercentage['actual'][$affid] = round($diffquantities['actual'][$affid]/$actual_divideby['quantities'], 2);
				//$diffquantitiespercentage['forecast'][$affid] = round($diffquantities['forecast'][$affid]/$forecast_divideby['quantities'], 2);
				if($actual_divideby['quantities'] == 0) {
					if($diffquantities['actual'][$affid] > 0) {
						$diffquantitiespercentage['actual'][$affid] = 100;
					}
					else {
						$diffquantitiespercentage['actual'][$affid] = 0;
					}
				}
				else {
					$diffquantitiespercentage['actual'][$affid] = round(($diffquantities['actual'][$affid] * 100) / $actual_divideby['quantities'], 0);
				}
				if($forecast_divideby['quantities'] == 0) {
					$diffquantitiespercentage['forecast'][$affid] = 100;
				}
				else {
					$diffquantitiespercentage['forecast'][$affid] = round(($diffquantities['forecast'][$affid] * 100) / $forecast_divideby['quantities'], 0);
				}
			}

			arsort($overviewtotals['uptoquarter']);
			foreach($overviewtotals['uptoquarter'] as $affid => $val) {
				$overviewtotals_output = $overviewtotals;

				$overview2totals_output = $overview2totals;
				array_walk_recursive($overviewtotals_output, 'format_numbers', array('match_key' => $affid));
				array_walk_recursive($overview2totals_output, 'format_numbers', array('match_key' => $affid));

				$diffcurrency['actual'][$affid] = round($diffcurrency['actual'][$affid]);
				$diffcurrency['forecast'][$affid] = round($diffcurrency['forecast'][$affid]);
				$diffquantities['actual'][$affid] = round($diffquantities['actual'][$affid]);
				$diffquantities['forecast'][$affid] = round($diffquantities['forecast'][$affid]);

				eval("\$affiliatestotals_list .= \"".$template->get('reporting_report_overviewbox_affiliaterow')."\";");
				eval("\$affiliatesquantitiestotals_list .= \"".$template->get('reporting_report_overviewbox2_affiliaterow')."\";");
			}

			$totalvalues['actualdiffcurrency'] = array_sum($diffcurrency['actual']);
			$totalvalues['forecastdiffcurrency'] = array_sum($diffcurrency['forecast']);
			if($totalvalues['totaluptoprevquarteryear'] == 0) {
				$totalvalues['actualdiffpercentage'] = 100;
			}
			else {
				$totalvalues['actualdiffpercentage'] = round(($totalvalues['actualdiffcurrency'] * 100) / $totalvalues['totaluptoprevquarteryear'], 0); //round(array_sum($diffpercentage['actual']), 2);
			}
			if($totalvalues['totalprevyear'] == 0) {
				$totalvalues['forecastdiffpercentage'] = 100;
			}
			else {
				$totalvalues['forecastdiffpercentage'] = round(($totalvalues['forecastdiffcurrency'] * 100) / $totalvalues['totalprevyear'], 0); //round(array_sum($diffpercentage['forecast']), 2);
			}

			$totalvalues['actualdiffquantities'] = array_sum($diffquantities['actual']);
			$totalvalues['forecastdiffquantities'] = array_sum($diffquantities['forecast']);

			if($totalvalues['totalquantitiesuptoprevquarteryear'] == 0) {
				$totalvalues['actualquantitiesdiffpercentage'] = 100;
			}
			else {
				$totalvalues['actualquantitiesdiffpercentage'] = @(($totalvalues['actualdiffquantities'] * 100) / $totalvalues['totalquantitiesuptoprevquarteryear']);
			}
			//$totalvalues['actualquantitiesdiffpercentage'] = round(array_sum($diffquantitiespercentage['actual']), 2);

			$totalvalues['forecastquantitiesdiffpercentage'] = @(($totalvalues['forecastdiffquantities'] * 100) / $totalvalues['totalquantitiesyearforecast']);
			//$totalvalues['forecastquantitiesdiffpercentage'] = round(array_sum($diffquantitiespercentage['forecast']), 2);

			$totalvalues['quartersturnover'][$previous_year] = @array_sum($quarter_totals[$previous_year]['turnover']);
			$totalvalues['quartersturnover'][$report['year']] = @array_sum($quarter_totals[$report['year']]['turnover']);
			$totalvalues['quartersquantity'][$previous_year] = @array_sum($quarter_totals[$previous_year]['quantity']);
			$totalvalues['quartersquantity'][$report['year']] = @array_sum($quarter_totals[$report['year']]['quantity']);

			$totalvalues_output = $totalvalues;

			array_walk_recursive($totalvalues_output, 'format_numbers', array('decimals' => 0, 'decimals_ignorezero' => true));

			if(is_array($overviewqcompare)) {
				$quarters_compare_firsttimein = true;
				ksort($overviewqcompare);
				foreach($overviewqcompare as $key => $val) {
					$yearforecast_sales_cell = $yearforecast_quantities_cell = '';
					if($quarters_compare_firsttimein == true) {
						$yearforecast_sales_cell = "<td class='lightdatacell_freewidth' rowspan='".(count($overviewqcompare) + 1)."'>".number_format(array_sum($overviewtotals['yearforecast']), 0, '.', ' ').'</td>';
						$yearforecast_quantities_cell = "<td class='lightdatacell_freewidth' rowspan='".(count($overviewqcompare) + 1)."'>".number_format(array_sum($overview2totals['yearforecast']), 0, '.', ' ').'</td>';

						$quarters_compare_firsttimein = false;
					}

					if(empty($val[$previous_year]['turnover'])) {
						$val['salesachivementprevyear'] = $val['quantityachivementprevyear'] = 100;
					}
					else {
						$val['salesachivementprevyear'] = @round(((($val[$current_year]['turnover'] * 100) / ($val[$previous_year]['turnover']))), 0); //@round(((($val[$current_year]['turnover'])/($val[$previous_year]['turnover'])*100)), 2);
						$val['quantityachivementprevyear'] = @round(((($val[$current_year]['quantity'] * 100) / ($val[$previous_year]['quantity']))), 0);
					}

					$val_output = $val;
					array_walk_recursive($val_output, 'format_numbers', array('decimals' => 0, 'decimals_ignorezero' => true));

					$overview_quarters_salescompare_rows .= "<tr><td style='width: 10%;'>Q{$key}</td><td class='lightdatacell_freewidth'>{$val_output[$previous_year][turnover]}</td><td class='datacell_freewidth'>{$val_output[$report[year]][turnover]}</td><td class='lightdatacell_freewidth'>{$val_output[salesachivementprevyear]}%</td>{$yearforecast_sales_cell}<td class='lightdatacell_freewidth'>".round(@(($val[$report['year']]['turnover'] * 100) / array_sum($overviewtotals['yearforecast'])), 0)."%</td></tr>";
					$overview_quarters_quantitiescompare_rows .= "<tr><td style='width: 10%;'>Q{$key}</td><td class='lightdatacell_freewidth'>{$val_output[$previous_year][quantity]}</td><td class='datacell_freewidth'>{$val_output[$report[year]][quantity]}</td><td class='lightdatacell_freewidth'>{$val_output[quantityachivementprevyear]}%</td>{$yearforecast_quantities_cell}<td class='lightdatacell_freewidth'>".round(@(($val[$report['year']]['quantity'] * 100) / array_sum($overview2totals['yearforecast'])), 0)."%</td></tr>";

					$chart_index[$key] = $key;
					$chart_sales_values[$previous_year][$key] = $val[$previous_year]['turnover'] + $chart_sales_values[$previous_year][$key - 1];
					$chart_sales_values[$current_year][$key] = $val[$current_year]['turnover'] + $chart_sales_values[$current_year][$key - 1];
					$chart_sales_values['forecast'][$key] = array_sum($overviewtotals['yearforecast']);

					$chart_quantities_values[$previous_year][$key] = $val[$previous_year]['quantity'] + $chart_quantities_values[$previous_year][$key - 1];
					$chart_quantities_values[$current_year][$key] = $val[$current_year]['quantity'] + $chart_quantities_values[$current_year][$key - 1];
					$chart_quantities_values['forecast'][$key] = array_sum($overview2totals['yearforecast']);

					$barchart_sales_values[$previous_year][$key] = round($val[$previous_year]['turnover'], 0);
					$barchart_sales_values[$current_year][$key] = round($val[$current_year]['turnover'], 0);

					$barchart_quantities_values[$previous_year][$key] = round($val[$previous_year]['quantity'], 0);
					$barchart_quantities_values[$current_year][$key] = round($val[$current_year]['quantity'], 0);
				}

				if(array_sum($overviewtotals['uptoprevquarteryear']) == 0) {
					$quarters_salescompare_achivementprevyear = $quarters_quantitiescompare_achivementprevyear = 100;
				}
				else {
					$quarters_salescompare_achivementprevyear = round(($totalvalues['quartersturnover'][$report['year']] * 100) / $totalvalues['quartersturnover'][$previous_year], 2);
					$quarters_quantitiescompare_achivementprevyear = round(($totalvalues['quartersquantity'][$report['year']] * 100) / $totalvalues['quartersquantity'][$previous_year], 2);
				}

				$quarters_salescompare_achivementforecast = round(@(($totalvalues['quartersturnover'][$report['year']] * 100) / $totalvalues['totalyearforecast']), 2);
				$quarters_quantitiescompare_achivementforecast = round(@(($totalvalues['quartersquantity'][$report['year']] * 100) / $totalvalues['totalquantitiesyearforecast']), 2);

				$sales_barchart = new Charts(array('x' => array($previous_year => $previous_year, $current_year => $current_year), 'y' => $barchart_sales_values), 'bar');
				$sales_linechart = new Charts(array('x' => $chart_index, 'y' => array("$current_year" => $chart_sales_values[$current_year], "$previous_year" => $chart_sales_values[$previous_year], 'forecast' => $chart_sales_values['forecast'])), 'line', array('xaxisname' => 'Quarters', 'yaxisname' => 'Amounts', 'yaxisunit' => 'k$'));

				$quantities_barchart = new Charts(array('x' => array($previous_year => $previous_year, $current_year => $current_year), 'y' => $barchart_quantities_values), 'bar');
				$quantities_linechart = new Charts(array('x' => $chart_index, 'y' => array("$current_year" => $chart_quantities_values[$current_year], "$previous_year" => $chart_quantities_values[$previous_year], 'forecast' => $chart_quantities_values['forecast'])), 'line', array('xaxisname' => 'Quarters', 'yaxisname' => 'Quantities', 'yaxisunit' => ''));

				$chart_values = $chart_index = array();

				$quarters_comparision_charts = "<span class='subtitle'>{$lang->salesaccumlationquarters}</span><br /><img src='{$sales_linechart->get_chart()}' /><br /><span class='subtitle'>{$lang->salescomparisonbyquarteryear}</span><br /><img src='{$sales_barchart->get_chart()}' /><br />";
				eval("\$valuesbox .= \"".$template->get('reporting_report_overviewbox')."\";");

				$quarters_comparision_charts2 .= "<span class='subtitle'>{$lang->quantitiesaccumlationquarters}</span><br /><img src='{$quantities_linechart->get_chart()}' /><br /><span class='subtitle'>{$lang->quantitiescomparisonbyquarteryear}</span><br /><img src='{$quantities_barchart->get_chart()}' /><br />";
				eval("\$valuesbox2 .= \"".$template->get('reporting_report_overviewbox2')."\";");
			}

			$query = $db->query("SELECT er.*, r.*
								FROM ".Tprefix."entitiesrepresentatives er LEFT JOIN ".Tprefix."representatives r ON (r.rpid=er.rpid)
								WHERE er.eid='{$report[spid]}'
								ORDER BY name ASC");
			while($representative = $db->fetch_array($query)) {
				$representatives_list .= "<tr><td style='width: 25%; text-align: left;'>{$representative[name]}</td><td style='text-align: left;'>{$representative[email]}</td></tr>";
			}

			list($report['supplierlogo']) = get_specificdata('entities', array('logo'), '0', 'logo', '', 0, "eid='{$report[spid]}'");
			if(!empty($report['supplierlogo'])) {
				$report['supplierlogo'] = '<img src="./uploads/entitieslogos/'.$report['supplierlogo'].'" alt="'.$report['supplier'].'" width="200px"/><br /><span style="font-size:12px; font-weight:100;font-style:italic;">'.$report['supplier'].'</span>';
			}
			else {
				$report['supplierlogo'] = $report['supplier'];
			}

			eval("\$coverpage = \"".$template->get('reporting_report_coverpage')."\";");
			eval("\$closingpage = \"".$template->get('reporting_report_closingpage')."\";");
		}
	}


	if($core->input['referrer'] == 'generate' || $core->input['referrer'] == 'direct') {
		/* Output contrinutors table - START */
		if(is_array($contributors_overview)) {
			$contributors_overview_entries = '';
			foreach($contributors_overview as $affid => $contributions) {
				$contributors_overview_entries .= '<tr><td colspan="2" class="thead">'.$cache['affiliates'][$affid].'</td></tr>';
				foreach($contributions as $psid => $contributors) {
					if(empty($cache['productsegments'][$psid])) {
						$cache['productsegments'][$psid] = $lang->na;
					}
					foreach($contributors as $uid => $name) {
						if(isset($cache['emails'][$uid])) {
							$contributors[$uid] = '<a href="mailto:'.$cache['emails'][$uid].'">'.$name.'</a> (<a href="mailto:'.$cache['emails'][$uid].'">'.$cache['emails'][$uid].'</a>)';
						}
					}
					$contributors_overview_entries .= '<tr><td class="lightdatacell_freewidth" style="text-align:left;">'.$cache['productsegments'][$psid].'</td><td style="width:70%; border-bottom: 1px dashed #CCCCCC;">'.implode(', ', $contributors).'</td></tr>';
				}
			}

			$auditor = '-';
			if($core->input['generateType'] == 1) {
				$auditor = $db->fetch_assoc($db->query("SELECT displayName AS employeeName, u.email FROM ".Tprefix."users u JOIN ".Tprefix."suppliersaudits sa ON (sa.uid=u.uid) WHERE sa.eid={$report[spid]}"));
				if(empty($auditor)) {
					$auditor = $db->fetch_assoc($db->query("SELECT displayName AS employeeName, email FROM ".Tprefix."users WHERE uid=3"));
				}
			}
			eval("\$contributorspage = \"".$template->get('reporting_report_contributionoverview')."\";");
		}

		/* Output contrinutors table - END */

		/* Output summary table - START */
		if(is_array($report_summary)) {
			if($core->usergroup['canViewAllSupp'] == 1) {
			$report_summary = $db->fetch_assoc($db->query("SELECT rs.summary FROM ".Tprefix."reports r JOIN ".Tprefix."reporting_summary rs ON(r.summary=rs.rpsid) WHERE r.rid=".$report['rid'].""));
				eval("\$summarypage = \"".$template->get('reporting_report_summary')."\";");
			}
		}

		/* Output summary table  - END */

		/* Output currencies FX table - Start */
		if(is_array($currencies) && !empty($currencies)) {
			$currency = new Currencies('USD'); //$reports_meta_data['baseCurrency']);
			$currencies_from = date_timestamp_get(date_create_from_format('j/m/Y', $core->settings['q'.$report['quarter'].'start'].'/'.($report['year'] - 1)));
			$currencies_to = date_timestamp_get(date_create_from_format('j/m/Y', $core->settings['q'.$report['quarter'].'end'].'/'.$report['year']));
			$currencies_fx = $currency->get_average_fxrates_transposed($currencies, array('from' => $currencies_from, 'to' => $currencies_to), array('distinct_by' => 'alphaCode', 'precision' => 4));

			if(is_array($currencies_fx)) {
				foreach($currencies_fx as $rate => $fx_currency) {
					if(empty($fx_currency)) {
						continue;
					}

					$currency_rates_year = $currency->get_yearaverage_fxrate_monthbased($fx_currency, $report['year'], array('distinct_by' => 'alphaCode', 'precision' => 4));
					$currency_rates_prevyear = $currency->get_yearaverage_fxrate_monthbased($fx_currency, $report['year'] - 1, array('distinct_by' => 'alphaCode', 'precision' => 4));

					$fxrates_linechart = new Charts(array('x' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12), 'y' => array('$1 '.$report['year'] => $currency_rates_year, '$1 '.($report['year'] - 1) => $currency_rates_prevyear)), 'line', array('xaxisname' => 'Months ('.$report['year'].')', 'yaxisname' => $fx_currency.' Rate', 'yaxisunit' => '', 'fixedscale' => array('min' => min($currency_rates_year), 'max' => max($currency_rates_year)), 'width' => 600, 'height' => 187));

					$fx_rates_entries .= '<tr><td class="lightdatacell_freewidth" style="text-align:left; width:5%;">'.$fx_currency.'</td><td style="width:5%; border-bottom: 1px dashed #CCCCCC;">'.$rate.'</td><td style="border-bottom: 1px dashed #CCCCCC;"><img src="'.$fxrates_linechart->get_chart().'" /></td></tr>';
				}

				eval("\$fxratespage = \"".$template->get('reporting_report_fxrates')."\";");
			}
		}
		/* Output currencies FX table - END */
		if($core->usergroup['canViewAllSupp'] == 1) {
			eval("\$reportingeditsummary = \"".$template->get('reporting_report_editsummary')."\";");
		}
	}


	$reports = $coverpage.$contributorspage.$summarypage.$valuesbox.$valuesbox2.$reports.$fxratespage.$closingpage;
	if($core->input['referrer'] != 'generate' && $core->input['referrer'] != 'list' && $core->input['referrer'] != 'direct') {
		//$headerinc .= "<link href='{$core->settings[rootdir]}/css/jqueryuitheme/jquery-ui-1.7.2.custom.css' rel='stylesheet' type='text/css' />";

		/* Check who hasn't yet filled in the report - Start */
		$missing_employees_query1 = $db->query("SELECT DISTINCT(u.uid), displayName
												FROM ".Tprefix."users u JOIN ".Tprefix."assignedemployees ae ON (u.uid=ae.uid)
												WHERE ae.affid='{$report[affid]}' AND ae.eid='{$report[spid]}' AND u.gid NOT IN (SELECT gid FROM usergroups WHERE canUseReporting=0) AND u.uid NOT IN (SELECT uid FROM ".Tprefix."reportcontributors WHERE rid='{$report[rid]}' AND isDone=1) AND u.uid!={$core->user[uid]}"); // AND rc.rid='{$report[rid]}'
		while($assigned_employee = $db->fetch_assoc($missing_employees_query1)) {
			$missing_employees['name'][] = $assigned_employee['displayName'];
			$missing_employees['uid'][] = $assigned_employee['uid'];
		}

		if(is_array($missing_employees)) {
			$missing_employees_notification = '<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; font-weight:bold;">'.$lang->employeesnotfillpart.' <ul><li>'.implode('</li><li>', $missing_employees['name']).'</li></ul></div><br />';
		}

		if(($reportmeta['auditor'] == 1 && is_array($missing_employees)) || !is_array($missing_employees)) {
			$reporting_preview_tools_finalize_button = $lang->suretofinalizebody.' <p align="center"><input type="button" id="save_report_reporting/fillreport_Button" value="'.$lang->yes.'" class="button" onclick="$(\'#popup_finalizereportconfirmation\').dialog(\'close\')"/></p>';
			$reporting_preview_tools_finalize_type = 'finalize';
		}
		else {
			$reporting_preview_tools_finalize_button = $lang->cannotfinalizereport.' <p align="center"><input type="button" id="save_report_reporting/fillreport_Button" value="'.$lang->yes.'" class="button" onclick="$(\'#popup_finalizereportconfirmation\').dialog(\'close\')"/></p>';
			$reporting_preview_tools_finalize_type = 'saveonly';
		}
		/* Check who hasn't yet filled in the report - End */
		unset($cache);
		eval("\$tools .= \"".$template->get('reporting_preview_tools_finalize')."\";");
	}
	else {
		//$session_identifier .= '_'.$report['rid'];



		if($core->input['referrer'] == 'direct') {
			if($no_send_icon == false) {
				if($core->usergroup['reporting_canSendReportsEmail'] == 1) {
					$unique_array = array_unique($reports_meta_data['spid']);
					if(count(array_unique($reports_meta_data['spid'])) == 1 || $core->usergroup['canViewAllSupp'] == 1) {
						if(in_array($reports_meta_data['spid'][0], $core->user['auditfor']) || $core->usergroup['canViewAllSupp'] == 1) {
							$tools_send = "<a href='index.php?module=reporting/preview&amp;action=saveandsend&amp;identifier={$session_identifier}'><img src='images/icons/send.gif' border='0' alt='{$lang->sendbyemail}' /></a> ";
						}
					}
				}
			}
		}

		if($core->input['referrer'] == 'list' || $core->input['referrer'] == 'generate' || $core->input['referrer'] == 'direct') {
			if($report['isApproved'] == 0) {
				if($core->usergroup['reporting_canApproveReports'] == 1) {
					$can_approve = true;
					foreach(array_unique($reports_meta_data['spid']) as $key => $val) {
						if(!in_array($val, $core->user['auditfor'])) {
							$can_approve = false;
							break;
						}
					}
					if($can_approve == true || $core->usergroup['canViewAllSupp'] == 1) {
						$tools_approve = "<script language='javascript' type='text/javascript'>$(function(){ $('#approvereport').click(function() { sharedFunctions.requestAjax('post', 'index.php?module=reporting/preview', 'action=approve&identifier={$session_identifier}', 'approvereport_span', 'approvereport_span');}) });</script>";
						$tools_approve .= "<span id='approvereport_span'><a href='#approvereport' id='approvereport'><img src='images/valid.gif' alt='{$lang->approve}' border='0' /></a></span> | ";
					}
				}
			}
		}
		$tools = $tools_approve.$tools_send."<a href='index.php?module=reporting/preview&amp;action=exportpdf&amp;identifier={$session_identifier}' target='_blank'><img src='images/icons/pdf.gif' border='0' alt='{$lang->downloadpdf}'/></a>&nbsp;<a href='index.php?module=reporting/preview&amp;action=print&amp;identifier={$session_identifier}' target='_blank'><img src='images/icons/print.gif' border='0' alt='{$lang->printreport}'/></a>";
	}
	$reports_meta_data['type'] = 'q';
	$reports_meta_data['quarter'] = $report['quarter'];
	$reports_meta_data['year'] = $report['year'];

	$session->set_phpsession(array('reportsmetadata_'.$session_identifier => serialize($reports_meta_data)));
	$session->set_phpsession(array('reports_'.$session_identifier => $reports));


	//$core->settings['rootdir'].'/index.php?module=reporting/preview&referrer=direct&identifier='.base64_encode(serialize(array('year' => $report['year'], 'quarter' => $report['quarter'], 'spid' => $reports_meta_data['spid'][0], 'affid' => $reports_meta_data['affid'])));
	eval("\$reportspage .= \"".$template->get('reporting_preview')."\";");

	output_page($reportspage);
}
else {
	}


	$reports = $coverpage.$contributorspage.$summarypage.$valuesbox.$valuesbox2.$reports.$fxratespage.$closingpage;
	if($core->input['referrer'] != 'generate' && $core->input['referrer'] != 'list' && $core->input['referrer'] != 'direct') {
		//$headerinc .= "<link href='{$core->settings[rootdir]}/css/jqueryuitheme/jquery-ui-1.7.2.custom.css' rel='stylesheet' type='text/css' />";

		/* Check who hasn't yet filled in the report - Start */
		$missing_employees_query1 = $db->query("SELECT DISTINCT(u.uid), displayName
												FROM ".Tprefix."users u JOIN ".Tprefix."assignedemployees ae ON (u.uid=ae.uid)
												WHERE ae.affid='{$report[affid]}' AND ae.eid='{$report[spid]}' AND u.gid NOT IN (SELECT gid FROM usergroups WHERE canUseReporting=0) AND u.uid NOT IN (SELECT uid FROM ".Tprefix."reportcontributors WHERE rid='{$report[rid]}' AND isDone=1) AND u.uid!={$core->user[uid]}"); // AND rc.rid='{$report[rid]}'
		while($assigned_employee = $db->fetch_assoc($missing_employees_query1)) {
			$missing_employees['name'][] = $assigned_employee['displayName'];
			$missing_employees['uid'][] = $assigned_employee['uid'];
		}

		if(is_array($missing_employees)) {
			$missing_employees_notification = '<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; font-weight:bold;">'.$lang->employeesnotfillpart.' <ul><li>'.implode('</li><li>', $missing_employees['name']).'</li></ul></div><br />';
		}

		if(($reportmeta['auditor'] == 1 && is_array($missing_employees)) || !is_array($missing_employees)) {
			$reporting_preview_tools_finalize_button = $lang->suretofinalizebody.' <p align="center"><input type="button" id="save_report_reporting/fillreport_Button" value="'.$lang->yes.'" class="button" onclick="$(\'#popup_finalizereportconfirmation\').dialog(\'close\')"/></p>';
			$reporting_preview_tools_finalize_type = 'finalize';
		}
		else {
			$reporting_preview_tools_finalize_button = $lang->cannotfinalizereport.' <p align="center"><input type="button" id="save_report_reporting/fillreport_Button" value="'.$lang->yes.'" class="button" onclick="$(\'#popup_finalizereportconfirmation\').dialog(\'close\')"/></p>';
			$reporting_preview_tools_finalize_type = 'saveonly';
		}
		/* Check who hasn't yet filled in the report - End */
		unset($cache);
		eval("\$tools .= \"".$template->get('reporting_preview_tools_finalize')."\";");
	}
	else {
		//$session_identifier .= '_'.$report['rid'];



		if($core->input['referrer'] == 'direct') {
			if($no_send_icon == false) {
				if($core->usergroup['reporting_canSendReportsEmail'] == 1) {
					$unique_array = array_unique($reports_meta_data['spid']);
					if(count(array_unique($reports_meta_data['spid'])) == 1 || $core->usergroup['canViewAllSupp'] == 1) {
						if(in_array($reports_meta_data['spid'][0], $core->user['auditfor']) || $core->usergroup['canViewAllSupp'] == 1) {
							$tools_send = "<a href='index.php?module=reporting/preview&amp;action=saveandsend&amp;identifier={$session_identifier}'><img src='images/icons/send.gif' border='0' alt='{$lang->sendbyemail}' /></a> ";
						}
					}
				}
			}
		}

		if($core->input['referrer'] == 'list' || $core->input['referrer'] == 'generate' || $core->input['referrer'] == 'direct') {
			if($report['isApproved'] == 0) {
				if($core->usergroup['reporting_canApproveReports'] == 1) {
					$can_approve = true;
					foreach(array_unique($reports_meta_data['spid']) as $key => $val) {
						if(!in_array($val, $core->user['auditfor'])) {
							$can_approve = false;
							break;
						}
					}
					if($can_approve == true || $core->usergroup['canViewAllSupp'] == 1) {
						$tools_approve = "<script language='javascript' type='text/javascript'>$(function(){ $('#approvereport').click(function() { sharedFunctions.requestAjax('post', 'index.php?module=reporting/preview', 'action=approve&identifier={$session_identifier}', 'approvereport_span', 'approvereport_span');}) });</script>";
						$tools_approve .= "<span id='approvereport_span'><a href='#approvereport' id='approvereport'><img src='images/valid.gif' alt='{$lang->approve}' border='0' /></a></span> | ";
					}
				}
			}
		}
		$tools = $tools_approve.$tools_send."<a href='index.php?module=reporting/preview&amp;action=exportpdf&amp;identifier={$session_identifier}' target='_blank'><img src='images/icons/pdf.gif' border='0' alt='{$lang->downloadpdf}'/></a>&nbsp;<a href='index.php?module=reporting/preview&amp;action=print&amp;identifier={$session_identifier}' target='_blank'><img src='images/icons/print.gif' border='0' alt='{$lang->printreport}'/></a>";
	}
	$reports_meta_data['type'] = 'q';
	$reports_meta_data['quarter'] = $report['quarter'];
	$reports_meta_data['year'] = $report['year'];

	$session->set_phpsession(array('reportsmetadata_'.$session_identifier => serialize($reports_meta_data)));
	$session->set_phpsession(array('reports_'.$session_identifier => $reports));


	//$core->settings['rootdir'].'/index.php?module=reporting/preview&referrer=direct&identifier='.base64_encode(serialize(array('year' => $report['year'], 'quarter' => $report['quarter'], 'spid' => $reports_meta_data['spid'][0], 'affid' => $reports_meta_data['affid'])));
	eval("\$reportspage .= \"".$template->get('reporting_preview')."\";");

	output_page($reportspage);
}
else {
	if($core->input['action'] == 'do_savesummary') {
		$reportid = unserialize(base64_decode($core->input['reportids']));

		if(empty($core->input['summary'])) {
			output_xml("<status>false</status><message>{$lang->fillrequiredfield}</message>");
			return false;
		}
		elseif(value_exists('reporting_summary', 'summary', $core->input['summary'])) {
			output_xml("<status>false</status><message>{$lang->entryexists}</message>");
			return false;
		}
		else {
			$summary = $core->sanitize_inputs($core->input['summary'], array('method' => 'striponly', 'allowable_tags' => '<span><div><a><br><p><b><i><del><strike><img><video><audio><embed><param><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><figure><figcaption><strong><em><table><tr><td><th><tbody><thead><tfoot><h1><h2><h3><h4><h5><h6>', 'removetags' => true));
			$summary_report = array(
					'uid' => $core->user['uid'],
					'summary' => $summary
			);

		$reportid = unserialize(base64_decode($core->input['reportids']));

		if(empty($core->input['summary'])) {
			output_xml("<status>false</status><message>{$lang->fillrequiredfield}</message>");
			return false;
		}
		elseif(value_exists('reporting_summary', 'summary', $core->input['summary'])) {
			output_xml("<status>false</status><message>{$lang->entryexists}</message>");
			return false;
		}
		else {
			$summary = $core->sanitize_inputs($core->input['summary'], array('method' => 'striponly', 'allowable_tags' => '<span><div><a><br><p><b><i><del><strike><img><video><audio><embed><param><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><figure><figcaption><strong><em><table><tr><td><th><tbody><thead><tfoot><h1><h2><h3><h4><h5><h6>', 'removetags' => true));
			$summary_report = array(
					'uid' => $core->user['uid'],
					'summary' => $summary
			);

			$query = $db->insert_query("reporting_summary", $summary_report);
			if($query) {
				$summary_id = $db->last_id();
				output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
				$db->update_query('reports', array('summary' => $summary_id), 'rid='.$reportid);
			}
		}
	}
	if($core->input['action'] == 'exportpdf' || $core->input['action'] == 'print' || $core->input['action'] == 'saveandsend' || $core->input['action'] == 'approve') {
		if($core->input['action'] == 'print') {
			$show_html = 1;
			$content = "<link href='{$core->settings[rootdir]}/report_printable.css' rel='stylesheet' type='text/css' />";
			$content .= "<script language='javascript' type='text/javascript'>window.print();</script>";
		}
		else {
			$content = "<link href='styles.css' rel='stylesheet' type='text/css' />";
			$content .= "<link href='report.css' rel='stylesheet' type='text/css' />";
		}
		$content .= $session->get_phpsession('reports_'.$core->input['identifier']);

		//$identifier = explode('_', $core->input['identifier']);
		$meta_data = unserialize($session->get_phpsession('reportsmetadata_'.$core->input['identifier']));
		/* $suppliername = $db->fetch_field($db->query("SELECT e.companyName AS suppliername FROM ".Tprefix."entities e, ".Tprefix."reports r 
		  WHERE r.spid=e.eid AND r.rid='".$db->escape_string($meta_data['spid'][0])."'"), 'suppliername');
		 */
		$suppliername = $db->fetch_field($db->query("SELECT companyName AS suppliername FROM ".Tprefix."entities WHERE eid='".$db->escape_string($meta_data['spid'][0])."'"), 'suppliername');
		ob_end_clean();
		require_once ROOT.'/'.INC_ROOT.'html2pdf/html2pdf.class.php';
		$html2pdf = new HTML2PDF('P', 'A4', 'en');
		$html2pdf->pdf->SetDisplayMode('fullpage');
		$html2pdf->pdf->SetTitle($suppliername, true);
		$content = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $content);

		if($core->input['action'] == 'saveandsend') {
			set_time_limit(0);

			$html2pdf->WriteHTML($content, $show_html);
			$html2pdf->Output($core->settings['exportdirectory'].'quarterlyreports_'.$core->input['identifier'].'.pdf', 'F');
			redirect('index.php?module=reporting/sendbymail&amp;identifier='.$core->input['identifier']);
		}
		if($core->input['action'] == 'approve') {
			if($core->usergroup['reporting_canApproveReports'] == 1) {
				foreach($meta_data['rid'] as $key => $val) {
					$db->update_query('reports', array('isApproved' => 1), "rid='".$db->escape_string($val)."'");
				}
				output_xml("<status>true</status><message>{$lang->approved}</message>");
				$log->record($meta_data['rid'], 'approve');

				if($core->settings['sendreportsonapprove'] == 1) {
					$html2pdf->WriteHTML($content, $show_html);
					$html2pdf->Output($core->settings['exportdirectory'].'quarterlyreports_'.$core->input['identifier'].'.pdf', 'F');

					if(empty($core->settings['sendreportsto'])) {
						$core->settings['sendreportsto'] = $core->settings['adminemail'];
					}

					$query = $db->query("SELECT r.quarter, r.year, a.name, s.companyName
										FROM ".Tprefix."reports r, ".Tprefix."entities s, ".Tprefix."affiliates a
										WHERE r.spid=s.eid AND a.affid=r.affid AND r.rid='{$identifier[1]}'");

					list($quarter, $year, $affiliate_name, $supplier_name) = $db->fetch_array($query);
					$email_data = array(
							'from_email' => 'no-reply@ocos.orkila.com',
							'from' => 'OCOS Mailer',
							'to' => $core->settings['sendreportsto'],
							'subject' => 'Just approved: Q'.$quarter.' '.$year.' '.$supplier_name.'/'.$affiliate_name,
							'message' => 'Q'.$quarter.' '.$year.' '.$supplier_name.'/'.$affiliate_name.' was just approved. ('.date($core->settings['dateformat'].' '.$core->settings['timeformat'], TIME_NOW).')',
							'attachments' => array($core->settings['exportdirectory'].'quarterlyreports_'.$core->input['identifier'].'.pdf')
					);

					$mail = new Mailer($email_data, 'php');
					@unlink($core->settings['exportdirectory'].'quarterlyreports_'.$core->input['identifier'].'.pdf');
				}
			}
		}
		else {
			set_time_limit(0);
			$html2pdf->WriteHTML(trim($content), $show_html);
			$html2pdf->Output($suppliername.'_'.date($core->settings['dateformat'], TIME_NOW).'.pdf');
		}
	}
}
function format_numbers(&$value, $key, $options = array()) {
	if(is_numeric($value)) {
		if(isset($options['alsoceil'])) {
			if($options['alsoceil'] === true) {
				$value = ceil($value);
			}
		}

		if(!isset($options['match_key']) && !empty($options['match_key'])) {
			if($key != $match_key) {
				return false;
			}
		}

		$decimals = 0;
		if(isset($options['decimals']) && !empty($options['decimals'])) {
			$decimals = $options['decimals'];
		}

		if(isset($options['decimals_ignorezero']) && $options['decimals_ignorezero'] == true) {
			if($value >= 0 && $value < 1 && $value != 0) {
				$decimals = 2;
			}
		}

		if($decimals == 0) {
			$remainder = fmod($value, 1);

			if($remainder > 0.5) {
				$value = ceil($value);
			}
			elseif($remainder < 0.5) {
				$value = floor($value);
			}
		}
		$value = number_format($value, $decimals, '.', ' ');
	}
}

function ceil_by_reference(&$value, $key) {
	if(is_numeric($value)) {
		$value = ceil($value);
	}
}

?>