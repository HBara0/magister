<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Edit products
 * $module: admin/products
 * $id: edit.php	
 * Last Update: @zaher.reda 	Feb 24, 2009 | 04:05 AM
 */
if(!defined('DIRECT_ACCESS'))
{
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canManageProducts'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!$core->input['action']) {
	if(!isset($core->input['pid']) || empty($core->input['pid'])) {
		redirect('index.php?module=products/view');
	}
	
	$pid = $db->escape_string($core->input['pid']);
	$product = $db->fetch_array($db->query("SELECT p.*, s.companyName AS suppliername 
											FROM ".Tprefix."products p LEFT JOIN ".Tprefix."entities s ON (p.spid=s.eid)
											WHERE p.pid='{$pid}'"));
	
	$generic_attributes = array('gpid', 'title');
	$generic_order = array(
		'by' => 'title', 
		'sort' => 'ASC'
	);
	
	$generics = get_specificdata('genericproducts', $generic_attributes, 'gpid', 'title', $generic_order);
	$generics_list = parse_selectlist("gpid", 3, $generics, $product['gpid']);
			
	$actiontype = "edit";
	$pagetitle = $lang->sprint($lang->editproductwithname, $product['name']);
	
	$pidfield = "<input type='hidden' value='{$pid}' name='pid'>";
	eval("\$editpage = \"".$template->get("admin_products_addedit")."\";");
	output_page($editpage);
}
else 
{
	if($core->input['action'] == 'do_perform_edit') {	
		if(empty($core->input['spid']) || empty($core->input['gpid']) || empty($core->input['name'])) {
			output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
			exit;
		}
		
		$check_query = $db->query("SELECT pid, name FROM ".Tprefix."products WHERE name='{$core->input[name]}' LIMIT 0,1");
		if($db->num_rows($check_query) > 0) {
			$existing = $db->fetch_array($check_query);
			
			if($existing['pid'] != $core->input['pid']) {
				output_xml("<status>false</status><message>{$lang->productalreadyexists}</message>"); 
				exit;
			}
		}
		
		log_action($core->input['name']);
		unset($core->input['action'], $core->input['module']);

		$query = $db->update_query('products', $core->input, "pid='".$db->escape_string($core->input['pid'])."'");
		if($query) {
			$lang->productedited = $lang->sprint($lang->productedited, $core->input['name']);
			output_xml("<status>true</status><message>{$lang->productedited}</message>");
		}
		else
		{
			output_xml("<status>false</status><message>{$lang->erroreditingproduct}</message>");
		}
	}
	elseif($core->input['action'] == 'perform_mergeanddelete') {
		$oldid = $db->escape_string($core->input['todelete']);
		$products_tables = array('productsactivity' => 'pid', ' integration_mediation_products' => 'localId');
		if(!empty($core->input['mergepid'])) {
			$newid = $db->escape_string($core->input['mergepid']);
			foreach($products_tables as $table => $attr) {
				$db->update_query($table, array($attr => $newid), $attr.'='.$oldid);
			}
		}
		
		$query = $db->delete_query('products', "pid='{$oldid}'");
		if($query) {
			log_action($oldid, $newid);
			output_xml("<status>true</status><message>{$lang->successdeletemerge}</message>");
		}
		else
		{
			output_xml("<status>false</status><message>{$lang->errordeleting}</message>");
		}
	}
	elseif($core->input['action'] == 'get_mergeanddelete') {
		eval("\$mergeanddeletebox = \"".$template->get("popup_mergeanddelete")."\";");
		echo $mergeanddeletebox; 
	}
}
?>