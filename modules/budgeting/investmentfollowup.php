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
    //  error($lang->sectionnopermission);
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
        $financialbudget_prevyear = $investprevyear = $financialbudget_year - 1;
        $financialbudget_prev2year = $financialbudget_year - 2;
        $financialbudget_prev3year = $financialbudget_year - 3;

        if($core->usergroup['canViewAllAff'] == 0) {
            $affiliates = $core->user['affiliates'];
            if(!in_array($core->input['financialbudget']['affid'], array_keys($affiliates))) {
                redirect('index.php?module=budgeting/createfinbudget');
            }
        }
        $affid = $core->input['financialbudget']['affid'];
        $affiliate = new Affiliates($affid);
    }
    $prevfinancialbudget = FinancialBudget::get_data(array('affid' => $affid, 'year' => $financialbudget_prevyear), array('simple' => false));
    $financialbudget = FinancialBudget::get_data(array('affid' => $affid, 'year' => $financialbudget_year), array('simple' => false));
    $investcategories = BudgetInvestCategories::get_data('', array('returnarray' => true));
    if(is_object($financialbudget) && $financialbudget->isFinalized()) {
        $type = 'hidden';
        $budgeting_investexpenses_categories = BudgetInvestCategories::parse_expensesfields($investcategories, array('mode' => 'display', 'financialbudget' => $financialbudget, 'prevfinancialbudget' => $prevfinancialbudget));
    }
    else {
        $type = 'submit';
        $budgeting_investexpenses_categories = BudgetInvestCategories::parse_expensesfields($investcategories, array('mode' => 'fill', 'financialbudget' => $financialbudget, 'prevfinancialbudget' => $prevfinancialbudget));
    }

//    $header_variation = '<td style="width:12.5%">% '.$lang->variation.'</td>';
//    eval("\$budgeting_header = \"".$template->get('budgeting_investheader')."\";");
    $headerfields = array('actual', 'actual', 'yef', 'budget');
    $headeryears = array($financialbudget_prev3year, $financialbudget_prev2year, $financialbudget_prevyear, $financialbudget_year);
    $budgeting_header .='<tr class="thead"><td style="width:25%"></td>';
    foreach($headerfields as $field) {
        $budgeting_header .= '<td style="width:12.5%">'.$lang->$field.'</td>';
    }
    $budgeting_header .='</tr>';
    $budgeting_header .='<tr><td style="width:25%"><input name="financialbudget[affid]" value="'.$affid.'" type="hidden"></td>';
    foreach($headeryears as $year) {
        $budgeting_header .= '<td style="width:12.5%">'.$year.'</td>';
    }
    $budgeting_header .='<input name="financialbudget[year]" value="'.$financialbudget_year.'" type="hidden"></td>';
    $budgeting_header .='</tr>';


    /* get main currecny of the affiliate being budgeted */

    $affilaite_obj = new Affiliates($affid);
    $budget_affiliatecurr = $affilaite_obj->get_country()->get_maincurrency();
    if(!empty($budget_affiliatecurr)) {
        $tocurrency = '840'; //usd
        $currencyto_obj = new Currencies($tocurrency);
        $currency_to = $currencyto_obj->get()['alphaCode'];
        $dal_config = array(
                'operators' => array('fromCurrency' => '=', 'affid' => 'in', 'year' => '='),
                'simple' => false,
                'returnarray' => false
        );
        $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $budget_affiliatecurr->numCode, 'toCurrency' => $tocurrency, 'affid' => $affid, 'year' => $financialbudget_year,), $dal_config);
        if(is_object($fxrates_obj)) {
            $output_currency = '<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; padding: 5px; margin-top: 10px; margin-bottom: 10px; display: block;"><span><em>'.$lang->sprint($lang->budgcurrdesc, $budget_affiliatecurr->alphaCode).'</em></span></br><em><strong>'.$lang->exchangerate.'</strong></em></br><span>'.$lang->sprint($lang->currrate, $budget_affiliatecurr->alphaCode, $currency_to, $fxrates_obj->rate).'</span></div>';
        }
    }
    eval("\$budgeting_investexpenses = \"".$template->get('budgeting_investexpenses')."\";");
    output_page($budgeting_investexpenses);
}
else if($core->input['action'] == 'do_perform_investmentfollowup') {
    if($core->usergroup['canViewAllAff'] == 0) {
        $affiliates = $core->user['affiliates'];
        if(!in_array($core->input['financialbudget']['affid'], array_keys($affiliates))) {
            return;
        }
    }
    unset($core->input['identifier'], $core->input['module'], $core->input['action']);
    $financialbudget = new FinancialBudget();
    $financialbudget->set($core->input);
    $financialbudget->save();
    switch($financialbudget->get_errorcode()) {
        case 0:
        case 1:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 2:
            output_xml('<status>false</status><message>'.$lang->fillrequiredfield.'</message>');
            break;
    }
}
?>