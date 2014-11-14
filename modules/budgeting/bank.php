<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: bank.php
 * Created:        @rasha.aboushakra    Nov 4, 2014 | 3:13:18 PM
 * Last Update:    @rasha.aboushakra    Nov 4, 2014 | 3:13:18 PM
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

    /* select list of banks for selected affiliate */
    $affiliate = new Affiliates($budget_data['affid']);
    $banks = array('0' => '');
    $banks_objs = Banks::get_data(array('affid' => $budget_data['affid']), array('returnarray' => true));
    if(is_array($banks_objs)) {
        foreach($banks_objs as $bank) {
            $banks[$bank->bnkid] = $bank->name;
        }
    }
    /**/

    /* parse select list of covered countries currencies */
    $currencies['840'] = 'USD';
    $currency['filter']['numCode'] = 'SELECT mainCurrency FROM countries where affid IS NOT NULL';
    $curr_objs = Currencies::get_data($currency['filter'], array('returnarray' => true, 'operators' => array('numCode' => 'IN')));
    if(is_array($curr_objs)) {
        foreach($curr_objs as $curr) {
            $currencies[$curr->numCode] = $curr->alphaCode;
        }
    }
    /**/

    $financialbudget = FinancialBudget::get_data(array('affid' => $budget_data['affid'], 'year' => $budget_data['year']), array('simple' => false));
    $banksfacilities = BudgetBankFacilities::get_data(array('bfbid' => $financialbudget->bfbid), array('returnarray' => true, 'simple' => false));
    if(is_array($banksfacilities)) {
        $rowid = 0;
        foreach($banksfacilities as $bankfacility) {
            ++$rowid;
            $banks_list = parse_selectlist('bank['.$rowid.'][bnkid]', '', $banks, $bankfacility->bnkid, '', '', array('width' => '100%'));
            $currencies_list = parse_selectlist('bank['.$rowid.'][facilityCurrency]', '', $currencies, $bankfacility->facilityCurrency, '', '', array('width' => '100%'));
            if($bankfacility->LastIssuanceDate != 0) {
                $bankfacility->LastIssuanceDate = date($core->settings['dateformat'], $bankfacility->LastIssuanceDate);
            }
            if($bankfacility->LastRenewalDate != 0) {
                $bankfacility->LastRenewalDate = date($core->settings['dateformat'], $bankfacility->LastRenewalDate);
            }
            $inputChecksum = $bankfacility->inputChecksum;
            eval("\$bank_row .= \"".$template->get('budgeting_bank_row')."\";");
        }
    }
    else {
        $rowid = 1;
        $inputChecksum = generate_checksum('budget');
        $banks_list = parse_selectlist('bank['.$rowid.'][bnkid]', '', $banks, 0, '', '', array('width' => '100%'));
        $currencies_list = parse_selectlist('bank['.$rowid.'][facilityCurrency]', '', $currencies, 840, '', '', array('width' => '100%'));
        eval("\$bank_row = \"".$template->get('budgeting_bank_row')."\";");
    }
    eval("\$bank_header = \"".$template->get('budgeting_bank_header')."\";");
    eval("\$output = \"".$template->get('budgeting_bank')."\";");
    output_page($output);
}
else if($core->input['action'] == 'ajaxaddmore_bankfacilities') {
    $rowid = intval($core->input['value']) + 1;
    $budget_data = $core->input['ajaxaddmoredata'];
    $inputChecksum = generate_checksum('budget');
    $financialbudget = FinancialBudget::get_data(array('affid' => $budget_data['affid'], 'year' => $budget_data['year']), array('simple' => false));
    $banksfacilities = BudgetBankFacilities::get_data(array('bfbid' => $financialbudget->bfbid), array('returnarray' => true, 'simple' => false));

    $facilitybanks = array();
    if(is_array($banksfacilities)) {
        foreach($banksfacilities as $bankfacility) {
            $bank = new Banks($bankfacility->bnkid);
            $facilitybanks[$bankfacility->bnkid] = $bank->name;
        }
    }
    unset($bankfacility);
    $banks = array('0' => '');
    $banks_objs = Banks::get_data(array('affid' => $budget_data['affid']), array('returnarray' => true));
    if(is_array($banks_objs)) {
        foreach($banks_objs as $bank) {
            $banks[$bank->bnkid] = $bank->name;
        }
    }
    $banks = array_diff($banks, $facilitybanks);
    $banks_list = parse_selectlist('bank['.$rowid.'][bnkid]', '', $banks, '0', '', '', array('width' => '100%'));

    $currency['filter']['numCode'] = 'SELECT mainCurrency FROM countries where affid IS NOT NULL';
    $curr_objs = Currencies::get_data($currency['filter'], array('returnarray' => true, 'operators' => array('numCode' => IN)));
    $currencies['840'] = 'USD';
    if(is_array($curr_objs)) {
        foreach($curr_objs as $curr) {
            $currencies[$curr->numCode] = $curr->alphaCode;
        }
    }
    $currencies_list = parse_selectlist('budget[toCurrency]', '', $currencies, 840, '', '', array('width' => '100%'));
    eval("\$row = \"".$template->get('budgeting_bank_row')."\";");
    output($row);
}
else if($core->input['action'] == 'do_perform_bank') {
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
            output_xml('<status>false</status><message>'.$lang->updateunsuccessfull.'</message>');
            break;
    }
}
?>