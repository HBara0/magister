<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: commercialexpenses.php
 * Created:        @rasha.aboushakra    Sep 23, 2014 | 11:01:11 AM
 * Last Update:    @rasha.aboushakra    Sep 23, 2014 | 11:01:11 AM
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
    //$session->set_phpsession(array('budget_expenses_'.$sessionidentifier => serialize($core->input['financialbudget'])));
}
else {
    $sessionidentifier = md5(uniqid(microtime()));
}

if(!isset($core->input['action'])) {
    //$session->set_phpsession(array('budget_expenses_'.$sessionidentifier => serialize($core->input['financialbudget'])));
    $budget_data = unserialize($session->get_phpsession('budget_expenses_'.$sessionidentifier));
    if(empty($budget_data)) {
        $budget_data = $core->input['financialbudget'];
    }
    $financialbudget_year = $budget_data['year'];
    $financialbudget_prevyear = $financialbudget_year - 1;
    $financialbudget_prev2year = $financialbudget_year - 2;
    $affid = $budget_data['affid'];
    $affiliate = new Affiliates($affid);

    $prevfinancialbudget = FinancialBudget::get_data(array('affid' => $affid, 'year' => $financialbudget_prevyear), array('simple' => false));
    $financialbudget = FinancialBudget::get_data(array('affid' => $affid, 'year' => $financialbudget_year), array('simple' => false));
    $expensescategories = BudgetExpenseCategories::get_data('', array('returnarray' => true));
    foreach($expensescategories as $category) {
        unset($subtotal);
        unset($readonly);
        $budgeting_commercialexpenses_item = '';
        $fields = array('actualPrevTwoYears', 'budgetPrevYear', 'yefPrevYear', 'budgetCurrent');
        $expensesitems = $category->get_items();
        if(is_array($expensesitems)) {
            foreach($expensesitems as $item) {
                $comadmin_expenses = BudgetComAdminExpenses::get_data(array('beciid' => $item->beciid, 'bfbid' => $financialbudget->bfbid), array('simple' => false));
                if(is_object($comadmin_expenses)) {
                    foreach($fields as $field) {
                        $budgetexps[$field] = $comadmin_expenses->$field;
                        $subtotal[$field] += $comadmin_expenses->$field;
                    }
                    $budgetexps['budYefPerc'] = sprintf("%.2f", $comadmin_expenses->budYefPerc).'%';
                    if($subtotal['yefPrevYear'] != 0) {
                        $subtotal['budYefPerc'] = sprintf("%.2f", (($subtotal['budgetCurrent'] - $subtotal['yefPrevYear']) / $subtotal['yefPrevYear']) * 100).'%';
                    }
                }

                if(is_object($prevfinancialbudget)) {
                    $prevyear_comadmin_expenses = BudgetComAdminExpenses::get_data(array('beciid' => $item->beciid, 'bfbid' => $prevfinancialbudget->bfbid), array('simple' => false));
                    $readonly = 'readonly';
                    $budgetexps['budgetPrevYear'] = $prevyear_comadmin_expenses->budgetCurrent;
                    $subtotal['budgetPrevYear'] += $budgetexps['budgetPrevYear'];
                }

                eval("\$budgeting_commercialexpenses_item .= \"".$template->get('budgeting_commercialexpenses_item')."\";");
                unset($budgetexps);
            }
        }
        foreach($fields as $field) {
            $total[$field] += $subtotal[$field];
            if($total[$field] == 0) {
                unset($total[$field]);
            }
        }
        eval("\$budgeting_commercialexpenses_category .= \"".$template->get('budgeting_commercialexpenses_category')."\";");
    }

    if(is_object($financialbudget)) {
        $financialbudgetdata[actualPrevTwoYears] = $financialbudget->finGenAdmExpAmtApty;
        $financialbudgetdata[budgetPrevYear] = $financialbudget->finGenAdmExpAmtBpy;
        $financialbudgetdata[yefPrevYear] = $financialbudget->finGenAdmExpAmtYpy;
        $financialbudgetdata[budgetCurrent] = $financialbudget->finGenAdmExpAmtCurrent;
        $fields = array('actualPrevTwoYears', 'budgetPrevYear', 'yefPrevYear', 'budgetCurrent');

        foreach($fields as $field) {
            $comexpenses[$field] = $total[$field] - $financialbudgetdata[$field];
            $propfin[$field] = sprintf("%.2f", (($financialbudgetdata[$field] / $total[$field]) * 100)).'%';
            $propcomexpenses[$field] = sprintf("%.2f", (( $comexpenses[$field] / $total[$field]) * 100)).'%';
        }
    }
    eval("\$budgeting_financeexpenses = \"".$template->get('budgeting_financeexpenses')."\";");
    eval("\$budgeting_commercialexpenses = \"".$template->get('budgeting_commercialexpenses')."\";");
    output_page($budgeting_commercialexpenses);
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