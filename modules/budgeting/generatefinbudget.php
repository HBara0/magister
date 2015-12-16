<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: genratefinbudget.php
 * Created:        @rasha.aboushakra    Oct 1, 2014 | 1:10:50 PM
 * Last Update:    @rasha.aboushakra    Oct 1, 2014 | 1:10:50 PM
 */

if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['budgeting_cangenerateFinBudgets'] == 0) {
    error($lang->sectionnopermission);
}
//$session->start_phpsession();

if(!$core->input['action']) {
    $affiliate_where = ' name LIKE "%orkila%"';
    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliate_where .= " AND affid IN ({$inaffiliates})";
    }
    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, "{$affiliate_where}");
    if(is_array($affiliates)) {
        foreach($affiliates as $key => $value) {
            if($key == 0) {
                continue;
            }
            $checked = $rowclass = '';
            $affiliates_list .='<tr class="'.$rowclass.'">';
            $affiliates_list .='<td><input name="budget[affiliates][]" type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td></tr>';
        }
    }

    $budgetypes = array('financialadminexpenses' => $lang->commercialadminstrationexpenses, 'investmentfollowup' => 'Investment Follow-up', 'headcount' => 'Head Count', 'forecastbalancesheet' => 'Forecast Balance Sheet', 'profitlossaccount' => 'Profit and Loss Account', 'overduereceivables' => 'Overdue Receivables', 'trainingvisits' => 'Training and visits', 'bank' => 'Bank');
    if(is_array($budgetypes)) {
        foreach($budgetypes as $key => $value) {
            $checked = $rowclass = '';
            $budgetypes_list .='<tr class="'.$rowclass.'">';
            $budgetypes_list .='<td><input name="budget[budgetypes][]" type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td></tr>';
        }
    }

    $years = FinancialBudget::get_availableyears();
    if(is_array($years)) {
        $checked = "checked=checked";
        foreach($years as $key => $value) {
            $rowclass = '';
            $budget_year_list .= '<tr class="'.$rowclass.'">';
            $budget_year_list .= '<td><input name="budget[year]" type="radio" value="'.$key.'"'.$checked.'>'.$value.'</td></tr>';
            $checked = '';
        }
    }
    $currency['filter']['numCode'] = 'SELECT mainCurrency FROM countries where affid IS NOT NULL';
    $curr_objs = Currencies::get_data($currency['filter'], array('returnarray' => true, 'operators' => array('numCode' => 'IN')));
    $curr_objs[840] = new Currencies(840);
    $currencies_list = parse_selectlist('budget[toCurrency]', 4, $curr_objs, '840');

    eval("\$generatefinbudget = \"".$template->get('budgeting_generatefinbudget')."\";");
    output_page($generatefinbudget);
}
?>
