<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Add entities
 *  $module: Sourcing
 * $id: addentities.php	
 * Created By: 		@tony.assaad		October 8, 2012 | 12:30 PM
 * Last Update: 	@tony.assaad		October 10, 2012 | 4:13 PM
 */
if(!defined('DIRECT_ACCESS'))
{
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['sourcing_canManageEntries'] == 0 ) {
	error($lang->sectionnopermission);
	exit;
}

if(!$core->input['action']) {
	//<input name='.supplier['isBlacklisted'].'type=checkbox value='.$supplier['isBlacklisted'].'>
	if($core->input['type'] == 'edit' && isset($core->input['id'])) {
		$actiontype = 'Edit';
		$id = $db->escape_string($core->input['id']);
		$potential_supplier = new Sourcing($id);
		$supplier = $potential_supplier->get_supplier();
		$checkboxes_index = array('isBlacklisted');
		foreach($checkboxes_index as $key) {
			if($supplier[$key] == 1) {
				$checkedboxes = ' checked="checked"';
			} 
		}
		$mark_blacklist = $lang->blacklisted.'<input name="supplier[isBlacklisted]" type="checkbox" value="1"'.$checkedboxes.'>'; 		
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
	$maturity_level = get_specificdata('entities_rmlevels', array('ermlid', 'title'), 'ermlid', 'title','');
	$relation_Maturity_level = 	parse_selectlist('supplier[relationMaturity]', 8, $maturity_level, $supplier['relationMaturity']);
	
	eval("\$sourcingmanagesupplier = \"".$template->get('sourcing_managesupplier')."\";");
	output_page($sourcingmanagesupplier);
}

elseif($core->input['action'] == 'do_Editpage') {
	$potential_supplier = new Sourcing($id);
	$potential_supplier->edit($core->input['supplier']);
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