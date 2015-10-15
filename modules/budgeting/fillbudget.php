<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: create.php
 * Created:        @tony.assaad    Aug 12, 2013 | 3:28:10 PM
 * Last Update:    @tony.assaad    Aug 22, 2013 | 3:28:10 PM
 *
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['canUseBudgeting'] == 0) {
    error($lang->sectionnopermission);
}

if(isset($core->input['identifier']) && !empty($core->input['identifier'])) {
    $sessionidentifier = $core->input['identifier'];
}
else {
    $sessionidentifier = md5(uniqid(microtime()));
}

$session->name_phpsession(COOKIE_PREFIX.'fillbudget'.$sessionidentifier);
$session->start_phpsession(480);

if(!$core->input['action']) {
    if($core->input['stage'] == 'fillbudgetline') {
        $display = 'none';
        $session->set_phpsession(array('budgetdata_'.$sessionidentifier => serialize($core->input['budget'])));
        $budget_data = $core->input['budget'];
        $affiliate = new Affiliates($budget_data['affid']);
        $budget_data['affiliateName'] = $affiliate->get()['name'];
        $supplier = new Entities($budget_data['spid']);
        $budget_data['supplierName'] = $supplier->get()['companyName'];
        $sup_segments = $supplier->get_segments();
        if(!is_array($sup_segments)) {
            error($lang->supplierhasnosegment);
        }
        $supplier_segments = array_filter($sup_segments);
        $currentbudget = Budgets::get_budget_bydata($budget_data);

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
        $fromusers = UsersTransferedAssignments::get_data(array('toUser' => $core->user['uid'], 'affid' => $budget_data['affid'], 'eid' => $budget_data['spid']), array('returnarray' => true));
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
        /* Validate Permissions - END */
        if($currentbudget != false) {
            $budgetobj = new Budgets($currentbudget['bid']);
            if($budgetobj->isLocked == 1) {
                error('Budget is Locked');
            }
            $budget_data['bid'] = $currentbudget['bid'];
            $budgetlinesdata = $budgetobj->get_budgetLines('', $filter);
            if(!is_array($budgetlinesdata) || empty($budgetlinesdata)) {
                $budgetlinesdata = $budgetobj->read_prev_budgetbydata('', $filter);
                $is_prevonly = true;
            }
            $session->set_phpsession(array('budgetmetadata_'.$sessionidentifier => serialize($currentbudget)));
        }
        else {
            $budgetobj = new Budgets();
            if($core->input['source'] == 'sales') {
                $data['integrationOBOrgId'] = $affiliate->integrationOBOrgId;
                $budgetlinesdata = $budgetobj->read_prev_sales($budget_data, $filter);
            }
            else {
                $budgetlinesdata = $budgetobj->read_prev_budgetbydata($budget_data, $filter);
            }
            $is_prevonly = true;
            $session->set_phpsession(array('budgetmetadata_'.$sessionidentifier => serialize($core->input)));
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
        $saleinvoice_query = $db->query('SELECT * FROM '.Tprefix.'saletypes_invoicing WHERE isActive = 1 AND affid = '.intval($budget_data['affid']));
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
//      $salepurchase_query = $db->query('SELECT * FROM '.Tprefix.'saletypes_invoicing WHERE isActive = 1 AND affid = '.intval($budget_data['affid']));
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
        //$currencies = get_specificdata('currencies', array('numCode', 'alphaCode'), 'numCode', 'alphaCode', array('by' => 'alphaCode', 'sort' => 'ASC'), 1, 'numCode = '.$affiliate_currency);
        $affiliate_currency = new Currencies($affiliate->get_country()->get()['mainCurrency']);
        $currencies = array_filter(array(840 => 'USD', 978 => 'EUR'));
        $currency['filter']['numCode'] = 'SELECT mainCurrency FROM countries where affid IS NOT NULL';
        $curr_objs = Currencies::get_data($currency['filter'], array('returnarray' => true, 'operators' => array('numCode' => 'IN')));
        foreach($curr_objs as $curr_obj) {
            $currencies[$curr_obj->get_id()] = $curr_obj->alphaCode;
        }

        /* check whether to display existing budget Form or display new one  */
        $unsetable_fields = array('quantity', 'amount', 'incomePerc', 'income', 'inputChecksum');
        $countries = Countries::get_coveredcountries();
        if(is_array($budgetlinesdata)) {
            $rowid = 1;
            foreach($budgetlinesdata as $cid => $customersdata) {
                /* Get Customer name from object */
                if(!is_int($cid)) {
                    $cid = 0;
                }
                $customer = new Entities($cid);
                foreach($customersdata as $pid => $productsdata) {
                    /* Get Products name from object */
                    $product = new Products($pid);

//				if(isset($budgetline[$rowid]['cid']) && !empty($budgetline[$rowid]['cid'])) {
//					$required = ' required = "required"';
//				}

                    foreach($productsdata as $saleid => $budgetline) {
                        unset($disabledattrs);
                        if(!empty($budgetline['cid'])) {
                            $disabledattrs['cid'] = $disabledattrs['unspecifiedCustomer'] = 'disabled="disabled"';
                        }
                        $previous_yearsqty = $previous_yearsamount = $previous_yearsincome = $prevyear_incomeperc = $prevyear_unitprice = $previous_actualqty = $previous_actualamount = $previous_actualincome = '';
                        if($is_prevonly === true || isset($budgetline['prevbudget'])) {
                            if($is_prevonly == true) {
                                $prev_budgetlines = $budgetline;
                                unset($budgetline['businessMgr']);
                            }
                            elseif(isset($budgetline['prevbudget'])) {
                                $prev_budgetlines = $budgetline['prevbudget'];
                            }
                            foreach($prev_budgetlines as $prev_budgetline) {
                                //get prev year YEF data
                                $yefline = BudgetingYEFLines::get_data(array('blid' => $prev_budgetline['blid']), array('returnarray' => false));
                                if(!is_object($yefline)) {
                                    $yefline = new BudgetingYEFLines();
                                }

                                if(!isset($budgetline['invoice'])) {
                                    $budgetline['invoice'] = $prev_budgetline['invoice'];
                                }
                                if($is_prevonly == true) {
                                    $unsetable_fields = array('blid', 'unitPrice', 'quantity', 'amount', 'incomePerc', 'income', 'inputChecksum');
                                    foreach($unsetable_fields as $field) {
                                        unset($budgetline[$field]);
                                    }
                                }
                                /* Get Actual data from mediation tables --START */

//								if(empty($budgetline['actualQty']) || empty($budgetline['actualincome']) || empty($budgetline['actualamount'])) {
//									$mediation_actual = $budgetobj->get_actual_meditaiondata(array('pid' => $prev_budgetline['pid'], 'cid' => $prev_budgetline['cid'], 'saleType' => $prev_budgetline['saleType']));
//
//									$budgetLines['actualQty'] = $mediation_actual['quantity'];
//									$actualqty = '<input type = "hidden" name = '.$budgetline['quantity'].' value = '.$budgetLines['actualQty'].' />';
//									$budgetLines['actualamount'] = $mediation_actual['cost'];
//									$budgetLines['actualincome'] = $mediation_actual['price'];
//								}
                                $budgetline['alternativecustomer'] .= '<span style="display:block;">'.ucfirst($prev_budgetline['altCid']).'</span>';
                                if(!empty($budgetline['cid']) || !empty($budgetline['altCid']) || $prev_budgetline['altCid'] == 'Unspecified Customer') {
                                    unset($budgetline['alternativecustomer']);
                                }
                                if(!empty($prev_budgetline['altPid'])) {
                                    $budgetline['alternativeproduct'] .= '<span style="display:block;">'.ucfirst($prev_budgetline['altPid']).'</span>';
                                }
                                if(empty($prev_budgetline['year'])) {
                                    $prev_budget = new Budgets($prev_budgetline['bid']);
                                    $prev_budgetline['year'] = $prev_budget->year;
                                }
                                $previous_blid = '<input type="hidden" name="budgetline['.$rowid.'][prevblid]" value="'.$prev_budgetline['blid'].'" />';
                                // $previous_customercountry = '<input type="hidden" name="budgetline['.$rowid.'][customerCountry]" value="'.$prev_budgetline['customerCountry'].'" />';
                                $previous_yearsqty .= '<span class="altrow smalltext" style="display:block;"><strong>'.$prev_budgetline['year'].'</strong><br />'.$lang->budgetabbr.': '.$prev_budgetline['quantity'].' | '.$lang->actualabbr.': '.$prev_budgetline['actualQty'].' | '.$lang->yef.': '.$yefline->quantity.'</span>';
                                $previous_yearsamount .= '<span class="altrow smalltext" style="display:block;"><strong>'.$prev_budgetline['year'].'</strong><br />'.$lang->budgetabbr.': '.$prev_budgetline['amount'].' | '.$lang->actualabbr.': '.$prev_budgetline['actualAmount'].' | '.$lang->yef.': '.$yefline->amount.'</span>';
                                $previous_yearsincome .= '<span class="altrow smalltext" style="display:block;"><strong>'.$prev_budgetline['year'].'</strong><br />'.$lang->budgetabbr.': '.$prev_budgetline['income'].' | '.$lang->actualabbr.': '.$prev_budgetline['actualIncome'].' | '.$lang->yef.': '.$yefline->income.'</span>';
                                $previous_yearslocalincome .= '<span class="altrow smalltext" style="display:block;"><strong>'.$prev_budgetline['year'].'</strong><br />'.$lang->budgetabbr.': '.$prev_budgetline['localIncomeAmount'].' | '.$lang->actualabbr.':  | '.$lang->yef.': '.$yefline->localIncomeAmount.'</span>';

                                $prev_budgetline['actualIncomePerc'] = 0;
                                if(!empty($prev_budgetline['actualAmount'])) {
                                    $prev_budgetline['actualIncomePerc'] = round(($prev_budgetline['actualIncome'] * 100) / $prev_budgetline['actualAmount'], 2);
                                }
                                $prevyear_incomeperc .= '<span class="altrow smalltext" style="display:block;"><strong>'.$prev_budgetline['year'].'</strong><br />'.$lang->budgetabbr.': '.$prev_budgetline['incomePerc'].' | '.$lang->actualabbr.': '.$prev_budgetline['actualIncomePerc'].' | '.$lang->yef.': '.$yefline->incomePerc.'</span>';

                                $prev_budgetline['actualUnitPrice'] = 0;
                                if(!empty($prev_budgetline['actualQty'])) {
                                    $prev_budgetline['actualUnitPrice'] = round($prev_budgetline['actualAmount'] / $prev_budgetline['actualQty'], 2);
                                }
                                $prevyear_unitprice .= '<span class="altrow smalltext" style="display:block;"><strong>'.$prev_budgetline['year'].'</strong><br />'.$lang->budgetabbr.': '.$prev_budgetline['unitPrice'].' | '.$lang->actualabbr.': '.$prev_budgetline['actualUnitPrice'].' | '.$lang->yef.': '.$yefline->unitPrice.'</span>';

                                $altcid = $budgetline['altCid'];
                                if(empty($altcid)) {
                                    $altcid = $prev_budgetline['altCid'];
                                }

                                /**
                                 * Below to be replaced by loop
                                 */
                                if(empty($budgetline['customerCountry'])) {
                                    $budgetline['customerCountry'] = $prev_budgetline['customerCountry'];
                                }

                                if(empty($budgetline['purchasingEntity'])) {
                                    $budgetline['purchasingEntity'] = $prev_budgetline['purchasingEntity'];
                                }

                                if(empty($budgetline['localIncomePercentage'])) {
                                    $budgetline['localIncomePercentage'] = $prev_budgetline['localIncomePercentage'];
                                }

                                if(!empty($prev_budgetline['interCompanyPurchase'])) {
                                    $budgetline['interCompanyPurchase'] = $prev_budgetline['interCompanyPurchase'];
                                    $intercompany_obj = new Affiliates($prev_budgetline['interCompanyPurchase']);
                                    $budgetline['interCompanyPurchase_output'] = $intercompany_obj->get_displayname();
                                }
                                if(!empty($prev_budgetline['commissionSplitAffid'])) {
                                    $budgetline['commissionSplitAffid'] = $prev_budgetline['commissionSplitAffid'];
                                    $intercompany_obj = new Affiliates($prev_budgetline['commissionSplitAffid']);
                                    $budgetline['commissionSplitAffid_output'] = $intercompany_obj->get_displayname();
                                }

                                $budgetline['originalCurrency'] = $prev_budgetline['originalCurrency'];
                                $budgetline['psid'] = $prev_budgetline['psid'];
                            }
                        }
                        if(empty($budgetline['localIncomePercentage'])) {
                            $budgetline['localIncomePercentage'] = 0;
                        }
                        if(empty($budgetline['localIncomeAmount'])) {
                            $budgetline['localIncomeAmount'] = 0;
                        }
                        $budgetline['altCid'] = $altcid;
                        $budgetline['cid'] = $cid;
                        $budgetline['customerName'] = $customer->get()['companyName'];
                        $budgetline['pid'] = $pid;
                        $budgetline['productName'] = $product->get()['name'];
                        $saletype_selectlist = parse_selectlist('budgetline['.$rowid.'][saleType]', 0, $saletype_selectlistdata, $saleid, '', '', array('id' => 'salestype_'.$rowid));
                        $invoice_selectlist = parse_selectlist('budgetline['.$rowid.'][invoice]', 0, $invoice_selectlistdata, $budgetline['invoice'], '', '', array('id' => 'invoice_'.$rowid));

                        if(empty($budgetline['purchasingEntity'])) {
                            $budgetline['purchasingEntity'] = 'direct';
                        }
                        $purchase_selectlistdata = array('alex' => 'Orkila FZ - Alex', 'fze' => 'Orkila Jebel Ali FZE', 'int' => 'Orkila International', 'customer' => 'Customer', 'direct' => $budget_data['affiliateName']);
                        $purchasingentity_selectlist = parse_selectlist('budgetline['.$rowid.'][purchasingEntity]', 0, $purchase_selectlistdata, $budgetline['purchasingEntity'], '', '', array('id' => 'purchasingEntity_'.$rowid));
                        $display = 'none';

                        if(empty($budgetline['cid']) && $budgetline['altCid'] == 'Unspecified Customer') {
                            $checked_checkboxes[$rowid]['unspecifiedCustomer'] = ' checked="checked"';
                            $display = 'block';
                        }
                        if(empty($budgetline['cid']) && $budgetline['altCid'] != 'Unspecified Customer') {
                            $budgetline['alternativecustomer'] = '<span style="display:block;">'.ucfirst($budgetline['altCid']).'</span>';
                            $prev_budgetline['altCid'] = $budgetline['altCid'];
                            if(!empty($budgetline['customerCountry'])) {
                                $display = 'block';
                            }
                        }

                        /* Get Actual data from mediation tables --END */
                        $budget_currencylist = '';
                        foreach($currencies as $numcode => $currency) {
                            if($budgetline['originalCurrency'] == $numcode) {
                                $budget_currencylist_selected = ' selected="selected"';
                            }
                            $budget_currencylist .= '<option value="'.$numcode.'"'.$budget_currencylist_selected.'>'.$currency.'</option>';
                            $budget_currencylist_selected = '';
                        }
                        if(!empty($budgetline['interCompanyPurchase'])) {
                            $intercompany_obj = new Affiliates($budgetline['interCompanyPurchase']);
                            $budgetline['interCompanyPurchase_output'] = $intercompany_obj->get_displayname();
                        }
                        if(!empty($budgetline['commissionSplitAffid'])) {
                            $intercompany_obj = new Affiliates($budgetline['commissionSplitAffid']);
                            $budgetline['commissionSplitAffid_output'] = $intercompany_obj->get_displayname();
                        }
                        $segments_selectlist = '';
                        if(count($supplier_segments) > 1) {
                            $segments_selectlist = parse_selectlist('budgetline['.$rowid.'][psid]', 3, $supplier_segments, $budgetline['psid'], null, null, array('placeholder' => 'Overwrite Segment'));
                        }

                        if($core->usergroup['budgeting_canFillLocalIncome'] == 1) {
                            $hidden_colcells = array('localincome_row' => ' <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center"><input name="budgetline['.$rowid.'][localIncomeAmount]"  value="'.$budgetline['localIncomeAmount'].'"  type="text" id="localincome_'.$rowid.'" size="10" accept="numeric" /> </td>',
                                    'localincomeper_row' => '<td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center"><input name="budgetline['.$rowid.'][localIncomePercentage]"  value="'.$budgetline['localIncomePercentage'].'" type="text" id="localincomeper_'.$rowid.'" size="10" accept="numeric"  /> </td>',
                                    'remainingcommaff_header_row' => '<td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center"><input type="text" placeholder="'.$lang->search.' '.$lang->affiliate.'" id="affiliate_noexception_'.$rowid.'_commission_autocomplete" name=""  value="'.$budgetline['commissionSplitAffid_output'].'" autocomplete="off" /><input type="hidden" value="'.$budgetline['commissionSplitAffid'].'" id="affiliate_noexception_'.$rowid.'_commission_id" name="budgetline['.$rowid.'][commissionSplitAffid]"/></td>'
                            );
                        }
                        else {
                            $hidden_colcells = array('localincome_row' => ' <td style="display:none"><input name="budgetline['.$rowid.'][localIncomeAmount]"  value="'.$budgetline['localIncomeAmount'].'" id="localincome_'.$rowid.'" type="hidden" /> </td>',
                                    'localincomeper_row' => '<td style="display:none" ><input name="budgetline['.$rowid.'][localIncomePercentage]"  value="'.$budgetline['localIncomePercentage'].'" type="hidden" id="localincomeper_'.$rowid.'"  /> </td>',
                                    'remainingcommaff_header_row' => '<td style="display:none"><input type="hidden" value="'.$budgetline['commissionSplitAffid'].'" id="affiliate_noexception_'.$rowid.'_commission_id" name="budgetline['.$rowid.'][commissionSplitAffid]"/></td>'
                            );
                        }

                        if(empty($budgetline['inputChecksum'])) {
                            $budgetline['inputChecksum'] = generate_checksum('bl');
                        }

                        if(empty($budgetline['customerCountry'])) {
                            $budgetline['customerCountry'] = $affiliate->country;
                        }
                        $countries_selectlist = parse_selectlist('budgetline['.$rowid.'][customerCountry]', 0, $countries, $budgetline['customerCountry'], '', '', '');

//                        $altcid = $budgetline['altCid'];
//                        if(empty($altcid)) {
//                            $altcid = $prev_budgetline['altCid'];
//                        }

                        eval("\$budgetlinesrows .= \"".$template->get('budgeting_fill_lines')."\";");
                        unset($yefline);
                        $rowid++;
                    }
                }
            }
        }
        else {
            $rowid = 1;
            $saletype_selectlist = parse_selectlist('budgetline['.$rowid.'][saleType]', 0, $saletype_selectlistdata, '', '', '', array('id' => 'salestype_'.$rowid));
            $invoice_selectlist = parse_selectlist('budgetline['.$rowid.'][invoice]', 0, $invoice_selectlistdata, '', '', '', array('id' => 'invoice_'.$rowid));
            $purchase_selectlistdata = array('alex' => 'Orkila FZ - Alex', 'fze' => 'Orkila Jebel Ali FZE', 'int' => 'Orkila International', 'customer' => 'Customer', 'direct' => $budget_data['affiliateName']);
            $purchasingentity_selectlist = parse_selectlist('budgetline['.$rowid.'][purchasingEntity]', 0, $purchase_selectlistdata, 'direct', '', '', array('id' => 'purchasingEntity_'.$rowid));
            foreach($currencies as $numcode => $currency) {
                $budget_currencylist .= '<option value="'.$numcode.'">'.$currency.'</option>';
            }

            if(count($supplier_segments) > 1) {
                $segments_selectlist = parse_selectlist('budgetline['.$rowid.'][psid]', 3, $supplier_segments, 0, null, null, array('placeholder' => 'Overwrite Segment'));
            }
            if($core->usergroup['budgeting_canFillLocalIncome'] == 1) {
                $hidden_colcells = array('localincome_row' => ' <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center"><input name="budgetline['.$rowid.'][localIncomeAmount]"  value="'.$budgetline[localIncomeAmount].'"  type="text" id="localincome_'.$rowid.'" size="10" accept="numeric" /> </td>',
                        'localincomeper_row' => '<td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center"><input name="budgetline['.$rowid.'][localIncomePercentage]"  value="'.$budgetline[localIncomePercentage].'" type="text" id="localincomeper_'.$rowid.'" size="10" accept="numeric"  /> </td>',
                        'remainingcommaff_header_row' => '<td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center">
             <input type="text" placeholder="'.$lang->search.' '.$lang->affiliate.'" id=affiliate_noexception_'.$rowid.'_commission_autocomplete name=""  value="'.$budgetline['commissionSplitAffid_output'].'" autocomplete="off" />
        <input type="hidden" value="'.$budgetline['commissionSplitAffid'].'" id="affiliate_noexception_'.$rowid.'_commission_id" name="budgetline['.$rowid.'][commissionSplitAffid]"/></td>'
                );
            }
            else {
                $hidden_colcells = array('localincome_row' => ' <td style="display:none"><input name="budgetline['.$rowid.'][localIncomeAmount]"  value="'.$budgetline['localIncomeAmount'].'" id="localincome_'.$rowid.'" type="hidden" /> </td>',
                        'localincomeper_row' => '<td style="display:none" ><input name="budgetline['.$rowid.'][localIncomePercentage]"  value="'.$budgetline['localIncomePercentage'].'" type="hidden" id="localincomeper_'.$rowid.'"  /> </td>',
                        'remainingcommaff_header_row' => '<td style="display:none"><input type="hidden" value="'.$budgetline['commissionSplitAffid'].'" id="affiliate_noexception_'.$rowid.'_commission_id" name="budgetline['.$rowid.'][commissionSplitAffid]"/></td>'
                );
            }
            $budgetline['inputChecksum'] = generate_checksum('bl');
            $countries_selectlist = parse_selectlist('budgetline['.$rowid.'][customerCountry]', 0, $countries, $affiliate->country, '', '', '');
            eval("\$budgetlinesrows .= \"".$template->get('budgeting_fill_lines')."\";");
        }
        unset($saletype_selectlistdata, $checked_checkboxes);

        /* Parse values for JS - START */
        foreach($saletypes as $stid => $saletype) {
            if($saletype['useLocalCurrency'] == 1) {
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
            $hidden_colcells = array('localincome_head' => '<td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">'.$lang->localincome.'<a href="#" title="'.$lang->localincomeexp.'"><img src="./images/icons/question.gif" ></a></td>',
                    'localincomeper_head' => '<td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">'.$lang->localincomeper.'</td>',
                    'remainingcommaff_head' => '<td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">'.$lang->remainingcommaff.'<a href="#" title="The affiliate which will keep the remaining commission"><img src="./images/icons/question.gif" ></a></td>'
            );
        }
        else {
            $hidden_colcells = array('localincome_head' => '<td style="display:none"></td>',
                    'localincomeper_head' => '<td style="display:none"></td>',
                    'remainingcommaff_head' => '<td style="display:none"></td>'
            );
        }

        eval("\$fillbudget = \"".$template->get('budgeting_fill')."\";");
        output_page($fillbudget);
    }
}
else {
    if($core->input['action'] == 'do_perform_fillbudget') {
        $budget_data = unserialize($session->get_phpsession('budgetdata_'.$core->input['identifier']));

        $keydata = array('year', 'spid', 'affid');
        foreach($keydata as $attr) {
            $budget_data[$attr] = $core->input[$attr];
        }
        if(is_array($core->input['budgetline'])) {
            if(isset($core->input['budget']['bid'])) {
                $currentbudget = $core->input['budget'];
                $budget = new Budgets($core->input['budget']['bid']);
            }
            else {
                $currentbudget = Budgets::get_budget_bydata($budget_data);
            }
            if(is_array($currentbudget) && !empty($currentbudget['bid'])) {
                $budget_data['bid'] = $currentbudget['bid'];
            }
            if(is_object($budget)) {
                $budget->save_budget($budget_data, $core->input['budgetline']);
            }
            else {
                $budget = new Budgets();
                $budget->save_budget($budget_data, $core->input['budgetline']);
            }
        }
        switch($budget->get_errorcode()) {
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
    elseif($core->input['action'] == 'ajaxaddmore_budgetlines') {
        $display = 'none';
        $rowid = intval($core->input['value']) + 1;
        $budget_data = $core->input['ajaxaddmoredata'];
        $affiliate = new Affiliates($budget_data['affid']);
        $budgetline['inputChecksum'] = generate_checksum('bl');
        if($affiliate->isIntReinvoiceAffiliate == 0) {
            $saletypes_query_where = ' WHERE stid NOT IN (SELECT s1.invoiceAffStid FROM saletypes s1 WHERE s1.invoiceAffStid IS NOT NULL)';
        }
        $saletypes_query = $db->query('SELECT * FROM '.Tprefix.'saletypes'.$saletypes_query_where);
        while($saletype = $db->fetch_assoc($saletypes_query)) {
            $saletype_selectlistdata[$saletype['stid']] = $saletype['title'];
            $saletypes[$saletype['stid']] = $saletype;
        }

        /* Get Invoice Types - START */
        $saleinvoice_query = $db->query('SELECT * FROM '.Tprefix.'saletypes_invoicing WHERE isActive=1 AND affid='.intval($budget_data['affid']));
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
        $saletype_selectlist = parse_selectlist('budgetline['.$rowid.'][saleType]', 0, $saletype_selectlistdata, $budgetline['saleType'], '', '', array('id' => 'salestype_'.$rowid));
        $invoice_selectlist = parse_selectlist('budgetline['.$rowid.'][invoice]', 0, $invoice_selectlistdata, $budgetline['invoice'], '', '', array('blankstart' => 0, 'id' => 'invoice_'.$rowid));

        /* Get budget data */

        $affiliate_currency = new Currencies($affiliate->get_country()->get()['mainCurrency']);
        $currencies = array_filter(array(840 => 'USD', 978 => 'EUR', $affiliate_currency->get()['numCode'] => $affiliate_currency->get()['alphaCode']));
        foreach($currencies as $numcode => $currency) {
            $budget_currencylist .= '<option value="'.$numcode.'">'.$currency.'</option>';
        }
        /* Parse  local amount felds based on specific permission */
        if($core->usergroup['budgeting_canFillLocalIncome'] == 1) {
            $hidden_colcells = array('localincome_row' => ' <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center"><input name="budgetline['.$rowid.'][localIncomeAmount]"  value="'.$budgetline[localIncomeAmount].'"  type="text" id="localincome_'.$rowid.'" size="10" accept="numeric" /> </td>',
                    'localincomeper_row' => '<td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center"><input name="budgetline['.$rowid.'][localIncomePercentage]"  value="'.$budgetline[localIncomePercentage].'" type="text" id="localincomeper_'.$rowid.'" size="10" accept="numeric"  /> </td>',
                    'remainingcommaff_header_row' => '<td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center">
             <input type="text" placeholder="'.$lang->search.' '.$lang->affiliate.'" id=affiliate_noexception_'.$rowid.'_commission_autocomplete name=""  value="'.$budgetline['commissionSplitAffid_output'].'" autocomplete="off" />
        <input type="hidden" value="'.$budgetline['commissionSplitAffid'].'" id="affiliate_noexception_'.$rowid.'_commission_id" name="budgetline['.$rowid.'][commissionSplitAffid]"/></td>'
            );
        }


        $purchase_selectlistdata = array('alex' => 'Orkila FZ - Alex', 'fze' => 'Orkila Jebel Ali FZE', 'int' => 'Orkila International', 'customer' => 'Customer', 'direct' => $affiliate->get_displayname());

        $purchasingentity_selectlist = parse_selectlist('budgetline['.$rowid.'][purchasingEntity]', 0, $purchase_selectlistdata, 'direct', '', '', array('id' => 'purchasingEntity_'.$rowid));
        $countries = Countries::get_coveredcountries();
        $countries_selectlist = parse_selectlist('budgetline['.$rowid.'][customerCountry]', 0, $countries, $affiliate->country, '', '');

        eval("\$budgetlinesrows = \"".$template->get('budgeting_fill_lines')."\";");
        output($budgetlinesrows);
    }
}
?>
