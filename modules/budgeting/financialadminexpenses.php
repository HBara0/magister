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
}
else {
    $sessionidentifier = md5(uniqid(microtime()));
}

if(!isset($core->input['action'])) {
    $budget_data = unserialize($session->get_phpsession('budget_expenses_'.$sessionidentifier));
    if(empty($budget_data)) {
        $budget_data = $core->input['financialbudget'];
    }
    if($core->usergroup['canViewAllAff'] == 0) {
        $affiliates = $core->user['affiliates'];
        if(!in_array($budget_data['affid'], array_keys($affiliates))) {
            redirect('index.php?module=budgeting/createfinbudget');
        }
    }
    $financialbudget_year = $budget_data['year'];
    $financialbudget_prevyear = $financialbudget_year - 1;
    $financialbudget_prev2year = $financialbudget_year - 2;
    $financialbudget_prev3year = $financialbudget_year - 3;
    $affid = $budget_data['affid'];
    $affiliate = new Affiliates($affid);

    $prevfinancialbudget = FinancialBudget::get_data(array('affid' => $affid, 'year' => $financialbudget_prevyear), array('simple' => false));
    $financialbudget = FinancialBudget::get_data(array('affid' => $affid, 'year' => $financialbudget_year), array('simple' => false));
    $expensescategories = BudgetExpenseCategories::get_data('', array('returnarray' => true));
    if(is_object($financialbudget) && $financialbudget->isFinalized == 1) {
        $type = 'hidden';
        $output = BudgetExpenseCategories::parse_financialadminfields($expensescategories, array('mode' => 'display', 'financialbudget' => $financialbudget, 'prevfinancialbudget' => $prevfinancialbudget));
    }
    else {
        $type = 'submit';
        $output = BudgetExpenseCategories::parse_financialadminfields($expensescategories, array('mode' => 'fill', 'financialbudget' => $financialbudget, 'prevfinancialbudget' => $prevfinancialbudget));
    }

    $headerfields = array('actual', 'actual', 'yef', 'budget', 'budyef');
    $headeryears = array($financialbudget_prev3year, $financialbudget_prev2year, $financialbudget_prevyear, $financialbudget_year, '');
    $budgeting_header .='<tr class="thead"><td style="width:30%"></td>';
    foreach($headerfields as $field) {
        $budgeting_header .= '<td style="width:10%">'.$lang->$field.'</td>';
    }
    $budgeting_header .= '</tr><tr><td style="width:30%"><input name="financialbudget[affid]" value="'.$affid.'" type="hidden">';
    $budgeting_header .= '<input name="financialbudget[year]" value="'.$financialbudget_year.'" type="hidden"></td>';
    foreach($headeryears as $year) {
        $budgeting_header .= '<td style="width:10%">'.$year.'</td>';
    }
    $budgeting_header .= '</tr>';

    /* get main currecny of the affiliate being budgeted */

    $budget_affiliatecurr = $affiliate->get_currency();
    if(!empty($budget_affiliatecurr)) {
        $tocurrency = array('840', '978'); //usd,eur
        $dal_config = array(
                'operators' => array('fromCurrency' => '=', 'affid' => 'in', 'toCurrency' => 'in'),
                'simple' => false,
                'order' => 'year',
                'returnarray' => true
        );
        $fxratesconfigs = array(array('year' => $financialbudget_year, 'ratecategory' => 'isBudget'), array('year' => ($financialbudget_year - 1), 'ratecategory' => 'isYef'), array('year' => $financialbudget_year - 2, 'ratecategory' => 'isActual'), array('year' => $financialbudget_year - 3, 'ratecategory' => 'isActual'));
        foreach($fxratesconfigs as $fxconfig) {
            $query = BudgetFxRates::get_data(array('fromCurrency' => $budget_affiliatecurr->numCode, 'toCurrency' => $tocurrency, 'affid' => $affid, 'year' => $fxconfig['year'], $fxconfig['ratecategory'] => 1), $dal_config);
            if($query) {
                $fxrates_obj[] = $query;
            }
        }
        $output_currency = '<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; padding: 5px; margin-top: 10px; margin-bottom: 10px; display: block;"><span><em>'.$lang->sprint($lang->budgcurrdesc, $budget_affiliatecurr->alphaCode).'</em></span></br>';
        if(is_array($fxrates_obj)) {
            $output_currency .= '<em><strong>'.$lang->exchangerate.'</strong></em></br>';
            foreach($fxrates_obj as $fxrate) {
                if(is_array($fxrate)) {
                    foreach($fxrate as $rate) {
                        $currencyto_obj = new Currencies($rate->toCurrency);
                        $output_currency .= '<span>'.$lang->sprint($lang->currrate, $budget_affiliatecurr->alphaCode, $currencyto_obj->get()['alphaCode'], $rate->rate).' for year: '.$rate->year.'</span><br/>';
                    }
                }
            }
        }
        //Exchange rate from USD to EUR
        $usdtoeur_fxrate = BudgetFxRates::get_data(array('fromCurrency' => $tocurrency[0], 'toCurrency' => $tocurrency[1], 'affid' => $affid, 'year' => $financialbudget_year, 'isBudget' => 1));
        if(is_object($usdtoeur_fxrate)) {
            $currencyfrom_obj = new Currencies($usdtoeur_fxrate->fromCurrency);
            $currencyto_obj = new Currencies($usdtoeur_fxrate->toCurrency);
            $output_currency .= '<span>'.$lang->sprint($lang->currrate, $currencyfrom_obj->get()['alphaCode'], $currencyto_obj->get()['alphaCode'], $usdtoeur_fxrate->rate).' for year: '.$usdtoeur_fxrate->year.'</span><br/>';
        }
        $output_currency .= '</div>';
    }

    eval("\$budgeting_commercialexpenses = \"".$template->get('budgeting_commercialexpenses')."\";");
    output_page($budgeting_commercialexpenses);
}
else if($core->input['action'] == 'do_perform_financialadminexpenses') {
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
        case 3:
            output_xml('<status>false</status><message>'.$lang->finexpensestotalerror.'</message>');
            break;
    }
}
?>