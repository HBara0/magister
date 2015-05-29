<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: create.php
 * Created:        @tony.assaad    Nov 7, 2013 | 3:17:03 PM
 * Last Update:    @tony.assaad    Nov 7, 2013 | 3:17:03 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['meetings_canCreateMeeting'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {
    if(isset($core->input['mtid']) && !empty($core->input['mtid'])) {
        $mtid = $core->input['mtid'];
        $action = 'edit';

        $lang->create = $lang->edit;

        $meeting_obj = new Meetings($mtid);

        $meeting = $meeting_obj->get();
        if(is_array($meeting)) {
            if(!empty($meeting['fromDate'])) {
                $meeting['fromDate_output'] = date($core->settings['dateformat'], $meeting['fromDate']);
                $meeting['fromTime_output'] = trim(preg_replace('/(AM|PM)/', '', date('H:i', $meeting['fromDate'])));
            }
            if(!empty($meeting['toDate'])) {
                $meeting['toDate_output'] = date($core->settings['dateformat'], $meeting['toDate']);
                $meeting['toTime_output'] = trim(preg_replace('/(AM|PM)/', '', date('H:i', $meeting['toDate'])));
            }
            if($meeting['isPublic'] == 1) {
                $checked_checkboxes['isPublic'] = ' checked="checked"';
            }
            if($meeting['fromDate'] < TIME_NOW) {
                $disabled_checkboxes['notifyuser'] = $disabled_checkboxes['notifyrep'] = ' disabled="disabled"';
            }
            $meeting_assoc = $meeting_obj->get_meetingassociations();

            if(is_array($meeting_assoc)) {
                foreach($meeting_assoc as $mtaid => $associaton) {
                    $associaton_temp = $associaton->get();
                    $associatons[$associaton_temp['idAttr']][$associaton->get()['mtaid']] = $associaton_temp['id'];
                }
                unset($associaton_temp);
            }

            $rowid = $reprowid = $rowattachmentid = 1;
            $meeting_attednobjs = $meeting_obj->get_attendees();
            if(is_array($meeting_attednobjs)) {
                foreach($meeting_attednobjs as $matid => $meeting_attednobj) {
                    $attendees_objs = $meeting_attednobj->get_attendee();
                    $meeting['attendees'][$matid] = $attendees_objs->get();
                    if(isset($meeting['attendees'][$matid]['uid'])) {
                        $meeting['attendees'][$matid]['name'] = $meeting['attendees'][$matid]['displayName'];
                        $meeting['attendees'][$matid]['id'] = $meeting['attendees'][$matid]['uid'];
                        eval("\$createmeeting_userattendees .= \"".$template->get('meeting_create_userattendee')."\";");
                        $rowid++;
                    }
                    if(isset($meeting['attendees'][$matid]['rpid'])) {
                        $meeting['attendees'][$matid]['id'] = $meeting['attendees'][$matid]['rpid'];
                        eval("\$createmeeting_repattendees .= \"".$template->get('meeting_create_repattendee')."\";");
                        $reprowid++;
                    }
                }
                unset($meeting['attendees'], $matid);
            }

            if(empty($createmeeting_userattendees)) {
                eval("\$createmeeting_userattendees = \"".$template->get('meeting_create_userattendee')."\";");
            }

            if(empty($createmeeting_repattendees)) {
                eval("\$createmeeting_repattendees  = \"".$template->get('meeting_create_repattendee')."\";");
            }

            $entity_obj = new Entities($associatons['cid']);
            $meeting['associations']['cutomername'] = $entity_obj->get()['companyName'];
            $meeting['associations']['spid'] = $associatons['cid'];
            if(is_array($associatons['spid'])) {
                $associatons['spid'] = current($associatons['spid']);
            }
            $entity_obj = new Entities($associatons['spid']);
            $meeting['associations']['suppliername'] = $entity_obj->get()['companyName'];
            $meeting['associations']['spid'] = $associatons['spid'];
            /* parse Attachments - START */
            $attachmentrow = 0;

            $meeting_attachobjs = $meeting_obj->get_attachments();
            if(is_array($meeting_attachobjs)) {
                foreach($meeting_attachobjs as $meeting_attachobj) {
                    $altrow = alt_row('trow');
                    $attachmentrow++;
                    $meeting_attachments = $meeting_attachobj->get();
                    /* Limit permission to view meeting attachments */
                    if(is_array($meeting_attachments) && $meeting_obj->can_viewmeeting()) {
                        eval("\$createmeeting_edit_attachmentsfiles .= \"".$template->get('meetings_edit_attachments_files')."\";");
                    }
                }
            }
            /* parse Attachments ---END */

            eval("\$meeting_attachments = \"".$template->get('meetings_edit_attachments')."\";");
        }
        else {
            redirect('index.php?module=meetings/list');
        }
        //$meeting['attendees'] = $meeting_obj->get_attendees();
    }
    else {
        $rowid = 1;
        $reprowid = 1;
        eval("\$createmeeting_userattendees = \"".$template->get('meeting_create_userattendee')."\";");
        eval("\$createmeeting_repattendees  = \"".$template->get('meeting_create_repattendee')."\";");
        eval("\$meeting_attachments = \"".$template->get('meeting_create_attachments')."\";");
        $sectionsvisibility['associationssection'] = ' display:none;';
        $action = 'create';
    }
    $pagetitle = $lang->{$action.'meeting'};
    $afiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, 'affid IN ('.implode(',', $core->user['affiliates']).')');
    $afiliates[0] = '';
    asort($afiliates);
    $affiliates_list = parse_selectlist('meeting[associations][affid][]', 5, $afiliates, $associatons['affid'], 1);
    $aff_events = Events::get_affiliatedevents($core->user['affiliates']);
    if(is_array($aff_events)) {
        foreach($aff_events as $ceid => $event) {
            if($associatons['ceid'] == $ceid) {
                $selected = ' selected="selected"';
            }
            $events_list .= '<option value="'.$ceid.'" "'.$selected.'">'.$event['title'].'</option>';
        }
    }

    /* get leaves of business type associated to the user where 'fromDate' is within one year from now */

    $leavetypes = LeaveTypes::get_data('isBusiness=1');
    $leaves = Leaves::get_data(array('uid' => $core->user['uid'], 'type' => array_keys($leavetypes), 'fromDate' => strtotime("-1 year")), array('operators' => array('type' => 'IN', 'fromDate' => 'grt'), 'returnarray' => true));
    $leaves_list = parse_selectlist('meeting[associations][lid]', $tabindex, $leaves, $associatons['lid'], 0, null, array('blankstart' => true));

    eval("\$createmeeting_associations = \"".$template->get('meeting_create_associations')."\";");
    eval("\$createmeeting = \"".$template->get('meeting_create')."\";");
    output_page($createmeeting);
}
else {
    if($core->input['action'] == 'deletefile') {
        $mattid = $db->escape_string($core->input[mattid]);
        if(!empty($mattid)) {
            $meetingattach_obj = new MeetingsAttachments($mattid);
            $deleted = $meetingattach_obj->delete();
            header('Content-type: text/javascript');
            if($deleted) {
                echo '$("div[id=\'file_'.$mattid.'\']").css("display", "none");';
                exit;
            }
        }
    }
    elseif($core->input['action'] == 'do_createmeeting') {
        $core->input['meeting']['attachments'] = $_FILES;
        $meeting_obj = new Meetings();
        $meeting_obj->create($core->input['meeting']);

        echo $headerinc;
        switch($meeting_obj->get_errorcode()) {
            case 0:
                $output_class = 'green_text';
                $output_message = $lang->successfullysaved;
                break;
            case 1:
                $output_class = 'red_text';
                $output_message = $lang->fillallrequiredfields;
                //output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
                break;
            case 2:
                $output_class = 'red_text';
                $output_message = $lang->errorsaving;
                //output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
                break;
            case 3:
                $output_class = 'red_text';
                $output_message = $lang->invaliddate;
                //output_xml('<status>false</status><message>'.$lang->invaliddate.'</message>');
                break;
            case 4:
                $output_class = 'red_text';
                $output_message = $lang->meetingintersect;
                //output_xml('<status>false</status><message>'.$lang->meetingintersect.'</message>');
                break;

            default:
                $output_class = 'red_text';
                $output_message = $lang->errorsaving;
                //output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
                break;
        }
    }
    elseif($core->input['action'] == 'do_editmeeting') {
        $mtid = $db->escape_string($core->input['mtid']);
        $core->input['meeting']['attachments'] = $_FILES;
        $meeting_obj = new Meetings($mtid);
        $meeting_obj->update($core->input['meeting']);
        echo $headerinc;
        switch($meeting_obj->get_errorcode()) {
            case 0:
                $output_class = 'green_text';
                $output_message = $lang->successfullysaved;
                break;
            case 1:
                $output_class = 'red_text';
                $output_message = $lang->fillallrequiredfields;
                //output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
                break;

            case 3:
                $output_class = 'red_text';
                $output_message = $lang->invaliddate;
                //output_xml('<status>false</status><message>'.$lang->invaliddate.'</message>');
                break;
            case 4:
                $output_class = 'red_text';
                $output_message = $lang->meetingintersect;
                //output_xml('<status>false</status><message>'.$lang->meetingintersect.'</message>');
                break;

            default:
                $output_class = 'red_text';
                $output_message = $lang->errorsaving;
                //output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
                break;
        }
    }
    elseif($core->input['action'] == 'do_add_representative') {
        $representative = new Entities($core->input, 'add_representative');

        if($representative->get_status() === true) {
            header('Content-type: text/xml+javascript');
            output_xml('<status>true</status><message>{$lang->representativecreated}<![CDATA[<script>$("#popup_addrepresentative").dialog("close");</script>]]></message>');
            //output_xml("<status>true</status><message>{$lang->representativecreated}</message>");
            exit;
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorcreatingreprentative}</message>");
        }
    }
    elseif($core->input['action'] == 'get_addnew_representative') {
        eval("\$addrepresentativebox = \"".$template->get('popup_addrepresentative')."\";");
        output($addrepresentativebox);
        exit;
    }
    ?>
    <script language="javascript" type="text/javascript">
        $(function () {
            top.$("#upload_Result").html("<span class='<?php echo $output_class;?>'><?php echo $output_message;?></span>");
        });
    </script>
    <?php
}
?>
