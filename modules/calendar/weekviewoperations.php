<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Fill up a visit report
 * $module: CRM
 * $id: weekviewoperations.php
 * Created: 	@tony.assaad 	May 28, 2012 | 11:21 AM
 * Last Update: @zaher.reda 	Julu 17, 2012 | 5:30 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->input['action'] == 'update_time') {
    if(empty($core->input['id']) && empty($core->input['todiff']) && empty($core->input['fromdiff'])) {
        echo '0';
        exit;
    }

    $core->input['id'] = $db->escape_string($core->input['id']);

    $leave = $db->fetch_assoc($db->query('SELECT lid, uid, fromDate, toDate, requestTime FROM '.Tprefix.'leaves WHERE lid='.$core->input['id']));
    if(!is_array($leave) || ($leave['uid'] != $core->user['uid'] && !value_exists('users', 'reportsTo', $core->user['uid'], "uid={$leave[uid]}"))) {
        echo '0';
        exit;
    }

    if(TIME_NOW > ($leave['toDate'] + ($core->settings['weeklyplan_editdays'] * 86400))) {
        echo '0';
        exit;
    }

    $new_time['toDate'] = $leave['toDate'];
    if(isset($core->input['todiff']) && !empty($core->input['todiff'])) {
        $new_time['toDate'] = $leave['toDate'] + $core->input['todiff'];
    }

    $new_time['fromDate'] = $leave['fromDate'];
    if(isset($core->input['fromdiff']) && !empty($core->input['fromdiff'])) {
        $new_time['fromDate'] = $leave['fromDate'] + $core->input['fromdiff'];
    }
    /* Check if it intersects with another leave */
    if(!value_exists('leaves', 'uid', $leave['uid'], 'lid!= '.$core->input['id'].' AND (('.$new_time['fromDate'].' BETWEEN fromDate AND toDate) OR ('.$new_time['toDate'].' BETWEEN fromDate AND toDate))')) {
        $query = $db->update_query('leaves', $new_time, 'lid='.$core->input['id']);
        $query = $db->update_query('visitreports', array('date' => $new_time['fromDate']), 'lid='.$core->input['id']);
        if(!$query) {
            echo '0';
        }
    }
    else {
        echo '0';
    }
}
elseif($core->input['action'] == 'delete_visit') {
    if(!empty($core->input['lid'])) {
        $id = $db->escape_string($core->input['lid']);
        $visit = $db->fetch_assoc($db->query('SELECT l.uid, l.lid, l.requestTime
				FROM '.Tprefix.'leaves l
				JOIN '.Tprefix.'users u ON (u.uid = l.uid)
				WHERE l.lid='.$db->escape_string($id)));
        /* Check if the visit request time is  more than 1 hour ago */
        if(TIME_NOW <= ($visit['requestTime'] + ($core->settings['weeklyplan_deletehours'] * 3600)) && ($visit['uid'] == $core->user['uid'] || value_exists('users', 'reportsTo', $core->user['uid'], "uid={$visit[uid]}"))) {
            $db->delete_query('leaves', 'lid='.$id);
            $delete_query = $db->delete_query('visitreports', 'lid='.$id);
            if($delete_query) {
                /* Hide the hourevent box and close the dialog on successfull deletion */
                header('Content-type: text/javascript');
                echo '$("#leave_'.$core->input['lid'].'").fadeOut();';
                echo '$("#popup_custvisitdetails").dialog("close");';
                exit;
            }
        }
    }
}
elseif($core->input['action'] == 'suggest_customervisits') {
    $core->input['cid'] = $db->escape_string($core->input['cid']);
    $core->input['uid'] = $db->escape_string($core->input['uid']);
    if(empty($core->input['cid'])) {
        exit;
    }
    $visits_query = $db->query('SELECT e.eid, e.companyName AS customer, vr.date
						FROM '.Tprefix.'visitreports vr
						JOIN '.Tprefix.'entities e ON (e.eid = vr.cid)
						WHERE (date < '.strtotime('-1 month').')
						AND e.eid IN (SELECT ase.eid
							FROM '.Tprefix.'assignedemployees ase JOIN '.Tprefix.'users u ON (u.uid=ase.uid) JOIN '.Tprefix.'entities e ON (e.eid=ase.eid)
							WHERE e.type = "c" AND ase.uid = '.intval($core->input['uid']).')
						AND e.eid != '.intval($core->input['cid']).'
						ORDER BY date DESC limit 0, 5');
    $cachearr['eid'] = array(0);
    while($suggestion = $db->fetch_assoc($visits_query)) {
        $cachearr['eid'][] = $suggestion['eid'];
        $suggestions_longtime .= '<li>'.$suggestion['customer'].' ('.date($core->settings['dateformat'], $suggestion['date']).')</li>';
    }
    /* Check leaves older than 1 month - START */
    $visits_count = $db->num_rows($visits_query);
    if($visits_count < 5) {
        $leaves_query = $db->query('SELECT e.companyName AS customer, l.fromDate AS date
						FROM '.Tprefix.'leaves l
						JOIN '.Tprefix.'entities e ON (e.eid = l.cid)
						WHERE (l.fromDate < '.strtotime('-1 month').')
						AND e.eid IN (SELECT ase.eid
						FROM '.Tprefix.'assignedemployees ase JOIN '.Tprefix.'users u ON (u.uid=ase.uid) JOIN '.Tprefix.'entities e ON (e.eid=ase.eid)
						WHERE e.type = "c" AND ase.uid = '.intval($core->input['uid']).')
						AND e.eid != '.intval($core->input['cid']).' AND e.eid NOT IN ('.implode(',', $cachearr['eid']).')
						ORDER BY date DESC limit 0, '.(5 - $visits_count));
        if($db->num_rows($leaves_query) > 0) {
            while($suggestion = $db->fetch_assoc($leaves_query)) {
                $suggestions_longtime .= '<li>'.$suggestion['customer'].' ('.date($core->settings['dateformat'], $suggestion['date']).')</li>';
            }
        }
    }
    /* Check leaves older than 1 month - END */
    if(!empty($suggestions_longtime)) {
        /* Return the suggestion in HTML format */
        echo $lang->custvisitedgrmonth.':<ul>'.$suggestions_longtime.'</ul>';
    }
}
elseif($core->input['action'] == 'get_popup_calendar_custvisitsdetails') {
    $visit = $db->fetch_assoc($db->query('SELECT l.*, u.displayName AS employeename, vr.vrid, vr.type, vr.purpose, vr.identifier, vr.affid, e.eid, e.companyName AS customername, finishDate
						FROM '.Tprefix.'leaves l
						JOIN '.Tprefix.'users u ON (u.uid = l.uid)
						JOIN '.Tprefix.'visitreports vr ON (l.lid = vr.lid)
						JOIN '.Tprefix.'entities e ON (e.eid = vr.cid)
						WHERE vr.lid='.intval($core->input['id'])));

    if(!empty($visit)) {
        $visit['fromDate_output'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $visit['fromDate']);
        $visit['toDate_output'] = date($core->settings['timeformat'], $visit['toDate']);
        parse_calltype($visit['type']);
        parse_callpurpose($visit['purpose']);

        $visit['employeename'] .= '<br />';
        if($visit['uid'] == $core->user['uid']) {
            $visit['employeename'] = '';
        }

        $visit['customername_output'] = '<a href="index.php?module=profiles/entityprofile&amp;eid='.$visit['eid'].' "target="_blank" title="{$lang->customerprofile}">'.$visit['customername'].'</a><br />';
        if(!value_exists('users', 'reportsTo', $core->user['uid'], "uid={$visit[uid]}") && !value_exists('affiliatedemployees', 'canAudit', 1, "uid={$core->user[uid]} AND affid={$visit[affid]}")) {
            /* if the leave is not  for the logged in user, show "Customer Visit instead of customer name */
            if($visit['uid'] != $core->user['uid']) {
                $visit['customername_output'] = $lang->customervisit.'<br />';
            }
        }

        /* Parse Controls - START */
        if(($visit['uid'] == $core->user['uid']) || (value_exists('users', 'reportsTo', $core->user['uid'], "uid={$visit[uid]}"))) {
            if(!empty($visit['finishDate'])) {
                $reportlink_querystring = 'module=crm/previewvisitreport&amp;referrer=list&amp;vrid='.$visit['vrid'];
            }
            else {
                $reportlink_querystring = 'module=crm/fillvisitreport&amp;identifier='.$visit['identifier'];
            }
            $control_icons = '<a href="index.php?'.$reportlink_querystring.'" target="_blank"><img src="./images/icons/report.gif" alt="'.$lang->editreport.'" border="0"/></a>';

            /* Edit leave icon */
            if(TIME_NOW < ($visit['toDate'] + ($core->settings['weeklyplan_editdays'] * 86400))) {
                $control_icons .= '<a href="index.php?module=attendance/editleave&amp;lid='.$visit['lid'].'" target="_blank"><img src="./images/icons/edit_gray.png" alt="'.$lang->editleave.'" border="0"/></a>';
            }

            /* Delete leave icon if the visit was created more than x hour ago. */
            if(TIME_NOW <= ($visit['requestTime'] + ($core->settings['weeklyplan_deletehours'] * 3600))) {
                $control_icons .= '<img id="deleltevisiticon_'.$visit['lid'].'" src="./images/icons/trash.png" alt="'.$lang->delete_visit.'" border="0" style="cursor:pointer" />';
            }
        }
        /* Parse Controls - END */
        eval("\$popup_custvisitdetails = \"".$template->get('popup_calendar_custvisitsdetails')."\";");
        echo $popup_custvisitdetails;
    }
}
elseif($core->input['action'] == 'do_perform_weekviewoperations') {
    if($core->usergroup['crm_canFillVisitReports'] == 0) {
        exit;
    }

    $required_fields = array('uid', 'pickDate_from', 'pickDate_to', 'fromHour', 'fromMinutes', 'toHour', 'toMinutes', 'cid');
    foreach($required_fields as $key) {
        if(empty($core->input[$key])) {
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            exit;
        }
    }

    $uid = $db->escape_string($core->input['uid']);

    $fromdate = strtotime($core->input['pickDate_from'].' '.$core->input['fromHour'].':'.$core->input['fromMinutes'].':00');
    $todate = strtotime($core->input['pickDate_to'].' '.$core->input['toHour'].':'.$core->input['toMinutes'].':00');
    $day = strtotime($core->input['pickDate_from']);
    $day_details = getdate_custom($day);
    if(is_empty($fromdate, $todate)) {
        error($lang->unspecifieddates);
    }
    /* check if no leave intersects for the user in the same given date - START */
    if(value_exists('leaves', 'uid', $uid, "(fromDate BETWEEN {$fromdate} AND {$todate} OR toDate BETWEEN {$fromdate} AND {$todate})")) {
        output_xml("<status>false</status><message>{$lang->requestintersectsleave}</message>");
        exit;
    }
    /* check if no leave intersects for the user in the same given date - END */

    if($fromdate > $todate) {
        output_xml("<status>false</status><message>{$lang->wrongdates}</message>");
        exit;
    }

    /* Get leave user */
    $leave_user = $db->fetch_assoc($db->query('SELECT u.uid, assistant, reportsTo, mobile, affid AS mainaffiliate FROM '.Tprefix.'users u JOIN '.Tprefix.'affiliatedemployees ae ON (ae.uid=u.uid) WHERE isMain=1 AND u.uid='.$uid));

    /* Get customer details */
    $customer = $db->fetch_assoc($db->query("SELECT eid AS cid, companyName, addressLine1 FROM ".Tprefix."entities WHERE eid=".$db->escape_string($core->input['cid'])));

    /* insert approved leave ---START */
    $leave_data = array('uid' => $leave_user['uid'],
            'fromDate' => $fromdate,
            'toDate' => $todate,
            "requestKey" => substr(md5(uniqid(microtime())), 1, 10),
            'type' => 10, //customer visit
            'contactPerson' => $leave_user['assistant'],
            'addressWhileAbsent' => $customer['addressLine1'],
            'phoneWhileAbsent' => $leave_user['mobile'],
            'requestTime' => TIME_NOW,
            'cid' => $customer['cid']
    );

    $query = $db->insert_query('leaves', $leave_data);
    if($query) {
        $lid = $db->last_id();
        $log->record($lid);
        $sequence = 1;
        $db->insert_query('leavesapproval', array('lid' => $lid, 'uid' => $uid, 'isApproved' => 1, 'timeApproved' => TIME_NOW, 'sequence' => $sequence));

        /* insert visitor reports --START */
        $identifier = substr(md5(uniqid(microtime())), 1, 10);
        $visitreport_main = array(
                'uid' => $uid,
                'identifier' => $identifier,
                'cid' => $customer['cid'],
                'affid' => $leave_user['mainaffiliate'],
                'date' => $fromdate,
                'type' => $core->input['type'],
                'purpose' => $core->input['purpose'],
                'isDraft' => 1,
                'isLocked' => 0,
                'lid' => $lid
        );
        $query_visitreport = $db->insert_query('visitreports', $visitreport_main);
        /* insert visitor reports --END */
        if($query_visitreport) {
            header('Content-type: text/xml+javascript');
            $value = array(
                    'fromDate_output' => date('H:i', $fromdate),
                    'toDate_output' => ' - <span id="toTime_'.$lid.'">'.date('H:i', $todate).'</span>',
                    'lid' => $lid,
                    'identifier' => $identifier,
                    'customername' => $customer['companyName'],
                    'cid' => $customer['cid']
            );

            $depth = 1800;
            $boxsize['top'] = (($fromdate - $day) / $depth) * 20;
            $boxsize['height'] = (($todate - $fromdate) / $depth) * 20;
            $boxsize['left'] = 62 + (170 * ($day_details['wdayiso'] - 1)) + (($day_details['wdayiso'] - 1) * 1);
            $boxsize['width'] = 170;

            $value['customername_prefix'] = '<br />';
            if(($leave_data['toDate'] - $leave_data['fromDate']) <= $depth) {
                $value['toDate_output'] = '';
                $value['customername_prefix'] = '';
            }

            eval("\$content = \"".$template->get('calendar_weekview_entry')."\";");
            //$content = gzip_compression($content);
            output_xml('<status>true</status><message><![CDATA[<script>drawbox(\''.$db->escape_string($content).'\');</script>]]></message>');
            exit;
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
        }
    }
    else {
        output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
    }
}
function parse_callpurpose(&$value) {
    global $lang;

    switch($value) {
        case '1':
            $value = $lang->followup;
            break;
        case '2':
            $value = $lang->service;
            break;
        default: break;
    }
}

function parse_calltype(&$value) {
    global $lang;

    switch($value) {
        case '1':
            $value = $lang->facetoface;
            break;
        case '2':
            $value = $lang->telephonecall;
            break;
        default: break;
    }
}

?>