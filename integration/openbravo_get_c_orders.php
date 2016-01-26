<?php
exit;
require '../inc/init.php';
$current_date = getdate(TIME_NOW);

$connection = pg_connect("host=localhost port=5432 dbname=openbrav_main user=openbrav_appuser password=8w8;MFRy4g^3");

$query = pg_query("SELECT o.c_order_id, o.dateordered, bp.name AS bpname, bp.c_bpartner_id AS bpid, bp.value AS bpname_abv, c.iso_code AS currency, o.salesrep_id, u.name AS salesrep, pt.netdays AS paymenttermsdays
					FROM c_order o JOIN c_bpartner bp ON (bp.c_bpartner_id=o.c_bpartner_id)
					JOIN c_currency c ON (c.c_currency_id=o.c_currency_id)
					JOIN ad_user u ON (u.ad_user_id=o.salesrep_id)
					JOIN c_paymentterm pt ON (o.c_paymentterm_id=pt.c_paymentterm_id)
					WHERE o.ad_org_id='C08F137534222BD001345BAA60661B97' AND docstatus NOT IN ('VO', 'CL') AND issotrx='Y' AND (dateordered BETWEEN '".date('Y-m-d 00:00:00', strtotime($current_date['year'].'-1-1'))."' AND '".date('Y-m-d 00:00:00', strtotime('last day of this month'))."')
					ORDER by dateordered ASC");

$exclude['products'] = array('0A36650996654AD2BA6B26CBC8BA7347');

$sales_table = '<table style="width:100%; font-size: inherit;" border="0" cellpadding="0" cellspacing="0">';
$sales_table .= '<tr>';

$required_headers = array('D', 'BM', 'Sector', 'Product', 'Supplier', 'Customer', 'Cost', 'Total Cost', 'PT Days', 'Qty', 'Selling Price', 'Amt', 'G. Margin', '%');

foreach($required_headers as $value) {
    $sales_table .= '<th style="padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#92D050;">'.$value.'</th>';
}
$sales_table .= '</tr>';

while($order = pg_fetch_assoc($query)) {
    $orderline_query = pg_query("SELECT ol.*, p.name AS productname, pc.value AS productcategory, ct.cost, bp.name AS supplier
								FROM c_orderline ol JOIN m_product p ON (p.m_product_id=ol.m_product_id)
								JOIN m_product_category pc ON (p.m_product_category_id=pc.m_product_category_id)
								LEFT JOIN m_costing ct ON (ct.m_product_id=p.m_product_id)
								LEFT JOIN m_product_po ppo ON (p.m_product_id=ppo.m_product_id)
								LEFT JOIN c_bpartner bp ON (bp.c_bpartner_id=ppo.c_bpartner_id)
								WHERE c_order_id='{$order[c_order_id]}' AND ('".date('Y-m-d H:i:s', time())."' BETWEEN ct.datefrom AND ct.dateto) AND ol.m_product_id NOT IN ('".implode('\',\'', $exclude['products'])."')");
    while($orderline = pg_fetch_assoc($orderline_query)) {
        $fxrate = 0.5; //0.649;

        $order_date = explode(' ', $order['dateordered']);
        $order_date_timestamp = strtotime($order_date[0]);

        $order['month'] = date('M', $order_date_timestamp);
        $order['month_num'] = date('n', $order_date_timestamp);
        $order['day'] = date('d', $order_date_timestamp);
        $order['week'] = date('W', $order_date_timestamp);

        $orderline['linenetamt'] = ($orderline['linenetamt'] * $fxrate);
        $orderline['priceactual'] = round($orderline['priceactual'] * $fxrate, 2);

        $orderline['totalcost'] = $orderline['cost'] * $orderline['qtyordered'];
        $orderline['grossmargin'] = $orderline['linenetamt'] - $orderline['totalcost'];
        $orderline['marginperc'] = round(($orderline['grossmargin'] * 100) / $orderline['linenetamt'], 1);
        //$orderline['cost'] = 0;

        $sales[$order['month_num']][$order['week']]['entries'][$orderline['c_orderline_id']] = array_merge($order, $orderline);
        //$sales[$order['month_num']][$order['week']]['linenetamt_total'] +=  $orderline['linenetamt'];
        //$sales[$order['month_num']][$order['week']]['grossmargin_total'] +=  $orderline['grossmargin'];
        $totals['linenetamt'][$order['month_num']][$order['week']] += $orderline['linenetamt'];
        $totals['grossmargin'][$order['month_num']][$order['week']] += $orderline['grossmargin'];
        //$total_sales += $orderline['linenetamt'];

        /* Set BM Data - START */
        $cache['salesrep'][$order['salesrep_id']] = $order['salesrep'];
        $total_details['salesrep']['amounts'][$order['salesrep_id']][$order['month_num']] += $orderline['linenetamt'];
        $total_details['salesrep']['numorders'][$order['salesrep_id']][$order['month_num']] ++;
        $total_details['salesrep']['grossmargin'][$order['salesrep_id']][$order['month_num']] += $orderline['grossmargin'];
        //		$bm_details['grossmargin'][$order['salesrep_id']][$order['month_num']] += $orderline['grossmargin'];

        /* Set BM Data - END */

        /* Set BM Data - START */
        $cache['customers'][$order['bpid']] = $order['bpname'];
        $total_details['customers']['amounts'][$order['bpid']][$order['month_num']] += $orderline['linenetamt'];
        $total_details['customers']['numorders'][$order['bpid']][$order['month_num']] ++;
        $total_details['customers']['grossmargin'][$order['bpid']][$order['month_num']] += $orderline['grossmargin'];
        /* Set BM Data - END */
    }
}
foreach($sales as $month => $weeks) {
    $sales_table .= '<tr><td style="text-align: left; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD;" colspan="14"><strong>'.date('F', mktime(0, 0, 0, $month, 1, 0)).' '.$current_date['year'].'</strong></td></tr>';
    foreach($weeks as $week => $sections) {
        $required_data = array('day', 'salesrep', 'productcategory', 'productname', 'supplier', 'bpname', 'cost', 'totalcost', 'paymenttermsdays', 'qtyordered', 'priceactual', 'linenetamt', 'grossmargin', 'marginperc');
        $toround = array('orderline', 'grossmargin', 'linenetamt', 'totalcost');
        $sales_table .= '<tr><td style="text-align: left; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F2FAED;" colspan="14"><strong>Week '.$week.'</strong> <em>(From '.date($core->settings['dateformat'], strtotime($current_date['year'].'W'.$week)).' to '.date($core->settings['dateformat'], strtotime($current_date['year'].'W'.$week.' +1 week')).')</em></td></tr>';
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

                $sales_table .= '<td style="text-align: '.$sales_table_cell_align.'; padding: 5px; border-bottom: 1px dashed #CCCCCC;">'.$value.'</td>';
            }
            $sales_table .= '</tr>';
        }
        //$totals['month'][$month] += $sales[$month][$week]['linenetamt_total'];
        $sales_table .= '<tr><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F2FAED;" colspan="11">Week Total</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F2FAED; font-weight:bold;">'.number_format($totals['linenetamt'][$month][$week], 0, '.', ' ').'</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F2FAED; font-weight:bold;">'.number_format($totals['grossmargin'][$month][$week], 0, '.', ' ').'</td><td colspan="2" style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F2FAED;">&nbsp;</td></tr>';
    }

    $sales_table .= '<tr><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD;" colspan="11">Month Total</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD; font-weight:bold;">'.number_format(array_sum_recursive($totals['linenetamt'][$month]), 0, '.', ' ').'</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD; font-weight:bold;">'.number_format(array_sum_recursive($totals['grossmargin'][$month]), 0, '.', ' ').'</td><td colspan="2" style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC; background-color:#F7FAFD;">&nbsp;</td></tr>';
}
$sales_table .= '<tr><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC;" colspan="11">Up to Total</td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC;"><strong>'.number_format(array_sum_recursive($totals['linenetamt']), 0, '.', ' ').'</strong></td><td style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC;"><strong>'.number_format(array_sum_recursive($totals['grossmargin']), 0, '.', ' ').'</strong></td><td colspan="2" style="text-align: right; padding: 5px; border-bottom: 1px dashed #CCCCCC;">&nbsp;</td></tr></tr>';
$sales_table .= '</table>';

$yearoverview = '<h1>Classifications</h1>';

$bm_details_headers = array('amounts' => 'Amounts', 'numorders' => '# Orders', 'grossmargin' => 'Gross Margin');
$details_headers = array('salesrep' => array('amounts' => 'Amounts USD', 'numorders' => '# Orders', 'grossmargin' => 'Gross Margin'), 'customers' => array('amounts' => 'Amounts USD', 'numorders' => '# Orders', 'grossmargin' => 'Gross Margin'));
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
        'from_email' => $core->settings['adminemail'],
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
        'from_email' => $core->settings['adminemail'],
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
?>