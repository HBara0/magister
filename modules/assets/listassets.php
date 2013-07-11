<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: 'listassets.php
 * Created:        @tony.assaad    Jun 25, 2013 | 2:56:12 PM
 * Last Update:    @tony.assaad    Jun 25, 2013 | 2:56:12 PM
 */


if($core->usergroup['assets_canManageAssets'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!$core->input['action']) {
	$assets = new Asset();
	$all_assets = $assets->get_affiliateassets();
	$sort_url = sort_url();

	foreach($all_assets as $asset) {
		$affilate = new Affiliates($asset['affid']);
		$asset['affiliate'] = $affilate->get_country()->get()['name'];
		eval("\$assets_listrow .= \"".$template->get('assets_listrow')."\";");
	}

	eval("\$assets_list = \"".$template->get('assets_list')."\";");
	output_page($assets_list);
}
elseif($core->input['action'] == 'get_deleteasset') {
	eval("\$deleteasset = \"".$template->get("popup_assets_listassetsdelete")."\";");
	echo $deleteasset;
}
elseif($core->input['action'] == 'get_editasset') {
	$asid = $db->escape_string($core->input['id']);
	$asset = new Asset($asid);
	$assets = $asset->get();
	$assetstype = get_specificdata('assets_types', array('astid', 'name', 'title'), 'astid', 'title', 'title');
	$assets_status = array('damaged' => 'damaged', 'notfunctional' => 'not-functional', 'fullyfunctional' => 'fully-functional');

	$assets_type = parse_selectlist('asset[type]', 3, $assetstype, $assets['type']);
	$assetsstatus = parse_selectlist('asset[status]', 4, $assets_status, $assets['status']);

	$affilate = new Affiliates($assets['affid']);
	$assets['affiliate'] = $affilate->get_country()->get()['name'];
	$affiliate_list = '<option value="'.$assets['affid'].'">'.$assets['affiliate'].'</option>';
	$actiontype = $lang->edit;
	eval("\$editasset = \"".$template->get("popup_assets_listassetsedit")."\";");
	echo $editasset;
}
elseif($core->input['action'] == 'perform_delete') {
	$asid = $db->escape_string($core->input['todelete']);
	echo $asid;
	$asset = new Asset();
	$asset->deactivate_asset($asid);
	switch($asset->get_errorcode()) {
		case 3:
			output_xml("<status>true</status><message>{$lang->successfullydeleted}</message>");
			break;
	}
}
?>
