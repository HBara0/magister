<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: generate.php
 * Created:        @tony.assaad    Aug 13, 2013 | 12:09:56 PM
 * Last Update:    @tony.assaad    Aug 13, 2013 | 12:09:56 PM
 */

if(!defined("DIRECT_ACCESS")) {
	die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canUseBudgeting'] == 0) {
	error($lang->sectionnopermission);
}

$session->start_phpsession();

if(!$core->input['action']) {
	$identifier = base64_decode($core->input['identifier']);
	$budget_data = unserialize($session->get_phpsession('budgetmetadata_'.$identifier));

	if($core->usergroup['canViewAllAff'] == 0) {
		$inaffiliates = implode(',', $core->user['affiliates']);
		$affiliate_where = " affid IN ({$inaffiliates})";
	}

	$affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, "{$affiliate_where}");
	$affiliated_budget = parse_selectlist('budget[affilliates][]', 1, $affiliates, $core->user['mainaffiliate'], 1, '', array('id' => 'affid'));

	if($core->usergroup['canViewAllSupp'] == 0) {
		$insupplier = implode(',', $core->user['suppliers']['eid']);
		$supplier_where = " eid IN ({$insupplier})";
	}
	else {
		$supplier_where = " type='s'";
	}
	$suppliers = get_specificdata('entities', array('eid', 'companyName'), 'eid', 'companyName', array('by' => 'companyName', 'sort' => 'ASC'), 1, "{$supplier_where}");
	$budget_supplierslist = parse_selectlist('budget[suppliers][]', 2, $suppliers, $core->user['suppliers']['eid'], 1, '', array('id' => 'spid'));

	$user = new Users($core->user['uid']);
	$user_segments = $user->get_segments();
	$reporting_touser = $user->get_reportingto();
	if(is_array($user_segments)) {
		$budget_segment.='<select name="budget[segments][]" multiple="multiple" tabindex="4">';
		foreach($user_segments as $segment) {
			$budget_segment .='<option value='.$segment['psid'].'>'.$segment['title'].'</option>';
		}
		$budget_segment.='</select>';
	}
	else {
		$budget_segment.=$lang->na;
	}
	$years = Budgets::get_availableyears();

	$budget_year_selectlist = parse_selectlist('budget[years][]', 4, $years, date('Y')+1, 1, '', array('id' => 'year'));

	//$years = array_combine(range(date("Y") + 1, date("Y") - 3), range(date("Y") + 1, date("Y") - 3));
//	foreach($years as $year) {
//		$year_selected = '';
//		if($year == $years[date("Y")]) {
//			$year_selected = "selected=selected";
//		}
//		$budget_year .= "<option value='{$year}'{$year_selected}>{$year}</option>";
//	}
	$affiliate = new Affiliates($core->user['mainaffiliate']);
	$affiliate_currency = $affiliate->get_country()->get()['mainCurrency'];
	$currencies = get_specificdata('currencies', array('numCode', 'alphaCode'), 'numCode', 'alphaCode', array('by' => 'alphaCode', 'sort' => 'ASC'), 1, 'numCode='.$affiliate_currency);

	if(is_array($currencies)) {
		$budget_currencylist = parse_selectlist('budget[currency]', 6, $currencies, $affiliate_currency, '', '', array('id' => 'currency'));
	}
	/* Can Generate users of the affiliates he belongs to */


	if(is_array($core->user['auditedaffids'])) {
		foreach($core->user['auditedaffids'] as $auditaffid) {
			$aff_obj = new Affiliates($auditaffid);
			$business_managers = $aff_obj->get_users();
			foreach($business_managers as $business_manager) {
				$business_managerslist.= "<option value='{$business_manager['uid']}'>{$business_manager['displayName']}</option>";
			}
		}
	}
	else {
		$business_managerslist .= "<option value='{$core->user['uid']}'>{$core->user['displayName']}</option>";
	}

//	if($core->usergroup['canViewaffBudget'] == 1) {
//		$business_managers = $user->get_affiliateuser();
//		//get budget users for businesmanager join with usres where uid in $business_managers
//		foreach($business_managers as $business_manager) {
//			$business_managerslist.= "<option value='{$business_manager['uid']}'>{$business_manager['displayName']}</option>";
//		}
//	}
//	elseif($core->usergroup['canViewusersBudget'] == 1) {
//		foreach($reporting_touser as $user) {
//			$business_managerslist .= "<option value='{$user['uid']}'>{$user['displayName']}</option>";
//		}
//	}
	/* Generate his own Budget */
//	else {
//		$business_managerslist .= "<option value='{$core->user['uid']}'>{$core->user['displayName']}</option>";
//	}

	eval("\$budgetgenerate = \"".$template->get('budgeting_generate')."\";");
	output_page($budgetgenerate);
}
?>
