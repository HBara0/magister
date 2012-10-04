<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Backup database
 * $module: admin/maintenance
 * $id: backupdb.php	
 * Last Update: @zaher.reda 	Mar 23, 2009 | 11:36 AM
 */
if(!defined('DIRECT_ACCESS'))
{
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canPerformMaintenance'] == 0) {
	error($lang->sectionnopermission);
}

if(!$core->input['action']) {
	$query = $db->query("SHOW TABLES FROM `".$config['database']['database']."`");
	while(list($table) = $db->fetch_array($query)) {
		$tables[$table] = $table;
	}
	
	$tables_list = parse_selectlist("tables[]", 1, $tables, '', 1);
	eval("\$backuppage = \"".$template->get('admin_maintenance_backupdb')."\";");
	output_page($backuppage);
}
else {
	if($core->input['action'] == 'do_perform_backupdb') {
		$time = date($core->settings['dateformat'].' '.$core->settings['timeformat'], time());
		$filename = 'backup_'.$time;
		
		header('Content-Type: text/x-sql');
		header('Content-Disposition: attachment; filename="'.$filename.'.sql"');
		
		$header = "-- OCOS Database Backup\n-- Generated: {$time}\n-- -------------------------------------\n\n";
		$contents = $header;
		
		foreach($core->input['tables'] as $table) {
			$field_list = array();
			$fields_array = $db->show_fields_from($table);
			
			foreach($fields_array as $field)
			{
				$field_list[] = $field['Field'];
			}
			
			$fields = implode(',', $field_list);
			
			$structure = $db->show_create_table($table).";\n";
			$contents .= $structure;
			
			$query = $db->query("SELECT * FROM ".Tprefix."{$table}");
			while($row = $db->fetch_array($query))
			{
				$insert = "INSERT INTO {$table} ($fields) VALUES (";
				$comma = '';
				foreach($field_list as $field)
				{
					if(!isset($row[$field]) || trim($row[$field]) == "")
					{
						$insert .= $comma."''";
					}
					else
					{
						$insert .= $comma."'".$db->escape_string($row[$field])."'";
					}
					$comma = ',';
				}
				$insert .= ");\n";
				$contents .= $insert;
			}
		}
		echo $contents;
		log_action();
	}
}
?>