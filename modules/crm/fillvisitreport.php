<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Fill up a visit report
 * $module: CRM
 * $id: fillvisitreport.php	
 * Created: 	@zaher.reda 	June 26, 2009 | 11:21 AM
 * Last Update: @zaher.reda 	July 11, 2012 | 11:32 AM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['crm_canFillVisitReports'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!isset($core->input['identifier'])) {
	$identifier = substr(md5(uniqid(microtime())), 1,10);//base64_encode($core->user['uid'].'_'.$timenow);
}
else
{
	$identifier = $core->input['identifier'];
}
$session->name_phpsession(COOKIE_PREFIX.'fillvisitreport'.$identifier);
$session->start_phpsession();

$lang->load('crm_visitreport');
if(!$core->input['action']) {	
	/* Check if there is data in Sessions - START */
	if(isset($core->input['identifier']) && !empty($core->input['identifier'])) {
		$identifier = $db->escape_string($core->input['identifier']);
		if($core->input['stage'] == 'visitdetails') {
			if($session->isset_phpsession('visitreportvisitdetailsdata_'.$identifier)) {
				$visitdetails = unserialize($session->get_phpsession("visitreportvisitdetailsdata_{$identifier}"));
				$visitreport_data = unserialize($session->get_phpsession("visitreportdata_{$identifier}"));
				if(is_array($visitreport_data['spid'])) {
					foreach($visitreport_data['spid'] as $key => $val) {
						if(empty($val) && $val != 0 || (count($visitreport_data['spid']) > 1 && $val == 0)) {
							unset($visitreport_data['spid'][$key]);
							continue;	
						}
						$visitdetails['comments'][$val]['suppliername'] = $db->fetch_field($db->query("SELECT companyName FROM ".Tprefix."entities WHERE eid='".$db->escape_string($val)."'"), 'companyName');
						
						if(empty($visitdetails['comments'][$val]['suppliername'])) {
							$visitdetails['comments'][$val]['suppliername'] = 'Unspecified';
						}
						
						if(is_array($visitdetails['comments'])) {
							foreach($visitdetails['comments'][$val] as $k => $v) {
								$visitdetails['comments'][$val][$k] = $core->sanitize_inputs($v, array('method'=> 'striponly', 'removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
							}
						}
						eval("\$visitdetails_fields .= \"".$template->get('crm_fillvisitreport_visitdetailspage_fields')."\";");
					}
				}
			}
			else
			{
				if(is_empty($core->input['cid'], $core->input['rpid'], $core->input['productLine'])) {
					error($lang->fillallrequiredfields, 'index.php?module=crm/fillvisitreport&identifier='.$identifier);
				}	

				if(!isset($core->input['spid'])) {
					error($lang->fillallrequiredfields, 'index.php?module=crm/fillvisitreport&identifier='.$identifier);
				}
				
				if(is_array($core->input['spid'])) {
					$marktask_date_output = date('F d, Y', strtotime('+5 weekdays'));
					$marktask_date = date('d-m-Y', strtotime('+5 weekdays'));

					foreach($core->input['spid'] as $key => $val) {
						if(empty($val) && $val != 0 || (count($core->input['spid']) > 1 && $val == 0)) {
							unset($core->input['spid'][$key]);
						}
						else
						{
							$visitdetails['comments'][$val]['suppliername'] = $db->fetch_field($db->query("SELECT companyName FROM ".Tprefix."entities WHERE eid='".$db->escape_string($val)."'"), "companyName");
							
							if(empty($visitdetails['comments'][$val]['suppliername'])) {
								$visitdetails['comments'][$val]['suppliername'] = 'Unspecified';
							}
							
							if(is_array($visitdetails['comments'])) {
								foreach($visitdetails['comments'][$val] as $k => $v) {
									$visitdetails['comments'][$val][$k] = $core->sanitize_inputs($v, array('method'=> 'striponly', 'removetags' => true, 'allowed_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
								}
							}
							eval("\$visitdetails_fields .= \"".$template->get('crm_fillvisitreport_visitdetailspage_fields')."\";");
						}
					}
				}
			
				$session->set_phpsession(array('visitreportdata_'.$identifier => serialize($core->input)));
			}
			
			eval("\$fillreportpage = \"".$template->get('crm_fillvisitreport_visitdetailspage')."\";");	
		}
		else
		{ 
			if($session->isset_phpsession('visitreportdata_'.$identifier)) {
				$visitreport_values = unserialize($session->get_phpsession('visitreportdata_'.$identifier));
			}
			else
			{
				$visitreport_values = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."visitreports WHERE identifier='".$identifier."'"));
				if(!isset($visitreport_values['spid'])) {
					$visitreport_values['spid'][0] = 0;
				}
			}
			
			if(is_array($visitreport_values) && !empty($visitreport_values)) {
				foreach($visitreport_values as $key => $val) {	
					switch($key) {
						case 'cid':
							$company_name = $db->fetch_field($db->query("SELECT companyName FROM ".Tprefix."entities WHERE eid='".$db->escape_string($val)."'"), "companyName");
							$visitreport_values['customername'] = $company_name;
							break;
						case 'spid':
							$supplierrownumber = 1;
							foreach($val as $k => $v) {
								$company_name = $db->fetch_field($db->query("SELECT companyName FROM ".Tprefix."entities WHERE eid='".$db->escape_string($v)."'"), "companyName");
								$visitreport_values['suppliername'][$k] = $company_name;
								$visitreport_values['spid'][$k]= $v;
								
								eval("\$suppliers_fields .= \"".$template->get('crm_fillvisitreport_supplierfield')."\";");	
								$supplierrownumber++;
							}
							break;
						case 'rpid':
							$visitreport_values['representativename'] = $db->fetch_field($db->query("SELECT name FROM ".Tprefix."representatives WHERE rpid='".$db->escape_string($val)."'"), "name");
							break;
						case 'type':
						case 'purpose':
						case 'availabilityIssues':
						case 'supplyStatus':
							$variable_name = "{$key}_selected";	
							${$variable_name}[$val] = " selected='selected'";
							break;
						case 'date':
							if(empty($val)) { 
								$val = TIME_NOW;
							}
							
							if((string)intval($val) != $val) {
								$val = strtotime($val);
							}
							
							$visitreport_values['date_formatted'] = date('d-m-Y', $val);
							$visitreport_values['date_output'] = date($core->settings['dateformat'], $val);
							break;
					}
				}
				$productLine_selected = $visitreport_values['productLine'];
			}
			$visitreport_values['competition'] =  unserialize($session->get_phpsession("visitreportdata_{$identifier}_competition"));
			
			/*$supplyStatus_selected[$visitreport_values['supplyStatus']] = " selected='selected'";
			$availabilityIssues_selected[$visitreport_values['availabilityIssues']] = " selected='selected'";
			$purpose_selected[$visitreport_values['purpose']] = " selected='selected'";
			$type_selected[$visitreport_values['type']] = " selected='selected'";*/	
		}
	}
	else
	{
		$timenow = TIME_NOW;
		$supplierrownumber = 1;
		
		$k = 1;
		$visitreport_values['spid'][$k] = 0;
		
		eval("\$suppliers_fields = \"".$template->get('crm_fillvisitreport_supplierfield')."\";");	
	}
	
	if($core->input['stage'] == 'visitdetails') {
		
	}
	elseif($core->input['stage'] == 'competition') {
		if(strpos(strtolower($_SERVER['HTTP_REFERER']), 'stage=visitdetails') !== false) {
			$session->set_phpsession(array('visitreportvisitdetailsdata_'.$identifier => serialize($core->input)));
		}
	
		$visitreport_data = unserialize($session->get_phpsession('visitreportdata_'.$identifier));
		$competition = unserialize($session->get_phpsession('visitreportcompetitiondata_'.$identifier));

		if(is_array($visitreport_data['spid'])) {
			foreach($visitreport_data['spid'] as $key => $val) {
				if(empty($val) && $val != 0 || (count($visitreport_data['spid']) > 1 && $val == 0)) {
					unset($visitreport_data['spid'][$key]);
					continue;	
				}
				$competition['comments'][$val]['suppliername'] = $db->fetch_field($db->query("SELECT companyName FROM ".Tprefix."entities WHERE eid='".$db->escape_string($val)."'"), "companyName");
				if(empty($competition['comments'][$val]['suppliername'])) {
					$competition['comments'][$val]['suppliername'] = 'Unspecified';
				}
				
				foreach($competition['comments'][$val] as $k => $v) {
					$competition['comments'][$val][$k] = $core->sanitize_inputs($v, array('method'=> 'striponly', 'removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
				}
				
				eval("\$competition_fields .= \"".$template->get('crm_visitreport_competitionpage_fields')."\";");	
			}
		}
		
		eval("\$fillreportpage = \"".$template->get('crm_fillvisitreport_competitionpage')."\";");
	}
	else
	{
		$affiliates_attributes = array('affid', 'name');
		$affiliates_order = array(
			'by' => 'name', 
			'sort' => 'ASC'
		);
		if($core->usergroup['canViewAllAff'] == 0) { 
			$inaffiliates = implode(',', $core->user['affiliates']);
			$affiliate_where = 'affid IN ('.$inaffiliates.')';
		}
		$affiliates = get_specificdata('affiliates', $affiliates_attributes, 'affid', 'name', $affiliates_order, 0, $affiliate_where);
		$affiliates_list = parse_selectlist("affid", 2, $affiliates, $visitreport_values['affid']);	
		
		$productline_attributes = array('psid', 'title');
		$productline_order = array(
			'by' => 'title', 
			'sort' => 'ASC'
		);
			
		$productlines_query = $db->query("SELECT ps.psid, title FROM ".Tprefix."productsegments ps JOIN ".Tprefix."employeessegments es ON (es.psid=ps.psid) WHERE es.uid={$core->user[uid]}");
		while($productline = $db->fetch_assoc($productlines_query)) {
			$productlines[$productline['psid']] = $productline['title'];
		}
		$productline_list = parse_selectlist('productLine[]', 3, $productlines, $productLine_selected, 1);

		//$headerinc .= "<link href='{$core->settings[rootdir]}/css/jqueryuitheme/jquery-ui-1.7.2.custom.css' rel='stylesheet' type='text/css' />";
		eval("\$fillreportpage = \"".$template->get('crm_fillvisitreport')."\";");
	}

	output_page($fillreportpage);
}
else
{
	if($core->input['action'] == 'do_add_fillvisitreport') {
		$visitreport = unserialize($session->get_phpsession('visitreportdata_'.$db->escape_string($core->input['identifier'])));
		//$competition = unserialize($session->get_phpsession('visitreportdata_'.$db->escape_string($core->input['identifier']).'_competition'));
		$visitdetails = unserialize($session->get_phpsession('visitreportvisitdetailsdata_'.$db->escape_string($core->input['identifier'])));
		$competition = unserialize($session->get_phpsession('visitreportcompetitiondata_'.$db->escape_string($core->input['identifier'])));

		if(is_empty($visitreport['cid'], $visitreport['spid'], $visitreport['rpid'], $visitreport['productLine'])) {
			output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
			exit;
		}
		
		if(is_array($visitdetails['comments'])) {
			foreach($visitdetails['comments'] as $key => $val) {
				$visitdetails['comments'][$key] = array_merge($visitdetails['comments'][$key], $competition['comments'][$key]);
			}
		}

		$fields_array = $db->show_fields_from('visitreports');	
		foreach($fields_array as $field) {
			if(isset($visitreport[$field['Field']])) {
				$visitreport_main[$field['Field']] = $visitreport[$field['Field']];
			}
		}
		
		$visitreport_main['uid'] =  $core->user['uid'];

		if(!empty($visitreport['date'])){
			$visitreportdate = explode('-', $visitreport['date']);
			$visitreport_main['date'] =  mktime(0,0,0, $visitreportdate[1], $visitreportdate[0], $visitreportdate[2]);
		}
		else
		{
			$visitreport_main['date']  = TIME_NOW;
		}
		
		$visitreport_main['isLocked'] = 1;
		$visitreport_main['isDraft'] = 0;
		$visitreport_main['finishDate'] = TIME_NOW;
		
		if(count($visitreport['spid']) <= 1)  {
			if($visitreport['spid'][0] == 0) {
				$visitreport_main['hasSupplier'] = 0;
			}
		}
		
		$existing_report = $db->fetch_assoc($db->query('SELECT vrid, identifier FROM '.Tprefix.'visitreports WHERE identifier="'.$db->escape_string($visitreport['identifier']).'"'));
		if(!empty($existing_report)) {
			$is_new = false;
			$query = $db->update_query('visitreports', $visitreport_main, 'vrid='.$existing_report['vrid']);	
		}
		else
		{
			$is_new = true;
			$query = $db->insert_query('visitreports', $visitreport_main);
		}
		
		if($query) {
			if($is_new == false) {
				$vrid = $existing_report['vrid'];
			}
			else
			{
				$vrid = $db->last_id();
			}
			
			if(is_array($visitreport['productLine'])) {
				if($is_new == false) {
					$db->delete_query('visitreports_productlines', 'vrid='.$vrid);
				}
				foreach($visitreport['productLine'] as $key => $val) {
					$db->insert_query('visitreports_productlines', array('vrid' => $vrid, 'productLine' => $val));
				}
			}
			
			if(is_array($visitreport['spid'])) {
				$comments_fields_array = $db->show_fields_from('visitreports_comments');
				if($is_new == false) {
					$db->delete_query('visitreports_reportsuppliers', 'vrid='.$vrid);
					$db->delete_query('visitreports_comments', 'vrid='.$vrid);
				}
				
				foreach($visitreport['spid'] as $key => $val) {
					$visitreport_supplier['spid'] = $val;
					$visitreport_supplier['vrid'] = $vrid;
					if(!empty($visitreport['sprid'])){
						$visitreport_supplier['vrid'] = $visitreport['sprid'];
					}
					$db->insert_query('visitreports_reportsuppliers', $visitreport_supplier);

					if($visitdetails['comments'][$val]['markTask'] == 1) {
						$new_task['markTask'] = $visitdetails['comments'][$val]['markTask'];
						$new_task['dueDate'] = $visitdetails['comments'][$val]['taskDate'];
						$new_task['suppliername'] = $visitdetails['comments'][$val]['suppliername'];
					}

					foreach($comments_fields_array as $field) {
						if(isset($visitdetails['comments'][$val][$field['Field']])) {
							$visitreport_comments[$field['Field']] = $visitdetails['comments'][$val][$field['Field']];
							$visitreport_comments[$field['Field']] = $core->sanitize_inputs($visitreport_comments[$field['Field']], array('method'=> 'striponly', 'removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
						}
					}
					
					if(is_array($visitreport_comments)) {
						$visitreport_comments['vrid'] = $vrid;
						$visitreport_comments['spid'] = $val; 
						$db->insert_query('visitreports_comments', $visitreport_comments);
						
						/* Create follow up task - START */ 
						if($new_task['markTask'] == 1 && !empty($visitreport_comments['followUp'])) {
							$customer_name = $db->fetch_field($db->query("SELECT companyName FROM ".Tprefix."entities WHERE eid='".$visitreport['cid']."'"), 'companyName');
		
							$new_task = array(
								'uid'		    => $core->user['uid'],
								'subject'		=> $lang->visitfollowup.': '.$customer_name.'/'.$new_task['suppliername'],
								'priority'	   => 1,
								'dueDate'		=> $new_task['dueDate'],
								'description'	=> $visitreport_comments['followUp'],
								'reminderInterval' => 604800,//Every 2 days
								'reminderStart' => $new_task['dueDate'],
								'createdBy'	  => $core->user['uid']
							);

							$task = new Tasks();
							$task->create_task($new_task);
						}
						/* Create follow up task - END */ 
					}
				}
			}
			$log->record($vrid);
			
			$session->destroy_phpsession();
			
			output_xml("<status>true</status><message>{$lang->visitreportfinalized}</message>");
		}
	}
	elseif($core->input['action'] == 'get_addnew_representative' || $core->input['action'] == 'get_addnew_supprepresentative') {
		if($core->input['action'] == 'get_addnew_supprepresentative') {
			eval("\$entity_field_row = \"".$template->get('popup_addrepresentative_supplierfield')."\";");
		}
		else
		{
			eval("\$entity_field_row = \"".$template->get('popup_addrepresentative_customerfield')."\";");
		}
		eval("\$addrepresentativebox = \"".$template->get('popup_addrepresentative')."\";");
		echo $addrepresentativebox;
	}
	elseif($core->input['action'] == 'do_add_representative') {
		$representative = new Entities($core->input, 'add_representative');
		
		if($representative->get_status() === true) {
			output_xml("<status>true</status><message>{$lang->representativecreated}</message>");
		}
		else
		{
			output_xml("<status>false</status><message>{$lang->errorcreatingreprentative}</message>");
		}	
	}
	elseif($core->input['action'] == 'autosave') {
		//print_r($core->input);
		//$comments_fields_array = $db->show_fields_from('visitreports_comments');
		
/*		foreach($comments_fields_array as $field) {
			if(isset($core->input['comments'][$val][$field['Field']])) {
				$visitreport_comments[$field['Field']] = $visitdetails['comments'][$val][$field['Field']];
				$visitreport_comments[$field['Field']] = $core->sanitize_inputs($visitreport_comments[$field['Field']], array('method'=> 'striponly', 'removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
			}
		}*/
	}
}
?>