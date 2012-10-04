<?php 
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2010 Orkila International Offshore, All Rights Reserved
 *
 * Import Representatives
 * $module: CRM
 * Created		@najwa.kassem 		November 30, 2010 | 11:15 AM
 * Last Update: 	@najwa.kassem		May 27, 2010 | 04:20 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['crm_canImportCustomers'] == 0) {
	error($lang->sectionnopermission);
}

$session->start_phpsession();

if(!$core->input['action']) {
    eval("\$importpage = \"".$template->get('crm_importrepresentatives')."\";");
    output_page($importpage);
}	
else
{ 
	if($core->input['action'] == 'preview') {	
		$upload = new Uploader('uploadfile', $_FILES, array('application/csv', 'application/excel', 'application/x-excel' , 'text/csv' ,'text/comma-separated-values','application/vnd.ms-excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'), 'readonly', 5242880, 0, 1);
		$upload->process_file();
		$filedata = $upload->get_filedata();

		$csv_file = new CSV($filedata, 2, true, $core->input['delimiter']);
		$csv_file->readdata_string();

        $csv_header = $csv_file->get_header();
		$representative_data = $csv_file->get_data();	
	
		eval("\$headerinc = \"".$template->get('headerinc')."\";");
		echo $headerinc;
		
		?>
		<script language="javascript" type="text/javascript">
		$(function() 
		{ 
			return window.top.$("#upload_Result").html("<?php echo addslashes(parse_datapreview($csv_header, $representative_data)); ?>");
		}); 
		</script>   
	<?php 
	}	
	elseif($core->input['action'] == 'do_perform_importrepresentatives') 
	{	
		$allowed_headers = array('name' => 'name', 'email' => 'email', 'entity' => 'entity', 'phone' => 'phone', 'position' => 'position', 'segment' => 'segment'); //Make language; CLEAR NAMES
		$required_headers_check = $required_headers = array('name', 'email', 'entity', 'position', 'segment');
		$alllowercase = array('name', 'email', 'entity', 'phone', 'position', 'segment');
		
		$headers_cache = array();
		
		for($i=0; $i < count($allowed_headers); $i++) {
			if(in_array($core->input['selectheader_'.$i], $headers_cache)) {
				output_xml("<status>false</status><message>{$lang->fieldrepeated}</message>"); 
				exit;
			}
			else
			{	
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
		unset($headers_cache);
		
		$representatives_data = unserialize($session->get_phpsession('crmimportrepresentatives_'.$core->input['identifier']));

		$entities_cache['entities']  = $position_cache['position'] = $segment_cache['segment']= array();
		
		foreach($representatives_data as $key => $row) {
			$count_input = 0;
			foreach($row as $header => $value) {
				if(in_array($header, $required_headers) && empty($value)) {
					$errors['ignored_entries'][] = $lang->row.$key;
					break;
				}
				
				$data_row[$key][$core->input['selectheader_'.$count_input]] = $db->escape_string(utf8_encode(trim(strtolower($value))));
				
				if(!in_array($core->input['selectheader_'.$count_input], $alllowercase)) {
					$data_row[$key][$core->input['selectheader_'.$count_input]] = ucfirst($data_row[$key][$core->input['selectheader_'.$count_input]]);
				}
				$count_input++;
			}

			if($core->validate_email($core->sanitize_email($data_row[$key]['email']))) {
				$data_row[$key]['email'] = $core->sanitize_email($data_row[$key]['email']);
			}
			else
			{
				$errors['invalidemail'][] = $data_row[$key]['name'];
				unset($data_row[$key]);
			}
			
			if(empty($data_row[$key]['entity'])) {
				$errors['noentity'][] = $data_row[$key]['name'];
				unset($data_row[$key]);
			}
	
			$query = $db->query("SELECT rpid FROM ".Tprefix."representatives WHERE LOWER(name)='".trim(strtolower($data_row[$key]['name']))."'");
			
			if($db->num_rows($query) > 0) {
				while($existingrepresentative = $db->fetch_assoc($query))
				{
					$existing_eid = $db->fetch_field($db->query("SELECT eid FROM ".Tprefix."entitiesrepresentatives WHERE rpid='{$existingrepresentative[rpid]}'"), 'eid');
					
					if(!empty($existing_eid)) {
						$action_required = 'update';
						$data_row[$key]['rpid'] = $existingrepresentative['rpid'];
						$entity[$key] = $existing_eid;
					}
					else
					{
						unset($data_row[$key]);
					}
					unset($data_row[$key]['entity']);
				}
			}
			
			else
			{	
				$heid = $db->fetch_field($db->query("SELECT eid FROM ".Tprefix."entities WHERE LOWER(companyName)='".trim(strtolower($data_row[$key]['entity']))."'"), 'eid');
				if(!empty($heid)) {
					$action_required = 'create';
				}
				else
				{
					$errors['entitynotfound'][] = $data_row[$key]['name'];
					unset($data_row[$key]);
				}	
			}
			
			 if(isset($data_row[$key]['entity'])) {
				if(!in_array($data_row[$key]['entity'], $entities_cache['entities'])) {
					$eid = $db->fetch_field($db->query("SELECT eid FROM ".Tprefix."entities WHERE LOWER(companyName)='".trim(strtolower($data_row[$key]['entity']))."'"), 'eid');
					$entities_cache['entities'][$eid] = $data_row[$key]['entity'];
					$entity[$key] = $eid;
				}
				else
				{
					$entity[$key] = array_search($data_row[$key]['entity'], $entities_cache['entities']);
				}
				unset($data_row[$key]['entity']);
			}  
			
			if(isset($data_row[$key]['position'])) {
				$positions_array = explode($core->input['multivalueseperator'], $data_row[$key]['position']);
				foreach($positions_array as $key2 => $val) {
				
					if(!in_array($val, $position_cache['position'])) {
						$posid = $db->fetch_field($db->query("SELECT posid FROM ".Tprefix."positions WHERE name='".str_replace(' ', '', strtolower($val))."'"), 'posid');
						if(!empty($posid)){
							$position_cache['position'][$posid] = $val;
							$representative_positions[$key][] = $posid;
						}	
					}
					else
					{
						$representative_positions[$key][] = array_search($val, $position_cache['position']);
					}
				}
				unset($data_row[$key]['position']);
			} 
			if(isset($data_row[$key]['segment'])) {
				$segments_array = explode($core->input['multivalueseperator'], $data_row[$key]['segment']);
				foreach($segments_array as $key2 => $val) {
					if(!in_array($val, $segment_cache['segment'])) {
						$psid = $db->fetch_field($db->query("SELECT psid FROM ".Tprefix."productsegments WHERE LOWER(title)='".trim(strtolower($val))."'"), 'psid');
						if(!empty($psid)){
							$segment_cache['segment'][$psid] = $val;
							$representative_segments[$key][] = $psid;
						}	
					}
					else
					{
						$representative_segments[$key][] = array_search($val, $segment_cache['segment']);
					}
				}
				unset($data_row[$key]['segment']);
			}  
			if($action_required == 'create') {
				$data_row[$key]['name'] = ucfirst(strtolower($data_row[$key]['name']));
				$query = $db->insert_query('representatives', $data_row[$key]);
				if($query) {
					$rpid = $db->last_id();
					if(isset($entity[$key]))
					{
						$db->insert_query('entitiesrepresentatives', array('rpid' => $rpid, 'eid' => $entity[$key]));	
					}
					if(isset($representative_positions[$key]))
					{
						foreach($representative_positions[$key] as $key2 => $posid) {
							$position_query = $db->insert_query('representativespositions', array('rpid'  => $rpid,'posid' => $posid));
						}
					}
					if(isset($representative_segments[$key]))
					{
						foreach($representative_segments[$key] as $key2 => $psid) {
							$segment_query = $db->insert_query('representativessegments', array('rpid'  => $rpid,'psid' => $psid));
						}
					}		
				}
			}
			else
			{	unset($data_row[$key]['entity']);
				
				$query = $db->update_query('representatives', $data_row[$key], 'rpid="'.$data_row[$key]['rpid'].'"');
				if($query) {
					$db->delete_query('entitiesrepresentatives', 'rpid="'.$data_row[$key]['rpid'].'"');
					if(isset($entity[$key]))
					{
						$db->insert_query('entitiesrepresentatives', array('rpid' => $data_row[$key]['rpid'], 'eid' => $entity[$key]));
					}
					$db->delete_query('representativespositions', 'rpid="'.$data_row[$key]['rpid'].'"');
					if(isset($representative_positions[$key]))
					{
						foreach($representative_positions[$key] as $key2 => $posid) {
							$position_query = $db->insert_query('representativespositions', array('rpid'  => $data_row[$key]['rpid'],'posid' => $posid));
						}
					}
					$db->delete_query('representativessegments', 'rpid="'.$data_row[$key]['rpid'].'"');
					if(isset($representative_segments[$key]))
					{
						foreach($representative_segments[$key] as $key2 => $psid) {
							$segment_query = $db->insert_query('representativessegments', array('rpid'  => $data_row[$key]['rpid'],'psid' => $psid));
						}
					}	
				}
			}
		}
		$log->record();
		if(is_array($errors)) {
			foreach($errors as $key => $val) {
				$importerrors .= '<br /><strong>'.$lang->{$key}.':</strong><ol>';
				foreach($val as $details) {
					if(is_array($details)) {
						//$details = implode(', ', $details);
					}
					$importerrors .= '<li>'.$details.'</li>';
				}
				$importerrors .= '</ol>';
			}		

			output_xml("<status>false</status><message>{$lang->resulterror}<![CDATA[{$importerrors}]]></message>");  
		}
		else
		{
			 $donewitherror = 'Done'.$data_errors;
			output_xml("<status>true</status><message><![CDATA[{$donewitherror}]]></message>"); //Lang fule Successfully imported
		}		
	}
	else
	{
	 	eval("\$importpage = \"".$template->get('crm_importrepresentatives')."\";");
		output_page($importpage);
	}	 
} 

function parse_datapreview($csv_header, $representative_data) {
	global $session, $lang, $core;
	$output = '<span class="subtitle">Import Preview</span><br /><form id="perform_crm/importrepresentatives_Form"><table class="datatable"><tr>';//Lang file title
	$allowed_headers = array('name' => 'name', 'email' => 'email', 'entity' => 'entity', 'phone' => 'phone', 'position' => 'position', 'segment' => 'segment'); //Make language; CLEAR NAMES
	
	foreach($csv_header as $header_key => $header_val) 
	{
		$output .= '<td><select name="selectheader_'.$header_key.'" id="selectheader_'.$header_key.'">';
		$output .= '<option value="">&nbsp;</option>';
		foreach($allowed_headers as $allowed_header_key => $allowed_header_val) 
		{
			if($header_val == $allowed_header_key)
			{
				$selected_header = ' selected="selected"';
			}
			else
			{
				$selected_header = '';
			} 
			
			$output .= '<option value="'.$allowed_header_key.'"'.$selected_header.'>'.$allowed_header_val.'</option>';
			$selected_header = '';
		}
		$output .= '</select></td>';
	}	
	
	$output .= '</tr>';
	foreach($representative_data as $key => $val) {	 
		$output .= '<tr>';
		foreach($val as $value) {
			$output .= '<td>'.utf8_encode($value).'</td>';
		}
		$output .= '</tr>';
	}
	
	$identifier = md5(uniqid(microtime()));
	$session->set_phpsession(array('crmimportrepresentatives_'.$identifier => serialize($representative_data)));
	
	$output .= '<tr><input type="hidden" name="identifier" id="identifier" value="'.$identifier.'"/><input type="hidden" name="dateformat" id="dateformat" value="'.$core->input['dateformat'].'"/><td colspan=6><input type="button" value="'.$lang->savecaps.'" class="button" id="perform_crm/importrepresentatives_Button" name="perform_crm/importrepresentatives_Button"/></td></tr></table></form><div id="perform_crm/importrepresentatives_Results"></div>';
	return $output;
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