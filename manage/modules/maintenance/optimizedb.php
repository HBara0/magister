<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Optimize DB
 * $module: admin/maintenance
 * $id: optimizedb.php	
 * Last Update: @zaher.reda 	Mar 18, 2009 | 03:33 AM
 */
if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canPerformMaintenance'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {

    $query = $db->query("SHOW TABLES FROM `".$config['database']['database']."`");
    while(list($table) = $db->fetch_array($query)) {
        $tables[$table] = $table;
    }

    $tables_list = parse_selectlist("tables[]", 1, $tables, "", 1);
    eval("\$optimizepage = \"".$template->get("admin_maintenance_optimizedb")."\";");
    output_page($optimizepage);
}
else {
    if($core->input['action'] == "do_perform_optimizedb") {
        foreach($core->input['tables'] as $key => $val) {
            $db->optimize_table($val);
            $db->analyze_table($val);
        }
        output_xml("<status>true</status><message>{$lang->tablesoptimized}</message>");
        log_action();
    }
}
?>