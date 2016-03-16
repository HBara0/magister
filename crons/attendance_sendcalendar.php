<?php
require '../inc/init.php';
require '../inc/attendance_functions.php';

$message = '<html><head><title>Expected leaves and holidays in your affiliates</title></head><body>';
$message .= '';

$affiliates_query = $db->query("SELECT generalManager, supervisor, hrManager FROM ".Tprefix."affiliates");
while($affiliate_mgmt = $db->fetch_assoc($affiliates_query)) {
    foreach($affiliate_mgmt as $val) {
        if($val == 0) {
            continue;
        }
        $users[$val] = $val;
    }
}
//$users = array();
//$users[63] = 63;

$excluded_leavetypes = array(10);
foreach($users as $uid) {
    $message = '';
    $affiliates = $leaves = $holidays = array();
    $user_excludedemployees = $user_excludedaffiliates = array();
    $current_date = getdate(TIME_NOW);
    $month['firstday'] = mktime(0, 0, 0, $current_date['mon'], 1, $current_date['year']);
    $month['numdays'] = date('t', $month['firstday']);
    $month['today_weekday'] = date('N', TIME_NOW);

    /* GET CALENDAR USER PREFERENCES - START */
    $query = $db->query("SELECT * FROM ".Tprefix."calendar_userpreferences WHERE uid={$uid}");
    if($db->num_rows($query) > 0) {
        $calendar_preferences = $db->fetch_array($query);

        $excludedaffiliates_query = $db->query("SELECT * FROM ".Tprefix."calendar_userpreferences_excludedaffiliates a JOIN calendar_userpreferences p ON (a.cpid=p.cpid) WHERE p.uid={$uid}");
        if($db->num_rows($excludedaffiliates_query) > 0) {
            while($excludedaffiliates = $db->fetch_array($excludedaffiliates_query)) {
                $user_excludedaffiliates[] = $excludedaffiliates['affid'];
            }
        }

        $excludedusers_query = $db->query("SELECT * FROM ".Tprefix."calendar_userpreferences_excludedusers u JOIN calendar_userpreferences p ON (u.cpid=p.cpid) WHERE p.uid={$uid}");
        if($db->num_rows($excludedusers_query) > 0) {
            while($excludedusers = $db->fetch_array($excludedusers_query)) {
                $user_excludedemployees[$excludedusers['euid']] = $excludedusers['euid'];
            }
        }
    }
    /* GET CALENDAR USER PREFERENCES - END */

    /* GET USER AFFILIATES - START */
    $affiliates_querystring = '';
    if(is_array($user_excludedaffiliates) && !empty($user_excludedaffiliates)) {
        $affiliates_querystring = ' AND ae.affid NOT IN ('.implode(",", $user_excludedaffiliates).')';
    }

    $query = $db->query("SELECT a.name, ae.* FROM ".Tprefix."affiliatedemployees ae JOIN ".Tprefix."affiliates a ON (a.affid=ae.affid) WHERE uid='{$uid}'{$affiliates_querystring} ORDER BY ae.affid ASC");
    while($affiliate = $db->fetch_assoc($query)) {
        $affiliates['affid'][$affiliate['affid']] = $affiliate['affid'];
        $affiliates['name'][$affiliate['affid']] = $affiliate['name'];
    }

    $query = $db->query("SELECT affid, name FROM ".Tprefix."affiliates WHERE supervisor='{$uid}'");
    if($db->num_rows($query) > 0) {
        while($affiliate = $db->fetch_assoc($query)) {
            if(isset($affiliates['affid'][$affiliate['affid']])) {
                $affiliates['supervised'][$affiliate['affid']] = $affiliate['affid'];
                $affiliates['name'][$affiliate['affid']] = $affiliate['name'];
            }
            else {
                $affiliates['affid'][$affiliate['affid']] = $affiliate['affid'];
                $affiliates['supervised'][$affiliate['affid']] = $affiliate['affid'];
            }
        }
    }
    /* GET USER AFFILIATES - END */

    /* GET RELATED LEAVES - START */
    if($calendar_preferences['excludeLeaves'] == 0) {
        $approved_lids = $unapproved_lids = array();
        foreach($affiliates['affid'] as $affid => $affiliate) {
            $affiliate_users_querystring = '';
            if(is_array($user_excludedemployees) && !empty($user_excludedemployees)) {
                $affiliate_users_querystring = ' AND uid NOT IN ('.implode(',', $user_excludedemployees).')';
            }

            $affiliate_users = get_specificdata('affiliatedemployees', 'uid', 'uid', 'uid', '', 0, "affid='{$affiliate}' AND isMain='1'{$affiliate_users_querystring}");
            if(empty($affiliate_users)) {
                continue;
            }

            //print_r($affiliate_users);
            $query = $db->query("SELECT l.lid, la.isApproved FROM ".Tprefix."leaves l JOIN ".Tprefix."leavesapproval la ON (l.lid=la.lid) WHERE ((".TIME_NOW." BETWEEN l.fromDate AND l.toDate) OR (l.fromDate > ".TIME_NOW.")) AND l.uid IN (".implode(', ', $affiliate_users).") AND l.type NOT IN (".implode(', ', $excluded_leavetypes).")");
            if($db->num_rows($query) > 0) {
                while($leave = $db->fetch_assoc($query)) {
                    if($leave['isApproved'] == 0) {
                        $unapproved_lids[$leave['lid']] = $leave['lid'];
                        if(in_array($leave['lid'], $approved_lids)) {
                            unset($approved_lids[$leave['lid']]);
                        }
                    }
                    if(!in_array($leave['lid'], $unapproved_lids) && $leave['isApproved'] == 1) {
                        $approved_lids[$leave['lid']] = $leave['lid'];
                    }
                }
            }
        }

        if(!empty($approved_lids)) {
            $query = $db->query("SELECT l.*, l.uid AS requester, Concat(u.firstName, ' ', u.lastName) AS employeename
					FROM ".Tprefix."leaves l JOIN ".Tprefix."users u ON (l.uid=u.uid)
					WHERE l.lid IN (".implode(',', $approved_lids).")  AND l.type NOT IN (".implode(', ', $excluded_leavetypes).") ORDER BY l.fromDate ASC");

            if($db->num_rows($query) > 0) {
                while($more_leaves = $db->fetch_assoc($query)) {
                    $num_days_off = ($more_leaves['toDate'] - $more_leaves['fromDate']) / 24 / 60 / 60; //(date('z', $more_leaves['toDate'])-date('z', $more_leaves['fromDate']))+1;

                    $leave_type_details = parse_type($more_leaves['type']);
                    $more_leaves['type'] = $leave_type_details;

                    if($num_days_off == 1) {
                        $current_check_date = getdate($more_leaves['toDate']);
                        $leaves[$current_check_date['mon']][$current_check_date['mday']][] = $more_leaves;
                    }
                    else {
                        for($i = 0; $i < $num_days_off; $i++) {
                            $current_check = $more_leaves['fromDate'] + (60 * 60 * 24 * $i);

                            if($month['firstday'] > $current_check) { //|| $more_leaves['toDate'] < $current_check) {
                                continue;
                            }

                            if($current_check > ($month['firstday'] * 60 * 60 * 24 * $month['numdays'])) {
                                break;
                            }
                            $current_check_date = getdate($current_check);

                            $leaves[$current_check_date['mon']][$current_check_date['mday']][] = $more_leaves;
                        }
                    }
                }
            }
        }
    }
    /* GET RELATED LEAVES - END */

    /* GET HOLIDAYS - START */
    if($calendar_preferences['excludeHolidays'] == 0) {
        $holidays_query = $db->query("SELECT aff.name AS affiliatename, h.*, c.acronym AS country
									FROM ".Tprefix."holidays h JOIN ".Tprefix."affiliates aff ON (aff.affid=h.affid) LEFT JOIN countries c ON (aff.country=c.coid)
									WHERE (year=0 OR year={$current_date[year]}) AND (month={$current_date[mon]} OR month=({$current_date[mon]}+1))"); // AND h.affid IN (".implode(',',$affiliates['affid']).")
        while($holiday = $db->fetch_assoc($holidays_query)) {
            if($holiday['year'] == 0) {
                $holiday['year'] == $time_details['year'];
            }

            if(!isset($affiliates['name'][$holiday['affid']])) {
                $affiliates['name'][$holiday['affid']] = $holiday['affiliatename'];
            }

            if(!isset($affiliates['country'][$holiday['affid']])) {
                $affiliates['country'][$holiday['affid']] = $holiday['country'];
            }
            $holidays[$holiday['month']][$holiday['day']][$holiday['affid']][] = $holiday;
            if($holiday['numDays'] > 1) {
                for($daynum = 1; $daynum < $holiday['numDays']; $daynum++) {
                    if(($holiday['day'] + $daynum) > date('t', mktime(0, 0, 0, $holiday['month'], $holiday['day'], $holiday['year']))) {
                        break;
                    }
                    $holidays[$holiday['month']][$holiday['day'] + $daynum][$holiday['affid']][] = $holiday;
                }
            }
        }
    }
    /* GET HOLIDAYS - END */

    $message .= '<table width="100%" cellspacing="0" cellpadding="5" style="border-left: 1px solid #CCC;" border="0">';
    $message .= '<tr><td style="background: #91b64f; font-weight: bold; text-align: center; width: 120px; padding: 5px; border-bottom: 1px solid #999; border-top: 1px solid #999; border-right: 1px solid #999;">Week</td>';
    $weekdays = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    $message .= '<td style="background: #91b64f; font-weight: bold; text-align: center; width: 120px; padding: 5px; border-bottom: 1px solid #999; border-top: 1px solid #999; border-right: 1px solid #999;">'.implode('</td><td style="background: #91b64f; font-weight: bold; text-align: center; width: 120px; padding: 5px; border-bottom: 1px solid #999; border-top: 1px solid #999; border-right: 1px solid #999;">', $weekdays).'</td></tr>';
    $message .= '<tr><td style="width: 3%; text-align:center; font-weight: bold; font-size: 11px; position: relative; padding: 5px; min-height: 80px; border-bottom: 1px solid #999; border-right: 1px solid #999; height: 80px;">'.date('W', TIME_NOW).'</td>';

    $week_num_days = 1;

    for($prev_days = 1; $prev_days < $month['today_weekday']; $prev_days++) {
        $message .= '<td style="background: #eee; width: 120px; padding: 5px; min-height: 80px; border-bottom: 1px solid #999; border-right: 1px solid #999; height: 80px;">&nbsp;</td>';
        $week_num_days++;
    }

    $legend = array();
    $message .= draw_available($current_date['mday'], $month['numdays'], $current_date);

    $message .= '</table><br /><span style="font-size: 11px;">'.implode('<br />', $legend).'</span>';
    $message .= '</body></html>';

    $email_data = array(
            'from_email' => $core->settings['maileremail'],
            'from' => 'OCOS Mailer',
            'subject' => 'Expected leaves and holidays in your affiliates',
            'message' => $message
    );
    $email_data['to'] = $db->fetch_field($db->query("SELECT email FROM ".Tprefix."users WHERE uid='".$uid."'"), 'email');
    if(empty($email_data['to'])) {
        continue;
    }

    //print_r($email_data);
    //echo '<br />';
    echo $message;
    //$mail = new Mailer($email_data, 'php');
}
function draw_available($start_from, $num_days, $start_date_info, $primary = true) {
    global $affiliates, $holidays, $leaves, $week_num_days, $legend;

    $current_date['mday'] = $start_from;
    $month['numdays'] = $num_days;

    for($day = $start_from; $day <= $num_days; $day++) {
        if($current_date['mday'] > $day) {
            $message .= '<td style="background: #eee; width: 120px; padding: 5px; min-height: 80px; border-bottom: 1px solid #999; border-right: 1px solid #999; height: 80px;">&nbsp;</td>';
        }
        else {
            if($current_date['mday'] == $day) {
                $current_day_style = '';
            }
            $message .= '<td style="width: 120px; vertical-align: top; 	font-size: 11px; position: relative; padding: 5px; min-height: 80px; border-bottom: 1px solid #999; border-right: 1px solid #999; height: 80px;">';
            $message .= '<div style="background: #CCC; padding: 5px; color: #333333; font-weight: bold; float: right; margin: -5px -5px 0 0; width: 20px; text-align: center;">'.$day.'</div>';

            if(isset($holidays[$start_date_info['mon']][$day])) {
                $message .= '<p style="font-size: 12px;">';
                foreach($holidays[$start_date_info['mon']][$day] as $affid => $affiliate_holidays) {
                    $message .= '<a href="'.DOMAIN.'/index.php?module=profiles/affiliateprofile&affid='.$affid.'" style="text-decoration: none; color:#666666;"><img src="'.DOMAIN.'/images/icons/flags/'.strtolower($affiliates['country'][$affid]).'.gif" border="0" alt="'.$affiliates['name'][$affid].'"/></a>';
                    $legend[$affid] = '<img src="'.DOMAIN.'/images/icons/flags/'.strtolower($affiliates['country'][$affid]).'.gif" border="0" alt="'.$affiliates['name'][$affid].'"/> '.$affiliates['name'][$affid];

                    foreach($affiliate_holidays as $val) {
                        $message .= '&nbsp;'.$val['title'].'<br />';
                    }
                }
                $message .= '</p>';
            }

            if(isset($leaves[$start_date_info['mon']][$day])) {
                $message .= '<p style="font-size: 12px;"><strong>Leaves:</strong><br />';
                foreach($leaves[$start_date_info['mon']][$day] as $val) {
                    if(!empty($val['type']['symbol'])) {
                        $val['type']['symbol'] = '<span class="smalltext">'.$val['type']['symbol'].'</span>';
                    }

                    $message .= '<a href="'.DOMAIN.'/users.php?action=profile&amp;uid='.$val['uid'].'" style="text-decoration: none; color:#666666;">'.$val['employeename'].'</a> '.$val['type']['symbol'].'<br />';
                }
                $message .= '</p>';
            }
        }
        $message .= '</td>';

        if($week_num_days == 7) {
            $message .= '</tr><tr><td style="width: 3%; text-align:center; font-weight: bold; font-size: 11px; position: relative; padding: 5px; min-height: 80px; border-bottom: 1px solid #999; border-right: 1px solid #999; height: 80px;">'.date('W', mktime(0, 0, 0, $start_date_info['mon'], 1, $start_date_info['year']) + (60 * 60 * 24 * ($day + 1))).'</font></td>';
            $week_num_days = 0;
        }
        else {
            if($day == $month['numdays']) {
                /* for($next_month = 1; $next_month<=(7-$week_num_days);$next_month++) {
                  $message .= '<td class="calendar_noday">&nbsp;</td>';
                  } */
                $week_num_days++;
                if($primary == true) {
                    //	$message .= draw_available(1, abs(1-$current_date['mday']), getdate(strtotime("+1 month", mktime(0, 0, 0, $start_date_info['mon'], 1, $start_date_info['year']))));
                    $message .= draw_available(1, 15, getdate(strtotime("+1 month", mktime(0, 0, 0, $start_date_info['mon'], 1, $start_date_info['year']))), $week_num_days, $leaves, $days_tohighlight, false);
                }
                //$message .= '</tr>';
            }
        }
        $week_num_days++;
    }

    return $message;
}

?>