<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: approvalchainspolicies_list.php
 * Created:        @tony.assaad    Feb 4, 2015 | 4:47:15 PM
 * Last Update:    @tony.assaad    Feb 4, 2015 | 4:47:15 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['aro_canManageApprovalPolicies'] == 0) {
    // error($lang->sectionnopermission);
}

if(!$core->input['action']) {

    $dal_config = array(
            'simple' => false,
            'returnarray' => true
    );
    $aroappr_pol = AroManageApprovalChainPolicies::get_data('effectiveFrom IS NOT NULL and effectiveTo IS NOT NULL', $dal_config);
    if(is_array($aroappr_pol)) {
        foreach($aroappr_pol as $approvers) {

            $approvers->effectiveTo = date($core->settings['dateformat'], $approvers->effectiveTo);
            $approvers->effectiveFrom = date($core->settings['dateformat'], $approvers->effectiveFrom);
            $affobj = new Affiliates($approvers->affid);

            $approver_data = unserialize($approvers->approvalChain);
            if(is_array($approver_data)) {
                foreach($approver_data as $approverschain) {
                    // $listapprovers[] = implode(',', $approverschain);
                    // print_R($approverschain);
                }
            }

            eval("\$policies_approverpolicieslistrow .= \"".$template->get('aro_warehouses_approverpolicies_list_rows')."\";");
            unset($listapprovers);
        }
    }

    eval("\$aro_warehousesapppolicieslist = \"".$template->get('aro_warehouses_approverpolicies_list')."\";");
    output_page($aro_warehousesapppolicieslist);
}