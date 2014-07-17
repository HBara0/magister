<?php
/*
 * Copyright � 2012 Orkila International Offshore, All Rights Reserved
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

        $currency_obj = new Currencies('USD');

        $affid = intval($core->input['affid']);
        $options = $core->input;
        $core->input['turnoverink'] = 0;
        $options['method'] = 'productbase';

        $options['fromDate'] = $options['year'].'-'.$core->settings['q'.$options['quarter'].'start'];
        $options['toDate'] = $options['year'].'-'.$core->settings['q'.$options['quarter'].'end'];

        $core->input['turnoverink'] = 0;
        $options['turnoverdivision'] = 1000;
        if($core->input['turnoverink'] == 1) {
            $options['turnoverdivision'] = 1;
        }
        if(isset($options['emptyreports'])) {
            if($options['emptyreports'] == 1) {
                $report_querywhere = ' AND NOT EXISTS (SELECT pa.* FROM productsactivity pa where r.rid=pa.rid)';
            }
            elseif($options['emptyreports'] == 0) {
                $report_querywhere = ' AND EXISTS (SELECT pa.* FROM productsactivity pa where r.rid=pa.rid)';
            }
        }

        if(!isset($options['salesonly']) || $options['salesonly'] != 1) {
            $po_query = $db->query("SELECT imso.*, ims.localId AS localspid, ims.foreignName AS foreignSupplierName
						FROM ".Tprefix."integration_mediation_purchaseorders imso
						LEFT JOIN ".Tprefix."integration_mediation_entities ims ON (imso.spid=ims.foreignId)
						WHERE imso.foreignSystem={$options[foreignSystem]} AND imso.affid={$affid} AND (imso.date BETWEEN ".strtotime($options['fromDate'])." AND ".strtotime($options['toDate']).")");

            if($db->num_rows($po_query) > 0) {
                while($purchaseorder = $db->fetch_assoc($po_query)) {
                    $pol_query = $db->query("SELECT DISTINCT(imsol.foreignId), imsol.*, imp.localId AS localpid, imsol.pid AS foreignpid, p.spid AS localspid, imp.foreignName AS productname
										FROM ".Tprefix."integration_mediation_purchaseorderlines imsol
										LEFT JOIN integration_mediation_products imp ON (imsol.pid=imp.foreignId)
										LEFT JOIN products p ON (p.pid=imp.localId)
										WHERE foreignOrderId='{$purchaseorder['foreignId']}'
										AND imp.foreignSystem={$options[foreignSystem]} AND (imp.affid={$affid} OR imp.affid=0)
										ORDER BY imp.foreignName ASC"); // AND imp.localId!=0
                    if($db->num_rows($pol_query) > 0) {
                        while($purchaseorderline = $db->fetch_assoc($pol_query)) {
                            if(is_empty($purchaseorderline['localspid'], $purchaseorderline['localpid'])) {
                                if(empty($purchaseorderline['productname'])) {
                                    $errors['productnotexist'][] = $purchaseorderline['foreignpid'].' - '.$purchaseorder['foreignSupplierName'];
                                }
                                else {
                                    $errors['productnotmatched'][] = $purchaseorderline['productname'].' - '.$purchaseorder['foreignSupplierName'];
                                }
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
                                $report = $db->fetch_assoc($db->query("SELECT r.* FROM reports r WHERE r.affid='{$affid}' AND r.quarter='{$quarter_info[quarter]}' AND r.year='{$quarter_info[year]}' AND r.spid='{$purchaseorderline[localspid]}' AND r.type='q'".$report_querywhere));
                                $reports_cache[$affid][$purchaseorderline['localspid']][$quarter_info['year']][$quarter_info['quarter']] = $report;
                            }

                            if(is_array($report)) {
                                if(!empty($purchaseorderline['producerPrice'])) {
                                    $purchaseorderline['price'] = $purchaseorderline['producerPrice'];
                                }
                                $purchaseorderline['amount'] = $purchaseorderline['quantity'] * $purchaseorderline['price'];
                                if(strtolower($purchaseorderline['quantityUnit']) == 'kg') {
                                    $purchaseorderline['quantity'] = $purchaseorderline['quantity'] / 1000;
                                }

                                if(!isset($newpurchase[$report['rid']][$purchaseorderline['localpid']])) {
                                    $newpurchase[$report['rid']][$purchaseorderline['localpid']] = array(
                                            'pid' => $purchaseorderline['localpid'],
                                            'quantity' => $purchaseorderline['quantity'],
                                            'rid' => $report['rid'],
                                            'uid' => 0
                                    );
                                }
                                else {
                                    $newpurchase[$report['rid']][$purchaseorderline['localpid']]['quantity'] += $purchaseorderline['quantity'];
                                }

                                if(strtoupper($purchaseorder['currency']) != 'USD') {
                                    if(empty($purchaseorder['usdFxrate'])) {
                                        $purchaseorderline['usdFxrate'] = $currency_obj->get_average_fxrate($purchaseorder['currency'], array('from' => strtotime(date('Y-m-d', $purchaseorder['date']).' 01:00'), 'to' => strtotime(date('Y-m-d', $purchaseorder['date']).' 24:00')));
                                        if(empty($purchaseorderline['usdFxrate'])) {
                                            $purchaseorderline['usdFxrate'] = $currency_obj->get_average_fxrate($purchaseorder['currency'], array('from' => strtotime($options['fromDate']), 'to' => strtotime($options['endDate'])));
                                        }
                                    }
                                    else {
                                        $purchaseorderline['usdFxrate'] = 1 / $purchaseorder['usdFxrate'];
                                    }

                                    $newpurchase[$report['rid']][$purchaseorderline['localpid']]['turnOver'] += (($purchaseorderline['amount'] / $purchaseorderline['usdFxrate']) / $options['turnoverdivision']);
                                    $newpurchase[$report['rid']][$purchaseorderline['localpid']]['turnOverOc'] += ($purchaseorderline['amount'] / $options['turnoverdivision']);
                                    $newpurchase[$report['rid']][$purchaseorderline['localpid']]['originalCurrency'] = $purchaseorder['currency'];
                                }
                                else {
                                    $newpurchase[$report['rid']][$purchaseorderline['localpid']]['turnOver'] += ($purchaseorderline['amount'] / $options['turnoverdivision']);
                                }

                                if(in_array(strtolower($purchaseorder['purchaseType']), array('ski', 'rei'))) {
                                    $temporary_purchasetype = 'distribution';
                                }
                                elseif(in_array(strtolower($purchaseorder['purchaseType']), array('di'))) {
                                    $temporary_purchasetype = 'indent';
                                }

                                if($newpurchase[$report['rid']][$purchaseorderline['localpid']]['saleType'] != $temporary_purchasetype) {
                                    $newpurchase[$report['rid']][$purchaseorderline['localpid']]['saleType'] = 'both';
                                }
                                else {
                                    $newpurchase[$report['rid']][$purchaseorderline['localpid']]['saleType'] = $temporary_purchasetype;
                                }
                                /* Get sold quantity - START */
                                $soldqty_query = $db->query("SELECT quantity, quantityUnit
								FROM ".Tprefix."integration_mediation_salesorderlines
								WHERE pid='".$purchaseorderline['foreignpid']."' AND foreignOrderId IN (SELECT foreignId FROM ".Tprefix."integration_mediation_salesorders WHERE foreignSystem={$options[foreignSystem]} AND affid={$affid} AND (date BETWEEN ".strtotime($options['fromDate'])." AND ".strtotime($options['toDate'])."))");
                                if($db->num_rows($soldqty_query) > 0) {
                                    $newpurchase[$report['rid']][$purchaseorderline['localpid']]['soldQty'] = 0;
                                    while($sale = $db->fetch_assoc($soldqty_query)) {
                                        if(strtolower($sale['quantityUnit']) == 'kg') {
                                            $sale['quantity'] = $sale['quantity'] / 1000;
                                        }
                                        $newpurchase[$report['rid']][$purchaseorderline['localpid']]['soldQty'] += $sale['quantity'];
                                        $useddata['foreignpid']['sale'][$purchaseorderline['foreignpid']] = $purchaseorderline['foreignpid'];
                                    }
                                }
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
        }
// AND imsol.foreignOrderId IN (SELECT imso.foreignId FROM '.Tprefix.'integration_mediation_salesorders imso WHERE imso.foreignSystem='.$options['foreignSystem'].' AND imso.affid='.$affid.' AND (imso.date BETWEEN '.strtotime($options['fromDate']).' AND '.strtotime($options['toDate']).'))
        $query = $db->query('SELECT imsol.pid AS foreignpid, imsol.foreignId FROM '.Tprefix.'integration_mediation_salesorderlines imsol WHERE NOT EXISTS (SELECT imp.foreignId FROM '.Tprefix.'integration_mediation_products imp WHERE imsol.pid=imp.foreignId AND imp.foreignSystem='.$options['foreignSystem'].' AND (imp.affid='.$affid.' OR imp.affid=0)) AND imsol.affid='.$affid.' AND imsol.foreignOrderId IN (SELECT imso.foreignId FROM '.Tprefix.'integration_mediation_salesorders imso WHERE imso.foreignSystem='.$options['foreignSystem'].' AND imso.affid='.$affid.' AND (imso.date BETWEEN '.strtotime($options['fromDate']).' AND '.strtotime($options['toDate']).'))');
        if($db->num_rows($query) > 0) {
            while($missing = $db->fetch_assoc($query)) {
                $errors['productnotexist'][] = $missing['foreignpid'].' | '.$missing['foreignId'];
            }
        }

        $query = $db->query('SELECT imsol.pid AS foreignpid, imsol.foreignId FROM '.Tprefix.'integration_mediation_purchaseorderlines imsol WHERE NOT EXISTS (SELECT imp.foreignId FROM '.Tprefix.'integration_mediation_products imp WHERE imsol.pid=imp.foreignId AND imp.foreignSystem='.$options['foreignSystem'].' AND (imp.affid='.$affid.' OR imp.affid=0)) AND imsol.affid='.$affid.' AND imsol.foreignOrderId IN (SELECT imso.foreignId FROM '.Tprefix.'integration_mediation_purchaseorders imso WHERE imso.foreignSystem='.$options['foreignSystem'].' AND imso.affid='.$affid.' AND (imso.date BETWEEN '.strtotime($options['fromDate']).' AND '.strtotime($options['toDate']).'))');
        if($db->num_rows($query) > 0) {
            while($missing = $db->fetch_assoc($query)) {
                $errors['productnotexist'][] = $missing['foreignpid'].' | '.$missing['foreignId'];
            }
        }

        if(is_array($useddata['foreignpid']['sale'])) {
            //$useddata['foreignpid']['sale'] = array();
            $sales_query_extrawhere = " AND imsol.pid NOT IN ('".implode('\',\'', $useddata['foreignpid']['sale'])."')";
        }

        $query = $db->query("SELECT DISTINCT(imsol.foreignId), quantity, quantityUnit, imp.localId AS localpid, p.spid AS localspid, imsol.pid AS foreignpid, imp.foreignName AS productname, ims.foreignName AS foreignSupplierName
								FROM ".Tprefix."integration_mediation_salesorderlines imsol
								LEFT JOIN ".Tprefix."integration_mediation_products imp ON (imsol.pid=imp.foreignId)
								LEFT JOIN ".Tprefix."integration_mediation_entities ims ON (imp.foreignSupplier=ims.foreignId)
								LEFT JOIN ".Tprefix."products p ON (p.pid=imp.localId)
								WHERE imp.foreignSystem={$options[foreignSystem]} AND (imp.affid={$affid} OR imp.affid=0)
								AND foreignOrderId IN (SELECT foreignId FROM ".Tprefix."integration_mediation_salesorders WHERE foreignSystem={$options[foreignSystem]} AND affid={$affid} AND (date BETWEEN ".strtotime($options['fromDate'])." AND ".strtotime($options['toDate'])."))
								".$sales_query_extrawhere);

        /* GET Quarter Information - START */
        $quarter_info = quarter_info(strtotime($options['fromDate']));
        /* GET Quarter Information - END */
        if($db->num_rows($query) > 0) {
            while($sale = $db->fetch_assoc($query)) {
                if(is_empty($sale['localspid'], $sale['localpid'])) {
                    if(empty($sale['productname'])) {
                        $errors['productnotexist'][] = $sale['foreignpid'].' - '.$sale['foreignSupplierName'];
                    }
                    else {
                        $errors['productnotmatched'][] = $sale['productname'].' - '.$sale['foreignSupplierName'];
                    }
                    continue;
                }

                if(isset($reports_cache[$affid][$sale['localspid']][$quarter_info['year']][$quarter_info['quarter']])) {
                    $report = $reports_cache[$affid][$sale['localspid']][$quarter_info['year']][$quarter_info['quarter']];
                }
                else {
                    $report = $db->fetch_assoc($db->query("SELECT r.* FROM reports r WHERE r.affid='{$affid}' AND r.quarter='{$quarter_info[quarter]}' AND r.year='{$quarter_info[year]}' AND r.spid='{$sale[localspid]}' AND r.type='q'".$report_querywhere));
                    $reports_cache[$affid][$sale['localspid']][$quarter_info['year']][$quarter_info['quarter']] = $report;
                }

                if(is_array($report)) {
                    if(strtolower($sale['quantityUnit']) == 'kg') {
                        $sale['quantity'] = $sale['quantity'] / 1000;
                    }
                    if(!isset($newpurchase[$report['rid']][$sale['localpid']])) {
                        $newpurchase[$report['rid']][$sale['localpid']] = array(
                                'pid' => $sale['localpid'],
                                'quantity' => 0,
                                'turnOver' => 0,
                                'rid' => $report['rid'],
                                'uid' => 0,
                                'soldQty' => $sale['quantity']
                        );
                    }
                    else {
                        $newpurchase[$report['rid']][$sale['localpid']]['soldQty'] += $sale['quantity'];
                    }
                }
                else {
                    if(!isset($sale['foreignName']) || empty($sale['foreignName'])) {
                        $sale['foreignName'] = $db->fetch_field($db->query("SELECT companyName FROM entities WHERE eid={$sale[localspid]}"), 'companyName');
                    }

                    $errors['reportnotfound'][] = 'Q'.$quarter_info['quarter'].'/'.$quarter_info['year'].' '.$affid.'-'.$sale['foreignName'];
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

        echo '<h1>'.$options['quarter'].' '.$options['year'].'</h1>';
        if(is_array($newpurchase)) {
            foreach($newpurchase as $rid => $products) {
                foreach($products as $pid => $activity) {
                    if(empty($activity)) {
                        continue;
                    }

                    if($options['operation'] != 'replace') {
                        $pacheck_querywhere = ' AND uid=0';
                    }

                    if(value_exists('productsactivity', 'rid', $rid, 'pid='.$pid.$pacheck_querywhere)) {
                        if($options['runtype'] == 'dry' && ($options['operation'] == 'addonly' || $options['operation'] == 'replace')) {
                            if($options['operation'] == 'replace') {
                                echo 'Skipped Replace: ';
                            }
                            else {
                                echo 'Skipped Update: ';
                            }
                        }
                        else {
                            if($options['operation'] == 'replace') {
                                echo 'Replaced: ';
                            }
                            else {
                                echo 'Updated: ';
                                $paupdate_querywhere = ' AND uid=0';
                            }
                            $db->update_query('productsactivity', $activity, 'rid='.$rid.' AND pid='.$pid.$paupdate_querywhere);
                        }
                    }
                    else {
                        if($options['runtype'] == 'dry' || $options['operation'] == 'updateonly') {
                            echo 'Skipped Add: ';
                        }
                        else {
                            echo 'Added: ';
                            if($options['runtype'] != 'dry') {
                                $db->insert_query('productsactivity', $activity);
                            }
                        }
                    }
                    echo $activity['rid'].' '.print_r($activity).'<hr />';
                    echo "Done<br />";
                }
                if($options['runtype'] != 'dry') {
                    $db->update_query('reports', array('isLocked' => 0, 'status' => 0, 'prActivityAvailable' => 1), 'rid='.$rid);
                }
            }
            if(is_array($errors)) {
                foreach($errors as $key => $val) {
                    echo '-'.$key.':<br />';
                    $val = array_unique($val);
                    foreach($val as $error) {
                        echo $error.'<br />';
                    }
                }
            }
        }
        else {
            if(is_array($errors)) {
                foreach($errors as $key => $val) {
                    echo '-'.$key.':<br />';
                    $val = array_unique($val);
                    foreach($val as $error) {
                        echo $error.'<br />';
                    }
                }
            }
            output_xml("<status>false</status><message>{$lang->na}</message>");
            exit;
        }
    }
}
function quarter_info($time) {
    return array('quarter' => ceil(date('m', $time) / 3), 'year' => date('Y', $time));
}

?>