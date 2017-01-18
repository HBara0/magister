<?php

class Calendar {

    private $preferences = array();
    private $exclude = array();
    private $affiliates = array();
    private $period = array();
    private $other_periods = array();
    private $dates = array();
    private $data = array();
    private $tohighlight = array();
    private $title = '';
    private $weekdays = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
    private $colors = array('c92200', 'ff887c', 'a4bdfc', 'ffead6', 'dbadff', 'ffb878', 'cccb51', '51b749', 'f78f10', 'f38630', 'bdfa4b', 'ffeed0', '69d2e7', 'eb6c5c', 'a7dbd8', 'bcd04c', 'e4f574', 'dc9b57', '91b64f');
    private $fixedwidth = array();
    private $options = array();
    private $cache = '';

    public function __construct($view = 'month', array $date_param = array()) {
        global $core, $lang, $db;

        $this->dates['actual'] = getdate_custom(TIME_NOW);

        if ($view == 'week') {
            $this->options['view'] = 'week';
            if (isset($date_param['week'], $date_param['year'])) {
                $this->dates['current'] = array('year' => $db->escape_string($date_param['year']), 'week' => $db->escape_string($date_param['week']));
                define('CALENDAR_TIME', strtotime($this->dates['current']['year'] . 'W' . $this->dates['current']['week']));
            }
            else {
                define('CALENDAR_TIME', TIME_NOW);
                $this->dates['current'] = $this->dates['actual'];
            }
            $this->period['firstday'] = strtotime($this->dates['current']['year'] . 'W' . $this->dates['current']['week']);
            $this->period['lastday'] = strtotime($this->dates['current']['year'] . 'W' . $this->dates['current']['week'] . ' +1 week -1 second');
            $this->period['numdays'] = round(($this->period['lastday'] - $this->period['firstday']) / 24 / 60 / 60);

            $this->title = $lang->week . ' ' . $this->dates['current']['week'] . ', ' . $this->dates['current']['year'];
            $this->title .= '<br /><span style="font-size: 14px;">(' . date('M d', $this->period['firstday']) . '</a> - ' . date('M d', $this->period['lastday']) . ')</span>';

            /* PARSE NEXT / PREV WEEK LINKS DATA - Start */
            $this->other_periods['previous'] = getdate_custom(strtotime('previous week first day', CALENDAR_TIME));
            $this->other_periods['next'] = getdate_custom(strtotime('next week first day', CALENDAR_TIME));
            /* PARSE NEXT / PREV WEEK LINKS DATA - END */
        }
        else {
            $this->options['view'] = 'month';
            if (isset($date_param['month'], $date_param['year'])) {
                $this->dates['current'] = array(
                    'year' => $db->escape_string($date_param['year']),
                    'mon' => $db->escape_string($date_param['month']),
                    'mday' => $this->dates['actual']['mday']
                );

                define('CALENDAR_TIME', mktime(0, 0, 0, $this->dates['current']['mon'], 1, $this->dates['current']['year']));
            }
            else {
                if (isset($date_param['from_time'], $date_param['to_time'])) {
                    define('CALENDAR_TIME', $date_param['from_time']);
                }
                else {
                    define('CALENDAR_TIME', TIME_NOW);
                    $this->dates['current'] = $this->dates['actual'];
                }
            }

            $this->title = $lang->{strtolower(date('F', CALENDAR_TIME))} . ', ' . $this->dates['current']['year'];

            if (isset($date_param['from_time'], $date_param['to_time'])) {
                $this->period['firstday'] = $db->escape_string($date_param['from_time']);
                $this->period['lastday'] = $db->escape_string($date_param['to_time']);
            }
            else {
                /* PARSE NEXT / PREV MONTH LINKS DATA - Start */
                $this->other_periods['previous'] = getdate(strtotime('-1 month', CALENDAR_TIME));
                $this->other_periods['next'] = getdate(strtotime('+1 month', CALENDAR_TIME));
                /* PARSE NEXT / PREV MONTH LINKS DATA - END */

                $this->period['firstday'] = mktime(0, 0, 0, $this->dates['current']['mon'], 1, $this->dates['current']['year']);
                $this->period['lastday'] = mktime(23, 0, 0, $this->other_periods['next']['mon'], 0, $this->other_periods['next']['year']);
            }

            $this->period['numdays'] = date('t', $this->period['firstday']);
            $this->period['firstday_weekday'] = date('N', $this->period['firstday']);
        }
        $this->read_userpreferences();
        $this->read_useraffiliates();


        /* Instantiate Cache for day */
        $this->cache = new Cache();
    }

    private function read_userpreferences() {
        global $core, $db;

        $query = $db->query("SELECT * FROM " . Tprefix . "calendar_userpreferences WHERE uid={$core->user[uid]}");
        if ($db->num_rows($query) > 0) {
            $this->preferences = $db->fetch_array($query);

            $excludedaffiliates_query = $db->query("SELECT * FROM " . Tprefix . "calendar_userpreferences_excludedaffiliates a JOIN " . Tprefix . "calendar_userpreferences p ON (a.cpid=p.cpid) WHERE p.uid={$core->user[uid]}");
            if ($db->num_rows($excludedaffiliates_query) > 0) {
                while ($excludedaffiliates = $db->fetch_array($excludedaffiliates_query)) {
                    $this->exclude['affiliates'][] = $excludedaffiliates['affid'];
                }
            }

            $excludedusers_query = $db->query("SELECT * FROM " . Tprefix . "calendar_userpreferences_excludedusers u JOIN " . Tprefix . "calendar_userpreferences p ON (u.cpid=p.cpid) WHERE p.uid={$core->user[uid]}");
            if ($db->num_rows($excludedusers_query) > 0) {
                while ($excludedusers = $db->fetch_array($excludedusers_query)) {
                    $this->exclude['employees'][$excludedusers['euid']] = $excludedusers['euid'];
                }
            }
        }
    }

    private function read_useraffiliates() {
        global $db, $core;

        if (is_array($this->exclude['affiliates'])) {
            $affiliates_querystring = ' AND ae.affid NOT IN (' . implode(',', $this->exclude['affiliates']) . ')';
        }

        /* if($this->options['view'] == 'week') {
          $affiliates_querystring = ' AND ae.affid='.$core->user['mainaffiliate'];
          } */

        $affiliates_query = $db->query("SELECT a.name, ae.* FROM " . Tprefix . "affiliatedemployees ae JOIN " . Tprefix . "affiliates a ON (a.affid=ae.affid) WHERE uid='{$core->user[uid]}'{$affiliates_querystring}");
        while ($affiliate = $db->fetch_assoc($affiliates_query)) {
            $this->affiliates['affid'][$affiliate['affid']] = $affiliate['affid'];
            $this->affiliates['name'][$affiliate['affid']] = $affiliate['name'];
        }

        $query = $db->query("SELECT affid, name FROM " . Tprefix . "affiliates WHERE supervisor='{$core->user[uid]}'");
        if ($db->num_rows($query) > 0) {
            while ($affiliate = $db->fetch_assoc($query)) {
                if (isset($this->affiliates['affid'][$affiliate['affid']])) {
                    $this->affiliates['supervised'][$affiliate['affid']] = $affiliate['affid'];
                    $this->affiliates['name'][$affiliate['affid']] = $affiliate['name'];
                }
                else {
                    $this->affiliates['affid'][$affiliate['affid']] = $affiliate['affid'];
                    $this->affiliates['supervised'][$affiliate['affid']] = $affiliate['affid'];
                }
            }
        }
    }

    public function read_leaves($ignore_preference = false, $affiliate = '', array $types = array()) {
        global $db, $core;

        if ($this->preferences['excludeLeaves'] == 0 || $ignore_preference == true) {
            $approved_lids = $unapproved_lids = array();

            if (is_array($this->exclude['employees'])) {
                $affiliate_users_querystring = ' AND uid NOT IN (' . implode(',', $this->exclude['employees']) . ')';
            }

            if ($this->options['view'] == 'week') {
                $affiliate_users_querystring = ' AND (uid=' . $core->user['uid'] . ' OR uid IN (SELECT uid FROM ' . Tprefix . 'users WHERE reportsTo=' . $core->user['uid'] . ')';
                $affiliate_users_querystring .= ' OR uid IN (SELECT uid FROM ' . Tprefix . 'affiliatedemployees WHERE affid IN (SELECT affid FROM ' . Tprefix . 'affiliatedemployees WHERE canAudit=1 AND uid=' . $core->user['uid'] . ')))';
            }

            if (empty($affiliate)) {
                if (empty($this->affiliates)) {
                    $this->read_useraffiliates();
                }
            }
            else {
                unset($this->affiliates);
                $this->affiliates['affid'][$affiliate] = $affiliate;
            }

            if (!empty($types)) {
                $type_querystring = ' type IN (' . $db->escape_string(implode(',', $types)) . ') AND ';
            }

            foreach ($this->affiliates['affid'] as $affid => $affiliate) {
                $affiliate_users = get_specificdata('affiliatedemployees', 'uid', 'uid', 'uid', '', 0, "affid='{$affiliate}' AND isMain='1'{$affiliate_users_querystring}");
                if (empty($affiliate_users)) {
                    continue;
                }

                $query = $db->query("SELECT l.lid, la.isApproved
                                    FROM " . Tprefix . "leaves l JOIN " . Tprefix . "leavesapproval la ON (l.lid=la.lid)
                                    WHERE {$type_querystring}(((l.fromDate BETWEEN " . $this->period['firstday'] . " AND " . $this->period['lastday'] . ") OR (l.toDate BETWEEN " . $this->period['firstday'] . " AND " . $this->period['lastday'] . ")) OR ((" . $this->period['firstday'] . " BETWEEN l.fromDate AND l.toDate) OR (" . $this->period['lastday'] . " BETWEEN l.fromDate AND l.toDate))) AND l.uid IN (" . implode(', ', $affiliate_users) . ")");
                if ($db->num_rows($query) > 0) {
                    while ($leave = $db->fetch_assoc($query)) {
                        if ($leave['isApproved'] == 0) {
                            $unapproved_lids[$leave['lid']] = $leave['lid'];
                            if (in_array($leave['lid'], $approved_lids)) {
                                unset($approved_lids[$leave['lid']]);
                            }
                        }
                        if (!in_array($leave['lid'], $unapproved_lids) && $leave['isApproved'] == 1) {
                            $approved_lids[$leave['lid']] = $leave['lid'];
                        }
                    }
                }
            }

            if (!empty($approved_lids)) {
                $query = $db->query("SELECT l.*, l.uid AS requester, Concat(u.firstName, ' ', u.lastName) AS employeename
						FROM " . Tprefix . "leaves l JOIN " . Tprefix . "users u ON (l.uid=u.uid)
						WHERE l.lid IN (" . implode(',', $approved_lids) . ") ORDER BY l.fromDate ASC, (l.toDate-l.fromDate) DESC");
                if ($db->num_rows($query) > 0) {
                    while ($more_leaves = $db->fetch_assoc($query)) {
                        $num_days_off = ($more_leaves['toDate'] - $more_leaves['fromDate']) / 24 / 60 / 60; //(date('z', $more_leaves['toDate'])-date('z', $more_leaves['fromDate']))+1;

                        $leave_type_details = parse_type($more_leaves['type']);
                        $more_leaves['type'] = $leave_type_details;

                        if ($num_days_off == 1) {
                            $current_check_date = getdate($more_leaves['toDate']);
                            $this->data['leaves'][$current_check_date['mday']][$more_leaves['lid']] = $more_leaves;
                        }
                        else {
                            for ($i = 0; $i < $num_days_off; $i++) {
                                $current_check = $more_leaves['fromDate'] + (60 * 60 * 24 * $i);

                                if ($this->period['firstday'] > $current_check || $this->period['lastday'] < $current_check) {
                                    continue;
                                }

                                if ($current_check > ($this->period['firstday'] * 60 * 60 * 24 * $this->period['numdays'])) {
                                    break;
                                }
                                $current_check_date = getdate($current_check);
                                $this->data['leaves'][$current_check_date['mday']][$more_leaves['lid']] = $more_leaves;
                            }
                        }
                    }
                }
            }
        }
    }

    public function read_holidays() {
        global $db;

        if ($this->preferences['excludeHolidays'] == 0) {
            if (is_array($this->exclude['affiliates'])) {
                $holidays_querystring = ' AND aff.affid NOT IN (' . implode(',', $this->exclude['affiliates']) . ')';
            }

            $holidays_query = $db->query("SELECT aff.name AS affiliatename, h.*, c.acronym AS country
											FROM " . Tprefix . "holidays h
											JOIN " . Tprefix . "affiliates aff ON (aff.affid=h.affid)
											LEFT JOIN countries c ON (aff.country=c.coid)
											WHERE ((validFrom = 0 OR ({$this->dates[current][year]} >= FROM_UNIXTIME(validFrom, '%Y') AND month >= FROM_UNIXTIME(validFrom, '%m') AND day >= FROM_UNIXTIME(validFrom, '%d'))) AND (validTo=0 OR ({$this->dates[current][year]} <= FROM_UNIXTIME(validTo, '%Y') AND month <= FROM_UNIXTIME(validTo, '%m') AND day <= FROM_UNIXTIME(validTo, '%d'))))
											AND (year=0 OR year={$this->dates[current][year]}) AND month={$this->dates[current][mon]}{$holidays_querystring}"); // AND h.affid IN (".implode(",',$affiliates['affid']).")
            while ($holiday = $db->fetch_assoc($holidays_query)) {
                if ($holiday['year'] == 0) {
                    $holiday['year'] == $time_details['year'];
                }

                if (!isset($this->affiliates['name'][$holiday['affid']])) {
                    $this->affiliates['name'][$holiday['affid']] = $holiday['affiliatename'];
                }
                if (!isset($this->affiliates['country'][$holiday['affid']])) {
                    $this->affiliates['country'][$holiday['affid']] = $holiday['country'];
                }
                $this->data['holidays'][$holiday['day']][$holiday['affid']][] = $holiday;

                if ($holiday['numDays'] > 1) {
                    for ($daynum = 1; $daynum < $holiday['numDays']; $daynum++) {
                        if (($holiday['day'] + $daynum) > date('t', mktime(0, 0, 0, $holiday['month'], $holiday['day'], $holiday['year']))) {
                            break;
                        }
                        $this->data['holidays'][$holiday['day'] + $daynum][$holiday['affid']][] = $holiday;
                    }
                }
            }
        }
    }

    public function read_tasks() {
        global $core, $db;

        $tasks_query = $db->query("SELECT * FROM " . Tprefix . "calendar_tasks WHERE (uid='{$core->user[uid]}' OR createdBy='{$core->user[uid]}') AND (dueDate BETWEEN {$this->period[firstday]} AND {$this->period[lastday]}) ORDER BY dueDate ASC, priority DESC");
        while ($task = $db->fetch_assoc($tasks_query)) {
            $task_date = getdate($task['dueDate']);
            $this->data['tasks'][$task_date['mday']][] = $task;
        }

        return true;
    }

    public function read_meetings() {
        global $core;
        /* get Meeting for the calender period ,add filter to display  Meeting to the logged uuser in case they are invited.   */
        $meeting_objs = Meetings::get_multiplemeetings(array('filter_where' => '((fromDate BETWEEN ' . $this->period['firstday'] . ' AND ' . $this->period['lastday'] . ') OR (toDate BETWEEN ' . $this->period['firstday'] . ' AND ' . $this->period['lastday'] . ') OR (' . $this->period['firstday'] . ' BETWEEN fromDate AND toDate) OR (' . $this->period['lastday'] . ' BETWEEN fromDate AND toDate)) AND  ((mtid IN (SELECT m.mtid FROM meetings m WHERE EXISTS (select mtid from meetings_attendees ma WHERE ma.mtid=m.mtid AND ma.idAttr= "uid" AND ma.attendee=' . $core->user['uid'] . '))) OR createdBy=' . $core->user['uid'] . ')'));
        if (is_array($meeting_objs)) {
            foreach ($meeting_objs as $meeting) {
                $meeting_date = getdate($meeting['fromDate']);
                $num_days_meeting = (($meeting['toDate'] - $meeting['fromDate']) / 24 / 60 / 60); /* divison to know how many days between the from and to */

                if ($num_days_meeting <= 1) {
                    $current_check_date = getdate($meeting['toDate']);
                    $this->data['meetings'][$current_check_date['mday']][] = $meeting;
                }//where uid in  invitess where uid =coreuser
                else {
                    for ($i = 0; $i < $num_days_meeting; $i++) {
                        $current_check = $meeting['fromDate'] + (60 * 60 * 24 * $i);

                        if ($this->period['firstday'] > $current_check) {
                            continue;
                        }
                        if ($current_check > ($this->period['firstday'] * 60 * 60 * 24 * $this->period['numdays'])) {
                            break;
                        }
                        $current_check_date = getdate($current_check);
                        $this->data['meetings'][$current_check_date['mday']][] = $meeting;
                    }
                }
            }
        }

        return true;
    }

    public function read_events() {
        global $core, $db;
        if ($this->preferences['excludeEvents'] == 0) {
            $events_query = $db->query("SELECT * FROM " . Tprefix . "calendar_events WHERE (uid='{$core->user[uid]}' OR isPublic=1) AND ((fromDate BETWEEN " . $this->period['firstday'] . ". AND " . $this->period['lastday'] . ") OR (toDate BETWEEN " . $this->period['firstday'] . " AND " . $this->period['lastday'] . "))");
            while ($event = $db->fetch_assoc($events_query)) {
                if ($event['isPublic'] == 1 && $core->usergroup['canViewAllAff'] == 0) {
                    $restricted = false;
                    $event_restrictions_query = $db->query("SELECT affid FROM " . Tprefix . "calendar_events_restrictions WHERE ceid='{$event[ceid]}'");
                    while ($restriction = $db->fetch_assoc($event_restrictions_query)) {
                        if (in_array($restriction['affid'], $core->user['affiliates'])) {
                            break;
                        }
                        $restricted = true;
                    }
                    if ($restricted == true) {
                        continue;
                    }
                }
                $num_days_event = (($event['toDate'] - $event['fromDate']) / 24 / 60 / 60); /* divison to know how many days between the from and to */ //(date('z', $event['toDate'])-date('z', $event['fromDate']))+1;

                if ($num_days_event == 1) {
                    $current_check_date = getdate($event['toDate']);
                    $this->data['events'][$current_check_date['mday']][] = $event;
                }
                else {
                    for ($i = 0; $i < $num_days_event; $i++) {
                        $current_check = $event['fromDate'] + (60 * 60 * 24 * $i);

                        if ($this->period['firstday'] > $current_check) { //|| $more_leaves['toDate'] < $current_check) {
                            continue;
                        }
                        if ($current_check > ($this->period['firstday'] * 60 * 60 * 24 * $this->period['numdays'])) {
                            break;
                        }

                        $current_check_date = getdate($current_check);
                        $this->data['events'][$current_check_date['mday']][] = $event;
                    }
                }
            }
        }
    }

    private function set_highlights($tohighlight) {
        if (empty($tohighlight)) {
            return false;
        }
        $this->tohighlight = $tohighlight;
    }

    private function calculate_entries_width($items = array(), $count_rec = 0, $depth = 0) {
        $fixedwidth = 160;

        if (is_array($items)) {
            foreach ($items as $key => $sub) {
                $count = count($sub, COUNT_RECURSIVE);

                if ($count_rec > 0) {
                    $count = ($count + ($count_rec - $count));
                }
                else {
                    $count += 1;
                }
                $this->widths[$key] = ($fixedwidth / $count);
                $this->leftindex[$key] = $depth;

                if (is_array($sub) && !empty($sub)) {
                    $this->calculate_entries_width($sub, $count, 1 + $depth);
                }
            }
        }
    }

    private function get_overlapping_leaves($day) {
        $types = array('holidays', 'leaves', 'tasks', 'events');
        foreach ($types as $type) {
            $prevkey = 0;
            if (isset($this->data[$type][$day])) {
                $data[$type] = array();

                foreach ($this->data[$type][$day] as $key => $value) {
                    switch ($type) {
                        case'leaves':
                            if ($prevkey != 0 && ($value['fromDate'] >= $this->data[$type][$day][$prevkey]['fromDate'] && $value['fromDate'] <= $this->data[$type][$day][$prevkey]['toDate'])) {
                                $chain[$key] = array();
                                $chain = &$chain[$key];
                            }
                            else {
                                unset($chain);
                                $chain = &$data[$type];
                                $chain[$key] = array();
                                $chain = &$chain[$key];
                            }
                            break;
                    }
                    $prevkey = $key;
                }
            }
        }
        return $data;
    }

    public function parse_hourevent($day) {
        global $core, $lang, $db, $template;
        if (empty($day)) {
            return false;
        }
        $top = '';

        $overlaps = $this->get_overlapping_leaves($day);
        $this->calculate_entries_width($overlaps['leaves']);

        $types = array('holidays', 'leaves', 'tasks', 'events');
        $content = '';
        foreach ($types as $type) {
            if (isset($this->data[$type][$day])) {
                $is_first = true;

                foreach ($this->data[$type][$day] as $key => $value) {
                    switch ($type) {
                        case 'leaves':
                            /* Generate difference colors for the different users - START */
                            $bgcolor = 'F7FAFD';
                            if ($core->user['uid'] != $value['uid']) {
                                /* if the color index not exsist for the user of this leave */
                                if (!$this->cache->iscached('colors', $value['uid'])) {
                                    if ((next($this->colors) === false)) {
                                        $bgcolor = generate_random_color(1);
                                    }
                                    else {
                                        $bgcolor = current($this->colors);
                                    }
                                    $this->cache->add('colors', $bgcolor, $value['uid']);
                                }
                                else {
                                    $bgcolor = $this->cache->data['colors'][$value['uid']];
                                }
                            }

                            $bgcolor = '#' . $bgcolor;
                            /* Generate difference colors for the different users - END */

                            /* Get the customer details for the current leave - START */
                            $visit = $db->fetch_assoc($db->query('SELECT l.uid, vr.type, vr.purpose, vr.identifier, vr.affid, e.companyName AS customername, e.companyNameAbbr, finishDate
											  FROM ' . Tprefix . 'leaves l
											  JOIN ' . Tprefix . 'visitreports vr ON (l.lid = vr.lid)
											  JOIN ' . Tprefix . 'entities e ON (e.eid = vr.cid)
											  WHERE vr.lid=' . $value['lid']));

                            if (empty($visit) || !is_array($visit)) {
                                continue;
                            }
                            if (!empty($visit) && is_array($visit)) {
                                if (!empty($visit['finishDate'])) {
                                    $reportlink_querystring = 'module=crm/previewvisitreport&amp;referrer=list&amp;vrid=' . $visit['identifier'];
                                    $visit['completesign'] = '&#10004;';
                                }
                                else {
                                    $reportlink_querystring = 'module=crm/fillvisitreport&amp;identifier=' . $visit['identifier'];
                                }
                                switch ($visit['type']) {
                                    case '1':
                                        $image = 'person.gif';
                                        break;
                                    case'2':
                                        $image = 'phone.gif';
                                        break;
                                    default: break;
                                }

                                if (!empty($visit['companyNameAbbr'])) {
                                    $value['customername'] = $visit['companyNameAbbr'];
                                }
                            }

                            if (!value_exists('users', 'reportsTo', $core->user['uid'], "uid={$value[uid]}") && !value_exists('affiliatedemployees', 'canAudit', 1, "uid={$core->user[uid]} AND affid={$visit[affid]}")) {
                                /* if the leave is not  for the logged in user, show "Customer Visit instead of customer name */
                                if ($visit['uid'] != $core->user['uid']) {
                                    $value['customername'] = $lang->customervisit;
                                    $value['cid'] = 0;
                                    $reportlink_querystring = $_SERVER['QUERY_STRING'];
                                }
                            }

                            /* Get the customer details for the current leave - END */
                            $date_details = getdate_custom($value['fromDate']);

                            $boxsize['top'] = (($value['fromDate'] - strtotime(date('Y-m-d', $value['fromDate']))) / 1800) * 20;
                            $height = (($value['toDate'] - $value['fromDate']) / 1800) * 20;
                            $boxsize['height'] = (($value['toDate'] - $value['fromDate']) / 1800) * 20;
                            $boxsize['left'] = 62 + (170 * ($date_details['wdayiso'] - 1)) + (($date_details['wdayiso'] - 1) * 1) + ($this->leftindex[$value['lid']] * $this->widths[$value['lid']]);
                            $boxsize['width'] = $this->widths[$value['lid']];
                            if ($is_first == false) {
                                $boxsize['left'] -= 8 * $this->leftindex[$value['lid']];
                                $boxsize['width'] += 8 * $this->leftindex[$value['lid']];
                            }

                            $is_first = false;

                            $value['fromDate_output'] = date('H:i', $value['fromDate']);
                            $value['toDate_output'] = ' - <span id="toTime_' . $value['lid'] . '">' . date('H:i', $value['toDate']) . '</span>';

                            $visit['customername_prefix'] = '<br />';
                            if (($value['toDate'] - $value['fromDate']) <= $this->options['depth']) {
                                $value['toDate_output'] = '';
                                $visit['customername_prefix'] = '';
                            }

                            $value = $value + $visit;
                            eval("\$content .= \"" . $template->get('calendar_weekview_entry') . "\";");
                            break;
                    }
                }
            }
        }
        return $content;
    }

    private function parse_dayevent($day) {
        global $lang;
        if (empty($day)) {
            return false;
        }
        $types = array('holidays', 'leaves', 'tasks', 'events', 'meetings');

        $content = '';
        foreach ($types as $type) {
            if (isset($this->data[$type][$day])) {
                $content .= '<p class="calendar_dayevent">';
                if (isset($lang->$type)) {
                    $content .= $lang->$type . ':</strong><br />';
                }
                foreach ($this->data[$type][$day] as $key => $value) {
                    switch ($type) {
                        case 'holidays':
                            $content .= '<a href="#" title="' . $this->affiliates['name'][$key] . '"><img src="./images/icons/flags/' . strtolower($this->affiliates['country'][$key]) . '.gif" border="0" alt="' . $this->affiliates['name'][$key] . '"/></a>';

                            foreach ($value as $val) {
                                $content .= '&nbsp;' . $val['title'] . '<br />';
                            }
                            break;
                        case 'leaves':
                            if (!$this->cache->iscached('monthdayuid_' . $day, $value['uid'])) {
                                if (!empty($value['type']['symbol'])) {
                                    $value['type']['symbol'] = '<span class="smalltext">' . $value['type']['symbol'] . '</span>';
                                }
                                $content .= '<a href="users.php?action=profile&amp;uid=' . $value['uid'] . '">' . $value['employeename'] . '</a> ' . $value['type']['symbol'] . '<br />';
                                $this->cache->add('monthdayuid_' . $day, $value['uid'], $value['uid']);
                            }
                            break;
                        case 'tasks':
                            $task_spanstyle = $checkbox_checked = '';
                            if ($value['isDone'] == 1) {
                                $task_spanstyle = ' style="text-decoration:line-through;"';
                                $checkbox_checked = ' checked="checked"';
                            }
                            $content .= '<span id="settaskdone_' . $value['ctid'] . '_calendar/eventstasks_checkbox_Result"><input type="checkbox" class="ajaxcheckbox" id="settaskdone_' . $value['ctid'] . '_calendar/eventstasks_checkbox" value="1"' . $checkbox_checked . '/></span><span' . $task_spanstyle . ' id="ctid_' . $value['ctid'] . '"><a href="#ctid_' . $value['ctid'] . '" id="taskdetails_' . $value['ctid'] . '_calendar/eventstasks_loadpopupbyid">' . $value['subject'] . '</a></span><br />';
                            break;
                        case 'events':
                            $content .= '<a href="#" id="eventdetails_' . $value['ceid'] . '_calendar/eventstasks_loadpopupbyid">' . $value['title'] . '</a><br />';
                            break;
                        case 'meetings':
                            $content .= '<a href="index.php?module=meetings/viewmeeting&referrer=calendar&mtid=' . $value['mtid'] . '" target="_blank" id="meetingdetails_' . $value['mtid'] . '">' . $value['title'] . '</a><br />';
                            break;
                        default: continue;
                    }
                }
                $content .= '</p>';
            }
        }

        return $content;
    }

    public function get_calendar() {
        global $lang;

        $calendar = '<tr><td class="calendar_head_legend">' . $lang->week . '</td>';
        array_walk($this->weekdays, create_function('&$string', 'global $lang; $string = $lang->{$string};'));
        $calendar .= '<td class="calendar_head">' . implode('</td><td class="calendar_head">', $this->weekdays) . '</td></tr>';
        $thisweek = getdate_custom($this->period['firstday']);
        $calendar .= '<tr><td class="calendar_weeknum"><a href="index.php?module=calendar/home&amp;week=' . $thisweek['week'] . '&amp;year=' . $thisweek['year'] . '#h27000">' . $thisweek['week'] . '</a></td>';

        $week_num_days = 1;

        for ($days_prev_month = 1; $days_prev_month < $this->period['firstday_weekday']; $days_prev_month++) {
            $calendar .= '<td class="calendar_noday">&nbsp;</td>';
            $week_num_days++;
        }

        for ($day = 1; $day <= $this->period['numdays']; $day++) {
            $current_day_style = 'calendar_day';

            if (!empty($this->tohighlight) && is_array($this->tohighlight)) {
                if (in_array($day, $this->tohighlight[$this->dates['current']['mon']])) {
                    $calendar_cell_highlight = 'background: #D5F2BF; ';
                }
            }
            else {
                if ($this->dates['actual']['mday'] == $day && $this->dates['actual']['mon'] == $this->dates['current']['mon'] && $this->dates['actual']['year'] == $this->dates['current']['year']) {
                    $current_day_style = 'calendar_today';
                }
            }

            $calendar .= '<td class="' . $current_day_style . '">';
            $calendar .= '<div class="calendar_day_number" id="day' . $day . '"><a href="#popup_createeventtask" id="createeventtask_' . $day . '-' . $this->dates['current']['mon'] . '-' . $this->dates['current']['year'] . '_day" class="showpopup">' . $day . '</a></div>';

            $calendar .= $this->parse_dayevent($day);
            $calendar .= '</td>';

            if ($week_num_days == 7) {
                $thisweek = getdate_custom($this->period['firstday'] + (60 * 60 * 24 * ($day + 1)));
                $calendar .= '</tr><tr><td class="calendar_weeknum"><a href="index.php?module=calendar/home&amp;week=' . $thisweek['week'] . '&amp;year=' . $thisweek['year'] . '#h27000">' . $thisweek['week'] . '</a></td>';
                $week_num_days = 0;
            }
            else {
                if ($day == $this->period['numdays']) {
                    for ($days_next_month = 1; $days_next_month <= (7 - $week_num_days); $days_next_month++) {
                        $calendar .= '<td class="calendar_noday">&nbsp;</td>';
                    }
                    $calendar .= '</tr>';
                }
            }
            $week_num_days++;
        }
        return $calendar;
    }

    public function get_calendar_weekview($depth = '', array $hours = array()) {
        if (empty($hours['from'])) {
            $hours['from'] = strtotime('today 00:00:00');
        }

        if (empty($hours['to'])) {
            $hours['to'] = strtotime('today 23:59:59');
        }
        if (empty($depth)) {
            $depth = 1800; //seconds
            $this->options['depth'] = 1800;
        }
        $depth = intval($depth);

        $hours['current'] = $hours['from'];

        $depth_accuml = 0;

        $calendar = '<tr><td class="calendar_head_legend">&nbsp;</td>';
        array_walk($this->weekdays, create_function('&$string', 'global $lang; $string = $lang->{$string};'));
        $calendar .= '<td class="calendar_head">' . implode('</td><td class="calendar_head">', $this->weekdays) . '</td><td style="width: 13px;">&nbsp;</td></tr>';
        $calendar .= '<tr><td colspan="9" style="padding:0px; margin:0px;">';
        $calendar .= '<div id="week_days_container">';

        /* Parse now marker - START */
        $markerposition = ((TIME_NOW - $hours['from']) / $depth) * 20;
        $calendar .= '<div class="calendar_nowmarker" style="top:' . $markerposition . 'px;"></div>';
        /* Parse now marker - END */

        for ($i = 1; $i <= $this->period['numdays']; $i++) {
            $today_time = ($this->period['firstday'] + (60 * 60 * 24 * ($i - 1)));
            $today = date('j', $today_time);
            $event .= $this->parse_hourevent($today);
        }
        //	$event = '<div id="xxx" style="position: absolute; width:100%; z-index: 8; top: 0pt; left: 0pt;">'.$event.'</div>';
        $calendar .= $event . '<table width="100%" cellspacing="0" cellpadding="0" style="margin:0px;" class="calendar" id="weekview_table">';


        while ($hours['current'] <= $hours['to']) {

            if ($rowclass == 'calendar_hourdepthrow') {
                $rowclass = 'calendar_hourrow';
                $hourlegend = '';
            }
            else {
                $rowclass = 'calendar_hourdepthrow';
                $hourlegend = date('H:i:s', $hours['current']);
            }

            $calendar .= '<tr style="height:20px;">';
            $calendar .= '<td class="calendar_legendhour ' . $rowclass . '" id="h' . $depth_accuml . '">' . $hourlegend . ' <a href="#' . $depth_accuml . '" /></td>';

            for ($i = 1; $i <= $this->period['numdays']; $i++) {
                $cell_time = ($this->period['firstday'] + $depth_accuml + (60 * 60 * 24 * ($i - 1)));
                $cellclass = '';
                if ($cell_time >= strtotime('today') && $cell_time < strtotime('tomorrow')) {
                    $cellclass = ' calendar_weekview_today';
                }

                $calendar .= '<td class="' . $rowclass . $cellclass . ' calendar_hour" id="day' . $i . '_' . $cell_time . '" title="' . date('d-m-Y H:i:s', $cell_time) . '"></td>';
            }
            //$calendar .= '<td style="width: 13px;">&nbsp;</td></tr>'; //Temporary in place of the scroll bar
            $calendar .= '</tr>';
            $hours['current'] += $depth;
            $depth_accuml += $depth;
        }
        $calendar .= '</table></div></td></tr>';

        return $calendar;
    }

    public function get_month() {
        return $this->period;
    }

    public function get_next_period() {
        return $this->other_periods['next'];
    }

    public function get_prev_period() {
        return $this->other_periods['previous'];
    }

    public function get_title() {
        return $this->title;
    }

    public function get_data() {
        return $this->data;
    }

    public function get_currentdate() {
        return $this->dates['current'];
    }

    public function get_actualdate() {
        return $this->dates['actual'];
    }

    public function get_affiliates() {
        return $this->affiliates;
    }

}

?>