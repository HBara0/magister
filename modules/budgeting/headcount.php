<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: headcount.php
 * Created:        @rasha.aboushakra    Sep 30, 2014 | 1:49:13 PM
 * Last Update:    @rasha.aboushakra    Sep 30, 2014 | 1:49:13 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['budgeting_canFillFinBudgets'] == 0) {
    error($lang->sectionnopermission);
}
$session->name_phpsession(COOKIE_PREFIX.'budget_expenses_'.$sessionidentifier);
$session->start_phpsession(480);

if(isset($core->input['identifier']) && !empty($core->input['identifier'])) {
    $sessionidentifier = $core->input['identifier'];
}
else {
    $sessionidentifier = md5(uniqid(microtime()));
}

if(!isset($core->input['action'])) {

    $budget_data = unserialize($session->get_phpsession('budget_expenses_'.$sessionidentifier));
    if(empty($budget_data)) {
        $budget_data = $core->input['financialbudget'];
    }
    $financialbudget_year = $budget_data['year'];
    $financialbudget_prevyear = $financialbudget_year - 1;
    $financialbudget_prev2year = $financialbudget_year - 2;
    $affid = $budget_data['affid'];
    $affiliate = new Affiliates($affid);
    // $positiongroups=
}
else if($core->input['action'] == 'do_perform_financialadminexpenses') {
    //$budget_data = unserialize($session->get_phpsession('budget_expenses_'.$core->input['identifier']));
    unset($core->input['identifier'], $core->input['module'], $core->input['action']);

    $financialbudget = new FinancialBudget();
    $financialbudget->set($core->input);
    $financialbudget->save();
    switch($financialbudget->get_errorcode()) {
        case 0:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 1:
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
            break;
    }
}
?>