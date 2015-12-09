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
if($_REQUEST['authkey'] == 'kia5ravb$op09dj4a!xhegalhj') {
    $lang = new Language('english', 'user');
    $lang->load('global');
    $lang->load('attendance_meta');

    $allactiveusers = Users::get_data('gid !=7', array('returnarray' => true));
    if(is_array($allactiveusers)) {
        $core->input['output'] = 'email';
        $core->input['action'] = 'do_generatereport';
        $core->input['referrer'] = 'log';
        $core->input['fromDate'] = mktime(0, 0, 0, date('n', strtotime('-1 month')), 1);
        $core->input['toDate'] = mktime(23, 59, 0, date('n', strtotime('-1 month')), date("t", strtotime('-1 month')));
        foreach($allactiveusers as $user) {
            $core->user['mainaffiliate'] = $user->get_mainaffiliate()->affid;
            $core->user['hraffids'] = AffiliatedEmployees::get_column('affid', array('canHr' => 1, 'uid' => $user->uid), array('returnarray' => true));
            $group = new UserGroups($user->gid);
            $core->usergroup['hr_canHrAllAffiliates'] = $group->hr_canHrAllAffiliates;
            $uids = $user->get_hruserpermissions();
            if(is_array($uids)) {
                $core->input['uid'] = $uids;
                array_unshift($core->input['uid'], $user->uid);
            }
            else {
                $core->input['uid'][] = $user->uid;
            }
            $core->input['emailto'] = $user->email;
            parse_attendance_reports($core);
        }
    }
}
?>