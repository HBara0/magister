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
    $affiliate_where = ' name LIKE "%orkila%"';
    //if user is coordinator append more options
    $segmentscoords = ProdSegCoordinators::get_data(array('uid' => $core->user['uid']), array('returnarray' => true));
    if(is_array($segmentscoords)) {
        $psids = array();
        $affids = array();
        $spids = array();
        foreach($segmentscoords as $segmentscoord) {
            if(in_array($segmentscoord->psid, $psids)) {
                continue;
            }
            $psids[] = $segmentscoord->psid;
        }
        if(is_array($psids)) {
            $entitysegments = EntitiesSegments::get_data('psid IN ('.implode(',', $psids).') AND eid IN (SELECT eid FROM entities WHERE type = "s" ) ', array('operators' => array('filter' => 'CUSTOMSQLSECURE'), 'returnarray' => true));
            if(is_array($entitysegments)) {
                foreach($entitysegments as $entitysegment) {
                    $entity = new Entities($entitysegment->eid);
                    if(!in_array($entity->eid, $spids)) {
                        $spids[] = $entity->eid;
                    }
                    $affiliatedsegs = AffiliatedEntities::get_column('affid', array('eid' => $entitysegment->eid), array('returnarray' => true));
                    if(is_array($affiliatedsegs)) {
                        foreach($affiliatedsegs as $affiliatedseg) {
                            if(!in_array($affiliatedseg, $affids)) {
                                $affids[] = $affiliatedseg;
                            }
                        }
                    }
                }
            }
            $employeesegments_usersids = EmployeeSegments::get_data('psid IN ('.implode(',', $psids).') AND uid IN (SELECT uid FROM '.Tprefix.'users WHERE gid != 7) ', array('returnarray' => true));
            if(is_array($employeesegments_usersids)) {
                foreach($employeesegments_usersids as $user) {
                    $segmentusers[$user->uid] = $user->get_displayname();
                }
            }
        }
    }
    if($core->usergroup['canViewAllAff'] == 0) {
        if(is_array($affids)) {
            $core->user['affiliates'] = array_unique(array_merge($core->user['affiliates'], $affids));
        }
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliate_where .= " AND affid IN ({$inaffiliates})";
    }


    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, "{$affiliate_where}");
    //$affiliated_budget = parse_selectlist('budget[affiliates][]', 1, $affiliates, $core->user['mainaffiliate'], 1, '', array('id' => 'affid'));
    if(is_array($affiliates)) {

        foreach($affiliates as $key => $value) {
            if($key == 0) {
                continue;
            }
            $checked = $rowclass = '';
            $affiliates_list .='<tr class="'.$rowclass.'">';
            $affiliates_list .='<td><input id="affiliatefilter_check_'.$key.'" name="budget[affiliates][]"  type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td></tr>';
        }
    }


    if($core->usergroup['canViewAllSupp'] == 0) {
        if(is_array($spids)) {
            $core->user['suppliers']['eid'] = array_unique(array_merge($core->user['suppliers']['eid'], $spids));
        }
        $insupplier = implode(',', $core->user['suppliers']['eid']);
        $supplier_where = " eid IN ({$insupplier})";
    }
    else {
        $supplier_where = " type='s'";
    }
    $suppliers = get_specificdata('entities', array('eid', 'companyName'), 'eid', 'companyName', array('by' => 'companyName', 'sort' => 'ASC'), 1, "{$supplier_where}");
    // $budget_supplierslist = parse_selectlist('budget[suppliers][]', 2, $suppliers, $core->user['suppliers']['eid'], 1, '', array('id' => 'spid'));

    if(is_array($suppliers)) {
        foreach($suppliers as $key => $value) {
            if($key == 0) {
                continue;
            }
            $checked = $rowclass = '';
            $suppliers_list .= ' <tr class="'.$rowclass.'">';
            $suppliers_list .= '<td><input id="supplierfilter_check_'.$key.'" name="budget[suppliers][]" type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td><tr>';
        }
    }

    $user = new Users($core->user['uid']);
    $user_segments_objs = $user->get_segments();
    if(is_array($user_segments_objs)) {
        foreach($user_segments_objs as $user_segments_obj) {
            $user_segments[$user_segments_obj->get()['psid']] = $user_segments_obj->get();
        }
    }
    $reporting_touser = $user->get_reportingto();
    if(is_array($user_segments)) {
        foreach($user_segments as $segment) {
            $checked = $rowclass = '';
            $budget_segments_list .='<tr class="'.$rowclass.'">';
            $budget_segments_list .='<td><input id="segmentfilter_check_'.$segment['psid'].'"  name="budget[segments][]" type="checkbox"'.$checked.' value="'.$segment['psid'].'">'.$segment['title'].'</td></tr>';
        }
    }
    else {
        $budget_segment.=$lang->na;
    }
    $years = BudgetingYearEndForecast::get_availableyears();
    if(is_array($years)) {
        foreach($years as $key => $value) {
            $checked = $rowclass = '';
            $budget_year_list .= '<tr class="'.$rowclass.'">';
            $budget_year_list .= '<td><input name="budget[years]"  required="required" type="radio" value="'.$key.'">'.$value.'</td></tr>';
        }
    }
    if(is_array($core->user['auditedaffids'])) {
        foreach($core->user['auditedaffids'] as $auditaffid) {
            $aff_obj = new Affiliates($auditaffid);
            $affiliate_users = $aff_obj->get_all_users(array('customfilter' => 'u.uid IN (SELECT users_usergroups.uid FROM users_usergroups WHERE gid IN (SELECT usergroups.gid FROM usergroups WHERE budgeting_canFillBudget=1))'));
            foreach($affiliate_users as $aff_businessmgr) {
                $business_managers[$aff_businessmgr['uid']] = $aff_businessmgr['displayName'];
            }
        }
    }
    else {
        if($core->usergroup['canViewAllEmp'] == 1) {
            $affiliate = new Affiliates($core->user['mainaffiliate']);
            $business_managers = $affiliate->get_all_users(array('displaynameonly' => true, 'customfilter' => 'u.uid IN (SELECT DISTINCT(users_usergroups.uid) FROM users_usergroups WHERE gid IN (SELECT usergroups.gid FROM usergroups WHERE budgeting_canFillBudget=1))'));
        }
        else {
            $business_managers[$core->user['uid']] = $core->user['displayName'];
        }
    }
    $affiliate_users = $core->user_obj->get_reportingto();
    if(is_array($affiliate_users)) {
        foreach($affiliate_users as $aff_businessmgr) {
            $business_managers[$aff_businessmgr['uid']] = $aff_businessmgr['displayName'];
        }
    }
    if(is_array($segmentusers)) {
        $business_managers = array_unique($business_managers + $segmentusers);
    }
    if(is_array($business_managers)) {
        foreach($business_managers as $key => $value) {
            $checked = $rowclass = '';
            $business_managerslist .= '<tr class="'.$rowclass.'">';
            $business_managerslist .= '<td><input id="bmfilter_check_'.$key.'" name="budget[managers][]" type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td></tr>';
        }
    }
    /* parse currencies */
    $currency['filter']['numCode'] = 'SELECT mainCurrency FROM '.Tprefix.'countries WHERE affid IS NOT NULL';
    $curr_objs = Currencies::get_data($currency['filter'], array('returnarray' => true, 'operators' => array('numCode' => 'IN')));
    $curr_objs[840] = new Currencies(840);
    //$curr_objs = Currencies::get_data('alphaCode IS NOT NULL');
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

        /* overrites the filters and get the user filter when no  filters are selected */
        $dummy_budget = new BudgetingYearEndForecast();
        $filters = $dummy_budget->generate_yefline_filters();
        //   $filters = array('affiliates' => $core->user['affiliates'], 'suppliers' => $core->user['suppliers']['eid'], 'segments' => array_keys($core->user_obj->get_segments()));
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
        $periods = array('current'); //, 'prev2years', 'prev3years');
        foreach($periods as $period) {
            $budgets[$period] = BudgetingYearEndForecast::get_yefs_bydata($budgetsdata[$period]);
        }
        if(!is_array($budgets['current'])) {
            output_xml('<status>false</status><message><![CDATA['.$lang->nomatchfound.']]></message>');
            exit;
        }
        $report_type = $core->input['budget']['reporttype'];
        $countrows = 0;
        if($report_type == 'dimensional') {
            $fields = array('current'); // 'prev2years', 'prev3years');
            foreach($fields as $field) {
                if(is_array($budgets[$field])) {
                    foreach($budgets[$field] as $budgetid) {
                        $budget_obj = new BudgetingYearEndForecast($budgetid);
                        /* Validate Permissions - START */
                        $filter = $budget_obj->generate_yefline_filters();
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
                        $budgetlines = $budget_obj->get_yeflines_objs(array('businessMgr' => $budgetsdata[$field]['managers']), array('operators' => array('createdBy' => 'in'), 'order' => 'quantity', 'returnarray' => true));

                        if(!is_array($budgetlines)) {
                            continue;
                        }

                        foreach($budgetlines as $yeflid => $budgetline) {
                            $rawdata[$field][$yeflid] = $budget_obj->get() + $budgetline->get();
                            $product = $budgetline->get_product();
                            if(empty($rawdata[$field][$yeflid]['pid'])) {
                                continue;
                            }
                            if(empty($rawdata[$field][$yeflid]['psid'])) {
                                $rawdata[$field][$yeflid]['psid'] = $product->get_productsegment()->psid;
                            }
                            $budget_currencies[$budgetline->blid] = $budgetline->originalCurrency;
                            /* get the currency rate of the Origin currency  of the current buudget and convert it - START */
                            if($budgetline->originalCurrency != $budgetsdata[$field]['toCurrency']) {
                                $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $budgetline->originalCurrency, 'toCurrency' => $budgetsdata[$field]['toCurrency'], 'affid' => $budget_obj->affid, 'year' => $budget_obj->year, 'isYef' => 1), $dal_config);
                                if(is_array($fxrates_obj)) {
                                    if($field !== 'current') {
                                        $budgetline->amount = $budgetline->actualAmount;
                                        $budgetline->income = $budgetline->actualIncome;
                                    }
                                    foreach($fxrates_obj as $fxid => $fxrates) {
                                        $rawdata[$field][$yeflid]['amount'] = ($budgetline->amount * $fxrates->rate);
                                        $rawdata[$field][$yeflid]['income'] = ($budgetline->income * $fxrates->rate);
                                        $rawdata[$field][$yeflid]['localIncomeAmount'] = ($budgetline->localIncomeAmount * $fxrates->rate);
                                        $rawdata[$field][$yeflid]['invoicingEntityIncome'] = ($budgetline->invoicingEntityIncome * $fxrates->rate);
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
                            $rawdata[$field][$yeflid]['reportsTo'] = $budget_obj->get_CreateUser()->get_reportsto()->uid;
                            $rawdata[$field][$yeflid]['uid'] = $budgetline->businessMgr;
                            $rawdata[$field][$yeflid]['stid'] = $budgetline->saleType;
                            $rawdata[$field][$yeflid]['spid'] = $product->get_supplier()->eid;
                            $rawdata[$field][$yeflid]['octoberAmount'] = $rawdata[$field][$yeflid]['amount'] * ($rawdata[$field][$yeflid]['october'] / 100);
                            $rawdata[$field][$yeflid]['novemberAmount'] = $rawdata[$field][$yeflid]['amount'] * ($rawdata[$field][$yeflid]['november'] / 100);
                            $rawdata[$field][$yeflid]['decemberAmount'] = $rawdata[$field][$yeflid]['amount'] * ($rawdata[$field][$yeflid]['december'] / 100);

                            $rawdata[$field][$yeflid]['octoberIncome'] = $rawdata[$field][$yeflid]['income'] * ($rawdata[$field][$yeflid]['october'] / 100);
                            $rawdata[$field][$yeflid]['novemberIncome'] = $rawdata[$field][$yeflid]['income'] * ($rawdata[$field][$yeflid]['november'] / 100);
                            $rawdata[$field][$yeflid]['decemberIncome'] = $rawdata[$field][$yeflid]['income'] * ($rawdata[$field][$yeflid]['december'] / 100);

                            $rawdata[$field][$yeflid]['interCompanyPurchase_output'] = $lang->na;
                            if(!empty($rawdata[$field][$yeflid]['customerCountry'])) {
                                $rawdata[$field][$yeflid]['coid'] = $rawdata[$yeflid]['customerCountry'];
                            }
                            else {

                                $rawdata[$field][$yeflid]['coid'] = $budgetline->get_customer()->get_country()->coid;
                                if(empty($rawdata[$field][$yeflid]['coid'])) {
                                    $rawdata[$field][$yeflid]['coid'] = $budget_obj->get_affiliate()->get_country()->coid;
                                }
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
                    /* Validate Permissions - START */
                    $filter = $budget_obj->generate_yefline_filters();
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
                                $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $budgetline['originalCurrency'], 'toCurrency' => $budgetsdata['current']['toCurrency'], 'affid' => $budgetsdata['current']['affiliates'], 'year' => $budgetsdata['current']['years'], 'isYef' => 1), $dal_config);
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
                $toolgenerate = '<div align="right" title="'.$lang->generate.'" style="float:right;padding:10px;width:10px;"><a href="index.php?module=budgeting/generateyearendforecast&identifier='.$export_identifier.'&action=exportexcel" target="_blank"><img src="./images/icons/xls.gif"/>'.$lang->generateexcel.'</a></div>';
            }
            else {
                $budgeting_budgetrawreport = '<tr><td>'.$lang->na.'</td></tr>';
            }

            if($core->usergroup['budgeting_canFillLocalIncome'] == 1) {
                $loalincome_header = '<th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">'.$lang->localincome.'</th>';
                $loalincome_header = '<th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">'.$lang->remainingcommaff.'</th>';
            }
            if(is_array($total) && !empty($total)) {
                unset($budgetline, $budget);
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
    elseif($core->input['action'] == 'exportexcel') { // Needs optimization reuse same code
        if(!isset($core->input['identifier'])) {
            error($lang->fillallrequirefields);
        }
        $budgetsdata['current'] = unserialize(base64_decode($core->input['identifier']));
        $budgets['current'] = BudgetingYearEndForecast::get_yefs_bydata($budgetsdata['current']);

        $headers_data = array('yeflid', 'manager', 'customer', 'customerCountry', 'affiliate', 'supplier', 'segment', 'product', 'quantity', 'uom', 'unitPrice', 'saleType', 'amount', 'income', 'october', 'november', 'december', 'purchasingEntity', 'commissionSplitAffName');
        if($core->usergroup['budgeting_canFillLocalIncome'] == 1) {
            $headers_data[] = 'localIncomeAmount';
            $headers_data[] = 'invoicingEntityIncome';
        }
        $counter = 1;
        if(is_array($budgets['current'])) {
            foreach($budgets['current'] as $budgetid) {
                $budget_obj = new BudgetingYearEndForecast($budgetid);
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
                                            redirect('index.php?module=budgeting/generateyearendforecast');
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
                if(!empty($filter['filters']['businessMgr'])) {
                    $budgetline_filter['businessMgr'] = $filter['filters']['businessMgr'];
                }
                $yefbudgetlines = $budget_obj->get_lines($budgetline_filter);
                if(is_array($yefbudgetlines)) {
                    // foreach($firstbudgetline as $cid => $customersdata) {
                    //  foreach($customersdata as $pid => $productsdata) {
                    foreach($yefbudgetlines as $yeflid => $yefbudgetline_obj) {
                        $budgetline[$counter] = $yefbudgetline_obj->get();
                        $countries = new Countries($yefbudgetline_obj->get_customer()->get()['country']);
                        $budgetline[$counter]['manager'] = $yefbudgetline_obj->get_businessMgr()->get()['displayName'];

                        $budgetline[$counter]['customerCountry'] = $yefbudgetline_obj->parse_country();

                        if(!empty($budgetline[$counter]['psid'])) {
                            $segment = new ProductsSegments($budgetline[$counter]['psid']);
                            $budgetline[$counter]['segment'] = $segment->title;
                        }
                        else {
                            $budgetline[$counter]['segment'] = $yefbudgetline_obj->get_product()->get_segment()['title'];
                        }
                        $dal_config = array(
                                'operators' => array('affid' => 'in', 'year' => '='),
                                'simple' => false,
                                'returnarray' => true
                        );

                        $budgetline[$counter]['product'] = $yefbudgetline_obj->get_product()->get()['name'];
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
                        $budgetline[$counter]['saleType'] = BudgetingYearEndForecast::get_saletype_byid($budgetline[$counter]['saleType']);
                        /* get the currency rate of the Origin currency  of the current buudget and convert it - START */
                        $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $budgetline[$counter]['originalCurrency'], 'toCurrency' => $budgetsdata['current']['toCurrency'], 'affid' => $budget_obj->affid, 'year' => $budget_obj->year, 'isYef' => 1), $dal_config);
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
                            $budgetline[$counter]['customer'] = $yefbudgetline_obj->get_customer()->get()['companyName'];
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

        $budgetline_temp = $budgetline;
        unset($budgetline);
        foreach($headers_data as $val) {
            $budgetline[0][$val] = $lang->{strtolower($val)};
            if(is_array($budgetline_temp)) {
                foreach($budgetline_temp as $counter => $value) {
                    $budgetline[$counter][$val] = $value[$val];
                }
            }
        }

        unset($budgetline_temp);
        $excelfile = new Excel('array', $budgetline);
    }
}
?>
