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
    $intermed_affiliates = Affiliates::get_affiliates(array('isActive' => 1), array('returnarray' => true, 'order' => array('sort' => 'ASC', 'by' => 'name')));
    $purchasetypes = PurchaseTypes::get_data('', array('returnarray' => true));

    $dal_config = array('returnarray' => true);
    $payment_terms = PaymentTerms::get_data('', $dal_config);
    $currencies = Currencies::get_data('', $dal_config);
    $incoterms = Incoterms::get_data('name IS NOT NULL', $dal_config);


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
            $affiliates_list = parse_selectlist('aropolicy[affid]', '', $affiliates, $aropolicy['affid'], 0, '', array('id' => 'aropolicy_affid', 'width' => '150px'));
            $intermediary_list = parse_selectlist('aropolicy[defaultIntermed]', '', $intermed_affiliates, $aropolicy['defaultIntermed'], 0, '', array('id' => 'aropolicy_defaultIntermed', 'width' => '150px', 'blankstart' => true));
            $purchasetypes_list = parse_selectlist('aropolicy[purchaseType]', '', $purchasetypes, $aropolicy['purchaseType'], 0, '', array('id' => 'aropolicy_purchaseType', 'width' => '150px'));
            $paymentterms_list = parse_selectlist('aropolicy[defaultPaymentTerm]', '', $payment_terms, $aropolicy['defaultPaymentTerm'], 0, '', array('id' => 'aropolicy_defaultPaymentTerm', 'width' => '150px', 'blankstart' => true));
            $currencies_list = parse_selectlist('aropolicy[defaultCurrency]', '', $currencies, $aropolicy['defaultCurrency'], 0, '', array('id' => 'aropolicy_defaultCurrency', 'width' => '150px', 'blankstart' => true));
            $incoterms_list = parse_selectlist('aropolicy[defaultIncoterms]', '', $incoterms, $aropolicy['defaultIncoterms'], 0, '', array('id' => 'aropolicy_defaultIncoterms', 'width' => '150px', 'blankstart' => true));
            $audittrailfields = array('createdOn', 'createdBy', 'modifiedOn', 'modifiedBy');
            foreach($audittrailfields as $field) {
                if(!empty($aropolicy[$field])) {
                    switch($field) {
                        case 'createdOn':
                        case 'modifiedOn':
                            $aropolicy[$field.'_output'] = date($core->settings['dateformat'], $aropolicy[$field]);
                            break;
                        default:
                            $user = new Users($aropolicy[$field]);
                            if(is_object($user)) {
                                $aropolicy[$field.'_output'] = $user->get_displayname();
                            }
                            break;
                    }
                    $field_strtolower = strtolower($field);
                    $audittrail .= '<tr><td>'.$lang->$field_strtolower.'</td><td>'.$aropolicy[$field.'_output'].'</td></tr>';
                }
            }
        }
        else {
            redirect($_SERVER['HTTP_REFERER'], 2, $lang->nomatchfound);
        }
    }
    else {
        $affiliates_list = parse_selectlist('aropolicy[affid]', '', $affiliates, '', 0, '', array('id' => 'aropolicy_affid', 'width' => '150px'));
        $purchasetypes_list = parse_selectlist('aropolicy[purchaseType]', '', $purchasetypes, '', 0, '', array('id' => 'aropolicy_purchaseType', 'width' => '150px'));
        $intermediary_list = parse_selectlist('aropolicy[defaultIntermed]', '', $intermed_affiliates, '', 0, '', array('id' => 'aropolicy_defaultIntermed', 'width' => '150px', 'blankstart' => true));
        $paymentterms_list = parse_selectlist('aropolicy[defaultPaymentTerm]', '', $payment_terms, '', 0, '', array('id' => 'aropolicy_defaultPaymentTerm', 'width' => '150px', 'blankstart' => true));
        $currencies_list = parse_selectlist('aropolicy[defaultCurrency]', '', $currencies, '', 0, '', array('id' => 'aropolicy_defaultCurrency', 'width' => '150px', 'blankstart' => true));
        $incoterms_list = parse_selectlist('aropolicy[defaultIncoterms]', '', $incoterms, '', 0, '', array('id' => 'aropolicy_defaultIncoterms', 'width' => '150px', 'blankstart' => true));
    }
    eval("\$aro_maangepolicies = \"".$template->get('aro_managepolicies')."\";");
    output_page($aro_maangepolicies);
    unset($checked);
}
else if($core->input['action'] == 'do_perform_managepolicies') {
    unset($core->input['identifier'], $core->input['module'], $core->input['action']);
    $aropolicy = new AroPolicies;
    if(!is_empty($core->input['aropolicy']['effectiveFrom'])) {
        $core->input['aropolicy']['effectiveFrom'] = strtotime($core->input['aropolicy']['effectiveFrom'].' 00:00:00');
    }
    if(!is_empty($core->input['aropolicy']['effectiveTo'])) {
        $core->input['aropolicy']['effectiveTo'] = strtotime($core->input['aropolicy']['effectiveTo'].' 23:59:59');
    }
    if($core->input['aropolicy']['effectiveFrom'] > $core->input['aropolicy']['effectiveTo']) {
        output_xml('<status>false</status><message>'.$lang->errordate.'</message>');
        exit;
    }
    $aropolicy->set($core->input['aropolicy']);
    $aropolicy = $aropolicy->save();
    switch($aropolicy->get_errorcode()) {
        case 0:
        case 1:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 2:
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
            break;
        case 3:
            output_xml('<status>false</status><message>'.$lang->policiescoexist.'</message>');
            break;
    }
}