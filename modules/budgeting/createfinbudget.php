<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: createexpenses.php
 * Created:        @rasha.aboushakra    Sep 25, 2014 | 10:47:14 AM
 * Last Update:    @rasha.aboushakra    Sep 25, 2014 | 10:47:14 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['budgeting_canFillComAdmExp'] == 0) {
    error($lang->sectionnopermission);
}

if(isset($core->input['identifier']) && !empty($core->input['identifier'])) {
    $sessionidentifier = $core->input['identifier'];
}
else {
    $sessionidentifier = md5(uniqid(microtime()));
}

$session->name_phpsession(COOKIE_PREFIX.'budget_expenses_'.$sessionidentifier);
$session->start_phpsession(480);

if(!$core->input['action']) {
    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliate_where = " affid IN ({$inaffiliates})";
    }
    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, "{$affiliate_where}");
    $affiliated_budget = parse_selectlist('financialbudget[affid]', 1, $affiliates, '', '', '', array('id' => 'affid'));

    $budgetypes = array("financialadminexpenses" => "Financial & Admin Expenses", "investmentfollowup" => "Investment Follow-up", "headcount" => "Head Count", "forecastbalancesheet" => "Forecast Balance Sheet");
    $budgettypes_list = parse_selectlist('financialbudget[budgettypes]', '', $budgetypes, '', '', '', '');


    $years = array_combine(range(date('Y') + 1, date('Y') - 2), range(date('Y') + 1, date('Y') - 2));
    foreach($years as $year) {
        $year_selected = '';
        if($year == $years[date("Y")] + 1) {
            $year_selected = "selected=selected";
        }
        $budget_year .= "<option value='{$year}'{$year_selected}>{$year}</option>";
    }
    eval("\$budgetexpenses = \"".$template->get('budgeting_createexpenses')."\";");
    output_page($budgetexpenses);
}
else if($core->input['action'] == 'do_perform_createexpenses') {
    unset($core->input['module']);
    unset($core->input['identifier']);
    unset($core->input['action']);

//    if(empty($core->input['year']) || empty($core->input['affid']) || empty($core->input['budgettypes'])) {
//        output_xml('<status>false</status><message>'.$lang->fillrequiredfield.'</message>');
//    }
//    else {
    $session->set_phpsession(array('budget_expenses_'.$sessionidentifier => serialize($core->input['financialbudget'])));
    header('Content-type: text/xml+javascript');
    $url = 'index.php?module=budgeting/'.$core->input['financialbudget']['budgettypes'].'&identifier='.$sessionidentifier;
    output_xml('<status>true</status><message><![CDATA[<script>window.location.replace(\''.$url.'\')</script>]]></message>');
    exit;
    // }
}
?>
