<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: overduereceivables.php
 * Created:        @rasha.aboushakra    Nov 3, 2014 | 8:52:52 AM
 * Last Update:    @rasha.aboushakra    Nov 3, 2014 | 8:52:52 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['budgeting_canFillFinBudgets'] == 0) {
    // error($lang->sectionnopermission);
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

    $affiliate = new Affiliates($budget_data['affid']);
    $financialbudget = FinancialBudget::get_data(array('affid' => $budget_data['affid'], 'year' => $budget_data['year']), array('simple' => false));
    $budget_affiliatecurr = $affiliate->get_country()->get_maincurrency();
    if(!empty($budget_affiliatecurr)) {
        $tocurrency = '840'; //usd
        $currencyto_obj = new Currencies($tocurrency);
        $currency_to = $currencyto_obj->get()['alphaCode'];
        $dal_config = array(
                'operators' => array('fromCurrency' => '=', 'affid' => 'in', 'year' => '='),
                'simple' => false,
                'returnarray' => false
        );
        $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $budget_affiliatecurr->numCode, 'toCurrency' => $tocurrency, 'affid' => $budget_data['affid'], 'year' => $budget_data['year'],), $dal_config);
        if(is_object($fxrates_obj)) {
            $output_currency = '<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; padding: 5px; margin-top: 10px; margin-bottom: 10px; display: block;"><span><em>'.$lang->sprint($lang->budgcurrdesc, $budget_affiliatecurr->alphaCode).'</em></span></br><em><strong>'.$lang->exchangerate.'</strong></em></br><span>'.$lang->sprint($lang->currrate, $budget_affiliatecurr->alphaCode, $currency_to, $fxrates_obj->rate).'</span></div>';
        }
    }
    $clientsoverdues = BudgetOverdueReceivables::get_data(array('bfbid' => $financialbudget->bfbid), array('returnarray' => true));
    $totalamount = 0;
    if(is_array($clientsoverdues)) {
        eval("\$overduereceivables_header = \"".$template->get('budgeting_overduereceivables_header')."\";");
        $rowid = 0;
        foreach($clientsoverdues as $clientoverdue) {
            ++$rowid;
            $client = new Entities($clientoverdue->cid);
            $clientoverdue->customername = $client->get_displayname();
            $totalamount +=$clientoverdue->totalAmount;
            if($clientoverdue->oldestUnpaidInvoiceDate != 0) {
                $clientoverdue->oldestUnpaidInvoiceDate = date($core->settings['dateformat'], $clientoverdue->oldestUnpaidInvoiceDate);
            }
            else {
                $clientoverdue->oldestUnpaidInvoiceDate = '';
            }
            $inputChecksum = $clientoverdue->inputChecksum;
            eval("\$overduereceivables_row .= \"".$template->get('budgeting_overduereceivables_row')."\";");
        }
    }
    else {
        $inputChecksum = generate_checksum('budget');
        eval("\$overduereceivables_header = \"".$template->get('budgeting_overduereceivables_header')."\";");
        $rowid = 1;
        eval("\$overduereceivables_row = \"".$template->get('budgeting_overduereceivables_row')."\";");
    }
    eval("\$output = \"".$template->get('budgeting_overduereceivables')."\";");
    output_page($output);
}
else if($core->input['action'] == 'ajaxaddmore_clientsoverdues') {
    $rowid = intval($core->input['value']) + 1;
    $budget_data = $core->input['ajaxaddmoredata'];
    $inputChecksum = generate_checksum('budget');

    eval("\$row = \"".$template->get('budgeting_overduereceivables_row')."\";");
    output($row);
}
else if($core->input['action'] == 'do_perform_overduereceivables') {
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