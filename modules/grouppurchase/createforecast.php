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
    // error($lang->sectionnopermission);
}

if(!$core->input['action']) {
    $affiliate_where = ' name LIKE "%orkila%" AND isActive=1';
    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliate_where .= " AND affid IN ({$inaffiliates})";
    }

    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, "{$affiliate_where}");

    $gp_affiliate = parse_selectlist('forecast[affid]', 1, $affiliates, '', '', '', array('id' => 'affid', 'required' => true));

    if($core->usergroup['canViewAllSupp'] == 0) {
        $insupplier = implode(',', $core->user['suppliers']['eid']);
        $supplier_where = " eid IN ({$insupplier})";
    }
    else {
        $supplier_where = " type='s'";
    }
    $supplier_where .= ' AND approved=1 AND isActive=1';
    // $suppliers = get_specificdata('entities', array('eid', 'companyName'), 'eid', 'companyName', array('by' => 'companyName', 'sort' => 'ASC'), 1, "{$supplier_where}");
    $gp_supplierslist = "<select name=forecast[spid] id=spid ><option value='0'>&nbsp;</option></select>";

    $years = array_combine(range(date('Y'), date('Y') + 1), range(date('Y'), date('Y') + 1));
    foreach($years as $year) {
        $year_selected = '';
        if($year == $years[date('Y')] + 1) {
            $year_selected = ' selected="selected"';
        }
        $forecast_year .= "<option value='{$year}'{$year_selected}>{$year}</option>";
    }

    eval("\$createforecast = \"".$template->get('grouppurchase_createforecast')."\";");
    output_page($createforecast);
}

if($core->input['action'] == 'get_supplierslist') {
    $affid = $db->escape_string($core->input['id']);
    $affiliate = new Affiliates($affid);

    $gp_suppliers = $affiliate->get_suppliers();

    $gp_supplierslist = '<option value="0"></option>';
    if(is_array($gp_suppliers)) {
        foreach($gp_suppliers as $supplier) {
            $gp_supplierslist .= '<option value="'.$supplier['eid'].'">'.$supplier['companyName'].'</option>';
        }
        output($gp_supplierslist);
    }
}
elseif($core->input['action'] == 'get_years') {
    /* implementing years restricitons */
    $next_year = date('Y') + 1;
    $gp_year = "<option value='{$next_year}' selected='selected'>{$next_year}</option>";
    output($gp_year);
}