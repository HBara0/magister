<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: preview.php
 * Created:        @tony.assaad    Aug 22, 2013 | 4:17:09 PM
 * Last Update:    @tony.assaad    Aug 22, 2013 | 4:17:09 PM
 */

if(!($core->input['action'])) {
    if($core->input['referrer'] == 'generate') {
        $budgetcache = new Cache();
// $identifier = base64_decode($core->input['identifier']);
//$generate_budget_data = unserialize($session->get_phpsession('generatebudgetdata_'.$identifier));

        if(is_array($core->user['auditedaffids'])) {
            foreach($core->user['auditedaffids'] as $auditaffid) {
                $aff_obj = new Affiliates($auditaffid);
                $affiliate_users = $aff_obj->get_users();
                foreach($affiliate_users as $aff_businessmgr) {
                    $business_managers[$aff_businessmgr['uid']] = $aff_businessmgr['uid'];
                }
            }
        }
        else {
            $business_managers['managers'][] = $core->user['uid'];
        }
        $budgetsdata['current'] = ($core->input['budget']);
        $aggregate_types = array('affiliates', 'suppliers', 'managers', 'segments', 'years');
        //eval("\$budgetreport_coverpage = \"".$template->get('budgeting_budgetreport_coverpage')."\";");

        /* overrites the filters and get the user filter when no  filters are selected */
        $dummy_budget = new Budgets();
        $filters = $dummy_budget->generate_budgetline_filters();
        ///$filters = array('affiliates' => $core->user['affiliates'], 'suppliers' => $core->user['suppliers']['eid'], 'segments' => array_keys($core->user_obj->get_segments()));
        if(is_array($filters)) {
            foreach($filters as $key => $val) {
                if(empty($budgetsdata['current'][$key])) {
                    if(empty($val)) {
                        unset($budgetsdata['current'][$key]);
                        continue;
                    }
                    $budgetsdata['current'][$key] = $val;
                }
                else {
                    $budgetsdata['current'][$key] = array_intersect($val, $budgetsdata['current'][$key]);
                }
            }
        }
        $dal_config = array(
                'operators' => array('affid' => 'in', 'year' => '='),
                'simple' => false,
                'returnarray' => true
        );

        $export_identifier = base64_encode(serialize($budgetsdata['current']));
        $budgetsdata['prev2years'] = $budgetsdata['prev3years'] = $budgetsdata['current'];
        $budgetsdata['prev2years']['years'] = $budgetsdata['current']['years'] - 2;
        $budgetsdata['prev3years']['years'] = $budgetsdata['current']['years'] - 3;
        $fields = array('current', 'prev2years', 'prev3years');
        foreach($fields as $field) {
            $budgets[$field] = Budgets::get_budgets_bydata($budgetsdata[$field]);
        }
        if(!is_array($budgets['current'])) {
            redirect($_SERVER['HTTP_REFERER'], 2, $lang->nomatchfound);
        }
        $report_type = $core->input['budget']['reporttype'];
        if($report_type == 'dimensional') {
            $fields = array('current', 'prev2years', 'prev3years');
            foreach($fields as $field) {
                if(is_array($budgets[$field])) {
                    foreach($budgets[$field] as $budgetid) {
                        $budget_obj = new Budgets($budgetid);
                        /* Validate Permissions - START */
                        $filter = $budget_obj->generate_budgetline_filters();
                        if($filter === false) {
                            continue;
                        }
                        /* Validate Permissions - END */

                        if(empty($filter)) {
                            if(isset($budgetsdata[$field]['managers'])) {
                                $budgetsdata[$field]['managers'] = array_intersect($business_managers, $budgetsdata[$field]['managers']);
                            }
                            else {
                                $budgetsdata[$field]['managers'] = $business_managers;
                            }
                        }

                        if(empty($budgetsdata[$field]['managers'])) {
                            $budgetsdata[$field]['managers'][] = $core->user['uid'];
                        }
                        $budgetlines = $budget_obj->get_budgetlines_objs(array('businessMgr' => $budgetsdata[$field]['managers']), array('operators' => array('createdBy' => 'in'), 'order' => 'quantity', 'returnarray' => true));

                        if(!is_array($budgetlines)) {
                            continue;
                        }

                        foreach($budgetlines as $blid => $budgetline) {
                            $rawdata[$field][$blid] = $budget_obj->get() + $budgetline->get();
                            $product = $budgetline->get_product();
                            if(empty($rawdata[$field][$blid]['psid'])) {
                                $rawdata[$field][$blid]['psid'] = $product->get_productsegment()->psid;
                            }
                            $budget_currencies[$budgetline->blid] = $budgetline->originalCurrency;
                            /* get the currency rate of the Origin currency  of the current buudget and convert it - START */
                            if($budgetline->originalCurrency != $budgetsdata[$field]['toCurrency']) {
                                $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $budgetline->originalCurrency, 'toCurrency' => $budgetsdata[$field]['toCurrency'], 'affid' => $budget_obj->affid, 'year' => $budget_obj->year), $dal_config);
                                if(is_array($fxrates_obj)) {
                                    if($field !== 'current') {
                                        $budgetline->amount = $budgetline->actualAmount;
                                        $budgetline->income = $budgetline->actualIncome;
                                    }
                                    echo $budgetline->actualAmount;
                                    foreach($fxrates_obj as $fxid => $fxrates) {
                                        $rawdata[$field][$blid]['amount'] = ($budgetline->amount * $fxrates->rate);
                                        $rawdata[$field][$blid]['income'] = ($budgetline->income * $fxrates->rate);
                                    }
                                }
                                else {
                                    error($lang->sprint($lang->noexchangerate, $budgetline->originalCurrency, $budgetsdata['toCurrency'], $budget_obj->year), $_SERVER['HTTP_REFERER']);
                                }
                            }
                            /* get the currency rate of the Origin currency  of the current buudget - START */

                            //get the report to of the user who created the budget.

                            $rawdata[$field][$blid]['reportsTo'] = $budget_obj->get_CreateUser()->get_reportsto()->uid;
                            $rawdata[$field][$blid]['uid'] = $budgetline->businessMgr;
                            $rawdata[$field][$blid]['spid'] = $product->get_supplier()->eid;
                            $rawdata[$field][$blid]['s1Income'] = $rawdata[$blid]['income'] * ($rawdata[$blid]['s1Perc'] / 100);
                            $rawdata[$field][$blid]['s2Income'] = $rawdata[$blid]['income'] * ($rawdata[$blid]['s2Perc'] / 100);
                            if(empty($rawdata[$field][$blid]['coid'])) {
                                if(!empty($rawdata[$field][$blid]['customerCountry'])) {
                                    $rawdata[$field][$blid]['coid'] = $rawdata[$blid]['customerCountry'];
                                }
                                else {
                                    $rawdata[$field][$blid]['coid'] = $budget_obj->get_affiliate()->get_country()->coid;
                                }
                            }
                            else {
                                $rawdata[$field][$blid]['coid'] = $budget_obj->get_customer()->get_country()->coid;
                            }
                        }
                    }
                }
            }
            if(empty($rawdata['current'])) {
                error($lang->nomatchfound, $_SERVER['HTTP_REFERER']);
            }
            print_R($rawdata);
            /* Dimensional Report Settings - START */
            $dimensions = explode(',', $budgetsdata['current']['dimension'][0]); // Need to be passed from options stage
            $required_fields = array('quantity', 'amount', 'income', 'incomePerc', 's1Income', 's2Income');
            $formats = array('incomePerc' => array('style' => NumberFormatter::PERCENT, 'pattern' => '#0.##'));
            $overwrite = array('unitPrice' => array('fields' => array('divider' => 'amount', 'dividedby' => 'quantity'), 'operation' => '/'),
                    'incomePerc' => array('fields' => array('divider' => 'income', 'dividedby' => 'amount'), 'operation' => '/'));
            /* Dimensional Report Settings - END */
            $dimensionalreport = new DimentionalData();
            $dimensionalreport->set_dimensions(array_combine(range(1, count($dimensions)), array_values($dimensions)));
            $dimensionalreport->set_requiredfields($required_fields);
            if(!empty($rawdata['current'])) {
                $dimensionalreport->set_data($rawdata['current']);
            }
            $budgeting_budgetrawreport .= '<table width="100%" class="datatable">';
            $budgeting_budgetrawreport .= '<tr><th></th>';
            foreach($required_fields as $field) {
                $budgeting_budgetrawreport .= '<th>'.$field.'</th>';
            }
            $budgeting_budgetrawreport .= '</tr>';
            $budgeting_budgetrawreport .= $dimensionalreport->get_output(array('outputtype' => 'table', 'noenclosingtags' => true, 'formats' => $formats, 'overwritecalculation' => $overwrite));
            $budgeting_budgetrawreport .= '</table>';

            $fields = array('current', 'prev2years', 'prev3years');
            foreach($fields as $field) {
                if(is_array($rawdata[$field])) {
                    foreach($rawdata[$field] as $data) {
                        foreach($dimensions as $dimension) {
                            if(!empty($dimension)) {
                                $amount = 'amount';
                                $income = 'income';
                                if($field !== 'current') {
                                    $amount = 'actualAmount';
                                    $income = 'actualIncome';
                                }
                                if($dimension == 'uid') {
                                    $data[$dimension] = $data['businessMgr'];
                                }
                                $cdata[$dimension]['amount'][$data[$dimension]][$field] += $data[$amount];
                                $cdata[$dimension]['income'][$data[$dimension]][$field] += $data[$income];

                                $dimension_objs = get_object_bytype($dimension, $data[$dimension]);
                                $cdata[$dimension]['title'][$data[$dimension]] = $dimension_objs->get_displayname();
                            }
                        }
                    }
                }
            }

            foreach($cdata as $dcdata) {
                // print_R($dcdata);
                $xaxis = 'Affiliate';
                $amount_barchart = new Charts(array('x' => array_values($dcdata['title']), 'y' => array_values($dcdata['amount'])), 'bar', array('yaxisname' => 'amount', 'xaxisname' => $xaxis));
                $budgeting_budgetrawreport.='<img src='.$amount_barchart->get_chart().' />';
                $income_barchart = new Charts(array('x' => array_values($dcdata['title']), 'y' => array_values($dcdata['income'])), 'bar', array('yaxisname' => 'income', 'xaxisname' => $xaxis));
                $budgeting_budgetrawreport.='<img src='.$income_barchart->get_chart().' />';
            }
        }
        elseif($report_type == 'statistical') {
            /* Parse Risks - Start */
            $value_types = array('income', 'amount');
            $value_perc = array(50, 80);
            $value_by = array('customers' => 'cid, altCid', 'suppliers' => 'bid');
            $budgeting_budgetrawreport = '<h1>Customers/Suppliers Risks</h1>';
            foreach($value_by as $by => $group) {
                $budgeting_budgetrawreport .= '<h2>'.ucwords($by).'</h2><table width="100%" class="datatable">';
                foreach($value_types as $type) {
                    foreach($value_perc as $perc) {
                        $data = BudgetLines::get_top($perc, $type, array('bid' => array_keys($budgets['current'])), array('group' => $group, 'operators' => array('bid' => 'IN')));
                        $budgeting_budgetrawreport .= '<tr><td>'.$perc.'% '.$by.' by '.$type.'</td><td>'.$data['count'].'</td></tr>';
                    }
                }
                $budgeting_budgetrawreport .= '</table>';
            }
            /* Parse Risks - END */

            $required_fields = array('amount', 'income', 'cost');
            $budgeting_budgetrawreport .= '<hr /><h1>Country vs. Affiliate</h1><table width="100%" class="datatable">';
            $budgeting_budgetrawreport .= '<tr class="thead"><th></th>';
            foreach($required_fields as $field) {
                if(!isset($lang->{$field})) {
                    $lang->{$field} = ucwords($field);
                }
                $budgeting_budgetrawreport .= '<th>'.$lang->{$field}.'</th>';
            }
            $budgeting_budgetrawreport .= '</tr>';

            foreach($budgetsdata['current']['affiliates'] as $affid) {
                $affiliate = new Affiliates($affid);
                if($affiliate->country == 0) {
                    continue;
                }
                $country = $affiliate->get_country();
                $budgeting_budgetrawreport .= '<tr><td colspan=4 class="subtitle">'.$affiliate->name.'</td></tr>';

                $operators = array('bid' => 'IN');
                $country_row = '<tr><td style="width: 40%;">'.$lang->country.' ('.$country->get_displayname().')</td>';
                $affiliate_row = '<tr><td>'.$lang->affiliate.'</td>';


                foreach($required_fields as $field) {

                    if($field == 'cost') {
                        $values['country'][$field] = $values['country']['amount'] - $values['country']['income'];
                        $values['affiliate'][$field] = $values['affiliate']['amount'] - $values['affiliate']['income'];
                    }
                    else {
                        $values['country'][$field] = ceil(BudgetLines::get_aggregate_bycountry($country, $field, array('bid' => array_keys($budgets['current'])), array('operators' => $operators)));
                        $values['affiliate'][$field] = ceil(BudgetLines::get_aggregate_byaffiliate($affiliate, $field, array('bid' => array_keys($budgets['current'])), array('operators' => $operators)));
                    }
                    $country_row .= '<td>'.$values['country'][$field].'</td>';
                    $affiliate_row .= '<td>'.$values['affiliate'][$field].'</td>';
                    $ydata = array('amount' => array($country->get_displayname() => $values['country']['amount'], $affiliate->name => $values['affiliate']['amount']),
                            'income' => array($country->get_displayname() => $values['country']['income'], $affiliate->name => $values['affiliate']['income']),
                            'cost' => array($country->get_displayname() => $values['country']['cost'], $affiliate->name => $values['affiliate']['cost']));

                    $countryaff_chart = new Charts(array('x' => array_keys($ydata), 'y' => $ydata), 'bar', array('xaxisname' => $lang->{$field}, 'yaxisunit' => 'k$'));
                }

                $country_row .= '</tr>';
                $affiliate_row .= '</tr>';
                $chart.='<tr><td colspan="3"><img src='.$countryaff_chart->get_chart().' /><td></tr>';
                $budgeting_budgetrawreport .= $country_row.$affiliate_row.$chart;
                unset($affiliate_row, $country_row);
                $chart = '';
            }
            $budgeting_budgetrawreport .= '</table>';
        }
        else {
            if(is_array($budgets['current'])) {
                foreach($budgets['current'] as $budgetid) {
                    $budget_obj = new Budgets($budgetid);
                    $budget['country'] = $budget_obj->get_affiliate()->get()['name'];
                    $budget['affiliate'] = $budget_obj->get_affiliate()->get()['name'];

                    $budget_data = $budget_obj->get();
                    /* Validate Permissions - START */
                    $filter = $budget_obj->generate_budgetline_filters();
                    if($filter === false) {
                        continue;
                    }
                    /* Validate Permissions - END */
                    $firstbudgetline = $budget_obj->get_budgetLines(null, $filter);
                    if(is_array($firstbudgetline)) {
                        foreach($firstbudgetline as $cid => $customersdata) {
                            foreach($customersdata as $pid => $productsdata) {
                                foreach($productsdata as $saleid => $budgetline) {
                                    $rowclass = alt_row($rowclass);
                                    $budgetline_obj = new BudgetLines($budgetline['blid']);
                                    if(isset($budgetline['invoice']) && !empty($budgetline['invoice'])) {
                                        $invoicetype = InvoiceTypes::get_data(array('invoicingEntity' => $budgetline['invoice'], 'stid' => $budgetline['saleType']));

                                        if(is_object($invoicetype)) {
                                            $budgetline['invoiceentity'] = $invoicetype->get_invoiceentity();
                                        }
                                    }
                                    $budget['manager'] = $budgetline_obj->get_businessMgr()->get();
                                    $budget['managerid'] = $budgetline_obj->get_businessMgr()->get()['uid'];

                                    if(!$budgetcache->iscached('managercache', $budget['manager']['uid'])) {
                                        $budgetcache->add('managercache', $budget['manager']['displayName'], $budget['manager']['uid']);
                                    }
                                    $budget['supplier'] = $budget_obj->get_supplier()->get()['companyNameShort'];
                                    if(empty($budget['supplier'])) {
                                        $budget['supplier'] = $budget_obj->get_supplier()->get()['companyName'];
                                    }
                                    $budget['manager'] = $budgetcache->data['managercache'][$budget['manager']['uid']];

                                    $budgetline['customerCountry'] = $budgetline_obj->parse_country();

                                    $budgetline['uom'] = 'Kg';
                                    $budgetline['saleType'] = Budgets::get_saletype_byid($saleid);

//								if(isset($budgetline['genericproduct']) && !empty($budgetline['genericproduct'])) {
//									$budgetline['genericproduct'] = $budgetline_obj->get_product()->get_generic_product();
//								}

                                    /* get the currency rate of the Origin currency  of the current buudget and convert it - START */
                                    if($budgetline['originalCurrency'] != $budgetsdata['current']['toCurrency']) {
                                        $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $budgetline['originalCurrency'], 'toCurrency' => $budgetsdata['current']['toCurrency'], 'affid' => $budget['affid'], 'year' => $budget['years']), $dal_config);
                                        if(is_array($fxrates_obj)) {
                                            foreach($fxrates_obj as $fxid => $fxrates) {
                                                $budgetline['amount'] = ($budgetline['amount'] * $fxrates->rate);
                                                $budgetline['income'] = ($budgetline['income'] * $fxrates->rate);
                                            }
                                        }
                                        else {
                                            error($lang->currencynotexist.' '.$budgetline['originalCurrency'], $_SERVER['HTTP_REFERER']);
                                        }
                                    }
                                    /* get the currency rate of the Origin currency  of the current buudget - END */
                                    if(isset($budgetline['pid']) && !empty($budgetline['pid'])) {
                                        if(!empty($budgetline['psid'])) {
                                            $segment = new ProductsSegments($budgetline['psid']);
                                            $budgetline['segment'] = $segment->titleAbbr;
                                        }
                                        else {
                                            $budgetline['segment'] = $budgetline_obj->get_product()->get_segment()['titleAbbr'];
                                        }
                                    }
                                    if((empty($budgetline['cid']) && !empty($budgetline['altCid']))) {
                                        $customername = $budgetline['altCid'];
                                    }
                                    else {
                                        $budget['customerid'] = $budgetline_obj->get_customer()->get()['eid'];
                                        $budgetline['customer'] = $budgetline_obj->get_customer()->get()['companyName'];
                                        $customername = '<a href="index.php?module=profiles/entityprofile&eid='.$budget['customerid'].'" target="_blank">'.$budgetline['customer'].'</a>';
                                    }

                                    $budgetline['product'] = $budgetline_obj->get_product($budgetline['pid'])->get()['name'];
                                    eval("\$budget_report_row .= \"".$template->get('budgeting_budgetrawreport_row')."\";");
                                }
                            }
                        }
                    }
                }
                $toolgenerate = '<div align="right" title="'.$lang->generate.'" style="float:right;padding:10px;width:10px;"><a href="index.php?module=budgeting/preview&identifier='.$export_identifier.'&action=exportexcel" target="_blank"><img src="./images/icons/xls.gif"/>'.$lang->generateexcel.'</a></div>';
            }
            else {
                $budgeting_budgetrawreport = '<tr><td>'.$lang->na.'</td></tr>';
            }
            eval("\$budgeting_budgetrawreport = \"".$template->get('budgeting_budgetrawreport')."\";");
        }
    }

    eval("\$budgetingpreview = \"".$template->get('budgeting_budgetreport_preview')."\";");
    output_page($budgetingpreview);
}
elseif($core->input['action'] == 'exportexcel') {
    if(!isset($core->input['identifier'])) {
        error($lang->fillallrequirefields);
    }
    $budgetsdata['current'] = unserialize(base64_decode($core->input['identifier']));
    $budgets['current'] = Budgets::get_budgets_bydata($budgetsdata['current']);

    $headers_data = array('manager', 'customer', 'customerCountry', 'affiliate', 'supplier', 'segment', 'product', 'quantity', 'uom', 'unitPrice', 'saleType', 'amount', 'income', 's1Perc', 's2Perc');
    $counter = 1;
    if(is_array($budgets['current'])) {
        foreach($budgets['current'] as $budgetid) {
            $budget_obj = new Budgets($budgetid);
            $budget['affiliate'] = $budget_obj->get_affiliate()->get()['name'];
            $budget['supplier'] = $budget_obj->get_supplier()->get()['companyNameShort'];
            if(empty($budget['supplier'])) {
                $budget['supplier'] = $budget_obj->get_supplier()->get()['companyName'];
            }
            $budget_data = $budget_obj->get();

            /* Validate Permissions - START */
            if($core->usergroup['canViewAllSupp'] == 0 && $core->usergroup['canViewAllAff'] == 0) {
                if(is_array($core->user['auditfor'])) {
                    if(!in_array($budget_data['spid'], $core->user['auditfor'])) {
                        if(is_array($core->user['auditedaffids'])) {
                            if(!in_array($budget_data['affid'], $core->user['auditedaffids'])) {
                                if(is_array($core->user['suppliers']['affid'][$budget_data['spid']])) {
                                    if(in_array($budget_data['affid'], $core->user['suppliers']['affid'][$budget_data['spid']])) {
                                        $filter = array('filters' => array('businessMgr' => array($core->user['uid'])));
                                    }
                                    else {
                                        redirect('index.php?module=budgeting/create');
                                    }
                                }
                                else {
                                    $filter = array('filters' => array('businessMgr' => array($core->user['uid'])));
                                }
                            }
                        }
                        else {
                            $filter = array('filters' => array('businessMgr' => array($core->user['uid'])));
                        }
                    }
                }
                else {
                    $filter = array('filters' => array('businessMgr' => array($core->user['uid'])));
                }
            }
            /* Validate Permissions - END */
            $budget['year'] = $budget_obj->get()['year'];

            $firstbudgetline = $budget_obj->get_budgetLines(0, $filter);
            if(is_array($firstbudgetline)) {
                foreach($firstbudgetline as $cid => $customersdata) {
                    foreach($customersdata as $pid => $productsdata) {
                        foreach($productsdata as $saleid => $budgetline[$counter]) {
                            $budgetline_obj = new BudgetLines($budgetline[$counter]['blid']);
                            $countries = new Countries($budgetline_obj->get_customer($cid)->get()['country']);
                            $budgetline[$counter]['manager'] = $budgetline_obj->get_businessMgr()->get()['displayName'];

                            $budgetline[$counter]['customerCountry'] = $budgetline_obj->parse_country();

                            if(!empty($budgetline[$counter]['psid'])) {
                                $segment = new ProductsSegments($budgetline[$counter]['psid']);
                                $budgetline[$counter]['segment'] = $segment->title;
                            }
                            else {
                                $budgetline[$counter]['segment'] = $budgetline_obj->get_product($pid)->get_segment()['title'];
                            }
                            $dal_config = array(
                                    'operators' => array('affid' => 'in', 'year' => '='),
                                    'simple' => false,
                                    'returnarray' => true
                            );


                            $budgetline[$counter]['product'] = $budgetline_obj->get_product($pid)->get()['name'];
                            $budgetline[$counter]['uom'] = 'Kg';
                            $budgetline[$counter]['unitPrice'] = $budgetline[$counter]['unitPrice'];
                            $budgetline[$counter]['saleType'] = Budgets::get_saletype_byid($saleid);

                            /* get the currency rate of the Origin currency  of the current buudget and convert it - START */
                            $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $budgetline[$counter]['originalCurrency'], 'toCurrency' => $budgetsdata['current']['toCurrency'], 'affid' => $budget_obj->affid, 'year' => $budget_obj->year), $dal_config);
                            if(is_array($fxrates_obj)) {
                                foreach($fxrates_obj as $fxid => $fxrates) {
                                    $budgetline[$counter]['amount'] = ( $budgetline[$counter]['amount'] * $fxrates->rate);
                                    $budgetline[$counter]['income'] = ( $budgetline[$counter]['income'] * $fxrates->rate);
                                }
                            }
                            /* get the currency rate of the Origin currency  of the current buudget - END */
                            if((empty($budgetline[$counter]['cid']) && !empty($budgetline[$counter]['altCid']))) {
                                $budgetline[$counter]['customer'] = $budgetline[$counter]['altCid'];
                            }
                            else {
                                $budgetline[$counter]['customer'] = $budgetline_obj->get_customer()->get()['companyName'];
                            }

                            foreach($budgetline[$counter] as $key => $val) {
                                if(!in_array($key, $headers_data)) {
                                    unset($budgetline[$counter][$key]);
                                }
                                $budgetline[$counter] += $budget;
                                unset($budgetline[$counter]['prevbudget']);
                            }
                            $counter++;
                        }
                    }
                }
            }
        }
    }

    $budgetline_temp = $budgetline;
    unset($budgetline);
    foreach($headers_data as $val) {
        $budgetline[0][$val] = $lang->{strtolower($val)};
        foreach($budgetline_temp as $counter => $value) {
            $budgetline[$counter][$val] = $value[$val];
        }
    }
    unset($budgetline_temp);

    //unset($budgetline['bid'], $budgetline['blid'], $budgetline['pid'], $budgetline['cid'], $budgetline['incomePerc'], $budgetline['invoice'], $budgetline['createdBy'], $budgetline['modifiedBy'], $budgetline['originalCurrency'], $budgetline['prevbudget'], $budgetline['cusomtercountry']);
    $excelfile = new Excel('array', $budgetline);
}
?>
