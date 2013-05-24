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
	$user = new Users($core->user['uid']);
	if($user->can_hr('inaffiliate')) {
		if(is_array($affiliate_users)) {
			foreach($affiliate_users as $uid => $users) {
				$users_list .= "<option value='{$users['uid']}'{$selected}>{$users['displayName']}</option>";
			}
		}
	}
	else {
		$users_list = "<option value='{$core->user['uid']}' selected=selected>{$single_user['displayName']}</option>";
		if(is_array($reporting_touser)) {
			foreach($reporting_touser as $uid => $user) {
				$users_list .= '<option value="'.$user['uid'].'"'.$selected.'>'.$user['displayName'].'</option>';
			}
		}
	}

	eval("\$addadditionaldays = \"".$template->get('attendance_addadditionaldays')."\";");
	output_page($addadditionaldays);
}
else {
	if($core->input['action'] == 'do_addadditionaldays') {
		$error_handler = new ErrorHandler();
		if(is_array($core->input['AttendanceAddDays']['uid'])) {
			foreach($core->input['AttendanceAddDays']['uid'] as $uid) { /* for a single user call the object and the function therefore */
				unset($usererror, $record_usererror);
				$outputerror = '';

				$useralreadyrequested = $lang->useralreadyrequested.' , ';
				$lang->useralreadyrequested = '';
				$newid = $attendance->request($uid, $core->input['AttendanceAddDays']);

				switch($attendance->get_status()) {
					case 0:
		
						$new_adddays = new AttendanceAddDays(array('adid' => $newid));
						$new_adddays->notify_request();
						break;
					case 1:
						$useralreadyrequested = '';
						//Record Error
						$error_handler->record('fillallrequiredfields', '');
						break;
					case 2:
						//Record Error
						$user = new Users($uid);
						$erroruser = $user->get();
						$error_handler->record('requestintersectsleave', $erroruser['displayName']);
						break;
				}
			}
			$errors = $error_handler->get_errors_inline();
			if(isset($errors)) {
				output_xml('<status>false</status><message>'.$errors.'</message>');
			}
			else {
				output_xml("<status>true</status><message>{$successfullysaved}</message>");
			}
		}
		else {
			output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
		}
	}
}
?>
