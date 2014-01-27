<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: viewmeeting.php
 * Created:        @tony.assaad    Nov 13, 2013 | 12:42:10 PM
 * Last Update:    @tony.assaad    Nov 13, 2013 | 12:42:10 PM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['meetings_canCreateMeeting'] == 0) {
	error($lang->sectionnopermission);
}

if(!$core->input['action']) {
	$meeting_obj = new Meetings($core->input['mtid']);
	if(!$meeting_obj->can_viewmeeting()) {
		error($lang->sectionnopermission);
	}
	$meeting = $meeting_obj->get();

	if(!empty($meeting['fromDate'])) {
		$meeting['fromDate_output'] = date($core->settings['dateformat'], $meeting['fromDate']);
		$meeting['fromTime_output'] = date($core->settings['timeformat'], $meeting['fromDate']);
	}
	if(!empty($meeting['toDate'])) {
		$meeting['toDate_output'] = date($core->settings['dateformat'], $meeting['toDate']);
		$meeting['toTime_output'] = date($core->settings['timeformat'], $meeting['toDate']);
	}

	$meeting['createdby'] = $meeting_obj->get_createdby()->get()['displayName'];

	if($meeting['hasMoM'] == 1) {
		$minsofmeeting = $meeting_obj->get_mom()->get();
		if(!empty($minsofmeeting['createdOn'])) {
			$minsofmeeting['createdOn_date_output'] = date($core->settings['dateformat'], $minsofmeeting['createdOn']);
			$minsofmeeting['createdOn_time_output'] = date($core->settings['timeformat'], $minsofmeeting['createdOn']);
			$minsofmeeting['createdOn_output'] = $lang->sprint($lang->createdon, $minsofmeeting['createdOn_date_output'], $minsofmeeting['createdOn_time_output']);
		}

		if(!empty($minsofmeeting['modifiedOn'])) {
			$minsofmeeting['modifiedOn_date_output'] = date($core->settings['dateformat'], $minsofmeeting['modifiedOn']);
			$minsofmeeting['modifiedOn_time_output'] = date($core->settings['timeformat'], $minsofmeeting['modifiedOn']);
			$minsofmeeting['modifiedOn_output'] = ' | '.$lang->sprint($lang->modifiedon, $minsofmeeting['modifiedOn_date_output'], $minsofmeeting['modifiedOn_time_output']);
		}

		eval("\$meetings_viewmeeting_mom = \"".$template->get('meetings_viewmeeting_mom')."\";");
	}

	$meeting['attendees_output'] = $meeting_obj->parse_attendees();

	eval("\$meeting_viewmeeting = \"".$template->get('meetings_viewmeeting')."\";");
	output_page($meeting_viewmeeting);
}
?>
