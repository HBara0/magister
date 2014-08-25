<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: salesreport_direct.php
 * Created:        @zaher.reda    Jun 27, 2014 | 11:59:43 AM
 * Last Update:    @zaher.reda    Jun 27, 2014 | 11:59:43 AM
 */


if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['crm_canGenerateSalesReports'] == 0) {
    error($lang->sectionnopermission);
}

$lang->load('crm_salesreport');
if(!$core->input['action']) {
    $affiliates = Affiliates::get_affiliates(array('affid' => $core->user['affiliates']), array('returnarray' => true));
    $affiliates_list = parse_selectlist('affids[]', 2, $affiliates, '');

    $fxtypes_selectlist = parse_selectlist('fxtype', 9, array('lastm' => $lang->lastmonthrate, 'ylast' => $lang->yearlatestrate, 'yavg' => $lang->yearaveragerate, 'mavg' => $lang->monthaveragerate, 'real' => $lang->realrate), '', 0);
    eval("\$generatepage = \"".$template->get('crm_generatesalesreport_live')."\";");
    output_page($generatepage);
}
else {
    if($core->input['action'] == 'do_generatereport') {
        require_once ROOT.INC_ROOT.'integration_config.php';
        if(empty($core->input['affids'])) {
            redirect('index.php?module=crm/salesreportlive');
        }

        if(is_empty($core->input['fromDate'])) {
            redirect('index.php?module=crm/salesreportlive');
        }

        $current_date = getdate(TIME_NOW);
        $period['from'] = strtotime($core->input['fromDate']);
        $period['to'] = TIME_NOW;
        if(!empty($core->input['toDate'])) {
            $period['to'] = strtotime($core->input['toDate']);
        }

        if(is_array($core->input['affids'])) {
            foreach($core->input['affids'] as $affid) {
                $affiliate = new Affiliates($affid);
                $orgs[] = $affiliate->integrationOBOrgId;
            }
        }
        else {
            $affiliate = new Affiliates($core->input['affids']);
            $orgs[] = $affiliate->integrationOBOrgId;
        }
        $currency_obj = new Currencies('USD');

        if(!empty($core->input['spid'])) {
            $orderline_query_where = ' AND ime.localId IN ('.implode(',', $core->input['spid']).')';
        }

        if(!empty($core->input['pid'])) {
            $orderline_query_where .= ' AND imp.localId IN ('.implode(',', $core->input['pid']).')';
        }

        if(!empty($core->input['cid'])) {
            $query_where .= ' AND ime.localId IN ('.implode(',', $core->input['cid']).')';
        }

        $filters = "c_invoice.ad_org_id IN ('".implode("','", $orgs)."') AND docstatus NOT IN ('VO', 'CL') AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', $period['from'])."' AND '".date('Y-m-d 00:00:00', $period['to'])."')";
        $integration = new IntegrationOB($intgconfig['openbravo']['database'], $intgconfig['openbravo']['entmodel']['client']);
        $invoices = $integration->get_saleinvoices($filters);
        $cols = array('month', 'week', 'documentno', 'salesrep', 'customername', 'suppliername', 'productname', 'segment', 'uom', 'qtyinvoiced', 'priceactual', 'linenetamt', 'purchaseprice', 'unitcostlocal', 'costlocal', 'costusd', 'grossmargin', 'grossmarginusd', 'netmargin', 'netmarginusd', 'marginperc');
        if(is_array($invoices)) {
            foreach($invoices as $invoice) {
                $invoice->customername = $invoice->get_customer()->name;
                $invoicelines = $invoice->get_invoicelines();
                $invoice->salesrep = $invoice->get_salesrep()->name;
                if(empty($invoice->salesrep)) {
                    $invoice->salesrep = 'Unknown Sales Rep';
                }

                $invoice->dateinvoiceduts = strtotime($invoice->dateinvoiced);
                $invoice->week = 'Week '.date('W-Y', $invoice->dateinvoiceduts);
                $invoice->month = date('M, Y', $invoice->dateinvoiceduts);
                $invoice->currency = $invoice->get_currency()->iso_code;
                $invoice->usdfxrate = $core->input['fxrate'];
                if(empty($core->input['fxrate'])) {
                    $invoice->usdfxrate = $currency_obj->get_fxrate_bytype($core->input['fxtype'], $invoice->currency, array('from' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 24:00'), 'year' => date('Y', $invoice->dateinvoiceduts), 'month' => date('m', $invoice->dateinvoiceduts)), array('precision' => 4));
                }

                if(empty($invoice->usdfxrate)) {
                    $invoice->usdfxrate = 1;
                }
                if(!is_array($invoicelines)) {
                    continue;
                }
                foreach($invoicelines as $invoiceline) {
                    $iltrx = $invoiceline->get_transaction();
                    if(is_object($iltrx)) {
                        $outputstack = $iltrx->get_outputstack();
                    }
                    if(is_object($outputstack)) {
                        $inputstack = $outputstack->get_inputstack();
                    }

                    $product = $invoiceline->get_product_local();
                    if(!isset($product->name)) {
                        $product = $invoiceline->get_product();
                        $invoiceline->segment = $product->get_category()->name;

                        if(is_object($inputstack)) {
                            $invoiceline->suppliername = $inputstack->get_supplier()->name;
                        }
                    }
                    else {
                        $invoiceline->suppliername = $product->get_supplier()->name;
                        $invoiceline->segment = $product->get_defaultchemfunction()->get_segment()->title;
                        if(empty($invoiceline->segment)) { /* Temp legacy fallback */
                            $invoiceline->segment = $product->get_segment()['title'];
                        }
                    }

                    if(empty($invoiceline->segment)) {
                        $invoiceline->segment = 'Unknown Segment';
                    }

                    $invoiceline->productname = $product->name;
                    if(empty($invoiceline->suppliername)) {
                        $invoiceline->suppliername = 'Unknown Supplier';
                    }

                    $invoiceline->uom = $invoiceline->get_uom()->uomsymbol;
                    $invoiceline->costlocal = $invoiceline->get_cost();
                    if($invoiceline->qtyinvoiced < 0) {
                        $invoiceline->costlocal = 0 - $invoiceline->costlocal;
                    }

                    $invoiceline->unitcostlocal = $invoiceline->costlocal / $invoiceline->qtyinvoiced;
                    $invoiceline->costusd = $invoiceline->costlocal / $invoice->usdfxrate;
                    $invoiceline->unitcostusd = $invoiceline->costusd / $invoiceline->qtyinvoiced;

                    if(is_object($inputstack)) {
                        $input_inoutline = $inputstack->get_transcation()->get_inoutline();
                        if(is_object($input_inoutline)) {
                            $ioinvoiceline = $input_inoutline->get_invoiceline();
                            if(is_object($ioinvoiceline)) {
                                $invoiceline->purchaseprice = $ioinvoiceline->priceactual;
                                $invoiceline->purchasecurr = $ioinvoiceline->get_invoice()->get_currency()->uomsymbol;
                                $invoiceline->purchasepriceusd = 0;
                            }
                            unset($ioinvoiceline);
                        }
                        else {
                            $invoiceline->purchaseprice = 0;
                        }
                    }

                    $invoiceline->grossmargin = $invoiceline->linenetamt - ($invoiceline->purchaseprice * $invoiceline->qtyinvoiced);
                    $invoiceline->grossmarginusd = $invoiceline->grossmargin / $invoice->usdfxrate;
                    $invoiceline->netmargin = $invoiceline->linenetamt - $invoiceline->costlocal;
                    $invoiceline->netmarginusd = $invoiceline->netmargin / $invoice->usdfxrate;
                    $invoiceline->marginperc = $invoiceline->netmargin / $invoiceline->linenetamt;

                    $output .= '<tr>';
                    foreach($cols as $col) {
                        $value = $invoice->{$col};
                        if(empty($value)) {
                            $value = $invoiceline->{$col};
                        }

                        $data[$invoiceline->c_invoiceline_id][$col] = $value;
                    }

                    if($invoiceline->marginperc < 0 || $invoiceline->marginperc > 0.5) {
                        $outliers[$invoiceline->c_invoiceline_id] = $data[$invoiceline->c_invoiceline_id];
                    }
                }
            }
        }
        else {
            redirect($url, $delay, $redirect_message);
        }

        if($core->input['type'] == 'analytic' || $core->input['type'] == 'dimensional') {
            $overwrite = array('marginperc' => array('fields' => array('divider' => 'netmargin', 'dividedby' => 'linenetamt'), 'operation' => '/'),
                    'priceactual' => array('fields' => array('divider' => 'linenetamt', 'dividedby' => 'qtyinvoiced'), 'operation' => '/'));
            $formats = array('marginperc' => array('style' => NumberFormatter::PERCENT, 'pattern' => '#0.##'));
            $required_fields = array('qtyinvoiced', 'priceactual', 'linenetamt', 'purchaseprice', 'costlocal', 'grossmargin', 'netmargin', 'marginperc');

            if($core->input['type'] == 'analytic') {
                $current_year = date('Y', TIME_NOW);
                $required_tables = array('segmentsummary' => array('segment'), 'salesrepsummary' => array('salesrep'), 'suppliersummary' => array('suppliername'), 'customerssummary' => array('customername'));

                $yearsummary_filter = "c_invoice.ad_org_id IN ('".implode("','", $orgs)."') AND docstatus NOT IN ('VO', 'CL') AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', strtotime((date('Y', TIME_NOW) - 2).'-01-01'))."' AND '".date('Y-m-d 00:00:00', $period['to'])."')";
                $monthdata = $integration->get_sales_byyearmonth($yearsummary_filter);
                if(is_array($monthdata)) {
                    $formatter = new NumberFormatter('EN_en', NumberFormatter::DECIMAL, '#.##');
                    $salesreport .= '<table width="100%" class="datatable datatable-striped">';
                    $salesreport .= '<tr><th style="font-size:14px; font-weight: bold; background-color: #F1F1F1;">Sales Rep</th>';
                    for($i = 1; $i <= 12; $i++) {
                        $salesreport .= '<th style="font-size:14px; font-weight: bold; background-color: #F1F1F1;">'.DateTime::createFromFormat('m', $i)->format('M').'</th>';
                    }
                    for($y = $current_year; $y >= ($current_year - 1); $y--) {
                        $salesreport .= '<th style="font-size:14px; font-weight: bold; background-color: #F1F1F1;">'.$y.'</th>';
                    }
                    $salesreport .= '</tr>';
                    foreach($monthdata['qty'] as $salerepid => $salerepdata) {
                        $currentyeardata = $salerepdata[$current_year];
                        $salesreport .= '<tr>';
                        $salesrep = new IntegrationOBUser($salerepid, $integration->get_dbconn());
                        if(empty($salesrep->name)) {
                            $salesrep->name = 'Not Specified';
                        }
                        $salesreport .= '<td>'.$salesrep->name.'</td>';

                        for($i = 1; $i <= 12; $i++) {
                            if(!isset($currentyeardata[$i])) {
                                $currentyeardata[$i] = 0;
                            }
                            $salesreport .= '<td style="text-align: right;">'.$formatter->format($currentyeardata[$i]).'</td>';
                        }
                        for($y = $current_year; $y >= ($current_year - 1); $y--) {
                            if(!is_array($salerepdata[$y])) {
                                $salerepdata[$y][] = 0;
                            }
                            $salesreport .= '<td style="text-align: right;">'.$formatter->format(array_sum($salerepdata[$y])).'</td>';
                        }
                        $salesreport .= '</tr>';
                    }
                    $invoicelinesdata = new IntegrationOBInvoiceLine(null, $integration->get_dbconn());
                    $yearsumrawtotals = $invoicelinesdata->get_aggreateddata_byyearmonth(null, $yearsummary_filter." AND c_invoice.issotrx='Y'");
                    foreach($yearsumrawtotals as $totaldata) {
                        $yearsummarytotals[$totaldata['year']][$totaldata['month']] = $totaldata['qty'];
                    }
                    for($y = $current_year; $y >= ($current_year - 1); $y--) {
                        $salesreport .= '<tr><th>Totals ('.$y.')</th>';
                        for($i = 1; $i <= 12; $i++) {
                            $salesreport .= '<th style="text-align: right;">'.$formatter->format($yearsummarytotals[$y][$i]).'</th>';
                        }

                        for($yy = $current_year; $yy >= ($current_year - 1); $yy--) {
                            if($yy != $y) {
                                $salesreport .= '<th style="text-align: center;">-</th>';
                                continue;
                            }
                            if(!is_array($yearsummarytotals[$yy])) {
                                $yearsummarytotals[$yy][] = 0;
                            }
                            $salesreport .= '<th style="text-align: right;">'.$formatter->format(array_sum($yearsummarytotals[$yy])).'</th>';
                        }
                        $salesreport .= '</tr>';
                    }
                    $salesreport .= '</table>';
                    unset($yearsumrawtotals, $yearsummarytotals, $monthdata, $currentyeardata);
                }
            }
            elseif($core->input['type'] == 'dimensional') {
                $required_tables = array('detailed' => array('month', 'week', 'salesrep', 'suppliername', 'customername', 'productname'));
            }
            foreach($required_tables as $dimensions) {
                $rawdata = $data;
                $dimensionalreport = new DimentionalData();
                $dimensionalreport->set_dimensions(array_combine(range(1, count($dimensions)), array_values($dimensions)));
                $dimensionalreport->set_requiredfields($required_fields);
                $dimensionalreport->set_data($rawdata);
                $salesreport .= '<table width="100%" class="datatable">';
                $salesreport .= '<tr><th></th>';
                foreach($required_fields as $field) {
                    $salesreport .= '<th>'.$field.'</th>';
                }
                $salesreport .= '</tr>';
                $salesreport .= $dimensionalreport->get_output(array('outputtype' => 'table', 'noenclosingtags' => true, 'formats' => $formats, 'overwritecalculation' => $overwrite));
                $salesreport .= '</table>';

                $chart_data = $dimensionalreport->get_data();
                //$chart = new Charts(array('x' => array($previous_year => $previous_year, $current_year => $current_year), 'y' => $barchart_quantities_values), 'bar');
            }
        }
        else {
            $required_details = array('outliers', 'data');
            foreach($required_details as $array) {
                if(!is_array(${$array})) {
                    continue;
                }
                $salesreport .= '<h3>'.ucwords($array).'</h3><table class="datatable"><tr class="thead">';
                foreach($cols as $col) {
                    if(!isset($lang->{$col})) {
                        $lang->{$col} = ucwords($col);
                    }
                    $salesreport .= '<th>'.$lang->{$col}.'</th>';
                }
                $salesreport .= '</tr>';

                if(is_array(${$array})) {
                    foreach(${$array} as $iol => $row) {
                        $salesreport .= '<tr>';
                        foreach($cols as $col) {
                            $value = $row[$col];
                            if(strstr($col, 'perc')) {
                                $value = numfmt_format(numfmt_create('en_EN', NumberFormatter::PERCENT), $value);
                            }
                            elseif(is_numeric($value) && $col != 'documentno') {
                                $value = numfmt_format(numfmt_create('en_EN', NumberFormatter::DECIMAL), $value);
                            }
                            $salesreport .= '<td>'.$value.'</td>';
                        }

                        $salesreport .= '</tr>';
                    }
                }
                $salesreport .= '</table><br />';
            }
        }
        eval("\$previewpage = \"".$template->get('crm_previewsalesreport')."\";");
        output_page($previewpage);
    }
}
?>