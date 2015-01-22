<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: previewforecast.php
 * Created:        @tony.assaad    Jan 20, 2015 | 3:58:00 PM
 * Last Update:    @tony.assaad    Jan 20, 2015 | 3:58:00 PM
 */

if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}
if($core->usergroup['grouppurchasing_canViewAllForecasts'] == 0) {
// error($lang->sectionnopermission);
}
if(!($core->input['action'])) {
    $report_type = $core->input['forecast']['reporttype'];

//    if(GroupPurchaseForecast::canview_permissions($core->input['forecast']) == false) {
//        error($lang->sectionnopermission);
//    }

    if($report_type == 'basic') {
        for($i = 1; $i <= 12; $i++) {
            $groupurchase_months[$i] = 'month'.$i;
            $dateObj = DateTime::createFromFormat('!m', $i);
            $groupurchase_monthname[$i] = $dateObj->format('F');
        }
        $groupurchase['monthead'] .= '<tr class="thead"><th style="vertical-align:central; padding:2px;  border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">'.$lang->product.'</th>';
        $groupurchase['monthead'] .='<th style = "vertical-align:central; padding:2px;border-bottom: dashed 1px #CCCCCC;" align = "center" class = "border_left">'.$lang->saletype.'</th>';
        $groupurchase['monthead'] .= '<th class="border_left">'.implode('</th><th  class="border_left">', $groupurchase_monthname).'</th></tr>';
        $groupurchase['summonth'] = 'SUM('.implode('),SUM(', $groupurchase_months).')';

        $dal_config = array(
                'operators' => array('affid' => 'in', 'spid' => 'in', 'year' => '='),
                'simple' => false,
                'returnarray' => true
        );
        $purchase_forcastobjs = GroupPurchaseForecast::get_grouppurchaseforecast($core->input['forecast']);

        //  $purchase_forcastobjs = GroupPurchaseForecast::get_data(array('affid' => $groupforecast_filters, 'year' => $core->input['forecast']['years'], 'spid' => $core->input['forecast']['suppliers']), $dal_config);

        /* stil under development... */
        if(is_array($purchase_forcastobjs)) {
            foreach($purchase_forcastobjs as $purchase_forcastobj) {
                $forecast_lines = GroupPurchaseForecastLines::get_data('gpfid='.$purchase_forcastobj->gpfid, array('returnarray' => true));
//                if(is_array($forecast_lines)) {
//                    foreach($forecast_lines as $forecast_line) {
//                        $filter = $forecast_line->filter_securityview();
//                    }
//                }
// filter of bm send to the query
            }

            $query = 'SELECT  pid,saleType,'.$groupurchase['summonth'].' FROM grouppurchase_forecastlines WHERE '.GroupPurchaseForecast::PRIMARY_KEY.' IN('.implode(',', array_keys($purchase_forcastobjs)).')'.$filter.' Group BY pid,saleType';
            $sql = $db->query($query);
            while($forecaslines = $db->fetch_assoc($sql)) {
                $product_obj = new Products($forecaslines['pid']);
                $slaletype = new SaleTypes($forecaslines['saleType']);
                foreach($groupurchase_months as $monthval) {
                    $groupurchase['monthval'] .= '<td class="smalltext" class="border_left">'.$forecaslines['SUM('.$monthval.')'].'</td>';
                }
                eval("\$grouppurchase_report_rows .= \"".$template->get('grouppurchase_report_rows')."\";");
                unset($groupurchase['monthval']);
            }
        }
        $reporttitle = $lang->grouppurchasetabular;
        eval("\$grouppurchase_report = \"".$template->get('grouppurchase_report')."\";");
        output_page($grouppurchase_report);
    }
    if($report_type == 'dimensional') {
        $forecastdata = $core->input['forecast'];
        unset($groupurchase[monthead]);
        $dal_config = array(
                'returnarray' => true,
                'operators' => array('affid' => 'IN', 'spid' => 'IN'),
        );
        $grouppurchaseforecasts = GroupPurchaseForecast::get_data(array('year' => $forecastdata['years'], 'affid' => $forecastdata['affiliates'], 'spid' => $forecastdata['suppliers']), $dal_config);
        //check
        if(is_array($grouppurchaseforecasts)) {
            foreach($grouppurchaseforecasts as $groupforecast) {
                $gpforecastlines = $groupforecast->get_forecastlines();
                if(is_array($gpforecastlines)) {
                    foreach($gpforecastlines as $grouppurchasline) {
                        $gplines_data[$grouppurchasline->gpflid] = $grouppurchasline->get();
                        $gplines_data[$grouppurchasline->gpflid]['affid'] = $groupforecast->affid;
                        $gplines_data[$grouppurchasline->gpflid]['spid'] = $groupforecast->spid;
                    }
                }
            }
        }
        /* Dimensional Report Settings - START */
        for($i = 0; $i < 12; $i++) {
            $j = $i + 1;
            $required_fields[$i] = 'month'.$j;
            $formats[$required_fields[$i]] = array('style' => NumberFormatter::DECIMAL, 'pattern' => '#,##0.00');
        }
        $dimensions = explode(',', $forecastdata['dimension'][0]);
        /* Dimensional Report Settings - END */

        $dimensionalreport = new DimentionalData();
        $dimensionalreport->set_dimensions(array_combine(range(1, count($dimensions)), array_values($dimensions)));
        $dimensionalreport->set_requiredfields($required_fields);

        $dimensionalreport->set_data($gplines_data);
        $gpforecat_report .= '<table width="100%" class="datatable">';
        $gpforecat_report .= '<tr class="thead">';
        for($i = 1; $i <= 12; $i++) {
            $groupurchase_months[$i] = 'month'.$i;
            $dateObj = DateTime::createFromFormat('!m', $i);
            $groupurchase_monthname[$i] = $dateObj->format('F');
        }
        $gpforecat_report .= '<th class="border_left" style="width:7.5%;"></th><th class="border_left" style="width:7.5%;">'.implode('</th><th  class="border_left" style="width:7.5%;">', $groupurchase_monthname).'</th>';
        $gpforecat_report .= '</tr>';
        $gpforecat_report .= $dimensionalreport->get_output(array('outputtype' => 'table', 'noenclosingtags' => true, 'formats' => $formats));
        $gpforecat_report .= '</table>';
        eval("\$grouppurchase_report = \"".$template->get('grouppurchase_report')."\";");
        output_page($grouppurchase_report);
    }
}

