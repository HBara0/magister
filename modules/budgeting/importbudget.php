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
		$uploadbudget = new Uploader('uploadbudget', $_FILES, array('application/csv', 'application/excel', 'application/x-excel', 'text/csv', 'text/comma-separated-values', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'), 'readonly', 5242880, 0, 1);
		$uploadbudget->process_file();
		$filedata = $uploadbudget->get_filedata();

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
	$all_data = unserialize($session->get_phpsession('budgetingimport_'.$core->input['identifier']));
	$budgetdata_allowed_headers = array('Sales Manager' => 'Sales Manager', 'Cutomer ID' => 'Cutomer ID', 'Customer Name' => 'Customer Name', 'Country' => 'Country', 'SupplierID' => 'SupplierID', 'Supplier Name' => 'Supplier Name', 'ProductID' => 'ProductID', 'Product Name' => 'Product Name', 'Year' => 'Year', 'Unit of Measure' => 'Unit of Measure', 'Sales amount' => 'Sales amount', 'Income' => 'Income', 'Market Segment' => 'Market Segment', 'Sale Type' => 'Sale Type');
	$required_headers_check = $required_headers = array('Cutomer ID', 'ProductID', 'SupplierID');

	$headers_cache = array();

	for($i = 0; $i < count($budgetdata_allowed_headers); $i++) {
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
				continue;
			}
			if(!empty($core->input['selectheader_'.$count_input])) {
				$data_row[$key][$core->input['selectheader_'.$count_input]] = $db->escape_string(utf8_encode(trim(strtolower($value))));
			}

			$count_input++;
		}

		/* get afiiliate by country */
		if(isset($data_row[$key]['Country'])) {
			$existing_coid = $db->fetch_field($db->query("SELECT coid as affid FROM ".Tprefix."countries WHERE name= '{$data_row[$key]['Country']}'"), 'affid');
			if(!empty($existing_coid)) {
				$budgetline_datarow[$key]['affid'] = $existing_coid;
			}
		}


		/* Check  only those whose supplier,  were successfully matched to one in each of their table) */
		if(isset($data_row[$key]['Supplier Name'])) {
			$existing_eid = $db->fetch_field($db->query("SELECT eid as spid FROM ".Tprefix."entities WHERE companyName= '{$data_row[$key]['Supplier Name']}'"), 'spid');
			if(!empty($existing_eid)) {
				$budgetline_datarow[$key]['spid'] = $existing_eid;
			}
		}
		$budget_data = array('identifier' => substr(uniqid(time()), 0, 10),
				'year' => $data_row[$key]['Year'],
				'affid' => $budgetline_datarow[$key]['affid'],
				'spid' => $budgetline_datarow[$key]['spid'],
				'createdBy' => $core->user['uid'],
				'createdOn' => TIME_NOW
		);
		/* create budget */
		if((!empty($budgetline_datarow[$key]['spid']) && isset($budgetline_datarow[$key]['spid'])) && (!empty($data_row[$key]['Year']) && isset($data_row[$key]['Year']))) {
			$budget_obj = new Budgets();
			$budget_obj->save_budget('', $budget_data, 'import');
		}

		/* Check if Cusotmer matched to one in their table */
		if(isset($data_row[$key]['Customer Name'])) {
			$existing_cid = $db->fetch_field($db->query("SELECT eid AS cid FROM ".Tprefix."entities WHERE companyName= '{$data_row[$key]['Customer Name']}'"), 'cid');
			if(!empty($existing_cid)) {
				$budgetline_datarow[$key]['cid'] = $existing_cid;
			}
		}

		/* Check if Cusotmer matched to one in their table */
		if(isset($data_row[$key]['Product Name'])) {
			$existing_pid = $db->fetch_field($db->query("SELECT pid FROM ".Tprefix."products WHERE name= '{$data_row[$key]['Product Name']}'"), 'pid');
			if(!empty($existing_pid)) {
				$budgetline_datarow[$key]['pid'] = $existing_pid;
			}
		}


		/* get user id by sale manager */
		if(isset($data_row[$key]['Sales Manager'])) {
			$existing_uid = $db->fetch_field($db->query("SELECT uid FROM ".Tprefix."users WHERE username= '{$data_row[$key]['Sales Manager']}' OR displayName= '{$data_row[$key]['Sales Manager']}'"), 'uid');
			if(!empty($existing_uid)) {
				$budgetline_datarow[$key]['uid'] = $existing_uid;
			}
		}
		/* get CreatedBy user object */
		$user_obj = new Users($budgetline_datarow[$key]['uid']);
		$budgetline_datarow[$key]['createdBy'] = $user_obj->get()['uid'];

		$budgetline_datarow[$key]['amount'] = $data_row[$key]['Sales amount'];
		$budgetline_datarow[$key]['income'] = $data_row[$key]['Income'];
		$budgetline_datarow[$key]['saleType'] = $data_row[$key]['Sale Type'];
		$supplier_id = $budgetline_datarow[$key]['spid'];
		unset($budgetline_datarow[$key]['affid'], $budgetline_datarow[$key]['spid']);
		if((isset($budgetline_datarow[$key]['pid']) && !empty($budgetline_datarow[$key]['pid'])) && (isset($budgetline_datarow[$key]['cid']) && !empty($budgetline_datarow[$key]['cid'])) && (isset($supplier_id) && !empty($supplier_id))) {
			print_r($budgetline_datarow[$key]);
			$budget_obj->import_budgetlines($budgetline_datarow[$key]);
		}
	}
}
function parse_datapreview($csv_header, $data) {
	global $session, $lang, $core;

	$output = "<span class='subtitle'></span><br /><form id='perform_budgeting/importbudget_Form'><table class='datatable'><tr><td colspan='16' class='subtitle' style='text-align:center'>{$lang->importpreview}</td></tr><tr>";
	$budgetdata_allowed_headers = array('Sales Manager' => 'Sales Manager', 'Cutomer ID' => 'Cutomer ID', 'Customer Name' => 'Customer Name', 'Country' => 'Country', 'SupplierID' => 'SupplierID', 'Supplier Name' => 'Supplier Name', 'ProductID' => 'ProductID', 'Product Name' => 'Product Name', 'Year' => 'Year', 'Unit of Measure' => 'Unit of Measure', 'Sales amount' => 'Sales amount', 'Income' => 'Income', 'Market Segment' => 'Market Segment', 'Sale Type' => 'Sale Type');
	$abbreviation = array('Ltd.', 'Ltd', 'Llc.', 'Llc', 'Sal.', 'Co.,', 'Co.', 'Co');

	foreach($csv_header['budget'] as $header_key => $header_val) {
		$output .= '<td style="width:20px;"><select name="selectheader_'.$header_key.'" id="selectheader_'.$header_key.'">';
		$output .= '<option value="">&nbsp;</option>';
		foreach($budgetdata_allowed_headers as $allowed_header_key => $allowed_header_val) {
			if($header_val == $allowed_header_key) {
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

