<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2011 Orkila International Offshore, All Rights Reserved
 * Automatic Monthly Leaves Overview
 * $module: Crons
 * Created		@najwa.kassem 		August 25, 2011 | 02:33 PM
 * Last Update: 	@zaher.reda			December 2, 2011 | 04:51 PM
 */

require '../inc/init.php';
require '../inc/attendance_functions.php';

$lang = new Language('english', 'user');
$lang->load('global');
$lang->load('attendance_meta');

if(!$core->input['action']) {
    $startdate = mktime(0, 0, 0, 1, 1, date('Y', TIME_NOW));
    $enddate = mktime(23, 59, 59, 12, 31, date('Y', TIME_NOW));
    $lang->thismonth = 'This Month';
    $lang->thisweek = 'This Week';
    $lang->lastmonth = 'Last Month';
    $lang->lastweek = 'Last Week';
    $cachearr = array();

    if((date('m', TIME_NOW) - 1) == 0) {
        $timelinestart = mktime(0, 0, 0, 12, 1, (date('Y', TIME_NOW) - 1));
        $lastmonthnumber = date('m', $timelinestart);
    }
    else {
        $timelinestart = mktime(0, 0, 0, (date('m', TIME_NOW) - 1), 1, date('Y', TIME_NOW));
        $lastmonthnumber = (date('m', TIME_NOW) - 1);
    }

    if((date('W', TIME_NOW) - 1) == 0) {
        $lastweeknumber = ltrim(date('W', mktime(0, 0, 0, 12, 1, (date('Y', TIME_NOW) - 1))), '0');
    }
    else {
        $lastweeknumber = (ltrim(date('W', TIME_NOW), '0') - 1);
    }
    $current_year = date('Y', TIME_NOW);
    $timelineend = mktime(0, 0, 0, date('m', TIME_NOW), 31, date('Y', TIME_NOW));
    $todaymonthnumber = ltrim(date('m', TIME_NOW), '0');
    $todayweeknumber = ltrim(date('W', TIME_NOW), '0');


    for($i = 1; $i <= 12; $i++) {
        $month_names .= '<th style="width:6%; padding:5px;">'.$lang->{strtolower(date("F", mktime(0, 0, 0, $i, 1, 0)))}.'</th>';
    }

    $hrgm_query = $db->query("SELECT affid, generalManager, supervisor, hrManager, finManager,coo FROM ".Tprefix."affiliates ORDER by name ASC");

    while($hrgm = $db->fetch_assoc($hrgm_query)) {
        if($hrgm['generalManager'] != 0) {
            $mgt_affid[$hrgm['generalManager']][$hrgm['affid']] = $hrgm['affid'];
            $mgt[$hrgm['affid']][$hrgm['generalManager']] = $hrgm['generalManager'];
            $mang[$hrgm['affid']]['gm'] = $hrgm['generalManager'];
        }
        if($hrgm['supervisor'] != 0) {
            $mgt_affid[$hrgm['supervisor']][$hrgm['affid']] = $hrgm['affid'];
            $mgt[$hrgm['affid']][$hrgm['supervisor']] = $hrgm['supervisor'];
            $mang[$hrgm['affid']]['su'] = $hrgm['supervisor'];
        }
        if($hrgm['hrManager'] != 0) {
            $mgt_affid[$hrgm['hrManager']][$hrgm['affid']] = $hrgm['affid'];
            $mgt[$hrgm['affid']][$hrgm['hrManager']] = $hrgm['hrManager'];
            $mang[$hrgm['affid']] ['hr'] = $hrgm['hrManager'];
        }
        if($hrgm['finManager'] != 0) {
            $mgt_affid[$hrgm['finManager']][$hrgm['affid']] = $hrgm['affid'];
            $mgt[$hrgm['affid']] [$hrgm['finManager']] = $hrgm['finManager'];
            $mang[$hrgm['affid']]['fn'] = $hrgm['finManager'];
        }
        if($hrgm['coo'] != 0) {
            $mgt_affid[$hrgm['coo']][$hrgm['affid']] = $hrgm['affid'];
            $mgt[$hrgm['affid']] [$hrgm['coo']] = $hrgm['coo'];
            $mang[$hrgm['affid']]['co'] = $hrgm['coo'];
        }
    }

    $supervisors_query = $db->query("SELECT DISTINCT(reportsTo) FROM ".Tprefix."users WHERE gid!=7 AND reportsTo!=0");
    while($supervisor = $db->fetch_assoc($supervisors_query)) {
        $supervisors[$supervisor['reportsTo']] = $supervisor['reportsTo'];
    }

    foreach($supervisors as $id) {
        $users_reportsto_query = $db->query("SELECT u.uid, displayName AS name, ae.affid, a.name as affname
                                            FROM ".Tprefix."users u
                                            JOIN ".Tprefix."affiliatedemployees ae ON (ae.uid = u.uid)
                                            JOIN ".Tprefix."affiliates a ON (ae.affid = a.affid)
                                            WHERE gid!=7 AND reportsTo={$id} AND isMain='1'
                                            ORDER by name ASC");
        while($users_reportsto = $db->fetch_assoc($users_reportsto_query)) {
            $users_supervisors[$id][$users_reportsto['uid']]['uid'] = $users_reportsto['uid'];
            $users_supervisors[$id][$users_reportsto['uid']]['name'] = $users_reportsto['name'];
            $users_info[$users_reportsto['uid']] = $users_reportsto;
            $supervisor_affids[$id][$users_reportsto['affid']] = $users_reportsto['affid'];
            if(!isset($cachearr['affiliates'][$users_reportsto['affid']])) {
                $cachearr['affiliates'][$users_reportsto['affid']] = $users_reportsto['affname'];
            }
        }
    }

    foreach($mgt_affid as $mgtuid => $affiliate) {
        foreach($affiliate as $affid => $val) {
            $users_affid_query = $db->query("SELECT u.uid, displayName AS name, ae.affid, a.name as affname, u.reportsTo
                                            FROM ".Tprefix."users u
                                            JOIN ".Tprefix."affiliatedemployees ae ON (u.uid=ae.uid)
                                            JOIN ".Tprefix."affiliates a ON (ae.affid = a.affid)
                                            WHERE gid!=7 AND isMain='1' AND ae.affid = {$affid}
                                            ORDER BY name ASC");

            while($users = $db->fetch_assoc($users_affid_query)) {
                //if(!isset($users_supervisors[$users['reportsTo']][$users['uid']])) {
                $users_supervisors[$mgtuid][$users['uid']]['uid'] = $users['uid'];
                $users_supervisors[$mgtuid][$users['uid']]['name'] = $users['name'];
                $supervisor_affids[$mgtuid][$users['affid']] = $users['affid'];
                if(!isset($users_info[$users['uid']])) {
                    $users_info[$users['uid']] = $users;
                }
                if(!isset($cachearr['affiliates'][$users_reportsto['affid']])) {
                    $cachearr['affiliates'][$users_reportsto['affid']] = $users_reportsto['affname'];
                }
                //}
            }
        }
    }

    /* Remove excluded employees - START */
    /* 	$excludedusers_query = $db->query("SELECT * FROM ".Tprefix."calendar_userpreferences_excludedusers u JOIN calendar_userpreferences p ON (u.cpid=p.cpid)");
      if($db->num_rows($excludedusers_query) > 0) {
      while($excludedusers = $db->fetch_array($excludedusers_query)) {
      $user_excludedemployees[$excludedusers['uid']][$excludedusers['euid']] = $excludedusers['euid'];
      unset($users_supervisors[$excludedusers['uid']][$excludedusers['euid']]);
      }
      } */
    /* Remove excluded employees - END */

    /* Get leaves - START */
    $interestedleaves = array(1, 3);
    $skipbalance_types = array(3);

    foreach($interestedleaves as $key => $ltid) {
        $types = array();
        $type_query = $db->query("SELECT ltid, title FROM ".Tprefix."leavetypes WHERE countWith = {$ltid}");
        $types[$ltid] = $ltid;
        $rowcolor = '';

        while($type = $db->fetch_assoc($type_query)) {
            $types[] = $type['ltid'];
            $typename[$type['ltid']] = $type['title'];
        }

        $typename[$ltid] = $db->fetch_field($db->query("SELECT title FROM ".Tprefix."leavetypes WHERE ltid='{$ltid}'"), 'title');

        foreach($users_info as $uid => $val) {
            $query = $db->query("SELECT l.*, t.isWholeDay
                                FROM ".Tprefix."leaves l
                                JOIN ".Tprefix."leavetypes t ON (l.type=t.ltid)
                                WHERE l.uid = {$uid} AND l.type IN (".implode(',', $types).") AND ((l.fromDate BETWEEN {$startdate} AND {$enddate}) OR (l.toDate BETWEEN {$startdate} AND {$enddate}))");

            if($db->num_rows($query) > 0) {
                $workshift_query = $db->query("SELECT uid, fromDate, toDate
                                                FROM ".Tprefix."employeesshifts
                                                WHERE uid = {$uid} AND (({$startdate} BETWEEN fromDate AND toDate) OR ({$enddate} BETWEEN fromDate AND toDate))");
                $array_shift = array();
                if($db->num_rows($workshift_query) > 0) {
                    while($shift = $db->fetch_assoc($workshift_query)) {
                        $array_shift[$shift['fromDate']][$shift['toDate']] = $shift;
                    }
                }

                while($leave = $db->fetch_assoc($query)) {
                    $status = array();
                    /* 					$query2 = $db->query("SELECT isApproved, COUNT(isApproved) AS approvecount FROM ".Tprefix."leavesapproval WHERE lid='{$leave[lid]}' GROUP BY isApproved");
                      if($db->num_rows($query2) > 0) {
                      while($approve = $db->fetch_assoc($query2)) {
                      if($approve['isApproved'] == 1) {
                      $status['approved'] = $approve['approvecount'];
                      }
                      else
                      {
                      $status['notapproved'] = $approve['approvecount'];
                      }
                      }
                      } */
                    $query2 = $db->query("SELECT isApproved FROM ".Tprefix."leavesapproval WHERE lid='{$leave[lid]}'");
                    if($db->num_rows($query2) > 0) {
                        $status['approved'] = true;
                        while($approve = $db->fetch_assoc($query2)) {
                            if($approve['isApproved'] == 0) {
                                $status['approved'] = 0;
                                break;
                            }
                        }
                    }

                    //if($status['approved'] == array_sum($status)) {
                    if($status['approved'] == true) {
                        if(isset($array_shift)) {
                            $shift_temp = $array_shift;
                            $result = array_walk($shift_temp, 'get_workshift', $leave['fromDate']);
                        }
                        if(isset($shift_temp)) {
                            if(sizeof($shift_temp) > 0) {
                                $shiftquery = $db->query("SELECT ws.weekDays, es.fromDate, es.toDate FROM ".Tprefix."employeesshifts es JOIN ".Tprefix."workshifts ws ON (ws.wsid=es.wsid) WHERE es.uid='".$uid."' AND ((".$leave['fromDate']." BETWEEN es.fromDate AND es.toDate) OR  (".$leave['toDate']." BETWEEN es.fromDate AND es.toDate))");
                                if($db->num_rows($shiftquery) > 0) {
                                    $leave_year['from'] = ltrim(date('Y', $leave['fromDate']), '0');
                                    $leave_year['to'] = ltrim(date('Y', $leave['toDate']), '0');

                                    if($current_year == $leave_year['from'] && $current_year == $leave_year['to']) {
                                        $nb_month_fromleave = ltrim(date('m', $leave['fromDate']), '0');
                                        $nb_month_toleave = ltrim(date('m', $leave['toDate']), '0');

                                        if($nb_month_fromleave == $nb_month_toleave) {
                                            $total[$ltid][$uid][$nb_month_fromleave] += count_workingdays($uid, $leave['fromDate'], $leave['toDate'], $leave['isWholeDay']);
                                        }
                                        else {
                                            $date_endfirstmonth = mktime(23, 0, 0, $nb_month_fromleave, date('t', $leave['fromDate']), date('Y', TIME_NOW));
                                            $date_startsecondmonth = mktime(0, 0, 0, $nb_month_toleave, 1, date('Y', TIME_NOW));
                                            $total[$ltid][$uid][$nb_month_fromleave] += count_workingdays($uid, $leave['fromDate'], $date_endfirstmonth, $leave['isWholeDay']);
                                            $total[$ltid][$uid][$nb_month_toleave] += count_workingdays($uid, $date_startsecondmonth, $leave['toDate'], $leave['isWholeDay']);

                                            $subbalance[$ltid][$uid] += $total[$ltid][$uid][$nb_month_toleave];
                                        }
                                        $subbalance[$ltid][$uid] += $total[$ltid][$uid][$nb_month_fromleave];
                                    }
                                    else {
                                        if($leave_year['from'] < $current_year) {
                                            $total[$ltid][$uid][1] += count_workingdays($uid, $startdate, $leave['toDate'], $leave['isWholeDay']);
                                            $subbalance[$ltid][$uid] += $total[$ltid][$uid][1];
                                        }
                                        elseif($leave_year['to'] > $current_year) {
                                            $total[$ltid][$uid][12] += count_workingdays($uid, $leave['fromDate'], $enddate, $leave['isWholeDay']);
                                            $subbalance[$ltid][$uid] += $total[$ltid][$uid][12];
                                        }
                                    }
                                }
                            }
                            else {
                                $nb_month_leave = ltrim(date('m', $leave['fromDate']), '0');
                                $total[$ltid][$uid][$nb_month_leave] = 0;
                                $subbalance[$ltid][$uid] = 0;
                            }
                        }
                        else {
                            $nb_month_leave = ltrim(date('m', $leave['fromDate']), '0');
                            $total[$ltid][$uid][$nb_month_leave] = 0;
                            $subbalance[$ltid][$uid] = 0;
                        }
                    }
                }

                if($rowcolor == '#F7FAFD') {
                    $rowcolor = '#FFF';
                }
                else {
                    $rowcolor = '#F7FAFD';
                }

                $output[$val['affid']][$ltid][$uid] = '<tr style="border-bottom: 1px dashed #CCCCCC; background-color:'.$rowcolor.'"><td><a href="'.DOMAIN.'/users.php?action=profile&uid='.$uid.'" target="_blank">'.$users_info[$uid]['name'].'</a></td>';

                for($i = 1; $i <= 12; $i++) {
                    if(isset($total[$ltid][$uid][$i]) && !empty($total[$ltid][$uid][$i])) {
                        $fromdate = mktime(0, 0, 0, $i, 1, date('Y', TIME_NOW));
                        $todate = mktime(23, 59, 0, $i, 31, date('Y', TIME_NOW));
                        $output[$val['affid']][$ltid][$uid] .= '<td style="text-align:right;"><a href="'.DOMAIN.'/index.php?module=attendance/listleaves&uid='.$uid.'&fromdate='.$fromdate.'&todate='.$todate.'" target="_blank">'.round($total[$ltid][$uid][$i], 1).'</a></td>';
                    }
                    else {
                        $output[$val['affid']][$ltid][$uid] .= '<td style="text-align:right;">0</td>';
                    }
                }

                if(is_array($total[$ltid][$uid])) {
                    $output[$val['affid']][$ltid][$uid] .= '<td style="font-style:italic; font-weight: bold; text-align:right;">'.round(array_sum($total[$ltid][$uid]), 1).'</td>';
                }
                else {
                    $output[$val['affid']][$ltid][$uid] .= '<td style="font-style:italic; text-align:right;">0</td>';
                }
            }
            else {
                if($rowcolor == '#F7FAFD') {
                    $rowcolor = '#FFF';
                }
                else {
                    $rowcolor = '#F7FAFD';
                }
                $output[$val['affid']][$ltid][$uid] = '<tr style="border-bottom: 1px dashed #CCCCCC; background-color:'.$rowcolor.'"><td><a href="'.DOMAIN.'/users.php?action=profile&uid='.$uid.'" target="_blank">'.$users_info[$uid]['name'].'</a></td>';

                for($i = 1; $i <= 12; $i++) {
                    $output[$val['affid']][$ltid][$uid] .= '<td style="text-align:right;">0</td>';
                }
                $output[$val['affid']][$ltid][$uid] .= '<td style="font-style:italic; text-align:right;">0</td>';
            }

            /* Get balance - START */
            $balance_query = $db->query("SELECT canTake, additionalDays
                                            FROM ".Tprefix."leavesstats
                                            WHERE uid = {$uid} AND ltid = {$ltid} AND (((periodStart BETWEEN {$startdate} AND {$enddate}) OR (periodEnd  BETWEEN {$startdate} AND {$enddate})))");

            while($stats = $db->fetch_assoc($balance_query)) {
                $balance[$ltid][$uid] = $stats['canTake'] + $stats['additionalDays']; //$subbalance[$ltid][$uid];

                if(is_array($total[$ltid][$uid])) {
                    $balance[$ltid][$uid] -= array_sum($total[$ltid][$uid]);
                }
            }
            if(!in_array($ltid, $skipbalance_types)) {
                if(isset($balance[$ltid][$uid])) {
                    $output_style = '';
                    if($balance[$ltid][$uid] < 0) {
                        $output_style = ' color: red;';
                    }
                    $output[$val['affid']][$ltid][$uid] .= '<td style="text-align: right; font-weight:bold;'.$output_style.'">'.round($balance[$ltid][$uid], 1).'</td></tr>';
                }
                else {
                    $output[$val['affid']][$ltid][$uid] .= '<td style="font-weight:bold; text-align:right;">0</td></tr>';
                }
            }
            else {
                $output[$val['affid']][$ltid][$uid] .= '<td style="font-weight:bold; text-align:center;">-</td></tr>';
            }
            /* Get balance - END */
            /* Get leaves - END */

            /* Parse Timeline - START */
            $timeline_excludedtypes = array(10);
            $timeline_query = $db->query("SELECT l.*, t.*
                                        FROM ".Tprefix."leaves l
                                        JOIN ".Tprefix."leavetypes t ON (l.type=t.ltid)
                                        WHERE l.uid = {$uid} AND ((l.fromDate BETWEEN {$timelinestart} AND {$timelineend}) OR (l.toDate BETWEEN {$timelinestart} AND {$timelineend}))
                                        AND l.type NOT IN (".implode(', ', $timeline_excludedtypes).")
                                        GROUP BY l.lid
                                        ORDER BY l.fromDate DESC");

            if($db->num_rows($timeline_query) > 0) {
                while($timeline_leave = $db->fetch_assoc($timeline_query)) {
                    $status = array();
                    $query2 = $db->query("SELECT isApproved, COUNT(isApproved) AS approvecount FROM ".Tprefix."leavesapproval WHERE lid='{$timeline_leave[lid]}' GROUP BY isApproved");
                    while($approve = $db->fetch_assoc($query2)) {
                        if($approve['isApproved'] == 1) {
                            $status['approved'] = $approve['approvecount'];
                        }
                        else {
                            $status['notapproved'] = $approve['approvecount'];
                        }
                    }
                    if($status['approved'] == array_sum($status)) {
                        if(isset($array_shift)) {
                            $shift_temp = $array_shift;
                            array_walk($shift_temp, 'get_workshift', $timeline_leave['fromDate']);
                        }
                        if(isset($shift_temp)) {
                            if(sizeof($shift_temp) > 0) {
                                $leave_duration = count_workingdays($timeline_leave['uid'], $timeline_leave['fromDate'], $timeline_leave['toDate'], $timeline_leave['isWholeDay']);
                                if(date('m', $timeline_leave['fromDate']) == $todaymonthnumber) {
                                    if(date('W', $timeline_leave['fromDate']) == $todayweeknumber) {
                                        $timeline[1]['thisweek'][$val['affid']]['leave'][$uid][$timeline_leave['lid']]['leavetype'] = $timeline_leave['title'];
                                        $timeline[1]['thisweek'][$val['affid']]['leave'][$uid][$timeline_leave['lid']]['duration'] = $leave_duration;
                                        $timeline[1]['thisweek'][$val['affid']]['leave'][$uid][$timeline_leave['lid']]['date'] = $timeline_leave['fromDate'];
                                    }
                                    elseif(date('W', $timeline_leave['fromDate']) == $lastweeknumber) {
                                        $timeline[2]['lastweek'][$val['affid']]['leave'][$uid][$timeline_leave['lid']]['leavetype'] = $timeline_leave['title'];
                                        $timeline[2]['lastweek'][$val['affid']]['leave'][$uid][$timeline_leave['lid']]['duration'] = $leave_duration;
                                        $timeline[2]['lastweek'][$val['affid']]['leave'][$uid][$timeline_leave['lid']]['date'] = $timeline_leave['fromDate'];
                                    }
                                    else {
                                        $timeline[3]['thismonth'][$val['affid']]['leave'][$uid][$timeline_leave['lid']]['leavetype'] = $timeline_leave['title'];
                                        $timeline[3]['thismonth'][$val['affid']]['leave'][$uid][$timeline_leave['lid']]['duration'] = $leave_duration;
                                        $timeline[3]['thismonth'][$val['affid']]['leave'][$uid][$timeline_leave['lid']]['date'] = $timeline_leave['fromDate'];
                                    }
                                }
                                else {
                                    $timeline[4]['lastmonth'][$val['affid']]['leave'][$uid][$timeline_leave['lid']]['leavetype'] = $timeline_leave['title'];
                                    $timeline[4]['lastmonth'][$val['affid']]['leave'][$uid][$timeline_leave['lid']]['duration'] = $leave_duration;
                                    $timeline[4]['lastmonth'][$val['affid']]['leave'][$uid][$timeline_leave['lid']]['date'] = $timeline_leave['fromDate'];
                                }
                                $leave_duration = '';
                            }
                        }
                    }
                }
            }

            $additionalleave_query = $db->query("SELECT ad.*, CONCAT(firstName, ' ', lastName) AS addedByName
                                                FROM ".Tprefix."attendance_additionalleaves ad
                                                JOIN ".Tprefix."users u ON (u.uid=ad.addedBy)
                                                WHERE ad.uid = {$uid} AND (date BETWEEN {$timelinestart} AND {$timelineend})
                                                ORDER BY date DESC");

            if($db->num_rows($additionalleave_query) > 0) {
                while($additionalleave = $db->fetch_assoc($additionalleave_query)) {
                    if(!isset($users_info[$additionalleave['addedBy']])) {
                        $users_info[$additionalleave['addedBy']][$val['affid']]['name'] = $additionalleave['addedByName'];
                    }
                    $addleaves[$additionalleave['date']][$uid] = $additionalleave;

                    if(date('m', $additionalleave['date']) == $todaymonthnumber) {
                        if(date('W', $additionalleave['date']) == $todayweeknumber) {
                            $timeline[1]['thisweek']['addleave'][$val['affid']][$uid] = $additionalleave;
                        }
                        elseif(date('W', $additionalleave['date']) == $lastweeknumber) {
                            $timeline[2]['lastweek'][$val['affid']]['addleave'][$uid] = $additionalleave;
                        }
                        else {
                            $timeline[3]['thismonth'][$val['affid']]['addleave'][$uid] = $additionalleave;
                        }
                    }
                    else {
                        $timeline[4]['lastmonth'][$val['affid']]['addleave'][$uid] = $additionalleave;
                    }
                }
            }
        }
    }

    /* Parse timeline - END */
    if(is_array($timeline)) {
        ksort($timeline);
    }

    foreach($users_supervisors as $supid => $val) {
        $content_exist = false;
        $message_row = $message = $timeline_message = '';
        if(isset($timeline)) {
            foreach($timeline as $sequence) {
                foreach($sequence as $period => $data) {
                    $timeline_message_period = '';
                    $linebreak['affiliate'] = '';
                    $found_one_entry_section = false;
                    if(is_array($data) && !empty($data)) {
                        //$timeline_message .= '<br /><strong>'.$lang->$period.'</strong>';

                        foreach($data as $affid => $affiliates) {
                            $timeline_message_affiliate = '';
                            $timeline_message_details = '';
                            $found_one_entry = false;
                            foreach($affiliates as $key => $users) {
                                foreach($val as $uid => $userdetails) {
                                    if(isset($users[$uid])) {
                                        $found_one_entry = true;
                                        if($key == 'addleave') {
                                            $timeline_message_details .= 'On '.date($core->settings['dateformat'], $users[$uid]['date']).', '.$users_info[$users[$uid]['addedBy']]['name'].' added '.$users[$uid]['numDays'].' day(s) to '.$users_info[$uid]['name'].' ('.$users[$uid]['remark'].').<br />';
                                        }
                                        elseif($key == 'leave') {
                                            foreach($users[$uid] as $lid => $vals) {
                                                $timeline_message_details .= 'On '.date($core->settings['dateformat'], $vals['date']).', '.$users_info[$uid]['name'].' took '.$vals['duration'].' day(s) '.strtolower($vals['leavetype']).'.<br />';
                                            }
                                        }
                                    }
                                }
                            }

                            if($found_one_entry == true) {
                                $found_one_entry_section = true;
                                if(sizeof($data) > 1) {
                                    $timeline_message_affiliate .= $linebreak['affiliate'].'<span style="font-style:italic;">'.$cachearr['affiliates'][$affid].'</span> <br />';
                                    $linebreak['affiliate'] = '<br />';
                                }
                                $timeline_message_affiliate .= $timeline_message_details;
                            }
                            $timeline_message_period .= $timeline_message_affiliate;
                        }

                        if($found_one_entry_section == true) {
                            $timeline_message .= '<div style="font-weight:bold; margin-bottom: 0px; margin-top:10px;">'.$lang->$period.'</div>'.$timeline_message_period;
                        }
                    }
                }
            }
        }

        foreach($supervisor_affids[$supid] as $affid) {
            $message = '';
            if(sizeof($output[$affid]) < 1) {
                continue;
            }
            $message .= '<div style="margin-top: 10px;font-weight: bold; color:#669900; border-bottom: 1px solid #F2F2F2;">'.$cachearr['affiliates'][$affid].'</div>';

            $message .= '<table border="0" width="100%" style="border: 0px; width: 100%; border-spacing: 0px; border-collapse:collapse; padding:0px;">';
            $message .= '<tr style="background-color:#92D050; font-weight: bold; font-size: 12px; border-bottom: dashed 1px #666666; text-align: left; padding: 4px;">';
            $message .= '<td style="width:20%;">'.$lang->employee.'</td>'.$month_names.'<td width="4%">'.$lang->daystaken.'</td>';
            // if(!in_array($ltid, $skipbalance_types)) {
            $message .= '<td width="4%">'.$lang->balance.'</td>';
            // }
            $message .= '</tr>';

            foreach($output[$affid] as $ltid => $affiliate_data) {
                uasort($val, 'sortusers');
                $message .= '<td colspan="15" style="font-weight:bold; background-color:#D6EAAC; border-bottom: dashed 1px #666666;">'.$typename[$ltid].'</td>';

                foreach($val as $key => $uid) {
                    if($users_info[$key]['affid'] != $affid) {
                        continue;
                    }

                    if(isset($output[$affid][$ltid][$key])) {
                        $message .= $output[$affid][$ltid][$key];
                    }
                    else {
                        if($rowcolor == '#F7FAFD') {
                            $rowcolor = '#FFF';
                        }
                        else {
                            $rowcolor = '#F7FAFD';
                        }
                        $message .= '<tr style="border-bottom: 1px dashed #CCCCCC; background-color:'.$rowcolor.'"><td><a href="'.DOMAIN.'/users.php?action=profile&uid='.$uid.'" target="_blank">'.$users_info[$key]['name'].'</a></td>';
                        $message .= str_repeat('<td>0</td>', 12);
                        $message .= '<td>0</td><td></td></tr>';
                    }
                }
            }

            $message .= '</table>';
            if(is_array($mang[$affid])) {
                foreach($mang[$affid] as $type => $id) {
                    if(empty($id)) {
                        continue;
                    }
                    switch($type) {
                        case 'hr':
                            $user = new Users($id);
                            if(is_object($user)) {
                                $message .= '<div style="inline-block">HR Manager: <a href="mailto:'.$user->email.'"> '.$user->get_displayname().'</a></div>';
                            }
                            break;
                        case 'fn':
                            $user = new Users($id);
                            if(is_object($user)) {
                                $message .= '<div style="inline-block">Financial Manager: <a href="mailto:'.$user->email.'"> '.$user->get_displayname().'</a></div>';
                            }
                            break;
                        case 'su':
                            $user = new Users($id);
                            if(is_object($user)) {
                                $message .= '<div style="inline-block">Supervisor: <a href="mailto: '.$user->email.'">'.$user->get_displayname().'</a></div>';
                            }
                            break;
                        case 'gm':
                            $user = new Users($id);
                            if(is_object($user)) {
                                $message .= '<div style="inline-block">General Manager: <a href="mailto:'.$user->email.'"> '.$user->get_displayname().'</a></div>';
                            }
                            break;
                        case 'co':
                            $user = new Users($id);
                            if(is_object($user)) {
                                $message .= '<div style="inline-block">Chief Operating Officer: <a href="mailto:'.$user->email.'"> '.$user->get_displayname().'</a></div>';
                            }
                            break;
                    }
                }
            }
            // unset($mang);
//        foreach($output as $ltid => $data) {
//            $message_rows = '';
//
//            foreach($data as $affid => $affiliate_data) {
//                $affiliate_parsed = false;
//
//                uasort($val, 'sortusers');
//                foreach($val as $key => $uid) {
//                    if(sizeof($data) > 1 && $affiliate_parsed == false && isset($output[$ltid][$affid][$key])) {
//                        $message_rows .= '<td colspan="15" style="font-weight:bold; background-color:#D6EAAC; border-bottom: dashed 1px #666666;">'.$cachearr['affiliates'][$affid].'</td>';
//                        $affiliate_parsed = true;
//                    }
//                    $message_rows .= $output[$ltid][$affid][$key];
//                }
//            }
//
//            if(empty($message_rows)) {
//                continue;
//            }
//            else {
//                $content_exist = true;
//                $message .= '<div style="margin-top: 10px;font-weight: bold; color:#669900; border-bottom: 1px solid #F2F2F2;">'.$typename[$ltid].'</div>';
//                $message .= '<table border="0" width="100%" style="border: 0px; width: 100%; border-spacing: 0px; border-collapse:collapse; padding:0px;">';
//                $message .= '<tr style="background-color:#92D050; font-weight: bold; font-size: 12px; border-bottom: dashed 1px #666666; text-align: left; padding: 4px;">';
//                $message .= '<td style="width:20%;">'.$lang->employee.'</td>'.$month_names.'<td width="4%">'.$lang->daystaken.'</td>';
//                if(!in_array($ltid, $skipbalance_types)) {
//                    $message .= '<td width="4%">'.$lang->balance.'</td>';
//                }
//                $message .= '</tr>';
//                $message .= $message_rows.'</table>';
//            }
//        }


            if(empty($message)) {
                continue;
            }
            if(empty($timeline_message)) {
                $timeline_output = '';
            }
            else {
                //	$timeline_output = '<h2 style="margin-bottom:10px;">Timeline</h2>'.$timeline_message[$affid];
            }
            $message_output = '<html><head><title>Monthly Leaves Overview</title></head><h2>Monthly Leaves Overview</h2><body>'.$message.'<br />'.$timeline_output.'</body></html>';

            $email_data = array(
                    'from_email' => $core->settings['maileremail'],
                    'from' => 'OCOS Mailer',
                    'subject' => 'Monthly Leaves Overview - '.$cachearr['affiliates'][$affid],
                    'message' => $message_output
            );

            $email_data['to'] = $db->fetch_field($db->query("SELECT email FROM ".Tprefix."users WHERE uid='".$supid."'"), 'email');

            if(empty($email_data['to'])) {
                continue;
            }

            //echo $message_output;
            //print_r($email_data);
            // echo '<hr />';
            $mail = new Mailer($email_data, 'php');
            if($mail->get_status() === true) {
                $log->record($lang->monthlyleavesoverview, $email_data['to']);
            }
            else {
                $result['error'][] = $supid;
            }
            //echo '<hr />';
            $timeline_output = $message = $message_rows = '';
        }
    }
}
function get_workshift($item, $fromdate, $secondarray) {
    if($secondarray < $fromdate) {
        if($secondarray > key($item)) {
            unset($item);
        }
    }
    if($secondarray > key($item)) {
        unset($item);
    }
}

function sortusers($a, $b) {
    return strcasecmp($a['name'], $b['name']);
}

?>