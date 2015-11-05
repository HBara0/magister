<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: generateyearendforecast.php
 * Created:        @rasha.aboushakra    Sep 10, 2015 | 9:48:53 PM
 * Last Update:    @rasha.aboushakra    Sep 10, 2015 | 9:48:53 PM
 */

if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canUseBudgeting'] == 0) {
    error($lang->sectionnopermission);
}

$session->start_phpsession();

if(!$core->input['action']) {
    $identifier = base64_decode($core->input['identifier']);
    $budget_data = unserialize($session->get_phpsession('budgetmetadata_'.$identifier));
    $user_obj = new Users($core->user['uid']);
    $permissions = $user_obj->get_businesspermissions();

    $affiliate_where = 'isActive =1';
    if(is_array($permissions['affid'])) {
        $affiliate_where .= " AND affid IN (".implode(',', $permissions['affid']).")";
    }
    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, $affiliate_where);

    if(is_array($affiliates)) {
        foreach($affiliates as $key => $value) {
            if($key == 0) {
                continue;
            }
            $checked = $rowclass = '';
            $affiliates_list .='<tr class="'.$rowclass.'">';
            $affiliates_list .='<td><input id="affiliatefilter_check_'.$key.'" name="budget[affid][]"  type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td></tr>';
        }
    }

    $supplier_where = 'type = "s"';
    if(is_array($permissions['spid'])) {
        $supplier_where = "  eid IN (".implode(',', $permissions['spid']).")";
    }
    $suppliers = get_specificdata('entities', array('eid', 'companyName'), 'eid', 'companyName', array('by' => 'companyName', 'sort' => 'ASC'), 1, $supplier_where);

    if(is_array($suppliers)) {
        foreach($suppliers as $key => $value) {
            if($key == 0) {
                continue;
            }
            $checked = $rowclass = '';
            $suppliers_list .= ' <tr class="'.$rowclass.'">';
            $suppliers_list .= '<td><input id="supplierfilter_check_'.$key.'" name="budget[spid][]" type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td><tr>';
        }
    }

    $user = new Users($core->user['uid']);
    $user_segments_objs = $user->get_segments();
    if(is_array($user_segments_objs)) {
        foreach($user_segments_objs as $user_segments_obj) {
            $user_segments[$user_segments_obj->get()['psid']] = $user_segments_obj->get();
        }
    }
    if(is_array($user_segments)) {
        foreach($user_segments as $segment) {
            $checked = $rowclass = '';
            $budget_segments_list .='<tr class="'.$rowclass.'">';
            $budget_segments_list .='<td><input id="segmentfilter_check_'.$segment['psid'].'"  name="budget[psid][]" type="checkbox"'.$checked.' value="'.$segment['psid'].'">'.$segment['title'].'</td></tr>';
        }
    }
    else {
        $budget_segment.=$lang->na;
    }
    $years = Budgets::get_availableyears();
    if(is_array($years)) {
        foreach($years as $key => $value) {
            $checked = $rowclass = '';
            $budget_year_list .= '<tr class="'.$rowclass.'">';
            $budget_year_list .= '<td><input name="budget[years]"  required="required" type="radio" value="'.$key.'">'.$value.'</td></tr>';
        }
    }


    $users_where = 'gid != 7';
    if($core->usergroup['canViewAllEmp'] == 0 && is_array($permissions['uid'])) {
        $users_where .= ' AND uid IN ('.implode(',', $permissions['uid']).')';
    }
    $bmanagers = get_specificdata('users', array('uid', 'displayName'), 'uid', 'displayName', array('by' => 'displayName', 'sort' => 'ASC'), 0, $users_where);
    if(is_array($bmanagers)) {
        foreach($bmanagers as $key => $value) {
            $checked = $rowclass = '';
            $business_managerslist .= '<tr class="'.$rowclass.'">';
            $business_managerslist .= '<td><input id="bmfilter_check_'.$key.'" name="budget[uid][]" type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td></tr>';
        }
    }

    $currency['filter']['numCode'] = 'SELECT mainCurrency FROM '.Tprefix.'countries WHERE affid IS NOT NULL';
    $curr_objs = Currencies::get_data($currency['filter'], array('returnarray' => true, 'operators' => array('numCode' => 'IN')));
    $curr_objs[840] = new Currencies(840);
    $currencies_list = parse_selectlist('budget[toCurrency]', 7, $curr_objs, 840);

    $dimensions = array('affid' => $lang->affiliate, 'spid' => $lang->supplier, 'cid' => $lang->customer, 'reportsTo' => $lang->reportsto, 'pid' => $lang->product, 'coid' => $lang->country, 'uid' => $lang->manager, 'psid' => $lang->segment, 'stid' => $lang->saletype);

    foreach($dimensions as $dimensionid => $dimension) {
        $dimension_item.='<li class = "ui-state-default" id = '.$dimensionid.' title = "Click and Hold to move the '.$dimension.'">'.$dimension.'</li>';
    }

    eval("\$budgetgenerate = \"".$template->get('budgeting_generateyef')."\";");
    output_page($budgetgenerate);
}
else {
    if($core->input['action'] == 'do_perform_generateyearendforecast') {
        $budgetcache = new Cache();
        $user_obj = new Users($core->user['uid']);
        $permissions = $user_obj->get_businesspermissions();
        $budgetsdata['current'] = ($core->input['budget']);
        $matchfields = array('affid' => 'affiliates', 'psid' => 'segments', 'spid' => 'suppliers', 'uid' => 'managers');
        foreach($matchfields as $key => $val) {
            $budgetsdata['current'][$val] = $budgetsdata['current'][$key];
        }
        if(is_array($permissions)) {
            foreach($permissions as $key => $val) {
                if(!isset($matchfields[$key])) {
                    continue;
                }
                if(is_array($val)) {
                    if(empty($budgetsdata['current'][$key])) {
                        if(empty($val)) {
                            unset($budgetsdata['current'][$key]);
                            continue;
                        }
                        $budgetsdata['current'][$matchfields[$key]] = $val;
                    }
                    else {
                        $budgetsdata['current'][$matchfields[$key]] = array_intersect($budgetsdata['current'][$key], $val);
                    }
                    $budgetsdata['current'][$matchfields[$key]] = array_filter($budgetsdata['current'][$matchfields[$key]]);
                    $budgetsdata['current'][$key] = $budgetsdata['current'][$matchfields[$key]];
                }
            }
        }
        $dal_config = array(
                'operators' => array('affid' => 'in', 'year' => '='),
                'simple' => false,
                'returnarray' => true
        );
        $export_identifier = base64_encode(serialize($budgetsdata['current']));
        $periods = array('current'); //, 'prev2years', 'prev3years');
        foreach($periods as $period) {
            $budgetfilter[$period] = $budgetsdata[$period];
            if(is_array($budgetsdata[$period][$matchfields['affid']]) && !is_empty(array_filter($budgetsdata[$period][$matchfields['affid']]))) {
                $budgetfilter[$period]['affiliates'] = $budgetsdata[$period][$matchfields['affid']];
            }
            else if($core->usergroup['canViewAllAff'] == 0) {
                $budgetfilter[$period]['affiliates'] = array(0);
            }

            if(is_array($budgetsdata[$period][$matchfields['spid']]) && !is_empty(array_filter($budgetsdata[$period][$matchfields['spid']]))) {
                $budgetfilter[$period]['suppliers'] = $budgetsdata[$period][$matchfields['spid']];
            }
            else if($core->usergroup['canViewAllSupp'] == 0) {
                $budgetfilter[$period]['suppliers'] = array(0);
            }
            $budgets[$period] = BudgetingYearEndForecast::get_yefs_bydata($budgetfilter[$period]);
        }
        if(!is_array($budgets['current'])) {
            output_xml('<status>false</status><message><![CDATA['.$lang->nomatchfound.']]></message>');
            exit;
        }
        $report_type = $core->input['budget']['reporttype'];
        $countrows = 0;
        if($report_type == 'dimensional') {
            if(is_array($budgets['current'])) {
                foreach($budgets['current'] as $budgetid) {
                    $budget_obj = new BudgetingYearEndForecast($budgetid);
                    $budget_data = $budget_obj->get();
                    if(isset($budgetsdata['current'][$matchfields['uid']]) && !is_empty(array_filter($budgetsdata['current'][$matchfields['uid']]))) {
                        $budgetlines_filters['businessMgr'] = array_filter($budgetsdata['current'][$matchfields['uid']]);
                    }
                    elseif($core->usergroup['canViewAllEmp'] == 0) {
                        $budgetlines_filters['businessMgr'][] = $core->user['uid'];
                    }
                    if(isset($budgetsdata['current'][$matchfields['psid']]) && !is_empty(array_filter($budgetsdata['current'][$matchfields['psid']]))) {
                        $budgetlines_filters['psid'] = array_filter($budgetsdata['current'][$matchfields['psid']]);
                    }
                    elseif($core->usergroup['canViewAllSupp'] == 0) {
                        $budgetlines_filters['psid'] = array(0);
                    }

                    $budgetlines = $budget_obj->get_yeflines_objs($budgetlines_filters, array('operators' => array('createdBy' => 'in'), 'order' => 'quantity', 'returnarray' => true));
                    unset($budgetlines_filters);
                    if(!is_array($budgetlines)) {
                        continue;
                    }

                    foreach($budgetlines as $yeflid => $budgetline) {
                        $rawdata['current'][$yeflid] = $budget_obj->get() + $budgetline->get();
                        $product = $budgetline->get_product();
                        if(empty($rawdata['current'][$yeflid]['pid'])) {
                            continue;
                        }
                        if(empty($rawdata['current'][$yeflid]['psid'])) {
                            $rawdata['current'][$yeflid]['psid'] = $product->get_productsegment()->psid;
                        }
                        $budget_currencies[$budgetline->blid] = $budgetline->originalCurrency;
                        /* get the currency rate of the Origin currency  of the current buudget and convert it - START */
                        if($budgetline->originalCurrency != $budgetsdata['current']['toCurrency']) {
                            $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $budgetline->originalCurrency, 'toCurrency' => $budgetsdata['current']['toCurrency'], 'affid' => $budget_obj->affid, 'year' => $budget_obj->year, 'isYef' => 1), $dal_config);
                            if(is_array($fxrates_obj)) {
                                if('current' !== 'current') {
                                    $budgetline->amount = $budgetline->actualAmount;
                                    $budgetline->income = $budgetline->actualIncome;
                                }
                                foreach($fxrates_obj as $fxid => $fxrates) {
                                    $rawdata['current'][$yeflid]['amount'] = ($budgetline->amount * $fxrates->rate);
                                    $rawdata['current'][$yeflid]['income'] = ($budgetline->income * $fxrates->rate);
                                    $rawdata['current'][$yeflid]['localIncomeAmount'] = ($budgetline->localIncomeAmount * $fxrates->rate);
                                    $rawdata['current'][$yeflid]['invoicingEntityIncome'] = ($budgetline->invoicingEntityIncome * $fxrates->rate);
                                }
                            }
                            else {
                                $currency = new Currencies($budgetline->originalCurrency);
                                $currency_output = $budgetline->originalCurrency;
                                if(is_object($currency)) {
                                    $currency_output = $currency->get_displayname();
                                }
                                output_xml('<status>false</status><message>'.$lang->sprint($lang->noexchangerate, $currency_output, $currency_output, $budget_obj->year).'</message>');
                                exit;
                            }
                        }
                        /* get the currency rate of the Origin currency  of the current buudget - START */

//get the report to of the user who created the budget.
                        $rawdata['current'][$yeflid]['reportsTo'] = $budget_obj->get_CreateUser()->get_reportsto()->uid;
                        $rawdata['current'][$yeflid]['uid'] = $budgetline->businessMgr;
                        $rawdata['current'][$yeflid]['stid'] = $budgetline->saleType;
                        $rawdata['current'][$yeflid]['spid'] = $product->get_supplier()->eid;
                        $rawdata['current'][$yeflid]['octoberAmount'] = $rawdata['current'][$yeflid]['amount'] * ($rawdata['current'][$yeflid]['october'] / 100);
                        $rawdata['current'][$yeflid]['novemberAmount'] = $rawdata['current'][$yeflid]['amount'] * ($rawdata['current'][$yeflid]['november'] / 100);
                        $rawdata['current'][$yeflid]['decemberAmount'] = $rawdata['current'][$yeflid]['amount'] * ($rawdata['current'][$yeflid]['december'] / 100);

                        $rawdata['current'][$yeflid]['octoberIncome'] = $rawdata['current'][$yeflid]['income'] * ($rawdata['current'][$yeflid]['october'] / 100);
                        $rawdata['current'][$yeflid]['novemberIncome'] = $rawdata['current'][$yeflid]['income'] * ($rawdata['current'][$yeflid]['november'] / 100);
                        $rawdata['current'][$yeflid]['decemberIncome'] = $rawdata['current'][$yeflid]['income'] * ($rawdata['current'][$yeflid]['december'] / 100);

                        $rawdata['current'][$yeflid]['interCompanyPurchase_output'] = $lang->na;
                        if(!empty($rawdata['current'][$yeflid]['customerCountry'])) {
                            $rawdata['current'][$yeflid]['coid'] = $rawdata[$yeflid]['customerCountry'];
                        }
                        else {

                            $rawdata['current'][$yeflid]['coid'] = $budgetline->get_customer()->get_country()->coid;
                            if(empty($rawdata['current'][$yeflid]['coid'])) {
                                $rawdata['current'][$yeflid]['coid'] = $budget_obj->get_affiliate()->get_country()->coid;
                            }
                        }
                    }
                }
            }

            if(empty($rawdata['current'])) {
                output_xml('<status>false</status><message><![CDATA['.$lang->nomatchfound.']]></message>');
                exit;
            }
            /* Dimensional Report Settings - START */
            $dimensions = explode(',', $budgetsdata['current']['dimension'][0]); // Need to be passed from options stage
            $required_fields = array('quantity', 'amount', 'income', 'incomePerc', 'octoberIncome', 'novemberIncome', 'decemberIncome', 'octoberAmount', 'novemberAmount', 'decemberAmount');
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
                    'octoberAmount' => array('style' => NumberFormatter::DECIMAL, 'pattern' => '#,##0.00'),
                    'novemberAmount' => array('style' => NumberFormatter::DECIMAL, 'pattern' => '#,##0.00'),
                    'decemberAmount' => array('style' => NumberFormatter::DECIMAL, 'pattern' => '#,##0.00')
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
        }
        else {
            if(is_array($budgets['current'])) {
                foreach($budgets['current'] as $budgetid) {
                    $budget_obj = new BudgetingYearEndForecast($budgetid);
                    $budget['country'] = $budget_obj->get_affiliate()->get_country()->get()['name'];
                    $budget['affiliate'] = $budget_obj->get_affiliate()->get()['name'];

                    $budget_data = $budget_obj->get();
                    if(isset($budgetsdata['current'][$matchfields['uid']]) && !is_empty(array_filter($budgetsdata['current'][$matchfields['uid']]))) {
                        $budgetlines_filters['businessMgr'] = array_filter($budgetsdata['current'][$matchfields['uid']]);
                    }
                    elseif($core->usergroup['canViewAllEmp'] == 0) {
                        $budgetlines_filters['businessMgr'][] = $core->user['uid'];
                    }
                    if(isset($budgetsdata['current'][$matchfields['psid']]) && !is_empty(array_filter($budgetsdata['current'][$matchfields['psid']]))) {
                        $budgetlines_filters['psid'] = array_filter($budgetsdata['current'][$matchfields['psid']]);
                    }
                    elseif($core->usergroup['canViewAllSupp'] == 0) {
                        $budgetlines_filters['psid'] = array(0);
                    }
                    $budgetlines = $budget_obj->get_lines($budgetlines_filters);
                    if(is_array($budgetlines)) {
//foreach($firstbudgetline as $cid => $customersdata) {
//foreach($customersdata as $pid => $productsdata) {
                        foreach($budgetlines as $yeflid => $budgetline_obj) {
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
                                $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $budgetline['originalCurrency'], 'toCurrency' => $budgetsdata['current']['toCurrency'], 'affid' => $budgetsdata['current']['affid'], 'year' => $budgetsdata['current']['years'], 'isYef' => 1), $dal_config);
                                if(is_array($fxrates_obj)) {
                                    foreach($fxrates_obj as $fxid => $fxrates) {
                                        $budgetline['amount'] = ($budgetline['amount'] * $fxrates->rate);
                                        $budgetline['income'] = ($budgetline['income'] * $fxrates->rate);
                                        $budgetline['unitPrice'] = ($budgetline['unitPrice'] * $fxrates->rate);
                                        $budgetline['localIncomeAmount'] = ($budgetline['localIncomeAmount'] * $fxrates->rate);
                                        $budgetline['invoicingEntityIncome'] = ($budgetline['invoicingEntityIncome'] * $fxrates->rate);
                                    }
                                }
                                else {
                                    $currency = new Currencies($budgetline['originalCurrency']);
                                    $currency_output = $budgetline->originalCurrency;
                                    if(is_object($currency)) {
                                        $currency_output = $currency->get_displayname();
                                    }
                                    output_xml('<status>false</status><message>'.$lang->sprint($lang->currencynotexistvar, $currency_output).' (YEF - '.$budget['affiliate'].')</message>');
                                    exit;
                                }
                            }
                            if($core->usergroup['budgeting_canFillLocalIncome'] == 1) {
                                $localincome_cell = '<td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="right" class="border_left">'.$budgetline['localIncomeAmount'].'</td>';
                                $localincome_cell .= '<td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="right" class="border_left">'.$budgetline['invoicingEntityIncome'].'</td>';
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
                            $budgetline['interCompanyPurchase_output'] = $lang->na;
                            $budgetline['product'] = $budgetline_obj->get_product()->name;
                            $monthfields = array('october', 'november', 'december');
                            foreach($monthfields as $month) {
                                $budgetline[$month.'qty'] = round($budgetline['quantity'] * ($budgetline[$month] / 100), 2);
                                $budgetline[$month.'amt'] = round($budgetline['amount'] * ($budgetline[$month] / 100), 2);
                                $budgetline[$month.'inc'] = round($budgetline['income'] * ($budgetline[$month] / 100), 2);
                                $total[$month.'amt'] += $budgetline[$month.'amt'];
                                $total[$month.'inc'] += $budgetline[$month.'inc'];
                            }
                            $total['amount'] += $budgetline['amount'];
                            $total['unitPrice'] += $budgetline['unitPrice'];
                            $total['income'] += $budgetline['income'];
                            $countrows++;
                            eval("\$budget_report_row .= \"".$template->get('budgeting_yefrawreport_row')."\";");
                        }
                    }
                }
//href="index.php?module=budgeting/generateyearendforecast&identifier='.$export_identifier.'&action=exportexcel" target="_blank"
                $onclickactin = "$('#tabletoexport').tableExport({type:'excel',escape:'false'});";
                $toolgenerate = '<div align="right" title="'.$lang->generate.'" style="float:right;padding:10px;width:10px;"><a onClick ="'.$onclickactin.'"><img src="./images/icons/xls.gif"/>'.$lang->generateexcel.'</a></div>';
            }
            else {
                $budgeting_budgetrawreport = '<tr><td>'.$lang->na.'</td></tr>';
            }

            if($core->usergroup['budgeting_canFillLocalIncome'] == 1) {
                $loalincome_header = '<th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">'.$lang->localincome.'</th>';
                $loalincome_header .= '<th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">'.$lang->remainingcommaff.'</th>';
            }
            if(is_array($total) && !empty($total)) {
                unset($budgetline, $budget, $customername);
                $rowclass = 'thead';
                $budget['managerid'] = '#';
                $budget['manager'] = 'TOTAL';
                $customername = '';
                if(!empty($countrows)) {
                    $budgetline['unitPrice'] = 'Avg '.number_format($total['unitPrice'] / $countrows, 2);
                }
                $monthfields = array('october', 'november', 'december');
                foreach($monthfields as $month) {
                    $budgetline[$month.'amt'] = $total[$month.'amt'];
                    $budgetline[$month.'inc'] = $budgetline[$month.'inc'];
                }
                $budgetline['amount'] = number_format($total['amount']);
                $budgetline['income'] = number_format($total['income']);
                eval("\$totals_row = \"".$template->get('budgeting_yefrawreport_row')."\";");
            }
            eval("\$budgeting_budgetrawreport = \"".$template->get('budgeting_yefrawreport')."\";");
        }


        eval("\$budgetingpreview = \"".$template->get('budgeting_yefpreview')."\";");

        output_xml('<status></status><message><![CDATA['.$budgetingpreview.']]></message>');
    }
}
?>
