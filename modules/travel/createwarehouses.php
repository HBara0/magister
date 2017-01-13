<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: createwarehouse.php
 * Created:        @rasha.aboushakra    Feb 3, 2015 | 10:57:13 AM
 * Last Update:    @rasha.aboushakra    Feb 3, 2015 | 10:57:13 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['contents_canManageWarehouses'] == 0) {
    error($lang->sectionnopermission);
}
if(!$core->input['action']) {

    $affiliate_where = ' name LIKE "%orkila%" AND isActive=1';
    if($core->usergroup ['canViewAllAff'] == 0) {
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliate_where .= ' AND affid IN ('.$inaffiliates.')';
    }
    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 0, $affiliate_where);

    if(isset($core->input['id']) && !empty($core->input['id'])) {
        $warehouse = Warehouses::get_data(array('wid' => $core->input['id']));
        if(is_object($warehouse)) {
            if($warehouse->isActive == 1) {
                $checked['isactive'] = 'checked="checked"';
            }
            $affiliates_list = parse_selectlist('warehouse[affid]', '', $affiliates, $warehouse->affid, 0, '', array('id' => 'warehouse_affid'));
            $city = new Cities($warehouse->ciid);
            $warehouse->city = $city->get_displayname();
            eval("\$addwarehouse = \"".$template->get('contents_warehouses_add')."\";");
            output_page($addwarehouse);
        }
        else {
            redirect($_SERVER['HTTP_REFERER'], 2, $lang->nomatchfound);
        }
    }
    else {
        $affiliates_list = parse_selectlist('warehouse[affid]', '', $affiliates, '', 0, '', array('id' => 'warehouse_affid'));
        eval("\$addwarehouse = \"".$template->get('contents_warehouses_add')."\";");
        output_page($addwarehouse);
    }
}
if($core->input['action'] == 'do_perform_createwarehouses') {
    unset($core->input['identifier'], $core->input['module'], $core->input['action']);
    $warehouse = new Warehouses();
    $warehouse->set($core->input['warehouse']);
    $warehouse->save();
    switch($warehouse->get_errorcode()) {
        case 0:
        case 1:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 2:
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
            break;
    }
}
?>