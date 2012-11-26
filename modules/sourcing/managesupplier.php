<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Manage Supplier
 *  $module: Sourcing
 * $id: Managesupplier.php	
 * Created By: 		@tony.assaad		October 8, 2012 | 12:30 PM
 * Last Update: 	@tony.assaad		October 10, 2012 | 4:13 PM
 */
if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['sourcing_canManageEntries'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!$core->input['action']) {
	if($core->input['type'] == 'edit' && isset($core->input['id'])) {
		$actiontype = 'Edit';

	if(!empty($core->input['$id'])) { /* supplier id afer sumbit contact supplier form */
		$id = $core->input['$id'];
}
else {
	$supplier_id = $db->escape_string($core->input[$supplier['ssid']]);
}

		$potential_supplier = new Sourcing($id);
		$supplier = $potential_supplier->get_supplier();
		$checkboxes_index = array('isBlacklisted');
		foreach($checkboxes_index as $key) {
			if($supplier[$key] == 1) {
				$checkedboxes = ' checked="checked"';
			}
		}
		$mark_blacklist = '<div style="display: table-cell; width:700px;vertical-align:middle;">'.$lang->blacklisted.'</div><div style="display: table-cell; width:700px;vertical-align:middle;"><input name="supplier[isBlacklisted]" type="checkbox" value="1"'.$checkedboxes.'></div>';
	}
	else {
		$actiontype = 'Add';
		$countries_list = '';
	}
	$countries = get_specificdata('countries', array('coid', 'name'), 'coid', 'name', '');
	$countries_list = parse_selectlist('supplier[country]', 8, $countries, '');
	$products = get_specificdata('productsegments', array('psid', 'title'), 'psid', 'title', '');
	$product_list = parse_selectlist('supplier[productsegment][]', 9, $products, $supplier['productsegment'], 1);
	$maturity_level = get_specificdata('entities_rmlevels', array('ermlid', 'title'), 'ermlid', 'title', '');
	$relation_Maturity_level = parse_selectlist('supplier[relationMaturity]', 10, $maturity_level, $supplier['relationMaturity']);
	$activityareas = get_specificdata('countries', array('coid', 'name'), 'coid', 'name', '', '', 'affid in (SELECT affid from affiliates)');
	$activityarea_list = parse_selectlist('supplier[activityarea][]', 8, $activityareas, $supplier['activityarea'], 1);
	$supplierid = $core->input['id'];
	eval("\$sourcingmanagesupplier = \"".$template->get('sourcing_managesupplier')."\";");
	output_page($sourcingmanagesupplier);
}

/* elseif($core->input['action'] == 'do_Editpage') {
  $potential_supplier = new Sourcing($id);
  $potential_supplier->edit($core->input['supplier']);
  } */
else {
	if($core->input['action'] == 'do_Addpage' || $core->input['action'] == 'do_Editpage') {
		if($core->input['action'] == 'do_Editpage') {
			$options['operationtype'] = 'update';
			$lang->successfullysaved = 'Successfully Update';
		}
		else {
			$options = array();
		}
		$potential_supplier = new Sourcing();
		$potential_supplier->add($core->input['supplier'], $options);

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
		else {
			output_xml("<status>false</status><message>{$lang->errorcreatingreprentative}</message>");
		}
	}
	elseif($core->input['action'] == 'get_addnew_representative') {
		eval("\$addrepresentativebox = \"".$template->get('popup_addrepresentative')."\";");
		output_page($addrepresentativebox);
	}
	elseif($core->input['action'] == 'checkcompany') {
		$company = $db->escape_string($core->input['company']);
		$companies_exists_query = $db->query("SELECT companyName FROM ".Tprefix."  sourcing_suppliers 
												 WHERE  companyName like '%".$company."%'");

		if($db->num_rows($companies_exists_query) > 0) {
			while($companies = $db->fetch_assoc($companies_exists_query)) {
				$companies_exists .= implode(' ', $companies);
			}
			echo $companies_exists.' ';
		}
		else {
			echo '';
		}
	}
}
?>