<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: create.php
 * Created:        @tony.assaad    Nov 7, 2013 | 3:17:03 PM
 * Last Update:    @tony.assaad    Nov 7, 2013 | 3:17:03 PM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['meetings_canCreateMeeting'] == 0) {
	error($lang->sectionnopermission);
}
if(!$core->input['action']) {
	if(isset($core->input['mtid']) && !empty($core->input['mtid'])) {
		$mtid = $db->escape_string = $core->input['mtid'];
		$action = 'edit';
		$lang->create = $lang->edit;

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
		$meeting['attendees'] = $meeting_obj->get_attendees();
	}
	else {
		$action = 'create';
	}
	$employees_affiliate = Meetings::get_affiliateemployees();

	$employees_list = parse_selectlist('meeting[attendees][uid]', 5, $employees_affiliate, $meeting['attendees']['attr']);

	eval("\$createmeeting = \"".$template->get('meeting_create')."\";");
	output_page($createmeeting);
}
elseif($core->input['action'] == "do_createmeeting") {
	$meeting_obj = new Meetings();
	$meeting_obj->create($core->input['meeting']);


	switch($meeting_obj->get_errorcode()) {
		case 0:
			output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
			break;
		case 1:
			output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
			break;
		case 3:
			output_xml('<status>false</status><message>'.$lang->invaliddate.'</message>');
			break;
	}
}
elseif($core->input['action'] == "do_editmeeting") {
	$mtid = $db->escape_string($core->input['mtid']);

	$meeting_obj = new Meetings($mtid);
	$meeting_obj->update($core->input['meeting']);

	switch($meeting_obj->get_errorcode()) {
		case 2:
			output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
			break;
		case 1:
			output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
			break;
	}
}
?>
