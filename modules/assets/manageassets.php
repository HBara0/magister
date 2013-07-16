<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: manageassets.php
 * Created:        @tony.assaad    Jun 24, 2013 | 11:09:38 AM
 * Last Update:    @tony.assaad    Jun 24, 2013 | 11:09:38 AM
 * 
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['assets_canManageAssets'] == 0) {
	error($lang->sectionnopermission);
	exit;
}


if(!$core->input['action']) {
	$affiliate = new Affiliates($core->user['affiliates']);
	$assetstype = get_specificdata('assets_types', array('astid','name','title'), 'astid', 'title', 'title');
	$assets_status = array('damaged' => 'damaged', 'not-functional' => 'not-functional', 'fully-functional' => 'fully-functional');
	if($core->input['type'] == 'edit' && isset($core->input['id'])) {
		$asid = $db->escape_string($core->input['id']);
		$asset = new Asset($asid);
		$assets = $asset->get();
		$actiontype = 'Edit';
		$assets_type = parse_selectlist('asset[type]', 3, $assetstype, $assets['title']);
		$assetsstatus = parse_selectlist('asset[status]', 4, $assets_status, $assets['status']);
	}
	else {
		$actiontype = 'Add';
	}

	$affiliatesquery = $db->query("SELECT affid,name FROM ".Tprefix."affiliates WHERE affid IN('".implode(',', $core->user['affiliates'])."')");

	while($affiliates_user = $db->fetch_assoc($affiliatesquery)) {
		$affiliate_list.='<option value="'.$affiliates_user['affid'].'">'.$affiliates_user['name'].'</option>';
	}

	$assets_type = parse_selectlist('asset[type]', 3, $assetstype, '');
	$assetsstatus = parse_selectlist('asset[status]', 4, $assets_status, '');

	eval("\$assetsmange = \"".$template->get('assets_manage')."\";");
	output_page($assetsmange);
}
else {
	$asset = new Asset();
	if($core->input['action'] == 'do_Add' || $core->input['action'] == 'do_Edit') {
		$core->input['asset']['asid'] = $db->escape_string($core->input['asid']);
		if($core->input['action'] == 'do_Edit') {
			$options['operationtype'] = 'update';
			$lang->successfullysaved = 'Successfully Update';
		}
		else {
			$options = array();
		}
		$asset->add($core->input['asset'], $options);
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



