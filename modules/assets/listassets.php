<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * List available assets
 * $id: 'listassets.php
 * Created:        @tony.assaad    Jun 25, 2013 | 2:56:12 PM
 * Last Update:    @tony.assaad    Jun 25, 2013 | 2:56:12 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['assets_canManageAssets'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $assets_status = array(1 => $lang->damaged, 2 => $lang->notfunctional, 3 => $lang->fullyfunctional);

    $assets = new Assets();
    $sort_url = sort_url();

    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('tag', 'title', 'affid', 'type', 'status', 'createdOn'),
                    'overwriteField' => array('type' => parse_selectlist('filters[type]', 4, get_specificdata('assets_types', array('astid', 'title'), 'astid', 'title', 'title'), '', '', '', array('blankstart' => true)),
                            'status' => parse_selectlist('filters[status]', 4, array('damaged' => 'damaged', 'notfunctional' => 'not-functional', 'fullyfunctional' => 'fully-functional'), '', '', '', array('blankstart' => true)),
                            'asid' => parse_selectlist('filters[affid]', 2, get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', 'affid in('.implode(',', $core->user['affiliates']).')'), ''),
                            'createdOn' => ''
                    ),
                    'fieldsSequence' => array('tag' => 1, 'title' => 2, 'affid' => 3, 'type' => 4, 'status' => 5, 'createdOn' => 6)
            ),
            'process' => array(
                    'filterKey' => 'asid',
                    'mainTable' => array(
                            'name' => 'assets',
                            'filters' => array('title' => 'title', 'affid' => array('operatorType' => 'multiple', 'name' => 'affid'), 'tag' => 'tag', 'type' => 'type', 'status' => 'status'),
                    )
            ),
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    $filters_row_display = 'hide';

    if(true) {/* Later to be, if has permission to view multiple affiliates */
        $get_affassets_options = array('mainaffidonly' => 1);
        $multipage_where = 'affid = '.$core->user['mainaffiliate'];
    }
    else {
        $get_affassets_options = array('mainaffidonly' => 1);
        $multipage_where = 'affid IN ('.$db->escape_string(implode(',', $core->user['affiliates'])).')';
    }

    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = $filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $all_assets = $assets->get_affiliateassets($get_affassets_options, $filter_where);

    if(is_array($all_assets)) {
        foreach($all_assets as $asset) {
            $rowclass = alt_row($rowclass);
            if($asset['isActive'] == 0) {
                $notactive = 'unapproved';
            }
            $affilate = new Affiliates($asset['affid']);

            if(!empty($asset['status'])) {
                $asset['status_output'] = $assets_status[$asset['status']];
            }
            if(!empty($asset['createdOn'])) {
                $asset['createdOn_output'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $asset['createdOn']);
            }
            $asset['affiliate'] = $affilate->get()['name'];
            eval("\$assets_listrow .= \"".$template->get('assets_list_row')."\";");
        }

        $multipages = new Multipages('assets', $core->settings['itemsperlist'], $multipage_where);
        $assets_listrow .= '<tr><td colspan="7">'.$multipages->parse_multipages().'</td></tr>';
    }
    else {
        $assets_listrow = '';
        $assets_listrow = '<tr><td colspan="7">'.$lang->na.'</td></tr>';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));
    /* Perform inline filtering - END */

    eval("\$assets_list = \"".$template->get('assets_list')."\";");
    output_page($assets_list);
}
elseif($core->input['action'] == 'get_deleteasset') {
    $asid = $db->escape_string($core->input['id']);
    $asset = new Assets($asid);
    $asset = $asset->get();

    eval("\$deleteasset = \"".$template->get('popup_assets_listassetsdelete')."\";");
    output($deleteasset);
}
elseif($core->input['action'] == 'get_editasset') {
    $asid = $db->escape_string($core->input['id']);
    $asset_obj = new Assets($asid);
    $asset = $asset_obj->get();

    if($asset['isActive'] == 1) {
        $checkboxes['isActive'] = 'checked="checked"';
    }

    $assetstype = get_specificdata('assets_types', array('astid', 'name', 'title'), 'astid', 'title', 'title');
    $assets_status = array(1 => $lang->damaged, 2 => $lang->notfunctional, 3 => $lang->fullyfunctional);

    $assettypes_selectlist = parse_selectlist('asset[type]', 3, $assetstype, $asset['type']);
    $assetstatus_selectlist = parse_selectlist('asset[status]', 4, $assets_status, $asset['status']);

    $affiliatesquery = $db->query("SELECT affid, name FROM ".Tprefix."affiliates WHERE affid IN ('".implode(',', $core->user['affiliates'])."')");
    while($affiliate = $db->fetch_assoc($affiliatesquery)) {
        $affiliates_list .= '<option value="'.$affiliate['affid'].'">'.$affiliate['name'].'</option>';
    }

    $actiontype = 'edit';
    eval("\$editasset = \"".$template->get('popup_assets_listassetsedit')."\";");
    output($editasset);
}
elseif($core->input['action'] == 'perform_delete') {
    $asid = $db->escape_string($core->input['todelete']);
    $asset = new Assets($asid);
    $asset_relatedtables = array('assets_users', 'assets_trackingdevices');
    foreach($asset_relatedtables as $assettable) {
        if(value_exists($assettable, 'asid', $asid)) {
            $asset->deactivate_asset();
            $operation = 'deactivate';
            break;
        }
        else {
            $asset->delete_asset();
            $operation = 'delete';
        }
    }
    switch($asset->get_errorcode()) {
        case 0:
            if($operation == 'deactivate') {
                output_xml("<status>true</status><message>{$lang->successfullydeactivated}</message>");
            }
            else {
                output_xml("<status>true</status><message>{$lang->successfullydeleted}</message>");
            }
            break;
        case 601:
        case 604:
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
            break;
        case 302:
            output_xml("<status>false</status><message>{$lang->actionnopermission}</message>");
            break;
    }
}
?>
