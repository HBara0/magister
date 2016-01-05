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

function parse_attendance_reports($core, $headerinc = '', $header = '', $menu = '', $footer = '') {
    global $db, $template, $log, $lang;
    $cache = new Cache;
    if(!$core->input['output'] == 'email') {
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
    }
    else {
        $fromdate = $core->input['fromDate'];
        $todate = $core->input['toDate'];
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
            if($user_obj->gid == 7) {
                continue;
            }

            $user_affiliate = $user_obj->get_mainaffiliate();
            if(!is_object($user_affiliate) || empty($user_affiliate->affid)) {
                continue;
            }
            if($cache->incache('norecordsaffiliates', $user_affiliate->affid)) {
                continue;
            }
            else {
                if(!$cache->incache('affiliateshasrecords', $user_affiliate->affid)) {
                    $affhasrecords = $user_affiliate->has_attendancerecords();
                    if($affhasrecords) {
                        $cache->add('affiliateshasrecords', $user_affiliate->affid);
                    }
                    else {
                        $cache->add('norecordsaffiliates', $user_affiliate->affid);
                        continue;
                    }
                }
            }
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
        if(!empty($holidays_query_where)) {
            $holidays_query_where = ' AND ('.$holidays_query_where.') ';
        }
        if(!empty($core->user['mainaffiliate'])) {
            $holiday_query = $db->query("SELECT *
                                        FROM ".Tprefix."holidays
                                        WHERE affid = {$core->user[mainaffiliate]} ".$holidays_query_where."
                                        AND hid NOT IN (SELECT hid FROM ".Tprefix."holidaysexceptions WHERE uid={$uid})");


            while($holiday = $db->fetch_assoc($holiday_query)) {
                if($holiday['year'] == 0) {
                    if($todate_details['year'] != $currentdate_details['year']) {
                        for($year = $currentdate_details['year']; $year <= $todate_details['year']; $year++) {
                            $holiday['year'] = $year;
                            parse_holiday($holiday, $data, $fromdate, $todate);
                        }
                    }
                    else {
                        $holiday['year'] = $currentdate_details['year'];
                        parse_holiday($holiday, $data, $fromdate, $todate);
                    }
                }
                else {
                    parse_holiday($holiday, $data, $fromdate, $todate);
                }
            }
            /* Check for holidays in period - END */
        }
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
        if(!is_array($worshifts)) {
            continue;
        }
        /* Check for the attendance during the period - END */
        if($core->input['referrer'] == 'report') {

            /* Loop over all days of period - START */
            while($currentdate <= $to) {
                $curdate = getdate_custom($currentdate);
                if($curdate['mday'] == $previousdaynum) {
                    $prevdate = $currentdate;
                    $currentdate += 86400;/** increment  by one day (timestamp) * */
                    continue;
                }
                $previousdaynum = $curdate['mday'];
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
                        $rowclass = 'style="background-color: #F9D0D0;"';
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
                    if(($currentdate + 86400) >= $to || ($currentdate + 86400) > TIME_NOW) {/* FIX HERE  */
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
                if($curdate['mday'] == $previousdaynum) {
                    $prevdate = $currentdate;
                    $currentdate += 86400;/** increment  by one day (timestamp) * */
                    continue;
                }
                $previousdaynum = $curdate['mday'];
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

                    /* Loop Through the Worshifts - END */
                    $total_days++;
                    if(isset($data[$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']])) {
                        $filled = '';
                        foreach($data[$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] as $type => $day_data) {
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
                                        $extra_style = 'background-color:#F9D0D0';
                                        $month_header[$curdate['mon']][date('d', $currentdate)] = date('d', $currentdate);
                                        $total['absent'][$curdate['year']][$curdate['mon']] ++;
                                        $total['requiredhours'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] = (($current_worshift['offDutyHour'] * 3600) + ($current_worshift['offDutyMinutes'] * 60)) - (($current_worshift['onDutyHour'] * 3600) + ($current_worshift['onDutyMinutes'] * 60));
                                    }
                                    elseif(empty($attendance['timeIn']) || !isset($attendance['timeIn']) || empty($attendance['timeOut']) || !isset($attendance['timeOut'])) {
                                        $day_content_value .= '?';
                                        $extra_style = 'background-color:#fcefa1';
                                        $month_header[$curdate['mon']][date('d', $currentdate)] = date('d', $currentdate);
                                        $attending_days++;
                                        if(empty($attendance['timeIn']) || !isset($attendance['timeIn'])) {
                                            $attendance['timeIn'] = mktime($current_worshift['onDutyHour'], $current_worshift['onDutyMinutes'], 0, $curdate['mon'], $curdate['mday'], $curdate['year']);
                                        }

                                        if(empty($attendance['timeOut']) || !isset($attendance['timeOut'])) {
                                            $attendance['timeOut'] = mktime($current_worshift['offDutyHour'], $current_worshift['offDutyMinutes'], 0, $curdate['mon'], $curdate['mday'], $curdate['year']);
                                        }
                                        $attendance['arrival'] = $attendance['timeIn'] - (mktime($current_worshift['onDutyHour'], $current_worshift['onDutyMinutes'], 0, $curdate['mon'], $curdate['mday'], $curdate['year']));
                                        $attendance['departure'] = $attendance['timeOut'] - (mktime($current_worshift['offDutyHour'], $current_worshift['offDutyMinutes'], 0, $curdate['mon'], $curdate['mday'], $curdate['year']));
                                        $attendance['hoursday'] = ($attendance['timeOut']) - ( $attendance['timeIn']);
                                        $attendance['deviation'] = $attendance['departure'] - $attendance['arrival'];
                                        $total['actualhours'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] = $attendance['hoursday'];
                                        $total['deviation'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] = $attendance['deviation'];
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
                                            $day_content_value .= number_format($workperc, 0).'</br>'.$extra.' ';
                                            $extra_style = 'background-color:#D6EAAC';
                                        }
                                        else {
                                            $day_content_value .= number_format($workperc, 0);
                                            $extra_style = 'background-color:#fcefa1';
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
                                $ishalfday = 0;
                                foreach($day_data as $leave) {
                                    $month_header[$curdate['mon']][date('d', $currentdate)] = date('d', $currentdate);
                                    $leave_obj = new Leaves($leave['lid']);
                                    $leavetype = $leave_obj->get_leavetype(false);
                                    if($leavetype->isUnpaid == 1) {
                                        $day_content_value .= 'UL';
                                    }
                                    elseif($leavetype->isWholeDay == 0) {
                                        $ishalfday = '1';
                                        $day_content_value .= $leavetype->symbol.' ';
                                    }
                                    else {
                                        $day_content_value .= 'L';
                                    }

                                    unset($leavetype);
                                }
                                if(empty($ishalfday)) {
                                    $filled = 1;
                                }
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
                                if(is_array($worshifts)) {
                                    if(in_array($curdate['wdayiso'], $workshift['weekDays'])) {
                                        $total['holidays'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] ++;
                                    }
                                }
                            }
                        }
                    }
                    else {
                        if(in_array($curdate['wdayiso'], $workshift['weekDays'])) {
                            $day_content_value .= '0%';
                            $extra_style = 'background-color:#F9D0D0';
                            $month_header[$curdate['mon']][date('d', $currentdate)] = date('d', $currentdate);
                            $total['absent'][$curdate['year']][$curdate['mon']] ++;
                            $total['requiredhours'][$curdate['year']][$curdate['mon']][$curdate['week']][$curdate['mday']] = (($current_worshift['offDutyHour'] * 3600) + ($current_worshift['offDutyMinutes'] * 60)) - (($current_worshift['onDutyHour'] * 3600) + ($current_worshift['onDutyMinutes'] * 60));
                        }
                    }
                    if(!in_array($curdate['wdayiso'], $workshift['weekDays'])) {
                        $weekends++;
                        $total['weekends'][$curdate['year']][$curdate['mon']][$curdate['week']] ++;
                        $day_content_value .= ' W/E';
                        $month_header[$curdate['mon']][date('d', $currentdate)] = date('d', $currentdate);
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

                    $day_content .= '<td style="width:2%;text-align:center;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;'.$extra_style.'">'.$day_content_value.'</td>';
                    $day_content_value = $extra_style = '';
                    if($nextdate_details['mon'] != $curdate['mon'] || ($currentdate + 86400) >= $to || ($currentdate + 86400) > TIME_NOW) {
                        $month_output = date('F', mktime(0, 0, 0, $curdate['mon'], 10));
                        $total_outputs['month']['actualhours'] = operation_time_value(array_sum_recursive($total['actualhours'][$curdate['year']][$curdate['mon']]));
                        $total_outputs['month']['requiredhours'] = operation_time_value(array_sum_recursive($total['requiredhours'][$curdate['year']][$curdate['mon']]));

                        $day_content .= '<td style="width:50px;text-align:center;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">'.$attending_days.' / '.($total_days).'</td>';
                        if($total_outputs['month']['requiredhours'] > 0) {
                            $day_content .= '<td style="width:25px;text-align:center;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">'.number_format(($total_outputs['month']['actualhours'] / $total_outputs['month']['requiredhours']) * 100, 0).'</td>';
                            $day_content .= '<td style="width:85px;text-align:center;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">'.$total_outputs['month']['actualhours'].' / '.$total_outputs['month']['requiredhours'].'</td>';
                        }
//$attendance_report_user_month .= 'a'.$nextdate_details['week'].' == '.$curdate['week'].' && '.$nextdate_details['mon'].' != '.$curdate['mon'];

                        eval("\$attendance_report_users{{$curdate['mon']}} .= \"".$template->get('attendance_log_month_user')."\";");
                        $month_header_output = '<th style="width:150px;text-align:center;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">'.$lang->employeename.'</th><th style="border-left: 1px solid #000;border-right: 1px solid #000;">'.implode('</th><th style="border-left: 1px solid #000;border-right: 1px solid #000;">', $month_header[$curdate['mon']]).'</th>';
                        $month_header_output .= '<th style="width:50px;text-align:center;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">'.$lang->capstotal.'</th>';
                        $month_header_output .= '<th style="width:25px;text-align:center;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">%</th>';
                        $month_header_output .= '<th style="width:85px;text-align:center;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;">'.$lang->capstotalhour.'</th>';
                        if(!$core->input['output'] == 'email') {
                            $monthclass = 'class="datatable-striped"';
                        }
                        eval("\$attendance_report_user_month[{$curdate['mon']}] = \"".$template->get('attendance_log_month')."\";");
                        unset($monthclass, $month_header, $month_header_output, $total, $total_days, $attending_days, $weekends, $data[$curdate['year']][$curdate['mon']]);
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
    if($core->input['output'] == 'email') {
        $message = "
          <h1>{$lang->attendancelog}
                <small><br />{$lang->fromdate} {$report[fromdate_output]} {$lang->todate} {$report[todate_output]}</small>
            </h1>
            <span> < : {$lang->arrivearly} | > : {$lang->leavelater} | <> : {$lang->earlyandlate} | H: {$lang->holiday} | W/E : {$lang->weekend} | L : {$lang->leave} | UL : {$lang->unpaidleave}</span>
            </hr>
            <div>
                {$output}
            </div>";
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
    else {
        output_page($generatepage);
    }
}

function parse_holiday($holiday, &$data, $fromdate, $todate) {

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

function reinitialize_balance($user, $type, $prevbalance = null) {
    global $db;
    $affiliate = $user->get_mainaffiliate();
    /* Temporary specific fix for time zone */
    date_default_timezone_set($affiliate->get_country()->defaultTimeZone);

    $hr_info = $user->get_hrinfo();
    if(empty($hr_info['joinDate'])) {
        return;
    }

    $leaves_objs = Leaves::get_data('uid='.$user->uid.' AND (type='.intval($type).' OR type IN (SELECT ltid FROM leavetypes WHERE countWith='.intval($type).'))', array('order' => array('by' => 'fromDate', 'sort' => 'ASC'), 'returnarray' => true));
    if(is_array($leaves_objs)) {
        foreach($leaves_objs as $leave) {
            //$existing_stats = LeavesStats::get_data('uid='.$user->uid.' AND ltid='.$leave->get_type()->ltid.' AND (('.$leave->fromDate.' BETWEEN periodStart AND periodEnd) OR ('.$leave->toDate.' BETWEEN periodStart AND periodEnd))', array('returnarray' => true));
            //if(!is_array($existing_stats)) {
            if(!$leave->is_approved()) {
                continue;
            }
            $leaves[$leave->lid] = $leave->get();
            // }
        }
    }

    $existing_stats = LeavesStats::get_data(array('uid' => $user->get_id(), 'ltid' => $type), array('returnarray' => true));
    if(is_array($existing_stats)) {
        foreach($existing_stats as $existing_stat) {
            $existing_stat->delete();
        }
    }
    if(is_array($leaves)) {
        $db->update_query(AttendanceAddDays::TABLE_NAME, array('isCounted' => 0), 'uid='.$user->get_id());
        $prevbalanceset = false;
        foreach($leaves as $leave) {
            $stat = new LeavesStats();
            $stat->generate_periodbased($leave);
            /* Update the first stat with prev balance */
            if($prevbalanceset == false) {
                $existing_stat = LeavesStats::get_data(array('uid' => $user->get_id(), 'ltid' => $type), array('order' => array('sort' => 'ASC', 'by' => 'periodStart'), 'limit' => '0, 1'));
                if(is_object($existing_stat)) {
                    $leavepolicy = AffiliatesLeavesPolicies::get_data(array('affid' => $affiliate->affid, 'ltid' => $type));
                    if(is_object($leavepolicy)) {
                        if(!empty($prevbalance)) {
                            if($prevbalance > $leavepolicy->maxAccumulateDays) {
                                $remainprevyear = $leavepolicy->maxAccumulateDays;
                            }
                            else {
                                $remainprevyear = $prevbalance;
                            }

                            $existing_stat->set(array('remainPrevYear' => $remainprevyear, 'canTake' => $existing_stat->canTake + $remainprevyear));
                            $existing_stat->save();
                            unset($remainprevyear);
                        }
                        $prevbalanceset = true;
                    }
                }
            }

            /* Count additional Days */
            $adddays = AttendanceAddDays::get_data(array('uid' => $user->get_id(), 'isApproved' => 1, 'isCounted' => 0), array('simple' => false, 'returnarray' => true));
            if(is_array($adddays)) {
                foreach($adddays as $addday) {
                    $addday->update_leavestats();
                }
            }
        }
    }
}

?>