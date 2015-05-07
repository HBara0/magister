<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: tableslist.php
 * Created:        @hussein.barakat    Apr 20, 2015 | 3:21:17 PM
 * Last Update:    @hussein.barakat    Apr 20, 2015 | 3:21:17 PM
 */
if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}
if($core->usergroup['admin_canManageSystemDef'] == 0) {
    error($lang->sectionnopermission);
    exit;
}
$lang = new Language('english', 'admin');
$lang->load('tables_meta');
$lang->load('global');

if(!$core->input['action']) {

    $table_objs = SystemTables::get_data('', array('returnarray' => true, 'order' => 'tableName'));
    if(is_array($table_objs) && !empty($table_objs)) {
        foreach($table_objs as $table_obj) {
            $tabledata = $table_obj->get();
            $waddings = base64_encode(serialize($tabledata));
            eval("\$tableslist_rows .= \"".$template->get('admin_tables_tableslist_rows')."\";");
            unset($waddings);
        }
    }
    eval("\$popup_addtable = \"".$template->get('popup_manage_tableslist_addtable')."\";");
    eval("\$tableslist = \"".$template->get('admin_tables_tableslist')."\";");
    output_page($tableslist);
}
else {
    if($core->input['action'] == 'do_addtable') {
        $table_obj = new SystemTables();
        $table_obj->set($core->input['table_data']);
        $table_obj->save();
        switch($table_obj->get_errorcode()) {
            case 0:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            case 1:
                output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
                break;
            case 2:
            default:
                output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
                break;
        }
        exit;
    }
    elseif($core->input['action'] == 'get_addsystable') {
        $table_obj = new SystemTables($core->input['id']);
        $table_data = $table_obj->get();
        eval("\$popup_addtable = \"".$template->get('popup_manage_tableslist_addtable')."\";");
        output($popup_addtable);
    }
}