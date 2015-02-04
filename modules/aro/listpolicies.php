<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: listpolicies.php
 * Created:        @rasha.aboushakra    Feb 4, 2015 | 12:44:23 PM
 * Last Update:    @rasha.aboushakra    Feb 4, 2015 | 12:44:23 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['aro_canManagePolicies'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $sort_url = sort_url();
    /* need to add inline filter */
    $affiliate_where = ' name LIKE "%orkila%" AND isActive=1';
    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliate_where .= ' AND affid IN ('.$inaffiliates.')';
    }
    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 0, $affiliate_where);

    $aropolicies = AroPolicies::get_data(array('affid' => array_keys($affiliates)), array('returnarray' => true, 'operators' => array('affid' => 'IN')));
    if(isset($core->input['sortby']) && isset($core->input['order'])) {
        $aropolicies = AroPolicies::get_data(array('affid' => array_keys($affiliates)), array('returnarray' => true, 'operators' => array('affid' => 'IN'), 'order' => array('by' => $core->input['sortby'], 'sort' => $core->input['order'])));
    }
    if(is_array($aropolicies)) {
        foreach($aropolicies as $policy) {
            $row_tools = '<a href=index.php?module=aro/managepolicies&id='.$policy->apid.' title="'.$lang->edit.'"><img src=./images/icons/edit.gif border=0 alt='.$lang->edit.'/></a>';
            $row_tools .= "<a href='#{$policy->apid}' id='deletearopolicy_{$policy->apid}_aro/listpolicies_icon'><img src='{$core->settings[rootdir]}/images/invalid.gif' border='0' alt='{$lang->deletearopolicy}' /></a>";
            $policy->effectiveTo = date($core->settings['dateformat'], $policy->effectiveTo);
            $policy->effectiveFrom = date($core->settings['dateformat'], $policy->effectiveFrom);
            $affiliate = new Affiliates($policy->affid);
            $purchasetype = new PurchaseTypes($policy->purchaseType);
            $policy->affid = $affiliate->get_displayname();
            $policy->purchaseType = $purchasetype->get_displayname();
            $policy->isactveicon = '<img src="./images/false.gif" />';
            if($policy->isActive == 1) {
                $policy->isactveicon = '<img src="./images/true.gif" />';
            }
            $rowclass = alt_row($rowclass);
            eval("\$aropolicies_rows .= \"".$template->get('aro_policieslist_row')."\";");
            $row_tools = '';
        }
    }
    eval("\$aro_policieslist = \"".$template->get('aro_policieslist')."\";");
    output_page($aro_policieslist);
}
else {
    if($core->input['action'] == 'perform_deletearopolicy') {
        $aropolicy = new AroPolicies($db->escape_string($core->input['todelete']));
        $aropolicy->delete();
        if($aropolicy->delete()) {
            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
            exit;
        }
    }
    elseif($core->input['action'] == 'get_deletearopolicy') {
        eval("\$deletearopolicybox = \"".$template->get('popup_deletearopolicy')."\";");
        output($deletearopolicybox);
    }
}