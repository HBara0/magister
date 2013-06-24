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
$affiliate= new Affiliates($core->user['affid']);
	if($core->input['type'] == 'edit' && isset($core->input['id'])) {
		$actiontype = 'Edit';
	}
	else {
		$actiontype = 'Add';
	}
	$aff=$affiliate->get();
	$affiliate_list = parse_selectlist('affid', 1, $aff, $core->input['e_affid']);
	eval("\$assetsmange = \"".$template->get('assets_manage')."\";");
	output_page($assetsmange);
}
else {
	$asset = new Asset();
	if($core->input['action'] == 'do_Add' || $core->input['action'] == 'do_Edit') {
		if($core->input['action'] == 'do_Edit') {
			$options['operationtype'] = 'update';
			$lang->successfullysaved = 'Successfully Update';
		}
		else {
			$options = array();
		}

		$asset->add($core->input['asset'], $options);
	}
}
?>



