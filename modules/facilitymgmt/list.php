<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: list.php
 * Created:        @rasha.aboushakra    Sep 23, 2015 | 10:01:21 AM
 * Last Update:    @rasha.aboushakra    Sep 23, 2015 | 10:01:21 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['facilitymgmt_canManageFacilities'] == 0) {
    error($lang->sectionnopermission);
}
if(!isset($core->input['action'])) {
    if($core->input['view'] == 'tree') {
        $facilities = FacilityMgmtFacilities::get_facilities_tree();
        if(is_array($facilities)) {
            $facility = new FacilityMgmtFacilities();
            $facilities_list = $facility->parse_facility_list($facilities);
//        foreach($endprod_objs as $endprod_obj) {
//            $altrow_class = alt_row($altrow_class);
//            $productypes = $endprod_obj->get();
//            $productypes['application'] = $endprod_obj->get_application()->get()['title'];
//            eval("\$productstypes_list .= \"".$template->get('admin_productstypes_rows')."\";");
//        }
        }
        else {
            $facilities_list = '<tr><td colspan="3">'.$lang->na.'</td></tr>';
        }
        eval("\$facilitiestree= \"".$template->get('facilitymgmt_facilitytree')."\";");
        output_page(array('content' => $facilitiestree, 'title' => $lang->facilities));
    }
    else {
        $facilities = FacilityMgmtFacilities::get_data('name IS NOT NULL', array('returnarray' => true));
        if(is_array($facilities)) {
            foreach($facilities as $facilitiy) {
                $edit_link = '<a href="index.php?module=facilitymgmt/managefacility&amp;id= '.$facilitiy->fmfid.'" title = "'.$lang->modifyfacility.'"><img src = ./images/icons/edit.gif border = 0 alt = '.$lang->edit.'/></a>';
                $delete_link = "<a href='#{$facilitiy->fmfid}' id='deletefacility_{$facilitiy->fmfid}_facilitymgmt/list_loadpopupbyid'><img src='{$core->settings[rootdir]}/images/invalid.gif' border='0' alt='{$lang->deletefacility}' /></a>";

                $facility_data['name'] = $facilitiy->get_displayname();
                $affiliate = new Affiliates($facilitiy->affid);
                if(is_object($affiliate) && !empty($affiliate->{Affiliates::PRIMARY_KEY})) {
                    $facility_data['affiliate'] = $affiliate->get_displayname();
                }
                $type = new FacilityMgmtFactypes($facilitiy->type);
                $facility_data['type'] = 'N/A';
                if(is_object($type) && !empty($type->{FacilityMgmtFactypes::PRIMARY_KEY})) {
                    $facility_data['type'] = $type->get_displayname();
                }
                $parent = new FacilityMgmtFacilities($facilitiy->parent);
                $facility_data['parent'] = 'N/A';
                if(is_object($parent) && !empty($parent->{FacilityMgmtFacilities::PRIMARY_KEY})) {
                    $facility_data['parent'] = $parent->get_displayname();
                }
                $facility_data['isactiveicon'] = '<img src="./images/false.gif" />';
                if($facilitiy->isActive == 1) {
                    $facility_data['isactiveicon'] = '<img src="./images/true.gif" />';
                }
                $rowclass = alt_row($rowclass);
                eval("\$facilities_rows .= \"".$template->get('facilitymgmt_facilityrow')."\";");
                $edit_link = $delete_link = '';
                unset($facility_data);
            }
        }
        eval("\$facilitytypelist= \"".$template->get('facilitymgmt_facilitylist')."\";");
        output_page($facilitytypelist);
    }
}
else {
    if($core->input['action'] == 'deletefacility') {
        $facility = new FacilityMgmtFacilities(intval($core->input['todelete']));
        if($facility->delete_facility($facility->fmfid)) {
            output_xml("<status>true</status><message>{$lang->successfullydeleted}</message>");
            exit;
        }
        else {
            output_xml("<status>false</status><message>{$lang->cannotdelete}</message>");
            exit;
        }
    }
    elseif($core->input['action'] == 'get_deletefacility') {
        $id = intval($core->input['id']);
        eval("\$deletefacility = \"".$template->get('popup_deletefacility')."\";");
        output($deletefacility);
    }
}