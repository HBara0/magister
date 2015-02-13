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

    $mainaffobj = new Affiliates($core->user['mainaffiliate']);

    $currencies = Currencies::get_data();
    $checksum = generate_checksum('odercustomer');
    $rowid = 1;
    $currencies_list = parse_selectlist('orderid[currency]', 4, $currencies, '', '', '', array('blankstart' => 1, 'id' => "currencies"));
    $inspections = array('inspection1' => 'inspection');
    $inspectionlist = parse_selectlist('orderid[inspectionType]', 4, $inspections, '');
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
    if($core->input ['action'] == 'populate_documentpattern') {
        $documentseq_obj = AroDocumentsSequenceConf::get_data(array('affid' => $core->input['affid'], 'ptid' => $core->input['ptid']), array('simple' => false, 'operators' => array('affid' => 'in', 'ptid' => 'in')));
        if(is_object($documentseq_obj)) {
            $orderreference = array('orderreference' => $documentseq_obj->prefix.'-'.$documentseq_obj->nextNumber.'-'.$documentseq_obj->suffix);
            echo json_encode($orderreference);
        }
        else {
            echo json_encode('error');
            exit;
        }
    }
    if($core->input['action'] == 'ajaxaddmore_newcustomer') {
        $rowid = intval($core->input['value']) + 1;
        $checksum = generate_checksum('odercustomer');
        eval("\$aro_managedocuments_ordercustomers_rows = \"".$template->get('aro_managedocuments_ordercustomers_rows')."\";");
        output($aro_managedocuments_ordercustomers_rows);
    }
    if($core->input['action'] == 'do_perform_managearodouments') {
        if(isset($core->input[orderid]) && !empty($core->input[orderid][affid])) {
            $orderident_obj = new AroOrderIdentification ();
            $orderident_obj->set($core->input['orderid']);
            $orderident_obj->save();
        }
        if(isset($core->input[orderid]) && !empty($core->input[orderid][affid])) {
            $ordercust_obj = new AroOrderCustomers();
            $ordercust_obj->set($core->input['customeroder']);
            $ordercust_obj->save();
        }
    }
}