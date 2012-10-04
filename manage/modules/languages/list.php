<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Modify Language File
 * $module: Admin/Languages
 * $id: edit.php
 * Created: 	@tony.assaad 	August 10, 2012 | 11:00:00 AM	
 * Last Update: @tony.assaad 	August 10, 2012 | 11:00:00 AM	
 */
 
if(!defined("DIRECT_ACCESS")) {
	die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['admin_canModifyLangFiles'] == 0 && $core->usergroup['admin_canCreateLangFiles'] == 0) {
	error($lang->sectionnopermission);
	exit;
} 

if(!$core->input['action']) {
	$list_lang_files = $lang->get_list_languages();
	if(is_array($list_lang_files)) {
		foreach($list_lang_files as $listfiles) {
			$listfiles['timeCreated_output'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $listfiles['timeCreated']);
			$listfiles['timeModified_output'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $listfiles['timeModified']);
			$rowclass = alt_row($rowclass);
			
			if($core->usergroup['admin_canModifyLangFiles'] == 1) {
				$editfile_link = DOMAIN.'/'.ADMIN_DIR.'/index.php?module=languages/manage&amp;type=edit&amp;filename='.$listfiles['fileName'];
			}	
			eval("\$listlang_rows .= \"".$template->get('admin_languages_listlang_row')."\";");
		}
	}
	
	eval("\$langfilesList = \"".$template->get('admin_languages_listlang')."\";");
	output_page($langfilesList);
}
?>