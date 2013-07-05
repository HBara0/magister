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
	$assets = new Asset();
	if($core->input['type'] == 'edit' && isset($core->input['id'])) {
		$auid = $db->escape_string($core->input['id']);
		$assignee = $assets->get_assigneduser($auid);
		$actiontype = $lang->edit;
	}
	else {
		$actiontype = $lang->add;
	}

	$assetslist = $assets->get_affiliateassets();
	$assets_list = parse_selectlist('assignee[asid]', 1, $assetslist, $assignee['asid']);
	$assigners = $assets->get_assignto();
	$employees_list = parse_selectlist('assignee[uid]', 1, $assigners, $assignee['uid']);


	eval("\$assetsassign = \"".$template->get('assets_assign')."\";");
	output_page($assetsassign);
}
else {
	$assets = new Asset();
	if($core->input['action'] == 'do_Add' || $core->input['action'] == 'do_Edit') {
		if($core->input['action'] == 'do_Add') {
			$assets->assign_assetuser($core->input['assignee']);
		}
		elseif($core->input['action'] == 'do_Edit') {
			$core->input['assignee']['auid'] = $db->escape_string($core->input['auid']);
			$assets->update_assetuser($core->input['assignee']);
			$lang->successfullysaved = 'Successfully Update';
		}

		switch($assets->get_errorcode()) {
			case 0:
				output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
				break;
			case 1:
				output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
				break;
			case 2:
				output_xml("<status>false</status><message>{$lang->assetexitsamedate}</message>");
				break;
		}
	}
}
?>
