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
		Asset::add_asset($data);
	}
	else {
		Asset::edit_asset($core->input['asid'], $data);
	}
}

//echo '<pre>'.print_r($core->input, true).'</pre>';

if(isset($core->input['delete_asset'])) {
	Asset::delete_asset($core->input['delete_asset']);
}


$resolve = array('affid' => array('table' => 'affiliates', 'id' => 'affid', 'name' => 'name'));
$query = 'SELECT * FROM '.Tprefix.'assets';
$query = $db->query($query);
$assetslist = '<div id="assetslisting">
	<table cellspacing=0 cellpadding=4 border=1  width="100%"><tr bgcolor="#91B64F">
	<th>'.$lang->asid.'</th>
	<th>'.$lang->afid.'</th>
	<th>'.$lang->title.'</th>
	<th>'.$lang->type.'</th>
	<th>'.$lang->status.'</th>
	<th>'.$lang->edit.'</th>
	<th>'.$lang->delete.'</th>
	<th>'.$lang->open.'</th></tr>';


if($db->num_rows($query) > 0) {
	while($row = $db->fetch_assoc($query)) {
		$assetslist.='<tr><td align="center" value="'.$row['asid'].'">'.$row['asid'].'</td>';
		$assetslist.='<td value="'.$row['asid'].'">'.get_name_from_id($row['affid'], $resolve['affid']['table'], $resolve['affid']['id'], $resolve['affid']['name']).'</td>';
		$assetslist.='<td value="'.$row['title'].'">'.$row["title"].'</td>';
		$assetslist.='<td value="'.$row['type'].'">'.$row["type"].'</td>';
		$assetslist.='<td value="'.$row['status'].'">'.$row["status"].'</td>';
		$assetslist.='

<td align="center">

<button e_affid="$row["affid"]" e_title="$row["title"]" e_type="$row["type"]" e_status="$row["status"]" type="submit" style="cursor:pointer;border: 0; background: transparent" name="edit_asset" value="'.$row['asid'].'" alt="Edit" id="edit_entry_'.$row['asid'].'"><img src="'.DOMAIN.'/images/edit.gif"/></button>
	</td>
<script src="'.$core->settings["rootdir"].'/js/object_explorer.js" type="text/javascript"></script>
<script>
$(document).ready(function() {
	$("[id^=edit_entry_]").click(function() {
		z_jots.ShowDump($(this).parent().html);
		//$(this).parent().parent().children("td").each(function() {});

	});
});


</script>
<!--</form>-->';
		$assetslist.='<form name="asset_delete" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/manageassets">
					<td align="center">
					<button type="submit" style="cursor:pointer;border: 0; background: transparent" name="delete_asset" value="'.$row['asid'].'" alt="Edit"><img src="'.DOMAIN.'/images/invalid.gif"/></button>
					</td></form>';
		$assetslist.='<td><a href="'.DOMAIN.'/index.php?module=assets/assets&action=list&asid='.$row['asid'].'">open</a></td></tr>';
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
	<tr><td>'.$lang->afid.'</td><td>'.parse_selectlist('affid', 1, getAffiliateList(), $core->input['e_affid']).'</td></tr>
	<tr><td>'.$lang->title.'</td><td><input type="text" name="Title" tabindex="2" value="'.$core->input['e_title'].'"/></td></tr>
	<tr><td>'.$lang->type.'</td><td><input type="text" name="Type" tabindex="3" value="'.$core->input['e_type'].'"/></td></tr>
	<tr><td>'.$lang->status.'</td><td><input type="text" name="Status" tabindex="4" value="'.$core->input['e_status'].'"/></td></tr>
	<td colspan=2><input type="submit" name="saveasset" value="Save" tabindex="5"/></tr></td></table></form>';
}
else {
	$assetedit = '<form name="asset_save" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/manageassets">
	<input type="hidden" name="asid" />
	<table cellspacing=5 cellpadding=4 border=0>
	<tr><td>'.$lang->afid.'</td><td>'.parse_selectlist('affid', 1, getAffiliateList(), '').'</td></tr>
	<tr><td>'.$lang->title.'</td><td><input type="text" name="Title" tabindex="2"/></td></tr>
	<tr><td>'.$lang->type.'</td><td><input type="text" name="Type" tabindex="3"/></td></tr>
	<tr><td>'.$lang->status.'</td><td><input type="text" name="Status" tabindex="4"/></td></tr>
	<td colspan=2><input type="submit" name="saveasset" value="Save" tabindex="5"/></tr></td></table></form>';
}

$pagetitle = $lang->assetsmanagepage;
$pagecontents = $assetedit.'<hr><br>'.$assetslist;
eval("\$assetslist = \"".$template->get('assets_assets')."\";");
echo $assetslist;
?>
