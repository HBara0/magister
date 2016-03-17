<?php
require '../inc/init.php';

$current_date = getdate(TIME_NOW);
$period['from'] = strtotime($current_date['year'].'-1-1'); //'30 minutes ago';
$period['to'] = strtotime('last day of this month');
;

$currency_obj = new Currencies('USD');

$affids = array(19);

$query = $db->query("SELECT imso.foreignId AS order_id, date, docNum, foreignName AS bpname, cid AS bpid, ime.foreignNameAbbr AS bpname_abv, currency, salesRepLocalId AS salesrep_id, salesRep AS salesrep, imso.paymentTerms AS paymenttermsdays
					FROM integration_mediation_salesorders imso
					JOIN integration_mediation_entities ime ON (ime.foreignId=imso.cid)
					WHERE imso.affid IN (".implode(',', $affids).") AND (date BETWEEN ".$period['from']." AND ".$period['to'].")
					ORDER by date DESC");
if($db->num_rows($query) > 0) {
    $sales_table = '<table style="width:100%; font-size: inherit;" border="0" cellpadding="0" cellspacing="0">';
    $sales_table .= '<tr>';

    $required_headers = array('D', 'BM', 'Sector', 'Product', 'Supplier', 'Customer', 'Price', 'Cost', 'Total Cost', 'PT Days', 'Qty', 'Selling Price', 'Amt', 'G. Margin', 'N. Margin', '%');

    foreach($required_headers as $value) {
        $sales_table .= '<th style="padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#92D050;">'.$value.'</th>';
    }
    $sales_table .= '</tr>';

    while($order = $db->fetch_assoc($query)) {
        if(!isset($average_fx[$order['currency']])) {
            $average_fx[$order['currency']] = $currency_obj->get_average_fxrate($order['currency'], $period);
        }
        $fxrate = $currency_obj->get_average_fxrate($order['currency'], array('from' => strtotime(date('Y-m-d', $order['date']).' 01:00'), 'to' => strtotime(date('Y-m-d', $order['date']).' 24:00')));
        if(empty($fxrate)) {
            $fxrate = $average_fx[$order['currency']];
        }

        $orderline_query = $db->query("SELECT imol.*, imol.foreignId AS orderline_id, imp.foreignName AS productname, cost, ime.foreignName AS supplier, ps.titleAbbr AS productcategory
									FROM integration_mediation_salesorderlines imol
									JOIN integration_mediation_products imp ON (imp.foreignId=imol.pid)
									LEFT JOIN products p ON (imp.localId=p.pid)
									LEFT JOIN genericproducts gp ON (p.gpid=gp.gpid)
									LEFT JOIN productsegments ps ON (ps.psid=gp.psid)
									LEFT JOIN integration_mediation_entities ime ON (ime.foreignId =imol.spid)
									WHERE foreignOrderId='{$order[order_id]}'");
        $quantity_accuml = 0;
        while($orderline = $db->fetch_assoc($orderline_query)) {
            /* Get Purchase Prices - START */
            $purchase_query = $db->query("SELECT * FROM ".Tprefix."integration_mediation_stockpurchases WHERE pid='{$orderline[pid]}' AND date < {$order[date]}");
            while($purchase = $db->fetch_assoc($purchase_query)) {
                $purchase_times = 1;
                if($purchase['quantityUnit'] == 'MT') {
                    $purchase_times = 1000;
                }

                $quantity_accuml += $purchase['quantity'] / $purchase_times;
                $purchase_prices[$orderline['pid']][$purchase['imspid']] = ($purchase['amount'] * $purchase['usdFxrate']) / ($purchase['quantity'] * $purchase_times);
                if($orderline['quantity'] < $quantity_accuml) {
                    break;
                }
            }
            $orderline['purchaseprice'] = 0;
            if(is_array($purchase_prices[$orderline['pid']])) {
                $orderline['purchaseprice'] = array_sum($purchase_prices[$orderline['pid']]) / count($purchase_prices[$orderline['pid']]);
            }

            unset($purchase_prices[$orderline['pid']]);

            /* Get Purchase Prices - END */
            $order['month'] = date('M', $order['date']);
            $order['month_num'] = date('n', $order['date']);
            $order['day'] = date('d', $order['date']);
            $order['week'] = date('W', $order['date']);

            $orderline['linenetamt'] = ($orderline['quantity'] * $orderline['price']) * $fxrate;
            $orderline['price'] = round($orderline['price'] * $fxrate, 2);

            $orderline['totalcost'] = $orderline['cost'] * $orderline['quantity'];
            $orderline['netmargin'] = $orderline['linenetamt'] - $orderline['totalcost'];
            $orderline['grossmargin'] = $orderline['linenetamt'] - ($orderline['purchaseprice'] * $orderline['quantity']);
            $orderline['marginperc'] = round(($orderline['netmargin'] * 100) / $orderline['linenetamt'], 1);

            $sales[$order['month_num']][$order['week']]['entries'][$orderline['orderline_id']] = array_merge($order, $orderline);
            //$sales[$order['month_num']][$order['week']]['linenetamt_total'] +=  $orderline['linenetamt'];
            //$sales[$order['month_num']][$order['week']]['grossmargin_total'] +=  $orderline['grossmargin'];
            $totals['linenetamt'][$order['month_num']][$order['week']] += $orderline['linenetamt'];
            $totals['grossmargin'][$order['month_num']][$order['week']] += $orderline['grossmargin'];
            $totals['netmargin'][$order['month_num']][$order['week']] += $orderline['netmargin'];
            //$total_sales += $orderline['linenetamt'];

            /* Set BM Data - START */
            $cache['salesrep'][$order['salesrep_id']] = $order['salesrep'];
            $total_details['salesrep']['amounts'][$order['salesrep_id']][$order['month_num']] += $orderline['linenetamt'];
            $total_details['salesrep']['numorders'][$order['salesrep_id']][$order['month_num']] ++;
            $total_details['salesrep']['grossmargin'][$order['salesrep_id']][$order['month_num']] += $orderline['grossmargin'];
            $total_details['salesrep']['netmargin'][$order['salesrep_id']][$order['month_num']] += $orderline['netmargin'];
            //		$bm_details['grossmargin'][$order['salesrep_id']][$order['month_num']] += $orderline['grossmargin'];

            /* Set BM Data - END */

            /* Set BM Data - START */
            $cache['customers'][$order['bpid']] = $order['bpname_abv'];
            $total_details['customers']['amounts'][$order['bpid']][$order['month_num']] += $orderline['linenetamt'];
            $total_details['customers']['numorders'][$order['bpid']][$order['month_num']] ++;
            $total_details['customers']['grossmargin'][$order['bpid']][$order['month_num']] += $orderline['grossmargin'];
            $total_details['customers']['netmargin'][$order['bpid']][$order['month_num']] += $orderline['netmargin'];
            /* Set BM Data - END */
        }
    }

    foreach($sales as $month => $weeks) {
        $required_data = array('day', 'salesrep', 'productcategory', 'productname', 'supplier', 'bpname', 'purchaseprice', 'cost', 'totalcost', 'paymenttermsdays', 'quantity', 'price', 'linenetamt', 'grossmargin', 'netmargin', 'marginperc');
        $required_data_count = count($required_data);
        $rightcols_count = 4;
        $toround = array('orderline', 'grossmargin', 'netmargin', 'linenetamt', 'totalcost');

        $sales_table .= '<tr><td style="text-align: left; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD;" colspan="'.$required_data_count.'"><strong>'.date('F', mktime(0, 0, 0, $month, 1, 0)).' '.$current_date['year'].'</strong></td></tr>';
        foreach($weeks as $week => $sections) {
            $sales_table .= '<tr><td style="text-align: left; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F2FAED;" colspan="'.$required_data_count.'"><strong>Week '.$week.'</strong> <em>(From '.date($core->settings['dateformat'], strtotime($current_date['year'].'W'.$week)).' to '.date($core->settings['dateformat'], strtotime($current_date['year'].'W'.$week.' +1 week')).')</em></td></tr>';
            foreach($sections['entries'] as $order => $order_details) {
                $sales_table .= '<tr>';
                foreach($required_data as $key) {
                    $sales_table_cell_align = 'left';
                    $value = $order_details[$key];
                    if(in_array($key, $toround)) {
                        $value = number_format($value, 0, '.', ' ');
                        $sales_table_cell_align = 'right';
                    }
                    if($key == 'bpname') {
                        if(strlen($value) > 15) {
                            $value = $order_details['bpname_abv'];
                        }
                    }

                    $sales_table .= '<td style="text-align: '.$sales_table_cell_align.'; padding: 5px; border-bottom: 1px dashed #CCCCCC; border-right: 1px solid #E1E1E1;">'.$value.'</td>';
                }
                $sales_table .= '</tr>';
            }
            //$totals['month'][$month] += $sales[$month][$week]['linenetamt_total'];
            $sales_table .= '<tr><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F2FAED;" colspan="'.($required_data_count - $rightcols_count).'">Week Total</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F2FAED; font-weight:bold;">'.number_format($totals['linenetamt'][$month][$week], 0, '.', ' ').'</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F2FAED; font-weight:bold;">'.number_format($totals['grossmargin'][$month][$week], 0, '.', ' ').'</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F2FAED; font-weight:bold;">'.number_format($totals['netmargin'][$month][$week], 0, '.', ' ').'</td><td colspan="2" style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F2FAED;">&nbsp;</td></tr>';
        }

        $sales_table .= '<tr><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD;" colspan="'.($required_data_count - $rightcols_count).'">Month Total</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD; font-weight:bold;">'.number_format(array_sum_recursive($totals['linenetamt'][$month]), 0, '.', ' ').'</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD; font-weight:bold;">'.number_format(array_sum_recursive($totals['grossmargin'][$month]), 0, '.', ' ').'</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD; font-weight:bold;">'.number_format(array_sum_recursive($totals['netmargin'][$month]), 0, '.', ' ').'</td><td colspan="2" style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD;">&nbsp;</td></tr>';
    }
    $sales_table .= '<tr><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC;" colspan="'.($required_data_count - $rightcols_count).'">Up to Total</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC;"><strong>'.number_format(array_sum_recursive($totals['linenetamt']), 0, '.', ' ').'</strong></td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC;"><strong>'.number_format(array_sum_recursive($totals['grossmargin']), 0, '.', ' ').'</strong></td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC;"><strong>'.number_format(array_sum_recursive($totals['netmargin']), 0, '.', ' ').'</strong></td><td colspan="2" style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC;">&nbsp;</td></tr></tr>';
    $sales_table .= '</table>';
    echo $sales_table;
    exit;
    $yearoverview = '<h1>Classifications</h1>';

    $bm_details_headers = array('amounts' => 'Amounts', 'numorders' => '# Orders', 'grossmargin' => 'Gross Margin');
    $details_headers = array('salesrep' => array('amounts' => 'Amounts USD', 'numorders' => '# Orders', 'grossmargin' => 'Gross Margin', 'netmargin' => 'Net Margin'), 'customers' => array('amounts' => 'Amounts USD', 'numorders' => '# Orders', 'grossmargin' => 'Gross Margin', 'netmargin' => 'Net Margin'));
    $sections_titles = array('salesrep' => 'Sales Representatives', 'customers' => 'Top 10 Customers');

    $classifications_rowlimits = array('customers' => 10);
    /* foreach($bm_details as $type => $salesreps) {
      arsort($salesreps);
      $bm_sales_table .= '<tr><td style="margin-top: 10px; font-weight: bold; text-align: left; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD;">BM</td><td style="margin-top: 10px; font-weight: bold; text-align: left; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD;">'.$bm_details_headers[$type].'</td></tr>';
      foreach($salesreps as $id => $amount) {
      $bm_sales_table .= '<tr><td style="text-align: left; padding: 5px; border-bottom: 1px dashed #CCCCCC;">'.$cache['salesrep'][$id].'</td><td style="text-align: left; padding: 5px; border-bottom: 1px dashed #CCCCCC;">'.number_format($amount, 2, '.', ' ').'</td></tr>';
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
        $yearoverview .= '<h3 style="text-decoration:underline;">'.$sections_titles[$etype].'</h1>';
        foreach($entity_details as $type => $entity) {
            $yearoverview .= '<h4>'.$details_headers[$etype][$type].'</h4>';

            $yearoverview .= '<table border="0" width="100%" style="font-size:inherit; border: 0px; width: 100%; border-spacing: 0px; border-collapse:collapse; padding:0px;">';
            $yearoverview .= '<tr style="background-color:#92D050; font-weight: bold; border-bottom: dashed 1px #666666; padding: 4px;">';
            $yearoverview .= '<td style="width:10%;">Name</td>'.$month_names.'<th width="6%">Total</th></tr>';
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

                $yearoverview .= '<tr><td style="text-align: left; padding: 5px; border-bottom: 1px dashed #CCCCCC;">'.$cache[$etype][$id].'</td>';

                for($month = 1; $month <= 12; $month++) {
                    $yearoverview .= '<td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC;">'.number_format($months_amounts[$month], 0, '.', ' ').'</td>';
                    $month_totals[$month] += $months_amounts[$month];
                }

                $yearoverview .= '<td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; font-weight: bold;">'.number_format($entity_total, 0, '.', ' ').'</td>';
                $yearoverview .= '</tr>';
                $counter++;
            }

            $yearoverview .= '<tr><td>&nbsp;</td>';
            for($month = 1; $month <= 12; $month++) {
                $yearoverview .= '<td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; font-weight: bold;">'.number_format($month_totals[$month], 0, '.', ' ').'</td>';
            }
            $yearoverview .= '<td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; font-weight: bold; text-decoration:underline;">'.number_format(array_sum($month_totals), 0, '.', ' ').'</td></tr>';
            $yearoverview .= '</table>';
        }
        $yearoverview .= '<hr />';
    }
    /* Parse year overview - END */

    /* Send Sales Table */
    $message = '<html><head></head><body style="font-size:12px; font-family: Tahoma; color: #333333;">'.$sales_table.'</body>';

    $email_data = array(
            'to' => 'christophe.sacy@orkila.com',
            'cc' => 'jalel.elghoul@orkila.tn',
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

    /* Send Classifications */
    $message = '<html><head></head><body style="font-size:12px; font-family: Tahoma; color: #333333;">'.$yearoverview.'</body>';
    $email_data = array(
            'to' => 'christophe.sacy@orkila.com',
            'from_email' => $core->settings['maileremail'],
            'from' => 'OCOS Mailer',
            'subject' => 'Week '.date('W', TIME_NOW).' '.$current_date['year'].' sales classifications',
            'message' => '<h2>Sales Year '.$current_date['year'].' Overview in Orkila Tunisie</h2>'.$message
    );

    $mail = new Mailer($email_data, 'php');
    if($mail->get_status() === true) {
        //$log->record('hrbirthdaynotification', array('to' => $recepient_details['email']), 'emailsent');
    }
    else {
        echo 'error';
        //$log->record('hrbirthdaynotification',array('to' => $recepient_details['email']), 'emailnotsent');
    }
}
?>