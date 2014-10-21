<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: forecastbalancesheet.php
 * Created:        @tony.assaad    Oct 1, 2014 | 12:59:06 PM
 * Last Update:    @tony.assaad    Oct 1, 2014 | 12:59:06 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['budgeting_canFillFinBudgets'] == 0) {
    //error($lang->sectionnopermission);
}
if(isset($core->input['identifier']) && !empty($core->input['identifier'])) {
    $sessionidentifier = $core->input['identifier'];
}
else {
    $sessionidentifier = md5(uniqid(microtime()));
}

$session->name_phpsession(COOKIE_PREFIX.'budget_expenses_'.$sessionidentifier);
$session->start_phpsession(480);

if(!isset($core->input['action'])) {
    $session->set_phpsession(array('budget_expenses_'.$sessionidentifier => serialize($core->input['financialbudget'])));
    if(isset($core->input['financialbudget']['year']) && !empty($core->input['financialbudget']['year'])) {
        $financialbudget_year = $core->input['financialbudget']['year'];
        //  $financialbudget_prevyear = $financialbudget_year - 1;
        //$financialbudget_prev2year = $financialbudget_year - 2;
        $affid = $core->input['financialbudget']['affid'];
        $affiliate = new Affiliates($affid);
    }
    $financialbudget = FinancialBudget::get_data(array('affid' => $affid, 'year' => $financialbudget_year), array('simple' => false));

    $budforecastobj = new BudgetForecastAccountsTree();
    if(is_object($financialbudget) && $financialbudget->isFinalized()) {
        $type = 'hidden';
        $accountitems_output = $budforecastobj->parse_account('a', array('financialbudget' => $financialbudget, 'mode' => 'display'));
    }
    else {
        $accountitems_output = $budforecastobj->parse_account('a', array('financialbudget' => $financialbudget, 'mode' => 'fill'));
    }
    //$budgetaccounts = BudgetForecastAccountsTree::parse_accounts(array('type' => array('assets' => $assets_accounts, 'liabilities' => $liability_accounts)), array('mode' => 'fill'));

    eval("\$budgeting_forecast_balancesheet = \"".$template->get('budgeting_forecast_balancesheet')."\";");
    output_page($budgeting_forecast_balancesheet);
}
else if($core->input['action'] == 'do_perform_forecastbalancesheet') {
    unset($core->input['identifier']);
    unset($core->input['module']);
    unset($core->input['action']);
    $financialbudget = new FinancialBudget();
    $financialbudget->set($core->input);

    $financialbudget->save();

    switch($financialbudget->get_errorcode()) {
        case 1:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
//        case 1:
//            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
//            break;
        case 4:
            output_xml('<status>false</status><message>'.$lang->totalerror.'</message>');
            break;
    }
}