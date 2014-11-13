<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: previewfinbudget.php
 * Created:        @rasha.aboushakra    Oct 1, 2014 | 2:52:27 PM
 * Last Update:    @rasha.aboushakra    Oct 1, 2014 | 2:52:27 PM
 */

if(!($core->input['action'])) {

    $budgetsdata = ($core->input['budget']);
    $financialbudget_year = $budgetsdata['year'];
    $financialbudget_prevyear = $financialbudget_year - 1;
    $financialbudget_prev2year = $financialbudget_year - 2;
    $financialbudget_prev3year = $financialbudget_year - 3;
    $affid = $budgetsdata['affiliates'];
    $budgettypes = $budgetsdata['budgetypes'];
    $dummy_budget = new Budgets();
    $filters = $dummy_budget->generate_budgetline_filters();
    if(is_array($filters)) {
        foreach($filters as $key => $val) {
            if(empty($budgetsdata[$key])) {
                if(empty($val)) {
                    unset($budgetsdata[$key]);
                    continue;
                }
                $budgetsdata[$key] = $val;
            }
            else {
                $budgetsdata[$key] = array_intersect($val, $budgetsdata[$key]);
            }
        }
    }
    $affid = $budgetsdata['affiliates'];
    /* if no budget selected */
    if(empty($budgettypes)) {
        error($lang->errorselectbudgettype, $_SERVER['HTTP_REFERER']);
    }

    $filters['year'] = $budgetsdata['year'];
    if(!empty($budgetsdata['affiliates'])) {
        $filters['affid'] = $budgetsdata['affiliates'];
    }
    $financialbudget = FinancialBudget::get_data($filters, array('simple' => false, 'returnarray' => true, 'operators' => array('affid' => IN)));
    if(is_array($financialbudget)) {
        $output = FinancialBudget::parse_financialbudget(array('budgettypes' => $budgettypes, 'affid' => $affid, 'tocurrency' => $budgetsdata['toCurrency'], 'year' => $financialbudget_year, 'filter' => array_keys($financialbudget)));
        $budgetypes = array('financialadminexpenses', 'investmentfollowup', 'headcount', 'forecastbalancesheet', 'profitlossaccount', 'overduereceivables', 'trainingvisits', 'bank');
        foreach($budgetypes as $type) {
            if(isset($output[$type]) && !empty($output[$type])) {
                $budgettitle = $lang->$type;
                if($type == 'forecastbalancesheet' || $type == 'overduereceivables' || $type == 'bank' || $type == 'trainingvisits') {
                    $outputdata[$type] = $output[$type]['data'];
                    ${"budgeting_".$type} .='<p class="thead">'.$budgettitle.'</p>';
                    ${"budgeting_".$type} .= $outputdata[$type];
                }
                else {
                    ${"budgeting_".$type} = '<table width="100%">';
                    $outputdata[$type] = $output[$type]['data'];
                    $header_budyef = $output[$type]['budyef'];
//                    $header_prevbudget = $output[$type]['prevbudget'];
//                    $header_prevbudget_year = $output[$type]['prevbudget_years'];
                    $header_variations = $output[$type]['variations'];
                    $header_variations_years = $output[$type]['variations_years'];
                    eval("\$budgeting_".$type." .= \"".$template->get('budgeting_financialbudget_header')."\";");
                    ${"budgeting_".$type} .= $outputdata[$type];
                    ${"budgeting_".$type} .= '</table><br/>';
                }
            }
        }
    }
    else {
        redirect($_SERVER['HTTP_REFERER'], 2, $lang->nomatchfound);
    }
    eval("\$budgeting_headcount = \"".$template->get('budgeting_previewfinbudget')."\";");
    output_page($budgeting_headcount);
}