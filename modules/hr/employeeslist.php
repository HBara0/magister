<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * List employees
 * $module: hr
 * $id: emplpoyeeslist.php
 * Created:		@najwa.kassem	October 21, 2010 | 9:37 AM
 * Last Update: @zaher.reda 	November 28, 2012 | 11:29 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canViewAllEmp'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $sort_query = 'fullname ASC';

    if(isset($core->input['sortby'], $core->input['order'])) {
        $sort_query = $db->escape_string($core->input['sortby']).' '.$db->escape_string($core->input['order']);
    }
    /* Check which affiliates user can see - START */
    if(!isset($core->input['affid']) || empty($core->input['affid'])) {
        $affid = $core->user['mainaffiliate'];
        if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
            if(is_array($core->user['hraffids']) && !empty($core->user['hraffids'])) {
                $affid = $core->user['mainaffiliate'];
                if(!in_array($core->user['mainaffiliate'], $core->user['hraffids'])) {
                    $affid = $core->user['hraffids'][current($core->user['hraffids'])];
                }
            }
            else {
                error($lang->sectionnopermission);
                exit;
            }
        }
    }
    else {
        $affid = $core->input['affid'];
        if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
            if(!in_array($core->input['affid'], $core->user['hraffids'])) {
                $affid = $core->user['mainaffiliate'];
                if(!in_array($core->user['mainaffiliate'], $core->user['hraffids'])) {
                    error($lang->sectionnopermission);
                    exit;
                }
            }
        }
    }
    /* Check which affiliates user can see - END */
    $sort_url = sort_url();
    $limit_start = 0;
    $multipage_where = ' affid='.$affid.' AND isMain=1 ';

    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('fullName', 'date', 'position', 'reportsTo'),
            ),
            'process' => array(
                    'filterKey' => 'uid',
                    'mainTable' => array(
                            'name' => 'users',
                            'filters' => array('reportsTo' => 'reportsTo'),
                            'extraSelect' => 'CONCAT(firstName, \' \', lastName) AS fullName',
                            'havingFilters' => array('fullName' => 'fullName')
                    ),
                    'secTables' => array(
                            'userhrinformation' => array(
                                    'filters' => array('date' => array('operatorType' => 'date', 'name' => 'joinDate')),
                            ),
                            'userspositions' => array(
                                    'filters' => array('position' => array('operatorType' => 'multiple', 'name' => 'posid')),
                            )
                    )
            )
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    $filters_row_display = 'hide';
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = 'AND u.'.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= 'AND u.'.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }
    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display), array('reportsTo'));

    /* Perform inline filtering - END */

    if(isset($core->input['start'])) {
        $limit_start = $db->escape_string($core->input['start']);
    }

    if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
        $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
    }

    $query = $db->query("SELECT u.uid, uhr.joinDate as joindate, CONCAT(firstName, ' ', lastName) as fullname
						FROM ".Tprefix."users u LEFT JOIN ".Tprefix."userhrinformation uhr ON (uhr.uid=u.uid) JOIN ".Tprefix."affiliatedemployees a ON (a.uid=u.uid)
						WHERE a.affid={$affid} AND isMain=1
						{$filter_where}
						ORDER BY {$sort_query}
						LIMIT {$limit_start}, {$core->settings[itemsperlist]}");

    if($db->num_rows($query) > 0) {
        while($user = $db->fetch_assoc($query)) {
            $row_class = alt_row($row_class);
            $user['positions'] = '';
            $position_query = $db->query("SELECT name, title FROM ".Tprefix."userspositions up JOIN ".Tprefix."positions p ON (up.posid=p.posid) WHERE up.uid={$user[uid]} ORDER BY title ASC");

            if($db->num_rows($position_query) > 0) {
                while($position = $db->fetch_assoc($position_query)) {
                    if(isset($lang->{$position['name']})) {
                        $position['title'] = $lang->{$position['name']};
                    }
                    $user['positions'] .= $position['title'].'<br />';
                }
            }
            else {
                $user['positions'] = '-';
            }

            $user['joindate_output'] = '-';
            if(!empty($user['joindate'])) {
                $user['joindate_output'] = date($core->settings['dateformat'], $user['joindate']);
            }

            eval("\$users_list .= \"".$template->get('hr_employeeslist_employeerow')."\";");
        }

        $multipages = new Multipages('users u JOIN '.Tprefix.'affiliatedemployees a ON (u.uid=a.uid)', $core->settings['itemsperlist'], $multipage_where);
        $users_list .= '<tr><td colspan="4">'.$multipages->parse_multipages().'</td></tr>';
    }
    else {
        $users_list = '<tr><td colspan="4">'.$lang->nomatchfound.'</td></tr>';
    }

    if($core->usergroup['hr_canHrAllAffiliates'] == 1) {
        $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'));
    }
    else {
        if(is_array($core->user['hraffids']) && !empty($core->user['hraffids']) && count($core->user['hraffids']) > 1) {
            $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 0, 'affid IN ('.implode(',', $core->user['hraffids']).')');
        }
    }

    if(is_array($affiliates)) {
        $affid_field = $lang->affiliate.': '.parse_selectlist('affid', 1, $affiliates, $affid, 0, 'goToURL("index.php?module=hr/employeeslist&amp;affid="+$(this).val())').'';
    }

    eval("\$listpage = \"".$template->get('hr_employeeslist')."\";");
    output_page($listpage);
}
else {
    if($core->input['action'] == 'manageworkshift') {
        if($core->usergroup['hr_canEditEmployee'] == 0) {
            error($lang->sectionnopermission);
        }
        $uid = $db->escape_string($core->input['id']);

        if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
            if(is_array($core->user['hraffids']) && !empty($core->user['hraffids'])) {
                if(!value_exists('affiliatedemployees', 'uid', $uid, 'affid IN ('.implode(',', $core->user['hraffids']).')')) {
                    redirect('index.php?module=hr/employeeslist');
                }
            }
        }

        $main_affiliate = $db->fetch_field($db->query("SELECT affid FROM ".Tprefix."affiliatedemployees WHERE uid='{$uid}' AND isMain=1"), 'affid');

        if(!isset($core->input['id']) || empty($core->input['id'])) {
            redirect('index.php?module=hr/employeeslist');
        }

        $query = $db->query("SELECT u.uid, CONCAT(firstName, ' ', lastName) as fullname
						FROM ".Tprefix."users u JOIN ".Tprefix."affiliatedemployees a ON (a.uid=u.uid)
						WHERE a.affid={$main_affiliate} AND isMain=1 AND u.gid!=7
						ORDER BY fullname ASC");
        while($user = $db->fetch_array($query)) {
            $users[$user['uid']] = $user['fullname'];
        }

        $users_list = parse_selectlist('uid[]', 1, $users, $core->input['id'], 1);

        $workshifts = get_specificdata('workshifts', array('wsid', "CONCAT(onDutyHour, ':',onDutyMinutes,' to ', offDutyHour,':',offDutyMinutes ) AS fulltime"), 'wsid', 'fulltime', 'onDutyHour, onDutyMinutes ASC, offDutyHour, offDutyMinutes', 1);
        $query = $db->query("SELECT * FROM ".Tprefix."workshifts ORDER BY onDutyHour ASC");
        $weekdays_cache = array();
        while($workshift = $db->fetch_assoc($query)) {
            if(!in_array($workshift['weekDays'], $weekdays_cache)) {
                $weekdays_cache[$workshift['wsid']] = $workshift['weekDays'];
                $comma = '';
                foreach(unserialize($workshift['weekDays']) as $day) {
                    $weekdays[$workshift['wsid']] .= $comma.get_day_name($day, 'letters');
                    $comma = ', ';
                }
            }
            else {
                $weekdays[$workshift['wsid']] = $weekdays[array_search($workshift['weekDays'], $weekdays_cache)];
            }
            $workshifts[$workshift['wsid']] = $workshift['onDutyHour'].':'.$workshift['onDutyMinutes'].' - '.$workshift['offDutyHour'].':'.$workshift['offDutyMinutes'].' ('.$weekdays[$workshift['wsid']].')';
        }
        $query = $db->query("SELECT *
							FROM ".Tprefix."employeesshifts e JOIN ".Tprefix."workshifts w ON (e.wsid=w.wsid)
							WHERE uid={$uid} ORDER BY fromDate");
        $rowid = 1;
        if($db->num_rows($query) > 0) {
            while($shift = $db->fetch_assoc($query)) {
                $fromDate[$rowid]['output'] = date($core->settings['dateformat'], $shift['fromDate']);
                $fromDate[$rowid]['formatted'] = date('d-m-y', $shift['fromDate']);

                $toDate[$rowid]['output'] = date($core->settings['dateformat'], $shift['toDate']);
                $toDate[$rowid]['formatted'] = date('d-m-y', $shift['toDate']);

                $workshifts_list = parse_selectlist("shift[{$rowid}]", 1, $workshifts, $shift['wsid']);

                eval("\$shift_row .= \"".$template->get('hr_employeeslist_workshift_shiftrow')."\";");
                $rowid++;
            }
        }
        else {
            $workshifts_list = parse_selectlist("shift[{$rowid}]", 1, $workshifts, 0);
            eval("\$shift_row = \"".$template->get('hr_employeeslist_workshift_shiftrow')."\";");
        }

        /* 	Add Workshift Popup - Start */
        for($i = 1; $i <= 24; $i++) {
            $hours[$i] = $i;
        }

        for($i = 0; $i < 60; $i++) {
            if($i >= 0 && $i < 10) {
                $mins['0'.$i] = '0'.$i;
            }
            else {
                $mins[$i] = $i;
            }
        }

        $offdutyhour_selectlist = parse_selectlist('offDutyHour', 1, $hours, 0, 0);
        $ondutyhour_selectlist = parse_selectlist('onDutyHour', 1, $hours, 0, 0);
        $offdutymins_selectlist = parse_selectlist('offDutyMinutes', 1, $mins, 0, 0);
        $ondutymins_selectlist = parse_selectlist('onDutyMinutes', 1, $mins, 0, 0);

        for($i = 1; $i <= 7; $i++) {
            $weekdays_checkbox .= ' <input type="checkbox" name="weekDays[]" id="weekDays[]" value="'.$i.'" />'.get_day_name($i, 'letters');
        }

        eval("\$addworkshift_popup = \"".$template->get('popup_hr_employeeslist_addworkshift')."\";");
        /* Add Workshift Popup - End */
        eval("\$workshiftbox = \"".$template->get('hr_employeeslist_workshift')."\";");
        output_page($workshiftbox);
    }
    elseif($core->input['action'] == 'do_setworkshifts') {
        if(!is_array($core->input['uid'])) {
            output_xml("<status>false</status><message>{$lang->missingusers}</message>");
            exit;
        }

        foreach($core->input['uid'] as $uid) {
            $newshifts = array();
            foreach($core->input['shift'] as $key => $shift) {
                if(empty($shift)) {
                    continue;
                }

                if(!isset($core->input['fromDate_timestamp'][$key])) {
                    $fromdate = explode('-', $core->input['fromDate'][$key]);
                    $todate = explode('-', $core->input['toDate'][$key]);

                    $core->input['fromDate_timestamp'][$key] = mktime(0, 0, 0, $fromdate[1], $fromdate[0], $fromdate[2]);
                    $core->input['toDate_timestamp'][$key] = mktime(23, 1, 0, $todate[1], $todate[0], $todate[2]);
                }

                if(empty($core->input['toDate_timestamp'][$key]) || empty($core->input['toDate_timestamp'][$key])) {
                    output_xml("<status>false</status><message>empty</message>");
                    exit;
                }
                elseif($core->input['toDate_timestamp'][$key] > $core->input['toDate_timestamp'][$key]) {
                    output_xml("<status>false</status><message>dateinvalid</message>");
                    exit;
                }

                for($i = $key + 1; $i <= sizeof($core->input['shift']); $i++) {
                    if(!isset($core->input['fromDate_timestamp'][$i])) {
                        $fromdate = explode('-', $core->input['fromDate'][$i]);
                        $todate = explode('-', $core->input['toDate'][$i]);

                        $core->input['fromDate_timestamp'][$i] = mktime(0, 0, 0, $fromdate[1], $fromdate[0], $fromdate[2]);
                        $core->input['toDate_timestamp'][$i] = mktime(23, 1, 0, $todate[1], $todate[0], $todate[2]);
                    }

                    if(($core->input['toDate_timestamp'][$key] <= $core->input['toDate_timestamp'][$i]) && ($core->input['toDate_timestamp'][$key] >= $core->input['fromDate_timestamp'][$i])) {
                        output_xml("<status>false</status><message>form date between previous</message>");
                        exit;
                    }
                    else {
                        if(($core->input['fromDate_timestamp'][$i] <= $core->input['toDate_timestamp'][$key]) && ($core->input['fromDate_timestamp'][$i] >= $core->input['toDate_timestamp'][$key])) {
                            output_xml("<status>false</status><message>form date 2 between previous</message>");
                            exit;
                        }
                    }

                    if(($core->input['toDate_timestamp'][$key] >= $core->input['fromDate_timestamp'][$i]) && ($core->input['toDate_timestamp'][$key] <= $core->input['toDate_timestamp'][$i])) {
                        output_xml("<status>false</status><message>to date between</message>");
                        exit;
                    }
                    else {
                        if(($core->input['toDate_timestamp'][$i] >= $core->input['toDate_timestamp'][$key]) && ($core->input['toDate_timestamp'][$i] <= $core->input['toDate_timestamp'][$key])) {
                            output_xml("<status>false</status><message>to date2 between</message>");
                            exit;
                        }
                    }
                }

                $newshifts[] = array(
                        'uid' => $uid,
                        'wsid' => $shift,
                        'fromDate' => $core->input['fromDate_timestamp'][$key],
                        'toDate' => $core->input['toDate_timestamp'][$key]
                );
            }

            $db->delete_query('employeesshifts', "uid={$uid}");
            foreach($newshifts as $newshift) {
                $query = $db->insert_query('employeesshifts', $newshift);
            }
        }

        if($query) {
            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
        }
    }
    elseif($core->input['action'] == 'do_addworkshift') {
        if(!is_array($core->input['weekDays'])) {
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            exit;
        }

        unset($core->input['module'], $core->input['action']);
        $core->input['weekDays'] = serialize($core->input['weekDays']);
        $query = $db->insert_query('workshifts', $core->input);

        if($query) {
            $log->record($db->last_id());
            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
        }
    }
}
?>