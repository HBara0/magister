<?php
/*
 * Copyright © 2012 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: manageassets.php
 * Created:        @alain.paulikevitch    Nov 29, 2012 | 7:43:48 PM
 * Last Update:    @alain.paulikevitch    Nov 29, 2012 | 7:43:48 PM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['assets_canManageAssets'] == 0) {
	error($lang->sectionnopermission);
	exit;
}



if(isset($core->input['savetracker'])) {

	$data['deviceId'] = $core->input['deviceId'];
	$data['asid'] = $core->input['asid'];
	$data['fromDate'] = strtotime($core->input['fromDate']);
	$data['toDate'] = strtotime($core->input['toDate']);

	if(!isset($core->input['atdid']) || $core->input['atdid'] == "") {
		$db->insert_query('assets_trackingdevices', $data);
	}
	else {
		$db->update_query('assets_trackingdevices', $data, 'atdid='.$core->input['atdid']);
	}
}

$query = 'SELECT * FROM '.Tprefix.'assets_trackingdevices';
$query = $db->query($query);
$assetslist = '<div id="trackerslisting">
	<table cellspacing=0 cellpadding=4 border=1><tr>
	<th>Tracker Id</th>
	<th>Device Id</th>
	<th>Asset</th>
	<th>From</th>
	<th>To</th>
	<th>Edit</td></tr>';

$resolve = array(
			'asid' => array('table' => 'assets', 'id' => 'asid', 'name' => 'title'),
		);



if($db->num_rows($query) > 0) {
	while($row = $db->fetch_assoc($query)) {
		$assetslist.='<tr><td>'.$row['atdid'].'</td>';
		$assetslist.='<td>'.$row['deviceId'].'</td>';
		$assetslist.='<td>'.get_name_from_id($row["asid"],$resolve['asid']['table'], $resolve['asid']['id'], $resolve['asid']['name']).'</td>';
		$assetslist.='<td>'.date('F d, Y',$row["fromDate"]).'</td>';
		$assetslist.='<td>'.date('F d, Y',$row["toDate"]).'</td>';
		$assetslist.='<form name="tracker_edit" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/managetrackers">
			<input type="hidden" name="e_atdid" value="'.$row['atdid'].'"/>
			<input type="hidden" name="e_deviceid" value="'.$row["deviceId"].'"/>
			<input type="hidden" name="e_asid" value="'.$row["asid"].'"/>
			<input type="hidden" name="e_fromdate" value="'.$row["fromDate"].'"/>
			<input type="hidden" name="e_todate" value="'.$row["toDate"].'"/>
			<td><button type="submit" name="edit_tracker" value="'.$row['atdid'].'" alt="Edit"><img src="'.DOMAIN.'/images/edit.gif"/></button></td></tr></form>';
	}
	$assetslist.='</table></div>';
}
else {
	$assetslist = '<div id="assetslist"></div>';
}



if(isset($core->input['edit_tracker'])) {
	$assetedit = '<form name="asset_save" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/managetrackers">
	<input type="hidden" name="atdid" value="'.$core->input['e_atdid'].'"/>
	<table cellspacing=5 cellpadding=4 border=0>
	<tr><td>Device Id</td><td><input type="text" name="deviceId" value="'.$core->input['e_deviceid'].'"/></td></tr>
	<tr><td>Asset ID</td><td>'.parse_selectlist('asid',2,getAssetsList(),$core->input['e_asid']).'</td></tr>
	<tr><td>From</td><td><input type="text" name="fromDate" tabindex="3" id="pickDateFrom" value="'.date('F d, Y',$core->input['e_fromdate']).'"/></td></tr>
	<tr><td>To</td><td><input type="text" name="toDate" tabindex="4" id="pickDateTo" value="'.date('F d, Y',$core->input['e_todate']).'"/></td></tr>
	<td colspan=2><input type="submit" name="savetracker" value="Save" tabindex="5"/></tr></td></form>';
}
else {
	$assetedit = '<form name="asset_save" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/managetrackers">
	<input type="hidden" name="asid" />
	<table cellspacing=5 cellpadding=4 border=0>
	<tr><td>Device Id</td><td><input type="text" name="deviceId" tabindex="1"/></td></tr>
	<tr><td>Asset Id</td><td>'.parse_selectlist('asid',2,getAssetsList(),'').'</td></tr>
	<tr><td>From</td><td><input type="text" name="fromDate" id="pickDateFrom" tabindex="3"/></td></tr>
	<tr><td>To</td><td><input type="text" name="toDate" id="pickDateTo" tabindex="4"/></td></tr>
	<td colspan=2><input type="submit" name="savetracker" value="Save" tabindex="5"/></tr></td></form>';
}


$pagetitle = $lang->trackersmanagepage;
$pagecontents = $assetslist.$assetedit;
eval("\$assetslist = \"".$template->get('assets_assets')."\";");
echo $assetslist;
function getAffiliateList($idsonly = false) {
	global $core, $db;
	if($core->usergroup['canViewAllAff'] == 0) {
		$tmpaffiliates = $core->user['affiliates'];
		foreach($tmpaffiliates as $value) {
			if($idsonly) {
				$affiliates[$value] = $value;
			}
			else {
				$affiliates[$value] = get_name_from_id($value, 'affiliates', 'affid', 'name');
			}
		}
	}
	else {
		$affiliates_query = $db->query('SELECT affid,name from '.Tprefix.'affiliates');
		if($db->num_rows($affiliates_query) > 0) {
			while($affiliate = $db->fetch_assoc($affiliates_query)) {
				if($idsonly) {
					$affiliates[$affiliate['affid']] = $affiliate['affid'];
				}
				else {
					$affiliates[$affiliate['affid']] = $affiliate['name'];
				}
			}
		}
	}
	asort($affiliates);
	return $affiliates;
}

function getAssetsList($idsonly = false) {
	$assets = array();
	global $core, $db;
	$query = $db->query('SELECT asid,title from '.Tprefix.'assets');
	if($db->num_rows($query) > 0) {
		while($asset = $db->fetch_assoc($query)) {
			if($idsonly) {
				$assets[$asset['asid']] = $asset['asid'];
			}
			else {
				$assets[$asset['asid']] = $asset['title'];
			}
		}
	}
	asort($assets);
	return $assets;
}

?>
