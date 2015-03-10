<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: managearodouments.php
 * Created:        @tony.assaad    Feb 11, 2015 | 11:53:19 AM
 * Last Update:    @tony.assaad    Feb 11, 2015 | 11:53:19 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['aro_canUseAro'] == 0) {
    //error($lang->sectionnopermission);
    // exit;
}

if(!($core->input['action'])) {
    /* Order idendtifications */
    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = $core->user['affiliates'];
    }
    if(is_array($inaffiliates)) {
        foreach($inaffiliates as $affid) {
            $affiliate[$affid] = new Affiliates($affid);
        }
    }
    else {
        $affiliate = Affiliates::get_affiliates();
    }

    $purchasetypes = PurchaseTypes::get_data('name IS NOT NULL', array('returnarray' => true));
    if(!is_array($purchasetypes)) {
        error($lang->missingconfigurations.' (Purchase Types)');
    }
    $inspections = array('inspection1' => 'inspection');
    $payment_terms = PaymentTerms::get_data('', array('returnarray' => ture));
    $segments = ProductsSegments::get_segments('');
    $packaging = Packaging::get_data('name IS NOT NULL', array('returnarray' => ture));
    $uom = Uom::get_data('name IS NOT NULL', array('returnarray' => ture));
    $mainaffobj = new Affiliates($core->user['mainaffiliate']);
    $currencies = Currencies::get_data();

    if(!isset($core->input['id'])) {
        //order identification
        $affiliate_list = parse_selectlist('affid', 1, $affiliate, $orderid[affid], '', '', array('blankstart' => true, 'id' => "affid", 'required' => 'required'));
        $purchasetypelist = parse_selectlist('orderType', 4, $purchasetypes, $orderid['ptid'], '', '', array('blankstart' => true, 'id' => "purchasetype", 'required' => 'required'));
        $currencies_list = parse_selectlist('currency', 4, $currencies, '', '', '', array('blankstart' => 1, 'id' => "currencies", 'required' => 'required'));
        $inspectionlist = parse_selectlist('inspectionType', 4, $inspections, '');

        //order Customers
        $customeroder['inputChecksum'] = generate_checksum('ucl');
        $rowid = 0;
        $altpayment_term = parse_selectlist('customeroder['.$rowid.'][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
        eval("\$unspecified_customer_row = \"".$template->get('aro_unspecifiedcustomer_row')."\";");
        $rowid++;
        $customeroder['inputChecksum'] = generate_checksum('cl');
        $payment_term = parse_selectlist('customeroder['.$rowid.'][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
        eval("\$aro_managedocuments_ordercustomers_rows = \"".$template->get('aro_managedocuments_ordercustomers_rows')."\";");

        //product Lines
        $plrowid = 1;
        $productline['inputChecksum'] = generate_checksum('pl');
        $segments_selectlist = parse_selectlist('productline['.$plrowid.'][psid]', '', $segments, '', null, null, array('id' => "productline_".$plrowid."_psid", 'placeholder' => 'Overwrite Segment', 'width' => '100%'));
        $packaging_list = parse_selectlist('productline['.$plrowid.'][packing]', '', $packaging, '', '', '', array('id' => "productline_".$plrowid."_packing", 'blankstart' => 1));
        $uom_list = parse_selectlist('productline['.$plrowid.'][uom]', '', $uom, '', '', '', array('id' => "productline_".$plrowid."_uom", 'blankstart' => 1, 'width' => '70px'));
        eval("\$aroproductlines_rows = \"".$template->get('aro_productlines_row')."\";");

        //Net Margin Parameters
        $netmarginparms_uomlist = parse_selectlist('parmsfornetmargin[uom]', '', $uom, '', '', '', array('id' => "parmsfornetmargin_uom", 'blankstart' => 1, 'width' => '70px'));

        // eval("\$actualpurchase_rows = \"".$template->get('aro_actualpurchase_row')."\";");
    }

    if(isset($core->input['id'])) {
        $aroorderrequest = AroRequests::get_data(array('aorid' => $core->input['id']), array('simple' => false));
        $purchasetype = new PurchaseTypes($aroorderrequest->orderType);

        if(is_object($aroorderrequest)) {
            $affiliate_list = parse_selectlist('affid', 1, $affiliate, $aroorderrequest->affid, '', '', array('blankstart' => true, 'id' => 'affid', 'required' => 'required'));
            $purchasetypelist = parse_selectlist('orderType', 4, $purchasetypes, $aroorderrequest->orderType, '', '', array('blankstart' => true, 'id' => 'purchasetype', 'required' => 'required'));
            $currencies_list = parse_selectlist('currency', 4, $currencies, $aroorderrequest->currency, '', '', array('blankstart' => 1, 'id' => 'currencies', 'required' => 'required'));
            $inspectionlist = parse_selectlist('inspectionType', 4, $inspections, $aroorderrequest->inspectionType);
            //*********Aro Order Customers -Start *********//
            $requestcustomers = AroOrderCustomers::get_data(array('aorid' => $aroorderrequest->aorid), array('returnarray' => true));
            $rowid = 1;
            if(is_array($requestcustomers)) {
                foreach($requestcustomers as $customer) {
                    $customeroder = $customer->get();
                    $customeroder['paymenttermbasedate_output'] = date($core->settings['dateformat'], $customeroder['paymentTermBaseDate']);
                    $customeroder['paymenttermbasedate_formatted'] = date('d-m-Y', $customeroder['paymentTermBaseDate']);
                    if($customeroder['cid'] == 0) {
                        $unspecifiedcust = $customeroder;
                        continue;
                    }
                    $customer = new Customers($customeroder['cid']);
                    $payment_term = parse_selectlist('customeroder['.$rowid.'][ptid]', 4, $payment_terms, $customeroder['ptid'], '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
                    $customeroder['customerName'] = $customer->get_displayname();
                    eval("\$aro_managedocuments_ordercustomers_rows .= \"".$template->get('aro_managedocuments_ordercustomers_rows')."\";");
                    $rowid++;
                }
                $clrowid = $rowid - 1;
                $rowid = 0;
                //Always parse the unspecified customer row
                unset($customeroder);
                if(isset($unspecifiedcust) && !empty($unspecifiedcust)) {
                    $checked['unsepcifiedCustomer'] = 'checked="checked"';
                    $customeroder = $unspecifiedcust;
                }
                $customeroder['inputChecksum'] = generate_checksum('ucl');
                $altpayment_term = parse_selectlist('customeroder['.$rowid.'][ptid]', 4, $payment_terms, $unspecifiedcust['ptid'], '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
                eval("\$unspecified_customer_row = \"".$template->get('aro_unspecifiedcustomer_row')."\";");
                $rowid = $clrowid;

                // If only unspecified customer row exist, parse default customer order row
                if(empty($aro_managedocuments_ordercustomers_rows)) {
                    unset($customeroder);
                    $customeroder['inputChecksum'] = generate_checksum('cl');
                    $rowid = 1;
                    $payment_term = parse_selectlist('customeroder['.$rowid.'][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
                    eval("\$aro_managedocuments_ordercustomers_rows .= \"".$template->get('aro_managedocuments_ordercustomers_rows')."\";");
                }
            }
            else {
                $customeroder['inputChecksum'] = generate_checksum('ucl');
                $rowid = 0;
                $altpayment_term = parse_selectlist('customeroder['.$rowid.'][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
                eval("\$unspecified_customer_row = \"".$template->get('aro_unspecifiedcustomer_row')."\";");
                $rowid++;
                $customeroder['inputChecksum'] = generate_checksum('cl');
                $payment_term = parse_selectlist('customeroder['.$rowid.'][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
                eval("\$aro_managedocuments_ordercustomers_rows = \"".$template->get('aro_managedocuments_ordercustomers_rows')."\";");
            }
            //*********Aro Order Customers - End *********//
            //*********Parameters Influencing Net Margin Calculation -Start ********//
            if($purchasetype->qtyIsNotStored == 1) {
                $disabled['warehousing'] = 'disabled="disabled"';
                $readonly['warehousing'] = 'readonly="readonly"';
            }
            $netmarginparms = AroNetMarginParameters::get_data(array('aorid' => $core->input['id']));
            $netmarginparms_uomlist = parse_selectlist('parmsfornetmargin[uom]', '', $uom, $netmarginparms->uom, '', '', array('id' => "parmsfornetmargin_uom", 'blankstart' => 1, 'width' => '70px'));
            $warehouse = Warehouses::get_data(array('wid' => $netmarginparms->warehouse));
            $warehouse_list = '<select '.$disabled['warehousing'].'><option value='.$netmarginparms->warehouse.' selected>'.$warehouse->name.'</option>'
                    .'<option value="0"></option></select>';
            $netmarginparms_warehousingRate = '<option value="'.$netmarginparms->warehousingRate.'">'.$netmarginparms->warehousingRate.'</option>';
            //  $netmarginparms->warehousingRate_output
            //*********Parameters Influencing Net Margin Calculation -End ********//
            //********** ARO Product Lines -Start **************//
            $plrowid = 1;
            $productlines = AroRequestLines::get_data(array('aorid' => $aroorderrequest->aorid), array('returnarray' => true));
            if(is_array($productlines)) {
                foreach($productlines as $line) {
                    $productline = $line->get();
                    $segments_selectlist = parse_selectlist('productline['.$plrowid.'][psid]', '', $segments, $productline['psid'], null, null, array('id' => "productline_".$plrowid."_psid", 'placeholder' => 'Overwrite Segment', 'width' => '100%'));
                    $packaging_list = parse_selectlist('productline['.$plrowid.'][packing]', '', $packaging, $productline['packing'], '', '', array('id' => "productline_".$plrowid."_packing", 'blankstart' => 1));
                    $uom_list = parse_selectlist('productline['.$plrowid.'][uom]', '', $uom, $productline['uom'], '', '', array('id' => "productline_".$plrowid."_uom", 'blankstart' => 1, 'width' => '70px'));
                    $product = new Products($productline['pid']);
                    $productline[productName] = $product->get_displayname();
                    if($purchasetype->qtyIsNotStored == 1) {
                        $disabled_fields['daysInStock'] = $disabled_fields['qtyPotentiallySold'] = 'readonly="readonly"';
                    }
                    if($productline['daysInStock'] == 0) {
                        $disabled_fields['qtyPotentiallySold'] = 'readonly="readonly"';
                    }
                    eval("\$aroproductlines_rows .= \"".$template->get('aro_productlines_row')."\";");
                    $plrowid++;
                    unset($disabled_fields);
                }
            }
            else {
                $productline['inputChecksum'] = generate_checksum('pl');
                $segments_selectlist = parse_selectlist('productline['.$plrowid.'][psid]', '', $segments, '', null, null, array('id' => "productline_".$plrowid."_psid", 'placeholder' => 'Overwrite Segment', 'width' => '100%'));
                $packaging_list = parse_selectlist('productline['.$plrowid.'][packing]', '', $packaging, '', '', '', array('id' => "productline_".$plrowid."_packing", 'blankstart' => 1));
                $uom_list = parse_selectlist('productline['.$plrowid.'][uom]', '', $uom, '', '', '', array('id' => "productline_".$plrowid."_uom", 'blankstart' => 1, 'width' => '70px'));
                eval("\$aroproductlines_rows .= \"".$template->get('aro_productlines_row')."\";");
            }
            //********** ARO Product Lines **************//
            //*********Aro Actual Purchase -Start *********//
            $aroreqlinesupervision = AroRequestLinesSupervision::get_data(array('aorid' => $aroorderrequest->aorid), array('returnarray' => true));

            $rowid = 1;
            if(is_array($aroreqlinesupervision)) {
                foreach($aroreqlinesupervision as $actualpurchase) {
                    $products = new Products($actualpurchase->pid);
                    $actualpurchase->productName = $products->get_displayname();
                    $actualpurchase->estDateOfStockEntry_output = date($core->settings['dateformat'], $actualpurchase->estDateOfStockEntry);
                    $actualpurchase->estDateOfSale_output = date($core->settings['dateformat'], $actualpurchase->estDateOfSale);
                    $packaging_selected_list = parse_selectlist('productline['.$plrowid.'][packing]', '', $packaging, $actualpurchase->packing, '', '', array('id' => "productline_".$plrowid."_packing", 'blankstart' => 1));
                    eval("\$actualpurchase_rows .= \"".$template->get('aro_actualpurchase_row')."\";");
                    $rowid++;
                }
            }
            //*********Aro Actual Purchase-End *********//
            //*********Aro Audit Trail -Start *********//

            $aroorderrequest->createdOn_output = date($core->settings['dateformat'], $aroorderrequest->createdOn);
            $aroorderrequest->modifiedOn_output = date($core->settings['dateformat'], $aroorderrequest->modifiedOn);
            $createdby_username = new Users($aroorderrequest->createdBy);
            $modifiedby_username = new Users($aroorderrequest->modifiedBy);
            $aroorderrequest->createdBy_output = $createdby_username->parse_link($attributes_param = array('target' => "_blank"));
            $aroorderrequest->modifiedBy_output = $modifiedby_username->parse_link($attributes_param = array('target' => '__blank'));
            $aroorderrequest->revision_output = $aroorderrequest->revision;
            eval("\$aro_managedocuments_audittrail_rows .= \"".$template->get('aro_managedocuments_audittrail_rows')."\";");
            eval("\$aro_audittrail= \"".$template->get('aro_managedocuments_audittrail')."\";");

            //*********Aro Audit Trail -End *********//
        }
        else {
            redirect($_SERVER['HTTP_REFERER'], 2, $lang->nomatchfound);
        }
    }

    eval("\$aro_productlines = \"".$template->get('aro_fillproductlines')."\";");
    eval("\$aro_managedocuments_orderident= \"".$template->get('aro_managedocuments_orderidentification')."\";");
    eval("\$aro_ordercustomers= \"".$template->get('aro_managedocuments_ordercustomers')."\";");
    eval("\$aro_netmarginparms= \"".$template->get('aro_netmarginparameters')."\";");
    eval("\$actualpurchase = \"".$template->get('aro_actualpurchase')."\";");
    eval("\$aro_managedocuments= \"".$template->get('aro_managedocuments')."\";");
    output_page($aro_managedocuments);
}
else {
    if($core->input['action'] == 'getexchangerate') {
        $currencyobj = new Currencies('USD');
        if(isset($core->input['currency']) && !empty($core->input['currency'])) {
            $tocurrency = new Currencies($core->input['currency']);
        }
        $rateusd = $currencyobj->get_latest_fxrate($tocurrency->alphaCode, null);
        if(!empty($rateusd)) {
            $exchangerate = array('exchangeRateToUSD' => round(1 / $rateusd, 2));
        }
        else {
            $exchangerate = array('exchangeRateToUSD' => '');
        }
        echo json_encode($exchangerate);
    }
    if($core->input ['action'] == 'populatedocnum') {
        $filter['filter']['time'] = '('.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo)';

        $documentseq_obj = AroDocumentsSequenceConf::get_data(array('time' => $filter['filter']['time'], 'affid' => $core->input['affid'], 'ptid' => $core->input['ptid']), array('simple' => false, 'operators' => array('affid' => 'in', 'ptid' => 'in', 'time' => 'CUSTOMSQLSECURE')));
        if(is_object($documentseq_obj)) {
            /* create the array to be encoded each dimension of the array represent the html element in the form */
            $orderreference = array('cpurchasetype' => $core->input['ptid'], 'orderreference' => $documentseq_obj->prefix.'-'.$documentseq_obj->nextNumber.'-'.$documentseq_obj->suffix);
            echo json_encode($orderreference); //return json to the ajax request to populate in the form
        }
    }
    if($core->input['action'] == 'ajaxaddmore_newcustomer') {
        $rowid = intval($core->input['value']) + 1;
        $customeroder['inputChecksum'] = generate_checksum('cl');
        $payment_terms = PaymentTerms::get_data('', array('returnarray' => ture));
        $payment_term = parse_selectlist('customeroder['.$rowid.'][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
        eval("\$aro_managedocuments_ordercustomers_rows = \"".$template->get('aro_managedocuments_ordercustomers_rows')."\";");
        output($aro_managedocuments_ordercustomers_rows);
    }
    if($core->input['action'] == 'do_perform_managearodouments') {
        unset($core->input['module'], $core->input['action']);
        $orderident_obj = new AroRequests();
        $orderreq_obj = AroRequests::get_data(array('aorid' => $core->input['aorid']));
        if(!is_object($orderreq_obj)) {
            $orderident_obj->create($core->input);
            switch($orderident_obj->get_errorcode()) {
                case 0:
                case 1:
                    output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                    break;
                case 2:
                    output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
                    break;
                case 3:
                    output_xml('<status>false</status><message>'.$lang->productlineerror.$orderident_obj->get_errorid().'</message>');
                    break;
            }
        }
        /* get arodocument of the affid and pruchase type */
        $documentseq_obj = AroDocumentsSequenceConf::get_data(array('affid' => $core->input['affid'], 'ptid' => $core->input['orderType']), array('simple' => false, 'operators' => array('affid' => 'in', 'ptid' => 'in')));
        if(is_object($documentseq_obj)) {
            $nextsequence_number = $documentseq_obj->get_nextaro_identification();
            $core->input['nextnumid']['nextnum'] = $nextsequence_number;
        }
        $orderident_obj->set($core->input);
        $orderident_obj->save();
        switch($orderident_obj->get_errorcode()) {
            case 0:
            case 1:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            case 2:
                output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
                break;
            case 3:
                output_xml('<status>false</status><message>'.$lang->productlineerror.$orderident_obj->get_errorid().'</message>');
                break;
        }
    }
    if($core->input['action'] == 'getestimatedate') {
        if(is_array($core->input[paymentermdays])) {
            $paymentermdays = explode(',', $core->input[paymentermdays][0]);
        }

        $purchasetype = new PurchaseTypes($core->input['ptid']);
        if($purchasetype->isPurchasedByEndUser != 0) {
            echo json_encode('error');
            exit;
        }
        if(is_array($paymentermdays)) {
            foreach($paymentermdays as $paymenterm) {
                $paymentermobjs = new PaymentTerms($paymenterm, false);
                $intervalspayment_terms[] = $paymentermobjs->overduePaymentDays; //get days
                $intervalspayment_terms = array_unique($intervalspayment_terms);
                if(!empty($intervalspayment_terms)) {
                    $countintervalspayment_terms = count($intervalspayment_terms);
                    $sumintervalspayment_terms = array_sum($intervalspayment_terms);
                    $avgpaymentterms = ($sumintervalspayment_terms / $countintervalspayment_terms);
                }
            }
        }
        $avgesdateofsale = strtotime($core->input['avgesdateofsale']);  // arraysum later
        /* convert the average days of the paymentterms to days in order to sum them with the average date of sale */
        $est_averagedate = $avgpaymentterms * (86400) + $avgesdateofsale;
        $conv = date($core->settings['dateformat'], ($est_averagedate));
        echo json_encode(array('avgeliduedate' => $conv)); //return json to the ajax request to populate in the form
    }
    if($core->input['action'] == 'ajaxaddmore_productline') {
        $plrowid = intval($core->input['value']) + 1;
        $display = 'none';
        $productlines_data = $core->input['ajaxaddmoredata'];
        $productline['inputChecksum'] = generate_checksum('pl');
        $packaging = Packaging::get_data('name IS NOT NULL');
        $segments = ProductsSegments::get_segments('');
        $segments_selectlist = parse_selectlist('productline['.$plrowid.'][psid]', '', $segments, '', null, null, array('id' => "productline_".$plrowid."_psid", 'placeholder' => 'Overwrite Segment', 'width' => '100%'));
        $packaging_list = parse_selectlist('productline['.$plrowid.'][packing]', '', $packaging, '', '', '', array('id' => "productline_".$plrowid."_packing", 'blankstart' => 1));
        $uom = Uom::get_data('name IS NOT NULL');
        $uom_list = parse_selectlist('productline['.$plrowid.'][uom]', '', $uom, '', '', '', array('id' => "productline_".$plrowid."_uom", 'blankstart' => 1, 'width' => '70px'));
        eval("\$aroproductlines_rows = \"".$template->get('aro_productlines_row')."\";");
        output($aroproductlines_rows);
    }
    if($core->input['action'] == 'disablefields') {
        $purchasetype = new PurchaseTypes($core->input['ptid']);
        $data['daysInStock_disabled'] = $data['qtyPotentiallySold_disabled'] = $data['parmsfornetmargin_warehousing_disabled'] = 1;
        if(is_object($purchasetype)) {
            if($purchasetype->qtyIsNotStored == 1) {
                $data['daysInStock_disabled'] = 0;
                $data['qtyPotentiallySold_disabled'] = 0;
                $data['parmsfornetmargin_warehousing_disabled'] = 0;
            }
        }
        $productline = array('productline_qtyPotentiallySold_disabled' => $data['qtyPotentiallySold_disabled'],
                'productline_daysInStock_disabled' => $data['daysInStock_disabled'],
                'parmsfornetmargin_warehousing_disabled' => $data['parmsfornetmargin_warehousing_disabled']);
        echo json_encode($productline);
    }
    if($core->input['action'] == 'populateproductlinefields') {
        $productline_obj = new AroRequestLines();
        $rowid = $core->input['rowid'];
        unset($core->input['action'], $core->input['module'], $core->input['rowid']);
        $parmsfornetmargin = array('localPeriodOfInterest', 'localBankInterestRate', 'warehousingPeriod', 'warehousingTotalLoad', 'warehousingRate', 'intermedBankInterestRate', 'intermedPeriodOfInterest');
        foreach($parmsfornetmargin as $parm) {
            $core->input['parmsfornetmargin'][$parm] = $core->input[$parm];
        }
        $data = $core->input;
        $productline_data = $productline_obj->calculate_values($data);
        foreach($productline_data as $key => $value) {
            if(!empty($value) || ($value == 0)) {
                $productline['productline_'.$rowid.'_'.$key] = $value;
            }
        }
        echo json_encode($productline);
    }
    if($core->input['action'] == 'populatewarehousepolicy') {
        unset($core->input['action'], $core->input['module']);
        $aroorderrequest = new AroRequests();
        $netmarginparms_data = $aroorderrequest->calculate_netmaginparms($core->input);
        foreach($netmarginparms_data as $key => $value) {
            $parmsfornetmargin['parmsfornetmargin_'.$key] = $value;
        }
        echo json_encode($parmsfornetmargin);
    }
    if($core->input['action'] == 'getwarehouses') {
        $warehouse_objs = Warehouses::get_data(array('affid' => $core->input['affid'], 'isActive' => 1), array('returnarray' => true));
        $warehouse_list = parse_selectlist('parmsfornetmargin[warehouse]', 1, $warehouse_objs, '', '', '', array('id' => 'parmsfornetmargin_warehouse', 'blankstart' => 1, 'width' => '100%'));
        output(($warehouse_list));
    }
    if($core->input['action'] == 'populateaffpolicy') {
        unset($core->input['action'], $core->input['module']);
        if($core->input['affid'] != ' ' && !empty($core->input['affid']) && !empty($core->input['ptid']) && $core->input['ptid'] != ' ') {
            $filter = 'affid='.$core->input['affid'].' AND purchaseType='.$core->input['ptid'].' AND isActive=1 AND ('.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo)';
            $localaffpolicy = AroPolicies::get_data($filter);
        }
        if(!is_object($localaffpolicy)) {
//            $localaffpolicy = new AroPolicies();
//            $localaffpolicy_data['yearlyInterestRate'] = $localaffpolicy_data['riskRatio'] = 0;
//            $localaffpolicy->set($localaffpolicy_data);
            output($lang->nopolicy);
            exit;
        }

        $core->input['intermed_affid'] = 27;
        $intermedpolicy_filter = 'affid='.$core->input['intermed_affid'].' AND purchaseType='.$core->input['ptid'].' AND isActive=1 AND ('.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo)';
        $intermedpolicy = AroPolicies::get_data($intermedpolicy_filter);
        if(!is_object($intermedpolicy)) {
            $intermedpolicy = new AroPolicies();
            $intermedpolicy_data['yearlyInterestRate'] = $intermedpolicy_data['riskRatio'] = 0;
            $intermedpolicy->set($intermedpolicy_data);
        }
        $aropolicy_data = array('parmsfornetmargin_localBankInterestRate' => $localaffpolicy->yearlyInterestRate,
                'parmsfornetmargin_localRiskRatio' => $localaffpolicy->riskRatio,
                'parmsfornetmargin_intermedBankInterestRate' => $intermedpolicy->yearlyInterestRate,
                'parmsfornetmargin_intermedRiskRatio' => $intermedpolicy->riskRatio
        );
        echo json_encode($aropolicy_data);
    }
    if($core->input['action'] == 'ajaxaddmore_actualpurchaserow') {
        $rowid = intval($core->input['value']);
        eval("\$actualpurchase_rows .= \"".$template->get('aro_actualpurchase_row')."\";");
        output($actualpurchase_rows);
    }
    if($core->input['action'] == 'populateactualpurchaserow') {
        $rowid = $core->input['rowid'];
        $core->input['totalValue'] = $core->input['totalBuyingValue'];
        unset($core->input['action'], $core->input['module'], $core->input['totalBuyingValue']);
        $actualpurchase_obj = new AroRequestLinesSupervision();
        $actualpurchase = $actualpurchase_obj->calculate_actualpurchasevalues($core->input);
        $packing = new Packaging($actualpurchase['packing']);
        $actualpurchase['packing'] = $packing->get_displayname();
        $fields = array('productName', 'pid', 'quantity', 'packing', 'totalValue', 'shelfLife', 'inputChecksum', 'daysInStock');
        foreach($fields as $field) {
            $actualpurchase_data['actualpurchase_'.$rowid.'_'.$field] = $actualpurchase[$field];
        }
        $actualpurchase_data['pickDate_from_stock_'.$rowid] = $actualpurchase[estDateOfStockEntry_output];
        $actualpurchase_data['pickDate_from_sale_'.$rowid.''] = $actualpurchase[estDateOfSale_output];
        echo json_encode($actualpurchase_data);
    }
}