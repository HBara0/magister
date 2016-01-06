<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Request Leave
 * $module: attendance
 * $id: requestleave.php
 * Last Update: @tony.assaad	June 21, 2012 | 12:12 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $action = 'requestleave';
    $tmwarning_show = 'style="display:none"';
    if(empty($core->user_obj->get_hrinfo()['joinDate'])) {
        error('Your HR file does not have your join date. Please contact your HR Manager to correct this.');
    }

    if($core->usergroup['attendance_canViewAffAllLeaves'] == 1) {
        if($core->usergroup['attendance_canRequestAllLeaves'] == 1) {
            $query = $db->query("SELECT u.uid, u.displayName FROM ".Tprefix."users u JOIN ".Tprefix."affiliatedemployees ae ON (u.uid=ae.uid) WHERE u.gid!=7 AND u.uid!={$core->user[uid]} ORDER BY displayName ASC");
        }
        else {
            $employees[$core->user['uid']] = $core->user['displayName'];
            if(is_array($core->user['hraffids'])) {
                $query_extrawhere = 'affid IN ('.implode(', ', $core->user['hraffids']).') OR ';
            }
            $query = $db->query("SELECT u.uid, u.displayName FROM ".Tprefix."users u JOIN ".Tprefix."affiliatedemployees ae ON (u.uid=ae.uid) WHERE (".$query_extrawhere."(canHr=1 AND ae.uid=".$core->user['uid'].") OR (ae.isMain=1 AND ae.affid='{$core->user[mainaffiliate]}') OR u.reportsTo='{$core->user[uid]}') AND u.gid!=7 AND u.uid!={$core->user[uid]} ORDER BY displayName ASC");
        }
        while($user = $db->fetch_assoc($query)) {
            $employees[$user['uid']] = $user['displayName'];
        }
        $show_onbehalf = true;
    }
    else {
        if(value_exists('users', 'reportsTo', $core->user['uid'])) {
            $employees = get_specificdata('users', array('uid', 'displayName'), 'uid', 'displayName', '', 0, "reportsTo='{$core->user[uid]}' AND gid!=7");
            $employees[$core->user['uid']] = $core->user['displayName'];
            asort($employees);
            $show_onbehalf = true;
        }
    }

    if($show_onbehalf == true) {
        $is_supervisor = true;
        $employees_list = parse_selectlist('uid', 1, $employees, $core->user['uid'], '', '', array('blankstart' => true));
        $requestonbehalf_field = '<tr><td width="18%">'.$lang->reqleaveonbehalf.'</td><td>'.$employees_list.'</td></tr>';
    }
    $employeeshift = $db->fetch_assoc($db->query("SELECT ws.* FROM ".Tprefix."employeesshifts es JOIN ".Tprefix."workshifts ws ON (ws.wsid=es.wsid) WHERE es.uid='{$core->user[uid]}' AND ((".TIME_NOW." BETWEEN es.fromDate AND es.toDate) OR es.fromDate IS NULL)"));

    $leave_actual_times['fromHour'] = $employeeshift['onDutyHour'];
    $leave_actual_times['toHour'] = $employeeshift['offDutyHour'];
    $leave_actual_times['fromMinutes'] = $employeeshift['onDutyMinutes'];
    $leave_actual_times['toMinutes'] = $employeeshift['offDutyMinutes'];
    foreach($leave_actual_times as $key => $val) {
        $hidden_fields .= '<input type="hidden" value="'.$val.'" name="'.$key.'" id="'.$key.'" />';
    }
    $lang->betweenhours = $lang->sprint($lang->betweenhours, $leave_actual_times['fromHour'], $leave_actual_times['fromMinutes'], $leave_actual_times['toHour'], $leave_actual_times['toMinutes'], 0).$hidden_fields;

    $query = $db->query("SELECT * FROM ".Tprefix."leavetypes WHERE isActive = 1 ORDER BY name ASC");
    while($type = $db->fetch_assoc($query)) {
        if($type['restricted'] == 1 && $is_supervisor != true) {
            continue;
        }
        /**
         * Temporary work around for Business Travel leave type.
         */
        if($type['ltid'] == 14 && $core->usergroup['canUseTravelManager'] == 0) {
            continue;
        }
        if(!empty($lang->{$type['name']})) {
            $type['title'] = $lang->{$type['name']};
        }
        if(!empty($type['description'])) {
            $type['description'] = ' ('.$type['description'].')';
        }
        $leave_types[$type['ltid']] = $type['title'].$type['description'];
    }

    $leavetypes_list = parse_selectlist('type', 4, $leave_types, '1');

    $telephone = explode('-', $core->user['mobile']);
    $telephone['intcode'] = &$telephone[0];
    $telephone['areacode'] = &$telephone[1];
    $telephone['number'] = &$telephone[2];

    /* if(!empty($core->user['building'])) {
      $leave['fulladdress'] .= $core->user['building'].' - ';
      }

      //$core->user['fulladdress'] = $core->user['building'];
      if(!empty($core->user['postCode'])) {
      $leave['addressWhileAbsent'] .= $core->user['postCode'].', ';
      }

      if(!empty($core->user['addressLine1'])) {
      $leave['addressWhileAbsent'] .= $core->user['addressLine1'].' ';
      }

      if(!empty($core->user['addressLine2'])) {
      $leave['addressWhileAbsent'] .= $core->user['addressLine2'].', ';
      }

      if(!empty($core->user['city'])) {
      $leave['addressWhileAbsent'] .= $core->user['city'].' - ';
      }

      $core->user['country'] = $db->fetch_field($db->query("SELECT name FROM ".Tprefix."countries WHERE coid='{$core->user[country]}'"), 'name');

      $leave['addressWhileAbsent'] .= $core->user['country'];
     */

    $limitedemail_radiobutton = parse_yesno('limitedEmail', 11, 1);
    $to_inform = parse_toinform_list();
    /* Parse user own leave to plan */
    //d $user_leaveobjs = TravelManagerPlan::get_unplannedleaves();   // get only approved business leave
//    if(is_array($user_leaveobjs)) {
//        foreach($user_leaveobjs as $user_leaveobj) {
//            $userleave = $user_leaveobj->get();
//            $userleave['from'] = date($core->settings['dateformat'], $userleave['fromDate']);
//            $userleave['to'] = date($core->settings['dateformat'], $userleave['toDate']);
//            $userleave['origincity'] = $user_leaveobj->get_sourcecity()->get()[name];
//            $userleave['destinationcity'] = $user_leaveobj->get_destinationcity();
//            $userleave['leavetype'] = $user_leaveobj->get_leavetype($userleave['type'])->get()['title'];
//            $userown_leaves.='<option value="'.$userleave['lid'].'">'.$userleave['leavetype'].' - '.$userleave['origincity'].' - '.$userleave['from'].'-'.$userleave['to'].' </option>';
//        }
//    }
    $main_aff = new Affiliates($core->user['mainaffiliate'], false);
    if(!is_object($main_aff) || empty($main_aff->affid) || empty($main_aff->cpAccount)) {
        $autoresp_checkshow = 'style="display:none"';
    }
    else {
        $helptour = new HelpTour();
        $helptour->set_id('requestleavear_helptour');
        $helptour->set_cookiename('requestleavear_helptour');

        $touritems = array(
                'check_autoresp' => array('options' => 'tipLocation:right;', 'text' => 'Tick this box if you wish to automatically create an auto-responder. The system will create the auto-responder for you with the appropriate start and end time.'),
                'autoresp_subject' => array('ignoreid' => true, 'text' => 'You can optionally put a custom subject. The system will automatically append "Auto Responder" to your custom subject. If not specified the system will use %subject% which is then replaced by the subject of the original message.'),
                'autoresp_body' => array('ignoreid' => true, 'text' => 'You can optionally put a custom message. If not specified the system will use a default message that details the start and end date of your leave, whether you have limited access to email or not, and who is to be contacted for urgent issues.')
        );

        $helptour->set_items($touritems);
        $helptour = $helptour->parse();
    }
    $autoresp_show = 'style="display:none"';
    $autoresp_disabled = 'disabled="disabled"';
    eval("\$requestleavepage = \"".$template->get('attendance_requestleave')."\";");
    output_page($requestleavepage);
}
else {
    if($core->input['action'] == 'getaffiliates') {
        $leavetype_details = $db->fetch_assoc($db->query("SELECT isBusiness, noNotification FROM ".Tprefix."leavetypes WHERE ltid='".$db->escape_string($core->input['ltid'])."'"));

        output(parse_toinform_list($core->input['uid'], '', $leavetype_details));
    }
    elseif($core->input['action'] == 'getadditionalfields') {
        if(empty($core->input['uid']) || $core->input['uid'] == $core->user['uid']) {
            $core->input['uid'] = $core->user['uid'];
            $leave_user_obj = $core->user_obj;
        }
        else {
            $leave_user_obj = new Users($core->input['uid']);
        }
        $leavetype_obj = new LeaveTypes($core->input['ltid'], false);

        $fields = $leavetype_obj->parse_additonalfields();
        output($fields);
    }
    elseif($core->input['action'] == 'getleavetime') {
        $ltid = $db->escape_string($core->input['ltid']);

        if(empty($core->input['uid'])) {
            $core->input['uid'] = $core->user['uid'];
            $core->input['affid'] = $core->user['mainaffiliate'];
        }
        if(empty($core->input['affid'])) {
            $core->input['affid'] = $db->fetch_field($db->query("SELECT affid FROM ".Tprefix."affiliatedemployees WHERE uid='".$db->escape_string($core->input['uid'])."' AND isMain=1"), 'affid');
        }
        $leave_type = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."leavetypes WHERE ltid='{$ltid}'"));
        $leave_policy_id = $ltid;
        if(!empty($leave_type['countWith'])) {
            $leave_policy_id = $leave_type['countWith'];
        }

        $leave_policy = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."affiliatesleavespolicies WHERE ltid='{$leave_policy_id}' AND affid={$core->input[affid]}"));

        if(isset($core->input['fromDate']) && isset($core->input['toDate'])) {
            $fromdate = explode('-', $core->input['fromDate']);

            $fromdate_count_workingdays = $core->input['fromDate'] = mktime(0, 0, 0, $fromdate[1], $fromdate[0], $fromdate[2]);
            $todate = explode('-', $core->input['toDate']);

            $todate_count_workingdays = $core->input['toDate'] = mktime(23, 0, 0, $todate[1], $todate[0], $todate[2]);

            $workshift = $db->fetch_assoc($db->query("SELECT ws.* FROM ".Tprefix."employeesshifts es JOIN ".Tprefix."workshifts ws ON (ws.wsid=es.wsid) WHERE es.uid='".$db->escape_string($core->input['uid'])."' AND ((".$db->escape_string($core->input['fromDate'])." BETWEEN es.fromDate AND es.toDate) AND (".$db->escape_string($core->input['toDate'])." BETWEEN es.fromDate AND es.toDate) OR es.fromDate IS NULL)"));
        }
        else {
            $workshift = $db->fetch_assoc($db->query("SELECT ws.* FROM ".Tprefix."employeesshifts es JOIN ".Tprefix."workshifts ws ON (ws.wsid=es.wsid) WHERE es.uid='".$db->escape_string($core->input['uid'])."' AND ((".TIME_NOW." BETWEEN es.fromDate AND es.toDate) OR es.fromDate IS NULL)"));
            $fromdate_count_workingdays = $todate_count_workingdays = TIME_NOW;
        }

        if($leave_type['isWholeDay'] == 0) {
            $dutytime = (($workshift['offDutyHour'] * 60 * 60) + ($workshift['offDutyMinutes'] * 60)) - (($workshift['onDutyHour'] * 60 * 60) + ($workshift['onDutyMinutes'] * 60));

            if($leave_type['isPM'] == 1) {
                $leavefrom = (($workshift['offDutyHour'] * 60 * 60) + ($workshift['offDutyMinutes'] * 60)) - ($dutytime / 2) + ($leave_policy['halfDayMargin'] * 60);
                $leaveto = (($workshift['offDutyHour'] * 60 * 60) + ($workshift['offDutyMinutes'] * 60));
            }
            else {
                $leavefrom = (($workshift['onDutyHour'] * 60 * 60) + ($workshift['onDutyMinutes'] * 60));
                $leaveto = (($workshift['onDutyHour'] * 60 * 60) + ($workshift['onDutyMinutes'] * 60)) + ($dutytime / 2) - ($leave_policy['halfDayMargin'] * 60);
            }

            $leave_actual_times['fromHour'] = floor($leavefrom / 60 / 60);
            $leave_actual_times['fromMinutes'] = fmod($leavefrom / 60, 60);
            if($leave_actual_times['fromMinutes'] == 0) {
                $leave_actual_times['fromMinutes'] = '00';
            }
            $leave_actual_times['toHour'] = floor($leaveto / 60 / 60);
            $leave_actual_times['toMinutes'] = fmod($leaveto / 60, 60);
            if($leave_actual_times['toMinutes'] == 0) {
                $leave_actual_times['toMinutes'] = '00';
            }

            $leave_actual_times['workingDays'] = count_workingdays($core->input['uid'], $fromdate_count_workingdays, $todate_count_workingdays, false);
        }
        else {
            $leave_actual_times = array(
                    'fromHour' => $workshift['onDutyHour'],
                    'toHour' => $workshift['offDutyHour'],
                    'fromMinutes' => $workshift['onDutyMinutes'],
                    'toMinutes' => $workshift['offDutyMinutes'],
                    'workingDays' => count_workingdays($core->input['uid'], $fromdate_count_workingdays, $todate_count_workingdays, true)
            );
        }

        $hidden_fields_exceptions = array('workingDays');
        if($leave_type['isWholeDay'] == 0) {
            if(isset($leave_type['additionalFields'])) {
                $additional_fields = unserialize($leave_type['additionalFields']);

                if(is_array($additional_fields)) {
                    foreach($additional_fields as $key => $val) {
                        if(isset($val['fromHidden'])) {
                            $from_hidden = $val['fromHidden'];
                        }
                    }
                }
            }
        }

        foreach($leave_actual_times as $key => $val) {
            if(in_array($key, $hidden_fields_exceptions)) {
                continue;
            }

            $input_type = 'hidden';
            $field_name = '';
            $name_field_width = 0;

            if(is_array($from_hidden) && !empty($from_hidden)) {
                if(in_array($key, $from_hidden)) {
                    $input_type = 'number';
                    $key_lowercased = strtolower($key);
                    if(isset($lang->$key_lowercased)) {
                        $field_name = '<br />';
                        $name_field_width = 100;
                    }
                    $field_name .= '<span style="display:inline-block; width: '.$name_field_width.'px;">'.$lang->$key_lowercased.'</span>';
                }
            }

            $hidden_fields .= $field_name.' <input min="0" max="60" type="'.$input_type.'" value="'.$val.'" name="'.$key.'" id="'.$key.'" size="4"/>';
        }

        if(is_array($from_hidden) && !empty($from_hidden)) {
            $hidden_fields = '<br />'.$lang->customizeit.':'.$hidden_fields;
        }

        output($lang->sprint($lang->betweenhours, $leave_actual_times['fromHour'], $leave_actual_times['fromMinutes'], $leave_actual_times['toHour'], $leave_actual_times['toMinutes'], $leave_actual_times['workingDays']).$hidden_fields);
    }
    elseif($core->input['action'] == 'do_perform_requestleave') {
        //NO LEAVE IF BEFORE EMPLOYMENT
        if(isset($core->input['fromDate']) && !is_empty($core->input['fromDate'], $core->input['fromMinutes'], $core->input['fromHour'])) {
            $fromdate = explode('-', $core->input['fromDate']);
            if(checkdate($fromdate[1], $fromdate[0], $fromdate[2]) && (ctype_digit($core->input['fromHour']) && ctype_digit($core->input['fromMinutes']))) {
                $core->input['fromDate'] = mktime($core->input['fromHour'], $core->input['fromMinutes'], 0, $fromdate[1], $fromdate[0], $fromdate[2]);
                unset($core->input['fromHour'], $core->input['fromMinutes']);
            }
            else {
                output_xml("<status>false</status><message>{$lang->invalidfromdate}</message>");
                exit;
            }
        }
        else {
            output_xml("<status>false</status><message>{$lang->invalidfromdate}</message>");
            exit;
        }

        if(isset($core->input['toDate']) && !is_empty($core->input['toDate'], $core->input['toHour'], $core->input['toMinutes'])) {
            $todate = explode('-', $core->input['toDate']);
            if(checkdate($todate[1], $todate[0], $todate[2]) && (ctype_digit($core->input['toHour']) && ctype_digit($core->input['toMinutes']))) {
                $core->input['toDate'] = mktime($core->input['toHour'], $core->input['toMinutes'], 0, $todate[1], $todate[0], $todate[2]);
                unset($core->input['toHour'], $core->input['toMinutes']);
            }
            else {
                output_xml("<status>false</status><message>{$lang->invalidtodate}</message>");
                exit;
            }
        }
        else {
            output_xml("<status>false</status><message>{$lang->invalidtodate}</message>");
            exit;
        }

        if($core->input['toDate'] < $core->input['fromDate']) {
            output_xml("<status>false</status><message>{$lang->invalidtodate}</message>");
            exit;
        }

        if(!isset($core->input['uid']) || empty($core->input['uid']) || $core->input['uid'] == $core->user['uid']) {
            $core->input['uid'] = $core->user['uid'];
            $leave_user = $core->user;
            $leave_user_obj = $core->user_obj;
            $is_onbehalf = false;
        }
        else {
            //$leave_user = $db->fetch_assoc($db->query("SELECT uid, firstName, lastName, reportsTo FROM ".Tprefix."users WHERE uid='".$db->escape_string($core->input['uid'])."'"));
            $leave_user_obj = new Users($core->input['uid']);
            $leave_user = $leave_user_obj->get();
            $is_onbehalf = true;
        }

        $leavetype_details = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."leavetypes WHERE ltid='".$db->escape_string($core->input['type'])."'"));
        if($leavetype_details['isSick'] == 1 && $core->input['uid'] == $core->user['uid']) {
            output_xml("<status>false</status><message>{$lang->cannotrequestthistype}</message>");
            exit;
        }

        if(!empty($lang->{$leavetype_details['name']})) {
            $leavetype_details['title'] = $lang->{$leavetype_details['name']};
        }
        $leave['type_output'] = $leavetype_details['title'];

        $leavetype_coexist = unserialize($leavetype_details['coexistWith']);

        if(is_array($leavetype_coexist)) {
            $coexistwhere = ' AND type NOT IN ('.implode(',', $leavetype_coexist).')';
        }

        if(value_exists('leaves', 'uid', $leave_user['uid'], "(fromDate BETWEEN {$core->input[fromDate]} AND {$core->input[toDate]} OR toDate BETWEEN {$core->input[fromDate]} AND {$core->input[toDate]}){$coexistwhere}")) {
            output_xml("<status>false</status><message>{$lang->requestintersectsleave}</message>");
            exit;
        }

        if(!empty($leavetype_details['additionalFields'])) {

            $leave['details_crumb'] = parse_additionaldata($core->input, $leavetype_details['additionalFields']);

            if(is_array($leave['details_crumb']) && !empty($leave['details_crumb'])) {
                $leave['details_crumb'] = implode(' ', $leave['details_crumb']);
            }
            else {
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                exit;
            }
        }

        if(!empty($core->input['telephone']['number'])) {
            $core->input['phoneWhileAbsent'] = implode('-', $core->input['telephone']);
        }
        unset($core->input['telephone']);

        /* if(empty($core->input['contactPerson'])) {
          output_xml("<status>false</status><message>{$lang->selectcontactperson}</message>");
          exit;
          } */

        $core->input['requestTime'] = TIME_NOW;
        $core->input['requestKey'] = substr(md5(uniqid(microtime())), 1, 10);
        unset($core->input['action'], $core->input['module']);

        $core->input['affToInform'] = serialize($core->input['affToInform']);
        $expenses_data = $core->input['leaveexpenses'];
        unset($core->input['leaveexpenses']);
        /* Validate required Fields - START */
        $leavetype = new LeaveTypes($core->input['type']);
        if($leavetype->has_expenses() && $core->usergroup['canUseTravelManager'] == 0) {
            $expensesfield_type = $leavetype->get_expenses();
            foreach($expensesfield_type as $alteid => $expensesfield) {
                if(($expensesfield['isRequired'] == 1 && (empty($expenses_data[$alteid]['expectedAmt']) && $expenses_data[$alteid]['expectedAmt'] != '0')) || (($expensesfield['requireComments'] == 1 && empty($expenses_data[$alteid]['description'])))) {
                    output_xml("<status>false</status><message>{$lang->fillallrequiredfields} (".$expensesfield_type['titleOverwrite'].")</message>");
                    exit;
                }
            }
        }

        if(isset($leavetype_details['reasonIsRequired']) && $leavetype_details['reasonIsRequired'] == 1) {
            if(empty($core->input['reason']) || strlen($core->input['reason']) <= 20) {
                header('Content-type: text/xml+javascript');
                output_xml('<status>false</status><message>'.$lang->minimumcharacter.'<![CDATA[<script>$("#reason").addClass("requiredfield").focus();</script>]]></message>');
                exit;
            }
        }

        /* Validate required Fields - END */
        $query = $db->insert_query('leaves', $core->input);
        if($query) {
            $lid = $db->last_id();
            $log->record($lid);

            /* Create leave expenses - START */
            $leave_obj = new Leaves(array('lid' => $lid), false);
            if($core->usergroup['canUseTravelManager'] == 0) {
                $leave_obj->create_expenses($expenses_data);
            }
//
//            if($leavetype_details['isBusiness'] == 1) {
//                $url = 'index.php?module=travelmanager/plantrip&lid=';
//                header('Content-type: text/xhml+javascript');
//                output_xml('<status>true</status><message>'.$lang->redirecttotmplantrip.'<![CDATA[<script>goToURL(\''.$url.$db->escape_string($lid).'\');</script>]]></message>');
//                exit;
//            }

            $lang->load('attendance_messages');

            $approve_immediately = false;
            if($is_onbehalf == true) {
                if($core->user['uid'] == $leave_user['reportsTo'] || $core->usergroup['attenance_canApproveAllLeaves'] == 1 || empty($leave_user['reportsTo'])) {
                    $approve_immediately = true; //To be fully implemented at second stage
                }
                elseif($core->user['uid'] != $leave_user['reportsTo'] && !empty($leavetype_details['toApprove'])) {
                    $approve_immediately = false;
                }
            }
            else {
                if(empty($leave_user['reportsTo'])) {
                    $approve_immediately = true;
                }
            }

            if(!isset($leavetype_details['toApprove']) || empty($leavetype_details['toApprove'])) {
                $approve_immediately = true;
            }
            /* if($leavetype_details['isWholeDay'] == 1) {
              $employeeshift = $db->fetch_assoc($db->query("SELECT ws.* FROM ".Tprefix."employeesshifts es JOIN ".Tprefix."workshifts ws ON (ws.wsid=es.wsid) WHERE es.uid='".$db->escape_string($core->input['uid'])."'"));
              $employeeshift['weekDays'] = unserialize($employeeshift['weekDays']);

              $lang->leavenotificationmessage_days = $lang->sprint($lang->leavenotificationmessage_days, count_workingdays($employeeshift['weekDays'], $core->input['fromDate'], $core->input['toDate']));
              }
              else
              {
              $lang->leavenotificationmessage_days = '.';
              } */
            $lang->leavenotificationmessage_days = $lang->sprint($lang->leavenotificationmessage_days, count_workingdays($leave_user['uid'], $core->input['fromDate'], $core->input['toDate'], $leavetype_details['isWholeDay']));

            //update_leavestats_periods($core->input, $leavetype_details['isWholeDay']);

            $approvers = $leave_obj->generate_approvalchain();
            $toapprove = $toapprove_select = unserialize($leavetype_details['toApprove']); //explode(',', $leavetype_details['toApprove']);
//            if(is_array($toapprove)) {
//                $aff_obj = new Affiliates($leave_user_obj->get_mainaffiliate()->get()['affid'], false);
//                foreach($toapprove as $key => $val) {
//                    switch($val) {
//                        case 'reportsTo':
//                            list($to) = get_specificdata('users', 'email', '0', 'email', '', 0, "uid='{$leave_user[reportsTo]}'");
//                            $approvers['reportsTo'] = $leave_user['reportsTo'];
//                            unset($toapprove_select[$key]);
//                            break;
//                        case 'generalManager':
//                            $approvers['generalManager'] = $aff_obj->get_generalmanager()->get()['uid'];
//                            break;
//                        case 'hrManager':
//                            $approvers['hrManager'] = $aff_obj->get_hrmanager()->get()['uid'];
//                            break;
//                        case 'supervisor':
//                            $approvers['supervisor'] = $aff_obj->get_supervisor()->get()['uid'];
//                            break;
//                        case 'segmentCoordinator':
//                            /* If leave has segment selected */
//                            if(is_object($leave_obj->get_segment())) {
//                                $leave_segmobjs = $leave_obj->get_segment();
//                                $leave_segment_coordinatorobjs = $leave_segmobjs->get_coordinators();
//                                if(is_array($leave_segment_coordinatorobjs)) {
//                                    $leave_segment_coordinatorobj = $leave_segment_coordinatorobjs[array_rand($leave_segment_coordinatorobjs, 1)];
//                                    $approvers['segmentCoordinator'] = $leave_segment_coordinatorobj->get_coordinator()->get()['uid'];
//                                }
//                            }
//                            break;
//                        case 'financialManager':
//                            $approvers['financialManager'] = $aff_obj->get_financialemanager()->get()['uid'];
//                            break;
//                        default:
//                            if(is_int($val)) {
//                                $approvers[$val] = $val;
//                            }
//                            unset($toapprove_select[$key]);
//                            break;
//                    }
//                }
//
//                /* Make list of approvers unique */
//                $approvers = array_unique($approvers);
//            }
//			if(is_array($toapprove_select) && !empty($toapprove_select)) {
//				$secondapprovers = $db->fetch_assoc($db->query("SELECT ".implode(', ', $toapprove_select)."
//									  FROM ".Tprefix."affiliates
//									  WHERE affid=(SELECT affid FROM affiliatedemployees WHERE uid='".$db->escape_string($leave_user['uid'])."' AND isMain='1')"));
//			}
//			if(is_array($secondapprovers)) {
//				$approvers = ($approvers + $secondapprovers);   /* merge the 2 arrays in one array */
//				unset($secondapprovers);
//			}

            if(is_array($approvers)) {
                if($leavetype->get()['isBusiness'] == 1) {
                    $approve_immediately = false;
                }
                foreach($approvers as $key => $val) {
                    if($key != 'reportsTo' && $val == $approvers['reportsTo']) {
                        continue;
                    }
                    if($key == 'reportsTo' && !empty($val)) {
                        list($to) = get_specificdata('users', 'email', '0', 'email', '', 0, "uid='{$leave_user[reportsTo]}'");
                    }
                    $approve_status = $timeapproved = 0;
                    if(($core->usergroup['attenance_canApproveAllLeaves'] == 1 && $approve_immediately == true) || ($val == $core->user['uid'] && $approve_immediately == true) || ($approve_immediately == true && $key == 'reportsTo' && $core->user['uid'] == $leave_user['reportsTo']) || ($key == 'reportsTo' && empty($leave_user['reportsTo']))) {
                        if($val == $core->user['uid'] || $core->usergroup['attenance_canApproveAllLeaves'] == 1) {
                            $approve_immediately = true;
                        }
                        $approve_status = 1;
                        $timeapproved = TIME_NOW;
                    }

                    $sequence = 1;
                    if(is_array($toapprove)) {
                        $sequence = array_search($key, $toapprove);
                    }
                    $db->insert_query('leavesapproval', array('lid' => $lid, 'uid' => $val, 'isApproved' => $approve_status, 'timeApproved' => $timeapproved, 'sequence' => $sequence));
                }
            }

            if(count($approvers) > 1 && $approve_immediately == true) {
                foreach($approvers as $key => $val) {
                    if($key != 'reportsTo' && $val == $approvers['reportsTo']) {
                        $already_approved = false;
                        continue;
                    }
                    elseif($key != 'reportsTo' && $val != $approvers['reportsTo']) {
                        list($to) = get_specificdata('users', 'email', '0', 'email', '', 0, "uid='".$approvers[$toapprove[array_search($key, $toapprove)]]."'"); //Second in sequence after reportsTo
                        $approve_immediately = false;
                        $already_approved = true;
                        break;
                    }
                }
            }

            /**
             * If leave should be planning through TM, trasfer user to there
             */
            if($leavetype_details['isBusiness'] == 1 && $core->usergroup['canUseTravelManager'] == 1) {
                $url = 'index.php?module=travelmanager/plantrip&lid=';
                header('Content-type: text/xhml+javascript');
                output_xml('<status>true</status><message>'.$lang->redirecttotmplantrip.'<![CDATA[<script>goToURL(\''.$url.$db->escape_string($lid).'\');</script>]]></message>');
                exit;
            }

            if(date($core->settings['dateformat'], $core->input['fromDate']) != date($core->settings['dateformat'], $core->input['toDate'])) {
                $message_todate_format = $core->settings['dateformat'].' '.$core->settings['timeformat'];
                $subject_todate_format = ' - '.$core->settings['dateformat'];
            }
            else {
                $message_todate_format = $core->settings['timeformat'];
                $subject_todate_format = '';
            }

            /* Generate Leaves Balances - START */
            if($leavetype_details['noBalance'] == 0) {
                $stat = new LeavesStats();
                $core->input['skipWorkingDays'] = !$approve_immediately; /* Negate the boolean */
                $stat->generate_periodbased($core->input);
            }
            /* Generate Leaves Balances - END */

            if($approve_immediately == false) {
                //$approve_link = DOMAIN.'/index.php?module=attendance/listleaves&action=perform_approveleave&toapprove='.base64_encode($core->input['requestKey']).'&referrer=email';
                $approve_link = DOMAIN.'/index.php?module=attendance/listleaves&action=takeactionpage&requestKey='.base64_encode($core->input['requestKey']).'&id='.base64_encode($lid);

                $leavestats = $db->fetch_assoc($db->query("SELECT *
												FROM ".Tprefix."leavesstats
												WHERE uid='".$db->escape_string($leave_user['uid'])."' AND (ltid='".$db->escape_string($core->input['type'])."' OR ltid = (SELECT countWith FROM ".Tprefix."leavetypes WHERE ltid='".$db->escape_string($core->input['type'])."' AND countWith!=0)) AND (".$db->escape_string($core->input['fromDate'])." BETWEEN periodStart AND periodEnd)"));

                $leave['workingdays'] = count_workingdays($leave_user['uid'], $core->input['fromDate'], $core->input['toDate'], $leavetype_details['isWholeDay']);
                $lang->leavenotificationmessage_days = $lang->sprint($lang->leavenotificationmessage_days, $leave['workingdays']);

                $lang->requestleavesubject = $lang->sprint($lang->requestleavesubject, $leave_user['firstName'].' '.$leave_user['lastName'], strtolower($leave['type_output']), $core->input['requestKey']);

                /* Parse expense information for message - START */
                if($leave_obj->has_expenses() && $core->usergroup['canUseTravelManager'] == 0) {
                    $expenses_data = $leave_obj->get_expensesdetails();
                    $expenses_message = '';
                    foreach($expenses_data as $expense) {
                        if(!empty($lang->{$expense['name']})) {
                            $expense['title'] = $lang->{$expense['name']};
                        }

                        if(isset($expense['description']) && !empty($expense['description'])) {
                            $expense['description'] = ' ('.$expense['description'].')';
                        }

                        $exptype_obj = LeaveExpenseTypes::get_exptype_byattr('title', $expense['title'], false);
                        if(is_object($exptype_obj)) {
                            $agency_link = $exptype_obj->parse_agencylink($leave_obj);
                        }

                        $expenses_message .= $expense['title'].': '.$expense['expectedAmt'].$expense['currency'].$expense['description'].' '.$agency_link.'<br />';
                        unset($agency_link);
                    }

                    $total = $leave_obj->get_expensestotal();
                    $expenses_message_ouput = '<br />'.$lang->associatedexpenses.'<br />'.$expenses_message.'<br />Total: '.$total.'USD<br />';
                }
                /* Parse expense information for message - END */

                $core->input['reason'] .= $expenses_message_ouput;
                if($already_approved == true) {
                    $lang->requestleavemessage = $lang->sprint($lang->requestleavemessagesupervisor, $leave_user['firstName'].' '.$leave_user['lastName'], strtolower($leave['type_output']), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $core->input['fromDate']), date($todate_format, $core->input['toDate']), $core->input['reason'], $core->user['firstName'].' '.$core->user['lastName'], $approve_link);
                }
                else {
                    //$lang->requestleavemessage = $lang->sprint($lang->requestleavemessage, $leave_user['firstName'].' '.$leave_user['lastName'], strtolower($leave['type_output']), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $core->input['fromDate']), date($message_todate_format, $core->input['toDate']), $core->input['reason'], $approve_link);

                    if($leavetype_details['isBusiness'] == 0) {
                        /* 						$lang->requestleavemessage_stats = $lang->sprint($lang->requestleavemessage_stats,
                          $leavestats['canTake'],
                          $leavestats['entitledFor'],
                          $leavestats['additionalDays'],
                          $leavestats['daysTaken'],
                          ($leavestats['canTake'] - $leavestats['daysTaken']),
                          ($leavestats['canTake'] - $leavestats['daysTaken']) - $leave['workingdays']); */

                        $lang->requestleavemessage_stats = $lang->sprint($lang->requestleavemessage_stats, ($leavestats ['canTake'] - $leavestats ['daysTaken']) + $leavestats ['additionalDays'], (($leavestats ['canTake'] - $leavestats['daysTaken'] ) + $leavestats['additionalDays']) - $leave['workingdays']);
                    }
                    else {

                        $lang->requestleavemessage_stats = '';
                    }

                    if(!empty($leave['details_crumb'])) {
                        $leave['details_crumb'] = ' - '.$leave['details_crumb'];
                    }
                    $modifyleave_link = DOMAIN.'/index.php?module=attendance/editleave&lid='.$lid;
                    $lang->requestleavemessage = $lang->sprint($lang->requestleavemessage, $leave_user['firstName'].' '.$leave_user['lastName'], strtolower($leave['type_output']).' ('.$leavetype_details['description'].')'.$leave['details_crumb'], date($core->settings['dateformat'].' '.$core->settings['timeformat'], $core->input['fromDate']), date($message_todate_format, $core->input['toDate']), $lang->leavenotificationmessage_days, $core->input['reason'], $lang->requestleavemessage_stats, $approve_link, $modifyleave_link);

                    /* Parse Calendar - Start */
                    $lang->requestleavemessage .= get_calendar(array('fromDate' => $core->input['fromDate'], 'affid' => $db->fetch_field($db->query("SELECT affid FROM ".Tprefix."affiliatedemployees WHERE uid='{$leave_user[uid]}'"), 'affid')));
                    /* Parse Calendar - End */
                }
            }
            else {
                if(TIME_NOW > $core->input['toDate']) {
                    if($leavetype_details['isBusiness'] == 0) {
                        $tooktaking = $lang->leavenotificationsubject_took;
                    }
                    else {
                        $tooktaking = $lang->leavenotificationsubject_wasat;
                    }
                }
                else {
                    if($leavetype_details['isBusiness'] == 0) {
                        $tooktaking = $lang->leavenotificationsubject_taking;
                    }
                    else {
                        $tooktaking = $lang->leavenotificationsubject_willbeat;
                    }

                    if(!empty($core->input['phoneWhileAbsent'])) {
                        $lang->leavenotificationmessage_owncontact = $lang->leavenotificationmessage_owncontact_limitedemail;

                        if($core->input['limitedEmail'] == 0) {
                            $lang->leavenotificationmessage_owncontact = $lang->leavenotificationmessage_owncontact_email;
                        }
                        $contact_details = $lang->sprint($lang->leavenotificationmessage_owncontact, $leave_user['firstName'].' '.$leave_user['lastName'], '+'.$core->input['phoneWhileAbsent'].'<br />'.$core->input['addressWhileAbsent']);
                    }

                    if(!empty($core->input['contactPerson'])) {
                        $cotactperson_details = $db->fetch_assoc($db->query("SELECT displayName AS contactPersonName, email FROM ".Tprefix."users WHERE uid=".$db->escape_string($core->input['contactPerson']).""));
                        $contactperson_details = $lang->sprint($lang->leavenotificationmessage_contactperson, $cotactperson_details['contactPersonName'], $cotactperson_details['email']);
                    }
                }

                $leave['details_crumb'] = parse_additionaldata($core->input, $leavetype_details['additionalFields'], 1);
                $leave['details_crumb'] = $core->sanitize_inputs($leave['details_crumb'], array('method' => 'striponly', 'removetags' => true));
                if(!empty($leave['details_crumb'])) {
                    /* 					$core->input['details_crumb'] = implode(' ', parse_additionaldata($core->input, $leavetype_details['additionalFields']));
                      //$lang->leavenotificationmessage_typedetails .= ' ('.$core->input['details_crumb'].')'; */
                    $lang->leavenotificationmessage_typedetails = $leave['details_crumb'];
                }
                else {
                    $lang->leavenotificationmessage_typedetails = strtolower($leave['type_output']);
                }

                $lang->leavenotificationsubject = $lang->sprint($lang->leavenotificationsubject, $leave_user['firstName'].' '.$leave_user['lastName'], $lang->leavenotificationmessage_typedetails, $tooktaking, date($core->settings['dateformat'], $core->input['fromDate']), date($subject_todate_format, $core->input['toDate']));
                $lang->leavenotificationmessage = $lang->sprint($lang->leavenotificationmessage, $leave_user['firstName'].' '.$leave_user['lastName'], $lang->leavenotificationmessage_typedetails, date($core->settings['dateformat'].' '.$core->settings['timeformat'], $core->input['fromDate']), date($message_todate_format, $core->input['toDate']), $lang->leavenotificationmessage_days, $tooktaking, $contact_details, $contactperson_details);
            }

            if($approve_immediately == false) {
                $email_data = array(
                        'from_email' => 'approve_leaverequest@ocos.orkila.com',
                        'from' => 'Orkila Attendance System',
                        'to' => $to,
                        'subject' => $lang->requestleavesubject,
                        'message' => $lang->requestleavemessage
                );
            }
            else {
                $to_inform = unserialize($core->input['affToInform']);
                if(is_array($to_inform)) {
                    $mailingLists_attr = 'altMailingList';
                    if($leavetype_details['isBusiness'] == 1) {
                        $mailingLists_attr = 'mailingList';
                    }

                    $mailingLists = get_specificdata('affiliates', array('affid', $mailingLists_attr), 'affid', $mailingLists_attr, '', 0, 'affid IN ('.implode(',', $to_inform).')');
                }

                if(!is_array($mailingLists) || empty($mailingLists)) {
                    $mailingLists = $to;
                }
                $main_affiliate = $leave_user_obj->get_mainaffiliate();
                if(is_object($main_affiliate) && !empty($main_affiliate->cpAccount) && $leave_obj->createAutoResp == 1) {
                    $leave_obj->create_autoresponder();
                }
                $email_data = array(
                        'from_email' => 'attendance@ocos.orkila.com',
                        'from' => 'Orkila Attendance System',
                        'to' => $mailingLists,
                        'subject' => $lang->leavenotificationsubject,
                        'message' => $lang->leavenotificationmessage
                );
            }

            $mail = new Mailer($email_data, 'php');
            if($mail->get_status() === true) {
                $log->record('notifysupervisors', $email_data['to']);
                output_xml("<status>true</status><message>{$lang->leavesuccessfullyrequested}</message>");
            }
            else {
                output_xml("<status>false</status><message>{$lang->errorsendingemail}</message>");
            }
        }
    }
    elseif($core->input['action'] == 'parseexpenses') {
        if($core->usergroup['canUseTravelManager'] == 1) {
            exit;
        }
        $leavetype = new LeaveTypes($core->input['ltid']);
        if($leavetype->has_expenses()) {
            $expenses_total = 0;
            $expenses_leavetype = $leavetype->get_expenses();
            foreach($expenses_leavetype as $val) {
                $expences_fields .= $leavetype->parse_expensesfield($val);
            }
            eval("\$expsection = \"".$template->get('attendance_requestleave_expsection')."\";");
            output($expsection);
        }
    }
}
function get_calendar($arguments) {
    global $core, $db;

    $current_date = getdate($arguments['fromDate']);
    $month['firstday'] = mktime(0, 0, 0, $current_date['mon'], 1, $current_date ['year']);
    $month['numdays'] = date('t', $month['firstday']);
    $month['today_weekday'] = date('N', $month['firstday']);
    $affiliate_users = get_specificdata('affiliatedemployees', 'uid', 'uid', 'uid', '', 0, "affid='{$arguments[affid]}' AND isMain='1'");
    if(empty($affiliate_users)) {
        return false;
    }
    /* GET RELATED LEAVES - START */
    $approved_lids = $unapproved_lids = array();
    $query = $db->query("SELECT l.lid, la.isApproved FROM ".Tprefix."leaves l JOIN ".Tprefix."leavesapproval la ON (l.lid=la.lid) WHERE ((".$month['firstday']." BETWEEN l.fromDate AND l.toDate) OR (l.fromDate > ".$month['firstday'].")) AND l.uid IN (".implode(', ', $affiliate_users).")");

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


    if(!empty($approved_lids)) {
        $query = $db->query("SELECT l.*, l.uid AS requester, Concat(u.firstName, ' ', u.lastName) AS employeename
					FROM ".Tprefix."leaves l JOIN ".Tprefix."users u ON (l.uid=u.uid)
					WHERE l.lid IN (".implode(',', $approved_lids).") ORDER BY l.fromDate ASC");

        if($db->num_rows($query) > 0) {
            while($more_leaves = $db->fetch_assoc($query)) {
                $num_days_off = (($more_leaves['toDate'] - $more_leaves['fromDate']) / 24 / 60 / 60);

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

    $current_leave_numdays = (date('z', $core->input['toDate']) - date('z', $core->input['fromDate'])) + 1;
    for($i = 0; $i < $current_leave_numdays; $i++) {
        $current_leave_check = $core->input['fromDate'] + (60 * 60 * 24 * $i);

        if($month['firstday'] > $current_leave_check) { //|| $more_leaves['toDate'] < $current_check) {
            continue;
        }

        /* 			if($current_leave_check > ($month['firstday']*60*60*24*$month['numdays'])) {
          break;
          } */
        $current_leave_check_date = getdate($current_leave_check);

        $days_tohighlight[$current_leave_check_date['mon']][$current_leave_check_date['mday']] = $current_leave_check_date['mday'];
    }

    /* GET RELATED LEAVES - END */
    $message .= '<table width="100%" cellspacing="0" cellpadding="5" style="border-left: 1px solid #CCC;" border="0">';
    $message .= '<tr><td style="background: #91b64f; font-weight: bold; text-align: center; width: 120px; padding: 5px; border-bottom: 1px solid #999; border-top: 1px solid #999; border-right: 1px solid #999;">Week</td>';
    $weekdays = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    $message .= '<td style="background: #91b64f; font-weight: bold; text-align: center; width: 120px; padding: 5px; border-bottom: 1px solid #999; border-top: 1px solid #999; border-right: 1px solid #999;">'.implode('</td><td style="background: #91b64f; font-weight: bold; text-align: center; width: 120px; padding: 5px; border-bottom: 1px solid #999; border-top: 1px solid #999; border-right: 1px solid #999;">', $weekdays).'</td></tr>';
    $message .= '<tr><td style="width: 3%; text-align:center; font-weight: bold; font-size: 11px; position: relative; padding: 5px; min-height: 80px; border-bottom: 1px solid #999; border-right: 1px solid #999; height: 80px;">'.date('W', $arguments['fromDate']).'</td>';

    $week_num_days = 1;

    for($prev_days = 1; $prev_days < $month['today_weekday']; $prev_days++) {
        $message .= '<td style="background: #eee; width: 120px; padding: 5px; min-height: 80px; border-bottom: 1px solid #999; border-right: 1px solid #999; height: 80px;">&nbsp;</td>';
        $week_num_days++;
    }

    $message .= draw_available(1, $month['numdays'], $current_date, $week_num_days, $leaves, $days_tohighlight);

    $message .= '</table>';
    //$message .= '</body></html>';

    return $message;
}

function draw_available($start_from, $num_days, $start_date_info, $week_num_days, $leaves = '', $days_tohighlight = array(), $primary = true) {
    $current_date['mday'] = $start_from;
    $month['numdays'] = $num_days;


    for($day = $start_from; $day <= $num_days; $day++) {
        if($current_date['mday'] > $day) {
            $message .= '<td style="background: #eee; width: 120px; padding: 5px; min-height: 80px; border-bottom: 1px solid #999; border-right: 1px solid #999; height: 80px;">&nbsp;</td>';
        }
        else {
            $calendar_cell_highlight = '';
            if(is_array($days_tohighlight[$start_date_info['mon']])) {
                if(in_array($day, $days_tohighlight[$start_date_info['mon']])) {
                    $calendar_cell_highlight = 'background: #D5F2BF; ';
                }
            }

            $message .= '<td style="'.$calendar_cell_highlight.'width: 120px; vertical-align: top; 	font-size: 11px; position: relative; padding: 5px; min-height: 80px; border-bottom: 1px solid #999; border-right: 1px solid #999; height: 80px;">';
            $message .= '<div style="background: #CCC; padding: 5px; color: #333333; font-weight: bold; float: right; margin: -5px -5px 0 0; width: 20px; text-align: center;">'.$day.'</div>';

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
            $message .= '</tr><tr><td style="width: 3%; text-align:center; font-weight: bold; font-size: 11px; position: relative; padding: 5px; min-height: 80px; border-bottom: 1px solid #999; border-right: 1px solid #999; height: 80px;">'.date('W', mktime(0, 0, 0, $start_date_info ['mon'], 1, $start_date_info ['year']) + (60 * 60 * 24 * ($day + 1))).'</font></td>';
            $week_num_days = 0;
        }
        else {
            if($day == $month['numdays']) {
                /* for($next_month = 1; $next_month<=(7-$week_num_days);$next_month++) {
                  $message .= '<td class="calendar_noday">&nbsp;</td>';
                  } */
                $week_num_days++;
                //$message .= draw_available(1, abs(1-$current_date['mday']), getdate(strtotime("+1 month", mktime(0, 0, 0, $start_date_info['mon'], 1, $start_date_info['year']))), $week_num_days);
                if($primary == true) {
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