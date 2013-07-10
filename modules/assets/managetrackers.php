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
		$trackerid = $db->escape_string($core->input['id']);
		$asset = new Asset();
		$actiontype = 'Edit';
		$trackers = $asset->get_trackingdevices($trackerid);
	}
	else {
		$actiontype = 'Add';
	}
	$asset = new Asset();
	$affiliate_assets = $asset->get_affiliateassets();  /* get assets for user affiliates */
	foreach($affiliate_assets as $id => $affasset) {
		$assetslist.='<option value="'.$id.'">'.$affasset['title'].'</option>';
	}

	eval("\$assetsmanagetrackers = \"".$template->get('assets_managetrackers')."\";");
	output_page($assetsmanagetrackers);
}
else {
	$asset = new Asset();
	if($core->input['action'] == 'do_Add' || $core->input['action'] == 'do_Edit') {
		$core->input['tracker']['atdid'] = $db->escape_string($core->input['atdid']);
		if($core->input['action'] == 'do_Edit') {
			$options['operationtype'] = 'update';
			$lang->successfullysaved = 'Successfully Update';
		}
		else {
			$options = array();
		}
		$asset->manage_tracker($core->input['tracker'], $options);
		switch($asset->get_errorcode()) {
			case 0:
				output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
				break;
			case 1:
				output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
				break;
			case 2:
				output_xml("<status>false</status><message>{$lang->entryexsist}</message>");
				break;
		}
	}
}
?>
