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


$map = new Maps(array(), array('infowindow' => 1, 'mapcenter' => '32.887078, 34.195312'));
$data=$asset->get_data_for_assets(array(8=>8));
foreach ($data as $key=>$trackedasset) {
	foreach ($trackedasset as $key=>$value) {
		$pagecontents .= $map->get_streetname($value['latitude'],$value['longitude']).'<br>';
	}
}

$pagecontents .= $asset->get_map($data);
eval("\$assetslist = \"".$template->get('assets_assets')."\";");
echo $assetslist;

?>
