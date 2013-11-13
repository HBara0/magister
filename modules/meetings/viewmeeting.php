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
	$mtid = $db->escape_string = $core->input['mtid'];
	$meeting_obj = new Meetings($mtid);
	$meeting = $meeting_obj->get();
	if(!empty($meeting['fromDate'])) {
		$meeting['fromDate_output'] = date($core->settings['dateformat'], $meeting['fromDate']);
		$meeting['fromTime_output'] = date($core->settings['timeformat'], $meeting['fromDate']);
	}
	if(!empty($meeting['toDate'])) {
		$meeting['toDate_output'] = date($core->settings['dateformat'], $meeting['toDate']);
		$meeting['toTime_output'] = date($core->settings['timeformat'], $meeting['toDate']);
	}
	$user_obj = new Users($meeting['createdBy']);
	$meeting['createdby'] = $user_obj->get()['displayName'];

	if($meeting ['hasMoM'] == 1) {
		$mom_obj = new MeetingsMOM($mtid);
		$minutes_meetings = $mom_obj->get();
		if(!empty($meeting['fromDate'])) {
			$minutes_meetings['createdon_output'] = date($core->settings['dateformat'],$minutes_meetings['createdOn']);
			$minutes_meetings['createdon_timeoutput'] = date($core->settings['timeformat'], $minutes_meetings['createdOn']);
			$minutes_meetings['created_outputdate'] = $lang->sprint($lang->createdon, $minutes_meetings['createdon_output'], $minutes_meetings['createdon_timeoutput']);
		}

		eval("\$meetings_viewmeeting_mom = \"".$template->get('meetings_viewmeeting_mom')."\";");
	}

	eval("\$meeting_viewmeeting = \"".$template->get('meetings_viewmeeting')."\";");
	output_page($meeting_viewmeeting);
}
?>
