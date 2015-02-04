<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: listwarehouses.php
 * Created:        @rasha.aboushakra    Feb 3, 2015 | 12:18:26 PM
 * Last Update:    @rasha.aboushakra    Feb 3, 2015 | 12:18:26 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['contents_canManageWarehouses'] == 0) {
    error($lang->sectionnopermission);
    exit;
}
if(!$core->input['action']) {

    $affiliate_where = ' name LIKE "%orkila%" AND isActive=1';
    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliate_where .= ' AND affid IN ('.$inaffiliates.')';
    }
    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 0, $affiliate_where);
    $warehouses = Warehouses::get_data(array('affid' => array_keys($affiliates)), array('returnarray' => true, 'operators' => array('affid' => 'IN')));

    if(is_array($warehouses)) {
        foreach($warehouses as $warehouse) {
            $edit_link = "<a href='index.php?module=contents/createwarehouses&amp;id={$warehouse->wid}'><img src='{$core->settings[rootdir]}/images/icons/edit.gif' border='0' alt='{$lang->editwarehouse}' /></a>";
            $delete_link = "<a href='#{$warehouse->wid}' id='deletewarehouse_{$warehouse->wid}_contents/listwarehouses_icon'><img src='{$core->settings[rootdir]}/images/invalid.gif' border='0' alt='{$lang->deletewarehouse}' /></a>";
            $affiliate = new Affiliates($warehouse->affid);
            $city = new Cities($warehouse->ciid);
            $country = new Countries($warehouse->coid);
            $rowclass = alt_row($rowclass);
            eval("\$warehouse_rows .= \"".$template->get('contents_warehouses_list_row')."\";");
            $edit_link = $delete_link = '';
        }
    }

    eval("\$warehouseslist = \"".$template->get('contents_warehouses_list')."\";");
    output_page($warehouseslist);
}
else {
    if($core->input['action'] == 'perform_deletewarehouse') {
        $warehouse = new Warehouses($db->escape_string($core->input['todelete']));
        $tables = $db->get_tables_havingcolumn('wid', 'TABLE_NAME !="warehouses"');
        if(is_array($tables)) {
            foreach($tables as $table) {
                $core->input['todelete'] = str_replace('_', ' ', $core->input['todelete']);
                $query = $db->query("SELECT * FROM ".Tprefix.$table." WHERE wid=".$db->escape_string($core->input['todelete'])." ");
                if($db->num_rows($query) > 0) {
                    output_xml("<status>false</status><message>{$lang->cannotdeletewarehouse}</message>");
                    exit;
                }
            }
        }
        $warehouse->delete();
        if($warehouse->delete()) {
            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
            exit;
        }
    }
    elseif($core->input['action'] == 'get_deletewarehouse') {
        eval("\$revokeleavebox = \"".$template->get('popup_deletewarehouse')."\";");
        output($revokeleavebox);
    }
}
?>