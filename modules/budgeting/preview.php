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
        if(is_array($core->user['auditedaffids'])) {
            foreach($core->user['auditedaffids'] as $auditaffid) {
                $aff_obj = new Affiliates($auditaffid);
                $affiliate_users = $aff_obj->get_all_users();
                foreach($affiliate_users as $aff_businessmgr) {
                    $business_managers[$aff_businessmgr['uid']] = $aff_businessmgr['uid'];
                }
            }
        }
        else {
            if($core->usergroup['canViewAllEmp'] == 1) {
                $affiliate = new Affiliates($core->user['mainaffiliate']);
                $business_managers = array_keys($affiliate->get_all_users(array('displaynameonly' => true)));
            }
            else {
                $business_managers[$core->user['uid']] = $core->user['uid'];
            }
        }
        $affiliate_users = $core->user_obj->get_reportingto();
        if(is_array($affiliate_users)) {
            foreach($affiliate_users as $aff_businessmgr) {
                $business_managers[$aff_businessmgr['uid']] = $aff_businessmgr['uid'];
            }
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
        $periods = array('current', 'prev2years', 'prev3years');
        foreach($periods as $period) {
            $budgets[$period] = Budgets::get_budgets_bydata($budgetsdata[$period]);
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
                            if(empty($rawdata[$field][$blid]['pid'])) {
                                continue;
                            }
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
                                    foreach($fxrates_obj as $fxid => $fxrates) {
                                        $rawdata[$field][$blid]['amount'] = ($budgetline->amount * $fxrates->rate);
                                        $rawdata[$field][$blid]['income'] = ($budgetline->income * $fxrates->rate);
                                        $rawdata[$field][$blid]['localIncomeAmount'] = ($budgetline->localIncomeAmount * $fxrates->rate);
                                        $rawdata[$field][$blid]['invoicingEntityIncome'] = ($budgetline->invoicingEntityIncome * $fxrates->rate);
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
                            $rawdata[$field][$blid]['stid'] = $budgetline->saleType;
                            $rawdata[$field][$blid]['spid'] = $product->get_supplier()->eid;
                            $rawdata[$field][$blid]['s1Amount'] = $rawdata[$field][$blid]['amount'] * ($rawdata[$field][$blid]['s1Perc'] / 100);
                            $rawdata[$field][$blid]['s2Amount'] = $rawdata[$field][$blid]['amount'] * ($rawdata[$field][$blid]['s2Perc'] / 100);
                            $rawdata[$field][$blid]['s1Income'] = $rawdata[$field][$blid]['income'] * ($rawdata[$field][$blid]['s1Perc'] / 100);
                            $rawdata[$field][$blid]['s2Income'] = $rawdata[$field][$blid]['income'] * ($rawdata[$field][$blid]['s2Perc'] / 100);
                            $rawdata[$field][$blid]['interCompanyPurchase_output'] = $lang->na;
                            if(!empty($rawdata[$field][$blid]['customerCountry'])) {
                                $rawdata[$field][$blid]['coid'] = $rawdata[$blid]['customerCountry'];
                            }
                            else {

                                $rawdata[$field][$blid]['coid'] = $budgetline->get_customer()->get_country()->coid;
                                if(empty($rawdata[$field][$blid]['coid'])) {
                                    $rawdata[$field][$blid]['coid'] = $budget_obj->get_affiliate()->get_country()->coid;
                                }
                            }
                        }
                    }
                }
            }
            if(empty($rawdata['current'])) {
                error($lang->nomatchfound, $_SERVER['HTTP_REFERER']);
            }
            /* Dimensional Report Settings - START */
            $dimensions = explode(',', $budgetsdata['current']['dimension'][0]); // Need to be passed from options stage
            $required_fields = array('quantity', 'amount', 'income', 'incomePerc', 's1Income', 's2Income', 's1Amount', 's2Amount');
            if($core->usergroup['budgeting_canFillLocalIncome'] == 1) {
                $required_fields[] = 'localIncomeAmount';
                $required_fields[] = 'localIncomePercentage';
            }

            $formats = array(
                    'incomePerc' => array('style' => NumberFormatter::PERCENT),
                    'localIncomePercentage' => array('style' => NumberFormatter::PERCENT),
                    'localIncomeAmount' => array('style' => NumberFormatter::DECIMAL, 'pattern' => '#,##0.00'),
                    'income' => array('style' => NumberFormatter::DECIMAL, 'pattern' => '#,##0.00'),
                    'amount' => array('style' => NumberFormatter::DECIMAL, 'pattern' => '#,##0.00'),
                    's1Amount' => array('style' => NumberFormatter::DECIMAL, 'pattern' => '#,##0.00'),
                    's2Amount' => array('style' => NumberFormatter::DECIMAL, 'pattern' => '#,##0.00')
            );
            $overwrite = array('unitPrice' => array('fields' => array('divider' => 'amount', 'dividedby' => 'quantity'), 'operation' => '/'),
                    'localIncomePercentage' => array('fields' => array('divider' => 'localIncomeAmount', 'dividedby' => 'amount'), 'operation' => '/'),
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
                $field = strtolower($field);
                if(!isset($lang->{$field})) {
                    $lang->{$field} = $field;
                }
                $budgeting_budgetrawreport .= '<th>'.$lang->{$field}.'</th>';
            }
            $budgeting_budgetrawreport .= '</tr>';
            $budgeting_budgetrawreport .= $dimensionalreport->get_output(array('outputtype' => 'table', 'noenclosingtags' => true, 'formats' => $formats, 'overwritecalculation' => $overwrite));
            $budgeting_budgetrawreport .= '</table>';

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

                                if(!isset($cdata[$dimension]['amount'][$data[$dimension]])) {
                                    foreach($fields as $field_check) {
                                        $cdata[$dimension]['amount'][$data[$dimension]][$field_check] = 0;
                                        $cdata[$dimension]['income'][$data[$dimension]][$field_check] = 0;
                                        $dimension_objs = get_object_bytype($dimension, $data[$dimension]);
                                        $cdata[$dimension]['title'][$data[$dimension]] = $dimension_objs->get_displayname();
                                        if($dimension_objs instanceof Entities) {
                                            $cdata[$dimension]['title'][$data[$dimension]] = $dimension_objs->get_shortdisplayname();
                                        }
                                    }
                                }
                                $cdata[$dimension]['amount'][$data[$dimension]][$field] += $data[$amount];
                                $cdata[$dimension]['income'][$data[$dimension]][$field] += $data[$income];
                                switch($dimension) {
                                    case 'affid':
                                        $cdata[$dimension]['charttitle'] = $lang->affiliate;
                                        break;
                                    case 'spid':
                                        $cdata[$dimension]['charttitle'] = $lang->supplier;
                                        break;
                                    case 'uid':
                                        $cdata[$dimension]['charttitle'] = $lang->bm;
                                        break;
                                    case 'cid':
                                        $cdata[$dimension]['charttitle'] = $lang->customer;
                                        break;
                                    case 'pid':
                                        $cdata[$dimension]['charttitle'] = $lang->product;
                                        break;
                                    case 'coid':
                                        $cdata[$dimension]['charttitle'] = $lang->country;
                                        break;
                                    case 'psid':
                                        $cdata[$dimension]['charttitle'] = $lang->segment;
                                        break;
                                    case 'reportsTo':
                                        $cdata[$dimension]['charttitle'] = $lang->reportsto;
                                        break;
                                }
                            }
                        }
                    }
                }
            }
            foreach($cdata as $dcdata) {
                $budgeting_budgetrawreport .= '<br /> <h1>'.$lang->amountchart.' '.$lang->vs.' '.$dcdata['charttitle'].'</h1>';
                $amount_barchart = new Charts(array('x' => $dcdata['title'], 'y' => $dcdata['amount']), 'bar', array('yaxisname' => 'amount', 'xaxisname' => $dcdata['charttitle'], 'scale' => 'SCALE_START0', 'nosort' => true));
                $budgeting_budgetrawreport.='<img src='.$amount_barchart->get_chart().' />';
                $budgeting_budgetrawreport .= '<h1>'.$lang->income.' '.$lang->vs.' '.$dcdata['charttitle'].'</h1>';
                $income_barchart = new Charts(array('x' => $dcdata['title'], 'y' => $dcdata['income']), 'bar', array('yaxisname' => 'income', 'xaxisname' => $dcdata['charttitle'], 'scale' => 'SCALE_START0', 'nosort' => true));
                $budgeting_budgetrawreport.='<img src='.$income_barchart->get_chart().' />';
            }
        }

        /* ------------------------------------------------------------------------------------------------------------------------------------------------------- */
        elseif($report_type == 'statistical') {


            ///Top 10 customers//
            $budgetline = new BudgetLines();
            $budgeting_budgetrawreport .=$budgetline->parse_toptencustomers_tables($budgets['current'], $budgetsdata['current']['toCurrency']);
            ////////////////////

            /* Parse suppliers weight - START */
            $query = $db->query('SELECT DISTINCT(spid) FROM '.Tprefix.'budgeting_budgets WHERE bid IN ('.implode(',', array_keys($budgets['current'])).') GROUP BY spid');
            while($supplier = $db->fetch_assoc($query)) {
                $suppliers[$supplier['spid']] = $supplier['spid'];
            }

            $numfmt_perc = new NumberFormatter($lang->settings['locale'], NumberFormatter::PERCENT);
            $numfmt_perc->setPattern("#0.###%");
            foreach($suppliers as $spid) {
                $suppliers[$spid] = new Entities($spid);
                $weightstotals['income'][$spid] = ceil(BudgetLines::get_aggregate_bysupplier($suppliers[$spid], 'localIncomeAmount', array('bid' => array_keys($budgets['current'])), array('toCurrency' => $budgetsdata['current']['toCurrency'], 'operators' => $operators)));
                $weightstotals['amount'][$spid] = ceil(BudgetLines::get_aggregate_bysupplier($suppliers[$spid], 'amount', array('bid' => array_keys($budgets['current'])), array('toCurrency' => $budgetsdata['current']['toCurrency'], 'operators' => $operators)));
                $weightstotals['customers'][$spid] = $db->fetch_field($db->query('SELECT COUNT(DISTINCT(cid)) AS count FROM budgeting_budgets_lines WHERE bid IN (SELECT bid FROM budgeting_budgets WHERE bid IN ('.implode(',', array_keys($budgets['current'])).') AND spid='.intval($spid).')'), 'count');
            }
            $weightsgtotals['income'] = array_sum_recursive($weightstotals['income']);
            $weightsgtotals['amount'] = array_sum_recursive($weightstotals['amount']);
            arsort($weightstotals['income']);
            arsort($weightstotals['amount']);
            $count = 1;

            $budgeting_budgetrawreport .= '<h1>Top 10 Suppliers Weight (Budget)</h1>';
            $budgeting_budgetrawreport .= '<table width="100%" class="datatable">';
            $budgeting_budgetrawreport .= '<tr><th>'.$lang->company.'</th><th>'.$lang->amount.'</th><th>'.$lang->income.'</th><th># '.$lang->customer.'</tr>';
            foreach($weightstotals['income'] as $spid => $total) {
                if($count > 10) {
                    break;
                }// print_R($weightstotals);
                $budgeting_budgetrawreport .= '<tr><td>'.$suppliers[$spid]->companyName.'</td><td>'.$numfmt_perc->format($weightstotals['amount'][$spid] / $weightsgtotals['amount']).'</td><td>'.$numfmt_perc->format($total / $weightsgtotals['income']).'</td><td>'.$weightstotals['customers'][$spid].'</td></tr>';

                $count++;
            }
            $budgeting_budgetrawreport .= '</table>';
            /* Parse suppliers weight - END */
            /* Parse Risks - Start */
            $value_types = array('localIncomeAmount', 'amount');
            $value_perc = array(50, 80);
            $value_by = array('customers' => 'cid, altCid', 'suppliers' => 'spid');
            $budgeting_budgetrawreport .= '<h1>Customers/Suppliers Risks</h1>';
            foreach($value_by as $by => $group) {
                $budgeting_budgetrawreport .= '<h2>'.ucwords($by).'</h2><table width="100%" class="datatable">';
                foreach($value_types as $type) {
                    foreach($value_perc as $perc) {
                        $data = BudgetLines::get_top($perc, $type, array('bb.bid' => array_keys($budgets['current'])), array('group' => $group, 'operators' => array('bb.bid' => 'IN')));
                        $budgeting_budgetrawreport .= '<tr><td>'.$perc.'% '.$by.' by '.$type.'</td><td>'.$data['count'].'</td></tr>';
                    }
                }
                $budgeting_budgetrawreport .= '</table>';
            }
            /* Parse Risks - END */

            $required_fields = array('amount', 'localincomeamount', 'cost');
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
                        $values['country'][$field] = $values['country']['amount'] - $values['country']['localIncomeAmount'];
                        $values['affiliate'][$field] = $values['affiliate']['amount'] - $values['affiliate']['localIncomeAmount'];
                    }
                    else {
                        $values['country'][$field] = ceil(BudgetLines::get_aggregate_bycountry($country, $field, array('bid' => array_keys($budgets['current'])), array('toCurrency' => $budgetsdata['current']['toCurrency'], 'operators' => $operators)));
                        $values['affiliate'][$field] = ceil(BudgetLines::get_aggregate_byaffiliate($affiliate, $field, array('bid' => array_keys($budgets['current'])), array('toCurrency' => $budgetsdata['current']['toCurrency'], 'operators' => $operators)));
                    }
                    $country_row .= '<td>'.$values['country'][$field].'</td>';
                    $affiliate_row .= '<td>'.$values['affiliate'][$field].'</td>';
                    $ydata = array('amount' => array($country->get_displayname() => $values['country']['amount'], $affiliate->name => $values['affiliate']['amount']),
                            'localIncomeAmount' => array($country->get_displayname() => $values['country']['localIncomeAmount'], $affiliate->name => $values['affiliate']['localIncomeAmount']),
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

        /* ------------------------------------------------------------------------------------------------------------------------------------------------------- */
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
                    //$firstbudgetline = $budget_obj->get_budgetLines(null, $filter);

                    if(empty($filter)) {
                        if(isset($budgetsdata['current']['managers'])) {
                            $budgetsdata['current']['managers'] = array_intersect($business_managers, $budgetsdata['current']['managers']);
                        }
                        else {
                            $budgetsdata['current']['managers'] = $business_managers;
                        }
                    }

                    if(empty($budgetsdata['current']['managers'])) {
                        $budgetsdata['current']['managers'][] = $core->user['uid'];
                    }
//$field
                    if(is_array($budgetsdata['current']['managers'])) {
                        $budgetlines_filters = array('businessMgr' => $budgetsdata['current']['managers']);
                    }
                    if(is_array($budgetsdata['current']['segments'])) {
                        $budgetlines_filters = array('psid' => $budgetsdata['current']['segments']);
                    }
                    $budgetlines = $budget_obj->get_lines($budgetlines_filters);
                    if(is_array($budgetlines)) {
                        //foreach($firstbudgetline as $cid => $customersdata) {
                        //foreach($customersdata as $pid => $productsdata) {
                        foreach($budgetlines as $blid => $budgetline_obj) {
                            $rowclass = alt_row($rowclass);
                            //$budgetline_obj = new BudgetLines($budgetline['blid']);
                            $budgetline = $budgetline_obj->get();
                            if(isset($budgetline['invoice']) && !empty($budgetline['invoice'])) {
                                $invoicetype = SaleTypesInvoicing::get_data(array('affid' => $budget_obj->affid, 'invoicingEntity' => $budgetline['invoice'], 'stid' => $budgetline['saleType']));

                                if(is_object($invoicetype)) {
                                    $budgetline['invoiceentity'] = $invoicetype->get_invoiceentity();
                                }
                            }
                            if(!empty($budgetline['purchasingEntityId'])) {
                                if($budgetline['purchasingEntity'] != 'customer') {
                                    $purchasingentity = new Affiliates($budgetline['purchasingEntityId']);
                                    $budgetline['purchasingEntity'] = $purchasingentity->name;
                                }
                            }
                            $budget['manager'] = $budgetline_obj->get_businessMgr()->get();
                            $budget['managerid'] = $budgetline_obj->get_businessMgr()->get()['uid'];
                            /* if empty localIncomeAmount by default */

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
                            $budgetline['saleType'] = Budgets::get_saletype_byid($budgetline['saleType']);

//								if(isset($budgetline['genericproduct']) && !empty($budgetline['genericproduct'])) {
//									$budgetline['genericproduct'] = $budgetline_obj->get_product()->get_generic_product();
//								}

                            /* get the currency rate of the Origin currency  of the current buudget and convert it - START */
                            if($budgetline['originalCurrency'] != $budgetsdata['current']['toCurrency']) {
                                $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $budgetline['originalCurrency'], 'toCurrency' => $budgetsdata['current']['toCurrency'], 'affid' => $budgetsdata['current']['affiliates'], 'year' => $budgetsdata['current']['years'], 'isBudget' => 1), $dal_config);
                                if(is_array($fxrates_obj)) {
                                    foreach($fxrates_obj as $fxid => $fxrates) {
                                        $budgetline['amount'] = ($budgetline['amount'] * $fxrates->rate);
                                        $budgetline['income'] = ($budgetline['income'] * $fxrates->rate);
                                        $budgetline['localIncomeAmount'] = ($budgetline['localIncomeAmount'] * $fxrates->rate);
                                        $budgetline['invoicingEntityIncome'] = ($budgetline['invoicingEntityIncome'] * $fxrates->rate);
                                    }
                                }
                                else {
                                    error($lang->currencynotexist.' '.$budgetline['originalCurrency'].' ('.$budget['affiliate'].')', $_SERVER['HTTP_REFERER']);
                                }
                            }
                            if($core->usergroup['budgeting_canFillLocalIncome'] == 1) {
                                $localincome_cell = '<td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="right" class="border_left">'.$budgetline['localIncomeAmount'].'</td>';
                                $localincome_cell = '<td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="right" class="border_left">'.$budgetline['invoicingEntityIncome'].'</td>';
                            }
                            else {
                                unset($localincome_cell, $budgetline['localIncomeAmount'], $budgetline['localIncomePercentage'], $budgetline['invoicingEntityIncome']);
                            }

                            /* get the currency rate of the Origin currency  of the current buudget - END */
                            if(isset($budgetline['pid']) && !empty($budgetline['pid'])) {
                                if(!empty($budgetline['psid'])) {
                                    $segment = new ProductsSegments($budgetline['psid']);
                                    $budgetline['segment'] = $segment->titleAbbr;
                                }
                                else {
                                    // $budgetline['segment'] = $budgetline_obj->get_product()->get_segment()['titleAbbr'];
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
                            $budgetline['interCompanyPurchase_output'] = $lang->na;

                            $budgetline['product'] = $budgetline_obj->get_product()->name;
                            eval("\$budget_report_row .= \"".$template->get('budgeting_budgetrawreport_row')."\";");
                        }
                    }
                    // }
                    //}
                }
                $toolgenerate = '<div align="right" title="'.$lang->generate.'" style="float:right;padding:10px;width:10px;"><a href="index.php?module=budgeting/preview&identifier='.$export_identifier.'&action=exportexcel" target="_blank"><img src="./images/icons/xls.gif"/>'.$lang->generateexcel.'</a></div>';
            }
            else {
                $budgeting_budgetrawreport = '<tr><td>'.$lang->na.'</td></tr>';
            }

            if($core->usergroup['budgeting_canFillLocalIncome'] == 1) {
                $loalincome_header = '<th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">'.$lang->localincome.'</th>';
                $loalincome_header = '<th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">'.$lang->remainingcommaff.'</th>';
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

    $headers_data = array('blid', 'manager', 'customer', 'customerCountry', 'affiliate', 'supplier', 'segment', 'product', 'quantity', 'uom', 'unitPrice', 'saleType', 'amount', 'income', 's1Perc', 's2Perc', 'purchasingEntity', 'commissionSplitAffName');
    if($core->usergroup['budgeting_canFillLocalIncome'] == 1) {
        $headers_data[] = 'localIncomeAmount';
        $headers_data[] = 'invoicingEntityIncome';
    }
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

            // $firstbudgetline = $budget_obj->get_budgetLines(0, $filter);
            if(!empty($filter['filters']['businessMgr'])) {
                $budgetline_filter['businessMgr'] = $filter['filters']['businessMgr'];
            }
            $budgetlines = $budget_obj->get_lines($budgetline_filter);
            if(is_array($budgetlines)) {
                // foreach($firstbudgetline as $cid => $customersdata) {
                //  foreach($customersdata as $pid => $productsdata) {
                foreach($budgetlines as $blid => $budgetline_obj) {
                    //$budgetline_obj = new BudgetLines($budgetline[$counter]['blid']);

                    $budgetline[$counter] = $budgetline_obj->get();
                    $countries = new Countries($budgetline_obj->get_customer()->get()['country']);
                    $budgetline[$counter]['manager'] = $budgetline_obj->get_businessMgr()->get()['displayName'];

                    $budgetline[$counter]['customerCountry'] = $budgetline_obj->parse_country();

                    if(!empty($budgetline[$counter]['psid'])) {
                        $segment = new ProductsSegments($budgetline[$counter]['psid']);
                        $budgetline[$counter]['segment'] = $segment->title;
                    }
                    else {
                        $budgetline[$counter]['segment'] = $budgetline_obj->get_product()->get_segment()['title'];
                    }
                    $dal_config = array(
                            'operators' => array('affid' => 'in', 'year' => '='),
                            'simple' => false,
                            'returnarray' => true
                    );

                    $budgetline[$counter]['product'] = $budgetline_obj->get_product()->get()['name'];
                    $budgetline[$counter]['uom'] = 'Kg';
                    if(isset($budgetline[$counter]['invoice']) && !empty($budgetline[$counter]['invoice'])) {
                        $invoicetype = SaleTypesInvoicing::get_data(array('affid' => $budget_obj->affid, 'invoicingEntity' => $budgetline[$counter]['invoice'], 'stid' => $budgetline[$counter]['saleType']));

                        if(is_object($invoicetype)) {
                            $budgetline[$counter]['invoiceentity'] = $invoicetype->get_invoiceentity();
                        }
                    }

                    if(!empty($budgetline[$counter]['purchasingEntityId'])) {
                        if($budgetline[$counter]['purchasingEntity'] != 'customer') {
                            $purchasingentity = new Affiliates($budgetline[$counter]['purchasingEntityId']);
                            $budgetline[$counter]['purchasingEntity'] = $purchasingentity->name;
                        }
                    }

                    if(!empty($budgetline[$counter]['commissionSplitAffid'])) {
                        $commissionsplitaff = new Affiliates($budgetline[$counter]['commissionSplitAffid']);
                        $budgetline[$counter]['commissionSplitAffName'] = $commissionsplitaff->name;
                    }
                    $budgetline[$counter]['saleType'] = Budgets::get_saletype_byid($budgetline[$counter]['saleType']);
                    /* get the currency rate of the Origin currency  of the current buudget and convert it - START */
                    $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $budgetline[$counter]['originalCurrency'], 'toCurrency' => $budgetsdata['current']['toCurrency'], 'affid' => $budget_obj->affid, 'year' => $budget_obj->year, 'isBudget' => 1), $dal_config);
                    if(is_array($fxrates_obj)) {
                        foreach($fxrates_obj as $fxid => $fxrates) {
                            $budgetline[$counter]['unitPrice'] = ($budgetline[$counter]['unitPrice'] * $fxrates->rate);
                            $budgetline[$counter]['amount'] = ($budgetline[$counter]['amount'] * $fxrates->rate);
                            $budgetline[$counter]['income'] = ($budgetline[$counter]['income'] * $fxrates->rate);
                            $budgetline[$counter]['localIncomeAmount'] = ($budgetline[$counter]['localIncomeAmount'] * $fxrates->rate);
                            $budgetline[$counter]['invoicingEntityIncome'] = ($budgetline[$counter]['invoicingEntityIncome'] * $fxrates->rate);
                        }
                    }
                    /* set permission for local income */
                    if($core->usergroup['budgeting_canFillLocalIncome'] != 1) {
                        unset($budgetline[$counter]['localIncomeAmount'], $budgetline[$counter]['localIncomePercentage'], $budgetline[$counter]['invoicingEntityIncome']);
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
                //  }
                // }
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
