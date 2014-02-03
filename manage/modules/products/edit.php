<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Edit products
 * $module: admin/products
 * $id: edit.php	
 * Last Update: @zaher.reda 	Feb 24, 2009 | 04:05 AM
 */
if(!defined('DIRECT_ACCESS')) {
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
	$product_obj = new Products($core->input['pid'], false);
	$product = $product_obj->get();
	$pid = $product['pid']; // Backward compatibility
	$product['supplier_obj'] = $product_obj->get_supplier();
	$product['suppliername'] = $product['supplier_obj']->get()['companyName'];

	$generic_attributes = array('gpid', 'title');
	$generic_order = array(
			'by' => 'title',
			'sort' => 'ASC'
	);

	$generics = get_specificdata('genericproducts', $generic_attributes, 'gpid', 'title', $generic_order);
	$generics_list = parse_selectlist("gpid", 3, $generics, $product['gpid']);

	$actiontype = 'edit';
	$pagetitle = $lang->sprint($lang->editproductwithname, $product['name']);
	//$checmicalfuncprod = $product_obj->get_defaultchemfunction();
	//$checmicalfuncprod_ids = $checmicalfuncprod->get();

	/* Parse all  segapplicationfunctions and get the associatives functions and segment  */
	$supplier['segments'] = $product['supplier_obj']->get_segments();
	$segment_applications = array();
	foreach($supplier['segments'] as $segment_obj) {
		$segmentapplications_obj = $segment_obj->get_applications();
		if(is_array($segmentapplications_obj)) {
			$segmentapplications = $segment_applications + array_keys($segmentapplications_obj);
		}
	}

	if(!empty($segmentapplications)) {
		$segappfunc_objs_where = 'psaid IN ('.implode(',', $segmentapplications).')';
	}
	$segappfunc_objs = Segapplicationfunctions::get_segmentsapplicationsfunctions($segappfunc_objs_where);
	if(is_array($segappfunc_objs)) {
		foreach($segappfunc_objs as $segappfunc_obj) {
			$rowclass = alt_row($rowclass);
			/* call the associatives objects */
			$segmentapp_data['segappfuncs'] = $segappfunc_obj->get();
			$cfpid = $db->fetch_field($db->query("SELECT cfpid 
											FROM ".Tprefix."chemfunctionproducts 
											WHERE safid=".$segmentapp_data['segappfuncs']['safid']." AND pid='{$pid}'"), 'cfpid');
			/* check the default */
			if($cfpid == $product['defaultFunction']) {
				$defaultfunctionchecked[$cfpid] = " checked='checked'";
			}
			$segmentapp_data['chemicalfunction'] = $segappfunc_obj->get_function()->get();
			$segmentapp_data['existingprodfunctionids'] = $segmentapp_data['chemicalfunction']['cfid'];

			$chemfunc_obj = new Chemicalfunctions($segmentapp_data['existingprodfunctionids']);
			//$chemicalfunc_id = $chemfunc_obj->get()['cfid'];
			if(value_exists('chemfunctionproducts', 'safid', $segmentapp_data['segappfuncs']['safid'], 'pid='.$pid)) {
				$defaultfunctionchecked[$segmentapp_data['segappfuncs']['safid']] = " checked='checked'";
			}
			$segmentapp_data['segment'] = $segappfunc_obj->get_segment()->get()['title'];
			$segmentapp_data['application'] = $segappfunc_obj->get_application()->get()['title'];

			eval("\$admin_products_addedit_segappfunc_rows .= \"".$template->get('\admin_products_addedit_segappfunc_row')."\";");
			$defaultfunctionchecked[$segmentapp_data['segappfuncs']['safid']] = '';
		}
	}
	else {
		$admin_products_addedit_segappfunc_rows = '<tr><td colspan=3>'.$lang->na.'</td></tr>';
	}
	$chemsubstance_objs = $product_obj->get_chemicalsubstance();
	if(is_array($chemsubstance_objs)) {
		foreach($chemsubstance_objs as $key => $chemsubstance_obj) {
			$chemicalp_rowid = $key;
			$product['chemicalsubstances'][$key] = $chemsubstance_obj->get();
			eval("\$chemrows .= \"".$template->get('admin_products_addedit_chemsubstances_row')."\";");
		}
	}
	else {
		$chemicalp_rowid = 1;
		eval("\$chemrows = \"".$template->get('admin_products_addedit_chemsubstances_row')."\";");
	}
	$pidfield = "<input type='hidden' value='{$pid}' name='pid'>";
	eval("\$editpage = \"".$template->get("admin_products_addedit")."\";");
	output_page($editpage);
}
else {
	if($core->input['action'] == 'do_perform_edit') {
		if(empty($core->input['spid']) || empty($core->input['gpid']) || empty($core->input['name'])) {
			output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
			exit;
		}
		if(empty($core->input['applicationfunction']) && !isset($core->input['applicationfunction'])) {
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

		$log->record($core->input['name']);
		$chemicalfunctionsproducts = $core->input['applicationfunction'];
		$productschemsubstances = $core->input['chemsubstances'];
		unset($core->input['action'], $core->input['module'], $core->input['applicationfunction'], $core->input['chemsubstances']);

		$query = $db->update_query('products', $core->input, "pid='".$db->escape_string($core->input['pid'])."'");
		if($query) {
			if(isset($chemicalfunctionsproducts)) {
				$db->delete_query('chemfunctionproducts', 'pid='.$db->escape_string($core->input['pid']));
				foreach($chemicalfunctionsproducts as $chemicalfunctions) {
					foreach($chemicalfunctions as $safid) {
						$chemfunctionproducts_arary = array('pid' => $core->input['pid'],
								'safid' => $safid,
								'modifiedBy' => $core->user['uid'],
								'modifiedOn' => TIME_NOW
						);
						$db->insert_query('chemfunctionproducts', $chemfunctionproducts_arary);
						if($safid == $core->input['defaultFunction']) {
							$defaultfunction = $db->last_id();
						}
					}
				}

				if(empty($defaultfunction)) {
					$defaultfunction = array_shift(array_values($chemicalfunctions)); /* shift the array and get the first element value */
				}

				$db->update_query('products', array('defaultFunction' => $defaultfunction), 'pid='.$db->escape_string($core->input['pid']));
			}

			/* insert chemical functions produts */
			if(isset($productschemsubstances)) {
				$db->delete_query('productschemsubstances', 'pid='.$db->escape_string($core->input['pid']));
				foreach($productschemsubstances as $productschemsubstances) {
					$chemsubstances_array = array('pid' => $core->input['pid'],
							'csid' => $productschemsubstances['csid'],
							'modifiedBy' => $core->user['uid'],
							'modifiedOn' => TIME_NOW
					);

					$db->insert_query('productschemsubstances', $chemsubstances_array);
				}
			}
			$lang->productedited = $lang->sprint($lang->productedited, $core->input['name']);
			output_xml("<status>true</status><message>{$lang->productedited}</message>");
		}
		else {
			output_xml("<status>false</status><message>{$lang->erroreditingproduct}</message>");
		}
	}
	elseif($core->input['action'] == 'perform_mergeanddelete') {
		$oldid = $db->escape_string($core->input['todelete']);
		$products_tables = array('productsactivity' => 'pid', ' integration_mediation_products' => 'localId', 'budgeting_budgets_lines' => 'pid');
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
		else {
			output_xml("<status>false</status><message>{$lang->errordeleting}</message>");
		}
	}
	elseif($core->input['action'] == 'get_mergeanddelete') {
		eval("\$mergeanddeletebox = \"".$template->get("popup_mergeanddelete")."\";");
		output($mergeanddeletebox);
	}
}
?>