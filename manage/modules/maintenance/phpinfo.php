<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * View PHP Info
 * $module: admin/maintenance
 * $id: phpinfo.php	
 * Last Update: @zaher.reda 	Mar 13, 2009 | 04:18 PM
 */
if(!defined("DIRECT_ACCESS"))
{
	die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canPerformMaintenance'] == 0) {
	error($lang->sectionnopermission);
	exit;
}


if($core->input['action'] == "phpinfo") {
	phpinfo();
	exit;
}

if(!$core->input['action']) {

	$php_info = '<iframe src="index.php?module=maintenance/phpinfo&amp;action=phpinfo" width="100%" height="500" frameborder="0">{$lang->no_iframe_support}</iframe>';
	eval("\$infopage = \"".$template->get("admin_maintenance_phpinfo")."\";");
	output_page($infopage);
}	
?>