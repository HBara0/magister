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
		Asset::add_tracker($data);
	}
	else {
		Asset::edit_tracker($core->input['atdid'], $data);
	}
}

if(isset($core->input['delete_tracker'])) {
	Asset::delete_tracker($core->input['delete_tracker']);
}

$resolve = array('asid' => array('table' => 'assets', 'id' => 'asid', 'name' => 'title'));
$query = 'SELECT * FROM '.Tprefix.'assets_trackingdevices';
$query = $db->query($query);
$assetslist = '
<script>
$(document).ready(function() {
	$("form[name=tracker_delete]").submit(function(){
		if (confirm("Delete tracker?")) {
			return true;
		} else {
			return false;
		}
	});
	$("#clear_asset_edit_form").click(function() {
		$("#atdid").val("");
	});
	$("[id^=edit_entry_]").click(function() {
		eval("javascript: expand_fieldset_"+$(".collapsible_fieldset").attr("id").substring(3,$(".collapsible_fieldset").attr("id").length)+"();");
		$(this).parent().parent().children("td").each(function() {
			switch($(this).attr("name")) {
				case "atdid":
					$("#atdid").val($(this).attr("value"));
					break;
				case "deviceId":
					$("#deviceId").val($(this).attr("value"));
					break;
				case "asid":
					$("#asid").val($(this).attr("value"));
					break;
				case "fromDate":
					$("#pickDateFrom").val($(this).attr("value"));
					break;
				case "toDate":
					$("#pickDateTo").val($(this).attr("value"));
					break;
			}
		});
	});
});
</script>

	<div id="trackerslisting">
	<table cellspacing=0 cellpadding=4 border=1  width="100%"><tr bgcolor="#91B64F">
	<th>'.$lang->atdid.'</th>
	<th>'.$lang->deviceid.'</th>
	<th>'.$lang->asid.'</th>
	<th>'.$lang->from.'</th>
	<th>'.$lang->to.'</th>';
$assetslist.='<th>'.$lang->edit.'</td>';
if($core->usergroup['assets_canDeleteTracker'] == 1) {
	$assetslist.='<th>'.$lang->delete.'</th>';
}
	$assetslist.='</tr>';


if($db->num_rows($query) > 0) {
	while($row = $db->fetch_assoc($query)) {
		$assetslist.='<tr><td align="center" name="atdid" value="'.$row['atdid'].'">'.$row['atdid'].'</td>';
		$assetslist.='<td name="deviceId" value="'.$row['deviceId'].'">'.$row['deviceId'].'</td>';
		$assetslist.='<td name="asid" value="'.$row['asid'].'">'.get_name_from_id($row["asid"], $resolve['asid']['table'], $resolve['asid']['id'], $resolve['asid']['name']).'</td>';
		$assetslist.='<td name="fromDate" value="'.date('F d, Y', $row["fromDate"]).'">'.date('F d, Y', $row["fromDate"]).'</td>';
		$assetslist.='<td name="toDate" value="'.date('F d, Y', $row["toDate"]).'">'.date('F d, Y', $row["toDate"]).'</td>';
		$assetslist.='<td align="center" name="edit" value="">
						<button id="edit_entry_'.$row['atdid'].'" type="submit" style="cursor:pointer;border: 0; background: transparent" name="edit_tracker" value="'.$row['atdid'].'" alt="Edit"><img src="'.DOMAIN.'/images/edit.gif"/></button></td>';
		if($core->usergroup['assets_canDeleteTracker'] == 1) {
			$assetslist.='<form name="tracker_delete" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/managetrackers">
					<td align="center" name="delete" value="">
					<button type="submit" id="button_delete_'.$row['atdid'].'" style="cursor:pointer;border: 0; background: transparent" name="delete_tracker" value="'.$row['atdid'].'" alt="Edit"><img src="'.DOMAIN.'/images/invalid.gif"/></button></td></form>';
		}
		$assetslist.='<tr>';
	}
	$assetslist.='</table></div>';
}
else {
	$assetslist = '<div id="assetslist"></div>';
}

$assetedit = '<form name="asset_save" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/managetrackers">
	<input type="hidden" id="atdid" name="atdid" />
	<table cellspacing=5 cellpadding=4 border=0>
	<tr><td>'.$lang->deviceid.'</td><td><input id="deviceId" type="text" name="deviceId" tabindex="1"/></td></tr>
	<tr><td>'.$lang->asid.'</td><td>'.parse_selectlist('asid', 2, getAssetsList(), '').'</td></tr>
	<tr><td>'.$lang->from.'</td><td><input type="text" name="fromDate" id="pickDateFrom" tabindex="3"/></td></tr>
	<tr><td>'.$lang->to.'</td><td><input type="text" name="toDate" id="pickDateTo" tabindex="4"/></td></tr>
	<td colspan=2><input type="submit" name="savetracker" value="Save" tabindex="5"/>&nbsp;
	<button type="reset" id="clear_asset_edit_form">Reset</button></td></tr></table></form>';

$pagetitle = $lang->trackersmanagepage;
$pagecontents = encapsulate_in_fieldset($assetedit,"Add/Edit",true).'<hr><br>'.$assetslist;
eval("\$assetslist = \"".$template->get('assets_assets')."\";");
echo $assetslist;

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
