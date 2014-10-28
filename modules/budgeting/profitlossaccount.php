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
    $plyear = $financialbudget_year = $budget_data['year'];
    $plprevyear = $financialbudget_prevyear = $plyear - 1;
    $financialbudget_prev3year = $financialbudget_year - 3;
    $financialbudget_prev2year = $plyear - 2;
    if($core->usergroup['canViewAllAff'] == 0) {
        $affiliates = $core->user['affiliates'];
        if(!in_array($budget_data['affid'], array_keys($affiliates))) {
            redirect('index.php?module=budgeting/createfinbudget');
        }
    };
    $affiliate = new Affiliates($budget_data['affid']);
    $financialbudget = FinancialBudget::get_data(array('affid' => $budget_data['affid'], 'year' => $budget_data['year']), array('simple' => false));
    $currency = $affiliate->get_country()->get_maincurrency();
//get 3 commercial budgets of current year, prev year and prev two years
    $commericalbudget = Budgets::get_data(array('affid' => $budget_data['affid'], 'year' => $budget_data['year']), array('returnarray' => true, 'simple' => false));
    $prevcommericalbudget = Budgets::get_data(array('affid' => $budget_data['affid'], 'year' => ($budget_data['year'] - 1)), array('returnarray' => true, 'simple' => false));
    $prevtwocommericalbudget = Budgets::get_data(array('affid' => $budget_data['affid'], 'year' => ($budget_data['year'] - 2)), array('returnarray' => true, 'simple' => false));

    //get commercial budget id's (current budget, prev budget and prev two years budget)
//    $current[$commericalbudget->bid] = $commericalbudget->bid;
//    $prevtwoyears[$prevtwocommericalbudget->bid] = $prevtwocommericalbudget->bid;
//    $prevyear[$prevcommericalbudget->bid] = $prevcommericalbudget->bid;
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
        $output = BudgetPlCategories::parse_plfields($plcategories, array('mode' => 'display', 'financialbudget' => $financialbudget, 'bid' => $budgetsids, 'tocurrency' => $currency->numCode));
    }
    else {
        $type = 'submit';
        $output = BudgetPlCategories::parse_plfields($plcategories, array('mode' => 'fill', 'financialbudget' => $financialbudget, 'bid' => $budgetsids, 'tocurrency' => $currency->numCode));
    }



    $headerfields = array($lang->actual, $lang->actual, $lang->budget, $lang->yef, '%YEF'.$financialbudget_prevyear, '%YEF'.$financialbudget_prevyear, $lang->budget, '%Bud'.$financialbudget_year);
    $headeryears = array($financialbudget_prev3year, $financialbudget_prev2year, $financialbudget_prevyear, $financialbudget_prevyear, '/Actual '.$financialbudget_prev2year, '/Budget '.$financialbudget_prevyear, $financialbudget_year, '/YEF '.$financialbudget_prevyear);
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
        $tocurrency = '840'; //usd
        $currencyto_obj = new Currencies($tocurrency);
        $currency_to = $currencyto_obj->get()['alphaCode'];
        $dal_config = array(
                'operators' => array('fromCurrency' => '=', 'affid' => 'in', 'year' => '='),
                'simple' => false,
                'returnarray' => false
        );
        $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $currency->numCode, 'toCurrency' => $tocurrency, 'affid' => $affid, 'year' => $financialbudget_year,), $dal_config);
        if(is_object($fxrates_obj)) {
            $output_currency = '<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; padding: 5px; margin-top: 10px; margin-bottom: 10px; display: block;"><span><em>'.$lang->sprint($lang->budgcurrdesc, $currency->alphaCode).'</em></br></span><em><strong>'.$lang->exchangerate.'</strong></em></br><span>'.$lang->sprint($lang->currrate, $currency->alphaCode, $currency_to, $fxrates_obj->rate).'</span></div>';
        }
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
//}
?>