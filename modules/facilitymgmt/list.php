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

    $facilities = FacilityMgmtFacilities::get_data('name IS NOT NULL', array('returnarray' => true));
    if(is_array($facilities)) {
        foreach($facilities as $facilitiy) {
            $edit_link = '<a href="index.php?module=facilitymgmt/managefacility&amp;id= '.$facilitiy->fmfid.'" title = "'.$lang->modifyfacility.'"><img src = ./images/icons/edit.gif border = 0 alt = '.$lang->edit.'/></a>';
            $delete_link = "<a href='#{$facilitiy->fmfid}' id='deletefacility_{$facilitiy->fmfid}_facilitymgmt/list_icon'><img src='{$core->settings[rootdir]}/images/invalid.gif' border='0' alt='{$lang->deletefacility}' /></a>";

            $facility_data['name'] = $facilitiy->get_displayname();
            $affiliate = new Affiliates($facilitiy->affid);
            if(is_object($affiliate)) {
                $facility_data['affiliate'] = $affiliate->get_displayname();
            }
            $type = new FacilityMgmtFactypes($facilitiy->type);
            if(is_object($type)) {
                $facility_data['type'] = $affiliate->get_displayname();
            }
            $parent = new FacilityMgmtFacilities($facilitiy->parent);
            if(is_object($parent)) {
                $facility_data['parent'] = $affiliate->get_displayname();
            }
            $facility_data['isactveicon'] = '<img src="./images/false.gif" />';
            if($facilitiy->isActive == 1) {
                $facility_data['isactveicon'] = '<img src="./images/true.gif" />';
            }
            $rowclass = alt_row($rowclass);
            eval("\$facilities_rows .= \"".$template->get('facilitymgmt_facilityrow')."\";");
            $edit_link = $delete_link = '';
        }
    }
    eval("\$facilitieslist= \"".$template->get('facilitymgmt_facilitylist')."\";");
    output($facilitieslist);
}
else {
    if($core->input['action'] == 'perform_deletefacility') {
        $facility = new FacilityMgmtFacilities($db->escape_string($core->input['todelete']));
//        $attributes = array('', '');
//        foreach($attributes as $attribute) {
//            $tables = $db->get_tables_havingcolumn($attribute, 'TABLE_NAME !="facilitymgmt_facilities"');
//            if(is_array($tables)) {
//                foreach($tables as $table) {
//                    $core->input['todelete'] = str_replace('_', ' ', $core->input['todelete']);
//                    $query = $db->query("SELECT * FROM ".Tprefix.$table." WHERE ".$attribute."=".$db->escape_string($core->input['todelete'])." ");
//                    if($db->num_rows($query) > 0) {
//                        output_xml("<status>false</status><message>{$lang->cannotdeletefacility}</message>");
//                        exit;
//                    }
//                }
//            }
//        }
        $facility->delete();
        if($facility->delete()) {
            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
            exit;
        }
    }
    elseif($core->input['action'] == 'get_deletefacility') {
        eval("\$deletefacility = \"".$template->get('popup_deletefacility')."\";");
        output($deletefacility);
    }
}