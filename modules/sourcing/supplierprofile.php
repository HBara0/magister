<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Potential Supplier Profile
 * $module: Sourcing
 * $id: supplierprofile.php	
 * Last Update: @tony.assaad	February 15, 2012 | 10:05 AM
 */
if(!defined('DIRECT_ACCESS'))
{
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['sourcing_canListSuppliers'] == 0 || $core->usergroup['sourcing_canManageEntries'] == 0) {
	error($lang->sectionnopermission);
	exit;
}
$supplier_id =  67;
$potential_supplier = new Sourcing($supplier_id);
if(!$core->input['action']) {
		$potential_supplier_details = $potential_supplier->get_supplier_contact();
		$segments_suppliers = $potential_supplier->get_supplier_segments();
		$supplier_contact =  $potential_supplier->get_supplier_contact_persons();
		$supplier_activity_area =  $potential_supplier->get_supplier_activity_area();
		$segment_data = '<ul>';
		foreach($segments_suppliers  as $segments_supplier) {
			$segment_data .='<li>'. $segments_supplier['segment'].'</li>';
			}
			$segment_data = $segment_data.'</ul>';
	
	foreach($supplier_contact  as $contact_person) {
			$contact_person_data .= '</br><span id="contactpersondata_'.$contact_person['rpid'].'">'.$contact_person['name'].'</span>';
		
			}	
		$activity_area_data = '<ul>';
		foreach($supplier_activity_area  as $activity_area) {
			$activity_area_data .='<li>'. $activity_area['name'].'-'.$activity_area['affiliate'].'</li>';
			}
			$activity_area_data = $activity_area_data.'</ul>';
			
		$potential_supplier_details['rating'].= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-value="'.$potential_supplier_details['businessPotential'].'"></div>';

	$potential_supplier_details['fulladress'] = $potential_supplier_details['addressLine1'].','.$potential_supplier_details['addressLine2'] ;	
		
/*When user has not initiated a contact -STARY*/		
	if(!value_exists('sourcing_suppliers_contacthist', 'ssid', $supplier_id, 'uid='.$core->user['uid'])) {
		$hashed_attributes = array('fulladress'=>$potential_supplier_details['fulladress'],'poBox'=>$potential_supplier_details['poBox'],'fax'=>$potential_supplier_details['fax'],'mainEmail'=>$potential_supplier_details['mainEmail'],'website'=>$potential_supplier_details['website']);
		foreach($hashed_attributes as $key=>$hashedvalue){
			$potential_supplier_details[$key] = md5($potential_supplier_details[$key]);
		}
		
	$contactsupplier_form = '<form  name="perform_sourcing/supplierprofile_Form" action="index.php?module=sourcing/supplierprofile&action=do_contactsupplier" method="post" id"perform_sourcing/supplierprofile_Form" >
<input type="submit" class="button" value="'.$lang->contact.'" /></form>';
		$header_blurjs = '$(function(){
			$(".detailsvalue").each(function(){$(this).addClass("blur");});
			});';
	}
/*When user has not initiated a contact -END*/
	
/*communication Report after the user has initiated contact-START*/		
	else
	{
		$affiliates = get_specificdata('affiliates', array('affid','name'), 'affid', 'name','');
		$affiliates_list = parse_selectlist("contacthst[affid]",1, $affiliates, $useraffiliates, 0);
		$countries = get_specificdata('countries', array('coid', 'name'), 'coid', 'name','');
		$countries_list = parse_selectlist('contacthst[origin]', 8, $countries, '');
	
		
		eval("\$sourcing_Potentialsupplierprofile_reportcommunication = \"".$template->get('sourcing_Potentialsupplierprofile_reportcommunication')."\";");		
	}
/*communication Report after the user has initiated contact -END*/		

	eval("\$sourcing_Potentialsupplierprofile_contactsection = \"".$template->get('sourcing_Potentialsupplierprofile_contactsection')."\";");


	eval("\$sourcingPotentialsupplierprofile = \"".$template->get('sourcing_Potentialsupplierprofile')."\";");
	output_page($sourcingPotentialsupplierprofile);

}

elseif($core->input['action']=='do_contactsupplier') {

	$potential_supplier->contact_supplier();
	redirect(DOMAIN."/index.php?module=sourcing/supplierprofile");
		
}
elseif($core->input['action'] == 'do_savecommunication') {
	//system should check if user has any entry in the sourcing_contactshistory, value_exists
	$potential_supplier->save_communication_report($core->input['contacthst']);		
}



elseif($core->input['action']=='preview') {
	$rpid = $db->escape_string($core->input['rpid']);
	$supplier_contact =  $potential_supplier->get_single_supplier_contact_persons($rpid);

	echo '<div style="min-width:400px; max-width:600px;">
	<div style="display:inline-block;width:180px;">'.$supplier_contact['name'].'<br><strong>'.$lang->email.'</strong>  <a href="mailto:'.$supplier_contact['email'].'">'.$supplier_contact['email'].'</a><br>'.'<strong>'.$lang->phone.'</strong> '.$supplier_contact['phone'].'<br>'.'<strong>'.$lang->positon.'</strong><br>'.'<strong>'.$contact_personposition.'</strong></div></div>'; 
					
	}
	
?>