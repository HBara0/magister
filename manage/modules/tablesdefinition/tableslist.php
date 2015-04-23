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
$lang = new Language('english', 'admin');
$lang->load('tables_meta');
$lang->load('global');

if(!$core->input['action']) {


    $table_objs = SystemTables::get_data('', array('returnarray' => true, 'order' => 'tablename'));
    if(is_array($table_objs) && !empty($table_objs)) {
        foreach($table_objs as $table_obj) {
            $tabledata = $table_obj->get();
        }
        $waddings = base64_encode(serialize($tabledata));
        eval("\$tableslist_rows .= \"".$template->get('admin_tables_tableslist_rows')."\";");
    }
    eval("\$tableslist = \"".$template->get('admin_tables_tableslist')."\";");
    output_page($tableslist);
}