<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Attendance Modules Functions File
 * $id: attendance_functions.php
 * Created: 	@zaher.reda		September 17, 2010 | 09:03 AM
 * Created: 	@zaher.reda		November 28, 2012 | 01:15 PM
 */

/*
 * 	Update user leave stats
 * 	Created a new stat entry if request for a new year
 *
 */
function update_leavestats_periods($leave, $is_wholeday = true, $countdays = true) {
    global $db, $core;

    $leave_user = $db->fetch_assoc($db->query("SELECT hr.joinDate, hr.firstJobDate, ae.affid
												FROM ".Tprefix."users u LEFT JOIN ".Tprefix."userhrinformation hr ON (u.uid=hr.uid) JOIN ".Tprefix."affiliatedemployees ae ON (ae.uid=hr.uid)
												WHERE ae.isMain=1 AND u.uid='".$db->escape_string($leave['uid'])."'"));
    if(empty($leave_user['joinDate'])) {
        $leave_user['joinDate'] = $leave['fromDate'];
    }

    if(!isset($leave['workingdays'])) {
        $leave['workingdays'] = count_workingdays($leave['uid'], $leave['fromDate'], $leave['toDate'], $is_wholeday);
    }

    if($countdays == false) {
        $leave['workingdays'] = 0;
    }

    $leavetype_details = parse_type($leave['type']);
    if(!empty($leavetype_details['countWith'])) {
        $leave['policy_ltid'] = $leavetype_details['countWith'];
    }
    else {
        $leave['policy_ltid'] = $db->escape_string($leave['type']);
    }

    $query = $db->query("SELECT *
						FROM ".Tprefix."leavesstats
						WHERE uid='".$db->escape_string($leave['uid'])."' AND ltid='".$db->escape_string($leave['policy_ltid'])."' AND ((".$db->escape_string($leave['fromDate'])." BETWEEN periodStart AND periodEnd) OR (".$db->escape_string($leave['toDate'])." BETWEEN periodStart AND periodEnd))");

    if($db->num_rows($query) > 1) {
        while($period = $db->fetch_assoc($query)) {
            if($leave['fromDate'] < $period['periodStart']) {
                $leave['fromDate'] = $period['periodStart'];
            }

            if($leave['toDate'] > $period['periodEnd']) {
                $leave['toDate'] = $period['periodEnd'];
            }

            update_leavestats_periods($leave, true);
        }

        return true;
    }
    elseif($db->num_rows($query) == 1) {
        $stats = $db->fetch_assoc($query);
        $db->update_query('leavesstats', array('daysTaken' => ($stats['daysTaken'] + $leave['workingdays'])), 'lsid='.$stats['lsid']);
    }
    else {
        $newleavestats = array(
                'uid' => $leave['uid'],
                'ltid' => $leave['policy_ltid'],
                'daysTaken' => $leave['workingdays']
        );

        $affiliate_policy = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."affiliatesleavespolicies WHERE ltid='{$leave[policy_ltid]}' AND affid='{$leave_user[affid]}'"));
        if(empty($affiliate_policy)) {
            return false;
            error('No policiy specified'); //Temp
        }

        if($affiliate_policy['useFirstJobDate'] == 1 && !empty($leave_user['firstJobDate'])) {
            $leave_user['joinDate'] = $leave_user['firstJobDate'];
        }

        $oneyear_anniversary = $leave_user['joinDate'] + (60 * 60 * 24 * 365);
        if($oneyear_anniversary > $leave['fromDate']) {
            $newleavestats['periodStart'] = $leave_user['joinDate'];
            $newleavestats['periodEnd'] = $oneyear_anniversary;
            if($affiliate_policy['entitleAfter'] == 0) {
                $newleavestats['entitledFor'] = $affiliate_policy['basicEntitlement'];
            }
            else {
                $newleavestats['entitledFor'] = 0;
            }
        }
        else {
            $leave_year = date('Y', $leave['fromDate']);
            $employment_year = date('Y', $leave_user['joinDate']);
            $first_fiscalyearend = mktime(23, 59, 0, 12, 31, $employment_year + 1);
            if($first_fiscalyearend > $leave['fromDate']) {
                $newleavestats['periodStart'] = $oneyear_anniversary + 60;
                $newleavestats['periodEnd'] = $first_fiscalyearend;

                if($affiliate_policy['entitleAfter'] == 1) {
                    $newleavestats['entitledFor'] = (round((($newleavestats['periodEnd'] - $newleavestats['periodStart']) / 60 / 60 / 24 / 30.4)) * $affiliate_policy['basicEntitlement']) / 12;
                    $entitlement_remainder = fmod($newleavestats['entitledFor'], 1);

                    if($entitlement_remainder > 0.5) {
                        $newleavestats['entitledFor'] = ceil($newleavestats['entitledFor']);
                    }
                    elseif($entitlement_remainder < 0.5) {
                        $newleavestats['entitledFor'] = floor($newleavestats['entitledFor']);
                    }
                }
            }
            else {
                $working_years = $leave_year - $employment_year;

                $newleavestats['periodStart'] = mktime(0, 0, 0, 1, 1, $leave_year);
                $newleavestats['periodEnd'] = mktime(23, 59, 0, 12, 31, $leave_year);

                $newleavestats['entitledFor'] = $affiliate_policy['basicEntitlement'];

                if(!empty($affiliate_policy['promotionPolicy'])) {
                    $promotion_policy = unserialize($affiliate_policy['promotionPolicy']);
                    ksort($promotion_policy);

                    while($val = current($promotion_policy)) {
                        if($working_years >= key($promotion_policy)) {
                            $newleavestats['entitledFor'] += $val;
                        }

                        if($working_years > key($promotion_policy) && $working_years != key($promotion_policy)) {
                            $prev_promotion += $val;
                        }
                        next($promotion_policy);
                    }

                    if(array_key_exists($working_years, $promotion_policy)) {
                        //$prev_promotion = prev($promotion_policy);
                        $employment_month = date('n', $leave_user['joinDate']);
                        //$newleavestats['entitledFor'] = ((($employment_month + 1)*$newleavestats['entitledFor'])/12)+(((12 - ($employment_month + 1))*($affiliate_policy['basicEntitlement']+$prev_promotion))/12);
                        $newleavestats['entitledFor'] = ((((12 - $employment_month) + 1) * $newleavestats['entitledFor']) / 12) + (((12 - ((12 - $employment_month) + 1)) * ($affiliate_policy['basicEntitlement'] + $prev_promotion)) / 12);
                    }
                }
            }
        }

        $query2 = $db->query("SELECT * FROM ".Tprefix."leavesstats WHERE uid='".$db->escape_string($leave['uid'])."' AND ltid='".$db->escape_string($leave['policy_ltid'])."' ORDER BY periodStart DESC LIMIT 0, {$affiliate_policy[canAccumulateFor]}");
        if($db->num_rows($query2) > 0) {
            $first_in = true;
            while($prev_leaves = $db->fetch_assoc($query2)) {
                if($first_in == true) {
                    $newleavestats['remainPrevYear'] = ($prev_leaves['entitledFor'] + $prev_leaves['additionalDays']) - $prev_leaves['daysTaken'];
                    $newleavestats['remainPrevYearActual'] = ($prev_leaves['canTake'] + $prev_leaves['additionalDays']) - $prev_leaves['daysTaken'];
                }
                $newleavestats['canTake'] += $prev_leaves['remainPrevYear'];
                $first_in = false;
            }
            $newleavestats['canTake'] += $newleavestats['remainPrevYear'];
        }

        $newleavestats['canTake'] += $newleavestats['entitledFor'];

        $accumulated_vs_entitled_diff = $newleavestats['canTake'] - $newleavestats['entitledFor'];

        $newleavestats['remainPrevYear'] = $newleavestats['remainPrevYearActual'];
        unset($newleavestats['remainPrevYearActual']);

        if($accumulated_vs_entitled_diff > $affiliate_policy['maxAccumulateDays']) {
            $newleavestats['canTake'] -= $accumulated_vs_entitled_diff - $affiliate_policy['maxAccumulateDays'];
        }

        $db->insert_query('leavesstats', $newleavestats);
    }
}

function update_leavestats_sequential($leave, $is_wholeday = true, $leave_year = '') {
    global $db, $core;
    //CHECK ALL CORE_USER SHOULD BE LEAVE USER
    if(!isset($leave_year) || empty($leave_year)) {
        $fromdate_info = getdate($leave['fromDate']);
        $todate_info = getdate($leave['toDate']);
        if($fromdate_info['year'] == $todate_info['year']) {
            $leave_year = $fromdate_info['year'];
            $query = $db->query("SELECT * FROM ".Tprefix."leavesstats WHERE uid='".$db->escape_string($leave['uid'])."' AND year='".$db->escape_string($leave_year)."' AND ltid='".$db->escape_string($leave['type'])."'");
        }
        else {
            $check_year = $fromdate_info['year'];
            while($check_year != $todate_info['year']) {
                update_leavestats_sequential($leave, $is_wholeday, $check_year);
                $check_year++;
            }
            return true;
        }
    }
    else {
        $query = $db->query("SELECT * FROM ".Tprefix."leavesstats WHERE uid='".$db->escape_string($leave['uid'])."' AND year='".$db->escape_string($leave_year)."' AND ltid='".$db->escape_string($leave['type'])."'");
    }

    if(!isset($leave['workingdays'])) {
        $leave['workingdays'] = count_workingdays($leave['uid'], $leave['fromDate'], $leave['toDate'], $is_wholeday);
    }

    if($db->num_rows($query) > 0) {
        $stats = $db->fetch_assoc($query);

        $db->update_query('leavesstats', array('daysTaken' => ($stats['daysTaken'] + $leave['workingdays'])), 'lsid='.$stats['lsid']);
    }
    else {

        $data = array(
                'uid' => $leave['uid'],
                'ltid' => $leave['type'],
                'year' => $leave_year,
                'daysTaken' => $leave['workingdays'],
        );

        $affiliate_policy = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."affiliatesleavespolicies WHERE ltid='".$db->escape_string($leave['type'])."' AND affid='{$leave_user[affid]}'"));

        $employment_year = date('Y', $core->user['joinDate']); // LEAVE USER
        $employment_year = 2009;
        $current_year = date('Y', TIME_NOW);
        $working_years = $current_year - $employment_year;

        $promotion_policy = unserialize($affiliate_policy['promotionPolicy']);
        ksort($promotion_policy);

        if($working_years < key($promotion_policy)) {
            if($working_years >= $affiliate_policy['entitleAfter']) {
                $data['entitledFor'] = $affiliate_policy['basicEntitlement'];
            }
            else {
                $data['entitledFor'] = 0;
            }
        }
        else {
            while(list($key, $val) = each($promotion_policy)) {
                if($working_years >= $key) {
                    $data['entitledFor'] = $affiliate_policy['basicEntitlement'] + $val;
                }
            }
        }

        $query2 = $db->query("SELECT * FROM ".Tprefix."leavesstats WHERE uid='".$db->escape_string($leave['uid'])."' AND year!='".$db->escape_string($leave_year)."' AND year>='".($leave_year - $affiliate_policy['canAccumulateFor'])."' AND ltid='".$db->escape_string($leave['type'])."'");
        if($db->num_rows($query2)) {
            while($prev_leaves = $db->fetch_assoc($query2)) {
                if(($leave_year - $prev_leaves['year']) == 1) {
                    $data['remainPrevYear'] = $prev_leaves['entitledFor'] - $prev_leaves['daysTaken'];
                    $data['canTake'] += $data['remainPrevYear'] + $prev_leaves['remainPrevYear'];
                }
                else {
                    if(($leave_year - $prev_leaves['year']) > 2) {
                        $data['canTake'] += $prev_leaves['remainPrevYear'];
                    }
                }
            }
        }

        $data['canTake'] += $data['entitledFor'];
        $db->insert_query('leavesstats', $data);
    }
}

function parse_type($type) {
    global $db, $lang;

    $leavetype_details = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."leavetypes WHERE ltid='".$db->escape_string($type)."'"));
    if(!empty($lang->{$leavetype_details['name']})) {
        $leavetype_details['title'] = $lang->{$leavetype_details['name']};
    }
    return $leavetype_details;
}

function count_workingdays($uid, $check_dates_start, $check_dates_end, $is_wholeday = true, $limited = false) {
    global $db;

    if($is_wholeday == false || $is_wholeday == 0) {
        return 0.5;
    }

    $reached_last_day == false;
    $date_being_checked = '';
    $count_working_days = 0;

    if($limited == true) {
        $query_andor = ' AND ';
        $query_ornull = '';
    }
    else {
        $query_andor = ' OR ';
        $query_ornull = ' OR es.fromDate IS NULL';
    }

    $query = $db->query("SELECT ws.weekDays, es.fromDate, es.toDate FROM ".Tprefix."employeesshifts es JOIN ".Tprefix."workshifts ws ON (ws.wsid=es.wsid) WHERE es.uid='".$db->escape_string($uid)."' AND ((".$db->escape_string($check_dates_start)." BETWEEN es.fromDate AND es.toDate){$query_andor}(".$db->escape_string($check_dates_end)." BETWEEN es.fromDate AND es.toDate){$query_ornull})");
    if($db->num_rows($query) == 1) {
        list($workdays) = $db->fetch_array($query);
        $workdays = unserialize($workdays);
    }
    elseif($db->num_rows($query) > 1) {
        while($workshift = $db->fetch_assoc($query)) {
            if($check_dates_start >= $workshift['fromDate'] && $check_dates_end <= $workshift['toDate']) {
                $workdays = unserialize($workshift['weekDays']);
                break;
            }
            else {
                if(empty($workshift['toDate'])) {
                    $workshift['toDate'] = $check_dates_end;
                }

                if($check_dates_start >= $workshift['fromDate'] && $check_dates_end >= $workshift['toDate']) {
                    $count_working_days += count_workingdays($uid, $check_dates_start, $workshift['toDate'], true, true);
                }
                elseif($check_dates_start <= $workshift['fromDate'] && $check_dates_end <= $workshift['toDate']) {
                    $count_working_days += count_workingdays($uid, $workshift['fromDate'], $check_dates_end, true, true);
                }
                elseif($check_dates_start <= $workshift['fromDate'] && $check_dates_end >= $workshift['toDate']) {
                    $count_working_days += count_workingdays($uid, $workshift['fromDate'], $workshift['toDate'], true, true);
                }
            }
        }
        return $count_working_days;
    }
    else {
        $user = new Users($uid);
        error('No workshift specified for user ('.$user->displayName.')');
    }

    while($reached_last_day == false) {
        if(empty($date_being_checked)) {
            $date_being_checked = $check_dates_start;
        }

        if(in_array(date('N', $date_being_checked), $workdays)) {
            $count_working_days++;
        }

        $date_being_checked = $date_being_checked + (60 * 60 * 24);
        if($date_being_checked > $check_dates_end) {
            $reached_last_day = true;
        }
    }

    $count_working_days -= count_holidays($uid, $check_dates_start, $check_dates_end);

    return $count_working_days;
}

function count_holidays($uid, $check_dates_start, $check_dates_end, $reoccurring_only = false, $specificyear_only = false) {
    global $db;

    $user = $db->fetch_assoc($db->query("SELECT ae.affid
										FROM ".Tprefix."affiliatedemployees ae
    									WHERE ae.isMain=1 AND ae.uid='".$db->escape_string($uid)."'"));
    $date_info['start'] = getdate($check_dates_start);
    $date_info['end'] = getdate($check_dates_end);
    $weekends = get_weekends($uid, $check_dates_start, $check_dates_end);
    $holidays_count = 0;

    if($date_info['start']['year'] == $date_info['end']['year']) {
        if($reoccurring_only == false && $specificyear_only == false) {
            $year_querystring = ' AND (year=0 OR year='.$date_info['start']['year'].')';
        }
        else {
            if($reoccurring_only == true) {
                $year_querystring = ' AND year=0';
            }
            elseif($specificyear_only == true) {
                $year_querystring = ' AND year='.$date_info['start']['year'];
            }
        }

        if($date_info['end']['mon'] == $date_info['start']['mon']) {
            $day_querystring = ' AND day BETWEEN '.$date_info['start']['mday'].' AND '.$date_info['end']['mday'];
        }
        else {
            $month_check = $date_info['start']['mon'];
            $day_querystring_or = '';
            while($month_check <= $date_info['end']['mon']) {
                if($month_check == $date_info['start']['mon']) {
                    $day_querystring .= $day_querystring_or.'(month='.$month_check.' AND (day BETWEEN '.$date_info['start']['mday'].' AND 31))';
                    $day_querystring_or = ' OR ';
                }
                elseif($month_check == $date_info['end']['mon']) {
                    $day_querystring .= $day_querystring_or.'(month='.$month_check.' AND (day BETWEEN 1 AND '.$date_info['end']['mday'].'))';
                    $day_querystring_or = ' OR ';
                }
                else {
                    $day_querystring .= $day_querystring_or.'(month='.$month_check.' AND (day BETWEEN 1 AND 31))';
                    $day_querystring_or = ' OR ';
                }
                $month_check++;
            }
            if(!empty($day_querystring)) {
                $day_querystring = ' AND ('.$day_querystring.')';
            }
        }

        $query = $db->query("SELECT * FROM ".Tprefix."holidays
					WHERE affid='{$user[affid]}' AND (((validFrom = 0 OR ({$date_info[start][year]} >= FROM_UNIXTIME(validFrom, '%Y') AND month >= FROM_UNIXTIME(validFrom, '%m') AND day >= FROM_UNIXTIME(validFrom, '%d'))) AND (validTo=0 OR ({$date_info[end][year]} <= FROM_UNIXTIME(validTo, '%Y') AND month <= FROM_UNIXTIME(validTo, '%m') AND day <= FROM_UNIXTIME(validTo, '%d'))))
					AND (month BETWEEN {$date_info[start][mon]} AND {$date_info[end][mon]}) AND hid NOT IN (SELECT hid FROM ".Tprefix."holidaysexceptions WHERE uid=".intval($uid).") {$day_querystring} {$year_querystring})");
        while($holiday = $db->fetch_assoc($query)) {
            if($holiday['year'] == 0) {
                $holiday['year'] = $date_info['start']['year'];
            }
            if(!isset($weekends[$holiday['year']][$holiday['month']][$holiday['day']])) {
                $holidays_count += $holiday['numDays'];
            }
            else {
                if($holiday['numDays'] > 1) {
                    $i = 0;
                    $holidays_count += $holiday['numDays'];
                    while($i < $holiday['numDays']) {
                        if(isset($weekends[$holiday['year']][$holiday['month']][$holiday['day'] + $i])) {
                            $holidays_count -= 1;
                        }
                        $i++;
                    }
                }
            }
        }
    }
    else {
        $year_check = $date_info['start']['year'];
        $repeated_leaves = 0;
        while($year_check <= $date_info['end']['year']) {
            if($year_check == $date_info['start']['year']) {
                $holidays_count += count_holidays($uid, $check_dates_start, mktime(23, 59, 0, 12, 31, $date_info['start']['year']));
            }
            elseif($year_check == $date_info['end']['year']) {
                $holidays_count += count_holidays($uid, mktime(0, 0, 0, 1, 1, $date_info['end']['year']), $check_dates_end);
            }
            else {
                if(empty($repeated_leaves)) {
                    $repeated_leaves = count_holidays($uid, mktime(0, 0, 0, 1, 1, $year_check), mktime(23, 59, 0, 12, 31, $year_check), true);
                }
                $holidays_count += $repeated_leaves;
                $holidays_count += count_holidays($uid, mktime(0, 0, 0, 1, 1, $year_check), mktime(23, 59, 0, 12, 31, $year_check), false, true);
            }
            $year_check++;
        }
    }
    return $holidays_count;
}

function get_weekends($uid, $check_dates_start, $check_dates_end, $limited = false) {
    global $db;

    $reached_last_day == false;
    $date_being_checked = '';
    $weekends = array();

    if($limited == true) {
        $query_andor = ' AND ';
        $query_ornull = '';
    }
    else {
        $query_andor = ' OR ';
        $query_ornull = ' OR es.fromDate IS NULL';
    }

    $query = $db->query("SELECT ws.weekDays, es.fromDate, es.toDate FROM ".Tprefix."employeesshifts es JOIN ".Tprefix."workshifts ws ON (ws.wsid=es.wsid) WHERE es.uid='".$db->escape_string($uid)."' AND ((".$db->escape_string($check_dates_start)." BETWEEN es.fromDate AND es.toDate){$query_andor}(".$db->escape_string($check_dates_end)." BETWEEN es.fromDate AND es.toDate){$query_ornull})");
    if($db->num_rows($query) == 1) {
        list($workdays) = $db->fetch_array($query);
        $workdays = unserialize($workdays);
    }
    elseif($db->num_rows($query) > 1) {
        while($workshift = $db->fetch_assoc($query)) {
            if($check_dates_start >= $workshift['fromDate'] && $check_dates_end <= $workshift['toDate']) {
                $workdays = unserialize($workshift['weekDays']);
                break;
            }
            else {
                if(empty($workshift['toDate'])) {
                    $workshift['toDate'] = $check_dates_end;
                }

                if($check_dates_start >= $workshift['fromDate'] && $check_dates_end >= $workshift['toDate']) {
                    $weekends = array_merge($weekends, get_weekends($uid, $check_dates_start, $workshift['toDate'], true));
                }
                elseif($check_dates_start <= $workshift['fromDate'] && $check_dates_end <= $workshift['toDate']) {
                    $weekends = array_merge($weekends, get_weekends($uid, $workshift['fromDate'], $check_dates_end, true));
                }
                elseif($check_dates_start <= $workshift['fromDate'] && $check_dates_end >= $workshift['toDate']) {
                    $weekends += array_merge($weekends, get_weekends($uid, $workshift['fromDate'], $workshift['toDate'], true));
                }
            }
        }
        return $weekends;
    }
    else {
        error('No workshift specified for user');
    }

    while($reached_last_day == false) {
        if(empty($date_being_checked)) {
            $date_being_checked = $check_dates_start;
        }

        if(!in_array(date('N', $date_being_checked), $workdays)) {
            //$weekends['count']++;
            $current_date_info = getdate($date_being_checked);
            $weekends[$current_date_info['year']][$current_date_info['mon']][$current_date_info['mday']] = $current_date_info;
        }

        $date_being_checked = $date_being_checked + (60 * 60 * 24);
        if($date_being_checked >= $check_dates_end) {
            $reached_last_day = true;
        }
    }
    return $weekends;
}

function parse_additonalfield($attribute, $field_settings) {
    global $db, $core, $lang, $leave;
    $field = '';

    switch($field_settings['type']) {
        case 'inline-search':
            $identifier = uniqid(TIME_NOW);

            if($attribute == 'cid') {
                $search_for = 'customer';
            }
            elseif($attribute == 'spid') {
                $search_for = 'supplier';
            }

            $field = '<input type="text" id="'.$search_for.'_'.$identifier.'_autocomplete" value="'.$field_settings['value_attribute_value'].'" required="required"/><input type="text" size="3" id="'.$search_for.'_'.$identifier.'_id_output" value="'.$field_settings['key_attribute_value'].'" disabled /><input type="hidden" value="'.$field_settings['key_attribute_value'].'" id="'.$search_for.'_'.$identifier.'_id" name="'.$attribute.'" /><div id="searchQuickResults_'.$identifier.'" class="searchQuickResults" style="display:none;"></div>';
            break;
        case 'select':
            if($field_settings['datasource'] == 'db') {
                if(isset($field_settings['table'], $field_settings['attributes'])) {
                    if(isset($field_settings['where'])) {
                        if($field_settings['affid_validation'] == true) {
                            if(empty($field_settings['uid'])) {
                                $field_settings['uid'] = $core->input['uid'];
                            }
                            $field_settings['affids'] = implode(', ', get_specificdata('affiliatedemployees', array('affid'), 'affid', 'affid', '', 0, 'uid='.$db->escape_string($field_settings['uid'])));
                        }

                        if(isset($leave['fromDate_formatted'])) {
                            $leave['fromDate'] = $leave['fromDate_output'];
                        }
                        $leave['fromDate'] = strtotime($leave['fromDate']);
                        if(isset($leave['toDate_formatted'])) {
                            $leave['toDate'] = $leave['toDate_output'];
                        }
                        $leave['toDate'] = strtotime($leave['toDate']);
                        eval("\$field_settings[where] = \"".$field_settings['where']."\";");
                    }

                    $data = get_specificdata($field_settings['table'], $field_settings['attributes'], $field_settings['key_attribute'], $field_settings['value_attribute'], array('by' => $field_settings['value_attribute'], 'sort' => 'ASC'), 0, $field_settings['where']);
                    if(is_array($data)) {
                        $field = parse_selectlist($attribute, 0, $data, $field_settings['key_attribute_value'], $field_settings['mulitpleselect'], '', array('required' => true));
                    }
                    else {
                        $field = '<span class="red_text">'.$lang->{$field_settings['errorlang_nodata']}.'</span>';
                    }
                }
                else {
                    break;
                }
            }
            break;
        default: break;
    }

    return $field;
}

function parse_additionaldata($leave, $field_settings, $mainonly = 0, $source = 'edit') {
    global $db, $lang;
    $field_settings = unserialize($field_settings);
    if(is_array($field_settings)) {
        foreach($field_settings as $key => $val) {
            if($mainonly == 1 && (!isset($val['isMain']) || empty($val['isMain']))) {
                continue;
            }
            if(isset($leave[$key])) {
                if($val['datasource'] == 'db') {
                    $key_attribute = $key;
                    if(isset($val['altkey_attribute'])) {
                        $key_attribute = $val['altkey_attribute'];
                    }
                    if(empty($leave[$key])) {
                        return false;
                    }

                    $output = $db->fetch_field($db->query("SELECT ".$db->escape_string($val['value_attribute'])." FROM ".Tprefix.$db->escape_string($val['table'])." WHERE {$val[key_attribute_prefix]}{$key_attribute}='{$leave[$key]}'"), $val['value_attribute']);
                }
                /*  This option will call the parse segment
                 * function based on the funcntion name passed from the
                 * configuration array
                 *
                 * */
                elseif($val['datasource'] == 'function') {
                    unset($val['key_attribute_value'], $val['type'], $val['table']);
                    $object = get_object_bytype($key, $leave[$key]);
                    $output = $object->get()[$val['value_attribute']];
                }

                if(!empty($output)) {
                    if(isset($val['titlelangvar']) && $source != 'edit') {
                        $output = '<br />'.$lang->{$val['titlelangvar']}.': '.$output;
                    }
                }
                $additionaldata[] = $output;
                unset($output);
            }
        }
        return $additionaldata;
    }
    return false;
}

function parse_toinform_list($uid = '', $checked = '', $leavetype_details = array()) {
    global $core, $db;

    if(empty($uid)) {
        $uid = $core->user['uid'];
    }

    $query = $db->query("SELECT affid, isMain FROM ".Tprefix."affiliatedemployees WHERE uid='".$db->escape_string($uid)."'");
    while($assigned_to = $db->fetch_assoc($query)) {
        $assigned_to_query_affids .= $comma.$assigned_to['affid'];
        $comma = ', ';
        if($assigned_to['isMain'] == 1) {
            $main_affiliate = $assigned_to['affid'];
        }
    }

    $assigned_to_query = ' WHERE affid IN ('.$assigned_to_query_affids.')';
    $mailinglists_attr = 'altMailingList';
    if($leavetype_details['isBusiness'] == 1) {
        $mailinglists_attr = 'mailingList';
    }

    $query = $db->query("SELECT affid, name, {$mailinglists_attr} FROM ".Tprefix."affiliates{$assigned_to_query} ORDER BY name ASC");
    $to_inform_counter = 2;
    $column_num = 1;
    while($affiliate = $db->fetch_assoc($query)) {
        if(empty($affiliate[$mailinglists_attr])) {
            continue;
        }
        $checkbox = $is_checked = '';
        if($main_affiliate != $affiliate['affid']) {
            if(is_array($checked)) {
                if(in_array($affiliate['affid'], $checked)) {
                    $is_checked = ' checked="checked"';
                }
            }

            $checkbox = '<input type="checkbox" value="'.$affiliate['affid'].'" name="affToInform[]"'.$is_checked.' /> ';
        }
        else {
            if($leavetype_details['noNotification'] == 0) {
                $checkbox = ' <img src="images/valid.gif" alt="&raquo;"/> <input type="hidden" value="'.$affiliate['affid'].'" name="affToInform[]" />';
            }
            else {
                if(is_array($checked)) {
                    if(in_array($affiliate['affid'], $checked)) {
                        $is_checked = ' checked="checked"';
                    }
                }
                $checkbox = '<input type="checkbox" value="'.$affiliate['affid'].'" name="affToInform[]"'.$is_checked.' /> ';
            }
        }
        if($to_inform_counter % 2 == 0) {
            $column_num = 1;
        }
        else {
            $column_num = 2;
        }
        $to_inform[$column_num] .= $checkbox.$affiliate['name'].'<br />';

        $to_inform_counter++;
    }
    return '<div style="display:inline-block; width:50%">'.$to_inform[1].'</div><div style="display:inline-block; width:50%">'.$to_inform[2].'</div>';
}

?>