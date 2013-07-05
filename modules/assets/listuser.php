<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: listuser.php
 * Created:        @tony.assaad    Jul 4, 2013 | 12:06:47 PM
 * Last Update:    @tony.assaad    Jul 4, 2013 | 12:06:47 PM
 */

if($core->usergroup['assets_canManageAssets'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!$core->input['action']) {
	$asset = new Asset();
	$assignee = $asset->get_allassignee();

	if(is_array($assignee)) {
		foreach($assignee as $auid => $assigneduser) {
			$assigneduser['fromDate_output'] = date($core->settings['dateformat'], $assigneduser['fromDate']);
			$assigneduser['toDate_output'] = date($core->settings['dateformat'], $assigneduser['toDate']);
			$auid = $assigneduser['auid'];
			/* Get assigned assets by assets object */
			$asset = new Asset($assigneduser['asid']);
			$assigneduser['asset'] = $asset->get()['title'];

			/* Get assigned USER by user object */
			$user = new Users($assigneduser['uid']);
			$employee = $user->get();
			eval("\$assignee_list .= \"".$template->get('assets_assigneelist')."\";");
		}
	}
	else {
		$assignee_list = '<tr><td colspan="7">'.$lang->na.'</td></tr>';
	}


	eval("\$assetsassignlist = \"".$template->get('assets_assignlist')."\";");
	output_page($assetsassignlist);
}
elseif($core->input['action'] == 'perform_delete') {
	echo'delete';
	$asset = new Asset();
	$asset->delete_userassets();
}
?>
