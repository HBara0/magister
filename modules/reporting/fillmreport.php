<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Fill up a monthly report
 * $module: reporting
 * $id: fillmreport.php	
 * Created: 	@zaher.reda 	January 15, 2010 | 12:53 PM
 * Last Update: @zaher.reda 	April 08, 2010  | 12:39 PM
 */

if(!defined('DIRECT_ACCESS'))
{
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canFillReports'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

$session->start_phpsession();

$lang->load('reporting_monthlyreport');
if(!$core->input['action']) {
	if(isset($core->input['identifier']) && !empty($core->input['identifier'])) {
		$identifier = $db->escape_string($core->input['identifier']);
		$report_data = unserialize($session->get_phpsession('reportmeta_'.$identifier));
		if(isset($report_data['rid']) && !empty($report_data['rid'])) {
			$rid_field = '<input type="hidden" name="rid" id="rid" value="'.$report_data['rid'].'" />';
		}
	}
	elseif($core->input['tool'] == 'edit' && (isset($core->input['rid']) && !empty($core->input['rid'])))
	{
		$rid = $db->escape_string($core->input['rid']);
		if($db->fetch_field($db->query("SELECT COUNT(*) AS countreports FROM ".Tprefix."reports WHERE rid='{$rid}' AND type='m'"), 'countreports') == 0) {
			redirect('index.php?module=reporting/fillmreport');
		}
		
		$report_data = $db->fetch_assoc($db->query("SELECT r.month, r.year, r.spid, r.affid, aff.name AS affiliatename, e.companyName AS suppliername
													FROM  ".Tprefix."reports r, ".Tprefix."affiliates aff, ".Tprefix."entities e
													WHERE r.spid=e.eid AND r.affid=aff.affid AND r.rid='{$rid}'"));
													
		$report_data = array_merge($report_data, $db->fetch_assoc($db->query("SELECT accomplishments, actions, considerations FROM ".Tprefix."monthly_highlights WHERE rid='{$rid}'")));
	
		$query = $db->query("SELECT ps.gpid, ps.status, gp.title 
							FROM ".Tprefix."monthly_productsstatus ps, ".Tprefix."genericproducts gp
							WHERE ps.gpid=gp.gpid AND ps.rid='{$rid}'");
							
		//$report_data['overallstatus_numrows'] = 1;
		$overallstatus_numrows =0;
		//$report_data['overallstatus'] = array();
		while($productsstatus = $db->fetch_assoc($query)) {
			$overallstatus_numrows++;
			$report_data['overallstatus'][$overallstatus_numrows] = $productsstatus;
		}
		$report_data['overallstatus_numrows'] = $overallstatus_numrows;
		/*
			Now get key customers
		*/
		$query = $db->query("SELECT kc.cid, kc.status, kc.changes, kc.risksOpportunities, e.companyName AS customername
							FROM ".Tprefix."keycustomers kc, ".Tprefix."entities e
							WHERE kc.cid=e.eid AND rid='{$rid}' ORDER BY kc.rank ASC");
		$keycustomers_numrows = 0;
		while($keycustomers = $db->fetch_assoc($query)) {
			$keycustomers_numrows++;
			$report_data['keycustomers'][$keycustomers_numrows] = $keycustomers;	
		}
		$report_data['keycustomers_numrows'] = $keycustomers_numrows;
	
		$identifier = md5(uniqid(microtime()).$rid);
		$rid_field = '<input type="hidden" name="rid" id="rid" value="'.$rid.'" />';
	}
	else
	{
		$identifier = md5(uniqid(microtime()));
		$report_data = array();
		$report_data['year'] = date('Y', TIME_NOW);
		$report_data['month'] = date('n', TIME_NOW);
		$report_data['affid'] = $core->user['mainaffiliate'];
	}
	
	if($core->usergroup['canViewAllAff'] == 0) {
		$inaffiliates = implode(',', $core->user['affiliates']);
		$where = 'affid IN ('.$inaffiliates.') ';
	}
	/*
	if($core->usergroup['canViewAllSupp'] == 0) {
		$insuppliers = implode(',', $core->user['suppliers']);
		$extra_where .= ' AND spid IN ('.$insuppliers.') ';	  
	}*/
		
	$affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 0, $where);
	$affiliates_list = parse_selectlist('affid', 2, $affiliates, $report_data['affid']);
	
	for($i=1;$i<=12;$i++) {
		$months[$i] = $lang->{strtolower(date("F", mktime(0,0,0, $i, 1, 0)))};
	}
	
	$month_list = parse_selectlist('month', 3, $months, $report_data['month']);
	
	$generic_attributes = array('gpid', 'title');		
	$generic_order = array(
		'by' => 'title', 
		'sort' => 'ASC'
	);
	
	$keycustomersrownumber = $overallstatusrownumber  = 1;
	
	$generics = get_specificdata('genericproducts', $generic_attributes, 'gpid', 'title', $generic_order, 1);	
	if(isset($report_data['overallstatus_numrows'], $report_data['keycustomers_numrows'])) {
		if(!empty($report_data['overallstatus_numrows'])) {	
			$overallstatusrownumber = intval($report_data['overallstatus_numrows']); 
			
			for($rowid=1;$rowid<=$overallstatusrownumber;$rowid++) {
				if(empty($report_data['overallstatus'][$rowid]['gpid'])) {
					unset($report_data['overallstatus'][$rowid]);
					if($overallstatusrownumber > 1) {
						continue;
					}
				}
				$generics_list = parse_selectlist('overallstatus['.$rowid.'][gpid]', 10, $generics, $report_data['overallstatus'][$rowid]['gpid']);
				
				if(!empty($report_data['overallstatus'][$rowid]['csid'])) {
					$report_data['overallstatus'][$rowid]['chemsubstance'] = $db->fetch_field($db->query('SELECT name FROM '.Tprefix.'chemicalsubstances WHERE csid='.intval($report_data['overallstatus'][$rowid]['csid'])), 'name');
				}
				eval("\$overallstatus_fields .= \"".$template->get('reporting_fillmreport_overallstatusrow')."\";");
			}
		}
		if(!empty($report_data['keycustomers_numrows'])) {
			$keycustomersrownumber = intval($report_data['keycustomers_numrows']); 
			
			for($rowid=1;$rowid<=$keycustomersrownumber;$rowid++) {
				if(empty($report_data['keycustomers'][$rowid]['cid'])) {
					unset($report_data['keycustomers'][$rowid]);
					if($keycustomersrownumber > 1) {
						continue;
					}
				}
				eval("\$keycustomers_fields .= \"".$template->get('reporting_fillmreport_keycustomerrow')."\";");		
			}
		}
	}
	
	if(!isset($report_data['overallstatus_numrows'], $report_data['keycustomers_numrows']) || (empty($keycustomers_fields) || empty($overallstatus_fields))) {
		$rowid = 1;
		if(empty($overallstatus_fields)) {
			$generics_list = parse_selectlist('overallstatus['.$rowid.'][gpid]', 10, $generics, $report_data['overallstatus'][$rowid]['gpid']);
			eval("\$overallstatus_fields = \"".$template->get('reporting_fillmreport_overallstatusrow')."\";");	
		}
		if(empty($keycustomers_fields)) {
			eval("\$keycustomers_fields = \"".$template->get('reporting_fillmreport_keycustomerrow')."\";");	
		}
	}
	
	eval("\$fillmreportpage = \"".$template->get('reporting_fillmreport')."\";");	
	output_page($fillmreportpage);
}
else
{	
	if($core->input['action'] == 'process' || $core->input['action'] == 'do_perform_fillmreport')
	{
		if($core->input['referrer'] == 'fill') {
			$time_now = time();
			
			$identifier = $db->escape_string($core->input['identifier']);
			if($core->input['processtype'] == 'finalize') {
				$report_data = unserialize($session->get_phpsession('reportmeta_'.$identifier));
			}
			else
			{
				if($core->input['action'] == 'do_perform_fillmreport') {
					$report_data = $core->input;
				}
				else
				{
					exit;
				}
			}
			
			if(isset($report_data['rid']) && !empty($report_data['rid'])) {
				$rid = $db->escape_string($report_data['rid']);
				unset($report_data['rid']);
				$action_type = 'update';
			}
			else
			{
				$action_type = 'create';
			}
			
			$report_attr = array('month', 'year', 'spid', 'affid');
			$highlights_attr = array('actions', 'considerations', 'accomplishments');
			
			foreach($report_data as $key => $val) {
				if(in_array($key, $report_attr)) {
					if(empty($val)) {
						if($core->input['processtype'] == 'finalize') {
							error($lang->fillrequiredfields, 'index.php?module=reporting/fillmreport&identifier='.$report_data['identifier']);
						}
						else
						{
							output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
							exit;
						}
					}
					$report[$key] = $db->escape_string($val);
				}
				elseif(in_array($key, $highlights_attr)) {
					$highlights[$key] = $val;
				}	
			}
		
			$report['type'] = 'm';
			$report['isSent'] = 0;
			$report['initDate'] = $time_now;
			if($core->input['processtype'] == 'finalize') {
				$report['uidFinish'] = $core->user['uid'];
				$report['finishDate'] = $time_now;
				$report['status'] = 1;
				$report['isLocked'] = 1;
			}

			if($action_type == 'update') {
				$query = $db->update_query('reports', $report, "rid='{$rid}'");
			}
			else
			{
				$query = $db->insert_query('reports', $report);
			}
			//$query = true;
			if($query) {
				if($action_type == 'create') {
					$rid = $db->last_id();
				}
				
				if($action_type == 'update') {
					$db->update_query('monthly_highlights', $highlights, "rid='{$rid}'");
				}
				else
				{
					$highlights['rid'] = $rid;
					$db->insert_query('monthly_highlights', $highlights);
				}
				
				if($action_type == 'update') { 
					$db->delete_query('monthly_productsstatus', "rid='{$rid}'");
				}
	
				for($rowid=1;$rowid<=intval($report_data['overallstatus_numrows']);$rowid++) {
					if(empty($report_data['overallstatus'][$rowid]['gpid'])) {
						unset($report_data['overallstatus'][$rowid]);
						continue;
					}
					
					$report_data['overallstatus'][$rowid]['rid'] = $rid;
					$db->insert_query('monthly_productsstatus', $report_data['overallstatus'][$rowid]);
				}
				
				if($action_type == 'update') { 
					$db->delete_query('keycustomers', "rid='{$rid}'");
				}
				for($rowid=1;$rowid<=intval($report_data['keycustomers_numrows']);$rowid++) {
					if(empty($report_data['keycustomers'][$rowid]['cid'])) {
						unset($report_data['keycustomers'][$rowid]);
						continue;
					}
					
					unset($report_data['keycustomers'][$rowid]['customername']);
					
					$report_data['keycustomers'][$rowid]['rank'] = $rowid;
					$report_data['keycustomers'][$rowid]['rid'] = $rid;	
					$db->insert_query('keycustomers', $report_data['keycustomers'][$rowid]);		
				}
				
				if($core->input['processtype'] == 'finalize') {
					$session->destroy_phpsession();
					log_action($rid, $core->input['process_type']);
					record_contribution($rid);
					redirect('index.php?module=reporting/home', 2, $lang->reportfinalized);
				}
				else
				{
					record_contribution($rid);
					if($action_type == 'create') {
						$js = "<script type=\"text/javascript\">\n$(function() { $(\"form[id='perform_reporting/fillmreport_Form']\").prepend(\"<input type='hidden' value='{$rid}' name='rid' id='rid' />\");});</script>\n";
					}
					output_xml("<status>true</status><message>{$lang->successfullysaved}<![CDATA[{$js}]]></message>");
					log_action($rid, $core->input['process_type']);
				}
			}
			else
			{
				if($core->input['processtype'] == 'finalize') {
					error($lang->errorsaving);
				}
				else
				{
					output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
				}
			}
		}
	}
}
?>