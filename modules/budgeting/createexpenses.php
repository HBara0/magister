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

if(!$core->input['action']) {
    $sessionidentifier = md5(uniqid(microtime()));
    $session->name_phpsession(COOKIE_PREFIX.'budget_expenses_'.$sessionidentifier);
    $session->start_phpsession(480);

    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliate_where = " affid IN ({$inaffiliates})";
    }

    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, "{$affiliate_where}");
    $affiliated_budget = parse_selectlist('financialbudget[affid]', 1, $affiliates, '', '', '', array('id' => 'affid'));


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
//else if($core->input['action'] == 'do_perform_createexpenses') {
//    unset($core->input['module']);
//    unset($core->input['identifier']);
//    unset($core->input['action']);
//    //$session->set_phpsession(array('budget_expenses_'.$sessionidentifier => serialize($core->input)));
//    // print_r($session);
//    redirect('index.php?module=budgeting/commercialexpenses');
//    exit;
//}
?>
