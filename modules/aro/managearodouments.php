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

    $affiliate_list = parse_selectlist('documentsequence[affid]', 1, $affiliate, $documentsequence[affid], '', '', array('blankstart' => true, 'id' => "affid"));
    $purchasetypes = PurchaseTypes::get_data('name IS NOT NULL', array('returnarray' => true));

    $purchasetypelist = parse_selectlist('documentsequence[purchaseType]', 4, $purchasetypes, $documentsequence['ptid'], '', '', array('blankstart' => true, 'id' => "purchasetype"));

    $mainaffobj = new Affiliates($core->user['mainaffiliate']);


    $currencies = Currencies::get_data();

    $currencies_list = parse_selectlist('documentsequence[currency]', 4, $currencies, '', '', '', array('blankstart' => 1, 'id' => "currencies"));
    $inspections = array('inspection1' => 'inspection');
    $inspectionlist = parse_selectlist('documentsequence[inspectionType]', 4, $inspections, '');
    eval("\$aro_managedocuments_orderident= \"".$template->get('aro_managedocuments_orderidentification')."\";");
    $newcustomer_rowid = 1;
    eval("\$aro_ordercustomers= \"".$template->get('aro_managedocuments_ordercustomers')."\";");

    eval("\$aro_managedocuments= \"".$template->get('aro_managedocuments')."\";");
    output_page($aro_managedocuments);
}
else {


    if($core->input ['action'] == 'getexchangerate') {
        $currencyobj = new Currencies($core->input['currency']);
        $rateusd = $currencyobj->get_latest_fxrate($currencyobj->get()['numCode'], '', 840);
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
}