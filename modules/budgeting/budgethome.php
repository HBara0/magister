<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: budgethome.php
 * Created:        @hussein.barakat    28-Oct-2015 | 15:12:50
 * Last Update:    @hussein.barakat    28-Oct-2015 | 15:12:50
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['canUseBudgeting'] == 0) {
    error($lang->sectionnopermission);
}
$sections = array('rates', 'budget', 'yef', 'fin');
$red = '#F04122';
$green = 'green';
foreach($sections as $section) {
    $status[$section]['color'] = $green;
    $status[$section]['count'] = '<span class="glyphicon glyphicon-ok"></span>';
    $status[$section]['disable'] = 'disabled="disabled"';
}

$user = new Users($core->user['uid']);

$permissions = $user->get_businesspermissions();
if(is_array($permissions)) {
    $baseyear = date('Y');
    $quarter = currentquarter_info(true);
    if($quarter['quarter'] == 4) {
        $baseyear = date('Y') + 1;
    }
    $affiliate_where = ' name LIKE "%orkila%" AND isActive=1';
    if($core->usergroup['canViewAllAff'] == 0) {
        $affs = $core->user['affiliates'];
        $inaffiliates = implode(',', $affs);
        $affiliate_where .= " AND affid IN ({$inaffiliates})";
    }
    $affiliates = Affiliates::get_affiliates($affiliate_where, array('operators' => array('filter' => 'CUSTOMSQLSECURE'), 'returnarray' => true));
    if(is_array($affiliates)) {
        foreach($affiliates as $affiliate) {
            $affiliates_list .= '<tr><td><input id="affiliatefilter_check_'.$affiliate->affid.'" type="checkbox" value="'.$affiliate->affid.'" name="budgeting[affid]['.$affiliate->affid.']">'.$affiliate->get_displayname().'</td></tr>';
        }
    }
    if($core->input['action'] == 'generate') {
        if(is_array($core->input['budgeting']['affid'])) {
            $affs = $core->input['budgeting']['affid'];
        }
        else if($core->usergroup['canViewAllAff'] == 0) {
            $affs = $core->user['affiliates'];
        }
    }
    /* build basic checks */
    if(!is_array($affs)) {
        $allaffs = Affiliates::get_column('affid', array('isActive' => 1), array('returnarray' => true));
        if(is_array($allaffs)) {
            $affs = $allaffs;
        }
    }

    $basic_where = ' isLocked = 0';
    $basic_where.=$affiliatewhere = ' AND affid IN ('.implode(',', $affs).') ';

    if($core->usergroup['canViewAllSup'] == 0) {
        if(is_array($permissions['spid'])) {
            $basic_where.=' AND spid IN ('.implode(',', $permissions['spid']).') ';
        }
    }
    /* check budgeting rates */
    if(is_array($affs)) {
        $yearstocheck = array($baseyear, $baseyear - 1);
        foreach($affs as $affid) {
            foreach($yearstocheck as $year) {
                $budgets = Budgets::get_column('bid', 'year = '.$year.$affiliatewhere, array('returnarray' => true));
                if(is_array($budgets)) {
                    $currencies = BudgetLines::get_column('DISTINCT(originalCurrency) as orcur', array('bid' => $budgets, 'originalCurrency' => 840), array('returnarray' => true, 'alias' => 'orcur', 'singlecolumn' => true, 'operators' => array('originalCurrency' => 'NOT IN')));
                    if(is_array($currencies)) {
                        foreach($currencies as $currency) {
                            $existing_cur = BudgetFxRates::get_data(array('fromCurrency' => $currency, 'toCurrency' => 840, 'year' => $year, 'affid' => $affid, 'isBudget' => 1), array('returnarray' => false));
                            if(!is_object($existing_cur)) {
                                $status['rates']['color'] = $red;
                                $status['rates']['disable'] = '';
                                $missingratesp['budget'][$year][$affid][$currency] = $currency;
                            }
                        }
                    }
                }
            }
            /* yef rates */
            $yefs = BudgetingYearEndForecast::get_column('yefid', 'year = '.($baseyear - 1).$affiliatewhere, array('returnarray' => true));
            if(is_array($yefs)) {
                $currencies = BudgetingYEFLines::get_column('DISTINCT(originalCurrency) as orcur', array('yefid' => $yefs, 'originalCurrency' => 840), array('returnarray' => true, 'alias' => 'orcur', 'singlecolumn' => true, 'operators' => array('originalCurrency' => 'NOT IN')));
                if(is_array($currencies)) {
                    foreach($currencies as $currency) {
                        $existing_cur = BudgetFxRates::get_data(array('fromCurrency' => $currency, 'toCurrency' => 840, 'year' => ($baseyear - 1), 'affid' => $affid, 'isYef' => 1), array('returnarray' => false));
                        if(!is_object($existing_cur)) {
                            $status['rates']['color'] = $red;
                            $status['rates']['disable'] = '';
                            $missingratesp['yef'][$year][$affid][$currency] = $currency;
                        }
                    }
                }
            }
            /* FINANCIAL rates */
            if($core->usergroup['budgeting_canFillFinBudgets'] == 1) {
                $affiliate = new Affiliates($affid);
                $country = $affiliate->get_country();
                if(!empty($country->maincurrency)) {
                    $existing_cur = BudgetFxRates::get_data(array('toCurrency' => $affiliate->mainCurrency, 'fromCurrency' => $country->maincurrency, 'year' => ($baseyear), 'affid' => $affid, 'isBudget' => 1), array('returnarray' => false));
                    if(!is_object($existing_cur)) {
                        $status['rates']['color'] = $red;
                        $status['rates']['disable'] = '';
                        $missingratesp['fin'][$year][$affid][$currency] = $currency;
                    }
                }
            }
        }
    }


//    $bids = Budgets::get_column('bid', $basic_where.' OR isLocked = 1 AND year IN ('.$baseyear.','.($baseyear - 1).')', array('returnarray' => true));
//    if(is_array($bids)) {
//        $missingratesp['budget'] = BudgetLines::get_data('fx.fromCurrency IS NULL   AND originalCurrency != 840 AND bid IN ('.implode(',', $bids).')', array('join' => 'LEFT JOIN ( SELECT fromCurrency FROM '.Tprefix.BudgetFxRates::TABLE_NAME.' WHERE year in ('.$baseyear.','.($baseyear - 1).') AND toCurrency = 840 AND isBudget =1'.$affiliatewhere.') AS fx ON originalCurrency=fx.fromCurrency ', 'returnarray' => true));
//        if(is_array($missingratesp['budget'])) {
//            $status['rates']['color'] = $red;
//            $status['rates']['disable'] = '';
//        }
//    }
    /* yef rates */
//    $yefids = BudgetingYearEndForecast::get_column('yefid', $basic_where.' OR isLocked = 1 AND year = '.$baseyear, array('returnarray' => true));
//    if(is_array($yefids)) {
//        $missingratesp['yef'] = BudgetingYEFLines::get_data('fx.fromCurrency IS NULL AND originalCurrency != 840 AND yefid IN ('.implode(', ', $yefids).')', array('join' => 'LEFT JOIN ( SELECT fromCurrency FROM '.Tprefix.BudgetFxRates::TABLE_NAME.' WHERE year = '.$baseyear.' AND toCurrency = 840 AND isYef = 1 '.$affiliatewhere.') AS fx ON originalCurrency = fx.fromCurrency ', 'returnarray' => true));
//        if(is_array($missingratesp['yef'])) {
//            $status['rates']['color'] = $red;
//            $status['rates']['disable'] = '';
//        }
//    }

    /* show missing lines in rates */
    if(is_array($missingratesp)) {
        $corrections['rates'] = '<ul>';
        $status['rates']['correction'] = '';
        $status['rates']['count'] = 0;
        foreach($missingratesp as $category => $years) {
            if(is_array($years)) {
                foreach($years as $year => $affids) {
                    if(is_array($affids)) {
                        foreach($affids as $affid => $currencies) {
                            foreach($currencies as $currency) {
                                if(!empty($currency)) {
                                    switch($category) {
                                        case 'budget':
                                            $ratetype = $lang->budget;
                                            break;
                                        case 'yef':
                                            $ratetype = $lang->yearendforecast;
                                            break;
                                        case 'fin':
                                            $ratetype = $lang->financialbudget;
                                            break;
                                        default:
                                            break;
                                    }
                                    $currency_obj = new Currencies($currency);
                                    $affiliate = new Affiliates($affid);
                                    $status['rates']['count'] ++;
                                    $corrections['rates'].= '<li>'.$affiliate->get_displayname().' - '.$year.' - '.$currency_obj->get_displayname().' - '.$ratetype.'</li>';
                                }
                            }
                        }
                    }
                }
            }
        }
        $corrections['rates'] .= '</ul>';
    }
    /* check budgets */
    $budget_where = ' AND year IN ('.$baseyear.')';
    $unbudgets = Budgets::get_data($basic_where.$budget_where, array('simple' => false, 'returnarray' => true));
    /* show missing lines in budgets */
    if(is_array($unbudgets)) {
        $status['budget']['count'] = 0;
        $status['budget']['color'] = $red;
        $status['budget']['disable'] = '';
        $corrections['budget'] = '<ul>';
        foreach($unbudgets as $budget_obj) {
            $status['budget']['count'] ++;
            $corrections['budget'].= '<li>'.$budget_obj->get_supplier()->get_displayname().' - '.$budget_obj->get_affiliate()->get_displayname().' - '.$budget_obj->year.'</li>';
        }
        $corrections['budget'] .= '</ul>';
    }
    /* check yef */
    $yef_where = ' AND year IN ('.$baseyear.')';
    $unyef = BudgetingYearEndForecast:: get_data($basic_where.$yef_where, array('returnarray' => true));
    /* show missing lines in budgets */
    if(is_array($unyef)) {
        $status['yef']['count'] = 0;
        $status['yef']['color'] = $red;
        $status['yef']['disable'] = '';
        $corrections['yef'] = '<ul>';
        foreach($unyef as $yef_obj) {
            $status['yef']['count'] ++;
            $corrections['yef'].= '<li>'.$yef_obj->get_supplier()->get_displayname().' - '.$yef_obj->get_affiliate()->get_displayname().' - '.$yef_obj->year.'</li>';
        }
        $corrections['yef'] .= '</ul>';
    }
    /* check financial */
    $hidefinance = 'style = "display:none"';
    if($core->usergroup['budgeting_canFillFinBudgets'] == 1) {
        $hidefinance = '';
        $fin_where = ' isFinalized = 0 AND year IN ('.$baseyear.')';
        if(is_array($affs)) {
            $fin_where.=' AND affid IN ('.implode(', ', $affs).') ';
        }
        $unfin = FinancialBudget::get_data($fin_where, array('returnarray' => true));
        if(is_array($unfin)) {
            $status['fin']['count'] = 0;
            $status['fin']['color'] = $red;
            $status['fin']['disable'] = '';
            $corrections['fin'] = '<ul>';
            foreach($unfin as $financialbudget) {
                $status['fin']['count'] ++;
                $corrections['fin'] .= '<li>'.$financialbudget->get_affiliate()->get_displayname().' - '.$financialbudget->year.'</li>';
            }
            $corrections['fin'] .= '<ul>';
        }
    }
}
eval("\$checklist = \"".$template->get('budgeting_home_checklist')."\";");
if($core->input['action'] == 'generate') {
    output_xml('<status></status><message><![CDATA['.$checklist.']]></message>');
    exit;
}
eval("\$budgethome = \"".$template->get('budgeting_home')."\";");
output_page($budgethome);
