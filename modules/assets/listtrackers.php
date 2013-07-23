<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: listtrackers.php
 * Created:        @tony.assaad    Jun 25, 2013 | 4:07:14 PM
 * Last Update:    @tony.assaad    Jun 25, 2013 | 4:07:14 PM
 */

if($core->usergroup['assets_canManageAssets'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!$core->input['action']) {
	$assets = new Asset();
	
	
	
	eval("\$assets_trackerslist= \"".$template->get('assets_trackerslist')."\";");
	output_page($assets_trackerslist);
}
?>
