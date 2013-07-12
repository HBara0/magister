<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Match data in mediation tables
 * $module: admin/integration
 * $id: matchdata.php	
 * Last Update: @zaher.reda 	September 30, 2011 | 02:26 PM
 */
if(!defined("DIRECT_ACCESS")) {
	die("Direct initialization of this file is not allowed.");
}

if(!$core->input['action']) {
	$affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'));
	$affiliates_list = parse_selectlist('filteraffiliate', 1, $affiliates, $affid, 0);

	eval("\$optionsstagepage = \"".$template->get('admin_integration_matchdata_optionsstage')."\";");
	output_page($optionsstagepage);
}
else
{
	if($core->input['action'] == 'preview_datatomatch') {	
		if(is_empty($core->input['matchitem'])) {
			error($lang->fillallrequiredfields);
		}
		
		$query_filter = ' WHERE foreignSystem='.$db->escape_string($core->input['foreignSystem']);
		
		if(!empty($core->input['filterphrase'])) {
			if(strstr($core->input['filterphrase'], ';')) {
				$core->input['filterphrase'] = explode(';', $core->input['filterphrase']);
				$query_filter .= ' AND (';
				foreach($core->input['filterphrase']  as $phrase) {
					$query_filter .= $query_filter_or.' foreignName LIKE "%'.$db->escape_string($phrase).'%"';
					$query_filter_or = ' OR ';
				}
				$query_filter .= ')';
			}
			else {
				$query_filter .= ' AND foreignName LIKE "%'.$db->escape_string($core->input['filterphrase']).'%"';
			}
			$query_filter_and = ' AND ';
		}
		
		//if($core->input['matchitem'] == 'suppliers' && !empty($core->input['filteraffiliate'])) {
			/*if(empty($query_filter)) {
				$query_filter_and = ' WHERE ';
			}*/
			$query_filter .= ' AND (affid="'.$db->escape_string($core->input['filteraffiliate']).'" OR affid=0)';
	//	}

		$check_query_parameters = array('suppliers' => array('id' => 'eid', 'name' => 'companyName', 'checkAttr' => 'companyName', 'table' => 'entities', 'mediationtable' => 'integration_mediation_entities', 'mediationtableid' => 'imspid', 'quicksearch' => 'supplier', 'addlink' => 'entities/add&type=supplier', 'extrawhere' => serialize(array(0 => array('attr' => 'type', 'value' => 's'))), 'mediationextrawhere' => serialize(array(0 => array('attr' => 'entityType', 'value' => 's')))), 'products' => array('id' => 'pid', 'name' => 'name', 'checkAttr' => 'name', 'table' => 'products', 'mediationtable' => 'integration_mediation_products', 'mediationtableid' => 'impid', 'quicksearch' => 'product', 'addlink' => 'products/add'));
		
		if(isset($check_query_parameters[$core->input['matchitem']]['extrawhere']) && !empty($check_query_parameters[$core->input['matchitem']]['extrawhere'])) {
			$check_query_parameters[$core->input['matchitem']]['extrawhere'] = unserialize($check_query_parameters[$core->input['matchitem']]['extrawhere']);
			foreach($check_query_parameters[$core->input['matchitem']]['extrawhere'] as $parameter) {
				$check_query_extrawhere .= ' AND '.$parameter['attr'].'="'.$parameter['value'].'"';
			}
		}
		
		if(isset($check_query_parameters[$core->input['matchitem']]['mediationextrawhere']) && !empty($check_query_parameters[$core->input['matchitem']]['mediationextrawhere'])) {
			$check_query_parameters[$core->input['matchitem']]['mediationextrawhere'] = unserialize($check_query_parameters[$core->input['matchitem']]['mediationextrawhere']);
			foreach($check_query_parameters[$core->input['matchitem']]['mediationextrawhere'] as $parameter) {
				$query_filter .= ' AND '.$parameter['attr'].'="'.$parameter['value'].'"';
			}
		}
		
		if($core->input['limitfrom'] >= 0 && !empty($core->input['limitnum'])) {
			$query_limit = ' LIMIT '.$core->input['limitfrom'].', '.$core->input['limitnum'];
		}
		$query = $db->query("SELECT m.*, m.{$check_query_parameters[$core->input[matchitem]][mediationtableid]} AS dbkey, t.{$check_query_parameters[$core->input[matchitem]][name]} AS localName 
							FROM ".Tprefix."{$check_query_parameters[$core->input[matchitem]][mediationtable]} m LEFT JOIN ".Tprefix."{$check_query_parameters[$core->input[matchitem]][table]} t ON (t.{$check_query_parameters[$core->input[matchitem]][id]}=m.localId)
							{$query_filter}
							ORDER BY foreignName ASC, localId ASC
							{$query_limit}");
							
		if($db->num_rows($query) > 0) {
			while($entrytomatch = $db->fetch_assoc($query)) {
				$matching_entries = $extra_info = '';
				$integration_entries .= '<tr>';
				if(isset($entrytomatch['foreignSupplier']) && !empty($entrytomatch['foreignSupplier'])) {
					
					$extra_info = $db->fetch_field($db->query('SELECT foreignName FROM '.$check_query_parameters['suppliers']['mediationtable'].' WHERE foreignId="'.$db->escape_string($entrytomatch['foreignSupplier']).'"'), 'foreignName');
					if(!empty($extra_info)) {
						$extra_info = '<div class="font-size:9px">'.$extra_info.'</div>';
					}
				}
				$integration_entries .= '<td><input type="hidden" value="'.$entrytomatch['foreignId'].'" id="foreignId_'.$entrytomatch['dbkey'].'" name="foreignId['.$entrytomatch['dbkey'].']"><input type="hidden" value="'.$entrytomatch['foreignName'].'" id="foreignName_'.$entrytomatch['dbkey'].'" name="foreignName['.$entrytomatch['dbkey'].']">'.$entrytomatch['foreignName'].$extra_info.'</td>';
				$integration_entries .= '<td>&lt;-&gt;</td>';
				
				$check_query = $db->query("SELECT {$check_query_parameters[$core->input[matchitem]][id]} as localId, {$check_query_parameters[$core->input[matchitem]][name]} as localName 
											FROM ".Tprefix."{$check_query_parameters[$core->input[matchitem]][table]} 
											WHERE {$check_query_parameters[$core->input[matchitem]][checkAttr]}='".$db->escape_string($entrytomatch['foreignName'])."'{$check_query_extrawhere}");
				if($db->num_rows($check_query) > 0) {
					$check_results = $db->fetch_assoc($check_query);
					$matching_entries = '<input type="hidden" value="'.$check_results['localId'].'" id="localId_'.$entrytomatch['dbkey'].'" name="localId['.$entrytomatch['dbkey'].']">'.$check_results['localName'];
				}
				else
				{
					$foreignname_parts = explode(' ', $entrytomatch['foreignName']);
					$furthercheck_query_extra = '';
					if(is_array($foreignname_parts)) {
						foreach($foreignname_parts as $part) {
							if(strlen($part) > 4 && is_string($part)) {
								$furthercheck_query_extra .= ' OR '.$check_query_parameters[$core->input['matchitem']]['checkAttr'].' LIKE "%'.$part.'%"';
							}
						}
					}
					
					if(!empty($entrytomatch['localName'])) {
						$furthercheck_query_extra .= ' OR '.$check_query_parameters[$core->input['matchitem']]['checkAttr'].' LIKE "%'.$part.'%"';
					}
					
					$furthercheck_query = $db->query("SELECT {$check_query_parameters[$core->input[matchitem]][id]} as localId, {$check_query_parameters[$core->input[matchitem]][name]} as localName
											FROM ".Tprefix."{$check_query_parameters[$core->input[matchitem]][table]}  
											WHERE (SOUNDEX({$check_query_parameters[$core->input[matchitem]][checkAttr]}) = SOUNDEX('".$db->escape_string($entrytomatch['foreignName'])."') 
											OR {$check_query_parameters[$core->input[matchitem]][checkAttr]} LIKE '%".$db->escape_string($entrytomatch['foreignName'])."%' OR {$check_query_parameters[$core->input[matchitem]][checkAttr]} SOUNDS LIKE '%".$db->escape_string($entrytomatch['foreignName'])."%'{$furthercheck_query_extra}){$check_query_extrawhere}");
						
					if($db->num_rows($furthercheck_query) > 0) {
						$matching_entries = '<select id="localId_'.$entrytomatch['dbkey'].'" name="localId['.$entrytomatch['dbkey'].']">';
						$matching_entries .= '<option value="0">&nbsp;</option>';
						
						while($match = $db->fetch_assoc($furthercheck_query)) {
							$option_selected = '';
							if($entrytomatch['localId'] != 0) {
								if($match['localId'] == $entrytomatch['localId']) {
									$option_selected = ' selected="selected"';
								}
							}
							$matching_entries .= '<option value="'.$match['localId'].'"'.$option_selected.'>'.$match['localName'].'</option>';
						}
						$matching_entries .= '</select>';
					}
					else
					{
						$matching_entries = '<input type="text" id="'.$check_query_parameters[$core->input['matchitem']]['quicksearch'].'_noexception_'.$entrytomatch['dbkey'].'_QSearch" value="'.$entrytomatch['localName'].'" autocomplete="off" size="40px" /><input type="hidden" id="'.$check_query_parameters[$core->input['matchitem']]['quicksearch'].'_'.$entrytomatch['dbkey'].'_id" name="localId['.$entrytomatch['dbkey'].']" value="'.$entrytomatch['localId'].'"/><a href="index.php?module='.$check_query_parameters[$core->input['matchitem']]['addlink'].'" target="_blank"><img src="../images/addnew.png" border="0" alt="'.$lang->add.'"></a><div id="searchQuickResults_'.$check_query_parameters[$core->input['matchitem']]['quicksearch'].'_'.$entrytomatch['dbkey'].'" class="searchQuickResults" style="display:none;"></div>';
					}
				}
				$integration_entries .= '<td>'.$matching_entries.'</td>';
				$integration_entries .= '</tr>';
			}
		}
		else
		{
			$integration_entries = '<tr><td>'.$lang->nomatchfound.'</td></tr>';
		}
		
		eval("\$matchdatapage = \"".$template->get('admin_integration_matchdata_previewstage')."\";");
		output_page($matchdatapage);
	}
	elseif($core->input['action'] == 'perform_matchdata') {
		if(empty($core->input['matchitem'])) {
			output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
		}

		$update_query_parameters = array('suppliers' => array('table' => 'integration_mediation_entities', 'id' => 'imspid'), 'products' => array('table' => 'integration_mediation_products', 'id' => 'impid'));
		if(is_array($core->input['foreignId'])) {
			foreach($core->input['foreignId'] as $key => $val) {
				if(isset($core->input['localId'][$key]) && !empty($core->input['localId'][$key])) {
					$db->update_query($update_query_parameters[$core->input['matchitem']]['table'], array('localId' => $core->input['localId'][$key]), $update_query_parameters[$core->input['matchitem']]['id'].'="'.$db->escape_string($key).'"');
				}
				else
				{
					$errors['nomatchselected'][] = $core->input['foreignName'][$key];
				}
			} 
		}
		
		if(is_array($errors)) {
			foreach($errors as $key => $val) {
				$errors_output .= '<br />'.$lang->$key.'<ul>';
				foreach($val as $error_item) {
					$errors_output .= '<li>'.$error_item.'</li>';
				}
				$errors_output .= '</ul>';
			}
		}
		if(!empty($errors_output)) {
			$errors_output = '<div class="red_text">'.$errors_output.'<div>';
		}
		output_xml("<status>true</status><message>{$lang->successfullymatched}<![CDATA[{$errors_output}]]></message>");
	}
}
?>