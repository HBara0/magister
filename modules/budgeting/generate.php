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

    $affiliate_where = ' name LIKE "orkila%"';
    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliate_where .= " AND affid IN ({$inaffiliates})";
    }


    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, "{$affiliate_where}");
    //$affiliated_budget = parse_selectlist('budget[affiliates][]', 1, $affiliates, $core->user['mainaffiliate'], 1, '', array('id' => 'affid'));
    if(is_array($affiliates)) {

        foreach($affiliates as $key => $value) {
            if($key == 0) {
                continue;
            }
            $checked = $rowclass = '';
            $affiliates_list .='<tr class="'.$rowclass.'">';
            $affiliates_list .='<td><input name="budget[affiliates][]"  type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td></tr>';
        }
    }


    if($core->usergroup['canViewAllSupp'] == 0) {
        $insupplier = implode(',', $core->user['suppliers']['eid']);
        $supplier_where = " eid IN ({$insupplier})";
    }
    else {
        $supplier_where = " type='s'";
    }
    $suppliers = get_specificdata('entities', array('eid', 'companyName'), 'eid', 'companyName', array('by' => 'companyName', 'sort' => 'ASC'), 1, "{$supplier_where}");
    // $budget_supplierslist = parse_selectlist('budget[suppliers][]', 2, $suppliers, $core->user['suppliers']['eid'], 1, '', array('id' => 'spid'));

    if(is_array($suppliers)) {
        foreach($suppliers as $key => $value) {
            if($key == 0) {
                continue;
            }
            $checked = $rowclass = '';
            $suppliers_list .= ' <tr class="'.$rowclass.'">';
            $suppliers_list .= '<td><input name="budget[suppliers][]" type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td><tr>';
        }
    }

    $user = new Users($core->user['uid']);
    $user_segments_objs = $user->get_segments();
    if(is_array($user_segments_objs)) {
        foreach($user_segments_objs as $user_segments_obj) {
            $user_segments[$user_segments_obj->get()['psid']] = $user_segments_obj->get();
        }
    }
    $reporting_touser = $user->get_reportingto();
    if(is_array($user_segments)) {
        foreach($user_segments as $segment) {
            $checked = $rowclass = '';
            $budget_segments_list .='<tr class="'.$rowclass.'">';
            $budget_segments_list .='<td><input name="budget[segments][]" type="checkbox"'.$checked.' value="'.$segment['psid'].'">'.$segment['title'].'</td></tr>';
        }
    }
    else {
        $budget_segment.=$lang->na;
    }
    $years = Budgets::get_availableyears();
    if(is_array($years)) {
        foreach($years as $key => $value) {
            $checked = $rowclass = '';
            $budget_year_list .= '<tr class="'.$rowclass.'">';
            $budget_year_list .= '<td><input name="budget[years]"  required="required" type="radio" value="'.$key.'">'.$value.'</td></tr>';
            // $budget_year_list .= '<td><input name="budget[years][]" type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td></tr>';
        }
    }
    // $budget_year_selectlist = parse_selectlist('budget[years][]', 4, $years, date('Y') + 1, 1, '', array('id' => 'year'));
//$years = array_combine(range(date("Y") + 1, date("Y") - 3), range(date("Y") + 1, date("Y") - 3));
//	foreach($years as $year) {
//		$year_selected = '';
//		if($year == $years[date("Y")]) {
//			$year_selected = "selected=selected";
//		}
//		$budget_year .= "<option value='{$year}'{$year_selected}>{$year}</option>";
//	}
//	$affiliate = new Affiliates($core->user['mainaffiliate']);
//	$affiliate_currency = $affiliate->get_country()->get()['mainCurrency'];
//	$currencies = get_specificdata('currencies', array('numCode', 'alphaCode'), 'numCode', 'alphaCode', array('by' => 'alphaCode', 'sort' => 'ASC'), 1, 'numCode='.$affiliate_currency);
//
//	if(is_array($currencies)) {
//		$budget_currencylist = parse_selectlist('budget[currency]', 6, $currencies, $affiliate_currency, '', '', array('id' => 'currency'));
//	}
    /* Can Generate users of the affiliates he belongs to */

    if(is_array($core->user['auditedaffids'])) {
        foreach($core->user['auditedaffids'] as $auditaffid) {
            $aff_obj = new Affiliates($auditaffid);
            $affiliate_users = $aff_obj->get_users();
            foreach($affiliate_users as $aff_businessmgr) {
                $business_managers[$aff_businessmgr['uid']] = $aff_businessmgr['displayName'];
            }
        }
    }
    else {
        if($core->usergroup['canViewAllEmp'] == 1) {
            $affiliate = new Affiliates($core->user['mainaffiliate']);
            $business_managers = $affiliate->get_users(array('displaynameonly' => true));
        }
        else {
            $business_managers[$core->user['uid']] = $core->user['displayName'];
        }
    }
    if(is_array($business_managers)) {
        foreach($business_managers as $key => $value) {
            $checked = $rowclass = '';
            $business_managerslist .= '<tr class="'.$rowclass.'">';
            $business_managerslist .= '<td><input name="budget[managers][]" type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td></tr>';
        }
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

    /* parse currencies */
    $currency['filter']['numCode'] = 'SELECT mainCurrency FROM countries where affid IS NOT NULL';
    $curr_objs = Currencies::get_data($currency['filter'], array('returnarray' => true, 'operators' => array('numCode' => 'IN')));
    $curr_objs[840] = new Currencies(840);
    //$curr_objs = Currencies::get_data('alphaCode IS NOT NULL');
    $currencies_list = parse_selectlist('budget[toCurrency]', 7, $curr_objs, 840);

    $dimensions = array('affid' => $lang->affiliate, 'spid' => $lang->supplier, 'cid' => $lang->customer, 'reportsTo' => $lang->reportsto, 'pid' => $lang->product, 'coid' => $lang->country, 'uid' => $lang->manager, 'psid' => $lang->segment, 'stid' => $lang->saletype);

    foreach($dimensions as $dimensionid => $dimension) {
        $dimension_item.='<li class = "ui-state-default" id = '.$dimensionid.' title = "Click and Hold to move the '.$dimension.'">'.$dimension.'</li>';
    }

    eval("\$budgetgenerate = \"".$template->get('budgeting_generate')."\";");
    output_page($budgetgenerate);
}
?>
