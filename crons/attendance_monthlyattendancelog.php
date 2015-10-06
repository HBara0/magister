<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: attendanc_monthlyattendancelog.php
 * Created:        @hussein.barakat    Oct 6, 2015 | 12:47:44 PM
 * Last Update:    @hussein.barakat    Oct 6, 2015 | 12:47:44 PM
 */


require '../inc/init.php';
require '../inc/attendance_functions.php';

$lang = new Language('english', 'user');
$lang->load('global');
$lang->load('attendance_meta');

$allactiveusers = Users::get_data('gid !=7', array('returnarray' => true));
if(is_array($allactiveusers)) {
    $core->input['output'] = 'email';
    $core->input['action'] = 'do_generatereport';
    $core->input['referrer'] = 'log';
    $core->input['fromDate'] = strtotime("first day of previous month");
    $core->input['toDate'] = strtotime("last day of previous month");
    foreach($allactiveusers as $user) {
        $permissions = $user->get_businesspermissions();
        if(is_array($permissions['uid'])) {
            $core->input['uid'] = $permissions['uid'];
        }
        else {
            $core->input['uid'][] = $user->uid;
        }
        $core->input['emailto'] = $user->email;
        parse_attendance_reports($core);
    }
}
?>