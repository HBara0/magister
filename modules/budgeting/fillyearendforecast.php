<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: fillyef.php
 * Created:        @hussein.barakat    Sep 8, 2015 | 12:44:05 PM
 * Last Update:    @hussein.barakat    Sep 8, 2015 | 12:44:05 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['canUseBudgeting'] == 0) {
    error($lang->sectionnopermission);
}
if(!$core->input['action']) {
    if($core->input['stage'] == 'fillbudgetline') {
        $display = 'none';
        $yef_data = $core->input['yef'];
        $affiliate = new Affiliates($yef_data['affid']);
        $yef_data['affiliateName'] = $affiliate->get()['name'];
        $supplier = new Entities($yef_data['spid']);
        $yef_data['supplierName'] = $supplier->get()['companyName'];
        $sup_segments = $supplier->get_segments();
        if(!is_array($sup_segments)) {
            error($lang->supplierhasnosegment);
        }
        $supplier_segments = array_filter($sup_segments);
        $currentbudget = Budgets::get_budget_bydata($yef_data);

        /* Validate Permissions - START */
        if($core->usergroup['canViewAllSupp'] == 0 && $core->usergroup['canViewAllAff'] == 0) {
            if(is_array($core->user['auditfor'])) {
                if(!in_array($yef_data['spid'], $core->user['auditfor'])) {
                    if(is_array($core->user['auditedaffids'])) {
                        if(!in_array($yef_data['affid'], $core->user['auditedaffids'])) {
                            if(is_array($core->user['suppliers']['affid'][$yef_data['spid']])) {
                                if(in_array($yef_data['affid'], $core->user['suppliers']['affid'][$yef_data['spid']])) {
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
        $fromusers = UsersTransferedAssignments::get_data(array('toUser' => $core->user['uid'], 'affid' => $yef_data['affid'], 'eid' => $yef_data['spid']), array('returnarray' => true));
        if(is_array($fromusers)) {
            foreach($fromusers as $fromuser) {
                $bm_ids[] = $fromuser->fromUser;
            }
        }

        if(is_array($filter['filters']['businessMgr']) && is_array($bm_ids)) {
            $filter['filters']['businessMgr'] = array_merge($filter['filters']['businessMgr'], $bm_ids);
        }
        elseif(is_array($bm_ids)) {
            $bm_ids[] = $core->user['uid'];
            $filter['filters']['businessMgr'] = $bm_ids;
        }

        $currentyef = BudgetingYearEndForecast::get_yef_bydata($yef_data);
        if($currentyef != false) {
            $yefobj = new BudgetingYearEndForecast($currentyef['yefid']);
            $yef_data['yefid'] = $yefobj->yefid;
            $yeflinesdata = $yefobj->get_yefLines('', $filter);
            if(!is_array($yeflinesdata) || empty($yeflinesdata)) {
                $noyeflines = true;
            }
        }
        else {
            $noyeflines = true;
        }

        if($currentbudget != false) {
            $budgetobj = new Budgets($currentbudget['bid']);
            $yef_data['bid'] = $budgetobj->bid;
            if($noyeflines == true) {
                $budgetlinesdata = $budgetobj->get_budgetLines('', $filter);
            }
            else {
                $budgetlinesdata = $budgetobj->get_budgetLines('', $filter, $yeflinesdata);
            }
            if(!is_array($budgetlinesdata) || empty($budgetlinesdata)) {
                $nolines = true;
            }
        }
        else {
            $nolines = true;
        }

        if($affiliate->isIntReinvoiceAffiliate != 1) {
            $saletypes_query_where = ' WHERE stid NOT IN (SELECT s1.invoiceAffStid FROM saletypes s1 WHERE s1.invoiceAffStid IS NOT NULL)';
        }
        $saletypes_query = $db->query('SELECT * FROM '.Tprefix.'saletypes'.$saletypes_query_where);
        while($saletype = $db->fetch_assoc($saletypes_query)) {
            $saletype_selectlistdata[$saletype['stid']] = $saletype['title'];
            $saletypes[$saletype['stid']] = $saletype;
            $tooltips['saletype'] .= '<strong>'.$saletype['title'].'</strong><br />'.$saletype['description'].'<hr />';
        }

        /* Get Invoice Types - START */
        $saleinvoice_query = $db->query('SELECT * FROM '.Tprefix.'saletypes_invoicing WHERE isActive = 1 AND affid = '.intval($yef_data['affid']));
        if($db->num_rows($saleinvoice_query) > 0) {
            while($saleinvoice = $db->fetch_assoc($saleinvoice_query)) {
                $invoice_selectlistdata[$saleinvoice['invoicingEntity']] = ucfirst($saleinvoice['invoicingEntity']);
                $saletypes_invoicing[$saleinvoice['stid']] = $saleinvoice['invoicingEntity'];
                if($saleinvoice['isAffiliate'] == 1 && !empty($saleinvoice['invoiceAffid'])) {
                    $saleinvoice['invoiceAffiliate'] = new Affiliates($saleinvoice['invoiceAffid']);
                    $invoice_selectlistdata[$saleinvoice['invoicingEntity']] = $saleinvoice['invoiceAffiliate']->get()['name'];
                }
            }
        }
        else {
            $invoice_selectlistdata['other'] = $lang->other;
        }
        /* Get Invoice Types - ENDs */

        /* Get Purchasing entity Types - START */
//      $salepurchase_query = $db->query('SELECT * FROM '.Tprefix.'saletypes_invoicing WHERE isActive = 1 AND affid = '.intval($yef_data['affid']));
//        if($db->num_rows($salepurchase_query) > 0) {
//            while($salepurchase = $db->fetch_assoc($salepurchase_query)) {
//                $purchase_selectlistdata[$salepurchase['invoicingEntity']] = ucfirst($salepurchase['invoicingEntity']);
//                $saletypes_purchasing[$salepurchase['stid']] = $salepurchase['invoicingEntity'];
//                if($salepurchase['isAffiliate'] == 1 && !empty($salepurchase['invoiceAffid'])) {
//                    $salepurchase['invoiceAffiliate'] = new Affiliates($salepurchase['invoiceAffid']);
//                    $purchase_selectlistdata[$salepurchase['invoicingEntity']] = $salepurchase['invoiceAffiliate']->get()['name'];
//                }
//            }
//        }
//        else {
//            $purchase_selectlistdata['other'] = $lang->other;
//        }
//        /* --------------------- */
//
//
        $affiliate_currency = new Currencies($affiliate->get_country()->get()['mainCurrency']);
        $currencies = array_filter(array(840 => 'USD', 978 => 'EUR'));
        $currency['filter']['numCode'] = 'SELECT mainCurrency FROM countries where affid IS NOT NULL';
        $curr_objs = Currencies::get_data($currency['filter'], array('returnarray' => true, 'operators' => array('numCode' => 'IN')));
        foreach($curr_objs as $curr_obj) {
            $currencies[$curr_obj->get_id()] = $curr_obj->alphaCode;
        }

        /* check whether to display existing budget Form or display new one  */
        $unsetable_fields = array('quantity', 'amount', 'incomePerc', 'income', 'inputCheckSum');
        $countries = Countries::get_coveredcountries();

        /* get already existing YEF lines corresponding to the data inputted */

        $rownums = 1;
        if($noyeflines != true) {
            foreach($yeflinesdata as $cid => $cyeflinesdata) {
                foreach($cyeflinesdata as $pid => $pyeflinesdata) {
                    foreach($pyeflinesdata as $saletypte => $yeflinedata) {
                        $filter['filters']['blid'][] = $yeflinedata['blid'];
                    }
                }
            }
            if(is_array($filter['filters']['blid'])) {
                $filter['filters']['blid'] = array_filter($filter['filters']['blid']);
            }
            $filter['operators']['blid'] = 'NOT IN';
            $user_prev_yeflines = $budgetobj->get_budgetLines('', $filter, 'userprevlines');
            if(is_array($user_prev_yeflines)) {

//                foreach($user_prev_yeflines as $cid => $cuser_prev_yeflines) {
//                    foreach($cuser_prev_yeflines as $pid => $puser_prev_yeflines) {
//                        foreach($puser_prev_yeflines as $saletype => $user_prev_yefline) {
//                            $yeflinesdata[$cid][$pid][$saleType] = $user_prev_yefline;
//                        }
//                    }
//                }
                $yeflinesdata = array_merge_recursive($yeflinesdata, $user_prev_yeflines);
            }

            $prevyehlines = parse_yefline($yeflinesdata, '', 'yef', $rownums, $supplier);
            if(is_array($prevyehlines) && !empty($prevyehlines)) {
                $budgetlinesrows = $prevyehlines['lines'];
                $rownums = $prevyehlines['rows'];
            }
        }
        elseif($nolines != true) {
            $prevbudlines = parse_yefline($budgetlinesdata, 'readonly', 'budget', $rownums, $supplier);
            if(is_array($prevbudlines) && !empty($prevbudlines)) {
                $budgetlinesrows .= $prevbudlines['lines'];
                $rownums = $prevbudlines['rows'];
            }
        }
        else {
            $rownums = 1;
            $rowid = generate_checksum();
            $saletype_selectlist = parse_selectlist('budgetline['.$rowid.'][saleType]', 0, $saletype_selectlistdata, '', '', '', array('id' => 'salestype_'.$rowid));
            $invoice_selectlist = parse_selectlist('budgetline['.$rowid.'][invoice]', 0, $invoice_selectlistdata, '', '', '', array('id' => 'invoice_'.$rowid));
            $purchase_selectlistdata = array('alex' => 'Orkila FZ - Alex', 'fze' => 'Orkila Jebel Ali FZE', 'int' => 'Orkila International', 'customer' => 'Customer', 'direct' => $yef_data['affiliateName']);
            $purchasingentity_selectlist = parse_selectlist('budgetline['.$rowid.'][purchasingEntity]', 0, $purchase_selectlistdata, 'direct', '', '', array('id' => 'purchasingEntity_'.$rowid));
            $budget_currencylist = ' <select id = "currency_{$rowid}" name = "budgetline['.$rowid.'][originalCurrency]">';
            foreach($currencies as $numcode => $currency) {
                $budget_currencylist .= '<option value="'.$numcode.'">'.$currency.'</option>';
            }
            $budget_currencylist.='</select>';
            if(count($supplier_segments) > 1) {
                $segments_selectlist = parse_selectlist('budgetline['.$rowid.'][psid]', 3, $supplier_segments, 0, null, null, array('placeholder' => 'Overwrite Segment'));
            }
            $purchasefromaff = '<input type="text" placeholder="'.$lang->search.' '.$lang->affiliate.'" id="affiliate_noexception_'.$rowid.'_autocomplete" name="" value="'.$budgetline['interCompanyPurchase_output'].'" autocomplete="off"  />
        <input type="hidden" value="'.$budgetline['interCompanyPurchase'].'" id="affiliate_noexception_'.$rowid.'_id" name="budgetline['.$rowid.'][interCompanyPurchase]" />';
            if($core->usergroup['budgeting_canFillLocalIncome'] == 1) {
                $hidden_colcells = array('localincome_row' => ' <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center"><input name="budgetline['.$rowid.'][localIncomeAmount]"  value="'.$budgetline[localIncomeAmount].'"  type="text" id="localincome_'.$rowid.'" size="10" accept="numeric" /> </td>',
                        'localincomeper_row' => '<td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center"><input name="budgetline['.$rowid.'][localIncomePercentage]"  value="'.$budgetline[localIncomePercentage].'" type="text" id="localincomeper_'.$rowid.'" size="10" accept="numeric"  /> </td>',
                        'remainingcommaff_header_row' => '<td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center">
             <input type="text" placeholder="'.$lang->search.' '.$lang->affiliate.'" id="affiliate_noexception_'.$rowid.'_commission_autocomplete" name=""  value="'.$budgetline['commissionSplitAffid_output'].'" autocomplete="off" />
        <input type="hidden" value="'.$budgetline['commissionSplitAffid'].'" id="affiliate_noexception_'.$rowid.'_commission_id" name="budgetline['.$rowid.'][commissionSplitAffid]"/></td>'
                );
            }
            $budgetline['inputCheckSum'] = generate_checksum();
            $countries_selectlist = parse_selectlist('budgetline['.$rowid.'][customerCountry]', 0, $countries, $affiliate->country, '', '', '');

            eval("\$budgetlinesrows .= \"".$template->get('budgeting_fill_yeflines')."\";");
            unset($budgetline);
        }
        unset($saletype_selectlistdata, $checked_checkboxes);

        /* Parse values for JS - START */

        foreach($saletypes as $stid => $saletype) {
            if($saletype ['useLocalCurrency'] == 1) {
                $saltypes_currencies[$stid] = $affiliate_currency->get()['numCode'];
            }
            else {
                $saltypes_currencies[$stid] = 840;
            }
        }

        $js_currencies = json_encode($saltypes_currencies);
        $js_saletypesinvoice = json_encode($saletypes_invoicing);
//  $js_saletypespurchase = json_encode($saletypes_purchasing);

        /* Parse values for JS - END */
        /* Parse  local amount felds based on specific permission */
        if($core->usergroup['budgeting_canFillLocalIncome'] == 1) {
            $hidden_colcells = array('localincome_head' => '<td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center"><div id="localincome_tour">'.$lang->localincome.'</div><a href="#" title="'.$lang->localincomeexp.'"><img src="./images/icons/question.gif" ></a></td>',
                    'localincomeper_head' => '<td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">'.$lang->localincomeper.'</td>',
                    'remainingcommaff_head' => '<td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center"><div id="commissionaff_tour">'.$lang->remainingcommaff.'</div><a href="#" title="The affiliate which will keep the remaining commission"><img src="./images/icons/question.gif" ></a></td>'
            );
        }
        else {
            $hidden_colcells = array('localincome_head' => '<td style="display:none"></td>',
                    'localincomeper_head' => '<td style="display:none"></td>',
                    'remainingcommaff_head' => '<td style="display:none"></td>'
            );
        }
        $helptour = new HelpTour();
        $helptour->set_id('fillyearendforecast_helptour');
        $helptour->set_cookiename('fillyearendforecast_helptour');
        $touritems = BudgetingYearEndForecast::get_helptouritems();
        $helptour->set_items($touritems);
        $helptour = $helptour->parse();
        eval("\$fillbudget = \"".$template->get('budgeting_fillyef')."\";");
        output_page($fillbudget);
    }
}
else {
    if($core->input['action'] == 'do_perform_fillyearendforecast') {
        $keydata = array('year', 'spid', 'affid');
        foreach($keydata as $attr) {
            $yef_data[$attr] = $core->input[$attr];
        }
        if(is_array($core->input['budgetline'])) {
            if(isset($core->input['yef']['yefid']) && !empty($core->input['yef']['yefid'])) {
                $currentbudget = $core->input['yef'];
                $yefobj = new BudgetingYearEndForecast($core->input['yef']['yefid']);
            }
            else {
                $currentbudget = BudgetingYearEndForecast::get_yef_bydata($yef_data);
            }
            if(is_array($currentbudget) && !empty($currentbudget['yefid'])) {
                $yef_data['yefid'] = $currentbudget['yefid'];
            }
            if(is_object($yefobj)) {
                $yefobj->save_budget($yef_data, $core->input['budgetline']);
            }
            else {
                $yefobj = BudgetingYearEndForecast::save_budget($yef_data, $core->input['budgetline']);
            }
        }
        if(!is_object($yefobj)) {
            if($yefobj == true) {
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                exit;
            }
            elseif($yefobj == false) {
                output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
                exit;
            }
        }
        else {
            switch($yefobj->get_errorcode()) {
                case 0:
                    output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                    break;
                case 2:
                    output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
                    break;
                case 602:
                    output_xml('<status>false</status><message>'.$lang->budgetexist.'</message>');
                    break;
                default:
                    output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
                    break;
            }
        }
    }
    elseif($core->input['action'] == 'ajaxaddmore_budgetlines') {
        $display = 'none';
        $rowid = generate_checksum();
        $budgetline['inputCheckSum'] = $rowid;
        $rownums = intval($core->input['value']) + 1;
        $yef_data = $core->input['ajaxaddmoredata'];
        $supplier = new Entities(intval($yef_data['spid']));
        $affiliate = new Affiliates(intval($yef_data['affid']));
        if($affiliate->isIntReinvoiceAffiliate == 0) {
            $saletypes_query_where = ' WHERE stid NOT IN (SELECT s1.invoiceAffStid FROM saletypes s1 WHERE s1.invoiceAffStid IS NOT NULL)';
        }
        $saletypes_query = $db->query('SELECT * FROM '.Tprefix.'saletypes'.$saletypes_query_where);
        while($saletype = $db->fetch_assoc($saletypes_query)) {
            $saletype_selectlistdata[$saletype['stid']] = $saletype['title'];
            $saletypes[$saletype['stid']] = $saletype;
        }

        /* Get Invoice Types - START */
        $saleinvoice_query = $db->query('SELECT * FROM '.Tprefix.'saletypes_invoicing WHERE isActive=1 AND affid='.intval($yef_data['affid']));
        if($db->num_rows($saleinvoice_query) > 0) {
            while($saleinvoice = $db->fetch_assoc($saleinvoice_query)) {
                $invoice_selectlistdata[$saleinvoice['invoicingEntity']] = ucfirst($saleinvoice['invoicingEntity']);
                $saletypes_invoicing[$saleinvoice['stid']] = $saleinvoice['invoicingEntity'];
                if($saleinvoice ['isAffiliate'] == 1 && !empty($saleinvoice['invoiceAffid'])) {
                    $saleinvoice['invoiceAffiliate'] = new Affiliates($saleinvoice['invoiceAffid']);
                    $invoice_selectlistdata[$saleinvoice['invoicingEntity']] = $saleinvoice['invoiceAffiliate']->get()['name'];
                }
            }
        }
        else {
            $invoice_selectlistdata['other'] = $lang->other;
        }
        $saletype_selectlist = parse_selectlist('budgetline['.$rowid.'][saleType]', 0, $saletype_selectlistdata, $budgetline['saleType'], '', '', array('id' => 'salestype_'.$rowid));
        $invoice_selectlist = parse_selectlist('budgetline['.$rowid.'][invoice]', 0, $invoice_selectlistdata, $budgetline['invoice'], '', '', array('blankstart' => 0, 'id' => 'invoice_'.$rowid));

        /* Get budget data */
        $budget_currencylist = ' <select id = "currency_{$rowid}" name = "budgetline['.$rowid.'][originalCurrency]">';
        $affiliate_currency = new Currencies($affiliate->get_country()->get()['mainCurrency']);
        $currencies = array_filter(array(840 => 'USD', 978 => 'EUR', $affiliate_currency->get()['numCode'] => $affiliate_currency->get()['alphaCode']));
        foreach($currencies as $numcode => $currency) {
            $budget_currencylist .= '<option value="'.$numcode.'">'.$currency.'</option>';
        }
        $budget_currencylist.='</select>';
        /* Parse  local amount felds based on specific permission */
        if($core->usergroup['budgeting_canFillLocalIncome'] == 1) {
            $hidden_colcells = array('localincome_row' => ' <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center"><input name="budgetline['.$rowid.'][localIncomeAmount]"  value="'.$budgetline[localIncomeAmount].'"  type="text" id="localincome_'.$rowid.'" size="10" accept="numeric" /> </td>',
                    'localincomeper_row' => '<td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center"><input name="budgetline['.$rowid.'][localIncomePercentage]"  value="'.$budgetline[localIncomePercentage].'" type="text" id="localincomeper_'.$rowid.'" size="10" accept="numeric"  /> </td>',
                    'remainingcommaff_header_row' => '<td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center">
             <input type="text" placeholder="'.$lang->search.' '.$lang->affiliate.'" id="affiliate_noexception_'.$rowid.'_commission_autocomplete" name="" value="'.$budgetline['commissionSplitAffid_output'].'" autocomplete="off" />
        <input type="hidden" value="'.$budgetline['commissionSplitAffid'].'" id="affiliate_noexception_'.$rowid.'_commission_id" name="budgetline['.$rowid.'][commissionSplitAffid]"/></td>'
            );
        }


        $purchase_selectlistdata = array('alex' => 'Orkila FZ - Alex', 'fze' => 'Orkila Jebel Ali FZE', 'int' => 'Orkila International', 'customer' => 'Customer', 'direct' => $affiliate->get_displayname());
        if(is_object($supplier)) {
            $segs = $supplier->get_segments();
            if(is_array($segs)) {
                $supplier_segments = array_filter($segs);
                if(count($supplier_segments) > 1) {
                    $segments_selectlist = parse_selectlist('budgetline['.$rowid.'][psid]', 3, $supplier_segments, $budgetline['psid'], null, null, array('placeholder' => 'Overwrite Segment'));
                }
            }
        }
        $purchasingentity_selectlist = parse_selectlist('budgetline['.$rowid.'][purchasingEntity]', 0, $purchase_selectlistdata, 'direct', '', '', array('blankstart' => true, 'id' => 'purchasingEntity_'.$rowid));
        $countries = Countries::get_coveredcountries();
        $countries_selectlist = parse_selectlist('budgetline['.$rowid.'][customerCountry]', 0, $countries, $affiliate->country, '', '');
        $purchasefromaff = '<input type = "text" placeholder = "'.$lang->search.' '.$lang->affiliate.'" id = "affiliate_noexception_'.$rowid.'_autocomplete" name = "" value = "'.$budgetline['interCompanyPurchase_output'].'" autocomplete = "off" />
                <input type = "hidden" value = "'.$budgetline['interCompanyPurchase'].'" id = "affiliate_noexception_'.$rowid.'_id" name = "budgetline['.$rowid.'][interCompanyPurchase]" />';
        eval("\$budgetlinesrows = \"".$template->get('budgeting_fill_yeflines')."\";");
        output($budgetlinesrows);
    }
}
function parse_yefline($data, $readonly = '', $source, $rownums, $supplier) {
    global $template, $core, $db, $lang, $yef_data;

    foreach($data as $cid => $customersdata) {
        /* Get Customer name from object */
        if(!is_int($cid)) {
            $cid = 0;
        }
        $customer = new Entities($cid);
        foreach($customersdata as $pid => $productsdata) {
            /* Get Products name from object */
            $product = new Products($pid);
            foreach($productsdata as $saleid => $budgetline) {
                if(empty($budgetline) || !is_array($budgetline)) {
                    continue;
                }
                unset($disabledattrs);
                $rowid = generate_checksum();
                if($source == 'yef') {
                    $rowid = $budgetline['inputCheckSum'];
                    if($budgetline['source'] == 'userprevlines') {
                        $rowid = $budgetline['inputChecksum'];
                    }
                }
                if($source == 'budget' || $budgetline['source'] == 'userprevlines') {
                    $tooltip['linedetails'] = 'Imported from budget <br/>';
                }
                elseif($budgetline['fromBudget'] == 1) {
                    $tooltip['linedetails'] = 'Line Existing In Current Year Budget <br/>';
                }
                if($source == 'budget' || $budgetline['fromBudget'] == 1 || $budgetline['source'] == 'userprevlines') {
                    $readonly = 'readonly';
                    $alert_div = '<span style="color:red" id="alert_'.$rowid.'"></span>';
                    $disabledattrs ['cid'] = $disabledattrs['unspecifiedCustomer'] = 'disabled = "disabled"';
                    $disabledattrs ['pid'] = 'disabled = "disabled"';
                }
                $previous_yearsqty = $previous_yearsamount = $previous_yearsincome = $prevyear_incomeperc = $prevyear_unitprice = $previous_actualqty = $previous_actualamount = $previous_actualincome = '';

                if(empty($budgetline['localIncomePercentage'])) {
                    $budgetline['localIncomePercentage'] = 0;
                }
                if(empty($budgetline['localIncomeAmount'])) {
                    $budgetline['localIncomeAmount'] = 0;
                }
                if(isset($budgetline['businessMgr']) && !empty($budgetline['businessMgr'])) {
                    $line_bm = Users::get_data(array('uid' => $budgetline['businessMgr']));
                    $tooltip['linedetails'] .= 'Buisness Manager: '.$line_bm->get_displayname();
                }
                $budgetline['cid'] = $cid;
                $budgetline['customerName'] = $customer->get()['companyName'];
                $budgetline['pid'] = $pid;
                $budgetline['productName'] = $product->get()['name'];
                $saletypes_query = $db->query('SELECT * FROM '.Tprefix.'saletypes'.$saletypes_query_where);
                while($saletype = $db->fetch_assoc($saletypes_query)) {
                    $saletype_selectlistdata[$saletype['stid']] = $saletype['title'];
                    $saletypes[$saletype['stid']] = $saletype;
                    $tooltips['saletype'] .= '<strong>'.$saletype['title'].'</strong><br />'.$saletype['description'].'<hr />';
                }
                $saletype_selectlist = parse_selectlist('budgetline['.$rowid.'][saleType]', 0, $saletype_selectlistdata, $saleid, '', '', array('id' => 'salestype_'.$rowid));
                $invoice_selectlist = parse_selectlist('budgetline['.$rowid.'][invoice]', 0, $invoice_selectlistdata, $budgetline['invoice'], '', '', array('id' => 'invoice_'.$rowid));
                $purchase_selectlistdata = array('alex' => 'Orkila FZ - Alex', 'fze' => 'Orkila Jebel Ali FZE', 'int' => 'Orkila International', 'customer' => 'Customer', 'direct' => $yef_data['affiliateName']);
                $purchasingentity_selectlist = parse_selectlist('budgetline['.$rowid.'][purchasingEntity]', 0, $purchase_selectlistdata, $budgetline['purchasingEntity'], '', '', array('id' => 'purchasingEntity_'.$rowid));
                if(!empty($budgetline['interCompanyPurchase'])) {
                    $intercompany_obj = new Affiliates($budgetline['interCompanyPurchase']);
                    $budgetline['interCompanyPurchase_output'] = $intercompany_obj->get_displayname();
                }
                if(!empty($budgetline['commissionSplitAffid'])) {
                    $intercompany_obj = new Affiliates($budgetline['commissionSplitAffid']);
                    $budgetline['commissionSplitAffid_output'] = $intercompany_obj->get_displayname();
                }
                $purchasefromaff = '<input type="text" placeholder="'.$lang->search.' '.$lang->affiliate.'" id="affiliate_noexception_'.$rowid.'_autocomplete" name = "" value = "'.$budgetline['interCompanyPurchase_output'].'" autocomplete = "off" />
                <input type = "hidden" value="'.$budgetline['interCompanyPurchase'].'" id="affiliate_noexception_'.$rowid.'_id" name = "budgetline['.$rowid.'][interCompanyPurchase]" />';

                if($budgetline['fromBudget'] == 1 || $source == 'budget' || $budgetline['source'] == 'userprevlines') {
                    $purchasefromaff = '<input type="text" disabled value = "'.$budgetline['interCompanyPurchase_output'].'" ><input type = "hidden" value = "'.$budgetline['interCompanyPurchase'].'" id = "affiliate_noexception_'.$rowid.'_id" name = "budgetline['.$rowid.'][interCompanyPurchase]" />';
                    $purchasingentity_selectlist = '<input type="text" disabled value = "'.$purchase_selectlistdata[$budgetline['purchasingEntity']].'" name = "purchasenetitytname"><input type = "hidden" value="'.$budgetline['purchasingEntity'].'" name = "budgetline['.$rowid.'][purchasingEntity]">';
                    $saletype_selectlist = '<input type="text" disabled value="'.$saletype_selectlistdata[$saleid].'" name = "saletypename"><input type = "hidden" value = "'.$saleid.'" name = "budgetline['.$rowid.'][saleType]">';
                    $invoice_selectlist = '<input type="text" disabled value="'.$invoice_selectlistdata[$budgetline['invoice']].'" name = "invoicename"><input type = "hidden" value = "'.$budgetline['invoice'].'" name = "budgetline['.$rowid.'][invoice]">';
                }
                if(empty($budgetline['purchasingEntity'])) {
                    $budgetline['purchasingEntity'] = 'direct';
                }
                $display = 'none';

                if(empty($budgetline['cid']) && $budgetline['altCid'] == 'Unspecified Customer') {
                    $checked_checkboxes[$rowid]['unspecifiedCustomer'] = ' checked = "checked"';
                    $display = 'block';
                }
                if(empty($budgetline['cid']) && $budgetline['altCid'] != 'Unspecified Customer') {
                    $budgetline['alternativecustomer'] = '<span style = "display:block;">'.ucfirst($budgetline['altCid']).'</span>';
                    $prev_budgetline['altCid'] = $budgetline['altCid'];
                    if(!empty($budgetline['customerCountry'])) {
                        $display = 'block';
                    }
                }

                /* Get Actual data from mediation tables --END */
                $budget_currencylist = ' <select id = "currency_{$rowid}" name = "budgetline['.$rowid.'][originalCurrency]">';
                $currencies = array_filter(array(840 => 'USD', 978 => 'EUR'));
                $currency = array();
                $currency['filter']['numCode'] = 'SELECT mainCurrency FROM countries where affid IS NOT NULL';
                $curr_objs = Currencies::get_data($currency['filter'], array('returnarray' => true, 'operators' => array('numCode' => 'IN')));
                foreach($curr_objs as $curr_obj) {
                    $currencies[$curr_obj->get_id()] = $curr_obj->alphaCode;
                }
                foreach($currencies as $numcode => $currency) {
                    if($budgetline['originalCurrency'] == $numcode) {
                        $budget_currencylist_selected = ' selected = "selected"';
                    }
                    $budget_currencylist .= '<option value = "'.$numcode.'"'.$budget_currencylist_selected.'>'.$currency.'</option>';
                    $budget_currencylist_selected = '';
                }
                $budget_currencylist.='</select>';
                $frombudgetline = '';
                if($source == 'budget' || $budgetline['fromBudget'] == 1 || $budgetline['source'] == 'userprevlines') {
                    if(!empty($budgetline['psid'])) {
                        $segment = new ProductsSegments($budgetline['psid']);
                        $segments_selectlist = $segment->get_displayname().'<input type="hidden" value="'.$budgetline['psid'].'" name="budgetline['.$rowid.'][psid]">';
                    }
                    $frombudgetline = '<input type = "hidden" value = "1" name = "budgetline['.$rowid.'][fromBudget]">';
                    $budget_currencylist = '<input type = "hidden" value = "'.$budgetline['originalCurrency'].'" name = "budgetline['.$rowid.'][originalCurrency]"><input style = "width=50px" type = "text" disabled value = "'.$currencies[$budgetline['originalCurrency']].'">';
                }
                else {
                    $segments_selectlist = '';
                    if(is_object($supplier)) {
                        $segs = $supplier->get_segments();
                        if(is_array($segs)) {
                            $supplier_segments = array_filter($segs);
                            if(count($supplier_segments) > 1) {
                                $segments_selectlist = parse_selectlist('budgetline['.$rowid.'][psid]', 3, $supplier_segments, $budgetline['psid'], null, null, array('placeholder' => 'Overwrite Segment'));
                            }
                        }
                    }
                }
                if(!empty($budgetline['interCompanyPurchase'])) {
                    $intercompany_obj = new Affiliates($budgetline['interCompanyPurchase']);
                    $budgetline['interCompanyPurchase_output'] = $intercompany_obj->get_displayname();
                }
                if(!empty($budgetline['commissionSplitAffid'])) {
                    $intercompany_obj = new Affiliates($budgetline['commissionSplitAffid']);
                    $budgetline['commissionSplitAffid_output'] = $intercompany_obj->get_displayname();
                }
                $maxfields = array('amount', 'quantity', 'unitPrice', 'income');
                if($source == 'budget' || $budgetline['source'] == 'userprevlines') {
                    foreach($maxfields as $field) {
                        $maxbudgetline[$field] = $budgetline[$field];
                    }
                    unset($budgetline['businessMgr']);
                }
                else if($budgetline['fromBudget'] == 1) {
                    $prev_actualbudgetline = new BudgetLines($budgetline['blid']);
                    if(is_object($prev_actualbudgetline) && !empty($prev_actualbudgetline->blid)) {
                        foreach($maxfields as $field) {
                            $maxbudgetline[$field] = $prev_actualbudgetline->$field;
                        }
                    }
                }
                if($source == 'budget' || $budgetline['source'] == 'userprevlines') {
                    $divided_fields = array('quantity', 'amount', 'income', 'actalQty', 'actualIncome', 'actualAmount', 'localIncomeAmount');
                    //'unitPrice',localIncomePercentage, 'incomePerc'
                    foreach($divided_fields as $field) {
                        if(!isset($budgetline[$field]) || empty($budgetline[$field])) {
                            continue;
                        }
                        $budgetline[$field] = ($budgetline[$field] * $budgetline['s2Perc'] / 100 ) / 2;
                    }
                    $months_fields = array('october', 'november', 'december');
                    foreach($months_fields as $month) {
                        $budgetline[$month] = number_format(33.333);
                        $budgetline[$month.'qty'] = number_format(($budgetline[quantity] / 3), 2, '.', '');
                    }
                }
                if($core->usergroup['budgeting_canFillLocalIncome'] == 1) {
                    $hidden_colcells = array('localincome_row' => ' <td style = "vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align = "center"><input name = "budgetline['.$rowid.'][localIncomeAmount]" value = "'.$budgetline[localIncomeAmount].'" type = "text" id = "localincome_'.$rowid.'" size = "10" accept = "numeric" /> </td>',
                            'localincomeper_row' => '<td style = "vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align = "center"><input name = "budgetline['.$rowid.'][localIncomePercentage]" value = "'.$budgetline[localIncomePercentage].'" type = "text" id = "localincomeper_'.$rowid.'" size = "10" accept = "numeric" /> </td>',
                    );
                    $hidden_colcells['remainingcommaff_header_row'] = '<td style = "vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align = "center">
                <input type = "text" placeholder = "'.$lang->search.' '.$lang->affiliate.'" id="affiliate_noexception_'.$rowid.'_commission_autocomplete" name = "" value="'.$budgetline['commissionSplitAffid_output'].'" autocomplete = "off" />
                <input type = "hidden" value = "'.$budgetline['commissionSplitAffid'].'" id="affiliate_noexception_'.$rowid.'_commission_id" name = "budgetline['.$rowid.'][commissionSplitAffid]"/></td>';

                    if($source == 'budget' || $budgetline['fromBudget'] == 1) {
                        $hidden_colcells ['remainingcommaff_header_row'] = '<td style = "vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align = "center"><input type="text" value="" disabled></td>';
                        if(!empty($budgetline['commissionSplitAffid'])) {
                            $aff = new Affiliates($budgetline['commissionSplitAffid']);
                            if(is_object($aff)) {
                                $hidden_colcells['remainingcommaff_header_row'] = '<td style = "vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center"><input type="text" disabled value=" '.$aff->get_displayname().'"><input type = "hidden" name = "budgetline['.$rowid.'][commissionSplitAffid]" value = "'.$aff->affid.'"></td>';
                            }
                        }
                    }
                }
                else {
                    $hidden_colcells = array('localincome_row' => ' <td style="display:none"><input name="budgetline['.$rowid.'][localIncomeAmount]"  value="'.$budgetline['localIncomeAmount'].'" id="localincome_'.$rowid.'" type="hidden" /> </td>',
                            'localincomeper_row' => '<td style="display:none" ><input name="budgetline['.$rowid.'][localIncomePercentage]"  value="'.$budgetline['localIncomePercentage'].'" type="hidden" id="localincomeper_'.$rowid.'"  /> </td>',
                            'remainingcommaff_header_row' => '<td style="display:none"><input type="hidden" value="'.$budgetline['commissionSplitAffid'].'" id="affiliate_noexception_'.$rowid.'_commission_id" name="budgetline['.$rowid.'][commissionSplitAffid]"/></td>'
                    );
                }
                if(empty($budgetline['customerCountry'])) {
                    $budgetline['customerCountry'] = $aff->country;
                }
                $countries = Countries::get_coveredcountries();
                $countries_selectlist = parse_selectlist('budgetline['.$rowid.'][customerCountry]', 0, $countries, $budgetline['customerCountry'], '', '', '');

                if($source == 'budget' || $budgetline['fromBudget'] == 1 || $budgetline['source'] == 'userprevlines') {
                    if(!empty($budgetline['customerCountry'])) {
                        $country = new Countries($budgetline['customerCountry']);
                        if(!empty($country->coid)) {
                            $countries_selectlist = $country->get_displayname().'<input type="hidden" name="budgetline['.$rowid.'][customerCountry]" value="'.$country->coid.'">';
                        }
                    }
                }
//                        $altcid = $budgetline['altCid'];
//                        if(empty($altcid)) {
//                            $altcid = $prev_budgetline['altCid'];
//                        }


                eval("\$budgetlinesrows .= \"".$template->get('budgeting_fill_yeflines')."\";");
                unset($readonly, $extra_script, $alert_div, $maxbudgetline, $intercompany_obj, $tooltip);
            }
        }
        $rownums++;
    }
    $result = array('lines' => $budgetlinesrows, 'rows' => $rownums);
    return $result;
}
?>

