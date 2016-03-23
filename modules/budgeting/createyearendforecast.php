<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: createyearendforecast.php
 * Created:        @hussein.barakat    Sep 8, 2015 | 12:50:13 PM
 * Last Update:    @hussein.barakat    Sep 8, 2015 | 12:50:13 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canUseBudgeting'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {

    $affiliate_where = ' name LIKE "%orkila%" AND isActive=1';
    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliate_where .= " AND affid IN ({$inaffiliates})";
    }

    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, "{$affiliate_where}");
    $affiliated_budget = parse_selectlist('yef[affid]', 1, $affiliates, '', '', '', array('id' => 'affid'));

    if($core->usergroup['canViewAllSupp'] == 0) {
        if(is_array($core->user['suppliers']['eid'])) {
            $insupplier = implode(',', $core->user['suppliers']['eid']);
            $supplier_where = " eid IN ({$insupplier})";
        }
        if(empty($supplier_where)) {
            $supplier_where = " eid = 0 ";
        }
    }
    else {
        $supplier_where = " type='s'";
    }

    $supplier_where .= ' AND approved=1 AND isActive=1';
    $suppliers = get_specificdata('entities', array('eid', 'companyName'), 'eid', 'companyName', array('by' => 'companyName', 'sort' => 'ASC'), 1, "{$supplier_where}");
    $budget_supplierslist = "<select name=yef[spid] id=spid ><option value='0'>&nbsp;</option></select>";

    $years = array_combine(range(date('Y'), date('Y')), range(date('Y'), date('Y')));

    foreach($years as $year) {
        $year_selected = '';
        if($year == $years[date("Y")]) {
            $year_selected = "selected=selected";
        }
        $budget_year .= "<option value='{$year}'{$year_selected}>{$year}</option>";
    }
    //$currencies = get_specificdata('currencies', array('numCode', 'alphaCode'), 'numCode', 'alphaCode', array('by' => 'alphaCode', 'sort' => 'ASC'), 1);
    $affiliate = new Affiliates($core->user['mainaffiliate']);
    $affiliate_currency = $affiliate->get_country()->get()['mainCurrency'];
    $budget_currencylist = parse_selectlist('yef[currency]', 1, array(), $affiliate_currency, '', '', array('id' => 'currency'));

    eval("\$yefcreate = \"".$template->get('budgeting_createyefbudget')."\";");
    $additionalheaderinc = '<script src="{$core->settings[rootdir]}/js/fillreport.js" type="text/javascript"></script>';
    output_page($yefcreate);
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
        $affid = $db->escape_string($core->input['affid']);
        $spid = $db->escape_string($core->input['spid']);
        /* implementing years restricitons */
        $yef = new BudgetingYearEndForecast;
        $budget_years = array(date('Y'), date('Y') - 1);
        $year_selected = '';
        if(is_array($budget_years)) {
            foreach($budget_years as $year) {
                $year_selected = '';
                if($year == date('Y')) {
                    $year_selected = ' selected="selected"';
                }
                $budget_year .= "<option value='{$year}'".$year_selected.">{$year}</option>";
            }
        }

        output($budget_year);
    }
}
?>