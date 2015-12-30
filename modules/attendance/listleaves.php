<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * List Leaves
 * $module: attendance
 * $id: listleaves.php
 * Last Update: @zaher.reda 	January 3, 2011 | 10:00 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

//$lang->load('attendance');
if(!$core->input['action']) {
    if(value_exists('users', 'reportsTo', $core->user['uid']) || value_exists('affiliates', 'generalManager', $core->user['uid'])) {
        $is_supervisor = true;
    }

    $sort_query = 'requestTime DESC, username ASC';
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

    if($core->usergroup['attendance_canViewAllLeaves'] == 0) {
        $where = ' WHERE ';
        $uid_where = ' (l.uid="'.$core->user['uid'].'"';
        if($core->usergroup['attendance_canViewAffAllLeaves'] == 1) {
            if(is_array($core->user['hraffids'])) {
                $query_extrawhere = 'affid IN ('.implode(', ', $core->user['hraffids']).') OR ';
            }
            $query = $db->query("SELECT u.uid, u.displayName FROM ".Tprefix."users u JOIN ".Tprefix."affiliatedemployees ae ON (u.uid=ae.uid) WHERE (".$query_extrawhere."(canHr=1 AND ae.uid=".$core->user['uid'].") OR (ae.isMain=1 AND ae.affid='{$core->user[mainaffiliate]}') OR u.reportsTo='{$core->user[uid]}') AND u.gid!=7 AND u.uid!={$core->user[uid]} ORDER BY displayName ASC");

            //$query = $db->query("SELECT u.uid FROM ".Tprefix."users u JOIN ".Tprefix."affiliatedemployees ae ON (u.uid=ae.uid) WHERE isMain=1 AND affid='{$core->user[mainaffiliate]}'");
            if($db->num_rows($query) > 1) {
                while($user = $db->fetch_assoc($query)) {
                    $users[] = $user['uid'];
                }
                $uid_where .= ' OR l.uid IN ('.implode(',', $users).')';
            }
        }

        $reporting_users = get_specificdata('users', 'uid', 'uid', 'uid', '', 0, "reportsTo='{$core->user[uid]}'");
        if(is_array($reporting_users) && !empty($reporting_users)) {
            $uid_where .= ' OR l.uid IN ('.implode(',', $reporting_users).')';
        }
        $uid_where .= ')';
    }
    else {
        $where = '';
    }

    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('employee', 'date', 'fromDate', 'toDate', 'type'),
                    'overwriteField' => array('employee' => parse_selectlist('filters[employee][]', 1, get_specificdata('users l', array('uid', 'displayName'), 'uid', 'displayName', 'displayName', 0, $uid_where), $core->input['filters']['employee'], 1, '', array('multiplesize' => 3)), 'type' => parse_selectlist('filters[type][]', 1, get_specificdata('leavetypes', array('ltid', 'title'), 'ltid', 'title', '', 0), $core->input['filters']['type'], 1, '', array('multiplesize' => 3))),
                    'fieldsSequence' => array('employee' => 1, 'date' => 2, 'fromDate' => 3, 'toDate' => 4, 'type' => 5)
            ),
            'process' => array(
                    'filterKey' => 'lid',
                    'mainTable' => array(
                            'name' => 'leaves',
                            'filters' => array('employee' => array('operatorType' => 'multiple', 'name' => 'uid'), 'date' => array('operatorType' => 'date', 'name' => 'requestTime'), 'fromDate' => array('operatorType' => 'date', 'name' => 'fromDate'), 'toDate', 'type' => array('operatorType' => 'multiple', 'name' => 'type')),
                    )
            )
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();

    $filters_row_display = 'hide';
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = ' AND ';
        if(empty($uid_where)) {
            $filter_where = ' WHERE ';
        }
        $filter_where .= $filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_filter_where = ' '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));
    /* Perform inline filtering - END */

    $multipage_where = $uid_where.$multipage_filter_where; //"uid='{$core->user[uid]}'";
    if(!empty($multipage_filter_where)) {
        $multipage_where = $multipage_filter_where;
    }

    $query = $db->query("SELECT l.*, l.fromDate AS fromdate, l.toDate AS till, l.requestTime AS daterequested, Concat(u.firstName, ' ', u.lastName) AS employeename
						FROM ".Tprefix."leaves l
						JOIN ".Tprefix."users u ON (u.uid=l.uid)
						{$where}{$uid_where}{$filter_where}
						ORDER BY {$sort_query}
						LIMIT {$limit_start}, {$core->settings[itemsperlist]}");

    if($db->num_rows($query) > 0) {
        while($leave = $db->fetch_assoc($query)) {
            $leave['fromDate_output'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['fromDate']);
            $leave['toDate_output'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['toDate']);
            $leave['requestTime_output'] = date($core->settings['dateformat'], $leave['requestTime']).' '.date($core->settings['timeformat'], $leave['requestTime']);
            $leave['requestKey_encoded'] = base64_encode($leave['requestKey']);

            if(!isset($type_cache[$leave['type']])) {
                $leavetype_details = $db->fetch_assoc($db->query("SELECT name, title, description,isBusiness FROM ".Tprefix."leavetypes WHERE ltid='".$db->escape_string($leave['type'])."'"));
                if(!empty($lang->{$leavetype_details['name']})) {
                    $leavetype_details['title'] = $lang->{$leavetype_details['name']};
                }
                $tmplan_link = '';
                if($leavetype_details['isBusiness'] == 1 && $core->usergroup['canUseTravelManager'] == 1) {
                    $tmplan = TravelManagerPlan::get_plan(array('lid' => $leave['lid']), array('returnarray' => false));
                    if(!is_object($tmplan)) {
                        $url = 'index.php?module=travelmanager/plantrip&lid='.$leave['lid'];
                        $tmplan_link = "<a target='_blank' href='{$url}'><img src='{$core->settings[rootdir]}/images/icons/red_flag.gif' border='0' title='{$lang->tmplanneeded}' alt='{$lang->tmplanneeded}' /></a>";
                    }
                }
                $leave['type_output'] = $leavetype_details['title'];
                if(!empty($leavetype_details['description'])) {
                    $leave['type_output'] .= ' ('.$leavetype_details['description'].')';
                }
            }
            else {
                $leave['type_output'] = $type_cache[$leave['type']];
            }

            $status = array();
            $query2 = $db->query("SELECT isApproved, COUNT(isApproved) AS approvecount FROM ".Tprefix."leavesapproval WHERE lid='{$leave[lid]}' GROUP BY isApproved");
            while($approve = $db->fetch_assoc($query2)) {
                if($approve['isApproved'] == 1) {
                    $status['approved'] = $approve['approvecount'];
                }
                else {
                    $status['notapproved'] = $approve['approvecount'];
                }
            }

            $rowclass = '';
            if($status['approved'] == array_sum($status)) {
                if($core->input['filterby'] == 'isapproved' && $core->input['filtervalue'] == '0') {
                    continue;
                }
                $rowclass = 'greenbackground';
            }
            elseif($status['approved'] < array_sum($status) && !empty($status['approved'])) {
                if($core->input['filterby'] == 'isapproved' && $core->input['filtervalue'] == '1') {
                    continue;
                }
                $rowclass = 'yellowbackground';
            }
            else {
                if($core->input['filterby'] == 'isapproved' && $core->input['filtervalue'] == '1') {
                    continue;
                }
                $rowclass = 'unapproved';
            }

            $approve_link = '';
            if($leave['uid'] != $core->user['uid'] || $is_supervisor == true) {
                if(value_exists('leavesapproval', 'isApproved', 0, "lid='{$leave[lid]}' AND uid='{$core->user[uid]}'")) {
                    $approve_link = "<a href='#{$leave[lid]}' id='approveleave_{$leave[requestKey_encoded]}_attendance/listleaves_icon'><img src='{$core->settings[rootdir]}/images/valid.gif' border='0' alt='{$lang->approveleave}' id='approveimg_{$leave[lid]}' /></a>";
                }
            }
            $edit_link = $revoke_link = '';
            $user_obj = new Users($leave['uid']);
            if(is_object($user_obj)) {
                $user_reportsto_obj = $user_obj->get_reportsto();
            }
            if(is_object($user_reportsto_obj)) { /* Incase the user doese not Reprot TO any other user */
                $reports_to = $user_reportsto_obj->get()['uid'];
            }
            if($core->usergroup['attenance_canApproveAllLeaves'] == 1 || (($core->usergroup['hr_canHrAllAffiliates'] == 1 || $reports_to == $core->user['uid'] || $leave['uid'] == $core->user['uid']) && TIME_NOW < ($leave['toDate'] + (60 * 60 * 24 * $core->settings['attendance_caneditleaveafter'])) || (TIME_NOW > $leave['toDate'] && $status['approved'] != array_sum($status)))) {
                $edit_link = "<a href='index.php?module=attendance/editleave&amp;lid={$leave[lid]}'><img src='{$core->settings[rootdir]}/images/icons/edit.gif' border='0' alt='{$lang->modifyleave}' /></a>";
                $revoke_link = "<a href='#{$leave[lid]}' id='revokeleave_{$leave[lid]}_attendance/listleaves_icon'><img src='{$core->settings[rootdir]}/images/invalid.gif' border='0' alt='{$lang->revokeleave}' /></a>";
            }
            $tmplan = TravelManagerPlan::get_plan(array('lid' => $leave['lid']));
            if(is_object($tmplan)) {
                $tm_link = "<a target='_blank' href='".DOMAIN."/index.php?module=travelmanager/viewplan&referrer=plan&lid={$leave['lid']}&id={$tmplan->tmpid}' border='0' alt='{$lang->modifyleave}' /><img src='{$core->settings[rootdir]}/images/icons/report.gif' border='0' /></a>";
            }
            unset($tmplan);
            eval("\$requestslist .= \"".$template->get('attendance_listleaves_leaverow')."\";");
        }
    }

    if(empty($requestslist)) {
        $requestslist = "<tr><td colspan='6' style='text-align:center;'>{$lang->norequestsavailable}</td></tr>";
    }

    $multipages = new Multipages('leaves l', $core->settings['itemsperlist'], $multipage_where);

    $requestslist .= '<tr><td colspan="6">'.$multipages->parse_multipages().'&nbsp;</td></tr>'; //<td colspan="3"><a href="index.php?module=attendance/leavesstats"><img src="images/icons/report.gif" alt="'.$lang->viewbalances.'" border="0" /> '.$lang->viewbalances.'</a></td>
//	if($is_supervisor == true) {
//		eval("\$moderationtools .= \"".$template->get('attendance_listleaves_moderationtools')."\";");
//	}

    eval("\$listleavespage = \"".$template->get('attendance_listleaves')."\";");
    output_page($listleavespage);
}
else {
    if($core->input['action'] == 'perform_revokeleave') {
        $lid = intval($core->input['torevoke']);
        $user_obj = new Users($core->user['uid']);

        $leave_obj = new Leaves($lid, false);
        $leave = $leave_obj->get();
        $leave_type = $leave_obj->get_leavetype(false)->get();
        $leave_user = $leave_obj->get_requester()->get();
        $reports_to = $leave_obj->get_requester()->get_reportsto()->get()['uid'];
        if(!$core->usergroup['attenance_canApproveAllLeaves'] == 1 && (($core->usergroup['hr_canHrAllAffiliates'] != 1 && $reports_to != $core->user['uid'] && $leave_user['uid'] != $core->user['uid']) && !TIME_NOW < ($leave['toDate'] + (60 * 60 * 24 * $core->settings['attendance_caneditleaveafter'])) && !(TIME_NOW > $leave['toDate']))) {
            output_xml("<status>false</status><message>{$lang->errorrevoking}</message>");
            exit;
        }

        if(value_exists('users', 'reportsTo', $core->user['uid'], "uid='{$leave[uid]}'")) {
            $on_behalf = true;
        }

        $leave['additionalInfo'] = parse_additionaldata($leave, $leave_type['additionalFields']);
        if(is_array($leave['additionalInfo']) && !empty($leave['additionalInfo'])) {
            $leave['additionalInfo'] = ' ('.implode(' ', $leave['additionalInfo']).')';
        }

        $query = $db->query("SELECT isApproved, COUNT(*) AS counter FROM ".Tprefix."leavesapproval WHERE lid='{$lid}' GROUP BY isApproved");
        while($approval = $db->fetch_assoc($query)) {
            $leave_approval[$approval['isApproved']] = $approval['counter'];
            $leave_approval['total'] += $approval['counter'];
        }

        $leavetype_details = $leave_type;
        if($leave_approval['total'] == $leave_approval[1]) { //1 = isApproved
            $to_inform = unserialize($leave['affToInform']);
            if(is_array($to_inform)) {
                $mailingLists_attr = 'altMailingList';
                if($leavetype_details['isBusiness'] == 1) {
                    $mailingLists_attr = 'mailingList';
                }

                $to_notify = get_specificdata('affiliates', array('affid', $mailingLists_attr), 'affid', $mailingLists_attr, '', 0, 'affid IN ('.implode(',', $to_inform).') AND '.$mailingLists_attr.' != ""');
            }
        }
        else {
            $query = $db->query("SELECT u.uid, u.email FROM ".Tprefix."users u JOIN ".Tprefix."leavesapproval la ON (la.uid=u.uid) WHERE lid='{$lid}'");
            while($approver = $db->fetch_assoc($query)) {
                if($on_behalf == true && $approver['uid'] == $core->user['uid']) {
                    continue;
                }
                $to_notify[] = $approver['email'];
            }
        }
        if($leave_obj->createAutoResp == 1) {
            $leave_obj->delete_autoresponder();
        }
        $query = $db->delete_query('leaves', 'lid='.$lid);
        if($query && $db->affected_rows() > 0) {
            //Reset Leave Balance - Start
            if(!value_exists('leavesapproval', 'isApproved', 0, 'lid='.$lid)) {
                $workingdays = count_workingdays($leave['uid'], $leave['fromDate'], $leave['toDate'], $leavetype_details['isWholeDay']);
                $leavestats_updatedetails = array(
                        'uid' => $leave['uid'],
                        'workingDays' => $workingdays,
                        'fromDate' => $leave['fromDate'],
                        'toDate' => $leave['toDate'],
                        'type' => $leave['type'],
                        'negativeWorkingDays' => true
                );
                $stat = new LeavesStats();
                $stat->generate_periodbased($leavestats_updatedetails);
            }
            //Reset Leave Balance - End

            $query2 = $db->delete_query('leavesapproval', "lid='{$lid}'");
            if($query2 && $db->affected_rows() > 0) {
                $lang->load('attendance_messages');
                //$to_inform = unserialize($leave['affToInform']);
                //$mailingLists = get_specificdata('affiliates', 'mailingList', 'affid', 'mailingList', '', 0, 'affid IN ('.implode(',', $to_inform).') AND mailingList != ""');
                if(date($core->settings['dateformat'], $leave['fromDate']) != date($core->settings['dateformat'], $leave['toDate'])) {
                    $todate_format = $core->settings['dateformat'].' '.$core->settings['timeformat'];
                }
                else {
                    $todate_format = $core->settings['timeformat'];
                }

                if(!empty($lang->{$leavetype_details['name']})) {
                    $leavetype_details['title'] = $lang->{$leavetype_details['name']};
                }
                $leave['type_output'] = $leavetype_details['title'];

                $email_data = array(
                        'from_email' => 'attendance@ocos.orkila.com',
                        'from' => 'Orkila Attendance System'
                );

                if($on_behalf == true) {
                    $email_data['to'] = $leave['email'];
                    if($leave_approval[1] == 0) {
                        $email_data['subject'] = $lang->sprint($lang->declineleavenotificationsubject, strtolower($leave['type_output']));
                        $email_data['message'] = $lang->sprint($lang->declineleavenotificationmessage, $leave_user['displayName'], strtolower($leave['type_output']), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['fromDate']), date($todate_format, $leave['toDate']));
                    }
                    else {
                        $email_data['subject'] = $lang->sprint($lang->revokeleavenotificationsubjectuser, strtolower($leave['type_output']), $leave['additionalInfo']);
                        $email_data['message'] = $lang->sprint($lang->revokeleavenotificationmessageuser, $leave_user['displayName'], strtolower($leave['type_output']), $leave['additionalInfo'], date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['fromDate']), date($todate_format, $leave['toDate']));
                    }
                    $mail = new Mailer($email_data, 'php');
                    if($mail->get_status() === true) {
                        $log->record('declinenotifyaemployee');
                    }
                }

                $email_data['to'] = $to_notify;
                $email_data['subject'] = $lang->sprint($lang->revokeleavenotificationsubject, $leave_user['displayName'], strtolower($leave['type_output']), $leave['additionalInfo']);
                $email_data['message'] = $lang->sprint($lang->revokeleavenotificationmessage, $leave_user['displayName'], strtolower($leave['type_output']), $leave['additionalInfo'], date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['fromDate']), date($todate_format, $leave['toDate']));

                $mail = new Mailer($email_data, 'php');

                if($mail->get_status() === true) {
                    $log->record('revokenotifyaffiliate', $to_notify);
                }

                if($core->input['referrer'] == 'email') {
                    redirect('index.php?module=attendance/listleaves', 3, $lang->leaverevoked);
                }
                else {
                    $js = "<script type=\"application/javascript\">\n\$(function() {\$(\"#popup_revokeleave\").remove(); \$(\"tr\[id='leave_{$lid}'\]\").hide();});</script>\n";
                    output_xml("<status>true</status><message>{$lang->leaverevoked}<![CDATA[{$js}]]></message>");
                }
            }
            else {
                if($core->input['referrer'] == 'email') {
                    error($lang->errorrevoking, 'index.php?module=attendance/listleaves&action=takeactionpage&requestKey='.base64_encode($leave['requestKey']).'&id='.base64_encode($lid));
                }
                else {
                    output_xml("<status>false</status><message>{$lang->errorrevoking}</message>");
                }
            }
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorrevoking}</message>");
        }
    }
    elseif($core->input['action'] == 'perform_approveleave') {
        $request_key = $db->escape_string($core->input['toapprove']);
        $request['notpiped'] = 1;
        if(isset($core->input['referrer'])) {
            $request['referrer'] = $db->escape_string($core->input['referrer']);
        }

        $request['requestkey'] = base64_decode($request_key);

        include './pipes/approve_leaverequest.php';
    }
    elseif($core->input['action'] == 'takeactionpage') {

        if(isset($core->input['id'], $core->input['requestKey'])) {
            $lid = base64_decode($core->input['id']);
            $leave_obj = new Leaves($lid, false);
            $leave = $leave_obj->get();
            $leave['requester'] = $leave_obj->get_requester()->get();
            $leave['type_details'] = $leave_obj->get_type(false)->get();

            $leave['fromDate_output'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['fromDate']);
            $leave['toDate_output'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['toDate']);
            if(!empty($lang->{$leave['type_details']['name']})) {
                $leave['type_details']['title'] = $lang->{$leave['type_details']['name']};
            }

            $leave['details_crumb'] = parse_additionaldata($leave, $leave['type_details']['additionalFields']);

            if(is_array($leave['details_crumb']) && !empty($leave['details_crumb'])) {
                $leave['details_crumb'] = ' - '.implode(' ', $leave['details_crumb']);
            }

            $leave['reason'] .= $leave_obj->parse_expenses();
            $leave['reason'] .= '<br/>'.$leave_obj->parse_approvalsapprovers(array('parselabel' => true));

            /* Conversation message --START */
            $leaemessag_obj = new LeavesMessages();
            $takeactionpage_conversation = $leave_obj->parse_messages(array('uid' => $core->user['uid']));
            /* Conversation  message --END */
            $tmplan = TravelManagerPlan::get_plan(array('lid' => $leave_obj->get_id()));
            if(is_object($tmplan)) {
                $preview_iteneraryframe = '<div id="container" style="width:100%;  margin: 0px auto;display:block;"><iframe style="width:100%;height:700px" src="'.DOMAIN.'/index.php?module=travelmanager/viewplan&referrer=plan&lid='.$lid.'&id='.$tmplan->tmpid.'&preview=1"></iframe></div>';
            }
            eval("\$takeactionpage = \"".$template->get('attendance_listleaves_takeaction')."\";");
            output_page($takeactionpage);
        }
    }
    elseif($core->input['action'] == 'perform_sendmessage') {
        $leavemessage_obj = new LeavesMessages();
        $leavemessage_obj->create_message($core->input['leavemessage'], $core->input['lid'], array('source' => 'emaillink'));
        /* Errors Should be handled Here */
        switch($leavemessage_obj->get_errorcode()) {
            case 0:
                $leavemessage_obj->send_message();
                switch($leavemessage_obj->get_errorcode()) {
                    case 0:
                        output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                        break;
                    case 5:
                        output_xml("<status>false</status><message>{$lang->successfullysaved} - {$lang->errorsendingemail}".$leavemessage_obj->get_errorcode()."</message>");
                        break;
                }
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                break;
            case 2:
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                break;
            case 3:
                output_xml("<status>false</status><message>{$lang->entryexists}</message>");
                break;
        }
        /* Need to have feedback message */
    }
    elseif($core->input['action'] == 'get_revokeleave') {
        eval("\$revokeleavebox = \"".$template->get('popup_revokeleave')."\";");
        output($revokeleavebox);
    }
    elseif($core->input['action'] == 'get_approveleave') {
        eval("\$approveleavebox = \"".$template->get('popup_approveleave')."\";");
        output($approveleavebox);
    }
}
?>