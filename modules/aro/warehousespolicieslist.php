<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: warehousespolicieslist.php
 * Created:        @tony.assaad    Feb 3, 2015 | 2:08:22 PM
 * Last Update:    @tony.assaad    Feb 3, 2015 | 2:08:22 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['aro_canManageWarehousePolicies'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {

    $dal_config = array(
            'simple' => false,
            'returnarray' => true
    );
    $aroobjs = AroManageWarehousesPolicies::get_data('effectiveFrom IS NOT NULL and effectiveTo IS NOT NULL', $dal_config);

    if(is_array($aroobjs)) {
        foreach($aroobjs as $aro) {

            $aro->effectiveTo = date($core->settings['dateformat'], $aro->effectiveTo);
            $aro->effectiveFrom = date($core->settings['dateformat'], $aro->effectiveFrom);
            $warehouse = new Warehouses($aro->warehouse);
            $aro->warehouse = $warehouse->get_displayname();
            $row_tools = '<a href=index.php?module=aro/managewarehousepolicies&id='.$aro->awpid.' title="'.$lang->edit.'"><img src=./images/icons/edit.gif border=0 alt='.$lang->edit.'/></a>';
            $row_tools .= ' <a href="#'.$aro->awpid.'" id="deletepolicy_'.$aro->awpid.'_aro/warehousespolicieslist_loadpopupbyid" rel="delete_'.$aro->awpid.'" title="'.$lang->delete.'"><img src="'.$core->settings['rootdir'].'/images/icons/delete.png" alt="'.$lang->delete.'" border="0"></a>';
            eval("\$policies_listrow .= \"".$template->get('aro_warehouses_policies_list_rows')."\";");
        }
    }

    eval("\$aro_warehousespolicieslist = \"".$template->get('aro_warehouses_policies_list')."\";");
    output_page($aro_warehousespolicieslist);
}
elseif($core->input['action'] == 'get_deletepolicy') {
    eval("\$deletebox = \"".$template->get('popup_aro_deletewarehousepolicy')."\";");
    output($deletebox);
}
elseif($core->input['action'] == 'perform_deletepolicy') {
    $areotodel = new AroManageWarehousesPolicies($core->input[todelelete]);
    if(is_object($areotodel)) {
        $areotodel->delete();
    }
}