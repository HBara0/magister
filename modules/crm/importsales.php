<?php 
/*
* Orkila Central Online System (OCOS)
* Copyright © 2010 Orkila International Offshore, All Rights Reserved
* Import Sales
* $module: CRM
* Created		@zaher.reda 		September 7, 2012 | 3:41 PM
* Last Update: 	@zaher.reda 		September 7, 2012 | 3:41 PM
*/
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['crm_canImportSales'] == 0) {
	error($lang->sectionnopermission);
}

$session->start_phpsession();
  
$lang->load('crm_importsales');
if(!$core->input['action']) {
	$formatdates = array(1 => 'm/d/Y', 2 => 'm-d-Y', 3 => 'd/m/Y', 4 => 'd-m-Y', 5 => 'Y-m-d');
	asort($formatdates);
	foreach($formatdates as $key => $formatvalue) {
		$formatdates[$key] = $formatvalue.' (ex. '.date($formatvalue).')';
	}

	$affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'));
	$affiliates_list = parse_selectlist('affid', 1, $affiliates, $affid, 0);

	$dateformats_selectlist = parse_selectlist('dateformat', '2', $formatdates, 1);
    eval("\$importpage = \"".$template->get('crm_salesimport')."\";");
    output_page($importpage);
}	   
else
{ 
	if($core->input['action'] == 'import') {
		$upload = new Uploader('uploadfile', $_FILES, array('application/csv', 'application/excel', 'application/octet-stream','application/x-excel' , 'text/csv' ,'text/comma-separated-values','application/vnd.ms-excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'), 'readonly', 5242880, 0, 1);

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
		$sales_data = $csv_file->get_data();
		?>
		<script language="javascript" type="text/javascript">
		$(function() {
			return window.top.$("#upload_Result").html('<?php echo addslashes(parse_datapreview($csv_header, $sales_data));?>');
		}); 
		</script>   
	<?php 
	exit;
	}	
	elseif($core->input['action'] == 'do_perform_importsales')
	{	
		$cache = new Cache();
		$error_handler = new ErrorHandler();
		$allowed_headers = array('foreignId', 'docNum', 'date', 'customer', 'currency', 'usdFxrate', 'paymentTerms', 'salesRep', 'orderLineForeignId', 'product', 'supplier', 'price', 'quantity', 'quantityUnit', 'cost', 'costCurrency', 'saleType');
		
		$order_headers = array('foreignId', 'docNum', 'date', 'cid', 'currency', 'usdFxrate', 'paymentTerms', 'salesRep', 'saleType');
		$orderline_headers = array('orderLineForeignId', 'pid', 'spid', 'price', 'quantity', 'quantityUnit', 'cost', 'costCurrency');
		
		$allowed_formatdates = array(1 => 'm/d/Y', 2 => 'm-d-Y', 3 => 'd/m/Y', 4 => 'd-m-Y', 5 => 'Y-m-d');
		
		$headers_cache = array();
		
		for($i=0; $i < count($allowed_headers); $i++) {	
			if((in_array($core->input['selectheader_'.$i], $headers_cache))) {
			if(empty($core->input['selectheader_'.$i])) {  
					//$error_text = $lang->fillallrequiredfields; //clarify names
					//$error_title = $lang->emptyfield;
				continue;
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
			elseif(empty($core->input['selectheader_'.$i])) {
				continue;
			}
			else
			{
				$headers_cache[] = $core->input['selectheader_'.$i];
			}
		}	 
		unset($headers_cache);
		
		$options = unserialize($session->get_phpsession('sinfo_'.$core->input['identifier']));
		$sales_data = unserialize($session->get_phpsession('sdata_'.$core->input['identifier']));
		//uasort($attendance_data, 'custom_sort_reverse');
		
		foreach($sales_data as $key => $row) {
			$data = array();
			/* Set Appropriate headers - START */
			$count_input = 0;
			foreach($row as $key2 => $value) {	
				if(!empty($core->input['selectheader_'.$count_input.''])) {
					$data[$core->input['selectheader_'.$count_input.'']] = $value;
				}
				
				$count_input++;
			}
			/* Set Appropriate headers - END */

			$date = DateTime::createFromFormat($allowed_formatdates[$options['dateformat']], $data['date']);
			
			if($date == false) {
				$error_handler->record('errordateformat', $data['date']);
				$import_errors = $error_handler->get_errors_inline();  	
				
				output_xml("<status>false</status><message>".$lang->sprint($lang->dateformatmismatch, $data['date'], $allowed_formatdates[$options['dateformat']])."</message>");
				exit;	
			}
		
			/* Resolve names if IDs are not provided - START */
			if($options['resolvesuppliername'] == 1) {
				if($cache->incache('suppliers', $data['supplier'])) {
					$data['spid'] = array_search($data['supplier'], $cache->data['suppliers']);
				}
				else
				{
					$data['spid'] = resolve_entity($data['supplier'], 's');
					$cache->add('suppliers', $data['supplier'], $data['spid']);
				}
			}
			else
			{
				$data['spid'] = $data['supplier'];
			}
			
			if($options['resolveproductname'] == 1) {
				if($cache->incache('products', $data['product'])) {
					$data['pid'] = array_search($data['product'], $cache->data['products']);
				}
				else
				{
					$data['pid'] = resolve_productname($data['product'], $data['spid']);
					$cache->add('products', $data['product'], $data['pid']);
				}
			}
			else
			{
				$data['pid'] = $data['product'];
			}
			
			if($options['resolvecustomername'] == 1) {
				if($cache->incache('customers', $data['customer'])) {
					$data['cid'] = $cache->data['customers'][$data['customer']];
				}
				else
				{
					$data['cid'] = resolve_entity($data['customer'], 'c');
					$cache->add('suppliers', $data['customer'], $data['cid']);
				}
			}
			else
			{
				$data['cid'] = $data['customer'];
			}
			unset($data['product'], $data['customer'], $data['supplier']);
			/* Resolve names if IDs are not provided - START */
			
			if(empty($data['orderLineForeignId']) || !isset($data['orderLineForeignId'])) {
				$data['orderLineForeignId'] = substr(md5(uniqid(microtime())), 1,10);
			}
			
			if($cache->incache('orders', $data['docNum'])) {
				$data['foreignId'] = array_search($data['docNum'], $cache->data['orders']);
				
				$orders[$data['foreignId']]['orderlines'][$data['orderLineForeignId']]['foreignId'] = $data['orderLineForeignId'];
				$orders[$data['foreignId']]['orderlines'][$data['orderLineForeignId']]['affid'] = $options['affid'];
				//$orders[$data['foreignId']]['orderlines'][$data['orderLineForeignId']]['foreignSystem'] = $options['foreignSystem'];
				$orders[$data['foreignId']]['orderlines'][$data['orderLineForeignId']]['foreignOrderId'] = $data['foreignId'];
		
				foreach($orderline_headers as $attr) {
					$orders[$data['foreignId']]['orderlines'][$data['orderLineForeignId']][$attr] = $data[$attr];
				}
				unset($orders[$data['foreignId']]['orderlines'][$data['orderLineForeignId']]['orderLineForeignId']);
			}
			else
			{
				if(empty($data['foreignId']) || !isset($data['foreignId'])) {
					/* Generate Random Id */
					$data['foreignId'] = substr(md5(uniqid(microtime())), 1,10);	
				}
				
				$cache->add('orders', $data['docNum'], $data['foreignId']);
				
				foreach($order_headers as $attr) {
					//echo $attr."\n";
					$orders[$data['foreignId']]['info'][$attr] = $data[$attr];
				}

				$orders[$data['foreignId']]['info']['affid'] = $options['affid'];;
				$orders[$data['foreignId']]['info']['foreignSystem'] = $options['foreignSystem'];
				$orders[$data['foreignId']]['info']['date'] = $date->getTimestamp();
				
				$orders[$data['foreignId']]['orderlines'][$data['orderLineForeignId']]['foreignId'] = $data['orderLineForeignId'];
				$orders[$data['foreignId']]['orderlines'][$data['orderLineForeignId']]['affid'] = $options['affid'];
				//$orders[$data['foreignId']]['orderlines'][$data['orderLineForeignId']]['foreignSystem'] = $options['foreignSystem'];
				$orders[$data['foreignId']]['orderlines'][$data['orderLineForeignId']]['foreignOrderId'] = $data['foreignId'];

				foreach($orderline_headers as $attr) {
					$orders[$data['foreignId']]['orderlines'][$data['orderLineForeignId']][$attr] = $data[$attr];
				}
				
				unset($orders[$data['foreignId']]['orderlines'][$data['orderLineForeignId']]['orderLineForeignId']);
			}
		}

		foreach($orders as $id => $order) {
			$new_order = $order['info'];
			
			$foreignorder_id = $db->fetch_field($db->query('SELECT foreignId FROM '.Tprefix.'integration_mediation_salesorders WHERE docNum="'.$db->escape_string($new_order['docNum']).'"'), 'foreignId');
			if(!empty($foreignorder_id) && isset($foreignorder_id)) {
				unset($new_order['foreignId']);
				$query = $db->update_query('integration_mediation_salesorders', $new_order, 'docNum="'.$db->escape_string($new_order['docNum']).'"');
				if($query) {
					$db->delete_query('integration_mediation_salesorderlines', 'affid="'.$new_order['affid'].'" AND foreignSystem="'.$new_order['foreignSystem'].'" AND foreignOrderId="'.$foreignorder_id.'"');
					foreach($order['orderlines'] as $olid => $line) {
						$line['foreignOrderId'] = $foreignorder_id;
						$db->insert_query('integration_mediation_salesorderlines', $line);
					}
				}
			}
			else
			{
				$db->insert_query('integration_mediation_salesorders', $new_order);
				foreach($order['orderlines'] as $olid => $line) {
					$db->insert_query('integration_mediation_salesorderlines', $line);
				}
			}
		}
		
		$import_errors = $error_handler->get_errors_inline();
		$log->record();
		if(isset($import_errors) && !empty($import_errors)) {
			output_xml("<status>false</status><message>{$lang->resulterror}<![CDATA[<br />{$import_errors}]]></message>");  
		}
		else
		{
			output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
		}		
	}
	else
	{
	 	eval("\$importpage = \"".$template->get('crm_salesimport')."\";");
		output_page($importpage);
	}	 
} 

function parse_datapreview($csv_header, $data) {
	global $session, $lang, $core;	

	$output .= '<span class="subtitle">'.$lang->importsalespreview.'</span><br /><form id="perform_crm/importsales_Form"><table class="datatable"><tr>'; //Lng file title

	$allowed_headers = array('foreignId', 'docNum', 'date', 'customer', 'currency', 'usdFxrate', 'paymentTerms', 'salesRep', 'orderLineForeignId', 'product', 'supplier', 'price', 'quantity', 'quantityUnit', 'cost', 'costCurrency', 'saleType');
		
	foreach($csv_header as $header_key => $header_val) {
		$output .= '<td><select name="selectheader_'.$header_key.'" id="selectheader_'.$header_key.'">';
		$output .= '<option value="">&nbsp;</option>';
		foreach($allowed_headers as $allowed_header_key) {
			if($header_val == $allowed_header_key) {
				$selected_header = ' selected="selected"';
			}
			else
			{
				$selected_header = '';
			} 
			
			$allowed_header_val = $lang->{strtolower($allowed_header_key)};
			if(empty($allowed_header_val)) {
				$allowed_header_val = $allowed_header_key;
			}
			$output .= '<option value="'.$allowed_header_key.'"'.$selected_header.'>'.$allowed_header_val.'</option>';
			$selected_header = '';
		}
		$output .= '</select></td>';
	}	
	
	$output .= '</tr>';

	/* Loop over and display the CSV attendance data - START */
	foreach($data as $key => $val) {
		$rowclass = alt_row($rowclass);	
		$output .= '<tr class="'.$rowclass.'">';
		foreach($val as $value) {
			$output .= '<td>'.$value.'</td>';
		}
		$output .= '</tr>';
	}
	/* Loop over the CSV attendance data - END */
	$identifier = md5(uniqid(microtime()));
	/* serialize and send the attendance_data via user session */
	$session->set_phpsession(array('sinfo_'.$identifier => serialize($core->input)));
	$session->set_phpsession(array('sdata_'.$identifier => serialize($data)));
	
	$output .= '<tr><input type="hidden" name="identifier" id="identifier" value="'.$identifier.'"/></table></form><hr /><input type="button" value="'.$lang->savecaps.'" class="button" id="perform_crm/importsales_Button" name="perform_crm/importsales_Button"/></br>';
	$output .= '<div id="perform_crm/importsales_Results"></div>';
	return trim($output);
}
		
function resolve_productname($product, $supplier='') {
	global $db, $options;
	/* Check mediation table */
	$match = $db->fetch_field($db->query('SELECT foreignId FROM '.Tprefix.'integration_mediation_products WHERE foreignName="'.$db->escape_string($product).'" AND affid="'.$db->escape_string($options['affid']).'" AND foreignSystem="'.$db->escape_string($options['foreignSystem']).'"'), 'foreignId');
	if(!empty($match)) {
		return $match;
	}
	else
	{
		/* Check products table */	
		$local = $db->fetch_field($db->query('SELECT pid FROM '.Tprefix.'products WHERE name="'.$db->escape_string($product).'"'), 'pid');
		
		$new_product = array(
			'foreignSystem' => $options['foreignSystem'],
			'foreignId' => substr(md5(uniqid(microtime())), 1,5).bin2hex($product),
	 		'foreignName' => $product,
			'affid' => $options['affid'],
			'localId' => $local
		);
		
		if(!empty($supplier)) {
			$new_product['foreignSupplier'] = $supplier;	
		}
		$db->insert_query('integration_mediation_products', $new_product);
		return $new_product['foreignId'];
	}
	return false;
}

function resolve_entity($entity, $type) {
	global $db, $options;
	/* Check mediation table */
	$match = $db->fetch_field($db->query('SELECT foreignId FROM '.Tprefix.'integration_mediation_entities WHERE foreignName="'.$db->escape_string($entity).'" AND affid="'.$db->escape_string($options['affid']).'" AND foreignSystem="'.$db->escape_string($options['foreignSystem']).'" AND entityType="'.$db->escape_string($type).'"'), 'foreignId');
	if(!empty($match)) {
		return $match;
	}
	else
	{
		$local = $db->fetch_field($db->query('SELECT eid FROM '.Tprefix.'entities WHERE companyName="'.$db->escape_string($entity).'" AND type="'.$db->escape_string($type).'"'), 'eid');

		$new_entity = array(
			'foreignSystem' => $options['foreignSystem'],
			'foreignId' => substr(md5(uniqid(microtime())), 1,5).bin2hex($entity),
	 		'foreignName' => $entity,
			'affid' => $options['affid'],
			'entityType' => $type,
			'localId' => $local
		);
		$db->insert_query('integration_mediation_entities', $new_entity);
		return $new_entity['foreignId'];
	}
	return false;
}

function custom_sort($a, $b) {
	if($a == $b) { return 0; }
	if($a > $b) { return 1; } else { return -1; }
}

function custom_sort_reverse($a, $b) {
	if($a == $b) { return 0; }
	if($a > $b) { return -1; } else { return 1; }
}
?>