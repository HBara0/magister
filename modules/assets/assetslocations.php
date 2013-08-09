<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: assetslocations.php
 * Created:        @tony.assaad    Aug 6, 2013 | 9:17:04 AM
 * Last Update:    @tony.assaad    Aug 6, 2013 | 9:17:04 AM
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
	$asid = $core->input['asset'];
	$asset = new Assets($asid);
	$asset_locations = $asset->get_asset_data('', '', $asid, 'topnew');
	if($core->input['view'] == 'list') {
		if(is_array($asset_locations)) {
			$locations_grid = '<table class="datatable highlight" border=1 style="display:block; overflow:hidden; width:100%;">';

			foreach($asset_locations as $key => $locations) {
				$altrow_class = alt_row($altrow_class);
				$timeLine[$key] = date('H:i A', $locations['timeLine']);
				$locations_grid.='<tr class="highlight"><td style="width:40%;">'.$timeLine[$key].'</td> <td style="width:60%;">'.json_decode($locations['parsedLocation'])->results[0]->formatted_address.'</td></tr>';
			}
			$locations_grid.='</table>';
		}
	}

	if($core->input['view'] == 'mapview') {
		$assets_map = $asset->get_map($asset_locations);
		$change_view_icon = 'list_view.gif';
		$change_view_url = preg_replace("/&view=[A-Za-z]+/i", '&view=list', $sort_url);
	}
	else {
		$change_view_icon = 'thumbnail_view.gif';
		if(isset($core->input['view'])) {
			$change_view_url = preg_replace("/&view=[A-Za-z]+/i", '&view=mapview', $sort_url);
		}
		else {
			$change_view_url = $sort_url.'&view=mapview';
		}
	}

	$view = $core->input['view'];

	eval("\$assets_assetlocationmap = \"".$template->get('assets_assetlocationmap')."\";");
	output_page($assets_assetlocationmap);
}
elseif($core->input['action'] == "getlocations") {
	if($core->input['view'] == 'mapview' || $core->input['view'] == 'list') {
		$fromtime = strtotime($db->escape_string($core->input['fromDate']));
		$totime = strtotime($db->escape_string($core->input['toDate']));
		$asid = $db->escape_string($core->input['asid']);
		$view = $db->escape_string($core->input['view']);
		$asset = new Assets($asid);
		$asset_locations = $asset->get_asset_data($fromtime, $totime, $asid);

		if($view == 'mapview') {
			//$map = new Maps(array(), array('infowindow' => 1, 'mapcenter' => '32.887078, 34.195312'));
			/* change long att in json structure */
			header('Content-type: text/javascript');
			'var photos = jQuery.parseJSON([{"places": {"id":"2326", "title":"Maarouf Saad, Lebanon","otherinfo":"some other info","lat":35.43782,"lng":33.46776,"link":"","hasInfoWindow":1 }}]);';
			echo'alert("places")';
			exit;
			$assets_map = $asset->get_map($asset_locations);

			echo $assets_map;
		}
		elseif($view == 'list') {
			if(is_array($asset_locations)) {
				$locations_grid = '<table class="datatable" style=" overflow:hidden; width:100%;">';

				foreach($asset_locations as $key => $locations) {
					$altrow_class = alt_row($altrow_class);
					$timeLine[$key] = date('H:i A', $locations['timeLine']);
					$locations_grid.='<tr class="'.$altrow_class.'"><td style="width:40%;">'.$timeLine[$key].'</td> <td style="width:60%;">'.json_decode($locations['parsedLocation'])->results[0]->formatted_address.'</td></tr>';
				}

				$locations_grid.='</table>';
				echo $locations_grid;
			}
			else {
				echo $lang->na;
			}
		}
	}
}
?>
