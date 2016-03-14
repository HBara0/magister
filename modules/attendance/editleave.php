<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Edit Leave
 * $module: attendance
 * $id: editleave.php
 * Last Update: @zaher.reda 	August 28, 2012 | 05:12 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    //$lid = $db->escape_string(base64_decode($core->input['lid']));
    $lid = $db->escape_string($core->input['lid']);
    $action = 'editleave';
    $lidfield = '<input type="hidden" value="'.$lid.'" name="lid" id="lid">';
    $leave_obj = new Leaves($core->input['lid']);
    $leavetype_obj = $leave_obj->get_type(false);
    $leave = $db->fetch_assoc($db->query("SELECT l.*, displayName AS contactPersonName FROM ".Tprefix."leaves l LEFT JOIN ".Tprefix."users u ON (u.uid=l.contactPerson) WHERE lid='{$lid}'"));
    $tmwarning_show = 'style="display:none"';
    $tmplan = TravelManagerPlan::get_plan(array('lid' => $lid), array('returnarray' => false));
    if(is_object($tmplan)) {
        $tmwarning_show = '';
    }
    if($leave['uid'] != $core->user['uid']) {
        if($core->usergroup['attendance_canViewAffAllLeaves'] == 0) {
            if(!value_exists('users', 'reportsTo', $core->user['uid'], "uid='{$leave[uid]}'") && $core->usergroup['attenance_canApproveAllLeaves'] == 0) {
                //if($core->usergroup['attendance_canViewAllAttendnace'] == 0) { //TO REVISE
                error($message, 'index.php?module=attendance/listleaves');
                //}
            }
        }
    }
    $uidfield = '<input type="hidden" value="'.$leave['uid'].'" name="uid" id="uid">';

    /*
      $leave['fromHour_output'] = date('H', $leave['fromDate']);
      $leave['fromMinutes_output'] = date('i', $leave['fromDate']);

      $leave['toHour_output'] = date('H', $leave['toDate']);
      $leave['toMinutes_output'] = date('i', $leave['toDate']); */

    $leave_actual_times['fromHour'] = date('H', $leave['fromDate']);
    $leave_actual_times['fromMinutes'] = date('i', $leave['fromDate']);
    $leave_actual_times['toHour'] = date('H', $leave['toDate']);
    $leave_actual_times['toMinutes'] = date('i', $leave['toDate']);

    $leavetype_details = $db->fetch_assoc($db->query("SELECT isBusiness, noNotification, additionalFields FROM ".Tprefix."leavetypes WHERE ltid='".$leave['type']."'"));
    if($leave_type['isWholeDay'] == 0) {
        if(isset($leavetype_details['additionalFields']) && !empty($leavetype_details['additionalFields'])) {
            $additional_fields = unserialize($leavetype_details['additionalFields']);
            $additional_fields_current = current($additional_fields);
        }
    }

    $hidden_fields_exceptions = array('workingDays');
    foreach($leave_actual_times as $key => $val) {
        if(in_array($key, $hidden_fields_exceptions)) {
            continue;
        }

        $input_type = 'hidden';
        $field_name = '';
        $name_field_width = 0;

        if(isset($additional_fields_current['fromHidden']) && !empty($additional_fields_current['fromHidden'])) {
            if(in_array($key, $additional_fields_current['fromHidden'])) {
                $input_type = 'text';
                $key_lowercased = strtolower($key);
                if(isset($lang->$key_lowercased)) {
                    $field_name = '<br />';
                    $name_field_width = 100;
                }
                $field_name .= '<span style="display:inline-block; width: '.$name_field_width.'px;">'.$lang->$key_lowercased.'</span>';
            }
        }
        $hidden_fields .= $field_name.' <input type="'.$input_type.'" value="'.$val.'" name="'.$key.'" id="'.$key.'" size="4"/>';
    }

    if(is_array($additional_fields_current['fromHidden']) && !empty($additional_fields_current['fromHidden'])) {
        $hidden_fields = '<br />'.$lang->customizeit.':'.$hidden_fields;
    }

    $type_details = parse_type($leave['type']);
    $leave['workingDays'] = count_workingdays($leave['uid'], $leave['fromDate'], $leave['toDate'], $type_details['isWholeDay']);

    $lang->betweenhours = $lang->sprint($lang->betweenhours, $leave_actual_times['fromHour'], $leave_actual_times['fromMinutes'], $leave_actual_times['toHour'], $leave_actual_times['toMinutes'], $leave['workingDays']).$hidden_fields;

    $leave['fromDate_output'] = date($core->settings['dateformat'], $leave['fromDate']);
    $leave['toDate_output'] = date($core->settings['dateformat'], $leave['toDate']);
    $leave['fromDate_formatted'] = date('d-m-Y', $leave['fromDate']); //$leave['fromDate']-((60*60*$leave['fromHour_output'])+(60*$leave['fromMinutes_output']));
    $leave['toDate_formatted'] = date('d-m-Y', $leave['toDate']); //$leave['toDate']-((60*60*$leave['toHour_output'])+(60*$leave['toMinutes_output']));

    $query = $db->query("SELECT ltid, name, title FROM ".Tprefix."leavetypes WHERE isActive=1 ORDER BY name ASC");
    while($type = $db->fetch_assoc($query)) {
        if(!empty($lang->{$type['name']})) {
            $type['title'] = $lang->{$type['name']};
        }
        $leave_types[$type['ltid']] = $type['title'];
    }

    $leavetypes_list = parse_selectlist('type', 4, $leave_types, $leave['type']);

    $additional_fields = unserialize($leavetype_details['additionalFields']);
    $additional_fields_output = '';
    $core->input['uid'] = $leave['uid'];
    if(is_array($additional_fields)) {
        foreach($additional_fields as $key => $val) {
            if(empty($val)) {
                continue;
            }
            $val['key_attribute_value'] = $leave[$key];
            $val['value_attribute_value'] = parse_additionaldata($leave, serialize(array($key => $val)));
            if(is_array($val['value_attribute_value'])) {
                $val['value_attribute_value'] = implode('', $val['value_attribute_value']);
            }
            $val['uid'] = $leave['uid'];
            $additional_fields_output .= $leavetype_obj->parse_additonalfield($key, $val);
            //$additional_fields_output .= parse_additonalfield($key, $val).'<br />';
        }
    }

    $telephone = explode('-', $leave['phoneWhileAbsent']);
    $telephone['intcode'] = &$telephone[0];
    $telephone['areacode'] = &$telephone[1];
    $telephone['number'] = &$telephone[2];

    $limitedemail_radiobutton = parse_yesno('limitedEmail', 11, $leave['limitedEmail']);

    $to_inform = parse_toinform_list($leave['uid'], unserialize($leave['affToInform']), $leavetype_details);

    $leaveobject = new Leaves(array('lid' => $core->input['lid']));
    $leavetype = new LeaveTypes($leaveobject->get_leavetype()->get()['ltid']);
    if($leavetype->has_expenses()) {
        $leaveexpenses = $leaveobject->get_expensesdetails();
        if(!is_array($leaveexpenses)) {
            $leaveexpenses = $leavetype->get_expenses();
        }

        foreach($leaveexpenses as $alteid => $leaveexpense) {
            $expences_fields .= $leavetype->parse_expensesfield($leaveexpense);
        }

        $expenses_total = $leaveobject->get_expensestotal();
        eval("\$expsection = \"".$template->get('attendance_requestleave_expsection')."\";");
    }
    $autoresp_show = 'style="display:none"';
    $main_aff = new Affiliates($core->user['mainaffiliate'], false);
    if(!is_object($main_aff) || empty($main_aff->affid) || empty($main_aff->cpAccount)) {
        $autoresp_checkshow = 'style="display:none"';
    }
    else {
        if($leave['createAutoResp'] == 1) {
            $autoresp_checked = 'checked="checked"';
            $autoresp_show = 'style="display:block"';
        }
    }
    eval("\$requestleavepage = \"".$template->get('attendance_requestleave')."\";");
    output_page($requestleavepage);
}
else {
    if($core->input['action'] == 'getaffiliates') {
        $leavetype_details = $db->fetch_assoc($db->query("SELECT isBusiness, noNotification FROM ".Tprefix."leavetypes WHERE ltid='".$db->escape_string($core->input['ltid'])."'"));

        echo parse_toinform_list($core->input['uid'], '', $leavetype_details);
    }
    elseif($core->input['action'] == 'parseexpenses') {
        $leavetype = new LeaveTypes($core->input['ltid']);
        if($leavetype->has_expenses()) {
            $expenses_total = 0;
            $leaveexpences = $leavetype->get_expenses();
            foreach($leaveexpences as $alteid => $expenses) {
                $expences_fields .= $leavetype->parse_expensesfield($expenses);
            }

            eval("\$expsection = \"".$template->get('attendance_requestleave_expsection')."\";");
            echo $expsection;
        }
    }
    elseif($core->input['action'] == 'do_perform_editleave') {
        unset($core->input['leaveid']);
        $expenses_data = $core->input['leaveexpenses'];
        unset($core->input['leaveexpenses']);

        $lid = $db->escape_string($core->input['lid']);
        if(isset($core->input['fromDate']) && !empty($core->input['fromDate']) && (ctype_digit($core->input['fromHour']) && ctype_digit($core->input['fromMinutes']))) {
            $fromdate = explode('-', $core->input['fromDate']);
            if(checkdate($fromdate[1], $fromdate[0], $fromdate[2]) && mktime($core->input['fromHour'], $core->input['fromMinutes'], 0, $fromdate[1], $fromdate[0], $fromdate[2])) {
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

        if(isset($core->input['toDate']) && !empty($core->input['toDate']) && (ctype_digit($core->input['toHour']) && ctype_digit($core->input['toMinutes']))) {
            $todate = explode('-', $core->input['toDate']);
            if(checkdate($todate[1], $todate[0], $todate[2]) && mktime($core->input['toHour'], $core->input['toMinutes'], 0, $todate[1], $todate[0], $todate[2])) {
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

        $notification_required = false;
        $current_leave_info = $db->fetch_assoc($db->query("SELECT fromDate, toDate, type, requestKey, affid, limitedEmail, affid, spid, cid, coid, ceid, kiid FROM ".Tprefix."leaves WHERE lid='{$lid}'"));
        foreach($current_leave_info as $key => $val) {
            $old_leave_info[$key] = $val;
            if(!empty($core->input[$key])) {
                if($core->input[$key] != $val) {
                    $notification_required = true;
                    //break;
                }
            }
        }

        if($core->input['uid'] != $core->user['uid']) {
            //$leave_user = $db->fetch_assoc($db->query("SELECT uid, firstName, lastName, reportsTo FROM ".Tprefix."users WHERE uid='".$db->escape_string($core->input['uid'])."'"));
            $leave_user_obj = new Users($core->input['uid']);
            $leave_user = $leave_user_obj->get();
            $is_onbehalf = true;
        }
        else {
            $leave_user_obj = $core->user_obj;
            $leave_user = $core->user;
            $is_onbehalf = false;
        }

        $leavetype_details = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."leavetypes WHERE ltid='".$db->escape_string($core->input['type'])."'"));

        if($leavetype_details['isSick'] == 1 && $core->input['uid'] == $core->user['uid']) {
            output_xml("<status>false</status><message>{$lang->cannotrequestthistype}</message>");
            exit;
        }

        if(isset($leavetype_details['reasonIsRequired']) && $leavetype_details['reasonIsRequired'] == 1) {
            if(empty($core->input['reason']) || strlen($core->input['reason']) <= 20) {
                header('Content-type: text/xml+javascript');
                output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'<![CDATA[<script>$("#reason").attr("required",true);</script>]]></message>');
                exit;
            }
        }

        $leavetype_coexist = unserialize($leavetype_details['coexistWith']);
        if(is_array($leavetype_coexist)) {
            $coexistwhere = " AND type NOT IN (".implode(',', $leavetype_coexist).")";
        }
        if(value_exists('leaves', 'uid', $leave_user['uid'], "(fromDate BETWEEN {$core->input[fromDate]} AND {$core->input[toDate]} OR toDate BETWEEN {$core->input[fromDate]} AND {$core->input[toDate]} {$coexistwhere}) AND lid!='{$lid}'")) {
            output_xml("<status>false</status><message>{$lang->requestintersectsleave}</message>");
            exit;
        }

        $leavetype_details = parse_type($core->input['type']);
        if(!empty($lang->{$leavetype_details['name']})) {
            $leavetype_details['title'] = $lang->{$leavetype_details['name']};
        }
        $leave['type_output'] = $leavetype_details['title'];

        if(!empty($leavetype_details['additionalFields'])) {
            $leave['details_crumb'] = parse_additionaldata($core->input, $leavetype_details['additionalFields'], 0, 'source');
            if(is_array($leave['details_crumb']) && !empty($leave['details_crumb'])) {
                $leave['details_crumb'] = implode(' ', $leave['details_crumb']);
            }
            else {
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                exit;
            }
        }

        if(!empty($core->input['telephone'])) {
            $core->input['phoneWhileAbsent'] = implode('-', $core->input['telephone']);
        }
        unset($core->input['telephone']);

        //$core->input['requestTime'] = TIME_NOW;
        //$core->input['requestKey'] =  substr(md5(uniqid(microtime())), 1,10);

        unset($core->input['action'], $core->input['module']);

        $core->input['affToInform'] = serialize($core->input['affToInform']);

        /* Validate required Fields --START */
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
//        if($leavetype->has_expenses()) {
//            $expensesfield_type = $leavetype->get_expenses();
//            foreach($expensesfield_type as $alteid => $expensesfield) {
//                if(($expensesfield['isRequired'] == 1 && (empty($expenses_data[$alteid]['expectedAmt']) && $expenses_data[$alteid]['expectedAmt'] != 0)) || ($expensesfield['requireComments'] == 1 && empty($expenses_data[$alteid]['description']))) {
//                    output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
//                    exit;
//                }
//            }
//        }
        /* Validate required Fields --END */
        //check if leave has a TM plan
        $leave_obj = new Leaves($lid, false);
        if(is_object($leave_obj)) {
            $tmplan = TravelManagerPlan::get_plan(array('lid' => $lid), array('returnarray' => false));
            if(is_object($tmplan)) {
                if($core->input['deletetm'] == 1) {
                    unset($core->input['deletetm']);
                    $tmplan->delete();
                    $deleted_tm = 1;
                    $url = 'index.php?module=travelmanager/plantrip&lid=';
                    header('Content-type: text/xhml+javascript');
                    output_xml('<status>true</status><message>'.$lang->redirecttotmplantrip.'<![CDATA[<script>goToURL(\''.$url.$db->escape_string($lid).'\');</script>]]></message>');
                    exit;
                }
                else {
                    $fields = array('destinationCity', 'fromDate', 'sourceCity', 'toDate', 'type');
                    foreach($fields as $field) {
                        if($leave_obj->{$field} != $core->input[$field]) {
                            $changed_fields[] = $field;
                        }
                    }
                    if(is_array($changed_fields)) {
//                        $imploded_fields = implode(',', $changed_fields);
                        foreach($changed_fields as $field) {
                            $imploded_fields.=$lang->$field.', ';
                        }
                        $actualform = serialize($core->input);
                        $actualform = htmlentities($actualform);
                        eval("\$deletetm = \"".$template->get('popup_atteendance_deletetmplan')."\";");
                        output_xml('<status></status><message><![CDATA['.$deletetm.']]></message>');
                        exit;
                    }
                }
            }
            if($leave_obj->createAutoResp == 1 && !isset($core->input['createAutoResp'])) {
                $leave_obj->delete_autoresponder();
            }
            $core->input['workingDays'] = $leave_obj->count_workingdays();
        }
        //check if leave has a TM plan end
        $query = $db->update_query('leaves', $core->input, "lid='{$lid}'");
        /* Update leave expenses - START */
        $leave_obj = new Leaves(array('lid' => $lid), false);
        if($core->usergroup['canUseTravelManager'] == 0 && is_array($expenses_data) && !empty($expenses_data)) {
            $leave_obj->update_leaveexpenses($expenses_data);
        }
        /* Update leave expenses - END */
        if($query) {
            if($db->affected_rows() == 0) {
                output_xml("<status>false</status><message>{$lang->leavenochangemade}</message>");
                exit;
            }

            //Reset Leave Balance - Start
            $old_type_details = parse_type($old_leave_info['type']);

            if($old_type_details['noBalance'] == 0) {
                if(!value_exists('leavesapproval', 'isApproved', 0, 'lid='.$lid)) {
                    $old_workingdays = count_workingdays($core->input['uid'], $old_leave_info['fromDate'], $old_leave_info['toDate'], $old_type_details['isWholeDay']);
                    $old_leave_updatedetails = array(
                            'uid' => $core->input['uid'],
                            'workingDays' => $old_workingdays,
                            'fromDate' => $old_leave_info['fromDate'],
                            'toDate' => $old_leave_info['toDate'],
                            'type' => $old_leave_info['type'],
                            'lid' => $lid,
                            'negativeWorkingDays' => true
                    );

                    $stat = new LeavesStats();
                    $stat->generate_periodbased($old_leave_updatedetails);
                }
            }
            //Reset Leave Balance - End

            $log->record($lid);
            /* Create leave expenses - START */
            if($core->usergroup['attenance_canApproveAllLeaves'] == 0) {
                $db->update_query('leavesapproval', array('isApproved' => 0, 'timeApproved' => 0), "lid='{$lid}'");
                $affected_rows = $db->affected_rows();
                if($affected_rows == 0) {
                    $never_approved_before = true;
                }
            }
            $approve_immediately = false;
            if($is_onbehalf == true) {
                if($core->user['uid'] == $leave_user['reportsTo'] || $core->usergroup['attenance_canApproveAllLeaves'] == 1 || empty($leave_user['reportsTo'])) {
                    $approve_immediately = true; //To be fully implemented at second stage
                    //$db->update_query('leavesapproval', array('isApproved' => 1, 'timeApproved' => TIME_NOW), "lid='{$lid}' AND uid='{$core->user[uid]}'");
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

            if($approve_immediately == true) {
                $db->update_query('leavesapproval', array('isApproved' => 1, 'timeApproved' => TIME_NOW), "lid='{$lid}' AND uid='{$core->user[uid]}'");
            }

            if($approve_immediately == false && $notification_required == true) {
                $toapprove = $toapprove_select = unserialize($leavetype_details['toApprove']); //explode(',', $leavetype_details['toApprove']);

                if(is_array($toapprove)) {
                    $aff_obj = new Affiliates($leave_user_obj->get_mainaffiliate()->get()['affid'], false);
                    foreach($toapprove as $key => $val) {
                        switch($val) {
                            case 'reportsTo':
                                list($to) = get_specificdata('users', 'email', '0', 'email', '', 0, "uid='{$leave_user[reportsTo]}'");
                                $approvers['reportsTo'] = $leave_user['reportsTo'];
                                unset($toapprove_select[$key]);
                                break;
                            case 'generalManager':
                                $approvers['generalManager'] = $aff_obj->get_generalmanager()->get()['uid'];
                                break;
                            case 'hrManager':
                                $approvers['hrManager'] = $aff_obj->get_hrmanager()->get()['uid'];
                                break;
                            case 'supervisor':
                                $approvers['supervisor'] = $aff_obj->get_supervisor()->get()['uid'];
                                break;
                            case 'segmentCoordinator':
                                /* If leave has segment selected  get  the related segment */
                                if(is_object($leave_obj->get_segment())) {
                                    $leave_segmobjs = $leave_obj->get_segment();
                                    /* For the related segment objet   get  their related coordinators */
                                    $leave_segment_coordinatorobjs = $leave_segmobjs->get_coordinators();
                                    if(is_array($leave_segment_coordinatorobjs)) {
                                        $leave_segment_coordinatorobj = $leave_segment_coordinatorobjs[array_rand($leave_segment_coordinatorobjs, 1)];
                                        $approvers['segmentCoordinator'] = $leave_segment_coordinatorobj->get_coordinator()->get()['uid'];
                                    }
                                }
                                break;
                            case 'financialManager':
                                $approvers['financialManager'] = $aff_obj->get_financialemanager()->get()['uid'];
                                break;
                            default:
                                if(is_int($val)) {
                                    $approvers[$val] = $val;
                                }
                                unset($toapprove_select[$key]);
                                break;
                        }
                    }
                }
                /* Make list of approvers unique */
                $approvers = array_unique($approvers);
            }

//			if(is_array($toapprove_select) && !empty($toapprove_select)) {
//				$secondapprovers = $db->fetch_assoc($db->query("SELECT ".implode(', ', $toapprove_select)."
//									  FROM ".Tprefix."affiliates
//									  WHERE affid=(SELECT affid FROM affiliatedemployees WHERE uid='".$db->escape_string($leave_user['uid'])."' AND isMain='1')"));
//			}

            if($approve_immediately == true) {
                $query = $db->query("SELECT la.uid, u.email FROM ".Tprefix."leavesapproval la JOIN ".Tprefix."users u ON (u.uid=la.uid) WHERE lid='{$lid}' ORDER BY sequence ASC");
                if($db->num_rows($query) > 1) {
                    $to = $db->fetch_field($query, 'email', 1); //Second in sequence after reportsTo
                    $approve_immediately = false;
                }
            }

//			if(is_array($secondapprovers)) {
//				$approvers = ($approvers + $secondapprovers);   /* merge the 2 arrays in one array */
//			}
            if(is_array($approvers)) {
                $db->delete_query('leavesapproval', 'lid='.$lid);
                foreach($approvers as $key => $val) {
                    if($key != 'reportsTo' && $val == $approvers['reportsTo']) {
                        continue;
                    }

                    $approve_status = $timeapproved = 0;
                    if(($val == $core->user['uid'] && $approve_immediately == true) || ($approve_immediately == true && $key == 'reportsTo' && $core->user['uid'] == $leave_user['reportsTo'])) {
                        if($val == $core->user['uid']) {
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
            /* if(is_array($toapprove_select) && !empty($toapprove_select)) {
              $approvers = $db->fetch_assoc($db->query("SELECT ".implode(', ', $toapprove_select)."
              FROM ".Tprefix."affiliates
              WHERE affid=(SELECT affid FROM affiliatedemployees WHERE uid='".$db->escape_string($leave_user['uid'])."' AND isMain='1')"));
              }

              $approvers['reportsTo'] = $leave_user['reportsTo'];

              if(count($approvers) > 1 && $approve_immediately == true) {
              foreach($approvers as $key => $val) {
              if($key != 'reportsTo' && $val == $approvers['reportsTo']) {
              continue;
              }
              else
              {
              list($to) = get_specificdata('users', 'email', '0', 'email', '', 0, "uid='".$approvers[$toapprove[array_search($key, $toapprove)]]."'"); //Second in sequence after reportsTo
              $approve_immediately = false;
              break;
              }
              }
              }
             */

            $lang->load('attendance_messages');

            if(date($core->settings['dateformat'], $core->input['fromDate']) != date($core->settings['dateformat'], $core->input['toDate'])) {
                $todate_format = $core->settings['dateformat'].' '.$core->settings['timeformat'];
            }
            else {
                $todate_format = $core->settings['timeformat'];
            }

            if($approve_immediately == false) {
                //$approve_link = DOMAIN.'/index.php?module=attendance/listleaves&action=perform_approveleave&toapprove='.base64_encode($old_leave_info['requestKey']).'&referrer=email';
                $approve_link = DOMAIN.'/index.php?module=attendance/listleaves&action=takeactionpage&requestKey='.base64_encode($current_leave_info['requestKey']).'&id='.base64_encode($lid);

                $leave['workingdays'] = count_workingdays($leave_user['uid'], $core->input['fromDate'], $core->input['toDate'], $leavetype_details['isWholeDay']);
                $lang->leavenotificationmessage_days = $lang->sprint($lang->leavenotificationmessage_days, $leave['workingdays']);

                if($leavetype_details['isBusiness'] == 0) {
                    $leavestats = $db->fetch_assoc($db->query("SELECT *
								FROM ".Tprefix."leavesstats
								WHERE uid='".$db->escape_string($leave_user['uid'])."' AND (ltid='".$db->escape_string($core->input['type'])."' OR ltid = (SELECT countWith FROM ".Tprefix."leavetypes WHERE ltid='".$db->escape_string($core->input['type'])."' AND countWith!=0)) AND (".$db->escape_string($core->input['fromDate'])." BETWEEN periodStart AND periodEnd)"));

                    /* 				$lang->modifyleavemessage_stats = $lang->sprint($lang->modifyleavemessage_stats,
                      $leavestats['canTake'],
                      $leavestats['entitledFor'],
                      $leavestats['additionalDays'],
                      $leavestats['daysTaken'],
                      ($leavestats['canTake'] - $leavestats['daysTaken']),
                      ($leavestats['canTake'] - $leavestats['daysTaken']) - $leave['workingdays']); */
                    $lang->modifyleavemessage_stats = $lang->sprint($lang->modifyleavemessage_stats, ($leavestats['canTake'] - $leavestats['daysTaken']) + $leavestats['additionalDays'], (($leavestats['canTake'] - $leavestats['daysTaken']) + $leavestats['additionalDays']) - $leave['workingdays']
                    );
                }
                else {
                    $lang->modifyleavemessage_stats = '';
                }

                $lang->modifyleavenotificationsubject = $lang->sprint($lang->modifyleavenotificationsubject, $leave_user['firstName'].' '.$leave_user['lastName'], '['.$old_leave_info['requestKey'].']');
                //$lang->modifyleavemessage = $lang->sprint($lang->modifyleavemessage, $leave_user['firstName'].' '.$leave_user['lastName'], strtolower($leave['type_output']), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $core->input['fromDate']), date($todate_format, $core->input['toDate']), $core->input['reason'], $approve_link);

                /* Parse expense information for message - START */

//                if($leave_obj->has_expenses()) {
//                    $expenses_data = $leave_obj->get_expensesdetails();
//                    $expenses_message = '';
//                    foreach($expenses_data as $expense) {
//                        if(!empty($lang->{$expense['name']})) {
//                            $expense['title'] = $lang->{$expense['name']};
//                        }
//
//                        if(isset($expense['description']) && !empty($expense['description'])) {
//                            $expense['description'] = ' ('.$expense['description'].')';
//                        }
//
//                        $expenses_message .= $expense['title'].': '.$expense['expectedAmt'].$expense['currency'].$expense['description'].'<br />';
//                    }
//                    $total = $leave_obj->get_expensestotal();
//
//                    $expenses_message_ouput = '<br />'.$expenses_message.'<br />Total: '.$total.' USD<br />';
//                }
                $core->input['reason'] .= $expenses_message_ouput;
                /* Parse expense information for message - END */

                if(!empty($leave['details_crumb'])) {
                    $leave['details_crumb'] = ' - '.$leave['details_crumb'];
                }
                $lang->modifyleavemessage = $lang->sprint($lang->modifyleavemessage, $leave_user['firstName'].' '.$leave_user['lastName'], strtolower($leave['type_output']).' ('.$leavetype_details['description'].')'.$leave['details_crumb'], date($core->settings['dateformat'].' '.$core->settings['timeformat'], $core->input['fromDate']), date($todate_format, $core->input['toDate']), $lang->leavenotificationmessage_days, $core->input['reason'], $lang->modifyleavemessage_stats, $approve_link);
            }
            elseif($approve_immediately == true && $notification_required == true) {
                /* if($leavetype_details['isWholeDay'] == 1) {
                  $employeeshift = $db->fetch_assoc($db->query("SELECT ws.* FROM ".Tprefix."employeesshifts es JOIN ".Tprefix."workshifts ws ON (ws.wsid=es.wsid) WHERE es.uid='{$leave_user[uid]}'"));
                  $employeeshift['weekDays'] = unserialize($employeeshift['weekDays']);

                  $lang->leavenotificationmessage_days = $lang->sprint($lang->leavenotificationmessage_days, count_workingdays($employeeshift['weekDays'], $core->input['fromDate'], $core->input['toDate']));
                  }
                  else
                  {
                  $lang->leavenotificationmessage_days = '.';
                  } */
                $lang->leavenotificationmessage_days = $lang->sprint($lang->leavenotificationmessage_days, count_workingdays($leave_user['uid'], $core->input['fromDate'], $core->input['toDate']));

                /* 				if(!empty($core->input['phoneWhileAbsent'])) {
                  $contact_details = $lang->contactwhileabsent.':<br />'.$core->input['phoneWhileAbsent'].'<br />'.$core->input['addressWhileAbsent'];
                  } */

                if(TIME_NOW > $core->input['fromDate']) {
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


                        //$contact_details = $lang->contactwhileabsent.':<br />'.$core->input['phoneWhileAbsent'].'<br />'.$core->input['addressWhileAbsent'];
                    }
                }
            }

            $leave['details_crumb'] = parse_additionaldata($core->input, $leavetype_details['additionalFields'], 1);
            if(is_array($leave['details_crumb']) && !empty($leave['details_crumb'])) {
                $leave['details_crumb'] = ' - '.implode(' ', $leave['details_crumb']);
            }

            $leave['details_crumb'] = $core->sanitize_inputs($leave['details_crumb'], array('method' => 'striponly', 'removetags' => true));
            if(!empty($leave['details_crumb'])) {
                //$leave['details_crumb'] = implode(' ', parse_additionaldata($core->input, $leavetype_details['additionalFields']));
                //$lang->leavenotificationmessage_typedetails .= ' ('.$core->input['details_crumb'].')';
                $lang->leavenotificationmessage_typedetails = $leave['details_crumb'];
            }
            else {
                $lang->leavenotificationmessage_typedetails = strtolower($leave['type_output']);
            }

            if($never_approved_before == true) {
                $lang->modifyleavenotificationsubject = $lang->sprint($lang->leavenotificationsubject, $leave_user['firstName'].' '.$leave_user['lastName'], 'modified their leave '.$lang->leavenotificationmessage_typedetails, $tooktaking, date($core->settings['dateformat'], $core->input['fromDate']), date($subject_todate_format, $core->input['toDate']));
                $lang->modifyleavenotificationmessage = $lang->sprint($lang->leavenotificationmessage, $leave_user['firstName'].' '.$leave_user['lastName'], $lang->leavenotificationmessage_typedetails, date($core->settings['dateformat'].' '.$core->settings['timeformat'], $core->input['fromDate']), date($todate_format, $core->input['toDate']), $lang->leavenotificationmessage_days, $tooktaking, $contact_details);
            }
            else {
                $lang->modifyleavenotificationsubject = $lang->sprint($lang->modifyleavenotificationsubject, $leave_user['firstName'].' '.$leave_user['lastName'], '');
                $lang->modifyleavenotificationmessage = $lang->sprint($lang->modifyleavenotificationmessage, $leave_user['firstName'].' '.$leave_user['lastName'], $tooktaking, $lang->leavenotificationmessage_typedetails, date($core->settings['dateformat'].' '.$core->settings['timeformat'], $core->input['fromDate']), date($todate_format, $core->input['toDate']), $lang->leavenotificationmessage_days, $contact_details);
            }

            if($approve_immediately == false) {
                $email_data = array(
                        'from_email' => 'approve_leaverequest@ocos.orkila.com',
                        'from' => 'Orkila Attendance System',
                        'to' => $to,
                        'subject' => $lang->modifyleavenotificationsubject,
                        'message' => $lang->modifyleavemessage
                );
            }
            elseif($approve_immediately == true) {  //&& $notification_required == true
                if($leavetype_details['noBalance'] == 0) {
                    $stat = new LeavesStats();
                    $stat->generate_periodbased($core->input);
                }

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

                $email_data = array(
                        'from_email' => 'attendance@ocos.orkila.com',
                        'from' => 'Orkila Attendance System',
                        'to' => $mailingLists,
                        'subject' => $lang->modifyleavenotificationsubject,
                        'message' => $lang->modifyleavenotificationmessage
                );
            }

            if(!empty($email_data['to'])) {
                $mail = new Mailer($email_data, 'php');
                if($mail->get_status() === true) {
                    $log->record('notifysupervisors', $email_data['to']);
                    output_xml("<status>true</status><message>{$lang->leavesuccessfullymodified}</message>");
                }
            }
            else {
                $log->record('notifysupervisors', $email_data['to']);
                output_xml("<status>true</status><message>{$lang->leavesuccessfullymodified}</message>");
            }
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
        }
    }
}
?>