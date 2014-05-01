#!/usr/bin/php -q
<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Pipe to approve additional days
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

if(preg_match("/\[([a-zA-Z0-9]+)\]$/", $data['subject'], $subject)) {
    /* Check if reply is possibly auto-responder */
    if(strstr(strtolower($data['subject']), 'auto')) {
        exit;
    }

    $request_key = $db->escape_string($subject[1]);
    $attendanceadddays = new AttendanceAddDays(array('identifier' => $request_key));

    $attendanceadddays->approve($data['from']);

    //notify approve user
    $attendanceadddays->update_leavestats();
    $attendanceadddays->notifyapprove();
}
?>