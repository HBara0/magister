<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: addadditionaldays.php
 * Created:        @tony.assaad    Apr 22, 2013 | 4:28:35 PM
 * Last Update:    @tony.assaad    Apr 22, 2013 | 4:28:35 PM
 */


if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

$attendance = new AttendanceAddDays();
$affiliate = new Affiliates($core->user['mainaffiliate']);
$affiliate_users = $affiliate->get_users();
$user = new Users($core->user['uid']);
$single_user = $user->get();
$reporting_touser = $user->get_reportingto();
$lang->load('attendance_messages');
if(!$core->input['action']) {

	/* if user is not HR */
	if($core->usergroup['hr_canHrAllAffiliates'] == 1) {
		if(is_array($reporting_touser)) {
			$affiliate_users = array_merge($reporting_touser, $affiliate_users);
		}
		if(is_array($affiliate_users)) {
			foreach($affiliate_users as $uid => $users) {
				$users_list .= "<option value='{$users['uid']}'{$selected}>{$users['displayName']}</option>";
			}
		}
	}
	else {
		$users_list = "<option value='{$core->user['uid']}' selected=selected>{$single_user['displayName']}</option>";
	}


	eval("\$addadditionaldays = \"".$template->get('attendance_addadditionaldays')."\";");
	output_page($addadditionaldays);
}
else {
	if($core->input['action'] == 'do_addadditionaldays') {
		foreach($core->input['AttendanceAddDays']['uid'] as $uid) { /* for a single user call the object and the function therefore */
			$attendance->request($uid, $core->input['AttendanceAddDays']);
		}
		switch($attendance->get_status()) {
			case 0:
				foreach($core->input['AttendanceAddDays']['uid'] as $uid) { /* for each user instantiate his object and  get his ReportsTo details ,and the user detaails */
					$user = new Users($uid);
					$reporttsto[$uid] = $user->get_reportsto()->get();
					$requester[$uid] = $user->get();
					$attendance_daydata[$uid] = $user->get_additionaldays_byuser();
					$attendance->notify_request($reporttsto[$uid], $requester[$uid], $attendance_daydata[$uid]);
				}
				output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
				break;
			case 1:
				output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
				break;
			case 2:
				output_xml("<status>false</status><message>{$lang->requestexist}</message>");
				break;
		}
	}
}
?>
