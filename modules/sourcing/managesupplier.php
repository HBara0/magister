<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Manage Supplier
 *  $module: Sourcing
 * $id: Managesupplier.php	
 * Created By: 		@tony.assaad		October 8, 2012 | 12:30 PM
 * Last Update: 	@tony.assaad		November 30, 2012 | 11:13 PM
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
		$actiontype = 'edit';
		$id = $db->escape_string($core->input['id']);
		$potential_supplier = new Sourcing($id);
		$supplier['details'] = $potential_supplier->get_supplier();
		$supplier['segments'] = array_keys($potential_supplier->get_supplier_segments());
		$supplier['contactpersons'] = $potential_supplier->get_supplier_contact_persons();
		$supplier['activityareas'] = $potential_supplier->get_supplier_activity_area();
		$supplier['chemicalsubstances'] = $potential_supplier->get_chemicalsubstances();

		$checkboxes_index = array('isBlacklisted');
		foreach($checkboxes_index as $key) {
			if($supplier['details'][$key] == 1) {
				$checkedboxes = ' checked="checked"';
			}
		}

		$selectlists_index = array('type');
		foreach($selectlists_index as $key) {
			$selecteditems[$key][$supplier['details'][$key]] = ' select="selected"';
		}


		if(is_array($supplier['chemicalsubstances'])) {
			$chemicalp_rowid = 1;
			foreach($supplier['chemicalsubstances'] as $chemicalproduct) {
				eval("\$chemicalproducts_rows .= \"".$template->get('sourcing_managesupplier_chemicalrow')."\";");
				$chemicalp_rowid++;
			}
		}


		if(is_array($supplier['contactpersons'])) {
			$contactp_rowid = 1;
			foreach($supplier['contactpersons'] as $contactperson) {
				eval("\$contactpersons_rows .= \"".$template->get('sourcing_managesupplier_contactprow')."\";");
				$contactp_rowid++;
			}
		}

		$supplier['details']['phone1'] = explode('-', $supplier['details']['phone1']);
		$supplier['details']['phone2'] = explode('-', $supplier['details']['phone2']);

		$mark_blacklist = '<div style="display: table-cell; width:700px;vertical-align:middle;">'.$lang->blacklisted.'</div><div style="display: table-cell; width:700px;vertical-align:middle;"><input name="supplier[isBlacklisted]" type="checkbox" value="1"'.$checkedboxes.'></div>';
	}
	else {
		$actiontype = 'add';
		$chemicalp_rowid = 1;
		eval("\$chemicalproducts_rows .= \"".$template->get('sourcing_managesupplier_chemicalrow')."\";");
		$contactp_rowid = 1;
		eval("\$contactpersons_rows .= \"".$template->get('sourcing_managesupplier_contactprow')."\";");
	}

	$countries_list = parse_selectlist('supplier[country]', 8, get_specificdata('countries', array('coid', 'name'), 'coid', 'name', array('sort' => 'ASC', 'by' => 'name')), $supplier['details']['country']);
	$product_list = parse_selectlist('supplier[productsegment][]', 9, get_specificdata('productsegments', array('psid', 'title'), 'psid', 'title', ''), $supplier['segments'], 1);
	$rml_selectlist = parse_selectlist('supplier[relationMaturity]', 10, get_specificdata('entities_rmlevels', array('ermlid', 'title'), 'ermlid', 'title', ''), $supplier['details']['relationMaturity']);
	$affiliates = get_specificdata('countries', array('coid', 'name'), 'coid', 'name', '', '', 'affid IN (SELECT affid FROM affiliates)');
	$availability_radiobutton_items = array('1' => $lang->yes, '2' => $lang->no, '3' => $lang->undefined, '4' => $lang->sourcingdecide);
	if(is_array($supplier['activityareas'])) {
		foreach($supplier['activityareas'] as $key => $activityareasdata) {
			$supplier['slectedactivityareas'] [$key] = $activityareasdata;
		}
	}
	foreach($affiliates as $affid => $name) {
		$rowclass = alt_row($rowclass);
		foreach($availability_radiobutton_items as $attr => $item) {
			$availability_radiobutton[$item] = parse_radiobutton('supplier[activityarea]['.$affid.'][availability]', array($attr => $item), $supplier['slectedactivityareas'][$affid]['availability']); /* pass the selected avaialability value  for each affiltae */
		}
		eval("\$activityarea_list_row .= \"".$template->get('sourcing_managesupplier_activityarea_list_row')."\";");
	}

	$supplierid = $core->input['id'];
	eval("\$sourcingmanagesupplier = \"".$template->get('sourcing_managesupplier')."\";");
	output_page($sourcingmanagesupplier);
}
else {
	$potential_supplier = new Sourcing();
	if($core->input['action'] == 'do_addpage' || $core->input['action'] == 'do_editpage') {
		if($core->input['action'] == 'do_editpage') {
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
	
		/*Creat new chemical -START*/
	elseif($core->input['action'] == 'do_createchemical') {
		$potential_supplier->create_chemical($core->input['supplier']['chemcialsubstances']);
		switch($potential_supplier->get_status()) {
			case 0:
				output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
				break;
			case 4:
				output_xml("<status>false</status><message>{$lang->chemicalrequired}</message>");
				break;
			case 5:
				output_xml("<status>false</status><message>{$lang->chemicalexsist}</message>");
				break;
		}
	}
	/*Creat new chemical -END*/
	elseif($core->input['action'] == 'get_addnew_chemical') {
		eval("\$createchemical= \"".$template->get('popup_sourcing_createchemicalrequests')."\";");
		output_page($createchemical);
	}
/* if we attempt to create new representative from the popup */
	elseif($core->input['action'] == 'do_add_representative') {
		$core->input['repPhone'] = $core->input['countrycode'].'-'.$core->input['area'].'-'.$core->input['repPhone'];
		$representative = new Entities($core->input, 'add_representative');

		if($representative->get_status() === true) {
			output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
		}
		else {
			output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
		}
	}
	elseif($core->input['action'] == 'get_addnew_representative') {
		eval("\$addrepresentativebox = \"".$template->get('popup_addrepresentative')."\";");
		output_page($addrepresentativebox);
	}
	elseif($core->input['action'] == 'inlineCheck') {
		$companies_exists_query = $db->query("SELECT companyName FROM ".Tprefix."sourcing_suppliers WHERE companyName LIKE '%".$db->escape_string($core->input['value'])."%'");

		if($db->num_rows($companies_exists_query) > 0) {
			while($companies = $db->fetch_assoc($companies_exists_query)) {
				$companies_exists .= implode('<br />', $companies);
			}
			output_xml("<status>false</status><message>{$lang->companyexists}<br />{$companies_exists}</message>");
		}
	}
}
?>