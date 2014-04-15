<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * Stock Summary Report from OB
 * $id: ob_report_stockaging.php
 * Created:        @zaher.reda    Sep 2, 2013 | 1:04:11 PM
 * Last Update:    @zaher.reda    Sep 2, 2013 | 1:04:11 PM
 */

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
            'category' => array('source' => array('product', 'category'), 'attribute' => 'value', 'title' => 'Segment', 'styles' => 'width: 5%;'),
            'supplier' => array('source' => 'supplier', 'attribute' => 'value', 'title' => 'Supplier'),
            'product' => array('source' => 'product', 'attribute' => 'name', 'title' => 'Product'),
            'warehouse' => array('source' => 'warehouse', 'attribute' => 'value', 'title' => 'Warehouse'),
//			'lot' => array('source' => array('transaction', 'attributes'), 'attribute' => 'lot', 'title' => 'Lot'),
            'packaging' => array('source' => array('transaction', 'attributes'), 'attribute' => 'packaging', 'title' => 'Packaging'),
            'initialquantity' => array('source' => 'stack', 'attribute' => 'qty', 'title' => 'Qty Received', 'numformat' => true),
            'quantitysold' => array('source' => 'stack', 'attribute' => 'soldqty', 'title' => 'Sold Qty', 'numformat' => true),
            'quantity' => array('source' => 'stack', 'attribute' => 'remaining_qty', 'title' => 'Stock Qty', 'numformat' => true),
            'uom' => array('source' => array('product', 'uom'), 'attribute' => 'uomsymbol', 'title' => 'UoM'),
            'unitcost' => array('source' => null, 'title' => 'Unit Cost', 'numformat' => true),
            'unitcostusd' => array('source' => null, 'title' => 'Unit Cost<br />(USD)', 'numformat' => true),
            'cost' => array('source' => 'stack', 'attribute' => 'remaining_cost', 'title' => 'Cost', 'numformat' => true),
            'costusd' => array('source' => null, 'title' => 'Cost (USD)', 'numformat' => true),
            'inputdate' => array('source' => 'transaction', 'attribute' => 'movementdate', 'title' => 'Entry Date', 'isdate' => true),
            'daysinstock' => array('source' => 'stack', 'attribute' => 'daysinstock', 'title' => 'In Stock<br />(Days)', 'styles' => array(150 => 'background-color: #F1594A; text-align: center;', 120 => 'background-color: #F8C830; text-align: center;', 90 => 'background-color: #F2EB80; text-align: center;', 0 => 'background-color: #ABD25E; text-align: center;')),
            'expirydate' => array('source' => array('transaction', 'attributes'), 'attribute' => 'guaranteedate', 'title' => 'Expiry Date', 'isdate' => true),
            'daystoexpire' => array('source' => array('transaction', 'attributes'), 'attribute' => 'daystoexpire', 'title' => 'Days to Expire', 'styles' => array(0 => 'background-color: #F1594A; text-align: center;', 90 => 'background-color: #F8C830; text-align: center;', 180 => 'background-color: #F2EB80; text-align: center;', 270 => 'background-color: #ABD25E; text-align: center;'))
    );

    $summary_categories = array('category' => 'm_product_category_id', 'warehouse' => 'm_warehouse_id', 'product' => 'm_product_id', 'supplier' => 'c_bpartner_id');
    $summary_reqinfo = array('quantity', 'cost', 'costusd');
    $summary_order_attr = 'costusd';

    $maintable_hiddencols = array('supplier', 'warehouse', 'category', 'product', 'packaging', 'uom', 'unitcost', 'inputdate', 'expirydate', 'daystoexpire');
    $total_types = array('initialquantity', 'quantitysold', 'quantity', 'cost', 'costusd');
    foreach($affiliates_index as $orgid => $affid) {
        $output = '';
        $totals = $summaries = array();
        $affiliateobj = new Affiliates($affid, false);
        $affiliate = $affiliateobj->get();
        $affiliate['currency'] = $affiliateobj->get_country()->get_maincurrency()->get()['alphaCode'];

        $integration->set_organisations(array($orgid));
        $inputs = $integration->get_fifoinputs(array($orgid), array('hasqty' => true));
        $fxrates['usd'] = $currency_obj->get_latest_fxrate($affiliate['currency']);

        $output = '<h3>Stock Details</h3>';
        $output .= '<table width="100%" cellspacing="0" cellpadding="5" style="border: 1px solid #CCC; font-size: 10px;" border="0">';
        $output .= '<tr>';
        foreach($output_fields as $field => $configs) {
            if(is_array($configs)) {
                $output .= '<th style="background: #91b64f;">'.$configs['title'].'</th>';
            }
            else {
                $output .= '<th style="background: #91b64f;">'.$configs.'</th>';
            }
        }
        $output .= '</tr>';

        if(is_array($inputs)) {
            $order_attr = 'remaining_cost';
            ${$order_attr} = array();
            foreach($inputs as $data_key => $data_row) {
                ${$order_attr}[$data_key] = $data_row['stack'][$order_attr];
            }
            array_multisort(${$order_attr}, SORT_DESC, $inputs);

            foreach($inputs as $id => $input) {
                $output .= '<tr>';
                foreach($output_fields as $field => $configs) {
                    $output_td_style = '';
                    if(is_array($configs) && $configs['source'] != null) {
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
                                $inputs[$i]['supplier']['name'] = $inputs[$i]['supplier']['value'] = $output_value;
                                $inputs[$i]['supplier']['c_bpartner_id'] = $input['supplier']['c_bpartner_id'];
                            }
                        }

                        if(in_array($field, $summary_reqinfo)) {
                            foreach($summary_categories as $category => $attribute) {
                                $summaries[$category][$input[$category][$attribute]]['name'] = $input[$category]['value'];
                                if($field == 'quantity') {
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
                            if(is_array($configs['styles'])) {
                                krsort($configs['styles']);
                                foreach($configs['styles'] as $num => $style) {
                                    if($output_value > $num) {
                                        $output_td_style .= $style;
                                        break;
                                    }
                                }
                            }
                            else {
                                $output_td_style = $configs['styles'];
                            }
                        }

                        $output .= '<td style="border: 1px solid #CCC; '.$output_td_style.'">'.$output_value.'</td>';
                        unset($output_value);
                    }
                    else {
                        switch($field) {
                            case 'unitcost':
                                $output_value = $input['stack']['remaining_cost'] / $input['stack']['remaining_qty'];
                                $input['unitcost'] = $output_value;
//                                if(in_array($field, $total_types)) {
//                                    $totals[$field] += $output_value;
//                                }
                                $output .= '<td style="border: 1px solid #CCC; text-align: right;">'.number_format($output_value, $report_options['roundto'], '.', ' ').'</td>';
                                break;
                            case 'unitcostusd':
                                $output_value = ($input['stack']['remaining_cost'] / $input['stack']['remaining_qty']) / $fxrates['usd'];
                                $input['unitcostusd'] = $output_value;
//                                if(in_array($field, $total_types)) {
//                                    $totals[$field] += $output_value;
//                                }

                                $output .= '<td style="border: 1px solid #CCC; text-align: right;">'.number_format($output_value, $report_options['roundto'], '.', ' ').'</td>';
                                break;
                            case 'costusd':
                                $output_value = $input['stack']['remaining_cost'] / $fxrates['usd'];
                                $input['costusd'] = $output_value;

                                if(in_array($field, $summary_reqinfo)) {
                                    foreach($summary_categories as $category => $attribute) {
                                        $summaries[$category][$input[$category][$attribute]][$field] += $output_value;
                                    }
                                }

                                $output .= '<td style="border: 1px solid #CCC; text-align: right;">'.number_format($output_value, $report_options['roundto'], '.', ' ').'</td>';

                                $date_value = $input[$output_fields['inputdate']['source']][$output_fields['inputdate']['attribute']];
                                if(strstr($date_value, '.')) {
                                    $date_valueobj = DateTime::createFromFormat('Y-m-d G:i:s.u', $date_value);
                                }
                                else {
                                    $date_valueobj = DateTime::createFromFormat('Y-m-d G:i:s', $date_value);
                                }

                                if(in_array($field, $total_types)) {
                                    $totals[$field] += $output_value;
                                }
                                //$totals['costusd'][$date_valueobj->format('Y')][$date_valueobj->format('n')] += $input['stack']['remaining_cost'] / $rate;
                                break;
                        }
                    }
                }
                $output .= '</tr>';

                if((!is_numeric($input['transaction']['attributes']['daystoexpire']) && !empty($input['transaction']['attributes']['daystoexpire'])) || ($input['transaction']['attributes']['daystoexpire'] <= 90) && $input['transaction']['attributes']['daystoexpire'] != '') {
                    $expired_entries[] = $input;
                }
            }
        }
        else {
            $output .= '<tr><td colspan="16">N/A</td></tr>';
        }
        /* Output main table totals row - START */
        $output .= '<tr>';
        foreach($output_fields as $field => $configs) {
            if(in_array($field, $maintable_hiddencols)) {
                $output .= '<td style="border: 1px solid #CCC;"></td>';
                continue;
            }
            $output_value = $totals[$field];
            if(is_numeric($output_value)) {
                $output_value = number_format($output_value, $report_options['roundto'], '.', ' ');
            }
            $output .= '<td style="border: 1px solid #CCC; font-weight: bold; text-align: right; text-decoration:underline;">'.$output_value.'</td>';
        }
        $output .= '</tr>';
        /* Output main table totals row - END */
        $output .= '</table>';

        /* Parse Summaries - Start */
        $summaries_ouput = '';
        if(is_array($summaries)) {
            foreach($summaries as $category => $category_data) {
                $totals = array();
                $summaries_ouput .= '<h3>'.$output_fields[$category]['title'].' Summary</h3>';
                $summaries_ouput .= '<table width="100%" cellspacing="0" cellpadding="5" style="border: 1px solid #CCC; font-size: 10px;" border="0">';
                $summaries_ouput .= '<tr><th style="background: #91b64f;">'.$output_fields[$category]['title'].'</th>';
                foreach($summary_reqinfo as $reqinfo) {
                    $summaries_ouput .= '<th style="background: #91b64f;">'.$output_fields[$reqinfo]['title'].'</th>';
                }
                unset($out_field_title);
                $summaries_ouput .= '</tr>';

                ${$summary_order_attr} = array();
                foreach($category_data as $cat_data_key => $cat_data_row) {
                    ${$summary_order_attr}[$cat_data_key] = $cat_data_row[$summary_order_attr];
                }
                array_multisort(${$summary_order_attr}, SORT_DESC, $category_data);

                foreach($category_data as $cat_data_row) {
                    $summaries_ouput .= '<tr>';
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
                        $summaries_ouput .= '<td style="border: 1px solid #CCC; width: 25%;'.$output_td_style.'">'.$output_value.'</td>';
                    }
                    $summaries_ouput .= '</tr>';
                }
                /* Output summary table totals row - START */
                $summaries_ouput .= '<tr>';
                $summaries_ouput .= '<td style="border: 1px solid #CCC;"></td>';
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
                    $summaries_ouput .= '<td style="border: 1px solid #CCC; font-weight: bold; text-align: right; text-decoration:underline;">'.$output_value.'</td>';
                    $output_value = '';
                }
                $summaries_ouput .= '</tr>';
                /* Output summary table totals row - END */
                $summaries_ouput .= '</table>';
            }
        }

        /* Parse Expired Products Table - START */
        $alerts = '';
        if(is_array($expired_entries)) {
            $totals = null;
            $alerts .= '<div style="font-weight: bold; color: red; font-size:18pt;">The following products have expired or are expiring soon!</div><br /><table width="100%" cellspacing="0" cellpadding="5" style="border: 1px solid #CCC; font-size: 10px;" border="0">';
            $alerts .= '<tr>';
            foreach($output_fields as $field => $configs) {
                if(is_array($configs)) {
                    $alerts .= '<th style="background: #91b64f;">'.$configs['title'].'</th>';
                }
                else {
                    $alerts .= '<th style="background: #91b64f;">'.$configs.'</th>';
                }
            }
            $alerts .= '</tr>';

            $order_attr = 'remaining_cost';
            ${$order_attr} = array();
            foreach($expired_entries as $data_key => $data_row) {
                ${$order_attr}[$data_key] = $data_row['stack'][$order_attr];
            }
            array_multisort(${$order_attr}, SORT_DESC, $expired_entries);

            foreach($expired_entries as $id => $input) {
                $alerts .= '<tr>';
                foreach($output_fields as $field => $configs) {
                    $output_td_style = '';
                    if(is_array($configs) && $configs['source'] != null) {
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
                            if(is_array($configs['styles'])) {
                                krsort($configs['styles']);
                                foreach($configs['styles'] as $num => $style) {
                                    if($output_value > $num) {
                                        $output_td_style .= $style;
                                        break;
                                    }
                                }
                            }
                            else {
                                $output_td_style = $configs['styles'];
                            }
                        }

                        $alerts .= '<td style="border: 1px solid #CCC; '.$output_td_style.'">'.$output_value.'</td>';
                        unset($output_value);
                    }
                    else {
                        if(in_array($field, $total_types)) {
                            $totals[$field] += $input[$field];
                        }
                        $alerts .= '<td style="border: 1px solid #CCC; text-align: right;">'.number_format($input[$field], $report_options['roundto'], '.', ' ').'</td>';

                        unset($output_value);
                    }
                }
                $alerts .= '</tr>';
            }

            /* Output expired table totals row - START */
            $alerts .= '<tr>';
            foreach($output_fields as $field => $configs) {
                if(in_array($field, $maintable_hiddencols)) {
                    $alerts .= '<td style="border: 1px solid #CCC;"></td>';
                    continue;
                }
                $output_value = $totals[$field];
                if(is_numeric($output_value)) {
                    $output_value = number_format($output_value, $report_options['roundto'], '.', ' ');
                }
                $alerts .= '<td style="border: 1px solid #CCC; font-weight: bold; text-align: right; text-decoration:underline;">'.$output_value.'</td>';
            }
            $alerts .= '</tr>';
            /* Output expired table totals row - END */
            $alerts .= '</table>';
        }
        unset($expired_entries);
        /* Parse Expired Products Table - END */


        /* Parse Stock Evolution Report - START */
        $aging_scale = array(2 => '90-119', 3 => '>=120');
        $stockevolution_output = '<h3>Stock Evolution</h3>';
        $stockevolution_output .= '<table width="100%" cellspacing="0" cellpadding="5" style="border: 1px solid #CCC; font-size: 10px;" border="0">';
        $stockevolution_output .= '<tr><th style="background: #91b64f;">Week</td><th style="background: #91b64f;">Value K.USD</td>';
        foreach($aging_scale as $key => $age) {
            $stockevolution_output .= '<th style="background: #91b64f;">'.$age.'</td>';
        }
        $stockevolution_output .= '</tr>';

        $first_transaction = $integration->get_firsttransaction(array($orgid));
        if(TIME_NOW - strtotime($first_transaction->get()['trxprocessdate']) > (60 * 60 * 24 * 365)) {
            $date_from = strtotime((date('Y', TIME_NOW) - 1).'-01-01');
        }
        else {
            $date_from = strtotime($first_transaction->get()['trxprocessdate']);
        }
        $date_to = strtotime('tomorrow -1 second');
        while($date_from < $date_to) {
            $date = getdate_custom($date_from);

            $costingrule_obj = $integration->get_currcostingrule();
            //$stockevolution_data = $integration->get_totalvalue_bydate(date('Y-m-d', $date_from), array('costingalgorithm' => $costingrule_obj->get()['m_costing_algorithm_id']), array($orgid));

            $stockevolution_data = $integration->get_totalvalue_bydate(date('Y-m-d', $date_from), array('method' => 'fifo'));
            $chart_data['x'][$date['week'].$date['year']] = $date['week'].'-'.$date['year'];
            $chart_data['y']['total'][$date['week'].$date['year']] = 0;
            if(is_array($stockevolution_data['value'])) {
                $value = (array_sum($stockevolution_data['value']) / $fxrates['usd']) / 1000;
                $stockevolution_output .= '<tr><td style="border: 1px solid #CCC;">Week '.$date['week'].' - '.$date['year'].' ('.date('Y-m-d', $date_from).')</td><td style="border: 1px solid #CCC;">'.$value.'</td>';
                $chart_data['y']['total'][$date['week'].$date['year']] = $value;
                unset($value);

                /* Parse Aging Info */
                foreach($aging_scale as $key => $age) {
                    if(isset($stockevolution_data['aging']['value'][$key])) {
                        //$stockevolution_output .= '<td style="border: 1px solid #CCC;">-</td>';
                        $stockevolution_output .= '<td style="border: 1px solid #CCC;">'.(($stockevolution_data['aging']['value'][$key] / $fxrates['usd']) / 1000).'</td>';
                    }
                    else {
                        $stockevolution_output .= '<td style="border: 1px solid #CCC;">-</td>';
                    }
                }

                $stockevolution_output .= '</tr>';
            }
            unset($stockevolution_data);

            $date_from = $date_from + (60 * 60 * 24 * 7);
        }

        $stockevolution_output .= '</table>';
        $stockevolution_chart = new Charts(array('x' => $chart_data['x'], 'y' => $chart_data['y']), 'line', array('path' => '../tmp/charts/', 'labelrotationangle' => 90, 'height' => 400, 'width' => 900, 'graphareay2margin' => 50, 'nosort' => true));
        $stockevolution_output .= '<img src="data:image/png;base64,'.base64_encode(file_get_contents($stockevolution_chart->get_chart())).'" />';
        $stockevolution_chart->delete_chartfile();
        unset($stockevolution_chart, $chart_data);
        /* Parse Stock Evolution Report - END */


        /* Parse Summaries - END */
//		$summarytables_headers = '';
//		for($month = 1; $month <= 12; $month++) {
//			$summarytables_headers .= '<th style="background: #91b64f;">'.$month.'</th>';
//		}
//
//		if(is_array($totals)) {
//			foreach($totals as $category) {
//				$output .= '<table width="100%" cellspacing="0" cellpadding="5" style="border: 1px solid #CCC; font-size: 10px;" border="0">';
//				$output .= '<th style="background: #91b64f;">Category</th>';
//				$output .= $summarytables_headers;
//				$output .= '<th style="background: #91b64f;">Total</th>';
//				
//				foreach($category as $rowkey => $row) {
//					$output .= '<tr>';
//					$output .= '<td>'.$rowkey.'</td>';
//					for($month = 1; $month <= 12; $month++) {
//						if(isset($row[$month])) {
//							$output .= '<td>'.$row[$month].'</td>';
//						}
//						else {
//							$output .= '<td>0</td>';
//						}
//					}
//					$output .= '<td>'.array_sum($category).'</td>';
//					$output .= '</tr>';
//				}
//				$output .= '</table>';
//			}
//		}
        $message .= '</body></html>';

        $message = '<html><head><title>Stock Report</title></head><body>';
        $message .= '<h3>Stock Summary Report - '.$affiliate['name'].' - Week '.$date_info['week'].' ( '.$affiliate['currency'].' | USD FX Rate:'.$fxrates['usd'].')</h3>';
        $message .= $alerts.$summaries_ouput.$stockevolution_output.$output;
        $email_data = array(
                'from_email' => $core->settings['maileremail'],
                'from' => 'OCOS Mailer',
                'subject' => 'Stock Report - '.$affiliate['name'].' - Week '.$date_info['week'],
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

        //$email_data['to'] = array();
        //$email_data['to'][] = 'zaher.reda@orkila.com';
        //$email_data['to'][] = 'christophe.sacy@orkila.com';
        //unset($email_data['to']);
        //print_r($email_data);
        //$email_data['to'][] = 'zaher.reda@orkila.com';
        $mail = new Mailer($email_data, 'php');
        unset($message);
    }
}
?>
