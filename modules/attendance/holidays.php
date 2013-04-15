<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * List of Holidays
 * $module: hr
 * $id: holidayslist.php	
 * Created:			@zaher.reda		January 10, 2011 | 04:25 AM
 * Last Update:		@zaher.reda		  January 10, 2011 | 04:25 PM
 */
if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
	$sort_query = 'month ASC, day ASC';
	$sort_url = sort_url();
	$limit_start = 0;

	if(isset($core->input['sortby'], $core->input['order'])) {
		$sort_query = $db->escape_string($core->input['sortby']).' '.$db->escape_string($core->input['order']);
	}

	if(isset($core->input['start'])) {
		$limit_start = $db->escape_string($core->input['start']);
	}

	if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
		$core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
	}
	$current_year = date('Y', TIME_NOW);
	$multipage_where = 'affid='.$core->user[mainaffiliate].' AND (year='.$current_year.' OR isOnce=0) AND hid NOT IN (SELECT hid FROM '.Tprefix.'holidaysexceptions WHERE uid='.$core->user['uid'].')';

	$query = $db->query("SELECT * FROM ".Tprefix."holidays
						WHERE affid={$core->user[mainaffiliate]} AND (year={$current_year} OR isOnce=0)
						AND hid NOT IN (SELECT hid FROM ".Tprefix."holidaysexceptions WHERE uid=".$core->user['uid'].")
						AND ((validFrom = 0 OR ({$current_year} >= FROM_UNIXTIME(validFrom, '%Y') AND month >= FROM_UNIXTIME(validFrom, '%m') AND day >= FROM_UNIXTIME(validFrom, '%d'))) 
						AND (validTo=0 OR ({$current_year} <= FROM_UNIXTIME(validTo, '%Y') AND month <= FROM_UNIXTIME(validTo, '%m') AND day <= FROM_UNIXTIME(validTo, '%d'))))
						ORDER BY {$sort_query}
						LIMIT {$limit_start}, {$core->settings[itemsperlist]}");
 
	if($db->num_rows($query) > 0) {
		while($holiday = $db->fetch_assoc($query)) {
			$holidays_list .= "<tr><td align='center'>{$holiday[title]}</td><td align='center'>".$lang->{strtolower(date("F", mktime(0, 0, 0, $holiday['month'], 1, 0)))}."</td><td align='center'>{$holiday[day]}</td><td align='center'>{$holiday[numDays]}</td></tr>";
		}

		$multipages = new Multipages('holidays', $core->settings['itemsperlist'], $multipage_where);
		$holidays_list .= '<tr><td colspan="4">'.$multipages->parse_multipages().'</td></tr>';
	}
	else {
		$holidays_list = '<tr><td colspan="4">'.$lang->nomatchfound.'</td></tr>';
	}

	eval("\$list = \"".$template->get("attendance_holidayslist")."\";");
	output_page($list);
}
?>