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

if(!isset($core->input["action"])) {
	$core->input["action"] = "map";
}

$asset = new Asset();

$pagecontents = '<form name="assets_filter" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/assets&action='.$core->input['action'].'">
<input type="text" name="fromDate" id="pickDateFrom" tabindex="1"/><br>
<input type="text" name="toDate" id="pickDateTo" tabindex="2"/><br>
<input type="submit" name="filter_assets" value="Filter" tabindex="3"/><br>
		</form>';


$data = $asset->get_data_for_users(array($core->user['uid']));

//$pagecontents='<pre>'.print_r($core->user,true).'</pre>';
if($core->input["action"] == "map") {
	$pagetitle = $lang->assetstrackmap;
	$pagecontents .= $asset->get_map($data, array('infowindow' => 1, 'mapcenter' => '33.89, 35.51', 'zoom' => 14));
}
elseif($core->input["action"] == "list") {
	$pagetitle = $lang->assetstrackpage;
	if(isset($data)) {
		foreach($data as $key => $trackedasset) {
			foreach($trackedasset as $key => $value) {
				$pagecontents .= Maps::get_streetname($value['latitude'], $value['longitude']).'<br>';
			}
		}
	}
}



eval("\$assetslist = \"".$template->get('assets_assets')."\";");
echo $assetslist;
?>
