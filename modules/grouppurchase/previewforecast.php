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

        $groupurchase['monthead'] = '<th class="border_left">'.implode('</th><th  class="border_left">', $groupurchase_monthname).'</th>';
        $groupurchase['summonth'] = 'SUM('.implode('),SUM(', $groupurchase_months).')';

        $dal_config = array(
                'operators' => array('affid' => 'in', 'spid' => 'in', 'year' => '='),
                'simple' => false,
                'returnarray' => true
        );
        $groupforecast_filters = GroupPurchaseForecast::canview_group_permissions($core->input['forecast']);

        $purchase_forcastobjs = GroupPurchaseForecast::get_data(array('affid' => $groupforecast_filters, 'year' => $core->input['forecast']['years'], 'spid' => $core->input['forecast']['suppliers']), $dal_config);

        /* stil under development... */
        if(is_array($purchase_forcastobjs)) {
            foreach($purchase_forcastobjs as $purchase_forcastobj) {
                $forecast_lines = GroupPurchaseForecastLines::get_data('gpfid='.$purchase_forcastobj->gpfid, array('returnarray' => true));
                if(is_array($forecast_lines)) {
                    foreach($forecast_lines as $forecast_line) {
                        $filter = $forecast_line->filter_securityview();
                    }
                }

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

        eval("\$grouppurchase_report = \"".$template->get('grouppurchase_report')."\";");
        output_page($grouppurchase_report);
    }
}

