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

if(isset($core->input['saveasset'])) {
	$data['affid'] = $core->input['affid'];
	$data['title'] = $core->input['Title'];
	$data['type'] = $core->input['Type'];
	$data['status'] = $core->input['Status'];
	if(!isset($core->input['asid']) || $core->input['asid'] == "") {
		$db->insert_query('assets', $data);
	}
	else {
		$db->update_query('assets', $data, 'asid='.$core->input['asid']);
	}
}

$query = 'SELECT * FROM '.Tprefix.'assets';
$query = $db->query($query);
$assetslist = '<div id="assetslisting">
	<table cellspacing=0 cellpadding=4 border=1><tr>
	<th>Asset ID</th>
	<th>Affiliate</th>
	<th>Title</th>
	<th>Type</th>
	<th>Status</th>
	<th>Edit</td></tr>';

$resolve = array('affid' => array('table' => 'affiliates', 'id' => 'affid', 'name' => 'name'));
//echo '<pre>'.print_r($core->input, true).'</pre>';
if($db->num_rows($query) > 0) {
	while($row = $db->fetch_assoc($query)) {
		$assetslist.='<tr><td>'.$row['asid'].'</td>';
		$assetslist.='<td>'.get_name_from_id($row['affid'], $resolve['affid']['table'], $resolve['affid']['id'], $resolve['affid']['name']).'</td>';
		$assetslist.='<td>'.$row["title"].'</td>';
		$assetslist.='<td>'.$row["type"].'</td>';
		$assetslist.='<td>'.$row["status"].'</td>';
		$assetslist.='<form name="asset_edit" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/manageassets"><input type="hidden" name="e_affid" value="'.$row['affid'].'"/><input type="hidden" name="e_title" value="'.$row["title"].'"/><input type="hidden" name="e_type" value="'.$row["type"].'"/><input type="hidden" name="e_status" value="'.$row["status"].'"/><td><button type="submit" name="edit_asset" value="'.$row['asid'].'" alt="Edit"><img src="'.DOMAIN.'/images/edit.gif"/></button></td></tr></form>';
	}
	$assetslist.='</table></div>';
}
else {
	$assetslist = '<div id="assetslist"></div>';
}

if(isset($core->input['edit_asset'])) {
	$assetedit = '<form name="asset_save" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/manageassets">
	<input type="hidden" name="asid" value="'.$core->input['edit_asset'].'"/>
	<table cellspacing=5 cellpadding=4 border=0>
	<tr><td>Affiliate</td><td>'.parse_selectlist('affid', 1, getAffiliateList(), $core->input['e_affid']).'</td></tr>
	<tr><td>Title</td><td><input type="text" name="Title" tabindex="2" value="'.$core->input['e_title'].'"/></td></tr>
	<tr><td>Type</td><td><input type="text" name="Type" tabindex="3" value="'.$core->input['e_type'].'"/></td></tr>
	<tr><td>Status</td><td><input type="text" name="Status" tabindex="4" value="'.$core->input['e_status'].'"/></td></tr>
	<td colspan=2><input type="submit" name="saveasset" value="Save" tabindex="5"/></tr></td></form>';
}
else {
	$assetedit = '<form name="asset_save" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/manageassets">
	<input type="hidden" name="asid" />
	<table cellspacing=5 cellpadding=4 border=0>
	<tr><td>Affiliate</td><td>'.parse_selectlist('affid', 1, getAffiliateList(), '').'</td></tr>
	<tr><td>Title</td><td><input type="text" name="Title" tabindex="2"/></td></tr>
	<tr><td>Type</td><td><input type="text" name="Type" tabindex="3"/></td></tr>
	<tr><td>Status</td><td><input type="text" name="Status" tabindex="4"/></td></tr>
	<td colspan=2><input type="submit" name="saveasset" value="Save" tabindex="5"/></tr></td></form>';
}

$pagetitle = $lang->assetsmanagepage;
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

?>
