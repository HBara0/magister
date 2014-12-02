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
    }

    if($core->usergroup['canViewAllAff'] == 0) {
        $affiliates = $core->user['affiliates'];
        if(!in_array($core->input['financialbudget']['affid'], array_keys($affiliates))) {
            redirect('index.php?module=budgeting/createfinbudget');
        }
    }
    $affid = $core->input['financialbudget']['affid'];
    $affiliate = new Affiliates($affid);
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
    $budget_affiliatecurr = $affiliate->get_currency();
    if(!empty($budget_affiliatecurr)) {
        $tocurrency = array('840', '978'); //usd,eur
        $dal_config = array(
                'operators' => array('fromCurrency' => '=', 'toCurrency' => 'in', 'affid' => 'in', 'year' => '='),
                'simple' => false,
                'order' => 'year',
                'returnarray' => false
        );
        $years = array(array($financialbudget_year, 'isCurrent'), array($financialbudget_year - 1, 'isYef'), array($financialbudget_year - 2, 'isActual'), array($financialbudget_year - 3, 'isActual'));
        foreach($years as $year) {
            $fxrates_obj[] = BudgetFxRates::get_data(array('fromCurrency' => $budget_affiliatecurr->numCode, 'toCurrency' => $tocurrency, 'affid' => $affid, 'year' => $year[0], $year[1] => 1), $dal_config);
        }
        $output_currency = '<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; padding: 5px; margin-top: 10px; margin-bottom: 10px; display: block;"><span><em>'.$lang->sprint($lang->budgcurrdesc, $budget_affiliatecurr->alphaCode).'</em></span></br>';
        if(is_array($fxrates_obj)) {
            $output_currency .='<em><strong>'.$lang->exchangerate.'</strong></em></br>';
            foreach($fxrates_obj as $rate) {
                $currencyto_obj = new Currencies($rate->toCurrency);
                $output_currency.='<span>'.$lang->sprint($lang->currrate, $budget_affiliatecurr->alphaCode, $currencyto_obj->get()['alphaCode'], $rate->rate).' for year: '.$rate->year.'</span><br/>';
            }
        }
        // Exchange rate from USD to EUR
        $usdtoeur_fxrate = BudgetFxRates::get_data(array('fromCurrency' => $tocurrency[0], 'toCurrency' => $tocurrency[1], 'affid' => $affid, 'year' => $years), array('operators' => array('year' => 'in')));
        if(is_object($usdtoeur_fxrate)) {
            $currencyfrom_obj = new Currencies($usdtoeur_fxrate->fromCurrency);
            $currencyto_obj = new Currencies($usdtoeur_fxrate->toCurrency);
            $output_currency .= '<span>'.$lang->sprint($lang->currrate, $currencyfrom_obj->get()['alphaCode'], $currencyto_obj->get()['alphaCode'], $usdtoeur_fxrate->rate).' for year: '.$usdtoeur_fxrate->year.'</span><br/>';
        }
        $output_currency.='</div>';
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
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
            break;
    }
}
?>