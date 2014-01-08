<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 *
 * Edit entities
 * $module: admin/entities
 * $id: edit.php
 * Created:		@zaher.reda
 * Last Update: @zaher.reda 	November 26, 2010 | 11:44 AM
 */
if(!defined("DIRECT_ACCESS")) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canManageSuppliers'] == 0 || $core->usergroup['canManageCustomers'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!$core->input['action']) {
	if(!isset($core->input['eid']) || empty($core->input['eid'])) {
		redirect('index.php?module=home/index');
	}

	$eid = $db->escape_string($core->input['eid']);
	$entity = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."entities WHERE eid='{$eid}'"));

	if($entity['type'] == 'c' && $core->usergroup['canManageCustomers'] == 0) {
		redirect('index.php?module=home/index');
	}

	if($entity['type'] == 's' && $core->usergroup['canManageSuppliers'] == 0) {
		redirect('index.php?module=home/index');
	}
	if($entity['isPotential'] == 1 && $entity[type] == 's') {
		$entity['type'] = 'potentialsupplier';
	}
	elseif($entity['isPotential'] == 1 && $entity[type] == 'c') {
		$entity['type'] = 'potentialcusotmer';
	}
	$presence = array('regional' => $lang->regional, 'local' => $lang->local, 'multinational' => $lang->multinational);

	$radiobuttons_check['presence'][$entity['presence']] = ' checked="checked"';

	$types_list = parse_selectlist('type', 1, array('c' => $lang->customer, 's' => $lang->supplier, 'potentialcusotmer' => $lang->potentialcusotmer, 'potentialsupplier' => $lang->potentialsupplier), $entity['type']);
	$supptypes = array('trader' => $lang->trader, 'producer' => $lang->producer, 'both' => $lang->both);
	$supptypes_list = parse_selectlist('supplierType', 1, $supptypes, $entity['supplierType']);

	$query = $db->query("SELECT psid FROM ".Tprefix."entitiessegments WHERE eid='{$eid}'");
	while($segment = $db->fetch_assoc($query)) {
		$entity_segments[$segment['psid']] = $segment['psid'];
	}
	$segments_list = parse_selectlist("psid[]", 3, get_specificdata('productsegments', array('psid', 'title'), 'psid', 'title', 'title'), $entity_segments, 1);


	$affiliates_attributes = array('affid', 'name');
	$affiliates_order = array(
			'by' => 'name',
			'sort' => 'ASC'
	);

	$affiliates = get_specificdata('affiliates', $affiliates_attributes, 'affid', 'name', $affiliates_order);

	$query = $db->query("SELECT uid FROM ".Tprefix."assignedemployees WHERE eid='{$eid}'");
	while($user = $db->fetch_assoc($query)) {
		$entity_users[$user['uid']] = $user['uid'];
	}
	$users_list = parse_selectlist("uids[]", 5, get_specificdata('users', array('uid', 'Concat(firstName, \' \', lastName) AS fullName'), 'uid', 'fullName', 'firstName'), $entity_users, 1);

	$query = $db->query("SELECT * FROM ".Tprefix."affiliatedentities WHERE eid='{$eid}'");
	while($affiliate = $db->fetch_array($query)) {
		$entity_affiliates[$affiliate['affid']] = $affiliate['affid'];
	}
	$affiliates_list = parse_selectlist('affid[]', 4, $affiliates, $entity_affiliates, 1);

	$countries_attributes = array('coid', 'name');
	$countries_order = array(
			'by' => 'name',
			'sort' => 'ASC'
	);

	$countries = get_specificdata('countries', $countries_attributes, 'coid', 'name', $countries_order);
	$countries_list = parse_selectlist('country', 8, $countries, $entity['country']);

	$phones_index = array('fax1', 'fax2', 'phone1', 'phone2');
	foreach($phones_index as $val) {
		$current_number = explode('-', $entity[$val]);
		unset($entity[$val]);

		$entity[$val]['intcode'] = $current_number[0];
		$entity[$val]['areacode'] = $current_number[1];
		$entity[$val]['number'] = $current_number[2];
	}

	$createreports_disabled = ' disabled';
	if($entity['type'] == 'c') {
		$noqreportreq_disabled = ' disabled';
	}

	$query3 = $db->query("SELECT ase.uid, ase.affid, u.username
						FROM ".Tprefix."assignedemployees ase JOIN ".Tprefix."users u ON (u.uid=ase.uid)
						WHERE ase.eid='{$eid}'");

	while($user = $db->fetch_array($query3)) {
		$users[$user['uid']]['uid'] = $user['uid'];
		$users[$user['uid']]['username'] = $user['username'];
		$users[$user['uid']]['affid'][] = $user['affid'];
	}

	$users_counter = 1;
	if(is_array($users)) {

		foreach($users as $key => $val) {
			$affiliates_list_userssection = parse_selectlist("users[{$users_counter}][affiliates][]", 0, $affiliates, $val['affid'], 1);
			if(value_exists('suppliersaudits', 'uid', $val['uid'], "eid='{$eid}'")) {
				$validator_checked[$val['uid']] = " checked='checked'";
			}
			eval("\$users_rows .= \"".$template->get('admin_entities_addedit_userrow')."\";");
			$users_counter++;
		}
	}
	else {
		$affiliates_list_userssection = parse_selectlist("users[{$users_counter}][affiliates][]", 0, $affiliates, '', 1);
		$val = array();
		eval("\$users_rows = \"".$template->get('admin_entities_addedit_userrow')."\";");
	}
	$query2 = $db->query("SELECT er.*, r.*
						  FROM ".Tprefix."entitiesrepresentatives er LEFT JOIN ".Tprefix."representatives r ON (r.rpid=er.rpid)
						  WHERE er.eid='{$eid}'");
	if($db->num_rows($query2) > 0) {
		$rep_counter = 0;
		while($representative = $db->fetch_array($query2)) {
			++$rep_counter;
			$representative_rows .= " <tr id='{$rep_counter}'><td><input type='text' id='representative_{$rep_counter}_QSearch' autocomplete='off' size='40px' value='{$representative[name]}'/><input type='hidden' id='representative_{$rep_counter}_id' name='representative[{$rep_counter}][rpid]' value='{$representative[rpid]}'/><a href='#' id='addnew_entities/add_representative'><img src='../images/addnew.png' border='0' alt='{$lang->add}'></a><div id='searchQuickResults_representative_{$rep_counter}' class='searchQuickResults' style='display:none;'></div></td></tr>";
		}
	}
	else {
		$representative_rows = " <tr id='1'><td><input type='text' id='representative_1_QSearch' autocomplete='off' size='40px'/><input type='hidden' id='representative_1_id' name='representative[1][rpid]'/><a href='#' id='addnew_entities/add_representative'><img src='../images/addnew.png' border='0' alt='{$lang->add}'></a><div id='searchQuickResults_1' class='searchQuickResults' style='display:none;'></div></td></tr>";
	}

	if($entity['noQReportReq'] == 1) {
		$noqreportreq_checkbox = " checked='checked'";
	}

	if($entity['noQReportSend'] == 1) {
		$noqreportsend_checkbox = " checked='checked'";
	}

	if(!empty($entity['contractFirstSigDate'])) {
		$entity['contractFirstSigDate_output'] = date($core->settings['dateformat'], $entity['contractFirstSigDate']);
		$entity['contractFirstSigDate'] = date('d-m-Y', $entity['contractFirstSigDate']);
	}

	if(!empty($entity['contractExpiryDate'])) {
		$entity['contractExpiryDate_output'] = date($core->settings['dateformat'], $entity['contractExpiryDate']);
		$entity['contractExpiryDate'] = date('d-m-Y', $entity['contractExpiryDate']);
	}
	$contractIsEvergreen_check = '';
	if($entity['contractIsEvergreen'] == 1) {
		$contractIsEvergreen_check = ' checked';
	}
	$actiontype = 'edit';
	$pagetitle = $lang->sprint($lang->editentitywithname, $entity['companyName']);

	$eidfield = "<input type='hidden' value='{$eid}' name='eid'>";
	$headerinc .= "<link href='{$core->settings[rootdir]}/css/jqueryuitheme/jquery-ui-1.7.2.custom.css' rel='stylesheet' type='text/css' />";


	$entity['logo_output'] = '<img src="../uploads/entitieslogos/'.$entity['logo'].'" width="200" />';
	eval("\$editpage = \"".$template->get('admin_entities_addedit')."\";");
	output_page($editpage);
}
else {
	if($core->input['action'] == 'do_perform_edit') {
		$entity_data = $core->input;
		unset($entity_data['module'], $entity_data['action'], $entity_data['createReports']);
		if($entity_data['type'] == 'potentialcusotmer') {
			$entity_data['isPotential'] = 1;
			$entity_data['type'] = 'c';
		}
		elseif($entity_data['type'] == 'potentialsupplier') {
			$entity_data['isPotential'] = 1;
			$entity_data['type'] = 's';
		}
		else {
			$entity_data['isPotential'] = 0;
		}

		$entity = new Entities($entity_data, 'edit');
		if($entity->get_status() === true) {
			log_action($entity->get_eid());
		}
	}
	elseif($core->input['action'] == 'do_approve') {
		$attribute = $db->escape_string($core->input['attribute']);
		$newvalue = $db->escape_string($core->input['newvalue']);
		$eid = $db->escape_string($core->input['id']);

		$query = $db->update_query('entities', array($attribute => $newvalue), "eid={$eid}");
		if($db->affected_rows() > 0) {
			output_xml("<status>true</status><message></message>");
			log_action($eid);
		}
		else {
			output_xml("<status>false</status><message></message>");
		}
	}
	elseif($core->input['action'] == 'get_addnew_representative') {
		eval("\$addrepresentativebox = \"".$template->get('popup_addrepresentative')."\";");
		output_page($addrepresentativebox);
	}
}
?>