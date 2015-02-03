<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Import Budgets
 * $id: importbudget.php
 * Created:        @tony.assaad    Sep 30, 2013 | 3:37:13 PM
 * Last Update:    @tony.assaad    Sep 30, 2013 | 3:37:13 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
$session->start_phpsession();

if($core->usergroup['canAdminCP'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {
    eval("\$importpage = \"".$template->get('budgeting_importbuget')."\";");
    output_page($importpage);
}
else {
    if($core->input['action'] == 'preview') {
        if(!empty($_FILES['uploadbudget']['name'])) {
            $upload = new Uploader('uploadbudget', $_FILES, array('application/csv', 'application/excel', 'application/x-excel', 'text/csv', 'text/comma-separated-values', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'), 'readonly', 5242880, 0, 1);
            eval("\$headerinc = \"".$template->get('headerinc')."\";");
            echo $headerinc;

            $upload->process_file();
            $filestatus = $upload->parse_status($upload->get_status());
            if($upload->get_status() != 4) {
                ?>
                <script language="javascript" type="text/javascript">
                    $(function () {
                        return window.top.$("#upload_Result").html('<?php echo addslashes($filestatus);?>');
                    });
                </script>
                <?php
                exit;
            }

            $filedata = $upload->get_filedata();

            $csv_file = new CSV($filedata, 2, true, $core->input['delimiter']);
            $csv_file->readdata_string();
            $data['budget'] = $csv_file->get_data();
            $csv_header['budget'] = $csv_file->get_header();
        }

        eval("\$headerinc = \"".$template->get('headerinc')."\";");
        echo $headerinc;
        ?>
        <script language="javascript" type="text/javascript">
            $(function ()
            {
                return window.top.$("#upload_Result").html("<?php echo addslashes(parse_datapreview($csv_header, $data));?>");
            });
        </script>
        <?php
    }
    elseif($core->input['action'] == 'do_perform_importbudget') {
        $cache = new Cache();
        //$options['runtype'] = 'dry';
        $options = $core->input['options'];
        $options['useAltCid'] = 1;
        $options['useAltPid'] = 1;
        $options['resolvesupplierbyproduct'] = 1;
        $options['resolveproductname'] = 1;
        $all_data = unserialize($session->get_phpsession('budgetingimport_'.$core->input['identifier']));
        $allowed_headers = array('affiliate' => 'Affiliate', 'salesManager' => 'Sales Manager', 'CustomerID' => 'Cutomer ID', 'customerName' => 'Customer Name', 'invoice' => 'invoice', 'country' => 'Country', 'supplierID' => 'Supplier ID', 'supplierName' => 'Supplier Name', 'productID' => 'Product ID', 'productName' => 'Product Name', 'year' => 'Year', 'quantity' => 'Quantity', 'actualQty' => 'Actual Qty', 'uom' => 'Unit of Measure', 'amount' => 'Sales amount', 'actualAmount' => 'Actual Amount', 'income' => 'Income', 'actualIncome' => 'Actual Income', 'incomePerc' => 'Income Perc', 'originalCurrency' => 'Currency', 'segment' => 'Market Segment', 'saleType' => 'Sale Type', 'Producer' => 'Producer', 's1Perc' => 'S1 %', 's2Perc' => 'S2 %');
        $required_headers_check = $required_headers = array('customerName', 'productName', 'supplierName', 'year', 'saleType');
        $budgetlines_valid_data = array('inputChecksum', 'cid', 'pid', 'psid', 'altPid', 'altCid', 'customerCountry', 'amount', 'actualAmount', 'income', 'actualIncome', 'incomePerc', 'quantity', 'unitPrice', 'businessMgr', 'actualQty', 'saleType', 'originalCurrency', 'invoice', 's1Perc', 's2Perc');
        $budgetlines_required_data = array('cid', 'pid', 'altPid', 'altCid', 'saleType', 'unitPrice', 'originalCurrency', 'businessMgr', 'invoice');

        $headers_cache = array();
        for($i = 0; $i < count($allowed_headers) + 1; $i++) {
            if(empty($core->input['selectheader_'.$i])) {
                continue;
            }
            if(in_array($core->input['selectheader_'.$i], $headers_cache)) {
                output_xml("<status>false</status><message>".$core->input['selectheader_'.$i]."{$lang->fieldrepeated}</message>");
                exit;
            }
            else {
                if(in_array($core->input['selectheader_'.$i], $required_headers_check)) {
                    unset($required_headers_check[array_search($core->input['selectheader_'.$i], $required_headers_check)]);
                }
                $headers_cache[] = $core->input['selectheader_'.$i];
            }
        }

        if(count($required_headers_check) > 0) {
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            exit;
        }

        foreach($all_data['budget'] as $key => $budgetrow) {
            $data = array();
            $count_input = 0;
            foreach($budgetrow as $header => $value) {
                if(empty($value)) {
                    $count_input++;
                    continue;
                }
                if(!empty($core->input['selectheader_'.$count_input])) {
                    $data[$core->input['selectheader_'.$count_input]] = (utf8_encode(trim(strtolower($value))));
                }

                $count_input++;
            }

            /* Resolve names if IDs are not provided - START */
            if($options['resolveaffiliatename'] == 1 || true) {
                if($cache->incache('affiliates', $data['affiliate'])) {
                    $data['affid'] = array_search($data['affiliate'], $cache->data['affiliates']);
                }
                else {
                    $data['affid'] = Affiliates::get_affiliate_byname($data['affiliate']);
                    if($data['affid'] != false) {
                        $data['affid'] = $data['affid']->get()['affid'];
                    }
                    if(empty($data['affid'])) {
                        $errorhandler->record('affiliatenomatch', $data['affiliate']);
                        continue;
                    }
                    else {
                        $cache->add('affiliates', $data['affiliate'], $data['affid']);
                    }
                }
            }
            else {
                $data['affid'] = $data['affiliate'];
            }

            if($options['resolveproductname'] == 1) {
                if($cache->incache('products', $data['productName'])) {
                    $data['pid'] = array_search($data['productName'], $cache->data['products']);
                }
                else {
                    $product_obj = Products::get_product_byname($data['productName']);
                    if($product_obj != false) {
                        $data['pid'] = $product_obj->get()['pid'];
                    }
                    if(empty($data['pid'])) {
                        $data['pid'] = $db->fetch_field($db->query('SELECT localId FROM '.Tprefix.'integration_mediation_products WHERE foreignName = "'.$db->escape_string($data['productName']).'" AND (affid = "'.$db->escape_string($data['affid']).'" OR affid = 0)'), 'localId');
                        if(empty($data['pid'])) {
                            if($options['useAltPid'] == 1) {
                                $data['altPid'] = $data['productName'];
                            }
                            else {
                                $errorhandler->record('productnomatch', ucwords($data['productName'].' - '.$data['supplierName']));
                                continue;
                            }
                        }
                    }
                    if(!empty($data['pid'])) {
                        $cache->add('products', $data['productName'], $data['pid']);
                    }
                }
            }
            else {
                $data['pid'] = $data['productName'];
            }

            if(empty($options['resolvesupplierbyproduct'])) {
                if($options['resolvesuppliername'] == 1 || true) {
                    if($cache->incache('suppliers', $data['supplierName'])) {
                        $data['spid'] = array_search($data['supplierName'], $cache->data['suppliers']);
                    }
                    else {
                        $data['spid'] = Entities::get_entity_byname($data['supplierName']);
                        if($data['spid'] != false) {
                            $data['spid'] = $data['spid']->get()['eid'];
                        }
                        if(empty($data['spid'])) {
                            $data['spid'] = $db->fetch_field($db->query('SELECT localId FROM '.Tprefix.'integration_mediation_entities WHERE foreignName = "'.$db->escape_string($data['supplierName']).'" AND (affid = "'.$db->escape_string($data['affid']).'" OR affid = 0) AND entityType = "s"'), 'localId');
                            if(empty($data['spid'])) {
                                $errorhandler->record('suppliernomatch', $data['supplierName']);
                                continue;
                            }
                        }
                        if(!empty($data['spid'])) {
                            $cache->add('suppliers', $data['supplierName'], $data['spid']);
                        }
                    }
                }
                else {
                    $data['spid'] = $data['supplier'];
                }
            }
            else {
                if($product_obj != false) {
                    $data['spid'] = $product_obj->get_supplier()->get()['eid'];
                }
                elseif(!empty($data['pid'])) {
                    $product_obj = new Products($data['pid']);
                    $data['spid'] = $product_obj->get_supplier()->get()['eid'];
                }
                else {
                    $errorhandler->record('suppliernomatch', $data['productName'].' ---'.$data['supplierName']);
                    continue;
                }
                unset($product_obj);
            }

            if(isset($data['segment'])) {
                if($options['resolvesegmentname'] == 1 || true) {
                    if($cache->incache('segments', $data['segment'])) {
                        $data['psid'] = array_search($data['segment'], $cache->data['segments']);
                    }
                    else {
                        $data['psid'] = ProductsSegments::get_segment_byname($data['segment']);
                        if($data['psid'] != false) {
                            $data['psid'] = $data['psid']->psid;
                        }

                        if(empty($data['psid'])) {
                            $errorhandler->record('segmentnomatch', $data['segment']);
                            continue;
                        }
                    }
                }
                else {
                    $data['psid'] = $data['segment'];
                }
            }

            if($options['resolvecustomername'] == 1 || true) {
                if($cache->incache('customers', $data['customerName'])) {
                    $data['cid'] = array_search($data['customerName'], $cache->data['customers']);
                }
                else {
                    $data['cid'] = Entities::get_entity_byname($data['customerName']);
                    if($data['cid'] != false) {
                        $data['cid'] = $data['cid']->get()['eid'];
                    }

                    if(empty($data['cid'])) {
                        $data['cid'] = $db->fetch_field($db->query('SELECT localId FROM '.Tprefix.'integration_mediation_entities WHERE foreignName = "'.$db->escape_string($data['customerName']).'" AND affid = "'.$db->escape_string($data['affid']).'" AND entityType = "c"'), 'localId');
                        if(empty($data['cid'])) {
                            if($options['useAltCid'] == 1) {
                                $data['altCid'] = $data['customerName'];
                            }
                            else {
                                $errorhandler->record('customernomatch', $data['customerName']);
                                continue;
                            }
                        }
                    }
                    if(!empty($data['cid'])) {
                        $cache->add('customers', $data['customerName'], $data['cid']);
                    }
                }
            }
            else {
                $data['cid'] = $data['customer'];
            }

            if($options['resolvebmname'] == 1 || true) {
                if($cache->incache('employees', $data['salesManager'])) {
                    $data['businessMgr'] = array_search($data['salesManager'], $cache->data['employees']);
                }
                else {
                    $data['businessMgr'] = Users::get_user_byattr('displayName', $data['salesManager']);
                    if($data['businessMgr'] != false) {
                        $data['businessMgr'] = $data['businessMgr']->get()['uid'];
                    }
                    if(empty($data['businessMgr'])) {
                        $errorhandler->record('bmnomatch', $data['salesManager']);
                        continue;
                    }
                    else {
                        $cache->add('employees', $data['salesManager'], $data['businessMgr']);
                    }
                }
            }
            else {
                $data['businessMgr'] = $data['salesManager'];
            }
            if($options['runtype'] != 'dry') {
                if(!value_exists('assignedemployees', 'uid', $data['businessMgr'], 'affid = '.intval($data['affid']).' AND eid = '.intval($data['spid']))) {
                    $db->insert_query('assignedemployees', array('uid' => $data['businessMgr'], 'affid' => $data['affid'], 'eid' => $data['spid']));
                    $errorhandler->record('new assignedemployees-', 'Row: '.$key.' - '.$data['supplierName']);
                }

                if(!value_exists('affiliatedentities', 'affid', $data['affid'], 'eid = '.intval($data['spid']))) {
                    $db->insert_query('affiliatedentities', array('affid' => $data['affid'], 'eid' => $data['spid']));
                    $errorhandler->record('new affiliatedentities-', 'Row: '.$key.' - '.$data['supplierName']);
                }

                if(!empty($data['cid']) && !value_exists('assignedemployees', 'uid', $data['businessMgr'], 'affid = '.intval($data['affid']).' AND eid = '.intval($data['cid']))) {
                    $db->insert_query('assignedemployees', array('uid' => $data['businessMgr'], 'affid' => $data['affid'], 'eid' => $data['cid']));
                    $errorhandler->record('new assignedemployees-', 'Row: '.$key.' - '.$data['customerName']);
                }
            }
            /* Resolve names if IDs are not provided - END */
            /* Resolve customercountry */
            if(isset($data['country'])) {
                if($options['resolvecountry'] == 1 || true) {
                    if($cache->incache('countries', $data['country'])) {
                        $data['customerCountry'] = array_search($data['country'], $cache->data['countries']);
                    }
                    else {
                        $data['customerCountry'] = Countries::get_country_byname($data['country']);
                        if($data['customerCountry'] != false) {
                            $data['customerCountry'] = $data['customerCountry']->get()['coid'];
                        }
                        if(empty($data['customerCountry'])) {
                            $errorhandler->record('countrynotmatch', $data['country']);
                            continue;
                        }
                        else {
                            $cache->add('countries', $data['country'], $data['customerCountry']);
                        }
                    }
                }
                else {
                    $data['customerCountry'] = $data['country'];
                }
            }

            /* Get saletype id by abbreviation */
            $data['saleTypeName'] = $data['saleType'];
            if($cache->incache('salesType', $data['saleTypeName'])) {
                $data['saleType'] = array_search($data['saleTypeName'], $cache->data['salesType']);
            }
            else {
                $data['saleType'] = Budgets::parse_saletype($data['saleTypeName']);
            }
            if(empty($data['saleType'])) {
                $errorhandler->record('saletypenotmatch', $data['saleTypeName']);
                continue;
            }
            else {
                $cache->add('salesType', $data['saleTypeName'], $data['saleType']);
            }

            $budget_data = array('identifier' => substr(uniqid(time()), 0, 10),
                    'year' => $data['year'],
                    'affid' => $data['affid'],
                    'spid' => $data['spid']
            );

            /* Create budget */

            if(empty($data['incomePerc'])) {
                if(!empty($data['income'])) {
                    $data['incomePerc'] = $data['amount'] / $data['income'];
                }
                else {
                    $data['incomePerc'] = 0;
                }
            }
            elseif(empty($data['income'])) {
                if(!empty($data['incomePerc'])) {
                    $data['income'] = $data['amount'] * ($data['incomePerc'] / 100);
                }
            }

            //parsing unit price

            if(($data['quantity'] > 0 && $data['amount'] > 0) && (empty($data['unitPrice']))) {
                $data['unitPrice'] = ($data['amount'] / $data['quantity']);
            }
            if(($data['quantity'] > 0 && $data['unitPrice'] > 0) && (empty($data['amount']))) {
                $data['amount'] = ($data['quantity'] * $data['unitPrice']);
            }

            $data['inputChecksum'] = generate_checksum('bl');
            $budgetlines = array();
            foreach($budgetlines_valid_data as $valid_attribute) {
                if($data[$valid_attribute] != '') {
                    $budgetlines[0][$valid_attribute] = $data[$valid_attribute];
                }
                else {
                    if(in_array($valid_attribute, $budgetlines_required_data)) {
                        if($valid_attribute == 'cid' && (isset($data['altCid']) && !empty($data['altCid']))) {
                            continue;
                        }

                        if(empty($data['altCid']) && !empty($data['cid'])) {
                            continue;
                        }

                        if((empty($data['altPid']) && !empty($data['pid'])) || (!empty($data['altPid']) && empty($data['pid']))) {
                            continue;
                        }


                        $errorhandler->record('incompletedata-'.$valid_attribute, 'Row: '.$key);
                        continue 2;
                    }
                }
            }

            if($options['runtype'] != 'dry') {
                Budgets::save_budget($budget_data, $budgetlines);
            }
            unset($data, $budgetlines, $budget_data);
        }

        $import_errors = $errorhandler->get_errors_inline();
        $log->record();
        if(isset($import_errors) && !empty($import_errors)) {
            output_xml("<status>false</status><message>{$lang->resulterror}<![CDATA[<br /> {$import_errors}]]></message>");
        }
        else {
            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
        }
    }
}
//$all = parse_datapreview($csv_header, $data);
//print_r($all);
function parse_datapreview($csv_header, $data) {
    global $session, $lang, $core, $cache;

    $output = "<span class='subtitle'></span><br /><form id='perform_budgeting/importbudget_Form'><table class='datatable'><tr><td colspan='16' class='subtitle' style='text-align:center'>{$lang->importpreview}</td></tr><tr>";
    $budgetlines_valid_data = array('affiliate', 'Sales Manager', 'Customer Name', 'country', 'SupplierID', 'Supplier Name', 'productID', 'Product Name', 'Year', 'quantity', 'actualQty', 'Unit of Measure', 'Sales amount', 'invoice', 'actualAmount', 'Income', 'actualIncome', 'incomePerc', 'originalCurrency', 'Market Segment', 'Sale Type', 'Producer');
    $allowed_headers = array('affiliate' => 'Affiliate', 'salesManager' => 'Sales Manager', 'CustomerID' => 'Cutomer ID', 'customerName' => 'Customer Name', 'country' => 'Country', 'invoice' => 'invoice', 'supplierID' => 'Supplier ID', 'supplierName' => 'Supplier Name', 'productID' => 'Product ID', 'productName' => 'Product Name', 'year' => 'Year', 'quantity' => 'Quantity', 'actualQty' => 'Actual Qty', 'uom' => 'Unit of Measure', 'amount' => 'Sales amount', 'unitPrice' => 'unitPrice', 'actualAmount' => 'Actual Amount', 'income' => 'Income', 'actualIncome' => 'Actual Income', 'incomePerc' => 'Income Perc', 'originalCurrency' => 'Currency', 'segment' => 'Market Segment', 'saleType' => 'Sale Type', 'Producer' => 'Producer');
    $abbreviation = array('Ltd.', 'Ltd', 'Llc.', 'Llc', 'Sal.', 'Co., ', 'Co.', 'Co');
    $output .= '<td>#</td>';

    if(!isset($csv_header['budget']['unitPrice'])) {
        $csv_header['budget']['unitPrice'] = 'unitPrice';
    }
    else {
        unset($csv_header['budget']['unitPrice']);
    }

    foreach($csv_header['budget'] as $header_key => $header_val) {
        $output .= '<td style="width:20px;"><select name="selectheader_'.$header_key.'" id="selectheader_'.$header_key.'">';
        $output .= '<option value="">&nbsp;</option>';

        foreach($allowed_headers as $allowed_header_key => $allowed_header_val) {
            if($header_val == $allowed_header_key || $header_val == $allowed_header_val) {
                $selected_header = ' selected="selected"';
            }
            else {
                $selected_header = '';
            }

            $output .= '<option value="'.$allowed_header_key.'"'.$selected_header.'>'.$allowed_header_val.'</option>';
            $selected_header = '';
        }
        $output .= '</select></td>';
    }

    foreach($data['budget'] as $key => $val) {
        $altrow = alt_row($altrow);
        $output .= '<tr class="'.$altrow.'">';
        if(isset($val['companyName']) && !empty($val['companyName'])) {
            $val['companyName'] = ucwords($val['companyName']);
            $name = explode(' ', $val['companyName']);

            foreach($abbreviation as $abb) {
                $search = array_search($abb, $name);
                if($search)
                    $name[$search] = strtoupper($name[$search]);
            }
            $val['companyName'] = implode(' ', $name);
        }
//        if(($val['Quantity'] > 0 && $val['Sales amount'] > 0) && (empty($val['unitPrice']))) {
//            $val['unitPrice'] = ($val['Sales amount'] / $val['Quantity']);
//        }
//        if(($val['Quantity'] > 0 && $val['unitPrice'] > 0) && (empty($val['Sales amount']))) {
//            $val['Sales amount'] = ($val['Quantity'] * $val['unitPrice']);
//        }

        $output .= '<td>'.$key.'</td>';
        foreach($val as $id => $value) {
            $output .= '<td id="'.$id.'"valign="top" style="width:20px;">'.utf8_encode($value).'</td>';
        }

        $output .= '</tr>';
    }

    $identifier = md5(uniqid(microtime()));
    $session->set_phpsession(array('budgetingimport_'.$identifier => serialize($data)));
    $output .= '<tr><input type="checkbox" name="options[runtype]" value="dry" checked="checked" /> Dry Run <br /> <input type="hidden" name="identifier" id="identifier" value="'.$identifier.'"/><input type="hidden" name="multivalueseperator" id="multivalueseperator" value="'.$core->input['multivalueseperator'].'"/></table></form>';
    $output .= '<div><input type="button" value="'.$lang->import.'" class="button" id="perform_budgeting/importbudget_Button" name="perform_budgeting/importbudget_Button"/></div><div id="perform_budgeting/importbudget_Results"></div>';
    return $output;
}

function resolve_product($name) {
    global $db, $cache, $data;

    if($cache->incache('products', $name)) {
        return array_search($name, $cache->data['products']);
    }
    else {
        $product = Products::get_product_byname($name);
        if($product != false) {
            $pid = $product->get()['pid'];
        }
        if(empty($pid)) {
            $pid = $db->fetch_field($db->query('SELECT localId FROM '.Tprefix.'integration_mediation_products WHERE foreignName="'.$db->escape_string($name).'" AND affid="'.$db->escape_string($data['affid']).'"'), 'localId');
            if(empty($pid)) {
                return false;
            }
        }
        $cache->add('products', $name, $pid);
        return $pid;
    }
    return false;
}
?>

