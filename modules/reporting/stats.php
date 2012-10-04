<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Show reports statistics
 * $module: reporting
 * $id: stats.php	
 * Last Update: @zaher.reda 	June 11, 2009 | 11:58 AM
 */
 
if(!defined('DIRECT_ACCESS'))
{
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canReadStats'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!$core->input['action']) {	
	if(!isset($core->input['year'], $core->input['quarter'])) {
		$core->input = currentquarter_info();
	}
	
	$query = $db->query("SELECT DISTINCT a.affid, a.name FROM ".Tprefix."reports r LEFT JOIN ".Tprefix."affiliates a ON (a.affid=r.affid) WHERE r.type='q' AND r.year='".$db->escape_string($core->input['year'])."' AND r.quarter='".$db->escape_string($core->input['quarter'])."' ORDER BY name ASC");
	while($affiliate = $db->fetch_assoc($query)) {
		$stats[$affiliate['affid']]['name'] = $affiliate['name'];
	}
	
	$quarter_details = explode('/', $settings['q'.$core->input['quarter'].'end']);// D/M
	$quarter_end = mktime(0, 0, 0, $quarter_details[1], $quarter_details[0], $core->input['year']);
	$duedate = $quarter_end + (60*60*24*15);

	$query = $db->query("SELECT affid, COUNT(rid) AS beforetime
					FROM ".Tprefix."reports
					WHERE type='q' AND year='".$db->escape_string($core->input['year'])."' AND quarter='".$db->escape_string($core->input['quarter'])."'  AND finishDate < {$duedate} AND status=1
					GROUP BY affid");
						
	while($count = $db->fetch_assoc($query)) {
		$stats[$count['affid']]['beforetime'] = $count['beforetime'];
	}
	

	$query = $db->query("SELECT affid, COUNT(rid) AS ontime
						FROM ".Tprefix."reports 
						WHERE type='q' AND year='".$db->escape_string($core->input['year'])."' AND quarter='".$db->escape_string($core->input['quarter'])."'  AND finishDate = {$duedate} AND status=1
						GROUP BY affid");
						
	while($count = $db->fetch_assoc($query)) {
		$stats[$count['affid']]['ontime'] = $count['ontime'];
	}
	
	$query = $db->query("SELECT affid, COUNT(rid) AS late
						FROM ".Tprefix."reports
						WHERE type='q' AND year='".$db->escape_string($core->input['year'])."' AND quarter='".$db->escape_string($core->input['quarter'])."'  AND finishDate > {$duedate} AND status=1
						GROUP BY affid");
						
	while($count = $db->fetch_assoc($query)) {
		$stats[$count['affid']]['late'] = $count['late'];
	}

	$query = $db->query("SELECT affid, COUNT(rid) AS never
						FROM ".Tprefix."reports
						WHERE type='q' AND year='".$db->escape_string($core->input['year'])."' AND quarter='".$db->escape_string($core->input['quarter'])."' AND status=0
						GROUP BY affid");
						
	while($count = $db->fetch_assoc($query)) {
		$stats[$count['affid']]['never'] = $count['never'];
	}
	
	$query = $db->query("SELECT affid, prActivityAvailable, keyCustAvailable, mktReportAvailable, isSent
						FROM ".Tprefix."reports
						WHERE type='q' AND year='".$db->escape_string($core->input['year'])."' AND quarter='".$db->escape_string($core->input['quarter'])."'");
	while($count = $db->fetch_assoc($query)) {
		if(is_null($stats[$count['affid']]['prActivityAvailable'])) {
			$stats[$count['affid']]['prActivityAvailable'] = 0;
		}
		if($count['prActivityAvailable'] == 0) {
			$stats[$count['affid']]['prActivityAvailable']++;
		}
		
		if(is_null($stats[$count['affid']]['keyCustAvailable'])) {
			$stats[$count['affid']]['keyCustAvailable'] = 0;
		}
		
		if($count['keyCustAvailable'] == 0) {
			$stats[$count['affid']]['keyCustAvailable']++;
		}
		
		if(is_null($stats[$count['affid']]['mktReportAvailable'])) {
			$stats[$count['affid']]['mktReportAvailable'] = 0;
		}
		if($count['mktReportAvailable'] == 0) {
			$stats[$count['affid']]['mktReportAvailable']++;
		}
		
		if(is_null($stats[$count['affid']]['isSent'])) {
			$stats[$count['affid']]['isSent'] = 0;
		}
		if($count['isSent'] == 1) {
			$stats[$count['affid']]['isSent']++;
		}
	}
	
	$affiliates_list = '';
	if(is_array($stats)) {
		foreach($stats as $key => $val) {	
			if(!isset($val['beforetime'])) { $val['beforetime'] = 0; };
			if(!isset($val['ontime'])) { $val['ontime'] = 0; };
			if(!isset($val['late'])) { $val['late'] = 0; };
			if(!isset($val['never'])) { $val['never'] = 0; };
			
			$totals['beforetime'] += $val['beforetime'];
			$totals['ontime'] += $val['ontime'];
			$totals['late'] += $val['late'];
			$totals['notfinalized'] += $val['never'];
			
			$val['finalizedtotal'] = $val['beforetime'] + $val['ontime'] + $val['late'];//$val['total'] - $val['never'];
			$val['total'] = $val['finalizedtotal'] + $val['never'];
			
			$totals['total'] += $val['total'];
			$totals['finalizedtotal'] += $val['finalizedtotal'];
			
			if($val['finalizedtotal'] > 0) {
				$val['beforetimeperc'] = round(($val['beforetime']*100)/$val['finalizedtotal'], 2);
				$val['ontimeperc'] = round(($val['ontime']*100)/$val['finalizedtotal'], 2);
				$val['lateperc'] = round(($val['late']*100)/$val['finalizedtotal'], 2);
			}
			
			$val['finalizedtotalperc'] = round(($val['finalizedtotal']*100)/$val['total'], 2);
			$val['neverperc'] = round(($val['never']*100)/$val['total'], 2);
			
			$class = alt_row($class);
			if($val['finalizedtotal'] > 0) {
				$finalized_stats .= "<tr class='{$class}'>";
				$finalized_stats .= "<td style='text-align:left; border-right: 1px solid #EAEDEE;'>{$val[name]}</td><td class='altrow' style='text-align:center; width:12px;'>{$val[beforetime]}</td><td style='text-align:center; width:12px; border-right: 1px solid #EAEDEE;'>{$val[beforetimeperc]}%</td><td class='altrow' style='text-align:center; width:12px;'>{$val[ontime]}</td><td style='text-align:center; width:12px; border-right: 1px solid #EAEDEE;'>{$val[ontimeperc]}%</td><td class='altrow' style='text-align:center; width:12px;'>{$val[late]}</td><td style='text-align:center; width:12px; border-right: 1px solid #EAEDEE;'>{$val[lateperc]}%</td><td style='text-align:center;'>{$val[finalizedtotal]}</td>";
				$finalized_stats .= '</tr>';
			}
			$general_stats .= "<tr class='{$class}'>";
			$general_stats .= "<td style='text-align:left; border-right: 1px solid #EAEDEE;'>{$val[name]}</td><td class='altrow' style='text-align:center; width:12px;'>{$val[finalizedtotal]}</td><td style='text-align:center; width:12px; border-right: 1px solid #EAEDEE;'>{$val[finalizedtotalperc]}%</td><td class='altrow' style='text-align:center; width:12px;'>{$val[never]}</td><td style='text-align:center; width:12px; border-right: 1px solid #EAEDEE;'>{$val[neverperc]}%</td><td style='text-align:center;'>{$val[total]}</td>";
			$general_stats .= '</tr>';
			
			$status_stats .= "<tr class='{$class}'>";
			$status_stats .= "<td style='text-align:left; border-right: 1px solid #EAEDEE;'>{$val[name]}</td><td class='altrow' style='text-align:center; width:12px;'>{$val[prActivityAvailable]}</td><td style='text-align:center; width:12px; border-right: 1px solid #EAEDEE;'>{$val[keyCustAvailable]}</td><td class='altrow' style='text-align:center; width:12px;'>{$val[mktReportAvailable]}</td><td style='text-align:center; width:12px;'>{$val[isSent]}</td>";
			$status_stats .= '</tr>';
		}
	}
	if($totals['finalizedtotal'] != 0) {
		$totals['beforetimeperc'] = round(@($totals['beforetime']*100)/$totals['finalizedtotal'], 2);
		$totals['ontimeperc'] = round(@($totals['ontime']*100)/$totals['finalizedtotal'], 2);
		$totals['lateperc'] = round(@($totals['late']*100)/$totals['finalizedtotal'], 2);
	}
	
	if($totals['total'] != 0) {
		$totals['finalizedtotalperc'] = round(($totals['finalizedtotal']*100)/$totals['total'], 2);
		$totals['notfinalizedperc'] = round(($totals['notfinalized']*100)/$totals['total'], 2);
	}
	
	$pie = new Charts(array("titles" => array("Before time", "On time", "Late"), "values" => array($totals['beforetime'], $totals['ontime'], $totals['late'])), "pie");
	$pie2 = new Charts(array("titles" => array("Finalized", "Not Finalized"), "values" => array($totals['finalizedtotal'], $totals['notfinalized']), "radius" => 150), "pie");
		
	$query = $db->query("SELECT DISTINCT(quarter) 
						FROM ".Tprefix."reports WHERE type='q'
						ORDER BY quarter ASC");
	$quarters[0] = '';
	while($quarter = $db->fetch_array($query)) {
		$quarters[$quarter['quarter']] = $quarter['quarter'];
	}	
						
	$quarters_list = parse_selectlist('quarter', 1, $quarters, 0);	
	eval("\$statspage = \"".$template->get('reporting_stats')."\";");
	output_page($statspage);
}
else
{ 
	if($core->input['action'] = 'get_year') {
		$quarter = $db->escape_string($core->input['quarter']);
		
		$query = $db->query("SELECT DISTINCT(year) FROM ".Tprefix."reports WHERE type='q' AND quarter='{$quarter}' AND status=1 ORDER BY year ASC");
		
		$years_list .= "<option value='0'></option>";				
		while($year= $db->fetch_array($query)) {
			$years_list .= "<option value='{$year[year]}'>{$year[year]}</option>";
		}
		echo $years_list;
	}
}
?>