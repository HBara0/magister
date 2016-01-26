<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Stock Aging Report from OB
 * $id: ob_report_stockaging.php
 * Created:        @zaher.reda    Sep 2, 2013 | 1:04:11 PM
 * Last Update:    @zaher.reda    Sep 2, 2013 | 1:04:11 PM
 */
exit;
require '../inc/init.php';

define('AUTHCODE', 'X1iIMm7pG06ip6o95HEa');
if($core->input['authCode'] == AUTHCODE) {
    $currency_obj = new Currencies('USD');
    $date_info = getdate_custom(TIME_NOW);

    $db_info = array('database' => 'openbrav_production', 'username' => 'openbrav_appuser', 'password' => '8w8;MFRy4g^3', 'engine' => 'postgre');

    $affiliates_index = array(
            'C08F137534222BD001345BAA60661B97' => 19, //Orkila Tunisia
            '0B366EFAE0524FDAA97A1322A57373BB' => 22, //Orkila East Africa
            'DA0CE0FED12C4424AA9B51D492AE96D2' => 11, //Orkila Nigeria
            'F2347759780B43B1A743BEE40BA213AD' => 23, //Orkila Ghana
            'BD9DC2F7883B4E11A90B02A9A47991DC' => 1, //Orkila Lebanon
            '933EC892369245E485E922731D46FCB1' => 20, //Orkila Senegal
            '51FB1280AB104EFCBBB982D50B3B7693' => 21, //Orkila CI
            '7AD08388D369403A9DF4B8240E3AD7FF' => 27 //Orkila International
    );

    $affiliates_addrecpt = array(
            19 => array(244, 'audrey.sacy'),
            22 => array(248, 246, 287, 270, 'audrey.sacy'),
            23 => array('zadok.oppong-boahene', 'courage.dzandu', 322, 321, 'audrey.sacy'),
            1 => array(12, 333, 182, 43, 'audrey.sacy'),
            21 => array(63, 158, 'audrey.sacy'),
            27 => array(12, 333, 68, 67, 'audrey.sacy'),
            20 => array('michel.mbengue', 'abdoulaye.lo', 'audrey.sacy'),
            11 => array(323, 108, 186, 335, 184, 111, 109, 280, 326, 295, 289, 187, 112, 113, 312, 107, 'audrey.sacy')
    );

    $integration = new IntegrationOB($db_info, 'C08F137534222BD001345B7B2E8F182D', $affiliates_index, 3, array('from' => 'last year'));

    $status = $integration->get_status();
    if(!empty($status)) {
        echo 'Error';
        exit;
    }

    $report_options = array('roundto' => 0);

    $output_fields = array(
//			'manager' => 'Business Manager',
            'product' => array('source' => 'product', 'attribute' => 'name', 'title' => 'Product'),
            'supplier' => array('source' => 'supplier', 'attribute' => 'value', 'title' => 'Supplier'),
            'warehouse' => array('source' => 'warehouse', 'attribute' => 'value', 'title' => 'Warehouse'),
            'category' => array('source' => array('product', 'category'), 'attribute' => 'value', 'title' => 'Segment'),
            //'packaging' => array('source' => array('transaction', 'attributes'), 'attribute' => 'packaging', 'title' => 'Packaging'),
            //'quantity' => 'Quantity',
            'quantity' => array('source' => null, 'title' => 'Stock Qty', 'numformat' => true),
            'uom' => array('source' => array('product', 'uom'), 'attribute' => 'uomsymbol', 'title' => 'UoM'),
            //'cost' => 'Cost',
            'cost' => array('source' => 'entries', 'attribute' => 'cost', 'title' => 'Cost', 'numformat' => true),
            'costusd' => array('source' => null, 'title' => 'Cost (USD)', 'numformat' => true),
            'range1cost' => array('source' => array('entries', 'costs'), 'attribute' => 1, 'title' => '0-90<br />Amt', 'numformat' => true, 'styles' => 'background-color: #ABD25E;'),
            'range1costusd' => array('source' => null, 'attribute' => 1, 'title' => '0-90<br />Amt USD', 'numformat' => true, 'styles' => 'background-color: #ABD25E;'),
            'range1qty' => array('source' => array('entries', 'qty'), 'attribute' => 1, 'title' => '0-90<br />Qty', 'numformat' => true, 'styles' => 'background-color: #ABD25E;'),
            //'range2cost' => array('source' => array('entries', 'cost'), 'attribute' => 2, 'title' => '30-59<br />Amt', 'numformat' => true, 'styles' => 'background-color: #B8E1F2;'),
            //'range2qty' => array('source' => array('entries', 'qty'), 'attribute' => 2, 'title' => '30-59<br />Qty', 'numformat' => true, 'styles' => 'background-color: #B8E1F2;'),
            //'range3cost' => array('source' => array('entries', 'cost'), 'attribute' => 3, 'title' => '60-89<br />Amt', 'numformat' => true, 'styles' => 'background-color: #F2EB80;'),
            //'range3qty' => array('source' => array('entries', 'qty'), 'attribute' => 3, 'title' => '60-89<br />Qty', 'numformat' => true, 'styles' => 'background-color: #F2EB80;'),
            'range4cost' => array('source' => array('entries', 'costs'), 'attribute' => 4, 'title' => '90-180<br />Amt', 'numformat' => true, 'styles' => 'background-color: #F8C830;'),
            'range4costusd' => array('source' => null, 'attribute' => 4, 'title' => '90-180<br />Amt USD', 'numformat' => true, 'styles' => 'background-color: #F8C830;'),
            'range4qty' => array('source' => array('entries', 'qty'), 'attribute' => 4, 'title' => '90-180<br />Qty', 'numformat' => true, 'styles' => 'background-color: #F8C830;'),
            'range5cost' => array('source' => array('entries', 'costs'), 'attribute' => 5, 'title' => '> 180<br />Amt', 'numformat' => true, 'styles' => 'background-color: #F1594A;'),
            'range5costusd' => array('source' => null, 'attribute' => 5, 'title' => '> 180<br />Amt USD', 'numformat' => true, 'styles' => 'background-color: #F1594A;'),
            'range5qty' => array('source' => array('entries', 'qty'), 'attribute' => 5, 'title' => '> 180<br />Qty', 'numformat' => true, 'styles' => 'background-color: #F1594A;')
    );

    $summary_categories = array('category' => 'm_product_category_id', 'warehouse' => 'm_warehouse_id', 'supplier' => 'c_bpartner_id');
    $summary_reqinfo = array('quantity', 'cost', 'range1cost', 'range1qty', /* 'range2cost', 'range2qty', 'range3cost', 'range3qty', */ 'range4cost', 'range4qty', 'range5cost', 'range5qty');
    $summary_order_attr = 'cost';
    $maintable_hiddencols = array('supplier', 'warehouse', 'category');

    $total_types = array('quantity', 'cost', 'costusd', 'range1cost', 'range1qty', 'range1costusd', /* 'range2cost', 'range2qty', 'range3cost', 'range3qty', */ 'range4cost', 'range4qty', 'range5cost', 'range5qty');
    foreach($affiliates_index as $orgid => $affid) {
        $output = '';
        $inputs = array();
        $totals = $summaries = array();
        $affiliateobj = new Affiliates($affid, false);
        $affiliate = $affiliateobj->get();
        $affiliate['currency'] = $affiliateobj->get_country()->get_maincurrency()->get()['alphaCode'];

        $rawinputs = $integration->get_fifoinputs(array($orgid), array('hasqty' => true));
        $fxrates['usd'] = $currency_obj->get_latest_fxrate($affiliate['currency']);

        $output = '<h1>'.$affiliate['name'].' - Week '.$date_info['week'].' ( '.$affiliate['currency'].' | USD FX Rate:'.$fxrates['usd'].')</h1>';
        foreach($rawinputs as $key => $input) {
            //$inputs[$input['product']['m_product_id']]['product'] = $input['product'];

            if($input['stack']['daysinstock'] < 29) {
                $range = 1;
            }
            elseif($input['stack']['daysinstock'] < 59) {
                $range = 1;
            }
            elseif($input['stack']['daysinstock'] < 90) {
                $range = 1;
            }
            elseif($input['stack']['daysinstock'] < 180) {
                $range = 4;
            }
            else {
                $range = 5;
            }
            $rawinputs[$key]['entries']['qty'][$range] += $input['stack']['remaining_qty'];
            $rawinputs[$key]['entries']['costs'][$range] += $input['stack']['remaining_cost'];

            $rawinputs[$key]['entries']['cost'] += $input['stack']['remaining_cost'];
        }

        $order_attr = 'cost';
        ${$order_attr} = array();
        foreach($rawinputs as $data_key => $data_row) {
            ${$order_attr}[$data_key] = $data_row['entries'][$order_attr];
        }
        array_multisort(${$order_attr}, SORT_DESC, $rawinputs);

        $output .= '<table width="100%" cellspacing="0" cellpadding="5" style="border: 1px solid #CCC; font-size: 10px;" border="0">';
        $output .= '<tr>';
        foreach($output_fields as $field => $configs) {
            if(in_array($field, $maintable_hiddencols)) {
                continue;
            }
            if(is_array($configs)) {
                $output .= '<th style="background: #91b64f;">'.$configs['title'].'</th>';
            }
            else {
                $output .= '<th style="background: #91b64f;">'.$configs.'</th>';
            }
        }
        $output .= '</tr>';

        reset($output_fields);
        foreach($rawinputs as $id => $input) {
            $output .= '<tr>';
            foreach($output_fields as $field => $configs) {
                $output_td_style = '';
                if(is_array($configs) && !is_null($configs['source'])) {
                    if(is_array($configs['source'])) {
                        $source_data = '';
                        foreach($configs['source'] as $source) {
                            if(empty($source_data)) {
                                $source_data = $input[$source];
                            }
                            else {
                                $source_data = $source_data[$source];
                            }
                        }
                        $output_value = $source_data[$configs['attribute']];
                    }
                    else {
                        $output_value = $input[$configs['source']][$configs['attribute']];
                    }

                    if(empty($output_value) || strstr($output_value, 'Orkila')) {
                        if($field == 'supplier') {
                            $product = new Products($db->fetch_field($db->query('SELECT localId FROM integration_mediation_products WHERE foreignSystem=3 AND foreignName="'.$input['product']['name'].'"'), 'localId'));
                            if(!is_object($product)) {
                                $product = new Products($db->fetch_field($db->query('SELECT pid FROM products WHERE name="'.$input['product']['name'].'"'), 'pid'));
                            }
                            $output_value = $product->get_supplier()->get()['companyNameShort'];
                            if(empty($output_value)) {
                                $output_value = $product->get_supplier()->get()['companyName'];
                                if(empty($output_value)) {
                                    $output_value = 'Information Unavailable';
                                }
                            }
                            $input['supplier']['name'] = $input['supplier']['value'] = $output_value;
                            $input['supplier']['c_bpartner_id'] = md5($output_value);
                        }
                    }

                    if(in_array($field, array_keys($summary_categories))) {
                        foreach($summary_categories as $category => $attribute) {
                            $summaries[$category][$input[$category][$attribute]]['name'] = $input[$category]['value'];
                        }
                    }

                    if(in_array($field, $summary_reqinfo)) {
                        foreach($summary_categories as $category => $attribute) {
                            if(in_array($field, array('range1qty', 'range2qty', 'range3qty', 'range4qty', 'range5qty'))) {
                                $summaries[$category][$input[$category][$attribute]][$field][$input['product']['uom']['uomsymbol']] += $output_value;
                            }
                            else {
                                $summaries[$category][$input[$category][$attribute]][$field] += $output_value;
                            }
                        }
                    }

                    if(in_array($field, $total_types)) {
                        $totals[$field] += $output_value;
                    }

                    if($configs['numformat'] == true) {
                        $output_value = number_format($output_value, $report_options['roundto'], '.', ' ');
                        $output_td_style = ' text-align: right;';
                    }

                    if($configs['isdate'] == true) {
                        if(strstr($output_value, '.')) {
                            $output_valueobj = DateTime::createFromFormat('Y-m-d G:i:s.u', $output_value);
                        }
                        else {
                            $output_valueobj = DateTime::createFromFormat('Y-m-d G:i:s', $output_value);
                        }

                        if($output_valueobj != false) {
                            $output_value = $output_valueobj->format($core->settings['dateformat']);
                        }
                    }

                    if(isset($configs['styles'])) {
                        $output_td_style .= $configs['styles'];
                    }

                    if(!in_array($field, $maintable_hiddencols)) {
                        $output .= '<td style="border: 1px solid #CCC; '.$output_td_style.'">'.$output_value.'</td>';
                    }
                    unset($output_value);
                }
                else {
                    switch($field) {
//                        case 'cost':
//                            $output_value = array_sum($input['entries']['cost']);
//                            if(in_array($field, $summary_reqinfo)) {
//                                foreach($summary_categories as $category => $attribute) {
//                                    $summaries[$category][$input[$category][$attribute]][$field] += $output_value;
//                                }
//                            }
//
//                            if(in_array($field, $total_types)) {
//                                $totals[$field] += $output_value;
//                            }
//
//                            $output .= '<td style="border: 1px solid #CCC; text-align: right;">'.number_format($output_value, $report_options['roundto'], '.', ' ').'</td>';
//                            break;

                        case 'costusd':
                            $output_value = $input['stack']['remaining_cost'] / $fxrates['usd'];
                            if(in_array($field, $summary_reqinfo)) {
                                foreach($summary_categories as $category => $attribute) {
                                    $summaries[$category][$input[$category][$attribute]][$field] += $output_value;
                                }
                            }
                            $output .= '<td style="border: 1px solid #CCC; text-align: right;">'.number_format($output_value, $report_options['roundto'], '.', ' ').'</td>';

                            if(in_array($field, $total_types)) {
                                $totals[$field] += $output_value;
                            }
                            break;
                        case 'quantity':
                            $output_value = array_sum($input['entries']['qty']);
                            if(in_array($field, $summary_reqinfo)) {
                                foreach($summary_categories as $category => $attribute) {
                                    $summaries[$category][$input[$category][$attribute]][$field][$input['product']['uom']['uomsymbol']] += $output_value;
                                }
                            }

                            if(in_array($field, $total_types)) {
                                $totals[$field] += $output_value;
                            }
                            $output .= '<td style="border: 1px solid #CCC; text-align: right;">'.number_format($output_value, $report_options['roundto'], '.', ' ').'</td>';
                            break;
                        case preg_match('/^range(0-9)+costusd/i', $field):
                            $output_value = $input['entries']['costs'][$configs['attribute']] / $fxrates['usd'];
                            if(in_array($field, $summary_reqinfo)) {
                                foreach($summary_categories as $category => $attribute) {
                                    $summaries[$category][$input[$category][$attribute]][$field] += $output_value;
                                }
                            }

                            if(isset($configs['styles'])) {
                                $output_td_style .= $configs['styles'];
                            }
                            $output .= '<td style="border: 1px solid #CCC; text-align: right; '.$output_td_style.'">'.number_format($output_value, $report_options['roundto'], '.', ' ').'</td>';

                            if(in_array($field, $total_types)) {
                                $totals[$field] += $output_value;
                            }
                            break;
                    }
                }
            }
            $output .= '</tr>';
        }
        $output .= '<tr>';
        foreach($output_fields as $field => $configs) {
            if(in_array($field, $maintable_hiddencols)) {
                continue;
            }
            $output_value = $totals[$field];
            if(is_numeric($output_value)) {
                $output_value = number_format($output_value, $report_options['roundto'], '.', ' ');
            }
            $output .= '<td style="border: 1px solid #CCC; font-weight: bold; text-align: right; text-decoration:underline">'.$output_value.'</td>';
        }
        $output .= '</tr>';
        $output .= '</table>';
        /* Parse Summaries - Start */
        if(is_array($summaries)) {
            foreach($summaries as $category => $category_data) {
                $totals = array();
                $output .= '<h1>'.$output_fields[$category]['title'].' Summary</h1>';
                $output .= '<table width="100%" cellspacing="0" cellpadding="5" style="border: 1px solid #CCC; font-size: 10px;" border="0">';
                $output .= '<tr><th style="background: #91b64f;">'.$output_fields[$category]['title'].'</th>';
                foreach($summary_reqinfo as $reqinfo) {
                    $output .= '<th style="background: #91b64f;">'.$output_fields[$reqinfo]['title'].'</th>';
                }
                unset($out_field_title);
                $output .= '</tr>';

                ${$summary_order_attr} = array();
                foreach($category_data as $cat_data_key => $cat_data_row) {
                    ${$summary_order_attr}[$cat_data_key] = $cat_data_row[$summary_order_attr];
                }
                array_multisort(${$summary_order_attr}, SORT_DESC, $category_data);

                foreach($category_data as $cat_data_row) {
                    $output .= '<tr>';
                    foreach($cat_data_row as $output_key => $output_value) {
                        $output_td_style = '';
                        if(is_array($output_value)) {
                            $output_values = $output_value;
                            $output_value = '';
                            foreach($output_values as $output_key_temp => $output_value_temp) {
                                if(in_array($output_key, $total_types)) {
                                    $totals[$output_key][$output_key_temp] += $output_value_temp;
                                }
                                if($output_fields[$output_key]['numformat'] == true) {
                                    $output_value_temp = number_format($output_value_temp, $report_options['roundto'], '.', ' ');
                                    $output_td_style = ' text-align: right;';
                                }
                                $output_value .= $output_value_temp.' '.$output_key_temp.'<br />';
                            }
                        }
                        else {
                            if($output_fields[$output_key]['numformat']) {
                                if(in_array($output_key, $total_types)) {
                                    $totals[$output_key] += $output_value;
                                }
                                if($output_fields[$output_key]['numformat'] == true) {
                                    $output_value = number_format($output_value, $report_options['roundto'], '.', ' ');
                                    $output_td_style = ' text-align: right;';
                                }
                            }
                        }

                        if(isset($output_fields[$output_key]['styles'])) {
                            $output_td_style .= $output_fields[$output_key]['styles'];
                        }
                        $output .= '<td style="border: 1px solid #CCC;'.$output_td_style.'">'.$output_value.'</td>';
                    }
                    $output .= '</tr>';
                }

                /* Output summary table totals row - START */
                $output .= '<tr>';
                $output .= '<td style="border: 1px solid #CCC;"></td>';
                $output_value = null;
                foreach($summary_reqinfo as $field) {
                    if(is_array($totals[$field])) {
                        foreach($totals[$field] as $key => $output_value_temp) {
                            if(is_numeric($output_value_temp)) {
                                $output_value .= number_format($output_value_temp, $report_options['roundto'], '.', ' ').' '.$key.'<br />';
                            }
                        }
                    }
                    else {
                        $output_value = $totals[$field];
                        if(is_numeric($output_value)) {
                            $output_value = number_format($output_value, $report_options['roundto'], '.', ' ');
                        }
                    }
                    $output .= '<td style="border: 1px solid #CCC; font-weight: bold; text-align: right; text-decoration:underline">'.$output_value.'</td>';
                    $output_value = '';
                }
                $output .= '</tr>';
                /* Output summary table totals row - END */
                $output .= '</table>';
            }
        }

        /* Parse Summaries - END */
        $message = '<html><head><title>Stock Aging Report</title></head><body>';
        $message .= $output;
        $email_data = array(
                'from_email' => $core->settings['maileremail'],
                'from' => 'OCOS Mailer',
                'subject' => 'Stock Aging Report - '.$affiliate['name'].' - Week '.$date_info['week'],
                'message' => $message
        );

        $email_data['to'][] = $affiliateobj->get_generalmanager()->get()['email'];
        $email_data['to'][] = $affiliateobj->get_supervisor()->get()['email'];

        if(isset($affiliates_addrecpt[$affid])) {
            foreach($affiliates_addrecpt[$affid] as $uid) {
                if(!is_numeric($uid)) {
                    $adduser = Users::get_user_byattr('username', $uid);
                }
                else {
                    $adduser = new Users($uid);
                }

                $email_data['to'][] = $adduser->get()['email'];
            }
        }

        //print_r($email_data);
        //print_r($email_data);
        //print_r($email_data);
        $mail = new Mailer($email_data, 'php');
        unset($message);
    }
}
?>
