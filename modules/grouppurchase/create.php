<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: create.php
 * Created:        @rasha.aboushakra    Dec 15, 2014 | 11:58:10 AM
 * Last Update:    @rasha.aboushakra    Dec 15, 2014 | 11:58:10 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['grouppurchase_canUpdateForecast'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {
    $affiliate_where = ' name LIKE "%orkila%" AND isActive=1';
    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliate_where .= " AND affid IN ({$inaffiliates})";
    }

    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, "{$affiliate_where}");
    $affiliated_budget = parse_selectlist('forecast[affid]', 1, $affiliates, '', '', '', array('id' => 'affid', 'required' => true));

    if($core->usergroup['canViewAllSupp'] == 0) {
        $insupplier = implode(',', $core->user['suppliers']['eid']);
        $supplier_where = " eid IN ({$insupplier})";
    }
    else {
        $supplier_where = " type='s'";
    }
    $supplier_where .= ' AND approved=1 AND isActive=1';
    $suppliers = get_specificdata('entities', array('eid', 'companyName'), 'eid', 'companyName', array('by' => 'companyName', 'sort' => 'ASC'), 1, "{$supplier_where}");
    $budget_supplierslist = parse_selectlist('forecast[spid]', '', $suppliers, 0, '', '', array('id' => 'spid', 'required' => true));

    $years = array_combine(range(date('Y'), date('Y') + 1), range(date('Y'), date('Y') + 1));
    foreach($years as $year) {
        $year_selected = '';
        if($year == $years[date('Y')] + 1) {
            $year_selected = ' selected="selected"';
        }
        $forecast_year .= "<option value='{$year}'{$year_selected}>{$year}</option>";
    }

    eval("\$createforecast = \"".$template->get('grouppurchase_create')."\";");
    output_page($createforecast);
}