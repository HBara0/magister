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
            $entitysegments = EntitiesSegments::get_data('psid IN ('.implode(',', $psids).') AND eid IN (Select eid FROM entities WHERE type = "s" ) ', array('operators' => array('filter' => 'CUSTOMSQLSECURE'), 'returnarray' => true));
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
    $years = Budgets::get_availableyears();
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
        $budgetsdata['prev2years'] = $budgetsdata['prev3years'] = $budgetsdata['current'];
        $budgetsdata['prev2years']['years'] = $budgetsdata['current']['years'] - 2;
        $budgetsdata['prev3years']['years'] = $budgetsdata['current']['years'] - 3;
        $periods = array('current', 'prev2years', 'prev3years');
        foreach($periods as $period) {
            $budgets[$period] = BudgetingYearEndForecast::get_yefs_bydata($budgetsdata[$period]);
        }
        if(!is_array($budgets['current'])) {
            output_xml('<status>false</status><message><![CDATA['.$lang->nomatchfound.']]></message>');
            exit;
        }
        $report_type = $core->input['budget']['reporttype'];
        if($report_type == 'dimensional') {
            $fields = array('current', 'prev2years', 'prev3years');
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
                            $rawdata[$field][$blid]['octoberAmount'] = $rawdata[$field][$blid]['amount'] * ($rawdata[$field][$blid]['october'] / 100);
                            $rawdata[$field][$blid]['novemberAmount'] = $rawdata[$field][$blid]['amount'] * ($rawdata[$field][$blid]['november'] / 100);
                            $rawdata[$field][$blid]['decemberAmount'] = $rawdata[$field][$blid]['amount'] * ($rawdata[$field][$blid]['december'] / 100);

                            $rawdata[$field][$blid]['octoberIncome'] = $rawdata[$field][$blid]['income'] * ($rawdata[$field][$blid]['october'] / 100);
                            $rawdata[$field][$blid]['novemberIncome'] = $rawdata[$field][$blid]['income'] * ($rawdata[$field][$blid]['november'] / 100);
                            $rawdata[$field][$blid]['decemberIncome'] = $rawdata[$field][$blid]['income'] * ($rawdata[$field][$blid]['december'] / 100);

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
            /* Parse suppliers weight - START */
            $query = $db->query('SELECT DISTINCT(spid) FROM '.Tprefix.'budgeting_yearendforecast WHERE yefid IN ('.implode(',', array_keys($budgets['current'])).') GROUP BY spid');
            while($supplier = $db->fetch_assoc($query)) {
                $suppliers[$supplier['spid']] = $supplier['spid'];
            }

            $numfmt_perc = new NumberFormatter($lang->settings['locale'], NumberFormatter::PERCENT);
            $numfmt_perc->setPattern("#0.###%");
            foreach($suppliers as $spid) {
                $suppliers[$spid] = new Entities($spid);
                $weightstotals['income'][$spid] = ceil(BudgetingYEFLines::get_aggregate_bysupplier($suppliers[$spid], 'localIncomeAmount', array('yefid' => array_keys($budgets['current'])), array('toCurrency' => $budgetsdata['current']['toCurrency'], 'operators' => $operators)));
                $weightstotals['amount'][$spid] = ceil(BudgetingYEFLines::get_aggregate_bysupplier($suppliers[$spid], 'amount', array('yefid' => array_keys($budgets['current'])), array('toCurrency' => $budgetsdata['current']['toCurrency'], 'operators' => $operators)));
                $weightstotals['customers'][$spid] = $db->fetch_field($db->query('SELECT COUNT(DISTINCT(cid)) AS count FROM budgeting_yef_lines WHERE yefid IN (SELECT yefid FROM budgeting_yearendforecast WHERE yefid IN ('.implode(',', array_keys($budgets['current'])).') AND spid='.intval($spid).')'), 'count');
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
                } print_R($weightstotals);
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
                        $data = BudgetingYEFLines::get_top($perc, $type, array('yefb.yefid' => array_keys($budgets['current'])), array('group' => $group, 'operators' => array('yefb.yefid' => 'IN')));
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
                        $values['country'][$field] = ceil(BudgetingYEFLines::get_aggregate_bycountry($country, $field, array('yefid' => array_keys($budgets['current'])), array('toCurrency' => $budgetsdata['current']['toCurrency'], 'operators' => $operators)));
                        $values['affiliate'][$field] = ceil(BudgetingYEFLines::get_aggregate_byaffiliate($affiliate, $field, array('yefid' => array_keys($budgets['current'])), array('toCurrency' => $budgetsdata['current']['toCurrency'], 'operators' => $operators)));
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
                                $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $budgetline['originalCurrency'], 'toCurrency' => $budgetsdata['current']['toCurrency'], 'affid' => $budgetsdata['current']['affiliates'], 'year' => $budgetsdata['current']['years']), $dal_config);
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
                            eval("\$budget_report_row .= \"".$template->get('budgeting_yefrawreport_row')."\";");
                        }
                    }
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
            eval("\$budgeting_budgetrawreport = \"".$template->get('budgeting_yefrawreport')."\";");
        }


        eval("\$budgetingpreview = \"".$template->get('budgeting_yefpreview')."\";");

        output_xml('<status></status><message><![CDATA['.$budgetingpreview.']]></message>');
    }
}
?>
