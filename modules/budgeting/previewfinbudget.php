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
    $affid = $budgetsdata['affilliates'];
    $budgettypes = $budgetsdata['budgetypes'];

    $financialbudget = FinancialBudget::get_data(array('affid' => $affid, 'year' => $financialbudget_year), array('simple' => false, 'returnarray' => true, 'operators' => array('affid' => IN)));
    if(is_array($financialbudget)) {
        $output = FinancialBudget::parse_financialbudget(array('budgettypes' => $budgettypes, 'affid' => $affid, 'tocurrency' => $budgetsdata['toCurrency'], 'year' => $financialbudget_year, 'filter' => array_keys($financialbudget)));

        $budgetypes = array('financialadminexpenses', 'investmentfollowup', 'headcount', 'forecastbalancesheet');
        foreach($budgetypes as $type) {
            if(isset($output[$type]) && !empty($output[$type])) {
                if($type == 'forecastbalancesheet') {
                    $outputdata[$type] = $output[$type]['data'];
                    ${"budgeting_".$type} .='<p class="thead">Forecast Balance Sheet</p>';
                    ${"budgeting_".$type} .= $outputdata[$type];
                }
                else {
                    $budgettitle = $lang->$type;
                    ${"budgeting_".$type} = '<table width="100%">';
                    $outputdata[$type] = $output[$type]['data'];
                    $header_actual = $output[$type]['headeractual'];
                    $header_percentage = $output[$type]['headerpercentage'];
                    $header_variation = $output[$type]['headervariation'];
                    eval("\$budgeting_".$type." .= \"".$template->get('budgeting_investheader')."\";");
                    ${"budgeting_".$type} .= $outputdata[$type];
                    if($type != financialadminexpenses) {
                        ${"budgeting_".$type} .= '</table><br/>';
                    }
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