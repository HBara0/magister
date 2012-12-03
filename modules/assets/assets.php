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
$data=$asset->get_data_for_assets(array(8=>8));
foreach ($data as $key=>$trackedasset) {
	foreach ($trackedasset as $key=>$value) {
		$pagecontents .= Maps::get_streetname($value['latitude'],$value['longitude']).'<br>';
	}
}

$pagecontents .= $asset->get_map($data,array('infowindow' => 1, 'mapcenter' => '33.89, 35.51','zoom'=>14));
eval("\$assetslist = \"".$template->get('assets_assets')."\";");
echo $assetslist;

?>
