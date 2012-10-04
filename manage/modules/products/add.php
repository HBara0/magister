<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Add products
 * $module: admin/products
 * $id: add.php	
 * Last Update: @zaher.reda 	Apr 23, 2009 | 01:38 PM
 */
if(!defined("DIRECT_ACCESS"))
{
	die("Direct initialization of this file is not allowed.");
}
if($core->usergroup['canAddProducts'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!$core->input['action']) {		
	$generic_attributes = array("gpid", "title");
	
	$generic_order = array(
		"by" => "title", 
		"sort" => "ASC"
	);
	
	$generics = get_specificdata("genericproducts", $generic_attributes, "gpid", "title", $generic_order, 1);
	$generics_list = parse_selectlist("gpid", 3, $generics, "");
	
	$actiontype = "add";
	$pagetitle = $lang->addaproduct;
	
	eval("\$addproductspage = \"".$template->get("admin_products_addedit")."\";");
	output_page($addproductspage);
}
else
{
	if($core->input['action'] == "do_perform_add") {
		if(empty($core->input['spid']) || empty($core->input['gpid']) || empty($core->input['name'])) {
			output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
			exit;
		}
		
		if(value_exists("products", "name", $core->input['name'])) {
			output_xml("<status>false</status><message>{$lang->productalreadyexists}</message>"); 
			exit;
		}
		
		log_action($core->input['name']);
		unset($core->input['action'], $core->input['module']);
		
		//Temporary hardcode
		$core->input['defaultCurrency'] = "USD";
		
		$query = $db->insert_query("products", $core->input);
		if($query) {
			$lang->productadded = $lang->sprint($lang->productadded, htmlspecialchars($core->input['name']));
			output_xml("<status>true</status><message>{$lang->productadded}</message>");
		}
		else
		{
			output_xml("<status>false</status><message>{$lang->erroraddingproduct}</message>");
		}
	}
}
?>