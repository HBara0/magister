<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Sales Report
 * $module: CRM
 * $id: salesreport.php
 * Created: 	@zaher.reda		September 12, 2012 | 10:11 AM
 * Modified: 	@zaher.reda		September 12, 2012 | 10:11 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['crm_canGenerateSalesReports'] == 0) {
    error($lang->sectionnopermission);
}

if($core->usergroup['canAdminCP'] == 0) {
    error($lang->sectionnopermission);
}

if(!isset($core->input['identifier'])) {
    $identifier = substr(md5(uniqid(microtime())), 1, 10);
}
else {
    $identifier = $core->input['identifier'];
}
$session->name_phpsession(COOKIE_PREFIX.'sreport'.$identifier);
$session->start_phpsession();

$lang->load('crm_salesreport');
if(!$core->input['action']) {
    $affiliates_query = $db->query("SELECT a.affid, a.name
						  FROM ".Tprefix."affiliates a LEFT JOIN ".Tprefix."affiliatedemployees ae ON (ae.affid=a.affid)
						  WHERE ae.uid='{$core->user[uid]}'
						  ORDER BY a.name ASC");

    while($affiliate = $db->fetch_array($affiliates_query)) {
        $affiliates[$affiliate['affid']] = $affiliate['name'];
    }

    $saletypes_list = parse_selectlist('saleType', 2, array('0' => $lang->any, 's-1' => $lang->stock, 'r-1' => $lang->reinvoice), '');
    $affiliates_list = parse_selectlist('affids[]', 2, $affiliates, '');

    if($core->usergroup['canViewAllSupp'] == 0) {
        if(!is_array($core->user['suppliers']['eid'])) {
            error($lang->sectionnopermission);
        }
        $suppliers_where = $products_where = ' AND eid IN ('.implode(',', $core->user['suppliers']['eid']).')';
        $suppliers_where = ' AND '.$suppliers_where;
    }
    $suppliers = get_specificdata('entities', array('eid', 'companyName'), 'eid', 'companyName', array('by' => 'companyName', 'sort' => 'ASC'), 0, 'type="s"'.$suppliers_where);
    $suppliers_list = parse_selectlist('spid[]', 9, $suppliers, '', 1);

    if($core->usergroup['canViewAllCust'] == 0) {
        if(!is_array($core->user['customers'])) {
            error($lang->sectionnopermission);
        }
        $customers_where = '  AND eid IN ('.implode(',', $core->user['customers']).')';
    }
    $customers = get_specificdata('entities', array('eid', 'companyName'), 'eid', 'companyName', array('by' => 'companyName', 'sort' => 'ASC'), 0, 'type="c"'.$customers_where);
    $customers_list = parse_selectlist('cid[]', 9, $customers, '', 1);

    $products = get_specificdata('products', array('pid', 'name'), 'pid', 'name', array('by' => 'name', 'sort' => 'ASC'), 0, $products_where);
    $products_list = parse_selectlist('pid[]', 9, $products, '', 1);

    $fxtypes_selectlist = parse_selectlist('fxtype', 9, array('lastm' => $lang->lastmonthrate, 'ylast' => $lang->yearlatestrate, 'yavg' => $lang->yearaveragerate, 'mavg' => $lang->monthaveragerate, 'real' => $lang->realrate), '', 0);
    eval("\$generatepage = \"".$template->get('crm_generatesalesreport')."\";");
    output_page($generatepage);
}
else {
    if($core->input['action'] == 'do_generatereport') {
        if(empty($core->input['affids'])) {
            redirect('index.php?module=crm/salesreport');
        }

        if(is_empty($core->input['fromDate'])) {
            redirect('index.php?module=crm/salesreport');
        }

        $current_date = getdate(TIME_NOW);
        $period['from'] = strtotime($core->input['fromDate']); //strtotime($current_date['year'].'-1-1');//'30 minutes ago';
        if(empty($core->input['toDate'])) {
            $period['to'] = TIME_NOW;
        }
        else {
            $period['to'] = strtotime($core->input['toDate']); //strtotime('last day of this month');;
        }

        $currency_obj = new Currencies('USD');
        //Verify AFFids
        if(!empty($core->input['spid'])) {
            $orderline_query_where = ' AND ime.localId IN ('.implode(',', $core->input['spid']).')';
        }

        if(!empty($core->input['pid'])) {
            $orderline_query_where .= ' AND imp.localId IN ('.implode(',', $core->input['pid']).')';
        }

        if(!empty($core->input['cid'])) {
            $query_where .= ' AND ime.localId IN ('.implode(',', $core->input['cid']).')';
        }

        $query = $db->query("SELECT imso.foreignId AS order_id, date, docNum, foreignName AS bpname, cid AS bpid, ime.foreignNameAbbr AS bpname_abv, currency, salesRepLocalId AS salesrep_id, salesRep AS salesrep, imso.paymentTerms AS paymenttermsdays, usdFxrate
					FROM integration_mediation_salesorders imso
					JOIN integration_mediation_entities ime ON (ime.foreignId=imso.cid)
					WHERE imso.affid IN (".$db->escape_string(implode(',', $core->input['affids'])).") AND (date BETWEEN ".$period['from']." AND ".$period['to']."){$query_where}
					ORDER by date DESC");

        if($db->num_rows($query) > 0) {
            while($order = $db->fetch_assoc($query)) {
                if(!isset($average_fx[$order['currency']])) {
                    $average_fx[$order['currency']] = $currency_obj->get_fxrate_bytype($core->input['fxtype'], $order['currency'], array('from' => strtotime(date('Y-m-d', $order['date']).' 01:00') - (7 * 24 * 3600), 'to' => strtotime(date('Y-m-d', $order['date'])), 'year' => date('Y', $order['date']), 'month' => date('m', $order['date'])), array('precision' => 4));
                }

                $fxrates['price'] = $currency_obj->get_fxrate_bytype($core->input['fxtype'], $order['currency'], array('from' => strtotime(date('Y-m-d', $order['date']).' 01:00'), 'to' => strtotime(date('Y-m-d', $order['date']).' 24:00'), 'year' => date('Y', $order['date']), 'month' => date('m', $order['date'])), array('precision' => 4));
                if(empty($fxrates['price'])) {
                    $fxrates['price'] = $average_fx[$order['currency']];
                }
                /* Temporary Fix */
                if(empty($fxrates['price'])) {
                    $fxrates['price'] = $order['usdFxrate'];
                }

                $orderline_query = $db->query("SELECT imol.*, imol.foreignId AS orderline_id, imp.foreignName AS productname, imp.foreignNameAbbr AS productnameAbbr, imp.localId AS productLocalId, cost, costCurrency, ime.foreignName AS supplier, ps.titleAbbr AS productcategory
											FROM integration_mediation_salesorderlines imol
											JOIN integration_mediation_products imp ON (imp.foreignId=imol.pid)
											LEFT JOIN products p ON (imp.localId=p.pid)
											LEFT JOIN genericproducts gp ON (p.gpid=gp.gpid)
											LEFT JOIN productsegments ps ON (ps.psid=gp.psid)
											LEFT JOIN integration_mediation_entities ime ON (ime.foreignId =imol.spid)
											WHERE foreignOrderId='{$order[order_id]}'{$orderline_query_where}");
                $quantity_accuml = 0;
                while($orderline = $db->fetch_assoc($orderline_query)) {
                    /* Get Supplier if not specified - START */
                    if(empty($orderline['supplier']) && !empty($orderline['productLocalId'])) {
                        $localproduct = new Products($orderline['productLocalId']);
                        $orderline['supplier'] = $localproduct->get_supplier()->get()['companyName'];
                    }
                    /* Get Supplier if not specified - END */

                    if(!isset($average_fx[$orderline['costCurrency']])) {
                        $average_fx[$orderline['costCurrency']] = $currency_obj->get_fxrate_bytype($core->input['fxtype'], $orderline['costCurrency'], array('from' => strtotime(date('Y-m-d', $order['date']).' 01:00'), 'to' => strtotime(date('Y-m-d', $order['date']).' 24:00') - (7 * 24 * 3600), 'year' => date('Y', $order['date']), 'month' => date('m', $order['date'])), array('precision' => 4));
                    }

                    $fxrates['cost'] = $currency_obj->get_fxrate_bytype($core->input['fxtype'], $orderline['costCurrency'], array('from' => strtotime(date('Y-m-d', $order['date']).' 01:00'), 'to' => strtotime(date('Y-m-d', $order['date']).' 24:00'), 'year' => date('Y', $order['date']), 'month' => date('m', $order['date'])), array('precision' => 4));

                    if(empty($fxrates['cost'])) {
                        $fxrates['cost'] = $average_fx[$orderline['costCurrency']];
                    }

                    /* Temporary Fix */
                    if(empty($fxrates['cost'])) {
                        $fxrates['cost'] = $order['usdFxrate'];
                    }

                    /* Get Purchase Prices - START */
                    if(empty($orderline['purchasePrice'])) {
//						$purchase_query = $db->query("SELECT * FROM ".Tprefix."integration_mediation_stockpurchases WHERE pid='{$orderline[pid]}' AND date < {$order[date]}");
//						while($purchase = $db->fetch_assoc($purchase_query)) {
//							$purchase_times = 1;
//							if($purchase['quantityUnit'] == 'MT') {
//								$purchase_times = 1000;
//							}
//
//							$quantity_accuml += $purchase['quantity']*$purchase_times;
//							if(empty($purchase['usdFxrate'])) {
//								$purchase['usdFxrate'] = 1;
//							}
//							$purchase_prices[$orderline['pid']][$purchase['imspid']] = ($purchase['amount']/$purchase['usdFxrate'])/($purchase['quantity']*$purchase_times);
//							if($orderline['quantity'] < $quantity_accuml) {
//								break;
//							}
//						}
//						$orderline['purchaseprice'] = 0;
//						if(is_array($purchase_prices[$orderline['pid']])) {
//							$orderline['purchaseprice'] = array_sum($purchase_prices[$orderline['pid']])/count($purchase_prices[$orderline['pid']]);
//						}
//
//						unset($purchase_prices[$orderline['pid']]);
                    }
                    else {
                        $fxrates['purchasePrice'] = $currency_obj->get_fxrate_bytype($core->input['fxtype'], $orderline['purPriceCurrency'], array('from' => strtotime(date('Y-m-d', $order['date']).' 01:00'), 'to' => strtotime(date('Y-m-d', $order['date']).' 24:00'), 'year' => date('Y', $order['date']), 'month' => date('m', $order['date'])), array('precision' => 4));
                        if(empty($fxrates['cost'])) {
                            $fxrates['purchasePrice'] = $average_fx[$orderline['purPriceCurrency']];
                        }
                        $orderline['purchasePrice'] = $orderline['purchasePrice'] * $fxrates['purchasePrice'];
                    }
                    /* Get Purchase Prices - END */

                    $order['month'] = date('M', $order['date']);
                    $order['month_num'] = date('n', $order['date']);
                    $order['day'] = date('d', $order['date']);
                    $order['week'] = date('W', $order['date']);

                    $orderline['price'] = $orderline['price'] / $fxrates['price'];
                    $orderline['linenetamt'] = $orderline['quantity'] * $orderline['price'];
                    //$orderline['price'] = round($orderline['price']*$fxrates['price'], 2);

                    if(!empty($fxrates['cost'])) {
                        $orderline['cost'] = $orderline['cost'] / $fxrates['cost'];
                    }
                    $orderline['totalcost'] = $orderline['cost']; //*$orderline['quantity'];
                    $orderline['netmargin'] = $orderline['linenetamt'] - $orderline['totalcost'];
                    $orderline['grossmargin'] = $orderline['linenetamt'] - ($orderline['purchasePrice'] * $orderline['quantity']);
                    if($orderline['linenetamt'] != 0) {
                        $orderline['marginperc'] = round(($orderline['netmargin'] * 100) / $orderline['linenetamt'], 1);
                    }
                    else {
                        $orderline['marginperc'] = 0;
                    }

                    $sales[$order['month_num']][$order['week']]['entries'][$orderline['orderline_id']] = array_merge($order, $orderline);
                    //$sales[$order['month_num']][$order['week']]['linenetamt_total'] +=  $orderline['linenetamt'];
                    //$sales[$order['month_num']][$order['week']]['grossmargin_total'] +=  $orderline['grossmargin'];
                    $totals['linenetamt'][$order['month_num']][$order['week']] += $orderline['linenetamt'];
                    $totals['grossmargin'][$order['month_num']][$order['week']] += $orderline['grossmargin'];
                    $totals['netmargin'][$order['month_num']][$order['week']] += $orderline['netmargin'];
                    //$total_sales += $orderline['linenetamt'];

                    $total_details_items = array('customers' => array('type' => 'topcustomers', 'id' => 'bpid', 'name' => 'bpname_abv', 'var' => 'order'),
                            'suppliers' => array('type' => 'topsuppliers', 'id' => 'spid', 'name' => 'supplier', 'var' => 'orderline'),
                            'salesrep' => array('type' => 'topemployees', 'id' => 'salesrep_id', 'name' => 'salesrep', 'var' => 'order'),
                            'products' => array('type' => 'topproducts', 'id' => 'pid', 'name' => 'productnameAbbr', 'var' => 'orderline'));

                    foreach($total_details_items as $cat => $config) {
                        if($core->input['type'] == $config['type']) {
                            $cachearr[$cat][${$config['var']}[$config['id']]] = ${$config['var']}[$config['name']]; // $order['bpname_abv'];
                            $total_details[$cat]['amounts'][${$config['var']}[$config['id']]][$order['month_num']] += $orderline['linenetamt'];
                            $total_details[$cat]['numorders'][${$config['var']}[$config['id']]][$order['month_num']] ++;
                            $total_details[$cat]['grossmargin'][${$config['var']}[$config['id']]][$order['month_num']] += $orderline['grossmargin'];
                            $total_details[$cat]['netmargin'][${$config['var']}[$config['id']]][$order['month_num']] += $orderline['netmargin'];
                        }
                    }
                }
            }

            if($core->input['type'] == 'detailed' || !empty($core->input['type']) || !isset($core->input['type'])) {
                $salesreport = '<table style="width:120%; font-size: inherit;" border="0" cellpadding="0" cellspacing="0">';
                $salesreport .= '<tr>';

                $required_headers = array('dayabbr', 'busmanagerabbr', 'sector', 'product', 'supplier', 'customer', 'price', 'cost', 'totalcost', 'ptdays', 'quantityabbr', 'sellingprice', 'amountabbr', 'gmargin', 'nmargin', '%');

                foreach($required_headers as $value) {
                    if(isset($lang->{$value})) {
                        $value = $lang->{$value};
                    }
                    $salesreport .= '<th style="padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#92D050;">'.$value.'</th>';
                }
                $salesreport .= '</tr>';

                foreach($sales as $month => $weeks) {
                    $required_data = array('day', 'salesrep', 'productcategory', 'productnameAbbr', 'supplier', 'bpname', 'purchasePrice', 'cost', 'totalcost', 'paymenttermsdays', 'quantity', 'price', 'linenetamt', 'grossmargin', 'netmargin', 'marginperc');
                    $required_data_count = count($required_data);
                    $rightcols_count = 4;
                    $toround = array('orderline', 'grossmargin', 'netmargin', 'linenetamt', 'totalcost', 'price', 'cost', 'purchasePrice');

                    $salesreport .= '<tr><td style="text-align: left; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD;" colspan="'.$required_data_count.'"><strong>'.date('F', mktime(0, 0, 0, $month, 1, 0)).' '.$current_date['year'].'</strong></td></tr>';
                    foreach($weeks as $week => $sections) {
                        $salesreport .= '<tr><td style="text-align: left; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F2FAED;" colspan="'.$required_data_count.'"><strong>Week '.$week.'</strong> <em>(From '.date($core->settings['dateformat'], strtotime($current_date['year'].'W'.$week)).' to '.date($core->settings['dateformat'], strtotime($current_date['year'].'W'.$week.' +1 week')).')</em></td></tr>';
                        foreach($sections['entries'] as $order => $order_details) {
                            $salesreport .= '<tr>';
                            foreach($required_data as $key) {
                                $salesreport_cell_align = 'left';
                                $value = $order_details[$key];
                                if(in_array($key, $toround)) {
                                    $value = number_format($value, 2, '.', ' ');
                                    $salesreport_cell_align = 'right';
                                }
                                if($key == 'bpname') {
                                    if(strlen($value) > 15) {
                                        $value = $order_details['bpname_abv'];
                                    }
                                }

                                $salesreport .= '<td style="text-align: '.$sales_table_cell_align.'; padding: 5px; border-bottom: 1px dashed #CCCCCC; border-right: 1px solid #E1E1E1;">'.$value.'</td>';
                            }
                            $salesreport .= '</tr>';
                        }
                        //$totals['month'][$month] += $sales[$month][$week]['linenetamt_total'];
                        $salesreport .= '<tr><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F2FAED;" colspan="'.($required_data_count - $rightcols_count).'">'.$lang->weektotal.'</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F2FAED; font-weight:bold;">'.number_format($totals['linenetamt'][$month][$week], 0, '.', ' ').'</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F2FAED; font-weight:bold;">'.number_format($totals['grossmargin'][$month][$week], 0, '.', ' ').'</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F2FAED; font-weight:bold;">'.number_format($totals['netmargin'][$month][$week], 0, '.', ' ').'</td><td colspan="2" style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F2FAED;">&nbsp;</td></tr>';
                    }

                    $salesreport .= '<tr><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD;" colspan="'.($required_data_count - $rightcols_count).'">'.$lang->monthtotal.'</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD; font-weight:bold;">'.number_format(array_sum_recursive($totals['linenetamt'][$month]), 0, '.', ' ').'</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD; font-weight:bold;">'.number_format(array_sum_recursive($totals['grossmargin'][$month]), 0, '.', ' ').'</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD; font-weight:bold;">'.number_format(array_sum_recursive($totals['netmargin'][$month]), 0, '.', ' ').'</td><td colspan="2" style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD;">&nbsp;</td></tr>';
                }
                $salesreport .= '<tr><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC;" colspan="'.($required_data_count - $rightcols_count).'">'.$lang->uptototal.'</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC;"><strong>'.number_format(array_sum_recursive($totals['linenetamt']), 0, '.', ' ').'</strong></td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC;"><strong>'.number_format(array_sum_recursive($totals['grossmargin']), 0, '.', ' ').'</strong></td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC;"><strong>'.number_format(array_sum_recursive($totals['netmargin']), 0, '.', ' ').'</strong></td><td colspan="2" style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC;">&nbsp;</td></tr></tr>';
                $salesreport .= '</table>';
            }
            if($core->input['type'] == 'topemployees' || $core->input['type'] == 'topcustomers' || $core->input['type'] == 'topproducts' || $core->input['type'] == 'topsuppliers') {
                $salesreport = '<h1>'.$lang->{$core->sanitize_inputs($core->input['type'])}.'</h1>';

                $bm_details_headers = array('amounts', 'numorders', 'grossmargin');
                $details_headers = array('salesrep' => array('amounts', 'numorders', 'grossmargin', 'netmargin'), 'customers' => array('amounts', 'numorders', 'grossmargin', 'netmargin'));
                //$sections_titles = array('salesrep' => 'Sales Representatives', 'customers' => 'Top 10 Customers');

                $classifications_rowlimits = array('customers' => 100);
                /* foreach($bm_details as $type => $salesreps) {
                  arsort($salesreps);
                  $bm_sales_table .= '<tr><td style="margin-top: 10px; font-weight: bold; text-align: left; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD;">BM</td><td style="margin-top: 10px; font-weight: bold; text-align: left; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD;">'.$bm_details_headers[$type].'</td></tr>';
                  foreach($salesreps as $id => $amount) {
                  $bm_sales_table .= '<tr><td style="text-align: left; padding: 5px; border-bottom: 1px dashed #CCCCCC;">'.$cachearr['salesrep'][$id].'</td><td style="text-align: left; padding: 5px; border-bottom: 1px dashed #CCCCCC;">'.number_format($amount, 2, '.', ' ').'</td></tr>';
                  }
                  }
                  $bm_sales_table .= '</table>';
                 */

                /* Parse year overview - START */
                for($i = 1; $i <= 12; $i++) {
                    $month_names .= '<th style="width:6%; padding:5px;">'.date('M', mktime(0, 0, 0, $i, 1, 0)).'</th>';
                }

                $totals = array();
                foreach($total_details as $etype => $entity_details) {
                    foreach($entity_details as $type => $entity) {
                        foreach($entity as $id => $months_amounts) {
                            $totals[$etype][$type][$id] = array_sum_recursive($months_amounts);
                        }
                    }
                }

                foreach($total_details as $etype => $entity_details) {
                    //$salesreport .= '<h3 style="text-decoration:underline;">'.$sections_titles[$etype].'</h1>';
                    foreach($entity_details as $type => $entity) {
                        $salesreport .= '<h4>'.$lang->{$type}.'</h4>';

                        $salesreport .= '<table border="0" width="100%" style="font-size:inherit; border: 0px; width: 100%; border-spacing: 0px; border-collapse:collapse; padding:0px;">';
                        $salesreport .= '<tr style="background-color:#92D050; font-weight: bold; border-bottom: dashed 1px #666666; padding: 4px;">';
                        $salesreport .= '<td style="width:10%;">'.$lang->name.'</td>'.$month_names.'<th width="6%">'.$lang->total.'</th></tr>';
                        $month_totals = array();

                        arsort($totals[$etype][$type]);
                        $counter = 0;
                        foreach($totals[$etype][$type] as $id => $entity_total) {
                            if(isset($classifications_rowlimits[$etype]) && !empty($classifications_rowlimits[$etype])) {
                                if($counter == $classifications_rowlimits[$etype]) {
                                    break;
                                }
                            }

                            $months_amounts = $entity[$id];

                            $salesreport .= '<tr><td style="text-align: left; padding: 5px; border-bottom: 1px dashed #CCCCCC;">'.$cachearr[$etype][$id].'</td>';

                            for($month = 1; $month <= 12; $month++) {
                                $salesreport .= '<td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC;">'.number_format($months_amounts[$month], 0, '.', ' ').'</td>';
                                $month_totals[$month] += $months_amounts[$month];
                            }

                            $salesreport .= '<td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; font-weight: bold;">'.number_format($entity_total, 0, '.', ' ').'</td>';
                            $salesreport .= '</tr>';
                            $counter++;
                        }

                        $salesreport .= '<tr><td>&nbsp;</td>';
                        for($month = 1; $month <= 12; $month++) {
                            $salesreport .= '<td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; font-weight: bold;">'.number_format($month_totals[$month], 0, '.', ' ').'</td>';
                        }
                        $salesreport .= '<td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; font-weight: bold; text-decoration:underline;">'.number_format(array_sum($month_totals), 0, '.', ' ').'</td></tr>';
                        $salesreport .= '</table>';
                    }
                    $salesreport .= '<hr />';
                }
                /* Parse year overview - END */
            }
        }

        $session->set_phpsession(array('sreportcontent_'.$identifier => base64_encode($salesreport)));

        $salesreport .= '<a href="index.php?module=crm/salesreport&action=sendreport&amp;identifier='.$identifier.'" target="_self">Send</a>';
        eval("\$previewpage = \"".$template->get('crm_previewsalesreport')."\";");
        output_page($previewpage);
    }
    elseif($core->input['action'] == 'sendreport') {
        $identifier = $db->escape_string($core->input['identifier']);
        $report_content = base64_decode($session->get_phpsession('sreportcontent_'.$identifier));
        $session->destroy_phpsession(true);

        /* Send Sales Table */
        $message = '<html><head></head><body style="font-size:12px; font-family: Tahoma; color: #333333;">'.$report_content.'</body>';

        $email_data = array(
                'to' => 'chris.sacy@orkila.com',
                //'cc'			=> 'jalel.elghoul@orkila.tn',
                'from_email' => $core->settings['maileremail'],
                'from' => 'OCOS Mailer',
                'subject' => 'Week '.date('W', TIME_NOW).' '.$current_date['year'].' sales report',
                'message' => '<h1>Sales '.$current_date['month'].' '.$current_date['year'].' in Orkila Tunisie</h1><br />'.$message
        );

        $mail = new Mailer($email_data, 'php');
        if($mail->get_status() === true) {
            //$log->record('hrbirthdaynotification', array('to' => $recepient_details['email']), 'emailsent');
        }
        else {
            echo 'error';
            //$log->record('hrbirthdaynotification',array('to' => $recepient_details['email']), 'emailnotsent');
        }

//		/* Send Classifications */
//		$message = '<html><head></head><body style="font-size:12px; font-family: Tahoma; color: #333333;">'.$yearoverview.'</body>';
//		$email_data = array(
//			'to'	      => 'christophe.sacy@orkila.com',
//			'from_email'  => $core->settings['adminemail'],
//			'from'	      => 'OCOS Mailer',
//			'subject'     => 'Week '.date('W', TIME_NOW).' '.$current_date['year'].' sales classifications',
//			'message'     => '<h2>Sales Year '.$current_date['year'].' Overview in Orkila Tunisie</h2>'.$message
//		);
//
//		$mail = new Mailer($email_data, 'php');
//		if($mail->get_status() === true) {
//			//$log->record('hrbirthdaynotification', array('to' => $recepient_details['email']), 'emailsent');
//		}
//		else
//		{
//			echo 'error';
//			//$log->record('hrbirthdaynotification',array('to' => $recepient_details['email']), 'emailnotsent');
//		}
    }
}
?>