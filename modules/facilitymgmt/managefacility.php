<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: createfacility.php
 * Created:        @rasha.aboushakra    Sep 23, 2015 | 10:03:10 AM
 * Last Update:    @rasha.aboushakra    Sep 23, 2015 | 10:03:10 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['facilitymgmt_canManageFacilities'] == 0) {
    error($lang->sectionnopermission);
}
if(!isset($core->input['action'])) {
    $affiliates = Affiliates::get_affiliates(array('affid' => $core->user['mainaffiliate']), array('returnarray' => true));
    $factypes = FacilityMgmtFactypes::get_data(array('isActive' => 1), array('returnarray' => true));
    $types = FacilityMgmtFactypes::get_data(array('isActive' => 1), array('returnarray' => true));

    if(is_array($types)) {
        $types = array_keys($types);
        $facilities = FacilityMgmtFacilities::get_data(array('affid' => $core->user['mainaffiliate'], 'isActive' => 1, 'type' => $types), array('returnarray' => true, 'operators' => (array('type' => 'IN'))));
    }
    else {
        $facilities = FacilityMgmtFacilities::get_data(array('affid' => $core->user['mainaffiliate'], 'isActive' => 1), array('returnarray' => true));
    }
    if(!isset($core->input['id'])) {
        $affiliate_list = parse_selectlist('facility[affid]', 1, $affiliates, $core->user['mainaffilaite'], '', '', array('width' => '150px', 'blankstart' => true));
        $factypes_list = parse_selectlist('facility[type]', 1, $factypes, '', '', '', array('width' => '150px', 'blankstart' => true));
        // If type isMainLocation it cannot have a parent.
        $facilities_list = parse_selectlist('facility[parent]', 1, $facilities, '', '', '', array('width' => '150px', 'blankstart' => true));
    }
    else if(isset($core->input['id'])) {
        $facility = FacilityMgmtFacilities::get_data(array('fmfid' => intval($core->input['id'])));
        if(!is_object($facility)) {
            redirect('index.php?module=facilitymgmt/list');
        }
        $facility = $facility->get();
        $facility['dimensions'] = explode("x", $facility['dimensions']);
        if(is_array($facility['dimensions'])) {
            $facility['x'] = $facility['dimensions'][0];
            $facility['y'] = $facility['dimensions'][1];
            $facility['z'] = $facility['dimensions'][2];
        }
        if($facility['isActive'] == 1) {
            $checked['isActive'] = "checked='checked'";
        }
        if($facility['allowReservation'] == 1) {
            $checked['allowReservation'] = "checked='checked'";
        }
        $affiliate_list = parse_selectlist('facility[affid]', 1, $affiliates, $facility['affid'], '', '', array('width' => '150px', 'blankstart' => true));
        $factypes_list = parse_selectlist('facility[type]', 1, $factypes, $facility['type'], '', '', array('width' => '150px', 'blankstart' => true));
        $facilities_list = parse_selectlist('facility[parent]', 1, $facilities, $facility['parent'], '', '', array('width' => '150px', 'blankstart' => true));
    }
    eval("\$managefacility= \"".$template->get('facilitymgmt_managefacility')."\";");
    output($managefacility);
}
else if($core->input['action'] == 'do_perform_managefacility') {
    unset($core->input['identifier'], $core->input['module'], $core->input['action']);
    $facility = new FacilityMgmtFacilities();
    $facility->set($core->input['facility']);
    $facility->save();
    switch($facility->get_errorcode()) {
        case 0:
        case 1:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 2:
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
            break;
    }
}