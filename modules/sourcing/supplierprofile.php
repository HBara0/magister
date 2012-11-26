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

if(value_exists('sourcing_suppliers', 'ssid', $supplier_id, ' isBlacklisted=1')) { /* if supplier isBlacklisted */
	redirect(DOMAIN."/index.php?module=sourcing/listpotentialsupplier");
}
if(!value_exists('sourcing_suppliers', 'ssid', $supplier_id)) { /* if no supplier id exist in the database */
	//error("id faulttt", 'index.php?module=sourcing/listpotentialsupplier');
}
$potential_supplier = new Sourcing($supplier_id);
if(!$core->input['action']) {
	$supplier_details = $potential_supplier->get_supplier();
	$potential_supplier_details = $potential_supplier->get_supplier_contactdetails();
	$segments_suppliers = $potential_supplier->get_supplier_segments();
	$supplier_contact = $potential_supplier->get_supplier_contact_persons();
	$supplier_activity_area = $potential_supplier->get_supplier_activity_area();
	$chemical_substances = $potential_supplier->get_chemicalsubstances();

	if(is_array($segments_suppliers)) {
		$segment_data = '<ul>';
		foreach($segments_suppliers as $segments_supplier) {
			$segment_data .='<li>'.$segments_supplier.'</li>';
		}
	}
	$segment_data = $segment_data.'</ul>';
	if(is_array($supplier_contact)) {
		foreach($supplier_contact as $contact_person) {
			$contact_person_data .= '</br><span id="contactpersondata_'.$contact_person['rpid'].'">'.$contact_person['name'].'</span>';
		}
	}

	if(is_array($supplier_activity_area)) { //print_r($supplier_activity_area);
		$activity_area_data = '<ul>';
		$langactivityarea = $lang->activityarea;
		foreach($supplier_activity_area as $activity_area) {
			$activity_area_data .='<li>'.$activity_area['country'].'-'.$activity_area['affiliate'].'</li>';
		}
	}
	$activity_area_data = $activity_area_data.'</ul>';

	/* Chemical nList -START */
	if(is_array($chemical_substances)) {
		$listcas_numbers_section = '<div style="width:100% ;height: 200px; overflow:auto; display:inline-block; vertical-align:top;">
				<table class="datatable" width="100%">
				<thead> <td class="thead">'.$lang->cas.'</td><td class="thead">'.$lang->checmicalproduct.'</td></thead>';
		foreach($chemical_substances as $chemical) {
			$listcas_numbers_section .='<tr class="{$rowclass}"><td width="10%">'.$chemical['casNum'].'</td>
					<td align="left">'.$chemical['name'].'</td>
					</tr>';
		}
		$listcas_numbers_section .='</table></div>';
	}

	/* Chemical List -END */
	if(!empty($supplier_details['commentsToShare'])) {
		$commentshare_section = "<div style=display:table-row; padding:10px;><strong>{$lang->commentstoshare}</strong></div>
								{$supplier_details[commentsToShare]}
								<div class=border_bottom> </div>";
	}
	if(!empty($supplier_details['marketingRecords'])) {
		$marketingrecords_section = "<div style=display:table-row; padding:10px;><strong>{$lang->marketingrecords}</strong></div>
								{$supplier_details[marketingRecords]}
								<div class=border_bottom> </div>";
	}
	if(!empty($supplier_details['historical'])) {
		$historical_section = "<div style=display:table-row; padding:10px;><strong>{$lang->historical}</strong></div>
							{$supplier_details[historical]}
							<div class=border_bottom> </div>";
	}
	if(!empty($supplier_details['sourcingRecords'])) {
		$sourcingRecords_section = "<div style=display:table-row; padding:10px;><strong>{$lang->sourcingRecords}</strong></div>
				{$supplier_details[sourcingRecords]}
				<div class=border_bottom> </div>";
	}
	if(!empty($supplier_details['coBriefing'])) {
		$coBriefing_section = "<div style=display:table-row; padding:10px;><strong>{$lang->coBriefing}</strong></div>
				{$supplier_details[coBriefing]}
				<div class=border_bottom> </div>";
	}
	$supplier_details['rating'].= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-value="'.$supplier_details['businessPotential'].'"></div>';

	$potential_supplier_details['fulladress'] = $potential_supplier_details['addressLine1'].','.$potential_supplier_details['addressLine2'];

	/* When user has not initiated a contact -START */

	if(!value_exists('sourcing_suppliers_contacthist', 'ssid', $supplier_id, 'uid='.$core->user['uid']) || ($core->usergroup['sourcing_canManageEntries'] == 0)) {
		$hashed_attributes = array('fulladress' => $potential_supplier_details['fulladress'], 'poBox' => $potential_supplier_details['poBox'], 'fax' => $potential_supplier_details['fax'], 'mainEmail' => $potential_supplier_details['mainEmail'], 'website' => $potential_supplier_details['website'], 'contact' => $contact_person['name']);
		foreach($hashed_attributes as $key => $hashedvalue) {
			$potential_supplier_details[$key] = md5($potential_supplier_details[$key]);
		}

		$contactsupplier_form = '<form  name="perform_sourcing/supplierprofile_Form" action="index.php?module=sourcing/supplierprofile&action=do_contactsupplier" method="post" id"perform_sourcing/supplierprofile_Form" >
<input type="submit" class="button" value="'.$lang->contact.'" />
<input type="hidden" value="'.$core->input['id'].'" name="supplierid" />	
</form>';
		$header_blurjs = '$(function(){
			$(".detailsvalue").each(function(){$(this).addClass("blur");});
			});';
	}


	/* When user has not initiated a contact -END */

	/* communication Report after the user has initiated contact-START */
	else {
		$contactsupplier_form = '';
		$affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', '');
		$affiliates_list = parse_selectlist("contacthst[affid]", 1, $affiliates, $core->user['mainaffiliate'], 0);
		$countries = get_specificdata('countries', array('coid', 'name'), 'coid', 'name', '');
		$countries_list = parse_selectlist('contacthst[origin]', 8, $countries, '');
		if(is_array($segments_suppliers)) {
			$product_segmentlist = parse_selectlist('contacthst[market]', 9, $segments_suppliers, ''); /* product segments (that the current supplier(loaded from the object) works in) */
		}
		$supplierid = $core->input['supplierid'];
		$newsupplierid = $core->input['id'];
		eval("\$sourcing_Potentialsupplierprofile_reportcommunication = \"".$template->get('sourcing_Potentialsupplierprofile_reportcommunication')."\";");
	}
	/* communication Report after the user has initiated contact -END */
	eval("\$sourcing_Potentialsupplierprofile_contactsection = \"".$template->get('sourcing_Potentialsupplierprofile_contactsection')."\";");

	/* contact histrory -START */
	if(value_exists('sourcing_suppliers_contacthist', 'ssid', $supplier_id, 'uid='.$core->user['uid'])) {
		$contacts_history = $potential_supplier->get_contact_history();
		if(is_array($contacts_history)) {
			foreach($contacts_history as $contact_history) {
				$rowclass = alt_row($rowclass);
				$contact_history['date_output'] = date($core->settings['dateformat'], $contact_history['date']);
				eval("\$sourcing_Potentialsupplierprofile_contacthistory .= \"".$template->get('sourcing_Potentialsupplierprofile_contacthistory')."\";");
			}
		}
	}

	/* contact histrory -END */

	eval("\$sourcingPotentialsupplierprofile = \"".$template->get('sourcing_Potentialsupplierprofile')."\";");
	output_page($sourcingPotentialsupplierprofile);
}
else {
	if($core->input['action'] == 'do_contactsupplier') {
		$supplier_id = $db->escape_string($core->input['supplierid']);
		$potential_supplier->contact_supplier($supplier_id);
		redirect(DOMAIN."/index.php?module=sourcing/supplierprofile&id=".$supplier_id."");
	}
	elseif($core->input['action'] == 'do_savecommunication') {
		$newsupplierid = $db->escape_string($core->input['contacthst']['ssid']);
		//$potential_supplier = new Sourcing($core->input['id']);
		/* system should check if user has  previous contactshistory */
		if(isset($core->input['contacthst']['orderpassed'])){
			echo'orderr';
			exit;
		}
		if(value_exists('sourcing_suppliers_contacthist', 'ssid', $newsupplierid, 'uid='.$core->user['uid'])) {
			$potential_supplier->save_communication_report($core->input['contacthst'], $newsupplierid);
		}
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
		$supplier_contact = $potential_supplier->get_single_supplier_contact_persons($rpid);

		echo '<div style="min-width:400px; max-width:600px;">
	<div style="display:inline-block;width:180px;">'.$supplier_contact['name'].'<br><strong>'.$lang->email.'</strong>  <a href="mailto:'.$supplier_contact['email'].'">'.$supplier_contact['email'].'</a><br>'.'<strong>'.$lang->phone.'</strong> '.$supplier_contact['phone'].'<br>'.'<strong>'.$lang->positon.'</strong><br>'.'<strong>'.$contact_personposition.'</strong></div></div>';
	}
}
?> 