<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Add products
 * $module: contents
 * $id: addproducts.php	
 * Last Update: @zaher.reda 	Mar 21, 2009 | 11:03 AM
 */
if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canAddProducts'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

$lang->load('contents_addproducts');
if(!$core->input['action']) {
	$generic_attributes = array("gpid", "title");

	$generic_order = array(
			'by' => 'title',
			'sort' => 'ASC'
	);

	$generics = get_specificdata('genericproducts', $generic_attributes, 'gpid', 'title', $generic_order);
	$generics_list = parse_selectlist('gpid', 3, $generics, '', '', '', array('required' => 'required', 'blankstart' => true));

	eval("\$addproductspage = \"".$template->get('contents_products_add')."\";");
	output_page($addproductspage);
}
else {
	if($core->input['action'] == 'do_perform_addproducts') {
		if(empty($core->input['spid']) || empty($core->input['gpid']) || empty($core->input['name'])) {
			output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
			exit;
		}

		if(value_exists('products', 'name', $core->input['name'])) {
			output_xml("<status>false</status><message>{$lang->productalreadyexists}</message>");
			exit;
		}
		unset($core->input['action'], $core->input['module']);
		//Temporary hardcode
		$core->input['defaultCurrency'] = 'USD';

		$query = $db->insert_query('products', $core->input);

		if($query) {
			$entity = new Entities($core->input['spid']);
			$entity->auto_assignsegment($core->input['gpid']);
			$log->record($core->input['name']);

			$lang->productadded = $lang->sprint($lang->productadded, $core->input['name']);
			output_xml("<status>true</status><message>{$lang->productadded}</message>");
		}
		else {
			output_xml("<status>false</status><message>{$lang->erroraddingproduct}</message>");
		}
	}
}
?>