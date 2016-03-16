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
ini_set('max_execution_time', 0);
$lang->load('crm_salesreport');
if(!$core->input['action']) {
    $affiliates = Affiliates::get_affiliates(array('affid' => $core->user['affiliates']), array('returnarray' => true));
    $affiliates_list = parse_selectlist('affids[]', 2, $affiliates, $core->user['mainaffiliate']);
    $mainaffiliate_obj = Affiliates::get_affiliates(array('affid' => $core->user['mainaffiliate']));
    $affcurrency_obj = $mainaffiliate_obj->get_currency();
    $currencies = Currencies::get_data('', array('order' => array('sort' => 'ASC', 'by' => 'name')));
    $currencies_list = parse_selectlist('reportCurrency', 4, $currencies, $affcurrency_obj->numCode, '', '', array('blankstart' => 1, 'id' => "reportCurrency"));

    $fxtypes_selectlist = parse_selectlist('fxtype', 9, array('lastm' => $lang->lastmonthrate, 'ylast' => $lang->yearlatestrate, 'yavg' => $lang->yearaveragerate, 'mavg' => $lang->monthaveragerate, 'real' => $lang->realrate), 'mavg', 0);
    $dimensions = array('documentno' => 'Document Number', 'suppliername' => $lang->supplier, 'customername' => $lang->customer, 'productname' => $lang->product, 'segment' => $lang->segment, 'salesrep' => $lang->employee/* ,  'wid' => $lang->warehouse */);
    foreach($dimensions as $dimensionid => $dimension) {
        $dimension_item.='<li class = "ui-state-default" id='.$dimensionid.' title="Click and Hold to move the '.$dimension.'">'.$dimension.'</li>';
    }

    eval("\$generatepage = \"".$template->get('crm_generatesalesreport_live')."\";");
    output_page($generatepage);
}
else {
    if($core->input['action'] == 'do_perform_salesreportlive') {
        require_once ROOT.INC_ROOT.'integration_config.php';

        if($core->input['type'] == 'endofmonth') {
            //   $core->input['affids'][] = $core->user['mainaffiliate'];
            $core->input['fromDate'] = date('Y-m-d', strtotime('first day of last month')); //date('Y-01-01', strtotime($query_date));
            $core->input['toDate'] = date('Y-m-d', strtotime('last day of last month')); // date('Y-01-31', strtotime($query_date));
            $reporttype = $core->input['type'];
            $core->input['type'] = 'analytic';
            $core->input['reportCurrency'] = 840;
        }

        if(empty($core->input['affids'])) {
            output_xml('<status></status><message>No Affiliate selected</message>');
        }
        if(is_empty($core->input['fromDate'])) {
            output_xml('<status></status><message>Please specify the From date</message>');
        }

        /* In-line CSS styles in form of array in order to be compatible with email message */
        $css_styles['table-datacell'] = 'text-align: right;';
        $css_styles['altrow'] = 'background-color: #f7fafd;';
        $css_styles['altrow2'] = 'background-color: #F2FAED;';
        $css_styles['altrow3'] = 'background-color: #FBF28E;';
        $css_styles['greenrow'] = 'background-color: #F2FAED;';

        $current_date = getdate(TIME_NOW);
        $period['from'] = strtotime($core->input['fromDate']);
        $period['to'] = TIME_NOW;
        if(!empty($core->input['toDate'])) {
            $period['to'] = strtotime($core->input['toDate']);
        }

        if(is_array($core->input['affids'])) {
            foreach($core->input['affids'] as $affid) {
                $affiliate = new Affiliates($affid, false);
                $orgs[] = $affiliate->integrationOBOrgId;
                $currency_obj = $affiliate->get_currency();
            }
        }
        else {
            $affiliate = new Affiliates($core->input['affids'], false);
            $orgs[] = $affiliate->integrationOBOrgId;
            $currency_obj = $affiliate->get_currency();
        }
        $integration = new IntegrationOB($intgconfig['openbravo']['database'], $intgconfig['openbravo']['entmodel']['client']);

        $permissions = $core->user_obj->get_businesspermissions();
        // $reportaff = new Affiliates($core->input['affids'], false);
        // $currency_obj = $reportaff->get_currency();
        //$currency_obj = new Currencies('USD');

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
//        if(count($permissions['uid']) == 1 && in_array($core->user['uid'], $permissions['uid']) && isset($permissions['spid'])) {
//            $intuser = $core->user_obj->get_integrationObUser();
//            if(is_object($intuser)) {
//                $filters .= ' AND (salesrep_id=\''.$intuser->get_id().'\' OR salesrep_id IS NULL)';
//            }
//        }

        if(isset($core->input['reportCurrency']) && !empty($core->input['reportCurrency'])) {
            $currency_obj = Currencies::get_data(array('numCode' => $core->input['reportCurrency']));
        }

        $invoices = $integration->get_saleinvoices($filters);
        $cols = array('month', 'week', 'documentno', 'salesrep', 'customername', 'suppliername', 'productname', 'segment', 'uom', 'qtyinvoiced', 'priceactual', 'linenetamt', 'purchaseprice', 'unitcostlocal', 'costlocal', 'costusd', 'grossmargin', 'grossmarginusd', 'grossmarginperc', 'netmargin', 'netmarginusd', 'marginperc');

        if(is_array($invoices)) {
            foreach($invoices as $invoice) {
                $orgcurrency = $invoice->get_organisation()->get_currency();
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

                if($invoice->currency == 'GHC') {
                    $invoice->currency = 'GHS';
                }
                if(empty($core->input['fxrate'])) {
                    $usdcurrency_obj = new Currencies('USD');
                    $invoice->usdfxrate = $usdcurrency_obj->get_fxrate_bytype($core->input['fxtype'], $invoice->currency, array('from' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 24:00'), 'year' => date('Y', $invoice->dateinvoiceduts), 'month' => date('m', $invoice->dateinvoiceduts)), array('precision' => 4));
                }
                if($currency_obj->alphaCode != $invoice->currency) {
                    $invoice->localfxrate = $currency_obj->get_fxrate_bytype($core->input['fxtype'], $invoice->currency, array('from' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 24:00'), 'year' => date('Y', $invoice->dateinvoiceduts), 'month' => date('m', $invoice->dateinvoiceduts)), array('precision' => 4), $currency_obj->alphaCode);
                }
                else {
                    $invoice->localfxrate = 1;
                }
                if(empty($invoice->localfxrate)) {
                    $core->input['fxtype'] = "ylast";

                    $invoice->localfxrate = $currency_obj->get_fxrate_bytype($core->input['fxtype'], $invoice->currency, array('from' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 24:00'), 'year' => date('Y', $invoice->dateinvoiceduts), 'month' => date('m', $invoice->dateinvoiceduts)), array('precision' => 4), $currency_obj->alphaCode);
                    if(empty($invoice->localfxrate)) {
                        output_xml('<status>true</status><message>No local exchange rate<br/> From '.$invoice->currency.' to '.$currency_obj->alphaCode.' in the invoice period '.date('Y-m-d', $invoice->dateinvoiceduts).' </message>');
                        exit;
                        $invoice->localfxrate = 0;
                    }
                }

                if(empty($invoice->usdfxrate)) {
                    $core->input['fxtype'] = "ylast";

                    $usdcurrency_obj = new Currencies('USD');
                    $invoice->usdfxrate = $usdcurrency_obj->get_fxrate_bytype($core->input['fxtype'], $invoice->currency, array('from' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 24:00'), 'year' => date('Y', $invoice->dateinvoiceduts), 'month' => date('m', $invoice->dateinvoiceduts)), array('precision' => 4));
                    if(empty($invoice->usdfxrate)) {
                        output_xml('<status>true</status><message>no usd exchange rate '.$invoice->currency.' in the invoice period '.date('Y-m-d', $invoice->dateinvoiceduts).' </message>');
                        exit;
                        $invoice->usdfxrate = 0;
                    }
                }
                if(!is_array($invoicelines)) {
                    continue;
                }
                foreach($invoicelines as $invoiceline) {
                    if($invoiceline->linenetamt == 0) {
                        continue;
                    }
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
                    if(empty($invoiceline->suppliername) || strstr($invoice->bpartner_name, 'Orkila')) {
                        $invoiceline->suppliername = 'Unspecified';
                    }

                    $invoiceline->uom = $invoiceline->get_uom()->uomsymbol;
                    $invoiceline->costlocal = $invoiceline->get_cost();
                    if(!empty($invoiceline->costlocal)) {
                        $costcurrency = $invoiceline->get_transaction()->get_currency();
                        if($currency_obj->alphaCode != $costcurrency->iso_code) {
                            if($costcurrency->iso_code == 'GHC') {
                                $costcurrency->iso_code = 'GHS';
                            }
                            $invoice->localcostfxrate = $currency_obj->get_fxrate_bytype($core->input['fxtype'], $costcurrency->iso_code, array('from' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 24:00'), 'year' => date('Y', $invoice->dateinvoiceduts), 'month' => date('m', $invoice->dateinvoiceduts)), array('precision' => 4), $currency_obj->alphaCode);
                            if(empty($invoice->localcostfxrate)) {
                                $invoice->localcostfxrate = $currency_obj->get_fxrate_bytype('ylast', $costcurrency->iso_code, array('from' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 24:00'), 'year' => date('Y', $invoice->dateinvoiceduts), 'month' => date('m', $invoice->dateinvoiceduts)), array('precision' => 4), $currency_obj->alphaCode);
                                if(empty($invoice->localcostfxrate)) {
                                    output_xml('<status>true</status><message>No local exchange rate<br/> From '.$costcurrency->iso_code.' to '.$currency_obj->alphaCode.' in the invoice period '.date('Y-m-d', $invoice->dateinvoiceduts).' </message>');
                                    exit;
                                    $invoice->localcostfxrate = 0;
                                }
                            }
                            $invoiceline->costlocal /= $invoice->localcostfxrate;
                        }
                    }
                    // }
//                    if($currency_obj->alphaCode != $invoice->currency) {
//                        if(!empty($invoice->localfxrate)) {
//                            $invoiceline->costlocal = $invoiceline->costlocal / $invoice->localfxrate;
//                        }
//                        else {
//                            unset($invoiceline);
//                            continue;
//                        }
//                    }
                    if($invoiceline->qtyinvoiced < 0) {
                        $invoiceline->costlocal = 0 - $invoiceline->costlocal;
                    }

                    if(is_object($invoiceline->get_transaction())) {
                        $firsttransaction = $invoiceline->get_transaction()->get_firsttransaction();

                        if(is_object($firsttransaction)) {
                            $input_inoutline = $firsttransaction->get_inoutline();
                        }
                        else {
                            $input_inoutline = $invoiceline->get_transaction()->get_inoutline();
                        }
                    }
//                    if(is_object($inputstack)) {
//                        if(is_object($inputstack->get_transcation())) {
//                            $input_inoutline = $inputstack->get_transcation()->get_inoutline();
//                        }
                    if(is_object($input_inoutline)) {
                        $ioinvoiceline = $input_inoutline->get_invoiceline();
                        if(is_object($ioinvoiceline)) {
                            $invoiceline->purchaseprice = $ioinvoiceline->priceactual;
                            $invoiceline->purchasecurr = $ioinvoiceline->get_invoice()->get_currency()->iso_code;
                            $invoiceline->purchasepriceusd = 0;
                            if($currency_obj->alphaCode != $invoiceline->purchasecurr) {
                                $invoice->purchaseprice_localfxrate = $currency_obj->get_fxrate_bytype($core->input['fxtype'], $invoiceline->purchasecurr, array('from' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 24:00'), 'year' => date('Y', $invoice->dateinvoiceduts), 'month' => date('m', $invoice->dateinvoiceduts)), array('precision' => 4));
                            }
                            if($usdcurrency_obj->alphaCode != $invoiceline->purchasecurr) {
                                $invoice->purchaseprice_usdfxrate = $usdcurrency_obj->get_fxrate_bytype($core->input['fxtype'], $invoiceline->purchasecurr, array('from' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 24:00'), 'year' => date('Y', $invoice->dateinvoiceduts), 'month' => date('m', $invoice->dateinvoiceduts)), array('precision' => 4));
                                if(!empty($invoice->purchaseprice_usdfxrate)) {
                                    $invoiceline->purchasepriceusd = $invoiceline->purchaseprice / $invoice->purchaseprice_usdfxrate;
                                }
                                else {
                                    $invoiceline->purchasepriceusd = 0;
                                }
                            }
                            if(!empty($invoice->purchaseprice_localfxrate)) {
                                $invoiceline->purchaseprice /= $invoice->purchaseprice_localfxrate;
                            }
                            else {
                                $invoiceline->purchaseprice = 0;
                            }
                        }
                        unset($ioinvoiceline);
                    }
                    else {
                        $invoiceline->purchaseprice = 0;
                    }
                    // }

                    if(!empty($invoice->usdfxrate)) {
                        $invoiceline->costusd = $invoiceline->costlocal / $invoice->usdfxrate;
                    }
                    if($invoiceline->qtyinvoiced != 0) {
                        $invoiceline->unitcostlocal = $invoiceline->costlocal / $invoiceline->qtyinvoiced;
                        $invoiceline->unitcostusd = $invoiceline->costusd / $invoiceline->qtyinvoiced;
                    }

                    $required_fields = array('qtyinvoiced', 'priceactual', 'linenetamt', 'purchaseprice', 'costlocal', 'grossmargin', 'grossmarginperc', 'netmargin', 'marginperc');

                    $invoiceline->linenetamt = $invoiceline->linenetamt / 1000;
                    /* Convert to local currency if invoice is in foreign currency */
                    if($currency_obj->alphaCode != $invoice->currency) {
                        if(!empty($invoice->localfxrate)) {
                            $invoiceline->priceactual /= $invoice->localfxrate;
                            $invoiceline->linenetamt /= $invoice->localfxrate;
                        }
                        else {
                            unset($invoiceline);
                            continue;
                        }
                    }

                    $invoiceline->unitcostlocal = $invoiceline->unitcostlocal / 1000;
                    $invoiceline->unitcostusd = $invoiceline->unitcostusd / 1000;

                    $invoiceline->costlocal = $invoiceline->costlocal / 1000;
                    $invoiceline->costusd = $invoiceline->costusd / 1000;
                    $invoiceline->purchaseprice = $invoiceline->purchaseprice / 1000;
                    $invoiceline->grossmargin = $invoiceline->linenetamt - ($invoiceline->purchaseprice * $invoiceline->qtyinvoiced);
                    if(!empty($invoice->usdfxrate)) {
                        $invoiceline->grossmarginusd = $invoiceline->grossmargin / $invoice->usdfxrate;
                    }
                    $invoiceline->netmargin = $invoiceline->linenetamt - $invoiceline->costlocal;
                    if(!empty($invoice->usdfxrate)) {
                        $invoiceline->netmarginusd = $invoiceline->netmargin / $invoice->usdfxrate;
                    }
                    $invoiceline->marginperc = $invoiceline->netmargin / $invoiceline->linenetamt;
                    $invoiceline->grossmarginperc = $invoiceline->grossmargin / $invoiceline->linenetamt;

                    $output .= '<tr>';
                    foreach($cols as $col) {
                        $value = $invoice->{$col};
                        if(empty($value)) {
                            $value = $invoiceline->{$col};
                        }
                        $data[$invoiceline->c_invoiceline_id][$col] = $value;
                        if($col == 'linenetamt') {
                            $data_linenetamt[$invoiceline->c_invoiceline_id] = $invoiceline->{$col};
                        }
                    }

                    if($invoiceline->marginperc < 0 || $invoiceline->marginperc > 0.5) {
                        $outliers[$invoiceline->c_invoiceline_id] = $data[$invoiceline->c_invoiceline_id];
                    }
                }
            }
            $salesreport_header = '<h1>'.$lang->salesreport.'<small><br />'.$lang->{$core->input['type']}.'</small><br />Values are in Thousands';
            if($reporttype != 'endofmonth') {
                $salesreport_header .= '<small>Local Currency';
            }
            $salesreport_header .= '(K '.$currency_obj->alphaCode.')</small></h1>';
            $salesreport_header .= '<p><em>The report might have issues in the cost information. If so please report them to the ERP Team.</em></p><br/>';


            if($core->input['type'] == 'analytic' || $core->input['type'] == 'dimensional') {
                $overwrite = array('marginperc' => array('fields' => array('divider' => 'netmargin', 'dividedby' => 'linenetamt'), 'operation' => '/'),
                        'grossmarginperc' => array('fields' => array('divider' => 'grossmargin', 'dividedby' => 'linenetamt'), 'operation' => '/'),
                        'priceactual' => array('fields' => array('divider' => 'linenetamt', 'dividedby' => 'qtyinvoiced'), 'operation' => '/'));

                $formats = array('marginperc' => array('style' => NumberFormatter::PERCENT_SYMBOL),
                        'grossmarginperc' => array('style' => NumberFormatter::PERCENT_SYMBOL),
                );
                $required_fields = array('qtyinvoiced', 'priceactual', 'linenetamt', 'purchaseprice', 'costlocal', 'grossmargin', 'grossmarginperc', 'netmargin', 'marginperc');

                if($core->input['type'] == 'analytic') {
                    $current_year = date('Y', $period['to']);

                    if(date('m') == '01' && $reporttype == 'endofmonth') {
                        $current_year = date('Y') - 1;
                    }
                    $required_tables = array('segmentsummary' => array('segment'), 'salesrepsummary' => array('salesrep', 'segment'), 'suppliersummary' => array('suppliername'), 'customerssummary' => array('customername', 'segment'));

                    $yearsummary_filter = "EXISTS (SELECT c_invoice_id FROM c_invoice WHERE c_invoice.c_invoice_id=c_invoiceline.c_invoice_id AND issotrx='Y' AND ad_org_id IN ('".implode("','", $orgs)."') AND docstatus NOT IN ('VO', 'CL') AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', strtotime((date('Y', TIME_NOW) - 2).'-01-01'))."' AND '".date('Y-m-d 00:00:00', $period['to'])."'))";
                    //$monthdata = $integration->get_sales_byyearmonth($yearsummary_filter);
                    $intgdb = $integration->get_dbconn();
                    $invoicelines = new IntegrationOBInvoiceLine(null);
                    $mdata = $invoicelines->get_data_byyearmonth($yearsummary_filter, array('reportcurrency' => $currency_obj->alphaCode, 'fxtype' => $core->input['fxtype']));

                    if(isset($core->input['generatecharts']) && $core->input['generatecharts'] == 1) {
                        $classifications = $invoicelines->get_classification($mdata['dataperday'], $period, array('reporttype' => $reporttype));
                    }

                    $monthdata = $mdata['salerep'];
                    if(is_array($monthdata)) {
                        //  $formatter = new NumberFormatter('EN_en', NumberFormatter::INTEGER_DIGITS, '#.##');
                        $percformatter = new NumberFormatter('EN_en', NumberFormatter::PERCENT);
                        //   $salesreport .= '<h2>Monthly Overview by BM</h2>';
                        $salesreport .= '<table width="100%" class="datatable">';
                        $salesreport .= '<tr style="background-color:#92D050;"><th colspan=15>Monthly Overview by BM</th></tr>';
                        $salesreport .= '<tr><th style="font-size:14px; font-weight: bold; background-color: #F1F1F1;">Sales Rep</th>';
                        for($i = 1; $i <= 12; $i++) {
                            $salesreport .= '<th style="font-size:14px; font-weight: bold; background-color: #F1F1F1;">'.DateTime::createFromFormat('m', $i)->format('M').'</th>';
                        }
                        for($y = $current_year; $y >= ($current_year - 1); $y--) {
                            $salesreport .= '<th style="font-size:14px; font-weight: bold; background-color: #F1F1F1;">'.$y.'</th>';
                        }
                        $salesreport .= '</tr>';
                        if(is_array($monthdata['linenetamt'])) {
                            foreach($monthdata['linenetamt'] as $salerepid => $salerepdata) {
                                $currentyeardata = $salerepdata[$current_year];
                                $salesreport .= '<tr style="background-color:#D0F6AA;">';
                                $salesrep = new IntegrationOBUser($salerepid, $integration->get_dbconn());
                                if(empty($salesrep->name) || $salesrep->name == 'System') {
                                    $salesrep->name = 'Not Specified';
                                    continue;
                                }
                                $salesreport .= '<td style="'.$css_styles['table-datacell'].'">'.$salesrep->name.'</td>';

                                for($i = 1; $i <= 12; $i++) {
                                    if(!isset($currentyeardata[$i])) {
                                        $currentyeardata[$i] = 0;
                                    }
                                    $numfmt = new NumberFormatter($lang->settings['locale'], NumberFormatter::DECIMAL);
                                    if($currentyeardata[$i] / 1000 > 10) {
                                        $numfmt->setPattern("#,##0");
                                    }
                                    else {
                                        $numfmt->setPattern("#0.##");
                                    }
                                    $salesreport .= '<td style="'.$css_styles['table-datacell'].'">'.$numfmt->format($currentyeardata[$i] / 1000).'</td>'; //$formatter->format($currentyeardata[$i] / 1000)
                                }
                                $styleindex = 3;
                                for($y = $current_year; $y >= ($current_year - 1); $y--) {
                                    if(!is_array($salerepdata[$y])) {
                                        $salerepdata[$y][] = 0;
                                    }
                                    for($m = 1; $m <= 12; $m++) {
                                        $salerepdata[$y][$m] = $salerepdata[$y][$m] / 1000;
                                        $yearsummarytotals[$y][$m] += $salerepdata[$y][$m];
                                    }
                                    $salesreport .= '<td style="'.$css_styles['table-datacell'].' '.$css_styles['altrow'.$styleindex].'">'.number_format(array_sum($salerepdata[$y])).'</td>'; //$formatter->format(array_sum($salerepdata[$y]))
                                    $styleindex--;
                                }
                                $salesreport .= '</tr>';
//                            if(empty($rowstyle)) {
//                                $rowstyle = $css_styles['altrow'];
//                            }
//                            else {
//                                $rowstyle = '';
//                            }
                            }
                        }
                        if(is_array($classifications) && (isset($core->input['generatecharts']) && $core->input['generatecharts'] == 1)) {
                            $classifications_output = $invoicelines->parse_classificaton_tables($classifications, array('reporttype' => $reporttype));
                        }
//                    $invoicelinesdata = new IntegrationOBInvoiceLine(null, $integration->get_dbconn());
//                    $yearsumrawtotals = $invoicelinesdata->get_aggreateddata_byyearmonth(null, $yearsummary_filter." AND c_invoice.issotrx='Y'");
//                    foreach($yearsumrawtotals as $totaldata) {
//                        $yearsummarytotals[$totaldata['year']][$totaldata['month']] = $totaldata['qty'];
//                    }
                        $styleindex = 3;
                        for($y = $current_year; $y >= ($current_year - 1); $y--) {
                            $salesreport .= '<tr style="'.$css_styles['altrow'.$styleindex].'"><th>Totals ('.$y.')</th>';
                            $styleindex--;
                            for($i = 1; $i <= 12; $i++) {
                                $salesreport .= '<th style="text-align: right;">'.number_format($yearsummarytotals[$y][$i]).'</th>'; //$formatter->format
                            }

                            for($yy = $current_year; $yy >= ($current_year - 1); $yy--) {
                                if($yy != $y) {
                                    $salesreport .= '<th style="text-align: center;">-</th>';
                                    continue;
                                }
                                if(!is_array($yearsummarytotals[$yy])) {
                                    $yearsummarytotals[$yy][] = 0;
                                }
                                $salesreport .= '<th style="text-align: right;">'.number_format(array_sum($yearsummarytotals[$yy])).'</th>'; //$formatter->format
                            }
                            $salesreport .= '</tr>';
                        }
                        $salesreport .= '</table><br/><br/>'.$classifications_output['tablesandcharts'];

                        $salesreport = $salesreport_header.$classifications_output['summary'].$salesreport;
                        unset($yearsumrawtotals, $yearsummarytotals, $currentyeardata);

                        /* YTD Comparison */

                        $salesreport .= '<br/><table width="100%" class="datatable" style="color:black;">';
                        $salesreport .= '<tr style="background-color:#92D050;"><th colspan=5>Progression by BM</th></tr>';
                        $salesreport .= '<tr><th style="font-size:14px; font-weight: bold; background-color: #F1F1F1;">Sales Rep</th>';
                        $salesreport .= '<th style="font-size:14px; font-weight: bold; background-color: #F1F1F1; text-align: center;">YTD</th>';
                        $salesreport .= '<th style="font-size:14px; font-weight: bold; background-color: #F1F1F1; text-align: center;">YTD / '.($current_year - 1).'</th>';
                        $salesreport .= '<th style="font-size:14px; font-weight: bold; background-color: #F1F1F1; text-align: center;">'.$current_year.' objective</th>';
                        $salesreport .= '<th style="font-size:14px; font-weight: bold; background-color: #F1F1F1; text-align: center;">YTD / '.$current_year.' objective</th>';
                        $salesreport .= '</tr>';
                        if(is_array($monthdata['linenetamt'])) {
                            foreach($monthdata['linenetamt'] as $salerepid => $salerepdata) {
                                for($y = $current_year; $y >= ($current_year - 1); $y--) {
                                    if(!is_array($salerepdata[$y])) {
                                        $salerepdata[$y][] = 0;
                                    }

                                    foreach($salerepdata[$y] as $key => $val) {
                                        if(!empty($val)) {
                                            $salerepdata[$y][$key] = $val / 1000;
                                        }
                                    }
                                }

                                $salesrep = new IntegrationOBUser($salerepid, $integration->get_dbconn());
                                if(empty($salesrep->name) || $salesrep->name == 'System') {
                                    continue;
                                }
                                $salerep_user = Users::get_data_byattr('displayName', $salesrep->name);
                                $salesreport .= '<tr style="'.$rowstyle.'">';
                                $salesreport .= '<td>'.$salesrep->name.'</td>';
                                $numfmt = new NumberFormatter($lang->settings['locale'], NumberFormatter::DECIMAL);
                                if(array_sum($salerepdata[$current_year]) > 10) {
                                    $numfmt->setPattern("#,##0");
                                }
                                else {
                                    $numfmt->setPattern("#0.##");
                                }
                                $salesreport .= '<td style="text-align: right;">'.$numfmt->format(array_sum($salerepdata[$current_year])).'</td>'; //$formatter->format

                                $percentages['prevyear']['linenetamt'] = 0.10;
                                if(array_sum($salerepdata[$current_year - 1]) != 0) {
                                    $percentages['prevyear']['linenetamt'] = (array_sum($salerepdata[$current_year]) / array_sum($salerepdata[$current_year - 1]));
                                }
                                $salesreport .= '<th style="text-align: right;">'.$percformatter->format($percentages['prevyear']['linenetamt']).'</th>';

                                /* Get budget */
                                if(is_object($salerep_user)) {
                                    $budgetlines = BudgetLines::get_data(array('businessMgr' => $salerep_user->uid, 'bid' => '(SELECT bid FROM budgeting_budgets WHERE year='.$current_year.' AND affid IN ('.implode(',', $core->input['affids']).'))'), array('returnarray' => true, 'operators' => array('bid' => 'IN')));
                                    $percentages['budget']['amt'] = 0.10;
                                    if(is_array($budgetlines)) {
                                        foreach($budgetlines as $budgetline) {
                                            $budget_totals['qty'] += $budgetline->quantity;
                                            $budget_totals['amt'] += $budgetline->get_convertedamount($currency_obj) / 1000;
                                        }
                                        if(!empty($budget_totals['amt'])) {
                                            $percentages['budget']['amt'] = (array_sum($salerepdata[$current_year]) / $budget_totals['amt']);
                                        }
                                    }

                                    $salesreport .= '<th style="text-align: right;">'.number_format($budget_totals['amt']).'</th>'; //$formatter->format
                                    $salesreport .= '<th style="text-align: right;">'.$percformatter->format($percentages['budget']['amt']).'</th>';
                                }
                                else {
                                    $salesreport .= '<th style="text-align: right;">-</th>';
                                    $salesreport .= '<th style="text-align: right;">-</th>';
                                }
                                $salesreport .= '</tr>';
                                if(empty($rowstyle)) {
                                    $rowstyle = $css_styles['altrow'];
                                }
                                else {
                                    $rowstyle = '';
                                }
                                unset($budget_totals, $percentages);
                            }
                        }
                        $salesreport .= '</table>';
                        /* YTD Comparison - END */

                        unset($monthdata);
                    }
                }
                elseif($core->input['type'] == 'dimensional') {
                    $required_tables = array('detailed' => explode(',', $core->input['salereport']['dimension'][0]));
                }


                $tabletypes[] = 'mainsummaytables';
                if($reporttype == 'endofmonth') {
                    $tabletypes[] = 'ytdsummarytables';
                }

                if(is_array($required_tables)) {
                    foreach($tabletypes as $type) {
                        if($type == 'ytdsummarytables') {
                            $ytddata = get_ytddata($core->input, $period, $orgs);
                        }
                        foreach($required_tables as $tabledesc => $dimensions) {
                            $dimensionalreport = new DimentionalData();
                            if($type == 'ytdsummarytables') {
                                unset($rawdata);
                                $rawdata = $ytddata;
                                $lang->{$tabledesc} = $lang->{$tabledesc}.' YTD';
                            }
                            else {
                                $rawdata = $data;
                            }
                            if(!is_array($rawdata)) {
                                continue;
                            }
                            $dimensionalreport->set_dimensions(array_combine(range(1, count($dimensions)), array_values($dimensions)));
                            $dimensionalreport->set_requiredfields($required_fields);
                            $dimensionalreport->set_data($rawdata);
                            // $salesreport .= '<h2><br />'.$lang->{$tabledesc}.'</h2>';
                            $salesreport .= '<br/><table width="100%" class="datatable" style="color:black;">';
                            $salesreport .= '<tr style="background-color:#92D050;"><th colspan="10">'.$lang->{$tabledesc}.'</th></tr>';
                            $salesreport .= '<tr><th></th>';
                            foreach($required_fields as $field) {
                                if(!isset($lang->{$field})) {
                                    $lang->{$field} = $field;
                                }
                                $salesreport .= '<th>'.$lang->{$field}.'</th>';
                            }
                            $salesreport .= '</tr>';
                            $salesreport .= $dimensionalreport->get_output(array('outputtype' => 'table', 'noenclosingtags' => true, 'formats' => $formats, 'overwritecalculation' => $overwrite));
                            $salesreport .= '</table>';

                            $chart_data = $dimensionalreport->get_data();
                            //$chart = new Charts(array('x' => array($previous_year => $previous_year, $current_year => $current_year), 'y' => $barchart_quantities_values), 'bar');
                        }
                        $cache->flush('totals');
                        unset($dimensionalreport);
                    }
                }
            }
            else {
                $required_details = array('outliers', 'data');
                foreach($required_details as $array) {
                    if(!is_array(${$array})) {
                        continue;
                    }
                    $salesreport .= '<h3>'.ucwords($array).'</h3><table class="datatable">';
                    $salesreport .= '<thead><tr class="thead">';
                    $tablefilters = '';
                    foreach($cols as $col) {
                        if(!isset($lang->{$col})) {
                            $lang->{$col} = ucwords($col);
                        }
                        $salesreport .= '<th>'.$lang->{$col}.'</th>';
                        $tablefilters .= '<th><input class="inlinefilterfield" type="text" style="width: 95%;"/></th>';
                    }
                    $salesreport .= '</tr>';

                    if($core->input['typereporttype'] != 'email') {
                        $salesreport .= '<tr>'.$tablefilters.'</tr>';
                    }
                    $salesreport .= '</thead>';
                    unset($tablefilters);
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

                    $totalcols = array('qtyinvoiced', 'linenetamt', 'purchaseprice', 'costlocal', 'costusd', 'grossmargin', 'grossmarginperc', 'grossmarginusd', 'netmargin', 'netmarginusd');
                    $avgcols = array('priceactual', 'purchaseprice', 'unitcostlocal', 'grossmarginperc', 'marginperc');

                    $salesreport .= '<tfoot>';
                    foreach($cols as $col) {
                        $class = '';
                        if(in_array($col, $avgcols)) {
                            $class = 'colavg';
                        }
                        else if(in_array($col, $totalcols)) {
                            $class = 'coltotal';
                        }
                        $salesreport .= '<td class="'.$class.'"></td>';
                    }
                    $salesreport .= '</tfoot>';
                    $salesreport .= '</table><br />';
                }
            }
        }
        else {
            $salesreport = '<p width="100%">No match found  <br/> Empty Report</p>';
            //eval("\$previewpage = \"".$template->get('crm_previewsalesreport')."\";");
            // output_xml('<status>true</status><message><![CDATA['.$previewpage.']]></message>');
            ////exit;
        }


        $affiliates_addrecpt = array(
                7 => array(457, 367),
                22 => array(457, 367),
                23 => array(457, 367),
                1 => array(457, 367),
                21 => array(457, 367),
                27 => array(457, 367),
                16 => array(457, 367),
                20 => array(457, 367),
                2 => array(457, 367),
                19 => array(457, 367),
                29 => array(457, 367),
                11 => array(457, 367),
        );

        if($core->input['reporttype'] == 'email') {
            if(count($core->input['affids']) > 1) {
                error('Cannot send when report contain multiple affiliates');
            }
            $mailer = new Mailer();
            $mailer = $mailer->get_mailerobj();
            $mailer->set_required_contenttypes(array('html'));
            $mailer->set_from(array('name' => 'OCOS Mailer', 'email' => $core->settings['maileremail']));
            if($reporttype == 'endofmonth' || $core->input['type'] == 'analytic') {
                $mailer->set_subject('Sales Report '.$affiliate->name.' '.date('F', strtotime($core->input['fromDate'])).' - '.date('y', strtotime($core->input['fromDate'])));
            }
            else {
                $mailer->set_subject('Sales Report '.$affiliate->name.' '.$core->input['fromDate'].' - '.$core->input['toDate']);
            }
            $mailer->set_message($salesreport);

            $finManager = $affiliate->get_financialemanager();
            if(!is_object($finManager)) {
                $finManager = $affiliate->get_globalfinancialemanager();
            }

            $recipients = array(
                    $affiliate->get_generalmanager()->email,
                    $affiliate->get_supervisor()->email,
                    $finManager->email,
                    $affiliate->get_coo()->email,
                    $affiliate->get_commercialManager()->email,
                    $core->user_obj->email,
                    Users::get_data(array('uid' => 3))->email/* Always include User 3 */
            );

            if(isset($affiliates_addrecpt[$affiliate->affid])) {
                foreach($affiliates_addrecpt[$affiliate->affid] as $uid) {
                    if(!is_numeric($uid)) {
                        $adduser = Users::get_user_byattr('username', $uid);
                    }
                    else {
                        $adduser = new Users($uid);
                    }
                    $recipients[] = $adduser->get()['email'];
                }
            }
            $recipients = array_filter($recipients);
            if(is_array($recipients)) {
                $recipients = array_unique($recipients);
            }
            $mailer->set_to($recipients);

//            $mailer->set_to('zaher.reda@orkila.com');
//            print_r($mailer->debug_info());
//            exit;
            $mailer->send();
            if($mailer->get_status() === true) {
                $sentreport = new ReportsSendLog();
                $sentreport->set(array('affid' => $affiliate->get_id(), 'report' => 'salesreport', 'date' => TIME_NOW, 'sentBy' => $core->user['uid'], 'sentTo' => ''))->save();
                unset($core->input['reporttype']);
                redirect('index.php?'.http_build_query($core->input), 1, 'Success');
            }
            else {
                error($lang->errorsendingemail);
            }
            unset($salesreport);
        }
        else {
            if(!is_array($core->input['affids']) || count($core->input['affids']) == 1) {
                $finManager = $affiliate->get_financialemanager();
                if(!is_object($finManager)) {
                    $finManager = $affiliate->get_globalfinancialemanager();
                }
                $recipients = array(
                        $affiliate->get_generalmanager()->displayName,
                        $affiliate->get_supervisor()->displayName,
                        $finManager->displayName,
                        $affiliate->get_coo()->displayName,
                        $affiliate->get_commercialManager()->displayName,
                        $core->user_obj->displayName,
                        Users::get_data(array('uid' => 3))->get_displayname()/* Always include User 3 */);

                if(isset($affiliates_addrecpt[$affiliate->affid])) {
                    foreach($affiliates_addrecpt[$affiliate->affid] as $uid) {
                        if(!is_numeric($uid)) {
                            $adduser = Users::get_user_byattr('username', $uid);
                        }
                        else {
                            $adduser = new Users($uid);
                        }
                        $recipients[] = $adduser->get_displayname();
                    }
                }

                $recipients = array_unique($recipients);
                if(is_array($recipients)) {
                    $recipients = array_filter($recipients);
                    $salesreport .= '<hr /><div class = "ui-state-highlight ui-corner-all" style = "padding-left: 5px; margin-bottom:10px;"><p>This report will be sent to <ul><li>'.implode('</li><li>', $recipients).'</li></ul></p></div>';
                    $salesreport .= '<a href = "index.php?reporttype=email&amp;'.http_build_query($core->input).'"><button class = "button">Send by email</button></a>';
                }
            }
        }
        eval("\$previewpage = \"".$template->get('crm_previewsalesreport')."\";");
        output_xml('<status>true</status><message><![CDATA['.$previewpage.']]></message>');
    }
}
function get_ytddata($input_data, $period, $orgs) {
    global $core, $integration, $intgdb;
    $permissions = $core->user_obj->get_businesspermissions();
    if(!empty($input_data['spid'])) {
        $orderline_query_where = ' AND ime.localId IN ('.implode(', ', $input_data['spid']).')';
    }
    if(!empty($input_data['pid'])) {
        $orderline_query_where .= ' AND imp.localId IN ('.implode(', ', $input_data['pid']).')';
    }
    if(!empty($input_data['cid'])) {
        $query_where .= ' AND ime.localId IN ('.implode(', ', $input_data['cid']).')';
    }
    $filters = "c_invoice.ad_org_id IN ('".implode("','", $orgs)."') AND docstatus NOT IN ('VO', 'CL') AND (dateinvoiced BETWEEN '".date('Y-01-01 00:00:00', $period['from'])."' AND '".date('Y-m-d 00:00:00', $period['to'])."')";
    if(count($permissions['uid']) == 1 && in_array($$input_data['uid'], $permissions['uid']) && isset($permissions['spid'])) {
        $intuser = $core->user_obj->get_integrationObUser();
        if(is_object($intuser)) {
            $filters .= ' AND (salesrep_id = \''.$intuser->get_id().'\' OR salesrep_id IS NULL)';
        }
    }

    $affiliate = new Affiliates($input_data['affids'], false);
    $currency_obj = $affiliate->get_currency();

    if(isset($input_data['reportCurrency']) && !empty($input_data['reportCurrency'])) {
        $currency_obj = Currencies::get_data(array('numCode' => $input_data['reportCurrency']));
    }
    $invoices = $integration->get_saleinvoices($filters);
    $cols = array('month', 'week', 'documentno', 'salesrep', 'customername', 'suppliername', 'productname', 'segment', 'uom', 'qtyinvoiced', 'priceactual', 'linenetamt', 'purchaseprice', 'unitcostlocal', 'costlocal', 'costusd', 'grossmargin', 'grossmarginusd', 'grossmarginperc', 'netmargin', 'netmarginusd', 'marginperc');
    if(is_array($invoices)) {
        foreach($invoices as $invoice) {
            $orgcurrency = $invoice->get_organisation()->get_currency();
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
            $invoice->usdfxrate = $input_data['fxrate'];
            if($invoice->currency == 'GHC') {
                $invoice->currency = 'GHS';
            }
            if(empty($input_data['fxrate'])) {
                $usdcurrency_obj = new Currencies('USD');
                $invoice->usdfxrate = $usdcurrency_obj->get_fxrate_bytype($input_data['fxtype'], $invoice->currency, array('from' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 24:00'), 'year' => date('Y', $invoice->dateinvoiceduts), 'month' => date('m', $invoice->dateinvoiceduts)), array('precision' => 4));
            }
            if($currency_obj->alphaCode != $invoice->currency) {
                $invoice->localfxrate = $currency_obj->get_fxrate_bytype($input_data['fxtype'], $invoice->currency, array('from' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 24:00'), 'year' => date('Y', $invoice->dateinvoiceduts), 'month' => date('m', $invoice->dateinvoiceduts)), array('precision' => 4), $currency_obj->alphaCode);
            }
            else {
                $invoice->localfxrate = 1;
            }

            if(empty($invoice->localfxrate)) {
                $core->input['fxtype'] = "ylast";

                $invoice->localfxrate = $currency_obj->get_fxrate_bytype($core->input['fxtype'], $invoice->currency, array('from' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 24:00'), 'year' => date('Y', $invoice->dateinvoiceduts), 'month' => date('m', $invoice->dateinvoiceduts)), array('precision' => 4), $currency_obj->alphaCode);
                if(empty($invoice->localfxrate)) {
                    output_xml('<status>true</status><message>No local exchange rate<br/> From '.$invoice->currency.' to '.$currency_obj->alphaCode.' in the invoice period '.date('Y-m-d', $invoice->dateinvoiceduts).' </message>');
                    exit;
                    $invoice->localfxrate = 0;
                }
            }

            if(empty($invoice->usdfxrate)) {
                $input_data['fxtype'] = "ylast";
                $usdcurrency_obj = new Currencies('USD');
                $invoice->usdfxrate = $usdcurrency_obj->get_fxrate_bytype($input_data['fxtype'], $invoice->currency, array('from' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 24:00'), 'year' => date('Y', $invoice->dateinvoiceduts), 'month' => date('m', $invoice->dateinvoiceduts)), array('precision' => 4));
                if(empty($invoice->usdfxrate)) {
                    output_xml('<status>true</status><message>no usd exchange rate '.$invoice->currency.' in the invoice period '.date('Y-m-d', $invoice->dateinvoiceduts).' </message>');
                    exit;
                    $invoice->usdfxrate = 0;
                }
            }
            if(!is_array($invoicelines)) {
                continue;
            }
            foreach($invoicelines as $invoiceline) {
                if($invoiceline->linenetamt == 0) {
                    continue;
                }
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
                if(empty($invoiceline->suppliername) || strstr($invoice->bpartner_name, 'Orkila')) {
                    $invoiceline->suppliername = 'Unspecified';
                }

                $invoiceline->uom = $invoiceline->get_uom()->uomsymbol;
                $invoiceline->costlocal = $invoiceline->get_cost();

                if(!empty($invoiceline->costlocal)) {
                    $costcurrency = $invoiceline->get_transaction()->get_currency();
                    if($currency_obj->alphaCode != $costcurrency->iso_code) {
                        if($costcurrency->iso_code == 'GHC') {
                            $costcurrency->iso_code = 'GHS';
                        }
                        $invoice->localcostfxrate = $currency_obj->get_fxrate_bytype($core->input['fxtype'], $costcurrency->iso_code, array('from' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 24:00'), 'year' => date('Y', $invoice->dateinvoiceduts), 'month' => date('m', $invoice->dateinvoiceduts)), array('precision' => 4), $currency_obj->alphaCode);
                        if(empty($invoice->localcostfxrate)) {
                            $invoice->localcostfxrate = $currency_obj->get_fxrate_bytype('ylast', $costcurrency->iso_code, array('from' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 24:00'), 'year' => date('Y', $invoice->dateinvoiceduts), 'month' => date('m', $invoice->dateinvoiceduts)), array('precision' => 4), $currency_obj->alphaCode);
                            if(empty($invoice->localcostfxrate)) {
                                output_xml('<status>true</status><message>No local exchange rate<br/> From '.$costcurrency->iso_code.' to '.$currency_obj->alphaCode.' in the invoice period '.date('Y-m-d', $invoice->dateinvoiceduts).' </message>');
                                exit;
                                $invoice->localcostfxrate = 0;
                            }
                        }
                        $invoiceline->costlocal /= $invoice->localcostfxrate;
                    }
                }
                if($invoiceline->qtyinvoiced < 0) {
                    $invoiceline->costlocal = 0 - $invoiceline->costlocal;
                }

                if(is_object($invoiceline->get_transaction())) {
                    $firsttransaction = $invoiceline->get_transaction()->get_firsttransaction();

                    if(is_object($firsttransaction)) {
                        $input_inoutline = $firsttransaction->get_inoutline();
                    }
                    else {
                        $input_inoutline = $invoiceline->get_transaction()->get_inoutline();
                    }
                }
//                    if(is_object($inputstack)) {
//                        if(is_object($inputstack->get_transcation())) {
//                            $input_inoutline = $inputstack->get_transcation()->get_inoutline();
//                        }
                if(is_object($input_inoutline)) {
                    $ioinvoiceline = $input_inoutline->get_invoiceline();
                    if(is_object($ioinvoiceline)) {
                        $invoiceline->purchaseprice = $ioinvoiceline->priceactual;
                        $invoiceline->purchasecurr = $ioinvoiceline->get_invoice()->get_currency()->iso_code;
                        $invoiceline->purchasepriceusd = 0;
                        if($currency_obj->alphaCode != $invoiceline->purchasecurr) {
                            $invoice->purchaseprice_localfxrate = $currency_obj->get_fxrate_bytype($input_data['fxtype'], $invoiceline->purchasecurr, array('from' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 24:00'), 'year' => date('Y', $invoice->dateinvoiceduts), 'month' => date('m', $invoice->dateinvoiceduts)), array('precision' => 4));
                        }
                        if($usdcurrency_obj->alphaCode != $invoiceline->purchasecurr) {
                            $invoice->purchaseprice_usdfxrate = $usdcurrency_obj->get_fxrate_bytype($$input_data['fxtype'], $invoiceline->purchasecurr, array('from' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 24:00'), 'year' => date('Y', $invoice->dateinvoiceduts), 'month' => date('m', $invoice->dateinvoiceduts)), array('precision' => 4));
                            if(!empty($invoice->purchaseprice_usdfxrate)) {
                                $invoiceline->purchasepriceusd = $invoiceline->purchaseprice / $invoice->purchaseprice_usdfxrate;
                            }
                            else {
                                $invoiceline->purchasepriceusd = 0;
                            }
                        }
                        if(!empty($invoice->purchaseprice_localfxrate)) {
                            $invoiceline->purchaseprice /= $invoice->purchaseprice_localfxrate;
                        }
                        else {
                            $invoiceline->purchaseprice = 0;
                        }
                    }
                    unset($ioinvoiceline);
                }
                else {
                    $invoiceline->purchaseprice = 0;
                }
                // }

                if(!empty($invoice->usdfxrate)) {
                    $invoiceline->costusd = $invoiceline->costlocal / $invoice->usdfxrate;
                }
                if($invoiceline->qtyinvoiced != 0) {
                    $invoiceline->unitcostlocal = $invoiceline->costlocal / $invoiceline->qtyinvoiced;
                    $invoiceline->unitcostusd = $invoiceline->costusd / $invoiceline->qtyinvoiced;
                }

                $required_fields = array('qtyinvoiced', 'priceactual', 'linenetamt', 'purchaseprice', 'costlocal', 'grossmargin', 'grossmarginperc', 'netmargin', 'marginperc');

                $invoiceline->linenetamt = $invoiceline->linenetamt / 1000;
                /* Convert to local currency if invoice is in foreign currency */
                if($currency_obj->alphaCode != $invoice->currency) {
                    if(!empty($invoice->localfxrate)) {
                        $invoiceline->priceactual /= $invoice->localfxrate;
                        $invoiceline->linenetamt /= $invoice->localfxrate;
                    }
                    else {
                        unset($invoiceline);
                        continue;
                    }
                }

                $invoiceline->unitcostlocal = $invoiceline->unitcostlocal / 1000;
                $invoiceline->unitcostusd = $invoiceline->unitcostusd / 1000;

                $invoiceline->costlocal = $invoiceline->costlocal / 1000;
                $invoiceline->costusd = $invoiceline->costusd / 1000;
                $invoiceline->purchaseprice = $invoiceline->purchaseprice / 1000;
                $invoiceline->grossmargin = $invoiceline->linenetamt - ($invoiceline->purchaseprice * $invoiceline->qtyinvoiced);
                if(!empty($invoice->usdfxrate)) {
                    $invoiceline->grossmarginusd = $invoiceline->grossmargin / $invoice->usdfxrate;
                }
                $invoiceline->netmargin = $invoiceline->linenetamt - $invoiceline->costlocal;
                if(!empty($invoice->usdfxrate)) {
                    $invoiceline->netmarginusd = $invoiceline->netmargin / $invoice->usdfxrate;
                }
                $invoiceline->marginperc = $invoiceline->netmargin / $invoiceline->linenetamt;
                $invoiceline->grossmarginperc = $invoiceline->grossmargin / $invoiceline->linenetamt;

                $output .= '<tr>';
                foreach($cols as $col) {
                    $value = $invoice->{$col};
                    if(empty($value)) {
                        $value = $invoiceline->{$col};
                    }
                    $data[$invoiceline->c_invoiceline_id][$col] = $value;
                    if($col == 'linenetamt') {
                        $data_linenetamt[$invoiceline->c_invoiceline_id] = $invoiceline->{$col};
                    }
                }

                if($invoiceline->marginperc < 0 || $invoiceline->marginperc > 0.5) {
                    $outliers[$invoiceline->c_invoiceline_id] = $data[$invoiceline->c_invoiceline_id];
                }
            }
        }
    }
    return $data;
}

?>