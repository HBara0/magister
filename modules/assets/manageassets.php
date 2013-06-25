<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: manageassets.php
 * Created:        @tony.assaad    Jun 24, 2013 | 11:09:38 AM
 * Last Update:    @tony.assaad    Jun 24, 2013 | 11:09:38 AM
 * 
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['assets_canManageAssets'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

	$data['description'] = $core->input['Description'];

if(!$core->input['action']) {
	$affiliate = new Affiliates($core->user['affiliates']);
if(isset($core->input['delete_asset'])) {
	Asset::delete_asset($core->input['delete_asset']);
}


$resolve = array('affid' => array('table' => 'affiliates', 'id' => 'affid', 'name' => 'name'));
		}
	});
	$("#clear_asset_edit_form").click(function() {
		$("#asid").val("");
	});
	$("[id^=edit_entry_]").click(function() {
		eval("javascript: expand_fieldset_"+$(".collapsible_fieldset").attr("id").substring(3,$(".collapsible_fieldset").attr("id").length)+"();");
		$(this).parent().parent().children("td").each(function() {
				switch($(this).attr("name")) {
					case "asid":
						$("#asid").val($(this).attr("value"));
						break;
					case "affid":
						$("#affid").val($(this).attr("value"));
						break;
					case "title":
						$("#title").val($(this).attr("value"));
						break;
					case "type":
						$("#type").val($(this).attr("value"));
						break;
					case "status":
						$("#status").val($(this).attr("value"));
						break;
					case "description":
						$("#description").val($(this).attr("value"));
						break;
				}
		});
	});
});
</script>
	<div id="assetslisting">
	<table cellspacing=0 cellpadding=4 border=1  width="100%"><tr bgcolor="#91B64F">
	<th>'.$lang->asid.'</th>
	<th>'.$lang->afid.'</th>
	<th>'.$lang->title.'</th>
	<th>'.$lang->type.'</th>
	<th>'.$lang->status.'</th>
	<th>'.$lang->description.'</th>
	<th>'.$lang->edit.'</th>';
if($core->usergroup['assets_canDeleteAsset'] == 1) {
	$assetslist .='<th>'.$lang->delete.'</th>';
}
$assetslist .='<th>'.$lang->open.'</th></tr>';

	if($core->input['type'] == 'edit' && isset($core->input['id'])) {
		$asid = $db->escape_string($core->input['id']);
		$asset = new Asset($asid);
		$assets = $asset->get_assets();
		$actiontype = 'Edit';
		$assetslist.='<td align="center" name="edit" value=""><button  type="submit" style="cursor:pointer;border: 0; background: transparent" name="edit_asset" value="'.$row['asid'].'" alt="Edit" id="edit_entry_'.$row['asid'].'"><img src="'.DOMAIN.'/images/edit.gif"/></button></td>';
		if($core->usergroup['assets_canDeleteAsset'] == 1) {
			$assetslist.='<form name="asset_delete" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/manageassets">
					  <td align="center" name="delete" value="">
					  <button type="submit" id="button_delete_'.$row['asid'].'" style="cursor:pointer;border: 0; background: transparent" name="delete_asset" value="'.$row['asid'].'" alt="Edit"><img src="'.DOMAIN.'/images/invalid.gif"/></button>
					  </td></form>';
		}
		$assetslist.='<td name="open" value=""><a href="'.DOMAIN.'/index.php?module=assets/assets&action=list&asid='.$row['asid'].'">open</a></td></tr>';
	}
	else {
		$actiontype = 'Add';
	}
	//$affiliate_country = $affiliate->get_country()->get();

	$affiliatesquery = $db->query("SELECT affid,name FROM ".Tprefix."affiliates WHERE affid IN('".implode(',', $core->user['affiliates'])."')");

	while($affiliates_user = $db->fetch_assoc($affiliatesquery)) {
		$affiliate_list.='<option value="'.$affiliates_user['affid'].'">'.$affiliates_user['name'].'</option>';
	}
	<button type="reset" id="clear_asset_edit_form">Reset</button></td></tr></table></form>';

$pagecontents = encapsulate_in_fieldset($assetedit,"Add/Edit",true).'<hr><br>'.$assetslist;
	eval("\$assetsmange = \"".$template->get('assets_manage')."\";");
	output_page($assetsmange);
}
else {
	$asset = new Asset();
	if($core->input['action'] == 'do_Add' || $core->input['action'] == 'do_Edit') {
		if($core->input['action'] == 'do_Edit') {
			$options['operationtype'] = 'update';
			$lang->successfullysaved = 'Successfully Update';
		}
		else {
			$options = array();
		}
		$asset->add($core->input['asset'], $options);
		switch($asset->get_errorcode()) {
			case 0:
				output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
				break;
			case 1:
				output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
				break;
			case 2:
				output_xml("<status>false</status><message>{$lang->entryexsist}</message>");
				break;
?>



