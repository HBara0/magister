<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: managepolicies.php
 * Created:        @rasha.aboushakra    Feb 4, 2015 | 10:31:32 AM
 * Last Update:    @rasha.aboushakra    Feb 4, 2015 | 10:31:32 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['aro_canManagePolicies'] == 0) {
    error($lang->sectionnopermission);
    exit;
}
if(!$core->input['action']) {

    $affiliate_where = ' name LIKE "%orkila%" AND isActive=1';
    if($core->usergroup ['canViewAllAff'] == 0) {
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliate_where .= ' AND affid IN ('.$inaffiliates.')';
    }
    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 0, $affiliate_where);
    $purchasetypes = PurchaseTypes::get_data('', array('returnarray' => true));

    if(isset($core->input['id']) && !empty($core->input['id'])) {
        $aropolicy = AroPolicies::get_data(array('apid' => $core->input['id']));
        if(is_object($aropolicy)) {
            $aropolicy = $aropolicy->get();
            $aropolicy[effectiveFrom_output] = date($core->settings['dateformat'], $aropolicy['effectiveFrom']);
            $aropolicy[effectiveTo_output] = date($core->settings['dateformat'], $aropolicy['effectiveTo']);

            $aropolicy['effectiveFrom_formatted'] = date('d-m-Y', $aropolicy['effectiveFrom']);
            $aropolicy['effectiveTo_formatted'] = date('d-m-Y', $aropolicy['effectiveTo']);
            if($aropolicy['isActive'] == 1) {
                $checked['isActive'] = 'checked="checked"';
            }

            $affiliates_list = parse_selectlist('aropolicy[affid]', '', $affiliates, $aropolicy['affid'], 0, '', array('id' => 'aropolicy_affid'));
            $purchasetypes_list = parse_selectlist('aropolicy[purchaseType]', '', $purchasetypes, $aropolicy['purchaseType'], 0, '', array('id' => 'aropolicy_purchaseType'));
        }
        else {
            redirect($_SERVER['HTTP_REFERER'], 2, $lang->nomatchfound);
        }
    }
    else {
        $affiliates_list = parse_selectlist('aropolicy[affid]', '', $affiliates, '', 0, '', array('id' => 'aropolicy_affid'));
        $purchasetypes_list = parse_selectlist('aropolicy[purchaseType]', '', $purchasetypes, '', 0, '', array('id' => 'aropolicy_purchaseType'));
    }


    eval("\$aro_maangepolicies = \"".$template->get('aro_managepolicies')."\";");
    output_page($aro_maangepolicies);
    unset($checked);
}
else if($core->input['action'] == 'do_perform_managepolicies') {
    unset($core->input['identifier'], $core->input['module'], $core->input['action']);
    $aropolicy = new AroPolicies;
    $core->input['aropolicy']['effectiveFrom'] = strtotime($core->input['aropolicy']['effectiveFrom']);
    $core->input['aropolicy']['effectiveTo'] = strtotime($core->input['aropolicy']['effectiveTo']);
    if($core->input['aropolicy']['effectiveFrom'] > $core->input['aropolicy']['effectiveTo']) {
        output_xml('<status>false</status><message>'.$lang->errordate.'</message>');
        exit;
    }
    $aropolicy->set($core->input['aropolicy']);
    $aropolicy->save();
    switch($aropolicy->get_errorcode()) {
        case 0:
        case 1:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 2:
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
            break;
    }
}