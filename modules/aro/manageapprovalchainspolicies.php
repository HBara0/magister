<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: manageapprovalchainspolicies.php
 * Created:        @tony.assaad    Feb 4, 2015 | 11:05:25 AM
 * Last Update:    @tony.assaad    Feb 4, 2015 | 11:05:25 AM
 */
if($core->usergroup['aro_canManageApprovalPolicies'] == 0) {
    // error($lang->sectionnopermission);
}


if(!$core->input['action']) {
    // $core->usergroup['canViewAllAff'] = 1;
    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = $core->user['affiliates'];
    }
    if(isset($core->input['id']) && !empty($core->input['id'])) {
        $chainpolicyobj = new AroApprovalChainPolicies($core->input['id'], false);
        $chainpolicy = $chainpolicyobj->get();
        /* parse approvers */

        $chainpolicy[effectiveTo_output] = date($core->settings['dateformat'], $chainpolicy['effectiveTo']);
        $chainpolicy[effectiveFrom_output] = date($core->settings['dateformat'], $chainpolicy['effectiveFrom']);

        $chainpolicy['effectiveFrom_formatted'] = date('d-m-Y', $chainpolicy['effectiveFrom']);
        $chainpolicy['effectiveTo_formatted'] = date('d-m-Y', $chainpolicy['effectiveTo']);
    }
    foreach($inaffiliates as $affid) {
        $affiliate[$affid] = new Affiliates($affid);
    }

    $affiliate_list = parse_selectlist('chainpolicy[affid]', 1, $affiliate, $chainpolicy[affid]);
    $purchasetypes = PurchaseTypes::get_data('name IS NOT NULL', array('returnarray' => true));

    $purchasetypelist = parse_selectlist('chainpolicy[purchaseType]', 4, $purchasetypes, $chainpolicy[purchaseType]);

    if(is_array(unserialize($chainpolicy['approvalChain'])) && !empty($core->input['id'])) {
        $approvers = array('businessManager' => 'Local Business Manager', 'lolm' => 'Local Logistics Manager', 'lfinancialManager' => 'Local Finance Manager', 'generalManager' => 'General Manager', 'gfinancialManager' => 'Global Finance Manager', 'cfo' => 'Global CFO', 'user' => 'user');

        foreach(unserialize($chainpolicy[approvalChain]) as $key => $approverdata) {

            $rowid++;
            if(empty($approverdata['approver'])) {
                continue;
            }
            if(empty($approverdata[sequence])) {
                $approverdata[sequence] = $rowid;
            }
            if(in_array($approverdata['approver'], array_keys($approvers))) {
                $checkbox[$approverdata['approver']]['checked'] = ' checked="checked"';
            }
            $display[$key][uid] = 'display:none;';
            if(isset($approverdata['uid']) && !empty($approverdata['uid']) && $approverdata['approver'] == 'user') {
                $user = new Users($approverdata['uid']);
                $chainpolicy[username] = $user->get_displayname();

                if(is_object($user)) {
                    $display[$key][uid] = 'display:block;';
                }
            }

            foreach($approvers as $key => $approver) {
                $list .= ' <div style="display: inline-block; width:32%;"><input  type="radio"  '.$checkbox[$key]['checked'].'   onchange =\''.$onchange_actions.'\' name="chainpolicy[approverchain]['.$rowid.'][approver]" value="'.$key.'" id="'.$key.'_'.$rowid.'_approver"'.$checked.'/> '.$val.' '.$approver.'</div>';
            }
            eval("\$aro_manageapprovalchainspolicies_approversrows  .= \"".$template->get('aro_manageapprovalchainspolicies_approversrows')."\";");
            unset($list, $checkbox);
        }
    }

    /* approvers predfined */
    else {
        $approvers = array('businessManager' => 'Local Business Manager', 'lolm' => 'Local Logistics Manager', 'lfinancialManager' => 'Local Finance Manager', 'generalManager' => 'General Manager', 'gfinancialManager' => 'Global Finance Manager', 'cfo' => 'Global CFO', 'user' => 'user');
        $rowid = 1;
        $display[1][uid] = 'display:none;';
        foreach($approvers as $key => $approver) {
            $list .= ' <div style="display: inline-block; width:32%;"><input  type="radio"  onchange =\''.$onchange_actions.'\' name="chainpolicy[approverchain]['.$rowid.'][approver]" value="'.$key.'" id="'.$key.'_'.$rowid.'_approver"'.$checked.'/> '.$val.''.$approver.'</div>';
        }
        eval("\$aro_manageapprovalchainspolicies_approversrows= \"".$template->get('aro_manageapprovalchainspolicies_approversrows')."\";");
        // $rowid = intval($core->input['value']) + 1;
    }
    eval("\$aro_manageapprovalchainspolicies= \"".$template->get('aro_manageapprovalchainspolicies')."\";");
    output_page($aro_manageapprovalchainspolicies);
}
else if($core->input['action'] == 'do_perform_manageapprovalchainspolicies') {

    unset($core->input['identifier'], $core->input['module'], $core->input['action']);
    $aroapproval_policy = new AroApprovalChainPolicies();
    $core->input['chainpolicy']['effectiveFrom'] = strtotime($core->input['chainpolicy']['effectiveFrom']);
    $core->input['chainpolicy']['effectiveTo'] = strtotime($core->input['chainpolicy']['effectiveTo']);
    if($core->input['chainpolicy']['effectiveFrom'] > $core->input['chainpolicy']['effectiveTo']) {
        output_xml('<status>false</status><message>'.$lang->errordate.'</message>');
        exit;
    }
    $aroapproval_policy->set($core->input['chainpolicy']);
    $aroapproval_policy->save();
    switch($aroapproval_policy->get_errorcode()) {
        case 0:
        case 1:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 2:
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
            break;
    }
}
else if($core->input['action'] == 'ajaxaddmore_approvers') {
    $rowid = intval($core->input['value']) + 1;
    $approvers = array('businessManager' => 'Local Business Manager', 'lolm' => 'Local Logistics Manager', 'lfinancialManager' => 'Local Finance Manager', 'generalManager' => 'General Manager', 'gfinancialManager' => 'Global Finance Manager', 'cfo' => 'Global CFO', 'user' => 'user');
    //$rowid = 1;
    $display[$rowid][uid] = 'display:none;';
    foreach($approvers as $key => $approver) {
        $list .= ' <div style="display: inline-block; width:32%;"><input  type="radio"  onchange =\''.$onchange_actions.'\' name="chainpolicy[approverchain]['.$rowid.'][approver]" value="'.$key.'" id="'.$key.'_'.$rowid.'_approver"'.$checked.'/> '.$val.''.$approver.'</div>';
    }
    eval("\$aro_manageapprovalchainspolicies_approversrows= \"".$template->get('aro_manageapprovalchainspolicies_approversrows')."\";");
    output($aro_manageapprovalchainspolicies_approversrows);
}