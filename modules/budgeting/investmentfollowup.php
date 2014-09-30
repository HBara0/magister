<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: investmentfollowup.php
 * Created:        @tony.assaad    Sep 29, 2014 | 1:06:48 PM
 * Last Update:    @tony.assaad    Sep 29, 2014 | 1:06:48 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['budgeting_canFillInvests'] == 0) {
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

if(!isset($core->input['action'])) {
    $session->set_phpsession(array('budget_expenses_'.$sessionidentifier => serialize($core->input['financialbudget'])));
    if(isset($core->input['financialbudget']['year']) && !empty($core->input['financialbudget']['year'])) {
        $financialbudget_year = $core->input['financialbudget']['year'];
        $financialbudget_prevyear = $financialbudget_year - 1;
        $financialbudget_prev2year = $financialbudget_year - 2;
        $affid = $core->input['financialbudget']['affid'];
        $affiliate = new Affiliates($affid);
    }
    $prevfinancialbudget = FinancialBudget::get_data(array('affid' => $affid, 'year' => $financialbudget_prevyear), array('simple' => false));
    $financialbudget = FinancialBudget::get_data(array('affid' => $affid, 'year' => $financialbudget_year), array('simple' => false));
    $investcategories = BudgetInvestCategories::get_data('', array('returnarray' => true));
    //  foreach($investcategories as $category) {
    unset($subtotal);
    unset($readonly);
    // $budgeting_investexpenses_categories = '';
    $fields = array('budgetPrevYear', 'yefPrevYear', 'budgetCurrent');

    $budgeting_investexpenses_categories = BudgetInvestCategories::parse_expensesfields($investcategories, array('mode' => 'fill', 'financialbudget' => $financialbudget, 'prevfinancialbudget' => $prevfinancialbudget));

    eval("\$budgeting_header = \"".$template->get('budgeting_investheader')."\";");

    eval("\$budgeting_investexpenses = \"".$template->get('budgeting_investexpenses')."\";");

    output_page($budgeting_investexpenses);
}
else if($core->input['action'] == 'do_perform_investmentfollowup') {

    unset($core->input['identifier']);
    unset($core->input['module']);
    unset($core->input['action']);
    $financialbudget = new FinancialBudget();
    $financialbudget->set($core->input);
    $financialbudget->save();
    switch($financialbudget->get_errorcode()) {
        case 0:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 1:
            output_xml('<status>false</status><message>'.$lang->fillrequiredfield.'</message>');
            break;
    }
}
?>