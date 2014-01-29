<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
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
	
	$chemicalp_rowid = ($core->input['val'] + 1); echo $chemicalp_rowid;
	$chemrows = '<tr id='.$chemicalp_rowid.'> <td colspan="2">'.$lang->chemicalsubstances.'</td><td> <input type="text" value="'.$product['chemicalsubstances'][$key]['name'].'" id=chemicalproducts_'.$chemicalp_rowid.'_QSearch autocomplete="off" size="40px"/> 
				  <input type="hidden" id="chemicalproducts_'.$chemicalp_rowid.'_id" name="chemsubstances['.$chemicalp_rowid.'][csid]"  value="'.$product['chemicalsubstances'][$key]['csid'].'"/>
					   <div id="searchQuickResults_chemicalproducts_'.$chemicalp_rowid.'" class="searchQuickResults" style="display:none;"></div> </td>
				   <td><a class="showpopup" id="showpopup_createchemical"><img src="../images/addnew.png" border="0" alt="'.$lang->add.'"/></a> </td>
			</tr>';
//	$chemsubstances_objs = Chemicalsubstances::get_chemicalsubstances();
//	$chemicalslist_section = '';
//	if(is_array($chemsubstances_objs)) {
//		foreach($chemsubstances_objs as $chemsubstances_obj) {
//			$rowclass = alt_row($rowclass);
//			$chemical = $chemsubstances_obj->get();
//			//$chemicalslist_section .= '<tr class="'.$rowclass.'" style="vertical-align:top;"><td width="1%"><input type="checkbox" value="'.$chemical['csid'].'" name="chemsubstances[][csid]"/></td><td width="33%">'.$chemical['casNum'].'</td><td align="left" width="33%">'.$chemical['name'].'</td><td width="33%">'.$chemical['synonyms'].'</td></tr>';
//		}
//	}
//	else {
//		$chemicalslist_section = '<tr><td colspan="2">'.$lang->na.'</td></tr>';
//	}

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
			exit;
		}
		unset($core->input['action'], $core->input['module']);
		//Temporary hardcode
		$core->input['defaultCurrency'] = "USD";
		
		$query = $db->insert_query("products", $core->input);
		if($query) {
			$entity = new Entities($core->input['spid']);
			$entity->auto_assignsegment($core->input['gpid']);
			$log->record($core->input['name']);
		
			$lang->productadded = $lang->sprint($lang->productadded, htmlspecialchars($core->input['name']));
			output_xml("<status>true</status><message>{$lang->productadded}</message>");
		}
		else
		{
			output_xml("<status>false</status><message>{$lang->erroraddingproduct}</message>");
		}
	}
	elseif($core->input['action'] == 'do_createchemical') {
		$chemical_obj = new Chemicalsubstances();
		$chemical_obj->create($core->input['chemcialsubstances']);
		switch($chemical_obj->get_status()) {
			case 0:
				output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
				break;
			case 1:
				output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
				break;
			case 2:
				output_xml("<status>false</status><message>{$lang->chemicalexsist}</message>");
				break;
		}
	}
	elseif($core->input['action'] == 'get_addnew_chemical') {
		eval("\$createchemical= \"".$template->get('popup_admin_product_createchemical')."\";");
		output_page($createchemical);
	}
}
?>