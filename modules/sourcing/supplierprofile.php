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

//if(value_exists('sourcing_suppliers', 'ssid', $supplier_id, 'isBlacklisted=1')) { /* if supplier isBlacklisted */
//	redirect(DOMAIN."/index.php?module=sourcing/listpotentialsupplier");
//}
//if(!value_exists('sourcing_suppliers', 'ssid', $supplier_id)) { /* if we no supplier id exist in the database */
//	error("id faulttt", 'index.php?module=sourcing/listpotentialsupplier');
//}
$potential_supplier = new Sourcing($supplier_id);
if(!$core->input['action']) {
	$supplier['maindetails'] = $potential_supplier->get_supplier();
	$supplier['contactdetails'] = $potential_supplier->get_supplier_contactdetails();
	$supplier['segments'] = $potential_supplier->get_supplier_segments();
	$supplier['contactpersons'] = $potential_supplier->get_supplier_contact_persons();
	$supplier['activityareas'] = $potential_supplier->get_supplier_activity_area();
	$supplier['chemicalsubstances'] = $potential_supplier->get_chemicalsubstances();

	if(is_array($supplier['segments'])) {
		$segment_data = $segments_output = '<ul><li>'.implode('</li><li>', $supplier['segments']).'</li></ul>';
	}

	if(is_array($supplier['contactpersons'])) {
		$contact_person_data = $contactpersons_output = '<ul><li><span id="contactpersondata_'.$contact_person['rpid'].'">'.implode('</span></li><li><span id="contactpersondata_'.$contact_person['rpid'].'">', $supplier['contactpersons']).'</span></li></ul>';
	}

	if(is_array($supplier['activityareas'])) {
		$langactivityarea = $lang->activityarea;
		foreach($supplier['activityareas'] as $activity_area) {
			$activity_area_data .= '<li>'.$activity_area['country'].' - '.$activity_area['affiliate'].'</li>';
		}
		$activity_area_data = $activityarea_output = '<ul>'.$activity_area_data.'</ul>';
	}

	/* Chemical List - START */
	if(is_array($supplier['chemicalsubstances'])) {
		$productslist_section = '<div style="width:100%; height: 200px; overflow:auto; display:inline-block; vertical-align:top;">
				<table class="datatable" width="100%">
				<thead> <td class="thead">'.$lang->cas.'</td><td class="thead">'.$lang->checmicalproduct.'</td></thead>';
		foreach($supplier['chemicalsubstances'] as $chemical) {
			$productslist_section .='<tr class="'.$rowclass.'"><td width="10%">'.$chemical['casNum'].'</td><td align="left">'.$chemical['name'].'</td></tr>';
		}
		$productslist_section .= '</table></div>';
	}
	/* Chemical List - END */
	
	if(!empty($supplier['maindetails']['commentsToShare'])) {
		$commentshare_section = "<div style=display:table-row; padding:10px;><strong>{$lang->commentstoshare}</strong></div>
								{$supplier['maindetails'][commentsToShare]}
								<div class=border_bottom> </div>";
	}
	if(!empty($supplier['maindetails']['marketingRecords'])) {
		$marketingrecords_section = "<div style=display:table-row; padding:10px;><strong>{$lang->marketingrecords}</strong></div>
								{$supplier['maindetails'][marketingRecords]}
								<div class=border_bottom> </div>";
	}
	if(!empty($supplier['maindetails']['historical'])) {
		$historical_section = "<div style=display:table-row; padding:10px;><strong>{$lang->historical}</strong></div>
							{$supplier['maindetails'][historical]}
							<div class=border_bottom> </div>";
	}
	if(!empty($supplier['maindetails']['sourcingRecords'])) {
		$sourcingRecords_section = "<div style=display:table-row; padding:10px;><strong>{$lang->sourcingRecords}</strong></div>
				{$supplier['maindetails'][sourcingRecords]}
				<div class=border_bottom> </div>";
	}
	if(!empty($supplier['maindetails']['coBriefing'])) {
		$coBriefing_section = "<div style=display:table-row; padding:10px;><strong>{$lang->coBriefing}</strong></div>
				{$supplier['maindetails'][coBriefing]}
				<div class=border_bottom> </div>";
	}
	$supplier['maindetails']['rating'].= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-value="'.$supplier['maindetails']['businessPotential'].'"></div>';

	$supplier['contactdetails']['fulladress'] = $supplier['contactdetails']['addressLine1'].','.$supplier['contactdetails']['addressLine2'];

	/* When user has not initiated a contact -START */

	if(!value_exists('sourcing_suppliers_contacthist', 'ssid', $supplier_id, 'uid='.$core->user['uid']) || $core->usergroup['sourcing_canManageEntries'] == 0) {
		$hashed_attributes = array('fulladress' => $supplier['contactdetails']['fulladress'], 'poBox' => $supplier['contactdetails']['poBox'], 'fax' => $supplier['contactdetails']['fax'], 'mainEmail' => $supplier['contactdetails']['mainEmail'], 'website' => $supplier['contactdetails']['website'], 'contact' => $contact_person['name']);
		foreach($hashed_attributes as $key => $hashedvalue) {
			$supplier['contactdetails'][$key] = md5($supplier['contactdetails'][$key]);
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
		$product_segmentlist = parse_selectlist('contacthst[market]', 9, $supplier['segments'], ''); /* product segments (that the current supplier(loaded from the object) works in) */
		$supplierid = $core->input['supplierid'];
		$newsupplierid = $core->input['id'];
		eval("\$sourcing_Potentialsupplierprofile_reportcommunication = \"".$template->get('sourcing_Potentialsupplierprofile_reportcommunication')."\";");
	}
	/* communication Report after the user has initiated contact -END */
	eval("\$sourcing_Potentialsupplierprofile_contactsection = \"".$template->get('sourcing_Potentialsupplierprofile_contactsection')."\";");

	/* contact histrory - START */
	//if(value_exists('sourcing_suppliers_contacthist', 'ssid', $supplier_id, 'uid='.$core->user['uid'])) {
		$contacts_history = $potential_supplier->get_contact_history();
		if(is_array($contacts_history)) {
			foreach($contacts_history as $contact_history) {
				$rowclass = alt_row($rowclass);
				$contact_history['date_output'] = date($core->settings['dateformat'], $contact_history['date']);
				eval("\$sourcing_Potentialsupplierprofile_contacthistory .= \"".$template->get('sourcing_Potentialsupplierprofile_contacthistory')."\";");
			}
		}
	//}

	/* contact histrory - END */

	eval("\$supplierprofile = \"".$template->get('sourcing_Potentialsupplierprofile')."\";");
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
		//Make a template
		echo '<div style="min-width:400px; max-width:600px;">
	<div style="display:inline-block;width:180px;">'.$supplier_contact['name'].'<br><strong>'.$lang->email.'</strong>  <a href="mailto:'.$supplier_contact['email'].'">'.$supplier_contact['email'].'</a><br>'.'<strong>'.$lang->phone.'</strong> '.$supplier_contact['phone'].'<br>'.'<strong>'.$lang->positon.'</strong><br>'.'<strong>'.$contact_personposition.'</strong></div></div>';
	}
}
?>