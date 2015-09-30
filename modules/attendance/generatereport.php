<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2012 Orkila International Offshore, All Rights Reserved
 * Generate a report for preview and export
 * $module: attendance
 * Created		@tony.assaad 		April 03, 2012 | 5:00 PM
 * Last Update: 	@zaher.reda			May 09, 2012 | 12:02 AM
 */
if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['attendance_canGenerateReport'] == 0) {
    error($lang->sectionnopermission);
}

$session->start_phpsession();
/* Temporary specific fix for time zone */
date_default_timezone_set($core->user_obj->get_mainaffiliate()->get_country()->defaultTimeZone);

if(!$core->input['action']) {
    $filter_where = '';
    if($core->usergroup['attendance_canViewAllAttendance'] != 1) {
        $filter_where = '(uid = '.$core->user['uid'].' OR reportsTo = '.$core->user['uid'].')';
    }
    else {
        if(is_array($core->user['affiliates'])) {
            $users_where = 'affid IN ('.implode(',', $core->user['affiliates']).')';
        }
        else {
            $users_where = 'isMain = 1 AND affid = '.$core->user['mainaffiliate'];
        }
        $users = get_specificdata('affiliatedemployees', array('uid'), 'uid', 'uid', '', 0, $users_where);
        $filter_where = 'uid IN ('.implode(',', $users).')';
    }

    $users = Users::get_data($filter_where, array('order' => array('by' => 'displayname', 'sort' => 'ASC'), 'returnarray' => true));
    if(is_array($users)) {
        foreach($users as $user) {
            $aff_output = $aff_output_name = '';
            $affiliate = $user->get_mainaffiliate();
            if(is_object($affiliate) && !empty($affiliate->affid)) {
                $aff_output_name = $affiliate->get_displayname();
            }
            $aff_output = '<td width:="40%">'.$aff_output_name.'</td>';
            $users_list .= ' <tr class="'.$rowclass.'">';
            $users_list .= '<td width="50%"><input style="width:5%;" id="usersfilter_check_'.$user->uid.'" type="checkbox" value="'.$user->uid.'" name="uid[]"><div style="width:90%;display:inline-block;margin-left:5px;">'.$user->get_displayname().'</div></td>'
                    .$aff_output.'</tr>';
        }
    }
    eval("\$generatepage = \"".$template->get('attendance_generatereport')."\";");
    output_page($generatepage);
}
else {
    if($core->input['action'] == 'do_generatereport') {
        if(is_empty($core->input['fromDate'], $core->input['uid'])) {
            error($lang->invalidtodate, 'index.php?module=attendance/generatereport', false);
        }

        if(empty($core->input['toDate'])) {
            $core->input['toDate'] = date('d-m-y', TIME_NOW);
        }

        $fromdate_object = new DateTime($core->input['fromDate'].' 00:00:00');
        $todate_object = new DateTime($core->input['toDate'].' 23:59:59');

        $fromdate = $fromdate_object->getTimestamp();
        $todate = $todate_object->getTimestamp();

        if($fromdate > $todate) {
            error($lang->invalidtodate, 'index.php?module=attendance/generatereport', false);
        }

        if($fromdate > TIME_NOW) {
            $fromdate = strtotime('today midnight');
        }

        $report['fromdate_output'] = date($core->settings['dateformat'], $fromdate);
        $report['todate_output'] = date($core->settings['dateformat'], $todate);
        $to = $todate;
        foreach($core->input['uid'] as $uid) {
            unset($attendance_report_user[$uid], $workshift_output);
            $uid = intval($uid);
            $user_obj = new Users($uid);
            $attending_days = $total_days = $weekends = 0;
            if(is_object($user_obj)) {
                $currentdate = $fromdate;
                $fromdate_details = getdate($fromdate);
                $currentdate_details = getdate($currentdate);
                $todate_details = getdate($todate);

                $joindate = $user_obj->get_joindate();
                if($joindate) {
                    if($fromdate < $joindate) {
                        $currentdate = $joindate;
                        $fromdate_details = getdate($joindate);
                        $currentdate_details = getdate($currentdate);
                    }
                }
            }
            $user_display = $user_obj->get_displayname(); //$db->fetch_field($db->query("SELECT displayName FROM ".Tprefix."users WHERE uid='{$uid}'"), 'displayName');

            /* Check for holidays in period - START */
            $holiday_todate_details = $todate_details;

            /* If multiple years, make end month as 12 to include all */
            $holidays_query_where = parse_holidayswhere($fromdate_details, $todate_details);

            $holiday_query = $db->query("SELECT *
                                        FROM ".Tprefix."holidays
                                        WHERE affid = {$core->user[mainaffiliate]} AND ({$holidays_query_where})
                                        AND hid NOT IN (SELECT hid FROM ".Tprefix."holidaysexceptions WHERE uid={$uid})");

            while($holiday = $db->fetch_assoc($holiday_query)) {
                if($holiday['year'] == 0) {
                    if($todate_details['year'] != $currentdate_details['year']) {
                        for($year = $currentdate_details['year']; $year <= $todate_details['year']; $year++) {
                            $holiday['year'] = $year;
                            parse_holiday($holiday, $data);
                        }
                    }
                    else {
                        $holiday['year'] = $currentdate_details['year'];
                        parse_holiday($holiday, $data);
                    }
                }
                else {
                    parse_holiday($holiday, $data);
                }
            }
            /* Check for holidays in period - END */

            /* Check for the Worshifts during period - START */
            $shifts_query = $db->query("SELECT w.*, e.fromDate, e.toDate
										FROM ".Tprefix."workshifts w
										JOIN ".Tprefix."employeesshifts e ON (w.wsid=e.wsid)
										WHERE e.uid='{$uid}' AND (({$fromdate} BETWEEN e.fromDate AND e.toDate) OR ({$todate} BETWEEN e.fromDate AND e.toDate))");

            while($workshift = $db->fetch_assoc($shifts_query)) {
                /* 				$workshift_date = getdate($workshift['fromDate']);
                  $workshift_date['week']  = date('W', $workshift['fromDate']);

                  $worshift_year  = date('Y',$shift['fromDate']);
                  $worshift_month = date('m',$shift['fromDate']);
                  $worshift_week  = ltrim(date('W', $shift['fromDate']), '0');
                  $worshift_day   = date('d', $shift['fromDate']); */

                $worshifts[$workshift['wsid']] = $workshift;
                $worshifts[$workshift['wsid']]['hoursperday'] = ((60 * 60 * $shift['offDutyHour']) + (60 * $shift['offDutyMinutes'])) - (( 60 * 60 * $shift['onDutyHour']) + (60 * $shift['onDutyMinutes']));
            }
            /* Check for the Worshifts during period - END */

            /* Check for APPROVED leaves within the period - START */
            $approved_lids = $unapproved_lids = array();
            $leave_query = $db->query("SELECT l.lid, fromDate, toDate, type, title
										FROM ".Tprefix."leaves l
										JOIN ".Tprefix."leavetypes lt ON (lt.ltid = l.type)
										WHERE ((l.fromDate BETWEEN {$fromdate} AND {$todate}) OR (l.toDate BETWEEN {$fromdate} AND {$todate})) AND l.uid = {$uid}
										GROUP BY l.lid");
            while($leave = $db->fetch_assoc($leave_query)) {
                if(value_exists('leavesapproval', 'isApproved', 0, "(lid={$leave[lid]})")) {
                    continue;
                }
                $approved_lids[$leave['lid']] = $leave;
                $num_days_off = ($leave['toDate'] - $leave['fromDate']) / 24 / 60 / 60;

                if($num_days_off == 1) {
                    $leave_date = getdate_custom($leave['fromDate']);
                    if($leave_date['week'] == 1 && $leave_date['mon'] == 12) {
                        $leave_date['week'] = 53;
                    }
                    $data[$leave_date['year']][$leave_date['mon']][$leave_date['week']][$leave_date['mday']]['leaves'][$leave['lid']] = $leave;
                }
                else {
                    for($i = 0; $i < $num_days_off; $i++) {
                        $leave_date = getdate_custom($leave['fromDate'] + (60 * 60 * 24 * $i));
                        if($leave_date['week'] == 1 && $leave_date['mon'] == 12) {
                            $leave_date['week'] = 53;
                        }
                        $data[$leave_date['year']][$leave_date['mon']][$leave_date['week']][$leave_date['mday']]['leaves'][$leave['lid']] = $leave;
                    }
                }
            }
            /* Check for APPROVED leaves within the period - END */

            /* Check for the attendance during the period - START */
            $attendance_query = $db->query("SELECT a.*, CONCAT(firstName, ' ', lastName) AS fullname
											FROM ".Tprefix."attendance_attrecords a
											JOIN ".Tprefix."users u ON (a.uid = u.uid)
											WHERE (time BETWEEN '{$fromdate}' AND '{$todate}') AND a.uid={$uid}
											ORDER BY time ASC");

            if($db->num_rows($attendance_query) > 0) {
                // $daycount = 1;
                while($attendance = $db->fetch_assoc($attendance_query)) {
                    $attendance_date = getdate_custom($attendance['time']);
                    $daycount = $attendance_date['year'].$attendance_date['mon'].$attendance_date['week'].$attendance_date['mday'];
                    if($attendance_date['week'] == 1 && $attendance_date['mon'] == 12) {
                        $attendance_date['week'] = 53;
                    }
                    if(!isset($data[$attendance_date['year']][$attendance_date['mon']][$attendance_date['week']][$attendance_date['mday']]['attendance'][$daycount])) {
                        $data[$attendance_date['year']][$attendance_date['mon']][$attendance_date['week']][$attendance_date['mday']]['attendance'][$daycount] = $attendance;
                        $data[$attendance_date['year']][$attendance_date['mon']][$attendance_date['week']][$attendance_date['mday']]['attendance'][$daycount]['date'] = $attendance['time'];
                    }
                    if($attendance['operation'] == 'check-in') {
                        if(empty($data[$attendance_date['year']][$attendance_date['mon']][$attendance_date['week']][$attendance_date['mday']]['attendance'][$daycount]['timeIn'])) {
                            $data[$attendance_date['year']][$attendance_date['mon']][$attendance_date['week']][$attendance_date['mday']]['attendance'][$daycount]['timeIn'] = $attendance['time'];
                        }
                        //$data[$attendance_date['year']][$attendance_date['mon']][$attendance_date['week']][$attendance_date['mday']]['attendance'][$daycount]['timeIn'] = $attendance['time'];
                        //if(!empty($data[$attendance_date['year']][$attendance_date['mon']][$attendance_date['week']][$attendance_date['mday']]['attendance'][$daycount]['timeOut'])) {
                        // $daycount++;
                        //}
                    }
                    elseif($attendance['operation'] == 'check-out') {
                        $data[$attendance_date['year']][$attendance_date['mon']][$attendance_date['week']][$attendance_date['mday']]['attendance'][$daycount]['timeOut'] = $attendance['time'];

                        // if(!empty($data[$attendance_date['year']][$attendance_date['mon']][$attendance_date['week']][$attendance_date['mday']]['attendance'][$daycount]['timeIn'])) {
                        //$daycount++;
                        //}
                    }
                }
            }
            /* else
              {
              $attendance_report .= '<tr><td colspan="6" align="center" class="subtitle">'.$lang->noattendanceavailable.'</td></tr>';
              continue;
              } */

            /* Check for the attendance during the period - END */
            if($core->input['referrer'] == 'report') {

                /* Loop over all days of period - START */
                while($currentdate <= $to) {
                    $curdate = getdate_custom($currentdate);
                    if($curdate['week'] == 1 && $curdate['mon'] == 12) {
                        $curdate['week'] = 53;
                    }
                    /* Loop Through the Worshifts - START */
                    if(is_array($worshifts)) {
                        foreach($worshifts as $key => $workshift) {
                            /* check if the dates of Worshifts between current date and to date - START */
                            $workshift['weekDays'] = unserialize($workshift['weekDays']);
                            if($currentdate >= $workshift['fromDate'] && $currentdate <= $workshift['toDate']) { // && in_array($day, $week_days)
                                $current_worshift = $worshifts[$key];
                                $workshift_output .= $workshift['weekDays'];
                                break; //Used to be continue
                            }
                        }
                    }
                    /* Loop Through the Worshifts - END */
                    if(isset($data[$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']])) {
                        foreach($data[$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] as $type => $day_data) {
                            $rowclass = '';
                            if($type == 'attendance') {
                                /* Check if leaves exist while attendance exists too & adjust accordingly - START */
                                if(isset($data[$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']]['leaves'])) {
                                    foreach($data[$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']]['leaves'] as $leave) {
                                        $leavetype_obj = new LeaveTypes($leave['type'], false);
                                        $leavetype = $leavetype_obj->get();
                                        if($leavetype['isWholeDay'] == 0) {
                                            $dutytime = (($current_worshift['offDutyHour'] * 60 * 60) + ($current_worshift['offDutyMinutes'] * 60)) - (($current_worshift['onDutyHour'] * 60 * 60) + ($current_worshift['onDutyMinutes'] * 60));
                                            if($leavetype['isPM'] == 1) {
                                                $leavefrom = (($current_worshift['offDutyHour'] * 60 * 60) + ($current_worshift['offDutyMinutes'] * 60)) - ($dutytime / 2) + (45 * 60);

                                                $current_worshift['onDutyHour'] = date('H', $leavefrom);
                                                $current_worshift['onDutyMinutes'] = date('i', $leavefrom);
                                            }
                                            else {
                                                $leaveto = (($current_worshift['onDutyHour'] * 60 * 60) + ($current_worshift['onDutyMinutes'] * 60)) + ($dutytime / 2) - (45 * 60);

                                                $current_worshift['offDutyHour'] = date('H', $leaveto);
                                                $current_worshift['offDutyMinutes'] = date('i', $leaveto);
                                            }
                                        }
                                    }
                                }
                                unset($leaveto, $leavefrom, $leavetype_obj, $leavetype);
                                /* Check if leaves exist while attendance exists too & adjust accordingly - END */
                                foreach($day_data as $attendance) {
                                    /* If person has not recorded entry use workshift default - START */
                                    if(empty($attendance['timeIn']) || !isset($attendance['timeIn'])) {
                                        $attendance['timeIn'] = mktime($current_worshift['onDutyHour'], $current_worshift['onDutyMinutes'], 0, $curdate['mon'], $curdate['mday'], $curdate['year']);
                                    }

                                    if(empty($attendance['timeOut']) || !isset($attendance['timeOut'])) {
                                        $attendance['timeOut'] = mktime($current_worshift['offDutyHour'], $current_worshift['offDutyMinutes'], 0, $curdate['mon'], $curdate['mday'], $curdate['year']);
                                    }
                                    /* If person has not recorded entry use workshift default - START */

                                    $attendance['timeIn_output'] = date($core->settings['timeformat'], $attendance['timeIn']);
                                    $attendance['timeOut_output'] = date($core->settings['timeformat'], $attendance['timeOut']);
                                    $attendance['date_output'] = date($core->settings['dateformat'], $attendance['date']);

                                    /* Check earlier arrival and latest departure - START */
                                    if(!isset($stats['earliest_arrival'])) {
                                        $stats['earliest_arrival'] = $attendance['timeIn'];
                                        $stats['earliest_arrival_output'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $stats['earliest_arrival']);
                                    }
                                    else {
                                        if(strtotime(date('G:i:s', $attendance['timeIn'])) < strtotime(date('G:i:s', $stats['earliest_arrival']))) {
                                            $stats['earliest_arrival'] = $attendance['timeIn'];
                                            $stats['earliest_arrival_output'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $stats['earliest_arrival']);
                                        }
                                    }

                                    if(!isset($stats['latest_departure'])) {
                                        $stats['latest_departure'] = $attendance['timeOut'];
                                        $stats['latest_departure_output'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $stats['latest_departure']);
                                    }
                                    else {
                                        if(strtotime(date('G:i:s', $attendance['timeOut'])) > strtotime(date('G:i:s', $stats['latest_departure']))) {
                                            $stats['latest_departure'] = $attendance['timeOut'];
                                            $stats['latest_departure_output'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $stats['latest_departure']);
                                        }
                                    }
                                    /* Check earlier arrival and latest departure - END */

                                    $attendance['hoursday'] = ($attendance['timeOut']) - ($attendance['timeIn']);
                                    $attendance['arrival'] = $attendance['timeIn'] - (mktime($current_worshift['onDutyHour'], $current_worshift['onDutyMinutes'], 0, $curdate['mon'], $curdate['mday'], $curdate['year']));
                                    $attendance['departure'] = $attendance['timeOut'] - (mktime($current_worshift['offDutyHour'], $current_worshift['offDutyMinutes'], 0, $curdate['mon'], $curdate['mday'], $curdate['year']));

                                    $attendance['deviation'] = $attendance['departure'] - $attendance['arrival'];

                                    $attendance['hoursday_output'] = operation_time_value($attendance['hoursday']);
                                    $attendance['arrival_output'] = operation_time_value($attendance['arrival']);
                                    $attendance['departure_output'] = operation_time_value($attendance['departure']);
                                    $attendance['deviation_output'] = operation_time_value($attendance['deviation']);

                                    eval("\$attendance_report_user_day .= \"".$template->get('attendance_report_user_month_week_day')."\";");
                                    $total['actualhours'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] = $attendance['hoursday'];
                                    $total['deviation'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] = $attendance['deviation'];
                                }
                                $total['requiredhours'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] = (($current_worshift['offDutyHour'] * 3600) + ($current_worshift['offDutyMinutes'] * 60)) - (($current_worshift['onDutyHour'] * 3600) + ($current_worshift['onDutyMinutes'] * 60));
                            }

                            if($type == 'leaves') {
                                $day_content = '';
                                foreach($day_data as $leave) {
                                    $day_content .= date('l '.$core->settings['dateformat'], $currentdate).' '.$leave['title'].'<br />';
                                }

                                /* check whether the day is not in weekend and not in holiday */
                                if(in_array($curdate['wdayiso'], $workshift['weekDays']) && !isset($data[$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']]['holiday'])) {
                                    $total['count_leaves'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] ++;
                                }

                                eval("\$attendance_report_user_day .= \"".$template->get('attendance_report_user_month_week_genday')."\";");
                            }

                            if($type == 'holiday') {
                                $day_content = '';
                                foreach($day_data as $holiday) {
                                    $day_content .= date('l '.$core->settings['dateformat'], $currentdate).' '.$holiday['title'].'<br />';
                                }

                                if(is_array($worshifts)) {
                                    if(in_array($curdate['wdayiso'], $workshift['weekDays'])) {
                                        $total['holidays'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] ++;
                                    }
                                }

                                eval("\$attendance_report_user_day .= \"".$template->get('attendance_report_user_month_week_genday')."\";");
                            }
                        }
                    }
                    else {
                        $rowclass = '';
                        if(in_array($curdate['wdayiso'], $workshift['weekDays'])) {
                            $rowclass = 'unapproved';
                            $day_content = date('l '.$core->settings['dateformat'], $currentdate).': Absent';

                            $total['absent'][$curdate['year']][$curdate['mon']][$curdate['week']] ++;
                            $total['requiredhours'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] = (($current_worshift['offDutyHour'] * 3600) + ($current_worshift['offDutyMinutes'] * 60)) - (($current_worshift['onDutyHour'] * 3600) + ($current_worshift['onDutyMinutes'] * 60));
                        }
                        else {
                            $total['weekends'][$curdate['year']][$curdate['mon']][$curdate['week']] ++;
                            $day_content = date('l '.$core->settings['dateformat'], $currentdate).': Weekend';
                        }
                        eval("\$attendance_report_user_day .= \"".$template->get('attendance_report_user_month_week_genday')."\";");
                    }

                    //$count_hours_week[$week] = array_sum_recursive($total['actual'][$year][$month][$week]);//($total['hoursweek'][$year][$month][$week]/3600);
                    /* Get prev/next day for verification */
                    $nextdate = $currentdate + 86400;
                    $nextdate_details = getdate_custom($nextdate);
                    $prevdate_details = getdate_custom($prevdate);

                    /* Parse month and week sections - START */

                    if($prevdate_details['week'] == 1 && $prevdate_details['mon'] == 12) {
                        $prevdate_details['week'] = 53;
                    }

                    if($nextdate_details['week'] == 1 && $nextdate_details['mon'] == 12) {
                        $nextdate_details['week'] = 53;
                    }

                    if($curdate['week'] == 1 && $curdate['mon'] == 12) {
                        $curdate['week'] = 53;
                    }
                    if($nextdate_details['week'] != $curdate['week'] && $nextdate_details['mon'] == $curdate['mon']) {
                        $total_outputs['week']['actualhours'] = operation_time_value(array_sum_recursive($total['actualhours'][$curdate['year']][$curdate['mon']][$curdate['week']]));
                        $total_outputs['week']['requiredhours'] = operation_time_value(array_sum_recursive($total['requiredhours'][$curdate['year']][$curdate['mon']][$curdate['week']]));

                        eval("\$attendance_report_user_week .= \"".$template->get('attendance_report_user_week')."\";");

                        if($nextdate >= $to) {
                            $total_outputs['month']['actualhours'] = operation_time_value(array_sum_recursive($total['actualhours'][$curdate['year']][$curdate['mon']]));
                            $total_outputs['month']['requiredhours'] = operation_time_value(array_sum_recursive($total['requiredhours'][$curdate['year']][$curdate['mon']]));

                            eval("\$attendance_report_user_month .= \"".$template->get('attendance_report_user_month')."\";");
                        }
                        $attendance_report_user_day = '';
                    }
                    elseif($nextdate_details['week'] == $curdate['week'] && $nextdate_details['mon'] != $curdate['mon']) {
                        //$curdate['mon'] = $nextdate_details['mon'];

                        $total_outputs['week']['actualhours'] = operation_time_value(array_sum_recursive($total['actualhours'][$curdate['year']][$curdate['mon']][$curdate['week']]));
                        $total_outputs['week']['requiredhours'] = operation_time_value(array_sum_recursive($total['requiredhours'][$curdate['year']][$curdate['mon']][$curdate['week']]));

                        eval("\$attendance_report_user_week .= \"".$template->get('attendance_report_user_week')."\";");
                        $total_outputs['month']['actualhours'] = operation_time_value(array_sum_recursive($total['actualhours'][$curdate['year']][$curdate['mon']]));
                        $total_outputs['month']['requiredhours'] = operation_time_value(array_sum_recursive($total['requiredhours'][$curdate['year']][$curdate['mon']]));

                        //$attendance_report_user_month .= 'a'.$nextdate_details['week'].' == '.$curdate['week'].' && '.$nextdate_details['mon'].' != '.$curdate['mon'];

                        eval("\$attendance_report_user_month .= \"".$template->get('attendance_report_user_month')."\";");
                        $attendance_report_user_week = '';
                        $attendance_report_user_day = '';
                    }
//                elseif($prevdate_details['week'] == $curdate['week'] && $prevdate_details['mon'] != $curdate['mon']) {
//                    //$curdate['mon'] = $prevdate_details['mon'];
//
//                    $total_outputs['week']['actualhours'] = operation_time_value(array_sum_recursive($total['actualhours'][$curdate['year']][$curdate['mon']][$curdate['week']]));
//                    $total_outputs['week']['requiredhours'] = operation_time_value(array_sum_recursive($total['requiredhours'][$curdate['year']][$curdate['mon']][$curdate['week']]));
//
//                    eval("\$attendance_report_user_week .= \"".$template->get('attendance_report_user_week')."\";");
//
//                    $total_outputs['month']['actualhours'] = operation_time_value(array_sum_recursive($total['actualhours'][$curdate['year']][$curdate['mon']]));
//                    $total_outputs['month']['requiredhours'] = operation_time_value(array_sum_recursive($total['requiredhours'][$curdate['year']][$curdate['mon']]));
//
//                    eval("\$attendance_report_user_month .= \"".$template->get('attendance_report_user_month')."\";");
//                    $attendance_report_user_day = '';
//                    $attendance_report_user_week = '';
//                }
                    elseif($nextdate_details['week'] != $curdate['week'] && $nextdate_details['mon'] != $curdate['mon']) {
                        $total['week']['actualhours'] = array_sum_recursive($total['actualhours'][$curdate['year']][$curdate['mon']][$curdate['week']]);
                        $total['hoursweek'][$year][$month][$week] = array_sum_recursive($total['hoursday']);

                        $total_outputs['week']['actualhours'] = operation_time_value(array_sum_recursive($total['actualhours'][$curdate['year']][$curdate['mon']][$curdate['week']]));
                        $total_outputs['week']['requiredhours'] = operation_time_value(array_sum_recursive($total['requiredhours'][$curdate['year']][$curdate['mon']][$curdate['week']]));

                        eval("\$attendance_report_user_week .= \"".$template->get('attendance_report_user_week')."\";");

                        $total_outputs['month']['actualhours'] = operation_time_value(array_sum_recursive($total['actualhours'][$curdate['year']][$curdate['mon']]));
                        $total_outputs['month']['requiredhours'] = operation_time_value(array_sum_recursive($total['requiredhours'][$curdate['year']][$curdate['mon']]));

                        eval("\$attendance_report_user_month .= \"".$template->get('attendance_report_user_month')."\";");
                        $attendance_report_user_week = '';
                        $attendance_report_user_day = '';
                    }
                    elseif($nextdate_details['week'] == $curdate['week'] && $nextdate_details['mon'] == $curdate['mon']) {
                        if($currentdate >= $to) {/* FIX HERE  */
                            $total_outputs['week']['actualhours'] = operation_time_value(array_sum_recursive($total['actualhours'][$curdate['year']][$curdate['mon']][$curdate['week']]));
                            $total_outputs['week']['requiredhours'] = operation_time_value(array_sum_recursive($total['requiredhours'][$curdate['year']][$curdate['mon']][$curdate['week']]));

                            eval("\$attendance_report_user_week .= \"".$template->get('attendance_report_user_week')."\";");



                            $total_outputs['month']['actualhours'] = operation_time_value(array_sum_recursive($total['actualhours'][$curdate['year']][$curdate['mon']]));
                            $total_outputs['month']['requiredhours'] = operation_time_value(array_sum_recursive($total['requiredhours'][$curdate['year']][$curdate['mon']]));

                            eval("\$attendance_report_user_month .= \"".$template->get('attendance_report_user_month')."\";");
                            $attendance_report_user_week = '';
                            $attendance_report_user_day = '';
                        }
                    }
                    /* Parse month and week sections - END */
                    $prevdate = $currentdate;
                    $currentdate += 86400;/** increment  by one day (timestamp) * */
                    $total['period_days'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] ++;
                }

                $overall_totals['actualhours'] = array_sum_recursive($total['actualhours']);
                $overall_totals['count_absent_days'] = array_sum_recursive($total['absent']);
                $overall_totals['count_all_holidays'] = array_sum_recursive($total['holidays']);
                $overall_totals['count_all_weekends'] = array_sum_recursive($total['weekends']);
                $overall_totals['count_all_leaves'] = array_sum_recursive($total['count_leaves']);
                $total_all_holidays = ($overall_totals['count_all_holidays']) + ($overall_totals['count_all_leaves']) + ($overall_totals['count_all_weekends']);

                $overall_totals['total_period_days'] = array_sum_recursive($total['period_days']);
                $overall_totals['deviation'] = operation_time_value(array_sum_recursive($total['deviation']));
                $required_days = $overall_totals['total_period_days'] - $total_all_holidays;
                $overall_totals['actual_working_days'] = $required_days - $overall_totals['count_absent_days'];

                $overall_totals['average_hour_day'] = $overall_totals['workpercentage'] = 0;
                if(!empty($overall_totals['actual_working_days'])) {
                    $overall_totals['average_hour_day'] = $overall_totals['actualhours'] / $overall_totals['actual_working_days'];

                    $overall_totals['workpercentage'] = round(($overall_totals['actualhours'] * 100) / array_sum_recursive($total['requiredhours']), 2);
                    $overall_totals['average_hour_day'] = operation_time_value($overall_totals['average_hour_day']);
                }

                $overall_totals['actualhours'] = operation_time_value($overall_totals['actualhours']);
                /* Loop over all days of period - END */

                //$attendance_report = $attendance_report_user_month[$year];
                eval("\$attendance_report .= \"".$template->get('attendance_report_user')."\";");
                $attendance_report_user_month = '';
            }
            elseif($core->input['referrer'] == 'log') {
                while($currentdate <= $to) {
                    $curdate = getdate_custom($currentdate);
                    if($curdate['week'] == 1 && $curdate['mon'] == 12) {
                        $curdate['week'] = 53;
                    }
                    /* Loop Through the Worshifts - START */
                    if(is_array($worshifts)) {
                        foreach($worshifts as $key => $workshift) {
                            /* check if the dates of Worshifts between current date and to date - START */
                            $workshift['weekDays'] = unserialize($workshift['weekDays']);
                            if($currentdate >= $workshift['fromDate'] && $currentdate <= $workshift['toDate']) { // && in_array($day, $week_days)
                                $current_worshift = $worshifts[$key];
                                $workshift_output .= $workshift['weekDays'];
                                break; //Used to be continue
                            }
                        }
                    }
                    /* Loop Through the Worshifts - END */
                    $total_days++;
                    if(isset($data[$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']])) {
                        $filled = '';
                        foreach($data[$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] as $type => $day_data) {
                            $rowclass = '';
                            if($type == 'attendance') {
                                if($filled == 1) {
                                    continue;
                                }
                                /* Check if leaves exist while attendance exists too & adjust accordingly - START */
                                if(isset($data[$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']]['leaves'])) {
                                    foreach($data[$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']]['leaves'] as $leave) {
                                        $leavetype_obj = new LeaveTypes($leave['type'], false);
                                        $leavetype = $leavetype_obj->get();
                                        if($leavetype['isWholeDay'] == 0) {
                                            $dutytime = (($current_worshift['offDutyHour'] * 60 * 60) + ($current_worshift['offDutyMinutes'] * 60)) - (($current_worshift['onDutyHour'] * 60 * 60) + ($current_worshift['onDutyMinutes'] * 60));
                                            if($leavetype['isPM'] == 1) {
                                                $leavefrom = (($current_worshift['offDutyHour'] * 60 * 60) + ($current_worshift['offDutyMinutes'] * 60)) - ($dutytime / 2) + (45 * 60);

                                                $current_worshift['onDutyHour'] = date('H', $leavefrom);
                                                $current_worshift['onDutyMinutes'] = date('i', $leavefrom);
                                            }
                                            else {
                                                $leaveto = (($current_worshift['onDutyHour'] * 60 * 60) + ($current_worshift['onDutyMinutes'] * 60)) + ($dutytime / 2) - (45 * 60);

                                                $current_worshift['offDutyHour'] = date('H', $leaveto);
                                                $current_worshift['offDutyMinutes'] = date('i', $leaveto);
                                            }
                                        }
                                    }
                                }
                                unset($leaveto, $leavefrom, $leavetype_obj, $leavetype);
                                /* Check if leaves exist while attendance exists too & adjust accordingly - END */
                                foreach($day_data as $attendance) {
                                    /* If person has not recorded entry use workshift default - START */
                                    if((empty($attendance['timeIn']) || !isset($attendance['timeIn']) ) && (empty($attendance['timeOut']) || !isset($attendance['timeOut']))) {
                                        $day_content_value .= '0%';
                                        $month_header[$curdate['mon']][date('d', $currentdate)] = date('d', $currentdate);
                                        $total['absent'][$curdate['year']][$curdate['mon']] ++;
                                        $total['requiredhours'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] = (($current_worshift['offDutyHour'] * 3600) + ($current_worshift['offDutyMinutes'] * 60)) - (($current_worshift['onDutyHour'] * 3600) + ($current_worshift['onDutyMinutes'] * 60));
                                    }
                                    elseif(empty($attendance['timeIn']) || !isset($attendance['timeIn']) || empty($attendance['timeOut']) || !isset($attendance['timeOut'])) {
                                        $day_content_value .= '?';
                                        $month_header[$curdate['mon']][date('d', $currentdate)] = date('d', $currentdate);
                                        $attending_days++;
                                        $total['requiredhours'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] = (($current_worshift['offDutyHour'] * 3600) + ($current_worshift['offDutyMinutes'] * 60)) - (($current_worshift['onDutyHour'] * 3600) + ($current_worshift['onDutyMinutes'] * 60));
                                    }
                                    else {
                                        /* LOG DATA */
                                        $attendance['date_output'] = date('d', $attendance['date']);
                                        $total_workshit = (mktime($current_worshift['offDutyHour'], $current_worshift['offDutyMinutes'], 0, $curdate['mon'], $curdate['mday'], $curdate['year'])) - (mktime($current_worshift['onDutyHour'], $current_worshift['onDutyMinutes'], 0, $curdate['mon'], $curdate['mday'], $curdate['year']));
                                        $attendance['hoursday'] = ($attendance['timeOut']) - ( $attendance['timeIn']);
                                        $attendance['arrival'] = $attendance['timeIn'] - (mktime($current_worshift['onDutyHour'], $current_worshift['onDutyMinutes'], 0, $curdate['mon'], $curdate['mday'], $curdate['year']));
                                        $attendance['departure'] = $attendance['timeOut'] - (mktime($current_worshift['offDutyHour'], $current_worshift['offDutyMinutes'], 0, $curdate['mon'], $curdate['mday'], $curdate['year']));
                                        $attendance['deviation'] = $attendance['departure'] - $attendance['arrival'];
                                        $workperc = ($attendance['hoursday'] / $total_workshit) * 100;

                                        if($attendance['arrival'] < 0) {
                                            $extra = '<';
                                        }
                                        if($attendance['departure'] > 0) {
                                            $extra .= '>';
                                        }
                                        if(number_format($workperc, 0) >= 100) {
                                            $day_content_value .= number_format($workperc, 0).'</br>'.$extra;
                                        }
                                        else {
                                            $day_content_value .= number_format($workperc, 0);
                                        }
                                        $month_header[$curdate['mon']][$attendance['date_output']] = $attendance['date_output'];
                                        $attending_days++;
                                        /* LOG DATA */
                                        $extra = '';
                                        $total['actualhours'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] = $attendance['hoursday'];
                                        $total['deviation'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] = $attendance['deviation'];
                                    }
                                }
                                $total['requiredhours'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] = (($current_worshift['offDutyHour'] * 3600) + ($current_worshift['offDutyMinutes'] * 60)) - (($current_worshift['onDutyHour'] * 3600) + ($current_worshift['onDutyMinutes'] * 60));
                            }

                            if($type == 'leaves') {
                                foreach($day_data as $leave) {
                                    $month_header[$curdate['mon']][date('d', $currentdate)] = date('d', $currentdate);
                                    $leave_obj = new Leaves($leave['lid']);

                                    if($leave_obj->get_leavetype(false)->isUnpaid == 1) {
                                        $day_content_value .= 'UL';
                                    }
                                    else {
                                        $day_content_value .= 'L';
                                    }
                                }
                                $filled = 1;
                                /* check whether the day is not in weekend and not in holiday */
                                if(in_array($curdate['wdayiso'], $workshift['weekDays']) && !isset($data[$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']]['holiday'])) {
                                    $total['count_leaves'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] ++;
                                }
                            }

                            if($type == 'holiday') {
                                foreach($day_data as $holiday) {
                                    $month_header[$curdate['mon']][date('d', $currentdate)] = date('d', $currentdate);
                                    $day_content_value .= 'H';
                                }
                                $filled = 1;
                                if(is_array($worshifts)) {
                                    if(in_array($curdate['wdayiso'], $workshift['weekDays'])) {
                                        $total['holidays'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] ++;
                                    }
                                }
                            }
                        }
                    }
                    else {
                        $rowclass = '';
                        if(in_array($curdate['wdayiso'], $workshift['weekDays'])) {
                            $day_content_value .= '0%';
                            $month_header[$curdate['mon']][date('d', $currentdate)] = date('d', $currentdate);
                            $total['absent'][$curdate['year']][$curdate['mon']] ++;
                            $total['requiredhours'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] = (($current_worshift['offDutyHour'] * 3600) + ($current_worshift['offDutyMinutes'] * 60)) - (($current_worshift['onDutyHour'] * 3600) + ($current_worshift['onDutyMinutes'] * 60));
                        }
                        else {
                            $weekends++;
                            $total['weekends'][$curdate['year']][$curdate['mon']][$curdate['week']] ++;
                            $day_content_value .= 'W/E';
                            $month_header[$curdate['mon']][date('d', $currentdate)] = date('d', $currentdate);
                        }
                    }

                    //$count_hours_week[$week] = array_sum_recursive($total['actual'][$year][$month][$week]);//($total['hoursweek'][$year][$month][$week]/3600);
                    /* Get prev/next day for verification */
                    $nextdate = $currentdate + 86400;
                    $nextdate_details = getdate_custom($nextdate);
                    $prevdate_details = getdate_custom($prevdate);

                    /* Parse month and week sections - START */

                    if($prevdate_details['week'] == 1 && $prevdate_details['mon'] == 12) {
                        $prevdate_details['week'] = 53;
                    }

                    if($nextdate_details['week'] == 1 && $nextdate_details['mon'] == 12) {
                        $nextdate_details['week'] = 53;
                    }

                    if($curdate['week'] == 1 && $curdate['mon'] == 12) {
                        $curdate['week'] = 53;
                    }
                    if(!isset($firstloop[$curdate['mon']])) {
                        $firstloop[$curdate['mon']] = 0;
                    }

                    $day_content .= '<td style="width:2%;text-align:center;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">'.$day_content_value.'</td>';
                    $day_content_value = '';
                    if($nextdate_details['mon'] != $curdate['mon'] || ($currentdate + 86400) >= $to || ($currentdate + 86400) > TIME_NOW) {
                        $month_output = date('F', mktime(0, 0, 0, $curdate['mon'], 10));
                        $total_outputs['month']['actualhours'] = operation_time_value(array_sum_recursive($total['actualhours'][$curdate['year']][$curdate['mon']]));
                        $total_outputs['month']['requiredhours'] = operation_time_value(array_sum_recursive($total['requiredhours'][$curdate['year']][$curdate['mon']]));

                        $day_content .= '<td style="width:50px;text-align:center;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">'.$attending_days.' / '.($total_days).'</td>';
                        $day_content .= '<td style="width:25px;text-align:center;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">'.number_format(($total_outputs['month']['actualhours'] / $total_outputs['month']['requiredhours']) * 100, 0).'</td>';
                        $day_content .= '<td style="width:85px;text-align:center;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">'.$total_outputs['month']['actualhours'].' / '.$total_outputs['month']['requiredhours'].'</td>';
                        //$attendance_report_user_month .= 'a'.$nextdate_details['week'].' == '.$curdate['week'].' && '.$nextdate_details['mon'].' != '.$curdate['mon'];

                        eval("\$attendance_report_users{{$curdate['mon']}} .= \"".$template->get('attendance_log_month_user')."\";");
                        $month_header_output = '<th style="width:150px;text-align:center;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">'.$lang->employeename.'</th><th style="border-left: 1px solid #000;border-right: 1px solid #000;">'.implode('</th><th style="border-left: 1px solid #000;border-right: 1px solid #000;">', $month_header[$curdate['mon']]).'</th>';
                        $month_header_output .= '<th style="width:50px;text-align:center;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">'.$lang->capstotal.'</th>';
                        $month_header_output .= '<th style="width:25px;text-align:center;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">%</th>';
                        $month_header_output .= '<th style="width:85px;text-align:center;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">'.$lang->capstotalhour.'</th>';

                        eval("\$attendance_report_user_month[{$curdate['mon']}] = \"".$template->get('attendance_log_month')."\";");
                        unset($month_header, $month_header_output, $total, $total_days, $attending_days, $weekends, $data[$curdate['year']][$curdate['mon']]);
                        $attendance_report_user_week = $attendance_report_user_day = $day_content = $extra = '';
                        if(($currentdate + 86400) > TIME_NOW) {
                            break;
                        }
                    }
                    /* Parse month and week sections - END */
                    $prevdate = $currentdate;
                    $currentdate += 86400;/** increment  by one day (timestamp) * */
                    $total['period_days'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] ++;
                }
            }
        }
        if($core->input['referrer'] == 'log') {
            if($attendance_report_user_month) {
                ksort($attendance_report_user_month);
                foreach($attendance_report_user_month as $html) {
                    $output.=$html;
                }
            }
            eval("\$generatepage = \"".$template->get('attendance_generatedlog')."\";");
        }
        else if($core->input['referrer'] == 'report') {
            eval("\$generatepage = \"".$template->get('attendance_report')."\";");
        }
        output_page($generatepage);
    }
}
function parse_holiday($holiday, &$data) {
    global $fromdate, $todate;

    if(!empty($holiday) && is_array($holiday)) {
        $holiday_timestamp = mktime(0, 0, 0, $holiday['month'], $holiday['day'], $holiday['year']);

        if($holiday_timestamp < $fromdate) {
            return;
        }

        if($holiday_timestamp > $todate) {
            return;
        }
        if($holiday['numDays'] > 1) {
            for($i = 0; $i <= $holiday['numDays']; $i++) {
                $holiday_timestamp = strtotime($holiday['day'].'+'.$i.' day');
                $holiday['day'] = date('d', $holiday_timestamp);
                $holiday['week'] = date('W', $holiday_timestamp);

                if($holiday['week'] == 1 && $holiday['month'] == 12) {
                    $holiday['week'] = 53;
                }

                $data[$holiday['year']][$holiday['month']][$holiday['week']][$holiday['day']]['holiday'][$holiday['hid']] = $holiday;
            }
        }
        else {
            $holiday['week'] = date('W', $holiday_timestamp);
            if($holiday['week'] == 1 && $holiday['month'] == 12) {
                $holiday['week'] = 53;
            }
            $data[$holiday['year']][$holiday['month']][$holiday['week']][$holiday['day']]['holiday'][$holiday['hid']] = $holiday;
        }
    }
}

function parse_holidayswhere($fromdate_details, $todate_details) {
    $where = '';
    if($todate_details['year'] != $fromdate_details['year']) {
        $year = $fromdate_details['year'];
        while($year <= $todate_details['year']) {
            if($year == $fromdate_details['year']) {
                $month_query = 'month >= '.$fromdate_details['mon'];
                $day_query = 'day >= '.$fromdate_details['mday'];
            }
            elseif($year == $todate_details['year']) {
                $month_query = 'month <= '.$todate_details['mon'];
                $day_query = 'day <= '.$todate_details['mday'];
            }
            else {
                $month_query = 'month BETWEEN 1 AND 12';
                $day_query = 'day BETWEEN 1 AND 31';
            }
            $where .= $or.'((year = 0 OR year = '.$year.') AND '.$month_query.' AND '.$day_query.')';
            $or = ' OR ';
            $year++;
        }
    }
    else { /* Same year */
        if($todate_details['mon'] != $fromdate_details['mon']) {
            $month = $fromdate_details['mon'];
            while($month <= $todate_details['mon']) {
                if($month == $fromdate_details['mon']) {
                    $day_query = 'day >= '.$fromdate_details['mday'];
                }
                elseif($month == $todate_details['mon']) {
                    $day_query = 'day <= '.$todate_details['mday'];
                }
                else {
                    $day_query = 'day BETWEEN 1 AND 31';
                }
                $where .= $or.'((year = 0 OR year = '.$fromdate_details['year'].') AND month='.$month.' AND '.$day_query.')';
                $or = ' OR ';
                $month++;
            }
        }
        else {
            $where .= '((year = 0 OR year = '.$fromdate_details['year'].') AND month = '.$fromdate_details['mon'].' AND day BETWEEN 1 AND 31)';
        }
    }
    return $where;
}

function operation_time_value($seconds) {
    $value = $seconds;
    $seconds = abs($seconds);

    $ret = '';

    /* Get the hours */
    $hours = intval(intval($seconds) / 3600);
    if($hours > 0) {
        $ret = $hours.':';
    }

    /* Fet the minutes */
    $minutes = bcmod((intval($seconds) / 60), 60);
    if($hours > 0 || $minutes > 0) {
        $ret .= str_pad($minutes, 2, '0', STR_PAD_LEFT).':';
    }
    else {
        $ret .= '00:';
    }

    /*  get the seconds */
    $seconds = bcmod(intval($seconds), 60);
    $ret .= str_pad($seconds, 2, '0', STR_PAD_LEFT);
    if($value < 0) {
        return '-'.$ret;
    }
    else {
        return $ret;
    }
}

?>