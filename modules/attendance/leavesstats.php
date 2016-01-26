<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Get Leaves Balance
 * $module: attendance
 * $id: leavesstats.php
 * Created:		@zaher.reda		January 03, 2010 | 10:51 AM
 * Last Update: @tony.assaad    March   08, 2012 | 2:00  PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $sort_query = 'uid ASC, periodStart ASC';
    if(isset($core->input['sortby'], $core->input['order'])) {
        $sort_query = $core->input['sortby'].' '.$core->input['order'];
    }
    $sort_url = sort_url();

    $limit_start = 0;
    if(isset($core->input['start'])) {
        $limit_start = $db->escape_string($core->input['start']);
    }

    if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
        $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
    }


    if(!isset($core->input['type'])) {
        $core->input['type'] = 1;
    }

    /* Parse types list - START */
    $query = $db->query("SELECT * FROM ".Tprefix."leavetypes WHERE countWith=0 AND noBalance=0 ORDER BY name ASC");
    while($type = $db->fetch_assoc($query)) {
        if(!empty($lang->{$type['name']})) {
            $type['title'] = $lang->{$type['name']};
        }
        if(!empty($type['description'])) {
            $type['description'] = ' ('.$type['description'].')';
        }
        $leave_types[$type['ltid']] = $type['title'].$type['description'];
    }

    /* Exceptional: Hide the sick leaves */
    unset($leave_types[3]);
    $types_list = parse_selectlist('type', 1, $leave_types, $core->input['type'], 0, 'goToURL("index.php?module=attendance/leavesstats&amp;type="+$(this).val())');
    /* Parse types list - END */

    if(!isset($core->input['uid'])) {
        if($core->usergroup['attendance_canViewAllLeaves'] == 0) {
            //$where = ' WHERE (".TIME_NOW." BETWEEN periodStart AND periodEnd) AND ';
            $uid_where = ' ls.uid="'.$core->user['uid'].'"';
            if($core->usergroup['attendance_canViewAffAllLeaves'] == 1) {
                $query = $db->query("SELECT u.uid FROM ".Tprefix."users u JOIN ".Tprefix."affiliatedemployees ae ON (u.uid=ae.uid) WHERE isMain=1 AND (affid IN (select affid FROM ".Tprefix." affiliatedemployees WHERE canHR=1 AND uid='{$core->user[uid]}') OR affid='{$core->user[mainaffiliate]}')");
                if($db->num_rows($query) > 1) {
                    while($user = $db->fetch_assoc($query)) {
                        $users[] = $user['uid'];
                    }

                    $uid_where .= ' OR ls.uid IN ('.implode(',', $users).')';
                }

                //$period_where = '('.TIME_NOW.' BETWEEN periodStart AND periodEnd) AND ';
            }
            else {
                $period_where = '';
            }
            $reporting_users = get_specificdata('users', 'uid', 'uid', 'uid', '', 0, "reportsTo='{$core->user[uid]}'");
            if(is_array($reporting_users) && !empty($reporting_users)) {
                $uid_where .= ' OR ls.uid IN ('.implode(',', $reporting_users).')';
                if(empty($period_where)) {
                    //$period_where = '('.TIME_NOW.' BETWEEN periodStart AND periodEnd) AND ';
                }
            }

            $where = $period_where.' ('.$uid_where.')';
        }
        else {
            //$where = '('.TIME_NOW.' BETWEEN periodStart AND periodEnd)';
        }
    }
    else {
        $where = 'ls.uid="'.$db->escape_string($core->input['uid']).'"';
    }

    $multipage_where = 'ltid="'.$db->escape_string($core->input['type']).'"'; //" AND '.$where;
    if(!empty($where)) {
        $where = ' AND '.$where;
    }

    $query = $db->query("SELECT ls.*, Concat(u.firstName, ' ', u.lastName) AS employeename
						FROM ".Tprefix."leavesstats ls JOIN ".Tprefix."users u ON (ls.uid=u.uid)
						WHERE ltid='".$db->escape_string($core->input['type'])."'{$where}
						ORDER BY {$sort_query}
						LIMIT {$limit_start}, {$core->settings[itemsperlist]}");

    $number_stats = $db->num_rows($query);
    if($number_stats > 0) {
        date_default_timezone_set('UTC');
        while($stats = $db->fetch_assoc($query)) {
            $row_class = alt_row($row_class);
            $stats['periodStart_output'] = date($core->settings['dateformat'], $stats['periodStart']);
            $stats['periodEnd_output'] = date($core->settings['dateformat'], $stats['periodEnd']);
            $stats['balance'] = $stats['canTake'] - $stats['daysTaken'];
            $stats['finalBalance'] = $stats['balance'] + $stats['additionalDays'];

            if($number_stats == 1 && $stats['uid'] == $core->user['uid']) {
                $stats['employeename'] = '';
            }
            else {
                $stats['employeename'] = $stats['employeename'];
            }

            if($stats['additionalDays'] != 0) {
                $stats['additionalDays'] = '<a href="index.php?module=attendance/listaddleavedays&filterby=uid&filtervalue='.$stats['uid'].'">'.$stats['additionalDays'].'</a>';
            }

            eval("\$statslist .= \"".$template->get('attendance_leavesstats_row')."\";");
        }

        $multipages = new Multipages('leavesstats ls JOIN '.Tprefix.'users u ON (ls.uid=u.uid)', $core->settings['itemsperlist'], $multipage_where);
        $statslist .= '<tr><td colspan="9">'.$multipages->parse_multipages().'&nbsp;</td></tr>';
    }
    else {
        $statslist .= '<tr><td colspan="9">'.$lang->na.'</td></tr>';
    }

    if($core->usergroup['canUseHR'] == 1) {
        $additonaldays_link = '<a href="index.php?module=attendance/addadditionalleaves"><img src="images/addnew.png" border="0" alt="'.$lang->additionaldays.'"> '.$lang->addadditionalbalance.'</a>';
    }

    eval("\$leavesstats = \"".$template->get('attendance_leavesstats')."\";");
    output_page($leavesstats);
}
?>