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
if($core->usergroup['grouppurchase_canGenerateReports'] == 0) {
    error($lang->sectionnopermission);
}
if(!($core->input['action'])) {
    if(!is_null($core->input['stuffings'])) {
        $forecast = unserialize(base64_decode($core->input['stuffings']));
        $core->input['forecast'] = $forecast;
    }
    $report_type = $core->input['forecast']['reporttype'];

    $filter_where = GroupPurchaseForecast::get_grppurchpermissions($core->input['forecast']);
    $filter_where['year'] = $core->input['forecast']['years'];

    $dal_config = array(
            'operators' => array('affid' => 'in', 'spid' => 'in', 'year' => 'in'),
            'simple' => false,
            'returnarray' => true
    );
    $purchase_forcastobjs = GroupPurchaseForecast::get_data(array_filter($filter_where), $dal_config);

    if($report_type == 'basic') {
        for($i = 1; $i <= 12; $i++) {
            $groupurchase_months[$i] = 'month'.$i;
            $dateObj = DateTime::createFromFormat('!m', $i);
            $groupurchase_monthname[$i] = $dateObj->format('F');
        }
        $groupurchase['monthead'] .= '<tr class = "thead"><th style = "vertical-align:central; padding:2px;  border-bottom: dashed 1px #CCCCCC;" align = "center" class = "border_left">'.$lang->product.'</th>';
        $groupurchase['monthead'] .='<th style = "vertical-align:central; padding:2px;border-bottom: dashed 1px #CCCCCC;" align = "center" class = "border_left">'.$lang->saletype.'</th>';
        $groupurchase['monthead'] .= '<th class = "border_left">'.implode('</th><th class = "border_left"> ', $groupurchase_monthname).'</th></tr>';
        $groupurchase['summonth'] = 'SUM('.implode('), SUM(', $groupurchase_months).')';

        $numfmt = new NumberFormatter($lang->settings['locale'], NumberFormatter::DECIMAL);
        $numfmt->setPattern("#0.###");
        if(is_array($purchase_forcastobjs)) {
            foreach($purchase_forcastobjs as $groupforecast) {
                $gplines_filter = GroupPurchaseForecastLines::get_forecastlinespermisiions($groupforecast);
                $gplines_filter['gpfid'] = $groupforecast->gpfid;
                $gpforecastlines = GroupPurchaseForecastLines::get_data($gplines_filter, array('returnarray' => true, 'simple' => false, 'operators' => array('psid' => 'IN')));
                if(is_array($gpforecastlines)) {
                    foreach($gpforecastlines as $grouppurchasline) {
                        $product_obj = new Products($grouppurchasline->pid);
                        $salestype = new SaleTypes($grouppurchasline->saleType);

                        foreach($groupurchase_months as $monthval) {
                            $group_purchase['monthval'] .= '<td class = "smalltext" class = "border_left">'.$numfmt->format($grouppurchasline->$monthval).'</td>';
                        }
                        if(is_object($saletypename)) {
                            $saletypename = $salestype->get_displayname();
                        }
                        eval("\$grouppurchase_report_rows .= \"".$template->get('grouppurchase_report_rows')."\";");
                        unset($group_purchase['monthval']);
                    }
                }
            }
        }
        if(is_array($filter_where['year'])) {
            $reportyears .='<br/><small> '.implode(', ', $filter_where['year']).'</small>';
        }
        $reporttitle = '<h1>'.$lang->quantitiesforecast.$reportyears.'</h1>';

        eval("\$grouppurchase_report = \"".$template->get('grouppurchase_report')."\";");
        output_page($grouppurchase_report);
    }
    if($report_type == 'dimensional') {
        $forecastdata = $core->input['forecast'];
        if(is_array($purchase_forcastobjs)) {
            foreach($purchase_forcastobjs as $groupforecast) {
                $gplines_filter = GroupPurchaseForecastLines::get_forecastlinespermisiions($groupforecast);
                $gplines_filter['gpfid'] = $groupforecast->gpfid;

                $gpforecastlines = GroupPurchaseForecastLines::get_data($gplines_filter, array('returnarray' => true, 'simple' => false, 'operators' => array('psid' => 'IN')));
                if(is_array($gpforecastlines)) {
                    foreach($gpforecastlines as $grouppurchasline) {
                        $gplines_data[$grouppurchasline->gpflid] = $grouppurchasline->get();
                        $gplines_data[$grouppurchasline->gpflid]['affid'] = $groupforecast->affid;
                        $gplines_data[$grouppurchasline->gpflid]['spid'] = $groupforecast->spid;
                        $gplines_data[$grouppurchasline->gpflid]['year'] = $groupforecast->year;
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
        $dimensions = explode(',', $forecastdata['dimension'][0]); /* Dimensional Report Settings - END */

        $dimensionalreport = new DimentionalData();
        $dimensionalreport->set_dimensions(array_combine(range(1, count($dimensions)), array_values($dimensions)));
        $dimensionalreport->set_requiredfields($required_fields);
        if(!empty($gplines_data)) {
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
        }
        else {
            redirect($_SERVER['HTTP_REFERER'], 2, $lang->nomatchfound);
        }
        $reporttitle = '<h1>'.$lang->forecastedquantities.'<br/><small> '.implode(', ', $filter_where['year']).'</small></h1>';
        eval("\$grouppurchase_report = \"".$template->get('grouppurchase_report')."\";");
        output_page($grouppurchase_report);
    }
}

