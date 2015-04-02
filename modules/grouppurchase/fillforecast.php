<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: quantitiesforecastlines.php
 * Created:        @rasha.aboushakra    Dec 15, 2014 | 1:17:05 PM
 * Last Update:    @rasha.aboushakra    Dec 15, 2014 | 1:17:05 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['grouppurchase_canUpdateForecast'] == 0) {
    // error($lang->sectionnopermission);
}
if(!$core->input['action']) {
    $forecast_data = $core->input['forecast'];
    $uid = intval($forecast_data['onBehalf']);
    if($forecast_data['onBehalf'] == 0) {
        $uid = $core->user['uid'];
    }

    if(empty($forecast_data) || array_search("0", $forecast_data) !== false) {
        redirect('index.php?module=grouppurchase/createforecast');
    }
    $affiliate = new Affiliates($forecast_data['affid']);
    $supplier = new Entities($forecast_data['spid']);
    $supplier_segments = array_filter($supplier->get_segments());

    $allowed_saletypes = array('localindent', 'proxydirect', 'localexstock', 'localreinvoicing');
    $saletypes = SaleTypes::get_data(array('name' => $allowed_saletypes), array('operators' => array('name' => 'IN')));
    $rowid = 1;

    for($i = 1; $i < 13; $i++) {
        $months[$i] = 'month'.$i;
    }

    $grouppurchaseforecast = GroupPurchaseForecast::get_data(array('affid' => $forecast_data['affid'], 'year' => $forecast_data['year'], 'spid' => $forecast_data['spid']));
    if(is_object($grouppurchaseforecast)) {
        $gpforecastlines = $grouppurchaseforecast->get_forecastlines($uid);
    }
    $budget = Budgets::get_budget_bydata($forecast_data);
    
    /* Read data from existing forecast lines. */
    if(is_array($gpforecastlines)) {
        foreach($gpforecastlines AS $gpforecastline) {
            $fields = array_merge(array('gpfid', 'gpflid', 'inputChecksum', 'pid', 'psid', 'saleType', 'businessMgr'), $months);
            foreach($fields as $field) {
                switch($field) {
                    case 'pid':
                        $forecastline['pid'] = $gpforecastline->$field;
                        $product = new Products($forecastline['pid']);
                        $forecastline['psid'] = $product->get_genericproduct()->get_segment()->psid;
                        $forecastline['productName'] = $product->name;
                        break;
                    case 'saleType':
                        $saletype_selectlist = parse_selectlist("forecastline[".$rowid."][saleType]", "", $saletypes, $gpforecastline->$field);
                        break;
                    default:
                        $forecastline[$field] = $gpforecastline->$field;
                        if(in_array($field, $months)) {
                            $total[$field] += $gpforecastline->$field;
                            $forecastline['quantity'] +=$gpforecastline->$field;
                            $forecastline[$field] = round($forecastline[$field], 2);
                            $forecastline['quantity'] = round($forecastline['quantity'], 2);
                            /* disable input fields on update for past months */
                            $date_str = $forecast_data['year'].'-'.trim($field, 'month');
//                            if(strtotime("$date_str") < strtotime('first day of '.date('F Y'))) {
//                                $readonly[$field] = 'readonly="readonly"';
//                            }
                        }
                }
            }
            $segments_selectlist = '';
            if(count($supplier_segments) > 1) {
                $segments_selectlist = parse_selectlist('forecastline['.$rowid.'][psid]', 3, $supplier_segments, $forecastline['psid'], null, null, array('placeholder' => 'Overwrite Segment'));
            }
            eval("\$forecastlines .= \"".$template->get('grouppurchase_fill_forecastlines')."\";");
            unset($forecastline, $readonly);
            $rowid++;
        }
    }
    /* If there's no group purchase forecast lines
     * Upon opening the forecast page for the 1st time, read data from commercial budgets and parse the rows accordingly */
    else {
        if(isset($budget['bid']) && !empty($budget['bid'])) {
            $sql = "SELECT businessMgr, pid, psid, saleType, SUM(quantity*s1Perc/100) AS s1quantity, SUM(quantity*s2Perc/100) AS s2quantity FROM budgeting_budgets_lines WHERE businessMgr=".$uid." AND bid=".$budget['bid']." AND saletype IN (".implode(', ', array_keys($saletypes)).") GROUP by pid, psid, saleType";
            $query = $db->query($sql);
            if($db->num_rows($query) > 0) {
                $fields = array('pid', 'saleType', 's1quantity', 's2quantity');
                while($line = $db->fetch_assoc($query)) {
                    foreach($fields as $field) {
                        switch($field) {
                            case 's1quantity':
                                for($i = 1; $i < 7; $i ++) {
                                    $forecastline[$months[$i]] = ($line['s1quantity'] / 6 );
                                    $total[$months[$i]] += $forecastline[$months[$i]];
                                    $date_str = $forecast_data[year].'-'.$i;
//                                    if(strtotime("$date_str") < strtotime('first day of '.date('F Y'))) {
//                                        $readonly[$months[$i]] = 'readonly = "readonly"';
//                                    }
                                }
                                break;
                            case 's2quantity':
                                for($i = 7; $i < 13; $i++) {
                                    $forecastline[$months[$i]] = $line['s2quantity'] / 6;
                                    $total[$months[$i]] += $forecastline[$months[$i]];
                                    $date_str = $forecast_data[year].'-'.$i;
//                                    if(strtotime("$date_str") < strtotime('first day of '.date('F Y'))) {
//                                        $readonly[$months[$i]] = 'readonly = "readonly"';
//                                    }
                                }
                                break;
                            case 'saleType';
                                $saletype_selectlist = parse_selectlist("forecastline[".$rowid."][saleType]", "", $saletypes, $line['saleType']);
                                break;
                            case 'pid':
                                $product = new Products($line['pid']);
                                $forecastline['pid'] = $line['pid'];
                                $forecastline['psid'] = $line['psid'];
                                if(empty($forecastline['psid'])) {
                                    $forecastline['psid'] = $product->get_genericproduct()->get_segment()->psid;
                                }
                                $forecastline['productName'] = $product->name;
                        }
                    }
                    $forecastline['quantity'] = $line['s1quantity'] + $line ['s2quantity'];
                    $segments_selectlist = '';
                    if(count($supplier_segments) > 1) {
                        $segments_selectlist = parse_selectlist('forecastline['.$rowid.'][psid]', 3, $supplier_segments, $forecastline['psid'], null, null, array('placeholder' => 'Overwrite Segment'));
                    }
                    foreach($months as $month) {
                        $forecastline[$month] = round($forecastline[$month], 2);
                    }
                    $forecastline['inputChecksum'] = generate_checksum('gp');
                    $forecastline['quantity'] = round($forecastline['quantity'], 2);
                    eval("\$forecastlines .= \"".$template->get('grouppurchase_fill_forecastlines')."\";");
                    $rowid++;
                }
            }
            else {
                for($month = 1; $month <= 12; $month++) {
                    $forecastline['month'.$month] = 0;
                }
                $forecastline['inputChecksum'] = generate_checksum('gp');
                $saletype_selectlist = parse_selectlist("forecastline[".$rowid."][saleType]", "", $saletypes, "");
                eval("\$forecastlines = \"".$template->get('grouppurchase_fill_forecastlines')."\";");
            }
        }
        else {
            for($month = 1; $month <= 12; $month++) {
                $forecastline['month'.$month] = 0;
            }
            $forecastline['inputChecksum'] = generate_checksum('gp');
            $saletype_selectlist = parse_selectlist("forecastline[".$rowid."][saleType]", "", $saletypes, "");
            eval("\$forecastlines .= \"".$template->get('grouppurchase_fill_forecastlines')."\";");
        }
    }
    foreach($months as $month) { /* output total row */
        $total_output .= '<td class = "border_right" align = "center"><span style = "font-weight:bold;" id = "forecastline_total_'.$month.'">'.number_format($total[$month], 2).'</span></td>';
    }
    eval("\$fillforecast = \"".$template->get('grouppurchase_fill_forecast')."\";");
    output_page($fillforecast);
}
else if($core->input['action'] == 'ajaxaddmore_forecastlines') {
    $rowid = intval($core->input['value']) + 1;
    $forecast_data = $core->input['ajaxaddmoredata'];
    $affiliate = new Affiliates($forecast_data['affid']);
    $forecastline['inputChecksum'] = generate_checksum('gp');
    $saletypes = SaleTypes::get_data();
    $saletype_selectlist = parse_selectlist("forecastline[".$rowid."][saleType]", "", $saletypes, "");

    for($month = 1; $month <= 12; $month++) {
        $forecastline['month'.$month] = 0;
    }
    eval("\$row = \"".$template->get('grouppurchase_fill_forecastlines')."\";");
    output($row);
}
else if($core->input['action'] == 'do_perform_fillforecast') {
    unset($core->input['identifier'], $core->input['module'], $core->input['action']);
    $gpforecast = new GroupPurchaseForecast();
    $gpforecast->set($core->input);
    $gpforecast->save();
    switch($gpforecast->get_errorcode()) {
        case 0:
        case 1:
            if(isset($core->input['notify']) && $core->input ['notify'] == 1) {
                $mailer = new Mailer();
                $mailer = $mailer->get_mailerobj();
                $mailer->set_type();
                $mailer->set_from(array('name' => 'tony.assaad', 'email' => 'tony.assaad@ocos.local'));
                $mailer->set_subject('');
                $mailer->set_message('');
                $mailer->set_to('');
                $mailer->send();
            }
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 2:
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
            break;
    }
}
?>