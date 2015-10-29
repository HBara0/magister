<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: arodocumentsequeneconf.php
 * Created:        @tony.assaad    Feb 10, 2015 | 3:58:14 PM
 * Last Update:    @tony.assaad    Feb 10, 2015 | 3:58:14 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['aro_canManagePolicies'] == 0) {
    error($lang->sectionnopermission);
}

if(!($core->input['action'])) {
    if(isset($core->input['id']) && !empty($core->input['id'])) {
        $document_sequenceobj = new AroDocumentsSequenceConf($core->input['id'], false);
        $documentsequence = $document_sequenceobj->get();
        /* parse approvers */

        $documentsequence['effectiveTo_output'] = date($core->settings['dateformat'], $documentsequence['effectiveTo']);
        $documentsequence['effectiveFrom_output'] = date($core->settings['dateformat'], $documentsequence['effectiveFrom']);

        $documentsequence['effectiveFrom_formatted'] = date('d-m-Y', $documentsequence['effectiveFrom']);
        $documentsequence['effectiveTo_formatted'] = date('d-m-Y', $documentsequence['effectiveTo']);
    }
    else {
        $documentsequence['incrementBy'] = 1;
        $documentsequence['nextNumber'] = 1;
    }


    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = $core->user['affiliates'];
        foreach($inaffiliates as $affid) {
            $affiliate[$affid] = new Affiliates($affid);
        }
    }
    else {
        $affiliate = Affiliates::get_affiliates();
    }
    $affiliate_list = parse_selectlist('documentsequence[affid]', 1, $affiliate, $documentsequence[affid]);
    $purchasetypes = PurchaseTypes::get_data('name IS NOT NULL', array('returnarray' => true));

    $purchasetypelist = parse_selectlist('documentsequence[ptid]', 4, $purchasetypes, $documentsequence[ptid]);

    eval("\$aro_managedocumentsequence = \"".$template->get('aro_managedocumentsequence')."\";");
    output_page($aro_managedocumentsequence);
}
elseif($core->input['action'] == 'do_perform_arodocumentsequeneconf') {
    $core->input['documentsequence']['effectiveFrom'] = strtotime("midnight", strtotime($core->input['documentsequence']['effectiveFrom']));
    $core->input['documentsequence']['effectiveTo'] = strtotime("tomorrow midnight - 1 second", strtotime($core->input['documentsequence']['effectiveTo']));
    if($core->input['documentsequence']['effectiveFrom'] > $core->input['documentsequence']['effectiveTo']) {
        output_xml('<status>false</status><message>'.$lang->errordate.'</message>');
        exit;
    }
    $arodoumentobj = new AroDocumentsSequenceConf();
    $arodoumentobj->set($core->input['documentsequence']);
    if(is_object($arodoumentobj->get_intersecting_sequenceconf())) {
        output_xml('<status>false</status><message>'.$lang->intersecterror.'</message>');
        exit;
    }
    $arodoumentobj->save();
    switch($arodoumentobj->get_errorcode()) {
        case 0:
        case 1:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 2:
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
            break;
    }
}