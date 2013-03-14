<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Reports overview
 * $module: reporting
 * $id: home.php	
 * Last Update: @zaher.reda 	May 22, 2009 | 09:58 AM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

$lang->load('index');

if(!$core->input['action']) {
	/* if($core->usergroup['canViewAllAff'] == 0) {
	  $inaffiliates = implode(',', $core->user['affiliates']);
	  $extra_where = ' AND affid IN ('.$inaffiliates.') ';
	  }

	  if($core->usergroup['canViewAllSupp'] == 0) {
	  $insuppliers = implode(',', $core->user['suppliers']);
	  $extra_where .= '  AND spid IN ('.$insuppliers.') ';
	  } */
	$additional_where = getquery_entities_viewpermissions();

	$quarter = currentquarter_info();
	$countall_current = $db->fetch_field($db->query("SELECT count(*) as countall FROM ".Tprefix."reports r WHERE type='q' AND year='{$quarter[year]}' AND quarter='{$quarter[quarter]}'{$additional_where[extra]}"), 'countall');
	if($countall_current > 0) {
		$countall_current_unfinalized = $db->fetch_field($db->query("SELECT count(*) as countall FROM ".Tprefix."reports r WHERE type='q' AND year='{$quarter[year]}' AND quarter='{$quarter[quarter]}' AND status='0'{$additional_where[extra]}"), 'countall');
	}
	else {
		$countall_current_unfinalized = 0;
	}
	$countall = $db->fetch_field($db->query("SELECT count(*) as countall FROM ".Tprefix."reports r WHERE type='q' AND (status='0' OR status='1'){$additional_where[extra]}"), 'countall');
	if($countall > 0) {
		$countall_unfinalized = $db->fetch_field($db->query("SELECT count(*) as countall FROM ".Tprefix."reports r WHERE type='q' AND status='0'{$additional_where[extra]}"), 'countall');
	}
	else {
		$countall_unfinalized = 0;
	}

	$lang->overviewcurrentquarter = $lang->sprint($lang->overviewcurrentquarter, $countall_current, $countall_current_unfinalized);
	$lang->overviewall = $lang->sprint($lang->overviewall, $countall, $countall_unfinalized);

	/* $extra_where = '';
	  if($core->usergroup['canViewAllAff'] == 0) {
	  $extra_where = ' AND r.affid IN ('.$inaffiliates.') ';
	  }

	  if($core->usergroup['canViewAllSupp'] == 0) {
	  $extra_where .= '  AND r.spid IN ('.$insuppliers.') ';
	  } */

	$query = $db->query("SELECT r.quarter, r.year, s.companyName, a.name AS affiliate_name
						FROM ".Tprefix."reports r JOIN ".Tprefix."entities s ON (r.spid=s.eid) JOIN ".Tprefix."affiliates a ON (r.affid=a.affid)
						WHERE r.type='q' AND r.status='1'{$additional_where[extra]}
						ORDER BY r.finishDate DESC
						LIMIT 0, 3");
	if($db->num_rows($query) > 0) {
		while($last_report = $db->fetch_array($query)) {
			$last_reports_list .= "<li>Q{$last_report[quarter]} {$last_report[year]} - {$last_report[companyName]} / {$last_report[affiliate_name]}</li>";
		}
	}
	else {
		$last_reports_list = '<li>'.$lang->na.'</li>';
	}

	$query = $db->query("SELECT r.quarter, r.year, s.companyName, a.name AS affiliate_name
						FROM ".Tprefix."reports r JOIN ".Tprefix."entities s ON (r.spid=s.eid) JOIN ".Tprefix."affiliates a ON (r.affid=a.affid)
						WHERE r.type='q' AND r.status='1' AND uidFinish='{$core->user[uid]}'
						ORDER BY r.finishDate DESC
						LIMIT 0, 3");
	if($db->num_rows($query) > 0) {
		while($last_finalized_report = $db->fetch_array($query)) {
			$last_finalized_reports_list .= "<li>Q{$last_report[quarter]} {$last_report[year]} - {$last_report[companyName]} / {$last_report[affiliate_name]}</li>";
		}
	}
	else {
		$last_finalized_reports_list = '<li>'.$lang->na.'</li>';
	}

	$quarter_settings = explode('-', $core->settings['q'.$quarter['quarter'].'start']);

	$start_notifications = mktime(0, 0, 0, $quarter_settings[1], $quarter_settings[0], $quarter['year']);

	$query = $db->query("SELECT r.quarter, r.year, s.companyName, a.name AS affiliate_name
						FROM ".Tprefix."reports r JOIN ".Tprefix."entities s ON (r.spid=s.eid) JOIN ".Tprefix."affiliates a ON (r.affid=a.affid)
						WHERE r.type='q' AND r.status='0' AND (r.initDate+(15*24*60*60)-(60*60*24*10)) < ".time()." AND ((".time()." - r.initDate)/24/60/60) < 10{$additional_where[extra]}
						ORDER BY r.initDate ASC
						LIMIT 0, 3");
	if($db->num_rows($query) > 0) {
		while($due_report = $db->fetch_array($query)) {
			$due_reports_list .= "<li>Q{$due_report[quarter]} {$due_report[year]} - {$due_report[companyName]} / {$due_report[affiliate_name]}</li>";
		}
	}
	else {
		$due_reports_list = '<li>'.$lang->na.'</li>';
	}

	if($core->usergroup['canAdminCP']) {
		$admin_countall = $db->fetch_field($db->query("SELECT count(*) as countall FROM ".Tprefix."reports WHERE type='q'"), 'countall');
		$admin_countall_unfinalized = $db->fetch_field($db->query("SELECT count(*) as countall FROM ".Tprefix."reports WHERE type='q' AND status='0'"), 'countall');
		$lang->overviewall_admin = $lang->sprint($lang->overviewall_admin, $admin_countall, $admin_countall_unfinalized);
		$admin_overview = '<li>'.$lang->overviewall_admin.'</li>';
	}

	$lang->duexdays = $lang->sprint($lang->duexdays, 10);
	eval("\$reportinghome = \"".$template->get('index')."\";");
	output_page($reportinghome);
}
?>