<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Add entities
 * $module: contents
 * $id: addentities.php	
 * Last Update: @zaher.reda 	February 15, 2012 | 10:05 AM
 */
if(!defined('DIRECT_ACCESS'))
{
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['sourcing_canManageEntries'] == 0 ) {
	//error($lang->sectionnopermission);
	//exit;
}

if(!$core->input['action']) {
	
	if($core->input['type'] == 'edit') {
		$actiontype = 'Edit';
		$id = $db->escape_string($core->input['id']);
		$potential_supplier = new Sourcing($id);
		$supplier = $potential_supplier->get();
	}
	else
	{
		$actiontype = 'Add';
		$countries_list = '';
	}
	$countries = get_specificdata('countries', array('coid', 'name'), 'coid', 'name','');
	$countries_list = parse_selectlist('supplier[country]', 8, $countries, '');
	$products = get_specificdata('productsegments', array('psid', 'title'), 'psid', 'title','');
	$product_list = parse_selectlist('supplier[productsegment]', 8, $products, $supplier['productsegment']);	
		
	eval("\$sourcingmanagesupplier = \"".$template->get('sourcing_managesupplier')."\";");
	output_page($sourcingmanagesupplier);
}
else 
{ 
	if($core->input['action'] == 'do_Addpage') {
		$potential_supplier = new Sourcing();
		$potential_supplier->add($core->input['supplier']);
		
		switch($potential_supplier->get_status()) {
			case 0:
				output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
			break;
			case 1:
				output_xml("<status>false</status><message>{$lang->fieldrequired}</message>");
			break;
			case 2:
				output_xml("<status>false</status><message>{$lang->companyexsist}</message>");
			break;
			}
		
	}
	/* if we attempt to create new representative from the popup */
	elseif($core->input['action'] == 'do_add_representative') {
		$representative = new Entities($core->input, 'add_representative');
		
		if($representative->get_status() === true) {
			output_xml("<status>true</status><message>{$lang->representativecreated}</message>");
		}
		else
		{
			output_xml("<status>false</status><message>{$lang->errorcreatingreprentative}</message>");
		}	
	}
	elseif($core->input['action'] == 'get_addnew_representative') {
		eval("\$addrepresentativebox = \"".$template->get('popup_addrepresentative')."\";");
		output_page($addrepresentativebox);
	}
elseif($core->input['action'] == 'checkcompany') {
	$company = $db->escape_string($core->input['company']);
	if(value_exists('sourcing_suppliers', 'companyName',  $company)) {
		echo 'companyexist';
	
		}
	}

}
?>