<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2010 Orkila International Offshore, All Rights Reserved
 * Import Stock
 * $module: Stock
 * Created		@zaher.reda 		September 7, 2012 | 3:41 PM
 * Last Update: 	@zaher.reda 		September 7, 2012 | 3:41 PM
 */
if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['stock_canImport'] == 0) {
	//error($lang->sectionnopermission);
}

$session->start_phpsession();

if(!$core->input['action']) {
	$formatdates = array(1=>'m/d/Y', 2=>'m-d-Y', 3=>'d/m/Y', 4=>'d-m-Y', 5=>'Y-m-d');
	asort($formatdates);
	foreach($formatdates as $key=>$formatvalue) {
		$formatdates[$key] = $formatvalue.' (ex. '.date($formatvalue).')';
	}

	$affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by'=>'name', 'sort'=>'ASC'));
	$affiliates_list = parse_selectlist('filteraffiliate', 1, $affiliates, $affid, 0);

	$dateformats_selectlist = parse_selectlist('dateformat', '2', $formatdates, 1);
	eval("\$importpage = \"".$template->get('stock_import')."\";");
	output_page($importpage);
}
else {
	if($core->input['action'] == 'import') {
		$upload = new Uploader('uploadfile', $_FILES, array('application/csv', 'application/excel', 'application/octet-stream', 'application/x-excel', 'text/csv', 'text/comma-separated-values', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'), 'readonly', 5242880, 0, 1);

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

		$csv_header = $csv_file->get_header();
		$attendance_data = $csv_file->get_data();
		?>
		<script language="javascript" type="text/javascript">
			$(function() {
				return window.top.$("#upload_Result").html('<?php echo addslashes(parse_datapreview($csv_header, $attendance_data));?>');
			});
		</script>
		<?php
		exit;
	}
	elseif($core->input['action'] == 'do_perform_importdata') {
		$error_handler = new ErrorHandler();
		$allowed_headers = array('spid'=>$lang->supplier, 'pid'=>$lang->product, 'date'=>$lang->date, 'amount'=>$lang->amount, 'currency'=>$lang->currency, 'usdFxrate'=>$lang->fxrate, 'quantity'=>$lang->quantity, 'quantityUnit'=>$lang->quantityunit, 'orderId'=>$lang->orderid, 'orderLineId'=>$lang->orderlineid);
		$allowed_formatdates = array(1=>'m/d/Y', 2=>'m-d-Y', 3=>'d/m/Y', 4=>'d-m-Y', 5=>'Y-m-d');

		$headers_cache = array();

		for($i = 0; $i < count($allowed_headers); $i++) {
			if((empty($core->input['selectheader_'.$i])) || (in_array($core->input['selectheader_'.$i], $headers_cache))) {
				if(empty($core->input['selectheader_'.$i])) {
					$error_text = $lang->fillallrequiredfields; //clarify names
					$error_title = $lang->emptyfield;
				}

				if(in_array($core->input['selectheader_'.$i], $headers_cache)) {
					$error_text = $lang->fieldrepeated; //clarify names
					$error_title = $lang->repeatedvalue;
				}

				$js = "<script language='javascript' type='text/javascript'>
							$('#errordialog').remove();
							$('body').append(\"<div id='errordialog' title= '{$error_title}'>{$error_text}</div>\");
							$('#errordialog').dialog({bgiframe: true, closeOnEscape: false, modal: true,width: 460, maxWidth: 460, minHeight: 50, zIndex: 1, draggable: false});
						</script>";
				output_xml("<status>false</status><message><![CDATA[{$js}]]></message>");
				exit;
			}
			else {
				$headers_cache[] = $core->input['selectheader_'.$i];
			}
		}
		unset($headers_cache);

		$stock_data = unserialize($session->get_phpsession('sdata_'.$core->input['identifier']));
		//uasort($attendance_data, 'custom_sort_reverse');

		$users_cache = array();

		foreach($attendance_data as $key=>$data) {
			$count_input = 0;
			foreach($data as $key2=>$row) {
				$data_row[$key][$core->input['selectheader_'.$count_input.'']] = $row;
				$count_input++;
			}

			$uid = '';
			if(in_array($data_row[$key]['name'], $users_cache)) {
				$uid = array_search($data_row[$key]['name'], $users_cache);
			}
			else {
				$split_name = explode(' ', $data_row[$key]['name']);
				$uid = $db->fetch_field($db->query("SELECT u.uid FROM ".Tprefix."users u JOIN ".Tprefix."affiliatedemployees ae ON (ae.uid=u.uid) WHERE isMain=1 AND affid={$core->user[mainaffiliate]} AND firstName='".$db->escape_string($split_name[0])."' AND lastName='".$db->escape_string($split_name[1])."'"), 'uid');

				$users_cache[$uid] = $data_row[$key]['name'];
			}

			/* If user doesn't exist, record in error, and continue */
			if(empty($uid)) {
				$error_handler->record('usernotrecognized', $data_row[$key]['name']);
				continue;
			}

			$date = DateTime::createFromFormat($allowed_formatdates[$core->input['dateformat']], $data_row[$key]['datetime']);

			if($date == false) {
				$error_handler->record('errordateformat', $data_row[$key]['datetime']);
				$import_errors = $error_handler->get_errors_inline();

				output_xml("<status>false</status><message>".$lang->sprint($lang->dateformatmismatch, $data_row[$key]['datetime'], $allowed_formatdates[$core->input['dateformat']])."</message>");
				exit;
			}
			$datetime_timestamp = $date->getTimestamp();
			$date_timestamp = strtotime($date->format('Y-m-d').' midnight');

			$data_state_name = strtolower(trim($data_row[$key]['state']));

			if(in_array($data_state_name, $accepted_state_name_in)) {
				$details[$uid][$date_timestamp]['in'][] = $datetime_timestamp;
			}
			elseif(in_array($data_state_name, $accepted_state_name_out)) {
				$details[$uid][$date_timestamp]['out'][] = $datetime_timestamp;
			}
			else {
				// change to error handler object : type unkown status => content row number and status name
				$js_state = "<script language='javascript' type='text/javascript'>
								$('#errordialog').remove();
								$('body').append(\"<div id='errordialog' title='{$lang->error}'>{$lang->unacceptedstatename}: '{$data_state_name}'.</div>\");
								$('#errordialog').dialog({bgiframe: true, closeOnEscape: false, modal: true, width: 460, maxWidth: 460, minHeight: 50, zIndex: 1,draggable: false});
							</script>";

				output_xml("<status>false</status><message><![CDATA[{$js_state}]]></message>");
				exit;
			}
		}

		if(is_array($details)) {
			foreach($details as $user_key=>$user_data) {
				foreach($user_data as $date_timestamp=>$inout) {
					if(count($inout['in']) > count($inout['out'])) {
						$count = count($inout['in']);
					}
					else {
						$count = count($inout['out']);
					}

					for($k = 0; $k < $count; $k++) {
						$attendance = array('uid'=>$user_key, 'timeIn'=>$inout['in'][$k], 'timeOut'=>$inout['out'][$k], 'date'=>$date_timestamp);
						if(value_exists('attendance', 'uid', $user_key, "(timeIn='{$inout[in][$k]}' OR timeOut='{$inout[out][$k]}') AND date={$date_timestamp}")) {
							$inout['in_output'][$k] = $inout['out_output'][$k] = '-';
							if(!empty($inout['in'][$k])) {
								$inout['in_output'][$k] = date($core->settings['timeformat'], $inout['in'][$k]);
							}

							if(!empty($inout['out'][$k])) {
								$inout['out_output'][$k] = date($core->settings['timeformat'], $inout['out'][$k]);
							}

							$error_handler->record('entryexist', $users_cache[$user_key].': '.date($core->settings['dateformat'], $date_timestamp).': '.$inout['in_output'][$k].' & '.$inout['out_output'][$k]);
						}
						else {
							$db->insert_query('attendance', $attendance);
						}
					}
				}
			}
		}
		$import_errors = $error_handler->get_errors_inline();
		$log->record();
		if(isset($import_errors)) {
			output_xml("<status>false</status><message>{$lang->resulterror}<![CDATA[<br />{$import_errors}]]></message>");
		}
		else {
			output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
		}
	}
	else {
		eval("\$importpage = \"".$template->get('attendance_import')."\";");
		output_page($importpage);
	}
}

function parse_datapreview($csv_header, $data) {
	global $session, $lang, $core;

	$output .= '<span class="subtitle">'.$lang->importstockpreview.'</span><br /><form id="perform_stock/import_Form"><table class="datatable"><tr>'; //Lng file title
	$allowed_headers = array('spid'=>$lang->supplier, 'pid'=>$lang->product, 'date'=>$lang->date, 'amount'=>$lang->amount, 'currency'=>$lang->currency, 'usdFxrate'=>$lang->fxrate, 'quantity'=>$lang->quantity, 'quantityUnit'=>$lang->quantityunit, 'orderId'=>$lang->orderid, 'orderLineId'=>$lang->orderlineid);

	foreach($csv_header as $header_key=>$header_val) {
		$output .= '<td><select name="selectheader_'.$header_key.'" id="selectheader_'.$header_key.'">';
		$output .= '<option value="">&nbsp;</option>';
		foreach($allowed_headers as $allowed_header_key=>$allowed_header_val) {
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

	$output .= '</tr>';

	/* Loop over and display the CSV attendance data - START */
	foreach($data as $key=>$val) {
		$rowclass = alt_row($rowclass);
		$output .= '<tr class="'.$rowclass.'">';
		foreach($val as $value) {
			$output .= '<td>'.$value.'</td>';
		}
		$output .='</tr>';
	}
	/* Loop over the CSV attendance data - END */
	$identifier = md5(uniqid(microtime()));
	/* serialize and send the attendance_data via user session */
	$session->set_phpsession(array('attendance_'.$identifier=>serialize($attendance_data)));

	$output .= '<tr><input type="hidden" name="identifier" id="identifier" value="'.$identifier.'"/><input type="hidden" name="dateformat" id="dateformat" value="'.$core->input['dateformat'].'"/></table></form><hr /><input type="button" value="'.$lang->savecaps.'" class="button" id="perform_stock/import_Button" name="perform_stock/import_Button"/></br>';
	$output .= '<div id="perform_stock/import_Results"></div>';
	return trim($output);
}

function custom_sort($a, $b) {
	if($a == $b) {
		return 0;
	}
	if($a > $b) {
		return 1;
	}
	else {
		return -1;
	}
}

function custom_sort_reverse($a, $b) {
	if($a == $b) {
		return 0;
	}
	if($a > $b) {
		return -1;
	}
	else {
		return 1;
	}
}
?>