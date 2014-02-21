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
			//'933EC892369245E485E922731D46FCB1' => 20, //Orkila Senegal
			'51FB1280AB104EFCBBB982D50B3B7693' => 21 //Orkila CI
	);

	$affiliates_addrecpt = array(
			19 => array(244),
			22 => array(248, 246, 287, 270),
			23 => array(285, 322, 321)
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
			'quantitysold' => array('source' => 'stack', 'attribute' => 'soldqty', 'title' => 'Sold Qty', 'numformat' => true),
			'quantity' => array('source' => 'stack', 'attribute' => 'remaining_qty', 'title' => 'Stock Qty', 'numformat' => true),
			'uom' => array('source' => array('product', 'uom'), 'attribute' => 'uomsymbol', 'title' => 'UoM'),
			'unitcost' => array('source' => null, 'title' => 'Unit Cost', 'numformat' => true),
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

	$total_types = array('quantitysold', 'quantity', 'cost', 'costusd');
	foreach($affiliates_index as $orgid => $affid) {
		$output = '';
		$totals = $summaries = array();
		$affiliateobj = new Affiliates($affid, false);
		$affiliate = $affiliateobj->get();
		$affiliate['currency'] = $affiliateobj->get_country()->get_maincurrency()->get()['alphaCode'];
		$output = '<h3>Stock Summary Report - '.$affiliate['name'].' - Week '.$date_info['week'].' ( '.$affiliate['currency'].')</h3>';
		$inputs = $integration->get_fifoinputs(array($orgid), array('hasqty' => true));

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
								}
								$input['supplier']['name'] = $input['supplier']['value'] = $output_value;
								$input['supplier']['c_bpartner_id'] = md5($output_value);
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
					else {
						switch($field) {
							case 'unitcost':
								$output .= '<td style="border: 1px solid #CCC; text-align: right;">'.number_format($input['stack']['remaining_cost'] / $input['stack']['remaining_qty'], $report_options['roundto'], '.', ' ').'</td>';
								break;
							case 'costusd':
								$rate = $currency_obj->get_average_fxrate($affiliate['currency'], array());
								if(empty($rate)) {
									$rate = $currency_obj->get_latest_fxrate($affiliate['currency']);
								}
								$output_value = $input['stack']['remaining_cost'] / $rate;
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

								$totals['costusd'][$date_valueobj->format('Y')][$date_valueobj->format('n')] += $input['stack']['remaining_cost'] / $rate;
								break;
						}
					}
				}
				$output .= '</tr>';
			}
		}
		else {
			$output .= '<tr><td colspan="16">N/A</td></tr>';
		}
		$output .= '</table>';

		/* Parse Summaries - Start */
		if(is_array($summaries)) {
			foreach($summaries as $category => $category_data) {
				$output .= '<h3>'.$output_fields[$category]['title'].' Summary</h3>';
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
								if($output_fields[$output_key]['numformat'] == true) {
									$output_value_temp = number_format($output_value_temp, $report_options['roundto'], '.', ' ');
									$output_td_style = ' text-align: right;';
								}
								$output_value .= $output_value_temp.' '.$output_key_temp.'<br />';
							}
						}
						else {
							if($output_fields[$output_key]['numformat']) {
								if($output_fields[$output_key]['numformat'] == true) {
									$output_value = number_format($output_value, $report_options['roundto'], '.', ' ');
									$output_td_style = ' text-align: right;';
								}
							}
						}
						$output .= '<td style="border: 1px solid #CCC; width: 25%;'.$output_td_style.'">'.$output_value.'</td>';
					}
					$output .= '</tr>';
				}
				$output .= '</table>';
			}
		}

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

		$message = '<html><head><title>Stock Summary</title></head><body>';
		$message .= $output;
		$email_data = array(
				'from_email' => $core->settings['maileremail'],
				'from' => 'OCOS Mailer',
				'subject' => 'Stock Summary Report - '.$affiliate['name'].' - Week '.$date_info['week'],
				'message' => $message
		);
		
		$email_data['to'][] = $affiliateobj->get_generalmanager()->get()['email'];
		$email_data['to'][] = $affiliateobj->get_supervisor()->get()['email'];

		if(isset($affiliates_addrecpt[$affid])) {
			foreach($affiliates_addrecpt[$affid] as $uid) {
				$adduser = new Users($uid);
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
