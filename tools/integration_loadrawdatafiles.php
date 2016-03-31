<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Tool to import raw integration data from CSV
 * $id: integration_loadrawdatafiles.php
 * Created:        @zaher.reda    Mar 12, 2013 | 1:04:11 PM
 * Last Update:    @zaher.reda    Mar 12, 2013 | 1:04:11 PM
 */

require '../inc/init.php';
define('AUTHCODE', 'ZdiILL7pG0GR4p6oi3fhHEc');
if($core->input['authCode'] == AUTHCODE) {
    $filepath = './';

    switch($core->input['datatype']) {
        case 'purchases':
            $filename = 'Integration - Purchases.csv';
            $tables = array(
                    'integration_mediation_purchaseinvoices' => array('pk' => 'impiid', 'identifier' => 'foreignId', 'attrAlias' => array('orderId' => 'foreignId'), 'dateAttrs' => array('date')),
                    'integration_mediation_purchaseinvoicelines' => array('pk' => 'impilid', 'identifier' => 'foreignId', 'attrAlias' => array('orderLineId' => 'foreignId', 'orderId' => 'foreignInvoiceId')));
            $required_fields = array(
                    'integration_mediation_purchaseinvoicelines' => array('foreignId', 'foreignInvoiceId', 'pid', 'price', 'quantity', 'quantityUnit'),
                    'integration_mediation_purchaseinvoices' => array('foreignId', 'date', 'spid'));
            break;
        case 'sales':
            $filename = 'Integration - Sales.csv';

            $tables = array(
                    'integration_mediation_salesinvoices' => array('pk' => 'imsiid', 'identifier' => 'foreignId', 'attrAlias' => array('invoiceId' => 'foreignId'), 'dateAttrs' => array('date')),
                    'integration_mediation_salesinvoicelines' => array('pk' => 'imsilid', 'identifier' => 'foreignId', 'attrAlias' => array('invoiceLineId' => 'foreignId', 'invoiceId' => 'foreignInvoiceId')));
            $required_fields = array(
                    'integration_mediation_salesinvoicelines' => array('foreignId', 'foreignInvoiceId', 'pid', 'price', 'quantity'),
                    'integration_mediation_salesinvoices' => array('foreignId', 'date', 'spid'));
            break;
        case 'entities':
            $filename = 'Integration - Business Partners.csv';

            $tables = array('integration_mediation_entities' => array('pk' => 'imspid', 'identifier' => 'foreignId', 'matchInfo' => array('table' => 'entities', 'dataField' => 'eid', 'match' => 'foreignName', 'matchWith' => 'companyName')));
            $required_fields = array('integration_mediation_entities' => array('foreignId', 'foreignName', 'entityType'));
            break;
        case 'products':
            $filename = 'Integration - Products.csv';

            $tables = array('integration_mediation_products' => array('pk' => 'impid', 'identifier' => 'foreignId', 'matchInfo' => array('table' => 'products', 'dataField' => 'pid', 'match' => 'foreignName', 'matchWith' => 'name')));
            $required_fields = array('integration_mediation_products' => array('foreignId', 'foreignName', 'foreignSupplier'));
            break;
        default:
            error('Unkown data type');
            exit;
            break;
    }

    $csv = new CSV($filepath.$filename, 1, true, ';');
    $csv->readdata_file();
    $data = $csv->get_data();

    if(is_array($tables)) {
        foreach($tables as $table => $table_config) {
            $temp_tables_fields[$table] = $db->show_fields_from($table);

            foreach($temp_tables_fields[$table] as $field) {
                $tables_fields[$table][] = $field['Field'];
            }
        }
        unset($temp_tables_fields);
    }

    validate_data();
    insert_data($core->input['runtype'], false);
}
function validate_data() {
    global $errorhandler;

    insert_data('dry', true);
    echo 'Validation Completed<br />';

    if(!empty($errorhandler->recorded_errors)) {
        $errorhandler->output_errors_inline();

        exit;
    }
}

function insert_data($runtype = 'dry', $validate = true) {
    global $db, $core, $errorhandler, $data, $tables, $tables_fields, $required_fields;
    if(is_array($data)) {
        foreach($tables as $table => $table_config) {
            foreach($data as $row => $values) {
                $tables_fields_values = array();
                foreach($values as $attr => $val) {
                    if($attr == $table_config['pk']) {
                        continue;
                    }

                    $true_attr = $attr;

                    if(isset($table_config['attrAlias'][$attr])) {
                        $true_attr = $table_config['attrAlias'][$attr];
                    }

                    if(in_array($true_attr, $required_fields[$table]) && $val == '') {
                        $errorhandler->record('emptyfields', 'Row: '.$row.' Attr: '.$true_attr.' is empty.');
                    }

                    if(is_array($table_config['dateAttrs'])) {
                        if(in_array($true_attr, $table_config['dateAttrs'])) {
                            $val = strtotime($val);
                        }
                    }
                    if(in_array($true_attr, $tables_fields[$table])) {
                        $tables_fields_values[$table][$true_attr] = $val;
                    }
                }

                if($validate == false) {
                    $value_exists_extrawhere = '';
                    if(!empty($tables_fields_values[$table]['foreignSystem'])) {
                        $value_exists_extrawhere = ' AND foreignSystem='.$db->escape_string($tables_fields_values[$table]['foreignSystem']);
                    }

                    if(!value_exists($table, $table_config['identifier'], substr($tables_fields_values[$table][$table_config['identifier']], 0, 32), 'affid='.intval($tables_fields_values[$table]['affid']).$value_exists_extrawhere)) {
                        if($core->input['domatch'] == 1) {
                            $tables_fields_values[$table]['localId'] = $db->fetch_field($db->query("SELECT ".$table_config['matchInfo']['dataField']." FROM ".Tprefix.$table_config['matchInfo']['table']." WHERE ".$table_config['matchInfo']['matchWith']."='".$db->escape_string($tables_fields_values[$table][$table_config['matchInfo']['match']])."'"), $table_config['matchInfo']['dataField']);
                        }

                        if(in_array('localDate', $tables_fields[$table])) {
                            $tables_fields_values[$table]['localDate'] = TIME_NOW;
                        }

                        if($runtype != 'dry') {
                            $db->insert_query($table, $tables_fields_values[$table]);
                        }
                        echo 'Added:';
                        print_r($tables_fields_values[$table]);
                        echo '<hr />';
                    }
                    else {
                        if($runtype != 'dry') {
                            unset($tables_fields_values[$table]['localId']);
                            $db->update_query($table, $tables_fields_values[$table], $table_config['identifier'].'="'.substr($tables_fields_values[$table][$table_config['identifier']], 0, 32).'" AND affid='.intval($tables_fields_values[$table]['affid']).$value_exists_extrawhere);
                        }
                        echo 'Updated:';
                        print_r($tables_fields_values[$table]);
                        echo '<hr />';
                    }
                }
            }
        }
    }
}

?>