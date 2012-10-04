<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Lists available reports
 * $module: reporting
 * $id: listmreports.php
 * Created: 	@zaher.reda 	January 22, 2009 | 11:51 AM
 * Last Update: @zaher.reda 	January 22, 2009 | 11:51 AM
 */
 
if(!defined('DIRECT_ACCESS'))
{
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canGenerateReports'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

$lang->load('reporting_monthlyreport');
if(!$core->input['action']) {	
	$sort_query = 'month DESC, year DESC';
	if(isset($core->input['sortby'], $core->input['order'])) {
		$sort_query = $core->input['sortby'].' '.$core->input['order'];
	}
	$sort_url = sort_url();
	
	$limit_start = 0;
	if(isset($core->input['start'])) {
		$limit_start = $db->escape_string($core->input['start']);
	}
	
	$additional_where = getquery_entities_viewpermissions();
	
	if(isset($core->input['filterby'], $core->input['filtervalue'])) {
		$additional_where['multipage'] = $db->escape_string($core->input['filterby']).'='.$db->escape_string($core->input['filtervalue']);
		$filter_where = ' AND '.$db->escape_string($core->input['filterby']).'='.$db->escape_string($core->input['filtervalue']);
		$query_and = ' AND ';
	}

	/*if($core->usergroup['canViewAllAff'] == 0) {
		$inaffiliates = implode(',', $core->user['affiliates']);
		$extra_where = ' AND a.affid IN ('.$inaffiliates.') ';
		$multipage_where = 'affid IN ('.$inaffiliates.')';
		$query_and = ' AND ';
	}
	
	if($core->usergroup['canViewAllSupp'] == 0) {
		$insuppliers = implode(',', $core->user['suppliers']);
		$extra_where .= '  AND r.spid IN ('.$insuppliers.') ';	  
		$multipage_where .= $query_and.'spid IN ('.$insuppliers.')';
		$query_and = ' AND ';
	}*/
	
	if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
		$core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
	}
		
	$query = $db->query("SELECT r.*, a.affid, a.name AS affiliatename, r.spid, s.companyName AS suppliername
						 FROM ".Tprefix."reports r JOIN affiliates a ON (r.affid=a.affid) JOIN entities s ON (r.spid=s.eid)
						 WHERE  r.type='m'{$filter_where}{$additional_where[extra]}
						 ORDER BY {$sort_query}
						 LIMIT {$limit_start}, {$core->settings[itemsperlist]}");

	if($db->num_rows($query) > 0) {
		while($report = $db->fetch_array($query)) {
			$report['month'] = $lang->{strtolower(date("F", mktime(0,0,0, $report['month'], 1, 0)))};
			
			$icon_locked = '';			
			if($report['isLocked'] == 1) { 
				$icon_locked = '_locked';
				$icon[$report['rid']]['edit'] = "<img src='images/icons/lock.png' alt='{$lang->locked}' />";
			}
			else
			{
				$icon[$report['rid']]['edit'] = "<a href='index.php?module=reporting/fillmreport&tool=edit&amp;rid={$report[rid]}'><img src='images/icons/edit.gif' alt='{$lang->edit}' border='0'/></a>";
			}
			
			$icon[$report['rid']]['view'] = "<a href='index.php?module=reporting/previewmreport&referrer=list&amp;id={$report[rid]}'><img src='images/icons/report{$icon_locked}.gif' alt='{$report[status]}' border='0'/></a>";
						
			if($core->usergroup['canLockUnlockReports'] == 1 || $core->usergroup['reporting_canApproveReports'] == 1) {	
				$checkbox[$report['rid']] = "<input type='checkbox' id='checkbox_{$report[rid]}' name='listCheckbox[]' value='{$report[rid]}'/>";
			}
			
			$rowclass = '';
			if($report['isApproved'] == 0) {
				$rowclass = 'unapproved';
			}
			if($report['isSent'] == 1) {
				$rowclass = 'greenbackground';
				if($report['isApproved'] == 0) {
					$rowclass = 'yellowbackground';
				}
			}
			eval("\$reportslist .= \"".$template->get('reporting_mreportslist_reportrow')."\";");
		}
		$multipage_where .= $query_and.'type="m"';
		$multipages = new Multipages('reports', $core->settings['itemsperlist'], $multipage_where);
			
		/*if($core->usergroup['canReadStats'] == 1) {
			$stats_link = "<a href='index.php?module=reporting/stats'><img src='images/icons/stats.gif' alt='{$lang->reportsstats}' border='0'></a>";
		}*/
	
		$reportslist .= "<tr><td colspan='5'>".$multipages->parse_multipages()."&nbsp;</td><td style='text-align: right;' colspan='2'><input type='image' src='images/icons/merge.gif' alt='{$lang->mergereports}' />&nbsp;<a href='".$_SERVER['REQUEST_URI']."&amp;action=exportexcel'><img src='images/icons/xls.gif' alt='{$lang->exportexcel}' border='0' /></a>&nbsp;{$stats_link}</td></tr>";
		if($core->usergroup['canLockUnlockReports'] == 1 || $core->usergroup['reporting_canApproveReports'] == 1) {	
			$moderationtools = "<tr><td colspan='3'>";
			$moderationtools .= "<div id='moderation_reporting/listmreports_Results'></div>&nbsp;";
			
			$moderationtools .= "</td><td style='text-align: right;' colspan='4'><strong>{$lang->moderatintools}:</strong> <select name='moderationtool' id='moderationtools'>";
			$moderationtools .= "<option value='' selected>&nbsp;</option>";
			if($core->usergroup['canLockUnlockReports'] == 1) {
				$moderationtools .= "<option value='lock'>{$lang->lock}</option>";
				$moderationtools .= "<option value='unlock'>{$lang->unlock}</option>";
				$moderationtools .= "<option value='lockunlock'>{$lang->lockunlock}</option>";
			}
			/*if($core->usergroup['reporting_canApproveReports'] == 1) {
				$moderationtools .= "<option value='approve'>{$lang->approve}</option>";
			}*/
			
			$moderationtools .= "</select></td></tr>";
		}
	}
	else
	{
		$reportslist = "<tr><td colspan='6' align='center'>{$lang->noreportsavailable}</td></tr>";
	}
	
	eval("\$listpage = \"".$template->get('reporting_mreportslist')."\";");
	output_page($listpage);
}
else
{
	if($core->input['action'] == 'do_moderation') {
		if($core->input['moderationtool'] == 'lock' || $core->input['moderationtool'] == 'unlock' || $core->input['moderationtool'] == 'lockunlock') {
			if($core->usergroup['canLockUnlockReports'] == 1) {	
				if(count($core->input['listCheckbox']) > 0) {
					if($core->input['moderationtool'] == 'lock') { $new_status = 1; }
					if($core->input['moderationtool'] == 'unlock') { $new_status = 0; }
						
					foreach($core->input['listCheckbox'] as $key => $val) {
						$rid = $db->escape_string($val);

						if($core->input['moderationtool'] == 'lockunlock') {
							list($current_status) = get_specificdata('reports', array('isLocked'), '0', 'isLocked', '', 0, "rid='{$rid}'");
							if($current_status == 0) { $new_status = 1; } else { $new_status = 0; }
						}
						$db->update_query('reports', array('isLocked' => $new_status), "rid='{$rid}'");
					}
					output_xml("<status>true</status><message>{$lang->lockchanged}</message>");
					log_action($core->input['listCheckbox'], $core->input['moderationtool']); 
				}
				else
				{
					output_xml("<status>false</status><message>{$lang->selectatleastonereport}</message>"); 
				}
			}
		}
		/*elseif($core->input['moderationtool'] == 'approve') {
			if($core->usergroup['reporting_canApproveReports'] == 1) {	
				if(count($core->input['listCheckbox']) > 0) {
					foreach($core->input['listCheckbox'] as $key => $val) {
						$rid = $db->escape_string($val);
						list($current_status) = get_specificdata('reports', array('isApproved'), '0', 'isApproved', '', 0, "rid='{$rid}'");
		
						if($current_status == 0) { $new_status = 1; } else { $new_status = 0; }
						$db->update_query('reports', array('isApproved' => $new_status), "rid='{$rid}'");
					}
					output_xml("<status>true</status><message>{$lang->reportsapproved}</message>"); 
					log_action($core->input['listCheckbox'], $core->input['moderationtool']);
				}
				else
				{
					output_xml("<status>false</status><message>{$lang->selectatleastonereport}</message>"); 
				}
			}
		}*/
	}
	elseif($core->input['action'] == 'exportexcel') {
		$sort_query = 'month, year DESC';
		if(isset($core->input['sortby'], $core->input['order'])) {
			$sort_query = $core->input['sortby'].' '.$core->input['order'];
		}
	
		/*if($core->usergroup['canViewAllAff'] == 0) {
			$inaffiliates = implode(',', $core->user['affiliates']);
			$extra_where = ' AND a.affid IN ('.$inaffiliates.') ';
			$query_and = ' AND ';
		}
	
		if($core->usergroup['canViewAllSupp'] == 0) {
			$insuppliers = implode(',', $core->user['suppliers']);
			$extra_where .= '  AND r.spid IN ('.$insuppliers.') ';	  
		}*/
		$additional_where = getquery_entities_viewpermissions();
		$query = $db->query("SELECT a.name AS affiliatename, s.companyName AS suppliername, r.month, r.year, r.status
						 FROM ".Tprefix."reports r JOIN affiliates a ON (r.affid=a.affid) JOIN entities s ON (r.spid=s.eid)
						 WHERE r.type='m'{$additional_where[extra]}
						 ORDER BY {$sort_query}");
		if($db->num_rows($query) > 0) {
			$reports[0]['affiliatename'] = $lang->affiliate;
			$reports[0]['suppliername'] = $lang->supplier;
			$reports[0]['month'] = $lang->month;
			$reports[0]['year'] = $lang->year;
			$reports[0]['status'] = $lang->status;
			
			$i= 1;
			while($reports[$i] = $db->fetch_assoc($query)) {
				$reports[$i]['status'] = parse_status($reports[$i]['status'], $reports[$i]['isLocked']);
				unset($reports[$i]['isLocked']);
				$i++;
			}
		
			$excelfile = new Excel('array', $reports);
		}
	}
}
function parse_status($status, $lock=0) {
	global $lang;
	
	if($status == 1) {
		$status_text = $lang->finalized;
	}
	else
	{
		$status_text = $lang->notfinished;
	}
	
	if($lock == 1) {
		$status_text .=  ' '.$lang->andlocked;
	}
	return $status_text;		
}
?>