<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Entity profile
 * $module: profiles
 * $id: entityprofile.php
 * Created:			@najwa.kassem		October 11, 2010 | 10:28 AM
 * Last Update: 	@zaher.reda 		September 12, 2011 | 05:15 PM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
	if(!isset($core->input['affid'])) {
		redirect($_SERVER['HTTP_REFERER']);
	}

	$affid = $db->escape_string($core->input['affid']);
	if($core->usergroup['profiles_canAddMkIntlData'] == 1) {
		$addmarketdata_link = '<div style="float:right;margin:-10px;" title="'.$lang->addmarket.'" ><a href="#" id="showpopup_profilesmarketdata" class="showpopup"><img  alt="'.$lang->addmarket.'" src="'.$core->settings['rootdir'].'/images/icons/marketintelligence.png" width="44px;" height="44px;"/></a></div>';
		$field = '<input type="text" required="required" name="eid" id="customer_0_QSearch" value="" autocomplete="off"/>
                    <input type="hidden"  id="customer_0_id" name="marketdata[cid]" />
					<div id="searchQuickResults_0" class="searchQuickResults" style="display:none;"></div>';
		$product_field = '<tr><td>'.$lang->product.'</td>
                <td><input type="text" required="required" size="25" name="marketdata[cfpid]" id="chemfunctionproducts_1_QSearch" size="100"  autocomplete="off"/>
                    <input type="hidden"  id="chemfunctionproducts_1_id" name="marketdata[cfpid]" /> 
                    <input type="hidden" value="1" id="userproducts" name="userproducts" /> 
                    <div id="searchQuickResults_1" class="searchQuickResults" style="display:none;"></div></td></tr>';
		$module = 'profiles';
		$hideselect = ' style="display:none;"';
		$elemtentid = $affid;
		$lang->fieldlabel = $lang->customer;
		$action = 'do_addmartkerdata';
		$modulefile = 'affiliateprofile';
	}
	$rowid = intval($core->input['value']) + 2;
	$query = $db->query("SELECT * FROM ".Tprefix."affiliates a LEFT JOIN ".Tprefix."workshifts ws ON (a.defaultWorkshift=ws.wsid) WHERE affid={$affid}");

	while($profile = $db->fetch_assoc($query)) {
		if(!empty($profile['addressLine1'])) {
			$profile['fulladdress'] .= $profile['addressLine1'].' ';
		}

		if(!empty($profile['addressLine2'])) {
			$profile['fulladdress'] .= $profile['addressLine2'].', ';
		}

		if(!empty($profile['city'])) {
			$profile['fulladdress'] .= $profile['city'].' - ';
		}

		$profile['fax'] = '+'.$profile['fax'];
		$profile['phone1'] = '+'.$profile['phone1'];
		if(isset($profile['phone2']) && !empty($profile['phone2'])) {
			$profile['phone2'] = '/+'.$profile['phone2'];
		}

		$management_query = $db->query("SELECT uid, CONCAT(firstName, ' ', lastName) AS generalManager FROM ".Tprefix."users WHERE uid IN ({$profile['supervisor']},{$profile['generalManager']},{$profile['hrManager']})");
		while($management = $db->fetch_array($management_query)) {
			$managers[$management['uid']] = $management['generalManager'];
		}

		if($profile['generalManager'] == 0) {
			$gm = $lang->na;
		}
		else {
			$gm = "<a href='./users.php?action=profile&uid={$profile['generalManager']}' target='_blank'>".$managers[$profile['generalManager']]."</a>";
		}

		if($profile['supervisor'] == 0) {
			$supervisor = $lang->na;
		}
		else {
			$supervisor = "<a href='./users.php?action=profile&uid={$profile['supervisor']}' target='_blank'>".$managers[$profile['supervisor']]."</a>";
		}

		if($profile['hrManager'] == 0) {
			$hr = $lang->na;
		}
		else {
			$hr = "<a href='./users.php?action=profile&uid={$profile['hrManager']}' target='_blank'>".$managers[$profile['hrManager']]."</a>";
		}

		/* Parse default workshift - START */
		if(!empty($profile['weekDays'])) {
			$profile['weekDays'] = unserialize($profile['weekDays']);
			if(is_array($profile['weekDays'])) {
				foreach($profile['weekDays'] as $day) {
					$profile['weekDays_output'] .= $comma.get_day_name($day, 'letters');
					$comma = ', ';
				}
				$profile['workshift'] = $profile['onDutyHour'].':'.$profile['onDutyMinutes'].' - '.$profile['offDutyHour'].':'.$profile['offDutyMinutes'].' ('.$profile['weekDays_output'].')';
			}
		}
		/* Parse default workshift - END */

		foreach($profile as $key => $val) {
			if(empty($val)) {
				$profile[$key] = $lang->na;
			}
		}

		$countries_query = $db->query("SELECT coid, name FROM ".Tprefix."countries WHERE affid={$affid} ORDER BY name");
		while($countries = $db->fetch_array($countries_query)) {
			$countrieslist[$countries['coid']] = $countries['name'];
		}
		$profile['fulladdress'] .= $countrieslist[$profile['country']];
		$countries_list = implode(', ', $countrieslist);

		$suppliers_query = $db->query(" SELECT *
							FROM ".Tprefix."affiliatedentities a LEFT JOIN ".Tprefix."entities e ON (a.eid=e.eid)
							WHERE a.affid={$affid} AND e.type='s'
							ORDER BY e.companyName ASC");

		$suppliers_counter = $customers_counter = $affiliateemployees_counter = 0;
		$user_mainaff = $db->fetch_field($db->query("SELECT affid FROM ".Tprefix."affiliatedemployees WHERE uid={$core->user['uid']} AND isMain=1"), 'affid');

		while($supplier = $db->fetch_array($suppliers_query)) {
			$listitem['link'] = 'index.php?module=profiles/entityprofile&eid='.$supplier['eid'];
			$listitem['title'] = $supplier['companyName'];
			$listitem['divhref'] = 'supplier';
			$listitem['loadiconid'] = 'loadentityusers_'.$supplier['eid'].'_'.$affid;

			if(++$suppliers_counter > 3) {
				eval("\$hidden_suppliers .= \"".$template->get('profiles_affliatesentities_inlinelistitem')."\";");
			}
			else {
				eval("\$shown_suppliers .= \"".$template->get('profiles_affliatesentities_inlinelistitem')."\";");
			}
		}

		if($suppliers_counter > 3) {
			$supplierslist = $shown_suppliers." <a href='#suppliers' id='showmore_suppliers_{$supplier[eid]}' class='smalltext'><img src='{$core->settings[rootdir]}/images/add.gif' alt='{$lang->edit}' border='0' /></a> <br /><span style='display:none;' id='suppliers_{$supplier[eid]}'>{$hidden_suppliers}</span>";
		}
		else {
			$supplierslist = "<ul style='list-style:none; padding:2px;'>".$shown_suppliers."</ul>";
		}

		$affiliateemployees_query = $db->query("SELECT *, CONCAT(firstName, ' ', lastName) AS fullname
							FROM ".Tprefix."assignedemployees e RIGHT JOIN ".Tprefix."users u ON (e.uid=u.uid) JOIN ".Tprefix."affiliatedemployees ae ON (ae.uid=u.uid)
							WHERE ae.affid={$affid} AND u.gid!=7 AND ae.isMain=1
							GROUP BY u.username
							ORDER BY u.firstName ASC");
		while($affililateemployees = $db->fetch_array($affiliateemployees_query)) {
			if(++$affiliateemployees_counter > 100) {
				$hidden_affililateemployees .= "<li><a href='./users.php?action=profile&uid={$affililateemployees[uid]}' target='_blank'>{$affililateemployees[fullname]}</a></li>";
			}
			elseif($affiliateemployees_counter == 100) {
				$shown_affililateemployees .= "<li><a href='./users.php?action=profile&uid={$affililateemployees[uid]}' target='_blank'>{$affililateemployees[fullname]}</a>";
			}
			else {
				$shown_affililateemployees .= "<li><a href='./users.php?action=profile&uid={$affililateemployees[uid]}' target='_blank'>{$affililateemployees[fullname]}</a></li>";
			}

			if(!empty($affililateemployees['internalExtension'])) {
				$rowclass = alt_row($rowclass);
				$extensions.= '<tr class="'.$rowclass.'"><td>'.$affililateemployees['fullname'].'</td><td>'.$affililateemployees['internalExtension'].'</td></tr>';
			}
		}

		if($affiliateemployees_counter > 100) {
			$supplierallusers = "<ul style='list-style:none; padding:2px;margin-top:0px;'>".$shown_affililateemployees.", <a href='#affililateemployees' id='showmore_affililateemployees_{$affililateemployees[uid]}' class='smalltext'>read more</a></li> <span style='display:none;' id='affililateemployees_{$affililateemployees[uid]}'>{$hidden_affililateemployees}</span></ul>";
		}
		else {
			$supplierallusers = "<ul style='list-style:none; padding:2px;margin-top:0px;'>".$shown_affililateemployees."</li></ul>";
		}

		if($user_mainaff == $affid) {
			$customers_query = $db->query("SELECT *
								FROM ".Tprefix."affiliatedentities a LEFT  JOIN ".Tprefix."entities e ON (a.eid=e.eid) JOIN ".Tprefix."assignedemployees ae ON (ae.eid=a.eid)
								WHERE a.affid={$affid} AND e.type='c' AND ae.uid={$core->user['uid']}
								GROUP BY e.companyName
								ORDER BY e.companyName ASC");
			if($db->num_rows($customers_query) > 0) {
				while($customer = $db->fetch_array($customers_query)) {
					if(++$customers_counter > 3) {
						$hidden_customers .= "<li><a href='index.php?module=profiles/entityprofile&eid={$customer[eid]}' target='_blank'>{$customer['companyName']}</a></li>";
					}
					elseif($customers_counter == 3) {
						$shown_customers .= "<li><a href='index.php?module=profiles/entityprofile&eid={$customer[eid]}' target='_blank'>{$customer['companyName']}</a>";
					}
					else {
						$shown_customers .= "<li><a href='index.php?module=profiles/entityprofile&eid={$customer[eid]}' target='_blank'>{$customer['companyName']}</a></li>";
					}
				}

				if($customers_counter > 3) {
					$customerslist = "<ul style='list-style:none; padding:2px;margin-top:0px;'>".$shown_customers.", <a href='#customers' id='showmore_customers_{$customer[eid]}' class='smalltext'>read more</a> </li><span style='display:none;' id='customers_{$customer[eid]}'>{$hidden_customers}</span></ul>";
				}
				else {
					$customerslist = '<ul style="list-style:none; padding:2px;margin-top:0px;">'.$shown_customers.'</ul>';
				}
			}

			$report_query = $db->query("SELECT *, e.companyName AS supplier_name
										FROM ".Tprefix." reports r LEFT JOIN ".Tprefix."entities e ON (r.spid=e.eid) JOIN ".Tprefix."assignedemployees ae ON (ae.eid=r.spid)
										WHERE r.affid={$affid} AND r.type='q'
										GROUP BY r.rid
										ORDER BY finishDate DESC
										LIMIT 0, 4");

			$reports_counter = 0;
			while($reports = $db->fetch_array($report_query)) {
				if(++$reports_counter < 3) {
					$shown_reports .= "<li><a href='index.php?module=reporting/preview&referrer=list&rid={$reports[rid]}' target='_blank'> Q{$reports['quarter']} / {$reports['year']} - {$reports['supplier_name']}</a></li>";
				}
				elseif($reports_counter == 3) {
					$shown_reports .= "<li><a href='index.php?module=reporting/preview&referrer=list&rid={$reports[rid]}' target='_blank'> Q{$reports['quarter']} / {$reports['year']} - {$reports['supplier_name']}</a>";
				}
				else {
					break;
				}
			}

			if($reports_counter > 3) {
				$reports_list = "<ul style='list-style:none; padding:2px;margin-top:0px;'>".$shown_reports.", <a href='index.php?module=reporting/list&filterby=affid&filtervalue={$affid}' target='_blank' class='smalltext'>read more</a></li></ul>";
			}
			else {
				$reports_list = "<ul style='list-style:none; padding:2px;margin-top:0px;'>".$shown_reports."</li></ul>";
			}
			eval("\$private_section = \"".$template->get('profiles_affiliateprofile_privatesection')."\";");
		}
		eval("\$popup_marketdata= \"".$template->get('popup_profiles_marketdata')."\";");
		eval("\$profilepage = \"".$template->get('profiles_affiliateprofile')."\";");
	}

	output_page($profilepage);
}
else {
	if($core->input['action'] == 'getentityusers' || $core->input['action'] == 'getallusers') {
		if($core->input['action'] == 'getentityusers') {
			$query_string = " AND e.eid = '".$db->escape_string($core->input['eid'])."'";
		}

		$affid = $db->escape_string($core->input['affid']);

		$entityusers_query = $db->query("SELECT *, CONCAT(firstName, ' ', lastName) AS fullname
							FROM ".Tprefix."assignedemployees e RIGHT JOIN ".Tprefix."users u ON (e.uid=u.uid) JOIN ".Tprefix."affiliatedemployees ae ON (ae.uid=u.uid)
							WHERE (ae.affid={$affid} AND ae.isMain=1){$query_string} AND u.gid!=7
							GROUP BY u.username
							ORDER BY u.firstName ASC");
		if($db->num_rows($entityusers_query) > 0) {
			while($entityusers = $db->fetch_array($entityusers_query)) {
				$entityusers_list .= "<li><a href='./users.php?action=profile&uid={$entityusers[uid]}' target='_blank'>{$entityusers[fullname]}</a></li>";
			}
		}
		else {
			$entityusers_list = $lang->na;
		}
		$entityusers_list_output = "<ul style='list-style:none; padding:2px; margin-top: 0px;'>{$entityusers_list}</ul> ";
		echo $entityusers_list_output;
	}
	elseif($core->input['action'] == 'inlineCheck') {
		$eid = $db->escape_string($core->input['value']);
		$entity_obj = new Entities($eid, '', false);
		$entbrandsproducts_objs = $entity_obj->get_brands();
		if(is_array($entbrandsproducts_objs)) {
			foreach($entbrandsproducts_objs as $entbrandsproducts_obj) {
				$entbrandsproducts = $entbrandsproducts_obj->get();
				//get entitiesbrandsproducts
				$entitiesbrandsproducts_objs = $entbrandsproducts_obj->get_entitybrands();

				foreach($entitiesbrandsproducts_objs as $entitiesbrandsproducts_obj) {
					$entbrandsproducts['ebpid'] = $entitiesbrandsproducts_obj->get()['ebpid'];

					/* get endproduct types */
					$endproducts_objs = $entbrandsproducts_obj->get_producttypes();//Entbrandsproducts::get_endproducts($entbrandsproducts['ebid']);
					foreach($endproducts_objs as $endproducts_obj) {
						$endproduct_types = '';
						$endproduct_types = $endproducts_obj->get()['name'];
					}
					/* get Brands */
					$entitybrand = $entbrandsproducts_obj->get()['name'];
					$entitiesbrandsproducts_data .= '<option value="'.$entbrandsproducts['ebpid'].'">'.$endproduct_types.'-'.$entitybrand.' </option>';
				}
			}
			$entitiesbrandsproducts_list = '<select name="marketdata[ebpid]">'.$entitiesbrandsproducts_data.'</select>';
		}
		output($entitiesbrandsproducts_list);
	}
	elseif($core->input['action'] == 'do_addmartkerdata') {
		$marketin_obj = new Marketintelligence();
		$marketin_obj->create($core->input['marketdata']);
		switch($marketin_obj->get_errorcode()) {
			case 0:
				output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
				break;
			case 1:
				output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
				break;
			case 2:
				output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
				break;
		}
	}
}
?>