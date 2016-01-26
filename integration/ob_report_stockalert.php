<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: ob_report_stockalert.php
 * Created:        @zaher.reda    Feb 24, 2014 | 12:21:03 AM
 * Last Update:    @zaher.reda    Feb 24, 2014 | 12:21:03 AM
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
            1 => array(12, 333, 182, 43, 68, 'audrey.sacy'),
            21 => array(63, 158, 'audrey.sacy'),
            20 => array(183, 221, 'audrey.sacy'),
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
            'category' => array('source' => array('product', 'category'), 'attribute' => 'value', 'title' => 'Segment'),
            'supplier' => array('source' => 'supplier', 'attribute' => 'value', 'title' => 'Supplier'),
            'product' => array('source' => 'product', 'attribute' => 'name', 'title' => 'Product'),
            'warehouse' => array('source' => 'warehouse', 'attribute' => 'value', 'title' => 'Warehouse'),
//			'lot' => array('source' => array('transaction', 'attributes'), 'attribute' => 'lot', 'title' => 'Lot'),
            'packaging' => array('source' => array('transaction', 'attributes'), 'attribute' => 'packaging', 'title' => 'Packaging'),
            'initialquantity' => array('source' => 'stack', 'attribute' => 'qty', 'title' => 'Qty Received', 'numformat' => true),
            'quantity' => array('source' => 'stack', 'attribute' => 'remaining_qty', 'title' => 'Stock Qty', 'numformat' => true),
            'uom' => array('source' => array('product', 'uom'), 'attribute' => 'uomsymbol', 'title' => 'UoM'),
            'cost' => array('source' => 'stack', 'attribute' => 'remaining_cost', 'title' => 'Cost', 'numformat' => true),
            'inputdate' => array('source' => 'transaction', 'attribute' => 'movementdate', 'title' => 'Entry Date', 'isdate' => true),
            'daysinstock' => array('source' => 'stack', 'attribute' => 'daysinstock', 'title' => 'In Stock<br />(Days)', 'styles' => array(150 => 'background-color: #F1594A; text-align: center;', 120 => 'background-color: #F8C830; text-align: center;', 90 => 'background-color: #F2EB80; text-align: center;', 0 => 'background-color: #ABD25E; text-align: center;')),
            'expirydate' => array('source' => array('transaction', 'attributes'), 'attribute' => 'guaranteedate', 'title' => 'Expiry Date', 'isdate' => true),
            'daystoexpire' => array('source' => array('transaction', 'attributes'), 'attribute' => 'daystoexpire', 'title' => 'Days to Expire', 'styles' => array(0 => 'background-color: #F1594A; text-align: center;', 90 => 'background-color: #F8C830; text-align: center;', 180 => 'background-color: #F2EB80; text-align: center;', 270 => 'background-color: #ABD25E; text-align: center;'))
    );

    foreach($affiliates_index as $orgid => $affid) {
        $output = '';
        $found_one = false;
        $affiliateobj = new Affiliates($affid, false);
        $affiliate = $affiliateobj->get();
        $affiliate['currency'] = $affiliateobj->get_country()->get_maincurrency()->get()['alphaCode'];
        $output = '<h1>Stock Alert Report - '.$affiliate['name'].' - Week '.$date_info['week'].' ( '.$affiliate['currency'].')</h1>';
        $inputs = $integration->get_fifoinputs(array($orgid), array('hasqty' => true));

        $output .= '<div style="font-weight: bold; color: red; font-size:18pt;">The following products have expired or are expiring soon!</div><br /><table width="100%" cellspacing="0" cellpadding="5" style="border: 1px solid #CCC; font-size: 10px;" border="0">';
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
                if($input['transaction']['attributes']['daystoexpire'] > 90 || $input['transaction']['attributes']['daystoexpire'] == '') {
                    unset($inputs[$id]);
                    continue;
                }
                $found_one = true;
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
                            }
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
                            krsort($configs['styles']);
                            foreach($configs['styles'] as $num => $style) {
                                if($output_value > $num) {
                                    $output_td_style .= $style;
                                    break;
                                }
                            }
                        }

                        $output .= '<td style="border: 1px solid #CCC; '.$output_td_style.'">'.$output_value.'</td>';
                        unset($output_value);
                    }
                }
                $output .= '</tr>';
            }
        }
        else {
            $output .= '<tr><td colspan="12">N/A</td></tr>';
        }
        $output .= '</table>';

        $message .= '</body></html>';
        if($found_one == true) {
            $message = '<html><head><title>Stock Alert</title></head><body>';
            $message .= $output;
            $email_data = array(
                    'from_email' => $core->settings['maileremail'],
                    'from' => 'OCOS Mailer',
                    'subject' => 'Stock Alert - '.$affiliate['name'].' - Week '.$date_info['week'],
                    'message' => $message
            );

            $email_data['to'][] = $affiliateobj->get_generalmanager()->get()['email'];
            $email_data['to'][] = $affiliateobj->get_financialemanager()->get()['email'];
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

            array_unique($email_data['to']);
            //$email_data['to'] = array();
            //$email_data['to'][] = 'christophe.sacy@orkila.com';
            //print_r($email_data);
            $mail = new Mailer($email_data, 'php');
            unset($message);
        }
    }
}
?>
