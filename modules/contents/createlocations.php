<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: createlocations.php
 * Created:        @tony.assaad    Dec 9, 2014 | 12:07:15 PM
 * Last Update:    @tony.assaad    Dec 9, 2014 | 12:07:15 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canAddSuppliers'] == 0 && $core->usergroup['canAddCustomers'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

$lang->load('contents_addentities');

if(!$core->input['action']) {
    $locationtypes = array('plant' => $lang->plant, 'office' => $lang->office, 'showroom' => $lang->showroom, 'rdlab' => $lang->rdlab);
    $locationstypes_list = parse_selectlist('entitylocation[locationType]', 1, $locationtypes, '', '', '', array('required' => 'required', 'blankstart' => true));

    $countries = Countries::get_data();
    $countries_list = parse_selectlist('entitylocation[coid]', 2, $countries, $core->user_obj->get_mainaffiliate()->get_country()->coid, '', '', array('required' => 'required', 'blankstart' => true));

    eval("\$addlocations = \"".$template->get('contents_entities_creatlocations')."\";");
    output_page($addlocations);
}
else if($core->input['action'] === 'do_perform_createlocations') {
    $entity_loc = new EntityLocations();
    $entity_loc->set($core->input['entitylocation']);
    $entity_loc->save();
    switch($entity_loc->get_errorcode()) {
        case 0:
        case 1:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 2:
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
            break;
    }
}