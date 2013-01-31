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
$assetslist = '
<script>
$(document).ready(function() {
	$("form[name=assignedasset_delete]").submit(function(){
		if (confirm("Delete asset assignment?")) {
			return true;
		} else {
			return false;
		}
	});
	$("#clear_asset_edit_form").click(function() {
		$("#auid").val("");
	});
	$("[id^=edit_entry_]").click(function() {
		eval("javascript: expand_fieldset_"+$(".collapsible_fieldset").attr("id").substring(3,$(".collapsible_fieldset").attr("id").length)+"();");
		$(this).parent().parent().children("td").each(function() {
				switch($(this).attr("name")) {
					case "uid":
						$("#uid").val($(this).attr("value"));
						break;
					case "asid":
						$("#asid").val($(this).attr("value"));
						break;
					case "fromdate":
						$("#pickDateFrom").val($(this).attr("value"));
						break;
					case "todate":
						$("#pickDateTo").val($(this).attr("value"));
						break;
					case "auid":
						$("#auid").val($(this).attr("value"));
						break;
					}
		});
	});
});
</script>
	<div id="assetslisting">
	<table cellspacing=0 cellpadding=4 border=1 width="100%"><tr bgcolor="#91B64F">
	<th>'.$lang->auid.'</th>
	<th>'.$lang->uid.'</th>
	<th>'.$lang->asid.'</th>
	<th>'.$lang->from.'</th>
	<th>'.$lang->to.'</th>
	<th>'.$lang->edit.'</td>';
if($core->usergroup['assets_canDeleteAssignement'] == 1) {
	$assetslist.='<th>'.$lang->delete.'</th>';
}
$assetslist.='</tr>';

if($db->num_rows($query) > 0) {
	while($row = $db->fetch_assoc($query)) {
		$assetslist.='<tr><td align="center" name="auid" value="'.$row['auid'].'">'.$row['auid'].'</td>';
		$assetslist.='<td name="uid" value="'.$row['uid'].'">'.get_name_from_id($row['uid'], $resolve['uid']['table'], $resolve['uid']['id'], $resolve['uid']['name']).'</td>';
		$assetslist.='<td name="asid" value="'.$row["asid"].'">'.get_name_from_id($row["asid"], $resolve['asid']['table'], $resolve['asid']['id'], $resolve['asid']['name']).'</td>';
		$assetslist.='<td name="fromdate" value="'.date('F d, Y', $row["fromDate"]).'">'.date('F d, Y', $row["fromDate"]).'</td>';
		$assetslist.='<td name="todate" value="'.date('F d, Y', $row["toDate"]).'">'.date('F d, Y', $row["toDate"]).'</td>';
		$assetslist.='<td align="center" name="edit" value=""><button id="edit_entry_'.$row['auid'].'" type="submit" style="cursor:pointer;border: 0; background: transparent" name="edit_asset_assign" value="'.$row['auid'].'" alt="Edit"><img src="'.DOMAIN.'/images/edit.gif"/></button></td>';
		if($core->usergroup['assets_canDeleteAssignement'] == 1) {
			$assetslist.='<form name="assignedasset_delete" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/assignassets">
					<td align="center" name="delete" value="">
					<button type="submit" id="button_delete_'.$row['auid'].'" style="cursor:pointer;border: 0; background: transparent" name="delete_assignedasset" value="'.$row['auid'].'" alt="Edit"><img src="'.DOMAIN.'/images/invalid.gif"/></button>
					</td></form>';
		}
		$assetslist.='</tr>';
	}
	$assetslist.='</table></div>';
}
else {
	$assetslist = '<div id="assetslist"></div>';
}

/*
if(isset($core->input['edit_asset_assign'])) {
	$assetedit = '<form name="asset_assign" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/assignassets">
	<input type="hidden" id="auid" name="auid" value="'.$core->input['edit_asset_assign'].'"/>
	<table cellspacing=5 cellpadding=4 border=0>
	<tr><td>'.$lang->uid.'</td><td>'.parse_selectlist('uid', 1, getAllUsers(), $core->input['e_uid']).'</td></tr>
	<tr><td>'.$lang->asid.'</td><td>'.parse_selectlist('asid', 2, Asset::getAllAssets(), $core->input['e_asid']).'</td></tr>
	<tr><td>'.$lang->from.'</td><td><input type="text" name="datefrom" id="pickDateFrom" tabindex="3" value="'.date('F d, Y', $core->input['e_dateFrom']).'"/></td></tr>
	<tr><td>'.$lang->to.'</td><td><input type="text" name="dateto" id="pickDateTo" tabindex="4" value="'.date('F d, Y', $core->input['e_dateTo']).'"/></td></tr>
	<td><input type="submit" name="assignasset" value="Save" tabindex="5"/></tr></td></table></form>';
}*/

	$assetedit = '<form name="asset_save" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/assignassets">
	<input type="hidden" id="auid" name="auid" />
	<table cellspacing=5 cellpadding=4 border=0>
	<tr><td>'.$lang->uid.'</td><td>'.parse_selectlist('uid', 1, getAllUsers(), array()).'</td></tr>
	<tr><td>'.$lang->asid.'</td><td>'.parse_selectlist('asid', 2, Asset::getAllAssets(), array()).'</td></tr>
	<tr><td>'.$lang->from.'</td><td><input type="text" name="datefrom" id="pickDateFrom" tabindex="3" value=""/></td></tr>
	<tr><td>'.$lang->to.'</td><td><input type="text" name="dateto" id="pickDateTo" tabindex="4" value=""/></td></tr>
	<td colspan=2><input type="submit" name="assignasset" value="Save" tabindex="5"/>&nbsp;
	<button type="reset" id="clear_asset_edit_form">Reset</button></td></tr></td></table></form>';


$pagetitle = $lang->assignassetspage;
$pagecontents = encapsulate_in_fieldset($assetedit,"Add/Edit",true).'<hr><br>'.$assetslist;
eval("\$assetslist = \"".$template->get('assets_assets')."\";");
echo $assetslist;
function getAllUsers() {
	global $db;
	$result = array();
	$affiliateslist = getAffiliateList();
	$affiliates = array();
	foreach($affiliateslist as $key => $value) {
		$affiliates[] = $key;
	}
	$query = 'SELECT DISTINCT(u.uid),displayName,affid FROM '.Tprefix.'users as u INNER JOIN affiliatedemployees as a ON (a.uid=u.uid) WHERE gid<>7 AND affid IN ('.implode(',', $affiliates).')';
	$query = $db->query($query);
	if($db->num_rows($query) > 0) {
		while($row = $db->fetch_assoc($query)) {
			$result[$row['uid']] = $row['displayName'];
		}
	}
	return $result;
}

?>
