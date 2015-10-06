<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: typeslist.php
 * Created:        @hussein.barakat    Oct 6, 2015 | 9:05:45 AM
 * Last Update:    @hussein.barakat    Oct 6, 2015 | 9:05:45 AM
 */


if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['facilitymgmt_canManageFacilities'] == 0) {
    error($lang->sectionnopermission);
}
if(!isset($core->input['action'])) {
    $filters_config = array(
            'parse' => array('filters' => array('title', 'typecategory', 'isActive'),
                    'overwriteField' => array(
                            'typecategory' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->typecategory.'"/>',
                            'isActive' => parse_selectlist('filters[isActive]', '1', array('1' => $lang->yes, '0' => $lang->no), $core->input['filters']['isActive'], 1, '', array('blankstart' => true)),
                    )
            ),
            'process' => array(
                    'filterKey' => FacilityMgmtFactypes::PRIMARY_KEY,
                    'mainTable' => array(
                            'name' => FacilityMgmtFactypes::TABLE_NAME,
                            'filters' => array('isActive' => array('operatorType' => 'equal', 'name' => 'isActive')),
                    ),
            )
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    $filter_where = null;
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = ' '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));


    $types = FacilityMgmtFactypes::get_data($filter_where, array('returnarray' => true));
    if(is_array($types)) {
        foreach($types as $type) {
            $edit_link = '<td width="5%"><a target="_blank" href="index.php?module=facilitymgmt/managefacilitytype&amp;id= '.$type->fmftid.'" title = "'.$lang->modifyfacilitytype.'"><img src = ./images/icons/edit.gif border = 0 alt = '.$lang->edit.'/></a></td>';
//          $delete_link = "<a href='#{$facilitiy->fmfid}' id='deletefacility_{$facilitiy->fmfid}_facilitymgmt/list_loadpopupbyid'><img src='{$core->settings[rootdir]}/images/invalid.gif' border='0' alt='{$lang->deletefacility}' /></a>";
            $type_output['title'] = $type->get_displayname();
            if($type->isRoom == 1) {
                $type_output['typecategory'] = $lang->isroom;
            }
            else if($type->isCoWorkingSpace == 1) {
                $type_output['typecategory'] = $lang->iscoworkingspace;
            }
            else if($type->isMainLocation == 1) {
                $type_output['typecategory'] = $lang->ismainlocation;
            }
            $type_output['isActive'] = '<img src = ./images/false.gif border = 0 alt = '.$lang->no.'/>';
            if($type->isActive == 1) {
                $type_output['isActive'] = '<img src = ./images/valid.gif border = 0 alt = '.$lang->yes.'/>';
            }
            $rowclass = alt_row($rowclass);
            eval("\$facilitytypes_rows .= \"".$template->get('facilitymgmt_facilitytyperow')."\";");
            $edit_link = $delete_link = '';
            unset($type_output);
        }
    }
    eval("\$facilitieslist= \"".$template->get('facilitymgmt_facilitytypeslist')."\";");
    output_page($facilitieslist);
}
//else {
//    if($core->input['action'] == 'deletefacility') {
//        $facility = new FacilityMgmtFacilities(intval($core->input['todelete']));
//        if($facility->delete_facility($facility->fmfid)) {
//            output_xml("<status>true</status><message>{$lang->successfullydeleted}</message>");
//            exit;
//        }
//        else {
//            output_xml("<status>false</status><message>{$lang->cannotdelete}</message>");
//            exit;
//        }
//    }
//    elseif($core->input['action'] == 'get_deletefacility') {
//        $id = intval($core->input['id']);
//        eval("\$deletefacility = \"".$template->get('popup_deletefacility')."\";");
//        output($deletefacility);
//    }
//}