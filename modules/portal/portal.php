<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Portal main
 * $module: portal
 * $id: portal.php
 * Last Update: @zaher.reda 	May 27, 2010 | 12:41 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

$lang->load('portal');
require_once INC_ROOT.'attendance_functions.php';
//require_once INC_ROOT.'currency_functions.php';

if(!$core->input['action']) {
    /* Attendance box - Start */
    $total_leaves = $db->fetch_field($db->query("SELECT count(*) as countall FROM ".Tprefix."leaves WHERE  uid='{$core->user[uid]}'"), 'countall');

    $leaves_approved = 0;
    $query = $db->query("SELECT * FROM ".Tprefix."leavesapproval la LEFT JOIN ".Tprefix."leaves l ON (l.lid=la.lid) WHERE l.uid={$core->user[uid]} ORDER BY la.lid ASC");
    while($leave = $db->fetch_array($query)) {
        if(!isset($found_unapprove[$leave['lid']])) {
            $found_unapprove[$leave['lid']] = false;
        }

        if($leave['isApproved'] == 0) {
            $found_unapprove[$leave['lid']] = true;
        }

        if($found_unapprove[$leave['lid']] == true) {
            continue;
        }
    }
    if(is_array($found_unapprove)) {
        foreach($found_unapprove as $key => $val) {
            if($val == false) {
                $leaves_approved++;
                //$approved_lids[] = $key;
            }
        }
    }

    $month_begining = mktime(0, 0, 0, date('n', TIME_NOW), 1, date('Y', TIME_NOW));
    $query = $db->query("SELECT l.*, lt.isWholeDay FROM ".Tprefix."leaves l JOIN ".Tprefix."leavetypes lt ON (l.type=lt.ltid) WHERE uid='{$core->user[uid]}' AND ((fromDate BETWEEN {$month_begining} AND ".TIME_NOW.") OR (toDate BETWEEN {$month_begining} AND ".TIME_NOW."))");

    $leaves_current_month = $days_current_month = 0;
    while($leave = $db->fetch_array($query)) {
        $leaves_current_month++;
        $days_current_month += count_workingdays($core->user['uid'], $leave['fromDate'], $leave['toDate'], $leave['isWholeDay']);
    }

    $lang->leavescurrentmonth = $lang->sprint($lang->leavescurrentmonth, $leaves_current_month, $days_current_month);
    /* Attendance box - End */

    /* Reporting module box - Start */
    if($core->usergroup['canUseReporting'] == 1) {
        /* if($core->usergroup['canViewAllAff'] == 0) {
          $inaffiliates = implode(',', $core->user['affiliates']);
          $extra_where = ' AND r.affid IN ('.$inaffiliates.') ';
          }

          if($core->usergroup['canViewAllSupp'] == 0) {
          $insuppliers = implode(',', $core->user['suppliers']);
          $extra_where .= ' AND r.spid IN ('.$insuppliers.') ';
          } */

        $additional_where = getquery_entities_viewpermissions();

        $quarter = currentquarter_info();

        $countall_current_quarterly = $db->fetch_field($db->query("SELECT count(*) as countall FROM ".Tprefix."reports r WHERE type='q' AND year='{$quarter[year]}' AND quarter='{$quarter[quarter]}'{$additional_where[extra]}"), 'countall');
        if($countall_current_quarterly > 0) {
            $countall_current_quarterly_unfinalized = $db->fetch_field($db->query("SELECT count(*) as countall FROM ".Tprefix."reports r WHERE type='q' AND year='{$quarter[year]}' AND quarter='{$quarter[quarter]}' AND status='0'{$additional_where[extra]}"), 'countall');
        }
        else {
            $countall_current_quarterly_unfinalized = 0;
        }

        $query = $db->query("SELECT r.quarter, r.year, s.companyName, a.name AS affiliate_name
							FROM ".Tprefix."reports r JOIN ".Tprefix."entities s ON (r.spid=s.eid) JOIN ".Tprefix."affiliates a ON (r.affid=a.affid)
							WHERE r.type='q' AND r.status='0'{$additional_where[extra]}
							ORDER BY r.initDate DESC
							LIMIT 0, 3");
        if($db->num_rows($query) > 0) {
            while($due_report = $db->fetch_array($query)) {
                $due_reports_list .= "<li>Q{$due_report[quarter]} {$due_report[year]} - {$due_report[companyName]} / {$due_report[affiliate_name]}</li>";
            }
        }
        else {
            $due_reports_list = '<li>'.$lang->na.'</li>';
        }

        $query = $db->query("SELECT r.quarter, r.year, s.companyName, a.name AS affiliate_name
							FROM ".Tprefix."reports r JOIN ".Tprefix."entities s ON (r.spid=s.eid) JOIN ".Tprefix."affiliates a ON (r.affid=a.affid)
							WHERE r.type='q' AND r.status='1'{$additional_where[extra]}
							ORDER BY r.finishDate DESC
							LIMIT 0, 3");
        if($db->num_rows($query) > 0) {
            while($last_report = $db->fetch_array($query)) {
                $last_reports_list .= "<li>Q{$last_report[quarter]} {$last_report[year]} - {$last_report[companyName]} / {$last_report[affiliate_name]}</li>";
            }
        }
        else {
            $last_reports_list = '<li>'.$lang->na.'</li>';
        }

        eval("\$reporting_box = \"".$template->get('portal_reporting')."\";");
    }
    /* Reporting module box - End */

    /* We're here to help box - Start */
    $lang->callonnum = $lang->sprint($lang->callonnum, '+961-1-218862/3');
    /* We're here to help box - End */

    /* World time box - Start */
    $gmttime = gmmktime(gmdate('H'), gmdate('i'), gmdate('s'), gmdate('n'), gmdate('d'), gmdate('Y'));
    $gmtdate = gmdate('H:i');

    $tz_cities = array('Africa/Casablanca', 'Africa/Dakar', 'Africa/Abidjan', 'Europe/Paris', 'Africa/Lagos', 'Africa/Algiers', 'Africa/Tunis', 'Africa/Cairo', 'Asia/Beirut', 'Asia/Amman', 'Africa/Nairobi', 'Asia/Riyadh', 'Asia/Tehran', 'Asia/Dubai', 'Asia/Hong_Kong');
    $timezones_list = '<li>'.$lang->sprint($lang->timegmt, $gmtdate).'</li>';
    foreach($tz_cities as $timezone) {
        $timezone_obj = new DateTimeZone($timezone);
        $time_obj = new DateTime('now', $timezone_obj);
        $timezone_city = str_replace('_', ' ', explode('/', $timezone));

        $timezones_list .= '<li>'.$lang->sprint($lang->timecity, date('H:i', $gmttime + $timezone_obj->getOffset($time_obj)), ucwords($timezone_city[1])).'</li>';
    }
    /* World time box - End */

    /* Portal Icons Section - Start */
    $portalicons_options = array(
            array('img' => 'portal-sourcing.png', 'title' => 'sourcing', 'link' => 'index.php?module=sourcing/listpotentialsupplier', 'permission' => 'canUseSourcing'),
            array('img' => 'portal-surveys.png', 'title' => 'surveys', 'link' => 'index.php?module=surveys/list', 'permission' => 'canUseSurveys'),
            array('img' => 'portal-reporting.png', 'title' => 'reporting', 'link' => 'index.php?module=reporting/home', 'permission' => 'canUseReporting'),
            array('img' => 'portal-attendance.png', 'title' => 'attendance', 'link' => 'index.php?module=attendance/requestleave', 'permission' => 'canUseAttendance'),
            array('img' => 'portal-crm.png', 'title' => 'crm', 'link' => 'index.php?module=crm/fillvisitreport', 'permission' => 'crm_canFillVisitReports'),
            array('img' => 'portal-userslist.png', 'title' => 'employeeslist', 'link' => 'users.php?action=userslist', 'permission' => 'canAccessSystem'),
            array('img' => 'portal-affiliates.png', 'title' => 'affiliateslist', 'link' => 'index.php?module=profiles/affiliateslist', 'permission' => 'canAccessSystem'),
            array('img' => 'portal-suppliers.png', 'title' => 'supplierslist', 'link' => 'index.php?module=profiles/supplierslist', 'permission' => 'canAccessSystem'),
            array('img' => 'portal-customers.png', 'title' => 'customerslist', 'link' => 'index.php?module=profiles/customerslist', 'permission' => 'canAccessSystem'),
            array('img' => 'portal-grouppurchase.png', 'title' => 'grouppurchase', 'link' => 'index.php?module=grouppurchase/createforecast', 'permission' => 'canUseGroupPurchase'),
            array('img' => 'portal-hr.png', 'title' => 'humanresources', 'link' => 'index.php?module=hr/employeeslist', 'permission' => 'canUseHR'),
            array('img' => 'portal-filesharing.png', 'title' => 'filesharing', 'link' => 'index.php?module=filesharing/fileslist', 'permission' => 'canUseFileSharing'),
            array('img' => 'portal-successstories.png', 'title' => 'successstories', 'link' => 'index.php?module=filesharing/fileslist&amp;ffid=6', 'permission' => 'canUseFileSharing')
    );

    foreach($portalicons_options as $icon) {
        if($core->usergroup[$icon['permission']] == 1) {
            $portalicons .= ' <span style="display:inline-block; width: 100px; text-align:center; vertical-align:top;"><a href="'.$icon['link'].'"><img src="images/icons/'.$icon['img'].'" border="0" alt="'.$lang->{$icon['title']}.'"><br />'.$lang->{$icon['title']}.'</a></span> ';
        }
    }
    /* Portal Icons Section - End */

    /* Portal Calendar Section - Start */
    $current_date = getdate(TIME_NOW);
    $current_date['weekday'] = $lang->{strtolower($current_date['weekday'])};
    $current_date['monthname'] = $lang->{strtolower(date('F', TIME_NOW))};
    /* Portal Calendar Section - End */

    /* Portal Currencies Section - Start */
    $currency_obj = new Currencies('USD');
    $affiliates_currencies['EUR']['name'] = array('alphaCode' => 'EUR', 'name' => 'EUR');
    $affiliates_currencies['GBP']['name'] = array('alphaCode' => 'GBP', 'name' => 'GBP');

    $affiliatecurrenciesquery = $db->query('SELECT affid, cur.alphaCode, cur.name
											FROM '.Tprefix.'countries c
											JOIN '.Tprefix.'currencies cur ON (c.mainCurrency=cur.numCode)
											WHERE affid IN('.implode(',', $core->user['affiliates']).')
											ORDER BY cur.alphaCode');
    while($country_currency = $db->fetch_assoc($affiliatecurrenciesquery)) {
        $affiliates_currencies[$country_currency['alphaCode']]['name'] = $country_currency;
    }

    foreach($affiliates_currencies as $code => $cname) {
        $currency = $currency_obj->get_currency_by_alphacode($code);
        $fxrates[$currency['alphaCode']] = $currency_obj->get_latest_fxrate($currency['alphaCode'], array('incDate' => 1));

        if(!empty($fxrates[$currency['alphaCode']]['rate'])) {
            $currencysrates_list .= '<li><span title="'.round(1 / $fxrates[$currency['alphaCode']]['rate'], 4).'">'.$currency['alphaCode'].' '.$fxrates[$currency['alphaCode']]['rate'].'</span> <span class="smalltext" style="color:#CCC;">'.date($core->settings['dateformat'], $fxrates[$currency['alphaCode']]['date']).'</span></li>';
        }
    }

    //$curreny = new Currencies('USD');
    //$curreny->set_fx_rates('http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml');
    //$currencies =  get_specificdata('currencies ', array('alphaCode', 'alphaCode'), 'alphaCode', 'alphaCode', 'alphaCode', 0);
    //$currencies = $curreny->get_average_fxrates_transposed(array('GBP', 'ZAR'), array('from' => strtotime('last week'), 'to' => TIME_NOW), 'alphaCode');
    //$currencyfrom_selectlist = parse_selectlist('currencyFrom', 1, $currencies, 'USD', '');
    //$currencyto_selectlist = parse_selectlist('currencyTo', 1, $currencies, 'EUR', '');
    //$xml = simplexml_load_string(fx_fromfeed('USD', 'EUR'));
    //$currencies['usdeuro'] = $xml[0];
    /* Portal Currencies Section - End */

    $menu = parse_moduleslist('portal', 'modules');

    eval("\$portal = \"".$template->get('portal')."\";");
    output_page($portal);
}
else {
    if($core->input['action'] == 'do_submitsupportticket') {
        if(is_empty($core->input['message'], $core->input['subject'])) {
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            exit;
        }

        if(!empty($core->user['skype'])) {
            $core->input['message'] .= '<hr />Skype: '.$core->user['skype'];
        }
        $email_data = array(
                'from_email' => $core->user['email'],
                'from' => $core->user['firstName'].' '.$core->user['lastName'],
                'to' => 'support@ocos.orkila.com',
                'subject' => $core->input['subject'],
                'message' => $core->input['message']
                //'attachments' => array($core->input['attachment'])
        );

        $mail = new Mailer($email_data, 'php');

        if($mail->get_status() === true) {
            $log->record($core->input['subject']);
            output_xml("<status>true</status><message>{$lang->ticketsubmitted}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorsendingemail}</message>");
        }
    }
}
?>