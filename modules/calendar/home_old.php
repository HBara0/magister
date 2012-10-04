<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * View calendar
 * $module: calendar
 * $id: home.php
 * Created: 	@zaher.reda		Feb 04, 2009 | 10:14 AM		
 * Last Update: @zaher.reda 	April 20, 2012 | 12:41 AM
 */
if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

//require './global.php';
require './inc/attendance_functions.php';

if(isset($core->input['month'], $core->input['year'])) {
	$actual_date = getdate(TIME_NOW);
	$current_date = array(
		'year' => $db->escape_string($core->input['year']),
		'mon' => $db->escape_string($core->input['month']),
		'mday' => $actual_date['mday']
	);
	
	define('CALENDAR_TIME', mktime(0, 0, 0, $current_date['mon'], 1, $current_date['year']));
}
else
{
	define('CALENDAR_TIME', TIME_NOW);
	$actual_date = $current_date = getdate(TIME_NOW);
}

$calendar_title = $lang->{strtolower(date('F', CALENDAR_TIME))}.', '.$current_date['year'];

/* PARSE NEXT / PREV MONTH LINKS DATA - Start */
$previous_month = getdate(strtotime('-1 month', CALENDAR_TIME));
$next_month = getdate(strtotime('+1 month', CALENDAR_TIME));
/* PARSE NEXT / PREV MONTH LINKS DATA - END */

$month['firstday'] = mktime(0, 0, 0, $current_date['mon'], 1, $current_date['year']);
$month['lastday'] = mktime(23, 0, 0, $next_month['mon'], 0, $next_month['year']);
$month['numdays'] = date('t', $month['firstday']);
$month['firstday_weekday'] = date('N', $month['firstday']);


/* GET CALENDAR USER PREFERENCES - START */
$query = $db->query("SELECT * FROM ".Tprefix."calendar_userpreferences WHERE uid={$core->user[uid]}");
if($db->num_rows($query) > 0) {
	$calendar_preferences = $db->fetch_array($query);
	
	$excludedaffiliates_query = $db->query("SELECT * FROM ".Tprefix."calendar_userpreferences_excludedaffiliates a JOIN calendar_userpreferences p ON (a.cpid=p.cpid) WHERE p.uid={$core->user[uid]}");  
	if($db->num_rows($excludedaffiliates_query) > 0) {
		while($excludedaffiliates = $db->fetch_array($excludedaffiliates_query)) {
			$user_excludedaffiliates[] = $excludedaffiliates['affid'];
		}
	}
	
	$excludedusers_query = $db->query("SELECT * FROM ".Tprefix."calendar_userpreferences_excludedusers u JOIN calendar_userpreferences p ON (u.cpid=p.cpid) WHERE p.uid={$core->user[uid]}");
	if($db->num_rows($excludedusers_query) > 0) {
		while($excludedusers = $db->fetch_array($excludedusers_query)) {
			$user_excludedemployees[$excludedusers['euid']] = $excludedusers['euid'];
		}
	}
}	
/* GET CALENDAR USER PREFERENCES - END */


/* GET USER AFFILIATES - START */
if(is_array($user_excludedaffiliates)) {
	$affiliates_querystring = ' AND ae.affid NOT IN ('.implode(",", $user_excludedaffiliates).')';
}

$affiliates_query = $db->query("SELECT a.name, ae.* FROM ".Tprefix."affiliatedemployees ae JOIN ".Tprefix."affiliates a ON (a.affid=ae.affid) WHERE uid='{$core->user[uid]}'{$affiliates_querystring}");
while($affiliate = $db->fetch_assoc($affiliates_query)) {
	$affiliates['affid'][$affiliate['affid']] = $affiliate['affid'];
	$affiliates['name'][$affiliate['affid']] = $affiliate['name'];
}

$query = $db->query("SELECT affid, name FROM ".Tprefix."affiliates WHERE supervisor='{$core->user[uid]}'");
if($db->num_rows($query) > 0) {
	while($affiliate = $db->fetch_assoc($query)) {
		if(isset($affiliates['affid'][$affiliate['affid']])) {
			$affiliates['supervised'][$affiliate['affid']] = $affiliate['affid'];
			$affiliates['name'][$affiliate['affid']] = $affiliate['name'];
		}
		else
		{
			$affiliates['affid'][$affiliate['affid']] = $affiliate['affid'];
			$affiliates['supervised'][$affiliate['affid']] = $affiliate['affid'];
		}
	}
}
/* GET USER AFFILIATES - END */

/* GET RELATED LEAVES - START */
if($calendar_preferences['excludeLeaves'] == 0) {
	$approved_lids = $unapproved_lids = array();
	
	if(is_array($user_excludedemployees)) {
		$affiliate_users_querystring = ' AND uid NOT IN ('.implode(",", $user_excludedemployees).')';
	}
	
	foreach($affiliates['affid'] as $affid => $affiliate) {
		$affiliate_users = get_specificdata('affiliatedemployees', 'uid', 'uid', 'uid', '', 0, "affid='{$affiliate}' AND isMain='1'{$affiliate_users_querystring}");
		if(empty($affiliate_users)) {
			continue;
		}
		
		//$query = $db->query("SELECT l.lid, la.isApproved FROM ".Tprefix."leaves l JOIN ".Tprefix."leavesapproval la ON (l.lid=la.lid) WHERE ((l.fromDate BETWEEN ".$month['firstday']." AND ".$month['lastday'].") OR (l.toDate BETWEEN ".$month['firstday']." AND ".$month['lastday'].")) AND l.uid IN (".implode(', ', $affiliate_users).")");
		$query = $db->query("SELECT l.lid, la.isApproved 
							FROM ".Tprefix."leaves l JOIN ".Tprefix."leavesapproval la ON (l.lid=la.lid) 
							WHERE (((l.fromDate BETWEEN ".$month['firstday'].". AND ".$month['lastday'].") OR (l.toDate BETWEEN ".$month['firstday']." AND ".$month['lastday'].")) OR ((".$month['firstday']." BETWEEN l.fromDate AND l.toDate) OR (".$month['lastday']." BETWEEN l.fromDate AND l.toDate))) AND l.uid IN (".implode(', ', $affiliate_users).")");
		if($db->num_rows($query) > 0) {
			while($leave = $db->fetch_assoc($query)) {
				if($leave['isApproved'] == 0) {
					$unapproved_lids[$leave['lid']] = $leave['lid'];
					if(in_array($leave['lid'], $approved_lids)) {
						unset($approved_lids[$leave['lid']]);
					}
				}
				if(!in_array($leave['lid'], $unapproved_lids) && $leave['isApproved'] == 1) {
					$approved_lids[$leave['lid']] = $leave['lid'];
				}
			}
		}
	}
	
	if(!empty($approved_lids)) {
		$query = $db->query("SELECT l.*, l.uid AS requester, Concat(u.firstName, ' ', u.lastName) AS employeename
				FROM ".Tprefix."leaves l JOIN ".Tprefix."users u ON (l.uid=u.uid) 
				WHERE l.lid IN (".implode(',', $approved_lids).") ORDER BY l.fromDate ASC");

		if($db->num_rows($query) > 0) {		
			while($more_leaves = $db->fetch_assoc($query)) {
				$num_days_off = ($more_leaves['toDate']-$more_leaves['fromDate'])/24/60/60;//(date('z', $more_leaves['toDate'])-date('z', $more_leaves['fromDate']))+1;
 
				$leave_type_details = parse_type($more_leaves['type']);
				$more_leaves['type'] = $leave_type_details;
				
				if($num_days_off == 1) {
					$current_check_date = getdate($more_leaves['toDate']);
					$leaves[$current_check_date['mday']][] = $more_leaves;
				}
				else
				{
					for($i=0;$i<$num_days_off;$i++) {
						$current_check = $more_leaves['fromDate']+(60*60*24*$i);
						
						if($month['firstday'] > $current_check || $month['lastday'] < $current_check) {							
							continue;
						}
						
						if($current_check > ($month['firstday']*60*60*24*$month['numdays'])) {
							break;
						}
						$current_check_date = getdate($current_check);
						$leaves[$current_check_date['mday']][] = $more_leaves;
					}
				}
			}
	
		}
	}
}
/* GET RELATED LEAVES - END */

/* GET HOLIDAYS - START */
if($calendar_preferences['excludeHolidays'] == 0) {
	if(is_array($user_excludedaffiliates)) {
		$holidays_querystring = ' AND aff.affid NOT IN ('.implode(",", $user_excludedaffiliates).')';
	}
	$holidays_query = $db->query("SELECT aff.name AS affiliatename, h.*, c.acronym AS country
									FROM ".Tprefix."holidays h JOIN ".Tprefix."affiliates aff ON (aff.affid=h.affid) LEFT JOIN countries c ON (aff.country=c.coid)
									WHERE (year=0 OR year={$current_date[year]}) AND month={$current_date[mon]}{$holidays_querystring}");// AND h.affid IN (".implode(',',$affiliates['affid']).")
	while($holiday = $db->fetch_assoc($holidays_query)) {
		if($holiday['year'] == 0) {
			$holiday['year'] == $time_details['year'];
		}
		
		if(!isset($affiliates['name'][$holiday['affid']])) {
			$affiliates['name'][$holiday['affid']] = $holiday['affiliatename'];
		}
		if(!isset($affiliates['country'][$holiday['affid']])) {
			$affiliates['country'][$holiday['affid']] = $holiday['country'];
		}
		$holidays[$holiday['day']][$holiday['affid']][] = $holiday;
		
		if($holiday['numDays'] > 1) {
			for($daynum = 1; $daynum < $holiday['numDays']; $daynum++) {
				if(($holiday['day']+$daynum) > date('t', mktime(0, 0, 0, $holiday['month'], $holiday['day'], $holiday['year']))) {
					break;
				}
				$holidays[$holiday['day']+$daynum][$holiday['affid']][] = $holiday;			
			}
		}
	}
}
/* GET HOLIDAYS - END */

/* Get Tasks - START */
$tasks_query = $db->query("SELECT * FROM ".Tprefix."calendar_tasks WHERE (uid='{$core->user[uid]}' OR createdBy='{$core->user[uid]}') AND (dueDate BETWEEN {$month[firstday]} AND {$month[lastday]}) ORDER BY dueDate ASC, priority DESC");
while($task = $db->fetch_assoc($tasks_query)) {
	$task_date = getdate($task['dueDate']);
	$tasks[$task_date['mday']][] = $task;
}
/* Get Tasks - END */

/*Get events - START */
if($calendar_preferences['excludeEvents'] == 0) {
	$events_query = $db->query("SELECT * FROM ".Tprefix."calendar_events WHERE (uid='{$core->user[uid]}' OR isPublic=1) AND ((fromDate BETWEEN ".$month['firstday'].". AND ".$month['lastday'].") OR (toDate BETWEEN ".$month['firstday']." AND ".$month['lastday']."))");
	while($event = $db->fetch_assoc($events_query)) {
	
		if($event['isPublic'] == 1 && $core->usergroup['canViewAllAff'] == 0) {
			$restricted = false;
			$event_restrictions_query = $db->query("SELECT affid FROM ".Tprefix."calendar_events_restrictions WHERE ceid='{$event[ceid]}'");
			while($restriction = $db->fetch_assoc($event_restrictions_query)) {
				if(in_array($restriction['affid'], $core->user['affiliates'])) {
					break;
				}
				$restricted = true;
			}
			
			if($restricted == true) {
				continue;
			}
		}
		$num_days_off = (($event['toDate']-$event['fromDate'])/24/60/60)+1;//(date('z', $event['toDate'])-date('z', $event['fromDate']))+1;
		
		if($num_days_off == 1) {
			$current_check_date = getdate($event['toDate']);
			$events[$current_check_date['mday']][] = $event;
		}
		else
		{
			for($i=0;$i<$num_days_off;$i++) {
				$current_check = $event['fromDate']+(60*60*24*$i);
				
				if($month['firstday'] > $current_check) { //|| $more_leaves['toDate'] < $current_check) {
					continue;
				}
				
				if($current_check > ($month['firstday']*60*60*24*$month['numdays'])) {
					break;
				}
				
				$current_check_date = getdate($current_check);
				$events[$current_check_date['mday']][] = $event;
			}
		}
	}
}
/* Get events - END */

$weekdays = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday', 'Sunday');

$calendar = '<tr><td class="calendar_head">Week</td>';
$calendar .= '<td class="calendar_head">'.implode('</td><td class="calendar_head">', $weekdays).'</td></tr>';
$calendar .= '<tr><td class="calendar_weeknum">'.date('W', $month['firstday']).'</td>';

$week_num_days = 1;

for($days_prev_month=1; $days_prev_month<$month['firstday_weekday'];$days_prev_month++) {
	$calendar .= '<td class="calendar_noday">&nbsp;</td>';
	$week_num_days++;
}

for($day = 1; $day <= $month['numdays']; $day++) {
	$current_day_style = 'calendar_day';

	if($actual_date['mday'] == $day && $actual_date['mon'] == $current_date['mon'] && $actual_date['year'] == $current_date['year']) {
		$current_day_style = 'calendar_today';
	}

	$calendar .= '<td class="'.$current_day_style.'">';
	$calendar .= '<div class="calendar_day_number" id="day'.$day.'"><a href="#popup_createeventtask" id="createeventtask_'.$day.'-'.$current_date['mon'].'-'.$current_date['year'].'_day" class="showpopup">'.$day.'</a></div>';
	
	if(isset($holidays[$day])) {
		$calendar .= '<p class="calendar_dayevent">';
		foreach($holidays[$day] as $affid => $affiliate_holidays) {
			$calendar .= '<a href="#" class="tooltip"><img src="./images/icons/flags/'.strtolower($affiliates['country'][$affid]).'.gif" border="0" alt="'.$affiliates['name'][$affid].'"/> <span>'.$affiliates['name'][$affid].'</span></a>';
			//$legend[$affid] = '<img src="'.DOMAIN.'/images/icons/flags/'.strtolower($affiliates['country'][$affid]).'.gif" border="0" alt="'.$affiliates['name'][$affid].'"/> '.$affiliates['name'][$affid]; 

			foreach($affiliate_holidays as $val) {
				$calendar .= '&nbsp;'.$val['title'].'<br />';
			}
		}
		$calendar .= '</p>';
	}
	
	if(isset($leaves[$day])) {		
		$calendar .= '<p class="calendar_dayevent"><strong>'.$lang->leaves.':</strong><br />';
		foreach($leaves[$day] as $val) {
			if(!empty($val['type']['symbol'])) {
				$val['type']['symbol'] =  '<span class="smalltext">'.$val['type']['symbol'].'</span>';
			}

			$calendar .= '<a href="users.php?action=profile&amp;uid='.$val['uid'].'">'.$val['employeename'].'</a> '.$val['type']['symbol'].'<br />';	
		}
		$calendar .= '</p>';
	}
	
	if(isset($tasks[$day])) {
		$calendar .= '<p class="calendar_dayevent"><strong>'.$lang->tasks.':</strong><br />';
		foreach($tasks[$day] as $val) {
			$task_spanstyle = $checkbox_checked = '';
			if($val['isDone'] == 1) {
				$task_spanstyle = ' style="text-decoration:line-through;"';
				$checkbox_checked = ' checked="checked"';
			}
			$calendar .= '<span id="settaskdone_'.$val['ctid'].'_calendar/eventstasks_checkbox_Result"><input type="checkbox" class="ajaxcheckbox" id="settaskdone_'.$val['ctid'].'_calendar/eventstasks_checkbox" value="1"'.$checkbox_checked.'/></span><span'.$task_spanstyle.' id="ctid_'.$val['ctid'].'"><a href="#ctid_'.$val['ctid'].'" id="taskdetails_'.$val['ctid'].'_calendar/eventstasks_loadpopupbyid">'.$val['subject'].'</a></span><br />';	
		}
		$calendar .= '</p>';
	}
	
	if(isset($events[$day])) {
		$calendar .= '<p class="calendar_dayevent"><strong>'.$lang->events.':</strong><br />';
		foreach($events[$day] as $val) {
			$calendar .= '<a href="#" id="eventdetails_'.$val['ceid'].'_calendar/eventstasks_loadpopupbyid">'.$val['title'].'</a><br />';
		}
	}
	
	$calendar .= '</td>';
	
	if($week_num_days == 7) {
		$calendar .= '</tr><tr><td class="calendar_weeknum">'.date('W', $month['firstday']+(60*60*24*($day+1))).'</td>';
		//$calendar .= '</tr><tr><td class="calendar_weeknum">'.($day_hours['firsthour']+(30*60)).'</td>';
		$week_num_days = 0;
	}
	else
	{
		if($day == $month['numdays']) {
			for($days_next_month = 1; $days_next_month<=(7-$week_num_days);$days_next_month++) {
				$calendar .= '<td class="calendar_noday">&nbsp;</td>';
			}
			$calendar .= '</tr>';
		}
	} 
	$week_num_days++;
}

//$calendar_legend = implode('<br />', $legend);

/* Parse events/tasks popup - Start */
$eventtypes = get_specificdata('calendar_eventtypes', array('cetid', 'title'), 'cetid', 'title', array('by' => 'title', 'sort' => 'ASC'));	
$eventypes_selectlist = parse_selectlist('event[type]', 1, $eventtypes, 0);

$affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name',  array('by' => 'name', 'sort' => 'ASC'));
$affiliates_selectlist = parse_selectlist('event[restrictto][]', 1, $affiliates, '', 1);

if($core->usergroup['calendar_canAddPublicEvents'] == 1) {
	$ispublic_checkbox = '<div style="width:20%; display:inline-block;">&nbsp;</div><div style="width:70%; display:inline-block;"><input name="event[isPublic]" type="checkbox" value="1" /> '.$lang->ispublic.'</div><br />';
    $restriction_selectlist = '<div style="width:20%; display:inline-block; vertical-align:top;">'.$lang->restricto.'</div><div style="width:70%; display:inline-block;">'.$affiliates_selectlist.'</div>';
	$notifyevent_checkbox = '<div style="width:20%; display:inline-block;">&nbsp;</div><div style="width:70%; display:inline-block;"><input name="event[notify]" type="checkbox" value="1" /> '.$lang->notifyevent.'</div><br />';
}

if($core->usergroup['calendar_canPublishEvents'] == 1) {
	$publishonwebsite_checkbox = '<div style="width:20%; display:inline-block;">&nbsp;</div><div style="width:70%; display:inline-block;"><input name="event[publishOnWebsite]" type="checkbox" value="1" /> '.$lang->publishonwebsite.'</div>';
}

$assignedto_employees = get_specificdata('users', array('uid', 'displayName'), 'uid', 'displayName', '', 0, "reportsTo='{$core->user[uid]}' AND gid!=7");
if(is_array($assignedto_employees)) {
	$assignedto_employees[$core->user['uid']] = '';
	asort($assignedto_employees);
	$assignedto_selectlist = parse_selectlist('task[uid]', 1, $assignedto_employees, '');	
}
else
{
	$assignedto_selectlist = '- <input type="hidden" id="task[uid]" name="task[uid]" value="'.$core->user['uid'].'" />';	
}

$tasks_notify_radiobutton = parse_yesno('task[notify]', 5, 1);

eval("\$addeventtask_popup = \"".$template->get('popup_calendar_createeventtask')."\";");
/* Parse events/tasks popup - End */

eval("\$calendarpage = \"".$template->get('calendar')."\";");
output_page($calendarpage);
?>