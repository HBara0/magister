<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: createexpenses.php
 * Created:        @rasha.aboushakra    Sep 25, 2014 | 10:47:14 AM
 * Last Update:    @rasha.aboushakra    Sep 25, 2014 | 10:47:14 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['budgeting_canFillComAdmExp'] == 0) {
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

if(!$core->input['action']) {
    $affiliate_where = ' name LIKE "%orkila%"';
    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliate_where .= ' AND affid IN ('.$inaffiliates.')';
    }
    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 0, $affiliate_where);
    $affiliated_budget = parse_selectlist('financialbudget[affid]', 1, $affiliates, $core->user['mainaffiliate'], 0, '', array('id' => 'affid'));

    $budgetypes = array('financialadminexpenses' => $lang->commercialadminstrationexpenses, 'investmentfollowup' => 'Investment Follow-up', 'headcount' => 'Head Count', 'profitlossaccount' => $lang->profitandlossaccount, 'forecastbalancesheet' => 'Forecast Balance Sheet', 'overduereceivables' => 'Overdue Receivables',
            'trainingvisits' => 'Trainings and Visits', 'bank' => 'Bank Facilities');
    $budgettypes_list = parse_selectlist('financialbudget[budgettypes]', '', $budgetypes, '', '', '$("#module_hiddenfield").val("budgeting/" + $(this).val());', '');

    $years = array_combine(range(date('Y') + 1, date('Y') - 2), range(date('Y') + 1, date('Y') - 2));
    foreach($years as $year) {
        $year_selected = '';
        if($year == $years[date('Y')] + 1) {
            $year_selected = ' selected="selected"';
        }
        $budget_year .= "<option value='{$year}'{$year_selected}>{$year}</option>";
    }
    eval("\$budgetexpenses = \"".$template->get('budgeting_createfinbudget')."\";");
    output_page($budgetexpenses);
}
?>
