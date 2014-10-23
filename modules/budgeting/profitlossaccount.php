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
    $commericalbudget = Budgets::get_data(array('affid' => $budget_data['affid'], 'year' => $budget_data['year']), array('simple' => false));
    $prevcommericalbudget = Budgets::get_data(array('affid' => $budget_data['affid'], 'year' => ($budget_data['year'] - 1)), array('simple' => false));
    $prevtwocommericalbudget = Budgets::get_data(array('affid' => $budget_data['affid'], 'year' => ($budget_data['year'] - 2)), array('simple' => false));

    //get commercial budget id's (current budget, prev budget and prev two years budget)
    $current[$commericalbudget->bid] = $commericalbudget->bid;
    $prevtwoyears[$prevtwocommericalbudget->bid] = $prevtwocommericalbudget->bid;
    $prevyear[$prevcommericalbudget->bid] = $prevcommericalbudget->bid;
    if(is_array($commericalbudget)) {
        unset($current[$commericalbudget->bid]);
        foreach($commericalbudget as $budget) {
            $current[$budget->bid] = $budget->bid;
        }
    }
    if(is_array($prevcommericalbudget)) {
        unset($prevyear[$prevcommericalbudget->bid]);
        foreach($prevcommericalbudget as $budget) {
            $prevyear[$budget->bid] = $budget->bid;
        }
    }
    if(is_array($prevtwocommericalbudget)) {
        unset($prevtwoyears[$prevtwocommericalbudget->bid]);
        foreach($prevtwocommericalbudget as $budget) {
            $prevtwoyears[$budget->bid] = $budget->bid;
        }
    }
    $bid = array('prevtwoyears' => $prevtwoyears, 'prevyear' => $prevyear, 'current' => $current);
    $plcategories = BudgetPlCategories::get_data('', array('returnarray' => true));
    if(is_object($financialbudget) && $financialbudget->isFinalized()) {
        $type = 'hidden';
        $output = BudgetPlCategories::parse_plfields($plcategories, array('mode' => 'display', 'financialbudget' => $financialbudget, 'bid' => $bid, 'tocurrency' => $currency->numCode));
    }
    else {
        $type = 'submit';
        $output = BudgetPlCategories::parse_plfields($plcategories, array('mode' => 'fill', 'financialbudget' => $financialbudget, 'bid' => $bid, 'tocurrency' => $currency->numCode));
    }
    $header_yef = '<td style = "width:8.3%">%'.$lang->yef.' '.$plprevyear.'</td>';
    $header_yef .= '<td style = "width:8.3%">%'.$lang->yef.' '.$plprevyear.'</td>';
    $header_budyef .= '<td style = "width:8.3%">%'.$lang->bud.' '.$plyear.'</td>';
    $actual = '<td>/'.$lang->actual.' '.$financialbudget_prev2year.'</td>';
    $bud = '/Budget ';
    $pl_yefprevyear = '/YEF '.$financialbudget_prevyear;
    eval("\$budgeting_header = \"".$template->get('budgeting_investheader')."\";");

///////
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
            $output_currency = '<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; padding: 5px; margin-top: 10px; margin-bottom: 10px; display: block;"><em><strong>'.$lang->exchangerate.'</strong></em></br><span>'.$lang->sprint($lang->currrate, $currency->alphaCode, $currency_to, $fxrates_obj->rate).'</span></div>';
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
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 1:
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
            break;
    }
}
//}
?>