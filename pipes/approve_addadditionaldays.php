#!/usr/bin/php -q
<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: approve_addadditionaldays.php
 * Created:        @tony.assaad    Apr 26, 2013 | 2:43:16 PM
 * Last Update:    @tony.assaad    Apr 26, 2013 | 2:43:16 PM
 */


$dir = dirname(dirname(__FILE__)).'/';
if(!$dir) {
	$dir = '..';
}
require_once $dir.'/inc/init.php';


$pipe = new Pipe();
$data = $pipe->get_data();
$lang = new Language('english');
$lang->load('attendance_messages');
$ignore_subject = false;

if(preg_match("/\[([a-zA-Z0-9]+)\]$/", $data['subject'], $subject) || $ignore_subject == true) {
/* Check if reply is possiblity auto-responder */
if(strstr(strtolower($data['subject']), 'auto')) {
	exit;
}
//$request_key = '4418fb4a4e';
$request_key = $db->escape_string($subject[1]);
$attendanceadddays = new AttendanceAddDays(array('identifier' => $request_key));

$adddays_data = $attendanceadddays->get();

$attendanceadddays->approve($request_key, $adddays_data['uid'], 'zaher.reda@orkila.com');

//notify approve user
 $attendanceadddays->notifyApprove($request_key,$adddays_data['uid']);
 


}
?>