<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: importbudget.php
 * Created:        @tony.assaad    Sep 30, 2013 | 3:37:13 PM
 * Last Update:    @tony.assaad    Sep 30, 2013 | 3:37:13 PM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}
$session->start_phpsession();

if(!$core->input['action']) {
	eval("\$importpage = \"".$template->get('budgeting_importbuget')."\";");
	output_page($importpage);
}
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
				$(function() {
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
		$(function()
		{
			return window.top.$("#upload_Result").html("<?php echo addslashes(parse_datapreview($csv_header, $data));?>");
		});
	</script>
	<?php
}
elseif($core->input['action'] == 'do_perform_importbudget') {
	$cache = new Cache();

	$all_data = unserialize($session->get_phpsession('budgetingimport_'.$core->input['identifier']));
	$allowed_headers = array('affiliate' => 'Affiliate', 'salesManager' => 'Sales Manager', 'CustomerID' => 'Cutomer ID', 'customerName' => 'Customer Name', 'country' => 'Country', 'supplierID' => 'Supplier ID', 'supplierName' => 'Supplier Name', 'productID' => 'Product ID', 'productName' => 'Product Name', 'year' => 'Year', 'quantity' => 'Quantity', 'uom' => 'Unit of Measure', 'amount' => 'Sales amount', 'income' => 'Income', 'originalCurrency' => 'Currency', 'segment' => 'Market Segment', 'saleType' => 'Sale Type');
	$required_headers_check = $required_headers = array('customerName', 'productName', 'supplierName', 'year', 'saleType');
	$budgetlines_valid_data = array('pid', 'cid', 'quantity', 'amount', 'income', 'saleType', 'createdBy', 'bid', 'originalCurrency');

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
		$count_input = 0;
		foreach($budgetrow as $header => $value) {
			if(empty($value)) {
				$count_input++;
				continue;
			}
			if(!empty($core->input['selectheader_'.$count_input])) {
				$data[$core->input['selectheader_'.$count_input]] = $db->escape_string(utf8_encode(trim(strtolower($value))));
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
					//Record Error
					continue;
				}
				$cache->add('affiliates', $data['affiliate'], $data['affid']);
			}
		}
		else {
			$data['affid'] = $data['affiliate'];
		}

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
					$data['spid'] = $db->fetch_field($db->query('SELECT localId FROM '.Tprefix.'integration_mediation_entities WHERE foreignName="'.$db->escape_string($data['supplierName']).'" AND affid="'.$db->escape_string($data['affid']).'" AND entityType="s"'), 'localId');
					if(empty($data['spid'])) {
						//Record Error
						continue;
					}
				}
				$cache->add('suppliers', $data['supplierName'], $data['spid']);
			}
		}
		else {
			$data['spid'] = $data['supplier'];
		}

		if($options['resolveproductname'] == 1 || true) {
			if($cache->incache('products', $data['productName'])) {
				$data['pid'] = array_search($data['productName'], $cache->data['products']);
			}
			else {
				$data['pid'] = Products::get_product_byname($data['productName']);
				if($data['pid'] != false) {
					$data['pid'] = $data['pid']->get()['pid'];
				}
				if(empty($data['pid'])) {
					$data['pid'] = $db->fetch_field($db->query('SELECT locaId FROM '.Tprefix.'integration_mediation_products WHERE foreignName="'.$db->escape_string($data['productName']).'" AND affid="'.$db->escape_string($data['affid']).'"'), 'localId');
					if(empty($data['pid'])) {
						//Record Error
						continue;
					}
				}
				$cache->add('products', $data['productName'], $data['pid']);
			}
		}
		else {
			$data['pid'] = $data['product'];
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
					$data['cid'] = $db->fetch_field($db->query('SELECT localId FROM '.Tprefix.'integration_mediation_entities WHERE foreignName="'.$db->escape_string($data['customerName']).'" AND affid="'.$db->escape_string($data['affid']).'" AND entityType="c"'), 'localId');
					if(empty($data['cid'])) {
						//Record Error
						continue;
					}
				}
				$cache->add('customers', $data['customerName'], $data['cid']);
			}
		}
		else {
			$data['cid'] = $data['customer'];
		}

		if($options['resolvebmname'] == 1 || true) {
			if($cache->incache('employees', $data['salesManager'])) {
				$data['uid'] = array_search($data['salesManager'], $cache->data['employees']);
			}
			else {
				$data['uid'] = Users::get_user_byattr('displayName', $data['salesManager']);
				if($data['uid'] != false) {
					$data['uid'] = $data['uid']->get()['uid'];
				}
				if(empty($data['uid'])) {
					//Record Error
					continue;
				}
				$cache->add('employees', $data['salesManager'], $data['uid']);
			}
		}
		else {
			$data['uid'] = $data['salesManager'];
		}
		/* Resolve names if IDs are not provided - END */



		/* get afiiliate by country */
//		if(isset($data_row[$key]['Country'])) {
//			$existing_coid = $db->fetch_field($db->query("SELECT coid as affid FROM ".Tprefix."countries WHERE name= '{$data_row[$key]['Country']}'"), 'affid');
//			if(!empty($existing_coid)) {
//				$budgetline_datarow[$key]['affid'] = $existing_coid;
//			}
//		}


		/* Check  only those whose supplier,  were successfully matched to one in each of their table) */
//		if(isset($data_row[$key]['Supplier Name'])) {
//			$existing_eid = $db->fetch_field($db->query("SELECT eid as spid FROM ".Tprefix."entities WHERE companyName= '{$data_row[$key]['Supplier Name']}'"), 'spid');
//			if(!empty($existing_eid)) {
//				$budgetline_datarow[$key]['spid'] = $existing_eid;
//			}
//		}

		$budget_data = array('identifier' => substr(uniqid(time()), 0, 10),
				'year' => $data['year'],
				'affid' => $data['affid'],
				'spid' => $data['spid']
		);

		/* create budget */
		//if((!empty($budgetline_datarow[$key]['spid']) && isset($budgetline_datarow[$key]['spid'])) && (!empty($data_row[$key]['Year']) && isset($data_row[$key]['Year']))) {
		//$budget_obj = new Budgets();
		foreach($budgetlines_valid_data as $key) {
			$budgetlines[0][$key] = $data[$key];
		}

		Budgets::save_budget($budget_data, $budgetlines);
		//}

		/* Check if Cusotmer matched to one in their table */
//		if(isset($data_row[$key]['Customer Name'])) {
//			$existing_cid = $db->fetch_field($db->query("SELECT eid AS cid FROM ".Tprefix."entities WHERE companyName= '{$data_row[$key]['Customer Name']}'"), 'cid');
//			if(!empty($existing_cid)) {
//				$budgetline_datarow[$key]['cid'] = $existing_cid;
//			}
//		}

		/* Check if Cusotmer matched to one in their table */
//		if(isset($data_row[$key]['Product Name'])) {
//			$existing_pid = $db->fetch_field($db->query("SELECT pid FROM ".Tprefix."products WHERE name= '{$data_row[$key]['Product Name']}'"), 'pid');
//			if(!empty($existing_pid)) {
//				$budgetline_datarow[$key]['pid'] = $existing_pid;
//			}
//		}


		/* get user id by sale manager */
//		if(isset($data_row[$key]['Sales Manager'])) {
//			$existing_uid = $db->fetch_field($db->query("SELECT uid FROM ".Tprefix."users WHERE username= '{$data_row[$key]['Sales Manager']}' OR displayName= '{$data_row[$key]['Sales Manager']}'"), 'uid');
//			if(!empty($existing_uid)) {
//				$budgetline_datarow[$key]['uid'] = $existing_uid;
//			}
//		}
		/* get CreatedBy user object */
//		$user_obj = new Users($budgetline_datarow[$key]['uid']);
//		$budgetline_datarow[$key]['createdBy'] = $user_obj->get()['uid'];
//
//		$budgetline_datarow[$key]['amount'] = $data_row[$key]['Sales amount'];
//		$budgetline_datarow[$key]['income'] = $data_row[$key]['Income'];
//		$budgetline_datarow[$key]['saleType'] = $data_row[$key]['Sale Type'];
//		$supplier_id = $budgetline_datarow[$key]['spid'];
//		unset($budgetline_datarow[$key]['affid'], $budgetline_datarow[$key]['spid']);
//		if((isset($budgetline_datarow[$key]['pid']) && !empty($budgetline_datarow[$key]['pid'])) && (isset($budgetline_datarow[$key]['cid']) && !empty($budgetline_datarow[$key]['cid'])) && (isset($supplier_id) && !empty($supplier_id))) {
//			$budget_obj->import_budgetlines($budgetline_datarow[$key]);
//		}
		//print_r($data);
		//$budget_obj->import_budgetlines($data);
		unset($data);
	}
}
function parse_datapreview($csv_header, $data) {
	global $session, $lang, $core;

	$output = "<span class='subtitle'></span><br /><form id='perform_budgeting/importbudget_Form'><table class='datatable'><tr><td colspan='16' class='subtitle' style='text-align:center'>{$lang->importpreview}</td></tr><tr>";
	$allowed_headers = array('affiliate' => 'Affiliate', 'salesManager' => 'Sales Manager', 'CustomerID' => 'Cutomer ID', 'customerName' => 'Customer Name', 'country' => 'Country', 'supplierID' => 'Supplier ID', 'supplierName' => 'Supplier Name', 'productID' => 'Product ID', 'productName' => 'Product Name', 'year' => 'Year', 'quantity' => 'Quantity', 'uom' => 'Unit of Measure', 'amount' => 'Sales amount', 'income' => 'Income', 'originalCurrency' => 'Currency', 'segment' => 'Market Segment', 'saleType' => 'Sale Type');

	$abbreviation = array('Ltd.', 'Ltd', 'Llc.', 'Llc', 'Sal.', 'Co.,', 'Co.', 'Co');

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
		$output .= '<tr>';
		$val['companyName'] = ucwords($val['companyName']);
		$name = explode(' ', $val['companyName']);

		foreach($abbreviation as $abb) {
			$search = array_search($abb, $name);
			if($search)
				$name[$search] = strtoupper($name[$search]);
		}
		$val['companyName'] = implode(' ', $name);
		foreach($val as $id => $value) {
			$output .= '<td id="'.$id.'"valign="top" style="width:20px;">'.utf8_encode($value).'</td>';
		}

		$output .= '</tr>';
	}

	$identifier = md5(uniqid(microtime()));
	$session->set_phpsession(array('budgetingimport_'.$identifier => serialize($data)));
	$output .= '<tr><input type="button" value="'.$lang->import.'" class="button" id="perform_budgeting/importbudget_Button" name="perform_budgeting/importbudget_Button"/><input type="hidden" name="identifier" id="identifier" value="'.$identifier.'"/><input type="hidden" name="multivalueseperator" id="multivalueseperator" value="'.$core->input['multivalueseperator'].'"/></table></form><div id="perform_budgeting/importbudget_Results"></div>';
	return $output;
}
?>

