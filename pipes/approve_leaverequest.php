#!/usr/local/bin/php -q
<?php
ini_set('memory_limit', '-1');
$dir = dirname(dirname(__FILE__)).'/';
if(!$dir) {
    $dir = '..';
}

if(isset($request['notpiped']) && $request['notpiped'] == 1) {
    $ignore_subject = true;
}
else {
    require_once $dir.'/inc/init.php';
    require_once $dir.'/inc/attendance_functions.php';
    $lang = new Language('english');
    $lang->load('messages');

    $pipe = new Pipe();
    $data = $pipe->get_data();
    $ignore_subject = false;
}
$lang->load('attendance_messages');
$lang->load('attendance_meta');
$lang->load('travelmanager_meta');

if(preg_match("/\[([a-zA-Z0-9]+)\]$/", $data['subject'], $subject) || $ignore_subject == true) {
    if($ignore_subject == true) {
        $request_key = $db->escape_string($request['requestkey']);
        $data['from'] = $core->user['email'];
    }
    else {
        /* Check if reply ia possiblity auto-responder */
        if(strstr(strtolower($data['subject']), 'auto')) {
            exit;
        }
        $request_key = $db->escape_string($subject[1]);
    }
    $leave = $db->fetch_assoc($db->query("SELECT l.*, u.firstName, u.lastName, email FROM ".Tprefix."leaves l LEFT JOIN ".Tprefix."users u ON (u.uid=l.uid) WHERE l.requestKey='{$request_key}'"));
    if(empty($leave['lid'])) {
        $lang->apporvinganonexistingleave = $lang->sprint($lang->apporvinganonexistingleavesubject, $core->user['displayName']);
        $email_data = array(
                'from_email' => 'attendance@ocos.orkila.com',
                'from' => 'Orkila Attendance System',
                'to' => $data['from'],
                'subject' => $lang->apporvinganonexistingleavesubject,
                'message' => $lang->apporvinganonexistingleave
        );
        $mail = new Mailer($email_data, 'php');
        error($lang->leavedoestnoexist, 'index.php?module=attendance/listleaves');
        exit;
    }
    $employee = new Users($leave['uid']);
    $leave_obj = new Leaves(array('lid' => $leave['lid']), false);

    $query = $db->query("SELECT DISTINCT(u.uid), Concat(firstName, ' ', lastName) AS employeename FROM ".Tprefix."users u LEFT JOIN ".Tprefix."usersemails ue ON (ue.uid=u.uid) WHERE u.email='".$db->escape_string($data['from'])."' OR ue.email='".$db->escape_string($data['from'])."'");
    if($db->num_rows($query) > 0) {
        $user = $db->fetch_assoc($query);
        //$user = $db->fetch_assoc($query);
        $db->update_query('leavesapproval', array('isApproved' => 1, 'timeApproved' => TIME_NOW), "lid='{$leave[lid]}' AND uid=".$user['uid']." AND isApproved='0'");
        if($db->affected_rows() > 0) {
            $query3 = $db->query("SELECT l.uid, u.email
				FROM ".Tprefix."leavesapproval l LEFT JOIN ".Tprefix."users u ON (u.uid=l.uid)
				WHERE l.isApproved='0' AND l.lid='{$leave[lid]}' AND sequence>(SELECT MAX(sequence) FROM ".Tprefix."leavesapproval WHERE lid='{$leave[lid]}' AND uid='{$user[uid]}' AND isApproved=1)
                                ORDER BY sequence ASC
                                LIMIT 0, 1");
            if($db->num_rows($query3) > 0) {
                $approver = $db->fetch_assoc($query3);
                $leave['type_details'] = parse_type($leave['type']);
                $leave['details_crumb'] = parse_additionaldata($leave, $leave['type_details']['additionalFields']);
                if(is_array($leave['details_crumb']) && !empty($leave['details_crumb'])) {
                    $leave['details_crumb'] = ' - '.implode(' ', $leave['details_crumb']);
                }

                //$approve_link = DOMAIN.'/index.php?module=attendance/listleaves&action=perform_approveleave&toapprove='.base64_encode($core->input['requestKey']).'&referrer=email';
                $approve_link = DOMAIN.'/index.php?module=attendance/listleaves&action=takeactionpage&requestKey='.base64_encode($core->input['requestKey']).'&id='.base64_encode($leave['lid']);
                $travelmanager_plan = TravelManagerPlan::get_plan(array('lid' => $leave['lid']), array('returnarray' => false));

                if(is_object($travelmanager_plan)) {
                    $planid = $travelmanager_plan->tmpid;
                    $approve_link = DOMAIN.'/index.php?module=attendance/listleaves&action=takeactionpage&requestKey='.base64_encode($leave_obj->requestKey).'&id='.base64_encode($leave_obj->lid).'&tmpid='.$travelmanager_plan->tmpid;
                    //$leave = $travelmanager_plan->get_leave();
                    $leave_type = $leave_obj->get_type();
                    $employee = $leave_obj->get_user(); //->get_displayname();
                    $leave_purpose = $leave_segment = $lang->na;
                    $leave_purpose = $leave_obj->reason; //get_purpose()->get()['name'];

                    if(is_object($leave_obj->get_segment())) {
                        $leave_segment = $leave_obj->get_segment()->get()['title'];
                    }
                    $plan_name = $leave_type->title.' - '.$leave_obj->get_country()->get_displayname();
                    $leave_requestey = $leave_obj->requestKey;
                    $approve_link = DOMAIN.'/index.php?module=attendance/listleaves&action=takeactionpage&requestKey='.base64_encode($leave_obj->requestKey).'&id='.base64_encode($leave_obj->lid).'&tmpid='.$planid;

                    $segment_objs = TravelManagerPlanSegments::get_segments(array('tmpid' => $planid), array('order' => 'sequence', 'simple' => false, 'returnarray' => true));
                    if(is_array($segment_objs)) {

                        foreach($segment_objs as $segmentid => $segment) {
                            $segment_details .= $segment->parse_segment();
                            $segment_expenses = $segment->parse_expensesummary();
                        }

                        foreach($segment_objs as $segmentid => $segment) {
                            $transportaion_fields_title = '<div style="font-size: 24px;color: #91B64F;font-weight: 100;">'.$segment->get_origincity()->name.' - '.$segment->get_destinationcity()->name.'</div>';
                            /* Get and parse all the possibe Flights */
                            if(!empty($segment->get()['apiFlightdata'])) {
                                $transportaionsegment_fields .= '<div style="horizontal-align: middle; font-weight: bold;border-bottom: 1px dashed #666;font-size: 14px;padding:5px; background-color: #92D050 ; ">'.$lang->allpossibleflights.'</div>';
                                $transportaionsegment_fields .= TravelManagerAirlines::parse_bestflight($segment->get()['apiFlightdata'], array(), $sequence, 'email');
                            }
                            /* Get and parse all the possibe Approved Hotels */
                            $destcity = new Cities($segment->destinationCity);
                            $approvedhotels = $destcity->get_country()->get_approvedhotels();
                            if(is_array($approvedhotels)) {
                                foreach($approvedhotels as $hotel) {
                                    $isselectedhotel = TravelManagerPlanaccomodations::get_data(array('tmpsid' => $segment->tmpsid, 'tmhid' => $hotel->tmhid));
                                    if(is_object($isselectedhotel)) {
                                        continue;
                                    }
                                    $path = "{$core->settings['rootdir']}/images/invalid.gif";
                                    $iscontractedicon = '<img src="data:image/png;base64,'.base64_encode(file_get_contents($path)).'" alt="'.$lang->no.'"/>';
                                    if($hotel->isContracted == 1) {
                                        $path = "{$core->settings['rootdir']}/images/valid.gif";
                                        $iscontractedicon = '<img src="data:image/png;base64,'.base64_encode(file_get_contents($path)).'" alt="'.$lang->yes.'"/>';
                                    }
                                    /* parse ratings */
                                    eval("\$otherapprovedhotels .= \"".$template->get('travelmanager_approvedhotel_row')."\";");
                                }
                                $transportaionsegment_fields .= $transportaion_fields_title;
                                eval("\$transportaionsegment_fields .= \"".$template->get('travelmanager_viewplan_approvedhotels')."\";");
                            }
                            unset($otherapprovedhotels);
                        }
                        if(!empty($transportaionsegment_fields)) {
                            $transportaion_fields .= $transportaionsegment_fields;
                            unset($transportaionsegment_fields, $transportaion_fields_title);
                        }
                    }
                    // eval("\$leave_details = \"".$template->get('travelmanager_viewlpan_leavedtls')."\";");
                    eval("\$travelmanager_viewplan = \"".$template->get('travelmanager_viewlpanemail')."\";");

                    //$leave = $leave_obj->get();
                    //$leave['firstName'] = $employee->firstName;
                    //$leave['lastName'] = $employee->lastName;
                    //$leave['type_details'] = parse_type($leave_type->ltid);
                }

                /* Parse expense information for message - START */
                if($leave_obj->has_expenses()) {
                    $expenses_data = $leave_obj->get_expensesdetails();
                    $total = 0;
                    $expenses_message = '';
                    foreach($expenses_data as $expense) {
                        if(!empty($lang->{$expense['name']})) {
                            $expense['title'] = $lang->{$expense['name']};
                        }
                        $total += $expense['expectedAmt'];

                        $exptype_obj = LeaveExpenseTypes::get_exptype_byattr('title', $expense['title'], false);
                        if(is_object($exptype_obj)) {
                            $agency_link = $exptype_obj->parse_agencylink($leave_obj);
                        }
                        $expenses_message .= $expense['title'].': '.$expense['expectedAmt'].$expense['currency'].' '.$agency_link.'<br>';
                        unset($agency_link);
                    }
                    $expenses_message_output = '<br />'.$lang->associatedexpenses.'<br />'.$expenses_message.'<br />Total: '.$total.'USD<br />';
                }
                $leave['reason'] .= $expenses_message_output;
                /* Parse expense information for message - END */

                $lang->requestleavesubject = $lang->sprint($lang->requestleavesubject, $leave['firstName'].' '.$leave['lastName'], strtolower($leave['type_details']['title']), $request_key);
                $lang->requestleavemessagesupervisor = $lang->sprint($lang->requestleavemessagesupervisor, $leave['firstName'].' '.$leave['lastName'], strtolower($leave['type_details']['title']).$leave['details_crumb'], date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['fromDate']), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['toDate']), $leave['reason'], $user['employeename'], $approve_link, $travelmanager_viewplan);

                $email_data = array(
                        'from_email' => 'approve_leaverequest@ocos.orkila.com',
                        'from' => 'Orkila Attendance System',
                        'to' => $approver['email'],
                        'subject' => $lang->requestleavesubject,
                        'message' => $lang->requestleavemessagesupervisor
                );
                $mail = new Mailer($email_data, 'php');
                if($mail->get_status() === true) {
                    $log->record('notifysupervisors', $email_data['to']);
                }
            }
            else {
                $leave['type_details'] = parse_type($leave['type']);

                if($leave['type_details']['noBalance'] == 0) {
                    $stat = new LeavesStats();
                    $stat->generate_periodbased($leave);
                }

                $modifyleave_link = 'https://ocos.orkila.com/index.php?module=attendance/editleave&lid='.$leave['lid'];
                $lang->leaveapprovedmessage = $lang->sprint($lang->leaveapprovedmessage, $leave['firstName'].' '.$leave['lastName'], strtolower($leave['type_details']['title']), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['fromDate']), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['toDate']), $modifyleave_link);

                $email_data = array(
                        'from_email' => 'attendance@ocos.orkila.com',
                        'from' => 'Orkila Attendance System',
                        'to' => $leave['email'],
                        'subject' => $lang->leaveapprovedsubject,
                        'message' => $lang->leaveapprovedmessage
                );

                $mail = new Mailer($email_data, 'php');
                if($mail->get_status() === true) {
                    $log->record('notifyapprovedleave', $email_data['to']);
                }

                $to_inform = unserialize($leave['affToInform']);

                if(is_array($to_inform)) {
                    $mailinglists_attr = 'altMailingList';
                    if($leave['type_details']['isBusiness'] == 1) {
                        $mailinglists_attr = 'mailingList';
                    }
                    $mailinglists = get_specificdata('affiliates', array('affid', $mailinglists_attr), 'affid', $mailinglists_attr, '', 0, 'affid IN ('.implode(', ', $to_inform).')');
                    //$mailingList = $db->fetch_field($db->query("SELECT aff.mailingList FROM ".Tprefix."affiliates aff LEFT JOIN ".Tprefix."affiliatedemployees ae ON (ae.affid=aff.affid) WHERE ae.isMain='1' AND ae.uid='{$leave[uid]}'"), 'mailingList');
                }

                if(is_array($mailinglists) || !empty($mailinglists)) {
                    if(date($core->settings['dateformat'], $leave['fromDate']) != date($core->settings['dateformat'], $leave['toDate'])) {
                        $message_todate_format = $core->settings['dateformat'].' '.$core->settings['timeformat'];
                        $subject_todate_format = ' - '.$core->settings['dateformat'];
                    }
                    else {
                        $message_todate_format = $core->settings['timeformat'];
                        $subject_todate_format = '';
                    }

                    /* if($leave['type_details']['isWholeDay'] == 1) {
                      //$employeeshift = $db->fetch_assoc($db->query("SELECT ws.* FROM ".Tprefix."employeesshifts es JOIN ".Tprefix."workshifts ws ON (ws.wsid=es.wsid) WHERE es.uid=' {
                      $leave[uid]
                      }'"));
                      //$employeeshift['weekDays'] = unserialize($employeeshift['weekDays']);

                      $lang->leavenotificationmessage_days = $lang->sprint($lang->leavenotificationmessage_days, count_workingdays($leave['uid'], $leave['fromDate'], $leave['toDate']));
                      }
                      else
                      {
                      $lang->leavenotificationmessage_days = '.';
                      } */
                    $lang->leavenotificationmessage_days = $lang->sprint($lang->leavenotificationmessage_days, count_workingdays($leave['uid'], $leave['fromDate'], $leave['toDate'], $leave['type_details']['isWholeDay']));

                    if(TIME_NOW > $leave['fromDate']) {
                        if($leave['type_details']['isBusiness'] == 0) {
                            $tooktaking = $lang->leavenotificationsubject_took;
                        }
                        else {
                            $tooktaking = $lang->leavenotificationsubject_wasat;
                        }
                    }
                    else {
                        if($leave['type_details']['isBusiness'] == 0) {
                            $tooktaking = $lang->leavenotificationsubject_taking;
                        }
                        else {
                            $tooktaking = $lang->leavenotificationsubject_willbeat;
                        }

                        if(!empty($leave['phoneWhileAbsent'])) {
                            $lang->leavenotificationmessage_owncontact = $lang->leavenotificationmessage_owncontact_limitedemail;

                            if($leave['limitedEmail'] == 0) {
                                $lang->leavenotificationmessage_owncontact = $lang->leavenotificationmessage_owncontact_email;
                            }

                            $contact_details = $lang->sprint($lang->leavenotificationmessage_owncontact, $leave['firstName'].' '.$leave['lastName'], '+'.$leave['phoneWhileAbsent'].'<br />'.$leave['addressWhileAbsent']);
                        }

                        if(!empty($leave['contactPerson'])) {
                            $cotactperson_details = $db->fetch_assoc($db->query("SELECT displayName AS contactPersonName, email FROM ".Tprefix."users WHERE uid=".$db->escape_string($leave['contactPerson']).""));
                            $contactperson_details = $lang->sprint($lang->leavenotificationmessage_contactperson, $cotactperson_details['contactPersonName'], $cotactperson_details['email']);
                        }
                    }

                    /* if(!empty($leave['phoneWhileAbsent'])) {
                      $contact_details = $lang->contactwhileabsent.':<br />'.$leave['phoneWhileAbsent'].'<br />'.$leave['addressWhileAbsent'];
                      } */
                    if(!empty($leave['type_details']['additionalFields'])) {
                        $additionaldata = parse_additionaldata($leave, $leave['type_details']['additionalFields'], 1);
                        if(is_array($additionaldata)) {
                            $leave['type_details']['details_crumb'] = implode(' ', $additionaldata);
                        }
                        $leave['type_details']['details_crumb'] = $core->sanitize_inputs($leave['type_details']['details_crumb'], array('method' => 'striponly', 'removetags' => true));
                        $lang->leavenotificationmessage_typedetails = $leave['type_details']['details_crumb'];
                    }

                    if(empty($lang->leavenotificationmessage_typedetails)) {
                        $lang->leavenotificationmessage_typedetails = strtolower($leave['type_details']['title']);
                    }

                    $lang->leavenotificationsubject = $lang->sprint($lang->leavenotificationsubject, $leave['firstName'].' '.$leave['lastName'], $lang->leavenotificationmessage_typedetails, $tooktaking, date($core->settings['dateformat'], $leave['fromDate']), date($subject_todate_format, $leave['toDate']));
                    $lang->leavenotificationmessage = $lang->sprint($lang->leavenotificationmessage, $leave['firstName'].' '.$leave['lastName'], $lang->leavenotificationmessage_typedetails, date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['fromDate']), date($message_todate_format, $leave['toDate']), $lang->leavenotificationmessage_days, $tooktaking, $contact_details, $contactperson_details);
                    $main_affiliate = $employee->get_mainaffiliate();
                    if(is_object($main_affiliate) && !empty($main_affiliate->cpAccount) && $leave_obj->createAutoResp = 1) {
                        $leave_obj->create_autoresponder();
                    }
                    $email_data = array(
                            'from_email' => 'attendance@ocos.orkila.com',
                            'from' => 'Orkila Attendance System',
                            'to' => $mailinglists,
                            'subject' => $lang->leavenotificationsubject,
                            'message' => $lang->leavenotificationmessage
                    );

                    $mail = new Mailer($email_data, 'php');
                    if($mail->get_status() == true) {
                        $travelmanager_plan = TravelManagerPlan::get_plan(array('lid' => $leave['lid']), array('returnarray' => false));
                        if(is_object($travelmanager_plan)) {
                            $planid = $travelmanager_plan->tmpid;
                            // $leave = $travelmanager_plan->get_leave();
                            $employee = $leave_obj->get_user()->get_displayname();
                            $segment_objs = TravelManagerPlanSegments::get_segments(array('tmpid' => $planid), array('order' => 'sequence', 'simple' => false, 'returnarray' => true));
                            if(is_array($segment_objs)) {
                                foreach($segment_objs as $segmentid => $segment) {
                                    $travelmanager_plan->email_finance($segment, $lang->sprint($lang->tmplanfinancenotification, $employee));
                                    break;
                                }
                            }
                        }

                        $log->record('notifyaffiliate', $mailingList);
                    }
                }
            }

            if($ignore_subject == true) {
                if(isset($request['referrer']) && $request['referrer'] == 'email') {
                    redirect('index.php?module=attendance/listleaves', 1, $lang->leavesuccessfullyapproved);
                }
                else {
                    ?>
                    <script language="javascript" type="text/javascript">
                        window.top.$('tr[id="leave_<?php echo $leave['lid'];?>"]').attr('class', 'greenbackground');
                        window.top.$("#approveimg_<?php echo $leave['lid'];?>").remove();
                        window.top.$("#approveleave_Result").html("<?php echo '<span class = \'green_text\'>'.$lang->leavesuccessfullyapproved.'</span>';?>");
                        window.top.$("#popup_approveleave").remove();
                    </script>
                    <?php
                }
            }
        }
        else {
            if($ignore_subject == true) {
                if(isset($request['referrer']) && $request['referrer'] == 'email') {
                    error($lang->youapprovedleave, 'index.php?module=attendance/listleaves');
                }
            }
        }
    }
    else {
        if($ignore_subject == true) {
            if(isset($request['referrer']) && $request['referrer'] == 'email') {
                error($lang->sectionnopermission, 'index.php?module=attendance/listleaves');
            }
        }
    }
}
?>