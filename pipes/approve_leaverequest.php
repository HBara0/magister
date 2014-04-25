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
$msg = LeavesMessages::extract_message($data['message'], false);

echo $msg;

$reply_message = get_replymessage($data['message']);
echo $reply_message;
switch($reply_message) {
    case 'approve':

        $lang->load('attendance_messages');
        $lang->load('attendance_meta');

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

            $query = $db->query("SELECT DISTINCT(u.uid), Concat(firstName, ' ', lastName) AS employeename FROM ".Tprefix."users u LEFT JOIN ".Tprefix."usersemails ue ON (ue.uid=u.uid) WHERE u.email='".$db->escape_string($data['from'])."' OR ue.email='".$db->escape_string($data['from'])."'");
            if($db->num_rows($query) > 0) {
                $user = $db->fetch_assoc($query);

                $db->update_query('leavesapproval', array('isApproved' => 1, 'timeApproved' => TIME_NOW), "lid='{$leave[lid]}' AND uid='{$user[uid]}' AND isApproved='0'");
                if($db->affected_rows() > 0) {
                    $query3 = $db->query("SELECT l.uid, u.email
				FROM ".Tprefix."leavesapproval l LEFT JOIN ".Tprefix."users u ON (u.uid=l.uid)
				WHERE l.isApproved='0' AND l.lid='{$leave[lid]}' AND sequence > (SELECT sequence FROM ".Tprefix."leavesapproval WHERE lid='{$leave[lid]}' AND uid='{$user[uid]}' AND isApproved=1)
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

                        /* Parse expense information for message - START */
                        $leaveexpense = new Leaves(array('lid' => $leave['lid']));
                        if($leaveexpense->has_expenses()) {
                            $expenses_data = $leaveexpense->get_expensesdetails();
                            $total = 0;
                            $expenses_message = '';
                            foreach($expenses_data as $expense) {
                                if(!empty($lang->{$expense['name']})) {
                                    $expense['title'] = $lang->{$expense['name']};
                                }
                                $total += $expense['expectedAmt'];
                                $expenses_message .= $expense['title'].': '.$expense['expectedAmt'].$expense['currency'].'<br>';
                            }
                            $expenses_message_output = '<br />'.$lang->associatedexpenses.'<br />'.$expenses_message.'<br />Total: '.$total.'USD<br />';
                        }
                        $leave['reason'] .= $expenses_message_output;
                        /* Parse expense information for message - END */

                        $lang->requestleavesubject = $lang->sprint($lang->requestleavesubject, $leave['firstName'].' '.$leave['lastName'], strtolower($leave['type_details']['title']), $request_key);
                        $lang->requestleavemessagesupervisor = $lang->sprint($lang->requestleavemessagesupervisor, $leave['firstName'].' '.$leave['lastName'], strtolower($leave['type_details']['title']).$leave['details_crumb'], date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['fromDate']), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['toDate']), $leave['reason'], $user['employeename'], '', $approve_link);

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
                            update_leavestats_periods($leave, $leave['type_details']['isWholeDay']);
                        }

                        $lang->leaveapprovedmessage = $lang->sprint($lang->leaveapprovedmessage, $leave['firstName'].' '.$leave['lastName'], strtolower($leave['type_details']['title']), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['fromDate']), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['toDate']));

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
                            $mailinglists = get_specificdata('affiliates', array('affid', $mailinglists_attr), 'affid', $mailinglists_attr, '', 0, 'affid IN ('.implode(',', $to_inform).')');
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
                              //$employeeshift = $db->fetch_assoc($db->query("SELECT ws.* FROM ".Tprefix."employeesshifts es JOIN ".Tprefix."workshifts ws ON (ws.wsid=es.wsid) WHERE es.uid='{$leave[uid]}'"));
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
                                $leave['type_details']['details_crumb'] = implode(' ', parse_additionaldata($leave, $leave['type_details']['additionalFields']));
                                $lang->leavenotificationmessage_typedetails = $leave['type_details']['details_crumb'];
                            }
                            else {
                                $lang->leavenotificationmessage_typedetails = strtolower($leave['type_details']['title']);
                            }

                            $lang->leavenotificationsubject = $lang->sprint($lang->leavenotificationsubject, $leave['firstName'].' '.$leave['lastName'], $lang->leavenotificationmessage_typedetails, $tooktaking, date($core->settings['dateformat'], $leave['fromDate']), date($subject_todate_format, $leave['toDate']));
                            $lang->leavenotificationmessage = $lang->sprint($lang->leavenotificationmessage, $leave['firstName'].' '.$leave['lastName'], $lang->leavenotificationmessage_typedetails, date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['fromDate']), date($message_todate_format, $leave['toDate']), $lang->leavenotificationmessage_days, $tooktaking, $contact_details, $contactperson_details);

                            $email_data = array(
                                    'from_email' => 'attendance@ocos.orkila.com',
                                    'from' => 'Orkila Attendance System',
                                    'to' => $mailinglists,
                                    'subject' => $lang->leavenotificationsubject,
                                    'message' => $lang->leavenotificationmessage
                            );

                            $mail = new Mailer($email_data, 'php');
                            if($mail->get_status() == true) {
                                $log->record('notifyaffiliate', $mailingList);
                            }
                        }
                    }

                    if($ignore_subject == true) {
                        if(isset($request['referrer']) && $request['referrer'] == 'email') {
                            redirect('index.php?module=attendance/listleaves', 3, $lang->leavesuccessfullyapproved);
                        }
                        else {
                            ?>
                            <script language="javascript" type="text/javascript">
                                window.top.$('tr[id="leave_<?php echo $leave['lid'];?>"]').attr('class', 'greenbackground');
                                window.top.$("#approveimg_<?php echo $leave['lid'];?>").remove();
                                window.top.$("#approveleave_Result").html("<?php echo '<span class=\'green_text\'>'.$lang->leavesuccessfullyapproved.'</span>';?>");
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
        break;
    case "message":
        /* Approval conversation messages ---START */
        /* check if the user in the approval chain */

        $request_key = $db->escape_string('5589ad0177');
        $leave = $db->fetch_assoc($db->query("SELECT l.*, u.firstName, u.lastName, email FROM ".Tprefix."leaves l LEFT JOIN ".Tprefix."users u ON (u.uid=l.uid) WHERE l.requestKey='{$request_key}'"));

        $leave_obj = new Leaves(array('lid' => $leave['lid']));
        $query = $db->query("SELECT DISTINCT(u.uid), Concat(firstName, ' ', lastName) AS employeename FROM ".Tprefix."users u  "."LEFT JOIN ".Tprefix."usersemails ue ON (ue.uid=u.uid) WHERE u.email='".$db->escape_string($data['from'])."' OR ue.email='".$db->escape_string($data['from'])."'");
        if($db->num_rows($query) > 0) {
            //$approver = $db->fetch_assoc($query);
        }
        $approvers_objs = $leave_obj->get_approvers();
        foreach($approvers_objs as $approvers_user) {
            $approvers = $approvers_user->get();
        }
        if(is_array($approvers)) {
            if(!$leave_obj->is_leaverequester() || (!in_array($leave['uid'], $approvers))) {
                echo 'end script';
                exit;
            }
        }
        $leavemessage_obj = new LeavesMessages();
        $leavemessage_obj->create_message(array('message' => $data['message'], 'permission' => 'public'), $leave['lid']);
        /* Approval conversation messages ---END */
        break;

    case 'limited':
        $leavemessage_obj = new LeavesMessages();
        $leavemessage_obj->create_message(array('message' => $data['message'], 'permission' => 'limited'), $leave['lid']);

        break;
    case 'private':
        $leavemessage_obj = new LeavesMessages();
        $leavemessage_obj->create_message(array('message' => $data['message'], 'permission' => 'private'), $leave['lid']);

        break;
    case 'revoke':

        /* revoke leave here */
        break;
}
function get_replymessage($messagedata) {
    if(strpos($messagedata, '#approve')) {
        return 'approve';
    }
    elseif(strpos($messagedata, '#reject')) {
        return 'revoke';
    }
    elseif(strpos($messagedata, '#message')) {
        return 'message';
    }
    elseif(strpos($messagedata, '#private')) {
        return 'private';
    }
    elseif(strpos($messagedata, '#limited')) {
        return 'limited';
    }
    else {
        return 'message';
    }
}
?>