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
    }

    $affid = $core->input['financialbudget']['affid'];
    $affiliate = new Affiliates($affid);
    $financialbudget = FinancialBudget::get_data(array('affid' => $affid, 'year' => $financialbudget_year), array('simple' => false));

    $budforecastobj = new BudgetForecastAccountsTree();
    if(is_object($financialbudget) && $financialbudget->isFinalized()) {
        $type = 'hidden';
        $accountitems_output = $budforecastobj->parse_account(array('financialbudget' => $financialbudget, 'mode' => 'display'));
    }
    else {
        $accountitems_output = $budforecastobj->parse_account(array('financialbudget' => $financialbudget, 'mode' => 'fill'));
    }
    //$budgetaccounts = BudgetForecastAccountsTree::parse_accounts(array('type' => array('assets' => $assets_accounts, 'liabilities' => $liability_accounts)), array('mode' => 'fill'));
    /* get main currecny of the affiliate being budgeted */

    $currency = $affiliate->get_currency();
    if(!empty($currency->alphaCode)) {
        $tocurrency = array('840', '978'); //usd,eur
        $dal_config = array(
                'operators' => array('fromCurrency' => '=', 'toCurrency' => 'in', 'affid' => 'in', 'year' => '='),
                'simple' => false,
                'returnarray' => true
        );
        $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $currency->numCode, 'toCurrency' => $tocurrency, 'affid' => $affid, 'year' => $financialbudget_year, 'isCurrent' => 1), $dal_config);
        $output_currency = '<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; padding: 5px; margin-top: 10px; margin-bottom: 10px; display: block;"><span><em>'.$lang->sprint($lang->budgcurrdesc, $currency->alphaCode).'</em></br></span>';
        if(is_array($fxrates_obj)) {
            $output_currency .='<em><strong>'.$lang->exchangerate.'</strong></em></br>';
            foreach($fxrates_obj as $fxrate) {
                $currencyto_obj = new Currencies($fxrate->toCurrency);
                $output_currency.='<span>'.$lang->sprint($lang->currrate, $currency->alphaCode, $currencyto_obj->get()['alphaCode'], $fxrate->rate).' for year: '.$fxrate->year.'</span><br/>';
            }
        }
        // Exchange rate from USD to EUR
        $usdtoeur_fxrate = BudgetFxRates::get_data(array('fromCurrency' => $tocurrency[0], 'toCurrency' => $tocurrency[1], 'affid' => $affid, 'year' => $financialbudget_year));
        if(is_object($usdtoeur_fxrate)) {
            $currencyfrom_obj = new Currencies($usdtoeur_fxrate->fromCurrency);
            $currencyto_obj = new Currencies($usdtoeur_fxrate->toCurrency);
            $output_currency .= '<span>'.$lang->sprint($lang->currrate, $currencyfrom_obj->get()['alphaCode'], $currencyto_obj->get()['alphaCode'], $usdtoeur_fxrate->rate).' for year: '.$usdtoeur_fxrate->year.'</span><br/>';
        }
        $output_currency .='</div>';
    }
    eval("\$budgeting_forecast_balancesheet = \"".$template->get('budgeting_forecast_balancesheet')."\";");
    output_page($budgeting_forecast_balancesheet);
}
else if($core->input['action'] == 'do_perform_forecastbalancesheet') {
    unset($core->input['identifier'], $core->input['module'], $core->input['action']);

    $financialbudget = new FinancialBudget();
    if(!empty($core->input['budgetforecastbs']['liabilities']['total']) && !empty($core->input['budgetforecastbs']['assets']['total'])) {
        $core->input['budgetforecastbs']['equityliabilities']['total'] = ($core->input['budgetforecastbs']['OwnersEquity']['total'] + $core->input['budgetforecastbs']['liabilities']['total']);
        if($core->input['budgetforecastbs']['equityliabilities']['total'] != $core->input['budgetforecastbs']['assets']['total']) {
            output_xml('<status>false</status><message>'.$lang->totalerror.'</message>');
            exit;
        }
    }
    unset($core->input['budgetforecastbs']['equityliabilities'], $core->input['budgetforecastbs']['assets'], $core->input['budgetforecastbs']['ownersequity'], $core->input['budgetforecastbs']['liabilities']);
    $financialbudget->set($core->input);

    $financialbudget->save();

    switch($financialbudget->get_errorcode()) {
        case 1:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 2:
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
            break;
        case 4:
            output_xml('<status>false</status><message>'.$lang->totalerror.'</message>');
            break;
    }
}