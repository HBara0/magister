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
		Asset::add_assignedasset($data);
	}
	else {
		Asset::edit_assignedasset($core->input['auid'], $data);
	}
}

if(isset($core->input['delete_assignedasset'])) {
	Asset::delete_assignedasset($core->input['delete_assignedasset']);
}

$resolve = array(
		'uid' => array('table' => 'users', 'id' => 'uid', 'name' => 'displayName'),
		'asid' => array('table' => 'assets', 'id' => 'asid', 'name' => 'title')
);
$query = 'SELECT * FROM '.Tprefix.' assets_users';
$query = $db->query($query);
$assetslist = '<div id="assetslisting">
	<table cellspacing=0 cellpadding=4 border=1 width="100%"><tr bgcolor="#91B64F">
	<th>'.$lang->auid.'</th>
	<th>'.$lang->uid.'</th>
	<th>'.$lang->asid.'</th>
	<th>'.$lang->from.'</th>
	<th>'.$lang->to.'</th>
	<th>'.$lang->edit.'</td>
	<th>'.$lang->delete.'</th></tr>';


//echo '<pre>'.print_r($core->input, true).'</pre>';
if($db->num_rows($query) > 0) {
	while($row = $db->fetch_assoc($query)) {
		$assetslist.='<tr><td align="center">'.$row['auid'].'</td>';
		$assetslist.='<td>'.get_name_from_id($row['uid'], $resolve['uid']['table'], $resolve['uid']['id'], $resolve['uid']['name']).'</td>';
		$assetslist.='<td>'.get_name_from_id($row["asid"], $resolve['asid']['table'], $resolve['asid']['id'], $resolve['asid']['name']).'</td>';
		$assetslist.='<td>'.date('F d, Y', $row["fromDate"]).'</td>';
		$assetslist.='<td>'.date('F d, Y', $row["toDate"]).'</td>';
		$assetslist.='<form name="asset_assign" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/assignassets">
			<input type="hidden" name="e_uid" value="'.$row['uid'].'"/>
			<input type="hidden" name="e_asid" value="'.$row["asid"].'"/>
			<input type="hidden" name="e_dateFrom" value="'.$row["fromDate"].'"/>
			<input type="hidden" name="e_dateTo" value="'.$row["toDate"].'"/><td align="center"><button type="submit" style="cursor:pointer;border: 0; background: transparent" name="edit_asset_assign" value="'.$row['auid'].'" alt="Edit"><img src="'.DOMAIN.'/images/edit.gif"/></button></td></form>';
		$assetslist.='<form name="assignedasset_delete" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/assignassets">
					<td align="center">
					<button type="submit" style="cursor:pointer;border: 0; background: transparent" name="delete_assignedasset" value="'.$row['auid'].'" alt="Edit"><img src="'.DOMAIN.'/images/invalid.gif"/></button>
					</td></form></tr>';
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
	<tr><td>'.$lang->uid.'</td><td>'.parse_selectlist('uid', 1, getAllUsers(), $core->input['e_uid']).'</td></tr>
	<tr><td>'.$lang->asid.'</td><td>'.parse_selectlist('asid', 2, Asset::getAllAssets(), $core->input['e_asid']).'</td></tr>
	<tr><td>'.$lang->from.'</td><td><input type="text" name="datefrom" id="pickDateFrom" tabindex="3" value="'.date('F d, Y', $core->input['e_dateFrom']).'"/></td></tr>
	<tr><td>'.$lang->to.'</td><td><input type="text" name="dateto" id="pickDateTo" tabindex="4" value="'.date('F d, Y', $core->input['e_dateTo']).'"/></td></tr>
	<td colspan=2><input type="submit" name="assignasset" value="Save" tabindex="5"/></tr></td></table></form>';
}
else {
	$assetedit = '<form name="asset_save" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/assignassets">
	<input type="hidden" name="asid" />
	<table cellspacing=5 cellpadding=4 border=0>
	<tr><td>'.$lang->uid.'</td><td>'.parse_selectlist('uid', 1, getAllUsers(), array()).'</td></tr>
	<tr><td>'.$lang->asid.'</td><td>'.parse_selectlist('asid', 2, Asset::getAllAssets(), array()).'</td></tr>
	<tr><td>'.$lang->from.'</td><td><input type="text" name="datefrom" id="pickDateFrom" tabindex="3" value=""/></td></tr>
	<tr><td>'.$lang->to.'</td><td><input type="text" name="dateto" id="pickDateTo" tabindex="4" value=""/></td></tr>
	<td colspan=2><input type="submit" name="assignasset" value="Save" tabindex="5"/></tr></td></table></form>';
}

$pagetitle = $lang->assignassetspage;
$pagecontents = $assetedit.'<hr><br>'.$assetslist;
eval("\$assetslist = \"".$template->get('assets_assets')."\";");
echo $assetslist;

function getAllUsers() {
	global $db;
	$result = array();
	$affiliateslist=getAffiliateList();
	$affiliates=array();
	foreach($affiliateslist as $key => $value) {
		$affiliates[]=$key;
	}
	$query = 'SELECT DISTINCT(u.uid),displayName,affid FROM '.Tprefix.'users as u INNER JOIN affiliatedemployees as a ON (a.uid=u.uid) WHERE gid<>7 AND affid IN ('.implode(',',$affiliates).')';
	$query = $db->query($query);
	if($db->num_rows($query) > 0) {
		while($row = $db->fetch_assoc($query)) {
			$result[$row['uid']] = $row['displayName'];
		}
	}
	return $result;
}

?>
