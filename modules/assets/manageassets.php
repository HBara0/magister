<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * Create/Edit Assets
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
	//$affiliate = new Affiliates($core->user['affiliates']);
	$assetstype = get_specificdata('assets_types', array('astid', 'name', 'title'), 'astid', 'title', 'title');
	$assets_status = array(1 => 'damaged', 2 => 'not-functional', 3 => 'fully-functional');
	
	if($core->input['type'] == 'edit' && isset($core->input['id'])) {
		$asid = $db->escape_string($core->input['id']);
		$asset = new Assets($asid);
		$assets = $asset->get();
		$actiontype = 'edit';
	}
	else {
		$actiontype = 'add';
	}

	$affiliatesquery = $db->query("SELECT affid, name FROM ".Tprefix."affiliates WHERE affid IN ('".implode(',', $core->user['affiliates'])."')");
	while($affiliate = $db->fetch_assoc($affiliatesquery)) {
		$affiliates_selectlist .= '<option value="'.$affiliate['affid'].'">'.$affiliate['name'].'</option>';
	}

	$assettypes_selectlist = parse_selectlist('asset[type]', 3, $assetstype, $assets['title']);
	$assetsstatus_selectlist = parse_selectlist('asset[status]', 4, $assets_status, $assets['status']);

	eval("\$assetsmanage = \"".$template->get('assets_manage')."\";");
	output_page($assetsmanage);
}
else {
	$asset = new Assets();
	if($core->input['action'] == 'do_add' || $core->input['action'] == 'do_edit') {
		$core->input['asset']['asid'] = $db->escape_string($core->input['asid']);
		if($core->input['action'] == 'do_edit') {
			$options['operationtype'] = 'update';
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
				output_xml("<status>false</status><message>{$lang->assetexists}</message>");
				break;
		}
	}
}
?>



