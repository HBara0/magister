<?php
/*
* Orkila Central Online System (OCOS)
* Copyright © 2009 Orkila International Offshore, All Rights Reserved
* 
* List of Holidays
* $module: hr
* $id: holidayslist.php	
* Created:			@najwa.kassem		October 28, 2010 | 13:30 AM
* Last Update:		@zaher.reda		  	May 20, 2012 | 10:29 AM
*/
if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
} 

if(!$core->input['action']) {	
	$sort_query = 'name ASC';
	$sort_url = sort_url();
	$limit_start = 0;
	
	if(!isset($core->input['affid']) || empty($core->input['affid'])) {
		$affid = $core->user['mainaffiliate'];
		if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
			if(is_array($core->user['hraffids']) && !empty($core->user['hraffids'])) {
				
					if(!in_array($core->user['mainaffiliate'], $core->user['hraffids'])) {
						$affid = $core->user['hraffids'][current($core->user['hraffids'])];
					}
			}
			else
			{
				error($lang->sectionnopermission);
				exit;	
			}
		}
	}
	else
	{
		$affid = $core->input['affid'];
		if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
			//if($core->input['affid'] != $core->user['mainaffiliate']) {
			if(!in_array($core->input['affid'], $core->user['hraffids'])) {
				$affid = $core->user['mainaffiliate'];
				if(!in_array($core->user['mainaffiliate'], $core->user['hraffids'])) {
					error($lang->sectionnopermission);
					exit;	
				}	
			}
		}
	}
	
	if(isset($core->input['sortby'], $core->input['order'])) {
		$sort_query = $db->escape_string($core->input['sortby']).' '.$db->escape_string($core->input['order']);
	}
	
	if(isset($core->input['start'])) {
		$limit_start = $db->escape_string($core->input['start']);
	}
	
	if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
		$core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
	}
	
	$multipage_where = 'affid='.$affid;
	
	if(isset($core->input['filtervalue'])) {
		$filter_where = 'AND name LIKE "%'.$db->escape_string($core->input['filtervalue']).'%"';
		$multipage_where .= ' AND name LIKE "%'.$db->escape_string($core->input['filtervalue']).'%"';
	}
	
	$query = $db->query("SELECT * FROM ".Tprefix."holidays
						WHERE affid={$affid}
						{$filter_where}
						ORDER BY {$sort_query}
						LIMIT {$limit_start}, {$core->settings[itemsperlist]}");
						
	if($db->num_rows($query) > 0) {
		while($holiday = $db->fetch_assoc($query)) {
			if($holiday['isOnce'] == 0) {
				$year = '<img src="./images/icons/update.png" border="0" alt="{$lang->recurring}" />'; 
			}
			else
			{
				$year = $holiday['year'];
			}
			$holidays_list .= "<tr><td align='center'>{$holiday[title]}</td><td align='center'>".$lang->{strtolower(date("F", mktime(0,0,0, $holiday['month'], 1, 0)))}."</td><td align='center'>{$holiday[day]}</td><td align='center'>{$holiday[numDays]}</td><td align='center'>{$year}</td><td><a href='index.php?module=hr/manageholidays&amp;id={$holiday[hid]}'><img src='./images/icons/edit.gif' border='0' alt=''/></a></td></tr>"; 
		}
		
		$multipages = new Multipages('holidays', $core->settings['itemsperlist'], $multipage_where);
		$holidays_list .= '<tr><td colspan="6">'.$multipages->parse_multipages().'</td></tr>'; 
	}
	else
	{
		$holidays_list = '<tr><td colspan="6">'.$lang->nomatchfound.'</td></tr>';
	}
	
	if($core->usergroup['hr_canHrAllAffiliates'] == 1) {
		$affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'));
	}
	else
	{
		if(is_array($core->user['hraffids']) && !empty($core->user['hraffids']) && count($core->user['hraffids']) > 1) {
			$affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 0, 'affid IN ('.implode(',', $core->user['hraffids']).')');
		}
	}
	
	if(is_array($affiliates)) {
		$affid_field = $lang->affiliate.': '.parse_selectlist('affid', 1, $affiliates, $affid, 0, 'goToURL("index.php?module=hr/holidayslist&amp;affid="+$(this).val())').'';
	}
	eval("\$list = \"".$template->get("hr_holidayslist")."\";");
	output_page($list);
}
else
{
	if($core->input['action'] == 'sendholidays') {
		if(!isset($core->input['affidtoinform'])) {
			output_xml("<status>false</status><message>{$lang->unknownaffiliate}</message>");
			exit;
		}
		
		$affiliate = $db->fetch_assoc($db->query("SELECT affid, mailingList FROM affiliates WHERE affid='".$db->escape_string($core->input['affidtoinform'])."'"));
		
		if(empty($affiliate['mailingList'])) {
			output_xml("<status>false</status><message>{$lang->errorsendingemail}</message>");
			exit;
		}
		$message = array();
		$message['recepient'] = $affiliate['mailingList'];
		
		$current_year = date('Y', TIME_NOW);
		
		$message['body'] = '<ul>';
		
		$query = $db->query("SELECT * FROM holidays WHERE affid='{$affiliate[affid]}' AND (year={$current_year} OR isOnce=0) ORDER BY month ASC, day ASC");
		if($db->num_rows($query) > 0) {
			while($holiday = $db->fetch_assoc($query)) {
				$message['body'] .= '<li>'.date('l, F j', mktime(0,0,0, $holiday['month'], $holiday['day'], $current_year)).' - '.$holiday['title'].', '.$holiday['numDays'].' day(s).</li>';
			}
			
			$message['body'] .= '</ul><br />';
			$lang->load('messages');
			
			$email_data = array(
				'to'		 => $message['recepient'],
				'from_email'  => $core->settings['adminemail'],
				'from'	   => 'OCOS Mailer',
				'subject'	=> $lang->sprint($lang->holidaysmessagesubject, $current_year),
				'message'   => $lang->sprint($lang->holidaysmessagebody, $current_year, $message['body'])
				);
	
			$mail = new Mailer($email_data, 'php');
			
			if($mail) {
				$log->record($affiliate['affid']);
				output_xml("<status>true</status><message>{$lang->holidayssent}</message>");
			}
			else
			{
				output_xml("<status>false</status><message>{$lang->errorsendingemail}</message>");
				exit;
			}
		}
		else
		{
			output_xml("<status>false</status><message>{$lang->noholidaysavailable}</message>");
		}
	}
}
?>