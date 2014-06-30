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
    eval("\$generatepage = \"".$template->get('crm_generatesalesreport_live')."\";");
    output_page($generatepage);
}
else {
    if($core->input['action'] == 'do_generatereport') {
        require_once ROOT.INC_ROOT.'integration_config.php';
        if(empty($core->input['affids'])) {
            redirect('index.php?module=crm/salesreport');
        }

        if(is_empty($core->input['fromDate'])) {
            redirect('index.php?module=crm/salesreport');
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

        $filters = "ad_org_id IN ('".implode("','", $orgs)."') AND docstatus NOT IN ('VO', 'CL') AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', $period['from'])."' AND '".date('Y-m-d 00:00:00', $period['to'])."')";
        $integration = new IntegrationOB($intgconfig['openbravo']['database'], $intgconfig['openbravo']['entmodel']['client']);

        $invoices = $integration->get_saleinvoices($filters);
        $cols = array('month', 'week', 'documentno', 'salesrep', 'customername', 'suppliername', 'productname', 'segment', 'uom', 'qtyinvoiced', 'priceactual', 'linenetamt', 'purchaseprice', 'costlocal', 'grossmargin', 'netmargin', 'marginperc');
        if(is_array($invoices)) {
            $output = '<table class="datatable">';
            foreach($invoices as $invoice) {
                $invoice->customername = $invoice->get_customer()->name;
                $invoicelines = $invoice->get_invoicelines();
                $invoice->salesrep = $invoice->get_salesrep()->name;
                if(empty($invoice->salesrep)) {
                    $invoice->salesrep = 'Unknown Sales Rep';
                }

                $invoice->week = 'Week '.date('W-Y', strtotime($invoice->dateinvoiced));
                $invoice->month = date('M, Y', strtotime($invoice->dateinvoiced));
                if(!is_array($invoicelines)) {
                    continue;
                }
                foreach($invoicelines as $invoiceline) {
                    $outputstack = $invoiceline->get_transaction()->get_outputstack();
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
                        $invoiceline->segment = $product->get_defaultchemfunction()->get_segment()->get_displayname();
                        if(empty($invoiceline->segment)) { /* Temp legacy fallback */
                            $invoiceline->segment = $product->get_segment()['title'];
                        }
                    }
                    $invoiceline->productname = $product->name;
                    if(empty($invoiceline->suppliername)) {
                        $invoiceline->suppliername = 'Unknown Supplier';
                    }

                    $invoiceline->uom = $invoiceline->get_uom()->uomsymbol;
                    $invoiceline->costlocal = $invoiceline->get_cost();

                    if(is_object($inputstack)) {
                        $input_inoutline = $inputstack->get_transcation()->get_inoutline();
                        if(is_object($input_inoutline)) {
                            $invoiceline->purchaseprice = $input_inoutline->get_invoiceline()->priceactual;
                        }
                        else {
                            $invoiceline->purchaseprice = 0;
                        }
                    }

                    $invoiceline->grossmargin = $invoiceline->linenetamt - ($invoiceline->purchaseprice * $invoiceline->qtyinvoiced);
                    $invoiceline->netmargin = $invoiceline->linenetamt - $invoiceline->costlocal;
                    $invoiceline->marginperc = numfmt_format(numfmt_create('en_EN', NumberFormatter::PERCENT), $invoiceline->netmargin / $invoiceline->linenetamt);

                    $output .= '<tr>';
                    foreach($cols as $col) {
                        $value = $invoice->{$col};
                        if(empty($value)) {
                            $value = $invoiceline->{$col};
                        }

                        $data[$invoiceline->c_invoiceline_id][$col] = $value;
                        $output .= '<td>'.$value.'</td>';
                    }

                    $output .= '</tr>';
                }
            }
            $output .= '</table>';
        }
        else {
            //redirect($url, $delay, $redirect_message);
        }

        $overwrite = array('marginperc' => array('fields' => array('divider' => 'netmargin', 'dividedby' => 'linenetamt'), 'operation' => '/'),
                'priceactual' => array('fields' => array('divider' => 'linenetamt', 'dividedby' => 'qtyinvoiced'), 'operation' => '/'));
        $formats = array('marginperc' => array('style' => NumberFormatter::PERCENT, 'pattern' => '#0.##'));

        $required_fields = array('qtyinvoiced', 'priceactual', 'linenetamt', 'purchaseprice', 'costlocal', 'grossmargin', 'netmargin', 'marginperc');
        $required_tables = array('segmentsummary' => array('segment'), 'salesrepsummary' => array('salesrep'), 'suppliersummary' => array('suppliername'), 'customerssummary' => array('customername'), 'detailed' => array('month', 'week', 'salesrep', 'suppliername', 'customername', 'productname'));

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
        eval("\$previewpage = \"".$template->get('crm_previewsalesreport')."\";");
        $previewpage .= $output;
        output_page($previewpage);
    }
}
?>