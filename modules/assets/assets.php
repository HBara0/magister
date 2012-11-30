<?php
/*
 * Copyright © 2012 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: assets.php
 * Created:        @alain.paulikevitch    Nov 26, 2012 | 10:21:56 AM
 * Last Update:    @alain.paulikevitch    Nov 26, 2012 | 10:21:56 AM
 */

if(!defined("DIRECT_ACCESS")) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['assets_canTrack'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

$pagetitle="My Title";
$pagecontents = 'Hello this is a new module<hr>';
$asset = new Asset();
$pagecontents .=$asset->get_map();
eval("\$assetslist = \"".$template->get('assets_assets')."\";");
echo $assetslist;

?>
