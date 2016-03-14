<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: affiliatemanagement.php
 * Created:        @rasha.aboushakra    Mar 9, 2016 | 11:49:14 AM
 * Last Update:    @rasha.aboushakra    Mar 9, 2016 | 11:49:14 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!isset($core->input['action'])) {
    if(!isset($core->input['affid']) || empty($core->input['affid'])) {
        redirect('index.php?module=regions/affiliates');
    }
    $affid = $core->input['affid'];
    $affiliate_obj = Affiliates::get_affiliates(array('affid' => $affid), array('simple' => false));
    if(is_object($affiliate_obj)) {
        $management_positions = array('generalManager', 'supervisor', 'hrManager', 'finManager', 'coo', 'regionalSupervisor', 'globalPurchaseManager', 'cfo', 'logisticsManager', 'commercialManager', 'globalFinManager', 'commercialemail');
        foreach($management_positions as $position) {
            switch($position) {
                case 'commercialemail':
                    break;

                default:
                    if(!empty($affiliate_obj->$position)) {
                        $user = Users::get_data(array('uid' => $affiliate_obj->$position));
                        if(is_object($user)) {
                            $affiliate[$position] = $user->uid;
                            $affiliate[$position.'_output'] = $user->get_displayname();
                        }
                    }
                    break;
            }
        }
    }
    eval("\$affiliatemanagement = \"".$template->get('admin_regions_affiliatemanagement')."\";");
    output_page($affiliatemanagement);
}
else {
    if($core->input['action'] == 'do_perform_affiliatemanagement') {
        unset($core->input['identifier'], $core->input['module'], $core->input['action']);
        $affid = $db->escape_string($core->input['affid']);
        $affiliate_obj = Affiliates::get_affiliates(array('affid' => $affid), array('simple' => false));
        if(is_object($affiliate_obj)) {
            $updateaffmanagement = $affiliate_obj->manage_affiliatemanagement($core->input);
            if($updateaffmanagement) {
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            }
            else {
                output_xml('<status>false</status><message>'.$lang->errorupdatingaffiliate.'</message>');
            }
        }
    }
}
?>