<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * View calendar
 * $module: calendar
 * $id: home.php
 * Created: 	@zaher.reda		Feb 04, 2009 | 10:14 AM
 * Last Update: @tony.assaad	Sep 6, 2013 | 12:09:56 PM
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
    $headerinc .= '<script src="./js/calendar_weekview.min.js" type="text/javascript"></script>';
}

$main_calendar = new Calendar($view_type, array('year' => $core->input['year'], 'week' => $core->input['week'], 'day' => $core->input['day'], 'month' => $core->input['month']));

$calendar_title = $main_calendar->get_title();

if($view_type == 'week') {
    $main_calendar->read_leaves(false, '', array(10));
    $calendar = $main_calendar->get_calendar_weekview();

    $previous_period = $main_calendar->get_prev_period();
    $next_period = $main_calendar->get_next_period();
    $prevlink_querystring = '&amp;week='.$previous_period['week'].'&amp;year='.$previous_period['year'];
    $nextlink_querystring = '&amp;week='.$next_period['week'].'&amp;year='.$next_period['year'];

    eval("\$addeventtask_popup = \"".$template->get('popup_calendar_weekview_createentry')."\";");
}
else {
    $main_calendar->read_tasks();
    $main_calendar->read_events();
    $main_calendar->read_meetings();
    $main_calendar->read_holidays();
    $main_calendar->read_leaves();
    $calendar = $main_calendar->get_calendar();

    $previous_period = $main_calendar->get_prev_period();
    $next_period = $main_calendar->get_next_period();
    $prevlink_querystring = '&amp;month='.$previous_period['mon'].'&amp;year='.$previous_period['year'];
    $nextlink_querystring = '&amp;month='.$next_period['mon'].'&amp;year='.$next_period['year'];

    /* Parse events/tasks popup - Start */
    $eventtypes = get_specificdata('calendar_eventtypes', array('cetid', 'title'), 'cetid', 'title', array('by' => 'title', 'sort' => 'ASC'));
    $eventypes_selectlist = parse_selectlist('event[type]', 1, $eventtypes, 0, '', '', array('blankstart' => 1, 'id' => 'event_type'));
    $etypemorefields = array(4);
    $etypemorefields = implode(', ', $etypemorefields);

    $affiliate_address = $db->fetch_field($db->query("SELECT CONCAT(addressLine1, ', ', addressLine2, ', ', city) AS address FROM ".Tprefix."affiliates WHERE affid = ".$core->user['mainaffiliate']), 'address');

    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliate_where = 'affid IN ('.$inaffiliates.')';
    }
    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 0, $affiliate_where);
    $eventaffiliates_selectlist = parse_selectlist('event[affid]', 2, $affiliates, '', '', '', array('blankstart' => 1));

    $suppliers_selectlist = '-';

    if($core->usergroup['canViewAllSupp'] == 0) {
        if(is_array($core->user['suppliers']['eid'])) {
            $insupplier = implode(',', $core->user['suppliers']['eid']);
            $supplier_where = ' eid IN ('.$insupplier.')';
        }
    }
    else {
        $supplier_where = ' type="s"';
        $suppliers = get_specificdata('entities', array('eid', 'companyName'), 'eid', 'companyName', array('by' => 'companyName', 'sort' => 'ASC'), 1, $supplier_where);
        $suppliers_selectlist = parse_selectlist('event[spid]', 3, $suppliers, '', 0, '', array('blankstart' => 1, 'id' => 'spid'));
    }
    //$affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'));
    $affiliates_selectlist = parse_selectlist('event[restrictto][]', 1, $affiliates, '', 1);

    if($core->usergroup['calendar_canAddPublicEvents'] == 1) {
        $ispublic_checkbox = '<div style="width:20%; display:inline-block;">&nbsp;</div><div style="width:70%; display:inline-block;"><input name="event[isPublic]" type="checkbox" value="1" /> '.$lang->ispublic.'</div><br />';
        $restriction_selectlist = '<div style="width:20%; display:inline-block; vertical-align:top;">'.$lang->restricto.'</div><div style="width:70%; display:inline-block;">'.$affiliates_selectlist.'</div>';
        $notifyevent_checkbox = '<div style="width:20%; display:inline-block;">&nbsp;</div><div style="width:70%; display:inline-block;"><input name="event[notify]" type="checkbox" value="1" /> '.$lang->notifyevent.'</div><br />';
    }


    $assignedto_employees = get_specificdata('users', array('uid', 'displayName'), 'uid', 'displayName', '', 0, "reportsTo='{$core->user[uid]}' AND gid!=7");
    if(is_array($assignedto_employees)) {
        $assignedto_employees[$core->user['uid']] = '';
        asort($assignedto_employees);
        $assignedto_selectlist = parse_selectlist('task[uid]', 1, $assignedto_employees, '');
    }
    else {
        $assignedto_selectlist = '- <input type="hidden" id="task[uid]" name="task[uid]" value="'.$core->user['uid'].'" />';
    }

    $tasks_notify_radiobutton = parse_yesno('task[notify]', 5, 1);
    $reminderinterval_selectlist = parse_selectlist('task[reminderInterval]', 1, array('' => '', '86400' => $lang->eveyday, '172800' => $lang->evey2day, '604800' => $lang->everyweek, '1209600' => $lang->every2weeks, '2592000' => $lang->everymonth, '31104000' => $lang->everyyear), '');
    $current_date = $main_calendar->get_currentdate();

    /* parse invitees - START */
    $affiliates_users = array();
    foreach($core->user['affiliates'] as $affid) {
        $affiliate_obj = new Affiliates($affid);
        $affiliates_users = $affiliates_users + $affiliate_obj->get_users(array('returnobjects' => true));
    }

    foreach($affiliates_users as $uid => $user_obj) {
        //$checked = '';
        $altrow = alt_row($altrow);
//		if($uid == $core->user['uid']) {
//			continue;
//		}
//		if(is_array($affiliates_users)) {
//			if(in_array($uid, $affiliates_usersid)) {
//				$checked = ' checked="checked"';
//			}
//		}
        $user = $user_obj->get();
        $user['affiliate'] = $user_obj->get_mainaffiliate()->get()['name'];
        eval("\$invitees_rows .= \"".$template->get('calendar_events_invitees_rows')."\";");
    }
    unset($affiliates_users);
    /* parse invitees - END */

    /* Parse share with users */
    $users = Users::get_allusers();
    foreach($users as $uid => $user) {
        $checked = $rowclass = '';
        if($uid == $core->user['uid']) {
            continue;
        }
        eval("\$sharewith_rows .= \"".$template->get('calendar_createeventtask_sharewithrows')."\";");
    }
    eval("\$task_sharewith = \"".$template->get('calendar_createeventtask_sharewithsection')."\";");

    eval("\$addeventtask_popup = \"".$template->get('popup_calendar_createeventtask')."\";");
    /* Parse events/tasks popup - End */
}

eval("\$calendarpage = \"".$template->get('calendar')."\";");
output_page($calendarpage);
?>