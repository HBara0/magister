<?php
/*
 * Copyright © 2012 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: assets.php
 * Created:        @alain.paulikevitch    Nov 26, 2012 | 10:21:56 AM
* Last Update:     @tony.assaad		      June 24, 2013 | 10:21:56 AM
 */

if(!defined("DIRECT_ACCESS")) {
	die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['assets_canTrack'] == 0) {
	error($lang->sectionnopermission);
	exit;
}
if(!$core->input['action']) {
	$asset = new Assets();

	$map = new Maps(array(), array('infowindow' => 1, 'mapcenter' => '32.887078, 34.195312'));
	$data = $asset->get_assets_data(array(8 => 10));

	foreach($data as $key => $trackedasset) {
		foreach($trackedasset as $key => $value) {
			$assets_location_ouput = '';
			$altrow_class = alt_row($altrow_class);
			$assets_location .= '<span  class="'.$altrow_class.'" style="width:100%; position:relative;">'.$map->get_streetname($value['latitude'], $value['longitude']).'<br></span>';
		}
	}
	$assets_map .= $asset->get_map($data);
	eval("\$assetslist = \"".$template->get('assets_assets')."\";");
	output_page($assetslist);
}
?>
