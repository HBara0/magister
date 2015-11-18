<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Events & Tasks
 * $module: calendar
 * $id: evenstasks.php
 * Created: 	@zaher.reda 	April 26, 2011 | 11:52 AM
 * Last Update: @tony.assaad    Sep 6, 2013 | 12:09:56 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    redirect('index.php?module=calendar/home');
}
else {
    if($core->input['action'] == 'do_createtask') {
//if($core->input['type'] == 'task') {
        $task = new Tasks();
        $task->create_task($core->input['task']);

        switch($task->get_status()) {
            case 0:
                if($core->input['task']['notify']) {
                    $task->notify_task();
                }
                header('Content-type: text/xml+javascript');
//output_xml('<![CDATA[<script>$("#popup_createeventtask").dialog("close");</script>]]>');
                output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                exit;
            case 2:
                output_xml("<status>false</status><message>{$lang->taskexists}</message>");
                exit;
            case 3:
                output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                exit;
        }
    }
    elseif($core->input['action'] == 'do_createeventtask') {

        echo $headerinc;
        if(is_empty($core->input['event']['title'], $core->input['event']['fromDate'], $core->input['event']['toDate'], $core->input['event']['type'], $core->input['event']['description'])) {
//output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            ?>
            <script language="javascript" type="text/javascript">
                $(function() {
                    top.$("#upload_Result").html("<span class='red_text'><?php echo $lang->fillallrequiredfields;?></span>");
                });
            </script>
            <?php
            exit;
        }

        $new_event = array(
                'title' => ucwords(strtolower($core->input['event']['title'])),
                'identifier' => substr(md5(uniqid(microtime())), 0, 10),
                'description' => ucfirst(strtolower($core->input['event']['description'])),
                'uid' => $core->user['uid'],
                'affid' => $core->input['event']['affid'],
                'spid' => $core->input['event']['spid'],
                'isPublic' => $core->input['event']['isPublic'],
                'place' => $core->input['event']['place'],
                'type' => $core->input['event']['type'],
                'createdOn' => TIME_NOW,
                'createdBy' => $core->user['uid'],
                'publishOnWebsite' => $core->input['event']['publishOnWebsite']
        );
        $new_event['alias'] = generate_alias($new_event['title']);
        $new_event['fromDate'] = strtotime($core->input['event']['fromDate'].' '.$core->input['event']['fromTime']);
        $new_event['toDate'] = strtotime($core->input['event']['toDate'].' '.$core->input['event']['toTime']);

        if(value_exists('calendar_events', 'title', $core->input['event']['title'], 'type='.$db->escape_string($core->input['event']['type']).' AND (toDate='.$new_event['toDate'].' OR fromDate='.$new_event['fromDate'].')')) {
            ?>
            <script language="javascript" type="text/javascript">
                $(function() {
                    top.$("#upload_Result").html("<span class='red_text'><?php echo $lang->eventexists;?></span>");
                });
            </script>
            <?php
            exit;
        }
        /* Parse incoming Attachemtns - START */
        $core->input['attachments'] = $_FILES['attachments'];

        if(!empty($core->input['attachments']['name'][0])) {
            $upload_param['upload_allowed_types'] = array('image/jpeg', 'image/gif', 'image/png', 'application/zip', 'application/pdf', 'application/x-pdf', 'application/msword', 'application/vnd.ms-powerpoint', 'text/plain', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation');
            if(is_array($core->input['attachments'])) {
                $upload_obj = new Uploader('attachments', $core->input, $upload_param['upload_allowed_types'], 'putfile', 5242880, 1, 1); //5242880 bytes = 5 MB (1024);
                $attachments_path = './uploads/eventsattachments';
                $upload_obj->set_upload_path($attachments_path);
                $upload_obj->process_file();
                $attachments = $upload_obj->get_filesinfo();

                if($upload_obj->get_status() != 4) {
                    ?>
                    <script language="javascript" type="text/javascript">
                        $(function() {
                            top.$("#upload_Result").html("<span class='red_text'><?php echo $upload_obj->parse_status($upload_obj->get_status());?></span>");
                        });
                    </script>
                    <?php
                    exit;
                }
            }
        }
        /* Parse incoming Attachemtns - END */
        /* Parse Event Logo - START */
        if(!empty($_FILES['logo']['name'][0])) {
            $_FILES['logo']['newname'][0] = $new_event['alias'];
            $upload_param['upload_allowed_types'] = array('image/jpg', 'image/jpeg', 'image/gif', 'image/png');
            $upload_obj = new Uploader('logo', $_FILES, $upload_param['upload_allowed_types'], 'putfile', 5242880, 1, 1); //5242880 bytes = 5 MB (1024);
            $logo_path = './uploads/eventslogos';
            $upload_obj->set_upload_path($logo_path);
            $upload_obj->process_file();
            $upload_obj->resize(150, '');

            $logo = $upload_obj->get_filesinfo();
            $new_event['logo'] = $upload_obj->get_filename();
            if($upload_obj->get_status() != 4) {
                ?>
                <script language="javascript" type="text/javascript">
                    $(function() {
                        top.$("#upload_Result").html("<span class='red_text'><?php echo $upload_obj->parse_status($upload_obj->get_status());?></span>");
                    });
                </script>
                <?php
                exit;
            }
        }
        /* Parse Event Logo - END */
        $query = $db->insert_query('calendar_events', $new_event);
        $last_id = $db->last_id();
        $event_obj = new Events($last_id, false);

        if(!empty($event_obj->logo)) {
            $cms = new Cms();
            $source = array('path' => './uploads/eventslogos/', 'filename' => $event_obj->logo);
            $destination = array('path' => '/development/website/uploads/eventslogos/', 'filename' => $event_obj->logo);
            $cms->copy_file_ftp($source, $destination);
        }

        $events_details = $event_obj->get();
        /* Add event Invitee */
        if(is_array($core->input['event']['invitee'])) {
            foreach($core->input['event']['invitee'] as $invitee) {
                if(empty($invitee)) {
                    continue;
                }
                $new_event_invitee_data = array(
                        'ceid' => $last_id,
                        'uid' => $invitee,
                        'createdOn' => TIME_NOW,
                        'createdBy' => $core->user['uid']
                );
                $db->insert_query('calendar_events_invitees', $new_event_invitee_data);
            }
        }

        /* Get invitess by user */
        $event_users_objs = $event_obj->get_invited_users();
        if(is_array($event_users_objs)) {
            foreach($event_users_objs as $event_users_obj) {
                $event_users = $event_users_obj->get();
                /* iCal event to the users */
                $ical_obj = new iCalendar(array('identifier' => $events_details['identifier'], 'uidtimestamp' => $events_details['createdOn']));  /* pass identifer to outlook to avoid creation of multiple file with the same date */
                $ical_obj->set_datestart($events_details['fromDate']);
                $ical_obj->set_datend($events_details['toDate']);
                $ical_obj->set_location($events_details['place']);
                $ical_obj->set_summary($events_details['title']);
                $ical_obj->set_categories('Event');
                $ical_obj->set_organizer();
                $ical_obj->set_icalattendees($event_users['uid']);
                $ical_obj->set_description($events_details['description']);
                $ical_obj->endical();

                $mailer = new Mailer();
                $mailer = $mailer->get_mailerobj();
                $mailer->set_type('ical', array('content-class' => 'meetingrequest', 'method' => 'REQUEST'));
                $mailer->set_from(array('name' => 'OCOS Mailer', 'email' => $core->settings['maileremail']));
                $mailer->set_subject($events_details['title']);
                $mailer->set_message($ical_obj->geticalendar());
                $mailer->set_to($event_users['email']);

                /* Add multiple Attachments */
                if(is_array($attachments)) {
                    foreach($attachments as $attachment) {
                        $mailer->add_attachment($attachments_path.'/'.$attachment['name']);
                    }
                }
                $mailer->send();
            }
        }
        if($core->input['event']['isPublic'] == 1 && $core->usergroup['calendar_canAddPublicEvents'] == 1) {
            if(isset($core->input['event']['restrictto'])) {
                if(is_array($core->input['event']['restrictto'])) {
                    foreach($core->input['event']['restrictto'] as $affid) {
                        $db->insert_query('calendar_events_restrictions', array('affid' => $affid, 'ceid' => $last_id));
                    }
                    if(isset($core->input['event']['notify']) && $core->input['event']['notify'] == 1) {
                        /* Send the event notification - START */
                        $notification_mails = get_specificdata('affiliates', array('affid', 'mailingList'), 'affid', 'mailingList', '', 0, 'mailingList != "" AND affid IN('.implode(',', $core->input['event']['restrictto']).')');
                        $assignedemp_affs = AffiliatedEmployees::get_column('uid', array('isMain' => 1, 'affid' => $core->input['event']['restrictto']), array('returnarray' => true));
                        if(is_array($assignedemp_affs)) {
                            $meetininvitees = Users::get_data('uid IN ('.implode(',', $assignedemp_affs).') AND gid !=7', array('operators' => array('filter' => 'CUSTOMSQLSECURE'), 'returnarray' => true));
                        }
                        /* More recipients for visiting us events - START */

                        $eventtype = CalendarEventTypes::get_data(array('cetid' => $core->input['event']['type']));
                        if(is_object($eventtype) && $eventtype->name == 'visitingus') {
                            $affiliate = new Affiliates($core->input['event']['affid']);
                            $supplier = Entities::get_data(array('eid' => $core->input['event']['spid'], 'type' => 's'));

                            //supplier's segments coordinators
                            if(is_object($supplier)) {
                                $supp_segments = $supplier->get_segments();
                            }
                            if(is_array($supp_segments)) {
                                foreach($supp_segments as $segment) {
                                    $segment_coordobjs = $segment->get_coordinators();
                                    if(is_array($segment_coordobjs)) {
                                        foreach($segment_coordobjs as $coord) {
                                            $notification_mails[] = $coord->get_coordinator()->email;
                                        }
                                    }
                                }
                            }
                            //Aff supervisor
                            if(is_object($affiliate)) {
                                $supervisor = $affiliate->get_supervisor();
                                if(is_object($supervisor)) {
                                    $notification_mails[] = $supervisor->email;
                                }
                            }
                            $notification_mails[] = 'nicole.sacy@orkila.com';
                        }
                        /* More recipients for visiting us events - END */
                        $ical_obj = new iCalendar(array('identifier' => $events_details['identifier'].'all', 'uidtimestamp' => $events_details['createdOn']));  /* pass identifer to outlook to avoid creation of multiple file with the same date */
                        $ical_obj->set_datestart($events_details['fromDate']);
                        $ical_obj->set_datend($events_details['toDate']);
                        $ical_obj->set_location($events_details['place']);
                        $ical_obj->set_summary($events_details['title']);
                        $ical_obj->set_name();
                        $ical_obj->set_status();
                        $ical_obj->set_transparency();
                        $ical_obj->set_icalattendees($notification_mails);
                        $ical_obj->set_description($events_details['description']);
                        $ical_obj->endical();

                        $mailer = new Mailer();
                        $mailer = $mailer->get_mailerobj();
                        $mailer->set_type('ical', array('content-class' => 'meetingrequest', 'method' => 'REQUEST', 'filename' => $events_details['title'].'.ics'));
                        $mailer->set_from(array('name' => 'Orkila Events Notifier', 'email' => 'events@orkila.com'));
                        $mailer->set_subject($events_details['title']);
                        $mailer->set_message($ical_obj->geticalendar());
                        $mailer->set_to($notification_mails);

                        /* Add multiple Attachments */
                        if(is_array($attachments)) {
                            foreach($attachments as $attachment) {
                                $mailer->add_attachment($attachments_path.'/'.$attachment['name']);
                            }
                        }
                        // $mailer->send();

                        if(is_object($eventtype) && $eventtype->name == 'visitingus') {
                            $meeting = array(
                                    'title' => $events_details['title'],
                                    'identifier' => substr(md5(uniqid(microtime())), 1, 10),
                                    'fromDate' => $events_details['fromDate'], // $new_event['fromDate'],
                                    'toDate' => $events_details['toDate'],
                                    'fromTime' => $core->input['event']['fromTime'],
                                    'toTime' => $core->input['event']['toTime'],
                                    'description' => $events_details['description'],
                                    'location' => $events_details['place'],
                                    'createdBy' => $core->user['uid'],
                                    'createdOn' => TIME_NOW,
                                    'associations' => array(
                                            'spid' => array(
                                                    'id' => $core->input['event']['spid'])
                            ));
                            $meeting['notifyuser'] = 1;
                            if(is_array($core->input['event']['invitee'])) {
                                $count = 0;
                                foreach($core->input['event']['invitee'] as $uid) {
                                    $meeting['attendees']['uid'][$count]['id'] = $uid;
                                    $count++;
                                }
                            }
                            elseif(is_array($meetininvitees)) {
                                $count = 0;
                                foreach($meetininvitees as $invitee) {
                                    $meeting['attendees']['uid'][$count]['id'] = $invitee->uid;
                                    $count++;
                                }
                            }
                            else {
                                $meeting['attendees']['uid'][0]['id'] = $core->user['uid'];
                            }
                            $meeting_obj = new Meetings();
                            $meeting_obj->create($meeting);
                        }
                        if($mailer->get_status() === true) {
                            $log->record($notification_mails, $last_id);
                        }
                        else {
                            $errors['notification'] = false;
                        }
                        /* Send the event notification - END */
                    }
                }
            }
        }

        if($query) {
            $log->record($core->input['type'], $last_id);
            ?>
            <script language="javascript" type="text/javascript">
                $(function() {
                    top.$("#upload_Result").html("<span class='green_text'><?php echo $lang->successfullysaved;?></span>");
                });
            </script>
            <?php
            exit;
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
            exit;
        }
//		else {
//			output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
//			exit;
//		}
    }

//}
    elseif($core->input['action'] == 'settaskdone') {
        if(is_empty($core->input['id'])) {
            output_xml("<status>false</status><message></message>");
            exit;
        }

        $task = new Tasks($core->input['id'], true);
        $task->change_status($core->input['value']);

        switch($task->get_status()) {
            case 0:
                header('Content-type: text/xml+javascript');
                if($core->input['value'] == 1) {
                    $output_js = "$('#ctid_".$core->input['id']."').css('text-decoration', 'line-through');";
                }
                else {
                    $output_js = '$("#ctid_'.$core->input['id'].'").css("text-decoration", "none");';
                }
                output_xml("<status>true</status><message><![CDATA[<script>".$output_js."</script>]]></message>");
                break;
            default: output_xml('<status>false</status><message></message>');
        }
    }
    elseif($core->input['action'] == 'get_eventdetails') {
        if(!empty($core->input['id'])) {
            $event = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."calendar_events WHERE ceid='".$db->escape_string($core->input['id'])."'"));

            $event['dates_output'] = date($core->settings['dateformat'], $event['fromDate']);
            if($event['toDate'] != $event['fromDate']) {
                $event['dates_output'] .= ' - '.date($core->settings['dateformat'], $event['toDate']);
            }

            if(!empty($event['place'])) {
                $event['place_output'] = $event['place'].' <a href="http://maps.google.com/maps?hl=en&q='.$event['place'].'" target="_blank"><img src="./images/icons/map.png" border="0" alt="'.$lang->map.'"></a>';
            }
            if($event['createdBy'] == $core->user['uid']) {
                $edittask = '<hr /><br><div style="display:inline-block"><a target="_blank" href="'.$core->settings['rootdir'].'/index.php?module=calendar/manageevents&id='.$event['ceid'].'" ><button>'.$lang->edit.'</button></a>';
                $edittask .= '<div style="float:right"><form id="perform_calendar/manageevents_Form" name="perform_calendar/manageevents_Form" action="#" method="post">
      <input type="hidden" name="action" value="delete_event" /><input type="hidden"  name="id" value="'.$event['ceid'].'" />
       <input type=\'button\' id=\'perform_calendar/manageevents_Button\' value="'.$lang->delete.'" class=\'button\'/>
    </form>
    </div>
    <div id="perform_calendar/manageevents_Results"></div></div>
';
            }
            eval("\$eventdetailsbox = \"".$template->get('popup_calendar_eventdetails')."\";");
            output($eventdetailsbox);
        }
    }
    elseif($core->input['action'] == 'get_taskdetails') {
        if(!empty($core->input['id'])) {
            $task = new Tasks($core->input['id']);

            $task_details = $task->get_task();

            if(!$task->is_sharedwithuser() && $core->user['uid'] != $task_details['uid'] && $core->user['uid'] != $task_details['createdBy']) {
                exit;
            }
//$task_details['dueDate_output'] = date($core->settings['dateformat'], $task_details['dueDate']);
            $task_details['priority_output'] = $task->parse_status();

            if(isset($task_details['timeDone_output'])) {
                $task_details['timeDone_output'] = $lang->datecompleted.': '.$task_details['timeDone_output'].'<br />';
            }

            if($task_details['uid'] != $core->user['uid']) {
                $task_details['assignedTo_output'] = $lang->assignedto.': '.$task_details['assignedTo'].'<br />';
            }

            $selected['percCompleted'][$task_details['percCompleted']] = ' selected="selected"';

            /* Get Notes - START */
            $task_notes = $task->get_notes();
            if(is_array($task_notes)) {
                $notes_count = count($task_notes);

                foreach($task_notes as $note) {
                    $rowclass = alt_row($rowclass);
                    $note_date_diff = (TIME_NOW - $note->dateAdded);
                    if(date('y-m-d', $note->dateAdded) != date('y-m-d', TIME_NOW)) {
                        $note->dateAdded_output = date($core->settings['dateformat'].' '.$core->setting['timeformat'], $note->dateAdded);
                    }
                    else {
                        $note->dateAdded_output = date($core->settings['timeformat'], $note->dateAdded);
                    }

                    fix_newline($note->note);
                    $task_notes_output .= '<div class="'.$rowclass.'" style="padding: 5px 0px 5px 10px;">'.$note->note.'. <span class="smalltext" style="font-style:italic;">'.$note->dateAdded_output.' by <a href="users.php?action=profile&uid='.$note->uid.'" target="_blank">'.$note->get_user()->displayName.'</a></span></div>';
                }
            }

            /* Parse share with users */
            if($core->user['uid'] == $task_details['uid'] || $core->user['uid'] == $task_details['createdBy']) {
                $shared_users = $task->get_shared_users();
                $users_order = '0';
                if(is_array($shared_users)) {
                    $shared_users_uids = array_keys($shared_users);
                    $users_order = implode(',', $shared_users_uids);
                }

                $users = Users::get_data('gid!=7', array('order' => 'CASE WHEN uid IN ('.$users_order.') THEN -1 ELSE displayName END, displayName'));
                foreach($users as $uid => $user) {
                    $checked = $rowclass = '';
                    if($uid == $core->user['uid']) {
                        continue;
                    }

                    if(is_array($shared_users_uids)) {
                        if(in_array($uid, $shared_users_uids)) {
                            $checked = ' checked="checked"';
                            $rowclass = 'selected';
                        }
                    }
                    eval("\$sharewith_rows .= \"".$template->get('calendar_createeventtask_sharewithrows')."\";");
                }
                eval("\$sharewith_section = \"".$template->get('calendar_createeventtask_sharewithsection')."\";");
                unset($sharewith_rows);
                eval("\$task_sharewith = \"".$template->get('calendar_createeventtask_sharewithform')."\";");
            }
            eval("\$eventdetailsbox = \"".$template->get('popup_calendar_taskdetails')."\";");
            output($eventdetailsbox);
        }
    }
    elseif($core->input['action'] == 'share_task') {
        $task = new Tasks($core->input['id']);
        $shares = $task->get_shares();

        if(is_array($shares)) {
            foreach($shares as $share) {
                $sharedusers[$share->uid] = $share;
            }

            if(empty($core->input['task']['share'])) {
                foreach($shares as $share) {
                    $share->delete();
                }
            }
            else {
                $users_toremove = array_diff(array_keys($sharedusers), $core->input['task']['share']);
                if(!empty($users_toremove)) {
                    foreach($users_toremove as $uid) {
                        $object = $sharedusers[$uid];
                        $object->delete();
                    }
                }
            }
        }

        if(is_array($core->input['task']['share'])) {
            foreach($core->input['task']['share'] as $uid) {
                $share = new CalendarTaskShares();
                $share->set(array('uid' => $uid, 'ctid' => $task->ctid));
                $share->save();
            }
        }

        switch($share->get_errorcode()) {
            case 0:
                output_xml("<status>true</status><message>".$lang->successfullysaved."</message>");
                break;
            case 1:
                output_xml("<status>true</status><message>".$lang->requiredfield."</message>");
                break;
        }
    }
    elseif($core->input['action'] == 'save_tasknote') {
        $lang->load('calendar_messages');
        $task = new Tasks($core->input['id']);
        $task->save_note($core->input['note']);
        $task_details = $task->get_task();

        switch($task->get_status()) {
            case 0:
                if($core->user['uid'] == $task_details['uid']) {
                    if($task_details['createdby'] != $core->user['uid']) {
                        $to[] = $db->fetch_field($db->query('SELECT email FROM '.Tprefix.'users WHERE uid='.$task_details['createdBy']), 'email');
                    }
                }
                else {
                    $to[] = $db->fetch_field($db->query('SELECT email FROM '.Tprefix.'users WHERE uid='.$task_details['uid']), 'email');
                }

                $shares = $task->get_shares();
                if(is_array($shares)) {
                    foreach($shares as $share) {
                        $to[] = $share->get_user()->email;
                    }
                }
                if(!empty($to)) {
                    $notification = array(
                            'to' => $to,
                            'from_email' => $core->settings['maileremail'],
                            'from' => 'OCOS Mailer',
                            'subject' => $lang->sprint($lang->newnotemessage_subject, $task_details['subject']),
                            'message' => $lang->sprint($lang->newnotemessage_body, $core->user['displayName'], $db->escape_string($core->input['note']), date($core->settings['dateformat'].' '.$core->settings['timeformat'], TIME_NOW))
                    );

                    $mail = new Mailer($notification, 'php');
                }
                header('Content-type: text/xml+javascript');
                output_xml("<status>true</status><message>{$lang->successfullysaved}<![CDATA[<script>$('#note').val(''); $('#calendar_task_notes').prepend('<div id=\'note_1\' style=\'padding: 5px 0px 5px 10px;\' class=\'altrow2\'>".$db->escape_string($core->input['note']).". <span class=\'smalltext\' style=\'font-style:italic;\'>".date($core->settings['dateformat'], TIME_NOW)." by <a href=\'users.php?action=profile&uid=".$core->user['uid']."\' target=\'_blank\'>".$core->user['displayName']."</a></span></div>');</script>]]></message>");
                exit;
            case 1:
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                break;
            case 2:
            case 3:
                output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                exit;
        }
    }
    elseif($core->input['action'] == 'update_task') {
        $task = new Tasks($core->input['ctid']);
        $task->update_task($core->input['percCompleted']);

        switch($task->get_status()) {
            case 0:
                header('Content-type: text/javascript');

                if($core->input['percCompleted'] == 100) {
                    $output_js = '$("#ctid_'.$core->input['ctid'].'").css("text-decoration", "line-through");';
                }
                else {
                    $output_js = '$("#ctid_'.$core->input['ctid'].'").css("text-decoration", "none");';
                }
                output($output_js);
                break;
            case 1:
            case 2:
            case 3:
                output_xml('<status>false</status><message></message>');
                break;
        }
    }
}
?>