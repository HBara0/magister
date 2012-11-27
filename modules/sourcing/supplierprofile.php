<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Potential Supplier Profile
 * $module: Sourcing
 * $id: supplierprofile.php	
 * Last Update: @tony.assaad	october 30, 2012 | 4:05 AM
 */
if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['sourcing_canListSuppliers'] == 0 || $core->usergroup['sourcing_canManageEntries'] == 0) {
	error($lang->sectionnopermission);
}

if(!empty($core->input['supplierid'])) { /* supplier id afer sumbit contact supplier form */
	$supplier_id = $core->input['supplierid'];
}
else {
	$supplier_id = $db->escape_string($core->input['id']);
}

$potential_supplier = new Sourcing($supplier_id);
if(!$potential_supplier->supplier_exists()) {
	redirect('index.php?module=sourcing/listpotentialsupplier');
}

if($potential_supplier->is_blacklisted()) { /* if supplier isBlacklisted */
	redirect(DOMAIN."/index.php?module=sourcing/listpotentialsupplier");
}

if(!$core->input['action']) {
	$supplier['maindetails'] = $potential_supplier->get_supplier();
	$supplier['contactdetails'] = $potential_supplier->get_supplier_contactdetails();
	$supplier['segments'] = $potential_supplier->get_supplier_segments();
	$supplier['contactpersons'] = $potential_supplier->get_supplier_contact_persons();
	$supplier['activityareas'] = $potential_supplier->get_supplier_activity_area();
	$supplier['chemicalsubstances'] = $potential_supplier->get_chemicalsubstances();

	if(is_array($supplier['segments'])) {
		$segments_output = '<ul><li>'.implode('</li><li>', $supplier['segments']).'</li></ul>';
	}

	if(is_array($supplier['activityareas'])) {
		$langactivityarea = $lang->activityarea;
		foreach($supplier['activityareas'] as $activity_area) {
			$activity_area_data .= '<li>'.$activity_area['country'].' - '.$activity_area['affiliate'].'</li>';
		}
		$activityarea_output = '<ul>'.$activity_area_data.'</ul>';
	}

	/* Chemical List - START */
	$chemicalslist_section = '';
	if(is_array($supplier['chemicalsubstances'])) {
		foreach($supplier['chemicalsubstances'] as $chemical) {
			$rowclass = alt_row($rowclass);
			$chemicalslist_section .='<tr class="'.$rowclass.'"><td width="10%">'.$chemical['casNum'].'</td><td align="left">'.$chemical['name'].'</td></tr>';
		}
	}
	else
	{
		$chemicalslist_section = '<tr><td colspan="2">{$lang->na}</td></tr>';
	}
	/* Chemical List - END */

	$supplier['maindetails']['businessPotential_output'].= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-value="'.$supplier['maindetails']['businessPotential'].'"></div>';

	/* Parse contact info - START */
	$supplier['contactdetails']['fulladress'] = $supplier['contactdetails']['addressLine1'].','.$supplier['contactdetails']['addressLine2'];
	$supplier['contactdetails']['phones'] = '+'.$supplier['contactdetails']['phone1'];
	if(!empty($supplier['contactdetails']['phone2'])) {
		$supplier['contactdetails']['phones'] .= '/'.'+'.$supplier['contactdetails']['phone2'];
	}
	
	$supplier['contactdetails']['fax'] = '+'.$supplier['contactdetails']['fax'];
	if(!value_exists('sourcing_suppliers_contacthist', 'ssid', $supplier_id, 'uid='.$core->user['uid']) && $core->usergroup['sourcing_canManageEntries'] == 0) {
		$can_seecontactinfo = false;
		/* Hash values */
		
		$hashed_attributes = array('fulladress' => $supplier['contactdetails']['fulladress'], 'phones' => $supplier['contactdetails']['phones'], 'poBox' => $supplier['contactdetails']['poBox'], 'fax' => $supplier['contactdetails']['fax'], 'mainEmail' => $supplier['contactdetails']['mainEmail'], 'website' => $supplier['contactdetails']['website'], 'contact' => $contact_person['name']);
		foreach($hashed_attributes as $key => $hashedvalue) {
			$supplier['contactdetails'][$key] = md5($supplier['contactdetails'][$key]);
		}

		/* Show contact button */
		$contactsupplier_button = '<hr /><form  name="perform_sourcing/supplierprofile_Form" action="index.php?module=sourcing/supplierprofile&amp;action=do_contactsupplier" method="post" id"perform_sourcing/supplierprofile_Form" >
			<input type="submit" class="button" value="'.$lang->contact.'" />
			<input type="hidden" value="'.$supplier['maindetails']['ssid'].'" name="supplierid" />	
		</form>';
		/* Blur text */
		$header_blurjs = '$(".contactsvalue").each(function(){$(this).addClass("blur");});';
	}
	else
	{
		$can_seecontactinfo = true;
	}
		
	if(is_array($supplier['contactpersons'])) {
		$contactpersons_output = '<ul>';
		foreach($supplier['contactpersons'] as $contactperson) {
			if($can_seecontactinfo == false) {
				$contactperson['name'] = md5($contactperson['name']);
				$contactperson['rpid'] = 0;
			}
			$contactpersons_output .= '<li><span class="contactsvalue" id="contactpersondata_'.$contactperson['rpid'].'">'.$contactperson['name'].'</span></li>';
		}
		$contactpersons_output .= '</ul>';
	}
	/* Parse contact info - END */

	/* Communication Report after the user has initiated contact - START */
	if($can_seecontactinfo == true) {
		$contactsupplier_button = '';
		$affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', '');
		$affiliates_list = parse_selectlist('contacthst[affid]', 1, $affiliates, $core->user['mainaffiliate'], 0);
		$countries = get_specificdata('countries', array('coid', 'name'), 'coid', 'name', '');
		$countries_list = parse_selectlist('contacthst[origin]', 8, $countries, '');
		$product_segmentlist = parse_selectlist('contacthst[market]', 9, $supplier['segments'], ''); /* product segments (that the current supplier(loaded from the object) works in) */
		$supplierid = $core->input['supplierid'];
		$newsupplierid = $core->input['id'];
		eval("\$reportcommunication_section = \"".$template->get('sourcing_potentialsupplierprofile_reportcommunication')."\";");
	}
	/* Communication Report after the user has initiated contact - END */

	/* contact histrory - START */
	$contacts_history = $potential_supplier->get_contact_history();
	if(is_array($contacts_history)) {
		foreach($contacts_history as $contact_history) {
			$rowclass = alt_row($rowclass);
			$contact_history['date_output'] = date($core->settings['dateformat'], $contact_history['date']);
			eval("\$contacthistory_section .= \"".$template->get('sourcing_potentialsupplierprofile_contacthistory')."\";");
		}
	}
	/* contact histrory - END */

	eval("\$supplierprofile = \"".$template->get('sourcing_potentialsupplierprofile')."\";");
	output_page($supplierprofile);
}
else {
	if($core->input['action'] == 'do_contactsupplier') {
		$supplier_id = $db->escape_string($core->input['supplierid']);
		$potential_supplier->contact_supplier($supplier_id);
		
		redirect(DOMAIN.'/index.php?module=sourcing/supplierprofile&amp;id='.$supplier_id);
	}
	elseif($core->input['action'] == 'do_savecommunication') {
		$newsupplierid = $db->escape_string($core->input['contacthst']['ssid']);
		//$potential_supplier = new Sourcing($core->input['id']);
		/* system should check if user has  previous contactshistory */

		$potential_supplier->save_communication_report($core->input['contacthst'], $newsupplierid);
		
		switch($potential_supplier->get_status()) {
			case 3:
				output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
				break;
			case 1:
				output_xml("<status>false</status><message>{$lang->reportfieldrequired}</message>");
				break;
			case 2:
				output_xml("<status>true</status><message>{$lang->successfullyupdate}</message>");
				break;
		}
	}
	elseif($core->input['action'] == 'preview') {
		$rpid = $db->escape_string($core->input['rpid']);
		$supplier_contact = $potential_supplier->get_single_supplier_contact_person($rpid);
		//Make a template
		echo '<div style="min-width:400px; max-width:600px;">
	<div style="display:inline-block;width:180px;">'.$supplier_contact['name'].'<br><strong>'.$lang->email.'</strong>  <a href="mailto:'.$supplier_contact['email'].'">'.$supplier_contact['email'].'</a><br>'.'<strong>'.$lang->phone.'</strong> '.$supplier_contact['phone'].'<br>'.'<strong>'.$lang->positon.'</strong><br>'.'<strong>'.$contact_personposition.'</strong></div></div>';
	}
}
?>