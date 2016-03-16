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
        $report['fromdate_output'] = date($core->settings['dateformat'], $core->input['fromDate']);
        $report['todate_output'] = date($core->settings['dateformat'], $core->input['toDate']);
        foreach($allactiveusers as $user) {
            $core->user['mainaffiliate'] = $user->get_mainaffiliate()->affid;
            $core->user['hraffids'] = AffiliatedEmployees::get_column('affid', array('canHr' => 1, 'uid' => $user->uid), array('returnarray' => true));
            $group = new UserGroups($user->gid);
            $core->usergroup['hr_canHrAllAffiliates'] = $group->hr_canHrAllAffiliates;
            $uids = $user->get_hruserpermissions_byaffid();
            $core->input['emailto'] = $user->email;
            if(is_array($uids)) {
                foreach($uids as $affid => $user_ids) {
                    if(!is_array($user_ids)) {
                        continue;
                    }
                    $core->input['uid'] = $user_ids;
                    $affiliate_obj = new Affiliates(intval($affid));
                    $affiliate_output = $affiliate_obj->get_displayname();
                    array_unshift($core->input['uid'], $user->uid);
                    $output = parse_attendance_reports($core);
                    $message .= "<h1>{$lang->attendancelogfor} {$affiliate_output}
                <small><br />{$lang->fromdate} {$report[fromdate_output]} {$lang->todate} {$report[todate_output]}</small>
            </h1>
            <span> < : {$lang->arrivearly} | > : {$lang->leavelater} | <> : {$lang->earlyandlate} | H: {$lang->holiday} | W/E : {$lang->weekend} | L : {$lang->leave} | UL : {$lang->unpaidleave}</span>
            </hr>
            <div>
                {$output}
            </div><hr><br>";
                    unset($output);
                }
                $email_data = array(
                        'from_email' => $core->settings['maileremail'],
                        'from' => 'OCOS Mailer',
                        'subject' => 'Monthly Attendance Log',
                        'message' => $message,
                        'to' => $core->input['emailto'],
                );

                $mail = new Mailer($email_data, 'php');
                if($mail->get_status() === true) {
                    $log->record($lang->monthlyattendancelog, $email_data['to']);
                }
                unset($message);
            }
            else {
                $core->input['uid'][] = $user->uid;
                $output = parse_attendance_reports($core);
                $message .= "<h1>{$lang->attendancelogfor} {$affiliate_output}
                <small><br />{$lang->fromdate} {$report[fromdate_output]} {$lang->todate} {$report[todate_output]}</small>
            </h1>
            <span> < : {$lang->arrivearly} | > : {$lang->leavelater} | <> : {$lang->earlyandlate} | H: {$lang->holiday} | W/E : {$lang->weekend} | L : {$lang->leave} | UL : {$lang->unpaidleave}</span>
            </hr>
            <div>
                {$output}
            </div><hr>";
                $email_data = array(
                        'from_email' => $core->settings['maileremail'],
                        'from' => 'OCOS Mailer',
                        'subject' => 'Monthly Attendance Log',
                        'message' => $message,
                        'to' => $core->input['emailto'],
                );

                $mail = new Mailer($email_data, 'php');
                if($mail->get_status() === true) {
                    $log->record($lang->monthlyattendancelog, $email_data['to']);
                }
            }
        }
    }
}
?>