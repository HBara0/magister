<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Potential Supplier Profile
 * $module: Sourcing
 * $id: supplierprofile.php	
 * Last Update: @tony.assaad	January 3, 2013 | 3:05 PM
 */
if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['sourcing_canListSuppliers'] == 0) {
	error($lang->sectionnopermission);
}

if(!empty($core->input['supplierid'])) { /* supplier id afer sumbit contact supplier form */
	$supplier_id = $core->input['supplierid'];
}
else {
	$supplier_id = $db->escape_string($core->input['id']);
}

$potential_supplier = new Sourcing($supplier_id);
$lang->load("global");
if(!$core->input['action']) {
	if(!$potential_supplier->supplier_exists()) {
		redirect('index.php?module=sourcing/listpotentialsupplier');
	}

	if($potential_supplier->is_blacklisted()) { /* if supplier isBlacklisted */
		redirect(DOMAIN."/index.php?module=sourcing/listpotentialsupplier");
	}

	$supplier['maindetails'] = $potential_supplier->get_supplier();
	$supplier['contactdetails'] = $potential_supplier->get_supplier_contactdetails();
	$supplier['segments'] = $potential_supplier->get_supplier_segments();
	$supplier['contactpersons'] = $potential_supplier->get_supplier_contact_persons();
	$supplier['activityareas'] = $potential_supplier->get_supplier_activity_area();
	$supplier['chemicalsubstances'] = $potential_supplier->get_chemicalsubstances();

	if(is_array($supplier['segments']) && !empty($supplier['segments'])) {
		$segments_output = '<ul><li>'.implode('</li><li>', $supplier['segments']).'</li></ul>';
	}
	else {
		$segments_output = '<ul><li>'.$lang->na.'</li></ul>';
	}

	if(is_array($supplier['activityareas'])) {
		$langactivityarea = $lang->activityarea;
		foreach($supplier['activityareas'] as $activity_area) {
			$activity_area_data .= '<li>'.$activity_area['country'].' - '.$activity_area['affiliate'].'</li>';
		}
		$activityarea_output = '<ul>'.$activity_area_data.'</ul>';
	}
	else {
		$activityarea_output = $activityarea_output = '<ul><li>'.$lang->na.'</li></ul>';
	}

	/* Chemical List - START */
	$chemicalslist_section = '';
	if(is_array($supplier['chemicalsubstances'])) {
		foreach($supplier['chemicalsubstances'] as $chemical) {
			$rowclass = alt_row($rowclass);
			$chemicalslist_section .= '<tr class="'.$rowclass.'" style="vertical-align:top;"><td width="10%">'.$chemical['casNum'].'</td><td align="left">'.$chemical['name'].'</td><td>'.$chemical['supplyType_output'].'</td><td width="50%">'.$chemical['synonyms'].'</td></tr>';
		}
	}
	else {
		$chemicalslist_section = '<tr><td colspan="2">'.$lang->na.'</td></tr>';
	}
	/* Chemical List - END */
	if(!empty($supplier['maindetails']['companyNameAbbr'])) {
		$supplier['maindetails']['companyName'] .= ' ('.$supplier['maindetails']['companyNameAbbr'].')';
	}

	$supplier['maindetails']['businessPotential_output'].= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-value="'.$supplier['maindetails']['businessPotential'].'"></div>';
	$supplier['maindetails']['relationMaturity_output'] = $potential_supplier->parse_rmlbar();


	/* Parse contact info - START */
	$supplier['contactdetails']['fulladress'] = $supplier['contactdetails']['addressLine1'].', '.$supplier['contactdetails']['addressLine2'];
	$supplier['contactdetails']['phones'] = '+'.$supplier['contactdetails']['phone1'];
	if(!empty($supplier['contactdetails']['phone2'])) {
		$supplier['contactdetails']['phones'] .= '/'.'+'.$supplier['contactdetails']['phone2'];
	}

	$supplier['contactdetails']['fax'] = '+'.$supplier['contactdetails']['fax'];
	/* if no contact history made with the user for  the supplier and he is not Sourcing Agent */
	if(!value_exists('sourcing_suppliers_contacthist', 'ssid', $supplier_id, 'uid='.$core->user['uid']) && $core->usergroup['sourcing_canManageEntries'] == 0) {
		$can_seecontactinfo = false;
		/* Hash values */
		;
		$hashed_attributes = array('fulladress' => $supplier['contactdetails']['fulladress'], 'phones' => $supplier['contactdetails']['phones'], 'city' => $supplier['contactdetails']['city'], 'postCode' => $supplier['contactdetails']['postCode'], 'country' => $supplier['contactdetails']['country'], 'poBox' => $supplier['contactdetails']['poBox'], 'fax' => $supplier['contactdetails']['fax'], 'mainEmail' => $supplier['contactdetails']['mainEmail'], 'website' => $supplier['contactdetails']['website'], 'contact' => $contact_person['name']);
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
	else {
		$can_seecontactinfo = true;
	}
	if(is_array($supplier['contactpersons'])) {
		$contactpersons_output = '<ul>';
		foreach($supplier['contactpersons'] as $contactperson) {
			if($can_seecontactinfo == false) {
				$contactperson['name'] = md5($contactperson['name']);
				$contactperson['rpid'] = 0;
			}
			$contactpersons_output .= '<li><span class="contactsvalue" id="contactpersondata_'.$contactperson['rpid'].'_'.$supplier_id.'">'.$contactperson['name'].'</span></li>';
		}
		$contactpersons_output .= '</ul>';
	}
	/* Parse contact info - END */

	/* contact histrory - START */
	$contacts_history = $potential_supplier->get_contact_history();
	$form_actiontype = 'newcommunicationform';
	if(value_exists('sourcing_suppliers_contacthist', 'ssid', $supplier_id, 'isCompleted=0')) {
		$form_actiontype = 'prevcommunicationform';
	}

	if(is_array($contacts_history) || $can_seecontactinfo == true) {
		$affiliates = get_specificdata('affiliates ', array('affid', 'name'), 'affid', 'name', '');
		$affiliates_list = parse_selectlist('contacthst[affid]', 1, $affiliates, $core->user['mainaffiliate'], 0);

		$origins = array($lang->anyorigin => $lang->anyorigin, $lang->chinese => $lang->chinese, $lang->nonchinese => $lang->nonchinese, $lang->indian => $lang->indian, $lang->nonindian => $lang->nonindian, $lang->european => $lang->european, $lang->noneuropean => $lang->noneuropean, $lang->american => $lang->american, $lang->nonamerican => $lang->nonamerican);
		$origins_list = parse_selectlist('contacthst[origin]', 8, $origins, '');

		$product_segmentlist = parse_selectlist('contacthst[market]', 9, $supplier['segments'], ''); /* product segments (that the current supplier(loaded from the object) works in) */
		$unit_measure = get_specificdata('uom', array('uomid', 'symbol'), 'uomid', 'symbol', '');
		$newsupplierid = $core->input['id'];
		foreach($unit_measure as $key => $unit) {
			$selected = '';
			if($key == 4) {
				$selected = ' selected = "selected"';
			}
			$uom .= '<option value = "'.$key.'"'.$selected.'>'.$unit.'</option>';
		}

		if(is_array($contacts_history)) {
			foreach($contacts_history as $historyid => $contact_history) {
				$contact_history['chemical'] = $potential_supplier->get_chemicalsubstance_byid($contact_history['chemical']);

				$array_converteddate = array('date', 'customerDocumentDate', 'receivedQuantityDate', 'providedDocumentsDate', 'customerAnswerDate', 'provisionDate', 'offerDate', 'OfferAnswerDate');
				foreach($array_converteddate as $key => $value) {
					if(isset($contact_history[$value]) && !empty($contact_history[$value])) {
						$datepicker_id[$value] = 'pickDate_'.$historyid.uniqid();
						$contact_history[$value.'_output'] = date($core->settings['dateformat'], $contact_history[$value]);
					}
				}
				if($contact_history['isCompleted'] == 0) {
					$communication_title = $lang->previouscommunication;
					$action = 'do_updateprevcommunication';
					$communication_id = 'previouscommunication';
					$button_label = $lang->updateprevious;
					$identifier = $contact_history['identifier'];
					if(isset($contact_history['identifier']) && !empty($contact_history['identifier'])) {
						$contact_history['identifier'] = $contact_history['identifier'];
					}
					else {
						$contact_history['identifier'] = substr(md5(uniqid(microtime())), 1, 10);
					}
					$chemical_text_field = '<input class="rounded" type="text" value="'.$contact_history['chemical']['name'].'" id="chemicalproducts_'.$contact_history['identifier'].'_QSearch" autocomplete="off"size="40px"/>';
					$chemical_hidden_field = '<input type="hidden" id="chemicalproducts_'.$contact_history['identifier'].'" name = "contacthst[chemical]" value="'.$contact_history['chemical']['csid'].'"/>';
					$chemical_div_result = '<div id="searchQuickResults_chemicalproducts_'.$contact_history['identifier'].'" class="searchQuickResults" style="display:none;"></div>';


					/* load previous communication */
					eval("\$reportcommunication_filled_section = \"".$template->get('sourcing_potentialsupplierprofile_reportcommunication')."\";");
					unset($datepicker_id);
				}
				elseif($contact_history['isCompleted'] == 1) {

					$contact_history['chemical'] = $potential_supplier->get_chemicalsubstance_byid($contact_history['chemical']);
					$communications_fields = array('paymenttermssection' => array($lang->paymentterms => 'paymentTerms', $lang->discussion => 'Discussion'),
							'samplerequest' => array($lang->reqquantity=>'requestedQuantity',$lang->reqdocuments=>'requestedDocuments',$lang->recquantity=>'receivedQuantity',$lang->recdocuments=>'receivedDocuments'),
							'customersample' => array($lang->providedquantity=>'providedQuantity',$lang->provideddocuments=>'providedDocuments',$lang->customerAnswer=>'customerAnswer',$lang->dateon=>'receivedQuantityDate_output',$lang->dateon=>'providedDocumentsDate_output',$lang->dateon=>'customerAnswerDate_output'),
							'industrialtrial' => array($lang->industrialquantity=>'industrialQuantity',$lang->provisiondate=>'provisionDate_output',$lang->trialresult=>'trialResult'),
							'commercialoffer' => array($lang->offer=>'offerMade',$lang->offerdate=>'offerDate_output',$lang->customerAnswer=>'customerOfferAnswer',$lang->answerdate=>'OfferAnswerDate_output')
					);
					foreach($communications_fields as $key => $section) {
						$maindiv = '<div class="content" id="'.$key.'">';
						foreach($section as $label => $val) {
							if(isset($val) && !empty($val)) {
								$communictation_section.= '<div class = "content"><strong>'.$label.'</strong>:'.$contact_history[$val].'</div>';
							}
						}
					}
					$maindiv.=$communictation_section.'</div>';
					eval("\$reportcommunication_filled_section = \"".$template->get('sourcing_potentialsupplierprofile_displaycontacthistory')."\";");
					$communictation_section = '';
				}
				$altrow_class = alt_row($altrow_class);
				eval("\$contacthistory_section .= \"".$template->get('sourcing_potentialsupplierprofile_contacthistory')."\";");
			}

			unset($communictation_section, $section);
		}
	}
	unset($contact_history);
	/* contact histrory - END */

	/* New Communication Report after the user has initiated contact - START */
	if($can_seecontactinfo == true) {
		$contactsupplier_button = '';
		$identifier = substr(md5(uniqid(microtime())), 1, 10);
		$supplierid = $core->input['supplierid'];
		$newsupplierid = $core->input['id'];
		$communication_title = $lang->reportcommunication;
		$action = 'do_savecommunication';
		$communication_id = 'reportcommunication';
		$button_label = $lang->save;
		$class = "border_left border_right altrow";
		$array_converteddate = array('date', 'customerDocumentDate', 'receivedQuantityDate', 'providedDocumentsDate', 'customerAnswerDate', 'provisionDate', 'offerDate', 'OfferAnswerDate');
		foreach($array_converteddate as $key => $value) {
			$datepicker_id[$value] = 'pickDate_'.uniqid();
		}
		$chemical_text_field = '<input class="rounded" type="text" id="chemicalproducts_1_QSearch" autocomplete="off"size="40px"/>';
		$chemical_hidden_field = ' <input type="hidden" id="chemicalproducts_1_id" name="contacthst[chemical]" value="'.$contacthst['chemical'].'"/>';
		$chemical_div_result = '<div id="searchQuickResults_chemicalproducts_1" class="searchQuickResults" style="display:none;"></div>';

		eval("\$reportcommunication_section = \"".$template->get('sourcing_potentialsupplierprofile_reportcommunication')."\";");
	}
	/* New  Communication Report after the user has initiated contact - END */

	eval("\$supplierprofile = \"".$template->get('sourcing_potentialsupplierprofile')."\";");
	output_page($supplierprofile);
}
else {
	if($core->input['action'] == 'do_contactsupplier') {
		$supplier_id = $db->escape_string($core->input['supplierid']);
		$potential_supplier->contact_supplier($supplier_id);

		redirect(DOMAIN.'/index.php?module = sourcing/supplierprofile&amp;
			id = '.$supplier_id);
	}
	elseif($core->input['action'] == 'do_savecommunication' || $core->input['action'] == 'do_updateprevcommunication') {

		if($core->input['action'] == 'do_updateprevcommunication') {
			$options['operationtype'] = 'update';
		}
		else {
			$options['operationtype'] = 'add';
		}
		$identifier = $core->input['contacthst']['identifier'];
		$newsupplierid = $core->input['contacthst']['ssid'];
		$potential_supplier = new Sourcing($core->input['id']);
		$potential_supplier->save_communication_report($core->input['contacthst'], $newsupplierid, $identifier, $options);

		if(isset($core->input['contacthst']['orderpassed']) && $core->input['contacthst']['orderpassed'] == 1) {
			$potential_supplier = new Sourcing($newsupplierid);
			$potential_supplier->register_supplier($core->input['contacthst']['affid']);
		}

		switch($potential_supplier->get_status()) {
			case 0:
				output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
				break;
			case 1:
				output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
				break;
			case 7:
				output_xml("<status>true</status><message>{$lang->successfullyupdate}</message>");
				break;
			case 6:
				output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
				break;
		}
	}
	elseif($core->input['action'] == 'preview') {
		$rpid = $db->escape_string($core->input['rpid']);
		$supplier_id = $db->escape_string($core->input['sid']);
		$contact = $potential_supplier->get_supplier_contact_persons($supplier_id);
		echo '<div style = "min-width:400px; max-width:600px;">
			<div style = "display:inline-block;width:180px;">'.$contact[$rpid]['name'].'<br /><a href = "mailto:'.$contact[$rpid]['email'].'">'.$contact[$rpid]['email'].'</a><br />'.$contact[$rpid]['phone'].'<br /><br />'.'<strong>'.$lang->repnotes.': </strong>'.$contact[$rpid]['notes'].'</div></div>   ';
	}
}
?>