<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * List of Holidays
 * $module: hr
 * $id: holidayslist.php
 * Created:			@najwa.kassem		October 28, 2010 | 13:30 AM
 * Last Update:		@zaher.reda		  	October 25, 2012 | 10:29 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['hr_canManageHolidays'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {
    $sort_query = 'name ASC';
    $sort_url = sort_url();
    $limit_start = 0;

    if(!isset($core->input['affid']) || empty($core->input['affid'])) {
        $affid = $core->user['mainaffiliate'];
        if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
            if(is_array($core->user['hraffids']) && !empty($core->user['hraffids'])) {
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
            //if($core->input['affid'] != $core->user['mainaffiliate']) {
            if(!in_array($core->input['affid'], $core->user['hraffids'])) {
                $affid = $core->user['mainaffiliate'];
                if(!in_array($core->user['mainaffiliate'], $core->user['hraffids'])) {
                    error($lang->sectionnopermission);
                    exit;
                }
            }
        }
    }

    if(isset($core->input['sortby'], $core->input['order'])) {
        $sort_query = $db->escape_string($core->input['sortby']).' '.$db->escape_string($core->input['order']);
    }

    if(isset($core->input['start'])) {
        $limit_start = $db->escape_string($core->input['start']);
    }

    if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
        $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
    }

    $multipage_where = 'affid='.$affid;
    /* Perform inline filtering - START */
    $months[''] = '';
    for($i = 1; $i <= 12; $i++) {
        $months[$i] = $lang->{strtolower(date('F', mktime(0, 0, 0, $i, 1, 1970)))};
    }

    $days_number = range(1, 31);
    array_unshift($days_number, '');
    $filters_config = array(
            'parse' => array('filters' => array('title', 'month', 'day', 'numDays', 'year'),
                    'overwriteField' => array('month' => parse_selectlist('filters[month]', 3, $months, $core->input['filters']['month'], 0), 'day' => parse_selectlist('filters[day]', 3, array_combine($days_number, $days_number), $core->input['filters']['day'], 0))
            ),
            'process' => array(
                    'filterKey' => 'hid',
                    'mainTable' => array(
                            'name' => 'holidays',
                            'filters' => array('title' => 'title', 'year' => 'year', 'day' => array('operatorType' => 'equal', 'name' => 'day'), 'month' => array('operatorType' => 'equal', 'name' => 'month'), 'numDays' => 'numDays')
                    )
            )
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    $filters_row_display = 'hide';

    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = 'AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));
    /* Perform inline filtering - END */

    $query = $db->query("SELECT * FROM ".Tprefix."holidays
                                    WHERE affid={$affid}
                                    {$filter_where}
                                    ORDER BY isOnce ASC, {$sort_query}
                                    LIMIT {$limit_start}, {$core->settings[itemsperlist]}");


    if($db->num_rows($query) > 0) {
        while($holiday = $db->fetch_assoc($query)) {
            if($holiday['isOnce'] == 0) {
                $year = '<img src="./images/icons/update.png" border="0" alt="{$lang->recurring}" />';
            }
            else {
                $year = $holiday['year'];
            }
            $holidays_list .= "<tr><td align='center'>{$holiday[title]}</td><td align='center'>".$lang->{strtolower(date('F', mktime(0, 0, 0, $holiday['month'], 1, 0)))}."</td><td align='center'>{$holiday[day]}</td><td align='center'>{$holiday[numDays]}</td><td align='center'>{$year}</td><td style='text-align: right;'><a href='index.php?module=hr/holidayslist&amp;action=sendholidays&amp;id={$holiday[hid]}'><img src='./images/icons/send.gif' border='0' alt=''/></a> <a href='index.php?module=hr/manageholidays&amp;id={$holiday[hid]}'><img src='./images/icons/edit.gif' border='0' alt=''/></a></td></tr>";
        }

        $multipages = new Multipages('holidays', $core->settings['itemsperlist'], $multipage_where);
        $holidays_list .= '<tr><td colspan="6">'.$multipages->parse_multipages().'</td></tr>';
    }
    else {
        $holidays_list = '<tr><td colspan="6">'.$lang->nomatchfound.'</td></tr>';
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
        $affid_field = $lang->affiliate.': '.parse_selectlist('affid', 1, $affiliates, $affid, 0, 'goToURL("index.php?module=hr/holidayslist&amp;affid="+$(this).val())').'';
    }
    eval("\$list = \"".$template->get('hr_holidayslist')."\";");
    output_page($list);
}
else {
    if($core->input['action'] == 'sendholidays') {
        $current_year = date('Y', TIME_NOW);
        $message = array();
        $message['body'] = '<ul>';

        if(isset($core->input['id'])) {
            $holiday = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."holidays  WHERE ((validFrom = 0 OR ({$current_year} >= FROM_UNIXTIME(validFrom, '%Y') AND month >= FROM_UNIXTIME(validFrom, '%m') AND day >= FROM_UNIXTIME(validFrom, '%d')))
													AND (validTo=0 OR ({$current_year} <= FROM_UNIXTIME(validTo, '%Y') AND month <= FROM_UNIXTIME(validTo, '%m') AND day <= FROM_UNIXTIME(validTo, '%d'))))
													AND hid=".intval($core->input['id']))); //TO add affid restriction

            if(!empty($holiday) && is_array($holiday)) {
                if($holiday['year'] == 0) {
                    $holiday['year'] = $current_year;
                }
                $holiday['hasexceptions'] = '';
                if(value_exists('holidaysexceptions', 'hid', $holiday['hid'])) {
                    $holiday['hasexceptions'] = '<sup>SEL</sup>';
                }

                $message['body'] .= '<li>'.date('l, F j', mktime(0, 0, 0, $holiday['month'], $holiday['day'], $holiday['year'])).' - '.$holiday['title'].', '.$holiday['numDays'].' day(s). '.$holiday['hasexceptions'].'</li>';

                $core->input['affidtoinform'] = $holiday['affid'];
            }
            else {
                error($lang->noholidaysavailable);
            }
        }
        else {
            if(!isset($core->input['affidtoinform'])) {
                output_xml("<status>false</status><message>{$lang->unknownaffiliate}</message>");
                exit;
            }
        }

        $affiliate_obj = new Affiliates($core->input['affidtoinform'], false);
        $affiliate = $affiliate_obj->get();
//        if(empty($affiliate['mailingList'])) {
//            output_xml("<status>false</status><message>{$lang->errorsendingemail}</message>");
//            exit;
//        }

        $message['recepient'] = $affiliate['mailingList'];
        if(!isset($core->input['id'])) {
            $query = $db->query("SELECT * FROM ".Tprefix."holidays  WHERE ((validFrom = 0 OR ({$current_year} >= FROM_UNIXTIME(validFrom, '%Y') AND month >= FROM_UNIXTIME(validFrom, '%m') AND day >= FROM_UNIXTIME(validFrom, '%d')))
								AND (validTo=0 OR ({$current_year} <= FROM_UNIXTIME(validTo, '%Y') AND month <= FROM_UNIXTIME(validTo, '%m') AND day <= FROM_UNIXTIME(validTo, '%d'))))
								AND affid='{$affiliate[affid]}' AND (year={$current_year} OR isOnce=0) ORDER BY month ASC, day ASC");

            if($db->num_rows($query) == 0) {
                output_xml("<status>false</status><message>{$lang->noholidaysavailable}</message>");
                exit;
            }

            while($holiday = $db->fetch_assoc($query)) {
                $holiday['hasexceptions'] = '';
                if(value_exists('holidaysexceptions', 'hid', $holiday['hid'])) {
                    $holiday['hasexceptions'] = '<sup>SEL</sup>';
                }

                $message['body'] .= '<li>'.date('l, F j', mktime(0, 0, 0, $holiday['month'], $holiday['day'], $current_year)).' - '.$holiday['title'].', '.$holiday['numDays'].' day(s). '.$holiday['hasexceptions'].'</li>';
            }
        }
        $message['body'] .= '</ul><br />';
        $lang->load('messages');

        $email_data = array(
                'to' => $message['recepient'],
                'from_email' => $core->settings['maileremail'],
                'from' => 'OCOS Mailer'
        );

        if(isset($core->input['id'])) {
            $email_data['subject'] = $lang->upcomingholidays_subject;
            $email_data['message'] = $lang->sprint($lang->upcomingholidays_message, $affiliate['name'], $message['body']);
        }
        else {
            $email_data['subject'] = $lang->sprint($lang->holidaysmessagesubject, $current_year);
            $email_data['message'] = $lang->sprint($lang->holidaysmessagebody, $current_year, $message['body']);
        }

        $mail = new Mailer($email_data, 'php');

        if($mail) {
            $log->record($affiliate['affid']);
            if(isset($core->input['id'])) {
                redirect('index.php?module=hr/holidayslist', 1, $lang->holidayssent);
            }
            output_xml("<status>true</status><message>{$lang->holidayssent}</message>");
        }
        else {
            if(isset($core->input['id'])) {
                error($lang->errorsendingemail, 'index.php?module=hr/holidayslist');
            }
            output_xml("<status>false</status><message>{$lang->errorsendingemail}</message>");
            exit;
        }
    }
}
?>