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
?>
