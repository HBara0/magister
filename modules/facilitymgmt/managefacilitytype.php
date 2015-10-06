<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: managefacilitytypes.php
 * Created:        @hussein.barakat    Oct 6, 2015 | 11:10:30 AM
 * Last Update:    @hussein.barakat    Oct 6, 2015 | 11:10:30 AM
 */


if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['facilitymgmt_canManageFacilities'] == 0) {
    error($lang->sectionnopermission);
}
if(!isset($core->input['action'])) {
    $fields = array('isRoom' => $lang->isroom, 'isCoWorkingSpace' => $lang->iscoworkingspace, 'isMainLocation' => $lang->ismainlocation);
    if(!isset($core->input['id'])) {
        $typeselectlist = parse_selectlist('type[maintype]', '1', $fields, '');
        $isactive = '<input type="checkbox" name="type[isActive]" value="1" '.$checked.'>';
    }
    else if(isset($core->input['id'])) {
        $facilitytype_obj = FacilityMgmtFactypes::get_data(array('fmftid' => intval($core->input['id'])));
        if(!is_object($facilitytype_obj)) {
            redirect('index.php?module=facilitymgmt/list');
        }
        $typeselectlist = parse_selectlist('type[maintype]', '1', $fields, '');
        foreach($fields as $name => $field) {
            if($facilitytype_obj->$name == 1) {
                $typeselectlist = parse_selectlist('type[maintype]', '1', $fields, $name);
                break;
            }
        }
        $facilitytype['description'] = $facilitytype_obj->description;
        $checked = '';
        if($facilitytype_obj->isActive == 1) {
            $checked = 'checked="checked"';
        }
        $isactive = '<input type="checkbox" name="type[isActive]" value="1" '.$checked.'>';
        $facilitytype['title'] = $facilitytype_obj->get_displayname();
    }
    eval("\$managefacility= \"".$template->get('facilitymgmt_managefacilitytypes')."\";");
    output($managefacility);
}
else if($core->input['action'] == 'do_perform_managefacilitytype') {
    $facilitytype = new FacilityMgmtFactypes();
    $core->input['type']['name'] = generate_alias($core->input['type']['title']);
    if(!empty($core->input['type']['maintype'])) {
        $core->input['type'][$core->input['type']['maintype']] = 1;
    }
    else {
        output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
        exit;
    }
    $facilitytype->set($core->input['type']);
    $facilitytype->save();
    switch($facilitytype->get_errorcode()) {
        case 0:
        case 1:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 2:
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
            break;
    }
}