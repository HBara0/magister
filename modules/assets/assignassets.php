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

if(isset($core->input['assignasset'])) {
	$data['uid'] = $core->input['uid'];
	$data['asid'] = $core->input['asid'];
	$data['fromDate'] = strtotime($core->input['datefrom']);
	$data['toDate'] = strtotime($core->input['dateto']);
	if(!isset($core->input['auid']) || $core->input['auid'] == "") {
		$db->insert_query('assets_users', $data);
	}
	else {
		$db->update_query('assets_users', $data, 'auid='.$core->input['auid']);
	}
}

$query = 'SELECT * FROM '.Tprefix.' assets_users';
$query = $db->query($query);
$assetslist = '<div id="assetslisting">
	<table cellspacing=0 cellpadding=4 border=1><tr>
	<th>Assignment ID</th>
	<th>User</th>
	<th>Asset</th>
	<th>fromDate</th>
	<th>toDate</th>
	<th>Edit</td></tr>';

$resolve = array('uid' => array('table' => 'users', 'id' => 'uid', 'name' => 'displayName'));
//echo '<pre>'.print_r($core->input, true).'</pre>';


if($db->num_rows($query) > 0) {
	while($row = $db->fetch_assoc($query)) {
		$assetslist.='<tr><td>'.$row['auid'].'</td>';
		$assetslist.='<td>'.get_name_from_id($row['uid'], $resolve['uid']['table'], $resolve['uid']['id'], $resolve['uid']['name']).'</td>';
		$assetslist.='<td>'.$row["asid"].'</td>';
		$assetslist.='<td>'.date('F d, Y',$row["fromDate"]).'</td>';
		$assetslist.='<td>'.date('F d, Y',$row["toDate"]).'</td>';
		$assetslist.='<form name="asset_assign" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/assignassets">
			<input type="hidden" name="e_uid" value="'.$row['uid'].'"/>
			<input type="hidden" name="e_asid" value="'.$row["asid"].'"/>
			<input type="hidden" name="e_dateFrom" value="'.$row["fromDate"].'"/>
			<input type="hidden" name="e_dateTo" value="'.$row["toDate"].'"/>
			<td><button type="submit" name="edit_asset_assign" value="'.$row['auid'].'" alt="Edit"><img src="'.DOMAIN.'/images/edit.gif"/></button></td></tr></form>';
	}
	$assetslist.='</table></div>';
}
else {
	$assetslist = '<div id="assetslist"></div>';
}

if(isset($core->input['edit_asset_assign'])) {
	$assetedit = '<form name="asset_assign" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/assignassets">
	<input type="hidden" name="auid" value="'.$core->input['edit_asset_assign'].'"/>
	<table cellspacing=5 cellpadding=4 border=0>
	<tr><td>User</td><td>'.parse_selectlist('uid', 1, getAllUsers(), $core->input['e_uid']).'</td></tr>
	<tr><td>Asset</td><td>'.parse_selectlist('asid', 2, getAllAssets(), $core->input['e_asid']).'</td></tr>
	<tr><td>dateFrom</td><td><input type="text" name="datefrom" id="pickDateFrom" tabindex="3" value="'.date('F d, Y',$core->input['e_dateFrom']).'"/></td></tr>
	<tr><td>dateTo</td><td><input type="text" name="dateto" id="pickDateTo" tabindex="4" value="'.date('F d, Y',$core->input['e_dateTo']).'"/></td></tr>
	<td colspan=2><input type="submit" name="assignasset" value="Save" tabindex="5"/></tr></td></form>';
}
else {
	$assetedit = '<form name="asset_save" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/assignassets">
	<input type="hidden" name="asid" />
	<table cellspacing=5 cellpadding=4 border=0>
	<tr><td>User</td><td>'.parse_selectlist('uid', 1, getAllUsers(),array()).'</td></tr>
	<tr><td>Asset</td><td>'.parse_selectlist('asid', 2, getAllAssets(),array()).'</td></tr>
	<tr><td>dateFrom</td><td><input type="text" name="datefrom" id="pickDateFrom" tabindex="3" value=""/></td></tr>
	<tr><td>dateTo</td><td><input type="text" name="dateto" id="pickDateTo" tabindex="4" value=""/></td></tr>
	<td colspan=2><input type="submit" name="assignasset" value="Save" tabindex="5"/></tr></td></form>';
}

$pagetitle = $lang->assignassetspage;
$pagecontents = $assetslist.$assetedit;
eval("\$assetslist = \"".$template->get('assets_assets')."\";");
echo $assetslist;
function getAllAssets() {
	global $db;
	$result = array();
	$query = 'SELECT asid,title FROM '.Tprefix.'assets';
	$query = $db->query($query);
	if($db->num_rows($query) > 0) {
		while($row = $db->fetch_assoc($query)) {
			$result[$row['asid']] = $row['title'];
		}
	}
	return $result;
}

function getAllUsers() {
	global $db;
	$result = array();
	$query = 'SELECT uid,displayName FROM '.Tprefix.'users';
	$query = $db->query($query);
	if($db->num_rows($query) > 0) {
		while($row = $db->fetch_assoc($query)) {
			$result[$row['uid']] = $row['displayName'];
		}
	}
	return $result;
}

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

?>
