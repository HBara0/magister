<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: integration_loadrawdatafiles.php
 * Created:        @zaher.reda    Mar 12, 2013 | 1:04:11 PM
 * Last Update:    @zaher.reda    Mar 12, 2013 | 1:04:11 PM
 */

require '../inc/init.php';

$file_path = 'Integration - Business Partners.csv';

$csv = new CSV($file_path, 1, true, ';');

$csv->readdata_file();

$data = $csv->get_data();

switch($core->input['datatype']) {
	case 'purchases':
		$tables = array(
				'integration_mediation_purchaseorders' => array('pk' => 'impoid', 'identifier' => 'foreignId', 'attrAlias' => array('orderId' => 'foreignId')),
				'integration_mediation_purchaseorderlines' => array('pk' => 'impolid', 'identifier' => 'foreignId', 'attrAlias' => array('orderLineId' => 'foreignId', 'orderId' => 'foreignOrderId')));
		$required_fields = array(
				'integration_mediation_purchaseorderlines' => array('foreignId', 'foreignOrderId', 'pid'),
				'integration_mediation_purchaseorders' => array('foreignId', 'date', 'spid'));
		break;
	case 'sales':
		$tables = array(
				'integration_mediation_salesorders' => array('pk' => 'imsoid', 'identifier' => 'foreignId', 'attrAlias' => array('invoiceId' => 'foreignId')),
				'integration_mediation_salesorderlines' => array('pk' => 'imsolid', 'identifier' => 'foreignId', 'attrAlias' => array('invoiceLineId' => 'foreignId', 'invoiceId' => 'foreignOrderId')));
		$required_fields = array(
				'integration_mediation_salesorderlines' => array('foreignId', 'foreignOrderId', 'pid'),
				'integration_mediation_salesorders' => array('foreignId', 'date', 'spid'));
		break;
	case 'entities':
		$tables = array('integration_mediation_entities' => array('pk' => 'imspid', 'identifier' => 'foreignId'));
		$required_fields = array('integration_mediation_entities' => array('foreignId', 'foreignName', 'entityType'));
		break;
	case 'products':
		$tables = array('integration_mediation_products' => array('pk' => 'impid', 'identifier' => 'foreignId'));
		$required_fields = array('integration_mediation_products' => array('foreignId', 'foreignName', 'foreignSupplier'));
		break;
	default:
		error('Unkown data type');
		exit;
		break;
}

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
	global $db, $errorhandler, $data, $tables, $tables_fields, $required_fields;
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

					if(in_array($true_attr, $tables_fields[$table])) {
						$tables_fields_values[$table][$true_attr] = $val;
					}
				}

				if($validate == false) {
					if(!value_exists($table, $table_config['identifier'], $tables_fields_values[$table][$table_config['identifier']])) {
						if($runtype != 'dry') {
							$db->insert_query($table, $tables_fields_values[$table]);
						}
						print_r($tables_fields_values[$table]);
						echo '<hr />';
					}
				}
			}
		}
	}
}

?>