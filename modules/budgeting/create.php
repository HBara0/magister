<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: create.php
 * Created:        @tony.assaad    Aug 13, 2013 | 12:09:50 PM
 * Last Update:    @tony.assaad    Aug 13, 2013 | 12:09:50 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canUseBudgeting'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {
    $sessionidentifier = md5(uniqid(microtime()));
    $session->name_phpsession(COOKIE_PREFIX.'fillbudget'.$sessionidentifier);
    $session->start_phpsession(480);

    $affiliate_where = ' name LIKE "%orkila%" AND isActive=1';
    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliate_where .= " AND affid IN ({$inaffiliates})";
    }

    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, "{$affiliate_where}");
    $affiliated_budget = parse_selectlist('budget[affid]', 1, $affiliates, '', '', '', array('id' => 'affid'));

    if($core->usergroup['canViewAllSupp'] == 0) {
        if(is_array($core->user['suppliers']['eid'])) {
            $insupplier = implode(',', $core->user['suppliers']['eid']);
            $supplier_where = " eid IN ({$insupplier}) AND";
        }
    }
    else {
        $supplier_where = " type='s' AND";
    }

    $supplier_where .= ' approved=1 AND isActive=1';
    $suppliers = get_specificdata('entities', array('eid', 'companyName'), 'eid', 'companyName', array('by' => 'companyName', 'sort' => 'ASC'), 1, "{$supplier_where}");
    $budget_supplierslist = "<select name=budget[spid] id=spid ><option value='0'>&nbsp;</option></select>";

    $years = array_combine(range(date('Y') + 1, date('Y') + 1), range(date('Y') + 1, date('Y') + 1));

    foreach($years as $year) {
        $year_selected = '';
        if($year == $years[date("Y")] + 1) {
            $year_selected = "selected=selected";
        }
        $budget_year .= "<option value='{$year}'{$year_selected}>{$year}</option>";
    }
    //$currencies = get_specificdata('currencies', array('numCode', 'alphaCode'), 'numCode', 'alphaCode', array('by' => 'alphaCode', 'sort' => 'ASC'), 1);
    $affiliate = new Affiliates($core->user['mainaffiliate']);
    $affiliate_currency = $affiliate->get_country()->get()['mainCurrency'];
    $budget_currencylist = parse_selectlist('budget[currency]', 1, array(), $affiliate_currency, '', '', array('id' => 'currency'));

    eval("\$budgetcreate = \"".$template->get('budgeting_createbudget')."\";");
    output_page($budgetcreate);
}
else {
    if($core->input['action'] == 'get_supplierslist') {
        $affid = $db->escape_string($core->input['id']);
        $affiliate = new Affiliates($affid);

        $budget_suppliers = $affiliate->get_suppliers();

        $budget_supplierslist = '<option value="0"></option>';
        if(is_array($budget_suppliers)) {
            foreach($budget_suppliers as $supplier) {
                $budget_supplierslist .= '<option value="'.$supplier['eid'].'">'.$supplier['companyName'].'</option>';
            }
            output($budget_supplierslist);
        }
    }
    elseif($core->input['action'] == 'get_currencylist') {
        $affid = $db->escape_string($core->input['affid']);
        $affiliate = new Affiliates($affid);
        $affiliate_currency = $affiliate->get_country()->get()['mainCurrency'];
        $currencies = get_specificdata('currencies', array('numCode', 'alphaCode'), 'numCode', 'alphaCode', array('by' => 'alphaCode', 'sort' => 'ASC'), 1, 'numCode='.$affiliate_currency);

        if(is_array($currencies)) {
            echo $budget_currencylist.= "<option value=''></option> <option value='{$currencies[$affiliate_currency]}'>{$currencies[$affiliate_currency]}</option>";
        }
    }
    elseif($core->input['action'] == 'get_years') {
        $affid = intval($core->input['affid']);
        $spid = intval($core->input['spid']);
        /* implementing years restricitons */
        $budget = new Budgets();
        $budget_years = $budget->populate_budgetyears(array('affid' => $affid, 'spid' => $spid));
        $year_selected = '';
        if(is_array($budget_years)) {
            foreach($budget_years as $year) {
                if($year == date('Y') + 1) {
                    $year_selected = ' selected="selected"';
                }
                else {
                    if($year < date('Y') - 1) {
                        continue;
                    }
                }
                $budget_year .= "<option value='{$year}'".$year_selected.">{$year}</option>";
            }
        }
        if(is_array($budget_years)) {
            if(!in_array(date('Y'), $budget_years)) {
                $budget_year .= "<option value='".(date('Y'))."'>".(date('Y'))."</option>";
            }
        }

        if(empty($year_selected)) {
            $next_year = date('Y') + 1;
            $budget_year .= "<option value='{$next_year}' selected='selected'>{$next_year}</option>";
        }

        output($budget_year);
    }
}
?>