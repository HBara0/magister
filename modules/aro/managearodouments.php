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
    foreach($inaffiliates as $affid) {
        $affiliate[$affid] = new Affiliates($affid);
    }

    $purchasetypes = PurchaseTypes::get_data('name IS NOT NULL', array('returnarray' => true));
    $inspections = array('inspection1' => 'inspection');
    $payment_terms = PaymentTerms::get_data('', array('returnarray' => ture));
    $segments = ProductsSegments::get_segments('');
    $packaging = Packaging::get_data('name IS NOT NULL');
    $uom = Uom::get_data('name IS NOT NULL');

    if(!isset($core->input['id'])) {
        //order identification
        $affiliate_list = parse_selectlist('affid', 1, $affiliate, $orderid[affid], '', '', array('blankstart' => true, 'id' => "affid"));
        $purchasetypelist = parse_selectlist('orderType', 4, $purchasetypes, $orderid['ptid'], '', '', array('blankstart' => true, 'id' => "purchasetype"));
        $currencies_list = parse_selectlist('currency', 4, $currencies, '', '', '', array('blankstart' => 1, 'id' => "currencies"));
        $inspectionlist = parse_selectlist('inspectionType', 4, $inspections, '');

        //order Customers
        $checksum = generate_checksum('odercustomer');
        $rowid = 1;
        $payment_term = parse_selectlist('customeroder[corder]['.$rowid.'][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
        $altpayment_term = parse_selectlist('customeroder[altcorder][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
        eval("\$aro_managedocuments_ordercustomers_rows = \"".$template->get('aro_managedocuments_ordercustomers_rows')."\";");

        //product Lines
        $plrowid = 1;
        $productline['inputChecksum'] = generate_checksum('pl');
        $segments_selectlist = parse_selectlist('productline['.$plrowid.'][psid]', '', $segments, '', null, null, array('id' => "productline_".$plrowid."_psid", 'placeholder' => 'Overwrite Segment', 'width' => '100%'));
        $packaging_list = parse_selectlist('productline['.$plrowid.'][packing]', '', $packaging, '', '', '', array('id' => "productline_".$plrowid."packing", 'blankstart' => 1));
        $uom_list = parse_selectlist('productline['.$plrowid.'][uom]', '', $uom, '', '', '', array('id' => "productline_".$plrowid."_uom", 'blankstart' => 1, 'width' => '70px'));
        eval("\$aroproductlines_rows = \"".$template->get('aro_productlines_row')."\";");
    }

    if(isset($core->input['id'])) {
        $aroorderrequest = AroOrderRequest::get_data(array('aorid' => $core->input['id']), array('simple' => false));
        if(is_object($aroorderrequest)) {
            $affiliate_list = parse_selectlist('affid', 1, $affiliate, $aroorderrequest->affid, '', '', array('blankstart' => true, 'id' => "affid"));
            $purchasetypelist = parse_selectlist('orderType', 4, $purchasetypes, $aroorderrequest->orderType, '', '', array('blankstart' => true, 'id' => "purchasetype"));
            $currencies_list = parse_selectlist('currency', 4, $currencies, $aroorderrequest->currency, '', '', array('blankstart' => 1, 'id' => "currencies"));
            $inspectionlist = parse_selectlist('inspectionType', 4, $inspections, $aroorderrequest->inspectionType);

            //*********Aro Order Customers -Start *********//
            $requestcustomers = AroOrderCustomers::get_data(array('aorid' => $aroorderrequest->aorid), array('returnarray' => true));
            $rowid = 1;
            if(is_array($requestcustomers)) {
                foreach($requestcustomers as $customer) {
                    $customeroder = $customer->get();
                    $payment_term = parse_selectlist('customeroder[corder]['.$rowid.'][ptid]', 4, $payment_terms, $customeroder['ptid'], '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
                    $altpayment_term = parse_selectlist('customeroder[altcorder][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
                    eval("\$aro_managedocuments_ordercustomers_rows .= \"".$template->get('aro_managedocuments_ordercustomers_rows')."\";");
                    $rowid++;
                }
            }
            else {
                $payment_term = parse_selectlist('customeroder[corder]['.$rowid.'][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
                $altpayment_term = parse_selectlist('customeroder[altcorder][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
                eval("\$aro_managedocuments_ordercustomers_rows .= \"".$template->get('aro_managedocuments_ordercustomers_rows')."\";");
            }
            //*********Aro Orde Customers - End *********//
            //********** ARO Product Lines -Start **************//
            $plrowid = 1;
            $productlines = AroRequestLines::get_data(array('aorid' => $aroorderrequest->aorid), array('returnarray' => true));
            if(is_array($productlines)) {
                foreach($productlines as $line) {
                    $productline = $line->get();
                    $segments_selectlist = parse_selectlist('productline['.$plrowid.'][psid]', '', $segments, $productline['psid'], null, null, array('id' => "productline_".$plrowid."_psid", 'placeholder' => 'Overwrite Segment', 'width' => '100%'));
                    $packaging_list = parse_selectlist('productline['.$plrowid.'][packing]', '', $packaging, $productline['packing'], '', '', array('id' => "productline_".$plrowid."packing", 'blankstart' => 1));
                    $uom_list = parse_selectlist('productline['.$plrowid.'][uom]', '', $uom, $productline['uom'], '', '', array('id' => "productline_".$plrowid."_uom", 'blankstart' => 1, 'width' => '70px'));
                    $product = new Products($productline['pid']);
                    $productline[productName] = $product->get_displayname();
//                    $purchasetype = new PurchaseTypes(array('ptid' => $aroorderrequest->orderType));
//                    if($purchasetype->qtyIsNotStored == 1) {
//                        $disabled_fields['daysInStock'] = $disabled_fields['qtyPotentiallySold'] = 'disabled="disabled"';
//                    }
//                    if($productline['daysInStock'] == 0) {
//                        $disabled_fields['qtyPotentiallySold'] = 'disabled="disabled"';
//                    }
                    eval("\$aroproductlines_rows .= \"".$template->get('aro_productlines_row')."\";");
                    $plrowid++;
                }
            }
            else {
                $productline['inputChecksum'] = generate_checksum('pl');
                $segments_selectlist = parse_selectlist('productline['.$plrowid.'][psid]', '', $segments, '', null, null, array('id' => "productline_".$plrowid."_psid", 'placeholder' => 'Overwrite Segment', 'width' => '100%'));
                $packaging_list = parse_selectlist('productline['.$plrowid.'][packing]', '', $packaging, '', '', '', array('id' => "productline_".$plrowid."packing", 'blankstart' => 1));
                $uom_list = parse_selectlist('productline['.$plrowid.'][uom]', '', $uom, '', '', '', array('id' => "productline_".$plrowid."_uom", 'blankstart' => 1, 'width' => '70px'));
                eval("\$aroproductlines_rows .= \"".$template->get('aro_productlines_row')."\";");
            }
            //********** ARO Product Lines **************//
        }
        else {
            redirect($_SERVER['HTTP_REFERER'], 2, $lang->nomatchfound);
        }
    }

    eval("\$aro_productlines = \"".$template->get('aro_fillproductlines')."\";");
    eval("\$aro_managedocuments_orderident= \"".$template->get('aro_managedocuments_orderidentification')."\";");
    eval("\$aro_ordercustomers= \"".$template->get('aro_managedocuments_ordercustomers')."\";");
    eval("\$aro_managedocuments= \"".$template->get('aro_managedocuments')."\";");
    output_page($aro_managedocuments);
}
else {
    if($core->input['action'] == 'getexchangerate') {
        $currencyobj = new Currencies('USD');
        $tocurrency = new Currencies($core->input['currency']);
        $rateusd = $currencyobj->get_latest_fxrate($tocurrency->alphaCode, null);
        $exchangerate = array('exchangeRateToUSD' => 1 / $rateusd);

        echo json_encode($exchangerate);
    }
    if($core->input ['action'] == 'populatedocnum') {
        $filter['filter']['time'] = '('.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo)';

        $documentseq_obj = AroDocumentsSequenceConf::get_data(array('time' => $filter['filter']['time'], 'affid' => $core->input['affid'], 'ptid' => $core->input['ptid']), array('simple' => false, 'operators' => array('affid' => 'in', 'ptid' => 'in', 'time' => 'CUSTOMSQLSECURE')));
        if(is_object($documentseq_obj)) {
            /* create the array to be encoded each dimension of the array represent the html element in the form */
            $orderreference = array('cpurchasetype' => $core->input['ptid'], 'orderreference' => $documentseq_obj->prefix.'-'.$documentseq_obj->nextNumber.'-'.date('y', $documentseq_obj->effectiveFrom).'-'.$documentseq_obj->suffix);
            echo json_encode($orderreference); //return json to the ajax request to populate in the form
        }
    }
    if($core->input['action'] == 'ajaxaddmore_newcustomer') {
        $rowid = intval($core->input['value']) + 1;
        $checksum = generate_checksum('odercustomer');
        $payment_terms = PaymentTerms::get_data('', array('returnarray' => ture));
        $payment_term = parse_selectlist('customeroder[corder]['.$rowid.'][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
        eval("\$aro_managedocuments_ordercustomers_rows = \"".$template->get('aro_managedocuments_ordercustomers_rows')."\";");
        output($aro_managedocuments_ordercustomers_rows);
    }
    if($core->input['action'] == 'do_perform_managearodouments') {
        //    if(isset($core->input['orderid']) && !empty($core->input['affid'])) {
        $orderident_obj = new AroOrderRequest ();
        /* get arodocument of the affid and pruchase type */
        $documentseq_obj = AroDocumentsSequenceConf::get_data(array('affid' => $core->input['affid'], 'ptid' => $core->input['orderType']), array('simple' => false, 'operators' => array('affid' => 'in', 'ptid' => 'in')));
        $nextsequence_number = $documentseq_obj->get_nextaro_identification();
        $core->input['nextnumid']['nextnum'] = $nextsequence_number;
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
                output_xml('<status>false</status><message>Error</message>');
                break;
        }
        //  }
//        if(isset($core->input[customeroder]['altcorder'][altcid])) {
//            $ordercust_obj = new AroOrderCustomers();
//            $ordercust_obj->set($core->input[customeroder]['altcorder']);
//            $ordercust_obj->save();
//        }
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
        $packaging_list = parse_selectlist('productline['.$plrowid.'][packing]', '', $packaging, '', '', '', array('id' => "productline_".$plrowid."packing", 'blankstart' => 1));
        $uom = Uom::get_data('name IS NOT NULL');
        $uom_list = parse_selectlist('productline['.$plrowid.'][uom]', '', $uom, '', '', '', array('id' => "productline_".$plrowid."_uom", 'blankstart' => 1, 'width' => '70px'));
        eval("\$aroproductlines_rows = \"".$template->get('aro_productlines_row')."\";");
        output($aroproductlines_rows);
    }

    if($core->input ['action'] == 'populateproductlinefields') {
        $productline_obj = new AroRequestLines();
        $rowid = $core->input['rowid'];
        unset($core->input['action'], $core->input['module'], $core->input['rowid']);
        $data = $core->input;
        $productline_data = $productline_obj->calculate_values($data);
        foreach($productline_data as $key => $value) {
            if(!empty($value)) {
                $productline['productline_'.$rowid.'_'.$key] = $value;
            }
            if($key == 'qtyPotentiallySold_disabled' || $key == 'daysInStock_disabled') {
                $productline['productline_'.$rowid.'_'.$key] = $value;
            }
        }
        //$purchasetype = new PurchaseTypes(array('ptid' => $core->input['ptid']));
        //if($purchasetype->qtyIsNotStored == 1) {
        //$disabled_fields['daysInStock'] = $disabled_fields['qtyPotentiallySold'] = 'disabled = "disabled"';
        // }
        //if($productline['daysInStock'] == 0) {
        // $disabled_fields['qtyPotentiallySold'] = 'disabled = "disabled"';
        //}
        echo json_encode($productline);
    }
}