<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Assign asset to user
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
	$assets = new Assets();
	if($core->input['type'] == 'edit' && isset($core->input['id'])) {
		$auid = $db->escape_string($core->input['id']);
		$assignee = $assets->get_assigneduser($auid);
		$actiontype = 'edit';
	}
	else {
		$actiontype = 'add';
	}

	$assetslist = $assets->get_affiliateassets(array('titleonly' => 1, 'mainaffidonly' => 1));
	if(is_array($assetslist)) {
		$assets_selectlist = parse_selectlist('assignee[asid]', 1, $assetslist, $assignee['asid']);
		$affiliate = new Affiliates($core->user['mainaffiliate']);
		$affiliate_users = $affiliate->get_users(array('ismain' => 1, 'displaynameonly' => 1));
		$employees_selectlist = parse_selectlist('assignee[uid]', 1, $affiliate_users, $assignee['uid']);
	} else {
		$assets_list = $employees_list = $lang->na;
	}
	eval("\$assetsassign = \"".$template->get('assets_assign')."\";");
	output_page($assetsassign);
}
else {
	$assets = new Assets();
	if($core->input['action'] == 'do_add' || $core->input['action'] == 'do_edit') {
		if($core->input['action'] == 'do_add') {
			$assets->assign_assetuser($core->input['assignee']);
		}
		elseif($core->input['action'] == 'do_edit') {
			$core->input['assignee']['auid'] = $db->escape_string($core->input['auid']);
			$assignee = $assets->get_assigneduser($core->input['assignee']['auid']);

			if(TIME_NOW > ($assignee['assignedon'] + ($core->settings['assets_preventeditasgnafter']))) {
				$field_tounset = array('uid', 'asid', 'fromDate', 'toDate', 'fromTime', 'toTime');
				foreach($field_tounset as $field) {
					unset($core->input['assignee'][$field]);
				}
			}
			if(TIME_NOW > ($assignee['assignedon'] + ($core->settings['assets_preventconditionupdtafter']))) {
				output_xml("<status>false</status><message>{$lang->assetexpired}</message>");
				exit;
			}
			$assets->update_assetuser($core->input['assignee']);
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
			case 401:
				output_xml("<status>false</status><message>{$lang->invaliddatetime}</message>");
				break;
			case 601:
			output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
			break;
		}
	}
}
?>
