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
if(!defined("DIRECT_ACCESS")) {
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

	/* Parse all  segapplicationfunctions and get the associatives functions and segment  */
	$segappfunc_objs = Segapplicationfunctions::get_segmentsapplicationsfunctions();
	foreach($segappfunc_objs as $segappfunc_obj) {
		$rowclass = alt_row($rowclass);
		/* call the associatives objects */
		$segmentapp_data['segappfuncs'] = $segappfunc_obj->get();
		$segmentapp_data['chemicalfunction'] = $segappfunc_obj->get_function()->get();
		$segmentapp_data['segment'] = $segappfunc_obj->get_segment()->get()['title'];
		$segmentapp_data['application'] = $segappfunc_obj->get_application()->get()['title'];

		eval("\$admin_products_addedit_segmentsapplicationsfunctions_rows .= \"".$template->get("admin_products_addedit_segmentsapplicationsfunctions_rows")."\";");
	}

	$chemicalp_rowid = ($core->input['val'] + 1);
	$chemrows = '<tr id='.$chemicalp_rowid.'> <td colspan="2">'.$lang->chemicalsubstances.'</td><td> <input type="text" value="'.$product['chemicalsubstances'][$key]['name'].'" id=chemicalproducts_'.$chemicalp_rowid.'_QSearch autocomplete="off" size="40px"/> 
				  <input type="hidden" id="chemicalproducts_'.$chemicalp_rowid.'_id" name="chemsubstances['.$chemicalp_rowid.'][csid]"  value="'.$product['chemicalsubstances'][$key]['csid'].'"/>
					   <div id="searchQuickResults_chemicalproducts_'.$chemicalp_rowid.'" class="searchQuickResults" style="display:none;"></div> </td>
				   <td><a class="showpopup" id="showpopup_createchemical"><img src="../images/addnew.png" border="0" alt="'.$lang->add.'"/></a> </td>
			</tr>';
	/* Chemical List - START */
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

	/* Chemical List - END */
	eval("\$chemicalsubstances = \"".$template->get("admin_products_chemicalsubstances")."\";");
	eval("\$addproductspage = \"".$template->get("admin_products_addedit")."\";");
	output_page($addproductspage);
}
else {
	if($core->input['action'] == "do_perform_add") {

		if(empty($core->input['spid']) || empty($core->input['gpid']) || empty($core->input['name'])) {
			output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
			exit;
		}

		if(value_exists("products", "name", $core->input['name'])) {
			output_xml("<status>false</status><message>{$lang->productalreadyexists}</message>");
			exit;
		}
		if(empty($core->input['applicationfunction']) && !isset($core->input['applicationfunction'])) {
			output_xml("<status>false</status><message>{$lang->funcapplrequired}</message>");
			exit;
		}
		$chemicalfunctionsproducts = $core->input['applicationfunction'];
		$productschemsubstances = $core->input['chemsubstances'];
		unset($core->input['action'], $core->input['module'], $core->input['applicationfunction'], $core->input['chemsubstances']);
		//Temporary hardcode
		$core->input['defaultCurrency'] = "USD";
		$query = $db->insert_query("products", $core->input);
		if($query) {
			$pid = $db->last_id();
			$entity = new Entities($core->input['spid']);
			$entity->auto_assignsegment($core->input['gpid']);

			/* insert chemical functions produts */
			if(isset($productschemsubstances)) {
				foreach($productschemsubstances as $productschemsubstance) {
					foreach($productschemsubstance as $csid) {
						$chemsubstances_arary = array('pid' => $pid,
								'csid' => $csid,
								'createdBy' => $core->user['uid'],
								'createdOn' => TIME_NOW
						);
						$db->insert_query("productschemsubstances", $chemsubstances_arary);
					}
				}
			}
			/* insert products chemical substances */
			if(isset($chemicalfunctionsproducts)) {
				foreach($chemicalfunctionsproducts as $chemicalfunctions) {
					foreach($chemicalfunctions as $safid) {
						$chemfunctionproducts_arary = array('pid' => $pid,
								'safid' => $safid,
								'createdBy' => $core->user['uid'],
								'createdOn' => TIME_NOW
						);
						$chemfunctionquery = $db->insert_query("chemfunctionproducts", $chemfunctionproducts_arary);
						/* In case a default function is not selected, system would automatically pick the first checked checkbox */
						if(empty($core->input['defaultFunction']) && !isset($core->input['defaultFunction'])) {
							$cfpid = array_shift(array_values($chemicalfunctions)); /* shift the array and get the first element value */
						}
						else if($chemfunctionquery && isset($core->input['defaultFunction'])) {
							$cfpid = $db->last_id();
						}
						$db->update_query('products', array('defaultFunction' => $cfpid), 'pid='.$pid);
					}
				}
			}
			$log->record($core->input['name']);

			$lang->productadded = $lang->sprint($lang->productadded, htmlspecialchars($core->input['name']));
			output_xml("<status>true</status><message>{$lang->productadded}</message>");
		}
		else {
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