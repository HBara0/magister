<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Preview and export monthly reports
 * $module: reporting
 * $id: previewmreport.php	
 * Last Update: @zaher.reda 	September, 2010 | 3:13 PM
 */

if(!defined('DIRECT_ACCESS'))
{
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canFillReports'] == 0 && $core->usergroup['canGenerateReports'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

$session->start_phpsession();

$lang->load('reporting_monthlyreport');
if(!$core->input['action']) {
	if($core->input['referrer'] != 'fill') {
		if(!isset($core->input['id']) || empty($core->input['id'])) {
			if(!isset($core->input['listCheckbox']) || count($core->input['listCheckbox']) == 0) {
				redirect('index.php?module=reporting/listmreports');
				exit;
			}
		}
	}
	
	if(isset($core->input['listCheckbox'])) {
		$core->input['id'] = $core->input['listCheckbox'];
	}
	
	if(!is_array($core->input['id'])) {
		$core->input['id']= array($core->input['id']);
	}
	if($core->input['referrer'] == 'generate' || $core->input['referrer'] == 'list') {
		$identifier = md5(uniqid(microtime()));
		$spid_cache = array();
		$is_mixed_report = false;
		
		end($core->input['id']);
		$last_element_index = key($core->input['id']); 
	}
	
	foreach($core->input['id'] as $key => $val) {		
		if($core->input['referrer'] == 'generate' || $core->input['referrer'] == 'list') {
			if(empty($val)) { continue; }
			//$rid = $db->escape_string($core->input['rid']);
			$rid = $db->escape_string($val);
			
			if($db->fetch_field($db->query("SELECT COUNT(*) AS countreports FROM ".Tprefix."reports WHERE rid='{$rid}' AND type='m'"), 'countreports') == 0) {
				redirect('index.php?module=reporting/fillmreport');
			}
			
			$report_data = $db->fetch_assoc($db->query("SELECT r.rid, r.month, r.year, r.spid, r.affid, aff.name AS affiliatename, e.companyName AS suppliername
														FROM ".Tprefix."reports r JOIN ".Tprefix."affiliates aff ON (r.affid=aff.affid) JOIN ".Tprefix."entities e ON (r.spid=e.eid)
														WHERE r.rid='{$rid}'"));
			
			$report_data['type'] = 'm';
			if($is_mixed_report == false) {
				if(is_array($spid_cache) && !empty($spid_cache)) {
					if(!in_array($report_data['spid'], $spid_cache)) {
						$is_mixed_report = true;
					}
				}
				$spid_cache[] = $report_data['spid'];
			}
			
			if($key == $last_element_index) {
				if($is_mixed_report == true) {
					$session->set_phpsession(array('reportsmetadata_'.$identifier => serialize(array('spid' => 0, 'type' => 'm', 'month' => $report_data['month'], 'year' => $report_data['year']))));
				}
				else
				{
					$session->set_phpsession(array('reportsmetadata_'.$identifier => serialize($report_data)));
				}
			}
			
			$report_highlights = $db->fetch_assoc($db->query("SELECT accomplishments, actions, considerations FROM ".Tprefix."monthly_highlights WHERE rid='{$rid}'"));			
			array_walk($report_highlights, 'chtmlspecialchars');
			array_walk($report_highlights, 'fix_newline');
			array_walk($report_highlights, 'parse_ocode');
			
			$report_data = array_merge($report_data, $report_highlights);
		
			$query = $db->query("SELECT ps.gpid, ps.status, gp.title 
								FROM ".Tprefix."monthly_productsstatus ps JOIN ".Tprefix."genericproducts gp ON (ps.gpid=gp.gpid)
								WHERE ps.rid='{$rid}'");
								
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
								FROM ".Tprefix."keycustomers kc JOIN ".Tprefix."entities e ON (kc.cid=e.eid)
								WHERE rid='{$rid}' ORDER BY kc.rank ASC");
			$keycustomers_numrows = 0;
			while($keycustomers = $db->fetch_assoc($query)) {
				$keycustomers_numrows++;
				$report_data['keycustomers'][$keycustomers_numrows] = $keycustomers;
			}
			$report_data['keycustomers_numrows'] = $keycustomers_numrows;
		}
		elseif($core->input['referrer'] == 'fill') 
		{
			if(!empty($val)) { continue; }
			
			$identifier = $db->escape_string($val);
			//$identifier = $db->escape_string($core->input['identifier']);
			$core->input['type'] = 'm';
			$session->set_phpsession(array('reportsmetadata_'.$core->input['identifier'] => serialize($core->input)));
			$report_data = $core->input;
			$highlights_attr = array('actions', 'considerations', 'accomplishments');
			foreach($highlights_attr as $key) {
				chtmlspecialchars($report_data[$key]);
				fix_newline($report_data[$key]);
				parse_ocode($report_data[$key]);
			}
				//array_walk($report_highlights, 'chtmlspecialchars');
				//array_walk($report_highlights, 'fix_newline');
				//array_walk($report_highlights, 'parse_ocode');
	
			if(is_empty($report_data['spid'], $report_data['affid'], $report_data['month'], $report_data['year'])) {
				redirect('index.php?module=reporting/fillmreport&amp;identifier='.$identifier);
			}
		}
		else
		{
			redirect('index.php?module=reporting/fillmreport');
		}	
		
		for($rowid=1;$rowid<=intval($report_data['overallstatus_numrows']);$rowid++) {
			if(empty($report_data['overallstatus'][$rowid]['gpid'])) {
				unset($report_data['overallstatus'][$rowid]);
				continue;
			}
			$toformat = array('status');
			foreach($report_data['overallstatus'][$rowid] as $key => $val) {
				if(in_array($key, $toformat)) {
					chtmlspecialchars($report_data['overallstatus'][$rowid][$key]);
					fix_newline($report_data['overallstatus'][$rowid][$key]);
					parse_ocode($report_data['overallstatus'][$rowid][$key]);
				}
			}
			$report_data['overallstatus'][$rowid]['generic'] = $db->fetch_field($db->query("SELECT title FROM ".Tprefix."genericproducts WHERE gpid='".$db->escape_string($report_data['overallstatus'][$rowid]['gpid'])."'"), 'title');
			eval("\$overallstatus_rows .= \"".$template->get('reporting_monthlyreport_overallstatusrow')."\";");	
		}
			
		for($rowid=1;$rowid<=intval($report_data['keycustomers_numrows']);$rowid++) {
			if(empty($report_data['keycustomers'][$rowid]['cid'])) {
				unset($report_data['keycustomers'][$rowid]);
				continue;
			}
			
			$toformat = array('status', 'changes', 'risksOpportunities');
			foreach($report_data['keycustomers'][$rowid] as $key => $val) {
				if(in_array($key, $toformat)) {
					chtmlspecialchars($report_data['keycustomers'][$rowid][$key]);
					fix_newline($report_data['keycustomers'][$rowid][$key]);
					parse_ocode($report_data['keycustomers'][$rowid][$key]);
				}
			}
	
			eval("\$keycustomers_rows .= \"".$template->get('reporting_monthlyreport_keycustomersrow')."\";");		
		}
		
		if(empty($keycustomers_rows)) {
			$keycustomers_rows = '<tr><td colspan="3" style="width:100%; text-align: center;">-</td></tr>';
		}
		
		/*
			PARSE VISITS
		*/
		if($db->num_rows($db->query("SHOW TABLES LIKE 'visitreports'")) > 0) { 
			$month_firstday = mktime(0, 0, 0, $report_data['month'], 1, $report_data['year']);
			$month_lastday = mktime(23, 59, 59, $report_data['month'], date('t', $month_firstday), $report_data['year']);
			
			$visits_query = $db->query("SELECT vr.*, rs.*, vrc.conclusions, vrc.followUp, displayName AS employeename, c.companyName AS customername, rep.name AS representativename
										FROM ".Tprefix."visitreports vr JOIN ".Tprefix."visitreports_reportsuppliers rs ON (vr.vrid=rs.vrid)
										JOIN ".Tprefix."visitreports_comments vrc ON (vrc.vrid=vr.vrid)
										JOIN ".Tprefix."users u ON (vr.uid=u.uid) 
										JOIN ".Tprefix."entities c ON (vr.cid=c.eid) 
										JOIN ".Tprefix."representatives rep ON (rep.rpid=vr.rpid)
										WHERE rs.spid='".$db->escape_string($report_data['spid'])."' AND vrc.spid='".$db->escape_string($report_data['spid'])."' AND vr.affid='".$db->escape_string($report_data['affid'])."' AND (vr.date BETWEEN {$month_firstday} AND {$month_lastday})");
			$visits_rows = $visits = '';
			if($db->num_rows($visits_query) > 0) {
				while($visitreport = $db->fetch_assoc($visits_query)) {
					parse_calltype($visitreport['type']);
					parse_callpurpose($visitreport['purpose']);
					
					$visitreport['date_output'] = date($core->settings['dateformat'], $visitreport['date']);
					eval("\$visits_rows .= \"".$template->get('reporting_monthlyreport_visitrow')."\";");
				}
				eval("\$visits = \"".$template->get('reporting_monthlyreport_visits')."\";");
			}
		}
		
		$report_data[affiliatename] = $db->fetch_field($db->query("SELECT name FROM ".Tprefix."affiliates WHERE affid='".$db->escape_string($report_data['affid'])."'"), 'name');
		$report_data['month'] = $lang->{strtolower(date("F", mktime(0,0,0, $report_data['month'], 1, 0)))};
	
		$query = $db->query("SELECT rc.*, u.firstName, u.lastName, u.email 
								FROM ".Tprefix."reportcontributors rc,".Tprefix."users u, ".Tprefix."reports r
								WHERE r.rid=rc.rid AND rc.uid=u.uid AND r.rid='$rid'
								ORDER BY u.firstName ASC");
								
		$lang->reportpreparedby_text = $lang->reportpreparedby;
		$lang->email_text = $lang->email;
		if($db->num_rows($query) > 0) { 
			$contributors = '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
			while($employee = $db->fetch_array($query)) {	
				eval("\$contributors .= \"".$template->get('reporting_preview_contributorrow')."\";");				
				$lang->reportpreparedby_text = $lang->email_text = '';
			}
			$contributors .= '</table>';
		}
			
			
		if($core->input['referrer'] != 'generate' && $core->input['referrer'] != 'list') {
			eval("\$tools .= \"".$template->get('reporting_mreportpreview_tools_finalize')."\";");
		}
		else
		{
			if($core->usergroup['reporting_canSendReportsEmail'] == 1) {
				$tools_send = "<a href='index.php?module=reporting/previewmreport&amp;action=saveandsend&amp;identifier={$identifier}'><img src='images/icons/send.gif' border='0' alt='{$lang->sendbyemail}' /></a> ";
			}
			
		/*	if($core->input['referrer'] == 'list' || $core->input['referrer'] == 'generate' || $core->input['referrer'] == 'direct') {
				if($report['isApproved'] == 0) {
					if($core->usergroup['reporting_canApproveReports'] == 1) {
						$tools_approve = "<script language='javascript' type='text/javascript'>$(function(){ $('#approvereport').click(function() { sharedFunctions.requestAjax('post', 'index.php?module=reporting/preview', 'action=approve&identifier={$session_identifier}', 'approvereport_span', 'approvereport_span');}) });</script>";
						$tools_approve .= "<span id='approvereport_span'><a href='#approvereport' id='approvereport'><img src='images/valid.gif' alt='{$lang->approve}' border='0' /></a></span> | ";
					}
				}
			}*/ 
		}
		eval("\$reports .= \"".$template->get('reporting_monthlyreport')."\";");
	}

	$tools = $tools.$tools_approve.$tools_send."<a href='index.php?module=reporting/previewmreport&amp;action=exportpdf&amp;identifier={$identifier}' target='_blank'><img src='images/icons/pdf.gif' border='0' alt='{$lang->downloadpdf}'/></a>&nbsp;<a href='index.php?module=reporting/previewmreport&amp;action=print&amp;identifier={$identifier}' target='_blank'><img src='images/icons/print.gif' border='0' alt='{$lang->printreport}'/></a>";

	$session->set_phpsession(array('monhtlyreports_'.$identifier => $reports));
	
	$reporttitle = $report_data['month'].' '.$report_data['year'].' - '.$report_data['suppliername'].' '.$lang->monthlyreport;
	eval("\$monthlyreportpage = \"".$template->get('reporting_mreportpreview')."\";");	
	output_page($monthlyreportpage);
}
else
{
	if($core->input['action'] == 'exportpdf' || $core->input['action'] == 'print' || $core->input['action'] == 'saveandsend' || $core->input['action'] == 'approve') {
		if($core->input['action'] == 'print') {
			$show_html = 1;
			$content = "<link href='{$core->settings[rootdir]}/report_printable.css' rel='stylesheet' type='text/css' />";
			$content .= "<script language='javascript' type='text/javascript'>window.print();</script>";
		}
		else
		{
			$content = "<link href='styles.css' rel='stylesheet' type='text/css' />";
			$content .= "<link href='report.css' rel='stylesheet' type='text/css' />";
		}
		$content .= $session->get_phpsession('monhtlyreports_'.$core->input['identifier']);
		
		//$identifier = explode('_', $core->input['identifier']);
		$meta_data = unserialize($session->get_phpsession('reportsmetadata_'.$core->input['identifier']));
		/*$suppliername = $db->fetch_field($db->query("SELECT e.companyName AS suppliername FROM ".Tprefix."entities e, ".Tprefix."reports r 
													WHERE r.spid=e.eid AND r.rid='".$db->escape_string($meta_data['spid'][0])."'"), 'suppliername');
													*/
		$suppliername = $meta_data['suppliername'];//$db->fetch_field($db->query("SELECT companyName AS suppliername FROM ".Tprefix."entities WHERE eid='".$db->escape_string($meta_data['spid'][0])."'"), 'suppliername');
			
		require_once ROOT.'/'.INC_ROOT.'html2pdf/html2pdf.class.php';
		$html2pdf = new HTML2PDF('P','A4', 'en');
		$html2pdf->pdf->SetDisplayMode('fullpage');
		$html2pdf->pdf->SetTitle($suppliername, true);
		$content = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $content);

		if($core->input['action'] == 'saveandsend') {
			$html2pdf->WriteHTML($content, $show_html);
			$html2pdf->Output($core->settings['exportdirectory'].'monthlyreports_'.$core->input['identifier'].'.pdf', 'F');
			redirect('index.php?module=reporting/sendbymail&amp;identifier='.$core->input['identifier']);
		}
		if($core->input['action'] == 'approve') {
			if($core->usergroup['reporting_canApproveReports'] == 1) {
				foreach($meta_data['rid'] as $key => $val) {
					$db->update_query('reports', array('isApproved' => 1), "rid='".$db->escape_string($val)."'");
				}
				output_xml("<status>true</status><message>{$lang->approved}</message>"); 
				log_action($meta_data['rid'], 'approve');
			}
		}
		else
		{
			$html2pdf->WriteHTML($content, $show_html);
			$html2pdf->Output($suppliername.'_'.date($core->settings['dateformat'], time()).'.pdf');
		}
	}
}
function parse_calltype(&$value) {
	global $lang;
	
	switch($value) {
		case '1': 
				$value = $lang->facetoface;
				break;
		case '2':
				$value = $lang->telephonecall;
				break;
		default: break;
	}
}
function parse_callpurpose(&$value) {
	global $lang;
	
	switch($value) {
		case '1':
				$value = $lang->followup;
				break;
		case '2':
				$value = $lang->service;
				break;
		default: break;
	}
}
?>