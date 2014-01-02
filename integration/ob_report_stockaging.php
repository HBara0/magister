<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * Stock Aging Report from OB
 * $id: ob_report_stockaging.php
 * Created:        @zaher.reda    Sep 2, 2013 | 1:04:11 PM
 * Last Update:    @zaher.reda    Sep 2, 2013 | 1:04:11 PM
 */

require '../inc/init.php';

define('AUTHCODE', 'X1iIMm7pG06ip6o95HEa');
if($core->input['authCode'] == AUTHCODE) {
	$currency_obj = new Currencies('USD');

	$db_info = array('database' => 'openbrav_production', 'username' => 'openbrav_appuser', 'password' => '8w8;MFRy4g^3', 'engine' => 'postgre');

	$affiliates_index = array(
			'C08F137534222BD001345BAA60661B97' => 19, //Orkila Tunisia
			'0B366EFAE0524FDAA97A1322A57373BB' => 22, //Orkila East Africa
			'DA0CE0FED12C4424AA9B51D492AE96D2' => 11, //Orkila Nigeria
	);

	$integration = new IntegrationOB($db_info, 'C08F137534222BD001345B7B2E8F182D', $affiliates_index, 3, array('from' => 'last year'));

	$status = $integration->get_status();
	if(!empty($status)) {
		echo 'Error';
		exit;
	}

	$report_options = array('roundto' => 2);

	$output_fields = array(
//			'manager' => 'Business Manager',
			'product' => array('source' => 'product', 'attribute' => 'name', 'title' => 'Product'),
			'packaging' => array('source' => array('transaction', 'attributes'), 'attribute' => 'packaging', 'title' => 'Packaging'),
			'quantity' => 'Quantity',
			'uom' => array('source' => array('product', 'uom'), 'attribute' => 'uomsymbol', 'title' => 'UoM'),
			'cost' => 'Cost',
			'range1cost' => array('source' => array('entries', 'cost'), 'attribute' => 1, 'title' => '0-29<br />Amt', 'numformat' => true, 'style' => 'background-color: #ABD25E;'),
			'range1qty' => array('source' => array('entries', 'qty'), 'attribute' => 1, 'title' => '0-29<br />Qty', 'numformat' => true, 'style' => 'background-color: #ABD25E;'),
			'range2cost' => array('source' => array('entries', 'cost'), 'attribute' => 2, 'title' => '30-59<br />Amt', 'numformat' => true, 'style' => 'background-color: #B8E1F2;'),
			'range2qty' => array('source' => array('entries', 'qty'), 'attribute' => 2, 'title' => '30-59<br />Qty', 'numformat' => true, 'style' => 'background-color: #B8E1F2;'),
			'range3cost' => array('source' => array('entries', 'cost'), 'attribute' => 3, 'title' => '60-89<br />Amt', 'numformat' => true, 'style' => 'background-color: #F2EB80;'),
			'range3qty' => array('source' => array('entries', 'qty'), 'attribute' => 3, 'title' => '60-89<br />Qty', 'numformat' => true, 'style' => 'background-color: #F2EB80;'),
			'range4cost' => array('source' => array('entries', 'cost'), 'attribute' => 4, 'title' => '90-119<br />Amt', 'numformat' => true, 'style' => 'background-color: #F8C830;'),
			'range4qty' => array('source' => array('entries', 'qty'), 'attribute' => 4, 'title' => '90-119<br />Qty', 'numformat' => true, 'style' => 'background-color: #F8C830;'),
			'range5cost' => array('source' => array('entries', 'cost'), 'attribute' => 5, 'title' => '> 120<br />Amt', 'numformat' => true, 'style' => 'background-color: #F1594A;'),
			'range5qty' => array('source' => array('entries', 'qty'), 'attribute' => 5, 'title' => '> 120<br />Qty', 'numformat' => true, 'style' => 'background-color: #F1594A;')
	);

	$totals = array('quantitysold', 'quantity', 'cost', 'costusd');
	foreach($affiliates_index as $orgid => $affid) {
		$output = '';
		$inputs = array();
		$affiliateobj = new Affiliates($affid);
		$affiliate = $affiliateobj->get();
		$affiliate['currency'] = $affiliateobj->get_country()->get_maincurrency()->get()['alphaCode'];
		$output = '<h3>'.$affiliate['name'].' ( '.$affiliate['currency'].')</h3>';
		$rawinputs = $integration->get_fifoinputs(array($orgid), array('hasqty' => true));

		foreach($rawinputs as $input) {
			$inputs[$input['product']['m_product_id']]['product'] = $input['product'];

			if($input['stack']['daysinstock'] < 29) {
				$range = 1;
			}
			elseif($input['stack']['daysinstock'] < 59) {
				$range = 2;
			}
			elseif($input['stack']['daysinstock'] < 89) {
				$range = 3;
			}
			elseif($input['stack']['daysinstock'] < 119) {
				$range = 4;
			}
			else {
				$range = 5;
			}
			$inputs[$input['product']['m_product_id']]['entries']['qty'][$range] += $input['stack']['remaining_qty'];
			$inputs[$input['product']['m_product_id']]['entries']['cost'][$range] += $input['stack']['remaining_cost'];
		}

		$output .= '<table width="100%" border="1" padding="2px">';
		$output .= '<tr>';
		foreach($output_fields as $field => $configs) {
			if(is_array($configs)) {
				$output .= '<th>'.$configs['title'].'</th>';
			}
			else {
				$output .= '<th>'.$configs.'</th>';
			}
		}
		$output .= '</tr>';

		foreach($inputs as $id => $input) {
			$output .= '<tr>';
			foreach($output_fields as $field => $configs) {
				if(is_array($configs)) {
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

					if($configs['numformat'] == true) {
						$output_value = number_format($output_value, $report_options['roundto'], '.', ' ');
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
					$output_style = '';
					if(isset($configs['style'])) {
						$output_style = ' style="'.$configs['style'].'"';
					}
					$output .= '<td '.$output_style.'>'.$output_value.'</td>';
					unset($output_value);
				}
				else {
					switch($field) {
						case 'cost':
							$output .= '<td>'.number_format(array_sum($input['entries']['cost']), $report_options['roundto'], '.', ' ').'</td>';
							break;
						case 'quantity':
							$output .= '<td>'.number_format(array_sum($input['entries']['qty']), $report_options['roundto'], '.', ' ').'</td>';
							break;
					}
				}
			}
			$output .= '</tr>';
		}
		$output .= '</table>';
		echo $output;
	}
}
?>
