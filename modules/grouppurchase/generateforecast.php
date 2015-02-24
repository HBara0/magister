<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: generate.php
 * Created:        @rasha.aboushakra    Dec 17, 2014 | 9:14:24 AM
 * Last Update:    @rasha.aboushakra    Dec 17, 2014 | 9:14:24 AM
 */

if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['grouppurchase_canGenerateReports'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {
    $affiliate_where = ' name LIKE "%orkila%"';
    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliate_where .= " AND affid IN ({$inaffiliates})";
    }

    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, "{$affiliate_where}");
    if(is_array($affiliates)) {
        foreach($affiliates as $key => $value) {
            if($key == 0) {
                continue;
            }
            $checked = $rowclass = '';
            $affiliates_list .='<tr class="'.$rowclass.'">';
            $affiliates_list .='<td><input id="affiliatefilter_check_'.$key.'" name="forecast[affiliates][]"  type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td></tr>';
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

    if(is_array($suppliers)) {
        foreach($suppliers as $key => $value) {
            if($key == 0) {
                continue;
            }
            $checked = $rowclass = '';
            $suppliers_list .= ' <tr class="'.$rowclass.'">';
            $suppliers_list .= '<td><input id="supplierfilter_check_'.$key.'" name="forecast[suppliers][]" type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td><tr>';
        }
    }

    $years = array_combine(range(date('Y') + 1, date('Y')), range(date('Y') + 1, date('Y')));
    if(is_array($years)) {
        foreach($years as $key => $value) {
            if($key == 0) {
                continue;
            }
            $checked = $rowclass = '';
            $forecast_year_list .= ' <tr class="'.$rowclass.'">';
            $forecast_year_list .= '<td><input id="yearsfilter_check_'.$key.'" name="forecast[years][]" type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td><tr>';
        }
    }
//    if(is_array($years)) {
//        foreach($years as $key => $value) {
//            $checked = $rowclass = '';
//            $forecast_year_list .= '<tr class="'.$rowclass.'">';
//            $forecast_year_list .= '<td><input name="forecast[years]"  required="required" type="radio" value="'.$key.'">'.$value.'</td></tr>';
//        }
//    }

    $dimensions = array('affid' => $lang->affiliate, 'spid' => $lang->supplier, 'pid' => $lang->product, 'saleType' => $lang->saletype, 'businessMgr' => $lang->bm, 'psid' => $lang->segment, 'year' => $lang->year);
    foreach($dimensions as $dimensionid => $dimension) {
        $dimension_item.='<li class="ui-state-default" id='.$dimensionid.' title="Click and Hold to move the '.$dimension.'">'.$dimension.'</li>';
    }

    eval("\$gpforecastgenerate = \"".$template->get('grouppurchase_generateforecast')."\";");
    output_page($gpforecastgenerate);
}
?>