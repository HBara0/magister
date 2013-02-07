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
//$main_calendar = new Calendar('month', array('month' => $core->input['month'], 'year' => $core->input['year']));
$view_type = 'month';
if(!isset($core->input['view'])) {
	if($db->fetch_field($db->query('SELECT defaultView FROM '.Tprefix.'calendar_userpreferences WHERE uid='.$core->user['uid']), 'defaultView') == 2) {
		$view_type = 'week';
	}
}
if($core->input['view'] == 'week' || !empty($core->input['week']) || $view_type == 'week') {
	$view_type = 'week';
	$headerinc .= '<script src="./js/calendar_weekview.js" type="text/javascript"></script>';
}

$main_calendar = new Calendar($view_type, array('year' => $core->input['year'], 'week' => $core->input['week'], 'month' => $core->input['month']));

$calendar_title = $main_calendar->get_title();

if($view_type == 'week') {
	$main_calendar->read_leaves(false, '', array(8));
	$calendar = $main_calendar->get_calendar_weekview();
	
	$previous_period = $main_calendar->get_prev_period();
	$next_period = $main_calendar->get_next_period();
	$prevlink_querystring = '&amp;week='.$previous_period['week'].'&amp;year='.$previous_period['year'];
	$nextlink_querystring = '&amp;week='.$next_period['week'].'&amp;year='.$next_period['year'];
	
	eval("\$addeventtask_popup = \"".$template->get('popup_calendar_weekview_createentry')."\";");
}
else 
{
	$main_calendar->read_tasks();
	$main_calendar->read_events();
	$main_calendar->read_holidays();
	$main_calendar->read_leaves();

	$calendar = $main_calendar->get_calendar();
	
	$previous_period = $main_calendar->get_prev_period();
	$next_period = $main_calendar->get_next_period();
	$prevlink_querystring = '&amp;month='.$previous_period['mon'].'&amp;year='.$previous_period['year'];
	$nextlink_querystring = '&amp;month='.$next_period['mon'].'&amp;year='.$next_period['year'];
		
	/* Parse events/tasks popup - Start */
	$eventtypes = get_specificdata('calendar_eventtypes', array('cetid', 'title'), 'cetid', 'title', array('by' => 'title', 'sort' => 'ASC'));	
	$eventypes_selectlist = parse_selectlist('event[type]', 1, $eventtypes, 0);
	$location_query = $db->fetch_assoc($db->query("SELECT addressLine1 AS eventlocaion FROM ".Tprefix."affiliates WHERE affid = ".$core->user['mainaffiliate']));

	$affiliate_address = $location_query['eventlocaion'];
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
	$reminderinterval_selectlist = parse_selectlist('task[reminderInterval]', 1, array('' => '', '86400' => $lang->eveyday, '172800' => $lang->evey2day, '604800' => $lang->everyweek, '1209600' => $lang->every2weeks, '2592000' => $lang->everymonth, '31104000' => $lang->everyyear), '');	
	$current_date = $main_calendar->get_currentdate();

	eval("\$addeventtask_popup = \"".$template->get('popup_calendar_createeventtask')."\";");
	/* Parse events/tasks popup - End */
}

eval("\$calendarpage = \"".$template->get('calendar')."\";");
output_page($calendarpage);
?>