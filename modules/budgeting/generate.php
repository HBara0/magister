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
    $user_obj = new Users($core->user['uid']);
    $permissions = $user_obj->get_businesspermissions();

    $affiliate_where = 'isActive =1';
    if(is_array($permissions['affid'])) {
        $affiliate_where .= " AND affid IN (".implode(',', $permissions['affid']).")";
    }
    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, $affiliate_where);

    if(is_array($affiliates)) {
        foreach($affiliates as $key => $value) {
            if($key == 0) {
                continue;
            }
            $checked = $rowclass = '';
            $affiliates_list .='<tr class="'.$rowclass.'">';
            $affiliates_list .='<td><input id="affiliatefilter_check_'.$key.'" name="budget[affid][]"  type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td></tr>';
        }
    }




    $supplier_where = 'type = "s"';
    if(is_array($permissions['spid'])) {
        $supplier_where = "  eid IN (".implode(',', $permissions['spid']).")";
    }
    $suppliers = get_specificdata('entities', array('eid', 'companyName'), 'eid', 'companyName', array('by' => 'companyName', 'sort' => 'ASC'), 1, $supplier_where);

    if(is_array($suppliers)) {
        foreach($suppliers as $key => $value) {
            if($key == 0) {
                continue;
            }
            $checked = $rowclass = '';
            $suppliers_list .= ' <tr class="'.$rowclass.'">';
            $suppliers_list .= '<td><input id="supplierfilter_check_'.$key.'" name="budget[spid][]" type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td><tr>';
        }
    }

    $user = new Users($core->user['uid']);
    $user_segments_objs = $user->get_segments();
    if(is_array($user_segments_objs)) {
        foreach($user_segments_objs as $user_segments_obj) {
            $user_segments[$user_segments_obj->get()['psid']] = $user_segments_obj->get();
        }
    }
    if(is_array($user_segments)) {
        foreach($user_segments as $segment) {
            $checked = $rowclass = '';
            $budget_segments_list .='<tr class="'.$rowclass.'">';
            $budget_segments_list .='<td><input id="segmentfilter_check_'.$segment['psid'].'"  name="budget[psid][]" type="checkbox"'.$checked.' value="'.$segment['psid'].'">'.$segment['title'].'</td></tr>';
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
        }
    }


    $users_where = 'gid != 7';
    if($core->usergroup['canViewAllEmp'] == 0 && is_array($permissions['uid'])) {
        $users_where .= ' AND uid IN ('.implode(',', $permissions['uid']).')';
    }
    $bmanagers = get_specificdata('users', array('uid', 'displayName'), 'uid', 'displayName', array('by' => 'displayName', 'sort' => 'ASC'), 1, $users_where);
    if(is_array($bmanagers)) {
        foreach($bmanagers as $key => $value) {
            $checked = $rowclass = '';
            $business_managerslist .= '<tr class="'.$rowclass.'">';
            $business_managerslist .= '<td><input id="bmfilter_check_'.$key.'" name="budget[uid][]" type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td></tr>';
        }
    }

    $currency['filter']['numCode'] = 'SELECT mainCurrency FROM '.Tprefix.'countries WHERE affid IS NOT NULL';
    $curr_objs = Currencies::get_data($currency['filter'], array('returnarray' => true, 'operators' => array('numCode' => 'IN')));
    $curr_objs[840] = new Currencies(840);
    $currencies_list = parse_selectlist('budget[toCurrency]', 7, $curr_objs, 840);

    $dimensions = array('affid' => $lang->affiliate, 'spid' => $lang->supplier, 'cid' => $lang->customer, 'reportsTo' => $lang->reportsto, 'pid' => $lang->product, 'coid' => $lang->country, 'uid' => $lang->manager, 'psid' => $lang->segment, 'stid' => $lang->saletype);

    foreach($dimensions as $dimensionid => $dimension) {
        $dimension_item.='<li class = "ui-state-default" id = '.$dimensionid.' title = "Click and Hold to move the '.$dimension.'">'.$dimension.'</li>';
    }

    eval("\$budgetgenerate = \"".$template->get('budgeting_generate')."\";");
    output_page($budgetgenerate);
}
?>
