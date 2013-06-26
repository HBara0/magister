<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: assignassets.php
 * Created:        @tony.assaad    Jun 25, 2013 | 3:58:55 PM
 * Last Update:    @tony.assaad    Jun 25, 2013 | 3:58:55 PM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['assets_canManageAssets'] == 0) {
	error($lang->sectionnopermission);
	exit;
}
if(!$core->input['action']) {
	
	
	
	
	
	eval("\$assetsassign = \"".$template->get('assets_assign')."\";");
	output_page($assetsassign);
}








?>
