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
    foreach($inaffiliates as $affid) {
        $affiliate[$affid] = new Affiliates($affid);
    }

    $affiliate_list = parse_selectlist('chainpolicy[affid]', 1, $affiliate, '');

    //$purchasetypes = purchaseTypes::get_data();
    $purchasetype = parse_selectlist('chainpolicy[purchaseType]', 1, $purchasetypes, '');



    /* approvers predfined */

    $approvers = array('BM' => 'Local Business Manager', 'lolm' => 'Local Logistics Manager', 'lofm' => 'Local Finance Manager', 'gm' => 'General Manager', 'fm' => 'Global Finance Manage', 'cfo' => 'Global CFO', 'user' => 'user');
    $rowid = 1;
    //$onchange_actions = 'if($(this).find(":selected").val()=="user"){ $("#"+$(this).find(":selected").val()+"_search_'.$rowid.'").effect("highlight",{ color: "#D6EAAC"}, 1500).find("input").first().focus().val(""); } else{$("#user_search_'.$rowid.'").hide();}';

    $list = '<table class="datacell_freewidth" width="100%;">';
    $list .= '<tr>';
    foreach($approvers as $key => $approver) {
        $list .= ' <td> <input type="radio" onchange =\''.$onchange_actions.'\' name="chainpolicy[approverchain]['.$rowid.'][approver]" value="'.$key.'" id="'.$key.'_'.$rowid.'_approver"'.$checked.'/> '.$val.'</td><td>'.$approver.'</td>';
    }
    $list .= '</tr>';
    $list .='</table>';
    eval("\$aro_manageapprovalchainspolicies_approversrows= \"".$template->get('aro_manageapprovalchainspolicies_approversrows')."\";");
    // $rowid = intval($core->input['value']) + 1;

    eval("\$aro_manageapprovalchainspolicies= \"".$template->get('aro_manageapprovalchainspolicies')."\";");
    output_page($aro_manageapprovalchainspolicies);
}
else if($core->input['action'] == 'do_perform_manageapprovalchainspolicies') {

    unset($core->input['identifier'], $core->input['module'], $core->input['action']);
    $aroapproval_policy = new AroManageApprovalChainPolicies();
    $core->input['chainpolicy']['effectiveFrom'] = strtotime($core->input['chainpolicy']['effectiveFrom']);
    $core->input['chainpolicy']['effectiveTo'] = strtotime($core->input['chainpolicy']['effectiveTo']);


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