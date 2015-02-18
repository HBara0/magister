<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: managearodouments.php
 * Created:        @tony.assaad    Feb 11, 2015 | 11:53:19 AM
 * Last Update:    @tony.assaad    Feb 11, 2015 | 11:53:19 AM
 */

if(!($core->input['action'])) {


    /* Order idendtifications */
    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = $core->user['affiliates'];
    }
    foreach($inaffiliates as $affid) {
        $affiliate[$affid] = new Affiliates($affid);
    }

    $affiliate_list = parse_selectlist('orderid[affid]', 1, $affiliate, $orderid[affid], '', '', array('blankstart' => true, 'id' => "affid"));
    $purchasetypes = PurchaseTypes::get_data('name IS NOT NULL', array('returnarray' => true));

    $purchasetypelist = parse_selectlist('orderid[orderType]', 4, $purchasetypes, $orderid['ptid'], '', '', array('blankstart' => true, 'id' => "purchasetype"));

    // $mainaffobj = new Affiliates($core->user['mainaffiliate']);
    //$currencies = Currencies::get_data();
    $checksum = generate_checksum('odercustomer');
    $rowid = 1;
    $currencies_list = parse_selectlist('orderid[currency]', 4, $currencies, '', '', '', array('blankstart' => 1, 'id' => "currencies"));
    $inspections = array('inspection1' => 'inspection');
    $inspectionlist = parse_selectlist('orderid[inspectionType]', 4, $inspections, '');
    $payment_terms = PaymentTerms::get_data('', array('returnarray' => ture));

    $payment_term = parse_selectlist('customeroder[corder]['.$rowid.']['.$checksum.'][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
    $altpayment_term = parse_selectlist('customeroder[altcorder][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));

    eval("\$aro_managedocuments_orderident= \"".$template->get('aro_managedocuments_orderidentification')."\";");

    eval("\$aro_managedocuments_ordercustomers_rows = \"".$template->get('aro_managedocuments_ordercustomers_rows')."\";");

    eval("\$aro_ordercustomers= \"".$template->get('aro_managedocuments_ordercustomers')."\";");


    eval("\$aro_managedocuments= \"".$template->get('aro_managedocuments')."\";");
    output_page($aro_managedocuments);
}
else {
    if($core->input['action'] == 'getexchangerate') {
        $currencyobj = new Currencies($core->input['currency']);
        $rateusd = $currencyobj->get_latest_fxrate($currencyobj->get()['alphaCode'], null, 'USD');
        $exchangerate = array('exchangeRateToUSD' => $rateusd);

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
        else {
            echo json_encode('error');
            exit;
        }
    }
    if($core->input['action'] == 'ajaxaddmore_newcustomer') {
        $rowid = intval($core->input['value']) + 1;
        $checksum = generate_checksum('odercustomer');
        $payment_terms = PaymentTerms::get_data('', array('returnarray' => ture));
        $payment_term = parse_selectlist('customeroder[corder]['.$rowid.']['.$checksum.'][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
        eval("\$aro_managedocuments_ordercustomers_rows = \"".$template->get('aro_managedocuments_ordercustomers_rows')."\";");
        output($aro_managedocuments_ordercustomers_rows);
    }
    if($core->input['action'] == 'do_perform_managearodouments') {
        if(isset($core->input['orderid']) && !empty($core->input['orderid']['affid'])) {
            $orderident_obj = new AroOrderRequest ();
            /* get arodocument of the affid and pruchase type */
            $documentseq_obj = AroDocumentsSequenceConf::get_data(array('affid' => $core->input['orderid']['affid'], 'ptid' => $core->input['orderid']['orderType']), array('simple' => false, 'operators' => array('affid' => 'in', 'ptid' => 'in')));
            $nextsequence_number = $documentseq_obj->get_nextaro_identification();
            $core->input['orderid']['nextnumid']['nextnum'] = $nextsequence_number;


            $orderident_obj->set($core->input['orderid']);
            $orderident_obj->save();
        }

        foreach($core->input['customeroder']['corder'] as $cusomeroder) {
            foreach($cusomeroder as $order) {
                if(isset($order['cid']) && !empty($order['cid'])) {
                    $ordercust_obj = new AroOrderCustomers();
                    $ordercust_obj->set($order);
                    $ordercust_obj->save();
                }
            }
        }
        if(isset($core->input[customeroder]['altcorder'][altcid])) {
            $ordercust_obj = new AroOrderCustomers();
            $ordercust_obj->set($core->input[customeroder]['altcorder']);
            $ordercust_obj->save();
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
}