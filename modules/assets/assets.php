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
	$sort_url = sort_url();
	$asset = new Assets();

	if($core->input['view'] == 'mapview') {
		eval("\$assets_assetlocationmap.= \"".$template->get('assets_assetlocationmap')."\";");
	}

	//$map = new Maps(array(), array('infowindow' => 1, 'mapcenter' => '32.887078, 34.195312'));
	$trackedasset = $asset->get_assets_data();
	if(is_array($trackedasset)) {
		foreach($trackedasset as $key => $assetloc) {
			$assets_location_ouput = '';
			$altrow_class = alt_row($altrow_class);
			$assets = new Assets($assetloc['asid']);
			$assetname = $assets->get()['title'];
			$streetname = json_decode($assetloc['parsedLocation'])->results[0]->formatted_address;

			//$assets_location .= '<span  class="'.$altrow_class.'" style="width:100%; position:relative;">'.$map->get_streetname($value['latitude'], $value['longitude'], $value['parsedLocation']).'<br></span>';
			eval("\$assets_assetstracking_row .= \"".$template->get('assets_assetstrackingrow')."\";");
		}
	}


	eval("\$assetslist = \"".$template->get('assets_assetstracking')."\";");
	output_page($assetslist);
}
?>
