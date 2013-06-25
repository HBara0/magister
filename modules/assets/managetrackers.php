<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: managetrackers.php
 * Created:        @tony.assaad    Jun 24, 2013 | 3:44:46 PM
 * Last Update:    @tony.assaad    Jun 24, 2013 | 3:44:46 PM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['assets_canManageAssets'] == 0) {
	error($lang->sectionnopermission);
	exit;
}


if(!$core->input['action']) {
	if($core->input['type'] == 'edit' && isset($core->input['id'])) {
		$asid = $db->escape_string($core->input['id']);
		$actiontype = 'Edit';
	}
	else {
		$actiontype = 'Add';
	}
	$asset = new Asset();
	$assetslist = $asset->get_allassets();
	print_r($assetslist);

	eval("\$assetsmanagetrackers = \"".$template->get('assets_managetrackers')."\";");
	output_page($assetsmanagetrackers);
}
?>
