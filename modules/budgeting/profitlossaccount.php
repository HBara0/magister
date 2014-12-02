<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: profitlossaccount.php
 * Created:        @rasha.aboushakra    Oct 13, 2014 | 2:32:40 PM
 * Last Update:    @rasha.aboushakra    Oct 13, 2014 | 2:32:40 PM
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['budgeting_canFillBPl'] == 0) {
    // error($lang->sectionnopermission);
}
if(!isset($core->input['action'])) {
    $budget_data = $core->input['financialbudget'];
    $affid = $budget_data['affid'];
    $financialbudget_year = $budget_data['year'];
    $financialbudget_prevyear = $budget_data['year'] - 1;
    $financialbudget_prev3year = $budget_data['year'] - 3;
    $financialbudget_prev2year = $budget_data['year'] - 2;
    if($core->usergroup['canViewAllAff'] == 0) {
        $affiliates = $core->user['affiliates'];
        if(!in_array($budget_data['affid'], array_keys($affiliates))) {
            redirect('index.php?module=budgeting/createfinbudget');
        }
    };
    $affiliate = new Affiliates($budget_data['affid']);
    $financialbudget = FinancialBudget::get_data(array('affid' => $budget_data['affid'], 'year' => $budget_data['year']), array('simple' => false));
    $currency = $affiliate->get_currency();

    //get 3 commercial budgets of current year, prev year and prev two years
    $commericalbudget = Budgets::get_data(array('affid' => $budget_data['affid'], 'year' => $budget_data['year']), array('returnarray' => true, 'simple' => false));
    $prevcommericalbudget = Budgets::get_data(array('affid' => $budget_data['affid'], 'year' => ($budget_data['year'] - 1)), array('returnarray' => true, 'simple' => false));
    $prevtwocommericalbudget = Budgets::get_data(array('affid' => $budget_data['affid'], 'year' => ($budget_data['year'] - 2)), array('returnarray' => true, 'simple' => false));

    //get commercial budget id's (current budget, prev budget and prev two years budget)
    if(is_array($commericalbudget)) {
        foreach($commericalbudget as $budget) {
            $current[$budget->bid] = $budget->bid;
        }
    }
    if(is_array($prevcommericalbudget)) {
        foreach($prevcommericalbudget as $budget) {
            $prevyear[$budget->bid] = $budget->bid;
        }
    }
    if(is_array($prevtwocommericalbudget)) {
        foreach($prevtwocommericalbudget as $budget) {
            $prevtwoyears[$budget->bid] = $budget->bid;
        }
    }
    $budgetsids = array('prevtwoyears' => $prevtwoyears, 'prevyear' => $prevyear, 'current' => $current);

    $plcategories = BudgetPlCategories::get_data('', array('returnarray' => true));
    if(is_object($financialbudget) && $financialbudget->isFinalized()) {
        $type = 'hidden';
        $output = BudgetPlCategories::parse_plfields($plcategories, array('mode' => 'display', 'financialbudget' => $financialbudget, 'bid' => $budgetsids, 'tocurrency' => $currency->numCode, 'affid' => array('0' => $affid), 'year' => $financialbudget_year));
    }
    else {
        $type = 'submit';
        $output = BudgetPlCategories::parse_plfields($plcategories, array('mode' => 'fill', 'financialbudget' => $financialbudget, 'bid' => $budgetsids, 'tocurrency' => $currency->numCode, 'affid' => array('0' => $affid), 'year' => $financialbudget_year));
    }

    $headerfields = array($lang->actual, $lang->actual, $lang->yef, '%YEF'.$financialbudget_prevyear, $lang->budget, '%Bud'.$financialbudget_year);
    $headeryears = array($financialbudget_prev3year, $financialbudget_prev2year, $financialbudget_prevyear, '/Actual '.$financialbudget_prev2year, $financialbudget_year, '/YEF '.$financialbudget_prevyear);
    $budgeting_header .='<tr class="thead"><td></td>';
    foreach($headerfields as $field) {
        $budgeting_header .= '<td>'.$field.'</td>';
    }
    $budgeting_header .='</tr><tr><td><input name="financialbudget[affid]" value="'.$affid.'" type="hidden">';
    $budgeting_header .='<input name="financialbudget[year]" value="'.$financialbudget_year.'" type="hidden"></td>';
    foreach($headeryears as $year) {
        $budgeting_header .= '<td>'.$year.'</td>';
    }
    $budgeting_header .='</tr>';

    if(!empty($currency->alphaCode)) {
        $tocurrency = array('840', '978'); //usd,eur
        $dal_config = array(
                'operators' => array('fromCurrency' => '=', 'affid' => 'in', 'toCurrency' => 'in', 'year' => 'in'),
                'simple' => false,
                'order' => 'year',
                'returnarray' => false
        );
        $years = array(array($financialbudget_year, 'isCurrent'), array($financialbudget_year - 1, 'isYef'), array($financialbudget_year - 2, 'isActual'), array($financialbudget_year - 3, 'isActual'));
        foreach($years as $year) {
            $fxrates_obj[] = BudgetFxRates::get_data(array('fromCurrency' => $currency->numCode, 'toCurrency' => $tocurrency, 'affid' => $affid, 'year' => $year[0], $year[1] => 1), $dal_config);
        }
        $output_currency = '<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; padding: 5px; margin-top: 10px; margin-bottom: 10px; display: block;"><span><em>'.$lang->sprint($lang->budgcurrdesc, $currency->alphaCode).'</em></span></br>';
        if(is_array($fxrates_obj)) {
            $output_currency .='<em><strong>'.$lang->exchangerate.'</strong></em></br>';
            foreach($fxrates_obj as $fxrate) {
                $currencyto_obj = new Currencies($fxrate->toCurrency);
                $output_currency.='<span>'.$lang->sprint($lang->currrate, $currency->alphaCode, $currencyto_obj->get()['alphaCode'], $fxrate->rate).' for year: '.$fxrate->year.'</span><br/>';
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

    eval("\$budgeting_placcount = \"".$template->get('budgeting_placcount')."\";");
    output_page($budgeting_placcount);
}
else if($core->input['action'] == 'do_perform_profitlossaccount') {
//    if($core->usergroup['canViewAllAff'] == 0) {
//        $affiliates = $core->user['affiliates'];
//        if(!in_array($core->input['financialbudget']['affid'], array_keys($affiliates))) {
//            output_xml('<status>false</status><message></message>');
//            return;
//        }
//    }
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